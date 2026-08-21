<?php
function erphpdown_tuan_install(){
	global $wpdb;
	$charset_collate = $wpdb->get_charset_collate();
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

	$create_ice_tuan_sql = "CREATE TABLE ".$wpdb->prefix."ice_tuan (".
		"ice_id int(11) NOT NULL auto_increment,".
		"ice_num varchar(50) NOT NULL,".
		"ice_post int(11) NOT NULL,".
		"ice_status int(11) NOT NULL DEFAULT 0,".
		"PRIMARY KEY (ice_id)) $charset_collate;";
	dbDelta( $create_ice_tuan_sql );

	$create_ice_tuanorder_sql = "CREATE TABLE ".$wpdb->prefix."ice_tuan_order (".
		"ice_id int(11) NOT NULL auto_increment,".
		"ice_num varchar(50) NOT NULL,".
		"ice_tuan_num varchar(50) NOT NULL,".
		"ice_price double(10,2) NOT NULL,".
		"ice_post int(11) NOT NULL,".
		"ice_user_id int(11) NOT NULL,".
		"ice_status int(11) NOT NULL DEFAULT 0,".
		"ice_time datetime NOT NULL,".
		"PRIMARY KEY (ice_id)) $charset_collate;";
	dbDelta( $create_ice_tuanorder_sql );
}

function erphpdown_tuan_html(){
	global $post,$wpdb,$current_user;
	$down_tuan=get_post_meta(get_the_ID(), 'down_tuan', true);
	$down_tuan_price=get_post_meta(get_the_ID(), 'down_tuan_price', true);
	$down_tuan_num=get_post_meta(get_the_ID(), 'down_tuan_num', true);
	$down_tuan_desc=get_post_meta(get_the_ID(), 'down_tuan_desc', true);
	$down_tuan_days=get_post_meta(get_the_ID(), 'down_tuan_days', true);
	$down_tuan_tui=get_post_meta(get_the_ID(), 'down_tuan_tui', true);
	$down_tuan_one = get_post_meta(get_the_ID(),'down_tuan_one',true);
	$tuanHtml = ''; $iframe = ''; $erphp_popdown = '';
	$down_tuan_desc = $down_tuan_desc?$down_tuan_desc:__('拼团完成即可下载，未参与的可进入下一轮。','mobantu');

	if(get_option('erphp_popdown')){
		$erphp_popdown=' erphpdown-down-layui';
		$iframe = '&iframe=1';
	}

	$erphp_url_front_login = wp_login_url();
	if(get_option('erphp_url_front_login')){
		$erphp_url_front_login = get_option('erphp_url_front_login');
	}

	if($down_tuan && epd_tuan_active()){
		$yituan = $wpdb->get_var("select ice_status from ".$wpdb->prefix."ice_tuan_order where ice_user_id=".$current_user->ID." and ice_post=".get_the_ID()." and ice_status>0");
		$tuanHtml .= '<div class="erphpdown-tuan'.($down_tuan=='1'?' erphpdown-tuan2':'').'">';
		if($yituan == '2'){
			$tuanHtml .= sprintf( __('此资源拼团价格为<span class="erphpdown-price">%s</span>%s','mobantu'),$down_tuan_price,get_option("ice_name_alipay") );
			$tuanHtml .= '<a class="erphpdown-down erphpdown-tuan-btn'.$erphp_popdown.'" href="'.constant("erphpdown").'download.php?postid='.get_the_ID().$iframe.'">'.__('拼团成功，立即下载','mobantu').'</a>';
		}else{
			$success = $wpdb->get_var("select ice_id from ".$wpdb->prefix."ice_tuan where ice_post='".get_the_ID()."' and ice_status=1");
			if($down_tuan_one && $success){
				$process = '<div class="erphpdown-tuan-process"><div class="line"><span style="width:100%"></span></div><div class="data">'.__('已完成100%','mobantu').'</div></div>';
				$tuanHtml .= sprintf( __('此资源拼团价格为<span class="erphpdown-price">%s</span>%s','mobantu'),$down_tuan_price,get_option("ice_name_alipay") ).'<a href="javascript:;" class="erphp-login-must erphpdown-tuan-btn erphpdown-tuan-over disabled">'.__('拼团已结束','mobantu').'</a>';
			}else{
				$tnum = get_erphpdown_tuan_num(get_the_ID());
				$percent = get_erphpdown_tuan_percent(get_the_ID(),$tnum);
				$process = '<div class="erphpdown-tuan-process"><div class="line"><span style="width:'.$percent.'%"></span></div><div class="data">'.sprintf( __('已完成%s','mobantu'), $percent).'% '.sprintf( __('(共需%s人，还需%s人)','mobantu'),$down_tuan_num,($down_tuan_num-get_erphpdown_tuan_count(get_the_ID(),$tnum))).'</div></div>';
				if(!is_user_logged_in()){
					$tuanHtml .= sprintf( __('此资源拼团价格为<span class="erphpdown-price">%s</span>%s','mobantu'),$down_tuan_price,get_option("ice_name_alipay") ).'，请先<a href="'.$erphp_url_front_login.'" target="_blank" class="erphp-login-must erphpdown-tuan-btn">登录</a>';
				}else{
					global $current_user;
					$tuan_time = $wpdb->get_var("select ice_time from ".$wpdb->prefix."ice_tuan_order where ice_user_id=".$current_user->ID." and ice_post=".get_the_ID()." and ice_status>0");
					if($yituan == '1'){
						$tuanHtml .= sprintf( __('此资源拼团价格为<span class="erphpdown-price">%s</span>%s','mobantu'),$down_tuan_price,get_option("ice_name_alipay") ).'<a href="javascript:;" class="erphpdown-down erphpdown-tuan-btn disabled">'.__('已参团','mobantu').'</a>';
						if($down_tuan_days > 0){
							$lastDownDate = date('Y-m-d H:i:s',strtotime('+'.$down_tuan_days.' day',strtotime($tuan_time)));
							$nowDate = date('Y-m-d H:i:s');

							$down_info=$wpdb->get_row("select * from ".$wpdb->icealipay." where ice_post='".get_the_ID()."' and ice_success=1 and (ice_index is null or ice_index = '') and ice_user_id=".$current_user->ID." order by ice_time desc");

							if(strtotime($nowDate) > strtotime($lastDownDate) || $down_info){
								$tuanHtml .= '<a href="javascript:;" data-num="'.$tnum.'" data-tui="'.$down_tuan_tui.'" data-post="'.get_the_ID().'" class="erphpdown-tuituan">'.__('我要退团','mobantu').'</a>';
							}
						}
					}else{
						$tuanHtml .= sprintf( __('此资源拼团价格为<span class="erphpdown-price">%s</span>%s','mobantu'),$down_tuan_price,get_option("ice_name_alipay") ).'<a href="javascript:;" class="erphpdown-down erphpdown-tuan-btn erphpdown-tuan-loader" data-post="'.get_the_ID().'" data-num="'.$tnum.'">'.__('立即参团','mobantu').'</a>';
					}
				}
			}
			$tuanHtml .= $process;
		}
		$tuanHtml .= '<div class="erphpdown-tips">'.$down_tuan_desc.'</div>';
		$tuanHtml .= '</div>';
	}
	return $tuanHtml;
}

