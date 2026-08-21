<?php
// +----------------------------------------------------------------------
// | ERPHP [ PHP DEVELOP ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.mobantu.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: mobantu <82708210@qq.com>
// +----------------------------------------------------------------------

if ( !defined('ABSPATH') ) {exit;}

add_shortcode( 'erphpdown_sc_user','erphpdown_sc_user');//整个个人中心（带左侧导航）

add_shortcode( 'erphpdown_sc_vip_page','erphpdown_sc_vip_page');//独立VIP介绍购买页（弹窗购买VIP）

add_shortcode( 'ice_purchased_goods','erphpdown_sc_order_down');//已购商品
add_shortcode( 'erphpdown_sc_order_down','erphpdown_sc_order_down');//已购商品

add_shortcode( 'ice_purchased_tuiguang','erphpdown_sc_ref');//推广用户
add_shortcode( 'erphpdown_sc_ref','erphpdown_sc_ref');//推广用户

add_shortcode( 'ice_purchased_tuiguangxiazai','erphpdown_sc_ref_down');//推广下载
add_shortcode( 'erphpdown_sc_ref_down','erphpdown_sc_ref_down');//推广下载

add_shortcode( 'ice_purchased_tuiguangvip','erphpdown_sc_ref_vip');//推广vip订单
add_shortcode( 'erphpdown_sc_ref_vip','erphpdown_sc_ref_vip');//推广vip订单

add_shortcode( 'ice_order_tracking','erphpdown_sc_sell');//销售订单
add_shortcode( 'erphpdown_sc_sell','erphpdown_sc_sell');//销售订单

add_shortcode( 'ice_my_property', 'erphpdown_sc_my' );//我的资产
add_shortcode( 'erphpdown_sc_my', 'erphpdown_sc_my' );//我的资产

add_shortcode( 'ice_recharge_money','erphpdown_sc_recharge');//充值
add_shortcode( 'erphpdown_sc_recharge','erphpdown_sc_recharge');//充值

add_shortcode( 'erphpdown_sc_recharge_card','erphpdown_sc_recharge_card');//充值卡

add_shortcode( 'erphpdown_sc_mycred','erphpdown_sc_mycred');//mycred积分兑换

add_shortcode( 'erphpdown_sc_recharges','erphpdown_sc_recharges');//充值记录

add_shortcode( 'ice_cash_application','erphpdown_sc_withdraw');//取现申请
add_shortcode( 'erphpdown_sc_withdraw','erphpdown_sc_withdraw');//取现申请

add_shortcode( 'ice_cash_application_lists','erphpdown_sc_withdraws');//取现列表
add_shortcode( 'erphpdown_sc_withdraws','erphpdown_sc_withdraws');//取现列表

add_shortcode( 'vip_tracking_lists','erphpdown_sc_order_vip');//VIP订单
add_shortcode( 'erphpdown_sc_order_vip','erphpdown_sc_order_vip');//VIP订单

add_shortcode( 'ice_vip_member_service','erphpdown_sc_vip');//余额升级VIP
add_shortcode( 'erphpdown_sc_vip','erphpdown_sc_vip');//余额升级VIP

add_shortcode( 'erphpdown_sc_vip_pay','erphpdown_sc_vip_pay');//支付升级VIP

add_shortcode( 'erphpdown_sc_vip_card','erphpdown_sc_vip_card');//VIP充值卡

add_shortcode( 'erphpdown_sc_vip_down','erphpdown_sc_vip_down');//VIP免费下载记录

add_shortcode( 'erphpdown_sc_tuan','erphpdown_sc_tuan');//团购订单 需要扩展

add_shortcode( 'erphpdown_sc_ad','erphpdown_sc_ad');//广告订单 需要扩展

add_shortcode( 'erphpdown_sc_info','erphpdown_sc_info');//个人资料

//整个个人中心
function erphpdown_sc_user(){
	if(!is_user_logged_in()){
		return '请先登录！';
	}
	global $wpdb, $current_user;
	$erphp_url_front_novip = get_option('erphp_url_front_novip');
	$erphp_url_front_notx = get_option('erphp_url_front_notx');
	$erphp_url_front_nogm = get_option('erphp_url_front_nogm');
	$erphp_url_front_notg = get_option('erphp_url_front_notg');
	$html = '<div class="erphpdown-sc-user-wrap">
			<ul class="erphpdown-sc-user-aside">';

				if(get_option('ice_ali_money_checkin')){
					if(erphpdown_check_checkin($current_user->ID)){
			      		$html .= '<li style="padding-top:0"><a href="javascript:;" class="usercheck erphpdown-sc-btn active">已签到</a></li>';
			        }else{
			      		$html .= '<li style="padding-top:0"><a href="javascript:;" class="usercheck erphpdown-sc-btn erphp-checkin">今日签到</a></li>';
			        }
			    }

				$html .= '<li '.(((isset($_GET["pd"]) && $_GET["pd"]=='money') || !isset($_GET["pd"]))?'class="active"':'').' ><a href="'.add_query_arg('pd','money',get_permalink()).'">在线充值</a></li>';
				if(!$erphp_url_front_novip) $html .= '<li '.((isset($_GET["pd"]) && $_GET["pd"]=='vip')?'class="active"':'').' ><a href="'.add_query_arg('pd','vip',get_permalink()).'">升级VIP</a></li>';
				if(function_exists('erphpad_install')){
				$html .='<li '.((isset($_GET["pd"]) && $_GET["pd"]=='ad')?'class="active"':'').'><a href="'.add_query_arg('pd','ad',get_permalink()).'">我的广告</a></li>';
				}
				if(function_exists('erphpdown_tuan_install')){
				$html .='<li '.((isset($_GET["pd"]) && $_GET["pd"]=='tuan')?'class="active"':'').'"><a href="'.add_query_arg('pd','tuan',get_permalink()).'">我的拼团</a></li>';
				}
				if(!$erphp_url_front_nogm) $html .='<li '.((isset($_GET["pd"]) && $_GET["pd"]=='cart')?'class="active"':'').' ><a href="'.add_query_arg('pd','cart',get_permalink()).'">购买清单</a></li>';
				$html .= '<li '.((isset($_GET["pd"]) && $_GET["pd"]=='recharge')?'class="active"':'').' ><a href="'.add_query_arg('pd','recharge',get_permalink()).'" >充值记录</a></li>';
				if(!$erphp_url_front_novip) $html .= '<li '.((isset($_GET["pd"]) && $_GET["pd"]=='vips')?'class="active"':'').' ><a href="'.add_query_arg('pd','vips',get_permalink()).'">VIP记录</a></li>';
				if(!$erphp_url_front_notg) $html .= '<li '.((isset($_GET["pd"]) && $_GET["pd"]=='ref')?'class="active"':'').' ><a href="'.add_query_arg('pd','ref',get_permalink()).'">推广注册</a></li>';
				if(!$erphp_url_front_novip && !$erphp_url_front_notg) $html .= '<li '.((isset($_GET["pd"]) && $_GET["pd"]=='ref2')?'class="active"':'').' ><a href="'.add_query_arg('pd','ref2',get_permalink()).'">推广VIP</a></li>';
				if(!$erphp_url_front_notx) $html .= '<li '.((isset($_GET["pd"]) && $_GET["pd"]=='outmo')?'class="active"':'').' ><a href="'.add_query_arg('pd','outmo',get_permalink()).'">申请提现</a></li>';
				$html .= '<li '.((isset($_GET["pd"]) && $_GET["pd"]=='info')?'class="active"':'').' ><a href="'.add_query_arg('pd','info',get_permalink()).'">个人资料</a></li>
				<li><a href="'.wp_logout_url(home_url()).'">安全退出</a></li>
			</ul>
			<div class="erphpdown-sc-user-content">';
				if((isset($_GET['pd']) && $_GET['pd'] == 'money') || !isset($_GET['pd'])){
					$html .= do_shortcode('[erphpdown_sc_my]');
					$html .= do_shortcode('[erphpdown_sc_recharge]');
					$html .= do_shortcode('[erphpdown_sc_recharge_card]');
					$html .= do_shortcode('[erphpdown_sc_mycred]');
				}elseif(isset($_GET['pd']) && $_GET['pd'] == 'vip'){
					$html .= do_shortcode('[erphpdown_sc_vip_pay]');
					$html .= do_shortcode('[erphpdown_sc_vip]');
					$html .= do_shortcode('[erphpdown_sc_vip_card]');
				}elseif(isset($_GET['pd']) && $_GET['pd'] == 'cart'){
					$html .= '<style>.erphpdown-sc h2{display:none}</style>';
					$html .= do_shortcode('[erphpdown_sc_order_down]');
				}elseif(isset($_GET['pd']) && $_GET['pd'] == 'recharge'){
					$html .= '<style>.erphpdown-sc h2{display:none}</style>';
					$html .= do_shortcode('[erphpdown_sc_recharges]');
				}elseif(isset($_GET['pd']) && $_GET['pd'] == 'vips'){
					$html .= '<style>.erphpdown-sc h2{display:none}</style>';
					$html .= do_shortcode('[erphpdown_sc_order_vip]');
				}elseif(isset($_GET['pd']) && $_GET['pd'] == 'ref'){
					$html .= '<style>.erphpdown-sc h2{display:none}</style>';
					$html .= do_shortcode('[erphpdown_sc_ref]');
				}elseif(isset($_GET['pd']) && $_GET['pd'] == 'ref2'){
					$html .= '<style>.erphpdown-sc h2{display:none}</style>';
					$html .= do_shortcode('[erphpdown_sc_ref_vip]');
				}elseif(isset($_GET['pd']) && $_GET['pd'] == 'outmo'){
					$html .= '<style>.erphpdown-sc h2{display:none}</style>';
					$html .= do_shortcode('[erphpdown_sc_withdraw]');
					$html .= do_shortcode('[erphpdown_sc_withdraws]');
				}elseif(isset($_GET['pd']) && $_GET['pd'] == 'tuan'){
					$html .= '<style>.erphpdown-sc h2{display:none}</style>';
					$html .= do_shortcode('[erphpdown_sc_tuan]');
				}elseif(isset($_GET['pd']) && $_GET['pd'] == 'ad'){
					$html .= '<style>.erphpdown-sc h2{display:none}</style>';
					$html .= do_shortcode('[erphpdown_sc_ad]');
				}elseif(isset($_GET['pd']) && $_GET['pd'] == 'info'){
					$html .= '<style>.erphpdown-sc h2{display:none}</style>';
					$html .= do_shortcode('[erphpdown_sc_info]');
				}
			$html .= '</div>
		</div>';
	return $html;
}

//VIP介绍
function erphpdown_sc_vip_page(){
	$erphp_quarter_price = get_option('erphp_quarter_price');
	$erphp_month_price  = get_option('erphp_month_price');
	$erphp_day_price  = get_option('erphp_day_price');
	$erphp_year_price    = get_option('erphp_year_price');
	$erphp_life_price  = get_option('erphp_life_price');

	$erphp_life_days    = get_option('erphp_life_days');
	$erphp_year_days    = get_option('erphp_year_days');
	$erphp_quarter_days = get_option('erphp_quarter_days');
	$erphp_month_days  = get_option('erphp_month_days');
	$erphp_day_days  = get_option('erphp_day_days');

	$erphp_life_name    = get_option('erphp_life_name')?get_option('erphp_life_name'):'终身VIP';
	$erphp_year_name    = get_option('erphp_year_name')?get_option('erphp_year_name'):'包年VIP';
	$erphp_quarter_name = get_option('erphp_quarter_name')?get_option('erphp_quarter_name'):'包季VIP';
	$erphp_month_name  = get_option('erphp_month_name')?get_option('erphp_month_name'):'包月VIP';
	$erphp_day_name  = get_option('erphp_day_name')?get_option('erphp_day_name'):'体验VIP';
	$erphp_vip_name  = get_option('erphp_vip_name')?get_option('erphp_vip_name'):'VIP';
	$moneyVipName = get_option('ice_name_alipay');
	$erphp_url_front_login = wp_login_url(get_permalink());
	if(get_option('erphp_url_front_login')){
		$erphp_url_front_login = get_option('erphp_url_front_login');
	}

	$html = '<div class="erphpdown-vip-content clearfix">';
			if($erphp_day_price){
            $html .= '<div class="vip-item">
                <div class="name">'.$erphp_day_name.'</div>
                <span class="price">'.$erphp_day_price.'<small>'.$moneyVipName.'</small></span>
                <p class="border-decor"><span>'.($erphp_day_days?$erphp_day_days:'1').'天</span></p>';
                if(is_user_logged_in()){
                $html .= '<a href="javascript:;" data-type="6" class="erphpdown-btn erphpdown-vip-do">立即升级</a>';
            	}else{
            	$html .= '<a href="'.$erphp_url_front_login.'" class="erphpdown-btn erphp-login-must">立即升级</a>';
            	}
            $html .= '</div>';
        	}

        	if($erphp_month_price){
            $html .= '<div class="vip-item">
                <div class="name">'.$erphp_month_name.'</div>
                <span class="price">'.$erphp_month_price.'<small>'.$moneyVipName.'</small></span>
                <p class="border-decor"><span>'.($erphp_month_days?$erphp_month_days:'30').'天</span></p>';
                if(is_user_logged_in()){
                $html .= '<a href="javascript:;" data-type="7" class="erphpdown-btn erphpdown-vip-do">立即升级</a>';
            	}else{
            	$html .= '<a href="'.$erphp_url_front_login.'" class="erphpdown-btn erphp-login-must">立即升级</a>';
            	}
            $html .= '</div>';
        	}

        	if($erphp_quarter_price){
            $html .= '<div class="vip-item">
                <div class="name">'.$erphp_quarter_name.'</div>
                <span class="price">'.$erphp_quarter_price.'<small>'.$moneyVipName.'</small></span>
                <p class="border-decor"><span>'.($erphp_quarter_days?$erphp_quarter_days:'3').'个月</span></p>';
                if(is_user_logged_in()){
                $html .= '<a href="javascript:;" data-type="8" class="erphpdown-btn erphpdown-vip-do">立即升级</a>';
            	}else{
            	$html .= '<a href="'.$erphp_url_front_login.'" class="erphpdown-btn erphp-login-must">立即升级</a>';
            	}
            $html .= '</div>';
        	}

        	if($erphp_year_price){
            $html .= '<div class="vip-item">
                <div class="name">'.$erphp_year_name.'</div>
                <span class="price">'.$erphp_year_price.'<small>'.$moneyVipName.'</small></span>
                <p class="border-decor"><span>'.($erphp_year_days?$erphp_year_days:'12').'个月</span></p>';
                if(is_user_logged_in()){
                $html .= '<a href="javascript:;" data-type="9" class="erphpdown-btn erphpdown-vip-do">立即升级</a>';
            	}else{
            	$html .= '<a href="'.$erphp_url_front_login.'" class="erphpdown-btn erphp-login-must">立即升级</a>';
            	}
            $html .= '</div>';
        	}

        	if($erphp_life_price){
            $html .= '<div class="vip-item">
                <div class="name">'.$erphp_life_name.'</div>
                <span class="price">'.$erphp_life_price.'<small>'.$moneyVipName.'</small></span>
                <p class="border-decor"><span>'.($erphp_life_days?$erphp_life_days.'年':'永久').'</span></p>';
                if(is_user_logged_in()){
                $html .= '<a href="javascript:;" data-type="10" class="erphpdown-btn erphpdown-vip-do">立即升级</a>';
            	}else{
            	$html .= '<a href="'.$erphp_url_front_login.'" class="erphpdown-btn erphp-login-must">立即升级</a>';
            	}
            $html .= '</div>';
        	}

        $html .= '</div>';
    return $html;
}

//个人资料
function erphpdown_sc_info(){
	if(!is_user_logged_in()){
		return '请先登录！';
	}
	global $wpdb, $current_user;
	$html = '<div class="erphpdown-sc">
		<h2>'.__('个人资料','erphpdown').'</h2>
		<form method="post"><table class="erphpdown-sc-table">
			<tr>
				<td valign="top" class="erphpdown-sc-td-title">'.__('用户名','erphpdown').'</td>
				<td>
				'.$current_user->user_login.'
				</td>
			</tr>
			<tr>
				<td valign="top" class="erphpdown-sc-td-title">'.__('注册时间','erphpdown').'</td>
				<td>
				'.get_date_from_gmt( $current_user->user_registered ).'
				</td>
			</tr>
			<tr>
				<td valign="top" class="erphpdown-sc-td-title">'.__('昵称','erphpdown').'</td>
				<td>
				<input type="text" id="mm_name" name="mm_name" class="erphpdown-sc-input" value="'.esc_attr( $current_user->nickname ).'" required />
				</td>
			</tr>
			<tr>
				<td valign="top" class="erphpdown-sc-td-title">'.__('邮箱','erphpdown').'</td>
				<td>
				<input type="text" id="mm_mail" name="mm_mail" class="erphpdown-sc-input" value="'.esc_attr( $current_user->user_email ).'" required />
				</td>
			</tr>
			<tr>
				<td valign="top" class="erphpdown-sc-td-title">'.__('简介','erphpdown').'</td>
				<td>
				<textarea type="text" id="mm_desc" name="mm_desc" class="erphpdown-sc-input">'.esc_html( $current_user->description ).'</textarea>
				</td>
			</tr>
			<tr>
				<td colspan="2"><input type="button" value="'.__('修改资料','erphpdown').'" class="erphpdown-sc-btn erphpdown-sc-info-do" /></td>
			</tr> 
			<tr>
				<td valign="top" class="erphpdown-sc-td-title">'.__('新密码','erphpdown').'</td>
				<td>
				<input type="password" id="mm_pass_new" name="mm_pass_new" class="erphpdown-sc-input" required />
				</td>
			</tr>
			<tr>
				<td valign="top" class="erphpdown-sc-td-title">'.__('重复新密码','erphpdown').'</td>
				<td>
				<input type="password" id="mm_pass_new2" name="mm_pass_new2" class="erphpdown-sc-input" required />
				</td>
			</tr>
			<tr>
				<td colspan="2"><input type="button" value="'.__('修改密码','erphpdown').'" class="erphpdown-sc-btn erphpdown-sc-pass-do" /></td>
			</tr>
		</table>
	</form>
	<script type="text/javascript">
		jQuery(document).ready(function($){
		
			$(".erphpdown-sc-info-do").click(function(){ 
				var reg = /^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/ ;
				if($("#mm_name").val().trim().length==0)
				{
					layer.msg("'.__('请输入昵称','erphpdown').'");
				}
				else if(!reg.test($("#mm_mail").val().trim()))
				{
					layer.msg("'.__('请输入正确邮箱，以免忘记密码时无法找回','erphpdown').'");
				}
				else
				{
					layer.msg("'.__('修改中...','erphpdown').'");
					$.ajax({
						type: "post",
						url: "'.ERPHPDOWN_URL.'/admin/action/ajax-profile.php",
						data: "do=profile&mm_name=" + $("#mm_name").val() + "&mm_mail=" + $("#mm_mail").val() + "&mm_desc=" + $("#mm_desc").val(),
						dataType: "text",
						success: function (data) {
							layer.msg("'.__('修改成功','erphpdown').'");
						},
						error: function () {
							layer.msg("'.__('系统超时，请稍后重试','erphpdown').'");
						}
					});
				}
			});

			$(".erphpdown-sc-pass-do").click(function(){ 
				if($("#mm_pass_new").val().trim().length==0)
				{
					layer.msg("'.__('请输入密码','erphpdown').'");
				}
				else if($("#mm_pass_new2").val().trim() != $("#mm_pass_new").val().trim())
				{
					layer.msg("'.__('两次输入密码不一样','erphpdown').'");
				}
				else
				{
					layer.msg("'.__('修改中...','erphpdown').'");
					$.ajax({
						type: "post",
						url: "'.ERPHPDOWN_URL.'/admin/action/ajax-profile.php",
						data: "do=password&mm_pass_new=" + $("#mm_pass_new").val() + "&mm_pass_new2=" + $("#mm_pass_new2").val(),
						dataType: "text",
						success: function (data) {
							layer.msg("'.__('修改成功','erphpdown').'");
						},
						error: function () {
							layer.msg("'.__('系统超时，请稍后重试','erphpdown').'");
						}
					});
				}
			});
		
		});
	</script>
	</div>';
	return $html;
}

//我的资产
function erphpdown_sc_my() {
	if(!is_user_logged_in()){
		return '请先登录！';
	}
	global $wpdb, $current_user;
	$erphp_aff_money = get_option('erphp_aff_money');
	$money_name = get_option('ice_name_alipay');
	$userMoney=$wpdb->get_row("select * from ".$wpdb->iceinfo." where ice_user_id=".$current_user->ID);
	if(!$userMoney){
		$okMoney=0;
	}else {
		$okMoney=$userMoney->ice_have_money - $userMoney->ice_get_money;
	}
	
	$html = '<div class="erphpdown-sc">
		<h2>'.__('我的资产','erphpdown').'</h2>
		<table class="erphpdown-sc-table">';
			if($erphp_aff_money){
			$html .= '<tr>
				<td valign="top" class="erphpdown-sc-td-title">'.__('收入+推广','erphpdown').'</td>
				<td>'.
					($userMoney? sprintf("%.2f",$userMoney->ice_have_aff) : '0').$money_name.
				'</td>
			</tr>
			<tr>
				<td valign="top" class="erphpdown-sc-td-title">'.__('可提现余额','erphpdown').'</td>
				<td>'.
					($userMoney? sprintf("%.2f",($userMoney->ice_have_aff - $userMoney->ice_get_aff)): '0').$money_name.
				'</td>
			</tr>';
			}
			$html .= '
			<tr>
				<td valign="top" class="erphpdown-sc-td-title">'.__('可用余额','erphpdown').'</td>
				<td>'.
					sprintf("%.2f",$okMoney).$money_name.
				'</td>
			</tr>
		</table>
	</div>';
	return $html;
}

//已购商品
function erphpdown_sc_order_down() {
	if(!is_user_logged_in()){
		return '请先登录！';
	}

	global $wpdb, $current_user;
	$total_trade   = $wpdb->get_var("SELECT COUNT(ice_id) FROM $wpdb->icealipay WHERE ice_success>0 and ice_user_id=".$current_user->ID);
	$ice_perpage = 20;
	$pages = ceil($total_trade / $ice_perpage);
	$page=isset($_GET['pp']) ?intval($_GET['pp']) :1;
	$offset = $ice_perpage*($page-1);
	$list = $wpdb->get_results("SELECT * FROM $wpdb->icealipay where ice_success=1 and ice_user_id=$current_user->ID order by ice_time DESC limit $offset,$ice_perpage");

	$html = '<div class="erphpdown-sc">
		<h2>'.__('购买清单','erphpdown').'</h2>
		<table class="erphpdown-sc-list">
			<thead>
				<tr>
					<th>'.__('名称','erphpdown').'</th>
					<th>'.__('订单号','erphpdown').'</th>
					<th>'.__('价格','erphpdown').'</th>
					<th>'.__('时间','erphpdown').'</th>
					<th>'.__('操作','erphpdown').'</th>
				</tr>
			</thead>
			<tbody>';
			if($list){
				foreach($list as $value){
					$start_down = get_post_meta( $value->ice_post, 'start_down', true );
					$start_down2 = get_post_meta( $value->ice_post, 'start_down2', true );
					$html .= "<tr>\n";
					$html .= "<td class='tit'><a href='".get_permalink($value->ice_post)."' target='_blank'>".get_post($value->ice_post)->post_title."</a></td>\n";
					$html .= "<td>$value->ice_num</td>";
					$html .= "<td>$value->ice_price</td>\n";
					$html .= "<td>$value->ice_time</td>\n";
					if($start_down || $start_down2){
						$html .= '<td><a href="'.constant("erphpdown").'download.php?postid='.$value->ice_post.'&index='.$value->ice_index.'&timestamp='.time().'" target="_blank">'.__('下载','erphpdown').'</a></td>';
					}else{
						$html .= '<td><a href="'.get_permalink($value->ice_post).'" target="_blank">'.__('查看','erphpdown').'</a></td>';
					}
					$html .= "</tr>";
				}
			}else{
				$html .= '<tr><td colspan="5" align="center">'.__('暂无记录','erphpdown').'</td></tr>';
			}
	$html .=	'</tbody>
		</table>'.erphpdown_paging($page,$pages,'cart').'
	</div>';
 	return $html;
}

//我的推广
function erphpdown_sc_ref() { 
	if(!is_user_logged_in()){
		return '请先登录！';
	}

	global $wpdb, $current_user;
	$total_user   = $wpdb->get_var("SELECT COUNT(ID) FROM $wpdb->users WHERE father_id=".$current_user->ID);
	$ice_perpage = 20;
	$pages = ceil($total_user / $ice_perpage);
	$page=isset($_GET['pp']) ?intval($_GET['pp']) :1;
	$offset = $ice_perpage*($page-1);
	$list = $wpdb->get_results("SELECT ID,user_login,user_registered FROM $wpdb->users where father_id=".$current_user->ID." limit $offset,$ice_perpage");
	$html = '<div class="erphpdown-sc">
		<h2>'.__('推广用户','erphpdown').'</h2>
		<p>'.sprintf(__('已推广注册 %s 人','erphpdown'), $total_user).__('，您的专属推广链接：','erphpdown').esc_url( home_url( '/?aff=' ) ).$current_user->ID.'</p>
		<table class="erphpdown-sc-list">
			<thead>
				<tr>
					<th>'.__('用户','erphpdown').'</th>
					<th>'.__('注册时间','erphpdown').'</th>	    
					<th>'.__('消费金额','erphpdown').'</th>	    
				</tr>
			</thead>
			<tbody>';
			if($list) {
				foreach($list as $value){
					$html .= "<tr>\n";
					$html .= "<td>".$value->user_login."</td>";
					$html .= "<td>".get_date_from_gmt($value->user_registered)."</td>";
					$html .= "<td>".erphpGetUserAllXiaofei($value->ID)."</td>";
					$html .= "</tr>";
				}
			}else{
				$html .= '<tr><td colspan="3" align="center">'.__('暂无记录','erphpdown').'</td></tr>';
			}
	$html .='</tbody>
		</table>'.erphpdown_paging($page,$pages,'ref').'
	</div>';
	return $html;
}


