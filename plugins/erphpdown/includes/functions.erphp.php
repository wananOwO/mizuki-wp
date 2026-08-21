<?php
// +----------------------------------------------------------------------
// | ERPHP [ PHP DEVELOP ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.mobantu.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: mobantu <82708210@qq.com>
// +----------------------------------------------------------------------

if ( !defined('ABSPATH') ) {exit;}

add_action('admin_menu', 'mobantu_erphp_menu');
function mobantu_erphp_menu() {
	global $wpdb;
	$tx_count = $wpdb->get_var("SELECT count(ice_id) FROM $wpdb->iceget where ice_success != 1");
	
	if (function_exists('add_menu_page')) {
		if(current_user_can('administrator')){
			add_menu_page('erphpdown', 'ErphpDown'.($tx_count?'<span class="awaiting-mod">'.$tx_count.'</span>':''), 'activate_plugins', 'erphpdown/admin/erphp-settings.php', '','dashicons-shield');
		}else{
			add_menu_page('erphpdown2', 'ErphpDown', 'read', 'erphpdown/admin/erphp-add-money-online.php', '','dashicons-shield');
		}
	}
	if (function_exists('add_submenu_page')) {
		if(current_user_can('administrator')){
			add_submenu_page('erphpdown/admin/erphp-settings.php', '基础设置','基础设置', 'activate_plugins', 'erphpdown/admin/erphp-settings.php');
			add_submenu_page('erphpdown/admin/erphp-settings.php', '支付设置', '支付设置', 'activate_plugins', 'erphpdown/admin/erphp-payment.php');
			add_submenu_page('erphpdown/admin/erphp-settings.php', '显示设置', '显示设置', 'activate_plugins', 'erphpdown/admin/erphp-front.php');
			add_submenu_page('erphpdown/admin/erphp-settings.php', 'VIP设置','VIP设置','activate_plugins', 'erphpdown/admin/erphp-vip-setting.php');
			//add_submenu_page('erphpdown/admin/erphp-settings.php', '网站收入统计', '<font color=#ff5f33>网站收入统计</font>', 'activate_plugins', 'erphpdown/admin/erphp-statistics.php');
			add_submenu_page('erphpdown/admin/erphp-settings.php', '充值统计', '<font color=#dba617>充值统计</font>', 'activate_plugins', 'erphpdown/admin/erphp-chong-list.php');
			add_submenu_page('erphpdown/admin/erphp-settings.php', '购买文章统计', '<font color=#dba617>购买统计(文章)</font>', 'activate_plugins', 'erphpdown/admin/erphp-orders-list.php');
			add_submenu_page('erphpdown/admin/erphp-settings.php', '购买VIP统计','<font color=#dba617>购买统计(VIP)</font>','activate_plugins', 'erphpdown/admin/erphp-vip-items.php');
			add_submenu_page('erphpdown/admin/erphp-settings.php', 'VIP用户','VIP用户','activate_plugins', 'erphpdown/admin/erphp-vip-users.php');
			add_submenu_page('erphpdown/admin/erphp-settings.php', '赠送VIP', '赠送VIP', 'activate_plugins', 'erphpdown/admin/erphp-add-vip.php');
			add_submenu_page('erphpdown/admin/erphp-settings.php', '赠送购买', '赠送购买', 'activate_plugins', 'erphpdown/admin/erphp-add-buy.php');
	        add_submenu_page('erphpdown/admin/erphp-settings.php', '充值扣钱', '充值扣钱', 'activate_plugins', 'erphpdown/admin/erphp-add-money.php');
	        add_submenu_page('erphpdown/admin/erphp-settings.php', '余额明细', '余额明细', 'activate_plugins', 'erphpdown/admin/erphp-moneylog-list.php');
			//add_submenu_page('erphpdown/admin/erphp-settings.php', '附加购买统计', '附加购买统计', 'activate_plugins', 'erphpdown/admin/erphp-indexs-list.php');
			//add_submenu_page('erphpdown/admin/erphp-settings.php', '免登录统计(旧)', '免登录统计(旧)', 'activate_plugins', 'erphpdown/admin/erphp-wppays-list.php');
			add_submenu_page('erphpdown/admin/erphp-settings.php', 'VIP免费下载查看统计', '下载查看统计(VIP)', 'activate_plugins', 'erphpdown/admin/erphp-vipdown-list.php');
			add_submenu_page('erphpdown/admin/erphp-settings.php', '普通免费下载查看统计', '下载统计(全站免费)', 'activate_plugins', 'erphpdown/admin/erphp-freedown-list.php');
			add_submenu_page('erphpdown/admin/erphp-settings.php', '提现统计', '提现统计'.($tx_count?'<span class="awaiting-mod">'.$tx_count.'</span>':''), 'activate_plugins', 'erphpdown/admin/erphp-tixian-list.php');
	        add_submenu_page('erphpdown/admin/erphp-settings.php', '推广统计', '推广统计', 'activate_plugins', 'erphpdown/admin/erphp-reference-all.php');
	        add_submenu_page('erphpdown/admin/erphp-settings.php', '签到统计', '签到统计', 'activate_plugins', 'erphpdown/admin/erphp-checkin-list.php');
	        add_submenu_page('erphpdown/admin/erphp-settings.php', '销售排行', '销售排行', 'activate_plugins', 'erphpdown/admin/erphp-items-list.php');
	        add_submenu_page('erphpdown/admin/erphp-settings.php', '查询用户', '查询用户', 'activate_plugins', 'erphpdown/admin/erphp-check-users.php');
	        add_submenu_page('erphpdown/admin/erphp-settings.php', '批量处理', '批量处理', 'activate_plugins', 'erphpdown/admin/erphp-shop-list.php');
	        add_submenu_page('erphpdown/admin/erphp-settings.php', '清理数据', '清理数据', 'activate_plugins', 'erphpdown/admin/erphp-clear.php');
			add_submenu_page('erphpdown/admin/erphp-settings.php', '个人资产充值', '个人资产充值', 'read', 'erphpdown/admin/erphp-add-money-online.php');
			add_submenu_page('erphpdown/admin/erphp-settings.php', '个人申请提现', '个人申请提现', 'read', 'erphpdown/admin/erphp-money.php');
			
			if(plugin_check_video()){
				add_submenu_page('erphpdown/admin/erphp-settings.php', 'VOD设置', '<font color=#f78565>VOD设置</font>', 'activate_plugins', 'erphpdown-addon-video/settings.php');
			}
			if(function_exists('modown_tuan_menu')){
				add_submenu_page('erphpdown/admin/erphp-settings.php', '拼团统计', '<font color=#f78565>拼团统计</font>', 'activate_plugins', 'modown_tuan_list', 'modown_tuan_list');
			}else{
				if(plugin_check_tuan()){
					add_submenu_page('erphpdown/admin/erphp-settings.php', '拼团统计', '<font color=#f78565>拼团统计</font>', 'activate_plugins', 'modown_tuan_list', 'modown_tuan_list');
				}
			}
			if(function_exists('MBT_erphp_vip_cat')){
				add_submenu_page('erphpdown/admin/erphp-settings.php', '分类VIP订单','<font color=#f78565>分类VIP订单</font>','activate_plugins', 'erphpdown/admin/erphp-vip-cat-items.php');
				add_submenu_page('erphpdown/admin/erphp-settings.php', '分类VIP用户','<font color=#f78565>分类VIP用户</font>','activate_plugins', 'erphpdown/admin/erphp-vip-cat-users.php');
			}
			if(plugin_check_card()){
				add_submenu_page('erphpdown/admin/erphp-settings.php', '充值卡','<font color=#f78565>充值卡</font>', 'activate_plugins', 'erphpdown/addon/card/list.php');
				add_submenu_page('erphpdown/admin/erphp-settings.php', '添加充值卡','<font color=#f78565>添加充值卡</font>', 'activate_plugins', 'erphpdown/addon/card/add.php');
			}
			if(plugin_check_vipcard()){
				add_submenu_page('erphpdown/admin/erphp-settings.php', 'VIP充值卡','<font color=#f78565>VIP充值卡</font>', 'activate_plugins', 'erphpdown/addon/vipcard/list.php');
				add_submenu_page('erphpdown/admin/erphp-settings.php', '添加VIP充值卡','<font color=#f78565>添加VIP充值卡</font>', 'activate_plugins', 'erphpdown/addon/vipcard/add.php');
			}
			if(plugin_check_activation()){
				add_submenu_page('erphpdown/admin/erphp-settings.php', '激活码','<font color=#f78565>激活码</font>', 'activate_plugins', 'erphpdown/addon/activation/list.php');
				add_submenu_page('erphpdown/admin/erphp-settings.php', '添加激活码','<font color=#f78565>添加激活码</font>', 'activate_plugins', 'erphpdown/addon/activation/add.php');
			}
		}else{
			add_submenu_page('erphpdown/admin/erphp-add-money-online.php', '资产充值', '资产充值', 'read', 'erphpdown/admin/erphp-add-money-online.php');
			if(plugin_check_cred()){
				add_submenu_page('erphpdown/admin/erphp-add-money-online.php', '积分兑换','积分兑换', 'read', 'erphpdown/addon/mycred/erphp-to-mycred.php');
			}
			add_submenu_page('erphpdown/admin/erphp-add-money-online.php', '充值记录', '充值记录', 'read', 'erphpdown/admin/erphp-add-money-list.php');
			add_submenu_page('erphpdown/admin/erphp-add-money-online.php', '升级VIP', '升级VIP', 'read', 'erphpdown/admin/erphp-update-vip.php');
			add_submenu_page('erphpdown/admin/erphp-add-money-online.php', 'VIP记录', 'VIP记录', 'read', 'erphpdown/admin/erphp-update-vip-list.php');
			add_submenu_page('erphpdown/admin/erphp-add-money-online.php', '消费清单', '消费清单', 'read', 'erphpdown/admin/erphp-get-items.php');
			add_submenu_page('erphpdown/admin/erphp-add-money-online.php', '销售订单', '销售订单', 'edit_posts', 'erphpdown/admin/erphp-items.php');
			add_submenu_page('erphpdown/admin/erphp-add-money-online.php', '提现列表', '提现列表', 'read', 'erphpdown/admin/erphp-money-list.php');
			add_submenu_page('erphpdown/admin/erphp-add-money-online.php', '申请提现', '申请提现', 'read', 'erphpdown/admin/erphp-money.php');
			add_submenu_page('erphpdown/admin/erphp-add-money-online.php', '推广注册', '推广注册', 'read', 'erphpdown/admin/erphp-reference.php');
			add_submenu_page('erphpdown/admin/erphp-add-money-online.php', '推广下载', '推广下载', 'read', 'erphpdown/admin/erphp-reference-list.php');
			add_submenu_page('erphpdown/admin/erphp-add-money-online.php', '推广VIP', '推广VIP', 'read', 'erphpdown/admin/erphp-reference-vip-list.php');
			add_submenu_page('erphpdown/admin/erphp-add-money-online.php', '免费下载记录', '免费下载记录', 'read', 'erphpdown/admin/erphp-vipdown-list-my.php');
		}
    }
    
}

