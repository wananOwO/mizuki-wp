<?php
require_once('../../../../../wp-load.php');
$secretkey = get_option('erphpdown_paypy_key');
$sign = $_POST['sign'];
$total_fee = $_POST['qr_price'];
$extension= $_POST['extension'];
$out_trade_no = esc_sql($_POST['order_id']);

if($sign == md5(md5($_POST['order_id']).$secretkey) && $secretkey){
	if(strstr($out_trade_no,'MD') || strstr($out_trade_no,'FK')){
		epd_set_wppay_success($out_trade_no,$total_fee,'paypy');
	}else{
		epd_set_order_success($out_trade_no,$total_fee,'paypy');
	}
	echo 'success';
	exit;
}