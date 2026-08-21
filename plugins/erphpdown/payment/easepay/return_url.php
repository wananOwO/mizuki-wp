<?php
require_once('../../../../../wp-config.php');
require_once("easepay.config.php");
require_once("lib/epay_notify.class.php");

$alipayNotify = new AlipayNotify($alipay_config);
$verify_result = $alipayNotify->verifyReturn();
if($verify_result) {

	$out_trade_no = $_GET['out_trade_no'];

	$trade_no = $_GET['trade_no'];

	//交易状态
	$trade_status = $_GET['trade_status'];

	//支付方式
	$type = $_GET['type'];


    if($_GET['trade_status'] == 'TRADE_SUCCESS') {
		
    	$re = get_option('erphp_url_front_success');
    	if(isset($_COOKIE['erphpdown_return']) && $_COOKIE['erphpdown_return']){
		    $re = $_COOKIE['erphpdown_return'];
		}
		if($re)
			wp_redirect($re);
		else{
			echo 'success';
			exit;
		}
    }
    else {
      echo "trade_status=".$_GET['trade_status'];
    }

	//——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
	
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
}
else {
    //验证失败
    //如要调试，请看alipay_notify.php页面的verifyReturn函数
    echo "fail";
}
?>