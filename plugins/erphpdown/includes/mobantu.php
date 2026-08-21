<?php
// +----------------------------------------------------------------------
// | ERPHP [ PHP DEVELOP ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.mobantu.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: mobantu <82708210@qq.com>
// +----------------------------------------------------------------------
if ( !defined('ABSPATH') ) {exit;}

function addDownLog($uid,$pid,$ip,$vip=1){
	date_default_timezone_set('Asia/Shanghai');
	global $wpdb;
	$sql="insert into $wpdb->down(ice_user_id,ice_post_id,ice_vip,ice_ip,ice_time)values('".$uid."','".$pid."','".$vip."','".$ip."','".date("Y-m-d H:i:s")."')";
	$wpdb->query($sql);
}

function checkDownLogNoVip($uid,$pid,$times){
	date_default_timezone_set('Asia/Shanghai');
	global $wpdb;
	$vip_times_see = get_option('vip_times_see');
	if($vip_times_see){
		$result = $wpdb->get_var("select count(distinct ice_post_id) from $wpdb->down where ice_user_id=".$uid." and ice_type=0 and DATEDIFF(ice_time,NOW())=0");
	}else{
		$result = $wpdb->get_var("select count(distinct ice_post_id) from $wpdb->down where ice_user_id=".$uid." and DATEDIFF(ice_time,NOW())=0");
	}
	if($result > $times){
		return false;
	}elseif($result == $times){
		if($vip_times_see){
			$exist = $wpdb->get_var("select ice_id from $wpdb->down where ice_user_id=".$uid." and ice_type=0 and DATEDIFF(ice_time,NOW())=0 and ice_post_id = $pid");
		}else{
			$exist = $wpdb->get_var("select ice_id from $wpdb->down where ice_user_id=".$uid." and DATEDIFF(ice_time,NOW())=0 and ice_post_id = $pid");
		}
		if($exist) 
			return true;
		else 
			return false;
	}
	else{
		return true;
	}
}

function checkDownLog($uid,$pid,$times,$vip=1){
	date_default_timezone_set('Asia/Shanghai');
	global $wpdb;
	$vip_times_see = get_option('vip_times_see');
	if($vip_times_see){
		$result = $wpdb->get_var("select count(distinct ice_post_id) from $wpdb->down where ice_user_id=".$uid." and ice_vip=".$vip." and ice_type=0 and DATEDIFF(ice_time,NOW())=0");
	}else{
		$result = $wpdb->get_var("select count(distinct ice_post_id) from $wpdb->down where ice_user_id=".$uid." and ice_vip=".$vip." and DATEDIFF(ice_time,NOW())=0");
	}
	if($result > $times){
		return false;
	}elseif($result == $times){
		if($vip_times_see){
			$exist = $wpdb->get_var("select ice_id from $wpdb->down where ice_user_id=".$uid." and ice_vip=".$vip." and ice_type=0 and DATEDIFF(ice_time,NOW())=0 and ice_post_id = $pid");
		}else{
			$exist = $wpdb->get_var("select ice_id from $wpdb->down where ice_user_id=".$uid." and ice_vip=".$vip." and DATEDIFF(ice_time,NOW())=0 and ice_post_id = $pid");
		}
		if($exist) 
			return true;
		else 
			return false;
	}else{
		return true;
	}
}

function addSeeLog($uid,$pid,$ip,$vip=1,$type=0){
	date_default_timezone_set('Asia/Shanghai');
	global $wpdb;
	$sql="insert into $wpdb->down(ice_user_id,ice_post_id,ice_vip,ice_type,ice_ip,ice_time)values('".$uid."','".$pid."','".$vip."','".$type."','".$ip."','".date("Y-m-d H:i:s")."')";
	$wpdb->query($sql);
}

function getSeeCountNoVip($uid){ //下载，不是查看
	date_default_timezone_set('Asia/Shanghai');
	global $wpdb;
	if(get_option('vip_times_see')){
		$result = $wpdb->get_var("select count(distinct ice_post_id) from $wpdb->down where ice_user_id=".$uid." and ice_type=0 and DATEDIFF(ice_time,NOW())=0");
	}else{
		$result = $wpdb->get_var("select count(distinct ice_post_id) from $wpdb->down where ice_user_id=".$uid." and DATEDIFF(ice_time,NOW())=0");
	}
	return $result;
}

function getDownCount($uid,$vip=1){
	date_default_timezone_set('Asia/Shanghai');
	global $wpdb;
	if(get_option('vip_times_see')){
		if($uid){
			$result = $wpdb->get_var("select count(distinct ice_post_id) from $wpdb->down where ice_user_id=".$uid." and ice_vip=".$vip." and ice_type=0 and DATEDIFF(ice_time,NOW())=0");
		}else{
			$result = $wpdb->get_var("select count(distinct ice_post_id) from $wpdb->down where ice_ip='".erphpGetIP()."' and ice_vip=".$vip." and ice_type=0 and DATEDIFF(ice_time,NOW())=0");
		}
	}else{
		if($uid){
			$result = $wpdb->get_var("select count(distinct ice_post_id) from $wpdb->down where ice_user_id=".$uid." and ice_vip=".$vip." and DATEDIFF(ice_time,NOW())=0");
		}else{
			$result = $wpdb->get_var("select count(distinct ice_post_id) from $wpdb->down where ice_ip='".erphpGetIP()."' and ice_vip=".$vip." and DATEDIFF(ice_time,NOW())=0");
		}
	}
	return $result;
}

function getSeeCount($uid,$vip=1){
	date_default_timezone_set('Asia/Shanghai');
	global $wpdb;
	if(get_option('vip_times_see')){
		if($uid){
			$result = $wpdb->get_var("select count(distinct ice_post_id) from $wpdb->down where ice_user_id=".$uid." and ice_vip=".$vip." and ice_type=1 and DATEDIFF(ice_time,NOW())=0");
		}else{
			$result = $wpdb->get_var("select count(distinct ice_post_id) from $wpdb->down where ice_ip='".erphpGetIP()."' and ice_vip=".$vip." and ice_type=1 and DATEDIFF(ice_time,NOW())=0");
		}
	}else{
		if($uid){
			$result = $wpdb->get_var("select count(distinct ice_post_id) from $wpdb->down where ice_user_id=".$uid." and ice_vip=".$vip." and DATEDIFF(ice_time,NOW())=0");
		}else{
			$result = $wpdb->get_var("select count(distinct ice_post_id) from $wpdb->down where ice_ip='".erphpGetIP()."' and ice_vip=".$vip." and DATEDIFF(ice_time,NOW())=0");
		}
	}
	return $result;
}

function checkSeeLog($uid,$pid,$times,$ip,$vip=1){
	date_default_timezone_set('Asia/Shanghai');
	global $wpdb;

	if(get_option('vip_times_see')){
		if($uid){
			$result = $wpdb->get_var("select count(distinct ice_post_id) from $wpdb->down where ice_user_id=".$uid." and ice_vip=".$vip." and ice_type=1 and DATEDIFF(ice_time,NOW())=0");
		}else{
			$result = $wpdb->get_var("select count(distinct ice_post_id) from $wpdb->down where ice_ip='".$ip."' and ice_vip=".$vip." and ice_type=1 and DATEDIFF(ice_time,NOW())=0");
		}
	}else{
		if($uid){
			$result = $wpdb->get_var("select count(distinct ice_post_id) from $wpdb->down where ice_user_id=".$uid." and ice_vip=".$vip." and DATEDIFF(ice_time,NOW())=0");
		}else{
			$result = $wpdb->get_var("select count(distinct ice_post_id) from $wpdb->down where ice_ip='".$ip."' and ice_vip=".$vip." and DATEDIFF(ice_time,NOW())=0");
		}
	}
	
	if($result >= $times) 
		return false;
	else 
		return true;
}

function checkDownHas($uid,$pid,$vip=1){
	date_default_timezone_set('Asia/Shanghai');
	global $wpdb;
	if($uid){
		$exist = $wpdb->get_var("select ice_id from $wpdb->down where ice_user_id=".$uid." and ice_vip=".$vip." and DATEDIFF(ice_time,NOW())=0 and ice_post_id = $pid");
	}else{
		$exist = $wpdb->get_var("select ice_id from $wpdb->down where ice_ip='".erphpGetIP()."' and ice_vip=".$vip." and DATEDIFF(ice_time,NOW())=0 and ice_post_id = $pid");
	}
	if($exist) 
		return true;
	else 
		return false;
}

function checkSeeHas($uid,$pid,$vip=1){
	date_default_timezone_set('Asia/Shanghai');
	global $wpdb;
	if(get_option('vip_times_see')){
		if($uid){
			$exist = $wpdb->get_var("select ice_id from $wpdb->down where ice_user_id=".$uid." and ice_vip=".$vip." and ice_type=1 and DATEDIFF(ice_time,NOW())=0 and ice_post_id = $pid");
		}else{
			$exist = $wpdb->get_var("select ice_id from $wpdb->down where ice_ip='".erphpGetIP()."' and ice_vip=".$vip." and ice_type=1 and DATEDIFF(ice_time,NOW())=0 and ice_post_id = $pid");
		}
	}else{
		if($uid){
			$exist = $wpdb->get_var("select ice_id from $wpdb->down where ice_user_id=".$uid." and ice_vip=".$vip." and DATEDIFF(ice_time,NOW())=0 and ice_post_id = $pid");
		}else{
			$exist = $wpdb->get_var("select ice_id from $wpdb->down where ice_ip='".erphpGetIP()."' and ice_vip=".$vip." and DATEDIFF(ice_time,NOW())=0 and ice_post_id = $pid");
		}
	}
	if($exist) 
		return true;
	else 
		return false;
}

function addVipCatLog($price,$userType,$cat){
	date_default_timezone_set('Asia/Shanghai');
	global $wpdb;
	$user_info = wp_get_current_user();
	$sql="insert into $wpdb->vipcat(ice_price,ice_user_id,ice_user_type,ice_cat_id,ice_time)values('".$price."','".$user_info->ID."','".$userType."','".$cat."','".date("Y-m-d H:i:s")."')";
	$wpdb->query($sql);
}

function addVipCatLogByAdmin($price,$userType,$cat,$uid){
	date_default_timezone_set('Asia/Shanghai');
	global $wpdb;
	$sql="insert into $wpdb->vipcat(ice_price,ice_user_id,ice_user_type,ice_cat_id,ice_time)values('".$price."','".$uid."','".$userType."','".$cat."','".date("Y-m-d H:i:s")."')";
	$wpdb->query($sql);
}

function addVipLog($price,$userType){
	date_default_timezone_set('Asia/Shanghai');
	global $wpdb;
	$user_info = wp_get_current_user();
	$sql="insert into $wpdb->vip(ice_price,ice_user_id,ice_user_type,ice_time)values('".$price."','".$user_info->ID."','".$userType."','".date("Y-m-d H:i:s")."')";
	$wpdb->query($sql);
}

function addVipLogByAdmin($price,$userType,$uid,$ip=''){
	date_default_timezone_set('Asia/Shanghai');
	global $wpdb;
	if($ip){
		$sql="insert into $wpdb->vip(ice_price,ice_user_id,ice_user_type,ice_ip,ice_time)values('".$price."','".$uid."','".$userType."','".$ip."','".date("Y-m-d H:i:s")."')";
	}else{
		$sql="insert into $wpdb->vip(ice_price,ice_user_id,ice_user_type,ice_time)values('".$price."','".$uid."','".$userType."','".date("Y-m-d H:i:s")."')";
	}
	$wpdb->query($sql);
}