function erphpdown_tuan_modown_html(){
	global $post,$wpdb,$current_user;
	$down_tuan=get_post_meta(get_the_ID(), 'down_tuan', true);
	$down_tuan_price=get_post_meta(get_the_ID(), 'down_tuan_price', true);
	$down_tuan_num=get_post_meta(get_the_ID(), 'down_tuan_num', true);
	$down_tuan_desc=get_post_meta(get_the_ID(), 'down_tuan_desc', true);
	$down_tuan_days=get_post_meta(get_the_ID(), 'down_tuan_days', true);
	$down_tuan_tui=get_post_meta(get_the_ID(), 'down_tuan_tui', true);
	$down_tuan_one = get_post_meta(get_the_ID(),'down_tuan_one',true);
	$tuanHtml = ''; $iframe = ''; $erphp_popdown = '';
	$down_tuan_desc = $down_tuan_desc?$down_tuan_desc:__('拼团完成即可下载，未参与的可进入下一轮。','mobantu');

	if(get_option('erphp_popdown')){
		$erphp_popdown=' erphpdown-down-layui';
		$iframe = '&iframe=1';
	}

	$erphp_url_front_login = wp_login_url();
	if(get_option('erphp_url_front_login')){
		$erphp_url_front_login = get_option('erphp_url_front_login');
	}

	if($down_tuan && epd_tuan_active()){
		$yituan = $wpdb->get_var("select ice_status from ".$wpdb->prefix."ice_tuan_order where ice_user_id=".$current_user->ID." and ice_post=".get_the_ID()." and ice_status>0");
		$tuanHtml .= '<div class="erphpdown-tuan'.($down_tuan=='1'?' erphpdown-tuan2':'').'">';
		if($yituan == '2'){
			$tuanHtml .= '<div class="item price"><font>拼团价</font><span>'.$down_tuan_price.'</span> '.get_option("ice_name_alipay").'</div>';
			$tuanHtml .= '<a class="down bought erphpdown-tuan-btn'.$erphp_popdown.'" href="'.constant("erphpdown").'download.php?postid='.get_the_ID().$iframe.'">'.__('拼团成功，立即下载','mobantu').'</a>';
		}else{
			$success = $wpdb->get_var("select ice_id from ".$wpdb->prefix."ice_tuan where ice_post='".get_the_ID()."' and ice_status=1");
			if($down_tuan_one && $success){
				$process = '<div class="erphpdown-tuan-process"><div class="line"><span style="width:100%"></span></div><div class="data">'.__('已完成100%','mobantu').'</div></div>';
				$tuanHtml .= '<div class="item price"><font>拼团价</font><span>'.$down_tuan_price.'</span> '.get_option("ice_name_alipay").'</div>';
				$tuanHtml .= $process;
				$tuanHtml .= '<a href="javascript:;" class="down erphpdown-tuan-btn erphpdown-tuan-over disabled">'.__('拼团已结束','mobantu').'</a>';
			}else{
				$tnum = get_erphpdown_tuan_num(get_the_ID());
				$percent = get_erphpdown_tuan_percent(get_the_ID(),$tnum);
				$process = '<div class="erphpdown-tuan-process"><div class="line"><span style="width:'.$percent.'%"></span></div><div class="data">'.sprintf( __('已完成%s','mobantu'), $percent).'% '.sprintf( __('(共需%s人，还需%s人)','mobantu'),$down_tuan_num,($down_tuan_num-get_erphpdown_tuan_count(get_the_ID(),$tnum))).'</div></div>';
				if(!is_user_logged_in()){
					$tuanHtml .= '<div class="item price"><font>拼团价</font><span>'.$down_tuan_price.'</span> '.get_option("ice_name_alipay").'</div>';
					$tuanHtml .= $process;
					$tuanHtml .= '<a href="javascript:;" class="down erphpdown-tuan-btn signin-loader">登录参团</a>';
				}else{
					global $current_user;
					$tuan_time = $wpdb->get_var("select ice_time from ".$wpdb->prefix."ice_tuan_order where ice_user_id=".$current_user->ID." and ice_post=".get_the_ID()." and ice_status>0");
					if($yituan == '1'){
						$tuanHtml .= '<div class="item price"><font>拼团价</font><span>'.$down_tuan_price.'</span> '.get_option("ice_name_alipay").'</div>';
						$tuanHtml .= $process;
						$tuanHtml .= '<a href="javascript:;" class="down erphpdown-tuan-btn disabled">'.__('已参团','mobantu').'</a>';
						if($down_tuan_days > 0){
							$lastDownDate = date('Y-m-d H:i:s',strtotime('+'.$down_tuan_days.' day',strtotime($tuan_time)));
							$nowDate = date('Y-m-d H:i:s');
							$down_info=$wpdb->get_row("select * from ".$wpdb->icealipay." where ice_post='".get_the_ID()."' and ice_success=1 and (ice_index is null or ice_index = '') and ice_user_id=".$current_user->ID." order by ice_time desc");

							if(strtotime($nowDate) > strtotime($lastDownDate) || $down_info){
								$tuanHtml .= '<a href="javascript:;" data-num="'.$tnum.'" data-tui="'.$down_tuan_tui.'" data-post="'.get_the_ID().'" class="erphpdown-tuituan">'.__('我要退团','mobantu').'</a>';
							}
						}
					}else{
						$tuanHtml .= '<div class="item price"><font>拼团价</font><span>'.$down_tuan_price.'</span> '.get_option("ice_name_alipay").'</div>';
						$tuanHtml .= $process;
						$tuanHtml .= '<a href="javascript:;" class="down erphpdown-tuan-loader" data-post="'.get_the_ID().'" data-num="'.$tnum.'">'.__('立即参团','mobantu').'</a>';
					}
				}
			}
		}
		$tuanHtml .= '<div class="tips">'.$down_tuan_desc.'</div>';
		$tuanHtml .= '</div>';
	}
	return $tuanHtml;
}