//推广下载
function erphpdown_sc_ref_down() { 
	if(!is_user_logged_in()){
		return '请先登录！';
	}

	global $wpdb, $current_user;
	$total_trade   = $wpdb->get_var("SELECT COUNT(ice_id) FROM $wpdb->icealipay WHERE ice_success>0 and ice_user_id in (select ID from $wpdb->users where father_id=".$current_user->ID.")");
	$ice_perpage = 20;
	$pages = ceil($total_trade / $ice_perpage);
	$page=isset($_GET['pp']) ?intval($_GET['pp']) :1;
	$offset = $ice_perpage*($page-1);
	$list = $wpdb->get_results("SELECT * FROM $wpdb->icealipay where ice_success=1 and ice_user_id in (select ID from $wpdb->users where father_id=".$current_user->ID.") order by ice_time DESC limit $offset,$ice_perpage");
	
	$html = '<div class="erphpdown-sc">
		<h2>'.__('推广订单','erphpdown').'</h2>
		<table class="erphpdown-sc-list">
			<thead>
				<tr>
					<th>'.__('名称','erphpdown').'</th>
					<th>'.__('用户','erphpdown').'</th>
					<th>'.__('订单号','erphpdown').'</th>
					<th>'.__('价格','erphpdown').'</th>
					<th>'.__('时间','erphpdown').'</th>		
				</tr>
			</thead>
			<tbody>';
			if($list) {
				foreach($list as $value){
					$html .= "<tr>\n";
					$html .= "<td class='tit'><a href='".get_permalink($value->ice_post)."' target='_blank'>".get_post($value->ice_post)->post_title."</a></td>\n";
					$html .= "<td>".get_the_author_meta( 'user_login', $value->ice_user_id )."</td>";
					$html .= "<td>$value->ice_num</td>";
					$html .= "<td>$value->ice_price</td>\n";
					$html .= "<td>$value->ice_time</td>\n";
					$html .= "</tr>";
				}
			}else{
				$html .= '<tr><td colspan="5" align="center">'.__('暂无记录','erphpdown').'</td></tr>';
			}
		$html .='</tbody>
		</table>'.erphpdown_paging($page,$pages,'ref3').'
	</div>';
	return $html;
}


//推广VIP订单
function erphpdown_sc_ref_vip() { 
	if(!is_user_logged_in()){
		return '请先登录！';
	}
	global $wpdb, $current_user;

	$erphp_life_name    = get_option('erphp_life_name')?get_option('erphp_life_name'):'终身VIP';
	$erphp_year_name    = get_option('erphp_year_name')?get_option('erphp_year_name'):'包年VIP';
	$erphp_quarter_name = get_option('erphp_quarter_name')?get_option('erphp_quarter_name'):'包季VIP';
	$erphp_month_name  = get_option('erphp_month_name')?get_option('erphp_month_name'):'包月VIP';
	$erphp_day_name  = get_option('erphp_day_name')?get_option('erphp_day_name'):'体验VIP';

	$total_trade   = $wpdb->get_var("SELECT COUNT(ice_id) FROM $wpdb->vip where ice_user_id in (select ID from $wpdb->users where father_id=".$current_user->ID.")");
	$ice_perpage = 20;
	$pages = ceil($total_trade / $ice_perpage);
	$page=isset($_GET['pp']) ?intval($_GET['pp']) :1;
	$offset = $ice_perpage*($page-1);
	$list = $wpdb->get_results("SELECT * FROM $wpdb->vip where ice_user_id in (select ID from $wpdb->users where father_id=".$current_user->ID.") order by ice_time DESC limit $offset,$ice_perpage");
	$html = '<div class="erphpdown-sc">
		<h2>'.__('推广VIP','erphpdown').'</h2>
		<p>'.sprintf(__('已推广VIP %s 单','erphpdown'), $total_trade).__('，您的专属推广链接：','erphpdown').esc_url( home_url( '/?aff=' ) ).$current_user->ID.'</p>
		<table class="erphpdown-sc-list">
			<thead>
				<tr>
					<th>'.__('用户','erphpdown').'</th>
					<th>'.__('VIP类型','erphpdown').'</th>
					<th>'.__('价格','erphpdown').'</th>
					<th>'.__('时间','erphpdown').'</th>			
				</tr>
			</thead>
			<tbody>';
			if($list) {
				foreach($list as $value){
					if($value->ice_user_type == 6) $typeName = $erphp_day_name;
					else {$typeName=$value->ice_user_type==7 ?$erphp_month_name :($value->ice_user_type==8 ?$erphp_quarter_name : ($value->ice_user_type==10 ?$erphp_life_name : $erphp_year_name));}
					$html .= "<tr>\n";
					$html .= "<td>".get_the_author_meta( 'user_login', $value->ice_user_id )."</td>\n";
					$html .= "<td>$typeName</td>\n";
					$html .= "<td>$value->ice_price</td>\n";
					$html .= "<td>$value->ice_time</td>\n";
					$html .= "</tr>";
				}
			}else{
				$html .= '<tr><td colspan="4" align="center">'.__('暂无记录','erphpdown').'</td></tr>';
			}
	$html .= '</tbody>
		</table>'.erphpdown_paging($page,$pages,'ref2').'
	</div>';
	return $html;
}

//销售订单
function erphpdown_sc_sell() {
	if(!is_user_logged_in()){
		return '请先登录！';
	}

	global $wpdb, $current_user;
	$total_trade   = $wpdb->get_var("SELECT COUNT(ice_id) FROM $wpdb->icealipay where ice_author=".$current_user->ID);
	$ice_perpage = 20;
	$pages = ceil($total_trade / $ice_perpage);
	$page=isset($_GET['pp']) ?intval($_GET['pp']) :1;
	$offset = $ice_perpage*($page-1);
	$list = $wpdb->get_results("SELECT * FROM $wpdb->icealipay where ice_author= ".$current_user->ID." order by ice_time DESC limit $offset,$ice_perpage");
	$html = '<div class="erphpdown-sc">
    	<h2>'.__('销售订单','erphpdown').'</h2>
      <table class="erphpdown-sc-list">
        <thead>
          <tr>
          	<th>'.__('名称','erphpdown').'</th>
          	<th>'.__('订单号','erphpdown').'</th>
            <th>'.__('用户','erphpdown').'</th>
            <th>'.__('价格','erphpdown').'</th>
            <th>'.__('时间','erphpdown').'</th>
          </tr>
        </thead>
        <tbody>';
          if($list) {
              foreach($list as $value)
              {
                  $html .= "<tr>\n";
                  $html .= "<td class='tit'><a href='".get_permalink($value->ice_post)."' target='_blank'>".$value->ice_title."</a></td>\n";
                  $html .= "<td>$value->ice_num</td>\n";
                  $html .= "<td>".get_the_author_meta( 'user_login', $value->ice_user_id )."</td>";
                  $html .= "<td>$value->ice_price</td>\n";
                  $html .= "<td>$value->ice_time</td>\n";
                  $html .= "</tr>";
              }
          }
          else
          {
              $html .= '<tr><td colspan="5" align="center">'.__('暂无记录','erphpdown').'</td></tr>';
          }
        $html .= '</tbody>
      </table>'.erphpdown_paging($page,$pages,'sell').'
    </div>';
  return $html;  
}


//vip订单
function erphpdown_sc_order_vip() {
	if(!is_user_logged_in()){
		return '请先登录！';
	}
	global $wpdb, $current_user;
	$erphp_life_name    = get_option('erphp_life_name')?get_option('erphp_life_name'):'终身VIP';
	$erphp_year_name    = get_option('erphp_year_name')?get_option('erphp_year_name'):'包年VIP';
	$erphp_quarter_name = get_option('erphp_quarter_name')?get_option('erphp_quarter_name'):'包季VIP';
	$erphp_month_name  = get_option('erphp_month_name')?get_option('erphp_month_name'):'包月VIP';
	$erphp_day_name  = get_option('erphp_day_name')?get_option('erphp_day_name'):'体验VIP';
	$total_trade   = $wpdb->get_var("SELECT COUNT(ice_id) FROM $wpdb->vip where ice_user_id=".$current_user->ID);
	$ice_perpage = 20;
	$pages = ceil($total_trade / $ice_perpage);
	$page=isset($_GET['pp']) ?intval($_GET['pp']) :1;
	$offset = $ice_perpage*($page-1);
	$list = $wpdb->get_results("SELECT * FROM $wpdb->vip where ice_user_id=".$current_user->ID." order by ice_time DESC limit $offset,$ice_perpage");
	$html = '<div class="erphpdown-sc">
		<h2>'.__('VIP订单','erphpdown').'</h2>
		<table class="erphpdown-sc-list">
			<thead>
				<tr>
					<th>'.__('VIP类型','erphpdown').'</th>
					<th>'.__('价格','erphpdown').'</th>
					<th>'.__('时间','erphpdown').'</th>			
				</tr>
			</thead>
			<tbody>';
			if($list) {
				foreach($list as $value){
					if($value->ice_user_type == 6) $typeName = $erphp_day_name;
					else {$typeName=$value->ice_user_type==7 ?$erphp_month_name :($value->ice_user_type==8 ?$erphp_quarter_name : ($value->ice_user_type==10 ?$erphp_life_name : $erphp_year_name));}
					$html .= "<tr>\n";
					$html .= "<td>$typeName</td>\n";
					$html .= "<td>$value->ice_price</td>\n";
					$html .= "<td>$value->ice_time</td>\n";
					$html .= "</tr>";
				}
			}else{
				$html .= '<tr><td colspan="3" align="center">'.__('暂无记录','erphpdown').'</td></tr>';
			}
	$html .= '</tbody>
		</table>'.erphpdown_paging($page,$pages,'vips').'
	</div>';
	return $html;
}

//充值记录
function erphpdown_sc_recharges() {
	if(!is_user_logged_in()){
		return '请先登录！';
	}
	global $wpdb, $current_user;
	$total_trade   = $wpdb->get_var("SELECT COUNT(ice_id) FROM $wpdb->icemoney where ice_user_id=".$current_user->ID." and ice_success=1");
	$ice_perpage = 20;
	$pages = ceil($total_trade / $ice_perpage);
	$page=isset($_GET['pp']) ?intval($_GET['pp']) :1;
	$offset = $ice_perpage*($page-1);
	$list = $wpdb->get_results("SELECT * FROM $wpdb->icemoney where ice_user_id=".$current_user->ID." and ice_success=1 order by ice_time DESC limit $offset,$ice_perpage");
	$html = '<div class="erphpdown-sc">
		<h2>'.__('充值记录','erphpdown').'</h2>
		<table class="erphpdown-sc-list">
			<thead>
				<tr>
					<th>'.__('金额','erphpdown').'</th>
					<th>'.__('方式','erphpdown').'</th>
					<th>'.__('时间','erphpdown').'</th>			
				</tr>
			</thead>
			<tbody>';
			if($list) {
				foreach($list as $value){
					$html .= "<tr>\n";
					$html .= "<td>$value->ice_money</td>\n";
					if(intval($value->ice_note)==0){
						$html .= "<td>".__('在线充值','erphpdown')."</td>\n";
					}elseif(intval($value->ice_note)==1){
						$html .= "<td>".__('后台充值','erphpdown')."</td>\n";
					}elseif(intval($value->ice_note)==4){
						$html .= "<td>".__('积分兑换','erphpdown')."</td>\n";
					}elseif(intval($value->ice_note)==6){
						$html .= "<td>".__('充值卡','erphpdown')."</td>\n";
					}else{
						$html .= "<td>".__('未知','erphpdown')."</td>\n";
					}
					$html .= "<td>$value->ice_time</td>\n";
					$html .= "</tr>";
				}
			}else{
				$html .= '<tr><td colspan="3" align="center">'.__('暂无记录','erphpdown').'</td></tr>';
			}
	$html .= '</tbody>
		</table>'.erphpdown_paging($page,$pages,'recharge').'
	</div>';
	return $html;
}

//升级VIP
function erphpdown_sc_vip() {
	if(!is_user_logged_in()){
		return '请先登录！';
	}
	global $wpdb, $current_user;
	$vip_update_pay = get_option('vip_update_pay');
	$error = '';
	$html = '<div class="erphpdown-sc">';

	if(isset($_POST['userType'])){
		$userType=isset($_POST['userType']) && is_numeric($_POST['userType']) ?intval($_POST['userType']) :0;
		$userType = esc_sql($userType);
		if($userType >5 && $userType < 11){
			$okMoney=erphpGetUserOkMoney();
			$priceArr=array('6'=>'erphp_day_price','7'=>'erphp_month_price','8'=>'erphp_quarter_price','9'=>'erphp_year_price','10'=>'erphp_life_price');
			$priceType=$priceArr[$userType];
			$price=get_option($priceType);

			$oldUserType = getUsreMemberTypeById($current_user->ID);
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
		                if(!$start_down2){
		                    $promo_money = $promo_money / get_option("ice_proportion_alipay");
		                }
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
				$error = '<div class="error">'.__("价格错误",'erphpdown').'</div>';
			}elseif($okMoney < $price){
				$error = '<div class="error">'.__("余额不足",'erphpdown').'</div>';
			}elseif($okMoney >=$price){
				if(erphpSetUserMoneyXiaoFei($price)){
					if(userPayMemberSetData($userType)){
						addVipLog($price, $userType);
						$EPD = new EPD();
						$EPD->doAff($price, $current_user->ID);
						$error = '<div class="updated">'.__("升级成功",'erphpdown').'</div>';
					}else{
						$error = '<div class="error">'.__("系统超时，请稍后重试",'erphpdown').'</div>';
					}
				}else{
					$error = '<div class="error">'.__("系统超时，请稍后重试",'erphpdown').'</div>';
				}
			}else{
				$error = '<div class="error">'.__("系统超时，请稍后重试",'erphpdown').'</div>';
			}
		}else{
			$error = '<div class="error">'.__("VIP类型错误",'erphpdown').'</div>';
		}
	}

	$erphp_life_price    = get_option('erphp_life_price');
	$erphp_year_price    = get_option('erphp_year_price');
	$erphp_quarter_price = get_option('erphp_quarter_price');
	$erphp_month_price  = get_option('erphp_month_price');
	$erphp_day_price  = get_option('erphp_day_price');
	$erphp_life_name    = get_option('erphp_life_name')?get_option('erphp_life_name'):'终身VIP';
	$erphp_year_name    = get_option('erphp_year_name')?get_option('erphp_year_name'):'包年VIP';
	$erphp_quarter_name = get_option('erphp_quarter_name')?get_option('erphp_quarter_name'):'包季VIP';
	$erphp_month_name  = get_option('erphp_month_name')?get_option('erphp_month_name'):'包月VIP';
	$erphp_day_name  = get_option('erphp_day_name')?get_option('erphp_day_name'):'体验VIP';
	$erphp_life_days    = get_option('erphp_life_days');
	$erphp_year_days    = get_option('erphp_year_days');
	$erphp_quarter_days = get_option('erphp_quarter_days');
	$erphp_month_days  = get_option('erphp_month_days');
	$erphp_day_days  = get_option('erphp_day_days');
	$money_name = get_option('ice_name_alipay');
	$okMoney=erphpGetUserOkMoney();
	$userTypeId=getUsreMemberType();
	$html .= '<h2>'.__('升级VIP','erphpdown').'<span>'.__('余额','erphpdown').'</span></h2>'.$error.
		'<p>'.__("当前类型：",'erphpdown');
		if($userTypeId==6)
		{
			$html .= $erphp_day_name;
		}
		elseif($userTypeId==7)
		{
			$html .= $erphp_month_name;
		}
		elseif ($userTypeId==8)
		{
			$html .= $erphp_quarter_name;
		}
		elseif ($userTypeId==9)
		{
			$html .= $erphp_year_name;
		}
		elseif ($userTypeId==10)
		{
			$html .= $erphp_life_name;
		}
		else 
		{
			$html .= __('普通用户','erphpdown');
		}
		if($userTypeId>5 && $userTypeId<10) $html .= __('，到期时间：','erphpdown').getUsreMemberTypeEndTime();
		$html .= '</p>
		<form method="post">
			<table class="erphpdown-sc-table">
			<tr>
				<td valign="top" class="erphpdown-sc-td-title">'.__('VIP类型','erphpdown').'</td>
				<td>';
					if($erphp_life_price){
						$old_price = '';
						if($vip_update_pay){
	                    	if($userTypeId == 6 && $erphp_day_price){
	                    		$old_price .= '<del>'.$erphp_life_price.'</del>';
	                     		$old_price .= $erphp_life_price - $erphp_day_price;
	                     	}elseif($userTypeId == 7 && $erphp_month_price){
	                     		$old_price .= '<del>'.$erphp_life_price.'</del>';
	                     		$old_price .= $erphp_life_price - $erphp_month_price;
	                     	}elseif($userTypeId == 8 && $erphp_quarter_price){
	                     		$old_price .= '<del>'.$erphp_life_price.'</del>';
	                     		$old_price .= $erphp_life_price - $erphp_quarter_price;
	                     	}elseif($userTypeId == 9 && $erphp_year_price){
	                     		$old_price .= '<del>'.$erphp_life_price.'</del>';
	                     		$old_price .= $erphp_life_price - $erphp_year_price;
	                     	}else{
	                     		$old_price .= $erphp_life_price;
	                     	}
	                    }else{
	                    	$old_price .= $erphp_life_price;
	                    }
						$html .= '<input type="radio" name="userType" value="10" checked /> '.$erphp_life_name.' --- '.$old_price.$money_name;
						if($erphp_life_days) $html .= '（'.$erphp_life_days.'年）<br />'; else $html .= '<br />';
					}
					if($erphp_year_price){
						$old_price = '';
						if($vip_update_pay){
	                    	if($userTypeId == 6 && $erphp_day_price){
	                    		$old_price .= '<del>'.$erphp_year_price.'</del>';
	                     		$old_price .= $erphp_year_price - $erphp_day_price;
	                     	}elseif($userTypeId == 7 && $erphp_month_price){
	                     		$old_price .= '<del>'.$erphp_year_price.'</del>';
	                     		$old_price .= $erphp_year_price - $erphp_month_price;
	                     	}elseif($userTypeId == 8 && $erphp_quarter_price){
	                     		$old_price .= '<del>'.$erphp_year_price.'</del>';
	                     		$old_price .= $erphp_year_price - $erphp_quarter_price;
	                     	}else{
	                     		$old_price .= $erphp_year_price;
	                     	}
	                    }else{
	                     	$old_price .= $erphp_year_price;
	                    }
						$html .= '<input type="radio" name="userType" value="9" checked/> '.$erphp_year_name.' --- '.$old_price.$money_name;
						if($erphp_year_days) $html .= '（'.$erphp_year_days.'个月）<br />';else $html .= '<br />';
					}
					if($erphp_quarter_price){
						$old_price = '';
						if($vip_update_pay){
	                    	if($userTypeId == 6 && $erphp_day_price){
	                    		$old_price .= '<del>'.$erphp_quarter_price.'</del>';
	                     		$old_price .= $erphp_quarter_price - $erphp_day_price;
	                     	}elseif($userTypeId == 7 && $erphp_month_price){
	                     		$old_price .= '<del>'.$erphp_quarter_price.'</del>';
	                     		$old_price .= $erphp_quarter_price - $erphp_month_price;
	                     	}else{
	                     		$old_price .= $erphp_quarter_price;
	                     	}
	                    }else{
	                     	$old_price .= $erphp_quarter_price;
	                    }
						$html .= '<input type="radio" name="userType" value="8" checked/> '.$erphp_quarter_name.' --- '.$old_price.$money_name;
						if($erphp_quarter_days) $html .= '（'.$erphp_quarter_days.'个月）<br />';else $html .= '<br />';
					}
					if($erphp_month_price){
						$old_price = '';
						if($vip_update_pay){
	                    	if($userTypeId == 6 && $erphp_day_price){
	                    		$old_price .= '<del>'.$erphp_month_price.'</del>';
	                     		$old_price .= $erphp_month_price - $erphp_day_price;
	                     	}else{
	                     		$old_price .= $erphp_month_price;
	                     	}
	                    }else{
	                     	$old_price .= $erphp_month_price;
	                    }
						$html .= '<input type="radio" name="userType" value="7" checked/> '.$erphp_month_name.' --- '.$old_price.$money_name;
						if($erphp_month_days) $html .= '（'.$erphp_month_days.'天）<br />';else $html .= '<br />';
					}
					if($erphp_day_price){
						$html .= '<input type="radio" name="userType" value="6" checked/> '.$erphp_day_name.' --- '.$erphp_day_price.$money_name;
						if($erphp_day_days) $html .= '（'.$erphp_day_days.'天）';
					}
				$html .= '</td>
			</tr>
			<tr>
				<td valign="top" class="erphpdown-sc-td-title">'.__('余额','erphpdown').'</td>
				<td>'.sprintf("%.2f",$okMoney).$money_name.'</td>
		</tr>
		<tr>
			<td colspan="2"><input type="submit" value="'.__('立即升级','erphpdown').'" class="erphpdown-sc-btn" />
			</td>
		</tr>
		</table>
		</form>
	</div>';
	return $html;
}

