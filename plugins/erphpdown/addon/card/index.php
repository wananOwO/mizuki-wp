<?php 
function erphpdown_card_create_guid($namespace = '') {
    $guid = '';
    $uid = uniqid("", true);
    $data = $namespace;
    $data .= $_SERVER['REQUEST_TIME'];
    $data .= $_SERVER['HTTP_USER_AGENT'];
    $data .= $_SERVER['REMOTE_ADDR'];
    $data .= $_SERVER['REMOTE_PORT'];
    $hash = strtoupper(hash('ripemd128', $uid . $guid . md5($data)));
    $guid = substr($hash, 0, 4).'-'.substr($hash, 8, 4).'-'.substr($hash, 12, 4).'-'.substr($hash, 16, 4).'-'.substr($hash, 20, 4);
    return $guid;
}

if(!function_exists('erphpdown_card_install')){
	function erphpdown_card_install(){
		global $wpdb;
		$table_name = $wpdb->prefix.'erphpdown_card';
		$sql = "CREATE TABLE ".$table_name." (
				id mediumint(9) NOT NULL AUTO_INCREMENT,
				card varchar(100),
				password varchar(100),
				uid int(10) DEFAULT '0',
				username varchar(200),
				usetime datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
				status int(3) DEFAULT '0' NOT NULL,
				price double(10,2) NOT NULL,
				UNIQUE KEY id (id)
				);";
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}
}

if(!function_exists('isErphpCardUsed')){
	function isErphpCardUsed($id){
		global $wpdb;
		$result = $wpdb->get_row("select * from $wpdb->erphpcard where id = '".$id."'");
		if(!$result->status) return '否';
		else{ 
			if($result->uid){
				return '是 [使用者：'.get_the_author_meta( 'user_login', $result->uid ).'，时间：'.$result->usetime.']';
			}else{
				return '是 ['.$result->username.'，时间：'.$result->usetime.']';
			}
		}
	}
}

if(!function_exists('checkDoCardResult')){
	function checkDoCardResult($card,$password){
		date_default_timezone_set('Asia/Shanghai');
		if(is_user_logged_in()){
			global $wpdb, $current_user;
			$result = $wpdb->get_row("select * from $wpdb->erphpcard where card = '".esc_sql($card)."'");
			if($result->status == '0'){
				if($result->password == $password){
					$ss = $wpdb->query("update $wpdb->erphpcard set status=1,uid='".$current_user->ID."',usetime='".date("Y-m-d H:i:s")."' where card='".esc_sql($card)."'");
					if($ss){
						$alipay_no = date("ymdhis").mt_rand(100, 999).mt_rand(100,999);
						$sql="INSERT INTO $wpdb->icemoney (ice_money,ice_num,ice_user_id,ice_time,ice_success,ice_note,ice_success_time,ice_alipay)
					VALUES ('".$result->price*get_option('ice_proportion_alipay')."','$alipay_no','".$current_user->ID."','".date("Y-m-d H:i:s")."',1,'6','".date("Y-m-d H:i:s")."','')";
						$a=$wpdb->query($sql);
						if($a){
							addUserMoney($current_user->ID, $result->price*get_option('ice_proportion_alipay'));
							return '1';
						}else{
							return '4';
						}
					}else{
						return '4';
					}
				}else{
					return '2';
				}
			}elseif($result->status == '1'){
				return '0';  //已被使用过
			}else{
				return '5';
			}
		}else{
			return '4';
		}
	}
}
