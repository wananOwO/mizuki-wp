<?php 
// +----------------------------------------------------------------------
// | ERPHP [ PHP DEVELOP ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.mobantu.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: mobantu <82708210@qq.com>
// +----------------------------------------------------------------------

require_once '../../../wp-load.php';
date_default_timezone_set('Asia/Shanghai');
?>
<html lang="zh-CN">
<head>
	<meta charset="UTF-8" />
	<link rel="stylesheet" href="<?php echo constant("erphpdown"); ?>static/erphpdown.css?v=<?php echo $erphpdown_version;?>" type="text/css" />
	<link rel="stylesheet" href="<?php echo constant("erphpdown"); ?>static/erphpdown-mizuki.css?v=<?php echo $erphpdown_version;?>" type="text/css" />
	<link rel="shortcut icon" href="<?php echo get_option('erphp_url_front_favicon');?>">
	<script type="text/javascript" src="<?php echo ERPHPDOWN_URL;?>/static/jquery-1.7.min.js"></script>
	<script type='text/javascript' id='erphpdown-js-extra'>
	/* <![CDATA[ */
	var _ERPHP = {"ajaxurl":"<?php echo admin_url("admin-ajax.php");?>"};
	var erphpdown_ajax_url = "<?php echo admin_url("admin-ajax.php");?>";
	/* ]]> */
	</script>
	<script type="text/javascript" src="<?php echo ERPHPDOWN_URL;?>/static/erphpdown.js?v=<?php echo $erphpdown_version;?>"></script>
