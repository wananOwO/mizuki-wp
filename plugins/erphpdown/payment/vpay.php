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

$epd_order = _epd_create_page_order('vpay');
$price = $epd_order['price'];
$trade_order_id = $epd_order['trade_order_id'];
$subject = $epd_order['subject'];
$money_info=$wpdb->get_row("select * from ".$wpdb->icemoney." where ice_num='".$trade_order_id."'");


$type=1;//微信
if(isset($_GET['type']) && $_GET['type']) $type = $_GET['type'];

$param = 'erphpdown';
$key = get_option('erphpdown_vpay_key');
$api = get_option('erphpdown_vpay_api');

$sign = md5($trade_order_id.$param.$type.$price.$key);

if(get_option('erphpdown_vpay_curl')){
    $result = erphpdown_curl_post($api,"payId=".$trade_order_id."&type=".$type."&price=".$price."&sign=".$sign."&notifyUrl=".constant("erphpdown")."payment/vpay/notify.php&returnUrl=".constant("erphpdown")."payment/vpay/return.php&param=".$param);
    $result = trim($result, "\xEF\xBB\xBF");
    $resultArray = json_decode($result,true);
}else{
    $body = array("payId"=>$trade_order_id, "type"=>$type, "price"=>$price, "sign"=>$sign, "notifyUrl"=>constant("erphpdown")."payment/vpay/notify.php", "returnUrl"=>constant("erphpdown")."payment/vpay/return.php", "param"=>$param);
    $result = wp_remote_request($api, array("method" => "POST", "body"=>$body));
    if(is_array($result)){
        $resultArray = json_decode($result['body'],true);
    }else{
        echo '获取支付失败：网站与V免签系统通信失败';
        exit;
    }
}

if(!is_array($resultArray)){
    echo '获取支付失败：网站与V免签系统通信失败';
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
    <title><?php echo ($type=='2')?'支付宝':'微信';?>支付</title>
    <link rel='stylesheet'  href='../static/erphpdown.css' type='text/css' media='all' />
    <link rel="shortcut icon" href="<?php echo get_option('erphp_url_front_favicon');?>">
</head>
<body<?php if(!isset($_GET['iframe'])){echo ' class="erphpdown-page-pay"';}?>>

	<div class="wppay-custom-modal-box mobantu-wppay erphpdown-custom-modal-box">
		<section class="wppay-modal">
            <section class="erphp-wppay-qrcode mobantu-wppay">
                <section class="tab">
                    <a href="javascript:;" class="active"><div class="payment"><img src="<?php echo constant("erphpdown");?>static/images/<?php echo ($type=='2')?'payment-alipay':'payment-weixin';?>.png"></div>￥<?php echo sprintf("%.2f",$resultArray['data']['reallyPrice']);?></a>
                    <?php if($resultArray['data']['reallyPrice'] != $price) echo '<div class="warning">随机增减，请务必支付金额￥'.$resultArray['data']['reallyPrice'].'</div>';?>
                </section>
                <section class="tab-list" style="background-color: <?php echo ($type=='2')?'#00a3ee':'#21ab36';?>;">
                    <section class="item">
                        <section class="qr-code">
                            <img src="<?php echo constant("erphpdown").'includes/qrcode.php?data='.$resultArray['data']['payUrl'];?>" class="img" alt="">
                        </section>
                        <p class="account">支付完成后请等待5秒左右，期间请勿刷新</p>
                        <p id="time" class="desc"></p>
                        <?php if(wp_is_mobile() || erphpdown_is_mobile()){
                            if($type=='2'){
                                echo '<p class="wap"><a id="erphp-wap-link" href="'.$resultArray['data']['payUrl'].'" target="_blank"><span>启动支付宝APP支付</span></a></p>';
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

        var m = <?php echo $resultArray['data']['timeOut'];?>, s = 0;  
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