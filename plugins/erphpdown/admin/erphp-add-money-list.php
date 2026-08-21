<?php
// +----------------------------------------------------------------------
// | ERPHP [ PHP DEVELOP ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.mobantu.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: mobantu <82708210@qq.com>
// +----------------------------------------------------------------------
if ( !defined('ABSPATH') ) {exit;}

$user_info=wp_get_current_user();
$total_trade   = $wpdb->get_var("SELECT COUNT(ice_id) FROM $wpdb->icemoney WHERE ice_success>0 and ice_user_id=".$user_info->ID);

//分页计算
$ice_perpage = 20;
$pages = ceil($total_trade / $ice_perpage);
$page=isset($_GET['paged']) ?intval($_GET['paged']) :1;
$offset = $ice_perpage*($page-1);
?>

<div class="wrap">
	<?php
	$adds=$wpdb->get_results("SELECT * FROM $wpdb->icemoney where ice_success=1 and ice_user_id=".$user_info->ID." order by ice_time DESC limit $offset,$ice_perpage");
	?>
	<h2>我的充值记录</h2>
	<table class="widefat fixed striped posts">

		<thead>
			<tr>
				<th width="35%">充值时间</th>
				<th width="25%">充值金额</th>
				<th width="20%">充值方式</th>
			</tr>
		</thead>
		<tbody>
			<?php
			if($adds) {
				foreach($adds as $value)
				{
					echo "<tr>\n";
					echo "<td>$value->ice_time</td>";
					echo "<td>$value->ice_money</td>\n";
					if(intval($value->ice_note)==0)
					{
						echo "<td><font color=green>在线充值</font></td>\n";
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
	<?php echo erphp_admin_pagenavi($total_trade,$ice_perpage);?>　		
</div>