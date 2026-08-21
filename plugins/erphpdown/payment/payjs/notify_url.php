<?php
/* *
 * by mobantu
*/
require_once('../../../../../wp-config.php');
require_once("class.php");

$payjs = new Payjs();
if(isset($_POST['type'])){
    $data = ["return_code" => $_POST['return_code'], "total_fee" => $_POST['total_fee'], "out_trade_no" => $_POST['out_trade_no'], "payjs_order_id" => $_POST['payjs_order_id'], "transaction_id" => $_POST['transaction_id'], "time_end" => $_POST['time_end'], "openid" => $_POST['openid'], "attach" => $_POST['attach'], "mchid" => $_POST['mchid'], "type" => $_POST['type']];
}else{
    $data = ["return_code" => $_POST['return_code'], "total_fee" => $_POST['total_fee'], "out_trade_no" => $_POST['out_trade_no'], "payjs_order_id" => $_POST['payjs_order_id'], "transaction_id" => $_POST['transaction_id'], "time_end" => $_POST['time_end'], "openid" => $_POST['openid'], "attach" => $_POST['attach'], "mchid" => $_POST['mchid']];
}

if($payjs->sign($data) == $_POST['sign'] && $_POST['return_code'] == '1' && get_option('erphpdown_payjs_appid')){

	$total_fee = $_POST['total_fee']/100;
	$out_trade_no = esc_sql($_POST['out_trade_no']);

	if(strstr($out_trade_no,'MD') || strstr($out_trade_no,'FK')){
		epd_set_wppay_success($out_trade_no,$total_fee,'payjs');
	}else{
		epd_set_order_success($out_trade_no,$total_fee,'payjs');
	}
	echo 'success';
}