function addAffLog($price,$uid,$ip){
	date_default_timezone_set('Asia/Shanghai');
	global $wpdb;
	$sql="insert into $wpdb->aff(ice_price,ice_user_id,ice_ip,ice_time)values('".$price."','".$uid."','".$ip."','".date("Y-m-d H:i:s")."')";
	$wpdb->query($sql);
	addUserMoney($uid,$price,'推广奖励');
}

function checkAffLog($uid,$ip){
	global $wpdb;
	$result = $wpdb->get_var("select ice_id from $wpdb->aff where ice_user_id=".$uid." and ice_ip='".$ip."'");
	if($result) return false;
	else return true;
}

function getUsreMemberCatStatus(){
	if(is_user_logged_in()){
		date_default_timezone_set('Asia/Shanghai');
		global $wpdb;
		$user_info = wp_get_current_user();
		$result=$wpdb->get_results("select * from ".$wpdb->icecat." where ice_user_id=".$user_info->ID);

		if($result){
			foreach ($result as $userTypeInfo) {
				if($userTypeInfo->userType == '10'){
					return 10;
				}elseif($userTypeInfo->userType){
					if(time() > strtotime($userTypeInfo->endTime) +24*3600){
						$wpdb->query("update $wpdb->icecat set userType=0,endTime='1000-01-01' where ice_user_id=".$user_info->ID." and ice_id=".$userTypeInfo->ice_id);
					}else{
						return $userTypeInfo->userType;
					}
				}
				
			}
		}
	}
	return 0;
}

function getUsreMemberCat($cid){
	date_default_timezone_set('Asia/Shanghai');
	global $wpdb;
	if(is_user_logged_in() && $cid){
		$user_info = wp_get_current_user();
		$userTypeInfo=$wpdb->get_row("select * from ".$wpdb->icecat." where ice_user_id=".$user_info->ID." and ice_cat_id=".$cid);

		if($userTypeInfo){
			if($userTypeInfo->userType == '10'){

			}else{
				if(time() > strtotime($userTypeInfo->endTime) +24*3600)
				{
					$wpdb->query("update $wpdb->icecat set userType=0,endTime='1000-01-01' where ice_user_id=".$user_info->ID." and ice_cat_id=".$cid);
					return 0;
				}
			}
			return $userTypeInfo->userType;
		}
	}
	return 0;
}

function getUsreMemberCatById($uid,$cid){
	date_default_timezone_set('Asia/Shanghai');
	global $wpdb;
	if($uid && $cid){
		$userTypeInfo=$wpdb->get_row("select * from ".$wpdb->icecat." where ice_user_id=".$uid." and ice_cat_id=".$cid);
		if($userTypeInfo)
		{
			if($userTypeInfo->userType == '10'){

			}else{
				if(time() > strtotime($userTypeInfo->endTime) +24*3600)
				{
					$wpdb->query("update $wpdb->icecat set userType=0,endTime='1000-01-01' where ice_user_id=".$uid." and ice_cat_id=".$cid);
					return 0;
				}
			}
			return $userTypeInfo->userType;
		}
	}
	return 0;
}


function getUsreMemberType(){
	date_default_timezone_set('Asia/Shanghai');
	$erphp_life_days = get_option('erphp_life_days');
	global $wpdb;
	$ip = erphpGetIP();
	if(is_user_logged_in()){
		$user_info = wp_get_current_user();
		$userTypeInfo=$wpdb->get_row("select * from ".$wpdb->iceinfo." where ice_user_id=".$user_info->ID);

		if($userTypeInfo){
			if($userTypeInfo->userType == '10' && !$erphp_life_days){

			}else{
				if(time() > strtotime($userTypeInfo->endTime) +24*3600)
				{
					$wpdb->query("update $wpdb->iceinfo set userType=0,endTime='1000-01-01' where ice_user_id=".$user_info->ID);
					return 0;
				}
			}
			return $userTypeInfo->userType;
		}
	}elseif(get_option('erphp_wppay_vip') && $ip){
		$userTypeInfo=$wpdb->get_row("select * from ".$wpdb->iceinfo." where ice_ip='".$ip."'");

		if($userTypeInfo){
			if($userTypeInfo->userType == '10' && !$erphp_life_days){

			}else{
				if(time() > strtotime($userTypeInfo->endTime) +24*3600)
				{
					$wpdb->query("update $wpdb->iceinfo set userType=0,endTime='1000-01-01' where ice_ip='".$ip."'");
					return 0;
				}
			}
			return $userTypeInfo->userType;
		}
	}
	return 0;
}

function getUsreMemberTypeById($uid){
	date_default_timezone_set('Asia/Shanghai');
	$erphp_life_days    = get_option('erphp_life_days');
	$ip = erphpGetIP();
	global $wpdb;
	if($uid){
		$userTypeInfo=$wpdb->get_row("select * from  ".$wpdb->iceinfo." where ice_user_id=".$uid);
		if($userTypeInfo){
			if($userTypeInfo->userType == '10' && !$erphp_life_days){

			}else{
				if(time() > strtotime($userTypeInfo->endTime) +24*3600)
				{
					$wpdb->query("update $wpdb->iceinfo set userType=0,endTime='1000-01-01' where ice_user_id=".$uid);
					return 0;
				}
			}
			return $userTypeInfo->userType;
		}
	}elseif(get_option('erphp_wppay_vip') && $ip){
		$userTypeInfo=$wpdb->get_row("select * from ".$wpdb->iceinfo." where ice_ip='".$ip."'");

		if($userTypeInfo){
			if($userTypeInfo->userType == '10' && !$erphp_life_days){

			}else{
				if(time() > strtotime($userTypeInfo->endTime) +24*3600)
				{
					$wpdb->query("update $wpdb->iceinfo set userType=0,endTime='1000-01-01' where ice_ip='".$ip."'");
					return 0;
				}
			}
			return $userTypeInfo->userType;
		}
	}
	return 0;
}

function getUsreMemberCatEndTime($cid){
	global $wpdb;
	$erphp_life_days    = get_option('erphp_life_days');
	$user_info = wp_get_current_user();
	$userTypeInfo=$wpdb->get_row("select * from ".$wpdb->icecat." where ice_user_id=".$user_info->ID." and ice_cat_id=".$cid);
	if($userTypeInfo)
	{
		if($userTypeInfo->userType == '10' && !$erphp_life_days){
			return '终身';
		}else{
			return $userTypeInfo->endTime;
		}
	}
	return '';
}

function getUsreMemberCatEndTimeById($uid,$cid){
	global $wpdb;
	$erphp_life_days    = get_option('erphp_life_days');
	$userTypeInfo=$wpdb->get_row("select * from  ".$wpdb->icecat." where ice_user_id=".$uid." and ice_cat_id=".$cid);
	if($userTypeInfo)
	{
		if($userTypeInfo->userType == '10' && !$erphp_life_days){
			return '终身';
		}else{
			return $userTypeInfo->endTime;
		}
	}
	return '';
}

function getUsreMemberTypeEndTime(){
	global $wpdb;
	$erphp_life_days    = get_option('erphp_life_days');
	$user_info = wp_get_current_user();
	$userTypeInfo=$wpdb->get_row("select * from ".$wpdb->iceinfo." where ice_user_id=".$user_info->ID);
	if($userTypeInfo)
	{
		if($userTypeInfo->userType == '10' && !$erphp_life_days){
			return '终身';
		}else{
			return $userTypeInfo->endTime;
		}
	}
	return '';
}

function getUsreMemberTypeEndTimeById($uid){
	global $wpdb;
	$erphp_life_days    = get_option('erphp_life_days');
	$userTypeInfo=$wpdb->get_row("select * from  ".$wpdb->iceinfo." where ice_user_id=".$uid);
	if($userTypeInfo)
	{
		if($userTypeInfo->userType == '10' && !$erphp_life_days){
			return '终身';
		}else{
			return $userTypeInfo->endTime;
		}
	}
	return '';
}

function epd_vip_gift($userType, $uid){
	$erphp_life_gift    = get_option('erphp_life_gift');
	$erphp_year_gift    = get_option('erphp_year_gift');
	$erphp_quarter_gift = get_option('erphp_quarter_gift');
	$erphp_month_gift  = get_option('erphp_month_gift');
	$erphp_day_gift  = get_option('erphp_day_gift');
	if($userType == '6' && $erphp_day_gift){
		addUserMoney($uid, $erphp_day_gift, 'VIP奖励');
	}elseif($userType == '7' && $erphp_month_gift){
		addUserMoney($uid, $erphp_month_gift, 'VIP奖励');
	}elseif($userType == '8' && $erphp_quarter_gift){
		addUserMoney($uid, $erphp_quarter_gift, 'VIP奖励');
	}elseif($userType == '9' && $erphp_year_gift){
		addUserMoney($uid, $erphp_year_gift, 'VIP奖励');
	}elseif($userType == '10' && $erphp_life_gift){
		addUserMoney($uid, $erphp_life_gift, 'VIP奖励');
	}
}

function userPayCatSetData($userType,$cat){
	date_default_timezone_set('Asia/Shanghai');
	global $wpdb;
	$user_info = wp_get_current_user();
	$oldUserType = getUsreMemberCat($cat);

	if($oldUserType){
		$oldEndTime = getUsreMemberCatEndTime($cat);
		if($userType==7)
		{
			$endTime=date("Y-m-d",strtotime("+1 month",strtotime($oldEndTime)));
		}
		elseif ($userType==8)
		{
			$endTime=date("Y-m-d",strtotime("+3 month",strtotime($oldEndTime)));
		}
		elseif ($userType==9)
		{
			$endTime=date("Y-m-d",strtotime("+1 year",strtotime($oldEndTime)));
		}
		elseif ($userType==10)
		{
			$endTime=date("Y-m-d",strtotime("2038-01-01"));
		}
	}else{
		$endTime=date("Y-m-d");
		if($userType==7)
		{
			$endTime=date("Y-m-d",strtotime("+1 month"));
		}
		elseif ($userType==8)
		{
			$endTime=date("Y-m-d",strtotime("+3 month"));
		}
		elseif ($userType==9)
		{
			$endTime=date("Y-m-d",strtotime("+1 year"));
		}
		elseif ($userType==10)
		{
			$endTime=date("Y-m-d",strtotime("2038-01-01"));
		}
	}

	if($oldUserType){
		if($oldUserType > $userType){
			$userType = $oldUserType;
		}
	}

	$info = $wpdb->get_var("select ice_id from $wpdb->icecat where ice_user_id=".$user_info->ID." and ice_cat_id=".$cat);
	if($info){
		$sql="update ".$wpdb->icecat." set userType=".$userType.", endTime='".$endTime."' where ice_user_id=".$user_info->ID." and ice_cat_id=".$cat;
		$wpdb->query($sql);
	}else{
		$sql="insert into ".$wpdb->icecat." (ice_user_id,ice_cat_id,userType,endTime) values (".$user_info->ID.",$cat,$userType,'".$endTime."')";
		$wpdb->query($sql);
	}

	return true;

}

