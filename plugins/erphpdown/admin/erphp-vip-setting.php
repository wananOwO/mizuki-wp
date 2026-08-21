<?php
// +----------------------------------------------------------------------
// | ERPHP [ PHP DEVELOP ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.mobantu.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: mobantu <82708210@qq.com>
// +----------------------------------------------------------------------
if ( !defined('ABSPATH') ) {exit;}
?>
<div class="wrap">
	<?php
	if(isset($_POST['Submit'])){
		
		
		if(isset($_POST['life_price'])) update_option('erphp_life_price', $_POST['life_price']);
		if(isset($_POST['year_price'])) update_option('erphp_year_price', $_POST['year_price']);
		if(isset($_POST['quarter_price'])) update_option('erphp_quarter_price', $_POST['quarter_price']);
		if(isset($_POST['month_price'])) update_option('erphp_month_price', $_POST['month_price']);
		if(isset($_POST['day_price'])) update_option('erphp_day_price', $_POST['day_price']);
		if(isset($_POST['life_price'])) update_option('ciphp_life_price', $_POST['life_price']);
		if(isset($_POST['year_price'])) update_option('ciphp_year_price', $_POST['year_price']);
		if(isset($_POST['quarter_price'])) update_option('ciphp_quarter_price', $_POST['quarter_price']);
		if(isset($_POST['month_price'])) update_option('ciphp_month_price', $_POST['month_price']);
		if(isset($_POST['day_price'])) update_option('ciphp_day_price', $_POST['day_price']);

		if(isset($_POST['vip_update_pay'])){
			update_option('vip_update_pay', $_POST['vip_update_pay']);
		}else{
			delete_option('vip_update_pay');
		}

		if(isset($_POST['life_times_includes_free'])){
			update_option('erphp_life_times_free', $_POST['life_times_includes_free']);
		}else{
			delete_option('erphp_life_times_free');
		}
		if(isset($_POST['year_times_includes_free'])){
			update_option('erphp_year_times_free', $_POST['year_times_includes_free']);
		}else{
			delete_option('erphp_year_times_free');
		}
		if(isset($_POST['quarter_times_includes_free'])){
			update_option('erphp_quarter_times_free', $_POST['quarter_times_includes_free']);
		}else{
			delete_option('erphp_quarter_times_free');
		}
		if(isset($_POST['month_times_includes_free'])){
			update_option('erphp_month_times_free', $_POST['month_times_includes_free']);
		}else{
			delete_option('erphp_month_times_free');
		}
		if(isset($_POST['day_times_includes_free'])){
			update_option('erphp_day_times_free', $_POST['day_times_includes_free']);
		}else{
			delete_option('erphp_day_times_free');
		}

		if(isset($_POST['life_times'])) update_option('erphp_life_times', $_POST['life_times']);
		if(isset($_POST['year_times'])) update_option('erphp_year_times', $_POST['year_times']);
		if(isset($_POST['quarter_times'])) update_option('erphp_quarter_times', $_POST['quarter_times']);
		if(isset($_POST['month_times'])) update_option('erphp_month_times', $_POST['month_times']);
		if(isset($_POST['day_times'])) update_option('erphp_day_times', $_POST['day_times']);

		if(isset($_POST['vip_times_see'])){
			update_option('vip_times_see', $_POST['vip_times_see']);
		}else{
			delete_option('vip_times_see');
		}

		if(isset($_POST['life_times2'])) update_option('erphp_life_times2', $_POST['life_times2']);
		if(isset($_POST['year_times2'])) update_option('erphp_year_times2', $_POST['year_times2']);
		if(isset($_POST['quarter_times2'])) update_option('erphp_quarter_times2', $_POST['quarter_times2']);
		if(isset($_POST['month_times2'])) update_option('erphp_month_times2', $_POST['month_times2']);
		if(isset($_POST['day_times2'])) update_option('erphp_day_times2', $_POST['day_times2']);

		if(isset($_POST['reg_times'])) update_option('erphp_reg_times', $_POST['reg_times']);
		if(isset($_POST['reg_times_from'])) update_option('erphp_reg_times_from', $_POST['reg_times_from']);
		if(isset($_POST['reg_times_to'])) update_option('erphp_reg_times_to', $_POST['reg_times_to']);
		if(isset($_POST['life_days'])) update_option('erphp_life_days', $_POST['life_days']);
		if(isset($_POST['year_days'])) update_option('erphp_year_days', $_POST['year_days']);
		if(isset($_POST['quarter_days'])) update_option('erphp_quarter_days', $_POST['quarter_days']);
		if(isset($_POST['month_days'])) update_option('erphp_month_days', $_POST['month_days']);
		if(isset($_POST['day_days'])) update_option('erphp_day_days', $_POST['day_days']);
		if(isset($_POST['life_name'])) update_option('erphp_life_name', $_POST['life_name']);
		if(isset($_POST['year_name'])) update_option('erphp_year_name', $_POST['year_name']);
		if(isset($_POST['quarter_name'])) update_option('erphp_quarter_name', $_POST['quarter_name']);
		if(isset($_POST['month_name'])) update_option('erphp_month_name', $_POST['month_name']);
		if(isset($_POST['day_name'])) update_option('erphp_day_name', $_POST['day_name']);
		if(isset($_POST['vip_name'])) update_option('erphp_vip_name', $_POST['vip_name']);
		if(isset($_POST['life_gift'])) update_option('erphp_life_gift', $_POST['life_gift']);
		if(isset($_POST['year_gift'])) update_option('erphp_year_gift', $_POST['year_gift']);
		if(isset($_POST['quarter_gift'])) update_option('erphp_quarter_gift', $_POST['quarter_gift']);
		if(isset($_POST['month_gift'])) update_option('erphp_month_gift', $_POST['month_gift']);
		if(isset($_POST['day_gift'])) update_option('erphp_day_gift', $_POST['day_gift']);
		if(isset($_POST['life_ref'])) update_option('erphp_life_ref', $_POST['life_ref']);
		if(isset($_POST['year_ref'])) update_option('erphp_year_ref', $_POST['year_ref']);
		if(isset($_POST['quarter_ref'])) update_option('erphp_quarter_ref', $_POST['quarter_ref']);
		if(isset($_POST['month_ref'])) update_option('erphp_month_ref', $_POST['month_ref']);
		if(isset($_POST['day_ref'])) update_option('erphp_day_ref', $_POST['day_ref']);
		if(isset($_POST['life_discount'])) update_option('erphp_life_discount', $_POST['life_discount']);
		if(isset($_POST['year_discount'])) update_option('erphp_year_discount', $_POST['year_discount']);
		if(isset($_POST['quarter_discount'])) update_option('erphp_quarter_discount', $_POST['quarter_discount']);
		if(isset($_POST['month_discount'])) update_option('erphp_month_discount', $_POST['month_discount']);
		if(isset($_POST['day_discount'])) update_option('erphp_day_discount', $_POST['day_discount']);
		echo'<div class="updated settings-error"><p>更新成功！</p></div>';

	}

	$erphp_life_price    = get_option('erphp_life_price');
	$erphp_year_price    = get_option('erphp_year_price');
	$erphp_quarter_price = get_option('erphp_quarter_price');
	$erphp_month_price  = get_option('erphp_month_price');
	$erphp_day_price  = get_option('erphp_day_price');

	$vip_update_pay = get_option('vip_update_pay');
	
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

	$vip_times_see = get_option('vip_times_see');

	$erphp_life_times2    = get_option('erphp_life_times2');
	$erphp_year_times2    = get_option('erphp_year_times2');
	$erphp_quarter_times2 = get_option('erphp_quarter_times2');
	$erphp_month_times2  = get_option('erphp_month_times2');
	$erphp_day_times2  = get_option('erphp_day_times2');

	$erphp_reg_times  = get_option('erphp_reg_times');
	$erphp_reg_times_from  = get_option('erphp_reg_times_from');
	$erphp_reg_times_to  = get_option('erphp_reg_times_to');
	$erphp_life_days    = get_option('erphp_life_days');
	$erphp_year_days    = get_option('erphp_year_days');
	$erphp_quarter_days = get_option('erphp_quarter_days');
	$erphp_month_days  = get_option('erphp_month_days');
	$erphp_day_days  = get_option('erphp_day_days');
	$erphp_life_name    = get_option('erphp_life_name');
	$erphp_year_name    = get_option('erphp_year_name');
	$erphp_quarter_name = get_option('erphp_quarter_name');
	$erphp_month_name  = get_option('erphp_month_name');
	$erphp_day_name  = get_option('erphp_day_name');
	$erphp_vip_name  = get_option('erphp_vip_name');
	$erphp_life_gift    = get_option('erphp_life_gift');
	$erphp_year_gift    = get_option('erphp_year_gift');
	$erphp_quarter_gift = get_option('erphp_quarter_gift');
	$erphp_month_gift  = get_option('erphp_month_gift');
	$erphp_day_gift  = get_option('erphp_day_gift');
	$erphp_life_discount    = get_option('erphp_life_discount');
	$erphp_year_discount    = get_option('erphp_year_discount');
	$erphp_quarter_discount = get_option('erphp_quarter_discount');
	$erphp_month_discount  = get_option('erphp_month_discount');
	$erphp_day_discount  = get_option('erphp_day_discount');
	$erphp_life_ref    = get_option('erphp_life_ref');
	$erphp_year_ref    = get_option('erphp_year_ref');
	$erphp_quarter_ref = get_option('erphp_quarter_ref');
	$erphp_month_ref  = get_option('erphp_month_ref');
	$erphp_day_ref  = get_option('erphp_day_ref');
