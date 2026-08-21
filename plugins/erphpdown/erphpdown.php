<?php
/*
	Plugin Name: ErphpDown
	Plugin URI: http://www.mobantu.com/1780.html
	Description: 会员推广下载专业版：支持在线支付(支付宝、微信支付、贝宝Paypal、信用卡Stripe)，用户推广、提现，发布收费下载与收费内容查看，VIP会员权限等功能的插件。
	Version: 17.1
	Author: 模板兔
	Author URI: http://www.mobantu.com
	Text Domain: erphpdown
	Domain Path: /lang
*/
if ( ! defined( 'ABSPATH' ) ) exit;
global $wpdb, $erphpdown_version, $wppay_table_name;
$erphpdown_version = '17.1';//请勿随意修改，否则可能出错
$wpdb->icealipay = $wpdb->prefix.'ice_download';
$wpdb->iceindex = $wpdb->prefix.'ice_download_index';
$wpdb->icemoney  = $wpdb->prefix.'ice_money';
$wpdb->icelog  = $wpdb->prefix.'ice_money_log';
$wpdb->iceinfo  = $wpdb->prefix.'ice_info';
$wpdb->icecat  = $wpdb->prefix.'ice_cat';
$wpdb->iceget  = $wpdb->prefix.'ice_get_money';
$wpdb->vip  = $wpdb->prefix.'ice_vip';
$wpdb->vipcat  = $wpdb->prefix.'ice_vip_cat';
$wpdb->aff  = $wpdb->prefix.'ice_aff';
$wpdb->down  = $wpdb->prefix.'ice_down';
$wpdb->tuan  = $wpdb->prefix.'ice_tuan';
$wpdb->tuanorder = $wpdb->prefix.'ice_tuan_order';
$wpdb->checkin  = $wpdb->prefix.'checkins';
$wpdb->erphpcard = $wpdb->prefix.'erphpdown_card';
$wpdb->erphpvipcard = $wpdb->prefix.'erphpdown_vipcard';
$wpdb->erphpact = $wpdb->prefix.'erphpdown_activation';
$wppay_table_name = $wpdb->prefix . 'wppay';
define("erphpdown",plugin_dir_url( __FILE__ ));
define('ERPHPDOWN_URL', plugins_url('', __FILE__));
define('ERPHPDOWN_PATH', dirname( __FILE__ ));

add_action( 'init', 'erphpdown_loaded' );
function erphpdown_loaded(){
	load_plugin_textdomain( 'erphpdown', true, dirname( plugin_basename( __FILE__ ) ) . '/lang' );
}

require_once ERPHPDOWN_PATH . '/includes/init.php';
require_once ERPHPDOWN_PATH . '/includes/mobantu.php';
if(file_exists(get_stylesheet_directory().'/erphpdown/metabox.php')){
	require_once get_stylesheet_directory().'/erphpdown/metabox.php';
}elseif(file_exists(get_template_directory().'/erphpdown/metabox.php')){
	require_once get_template_directory().'/erphpdown/metabox.php';
}else{
	require_once ERPHPDOWN_PATH . '/includes/metabox.php';
}
require_once ERPHPDOWN_PATH . '/includes/shortcode.php';
require_once ERPHPDOWN_PATH . '/includes/show.php';
require_once ERPHPDOWN_PATH . '/includes/functions.erphp.php';
require_once ERPHPDOWN_PATH . '/includes/class.erphp.php';
require_once ERPHPDOWN_PATH . '/includes/pay.erphp.php';
require_once ERPHPDOWN_PATH . '/includes/crypt.class.php';
require_once ERPHPDOWN_PATH . '/diy.php';

if(plugin_check_card()){
	require_once ERPHPDOWN_PATH . '/addon/card/index.php';
}

if(plugin_check_vipcard()){
	require_once ERPHPDOWN_PATH . '/addon/vipcard/index.php';
}

if(plugin_check_activation()){
	require_once ERPHPDOWN_PATH . '/addon/activation/index.php';
}

if(plugin_check_pancheck()){
	require_once ERPHPDOWN_PATH . '/addon/pancheck/index.php';
}

if(plugin_check_tuan()){
	require_once ERPHPDOWN_PATH . '/addon/tuan/index.php';
}

register_activation_hook(__FILE__, 'erphpdown_install');
register_deactivation_hook(__FILE__, 'erphpdown_uninstall');