function userSetCatSetData($userType,$cat,$uid){
	date_default_timezone_set('Asia/Shanghai');
	global $wpdb;
	$oldUserType = getUsreMemberCatById($uid,$cat);

	if($oldUserType){
		$oldEndTime = getUsreMemberCatEndTimeById($uid,$cat);
		if($userType==7)
		{
			$endTime=date("Y-m-d",strtotime("+1 month",strtotime($oldEndTime)));
		}
		elseif ($userType==8)
		{
			$endTime=date("Y-m-d",strtotime("+3 month",strtotime($oldEndTime)));
		}
		elseif ($userType==9)
		{
			$endTime=date("Y-m-d",strtotime("+1 year",strtotime($oldEndTime)));
		}
		elseif ($userType==10)
		{
			$endTime=date("Y-m-d",strtotime("2038-01-01"));
		}
	}else{
		$endTime=date("Y-m-d");
		if($userType==7)
		{
			$endTime=date("Y-m-d",strtotime("+1 month"));
		}
		elseif ($userType==8)
		{
			$endTime=date("Y-m-d",strtotime("+3 month"));
		}
		elseif ($userType==9)
		{
			$endTime=date("Y-m-d",strtotime("+1 year"));
		}
		elseif ($userType==10)
		{
			$endTime=date("Y-m-d",strtotime("2038-01-01"));
		}
	}

	if($oldUserType){
		if($oldUserType > $userType){
			$userType = $oldUserType;
		}
	}

	$info = $wpdb->get_var("select ice_id from $wpdb->icecat where ice_user_id=".$uid." and ice_cat_id=".$cat);
	if($info){
		$sql="update ".$wpdb->icecat." set userType=".$userType.", endTime='".$endTime."' where ice_user_id=".$uid." and ice_cat_id=".$cat;
		$wpdb->query($sql);
	}else{
		$sql="insert into ".$wpdb->icecat." (ice_user_id,ice_cat_id,userType,endTime) values (".$uid.",$cat,$userType,'".$endTime."')";
		$wpdb->query($sql);
	}

	return true;

}


function userPayMemberSetData($userType){
	date_default_timezone_set('Asia/Shanghai');
	global $wpdb;
	$user_info = wp_get_current_user();
	$oldUserType = getUsreMemberType();
	$erphp_life_days    = get_option('erphp_life_days');
	$erphp_year_days    = get_option('erphp_year_days');
	$erphp_quarter_days = get_option('erphp_quarter_days');
	$erphp_month_days  = get_option('erphp_month_days');
	$erphp_day_days  = get_option('erphp_day_days');

	if($oldUserType){
		$vip_update_pay = get_option('vip_update_pay');
		$oldEndTime = getUsreMemberTypeEndTime();
		if($userType==6)
		{
			if($erphp_day_days){
				$endTime=date("Y-m-d",strtotime("+".$erphp_day_days." day",strtotime($oldEndTime)));
			}else{
				$endTime=date("Y-m-d",strtotime("+1 day",strtotime($oldEndTime)));
			}
		}
		elseif($userType==7)
		{
			if($vip_update_pay){
				if($oldUserType < 7){
					$oldEndTime = $wpdb->get_var("select ice_time from $wpdb->vip where ice_user_id=".$user_info->ID." and ice_user_type=".$oldUserType." order by ice_time DESC limit 1");
				}
			}

			if($erphp_month_days){
				$endTime=date("Y-m-d",strtotime("+".$erphp_month_days." day",strtotime($oldEndTime)));
			}else{
				$endTime=date("Y-m-d",strtotime("+1 month",strtotime($oldEndTime)));
			}
		}
		elseif ($userType==8)
		{
			if($vip_update_pay){
				if($oldUserType < 8){
					$oldEndTime = $wpdb->get_var("select ice_time from $wpdb->vip where ice_user_id=".$user_info->ID." and ice_user_type=".$oldUserType." order by ice_time DESC limit 1");
				}
			}

			if($erphp_quarter_days){
				$endTime=date("Y-m-d",strtotime("+".$erphp_quarter_days." month",strtotime($oldEndTime)));
			}else{
				$endTime=date("Y-m-d",strtotime("+3 month",strtotime($oldEndTime)));
			}
		}
		elseif ($userType==9)
		{
			if($vip_update_pay){
				if($oldUserType < 9){
					$oldEndTime = $wpdb->get_var("select ice_time from $wpdb->vip where ice_user_id=".$user_info->ID." and ice_user_type=".$oldUserType." order by ice_time DESC limit 1");
				}
			}

			if($erphp_year_days){
				$endTime=date("Y-m-d",strtotime("+".$erphp_year_days." month",strtotime($oldEndTime)));
			}else{
				$endTime=date("Y-m-d",strtotime("+1 year",strtotime($oldEndTime)));
			}
		}
		elseif ($userType==10)
		{
			if($vip_update_pay){
				if($oldUserType < 10){
					$oldEndTime = $wpdb->get_var("select ice_time from $wpdb->vip where ice_user_id=".$user_info->ID." and ice_user_type=".$oldUserType." order by ice_time DESC limit 1");
				}
			}

			if($erphp_life_days){
				$endTime=date("Y-m-d",strtotime("+".$erphp_life_days." year",strtotime($oldEndTime)));
			}else{
				$endTime=date("Y-m-d",strtotime("2038-01-01"));
			}
		}
	}else{
		$endTime=date("Y-m-d");
		if($userType==6)
		{
			if($erphp_day_days){
				$endTime=date("Y-m-d",strtotime("+".$erphp_day_days." day"));
			}else{
				$endTime=date("Y-m-d",strtotime("+0 day"));
			}
		}
		elseif($userType==7)
		{
			if($erphp_month_days){
				$endTime=date("Y-m-d",strtotime("+".$erphp_month_days." day"));
			}else{
				$endTime=date("Y-m-d",strtotime("+1 month"));
			}
		}
		elseif ($userType==8)
		{
			if($erphp_quarter_days){
				$endTime=date("Y-m-d",strtotime("+".$erphp_quarter_days." month"));
			}else{
				$endTime=date("Y-m-d",strtotime("+3 month"));
			}
		}
		elseif ($userType==9)
		{
			if($erphp_year_days){
				$endTime=date("Y-m-d",strtotime("+".$erphp_year_days." month"));
			}else{
				$endTime=date("Y-m-d",strtotime("+1 year"));
			}
		}
		elseif ($userType==10)
		{
			if($erphp_life_days){
				$endTime=date("Y-m-d",strtotime("+".$erphp_life_days." year"));
			}else{
				$endTime=date("Y-m-d",strtotime("2038-01-01"));
			}
		}
	}

	epd_vip_gift($userType, $user_info->ID);

	if($oldUserType){
		if($oldUserType > $userType){
			$userType = $oldUserType;
		}
	}

	$sql="update ".$wpdb->iceinfo." set userType=".$userType.", endTime='".$endTime."' where ice_user_id=".$user_info->ID;
	$wpdb->query($sql);

	return true;

}

function userSetMemberSetData($userType,$uid,$ip=''){
	date_default_timezone_set('Asia/Shanghai');
	global $wpdb;

	$erphp_life_days    = get_option('erphp_life_days');
	$erphp_year_days    = get_option('erphp_year_days');
	$erphp_quarter_days = get_option('erphp_quarter_days');
	$erphp_month_days  = get_option('erphp_month_days');
	$erphp_day_days  = get_option('erphp_day_days');

	if($uid){
		$oldUserType = getUsreMemberTypeById($uid);

		if($oldUserType){
			$vip_update_pay = get_option('vip_update_pay');
			$oldEndTime = getUsreMemberTypeEndTimeById($uid);

			if($userType==6)
			{
				if($erphp_day_days){
					$endTime=date("Y-m-d",strtotime("+".$erphp_day_days." day",strtotime($oldEndTime)));
				}else{
					$endTime=date("Y-m-d",strtotime("+1 day",strtotime($oldEndTime)));
				}
			}
			elseif($userType==7)
			{
				if($vip_update_pay){
					if($oldUserType < 7){
						$oldEndTime = $wpdb->get_var("select ice_time from $wpdb->vip where ice_user_id=".$uid." and ice_user_type=".$oldUserType." order by ice_time DESC limit 1");
					}
				}

				if($erphp_month_days){
					$endTime=date("Y-m-d",strtotime("+".$erphp_month_days." day",strtotime($oldEndTime)));
				}else{
					$endTime=date("Y-m-d",strtotime("+1 month",strtotime($oldEndTime)));
				}
			}
			elseif ($userType==8)
			{
				if($vip_update_pay){
					if($oldUserType < 8){
						$oldEndTime = $wpdb->get_var("select ice_time from $wpdb->vip where ice_user_id=".$uid." and ice_user_type=".$oldUserType." order by ice_time DESC limit 1");
					}
				}

				if($erphp_quarter_days){
					$endTime=date("Y-m-d",strtotime("+".$erphp_quarter_days." month",strtotime($oldEndTime)));
				}else{
					$endTime=date("Y-m-d",strtotime("+3 month",strtotime($oldEndTime)));
				}
			}
			elseif ($userType==9)
			{
				if($vip_update_pay){
					if($oldUserType < 9){
						$oldEndTime = $wpdb->get_var("select ice_time from $wpdb->vip where ice_user_id=".$uid." and ice_user_type=".$oldUserType." order by ice_time DESC limit 1");
					}
				}

				if($erphp_year_days){
					$endTime=date("Y-m-d",strtotime("+".$erphp_year_days." month",strtotime($oldEndTime)));
				}else{
					$endTime=date("Y-m-d",strtotime("+1 year",strtotime($oldEndTime)));
				}
			}
			elseif ($userType==10)
			{
				if($vip_update_pay){
					if($oldUserType < 10){
						$oldEndTime = $wpdb->get_var("select ice_time from $wpdb->vip where ice_user_id=".$uid." and ice_user_type=".$oldUserType." order by ice_time DESC limit 1");
					}
				}

				if($erphp_life_days){
					$endTime=date("Y-m-d",strtotime("+".$erphp_life_days." year",strtotime($oldEndTime)));
				}else{
					$endTime=date("Y-m-d",strtotime("2038-01-01"));
				}
			}
		}else{
			$endTime=date("Y-m-d");
			if($userType==6)
			{
				if($erphp_day_days){
					$endTime=date("Y-m-d",strtotime("+".$erphp_day_days." day"));
				}else{
					$endTime=date("Y-m-d",strtotime("+0 day"));
				}
			}
			elseif($userType==7)
			{
				if($erphp_month_days){
					$endTime=date("Y-m-d",strtotime("+".$erphp_month_days." day"));
				}else{
					$endTime=date("Y-m-d",strtotime("+1 month"));
				}
			}
			elseif ($userType==8)
			{
				if($erphp_quarter_days){
					$endTime=date("Y-m-d",strtotime("+".$erphp_quarter_days." month"));
				}else{
					$endTime=date("Y-m-d",strtotime("+3 month"));
				}
			}
			elseif ($userType==9)
			{
				if($erphp_year_days){
					$endTime=date("Y-m-d",strtotime("+".$erphp_year_days." month"));
				}else{
					$endTime=date("Y-m-d",strtotime("+1 year"));
				}
			}
			elseif ($userType==10)
			{
				if($erphp_life_days){
					$endTime=date("Y-m-d",strtotime("+".$erphp_life_days." year"));
				}else{
					$endTime=date("Y-m-d",strtotime("2038-01-01"));
				}
			}
		}

		epd_vip_gift($userType, $uid);

		if($oldUserType){
			if($oldUserType > $userType){
				$userType = $oldUserType;
			}
		}

		$sql="update ".$wpdb->iceinfo." set userType=".$userType.",endTime='".$endTime."' where ice_user_id=".$uid;
		$wpdb->query($sql);
	}elseif($ip){
		$endTime=date("Y-m-d");
		if($userType==6)
		{
			if($erphp_day_days){
				$endTime=date("Y-m-d",strtotime("+".$erphp_day_days." day"));
			}else{
				$endTime=date("Y-m-d",strtotime("+0 day"));
			}
		}
		elseif($userType==7)
		{
			if($erphp_month_days){
				$endTime=date("Y-m-d",strtotime("+".$erphp_month_days." day"));
			}else{
				$endTime=date("Y-m-d",strtotime("+1 month"));
			}
		}
		elseif ($userType==8)
		{
			if($erphp_quarter_days){
				$endTime=date("Y-m-d",strtotime("+".$erphp_quarter_days." month"));
			}else{
				$endTime=date("Y-m-d",strtotime("+3 month"));
			}
		}
		elseif ($userType==9)
		{
			if($erphp_year_days){
				$endTime=date("Y-m-d",strtotime("+".$erphp_year_days." month"));
			}else{
				$endTime=date("Y-m-d",strtotime("+1 year"));
			}
		}
		elseif ($userType==10)
		{
			if($erphp_life_days){
				$endTime=date("Y-m-d",strtotime("+".$erphp_life_days." year"));
			}else{
				$endTime=date("Y-m-d",strtotime("2038-01-01"));
			}
		}

		$sql="update ".$wpdb->iceinfo." set userType=".$userType.",endTime='".$endTime."' where ice_ip='".$ip."'";
		$wpdb->query($sql);
	}

	return true;
}


