<?php
// +----------------------------------------------------------------------
// | ERPHP [ PHP DEVELOP ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.mobantu.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: mobantu <82708210@qq.com>
// +----------------------------------------------------------------------
function _epd_create_page_order($payment, $c1 = '', $c2 = ''){
	global $wpdb;
	$post_id   = isset($_GET['ice_post']) && is_numeric($_GET['ice_post']) ?$_GET['ice_post'] :0;
	$user_type   = isset($_GET['ice_type']) && is_numeric($_GET['ice_type']) ?$_GET['ice_type'] :0;
	$index   = isset($_GET['index']) && is_numeric($_GET['index']) ?$_GET['index'] :'';
	
	$trade_order_id = '';
	$subject = '';
	$price = 0;

	if($c1 == 'v'){
		$user_type = $c2;
	}elseif($c1 == 'p'){
		$post_id = $c2;
	}elseif($c1 == 'r'){
		$price = $c2;
	}

	if(!$post_id && !$user_type && !is_user_logged_in()){
	    $erphp_url_front_login = wp_login_url();
	    if(get_option('erphp_url_front_login')){
	        $erphp_url_front_login = get_option('erphp_url_front_login');
	    }
	    wp_die("请先<a href='".$erphp_url_front_login."'>登录</a>！", __("友情提示",'erphpdown'));
	}

	if($post_id){

		$erphp_justbuy = get_option('erphp_justbuy');
	    if(!$erphp_justbuy){
	        wp_die('您无权直接支付购买此资源，请登录后使用余额支付！或联系站长开启直接支付购买功能！', __("友情提示",'erphpdown'));
	    }

		$down_user_one=get_post_meta($post_id, 'down_user_one',true);
		if($down_user_one){
			if(is_user_logged_in()){
				global $current_user;
				$downInfot=$wpdb->get_row("select * from ".$wpdb->icealipay." where ice_user_id=".$current_user->ID ." and ice_post=".$post_id." and ice_success=1 order by ice_time desc");
				if($downInfot){
					$down_user_ones=get_post_meta($post_id, 'down_user_ones',true);
					if($down_user_ones){
						$down_user_ones_arr = explode(',', str_replace('，', ',', $down_user_ones));
						if(is_array($down_user_ones_arr) && count($down_user_ones_arr) && in_array($current_user->user_login, $down_user_ones_arr)){

						}else{
							wp_die(__("抱歉，限制每人仅可购买一次！",'erphpdown'), __("友情提示",'erphpdown'));
						}
					}else{
						wp_die(__("抱歉，限制每人仅可购买一次！",'erphpdown'), __("友情提示",'erphpdown'));
					}
				}
			}else{
				wp_die(__("抱歉，由于限制每人仅可购买一次，请先登录！",'erphpdown'), __("友情提示",'erphpdown'));
			}
		}
		

	    $erphp_down=get_post_meta($post_id, 'erphp_down',TRUE);
	    if($erphp_down != 6){
	    	$days=get_post_meta($post_id, 'down_days', true);
	    	$down_repeat = get_post_meta($post_id, 'down_repeat', true);

			$user_info=wp_get_current_user();
			if($index){
				$hasdown_info=$wpdb->get_row("select * from ".$wpdb->icealipay." where ice_post='".$post_id."' and ice_index='".$index."' and ice_success=1 and ice_user_id=".$user_info->ID." order by ice_time desc");
			}else{
				$hasdown_info=$wpdb->get_row("select * from ".$wpdb->icealipay." where ice_post='".$post_id."' and ice_success=1 and (ice_index is null or ice_index = '') and ice_user_id=".$user_info->ID." order by ice_time desc");
			}
			if($days > 0){
				$lastDownDate = date('Y-m-d H:i:s',strtotime('+'.$days.' day',strtotime($hasdown_info->ice_time)));
				$nowDate = date('Y-m-d H:i:s');
				if(strtotime($nowDate) > strtotime($lastDownDate)){
					$hasdown_info = null;
				}
			}

		    $down_user_ones=get_post_meta($post_id, 'down_user_ones',true);
			if($down_user_ones){
				if(is_user_logged_in()){
					global $current_user;
					$down_user_ones_arr = explode(',', str_replace('，', ',', $down_user_ones));
					if(is_array($down_user_ones_arr) && count($down_user_ones_arr) && in_array($current_user->user_login, $down_user_ones_arr)){
						$down_repeat = 1;
					}
				}
			}

			if($hasdown_info && !$down_repeat){
				wp_die('请勿重复购买', __("友情提示",'erphpdown'));
			}
		}

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

		$erphp_life_discount    = get_option('erphp_life_discount');
		$erphp_year_discount    = get_option('erphp_year_discount');
		$erphp_quarter_discount = get_option('erphp_quarter_discount');
		$erphp_month_discount  = get_option('erphp_month_discount');
		$erphp_day_discount  = get_option('erphp_day_discount');
	    
	    $start_down2 = get_post_meta($post_id, 'start_down2',TRUE);
	    $erphp_wppay_down = get_option('erphp_wppay_down');
	    if(!$erphp_wppay_down && !$start_down2 && !is_user_logged_in()){
	        $erphp_url_front_login = wp_login_url();
	        if(get_option('erphp_url_front_login')){
	            $erphp_url_front_login = get_option('erphp_url_front_login');
	        }
	        wp_die("请先<a href='".$erphp_url_front_login."'>登录</a>！", __("友情提示",'erphpdown'));
	    }

	    $index_vip = '';

	    if($index){
	        $urls = get_post_meta($post_id, 'down_urls', true);
	        if($urls){
	            $cnt = count($urls['index']);
	            if($cnt){
	                for($i=0; $i<$cnt;$i++){
	                    if($urls['index'][$i] == $index){
	                        $index_name = $urls['name'][$i];
	                        $price = $urls['price'][$i];
	                        $index_vip = $urls['vip'][$i];
	                        break;
	                    }
	                }
	            }
	        }
	    }else{
	        $price=get_post_meta($post_id, 'down_price', true);
	    }

	    $memberDown=get_post_meta($post_id, 'member_down',TRUE);
	    if($index_vip){
	        $memberDown = $index_vip;
	    }
	    $userType=getUsreMemberType();

	    $categories = get_the_category($post_id);
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
			
	    if($memberDown==4 || $memberDown==15 || $memberDown==8 || $memberDown==9 || (($memberDown == 10 || $memberDown == 11 || $memberDown == 12) && !$userType)){
	        wp_die('您无权购买此资源！', __("友情提示",'erphpdown'));
	    }

	    if($userType && ($memberDown==2 || $memberDown==13)){
	        $price=sprintf("%.2f",$price*0.5);
	    }elseif($userType && ($memberDown==5 || $memberDown==14)){
	        $price=sprintf("%.2f",$price*0.8);
	    }elseif($userType>=9 && $memberDown==11){
	        $price=sprintf("%.2f",$price*0.5);
	    }elseif($userType>=9 && $memberDown==12){
	        $price=sprintf("%.2f",$price*0.8);
	    }elseif($userType && $memberDown==20)
		{
			if($userType == 6 && $erphp_day_discount){
				$price=$price*$erphp_day_discount*0.1;
			}elseif($userType == 7 && $erphp_month_discount){
				$price=$price*$erphp_month_discount*0.1;
			}elseif($userType == 8 && $erphp_quarter_discount){
				$price=$price*$erphp_quarter_discount*0.1;
			}elseif($userType == 9 && $erphp_year_discount){
				$price=$price*$erphp_year_discount*0.1;
			}
			$price=sprintf("%.2f",$price);
		}

		$user_info=wp_get_current_user();

		if( ($userType && ($memberDown==3 || $memberDown==4)) || (($memberDown==15 || $memberDown==16) && $userType >= 8) || (($memberDown==6 || $memberDown==8) && $userType >= 9) || (($memberDown==7 || $memberDown==9 || $memberDown==13 || $memberDown==14) && $userType == 10) ){
		    if($userType == 6 && $erphp_day_times > 0 && $erphp_day_discount > 0){
				if($day_times_includes_free){
					if( checkDownLogNoVip($user_info->ID,$post_id,$erphp_day_times) ){

					}else{
						$price=sprintf("%.2f",$price*$erphp_day_discount*0.1);
					}
				}else{
					if( checkDownLog($user_info->ID,$post_id,$erphp_day_times,1) ){

					}else{
						$price=sprintf("%.2f",$price*$erphp_day_discount*0.1);
					}
				}
			}elseif($userType == 7 && $erphp_month_times > 0 && $erphp_month_discount > 0){
				if($month_times_includes_free){
					if( checkDownLogNoVip($user_info->ID,$post_id,$erphp_month_times) ){

					}else{
						$price=sprintf("%.2f",$price*$erphp_month_discount*0.1);
					}
				}else{
					if( checkDownLog($user_info->ID,$post_id,$erphp_month_times,1) ){

					}else{
						$price=sprintf("%.2f",$price*$erphp_month_discount*0.1);
					}
				}
			}elseif($userType == 8 && $erphp_quarter_times > 0 && $erphp_quarter_discount > 0){
				if($quarter_times_includes_free){
					if( checkDownLogNoVip($user_info->ID,$post_id,$erphp_quarter_times) ){

					}else{
						$price=sprintf("%.2f",$price*$erphp_quarter_discount*0.1);
					}
				}else{
					if( checkDownLog($user_info->ID,$post_id,$erphp_quarter_times,1) ){

					}else{
						$price=sprintf("%.2f",$price*$erphp_quarter_discount*0.1);
					}
				}
			}elseif($userType == 9 && $erphp_year_times > 0 && $erphp_year_discount > 0){
				if($year_times_includes_free){
					if( checkDownLogNoVip($user_info->ID,$post_id,$erphp_year_times) ){

					}else{
						$price=sprintf("%.2f",$price*$erphp_year_discount*0.1);
					}
				}else{
					if( checkDownLog($user_info->ID,$post_id,$erphp_year_times,1) ){

					}else{
						$price=sprintf("%.2f",$price*$erphp_year_discount*0.1);
					}
				}
			}elseif($userType == 10 && $erphp_life_times > 0 && $erphp_life_discount > 0){
				if($life_times_includes_free){
					if( checkDownLogNoVip($user_info->ID,$post_id,$erphp_life_times) ){

					}else{
						$price=sprintf("%.2f",$price*$erphp_life_discount*0.1);
					}
				}else{
					if( checkDownLog($user_info->ID,$post_id,$erphp_life_times,1) ){

					}else{
						$price=sprintf("%.2f",$price*$erphp_life_discount*0.1);
					}
				}
			}
		}

	    if(isset($_SESSION['erphp_promo_code']) && $_SESSION['erphp_promo_code']){
	        $promo = str_replace("\\","", $_SESSION['erphp_promo_code']);
	        $promo_arr = json_decode($promo,true);
	        if($promo_arr['type'] == 1){
	            $promo_money = get_option('erphp_promo_money1');
	            if($promo_money){
	                if($start_down2){
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

	    if(!$start_down2){
	        $price = $price / get_option("ice_proportion_alipay");
	    }

	}elseif($user_type){
	    $erphp_wppay_vip    = get_option('erphp_wppay_vip');
	    if(!$erphp_wppay_vip && !is_user_logged_in()){
	        $erphp_url_front_login = wp_login_url();
	        if(get_option('erphp_url_front_login')){
	            $erphp_url_front_login = get_option('erphp_url_front_login');
	        }
	        wp_die("请先<a href='".$erphp_url_front_login."'>登录</a>！", __("友情提示",'erphpdown'));
	    }

	    $erphp_life_price    = get_option('erphp_life_price');
	    $erphp_year_price    = get_option('erphp_year_price');
	    $erphp_quarter_price = get_option('erphp_quarter_price');
	    $erphp_month_price  = get_option('erphp_month_price');
	    $erphp_day_price  = get_option('erphp_day_price');

	    if(isset($_SESSION['erphp_promo_code']) && $_SESSION['erphp_promo_code']){
	        $promo = str_replace("\\","", $_SESSION['erphp_promo_code']);
	        $promo_arr = json_decode($promo,true);
	        if($promo_arr['type'] == 1){
	            $promo_money = get_option('erphp_promo_money1');
	            if($promo_money){
	                if($erphp_life_price){
	                    $erphp_life_price = $erphp_life_price - $promo_money;
	                }
	                if($erphp_year_price){
	                    $erphp_year_price = $erphp_year_price - $promo_money;
	                }
	                if($erphp_quarter_price){
	                    $erphp_quarter_price = $erphp_quarter_price - $promo_money;
	                }
	                if($erphp_month_price){
	                    $erphp_month_price = $erphp_month_price - $promo_money;
	                }
	                if($erphp_day_price){
	                    $erphp_day_price = $erphp_day_price - $promo_money;
	                }
	            }
	        }elseif($promo_arr['type'] == 2){
	            $promo_money = get_option('erphp_promo_money2');
	            if($promo_money){
	                if($erphp_life_price){
	                    $erphp_life_price = $erphp_life_price * 0.1 * $promo_money;
	                }
	                if($erphp_year_price){
	                    $erphp_year_price = $erphp_year_price * 0.1 * $promo_money;
	                }
	                if($erphp_quarter_price){
	                    $erphp_quarter_price = $erphp_quarter_price * 0.1 * $promo_money;
	                }
	                if($erphp_month_price){
	                    $erphp_month_price = $erphp_month_price * 0.1 * $promo_money;
	                }
	                if($erphp_day_price){
	                    $erphp_day_price = $erphp_day_price * 0.1 * $promo_money;
	                }
	            }
	        }
	    }

	    if($user_type == 6){
	        $price = $erphp_day_price/get_option('ice_proportion_alipay');
	    }elseif($user_type == 7){
	        $price = $erphp_month_price/get_option('ice_proportion_alipay');
	    }elseif($user_type == 8){
	        $price = $erphp_quarter_price/get_option('ice_proportion_alipay');
	    }elseif($user_type == 9){
	        $price = $erphp_year_price/get_option('ice_proportion_alipay');
	    }elseif($user_type == 10){
	        $price = $erphp_life_price/get_option('ice_proportion_alipay');
	    }

	    $vip_update_pay = 0;$oldUserType = 0;
	    if(get_option('vip_update_pay') && is_user_logged_in()){
	        global $current_user;
	        $oldUserType = getUsreMemberTypeById($current_user->ID);

	        if($user_type == 7){
	            if($oldUserType == 6){
	                $price = ($erphp_month_price - $erphp_day_price)/get_option('ice_proportion_alipay');
	            }
	        }elseif($user_type == 8){
	            if($oldUserType == 6){
	                $price = ($erphp_quarter_price - $erphp_day_price)/get_option('ice_proportion_alipay');
	            }elseif($oldUserType == 7){
	                $price = ($erphp_quarter_price - $erphp_month_price)/get_option('ice_proportion_alipay');
	            }
	        }elseif($user_type == 9){
	            if($oldUserType == 6){
	                $price = ($erphp_year_price - $erphp_day_price)/get_option('ice_proportion_alipay');
	            }elseif($oldUserType == 7){
	                $price = ($erphp_year_price - $erphp_month_price)/get_option('ice_proportion_alipay');
	            }elseif($oldUserType == 8){
	                $price = ($erphp_year_price - $erphp_quarter_price)/get_option('ice_proportion_alipay');
	            }
	        }elseif($user_type == 10){
	            if($oldUserType == 6){
	                $price = ($erphp_life_price - $erphp_day_price)/get_option('ice_proportion_alipay');
	            }elseif($oldUserType == 7){
	                $price = ($erphp_life_price - $erphp_month_price)/get_option('ice_proportion_alipay');
	            }elseif($oldUserType == 8){
	                $price = ($erphp_life_price - $erphp_quarter_price)/get_option('ice_proportion_alipay');
	            }elseif($oldUserType == 9){
	                $price = ($erphp_life_price - $erphp_year_price)/get_option('ice_proportion_alipay');
	            }
	        }
	    }
	}else{
	    $price   = isset($_GET['ice_money']) && is_numeric($_GET['ice_money']) ?$_GET['ice_money'] :0;
	    if($c1 == 'r'){
			$price = $c2;
		}
	    $price = esc_sql($price);   
	    $erphpdown_min_price    = get_option('erphpdown_min_price');
	    if($erphpdown_min_price > 0){
	        if($price < $erphpdown_min_price){
	            wp_die('您最低需充值'.$erphpdown_min_price.'元', __("友情提示",'erphpdown'));
	        }
	    }
	}

	if($price > 0){
	    $trade_order_id = date("ymdhis").mt_rand(100,999).mt_rand(100,999);
	    $ice_aff = '';
	    if(is_user_logged_in()){
	        $subject = get_bloginfo('name').'订单['.get_the_author_meta( 'user_login', wp_get_current_user()->ID ).']';
	    }else{
	        $trade_order_id = 'MD'.$trade_order_id;
	        $subject = get_bloginfo('name').'订单';
	        if(isset($_COOKIE["erphprefid"]) && is_numeric($_COOKIE["erphprefid"])){
	            $ice_aff = $_COOKIE["erphprefid"];
	        }
	    }
	    $erphp_order_title = get_option('erphp_order_title');
	    if($erphp_order_title){
	        $subject = $erphp_order_title;
	    }

	    $ice_data = '';
	    if($post_id){
		    $erphp_down=get_post_meta($post_id, 'erphp_down',TRUE);
		    if($erphp_down == 6){
		        if(function_exists('getErphpActLeft')){
		            $ErphpActLeft = getErphpActLeft($post_id);
		            if($ErphpActLeft < 1){
		                wp_die('抱歉，库存不足!', __("友情提示",'erphpdown'));
		            }
		        }else{
		            wp_die('抱歉，网站未启用【激活码发放】扩展（Erphpdown-基础设置 里的免费扩展）!', __("友情提示",'erphpdown'));
		        }
		        
		        $num = (isset($_GET['num']) && is_numeric($_GET['num']) && floor($_GET['num'])==$_GET['num']) ?$_GET['num'] : 1;
		        $email = isset($_GET['data']) && is_email($_GET['data']) ?$_GET['data'] : '';
		        $pass = isset($_GET['data2']) && $_GET['data2'] ?$_GET['data2'] : '';
		        if(!$email){
		            wp_die('请填写一个接收卡密的邮箱!', __("友情提示",'erphpdown'));
		        }
		        $ice_data = $email.'|'.$num;
		        if($pass){
		        	$ice_data = $email.'|'.$num.'|'.$pass;
		        }
		        $price = $price*$num;

		        $trade_order_id = str_replace('MD','',$trade_order_id);
		        $trade_order_id = 'FK'.$trade_order_id;
		        $_SESSION['ice_num'] = $trade_order_id;
		    }
		}

	    $user_Info = wp_get_current_user();
	    $sql="INSERT INTO $wpdb->icemoney (ice_money,ice_num,ice_user_id,ice_user_type,ice_post_id,ice_post_index,ice_time,ice_success,ice_note,ice_success_time,ice_alipay,ice_aff,ice_ip,ice_data) VALUES ('$price','$trade_order_id','".$user_Info->ID."','".$user_type."','".$post_id."','".$index."','".date("Y-m-d H:i:s")."',0,'0','".date("Y-m-d H:i:s")."','".$payment."','".$ice_aff."','".erphpGetIP()."','".$ice_data."')";
	    $a=$wpdb->query($sql);
	    if(!$a){
	        wp_die('系统发生错误，请稍后重试!', __("友情提示",'erphpdown'));
	    }else{
			//$money_info=$wpdb->get_row("select * from ".$wpdb->icemoney." where ice_num='".$trade_order_id."'");
		}
	}else{
	    wp_die('请输入您要充值的金额！', __("友情提示",'erphpdown'));
	}
	return array("price"=>sprintf("%.2f",$price), "trade_order_id"=>$trade_order_id, "subject"=>$subject);
}

add_filter('init', '_epd_r64', 10);
function _epd_r64(){
	if(isset($_GET['epd_r64']) && $_GET['epd_r64']){
		session_start();
		header("Content-Type: text/html;charset=utf-8");
		date_default_timezone_set('Asia/Shanghai');
		global $wpdb;
		$epd_v64 = base64_decode($_GET['epd_r64']);
		$epd_v64_arr = explode('-', $epd_v64);
		if(is_array($epd_v64_arr) && count($epd_v64_arr) == 3){
			$method = $epd_v64_arr[0];
			$price = $epd_v64_arr[1];

			if(time()-$epd_v64_arr[2] > 60*30){
				wp_die("链接已过期！", __("友情提示",'erphpdown'));
			}
			
			$_SESSION['erphpdown_token']=md5(time().rand(100,999));
			if(isset($_GET['redirect_url'])){
			    $_COOKIE['erphpdown_return'] = urldecode($_GET['redirect_url']);
			    setcookie('erphpdown_return',urldecode($_GET['redirect_url']),0,'/');
			}else{
			    $_COOKIE['erphpdown_return'] = '';
			    setcookie('erphpdown_return','',0,'/');
			}

			$epd_order = _epd_create_page_order($method, 'r', $price);
			$price = $epd_order['price'];
			$out_trade_no = $epd_order['trade_order_id'];
			$subject = $epd_order['subject'];
			$money_info=$wpdb->get_row("select * from ".$wpdb->icemoney." where ice_num='".$out_trade_no."'");

			if($method == 'usdt'){
				$code_qr = get_option('erphpdown_usdt_address');
				if(function_exists('erphpdown_addon_epusdt')){
					$resultArray = erphpdown_addon_epusdt_create_transaction($out_trade_no, $price);
					if(isset($resultArray['status_code']) && $resultArray['status_code'] == '200'){
    					$price = $resultArray['data']['actual_amount'];//实际需要支付的USDT金额
    					$code_qr = $resultArray['data']['token'];
    				}else{
    					echo '获取支付失败：'.$resultArray['message'];
    					exit;
    				}
				}else{
					$price = sprintf("%.2f",$price/get_option('erphpdown_usdt_rmb'));
				}
			?>
				<html>
				<head>
				    <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
				    <meta name="viewport" content="width=device-width, initial-scale=1" /> 
				    <title>在线支付</title>
				    <link rel='stylesheet'  href='<?php echo ERPHPDOWN_URL;?>/static/erphpdown.css' type='text/css' media='all' />
				    <link rel="shortcut icon" href="<?php echo get_option('erphp_url_front_favicon');?>">
				</head>
				<body<?php if(!isset($_GET['iframe'])){echo ' class="erphpdown-page-pay"';}?>>

					<div class="wppay-custom-modal-box mobantu-wppay erphpdown-custom-modal-box">
						<section class="wppay-modal ut-modal">
				                    
				            <section class="erphp-wppay-qrcode mobantu-wppay">
				                <section class="tab">
				                    <a href="javascript:;" class="active"><div class="payment"><i class="erphp-iconfont erphp-icon-ut"></i></div><?php echo $price;?> USDT</a>
				                           </section>
				                <section class="tab-list">
				                    <section class="item">
				                    	<section class="qr-code">
				                            <img src='<?php echo constant("erphpdown").'includes/qrcode.php?data='.$code_qr;?>' class="img" alt="<?php echo $code_qr;?>">
				                        </section>
				                        <div class="ut-box">
				                        	<div class="ut-item">公链名称：<span><?php echo get_option('erphpdown_usdt_name');?></span><?php echo "<a class='erphpdown-copy' data-clipboard-text='".get_option('erphpdown_usdt_name')."' href='javascript:;'>".__('复制','erphpdown')."</a>";?></div>
				                        	<div class="ut-item">转币地址：<span style="color:#0e932e"><?php echo $code_qr;?></span><?php echo "<a class='erphpdown-copy' data-clipboard-text='".$code_qr."' href='javascript:;'>".__('复制','erphpdown')."</a>";?></div>
				                        	<div class="ut-item">附加说明：<span><?php echo $out_trade_no;?><?php echo "<a class='erphpdown-copy' data-clipboard-text='".$out_trade_no."' href='javascript:;'>".__('复制','erphpdown')."</a>";?></span></div>
				                        </div>
				                        <?php if(function_exists('erphpdown_addon_epusdt')){?>
				                        <p class="account" style="color:#ff5f33 !important">务必支付上面显示的金额，完成后请等待10秒左右，期间请勿刷新</p>
                        				<p id="time" class="desc"></p>
				                        <?php }else{?>
				                        <p class="account" style="color: #999 !important;">支付完成后请等待5分钟左右，有问题请联系客服</p>
				                    	<?php }?>
				                        <div class="kefu"><?php echo get_option('erphpdown_kefu');?></div>
				                    </section>
				                </section>
				            </section>
				        
				    	</section>
				    </div>

				    <script src="<?php echo ERPHPDOWN_URL;?>/static/jquery-1.7.min.js"></script>
				    <script src="<?php echo ERPHPDOWN_URL;?>/static/erphpdown.js"></script>
					<script>
						erphpOrder = setInterval(function() {
							$.ajax({  
					            type: 'POST',  
					            url: '<?php echo ERPHPDOWN_URL;?>/admin/action/order.php',  
					            data: {
					            	do: 'checkOrder',
					            	order: '<?php echo $money_info->ice_id;?>',
				                    token: '<?php echo $_SESSION['erphpdown_token'];?>'
					            },  
					            dataType: 'text',
					            success: function(data){  
					                if( $.trim(data) == '1' ){
					                    clearInterval(erphpOrder);
				                        <?php if(isset($_GET['iframe'])){?>
				                            var mylayer= parent.layer.getFrameIndex(window.name);
				                            parent.layer.close(mylayer);
				                            parent.layer.msg('充值成功！');
				                            parent.location.reload();  
				                        <?php }else{?>
				    	                    alert('支付成功！');
				    	                    <?php if(isset($_COOKIE['erphpdown_return']) && $_COOKIE['erphpdown_return']){?>
				                            location.href="<?php echo $_COOKIE['erphpdown_return'];?>";
				    	                    <?php }elseif(get_option('erphp_url_front_success')){?>
				    	                    location.href="<?php echo get_option('erphp_url_front_success');?>";
				    	                    <?php }else{?>
				    	                    window.close();
				    	                	<?php }?>
				                        <?php }?>
					                }  
					            },
					            error: function(XMLHttpRequest, textStatus, errorThrown){
					            	//alert(errorThrown);
					            }
					        });

						}, 10000);

						<?php if(function_exists('erphpdown_addon_epusdt')){?>
						var m = 5, s = 0;  
				        var Timer = document.getElementById("time");
				        wppayCountdown();
				        erphpTimer = setInterval(function(){ wppayCountdown() },1000);
				        function wppayCountdown (){
				            Timer.innerHTML = "支付倒计时：<span>0"+m+"分"+s+"秒</span>";
				            if( m == 0 && s == 0 ){
				                clearInterval(erphpOrder);
				                clearInterval(erphpTimer);
				                $(".qr-code").append('<div class="expired"></div>');
				                m = 4;
				                s = 59;
				            }else if( m >= 0 ){
				                if( s > 0 ){
				                    s--;
				                }else if( s == 0 ){
				                    m--;
				                    s = 59;
				                }
				            }
				        }
				    	<?php }?>
					</script>
				</body>
				</html>

				<?php
			}elseif($method == 'stripe'){
				$erphpdown_stripe_pk  = get_option('erphpdown_stripe_pk');
				$erphpdown_stripe_sk  = get_option('erphpdown_stripe_sk');

				require_once ERPHPDOWN_PATH.'/payment/stripe/init.php';

				\Stripe\Stripe::setApiKey($erphpdown_stripe_sk);
				header('Content-Type: application/json');

				$checkout_session = \Stripe\Checkout\Session::create([
				  'line_items' => [[
				    'price_data' => [
				      'currency' => 'cny',
				      'unit_amount' => $price*100,
				      'product_data' => [
				        'name' => get_bloginfo('name'),
				        'description' => $subject
				      ],
				    ],
				    'quantity' => 1,
				  ]],
				  'payment_intent_data'=>['metadata' => ["order_id" => $out_trade_no]],
				  'metadata' => ['order_id' => $out_trade_no],
				  'mode' => 'payment',
				  'success_url' => ERPHPDOWN_URL.'/payment/stripe/return.php',
				  'cancel_url' => home_url(),
				]);

				//var_dump($checkout_session);
				//exit;

				header("HTTP/1.1 303 See Other");
				header("Location: " . $checkout_session->url);
			
			}
		}
		exit;
	}
}

add_filter('init', '_epd_p64', 10);
function _epd_p64(){
	if(isset($_GET['epd_p64']) && $_GET['epd_p64']){
		session_start();
		header("Content-Type: text/html;charset=utf-8");
		date_default_timezone_set('Asia/Shanghai');
		global $wpdb;
		$epd_v64 = base64_decode($_GET['epd_p64']);
		$epd_v64_arr = explode('-', $epd_v64);
		if(is_array($epd_v64_arr) && count($epd_v64_arr) == 3){
			$method = $epd_v64_arr[0];
			$post_id = $epd_v64_arr[1];

			if(time()-$epd_v64_arr[2] > 60*30){
				wp_die("链接已过期！", __("友情提示",'erphpdown'));
			}
			
			$_SESSION['erphpdown_token']=md5(time().rand(100,999));
			if(isset($_GET['redirect_url'])){
			    $_COOKIE['erphpdown_return'] = urldecode($_GET['redirect_url']);
			    setcookie('erphpdown_return',urldecode($_GET['redirect_url']),0,'/');
			}else{
			    $_COOKIE['erphpdown_return'] = '';
			    setcookie('erphpdown_return','',0,'/');
			}

			$epd_order = _epd_create_page_order($method, 'p', $post_id);
			$price = $epd_order['price'];
			$out_trade_no = $epd_order['trade_order_id'];
			$subject = $epd_order['subject'];
			$money_info=$wpdb->get_row("select * from ".$wpdb->icemoney." where ice_num='".$out_trade_no."'");

			if($method == 'usdt'){
				$code_qr = get_option('erphpdown_usdt_address');
				if(function_exists('erphpdown_addon_epusdt')){
					$resultArray = erphpdown_addon_epusdt_create_transaction($out_trade_no, $price);
					if(isset($resultArray['status_code']) && $resultArray['status_code'] == '200'){
    					$price = $resultArray['data']['actual_amount'];//实际需要支付的USDT金额
    					$code_qr = $resultArray['data']['token'];
    				}else{
    					echo '获取支付失败：'.$resultArray['message'];
    					exit;
    				}
				}else{
					$price = sprintf("%.2f",$price/get_option('erphpdown_usdt_rmb'));
				}
			?>
				<html>
				<head>
				    <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
				    <meta name="viewport" content="width=device-width, initial-scale=1" /> 
				    <title>在线支付</title>
				    <link rel='stylesheet'  href='<?php echo ERPHPDOWN_URL;?>/static/erphpdown.css' type='text/css' media='all' />
				    <link rel="shortcut icon" href="<?php echo get_option('erphp_url_front_favicon');?>">
				</head>
				<body<?php if(!isset($_GET['iframe'])){echo ' class="erphpdown-page-pay"';}?>>

					<div class="wppay-custom-modal-box mobantu-wppay erphpdown-custom-modal-box">
						<section class="wppay-modal ut-modal">
				                    
				            <section class="erphp-wppay-qrcode mobantu-wppay">
				                <section class="tab">
				                    <a href="javascript:;" class="active"><div class="payment"><i class="erphp-iconfont erphp-icon-ut"></i></div><?php echo $price;?> USDT</a>
				                           </section>
				                <section class="tab-list">
				                    <section class="item">
				                    	<section class="qr-code">
				                            <img src='<?php echo constant("erphpdown").'includes/qrcode.php?data='.$code_qr;?>' class="img" alt="<?php echo $code_qr;?>">
				                        </section>
				                        <div class="ut-box">
				                        	<div class="ut-item">公链名称：<span><?php echo get_option('erphpdown_usdt_name');?></span><?php echo "<a class='erphpdown-copy' data-clipboard-text='".get_option('erphpdown_usdt_name')."' href='javascript:;'>".__('复制','erphpdown')."</a>";?></div>
				                        	<div class="ut-item">转币地址：<span style="color:#0e932e"><?php echo $code_qr;?></span><?php echo "<a class='erphpdown-copy' data-clipboard-text='".$code_qr."' href='javascript:;'>".__('复制','erphpdown')."</a>";?></div>
				                        	<div class="ut-item">附加说明：<span><?php echo $out_trade_no;?><?php echo "<a class='erphpdown-copy' data-clipboard-text='".$out_trade_no."' href='javascript:;'>".__('复制','erphpdown')."</a>";?></span></div>
				                        </div>
				                        <?php if(function_exists('erphpdown_addon_epusdt')){?>
				                        <p class="account" style="color:#ff5f33 !important">务必支付上面显示的金额，完成后请等待10秒左右，期间请勿刷新</p>
                        				<p id="time" class="desc"></p>
				                        <?php }else{?>
				                        <p class="account" style="color: #999 !important;">支付完成后请等待5分钟左右，有问题请联系客服</p>
				                    	<?php }?>
				                        <div class="kefu"><?php echo get_option('erphpdown_kefu');?></div>
				                    </section>
				                </section>
				            </section>
				        
				    	</section>
				    </div>

				    <script src="<?php echo ERPHPDOWN_URL;?>/static/jquery-1.7.min.js"></script>
				    <script src="<?php echo ERPHPDOWN_URL;?>/static/erphpdown.js"></script>
					<script>
						erphpOrder = setInterval(function() {
							$.ajax({  
					            type: 'POST',  
					            url: '<?php echo ERPHPDOWN_URL;?>/admin/action/order.php',  
					            data: {
					            	do: 'checkOrder',
					            	order: '<?php echo $money_info->ice_id;?>',
				                    token: '<?php echo $_SESSION['erphpdown_token'];?>'
					            },  
					            dataType: 'text',
					            success: function(data){  
					                if( $.trim(data) == '1' ){
					                    clearInterval(erphpOrder);
				                        <?php if(isset($_GET['iframe'])){?>
				                            var mylayer= parent.layer.getFrameIndex(window.name);
				                            parent.layer.close(mylayer);
				                            parent.layer.msg('充值成功！');
				                            parent.location.reload();  
				                        <?php }else{?>
				    	                    alert('支付成功！');
				    	                    <?php if(isset($_COOKIE['erphpdown_return']) && $_COOKIE['erphpdown_return']){?>
				                            location.href="<?php echo $_COOKIE['erphpdown_return'];?>";
				    	                    <?php }elseif(get_option('erphp_url_front_success')){?>
				    	                    location.href="<?php echo get_option('erphp_url_front_success');?>";
				    	                    <?php }else{?>
				    	                    window.close();
				    	                	<?php }?>
				                        <?php }?>
					                }  
					            },
					            error: function(XMLHttpRequest, textStatus, errorThrown){
					            	//alert(errorThrown);
					            }
					        });

						}, 10000);

						<?php if(function_exists('erphpdown_addon_epusdt')){?>
						var m = 5, s = 0;  
				        var Timer = document.getElementById("time");
				        wppayCountdown();
				        erphpTimer = setInterval(function(){ wppayCountdown() },1000);
				        function wppayCountdown (){
				            Timer.innerHTML = "支付倒计时：<span>0"+m+"分"+s+"秒</span>";
				            if( m == 0 && s == 0 ){
				                clearInterval(erphpOrder);
				                clearInterval(erphpTimer);
				                $(".qr-code").append('<div class="expired"></div>');
				                m = 4;
				                s = 59;
				            }else if( m >= 0 ){
				                if( s > 0 ){
				                    s--;
				                }else if( s == 0 ){
				                    m--;
				                    s = 59;
				                }
				            }
				        }
				    	<?php }?>
					</script>
				</body>
				</html>

				<?php
			}elseif($method == 'stripe'){
				$erphpdown_stripe_pk  = get_option('erphpdown_stripe_pk');
				$erphpdown_stripe_sk  = get_option('erphpdown_stripe_sk');

				require_once ERPHPDOWN_PATH.'/payment/stripe/init.php';

				\Stripe\Stripe::setApiKey($erphpdown_stripe_sk);
				header('Content-Type: application/json');

				$checkout_session = \Stripe\Checkout\Session::create([
				  'line_items' => [[
				    'price_data' => [
				      'currency' => 'cny',
				      'unit_amount' => $price*100,
				      'product_data' => [
				        'name' => get_bloginfo('name'),
				        'description' => $subject
				      ],
				    ],
				    'quantity' => 1,
				  ]],
				  'payment_intent_data'=>['metadata' => ["order_id" => $out_trade_no]],
				  'metadata' => ['order_id' => $out_trade_no],
				  'mode' => 'payment',
				  'success_url' => ERPHPDOWN_URL.'/payment/stripe/return.php',
				  'cancel_url' => home_url(),
				]);

				//var_dump($checkout_session);
				//exit;

				header("HTTP/1.1 303 See Other");
				header("Location: " . $checkout_session->url);
			}
		}
		exit;
	}
}


add_filter('init', '_epd_v64', 10);
function _epd_v64(){
	if(isset($_GET['epd_v64']) && $_GET['epd_v64']){
		session_start();
		header("Content-Type: text/html;charset=utf-8");
		date_default_timezone_set('Asia/Shanghai');
		global $wpdb;
		$epd_v64 = base64_decode($_GET['epd_v64']);
		$epd_v64_arr = explode('-', $epd_v64);
		if(is_array($epd_v64_arr) && count($epd_v64_arr) == 3){
			$method = $epd_v64_arr[0];
			$user_type = $epd_v64_arr[1];

			if(time()-$epd_v64_arr[2] > 60*30){
				wp_die("链接已过期！", __("友情提示",'erphpdown'));
			}
			
			$_SESSION['erphpdown_token']=md5(time().rand(100,999));
			if(isset($_GET['redirect_url'])){
			    $_COOKIE['erphpdown_return'] = urldecode($_GET['redirect_url']);
			    setcookie('erphpdown_return',urldecode($_GET['redirect_url']),0,'/');
			}else{
			    $_COOKIE['erphpdown_return'] = '';
			    setcookie('erphpdown_return','',0,'/');
			}

			$epd_order = _epd_create_page_order($method, 'v', $user_type);
			$price = $epd_order['price'];
			$out_trade_no = $epd_order['trade_order_id'];
			$subject = $epd_order['subject'];
			$money_info=$wpdb->get_row("select * from ".$wpdb->icemoney." where ice_num='".$out_trade_no."'");

			if($method == 'usdt'){
				$code_qr = get_option('erphpdown_usdt_address');
				if(function_exists('erphpdown_addon_epusdt')){
					$resultArray = erphpdown_addon_epusdt_create_transaction($out_trade_no, $price);
					if(isset($resultArray['status_code']) && $resultArray['status_code'] == '200'){
    					$price = $resultArray['data']['actual_amount'];//实际需要支付的USDT金额
    					$code_qr = $resultArray['data']['token'];
    				}else{
    					echo '获取支付失败：'.$resultArray['message'];
    					exit;
    				}
				}else{
					$price = sprintf("%.2f",$price/get_option('erphpdown_usdt_rmb'));
				}
			?>
				<html>
				<head>
				    <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
				    <meta name="viewport" content="width=device-width, initial-scale=1" /> 
				    <title>在线支付</title>
				    <link rel='stylesheet'  href='<?php echo ERPHPDOWN_URL;?>/static/erphpdown.css' type='text/css' media='all' />
				    <link rel="shortcut icon" href="<?php echo get_option('erphp_url_front_favicon');?>">
				</head>
				<body<?php if(!isset($_GET['iframe'])){echo ' class="erphpdown-page-pay"';}?>>

					<div class="wppay-custom-modal-box mobantu-wppay erphpdown-custom-modal-box">
						<section class="wppay-modal ut-modal">
				                    
				            <section class="erphp-wppay-qrcode mobantu-wppay">
				                <section class="tab">
				                    <a href="javascript:;" class="active"><div class="payment"><i class="erphp-iconfont erphp-icon-ut"></i></div><?php echo $price;?> USDT</a>
				                           </section>
				                <section class="tab-list">
				                    <section class="item">
				                    	<section class="qr-code">
				                            <img src='<?php echo constant("erphpdown").'includes/qrcode.php?data='.$code_qr;?>' class="img" alt="<?php echo $code_qr;?>">
				                        </section>
				                        <div class="ut-box">
				                        	<div class="ut-item">公链名称：<span><?php echo get_option('erphpdown_usdt_name');?></span><?php echo "<a class='erphpdown-copy' data-clipboard-text='".get_option('erphpdown_usdt_name')."' href='javascript:;'>".__('复制','erphpdown')."</a>";?></div>
				                        	<div class="ut-item">转币地址：<span style="color:#0e932e"><?php echo $code_qr;?></span><?php echo "<a class='erphpdown-copy' data-clipboard-text='".$code_qr."' href='javascript:;'>".__('复制','erphpdown')."</a>";?></div>
				                        	<div class="ut-item">附加说明：<span><?php echo $out_trade_no;?><?php echo "<a class='erphpdown-copy' data-clipboard-text='".$out_trade_no."' href='javascript:;'>".__('复制','erphpdown')."</a>";?></span></div>
				                        </div>
				                        <?php if(function_exists('erphpdown_addon_epusdt')){?>
				                        <p class="account" style="color:#ff5f33 !important">务必支付上面显示的金额，完成后请等待10秒左右，期间请勿刷新</p>
                        				<p id="time" class="desc"></p>
				                        <?php }else{?>
				                        <p class="account" style="color: #999 !important;">支付完成后请等待5分钟左右，有问题请联系客服</p>
				                    	<?php }?>
				                        <div class="kefu"><?php echo get_option('erphpdown_kefu');?></div>
				                    </section>
				                </section>
				            </section>
				        
				    	</section>
				    </div>

				    <script src="<?php echo ERPHPDOWN_URL;?>/static/jquery-1.7.min.js"></script>
				    <script src="<?php echo ERPHPDOWN_URL;?>/static/erphpdown.js"></script>
					<script>
						erphpOrder = setInterval(function() {
							$.ajax({  
					            type: 'POST',  
					            url: '<?php echo ERPHPDOWN_URL;?>/admin/action/order.php',  
					            data: {
					            	do: 'checkOrder',
					            	order: '<?php echo $money_info->ice_id;?>',
				                    token: '<?php echo $_SESSION['erphpdown_token'];?>'
					            },  
					            dataType: 'text',
					            success: function(data){  
					                if( $.trim(data) == '1' ){
					                    clearInterval(erphpOrder);
				                        <?php if(isset($_GET['iframe'])){?>
				                            var mylayer= parent.layer.getFrameIndex(window.name);
				                            parent.layer.close(mylayer);
				                            parent.layer.msg('充值成功！');
				                            parent.location.reload();  
				                        <?php }else{?>
				    	                    alert('支付成功！');
				    	                    <?php if(isset($_COOKIE['erphpdown_return']) && $_COOKIE['erphpdown_return']){?>
				                            location.href="<?php echo $_COOKIE['erphpdown_return'];?>";
				    	                    <?php }elseif(get_option('erphp_url_front_success')){?>
				    	                    location.href="<?php echo get_option('erphp_url_front_success');?>";
				    	                    <?php }else{?>
				    	                    window.close();
				    	                	<?php }?>
				                        <?php }?>
					                }  
					            },
					            error: function(XMLHttpRequest, textStatus, errorThrown){
					            	//alert(errorThrown);
					            }
					        });

						}, 10000);

						<?php if(function_exists('erphpdown_addon_epusdt')){?>
						var m = 5, s = 0;  
				        var Timer = document.getElementById("time");
				        wppayCountdown();
				        erphpTimer = setInterval(function(){ wppayCountdown() },1000);
				        function wppayCountdown (){
				            Timer.innerHTML = "支付倒计时：<span>0"+m+"分"+s+"秒</span>";
				            if( m == 0 && s == 0 ){
				                clearInterval(erphpOrder);
				                clearInterval(erphpTimer);
				                $(".qr-code").append('<div class="expired"></div>');
				                m = 4;
				                s = 59;
				            }else if( m >= 0 ){
				                if( s > 0 ){
				                    s--;
				                }else if( s == 0 ){
				                    m--;
				                    s = 59;
				                }
				            }
				        }
				    	<?php }?>
					</script>
				</body>
				</html>

				<?php
			}elseif($method == 'stripe'){
				$erphpdown_stripe_pk  = get_option('erphpdown_stripe_pk');
				$erphpdown_stripe_sk  = get_option('erphpdown_stripe_sk');

				require_once ERPHPDOWN_PATH.'/payment/stripe/init.php';

				\Stripe\Stripe::setApiKey($erphpdown_stripe_sk);
				header('Content-Type: application/json');

				$checkout_session = \Stripe\Checkout\Session::create([
				  'line_items' => [[
				    'price_data' => [
				      'currency' => 'cny',
				      'unit_amount' => $price*100,
				      'product_data' => [
				        'name' => get_bloginfo('name'),
				        'description' => $subject
				      ],
				    ],
				    'quantity' => 1,
				  ]],
				  'payment_intent_data'=>['metadata' => ["order_id" => $out_trade_no]],
				  'metadata' => ['order_id' => $out_trade_no],
				  'mode' => 'payment',
				  'success_url' => ERPHPDOWN_URL.'/payment/stripe/return.php',
				  'cancel_url' => home_url(),
				]);

				//var_dump($checkout_session);
				//exit;

				header("HTTP/1.1 303 See Other");
				header("Location: " . $checkout_session->url);
			}
		}
		exit;
	}
}