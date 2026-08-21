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
	if(isset($_POST['Submit']) && $_POST['Submit']=='确认购买')
	{
		$userType=isset($_POST['userType']) && is_numeric($_POST['userType']) ?intval($_POST['userType']) :0;
		$userType = esc_sql($userType);
		if($userType >5 && $userType < 11)
		{
			$okMoney=erphpGetUserOkMoney();
			$priceArr=array('6'=>'erphp_day_price','7'=>'erphp_month_price','8'=>'erphp_quarter_price','9'=>'erphp_year_price','10'=>'erphp_life_price');
			$priceType=$priceArr[$userType];
			$price=get_option($priceType);
			if(!$price)
			{
				echo '<div class="error settings-error"><p>此类型的会员价格错误，请稍候重试！</p></div>';
			}
			elseif($okMoney < $price)
			{
				echo '<div class="error settings-error"><p>当前可用余额不足完成此次交易！请充值后重试！</p></div>';
			}
			elseif($okMoney >=$price)
			{
				if(erphpSetUserMoneyXiaoFei($price))//扣钱
				{
					if(userPayMemberSetData($userType))
					{
						addVipLog($price, $userType);
						$user_info=wp_get_current_user();
						$EPD = new EPD();
						$EPD->doAff($price, $user_info->ID);
						echo '<div class="updated settings-error"><p>购买成功，您即可享受高级会员服务！</p></div>';
					}
					else
					{
						echo '<div class="error settings-error"><p>系统发生错误，请联系管理员！</p></div>';
					}
				}
				else
				{
					echo '<div class="error settings-error"><p>系统发生错误，请稍后重试！</p></div>';
				}
			}
			else
			{
				echo '<div class="error settings-error"><p>系统发生错误！</p></div>';
			}
	}
	else
	{
		echo '<div class="error settings-error"><p>会员类型错误！</p></div>';
	}
}
/////////////////////////////////////////////////www.mobantu.com   82708210@qq.com
$erphp_life_price    = get_option('erphp_life_price');
$erphp_year_price    = get_option('erphp_year_price');
$erphp_quarter_price = get_option('erphp_quarter_price');
$erphp_month_price  = get_option('erphp_month_price');
$erphp_day_price  = get_option('erphp_day_price');
$erphp_life_name    = get_option('erphp_life_name')?get_option('erphp_life_name'):'终身VIP';
$erphp_year_name    = get_option('erphp_year_name')?get_option('erphp_year_name'):'包年VIP';
$erphp_quarter_name = get_option('erphp_quarter_name')?get_option('erphp_quarter_name'):'包季VIP';
$erphp_month_name  = get_option('erphp_month_name')?get_option('erphp_month_name'):'包月VIP';
$erphp_day_name  = get_option('erphp_day_name')?get_option('erphp_day_name'):'体验VIP';

	$okMoney=erphpGetUserOkMoney();//判断余额
	?>
	<form method="post"
	action="<?php echo admin_url('admin.php?page='.plugin_basename(__FILE__)); ?>"
	style="width: 70%; float: left;">

	<h2>购买VIP服务</h2>
	<table class="form-table">
		<tr>
			<td valign="top" width="30%"><strong>当前类型</strong><br /></td>
			<td><?php 
			$userTypeId=getUsreMemberType();
			if($userTypeId==6)
			{
				echo $erphp_day_name;
			}
			elseif($userTypeId==7)
			{
				echo $erphp_month_name;
			}
			elseif ($userTypeId==8)
			{
				echo $erphp_quarter_name;
			}
			elseif ($userTypeId==9)
			{
				echo $erphp_year_name;
			}
			elseif ($userTypeId==10)
			{
				echo $erphp_life_name;
			}
			else 
			{
				echo '未购买任何会员服务';
			}
			?>,&nbsp;&nbsp;&nbsp;<?php if($userTypeId>5 && $userTypeId<10){?>到期时间：<?php echo $userTypeId>0 ?getUsreMemberTypeEndTime() :''?></td><?php }?>
		</tr>
		
		
		<tr>
			<td valign="top" width="30%"><strong>VIP类型</strong><br />
			</td>
			<td>
				<?php if($erphp_life_price){?><input type="radio" id="userType" name="userType" value="10" checked /><?php echo $erphp_life_name;?> --- <?php echo $erphp_life_price?><?php echo get_option('ice_name_alipay')?><br /><?php }?>
				<?php if($erphp_year_price){?><input type="radio" id="userType" name="userType" value="9" checked/><?php echo $erphp_year_name;?> --- <?php echo $erphp_year_price?><?php echo get_option('ice_name_alipay')?><br /> <?php }?>
				<?php if($erphp_quarter_price){?><input type="radio" id="userType" name="userType" value="8" checked/><?php echo $erphp_quarter_name;?> --- <?php echo $erphp_quarter_price?><?php echo get_option('ice_name_alipay')?><br /><?php }?>
				<?php if($erphp_month_price){?><input type="radio" id="userType" name="userType" value="7" checked/><?php echo $erphp_month_name;?> --- <?php echo $erphp_month_price?><?php echo get_option('ice_name_alipay')?><br /><?php }?>
				<?php if($erphp_day_price){?><input type="radio" id="userType" name="userType" value="6" checked/><?php echo $erphp_day_name;?> --- <?php echo $erphp_day_price?><?php echo get_option('ice_name_alipay')?><?php }?>
			</td>
		</tr>
		<tr>
			<td valign="top" width="30%"><strong>可用余额</strong><br />
			</td>
			<td><?php echo sprintf("%.2f",$okMoney)?><?php echo get_option('ice_name_alipay')?>
		</td>
	</tr>
	<tr>
		<td colspan="2"><input type="submit" name="Submit" value="确认购买" class="button-primary" />
		</td>
	</tr>
	
	
</table>
</form>
</div>