//支付升级VIP
function erphpdown_sc_vip_pay() {
	if(!is_user_logged_in()){
		return '请先登录！';
	}
	global $wpdb, $current_user;
	$vip_update_pay = get_option('vip_update_pay');
	$html = '<div class="erphpdown-sc">';

	if(isset($_POST['userType2']) && isset($_POST['paytype2'])){
		$paytype=intval($_POST['paytype2']);
		$usertype = intval($_POST['userType2']);

		if($paytype==1)
		{
			$url=constant("erphpdown")."payment/alipay.php?ice_type=".$usertype;
		}
		elseif($paytype==5)
		{
			$url=constant("erphpdown")."payment/f2fpay.php?ice_type=".$usertype;
		}
		elseif($paytype==4)
		{
			if(erphpdown_is_weixin() && get_option('ice_weixin_app')){
				$url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.get_option('ice_weixin_appid').'&redirect_uri='.urlencode(constant("erphpdown")).'payment%2Fweixin.php%3Fice_type%3D'.$usertype.'&response_type=code&scope=snsapi_base&state=STATE&connect_redirect=1#wechat_redirect';
			}else{
				$url=constant("erphpdown")."payment/weixin.php?ice_type=".$usertype;
			}
		}
		elseif($paytype==7)
		{
			$url=constant("erphpdown")."payment/paypy.php?ice_type=".$usertype;
		}
		elseif($paytype==8)
		{
			$url=constant("erphpdown")."payment/paypy.php?ice_type=".$usertype."&type=alipay";
		}
		elseif($paytype==2)
		{
			$url=constant("erphpdown")."payment/paypal.php?ice_type=".$usertype;
		}
	    elseif($paytype==18)
		{
			$url=constant("erphpdown")."payment/xhpay3.php?ice_type=".$usertype."&type=2";
		}
		elseif($paytype==17)
		{
			$url=constant("erphpdown")."payment/xhpay3.php?ice_type=".$usertype."&type=1";
		}elseif($paytype==19)
		{
			$url=constant("erphpdown")."payment/payjs.php?ice_type=".$usertype;
		}elseif($paytype==20)
		{
			$url=constant("erphpdown")."payment/payjs.php?ice_type=".$usertype."&type=alipay";
		}
	    elseif($paytype==13)
	    {
	        $url=constant("erphpdown")."payment/codepay.php?ice_type=".$usertype."&type=1";
	    }elseif($paytype==14)
	    {
	        $url=constant("erphpdown")."payment/codepay.php?ice_type=".$usertype."&type=3";
	    }elseif($paytype==15)
	    {
	        $url=constant("erphpdown")."payment/codepay.php?ice_type=".$usertype."&type=2";
	    }
	    elseif($paytype==21)
		{
			$url=constant("erphpdown")."payment/epay.php?ice_type=".$usertype."&type=alipay";
		}elseif($paytype==22)
		{
			$url=constant("erphpdown")."payment/epay.php?ice_type=".$usertype."&type=wxpay";
		}elseif($paytype==23)
		{
			$url=constant("erphpdown")."payment/epay.php?ice_type=".$usertype."&type=qqpay";
		}elseif($paytype==31)
		{
			$url=constant("erphpdown")."payment/vpay.php?ice_type=".$usertype."&type=2";
		}elseif($paytype==32)
		{
			$url=constant("erphpdown")."payment/vpay.php?ice_type=".$usertype;
		}elseif($paytype==41)
		{
			$url=constant("erphpdown")."payment/easepay.php?ice_type=".$usertype."&type=alipay";
		}elseif($paytype==42)
		{
			$url=constant("erphpdown")."payment/easepay.php?ice_type=".$usertype."&type=wxpay";
		}elseif($paytype==43)
		{
			$url=constant("erphpdown")."payment/easepay.php?ice_type=".$usertype."&type=usdt";
		}elseif($paytype==50)
		{
			$url=home_url('?epd_v64='.base64_encode('usdt-'.$usertype.'-'.time()));
		}elseif($paytype==60)
		{
			$url=home_url('?epd_v64='.base64_encode('stripe-'.$usertype.'-'.time()));
		}elseif(plugin_check_ecpay() && $paytype==70)
		{
			$url=ERPHPDOWN_ECPAY_URL."/ecpay.php?ice_type=".$usertype;
		}elseif(plugin_check_newebpay() && $paytype==80)
		{
			$url=ERPHPDOWN_NEWEBPAY_URL."/newebpay.php?ice_type=".$usertype;
		}
		echo "<script>location.href='".$url."'</script>";
	}

	$erphp_life_price    = get_option('erphp_life_price');
	$erphp_year_price    = get_option('erphp_year_price');
	$erphp_quarter_price = get_option('erphp_quarter_price');
	$erphp_month_price  = get_option('erphp_month_price');
	$erphp_day_price  = get_option('erphp_day_price');
	$erphp_life_name    = get_option('erphp_life_name')?get_option('erphp_life_name'):'终身VIP';
	$erphp_year_name    = get_option('erphp_year_name')?get_option('erphp_year_name'):'包年VIP';
	$erphp_quarter_name = get_option('erphp_quarter_name')?get_option('erphp_quarter_name'):'包季VIP';
	$erphp_month_name  = get_option('erphp_month_name')?get_option('erphp_month_name'):'包月VIP';
	$erphp_day_name  = get_option('erphp_day_name')?get_option('erphp_day_name'):'体验VIP';
	$erphp_life_days    = get_option('erphp_life_days');
	$erphp_year_days    = get_option('erphp_year_days');
	$erphp_quarter_days = get_option('erphp_quarter_days');
	$erphp_month_days  = get_option('erphp_month_days');
	$erphp_day_days  = get_option('erphp_day_days');
	$money_name = get_option('ice_name_alipay');
	$okMoney=erphpGetUserOkMoney();
	$userTypeId=getUsreMemberType();
	$html .= '<h2>'.__('升级VIP','erphpdown').'<span>'.__('支付','erphpdown').'</span></h2>
		<p>'.__("当前类型：",'erphpdown');
		if($userTypeId==6)
		{
			$html .= $erphp_day_name;
		}
		elseif($userTypeId==7)
		{
			$html .= $erphp_month_name;
		}
		elseif ($userTypeId==8)
		{
			$html .= $erphp_quarter_name;
		}
		elseif ($userTypeId==9)
		{
			$html .= $erphp_year_name;
		}
		elseif ($userTypeId==10)
		{
			$html .= $erphp_life_name;
		}
		else 
		{
			$html .= __('普通用户','erphpdown');
		}
		if($userTypeId>5 && $userTypeId<10) $html .= __('，到期时间：','erphpdown').getUsreMemberTypeEndTime();
		$html .= '</p>
		<form method="post">
			<table class="erphpdown-sc-table">
			<tr>
				<td valign="top" class="erphpdown-sc-td-title">'.__('VIP类型','erphpdown').'</td>
				<td>';
					if($erphp_life_price){
						$old_price = '';
						if($vip_update_pay){
	                    	if($userTypeId == 6 && $erphp_day_price){
	                    		$old_price .= '<del>'.$erphp_life_price.'</del>';
	                     		$old_price .= $erphp_life_price - $erphp_day_price;
	                     	}elseif($userTypeId == 7 && $erphp_month_price){
	                     		$old_price .= '<del>'.$erphp_life_price.'</del>';
	                     		$old_price .= $erphp_life_price - $erphp_month_price;
	                     	}elseif($userTypeId == 8 && $erphp_quarter_price){
	                     		$old_price .= '<del>'.$erphp_life_price.'</del>';
	                     		$old_price .= $erphp_life_price - $erphp_quarter_price;
	                     	}elseif($userTypeId == 9 && $erphp_year_price){
	                     		$old_price .= '<del>'.$erphp_life_price.'</del>';
	                     		$old_price .= $erphp_life_price - $erphp_year_price;
	                     	}else{
	                     		$old_price .= $erphp_life_price;
	                     	}
	                    }else{
	                    	$old_price .= $erphp_life_price;
	                    }
						$html .= '<input type="radio" name="userType2" value="10" checked /> '.$erphp_life_name.' --- '.$old_price.$money_name;
						if($erphp_life_days) $html .= '（'.$erphp_life_days.'年）<br />'; else $html .= '<br />';
					}
					if($erphp_year_price){
						$old_price = '';
						if($vip_update_pay){
	                    	if($userTypeId == 6 && $erphp_day_price){
	                    		$old_price .= '<del>'.$erphp_year_price.'</del>';
	                     		$old_price .= $erphp_year_price - $erphp_day_price;
	                     	}elseif($userTypeId == 7 && $erphp_month_price){
	                     		$old_price .= '<del>'.$erphp_year_price.'</del>';
	                     		$old_price .= $erphp_year_price - $erphp_month_price;
	                     	}elseif($userTypeId == 8 && $erphp_quarter_price){
	                     		$old_price .= '<del>'.$erphp_year_price.'</del>';
	                     		$old_price .= $erphp_year_price - $erphp_quarter_price;
	                     	}else{
	                     		$old_price .= $erphp_year_price;
	                     	}
	                    }else{
	                     	$old_price .= $erphp_year_price;
	                    }
						$html .= '<input type="radio" name="userType2" value="9" checked/> '.$erphp_year_name.' --- '.$old_price.$money_name;
						if($erphp_year_days) $html .= '（'.$erphp_year_days.'个月）<br />';else $html .= '<br />';
					}
					if($erphp_quarter_price){
						$old_price = '';
						if($vip_update_pay){
	                    	if($userTypeId == 6 && $erphp_day_price){
	                    		$old_price .= '<del>'.$erphp_quarter_price.'</del>';
	                     		$old_price .= $erphp_quarter_price - $erphp_day_price;
	                     	}elseif($userTypeId == 7 && $erphp_month_price){
	                     		$old_price .= '<del>'.$erphp_quarter_price.'</del>';
	                     		$old_price .= $erphp_quarter_price - $erphp_month_price;
	                     	}else{
	                     		$old_price .= $erphp_quarter_price;
	                     	}
	                    }else{
	                     	$old_price .= $erphp_quarter_price;
	                    }
						$html .= '<input type="radio" name="userType2" value="8" checked/> '.$erphp_quarter_name.' --- '.$old_price.$money_name;
						if($erphp_quarter_days) $html .= '（'.$erphp_quarter_days.'个月）<br />';else $html .= '<br />';
					}
					if($erphp_month_price){
						$old_price = '';
						if($vip_update_pay){
	                    	if($userTypeId == 6 && $erphp_day_price){
	                    		$old_price .= '<del>'.$erphp_month_price.'</del>';
	                     		$old_price .= $erphp_month_price - $erphp_day_price;
	                     	}else{
	                     		$old_price .= $erphp_month_price;
	                     	}
	                    }else{
	                     	$old_price .= $erphp_month_price;
	                    }
						$html .= '<input type="radio" name="userType2" value="7" checked/> '.$erphp_month_name.' --- '.$old_price.$money_name;
						if($erphp_month_days) $html .= '（'.$erphp_month_days.'天）<br />';else $html .= '<br />';
					}
					if($erphp_day_price){
						$html .= '<input type="radio" name="userType2" value="6" checked/> '.$erphp_day_name.' --- '.$erphp_day_price.$money_name;
						if($erphp_day_days) $html .= '（'.$erphp_day_days.'天）';
					}
				$html .= '</td>
			</tr>
			<tr>
        <td valign="top" class="erphpdown-sc-td-title">'.__('支付方式','erphpdown').'</td>
        <td>';
        $erphpdown_recharge_order = get_option('erphpdown_recharge_order');
    		if($erphpdown_recharge_order){
    			$erphpdown_recharge_order_arr = explode(',', str_replace('，', ',', trim($erphpdown_recharge_order)));
    			$pi = 0;
    			foreach ($erphpdown_recharge_order_arr as $payment) {
    				if($pi == 0) $checked = ' checked'; else $checked = '';
    				switch ($payment) {
    					case 'alipay':
    						$html .= '<input type="radio" id="paytype1"'.$checked.' class="paytype" name="paytype2" value="1" /> <label for="paytype1" class="payment-label payment-alipay-label">'.__('支付宝','erphpdown').'</label>';
    						break;
    					case 'wxpay':
    						$html .= '<input type="radio" id="paytype4" class="paytype"'.$checked.' name="paytype2" value="4" /> <label for="paytype4" class="payment-label payment-wxpay-label">'.__('微信','erphpdown').'</label>';
    						break;
    					case 'f2fpay':
    						$html .= '<input type="radio" id="paytype5" class="paytype"'.$checked.' name="paytype2" value="5" /> <label for="paytype5" class="payment-label payment-alipay-label">'.__('支付宝','erphpdown').'</label>';
    						break;
    					case 'paypal':
    						$html .= '<input type="radio" id="paytype2" class="paytype"'.$checked.' name="paytype2" value="2" /> <label for="paytype2" class="payment-label payment-paypal-label">PayPal</label>';
    						break;
    					case 'paypy-wx':
    						$html .= '<input type="radio" id="paytype7" class="paytype" name="paytype2" value="7"'.$checked.' /> <label for="paytype7" class="payment-label payment-wxpay-label">'.__('微信','erphpdown').'</label>';
    						break;
    					case 'paypy-ali':
    						$html .= '<input type="radio" id="paytype8" class="paytype" name="paytype2" value="8"'.$checked.' /> <label for="paytype8" class="payment-label payment-alipay-label">'.__('支付宝','erphpdown').'</label>';
    						break;
    					case 'payjs-wx':
    						$html .= '<input type="radio" id="paytype19" class="paytype" name="paytype2" value="19"'.$checked.' /><label for="paytype19" class="payment-label payment-wxpay-label">'.__('微信','erphpdown').'</label>';
    						break;
    					case 'payjs-ali':
    						$html .= '<input type="radio" id="paytype20" class="paytype" name="paytype2" value="20"'.$checked.' /><label for="paytype20" class="payment-label payment-alipay-label">'.__('支付宝','erphpdown').'</label>';
    						break;
    					case 'xhpay-wx':
    						$html .= '<input type="radio" id="paytype18" class="paytype" name="paytype2" value="18"'.$checked.' /> <label for="paytype18" class="payment-label payment-wxpay-label">'.__('微信','erphpdown').'</label>';
    						break;
    					case 'xhpay-ali':
    						$html .= '<input type="radio" id="paytype17" class="paytype" name="paytype2" value="17"'.$checked.' /> <label for="paytype17" class="payment-label payment-alipay-label">'.__('支付宝','erphpdown').'</label>';
    						break;
    					case 'codepay-wx':
    						$html .= '<input type="radio" id="paytype14" class="paytype" name="paytype2" value="14"'.$checked.' /> <label for="paytype14" class="payment-label payment-wxpay-label">'.__('微信','erphpdown').'</label>';
    						break;
    					case 'codepay-ali':
    						$html .= '<input type="radio" id="paytype13" class="paytype" name="paytype2" value="13"'.$checked.' /> <label for="paytype13" class="payment-label payment-alipay-label">'.__('支付宝','erphpdown').'</label>';
    						break;
    					case 'codepay-qq':
    						$html .= '<input type="radio" id="paytype15" class="paytype" name="paytype2" value="15"'.$checked.' /> <label for="paytype15" class="payment-label payment-qqpay-label">'.__('QQ钱包','erphpdown').'</label>';
    						break;
    					case 'epay-wx':
    						$html .= '<input type="radio" id="paytype22" class="paytype" name="paytype2" value="22"'.$checked.' /> <label for="paytype22" class="payment-label payment-wxpay-label">'.__('微信','erphpdown').'</label>';
    						break;
    					case 'epay-ali':
    						$html .= '<input type="radio" id="paytype21" class="paytype" name="paytype2" value="21"'.$checked.' /> <label for="paytype21" class="payment-label payment-alipay-label">'.__('支付宝','erphpdown').'</label>';
    						break;
    					case 'epay-qq':
    						$html .= '<input type="radio" id="paytype23" class="paytype" name="paytype2" value="23"'.$checked.' /> <label for="paytype23" class="payment-label payment-qqpay-label">'.__('QQ钱包','erphpdown').'</label>';
    						break;
    					case 'easepay-wx':
    						$html .= '<input type="radio" id="paytype42" class="paytype" name="paytype2" value="42"'.$checked.' /> <label for="paytype42" class="payment-label payment-wxpay-label">'.__('微信','erphpdown').'</label>';
    						break;
    					case 'easepay-ali':
    						$html .= '<input type="radio" id="paytype41" class="paytype" name="paytype2" value="41"'.$checked.' /> <label for="paytype41" class="payment-label payment-alipay-label">'.__('支付宝','erphpdown').'</label>';
    						break;
    					case 'easepay-usdt':
    						$html .= '<input type="radio" id="paytype43" class="paytype" name="paytype2" value="43"'.$checked.' /> <label for="paytype43" class="payment-label payment-ut-label">'.__('USDT','erphpdown').'</label>';
    						break;
    					case 'vpay-wx':
    						$html .= '<input type="radio" id="paytype32" class="paytype" name="paytype2" value="32"'.$checked.' /> <label for="paytype32" class="payment-label payment-wxpay-label">'.__('微信','erphpdown').'</label>';
    						break;
    					case 'vpay-ali':
    						$html .= '<input type="radio" id="paytype31" class="paytype" name="paytype2" value="31"'.$checked.' /> <label for="paytype31" class="payment-label payment-alipay-label">'.__('支付宝','erphpdown').'</label>';
    						break;
    					case 'usdt':
    						$html .= '<input type="radio" id="paytype50" class="paytype" name="paytype2" value="50"'.$checked.' /> <label for="paytype50" class="payment-label payment-ut-label">'.__('USDT','erphpdown').'</label>';
    						break;
    					case 'stripe':
    						$html .= '<input type="radio" id="paytype60" class="paytype" name="paytype2" value="60"'.$checked.' /> <label for="paytype60" class="payment-label payment-stripe-label">'.__('信用卡','erphpdown').'</label>';
    						break;
    					case 'ecpay':
    						$html .= '<input type="radio" id="paytype70" class="paytype" name="paytype2" value="70"'.$checked.' /> <label for="paytype70" class="payment-label payment-ecpay-label">'.__('新台币','erphpdown').'</label>';
    						break;
    					case 'newebpay':
    						$html .= '<input type="radio" id="paytype80" class="paytype" name="paytype2" value="80"'.$checked.' /> <label for="paytype80" class="payment-label payment-newebpay-label">'.__('新台币','erphpdown').'</label>';
    						break;
    					default:
    						break;
    				}
    				$pi ++;
    			}
    		}else{
    			if(get_option('erphpdown_usdt_address')){
						$html .= '<input type="radio" id="paytype50" class="paytype" name="paytype2" value="50" checked/>'.__('USDT','erphpdown').'&nbsp;';
					}
          if(get_option('ice_payapl_api_uid')){
          	$html .= '<input type="radio" id="paytype2" class="paytype" checked name="paytype2" value="2" />PayPal&nbsp;';
          }
          if(get_option('erphpdown_stripe_pk')){
						$html .= '<input type="radio" id="paytype60" class="paytype" name="paytype2" value="60" checked/>'.__('信用卡','erphpdown').'&nbsp;';
					}
		 if(plugin_check_ecpay() && get_option('erphpdown_ecpay_MerchantID')){
						$html .= '<input type="radio" id="paytype70" class="paytype" name="paytype2" value="70" checked/>'.__('新台币','erphpdown').'&nbsp;';
					}
		if(plugin_check_newebpay() && get_option('erphpdown_newebpay_MerchantID')){
						$html .= '<input type="radio" id="paytype80" class="paytype" name="paytype2" value="80" checked/>'.__('新台币','erphpdown').'&nbsp;';
					}
          if(get_option('ice_weixin_mchid')){ 
          	$html .= '<input type="radio" id="paytype4" class="paytype" checked name="paytype2" value="4" />'.__('微信','erphpdown').'&nbsp;';
          }
          if(get_option('ice_ali_partner') || get_option('ice_ali_app_id')){ 
          	$html .= '<input type="radio" id="paytype1" class="paytype" checked name="paytype2" value="1" />'.__('支付宝','erphpdown').'&nbsp;';
          }
          if(get_option('erphpdown_f2fpay_id')){
						$html .= '<input type="radio" id="paytype5" class="paytype" checked name="paytype2" value="5" />'.__('支付宝','erphpdown').'&nbsp;';
					}
					if(get_option('erphpdown_payjs_appid')){
						if(!get_option('erphpdown_payjs_alipay')){ $html .= '<input type="radio" id="paytype20" class="paytype" name="paytype2" value="20" checked />'.__('支付宝','erphpdown').'&nbsp;';}
						if(!get_option('erphpdown_payjs_wxpay')){ $html .= '<input type="radio" id="paytype19" class="paytype" name="paytype2" value="19" checked />'.__('微信','erphpdown').'&nbsp;';}
					}
					if(get_option('erphpdown_xhpay_appid31')){
						$html .= '<input type="radio" id="paytype18" class="paytype" name="paytype2" value="18" checked />'.__('微信','erphpdown').'&nbsp;';
					}
					if(get_option('erphpdown_xhpay_appid32')){
						$html .= '<input type="radio" id="paytype17" class="paytype" name="paytype2" value="17" checked />'.__('支付宝','erphpdown').'&nbsp;';     
					}
          if(get_option('erphpdown_codepay_appid')){
          	if(!get_option('erphpdown_codepay_alipay')){ $html .= '<input type="radio" id="paytype13" class="paytype" name="paytype2" value="13" checked />'.__('支付宝','erphpdown').'&nbsp;';}
          	if(!get_option('erphpdown_codepay_wxpay')){ $html .= '<input type="radio" id="paytype14" class="paytype" name="paytype2" value="14" />'.__('微信','erphpdown').'&nbsp;';}
          	if(!get_option('erphpdown_codepay_qqpay')){ $html .= '<input type="radio" id="paytype15" class="paytype" name="paytype2" value="15" />'.__('QQ钱包','erphpdown').'&nbsp;';}
          }
          if(get_option('erphpdown_paypy_key')){
          	if(!get_option('erphpdown_paypy_alipay')){ $html .= '<input type="radio" id="paytype8" class="paytype" name="paytype2" value="8" checked/>'.__('支付宝','erphpdown').'&nbsp;';}
						if(!get_option('erphpdown_paypy_wxpay')){ $html .= '<input type="radio" id="paytype7" class="paytype" name="paytype2" value="7" checked />'.__('微信','erphpdown').'&nbsp;';}  
					}
					if(get_option('erphpdown_epay_id')){
						if(!get_option('erphpdown_epay_alipay')){ $html .= '<input type="radio" id="paytype21" class="paytype" name="paytype2" value="21" checked />'.__('支付宝','erphpdown').'&nbsp;';}
						if(!get_option('erphpdown_epay_qqpay')){ $html .= '<input type="radio" id="paytype23" class="paytype" name="paytype2" value="23" checked/>'.__('QQ钱包','erphpdown').'&nbsp;';}
						if(!get_option('erphpdown_epay_wxpay')){ $html .= '<input type="radio" id="paytype22" class="paytype" name="paytype2" value="22" checked />'.__('微信','erphpdown').'&nbsp;';}
					}
					if(get_option('erphpdown_easepay_id')){
						if(!get_option('erphpdown_easepay_usdt')){ $html .= '<input type="radio" id="paytype43" class="paytype" name="paytype2" value="43" checked />'.__('USDT','erphpdown').'&nbsp;';}
						if(!get_option('erphpdown_easepay_alipay')){ $html .= '<input type="radio" id="paytype41" class="paytype" name="paytype2" value="41" checked />'.__('支付宝','erphpdown').'&nbsp;';}
						if(!get_option('erphpdown_easepay_wxpay')){ $html .= '<input type="radio" id="paytype42" class="paytype" name="paytype2" value="42" checked />'.__('微信','erphpdown').'&nbsp;';}
					}
          if(get_option('erphpdown_vpay_key')){
						if(!get_option('erphpdown_vpay_alipay')){ $html .= '<input type="radio" id="paytype31" class="paytype" name="paytype2" value="31" checked />'.__('支付宝','erphpdown').'&nbsp;';}
						if(!get_option('erphpdown_vpay_wxpay')){ $html .= '<input type="radio" id="paytype32" class="paytype" name="paytype2" value="32" checked />'.__('微信','erphpdown').'';}
					}
				}
      $html .= '</td>
      </tr>
			<tr>
				<td colspan="2"><input type="submit" value="'.__('立即升级','erphpdown').'" class="erphpdown-sc-btn" />
				</td>
			</tr>
		</table>
		</form>
	</div>';
	return $html;
}

//积分兑换
function erphpdown_sc_mycred(){
	if(!is_user_logged_in()){
		return '请先登录！';
	}

	if(!(plugin_check_cred() && function_exists('mycred_get_users_cred') && get_option('erphp_mycred') == 'yes')){
		return '';
	}
	global $wpdb, $current_user;

	$erphp_to_mycred = get_option('erphp_to_mycred');
	$mycred_core = get_option('mycred_pref_core');
	$error = '';
	if(isset($_POST['epdmycrednum']) && $_POST['epdmycrednum']){
		$epdmycrednum = esc_sql($_POST['epdmycrednum']);
		if(is_numeric($epdmycrednum) && $epdmycrednum > 0){
			if(floatval($epdmycrednum*$erphp_to_mycred) < 1 || floor($epdmycrednum*$erphp_to_mycred) != $epdmycrednum*$erphp_to_mycred){
				$error= "<div class='error'>".__('兑换失败，兑换扣除的'.$mycred_core['name']['plural'].'数需为整数','erphpdown')."</div>";
			}else{
				if(floatval(mycred_get_users_cred( $current_user->ID )) < floatval($epdmycrednum*$erphp_to_mycred)){
					$error= "<div class='error'>".__($mycred_core['name']['plural'].'不足','erphpdown')."</div>";
				}else{
					mycred_add( '兑换', $current_user->ID, '-'.$epdmycrednum*$erphp_to_mycred, '兑换扣除%plural%!', date("Y-m-d H:i:s") );
					$money = $epdmycrednum;
					if(addUserMoney($current_user->ID, $money)){
						$sql="INSERT INTO $wpdb->icemoney (ice_money,ice_num,ice_user_id,ice_time,ice_success,ice_note,ice_success_time,ice_alipay)
						VALUES ('".$money."','".date("ymdhis").mt_rand(10000,99999)."','".$current_user->ID."','".date("Y-m-d H:i:s")."',1,'4','".date("Y-m-d H:i:s")."','')";
						$wpdb->query($sql);
						$error= "<div class='updated'>".__('兑换成功','erphpdown')."</div>";
					}else{
						$error= "<div class='error'>".__('系统超时，请稍后重试','erphpdown')."</div>";
					}
				}
			}
		}
	}

	$html = '<div class="erphpdown-sc">
			<h2>'.sprintf( __('%s兑换','erphpdown'), $mycred_core['name']['plural']).'</h2>'.$error.'
			<form method="post">
			<table class="erphpdown-sc-table">
				<tr>
					<td valign="top" class="erphpdown-sc-td-title">'.__('兑换比例','erphpdown').'</td>
					<td>
					  1 '.get_option('ice_name_alipay').' = '.$erphp_to_mycred.' '.$mycred_core['name']['plural'].'
					</td>
				</tr>
				 <tr>
					<td valign="top" class="erphpdown-sc-td-title">'.get_option('ice_name_alipay').'</td>
					<td>
					<input type="number" min="'.(1/$erphp_to_mycred).'" step="'.(1/$erphp_to_mycred).'" id="epdmycrednum" name="epdmycrednum" class="erphpdown-sc-input" placeholder="'.sprintf(__('输入兑换的 %s 数量','erphpdown'),get_option('ice_name_alipay')).'" required />
					</td>
				</tr>
				<tr>
					<td valign="top" class="erphpdown-sc-td-title">'.$mycred_core['name']['plural'].'</td>
					<td>
					  '.mycred_get_users_cred( $current_user->ID ).'
					</td>
				</tr>
				<tr>
					<td colspan="2"><input type="submit" value="'.__('立即兑换','erphpdown').'" class="erphpdown-sc-btn" /></td>
				</tr> 
			</table>
		</form>
		</div>';
	return $html;
}

//充值卡
function erphpdown_sc_recharge_card(){
	if(!is_user_logged_in()){
		return '请先登录！';
	}

	if(!function_exists("checkDoCardResult")){
		return '';
	}

	global $wpdb, $current_user;
	$error = '';$html = '';
	if(isset($_POST['epdcardnum']) && $_POST['epdcardnum']){
		$cardnum = esc_sql($_POST['epdcardnum']);
		$cardpass = esc_sql($_POST['epdcardpass']);
		$result = checkDoCardResult($cardnum,$cardpass);
		if($result == '5'){
			$error= "<div class='error'>".__('充值卡不存在','erphpdown')."</div>";
		}elseif($result == '0'){
			$error= "<div class='error'>".__('充值卡已被使用','erphpdown')."</div>";
		}elseif($result == '2'){
			$error= "<div class='error'>".__('充值卡密错误','erphpdown')."</div>";
		}elseif($result == '1'){
			$error= "<div class='updated'>".__('充值成功','erphpdown')."</div>";
		}else{
			$error= "<div class='error'>".__('系统超时，请稍后重试','erphpdown')."</div>";
		}
	}

	$html .= '<div class="erphpdown-sc">
			<h2>'.__('充值卡','erphpdown').'</h2>'.$error.'
			<form method="post">
			<table class="erphpdown-sc-table">
				 <tr>
					<td valign="top" class="erphpdown-sc-td-title">'.__('充值卡号','erphpdown').'</td>
					<td>
					<input type="text" id="epdcardnum" name="epdcardnum" class="erphpdown-sc-input" required />
					</td>
				</tr>
				<tr>
					<td valign="top" class="erphpdown-sc-td-title"><span id="cname">'.__('充值卡密','erphpdown').'</span></td>
					<td>
					<input type="text" id="epdcardpass" name="epdcardpass" class="erphpdown-sc-input" required />
					</td>
				</tr>
				<tr>
					<td colspan="2"><input type="submit" value="'.__('立即充值','erphpdown').'" class="erphpdown-sc-btn" /><br>'.get_option('ice_tips_card').'</td>
				</tr> 
			</table>
		</form>
		</div>';
	return $html;
}

//VIP充值卡
function erphpdown_sc_vip_card(){
	if(!is_user_logged_in()){
		return '请先登录！';
	}

	if(!function_exists("checkDoVipCardResult")){
		return '';
	}

	global $wpdb, $current_user;
	$error = '';
	if(isset($_POST['epdcardnum2']) && $_POST['epdcardnum2']){
		$cardnum = esc_sql($_POST['epdcardnum2']);
		$result = checkDoVipCardResult($cardnum);
		if($result == '3'){
			$error= "<div class='error'>充值卡不存在</div>";
		}elseif($result == '0'){
			$error= "<div class='error'>充值卡已被使用</div>";
		}elseif($result == '2'){
			$error= "<div class='error'>充值卡已过期</div>";
		}elseif($result == '1'){
			$error= "<div class='updated'>升级成功</div>";
		}else{
			$error= "<div class='error'>系统超时，请稍后重试</div>";
		}
	}

	$html = '<div class="erphpdown-sc">
			<h2>'.__('升级VIP','erphpdown').'<span>'.__('充值卡','erphpdown').'</span></h2>'.$error.'
			<form method="post">
			<table class="erphpdown-sc-table">
				 <tr>
					<td valign="top" class="erphpdown-sc-td-title">'.__('VIP卡号','erphpdown').'</td>
					<td>
					<input type="text" id="epdcardnum2" name="epdcardnum2" maxlength="50" size="50" class="erphpdown-sc-input" required />
					</td>
				</tr>
				<tr>
					<td colspan="2"><input type="submit" value="'.__('立即升级','erphpdown').'" class="erphpdown-sc-btn" /><br>'.get_option('ice_tips_card').'</td>
				</tr> 
			</table>
		</form>
		</div>';
	return $html;
}

function erphpdown_sc_vip_down(){
	if(!is_user_logged_in()){
		return '请先登录！';
	}

	global $wpdb, $current_user;
	$total_trade   = $wpdb->get_var("SELECT COUNT(ice_id) FROM $wpdb->down where ice_user_id=".$current_user->ID." and ice_vip=1");
	$ice_perpage = 20;
	$pages = ceil($total_trade / $ice_perpage);
	$page=isset($_GET['pp']) ?intval($_GET['pp']) :1;
	$offset = $ice_perpage*($page-1);
	$list = $wpdb->get_results("SELECT * FROM $wpdb->down where ice_user_id= ".$current_user->ID." and ice_vip=1 order by ice_time DESC limit $offset,$ice_perpage");
	$html = '<div class="erphpdown-sc">
    	<h2>'.__('VIP下载记录','erphpdown').'</h2>
      <table class="erphpdown-sc-list">
        <thead>
          <tr>
          	<th>'.__('名称','erphpdown').'</th>
            <th>'.__('时间','erphpdown').'</th>
          </tr>
        </thead>
        <tbody>';
          if($list) {
              foreach($list as $value)
              {
                  $html .= "<tr>\n";
                  $html .= "<td class='tit'><a href='".get_permalink($value->ice_post_id)."' target='_blank'>".get_post($value->ice_post_id)->post_title."</a></td>\n";
                  $html .= "<td>$value->ice_time</td>\n";
                  $html .= "</tr>";
              }
          }
          else
          {
              $html .= '<tr><td colspan="2" align="center">'.__('暂无记录','erphpdown').'</td></tr>';
          }
        $html .= '</tbody>
      </table>'.erphpdown_paging($page,$pages,'vipdown').'
    </div>';
  	return $html; 
}

