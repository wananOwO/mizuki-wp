<?php
// +----------------------------------------------------------------------
// | ERPHP [ PHP DEVELOP ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.mobantu.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: mobantu <82708210@qq.com>
// +----------------------------------------------------------------------
if ( !defined('ABSPATH') ) {exit;}

if(isset($_POST['action'])){
	if($_POST['action'] == '7'){
		$wpdb->query("delete from $wpdb->checkin WHERE DATE(create_time) < DATE_SUB(CURDATE(), INTERVAL 1 WEEK)");
		echo '<div class="updated settings-error"><p>清理成功！</p></div>';
	}elseif($_POST['action'] == '3'){
		$wpdb->query("delete from $wpdb->checkin WHERE DATE(create_time) < DATE_SUB(CURDATE(), INTERVAL 3 DAY)");
		echo '<div class="updated settings-error"><p>清理成功！</p></div>';
	}elseif($_POST['action'] == '30'){
		$wpdb->query("delete from $wpdb->checkin WHERE DATE(create_time) < DATE_SUB(CURDATE(), INTERVAL 1 MONTH)");
		echo '<div class="updated settings-error"><p>清理成功！</p></div>';
	}elseif($_POST['action'] == '180'){
		$wpdb->query("delete from $wpdb->checkin WHERE DATE(create_time) < DATE_SUB(CURDATE(), INTERVAL 6 MONTH)");
		echo '<div class="updated settings-error"><p>清理成功！</p></div>';
	}elseif($_POST['action'] == '365'){
		$wpdb->query("delete from $wpdb->checkin WHERE DATE(create_time) < DATE_SUB(CURDATE(), INTERVAL 1 YEAR)");
		echo '<div class="updated settings-error"><p>清理成功！</p></div>';
	}
}

$issearch = 0;
if(isset($_GET['username']) && $_GET['username']){
	$user = get_user_by('login',$_GET['username']);
	if($user){
		$suid = $user->ID;
		$issearch = 1;
	}else{
		$suid = 0;
		echo '<div class="error settings-error"><p>用户不存在！</p></div>';
	}
}
if($issearch == '1'){
	$total_trade   = $wpdb->get_var("SELECT COUNT(ID) FROM $wpdb->checkin where user_id=".$suid);
}else{
	$total_trade   = $wpdb->get_var("SELECT COUNT(ID) FROM $wpdb->checkin");
}
$ice_perpage = 20;
$pages = ceil($total_trade / $ice_perpage);
$page=isset($_GET['paged']) ?intval($_GET['paged']) :1;
$offset = $ice_perpage*($page-1);
if($issearch == '1'){
	$adds=$wpdb->get_results("SELECT * FROM $wpdb->checkin where user_id=".$suid." order by create_time DESC limit $offset,$ice_perpage");
}else{
	$adds=$wpdb->get_results("SELECT * FROM $wpdb->checkin order by create_time DESC limit $offset,$ice_perpage");
}
?>
<div class="wrap">
	<h2>签到记录</h2>
	<form method="get"><input type="hidden" name="page" value="erphpdown/admin/erphp-checkin-list.php"><input type="text" name="username" placeholder="登录名，例如：admin" value="<?php if($issearch=='1') echo $_GET['username'];?>"> <input type="submit" value="查询" class="button"></form><br>
	<table class="widefat fixed striped posts">

		<thead>
			<tr>
				<th>用户ID</th>
				<th>奖励</th>
				<th>时间</th>
			</tr>
		</thead>
		<tbody>
			<?php
			if($adds) {
				foreach($adds as $value)
				{
					echo "<tr>\n";
					echo "<td>".get_the_author_meta( 'user_login', $value->user_id )."</td>";
					echo "<td>".$value->credit."</td>";
					echo "<td>".$value->create_time."</td>";
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
	<form action="" method="post" style="display:inline-block;">
		<input type="hidden" name="action" value="3"  />
		<input type="submit" value="清理三天之前所有记录" class="button-primary">
	</form>
    <form action="" method="post" style="display:inline-block;">
		<input type="hidden" name="action" value="7"  />
		<input type="submit" value="清理一周之前所有记录" class="button-primary">
	</form>
	<form action="" method="post" style="display:inline-block;">
		<input type="hidden" name="action" value="30"  />
		<input type="submit" value="清理一月之前所有记录" class="button-primary">
	</form>
	<form action="" method="post" style="display:inline-block;">
		<input type="hidden" name="action" value="180"  />
		<input type="submit" value="清理半年之前所有记录" class="button-primary">
	</form>
	<form action="" method="post" style="display:inline-block;">
		<input type="hidden" name="action" value="365"  />
		<input type="submit" value="清理一年之前所有记录" class="button-primary">
	</form>
</div>