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
$total_trade   = $wpdb->get_var("SELECT COUNT(ice_id) FROM $wpdb->vip where ice_user_id=".$user_info->ID);
//分页计算
$ice_perpage = 20;
$pages = ceil($total_trade / $ice_perpage);
$page=isset($_GET['paged']) ?intval($_GET['paged']) :1;
$offset = $ice_perpage*($page-1);

$list = $wpdb->get_results("SELECT * FROM $wpdb->vip where ice_user_id=".$user_info->ID." order by ice_time DESC limit $offset,$ice_perpage");


?>
<div class="wrap">
	<h2>VIP升级记录</h2>
	<table class="widefat fixed striped posts">
		<thead>
			<tr>
				<th>VIP类型</th>
				<th>价格</th>
				<th>交易时间</th>				
			</tr>
		</thead>
		<tbody>
			<?php
			if($list) {
				$erphp_life_name    = get_option('erphp_life_name')?get_option('erphp_life_name'):'终身VIP';
				$erphp_year_name    = get_option('erphp_year_name')?get_option('erphp_year_name'):'包年VIP';
				$erphp_quarter_name = get_option('erphp_quarter_name')?get_option('erphp_quarter_name'):'包季VIP';
				$erphp_month_name  = get_option('erphp_month_name')?get_option('erphp_month_name'):'包月VIP';
				$erphp_day_name  = get_option('erphp_day_name')?get_option('erphp_day_name'):'体验VIP';
				foreach($list as $value)
				{
					if($value->ice_user_type == 6) $typeName = $erphp_day_name;
					else{$typeName=$value->ice_user_type==7 ?$erphp_month_name :($value->ice_user_type==8 ?$erphp_quarter_name : ($value->ice_user_type==10 ?$erphp_life_name : $erphp_year_name));}
					echo "<tr>\n";
					echo "<td>$typeName</td>\n";
					echo "<td>$value->ice_price</td>\n";
					echo "<td>$value->ice_time</td>\n";
					echo "</tr>";
				}
			}
			else
			{
				echo '<tr><td colspan="3" align="center"><strong>没有交易记录</strong></td></tr>';
			}
			?>
		</tbody>
	</table>
	<?php echo erphp_admin_pagenavi($total_trade,$ice_perpage);?>
</div>
