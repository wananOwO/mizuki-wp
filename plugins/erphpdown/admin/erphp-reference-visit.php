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
if(current_user_can('level_10')){
	$total_user   = $wpdb->get_var("SELECT COUNT(ice_id) FROM $wpdb->aff");
}else{
	$total_user   = $wpdb->get_var("SELECT COUNT(ice_id) FROM $wpdb->aff WHERE ice_user_id=".$user_Info->ID);
}

//分页计算
$ice_perpage = 20;
$pages = ceil($total_user / $ice_perpage);
$page=isset($_GET['paged']) ?intval($_GET['paged']) :1;
$offset = $ice_perpage*($page-1);
$list = $wpdb->get_results("SELECT * FROM $wpdb->aff where ice_user_id=".$user_Info->ID." limit $offset,$ice_perpage");

?>
<div class="wrap">
	<h2>推广访问</h2>
	<p><?php printf(('共<strong>%s</strong>次'), $total_user); ?>&nbsp;&nbsp;&nbsp;&nbsp;永久推广链接：<textarea id="spreadurl" rows="1" cols="80"><?php echo esc_url( home_url( '/?aff=' ) ).$user_Info->ID; ?></textarea></p>
	<table class="widefat fixed posts">
		<thead>
			<tr>
				<th width="20%">推广主</th>
				<th width="20%">访问时间</th>	 
				<th width="20%">推广奖励</th>	    
				<th width="30%">访问IP</th>	    
			</tr>
		</thead>
		<tbody>
			<?php
			if($list) {
				foreach($list as $value)
				{
					echo "<tr>\n";
					echo "<td>".get_the_author_meta( 'user_login', $value->ice_user_id )."</td>";
					echo "<td>".$value->ice_time."</td>";
					echo "<td>".$value->ice_price.get_option('ice_name_alipay')."</td>";
					echo "<td>".$value->ice_ip."</td>";
					echo "</tr>";
				}
			}
			else
			{
				echo '<tr><td colspan="3" align="center"><strong>没有推广记录</strong></td></tr>';
			}
			?>
		</tbody>
	</table>
	<?php echo erphp_admin_pagenavi($total_user,$ice_perpage);?> 
	　　
</div>

