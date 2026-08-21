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
	if(isset($_POST['ice_ali_money_limit'])) update_option('ice_ali_money_limit', trim($_POST['ice_ali_money_limit']));
	if(isset($_POST['ice_ali_money_site'])) update_option('ice_ali_money_site', trim($_POST['ice_ali_money_site']));
	if(isset($_POST['ice_ali_money_author'])) update_option('ice_ali_money_author', trim($_POST['ice_ali_money_author']));
	if(isset($_POST['ice_ali_money_ref'])) update_option('ice_ali_money_ref', trim($_POST['ice_ali_money_ref']));
	if(isset($_POST['ice_ali_money_ref2'])) update_option('ice_ali_money_ref2', trim($_POST['ice_ali_money_ref2']));
	if(isset($_POST['erphp_aff_ip'])){
		update_option('erphp_aff_ip', trim($_POST['erphp_aff_ip']));
	}else{
		delete_option('erphp_aff_ip');
	}
	if(isset($_POST['ice_ali_money_checkin'])) update_option('ice_ali_money_checkin', trim($_POST['ice_ali_money_checkin']));
	if(isset($_POST['ice_ali_money_new'])) update_option('ice_ali_money_new', trim($_POST['ice_ali_money_new']));
	if(isset($_POST['ice_ali_vip_new'])) update_option('ice_ali_vip_new', $_POST['ice_ali_vip_new']);
	if(isset($_POST['ice_ali_money_reg'])) update_option('ice_ali_money_reg', trim($_POST['ice_ali_money_reg']));
	if(isset($_POST['erphp_mycred'])){
		update_option('erphp_mycred', trim($_POST['erphp_mycred']));
	}else{
		delete_option('erphp_mycred');
	}
	if(isset($_POST['erphp_to_mycred'])) update_option('erphp_to_mycred', trim($_POST['erphp_to_mycred']));
	if(isset($_POST['ice_tips'])) update_option('ice_tips', str_replace('\"', '"', trim($_POST['ice_tips'])));
	if(isset($_POST['ice_tips_see'])) update_option('ice_tips_see', str_replace('\"', '"', trim($_POST['ice_tips_see'])));
	if(isset($_POST['ice_tips_faka'])) update_option('ice_tips_faka', str_replace('\"', '"', trim($_POST['ice_tips_faka'])));
	if(isset($_POST['ice_tips_free'])) update_option('ice_tips_free', str_replace('\"', '"', trim($_POST['ice_tips_free'])));
	if(isset($_POST['ice_tips_card'])) update_option('ice_tips_card', str_replace('\"', '"', trim($_POST['ice_tips_card'])));
	if(isset($_POST['erphpdown_kefu'])) update_option('erphpdown_kefu', str_replace('\"', '"', trim($_POST['erphpdown_kefu'])));
	if(isset($_POST['erphpdown_downkey'])) update_option('erphpdown_downkey', trim($_POST['erphpdown_downkey']));
	if(isset($_POST['erphp_ajaxbuy'])){
		update_option('erphp_ajaxbuy', trim($_POST['erphp_ajaxbuy']));
	}else{
		delete_option('erphp_ajaxbuy');
	}
	if(isset($_POST['erphp_popdown'])){
		update_option('erphp_popdown', trim($_POST['erphp_popdown']));
	}else{
		delete_option('erphp_popdown');
	}
	if(isset($_POST['erphp_repeatdown_btn'])){
		update_option('erphp_repeatdown_btn', trim($_POST['erphp_repeatdown_btn']));
	}else{
		delete_option('erphp_repeatdown_btn');
	}
	if(isset($_POST['erphp_justbuy'])){
		update_option('erphp_justbuy', trim($_POST['erphp_justbuy']));
	}else{
		delete_option('erphp_justbuy');
	}
	if(isset($_POST['erphp_free_wait'])) update_option('erphp_free_wait', trim($_POST['erphp_free_wait']));
	if(isset($_POST['erphp_remind'])){
		update_option('erphp_remind', trim($_POST['erphp_remind']));
	}else{
		delete_option('erphp_remind');
	}
	if(isset($_POST['erphp_aff_money'])){
		update_option('erphp_aff_money', trim($_POST['erphp_aff_money']));
	}else{
		delete_option('erphp_aff_money');
	}
	if(isset($_POST['erphp_remind_recharge'])){
		update_option('erphp_remind_recharge', trim($_POST['erphp_remind_recharge']));
	}else{
		delete_option('erphp_remind_recharge');
	}
	if(isset($_POST['ice_name_alipay'])) update_option('ice_name_alipay', trim($_POST['ice_name_alipay']));
	if(isset($_POST['ice_proportion_alipay'])) update_option('ice_proportion_alipay', trim($_POST['ice_proportion_alipay']));
	if(isset($_POST['erphpdown_min_price'])) update_option('erphpdown_min_price', trim($_POST['erphpdown_min_price']));
	if(isset($_POST['epd_game_price'])){
		update_option('epd_game_price', $_POST['epd_game_price']);
	}else{
		delete_option('epd_game_price');
	}
	if(isset($_POST['erphp_wppay_cookie'])) update_option('erphp_wppay_cookie', trim($_POST['erphp_wppay_cookie']));
	if(isset($_POST['erphp_wppay_close'])){
		update_option('erphp_wppay_close', trim($_POST['erphp_wppay_close']));
	}else{
		delete_option('erphp_wppay_close');
	}
	if(isset($_POST['erphp_wppay_vip'])){
		update_option('erphp_wppay_vip', trim($_POST['erphp_wppay_vip']));
	}else{
		delete_option('erphp_wppay_vip');
	}
	if(isset($_POST['erphp_wppay_down'])){
		update_option('erphp_wppay_down', trim($_POST['erphp_wppay_down']));
	}else{
		delete_option('erphp_wppay_down');
	}
	if(isset($_POST['erphp_free_login'])){
		update_option('erphp_free_login', trim($_POST['erphp_free_login']));
	}else{
		delete_option('erphp_free_login');
	}
	if(isset($_POST['erphp_wppay_ip'])){
		update_option('erphp_wppay_ip', trim($_POST['erphp_wppay_ip']));
	}else{
		delete_option('erphp_wppay_ip');
	}
	if(isset($_POST['erphp_wppay_download'])){
		update_option('erphp_wppay_download', trim($_POST['erphp_wppay_download']));
	}else{
		delete_option('erphp_wppay_download');
	}
	if(isset($_POST['erphp_wppay_type'])) update_option('erphp_wppay_type', trim($_POST['erphp_wppay_type']));
	if(isset($_POST['erphp_wppay_payment'])) update_option('erphp_wppay_payment', trim($_POST['erphp_wppay_payment']));
	if(isset($_POST['erphp_addon_card'])){
		update_option('erphp_addon_card', trim($_POST['erphp_addon_card']));
	}else{
		delete_option('erphp_addon_card');
	}
	if(isset($_POST['erphp_addon_card_total'])){
		update_option('erphp_addon_card_total', trim($_POST['erphp_addon_card_total']));
	}else{
		delete_option('erphp_addon_card_total');
	}
	if(isset($_POST['erphp_addon_vipcard'])){
		update_option('erphp_addon_vipcard', trim($_POST['erphp_addon_vipcard']));
	}else{
		delete_option('erphp_addon_vipcard');
	}
	if(isset($_POST['erphp_addon_vipcard_aff'])){
		update_option('erphp_addon_vipcard_aff', trim($_POST['erphp_addon_vipcard_aff']));
	}else{
		delete_option('erphp_addon_vipcard_aff');
	}
	if(isset($_POST['erphp_addon_activation'])){
		update_option('erphp_addon_activation', trim($_POST['erphp_addon_activation']));
	}else{
		delete_option('erphp_addon_activation');
	}
	if(isset($_POST['erphp_addon_pancheck'])){
		update_option('erphp_addon_pancheck', trim($_POST['erphp_addon_pancheck']));
	}else{
		delete_option('erphp_addon_pancheck');
	}
	if(isset($_POST['erphp_addon_tuan'])){
		update_option('erphp_addon_tuan', trim($_POST['erphp_addon_tuan']));
	}else{
		delete_option('erphp_addon_tuan');
	}
	if(isset($_POST['erphpdown_direct_type'])) update_option('erphpdown_direct_type', trim($_POST['erphpdown_direct_type']));
	if(isset($_POST['erphp_promo'])){
		update_option('erphp_promo', trim($_POST['erphp_promo']));
	}else{
		delete_option('erphp_promo');
	}
	if(isset($_POST['erphp_promo_code1'])) update_option('erphp_promo_code1',trim($_POST['erphp_promo_code1']));
	if(isset($_POST['erphp_promo_money1'])) update_option('erphp_promo_money1',trim($_POST['erphp_promo_money1']));
	if(isset($_POST['erphp_promo_code2'])) update_option('erphp_promo_code2',trim($_POST['erphp_promo_code2']));
	if(isset($_POST['erphp_promo_money2'])) update_option('erphp_promo_money2',trim($_POST['erphp_promo_money2']));

	echo'<div class="updated settings-error"><p>更新成功！</p></div>';
}