//充值
function erphpdown_sc_recharge() {
	if(!is_user_logged_in()){
		return '请先登录！';
	}

	global $wpdb, $current_user;
	$doo = 1;$error='';
	if(isset($_POST['paytype']) && $_POST['paytype']){
		$paytype=esc_sql(intval($_POST['paytype']));
		

		if(isset($_POST['paytype']) && $paytype==1)
		{
			$url=constant("erphpdown")."payment/alipay.php?ice_money=".esc_sql($_POST['ice_money']);
		}
		elseif(isset($_POST['paytype']) && $paytype==5)
		{
			$url=constant("erphpdown")."payment/f2fpay.php?ice_money=".esc_sql($_POST['ice_money']);
		}
		elseif(isset($_POST['paytype']) && $paytype==2)
		{
			$url=constant("erphpdown")."payment/paypal.php?ice_money=".esc_sql($_POST['ice_money']);
		}
		elseif(isset($_POST['paytype']) && $paytype==4)
		{
			if(erphpdown_is_weixin() && get_option('ice_weixin_app')){
				$url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.get_option('ice_weixin_appid').'&redirect_uri='.urlencode(constant("erphpdown")).'payment%2Fweixin.php%3Fice_money%3D'.esc_sql($_POST['ice_money']).'&response_type=code&scope=snsapi_base&state=STATE&connect_redirect=1#wechat_redirect';
			}else{
				$url=constant("erphpdown")."payment/weixin.php?ice_money=".esc_sql($_POST['ice_money']);
			}
		}
		elseif(isset($_POST['paytype']) && $paytype==7)
		{
			$url=constant("erphpdown")."payment/paypy.php?ice_money=".esc_sql($_POST['ice_money']);
		}
		elseif(isset($_POST['paytype']) && $paytype==8)
		{
			$url=constant("erphpdown")."payment/paypy.php?ice_money=".esc_sql($_POST['ice_money'])."&type=alipay";
		}
		elseif(isset($_POST['paytype']) && $paytype==18)
		{
			$url=constant("erphpdown")."payment/xhpay3.php?ice_money=".esc_sql($_POST['ice_money'])."&type=2";
		}
		elseif(isset($_POST['paytype']) && $paytype==17)
		{
			$url=constant("erphpdown")."payment/xhpay3.php?ice_money=".esc_sql($_POST['ice_money'])."&type=1";
		}elseif(isset($_POST['paytype']) && $paytype==19)
		{
			$url=constant("erphpdown")."payment/payjs.php?ice_money=".esc_sql($_POST['ice_money']);
		}elseif(isset($_POST['paytype']) && $paytype==20)
		{
			$url=constant("erphpdown")."payment/payjs.php?ice_money=".esc_sql($_POST['ice_money'])."&type=alipay";
		}
		elseif(isset($_POST['paytype']) && $paytype==13)
		{
			$url=constant("erphpdown")."payment/codepay.php?ice_money=".esc_sql($_POST['ice_money'])."&type=1";
		}elseif(isset($_POST['paytype']) && $paytype==14)
		{
			$url=constant("erphpdown")."payment/codepay.php?ice_money=".esc_sql($_POST['ice_money'])."&type=3";
		}elseif(isset($_POST['paytype']) && $paytype==15)
		{
			$url=constant("erphpdown")."payment/codepay.php?ice_money=".esc_sql($_POST['ice_money'])."&type=2";
		}
		elseif(isset($_POST['paytype']) && $paytype==21)
		{
			$url=constant("erphpdown")."payment/epay.php?ice_money=".esc_sql($_POST['ice_money'])."&type=alipay";
		}elseif(isset($_POST['paytype']) && $paytype==22)
		{
			$url=constant("erphpdown")."payment/epay.php?ice_money=".esc_sql($_POST['ice_money'])."&type=wxpay";
		}elseif(isset($_POST['paytype']) && $paytype==23)
		{
			$url=constant("erphpdown")."payment/epay.php?ice_money=".esc_sql($_POST['ice_money'])."&type=qqpay";
		}elseif(isset($_POST['paytype']) && $paytype==31)
		{
			$url=constant("erphpdown")."payment/vpay.php?ice_money=".esc_sql($_POST['ice_money'])."&type=2";
		}elseif(isset($_POST['paytype']) && $paytype==32)
		{
			$url=constant("erphpdown")."payment/vpay.php?ice_money=".esc_sql($_POST['ice_money']);
		}elseif(isset($_POST['paytype']) && $paytype==41)
		{
			$url=constant("erphpdown")."payment/easepay.php?ice_money=".esc_sql($_POST['ice_money'])."&type=alipay";
		}elseif(isset($_POST['paytype']) && $paytype==42)
		{
			$url=constant("erphpdown")."payment/easepay.php?ice_money=".esc_sql($_POST['ice_money'])."&type=wxpay";
		}elseif(isset($_POST['paytype']) && $paytype==43)
		{
			$url=constant("erphpdown")."payment/easepay.php?ice_money=".esc_sql($_POST['ice_money'])."&type=usdt";
		}elseif(isset($_POST['paytype']) && $paytype==50)
		{
			$url=home_url('?epd_r64='.base64_encode('usdt-'.esc_sql($_POST['ice_money']).'-'.time()));
		}elseif(isset($_POST['paytype']) && $paytype==60)
		{
			$url=home_url('?epd_r64='.base64_encode('stripe-'.esc_sql($_POST['ice_money']).'-'.time()));
		}elseif(plugin_check_ecpay() && isset($_POST['paytype']) && $paytype==70)
		{
			$url=ERPHPDOWN_ECPAY_URL."/ecpay.php?ice_money=".esc_sql($_POST['ice_money']);
		}elseif(plugin_check_newebpay() && isset($_POST['paytype']) && $paytype==80)
		{
			$url=ERPHPDOWN_NEWEBPAY_URL."/newebpay.php?ice_money=".esc_sql($_POST['ice_money']);
		}
		elseif(isset($_POST['paytype']) && $paytype==6)
		{
			$doo = 0;
			$result = checkDoCardResult(esc_sql($_POST['ice_money']),esc_sql($_POST['ice_pass']));
			if($result == '0') $error= "<div class='error'>充值卡已被使用</div>";
			elseif($result == '5') $error= "<div class='error'>充值卡不存在</div>";
			elseif($result == '1') $error= "<div class='updated'>充值成功</div>";
			else $error= "<div class='error'>系统超时，请稍后重试</div>";
		}

		if($doo){
			echo "<script>location.href='".$url."'</script>";
		}
	}

	$html = '<div class="erphpdown-sc">
	<form action="" method="post" onsubmit="return checkFm();">
			<h2>'.__('在线充值','erphpdown').'</h2>'.$error.'
			<table class="erphpdown-sc-table">
				<tr>
					<td valign="top" class="erphpdown-sc-td-title">'.__('充值比例','erphpdown').'</td>
					<td>
						1 '.__('元','erphpdown').' = '.get_option('ice_proportion_alipay') .' '. get_option('ice_name_alipay') .'
					</td>
				</tr>
				 <tr>
					<td valign="top" class="erphpdown-sc-td-title">'.__('充值金额','erphpdown').'</td>
					<td>
					<input type="text" id="ice_money" name="ice_money" maxlength="50" size="50" class="erphpdown-sc-input" />
					</td>
				</tr>
						<tr>
					<td valign="top" class="erphpdown-sc-td-title">'.__('支付方式','erphpdown').'</td>
					<td>';
					$erphpdown_recharge_order = get_option('erphpdown_recharge_order');
      		if($erphpdown_recharge_order){
      			$erphpdown_recharge_order_arr = explode(',', str_replace('，', ',', trim($erphpdown_recharge_order)));
      			$pi = 0;
      			foreach ($erphpdown_recharge_order_arr as $payment) {
      				if($pi == 0) $checked = ' checked'; else $checked = '';
      				switch ($payment) {
      					case 'alipay':
      						$html .= '<input type="radio" id="paytype1"'.$checked.' class="paytype" name="paytype" value="1" /> <label for="paytype1" class="payment-label payment-alipay-label">'.__('支付宝','erphpdown').'</label>';
      						break;
      					case 'wxpay':
      						$html .= '<input type="radio" id="paytype4" class="paytype"'.$checked.' name="paytype" value="4" /> <label for="paytype4" class="payment-label payment-wxpay-label">'.__('微信','erphpdown').'</label>';
      						break;
      					case 'f2fpay':
      						$html .= '<input type="radio" id="paytype5" class="paytype"'.$checked.' name="paytype" value="5" /> <label for="paytype5" class="payment-label payment-alipay-label">'.__('支付宝','erphpdown').'</label>';
      						break;
      					case 'paypal':
      						$html .= '<input type="radio" id="paytype2" class="paytype"'.$checked.' name="paytype" value="2" /> <label for="paytype2" class="payment-label payment-paypal-label">PayPal</label>';
      						break;
      					case 'paypy-wx':
      						$html .= '<input type="radio" id="paytype7" class="paytype" name="paytype" value="7"'.$checked.' /> <label for="paytype7" class="payment-label payment-wxpay-label">'.__('微信','erphpdown').'</label>';
      						break;
      					case 'paypy-ali':
      						$html .= '<input type="radio" id="paytype8" class="paytype" name="paytype" value="8"'.$checked.' /> <label for="paytype8" class="payment-label payment-alipay-label">'.__('支付宝','erphpdown').'</label>';
      						break;
      					case 'payjs-wx':
      						$html .= '<input type="radio" id="paytype19" class="paytype" name="paytype" value="19"'.$checked.' /><label for="paytype19" class="payment-label payment-wxpay-label">'.__('微信','erphpdown').'</label>';
      						break;
      					case 'payjs-ali':
      						$html .= '<input type="radio" id="paytype20" class="paytype" name="paytype" value="20"'.$checked.' /><label for="paytype20" class="payment-label payment-alipay-label">'.__('支付宝','erphpdown').'</label>';
      						break;
      					case 'xhpay-wx':
      						$html .= '<input type="radio" id="paytype18" class="paytype" name="paytype" value="18"'.$checked.' /> <label for="paytype18" class="payment-label payment-wxpay-label">'.__('微信','erphpdown').'</label>';
      						break;
      					case 'xhpay-ali':
      						$html .= '<input type="radio" id="paytype17" class="paytype" name="paytype" value="17"'.$checked.' /> <label for="paytype17" class="payment-label payment-alipay-label">'.__('支付宝','erphpdown').'</label>';
      						break;
      					case 'codepay-wx':
      						$html .= '<input type="radio" id="paytype14" class="paytype" name="paytype" value="14"'.$checked.' /> <label for="paytype14" class="payment-label payment-wxpay-label">'.__('微信','erphpdown').'</label>';
      						break;
      					case 'codepay-ali':
      						$html .= '<input type="radio" id="paytype13" class="paytype" name="paytype" value="13"'.$checked.' /> <label for="paytype13" class="payment-label payment-alipay-label">'.__('支付宝','erphpdown').'</label>';
      						break;
      					case 'codepay-qq':
      						$html .= '<input type="radio" id="paytype15" class="paytype" name="paytype" value="15"'.$checked.' /> <label for="paytype15" class="payment-label payment-qqpay-label">'.__('QQ钱包','erphpdown').'</label>';
      						break;
      					case 'epay-wx':
      						$html .= '<input type="radio" id="paytype22" class="paytype" name="paytype" value="22"'.$checked.' /> <label for="paytype22" class="payment-label payment-wxpay-label">'.__('微信','erphpdown').'</label>';
      						break;
      					case 'epay-ali':
      						$html .= '<input type="radio" id="paytype21" class="paytype" name="paytype" value="21"'.$checked.' /> <label for="paytype21" class="payment-label payment-alipay-label">'.__('支付宝','erphpdown').'</label>';
      						break;
      					case 'epay-qq':
      						$html .= '<input type="radio" id="paytype23" class="paytype" name="paytype" value="23"'.$checked.' /> <label for="paytype23" class="payment-label payment-qqpay-label">'.__('QQ钱包','erphpdown').'</label>';
      						break;
      					case 'easepay-wx':
      						$html .= '<input type="radio" id="paytype42" class="paytype" name="paytype" value="42"'.$checked.' /> <label for="paytype42" class="payment-label payment-wxpay-label">'.__('微信','erphpdown').'</label>';
      						break;
      					case 'easepay-ali':
      						$html .= '<input type="radio" id="paytype41" class="paytype" name="paytype" value="41"'.$checked.' /> <label for="paytype41" class="payment-label payment-alipay-label">'.__('支付宝','erphpdown').'</label>';
      						break;
      					case 'easepay-usdt':
      						$html .= '<input type="radio" id="paytype43" class="paytype" name="paytype" value="43"'.$checked.' /> <label for="paytype43" class="payment-label payment-ut-label">'.__('USDT','erphpdown').'</label>';
      						break;
      					case 'vpay-wx':
      						$html .= '<input type="radio" id="paytype32" class="paytype" name="paytype" value="32"'.$checked.' /> <label for="paytype32" class="payment-label payment-wxpay-label">'.__('微信','erphpdown').'</label>';
      						break;
      					case 'vpay-ali':
      						$html .= '<input type="radio" id="paytype31" class="paytype" name="paytype" value="31"'.$checked.' /> <label for="paytype31" class="payment-label payment-alipay-label">'.__('支付宝','erphpdown').'</label>';
      						break;
      					case 'usdt':
      						$html .= '<input type="radio" id="paytype50" class="paytype" name="paytype" value="50"'.$checked.' /> <label for="paytype50" class="payment-label payment-ut-label">'.__('USDT','erphpdown').'</label>';
      						break;
      					case 'stripe':
      						$html .= '<input type="radio" id="paytype60" class="paytype" name="paytype" value="60"'.$checked.' /> <label for="paytype60" class="payment-label payment-stripe-label">'.__('信用卡','erphpdown').'</label>';
      						break;
      					case 'ecpay':
      						$html .= '<input type="radio" id="paytype70" class="paytype" name="paytype" value="70"'.$checked.' /> <label for="paytype70" class="payment-label payment-ecpay-label">'.__('新台币','erphpdown').'</label>';
      						break;
      					case 'newebpay':
      						$html .= '<input type="radio" id="paytype80" class="paytype" name="paytype" value="80"'.$checked.' /> <label for="paytype80" class="payment-label payment-newebpay-label">'.__('新台币','erphpdown').'</label>';
      						break;
      					default:
      						break;
      				}
      				$pi ++;
      			}
      		}else{
      			if(get_option('erphpdown_usdt_address')){
						$html .= '<input type="radio" id="paytype50" class="paytype" name="paytype" value="50" checked />'.__('USDT','erphpdown').'&nbsp;';
					}
						if(get_option('ice_payapl_api_uid')){
	          	$html .= '<input type="radio" id="paytype2" class="paytype" checked name="paytype" value="2" />PayPal&nbsp;';
	          }
	          if(get_option('erphpdown_stripe_pk')){
						$html .= '<input type="radio" id="paytype60" class="paytype" name="paytype" value="60" checked />'.__('信用卡','erphpdown').'&nbsp;';
					}
				if(plugin_check_ecpay() && get_option('erphpdown_ecpay_MerchantID')){
						$html .= '<input type="radio" id="paytype70" class="paytype" name="paytype" value="70" checked />'.__('新台币','erphpdown').'&nbsp;';
					}
				if(plugin_check_newebpay() && get_option('erphpdown_newebpay_MerchantID')){
						$html .= '<input type="radio" id="paytype80" class="paytype" name="paytype" value="80" checked />'.__('新台币','erphpdown').'&nbsp;';
					}
	          if(get_option('ice_weixin_mchid')){ 
	          	$html .= '<input type="radio" id="paytype4" class="paytype" checked name="paytype" value="4" />'.__('微信','erphpdown').'&nbsp;';
	          }
	          if(get_option('ice_ali_partner') || get_option('ice_ali_app_id')){ 
	          	$html .= '<input type="radio" id="paytype1" class="paytype" checked name="paytype" value="1" />'.__('支付宝','erphpdown').'&nbsp;';
	          }
	          if(get_option('erphpdown_f2fpay_id')){
							$html .= '<input type="radio" id="paytype5" class="paytype" checked name="paytype" value="5" />'.__('支付宝','erphpdown').'&nbsp;';
						}
						if(get_option('erphpdown_payjs_appid')){
							if(!get_option('erphpdown_payjs_alipay')){ $html .= '<input type="radio" id="paytype20" class="paytype" name="paytype" value="20" checked />'.__('支付宝','erphpdown').'&nbsp;';}
							if(!get_option('erphpdown_payjs_wxpay')){ $html .= '<input type="radio" id="paytype19" class="paytype" name="paytype" value="19" checked />'.__('微信','erphpdown').'&nbsp;';}
						}
						if(get_option('erphpdown_xhpay_appid31')){
							$html .= '<input type="radio" id="paytype18" class="paytype" name="paytype" value="18" checked />'.__('微信','erphpdown').'&nbsp;';
						}
						if(get_option('erphpdown_xhpay_appid32')){
							$html .= '<input type="radio" id="paytype17" class="paytype" name="paytype" value="17" checked />'.__('支付宝','erphpdown').'&nbsp;';     
						}
	          if(get_option('erphpdown_codepay_appid')){
	          	if(!get_option('erphpdown_codepay_alipay')){ $html .= '<input type="radio" id="paytype13" class="paytype" name="paytype" value="13" checked  />'.__('支付宝','erphpdown').'&nbsp;';}
	          	if(!get_option('erphpdown_codepay_wxpay')){ $html .= '<input type="radio" id="paytype14" class="paytype" name="paytype" value="14" />'.__('微信','erphpdown').'&nbsp;';}
	          	if(!get_option('erphpdown_codepay_qqpay')){ $html .= '<input type="radio" id="paytype15" class="paytype" name="paytype" value="15" />'.__('QQ钱包','erphpdown').'&nbsp;';}
	          }
	          if(get_option('erphpdown_paypy_key')){
	          	if(!get_option('erphpdown_paypy_alipay')){ $html .= '<input type="radio" id="paytype8" class="paytype" name="paytype" value="8" checked />'.__('支付宝','erphpdown').'&nbsp;';}
							if(!get_option('erphpdown_paypy_wxpay')){ $html .= '<input type="radio" id="paytype7" class="paytype" name="paytype" value="7" checked />'.__('微信','erphpdown').'&nbsp;';}  
						}
						if(get_option('erphpdown_epay_id')){
							if(!get_option('erphpdown_epay_alipay')){ $html .= '<input type="radio" id="paytype21" class="paytype" name="paytype" value="21" checked />'.__('支付宝','erphpdown').'&nbsp;';}
							if(!get_option('erphpdown_epay_qqpay')){ $html .= '<input type="radio" id="paytype23" class="paytype" name="paytype" value="23" checked />'.__('QQ钱包','erphpdown').'&nbsp;';}
							if(!get_option('erphpdown_epay_wxpay')){ $html .= '<input type="radio" id="paytype22" class="paytype" name="paytype" value="22" checked />'.__('微信','erphpdown').'&nbsp;';}
						}
						if(get_option('erphpdown_easepay_id')){
							if(!get_option('erphpdown_easepay_usdt')){ $html .= '<input type="radio" id="paytype43" class="paytype" name="paytype" value="43" checked />'.__('USDT','erphpdown').'&nbsp;';}
							if(!get_option('erphpdown_easepay_alipay')){ $html .= '<input type="radio" id="paytype41" class="paytype" name="paytype" value="41" checked />'.__('支付宝','erphpdown').'&nbsp;';}
							if(!get_option('erphpdown_easepay_wxpay')){ $html .= '<input type="radio" id="paytype42" class="paytype" name="paytype" value="42" checked />'.__('微信','erphpdown').'&nbsp;';}
						}
	          if(get_option('erphpdown_vpay_key')){
							if(!get_option('erphpdown_vpay_alipay')){ $html .= '<input type="radio" id="paytype31" class="paytype" name="paytype" value="31" checked />'.__('支付宝','erphpdown').'&nbsp;';}
							if(!get_option('erphpdown_vpay_wxpay')){ $html .= '<input type="radio" id="paytype32" class="paytype" name="paytype" value="32" checked />'.__('微信','erphpdown');}
						}
					}
					
					$html .='</td>
				</tr>
				<tr>
					<td colspan="2"><input type="submit" value="'.__('立即充值','erphpdown').'" class="erphpdown-sc-btn" /></td>
				</tr> 
			</table>
		</form>
	</div>';
	return $html;
}


//提现申请
function erphpdown_sc_withdraw() {
	if(!is_user_logged_in()){
		return '请先登录！';
	}
	global $wpdb, $current_user;
	$fee=get_option("ice_ali_money_site");
	$fee=isset($fee) ?$fee :100;

	$ice_ali_money_site = get_user_meta($current_user->ID,'ice_ali_money_site',true);
	if($ice_ali_money_site != '' && ($ice_ali_money_site || $ice_ali_money_site == 0)){
		$fee = $ice_ali_money_site;
	}

	$erphp_aff_money = get_option('erphp_aff_money');
	$okMoney = erphpGetUserOkMoney();
	if($erphp_aff_money){
		$okMoney = erphpGetUserOkAff();
	}

	$error = '';
	if(isset($_POST['ice_alipay'])) {

		$ice_alipay = esc_sql($_POST['ice_alipay']);
		$ice_name   = esc_sql($_POST['ice_name']);
		$ice_money  = isset($_POST['ice_money']) && is_numeric($_POST['ice_money']) ?esc_sql($_POST['ice_money']) :0;
		if($ice_money<get_option('ice_ali_money_limit'))
		{
			$error =  "<div class='error'>".__('提现金额不得低于','erphpdown').get_option('ice_ali_money_limit').get_option('ice_name_alipay')."</div>";
		}
		elseif(empty($ice_name) || empty($ice_alipay))
		{
			$error =  "<div class='error'>".__('请输入支付宝帐号和姓名','erphpdown')."</div>";
		}
		elseif($ice_money > $okMoney)
		{
			$error =  "<div class='error'>".__('余额不足','erphpdown')."</div>";
		}
		else
		{
	
			$sql="insert into ".$wpdb->iceget."(ice_money,ice_user_id,ice_time,ice_success,ice_success_time,ice_note,ice_name,ice_alipay)values
				('".$ice_money."','".$current_user->ID."','".date("Y-m-d H:i:s")."',0,'".date("Y-m-d H:i:s")."','','$ice_name','$ice_alipay')";
			if($wpdb->query($sql))
			{
				if($erphp_aff_money){
					addUserAffXiaoFei($current_user->ID, $ice_money);
				}else{
					addUserMoney($current_user->ID, '-'.$ice_money);
				}
				$error = "<div class='updated'>".__('申请成功','erphpdown')."</div>";
			}
			else
			{
				$error = "<div class='error'>".__('系统超时，请稍后重试','erphpdown')."</div>";
			}
		}
	}
	$userAli=$wpdb->get_row("select * from ".$wpdb->iceget." where ice_user_id=".$current_user->ID);

	$html = '<div class="erphpdown-sc">
		<h2>'.__('申请提现','erphpdown').'</h2>'.$error.'
		<form method="post">
			<table class="erphpdown-sc-table">
				<tr>
					<td valign="top" class="erphpdown-sc-td-title">'.__('支付宝','erphpdown').'</td>
					<td>
							<input type="text" class="erphpdown-sc-input" id="ice_alipay" name="ice_alipay" maxlength="50" size="50" value="'.($userAli?$userAli->ice_alipay:'').'" required />
	
					</td>
				</tr>
				<tr>
					<td valign="top" class="erphpdown-sc-td-title">'.__('姓名','erphpdown').'</td>
					<td>
							<input type="text" class="erphpdown-sc-input" id="ice_name" name="ice_name" maxlength="50" size="50" value="'.($userAli? $userAli->ice_alipay:'').'" required />
					</td>
				</tr>
				 <tr>
					<td valign="top" class="erphpdown-sc-td-title">'.__('手续费','erphpdown').'</td>
					<td>
					'.$fee.'%
					</td>
				</tr>
				<tr>
					<td>'.__('提现金额','erphpdown').'</td>
					<td>
					<input type="text" class="erphpdown-sc-input" id="ice_money" name="ice_money" maxlength="50" size="50" placeholder="'.get_option('ice_name_alipay').'" required />
					</td>				
				</tr>
				<tr valign="top" class="erphpdown-sc-td-title"><td>'.__('可提现余额','erphpdown').'</td>
				<td>'.sprintf("%.2f",$okMoney).get_option('ice_name_alipay').'</td>
				</tr>
				<tr>
					<td colspan="2">
						<input type="submit" value="'.__('立即提现','erphpdown').'" class="erphpdown-sc-btn"/>
					</td>
				</tr> 
		</table>
	</form>
	</div>';
	return $html;
}


//取现列表
if(erphpdown_lock_url(substr(plugins_url('', __FILE__),'-18','-9'),'cvujz') != 'gxLmUkVVK9I8u3reMFrX8Vc'){
	exit();}
function erphpdown_sc_withdraws() {
	if(!is_user_logged_in()){
		return '请先登录！';
	}

	global $wpdb, $current_user;

	$fee=get_option("ice_ali_money_site");
	$fee=isset($fee) ?$fee :100;

	$ice_ali_money_site = get_user_meta($current_user->ID,'ice_ali_money_site',true);
	if($ice_ali_money_site != '' && ($ice_ali_money_site || $ice_ali_money_site == 0)){
		$fee = $ice_ali_money_site;
	}
	
	$totallists = $wpdb->get_var("SELECT count(*) FROM $wpdb->iceget WHERE ice_user_id=".$current_user->ID);
	$ice_perpage = 20;
	$pages = ceil($totallists / $ice_perpage);
	$page=isset($_GET['pp']) ?intval($_GET['pp']) :1;
	$offset = $ice_perpage*($page-1);
	$lists = $wpdb->get_results("SELECT * FROM $wpdb->iceget where ice_user_id=".$current_user->ID." order by ice_time DESC limit $offset,$ice_perpage");

	$html = '<div class="erphpdown-sc">
		<h2>'.__('提现列表','erphpdown').'</h2>
		<table class="erphpdown-sc-list">
			<thead>
				<tr>
					<th>'.__('时间','erphpdown').'</th>
					<th>'.__('申请金额','erphpdown').'</th>
					<th>'.__('到账金额','erphpdown').'</th>
					<th>'.__('状态','erphpdown').'</th>
					<th>'.__('备注','erphpdown').'</th>
				</tr>
			</thead>
			<tbody>';
			if($lists) {
				foreach($lists as $value)
				{
					$result=$value->ice_success==1?__('已完成','erphpdown'):'--';
					$html .= "<tr>\n";
					$html .= "<td>$value->ice_time</td>\n";
					$html .= "<td>$value->ice_money</td>\n";
					$html .= "<td>".sprintf("%.2f",(((100-$fee)*$value->ice_money)/100))."</td>\n";
					$html .= "<td>$result</td>\n";
					$html .= "<td>$value->ice_note</td>\n";
					$html .= "</tr>";
				}
			}
			else
			{
				$html .= '<tr><td colspan="5" align="center">'.__('暂无记录','erphpdown').'</td></tr>';
			}
		$html .='</tbody>
		</table>'.erphpdown_paging($page,$pages,'outmo').'
	</div>';
	return $html;
}


