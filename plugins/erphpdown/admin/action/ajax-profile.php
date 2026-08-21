<?php 
require( dirname(__FILE__) . '/../../../../../wp-load.php' );
if(is_user_logged_in()){
	global $wpdb;
	if($_POST['do']=='profile'){
		$userdata = array();
		$userdata['ID'] = wp_get_current_user()->ID;
		$userdata['nickname'] = str_replace(array('<','>','&','"','\'','#','^','*','_','+','$','?','!'), '', esc_sql($_POST['mm_name']));
		$userdata['user_email'] = esc_sql($_POST['mm_mail']);
		$userdata['user_url'] = esc_sql($_POST['mm_url']);
		$userdata['description'] = esc_sql($_POST['mm_desc']);
		wp_update_user($userdata);
		echo "success";
	}elseif($_POST['do']=='password'){
		$userdata = array();
		$userdata['ID'] = wp_get_current_user()->ID;
		$userdata['user_pass'] = esc_sql($_POST['mm_pass_new']);
		wp_update_user($userdata);
		echo "success";  
	}
}
