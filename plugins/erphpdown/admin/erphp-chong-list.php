<?php
// +----------------------------------------------------------------------
// | ERPHP [ PHP DEVELOP ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.mobantu.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: mobantu <82708210@qq.com>
// +----------------------------------------------------------------------

if ( !defined('ABSPATH') ) {exit;}
$ice_proportion_alipay = get_option('ice_proportion_alipay');
$where = '1=1';

if(isset($_GET['type'])){
	if($_GET['type'] == 'all'){
		
	}elseif($_GET['type'] == 'unpaid'){
		$where .= ' and ice_success=0';
	}else{
		$where .= ' and ice_success=1';
	}
}else{
	$where .= ' and ice_success=1';
}

if(isset($_GET['username']) && $_GET['username']){
	$user = get_user_by('login',$_GET['username']);
	if($user){
		$suid = $user->ID;
		$where .= ' and ice_user_id='.$suid;
	}else{
		$suid = 0;
		echo '<div class="error settings-error"><p>用户不存在！</p></div>';
	}
}
if(isset($_GET['order']) && $_GET['order']){
	$ice_num = $_GET['order'];
	$where .= " and ice_num='".$ice_num."'";
}
if(isset($_GET['payment']) && $_GET['payment']){
	if($_GET['payment'] == 'card'){
		$where .= ' and ice_note=6';
	}elseif($_GET['payment'] == 'dashbord'){
		$where .= ' and ice_note=1';
	}elseif($_GET['payment'] == 'online'){
		$where .= ' and ice_note=0';
	}elseif($_GET['payment'] == 'mycred'){
		$where .= ' and ice_note=4';
	}
}
if(isset($_GET['date_start']) && $_GET['date_start']){
	$where .= " and unix_timestamp(ice_time) >= unix_timestamp('".$_GET['date_start']."')";
}
if(isset($_GET['date_over']) && $_GET['date_over']){
	$where .= " and unix_timestamp(ice_time) <= unix_timestamp('".$_GET['date_over']."')";
}

$total_trade   = $wpdb->get_var("SELECT COUNT(ice_id) FROM $wpdb->icemoney WHERE ".$where);
$ice_perpage = 30;
$pages = ceil($total_trade / $ice_perpage);
$page=isset($_GET['paged']) ?intval($_GET['paged']) :1;
$offset = $ice_perpage*($page-1);
$results=$wpdb->get_results("SELECT * FROM $wpdb->icemoney where ".$where." order by ice_time DESC limit $offset,$ice_perpage");
?>