function epd_wppay_callback(){
	global $wpdb;
	$post_id = esc_sql($_POST['post_id']);
	$index = (isset($_POST['index']) && $_POST['index']) ?esc_sql($_POST['index']):'';
	$index_name = '';
	$user_id = is_user_logged_in() ? wp_get_current_user()->ID : 0;
	$price = get_post_meta($post_id,'down_price',true);
	$price2 = '';$code='';$code2='';$link='';$msg='';$num='';$status=400;$minute=0;$aliurl='';$wxurl='';$gift=0;$gift2=0;
	$out_trade_no = 'MD'.date("ymdhis").mt_rand(100,999).mt_rand(100,999).mt_rand(100,999);

	if($index){
		$urls = get_post_meta($post_id, 'down_urls', true);
		if($urls){
			$cnt = count($urls['index']);
			if($cnt){
				for($i=0; $i<$cnt;$i++){
					if($urls['index'][$i] == $index){
    					$index_name = $urls['name'][$i];
    					$price = $urls['price'][$i];
    					break;
    				}
				}
			}
		}
	}

	if($price && !get_option('erphp_wppay_close')){

		$wppay = new EPD($post_id, $user_id);

		if(is_user_logged_in()){
			global $current_user;
			$okMoney = erphpGetUserOkMoney();
			// 修复：兑换比例未配置时 get_option 返回 false，$price*false=0 会导致余额购买实扣 0 元；此处兜底默认 1，且判断与扣款统一用 $balancePrice
			$erphp_prop_alipay = get_option('ice_proportion_alipay');
			$erphp_prop_alipay = ($erphp_prop_alipay === '' || $erphp_prop_alipay === false) ? 1 : floatval($erphp_prop_alipay);
			$balancePrice = $price * $erphp_prop_alipay;
			if($okMoney >= $balancePrice){

				if(erphpSetUserMoneyXiaoFei($balancePrice)){
					$subject   = get_post($post_id)->post_title;
					$postUserId=get_post($post_id)->post_author;

					if($index_name){
						$subject .= ' - '.$index_name;
					}
					
					$result=erphpAddDownloadBuyNum($subject, $post_id, $out_trade_no, $balancePrice, 1, '', $postUserId, $index);
					if($result){
						if($down_activation && function_exists('doErphpAct')){
							$activation_num = doErphpAct($current_user->ID,$post_id);
							$wpdb->query("update $wpdb->icealipay set ice_data = '".$activation_num."' where ice_url='".$result."'");
							if($current_user->user_email){
								wp_mail($current_user->user_email, '【'.$subject.'】激活码', '您购买的资源【'.$subject.'】激活码：'.$activation_num);
							}
						}

						$EPD = new EPD();
						$EPD->doAuthorAff($balancePrice, $postUserId);
						$EPD->doAff($balancePrice, $current_user->ID);

						if(get_option('erphp_remind')){
							wp_mail(get_option('admin_email'), '【'.get_bloginfo('name').'】订单提醒 - '.$subject, '用户'.$current_user->user_login.'消费'.($balancePrice).get_option('ice_name_alipay').'购买了'.$subject.get_permalink($post_id));
						}

						$num = $out_trade_no;
						$status=202;

					}else{
						$wpdb->query("update $wpdb->iceinfo set ice_get_money=ice_get_money-".($balancePrice) ." where ice_user_id=".$current_user->ID);
						$status=201;
						$msg='系统错误，请稍后重试！';
					}
					
				}else{
					$status=201;
					$msg = '支付失败，请稍后重试！';
				}

				$result = array(
					'status' => $status,
					'num' => $num,
					'msg' => $msg
				);

				header('Content-type: application/json');
				echo json_encode($result);
				exit;
			}
		}

		$price = sprintf("%.2f",$price);

		if(get_option('erphp_wppay_payment') == 'f2fpay'){
			$qrPayResult = $wppay->f2fpayWppayQr($out_trade_no, $price);
			if($qrPayResult->getTradeStatus() == 'SUCCESS'){
				if($wppay->addWppayNew($out_trade_no, $price, $index, 'f2fpay')){
					$response = $qrPayResult->getResponse();
					$code = ERPHPDOWN_URL.'/includes/qrcode.php?data='.urlencode($response->qr_code);
					$aliurl = $response->qr_code;
					$num = $out_trade_no;
					$status=200;
				}
			}else{
				$status=201;
				$msg = '获取支付信息失败！';
			}
		}elseif(get_option('erphp_wppay_payment') == 'weixin'){
			$qrPayResult = $wppay->weixinWppayQr($out_trade_no, $price);
			if($qrPayResult['result_code'] == 'SUCCESS'){
				if($wppay->addWppayNew($out_trade_no, $price, $index, 'wxpay')){
					$code = ERPHPDOWN_URL.'/includes/qrcode.php?data='.urlencode($qrPayResult['code_url']);
					$link = '';
					$num = $out_trade_no;
					$status=200;
				}
			}else{
				$status=201;
				$msg = '获取支付信息失败！';
			}
		}elseif(get_option('erphp_wppay_payment') == 'f2fpay_weixin'){
			$qrF2fpayPayResult = $wppay->f2fpayWppayQr($out_trade_no, $price);
			$qrWeixinPayResult = $wppay->weixinWppayQr($out_trade_no, $price);
			if($qrWeixinPayResult['result_code'] == 'SUCCESS' && $qrF2fpayPayResult->getTradeStatus() == 'SUCCESS'){
				if($wppay->addWppayNew($out_trade_no, $price, $index, 'f2fpay')){
					$response = $qrF2fpayPayResult->getResponse();
					$code = ERPHPDOWN_URL.'/includes/qrcode.php?data='.urlencode($response->qr_code);
					$code2 = ERPHPDOWN_URL.'/includes/qrcode.php?data='.urlencode($qrWeixinPayResult['code_url']);
					$aliurl = $response->qr_code;
					$num = $out_trade_no;
					$status=200;
				}
			}else{
				$status=201;
				$msg = '获取支付信息失败！';
			}
		}elseif(get_option('erphp_wppay_payment') == 'weixin_hupiv3'){
		    $erphpdown_xhpay_admin = get_option('erphpdown_xhpay_admin');
		    if($erphpdown_xhpay_admin){
		        $qrAlipayPayResult = $wppay->xunhuAliWppayQr($out_trade_no, $price);
    			$qrWeixinPayResult = $wppay->weixinWppayQr($out_trade_no, $price);
    			if($qrWeixinPayResult['result_code'] == 'SUCCESS' && (isset($qrAlipayPayResult) && $qrAlipayPayResult['return_code'] == 'SUCCESS')){
    				if($wppay->addWppayNew($out_trade_no, $price, $index, 'wxpay')){
    					$code = ERPHPDOWN_URL.'/includes/qrcode.php?data='.urlencode($qrAlipayPayResult['code_url']);
    					$aliurl = urldecode($qrAlipayPayResult['code_url']);
    					$code2 = ERPHPDOWN_URL.'/includes/qrcode.php?data='.urlencode($qrWeixinPayResult['code_url']);
    					$num = $out_trade_no;
    					$status=200;
    				}
    			}else{
    				$status=201;
    				$msg = '获取支付信息失败！';
    			}
		    }else{
    			$qrAlipayPayResult = $wppay->hupiAliWppayQr($out_trade_no, $price);
    			$qrWeixinPayResult = $wppay->weixinWppayQr($out_trade_no, $price);
    			if($qrWeixinPayResult['result_code'] == 'SUCCESS' && (isset($qrAlipayPayResult) && $qrAlipayPayResult['errcode'] == 0)){
    				if($wppay->addWppayNew($out_trade_no, $price, $index, 'wxpay')){
    					$code = $qrAlipayPayResult['url_qrcode'];
    					$aliurl = $qrAlipayPayResult['url'];
    					$code2 = ERPHPDOWN_URL.'/includes/qrcode.php?data='.urlencode($qrWeixinPayResult['code_url']);
    					$num = $out_trade_no;
    					$status=200;
    				}
    			}else{
    				$status=201;
    				$msg = '获取支付信息失败！';
    			}
		    }
		}elseif(get_option('erphp_wppay_payment') == 'f2fpay_hupiv3'){
			$qrF2fpayPayResult = $wppay->f2fpayWppayQr($out_trade_no, $price);
			$qrWeixinPayResult = $wppay->hupiWxWppayQr($out_trade_no, $price);
			if($qrWeixinPayResult['errcode'] == 0 && $qrF2fpayPayResult->getTradeStatus() == 'SUCCESS'){
				if($wppay->addWppayNew($out_trade_no, $price, $index, 'f2fpay')){
					$response = $qrF2fpayPayResult->getResponse();
					$code = ERPHPDOWN_URL.'/includes/qrcode.php?data='.urlencode($response->qr_code);
					$code2 = $qrWeixinPayResult['url_qrcode'];
					$aliurl = $response->qr_code;
					$wxurl = $qrWeixinPayResult['url'];
					$num = $out_trade_no;
					$status=200;
				}
			}else{
				$status=201;
				$msg = '获取支付信息失败！';
			}
		}elseif(get_option('erphp_wppay_payment') == 'payjs'){ 

			$qrAlipayPayResult = $wppay->payjsAliWppayQr($out_trade_no, $price);
			$qrWeixinPayResult = $wppay->payjsWxWppayQr($out_trade_no, $price);
			if((isset($qrWeixinPayResult) && $qrWeixinPayResult->return_code == 1) || (isset($qrAlipayPayResult) && $qrAlipayPayResult->return_code == 1)){
				if($wppay->addWppayNew($out_trade_no, $price, $index, 'payjs')){
					
					if($qrAlipayPayResult->return_code == 1){
						$code = $qrAlipayPayResult->qrcode;
						$aliurl = $qrAlipayPayResult->code_url;
					}
				
				
					if($qrWeixinPayResult->return_code == 1){
						$code2 = $qrWeixinPayResult->qrcode;
					}

					$link = '';
					$num = $out_trade_no;
					$status=200;
				}
			}else{
				$status=201;
				$msg = '获取支付信息失败！';
			}
		}elseif(get_option('erphp_wppay_payment') == 'hupiv3'){ 
			$appid_ali = get_option('erphpdown_xhpay_appid32');
			$appid_wx = get_option('erphpdown_xhpay_appid31');

			if($appid_ali) $qrAlipayPayResult = $wppay->hupiAliWppayQr($out_trade_no, $price);
			if($appid_wx) $qrWeixinPayResult = $wppay->hupiWxWppayQr($out_trade_no, $price);
			if((isset($qrWeixinPayResult) && $qrWeixinPayResult['errcode'] == 0) || (isset($qrAlipayPayResult) && $qrAlipayPayResult['errcode'] == 0)){
				if($wppay->addWppayNew($out_trade_no, $price, $index, 'xhpay')){
					if($appid_ali){
						if($qrAlipayPayResult['errcode'] == 0){
							$code = $qrAlipayPayResult['url_qrcode'];
							$aliurl = $qrAlipayPayResult['url'];
						}
					}
					if($appid_wx){
						if($qrWeixinPayResult['errcode'] == 0){
							$code2 = $qrWeixinPayResult['url_qrcode'];
							$wxurl = $qrWeixinPayResult['url'];
						}
					}

					$link = '';
					$num = $out_trade_no;
					$status=200;
				}
			}else{
				$status=201;
				$msg = '获取支付信息失败！';
			}
		}elseif(get_option('erphp_wppay_payment') == 'f2fpay_paypy'){
			$old_price = $price;
			$qrF2fpayPayResult = $wppay->f2fpayWppayQr($out_trade_no, $price);
			$qrResult = $wppay->paypyWxQr($out_trade_no, $price);
			if($qrResult['status'] == '1' && $qrF2fpayPayResult->getTradeStatus() == 'SUCCESS'){
				if($wppay->addWppayNew($out_trade_no, $price, $index, 'f2fpay')){

					$response = $qrF2fpayPayResult->getResponse();
					$code = ERPHPDOWN_URL.'/includes/qrcode.php?data='.urlencode($response->qr_code);
					$aliurl = $response->qr_code;

					if($qrResult['wx_url']){
						$code2 = ERPHPDOWN_URL.'/includes/qrcode.php?data='.urldecode($qrResult['wx_url']);
						$price2 = $qrResult['wx_qr_price'];
						if($price2 < $old_price){
							$gift2 = 1;
						}
					}
					$minute = $qrResult['minute'];
					$num = $out_trade_no;
					$status=200;
				}
			}else{
				$status=201;
				$msg = '获取支付信息失败！';
			}
		}elseif(get_option('erphp_wppay_payment') == 'paypy'){
			$old_price = $price;
			$qrResult = $wppay->paypyQr($out_trade_no, $price);
			if($qrResult['status'] == '1'){
				if($wppay->addWppayNew($out_trade_no, $price, $index, 'paypy')){
					if($qrResult['ali_url']){
						$code = ERPHPDOWN_URL.'/includes/qrcode.php?data='.urldecode($qrResult['ali_url']);
						$price = $qrResult['ali_qr_price'];
						if($price < $old_price){
							$gift = 1;
						}
						$aliurl = urldecode($qrResult['ali_url']);
					}
					if($qrResult['wx_url']){
						$code2 = ERPHPDOWN_URL.'/includes/qrcode.php?data='.urldecode($qrResult['wx_url']);
						$price2 = $qrResult['wx_qr_price'];
						if($price2 < $old_price){
							$gift2 = 1;
						}
					}
					$minute = $qrResult['minute'];
					$num = $out_trade_no;
					$status=200;
				}
			}else{
				$status=201;
				$msg = '获取支付信息失败！';
			}
		}elseif(get_option('erphp_wppay_payment') == 'vpay'){
			$old_price = $price;
			$qrResult = $wppay->vpayQr($out_trade_no, $price);
			if($qrResult['status'] == '1'){
				if($wppay->addWppayNew($out_trade_no, $price, $index, 'vpay')){
					if($qrResult['ali_url']){
						$code = ERPHPDOWN_URL.'/includes/qrcode.php?data='.$qrResult['ali_url'];
						$price = $qrResult['ali_qr_price'];
						if($price != $old_price){
							$gift = 1;
						}
						$aliurl = urldecode($qrResult['ali_url']);
					}
					if($qrResult['wx_url']){
						$code2 = ERPHPDOWN_URL.'/includes/qrcode.php?data='.$qrResult['wx_url'];
						$price2 = $qrResult['wx_qr_price'];
						if($price2 != $old_price){
							$gift2 = 1;
						}
					}
					$minute = $qrResult['minute'];
					$num = $out_trade_no;
					$status=200;
				}
			}else{
				$status=201;
				$msg = '获取支付信息失败！';
			}
		}
	}else{
		$result = array(
			'status' => $status,
			'msg' => '获取支付信息失败！'
		);

		header('Content-type: application/json');
		echo json_encode($result);
		exit;
	}

	$result = array(
		'status' => $status,
		'price' =>$price,
		'price2' =>$price2,
		'code' => $code,
		'code2' => $code2,
		'gift' => $gift,
		'gift2' => $gift2,
		'link' => $link,
		'aliurl' => $aliurl,
		'wxurl' => $wxurl,
		'minute' => $minute,
		'num' => $num,
		'msg' => $msg
	);

	header('Content-type: application/json');
	echo json_encode($result);
	exit;
}
add_action( 'wp_ajax_epd_wppay', 'epd_wppay_callback');
add_action( 'wp_ajax_nopriv_epd_wppay', 'epd_wppay_callback');