function erphpdown_tuan_modown_html2(){
	global $post,$wpdb,$current_user;
	$down_tuan=get_post_meta(get_the_ID(), 'down_tuan', true);
	$down_tuan_price=get_post_meta(get_the_ID(), 'down_tuan_price', true);
	$down_tuan_num=get_post_meta(get_the_ID(), 'down_tuan_num', true);
	$down_tuan_desc=get_post_meta(get_the_ID(), 'down_tuan_desc', true);
	$down_tuan_days=get_post_meta(get_the_ID(), 'down_tuan_days', true);
	$down_tuan_tui=get_post_meta(get_the_ID(), 'down_tuan_tui', true);
	$down_tuan_one = get_post_meta(get_the_ID(),'down_tuan_one',true);
	$tuanHtml = ''; $iframe = ''; $erphp_popdown = '';
	$down_tuan_desc = $down_tuan_desc?$down_tuan_desc:__('拼团完成即可下载，未参与的可进入下一轮。','mobantu');

	if(get_option('erphp_popdown')){
		$erphp_popdown=' erphpdown-down-layui';
		$iframe = '&iframe=1';
	}

	$erphp_url_front_login = wp_login_url();
	if(get_option('erphp_url_front_login')){
		$erphp_url_front_login = get_option('erphp_url_front_login');
	}

	if($down_tuan && epd_tuan_active()){
		$yituan = $wpdb->get_var("select ice_status from ".$wpdb->prefix."ice_tuan_order where ice_user_id=".$current_user->ID." and ice_post=".get_the_ID()." and ice_status>0");
		$tuanHtml .= '<div class="erphpdown-tuan'.($down_tuan=='1'?' erphpdown-tuan2':'').' clearfix">';
		if($yituan == '2'){
			$tuanHtml .= '<div class="erphpdown-price">拼团价格<span>'.$down_tuan_price.'</span> '.get_option("ice_name_alipay").'</div>';
			$tuanHtml .= '<div class="erphpdown-cart"><a class="down bought erphpdown-tuan-btn'.$erphp_popdown.'" href="'.constant("erphpdown").'download.php?postid='.get_the_ID().$iframe.'">'.__('拼团成功，立即下载','mobantu').'</a></div>';
		}else{

			$success = $wpdb->get_var("select ice_id from ".$wpdb->prefix."ice_tuan where ice_post='".get_the_ID()."' and ice_status=1");
			if($down_tuan_one && $success){
				$process = '<div class="erphpdown-tuan-process"><div class="line"><span style="width:100%"></span></div><div class="data">'.__('已完成100%','mobantu').'</div></div>';
				$tuanHtml .= '<div class="erphpdown-price">拼团价格<span>'.$down_tuan_price.'</span> '.get_option("ice_name_alipay").'</div>';
				$tuanHtml .= '<div class="erphpdown-cart">';
				$tuanHtml .= $process;
				$tuanHtml .= '<a href="javascript:;" class="down erphpdown-tuan-btn erphpdown-tuan-over disabled">'.__('拼团已结束','mobantu').'</a>';
				$tuanHtml .= '</div>';
			}else{
				$tnum = get_erphpdown_tuan_num(get_the_ID());
				$percent = get_erphpdown_tuan_percent(get_the_ID(),$tnum);
				$process = '<div class="erphpdown-tuan-process"><div class="line"><span style="width:'.$percent.'%"></span></div><div class="data">'.sprintf( __('已完成%s','mobantu'), $percent).'% '.sprintf( __('(共需%s人，还需%s人)','mobantu'),$down_tuan_num,($down_tuan_num-get_erphpdown_tuan_count(get_the_ID(),$tnum))).'</div></div>';
				if(!is_user_logged_in()){
					$tuanHtml .= '<div class="erphpdown-price">拼团价格<span>'.$down_tuan_price.'</span> '.get_option("ice_name_alipay").'</div>';
					$tuanHtml .= '<div class="erphpdown-cart">';
					$tuanHtml .= $process;
					$tuanHtml .= '<a href="javascript:;" class="down erphpdown-tuan-btn signin-loader">登录参团</a>';
					$tuanHtml .= '</div>';
				}else{
					global $current_user;
					$tuan_time = $wpdb->get_var("select ice_time from ".$wpdb->prefix."ice_tuan_order where ice_user_id=".$current_user->ID." and ice_post=".get_the_ID()." and ice_status>0");
					if($yituan == '1'){
						$tuanHtml .= '<div class="erphpdown-price">拼团价格<span>'.$down_tuan_price.'</span> '.get_option("ice_name_alipay").'</div>';
						$tuanHtml .= '<div class="erphpdown-cart">';
						$tuanHtml .= $process;
						$tuanHtml .= '<a href="javascript:;" class="down erphpdown-tuan-btn disabled">'.__('已参团','mobantu').'</a>';
						if($down_tuan_days > 0){
							$lastDownDate = date('Y-m-d H:i:s',strtotime('+'.$down_tuan_days.' day',strtotime($tuan_time)));
							$nowDate = date('Y-m-d H:i:s');
							$down_info=$wpdb->get_row("select * from ".$wpdb->icealipay." where ice_post='".get_the_ID()."' and ice_success=1 and (ice_index is null or ice_index = '') and ice_user_id=".$current_user->ID." order by ice_time desc");

							if(strtotime($nowDate) > strtotime($lastDownDate) || $down_info){
								$tuanHtml .= '<a href="javascript:;" data-num="'.$tnum.'" data-tui="'.$down_tuan_tui.'" data-post="'.get_the_ID().'" class="erphpdown-tuituan">'.__('我要退团','mobantu').'</a>';
							}
						}
						$tuanHtml .= '</div>';
					}else{
						$tuanHtml .= '<div class="erphpdown-price">拼团价格<span>'.$down_tuan_price.'</span> '.get_option("ice_name_alipay").'</div>';
						$tuanHtml .= '<div class="erphpdown-cart">';
						$tuanHtml .= $process;
						$tuanHtml .= '<a href="javascript:;" class="down erphpdown-tuan-loader" data-post="'.get_the_ID().'" data-num="'.$tnum.'">'.__('立即参团','mobantu').'</a>';
						$tuanHtml .= '</div>';
					}
				}
			}
		}
		$tuanHtml .= '</div>';
		$tuanHtml .= '<div class="tips2">'.$down_tuan_desc.'</div>';
	}
	return $tuanHtml;
}

