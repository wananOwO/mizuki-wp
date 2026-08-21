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
		$wpdb->query("delete from $wpdb->down WHERE ice_vip=0 and ice_time < DATE_SUB(CURDATE(), INTERVAL 1 WEEK)");
		echo '<div class="updated settings-error"><p>清理成功！</p></div>';
	}elseif($_POST['action'] == '3'){
		$wpdb->query("delete from $wpdb->down WHERE ice_vip=0 and ice_time < DATE_SUB(CURDATE(), INTERVAL 3 DAY)");
		echo '<div class="updated settings-error"><p>清理成功！</p></div>';
	}elseif($_POST['action'] == '30'){
		$wpdb->query("delete from $wpdb->down WHERE ice_vip=0 and ice_time < DATE_SUB(CURDATE(), INTERVAL 1 MONTH)");
		echo '<div class="updated settings-error"><p>清理成功！</p></div>';
	}elseif($_POST['action'] == '180'){
		$wpdb->query("delete from $wpdb->down WHERE ice_vip=0 and ice_time < DATE_SUB(CURDATE(), INTERVAL 6 MONTH)");
		echo '<div class="updated settings-error"><p>清理成功！</p></div>';
	}elseif($_POST['action'] == '365'){
		$wpdb->query("delete from $wpdb->down WHERE ice_vip=0 and ice_time < DATE_SUB(CURDATE(), INTERVAL 1 YEAR)");
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
}elseif(isset($_GET['userip']) && $_GET['userip']){
	$issearch = 2;
}elseif(isset($_GET['postid']) && $_GET['postid']){
	$issearch = 3;
}

if(isset($_GET['time']) && $_GET['time'] == '1'){
	$total_trade   = $wpdb->get_var("SELECT COUNT(DISTINCT ice_post_id) FROM $wpdb->down where ice_vip=0 and to_days(ice_time) = to_days(now())");
}elseif(isset($_GET['time']) && $_GET['time'] == '7'){
	$total_trade   = $wpdb->get_var("SELECT COUNT(DISTINCT ice_post_id) FROM $wpdb->down where ice_vip=0 and ice_time>DATE_SUB(CURDATE(), INTERVAL 7 DAY)");
}elseif(isset($_GET['time']) && $_GET['time'] == '30'){
	$total_trade   = $wpdb->get_var("SELECT COUNT(DISTINCT ice_post_id) FROM $wpdb->down where ice_vip=0 and ice_time>DATE_SUB(CURDATE(), INTERVAL 1 MONTH)");
}elseif(isset($_GET['time']) && $_GET['time'] == '365'){
	$total_trade   = $wpdb->get_var("SELECT COUNT(DISTINCT ice_post_id) FROM $wpdb->down where ice_vip=0 and ice_time>DATE_SUB(CURDATE(), INTERVAL 1 YEAR)");
}else{
	if($issearch == '1'){
		$total_trade   = $wpdb->get_var("SELECT COUNT(ice_id) FROM $wpdb->down where ice_user_id=".$suid." and ice_vip=0");
	}elseif($issearch == '2'){
		$total_trade   = $wpdb->get_var("SELECT COUNT(ice_id) FROM $wpdb->down where ice_ip='".$_GET['userip']."' and ice_vip=0");
	}elseif($issearch == '3'){
		$total_trade   = $wpdb->get_var("SELECT COUNT(ice_id) FROM $wpdb->down where ice_post_id='".$_GET['postid']."' and ice_vip=0");
	}else{
		$total_trade   = $wpdb->get_var("SELECT COUNT(ice_id) FROM $wpdb->down where ice_vip=0");
	}
}

