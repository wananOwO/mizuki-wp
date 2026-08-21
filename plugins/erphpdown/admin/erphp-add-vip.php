<?php 
// +----------------------------------------------------------------------
// | ERPHP [ PHP DEVELOP ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.mobantu.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: mobantu <82708210@qq.com>
// +----------------------------------------------------------------------
if ( !defined('ABSPATH') ) {exit;}
date_default_timezone_set("PRC");
 ?>
<div class="wrap">
	<?php
	
	if(isset($_POST['action']) && $_POST['action']=='1'){
		$user_info=get_user_by('login', esc_sql($_POST['vipusername']));
		$uid=$user_info->ID;
		$userType=isset($_POST['userType']) && is_numeric($_POST['userType']) ?intval($_POST['userType']) :0;
		if($userType >5 && $userType < 11 && $uid)
		{

			addUserMoney($uid,'0');
			if(userSetMemberSetData($userType,$uid))
			{
				addVipLogByAdmin(0, $userType, $uid);
				echo '<div class="updated settings-error"><p>赠送VIP成功！</p></div>';
			}
			else
			{
				echo '<div class="error settings-error"><p>赠送VIP失败！</p></div>';
			}
			
		}
		else
		{
			echo '<div class="error settings-error"><p>用户名不存在或会员类型错误！</p></div>';
		}
	}elseif(isset($_POST['action']) && $_POST['action']=='2'){
		$user_info=get_user_by('login', esc_sql($_POST['vipusername']));
		$uid=$user_info->ID;
		if($uid){
			$catvip = $_POST['catvip'];
			$catvip_arr = explode('-',$catvip);
			userSetCatSetData($catvip_arr[1],$catvip_arr[0],$uid);
			addVipCatLogByAdmin(0, $catvip_arr[1], $catvip_arr[0], $uid);
			echo '<div class="updated settings-error"><p>赠送分类VIP成功！</p></div>';
		}else{
			echo '<div class="error settings-error"><p>用户名不存在！</p></div>';
		}
	}

	$erphp_life_name    = '终身VIP'.(get_option('erphp_life_name')?'('.get_option('erphp_life_name').')':'');
	$erphp_year_name    = '包年VIP'.(get_option('erphp_year_name')?'('.get_option('erphp_year_name').')':'');
	$erphp_quarter_name = '包季VIP'.(get_option('erphp_quarter_name')?'('.get_option('erphp_quarter_name').')':'');
	$erphp_month_name  = '包月VIP'.(get_option('erphp_month_name')?'('.get_option('erphp_month_name').')':'');
	$erphp_day_name  = '体验VIP'.(get_option('erphp_day_name')?'('.get_option('erphp_day_name').')':'');

?>
<h1>后台赠送VIP</h1>
<form method="post" action="<?php echo admin_url('admin.php?page='.plugin_basename(__FILE__)); ?>">
	<h2>全站VIP</h2>
	<table class="form-table">
		
		<tr>
			<th valign="top">VIP类型</th>
			<td>
				<input type="radio" id="userType" name="userType" value="10" checked /><?php echo $erphp_life_name;?><br />
				<input type="radio" id="userType" name="userType" value="9" checked/><?php echo $erphp_year_name;?><br /> 
				<input type="radio" id="userType" name="userType" value="8" checked/><?php echo $erphp_quarter_name;?><br />
				<input type="radio" id="userType" name="userType" value="7" checked/><?php echo $erphp_month_name;?><br />
				<input type="radio" id="userType" name="userType" value="6" checked/><?php echo $erphp_day_name;?>
			</td>
		</tr>
		<tr>
			<th valign="top">被赠送用户登录名</th>
			<td><input type="text" name="vipusername" class="regular-text"></td>
		</tr>
		<tr>
			<td colspan="2"><input type="submit" name="Submit" value="确认赠送" onclick="return confirm('确认赠送?')" class="button-primary" />
				<input type="hidden" name="action" value="1">
			</td>
		</tr>
	</table>
</form>

<?php if(function_exists('MBT_erphp_vip_cat')){
	$cat_vips = get_terms('category', array(
	    'hide_empty' => false,
	    'meta_query' => array(
		    array(
		       'key'       => 'cat_vip',
		       'value'     => '1',
		       'compare'   => '='
		    )
		)
	) );
	if ( ! empty( $cat_vips ) && ! is_wp_error( $cat_vips ) ){
?>

	<h2>分类VIP</h2>
	<table class="form-table">
		<tr>
			<th valign="top">分类VIP</th>
			<td>
				<div style="overflow: hidden;">
				<?php
					foreach ( $cat_vips as $cat ) {
						$cat_vip_name = get_term_meta($cat->term_id,'cat_vip_name',true);
				?>
					<div style="float: left;margin-right:30px">
						<form method="post" action="<?php echo admin_url('admin.php?page='.plugin_basename(__FILE__)); ?>">
		                    <p class="border-decor border-decor-cat"><a href="<?php echo get_term_link($cat);?>" target="_blank"><?php echo $cat->name;?></a> (<?php echo $cat_vip_name?$cat_vip_name:$cat->name;?>)</p>
		                    <ul>
		                    	<?php 
		                    		echo '<li><input type="radio" name="catvip" id="catvip'.$cat->term_id.'-life" value="'.$cat->term_id.'-10" > <label for="catvip'.$cat->term_id.'-life">'.$erphp_life_name.'</label></li>';
		                    		echo '<li><input type="radio" name="catvip" id="catvip'.$cat->term_id.'-year" value="'.$cat->term_id.'-9" > <label for="catvip'.$cat->term_id.'-year">'.$erphp_year_name.'</label></li>';
		                    		echo '<li><input type="radio" name="catvip" id="catvip'.$cat->term_id.'-quarter" value="'.$cat->term_id.'-8" > <label for="catvip'.$cat->term_id.'-quarter">'.$erphp_quarter_name.'</label></li>';
		                    		echo '<li><input type="radio" name="catvip" id="catvip'.$cat->term_id.'-month" value="'.$cat->term_id.'-7" checked> <label for="catvip'.$cat->term_id.'-month">'.$erphp_month_name.'</label></li>';
	
		                    	?>
		                    </ul>
		                    <input type="text" name="vipusername" class="regular-text" placeholder="用户名"><br><br>
		                    <input type="submit" value="确认赠送" onclick="return confirm('确认赠送?')" class="button-primary" /><input type="hidden" name="action" value="2">
		                </form>
	                </div>
				<?php
					}
				?>
				</div>
			</td>
		</tr>
	</table>
<?php }
}?>
</div>