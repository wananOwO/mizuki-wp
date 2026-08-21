<?php
require_once('../../../../../wp-load.php');
require_once("easepay.config.php");
require_once("lib/epay_notify.class.php");

$alipayNotify = new AlipayNotify($alipay_config);
$verify_result = $alipayNotify->verifyNotify();

if($verify_result && get_option('erphpdown_easepay_id')) {
	$out_trade_no = esc_sql($_GET['out_trade_no']);
	//$trade_no = $_GET['trade_no'];
	$trade_status = $_GET['trade_status'];
	$type = $_GET['type'];
	$total_fee = esc_sql($_GET['money']);

	if ($_GET['trade_status'] == 'TRADE_SUCCESS') {
		if(strstr($out_trade_no,'MD') || strstr($out_trade_no,'FK')){
			epd_set_wppay_success($out_trade_no,$total_fee,'easepay');
		}else{
			epd_set_order_success($out_trade_no,$total_fee,'easepay');
		}
    }

	echo "success";
}else {
    echo "fail";
}
?>