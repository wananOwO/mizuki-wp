<?php
// +----------------------------------------------------------------------
// | ERPHP [ PHP DEVELOP ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.mobantu.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: mobantu <82708210@qq.com>
// +----------------------------------------------------------------------

if ( !defined('ABSPATH') ) {exit;}
date_default_timezone_set('Asia/Shanghai');
$erphp_aff_money = get_option('erphp_aff_money');

if(isset($_POST['paytype']) && $_POST['paytype']){
	$paytype=esc_sql(intval($_POST['paytype']));
	$doo = 1;

	if(isset($_POST['paytype']) && $paytype==1)
	{
		$url=constant("erphpdown")."payment/alipay.php?ice_money=".esc_sql($_POST['ice_money']);
	}
	elseif(isset($_POST['paytype']) && $paytype==5)
	{
		$url=constant("erphpdown")."payment/f2fpay.php?ice_money=".esc_sql($_POST['ice_money']);
	}
	elseif(isset($_POST['paytype']) && $paytype==2)
	{
		$url=constant("erphpdown")."payment/paypal.php?ice_money=".esc_sql($_POST['ice_money']);
	}
	elseif(isset($_POST['paytype']) && $paytype==4)
	{
		if(erphpdown_is_weixin() && get_option('ice_weixin_app')){
			$url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.get_option('ice_weixin_appid').'&redirect_uri='.urlencode(constant("erphpdown")).'payment%2Fweixin.php%3Fice_money%3D'.esc_sql($_POST['ice_money']).'&response_type=code&scope=snsapi_base&state=STATE&connect_redirect=1#wechat_redirect';
		}else{
			$url=constant("erphpdown")."payment/weixin.php?ice_money=".esc_sql($_POST['ice_money']);
		}
	}
	elseif(isset($_POST['paytype']) && $paytype==7)
	{
		$url=constant("erphpdown")."payment/paypy.php?ice_money=".esc_sql($_POST['ice_money']);
	}
	elseif(isset($_POST['paytype']) && $paytype==8)
	{
		$url=constant("erphpdown")."payment/paypy.php?ice_money=".esc_sql($_POST['ice_money'])."&type=alipay";
	}
	elseif(isset($_POST['paytype']) && $paytype==18)
	{
		$url=constant("erphpdown")."payment/xhpay3.php?ice_money=".esc_sql($_POST['ice_money'])."&type=2";
	}
	elseif(isset($_POST['paytype']) && $paytype==17)
	{
		$url=constant("erphpdown")."payment/xhpay3.php?ice_money=".esc_sql($_POST['ice_money'])."&type=1";
	}elseif(isset($_POST['paytype']) && $paytype==19)
	{
		$url=constant("erphpdown")."payment/payjs.php?ice_money=".esc_sql($_POST['ice_money']);
	}elseif(isset($_POST['paytype']) && $paytype==20)
	{
		$url=constant("erphpdown")."payment/payjs.php?ice_money=".esc_sql($_POST['ice_money'])."&type=alipay";
	}
	elseif(isset($_POST['paytype']) && $paytype==13)
	{
		$url=constant("erphpdown")."payment/codepay.php?ice_money=".esc_sql($_POST['ice_money'])."&type=1";
	}elseif(isset($_POST['paytype']) && $paytype==14)
	{
		$url=constant("erphpdown")."payment/codepay.php?ice_money=".esc_sql($_POST['ice_money'])."&type=3";
	}elseif(isset($_POST['paytype']) && $paytype==15)
	{
		$url=constant("erphpdown")."payment/codepay.php?ice_money=".esc_sql($_POST['ice_money'])."&type=2";
	}
	elseif(isset($_POST['paytype']) && $paytype==21)
	{
		$url=constant("erphpdown")."payment/epay.php?ice_money=".esc_sql($_POST['ice_money'])."&type=alipay";
	}elseif(isset($_POST['paytype']) && $paytype==22)
	{
		$url=constant("erphpdown")."payment/epay.php?ice_money=".esc_sql($_POST['ice_money'])."&type=wxpay";
	}elseif(isset($_POST['paytype']) && $paytype==23)
	{
		$url=constant("erphpdown")."payment/epay.php?ice_money=".esc_sql($_POST['ice_money'])."&type=qqpay";
	}elseif(isset($_POST['paytype']) && $paytype==31)
	{
		$url=constant("erphpdown")."payment/vpay.php?ice_money=".esc_sql($_POST['ice_money'])."&type=2";
	}elseif(isset($_POST['paytype']) && $paytype==32)
	{
		$url=constant("erphpdown")."payment/vpay.php?ice_money=".esc_sql($_POST['ice_money']);
	}elseif(isset($_POST['paytype']) && $paytype==41)
	{
		$url=constant("erphpdown")."payment/easepay.php?ice_money=".esc_sql($_POST['ice_money'])."&type=alipay";
	}elseif(isset($_POST['paytype']) && $paytype==42)
	{
		$url=constant("erphpdown")."payment/easepay.php?ice_money=".esc_sql($_POST['ice_money'])."&type=wxpay";
	}
	elseif(isset($_POST['paytype']) && $paytype==50)
	{
		$url=home_url('?epd_r64='.base64_encode('usdt-'.esc_sql($_POST['ice_money']).'-'.time()));
	}
	elseif(isset($_POST['paytype']) && $paytype==60)
	{
		$url=home_url('?epd_r64='.base64_encode('stripe-'.esc_sql($_POST['ice_money']).'-'.time()));
	}
	elseif(plugin_check_ecpay() && isset($_POST['paytype']) && $paytype==70)
	{
		$url=ERPHPDOWN_ECPAY_URL."/ecpay.php?ice_money=".esc_sql($_POST['ice_money']);
	}
	elseif(isset($_POST['paytype']) && $paytype==6)
	{
		$doo = 0;
		$result = checkDoCardResult(esc_sql($_POST['ice_money']),esc_sql($_POST['password']));
		if($result == '0') echo "此充值卡已被使用，请重新换张！";
		if($result == '4') echo "系统出错，出现问题，请联系管理员！";
		if($result == '1') echo "充值成功！";
	}

	if($doo){
		echo "<script>location.href='".$url."'</script>";
	}
	exit;
}