function epd_set_wppay_success($order_num,$total_fee,$pay_method=''){
	date_default_timezone_set('Asia/Shanghai');
	global $wpdb;
	if($order_num){
		//$order=$wpdb->get_row("select * from $wpdb->icemoney where ice_num='".esc_sql($order_num)."' and ice_alipay = '".$pay_method."'");
		$order=$wpdb->get_row("select * from $wpdb->icemoney where ice_num='".esc_sql($order_num)."'");
		if($order){
			if(!$order->ice_success){
				$total_fee = $order->ice_money;
				$ice_proportion_alipay = get_option('ice_proportion_alipay')?get_option('ice_proportion_alipay'):1;

				$wpdb->query("UPDATE $wpdb->icemoney SET ice_success=1, ice_money = '".$total_fee*$ice_proportion_alipay."',ice_success_time = '".date("Y-m-d H:i:s")."' WHERE ice_num = '".esc_sql($order_num)."'");

				if($order->ice_post_id){
					$ppost = get_post($order->ice_post_id);

					if($order->ice_post_index){
	            		$index = $order->ice_post_index;
	            		$urls = get_post_meta($order->ice_post_id, 'down_urls', true);
						if($urls){
							$cnt = count($urls['index']);
							if($cnt){
								for($i=0; $i<$cnt;$i++){
									if($urls['index'][$i] == $index){
				    					$index_name = $urls['name'][$i];
				    					break;
				    				}
								}
							}
						}
	            	}

	                $subject   = $ppost->post_title;
	                if($index_name){
						$subject .= ' - '.$index_name;
					}

					erphpAddDownloadByWppay($subject,$order->ice_post_id,$order->ice_user_id,$order_num,$total_fee*$ice_proportion_alipay,1,'',$ppost->post_author,$order->ice_aff,$order->ice_ip,$order->ice_post_index);

					if(function_exists('_mbt_add_activity')){
						_mbt_add_activity($order->ice_user_id,'buy','',$order->ice_post_id);
					}

					$erphp_down=get_post_meta($order->ice_post_id, 'erphp_down',TRUE);
		    		if($erphp_down == 6){
		    			$ice_data = explode('|', trim($order->ice_data));
		    			$email = $ice_data[0];
		    			$num = $ice_data[1];

		    			if(function_exists('doErphpActKa')){
		    				if($num > 1){
		    					$activation_num = '';
		    					for($i=0; $i<$num; $i++){
		    						$anum = doErphpActKa($order->ice_user_id, $order->ice_post_id, $order_num);
		    						if($anum){
			    						$activation_num .= $anum.'<br>';
			    					}
		    					}
		    				}else{
								$activation_num = doErphpActKa($order->ice_user_id, $order->ice_post_id, $order_num);
							}

							if($activation_num){
								$wpdb->query("update $wpdb->icealipay set ice_data = '".$activation_num."' where ice_num='".esc_sql($order_num)."'");
								if($email){
									$headers = 'Content-Type: text/html; charset=' . get_option('blog_charset') . "\n";
									wp_mail($email, '【'.$ppost->post_title.'】卡密', '您购买的【'.$ppost->post_title.'】卡密：<br>'.$activation_num.'<br>订单号：<br>'.$order_num.'<br>资源链接：<br>'.get_permalink($order->ice_post_id), $headers);
								}
							}
						}
		    		}

					$EPD = new EPD();
		        	$EPD->doAuthorAff($total_fee*$ice_proportion_alipay, $ppost->post_author);

		        	if($order->ice_user_id){
		        		$EPD->doAff($total_fee*$ice_proportion_alipay, $order->ice_user_id);
		        	}else{
		        		if($order->ice_aff){
		        			$EPD->doAff2($total_fee*$ice_proportion_alipay, $order->ice_aff);
		        		}
		        	}

					if(get_option('erphp_remind')){
						$headers = 'Content-Type: text/html; charset=' . get_option('blog_charset') . "\n";
						wp_mail(get_option('admin_email'), '【'.get_bloginfo('name').'】订单提醒 - '.$ppost->post_title, '游客消费'.$total_fee.'元购买了'.$ppost->post_title.get_permalink($post_id).'<br>订单号：<br>'.$order_num, $headers);
					}
				}elseif($order->ice_user_type){
					addUserIp($order->ice_ip);
					userSetMemberSetData($order->ice_user_type,0,$order->ice_ip);
					addVipLogByAdmin($total_fee*$ice_proportion_alipay, $order->ice_user_type, 0,$order->ice_ip);
							
					if($order->ice_aff){
						$EPD = new EPD();
						$EPD->doAff2($total_fee*$ice_proportion_alipay, $order->ice_aff);	
					}

					if(get_option('erphp_remind')){
						$headers = 'Content-Type: text/html; charset=' . get_option('blog_charset') . "\n";
						$typeName = getVipTypeName($order->ice_user_type);
						wp_mail(get_option('admin_email'), '【'.get_bloginfo('name').'】VIP订单提醒 - '.$typeName, '游客消费'.$total_fee.'元购买了'.$typeName.'<br>订单号：<br>'.$order_num, $headers);
					}
				}

			}
		}
	}
}

function epd_set_order_success($order_num,$total_fee,$pay_method=''){
	date_default_timezone_set('Asia/Shanghai');
	global $wpdb;
	if($order_num){
		$order=$wpdb->get_row("select * from ".$wpdb->icemoney." where ice_num='".esc_sql($order_num)."' and ice_alipay = '".$pay_method."'");
		if($order){
			if(!$order->ice_success){
				$ice_proportion_alipay = get_option('ice_proportion_alipay');
				$total_fee = $order->ice_money;

				$total_fee_old = $total_fee;

				if(!$order->ice_post_id && !$order->ice_user_type){
					$epd_game_price  = get_option('epd_game_price');
			        if($epd_game_price){
			          	$cnt = count($epd_game_price['buy']);
			          	for($i=0; $i<$cnt;$i++){
				            if($total_fee == $epd_game_price['buy'][$i]){
				              	$total_fee = $epd_game_price['get'][$i];
				              	break;
				            }
			          	}
			        }
			    }

			    if($wpdb->query("show columns from $wpdb->icemoney like 'ice_money_real'")){
					$updatOrder=$wpdb->query("update $wpdb->icemoney set ice_success=1, ice_money = '".$total_fee_old*$ice_proportion_alipay."', ice_money_real = '".$total_fee*$ice_proportion_alipay."', ice_success_time = '".date("Y-m-d H:i:s")."' where ice_num='".esc_sql($order_num)."'");
				}else{
					$updatOrder=$wpdb->query("update $wpdb->icemoney set ice_success=1, ice_money = '".$total_fee_old*$ice_proportion_alipay."',  ice_success_time = '".date("Y-m-d H:i:s")."' where ice_num='".esc_sql($order_num)."'");
				}

				
				if($updatOrder){
					addUserMoney($order->ice_user_id,$total_fee*$ice_proportion_alipay,'在线充值');
				}

				if($order->ice_post_id){
					//$okMoney=erphpGetUserOkMoneyById($order->ice_user_id);
	                $postid = $order->ice_post_id;
	                $index = '';$index_name = '';
	                $price = $total_fee*$ice_proportion_alipay;

                    if(erphpSetUserMoneyXiaoFeiByUid($price,$order->ice_user_id)){
                    	addUserMoneyLog($order->ice_user_id, '-'.$price, '购买资源');
                    	if(function_exists('_mbt_add_activity')){
							_mbt_add_activity($order->ice_user_id,'buy','',$order->ice_post_id);
						}
                    	if($order->ice_post_index){
                    		$index = $order->ice_post_index;
                    		$urls = get_post_meta($postid, 'down_urls', true);
							if($urls){
								$cnt = count($urls['index']);
								if($cnt){
									for($i=0; $i<$cnt;$i++){
										if($urls['index'][$i] == $index){
					    					$index_name = $urls['name'][$i];
					    					break;
					    				}
									}
								}
							}
                    	}

                        $subject   = get_post($postid)->post_title;
                        if($index_name){
							$subject .= ' - '.$index_name;
						}
                        $postUserId=get_post($postid)->post_author;
                        
                        $result=erphpAddDownloadByUid($subject, $postid, $order->ice_user_id,$price,1, '', $postUserId, $index, $order->ice_ip);
                        if($result)
                        {

                        	$erphp_down=get_post_meta($order->ice_post_id, 'erphp_down',TRUE);
				    		if($erphp_down == 6){
				    			$ice_data = explode('|', trim($order->ice_data));
				    			$email = $ice_data[0];
				    			$num = $ice_data[1];

				    			if(function_exists('doErphpActKa')){
				    				if($num > 1){
				    					$activation_num = '';
				    					for($i=0; $i<$num; $i++){
				    						$anum = doErphpActKa($order->ice_user_id, $order->ice_post_id, $order_num);
				    						if($anum){
					    						$activation_num .= $anum.'<br>';
					    					}
				    					}
				    				}else{
										$activation_num = doErphpActKa($order->ice_user_id, $order->ice_post_id, $order_num);
									}

									if($activation_num){
										$wpdb->query("update $wpdb->icealipay set ice_data = '".$activation_num."' where ice_num='".$order_num."'");
										if($email){
											$headers = 'Content-Type: text/html; charset=' . get_option('blog_charset') . "\n";
											wp_mail($email, '【'.$subject.'】卡密', '您购买的【'.$subject.'】卡密：<br>'.$activation_num.'<br>订单号：<br>'.$order_num.'<br>资源链接：<br>'.get_permalink($order->ice_post_id), $headers);
										}
									}
								}
				    		}else{
	                        	$down_activation = get_post_meta($postid, 'down_activation', true);
	                        	if($down_activation && function_exists('doErphpAct')){
									$activation_num = doErphpAct($order->ice_user_id,$postid);
									if($activation_num){
										$wpdb->query("update $wpdb->icealipay set ice_data = '".$activation_num."' where ice_url='".$result."'");
										$cuser = get_user_by('id',$order->ice_user_id);
										if($cuser && $cuser->user_email){
											$headers = 'Content-Type: text/html; charset=' . get_option('blog_charset') . "\n";
											wp_mail($cuser->user_email, '【'.$subject.'】激活码', '您购买的资源【'.$subject.'】激活码：'.$activation_num.'<br>订单号：<br>'.$order_num.'<br>资源链接：<br>'.get_permalink($order->ice_post_id), $headers);
										}
									}
								}
							}
                            
                            $EPD = new EPD();
                            $EPD->doAuthorAff($price, $postUserId);
							$EPD->doAff($price, $order->ice_user_id);

							if(get_option('erphp_remind')){
								$headers = 'Content-Type: text/html; charset=' . get_option('blog_charset') . "\n";
								wp_mail(get_option('admin_email'), '【'.get_bloginfo('name').'】订单提醒 - '.$subject, '用户'.get_user_by('id',$order->ice_user_id)->user_login.'消费'.$price.get_option('ice_name_alipay').'购买了'.$subject.get_permalink($postid).'<br>订单号：<br>'.$order_num, $headers);
							}

							do_action( 'erphpdown_post_checkout', $order->ice_user_id, $postid, $index, $price, $result );
                        } 
                    }
				}elseif($order->ice_user_type){
					addUserMoney($order->ice_user_id, '-'.$total_fee*$ice_proportion_alipay, '升级VIP');
					userSetMemberSetData($order->ice_user_type,$order->ice_user_id);
					addVipLogByAdmin($total_fee*$ice_proportion_alipay, $order->ice_user_type, $order->ice_user_id);

					if(function_exists('_mbt_add_notice')){
						_mbt_add_notice($order->ice_user_id, '您好，您已成功升级成为VIP。', 'vip', $order->ice_user_type);
					}

					if(function_exists('_mbt_add_activity')){
						_mbt_add_activity($order->ice_user_id,'vip','',$order->ice_user_type);
					}
							
					$EPD = new EPD();
					$EPD->doAff($total_fee*$ice_proportion_alipay, $order->ice_user_id);	

					if(get_option('erphp_remind')){
						$headers = 'Content-Type: text/html; charset=' . get_option('blog_charset') . "\n";
						$typeName = getVipTypeName($order->ice_user_type);
						wp_mail(get_option('admin_email'), '【'.get_bloginfo('name').'】VIP订单提醒 - '.$typeName, '用户'.get_user_by('id',$order->ice_user_id)->user_login.'消费'.$total_fee.'元购买了'.$typeName.'<br>订单号：<br>'.$order_num, $headers);
					}
				}else{
					if(get_option('erphp_remind_recharge')){
						$headers = 'Content-Type: text/html; charset=' . get_option('blog_charset') . "\n";
						wp_mail(get_option('admin_email'), '【'.get_bloginfo('name').'】充值提醒 - '.$total_fee_old.'元', '用户'.get_user_by('id',$order->ice_user_id)->user_login.'刚刚充值了'.$total_fee_old.'元。'.'<br>订单号：<br>'.$order_num, $headers);
					}
				}
			}
		}
	}
}

