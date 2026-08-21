<?php
// +----------------------------------------------------------------------
// | ERPHP [ PHP DEVELOP ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.mobantu.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: mobantu <82708210@qq.com>
// +----------------------------------------------------------------------

if(isset($_REQUEST['aff']) && !isset($_COOKIE["erphprefid"])){
	setcookie("erphprefid", $_REQUEST['aff'], time()+2592000, '/');
}

$ice_proportion_alipay = get_option('ice_proportion_alipay');
if(!$ice_proportion_alipay){
	add_action( 'admin_notices', 'erphpdown_admin_notice' );
}

function erphpdown_style() {
	global $erphpdown_version;
	wp_enqueue_style( 'erphpdown', constant("erphpdown")."static/erphpdown.css", array(), $erphpdown_version,'screen' );
	// Mizuki 皮肤层：依赖原样式在其之后加载，只覆盖视觉（图标字体/layui 引擎不动）
	wp_enqueue_style( 'erphpdown-mizuki', constant("erphpdown")."static/erphpdown-mizuki.css", array('erphpdown'), $erphpdown_version,'screen' );
	wp_enqueue_script('jquery');
	wp_enqueue_script( 'erphpdown', constant("erphpdown")."static/erphpdown.js", array('jquery'), $erphpdown_version, true);
	wp_localize_script( 'erphpdown', '_ERPHP', array(
		'ajaxurl' => admin_url("admin-ajax.php"),
		'checkin_nonce' => wp_create_nonce( 'erphp-checkin' ),
	));
}
add_action('wp_enqueue_scripts', 'erphpdown_style',20,1);

