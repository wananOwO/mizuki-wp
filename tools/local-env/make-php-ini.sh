#!/usr/bin/env bash
# 生成本地开发用 php.ini（扩展目录指向当前 PHP 安装的 ext）
set -euo pipefail
DIR="$(cd "$(dirname "$0")" && pwd)"
PHP_DIR="$(php -r 'echo dirname(PHP_BINARY);')"
mkdir -p /e/code/mizuki-wp/.wp-local
cat > "$DIR/php.ini" <<EOF
[PHP]
memory_limit = 512M
upload_max_filesize = 64M
post_max_size = 64M
max_execution_time = 300
error_reporting = E_ALL
display_errors = On
log_errors = On
error_log = "e:/code/mizuki-wp/.wp-local/php-error.log"
date.timezone = Asia/Shanghai
extension_dir = "$PHP_DIR/ext"
extension = mysqli
extension = pdo_mysql
extension = curl
extension = gd
extension = mbstring
extension = openssl
extension = zip
extension = fileinfo
extension = intl
extension = sqlite3
extension = pdo_sqlite
EOF
echo "php.ini 已生成: $DIR/php.ini"
php -c "$DIR/php.ini" -m
