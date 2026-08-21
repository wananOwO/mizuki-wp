<?php
// +----------------------------------------------------------------------
// | ERPHP [ PHP DEVELOP ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.mobantu.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: mobantu <82708210@qq.com>
// +----------------------------------------------------------------------

require_once('../../../wp-load.php');
date_default_timezone_set('Asia/Shanghai');

$erphp_wppay_download = get_option("erphp_wppay_download");
if($erphp_wppay_download && !is_user_logged_in()){
	wp_die(__('请先登录','erphpdown'),__("友情提示",'erphpdown'));
}

$start_down2='';
if(isset($_GET['id']) && $_GET['id']){
	$pid = esc_sql($_GET['id']);
	$postid = esc_sql($_GET['id']);
	$ppost = get_post($pid);
	if(!$ppost) wp_die(__('下载信息错误！','erphpdown'),__("友情提示",'erphpdown'));

	$start_down2 = get_post_meta($pid, 'start_down2',TRUE);
}

if(!is_user_logged_in() && !$start_down2 && !get_option('erphp_wppay_down') && !get_option('erphp_wppay_vip')){
	wp_die(__('请先登录','erphpdown'),__("友情提示",'erphpdown'));
}

$user_info=wp_get_current_user();
$filename=$_GET['filename'];
$md5key=$_GET['md5key'];
$times=$_GET['times'];
$session_name=$_GET['session_name'];
$index=isset($_GET['index']) ? esc_sql($_GET['index']) : '';
$index_vip = '';
$hasdown_info = 0;
$freedown = 0;
$timekey=isset($_GET['timekey']) ? $_GET['timekey'] :'';
if($timekey && $_GET['id'] && $timekey == md5($_GET['id'].get_option('erphpdown_downkey'))){
	$_COOKIE['erphpdown_wait_'.$_GET['id']] = 1;
    setcookie('erphpdown_wait_'.$_GET['id'],1,0,'/');
}

