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
	if(isset($_POST['erphp_url_front_favicon'])) update_option('erphp_url_front_favicon', trim($_POST['erphp_url_front_favicon']));
	if(isset($_POST['erphp_url_front_vip'])) update_option('erphp_url_front_vip', trim($_POST['erphp_url_front_vip']));
	if(isset($_POST['erphp_url_front_recharge'])) update_option('erphp_url_front_recharge', trim($_POST['erphp_url_front_recharge']));
	if(isset($_POST['erphp_url_front_login'])) update_option('erphp_url_front_login', trim($_POST['erphp_url_front_login']));
	if(isset($_POST['erphp_url_front_noadmin'])){
		update_option('erphp_url_front_noadmin', trim($_POST['erphp_url_front_noadmin']));
	}else{
		delete_option('erphp_url_front_noadmin');
	}
	if(isset($_POST['erphp_url_front_novip'])){
		update_option('erphp_url_front_novip', trim($_POST['erphp_url_front_novip']));
	}else{
		delete_option('erphp_url_front_novip');
	}
	if(isset($_POST['erphp_url_front_notx'])){
		update_option('erphp_url_front_notx', trim($_POST['erphp_url_front_notx']));
	}else{
		delete_option('erphp_url_front_notx');
	}
	if(isset($_POST['erphp_url_front_nogm'])){
		update_option('erphp_url_front_nogm', trim($_POST['erphp_url_front_nogm']));
	}else{
		delete_option('erphp_url_front_nogm');
	}
	if(isset($_POST['erphp_url_front_notg'])){
		update_option('erphp_url_front_notg', trim($_POST['erphp_url_front_notg']));
	}else{
		delete_option('erphp_url_front_notg');
	}
	if(isset($_POST['erphp_url_front_userpage'])) update_option('erphp_url_front_userpage', trim($_POST['erphp_url_front_userpage']));
	if(isset($_POST['erphp_url_front_success'])) update_option('erphp_url_front_success', trim($_POST['erphp_url_front_success']));
	if(isset($_POST['erphp_post_types'])){
		update_option('erphp_post_types', $_POST['erphp_post_types']);
	}else{
		delete_option('erphp_post_types');
	}
	if(isset($_POST['erphp_admin_users_filter'])){
		update_option('erphp_admin_users_filter', $_POST['erphp_admin_users_filter']);
	}else{
		delete_option('erphp_admin_users_filter');
	}
	if(isset($_POST['erphp_down_default'])) update_option('erphp_down_default', $_POST['erphp_down_default']);
	if(isset($_POST['member_down_default'])) update_option('member_down_default', $_POST['member_down_default']);
	if(isset($_POST['down_price_type_default'])) update_option('down_price_type_default', $_POST['down_price_type_default']);
	if(isset($_POST['down_price_default'])) update_option('down_price_default', $_POST['down_price_default']);
	if(isset($_POST['down_prices_default'])) update_option('down_prices_default', $_POST['down_prices_default']);
	if(isset($_POST['down_days_default'])) update_option('down_days_default', $_POST['down_days_default']);
	if(isset($_POST['erphp_custom_css'])) update_option('erphp_custom_css', $_POST['erphp_custom_css']);
	if(isset($_POST['erphp_blank_domains'])) update_option('erphp_blank_domains', $_POST['erphp_blank_domains']);
	if(isset($_POST['erphp_colon_domains'])) update_option('erphp_colon_domains', $_POST['erphp_colon_domains']);
	if(isset($_POST['erphp_order_title'])) update_option('erphp_order_title', $_POST['erphp_order_title']);
	if(isset($_POST['erphp_downurl_old'])) update_option('erphp_downurl_old', $_POST['erphp_downurl_old']);
	if(isset($_POST['erphp_downurl_new'])) update_option('erphp_downurl_new', $_POST['erphp_downurl_new']);
	if(isset($_POST['erphp_metabox_mini'])){
		update_option('erphp_metabox_mini', $_POST['erphp_metabox_mini']);
	}else{
		delete_option('erphp_metabox_mini');
	}
	if(isset($_POST['erphp_see2_style'])){
		update_option('erphp_see2_style', $_POST['erphp_see2_style']);
	}else{
		delete_option('erphp_see2_style');
	}
	if(isset($_POST['erphp_hide_style'])){
		update_option('erphp_hide_style', $_POST['erphp_hide_style']);
	}else{
		delete_option('erphp_hide_style');
	}
	if(isset($_POST['erphp_box_down_title'])) update_option('erphp_box_down_title', $_POST['erphp_box_down_title']);
	if(isset($_POST['erphp_box_see_title'])) update_option('erphp_box_see_title', $_POST['erphp_box_see_title']);
	if(isset($_POST['erphp_box_faka_title'])) update_option('erphp_box_faka_title', $_POST['erphp_box_faka_title']);
	if(isset($_POST['erphp_box_style'])) update_option('erphp_box_style', $_POST['erphp_box_style']);
	if(isset($_POST['erphp_ad_download'])) update_option('erphp_ad_download', str_replace('\"', '"', $_POST['erphp_ad_download']));
	echo'<div class="updated settings-error"><p>更新成功！</p></div>';
}