function erphpdown_tuan_modown_html3(){
	global $post,$wpdb,$current_user;
	$down_tuan=get_post_meta(get_the_ID(), 'down_tuan', true);
	$down_tuan_price=get_post_meta(get_the_ID(), 'down_tuan_price', true);
	$down_tuan_num=get_post_meta(get_the_ID(), 'down_tuan_num', true);
	$down_tuan_desc=get_post_meta(get_the_ID(), 'down_tuan_desc', true);
	$down_tuan_days=get_post_meta(get_the_ID(), 'down_tuan_days', true);
	$down_tuan_tui=get_post_meta(get_the_ID(), 'down_tuan_tui', true);
	$down_tuan_one = get_post_meta(get_the_ID(),'down_tuan_one',true);
	$tuanHtml = ''; $iframe = ''; $erphp_popdown = '';
	$down_tuan_desc = $down_tuan_desc?$down_tuan_desc:__('拼团完成即可下载，未参与的可进入下一轮。','mobantu');

	if(get_option('erphp_popdown')){
		$erphp_popdown=' erphpdown-down-layui';
		$iframe = '&iframe=1';
	}

	$erphp_url_front_login = wp_login_url();
	if(get_option('erphp_url_front_login')){
		$erphp_url_front_login = get_option('erphp_url_front_login');
	}

	if($down_tuan && epd_tuan_active()){
		$yituan = $wpdb->get_var("select ice_status from ".$wpdb->prefix."ice_tuan_order where ice_user_id=".$current_user->ID." and ice_post=".get_the_ID()." and ice_status>0");
		$tuanHtml .= '<div class="erphpdown-tuan'.($down_tuan=='1'?' erphpdown-tuan2':'').'">';
		if($yituan == '2'){
			$tuanHtml .= '<div class="erphpdown-box-tips">'.sprintf( __('此资源拼团价格为<span class="erphpdown-price">%s</span>%s','mobantu'),$down_tuan_price,get_option("ice_name_alipay") ).'</div>';
			$tuanHtml .= '<a class="erphpdown-down erphpdown-btn-large bought erphpdown-tuan-btn'.$erphp_popdown.'" href="'.constant("erphpdown").'download.php?postid='.get_the_ID().$iframe.'">'.__('拼团成功，立即下载','mobantu').'</a>';
		}else{
			$success = $wpdb->get_var("select ice_id from ".$wpdb->prefix."ice_tuan where ice_post='".get_the_ID()."' and ice_status=1");
			if($down_tuan_one && $success){
				$process = '<div class="erphpdown-tuan-process"><div class="line"><span style="width:100%"></span></div><div class="data">'.__('已完成100%','mobantu').'</div></div>';
				$tuanHtml .= '<div class="erphpdown-box-tips">'.sprintf( __('此资源拼团价格为<span class="erphpdown-price">%s</span>%s','mobantu'),$down_tuan_price,get_option("ice_name_alipay") ).'</div>';
				$tuanHtml .= "<a href='javascript:;' class='erphpdown-down erphpdown-btn-large erphpdown-tuan-btn erphpdown-tuan-over disabled'>".__('拼团已结束','mobantu')."</a>";
			}else{
				$tnum = get_erphpdown_tuan_num(get_the_ID());
				$percent = get_erphpdown_tuan_percent(get_the_ID(),$tnum);
				$process = '<div class="erphpdown-tuan-process"><div class="line"><span style="width:'.$percent.'%"></span></div><div class="data">'.sprintf( __('已完成%s','mobantu'), $percent).'% '.sprintf( __('(共需%s人，还需%s人)','mobantu'),$down_tuan_num,($down_tuan_num-get_erphpdown_tuan_count(get_the_ID(),$tnum))).'</div>'.'</div>';
				if(!is_user_logged_in()){
					$tuanHtml .= '<div class="erphpdown-box-tips">'.sprintf( __('此资源拼团价格为<span class="erphpdown-price">%s</span>%s','mobantu'),$down_tuan_price,get_option("ice_name_alipay") ).'</div>';
					$tuanHtml .= "<a href='javascript:;' class='erphpdown-down erphpdown-btn-large erphpdown-tuan-btn signin-loader'>登录参团</a>";
				}else{
					global $current_user;
					$tuan_time = $wpdb->get_var("select ice_time from ".$wpdb->prefix."ice_tuan_order where ice_user_id=".$current_user->ID." and ice_post=".get_the_ID()." and ice_status>0");
					if($yituan == '1'){
						$tuanHtml .= '<div class="erphpdown-box-tips">'.sprintf( __('此资源拼团价格为<span class="erphpdown-price">%s</span>%s','mobantu'),$down_tuan_price,get_option("ice_name_alipay") ).'</div><a href="javascript:;" class="erphpdown-down erphpdown-btn-large erphpdown-tuan-btn disabled">'.__('已参团','mobantu').'</a>';
						if($down_tuan_days > 0){
							$lastDownDate = date('Y-m-d H:i:s',strtotime('+'.$down_tuan_days.' day',strtotime($tuan_time)));
							$nowDate = date('Y-m-d H:i:s');
							$down_info=$wpdb->get_row("select * from ".$wpdb->icealipay." where ice_post='".get_the_ID()."' and ice_success=1 and (ice_index is null or ice_index = '') and ice_user_id=".$current_user->ID." order by ice_time desc");

							if(strtotime($nowDate) > strtotime($lastDownDate) || $down_info){
								$tuanHtml .= '<a href="javascript:;" data-num="'.$tnum.'" data-tui="'.$down_tuan_tui.'" data-post="'.get_the_ID().'" class="erphpdown-tuituan">'.__('我要退团','mobantu').'</a>';
							}
						}
					}else{
						$tuanHtml .= '<div class="erphpdown-box-tips">'.sprintf( __('此资源拼团价格为<span class="erphpdown-price">%s</span>%s','mobantu'),$down_tuan_price,get_option("ice_name_alipay") ).'</div><a href="javascript:;" class="erphpdown-down erphpdown-btn-large erphpdown-tuan-loader" data-post="'.get_the_ID().'" data-num="'.$tnum.'">'.__('立即参团','mobantu').'</a>';
					}
				}
			}
			$tuanHtml .= $process;
		}
		$tuanHtml .= '<div class="erphpdown-tips">'.$down_tuan_desc.'</div>';
		$tuanHtml .= '</div>';
	}
	return $tuanHtml;
}