function erphpCheckAlipayReturnNum($orderNum,$money)
{
	global $wpdb;
	$row=$wpdb->get_row("select * from ".$wpdb->icemoney." where ice_num='".$orderNum."'");
	if($row)
	{
		if($row->ice_money == $money)
		{
			return true;
		}
	}
	return false;
}
function erphpAddDownloadByUid($subject,$postid,$userid,$price,$success,$data,$postUserId,$index='',$ip='')
{
	date_default_timezone_set('Asia/Shanghai');
	
	global $wpdb;
	$subject = str_replace("'","",$subject);
	$subject = str_replace("‘","",$subject);
	$url       = md5(date("YmdHis").$postid.mt_rand(1000000, 9999999));
	$orderNum  = date("YmdHis").mt_rand(10000, 99999);
	$sql       = "INSERT INTO $wpdb->icealipay (ice_num,ice_title,ice_post,ice_price,ice_success,ice_url,ice_user_id,ice_time,ice_data,ice_index,
	ice_author,ice_ip)VALUES ('$orderNum','$subject','$postid','$price','$success','$url','".$userid."','".date("Y-m-d H:i:s")."','".$data."','".$index."','$postUserId','".$ip."')";
	if($wpdb->query($sql))
	{
		return $url;
	}
	
	return false;
}
function erphpAddDownloadByWppay($subject,$postid,$userid,$num,$price,$success,$data,$postUserId,$aff,$ip,$index='')
{
	date_default_timezone_set('Asia/Shanghai');
	
	global $wpdb;
	$subject = str_replace("'","",$subject);
	$subject = str_replace("‘","",$subject);
	$url       = md5(date("YmdHis").$postid.mt_rand(1000000, 9999999));
	$sql       = "INSERT INTO $wpdb->icealipay (ice_num,ice_title,ice_post,ice_price,ice_success,ice_url,ice_user_id,ice_time,ice_data,ice_index,
	ice_author,ice_aff,ice_ip)VALUES ('$num','$subject','$postid','$price','$success','$url','".$userid."','".date("Y-m-d H:i:s")."','".$data."','".$index."','$postUserId','".$aff."','".$ip."')";
	if($wpdb->query($sql))
	{
		return $url;
	}
	
	return false;
}
function erphpAddDownloadBuyNum($subject,$postid,$num,$price,$success,$data,$postUserId,$index='')
{
	date_default_timezone_set('Asia/Shanghai');
	
	global $wpdb;
	$subject = str_replace("'","",$subject);
	$subject = str_replace("‘","",$subject);
	$user_info = wp_get_current_user();
	$url       = md5(date("YmdHis").$postid.mt_rand(1000000, 9999999));
	$sql       = "INSERT INTO $wpdb->icealipay (ice_num,ice_title,ice_post,ice_price,ice_success,ice_url,ice_user_id,ice_time,ice_data,ice_index,
	ice_author)VALUES ('$num','$subject','$postid','$price','$success','$url','".$user_info->ID."','".date("Y-m-d H:i:s")."','".$data."','".$index."','$postUserId')";
	if($wpdb->query($sql))
	{
		return $url;
	}
	
	return false;
}
function erphpAddDownload($subject,$postid,$price,$success,$data,$postUserId,$index='',$ip='')
{
	date_default_timezone_set('Asia/Shanghai');
	
	global $wpdb;
	$subject = str_replace("'","",$subject);
	$subject = str_replace("‘","",$subject);
	$user_info = wp_get_current_user();
	$url       = md5(date("YmdHis").$postid.mt_rand(1000000, 9999999));
	$orderNum  = date("YmdHis").mt_rand(10000, 99999);
	$sql       = "INSERT INTO $wpdb->icealipay (ice_num,ice_title,ice_post,ice_price,ice_success,ice_url,ice_user_id,ice_time,ice_data,ice_index,
	ice_author,ice_ip)VALUES ('$orderNum','$subject','$postid','$price','$success','$url','".$user_info->ID."','".date("Y-m-d H:i:s")."','".$data."','".$index."','$postUserId','".$ip."')";
	if($wpdb->query($sql))
	{
		return $url;
	}
	
	return false;
}
function erphpAddDownloadIndex($postid,$price,$index)
{
	date_default_timezone_set('Asia/Shanghai');
	
	global $wpdb;
	$user_info = wp_get_current_user();
	$url       = md5(date("YmdHis").$postid.mt_rand(1000000, 9999999));
	$orderNum  = date("YmdHis").mt_rand(10000, 99999);
	$sql       = "INSERT INTO $wpdb->iceindex (ice_num,ice_post,ice_price,ice_url,ice_user_id,ice_time,ice_index)VALUES ('$orderNum','$postid','$price','$url','".$user_info->ID."','".date("Y-m-d H:i:s")."','".$index."')";
	if($wpdb->query($sql))
	{
		return $url;
	}
	
	return false;
}
function erphpSetUserMoneyXiaoFei($num)
{
	if($num > 0){
		global $wpdb;
		$user_info=wp_get_current_user();
		return $wpdb->query("update $wpdb->iceinfo set ice_get_money=ice_get_money+".$num." where ice_user_id=".$user_info->ID);
	}else{
		return false;
	}
}
function erphpSetUserMoneyXiaoFeiByUid($num,$uid)
{
	if($num > 0){
		global $wpdb;
		return $wpdb->query("update $wpdb->iceinfo set ice_get_money=ice_get_money+".$num." where ice_user_id=".$uid);
	}else{
		return false;
	}
}
function erphpGetUserAllXiaofei($uid){
	global $wpdb;
	$money = $wpdb->get_var("SELECT SUM(ice_price) FROM $wpdb->icealipay WHERE ice_success>0 and ice_user_id=".$uid);
	$money2 = $wpdb->get_var("SELECT sum(ice_price) FROM $wpdb->vip where ice_user_id=".$uid);
	$money += $money2;
	return $money ? $money :'0';
}
function erphpGetUserOkAff()
{
	global $wpdb;
	if(is_user_logged_in()){
		$user_info=wp_get_current_user();
		if($user_info)
		{
			$userMoney=$wpdb->get_row("select * from ".$wpdb->iceinfo." where ice_user_id=".$user_info->ID);
			return $userMoney ?sprintf("%.2f",($userMoney->ice_have_aff - $userMoney->ice_get_aff)) : 0;
		}
	}
	return 0;
}
function erphpGetUserOkAffById($uid)
{
	global $wpdb;
	if($uid)
	{
		$userMoney=$wpdb->get_row("select * from ".$wpdb->iceinfo." where ice_user_id=".$uid);
		return $userMoney ?sprintf("%.2f",($userMoney->ice_have_aff - $userMoney->ice_get_aff)) : 0;
	}
	return 0;
}
function erphpGetUserOkMoney()
{
	global $wpdb;
	if(is_user_logged_in()){
		$user_info=wp_get_current_user();
		if($user_info)
		{
			$userMoney=$wpdb->get_row("select * from ".$wpdb->iceinfo." where ice_user_id=".$user_info->ID);
			return $userMoney ? sprintf("%.2f",($userMoney->ice_have_money - $userMoney->ice_get_money)) : 0;
		}
	}
	return 0;
}
function erphpGetUserOkMoneyById($uid)
{
	global $wpdb;
	if($uid){
		$userMoney=$wpdb->get_row("select * from ".$wpdb->iceinfo." where ice_user_id=".$uid);
		return $userMoney ? sprintf("%.2f",($userMoney->ice_have_money - $userMoney->ice_get_money)) : 0;
	}
	return 0;
}
function getProductSales($pid,$time=''){
	global $wpdb;
	if($time){
		$where = '';
		if($time == '365'){
			$where = ' and ice_time>DATE_SUB(CURDATE(), INTERVAL 1 YEAR)';
		}elseif($time == '182'){
			$where = ' and ice_time>DATE_SUB(CURDATE(), INTERVAL 6 MONTH)';
		}elseif($time == '30'){
			$where = ' and ice_time>DATE_SUB(CURDATE(), INTERVAL 1 MONTH)';
		}elseif($time == '7'){
			$where = ' and ice_time>DATE_SUB(CURDATE(), INTERVAL 7 DAY)';
		}elseif($time == '1'){
			$where = ' and to_days(ice_time) = to_days(now())';
		}
		$total_trade  = $wpdb->get_var("SELECT COUNT(ice_id) FROM $wpdb->icealipay WHERE ice_success>0 and ice_post=".$pid.$where);
	}else{
		$total_trade  = $wpdb->get_var("SELECT COUNT(ice_id) FROM $wpdb->icealipay WHERE ice_success>0 and ice_post=".$pid);
	}
	return $total_trade;
}

function getProductMember($pid){
	$type = get_post_meta($pid,"member_down",true);
	if($type == "1"){
		return "无";
	}elseif($type == "2"){
		return "VIP 5折";
	}elseif($type == "3"){
		return "VIP免费";
	}elseif($type == "4"){
		return "VIP专享";
	}elseif($type == "5"){
		return "VIP 8折";
	}elseif($type == "6"){
		return "年费VIP免费";
	}elseif($type == "7"){
		return "终身VIP免费";
	}elseif($type == "8"){
		return "年费VIP专享";
	}elseif($type == "9"){
		return "终身VIP专享";
	}elseif($type == "10"){
		return "VIP专购";
	}elseif($type == "17"){
		return "包季VIP专购";
	}elseif($type == "18"){
		return "包年VIP专购";
	}elseif($type == "19"){
		return "终身VIP专购";
	}elseif($type == "11"){
		return "VIP专购|年费5折";
	}elseif($type == "12"){
		return "VIP专购|年费8折";
	}elseif($type == "13"){
		return "VIP 5折|终身免费";
	}elseif($type == "14"){
		return "VIP 8折|终身免费";
	}elseif($type == "15"){
		return "包季VIP专享";
	}elseif($type == "16"){
		return "包季VIP免费";
	}elseif($type == "20"){
		return "自定义折扣|终身免费";
	}else{
		return "未知";
	}
}

