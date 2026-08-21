<?php
// +----------------------------------------------------------------------
// | ERPHP [ PHP DEVELOP ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.mobantu.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: mobantu <82708210@qq.com>
// +----------------------------------------------------------------------
if ( !defined('ABSPATH') ) {exit;}

add_action('the_content','erphpdown_content_show', 10, 1);
function erphpdown_content_show($content){
	global $wpdb;
	$content2 = $content;
	$down_box_hide = get_post_meta(get_the_ID(), 'down_box_hide', true);
	if(!$down_box_hide){
		
		$erphp_post_types = get_option('erphp_post_types')?get_option('erphp_post_types'):array('post');
		if(is_singular() && in_array(get_post_type(),$erphp_post_types)){

			$erphp_see2_style = get_option('erphp_see2_style');
			$erphp_life_name    = get_option('erphp_life_name')?get_option('erphp_life_name'):'终身VIP';
			$erphp_year_name    = get_option('erphp_year_name')?get_option('erphp_year_name'):'包年VIP';
			$erphp_quarter_name = get_option('erphp_quarter_name')?get_option('erphp_quarter_name'):'包季VIP';
			$erphp_month_name  = get_option('erphp_month_name')?get_option('erphp_month_name'):'包月VIP';
			$erphp_day_name  = get_option('erphp_day_name')?get_option('erphp_day_name'):'体验VIP';
			$erphp_vip_name  = get_option('erphp_vip_name')?get_option('erphp_vip_name'):'VIP';

			$erphp_down=get_post_meta(get_the_ID(), 'erphp_down', true);
			$start_down=get_post_meta(get_the_ID(), 'start_down', true);
			$start_down2=get_post_meta(get_the_ID(), 'start_down2', true);
			$start_see=get_post_meta(get_the_ID(), 'start_see', true);
			$start_see2=get_post_meta(get_the_ID(), 'start_see2', true);
			$days=get_post_meta(get_the_ID(), 'down_days', true);
			$price=get_post_meta(get_the_ID(), 'down_price', true);
			$price_type=get_post_meta(get_the_ID(), 'down_price_type', true);
			$url=get_post_meta(get_the_ID(), 'down_url', true);
			$urls=get_post_meta(get_the_ID(), 'down_urls', true);
			$url_free=get_post_meta(get_the_ID(), 'down_url_free', true);
			$memberDown=get_post_meta(get_the_ID(), 'member_down',TRUE);
			$hidden=get_post_meta(get_the_ID(), 'hidden_content', true);

			$erphp_box_down_title = get_option('erphp_box_down_title');
			$erphp_box_see_title = get_option('erphp_box_see_title');
			$erphp_box_faka_title = get_option('erphp_box_faka_title');

			$erphp_vip_discounts = $erphp_vip_name.'折扣';

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

			$down_info = null;$downMsgFree = '';$down_checkpan = '';$yituan = '';$down_tuan=0;$erphp_popdown='';$iframe='';$down_repeat=0;$down_info_repeat = null;$down_can = 0;

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

			$erphp_wppay_vip = get_option('erphp_wppay_vip');

			if(get_option('erphp_popdown')){
				$erphp_popdown=' erphpdown-down-layui';
				$iframe = '&iframe=1';
			}

			if(is_user_logged_in()){
				$erphp_url_front_vip2 = $erphp_url_front_vip;
			}else{
				$erphp_url_front_vip2 = $erphp_url_front_login;
			}

			$erphp_blank_domains = get_option('erphp_blank_domains')?get_option('erphp_blank_domains'):'pan.baidu.com';
			$erphp_colon_domains = get_option('erphp_colon_domains')?get_option('erphp_colon_domains'):'pan.baidu.com';

			if($down_tuan && is_user_logged_in()){
				global $current_user;
				$yituan = $wpdb->get_var("select ice_status from $wpdb->tuanorder where ice_user_id=".$current_user->ID." and ice_post=".get_the_ID()." and ice_status>0");
			}

			if($url_free){
				$downMsgFree .= '<div class="erphpdown-title">免费资源</div><div class="erphpdown-free">';
				$downList=explode("\r\n",$url_free);
				foreach ($downList as $k=>$v){
					$filepath = $downList[$k];
					if($filepath){

						if($erphp_colon_domains){
							$erphp_colon_domains_arr = explode(',', $erphp_colon_domains);
							foreach ($erphp_colon_domains_arr as $erphp_colon_domain) {
								if(strpos($filepath, $erphp_colon_domain) !== false){
									$filepath = str_replace('：', ': ', $filepath);
									break;
								}
							}
						}

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
								$downMsgFree.="<div class='erphpdown-item'>文件".($k+1)."地址<a href='".$filepath."' rel='nofollow' target='_blank' class='erphpdown-down'>点击下载</a></div>";
							}elseif($arrlength == 2){
								$downMsgFree.="<div class='erphpdown-item'>".$filearr[0]."<a href='".$filearr[1]."' rel='nofollow' target='_blank' class='erphpdown-down'>点击下载</a></div>";
							}elseif($arrlength == 3){
								$filearr2 = str_replace('：', ': ', $filearr[2]);
								$downMsgFree.="<div class='erphpdown-item'>".$filearr[0]."<a href='".$filearr[1]."' rel='nofollow' target='_blank' class='erphpdown-down'>点击下载</a>".$filearr2."<a class='erphpdown-copy' data-clipboard-text='".str_replace('提取码: ', '', $filearr2)."' href='javascript:;'>复制</a></div>";
							}
						}elseif(strpos($filepath,'  ') !== false && $erphp_blank_domain_is){
							$filearr = explode('  ',$filepath);
							$arrlength = count($filearr);
							if($arrlength == 1){
								$downMsgFree.="<div class='erphpdown-item'>文件".($k+1)."地址<a href='".$filepath."' rel='nofollow' target='_blank' class='erphpdown-down'>点击下载</a></div>";
							}elseif($arrlength >= 2){
								$filearr2 = explode(':',$filearr[0]);
								$filearr3 = explode(':',$filearr[1]);
								$downMsgFree.="<div class='erphpdown-item'>".$filearr2[0]."<a href='".trim($filearr2[1].':'.$filearr2[2])."' target='_blank' rel='nofollow' class='erphpdown-down'>点击下载</a>提取码: ".trim($filearr3[1])."<a class='erphpdown-copy' data-clipboard-text='".trim($filearr3[1])."' href='javascript:;'>复制</a></div>";
							}
						}elseif(strpos($filepath,' ') !== false && $erphp_blank_domain_is){
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
				$downMsg = '';
				if($url){
					if(function_exists('epd_check_pan_callback')){
						if(strpos($url,'pan.baidu.com') !== false || (strpos($url,'lanzou') !== false && strpos($url,'.com') !== false) || strpos($url,'cloud.189.cn') !== false){
							$down_checkpan = '<a class="erphpdown-buy erphpdown-checkpan2" href="javascript:;" data-id="'.get_the_ID().'" data-post="'.get_the_ID().'">点击检测网盘有效后购买</a>';
						}
					}

					$content.='<fieldset class="erphpdown erphpdown-default" id="erphpdown"><legend>'.($erphp_box_down_title?$erphp_box_down_title:'资源下载').'</legend>'.$downMsgFree;
					
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
										if(strpos($filepath, $erphp_colon_domain) !== false){
											$filepath = str_replace('：', ': ', $filepath);
											break;
										}
									}
								}

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
										$downMsg.="<div class='erphpdown-item'>文件".($k+1)."地址<a href='".ERPHPDOWN_URL."/download.php?postid=".get_the_ID()."&key=".($k+1)."&nologin=1&timestamp=".time()."' target='_blank' class='erphpdown-down'>点击下载</a></div>";
									}elseif($arrlength == 2){
										$downMsg.="<div class='erphpdown-item'>".$filearr[0]."<a href='".ERPHPDOWN_URL."/download.php?postid=".get_the_ID()."&key=".($k+1)."&nologin=1&timestamp=".time()."' target='_blank' class='erphpdown-down'>点击下载</a></div>";
									}elseif($arrlength == 3){
										$filearr2 = str_replace('：', ': ', $filearr[2]);
										$downMsg.="<div class='erphpdown-item'>".$filearr[0]."<a href='".ERPHPDOWN_URL."/download.php?postid=".get_the_ID()."&key=".($k+1)."&nologin=1&timestamp=".time()."' target='_blank' class='erphpdown-down'>点击下载</a>（".$filearr2."）<a class='erphpdown-copy' data-clipboard-text='".str_replace('提取码: ', '', $filearr2)."' href='javascript:;'>复制</a></div>";
									}
								}elseif(strpos($filepath,'  ') !== false && $erphp_blank_domain_is){
									$filearr = explode('  ',$filepath);
									$arrlength = count($filearr);
									if($arrlength == 1){
										$downMsg.="<div class='erphpdown-item'>文件".($k+1)."地址<a href='".ERPHPDOWN_URL."/download.php?postid=".get_the_ID()."&key=".($k+1)."&nologin=1&timestamp=".time()."' target='_blank' class='erphpdown-down'>点击下载</a></div>";
									}elseif($arrlength >= 2){
										$filearr2 = explode(':',$filearr[0]);
										$filearr3 = explode(':',$filearr[1]);
										$downMsg.="<div class='erphpdown-item'>".$filearr2[0]."<a href='".ERPHPDOWN_URL."/download.php?postid=".get_the_ID()."&key=".($k+1)."&nologin=1&timestamp=".time()."' target='_blank' class='erphpdown-down'>点击下载</a>（提取码: ".trim($filearr3[1])."）<a class='erphpdown-copy' data-clipboard-text='".trim($filearr3[1])."' href='javascript:;'>复制</a></div>";
									}
								}elseif(strpos($filepath,' ') !== false && $erphp_blank_domain_is){
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
									$downMsg.="<div class='erphpdown-item'>文件".($k+1)."地址<a href='".ERPHPDOWN_URL."/download.php?postid=".get_the_ID()."&key=".($k+1)."&nologin=1&timestamp=".time()."' target='_blank' class='erphpdown-down'>点击下载</a></div>";
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

							if($down_checkpan) $content .= $tname.'价格<span class="erphpdown-price">'.$price.'</span>元'.$down_checkpan.'<span class="epd-or">或</span>升级'.$wppay_vip_name.'后免费<a href="'.$erphp_url_front_vip2.'" target="_blank" class="erphpdown-vip'.(is_user_logged_in()?'':' erphp-login-must').'">升级'.$wppay_vip_name.'</a>';
							else $content .= $tname.'价格<span class="erphpdown-price">'.$price.'</span>元<a href="javascript:;" class="erphp-wppay-loader erphpdown-buy" data-post="'.get_the_ID().'">立即购买</a><span class="epd-or">或</span>升级'.$wppay_vip_name.'后免费<a href="'.$erphp_url_front_vip2.'" target="_blank" class="erphpdown-vip'.(is_user_logged_in()?'':' erphp-login-must').'">升级'.$wppay_vip_name.'</a>';
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
				$content.='<fieldset class="erphpdown erphpdown-default" id="erphpdown"><legend>'.($erphp_box_down_title?$erphp_box_down_title:'资源下载').'</legend>'.$downMsgFree;
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

	            					$content .= '<fieldset class="erphpdown-child"><legend>'.$index_name.'</legend>';

	            					$down_checkpan = '';
	            					if(function_exists('epd_check_pan_callback')){
										if(strpos($index_url,'pan.baidu.com') !== false || (strpos($index_url,'lanzou') !== false && strpos($index_url,'.com') !== false) || strpos($index_url,'cloud.189.cn') !== false){
											$down_checkpan = '<a class="erphpdown-buy erphpdown-checkpan" href="javascript:;" data-id="'.get_the_ID().'" data-index="'.$index.'" data-buy="'.constant("erphpdown").'buy.php?postid='.get_the_ID().'&timestamp='.time().'&index='.$index.'">点击检测网盘有效后购买</a>';
										}
									}

	            					if(is_user_logged_in() || get_option('erphp_wppay_down') || ( ($userType && ($indexMemberDown==3 || $indexMemberDown==4)) || (($indexMemberDown==15 || $indexMemberDown==16) && $userType >= 8) || (($indexMemberDown==6 || $indexMemberDown==8) && $userType >= 9) || (($indexMemberDown==7 || $indexMemberDown==9 || $indexMemberDown==13 || $indexMemberDown==14 || $indexMemberDown==20) && $userType == 10) )){
										if($price){
											if($indexMemberDown != 4 && $indexMemberDown != 15 && $indexMemberDown != 8 && $indexMemberDown != 9)
												$content.='此资源下载价格为<span class="erphpdown-price">'.$price.'</span>'.get_option("ice_name_alipay");
										}else{
											if($indexMemberDown != 4 && $indexMemberDown != 15 && $indexMemberDown != 8 && $indexMemberDown != 9)
												$content.='此资源为免费资源';
										}

										if($price || $indexMemberDown == 4 || $indexMemberDown == 15 || $indexMemberDown == 8 || $indexMemberDown == 9){
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
												}elseif($indexMemberDown==16){
													$content .= '（'.$erphp_quarter_name.'免费）';
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
												}elseif($indexMemberDown==10){
													$content .= '（仅限'.$erphp_vip_name.'购买）';
												}elseif($indexMemberDown==17){
													$content .= '（仅限'.$erphp_quarter_name.'购买）';
												}elseif($indexMemberDown==18){
													$content .= '（仅限'.$erphp_year_name.'购买）';
												}elseif($indexMemberDown==19){
													$content .= '（仅限'.$erphp_life_name.'购买）';
												}elseif($indexMemberDown==11){
													$content .= '（仅限'.$erphp_vip_name.'购买、'.$erphp_year_name.' 5折）';
												}elseif($indexMemberDown==12){
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
													$content.='（'.$erphp_vip_name.' 8折'.$vipText.'）';
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
														else $content.='<a class="erphpdown-iframe erphpdown-buy" href='.constant("erphpdown").'buy.php?postid='.get_the_ID().'&timestamp='.time().'&index='.$index.' target="_blank">'.$buyText.'</a>';

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
														else $content.='<a class="erphpdown-iframe erphpdown-buy" href='.constant("erphpdown").'buy.php?postid='.get_the_ID().'&timestamp='.time().'&index='.$index.' target="_blank">'.$buyText.'</a>';

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
														else $content.='<a class="erphpdown-iframe erphpdown-buy" href='.constant("erphpdown").'buy.php?postid='.get_the_ID().'&timestamp='.time().'&index='.$index.' target="_blank">'.$buyText.'</a>';

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
														else $content.='<a class="erphpdown-iframe erphpdown-buy" href='.constant("erphpdown").'buy.php?postid='.get_the_ID().'&timestamp='.time().'&index='.$index.' target="_blank">'.$buyText.'</a>';

														if($days){
															$content.= '（购买后'.$days.'天内可下载）';
														}
													}else{
														$content.='（仅限'.$erphp_life_name.'购买）<a href="'.$erphp_url_front_vip.'" class="erphpdown-vip">升级'.$erphp_life_name.'</a>';
													}
												}elseif($indexMemberDown==11){
													if($userType){
														$content.='（仅限'.$erphp_vip_name.'购买、'.$erphp_year_name.' 5折）';
														if($down_checkpan) $content .= $down_checkpan;
														else $content.='<a class="erphpdown-iframe erphpdown-buy" href='.constant("erphpdown").'buy.php?postid='.get_the_ID().'&timestamp='.time().'&index='.$index.' target="_blank">'.$buyText.'</a>';

														if($days){
															$content.= '（购买后'.$days.'天内可下载）';
														}
													}else{
														$content.='（仅限'.$erphp_vip_name.'购买、'.$erphp_year_name.' 5折）<a href="'.$erphp_url_front_vip.'" class="erphpdown-vip">升级'.$erphp_vip_name.'</a>';
													}
												}elseif($indexMemberDown==12){
													if($userType){
														$content.='（仅限'.$erphp_vip_name.'购买、'.$erphp_year_name.' 8折）';
														if($down_checkpan) $content .= $down_checkpan;
														else $content.='<a class="erphpdown-iframe erphpdown-buy" href='.constant("erphpdown").'buy.php?postid='.get_the_ID().'&timestamp='.time().'&index='.$index.' target="_blank">'.$buyText.'</a>';

														if($days){
															$content.= '（购买后'.$days.'天内可下载）';
														}
													}else{
														$content.='（仅限'.$erphp_vip_name.'购买、'.$erphp_year_name.' 8折）<a href="'.$erphp_url_front_vip.'" class="erphpdown-vip">升级'.$erphp_vip_name.'</a>';
													}
												}else{
													if($down_checkpan) $content .= $down_checkpan;
													else $content.='<a class="erphpdown-iframe erphpdown-buy" href="'.constant("erphpdown").'buy.php?postid='.get_the_ID().'&timestamp='.time().'&index='.$index.'" target="_blank">'.$buyText.'</a>';

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
												$content.='此资源下载价格为<span class="erphpdown-price">'.$price.'</span>'.get_option('ice_name_alipay').'，请先<a href="'.$erphp_url_front_login.'" class="erphp-login-must">登录</a>';
											}else{
												$content.='此资源为免费资源，请先<a href="'.$erphp_url_front_login.'" class="erphp-login-must">登录</a>';
											}
										}
									}
									if(get_option('erphp_repeatdown_btn') && $down_repeat && $down_info_repeat && !$down_info){
										$content.='<a href="'.constant("erphpdown").'download.php?postid='.get_the_ID().'&index='.$index.$iframe.'&timestamp='.time().'" class="erphpdown-down'.$erphp_popdown.'" target="_blank">立即下载</a>';
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
								$down_checkpan = '<a class="erphpdown-buy erphpdown-checkpan" href="javascript:;" data-id="'.get_the_ID().'" data-index="0" data-buy="'.constant("erphpdown").'buy.php?postid='.get_the_ID().'&timestamp='.time().'">点击检测网盘有效后购买</a>';
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
									}elseif($memberDown==20){
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
									}elseif ($memberDown==15){
										if($userType >= 9){
											$content.='此资源为'.$erphp_quarter_name.'专享资源';
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
											else $content.='<a class="erphpdown-iframe erphpdown-buy" href="'.constant("erphpdown").'buy.php?postid='.get_the_ID().'&timestamp='.time().'" target="_blank">'.$buyText.'</a>';

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
											else $content.='<a class="erphpdown-iframe erphpdown-buy" href="'.constant("erphpdown").'buy.php?postid='.get_the_ID().'&timestamp='.time().'" target="_blank">'.$buyText.'</a>';

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
											else $content.='<a class="erphpdown-iframe erphpdown-buy" href="'.constant("erphpdown").'buy.php?postid='.get_the_ID().'&timestamp='.time().'" target="_blank">'.$buyText.'</a>';

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
											else $content.='<a class="erphpdown-iframe erphpdown-buy" href="'.constant("erphpdown").'buy.php?postid='.get_the_ID().'&timestamp='.time().'" target="_blank">'.$buyText.'</a>';

											if($days){
												$content.= '（购买后'.$days.'天内可下载）';
											}
										}else{
											$content.='（仅限'.$erphp_life_name.'购买）<a href="'.$erphp_url_front_vip.'" class="erphpdown-vip">升级'.$erphp_life_name.'</a>';
										}
									}elseif($memberDown==11){
										if($userType){
											$content.='（仅限'.$erphp_vip_name.'购买、'.$erphp_year_name.' 5折）';
											if($down_checkpan) $content .= $down_checkpan;
											else $content.='<a class="erphpdown-iframe erphpdown-buy" href="'.constant("erphpdown").'buy.php?postid='.get_the_ID().'&timestamp='.time().'" target="_blank">'.$buyText.'</a>';

											if($days){
												$content.= '（购买后'.$days.'天内可下载）';
											}
										}else{
											$content.='（仅限'.$erphp_vip_name.'购买、'.$erphp_year_name.' 5折）<a href="'.$erphp_url_front_vip.'" class="erphpdown-vip">升级'.$erphp_vip_name.'</a>';
										}
									}elseif($memberDown==12){
										if($userType){
											$content.='（仅限'.$erphp_vip_name.'购买、'.$erphp_year_name.' 8折）';
											if($down_checkpan) $content .= $down_checkpan;
											else $content.='<a class="erphpdown-iframe erphpdown-buy" href="'.constant("erphpdown").'buy.php?postid='.get_the_ID().'&timestamp='.time().'" target="_blank">'.$buyText.'</a>';

											if($days){
												$content.= '（购买后'.$days.'天内可下载）';
											}
										}else{
											$content.='（仅限'.$erphp_vip_name.'购买、'.$erphp_year_name.' 8折）<a href="'.$erphp_url_front_vip.'" class="erphpdown-vip">升级'.$erphp_vip_name.'</a>';
										}
									}else{
										
										if($down_checkpan) $content .= $down_checkpan;
										else $content.='<a class="erphpdown-iframe erphpdown-buy" href="'.constant("erphpdown").'buy.php?postid='.get_the_ID().'&timestamp='.time().'" target="_blank">'.$buyText.'</a>';

										if($days){
											$content.= '（购买后'.$days.'天内可下载）';
										}
									}
								}
								
							}else{
								$down_can = 1;
								$content.="<a href='".constant("erphpdown").'download.php?postid='.get_the_ID().$iframe."&timestamp=".time()."' class='erphpdown-down".$erphp_popdown."' target='_blank'>立即下载</a>";
							}
							
						}else{
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
									$user_id = 0;
									$wppay = new EPD(get_the_ID(), $user_id);
									if($wppay->isWppayPaid() || $wppay->isWppayPaidNew()){
										$down_can = 1;
										if($price){
											$content.='此资源下载价格为<span class="erphpdown-price">'.$price.'</span>'.get_option("ice_name_alipay");
										}
										$content.="<a href='".constant("erphpdown").'download.php?postid='.get_the_ID().$iframe."&timestamp=".time()."' class='erphpdown-down".$erphp_popdown."' target='_blank'>立即下载</a>";
									}else{
										if($price){
											$content.='此资源下载价格为<span class="erphpdown-price">'.$price.'</span>'.get_option('ice_name_alipay');

											if($down_checkpan) $content .= $down_checkpan;
											else $content.='<a class="erphpdown-iframe erphpdown-buy" href="'.constant("erphpdown").'buy.php?postid='.get_the_ID().'&timestamp='.time().'" target="_blank">立即购买</a>';

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
										$content.='此资源下载价格为<span class="erphpdown-price">'.$price.'</span>'.get_option('ice_name_alipay').$vip_content.'，请先<a href="'.$erphp_url_front_login.'" class="erphp-login-must">登录</a>';
									}else{
										$content.='此资源仅限注册用户下载，请先<a href="'.$erphp_url_front_login.'" class="erphp-login-must">登录</a>';
									}
									
								}
							}
						}

						if(get_option('erphp_repeatdown_btn') && $down_repeat && $down_info_repeat && !$down_info){
							$down_can = 1;
							$content.='<a href="'.constant("erphpdown").'download.php?postid='.get_the_ID().$iframe.'&timestamp='.time().'" class="erphpdown-down'.$erphp_popdown.'" target="_blank">立即下载</a>';
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
			}elseif($start_see){
				
				if(is_user_logged_in() || ( ($userType && ($memberDown==3 || $memberDown==4)) || (($memberDown==15 || $memberDown==16) && $userType >= 8) || (($memberDown==6 || $memberDown==8) && $userType >= 9) || (($memberDown==7 || $memberDown==9 || $memberDown==13 || $memberDown==14 || $memberDown==20) && $userType == 10) )){
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
					}

					$user_id = $user_info->ID;
					$wppay = new EPD(get_the_ID(), $user_id);

					if( ($userType && ($memberDown==3 || $memberDown==4)) || $wppay->isWppayPaid() || $wppay->isWppayPaidNew() || $down_info || (($memberDown==15 || $memberDown==16) && $userType >= 8) || (($memberDown==6 || $memberDown==8) && $userType >= 9) || (($memberDown==7 || $memberDown==9 || $memberDown==13 || $memberDown==14 || $memberDown==20) && $userType == 10) || (!$price && $memberDown!=4 && $memberDown!=15 && $memberDown!=8 && $memberDown!=9)){
						return $content;
					}else{
					
						$content2='<fieldset class="erphpdown erphpdown-default erphpdown-see" id="erphpdown"><legend>'.($erphp_box_see_title?$erphp_box_see_title:'内容查看').'</legend>';
						if($price){
							if($memberDown != 4 && $memberDown != 15 && $memberDown != 8 && $memberDown != 9){
								$content2.='此内容查看价格为<span class="erphpdown-price">'.$price.'</span>'.get_option('ice_name_alipay');
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
							$content2.='（'.$erphp_vip_name.'免费）'.$vipText;
						}elseif ($memberDown==2){
							$content2.='（'.$erphp_vip_name.' 5折）'.$vipText;
						}elseif ($memberDown==13){
							$content2.='（'.$erphp_vip_name.' 5折、'.$erphp_life_name.'免费）'.$vipText;
						}elseif ($memberDown==5){
							$content2.='（'.$erphp_vip_name.' 8折）'.$vipText;
						}elseif ($memberDown==14){
							$content2.='（'.$erphp_vip_name.' 8折、'.$erphp_life_name.'免费）'.$vipText;
						}elseif ($memberDown==20){
							$content2.='（'.$erphp_vip_discounts.'、'.$erphp_life_name.'免费）'.$vipText;
						}elseif ($memberDown==16){
							if($userType < 8){
								$vipText = '<a href="'.$erphp_url_front_vip.'" target="_blank" class="erphpdown-vip">升级'.$erphp_quarter_name.'</a>';
							}
							$content2.='（'.$erphp_quarter_name.'免费）'.$vipText;
						}elseif ($memberDown==6){
							if($userType < 9){
								$vipText = '<a href="'.$erphp_url_front_vip.'" target="_blank" class="erphpdown-vip">升级'.$erphp_year_name.'</a>';
							}
							$content2.='（'.$erphp_year_name.'免费）'.$vipText;
						}elseif ($memberDown==7){
							if($userType < 10){
								$vipText = '<a href="'.$erphp_url_front_vip.'" target="_blank" class="erphpdown-vip">升级'.$erphp_life_name.'</a>';
							}
							$content2.='（'.$erphp_life_name.'免费）'.$vipText;
						}
						
						if($memberDown==4)
						{
							$content2.='此内容仅限'.$erphp_vip_name.'查看<a href="'.$erphp_url_front_vip.'" target="_blank" class="erphpdown-vip">升级'.$erphp_vip_name.'</a>';
						}elseif($memberDown==15)
						{
							$content2.='此内容仅限'.$erphp_quarter_name.'查看<a href="'.$erphp_url_front_vip.'" target="_blank" class="erphpdown-vip">升级'.$erphp_quarter_name.'</a>';
						}elseif($memberDown==8)
						{
							$content2.='此内容仅限'.$erphp_year_name.'查看<a href="'.$erphp_url_front_vip.'" target="_blank" class="erphpdown-vip">升级'.$erphp_year_name.'</a>';
						}elseif($memberDown==9)
						{
							$content2.='此内容仅限'.$erphp_life_name.'查看<a href="'.$erphp_url_front_vip.'" target="_blank" class="erphpdown-vip">升级'.$erphp_life_name.'</a>';
						}elseif($memberDown==10){
							if($userType){
								$content2.='（仅限'.$erphp_vip_name.'购买）<a class="erphpdown-iframe erphpdown-buy" href="'.constant("erphpdown").'buy.php?postid='.get_the_ID().'&timestamp='.time().'" target="_blank">立即购买</a>';

								if($days){
									$content2.= '（购买后'.$days.'天内可查看）';
								}
							}else{
								$content2.='（仅限'.$erphp_vip_name.'购买）<a href="'.$erphp_url_front_vip.'" class="erphpdown-vip">升级'.$erphp_vip_name.'</a>';
							}
						}elseif($memberDown==17){
							if($userType >= 8){
								$content2.='（仅限'.$erphp_quarter_name.'购买）<a class="erphpdown-iframe erphpdown-buy" href="'.constant("erphpdown").'buy.php?postid='.get_the_ID().'&timestamp='.time().'" target="_blank">立即购买</a>';

								if($days){
									$content2.= '（购买后'.$days.'天内可查看）';
								}
							}else{
								$content2.='（仅限'.$erphp_quarter_name.'购买）<a href="'.$erphp_url_front_vip.'" class="erphpdown-vip">升级'.$erphp_quarter_name.'</a>';
							}
						}elseif($memberDown==18){
							if($userType >= 9){
								$content2.='（仅限'.$erphp_year_name.'购买）<a class="erphpdown-iframe erphpdown-buy" href="'.constant("erphpdown").'buy.php?postid='.get_the_ID().'&timestamp='.time().'" target="_blank">立即购买</a>';

								if($days){
									$content2.= '（购买后'.$days.'天内可查看）';
								}
							}else{
								$content2.='（仅限'.$erphp_year_name.'购买）<a href="'.$erphp_url_front_vip.'" class="erphpdown-vip">升级'.$erphp_year_name.'</a>';
							}
						}elseif($memberDown==19){
							if($userType == 10){
								$content2.='（仅限'.$erphp_life_name.'购买）<a class="erphpdown-iframe erphpdown-buy" href="'.constant("erphpdown").'buy.php?postid='.get_the_ID().'&timestamp='.time().'" target="_blank">立即购买</a>';

								if($days){
									$content2.= '（购买后'.$days.'天内可查看）';
								}
							}else{
								$content2.='（仅限'.$erphp_life_name.'购买）<a href="'.$erphp_url_front_vip.'" class="erphpdown-vip">升级'.$erphp_life_name.'</a>';
							}
						}elseif($memberDown==11){
							if($userType){
								$content2.='（仅限'.$erphp_vip_name.'购买，'.$erphp_year_name.' 5折）<a class="erphpdown-iframe erphpdown-buy" href="'.constant("erphpdown").'buy.php?postid='.get_the_ID().'&timestamp='.time().'" target="_blank">立即购买</a>';

								if($days){
									$content2.= '（购买后'.$days.'天内可查看）';
								}
							}else{
								$content2.='（仅限'.$erphp_vip_name.'购买，'.$erphp_year_name.' 5折）<a href="'.$erphp_url_front_vip.'" class="erphpdown-vip">升级'.$erphp_vip_name.'</a>';
							}
						}elseif($memberDown==12){
							if($userType){
								$content2.='（仅限'.$erphp_vip_name.'购买，'.$erphp_year_name.' 8折）<a class="erphpdown-iframe erphpdown-buy" href="'.constant("erphpdown").'buy.php?postid='.get_the_ID().'&timestamp='.time().'" target="_blank">立即购买</a>';

								if($days){
									$content2.= '（购买后'.$days.'天内可查看）';
								}
							}else{
								$content2.='（仅限'.$erphp_vip_name.'购买，'.$erphp_year_name.' 8折）<a href="'.$erphp_url_front_vip.'" class="erphpdown-vip">升级'.$erphp_vip_name.'</a>';
							}
						}else{
							$content2.='<a class="erphpdown-iframe erphpdown-buy" href="'.constant("erphpdown").'buy.php?postid='.get_the_ID().'&timestamp='.time().'">立即购买</a>';

							if($days){
								$content2.= '（购买后'.$days.'天内可查看）';
							}
						}
					}

				}else{
					$content2='<fieldset class="erphpdown erphpdown-default erphpdown-see" id="erphpdown"><legend>'.($erphp_box_see_title?$erphp_box_see_title:'内容查看').'</legend>';

					if($memberDown == 4){
						$content2.='此内容仅限'.$erphp_vip_name.'查看，请先<a href="'.$erphp_url_front_login.'" class="erphp-login-must">登录</a>';
					}elseif($memberDown == 15){
						$content2.='此内容仅限'.$erphp_quarter_name.'查看，请先<a href="'.$erphp_url_front_login.'" class="erphp-login-must">登录</a>';
					}elseif($memberDown == 8){
						$content2.='此内容仅限'.$erphp_year_name.'查看，请先<a href="'.$erphp_url_front_login.'" class="erphp-login-must">登录</a>';
					}elseif($memberDown == 9){
						$content2.='此内容仅限'.$erphp_life_name.'查看，请先<a href="'.$erphp_url_front_login.'" class="erphp-login-must">登录</a>';
					}elseif($memberDown == 10){
						$content2.='此内容仅限'.$erphp_vip_name.'购买，请先<a href="'.$erphp_url_front_login.'" class="erphp-login-must">登录</a>';
					}elseif($memberDown == 17){
						$content2.='此内容仅限'.$erphp_quarter_name.'购买，请先<a href="'.$erphp_url_front_login.'" class="erphp-login-must">登录</a>';
					}elseif($memberDown == 18){
						$content2.='此内容仅限'.$erphp_year_name.'购买，请先<a href="'.$erphp_url_front_login.'" class="erphp-login-must">登录</a>';
					}elseif($memberDown == 19){
						$content2.='此内容仅限'.$erphp_life_name.'购买，请先<a href="'.$erphp_url_front_login.'" class="erphp-login-must">登录</a>';
					}elseif($memberDown == 11){
						$content2.='此内容仅限'.$erphp_vip_name.'购买、'.$erphp_year_name.' 5折，请先<a href="'.$erphp_url_front_login.'" class="erphp-login-must">登录</a>';
					}elseif($memberDown == 12){
						$content2.='此内容仅限'.$erphp_vip_name.'购买、'.$erphp_year_name.' 8折，请先<a href="'.$erphp_url_front_login.'" class="erphp-login-must">登录</a>';
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
								return $content;
							}else{
								if($price){
									$content2.='此内容查看价格为<span class="erphpdown-price">'.$price.'</span>'.get_option('ice_name_alipay');
									$content2.='<a class="erphpdown-iframe erphpdown-buy" href="'.constant("erphpdown").'buy.php?postid='.get_the_ID().'&timestamp='.time().'" target="_blank">立即购买</a>';

									$content2 .= $vip_content?($vip_content.'<a href="'.$erphp_url_front_login.'" target="_blank" class="erphpdown-vip erphp-login-must">立即升级</a>'):'';
								}else{
									if(!get_option('erphp_free_login')){
										return $content;
									}else{
										$content2.='此内容仅限注册用户查看，请先<a href="'.$erphp_url_front_login.'" class="erphp-login-must">登录</a>';
									}
								}
							}
						}else{
							if($price){
								$content2.='此内容查看价格为<span class="erphpdown-price">'.$price.'</span>'.get_option('ice_name_alipay').$vip_content.'，请先<a href="'.$erphp_url_front_login.'" target="_blank" class="erphp-login-must">登录</a>';
							}else{
								$content2.='此内容仅限注册用户查看，请先<a href="'.$erphp_url_front_login.'" class="erphp-login-must">登录</a>';
							}
							
						}
					}
					
				}
				if(get_option('ice_tips')) $content2.='<div class="erphpdown-tips">'.get_option('ice_tips').'</div>';
				$content2.='</fieldset>';
				return $content2;
				
			}elseif($start_see2 && $erphp_see2_style){
				
				if(is_user_logged_in() || ( ($userType && ($memberDown==3 || $memberDown==4)) || (($memberDown==15 || $memberDown==16) && $userType >= 8) || (($memberDown==6 || $memberDown==8) && $userType >= 9) || (($memberDown==7 || $memberDown==9 || $memberDown==13 || $memberDown==14 || $memberDown==20) && $userType == 10) )){
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
					}

					if( ($userType && ($memberDown==3 || $memberDown==4)) || $down_info || (($memberDown==15 || $memberDown==16) && $userType >= 8) || (($memberDown==6 || $memberDown==8) && $userType >= 9) || (($memberDown==7 || $memberDown==9 || $memberDown==13 || $memberDown==14 || $memberDown==20) && $userType == 10) || (!$price && $memberDown!=4 && $memberDown!=15 && $memberDown!=8 && $memberDown!=9)){
						
					}else{
					
						$content.='<fieldset class="erphpdown erphpdown-default erphpdown-see" id="erphpdown"><legend>'.($erphp_box_see_title?$erphp_box_see_title:'内容查看').'</legend>';
						if($price){
							if($memberDown != 4 && $memberDown != 15 && $memberDown != 8 && $memberDown != 9)
								$content.='本文隐藏内容查看价格为<span class="erphpdown-price">'.$price.'</span>'.get_option('ice_name_alipay');
						}
						

						$vipText = '<a href="'.$erphp_url_front_vip.'" target="_blank" class="erphpdown-vip">升级'.$erphp_vip_name.'</a>';
						if($userType){
							$vipText = '';
							if(($memberDown == 13 || $memberDown == 14) && $userType < 10){
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
						
						if($memberDown==4)
						{
							$content.='本文隐藏内容仅限'.$erphp_vip_name.'查看<a href="'.$erphp_url_front_vip.'" target="_blank" class="erphpdown-vip">升级'.$erphp_vip_name.'</a>';
						}elseif($memberDown==15)
						{
							$content.='本文隐藏内容仅限'.$erphp_quarter_name.'查看<a href="'.$erphp_url_front_vip.'" target="_blank" class="erphpdown-vip">升级'.$erphp_quarter_name.'</a>';
						}elseif($memberDown==8)
						{
							$content.='本文隐藏内容仅限'.$erphp_year_name.'查看<a href="'.$erphp_url_front_vip.'" target="_blank" class="erphpdown-vip">升级'.$erphp_year_name.'</a>';
						}elseif($memberDown==9)
						{
							$content.='本文隐藏内容仅限'.$erphp_life_name.'查看<a href="'.$erphp_url_front_vip.'" target="_blank" class="erphpdown-vip">升级'.$erphp_life_name.'</a>';
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
								$content.='（仅限'.$erphp_vip_name.'购买，'.$erphp_year_name.' 5折）<a class="erphpdown-iframe erphpdown-buy" href="'.constant("erphpdown").'buy.php?postid='.get_the_ID().'&timestamp='.time().'" target="_blank">立即购买</a>';
								if($days){
									$content.= '（购买后'.$days.'天内可查看）';
								}
							}else{
								$content.='（仅限'.$erphp_vip_name.'购买，'.$erphp_year_name.' 5折）<a href="'.$erphp_url_front_vip.'" class="erphpdown-vip">升级'.$erphp_vip_name.'</a>';
							}
						}elseif($memberDown==12){
							if($userType){
								$content.='（仅限'.$erphp_vip_name.'购买，'.$erphp_year_name.' 8折）<a class="erphpdown-iframe erphpdown-buy" href="'.constant("erphpdown").'buy.php?postid='.get_the_ID().'&timestamp='.time().'" target="_blank">立即购买</a>';

								if($days){
									$content.= '（购买后'.$days.'天内可查看）';
								}
							}else{
								$content.='（仅限'.$erphp_vip_name.'购买，'.$erphp_year_name.' 8折）<a href="'.$erphp_url_front_vip.'" class="erphpdown-vip">升级'.$erphp_vip_name.'</a>';
							}
						}else{
							
							$content.='<a class="erphpdown-iframe erphpdown-buy" href="'.constant("erphpdown").'buy.php?postid='.get_the_ID().'&timestamp='.time().'">立即购买</a>';
							if($days){
								$content.= '（购买后'.$days.'天内可查看）';
							}
						}

						if(get_option('ice_tips')) $content.='<div class="erphpdown-tips">'.get_option('ice_tips').'</div>';
						$content.='</fieldset>';
					}

				}else{
					$content.='<fieldset class="erphpdown erphpdown-default erphpdown-see" id="erphpdown"><legend>'.($erphp_box_see_title?$erphp_box_see_title:'内容查看').'</legend>';
					
					if($memberDown == 4){
						if($erphp_wppay_vip){
							$content.='本文隐藏内容仅限'.$erphp_vip_name.'查看<a href="'.$erphp_url_front_vip.'" class="erphpdown-vip" target="_blank">升级'.$erphp_vip_name.'</a>';
						}else{
							$content.='本文隐藏内容仅限'.$erphp_vip_name.'查看，请先<a href="'.$erphp_url_front_login.'" class="erphp-login-must">登录</a>';
						}
					}elseif($memberDown == 15){
						if($erphp_wppay_vip){
							$content.='本文隐藏内容仅限'.$erphp_quarter_name.'查看<a href="'.$erphp_url_front_vip.'" class="erphpdown-vip" target="_blank">升级'.$erphp_quarter_name.'</a>';
						}else{
							$content.='本文隐藏内容仅限'.$erphp_quarter_name.'查看，请先<a href="'.$erphp_url_front_login.'" class="erphp-login-must">登录</a>';
						}
					}elseif($memberDown == 8){
						if($erphp_wppay_vip){
							$content.='本文隐藏内容仅限'.$erphp_year_name.'查看<a href="'.$erphp_url_front_vip.'" class="erphpdown-vip" target="_blank">升级'.$erphp_year_name.'</a>';
						}else{
							$content.='本文隐藏内容仅限'.$erphp_year_name.'查看，请先<a href="'.$erphp_url_front_login.'" class="erphp-login-must">登录</a>';
						}
					}elseif($memberDown == 9){
						if($erphp_wppay_vip){
							$content.='本文隐藏内容仅限'.$erphp_life_name.'查看<a href="'.$erphp_url_front_vip.'" class="erphpdown-vip" target="_blank">升级'.$erphp_life_name.'</a>';
						}else{
							$content.='本文隐藏内容仅限'.$erphp_life_name.'查看，请先<a href="'.$erphp_url_front_login.'" class="erphp-login-must">登录</a>';
						}
					}elseif($memberDown == 10){
						if($erphp_wppay_vip){
							$content.='本文隐藏内容仅限'.$erphp_vip_name.'购买<a href="'.$erphp_url_front_vip.'" class="erphpdown-vip" target="_blank">升级'.$erphp_vip_name.'</a>';
						}else{
							$content.='本文隐藏内容仅限'.$erphp_vip_name.'购买，请先<a href="'.$erphp_url_front_login.'" class="erphp-login-must">登录</a>';
						}
					}elseif($memberDown == 17){
						if($erphp_wppay_vip){
							$content.='本文隐藏内容仅限'.$erphp_quarter_name.'购买<a href="'.$erphp_url_front_vip.'" class="erphpdown-vip" target="_blank">升级'.$erphp_quarter_name.'</a>';
						}else{
							$content.='本文隐藏内容仅限'.$erphp_quarter_name.'购买，请先<a href="'.$erphp_url_front_login.'" class="erphp-login-must">登录</a>';
						}
					}elseif($memberDown == 18){
						if($erphp_wppay_vip){
							$content.='本文隐藏内容仅限'.$erphp_year_name.'购买<a href="'.$erphp_url_front_vip.'" class="erphpdown-vip" target="_blank">升级'.$erphp_year_name.'</a>';
						}else{
							$content.='本文隐藏内容仅限'.$erphp_year_name.'购买，请先<a href="'.$erphp_url_front_login.'" class="erphp-login-must">登录</a>';
						}
					}elseif($memberDown == 19){
						if($erphp_wppay_vip){
							$content.='本文隐藏内容仅限'.$erphp_life_name.'购买<a href="'.$erphp_url_front_vip.'" class="erphpdown-vip" target="_blank">升级'.$erphp_life_name.'</a>';
						}else{
							$content.='本文隐藏内容仅限'.$erphp_life_name.'购买，请先<a href="'.$erphp_url_front_login.'" class="erphp-login-must">登录</a>';
						}
					}elseif($memberDown == 11){
						if($erphp_wppay_vip){
							$content.='本文隐藏内容仅限'.$erphp_vip_name.'购买、'.$erphp_year_name.' 5折<a href="'.$erphp_url_front_vip.'" class="erphpdown-vip" target="_blank">升级'.$erphp_vip_name.'</a>';
						}else{
							$content.='本文隐藏内容仅限'.$erphp_vip_name.'购买、'.$erphp_year_name.' 5折，请先<a href="'.$erphp_url_front_login.'" class="erphp-login-must">登录</a>';
						}
					}elseif($memberDown == 12){
						if($erphp_wppay_vip){
							$content.='本文隐藏内容仅限'.$erphp_vip_name.'购买、'.$erphp_year_name.' 8折<a href="'.$erphp_url_front_vip.'" class="erphpdown-vip" target="_blank">升级'.$erphp_vip_name.'</a>';
						}else{
							$content.='本文隐藏内容仅限'.$erphp_vip_name.'购买、'.$erphp_year_name.' 8折，请先<a href="'.$erphp_url_front_login.'" class="erphp-login-must">登录</a>';
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
								return '';
							}else{
								if($price){
									$content.='本文隐藏内容查看价格为<span class="erphpdown-price">'.$price.'</span>'.get_option('ice_name_alipay');
									$content.='<a class="erphpdown-iframe erphpdown-buy" href="'.constant("erphpdown").'buy.php?postid='.get_the_ID().'&timestamp='.time().'" target="_blank">立即购买</a>';

									$content .= $vip_content?($vip_content.'<a href="'.$erphp_url_front_login.'" target="_blank" class="erphpdown-vip erphp-login-must">立即升级</a>'):'';
								}else{
									if(!get_option('erphp_free_login')){
										return '';
									}else{
										$content.='此内容仅限注册用户查看，请先<a href="'.$erphp_url_front_login.'" class="erphp-login-must">登录</a>';
									}
								}
							}
						}else{
							if($price){
								$content.='本文隐藏内容查看价格为<span class="erphpdown-price">'.$price.'</span>'.get_option('ice_name_alipay').$vip_content.'，请先<a href="'.$erphp_url_front_login.'" class="erphp-login-must">登录</a>';
							}else{
								$content.='本文隐藏内容仅限注册用户查看，请先<a href="'.$erphp_url_front_login.'" class="erphp-login-must">登录</a>';
							}
							
						}
					}
					if(get_option('ice_tips')) $content.='<div class="erphpdown-tips">'.get_option('ice_tips').'</div>';
					$content.='</fieldset>';
				}
				
				return $content;
				
			}elseif($erphp_down == 6){
				$content .= '<fieldset class="erphpdown erphpdown-default" id="erphpdown"><legend>'.($erphp_box_faka_title?$erphp_box_faka_title:'自动发卡').'</legend>';
				$content .= '此卡密价格为<span class="erphpdown-price">'.$price.'</span>'.get_option("ice_name_alipay");
				$content .= '<a class="erphpdown-iframe erphpdown-buy" href="'.constant("erphpdown").'buy.php?postid='.get_the_ID().'&timestamp='.time().'" target="_blank">立即购买</a>';
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
				if($downMsgFree) $content.='<fieldset class="erphpdown erphpdown-default" id="erphpdown"><legend>'.($erphp_box_down_title?$erphp_box_down_title:'资源下载').'</legend>'.$downMsgFree.'</fieldset>';
			}
			
		}else{
			$start_see=get_post_meta(get_the_ID(), 'start_see', true);
			if($start_see){
				return '';
			}
		}
	}
	
	return apply_filters('erphpdown_content_show', $content, $content2);
}