function get_erphpdown_tuan_num($post_id){
	global $wpdb;
	if(epd_tuan_active()){
		$tuan_num = $wpdb->get_var("select ice_num from ".$wpdb->prefix."ice_tuan where ice_post=".$post_id." and ice_status=0");
		if($tuan_num){
			return $tuan_num;
		}else{
			$tuan_num = date("ymdhis").mt_rand(100,999).mt_rand(100,999).mt_rand(100,999);
			$result = $wpdb->query("insert into ".$wpdb->prefix."ice_tuan (ice_num,ice_post) values ('".$tuan_num."',$post_id)");
			if($result){
				return $tuan_num;
			}else{
				return '';
			}
		}
	}
	return '';
}

function get_erphpdown_tuan_percent($post_id,$tuan_num){
	global $wpdb;
	if(epd_tuan_active()){
		$down_tuan_one = get_post_meta($post_id,'down_tuan_one',true);
		$down_tuan_num = get_post_meta($post_id,'down_tuan_num',true);
		$success = $wpdb->get_var("select ice_id from ".$wpdb->prefix."ice_tuan where ice_post='".get_the_ID()."' and ice_status=1");
		if($down_tuan_one && $success){
			return 100;
		}else{
			$count = $wpdb->get_var("select count(ice_id) from ".$wpdb->prefix."ice_tuan_order where ice_post = ".$post_id." and ice_tuan_num='".$tuan_num."' and ice_status>0");
			if($down_tuan_num){
				return sprintf("%.2f", $count/$down_tuan_num)*100;
			}
		}
	}
	return 0;
}

function get_erphpdown_tuan_count($post_id,$tuan_num){
	global $wpdb;
	if(epd_tuan_active()){
		$down_tuan_num = get_post_meta($post_id,'down_tuan_num',true);
		$count = $wpdb->get_var("select count(ice_id) from ".$wpdb->prefix."ice_tuan_order where ice_post = ".$post_id." and ice_tuan_num='".$tuan_num."' and ice_status>0");
	}
	return $count;
}

function get_erphpdown_tuan_avatars($post_id,$tuan_num){
	global $wpdb;
	$html = '';
	$users = $wpdb->get_results("select ice_user_id from ".$wpdb->prefix."ice_tuan_order where ice_post = ".$post_id." and ice_tuan_num='".$tuan_num."' and ice_status>0");
	if($users){
		$html .= '<div class="avatars">';
		foreach ($users as $user) {
			$html .= get_avatar($user->ice_user_id);
		}
		$html .= '</div>';
	}
	return $html;
}

