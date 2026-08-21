<?php
session_start();
require( dirname(__FILE__) . '/../../../../../wp-load.php' );
$erphpdown_token = '';
if(isset($_SESSION['erphpdown_token'])){
	$erphpdown_token = $_SESSION['erphpdown_token'];
}
if(is_user_logged_in()){
	global $wpdb, $wppay_table_name;
	if($_POST['do']=='checkOrder' && $_POST['token'] == $erphpdown_token && $erphpdown_token){
		global $current_user;
		$id = esc_sql($_POST['order']);
		$result = $wpdb->get_var("select ice_success from $wpdb->icemoney where ice_user_id = '".$current_user->ID."' and ice_id='".$id."'");
		if($result){
			echo '1';
		}else{
			echo '0';
		}
	}elseif($_POST['do'] == 'delorder'){
		if(current_user_can('administrator')){
			$result = $wpdb->query("delete from $wpdb->icealipay where ice_id=".esc_sql($_POST['id']));
			if($result){
				echo '1';
			}else{
				echo '0';
			}
		}
	}elseif($_POST['do'] == 'fkorder'){
		if(current_user_can('administrator')){
			$postid = esc_sql($_POST['pid']);
			$userid = esc_sql($_POST['uid']);
			if($userid){
				$down_activation = get_post_meta($postid, 'down_activation', true);
				if($down_activation && function_exists('doErphpAct')){
					$activation_num = doErphpAct($userid,$postid);
					if($activation_num){
						$wpdb->query("update $wpdb->icealipay set ice_data = '".$activation_num."' where ice_id='".esc_sql($_POST['id'])."'");
						$user_info = get_user_by('id',$userid);
						if($user_info->user_email){
							$subject   = get_post($postid)->post_title;
							$headers = 'Content-Type: text/html; charset=' . get_option('blog_charset') . "\n";
							wp_mail($user_info->user_email, '【'.$subject.'】激活码补发', '您购买的资源【'.$subject.'】激活码：'.$activation_num, $headers);
						}
						echo '1';
					}else{
						echo '补发失败，库存不足';
					}
				}else{
					echo '补发失败';
				}
			}else{
				echo '补发失败，这单没有用户ID';
			}
			
		}
	}elseif($_POST['do'] == 'delviporder'){
		if(current_user_can('administrator')){
			$result = $wpdb->query("delete from $wpdb->vip where ice_id=".esc_sql($_POST['id']));
			if($result){
				echo '1';
			}else{
				echo '0';
			}
		}
	}elseif($_POST['do'] == 'delvipcatorder'){
		if(current_user_can('administrator')){
			$result = $wpdb->query("delete from $wpdb->vipcat where ice_id=".esc_sql($_POST['id']));
			if($result){
				echo '1';
			}else{
				echo '0';
			}
		}
	}elseif($_POST['do'] == 'delpost'){
		if(current_user_can('administrator')){
			$result = $wpdb->query("delete from $wpdb->icealipay where ice_post=".esc_sql($_POST['id']));
			if($result){
				echo '1';
			}else{
				echo '0';
			}
		}
	}elseif($_POST['do'] == 'delwppay'){
		if(current_user_can('administrator')){
			$result = $wpdb->query("delete from $wppay_table_name where id=".esc_sql($_POST['id']));
			if($result){
				echo '1';
			}else{
				echo '0';
			}
		}
	}elseif($_POST['do'] == 'delchong'){
		if(current_user_can('administrator')){
			$result = $wpdb->query("delete from $wpdb->icemoney where ice_id=".esc_sql($_POST['id']));
			if($result){
				echo '1';
			}else{
				echo '0';
			}
		}
	}elseif($_POST['do'] == 'yeschong'){
		if(current_user_can('administrator')){
			$out_trade_no = esc_sql($_POST['id']);
			$result = $wpdb->get_row("select ice_money,ice_alipay from $wpdb->icemoney where ice_num='".$out_trade_no."' and ice_success=0");
			if($result){
				if(strstr($out_trade_no,'MD') || strstr($out_trade_no,'FK')){
					epd_set_wppay_success($out_trade_no,$result->ice_money,$result->ice_alipay);
				}else{
					epd_set_order_success($out_trade_no,$result->ice_money,$result->ice_alipay);
				}

				echo '1';
			}
		}
	}elseif($_POST['do'] == 'delindex'){
		if(current_user_can('administrator')){
			$result = $wpdb->query("delete from $wpdb->iceindex where ice_id=".esc_sql($_POST['id']));
			if($result){
				echo '1';
			}else{
				echo '0';
			}
		}
	}elseif($_POST['do'] == 'deltuan'){
		if(current_user_can('administrator')){
			$result = $wpdb->query("delete from ".$wpdb->prefix."ice_tuan_order where ice_id=".esc_sql($_POST['id']));
			if($result){
				echo '1';
			}else{
				echo '0';
			}
		}
	}elseif($_POST['do'] == 'delfreedown'){
		if(current_user_can('administrator')){
			$result = $wpdb->query("delete from $wpdb->down where ice_id=".esc_sql($_POST['id']));
			if($result){
				echo '1';
			}else{
				echo '0';
			}
		}
	}elseif($_POST['do'] == 'dellog'){
		if(current_user_can('administrator')){
			$result = $wpdb->query("delete from $wpdb->icelog where id=".esc_sql($_POST['id']));
			if($result){
				echo '1';
			}else{
				echo '0';
			}
		}
	}elseif($_POST['do'] == 'deltx'){
		if(current_user_can('administrator')){
			$result = $wpdb->query("delete from $wpdb->iceget where ice_id=".esc_sql($_POST['id']));
			if($result){
				echo '1';
			}else{
				echo '0';
			}
		}
	}
}else{
	global $wpdb, $wppay_table_name;
	if($_POST['do']=='checkOrder' && $_POST['token'] == $erphpdown_token && $erphpdown_token){
		$id = esc_sql($_POST['order']);
		$result = $wpdb->get_var("select ice_success from $wpdb->icemoney where ice_user_id = 0 and ice_id='".$id."'");
		if($result){
			echo '1';
		}else{
			echo '0';
		}
	}
}
exit;