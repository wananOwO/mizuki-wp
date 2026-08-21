#!/usr/bin/env bash
# 签到端到端测试：断言"接口报成功"与"余额真实增加"必须一致
set -uo pipefail
ROOT="e:/code/mizuki-wp"
ENVDIR="$ROOT/tools/local-env"
WPDIR="$ROOT/.wp-local/wordpress"
WP() { php -c "$ENVDIR/php.ini" "$ENVDIR/wp-cli.phar" --path="$WPDIR" "$@"; }

UID_T=$(WP user get tester --field=ID)
GIFT=$(WP option get ice_ali_money_checkin)

# 清掉今日签到记录，保证可重复执行
WP eval 'global $wpdb; $wpdb->query("DELETE FROM {$wpdb->prefix}checkins WHERE user_id='"$UID_T"'");'

BEFORE=$(WP eval 'global $wpdb; echo (float)$wpdb->get_var("SELECT ice_have_money FROM {$wpdb->prefix}ice_info WHERE ice_user_id='"$UID_T"'");')

RESP=$(WP eval '
wp_set_current_user('"$UID_T"');
$_REQUEST["nonce"] = wp_create_nonce("erphp-checkin");
do_action("wp_ajax_epd_checkin");
' 2>/dev/null)
echo "接口返回: $RESP"

AFTER=$(WP eval 'global $wpdb; echo (float)$wpdb->get_var("SELECT ice_have_money FROM {$wpdb->prefix}ice_info WHERE ice_user_id='"$UID_T"'");')
echo "余额: $BEFORE -> $AFTER (奖励应为 $GIFT)"

OK=$(echo "$RESP" | grep -c '"status":200' || true)
if [ "$OK" -ge 1 ] && php -r "exit(abs(((float)'$AFTER')-((float)'$BEFORE')-((float)'$GIFT')) < 0.001 ? 0 : 1);"; then
  echo "PASS: 报成功且余额确实增加 $GIFT"
  exit 0
fi
echo "FAIL: 接口报成功=$OK，余额变化=$(php -r "echo ((float)'$AFTER')-((float)'$BEFORE');")，期望=$GIFT"
exit 1
