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
if(isset($_GET['type'])){
	$total_trade   = $wpdb->get_var("SELECT COUNT(ice_id) FROM $wpdb->vip where ice_user_type=".esc_sql($_GET['type']));
	$total_success = $wpdb->get_var("SELECT sum(ice_price) FROM $wpdb->vip where ice_user_type=".esc_sql($_GET['type']));
}else{
	if(isset($_GET['username']) && $_GET['username']){
		$user = get_user_by('login',$_GET['username']);
		if($user){
			$suid = $user->ID;
			$issearch = 1;
		}else{
			$suid = 0;
			echo '<div class="error settings-error"><p>用户不存在！</p></div>';
		}
		$total_trade   = $wpdb->get_var("SELECT COUNT(ice_id) FROM $wpdb->vip where ice_user_id=".$suid);
		$total_success = $wpdb->get_var("SELECT sum(ice_price) FROM $wpdb->vip where ice_user_id=".$suid);
	}elseif(isset($_GET['ip']) && $_GET['ip']){
		$issearch = 2;
		$total_trade   = $wpdb->get_var("SELECT COUNT(ice_id) FROM $wpdb->vip where ice_ip='".$_GET['ip']."'");
		$total_success = $wpdb->get_var("SELECT sum(ice_price) FROM $wpdb->vip where ice_ip='".$_GET['ip']."'");
	}else{
		$total_trade   = $wpdb->get_var("SELECT COUNT(ice_id) FROM $wpdb->vip");
		$total_success = $wpdb->get_var("SELECT sum(ice_price) FROM $wpdb->vip");
	}
}

$ice_perpage = 20;
$pages = ceil($total_trade / $ice_perpage);
$page=isset($_GET['paged']) ?intval($_GET['paged']) :1;
$offset = $ice_perpage*($page-1);

if(isset($_GET['type'])){
	$list = $wpdb->get_results("SELECT * FROM $wpdb->vip where ice_user_type=".esc_sql($_GET['type'])." order by ice_time DESC limit $offset,$ice_perpage");
}else{
	if($issearch==1){
		$list = $wpdb->get_results("SELECT * FROM $wpdb->vip where ice_user_id=".$suid." order by ice_time DESC limit $offset,$ice_perpage");
	}elseif($issearch==2){
		$list = $wpdb->get_results("SELECT * FROM $wpdb->vip where ice_ip='".$_GET['ip']."' order by ice_time DESC limit $offset,$ice_perpage");
	}else{
		$list = $wpdb->get_results("SELECT * FROM $wpdb->vip order by ice_time DESC limit $offset,$ice_perpage");
	}
}
?>
<div class="wrap">
	<h2>VIP订单</h2>
	<p><?php printf(('共有<strong>%s</strong>笔交易，总金额：<strong>%s</strong>'), $total_trade, $total_success); ?></p>
	<form method="get"><input type="hidden" name="page" value="erphpdown/admin/erphp-vip-items.php"><input type="text" name="username" placeholder="用户名，例如：admin" value="<?php if($issearch == 1) echo $_GET['username'];?>"><input type="text" name="ip" placeholder="IP，例如：127.0.0.1" value="<?php if($issearch==2) echo $_GET['ip'];?>"><input type="submit" value="查询" class="button"></form>
	<p>筛选：
		<a href="<?php echo admin_url();?>admin.php?page=erphpdown/admin/erphp-vip-items.php">全部</a>&nbsp;&nbsp;
		<a href="<?php echo admin_url();?>admin.php?page=erphpdown/admin/erphp-vip-items.php&type=6">体验VIP</a>&nbsp;&nbsp;
		<a href="<?php echo admin_url();?>admin.php?page=erphpdown/admin/erphp-vip-items.php&type=7">包月VIP</a>&nbsp;&nbsp;
		<a href="<?php echo admin_url();?>admin.php?page=erphpdown/admin/erphp-vip-items.php&type=8">包季VIP</a>&nbsp;&nbsp;
		<a href="<?php echo admin_url();?>admin.php?page=erphpdown/admin/erphp-vip-items.php&type=9">包年VIP</a>&nbsp;&nbsp;
		<a href="<?php echo admin_url();?>admin.php?page=erphpdown/admin/erphp-vip-items.php&type=10">终身VIP</a>
	</p>
	<table class="widefat fixed striped posts">
		<thead>
			<tr>
				<th>用户ID</th>
				<th>VIP类型</th>
				<th><?php echo get_option('ice_name_alipay');?></th>
				<th>交易时间</th>	
				<th>管理</th>			
			</tr>
		</thead>
		<tbody>
			<?php
			if($list) {
				$erphp_life_name    = get_option('erphp_life_name')?'('.get_option('erphp_life_name').')':'';
				$erphp_year_name    = get_option('erphp_year_name')?'('.get_option('erphp_year_name').')':'';
				$erphp_quarter_name = get_option('erphp_quarter_name')?'('.get_option('erphp_quarter_name').')':'';
				$erphp_month_name  = get_option('erphp_month_name')?'('.get_option('erphp_month_name').')':'';
				$erphp_day_name  = get_option('erphp_day_name')?'('.get_option('erphp_day_name').')':'';
				foreach($list as $value)
				{
					if($value->ice_user_type == 6) $typeName = '体验'.$erphp_day_name;
					else {$typeName=$value->ice_user_type==7 ?'包月'.$erphp_month_name :($value->ice_user_type==8 ?'包季'.$erphp_quarter_name : ($value->ice_user_type==10 ?'终身'.$erphp_life_name : '包年'.$erphp_year_name));}

					echo "<tr>\n";
					echo "<td>";
					if($value->ice_user_id){
						echo get_the_author_meta( 'user_login', $value->ice_user_id );
					}else{
						echo '游客IP:'.$value->ice_ip;
					}
					echo "</td>\n";
					echo "<td>$typeName</td>\n";
					echo "<td>$value->ice_price</td>\n";
					echo "<td>$value->ice_time</td>\n";
					echo '<td><a href="javascript:;" class="delorder" data-id="'.$value->ice_id.'">删除</a></td>';
					echo "</tr>";
				}
			}
			else
			{
				echo '<tr><td colspan="4" align="center"><strong>没有交易记录</strong></td></tr>';
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
				data: "do=delviporder&id=" + jQuery(this).data("id"),
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
