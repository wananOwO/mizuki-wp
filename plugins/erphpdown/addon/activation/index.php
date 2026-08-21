<?php 
if(!function_exists('getErphpActivation')){
	function getErphpActivation($id){
		global $wpdb;
		$result = $wpdb->get_row("select * from $wpdb->erphpact where id = '".$id."'");
		return $result;
	}
}

if(!function_exists('getErphpActStatus')){
	function getErphpActStatus($id){
		global $wpdb;
		$result = $wpdb->get_row("select * from $wpdb->erphpact where id = '".$id."'");
		if($result->status == 1){
			if($result->uid){
				return '已发放，用户ID：'.get_user_by('id',$result->uid)->user_login.'，发放日期：'.$result->usetime;
			}else{
				return '已发放，用户为游客，发放日期：'.$result->usetime;
			}
		}else{
			return '无';
		}
	}
}

if(!function_exists('doErphpAct')){
	function doErphpAct($uid, $pid){
		date_default_timezone_set('Asia/Shanghai');
		global $wpdb;
		$result = $wpdb->get_row("select id,num from $wpdb->erphpact where status = 0 and pid=$pid order by id asc limit 1");
		if($result){
			$wpdb->query("update $wpdb->erphpact set status=1,usetime='".date("Y-m-d H:i:s")."',uid=$uid where id=".$result->id);
			return $result->num;
		}else{
			return '';
		}
	}
}

function doErphpActKa($uid, $pid, $onum=''){
	date_default_timezone_set('Asia/Shanghai');
	global $wpdb;
	$result = $wpdb->get_row("select id,num from $wpdb->erphpact where status = 0 and pid=$pid order by id asc limit 1");
	if($result){
		$wpdb->query("update $wpdb->erphpact set status=1,usetime='".date("Y-m-d H:i:s")."',uid=$uid,ice_num='".esc_sql($onum)."' where id=".$result->id);
		return $result->num;
	}else{
		return '';
	}
}

if(!function_exists('getErphpActLeft')){
	function getErphpActLeft($pid){
		global $wpdb;
		$result = $wpdb->get_var("select count(id) from $wpdb->erphpact where pid = '".$pid."' and status = 0");
		return $result;
	}
}