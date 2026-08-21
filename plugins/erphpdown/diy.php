<?php 
// +----------------------------------------------------------------------
// | ERPHP [ PHP DEVELOP ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.mobantu.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: mobantu <82708210@qq.com>
// +----------------------------------------------------------------------

function epd_download_page($msg, $pid=0){
?>
    <html lang="zh-CN">
        <head>
            <meta charset="UTF-8" />
            <link rel="stylesheet" href="<?php echo constant("erphpdown");?>static/erphpdown.css" type="text/css" />
            <link rel="stylesheet" href="<?php echo constant("erphpdown");?>static/erphpdown-mizuki.css" type="text/css" />
            <link rel="shortcut icon" href="<?php echo get_option('erphp_url_front_favicon');?>">
            <script src="<?php echo constant("erphpdown"); ?>static/jquery-1.7.min.js"></script>
            <script src="<?php echo constant("erphpdown"); ?>static/erphpdown.js"></script>
            <title><?php _e("文件下载",'erphpdown')?> - <?php echo get_the_title($pid);?> - <?php bloginfo('name');?></title>
            <style>
                ::-webkit-scrollbar {width:6px;height:6px}
                ::-webkit-scrollbar-thumb {background-color: #c7c7c7;border-radius:0;}
                <?php echo get_option('erphp_custom_css');?>
            </style>
        </head>
        <body class="erphpdown-body">
        	<div id="erphpdown-download">
                <!-- 以下内容不要动 -->
        		<div class="msg"><?php echo $msg;?></div>
                <!-- 以上内容不要动 -->
                <?php do_action('erphpdown_download_ad');?>
            </div>
        </body>
    </html>
<?php 
    exit;
}

function epd_wait_page($pid=0){
    $erphp_url_front_vip = get_bloginfo('wpurl').'/wp-admin/admin.php?page=erphpdown/admin/erphp-update-vip.php';
    if(get_option('erphp_url_front_vip')){
        $erphp_url_front_vip = get_option('erphp_url_front_vip');
    }
?>
    <html lang="zh-CN">
        <head>
            <meta charset="UTF-8" />
            <link rel="stylesheet" href="<?php echo constant("erphpdown");?>static/erphpdown.css" type="text/css" />
            <link rel="stylesheet" href="<?php echo constant("erphpdown");?>static/erphpdown-mizuki.css" type="text/css" />
            <link rel="shortcut icon" href="<?php echo get_option('erphp_url_front_favicon');?>">
            <script src="<?php echo constant("erphpdown"); ?>static/jquery-1.7.min.js"></script>
            <title><?php _e("文件下载等待",'erphpdown')?> - <?php echo get_the_title($pid);?> - <?php bloginfo('name');?></title>
            <style>
            .loading{
                width: 80px;
                height: 40px;
                margin: 0 auto;
                margin-top:20px;
                margin-bottom: 40px;
            }
            .loading span{
                display: inline-block;
                width: 8px;
                height: 100%;
                border-radius: 4px;
                background: lightgreen;
                -webkit-animation: load 1s ease infinite;
            }
            @-webkit-keyframes load{
                0%,100%{
                    height: 40px;
                    background: lightgreen;
                }
                50%{
                    height: 70px;
                    margin: -15px 0;
                    background: lightblue;
                }
            }
            .loading span:nth-child(2){
                -webkit-animation-delay:0.2s;
            }
            .loading span:nth-child(3){
                -webkit-animation-delay:0.4s;
            }
            .loading span:nth-child(4){
                -webkit-animation-delay:0.6s;
            }
            .loading span:nth-child(5){
                -webkit-animation-delay:0.8s;
            }
            </style>
        </head>
        <body class="erphpdown-body">
            <div id="erphpdown-download">
                <div class="loading">
                        <span></span>
                        <span></span>
                        <span></span>
                        <span></span>
                        <span></span>
                </div>
                <div class="msg">
                    <p style="font-size: 15px;"><?php _e("下载即将开始，剩余等待时间...",'erphpdown')?><span id="time" style="color:#ff5f33"><?php echo get_option('erphp_free_wait');?></span>秒</p>
                    <a href="<?php echo $erphp_url_front_vip;?>" target="_blank" class="erphpdown-btn" style="color:green;margin-top:25px;background: lightgreen;"><?php _e("升级VIP，下载不用等待",'erphpdown')?></a>
                </div>
                <?php do_action('erphpdown_download_ad');?>
            </div>
            <script>
                var s = <?php echo get_option('erphp_free_wait');?>;  
                var Timer = document.getElementById("time");
                wppayCountdown();
                erphpTimer = setInterval(function(){ wppayCountdown() },1000);
                function wppayCountdown (){
                    Timer.innerHTML = s;
                    if( s == 0 ){
                        clearInterval(erphpTimer);
                        location.href=window.location.href+'&timekey=<?php echo md5($pid.get_option('erphpdown_downkey'));?>';
                    }else {
                        s--;
                    }
                }
            </script>
        </body>
    </html>
<?php 
    exit;  
}