<div class="wrap">
	<h2>充值记录 <a href="admin.php?page=erphpdown%2Fadmin%2Ferphp-clear.php" style="font-size:14px;text-decoration: none;">清理数据表</a></h2>
	<p>所有直接支付购买文章、直接支付购买VIP、充值的支付订单都会先汇总于此，然后非充值订单会额外单独统计比如购买VIP订单、购买文章订单</p>
	<ul class="subsubsub">
		<li class="all"><a href="admin.php?page=erphpdown/admin/erphp-chong-list.php&amp;type=all" class="<?php if(isset($_GET['type']) && $_GET['type'] == 'all') echo 'current';?>">全部</a> |</li>
		<li class="mine"><a href="admin.php?page=erphpdown/admin/erphp-chong-list.php" class="<?php if(!isset($_GET['type']) || (isset($_GET['type']) && $_GET['type'] == '')) echo 'current';?>">已支付</a> |</li>
		<li class="mine"><a href="admin.php?page=erphpdown/admin/erphp-chong-list.php&amp;type=unpaid" class="<?php if(isset($_GET['type']) && $_GET['type'] == 'unpaid') echo 'current';?>">未支付</a></li>
	</ul>
	<div class="tablenav top">
		<form method="get"><input type="hidden" name="page" value="erphpdown/admin/erphp-chong-list.php"><input type="hidden" name="type" value="<?php if(isset($_GET['type'])) echo $_GET['type'];?>"><input type="text" name="username" style="float: left;" placeholder="登录名，例如：admin" value="<?php if(isset($_GET['username']) && $_GET['username']) echo $_GET['username'];?>"><input type="text" name="order" style="float: left;" placeholder="订单号" value="<?php if(isset($_GET['order']) && $_GET['order']) echo $_GET['order'];?>"><input type="date" name="date_start" style="float: left;" placeholder="开始日期" value="<?php if(isset($_GET['date_start']) && $_GET['date_start']) echo $_GET['date_start'];?>"><input type="date" name="date_over" style="float: left;" placeholder="结束日期" value="<?php if(isset($_GET['date_over']) && $_GET['date_over']) echo $_GET['date_over'];?>"><select name="payment" style="float: left;">
				<option value="">支付方式</option>
				<option value="online"<?php if(isset($_GET['payment']) && $_GET['payment'] == 'online') echo ' selected';?>>在线支付</option>
				<option value="card"<?php if(isset($_GET['payment']) && $_GET['payment'] == 'card') echo ' selected';?>>充值卡</option>
				<option value="dashbord"<?php if(isset($_GET['payment']) && $_GET['payment'] == 'dashbord') echo ' selected';?>>后台充值</option>
				<option value="mycred"<?php if(isset($_GET['payment']) && $_GET['payment'] == 'mycred') echo ' selected';?>>Mycred兑换</option>
			</select><input type="submit" value="查询" class="button"></form>
	</div>
	<table class="widefat fixed striped posts">
		<thead>
			<tr>
				<th>用户ID</th>
				<th>订单号</th>
				<th>充值<?php echo get_option('ice_name_alipay');?></th>
				<th>得到<?php echo get_option('ice_name_alipay');?></th>
				<th>来源</th>
				<th>方式</th>
				<th>时间</th>
				<th>状态</th>
				<th>管理</th>
			</tr>
		</thead>
		<tbody>
			<?php
			if($results) {
				$erphp_life_name    = get_option('erphp_life_name')?'('.get_option('erphp_life_name').')':'';
				$erphp_year_name    = get_option('erphp_year_name')?'('.get_option('erphp_year_name').')':'';
				$erphp_quarter_name = get_option('erphp_quarter_name')?'('.get_option('erphp_quarter_name').')':'';
				$erphp_month_name  = get_option('erphp_month_name')?'('.get_option('erphp_month_name').')':'';
				$erphp_day_name  = get_option('erphp_day_name')?'('.get_option('erphp_day_name').')':'';
				foreach($results as $value)
				{
					echo "<tr>\n";
					if($value->ice_user_id){
						$cu = get_user_by('id',$value->ice_user_id);
						if($cu){
							echo "<td>".$cu->user_login."<span style='font-size:12px;color:#999'>（昵称：".$cu->nickname."）</span></td>";
						}else{
							echo "<td></td>";
						}
					}else{
						echo "<td>游客<span style='font-size:12px;color:#999'>（IP：".$value->ice_ip."）</span></td>";
					}
					echo "<td>$value->ice_num</td>\n";
					if($value->ice_success == 0){
						echo "<td>".$value->ice_money*$ice_proportion_alipay."（".$value->ice_money."元）</td>\n";
						echo "<td>—</td>";
					}else{
						echo "<td>$value->ice_money</td>\n";
						if(isset($value->ice_money_real) && $value->ice_money_real){
							echo "<td>$value->ice_money_real</td>\n";
						}else{
							echo "<td>$value->ice_money</td>\n";
						}
					}
					if($value->ice_post_id){
						echo "<td><a target=_blank href='".get_permalink($value->ice_post_id)."'>".get_post($value->ice_post_id)->post_title."</a></td>\n";
					}elseif($value->ice_user_type){
						if($value->ice_user_type == 6) $typeName = '体验VIP'.$erphp_day_name;
						else {$typeName=$value->ice_user_type==7 ?'包月VIP'.$erphp_month_name :($value->ice_user_type==8 ?'包季VIP'.$erphp_quarter_name : ($value->ice_user_type==10 ?'终身VIP'.$erphp_life_name : '包年VIP'.$erphp_year_name));}
						echo "<td>$typeName</td>\n";
					}else{
						echo "<td>直接充值</td>\n";
					}

					if(intval($value->ice_note)==0)
					{
						if($value->ice_success){
							if($value->ice_alipay){
								echo "<td><font color=green>".$value->ice_alipay."</font></td>\n";
							}else{
								echo "<td><font color=green>在线支付</font></td>\n";
							}
						}else{
							if($value->ice_alipay){
								echo "<td>".$value->ice_alipay."</td>\n";
							}else{
								echo "<td>在线支付</td>\n";
							}
						}
					}elseif(intval($value->ice_note)==1)
					{
						echo "<td>后台充值</td>\n";
					}
					elseif(intval($value->ice_note)==4)
					{
						echo "<td><font color=orange>mycred兑换</font></td>\n";
					}
					elseif(intval($value->ice_note)==6)
					{
						echo "<td><font color=orange>充值卡</font></td>\n";
					}
					echo "<td>".$value->ice_time."</td>";
					echo "<td>".($value->ice_success?'<font color=green>已支付</font>':'未支付')."</td>";
					echo '<td><a href="javascript:;" class="delorder" data-id="'.$value->ice_id.'">删除</a>';
					if($value->ice_success == 0){
						echo '&nbsp;&nbsp;<a href="javascript:;" class="yesorder" data-id="'.$value->ice_num.'" style="color:green">补单</a>';
					}
					echo '</td>';
					echo "</tr>";
				}
			}
			else
			{
				echo '<tr><td colspan="9" align="center"><strong>没有记录</strong></td></tr>';
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
				data: "do=delchong&id=" + jQuery(this).data("id"),
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

	jQuery(".yesorder").click(function(){
		if(confirm('确定补单？')){
			var that = jQuery(this);
			that.text("补单中...");
			jQuery.ajax({
				type: "post",
				url: "<?php echo constant("erphpdown");?>admin/action/order.php",
				data: "do=yeschong&id=" + jQuery(this).data("id"),
				dataType: "html",
				success: function (data) {
					if(jQuery.trim(data) == '1'){
						that.text("处理成功");
					}
				},
				error: function (request) {
					that.text("补单");
					alert("补单失败");
				}
			});
		}
	});
</script>