function epd_wppay_pay_callback(){
	global $wpdb;
	$post_id = esc_sql($_POST['post_id']);
	$order_num = esc_sql($_POST['order_num']);
	$status = 0;
	if($post_id && $order_num && !get_option('erphp_wppay_close')){
		$user_id = is_user_logged_in() ? wp_get_current_user()->ID : 0;
		$wppay = new EPD($post_id, $user_id);
		if($wppay->checkWppayPaidNew($order_num)){
			$days = get_option('erphp_wppay_cookie');
			$expire = time() + $days*24*60*60;
		    setcookie('wppay_'.$post_id, $wppay->setWppayKey($order_num), $expire, '/', $_SERVER['HTTP_HOST'], false);
		    $status = 1;
		}
	}

	$result = array(
		'status' => $status
	);

	header('Content-type: application/json');
	echo json_encode($result);
	exit;
}
add_action( 'wp_ajax_epd_wppay_pay', 'epd_wppay_pay_callback');
add_action( 'wp_ajax_nopriv_epd_wppay_pay', 'epd_wppay_pay_callback');


function epd_index_callback(){
	global $wpdb;
	$post_id = $_POST['post_id'];
	$index_id = $_POST['index_id'];
	$price = $_POST['price'];
	$user_id = wp_get_current_user()->ID;
	$okMoney=erphpGetUserOkMoney();

	$erphp_url_front_recharge = get_bloginfo('wpurl').'/wp-admin/admin.php?page=erphpdown/admin/erphp-add-money-online.php';
	if(get_option('erphp_url_front_recharge')){
		$erphp_url_front_recharge = get_option('erphp_url_front_recharge');
	}
							
	$status = 201;

	if(sprintf("%.2f",$okMoney) >= $price && $okMoney > 0 && $price > 0){
		if(erphpSetUserMoneyXiaoFei($price)){
			$postUserId=get_post($post_id)->post_author;
			$result = erphpAddDownloadIndex($post_id,$price,$index_id);
			if($result){

				$EPD = new EPD();
				$EPD->doAuthorAff($price, $postUserId);
				$EPD->doAff($price, $user_id);

				$status = 200;
			}else{
				$msg = '购买失败，请稍后重试！';
			}
		}else{
			$msg = '购买失败，请稍后重试！';
		}
	}else{
		$status = 202;
		$msg = '余额不足，请先充值！';
	}

	$result = array(
		'status' => $status,
		'msg' => $msg,
		'recharge' => $erphp_url_front_recharge
	);

	header('Content-type: application/json');
	echo json_encode($result);
	exit;
}
add_action( 'wp_ajax_epd_index', 'epd_index_callback');

