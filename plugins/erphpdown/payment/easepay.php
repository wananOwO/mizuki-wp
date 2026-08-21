<?php
// +----------------------------------------------------------------------
// | ERPHP [ PHP DEVELOP ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.mobantu.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: mobantu <82708210@qq.com>
// +----------------------------------------------------------------------
session_start();
$_SESSION['erphpdown_token']=md5(time().rand(100,999));
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

$epd_order = _epd_create_page_order('easepay');
$price = $epd_order['price'];
$out_trade_no = $epd_order['trade_order_id'];
$subject = $epd_order['subject'];
//$money_info=$wpdb->get_row("select * from ".$wpdb->icemoney." where ice_num='".$out_trade_no."'");

    require_once("easepay/easepay.config.php");
    require_once("easepay/lib/epay_submit.class.php");

    /**************************请求参数**************************/
    $notify_url = ERPHPDOWN_URL.'/payment/easepay/notify_url.php';
    //需http://格式的完整路径，不能加?id=123这类自定义参数

    //页面跳转同步通知页面路径
    $return_url = ERPHPDOWN_URL.'/payment/easepay/return_url.php';
    //需http://格式的完整路径，不能加?id=123这类自定义参数，不能写成http://localhost/


	//支付方式
    $type='alipay';
    if(isset($_GET['type']) && $_GET['type']) $type = $_GET['type'];

    /************************************************************/

    $parameter = array(
        "pid" => trim($alipay_config['partner']),
        "type" => $type,
        "notify_url"    => $notify_url,
        "return_url"    => $return_url,
        "out_trade_no"  => $out_trade_no,
        "name"  => $subject,
        "money" => $price,
        "sitename"  => get_bloginfo('name')
    );

    //建立请求
    $alipaySubmit = new AlipaySubmit($alipay_config);
    $html_text = $alipaySubmit->buildRequestForm($parameter);
?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>正在跳转...</title>
        <link rel="shortcut icon" href="<?php echo get_option('erphp_url_front_favicon');?>">
        <style>input{display:none}</style>
    </head>
<?php
    echo $html_text;
?>
</body>
</html>