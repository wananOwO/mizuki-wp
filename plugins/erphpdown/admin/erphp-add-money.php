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

if(!current_user_can('administrator')) exit;
?>
<?php 

	if(isset($_POST['action']) && $_POST['action'] == 'clear'){
		$wpdb->query("update $wpdb->iceinfo set ice_have_money=0, ice_get_money=0");
		echo "<div class='updated settings-error'><p>处理成功！</p></div>";
	}elseif(isset($_POST['action']) && $_POST['action'] == 'add'){
		$money=esc_sql($_POST['ice_money']);
		$user_name=esc_sql($_POST['user_id']);
		$user_info=get_user_by('login', $user_name);
		if($user_info){
			$user_id=$user_info->ID;
			if(addUserMoney($user_id, $money, '后台充值'))
			{
				$sql="INSERT INTO $wpdb->icemoney (ice_money,ice_num,ice_user_id,ice_time,ice_success,ice_note,ice_success_time,ice_alipay)
				VALUES ('$money','".date("ymdhis").mt_rand(100,999).mt_rand(100,999).mt_rand(100,999)."','".$user_id."','".date("Y-m-d H:i:s")."',1,'1','".date("Y-m-d H:i:s")."','')";
				$wpdb->query($sql);
			}
	
			if($money > 0){
				echo "<div class='updated settings-error'><p>充值成功！</p></div>";
			}elseif($money < 0){
				echo "<div class='updated settings-error'><p>扣钱成功！</p></div>";
			}
		}else{
			echo "<div class='error settings-error'><p>用户不存在！</p></div>";
		}
	} elseif (isset($_POST['action']) && $_POST['action'] == 'addall') {
		$money = esc_sql($_POST['ice_money']);
		$result = $wpdb->query("UPDATE $wpdb->iceinfo SET ice_have_money = ice_have_money + $money");

		if($money >= 0) {
			echo "<div class='error settings-error'><p>充值成功，更新了 $result 条数据</p></div>";
		} elseif($money < 0) {
			echo "<div class='error settings-error'><p>扣款成功，更新了 $result 条数据</p></div>";
		}
	}
?>
<div class="wrap">
	<script type="text/javascript">
		function checkFm()
		{
			if(document.getElementById("ice_money").value=="")
			{
				alert('请输入金额');
				return false;
			}

		}
	</script>
	<form action="" method="post" onsubmit="return checkFm();">

		<h2>给用户充值/扣钱</h2>
		<table class="form-table">
			<tr>
				<td valign="top" width="30%"><strong>充值金额</strong><br />
				</td>
				<td>
					<input type="text" id="ice_money" name="ice_money" maxlength="50" size="50" /><?php echo get_option('ice_name_alipay');?>
					<p>请输入一个整数，负数为扣钱</p>
				</td>
			</tr>
			<tr>
				<td valign="top" width="30%"><strong>用户名</strong><br />
				</td>
				<td>
					<input type="text" id="user_id" name="user_id" maxlength="50" size="50" />
				</td>
			</tr>
		</table>
		<br /> <br />
		<table> <tr>
			<td><p class="submit">
				<input type="submit" value="充值/扣钱" class="button-primary" onclick="return confirm('确认此操作?');"/>
				<input type="hidden" name="action" value="add">
			</p>
		</td><td><p class="submit">
				<input type="submit" value="为所有用户充值/扣钱" class="button-primary" onclick="return confirm('确认此操作?');"/>
				<input type="hidden" name="action" value="addall">
			</p>
		</td>

	</tr> </table>

</form>

<form action="" method="post">

		<h2>特别处理</h2>
		<table> <tr>
			<td><p class="submit">
				<input type="submit" value="清空所有人的余额" class="button-primary" onclick="return confirm('确认此操作?');"/>
				<input type="hidden" name="action" value="clear">
			</p>
		</td>

	</tr> </table>

</form>
</div>