<?php
function create_vipguid($namespace = '') {
    static $guid = '';
    $uid = uniqid("", true);
    $data = $namespace;
    $data .= $_SERVER['REQUEST_TIME'];
    $data .= $_SERVER['HTTP_USER_AGENT'];
    //$data .= $_SERVER['LOCAL_ADDR'];
    //$data .= $_SERVER['LOCAL_PORT'];
    $data .= $_SERVER['REMOTE_ADDR'];
    $data .= $_SERVER['REMOTE_PORT'];
    $hash = strtoupper(hash('ripemd128', $uid . $guid . md5($data)));
    $guid = substr($hash, 0, 4).'-'.substr($hash, 8, 4).'-'.substr($hash, 12, 4).'-'.substr($hash, 16, 4).'-'.substr($hash, 20, 4);
    return $guid;
}


function erphpdown_vipcard_install(){
	return true;
}

function isErphpVipCardUsed($id){
	global $wpdb;
	$result = $wpdb->get_row("select * from $wpdb->erphpvipcard where id = '".$id."'");
	if(!$result->status) return '否';
	else return '是 [使用者：'.($result->uid?get_the_author_meta( 'user_login', $result->uid ):'游客').'，时间：'.$result->usetime.']';
}

function checkDoVipCardResult($card){
	date_default_timezone_set('Asia/Shanghai');
	if(is_user_logged_in()){
		global $wpdb, $current_user;
		$result = $wpdb->get_row("select * from $wpdb->erphpvipcard where card = '".esc_sql($card)."'");
		if($result->status == '0'){
			$user_type = $result->usertype;
			$endtime = $result->endtime;
			if(time() > strtotime($endtime) && $endtime != '0000-00-00 00:00:00'){
				return '2';//过期
			}else{
				$ss = $wpdb->query("update $wpdb->erphpvipcard set status=1,uid='".$current_user->ID."',usetime='".date("Y-m-d H:i:s")."' where card='".esc_sql($card)."'");
				if($ss){
					addUserMoney($current_user->ID,'0');
					if(userSetMemberSetData($user_type,$current_user->ID) ){
						addVipLogByAdmin('0', $user_type, $current_user->ID);

						if(function_exists('_mbt_add_notice')){
							_mbt_add_notice($current_user->ID, '您好，您已成功升级成为VIP。', 'vip', $user_type);
						}

						if(get_option('erphp_addon_vipcard_aff')){
							$priceArr=array('6'=>'erphp_day_price','7'=>'erphp_month_price','8'=>'erphp_quarter_price','9'=>'erphp_year_price','10'=>'erphp_life_price');
							$priceType=$priceArr[$user_type];
							$price=get_option($priceType);
				
							$EPD = new EPD();
							$EPD->doAff($price, $current_user->ID);
						}
						return '1'; //成功
					}
				}else{
					return '4'; //系统错误
				}
			}
			
		}elseif($result->status == '1'){
			return '0';  //已被使用过
		}else{
			return '3'; //不存在
		}
	}elseif(get_option('erphp_wppay_vip')){
		global $wpdb, $current_user;
		$ip = erphpGetIP();
		if($ip){
			$result = $wpdb->get_row("select * from $wpdb->erphpvipcard where card = '".esc_sql($card)."'");
			if($result->status == '0'){
				$user_type = $result->usertype;
				$endtime = $result->endtime;
				if(time() > strtotime($endtime) && $endtime != '0000-00-00 00:00:00'){
					return '2';//过期
				}else{
					$ss = $wpdb->query("update $wpdb->erphpvipcard set status=1,uid='".$current_user->ID."',usetime='".date("Y-m-d H:i:s")."' where card='".esc_sql($card)."'");
					if($ss){
						if(userSetMemberSetData($user_type,$current_user->ID,$ip) ){
							addVipLogByAdmin('0', $user_type, $current_user->ID,$ip);
							$ice_aff = '';
							if(isset($_COOKIE["erphprefid"]) && is_numeric($_COOKIE["erphprefid"])){
					            $ice_aff = $_COOKIE["erphprefid"];
					        }

							if(get_option('erphp_addon_vipcard_aff') && $ice_aff){
								$priceArr=array('6'=>'erphp_day_price','7'=>'erphp_month_price','8'=>'erphp_quarter_price','9'=>'erphp_year_price','10'=>'erphp_life_price');
								$priceType=$priceArr[$user_type];
								$price=get_option($priceType);
					
								$EPD = new EPD();
								$EPD->doAff2($price, $ice_aff);
							}
							return '1'; //成功
						}
					}else{
						return '4'; //系统错误
					}
				}
				
			}elseif($result->status == '1'){
				return '0';  //已被使用过
			}else{
				return '3'; //不存在
			}
		}else{
			return '4'; //系统错误
		}
	}else{
		return '4';
	}
}

function getVipCardTypeLeft($type){
	global $wpdb;
	$result = $wpdb->get_var("select count(id) from $wpdb->erphpvipcard where status = 0 and usertype = '".$type."'");
	return $result;
}