function epd_tuan_callback(){
	global $wpdb, $current_user;
	date_default_timezone_set('Asia/Shanghai');
	$post_id = esc_sql($_POST['post_id']);
	$tuan_num = esc_sql($_POST['tuan_num']);
	if(epd_tuan_active()){
		$user_id = $current_user->ID;
		$tuan_price = get_post_meta($post_id,'down_tuan_price',true);
		$down_tuan_one = get_post_meta($post_id,'down_tuan_one',true);
		$reload=0;$msg='';$status=400;
		$out_trade_no = date("ymdhis").mt_rand(100,999).mt_rand(100,999).mt_rand(100,999);

		$down_info=$wpdb->get_row("select * from ".$wpdb->icealipay." where ice_post='".$post_id."' and ice_success=1 and (ice_index is null or ice_index = '') and ice_user_id=".$current_user->ID." order by ice_time desc");
		if($down_info){
			$status=201;
			$msg = __('抱歉，您已单独购买，可直接下载，不用参团','mobantu');
			$reload = 1;
		}else{
			$success = $wpdb->get_var("select ice_id from ".$wpdb->prefix."ice_tuan where ice_post='".$post_id."' and ice_status=1");
			if($down_tuan_one && $success){
				$status=201;
				$msg = __('抱歉，拼团已结束，暂时无法参团','mobantu');
				$reload = 1;
			}else{
				if($tuan_price){
					$can = $wpdb->get_var("select ice_id from ".$wpdb->prefix."ice_tuan where ice_num='".$tuan_num."' and ice_status=0");
					if($can){
						$okMoney = erphpGetUserOkMoney();
						if($okMoney >= $tuan_price){
							addUserMoney($user_id, '-'.$tuan_price);
							if(function_exists('addUserMoneyLog')){
				              addUserMoneyLog($user_id, '-'.$tuan_price, __('团购资源','mobantu'));
				            }

							$re = $wpdb->query("insert into ".$wpdb->prefix."ice_tuan_order (ice_num,ice_tuan_num,ice_post,ice_user_id,ice_price,ice_status,ice_time) values ('".$out_trade_no."','".$tuan_num."',$post_id,$user_id,'".$tuan_price."',1,'".date("Y-m-d H:i:s")."')");
							if($re){
								$status=200;
								$percent = get_erphpdown_tuan_percent($post_id,$tuan_num);
								if($percent >= 100){
									if($down_tuan_one) update_post_meta($post_id,'down_tuan_success',1);
									$wpdb->query("update ".$wpdb->prefix."ice_tuan set ice_status=1 where ice_num='".$tuan_num."'");
									$wpdb->query("update ".$wpdb->prefix."ice_tuan_order set ice_status=2 where ice_tuan_num='".$tuan_num."' and ice_status=1");
								}
							}
							
						}else{
							$status=201;
							$msg = __('余额不足，请先充值','mobantu');
						}	
					}else{
						$status=201;
						$msg = __('抱歉，此团已结束，请重新开团','mobantu');
						$reload = 1;
					}	
					
				}
			}
		}
	}

	$result = array(
		'status' => $status,
		'reload' => $reload,
		'msg' => $msg
	);

	header('Content-type: application/json');
	echo json_encode($result);
	exit;
}
add_action( 'wp_ajax_epd_tuan', 'epd_tuan_callback');

function epd_tuituan_callback(){
	global $wpdb, $current_user;
	$post_id = esc_sql($_POST['post_id']);
	$tuan_num = esc_sql($_POST['tuan_num']);
	$down_tuan_days=get_post_meta($post_id, 'down_tuan_days', true);

	$reload=0;$msg='';$status=400;
	if(epd_tuan_active()){
		if($down_tuan_days > 0){
			$tuan_time = $wpdb->get_var("select ice_time from ".$wpdb->prefix."ice_tuan_order where ice_user_id=".$current_user->ID." and ice_post=".$post_id." and ice_status>0");
			if($tuan_time){
				$lastDownDate = date('Y-m-d H:i:s',strtotime('+'.$down_tuan_days.' day',strtotime($tuan_time)));
				$nowDate = date('Y-m-d H:i:s');

				$down_info=$wpdb->get_row("select * from ".$wpdb->icealipay." where ice_post='".$post_id."' and ice_success=1 and (ice_index is null or ice_index = '') and ice_user_id=".$current_user->ID." order by ice_time desc");

				if(strtotime($nowDate) > strtotime($lastDownDate) || $down_info){

					$can = $wpdb->get_var("select ice_id from ".$wpdb->prefix."ice_tuan where ice_num='".$tuan_num."' and ice_status=0");
					if($can){
						$tui = get_post_meta($post_id,"down_tuan_tui",true);
						$price = $wpdb->get_var("select ice_price from ".$wpdb->prefix."ice_tuan_order where ice_tuan_num='".$tuan_num."' and ice_user_id=".$current_user->ID." and ice_status=1");
						$result = $wpdb->query("delete from ".$wpdb->prefix."ice_tuan_order where ice_tuan_num='".$tuan_num."' and ice_user_id=".$current_user->ID);
						if($result){
							addUserMoney($current_user->ID,$price*(100-$tui)/100);
							if(function_exists('addUserMoneyLog')){
				              addUserMoneyLog($current_user->ID, $price*(100-$tui)/100, __('退团返回','mobantu'));
				            }
							$status=200;
						}else{
							$status=201;
							$msg = __('退团失败，请稍后重试！','mobantu');
						}
					}else{
						$status=201;
						$msg = __('抱歉，此团已完成，无法退团','mobantu');
						$reload = 1;
					}
				}else{
					$status=201;
					$msg = __('退团失败，请稍后重试！','mobantu');
				}
			}else{
				$status=201;
				$msg = __('退团失败，请稍后重试！','mobantu');
			}
		}else{
			$status=201;
			$msg = __('退团失败，请稍后重试！','mobantu');
		}
	}

	$result = array(
		'status' => $status,
		'reload' => $reload,
		'msg' => $msg
	);

	header('Content-type: application/json');
	echo json_encode($result);
	exit;
}
add_action( 'wp_ajax_epd_tuituan', 'epd_tuituan_callback');

function epd_tuan_active(){
	return true;
}

function erphpdown_tuan_metaboxs() {
	$tuan_boxes = array(
		array(
			"name"             => "down_tuan",
			"title"            => "拼团",
			"desc"             => "Erphpdown属性里的收费模式选下载，单价格设置一个收费价格，否则可绕过拼团直接下载",
			"type"             => "radio",
			'options' => array(
				'0' => '关闭',
	            '2' => '开启并关闭单独购买',
	            '1' => '开启(同时可单独购买)'
	        ),
	        'default' => '0',
			"capability"       => "manage_options"
		),
		array(
			"name"             => "down_tuan_price",
			"title"            => "拼团价",
			"desc"             => "",
			"type"             => "number",
			'default'          => '',
			"required"         => "0",
			"capability"       => "manage_options"
		),
		array(
			"name"             => "down_tuan_num",
			"title"            => "拼团人数",
			"desc"             => "设置后请不要随意更改，若需修改，必须修改数大于当前已参团人数",
			"type"             => "number",
			'default'          => '',
			"required"         => "0",
			"capability"       => "manage_options"
		),
		array(
			"name"             => "down_tuan_desc",
			"title"            => "拼团说明",
			"desc"             => "留空则显示默认说明",
			"type"             => "textarea",
			'default'          => '',
			"required"         => "0",
			"capability"       => "manage_options"
		),
		array(
			"name"             => "down_tuan_tui",
			"title"            => "退团扣除(%)",
			"desc"             => "退团扣除的百分比",
			"type"             => "number",
			'default'          => '',
			"required"         => "0",
			"capability"       => "manage_options"
		),
		array(
			"name"             => "down_tuan_days",
			"title"            => "退团限定天数",
			"desc"             => "参团多少天以后可以退团",
			"type"             => "number",
			'default'          => '',
			"required"         => "0",
			"capability"       => "manage_options"
		),
		array(
			"name"             => "down_tuan_one",
			"title"            => "仅限团一次",
			"desc"             => "此资源仅可被拼团成功一次，之后就不再允许拼团",
			"type"             => "checkbox",
			'default'          => '',
			"required"         => "0",
			"capability"       => "manage_options"
		)
		/*array(
			"name"             => "down_tuan_mail",
			"title"            => "邮件提醒",
			"desc"             => "拼团成功后每次更新下载地址时勾选，可邮件通知已拼团成功的用户",
			"type"             => "checkbox",
			'default'          => '',
			"required"         => "0",
			"capability"       => "manage_options"
		)*/
	);
	return $tuan_boxes;
}

