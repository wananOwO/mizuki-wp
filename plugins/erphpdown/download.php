<?php
// +----------------------------------------------------------------------
// | ERPHP [ PHP DEVELOP ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.mobantu.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: mobantu <82708210@qq.com>
// +----------------------------------------------------------------------

header("Content-type:text/html;character=utf-8");
require_once('../../../wp-load.php');
date_default_timezone_set('Asia/Shanghai');

add_filter('wp_title', 'assignPageTitle');
function assignPageTitle(){
	return __("文件下载",'erphpdown');
}

$erphp_wppay_download = get_option("erphp_wppay_download");
if($erphp_wppay_download && !is_user_logged_in()){
	wp_die(__('请先登录','erphpdown'),__("友情提示",'erphpdown'));
}

$postid=isset($_GET['postid']) && is_numeric($_GET['postid']) ?intval($_GET['postid']) :false;
$start_down2='';

if($postid){
	$ppost = get_post($postid);
	if(!$ppost) wp_die(__('下载信息错误！','erphpdown'),__("友情提示",'erphpdown'));

	$start_down2 = get_post_meta($postid, 'start_down2',TRUE);
}

if(!is_user_logged_in() && !$start_down2 && !get_option('erphp_wppay_down') && !get_option('erphp_wppay_vip')){
	wp_die(__('请先登录','erphpdown'),__("友情提示",'erphpdown'));
}

$url=isset($_GET['url']) ? esc_sql($_GET['url']) :false;
$key=isset($_GET['key']) ? esc_sql($_GET['key']) :false;
$timekey=isset($_GET['timekey']) ? $_GET['timekey'] :'';
$iframe=isset($_GET['iframe']) ? $_GET['iframe'] : 0;
$index=isset($_GET['index']) ? esc_sql($_GET['index']) : '';

$postid = esc_sql($postid);
$index_name = '';
$index_vip = '';
$hasdown_info = 0;
$freedown = 0;
$ice_data = '';
if($timekey && $postid && $timekey == md5($postid.get_option('erphpdown_downkey'))){
	$_COOKIE['erphpdown_wait_'.$postid] = 1;
    setcookie('erphpdown_wait_'.$postid,1,0,'/');
}

if($postid==false && $url==false ){
	wp_die(__("下载信息错误！",'erphpdown'),__("友情提示",'erphpdown'));
}

