<?php
// +----------------------------------------------------------------------
// | ERPHP [ PHP DEVELOP ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.mobantu.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: mobantu <82708210@qq.com>
// +----------------------------------------------------------------------
if ( !defined('ABSPATH') ) {exit;}
$user_Info   = wp_get_current_user();
$total_trade   = $wpdb->get_var("SELECT COUNT(ice_id) FROM $wpdb->icealipay WHERE ice_success>0 and ice_user_id in (select ID from $wpdb->users where father_id=".$user_Info->ID.")");
$total_money   = $wpdb->get_var("SELECT SUM(ice_price) FROM $wpdb->icealipay WHERE ice_success>0 and ice_user_id in (select ID from $wpdb->users where father_id=".$user_Info->ID.")");

//分页计算
$ice_perpage = 20;
$pages = ceil($total_trade / $ice_perpage);
$page=isset($_GET['p']) ?intval($_GET['p']) :1;
$offset = $ice_perpage*($page-1);
$list = $wpdb->get_results("SELECT * FROM $wpdb->icealipay where ice_success=1 and ice_user_id in (select ID from $wpdb->users where father_id=".$user_Info->ID.") order by ice_time DESC limit $offset,$ice_perpage");

?>
<div class="wrap">
	<h2>推广下载订单</h2>
	<p><?php printf(('共<strong>%s</strong>.'), $total_money); ?></p>
	<table class="widefat fixed striped posts">
		<thead>
			<tr>
				<th width="10%">用户ID</th>
				<th width="20%">订单号</th>
				<th width="25%">商品名称</th>
				<th width="5%"><?php echo get_option('ice_name_alipay');?></th>
				<th width="15%">交易时间</th>		
			</tr>
		</thead>
		<tbody>
			<?php
			if($list) {
				foreach($list as $value)
				{
					echo "<tr>\n";
					echo "<td>".get_the_author_meta( 'user_login', $value->ice_user_id )."</td>";
					echo "<td>$value->ice_num</td>";
					echo "<td>$value->ice_title</td>\n";
					echo "<td>$value->ice_price</td>\n";
					echo "<td>$value->ice_time</td>\n";
					echo "</tr>";
				}
			}
			else
			{
				echo '<tr><td colspan="5" align="center"><strong>没有推广记录</strong></td></tr>';
			}
			?>
		</tbody>
	</table>
	<?php echo erphp_admin_pagenavi($total_trade,$ice_perpage);?> 
	　　
</div>