add_action( 'wp_head', 'erphpdown_head_style' );
function erphpdown_head_style(){
	$erphp_box_style = get_option('erphp_box_style');
?>
<style id="erphpdown-custom"><?php if($erphp_box_style == '1'){?>.erphpdown-default, .erphpdown-see{padding: 15px 25px !important;border: none !important;background: #f5f5f5;}.erphpdown-default > legend{display: none !important;}.erphpdown-default .erphpdown-child{border: none !important;background: #e8e8e8;padding: 25px 15px 15px !important;margin: 10px 0 !important;}.erphpdown-default .erphpdown-child > legend{position: absolute;top: 0;background: #d4d4d4;color: #444;padding: 0 8px !important}<?php }?><?php echo get_option('erphp_custom_css');?></style>
<?php 
}

add_action( 'wp_footer', 'erphpdown_foot_script' );
function erphpdown_foot_script(){
	$erphp_wppay_payment = get_option('erphp_wppay_payment');
?>
<script>window._ERPHPDOWN = {"uri":"<?php echo ERPHPDOWN_URL;?>", "payment": "<?php if($erphp_wppay_payment == 'f2fpay') echo "1";elseif($erphp_wppay_payment == 'f2fpay_weixin') echo "4";elseif($erphp_wppay_payment == 'f2fpay_hupiv3') echo "4";elseif($erphp_wppay_payment == 'weixin_hupiv3') echo "4";elseif($erphp_wppay_payment == 'weixin') echo "3";elseif($erphp_wppay_payment == 'paypy' || $erphp_wppay_payment == 'vpay' || $erphp_wppay_payment == 'f2fpay_paypy') echo "6";elseif($erphp_wppay_payment == 'hupiv3') echo "5";elseif($erphp_wppay_payment == 'payjs') echo "5"; else echo "2";?>", "wppay": "<?php echo get_option('erphp_wppay_type');?>", "tuan":"<?php if(plugin_check_tuan()) echo '1';?>", "author": "mobantu"}</script>
<?php
}

add_action('user_register', 'erphp_register_extra_fields');
function erphp_register_extra_fields($user_id, $password="", $meta=array()) {
	global $wpdb;
	$erphp_aff_ip = get_option('erphp_aff_ip');
	if($erphp_aff_ip){
		$ice_ali_money_reg = get_option('ice_ali_money_reg');
		$ice_ali_money_new = get_option('ice_ali_money_new');
		$ice_ali_vip_new    = get_option('ice_ali_vip_new');
		if($ice_ali_money_new){
			addUserMoney($user_id,$ice_ali_money_new, '注册奖励');
		}

		if($ice_ali_vip_new){
			addUserMoney($user_id,'0');
			if(userSetMemberSetData($ice_ali_vip_new,$user_id)){
				addVipLogByAdmin(0, $ice_ali_vip_new, $user_id);
			}
		}

		if(isset($_COOKIE["erphprefid"]) && is_numeric($_COOKIE["erphprefid"])){
			$sql = "update $wpdb->users set father_id='".esc_sql($_COOKIE["erphprefid"])."',reg_ip = '".erphpGetIP()."' where ID=".$user_id;
			$wpdb->query($sql);
			if($ice_ali_money_reg){
				addUserMoney($_COOKIE["erphprefid"], $ice_ali_money_reg, '推广奖励');
			}
		}
	}else{
		$ice_ali_vip_new    = get_option('ice_ali_vip_new');
		if($ice_ali_vip_new){
			addUserMoney($user_id,'0');
			if(userSetMemberSetData($ice_ali_vip_new,$user_id)){
				addVipLogByAdmin(0, $ice_ali_vip_new, $user_id);
			}
		}

		// 本地测试豁免：localhost 注册不做同 IP 防刷，便于本地反复验收注册奖励（线上真实 IP 完全不受影响）
		$erphp_reg_ip = erphpGetIP();
		$erphp_is_local_reg = in_array($erphp_reg_ip, array('127.0.0.1', '::1', 'localhost'), true);
		$hasRe = $erphp_is_local_reg ? null : $wpdb->get_row("select ID,father_id from $wpdb->users where reg_ip = '".erphpGetIP()."'");
		$ice_ali_money_reg = get_option('ice_ali_money_reg');
		if($hasRe){
			$sql = "update $wpdb->users set reg_ip = '".erphpGetIP()."' where ID=".$user_id;
			$wpdb->query($sql);

			if(!$hasRe->father_id){
				if(isset($_COOKIE["erphprefid"]) && is_numeric($_COOKIE["erphprefid"])){
					$sql = "update $wpdb->users set father_id='".esc_sql($_COOKIE["erphprefid"])."',reg_ip = '".erphpGetIP()."' where ID=".$user_id;
					$wpdb->query($sql);
					if($ice_ali_money_reg){
						addUserMoney($_COOKIE["erphprefid"], $ice_ali_money_reg, '推广奖励');
					}
				}
			}
		}else{
			$ice_ali_money_new = get_option('ice_ali_money_new');
			if($ice_ali_money_new){
				addUserMoney($user_id,$ice_ali_money_new, '注册奖励');
			}

			if(isset($_COOKIE["erphprefid"]) && is_numeric($_COOKIE["erphprefid"])){
				$sql = "update $wpdb->users set father_id='".esc_sql($_COOKIE["erphprefid"])."',reg_ip = '".erphpGetIP()."' where ID=".$user_id;
				$wpdb->query($sql);
				if($ice_ali_money_reg){
					addUserMoney($_COOKIE["erphprefid"], $ice_ali_money_reg, '推广奖励');
				}
			}
		}
	}
}

add_action("init","erphpdown_noadmin_redirect");
function erphpdown_noadmin_redirect(){
	global $wpdb;
	if ( is_admin() && ( !defined( 'DOING_AJAX' ) || !DOING_AJAX ) && get_option('erphp_url_front_noadmin')=='yes') {
	  	$current_user = wp_get_current_user();
	  	if($current_user->roles[0] == get_option('default_role')) {
			$userpage = get_bloginfo('url');
			if(get_option('erphp_url_front_userpage')){
				$userpage = get_option('erphp_url_front_userpage');
			}
			wp_redirect( $userpage );
	  	}
	}
}


add_filter('user_contactmethods','erphpdown_new_contactmethods',10,1); 
function erphpdown_new_contactmethods( $contactmethods ) { 
	if(current_user_can('administrator')){
		$contactmethods['ice_ali_money_ref'] = '推广提成（百分点，填整数，不填则默认erphpdown的设置）'; 
		$contactmethods['ice_ali_money_ref2'] = '二级推广提成（百分点，填整数，不填则默认erphpdown的设置）'; 
		$contactmethods['ice_ali_money_author'] = '作者分成（百分点，填整数，不填则默认erphpdown的设置）'; 
		$contactmethods['ice_ali_money_site'] = '提现手续费（百分点，填整数，不填则默认erphpdown的设置）'; 
	}
	return $contactmethods; 
} 

function erphpdown_admin_notice() {
    ?>
    <br>
    <div id="message" class="error updated notice is-dismissible">
        <p>Erphpdown插件需要先设置下哦！<a href="<?php echo admin_url().'admin.php?page=erphpdown/admin/erphp-settings.php';?>">去设置</a></p>
        <button type="button" class="notice-dismiss"><span class="screen-reader-text">忽略此通知。</span></button>
    </div>
    <?php
}


function erphp_admin_pagenavi($total_count, $number_per_page=15){

	$current_page = isset($_GET['paged'])?$_GET['paged']:1;

	if(isset($_GET['paged'])){
		unset($_GET['paged']);
	}

	$base_url = add_query_arg($_GET,admin_url('admin.php'));

	$total_pages	= ceil($total_count/$number_per_page);

	$first_page_url	= $base_url.'&amp;paged=1';
	$last_page_url	= $base_url.'&amp;paged='.$total_pages;
	
	if($current_page > 1 && $current_page < $total_pages){
		$prev_page		= $current_page-1;
		$prev_page_url	= $base_url.'&amp;paged='.$prev_page;

		$next_page		= $current_page+1;
		$next_page_url	= $base_url.'&amp;paged='.$next_page;
	}elseif($current_page == 1){
		$prev_page_url	= '#';
		$first_page_url	= '#';
		if($total_pages > 1){
			$next_page		= $current_page+1;
			$next_page_url	= $base_url.'&amp;paged='.$next_page;
		}else{
			$next_page_url	= '#';
		}
	}elseif($current_page == $total_pages){
		$prev_page		= $current_page-1;
		$prev_page_url	= $base_url.'&amp;paged='.$prev_page;
		$next_page_url	= '#';
		$last_page_url	= '#';
	}
	?>
	<div class="tablenav bottom">
		<div class="tablenav-pages">
			<span class="displaying-num">每页 <?php echo $number_per_page;?> 共 <?php echo $total_count;?></span>
			<span class="pagination-links">
				<a class="first-page button <?php if($current_page==1) echo 'disabled'; ?>" title="前往第一页" href="<?php echo $first_page_url;?>">«</a>
				<a class="prev-page button <?php if($current_page==1) echo 'disabled'; ?>" title="前往上一页" href="<?php echo $prev_page_url;?>">‹</a>
				<span class="paging-input">第 <?php echo $current_page;?> 页，共 <span class="total-pages"><?php echo $total_pages; ?></span> 页</span>
				<a class="next-page button <?php if($current_page==$total_pages) echo 'disabled'; ?>" title="前往下一页" href="<?php echo $next_page_url;?>">›</a>
				<a class="last-page button <?php if($current_page==$total_pages) echo 'disabled'; ?>" title="前往最后一页" href="<?php echo $last_page_url;?>">»</a>
			</span>
		</div>
		<br class="clear">
	</div>
	<?php
}

add_filter('admin_footer_text', 'erphp_left_admin_footer_text'); 
function erphp_left_admin_footer_text($text) {
	$text = '<span id="footer-thankyou">感谢使用<a href=http://cn.wordpress.org/ >WordPress</a>进行创作，使用<a href="http://www.erphpdown.com">Erphpdown</a>进行网站VIP支付下载功能。</span>'; 
	return $text;
}

function erphpdown_paging($paged,$max_page,$type='') {
	if ( $max_page <= 1 ) return; 
	if ( empty( $paged ) ) $paged = 1;
	$html = '';
	$html .= '<div class="erphpdown-pagination"><ul>';
	$html .= "<li><a class=extend href='".add_query_arg(array("pp"=>1,"pd"=>$type),get_permalink())."'>".__("首页",'erphpdown')."</a></li>";
	if($paged > 1){
		$html .= '<li class="prev-page"><a href="'.add_query_arg(array("pp"=>$paged-1,"pd"=>$type),get_permalink()).'">'.__("上一页",'erphpdown').'</a></li>';
	}
	if ( $paged > 2 ) $html .= "<li><span> ... </span></li>";
	for( $i = $paged - 1; $i <= $paged + 3; $i++ ) { 
		if ( $i > 0 && $i <= $max_page ) {
			if($i == $paged) 
				$html .= "<li class=\"active\"><span>{$i}</span></li>";
			else
				$html .= "<li><a href='".add_query_arg(array("pp"=>$i,"pd"=>$type),get_permalink())."'><span>{$i}</span></a></li>";
		}
	}
	if ( $paged < $max_page - 3 ) $html .= "<li><span> ... </span></li>";
	if($paged < $max_page){
		$html .= '<li class="next-page"><a href="'.add_query_arg(array("pp"=>$paged+1,"pd"=>$type),get_permalink()).'">'.__("下一页",'erphpdown').'</a></li>';
	}
	$html .= "<li><a class=extend href='".add_query_arg(array("pp"=>$max_page,"pd"=>$type),get_permalink())."'>".__("尾页",'erphpdown')."</a></li>";
	$html .= '</ul></div>';
	return $html;
}

function erphpGetIP(){
    if (@$_SERVER["HTTP_X_FORWARDED_FOR"])
		$ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
	elseif (@$_SERVER["HTTP_CLIENT_IP"])
		$ip = $_SERVER["HTTP_CLIENT_IP"];
	elseif (@$_SERVER["REMOTE_ADDR"])
		$ip = $_SERVER["REMOTE_ADDR"];
	elseif (@getenv("HTTP_X_FORWARDED_FOR"))
		$ip = getenv("HTTP_X_FORWARDED_FOR");
	elseif (@getenv("HTTP_CLIENT_IP"))
		$ip = getenv("HTTP_CLIENT_IP");
	elseif (@getenv("REMOTE_ADDR"))
		$ip = getenv("REMOTE_ADDR");
	else
		$ip = "";

	return esc_sql($ip);
}

function erphpdown_encrypt($string,$operation,$key=''){ 
    $key=md5($key); 
    $key_length=strlen($key); 
      $string=$operation=='D'?base64_decode($string):substr(md5($string.$key),0,8).$string; 
    $string_length=strlen($string); 
    $rndkey=$box=array(); 
    $result=''; 
    for($i=0;$i<=255;$i++){ 
           $rndkey[$i]=ord($key[$i%$key_length]); 
        $box[$i]=$i; 
    } 
    for($j=$i=0;$i<256;$i++){ 
        $j=($j+$box[$i]+$rndkey[$i])%256; 
        $tmp=$box[$i]; 
        $box[$i]=$box[$j]; 
        $box[$j]=$tmp; 
    } 
    for($a=$j=$i=0;$i<$string_length;$i++){ 
        $a=($a+1)%256; 
        $j=($j+$box[$a])%256; 
        $tmp=$box[$a]; 
        $box[$a]=$box[$j]; 
        $box[$j]=$tmp; 
        $result.=chr(ord($string[$i])^($box[($box[$a]+$box[$j])%256])); 
    } 
    if($operation=='D'){ 
        if(substr($result,0,8)==substr(md5(substr($result,8).$key),0,8)){ 
            return substr($result,8); 
        }else{ 
            return''; 
        } 
    }else{ 
        return str_replace('=','',base64_encode($result)); 
    } 
}

function erphpdown_lock_url($str,$key){
	return erphpdown_encrypt($str,'E',$key);
}

function erphpdown_unlock_url($str,$key){
	return erphpdown_encrypt($str,'D',$key);
}

function erphpdown_file_post($url = '', $postData = ''){
	$data = http_build_query($postData);
	$opts = array(
	   'http'=>array(
	     'method'=>"POST",
	     'header'=>"Content-type: application/x-www-form-urlencoded\r\n".
	               "Content-length:".strlen($data)."\r\n" .
	               "Cookie: foo=bar\r\n" .
	               "\r\n",
	     'content' => $data,
	   )
	);
	$cxContext = stream_context_create($opts);
	$result = file_get_contents($url, false, $cxContext);
	return $result;
}

//url,请求路径，postdata 请求body参数，注意是json格式
function erphpdown_curl_post_body($url,$postdata){
    # 初始化一个curl会话
    $ch = curl_init();
	# 启用时会发送一个常规的POST请求
    curl_setopt($ch, CURLOPT_POST, 1);
	# 需要获取的URL地址
    curl_setopt($ch, CURLOPT_URL, $url);
	# 全部数据使用HTTP协议中的"POST"操作来发送。
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
	# 将curl_exec()获取的信息以文件流的形式返回，而不是直接输出。
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	# 一个用来设置HTTP请求头字段的数组
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            # 设置编码格式
            'Content-Type: application/json;charset=utf-8',
            'Content-Length: ' . strlen($postdata)
        )
   	);
	# curl_exec 执行一个cURL会话。
    $response = curl_exec($ch);
	# 关闭一个cURL会话
    curl_close($ch);
    return $response;
}


