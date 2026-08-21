#!/usr/bin/env bash
# 初始化 MariaDB 数据目录并创建 wordpress 库与账号（可重复执行）
set -euo pipefail
BIN="$(ls -d "/c/Program Files/MariaDB"*/bin | head -1)"
DATA="e:/code/mizuki-wp/.wp-local/db-data"
mkdir -p "$DATA"
if [ ! -d "$DATA/mysql" ]; then
  "$BIN/mariadb-install-db.exe" --datadir="$(cygpath -w "$DATA")"
fi
echo "数据目录就绪。请先运行 start-db.sh 再执行本脚本第二段。"
if "$BIN/mariadb.exe" --port=3307 --host=127.0.0.1 -u root -e "SELECT 1" >/dev/null 2>&1; then
  "$BIN/mariadb.exe" --port=3307 --host=127.0.0.1 -u root <<'SQL'
CREATE DATABASE IF NOT EXISTS wordpress DEFAULT CHARACTER SET utf8mb4;
CREATE USER IF NOT EXISTS 'wp'@'localhost' IDENTIFIED BY 'wp123';
CREATE USER IF NOT EXISTS 'wp'@'127.0.0.1' IDENTIFIED BY 'wp123';
GRANT ALL PRIVILEGES ON wordpress.* TO 'wp'@'localhost';
GRANT ALL PRIVILEGES ON wordpress.* TO 'wp'@'127.0.0.1';
FLUSH PRIVILEGES;
SQL
  echo "数据库与账号已就绪 (wp/wp123 @ 127.0.0.1:3307)"
else
  echo "数据库未运行，跳过建库段。"
fi