function getProductDownType($pid){
	$start_down = get_post_meta($pid,"start_down",true);
	$start_down2 = get_post_meta($pid,"start_down2",true);
	$start_see = get_post_meta($pid,"start_see",true);
	$start_see2 = get_post_meta($pid,"start_see2",true);
	$erphp_down = get_post_meta($pid,"erphp_down",true);
	if($start_down == "yes"){
		return "下载";
	}elseif($start_down2 == "yes"){
		return "免登录";
	}elseif($start_see == "yes"){
		return "查看";
	}elseif($start_see2 == "yes"){
		return "部分查看";
	}elseif($erphp_down == "6"){
		return "发卡";
	}else{
		return "无";
	}
}

function getVipTypeName($userTypeId){
	$erphp_life_name    = get_option('erphp_life_name')?get_option('erphp_life_name'):'终身VIP';
	$erphp_year_name    = get_option('erphp_year_name')?get_option('erphp_year_name'):'包年VIP';
	$erphp_quarter_name = get_option('erphp_quarter_name')?get_option('erphp_quarter_name'):'包季VIP';
	$erphp_month_name  = get_option('erphp_month_name')?get_option('erphp_month_name'):'包月VIP';
	$erphp_day_name  = get_option('erphp_day_name')?get_option('erphp_day_name'):'体验VIP';
	$erphp_vip_name  = get_option('erphp_vip_name')?get_option('erphp_vip_name'):'VIP';

    if($userTypeId==6){
        return $erphp_day_name;
    }elseif($userTypeId==7){
        return $erphp_month_name;
    }elseif ($userTypeId==8){
        return $erphp_quarter_name;
    }elseif ($userTypeId==9){
        return $erphp_year_name;
    }elseif ($userTypeId==10){
        return $erphp_life_name;
    }else {
        return '普通用户';
    }
}

add_action('wp_dashboard_setup', 'erphp_modify_dashboard_widgets' );
function erphp_modify_dashboard_widgets() {
	if(current_user_can('administrator')){
		add_meta_box( 'erphpdown_total_widget', 'Erphpdown', 'erphpdown_dashboard_widget_function','dashboard', 'normal', 'core' );
	
	}
}

