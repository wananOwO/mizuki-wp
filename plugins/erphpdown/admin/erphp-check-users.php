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
	<script type="text/javascript">
		function checkFm()
		{
			if(document.getElementById("user_id").value=="")
			{
				alert('请输入用户名');
				return false;
			}

		}
	</script>
	<?php
    //global $wpdb;
    if(isset($_POST['user_id'])){
	$user_name=esc_sql($_POST['user_id']);
	if($user_name){
		$user_info=get_user_by('login', $user_name);


	if(!$user_info){
		echo '<h2>'.$user_name.'不存在！</h2>';
	}else{
		if (isset($_POST['check_money']) && $_POST['check_money']){

			$userMoney=$wpdb->get_row("select * from ".$wpdb->iceinfo." where ice_user_id=".$user_info->ID);
			if(!$userMoney)
			{
				$okMoney=0;
			}
			else
			{
				$okMoney=$userMoney->ice_have_money - $userMoney->ice_get_money;
			}?>
			<h2><?php echo $user_name;?>的余额</h2>
			<table class="form-table">

				<tr>
					<td valign="top" width="30%"><strong>可用金额：</strong><br />
					</td>
					<td>
						<?php echo sprintf("%.2f",$okMoney)?><?php echo get_option('ice_name_alipay')?>
					</td>
				</tr>
			</table>
			<?php
		}elseif(isset($_POST['check_add']) && $_POST['check_add']){
			$adds=$wpdb->get_results("SELECT * FROM $wpdb->icemoney where ice_success=1 and  ice_user_id=$user_info->ID order by ice_time DESC limit 0,60");
			?>
			<h3><?php echo $user_name;?>&nbsp;的充值记录</h3>

			<table class="widefat striped" style="width:100%;">
				<thead>
					<tr>
						<th>充值金额</th>
						<th>充值时间</th>
						<th>充值方式</th>
					</tr>
				</thead>
				<tbody>
					<?php
					if($adds) {
						foreach($adds as $value)
						{
							echo "<tr>\n";
							
							echo "<td>$value->ice_money</td>\n";
							echo "<td>$value->ice_time</td>";
							if(intval($value->ice_note)==0)
							{
								if($value->ice_alipay){
									echo "<td><font color=green>".$value->ice_alipay."</font></td>\n";
								}else{
									echo "<td><font color=green>在线充值</font></td>\n";
								}
							}elseif(intval($value->ice_note)==1)
							{
								echo "<td>后台充值</td>\n";
							}
							elseif(intval($value->ice_note)==4)
							{
								echo "<td><font color=orange>mycred兑换</font></td>\n";
							}
							elseif(intval($value->ice_note)==6)
							{
								echo "<td><font color=orange>充值卡</font></td>\n";
							}
							echo "</tr>";
						}
					}
					else
					{
						echo '<tr><td colspan="3" align="center"><strong>没有记录</strong></td></tr>';
					}
					?>
				</tbody>
			</table>
			<?php
		}elseif(isset($_POST['check_vipdown']) && $_POST['check_vipdown']){
			$adds=$wpdb->get_results("SELECT * FROM $wpdb->down where ice_user_id=$user_info->ID order by ice_time DESC limit 0,60");
			?>
			<h3><?php echo $user_name;?>&nbsp;的VIP免费下载记录</h3>

			<table class="widefat striped" style="width:100%;">
				<thead>
					<tr>

						<th width="60%">资源名称</th>
						<th width="30%">下载时间</th>
						<th width="10%">下载IP</th>
					</tr>
				</thead>
				<tbody>
					<?php
					if($adds) {
						foreach($adds as $value)
						{
							echo "<tr>\n";
							echo "<td><a href='".get_permalink($value->ice_post_id)."' target=_blank>".get_post($value->ice_post_id)->post_title."</a></td>";
							echo "<td>$value->ice_time</td>\n";
							echo "<td>$value->ice_ip</td>\n";
							echo "</tr>";
						}
					}
					else
					{
						echo '<tr><td colspan="3" align="center"><strong>没有记录</strong></td></tr>';
					}
					?>
				</tbody>
			</table>
			<?php
		}elseif(isset($_POST['check_cost']) && $_POST['check_cost']){


			$total_trade   = $wpdb->get_var("SELECT COUNT(ice_id) FROM $wpdb->icealipay WHERE ice_success>0 and ice_user_id=".$user_info->ID);
			$total_money   = $wpdb->get_var("SELECT SUM(ice_price) FROM $wpdb->icealipay WHERE ice_success>0 and ice_user_id=".$user_info->ID);
			$ice_perpage = 60;

			$list =$wpdb->get_results("SELECT * FROM $wpdb->icealipay where ice_success=1 and  ice_user_id=$user_info->ID order by ice_time DESC limit 0,$ice_perpage");
			?>
			<h3><?php echo $user_name;?>&nbsp;的消费清单</h3>
			<p><?php printf(('共<strong>%s</strong>.'), $total_money); ?></p>
			<table class="widefat">
				<thead>
					<tr>
						<th>订单号</th>
						<th>商品名称</th>
						<th>价格</th>
						<th>交易时间</th>
					</tr>
				</thead>
				<tbody>
					<?php
					if($list) {
						foreach($list as $value)
						{
							echo "<tr>\n";
							echo "<td>$value->ice_num</td>";
							echo "<td>$value->ice_title</td>\n";
							echo "<td>$value->ice_price</td>\n";
							echo "<td>$value->ice_time</td>\n";
							echo "</tr>";
						}
					}
					else
					{
						echo '<tr><td colspan="4" align="center"><strong>没有记录</strong></td></tr>';
					}
					?>
				</tbody>
			</table>


			<?php
		}
	}
}
}
?>

<form action="" method="post" onsubmit="return checkFm();">

	<h3>用户信息查询</h3>
	<table class="form-table">
		<tr>
			<td valign="top"><strong>用户名</strong><br />
			</td>
			<td>
				<input type="text" id="user_id" name="user_id" maxlength="50" size="50" />
				&nbsp;&nbsp;
				<?php
				submit_button('余额查询', 'primary', 'check_money', '');
				?>
				&nbsp;
				<?php
				submit_button('充值记录查询', 'primary', 'check_add', '');
				?>
				&nbsp;
				<?php
				submit_button('消费记录查询', 'primary', 'check_cost', '');
				?>
				&nbsp;
				<?php
				submit_button('VIP免费下载记录查询', 'primary', 'check_vipdown', '');
				?>
			</td>

		</tr>
	</table>

</form>


</div>