//广告订单
function erphpdown_sc_ad(){
	if(!is_user_logged_in()){
		return '请先登录！';
	}
	global $wpdb, $current_user;
	$total_trade = $wpdb->get_var("SELECT count(id) FROM $erphpad_table WHERE user_id=".$user_info->ID." and order_status=1");
	$ice_perpage = 20;
	$pages = ceil($total_trade / $ice_perpage);
	$page=isset($_GET['pp']) ?intval($_GET['pp']) :1;
	$offset = $ice_perpage*($page-1);
	$list = $wpdb->get_results("SELECT * FROM $erphpad_table where user_id=".$user_info->ID." and order_status=1 order by order_time DESC limit $offset,$ice_perpage");
	$html = '<div class="erphpdown-sc">
		<h2>'.__('广告订单','erphpdown').'</h2>
		<table class="erphpdown-sc-list">
			<thead>
				<tr>
					<th>'.__('广告位','erphpdown').'</th>
					<th>'.__('价格','erphpdown').'</th>
					<th>'.__('生效时间','erphpdown').'</th>
					<th>'.__('周期','erphpdown').'</th>
					<th>'.__('说明','erphpdown').'</th>
					<th>'.__('状态','erphpdown').'</th>			
					<th>'.__('操作','erphpdown').'</th>					
				</tr>
			</thead>
			<tbody>';
			if($list) {
				foreach($list as $value){
					$html .= "<tr>\n";
					$html .= "<td>".erphpad_get_pos_name($value->pos_id)."</td>\n";
					$html .= "<td>$value->order_price</td>";
					$html .= "<td>$value->order_time</td>\n";
					$html .= "<td>".$value->order_cycle."天</td>\n";
					$html .= "<td class='tit'>".erphpad_get_pos($value->pos_id)->pos_tips."</td>\n";
					$html .= "<td>".($value->order_status == 1?__('正常','erphpdown'):__('过期','erphpdown'))."</td>\n";
					$html .= "<td><a href='javascript:;' data-id='".$value->id."' class='erphpad-edit-loader'>'.__('修改','erphpdown').'</a></td>\n";
					$html .= "</tr>";
				}
			}else{
				$html .= '<tr><td colspan="7" align="center">'.__('暂无记录','erphpdown').'</td></tr>';
			}
	$html .= '</tbody>
		</table>'.erphpdown_paging($page,$pages,'ad').'
		<form id="uploadad" action="'.ERPHPAD_URL.'/action/ad.php" method="post" enctype="multipart/form-data" style="display:none;">
        <input type="file" id="adimage" name="adimage" accept="image/png, image/jpeg, image/gif">
    </form>
	</div>';
	return $html;
}

//团购订单
function erphpdown_sc_tuan(){
	if(!is_user_logged_in()){
		return '请先登录！';
	}
	global $wpdb, $current_user;
	$total_trade = $wpdb->get_var("SELECT count(ice_id) FROM $wpdb->tuanorder WHERE ice_user_id=".$current_user->ID." and ice_status>0");
	$ice_perpage = 20;
	$pages = ceil($total_trade / $ice_perpage);
	$page=isset($_GET['pp']) ?intval($_GET['pp']) :1;
	$offset = $ice_perpage*($page-1);
	$list = $wpdb->get_results("SELECT * FROM $wpdb->tuanorder where ice_user_id=".$current_user->ID." and ice_status>0 order by ice_time DESC limit $offset,$ice_perpage");
	$html = '<div class="erphpdown-sc">
		<h2>'.__('拼团订单','erphpdown').'</h2>
		<table class="erphpdown-sc-list">
			<thead>
				<tr>
					<th>'.__('名称','erphpdown').'</th>
					<th>'.__('订单号','erphpdown').'</th>
					<th>'.__('价格','erphpdown').'</th>
					<th>'.__('时间','erphpdown').'</th>
					<th>'.__('进度','erphpdown').'</th>			
					<th>'.__('状态','erphpdown').'</th>						
				</tr>
			</thead>
			<tbody>';
			if($list) {
				foreach($list as $value){
					$html .= "<tr>\n";
					$html .= "<td class='tit'><a href='".get_permalink($value->ice_post)."' target='_blank'>".get_post($value->ice_post)->post_title."</a></td>\n";
					$html .= "<td>$value->ice_num</td>";
					$html .= "<td>$value->ice_price</td>\n";
					$html .= "<td>$value->ice_time</td>\n";
					$html .= "<td>".get_erphpdown_tuan_percent($value->ice_post,$value->ice_tuan_num)."%</td>\n";
					$html .= "<td>".($value->ice_status == 1?__('进行中','erphpdown'):__('已完成','erphpdown'))."</td>\n";
					$html .= "</tr>";
				}
			}else{
				$html .= '<tr><td colspan="6" align="center">'.__('暂无记录','erphpdown').'</td></tr>';
			}
	$html .= '</tbody>
		</table>'.erphpdown_paging($page,$pages,'tuan').'
	</div>';
	return $html;
}








add_shortcode('buy','erphpdown_shortcode_buy');
function erphpdown_shortcode_buy($atts){
	$atts = shortcode_atts( array(
        'id' => '',
        'buy' => '立即购买',
        'down' => '立即下载'
    ), $atts, 'buy' );

  date_default_timezone_set('Asia/Shanghai'); 
	global $post,$wpdb;

	if($atts['id']) {
		$post_id = $atts['id'];
	}else{
		$post_id = $post->ID;
	}

	$memberDown=get_post_meta($post_id, 'member_down',TRUE);
	$start_down=get_post_meta($post_id, 'start_down', true);
	$days=get_post_meta($post_id, 'down_days', true);
	$price=get_post_meta($post_id, 'down_price', true);
	$userType=getUsreMemberType();
	if(is_single()){
		$categories = get_the_category();
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
	}
	$down_info=null;

	if(is_user_logged_in()){
		$user_info=wp_get_current_user();
		$down_info=$wpdb->get_row("select * from ".$wpdb->icealipay." where ice_post='".$post_id."' and ice_success=1 and (ice_index is null or ice_index = '') and ice_user_id=".$user_info->ID." order by ice_time desc");
		if($days > 0 && $down_info){
			$lastDownDate = date('Y-m-d H:i:s',strtotime('+'.$days.' day',strtotime($down_info->ice_time)));
			$nowDate = date('Y-m-d H:i:s');
			if(strtotime($nowDate) > strtotime($lastDownDate)){
				$down_info = null;
			}
		}
	}

	$user_id = is_user_logged_in() ? wp_get_current_user()->ID : 0;
	$wppay = new EPD($post_id, $user_id);
	
	if( ($userType && ($memberDown==3 || $memberDown==4)) || $wppay->isWppayPaid() || $wppay->isWppayPaidNew() || $down_info || (($memberDown==15 || $memberDown==16) && $userType >= 8) || (($memberDown==6 || $memberDown==8) && $userType >= 9) || (($memberDown==7 || $memberDown==9 || $memberDown==13 || $memberDown==14 || $memberDown==20) && $userType == 10) || (!$price && $memberDown!=4 && $memberDown!=15 && $memberDown!=8 && $memberDown!=9)){
		if($start_down){
			return "<a href='".constant("erphpdown").'download.php?postid='.$post_id."&timestamp=".time()."' class='erphpdown-down' target='_blank'>".$atts['down']."</a>";
		}else{
			return '';
		}
	}else{
		return '<a class="erphpdown-iframe erphpdown-buy" href="'.constant("erphpdown").'buy.php?postid='.$post_id.'&timestamp='.time().'" target="_blank" >'.$atts['buy'].'</a>';
	}
}