function epd_see_callback(){
	date_default_timezone_set('Asia/Shanghai'); 
	global $wpdb;
	$post_id = $_POST['post_id'];
	$token = $_POST['token'];
	$vip = $_POST['vip'];
	$free = ($vip == 'free') ? 1:0;
	$user_info=wp_get_current_user();
	$userType=getUsreMemberType();
	$status = 0;

	$erphpdown_downkey = get_option('erphpdown_downkey');

	$erphp_life_times    = get_option('erphp_life_times');
	$erphp_year_times    = get_option('erphp_year_times');
	$erphp_quarter_times = get_option('erphp_quarter_times');
	$erphp_month_times  = get_option('erphp_month_times');
	$erphp_day_times  = get_option('erphp_day_times');

	if(get_option('vip_times_see')){
		$erphp_life_times    = get_option('erphp_life_times2');
		$erphp_year_times    = get_option('erphp_year_times2');
		$erphp_quarter_times = get_option('erphp_quarter_times2');
		$erphp_month_times  = get_option('erphp_month_times2');
		$erphp_day_times  = get_option('erphp_day_times2');
	}

	$memberDown=get_post_meta($post_id, 'member_down',true);

	if($free == '1'){
		$price=get_post_meta($post_id, 'down_price', true);
		$erphp_reg_times = get_option("erphp_reg_times");
		if(!$price && $memberDown != 4 && $memberDown != 15 && $memberDown != 8 && $memberDown != 9){
			if($erphp_reg_times){
				if( checkSeeLog($user_info->ID,$post_id,$erphp_day_times,erphpGetIP(),0) ){
					if(get_option('vip_times_see')){
						addSeeLog($user_info->ID,$post_id,erphpGetIP(),0,1);
					}else{
						addDownLog($user_info->ID,$post_id,erphpGetIP(),0);
					}
					$status = 200;
				}else{
					$status = 201;
				}
			}
		}
	}else{

		/*if(($userType && ($memberDown==3 || $memberDown==4)) || (($memberDown==15 || $memberDown==16) && $userType >= 8) || (($memberDown==6 || $memberDown==8) && $userType >= 9) || (($memberDown==7 || $memberDown==9 || $memberDown==13 || $memberDown==14 || $memberDown==20) && $userType == 10)){*/
		if($token && $token == md5($erphpdown_downkey.$post_id.$erphpdown_downkey.$vip)){
			if($userType == 6 && $erphp_day_times > 0){
				if( checkSeeLog($user_info->ID,$post_id,$erphp_day_times,erphpGetIP(),1) ){
					if(get_option('vip_times_see')){
						addSeeLog($user_info->ID,$post_id,erphpGetIP(),1,1);
					}else{
						addDownLog($user_info->ID,$post_id,erphpGetIP(),1);
					}
					$status = 200;
				}else{
					$status = 201;
				}
			}elseif($userType == 7 && $erphp_month_times > 0){
				if( checkSeeLog($user_info->ID,$post_id,$erphp_month_times,erphpGetIP(),1) ){
					if(get_option('vip_times_see')){
						addSeeLog($user_info->ID,$post_id,erphpGetIP(),1,1);
					}else{
						addDownLog($user_info->ID,$post_id,erphpGetIP(),1);
					}
					$status = 200;
				}else{
					$status = 201;
				}
			}elseif($userType == 8 && $erphp_quarter_times > 0){
				if( checkSeeLog($user_info->ID,$post_id,$erphp_quarter_times,erphpGetIP(),1) ){
					if(get_option('vip_times_see')){
						addSeeLog($user_info->ID,$post_id,erphpGetIP(),1,1);
					}else{
						addDownLog($user_info->ID,$post_id,erphpGetIP(),1);
					}
					$status = 200;
				}else{
					$status = 201;
				}
			}elseif($userType == 9 && $erphp_year_times > 0){
				if( checkSeeLog($user_info->ID,$post_id,$erphp_year_times,erphpGetIP(),1) ){
					if(get_option('vip_times_see')){
						addSeeLog($user_info->ID,$post_id,erphpGetIP(),1,1);
					}else{
						addDownLog($user_info->ID,$post_id,erphpGetIP(),1);
					}
					$status = 200;
				}else{
					$status = 201;
				}
			}elseif($userType == 10 && $erphp_life_times > 0){
				if( checkSeeLog($user_info->ID,$post_id,$erphp_life_times,erphpGetIP(),1) ){
					if(get_option('vip_times_see')){
						addSeeLog($user_info->ID,$post_id,erphpGetIP(),1,1);
					}else{
						addDownLog($user_info->ID,$post_id,erphpGetIP(),1);
					}
					$status = 200;
				}else{
					$status = 201;
				}
			}
		}else{
			$status = 202;
		}

		
	}

	$result = array(
		'status' => $status
	);

	header('Content-type: application/json');
	echo json_encode($result);
	exit;
}
add_action( 'wp_ajax_epd_see', 'epd_see_callback');
add_action( 'wp_ajax_nopriv_epd_see', 'epd_see_callback');

