/**
 * pack-theme.mjs — 将 theme/mizuki-wp 打包为符合 ZIP 规范的 mizuki-wp-theme.zip。
 *
 * 为什么需要它:
 *   Windows 的 PowerShell 5.1 `Compress-Archive` 和 .NET Framework 的
 *   `ZipFile.CreateFromDirectory` 会把条目路径写成反斜杠(`mizuki-wp\assets\...`)。
 *   ZIP 规范(APPNOTE 4.4.17)要求路径分隔符必须是正斜杠 `/`。反斜杠条目在
 *   Linux 服务器上解压时,反斜杠被当作文件名的字面字符 → 不建立目录结构 →
 *   主题 CSS/JS 全部 404 → 前台只显示纯文本;纯 PHP 的 PCLZip 解压器处理这些
 *   畸形条目还会变慢甚至超时。
 *
 * 本脚本只依赖 Node 内置 zlib,不需要 npm 依赖,且强制所有条目使用正斜杠。
 *
 * 用法: node tools/pack-theme.mjs
 *   可选环境变量: SRC_DIR(默认 theme)、THEME(默认 mizuki-wp)、OUT(默认 mizuki-wp-theme.zip)
 */
import { deflateRawSync } from 'node:zlib';
import { readdirSync, statSync, readFileSync, writeFileSync } from 'node:fs';
import { join, relative, sep, resolve } from 'node:path';
import { validateArchiveEntryNames } from './package-validation.mjs';

const ROOT = resolve(process.cwd());
const SRC_DIR = process.env.SRC_DIR || 'theme';
const THEME = process.env.THEME || 'mizuki-wp';
const OUT = process.env.OUT || 'mizuki-wp-theme.zip';

const themeRoot = resolve(ROOT, SRC_DIR, THEME);
const baseForArc = resolve(ROOT, SRC_DIR); // 相对它取路径,使顶层为 mizuki-wp/
const expectedVersion = JSON.parse(readFileSync(resolve(ROOT, 'package.json'), 'utf8')).version;

function failValidation(message) {
  console.error(`Package validation failed: ${message}`);
  process.exit(1);
}

function requireMatch(relativePath, pattern, label) {
  const fullPath = resolve(themeRoot, relativePath);
  let contents;
  try {
    contents = readFileSync(fullPath, 'utf8');
  } catch {
    failValidation(`missing metadata file ${relativePath}`);
  }

  const match = contents.match(pattern);
  if (!match) {
    failValidation(`could not read ${label} from ${relativePath}`);
  }
  if (match[1] !== expectedVersion) {
    failValidation(`${label} version ${match[1]} does not match ${expectedVersion}`);
  }
}