add_shortcode('box','erphpdown_shortcode_box');
function erphpdown_shortcode_box(){
	date_default_timezone_set('Asia/Shanghai'); 
	global $post, $wpdb;
	$erphp_down=get_post_meta(get_the_ID(), 'erphp_down', true);
	$start_down=get_post_meta(get_the_ID(), 'start_down', true);
	$start_down2=get_post_meta(get_the_ID(), 'start_down2', true);
	$days=get_post_meta(get_the_ID(), 'down_days', true);
	$price=get_post_meta(get_the_ID(), 'down_price', true);
	$price_type=get_post_meta(get_the_ID(), 'down_price_type', true);
	$url=get_post_meta(get_the_ID(), 'down_url', true);
	$urls=get_post_meta(get_the_ID(), 'down_urls', true);
	$url_free=get_post_meta(get_the_ID(), 'down_url_free', true);
	$memberDown=get_post_meta(get_the_ID(), 'member_down',TRUE);
	$hidden=get_post_meta(get_the_ID(), 'hidden_content', true);
	$userType=getUsreMemberType();
	if(is_single()){
		$categories = get_the_category();
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
	}
	$down_info = null;$downMsgFree = '';$downMsg = '';$yituan = '';$down_tuan=0;$iframe='';$erphp_popdown='';$down_checkpan = '';$down_repeat=0;$down_info_repeat=null;$down_can = 0;

	$erphp_life_name    = get_option('erphp_life_name')?get_option('erphp_life_name'):'终身VIP';
	$erphp_year_name    = get_option('erphp_year_name')?get_option('erphp_year_name'):'包年VIP';
	$erphp_quarter_name = get_option('erphp_quarter_name')?get_option('erphp_quarter_name'):'包季VIP';
	$erphp_vip_name  = get_option('erphp_vip_name')?get_option('erphp_vip_name'):'VIP';

	$erphp_box_down_title = get_option('erphp_box_down_title');
	$erphp_box_see_title = get_option('erphp_box_see_title');
	$erphp_box_faka_title = get_option('erphp_box_faka_title');

	$erphp_vip_discounts = $erphp_vip_name.'折扣';

	if(get_option('erphp_popdown')){
		$erphp_popdown=' erphpdown-down-layui';
		$iframe = '&iframe=1';
	}

	if(function_exists('erphpdown_tuan_install')){
		$down_tuan=get_post_meta(get_the_ID(), 'down_tuan', true);
	}

	$down_repeat = get_post_meta(get_the_ID(), 'down_repeat', true);
	$down_user_ones=get_post_meta(get_the_ID(), 'down_user_ones',true);
	if($down_user_ones){
		if(is_user_logged_in()){
			global $current_user;
			$down_user_ones_arr = explode(',', str_replace('，', ',', $down_user_ones));
			if(is_array($down_user_ones_arr) && count($down_user_ones_arr) && in_array($current_user->user_login, $down_user_ones_arr)){
				$down_repeat = 1;
			}
		}
	}
	
	$erphp_url_front_vip = get_bloginfo('wpurl').'/wp-admin/admin.php?page=erphpdown/admin/erphp-update-vip.php';
	if(get_option('erphp_url_front_vip')){
		$erphp_url_front_vip = get_option('erphp_url_front_vip');
	}
	$erphp_url_front_login = wp_login_url(get_permalink());
	if(get_option('erphp_url_front_login')){
		$erphp_url_front_login = get_option('erphp_url_front_login');
	}
	if(is_user_logged_in()){
		$erphp_url_front_vip2 = $erphp_url_front_vip;
	}else{
		$erphp_url_front_vip2 = $erphp_url_front_login;
	}

	$erphp_blank_domains = get_option('erphp_blank_domains')?get_option('erphp_blank_domains'):'pan.baidu.com';
	$erphp_colon_domains = get_option('erphp_colon_domains')?get_option('erphp_colon_domains'):'pan.baidu.com';

	$content = '';

	if($url_free){
		$downMsgFree .= '<div class="erphpdown-title">免费资源</div><div class="erphpdown-free">';
		$downList=explode("\r\n",$url_free);
		foreach ($downList as $k=>$v){
			$filepath = $downList[$k];
			if($filepath){

				if($erphp_colon_domains){
					$erphp_colon_domains_arr = explode(',', $erphp_colon_domains);
					foreach ($erphp_colon_domains_arr as $erphp_colon_domain) {
						if(strpos($filepath, $erphp_colon_domain)){
							$filepath = str_replace('：', ': ', $filepath);
							break;
						}
					}
				}

				$erphp_blank_domain_is = 0;
				if($erphp_blank_domains){
					$erphp_blank_domains_arr = explode(',', $erphp_blank_domains);
					foreach ($erphp_blank_domains_arr as $erphp_blank_domain) {
						if(strpos($filepath, $erphp_blank_domain)){
							$erphp_blank_domain_is = 1;
							break;
						}
					}
				}

				if(strpos($filepath,',')){
					$filearr = explode(',',$filepath);
					$arrlength = count($filearr);
					if($arrlength == 1){
						$downMsgFree.="<div class='erphpdown-item'>文件".($k+1)."地址<a href='".$filepath."' rel='nofollow' target='_blank' class='erphpdown-down'>点击下载</a></div>";
					}elseif($arrlength == 2){
						$downMsgFree.="<div class='erphpdown-item'>".$filearr[0]."<a href='".$filearr[1]."' rel='nofollow' target='_blank' class='erphpdown-down'>点击下载</a></div>";
					}elseif($arrlength == 3){
						$filearr2 = str_replace('：', ': ', $filearr[2]);
						$downMsgFree.="<div class='erphpdown-item'>".$filearr[0]."<a href='".$filearr[1]."' rel='nofollow' target='_blank' class='erphpdown-down'>点击下载</a>".$filearr2."<a class='erphpdown-copy' data-clipboard-text='".str_replace('提取码: ', '', $filearr2)."' href='javascript:;'>复制</a></div>";
					}
				}elseif(strpos($filepath,'  ') && $erphp_blank_domain_is){
					$filearr = explode('  ',$filepath);
					$arrlength = count($filearr);
					if($arrlength == 1){
						$downMsgFree.="<div class='erphpdown-item'>文件".($k+1)."地址<a href='".$filepath."' rel='nofollow' target='_blank' class='erphpdown-down'>点击下载</a></div>";
					}elseif($arrlength >= 2){
						$filearr2 = explode(':',$filearr[0]);
						$filearr3 = explode(':',$filearr[1]);
						$downMsgFree.="<div class='erphpdown-item'>".$filearr2[0]."<a href='".trim($filearr2[1].':'.$filearr2[2])."' target='_blank' rel='nofollow' class='erphpdown-down'>点击下载</a>提取码: ".trim($filearr3[1])."<a class='erphpdown-copy' data-clipboard-text='".trim($filearr3[1])."' href='javascript:;'>复制</a></div>";
					}
				}elseif(strpos($filepath,' ') && $erphp_blank_domain_is){
					$filearr = explode(' ',$filepath);
					$arrlength = count($filearr);
					if($arrlength == 1){
						$downMsgFree.="<div class='erphpdown-item'>文件".($k+1)."地址<a href='".$filepath."' rel='nofollow' target='_blank' class='erphpdown-down'>点击下载</a></div>";
					}elseif($arrlength == 2){
						$downMsgFree.="<div class='erphpdown-item'>".$filearr[0]."<a href='".$filearr[1]."' rel='nofollow' target='_blank' class='erphpdown-down'>点击下载</a></div>";
					}elseif($arrlength >= 3){
						$downMsgFree.="<div class='erphpdown-item'>".str_replace(':', '', $filearr[0])."<a href='".$filearr[1]."' target='_blank' rel='nofollow' class='erphpdown-down'>点击下载</a>".$filearr[2].' '.$filearr[3]."<a class='erphpdown-copy' data-clipboard-text='".$filearr[3]."' href='javascript:;'>复制</a></div>";
					}
				}else{
					$downMsgFree.="<div class='erphpdown-item'>文件".($k+1)."地址<a href='".$filepath."' rel='nofollow' target='_blank' class='erphpdown-down'>点击下载</a></div>";
				}
			}
		}

		$downMsgFree .= '</div>';
		if(get_option('ice_tips_free')) $downMsgFree.='<div class="erphpdown-tips erphpdown-tips-free">'.get_option('ice_tips_free').'</div>';
		if($start_down2 || $start_down){
			$downMsgFree .= '<div class="erphpdown-title">付费资源</div>';
		}
	}
	
	if($start_down2){
		if($url){
			if(function_exists('epd_check_pan_callback')){
				if(strpos($url,'pan.baidu.com') !== false || (strpos($url,'lanzou') !== false && strpos($url,'.com') !== false) || strpos($url,'cloud.189.cn') !== false){
					$down_checkpan = '<a class="erphpdown-buy erphpdown-checkpan2" href="javascript:;" data-id="'.get_the_ID().'" data-post="'.get_the_ID().'">点击检测网盘有效后购买</a>';
				}
			}

			$content.='<fieldset class="erphpdown erphpdown-default" id="erphpdown" style="display:block"><legend>'.($erphp_box_down_title?$erphp_box_down_title:'资源下载').'</legend>'.$downMsgFree;
			
			$user_id = is_user_logged_in() ? wp_get_current_user()->ID : 0;
			$wppay = new EPD(get_the_ID(), $user_id);
			$ews_erphpdown = get_option("ews_erphpdown");
			if($wppay->isWppayPaid() || $wppay->isWppayPaidNew() || !$price || ($memberDown == 3 && $userType) || ($memberDown == 16 && $userType >= 8) || ($memberDown == 6 && $userType >= 9) || ($memberDown == 7 && $userType >= 10) || ($ews_erphpdown && function_exists("ews_erphpdown") && isset($_COOKIE['ewd_'.get_the_ID()]) && $_COOKIE['ewd_'.get_the_ID()] == md5(get_the_ID().get_option('erphpdown_downkey')) )){
				$down_can = 1;
				$downList=explode("\r\n",trim($url));
				foreach ($downList as $k=>$v){
					$filepath = trim($downList[$k]);
					if($filepath){

						if($erphp_colon_domains){
							$erphp_colon_domains_arr = explode(',', $erphp_colon_domains);
							foreach ($erphp_colon_domains_arr as $erphp_colon_domain) {
								if(strpos($filepath, $erphp_colon_domain)){
									$filepath = str_replace('：', ': ', $filepath);
									break;
								}
							}
						}

						$erphp_blank_domain_is = 0;
						if($erphp_blank_domains){
							$erphp_blank_domains_arr = explode(',', $erphp_blank_domains);
							foreach ($erphp_blank_domains_arr as $erphp_blank_domain) {
								if(strpos($filepath, $erphp_blank_domain)){
									$erphp_blank_domain_is = 1;
									break;
								}
							}
						}

						if(strpos($filepath,',')){
							$filearr = explode(',',$filepath);
							$arrlength = count($filearr);
							if($arrlength == 1){
								$downMsg.="<div class='erphpdown-item'>文件".($k+1)."地址<a href='".ERPHPDOWN_URL."/download.php?postid=".get_the_ID()."&key=".($k+1)."&nologin=1&timestamp=".time()."' target='_blank' class='erphpdown-down'>点击下载</a></div>";
							}elseif($arrlength == 2){
								$downMsg.="<div class='erphpdown-item'>".$filearr[0]."<a href='".ERPHPDOWN_URL."/download.php?postid=".get_the_ID()."&key=".($k+1)."&nologin=1&timestamp=".time()."' target='_blank' class='erphpdown-down'>点击下载</a></div>";
							}elseif($arrlength == 3){
								$filearr2 = str_replace('：', ': ', $filearr[2]);
								$downMsg.="<div class='erphpdown-item'>".$filearr[0]."<a href='".ERPHPDOWN_URL."/download.php?postid=".get_the_ID()."&key=".($k+1)."&nologin=1&timestamp=".time()."' target='_blank' class='erphpdown-down'>点击下载</a>（".$filearr2."）<a class='erphpdown-copy' data-clipboard-text='".str_replace('提取码: ', '', $filearr2)."' href='javascript:;'>复制</a></div>";
							}
						}elseif(strpos($filepath,'  ') && $erphp_blank_domain_is){
							$filearr = explode('  ',$filepath);
							$arrlength = count($filearr);
							if($arrlength == 1){
								$downMsg.="<div class='erphpdown-item'>文件".($k+1)."地址<a href='".ERPHPDOWN_URL."/download.php?postid=".get_the_ID()."&key=".($k+1)."&nologin=1&timestamp=".time()."' target='_blank' class='erphpdown-down'>点击下载</a></div>";
							}elseif($arrlength >= 2){
								$filearr2 = explode(':',$filearr[0]);
								$filearr3 = explode(':',$filearr[1]);
								$downMsg.="<div class='erphpdown-item'>".$filearr2[0]."<a href='".ERPHPDOWN_URL."/download.php?postid=".get_the_ID()."&key=".($k+1)."&nologin=1&timestamp=".time()."' target='_blank' class='erphpdown-down'>点击下载</a>（提取码: ".trim($filearr3[1])."）<a class='erphpdown-copy' data-clipboard-text='".trim($filearr3[1])."' href='javascript:;'>复制</a></div>";
							}
						}elseif(strpos($filepath,' ') && $erphp_blank_domain_is){
							$filearr = explode(' ',$filepath);
							$arrlength = count($filearr);
							if($arrlength == 1){
								$downMsg.="<div class='erphpdown-item'>文件".($k+1)."地址<a href='".ERPHPDOWN_URL."/download.php?postid=".get_the_ID()."&key=".($k+1)."&nologin=1&timestamp=".time()."' target='_blank' class='erphpdown-down'>点击下载</a></div>";
							}elseif($arrlength == 2){
								$downMsg.="<div class='erphpdown-item'>".$filearr[0]."<a href='".ERPHPDOWN_URL."/download.php?postid=".get_the_ID()."&key=".($k+1)."&nologin=1&timestamp=".time()."' target='_blank' class='erphpdown-down'>点击下载</a></div>";
							}elseif($arrlength >= 3){
								$downMsg.="<div class='erphpdown-item'>".str_replace(':', '', $filearr[0])."<a href='".ERPHPDOWN_URL."/download.php?postid=".get_the_ID()."&key=".($k+1)."&nologin=1&timestamp=".time()."' target='_blank' class='erphpdown-down'>点击下载</a>（".$filearr[2].' '.$filearr[3]."）<a class='erphpdown-copy' data-clipboard-text='".$filearr[3]."' href='javascript:;'>复制</a></div>";
							}
						}else{
							$downMsg.="<div class='erphpdown-item'>文件".($k+1)."地址<a href='".$filepath."' target='_blank' class='erphpdown-down'>点击下载</a></div>";
						}
					}
				}
				$content .= $downMsg;	
				if($hidden){
					$content .= '<div class="erphpdown-item">提取码：'.$hidden.' <a class="erphpdown-copy" data-clipboard-text="'.$hidden.'" href="javascript:;">复制</a></div>';
				}
			}else{
				if($url){
					$tname = $erphp_box_down_title?$erphp_box_down_title:'资源下载';
				}else{
					$tname = $erphp_box_see_title?$erphp_box_see_title:'内容查看';
				}
				if($memberDown == 3 || $memberDown == 16 || $memberDown == 6 || $memberDown == 7){
					$wppay_vip_name = $erphp_vip_name;
					if($memberDown == 16){
						$wppay_vip_name = $erphp_quarter_name;
					}elseif($memberDown == 6){
						$wppay_vip_name = $erphp_year_name;
					}elseif($memberDown == 7){
						$wppay_vip_name = $erphp_life_name;
					}
					if($down_checkpan) $content .= $tname.'价格<span class="erphpdown-price">'.$price.'</span>元'.$down_checkpan.'&nbsp;&nbsp;<b>或</b>&nbsp;&nbsp;升级'.$wppay_vip_name.'后免费<a href="'.$erphp_url_front_vip2.'" target="_blank" class="erphpdown-vip'.(is_user_logged_in()?'':' erphp-login-must').'">升级'.$wppay_vip_name.'</a>';
					else $content .= $tname.'价格<span class="erphpdown-price">'.$price.'</span>元<a href="javascript:;" class="erphp-wppay-loader erphpdown-buy" data-post="'.get_the_ID().'">立即购买</a>&nbsp;&nbsp;<b>或</b>&nbsp;&nbsp;升级'.$wppay_vip_name.'后免费<a href="'.$erphp_url_front_vip2.'" target="_blank" class="erphpdown-vip'.(is_user_logged_in()?'':' erphp-login-must').'">升级'.$wppay_vip_name.'</a>';
				}else{
					if($down_checkpan) $content .= $tname.'价格<span class="erphpdown-price">'.$price.'</span>元'.$down_checkpan;
					else $content .= $tname.'价格<span class="erphpdown-price">'.$price.'</span>元<a href="javascript:;" class="erphp-wppay-loader erphpdown-buy" data-post="'.get_the_ID().'">立即购买</a>';	
				}

				$ews_erphpdown = get_option("ews_erphpdown");
				if(!$down_can && $ews_erphpdown && function_exists("ews_erphpdown")){
					$ews_erphpdown_btn = get_option("ews_erphpdown_btn");
					$ews_erphpdown_btn = $ews_erphpdown_btn?$ews_erphpdown_btn:'关注公众号免费下载';
					$content.='<a class="erphpdown-buy ews-erphpdown-button" data-id="'.get_the_ID().'" href="javascript:;">'.$ews_erphpdown_btn.'</a>';
				}
			}
			
			if(get_option('ice_tips')) $content.='<div class="erphpdown-tips">'.get_option('ice_tips').'</div>';
			$content.='</fieldset>';
		}

	}elseif($start_down){
		$tuanHtml = '';
		$content.='<fieldset class="erphpdown erphpdown-default" id="erphpdown" style="display:block"><legend>'.($erphp_box_down_title?$erphp_box_down_title:'资源下载').'</legend>'.$downMsgFree;
		if($down_tuan == '2' && function_exists('erphpdown_tuan_install')){
			$tuanHtml = erphpdown_tuan_html();
			$content .= $tuanHtml;
		}else{
			if($price_type){
				if($urls){
					$cnt = count($urls['index']);
    			if($cnt){
    				for($i=0; $i<$cnt;$i++){
    					$index = $urls['index'][$i];
    					$index_name = $urls['name'][$i];
    					$price = $urls['price'][$i];
    					$index_url = $urls['url'][$i];
    					$index_vip = $urls['vip'][$i];

    					$indexMemberDown = $memberDown;
    					if($index_vip){
    						$indexMemberDown = $index_vip;
    					}

    					$down_checkpan = '';
    					if(function_exists('epd_check_pan_callback')){
								if(strpos($index_url,'pan.baidu.com') !== false || (strpos($index_url,'lanzou') !== false && strpos($index_url,'.com') !== false) || strpos($index_url,'cloud.189.cn') !== false){
									$down_checkpan = '<a class="erphpdown-buy erphpdown-checkpan" href="javascript:;" data-id="'.get_the_ID().'" data-index="'.$index.'" data-buy="'.constant("erphpdown").'buy.php?postid='.get_the_ID().'&index='.$index.'">点击检测网盘有效后购买</a>';
								}
							}
            					
    					$content .= '<fieldset class="erphpdown-child"><legend>'.$index_name.'</legend>';
	    				if(is_user_logged_in() || get_option('erphp_wppay_down') || ( ($userType && ($indexMemberDown==3 || $indexMemberDown==4)) || (($indexMemberDown==15 || $indexMemberDown==16) && $userType >= 8) || (($indexMemberDown==6 || $indexMemberDown==8) && $userType >= 9) || (($indexMemberDown==7 || $indexMemberDown==9 || $indexMemberDown==13 || $indexMemberDown==14 || $indexMemberDown==20) && $userType == 10) )){
								if($price){
									if($indexMemberDown != 4 && $indexMemberDown != 15 && $indexMemberDown != 8 && $indexMemberDown != 9)
										$content.='此资源下载价格为<span class="erphpdown-price">'.$price.'</span>'.get_option("ice_name_alipay");
								}else{
									if($indexMemberDown != 4 && $indexMemberDown != 15 && $indexMemberDown != 8 && $indexMemberDown != 9)
										$content.='此资源仅限注册用户下载';
								}

								if($price || $indexMemberDown == 4 || $indexMemberDown == 15 || $indexMemberDown == 8 || $indexMemberDown == 9){
									global $wpdb;
									$user_info=wp_get_current_user();
									if($user_info->ID){
										$down_info=$wpdb->get_row("select * from ".$wpdb->icealipay." where ice_post='".get_the_ID()."' and ice_index='".$index."' and ice_success=1 and ice_user_id=".$user_info->ID." order by ice_time desc");
										if($days > 0 && $down_info){
											$lastDownDate = date('Y-m-d H:i:s',strtotime('+'.$days.' day',strtotime($down_info->ice_time)));
											$nowDate = date('Y-m-d H:i:s');
											if(strtotime($nowDate) > strtotime($lastDownDate)){
												$down_info = null;
											}
										}

										if($down_repeat){
											$down_info_repeat = $down_info;
											$down_info = null;
										}
									}

									$buyText = '立即购买';
									if($down_repeat && $down_info_repeat && !$down_info){
										$buyText = '再次购买';
									}

									$user_id = $user_info->ID;
									$wppay = new EPD(get_the_ID(), $user_id);

									if( ($userType && ($indexMemberDown==3 || $indexMemberDown==4)) || ($wppay->isWppayPaidNew($index) && !$down_repeat) || $down_info || (($indexMemberDown==15 || $indexMemberDown==16) && $userType >= 8) || (($indexMemberDown==6 || $indexMemberDown==8) && $userType >= 9) || (($indexMemberDown==7 || $indexMemberDown==9 || $indexMemberDown==13 || $indexMemberDown==14 || $indexMemberDown==20) && $userType == 10) || (!$price && $indexMemberDown!=4 && $indexMemberDown!=15 && $indexMemberDown!=8 && $indexMemberDown!=9)){

										if($indexMemberDown==3){
											$content.='（'.$erphp_vip_name.'免费）';
										}elseif($indexMemberDown==2){
											$content.='（'.$erphp_vip_name.' 5折）';
										}elseif($indexMemberDown==13){
											$content.='（'.$erphp_vip_name.' 5折、'.$erphp_life_name.'免费）';
										}elseif($indexMemberDown==5){
											$content.='（'.$erphp_vip_name.' 8折）';
										}elseif($indexMemberDown==14){
											$content.='（'.$erphp_vip_name.' 8折、'.$erphp_life_name.'免费）';
										}elseif($indexMemberDown==20){
											$content.='（'.$erphp_vip_discounts.'、'.$erphp_life_name.'免费）';
										}elseif($indexMemberDown==6){
											$content .= '（'.$erphp_year_name.'免费）';
										}elseif($indexMemberDown==7){
											$content .= '（'.$erphp_life_name.'免费）';
										}elseif($indexMemberDown==4){
											$content .= '（此资源仅限'.$erphp_vip_name.'下载）';
										}elseif($indexMemberDown == 15){
											$content .= '（此资源仅限'.$erphp_quarter_name.'下载）';
										}elseif($indexMemberDown == 8){
											$content .= '（此资源仅限'.$erphp_year_name.'下载）';
										}elseif($indexMemberDown == 9){
											$content .= '（此资源仅限'.$erphp_life_name.'下载）';
										}elseif ($indexMemberDown==10){
											$content .= '（仅限'.$erphp_vip_name.'购买）';
										}elseif ($indexMemberDown==17){
											$content .= '（仅限'.$erphp_quarter_name.'购买）';
										}elseif ($indexMemberDown==18){
											$content .= '（仅限'.$erphp_year_name.'购买）';
										}elseif ($indexMemberDown==19){
											$content .= '（仅限'.$erphp_life_name.'购买）';
										}elseif ($indexMemberDown==11){
											$content .= '（仅限'.$erphp_vip_name.'购买、'.$erphp_year_name.' 5折）';
										}elseif ($indexMemberDown==12){
											$content .= '（仅限'.$erphp_vip_name.'购买、'.$erphp_year_name.' 8折）';
										}

										$content.="<a href='".constant("erphpdown").'download.php?postid='.get_the_ID()."&index=".$index.$iframe."&timestamp=".time()."' class='erphpdown-down".$erphp_popdown."' target='_blank'>立即下载</a>";
									}else{
									
										$vipText = '<a href="'.$erphp_url_front_vip.'" target="_blank" class="erphpdown-vip">升级'.$erphp_vip_name.'</a>';
										if($userType){
											$vipText = '';
											if(($indexMemberDown == 13 || $indexMemberDown == 14 || $indexMemberDown == 20) && $userType < 10){
												$vipText = '<a href="'.$erphp_url_front_vip.'" target="_blank" class="erphpdown-vip">升级'.$erphp_life_name.'</a>';
											}
										}
										if($indexMemberDown==3){
											$content.='（'.$erphp_vip_name.'免费）'.$vipText;
										}elseif ($indexMemberDown==2){
											$content.='（'.$erphp_vip_name.' 5折）'.$vipText;
										}elseif ($indexMemberDown==13){
											$content.='（'.$erphp_vip_name.' 5折、'.$erphp_life_name.'免费）'.$vipText;
										}elseif ($indexMemberDown==5){
											$content.='（'.$erphp_vip_name.' 8折）'.$vipText;
										}elseif ($indexMemberDown==14){
											$content.='（'.$erphp_vip_name.' 8折、'.$erphp_life_name.'免费）'.$vipText;
										}elseif ($indexMemberDown==20){
											$content.='（'.$erphp_vip_discounts.'、'.$erphp_life_name.'免费）'.$vipText;
										}elseif ($indexMemberDown==16){
											if($userType < 8){
												$vipText = '<a href="'.$erphp_url_front_vip.'" target="_blank" class="erphpdown-vip">升级'.$erphp_quarter_name.'</a>';
											}
											$content.='（'.$erphp_quarter_name.'免费）'.$vipText;
										}elseif ($indexMemberDown==6){
											if($userType < 9){
												$vipText = '<a href="'.$erphp_url_front_vip.'" target="_blank" class="erphpdown-vip">升级'.$erphp_year_name.'</a>';
											}
											$content.='（'.$erphp_year_name.'免费）'.$vipText;
										}elseif ($indexMemberDown==7){
											if($userType < 10){
												$vipText = '<a href="'.$erphp_url_front_vip.'" target="_blank" class="erphpdown-vip">升级'.$erphp_life_name.'</a>';
											}
											$content.='（'.$erphp_life_name.'免费）'.$vipText;
										}elseif ($indexMemberDown==4){
											if($userType){
												$content.='此资源为'.$erphp_vip_name.'专享资源';
											}
										}elseif ($indexMemberDown==15){
											if($userType >= 9){
												$content.='此资源为'.$erphp_quarter_name.'专享资源';
											}
										}elseif ($indexMemberDown==8){
											if($userType >= 9){
												$content.='此资源为'.$erphp_year_name.'专享资源';
											}
										}elseif ($indexMemberDown==9){
											if($userType >= 10){
												$content.='此资源为'.$erphp_life_name.'专享资源';
											}
										}
										

										if($indexMemberDown==4){
											$content.='此资源仅限'.$erphp_vip_name.'下载<a href="'.$erphp_url_front_vip.'" class="erphpdown-vip">升级'.$erphp_vip_name.'</a>';
										}elseif($indexMemberDown==15){
											$content.='此资源仅限'.$erphp_quarter_name.'下载<a href="'.$erphp_url_front_vip.'" class="erphpdown-vip">升级'.$erphp_quarter_name.'</a>';
										}elseif($indexMemberDown==8){
											$content.='此资源仅限'.$erphp_year_name.'下载<a href="'.$erphp_url_front_vip.'" class="erphpdown-vip">升级'.$erphp_year_name.'</a>';
										}elseif($indexMemberDown==9){
											$content.='此资源仅限'.$erphp_life_name.'下载<a href="'.$erphp_url_front_vip.'" class="erphpdown-vip">升级'.$erphp_life_name.'</a>';
										}elseif($indexMemberDown==10){
											if($userType){
												$content.='（仅限'.$erphp_vip_name.'购买）';
												if($down_checkpan) $content .= $down_checkpan;
												else $content.='<a class="erphpdown-iframe erphpdown-buy" href='.constant("erphpdown").'buy.php?postid='.get_the_ID().'&index='.$index.' target="_blank">'.$buyText.'</a>';

												if($days){
													$content.= '（购买后'.$days.'天内可下载）';
												}
											}else{
												$content.='（仅限'.$erphp_vip_name.'购买）<a href="'.$erphp_url_front_vip.'" class="erphpdown-vip">升级'.$erphp_vip_name.'</a>';
											}
										}elseif($indexMemberDown==17){
											if($userType >= 8){
												$content.='（仅限'.$erphp_quarter_name.'购买）';
												if($down_checkpan) $content .= $down_checkpan;
												else $content.='<a class="erphpdown-iframe erphpdown-buy" href='.constant("erphpdown").'buy.php?postid='.get_the_ID().'&index='.$index.' target="_blank">'.$buyText.'</a>';

												if($days){
													$content.= '（购买后'.$days.'天内可下载）';
												}
											}else{
												$content.='（仅限'.$erphp_quarter_name.'购买）<a href="'.$erphp_url_front_vip.'" class="erphpdown-vip">升级'.$erphp_quarter_name.'</a>';
											}
										}elseif($indexMemberDown==18){
											if($userType >= 9){
												$content.='（仅限'.$erphp_year_name.'购买）';
												if($down_checkpan) $content .= $down_checkpan;
												else $content.='<a class="erphpdown-iframe erphpdown-buy" href='.constant("erphpdown").'buy.php?postid='.get_the_ID().'&index='.$index.' target="_blank">'.$buyText.'</a>';

												if($days){
													$content.= '（购买后'.$days.'天内可下载）';
												}
											}else{
												$content.='（仅限'.$erphp_year_name.'购买）<a href="'.$erphp_url_front_vip.'" class="erphpdown-vip">升级'.$erphp_year_name.'</a>';
											}
										}elseif($indexMemberDown==19){
											if($userType == 10){
												$content.='（仅限'.$erphp_life_name.'购买）';
												if($down_checkpan) $content .= $down_checkpan;
												else $content.='<a class="erphpdown-iframe erphpdown-buy" href='.constant("erphpdown").'buy.php?postid='.get_the_ID().'&index='.$index.' target="_blank">'.$buyText.'</a>';

												if($days){
													$content.= '（购买后'.$days.'天内可下载）';
												}
											}else{
												$content.='（仅限'.$erphp_life_name.'购买）<a href="'.$erphp_url_front_vip.'" class="erphpdown-vip">升级'.$erphp_life_name.'</a>';
											}
										}elseif($indexMemberDown==11){
											if($userType){
												$content.='（仅限'.$erphp_vip_name.'购买，'.$erphp_year_name.' 5折）';
												if($down_checkpan) $content .= $down_checkpan;
												else $content.='<a class="erphpdown-iframe erphpdown-buy" href='.constant("erphpdown").'buy.php?postid='.get_the_ID().'&index='.$index.' target="_blank">'.$buyText.'</a>';

												if($days){
													$content.= '（购买后'.$days.'天内可下载）';
												}
											}else{
												$content.='（仅限'.$erphp_vip_name.'购买，'.$erphp_year_name.' 5折）<a href="'.$erphp_url_front_vip.'" class="erphpdown-vip">升级'.$erphp_vip_name.'</a>';
											}
										}elseif($indexMemberDown==12){
											if($userType){
												$content.='（仅限'.$erphp_vip_name.'购买，'.$erphp_year_name.' 8折）';
												if($down_checkpan) $content .= $down_checkpan;
												else $content.='<a class="erphpdown-iframe erphpdown-buy" href='.constant("erphpdown").'buy.php?postid='.get_the_ID().'&index='.$index.' target="_blank">'.$buyText.'</a>';

												if($days){
													$content.= '（购买后'.$days.'天内可下载）';
												}
											}else{
												$content.='（仅限'.$erphp_vip_name.'购买，'.$erphp_year_name.' 8折）<a href="'.$erphp_url_front_vip.'" class="erphpdown-vip">升级'.$erphp_vip_name.'</a>';
											}
										}else{
											if($down_checkpan) $content .= $down_checkpan;
											else $content.='<a class="erphpdown-iframe erphpdown-buy" href="'.constant("erphpdown").'buy.php?postid='.get_the_ID().'&index='.$index.'" target="_blank">'.$buyText.'</a>';

											if($days){
												$content.= '（购买后'.$days.'天内可下载）';
											}
										}
									}
									
								}else{
									$content.="<a href='".constant("erphpdown").'download.php?postid='.get_the_ID()."&index=".$index.$iframe."&timestamp=".time()."' class='erphpdown-down".$erphp_popdown."' target='_blank'>立即下载</a>";
								}
								
							}else{
								if($indexMemberDown == 4 || $indexMemberDown == 15 || $indexMemberDown == 8 || $indexMemberDown == 9){
									$content.='此资源仅限'.$erphp_vip_name.'下载，请先<a href="'.$erphp_url_front_login.'" class="erphp-login-must">登录</a>';
								}else{
									if($price){
										$content.='此资源下载价格为<span class="erphpdown-price">'.$price.'</span>'.get_option('ice_name_alipay').'，请先<a href="'.$erphp_url_front_login.'" target="_blank" class="erphp-login-must">登录</a>';
									}else{
										$content.='此资源仅限注册用户下载，请先<a href="'.$erphp_url_front_login.'" class="erphp-login-must">登录</a>';
									}
								}
							}
							if(get_option('erphp_repeatdown_btn') && $down_repeat && $down_info_repeat && !$down_info){
								$content.="<a href='".constant("erphpdown").'download.php?postid='.get_the_ID()."&index=".$index.$iframe."&timestamp=".time()."' class='erphpdown-down".$erphp_popdown."' target='_blank'>立即下载</a>";
							}
	    					$content .= '</fieldset>';
	    				}
	    			}
				}
			}else{
				if(function_exists('erphpdown_tuan_install')){
					$tuanHtml = erphpdown_tuan_html();
				}

				if(function_exists('epd_check_pan_callback')){
					if(strpos($url,'pan.baidu.com') !== false || (strpos($url,'lanzou') !== false && strpos($url,'.com') !== false) || strpos($url,'cloud.189.cn') !== false){
						$down_checkpan = '<a class="erphpdown-buy erphpdown-checkpan" href="javascript:;" data-id="'.get_the_ID().'" data-index="0" data-buy="'.constant("erphpdown").'buy.php?postid='.get_the_ID().'">点击检测网盘有效后购买</a>';
					}
				}

				if(is_user_logged_in() || ( ($userType && ($memberDown==3 || $memberDown==4)) || (($memberDown==15 || $memberDown==16) && $userType >= 8) || (($memberDown==6 || $memberDown==8) && $userType >= 9) || (($memberDown==7 || $memberDown==9 || $memberDown==13 || $memberDown==14 || $memberDown==20) && $userType == 10) )){
					if($price){
						if($memberDown != 4 && $memberDown != 15 && $memberDown != 8 && $memberDown != 9)
							$content.='此资源下载价格为<span class="erphpdown-price">'.$price.'</span>'.get_option("ice_name_alipay");
					}else{
						if($memberDown != 4 && $memberDown != 15 && $memberDown != 8 && $memberDown != 9)
							$content.='此资源仅限注册用户下载';
					}

					if($price || $memberDown == 4 || $memberDown == 15 || $memberDown == 8 || $memberDown == 9){
						global $wpdb;
						$user_info=wp_get_current_user();
						if($user_info->ID){
							$down_info=$wpdb->get_row("select * from ".$wpdb->icealipay." where ice_post='".get_the_ID()."' and ice_success=1 and (ice_index is null or ice_index = '') and ice_user_id=".$user_info->ID." order by ice_time desc");
							if($days > 0 && $down_info){
								$lastDownDate = date('Y-m-d H:i:s',strtotime('+'.$days.' day',strtotime($down_info->ice_time)));
								$nowDate = date('Y-m-d H:i:s');
								if(strtotime($nowDate) > strtotime($lastDownDate)){
									$down_info = null;
								}
							}

							if($down_repeat){
								$down_info_repeat = $down_info;
								$down_info = null;
							}
						}

						$buyText = '立即购买';
						if($down_repeat && $down_info_repeat && !$down_info){
							$buyText = '再次购买';
						}

						$user_id = $user_info->ID;
						$wppay = new EPD(get_the_ID(), $user_id);

						$ews_erphpdown = get_option("ews_erphpdown");
						if($ews_erphpdown && function_exists("ews_erphpdown") && isset($_COOKIE['ewd_'.get_the_ID()]) && $_COOKIE['ewd_'.get_the_ID()] == md5(get_the_ID().get_option('erphpdown_downkey')) ){
							$down_can = 1;
							$content.="<a href='".constant("erphpdown").'download.php?postid='.get_the_ID().$iframe."&timestamp=".time()."' class='erphpdown-down".$erphp_popdown."' target='_blank'>立即下载</a>";

						}elseif( ($userType && ($memberDown==3 || $memberDown==4)) || (($wppay->isWppayPaid() || $wppay->isWppayPaidNew()) && !$down_repeat) || $down_info || (($memberDown==15 || $memberDown==16) && $userType >= 8) || (($memberDown==6 || $memberDown==8) && $userType >= 9) || (($memberDown==7 || $memberDown==9 || $memberDown==13 || $memberDown==14 || $memberDown==20) && $userType == 10) || (!$price && $memberDown!=4 && $memberDown!=15 && $memberDown!=8 && $memberDown!=9)){

							$down_can = 1;

							if($memberDown==3){
								$content.='（'.$erphp_vip_name.'免费）';
							}elseif($memberDown==2){
								$content.='（'.$erphp_vip_name.' 5折）';
							}elseif($memberDown==13){
								$content.='（'.$erphp_vip_name.' 5折、'.$erphp_life_name.'免费）';
							}elseif($memberDown==5){
								$content.='（'.$erphp_vip_name.' 8折）';
							}elseif($memberDown==14){
								$content.='（'.$erphp_vip_name.' 8折、'.$erphp_life_name.'免费）';
							}elseif($memberDown==14){
								$content.='（'.$erphp_vip_discounts.'、'.$erphp_life_name.'免费）';
							}elseif($memberDown==16){
								$content .= '（'.$erphp_quarter_name.'免费）';
							}elseif($memberDown==6){
								$content .= '（'.$erphp_year_name.'免费）';
							}elseif($memberDown==7){
								$content .= '（'.$erphp_life_name.'免费）';
							}elseif($memberDown==4){
								$content .= '（此资源仅限'.$erphp_vip_name.'下载）';
							}elseif($memberDown==15){
								$content .= '（此资源仅限'.$erphp_quarter_name.'下载）';
							}elseif($memberDown==8){
								$content .= '（此资源仅限'.$erphp_year_name.'下载）';
							}elseif($memberDown==9){
								$content .= '（此资源仅限'.$erphp_life_name.'下载）';
							}elseif ($memberDown==10){
								$content .= '（仅限'.$erphp_vip_name.'购买）';
							}elseif ($memberDown==17){
								$content .= '（仅限'.$erphp_quarter_name.'购买）';
							}elseif ($memberDown==18){
								$content .= '（仅限'.$erphp_year_name.'购买）';
							}elseif ($memberDown==19){
								$content .= '（仅限'.$erphp_life_name.'购买）';
							}elseif ($memberDown==11){
								$content .= '（仅限'.$erphp_vip_name.'购买、'.$erphp_year_name.' 5折）';
							}elseif ($memberDown==12){
								$content .= '（仅限'.$erphp_vip_name.'购买、'.$erphp_year_name.' 8折）';
							}

							$content.="<a href='".constant("erphpdown").'download.php?postid='.get_the_ID().$iframe."&timestamp=".time()."' class='erphpdown-down".$erphp_popdown."' target='_blank'>立即下载</a>";

						}else{

							$vipText = '<a href="'.$erphp_url_front_vip.'" target="_blank" class="erphpdown-vip">升级'.$erphp_vip_name.'</a>';
							if($userType){
								$vipText = '';
								if(($memberDown == 13 || $memberDown == 14 || $memberDown == 20) && $userType < 10){
									$vipText = '<a href="'.$erphp_url_front_vip.'" target="_blank" class="erphpdown-vip">升级'.$erphp_life_name.'</a>';
								}
							}
							if($memberDown==3){
								$content.='（'.$erphp_vip_name.'免费）'.$vipText;
							}elseif ($memberDown==2){
								$content.='（'.$erphp_vip_name.' 5折）'.$vipText;
							}elseif ($memberDown==13){
								$content.='（'.$erphp_vip_name.' 5折、'.$erphp_life_name.'免费）'.$vipText;
							}elseif ($memberDown==5){
								$content.='（'.$erphp_vip_name.' 8折）'.$vipText;
							}elseif ($memberDown==14){
								$content.='（'.$erphp_vip_name.' 8折、'.$erphp_life_name.'免费）'.$vipText;
							}elseif ($memberDown==20){
								$content.='（'.$erphp_vip_discounts.'、'.$erphp_life_name.'免费）'.$vipText;
							}elseif ($memberDown==16){
								if($userType < 8){
									$vipText = '<a href="'.$erphp_url_front_vip.'" target="_blank" class="erphpdown-vip">升级'.$erphp_quarter_name.'</a>';
								}
								$content.='（'.$erphp_quarter_name.'免费）'.$vipText;
							}elseif ($memberDown==6){
								if($userType < 9){
									$vipText = '<a href="'.$erphp_url_front_vip.'" target="_blank" class="erphpdown-vip">升级'.$erphp_year_name.'</a>';
								}
								$content.='（'.$erphp_year_name.'免费）'.$vipText;
							}elseif ($memberDown==7){
								if($userType < 10){
									$vipText = '<a href="'.$erphp_url_front_vip.'" target="_blank" class="erphpdown-vip">升级'.$erphp_life_name.'</a>';
								}
								$content.='（'.$erphp_life_name.'免费）'.$vipText;
							}elseif ($memberDown==4){
								if($userType){
									$content.='此资源为'.$erphp_vip_name.'专享资源';
								}
							}elseif ($memberDown==8){
								if($userType >= 9){
									$content.='此资源为'.$erphp_year_name.'专享资源';
								}
							}elseif ($memberDown==9){
								if($userType >= 10){
									$content.='此资源为'.$erphp_life_name.'专享资源';
								}
							}
							

							if($memberDown==4){
								$content.='此资源仅限'.$erphp_vip_name.'下载<a href="'.$erphp_url_front_vip.'" class="erphpdown-vip">升级'.$erphp_vip_name.'</a>';
							}elseif($memberDown==15){
								$content.='此资源仅限'.$erphp_quarter_name.'下载<a href="'.$erphp_url_front_vip.'" class="erphpdown-vip">升级'.$erphp_quarter_name.'</a>';
							}elseif($memberDown==8){
								$content.='此资源仅限'.$erphp_year_name.'下载<a href="'.$erphp_url_front_vip.'" class="erphpdown-vip">升级'.$erphp_year_name.'</a>';
							}elseif($memberDown==9){
								$content.='此资源仅限'.$erphp_life_name.'下载<a href="'.$erphp_url_front_vip.'" class="erphpdown-vip">升级'.$erphp_life_name.'</a>';
							}elseif($memberDown==10){
								if($userType){
									$content.='（仅限'.$erphp_vip_name.'购买）';
									if($down_checkpan) $content .= $down_checkpan;
									else $content.='<a class="erphpdown-iframe erphpdown-buy" href='.constant("erphpdown").'buy.php?postid='.get_the_ID().' target="_blank">'.$buyText.'</a>';

									if($days){
										$content.= '（购买后'.$days.'天内可下载）';
									}
								}else{
									$content.='（仅限'.$erphp_vip_name.'购买）<a href="'.$erphp_url_front_vip.'" class="erphpdown-vip">升级'.$erphp_vip_name.'</a>';
								}
							}elseif($memberDown==17){
								if($userType >= 8){
									$content.='（仅限'.$erphp_quarter_name.'购买）';
									if($down_checkpan) $content .= $down_checkpan;
									else $content.='<a class="erphpdown-iframe erphpdown-buy" href='.constant("erphpdown").'buy.php?postid='.get_the_ID().' target="_blank">'.$buyText.'</a>';

									if($days){
										$content.= '（购买后'.$days.'天内可下载）';
									}
								}else{
									$content.='（仅限'.$erphp_quarter_name.'购买）<a href="'.$erphp_url_front_vip.'" class="erphpdown-vip">升级'.$erphp_quarter_name.'</a>';
								}
							}elseif($memberDown==18){
								if($userType >= 9){
									$content.='（仅限'.$erphp_year_name.'购买）';
									if($down_checkpan) $content .= $down_checkpan;
									else $content.='<a class="erphpdown-iframe erphpdown-buy" href='.constant("erphpdown").'buy.php?postid='.get_the_ID().' target="_blank">'.$buyText.'</a>';

									if($days){
										$content.= '（购买后'.$days.'天内可下载）';
									}
								}else{
									$content.='（仅限'.$erphp_year_name.'购买）<a href="'.$erphp_url_front_vip.'" class="erphpdown-vip">升级'.$erphp_year_name.'</a>';
								}
							}elseif($memberDown==19){
								if($userType == 10){
									$content.='（仅限'.$erphp_life_name.'购买）';
									if($down_checkpan) $content .= $down_checkpan;
									else $content.='<a class="erphpdown-iframe erphpdown-buy" href='.constant("erphpdown").'buy.php?postid='.get_the_ID().' target="_blank">'.$buyText.'</a>';

									if($days){
										$content.= '（购买后'.$days.'天内可下载）';
									}
								}else{
									$content.='（仅限'.$erphp_life_name.'购买）<a href="'.$erphp_url_front_vip.'" class="erphpdown-vip">升级'.$erphp_life_name.'</a>';
								}
							}elseif($memberDown==11){
								if($userType){
									$content.='（仅限'.$erphp_vip_name.'购买，'.$erphp_year_name.' 5折）';
									if($down_checkpan) $content .= $down_checkpan;
									else $content.='<a class="erphpdown-iframe erphpdown-buy" href='.constant("erphpdown").'buy.php?postid='.get_the_ID().' target="_blank">'.$buyText.'</a>';

									if($days){
										$content.= '（购买后'.$days.'天内可下载）';
									}
								}else{
									$content.='（仅限'.$erphp_vip_name.'购买，'.$erphp_year_name.' 5折）<a href="'.$erphp_url_front_vip.'" class="erphpdown-vip">升级'.$erphp_vip_name.'</a>';
								}
							}elseif($memberDown==12){
								if($userType){
									$content.='（仅限'.$erphp_vip_name.'购买，'.$erphp_year_name.' 8折）';
									if($down_checkpan) $content .= $down_checkpan;
									else $content.='<a class="erphpdown-iframe erphpdown-buy" href='.constant("erphpdown").'buy.php?postid='.get_the_ID().' target="_blank">'.$buyText.'</a>';

									if($days){
										$content.= '（购买后'.$days.'天内可下载）';
									}
								}else{
									$content.='（仅限'.$erphp_vip_name.'购买，'.$erphp_year_name.' 8折）<a href="'.$erphp_url_front_vip.'" class="erphpdown-vip">升级'.$erphp_vip_name.'</a>';
								}
							}else{
								if($down_checkpan) $content .= $down_checkpan;
								else $content.='<a class="erphpdown-iframe erphpdown-buy" href='.constant("erphpdown").'buy.php?postid='.get_the_ID().' target="_blank">'.$buyText.'</a>';

								if($days){
									$content.= '（购买后'.$days.'天内可下载）';
								}
							}
						}
						
					}else{
						$down_can = 1;
						$content.="<a href='".constant("erphpdown").'download.php?postid='.get_the_ID().$iframe."&timestamp=".time()."' class='erphpdown-down".$erphp_popdown."' target='_blank'>立即下载</a>";
					}
					
				}else {
					if($memberDown == 4){
						$content.='此资源仅限'.$erphp_vip_name.'下载，请先<a href="'.$erphp_url_front_login.'" class="erphp-login-must">登录</a>';
					}elseif($memberDown == 15){
						$content.='此资源仅限'.$erphp_quarter_name.'下载，请先<a href="'.$erphp_url_front_login.'" class="erphp-login-must">登录</a>';
					}elseif($memberDown == 8){
						$content.='此资源仅限'.$erphp_year_name.'下载，请先<a href="'.$erphp_url_front_login.'" class="erphp-login-must">登录</a>';
					}elseif($memberDown == 9){
						$content.='此资源仅限'.$erphp_life_name.'下载，请先<a href="'.$erphp_url_front_login.'" class="erphp-login-must">登录</a>';
					}elseif($memberDown == 10){
						$content.='此资源仅限'.$erphp_vip_name.'购买，请先<a href="'.$erphp_url_front_login.'" class="erphp-login-must">登录</a>';
					}elseif($memberDown == 17){
						$content.='此资源仅限'.$erphp_quarter_name.'购买，请先<a href="'.$erphp_url_front_login.'" class="erphp-login-must">登录</a>';
					}elseif($memberDown == 18){
						$content.='此资源仅限'.$erphp_year_name.'购买，请先<a href="'.$erphp_url_front_login.'" class="erphp-login-must">登录</a>';
					}elseif($memberDown == 19){
						$content.='此资源仅限'.$erphp_life_name.'购买，请先<a href="'.$erphp_url_front_login.'" class="erphp-login-must">登录</a>';
					}elseif($memberDown == 11){
						$content.='此资源仅限'.$erphp_vip_name.'购买、'.$erphp_year_name.' 5折，请先<a href="'.$erphp_url_front_login.'" class="erphp-login-must">登录</a>';
					}elseif($memberDown == 12){
						$content.='此资源仅限'.$erphp_vip_name.'购买、'.$erphp_year_name.' 8折，请先<a href="'.$erphp_url_front_login.'" class="erphp-login-must">登录</a>';
					}else{
						$vip_content = '';
						if($memberDown==3){
							$vip_content.='（'.$erphp_vip_name.'免费）';
						}elseif($memberDown==2){
							$vip_content.='（'.$erphp_vip_name.' 5折）';
						}elseif($memberDown==13){
							$vip_content.='（'.$erphp_vip_name.' 5折、'.$erphp_life_name.'免费）';
						}elseif($memberDown==5){
							$vip_content.='（'.$erphp_vip_name.' 8折）';
						}elseif($memberDown==14){
							$vip_content.='（'.$erphp_vip_name.' 8折、'.$erphp_life_name.'免费）';
						}elseif($memberDown==20){
							$vip_content.='（'.$erphp_vip_discounts.'、'.$erphp_life_name.'免费）';
						}elseif($memberDown==16){
							$vip_content .= '（'.$erphp_quarter_name.'免费）';
						}elseif($memberDown==6){
							$vip_content .= '（'.$erphp_year_name.'免费）';
						}elseif($memberDown==7){
							$vip_content .= '（'.$erphp_life_name.'免费）';
						}

						if(get_option('erphp_wppay_down')){
							$user_id = is_user_logged_in() ? wp_get_current_user()->ID : 0;
							$wppay = new EPD(get_the_ID(), $user_id);
							if($wppay->isWppayPaid() || $wppay->isWppayPaidNew()){
								$down_can = 1;
								if($price){
									if($memberDown != 4 && $memberDown != 15 && $memberDown != 8 && $memberDown != 9)
										$content.='此资源下载价格为<span class="erphpdown-price">'.$price.'</span>'.get_option("ice_name_alipay");
								}
								$content.="<a href='".constant("erphpdown").'download.php?postid='.get_the_ID().$iframe."&timestamp=".time()."' class='erphpdown-down".$erphp_popdown."' target='_blank'>立即下载</a>";
							}else{
								if($price){
									$content.='此资源下载价格为<span class="erphpdown-price">'.$price.'</span>'.get_option('ice_name_alipay');

									if($down_checkpan) $content .= $down_checkpan;
									else $content.='<a class="erphpdown-iframe erphpdown-buy" href='.constant("erphpdown").'buy.php?postid='.get_the_ID().' target="_blank">立即购买</a>';

									$content .= $vip_content?($vip_content.'<a href="'.$erphp_url_front_login.'" target="_blank" class="erphpdown-vip erphp-login-must">立即升级</a>'):'';
								}else{
									if(!get_option('erphp_free_login')){
										$down_can = 1;
										$content.="此资源为免费资源<a href='".constant("erphpdown").'download.php?postid='.get_the_ID().$iframe."&timestamp=".time()."' class='erphpdown-down".$erphp_popdown."' target='_blank'>立即下载</a>";
									}else{
										$content.='此资源仅限注册用户下载，请先<a href="'.$erphp_url_front_login.'" class="erphp-login-must">登录</a>';
									}
								}
							}
						}else{
							if($price){
								$content.='此资源下载价格为<span class="erphpdown-price">'.$price.'</span>'.get_option('ice_name_alipay').$vip_content.'，请先<a href="'.$erphp_url_front_login.'"  class="erphp-login-must">登录</a>';
							}else{
								$content.='此资源仅限注册用户下载，请先<a href="'.$erphp_url_front_login.'" class="erphp-login-must">登录</a>';
							}
							
						}
					}
				}

				if(get_option('erphp_repeatdown_btn') && $down_repeat && $down_info_repeat && !$down_info){
					$content.="<a href='".constant("erphpdown").'download.php?postid='.get_the_ID().$iframe."&timestamp=".time()."' class='erphpdown-down".$erphp_popdown."' target='_blank'>立即下载</a>";
				}
			}

			$ews_erphpdown = get_option("ews_erphpdown");
			if(!$down_can && $ews_erphpdown && function_exists("ews_erphpdown")){
				$ews_erphpdown_btn = get_option("ews_erphpdown_btn");
				$ews_erphpdown_btn = $ews_erphpdown_btn?$ews_erphpdown_btn:'关注公众号免费下载';
				$content.='<a class="erphpdown-buy ews-erphpdown-button" data-id="'.get_the_ID().'" href="javascript:;">'.$ews_erphpdown_btn.'</a>';
			}
			
			if(get_option('ice_tips')) $content.='<div class="erphpdown-tips">'.get_option('ice_tips').'</div>';
			$content .= $tuanHtml;
		}
		$content.='</fieldset>';
		
	}elseif($erphp_down == 6){
		$content .= '<fieldset class="erphpdown erphpdown-default" id="erphpdown" style="display:block"><legend>'.($erphp_box_faka_title?$erphp_box_faka_title:'自动发卡').'</legend>';
		$content .= '此卡密价格为<span class="erphpdown-price">'.$price.'</span>'.get_option("ice_name_alipay");
		$content .= '<a class="erphpdown-iframe erphpdown-buy" href='.constant("erphpdown").'buy.php?postid='.get_the_ID().' target="_blank">立即购买</a>';
		if(function_exists('getErphpActLeft')) $content .= '（库存：'.getErphpActLeft(get_the_ID()).'）';
		$user_info=wp_get_current_user();
		if($user_info->ID){
			$down_info=$wpdb->get_row("select * from ".$wpdb->icealipay." where ice_post='".get_the_ID()."' and ice_success=1 and (ice_index is null or ice_index = '') and ice_user_id=".$user_info->ID." order by ice_time desc");
			if($down_info){
				$content .= '<div class="erphpdown-bought-activation">已购卡密：'.$down_info->ice_data.'</div>';
			}
		}
		$content .= '</fieldset>';
	}else{
		if($downMsgFree) $content.='<fieldset class="erphpdown erphpdown-default" id="erphpdown" style="display:block"><legend>'.($erphp_box_down_title?$erphp_box_down_title:'资源下载').'</legend>'.$downMsgFree.'</fieldset>';
	}
	
	return $content;
}