function epd_vip_pay_callback(){
	session_start();
	global $wpdb,$current_user;

	$error = 1;$msg = __('升级失败','erphpdown');

	if(function_exists('_MBT') && _MBT('vip_only_pay')){
		$error = 1;$msg = __('抱歉，仅支持在线支付升级','erphpdown');
	}else{

		$erphp_life_name    = get_option('erphp_life_name')?get_option('erphp_life_name'):'终身VIP';
		$userType=isset($_POST['type']) && is_numeric($_POST['type']) ?intval($_POST['type']) :0;
		$oldUserType = getUsreMemberTypeById($current_user->ID);
		if($oldUserType == '10' && !get_option('erphp_life_days')){
			$msg = sprintf( __('您已经是%s，请勿重复升级','erphpdown'), $erphp_life_name);
		}else{
			if($userType >5 && $userType < 11){
				$okMoney=erphpGetUserOkMoney();
				$okMoney=sprintf("%.2f",$okMoney);
				$priceArr=array('6'=>'erphp_day_price','7'=>'erphp_month_price','8'=>'erphp_quarter_price','9'=>'erphp_year_price','10'=>'erphp_life_price');
				$priceType=$priceArr[$userType];
				$price=get_option($priceType);

				$vip_update_pay = get_option('vip_update_pay');
				if($vip_update_pay){
					$erphp_quarter_price = get_option('erphp_quarter_price');
					$erphp_month_price  = get_option('erphp_month_price');
					$erphp_day_price  = get_option('erphp_day_price');
					$erphp_year_price    = get_option('erphp_year_price');
					$erphp_life_price  = get_option('erphp_life_price');

					if($userType == 7){
						if($oldUserType == 6){
	                 		$price = $erphp_month_price - $erphp_day_price;
	                 	}
					}elseif($userType == 8){
						if($oldUserType == 6){
	                 		$price = $erphp_quarter_price - $erphp_day_price;
	                 	}elseif($oldUserType == 7){
	                 		$price = $erphp_quarter_price - $erphp_month_price;
	                 	}
					}elseif($userType == 9){
						if($oldUserType == 6){
	                 		$price = $erphp_year_price - $erphp_day_price;
	                 	}elseif($oldUserType == 7){
	                 		$price = $erphp_year_price - $erphp_month_price;
	                 	}elseif($oldUserType == 8){
	                 		$price = $erphp_year_price - $erphp_quarter_price;
	                 	}
					}elseif($userType == 10){
						if($oldUserType == 6){
	                 		$price = $erphp_life_price - $erphp_day_price;
	                 	}elseif($oldUserType == 7){
	                 		$price = $erphp_life_price - $erphp_month_price;
	                 	}elseif($oldUserType == 8){
	                 		$price = $erphp_life_price - $erphp_quarter_price;
	                 	}elseif($oldUserType == 9){
	                 		$price = $erphp_life_price - $erphp_year_price;
	                 	}
					}
				}

				if(isset($_SESSION['erphp_promo_code']) && $_SESSION['erphp_promo_code']){
			        $promo = str_replace("\\","", $_SESSION['erphp_promo_code']);
			        $promo_arr = json_decode($promo,true);
			        if($promo_arr['type'] == 1){
			            $promo_money = get_option('erphp_promo_money1');
			            if($promo_money){
			                $price = $price - $promo_money;
			            }
			        }elseif($promo_arr['type'] == 2){
			            $promo_money = get_option('erphp_promo_money2');
			            if($promo_money){
			                $price = $price * 0.1 * $promo_money;
			            }
			        }
			    }

				if($price <= 0){
					$msg = __('VIP价格错误','erphpdown');
				}elseif($okMoney < $price){
					$msg = __('余额不足，请先充值','erphpdown');
				}elseif($okMoney >=$price){
					if(erphpSetUserMoneyXiaoFei($price)){
						if(function_exists('addUserMoneyLog')){
	                        addUserMoneyLog($current_user->ID, '-'.$price, __('升级VIP','erphpdown'));
	                    }
						if(userPayMemberSetData($userType)){
							$error = 0;
							addVipLog($price, $userType);

							$EPD = new EPD();
							$EPD->doAff($price, $current_user->ID);

							if(get_option('erphp_remind')){
								$headers = 'Content-Type: text/html; charset=' . get_option('blog_charset') . "\n";
								$typeName = getVipTypeName($userType);
								wp_mail(get_option('admin_email'), '['.get_bloginfo('name').']VIP订单提醒 - '.$typeName, '用户'.$current_user->user_login.'消费'.$price.get_option('ice_name_alipay').'购买了'.$typeName, $headers);
							}

						}else{
							$msg = __('升级失败','erphpdown');
						}
					}else{
						$msg = __('升级失败','erphpdown');
					}
				}else{
					$msg = __('升级失败','erphpdown');
				}
			}
		}
	}
	
	
	$result = array(
		'error' => $error,
		'msg' => $msg
	);

	header('Content-type: application/json');
	echo json_encode($result);
	exit;
}
add_action( 'wp_ajax_epd_vip_pay', 'epd_vip_pay_callback');