function erphpdown_tuan_show_metabox() {
	global $post;
	$tuan_boxes = erphpdown_tuan_metaboxs(); 
	?>
	<table class="form-table">
		<?php 
		foreach ( $tuan_boxes as $meta ) :
			$value = get_post_meta( $post->ID, $meta['name'], true );
			if ( $meta['type'] == 'text' )
				erphpdown_tuan_show_text( $meta, $value );
			elseif ( $meta['type'] == 'number' )
				erphpdown_tuan_show_number( $meta, $value );
			elseif ( $meta['type'] == 'textarea' )
				erphpdown_tuan_show_textarea( $meta, $value );
			elseif ( $meta['type'] == 'checkbox' )
				erphpdown_tuan_show_checkbox( $meta, $value );
			elseif ($meta['type'] == 'radio')
				erphpdown_tuan_show_radio( $meta, $value );
		endforeach; 
		?>
	</table>
	<?php
	echo '<input type="hidden" name="tuan_metaboxes_noncename" id="tuan_metaboxes_noncename" value="'.wp_create_nonce( plugin_basename(__FILE__) ).'" />';
}

function erphpdown_tuan_show_radio( $args = array(), $value = false ) {
	extract( $args ); ?>
	<div class="erphpdown-metabox-item">
		<label class="title"><?php echo $title; ?></label>
		<?php
			$i=1;
            foreach ($options as $key => $option) {
            	if(!$value) $value=$default;
                echo '<span><input type="radio" name="'.$name.'" id="'.$name.$i.'" value="'. esc_attr( $key ) . '" '. checked( $value, $key, false) .'/><label for="'.$name.$i.'">' . esc_html( $option ) . '</label>&nbsp;&nbsp;&nbsp;&nbsp;</span>';
                $i ++;
            }
        ?>
		<input type="hidden" name="<?php echo $name; ?>_input_name" id="<?php echo $name; ?>_input_name" value="<?php echo wp_create_nonce( plugin_basename( __FILE__ ) ); ?>" />
		<br />
		<p class="description"><?php echo $desc; ?></p>
	</div>
	<?php
}

function erphpdown_tuan_show_checkbox( $args = array(), $value = false ) {
	extract( $args ); ?>
	<div class="erphpdown-metabox-item">
		<label class="title"><?php echo $title; ?></label>
		<input type="checkbox" name="<?php echo $name; ?>" id="<?php echo $name; ?>" value="1"
		<?php if ( htmlentities( $value, 1 ) == '1' ) echo ' checked="checked"'; ?>
		style="width: auto;" />
		<input type="hidden" name="<?php echo $name; ?>_input_name" id="<?php echo $name; ?>_input_name" value="<?php echo wp_create_nonce( plugin_basename( __FILE__ ) ); ?>" />
		<p class="description"><?php echo $desc; ?></p>
	</div>
<?php }

function erphpdown_tuan_show_text( $args = array(), $value = false ) {
	extract( $args ); ?>
	<div class="erphpdown-metabox-item">
		<label class="title"><?php echo $title; ?></label>
		<input type="text" name="<?php echo $name; ?>" id="<?php echo $name; ?>" value="<?php echo esc_html( $value, 1 ); ?>" style="width: 100%;" />
		<input type="hidden" name="<?php echo $name; ?>_input_name" id="<?php echo $name; ?>_input_name" value="<?php echo wp_create_nonce( plugin_basename( __FILE__ ) ); ?>" />
		<br />
		<p class="description"><?php echo $desc; ?></p>
	</div>
	<?php
}

function erphpdown_tuan_show_number( $args = array(), $value = false ) {
	extract( $args ); if(!$value) $value=$default; ?>
	<div class="erphpdown-metabox-item">
		<label class="title"><?php echo $title; ?></label>
		<input type="number" min="0" step="1" name="<?php echo $name; ?>" id="<?php echo $name; ?>" value="<?php echo esc_html( $value, 1 ); ?>" style="width: 100px;" <?php if($required) echo 'required';?>/>
		<input type="hidden" name="<?php echo $name; ?>_input_name" id="<?php echo $name; ?>_input_name" value="<?php echo wp_create_nonce( plugin_basename( __FILE__ ) ); ?>" />
		<br />
		<p class="description"><?php echo $desc; ?></p>
	</div>
	<?php
}

function erphpdown_tuan_show_textarea( $args = array(), $value = false ) {
	extract( $args ); ?>
	<div class="erphpdown-metabox-item">
		<label class="title"><?php echo $title; ?></label>
		<textarea name="<?php echo $name; ?>" id="<?php echo $name; ?>" cols="60" rows="4" tabindex="30" style="width: 100%;"><?php echo esc_html( $value, 1 ); ?></textarea>
		<input type="hidden" name="<?php echo $name; ?>_input_name" id="<?php echo $name; ?>_input_name" value="<?php echo wp_create_nonce( plugin_basename( __FILE__ ) ); ?>" />
		<br />
		<p class="description"><?php echo $desc; ?></p>	
	</div>
	<?php
}