if(isset($_GET['id']) && $_GET['id']){
	$pid = esc_sql($_GET['id']);
	$postid = esc_sql($_GET['id']);
	$ppost = get_post($pid);
	if(!$ppost) wp_die(__('下载信息错误！','erphpdown'),__("友情提示",'erphpdown'));
	
	if($index){
		$urls = get_post_meta($pid, 'down_urls', true);
		if($urls){
			$cnt = count($urls['index']);
			if($cnt){
				for($i=0; $i<$cnt;$i++){
					if($urls['index'][$i] == $index){
    					$data = $urls['url'][$i];
    					$price = $urls['price'][$i];
    					$index_vip = $urls['vip'][$i];
    					break;
    				}
				}
			}
		}
	}else{
		$data = get_post_meta($pid, 'down_url', true);
		$price = get_post_meta($pid, 'down_price',true);
	}

	$memberDown=get_post_meta($pid, 'member_down',true);
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

	$days=get_post_meta($pid, 'down_days', true);
	$down_tuan=get_post_meta($pid, 'down_tuan', true);

	if(is_user_logged_in()){
		if($index){
			$hasdown_info=$wpdb->get_row("select * from ".$wpdb->icealipay." where ice_post='".$pid."' and ice_index='".$index."' and ice_success=1 and ice_user_id=".$user_info->ID." order by ice_time desc");
		}else{
			$hasdown_info=$wpdb->get_row("select * from ".$wpdb->icealipay." where ice_post='".$pid."' and ice_success=1 and (ice_index is null or ice_index = '') and ice_user_id=".$user_info->ID." order by ice_time desc");
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
	$wppay = new EPD($pid, $user_id);

	if($wppay->isWppayPaid() || $wppay->isWppayPaidNew($index)){
		$hasdown_info = 1;
	}


	$ews_erphpdown = get_option("ews_erphpdown");
	if($ews_erphpdown && function_exists("ews_erphpdown") && isset($_COOKIE['ewd_'.$postid]) && $_COOKIE['ewd_'.$postid] == md5($postid.get_option('erphpdown_downkey')) ){
		$hasdown_info = 1;
	}

	if($down_tuan){
		$yituan = $wpdb->get_var("select ice_id from $wpdb->tuanorder where ice_user_id=".$user_info->ID." and ice_post=".$pid." and ice_status=2");
		if($yituan){
			$hasdown_info = 1;
		}
	}

	$isDown = false;

	if($hasdown_info){
		$isDown=true;
	}
	elseif($user_info && $userType && ($memberDown ==3 || $memberDown ==4))
	{
		$isDown=true;
	}
	elseif($user_info && $userType >= 8 && ($memberDown ==15 || $memberDown ==16))
	{
		$isDown=true;
	}
	elseif($user_info && ($userType == 9 || $userType == 10) && ($memberDown ==6 || $memberDown ==8) )
	{
		$isDown=true;
	}
	elseif($user_info && $userType == 10 && ($memberDown ==7 || $memberDown ==9 || $memberDown == 13 || $memberDown == 14 || $memberDown == 20) )
	{
		$isDown=true;
	}
	else 
	{
		if( empty($price) || $price==0 )
		{
			if( ($memberDown ==4 && !$userType) || ($memberDown ==15 && $userType < 8) || ($memberDown ==8 && $userType < 9) || ($memberDown ==9 && $userType < 10) ){
				
			}else{
				$isDown=true;
				$freedown = 1;
			}
		}
	}

	if(!is_user_logged_in() && $freedown && get_option('erphp_free_login')){
		wp_die(__('请先登录','erphpdown'),__("友情提示",'erphpdown'));
	}

	if($isDown){
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
						if( checkDownLog($user_info->ID,$pid,$erphp_reg_times,0) ){

						}else{
							wp_die("普通用户每天只能下载".$erphp_reg_times."个免费资源！<a href='".get_option('erphp_url_front_vip')."'>升级VIP下载更多资源</a>",__("友情提示",'erphpdown'));
						}
					}
					addDownLog($user_info->ID,$pid,erphpGetIP(),0);
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
							addDownLog($user_info->ID,$pid,erphpGetIP(),1);
						}else{
							wp_die($erphp_day_name."用户每天只能免费下载".$erphp_day_times."个免费资源！<a href='".constant("erphpdown")."buy.php?postid=".$postid."&index=".$index."'>单独购买</a>",__("友情提示",'erphpdown'));
						}
					}elseif($userType == 7 && $erphp_month_times > 0 && $month_times_includes_free){
						if( checkDownLogNoVip($user_info->ID,$postid,$erphp_month_times) ){
							addDownLog($user_info->ID,$pid,erphpGetIP(),1);
						}else{
							wp_die($erphp_month_name."用户每天只能免费下载".$erphp_month_times."个免费资源！<a href='".constant("erphpdown")."buy.php?postid=".$postid."&index=".$index."'>单独购买</a>",__("友情提示",'erphpdown'));
						}
					}elseif($userType == 8 && $erphp_quarter_times > 0 && $quarter_times_includes_free){
						if( checkDownLogNoVip($user_info->ID,$postid,$erphp_quarter_times) ){
							addDownLog($user_info->ID,$pid,erphpGetIP(),1);
						}else{
							wp_die($erphp_quarter_name."用户每天只能免费下载".$erphp_quarter_times."个免费资源！<a href='".constant("erphpdown")."buy.php?postid=".$postid."&index=".$index."'>单独购买</a>",__("友情提示",'erphpdown'));
						}
					}elseif($userType == 9 && $erphp_year_times > 0 && $year_times_includes_free){
						if( checkDownLogNoVip($user_info->ID,$postid,$erphp_year_times) ){
							addDownLog($user_info->ID,$pid,erphpGetIP(),1);
						}else{
							wp_die($erphp_year_name."用户每天只能免费下载".$erphp_year_times."个免费资源！<a href='".constant("erphpdown")."buy.php?postid=".$postid."&index=".$index."'>单独购买</a>",__("友情提示",'erphpdown'));
						}
					}elseif($userType == 10 && $erphp_life_times > 0 && $life_times_includes_free){
						if( checkDownLogNoVip($user_info->ID,$postid,$erphp_life_times) ){
							addDownLog($user_info->ID,$pid,erphpGetIP(),1);
						}else{
							wp_die($erphp_life_name."用户每天只能免费下载".$erphp_life_times."个免费资源！<a href='".constant("erphpdown")."buy.php?postid=".$postid."&index=".$index."'>单独购买</a>",__("友情提示",'erphpdown'));
						}
					}
				}
				
			}else{
				if($memberDown == 3 || $memberDown == 4 || $memberDown == 15 || $memberDown == 16 || $memberDown == 6 || $memberDown == 7 || $memberDown == 8 || $memberDown == 9 || $memberDown == 13 || $memberDown == 14 || $memberDown == 20){
					
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

						addDownLog($user_info->ID,$pid,erphpGetIP(),1);
						
					}else{
						wp_die(__('下载信息错误！','erphpdown'),__("友情提示",'erphpdown'));
					}
				}else{
					wp_die(__('下载信息错误！','erphpdown'),__("友情提示",'erphpdown'));
				}
			}
		}
	}else{
		wp_die(__('下载信息错误！','erphpdown'),__("友情提示",'erphpdown'));
	}

	$g=(int)get_post_meta($pid,'down_times',true);
	if(!$g)$g=0;
	update_post_meta($pid,'down_times',$g+1);

	if(function_exists('_mbt_add_activity')){
		_mbt_add_activity($user_info->ID,'download','',$pid);
	}

}else{
	wp_die(__('下载信息错误！','erphpdown'),__("友情提示",'erphpdown'));
}