$ice_ali_money_limit    = get_option('ice_ali_money_limit');
$ice_ali_money_site    = get_option('ice_ali_money_site');
$ice_ali_money_author   = get_option('ice_ali_money_author');
$ice_ali_money_ref    = get_option('ice_ali_money_ref');
$ice_ali_money_ref2    = get_option('ice_ali_money_ref2');
$erphp_aff_ip = get_option('erphp_aff_ip');
$ice_ali_money_checkin = get_option('ice_ali_money_checkin');
$ice_ali_money_new    = get_option('ice_ali_money_new');
$ice_ali_vip_new    = get_option('ice_ali_vip_new');
$ice_ali_money_reg    = get_option('ice_ali_money_reg');
$erphp_mycred    = get_option('erphp_mycred');
$erphp_to_mycred    = get_option('erphp_to_mycred');
$ice_tips    = get_option('ice_tips');
$ice_tips_see    = get_option('ice_tips_see');
$ice_tips_faka    = get_option('ice_tips_faka');
$ice_tips_free    = get_option('ice_tips_free');
$ice_tips_card    = get_option('ice_tips_card');
$erphpdown_kefu    = get_option('erphpdown_kefu');
$erphpdown_downkey    = get_option('erphpdown_downkey')?get_option('erphpdown_downkey'):wp_generate_password(7, false);
$erphp_ajaxbuy    = get_option('erphp_ajaxbuy');
$erphp_popdown    = get_option('erphp_popdown');
$erphp_repeatdown_btn = get_option('erphp_repeatdown_btn');
$erphp_justbuy = get_option('erphp_justbuy');
$erphp_free_wait = get_option('erphp_free_wait');
$erphp_remind = get_option('erphp_remind');
$erphp_aff_money = get_option('erphp_aff_money');
$erphp_remind_recharge = get_option('erphp_remind_recharge');
$ice_name_alipay    = get_option('ice_name_alipay');
$ice_proportion_alipay    = get_option('ice_proportion_alipay');
$erphpdown_min_price    = get_option('erphpdown_min_price');
$epd_game_price  = get_option('epd_game_price');
$erphp_wppay_cookie    = get_option('erphp_wppay_cookie');
$erphp_wppay_close    = get_option('erphp_wppay_close');
$erphp_wppay_vip    = get_option('erphp_wppay_vip');
$erphp_wppay_down    = get_option('erphp_wppay_down');
$erphp_free_login    = get_option('erphp_free_login');
$erphp_wppay_ip    = get_option('erphp_wppay_ip');
$erphp_wppay_download    = get_option('erphp_wppay_download');
$erphp_wppay_type    = get_option('erphp_wppay_type');
$erphp_wppay_payment    = get_option('erphp_wppay_payment');
$erphp_addon_card    = get_option('erphp_addon_card');
$erphp_addon_card_total    = get_option('erphp_addon_card_total');
$erphp_addon_vipcard    = get_option('erphp_addon_vipcard');
$erphp_addon_vipcard_aff    = get_option('erphp_addon_vipcard_aff');
$erphp_addon_activation = get_option('erphp_addon_activation');
$erphp_addon_pancheck = get_option('erphp_addon_pancheck');
$erphp_addon_tuan = get_option('erphp_addon_tuan');
$erphpdown_direct_type = get_option('erphpdown_direct_type');
$erphp_promo = get_option('erphp_promo');
$erphp_promo_code1 = get_option('erphp_promo_code1');
$erphp_promo_money1 = get_option('erphp_promo_money1');
$erphp_promo_code2 = get_option('erphp_promo_code2');
$erphp_promo_money2 = get_option('erphp_promo_money2');