$ice_perpage = 20;
$pages = ceil($total_trade / $ice_perpage);
$page=isset($_GET['paged']) ?intval($_GET['paged']) :1;
$offset = $ice_perpage*($page-1);
if(isset($_GET['time']) && $_GET['time'] == '1'){
	$adds   = $wpdb->get_results("SELECT ice_post_id,count(ice_id) as ice_total FROM $wpdb->down where ice_vip=0 and to_days(ice_time) = to_days(now()) group by ice_post_id order by ice_total DESC limit $offset,$ice_perpage");
}elseif(isset($_GET['time']) && $_GET['time'] == '7'){
	$adds   = $wpdb->get_results("SELECT ice_post_id,count(ice_id) as ice_total FROM $wpdb->down where ice_vip=0 and ice_time>DATE_SUB(CURDATE(), INTERVAL 7 DAY) group by ice_post_id order by ice_total DESC limit $offset,$ice_perpage");
}elseif(isset($_GET['time']) && $_GET['time'] == '30'){
	$adds   = $wpdb->get_results("SELECT ice_post_id,count(ice_id) as ice_total FROM $wpdb->down where ice_vip=0 and ice_time>DATE_SUB(CURDATE(), INTERVAL 1 MONTH) group by ice_post_id order by ice_total DESC limit $offset,$ice_perpage");
}elseif(isset($_GET['time']) && $_GET['time'] == '365'){
	$adds   = $wpdb->get_results("SELECT ice_post_id,count(ice_id) as ice_total FROM $wpdb->down where ice_vip=0 and ice_time>DATE_SUB(CURDATE(), INTERVAL 1 YEAR) group by ice_post_id order by ice_total DESC limit $offset,$ice_perpage");
}else{
	if($issearch == '1'){
		$adds=$wpdb->get_results("SELECT * FROM $wpdb->down where ice_user_id=".$suid." and ice_vip=0 order by ice_time DESC limit $offset,$ice_perpage");
	}elseif($issearch == '2'){
		$adds=$wpdb->get_results("SELECT * FROM $wpdb->down where ice_ip='".$_GET['userip']."' and ice_vip=0 order by ice_time DESC limit $offset,$ice_perpage");
	}elseif($issearch == '3'){
		$adds=$wpdb->get_results("SELECT * FROM $wpdb->down where ice_post_id='".$_GET['postid']."' and ice_vip=0 order by ice_time DESC limit $offset,$ice_perpage");
	}else{
		$adds=$wpdb->get_results("SELECT * FROM $wpdb->down where ice_vip=0 order by ice_time DESC limit $offset,$ice_perpage");
	}
}
?>
<div class="wrap">
	<h2>普通资源免费下载记录</h2>
	<ul class="subsubsub">
		<li class="all"><a href="admin.php?page=erphpdown/admin/erphp-freedown-list.php" class="<?php if(!isset($_GET['time']) || (isset($_GET['time']) && $_GET['time'] == '')) echo 'current';?>">全部记录</a> |</li>
		<li class="mine"><a href="admin.php?page=erphpdown/admin/erphp-freedown-list.php&amp;time=1" class="<?php if(isset($_GET['time']) && $_GET['time'] == '1') echo 'current';?>">今日下载/查看排行</a> |</li>
		<li class="mine"><a href="admin.php?page=erphpdown/admin/erphp-freedown-list.php&amp;time=7" class="<?php if(isset($_GET['time']) && $_GET['time'] == '7') echo 'current';?>">7日下载/查看排行</a> |</li>
		<li class="mine"><a href="admin.php?page=erphpdown/admin/erphp-freedown-list.php&amp;time=30" class="<?php if(isset($_GET['time']) && $_GET['time'] == '30') echo 'current';?>">1个月下载/查看排行</a> |</li>
		<li class="mine"><a href="admin.php?page=erphpdown/admin/erphp-freedown-list.php&amp;time=365" class="<?php if(isset($_GET['time']) && $_GET['time'] == '365') echo 'current';?>">1年下载/查看排行</a></li>
	</ul>
	<div class="tablenav top">
		<form method="get"><input type="hidden" name="page" value="erphpdown/admin/erphp-freedown-list.php"><input type="text" name="username" placeholder="登录名，例如：admin" value="<?php if($issearch=='1') echo $_GET['username'];?>"> <input type="text" name="postid" placeholder="资源文章ID，例如：1" value="<?php if($issearch=='3') echo $_GET['postid'];?>"> <input type="text" name="userip" placeholder="IP地址" value="<?php if($issearch=='2') echo $_GET['userip'];?>"> <input type="submit" value="查询" class="button"></form>
	</div>
	<table class="widefat fixed striped posts">
		<thead>
			<tr>
				<?php if(isset($_GET['time']) && $_GET['time']){?>
				<th>资源名称</th>
				<th>统计</th>
				<?php }else{?>
				<th>资源名称</th>
				<th>用户ID</th>
				<th>时间</th>
				<th>IP</th>
				<th>管理</th>
				<?php }?>
			</tr>
		</thead>
		<tbody>
			<?php
			if($adds) {
				foreach($adds as $value)
				{
					echo "<tr>\n";
					echo "<td><a href='".get_permalink($value->ice_post_id)."' target=_blank>".get_post($value->ice_post_id)->post_title."</a></td>";
					if(isset($_GET['time']) && $_GET['time']){
						echo "<td>$value->ice_total</td>";
					}else{
					echo "<td>".get_user_by("id",$value->ice_user_id)->user_login."</td>\n";
					echo "<td>$value->ice_time</td>\n";
					echo "<td>$value->ice_ip</td>\n";
					echo '<td><a href="javascript:;" class="delorder" data-id="'.$value->ice_id.'">删除</a></td>';
					}
					echo "</tr>";
				}
			}
			else
			{
				echo '<tr><td colspan="5" align="center"><strong>没有记录</strong></td></tr>';
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
<script>
	jQuery(".delorder").click(function(){
		if(confirm('确定删除？')){
			var that = jQuery(this);
			that.text("删除中...");
			jQuery.ajax({
				type: "post",
				url: "<?php echo constant("erphpdown");?>admin/action/order.php",
				data: "do=delfreedown&id=" + jQuery(this).data("id"),
				dataType: "html",
				success: function (data) {
					if(jQuery.trim(data) == '1'){
						that.parent().parent().remove();
					}
				},
				error: function (request) {
					that.text("删除");
					alert("删除失败");
				}
			});
		}
	});
</script>