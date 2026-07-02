#!/usr/bin/env python3
"""
Mizuki WP 主题打包脚本

使用 Python zipfile 模块打包,保证 zip 内路径使用正斜杠(/),
兼容 Linux 服务器解压。

用法:
    python pack-theme.py [--output PATH]

输出:
    默认输出到项目根目录的 mizuki-wp-theme.zip
"""
import os
import sys
import zipfile
import argparse

# 项目根目录(tools 目录的上一级)
ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
THEME_DIR = os.path.join(ROOT, 'theme', 'mizuki-wp')


def should_include(rel_path):
    """判断文件是否应该包含在 zip 中。"""
    # 排除开发相关文件
    exclude_dirs = {
        '.git', '.github', '.vscode', '.idea',
        'node_modules', '__pycache__', '.cache',
    }
    exclude_files = {
        '.DS_Store', 'Thumbs.db', 'desktop.ini',
        '.gitignore', '.gitattributes',
    }
    parts = rel_path.replace('\\', '/').split('/')
    for ex in exclude_dirs:
        if ex in parts:
            return False
    if rel_path in exclude_files:
        return False
    # 排除测试文件
    if rel_path.startswith('test-') and rel_path.endswith('.php'):
        return False
    return True


def pack(output_path):
    """打包主题目录到 zip 文件。"""
    if not os.path.isdir(THEME_DIR):
        print(f'错误: 主题目录不存在: {THEME_DIR}')
        return 1

    # 确保输出目录存在
    out_dir = os.path.dirname(output_path)
    if out_dir and not os.path.isdir(out_dir):
        os.makedirs(out_dir, exist_ok=True)

    file_count = 0
    total_size = 0

    with zipfile.ZipFile(output_path, 'w', zipfile.ZIP_DEFLATED, compresslevel=6) as zf:
        for dirpath, dirnames, filenames in os.walk(THEME_DIR):
            # 排除目录
            dirnames[:] = [d for d in dirnames if d not in {
                '.git', '.github', '.vscode', '.idea',
                'node_modules', '__pycache__', '.cache',
            }]

            for filename in filenames:
                full_path = os.path.join(dirpath, filename)
                # 相对于 theme 目录的路径(zip 内以 mizuki-wp/ 开头)
                rel_path = os.path.relpath(full_path, os.path.join(THEME_DIR, '..'))
                # 关键: 转换为正斜杠(Linux 兼容)
                arcname = rel_path.replace('\\', '/')

                if not should_include(rel_path):
                    continue

                zf.write(full_path, arcname)
                file_count += 1
                total_size += os.path.getsize(full_path)

    zip_size = os.path.getsize(output_path)
    print(f'打包完成: {output_path}')
    print(f'文件数: {file_count}')
    print(f'原始大小: {total_size / 1024 / 1024:.2f} MB')
    print(f'压缩后: {zip_size / 1024 / 1024:.2f} MB')
    print(f'压缩率: {(1 - zip_size / total_size) * 100:.1f}%')

    # 验证 zip 内路径使用正斜杠
    print('\n验证 zip 内路径(抽样):')
    with zipfile.ZipFile(output_path, 'r') as zf:
        names = zf.namelist()
        for n in names[:5]:
            print(f'  {n}')
        if len(names) > 5:
            print(f'  ... (共 {len(names)} 个文件)')
        # 检查是否有反斜杠
        bad = [n for n in names if '\\' in n]
        if bad:
            print(f'\n错误: 发现 {len(bad)} 个文件路径包含反斜杠!')
            for b in bad[:3]:
                print(f'  {b}')
            return 1
        else:
            print('\n所有路径使用正斜杠,兼容 Linux ✓')

    return 0


def main():
    parser = argparse.ArgumentParser(description='打包 Mizuki WP 主题')
    parser.add_argument('--output', '-o', default=os.path.join(ROOT, 'mizuki-wp-theme.zip'),
                        help='输出 zip 文件路径(默认: mizuki-wp-theme.zip)')
    args = parser.parse_args()
    sys.exit(pack(args.output))


if __name__ == '__main__':
    main()