add_action( 'init', 'erphp_checkin_schema_guard' );
function erphp_checkin_schema_guard(){
	if ( get_option( 'erphp_checkin_day_migrated' ) ) return;
	global $wpdb;
	if ( ! $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $wpdb->checkin ) ) ) return;
	if ( ! $wpdb->get_var( "SHOW COLUMNS FROM `{$wpdb->checkin}` LIKE 'create_day'" ) ) {
		$wpdb->query( "ALTER TABLE `{$wpdb->checkin}` ADD COLUMN `create_day` date NULL" );
		$wpdb->query( "UPDATE `{$wpdb->checkin}` SET create_day = DATE(create_time) WHERE create_day IS NULL" );
	}
	if ( ! $wpdb->get_var( "SHOW INDEX FROM `{$wpdb->checkin}` WHERE Key_name = 'uniq_user_day'" ) ) {
		$wpdb->query( "DELETE t1 FROM `{$wpdb->checkin}` t1 JOIN `{$wpdb->checkin}` t2 ON t1.user_id = t2.user_id AND t1.create_day = t2.create_day AND t1.ID > t2.ID" );
		$wpdb->query( "ALTER TABLE `{$wpdb->checkin}` ADD UNIQUE KEY `uniq_user_day` (`user_id`, `create_day`)" );
	}
	$column_ok = (bool) $wpdb->get_var( "SHOW COLUMNS FROM `{$wpdb->checkin}` LIKE 'create_day'" );
	$index_ok = (bool) $wpdb->get_var( "SHOW INDEX FROM `{$wpdb->checkin}` WHERE Key_name = 'uniq_user_day'" );
	if ( $column_ok && $index_ok ) update_option( 'erphp_checkin_day_migrated', 1 );
}

function epd_checkin_callback(){
	global $wpdb;
	if ( ! is_user_logged_in() ) {
		wp_send_json( array( 'status' => 401, 'msg' => '请先登录！' ) );
	}
	if ( ! check_ajax_referer( 'erphp-checkin', 'nonce', false ) ) {
		wp_send_json( array( 'status' => 403, 'msg' => '安全校验失败，请刷新页面后重试。' ) );
	}
	$user_id = get_current_user_id();
	$gift = floatval( get_option( 'ice_ali_money_checkin' ) );
	if ( $gift <= 0 ) {
		wp_send_json( array( 'status' => 201, 'msg' => '抱歉，签到功能已关闭！' ) );
	}
	erphp_checkin_schema_guard();
	if ( erphpdown_check_checkin( $user_id ) ) {
		wp_send_json( array( 'status' => 201, 'msg' => '您今天已经签过到了，明儿再来哦～' ) );
	}
	$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	$inserted = $wpdb->query( $wpdb->prepare(
		"INSERT IGNORE INTO `{$wpdb->checkin}` (user_id, credit, create_time, create_day, user_ip) VALUES (%d, %s, NOW(), CURDATE(), %s)",
		$user_id, (string) $gift, $ip
	) );
	if ( ! $inserted ) {
		wp_send_json( array( 'status' => 201, 'msg' => '您今天已经签过到了，明儿再来哦～' ) );
	}
	$added = function_exists( 'addUserMoney' ) ? addUserMoney( $user_id, $gift, '每日签到奖励' ) : false;
	if ( ! $added ) {
		$wpdb->query( $wpdb->prepare( "DELETE FROM `{$wpdb->checkin}` WHERE user_id = %d AND create_day = CURDATE()", $user_id ) );
		error_log( '[erphpdown] 签到加钱失败 user=' . $user_id . ' db_error=' . $wpdb->last_error );
		wp_send_json( array( 'status' => 500, 'msg' => '签到失败：奖励发放异常，请联系管理员。' ) );
	}
	wp_send_json( array( 'status' => 200, 'msg' => '签到成功，获得 ' . $gift . ' 余额' ) );
}
add_action( 'wp_ajax_epd_checkin', 'epd_checkin_callback');

function epd_promo_callback(){
	session_start();
	global $wpdb;
	$promo = esc_sql($_POST['promo']);
	$status = 0;$type = 0;$money = '';
	if($promo){
		$erphp_promo_code1 = get_option('erphp_promo_code1');
		$erphp_promo_money1 = get_option('erphp_promo_money1');
		$erphp_promo_code2 = get_option('erphp_promo_code2');
		$erphp_promo_money2 = get_option('erphp_promo_money2');

		if($promo == $erphp_promo_code1){
			$_SESSION['erphp_promo_code'] = '{"code":"'.$promo.'","money":"'.$erphp_promo_money1.'","type":"1"}';
		    $status = 1;
		    $type = 1;
		    $money = $erphp_promo_money1;
		}elseif($promo == $erphp_promo_code2){
			$_SESSION['erphp_promo_code'] = '{"code":"'.$promo.'","money":"'.$erphp_promo_money2.'","type":"2"}';
		    $status = 1;
		    $type = 2;
		    $money = $erphp_promo_money2;
		}
	}

	$result = array(
		'status' => $status,
		'type' => $type,
		'money' => $money
	);

	header('Content-type: application/json');
	echo json_encode($result);
	exit;
}
add_action( 'wp_ajax_epd_promo', 'epd_promo_callback');
add_action( 'wp_ajax_nopriv_epd_promo', 'epd_promo_callback');

function epd_admin_search_post_callback(){
	global $wpdb;
	$post_search = $_POST['post_search'];
	$posts = array();
	$status = 0;

	if(current_user_can('administrator')){
		$args = array(
	        's' => $post_search,
	        'post_type' => 'post',
	        'showposts' => -1
	    );
		query_posts($args);
		while ( have_posts() ) : the_post(); 
			$posts[] = array('id'=>get_the_ID(),'title'=>get_the_title());
		endwhile;wp_reset_query(); 
		$status = 1;
	}
	
	$result = array(
		'status' => $status,
		'posts' => $posts
	);

	header('Content-type: application/json');
	echo json_encode($result);
	exit;
}
add_action( 'wp_ajax_epd_admin_search_post', 'epd_admin_search_post_callback');

function epd_download_html($content){
	echo $content;
	exit;
}

