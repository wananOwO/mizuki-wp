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
$total_trade   = $wpdb->get_var("SELECT COUNT(ice_id) FROM $wpdb->down where ice_user_id=".$user_info->ID);

//分页计算
$ice_perpage = 20;
$pages = ceil($total_trade / $ice_perpage);
$page=isset($_GET['paged']) ?intval($_GET['paged']) :1;
$offset = $ice_perpage*($page-1);
?>

<div class="wrap">
	<?php
	

	$adds=$wpdb->get_results("SELECT * FROM $wpdb->down where ice_user_id=".$user_info->ID." order by ice_time DESC limit $offset,$ice_perpage");

	?>
	<h2>免费下载记录</h2><table class="widefat fixed striped posts">
<p>不统计VIP用户下载对全站用户免费的资源</p>
		<thead>
			<tr>
				<th width="70%">资源名称</th>
				<th width="20%">下载时间</th>
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
	<?php echo erphp_admin_pagenavi($total_trade,$ice_perpage);?>
	　　		
</div>