</head>
<style>
::-webkit-scrollbar {width:0;height:6px}
::-webkit-scrollbar-thumb {background-color: #c7c7c7;border-radius:5px;}
body{margin:10px 20px;padding: 0;}
<?php echo get_option('erphp_custom_css');?>
</style>
<body>
	<div id="erphpdown-paybox">
	<?php
	$erphp_ajaxbuy = get_option('erphp_ajaxbuy');
	$erphp_justbuy = get_option('erphp_justbuy');
	$postid=isset($_GET['postid']) && is_numeric($_GET['postid']) ?intval($_GET['postid']) :false;
	$index=isset($_GET['index']) && is_numeric($_GET['index']) ?intval($_GET['index']) : '';
	$user_type=isset($_GET['user_type']) && is_numeric($_GET['user_type']) ?intval($_GET['user_type']) : '';
	$index = esc_sql($index);
	if($postid){
		$postid = esc_sql($postid);
		$erphp_down=get_post_meta($postid, 'erphp_down',TRUE);
		$down_only_pay=get_post_meta($postid, 'down_only_pay',true);

		$down_user_one=get_post_meta($postid, 'down_user_one',true);
		if($down_user_one){
			if(is_user_logged_in()){
				global $current_user;
				$downInfot=$wpdb->get_row("select * from ".$wpdb->icealipay." where ice_user_id=".$current_user->ID ." and ice_post=".$postid." and ice_success=1 order by ice_time desc");
				if($downInfot){
					$down_user_ones=get_post_meta($postid, 'down_user_ones',true);
					if($down_user_ones){
						$down_user_ones_arr = explode(',', str_replace('，', ',', $down_user_ones));
						if(is_array($down_user_ones_arr) && count($down_user_ones_arr) && in_array($current_user->user_login, $down_user_ones_arr)){

						}else{
							echo '<div class="error">'.__("抱歉，限制每人仅可购买一次！",'erphpdown').'</div>';
							exit;
						}
					}else{
						echo '<div class="error">'.__("抱歉，限制每人仅可购买一次！",'erphpdown').'</div>';
						exit;
					}
				}
			}else{
				echo '<div class="error">'.__("抱歉，由于限制每人仅可购买一次，请先登录！",'erphpdown').'</div>';
				exit;
			}
		}

		if($erphp_down == 6){
			if(!function_exists('getErphpActLeft')){
				echo '<div class="error">'.__("请先在后台erphpdown-基础设置里启用激活码发放扩展！",'erphpdown').'</div>';
				exit;
			}
			$ErphpActLeft = getErphpActLeft($postid);
			$price=get_post_meta($postid, 'down_price', true);
			$price = $price / get_option('ice_proportion_alipay');
			if($price){
	?>
				<div class="erphpdown-table">
						<div class="item">
							<label><?php _e('购买内容','erphpdown');?><span>（<?php _e('库存：','erphpdown');?><?php echo $ErphpActLeft;?>）</span></label>
							<div class="tit"><?php echo get_post($postid)->post_title;?></div>
						</div>
						<div class="item">
							<label><?php _e('购买数量','erphpdown');?></label>
							<div class="tit" style="text-align:right">
								<div class="erphp-faka-num">
									<a href="javascript:;" class="erphp-faka-minus">-</a><input type="number" step="1" min="1" max="<?php echo $ErphpActLeft;?>" id="erphp_faka_num" value="1" oninput="intValidator(event)" /><a href="javascript:;" class="erphp-faka-plus">+</a>
								</div><?php _e('合计','erphpdown');?> <b id="erphp_faka_total" data-price="<?php echo sprintf("%.2f",$price);?>"><?php echo sprintf("%.2f",$price);?></b> <?php _e('元','erphpdown');?></div>
						</div>
						<div class="item">
							<label><?php _e('接收邮箱','erphpdown');?></label>
							<div class="tit">
								<input type="email" id="erphp_faka_email" />
							</div>
						</div>
						<div class="item">
							<div style="padding-top:20px;">
							<?php echo '<div class="erphp-justbuy">';?>
								<?php if(get_option('ice_weixin_mchid')){?> 
									<?php if(erphpdown_is_weixin() && get_option('ice_weixin_app')){?>
									<a data-href="<?php echo urlencode(constant("erphpdown")).'payment%2Fweixin.php%3Fice_post%3D'.$postid.'%26redirect_url='.urlencode(add_query_arg('timestamp',time(),get_permalink($postid)));?>" class="pmt-wx erphpdown-jump3 pmt-wx-app" data-prefix="<?php echo 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.get_option('ice_weixin_appid').'&redirect_uri=';?>" data-suffix="&response_type=code&scope=snsapi_base&state=STATE&connect_redirect=1#wechat_redirect" target="_blank"><i class="erphp-iconfont erphp-icon-wxpay"></i> <?php _e('微信支付','erphpdown');?></a>
									<?php }else{?>
									<a data-href="<?php echo constant("erphpdown")."payment/weixin.php?ice_post=".$postid."&redirect_url=".urlencode(add_query_arg('timestamp',time(),get_permalink($postid)));?>" class="pmt-wx erphpdown-jump3" target="_blank"><i class="erphp-iconfont erphp-icon-wxpay"></i> <?php _e('微信支付','erphpdown');?></a>
									<?php }?>
								<?php }?>
								<?php if(get_option('ice_ali_partner') || get_option('ice_ali_app_id')){?> 
									<a data-href="<?php echo constant("erphpdown")."payment/alipay.php?ice_post=".$postid."&redirect_url=".urlencode(add_query_arg('timestamp',time(),get_permalink($postid)));?>" class="pmt-ali erphpdown-jump3" target="_blank"><i class="erphp-iconfont erphp-icon-alipay"></i> <?php _e('支付宝','erphpdown');?></a>
								<?php }?>
								<?php if(get_option('erphpdown_f2fpay_id') && !get_option('erphpdown_f2fpay_alipay')){?> 
									<a data-href="<?php echo constant("erphpdown")."payment/f2fpay.php?ice_post=".$postid."&redirect_url=".urlencode(add_query_arg('timestamp',time(),get_permalink($postid)));?>" class="pmt-ali erphpdown-jump3" target="_blank"><i class="erphp-iconfont erphp-icon-alipay"></i> <?php _e('支付宝','erphpdown');?></a>
								<?php }?>
								<?php if(get_option('erphpdown_payjs_appid')){?> 
									<?php if(!get_option('erphpdown_payjs_wxpay')){?><a data-href="<?php echo constant("erphpdown")."payment/payjs.php?ice_post=".$postid."&redirect_url=".urlencode(add_query_arg('timestamp',time(),get_permalink($postid)));?>" class="pmt-wx erphpdown-jump3" target="_blank"><i class="erphp-iconfont erphp-icon-wxpay"></i> <?php _e('微信支付','erphpdown');?></a><?php }?>  
									<?php if(!get_option('erphpdown_payjs_alipay')){?><a data-href="<?php echo constant("erphpdown")."payment/payjs.php?ice_post=".$postid."&type=alipay&redirect_url=".urlencode(add_query_arg('timestamp',time(),get_permalink($postid)));?>" class="pmt-ali erphpdown-jump3" target="_blank"><i class="erphp-iconfont erphp-icon-alipay"></i> <?php _e('支付宝','erphpdown');?></a><?php }?> 
								<?php }?>
								<?php if(get_option('erphpdown_xhpay_appid31')){?> 
									<a data-href="<?php echo constant("erphpdown")."payment/xhpay3.php?ice_post=".$postid."&type=2"."&redirect_url=".urlencode(add_query_arg('timestamp',time(),get_permalink($postid)));?>" class="pmt-wx erphpdown-jump3" target="_blank"><i class="erphp-iconfont erphp-icon-wxpay"></i> <?php _e('微信支付','erphpdown');?></a>   
								<?php }?>
								<?php if(get_option('erphpdown_xhpay_appid32')){?> 
									<a data-href="<?php echo constant("erphpdown")."payment/xhpay3.php?ice_post=".$postid."&redirect_url=".urlencode(add_query_arg('timestamp',time(),get_permalink($postid)));?>" class="pmt-ali erphpdown-jump3" target="_blank"><i class="erphp-iconfont erphp-icon-alipay"></i> <?php _e('支付宝','erphpdown');?></a>  
								<?php }?>
								<?php if(get_option('erphpdown_paypy_key')){?> 
									<?php if(!get_option('erphpdown_paypy_wxpay')){?><a data-href="<?php echo constant("erphpdown")."payment/paypy.php?ice_post=".$postid."&redirect_url=".urlencode(add_query_arg('timestamp',time(),get_permalink($postid)));?>" class="pmt-wx erphpdown-jump3" target="_blank"><i class="erphp-iconfont erphp-icon-wxpay"></i> <?php _e('微信支付','erphpdown');?></a><?php }?>  
									<?php if(!get_option('erphpdown_paypy_alipay')){?><a data-href="<?php echo constant("erphpdown")."payment/paypy.php?ice_post=".$postid."&type=alipay"."&redirect_url=".urlencode(add_query_arg('timestamp',time(),get_permalink($postid)));?>" class="pmt-ali erphpdown-jump3" target="_blank"><i class="erphp-iconfont erphp-icon-alipay"></i> <?php _e('支付宝','erphpdown');?></a><?php }?>
								<?php }?>
								<?php if(get_option('erphpdown_codepay_appid')){?> 
									<?php if(!get_option('erphpdown_codepay_alipay')){?><a data-href="<?php echo constant("erphpdown")."payment/codepay.php?ice_post=".$postid."&type=1"."&redirect_url=".urlencode(add_query_arg('timestamp',time(),get_permalink($postid)));?>" class="pmt-ali erphpdown-jump3" target="_blank"><i class="erphp-iconfont erphp-icon-alipay"></i> <?php _e('支付宝','erphpdown');?></a><?php }?>
									<?php if(!get_option('erphpdown_codepay_wxpay')){?><a data-href="<?php echo constant("erphpdown")."payment/codepay.php?ice_post=".$postid."&type=3"."&redirect_url=".urlencode(add_query_arg('timestamp',time(),get_permalink($postid)));?>" class="pmt-wx erphpdown-jump3" target="_blank"><i class="erphp-iconfont erphp-icon-wxpay"></i> <?php _e('微信支付','erphpdown');?></a><?php }?>
									<?php if(!get_option('erphpdown_codepay_qqpay')){?><a data-href="<?php echo constant("erphpdown")."payment/codepay.php?ice_post=".$postid."&type=2"."&redirect_url=".urlencode(add_query_arg('timestamp',time(),get_permalink($postid)));?>" class="pmt-qq erphpdown-jump3" target="_blank"><i class="erphp-iconfont erphp-icon-qqpay"></i> <?php _e('QQ钱包','erphpdown');?></a><?php }?>    
								<?php }?>
								<?php if(get_option('erphpdown_epay_id')){?> 
									<?php if(!get_option('erphpdown_epay_wxpay')){?><a data-href="<?php echo constant("erphpdown")."payment/epay.php?ice_post=".$postid."&type=wxpay&redirect_url=".urlencode(add_query_arg('timestamp',time(),get_permalink($postid)));?>" class="pmt-wx erphpdown-jump3" target="_blank"><i class="erphp-iconfont erphp-icon-wxpay"></i> <?php _e('微信支付','erphpdown');?></a><?php }?>  
									<?php if(!get_option('erphpdown_epay_alipay')){?><a data-href="<?php echo constant("erphpdown")."payment/epay.php?ice_post=".$postid."&type=alipay"."&redirect_url=".urlencode(add_query_arg('timestamp',time(),get_permalink($postid)));?>" class="pmt-ali erphpdown-jump3" target="_blank"><i class="erphp-iconfont erphp-icon-alipay"></i> <?php _e('支付宝','erphpdown');?></a><?php }?>
									<?php if(!get_option('erphpdown_epay_qqpay')){?><a data-href="<?php echo constant("erphpdown")."payment/epay.php?ice_post=".$postid."&type=qqpay"."&redirect_url=".urlencode(add_query_arg('timestamp',time(),get_permalink($postid)));?>" class="pmt-qq erphpdown-jump3" target="_blank"><i class="erphp-iconfont erphp-icon-qqpay"></i> <?php _e('QQ钱包','erphpdown');?></a><?php }?>
								<?php }?>
								<?php if(get_option('erphpdown_easepay_id')){?> 
									<?php if(!get_option('erphpdown_easepay_wxpay')){?><a data-href="<?php echo constant("erphpdown")."payment/easepay.php?ice_post=".$postid."&type=wxpay&redirect_url=".urlencode(add_query_arg('timestamp',time(),get_permalink($postid)));?>" class="pmt-wx erphpdown-jump3" target="_blank"><i class="erphp-iconfont erphp-icon-wxpay"></i> <?php _e('微信支付','erphpdown');?></a><?php }?>  
									<?php if(!get_option('erphpdown_easepay_alipay')){?><a data-href="<?php echo constant("erphpdown")."payment/easepay.php?ice_post=".$postid."&type=alipay"."&redirect_url=".urlencode(add_query_arg('timestamp',time(),get_permalink($postid)));?>" class="pmt-ali erphpdown-jump3" target="_blank"><i class="erphp-iconfont erphp-icon-alipay"></i> <?php _e('支付宝','erphpdown');?></a><?php }?>
									<?php if(!get_option('erphpdown_easepay_usdt')){?><a data-href="<?php echo constant("erphpdown")."payment/easepay.php?ice_post=".$postid."&type=usdt&redirect_url=".urlencode(add_query_arg('timestamp',time(),get_permalink($postid)));?>" class="pmt-ut erphpdown-jump3" target="_blank"><i class="erphp-iconfont erphp-icon-ut"></i> <?php _e('USDT','erphpdown');?></a><?php }?>
								<?php }?>
								<?php if(get_option('erphpdown_vpay_key')){?> 
									<?php if(!get_option('erphpdown_vpay_wxpay')){?><a data-href="<?php echo constant("erphpdown")."payment/vpay.php?ice_post=".$postid."&redirect_url=".urlencode(add_query_arg('timestamp',time(),get_permalink($postid)));?>" class="pmt-wx erphpdown-jump3" target="_blank"><i class="erphp-iconfont erphp-icon-wxpay"></i> <?php _e('微信支付','erphpdown');?></a><?php }?>  
									<?php if(!get_option('erphpdown_vpay_alipay')){?><a data-href="<?php echo constant("erphpdown")."payment/vpay.php?ice_post=".$postid."&type=2"."&redirect_url=".urlencode(add_query_arg('timestamp',time(),get_permalink($postid)));?>" class="pmt-ali erphpdown-jump3" target="_blank"><i class="erphp-iconfont erphp-icon-alipay"></i> <?php _e('支付宝','erphpdown');?></a><?php }?>
								<?php }?>
								<?php if(get_option('erphpdown_stripe_pk')){?> 
									<a data-href="<?php echo home_url('?epd_p64='.base64_encode('stripe-'.$postid.'-'.time()))."&redirect_url=".urlencode(add_query_arg('timestamp',time(),get_permalink($postid)));?>" class="pmt-stripe erphpdown-jump3" target="_blank"><i class="erphp-iconfont erphp-icon-credit-card"></i> <?php _e('信用卡','erphpdown');?></a>
								<?php }?>
								<?php if(get_option('erphpdown_ecpay_MerchantID') && plugin_check_ecpay()){?> 
									<a data-href="<?php echo ERPHPDOWN_ECPAY_URL."/ecpay.php?ice_post=".$postid."&redirect_url=".urlencode(add_query_arg('timestamp',time(),get_permalink($postid)));?>" class="pmt-ecpay erphpdown-jump3" target="_blank"><i class="erphp-iconfont erphp-icon-ecpay"></i> <?php _e('新台币','erphpdown');?></a>
								<?php }?>
								<?php if(get_option('erphpdown_newebpay_MerchantID') && plugin_check_newebpay()){?> 
									<a data-href="<?php echo ERPHPDOWN_NEWEBPAY_URL."/newebpay.php?ice_post=".$postid."&redirect_url=".urlencode(add_query_arg('timestamp',time(),get_permalink($postid)));?>" class="pmt-newebpay erphpdown-jump3" target="_blank"><i class="erphp-iconfont erphp-icon-ecpay"></i> <?php _e('新台币','erphpdown');?></a>
								<?php }?>
								<?php if(get_option('erphpdown_usdt_address')){?> 
									<a data-href="<?php echo home_url('?epd_p64='.base64_encode('usdt-'.$postid.'-'.time()))."&redirect_url=".urlencode(add_query_arg('timestamp',time(),get_permalink($postid)));?>" class="pmt-ut erphpdown-jump3" target="_blank"><i class="erphp-iconfont erphp-icon-ut"></i> <?php _e('USDT','erphpdown');?></a>
								<?php }?>
								<?php if(get_option('ice_payapl_api_uid')){?> 
									<a data-href="<?php echo constant("erphpdown")."payment/paypal.php?ice_post=".$postid."&redirect_url=".urlencode(add_query_arg('timestamp',time(),get_permalink($postid)));?>" class="pmt-pp erphpdown-jump3" target="_blank"><i class="erphp-iconfont erphp-icon-paypay"></i> Paypal</a>
								<?php }?>
							<?php echo '</div>';?>
							</div>
							<?php if(!is_user_logged_in()){
								$erphp_url_front_login = wp_login_url();
								if(get_option('erphp_url_front_login')){
									$erphp_url_front_login = get_option('erphp_url_front_login');
								}
							?><div class="login-tips" style="display:none"><?php echo sprintf(__('<a href="%s" target="_blank">登录</a>后购买可保存购买记录','erphpdown'), $erphp_url_front_login); ?></div><?php }elseif(!$down_only_pay){?>
							<a class="ss-button erphpdown-btn do-erphpdown-pay3" data-href="<?php echo constant("erphpdown").'checkout.php?ajax=1&postid='.$postid.'&timestamp='.time();?>" style="border:none;cursor: pointer;"><?php _e('余额支付','erphpdown');?></a>
							<?php }?>
						</div>
					</div>
					<script>
						$(".do-erphpdown-pay3").click(function(){
							var that = $(this);
							that.text("<?php _e('处理中...','erphpdown');?>").attr("disabled","disabled");
							$.ajax({  
					            type: 'GET',  
					            url:  $(this).data("href"),  
					            dataType: 'json',
								data: {
									num:$("#erphp_faka_num").val(),
									email:$("#erphp_faka_email").val()
								},
					            success: function(data){
					            	that.text("<?php _e('余额支付','erphpdown');?>").removeAttr("disabled");  
					                if( data.error ){
					                    if( data.msg ){
					                        layer.msg(data.msg);
					                    }
					                }else{
					                	if(data.jump == '2'){
					                		layer.msg("<?php _e('购买成功','erphpdown');?>");
					                		parent.location.reload();
					                	}else if(data.jump == '1'){
					                		parent.location.href=data.link;
					                	}else{
					                		parent.location.reload();
					                	}
					                }

					            }  

					        });
					        return false;
						});

						$(".erphpdown-jump3").click(function(){
							var cmail = $("#erphp_faka_email").val(),
								cnum = Number($("#erphp_faka_num").val()),
								clink = $(this).data("href");
							if(cnum < 1){
								layer.msg('<?php _e('请输入购买数量','erphpdown');?>');
								return false;
							}else if(cmail == ''){
								layer.msg('<?php _e('请输入邮箱，用于接收卡密','erphpdown');?>');
								return false;
							}else{
								if($(this).hasClass("pmt-wx-app")){
									$(this).attr("href",$(this).data("prefix")+clink+"%26num%3D"+cnum+"%26data%3D"+cmail+$(this).data("suffix"));
								}else{
									$(this).attr("href",clink+"&num="+cnum+"&data="+cmail);
								}
								parent.layer.closeAll();
							}
						});

						$(".erphp-faka-minus").click(function(){
							var cnum = Number($("#erphp_faka_num").val()),
								cprice = $("#erphp_faka_total").data("price");
							if(cnum > 1){
								$("#erphp_faka_num").val(cnum-1);
								$("#erphp_faka_total").text(((cnum-1)*cprice).toFixed(2));
							}
						});
						
						$(".erphp-faka-plus").click(function(){
							var cnum = Number($("#erphp_faka_num").val()),
								cprice = $("#erphp_faka_total").data("price"),
								cmax = Number($("#erphp_faka_num").attr("max"));
							if(cnum < cmax){
								$("#erphp_faka_num").val(cnum+1);
								$("#erphp_faka_total").text(((cnum+1)*cprice).toFixed(2));
							}
						});

						$("#erphp_faka_num").blur(function(){
							var cnum = Number($("#erphp_faka_num").val()),
								cprice = $("#erphp_faka_total").data("price");
							if(cnum > 1){
								if(cnum > <?php echo $ErphpActLeft;?>){
									$("#erphp_faka_num").val(<?php echo $ErphpActLeft;?>);
									$("#erphp_faka_total").text((<?php echo $ErphpActLeft;?>*cprice).toFixed(2));
								}else{
									$("#erphp_faka_total").text((cnum*cprice).toFixed(2));
								}
							}else{
								$("#erphp_faka_total").text(cprice.toFixed(2));
							}
						});

						function intValidator(e){
						    var value = e.target.value;
						    value = value.replace( /\D+/, "");
						    if(value.length > 0){
						        if(value.length > 1 && value[0] == 0){
						            e.target.value = value.substring(1, value.length);
						            return;
						        }
						        //判断不要超过9位
						        if(value.length>9){
						            e.target.value=value.slice(0,9)
						        }else{
						            e.target.value = value;
						        }
						    }else{
						        e.target.value = 0;
						    };
						}
					</script>
	<?php
			}else{
				_e('价格错误','erphpdown');
			}
		}else{
			$days=get_post_meta($postid, 'down_days', true);
			$down_repeat = get_post_meta($postid, 'down_repeat', true);
			$down_only_pay = get_post_meta($postid, 'down_only_pay', true);
			$memberDown=get_post_meta($postid, 'member_down',TRUE);
			$start_down2=get_post_meta($postid, 'start_down2',TRUE);

			$erphp_url_front_login = wp_login_url();
			if(get_option('erphp_url_front_login')){
				$erphp_url_front_login = get_option('erphp_url_front_login');
			}

			$erphp_url_front_recharge = get_bloginfo('wpurl').'/wp-admin/admin.php?page=erphpdown/admin/erphp-add-money-online.php';
			if(get_option('erphp_url_front_recharge')){
				$erphp_url_front_recharge = get_option('erphp_url_front_recharge');
			}

			if(is_user_logged_in() || get_option('erphp_wppay_down') || $start_down2){
				$user_info=wp_get_current_user();
				$userType=getUsreMemberType();

				$categories = get_the_category($postid);
				if ( !empty($categories) ) {
					$userCat=getUsreMemberCat(erphpdown_parent_cid($categories[0]->term_id));
					if(!$userType){
						if($userCat){
							$userType = $userCat;
						}
					}else{
						if($userCat){
							if($userCat > $userType){
								$userType = $userCat;
							}
						}
					}
				}

				$user_id = $user_info->ID;
				$wppay = new EPD(get_the_ID(), $user_id);

				$downInfo = null;
				if($user_id){
					$downInfo=$wpdb->get_row("select * from ".$wpdb->icealipay." where ice_user_id=".$user_info->ID ." and ice_post=".$postid." and ice_success=1 and (ice_index is null or ice_index = '') order by ice_time desc");
				}

				if($downInfo && $days > 0){
					$lastDownDate = date('Y-m-d H:i:s',strtotime('+'.$days.' day',strtotime($downInfo->ice_time)));
					$nowDate = date('Y-m-d H:i:s');
					if(strtotime($nowDate) > strtotime($lastDownDate)){
						$downInfo = null;
					}
				}


				$down_user_ones=get_post_meta($postid, 'down_user_ones',true);
				if($down_user_ones){
					if(is_user_logged_in()){
						global $current_user;
						$down_user_ones_arr = explode(',', str_replace('，', ',', $down_user_ones));
						if(is_array($down_user_ones_arr) && count($down_user_ones_arr) && in_array($current_user->user_login, $down_user_ones_arr)){
							$down_repeat = 1;
						}
					}
				}

				if(($downInfo || $wppay->isWppayPaid() || $wppay->isWppayPaidNew($index)) && !$down_repeat){
					echo '<div class="error">'.__("您已购买过，请关闭此窗口后刷新页面试试！",'erphpdown').'</div>';
					exit;
				}else{

					$life_times_includes_free    = get_option('erphp_life_times_free');
					$year_times_includes_free    = get_option('erphp_year_times_free');
					$quarter_times_includes_free = get_option('erphp_quarter_times_free');
					$month_times_includes_free  = get_option('erphp_month_times_free');
					$day_times_includes_free  = get_option('erphp_day_times_free');

					$erphp_life_times    = get_option('erphp_life_times');
					$erphp_year_times    = get_option('erphp_year_times');
					$erphp_quarter_times = get_option('erphp_quarter_times');
					$erphp_month_times  = get_option('erphp_month_times');
					$erphp_day_times  = get_option('erphp_day_times');

					$erphp_life_discount    = get_option('erphp_life_discount');
					$erphp_year_discount    = get_option('erphp_year_discount');
					$erphp_quarter_discount = get_option('erphp_quarter_discount');
					$erphp_month_discount  = get_option('erphp_month_discount');
					$erphp_day_discount  = get_option('erphp_day_discount');

					if($index){
						$urls = get_post_meta($postid, 'down_urls', true);
						if($urls){
							$cnt = count($urls['index']);
	            			if($cnt){
	            				for($i=0; $i<$cnt;$i++){
	            					if($urls['index'][$i] == $index){
		            					$index_name = $urls['name'][$i];
		            					$price = $urls['price'][$i];
		            					$index_vip = $urls['vip'][$i];
		            					break;
		            				}
	            				}
	            				$price_old = $price;
								$hidden=get_post_meta($postid, 'hidden_content', true);
								if($price){
									$okMoney=erphpGetUserOkMoney();
									$vip=false;
									$memberDown=get_post_meta($postid, 'member_down',TRUE);
									$indexMemberDown = $memberDown;
	            					if($index_vip){
	            						$indexMemberDown = $index_vip;
	            					}

									if( $indexMemberDown==4 || $indexMemberDown==15 || $indexMemberDown==8 || $indexMemberDown==9 || (($indexMemberDown == 10 || $indexMemberDown == 11 || $indexMemberDown == 12) && !$userType) || ($indexMemberDown == 17 && $userType < 8) || ($indexMemberDown == 18 && $userType < 9) || ($indexMemberDown == 19 && $userType != 10))
									{
										echo '<div class="error">'.__("您无权购买此资源",'erphpdown').'</div>';exit;
									}
									if($userType && ($indexMemberDown==2 || $indexMemberDown==13))
									{
										$vip=TRUE;
										$price=$price*0.5;
									}
									if($userType && ($indexMemberDown==5 || $indexMemberDown==14))
									{
										$vip=TRUE;
										$price=$price*0.8;
									}
									if($userType>=9 && $indexMemberDown==11)
									{
										$vip=TRUE;
										$price=$price*0.5;
									}
									if($userType>=9 && $indexMemberDown==12)
									{
										$vip=TRUE;
										$price=$price*0.8;
									}
									if($userType && $indexMemberDown==20)
									{
										if($userType == 6 && $erphp_day_discount){
											$vip=TRUE;
											$price=$price*$erphp_day_discount*0.1;
										}elseif($userType == 7 && $erphp_month_discount){
											$vip=TRUE;
											$price=$price*$erphp_month_discount*0.1;
										}elseif($userType == 8 && $erphp_quarter_discount){
											$vip=TRUE;
											$price=$price*$erphp_quarter_discount*0.1;
										}elseif($userType == 9 && $erphp_year_discount){
											$vip=TRUE;
											$price=$price*$erphp_year_discount*0.1;
										}
									}

									if( ($userType && ($indexMemberDown==3 || $indexMemberDown==4)) || (($indexMemberDown==15 || $indexMemberDown==16) && $userType >= 8) || (($indexMemberDown==6 || $indexMemberDown==8) && $userType >= 9) || (($indexMemberDown==7 || $indexMemberDown==9 || $indexMemberDown==13 || $indexMemberDown==14) && $userType == 10) ){
										if($userType == 6 && $erphp_day_times > 0 && $erphp_day_discount > 0){
											if($day_times_includes_free){
												if( checkDownLogNoVip($user_info->ID,$postid,$erphp_day_times) ){

												}else{
													$vip=TRUE;
													$price=$price*$erphp_day_discount*0.1;
												}
											}else{
												if( checkDownLog($user_info->ID,$postid,$erphp_day_times,1) ){

												}else{
													$vip=TRUE;
													$price=$price*$erphp_day_discount*0.1;
												}
											}
										}elseif($userType == 7 && $erphp_month_times > 0 && $erphp_month_discount > 0){
											if($month_times_includes_free){
												if( checkDownLogNoVip($user_info->ID,$postid,$erphp_month_times) ){

												}else{
													$vip=TRUE;
													$price=$price*$erphp_month_discount*0.1;
												}
											}else{
												if( checkDownLog($user_info->ID,$postid,$erphp_month_times,1) ){

												}else{
													$vip=TRUE;
													$price=$price*$erphp_month_discount*0.1;
												}
											}
										}elseif($userType == 8 && $erphp_quarter_times > 0 && $erphp_quarter_discount > 0){
											if($quarter_times_includes_free){
												if( checkDownLogNoVip($user_info->ID,$postid,$erphp_quarter_times) ){

												}else{
													$vip=TRUE;
													$price=$price*$erphp_quarter_discount*0.1;
												}
											}else{
												if( checkDownLog($user_info->ID,$postid,$erphp_quarter_times,1) ){

												}else{
													$vip=TRUE;
													$price=$price*$erphp_quarter_discount*0.1;
												}
											}
										}elseif($userType == 9 && $erphp_year_times > 0 && $erphp_year_discount > 0){
											if($year_times_includes_free){
												if( checkDownLogNoVip($user_info->ID,$postid,$erphp_year_times) ){

												}else{
													$vip=TRUE;
													$price=$price*$erphp_year_discount*0.1;
												}
											}else{
												if( checkDownLog($user_info->ID,$postid,$erphp_year_times,1) ){

												}else{
													$vip=TRUE;
													$price=$price*$erphp_year_discount*0.1;
												}
											}
										}elseif($userType == 10 && $erphp_life_times > 0 && $erphp_life_discount > 0){
											if($life_times_includes_free){
												if( checkDownLogNoVip($user_info->ID,$postid,$erphp_life_times) ){

												}else{
													$vip=TRUE;
													$price=$price*$erphp_life_discount*0.1;
												}
											}else{
												if( checkDownLog($user_info->ID,$postid,$erphp_life_times,1) ){

												}else{
													$vip=TRUE;
													$price=$price*$erphp_life_discount*0.1;
												}
											}
										}
									}
									?>

									<div class="erphpdown-table">
										<div class="item">
											<label><?php _e('购买内容','erphpdown');?></label>
											<div class="tit"><?php echo get_post($postid)->post_title;?> - <?php echo $index_name;?></div>
										</div>
										<div class="item item-price">
											<label><?php _e('购买结算','erphpdown');?></label>
											<div class="tit" style="text-align:right"><span><?php _e('小计','erphpdown');?></span><t class="login" data-price="<?php echo sprintf("%.2f",$price);?>" data-proportion="<?php echo get_option('ice_proportion_alipay');?>"><?php echo sprintf("%.2f",$price);?><?php echo  $vip==TRUE?' <del>('.__('原价','erphpdown').' '.sprintf("%.2f",$price_old).')</del>' :'';?></t> <?php echo get_option('ice_name_alipay');?></div>
										</div>
										<?php if(is_user_logged_in() && !$down_only_pay){?>
										<div class="item" style="font-size:13px;color:#999;">
											<?php _e('账户余额：','erphpdown');?><t id="erphpdown_left"><?php echo sprintf("%.2f",$okMoney);?></t> <?php echo get_option('ice_name_alipay');?>
										</div>
										<?php }?>
										<div class="item">
											<div style="padding-top:20px;">
											<?php if(sprintf("%.2f",$okMoney) >= sprintf("%.2f",$price) && !$down_only_pay) {?>
												<div style="margin-top: 30px;">
												<?php if($erphp_ajaxbuy){?>
												<a class="ss-button erphpdown-btn do-erphpdown-pay" data-href="<?php echo constant("erphpdown").'checkout.php?ajax=1&postid='.$postid.'&timestamp='.time();?>&index=<?php echo $index;?>" style="border:none;cursor: pointer;"><?php _e('余额支付','erphpdown');?></a>
												<?php }else{?>
												<a class="ss-button erphpdown-btn" href="<?php echo constant("erphpdown").'checkout.php?postid='.$postid.'&timestamp='.time(); ?>&index=<?php echo $index;?>" target="_blank"><?php _e('余额支付','erphpdown');?></a>
												<?php }?>
												</div>
											<?php }else{

												if($erphp_justbuy){
													echo '<div class="erphp-justbuy">';
											?>
												<?php if(get_option('ice_weixin_mchid')){?> 
													<?php if(erphpdown_is_weixin() && get_option('ice_weixin_app')){?>
													<a href="<?php echo 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.get_option('ice_weixin_appid').'&redirect_uri='.urlencode(constant("erphpdown")).'payment%2Fweixin.php%3Fice_post%3D'.$postid.'%26index='.$index.'%26redirect_url='.urlencode(add_query_arg('timestamp',time(),get_permalink($postid))).'&response_type=code&scope=snsapi_base&state=STATE&connect_redirect=1#wechat_redirect';?>" class="pmt-wx erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-wxpay"></i> <?php _e('微信支付','erphpdown');?></a>
													<?php }else{?>
													<a href="<?php echo constant("erphpdown")."payment/weixin.php?ice_post=".$postid."&index=".$index."&redirect_url=".urlencode(add_query_arg('timestamp',time(),get_permalink($postid)));?>" class="pmt-wx erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-wxpay"></i> <?php _e('微信支付','erphpdown');?></a>
													<?php }?>
												<?php }?>
												<?php if(get_option('ice_ali_partner') || get_option('ice_ali_app_id')){?> 
													<a href="<?php echo constant("erphpdown")."payment/alipay.php?ice_post=".$postid."&index=".$index."&redirect_url=".urlencode(add_query_arg('timestamp',time(),get_permalink($postid)));?>" class="pmt-ali erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-alipay"></i> <?php _e('支付宝','erphpdown');?></a>
												<?php }?>
												<?php if(get_option('erphpdown_f2fpay_id') && !get_option('erphpdown_f2fpay_alipay')){?> 
													<a href="<?php echo constant("erphpdown")."payment/f2fpay.php?ice_post=".$postid."&index=".$index."&redirect_url=".urlencode(add_query_arg('timestamp',time(),get_permalink($postid)));?>" class="pmt-ali erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-alipay"></i> <?php _e('支付宝','erphpdown');?></a>
												<?php }?>
												<?php if(get_option('erphpdown_payjs_appid')){?> 
													<?php if(!get_option('erphpdown_payjs_wxpay')){?><a href="<?php echo constant("erphpdown")."payment/payjs.php?ice_post=".$postid."&index=".$index."&redirect_url=".urlencode(add_query_arg('timestamp',time(),get_permalink($postid)));?>" class="pmt-wx erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-wxpay"></i> <?php _e('微信支付','erphpdown');?></a><?php }?>
													<?php if(!get_option('erphpdown_payjs_alipay')){?><a href="<?php echo constant("erphpdown")."payment/payjs.php?ice_post=".$postid."&index=".$index."&type=alipay&redirect_url=".urlencode(add_query_arg('timestamp',time(),get_permalink($postid)));?>" class="pmt-ali erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-alipay"></i> <?php _e('支付宝','erphpdown');?></a><?php }?>   
												<?php }?>
												<?php if(get_option('erphpdown_xhpay_appid31')){?> 
													<a href="<?php echo constant("erphpdown")."payment/xhpay3.php?ice_post=".$postid."&index=".$index."&type=2"."&redirect_url=".urlencode(add_query_arg('timestamp',time(),get_permalink($postid)));?>" class="pmt-wx erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-wxpay"></i> <?php _e('微信支付','erphpdown');?></a>   
												<?php }?>
												<?php if(get_option('erphpdown_xhpay_appid32')){?> 
													<a href="<?php echo constant("erphpdown")."payment/xhpay3.php?ice_post=".$postid."&index=".$index."&redirect_url=".urlencode(add_query_arg('timestamp',time(),get_permalink($postid)));?>" class="pmt-ali erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-alipay"></i> <?php _e('支付宝','erphpdown');?></a>  
												<?php }?>
												<?php if(get_option('erphpdown_paypy_key')){?> 
													<?php if(!get_option('erphpdown_paypy_wxpay')){?><a href="<?php echo constant("erphpdown")."payment/paypy.php?ice_post=".$postid."&index=".$index."&redirect_url=".urlencode(add_query_arg('timestamp',time(),get_permalink($postid)));?>" class="pmt-wx erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-wxpay"></i> <?php _e('微信支付','erphpdown');?></a><?php }?>  
													<?php if(!get_option('erphpdown_paypy_alipay')){?><a href="<?php echo constant("erphpdown")."payment/paypy.php?ice_post=".$postid."&index=".$index."&type=alipay"."&redirect_url=".urlencode(add_query_arg('timestamp',time(),get_permalink($postid)));?>" class="pmt-ali erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-alipay"></i> <?php _e('支付宝','erphpdown');?></a><?php }?>
												<?php }?>
												<?php if(get_option('erphpdown_codepay_appid')){?> 
													<?php if(!get_option('erphpdown_codepay_alipay')){?><a href="<?php echo constant("erphpdown")."payment/codepay.php?ice_post=".$postid."&index=".$index."&type=1"."&redirect_url=".urlencode(add_query_arg('timestamp',time(),get_permalink($postid)));?>" class="pmt-ali erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-alipay"></i> <?php _e('支付宝','erphpdown');?></a><?php }?>
													<?php if(!get_option('erphpdown_codepay_wxpay')){?><a href="<?php echo constant("erphpdown")."payment/codepay.php?ice_post=".$postid."&index=".$index."&type=3"."&redirect_url=".urlencode(add_query_arg('timestamp',time(),get_permalink($postid)));?>" class="pmt-wx erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-wxpay"></i> <?php _e('微信支付','erphpdown');?></a><?php }?>
													<?php if(!get_option('erphpdown_codepay_qqpay')){?><a href="<?php echo constant("erphpdown")."payment/codepay.php?ice_post=".$postid."&index=".$index."&type=2"."&redirect_url=".urlencode(add_query_arg('timestamp',time(),get_permalink($postid)));?>" class="pmt-qq erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-qqpay"></i> <?php _e('QQ钱包','erphpdown');?></a><?php }?>    
												<?php }?>
												<?php if(get_option('erphpdown_epay_id')){?> 
													<?php if(!get_option('erphpdown_epay_wxpay')){?><a href="<?php echo constant("erphpdown")."payment/epay.php?ice_post=".$postid."&index=".$index."&type=wxpay&redirect_url=".urlencode(add_query_arg('timestamp',time(),get_permalink($postid)));?>" class="pmt-wx erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-wxpay"></i> <?php _e('微信支付','erphpdown');?></a><?php }?>  
													<?php if(!get_option('erphpdown_epay_alipay')){?><a href="<?php echo constant("erphpdown")."payment/epay.php?ice_post=".$postid."&index=".$index."&type=alipay"."&redirect_url=".urlencode(add_query_arg('timestamp',time(),get_permalink($postid)));?>" class="pmt-ali erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-alipay"></i> <?php _e('支付宝','erphpdown');?></a><?php }?>
													<?php if(!get_option('erphpdown_epay_qqpay')){?><a href="<?php echo constant("erphpdown")."payment/epay.php?ice_post=".$postid."&index=".$index."&type=qqpay"."&redirect_url=".urlencode(add_query_arg('timestamp',time(),get_permalink($postid)));?>" class="pmt-qq erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-qqpay"></i> <?php _e('QQ钱包','erphpdown');?></a><?php }?>
												<?php }?>
												<?php if(get_option('erphpdown_easepay_id')){?> 
													<?php if(!get_option('erphpdown_easepay_wxpay')){?><a href="<?php echo constant("erphpdown")."payment/easepay.php?ice_post=".$postid."&index=".$index."&type=wxpay&redirect_url=".urlencode(add_query_arg('timestamp',time(),get_permalink($postid)));?>" class="pmt-wx erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-wxpay"></i> <?php _e('微信支付','erphpdown');?></a><?php }?>  
													<?php if(!get_option('erphpdown_easepay_alipay')){?><a href="<?php echo constant("erphpdown")."payment/easepay.php?ice_post=".$postid."&index=".$index."&type=alipay"."&redirect_url=".urlencode(add_query_arg('timestamp',time(),get_permalink($postid)));?>" class="pmt-ali erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-alipay"></i> <?php _e('支付宝','erphpdown');?></a><?php }?>
													<?php if(!get_option('erphpdown_easepay_usdt')){?><a href="<?php echo constant("erphpdown")."payment/easepay.php?ice_post=".$postid."&index=".$index."&type=usdt&redirect_url=".urlencode(add_query_arg('timestamp',time(),get_permalink($postid)));?>" class="pmt-ut erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-usdt"></i> <?php _e('USDT','erphpdown');?></a><?php }?>  
												<?php }?>
												<?php if(get_option('erphpdown_vpay_key')){?> 
													<?php if(!get_option('erphpdown_vpay_wxpay')){?><a href="<?php echo constant("erphpdown")."payment/vpay.php?ice_post=".$postid."&index=".$index."&redirect_url=".urlencode(add_query_arg('timestamp',time(),get_permalink($postid)));?>" class="pmt-wx erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-wxpay"></i> <?php _e('微信支付','erphpdown');?></a><?php }?>  
													<?php if(!get_option('erphpdown_vpay_alipay')){?><a href="<?php echo constant("erphpdown")."payment/vpay.php?ice_post=".$postid."&index=".$index."&type=2"."&redirect_url=".urlencode(add_query_arg('timestamp',time(),get_permalink($postid)));?>" class="pmt-ali erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-alipay"></i> <?php _e('支付宝','erphpdown');?></a><?php }?>
												<?php }?>
												<?php if(get_option('erphpdown_stripe_pk')){?> 
													<a href="<?php echo home_url('?epd_p64='.base64_encode('stripe-'.$postid.'-'.time()))."&index=".$index."&redirect_url=".urlencode(add_query_arg('timestamp',time(),get_permalink($postid)));?>" class="pmt-stripe erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-credit-card"></i> <?php _e('信用卡','erphpdown');?></a>
												<?php }?>
												<?php if(get_option('erphpdown_ecpay_MerchantID') && plugin_check_ecpay()){?> 
													<a href="<?php echo ERPHPDOWN_ECPAY_URL."/ecpay.php?ice_post=".$postid."&index=".$index."&redirect_url=".urlencode(add_query_arg('timestamp',time(),get_permalink($postid)));?>" class="pmt-ecpay erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-ecpay"></i> <?php _e('新台币','erphpdown');?></a>
												<?php }?>
												<?php if(get_option('erphpdown_newebpay_MerchantID') && plugin_check_newebpay()){?> 
													<a href="<?php echo ERPHPDOWN_NEWEBPAY_URL."/newebpay.php?ice_post=".$postid."&index=".$index."&redirect_url=".urlencode(add_query_arg('timestamp',time(),get_permalink($postid)));?>" class="pmt-newebpay erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-ecpay"></i> <?php _e('新台币','erphpdown');?></a>
												<?php }?>
												<?php if(get_option('erphpdown_usdt_address')){?> 
													<a href="<?php echo home_url('?epd_p64='.base64_encode('usdt-'.$postid.'-'.time()))."&index=".$index."&redirect_url=".urlencode(add_query_arg('timestamp',time(),get_permalink($postid)));?>" class="pmt-ut erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-ut"></i> <?php _e('USDT','erphpdown');?></a>
												<?php }?>
												<?php if(get_option('ice_payapl_api_uid')){?> 
													<a href="<?php echo constant("erphpdown")."payment/paypal.php?ice_post=".$postid."&index=".$index."&redirect_url=".urlencode(add_query_arg('timestamp',time(),get_permalink($postid)));?>" class="pmt-pp erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-paypay"></i> Paypal</a>
												<?php }?>
											<?php	
													echo '</div>';
												}
												if(is_user_logged_in() && !$down_only_pay){
													echo "<div class='erphp-creditbuy'><a target=_blank class='erphpdown-btn erphpdown-jump2 erphpdown-btn-show' href='".$erphp_url_front_recharge."'>".__('余额支付','erphpdown')."</a></div>";

													if($erphp_ajaxbuy){?>
													<a class="ss-button erphpdown-btn do-erphpdown-pay erphpdown-btn-hide" data-href="<?php echo constant("erphpdown").'checkout.php?ajax=1&postid='.$postid.'&timestamp='.time();?>&index=<?php echo $index;?>" style="border:none;cursor: pointer;display: none;"><?php _e('余额支付','erphpdown');?></a>
													<?php }else{?>
													<a class="ss-button erphpdown-btn erphpdown-btn-hide" href="<?php echo constant("erphpdown").'checkout.php?postid='.$postid.'&timestamp='.time(); ?>&index=<?php echo $index;?>" target="_blank" style="display: none;"><?php _e('余额支付','erphpdown');?></a>
													<?php }
												}
											}?>
											</div>
											<?php if(!is_user_logged_in()){?>
											<div class="login-tips" style="display:none"><?php echo sprintf(__('<a href="%s" target="_blank">登录</a>后购买可保存购买记录','erphpdown'), $erphp_url_front_login); ?></div>
											<?php }?>
										</div>
									</div>
									<?php
								}else{
									_e('价格错误','erphpdown');
								}
	            			}
	            		}
					}else{
						$price=get_post_meta($postid, 'down_price', true);
						$start_down2 = get_post_meta($postid, 'start_down2',TRUE);
						if($start_down2){
							$price = $price*get_option('ice_proportion_alipay');
						}
						$price_old = $price;
						$hidden=get_post_meta($postid, 'hidden_content', true);
						if($price){
							$okMoney=erphpGetUserOkMoney();
							$vip=false;
							$memberDown=get_post_meta($postid, 'member_down',TRUE);

							if( $memberDown==4 || $memberDown==15 || $memberDown==8 || $memberDown==9 || (($memberDown == 10 || $memberDown == 11 || $memberDown == 12) && !$userType) || ($memberDown == 17 && $userType < 8) || ($memberDown == 18 && $userType < 9) || ($memberDown == 19 && $userType != 10))
							{
								echo '<div class="error">'.__("您无权购买此资源",'erphpdown').'</div>';exit;
							}
							if($userType && ($memberDown==2 || $memberDown==13))
							{
								$vip=TRUE;
								$price=$price*0.5;
							}
							if($userType && ($memberDown==5 || $memberDown==14))
							{
								$vip=TRUE;
								$price=$price*0.8;
							}
							if($userType>=9 && $memberDown==11)
							{
								$vip=TRUE;
								$price=$price*0.5;
							}
							if($userType>=9 && $memberDown==12)
							{
								$vip=TRUE;
								$price=$price*0.8;
							}
							if($userType && $memberDown==20)
							{
								if($userType == 6 && $erphp_day_discount){
									$vip=TRUE;
									$price=$price*$erphp_day_discount*0.1;
								}elseif($userType == 7 && $erphp_month_discount){
									$vip=TRUE;
									$price=$price*$erphp_month_discount*0.1;
								}elseif($userType == 8 && $erphp_quarter_discount){
									$vip=TRUE;
									$price=$price*$erphp_quarter_discount*0.1;
								}elseif($userType == 9 && $erphp_year_discount){
									$vip=TRUE;
									$price=$price*$erphp_year_discount*0.1;
								}
							}

							if( ($userType && ($memberDown==3 || $memberDown==4)) || (($memberDown==15 || $memberDown==16) && $userType >= 8) || (($memberDown==6 || $memberDown==8) && $userType >= 9) || (($memberDown==7 || $memberDown==9 || $memberDown==13 || $memberDown==14) && $userType == 10) ){
								if($userType == 6 && $erphp_day_times > 0 && $erphp_day_discount > 0){
									if($day_times_includes_free){
										if( checkDownLogNoVip($user_info->ID,$postid,$erphp_day_times) ){

										}else{
											$vip=TRUE;
											$price=$price*$erphp_day_discount*0.1;
										}
									}else{
										if( checkDownLog($user_info->ID,$postid,$erphp_day_times,1) ){

										}else{
											$vip=TRUE;
											$price=$price*$erphp_day_discount*0.1;
										}
									}
								}elseif($userType == 7 && $erphp_month_times > 0 && $erphp_month_discount > 0){
									if($month_times_includes_free){
										if( checkDownLogNoVip($user_info->ID,$postid,$erphp_month_times) ){

										}else{
											$vip=TRUE;
											$price=$price*$erphp_month_discount*0.1;
										}
									}else{
										if( checkDownLog($user_info->ID,$postid,$erphp_month_times,1) ){

										}else{
											$vip=TRUE;
											$price=$price*$erphp_month_discount*0.1;
										}
									}
								}elseif($userType == 8 && $erphp_quarter_times > 0 && $erphp_quarter_discount > 0){
									if($quarter_times_includes_free){
										if( checkDownLogNoVip($user_info->ID,$postid,$erphp_quarter_times) ){

										}else{
											$vip=TRUE;
											$price=$price*$erphp_quarter_discount*0.1;
										}
									}else{
										if( checkDownLog($user_info->ID,$postid,$erphp_quarter_times,1) ){

										}else{
											$vip=TRUE;
											$price=$price*$erphp_quarter_discount*0.1;
										}
									}
								}elseif($userType == 9 && $erphp_year_times > 0 && $erphp_year_discount > 0){
									if($year_times_includes_free){
										if( checkDownLogNoVip($user_info->ID,$postid,$erphp_year_times) ){

										}else{
											$vip=TRUE;
											$price=$price*$erphp_year_discount*0.1;
										}
									}else{
										if( checkDownLog($user_info->ID,$postid,$erphp_year_times,1) ){

										}else{
											$vip=TRUE;
											$price=$price*$erphp_year_discount*0.1;
										}
									}
								}elseif($userType == 10 && $erphp_life_times > 0 && $erphp_life_discount > 0){
									if($life_times_includes_free){
										if( checkDownLogNoVip($user_info->ID,$postid,$erphp_life_times) ){

										}else{
											$vip=TRUE;
											$price=$price*$erphp_life_discount*0.1;
										}
									}else{
										if( checkDownLog($user_info->ID,$postid,$erphp_life_times,1) ){

										}else{
											$vip=TRUE;
											$price=$price*$erphp_life_discount*0.1;
										}
									}
								}
							}
							?>

							<div class="erphpdown-table">
								<div class="item">
									<label><?php _e('购买内容','erphpdown');?></label>
									<div class="tit"><?php echo get_post($postid)->post_title;?></div>
								</div>
								<div class="item item-price">
									<label><?php _e('购买结算','erphpdown');?></label>
									<div class="tit" style="text-align:right"><span><?php _e('小计','erphpdown');?></span><t class="login" data-price="<?php echo sprintf("%.2f",$price);?>" data-proportion="<?php echo get_option('ice_proportion_alipay');?>"><?php echo sprintf("%.2f",$price);?><?php echo $vip==TRUE?' <del>('.__('原价','erphpdown').' '.sprintf("%.2f",$price_old).')</del>' :'';?></t> <?php echo get_option('ice_name_alipay');?></div>
								</div>
								<?php if(is_user_logged_in() && !$down_only_pay){?>
								<div class="item" style="font-size:13px;color:#999;">
									<?php _e('账户余额：','erphpdown');?><t id="erphpdown_left"><?php echo sprintf("%.2f",$okMoney);?></t> <?php echo get_option('ice_name_alipay');?>
								</div>
								<?php }?>
								<div class="item">
									<div style="padding-top:20px;">
									<?php if(sprintf("%.2f",$okMoney) >= sprintf("%.2f",$price) && !$down_only_pay) {?>
										<div style="margin-top: 30px;">
										<?php if($erphp_ajaxbuy){?>
										<a class="ss-button erphpdown-btn do-erphpdown-pay" data-href="<?php echo constant("erphpdown").'checkout.php?ajax=1&postid='.$postid.'&timestamp='.time();?>" style="border:none;cursor: pointer;"><?php _e('余额支付','erphpdown');?></a>
										<?php }else{?>
										<a class="ss-button erphpdown-btn" href="<?php echo constant("erphpdown").'checkout.php?postid='.$postid.'&timestamp='.time(); ?>"
											target="_blank"><?php _e('余额支付','erphpdown');?></a>
										<?php }?>
										</div>
									<?php }else{

										if($erphp_justbuy){
											echo '<div class="erphp-justbuy">';
									?>
										<?php if(get_option('ice_weixin_mchid')){?> 
											<?php if(erphpdown_is_weixin() && get_option('ice_weixin_app')){?>
											<a href="<?php echo 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.get_option('ice_weixin_appid').'&redirect_uri='.urlencode(constant("erphpdown")).'payment%2Fweixin.php%3Fice_post%3D'.$postid.'%26redirect_url='.urlencode(add_query_arg('timestamp',time(),get_permalink($postid))).'&response_type=code&scope=snsapi_base&state=STATE&connect_redirect=1#wechat_redirect';?>" class="pmt-wx erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-wxpay"></i> <?php _e('微信支付','erphpdown');?></a>
											<?php }else{?>
											<a href="<?php echo constant("erphpdown")."payment/weixin.php?ice_post=".$postid."&redirect_url=".urlencode(add_query_arg('timestamp',time(),get_permalink($postid)));?>" class="pmt-wx erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-wxpay"></i> <?php _e('微信支付','erphpdown');?></a>
											<?php }?>
										<?php }?>
										<?php if(get_option('ice_ali_partner') || get_option('ice_ali_app_id')){?> 
											<a href="<?php echo constant("erphpdown")."payment/alipay.php?ice_post=".$postid."&redirect_url=".urlencode(add_query_arg('timestamp',time(),get_permalink($postid)));?>" class="pmt-ali erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-alipay"></i> <?php _e('支付宝','erphpdown');?></a>
										<?php }?>
										<?php if(get_option('erphpdown_f2fpay_id') && !get_option('erphpdown_f2fpay_alipay')){?> 
											<a href="<?php echo constant("erphpdown")."payment/f2fpay.php?ice_post=".$postid."&redirect_url=".urlencode(add_query_arg('timestamp',time(),get_permalink($postid)));?>" class="pmt-ali erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-alipay"></i> <?php _e('支付宝','erphpdown');?></a>
										<?php }?>
										<?php if(get_option('erphpdown_payjs_appid')){?> 
											<?php if(!get_option('erphpdown_payjs_wxpay')){?><a href="<?php echo constant("erphpdown")."payment/payjs.php?ice_post=".$postid."&redirect_url=".urlencode(add_query_arg('timestamp',time(),get_permalink($postid)));?>" class="pmt-wx erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-wxpay"></i> <?php _e('微信支付','erphpdown');?></a><?php }?>  
											<?php if(!get_option('erphpdown_payjs_alipay')){?><a href="<?php echo constant("erphpdown")."payment/payjs.php?ice_post=".$postid."&type=alipay&redirect_url=".urlencode(add_query_arg('timestamp',time(),get_permalink($postid)));?>" class="pmt-ali erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-alipay"></i> <?php _e('支付宝','erphpdown');?></a><?php }?> 
										<?php }?>
										<?php if(get_option('erphpdown_xhpay_appid31')){?> 
											<a href="<?php echo constant("erphpdown")."payment/xhpay3.php?ice_post=".$postid."&type=2"."&redirect_url=".urlencode(add_query_arg('timestamp',time(),get_permalink($postid)));?>" class="pmt-wx erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-wxpay"></i> <?php _e('微信支付','erphpdown');?></a>   
										<?php }?>
										<?php if(get_option('erphpdown_xhpay_appid32')){?> 
											<a href="<?php echo constant("erphpdown")."payment/xhpay3.php?ice_post=".$postid."&redirect_url=".urlencode(add_query_arg('timestamp',time(),get_permalink($postid)));?>" class="pmt-ali erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-alipay"></i> <?php _e('支付宝','erphpdown');?></a>  
										<?php }?>
										<?php if(get_option('erphpdown_paypy_key')){?> 
											<?php if(!get_option('erphpdown_paypy_wxpay')){?><a href="<?php echo constant("erphpdown")."payment/paypy.php?ice_post=".$postid."&redirect_url=".urlencode(add_query_arg('timestamp',time(),get_permalink($postid)));?>" class="pmt-wx erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-wxpay"></i> <?php _e('微信支付','erphpdown');?></a><?php }?>  
											<?php if(!get_option('erphpdown_paypy_alipay')){?><a href="<?php echo constant("erphpdown")."payment/paypy.php?ice_post=".$postid."&type=alipay"."&redirect_url=".urlencode(add_query_arg('timestamp',time(),get_permalink($postid)));?>" class="pmt-ali erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-alipay"></i> <?php _e('支付宝','erphpdown');?></a><?php }?>
										<?php }?>
										<?php if(get_option('erphpdown_codepay_appid')){?> 
											<?php if(!get_option('erphpdown_codepay_alipay')){?><a href="<?php echo constant("erphpdown")."payment/codepay.php?ice_post=".$postid."&type=1"."&redirect_url=".urlencode(add_query_arg('timestamp',time(),get_permalink($postid)));?>" class="pmt-ali erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-alipay"></i> <?php _e('支付宝','erphpdown');?></a><?php }?>
											<?php if(!get_option('erphpdown_codepay_wxpay')){?><a href="<?php echo constant("erphpdown")."payment/codepay.php?ice_post=".$postid."&type=3"."&redirect_url=".urlencode(add_query_arg('timestamp',time(),get_permalink($postid)));?>" class="pmt-wx erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-wxpay"></i> <?php _e('微信支付','erphpdown');?></a><?php }?>
											<?php if(!get_option('erphpdown_codepay_qqpay')){?><a href="<?php echo constant("erphpdown")."payment/codepay.php?ice_post=".$postid."&type=2"."&redirect_url=".urlencode(add_query_arg('timestamp',time(),get_permalink($postid)));?>" class="pmt-qq erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-qqpay"></i> <?php _e('QQ钱包','erphpdown');?></a><?php }?>    
										<?php }?>
										<?php if(get_option('erphpdown_epay_id')){?> 
											<?php if(!get_option('erphpdown_epay_wxpay')){?><a href="<?php echo constant("erphpdown")."payment/epay.php?ice_post=".$postid."&type=wxpay&redirect_url=".urlencode(add_query_arg('timestamp',time(),get_permalink($postid)));?>" class="pmt-wx erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-wxpay"></i> <?php _e('微信支付','erphpdown');?></a><?php }?>
											<?php if(!get_option('erphpdown_epay_alipay')){?><a href="<?php echo constant("erphpdown")."payment/epay.php?ice_post=".$postid."&type=alipay"."&redirect_url=".urlencode(add_query_arg('timestamp',time(),get_permalink($postid)));?>" class="pmt-ali erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-alipay"></i> <?php _e('支付宝','erphpdown');?></a><?php }?>
											<?php if(!get_option('erphpdown_epay_qqpay')){?><a href="<?php echo constant("erphpdown")."payment/epay.php?ice_post=".$postid."&type=qqpay"."&redirect_url=".urlencode(add_query_arg('timestamp',time(),get_permalink($postid)));?>" class="pmt-qq erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-qqpay"></i> <?php _e('QQ钱包','erphpdown');?></a><?php }?>
										<?php }?>
										<?php if(get_option('erphpdown_easepay_id')){?> 
											<?php if(!get_option('erphpdown_easepay_wxpay')){?><a href="<?php echo constant("erphpdown")."payment/easepay.php?ice_post=".$postid."&type=wxpay&redirect_url=".urlencode(add_query_arg('timestamp',time(),get_permalink($postid)));?>" class="pmt-wx erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-wxpay"></i> <?php _e('微信支付','erphpdown');?></a><?php }?>
											<?php if(!get_option('erphpdown_easepay_alipay')){?><a href="<?php echo constant("erphpdown")."payment/easepay.php?ice_post=".$postid."&type=alipay"."&redirect_url=".urlencode(add_query_arg('timestamp',time(),get_permalink($postid)));?>" class="pmt-ali erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-alipay"></i> <?php _e('支付宝','erphpdown');?></a><?php }?>
											<?php if(!get_option('erphpdown_easepay_usdt')){?><a href="<?php echo constant("erphpdown")."payment/easepay.php?ice_post=".$postid."&type=usdt&redirect_url=".urlencode(add_query_arg('timestamp',time(),get_permalink($postid)));?>" class="pmt-ut erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-usdt"></i> <?php _e('USDT','erphpdown');?></a><?php }?>
										<?php }?>
										<?php if(get_option('erphpdown_vpay_key')){?> 
											<?php if(!get_option('erphpdown_vpay_wxpay')){?><a href="<?php echo constant("erphpdown")."payment/vpay.php?ice_post=".$postid."&redirect_url=".urlencode(add_query_arg('timestamp',time(),get_permalink($postid)));?>" class="pmt-wx erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-wxpay"></i> <?php _e('微信支付','erphpdown');?></a><?php }?>  
											<?php if(!get_option('erphpdown_vpay_alipay')){?><a href="<?php echo constant("erphpdown")."payment/vpay.php?ice_post=".$postid."&type=2"."&redirect_url=".urlencode(add_query_arg('timestamp',time(),get_permalink($postid)));?>" class="pmt-ali erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-alipay"></i> <?php _e('支付宝','erphpdown');?></a><?php }?>
										<?php }?>
										<?php if(get_option('erphpdown_stripe_pk')){?> 
											<a href="<?php echo home_url('?epd_p64='.base64_encode('stripe-'.$postid.'-'.time()))."&redirect_url=".urlencode(add_query_arg('timestamp',time(),get_permalink($postid)));?>" class="pmt-stripe erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-credit-card"></i> <?php _e('信用卡','erphpdown');?></a>
										<?php }?>
										<?php if(get_option('erphpdown_ecpay_MerchantID') && plugin_check_ecpay()){?> 
											<a href="<?php echo ERPHPDOWN_ECPAY_URL."/ecpay.php?ice_post=".$postid."&redirect_url=".urlencode(add_query_arg('timestamp',time(),get_permalink($postid)));?>" class="pmt-ecpay erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-ecpay"></i> <?php _e('新台币','erphpdown');?></a>
										<?php }?>
										<?php if(get_option('erphpdown_newebpay_MerchantID') && plugin_check_newebpay()){?> 
											<a href="<?php echo ERPHPDOWN_NEWEBPAY_URL."/newebpay.php?ice_post=".$postid."&redirect_url=".urlencode(add_query_arg('timestamp',time(),get_permalink($postid)));?>" class="pmt-newebpay erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-ecpay"></i> <?php _e('新台币','erphpdown');?></a>
										<?php }?>
										<?php if(get_option('erphpdown_usdt_address')){?> 
											<a href="<?php echo home_url('?epd_p64='.base64_encode('usdt-'.$postid.'-'.time()))."&redirect_url=".urlencode(add_query_arg('timestamp',time(),get_permalink($postid)));?>" class="pmt-ut erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-ut"></i> <?php _e('USDT','erphpdown');?></a>
										<?php }?>
										<?php if(get_option('ice_payapl_api_uid')){?> 
											<a href="<?php echo constant("erphpdown")."payment/paypal.php?ice_post=".$postid."&redirect_url=".urlencode(add_query_arg('timestamp',time(),get_permalink($postid)));?>" class="pmt-pp erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-paypay"></i> Paypal</a>
										<?php }?>
									<?php	
											echo '</div>';
										}
										if(is_user_logged_in() && !$down_only_pay){
											echo "<div class='erphp-creditbuy'><a target=_blank class='erphpdown-btn erphpdown-jump2 erphpdown-btn-show' href='".$erphp_url_front_recharge."'>".__('余额支付','erphpdown')."</a></div>";

											if($erphp_ajaxbuy){?>
											<a class="ss-button erphpdown-btn do-erphpdown-pay erphpdown-btn-hide" data-href="<?php echo constant("erphpdown").'checkout.php?ajax=1&postid='.$postid.'&timestamp='.time();?>" style="border:none;cursor: pointer;display: none;"><?php _e('余额支付','erphpdown');?></a>
											<?php }else{?>
											<a class="ss-button erphpdown-btn erphpdown-btn-hide" href="<?php echo constant("erphpdown").'checkout.php?postid='.$postid.'&timestamp='.time(); ?>" target="_blank" style="display: none;"><?php _e('余额支付','erphpdown');?></a>
											<?php }
										}
									}?>
									</div>
									<?php if(!is_user_logged_in()){?>
									<div class="login-tips" style="display:none"><?php echo sprintf(__('<a href="%s" target="_blank">登录</a>后购买可保存购买记录','erphpdown'), $erphp_url_front_login); ?></div>
									<?php }?>
								</div>
							</div>
								<?php
						}else{
							_e('价格错误','erphpdown');
						}
					}
				}
			}else{
				_e('请先登录','erphpdown');
			}

			if(get_option('erphp_promo')){?>
			<div class="erphpdown-promo">
				<?php _e('优惠码：','erphpdown');?><input type="text" id="erphp_promo" /> <a href="javascript:;" class="erphp-promo-do"><?php _e('使用','erphpdown');?></a>
			</div>
			<?php }
		}
	}elseif($user_type){
		if($user_type >5 && $user_type < 11){
			$priceArr=array('6'=>'erphp_day_price','7'=>'erphp_month_price','8'=>'erphp_quarter_price','9'=>'erphp_year_price','10'=>'erphp_life_price');
			$priceType=$priceArr[$user_type];
			$price=get_option($priceType);
			$okMoney=erphpGetUserOkMoney();
			if($price){
				$erphp_life_name    = get_option('erphp_life_name')?get_option('erphp_life_name'):'终身VIP';
				$erphp_year_name    = get_option('erphp_year_name')?get_option('erphp_year_name'):'包年VIP';
				$erphp_quarter_name = get_option('erphp_quarter_name')?get_option('erphp_quarter_name'):'包季VIP';
				$erphp_month_name  = get_option('erphp_month_name')?get_option('erphp_month_name'):'包月VIP';
				$erphp_day_name  = get_option('erphp_day_name')?get_option('erphp_day_name'):'体验VIP';
	?>
		<div class="erphpdown-table">
			<div class="item">
				<label><?php _e('购买内容','erphpdown');?></label>
				<div class="tit"><?php 
				if($user_type == '6') echo $erphp_day_name;
				elseif($user_type == '7') echo $erphp_month_name;
				elseif($user_type == '8') echo $erphp_quarter_name;
				elseif($user_type == '9') echo $erphp_year_name;
				elseif($user_type == '10') echo $erphp_life_name;
			?></div>
			</div>
			<div class="item item-price">
				<label><?php _e('购买结算','erphpdown');?></label>
				<div class="tit" style="text-align:right"><span><?php _e('小计','erphpdown');?></span><t class="login" data-price="<?php echo sprintf("%.2f",$price);?>" data-proportion="<?php echo get_option('ice_proportion_alipay');?>"><?php echo sprintf("%.2f",$price);?></t> <?php echo get_option('ice_name_alipay');?></div>
			</div>
			<div class="item" style="font-size:13px;color:#999;">
				<?php _e('账户余额：','erphpdown');?><t id="erphpdown_left"><?php echo sprintf("%.2f",$okMoney);?></t> <?php echo get_option('ice_name_alipay');?>
			</div>
			<div class="item">
				<div style="padding-top:20px;">
				<?php echo '<div class="erphp-justbuy">';?>
					<?php if(get_option('ice_weixin_mchid')){?> 
						<?php if(erphpdown_is_weixin() && get_option('ice_weixin_app')){?>
						<a href="<?php echo 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.get_option('ice_weixin_appid').'&redirect_uri='.urlencode(constant("erphpdown")).'payment%2Fweixin.php%3Fice_type%3D'.$user_type.'%26redirect_url='.urlencode(home_url()).'&response_type=code&scope=snsapi_base&state=STATE&connect_redirect=1#wechat_redirect';?>" class="pmt-wx erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-wxpay"></i> <?php _e('微信支付','erphpdown');?></a>
						<?php }else{?>
						<a href="<?php echo constant("erphpdown")."payment/weixin.php?ice_type=".$user_type."&redirect_url=".urlencode(home_url());?>" class="pmt-wx erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-wxpay"></i> <?php _e('微信支付','erphpdown');?></a>
						<?php }?>
					<?php }?>
					<?php if(get_option('ice_ali_partner') || get_option('ice_ali_app_id')){?> 
						<a href="<?php echo constant("erphpdown")."payment/alipay.php?ice_type=".$user_type."&redirect_url=".urlencode(home_url());?>" class="pmt-ali erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-alipay"></i> <?php _e('支付宝','erphpdown');?></a>
					<?php }?>
					<?php if(get_option('erphpdown_f2fpay_id') && !get_option('erphpdown_f2fpay_alipay')){?> 
						<a href="<?php echo constant("erphpdown")."payment/f2fpay.php?ice_type=".$user_type."&redirect_url=".urlencode(home_url());?>" class="pmt-ali erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-alipay"></i> <?php _e('支付宝','erphpdown');?></a>
					<?php }?>
					<?php if(get_option('erphpdown_payjs_appid')){?> 
						<?php if(!get_option('erphpdown_payjs_wxpay')){?><a href="<?php echo constant("erphpdown")."payment/payjs.php?ice_type=".$user_type."&redirect_url=".urlencode(home_url());?>" class="pmt-wx erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-wxpay"></i> <?php _e('微信支付','erphpdown');?></a><?php }?>  
						<?php if(!get_option('erphpdown_payjs_alipay')){?><a href="<?php echo constant("erphpdown")."payment/payjs.php?ice_type=".$user_type."&type=alipay&redirect_url=".urlencode(home_url());?>" class="pmt-ali erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-alipay"></i> <?php _e('支付宝','erphpdown');?></a><?php }?> 
					<?php }?>
					<?php if(get_option('erphpdown_xhpay_appid31')){?> 
						<a href="<?php echo constant("erphpdown")."payment/xhpay3.php?ice_type=".$user_type."&type=2"."&redirect_url=".urlencode(home_url());?>" class="pmt-wx erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-wxpay"></i> <?php _e('微信支付','erphpdown');?></a>   
					<?php }?>
					<?php if(get_option('erphpdown_xhpay_appid32')){?> 
						<a href="<?php echo constant("erphpdown")."payment/xhpay3.php?ice_type=".$user_type."&redirect_url=".urlencode(home_url());?>" class="pmt-ali erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-alipay"></i> <?php _e('支付宝','erphpdown');?></a>  
					<?php }?>
					<?php if(get_option('erphpdown_paypy_key')){?> 
						<?php if(!get_option('erphpdown_paypy_wxpay')){?><a href="<?php echo constant("erphpdown")."payment/paypy.php?ice_type=".$user_type."&redirect_url=".urlencode(home_url());?>" class="pmt-wx erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-wxpay"></i> <?php _e('微信支付','erphpdown');?></a><?php }?>  
						<?php if(!get_option('erphpdown_paypy_alipay')){?><a href="<?php echo constant("erphpdown")."payment/paypy.php?ice_type=".$user_type."&type=alipay"."&redirect_url=".urlencode(home_url());?>" class="pmt-ali erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-alipay"></i> <?php _e('支付宝','erphpdown');?></a><?php }?>
					<?php }?>
					<?php if(get_option('erphpdown_codepay_appid')){?> 
						<?php if(!get_option('erphpdown_codepay_alipay')){?><a href="<?php echo constant("erphpdown")."payment/codepay.php?ice_type=".$user_type."&type=1"."&redirect_url=".urlencode(home_url());?>" class="pmt-ali erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-alipay"></i> <?php _e('支付宝','erphpdown');?></a><?php }?>
						<?php if(!get_option('erphpdown_codepay_wxpay')){?><a href="<?php echo constant("erphpdown")."payment/codepay.php?ice_type=".$user_type."&type=3"."&redirect_url=".urlencode(home_url());?>" class="pmt-wx erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-wxpay"></i> <?php _e('微信支付','erphpdown');?></a><?php }?>
						<?php if(!get_option('erphpdown_codepay_qqpay')){?><a href="<?php echo constant("erphpdown")."payment/codepay.php?ice_type=".$user_type."&type=2"."&redirect_url=".urlencode(home_url());?>" class="pmt-qq erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-qqpay"></i> <?php _e('QQ钱包','erphpdown');?></a><?php }?>    
					<?php }?>
					<?php if(get_option('erphpdown_epay_id')){?> 
						<?php if(!get_option('erphpdown_epay_wxpay')){?><a href="<?php echo constant("erphpdown")."payment/epay.php?ice_type=".$user_type."&type=wxpay&redirect_url=".urlencode(home_url());?>" class="pmt-wx erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-wxpay"></i> <?php _e('微信支付','erphpdown');?></a><?php }?>  
						<?php if(!get_option('erphpdown_epay_alipay')){?><a href="<?php echo constant("erphpdown")."payment/epay.php?ice_type=".$user_type."&type=alipay"."&redirect_url=".urlencode(home_url());?>" class="pmt-ali erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-alipay"></i> <?php _e('支付宝','erphpdown');?></a><?php }?>
						<?php if(!get_option('erphpdown_epay_qqpay')){?><a href="<?php echo constant("erphpdown")."payment/epay.php?ice_type=".$user_type."&type=qqpay"."&redirect_url=".urlencode(home_url());?>" class="pmt-qq erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-qqpay"></i> <?php _e('QQ钱包','erphpdown');?></a><?php }?>
					<?php }?>
					<?php if(get_option('erphpdown_easepay_id')){?> 
						<?php if(!get_option('erphpdown_easepay_wxpay')){?><a href="<?php echo constant("erphpdown")."payment/easepay.php?ice_type=".$user_type."&type=wxpay&redirect_url=".urlencode(home_url());?>" class="pmt-wx erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-wxpay"></i> <?php _e('微信支付','erphpdown');?></a><?php }?>  
						<?php if(!get_option('erphpdown_easepay_alipay')){?><a href="<?php echo constant("erphpdown")."payment/easepay.php?ice_type=".$user_type."&type=alipay"."&redirect_url=".urlencode(home_url());?>" class="pmt-ali erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-alipay"></i> <?php _e('支付宝','erphpdown');?></a><?php }?>
						<?php if(!get_option('erphpdown_easepay_usdt')){?><a href="<?php echo constant("erphpdown")."payment/easepay.php?ice_type=".$user_type."&type=usdt&redirect_url=".urlencode(home_url());?>" class="pmt-ut erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-usdt"></i> <?php _e('USDT','erphpdown');?></a><?php }?>
					<?php }?>
					<?php if(get_option('erphpdown_vpay_key')){?> 
						<?php if(!get_option('erphpdown_vpay_wxpay')){?><a href="<?php echo constant("erphpdown")."payment/vpay.php?ice_type=".$user_type."&redirect_url=".urlencode(home_url());?>" class="pmt-wx erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-wxpay"></i> <?php _e('微信支付','erphpdown');?></a><?php }?>  
						<?php if(!get_option('erphpdown_vpay_alipay')){?><a href="<?php echo constant("erphpdown")."payment/vpay.php?ice_type=".$user_type."&type=2"."&redirect_url=".urlencode(home_url());?>" class="pmt-ali erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-alipay"></i> <?php _e('支付宝','erphpdown');?></a><?php }?>
					<?php }?>
					<?php if(get_option('erphpdown_stripe_pk')){?> 
						<a href="<?php echo home_url('?epd_v64='.base64_encode('stripe-'.$user_type.'-'.time()))."&redirect_url=".urlencode(home_url());?>" class="pmt-stripe erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-credit-card"></i> <?php _e('信用卡','erphpdown');?></a>
					<?php }?>
					<?php if(get_option('erphpdown_ecpay_MerchantID') && plugin_check_ecpay()){?> 
						<a href="<?php echo ERPHPDOWN_ECPAY_URL."/ecpay.php?ice_type=".$user_type."&redirect_url=".urlencode(home_url());?>" class="pmt-ecpay erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-ecpay"></i> <?php _e('新台币','erphpdown');?></a>
					<?php }?>
					<?php if(get_option('erphpdown_newebpay_MerchantID') && plugin_check_newebpay()){?> 
						<a href="<?php echo ERPHPDOWN_NEWEBPAY_URL."/newebpay.php?ice_type=".$user_type."&redirect_url=".urlencode(home_url());?>" class="pmt-newebpay erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-ecpay"></i> <?php _e('新台币','erphpdown');?></a>
					<?php }?>
					<?php if(get_option('erphpdown_usdt_address')){?> 
						<a href="<?php echo home_url('?epd_v64='.base64_encode('usdt-'.$user_type.'-'.time()))."&redirect_url=".urlencode(home_url());?>" class="pmt-ut erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-ut"></i> <?php _e('USDT','erphpdown');?></a>
					<?php }?>
					<?php if(get_option('ice_payapl_api_uid')){?> 
						<a href="<?php echo constant("erphpdown")."payment/paypal.php?ice_type=".$user_type."&redirect_url=".urlencode(home_url());?>" class="pmt-pp erphpdown-jump" target="_blank"><i class="erphp-iconfont erphp-icon-paypay"></i> Paypal</a>
					<?php }?>
				<?php echo '</div>';
				echo "<div class='erphp-creditbuy'><a class='erphpdown-btn erphpdown-btn-show do-erphpdown-vip-pay' data-type='".$user_type."' href='javascript:;'>".__('余额支付','erphpdown')."</a></div>";
				?>
				</div>
			</div>
		</div>
	<?php
			if(get_option('erphp_promo')){?>
			<div class="erphpdown-promo">
				<?php _e('优惠码：','erphpdown');?><input type="text" id="erphp_promo" /> <a href="javascript:;" class="erphp-promo-do"><?php _e('使用','erphpdown');?></a>
			</div>
			<?php }
			}else{
				_e('价格错误','erphpdown');
			}
		}else{
			_e('VIP类型错误','erphpdown');
		}
	}else{
		_e('文章ID错误','erphpdown');
	}
	?>

	</div>

	<script>
		$(".erphpdown-jump").click(function(){
			setTimeout(function(){
				parent.layer.closeAll();
				window.parent.erphpdownOrderSuccess();
			},1000);
		});

		$(".erphpdown-jump2").click(function(){
			parent.layer.closeAll();
		});
	</script>

	<?php if($erphp_ajaxbuy){?>
	<script>
		$(".do-erphpdown-pay").click(function(){
			var that = $(this);
			that.text("<?php _e('处理中...','erphpdown');?>").attr("disabled","disabled");
			$.ajax({  
	            type: 'GET',  
	            url:  $(this).data("href"),  
	            dataType: 'json',
				data: {

				},
	            success: function(data){
	            	that.text("<?php _e('余额支付','erphpdown');?>").removeAttr("disabled");  
	                if( data.error ){
	                    if( data.msg ){
	                        layer.msg(data.msg);
	                    }
	                }else{
	                	if(data.jump == '2'){
	                		parent.location.reload();
	                	}else if(data.jump == '1'){
	                		parent.location.href=data.link;
	                	}else{
	                		parent.location.reload();
	                	}
	                }

	            }  

	        });
	        return false;
		});
	</script>
	<?php }?>
</body>
</html>