$erphp_url_front_favicon = get_option('erphp_url_front_favicon');
$erphp_url_front_vip = get_option('erphp_url_front_vip');
$erphp_url_front_recharge = get_option('erphp_url_front_recharge');
$erphp_url_front_login = get_option('erphp_url_front_login');
$erphp_url_front_noadmin = get_option('erphp_url_front_noadmin');
$erphp_url_front_novip = get_option('erphp_url_front_novip');
$erphp_url_front_notx = get_option('erphp_url_front_notx');
$erphp_url_front_nogm = get_option('erphp_url_front_nogm');
$erphp_url_front_notg = get_option('erphp_url_front_notg');
$erphp_url_front_userpage = get_option('erphp_url_front_userpage');
$erphp_url_front_success = get_option('erphp_url_front_success');
$erphp_post_types = get_option('erphp_post_types');
$erphp_admin_users_filter = get_option('erphp_admin_users_filter');
$erphp_down_default = get_option('erphp_down_default');
$member_down_default = get_option('member_down_default');
$down_price_type_default = get_option('down_price_type_default');
$down_price_default = get_option('down_price_default');
$down_prices_default = get_option('down_prices_default');
$down_days_default = get_option('down_days_default');
$erphp_custom_css = get_option('erphp_custom_css');
$erphp_blank_domains = get_option('erphp_blank_domains');
$erphp_colon_domains = get_option('erphp_colon_domains');
$erphp_order_title = get_option('erphp_order_title');
$erphp_downurl_old = get_option('erphp_downurl_old');
$erphp_downurl_new = get_option('erphp_downurl_new');
$erphp_metabox_mini = get_option('erphp_metabox_mini');
$erphp_see2_style = get_option('erphp_see2_style');
$erphp_hide_style = get_option('erphp_hide_style');
$erphp_box_style = get_option('erphp_box_style');
$erphp_box_down_title = get_option('erphp_box_down_title');
$erphp_box_see_title = get_option('erphp_box_see_title');
$erphp_box_faka_title = get_option('erphp_box_faka_title');
$erphp_ad_download = str_replace('\"', '"', get_option("erphp_ad_download"));
?>
<style>.form-table th{font-weight: 400}</style>
<div class="wrap">
	<h1>显示设置</h1>
	<form method="post" action="<?php echo admin_url('admin.php?page='.plugin_basename(__FILE__)); ?>">
		<h3>文章类型设置</h3>
		<p>选择你所需要支持erphpdown的文章类型。</p>
		<table class="form-table">
			<tr>
				<th valign="top">文章类型</th>
				<td>
					<?php 
					$args = array('public' => true,);
					$post_types = get_post_types($args);
					foreach ( $post_types  as $post_type ) {
						if($post_type != 'attachment'){
							$postType = get_post_type_object($post_type);
							?>
							<label>
								<input type="checkbox" name="erphp_post_types[]" value="<?php echo $post_type;?>" <?php if($erphp_post_types) {if(in_array($post_type,$erphp_post_types)) echo 'checked';}?>> <?php echo $postType->labels->singular_name;?>&nbsp;&nbsp;&nbsp;&nbsp;
							</label>
							<?php
						}
					}
					?>
				</td>
			</tr>
		</table>

		<br><br>
		<h3>后台设置</h3>
		<table class="form-table">
			<tr>
				<th valign="top">用户列表显示选项</th>
				<td>
					<input type="checkbox" id="erphp_admin_users_filter" name="erphp_admin_users_filter" value="yes" <?php if($erphp_admin_users_filter == 'yes') echo 'checked'; ?> />
					<p class="description">用户列表显示是否是VIP、是否有消费、余额等，这些可能会导致用户列表加载变慢</p>
				</td>
			</tr>
		</table>

		<br><br>
		<h3>发布默认设置</h3>
		<p>仅对后台新发布有效（只是默认选中，需提交发布才会应用上），其他地方发布、编辑、采集无效。</p>
		<table class="form-table">
			<tr>
				<th valign="top">收费模式</th>
				<td>
					<select name="erphp_down_default">
 						<option value ="4" <?php if($erphp_down_default == '4') echo 'selected="selected"';?>>不启用</option>
 						<option value ="1" <?php if($erphp_down_default == '1') echo 'selected="selected"';?>>下载</option>
 						<option value ="5" <?php if($erphp_down_default == '5') echo 'selected="selected"';?>>免登录</option>
 						<option value ="2" <?php if($erphp_down_default == '2') echo 'selected="selected"';?>>查看</option>
 						<option value ="3" <?php if($erphp_down_default == '3') echo 'selected="selected"';?>>部分查看</option>
 						<option value ="6" <?php if($erphp_down_default == '6') echo 'selected="selected"';?>>发卡</option>
 					</select>
				</td>
			</tr>
			<tr>
				<th valign="top">VIP优惠</th>
				<td>
					<select name="member_down_default">
 						<option value ="1" <?php if($member_down_default == '1') echo 'selected="selected"';?>>无</option>
 						<option value ="3" <?php if($member_down_default == '3') echo 'selected="selected"';?>>免费</option>
 						<option value ="16" <?php if($member_down_default == '16') echo 'selected="selected"';?>>包季免费</option>
 						<option value ="6" <?php if($member_down_default == '6') echo 'selected="selected"';?>>包年免费</option>
 						<option value ="7" <?php if($member_down_default == '7') echo 'selected="selected"';?>>终身免费</option>
 						<option value ="4" <?php if($member_down_default == '4') echo 'selected="selected"';?>>专享</option>
 						<option value ="15" <?php if($member_down_default == '15') echo 'selected="selected"';?>>包季专享</option>
 						<option value ="8" <?php if($member_down_default == '8') echo 'selected="selected"';?>>包年专享</option>
 						<option value ="9" <?php if($member_down_default == '9') echo 'selected="selected"';?>>终身专享</option>
 						<option value ="2" <?php if($member_down_default == '2') echo 'selected="selected"';?>>5折</option>
 						<option value ="5" <?php if($member_down_default == '5') echo 'selected="selected"';?>>8折</option>
 						<option value ="13" <?php if($member_down_default == '13') echo 'selected="selected"';?>>5折|终身免费</option>
 						<option value ="14" <?php if($member_down_default == '14') echo 'selected="selected"';?>>8折|终身免费</option>
 						<option value ="20" <?php if($member_down_default == '20') echo 'selected="selected"';?>>自定义折扣|终身免费</option>
 						<option value ="10" <?php if($member_down_default == '10') echo 'selected="selected"';?>>专购</option>
 						<option value ="17" <?php if($member_down_default == '17') echo 'selected="selected"';?>>包季专购</option>
 						<option value ="18" <?php if($member_down_default == '18') echo 'selected="selected"';?>>包年专购</option>
 						<option value ="19" <?php if($member_down_default == '19') echo 'selected="selected"';?>>终身专购</option>
 						<option value ="11" <?php if($member_down_default == '11') echo 'selected="selected"';?>>专购|包年5折</option>
 						<option value ="12" <?php if($member_down_default == '12') echo 'selected="selected"';?>>专购|包年8折</option>
 					</select>
				</td>
			</tr>
			<tr>
				<th valign="top">价格类型</th>
				<td>
					<select name="down_price_type_default">
 						<option value ="0" <?php if($down_price_type_default == '0') echo 'selected="selected"';?>>单价格</option>
 						<option value ="1" <?php if($down_price_type_default == '1') echo 'selected="selected"';?>>多价格</option>
 					</select>
				</td>
			</tr>
			<tr>
				<th valign="top">收费单价格</th>
				<td>
					<input type="number" step="0.01" id="down_price_default" name="down_price_default" value="<?php echo $down_price_default;?>" class="regular-text" />
				</td>
			</tr>
			<tr>
				<th valign="top">收费多价格</th>
				<td>
					<input type="number" step="0.01" id="down_prices_default" name="down_prices_default" value="<?php echo $down_prices_default;?>" class="regular-text" />
				</td>
			</tr>
			<tr>
				<th valign="top">过期天数</th>
				<td>
					<input type="number" step="0.01" id="down_days_default" name="down_days_default" value="<?php echo $down_days_default;?>" class="regular-text" />
				</td>
			</tr>
			<tr>
				<th valign="top">精简模式</th>
				<td>
					<input type="checkbox" id="erphp_metabox_mini" name="erphp_metabox_mini" value="yes" <?php if($erphp_metabox_mini == 'yes') echo 'checked'; ?> />（后台发布时Erphpdown属性隐藏部分属性，只显示常用的几个）
				</td>
			</tr>
			
		</table>
		<br><br>
		<h3>前端设置</h3>
		<p>假如你主题集成了前端用户中心，并且包含了erphpdown插件功能，可以把相应链接填在此处！</p>
		<table class="form-table">
			<tr>
				<th valign="top">Favicon图标地址</th>
				<td>
					<input type="text" id="erphp_url_front_favicon" name="erphp_url_front_favicon" value="<?php echo $erphp_url_front_favicon;?>" class="regular-text" placeholder="http://"/>（下载页面显示的Favicon图标）
				</td>
			</tr>
			<tr>
				<th valign="top">禁止进后台</th>
				<td>
					<input type="checkbox" id="erphp_url_front_noadmin" name="erphp_url_front_noadmin" value="yes" <?php if($erphp_url_front_noadmin == 'yes') echo 'checked'; ?> />（普通用户无法进后台，若开启此项，下面的升级VIP与充值地址均得设置为非后台的地址，部分主题可能不兼容）
				</td>
			</tr>
			<tr>
				<th valign="top">前端用户中心隐藏VIP</th>
				<td>
					<input type="checkbox" id="erphp_url_front_novip" name="erphp_url_front_novip" value="yes" <?php if($erphp_url_front_novip == 'yes') echo 'checked'; ?> />
					<p class="description">仅对插件自带赠送的前端个人中心页面有效</p>
				</td>
			</tr>
			<tr>
				<th valign="top">前端用户中心隐藏提现</th>
				<td>
					<input type="checkbox" id="erphp_url_front_notx" name="erphp_url_front_notx" value="yes" <?php if($erphp_url_front_notx == 'yes') echo 'checked'; ?> />
					<p class="description">仅对插件自带赠送的前端个人中心页面有效</p>
				</td>
			</tr>
			<tr>
				<th valign="top">前端用户中心隐藏购买清单</th>
				<td>
					<input type="checkbox" id="erphp_url_front_nogm" name="erphp_url_front_nogm" value="yes" <?php if($erphp_url_front_nogm == 'yes') echo 'checked'; ?> />
					<p class="description">仅对插件自带赠送的前端个人中心页面有效</p>
				</td>
			</tr>
			<tr>
				<th valign="top">前端用户中心隐藏推广</th>
				<td>
					<input type="checkbox" id="erphp_url_front_notg" name="erphp_url_front_notg" value="yes" <?php if($erphp_url_front_notg == 'yes') echo 'checked'; ?> />
					<p class="description">仅对插件自带赠送的前端个人中心页面有效</p>
				</td>
			</tr>
			<tr>
				<th valign="top">前端用户中心地址</th>
				<td>
					<input type="text" id="erphp_url_front_userpage" name="erphp_url_front_userpage" value="<?php echo $erphp_url_front_userpage;?>" class="regular-text" placeholder="http://"/>（你网站前台的个人用户中心页面地址）
				</td>
			</tr>
			<tr>
				<th valign="top">前端升级VIP地址</th>
				<td>
					<input type="text" id="erphp_url_front_vip" name="erphp_url_front_vip" value="<?php echo $erphp_url_front_vip ; ?>" class="regular-text" placeholder="http://" />（你网站前台的个人用户中心页面里的升级VIP界面地址）
				</td>
			</tr>
			<tr>
				<th valign="top">前端充值地址</th>
				<td>
					<input type="text" id="erphp_url_front_recharge" name="erphp_url_front_recharge" value="<?php echo $erphp_url_front_recharge ; ?>" class="regular-text" placeholder="http://"/>（你网站前台的个人用户中心页面里的充值界面地址）
				</td>
			</tr>
			<tr>
				<th valign="top">支付成功跳转地址 *</th>
				<td>
					<input type="text" id="erphp_url_front_success" name="erphp_url_front_success" value="<?php echo $erphp_url_front_success;?>" class="regular-text" placeholder="http://" />（你网站前台的个人用户中心页面里的充值记录界面地址）
				</td>
			</tr>
			<tr>
				<th valign="top">前端登录地址</th>
				<td>
					<input type="text" id="erphp_url_front_login" name="erphp_url_front_login" value="<?php echo $erphp_url_front_login ; ?>" class="regular-text" placeholder="http://"/>（不填则显示默认wp-login.php登录地址；链接的class为erphp-login-must）
				</td>
			</tr>
			<tr>
				<th valign="top">[erphpdown]显示框模式</th>
				<td>
					<input type="checkbox" id="erphp_see2_style" name="erphp_see2_style" value="yes" <?php if($erphp_see2_style == 'yes') echo 'checked'; ?> />仅在正文底部显示一个购买框（[erphpdown]短代码处不显示购买按钮，只提示权限不足。建议一篇文章有多个隐藏内容时勾选~）
				</td>
			</tr>
			<tr>
				<th valign="top">隐藏内容格式化</th>
				<td>
					<input type="checkbox" id="erphp_hide_style" name="erphp_hide_style" value="yes" <?php if($erphp_hide_style == 'yes') echo 'checked'; ?> />文章单独设置的隐藏内容基于中文冒号与中文逗号分割提取码、解压密码等，方便一键复制（注意：提取码与解压密码里不要出现冒号与逗号，否则影响分割显示），格式如下
					<br>提取码：xxxx，解压密码：oooo<br>提取码：xxxx<br>解压密码：oooo<br>上面三种格式，用中文逗号分割多个隐藏内容，用中文冒号分割名称与值
				</td>
			</tr>
			<tr>
				<th valign="top">购买框样式</th>
				<td>
					<input type="radio" id="erphp_box_style_default" name="erphp_box_style" value="0" checked /><label for="erphp_box_style_default"><img src="<?php echo ERPHPDOWN_URL;?>/static/box/default.png" style="height: 70px;width: auto;border-radius: 5px;"></label>
					<input type="radio" id="erphp_box_style_style1" name="erphp_box_style" value="1"<?php if($erphp_box_style == '1') echo ' checked'; ?> /><label for="erphp_box_style_style1"><img src="<?php echo ERPHPDOWN_URL;?>/static/box/style1.png" style="height: 70px;width: auto;border-radius: 5px;"></label>
				</td>
			</tr>
			<tr>
				<th valign="top">购买框下载标题</th>
				<td>
					<input type="text" id="erphp_box_down_title" name="erphp_box_down_title" value="<?php echo $erphp_box_down_title ; ?>" class="regular-text" />（默认为资源下载）
				</td>
			</tr>
			<tr>
				<th valign="top">购买框查看标题</th>
				<td>
					<input type="text" id="erphp_box_see_title" name="erphp_box_see_title" value="<?php echo $erphp_box_see_title ; ?>" class="regular-text" />（默认为内容查看）
				</td>
			</tr>
			<tr>
				<th valign="top">购买框发卡标题</th>
				<td>
					<input type="text" id="erphp_box_faka_title" name="erphp_box_faka_title" value="<?php echo $erphp_box_faka_title ; ?>" class="regular-text" />（默认为自动发卡）
				</td>
			</tr>
		</table>

		<br><br>
		<h3>下载格式设置</h3>
		<p>假如不明白这里的设置或者你目前的下载链接没有任何问题，请留空，或在模板兔的指导下设置！</p>
		<table class="form-table">
			<tr>
				<th valign="top">需要空格分隔地址的域名</th>
				<td>
					<input type="text" id="erphp_blank_domains" name="erphp_blank_domains" value="<?php echo $erphp_blank_domains; ?>" class="regular-text" placeholder=""/>（多个域名用英文逗号隔开，例如：pan.baidu.com,pan.mobantu.com）
					<p class="description">整个下载地址里存在空格，需要通过空格来分割名称、地址、提取码</p>
				</td>
			</tr>
			<tr>
				<th valign="top">需要将中文冒号替换的域名</th>
				<td>
					<input type="text" id="erphp_colon_domains" name="erphp_colon_domains" value="<?php echo $erphp_colon_domains; ?>" class="regular-text" placeholder=""/>（多个域名用英文逗号隔开，例如：pan.baidu.com,pan.mobantu.com）
					<p class="description">整个下载地址里存在中文冒号，需要将中文冒号替换为英文冒号+空格</p>
				</td>
			</tr>
			<tr>
				<th valign="top">下载地址跳转替换</th>
				<td>
					<input type="text" id="erphp_downurl_old" name="erphp_downurl_old" value="<?php echo $erphp_downurl_old; ?>" class="regular-text" placeholder="旧域名，例如lanzous.com"/>替换成<input type="text" id="erphp_downurl_new" name="erphp_downurl_new" value="<?php echo $erphp_downurl_new; ?>" class="regular-text" placeholder="新域名，例如lanzoui.com"/>
					<p class="description">可设置下载链接里部分字符替换，这里的替换不是替换掉数据库里的数据，而是在下载跳转的时候替换，不支持免费下载的地址</p>
				</td>
			</tr>
		</table>

		<br><br>
		<h3>订单设置</h3>
		<table class="form-table">
			<tr>
				<th valign="top">全站固定订单标题</th>
				<td>
					<input type="text" id="erphp_order_title" name="erphp_order_title" value="<?php echo $erphp_order_title; ?>" class="regular-text" placeholder=""/>
					<p class="description">所有支付订单显示固定相同的一个标题，例如：XX网订单</p>
				</td>
			</tr>
		</table>

		<br><br>
		<h3>样式设置</h3>
		<table class="form-table">
			<tr>
				<th valign="top">自定义CSS</th>
				<td>
					<textarea id="erphp_custom_css" name="erphp_custom_css" class="regular-text" style="height: 200px;"><?php echo $erphp_custom_css; ?></textarea>
				</td>
			</tr>
		</table>
		
		<br><br>
		<h3>广告设置</h3>
		<table class="form-table">
			<tr>
				<th valign="top">下载页广告</th>
				<td>
					<textarea id="erphp_ad_download" name="erphp_ad_download" class="regular-text" style="height: 200px;"><?php echo $erphp_ad_download; ?></textarea>
				</td>
			</tr>
		</table>

		<table> 
			<tr>
				<td>
					<p class="submit"><input type="submit" name="Submit" value="保存设置" class="button-primary"/></p>
				</td>
			</tr> 
		</table>

</form>
</div>