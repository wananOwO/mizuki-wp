<?php
/* *
 * 功能：支付宝服务器异步通知页面
 * 版本：2.0
 * 修改日期：2016-11-01
 * 说明：
 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。

 *************************页面功能说明*************************
 * 创建该页面文件时，请留心该页面文件中无任何HTML代码及空格。
 * 该页面不能在本机电脑测试，请到服务器上做测试。请确保外部可以访问该页面。
 * 如果没有收到该页面返回的 success 信息，支付宝会在24小时内按一定的时间策略重发通知
 */
require_once('../../../../../wp-load.php');
require_once("config.php");
require_once 'wappay/service/AlipayTradeService.php';

$sign = $_POST['sign'];
$signType = $_POST['sign_type'];
$total_fee= $_POST['total_amount'];
$out_trade_no = esc_sql($_POST['out_trade_no']);
$trade_no = esc_sql($_POST['trade_no']);
$buyer_logon_id = esc_sql($_POST['buyer_logon_id']);
$status = $_POST['trade_status'];

ksort($_POST); //排序post参数
reset($_POST); //内部指针指向数组中的第一个元素
$signStr = '';//初始化
foreach ($_POST AS $key => $val) { //遍历POST参数
    if ($val == '' || $key == 'sign' || $key == 'sign_type') continue; //跳过这些不签名
    if ($signStr) $signStr .= '&'; //第一个字符串签名不加& 其他加&连接起来参数
    $signStr .= "$key=$val"; //拼接为url参数形式
}
$signStr = str_replace('\"', '"', $signStr);
$res = "-----BEGIN PUBLIC KEY-----\n".wordwrap($config['alipay_public_key'], 64, "\n", true) ."\n-----END PUBLIC KEY-----";
$result = (bool)openssl_verify($signStr, base64_decode($sign), $res, OPENSSL_ALGO_SHA256);

if($result && $config['alipay_public_key']){
	if($status == 'TRADE_FINISHED' || $status == 'TRADE_SUCCESS') {
        if(strstr($out_trade_no,'MD') || strstr($out_trade_no,'FK')){
            epd_set_wppay_success($out_trade_no,$total_fee,'alipay');
        }else{
    		epd_set_order_success($out_trade_no,$total_fee,'alipay');   
        }
		echo "success";
	}
}else {
    echo "fail";
}