function erphpdown_curl_post($url = '', $postData = ''){
	if(function_exists('curl_init')){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
	}else{
		wp_die("网站未开启curl组件，正常情况下该组件必须开启，请开启curl组件解决该问题");
	}
} 

function erphpdown_http_post($url, $data) {  
  $ch = curl_init();  
  curl_setopt($ch, CURLOPT_URL,$url);  
  curl_setopt($ch, CURLOPT_HEADER,0);  
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
  curl_setopt($ch, CURLOPT_POST, 1);  
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  //不验证 SSL 证书
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); //不验证 SSL 证书域名
  curl_setopt($ch, CURLOPT_POSTFIELDS, $data);  
  $res = curl_exec($ch);  
  curl_close($ch);  
  return $res;  
}

function erphpdown_replace_each(&$array){
   $res = array();
   $key = key($array);
   if($key !== null){
       next($array); 
       $res[1] = $res['value'] = $array[$key];
       $res[0] = $res['key'] = $key;
   }else{
       $res = false;
   }
   return $res;
}

function erphpdown_is_mobile(){  
    $_SERVER['ALL_HTTP'] = isset($_SERVER['ALL_HTTP']) ? $_SERVER['ALL_HTTP'] : '';  
    $mobile_browser = '0';  
  	if (isset($_SERVER['HTTP_USER_AGENT'])) {
      $clientkeywords = array('iphone', 'android', 'phone', 'mobile', 'wap', 'netfront', 'java', 'opera mobi', 'opera mini','ucweb', 'windows ce', 'symbian', 'series', 'webos', 'sony', 'blackberry', 'dopod', 'nokia', 'samsung','palmsource', 'xda', 'pieplus', 'meizu', 'midp', 'cldc', 'motorola', 'foma', 'docomo', 'up.browser','up.link', 'blazer', 'helio', 'hosin', 'huawei', 'xiaomi', 'novarra', 'coolpad', 'webos', 'techfaith', 'palmsource','alcatel', 'amoi', 'ktouch', 'nexian', 'ericsson', 'philips', 'sagem', 'wellcom', 'bunjalloo', 'maui', 'smartphone','iemobile', 'spice', 'bird', 'zte-', 'longcos', 'pantech', 'gionee', 'portalmmm', 'jig browser', 'hiptop','benq', 'haier', '^lct', '320x320', '240x320', '176x220', 'windows phone');
      if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
      	$mobile_browser++;  
      }
    }
    if(preg_match('/(up.browser|up.link|ucweb|mmp|symbian|smartphone|midp|wap|phone|iphone|ipad|ipod|android|xoom)/i', strtolower($_SERVER['HTTP_USER_AGENT'])))  
        $mobile_browser++;  
    if((isset($_SERVER['HTTP_ACCEPT'])) and (strpos(strtolower($_SERVER['HTTP_ACCEPT']),'application/vnd.wap.xhtml+xml') !== false))  
        $mobile_browser++;  
    if(isset($_SERVER['HTTP_X_WAP_PROFILE']))  
        $mobile_browser++;  
    if(isset($_SERVER['HTTP_PROFILE']))  
        $mobile_browser++;  
    $mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'],0,4));  
    $mobile_agents = array(  
        'w3c ','acs-','alav','alca','amoi','audi','avan','benq','bird','blac',  
        'blaz','brew','cell','cldc','cmd-','dang','doco','eric','hipt','inno',  
        'ipaq','java','jigs','kddi','keji','leno','lg-c','lg-d','lg-g','lge-',  
        'maui','maxo','midp','mits','mmef','mobi','mot-','moto','mwbp','nec-',  
        'newt','noki','oper','palm','pana','pant','phil','play','port','prox',  
        'qwap','sage','sams','sany','sch-','sec-','send','seri','sgh-','shar',  
        'sie-','siem','smal','smar','sony','sph-','symb','t-mo','teli','tim-',  
        'tosh','tsm-','upg1','upsi','vk-v','voda','wap-','wapa','wapi','wapp',  
        'wapr','webc','winw','winw','xda','xda-' 
        );  
    if(in_array($mobile_ua, $mobile_agents))  
        $mobile_browser++;  
    if(strpos(strtolower($_SERVER['ALL_HTTP']), 'operamini') !== false)  
        $mobile_browser++;  
    // Pre-final check to reset everything if the user is on Windows  
    if(strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows') !== false)  
        $mobile_browser=0;  
    // But WP7 is also Windows, with a slightly different characteristic  
    if(strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows phone') !== false)  
        $mobile_browser++;  
    if($mobile_browser>0)  
        return true;  
    else
        return false;  
}

function erphpdown_is_weixin(){ 
	if ( strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false ) {
        return true;
    }  
    return false;
}

function showMsgNotice($msg,$color=FALSE){
	echo '<div class="updated settings-error"><p>'.$msg.'</p></div>';
}

function plugin_check_card(){
	$erphp_addon_card = get_option('erphp_addon_card');
	if($erphp_addon_card){
		return 1;
	}
	return 0;
}

function plugin_check_vipcard(){
	$erphp_addon_vipcard = get_option('erphp_addon_vipcard');
	if($erphp_addon_vipcard){
		return 1;
	}
	return 0;
}

function plugin_check_cred(){
	$erphp_addon_mycred = get_option('erphp_mycred');
	if($erphp_addon_mycred){
		return 1;
	}
	return 0;
}

function plugin_check_activation(){
	$erphp_addon_activation = get_option('erphp_addon_activation');
	if($erphp_addon_activation){
		return 1;
	}
	return 0;
}

function plugin_check_pancheck(){
	$erphp_addon_pancheck = get_option('erphp_addon_pancheck');
	if($erphp_addon_pancheck){
		return 1;
	}
	return 0;
}

function plugin_check_video(){
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	if(!is_plugin_active( 'erphpdown-addon-video/erphpdown-addon-video.php' )){
		return 0;
	}
	else{
		return 1;
	}
}

function plugin_check_tuan(){
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	if(!is_plugin_active( 'erphpdown-addon-tuan/erphpdown-addon-tuan.php' )){
		$erphp_addon_tuan = get_option('erphp_addon_tuan');
		if($erphp_addon_tuan){
			return 1;
		}
		return 0;
	}else{
		return 1;
	}
}

function plugin_check_epay(){
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	if(!is_plugin_active( 'erphpdown-addon-epay/erphpdown-addon-epay.php' )){
		return 0;
	}
	else{
		return 1;
	}
}

function plugin_check_stripe(){
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	if(!is_plugin_active( 'erphpdown-addon-stripe/erphpdown-addon-stripe.php' )){
		return 0;
	}
	else{
		return 1;
	}
}

function plugin_check_ecpay(){
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	if(!is_plugin_active( 'erphpdown-addon-ecpay/erphpdown-addon-ecpay.php' )){
		return 0;
	}
	else{
		return 1;
	}
}

function plugin_check_newebpay(){
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	if(!is_plugin_active( 'erphpdown-addon-newebpay/erphpdown-addon-newebpay.php' )){
		return 0;
	}
	else{
		return 1;
	}
}

function erphpdown_selfurl(){  
	$pageURL = 'http';
  	$pageURL .= (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on")?"s":"";
  	$pageURL .= "://";
  	$pageURL .= $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
  	return $pageURL;   
}

function erphpdown_parent_cid($catid){
  while ($catid) {
    $cat = get_category($catid);
    $catid = $cat->category_parent;
    $catParent = $cat->cat_ID;
  }
  return $catParent;
}

function erphpdown_admin_load_scripts($hook) {
	//if( $hook != 'edit.php' ) 
	//	return;
	wp_enqueue_script( 'erphpdown-js', plugins_url( 'static/admin.js' , dirname(__FILE__) ) );
}
add_action('admin_enqueue_scripts', 'erphpdown_admin_load_scripts');