$erphp_free_wait = get_option('erphp_free_wait');
if($freedown && $erphp_free_wait && !(isset($_COOKIE['erphpdown_wait_'.$_GET['id']]) && $_COOKIE['erphpdown_wait_'.$_GET['id']]) && !getUsreMemberType()){
	epd_wait_page($_GET['id']);
}else{

	if(abs(time()-$times) < 100){
		if(is_user_logged_in()){
			$md5my=md5(md5($user_info->ID.'erphpdown'.$filename.$times.get_option('erphpdown_downkey')).$user_info->ID.'erphpdown'.$filename.$times.get_option('erphpdown_downkey').'mobantu');
		}else{
			$md5my=md5(md5('erphpdown'.$filename.$times.get_option('erphpdown_downkey')).'erphpdown'.$filename.$times.get_option('erphpdown_downkey').'mobantu');
		}

		$cypher = new ErphpCrypt(ErphpCrypt::CRYPT_MODE_HEXADECIMAL, ErphpCrypt::CRYPT_HASH_SHA1);
		$cypher->Key = get_option('erphpdown_downkey');
		$entemp = $cypher->decrypt($session_name);

		if($md5key==$md5my && $entemp == $times){
			$erphp_downurl_old = get_option('erphp_downurl_old');
			$erphp_downurl_new = get_option('erphp_downurl_new');
			if($erphp_downurl_old && $erphp_downurl_new){
				$data = str_replace($erphp_downurl_old, $erphp_downurl_new, $data);
			}
			$downList=explode("\r\n",trim($data));
			$file=trim($downList[$filename-1]);

			if(substr($file,0,7) == 'http://' || substr($file,0,8) == 'https://' || substr($file,0,10) == 'thunder://' || substr($file,0,7) == 'magnet:' || substr($file,0,5) == 'ed2k:' || substr($file,0,4) == 'ftp:'){
				$info=erphpdown_download_file($file);
			}else{

				$erphp_colon_domains = get_option('erphp_colon_domains')?get_option('erphp_colon_domains'):'pan.baidu.com';
				if($erphp_colon_domains){
					$erphp_colon_domains_arr = explode(',', $erphp_colon_domains);
					foreach ($erphp_colon_domains_arr as $erphp_colon_domain) {
						if(strpos($file, $erphp_colon_domain) !== false){
							$file = str_replace('：', ': ', $file);
							break;
						}
					}
				}

				$erphp_blank_domains = get_option('erphp_blank_domains')?get_option('erphp_blank_domains'):'pan.baidu.com';
				$erphp_blank_domain_is = 0;
				if($erphp_blank_domains){
					$erphp_blank_domains_arr = explode(',', $erphp_blank_domains);
					foreach ($erphp_blank_domains_arr as $erphp_blank_domain) {
						if(strpos($file, $erphp_blank_domain) !== false){
							$erphp_blank_domain_is = 1;
							break;
						}
					}
				}

				if(strpos($file,',') !== false){
					$filearr = explode(',',$file);
					$arrlength = count($filearr);
					if($arrlength == 1){
						$info=erphpdown_download_file(ABSPATH.'/'.$file);
					}elseif($arrlength >= 2){
						if(substr($filearr[1],0,7) == 'http://' || substr($filearr[1],0,8) == 'https://' || substr($filearr[1],0,10) == 'thunder://' || substr($filearr[1],0,7) == 'magnet:' || substr($filearr[1],0,5) == 'ed2k:' || substr($filearr[1],0,4) == 'ftp:'){
							$info=erphpdown_download_file($filearr[1]);
						}else{
							$info=erphpdown_download_file(ABSPATH.'/'.$filearr[1]);
						}
					}
				}elseif(strpos($file,'  ') !== false && $erphp_blank_domain_is){
					$filearr = explode('  ',$file);
					$arrlength = count($filearr);
					if($arrlength == 1){
						$info=erphpdown_download_file(ABSPATH.'/'.$file);
					}elseif($arrlength >= 2){
						$filearr2 = explode(':',$filearr[0]);
						$file2 = trim($filearr2[1].':'.$filearr2[2]);
						if(substr($file2,0,7) == 'http://' || substr($file2,0,8) == 'https://' || substr($file2,0,10) == 'thunder://' || substr($file2,0,7) == 'magnet:' || substr($file2,0,5) == 'ed2k:' || substr($file2,0,4) == 'ftp:'){
							$info=erphpdown_download_file($file2);
						}else{
							$info=erphpdown_download_file(ABSPATH.'/'.$file2);
						}
					}
				}elseif(strpos($file,' ') !== false && $erphp_blank_domain_is){
					$filearr = explode(' ',$file);
					$arrlength = count($filearr);
					if($arrlength == 1){
						$info=erphpdown_download_file(ABSPATH.'/'.$file);
					}elseif($arrlength >= 2){
						if(substr($filearr[1],0,7) == 'http://' || substr($filearr[1],0,8) == 'https://' || substr($filearr[1],0,10) == 'thunder://' || substr($filearr[1],0,7) == 'magnet:' || substr($filearr[1],0,5) == 'ed2k:' || substr($filearr[1],0,4) == 'ftp:'){
							$info=erphpdown_download_file($filearr[1]);
						}else{
							$info=erphpdown_download_file(ABSPATH.'/'.$filearr[1]);
						}
					}
				}else{
					$info=erphpdown_download_file(ABSPATH.'/'.$file);
				}

			}
			if(!$info)
			{
				wp_die(__('下载信息错误！','erphpdown'),__("友情提示",'erphpdown'));
			}
		}else{
			wp_die(__('下载信息错误！','erphpdown'),__("友情提示",'erphpdown'));
		}
	}
}
