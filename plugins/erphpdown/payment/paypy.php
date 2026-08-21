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
$secretkey = get_option('erphpdown_paypy_key');
$api = get_option('erphpdown_paypy_api').'api/order/';

header("Content-Type: text/html;charset=utf-8");
date_default_timezone_set('Asia/Shanghai');

$epd_order = _epd_create_page_order('paypy');
$price = $epd_order['price'];
$trade_order_id = $epd_order['trade_order_id'];
$subject = $epd_order['subject'];
$money_info=$wpdb->get_row("select * from ".$wpdb->icemoney." where ice_num='".$trade_order_id."'");

$order_type = 'wechat';
if(isset($_GET['type']) && $_GET['type'] == 'alipay') $order_type = 'alipay';

$sign = md5(md5($trade_order_id.$price).$secretkey);
$logged_ip = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? esc_sql($_SERVER['HTTP_X_FORWARDED_FOR']) : esc_sql($_SERVER['REMOTE_ADDR']);
if(function_exists('paypy_install') && $api == (PAYPY_URL.'/api/order/')){
    $minute = get_option("paypy_minute");
    $secretkey = get_option("paypy_key");
    $max = get_option("paypy_max");
    $paypy_fresh = get_option("paypy_fresh");
    $paypy_alipay_trans = get_option("paypy_alipay_trans");
    $alipayUid = get_option("paypy_alipayUid");
    $paypy_method = get_option("paypy_method");
    $order_id = $trade_order_id;
    $order_price = $price;
    $order_name = $subject;
    $order_ip = $logged_ip;
    $redirect_url = constant("erphpdown")."payment/paypy/notify.php";
    $extension = "erphpdown-".$order_type;

    $code = '-1';
    $msg = '';
    $can = 0;
    $qr_price = $order_price;
    $qr_url = '';
    if($sign == md5(md5($order_id.$order_price).$secretkey) && paypy_active()){
        if($paypy_method){ //随意金额
            if($order_ip && $paypy_fresh){
                $check_or = $wpdb->get_var("select id from ".$wpdb->prefix."paypy_orders where qr_price='".$qr_price."' and order_type='".$order_type."' and qr_ip = '".$order_ip."' and created_at >= SUBDATE(NOW(), INTERVAL ".$minute." MINUTE)");
                if($check_or){ //有这个IP的价格
                    $del_ord = $wpdb->query("delete from ".$wpdb->prefix."paypy_orders where id='".$check_or."' and pay_status='未支付'");
                    if($del_ord){ //删除这个IP的价格
                        if($order_type == 'alipay' && $paypy_alipay_trans && $alipayUid){
                            $qr_url = 'alipays%3A%2F%2Fplatformapi%2Fstartapp%3FappId%3D20000123%26actionType%3Dscan%26biz_data%3D%7B"s"%3A+"money"%2C+"u"%3A+"'.$alipayUid.'"%2C+"a"%3A+"'.$qr_price.'"%2C+"m"%3A+"'.$order_id.'"%7D';
                            $can = 1;
                        }else{
                            $qr_url = $wpdb->get_var("select qr_url from ".$wpdb->prefix."paypy_qrcodes where qr_price='0.00' and qr_type='".$order_type."'");
                            if($qr_url){
                                $can = 1;
                            }
                        }
                    }else{ //没删成功 正常减免
                        for($i = 1;$i <= $max;$i ++){
                            $qr_price = $order_price - 0.01*$i;
                            if($qr_price > 0){
                                $check_or = $wpdb->get_var("select id from ".$wpdb->prefix."paypy_orders where qr_price='".$qr_price."' and order_type='".$order_type."' and created_at >= SUBDATE(NOW(), INTERVAL ".$minute." MINUTE)");
                                if($check_or){
                                    continue;
                                }else{
                                    if($order_type == 'alipay' && $paypy_alipay_trans && $alipayUid){
                                        $qr_url = 'alipays%3A%2F%2Fplatformapi%2Fstartapp%3FappId%3D20000123%26actionType%3Dscan%26biz_data%3D%7B"s"%3A+"money"%2C+"u"%3A+"'.$alipayUid.'"%2C+"a"%3A+"'.$qr_price.'"%2C+"m"%3A+"'.$order_id.'"%7D';
                                        $can = 1;
                                        break;
                                    }else{
                                        $qr_url = $wpdb->get_var("select qr_url from ".$wpdb->prefix."paypy_qrcodes where qr_price='0.00' and qr_type='".$order_type."'");
                                        if($qr_url){
                                            $can = 1;
                                            break;
                                        }else{
                                            continue;
                                        }
                                    }
                                }
                            }
                        }
                        if($i == $max+1) $can = 3;
                    }
                }else{ //没有的话得判断全局有没有这个价格
                    $check_or = $wpdb->get_var("select id from ".$wpdb->prefix."paypy_orders where qr_price='".$qr_price."' and order_type='".$order_type."' and created_at >= SUBDATE(NOW(), INTERVAL ".$minute." MINUTE)");
                    if($check_or){
                        for($i = 1;$i <= $max;$i ++){
                            $qr_price = $order_price - 0.01*$i;
                            if($qr_price > 0){
                                $check_or = $wpdb->get_var("select id from ".$wpdb->prefix."paypy_orders where qr_price='".$qr_price."' and order_type='".$order_type."' and qr_ip = '".$order_ip."' and created_at >= SUBDATE(NOW(), INTERVAL ".$minute." MINUTE)");
                                if($check_or){ //检查是否有该IP的减免价格
                                    $del_ord = $wpdb->query("delete from ".$wpdb->prefix."paypy_orders where id='".$check_or."' and pay_status='未支付'");
                                    if($del_ord){
                                        if($order_type == 'alipay' && $paypy_alipay_trans && $alipayUid){
                                            $qr_url = 'alipays%3A%2F%2Fplatformapi%2Fstartapp%3FappId%3D20000123%26actionType%3Dscan%26biz_data%3D%7B"s"%3A+"money"%2C+"u"%3A+"'.$alipayUid.'"%2C+"a"%3A+"'.$qr_price.'"%2C+"m"%3A+"'.$order_id.'"%7D';
                                            $can = 1;
                                            break;
                                        }else{
                                            $qr_url = $wpdb->get_var("select qr_url from ".$wpdb->prefix."paypy_qrcodes where qr_price='0.00' and qr_type='".$order_type."'");
                                            if($qr_url){
                                                $can = 1;
                                                break;
                                            }else{
                                                continue;
                                            }
                                        }
                                    }else{
                                        continue;
                                    }
                                }else{
                                    $check_or = $wpdb->get_var("select id from ".$wpdb->prefix."paypy_orders where qr_price='".$qr_price."' and order_type='".$order_type."' and created_at >= SUBDATE(NOW(), INTERVAL ".$minute." MINUTE)");
                                    if($check_or){
                                        continue;
                                    }else{
                                        if($order_type == 'alipay' && $paypy_alipay_trans && $alipayUid){
                                            $qr_url = 'alipays%3A%2F%2Fplatformapi%2Fstartapp%3FappId%3D20000123%26actionType%3Dscan%26biz_data%3D%7B"s"%3A+"money"%2C+"u"%3A+"'.$alipayUid.'"%2C+"a"%3A+"'.$qr_price.'"%2C+"m"%3A+"'.$order_id.'"%7D';
                                            $can = 1;
                                            break;
                                        }else{
                                            $qr_url = $wpdb->get_var("select qr_url from ".$wpdb->prefix."paypy_qrcodes where qr_price='0.00' and qr_type='".$order_type."'");
                                            if($qr_url){
                                                $can = 1;
                                                break;
                                            }else{
                                                continue;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        if($i == $max+1) $can = 3;
                    }else{
                        if($order_type == 'alipay' && $paypy_alipay_trans && $alipayUid){
                            $qr_url = 'alipays%3A%2F%2Fplatformapi%2Fstartapp%3FappId%3D20000123%26actionType%3Dscan%26biz_data%3D%7B"s"%3A+"money"%2C+"u"%3A+"'.$alipayUid.'"%2C+"a"%3A+"'.$qr_price.'"%2C+"m"%3A+"'.$order_id.'"%7D';
                            $can = 1;
                        }else{
                            $qr_url = $wpdb->get_var("select qr_url from ".$wpdb->prefix."paypy_qrcodes where qr_price='0.00' and qr_type='".$order_type."'");
                            if($qr_url){
                                $can = 1;
                            }
                        }
                    }
                }
            }else{
                $check_or = $wpdb->get_var("select id from ".$wpdb->prefix."paypy_orders where qr_price='".$qr_price."' and order_type='".$order_type."' and created_at >= SUBDATE(NOW(), INTERVAL ".$minute." MINUTE)");
                if($check_or){
                    for($i = 1;$i <= $max;$i ++){
                        $qr_price = $order_price - 0.01*$i;
                        if($qr_price > 0){
                            $check_or = $wpdb->get_var("select id from ".$wpdb->prefix."paypy_orders where qr_price='".$qr_price."' and order_type='".$order_type."' and created_at >= SUBDATE(NOW(), INTERVAL ".$minute." MINUTE)");
                            if($check_or){
                                continue;
                            }else{
                                if($order_type == 'alipay' && $paypy_alipay_trans && $alipayUid){
                                    $qr_url = 'alipays%3A%2F%2Fplatformapi%2Fstartapp%3FappId%3D20000123%26actionType%3Dscan%26biz_data%3D%7B"s"%3A+"money"%2C+"u"%3A+"'.$alipayUid.'"%2C+"a"%3A+"'.$qr_price.'"%2C+"m"%3A+"'.$order_id.'"%7D';
                                    $can = 1;
                                    break;
                                }else{
                                    $qr_url = $wpdb->get_var("select qr_url from ".$wpdb->prefix."paypy_qrcodes where qr_price='0.00' and qr_type='".$order_type."'");
                                    if($qr_url){
                                        $can = 1;
                                        break;
                                    }else{
                                        continue;
                                    }
                                }
                            }
                        }
                    }
                    if($i == $max+1) $can = 3;
                }else{
                    if($order_type == 'alipay' && $paypy_alipay_trans && $alipayUid){
                        $qr_url = 'alipays%3A%2F%2Fplatformapi%2Fstartapp%3FappId%3D20000123%26actionType%3Dscan%26biz_data%3D%7B"s"%3A+"money"%2C+"u"%3A+"'.$alipayUid.'"%2C+"a"%3A+"'.$qr_price.'"%2C+"m"%3A+"'.$order_id.'"%7D';
                        $can = 1;
                    }else{
                        $qr_url = $wpdb->get_var("select qr_url from ".$wpdb->prefix."paypy_qrcodes where qr_price='0.00' and qr_type='".$order_type."'");
                        if($qr_url){
                            $can = 1;
                        }
                    }
                }
            }
        }else{ //固定金额
            $check_qr = $wpdb->get_var("select id from ".$wpdb->prefix."paypy_qrcodes where qr_price='".$qr_price."' and qr_type='".$order_type."'");
            if($check_qr || ($order_type == 'alipay' && $paypy_alipay_trans && $alipayUid)){
                if($order_ip && $paypy_fresh){
                    $check_or = $wpdb->get_var("select id from ".$wpdb->prefix."paypy_orders where qr_price='".$qr_price."' and order_type='".$order_type."' and qr_ip = '".$order_ip."' and created_at >= SUBDATE(NOW(), INTERVAL ".$minute." MINUTE)");
                    if($check_or){ //有这个IP的价格  
                        $del_ord = $wpdb->query("delete from ".$wpdb->prefix."paypy_orders where id='".$check_or."' and pay_status='未支付'");
                        if($del_ord){ //删除这个IP的价格
                            if($order_type == 'alipay' && $paypy_alipay_trans && $alipayUid){
                                $qr_url = 'alipays%3A%2F%2Fplatformapi%2Fstartapp%3FappId%3D20000123%26actionType%3Dscan%26biz_data%3D%7B"s"%3A+"money"%2C+"u"%3A+"'.$alipayUid.'"%2C+"a"%3A+"'.$qr_price.'"%2C+"m"%3A+"'.$order_id.'"%7D';
                                $can = 1;
                            }else{
                                $qr_url = $wpdb->get_var("select qr_url from ".$wpdb->prefix."paypy_qrcodes where qr_price='".$qr_price."' and qr_type='".$order_type."'");
                                if($qr_url){
                                    $can = 1;
                                }else{
                                    $can = 2;
                                }
                            }
                        }else{ //没删成功 正常减免
                            for($i = 1;$i <= $max;$i ++){
                                $qr_price = $order_price - 0.01*$i;
                                if($qr_price > 0){
                                    $check_qr = $wpdb->get_var("select id from ".$wpdb->prefix."paypy_qrcodes where qr_price='".$qr_price."' and qr_type='".$order_type."'");
                                    if($check_qr || ($order_type == 'alipay' && $paypy_alipay_trans && $alipayUid)){
                                        $check_or = $wpdb->get_var("select id from ".$wpdb->prefix."paypy_orders where qr_price='".$qr_price."' and order_type='".$order_type."' and created_at >= SUBDATE(NOW(), INTERVAL ".$minute." MINUTE)");
                                        if($check_or){
                                            continue;
                                        }else{
                                            if($order_type == 'alipay' && $paypy_alipay_trans && $alipayUid){
                                                $qr_url = 'alipays%3A%2F%2Fplatformapi%2Fstartapp%3FappId%3D20000123%26actionType%3Dscan%26biz_data%3D%7B"s"%3A+"money"%2C+"u"%3A+"'.$alipayUid.'"%2C+"a"%3A+"'.$qr_price.'"%2C+"m"%3A+"'.$order_id.'"%7D';
                                                $can = 1;
                                                break;
                                            }else{
                                                $qr_url = $wpdb->get_var("select qr_url from ".$wpdb->prefix."paypy_qrcodes where qr_price='".$qr_price."' and qr_type='".$order_type."'");
                                                if($qr_url){
                                                    $can = 1;
                                                    break;
                                                }else{
                                                    continue;
                                                }
                                            }
                                        }
                                    }else{
                                        continue;
                                    }
                                }
                            }
                            if($i == $max+1) $can = 3;
                        }
                    }else{ //没有的话得判断全局有没有这个价格
                        $check_or = $wpdb->get_var("select id from ".$wpdb->prefix."paypy_orders where qr_price='".$qr_price."' and order_type='".$order_type."' and created_at >= SUBDATE(NOW(), INTERVAL ".$minute." MINUTE)");
                        if($check_or){
                            for($i = 1;$i <= $max;$i ++){
                                $qr_price = $order_price - 0.01*$i;
                                if($qr_price > 0){
                                    $check_qr = $wpdb->get_var("select id from ".$wpdb->prefix."paypy_qrcodes where qr_price='".$qr_price."' and qr_type='".$order_type."'");
                                    if($check_qr || ($order_type == 'alipay' && $paypy_alipay_trans && $alipayUid)){
                                        $check_or = $wpdb->get_var("select id from ".$wpdb->prefix."paypy_orders where qr_price='".$qr_price."' and order_type='".$order_type."' and qr_ip = '".$order_ip."' and created_at >= SUBDATE(NOW(), INTERVAL ".$minute." MINUTE)");
                                        if($check_or){ //检查是否有该IP的减免价格
                                            $del_ord = $wpdb->query("delete from ".$wpdb->prefix."paypy_orders where id='".$check_or."' and pay_status='未支付'");
                                            if($del_ord){
                                                if($order_type == 'alipay' && $paypy_alipay_trans && $alipayUid){
                                                    $qr_url = 'alipays%3A%2F%2Fplatformapi%2Fstartapp%3FappId%3D20000123%26actionType%3Dscan%26biz_data%3D%7B"s"%3A+"money"%2C+"u"%3A+"'.$alipayUid.'"%2C+"a"%3A+"'.$qr_price.'"%2C+"m"%3A+"'.$order_id.'"%7D';
                                                    $can = 1;
                                                    break;
                                                }else{
                                                    $qr_url = $wpdb->get_var("select qr_url from ".$wpdb->prefix."paypy_qrcodes where qr_price='".$qr_price."' and qr_type='".$order_type."'");
                                                    if($qr_url){
                                                        $can = 1;
                                                        break;
                                                    }else{
                                                        continue;
                                                    }
                                                }
                                            }else{
                                                continue;
                                            }
                                        }else{
                                            $check_or = $wpdb->get_var("select id from ".$wpdb->prefix."paypy_orders where qr_price='".$qr_price."' and order_type='".$order_type."' and created_at >= SUBDATE(NOW(), INTERVAL ".$minute." MINUTE)");
                                            if($check_or){
                                                continue;
                                            }else{
                                                if($order_type == 'alipay' && $paypy_alipay_trans && $alipayUid){
                                                    $qr_url = 'alipays%3A%2F%2Fplatformapi%2Fstartapp%3FappId%3D20000123%26actionType%3Dscan%26biz_data%3D%7B"s"%3A+"money"%2C+"u"%3A+"'.$alipayUid.'"%2C+"a"%3A+"'.$qr_price.'"%2C+"m"%3A+"'.$order_id.'"%7D';
                                                    $can = 1;
                                                    break;
                                                }else{
                                                    $qr_url = $wpdb->get_var("select qr_url from ".$wpdb->prefix."paypy_qrcodes where qr_price='".$qr_price."' and qr_type='".$order_type."'");
                                                    if($qr_url){
                                                        $can = 1;
                                                        break;
                                                    }else{
                                                        continue;
                                                    }
                                                }
                                            }
                                        }
                                    }else{
                                        continue;
                                    }
                                }
                            }
                            if($i == $max+1) $can = 3;
                        }else{
                            if($order_type == 'alipay' && $paypy_alipay_trans && $alipayUid){
                                $qr_url = 'alipays%3A%2F%2Fplatformapi%2Fstartapp%3FappId%3D20000123%26actionType%3Dscan%26biz_data%3D%7B"s"%3A+"money"%2C+"u"%3A+"'.$alipayUid.'"%2C+"a"%3A+"'.$qr_price.'"%2C+"m"%3A+"'.$order_id.'"%7D';
                                $can = 1;
                            }else{
                                $qr_url = $wpdb->get_var("select qr_url from ".$wpdb->prefix."paypy_qrcodes where qr_price='".$qr_price."' and qr_type='".$order_type."'");
                                if($qr_url){
                                    $can = 1;
                                }else{
                                    $can = 2;
                                }
                            }
                        }
                    }
                }else{
                    $check_or = $wpdb->get_var("select id from ".$wpdb->prefix."paypy_orders where qr_price='".$qr_price."' and order_type='".$order_type."' and created_at >= SUBDATE(NOW(), INTERVAL ".$minute." MINUTE)");
                    if($check_or){
                        for($i = 1;$i <= $max;$i ++){
                            $qr_price = $order_price - 0.01*$i;
                            if($qr_price > 0){
                                $check_qr = $wpdb->get_var("select id from ".$wpdb->prefix."paypy_qrcodes where qr_price='".$qr_price."' and qr_type='".$order_type."'");
                                if($check_qr || ($order_type == 'alipay' && $paypy_alipay_trans && $alipayUid)){
                                    $check_or = $wpdb->get_var("select id from ".$wpdb->prefix."paypy_orders where qr_price='".$qr_price."' and order_type='".$order_type."' and created_at >= SUBDATE(NOW(), INTERVAL ".$minute." MINUTE)");
                                    if($check_or){
                                        continue;
                                    }else{
                                        if($order_type == 'alipay' && $paypy_alipay_trans && $alipayUid){
                                            $qr_url = 'alipays%3A%2F%2Fplatformapi%2Fstartapp%3FappId%3D20000123%26actionType%3Dscan%26biz_data%3D%7B"s"%3A+"money"%2C+"u"%3A+"'.$alipayUid.'"%2C+"a"%3A+"'.$qr_price.'"%2C+"m"%3A+"'.$order_id.'"%7D';
                                            $can = 1;
                                            break;
                                        }else{
                                            $qr_url = $wpdb->get_var("select qr_url from ".$wpdb->prefix."paypy_qrcodes where qr_price='".$qr_price."' and qr_type='".$order_type."'");
                                            if($qr_url){
                                                $can = 1;
                                                break;
                                            }else{
                                                continue;
                                            }
                                        }
                                    }
                                }else{
                                    continue;
                                }
                            }
                        }
                        if($i == $max+1) $can = 3;
                    }else{
                        if($order_type == 'alipay' && $paypy_alipay_trans && $alipayUid){
                            $qr_url = 'alipays%3A%2F%2Fplatformapi%2Fstartapp%3FappId%3D20000123%26actionType%3Dscan%26biz_data%3D%7B"s"%3A+"money"%2C+"u"%3A+"'.$alipayUid.'"%2C+"a"%3A+"'.$qr_price.'"%2C+"m"%3A+"'.$order_id.'"%7D';
                            $can = 1;
                        }else{
                            $qr_url = $wpdb->get_var("select qr_url from ".$wpdb->prefix."paypy_qrcodes where qr_price='".$qr_price."' and qr_type='".$order_type."'");
                            if($qr_url){
                                $can = 1;
                            }else{
                                $can = 2;
                            }
                        }
                    }
                }
            }
        }
    }else{
        $can = 4;
    }


    if($can == 1){
        if($order_ip && $paypy_fresh){
            $re = $wpdb->query("insert into ".$wpdb->prefix."paypy_orders(order_id,order_type,order_price,order_name,qr_ip,qr_url,qr_price,redirect_url,extension,created_at,updated_at) values('".$order_id."','".$order_type."','".$order_price."','".$order_name."','".$order_ip."','".$qr_url."','".$qr_price."','".$redirect_url."','".$extension."','".date("Y-m-d H:i:s")."','".date("Y-m-d H:i:s")."')");
        }else{
            $re = $wpdb->query("insert into ".$wpdb->prefix."paypy_orders(order_id,order_type,order_price,order_name,qr_url,qr_price,redirect_url,extension,created_at,updated_at) values('".$order_id."','".$order_type."','".$order_price."','".$order_name."','".$qr_url."','".$qr_price."','".$redirect_url."','".$extension."','".date("Y-m-d H:i:s")."','".date("Y-m-d H:i:s")."')");
        }

        if($re){
            $code = '1';
        }else{
            $msg = '系统超时，请稍后重试！';
        }
    }elseif($can == 2){
        $msg = '系统超时，请稍后重试！';
    }elseif($can == 3){
        $msg = '操作太快啦，请您等待'.$minute.'分钟后再来！';
    }elseif($can == 4){
        $msg = '请求失败，请检查配置是否正确！';
    }else{
        $msg = '此商户未上传此价格的收款二维码！';
    }

    $resultArray = array(
        'code' => $code,
        'qr_price' => sprintf("%.2f",$qr_price),
        'qr_url' => $qr_url?urlencode($qr_url):$qr_url,
        'qr_minute' => $minute,
        'msg' => $msg
    );
}else{
    if(get_option('erphpdown_paypy_curl')){
        $result = erphpdown_curl_post($api,"order_id=".$trade_order_id."&order_type=".$order_type."&order_price=".$price."&order_ip=".$logged_ip."&order_name=".$subject."&sign=".$sign."&redirect_url=".constant("erphpdown")."payment/paypy/notify.php"."&extension=erphpdown-".$order_type);
        $result = trim($result, "\xEF\xBB\xBF");
        $resultArray = json_decode($result,true);
    }else{
        $body = array("order_id"=>$trade_order_id, "order_type"=>$order_type, "order_price"=>$price, "order_ip"=>$logged_ip, "order_name"=>$subject, "sign"=>$sign, "redirect_url"=>constant("erphpdown")."payment/paypy/notify.php", "extension"=>"erphpdown-".$order_type);
        $result = wp_remote_request($api, array("method" => "POST", "body"=>$body));
        if(is_array($result)){
            $resultArray = json_decode($result['body'],true);
        }else{
            echo '获取支付失败：网站与paypy系统通信失败';
            exit;
        }
    }
}

if(!is_array($resultArray)){
    echo '获取支付失败：网站与paypy系统通信失败';
    exit;
}

if($resultArray['code'] != '1'){
	echo '获取支付失败：'.$resultArray['msg'];
}else{
?>
<html>
<head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1" /> 
    <title><?php echo ($order_type=='alipay')?'支付宝':'微信';?>支付</title>
    <link rel='stylesheet'  href='../static/erphpdown.css' type='text/css' media='all' />
    <link rel="shortcut icon" href="<?php echo get_option('erphp_url_front_favicon');?>">
</head>
<body<?php if(!isset($_GET['iframe'])){echo ' class="erphpdown-page-pay"';}?>>

	<div class="wppay-custom-modal-box mobantu-wppay erphpdown-custom-modal-box">
		<section class="wppay-modal">
            <section class="erphp-wppay-qrcode mobantu-wppay">
                <section class="tab">
                    <a href="javascript:;" class="active"><div class="payment"><img src="<?php echo constant("erphpdown");?>static/images/<?php echo ($order_type=='alipay')?'payment-alipay':'payment-weixin';?>.png"></div>￥<?php echo sprintf("%.2f",$resultArray['qr_price']);?></a>
                    <?php if($resultArray['qr_price']<$price) echo '<div class="warning">随机减免，请务必支付金额￥'.$resultArray['qr_price'].'</div>';?>
                </section>
                <section class="tab-list" style="background-color: <?php echo ($order_type=='alipay')?'#00a3ee':'#21ab36';?>;">
                    <section class="item">
                        <section class="qr-code">
                            <img src='<?php echo constant("erphpdown").'includes/qrcode.php?data='.urldecode($resultArray['qr_url']);?>' class="img" alt="">
                        </section>
                        <p class="account">支付完成后请等待5秒左右，期间请勿刷新</p>
                        <p id="time" class="desc"></p>
                        <?php if(wp_is_mobile() || erphpdown_is_mobile()){
                            if($order_type=='alipay'){
                        ?>
                            <p class="wap"><a id="erphp-wap-link" href='<?php echo str_replace(' ', '%20', str_replace('"', '%22', urldecode(urldecode($resultArray['qr_url']))));?>' target="_blank"><span>启动支付宝APP支付</span></a></p>
                        <?php
                            }else{
                                echo '<p class="wap">请截屏后，打开微信扫一扫，从相册选择二维码图片</p>';
                            }
                        }?>
                    </section>
                </section>
            </section>
    	</section>
    </div>

    <script src="<?php echo ERPHPDOWN_URL;?>/static/jquery-1.7.min.js"></script>
	<script>
        <?php if(wp_is_mobile() || erphpdown_is_mobile()){?>
            $(function(){$("#erphp-wap-link").find("span").trigger("click");});
        <?php }?>
		erphpOrder = setInterval(function() {
			$.ajax({  
	            type: 'POST',  
	            url: '<?php echo ERPHPDOWN_URL;?>/admin/action/order.php',  
	            data: {
	            	do: 'checkOrder',
	            	order: '<?php echo $money_info->ice_id;?>',
                    token: '<?php echo $_SESSION['erphpdown_token'];?>'
	            },  
	            dataType: 'text',
	            success: function(data){  
	                if( $.trim(data) == '1' ){
	                    clearInterval(erphpOrder);
                        <?php if(isset($_GET['iframe'])){?>
                            var mylayer= parent.layer.getFrameIndex(window.name);
                            parent.layer.close(mylayer);
                            parent.layer.msg('支付成功！');
                            parent.location.reload();  
                        <?php }else{?>
    	                    alert('支付成功！');
                            <?php if(isset($_COOKIE['erphpdown_return']) && $_COOKIE['erphpdown_return']){?>
                            location.href="<?php echo $_COOKIE['erphpdown_return'];?>";
    	                    <?php }elseif(get_option('erphp_url_front_success')){?>
    	                    location.href="<?php echo get_option('erphp_url_front_success');?>";
    	                    <?php }else{?>
    	                    window.close();
    	                	<?php }?>
                        <?php }?>
	                }  
	            },
	            error: function(XMLHttpRequest, textStatus, errorThrown){
	            	//alert(errorThrown);
	            }
	        });
		}, 5000);

        var m = <?php echo $resultArray['qr_minute'];?>, s = 0;  
        var Timer = document.getElementById("time");
        wppayCountdown();
        erphpTimer = setInterval(function(){ wppayCountdown() },1000);
        function wppayCountdown (){
            Timer.innerHTML = "支付倒计时：<span>0"+m+"分"+s+"秒</span>";
            if( m == 0 && s == 0 ){
                clearInterval(erphpOrder);
                clearInterval(erphpTimer);
                $(".qr-code").append('<div class="expired"></div>');
                m = 4;
                s = 59;
            }else if( m >= 0 ){
                if( s > 0 ){
                    s--;
                }else if( s == 0 ){
                    m--;
                    s = 59;
                }
            }
        }

	</script>
</body>
</html>
<?php
}