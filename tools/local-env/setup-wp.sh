#!/usr/bin/env bash
# 一键安装本地 WordPress 并接入 Mizuki 主题与 erphpdown 插件（可重复执行）
set -euo pipefail
ROOT="e:/code/mizuki-wp"
ENVDIR="$ROOT/tools/local-env"
WPDIR="$ROOT/.wp-local/wordpress"
WP() { php -c "$ENVDIR/php.ini" "$ENVDIR/wp-cli.phar" --path="$WPDIR" "$@"; }

mkdir -p "$ROOT/.wp-local"
if [ ! -f "$ENVDIR/wp-cli.phar" ]; then
  # raw.githubusercontent.com 与 github.com releases 在本机网络环境下连接被重置/超时，
  # 改用 jsdelivr 的 GitHub 镜像（同一份 wp-cli/builds gh-pages 产物）。
  curl -fL -o "$ENVDIR/wp-cli.phar" https://cdn.jsdelivr.net/gh/wp-cli/builds@gh-pages/phar/wp-cli.phar
fi
if [ ! -f "$WPDIR/wp-load.php" ]; then
  WP core download --locale=zh_CN
fi
if [ ! -f "$WPDIR/wp-config.php" ]; then
  WP config create --dbname=wordpress --dbuser=wp --dbpass=wp123 --dbhost=127.0.0.1:3307 --skip-check
  WP config set WP_DEBUG true --raw
  WP config set WP_DEBUG_LOG true --raw
  WP config set WP_DEBUG_DISPLAY false --raw
fi
if ! WP core is-installed >/dev/null 2>&1; then
  WP core install --url="http://127.0.0.1:8080" --title="Mizuki 本地测试站" \
    --admin_user=admin --admin_password=admin123456 --admin_email=admin@example.com --skip-email
fi
# 目录联接：改仓库代码即时生效
if [ ! -e "$WPDIR/wp-content/themes/mizuki-wp" ]; then
  powershell -NoProfile -Command "New-Item -ItemType Junction -Path '$(cygpath -w "$WPDIR/wp-content/themes/mizuki-wp")' -Target '$(cygpath -w "$ROOT/theme/mizuki-wp")'" >/dev/null
fi
if [ ! -e "$WPDIR/wp-content/plugins/erphpdown" ]; then
  powershell -NoProfile -Command "New-Item -ItemType Junction -Path '$(cygpath -w "$WPDIR/wp-content/plugins/erphpdown")' -Target '$(cygpath -w "$ROOT/plugins/erphpdown")'" >/dev/null
fi
WP theme activate mizuki-wp
WP plugin activate erphpdown
WP user get tester --field=ID >/dev/null 2>&1 || WP user create tester tester@example.com --user_pass=test123456 --role=subscriber
WP option update ice_ali_money_checkin 1
WP post list --post_type=page --fields=post_title | grep -qx '用户中心' || \
  WP post create --post_type=page --post_status=publish --post_title='用户中心' --post_content='[erphpdown_sc_user]'
WP post list --post_type=page --fields=post_title | grep -qx 'VIP会员' || \
  WP post create --post_type=page --post_status=publish --post_title='VIP会员' --post_content='[erphpdown_sc_vip_page]'
echo "== 安装完成: http://127.0.0.1:8080 (admin/admin123456, tester/test123456) =="
