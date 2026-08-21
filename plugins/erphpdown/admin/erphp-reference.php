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
$total_user   = $wpdb->get_var("SELECT COUNT(ID) FROM $wpdb->users WHERE father_id=".$user_Info->ID);

//分页计算
$ice_perpage = 20;
$pages = ceil($total_user / $ice_perpage);
$page=isset($_GET['paged']) ?intval($_GET['paged']) :1;
$offset = $ice_perpage*($page-1);
$list = $wpdb->get_results("SELECT ID,user_login,user_registered FROM $wpdb->users where father_id=".$user_Info->ID." order by ID desc limit $offset,$ice_perpage");

?>
<div class="wrap">
	<h2>推广用户</h2>
	<p><?php printf(('共<strong>%s</strong>人'), $total_user); ?>&nbsp;&nbsp;&nbsp;&nbsp;永久推广链接：<textarea id="spreadurl" rows="1" cols="80"><?php echo esc_url( home_url( '/?aff=' ) ).$user_Info->ID; ?></textarea></p>
	<table class="widefat fixed striped posts">
		<thead>
			<tr>
				<th width="30%">用户ID</th>
				<th width="40%">注册时间</th>	    
				<th width="30%">消费额</th>	    
			</tr>
		</thead>
		<tbody>
			<?php
			if($list) {
				foreach($list as $value)
				{
					echo "<tr>\n";
					echo "<td>".$value->user_login."</td>";
					echo "<td>".get_date_from_gmt($value->user_registered)."</td>";
					echo "<td>".erphpGetUserAllXiaofei($value->ID)."</td>";
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

