<?php
/* *
 * 功能：支付宝服务器异步通知页面
 * 版本：2.0
 * 修改日期：2017-05-01
 * 说明：
 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。

 *************************页面功能说明*************************
 * 创建该页面文件时，请留心该页面文件中无任何HTML代码及空格。
 * 该页面不能在本机电脑测试，请到服务器上做测试。请确保外部可以访问该页面。
 * 如果没有收到该页面返回的 success 信息，支付宝会在24小时内按一定的时间策略重发通知
 */
require_once('../../../../../wp-load.php');
require_once 'config.php';
require_once 'pagepay/service/AlipayTradeService.php';

$sign = $_POST['sign'];
$signType = $_POST['sign_type'];
$total_fee= $_POST['total_amount'];
$out_trade_no = esc_sql($_POST['out_trade_no']);
$trade_no = esc_sql($_POST['trade_no']);
//$buyer_logon_id = esc_sql($_POST['buyer_logon_id']);
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

//$arr=$_POST;
//$alipaySevice = new AlipayTradeService($config); 
//$alipaySevice->writeLog(var_export($_POST,true));
//$result = $alipaySevice->check($arr);

if($result && $config['alipay_public_key']) {//验证成功
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//请在这里加上商户的业务逻辑程序代

	
	//——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
	
    //获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表
	
	//商户订单号
	//$out_trade_no = $_POST['out_trade_no'];

	//支付宝交易号
	//$trade_no = $_POST['trade_no'];

	//$total_fee= $_POST['total_amount'];

	//交易状态
	//$status = $_POST['trade_status'];

    if($status == 'TRADE_FINISHED' || $status == 'TRADE_SUCCESS') {
		//判断该笔订单是否在商户网站中已经做过处理
			//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
			//请务必判断请求时的total_amount与通知时获取的total_fee为一致的
			//如果有做过处理，不执行商户的业务程序
    	if(strstr($out_trade_no,'MD') || strstr($out_trade_no,'FK')){
			epd_set_wppay_success($out_trade_no,$total_fee,'alipay');
		}else{
			epd_set_order_success($out_trade_no,$total_fee,'alipay');	
		}
		//注意：
		//退款日期超过可退款期限后（如三个月可退款），支付宝系统发送该交易状态通知
    }
	//——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
	echo "success";	//请不要修改或删除
}else {
    //验证失败
    echo "fail";

}