add_action( 'admin_menu', 'erphpdown_tuan_create_metabox' );
add_action( 'save_post', 'erphpdown_tuan_save_metabox' );

function erphpdown_tuan_create_metabox() {
	$erphp_post_types = get_option('erphp_post_types');
	$args = array(
		'public'   => true,
	);
	$post_types = get_post_types($args);
	foreach ( $post_types  as $post_type ) {
		if($erphp_post_types){
			if(in_array($post_type,$erphp_post_types)) add_meta_box( 'erphpdown-tuan-postmeta-box','Erphpdown团购属性', 'erphpdown_tuan_show_metabox', $post_type, 'normal', '' );
		}
	}
}

function erphpdown_tuan_save_metabox( $post_id ) {

	if(!isset($_POST['tuan_metaboxes_noncename']))
        return;
    if ( !current_user_can( 'edit_posts', $post_id ))
        return; 

	$tuan_boxes = array_merge( erphpdown_tuan_metaboxs() );
	foreach ( $tuan_boxes as $meta_box ) :

		if($meta_box['name'] != 'down_tuan_mail'){
			$data = $_POST[$meta_box['name']];
	        if(get_post_meta($post_id, $meta_box['name']) == "")
	            add_post_meta($post_id, $meta_box['name'], $data, true);
	        elseif($data != get_post_meta($post_id, $meta_box['name'], true))
	            update_post_meta($post_id, $meta_box['name'], $data);
	        elseif($data == "")
	            delete_post_meta($post_id, $meta_box['name'], get_post_meta($post_id, $meta_box['name'], true));
        }	
		
	endforeach;
}

//add_action('admin_menu', 'modown_tuan_menu'); //菜单移动到erphpdown里
function modown_tuan_menu() {
	add_management_page('团购订单', '团购订单', 'activate_plugins', 'modown_tuan_list', 'modown_tuan_list');
}

function modown_tuan_list(){
	global $wpdb;
	$total_trade   = $wpdb->get_var("SELECT COUNT(ice_id) FROM ".$wpdb->prefix."ice_tuan_order WHERE ice_status>0");
	$ice_perpage = 20;
	$pages = ceil($total_trade / $ice_perpage);
	$page=isset($_GET['paged']) ?intval($_GET['paged']) :1;
	$offset = $ice_perpage*($page-1);
	$list = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."ice_tuan_order where ice_status>0 order by ice_time DESC limit $offset,$ice_perpage");
?>
<div class="wrap">
	<h2>所有拼团统计</h2>
	<table class="widefat fixed striped posts">
		<thead>
			<tr>
				<th>用户ID</th>
				<th>团号</th>
				<th>单号</th>
				<th>名称</th>
				<th>价格</th>
				<th>时间</th>	
				<th>状态</th>
				<th>进度</th>
				<th>管理</th>
			</tr>
		</thead>
		<tbody>
			<?php
			if($list) {
				foreach($list as $value)
				{

					echo "<tr>\n";
					echo "<td>".get_the_author_meta( 'user_login', $value->ice_user_id )."</td>";
					echo "<td>$value->ice_tuan_num</td>";
					echo "<td>$value->ice_num</td>";
					echo "<td><a target=_blank href='".get_permalink($value->ice_post)."'>".get_post($value->ice_post)->post_title."</a></td>\n";
					echo "<td>$value->ice_price</td>\n";
					echo "<td>$value->ice_time</td>\n";
					echo '<td>'.(($value->ice_status == '1')?'进行中':'已完成').'</td>';
					echo '<td>'.get_erphpdown_tuan_percent($value->ice_post,$value->ice_tuan_num).'%</td>';
					echo '<td><a href="javascript:;" class="delorder" data-id="'.$value->ice_id.'">删除</a></td>';
					echo "</tr>";
				}
			}
			else
			{
				echo '<tr><td colspan="9" align="center"><strong>没有拼团记录</strong></td></tr>';
			}
			?>
		</tbody>
	</table>
	<?php echo erphp_admin_pagenavi($total_trade,$ice_perpage);?>

</div>
<script>
	jQuery(".delorder").click(function(){
		if(confirm('确定删除？')){
			var that = jQuery(this);
			that.text("删除中...");
			jQuery.ajax({
				type: "post",
				url: "<?php echo constant("erphpdown");?>admin/action/order.php",
				data: "do=deltuan&id=" + jQuery(this).data("id"),
				dataType: "html",
				success: function (data) {
					if(jQuery.trim(data) == '1'){
						that.parent().parent().remove();
					}
				},
				error: function (request) {
					that.text("删除");
					alert("删除失败");
				}
			});
		}
	});
</script>
<?php
}


add_action('post_updated', 'modown_tuan_publish_post_mail', 100, 3 );
function modown_tuan_publish_post_mail($post_ID ,$post_after, $post_before){
	if((defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) || !current_user_can('edit_post', $post_ID)) return;
    if (wp_is_post_revision($post_ID) || wp_is_post_autosave($post_ID)) return;
	global $wpdb;
  	$ppost = get_post($post_ID);
  	$down_tuan_mail = isset($_POST['down_tuan_mail']) ? $_POST['down_tuan_mail'] : 0;
  	$down_url = get_post_meta($post_ID,'down_url',true);
  	if($down_tuan_mail){
	  	$results = $wpdb->get_results("select ice_user_id from ".$wpdb->prefix."ice_tuan_order where ice_post=".get_the_ID()." and ice_status = 2");
	  	if($results){
	  		$headers = 'Content-Type: text/html; charset=' . get_option('blog_charset') . "\n";
	  		$subject   = $ppost->post_title;
	  		foreach($results as $value){
	  			$cu = get_user_by('id',$value->ice_user_id);
	  			if($cu->user_email){
					wp_mail($cu->user_email, '【'.$subject.'】下载更新', '您团购的资源【'.$subject.'】下载地址更新：'.$down_url, $headers);
	  			}
	  		}
	  	}
  	}
}