if ($postid){
	$ypost = get_post($postid);
	if(!$ypost){
		wp_die(__("下载信息错误！",'erphpdown'),__("友情提示",'erphpdown'));
	}
	$isDown=FALSE;

	if($index){
		$urls = get_post_meta($postid, 'down_urls', true);
		if($urls){
			$cnt = count($urls['index']);
			if($cnt){
				for($i=0; $i<$cnt;$i++){
					if($urls['index'][$i] == $index){
    					$data = $urls['url'][$i];
    					$index_name = $urls['name'][$i];
    					$price = $urls['price'][$i];
    					$index_vip = $urls['vip'][$i];
    					break;
    				}
				}
			}
		}
	}else{
		$data=get_post_meta($postid, 'down_url', true);
		$price=get_post_meta($postid, 'down_price', true);
	}

	$memberDown=get_post_meta($postid, 'member_down',TRUE);
	if($index_vip){
		$memberDown = $index_vip;
	}
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

	$user_info=wp_get_current_user();
	$days=get_post_meta($postid, 'down_days', true);
	$down_tuan=get_post_meta($postid, 'down_tuan', true);

	if(is_user_logged_in()){
		if($index){
			$hasdown_info=$wpdb->get_row("select * from ".$wpdb->icealipay." where ice_post='".$postid."' and ice_index='".$index."' and ice_success=1 and ice_user_id=".$user_info->ID." order by ice_time desc");
		}else{
			$hasdown_info=$wpdb->get_row("select * from ".$wpdb->icealipay." where ice_post='".$postid."' and ice_success=1 and (ice_index is null or ice_index = '') and ice_user_id=".$user_info->ID." order by ice_time desc");
		}

		if($hasdown_info){
			$ice_data = $hasdown_info->ice_data;
		}

		if($days > 0 && $hasdown_info){
			$lastDownDate = date('Y-m-d H:i:s',strtotime('+'.$days.' day',strtotime($hasdown_info->ice_time)));
			$nowDate = date('Y-m-d H:i:s');
			if(strtotime($nowDate) > strtotime($lastDownDate)){
				$hasdown_info = 0;
				//wp_die("下载权限已过期，请重新购买！","友情提示");
			}
		}
	}


	$user_id = is_user_logged_in() ? wp_get_current_user()->ID : 0;
	$wppay = new EPD($postid, $user_id);

	if($wppay->isWppayPaid() || $wppay->isWppayPaidNew($index)){
		$hasdown_info = 1;
	}



	$ews_erphpdown = get_option("ews_erphpdown");
	if($ews_erphpdown && function_exists("ews_erphpdown") && isset($_COOKIE['ewd_'.$postid]) && $_COOKIE['ewd_'.$postid] == md5($postid.get_option('erphpdown_downkey'))){
		$hasdown_info = 1;
	}

	if($down_tuan){
		$yituan = $wpdb->get_var("select ice_id from $wpdb->tuanorder where ice_user_id=".$user_info->ID." and ice_post=".$postid." and ice_status=2");
		if($yituan){
			$hasdown_info = 1;
		}
	}
	
	if(!$hasdown_info && is_user_logged_in()){
		if(!$price && $memberDown != 4 && $memberDown != 15 && $memberDown != 8 && $memberDown != 9){

			$erphp_reg_times_from  = get_option('erphp_reg_times_from');
			$erphp_reg_times_to  = get_option('erphp_reg_times_to');

			if($erphp_reg_times_from && $erphp_reg_times_to && !$userType){
				if(date("H") >= $erphp_reg_times_from && date("H") < $erphp_reg_times_to){
					wp_die("用户您好，".$erphp_reg_times_from.":00 — ".$erphp_reg_times_to.":00为网站访问高峰期，免费下载通道拥挤，<a href='".get_option('erphp_url_front_vip')."' target='_blank'>升级VIP</a>，畅想高速下载！",__("友情提示",'erphpdown'));
				}
			}

			$erphp_reg_times  = get_option('erphp_reg_times');
			if(!$userType){
				if($erphp_reg_times > 0){
					if( checkDownLog($user_info->ID,$postid,$erphp_reg_times,0) ){

					}else{
						wp_die("普通用户每天只能下载".$erphp_reg_times."个免费资源！<a href='".get_option('erphp_url_front_vip')."' target='_blank'>升级VIP下载更多资源</a>",__("友情提示",'erphpdown'));
					}
				}
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

				$erphp_life_name    = get_option('erphp_life_name')?get_option('erphp_life_name'):'终身VIP';
				$erphp_year_name    = get_option('erphp_year_name')?get_option('erphp_year_name'):'包年VIP';
				$erphp_quarter_name = get_option('erphp_quarter_name')?get_option('erphp_quarter_name'):'包季VIP';
				$erphp_month_name  = get_option('erphp_month_name')?get_option('erphp_month_name'):'包月VIP';
				$erphp_day_name  = get_option('erphp_day_name')?get_option('erphp_day_name'):'体验VIP';
				$erphp_vip_name  = get_option('erphp_vip_name')?get_option('erphp_vip_name'):'VIP';

				if($userType == 6 && $erphp_day_times > 0 && $day_times_includes_free){
					if( checkDownLogNoVip($user_info->ID,$postid,$erphp_day_times) ){

					}else{
						wp_die($erphp_day_name."用户每天只能免费下载".$erphp_day_times."个免费资源！",__("友情提示",'erphpdown'));
					}
				}elseif($userType == 7 && $erphp_month_times > 0 && $month_times_includes_free){
					if( checkDownLogNoVip($user_info->ID,$postid,$erphp_month_times) ){

					}else{
						wp_die($erphp_month_name."用户每天只能免费下载".$erphp_month_times."个免费资源！",__("友情提示",'erphpdown'));
					}
				}elseif($userType == 8 && $erphp_quarter_times > 0 && $quarter_times_includes_free){
					if( checkDownLogNoVip($user_info->ID,$postid,$erphp_quarter_times) ){

					}else{
						wp_die($erphp_quarter_name."用户每天只能免费下载".$erphp_quarter_times."个免费资源！",__("友情提示",'erphpdown'));
					}
				}elseif($userType == 9 && $erphp_year_times > 0 && $year_times_includes_free){
					if( checkDownLogNoVip($user_info->ID,$postid,$erphp_year_times) ){

					}else{
						wp_die($erphp_year_name."用户每天只能免费下载".$erphp_year_times."个免费资源！",__("友情提示",'erphpdown'));
					}
				}elseif($userType == 10 && $erphp_life_times > 0 && $life_times_includes_free){
					if( checkDownLogNoVip($user_info->ID,$postid,$erphp_life_times) ){

					}else{
						wp_die($erphp_life_name."用户每天只能免费下载".$erphp_life_times."个免费资源！",__("友情提示",'erphpdown'));
					}
				}
			}

		}else{
			if($memberDown == 3 || $memberDown == 4 || $memberDown == 16 || $memberDown == 15 || $memberDown == 6 || $memberDown == 7 || $memberDown == 8 || $memberDown == 9 || $memberDown == 13 || $memberDown == 14 || $memberDown == 20){
				
				if($userType){

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

					$erphp_life_name    = get_option('erphp_life_name')?get_option('erphp_life_name'):'终身VIP';
					$erphp_year_name    = get_option('erphp_year_name')?get_option('erphp_year_name'):'包年VIP';
					$erphp_quarter_name = get_option('erphp_quarter_name')?get_option('erphp_quarter_name'):'包季VIP';
					$erphp_month_name  = get_option('erphp_month_name')?get_option('erphp_month_name'):'包月VIP';
					$erphp_day_name  = get_option('erphp_day_name')?get_option('erphp_day_name'):'体验VIP';
					$erphp_vip_name  = get_option('erphp_vip_name')?get_option('erphp_vip_name'):'VIP';

					$erphp_life_discount    = get_option('erphp_life_discount');
					$erphp_year_discount    = get_option('erphp_year_discount');
					$erphp_quarter_discount = get_option('erphp_quarter_discount');
					$erphp_month_discount  = get_option('erphp_month_discount');
					$erphp_day_discount  = get_option('erphp_day_discount');

					if($userType == 6 && $erphp_day_times > 0){
						if($day_times_includes_free){
							if( checkDownLogNoVip($user_info->ID,$postid,$erphp_day_times) ){

							}else{
								wp_die($erphp_day_name."用户每天只能免费下载".$erphp_day_times."个免费资源！<a href='".constant("erphpdown")."buy.php?postid=".$postid."&index=".$index."'>单独购买".($erphp_day_discount?'('.$erphp_day_discount.'折)':'')."</a>",__("友情提示",'erphpdown'));
							}
						}else{
							if( checkDownLog($user_info->ID,$postid,$erphp_day_times,1) ){

							}else{
								wp_die($erphp_day_name."用户每天只能免费下载".$erphp_day_times."个".$erphp_vip_name."资源！<a href='".constant("erphpdown")."buy.php?postid=".$postid."&index=".$index."'>单独购买".($erphp_day_discount?'('.$erphp_day_discount.'折)':'')."</a>",__("友情提示",'erphpdown'));
							}
						}
					}elseif($userType == 7 && $erphp_month_times > 0){
						if($month_times_includes_free){
							if( checkDownLogNoVip($user_info->ID,$postid,$erphp_month_times) ){

							}else{
								wp_die($erphp_month_name."用户每天只能免费下载".$erphp_month_times."个免费资源！<a href='".constant("erphpdown")."buy.php?postid=".$postid."&index=".$index."'>单独购买".($erphp_month_discount?'('.$erphp_month_discount.'折)':'')."</a>",__("友情提示",'erphpdown'));
							}
						}else{
							if( checkDownLog($user_info->ID,$postid,$erphp_month_times,1) ){

							}else{
								wp_die($erphp_month_name."用户每天只能免费下载".$erphp_month_times."个".$erphp_vip_name."资源！<a href='".constant("erphpdown")."buy.php?postid=".$postid."&index=".$index."'>单独购买".($erphp_month_discount?'('.$erphp_month_discount.'折)':'')."</a>",__("友情提示",'erphpdown'));
							}
						}
					}elseif($userType == 8 && $erphp_quarter_times > 0){
						if($quarter_times_includes_free){
							if( checkDownLogNoVip($user_info->ID,$postid,$erphp_quarter_times) ){

							}else{
								wp_die($erphp_quarter_name."用户每天只能免费下载".$erphp_quarter_times."个免费资源！<a href='".constant("erphpdown")."buy.php?postid=".$postid."&index=".$index."'>单独购买".($erphp_quarter_discount?'('.$erphp_quarter_discount.'折)':'')."</a>",__("友情提示",'erphpdown'));
							}
						}else{
							if( checkDownLog($user_info->ID,$postid,$erphp_quarter_times,1) ){

							}else{
								wp_die($erphp_quarter_name."用户每天只能免费下载".$erphp_quarter_times."个".$erphp_vip_name."资源！<a href='".constant("erphpdown")."buy.php?postid=".$postid."&index=".$index."'>单独购买".($erphp_quarter_discount?'('.$erphp_quarter_discount.'折)':'')."</a>",__("友情提示",'erphpdown'));
							}
						}
					}elseif($userType == 9 && $erphp_year_times > 0){
						if($year_times_includes_free){
							if( checkDownLogNoVip($user_info->ID,$postid,$erphp_year_times) ){

							}else{
								wp_die($erphp_year_name."用户每天只能免费下载".$erphp_year_times."个免费资源！<a href='".constant("erphpdown")."buy.php?postid=".$postid."&index=".$index."'>单独购买".($erphp_year_discount?'('.$erphp_year_discount.'折)':'')."</a>",__("友情提示",'erphpdown'));
							}
						}else{
							if( checkDownLog($user_info->ID,$postid,$erphp_year_times,1) ){

							}else{
								wp_die($erphp_year_name."用户每天只能免费下载".$erphp_year_times."个".$erphp_vip_name."资源！<a href='".constant("erphpdown")."buy.php?postid=".$postid."&index=".$index."'>单独购买".($erphp_year_discount?'('.$erphp_year_discount.'折)':'')."</a>",__("友情提示",'erphpdown'));
							}
						}
					}elseif($userType == 10 && $erphp_life_times > 0){
						if($life_times_includes_free){
							if( checkDownLogNoVip($user_info->ID,$postid,$erphp_life_times) ){

							}else{
								wp_die($erphp_life_name."用户每天只能免费下载".$erphp_life_times."个免费资源！<a href='".constant("erphpdown")."buy.php?postid=".$postid."&index=".$index."'>单独购买".($erphp_life_discount?'('.$erphp_life_discount.'折)':'')."</a>",__("友情提示",'erphpdown'));
							}
						}else{
							if( checkDownLog($user_info->ID,$postid,$erphp_life_times,1) ){

							}else{
								wp_die($erphp_life_name."用户每天只能免费下载".$erphp_life_times."个".$erphp_vip_name."资源！<a href='".constant("erphpdown")."buy.php?postid=".$postid."&index=".$index."'>单独购买".($erphp_life_discount?'('.$erphp_life_discount.'折)':'')."</a>",__("友情提示",'erphpdown'));
							}
						}
					}
					
				}
			}
		}
	}


	if(strlen($data) > 2)
	{
		if($hasdown_info){
			$isDown=true;
			$pp = $postid;
		}
		elseif($user_info && $userType && ($memberDown ==3 || $memberDown ==4))
		{
			$isDown=true;
			$pp = $postid;
		}
		elseif($user_info && $userType >= 8 && ($memberDown ==15 || $memberDown ==16))
		{
			$isDown=true;
			$pp = $postid;
		}
		elseif($user_info && ($userType == 9 || $userType == 10) && ($memberDown ==6 || $memberDown ==8) )
		{
			$isDown=true;
			$pp = $postid;
		}
		elseif($user_info && $userType == 10 && ($memberDown ==7 || $memberDown ==9 || $memberDown == 13 || $memberDown == 14 || $memberDown == 20) )
		{
			$isDown=true;
			$pp = $postid;
		}
		else 
		{
			if( empty($price) || $price==0 )
			{
				if( ($memberDown ==4 && !$userType) || ($memberDown ==15 && $userType < 8) || ($memberDown ==8 && $userType < 9) || ($memberDown ==9 && $userType < 10) ){
					
				}else{
					$freedown = 1;
					$isDown=true;
					$pp = $postid;
				}
			}
		}
	}

	if(!is_user_logged_in() && $freedown && get_option('erphp_free_login')){
		wp_die(__('请先登录','erphpdown'),__("友情提示",'erphpdown'));
	}

	if(!$isDown){
		wp_die(__('下载信息错误！','erphpdown'),__("友情提示",'erphpdown'));
	}
}elseif($url){
	$user_info=wp_get_current_user();

	$down_info=$wpdb->get_row("select * from ".$wpdb->icealipay." where ice_url='".esc_sql($url)."' and ice_user_id=".$user_info->ID." order by ice_time desc");
	if($down_info){
		$downPostId=$down_info->ice_post;
		$ice_data = $down_info->ice_data;
		$days=get_post_meta($downPostId, 'down_days', true);
		if($days > 0){
			$lastDownDate = date('Y-m-d H:i:s',strtotime('+'.$days.' day',strtotime($down_info->ice_time)));
			$nowDate = date('Y-m-d H:i:s');
			if(strtotime($nowDate) > strtotime($lastDownDate)){
				wp_die(__('下载权限已过期，请重新购买','erphpdown'),__("友情提示",'erphpdown'));
			}
		}

		$pp = $downPostId;
		$postid = $downPostId;
		if($down_info->ice_index){
			$index = $down_info->ice_index;
			$urls = get_post_meta($postid, 'down_urls', true);
			if($urls){
				$cnt = count($urls['index']);
				if($cnt){
					for($i=0; $i<$cnt;$i++){
						if($urls['index'][$i] == $index){
	    					$data = $urls['url'][$i];
	    					$index_name = $urls['name'][$i];
	    					break;
	    				}
					}
				}
			}
		}else{
			$data=get_post_meta($downPostId, 'down_url', true);
		}
	}
	
	if(!$down_info || !$data){
		wp_die(__('下载信息错误！','erphpdown'),__("友情提示",'erphpdown'));
	}
}

