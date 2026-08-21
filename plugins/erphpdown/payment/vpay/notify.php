<?php
require_once('../../../../../wp-load.php');
$key=get_option('erphpdown_vpay_key');
if($key){
    $payId = $_GET['payId'];//商户订单号
	$param = $_GET['param'];//创建订单的时候传入的参数
	$type = $_GET['type'];//支付方式 ：微信支付为1 支付宝支付为2
	$price = $_GET['price'];//订单金额
	$reallyPrice = $_GET['reallyPrice'];//实际支付金额
	$sign = $_GET['sign'];//校验签名，计算方式 = md5(payId + param + type + price + reallyPrice + 通讯密钥)
	//开始校验签名
	$_sign =  md5($payId . $param . $type . $price . $reallyPrice . $key);
	if ($_sign != $sign) {
	    echo "error_sign";//sign校验不通过
	    exit();
	}else{
		if(strstr($payId,'MD') || strstr($payId,'FK')){
			$payId = str_replace('ali', '', $payId);
			$payId = str_replace('wx', '', $payId);
			$payId = esc_sql($payId);

			epd_set_wppay_success($payId,$price,'vpay');
		}else{
			epd_set_order_success($payId,$price,'vpay');
		}
	}
	echo "success";
}