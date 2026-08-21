<?php
// +----------------------------------------------------------------------
// | ERPHP [ PHP DEVELOP ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.mobantu.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: mobantu <82708210@qq.com>
// +----------------------------------------------------------------------
if ( !defined('ABSPATH') ) {exit;}

 if(isset($_POST['Submit'])) {
    if(isset($_POST['erphpdown_recharge_order'])) update_option('erphpdown_recharge_order', trim($_POST['erphpdown_recharge_order']));
    if(isset($_POST['erphpdown_alipay_type'])) update_option('erphpdown_alipay_type', trim($_POST['erphpdown_alipay_type']));
    if(isset($_POST['ice_ali_partner'])) update_option('ice_ali_partner', trim($_POST['ice_ali_partner']));
    if(isset($_POST['ice_ali_security_code'])) update_option('ice_ali_security_code', trim($_POST['ice_ali_security_code']));
    if(isset($_POST['ice_ali_seller_email'])) update_option('ice_ali_seller_email', trim($_POST['ice_ali_seller_email']));
    if(isset($_POST['ice_ali_app'])){
        update_option('ice_ali_app', trim($_POST['ice_ali_app']));
    }else{
        delete_option('ice_ali_app');
    }
    if(isset($_POST['ice_ali_app_id'])) update_option('ice_ali_app_id', trim($_POST['ice_ali_app_id']));
    if(isset($_POST['ice_ali_private_key'])) update_option('ice_ali_private_key', trim($_POST['ice_ali_private_key']));
    if(isset($_POST['ice_ali_public_key'])) update_option('ice_ali_public_key', trim($_POST['ice_ali_public_key']));
    if(isset($_POST['ice_payapl_api_uid'])) update_option('ice_payapl_api_uid', trim($_POST['ice_payapl_api_uid']));
    if(isset($_POST['ice_payapl_api_pwd'])) update_option('ice_payapl_api_pwd', trim($_POST['ice_payapl_api_pwd']));
    if(isset($_POST['ice_payapl_api_md5'])) update_option('ice_payapl_api_md5', trim($_POST['ice_payapl_api_md5']));
    if(isset($_POST['ice_payapl_api_rmb'])) update_option('ice_payapl_api_rmb', trim($_POST['ice_payapl_api_rmb']));   
    if(isset($_POST['erphpdown_xhpay_appid31'])) update_option('erphpdown_xhpay_appid31', trim($_POST['erphpdown_xhpay_appid31']));
    if(isset($_POST['erphpdown_xhpay_appsecret31'])) update_option('erphpdown_xhpay_appsecret31', trim($_POST['erphpdown_xhpay_appsecret31']));
    if(isset($_POST['erphpdown_xhpay_api31'])) update_option('erphpdown_xhpay_api31', trim($_POST['erphpdown_xhpay_api31']));
    if(isset($_POST['erphpdown_xhpay_appid32'])) update_option('erphpdown_xhpay_appid32', trim($_POST['erphpdown_xhpay_appid32']));
    if(isset($_POST['erphpdown_xhpay_appsecret32'])) update_option('erphpdown_xhpay_appsecret32', trim($_POST['erphpdown_xhpay_appsecret32']));
    if(isset($_POST['erphpdown_xhpay_api32'])) update_option('erphpdown_xhpay_api32', trim($_POST['erphpdown_xhpay_api32']));
    if(isset($_POST['erphpdown_xhpay_admin'])){
        update_option('erphpdown_xhpay_admin', trim($_POST['erphpdown_xhpay_admin']));
    }else{
        delete_option('erphpdown_xhpay_admin');
    }
    if(isset($_POST['ice_weixin_mchid'])) update_option('ice_weixin_mchid', trim($_POST['ice_weixin_mchid']));
    if(isset($_POST['ice_weixin_appid'])) update_option('ice_weixin_appid', trim($_POST['ice_weixin_appid']));
    if(isset($_POST['ice_weixin_key'])) update_option('ice_weixin_key', trim($_POST['ice_weixin_key']));
    if(isset($_POST['ice_weixin_secret'])) update_option('ice_weixin_secret', trim($_POST['ice_weixin_secret']));
    if(isset($_POST['ice_weixin_app'])){
        update_option('ice_weixin_app', trim($_POST['ice_weixin_app']));
    }else{
        delete_option('ice_weixin_app');
    }
    if(isset($_POST['ice_weixin_ip'])){
        update_option('ice_weixin_ip', trim($_POST['ice_weixin_ip']));
    }else{
        delete_option('ice_weixin_ip');
    }
    if(isset($_POST['erphpdown_paypy_key'])) update_option('erphpdown_paypy_key', trim($_POST['erphpdown_paypy_key']));
    if(isset($_POST['erphpdown_paypy_api'])) update_option('erphpdown_paypy_api', trim($_POST['erphpdown_paypy_api']));
    if(isset($_POST['erphpdown_paypy_alipay'])){
        update_option('erphpdown_paypy_alipay', trim($_POST['erphpdown_paypy_alipay']));
    }else{
        delete_option('erphpdown_paypy_alipay');
    }
    if(isset($_POST['erphpdown_paypy_wxpay'])){
        update_option('erphpdown_paypy_wxpay', trim($_POST['erphpdown_paypy_wxpay']));
    }else{
        delete_option('erphpdown_paypy_wxpay');
    }
    if(isset($_POST['erphpdown_paypy_curl'])){
        update_option('erphpdown_paypy_curl', trim($_POST['erphpdown_paypy_curl']));
    }else{
        delete_option('erphpdown_paypy_curl');
    }
    if(isset($_POST['erphpdown_payjs_appid'])) update_option('erphpdown_payjs_appid', trim($_POST['erphpdown_payjs_appid']));
    if(isset($_POST['erphpdown_payjs_appsecret'])) update_option('erphpdown_payjs_appsecret', trim($_POST['erphpdown_payjs_appsecret']));
    if(isset($_POST['erphpdown_payjs_alipay'])){
        update_option('erphpdown_payjs_alipay', trim($_POST['erphpdown_payjs_alipay']));
    }else{
        delete_option('erphpdown_payjs_alipay');
    }
    if(isset($_POST['erphpdown_payjs_wxpay'])){
        update_option('erphpdown_payjs_wxpay', trim($_POST['erphpdown_payjs_wxpay']));
    }else{
        delete_option('erphpdown_payjs_wxpay');
    }
    if(isset($_POST['erphpdown_codepay_appid'])) update_option('erphpdown_codepay_appid', trim($_POST['erphpdown_codepay_appid']));
    if(isset($_POST['erphpdown_codepay_appsecret'])) update_option('erphpdown_codepay_appsecret', trim($_POST['erphpdown_codepay_appsecret']));
    if(isset($_POST['erphpdown_codepay_alipay'])){
        update_option('erphpdown_codepay_alipay', trim($_POST['erphpdown_codepay_alipay']));
    }else{
        delete_option('erphpdown_codepay_alipay');
    }
    if(isset($_POST['erphpdown_codepay_qqpay'])){
        update_option('erphpdown_codepay_qqpay', trim($_POST['erphpdown_codepay_qqpay']));
    }else{
        delete_option('erphpdown_codepay_qqpay');
    }
    if(isset($_POST['erphpdown_codepay_wxpay'])){
        update_option('erphpdown_codepay_wxpay', trim($_POST['erphpdown_codepay_wxpay']));
    }else{
        delete_option('erphpdown_codepay_wxpay');
    }
    if(isset($_POST['erphpdown_codepay_api'])) update_option('erphpdown_codepay_api', trim($_POST['erphpdown_codepay_api']));
    if(isset($_POST['erphpdown_f2fpay_id'])) update_option('erphpdown_f2fpay_id', trim($_POST['erphpdown_f2fpay_id']));
    if(isset($_POST['erphpdown_f2fpay_public_key'])) update_option('erphpdown_f2fpay_public_key', trim($_POST['erphpdown_f2fpay_public_key']));
    if(isset($_POST['erphpdown_f2fpay_private_key'])) update_option('erphpdown_f2fpay_private_key', trim($_POST['erphpdown_f2fpay_private_key']));
    if(isset($_POST['erphpdown_f2fpay_alipay'])){
        update_option('erphpdown_f2fpay_alipay', trim($_POST['erphpdown_f2fpay_alipay']));
    }else{
        delete_option('erphpdown_f2fpay_alipay');
    }
    if(isset($_POST['erphpdown_easepay_id'])) update_option('erphpdown_easepay_id', trim($_POST['erphpdown_easepay_id']));
    if(isset($_POST['erphpdown_easepay_key'])) update_option('erphpdown_easepay_key', trim($_POST['erphpdown_easepay_key']));
    if(isset($_POST['erphpdown_easepay_url'])) update_option('erphpdown_easepay_url', trim($_POST['erphpdown_easepay_url']));
    if(isset($_POST['erphpdown_easepay_alipay'])){
        update_option('erphpdown_easepay_alipay', trim($_POST['erphpdown_easepay_alipay']));
    }else{
        delete_option('erphpdown_easepay_alipay');
    }
    if(isset($_POST['erphpdown_easepay_wxpay'])){
        update_option('erphpdown_easepay_wxpay', trim($_POST['erphpdown_easepay_wxpay']));
    }else{
        delete_option('erphpdown_easepay_wxpay');
    }
    if(isset($_POST['erphpdown_easepay_usdt'])){
        update_option('erphpdown_easepay_usdt', trim($_POST['erphpdown_easepay_usdt']));
    }else{
        delete_option('erphpdown_easepay_usdt');
    }
    if(isset($_POST['erphpdown_epay_id'])) update_option('erphpdown_epay_id', trim($_POST['erphpdown_epay_id']));
    if(isset($_POST['erphpdown_epay_key'])) update_option('erphpdown_epay_key', trim($_POST['erphpdown_epay_key']));
    if(isset($_POST['erphpdown_epay_url'])) update_option('erphpdown_epay_url', trim($_POST['erphpdown_epay_url']));
    if(isset($_POST['erphpdown_epay_alipay'])){
        update_option('erphpdown_epay_alipay', trim($_POST['erphpdown_epay_alipay']));
    }else{
        delete_option('erphpdown_epay_alipay');
    }
    if(isset($_POST['erphpdown_epay_wxpay'])){
        update_option('erphpdown_epay_wxpay', trim($_POST['erphpdown_epay_wxpay']));
    }else{
        delete_option('erphpdown_epay_wxpay');
    }
    if(isset($_POST['erphpdown_epay_qqpay'])){
        update_option('erphpdown_epay_qqpay', trim($_POST['erphpdown_epay_qqpay']));
    }else{
        delete_option('erphpdown_epay_qqpay');
    }
    if(isset($_POST['erphpdown_epay_alipay_json'])){
        update_option('erphpdown_epay_alipay_json', trim($_POST['erphpdown_epay_alipay_json']));
    }else{
        delete_option('erphpdown_epay_alipay_json');
    }
    if(isset($_POST['erphpdown_epay_wxpay_json'])){
        update_option('erphpdown_epay_wxpay_json', trim($_POST['erphpdown_epay_wxpay_json']));
    }else{
        delete_option('erphpdown_epay_wxpay_json');
    }
    if(isset($_POST['erphpdown_epay_qqpay_json'])){
        update_option('erphpdown_epay_qqpay_json', trim($_POST['erphpdown_epay_qqpay_json']));
    }else{
        delete_option('erphpdown_epay_qqpay_json');
    }
    if(isset($_POST['erphpdown_vpay_key'])) update_option('erphpdown_vpay_key', trim($_POST['erphpdown_vpay_key']));
    if(isset($_POST['erphpdown_vpay_api'])) update_option('erphpdown_vpay_api', trim($_POST['erphpdown_vpay_api']));
    if(isset($_POST['erphpdown_vpay_alipay'])){
        update_option('erphpdown_vpay_alipay', trim($_POST['erphpdown_vpay_alipay']));
    }else{
        delete_option('erphpdown_vpay_alipay');
    }
    if(isset($_POST['erphpdown_vpay_wxpay'])){
        update_option('erphpdown_vpay_wxpay', trim($_POST['erphpdown_vpay_wxpay']));
    }else{
        delete_option('erphpdown_vpay_wxpay');
    }
    if(isset($_POST['erphpdown_vpay_curl'])){
        update_option('erphpdown_vpay_curl', trim($_POST['erphpdown_vpay_curl']));
    }else{
        delete_option('erphpdown_vpay_curl');
    }
    if(isset($_POST['erphpdown_usdt_name'])) update_option('erphpdown_usdt_name', trim($_POST['erphpdown_usdt_name']));
    if(isset($_POST['erphpdown_usdt_address'])) update_option('erphpdown_usdt_address', trim($_POST['erphpdown_usdt_address']));
    if(isset($_POST['erphpdown_usdt_api'])) update_option('erphpdown_usdt_api', trim($_POST['erphpdown_usdt_api']));
    if(isset($_POST['erphpdown_usdt_token'])) update_option('erphpdown_usdt_token', trim($_POST['erphpdown_usdt_token']));
    if(isset($_POST['erphpdown_usdt_rmb'])) update_option('erphpdown_usdt_rmb', trim($_POST['erphpdown_usdt_rmb']));
    if(isset($_POST['erphpdown_stripe_pk'])) update_option('erphpdown_stripe_pk', trim($_POST['erphpdown_stripe_pk']));
    if(isset($_POST['erphpdown_stripe_sk'])) update_option('erphpdown_stripe_sk', trim($_POST['erphpdown_stripe_sk']));
    if(isset($_POST['erphpdown_stripe_hk'])) update_option('erphpdown_stripe_hk', trim($_POST['erphpdown_stripe_hk']));

    if(plugin_check_ecpay()){
        if(isset($_POST['erphpdown_ecpay_HashKey'])) update_option('erphpdown_ecpay_HashKey', trim($_POST['erphpdown_ecpay_HashKey']));
        if(isset($_POST['erphpdown_ecpay_HashIV'])) update_option('erphpdown_ecpay_HashIV', trim($_POST['erphpdown_ecpay_HashIV']));
        if(isset($_POST['erphpdown_ecpay_MerchantID'])) update_option('erphpdown_ecpay_MerchantID', trim($_POST['erphpdown_ecpay_MerchantID']));
        if(isset($_POST['erphpdown_ecpay_rmb'])) update_option('erphpdown_ecpay_rmb', trim($_POST['erphpdown_ecpay_rmb']));
    }

    if(plugin_check_newebpay()){
        if(isset($_POST['erphpdown_newebpay_HashKey'])) update_option('erphpdown_newebpay_HashKey', trim($_POST['erphpdown_newebpay_HashKey']));
        if(isset($_POST['erphpdown_newebpay_HashIV'])) update_option('erphpdown_newebpay_HashIV', trim($_POST['erphpdown_newebpay_HashIV']));
        if(isset($_POST['erphpdown_newebpay_MerchantID'])) update_option('erphpdown_newebpay_MerchantID', trim($_POST['erphpdown_newebpay_MerchantID']));
        if(isset($_POST['erphpdown_newebpay_rmb'])) update_option('erphpdown_newebpay_rmb', trim($_POST['erphpdown_newebpay_rmb']));
    }

    echo'<div class="updated settings-error"><p>更新成功！</p></div>';

 }
 $erphpdown_recharge_order = get_option('erphpdown_recharge_order');
 $erphpdown_alipay_type = get_option('erphpdown_alipay_type');
 $ice_ali_partner       = get_option('ice_ali_partner');
 $ice_ali_security_code = get_option('ice_ali_security_code');
 $ice_ali_seller_email  = get_option('ice_ali_seller_email');
 $ice_ali_app   = get_option('ice_ali_app');
 $ice_ali_app_id   = get_option('ice_ali_app_id');
 $ice_ali_private_key   = get_option('ice_ali_private_key');
 $ice_ali_public_key   = get_option('ice_ali_public_key');
 $ice_payapl_api_uid    = get_option('ice_payapl_api_uid');
 $ice_payapl_api_pwd    = get_option('ice_payapl_api_pwd');
 $ice_payapl_api_md5    = get_option('ice_payapl_api_md5');
 $ice_payapl_api_rmb    = get_option('ice_payapl_api_rmb');
 $erphpdown_xhpay_appid31    = get_option('erphpdown_xhpay_appid31');
 $erphpdown_xhpay_appsecret31    = get_option('erphpdown_xhpay_appsecret31');
 $erphpdown_xhpay_api31    = get_option('erphpdown_xhpay_api31');
 $erphpdown_xhpay_appid32    = get_option('erphpdown_xhpay_appid32');
 $erphpdown_xhpay_appsecret32    = get_option('erphpdown_xhpay_appsecret32');
 $erphpdown_xhpay_api32    = get_option('erphpdown_xhpay_api32');
 $erphpdown_xhpay_admin = get_option('erphpdown_xhpay_admin');
 $ice_weixin_mchid  = get_option('ice_weixin_mchid');
 $ice_weixin_appid  = get_option('ice_weixin_appid');
 $ice_weixin_key  = get_option('ice_weixin_key');
 $ice_weixin_secret  = get_option('ice_weixin_secret');
 $ice_weixin_app  = get_option('ice_weixin_app');
 $ice_weixin_ip  = get_option('ice_weixin_ip');
 $erphpdown_paypy_key    = get_option('erphpdown_paypy_key');
 $erphpdown_paypy_api    = get_option('erphpdown_paypy_api');
 $erphpdown_paypy_alipay = get_option('erphpdown_paypy_alipay');
 $erphpdown_paypy_wxpay = get_option('erphpdown_paypy_wxpay');
 $erphpdown_paypy_curl = get_option('erphpdown_paypy_curl');
 $erphpdown_payjs_appid    = get_option('erphpdown_payjs_appid');
 $erphpdown_payjs_appsecret    = get_option('erphpdown_payjs_appsecret');
 $erphpdown_payjs_alipay    = get_option('erphpdown_payjs_alipay');
 $erphpdown_payjs_wxpay    = get_option('erphpdown_payjs_wxpay');
 $erphpdown_codepay_appid    = get_option('erphpdown_codepay_appid');
 $erphpdown_codepay_appsecret    = get_option('erphpdown_codepay_appsecret');
 $erphpdown_codepay_alipay = get_option('erphpdown_codepay_alipay');
 $erphpdown_codepay_qqpay = get_option('erphpdown_codepay_qqpay');
 $erphpdown_codepay_wxpay = get_option('erphpdown_codepay_wxpay');
 $erphpdown_codepay_api = get_option('erphpdown_codepay_api');
 $erphpdown_f2fpay_id       = get_option('erphpdown_f2fpay_id');
 $erphpdown_f2fpay_public_key       = get_option('erphpdown_f2fpay_public_key');
 $erphpdown_f2fpay_private_key       = get_option('erphpdown_f2fpay_private_key');
 $erphpdown_f2fpay_alipay = get_option('erphpdown_f2fpay_alipay');
 $erphpdown_easepay_id  = get_option('erphpdown_easepay_id');
 $erphpdown_easepay_key  = get_option('erphpdown_easepay_key');
 $erphpdown_easepay_url  = get_option('erphpdown_easepay_url');
 $erphpdown_easepay_alipay = get_option('erphpdown_easepay_alipay');
 $erphpdown_easepay_wxpay = get_option('erphpdown_easepay_wxpay');
 $erphpdown_easepay_usdt = get_option('erphpdown_easepay_usdt');
 $erphpdown_epay_id  = get_option('erphpdown_epay_id');
 $erphpdown_epay_key  = get_option('erphpdown_epay_key');
 $erphpdown_epay_url  = get_option('erphpdown_epay_url');
 $erphpdown_epay_alipay = get_option('erphpdown_epay_alipay');
 $erphpdown_epay_wxpay = get_option('erphpdown_epay_wxpay');
 $erphpdown_epay_qqpay = get_option('erphpdown_epay_qqpay');
 $erphpdown_epay_alipay_json = get_option('erphpdown_epay_alipay_json');
 $erphpdown_epay_wxpay_json = get_option('erphpdown_epay_wxpay_json');
 $erphpdown_epay_qqpay_json = get_option('erphpdown_epay_qqpay_json');
 $erphpdown_vpay_key  = get_option('erphpdown_vpay_key');
 $erphpdown_vpay_api  = get_option('erphpdown_vpay_api');
 $erphpdown_vpay_alipay = get_option('erphpdown_vpay_alipay');
 $erphpdown_vpay_wxpay = get_option('erphpdown_vpay_wxpay');
 $erphpdown_vpay_curl = get_option('erphpdown_vpay_curl');
 $erphpdown_usdt_name = get_option('erphpdown_usdt_name');
 $erphpdown_usdt_address = get_option('erphpdown_usdt_address');
 $erphpdown_usdt_api = get_option('erphpdown_usdt_api');
 $erphpdown_usdt_token = get_option('erphpdown_usdt_token');
 $erphpdown_usdt_rmb = get_option('erphpdown_usdt_rmb');
 $erphpdown_stripe_pk = get_option('erphpdown_stripe_pk');
 $erphpdown_stripe_sk = get_option('erphpdown_stripe_sk');
 $erphpdown_stripe_hk = get_option('erphpdown_stripe_hk');
 if(plugin_check_ecpay()){
    $erphpdown_ecpay_HashKey = get_option('erphpdown_ecpay_HashKey');
    $erphpdown_ecpay_HashIV = get_option('erphpdown_ecpay_HashIV');
    $erphpdown_ecpay_MerchantID = get_option('erphpdown_ecpay_MerchantID');
    $erphpdown_ecpay_rmb = get_option('erphpdown_ecpay_rmb');
 }
 if(plugin_check_newebpay()){
    $erphpdown_newebpay_HashKey = get_option('erphpdown_newebpay_HashKey');
    $erphpdown_newebpay_HashIV = get_option('erphpdown_newebpay_HashIV');
    $erphpdown_newebpay_MerchantID = get_option('erphpdown_newebpay_MerchantID');
    $erphpdown_newebpay_rmb = get_option('erphpdown_newebpay_rmb');
 }
 ?>
 <style>.form-table th{font-weight: 400} .erphpdown-payment h3 span{font-weight: normal;background: #ff5f33;display: inline-block;padding: 3px 6px;line-height: 1;border-radius: 3px;color: #fff;font-size: 12px;}</style>
 <div class="wrap erphpdown-payment">
    <h1>支付设置</h1>
    <form method="post" action="<?php echo admin_url('admin.php?page='.plugin_basename(__FILE__)); ?>">
        <h3>充值支付接口顺序</h3>
        支付宝 <code>alipay</code>、支付宝当面付 <code>f2fpay</code>、微信支付 <code>wxpay</code>、PayPal <code>paypal</code>、Paypy微信 <code>paypy-wx</code>、Paypy支付宝 <code>paypy-ali</code>、Payjs微信 <code>payjs-wx</code>、Payjs支付宝 <code>payjs-ali</code>、虎皮椒微信 <code>xhpay-wx</code>、虎皮椒支付宝 <code>xhpay-ali</code>、码支付微信 <code>codepay-wx</code>、码支付支付宝 <code>codepay-ali</code>、码支付QQ钱包 <code>codepay-qq</code>、易支付微信 <code>epay-wx</code>、易支付支付宝 <code>epay-ali</code>、易支付QQ钱包 <code>epay-qq</code>、易支付2微信 <code>easepay-wx</code>、易支付2支付宝 <code>easepay-ali</code>、易支付2USDT <code>easepay-usdt</code>、V免签微信 <code>vpay-wx</code>、V免签支付宝 <code>vpay-ali</code>、USDT <code>usdt</code>、Stripe信用卡 <code>stripe</code>
        <table class="form-table">
            <tr>
                <td style="padding-left: 0">
                    <input type="text" id="erphpdown_recharge_order" name="erphpdown_recharge_order" value="<?php echo $erphpdown_recharge_order ; ?>" class="regular-text"/>
                    <p>留空则默认，如需设置请填写可用接口的<code>代号</code>，多个用英文逗号隔开，例如：alipay,wxpay</p>
                </td>
            </tr>
        </table>
        <h3>1、支付宝（官方接口）</h3>
        详情：https://b.alipay.com/signing/productDetailV2.htm?productId=I1011000290000001000<br>
        开放平台申请接口：https://openhome.alipay.com/platform/developerIndex.htm 网页&移动应用，能力名称：电脑网站支付<br>
        新接口设置教程与当面付的接口一样，填写的信息也一样：<a href="https://www.mobantu.com/7731.html" target="_blank">https://www.mobantu.com/7731.html</a>
        <table class="form-table">
            <tr>
                <th valign="top">PC端电脑支付版本</th>
                <td>
                    <select name="erphpdown_alipay_type" id="erphpdown_alipay_type">
                        <option value="create_direct_pay_by_user" <?php if($erphpdown_alipay_type == 'create_direct_pay_by_user') echo 'selected="selected"';?>>老接口（MD5签名）</option>
                        <option value="create_trade_pay_by_user" <?php if($erphpdown_alipay_type == 'create_trade_pay_by_user') echo 'selected="selected"';?>>新接口（RSA2签名）</option>
                    </select>
                    <p><b>请使用新接口，老接口支付宝官方即将废弃</b></p>
                </td>
            </tr>
            <tr class="alipayold">
                <th valign="top">合作者身份(Partner ID)</th>
                <td>
                    <input type="text" id="ice_ali_partner" name="ice_ali_partner" value="<?php echo $ice_ali_partner ; ?>" class="regular-text"/>
                    <p>老接口需填写，查看地址：https://openhome.alipay.com/platform/keyManage.htm?keyType=partner</p>
                </td>
            </tr>
            <tr class="alipayold">
                <th valign="top">安全校验码(Key)</th>
                <td>
                    <input type="text" id="ice_ali_security_code" name="ice_ali_security_code" value="<?php echo $ice_ali_security_code; ?>" class="regular-text"/>
                    <p>老接口需填写，密钥管理 - mapi网关产品密钥，MD5密钥</p>
                </td>
            </tr>
            <tr class="alipayold">
                <th valign="top">支付宝收款账号</th>
                <td>
                    <input type="text" id="ice_ali_seller_email" name="ice_ali_seller_email" value="<?php echo $ice_ali_seller_email; ?>" class="regular-text"/>
                    <p>老接口需填写</p>
                </td>
            </tr>
            <tr>
                <th valign="top">启用手机端H5支付</th>
                <td>
                    <input type="checkbox" id="ice_ali_app" name="ice_ali_app" value="yes" <?php if($ice_ali_app == 'yes') echo 'checked'; ?> /> （能力名称：手机网站支付）
                </td>
            </tr>
            <tr>
                <th valign="top">APPID</th>
                <td>
                <input type="text" id="ice_ali_app_id" name="ice_ali_app_id" value="<?php echo $ice_ali_app_id; ?>" class="regular-text"/>
                <p>新接口与H5支付需填写</p>
                </td>
            </tr>
            <tr>
                <th valign="top">商户应用私钥</th>
                <td>
                <textarea id="ice_ali_private_key" name="ice_ali_private_key" class="regular-text" style="height: 200px;"><?php echo $ice_ali_private_key; ?></textarea>
                <p>新接口与H5支付需填写</p>
                </td>
            </tr>
            <tr>
                <th valign="top">支付宝公钥</th>
                <td>
                <textarea id="ice_ali_public_key" name="ice_ali_public_key" class="regular-text" style="height: 200px;"><?php echo $ice_ali_public_key; ?></textarea>
                <p>新接口与H5支付需填写，注意<b>不是应用公钥</b></p>
                </td>
            </tr>
        </table>
        <script>
            jQuery(function($){
                if($("#erphpdown_alipay_type").val() == 'create_trade_pay_by_user'){
                    $(".alipayold").css("display", "none");
                }

                $("#erphpdown_alipay_type").change(function(){
                    if($(this).val() == 'create_trade_pay_by_user'){
                        $(".alipayold").css("display", "none");
                    }else{
                        $(".alipayold").css("display", "table-row");
                    }
                });
            });
        </script>
        
        <br />
        <h3>2、支付宝当面付（官方接口）<span>个人可用</span></h3>
        详情：https://b.alipay.com/signing/productDetailV2.htm?productId=I1011000290000001003<br>接口设置教程：<a href="https://www.mobantu.com/7731.html" target="_blank">https://www.mobantu.com/7731.html</a>
        <table class="form-table">
                <tr>
                <th valign="top">应用APPID</th>
                <td>
                    <input type="text" id="erphpdown_f2fpay_id" name="erphpdown_f2fpay_id" value="<?php echo $erphpdown_f2fpay_id ; ?>" class="regular-text"/>
                </td>
            </tr>
            <tr>
                <th valign="top">商户应用私钥</th>
                <td>
                    <textarea id="erphpdown_f2fpay_private_key" name="erphpdown_f2fpay_private_key" class="regular-text" style="height: 200px;"><?php echo $erphpdown_f2fpay_private_key; ?></textarea>
                </td>
            </tr>
            <tr>
                <th valign="top">支付宝公钥</th>
                <td>
                    <textarea id="erphpdown_f2fpay_public_key" name="erphpdown_f2fpay_public_key" class="regular-text" style="height: 200px;"><?php echo $erphpdown_f2fpay_public_key; ?></textarea>
                    <p>注意<b>不是应用公钥</b></p>
                </td>
            </tr>
            <tr>
                <th valign="top">隐藏</th>
                <td>
                    <input type="checkbox" id="erphpdown_f2fpay_alipay" name="erphpdown_f2fpay_alipay" value="yes" <?php if($erphpdown_f2fpay_alipay == 'yes') echo 'checked'; ?> /> 
                </td>
            </tr>
        </table>

        <br />
        <h3>3、微信支付（官方接口）</h3>
        需认证的服务号，开通了商户号并绑定服务号，详情：https://pay.weixin.qq.com/static/product/product_intro.shtml?name=native<br>
        微信支付-->开发配置，设置<b>支付授权目录</b>：<?php echo home_url();?>/wp-content/plugins/erphpdown/payment/<br>
        接口设置教程：<a href="https://www.mobantu.com/7919.html" target="_blank">https://www.mobantu.com/7919.html</a>
        <table class="form-table">
            <tr>
                <th valign="top">商户号(MCHID)</th>
                <td>
                    <input type="text" id="ice_weixin_mchid" name="ice_weixin_mchid" value="<?php echo $ice_weixin_mchid ; ?>" class="regular-text"/>
                </td>
            </tr>
            <tr>
                <th valign="top">APPID</th>
                <td>
                    <input type="text" id="ice_weixin_appid" name="ice_weixin_appid" value="<?php echo $ice_weixin_appid; ?>" class="regular-text"/>
                </td>
            </tr>
            <tr>
                <th valign="top">公众号AppSecret</th>
                <td>
                    <input type="text" id="ice_weixin_secret" name="ice_weixin_secret" value="<?php echo $ice_weixin_secret; ?>" class="regular-text"/>
                </td>
            </tr>
            <tr>
                <th valign="top">商户支付密钥(KEY)</th>
                <td>
                    <input type="text" id="ice_weixin_key" name="ice_weixin_key" value="<?php echo $ice_weixin_key; ?>" class="regular-text"/><br>
                    设置地址：<a href="https://pay.weixin.qq.com/index.php/account/api_cert" target="_blank">https://pay.weixin.qq.com/index.php/account/api_cert </a>，建议为32位字符串
                </td>
            </tr>
            <tr>
                <th valign="top">启用唤醒APP H5支付</th>
                <td>
                    <input type="checkbox" id="ice_weixin_app" name="ice_weixin_app" value="yes" <?php if($ice_weixin_app == 'yes') echo 'checked'; ?> /> 
                    <p>1、微信公众平台-->公众号设置-->功能设置，设置业务域名、JS接口安全域名、网页授权域名<br>2、商户平台-->产品中心-->开发配置，设置支付授权目录、H5支付域名</p>
                </td>
            </tr>
            <tr>
                <th valign="top">固定IP模式</th>
                <td>
                    <input type="checkbox" id="ice_weixin_ip" name="ice_weixin_ip" value="yes" <?php if($ice_weixin_ip == 'yes') echo 'checked'; ?> /> 
                    <p>若提示“spbill_create_ip”相关错误，勾选试试</p>
                </td>
            </tr>
        </table>

        <br />
        <h3>4、PayPal贝宝（官方接口）</h3>
        部分个人账号可用，详情：https://www.paypal.com 申请方法：https://www.mobantu.com/8412.html
        <table class="form-table">
            <tr>
                <th valign="top">API帐号</th>
                <td>
                    <input type="text" id="ice_payapl_api_uid" name="ice_payapl_api_uid" value="<?php echo $ice_payapl_api_uid ; ?>" class="regular-text"/>
                </td>
            </tr>
            <tr>
                <th valign="top">API密码</th>
                <td>
                    <input type="text" id="ice_payapl_api_pwd" name="ice_payapl_api_pwd" value="<?php echo $ice_payapl_api_pwd; ?>" class="regular-text"/>
                </td>
            </tr>
            <tr>
                <th valign="top">API签名</th>
                <td>
                    <input type="text" id="ice_payapl_api_md5" name="ice_payapl_api_md5" value="<?php echo $ice_payapl_api_md5; ?>" class="regular-text"/>
                </td>
            </tr>
            <tr>
                <th valign="top">汇率</th>
                <td>
                    <input type="number" step="0.01" id="ice_payapl_api_rmb" name="ice_payapl_api_rmb" value="<?php echo $ice_payapl_api_rmb; ?>" class="regular-text"/>
                    <p>填5表示1美元=5元</p>
                </td>
            </tr>
        </table>

        <br />
        <h3>5、Paypy（微信/支付宝）<span>个人免签</span></h3>
        <div>详情：<a href="http://www.mobantu.com/8080.html" target="_blank" rel="nofollow">http://www.mobantu.com/8080.html</a></div>
        <table class="form-table">
            <tr>
                <th valign="top">Api地址</th>
                <td>
                    <input type="text" id="erphpdown_paypy_api" name="erphpdown_paypy_api" value="<?php echo $erphpdown_paypy_api; ?>" class="regular-text"/>
                </td>
            </tr>
            <tr>
                <th valign="top">签名密钥</th>
                <td>
                    <input type="text" id="erphpdown_paypy_key" name="erphpdown_paypy_key" value="<?php echo $erphpdown_paypy_key ; ?>" class="regular-text"/>
                </td>
            </tr>
            <tr>
                <th valign="top">隐藏支付宝</th>
                <td>
                    <input type="checkbox" id="erphpdown_paypy_alipay" name="erphpdown_paypy_alipay" value="yes" <?php if($erphpdown_paypy_alipay == 'yes') echo 'checked'; ?> /> 
                </td>
            </tr>
            <tr>
                <th valign="top">隐藏微信</th>
                <td>
                    <input type="checkbox" id="erphpdown_paypy_wxpay" name="erphpdown_paypy_wxpay" value="yes" <?php if($erphpdown_paypy_wxpay == 'yes') echo 'checked'; ?> /> 
                </td>
            </tr>
            <tr>
                <th valign="top">兼容切换</th>
                <td>
                    <input type="checkbox" id="erphpdown_paypy_curl" name="erphpdown_paypy_curl" value="yes" <?php if($erphpdown_paypy_curl == 'yes') echo 'checked'; ?> /> 
                    <p>如果都配置好了但无法出码，可勾选此项试试</p>
                </td>
            </tr>
        </table>

        <br />
        <h3>6、V免签（微信/支付宝）<span>个人免签</span></h3>
        <div>需要自行搭建系统，详情：https://github.com/szvone/vmqphp</div>
        <table class="form-table">
            <tr>
                <th valign="top">通信密钥key</th>
                <td>
                <input type="text" id="erphpdown_vpay_key" name="erphpdown_vpay_key" value="<?php echo $erphpdown_vpay_key; ?>" class="regular-text"/>
                </td>
            </tr>
            <tr>
                <th valign="top">API地址</th>
                <td>
                <input type="text" id="erphpdown_vpay_api" name="erphpdown_vpay_api" value="<?php echo $erphpdown_vpay_api; ?>" class="regular-text"/>
                <p>发起订单的地址，一般是vpay域名+/createOrder结尾（需要配置好伪静态），例如http://vpay.erphpdown.com/createOrder</p>
                </td>
            </tr>
            <tr>
                <th valign="top">隐藏支付宝</th>
                <td>
                    <input type="checkbox" id="erphpdown_vpay_alipay" name="erphpdown_vpay_alipay" value="yes" <?php if($erphpdown_vpay_alipay == 'yes') echo 'checked'; ?> /> 
                </td>
            </tr>
            <tr>
                <th valign="top">隐藏微信</th>
                <td>
                    <input type="checkbox" id="erphpdown_vpay_wxpay" name="erphpdown_vpay_wxpay" value="yes" <?php if($erphpdown_vpay_wxpay == 'yes') echo 'checked'; ?> /> 
                </td>
            </tr>
            <tr>
                <th valign="top">兼容切换</th>
                <td>
                    <input type="checkbox" id="erphpdown_vpay_curl" name="erphpdown_vpay_curl" value="yes" <?php if($erphpdown_vpay_curl == 'yes') echo 'checked'; ?> /> 
                    <p>如果都配置好了但无法出码，可勾选此项试试</p>
                </td>
            </tr>
        </table>

        <br />
        <h3>7、Payjs（微信/支付宝）<span>个人第三方签约</span></h3>
        <div>关于此接口的安全稳定性，请使用者自行把握，我们只对接接口，接口申请地址：<a href="http://payjs.cn/?utm_source=erphpdown" target="_blank" rel="nofollow">点击查看</a></div>
        <table class="form-table">
            <tr>
                <th valign="top">商户号</th>
                <td>
                    <input type="text" id="erphpdown_payjs_appid" name="erphpdown_payjs_appid" value="<?php echo $erphpdown_payjs_appid ; ?>" class="regular-text"/>
                </td>
            </tr>
            <tr>
                <th valign="top">通讯密钥</th>
                <td>
                    <input type="text" id="erphpdown_payjs_appsecret" name="erphpdown_payjs_appsecret" value="<?php echo $erphpdown_payjs_appsecret; ?>" class="regular-text"/>
                </td>
            </tr>
            <tr>
                <th valign="top">隐藏支付宝</th>
                <td>
                    <input type="checkbox" id="erphpdown_payjs_alipay" name="erphpdown_payjs_alipay" value="yes" <?php if($erphpdown_payjs_alipay == 'yes') echo 'checked'; ?> /> 
                </td>
            </tr>
            <tr>
                <th valign="top">隐藏微信</th>
                <td>
                    <input type="checkbox" id="erphpdown_payjs_wxpay" name="erphpdown_payjs_wxpay" value="yes" <?php if($erphpdown_payjs_wxpay == 'yes') echo 'checked'; ?> /> 
                </td>
            </tr>
        </table>

        <br />
        <h3>8、虎皮椒/迅虎支付（微信/支付宝）<span>个人第三方签约</span></h3>
        <div>关于此接口的安全稳定性，请使用者自行把握，我们只对接接口，接口申请地址：<a href="https://admin.xunhupay.com/sign-up/451.html" target="_blank" rel="nofollow">点击查看</a></div>
        <table class="form-table">
            <tr>
                <th valign="top">微信appid/商户id</th>
                <td>
                    <input type="text" id="erphpdown_xhpay_appid31" name="erphpdown_xhpay_appid31" value="<?php echo $erphpdown_xhpay_appid31 ; ?>" class="regular-text"/>
                </td>
            </tr>
            <tr>
                <th valign="top">微信appsecret/api密钥</th>
                <td>
                    <input type="text" id="erphpdown_xhpay_appsecret31" name="erphpdown_xhpay_appsecret31" value="<?php echo $erphpdown_xhpay_appsecret31; ?>" class="regular-text"/>
                </td>
            </tr>
            <tr>
                <th valign="top">微信网关</th>
                <td>
                    <input type="text" id="erphpdown_xhpay_api31" name="erphpdown_xhpay_api31" value="<?php echo $erphpdown_xhpay_api31; ?>" class="regular-text"/>
                    <p>留空则默认网关，请留空即可。</p>
                </td>
            </tr>
            <tr>
                <th valign="top">支付宝appid/商户id</th>
                <td>
                    <input type="text" id="erphpdown_xhpay_appid32" name="erphpdown_xhpay_appid32" value="<?php echo $erphpdown_xhpay_appid32 ; ?>" class="regular-text"/>
                </td>
            </tr>
            <tr>
                <th valign="top">支付宝appsecret/api密钥</th>
                <td>
                    <input type="text" id="erphpdown_xhpay_appsecret32" name="erphpdown_xhpay_appsecret32" value="<?php echo $erphpdown_xhpay_appsecret32; ?>" class="regular-text"/>
                </td>
            </tr>
            <tr>
                <th valign="top">支付宝网关</th>
                <td>
                    <input type="text" id="erphpdown_xhpay_api32" name="erphpdown_xhpay_api32" value="<?php echo $erphpdown_xhpay_api32; ?>" class="regular-text"/>
                    <p>留空则默认网关，请留空即可。</p>
                </td>
            </tr>
            <tr>
                <th valign="top">迅虎支付</th>
                <td>
                    <input type="checkbox" id="erphpdown_xhpay_admin" name="erphpdown_xhpay_admin" value="yes" <?php if($erphpdown_xhpay_admin == 'yes') echo 'checked'; ?> /> 
                    <p>如果你的接口是迅虎支付的接口，请勾选！</p>
                </td>
            </tr>
        </table>
        
        <br />
        <h3>9、易支付（支付宝/微信/QQ钱包）<span>代收个人免签</span></h3>
        <div>几乎支持市面上99%的易支付平台，关于此接口的安全稳定性，请使用者自行把握，我们只对接接口，例如：<a href="http://erphpdown.com/go/epay" target="_blank" rel="nofollow">点击查看</a></div>
        <table class="form-table">
            <tr>
                <th valign="top">商户ID</th>
                <td>
                <input type="text" id="erphpdown_epay_id" name="erphpdown_epay_id" value="<?php echo $erphpdown_epay_id ; ?>" class="regular-text"/>
                </td>
            </tr>
            <tr>
                <th valign="top">商户密钥key</th>
                <td>
                <input type="text" id="erphpdown_epay_key" name="erphpdown_epay_key" value="<?php echo $erphpdown_epay_key; ?>" class="regular-text"/>
                </td>
            </tr>
            <tr>
                <th valign="top">API地址</th>
                <td>
                <input type="text" id="erphpdown_epay_url" name="erphpdown_epay_url" value="<?php echo $erphpdown_epay_url; ?>" class="regular-text"/>
                <p>注意：地址最后需要带上斜杠/，例如http://epay.erphpdown.com/</p>
                </td>
            </tr>
            <tr>
                <th valign="top">隐藏支付宝</th>
                <td>
                    <input type="checkbox" id="erphpdown_epay_alipay" name="erphpdown_epay_alipay" value="yes" <?php if($erphpdown_epay_alipay == 'yes') echo 'checked'; ?> /> 
                </td>
            </tr>
            <tr>
                <th valign="top">隐藏微信</th>
                <td>
                    <input type="checkbox" id="erphpdown_epay_wxpay" name="erphpdown_epay_wxpay" value="yes" <?php if($erphpdown_epay_wxpay == 'yes') echo 'checked'; ?> /> 
                </td>
            </tr>
            <tr>
                <th valign="top">隐藏QQ钱包</th>
                <td>
                    <input type="checkbox" id="erphpdown_epay_qqpay" name="erphpdown_epay_qqpay" value="yes" <?php if($erphpdown_epay_qqpay == 'yes') echo 'checked'; ?> /> 
                </td>
            </tr>
            <tr>
                <th valign="top">支付宝不跳转直接获取二维码</th>
                <td>
                    <input type="checkbox" id="erphpdown_epay_alipay_json" name="erphpdown_epay_alipay_json" value="yes" <?php if($erphpdown_epay_alipay_json == 'yes') echo 'checked'; ?> /> 
                    <p>请勿勾选，除非接口支持，需指定易支付系统</p>
                </td>
            </tr>
            <tr>
                <th valign="top">微信不跳转直接获取二维码</th>
                <td>
                    <input type="checkbox" id="erphpdown_epay_wxpay_json" name="erphpdown_epay_wxpay_json" value="yes" <?php if($erphpdown_epay_wxpay_json == 'yes') echo 'checked'; ?> /> 
                    <p>请勿勾选，除非接口支持，需指定易支付系统</p>
                </td>
            </tr>
            <tr>
                <th valign="top">QQ钱包不跳转直接获取二维码</th>
                <td>
                    <input type="checkbox" id="erphpdown_epay_qqpay_json" name="erphpdown_epay_qqpay_json" value="yes" <?php if($erphpdown_epay_qqpay_json == 'yes') echo 'checked'; ?> /> 
                    <p>请勿勾选，除非接口支持，需指定易支付系统</p>
                </td>
            </tr>
        </table>
        

        <br />
        <h3>10、易支付2（支付宝/微信/USDT）<span>代收个人免签</span></h3>
        <div>满足同时使用两家易支付的需求。几乎支持市面上99%的易支付平台，关于此接口的安全稳定性，请使用者自行把握，我们只对接接口，例如：<a href="http://erphpdown.com/go/easepay" target="_blank" rel="nofollow">点击查看</a></div>
        <table class="form-table">
            <tr>
                <th valign="top">商户ID</th>
                <td>
                <input type="text" id="erphpdown_easepay_id" name="erphpdown_easepay_id" value="<?php echo $erphpdown_easepay_id ; ?>" class="regular-text"/>
                </td>
            </tr>
            <tr>
                <th valign="top">商户key</th>
                <td>
                <input type="text" id="erphpdown_easepay_key" name="erphpdown_easepay_key" value="<?php echo $erphpdown_easepay_key; ?>" class="regular-text"/>
                </td>
            </tr>
            <tr>
                <th valign="top">API地址</th>
                <td>
                <input type="text" id="erphpdown_easepay_url" name="erphpdown_easepay_url" value="<?php echo $erphpdown_easepay_url; ?>" class="regular-text"/>
                <p>注意：地址最后需要带上斜杠/，例如http://easepay.erphpdown.com/</p>
                </td>
            </tr>
            <tr>
                <th valign="top">隐藏支付宝</th>
                <td>
                    <input type="checkbox" id="erphpdown_easepay_alipay" name="erphpdown_easepay_alipay" value="yes" <?php if($erphpdown_easepay_alipay == 'yes') echo 'checked'; ?> /> 
                </td>
            </tr>
            <tr>
                <th valign="top">隐藏微信</th>
                <td>
                    <input type="checkbox" id="erphpdown_easepay_wxpay" name="erphpdown_easepay_wxpay" value="yes" <?php if($erphpdown_easepay_wxpay == 'yes') echo 'checked'; ?> /> 
                </td>
            </tr>
            <tr>
                <th valign="top">隐藏USDT</th>
                <td>
                    <input type="checkbox" id="erphpdown_easepay_usdt" name="erphpdown_easepay_usdt" value="yes" <?php if($erphpdown_easepay_usdt == 'yes') echo 'checked'; ?> /> 
                </td>
            </tr>
        </table>

        <br />
        <h3>11、码支付（支付宝/微信/QQ钱包）<span>个人第三方免签</span></h3>
        <div>新版码支付，支持市面上多数码支付平台，关于此接口的安全稳定性，请使用者自行把握，我们只对接接口，例如：<a href="http://erphpdown.com/go/codepay" target="_blank" rel="nofollow">点击查看</a></div>
        <table class="form-table">
            <tr>
                <th valign="top">商户ID</th>
                <td>
                    <input type="text" id="erphpdown_codepay_appid" name="erphpdown_codepay_appid" value="<?php echo $erphpdown_codepay_appid ; ?>" class="regular-text"/>
                </td>
            </tr>
            <tr>
                <th valign="top">商户密钥</th>
                <td>
                    <input type="text" id="erphpdown_codepay_appsecret" name="erphpdown_codepay_appsecret" value="<?php echo $erphpdown_codepay_appsecret; ?>" class="regular-text"/>
                </td>
            </tr>
            <tr>
                <th valign="top">API网关</th>
                <td>
                    <input type="text" id="erphpdown_codepay_api" name="erphpdown_codepay_api" value="<?php echo $erphpdown_codepay_api; ?>" class="regular-text"/>
                    <p>注意：地址最后需要带上斜杠/，例如http://codepay.erphpdown.com/</p>
                </td>
            </tr>
            <tr>
                <th valign="top">隐藏支付宝</th>
                <td>
                    <input type="checkbox" id="erphpdown_codepay_alipay" name="erphpdown_codepay_alipay" value="yes" <?php if($erphpdown_codepay_alipay == 'yes') echo 'checked'; ?> /> 
                </td>
            </tr>
            <tr>
                <th valign="top">隐藏微信</th>
                <td>
                    <input type="checkbox" id="erphpdown_codepay_wxpay" name="erphpdown_codepay_wxpay" value="yes" <?php if($erphpdown_codepay_wxpay == 'yes') echo 'checked'; ?> /> 
                </td>
            </tr>
            <tr>
                <th valign="top">隐藏QQ钱包</th>
                <td>
                    <input type="checkbox" id="erphpdown_codepay_qqpay" name="erphpdown_codepay_qqpay" value="yes" <?php if($erphpdown_codepay_qqpay == 'yes') echo 'checked'; ?> /> 
                </td>
            </tr>
        </table>
        
        

        <br />
        <h3>12、Stripe（信用卡/银联）</h3>
        回调地址：<?php echo ERPHPDOWN_URL;?>/payment/stripe/notify.php<br>
        设置webhook回调通知教程：<a href="https://www.mobantu.com/9718.html" target="_blank">https://www.mobantu.com/9718.html</a>
        <table class="form-table">
            <tr>
                <th valign="top">公钥Publishable Key</th>
                <td>
                    <input type="text" id="erphpdown_stripe_pk" name="erphpdown_stripe_pk" value="<?php echo $erphpdown_stripe_pk; ?>" class="regular-text"/>
                </td>
            </tr>
            <tr>
                <th valign="top">私钥Secret Key</th>
                <td>
                    <input type="text" id="erphpdown_stripe_sk" name="erphpdown_stripe_sk" value="<?php echo $erphpdown_stripe_sk ; ?>" class="regular-text"/>
                </td>
            </tr>
            <tr>
                <th valign="top">回调密钥签名</th>
                <td>
                    <input type="text" id="erphpdown_stripe_hk" name="erphpdown_stripe_hk" value="<?php echo $erphpdown_stripe_hk ; ?>" class="regular-text"/>
                </td>
            </tr>
        </table>

        <br />
        <h3>13、USDT转账 <span>手动处理</span></h3>
        请勿用于非法途径！<br>需后台手动处理补单（收到款后可在充值统计里的未支付里补单）。<br>若需要自动回调处理，可自行搭建epusdt然后安装相关扩展插件。关于此接口的安全稳定性，请使用者自行把握，我们只对接接口，例如：<a href="http://erphpdown.com/go/epupay" target="_blank" rel="nofollow">点击查看</a>
        <table class="form-table">
            <tr>
                <th valign="top">转币地址</th>
                <td>
                    <input type="text" id="erphpdown_usdt_address" name="erphpdown_usdt_address" value="<?php echo $erphpdown_usdt_address; ?>" class="regular-text"/>
                    <p>一般是TRC20网络的地址</p>
                </td>
            </tr>
            <tr>
                <th valign="top">公链名称</th>
                <td>
                    <input type="text" id="erphpdown_usdt_name" name="erphpdown_usdt_name" value="<?php echo $erphpdown_usdt_name ; ?>" class="regular-text"/>
                    <p>例如：TRC20网络</p>
                </td>
            </tr>
            <tr>
                <th valign="top">汇率</th>
                <td>
                    <input type="number" step="0.01" id="erphpdown_usdt_rmb" name="erphpdown_usdt_rmb" value="<?php echo $erphpdown_usdt_rmb; ?>" class="regular-text"/>
                    <p>填5表示1USDT=5元</p>
                </td>
            </tr>
            <?php if(function_exists('erphpdown_addon_epusdt')){?>
            <tr>
                <th valign="top">epusdt API地址</th>
                <td>
                    <input type="text" id="erphpdown_usdt_api" name="erphpdown_usdt_api" value="<?php echo $erphpdown_usdt_api ; ?>" class="regular-text"/>
                    <p>epusdt的api接口地址，例如：http://upay.erphpdown.com，结尾不要加/</p>
                </td>
            </tr>
            <tr>
                <th valign="top">epusdt API TOKEN</th>
                <td>
                    <input type="text" id="erphpdown_usdt_token" name="erphpdown_usdt_token" value="<?php echo $erphpdown_usdt_token ; ?>" class="regular-text"/>
                    <p>epusdt的api接口认证token</p>
                </td>
            </tr>
            <?php }?>
        </table>

        <?php if(plugin_check_ecpay()){?>
        <br />
        <h3>定制-绿界支付 <span>新台币</span></h3>
        接口申请：ecpay.com.tw
        <table class="form-table">
            <tr>
                <th valign="top">MerchantID</th>
                <td>
                    <input type="text" id="erphpdown_ecpay_MerchantID" name="erphpdown_ecpay_MerchantID" value="<?php echo $erphpdown_ecpay_MerchantID; ?>" class="regular-text"/>
                </td>
            </tr>
            <tr>
                <th valign="top">Hashkey</th>
                <td>
                    <input type="text" id="erphpdown_ecpay_HashKey" name="erphpdown_ecpay_HashKey" value="<?php echo $erphpdown_ecpay_HashKey ; ?>" class="regular-text"/>
                </td>
            </tr>
            <tr>
                <th valign="top">HashIV</th>
                <td>
                    <input type="text" id="erphpdown_ecpay_HashIV" name="erphpdown_ecpay_HashIV" value="<?php echo $erphpdown_ecpay_HashIV; ?>" class="regular-text"/>
                </td>
            </tr>
            <tr>
                <th valign="top">汇率</th>
                <td>
                    <input type="text" id="erphpdown_ecpay_rmb" name="erphpdown_ecpay_rmb" value="<?php echo $erphpdown_ecpay_rmb; ?>" class="regular-text"/>
                    <p>1元等于多少新台币，例如填5，则表示1元=5新台币</p>
                </td>
            </tr>
        </table>
        <?php }?>

        <?php if(plugin_check_newebpay()){?>
        <br />
        <h3>定制-蓝新金流 <span>新台币</span></h3>
        接口申请：www.newebpay.com
        <table class="form-table">
            <tr>
                <th valign="top">MerchantID</th>
                <td>
                    <input type="text" id="erphpdown_newebpay_MerchantID" name="erphpdown_newebpay_MerchantID" value="<?php echo $erphpdown_newebpay_MerchantID; ?>" class="regular-text"/>
                </td>
            </tr>
            <tr>
                <th valign="top">Hashkey</th>
                <td>
                    <input type="text" id="erphpdown_newebpay_HashKey" name="erphpdown_newebpay_HashKey" value="<?php echo $erphpdown_newebpay_HashKey ; ?>" class="regular-text"/>
                </td>
            </tr>
            <tr>
                <th valign="top">HashIV</th>
                <td>
                    <input type="text" id="erphpdown_newebpay_HashIV" name="erphpdown_newebpay_HashIV" value="<?php echo $erphpdown_newebpay_HashIV; ?>" class="regular-text"/>
                </td>
            </tr>
            <tr>
                <th valign="top">汇率</th>
                <td>
                    <input type="text" id="erphpdown_newebpay_rmb" name="erphpdown_newebpay_rmb" value="<?php echo $erphpdown_newebpay_rmb; ?>" class="regular-text"/>
                    <p>1元等于多少新台币，例如填5，则表示1元=5新台币</p>
                </td>
            </tr>
        </table>
        <?php }?>

        <p class="submit">
            <input type="submit" name="Submit" value="保存设置" class="button-primary"/>
            <div >技术支持：mobantu.com <a href="http://www.mobantu.com/6658.html" target="_blank">使用教程>></a></div>
        </p>      
    </form>
</div>