$erphpdown_payname = get_option('ice_name_alipay')?get_option('ice_name_alipay'):'模板币';
?>
 <style>.form-table th{font-weight: 400}</style>
 <div class="wrap">
 	<h1>基础设置</h1>
 	<form method="post" action="<?php echo admin_url('admin.php?page='.plugin_basename(__FILE__)); ?>">
 		<h3>基础设置</h3>
 		<table class="form-table">
 			<tr>
 				<th valign="top">货币昵称 *</th>
 				<td>
 					<input type="text" id="ice_name_alipay" name="ice_name_alipay" value="<?php echo $ice_name_alipay;?>" class="regular-text"/> （例如：模板币）
 				</td>
 			</tr>
 			<tr>
 				<th valign="top">推广消费提成（百分点）*</th>
 				<td>
 					<input type="number" step="0.01" id="ice_ali_money_ref" name="ice_ali_money_ref" value="<?php echo $ice_ali_money_ref; ?>" required="required" class="regular-text"/>% 
                    <p>A推广B，B消费后A的提成</p>
 				</td>
 			</tr>
            <tr>
                <th valign="top">二级推广消费提成（百分点）</th>
                <td>
                    <input type="number" step="0.01" id="ice_ali_money_ref2" name="ice_ali_money_ref2" value="<?php echo $ice_ali_money_ref2; ?>" class="regular-text"/>% 
                    <p>A推广B，B推广C，C消费后A的提成</p>
                </td>
            </tr>
 			<tr>
 				<th valign="top">作者分成（百分点）</th>
 				<td>
 					<input type="number" step="0.01" id="ice_ali_money_author" name="ice_ali_money_author" value="<?php echo $ice_ali_money_author; ?>"  class="regular-text"/>%
                    <p>例如输入80，表示作者A发布的收费资源用户B购买后，A将得到其资源价格的80%，不填则默认100%</p>
 				</td>
 			</tr>
 			<tr>
 				<th valign="top">每日签到奖励</th>
 				<td>
 					<input type="number" step="0.01" id="ice_ali_money_checkin" name="ice_ali_money_checkin" value="<?php echo $ice_ali_money_checkin; ?>"  class="regular-text"/> <?php echo $erphpdown_payname;?> （请输入一个数字，0或留空则不启用）
 				</td>
 			</tr>
 			<tr>
 				<th valign="top">新注册赠送</th>
 				<td>
 					<input type="number" step="0.01" id="ice_ali_money_new" name="ice_ali_money_new" value="<?php echo $ice_ali_money_new; ?>" class="regular-text"/> <?php echo $erphpdown_payname;?> （请输入一个数字，赠送新用户，0表示不赠送）
 				</td>
 			</tr>
 			<tr>
 				<th valign="top">新注册赠送VIP</th>
 				<td>
 					<select name="ice_ali_vip_new">
 						<option value="">不赠送</option>
 						<option value="6" <?php if($ice_ali_vip_new == '6') echo 'selected="selected"';?>>体验VIP</option>
 						<option value="7" <?php if($ice_ali_vip_new == '7') echo 'selected="selected"';?>>包月VIP</option>
 						<option value="8" <?php if($ice_ali_vip_new == '8') echo 'selected="selected"';?>>包季VIP</option>
 						<option value="9" <?php if($ice_ali_vip_new == '9') echo 'selected="selected"';?>>包年VIP</option>
 						<option value="10" <?php if($ice_ali_vip_new == '10') echo 'selected="selected"';?>>终身VIP</option>
 					</select>
 					<p>赠送新注册用户的VIP类型</p>
 				</td>
 			</tr>
 			<tr>
 				<th valign="top">推广注册奖励</th>
 				<td>
 					<input type="number" step="0.01" id="ice_ali_money_reg" name="ice_ali_money_reg" value="<?php echo $ice_ali_money_reg; ?>" class="regular-text"/> <?php echo $erphpdown_payname;?> （请输入一个数字，奖励推广人，0表示不奖励）
 				</td>
 			</tr>
 			<tr>
 				<th valign="top">推广注册不限制IP</th>
 				<td>
 					<input type="checkbox" id="erphp_aff_ip" name="erphp_aff_ip" value="yes" <?php if($erphp_aff_ip == 'yes') echo 'checked'; ?> /> 不限制（默认一个IP仅可推广注册一个用户，你可以勾选不限制，那么一个IP就可以推广注册多个用户）
 				</td>
 			</tr>
 			<tr>
 				<th valign="top">提现规则 *</th>
 				<td>
 					<input type="number" step="0.01" id="ice_ali_money_limit" name="ice_ali_money_limit" value="<?php echo $ice_ali_money_limit; ?>" required="required" class="regular-text"/> <?php echo $erphpdown_payname;?>以上方可提现 （请输入一个整数）
 				</td>
 			</tr>
 			<tr>
 				<th valign="top">提现手续费（百分点）*</th>
 				<td>
 					<input type="number" step="0.01" id="ice_ali_money_site" name="ice_ali_money_site" value="<?php echo $ice_ali_money_site; ?>" required="required" class="regular-text"/>% （请输入一个整数）
 				</td>
 			</tr>
 			<tr>
 				<th valign="top">购买下载说明</th>
 				<td>
 					<textarea id="ice_tips" name="ice_tips" placeholder="客服QQ：82708210" rows="5" cols="70"><?php echo $ice_tips; ?></textarea>
 				</td>
 			</tr>
 			<tr>
 				<th valign="top">购买查看说明</th>
 				<td>
 					<textarea id="ice_tips_see" name="ice_tips_see" placeholder="客服QQ：82708210" rows="5" cols="70"><?php echo $ice_tips_see; ?></textarea>
 					<p>用于显示在短代码[erphpdown]处</p>
 				</td>
 			</tr>
 			<tr>
 				<th valign="top">购买发卡说明</th>
 				<td>
 					<textarea id="ice_tips_see" name="ice_tips_faka" placeholder="客服QQ：82708210" rows="5" cols="70"><?php echo $ice_tips_faka; ?></textarea>
 				</td>
 			</tr>
 			<tr>
 				<th valign="top">免费下载说明</th>
 				<td>
 					<textarea id="ice_tips_free" name="ice_tips_free" placeholder="客服QQ：82708210" rows="5" cols="70"><?php echo $ice_tips_free; ?></textarea>
 				</td>
 			</tr>
 			<tr>
 				<th valign="top">充值卡购买说明</th>
 				<td>
 					<textarea id="ice_tips_card" name="ice_tips_card" placeholder="客服QQ：82708210，购卡地址：http://erphpdown.com/card" rows="5" cols="70"><?php echo $ice_tips_card; ?></textarea>
 				</td>
 			</tr>
 			<tr>
 				<th valign="top">客服信息</th>
 				<td>
 					<textarea id="erphpdown_kefu" name="erphpdown_kefu" placeholder="客服QQ：82708210" rows="5" cols="70"><?php echo $erphpdown_kefu; ?></textarea>
 					<p>部分充值接口二维码界面会显示</p>
 				</td>
 			</tr>
 			<tr>
 				<th valign="top">Ajax无跳转购买</th>
 				<td>
 					<input type="checkbox" id="erphp_ajaxbuy" name="erphp_ajaxbuy" value="yes" <?php if($erphp_ajaxbuy == 'yes') echo 'checked'; ?> /> 
 				</td>
 			</tr>
 			<tr>
 				<th valign="top">直接支付购买</th>
 				<td>
 					<input type="checkbox" id="erphp_justbuy" name="erphp_justbuy" value="yes" <?php if($erphp_justbuy == 'yes') echo 'checked'; ?> /> （用户单独购买资源时可直接支付，跳过充值，如有启用了免登录购买功能，请务必勾选此选项）
 				</td>
 			</tr>
 			<tr>
 				<th valign="top">充值提醒</th>
 				<td>
 					<input type="checkbox" id="erphp_remind_recharge" name="erphp_remind_recharge" value="yes" <?php if($erphp_remind_recharge == 'yes') echo 'checked'; ?> /> （有用户充值时邮件提醒管理员，收件箱为【设置-常规】里的管理邮件地址，请务必提前配置好SMTP发件，否则会导致卡顿。）
 				</td>
 			</tr>
 			<tr>
 				<th valign="top">购买提醒</th>
 				<td>
 					<input type="checkbox" id="erphp_remind" name="erphp_remind" value="yes" <?php if($erphp_remind == 'yes') echo 'checked'; ?> /> （有用户购买时邮件提醒管理员，收件箱为【设置-常规】里的管理邮件地址，请务必提前配置好SMTP发件，否则会导致卡顿。开启提醒可能会导致用户购买卡顿，请知晓！）
 				</td>
 			</tr>
 			<tr>
 				<th valign="top">单独提现余额</th>
 				<td>
 					<input type="checkbox" id="erphp_aff_money" name="erphp_aff_money" value="yes" <?php if($erphp_aff_money == 'yes') echo 'checked'; ?> /> （<span style="color:#ff5f33">请勿随意开启，除非你知晓此功能，开启后之前的默认余额都无法提现。</span>推广+投稿售卖收入+其他部分奖励(不含签到)不进默认余额，而是单独统计，这样针对有提现功能的站，只能对单独提现余额进行提现。若之前有使用低于v13.2版本，需要重启一下插件）
 				</td>
 			</tr>
 		</table>
 		<h3>充值设置</h3>
 		<table class="form-table">
 			<tr>
 				<th valign="top">充值比例 *</th>
 				<td>
 					<input type="number" id="ice_proportion_alipay" name="ice_proportion_alipay" value="<?php echo $ice_proportion_alipay;?>" required="required" class="regular-text"/> （请输入一个整数，例如：10，代表1元=10 <?php echo $erphpdown_payname;?>）
 				</td>
 			</tr>
 			<tr>
 				<th valign="top">最小充值金额</th>
 				<td>
 					<input type="text" id="erphpdown_min_price" name="erphpdown_min_price" value="<?php echo $erphpdown_min_price;?>" class="regular-text"/> 元（这里是充值的最小金额，不设置则不限制）
 				</td>
 			</tr>
 			<tr>
				<th valign="top">充值奖励</th>
				<td>
					<?php if($epd_game_price){ $cnt = count($epd_game_price['buy']); if($cnt){?>
					<div class="prices">
						<?php for($i=0; $i<$cnt;$i++){?>
						<p>充值 <input type="number" name="epd_game_price[buy][]" value="<?php echo $epd_game_price['buy'][$i]?>" class="regular-text" style="width:150px;" step="0.01"/> 元 实际得到 <input type="number" name="epd_game_price[get][]" value="<?php echo $epd_game_price['get'][$i]?>" class="regular-text" style="width:150px;" step="0.01"/> 元 <a href="javascript:;" class="del-price">删除</a></p>
						<?php }?>
					</div>
					<?php }}else{?>
					<div class="prices"></div>
					<?php }?>
					<button class="button add-more-price" type="button">+添加规则</button>
					<p>（示例：充值比例是1:10，那么可以设置充值1元（10<?php echo get_option('ice_name_alipay');?>），实际得到1.2元（12<?php echo get_option('ice_name_alipay');?>），相当于赠送了2<?php echo get_option('ice_name_alipay');?>）</p>
				</td>
			</tr>
 		</table>
 		<h3>优惠码</h3>
 		<p>节日促销时可以用一用，平时不用可留空；</p>
 		<table class="form-table">
 			<tr>
 				<th valign="top">启用</th>
 				<td>
 					<input type="checkbox" id="erphp_promo" name="erphp_promo" value="yes" <?php if($erphp_promo == 'yes') echo 'checked'; ?> /> 
 				</td>
 			</tr>
 			<tr>
 				<th valign="top">减多少</th>
 				<td>
 					优惠码 <input type="text" id="erphp_promo_code1" name="erphp_promo_code1" value="<?php echo $erphp_promo_code1;?>" style="width: 200px;" class="regular-text"/> 立减 <input type="text" id="erphp_promo_money1" name="erphp_promo_money1" value="<?php echo $erphp_promo_money1;?>" style="width: 100px" class="regular-text"/> <?php echo get_option('ice_name_alipay');?>（使用优惠码后直接减多少，比如填20就价格少20）
 				</td>
 			</tr>
 			<tr>
 				<th valign="top">打几折</th>
 				<td>
 					优惠码 <input type="text" id="erphp_promo_code2" name="erphp_promo_code2" value="<?php echo $erphp_promo_code2;?>" style="width: 200px;" class="regular-text"/> 立打 <input type="text" id="erphp_promo_money2" name="erphp_promo_money2" value="<?php echo $erphp_promo_money2;?>" style="width: 100px" class="regular-text"/> 折（使用优惠码后直接打几折，比如填8就打8折）
 				</td>
 			</tr>
 		</table>
 		<h3>下载设置</h3>
 		<table class="form-table">
 			<tr>
 				<th valign="top">免费下载等待时间</th>
 				<td>
 					<input type="number" step="1" id="erphp_free_wait" name="erphp_free_wait" value="<?php echo $erphp_free_wait; ?>"  class="regular-text"/>秒
 					<p>普通用户下载免费资源前等待多少秒，VIP无需等待，留空则无需等待</p>
 				</td>
 			</tr>
 			<tr>
 				<th valign="top">下载标识码 *</th>
 				<td>
 					<input type="text" id="erphpdown_downkey" name="erphpdown_downkey" value="<?php echo $erphpdown_downkey;?>" class="regular-text" required="required"/> 
          <p>务必设置一个随机字符串，长度为8位左右即可，不要告知他人</p>
 				</td>
 			</tr>
 			<tr>
 				<th valign="top">后缀文件直接下载</th>
 				<td>
 					<input type="text" id="erphpdown_direct_type" name="erphpdown_direct_type" value="<?php echo $erphpdown_direct_type;?>" class="regular-text"/> 
          <p>下载地址里哪些后缀文件需要直接下载而不是访问，例如pdf,doc,txt,jpg等，不需要填压缩包的后缀，多个后缀名用英文半角逗号<code>,</code>隔开<br>特别说明：如果文件较大，网站加载文件很费带宽，可能会导致网站卡顿<br>建议下载地址都使用压缩包或者网盘链接，不要直接放后缀文件地址</p>
 				</td>
 			</tr>
 			<tr>
 				<th valign="top">弹窗下载</th>
 				<td>
 					<input type="checkbox" id="erphp_popdown" name="erphp_popdown" value="yes" <?php if($erphp_popdown == 'yes') echo 'checked'; ?> /> 
 				</td>
 			</tr>
 			<tr>
 				<th valign="top">重复购买显示下载按钮</th>
 				<td>
 					<input type="checkbox" id="erphp_repeatdown_btn" name="erphp_repeatdown_btn" value="yes" <?php if($erphp_repeatdown_btn == 'yes') echo 'checked'; ?> /> 
 					<p>文章开启可重复购买时，已购买过的会在显示再次购买按钮的同时也显示一个下载按钮</p>
 				</td>
 			</tr>
 		</table>
 		<h3>免登录购买设置</h3>
 		<p class="description">发布文章时下载模式可选择【下载】【查看】【部分查看】或【免登录】，自13.0版本开始，【下载】【查看】【部分查看】模式已同时支持登录与免登录购买，勾选下面的允许免登录购买即可，老的【免登录】模式会逐渐废弃</p>
 		<table class="form-table">
 			<tr>
 				<th valign="top">关闭旧版免登录</th>
 				<td>
 					<input type="checkbox" id="erphp_wppay_close" name="erphp_wppay_close" value="yes" <?php if($erphp_wppay_close == 'yes') echo 'checked'; ?> />（如果你未使用旧版免登录，也就是发布文章时erphpdown属性的收费模式没选过免登录（旧），建议关闭）
 				</td>
 			</tr>
 			<tr style="display:none">
 				<th valign="top">VIP允许免登录购买</th>
 				<td>
 					<input type="checkbox" id="erphp_wppay_vip" name="erphp_wppay_vip" value="yes" <?php if($erphp_wppay_vip == 'yes') echo 'checked'; ?> />（默认是需要登录才能购买，启用后会同时支持未登录的游客直接支付购买，基于用户IP来判断VIP状态）
 				</td>
 			</tr>
 			<tr>
 				<th valign="top">下载、查看、部分查看模式允许免登录购买</th>
 				<td>
 					<input type="checkbox" id="erphp_wppay_down" name="erphp_wppay_down" value="yes" <?php if($erphp_wppay_down == 'yes') echo 'checked'; ?> />（默认是需要登录才能购买，启用后会同时支持未登录的游客直接支付购买；购买样式固定为弹窗跳转；不支持多价格）
 				</td>
 			</tr>
 			<tr>
 				<th valign="top">普通免费资源必须登录</th>
 				<td>
 					<input type="checkbox" id="erphp_free_login" name="erphp_free_login" value="yes" <?php if($erphp_free_login == 'yes') echo 'checked'; ?> />（启用【下载、查看、部分查看模式允许免登录购买】后，普通免费的资源需登录才能下载查看）
 				</td>
 			</tr>
 			<tr>
 				<th valign="top">免登录模式购买样式</th>
 				<td>
 					<select name="erphp_wppay_type" id="erphp_wppay_type">
 						<option value ="scan">弹窗扫码（仅支持旧版免登录）（仅支持部分支付接口）</option>
 						<option value ="link" <?php if($erphp_wppay_type == 'link') echo 'selected="selected"';?>>弹窗跳转（支持所有支付接口）</option>
 					</select>
 					<p>推荐使用第二种：弹窗跳转。第一种支持的接口有限，但能记录Cookie；第二种支持所有接口，但不能记录Cookie，不过只要开启了通过IP判断，Cookie不重要</p>
 				</td>
 			</tr>
 			<tr class="scanment">
 				<th valign="top">弹窗扫码支付接口</th>
 				<td>
 					<select name="erphp_wppay_payment">
 						<option value ="f2fpay" <?php if($erphp_wppay_payment == 'f2fpay') echo 'selected="selected"';?>>支付宝当面付</option>
 						<option value ="weixin" <?php if($erphp_wppay_payment == 'weixin') echo 'selected="selected"';?>>官方微信扫码支付</option>
 						<option value ="f2fpay_weixin" <?php if($erphp_wppay_payment == 'f2fpay_weixin') echo 'selected="selected"';?>>支付宝当面付/官方微信扫码支付</option>
 						<option value ="paypy" <?php if($erphp_wppay_payment == 'paypy') echo 'selected="selected"';?>>Paypy个人免签</option>
 						<option value ="f2fpay_paypy" <?php if($erphp_wppay_payment == 'f2fpay_paypy') echo 'selected="selected"';?>>支付宝当面付/Paypy微信个人免签</option>
 						<option value ="payjs" <?php if($erphp_wppay_payment == 'payjs') echo 'selected="selected"';?>>Payjs</option>
 						<option value ="hupiv3" <?php if($erphp_wppay_payment == 'hupiv3') echo 'selected="selected"';?>>虎皮椒V3</option>
 						<option value ="f2fpay_hupiv3" <?php if($erphp_wppay_payment == 'f2fpay_hupiv3') echo 'selected="selected"';?>>支付宝当面付/虎皮椒V3微信支付</option>
 						<option value ="vpay" <?php if($erphp_wppay_payment == 'vpay') echo 'selected="selected"';?>>V免签</option>
 					</select>
 				</td>
 			</tr>
 			<tr>
				<th valign="top">浏览器Cookie过期天数 *</th>
				<td>
					<input type="number" id="erphp_wppay_cookie" name="erphp_wppay_cookie" value="<?php echo $erphp_wppay_cookie ; ?>" class="regular-text" required="required"/>
				</td>
			</tr>
 			<tr>
 				<th valign="top">通过IP判断</th>
 				<td>
 					<input type="checkbox" id="erphp_wppay_ip" name="erphp_wppay_ip" value="yes" <?php if($erphp_wppay_ip == 'yes') echo 'checked'; ?> />（<span style="color: #ff5f33">建议勾选</span>，否则可能出现支付后还是无法下载查看。勾选后就算换了浏览器，只要IP不变，一样会判断成已支付）
 				</td>
 			</tr>
 			<tr>
 				<th valign="top">彻底关闭下载页面的免登录权限</th>
 				<td>
 					<input type="checkbox" id="erphp_wppay_download" name="erphp_wppay_download" value="yes" <?php if($erphp_wppay_download == 'yes') echo 'checked'; ?> />（<span style="color: #ff5f33">不明白作用请勿勾选</span>，插件跳转的下载页面不允许未登录情况下请求权限）
 				</td>
 			</tr>
 		</table>
 		<script>
        jQuery(function($){
            if($("#erphp_wppay_type").val() == 'link'){
                $(".scanment").css("display", "none");
            }

            $("#erphp_wppay_type").change(function(){
                if($(this).val() == 'link'){
                    $(".scanment").css("display", "none");
                }else{
                    $(".scanment").css("display", "table-row");
                }
            });
        });
    </script>
 		<h3>免费扩展</h3>
 		<table class="form-table">
 			<tr>
 				<th valign="top">充值卡</th>
 				<td>
 					<input type="checkbox" id="erphp_addon_card" name="erphp_addon_card" value="yes" <?php if($erphp_addon_card == 'yes') echo 'checked'; ?> />（充值到网站余额，请不要同时安装充值卡扩展插件，以免冲突）<input type="checkbox" id="erphp_addon_card_total" name="erphp_addon_card_total" value="yes" <?php if($erphp_addon_card_total == 'yes') echo 'checked'; ?> />算进每日统计
 				</td>
 			</tr>
 			<tr>
 				<th valign="top">VIP充值卡</th>
 				<td>
 					<input type="checkbox" id="erphp_addon_vipcard" name="erphp_addon_vipcard" value="yes" <?php if($erphp_addon_vipcard == 'yes') echo 'checked'; ?> />（直接升级VIP，请不要同时安装VIP充值卡扩展插件，以免冲突；不会算进每日充值统计）<input type="checkbox" id="erphp_addon_vipcard_aff" name="erphp_addon_vipcard_aff" value="yes" <?php if($erphp_addon_vipcard_aff == 'yes') echo 'checked'; ?> />算推广提成
 				</td>
 			</tr>
 			<tr>
 				<th valign="top">Mycred兑换</th>
 				<td>
 					<input type="checkbox" id="erphp_mycred" name="erphp_mycred" value="yes" <?php if($erphp_mycred == 'yes') echo 'checked'; ?> />（需安装<a href="https://wordpress.org/plugins/mycred/" target="_blank">mycred插件</a>） 兑换比例：
 					<input type="number" step="0.1" id="erphp_to_mycred" name="erphp_to_mycred" value="<?php echo $erphp_to_mycred; ?>" style="width:100px" />（输入10则表示：10 mycred的积分 = 1 erphpdown的<?php echo $erphpdown_payname;?>，建议要么填10要么填1，否则可能会出问题）
 				</td>
 			</tr>
 			<tr>
 				<th valign="top">激活码发放</th>
 				<td>
 					<input type="checkbox" id="erphp_addon_activation" name="erphp_addon_activation" value="yes" <?php if($erphp_addon_activation == 'yes') echo 'checked'; ?> />（请不要同时安装激活码发放扩展插件，以免冲突）
 				</td>
 			</tr>
 			<tr>
 				<th valign="top">网盘检测</th>
 				<td>
 					<input type="checkbox" id="erphp_addon_pancheck" name="erphp_addon_pancheck" value="yes" <?php if($erphp_addon_pancheck == 'yes') echo 'checked'; ?> />（请不要同时安装网站检测扩展插件，以免冲突；支持百度网盘、蓝奏云盘、天翼云盘的链接失效检测，用户在购买前检测有效后再购买）
 				</td>
 			</tr>
 			<tr>
 				<th valign="top">团购下载</th>
 				<td>
 					<input type="checkbox" id="erphp_addon_tuan" name="erphp_addon_tuan" value="yes" <?php if($erphp_addon_tuan == 'yes') echo 'checked'; ?> />（<span style="color: #ff5f33">第一次启用请先停用插件再重新启用插件！</span>请不要同时安装团购扩展插件，以免冲突；发布文章时可设置团购相关的选项，仅对下载模式有效，）
 				</td>
 			</tr>
 		</table>
 		<p class="submit">
 			<input type="submit" name="Submit" value="保存设置" class="button-primary"/>
 			<div >技术支持：mobantu.com <a href="http://www.mobantu.com/6658.html" target="_blank">使用教程>></a></div>
 		</p>      
 	</form>
 	<script>
  jQuery(".add-more-price").click(function(){
    jQuery(".prices").append('<p>充值 <input type="number" name="epd_game_price[buy][]" value="" class="regular-text" style="width:150px;" step="0.01"/> 元 实际得到 <input type="number" name="epd_game_price[get][]" value="" class="regular-text" style="width:150px;" step="0.01"/> 元 <a href="javascript:;" class="del-price">删除</a></p>');
    jQuery(".del-price").click(function(){
      jQuery(this).parent().remove();
    });
    return false;
  });

  
  jQuery(".del-price").click(function(){
    jQuery(this).parent().remove();
  });
</script>
 </div>
