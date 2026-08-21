<?php
// +----------------------------------------------------------------------
// | ERPHP [ PHP DEVELOP ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.mobantu.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: mobantu <82708210@qq.com>
// +----------------------------------------------------------------------
session_start();
if(isset($_GET['redirect_url'])){
    $_COOKIE['erphpdown_return'] = urldecode($_GET['redirect_url']);
    setcookie('erphpdown_return',urldecode($_GET['redirect_url']),0,'/');
}else{
    $_COOKIE['erphpdown_return'] = '';
    setcookie('erphpdown_return','',0,'/');
}

require_once('../../../../wp-load.php');
header("Content-Type: text/html;charset=utf-8");
date_default_timezone_set('Asia/Shanghai');

/*
if(erphpdown_is_weixin()){
    echo '<img src="'.ERPHPDOWN_URL.'/static/images/browser.gif" />';
    echo '<div style="margin:100px auto 0;text-align:center;font-size:16px;padding:0 30px;">'.erphpdown_selfurl().'</div>';
    exit;
}*/

$epd_order = _epd_create_page_order('alipay');
$price = $epd_order['price'];
$out_trade_no = $epd_order['trade_order_id'];
$subject = $epd_order['subject'];
//$money_info=$wpdb->get_row("select * from ".$wpdb->icemoney." where ice_num='".$out_trade_no."'");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>正在前往支付宝...</title>
    <link rel="shortcut icon" href="<?php echo get_option('erphp_url_front_favicon');?>">
    <style>input{display:none}</style>
</head>
<?php
    $ice_ali_app  = get_option('ice_ali_app');
    $erphpdown_alipay_type = get_option('erphpdown_alipay_type');

    if($ice_ali_app && erphpdown_is_mobile()){
        require_once("alipay-h5/wappay/service/AlipayTradeService.php");
        require_once("alipay-h5/wappay/buildermodel/AlipayTradeWapPayContentBuilder.php");
        require_once("alipay-h5/config.php");

        $payRequestBuilder = new AlipayTradeWapPayContentBuilder();
        $payRequestBuilder->setBody('');
        $payRequestBuilder->setSubject($subject);
        $payRequestBuilder->setOutTradeNo($out_trade_no);
        $payRequestBuilder->setTotalAmount($price);
        $payRequestBuilder->setTimeExpress("1m");
        $payResponse = new AlipayTradeService($config);
        $result=$payResponse->wapPay($payRequestBuilder,$config['return_url'],$config['notify_url']);
        return ;
    }elseif($erphpdown_alipay_type == 'create_trade_pay_by_user'){
        require_once 'alipay/config.php';
        require_once 'alipay/pagepay/service/AlipayTradeService.php';
        require_once 'alipay/pagepay/buildermodel/AlipayTradePagePayContentBuilder.php';

        //付款金额，必填
        $total_amount = $price;

        //商品描述，可空
        $body = "";

        //构造参数
        $payRequestBuilder = new AlipayTradePagePayContentBuilder();
        $payRequestBuilder->setBody($body);
        $payRequestBuilder->setSubject($subject);
        $payRequestBuilder->setTotalAmount($total_amount);
        $payRequestBuilder->setOutTradeNo($out_trade_no);

        $aop = new AlipayTradeService($config);

        /**
         * pagePay 电脑网站支付请求
         * @param $builder 业务参数，使用buildmodel中的对象生成。
         * @param $return_url 同步跳转地址，公网可以访问
         * @param $notify_url 异步通知地址，公网可以访问
         * @return $response 支付宝返回的信息
        */
        $response = $aop->pagePay($payRequestBuilder,$config['return_url'],$config['notify_url']);

        //输出表单
        var_dump($response);
        exit;
    }else{
	
        require_once("alipay/alipay.config.php");
        require_once("alipay/lib/alipay_submit.class.php");
    	/**************************请求参数**************************/

            //支付类型
            $payment_type = "1";
            //必填，不能修改
            //服务器异步通知页面路径
            $notify_url = constant("erphpdown").'payment/alipay/notify_url.php';
            //需http://格式的完整路径，不能加?id=123这类自定义参数

            //页面跳转同步通知页面路径
            $return_url = constant("erphpdown").'payment/alipay/return_url.php';
            //需http://格式的完整路径，不能加?id=123这类自定义参数，不能写成http://localhost/

            //商品数量
            $quantity = "1";
            //必填，建议默认为1，不改变值，把一次交易看成是一次下订单而非购买一件商品
            //物流费用
            $logistics_fee = "0.00";
            //必填，即运费
            //物流类型
            $logistics_type = "EXPRESS";
            //必填，三个值可选：EXPRESS（快递）、POST（平邮）、EMS（EMS）
            //物流支付方式
            $logistics_payment = "SELLER_PAY";
            //必填，两个值可选：SELLER_PAY（卖家承担运费）、BUYER_PAY（买家承担运费）
            //订单描述

            $body = '';
            //商品展示地址
            $show_url = '';
            //需以http://开头的完整路径，如：http://www.商户网站.com/myorder.html

            $receive_name= "张三"; //收货人姓名，如：张三
    		$receive_address= "XX省XXX市XXX区XXX路XXX小区XXX栋XXX单元XXX号"; //收货人地址，如：XX省XXX市XXX区XXX路XXX小区XXX栋XXX单元XXX号
    		$receive_zip= "123456"; //收货人邮编，如：123456
    		$receive_phone= "0571-81234567"; //收货人电话号码，如：0571-81234567
    		$receive_mobile= "13312341234"; //收货人手机号码，如：13312341234


    	/************************************************************/
    	
    	//构造要请求的参数数组，无需改动
    	$parameter = array(
    			"service" => "create_direct_pay_by_user",
    			"partner" => trim($alipay_config['partner']),
    			"seller_email" => trim($alipay_config['seller_email']),
    			"payment_type"	=> $payment_type,
    			"notify_url"	=> $notify_url,
    			"return_url"	=> $return_url,
    			"out_trade_no"	=> $out_trade_no,
    			"subject"	=> $subject,
    			"price"	=> $price,
    			"quantity"	=> $quantity,
    			"logistics_fee"	=> $logistics_fee,
    			"logistics_type"	=> $logistics_type,
    			"logistics_payment"	=> $logistics_payment,
    			"body"	=> $body,
    			"show_url"	=> $show_url,
    			"receive_name"	=> $receive_name,
    			"receive_address"	=> $receive_address,
    			"receive_zip"	=> $receive_zip,
    			"receive_phone"	=> $receive_phone,
    			"receive_mobile"	=> $receive_mobile,
    			"_input_charset"	=> trim(strtolower($alipay_config['input_charset']))
    	);
    	
    	//建立请求
    	$alipaySubmit = new AlipaySubmit($alipay_config);
    	$html_text = $alipaySubmit->buildRequestForm($parameter,"get", "确认");
    	echo $html_text;
    }

?>
</body>
</html>