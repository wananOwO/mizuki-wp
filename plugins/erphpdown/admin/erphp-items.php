<?php
// +----------------------------------------------------------------------
// | ERPHP [ PHP DEVELOP ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.mobantu.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: mobantu <82708210@qq.com>
// +----------------------------------------------------------------------
if ( !defined('ABSPATH') ) {exit;}
if(current_user_can('administrator'))
{
	//统计数据
	$total_trade   = $wpdb->get_var("SELECT COUNT(ice_id) FROM $wpdb->icealipay");
	$total_success = $wpdb->get_var("SELECT COUNT(ice_id) FROM $wpdb->icealipay WHERE ice_success>0");
	$total_money   = $wpdb->get_var("SELECT SUM(ice_price) FROM $wpdb->icealipay WHERE ice_success>0");
}
else 
{
	$user_info=wp_get_current_user();
	$total_trade   = $wpdb->get_var("SELECT COUNT(ice_id) FROM $wpdb->icealipay where ice_author=".$user_info->ID);
	$total_success = $wpdb->get_var("SELECT COUNT(ice_id) FROM $wpdb->icealipay WHERE ice_success>0 and ice_author=".$user_info->ID);
	$total_money   = $wpdb->get_var("SELECT SUM(ice_price) FROM $wpdb->icealipay WHERE ice_success>0 and ice_author=".$user_info->ID);
}
//分页计算
$ice_perpage = 20;
$pages = ceil($total_trade / $ice_perpage);
$page=isset($_GET['paged']) ?intval($_GET['paged']) :1;
$offset = $ice_perpage*($page-1);

if(current_user_can('administrator'))
{
	$list = $wpdb->get_results("SELECT * FROM $wpdb->icealipay order by ice_time DESC limit $offset,$ice_perpage");
}
else 
{
	$list = $wpdb->get_results("SELECT * FROM $wpdb->icealipay where ice_author= ".$user_info->ID." order by ice_time DESC limit $offset,$ice_perpage");
}
?>
<div class="wrap">
	<h2>销售订单</h2>
	<p><?php printf(('共有<strong>%s</strong>笔交易，其中<strong>%s</strong>笔交易完成了付款.总金额：<strong>%s</strong>'), 
	number_format_i18n($total_trade), number_format_i18n($total_success),$total_money); ?></p>
	<table class="widefat fixed striped posts">
		<thead>
			<tr>
				<th width="7%">用户ID</th>
				<th width="7%">订单号</th>
				<th width="25%">商品名称</th>
				<th width="5%">价格</th>
				<th width="5%">下载次数</th>
				<th width="12%">交易时间</th>			
				<th width="8%">交易状态</th>			
			</tr>
		</thead>
		<tbody>
			<?php
			if($list) {
				foreach($list as $value)
				{
					$result=$value->ice_success?'成功':'未完成';
					echo "<tr>\n";
					echo "<td>".get_the_author_meta( 'user_login', $value->ice_user_id )."</td>";
					echo "<td>$value->ice_num</td>\n";
					echo "<td><a target=_blank href='".get_permalink($value->ice_post)."'>$value->ice_title</a></td>\n";
					echo "<td>$value->ice_price</td>\n";
					echo "<td>".get_post_meta($value->ice_post,'down_times',true)."</td>\n";
					echo "<td>$value->ice_time</td>\n";
					echo "<td>$result</td>\n";
					echo "</tr>";
				}
			}
			else
			{
				echo '<tr><td colspan="7" align="center"><strong>没有交易记录</strong></td></tr>';
			}
			?>
		</tbody>
	</table>
	<?php echo erphp_admin_pagenavi($total_trade,$ice_perpage);?> 
	　
</div>
