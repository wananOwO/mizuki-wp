#!/usr/bin/env bash
# 问题1 端到端测试：兑换比例未配置时，余额购买必须按"价格×兜底比例1"实扣，而非扣 0 元
# 复现原 bug：ice_proportion_alipay 为空 → $price*false=0 → 0 元到手
set -uo pipefail
ROOT="e:/code/mizuki-wp"
ENVDIR="$ROOT/tools/local-env"
WPDIR="$ROOT/.wp-local/wordpress"
WP() { php -c "$ENVDIR/php.ini" "$ENVDIR/wp-cli.phar" --path="$WPDIR" "$@"; }

UID_T=$(WP user get tester --field=ID)

# 关键：把兑换比例清空，制造 bug 触发条件
WP option update ice_proportion_alipay '' >/dev/null

# 准备一篇收费文章，价格 2
PID=$(WP eval '
$id = wp_insert_post(array("post_title"=>"零元扣款测试资源","post_status"=>"publish","post_type"=>"post","post_author"=>1));
update_post_meta($id,"down_price","2");
echo $id;
')
echo "测试文章 ID=$PID，价格=2"

# 给 tester 充足余额 100，并清掉该文章的历史购买记录（可重复执行）
# 余额模型：可用 = ice_have_money(总充值) - ice_get_money(已消费)，扣款是加 get 而非减 have
WP eval '
global $wpdb;
$u='"$UID_T"';
$wpdb->query("UPDATE {$wpdb->prefix}ice_info SET ice_have_money=100, ice_get_money=0 WHERE ice_user_id=$u");
$wpdb->query("DELETE FROM {$wpdb->prefix}ice_download WHERE user_id=$u AND post_id='"$PID"'");
' >/dev/null

# 可用余额 = have - get
OKMONEY() { WP eval 'global $wpdb; $r=$wpdb->get_row("SELECT ice_have_money,ice_get_money FROM {$wpdb->prefix}ice_info WHERE ice_user_id='"$UID_T"'"); echo (float)($r->ice_have_money - $r->ice_get_money);'; }
BEFORE=$(OKMONEY)

RESP=$(WP eval '
wp_set_current_user('"$UID_T"');
$_POST["post_id"] = "'"$PID"'";
do_action("wp_ajax_epd_wppay");
' 2>/dev/null)
echo "接口返回: $RESP"

AFTER=$(OKMONEY)
DEDUCT=$(php -r "echo ((float)'$BEFORE')-((float)'$AFTER');")
echo "可用余额: $BEFORE -> $AFTER (实扣=$DEDUCT，期望=2)"

# 断言：实扣必须等于 2（价格×兜底比例1），不能是 0；且接口须返回余额支付成功码 202
PAYOK=$(echo "$RESP" | grep -c '"status":202' || true)
if [ "$PAYOK" -ge 1 ] && php -r "exit(abs(((float)'$DEDUCT')-2.0) < 0.001 ? 0 : 1);"; then
  echo "PASS: 兑换比例为空时仍按价格实扣 2 元，未发生 0 元到手"
  RC=0
else
  echo "FAIL: 支付成功码(202)=$PAYOK，实扣=$DEDUCT，期望 2；比例未配置导致扣款异常"
  RC=1
fi

# 清理：删除测试文章
WP post delete "$PID" --force >/dev/null 2>&1
exit $RC