function erphpdown_shortcode_see($atts, $content=null){
	$atts = shortcode_atts( array(
        'index' => '',
        'type' => '',
        'image' => '',
        'price' => ''
    ), $atts, 'erphpdown' );
	date_default_timezone_set('Asia/Shanghai'); 
	global $post,$wpdb;

	$type_class = '';
	$type_style = '';
	if($atts['type'] == "video"){
		$type_class = " erphpdown-see-video";
	}
	if($atts['image']){
		$type_style = 'position:relative;background-color:#000 !important;background-image:url('.$atts['image'].') !important;background-repeat:no-repeat !important;background-size:cover !important;background-position:center !important;border:none;text-align:center;color:#fff';
	}

	$erphp_life_name    = get_option('erphp_life_name')?get_option('erphp_life_name'):'终身VIP';
	$erphp_year_name    = get_option('erphp_year_name')?get_option('erphp_year_name'):'包年VIP';
	$erphp_quarter_name = get_option('erphp_quarter_name')?get_option('erphp_quarter_name'):'包季VIP';
	$erphp_vip_name  = get_option('erphp_vip_name')?get_option('erphp_vip_name'):'VIP';

	$erphpdown_downkey = get_option('erphpdown_downkey');

	$erphp_vip_discounts = $erphp_vip_name.'折扣';

	$original_content = $content;

	$erphp_see2_style = get_option('erphp_see2_style');
	$erphp_wppay_vip = get_option('erphp_wppay_vip');

	$days=get_post_meta($post->ID, 'down_days', true);
	$down_info = null;

	$erphp_url_front_vip = get_bloginfo('wpurl').'/wp-admin/admin.php?page=erphpdown/admin/erphp-update-vip.php';
	if(get_option('erphp_url_front_vip')){
		$erphp_url_front_vip = get_option('erphp_url_front_vip');
	}
	$erphp_url_front_login = wp_login_url(get_permalink());
	if(get_option('erphp_url_front_login')){
		$erphp_url_front_login = get_option('erphp_url_front_login');
	}

	if(is_user_logged_in()){
		$erphp_url_front_vip2 = $erphp_url_front_vip;
	}else{
		$erphp_url_front_vip2 = $erphp_url_front_login;
	}

	if($atts['index'] > 0 && is_numeric($atts['index'])){
		if($atts['price'] > 0 && is_numeric($atts['price'])){
			$price_index = $atts['price'];
		}else{
			$price_index = get_post_meta($post->ID, 'down_price', true);
		}

		if($price_index > 0){
			$html='<div class="erphpdown erphpdown-see erphpdown-content-vip" style="display:block">';
			if(is_user_logged_in()){
				$user_info=wp_get_current_user();
				$down_info=$wpdb->get_row("select * from ".$wpdb->iceindex." where ice_post='".$post->ID."' and ice_index=".$atts['index']." and ice_user_id=".$user_info->ID." and ice_price='".$price_index."' order by ice_time desc");
				if($days > 0 && $down_info){
					$lastDownDate = date('Y-m-d H:i:s',strtotime('+'.$days.' day',strtotime($down_info->ice_time)));
					$nowDate = date('Y-m-d H:i:s');
					if(strtotime($nowDate) > strtotime($lastDownDate)){
						$down_info = null;
					}
				}
				if($down_info){
					return '<div class="erphpdown-content-view">'.do_shortcode($content).'</div>';
				}else{
					$html.='此内容查看价格为<span class="erphpdown-price">'.$price_index.'</span>'.get_option('ice_name_alipay');
					$html.='<a class="erphpdown-buy erphpdown-buy-index" href="javascript:;" data-post="'.$post->ID.'" data-index="'.$atts['index'].'" data-price="'.$price_index.'">立即购买</a>';
					if($days){
						$html.= '（购买后'.$days.'天内可查看）';
					}
					$html .= '</div>';
				}
			}else{
				$html.='此内容查看价格为<span class="erphpdown-price">'.$price_index.'</span>'.get_option('ice_name_alipay').'，请先<a href="'.$erphp_url_front_login.'" class="erphp-login-must">登录</a></div>';
			}
			return $html;
		}else{
			return '';
		}
	}else{
		$userType=getUsreMemberType();
		if(is_single()){
			$categories = get_the_category();
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
		}
		$memberDown=get_post_meta($post->ID, 'member_down',TRUE);
		$start_down2=get_post_meta($post->ID, 'start_down2', true);
		$start_down=get_post_meta($post->ID, 'start_down', true);
		$start_see2=get_post_meta($post->ID, 'start_see2', true);
		$start_see=get_post_meta($post->ID, 'start_see', true);
		$price=get_post_meta($post->ID, 'down_price', true);

		$user_info=wp_get_current_user();
		if($user_info->ID){
			$down_info=$wpdb->get_row("select * from ".$wpdb->icealipay." where ice_post='".$post->ID."' and ice_success=1 and (ice_index is null or ice_index = '') and ice_user_id=".$user_info->ID." order by ice_time desc");
		}
		$user_id = is_user_logged_in() ? wp_get_current_user()->ID : 0;
		$wppay = new EPD($post->ID, $user_id);

		if($start_down2){
			if( $wppay->isWppayPaid() || $wppay->isWppayPaidNew() || ($memberDown == 3 && $userType) || ($memberDown == 16 && $userType >= 8) || ($memberDown == 6 && $userType >= 9) || ($memberDown == 7 && $userType >= 10) || !$price){
				return '<div class="erphpdown-content-view">'.do_shortcode($content).'</div>';
			}else{
				if($memberDown == 3 || $memberDown == 16 || $memberDown == 6 || $memberDown == 7){
					$wppay_vip_name = $erphp_vip_name;
					if($memberDown == 16){
						$wppay_vip_name = $erphp_quarter_name;
					}elseif($memberDown == 6){
						$wppay_vip_name = $erphp_year_name;
					}elseif($memberDown == 7){
						$wppay_vip_name = $erphp_life_name;
					}
					$content = '<div class="erphpdown erphpdown-see erphpdown-content-vip erphpdown-see-pay" style="display:block">此内容查看价格<span class="erphpdown-price">'.$price.'</span>元<a href="javascript:;" class="erphp-wppay-loader erphpdown-buy" data-post="'.$post->ID.'">立即购买</a>&nbsp;&nbsp;<b>或</b>&nbsp;&nbsp;升级'.$wppay_vip_name.'后免费<a href="'.$erphp_url_front_vip2.'" target="_blank" class="erphpdown-vip'.(is_user_logged_in()?'':' erphp-login-must').'">升级'.$wppay_vip_name.'</a>';
				}else{
					$content = '<div class="erphpdown erphpdown-see erphpdown-content-vip erphpdown-see-pay" style="display:block">此内容查看价格<span class="erphpdown-price">'.$price.'</span>元<a href="javascript:;" class="erphp-wppay-loader erphpdown-buy" data-post="'.get_the_ID().'">立即购买</a>';	
				}

				if(get_option('ice_tips_see')) $content.='<div class="erphpdown-tips">'.get_option('ice_tips_see').'</div>';

				$content .= '</div>'; 
				return $content;
			}
		}elseif($start_down || $start_see2 || $start_see){
			if(is_user_logged_in() || ( (($memberDown==3 || $memberDown==4) && $userType) || (($memberDown==15 || $memberDown==16) && $userType >= 8) || (($memberDown==6 || $memberDown==8) && $userType >= 9) || (($memberDown==7 || $memberDown==9 || $memberDown==13 || $memberDown==14 || $memberDown==20) && $userType == 10) )){
				if($days > 0 && $down_info){
					$lastDownDate = date('Y-m-d H:i:s',strtotime('+'.$days.' day',strtotime($down_info->ice_time)));
					$nowDate = date('Y-m-d H:i:s');
					if(strtotime($nowDate) > strtotime($lastDownDate)){
						$down_info = null;
					}
				}

				if(!$price && $memberDown!=4 && $memberDown!=15 && $memberDown!=8 && $memberDown!=9){
					return '<div class="erphpdown-content-view">'.do_shortcode($content).'</div>';
				}

				if( (($memberDown==3 || $memberDown==4) && $userType) || $wppay->isWppayPaid() || $wppay->isWppayPaidNew() || $down_info || (($memberDown==15 || $memberDown==16) && $userType >= 8) || (($memberDown==6 || $memberDown==8) && $userType >= 9) || (($memberDown==7 || $memberDown==9 || $memberDown==13 || $memberDown==14 || $memberDown==20) && $userType == 10) ){

					if($memberDown==3 || $memberDown==4){
						$data_vip = 1;
					}elseif($memberDown==15 || $memberDown==16){
						$data_vip = 8;
					}elseif($memberDown==6 || $memberDown==8){
						$data_vip = 9;
					}elseif($memberDown==7 || $memberDown==9 || $memberDown==13 || $memberDown==14 || $memberDown==20){
						$data_vip = 10;
					}else{
						$data_vip = 0;
					}

					if(!$wppay->isWppayPaid() && !$wppay->isWppayPaidNew() && !$down_info){

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

						if(checkSeeHas($user_info->ID,$post->ID)){
							return '<div class="erphpdown-content-view">'.do_shortcode($content).'</div>';
						}else{
							if($userType == 6 && $erphp_day_times > 0){
								if( checkSeeLog($user_info->ID,$post->ID,$erphp_day_times,erphpGetIP()) ){
									return '<p class="erphpdown-content-vip erphpdown-content-vip-see">您可免费查看本文隐藏内容！<a href="javascript:;" class="erphpdown-see-btn" data-post="'.$post->ID.'" data-vip="'.$data_vip.'" data-token="'.md5($erphpdown_downkey.$post->ID.$erphpdown_downkey.$data_vip).'">立即查看</a>（今日已查看'.getSeeCount($user_info->ID).'个，还可查看'.($erphp_day_times-getSeeCount($user_info->ID)).'个）</p>';
								}else{
									return '<p class="erphpdown-content-vip">您暂时无权查看本文隐藏内容，请明天再来！（今日已查看'.getSeeCount($user_info->ID).'个，还可查看'.($erphp_day_times-getSeeCount($user_info->ID)).'个）</p>';
								}
							}elseif($userType == 7 && $erphp_month_times > 0){
								if( checkSeeLog($user_info->ID,$post->ID,$erphp_month_times,erphpGetIP()) ){
									return '<p class="erphpdown-content-vip erphpdown-content-vip-see">您可免费查看本文隐藏内容！<a href="javascript:;" class="erphpdown-see-btn" data-post="'.$post->ID.'" data-vip="'.$data_vip.'" data-token="'.md5($erphpdown_downkey.$post->ID.$erphpdown_downkey.$data_vip).'">立即查看</a>（今日已查看'.getSeeCount($user_info->ID).'个，还可查看'.($erphp_month_times-getSeeCount($user_info->ID)).'个）</p>';
								}else{
									return '<p class="erphpdown-content-vip">您暂时无权查看本文隐藏内容，请明天再来！（今日已查看'.getSeeCount($user_info->ID).'个，还可查看'.($erphp_month_times-getSeeCount($user_info->ID)).'个）</p>';
								}
							}elseif($userType == 8 && $erphp_quarter_times > 0){
								if( checkSeeLog($user_info->ID,$post->ID,$erphp_quarter_times,erphpGetIP()) ){
									return '<p class="erphpdown-content-vip erphpdown-content-vip-see">您可免费查看本文隐藏内容！<a href="javascript:;" class="erphpdown-see-btn" data-post="'.$post->ID.'" data-vip="'.$data_vip.'" data-token="'.md5($erphpdown_downkey.$post->ID.$erphpdown_downkey.$data_vip).'">立即查看</a>（今日已查看'.getSeeCount($user_info->ID).'个，还可查看'.($erphp_quarter_times-getSeeCount($user_info->ID)).'个）</p>';
								}else{
									return '<p class="erphpdown-content-vip">您暂时无权查看本文隐藏内容，请明天再来！（今日已查看'.getSeeCount($user_info->ID).'个，还可查看'.($erphp_quarter_times-getSeeCount($user_info->ID)).'个）</p>';
								}
							}elseif($userType == 9 && $erphp_year_times > 0){
								if( checkSeeLog($user_info->ID,$post->ID,$erphp_year_times,erphpGetIP()) ){
									return '<p class="erphpdown-content-vip erphpdown-content-vip-see">您可免费查看本文隐藏内容！<a href="javascript:;" class="erphpdown-see-btn" data-post="'.$post->ID.'" data-vip="'.$data_vip.'" data-token="'.md5($erphpdown_downkey.$post->ID.$erphpdown_downkey.$data_vip).'">立即查看</a>（今日已查看'.getSeeCount($user_info->ID).'个，还可查看'.($erphp_year_times-getSeeCount($user_info->ID)).'个）</p>';
								}else{
									return '<p class="erphpdown-content-vip">您暂时无权查看本文隐藏内容，请明天再来！（今日已查看'.getSeeCount($user_info->ID).'个，还可查看'.($erphp_year_times-getSeeCount($user_info->ID)).'个）</p>';
								}
							}elseif($userType == 10 && $erphp_life_times > 0){
								if( checkSeeLog($user_info->ID,$post->ID,$erphp_life_times,erphpGetIP()) ){
									return '<p class="erphpdown-content-vip erphpdown-content-vip-see">您可免费查看本文隐藏内容！<a href="javascript:;" class="erphpdown-see-btn" data-post="'.$post->ID.'" data-vip="'.$data_vip.'" data-token="'.md5($erphpdown_downkey.$post->ID.$erphpdown_downkey.$data_vip).'">立即查看</a>（今日已查看'.getSeeCount($user_info->ID).'个，还可查看'.($erphp_life_times-getSeeCount($user_info->ID)).'个）</p>';
								}else{
									return '<p class="erphpdown-content-vip">您暂时无权查看本文隐藏内容，请明天再来！（今日已查看'.getSeeCount($user_info->ID).'个，还可查看'.($erphp_life_times-getSeeCount($user_info->ID)).'个）</p>';
								}
							}else{
								return '<div class="erphpdown-content-view">'.do_shortcode($content).'</div>';
							}
						}
					}else{
						return '<div class="erphpdown-content-view">'.do_shortcode($content).'</div>';
					}
				}else{
					if($erphp_see2_style){
						$content = '<div class="erphpdown-content-vip erphpdown-content-vip2">'.__('您暂时无权查看此隐藏内容！','erphpdown').'</div>';
					}else{
						$content = '<div class="erphpdown erphpdown-see erphpdown-see-pay erphpdown-content-vip'.$type_class.'" style="display:block;'.$type_style.'">';
						if($price){
							if($memberDown != 4 && $memberDown != 15 && $memberDown != 8 && $memberDown != 9){
								$content.='此内容查看价格为<span class="erphpdown-price">'.$price.'</span>'.get_option('ice_name_alipay');
							}
						}else{
							if($memberDown != 4 && $memberDown != 15 && $memberDown != 8 && $memberDown != 9){
								return '<div class="erphpdown-content-view">'.do_shortcode($content).'</div>';
							}
						}
						

						$vipText = '<a href="'.$erphp_url_front_vip.'" target="_blank" class="erphpdown-vip">升级'.$erphp_vip_name.'</a>';
						if($userType){
							$vipText = '';
							if(($memberDown == 13 || $memberDown == 14 || $memberDown == 20) && $userType < 10){
								$vipText = '<a href="'.$erphp_url_front_vip.'" target="_blank" class="erphpdown-vip">升级'.$erphp_life_name.'</a>';
							}
						}
						if($memberDown==3){
							$content.='（'.$erphp_vip_name.'免费）'.$vipText;
						}elseif ($memberDown==2){
							$content.='（'.$erphp_vip_name.' 5折）'.$vipText;
						}elseif ($memberDown==5){
							$content.='（'.$erphp_vip_name.' 8折）'.$vipText;
						}elseif ($memberDown==13){
							$content.='（'.$erphp_vip_name.' 5折、'.$erphp_life_name.'免费）'.$vipText;
						}elseif ($memberDown==14){
							$content.='（'.$erphp_vip_name.' 8折、'.$erphp_life_name.'免费）'.$vipText;
						}elseif ($memberDown==20){
							$content.='（'.$erphp_vip_discounts.'、'.$erphp_life_name.'免费）'.$vipText;
						}elseif ($memberDown==16){
							if($userType < 9){
								$vipText = '<a href="'.$erphp_url_front_vip.'" target="_blank" class="erphpdown-vip">升级'.$erphp_quarter_name.'</a>';
							}
							$content.='（'.$erphp_quarter_name.'免费）'.$vipText;
						}elseif ($memberDown==6){
							if($userType < 9){
								$vipText = '<a href="'.$erphp_url_front_vip.'" target="_blank" class="erphpdown-vip">升级'.$erphp_year_name.'</a>';
							}
							$content.='（'.$erphp_year_name.'免费）'.$vipText;
						}elseif ($memberDown==7){
							if($userType < 10){
								$vipText = '<a href="'.$erphp_url_front_vip.'" target="_blank" class="erphpdown-vip">升级'.$erphp_life_name.'</a>';
							}
							$content.='（'.$erphp_life_name.'免费）'.$vipText;
						}
						

						if($memberDown==4){
							$content.='此内容仅限'.$erphp_vip_name.'查看<a href="'.$erphp_url_front_vip.'" target="_blank" class="erphpdown-vip">升级'.$erphp_vip_name.'</a>';
						}elseif($memberDown==15)
						{
							$content.='此内容仅限'.$erphp_quarter_name.'查看<a href="'.$erphp_url_front_vip.'" target="_blank" class="erphpdown-vip">升级'.$erphp_quarter_name.'</a>';
						}elseif($memberDown==8)
						{
							$content.='此内容仅限'.$erphp_year_name.'查看<a href="'.$erphp_url_front_vip.'" target="_blank" class="erphpdown-vip">升级'.$erphp_year_name.'</a>';
						}elseif($memberDown==9)
						{
							$content.='此内容仅限'.$erphp_life_name.'查看<a href="'.$erphp_url_front_vip.'" target="_blank" class="erphpdown-vip">升级'.$erphp_life_name.'</a>';
						}elseif($memberDown==10){
							if($userType){
								$content.='（仅限'.$erphp_vip_name.'购买）<a class="erphpdown-iframe erphpdown-buy" href="'.constant("erphpdown").'buy.php?postid='.get_the_ID().'&timestamp='.time().'" target="_blank">立即购买</a>';
								if($days){
									$content.= '（购买后'.$days.'天内可查看）';
								}
							}else{
								$content.='（仅限'.$erphp_vip_name.'购买）<a href="'.$erphp_url_front_vip.'" class="erphpdown-vip">升级'.$erphp_vip_name.'</a>';
							}
						}elseif($memberDown==17){
							if($userType >= 8){
								$content.='（仅限'.$erphp_quarter_name.'购买）<a class="erphpdown-iframe erphpdown-buy" href="'.constant("erphpdown").'buy.php?postid='.get_the_ID().'&timestamp='.time().'" target="_blank">立即购买</a>';
								if($days){
									$content.= '（购买后'.$days.'天内可查看）';
								}
							}else{
								$content.='（仅限'.$erphp_quarter_name.'购买）<a href="'.$erphp_url_front_vip.'" class="erphpdown-vip">升级'.$erphp_quarter_name.'</a>';
							}
						}elseif($memberDown==18){
							if($userType >= 9){
								$content.='（仅限'.$erphp_year_name.'购买）<a class="erphpdown-iframe erphpdown-buy" href="'.constant("erphpdown").'buy.php?postid='.get_the_ID().'&timestamp='.time().'" target="_blank">立即购买</a>';
								if($days){
									$content.= '（购买后'.$days.'天内可查看）';
								}
							}else{
								$content.='（仅限'.$erphp_year_name.'购买）<a href="'.$erphp_url_front_vip.'" class="erphpdown-vip">升级'.$erphp_year_name.'</a>';
							}
						}elseif($memberDown==19){
							if($userType == 10){
								$content.='（仅限'.$erphp_life_name.'购买）<a class="erphpdown-iframe erphpdown-buy" href="'.constant("erphpdown").'buy.php?postid='.get_the_ID().'&timestamp='.time().'" target="_blank">立即购买</a>';
								if($days){
									$content.= '（购买后'.$days.'天内可查看）';
								}
							}else{
								$content.='（仅限'.$erphp_life_name.'购买）<a href="'.$erphp_url_front_vip.'" class="erphpdown-vip">升级'.$erphp_life_name.'</a>';
							}
						}elseif($memberDown==11){
							if($userType){
								$content.='（仅限'.$erphp_vip_name.'购买，'.$erphp_year_name.' 5折）';
								$content.='<a class="erphpdown-iframe erphpdown-buy" href="'.constant("erphpdown").'buy.php?postid='.get_the_ID().'&timestamp='.time().'" target="_blank">立即购买</a>';
								if($days){
									$content.= '（购买后'.$days.'天内可查看）';
								}
							}else{
								$content.='（仅限'.$erphp_vip_name.'购买，'.$erphp_year_name.' 5折）<a href="'.$erphp_url_front_vip.'" class="erphpdown-vip">升级'.$erphp_vip_name.'</a>';
							}
						}elseif($memberDown==12){
							if($userType){
								$content.='（仅限'.$erphp_vip_name.'购买，'.$erphp_year_name.' 8折）';
								$content.='<a class="erphpdown-iframe erphpdown-buy" href="'.constant("erphpdown").'buy.php?postid='.get_the_ID().'&timestamp='.time().'" target="_blank">立即购买</a>';
								if($days){
									$content.= '（购买后'.$days.'天内可查看）';
								}
							}else{
								$content.='（仅限'.$erphp_vip_name.'购买，'.$erphp_year_name.' 8折）<a href="'.$erphp_url_front_vip.'" class="erphpdown-vip">升级'.$erphp_vip_name.'</a>';
							}
						}else{

							$content.='<a class="erphpdown-iframe erphpdown-buy" href="'.constant("erphpdown").'buy.php?postid='.get_the_ID().'&timestamp='.time().'" target="_blank">立即购买</a>';
							if($days){
								$content.= '（购买后'.$days.'天内可查看）';
							}
						}

						if(get_option('ice_tips_see')) $content.='<div class="erphpdown-tips">'.get_option('ice_tips_see').'</div>';
						$content.='</div>';
					}
					return $content;
				}
			}else{
				$content2 = $content;
				$content='<div class="erphpdown erphpdown-see erphpdown-see-pay erphpdown-content-vip'.$type_class.'" id="erphpdown" style="display:block;'.$type_style.'">';

				if($memberDown == 4){
					if($erphp_see2_style){
						return '<div class="erphpdown-content-vip erphpdown-content-vip2">'.__('您暂时无权查看此隐藏内容！','erphpdown').'</div>';
					}else{
						if($erphp_wppay_vip){
							$content.='此内容仅限'.$erphp_vip_name.'查看<a href="'.$erphp_url_front_vip.'" class="erphpdown-vip" target="_blank">升级'.$erphp_vip_name.'</a>';
						}else{
							$content.='此内容仅限'.$erphp_vip_name.'查看，请先<a href="'.$erphp_url_front_login.'" class="erphp-login-must">登录</a>';
						}
					}
				}elseif($memberDown == 15){
					if($erphp_see2_style){
						return '<div class="erphpdown-content-vip erphpdown-content-vip2">'.__('您暂时无权查看此隐藏内容！','erphpdown').'</div>';
					}else{
						if($erphp_wppay_vip){
							$content.='此内容仅限'.$erphp_quarter_name.'查看<a href="'.$erphp_url_front_vip.'" class="erphpdown-vip" target="_blank">升级'.$erphp_quarter_name.'</a>';
						}else{
							$content.='此内容仅限'.$erphp_quarter_name.'查看，请先<a href="'.$erphp_url_front_login.'" class="erphp-login-must">登录</a>';
						}
					}
				}elseif($memberDown == 8){
					if($erphp_see2_style){
						return '<div class="erphpdown-content-vip erphpdown-content-vip2">'.__('您暂时无权查看此隐藏内容！','erphpdown').'</div>';
					}else{
						if($erphp_wppay_vip){
							$content.='此内容仅限'.$erphp_year_name.'查看<a href="'.$erphp_url_front_vip.'" class="erphpdown-vip" target="_blank">升级'.$erphp_year_name.'</a>';
						}else{
							$content.='此内容仅限'.$erphp_year_name.'查看，请先<a href="'.$erphp_url_front_login.'" class="erphp-login-must">登录</a>';
						}
					}
				}elseif($memberDown == 9){
					if($erphp_see2_style){
						return '<div class="erphpdown-content-vip erphpdown-content-vip2">'.__('您暂时无权查看此隐藏内容！','erphpdown').'</div>';
					}else{
						if($erphp_wppay_vip){
							$content.='此内容仅限'.$erphp_life_name.'查看<a href="'.$erphp_url_front_vip.'" class="erphpdown-vip" target="_blank">升级'.$erphp_life_name.'</a>';
						}else{
							$content.='此内容仅限'.$erphp_life_name.'查看，请先<a href="'.$erphp_url_front_login.'" class="erphp-login-must">登录</a>';
						}
					}
				}elseif($memberDown == 10){
					if($erphp_see2_style){
						return '<div class="erphpdown-content-vip erphpdown-content-vip2">'.__('您暂时无权查看此隐藏内容！','erphpdown').'</div>';
					}else{
						if($erphp_wppay_vip){
							$content.='此内容仅限'.$erphp_vip_name.'购买<a href="'.$erphp_url_front_vip.'" class="erphpdown-vip" target="_blank">升级'.$erphp_vip_name.'</a>';
						}else{
							$content.='此内容仅限'.$erphp_vip_name.'购买，请先<a href="'.$erphp_url_front_login.'" class="erphp-login-must">登录</a>';
						}
					}
				}elseif($memberDown == 17){
					if($erphp_see2_style){
						return '<div class="erphpdown-content-vip erphpdown-content-vip2">'.__('您暂时无权查看此隐藏内容！','erphpdown').'</div>';
					}else{
						if($erphp_wppay_vip){
							$content.='此内容仅限'.$erphp_quarter_name.'购买<a href="'.$erphp_url_front_vip.'" class="erphpdown-vip" target="_blank">升级'.$erphp_quarter_name.'</a>';
						}else{
							$content.='此内容仅限'.$erphp_quarter_name.'购买，请先<a href="'.$erphp_url_front_login.'" class="erphp-login-must">登录</a>';
						}
					}
				}elseif($memberDown == 18){
					if($erphp_see2_style){
						return '<div class="erphpdown-content-vip erphpdown-content-vip2">'.__('您暂时无权查看此隐藏内容！','erphpdown').'</div>';
					}else{
						if($erphp_wppay_vip){
							$content.='此内容仅限'.$erphp_year_name.'购买<a href="'.$erphp_url_front_vip.'" class="erphpdown-vip" target="_blank">升级'.$erphp_year_name.'</a>';
						}else{
							$content.='此内容仅限'.$erphp_year_name.'购买，请先<a href="'.$erphp_url_front_login.'" class="erphp-login-must">登录</a>';
						}
					}
				}elseif($memberDown == 19){
					if($erphp_see2_style){
						return '<div class="erphpdown-content-vip erphpdown-content-vip2">'.__('您暂时无权查看此隐藏内容！','erphpdown').'</div>';
					}else{
						if($erphp_wppay_vip){
							$content.='此内容仅限'.$erphp_life_name.'购买<a href="'.$erphp_url_front_vip.'" class="erphpdown-vip" target="_blank">升级'.$erphp_life_name.'</a>';
						}else{
							$content.='此内容仅限'.$erphp_life_name.'购买，请先<a href="'.$erphp_url_front_login.'" class="erphp-login-must">登录</a>';
						}
					}
				}elseif($memberDown == 11){
					if($erphp_see2_style){
						return '<div class="erphpdown-content-vip erphpdown-content-vip2">'.__('您暂时无权查看此隐藏内容！','erphpdown').'</div>';
					}else{
						if($erphp_wppay_vip){
							$content.='此内容仅限'.$erphp_vip_name.'购买、'.$erphp_year_name.' 5折<a href="'.$erphp_url_front_vip.'" class="erphpdown-vip" target="_blank">升级'.$erphp_vip_name.'</a>';
						}else{
							$content.='此内容仅限'.$erphp_vip_name.'购买、'.$erphp_year_name.' 5折，请先<a href="'.$erphp_url_front_login.'" class="erphp-login-must">登录</a>';
						}
					}
				}elseif($memberDown == 12){
					if($erphp_see2_style){
						return '<div class="erphpdown-content-vip erphpdown-content-vip2">'.__('您暂时无权查看此隐藏内容！','erphpdown').'</div>';
					}else{
						if($erphp_wppay_vip){
							$content.='此内容仅限'.$erphp_vip_name.'购买、'.$erphp_year_name.' 8折<a href="'.$erphp_url_front_vip.'" class="erphpdown-vip" target="_blank">升级'.$erphp_vip_name.'</a>';
						}else{
							$content.='此内容仅限'.$erphp_vip_name.'购买、'.$erphp_year_name.' 8折，请先<a href="'.$erphp_url_front_login.'" class="erphp-login-must">登录</a>';
						}
					}
				}else{
					$vip_content = '';
					if($memberDown==3){
						$vip_content.='（'.$erphp_vip_name.'免费）';
					}elseif($memberDown==2){
						$vip_content.='（'.$erphp_vip_name.' 5折）';
					}elseif($memberDown==13){
						$vip_content.='（'.$erphp_vip_name.' 5折、'.$erphp_life_name.'免费）';
					}elseif($memberDown==5){
						$vip_content.='（'.$erphp_vip_name.' 8折）';
					}elseif($memberDown==14){
						$vip_content.='（'.$erphp_vip_name.' 8折、'.$erphp_life_name.'免费）';
					}elseif($memberDown==20){
						$vip_content.='（'.$erphp_vip_discounts.'、'.$erphp_life_name.'免费）';
					}elseif($memberDown==16){
						$vip_content .= '（'.$erphp_quarter_name.'免费）';
					}elseif($memberDown==6){
						$vip_content .= '（'.$erphp_year_name.'免费）';
					}elseif($memberDown==7){
						$vip_content .= '（'.$erphp_life_name.'免费）';
					}

					if(get_option('erphp_wppay_down')){
						$user_id = is_user_logged_in() ? wp_get_current_user()->ID : 0;
						$wppay = new EPD(get_the_ID(), $user_id);
						if($wppay->isWppayPaid() || $wppay->isWppayPaidNew()){
							return '<div class="erphpdown-content-view">'.do_shortcode($content2).'</div>';
						}else{
							if($price){
								if($erphp_see2_style){
									return '<div class="erphpdown-content-vip erphpdown-content-vip2">'.__('您暂时无权查看此隐藏内容！','erphpdown').'</div>';
								}else{
									$content.='此内容查看价格为<span class="erphpdown-price">'.$price.'</span>'.get_option('ice_name_alipay');
									$content.='<a class="erphpdown-iframe erphpdown-buy" href="'.constant("erphpdown").'buy.php?postid='.get_the_ID().'&timestamp='.time().'" target="_blank">立即购买</a>';

									$content .= $vip_content?($vip_content.'<a href="'.$erphp_url_front_login.'" target="_blank" class="erphpdown-vip erphp-login-must">立即升级</a>'):'';
								}
							}else{
								if(!get_option('erphp_free_login')){
									return '<div class="erphpdown-content-view">'.do_shortcode($content2).'</div>';
								}else{
									if($erphp_see2_style){
										return '<div class="erphpdown-content-vip erphpdown-content-vip2">'.__('您暂时无权查看此隐藏内容！','erphpdown').'</div>';
									}else{
										$content.='此内容仅限注册用户查看，请先<a href="'.$erphp_url_front_login.'" class="erphp-login-must">登录</a>';
									}
								}
							}
						}
					}else{
						if($erphp_see2_style){
							return '<div class="erphpdown-content-vip erphpdown-content-vip2">'.__('您暂时无权查看此隐藏内容！','erphpdown').'</div>';
						}else{
							if($price){
								$content.='此内容查看价格为<span class="erphpdown-price">'.$price.'</span>'.get_option('ice_name_alipay').$vip_content.'，请先<a href="'.$erphp_url_front_login.'" target="_blank" class="erphp-login-must">登录</a>';
							}else{
								$content.='此内容仅限注册用户查看，请先<a href="'.$erphp_url_front_login.'" class="erphp-login-must">登录</a>';
							}
						}
					}
				}
				
				if(get_option('ice_tips_see')) $content.='<div class="erphpdown-tips">'.get_option('ice_tips_see').'</div>';
				$content.='</div>';
				return $content;
			}
		}
	}
}  
add_shortcode('erphpdown','erphpdown_shortcode_see');

