#!/usr/bin/env bash
# 重新生成补充 Tailwind 工具类(扫描 WP 主题 PHP/JS),并复制进主题。
# 每当模板新增 Tailwind 类后运行本脚本。
set -euo pipefail
cd "$(dirname "$0")"
./node_modules/.bin/tailwindcss -i input.css -o output.css >/dev/null 2>&1
cp output.css /root/mizuki/theme/mizuki-wp/assets/css/mizuki-tw-utilities.css
echo "rebuilt mizuki-tw-utilities.css ($(wc -c < output.css) bytes)"
