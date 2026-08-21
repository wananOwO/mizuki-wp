<?php
require_once('../../../../../wp-load.php');
require_once 'f2fpay/service/AlipayTradeService.php';

$sign = $_POST['sign'];
$signType = $_POST['sign_type'];
$total_fee= $_POST['total_amount'];
$out_trade_no = esc_sql($_POST['out_trade_no']);
$trade_no = esc_sql($_POST['trade_no']);
$buyer_logon_id = esc_sql($_POST['buyer_logon_id']);
$status = $_POST['trade_status'];

ksort($_POST);
reset($_POST);
$signStr = '';
foreach ($_POST AS $key => $val) { 
    if ($val == '' || $key == 'sign' || $key == 'sign_type') continue;
    if ($signStr) $signStr .= '&';
    $signStr .= "$key=$val";
}
$signStr = str_replace('\"', '"', $signStr);
$res = "-----BEGIN PUBLIC KEY-----\n".wordwrap($config['alipay_public_key'], 64, "\n", true) ."\n-----END PUBLIC KEY-----";
$result = (bool)openssl_verify($signStr, base64_decode($sign), $res, OPENSSL_ALGO_SHA256);

if($result && $config['alipay_public_key']){
	if($status == 'TRADE_FINISHED' || $status == 'TRADE_SUCCESS') {
		
		if(strstr($out_trade_no,'MD') || strstr($out_trade_no,'FK')){
			epd_set_wppay_success($out_trade_no,$total_fee,'f2fpay');
		}else{
			epd_set_order_success($out_trade_no,$total_fee,'f2fpay');
		}

	    echo "success";
	}
}else{
	echo 'error';
}