#!/usr/bin/env bash
# 同步仓库主题文件到 wp-env 源目录(开发用)
set -euo pipefail
SRC="/root/mizuki/theme/mizuki-wp"
DST="/root/wp-env/a2f1695a3ac249d65eabc5cd9da46ed2/WordPress/wp-content/themes/mizuki-wp"
rsync -a --delete "$SRC/" "$DST/"
echo "synced: $SRC -> $DST"
