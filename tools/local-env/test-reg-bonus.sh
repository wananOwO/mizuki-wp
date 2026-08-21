#!/usr/bin/env bash
# 问题3 端到端测试：localhost 注册豁免同 IP 防刷，本地可反复领注册奖励
# 复现原限制：同 IP 二次注册 → 命中 reg_ip 已存在 → 不发注册奖励
# 期望：localhost（127.0.0.1）注册时豁免防刷，每个新用户都实发注册奖励
set -uo pipefail
ROOT="e:/code/mizuki-wp"
ENVDIR="$ROOT/tools/local-env"
WPDIR="$ROOT/.wp-local/wordpress"
WP() { php -c "$ENVDIR/php.ini" "$ENVDIR/wp-cli.phar" --path="$WPDIR" "$@"; }

# 关闭“同注册 IP 才发奖励”开关（erphp_aff_ip 为空 → 走含 localhost 豁免的 else 分支）
WP option update erphp_aff_ip '' >/dev/null
# 注册奖励金额设为 8
WP option update ice_ali_money_new '8' >/dev/null
# 新注册不自动发 VIP，避免干扰
WP option update ice_ali_vip_new '' >/dev/null

# 可用余额 = have - get
OKMONEY() { WP eval 'global $wpdb; $r=$wpdb->get_row("SELECT ice_have_money,ice_get_money FROM {$wpdb->prefix}ice_info WHERE ice_user_id='"$1"'"); echo (float)($r->ice_have_money - $r->ice_get_money);'; }

PASS_ALL=1
# 连续注册两个用户，两者都应各自到账 8 元（若防刷未豁免，第二个会因同 IP 命中而漏发）
for N in 1 2; do
  USER="regtest_${N}_$$"
  # 模拟 localhost 注册环境
  # wp_create_user 内部已自动触发 user_register 钩子（即 erphp 发注册奖励的入口），不可再手动 do_action，否则重复发放
  UID_N=$(REMOTE_ADDR=127.0.0.1 WP eval '
    $_SERVER["REMOTE_ADDR"]="127.0.0.1";
    $uid = wp_create_user("'"$USER"'", "pass123456", "'"$USER"'@local.test");
    if (is_wp_error($uid)) { echo "ERR:".$uid->get_error_message(); exit; }
    echo $uid;
  ')
  case "$UID_N" in
    ERR:*) echo "创建用户失败: $UID_N"; PASS_ALL=0; continue ;;
  esac
  BAL=$(OKMONEY "$UID_N")
  echo "用户 $USER (UID=$UID_N) 注册后余额: $BAL (期望=8)"
  if ! php -r "exit(abs(((float)'$BAL')-8.0) < 0.001 ? 0 : 1);"; then
    echo "  -> FAIL: 该用户未足额到账注册奖励"
    PASS_ALL=0
  fi
  # 清理该测试用户
  WP user delete "$UID_N" --yes >/dev/null 2>&1
done

if [ "$PASS_ALL" -eq 1 ]; then
  echo "PASS: localhost 连续两次注册均足额发放注册奖励，同 IP 防刷已正确豁免"
  exit 0
else
  echo "FAIL: 存在未足额发放的注册，豁免逻辑或发钱链路异常"
  exit 1
fi