?>

		<form method="post" action="<?php echo admin_url('admin.php?page='.plugin_basename(__FILE__)); ?>">

			<h2>VIP价格设置</h2>
			<p>如不需要某个VIP类型，可留空价格</p>
			<table class="form-table">
				<tr>
					<th valign="top" width="30%"><strong>补差价升级</strong></th>
					<td><input type="checkbox" id="vip_update_pay" name="vip_update_pay" value="yes" <?php if($vip_update_pay == 'yes') echo 'checked'; ?> />启用（规则：例如用户2022.3.1升级了包月VIP，到了2022.3.15时想升级为包年VIP，那么升级价格就是包年价减去包月价，升级包年后到期时间为2023.3.1）
					</td>
				</tr>
				<tr>
					<th valign="top" width="30%"><strong>终身VIP</strong></th>
					<td><input type="number" step="0.01" id="life_price" name="life_price"
						value="<?php echo $erphp_life_price ; ?>" class="regular-text" /><?php echo get_option('ice_name_alipay');?>
					</td>
				</tr>
				<tr>
					<th valign="top" width="30%"><strong>包年VIP</strong></th>
					<td><input type="number" step="0.01" id="year_price" name="year_price"
						value="<?php echo $erphp_year_price ; ?>" class="regular-text" /><?php echo get_option('ice_name_alipay');?>
					</td>
				</tr>
				<tr>
					<th valign="top" width="30%"><strong>包季VIP</strong></th>
					<td><input type="number" step="0.01" id="quarter_price" name="quarter_price"
						value="<?php echo $erphp_quarter_price; ?>" class="regular-text" /><?php echo get_option('ice_name_alipay');?>
					</td>
				</tr>
				<tr>
					<th valign="top" width="30%"><strong>包月VIP</strong></th>
					<td><input type="number" step="0.01" id="month_price" name="month_price"
						value="<?php echo $erphp_month_price; ?>" class="regular-text" /><?php echo get_option('ice_name_alipay');?>
					</td>
				</tr>
				<tr>
					<th valign="top" width="30%"><strong>体验VIP</strong></th>
					<td><input type="number" step="0.01" id="day_price" name="day_price"
						value="<?php echo $erphp_day_price; ?>" class="regular-text" /><?php echo get_option('ice_name_alipay');?>
					</td>
				</tr>
			</table>

			<h2>VIP天数设置</h2>
			<p><span style="color:red">默认不需要填写</span></p>
			<table class="form-table">
				<tr>
					<th valign="top" width="30%"><strong>终身VIP</strong></th>
					<td><input type="number" step="0.01" id="life_price" name="life_days"
						value="<?php echo $erphp_life_days; ?>" class="regular-text" /><font color="blue">年</font>
						<p>如果就是终身而不是多少年，不要填写，否则可能会出问题（不用填999这类大数字哦，留空即表示终身）</p>
					</td>
				</tr>
				<tr>
					<th valign="top" width="30%"><strong>包年VIP</strong></th>
					<td><input type="number" step="0.01" id="year_price" name="year_days"
						value="<?php echo $erphp_year_days; ?>" class="regular-text" /><font color="red">月</font>
					</td>
				</tr>
				<tr>
					<th valign="top" width="30%"><strong>包季VIP</strong></th>
					<td><input type="number" step="0.01" id="quarter_price" name="quarter_days"
						value="<?php echo $erphp_quarter_days; ?>" class="regular-text" /><font color="red">月</font>
					</td>
				</tr>
				<tr>
					<th valign="top" width="30%"><strong>包月VIP</strong></th>
					<td><input type="number" step="0.01" id="month_price" name="month_days"
						value="<?php echo $erphp_month_days; ?>" class="regular-text" />天
					</td>
				</tr>
				<tr>
					<th valign="top" width="30%"><strong>体验VIP</strong></th>
					<td><input type="number" step="0.01" id="day_price" name="day_days"
						value="<?php echo $erphp_day_days; ?>" class="regular-text" />天
						<p>默认留空则当天有效，如果填1就是到明天也有效</p>
					</td>
				</tr>
			</table>

			<h2>VIP名称设置</h2>
			<p>留空则显示默认名称</p>
			<table class="form-table">
				<tr>
					<th valign="top" width="30%"><strong>终身VIP</strong></th>
					<td><input type="text" id="life_name" name="life_name" value="<?php echo $erphp_life_name ; ?>" class="regular-text" />
					</td>
				</tr>
				<tr>
					<th valign="top" width="30%"><strong>包年VIP</strong></th>
					<td><input type="text" id="year_name" name="year_name" value="<?php echo $erphp_year_name ; ?>" class="regular-text" />
					</td>
				</tr>
				<tr>
					<th valign="top" width="30%"><strong>包季VIP</strong></th>
					<td><input type="text" id="quarter_name" name="quarter_name" value="<?php echo $erphp_quarter_name; ?>" class="regular-text" />
					</td>
				</tr>
				<tr>
					<th valign="top" width="30%"><strong>包月VIP</strong></th>
					<td><input type="text" id="month_name" name="month_name" value="<?php echo $erphp_month_name; ?>" class="regular-text" />
					</td>
				</tr>
				<tr>
					<th valign="top" width="30%"><strong>体验VIP</strong></th>
					<td><input type="text" id="day_name" name="day_name" value="<?php echo $erphp_day_name; ?>" class="regular-text" />
					</td>
				</tr>
				<tr>
					<th valign="top" width="30%"><strong>VIP</strong></th>
					<td><input type="text" id="vip_name" name="vip_name" value="<?php echo $erphp_vip_name; ?>" class="regular-text" />
						<p>建议留空（默认为VIP），否则可能显示会有差异！VIP的统称，用于购买时针对体验、包月权限的显示</p>
					</td>
				</tr>
			</table>

			<h2>VIP用户每天下载/查看VIP资源个数限制</h2>
			<p>留空则不限制，这里的下载/查看个数指VIP资源合计每天下载/查看的资源个数，仅对VIP资源有效，单独购买的资源无效</p>
			<table class="form-table">
				<tr>
					<th valign="top" width="30%"><strong>终身VIP</strong></th>
					<td><input type="number" id="life_times" name="life_times"
						value="<?php echo $erphp_life_times ; ?>" class="regular-text" min="0" step="1"/>个&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" id="life_times_includes_free" name="life_times_includes_free" value="yes" <?php if($life_times_includes_free == 'yes') echo 'checked'; ?> />含普通免费资源（下载普通免费资源也会算进每天的次数里）
					</td>
				</tr>
				<tr>
					<th valign="top" width="30%"><strong>包年VIP</strong></th>
					<td><input type="number" id="year_times" name="year_times"
						value="<?php echo $erphp_year_times ; ?>" class="regular-text" min="0" step="1"/>个&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" id="year_times_includes_free" name="year_times_includes_free" value="yes" <?php if($year_times_includes_free == 'yes') echo 'checked'; ?> />含普通免费资源（下载普通免费资源也会算进每天的次数里）
					</td>
				</tr>
				<tr>
					<th valign="top" width="30%"><strong>包季VIP</strong></th>
					<td><input type="number" id="quarter_times" name="quarter_times"
						value="<?php echo $erphp_quarter_times; ?>" class="regular-text" min="0" step="1"/>个&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" id="quarter_times_includes_free" name="quarter_times_includes_free" value="yes" <?php if($quarter_times_includes_free == 'yes') echo 'checked'; ?> />含普通免费资源（下载普通免费资源也会算进每天的次数里）
					</td>
				</tr>
				<tr>
					<th valign="top" width="30%"><strong>包月VIP</strong></th>
					<td><input type="number" id="month_times" name="month_times"
						value="<?php echo $erphp_month_times; ?>" class="regular-text" min="0" step="1"/>个&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" id="month_times_includes_free" name="month_times_includes_free" value="yes" <?php if($month_times_includes_free == 'yes') echo 'checked'; ?> />含普通免费资源（下载普通免费资源也会算进每天的次数里）
					</td>
				</tr>
				<tr>
					<th valign="top" width="30%"><strong>体验VIP</strong></th>
					<td><input type="number" id="day_times" name="day_times"
						value="<?php echo $erphp_day_times; ?>" class="regular-text" min="0" step="1"/>个&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" id="day_times_includes_free" name="day_times_includes_free" value="yes" <?php if($day_times_includes_free == 'yes') echo 'checked'; ?> />含普通免费资源（下载普通免费资源也会算进每天的次数里）
					</td>
				</tr>
				<tr>
					<th valign="top" width="30%"><strong>单独限制查看</strong></th>
					<td><input type="checkbox" id="vip_times_see" name="vip_times_see" value="yes" <?php if($vip_times_see == 'yes') echo 'checked'; ?> />启用（查看与下载分开限制；<span style="color:red">勾选</span>就是上面的个数仅是下载限制，下面的是查看限制；<span style="color:red">不勾选</span>就是上面的个数是下载与查看共用的限制）<br><span style="color:red">插件从低版本升级到v17.0后，务必停用插件后再重新启用，然后测试这个限制是否生效</span>
					</td>
				</tr>
				<tr>
					<th valign="top" width="30%"><strong>终身VIP</strong></th>
					<td><input type="number" id="life_times2" name="life_times2"
						value="<?php echo $erphp_life_times2 ; ?>" class="regular-text" min="0" step="1"/>查看
					</td>
				</tr>
				<tr>
					<th valign="top" width="30%"><strong>包年VIP</strong></th>
					<td><input type="number" id="year_times2" name="year_times2"
						value="<?php echo $erphp_year_times2 ; ?>" class="regular-text" min="0" step="1"/>查看
					</td>
				</tr>
				<tr>
					<th valign="top" width="30%"><strong>包季VIP</strong></th>
					<td><input type="number" id="quarter_times2" name="quarter_times2"
						value="<?php echo $erphp_quarter_times2; ?>" class="regular-text" min="0" step="1"/>查看
					</td>
				</tr>
				<tr>
					<th valign="top" width="30%"><strong>包月VIP</strong></th>
					<td><input type="number" id="month_times2" name="month_times2"
						value="<?php echo $erphp_month_times2; ?>" class="regular-text" min="0" step="1"/>查看
					</td>
				</tr>
				<tr>
					<th valign="top" width="30%"><strong>体验VIP</strong></th>
					<td><input type="number" id="day_times2" name="day_times2"
						value="<?php echo $erphp_day_times2; ?>" class="regular-text" min="0" step="1"/>查看
					</td>
				</tr>
			</table>

			<h2>普通用户每天下载普通免费资源个数限制</h2>
			<p>留空则不限制，这里的下载个数指普通免费资源合计每天下载的资源个数，仅对非VIP用户下载普通免费资源有效，单独购买的资源无效<br /><span style="color:red">VIP用户对免费资源下载不限制</span></p>
			<table class="form-table">
				<tr>
					<th valign="top" width="30%"><strong>注册用户</strong></th>
					<td><input type="number" id="reg_times" name="reg_times"
						value="<?php echo $erphp_reg_times; ?>" class="regular-text" min="0" step="1"/>个
					</td>
				</tr>
				<tr>
					<th valign="top" width="30%"><strong>时间限制（24小时制）</strong></th>
					<td>每天<input type="number" id="reg_times_from" name="reg_times_from"
						value="<?php echo $erphp_reg_times_from; ?>" class="regular-text" min="0" max="24" step="1" style="width:150px" />点 — <input type="number" id="reg_times_to" name="reg_times_to"
						value="<?php echo $erphp_reg_times_to; ?>" class="regular-text" min="0" max="24" step="1" style="width:150px" />点 不可免费下载，提高VIP转化
					</td>
				</tr>
			</table>

			<h2 id="erphp-discounts">自定义折扣设置/VIP个数限制后折扣设置</h2>
			<p>固定的自定义VIP折扣/超过VIP每天下载个数限制后单独购买资源时可以打几折<br><span style="color:red">留空则不打折</span></p>
			<table class="form-table">
				<tr>
					<th valign="top" width="30%"><strong>终身VIP</strong></th>
					<td><input type="number" id="life_discount" name="life_discount" value="<?php echo $erphp_life_discount ; ?>" class="regular-text" />折
					</td>
				</tr>
				<tr>
					<th valign="top" width="30%"><strong>包年VIP</strong></th>
					<td><input type="number" id="year_discount" name="year_discount" value="<?php echo $erphp_year_discount ; ?>" class="regular-text" />折
					</td>
				</tr>
				<tr>
					<th valign="top" width="30%"><strong>包季VIP</strong></th>
					<td><input type="number" id="quarter_discount" name="quarter_discount" value="<?php echo $erphp_quarter_discount; ?>" class="regular-text" />折
					</td>
				</tr>
				<tr>
					<th valign="top" width="30%"><strong>包月VIP</strong></th>
					<td><input type="number" id="month_discount" name="month_discount" value="<?php echo $erphp_month_discount; ?>" class="regular-text" />折
					</td>
				</tr>
				<tr>
					<th valign="top" width="30%"><strong>体验VIP</strong></th>
					<td><input type="number" id="day_discount" name="day_discount" value="<?php echo $erphp_day_discount; ?>" class="regular-text" />折
					</td>
				</tr>
			</table>

			<h2>VIP奖励设置</h2>
			<p>升级VIP奖励<?php echo get_option('ice_name_alipay');?></p>
			<table class="form-table">
				<tr>
					<th valign="top" width="30%"><strong>终身VIP</strong></th>
					<td><input type="text" id="life_gift" name="life_gift" value="<?php echo $erphp_life_gift ; ?>" class="regular-text" /><?php echo get_option('ice_name_alipay');?>
					</td>
				</tr>
				<tr>
					<th valign="top" width="30%"><strong>包年VIP</strong></th>
					<td><input type="text" id="year_gift" name="year_gift" value="<?php echo $erphp_year_gift ; ?>" class="regular-text" /><?php echo get_option('ice_name_alipay');?>
					</td>
				</tr>
				<tr>
					<th valign="top" width="30%"><strong>包季VIP</strong></th>
					<td><input type="text" id="quarter_gift" name="quarter_gift" value="<?php echo $erphp_quarter_gift; ?>" class="regular-text" /><?php echo get_option('ice_name_alipay');?>
					</td>
				</tr>
				<tr>
					<th valign="top" width="30%"><strong>包月VIP</strong></th>
					<td><input type="text" id="month_gift" name="month_gift" value="<?php echo $erphp_month_gift; ?>" class="regular-text" /><?php echo get_option('ice_name_alipay');?>
					</td>
				</tr>
				<tr>
					<th valign="top" width="30%"><strong>体验VIP</strong></th>
					<td><input type="text" id="day_gift" name="day_gift" value="<?php echo $erphp_day_gift; ?>" class="regular-text" /><?php echo get_option('ice_name_alipay');?>
					</td>
				</tr>
			</table>

			<h2>VIP推广消费提成设置</h2>
			<p>单独给VIP用户设置他们的一级推广提成比例（百分点），让他们与普通用户的一级推广提成比例不一样</p>
			<table class="form-table">
				<tr>
					<th valign="top" width="30%"><strong>终身VIP</strong></th>
					<td><input type="number" step="0.01" id="life_ref" name="life_ref" value="<?php echo $erphp_life_ref ; ?>" class="regular-text" />%
					</td>
				</tr>
				<tr>
					<th valign="top" width="30%"><strong>包年VIP</strong></th>
					<td><input type="number" step="0.01" id="year_ref" name="year_ref" value="<?php echo $erphp_year_ref ; ?>" class="regular-text" />%
					</td>
				</tr>
				<tr>
					<th valign="top" width="30%"><strong>包季VIP</strong></th>
					<td><input type="number" step="0.01" id="quarter_ref" name="quarter_ref" value="<?php echo $erphp_quarter_ref; ?>" class="regular-text" />%
					</td>
				</tr>
				<tr>
					<th valign="top" width="30%"><strong>包月VIP</strong></th>
					<td><input type="number" step="0.01" id="month_ref" name="month_ref" value="<?php echo $erphp_month_ref; ?>" class="regular-text" />%
					</td>
				</tr>
				<tr>
					<th valign="top" width="30%"><strong>体验VIP</strong></th>
					<td><input type="number" step="0.01" id="day_ref" name="day_ref" value="<?php echo $erphp_day_ref; ?>" class="regular-text" />%
					</td>
				</tr>
			</table>

			<table class="form-table">
				<tr>
					<td colspan="2">
						<p class="submit">
							<input type="submit" name="Submit" value="保存设置" class="button-primary" />
						</p>
					</td>
				</tr>
			</table>

		</form>
	</div>