requireMatch('style.css', /^\s*Version:\s*([^\s]+)\s*$/mi, 'style.css');
requireMatch('functions.php', /define\(\s*['"]MIZUKI_VERSION['"]\s*,\s*['"]([^'"]+)['"]\s*\)/, 'MIZUKI_VERSION');
requireMatch('readme.txt', /^\s*Stable tag:\s*([^\s]+)\s*$/mi, 'readme stable tag');

function validateOrFail(entryNames) {
  try {
    return validateArchiveEntryNames(entryNames, THEME);
  } catch (error) {
    failValidation(error.message);
  }
}

function readCentralDirectoryEntryNames(zip) {
  const eocdSignature = Buffer.from([0x50, 0x4b, 0x05, 0x06]);
  const eocdOffset = zip.lastIndexOf(eocdSignature);
  if (eocdOffset < 0 || eocdOffset + 22 > zip.length) {
    throw new Error('generated ZIP has no valid end-of-central-directory record');
  }

  const entryCount = zip.readUInt16LE(eocdOffset + 10);
  let offset = zip.readUInt32LE(eocdOffset + 16);
  const names = [];
  for (let index = 0; index < entryCount; index++) {
    if (offset + 46 > zip.length || zip.readUInt32LE(offset) !== 0x02014b50) {
      throw new Error(`generated ZIP has an invalid central-directory entry at ${offset}`);
    }
    const nameLength = zip.readUInt16LE(offset + 28);
    const extraLength = zip.readUInt16LE(offset + 30);
    const commentLength = zip.readUInt16LE(offset + 32);
    const nameStart = offset + 46;
    const nameEnd = nameStart + nameLength;
    if (nameEnd > zip.length) {
      throw new Error('generated ZIP has a truncated central-directory name');
    }
    names.push(zip.subarray(nameStart, nameEnd).toString('utf8'));
    offset = nameEnd + extraLength + commentLength;
  }
  return names;
}

// --- CRC32(自实现,不依赖 Node 版本特性)---
const CRC_TABLE = (() => {
  const t = new Uint32Array(256);
  for (let n = 0; n < 256; n++) {
    let c = n;
    for (let k = 0; k < 8; k++) c = (c & 1) ? (0xEDB88320 ^ (c >>> 1)) : (c >>> 1);
    t[n] = c >>> 0;
  }
  return t;
})();
function crc32(buf) {
  let c = 0xFFFFFFFF;
  for (let i = 0; i < buf.length; i++) c = CRC_TABLE[(c ^ buf[i]) & 0xFF] ^ (c >>> 8);
  return (c ^ 0xFFFFFFFF) >>> 0;
}

// --- 递归收集文件,arcname 一律正斜杠 ---
function walk(dir, acc) {
  for (const name of readdirSync(dir).sort()) {
    const full = join(dir, name);
    const st = statSync(full);
    if (st.isDirectory()) {
      walk(full, acc);
    } else if (st.isFile()) {
      const arc = relative(baseForArc, full).split(sep).join('/'); // 强制 /
      if (arc.toLowerCase().endsWith('.md')) continue;
      acc.push({ full, arc, mtime: st.mtime });
    }
  }
  return acc;
}

function dosDateTime(date) {
  const yr = date.getFullYear();
  const dosYear = yr < 1980 ? 0 : yr - 1980;
  const time = (Math.floor(date.getSeconds() / 2)) | (date.getMinutes() << 5) | (date.getHours() << 11);
  const dosDate = date.getDate() | ((date.getMonth() + 1) << 5) | (dosYear << 9);
  return { time: time & 0xFFFF, date: dosDate & 0xFFFF };
}

const files = walk(themeRoot, []);
if (files.length === 0) {
  console.error(`没有文件可打包: ${themeRoot}`);
  process.exit(1);
}

validateOrFail(files.map((file) => file.arc));

const localParts = [];
const centralParts = [];
let offset = 0;
let entryCount = 0;

for (const f of files) {
  const data = readFileSync(f.full);
  const crc = crc32(data);
  const uncompSize = data.length;
  let compData = deflateRawSync(data, { level: 9 });
  let method = 8; // deflate
  // 若压缩反而变大(极小文件),退回 store
  if (compData.length >= uncompSize) {
    compData = data;
    method = 0;
  }
  const compSize = compData.length;
  const nameBuf = Buffer.from(f.arc, 'utf8');
  const { time, date } = dosDateTime(f.mtime);
  const flags = 0x0800; // bit 11: 文件名按 UTF-8 解释

  // Local file header
  const lh = Buffer.alloc(30);
  lh.writeUInt32LE(0x04034b50, 0);
  lh.writeUInt16LE(20, 4);            // version needed
  lh.writeUInt16LE(flags, 6);
  lh.writeUInt16LE(method, 8);
  lh.writeUInt16LE(time, 10);
  lh.writeUInt16LE(date, 12);
  lh.writeUInt32LE(crc, 14);
  lh.writeUInt32LE(compSize, 18);
  lh.writeUInt32LE(uncompSize, 22);
  lh.writeUInt16LE(nameBuf.length, 26);
  lh.writeUInt16LE(0, 28);            // extra len
  localParts.push(lh, nameBuf, compData);

  // Central directory header
  const ch = Buffer.alloc(46);
  ch.writeUInt32LE(0x02014b50, 0);
  ch.writeUInt16LE(20, 4);            // version made by
  ch.writeUInt16LE(20, 6);            // version needed
  ch.writeUInt16LE(flags, 8);
  ch.writeUInt16LE(method, 10);
  ch.writeUInt16LE(time, 12);
  ch.writeUInt16LE(date, 14);
  ch.writeUInt32LE(crc, 16);
  ch.writeUInt32LE(compSize, 20);
  ch.writeUInt32LE(uncompSize, 24);
  ch.writeUInt16LE(nameBuf.length, 28);
  ch.writeUInt16LE(0, 30);           // extra len
  ch.writeUInt16LE(0, 32);           // comment len
  ch.writeUInt16LE(0, 34);           // disk start
  ch.writeUInt16LE(0, 36);           // internal attrs
  ch.writeUInt32LE(0o644 << 16, 38); // external attrs (unix 0644)
  ch.writeUInt32LE(offset, 42);      // local header offset
  centralParts.push(ch, nameBuf);

  offset += lh.length + nameBuf.length + compData.length;
  entryCount++;
}

const centralBuf = Buffer.concat(centralParts);
const localBuf = Buffer.concat(localParts);

const eocd = Buffer.alloc(22);
eocd.writeUInt32LE(0x06054b50, 0);
eocd.writeUInt16LE(0, 4);            // disk number
eocd.writeUInt16LE(0, 6);            // disk with CD
eocd.writeUInt16LE(entryCount, 8);
eocd.writeUInt16LE(entryCount, 10);
eocd.writeUInt32LE(centralBuf.length, 12);
eocd.writeUInt32LE(localBuf.length, 16); // CD offset = size of all local entries
eocd.writeUInt16LE(0, 20);          // comment len

const zip = Buffer.concat([localBuf, centralBuf, eocd]);
const outputPath = resolve(ROOT, OUT);
writeFileSync(outputPath, zip);
try {
  validateOrFail(readCentralDirectoryEntryNames(readFileSync(outputPath)));
} catch (error) {
  failValidation(error.message);
}

console.log(`打包完成: ${OUT}`);
console.log(`  条目数: ${entryCount}`);
console.log(`  大小:   ${(zip.length / 1024 / 1024).toFixed(2)} MB`);
console.log(`  顶层:   ${THEME}/`);
console.log(`  分隔符: 正斜杠 / (符合 ZIP 规范)`);
