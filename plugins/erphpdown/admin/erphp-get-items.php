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
$total_trade   = $wpdb->get_var("SELECT COUNT(ice_id) FROM $wpdb->icealipay WHERE ice_success>0 and ice_user_id=".$user_info->ID);
$total_money   = $wpdb->get_var("SELECT SUM(ice_price) FROM $wpdb->icealipay WHERE ice_success>0 and ice_user_id=".$user_info->ID);
$ice_perpage = 20;
$pages = ceil($total_trade / $ice_perpage);
$page=isset($_GET['paged']) ?intval($_GET['paged']) :1;
$offset = $ice_perpage*($page-1);

$list = $wpdb->get_results("SELECT * FROM $wpdb->icealipay where ice_success=1 and ice_user_id=$user_info->ID order by ice_time DESC limit $offset,$ice_perpage");

?>
<div class="wrap">
	<h2>虚拟商品订单</h2>
	<p><?php printf(('共<strong>%s</strong>.'), $total_money); ?></p>
	<table class="wp-list-table widefat fixed striped posts">
		<thead>
			<tr>
				<th width="8%">订单号</th>
				<th width="25%">商品名称</th>
				<th width="5%">价格</th>
				<th width="15%">交易时间</th>	
				<th width="15%">操作</th>		
			</tr>
		</thead>
		<tbody>
			<?php
			if($list) {
				foreach($list as $value)
				{
					$start_down = get_post_meta($value->ice_post, 'start_down', true);
					$start_down2 = get_post_meta($value->ice_post, 'start_down2', true);
					echo "<tr>\n";
					echo "<td>$value->ice_num</td>";
					echo "<td><a target=_blank href='".get_permalink($value->ice_post)."'>$value->ice_title</a></td>\n";
					echo "<td>$value->ice_price</td>\n";
					echo "<td>$value->ice_time</td>\n";
					if($start_down || $start_down2){
						echo "<td><a href='".constant("erphpdown").'download.php?url='.$value->ice_url."' target='_blank'>下载</a></td>\n";
					}else{
						echo "<td><a href='".get_permalink($value->ice_post)."' target='_blank'>查看</a></td>\n";
					}
					echo "</tr>";
				}
			}
			else
			{
				echo '<tr><td colspan="5" align="center"><strong>没有交易记录</strong></td></tr>';
			}
			?>
		</tbody>
	</table>
	<?php echo erphp_admin_pagenavi($total_trade,$ice_perpage);?>

</div>