function erphpdown_shortcode_vip($atts, $content=null){
	$atts = shortcode_atts( array(
        'type' => '',
    ), $atts, 'vip' );

  	global $post;

  	$erphp_life_name    = get_option('erphp_life_name')?get_option('erphp_life_name'):'终身VIP';
	$erphp_year_name    = get_option('erphp_year_name')?get_option('erphp_year_name'):'包年VIP';
	$erphp_quarter_name = get_option('erphp_quarter_name')?get_option('erphp_quarter_name'):'包季VIP';
	$erphp_month_name  = get_option('erphp_month_name')?get_option('erphp_month_name'):'包月VIP';
	$erphp_day_name  = get_option('erphp_day_name')?get_option('erphp_day_name'):'体验VIP';
	$erphp_vip_name  = get_option('erphp_vip_name')?get_option('erphp_vip_name'):'VIP';

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

    $erphp_url_front_vip = get_bloginfo('wpurl').'/wp-admin/admin.php?page=erphpdown/admin/erphp-update-vip.php';
	if(get_option('erphp_url_front_vip')){
		$erphp_url_front_vip = get_option('erphp_url_front_vip');
	}
	$erphp_url_front_login = wp_login_url(get_permalink());
	if(get_option('erphp_url_front_login')){
		$erphp_url_front_login = get_option('erphp_url_front_login');
	}

	$vip = '<div class="erphpdown erphpdown-see erphpdown-content-vip" style="display:block">此隐藏内容仅限'.$erphp_vip_name.'查看<a href="'.$erphp_url_front_vip.'" target="_blank" class="erphpdown-vip">升级'.$erphp_vip_name.'</a></div>';
	$userType=getUsreMemberType();

	if(is_user_logged_in() || $userType){
		if(is_single()){
			$categories = get_the_category();
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
		}

		$user_info = wp_get_current_user();
		if(!$atts['type']){
			if($userType){
				//return '<div class="erphpdown-content-view">'.do_shortcode($content).'</div>';

				if(checkSeeHas($user_info->ID,$post->ID)){
					return '<div class="erphpdown-content-view">'.do_shortcode($content).'</div>';
				}else{
					if($userType == 6 && $erphp_day_times > 0){
						if( checkSeeLog($user_info->ID,$post->ID,$erphp_day_times,erphpGetIP()) ){
							return '<p class="erphpdown-content-vip erphpdown-content-vip-see">您可免费查看此隐藏内容！<a href="javascript:;" class="erphpdown-see-btn" data-post="'.$post->ID.'" data-vip="1" data-token="'.md5($erphpdown_downkey.$post->ID.$erphpdown_downkey.'1').'">立即查看</a>（今日已查看'.getSeeCount($user_info->ID).'个，还可查看'.($erphp_day_times-getSeeCount($user_info->ID)).'个）</p>';
						}else{
							return '<p class="erphpdown-content-vip">您暂时无权查看此隐藏内容，请明天再来！（今日已查看'.getSeeCount($user_info->ID).'个，还可查看'.($erphp_day_times-getSeeCount($user_info->ID)).'个）</p>';
						}
					}elseif($userType == 7 && $erphp_month_times > 0){
						if( checkSeeLog($user_info->ID,$post->ID,$erphp_month_times,erphpGetIP()) ){
							return '<p class="erphpdown-content-vip erphpdown-content-vip-see">您可免费查看此隐藏内容！<a href="javascript:;" class="erphpdown-see-btn" data-post="'.$post->ID.'" data-vip="1" data-token="'.md5($erphpdown_downkey.$post->ID.$erphpdown_downkey.'1').'">立即查看</a>（今日已查看'.getSeeCount($user_info->ID).'个，还可查看'.($erphp_month_times-getSeeCount($user_info->ID)).'个）</p>';
						}else{
							return '<p class="erphpdown-content-vip">您暂时无权查看此隐藏内容，请明天再来！（今日已查看'.getSeeCount($user_info->ID).'个，还可查看'.($erphp_month_times-getSeeCount($user_info->ID)).'个）</p>';
						}
					}elseif($userType == 8 && $erphp_quarter_times > 0){
						if( checkSeeLog($user_info->ID,$post->ID,$erphp_quarter_times,erphpGetIP()) ){
							return '<p class="erphpdown-content-vip erphpdown-content-vip-see">您可免费查看此隐藏内容！<a href="javascript:;" class="erphpdown-see-btn" data-post="'.$post->ID.'" data-vip="1" data-token="'.md5($erphpdown_downkey.$post->ID.$erphpdown_downkey.'1').'">立即查看</a>（今日已查看'.getSeeCount($user_info->ID).'个，还可查看'.($erphp_quarter_times-getSeeCount($user_info->ID)).'个）</p>';
						}else{
							return '<p class="erphpdown-content-vip">您暂时无权查看此隐藏内容，请明天再来！（今日已查看'.getSeeCount($user_info->ID).'个，还可查看'.($erphp_quarter_times-getSeeCount($user_info->ID)).'个）</p>';
						}
					}elseif($userType == 9 && $erphp_year_times > 0){
						if( checkSeeLog($user_info->ID,$post->ID,$erphp_year_times,erphpGetIP()) ){
							return '<p class="erphpdown-content-vip erphpdown-content-vip-see">您可免费查看此隐藏内容！<a href="javascript:;" class="erphpdown-see-btn" data-post="'.$post->ID.'" data-vip="1" data-token="'.md5($erphpdown_downkey.$post->ID.$erphpdown_downkey.'1').'">立即查看</a>（今日已查看'.getSeeCount($user_info->ID).'个，还可查看'.($erphp_year_times-getSeeCount($user_info->ID)).'个）</p>';
						}else{
							return '<p class="erphpdown-content-vip">您暂时无权查看此隐藏内容，请明天再来！（今日已查看'.getSeeCount($user_info->ID).'个，还可查看'.($erphp_year_times-getSeeCount($user_info->ID)).'个）</p>';
						}
					}elseif($userType == 10 && $erphp_life_times > 0){
						if( checkSeeLog($user_info->ID,$post->ID,$erphp_life_times,erphpGetIP()) ){
							return '<p class="erphpdown-content-vip erphpdown-content-vip-see">您可免费查看此隐藏内容！<a href="javascript:;" class="erphpdown-see-btn" data-post="'.$post->ID.'" data-vip="1" data-token="'.md5($erphpdown_downkey.$post->ID.$erphpdown_downkey.'1').'">立即查看</a>（今日已查看'.getSeeCount($user_info->ID).'个，还可查看'.($erphp_life_times-getSeeCount($user_info->ID)).'个）</p>';
						}else{
							return '<p class="erphpdown-content-vip">您暂时无权查看此隐藏内容，请明天再来！（今日已查看'.getSeeCount($user_info->ID).'个，还可查看'.($erphp_life_times-getSeeCount($user_info->ID)).'个）</p>';
						}
					}else{
						return '<div class="erphpdown-content-view">'.do_shortcode($content).'</div>';
					}
				}

			}else{
				return $vip;
			}
		}else{
			if($atts['type'] == '6' && $userType < 6){
				return '<div class="erphpdown erphpdown-see erphpdown-content-vip" style="display:block">此隐藏内容仅限'.$erphp_vip_name.'查看<a href="'.$erphp_url_front_vip.'" target="_blank" class="erphpdown-vip">升级'.$erphp_vip_name.'</a></div>';
			}elseif($atts['type'] == '7' && $userType < 7){
				return '<div class="erphpdown erphpdown-see erphpdown-content-vip" style="display:block">此隐藏内容仅限'.$erphp_month_name.'查看<a href="'.$erphp_url_front_vip.'" target="_blank" class="erphpdown-vip">升级'.$erphp_month_name.'</a></div>';
			}elseif($atts['type'] == '8' && $userType < 8){
				return '<div class="erphpdown erphpdown-see erphpdown-content-vip" style="display:block">此隐藏内容仅限'.$erphp_quarter_name.'查看<a href="'.$erphp_url_front_vip.'" target="_blank" class="erphpdown-vip">升级'.$erphp_quarter_name.'</a></div>';
			}elseif($atts['type'] == '9' && $userType < 9){
				return '<div class="erphpdown erphpdown-see erphpdown-content-vip" style="display:block">此隐藏内容仅限'.$erphp_year_name.'查看<a href="'.$erphp_url_front_vip.'" target="_blank" class="erphpdown-vip">升级'.$erphp_year_name.'</a></div>';
			}elseif($atts['type'] == '10' && $userType < 10){
				return '<div class="erphpdown erphpdown-see erphpdown-content-vip" style="display:block">此隐藏内容仅限'.$erphp_life_name.'查看<a href="'.$erphp_url_front_vip.'" target="_blank" class="erphpdown-vip">升级'.$erphp_life_name.'</a></div>';
			}else{
				//return '<div class="erphpdown-content-view">'.do_shortcode($content).'</div>';

				if(checkSeeHas($user_info->ID,$post->ID)){
					return '<div class="erphpdown-content-view">'.do_shortcode($content).'</div>';
				}else{
					if($userType == 6 && $erphp_day_times > 0){
						if( checkSeeLog($user_info->ID,$post->ID,$erphp_day_times,erphpGetIP()) ){
							return '<p class="erphpdown-content-vip erphpdown-content-vip-see">您可免费查看此隐藏内容！<a href="javascript:;" class="erphpdown-see-btn" data-post="'.$post->ID.'" data-vip="'.$atts['type'].'" data-token="'.md5($erphpdown_downkey.$post->ID.$erphpdown_downkey.$atts['type']).'">立即查看</a>（今日已查看'.getSeeCount($user_info->ID).'个，还可查看'.($erphp_day_times-getSeeCount($user_info->ID)).'个）</p>';
						}else{
							return '<p class="erphpdown-content-vip">您暂时无权查看此隐藏内容，请明天再来！（今日已查看'.getSeeCount($user_info->ID).'个，还可查看'.($erphp_day_times-getSeeCount($user_info->ID)).'个）</p>';
						}
					}elseif($userType == 7 && $erphp_month_times > 0){
						if( checkSeeLog($user_info->ID,$post->ID,$erphp_month_times,erphpGetIP()) ){
							return '<p class="erphpdown-content-vip erphpdown-content-vip-see">您可免费查看此隐藏内容！<a href="javascript:;" class="erphpdown-see-btn" data-post="'.$post->ID.'" data-vip="'.$atts['type'].'" data-token="'.md5($erphpdown_downkey.$post->ID.$erphpdown_downkey.$atts['type']).'">立即查看</a>（今日已查看'.getSeeCount($user_info->ID).'个，还可查看'.($erphp_month_times-getSeeCount($user_info->ID)).'个）</p>';
						}else{
							return '<p class="erphpdown-content-vip">您暂时无权查看此隐藏内容，请明天再来！（今日已查看'.getSeeCount($user_info->ID).'个，还可查看'.($erphp_month_times-getSeeCount($user_info->ID)).'个）</p>';
						}
					}elseif($userType == 8 && $erphp_quarter_times > 0){
						if( checkSeeLog($user_info->ID,$post->ID,$erphp_quarter_times,erphpGetIP()) ){
							return '<p class="erphpdown-content-vip erphpdown-content-vip-see">您可免费查看此隐藏内容！<a href="javascript:;" class="erphpdown-see-btn" data-post="'.$post->ID.'" data-vip="'.$atts['type'].'" data-token="'.md5($erphpdown_downkey.$post->ID.$erphpdown_downkey.$atts['type']).'">立即查看</a>（今日已查看'.getSeeCount($user_info->ID).'个，还可查看'.($erphp_quarter_times-getSeeCount($user_info->ID)).'个）</p>';
						}else{
							return '<p class="erphpdown-content-vip">您暂时无权查看此隐藏内容，请明天再来！（今日已查看'.getSeeCount($user_info->ID).'个，还可查看'.($erphp_quarter_times-getSeeCount($user_info->ID)).'个）</p>';
						}
					}elseif($userType == 9 && $erphp_year_times > 0){
						if( checkSeeLog($user_info->ID,$post->ID,$erphp_year_times,erphpGetIP()) ){
							return '<p class="erphpdown-content-vip erphpdown-content-vip-see">您可免费查看此隐藏内容！<a href="javascript:;" class="erphpdown-see-btn" data-post="'.$post->ID.'" data-vip="'.$atts['type'].'" data-token="'.md5($erphpdown_downkey.$post->ID.$erphpdown_downkey.$atts['type']).'">立即查看</a>（今日已查看'.getSeeCount($user_info->ID).'个，还可查看'.($erphp_year_times-getSeeCount($user_info->ID)).'个）</p>';
						}else{
							return '<p class="erphpdown-content-vip">您暂时无权查看此隐藏内容，请明天再来！（今日已查看'.getSeeCount($user_info->ID).'个，还可查看'.($erphp_year_times-getSeeCount($user_info->ID)).'个）</p>';
						}
					}elseif($userType == 10 && $erphp_life_times > 0){
						if( checkSeeLog($user_info->ID,$post->ID,$erphp_life_times,erphpGetIP()) ){
							return '<p class="erphpdown-content-vip erphpdown-content-vip-see">您可免费查看此隐藏内容！<a href="javascript:;" class="erphpdown-see-btn" data-post="'.$post->ID.'" data-vip="'.$atts['type'].'" data-token="'.md5($erphpdown_downkey.$post->ID.$erphpdown_downkey.$atts['type']).'">立即查看</a>（今日已查看'.getSeeCount($user_info->ID).'个，还可查看'.($erphp_life_times-getSeeCount($user_info->ID)).'个）</p>';
						}else{
							return '<p class="erphpdown-content-vip">您暂时无权查看此隐藏内容，请明天再来！（今日已查看'.getSeeCount($user_info->ID).'个，还可查看'.($erphp_life_times-getSeeCount($user_info->ID)).'个）</p>';
						}
					}else{
						return '<div class="erphpdown-content-view">'.do_shortcode($content).'</div>';
					}
				}
			}
		}
	}else{
		return $vip;
	}			
}  
add_shortcode('vip','erphpdown_shortcode_vip');