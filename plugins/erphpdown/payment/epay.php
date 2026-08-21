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

$epd_order = _epd_create_page_order('epay');
$price = $epd_order['price'];
$out_trade_no = $epd_order['trade_order_id'];
$subject = $epd_order['subject'];
$money_info=$wpdb->get_row("select * from ".$wpdb->icemoney." where ice_num='".$out_trade_no."'");

    require_once("epay/epay.config.php");
    require_once("epay/lib/epay_submit.class.php");

    /**************************请求参数**************************/
    $notify_url = ERPHPDOWN_URL.'/payment/epay/notify_url.php';
    //需http://格式的完整路径，不能加?id=123这类自定义参数

    //页面跳转同步通知页面路径
    $return_url = ERPHPDOWN_URL.'/payment/epay/return_url.php';
    //需http://格式的完整路径，不能加?id=123这类自定义参数，不能写成http://localhost/


	//支付方式
    $type='alipay';
    if(isset($_GET['type']) && $_GET['type']) $type = $_GET['type'];

    //返回方式
    $data_type = 'html';
    if(get_option('erphpdown_epay_alipay_json') && $type == 'alipay'){
        $data_type = 'json';
    }elseif(get_option('erphpdown_epay_wxpay_json') && $type == 'wxpay'){
        $data_type = 'json';
    }elseif(get_option('erphpdown_epay_qqpay_json') && $type == 'qqpay'){
        $data_type = 'json';
    }

    /************************************************************/

    if($data_type == 'json' && !(wp_is_mobile() || erphpdown_is_mobile())){
        $parameter = array(
    		"pid" => trim($alipay_config['partner']),
    		"type" => $type,
    		"notify_url"	=> $notify_url,
    		"return_url"	=> $return_url,
    		"out_trade_no"	=> $out_trade_no,
    		"name"	=> $subject,
    		"money"	=> $price,
    		"sitename"	=> get_bloginfo('name'),
            "clientip" => erphpGetIP(),
            //"device" => 'pc'
        );
    }else{
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
    }

    //建立请求
    $alipaySubmit = new AlipaySubmit($alipay_config);
    if($data_type == 'json' && !(wp_is_mobile() || erphpdown_is_mobile())){
        $resultArray = $alipaySubmit->buildRequestJson($parameter);
        //var_dump($resultArray);exit;
        if(is_array($resultArray) && $resultArray['code'] == '1'){
?>
        <html>
        <head>
            <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
            <meta name="viewport" content="width=device-width, initial-scale=1" /> 
            <title><?php if($type == 'qqpay') echo 'QQ钱包'; else echo ($type=='alipay')?'支付宝':'微信';?>支付</title>
            <link rel="shortcut icon" href="<?php echo get_option('erphp_url_front_favicon');?>">
            <link rel='stylesheet'  href='../static/erphpdown.css' type='text/css' media='all' />
        </head>
        <body<?php if(!isset($_GET['iframe'])){echo ' class="erphpdown-page-pay"';}?>>
            <div class="wppay-custom-modal-box mobantu-wppay erphpdown-custom-modal-box">
                <section class="wppay-modal">  
                    <section class="erphp-wppay-qrcode mobantu-wppay">
                        <section class="tab">
                            <a href="javascript:;" class="active"><div class="payment"><img src="<?php echo constant("erphpdown");?>static/images/<?php if($type == 'qqpay') echo 'payment-qqpay'; else echo ($type=='alipay')?'payment-alipay':'payment-weixin';?>.png"></div>￥<?php echo sprintf("%.2f",$price);?></a>
                                   </section>
                        <section class="tab-list" style="background-color: <?php echo ($type=='alipay')?'#00a3ee':'#21ab36';?>;">
                            <section class="item">
                                <section class="qr-code">
                                    <img src="<?php echo constant("erphpdown").'includes/qrcode.php?data='.urlencode($resultArray['qrcode']);?>" class="img" alt="">
                                </section>
                                <p class="account">支付完成后请等待5秒左右</p>
                                <p id="time" class="desc"></p>
                                <?php if(wp_is_mobile() || erphpdown_is_mobile()){
                                    if($type=='alipay'){
                                        echo '<p class="wap"><a id="erphp-wap-link" href="'.$resultArray['qrcode'].'" target="_blank"><span>启动支付宝APP支付</span></a></p>';
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
                                    parent.layer.msg('充值成功！');
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

                var m = 5, s = 0;  
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
        }else{
            echo '创建订单二维码失败！请检查接口配置！';
        }
    }else{
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
    }
?>
</body>
</html>