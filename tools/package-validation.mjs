export const REQUIRED_THEME_FILES = Object.freeze([
  'style.css',
  'functions.php',
  'readme.txt',
  'index.php',
  'header.php',
  'footer.php',
  'inc/setup.php',
  'inc/enqueue.php',
  'inc/template-tags.php',
  'inc/customizer.php',
  'inc/cpt.php',
  'inc/default-content.php',
  'inc/api-handlers.php',
  'inc/parts/banner.php',
  'inc/parts/navbar.php',
  'inc/parts/sidebar.php',
  'assets/css/mizuki-main.css',
  'assets/css/mizuki-fonts.css',
  'assets/css/mizuki-layout-modes.css',
  'assets/css/mizuki-overrides.css',
  'assets/css/mizuki-settings-panel.css',
  'assets/js/banner-carousel.js',
  'assets/js/mizuki-theme.js',
  'assets/fonts/ZenMaruGothic-Medium.ttf',
  'assets/fonts/loli.ttf',
]);

const FORBIDDEN_DIRECTORY_NAMES = new Set([
  '.git',
  '.github',
  'node_modules',
  'tests',
  'tools',
  'docs',
  'debug',
]);

const FORBIDDEN_FILE_NAMES = new Set([
  '.ds_store',
  'thumbs.db',
  'task_plan.md',
  'findings.md',
  'progress.md',
  '.plan-attestation',
]);

function isDevelopmentFile(relativePath) {
  const segments = relativePath.split('/');
  const fileName = segments.at(-1);
  const normalizedFileName = fileName.toLowerCase();
  const hasForbiddenDirectory = segments
    .slice(0, -1)
    .some((segment) => FORBIDDEN_DIRECTORY_NAMES.has(segment.toLowerCase()));
  const isForbiddenFile = FORBIDDEN_FILE_NAMES.has(normalizedFileName)
    || /(^|[._-])debug([._-]|$)/i.test(fileName)
    || normalizedFileName.endsWith('.map')
    || normalizedFileName.endsWith('.md');

  return hasForbiddenDirectory || isForbiddenFile;
}

export function validateArchiveEntryNames(entryNames, theme = 'mizuki-wp') {
  if (theme !== 'mizuki-wp') {
    throw new Error(`archive top-level root must be mizuki-wp/, received ${theme}/`);
  }
  if (!Array.isArray(entryNames) || entryNames.length === 0) {
    throw new Error('archive must contain at least one file entry');
  }

  const names = new Set();
  for (const entryName of entryNames) {
    if (typeof entryName !== 'string' || entryName.length === 0) {
      throw new Error('archive contains an empty entry name');
    }
    if (entryName.includes('\\')) {
      throw new Error(`archive entry uses a backslash separator: ${entryName}`);
    }
    if (entryName.startsWith('/') || /^[A-Za-z]:/.test(entryName)) {
      throw new Error(`archive entry uses an unsafe absolute path: ${entryName}`);
    }

    const segments = entryName.split('/');
    if (segments.some((segment) => segment === '' || segment === '.' || segment === '..')) {
      throw new Error(`archive entry uses an unsafe traversal path: ${entryName}`);
    }
    if (segments.length < 2 || segments[0] !== theme) {
      throw new Error(`archive entry is outside the ${theme}/ top-level root: ${entryName}`);
    }

    const relativePath = segments.slice(1).join('/');
    if (isDevelopmentFile(relativePath)) {
      throw new Error(`forbidden development file ${entryName}`);
    }
    if (names.has(entryName)) {
      throw new Error(`archive contains duplicate entry ${entryName}`);
    }
    names.add(entryName);
  }

  for (const relativePath of REQUIRED_THEME_FILES) {
    const archivePath = `${theme}/${relativePath}`;
    if (!names.has(archivePath)) {
      throw new Error(`missing required theme asset ${archivePath}`);
    }
  }

  return { entryCount: names.size, topLevel: theme };
}
