<?php
// +----------------------------------------------------------------------
// | ERPHP [ PHP DEVELOP ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.mobantu.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: mobantu <82708210@qq.com>
// +----------------------------------------------------------------------

if ( !defined('ABSPATH') ) {exit;}

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
	$total_trade   = $wpdb->get_var("SELECT COUNT(id) FROM $wpdb->icelog WHERE user_id=".$suid);
}else{
	$total_trade   = $wpdb->get_var("SELECT COUNT(id) FROM $wpdb->icelog");
}
$ice_perpage = 30;
$pages = ceil($total_trade / $ice_perpage);
$page=isset($_GET['paged']) ?intval($_GET['paged']) :1;
$offset = $ice_perpage*($page-1);

if($issearch == 1){
	$results=$wpdb->get_results("SELECT * FROM $wpdb->icelog where user_id=".$suid." order by ice_time DESC limit $offset,$ice_perpage");
}else{
	$results=$wpdb->get_results("SELECT * FROM $wpdb->icelog order by ice_time DESC limit $offset,$ice_perpage");
}
?>

<div class="wrap">
	<h2>余额明细</h2>
	<div class="tablenav top">
		<form method="get"><input type="hidden" name="page" value="erphpdown/admin/erphp-moneylog-list.php"><input type="text" name="username" placeholder="登录名，例如：admin" value="<?php if($issearch == 1) echo $_GET['username'];?>"><input type="submit" value="查询" class="button"></form>
	</div>
	<table class="widefat fixed striped posts">
		<thead>
			<tr>
				<th>用户ID</th>
				<th><?php echo get_option('ice_name_alipay');?></th>
				<th>来源</th>
				<th>时间</th>
				<th>管理</th>
			</tr>
		</thead>
		<tbody>
			<?php
			if($results) {
				foreach($results as $value)
				{
					echo "<tr>\n";
					if($value->user_id){
						$cu = get_user_by('id',$value->user_id);
						if($cu){
							echo "<td>".$cu->user_login."<span style='font-size:12px;color:#999'>（昵称：".$cu->nickname."）</span></td>";
						}else{
							echo "<td></td>";
						}
					}
					echo "<td>".($value->ice_money>0?'+':'')."$value->ice_money</td>\n";
					echo "<td>$value->ice_note</td>\n";
					echo "<td>".$value->ice_time."</td>";
					echo '<td><a href="javascript:;" class="delorder" data-id="'.$value->id.'">删除</a></td>';
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
	　　		
</div>
<script>
	jQuery(".delorder").click(function(){
		if(confirm('确定删除？')){
			var that = jQuery(this);
			that.text("删除中...");
			jQuery.ajax({
				type: "post",
				url: "<?php echo constant("erphpdown");?>admin/action/order.php",
				data: "do=dellog&id=" + jQuery(this).data("id"),
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