function erphpdown_install() {
	global $wpdb, $erphpdown_version, $wppay_table_name;
	$charset_collate = 'DEFAULT CHARACTER SET utf8mb4';

	if( $wpdb->get_var("show tables like '{$wppay_table_name}'") != $wppay_table_name ) {
		$wpdb->query("CREATE TABLE {$wppay_table_name} (
			id      BIGINT(20) NOT NULL AUTO_INCREMENT,
			order_num VARCHAR(50) NOT NULL,
			post_id BIGINT(20) NOT NULL,
			post_price double(10,2) NOT NULL,
			user_id BIGINT(20) NOT NULL DEFAULT 0,
			order_pay_num VARCHAR(100),
			order_time datetime NOT NULL,
			order_status int(1) NOT NULL DEFAULT 0,
			ip_address VARCHAR(25) NOT NULL,
			UNIQUE KEY id (id)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1");
	}

	$create_ice_alipay_sql = "CREATE TABLE $wpdb->icealipay (".
			"ice_id int(11) NOT NULL auto_increment,".
			"ice_num varchar(50) NOT NULL,".
			"ice_title varchar(100) NOT NULL,".
			"ice_post int(11) NOT NULL,".
			"ice_price double(10,2) NOT NULL,".
			"ice_url varchar(32) NOT NULL,".
			"ice_user_id int(11) NOT NULL,".
			"ice_time datetime NOT NULL,".
			"ice_data text,".
			"ice_index int(11),".
			"ice_success int(1) NOT NULL,".
			"ice_author int(11) NOT NULL,".
			"ice_aff varchar(50),".
			"ice_ip varchar(50),".
			"PRIMARY KEY (ice_id),INDEX(`ice_user_id`),INDEX(`ice_post`)) $charset_collate;";
	$wpdb->query( $create_ice_alipay_sql );

	$create_ice_index_sql = "CREATE TABLE $wpdb->iceindex (".
			"ice_id int(11) NOT NULL auto_increment,".
			"ice_num varchar(50) NOT NULL,".
			"ice_post int(11) NOT NULL,".
			"ice_price double(10,2) NOT NULL,".
			"ice_url varchar(32) NOT NULL,".
			"ice_user_id int(11) NOT NULL,".
			"ice_time datetime NOT NULL,".
			"ice_index int(11) NOT NULL,".
			"PRIMARY KEY (ice_id)) $charset_collate;";
	$wpdb->query( $create_ice_index_sql );
	
	$create_ice_money_sql="CREATE TABLE $wpdb->icemoney (".
			"ice_id int(11) NOT NULL auto_increment,".
			"ice_num varchar(50) NOT NULL,".
			"ice_money double(10,2) NOT NULL,".
			"ice_money_real double(10,2),".
			"ice_user_id int(11) NOT NULL,".
			"ice_post_id int(11),".
			"ice_post_index int(11),".
			"ice_user_type int(2),".
			"ice_time datetime NOT NULL,".
			"ice_data text,".
			"ice_success int(1) NOT NULL,".
			"ice_note varchar(50) NOT NULL,".
			"ice_note_other varchar(200),".
			"ice_success_time datetime NOT NULL,".
			"ice_alipay varchar(200) NOT NULL,".
			"ice_aff varchar(50),".
			"ice_ip varchar(50),".
			"PRIMARY KEY (ice_id), INDEX(`ice_num`)) $charset_collate;";
	$wpdb->query( $create_ice_money_sql );
	
	$create_money_info_sql="CREATE TABLE $wpdb->iceinfo (".
			"ice_id int(11) NOT NULL auto_increment,".
			"ice_have_money double(10,2) NOT NULL,".
			"ice_user_id int(11) NOT NULL,".
			"ice_get_money double(10,2) NOT NULL,".
			"ice_have_aff double(10,2) default 0,".
			"ice_get_aff double(10,2) default 0,".
			"ice_ip varchar(50),".
			"userType TINYINT(4) NOT NULL DEFAULT 0,".
			"endTime DATE NOT NULL DEFAULT '1000-01-01',".
			"PRIMARY KEY (ice_id), INDEX(`ice_user_id`)) $charset_collate;";
	$wpdb->query( $create_money_info_sql );

	$create_ice_cat_sql="CREATE TABLE $wpdb->icecat (".
			"ice_id int(11) NOT NULL auto_increment,".
			"ice_user_id int(11) NOT NULL,".
			"ice_cat_id int(11) NOT NULL default 0,".
			"userType TINYINT(4) NOT NULL DEFAULT 0,".
			"endTime DATE NOT NULL DEFAULT '1000-01-01',".
			"PRIMARY KEY (ice_id)) $charset_collate;";
	$wpdb->query( $create_ice_cat_sql );
	
	$create_get_money_sql="CREATE TABLE $wpdb->iceget (".
			"ice_id int(11) NOT NULL auto_increment,".
			"ice_alipay varchar(100) NOT NULL,".
			"ice_name varchar(30) NOT NULL,".
			"ice_user_id int(11) NOT NULL,".
			"ice_money double(10,2) NOT NULL,".
			"ice_time datetime NOT NULL,".
			"ice_success int(1) NOT NULL,".
			"ice_note varchar(50) NOT NULL,".
			"ice_success_time datetime NOT NULL,".
			"PRIMARY KEY (ice_id)) $charset_collate;";
	$wpdb->query( $create_get_money_sql );
	
	$create_ice_vip_sql = "CREATE TABLE $wpdb->vip (".
			"ice_id int(11) NOT NULL auto_increment,".
			"ice_price double(10,2) NOT NULL,".
			"ice_user_id int(11) NOT NULL,".
			"ice_user_type tinyint(4) NOT NULL default 0,".
			"ice_ip varchar(50),".
			"ice_time datetime NOT NULL,".
			"PRIMARY KEY (ice_id)) $charset_collate;";
	$wpdb->query( $create_ice_vip_sql );

	$create_ice_vipcat_sql = "CREATE TABLE $wpdb->vipcat (".
			"ice_id int(11) NOT NULL auto_increment,".
			"ice_price double(10,2) NOT NULL,".
			"ice_user_id int(11) NOT NULL,".
			"ice_user_type tinyint(4) NOT NULL default 0,".
			"ice_cat_id int(8) NOT NULL default 0,".
			"ice_time datetime NOT NULL,".
			"PRIMARY KEY (ice_id)) $charset_collate;";
	$wpdb->query( $create_ice_vipcat_sql );
	
	$create_ice_aff_sql = "CREATE TABLE $wpdb->aff (".
			"ice_id int(11) NOT NULL auto_increment,".
			"ice_price double(10,2) NOT NULL,".
			"ice_user_id int(11) NOT NULL,".
			"ice_user_id_visit int(11),".
			"ice_ip varchar(50),".
			"ice_time datetime NOT NULL,".
			"PRIMARY KEY (ice_id)) $charset_collate;";
	$wpdb->query( $create_ice_aff_sql );

	$create_ice_down_sql = "CREATE TABLE $wpdb->down (".
			"ice_id int(11) NOT NULL auto_increment,".
			"ice_user_id int(11) NOT NULL,".
			"ice_post_id int(11),".
			"ice_vip int(1) default 0,".
			"ice_type int(1) default 0,".
			"ice_ip varchar(50),".
			"ice_time datetime NOT NULL,".
			"PRIMARY KEY (ice_id), INDEX(`ice_user_id`)) $charset_collate;";
	$wpdb->query( $create_ice_down_sql );

	$create_ice_checkin_sql = "CREATE TABLE $wpdb->checkin (
	   ID int(11) NOT NULL auto_increment,
	   user_id int(11) NOT NULL,
	   credit varchar(10),
	   create_time datetime NOT NULL,
	   user_ip varchar(50),
	   PRIMARY KEY (ID),
	   INDEX(`user_id`)
	  );";
	$wpdb->query($create_ice_checkin_sql);

	$create_ice_card_sql = "CREATE TABLE $wpdb->erphpcard (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			card varchar(100),
			password varchar(100),
			uid int(10) DEFAULT '0',
			username varchar(200),
			usetime datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			status int(3) DEFAULT '0' NOT NULL,
			price double(10,2) NOT NULL,
			PRIMARY KEY (id)) $charset_collate;";
	$wpdb->query($create_ice_card_sql);

	$create_ice_vipcard_sql = "CREATE TABLE $wpdb->erphpvipcard (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			card varchar(100),
			uid int(10) DEFAULT '0',
			usetime datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			createtime datetime,
			endtime datetime,
			status int(3) DEFAULT '0' NOT NULL,
			usertype int(3) NOT NULL,
			PRIMARY KEY (id)) $charset_collate;";
	$wpdb->query($create_ice_vipcard_sql);

	$create_ice_activation_sql = "CREATE TABLE $wpdb->erphpact (
			id bigint(15) NOT NULL AUTO_INCREMENT,
			num varchar(500),
			ice_num varchar(50),
			uid int(10) DEFAULT '0',
			pid int(10) DEFAULT '0',
			usetime datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			status int(1) DEFAULT '0' NOT NULL,
			ctime datetime,
			PRIMARY KEY (id)) $charset_collate;";
	$wpdb->query($create_ice_activation_sql);

	$create_ice_moneylog_sql = "CREATE TABLE $wpdb->icelog (
			id bigint(15) NOT NULL AUTO_INCREMENT,
			user_id int(11) NOT NULL,
			ice_money double(10,2) NOT NULL,
			ice_note varchar(200),
			ice_time datetime,
			PRIMARY KEY (id)) $charset_collate;";
	$wpdb->query($create_ice_moneylog_sql);

	$create_ice_tuan_sql = "CREATE TABLE ".$wpdb->prefix."ice_tuan (".
    "ice_id int(11) NOT NULL auto_increment,".
    "ice_num varchar(50) NOT NULL,".
    "ice_post int(11) NOT NULL,".
    "ice_status int(11) NOT NULL DEFAULT 0,".
    "PRIMARY KEY (ice_id)) $charset_collate;";
    $wpdb->query( $create_ice_tuan_sql );

    $create_ice_tuanorder_sql = "CREATE TABLE ".$wpdb->prefix."ice_tuan_order (".
    "ice_id int(11) NOT NULL auto_increment,".
    "ice_num varchar(50) NOT NULL,".
    "ice_tuan_num varchar(50) NOT NULL,".
    "ice_price double(10,2) NOT NULL,".
    "ice_post int(11) NOT NULL,".
    "ice_user_id int(11) NOT NULL,".
    "ice_status int(11) NOT NULL DEFAULT 0,".
    "ice_time datetime NOT NULL,".
    "PRIMARY KEY (ice_id)) $charset_collate;";
    $wpdb->query( $create_ice_tuanorder_sql );

	$update1="ALTER TABLE `".$wpdb->users."` ADD  `father_id` INT( 10 ) NOT NULL DEFAULT  '0'";
	$wpdb->query($update1);

	$update2="ALTER TABLE `".$wpdb->users."` ADD  `reg_ip` varchar( 100 ) DEFAULT  ''";
	$wpdb->query($update2);

	$update3="ALTER TABLE `".$wpdb->icemoney."` ADD `ice_post_id` int(11), ADD `ice_user_type` int(2)";
	$wpdb->query($update3);

	$update4="ALTER TABLE `".$wpdb->icealipay."` ADD `ice_index` int(11)";
	$wpdb->query($update4);

	$update5="ALTER TABLE `".$wpdb->icemoney."` ADD `ice_post_index` int(11)";
	$wpdb->query($update5);

	$update6="ALTER TABLE `".$wpdb->down."` ADD `ice_vip` int(1) default 0";
	$wpdb->query($update6);

	$update7="ALTER TABLE `".$wpdb->icealipay."` ADD `ice_ip` varchar(50)";
	$wpdb->query($update7);

	$update8="ALTER TABLE `".$wpdb->icealipay."` ADD `ice_aff` varchar(50)";
	$wpdb->query($update8);

	$update9="ALTER TABLE `".$wpdb->icemoney."` ADD `ice_aff` varchar(50)";
	$wpdb->query($update9);

	$update10="ALTER TABLE `".$wpdb->icemoney."` ADD `ice_ip` varchar(50)";
	$wpdb->query($update10);

	$update11="ALTER TABLE `".$wpdb->icemoney."` ADD `ice_data` text";
	$wpdb->query($update11);

	$update12="ALTER TABLE `".$wpdb->checkin."` ADD `user_ip` varchar(50)";
	$wpdb->query($update12);

	$update13="ALTER TABLE `".$wpdb->erphpact."` ADD `ice_num` varchar(50)";
	$wpdb->query($update13);

	$update14="ALTER TABLE `".$wpdb->iceinfo."` ADD `ice_have_aff` double(10,2) default 0";
	$wpdb->query($update14);

	$update15="ALTER TABLE `".$wpdb->iceinfo."` ADD `ice_get_aff` double(10,2) default 0";
	$wpdb->query($update15);

	$update17="ALTER TABLE `".$wpdb->iceinfo."` ADD `ice_ip` varchar(50)";
	$wpdb->query($update17);

	$update18="ALTER TABLE `".$wpdb->vip."` ADD `ice_ip` varchar(50)";
	$wpdb->query($update18);

	$update19="ALTER TABLE `".$wpdb->icemoney."` ADD `ice_money_real` double(10,2)";
	$wpdb->query($update19);

	$update19="ALTER TABLE `".$wpdb->icemoney."` ADD `ice_note_other` varchar(200)";
	$wpdb->query($update19);

	$update20="ALTER TABLE `".$wpdb->down."` ADD `ice_type` int(1) default 0";
	$wpdb->query($update20);

	if(get_option('erphpdown_version') < 9.00){
		update_option('erphp_post_types',array('post'));
	}

	update_option( 'erphpdown_version', $erphpdown_version );
	delete_option('MBT_ERPHPDOWN_key');

	if(!wp_next_scheduled('erphpdown_vip_daily_hook')){
		wp_schedule_event(time(), 'daily', 'erphpdown_vip_daily_hook');
	}
}

function erphpdown_uninstall(){
	wp_clear_scheduled_hook('erphpdown_vip_daily_hook');
}

add_filter('cron_schedules', 'erphpdown_cron_add');
function erphpdown_cron_add($schedules){
	return array_merge($schedules, [
		'daily'	=> ['interval'=>86400,	'display'=>'每天检测VIP一次']
	]);
}

add_action( 'erphpdown_vip_daily_hook', 'erphpdown_vip_daily_function');
function erphpdown_vip_daily_function(){
	date_default_timezone_set('Asia/Shanghai');
	global $wpdb;
	$erphp_life_days = get_option('erphp_life_days');
	if($erphp_life_days){
		$wpdb->query("update $wpdb->iceinfo set userType=0,endTime='1000-01-01' where userType > 0 and STR_TO_DATE(endTime, '%Y-%m-%d') < STR_TO_DATE(NOW(), '%Y-%m-%d')");
	}else{
		$wpdb->query("update $wpdb->iceinfo set userType=0,endTime='1000-01-01' where userType > 0 and userType < 10 and STR_TO_DATE(endTime, '%Y-%m-%d') < STR_TO_DATE(NOW(), '%Y-%m-%d')");
	}
}