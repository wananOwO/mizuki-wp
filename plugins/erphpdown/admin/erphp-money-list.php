<?php
// +----------------------------------------------------------------------
// | ERPHP [ PHP DEVELOP ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.mobantu.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: mobantu <82708210@qq.com>
// +----------------------------------------------------------------------
if ( !defined('ABSPATH') ) {exit;}

$user_Info = wp_get_current_user();
$sql       = "SELECT SUM(ice_money) FROM $wpdb->iceget WHERE ice_user_id=".$user_Info->ID;
$listSql   = "SELECT * FROM $wpdb->iceget where ice_user_id=".$user_Info->ID." order by ice_time DESC";

$total_money = $wpdb->get_var($sql);
$list        = $wpdb->get_results($listSql);

$lv=get_option("ice_ali_money_site");

$ice_ali_money_site = get_user_meta($user_Info->ID,'ice_ali_money_site',true);
if($ice_ali_money_site != '' && ($ice_ali_money_site || $ice_ali_money_site == 0)){
	$lv = $ice_ali_money_site;
}

?>
<div class="wrap">
	<h2>提现列表</h2>
	<p><?php printf(("共申请提现<strong>%.2f</strong>"), $total_money); ?>&nbsp;&nbsp;&nbsp;&nbsp;
		<?php $user_Info   = wp_get_current_user();
		$userMoney=$wpdb->get_row("select * from ".$wpdb->iceinfo." where ice_user_id=".$user_Info->ID);

		if(!$userMoney)
		{
			$okMoney=0;
		}
		else 
		{
			$okMoney=$userMoney->ice_have_money - $userMoney->ice_get_money;

		} 
		if($okMoney >= get_option('ice_ali_money_limit'))
		{
			?>
			<a href="<?php echo admin_url('admin.php?page=erphpdown/admin/erphp-money.php')?>">申请提现</a>
		<?php } else {?>
			余额满￥<?php echo get_option('ice_ali_money_limit'); ?>方可提现！
		<?php } ?>
	</p>
	<table class="widefat fixed striped posts">
		<thead>
			<tr>
				<th width="15%">申请时间</th>
				<th width="15%">申请金额</th>
				<th width="15%">到帐金额</th>
				<th width="17%">支付状态</th>
				<th width="15%">备注</th>		
			</tr>
		</thead>
		<tbody>
			<?php
			if($list) {
				foreach($list as $value)
				{
					$result=$value->ice_success==1?'已支付':'--';
					echo "<tr>\n";
					echo "<td>$value->ice_time</td>\n";
					echo "<td>$value->ice_money</td>\n";
					echo "<td>".sprintf("%.2f",(((100-$lv)*$value->ice_money)/100))."</td>\n";
					echo "<td>$result</td>\n";
					echo "<td>$value->ice_note</td>\n";
					echo "</tr>";
				}
			}
			else
			{
				echo '<tr><td colspan="5" align="center"><strong>没有提现记录</strong></td></tr>';
			}
			?>
		</tbody>
	</table>
</div>