$user_Info   = wp_get_current_user();
$userMoney=$wpdb->get_row("select * from ".$wpdb->iceinfo." where ice_user_id=".$user_Info->ID);
if(!$userMoney){
	$okMoney=0;
}else {
	$okMoney=$userMoney->ice_have_money - $userMoney->ice_get_money;
}
?>
<div class="wrap">
	<script type="text/javascript">
		jQuery(document).ready(function() {
			var c = jQuery("input[name='paytype']:checked").val();
			if(c == 6){jQuery("#cpass").css("display","");jQuery("#cname").html("充值卡号");}
			else{jQuery("#cpass").css("display","none");jQuery("#cname").html("充值金额");}
		});

		function checkFm()
		{
			if(document.getElementById("ice_money").value=="")
			{
				alert('请输入金额或卡号');
				return false;
			}
		}

		function checkCard()
		{
			var c = jQuery("input[name='paytype']:checked").val();
			if(c == 6){jQuery("#cpass").css("display","");jQuery("#cname").html("充值卡号");}
			else{jQuery("#cpass").css("display","none");jQuery("#cname").html("充值金额");}
		}
	</script>
	<h2>我的资产</h2>
	<table class="form-table">
		<?php if($erphp_aff_money){?>
		<tr>
			<td valign="top" width="30%"><strong>收入+推广：</strong><br />
			</td>
			<td>
				<?php if($userMoney) echo sprintf("%.2f",$userMoney->ice_have_aff); else echo '0';?><?php echo get_option('ice_name_alipay')?>
			</td>
		</tr>
		<tr>
			<td valign="top" width="30%"><strong style="color:red">可提现余额：</strong><br />
			</td>
			<td style="color:red">
				<?php if($userMoney) echo sprintf("%.2f",($userMoney->ice_have_aff - $userMoney->ice_get_aff)); else echo '0';?><?php echo get_option('ice_name_alipay')?>
			</td>
		</tr>
		<?php }?>
		<tr>
			<td valign="top" width="30%"><strong>已消费：</strong><br />
			</td>
			<td>
				<?php if($userMoney) echo sprintf("%.2f",$userMoney->ice_get_money); else echo '0';?><?php echo get_option('ice_name_alipay')?>
			</td>
		</tr>
		<tr>
			<td valign="top" width="30%"><strong style="color:red">可消费余额：</strong><br />
			</td>
			<td style="color:red">
				<?php echo sprintf("%.2f",$okMoney)?><?php echo get_option('ice_name_alipay')?>
			</td>
		</tr>
	</table>
	<form action="" method="post" <?php if(!erphpdown_is_weixin()){?>target="_blank"<?php }?>>
		<h2>在线充值</h2>
		<table class="form-table">
			<tr>
				<td valign="top"><strong>充值比例</strong><br />
				</td>
				<td>
					<font color="#006600">1元 = <?php echo get_option('ice_proportion_alipay') ?><?php echo get_option('ice_name_alipay') ?></font>
				</td>
			</tr>
			<tr>
				<td valign="top"><strong><span id="cname">充值金额</span></strong><br />
				</td>
				<td>
					<input type="text" id="ice_money" name="ice_money" maxlength="50" size="50" required="" />
				</td>
			</tr>
			<tr id="cpass" style="display:none">
				<td valign="top"><strong>充值卡密</strong><br />
				</td>
				<td>
					<input type="text" id="password" name="password" maxlength="50" size="50" placeholder="充值卡密码"/>
				</td>
			</tr>
			<tr>
				<td valign="top"><strong>充值方式</strong><br />
				</td>
				<td>
					<?php if(plugin_check_card()){?>
						<input type="radio" id="paytype6" class="paytype" name="paytype" value="6" checked onclick="checkCard()"/>充值卡
					<?php }?>
					<?php if(get_option('erphpdown_stripe_pk')){?>
						<input type="radio" id="paytype60" class="paytype" name="paytype" value="60" checked onclick="checkCard()"/>信用卡
					<?php }?>
					<?php if(plugin_check_ecpay() && get_option('erphpdown_ecpay_MerchantID')){?>
						<input type="radio" id="paytype70" class="paytype" name="paytype" value="70" checked onclick="checkCard()"/>新台币
					<?php }?>
					<?php if(get_option('erphpdown_usdt_address')){?>
						<input type="radio" id="paytype50" class="paytype" name="paytype" value="50" checked onclick="checkCard()"/>USDT
					<?php }?>
					<?php if(get_option('ice_payapl_api_uid')){?> 
						<input type="radio" id="paytype2" class="paytype" name="paytype" value="2" checked onclick="checkCard()"/>PayPal&nbsp;  
					<?php }?> 
					<?php if(get_option('ice_weixin_mchid')){?> 
						<input type="radio" id="paytype4" class="paytype" checked name="paytype" value="4" checked onclick="checkCard()" />微信&nbsp;
					<?php }?>
					<?php if((get_option('ice_ali_partner') || get_option('ice_ali_app_id')) && !erphpdown_is_weixin()){?> 
						<input type="radio" id="paytype1" class="paytype" checked name="paytype" value="1" checked onclick="checkCard()" />支付宝&nbsp;
					<?php }?>
					<?php if(get_option('erphpdown_f2fpay_id') && !erphpdown_is_weixin()){?> 
						<input type="radio" id="paytype5" class="paytype" checked name="paytype" value="5" checked onclick="checkCard()" />支付宝&nbsp;
					<?php }?>
					<?php if(get_option('erphpdown_payjs_appid')){?> 
						<input type="radio" id="paytype19" class="paytype" name="paytype" value="19" checked onclick="checkCard()"/>微信&nbsp;      
						<input type="radio" id="paytype20" class="paytype" name="paytype" value="20" checked onclick="checkCard()"/>支付宝&nbsp;      
					<?php }?>
					<?php if(get_option('erphpdown_xhpay_appid31')){?> 
						<input type="radio" id="paytype18" class="paytype" name="paytype" value="18" checked onclick="checkCard()"/>微信&nbsp;      
					<?php }?>
					<?php if(get_option('erphpdown_xhpay_appid32')){?> 
						<input type="radio" id="paytype17" class="paytype" name="paytype" value="17" checked onclick="checkCard()"/>支付宝&nbsp;      
					<?php }?>
					<?php if(get_option('erphpdown_paypy_key')){?> 
						<?php if(!get_option('erphpdown_paypy_alipay')){?><input type="radio" id="paytype8" class="paytype" name="paytype" value="8" checked onclick="checkCard()"/>支付宝&nbsp;<?php }?> 
						<?php if(!get_option('erphpdown_paypy_wxpay')){?><input type="radio" id="paytype7" class="paytype" name="paytype" value="7" checked onclick="checkCard()"/>微信&nbsp;<?php }?>    
					<?php }?>
					<?php if(get_option('erphpdown_codepay_appid')){?> 
						<?php if(!get_option('erphpdown_codepay_qqpay')){?><input type="radio" id="paytype15" class="paytype" name="paytype" value="15"  checked onclick="checkCard()"/>QQ钱包&nbsp;<?php }?>
						<?php if(!get_option('erphpdown_codepay_alipay')){?><input type="radio" id="paytype13" class="paytype" name="paytype" value="13" checked onclick="checkCard()"/>支付宝&nbsp;<?php }?>
						<?php if(!get_option('erphpdown_codepay_wxpay')){?><input type="radio" id="paytype14" class="paytype" name="paytype" value="14"  checked onclick="checkCard()"/>微信&nbsp;<?php }?>
					<?php }?>
					<?php if(get_option('erphpdown_epay_id')){?>
						<?php if(!get_option('erphpdown_epay_qqpay')){?><input type="radio" id="paytype23" class="paytype" name="paytype" value="23" checked onclick="checkCard()"/>QQ钱包<?php }?>
						<?php if(!get_option('erphpdown_epay_alipay')){?><input type="radio" id="paytype21" class="paytype" name="paytype" value="21" checked onclick="checkCard()"/>支付宝&nbsp;<?php }?>
						<?php if(!get_option('erphpdown_epay_wxpay')){?><input type="radio" id="paytype22" class="paytype" name="paytype" value="22" checked onclick="checkCard()"/>微信<?php }?>
					<?php }?>
					<?php if(get_option('erphpdown_easepay_id')){?>
						<?php if(!get_option('erphpdown_easepay_alipay')){?><input type="radio" id="paytype41" class="paytype" name="paytype" value="41" checked onclick="checkCard()"/>支付宝&nbsp;<?php }?>
						<?php if(!get_option('erphpdown_easepay_wxpay')){?><input type="radio" id="paytype42" class="paytype" name="paytype" value="42" checked onclick="checkCard()"/>微信<?php }?>
					<?php }?>
					<?php if(get_option('erphpdown_vpay_key')){?>
						<?php if(!get_option('erphpdown_vpay_alipay')){?><input type="radio" id="paytype31" class="paytype" name="paytype" value="31" checked onclick="checkCard()"/>支付宝&nbsp;<?php }?>
						<?php if(!get_option('erphpdown_vpay_wxpay')){?><input type="radio" id="paytype32" class="paytype" name="paytype" value="32" checked onclick="checkCard()"/>微信<?php }?>
					<?php }?>
					
				</td>
			</tr>
		</table>
		<br /> 
		<table> <tr>
			<td><p class="submit">
				<input type="submit" name="Submit" value="充值" class="button-primary" />
			</p>
		</td>

	</tr> </table>

</form>

</div>
