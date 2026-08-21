<?php
// +----------------------------------------------------------------------
// | ERPHP [ PHP DEVELOP ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.mobantu.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: mobantu <82708210@qq.com>
// +----------------------------------------------------------------------
if ( !defined('ABSPATH') ) {exit;}
$total_trade   = $wpdb->get_var("SELECT COUNT(ice_id) FROM $wpdb->iceindex");
$total_money   = $wpdb->get_var("SELECT SUM(ice_price) FROM $wpdb->iceindex");
$ice_perpage = 20;
$pages = ceil($total_trade / $ice_perpage);
$page=isset($_GET['paged']) ?intval($_GET['paged']) :1;
$offset = $ice_perpage*($page-1);
$list = $wpdb->get_results("SELECT * FROM $wpdb->iceindex order by ice_time DESC limit $offset,$ice_perpage");
?>
<div class="wrap">
	<h2>附加购买隐藏内容统计</h2>
	<p><?php printf(('共<strong>%s</strong>.'), $total_money); ?></p>
	<table class="widefat fixed striped posts">
		<thead>
			<tr>
				<th>用户ID</th>
				<th>订单号</th>
				<th>商品名称</th>
				<th>index</th>
				<th><?php echo get_option('ice_name_alipay');?></th>
				<th>交易时间</th>	
				<th>管理</th>
			</tr>
		</thead>
		<tbody>
			<?php
			if($list) {
				foreach($list as $value)
				{
					echo "<tr>\n";
					echo "<td>".get_the_author_meta( 'user_login', $value->ice_user_id )."</td>";
					echo "<td>$value->ice_num</td>";
					echo "<td><a target=_blank href='".get_permalink($value->ice_post)."'>".get_post($value->ice_post)->post_title."</a></td>\n";
					echo "<td>$value->ice_index</td>\n";
					echo "<td>$value->ice_price</td>\n";
					echo "<td>$value->ice_time</td>\n";
					echo '<td><a href="javascript:;" class="delorder" data-id="'.$value->ice_id.'">删除</a></td>';
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
<script>
	jQuery(".delorder").click(function(){
		if(confirm('确定删除？')){
			var that = jQuery(this);
			that.text("删除中...");
			jQuery.ajax({
				type: "post",
				url: "<?php echo constant("erphpdown");?>admin/action/order.php",
				data: "do=delindex&id=" + jQuery(this).data("id"),
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