function erphpdown_dashboard_widget_function() {
	global $wpdb, $wppay_table_name;
	if(current_user_can('administrator')){
		$ice_proportion_alipay = get_option('ice_proportion_alipay');
		$ice_proportion_alipay = $ice_proportion_alipay?$ice_proportion_alipay:1;

		$ice_note = "ice_note=0";
		if(get_option("erphp_addon_card_total")){
			$ice_note = "(ice_note=0 or ice_note=6)";
		}

		$today_chong_money = $wpdb->get_row("SELECT SUM(ice_money) as cm, count(ice_id) as ct FROM $wpdb->icemoney WHERE ice_success>0 and ".$ice_note." and TO_DAYS(NOW())- TO_DAYS(ice_time) = 0");
		$today_order_money = $wpdb->get_row("SELECT SUM(ice_price) as cm, count(ice_id) as ct FROM $wpdb->icealipay WHERE ice_success>0 and TO_DAYS(NOW())- TO_DAYS(ice_time) = 0");
		$today_vip_money = $wpdb->get_row("SELECT SUM(ice_price) as cm, count(ice_id) as ct FROM $wpdb->vip WHERE TO_DAYS(NOW())- TO_DAYS(ice_time) = 0");

		$yestoday_chong_money = $wpdb->get_row("SELECT SUM(ice_money) as cm, count(ice_id) as ct FROM $wpdb->icemoney WHERE ice_success>0 and ".$ice_note." and TO_DAYS(NOW())- TO_DAYS(ice_time) = 1");
		$yestoday_order_money = $wpdb->get_row("SELECT SUM(ice_price) as cm, count(ice_id) as ct FROM $wpdb->icealipay WHERE ice_success>0 and TO_DAYS(NOW())- TO_DAYS(ice_time) = 1");
		$yestoday_vip_money = $wpdb->get_row("SELECT SUM(ice_price) as cm, count(ice_id) as ct FROM $wpdb->vip WHERE TO_DAYS(NOW())- TO_DAYS(ice_time) = 1");

		echo '<div class="activity-block"><ul style="margin:0 -10px;overflow:hidden">';
		echo '<li style="margin:0 10px 10px;float:left;width:calc(50% - 20px);border:1px solid #f0f0f1;padding:10px;box-sizing:border-box">
				<div style="font-size:19px;margin-bottom:20px;">今日充值</div>
				<div><a style="font-size:16px" href="'.admin_url('admin.php?page=erphpdown/admin/erphp-chong-list.php').'">'.($today_chong_money->cm?$today_chong_money->cm:'0')/$ice_proportion_alipay.' 元</a><span style="float:right;position:relative;top:5px;">'.($today_chong_money->ct?$today_chong_money->ct:'0').' 笔</span></div>
			  </li>';
		echo '<li style="margin:0 10px 10px;float:left;width:calc(50% - 20px);border:1px solid #f0f0f1;padding:10px;box-sizing:border-box">
				<div style="font-size:19px;margin-bottom:20px;">昨日充值</div>
				<div><a style="font-size:16px" href="'.admin_url('admin.php?page=erphpdown/admin/erphp-chong-list.php').'">'.($yestoday_chong_money->cm?$yestoday_chong_money->cm:'0')/$ice_proportion_alipay.' 元</a><span style="float:right;position:relative;top:5px;">'.($yestoday_chong_money->ct?$yestoday_chong_money->ct:'0').' 笔</span></div>
			  </li>';
		echo '<li style="margin:0 10px 10px;float:left;width:calc(50% - 20px);border:1px solid #f0f0f1;padding:10px;box-sizing:border-box"><span>文章</span>：<a href="'.admin_url('admin.php?page=erphpdown/admin/erphp-orders-list.php').'">'.($today_order_money->cm?$today_order_money->cm:'0').' '.get_option('ice_name_alipay').'</a><span style="float:right;">'.($today_order_money->ct?$today_order_money->ct:'0').' 单</span></li>';
		echo '<li style="margin:0 10px 10px;float:left;width:calc(50% - 20px);border:1px solid #f0f0f1;padding:10px;box-sizing:border-box"><span>文章</span>：<a href="'.admin_url('admin.php?page=erphpdown/admin/erphp-orders-list.php').'">'.($yestoday_order_money->cm?$yestoday_order_money->cm:'0').' '.get_option('ice_name_alipay').'</a><span style="float:right;">'.($yestoday_order_money->ct?$yestoday_order_money->ct:'0').' 单</span></li>';
		echo '<li style="margin:0 10px 10px;float:left;width:calc(50% - 20px);border:1px solid #f0f0f1;padding:10px;box-sizing:border-box"><span>VIP</span>：<a href="'.admin_url('admin.php?page=erphpdown/admin/erphp-vip-items.php').'">'.($today_vip_money->cm?$today_vip_money->cm:'0').' '.get_option('ice_name_alipay').'</a><span style="float:right;">'.($today_vip_money->ct?$today_vip_money->ct:'0').' 个</span></li>';
		echo '<li style="margin:0 10px 10px;float:left;width:calc(50% - 20px);border:1px solid #f0f0f1;padding:10px;box-sizing:border-box"><span>VIP</span>：<a href="'.admin_url('admin.php?page=erphpdown/admin/erphp-vip-items.php').'">'.($yestoday_vip_money->cm?$yestoday_vip_money->cm:'0').' '.get_option('ice_name_alipay').'</a><span style="float:right;">'.($yestoday_vip_money->ct?$yestoday_vip_money->ct:'0').' 个</span></li>';
		echo '</ul></div>';

		$month_total = $wpdb->get_row("select
		    sum(case month(ice_time) when '1'  then ice_money else 0 end) as Jan,
		    sum(case month(ice_time) when '2'  then ice_money else 0 end) as Feb,
		    sum(case month(ice_time) when '3'  then ice_money else 0 end) as Mar,
		    sum(case month(ice_time) when '4'  then ice_money else 0 end) as Apr,
		    sum(case month(ice_time) when '5'  then ice_money else 0 end) as May,
		    sum(case month(ice_time) when '6'  then ice_money else 0 end) as June,
		    sum(case month(ice_time) when '7'  then ice_money else 0 end) as July,
		    sum(case month(ice_time) when '8'  then ice_money else 0 end) as Aug,
		    sum(case month(ice_time) when '9'  then ice_money else 0 end) as Sept,
		    sum(case month(ice_time) when '10' then ice_money else 0 end) as Oct,
		    sum(case month(ice_time) when '11' then ice_money else 0 end) as Nov,
		    sum(case month(ice_time) when '12' then ice_money else 0 end) as Dece
		from $wpdb->icemoney where year(ice_time)='".date("Y")."' and ice_success>0 and ".$ice_note.";");

		$month_total2 = $wpdb->get_row("select
		    sum(case month(ice_time) when '1'  then 1 else 0 end) as Jan,
		    sum(case month(ice_time) when '2'  then 1 else 0 end) as Feb,
		    sum(case month(ice_time) when '3'  then 1 else 0 end) as Mar,
		    sum(case month(ice_time) when '4'  then 1 else 0 end) as Apr,
		    sum(case month(ice_time) when '5'  then 1 else 0 end) as May,
		    sum(case month(ice_time) when '6'  then 1 else 0 end) as June,
		    sum(case month(ice_time) when '7'  then 1 else 0 end) as July,
		    sum(case month(ice_time) when '8'  then 1 else 0 end) as Aug,
		    sum(case month(ice_time) when '9'  then 1 else 0 end) as Sept,
		    sum(case month(ice_time) when '10' then 1 else 0 end) as Oct,
		    sum(case month(ice_time) when '11' then 1 else 0 end) as Nov,
		    sum(case month(ice_time) when '12' then 1 else 0 end) as Dece
		from $wpdb->icemoney where year(ice_time)='".date("Y")."' and ice_success>0 and ".$ice_note.";");

		$day_total = $wpdb->get_row("select
		    sum(case day(ice_time) when '1'  then ice_money else 0 end) as one,
		    sum(case day(ice_time) when '2'  then ice_money else 0 end) as two,
		    sum(case day(ice_time) when '3'  then ice_money else 0 end) as three,
		    sum(case day(ice_time) when '4'  then ice_money else 0 end) as four,
		    sum(case day(ice_time) when '5'  then ice_money else 0 end) as five,
		    sum(case day(ice_time) when '6'  then ice_money else 0 end) as six,
		    sum(case day(ice_time) when '7'  then ice_money else 0 end) as seven,
		    sum(case day(ice_time) when '8'  then ice_money else 0 end) as eight,
		    sum(case day(ice_time) when '9'  then ice_money else 0 end) as nine,
		    sum(case day(ice_time) when '10' then ice_money else 0 end) as ten,
		    sum(case day(ice_time) when '11' then ice_money else 0 end) as eleven,
		    sum(case day(ice_time) when '12' then ice_money else 0 end) as twelve,
		    sum(case day(ice_time) when '13' then ice_money else 0 end) as thirteen,
		    sum(case day(ice_time) when '14' then ice_money else 0 end) as fourteen,
		    sum(case day(ice_time) when '15' then ice_money else 0 end) as fifteen,
		    sum(case day(ice_time) when '16' then ice_money else 0 end) as sixteen,
		    sum(case day(ice_time) when '17' then ice_money else 0 end) as seventeen,
		    sum(case day(ice_time) when '18' then ice_money else 0 end) as eighteen,
		    sum(case day(ice_time) when '19' then ice_money else 0 end) as nineteen,
		    sum(case day(ice_time) when '20' then ice_money else 0 end) as twenty,
		    sum(case day(ice_time) when '21' then ice_money else 0 end) as twentyone,
		    sum(case day(ice_time) when '22' then ice_money else 0 end) as twentytwo,
		    sum(case day(ice_time) when '23' then ice_money else 0 end) as twentythree,
		    sum(case day(ice_time) when '24' then ice_money else 0 end) as twentyfour,
		    sum(case day(ice_time) when '25' then ice_money else 0 end) as twentyfive,
		    sum(case day(ice_time) when '26' then ice_money else 0 end) as twentysix,
		    sum(case day(ice_time) when '27' then ice_money else 0 end) as twentyseven,
		    sum(case day(ice_time) when '28' then ice_money else 0 end) as twentyeight,
		    sum(case day(ice_time) when '29' then ice_money else 0 end) as twentynine,
		    sum(case day(ice_time) when '30' then ice_money else 0 end) as thirty,
		    sum(case day(ice_time) when '31' then ice_money else 0 end) as thirtyone
		from $wpdb->icemoney where year(ice_time)='".date("Y")."' and month(ice_time)='".ltrim(date("m"),'0')."' and ice_success>0 and ".$ice_note.";");

		$day_total2 = $wpdb->get_row("select
		    sum(case day(ice_time) when '1'  then 1 else 0 end) as one,
		    sum(case day(ice_time) when '2'  then 1 else 0 end) as two,
		    sum(case day(ice_time) when '3'  then 1 else 0 end) as three,
		    sum(case day(ice_time) when '4'  then 1 else 0 end) as four,
		    sum(case day(ice_time) when '5'  then 1 else 0 end) as five,
		    sum(case day(ice_time) when '6'  then 1 else 0 end) as six,
		    sum(case day(ice_time) when '7'  then 1 else 0 end) as seven,
		    sum(case day(ice_time) when '8'  then 1 else 0 end) as eight,
		    sum(case day(ice_time) when '9'  then 1 else 0 end) as nine,
		    sum(case day(ice_time) when '10' then 1 else 0 end) as ten,
		    sum(case day(ice_time) when '11' then 1 else 0 end) as eleven,
		    sum(case day(ice_time) when '12' then 1 else 0 end) as twelve,
		    sum(case day(ice_time) when '13' then 1 else 0 end) as thirteen,
		    sum(case day(ice_time) when '14' then 1 else 0 end) as fourteen,
		    sum(case day(ice_time) when '15' then 1 else 0 end) as fifteen,
		    sum(case day(ice_time) when '16' then 1 else 0 end) as sixteen,
		    sum(case day(ice_time) when '17' then 1 else 0 end) as seventeen,
		    sum(case day(ice_time) when '18' then 1 else 0 end) as eighteen,
		    sum(case day(ice_time) when '19' then 1 else 0 end) as nineteen,
		    sum(case day(ice_time) when '20' then 1 else 0 end) as twenty,
		    sum(case day(ice_time) when '21' then 1 else 0 end) as twentyone,
		    sum(case day(ice_time) when '22' then 1 else 0 end) as twentytwo,
		    sum(case day(ice_time) when '23' then 1 else 0 end) as twentythree,
		    sum(case day(ice_time) when '24' then 1 else 0 end) as twentyfour,
		    sum(case day(ice_time) when '25' then 1 else 0 end) as twentyfive,
		    sum(case day(ice_time) when '26' then 1 else 0 end) as twentysix,
		    sum(case day(ice_time) when '27' then 1 else 0 end) as twentyseven,
		    sum(case day(ice_time) when '28' then 1 else 0 end) as twentyeight,
		    sum(case day(ice_time) when '29' then 1 else 0 end) as twentynine,
		    sum(case day(ice_time) when '30' then 1 else 0 end) as thirty,
		    sum(case day(ice_time) when '31' then 1 else 0 end) as thirtyone
		from $wpdb->icemoney where year(ice_time)='".date("Y")."' and month(ice_time)='".ltrim(date("m"),'0')."' and ice_success>0 and ".$ice_note.";");
?>
		<script src="<?php echo ERPHPDOWN_URL;?>/static/chart.js"></script>
		<canvas id="erphpdown_month_total" class="activity-block"></canvas>
		<canvas id="erphpdown_year_total" class="activity-block"></canvas>
		<script>
			var config = {
				type: 'bar',
				data: {
					labels: ['1月', '2月', '3月', '4月', '5月', '6月', '7月', '8月', '9月', '10月', '11月', '12月'],
					datasets: [{
						label: '充值额(元)',
						backgroundColor: window.chartColors.red,
						borderColor: window.chartColors.red,
						data: [
							<?php echo $month_total->Jan / $ice_proportion_alipay;?>,
							<?php echo $month_total->Feb / $ice_proportion_alipay;?>,
							<?php echo $month_total->Mar / $ice_proportion_alipay;?>,
							<?php echo $month_total->Apr / $ice_proportion_alipay;?>,
							<?php echo $month_total->May / $ice_proportion_alipay;?>,
							<?php echo $month_total->June / $ice_proportion_alipay;?>,
							<?php echo $month_total->July / $ice_proportion_alipay;?>,
							<?php echo $month_total->Aug / $ice_proportion_alipay;?>,
							<?php echo $month_total->Sept / $ice_proportion_alipay;?>,
							<?php echo $month_total->Oct / $ice_proportion_alipay;?>,
							<?php echo $month_total->Nov / $ice_proportion_alipay;?>,
							<?php echo $month_total->Dece / $ice_proportion_alipay;?>
						],
						fill: false,
					}, {
						label: '订单数(笔)',
						fill: false,
						backgroundColor: window.chartColors.blue,
						borderColor: window.chartColors.blue,
						data: [
							<?php echo $month_total2->Jan?$month_total2->Jan:0;?>,
							<?php echo $month_total2->Feb?$month_total2->Feb:0;?>,
							<?php echo $month_total2->Mar?$month_total2->Mar:0;?>,
							<?php echo $month_total2->Apr?$month_total2->Apr:0;?>,
							<?php echo $month_total2->May?$month_total2->May:0;?>,
							<?php echo $month_total2->June?$month_total2->June:0;?>,
							<?php echo $month_total2->July?$month_total2->July:0;?>,
							<?php echo $month_total2->Aug?$month_total2->Aug:0;?>,
							<?php echo $month_total2->Sept?$month_total2->Sept:0;?>,
							<?php echo $month_total2->Oct?$month_total2->Oct:0;?>,
							<?php echo $month_total2->Nov?$month_total2->Nov:0;?>,
							<?php echo $month_total2->Dece?$month_total2->Dece:0;?>
						],
					}]
				},
				options: {
					responsive: true,
					title: {
						display: true,
						text: '今年收入统计'
					},
					tooltips: {
						mode: 'index',
						intersect: false,
					},
					hover: {
						mode: 'nearest',
						intersect: true
					}
				}
			};

			var config2 = {
				type: 'bar',
				data: {
					labels: ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23', '24', '25', '26', '27', '28', '29', '30', '31'],
					datasets: [{
						label: '充值额(元)',
						backgroundColor: window.chartColors.red,
						borderColor: window.chartColors.red,
						data: [
							<?php echo $day_total->one / $ice_proportion_alipay;?>,
							<?php echo $day_total->two / $ice_proportion_alipay;?>,
							<?php echo $day_total->three / $ice_proportion_alipay;?>,
							<?php echo $day_total->four / $ice_proportion_alipay;?>,
							<?php echo $day_total->five / $ice_proportion_alipay;?>,
							<?php echo $day_total->six / $ice_proportion_alipay;?>,
							<?php echo $day_total->seven / $ice_proportion_alipay;?>,
							<?php echo $day_total->eight / $ice_proportion_alipay;?>,
							<?php echo $day_total->nine / $ice_proportion_alipay;?>,
							<?php echo $day_total->ten / $ice_proportion_alipay;?>,
							<?php echo $day_total->eleven / $ice_proportion_alipay;?>,
							<?php echo $day_total->twelve / $ice_proportion_alipay;?>,
							<?php echo $day_total->thirteen / $ice_proportion_alipay;?>,
							<?php echo $day_total->fourteen / $ice_proportion_alipay;?>,
							<?php echo $day_total->fifteen / $ice_proportion_alipay;?>,
							<?php echo $day_total->sixteen / $ice_proportion_alipay;?>,
							<?php echo $day_total->seventeen / $ice_proportion_alipay;?>,
							<?php echo $day_total->eighteen / $ice_proportion_alipay;?>,
							<?php echo $day_total->nineteen / $ice_proportion_alipay;?>,
							<?php echo $day_total->twenty / $ice_proportion_alipay;?>,
							<?php echo $day_total->twentyone / $ice_proportion_alipay;?>,
							<?php echo $day_total->twentytwo / $ice_proportion_alipay;?>,
							<?php echo $day_total->twentythree / $ice_proportion_alipay;?>,
							<?php echo $day_total->twentyfour / $ice_proportion_alipay;?>,
							<?php echo $day_total->twentyfive / $ice_proportion_alipay;?>,
							<?php echo $day_total->twentysix / $ice_proportion_alipay;?>,
							<?php echo $day_total->twentyseven / $ice_proportion_alipay;?>,
							<?php echo $day_total->twentyeight / $ice_proportion_alipay;?>,
							<?php echo $day_total->twentynine / $ice_proportion_alipay;?>,
							<?php echo $day_total->thirty / $ice_proportion_alipay;?>,
							<?php echo $day_total->thirtyone / $ice_proportion_alipay;?>
						],
						fill: false,
					}, {
						label: '订单数(笔)',
						fill: false,
						backgroundColor: window.chartColors.blue,
						borderColor: window.chartColors.blue,
						data: [
							<?php echo $day_total2->one?$day_total2->one:0;?>,
							<?php echo $day_total2->two?$day_total2->two:0;?>,
							<?php echo $day_total2->three?$day_total2->three:0;?>,
							<?php echo $day_total2->four?$day_total2->four:0;?>,
							<?php echo $day_total2->five?$day_total2->five:0;?>,
							<?php echo $day_total2->six?$day_total2->six:0;?>,
							<?php echo $day_total2->seven?$day_total2->seven:0;?>,
							<?php echo $day_total2->eight?$day_total2->eight:0;?>,
							<?php echo $day_total2->nine?$day_total2->nine:0;?>,
							<?php echo $day_total2->ten?$day_total2->ten:0;?>,
							<?php echo $day_total2->eleven?$day_total2->eleven:0;?>,
							<?php echo $day_total2->twelve?$day_total2->twelve:0;?>,
							<?php echo $day_total2->thirteen?$day_total2->thirteen:0;?>,
							<?php echo $day_total2->fourteen?$day_total2->fourteen:0;?>,
							<?php echo $day_total2->fifteen?$day_total2->fifteen:0;?>,
							<?php echo $day_total2->sixteen?$day_total2->sixteen:0;?>,
							<?php echo $day_total2->seventeen?$day_total2->seventeen:0;?>,
							<?php echo $day_total2->eighteen?$day_total2->eighteen:0;?>,
							<?php echo $day_total2->nineteen?$day_total2->nineteen:0;?>,
							<?php echo $day_total2->twenty?$day_total2->twenty:0;?>,
							<?php echo $day_total2->twentyone?$day_total2->twentyone:0;?>,
							<?php echo $day_total2->twentytwo?$day_total2->twentytwo:0;?>,
							<?php echo $day_total2->twentythree?$day_total2->twentythree:0;?>,
							<?php echo $day_total2->twentyfour?$day_total2->twentyfour:0;?>,
							<?php echo $day_total2->twentyfive?$day_total2->twentyfive:0;?>,
							<?php echo $day_total2->twentysix?$day_total2->twentysix:0;?>,
							<?php echo $day_total2->twentyseven?$day_total2->twentyseven:0;?>,
							<?php echo $day_total2->twentyeight?$day_total2->twentyeight:0;?>,
							<?php echo $day_total2->twentynine?$day_total2->twentynine:0;?>,
							<?php echo $day_total2->thirty?$day_total2->thirty:0;?>,
							<?php echo $day_total2->thirtyone?$day_total2->thirtyone:0;?>
						],
					}]
				},
				options: {
					responsive: true,
					title: {
						display: true,
						text: '本月收入统计'
					},
					tooltips: {
						mode: 'index',
						intersect: false,
					},
					hover: {
						mode: 'nearest',
						intersect: true
					}
				}
			};

			window.onload = function() {
				var ctx = document.getElementById('erphpdown_year_total').getContext('2d');
				window.myLine = new Chart(ctx, config);

				var ctx2 = document.getElementById('erphpdown_month_total').getContext('2d');
				window.myLine2 = new Chart(ctx2, config2);
			};
		</script>

<?php
		echo '<div style="padding: 8px 12px 0;margin:0 -12px"><i class="dashicons dashicons-external"></i> 强烈推荐资源下载主题<a href="https://www.mobantu.com/7191.html" target="_blank">Modown</a>。</div>';
	}
}

function addUserAff($userId,$money){
	global $wpdb;
	$myinfo=$wpdb->get_row("select * from ".$wpdb->iceinfo." where ice_user_id=".$userId);
	if(!$myinfo){
		return $wpdb->query("insert into $wpdb->iceinfo(ice_have_money,ice_user_id,ice_get_money,ice_have_aff)values(0,'$userId',0,'$money')");
	}else{
		return $wpdb->query("update $wpdb->iceinfo set ice_have_aff=ice_have_aff+".$money." where ice_user_id=".$userId);
	}
}

function addUserAffXiaoFei($userId,$money){
	global $wpdb;
	return $wpdb->query("update $wpdb->iceinfo set ice_get_aff=ice_get_aff+".$money." where ice_user_id=".$userId);	
}

function addUserIp($ip){
	global $wpdb;
	if($ip){
		$myinfo=$wpdb->get_row("select * from ".$wpdb->iceinfo." where ice_ip='".$ip."'");
		if(!$myinfo){
		    $wpdb->query("insert into $wpdb->iceinfo(ice_have_money,ice_user_id,ice_get_money,ice_ip)values(0,0,0,'".$ip."')");
		}
	}
}

function addUserMoney($userId,$money,$why=''){
	global $wpdb;
	// 修复：$money 原生拼进算术 SQL，非数字入参（带单位/逗号）会触发语法错误并静默 return false，导致充值/返利不到账。此处统一规范化，治本覆盖所有调用方。
	$userId = intval($userId);
	$money  = floatval($money);
	if($userId){
		if($why){
			addUserMoneyLog($userId,$money,$why);
		}
		$myinfo=$wpdb->get_row("select * from ".$wpdb->iceinfo." where ice_user_id=".$userId);
		if(!$myinfo){
			return $wpdb->query("insert into $wpdb->iceinfo(ice_have_money,ice_user_id,ice_get_money)values('$money','$userId',0)");
		}else{
			return $wpdb->query("update $wpdb->iceinfo set ice_have_money=ice_have_money+".$money." where ice_user_id=".$userId);
		}
	}else{
		return false;
	}
}

function addUserMoneyLog($userId,$money,$why=''){
	date_default_timezone_set('Asia/Shanghai');
	global $wpdb;
	if($userId){
		if($wpdb->get_var("show tables like '".$wpdb->icelog."'")){
			$wpdb->query("insert into $wpdb->icelog(user_id,ice_money,ice_note,ice_time)values('$userId','$money','$why','".date("Y-m-d H:i:s")."')");
		}
	}
}

function erphpdown_check_xiaofei($uid){
	global $wpdb;
	$down_info=$wpdb->get_row("select * from ".$wpdb->icealipay." where ice_success=1 and ice_user_id=".$uid);
	if($down_info)
		return true;
	return false;
}

function erphpdown_check_checkin($uid){
	date_default_timezone_set('Asia/Shanghai');
    global $wpdb;
    $result = $wpdb->get_var("select count(ID) from $wpdb->checkin where TO_DAYS(create_time) = TO_DAYS(NOW()) and user_id=".$uid);
    if($result){
        return 1;
    }
    return 0;
}

function erphpdown_download_file($file_dir){
	//$file_dir = iconv('UTF-8', 'GBK//TRANSLIT', $file_dir);
	if(substr($file_dir,0,7) == 'http://' || substr($file_dir,0,8) == 'https://' || substr($file_dir,0,10) == 'thunder://' || substr($file_dir,0,7) == 'magnet:' || substr($file_dir,0,5) == 'ed2k:' || substr($file_dir,0,4) == 'ftp:'){
		$file_path = chop($file_dir);
		$allow_type = get_option('erphpdown_direct_type');
		if($allow_type){
			$allow = explode(",",$allow_type); 
			if (erphpdown_file_suffix($file_path,$allow)){
	            ob_clean();
	            ob_start();
	            if(strpos(strtolower($file_path),'.pdf')){
	            	header('Content-type: application/pdf');
	            }
	            header('Content-Disposition: attachment; filename="'.basename($file_path).'"');
				@readfile($file_path);
			}else{
				header('Location:'.$file_path);
				exit;
				//echo '<script>window.location.href="'.$file_path.'";</script>';
			}
		}else{
			header('Location:'.$file_path);
			exit;
			//echo '<script>window.location.href="'.$file_path.'";</script>';
		}

		exit;
	}

	$file_dir=chop($file_dir);
	if(!file_exists($file_dir)){
		return false;
	}
	
	$temp=explode("/",$file_dir);

	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: public");
	header("Content-Description: File Transfer");
	header("Content-type: application/octet-stream");
	header("Content-Disposition: attachment; filename=\"".end($temp)."\"");
	header("Content-Transfer-Encoding: binary");
	header("Content-Length: ".filesize($file_dir));
	ob_end_flush();
	@readfile($file_dir);
}

function erphpdown_file_suffix($file_name, $allow_type = array()){
  $fnarray=explode('.', $file_name);
    $file_suffix = strtolower(array_pop($fnarray));
  if (empty($allow_type))
  {
    return $file_suffix;
  }
  else
  {
    if (in_array($file_suffix, $allow_type))
    {
      return true;
    }
    else
    {
      return false;
    }
  }
}


function erphpdown_modify_user_table( $column ) {
	$erphp_admin_users_filter = get_option('erphp_admin_users_filter');
	if($erphp_admin_users_filter){
	    $column['vip'] = 'VIP';
	    $column['money'] = '站内余额';
	    $erphp_aff_money = get_option('erphp_aff_money');
	    if($erphp_aff_money){
		    $column['money2'] = '推广余额';
		}
	    $column['cart'] = '消费记录';
	}
    $column['reg'] = '注册时间';
    return $column;
}
add_filter( 'manage_users_columns', 'erphpdown_modify_user_table' , 20, 1);

function erphpdown_modify_user_table_row( $val, $column_name, $user_id ) {
	$erphp_admin_users_filter = get_option('erphp_admin_users_filter');
	if($erphp_admin_users_filter){
	    switch ($column_name) {
	        case 'vip' :
	        	$userType = getUsreMemberTypeById($user_id);
	            if($userType == 6){
	            	return '体验';
	            }elseif($userType == 7){
	            	return '包月';
	            }elseif($userType == 8){
	            	return '包季';
	            }elseif($userType == 9){
	            	return '包年';
	            }elseif($userType == 10){
	            	return '终身';
	            }else{
	            	return '—';
	            }
	            break;
	        case 'money':
	        	$okm = erphpGetUserOkMoneyById($user_id);
	            return $okm!=0?$okm:'0';
	        	break;
	        case 'money2':
	        	$oka = erphpGetUserOkAffById($user_id);
	            return $oka!=0?$oka:'0';
	        	break;
	        default:
	        case 'cart':
	            return erphpdown_check_xiaofei($user_id)?'有':'—';
	        	break;
	        case 'reg':
	        	$user = get_user_by("ID",$user_id);
	            return get_date_from_gmt($user->user_registered);
	        	break;
	    }
	}else{
		switch ($column_name) {
	        case 'reg':
	        	$user = get_user_by("ID",$user_id);
	            return get_date_from_gmt($user->user_registered);
	        	break;
	    }
	}
    return $val;
}
add_filter( 'manage_users_custom_column', 'erphpdown_modify_user_table_row', 10, 3 );

add_filter( 'manage_users_sortable_columns', 'erphpdown_modify_user_table_row_sortable' );
function erphpdown_modify_user_table_row_sortable( $columns ) {
	return wp_parse_args( array( 'reg' => 'registered' ), $columns );
}

add_action( 'pre_user_query', 'erphpdown_users_search_order' ); 
function erphpdown_users_search_order($obj){ 
	if(!isset($_REQUEST['orderby']) || $_REQUEST['orderby']=='reg' ){ 
	    $order = 'desc';
		if( isset($_REQUEST['order']) && !in_array($_REQUEST['order'],array('asc','desc')) ){ 
			$order = 'desc'; 
		} 
		$obj->query_orderby = "ORDER BY user_registered ".$order.""; 
	}
} 

function erphpdown_column_width() {
    echo '<style type="text/css">';
    echo '.column-vip, .column-money, .column-money2 , .column-cart{width:74px;}.column-reg{width:90px;}';
    echo '</style>';
}
//add_action('admin_head', 'erphpdown_column_width');

function erphpdown_download_ad_callback() {
    $erphp_ad_download = str_replace('\"', '"', get_option("erphp_ad_download"));
    if($erphp_ad_download){
    	echo '<div class="erphpdown-download-da">'.$erphp_ad_download.'</div>';
    }
}
add_action( 'erphpdown_download_ad', 'erphpdown_download_ad_callback', 10, 0 );

function erphpdown_get_vip_ref($uid){
	if($uid){
		$userType = getUsreMemberTypeById($uid);
		$erphp_life_ref    = get_option('erphp_life_ref');
		$erphp_year_ref    = get_option('erphp_year_ref');
		$erphp_quarter_ref = get_option('erphp_quarter_ref');
		$erphp_month_ref  = get_option('erphp_month_ref');
		$erphp_day_ref  = get_option('erphp_day_ref');
		if($userType == 6){
			return $erphp_day_ref;
		}elseif($userType == 7){
			return $erphp_month_ref;
		}elseif($userType == 8){
			return $erphp_quarter_ref;
		}elseif($userType == 9){
			return $erphp_year_ref;
		}elseif($userType == 10){
			return $erphp_life_ref;
		}
	}
	return '';
}

function erphpdown_get_vip_ref2($uid){
	if($uid){
		$userType = getUsreMemberTypeById($uid);
		$erphp_life_ref    = get_option('erphp_life_ref2');
		$erphp_year_ref    = get_option('erphp_year_ref2');
		$erphp_quarter_ref = get_option('erphp_quarter_ref2');
		$erphp_month_ref  = get_option('erphp_month_ref2');
		$erphp_day_ref  = get_option('erphp_day_ref2');
		if($userType == 6){
			return $erphp_day_ref;
		}elseif($userType == 7){
			return $erphp_month_ref;
		}elseif($userType == 8){
			return $erphp_quarter_ref;
		}elseif($userType == 9){
			return $erphp_year_ref;
		}elseif($userType == 10){
			return $erphp_life_ref;
		}
	}
	return '';
}