$downList=explode("\r\n",trim($data));
$downMsg = '<div class="title"><span>'.($index_name?$index_name:'下载地址').'</span></div>';

if($key){
	if(is_numeric($key)){
		$key=intval($key);
	}else {
		wp_die(__('下载信息错误！','erphpdown'),__("友情提示",'erphpdown'));
	}

	$user_info=wp_get_current_user();
	$file=$downList[$key-1];
	//$file = iconv('UTF-8', 'GBK//TRANSLIT', $file);
	$times=time();
	if(is_user_logged_in()){
		$md5key=md5(md5($user_info->ID.'erphpdown'.$key.$times.get_option('erphpdown_downkey')).$user_info->ID.'erphpdown'.$key.$times.get_option('erphpdown_downkey').'mobantu');
	}else{
		$md5key=md5(md5('erphpdown'.$key.$times.get_option('erphpdown_downkey')).'erphpdown'.$key.$times.get_option('erphpdown_downkey').'mobantu');
	}

	$cypher = new ErphpCrypt(ErphpCrypt::CRYPT_MODE_HEXADECIMAL, ErphpCrypt::CRYPT_HASH_SHA1);
	$cypher->Key = get_option('erphpdown_downkey');
	$entemp = $cypher->encrypt($times);

	$file = trim($file);

	header("Location:downloadfile.php?id=".$pp."&filename=".$key."&index=".$index."&md5key=".$md5key."&times=".$times."&session_name=".$entemp);
	exit;
	
}else{
	$erphp_free_wait = get_option('erphp_free_wait');
	if($freedown && $erphp_free_wait && !(isset($_COOKIE['erphpdown_wait_'.$postid]) && $_COOKIE['erphpdown_wait_'.$postid]) && !getUsreMemberType()){
		epd_wait_page($postid);
	}else{
		foreach ($downList as $k=>$v){
			$filepath = trim($downList[$k]);
			if($filepath){

				$erphp_colon_domains = get_option('erphp_colon_domains')?get_option('erphp_colon_domains'):'pan.baidu.com';
				if($erphp_colon_domains){
					$erphp_colon_domains_arr = explode(',', $erphp_colon_domains);
					foreach ($erphp_colon_domains_arr as $erphp_colon_domain) {
						if(strpos($filepath, $erphp_colon_domain) !== false){
							$filepath = str_replace('：', ': ', $filepath);
							break;
						}
					}
				}

				$erphp_blank_domains = get_option('erphp_blank_domains')?get_option('erphp_blank_domains'):'pan.baidu.com';
				$erphp_blank_domain_is = 0;
				if($erphp_blank_domains){
					$erphp_blank_domains_arr = explode(',', $erphp_blank_domains);
					foreach ($erphp_blank_domains_arr as $erphp_blank_domain) {
						if(strpos($filepath, $erphp_blank_domain) !== false){
							$erphp_blank_domain_is = 1;
							break;
						}
					}
				}

				if(strpos($filepath,',') !== false){
					$filearr = explode(',',$filepath);
					$arrlength = count($filearr);
					if($arrlength == 1){
						$downMsg.="<p><span class='tit'>".__('下载地址','erphpdown').($k+1)."</span><a href='download.php?postid=".$postid."&key=".($k+1)."&index=".$index."' target='_blank' class='link'>".__('立即下载','erphpdown')."</a></p>";
					}elseif($arrlength == 2){
						$downMsg.="<p><span class='tit'>".$filearr[0]."</span><a href='download.php?postid=".$postid."&key=".($k+1)."&index=".$index."' target='_blank' class='link'>".__('立即下载','erphpdown')."</a></p>";
					}elseif($arrlength == 3){
						$filearr2 = str_replace('：', ': ', $filearr[2]);
						$filearr3 = explode(':',$filearr2);
						$arrlength = count($filearr3);
						if($arrlength >= 2){
							$downMsg.="<p><span class='tit'>".$filearr[0]."</span><a href='download.php?postid=".$postid."&key=".($k+1)."&index=".$index."' target='_blank' class='link erphpdown-down-btn' data-clipboard-text='".trim($filearr3[1])."'>".__('立即下载','erphpdown')."</a>".$filearr3[0].": <span class='erphpdown-code'>".trim($filearr3[1])."</span><a class='erphpdown-copy' data-clipboard-text='".trim($filearr3[1])."' href='javascript:;'>".__('复制','erphpdown')."</a></p>";
						}else{
							$downMsg.="<p><span class='tit'>".$filearr[0]."</span><a href='download.php?postid=".$postid."&key=".($k+1)."&index=".$index."' target='_blank' class='link erphpdown-down-btn' data-clipboard-text='".$filearr2."'>".__('立即下载','erphpdown')."</a><span class='erphpdown-code'>".$filearr2."</span><a class='erphpdown-copy' data-clipboard-text='".$filearr2."' href='javascript:;'>".__('复制','erphpdown')."</a></p>";
						}
					}
				}elseif(strpos($filepath,'  ') !== false && $erphp_blank_domain_is){
					$filearr = explode('  ',$filepath);
					$arrlength = count($filearr);
					if($arrlength == 1){
						$downMsg.="<p><span class='tit'>".__('下载地址','erphpdown').($k+1)."</span><a href='download.php?postid=".$postid."&key=".($k+1)."&index=".$index."' target='_blank' class='link'>".__('立即下载','erphpdown')."</a></p>";
					}elseif($arrlength >= 2){
						$filearr2 = explode(':',$filearr[0]);
						$filearr3 = explode(':',$filearr[1]);
						$downMsg.="<p><span class='tit'>".$filearr2[0]."</span><a href='download.php?postid=".$postid."&key=".($k+1)."&index=".$index."' target='_blank' class='link erphpdown-down-btn' data-clipboard-text='".trim($filearr3[1])."'>".__('立即下载','erphpdown')."</a>".__('提取码：','erphpdown')."<span class='erphpdown-code'>".trim($filearr3[1])."</span><a class='erphpdown-copy' data-clipboard-text='".trim($filearr3[1])."' href='javascript:;'>".__('复制','erphpdown')."</a></p>";
					}
				}elseif(strpos($filepath,' ') !== false && $erphp_blank_domain_is){
					$filearr = explode(' ',$filepath);
					$arrlength = count($filearr);
					if($arrlength == 1){
						$downMsg.="<p><span class='tit'>".__('下载地址','erphpdown').($k+1)."</span><a href='download.php?postid=".$postid."&key=".($k+1)."&index=".$index."' target='_blank' class='link'>".__('立即下载','erphpdown')."</a></p>";
					}elseif($arrlength == 2){
						$downMsg.="<p><span class='tit'>".$filearr[0]."</span><a href='download.php?postid=".$postid."&key=".($k+1)."&index=".$index."' target='_blank' class='link'>".__('立即下载','erphpdown')."</a></p>";
					}elseif($arrlength >= 3){
						$downMsg.="<p><span class='tit'>".str_replace(':', '', $filearr[0])."</span><a href='download.php?postid=".$postid."&key=".($k+1)."&index=".$index."' target='_blank' class='link erphpdown-down-btn' data-clipboard-text='".$filearr[3]."' >".__('立即下载','erphpdown')."</a>".$filearr[2].' <span class="erphpdown-code">'.$filearr[3]."</span><a class='erphpdown-copy' data-clipboard-text='".$filearr[3]."' href='javascript:;'>".__('复制','erphpdown')."</a></p>";
					}
				}else{
					$downMsg.="<p><span class='tit'>".__('下载地址','erphpdown').($k+1)."</span><a href='download.php?postid=".$postid."&key=".($k+1)."&index=".$index."' target='_blank' class='link'>".__('立即下载','erphpdown')."</a></p>";
				}
			}
		}
		$hiddens = get_post_meta($pp,'hidden_content',true);
		if($hiddens){
			$erphp_hide_style = get_option('erphp_hide_style');
			if($erphp_hide_style){
				$downMsg .='<div class="title hidden-title"><span>'.__('隐藏信息','erphpdown').'</span></div><div class="hidden-content">';
				$hiddens_arr1 = explode('，', $hiddens);
				foreach ($hiddens_arr1 as $hiddens1) {
					$hiddens_arr2 = explode('：', $hiddens1);
					if(count($hiddens_arr2) > 1){
						$downMsg .= '<div>'.$hiddens_arr2[0].'：<span class="erphpdown-code">'.trim($hiddens_arr2[1]).'</span><a class="erphpdown-copy" data-clipboard-text="'.trim($hiddens_arr2[1]).'" href="javascript:;" style="margin-left:10px;">'.__('复制','erphpdown').'</a></div>';
					}else{
						$downMsg .= '<div><span class="erphpdown-code">'.trim($hiddens1).'</span><a class="erphpdown-copy" data-clipboard-text="'.trim($hiddens1).'" href="javascript:;" style="margin-left:10px;">'.__('复制','erphpdown').'</a></div>';
					}
				}
				$downMsg .='</div>';
			}else{
				$downMsg .='<div class="title hidden-title"><span>'.__('隐藏信息','erphpdown').'</span></div><div class="hidden-content" style="margin-top:10px;">'.$hiddens.'<a class="erphpdown-copy" data-clipboard-text="'.$hiddens.'" href="javascript:;" style="margin-left:10px;">'.__('复制','erphpdown').'</a></div>';
			}
		}

		if($ice_data){
			$downMsg .='<div class="title hidden-title"><span>'.__('激活码','erphpdown').'</span></div><div class="hidden-content" style="margin-top:10px;">'.__('激活码/卡密：','erphpdown').$ice_data.'<a class="erphpdown-copy" data-clipboard-text="'.$ice_data.'" href="javascript:;" style="margin-left:10px;">'.__('复制','erphpdown').'</a></div>';
		}
		
		if(function_exists('MBThemes_erphpdown_download') && !$iframe){
			MBThemes_erphpdown_download($downMsg,$pp);
		}else{
			epd_download_page($downMsg,$pp);
		}
	}
}