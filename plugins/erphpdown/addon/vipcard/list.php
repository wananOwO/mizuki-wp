<?php 
if ( !defined('ABSPATH') ) {exit;}
if(!is_user_logged_in())
{
	wp_die('请登录系统');
}

global $wpdb;

if(isset($_POST['delid']) && $_POST['delid']){
	$sql = "delete from $wpdb->erphpvipcard where id=".$_POST['delid'];
	$result=$wpdb->query($sql);
	if(!$result)
	{
		echo "<div id=message><div class='error settings-error'><p>添加失败！</p></div></div>";
	}else{echo "<div id=message><div class='updated settings-error'><p>添加成功！</p></div></div>";}
}

if(isset($_GET['type']) && $_GET['type']){
	$totals = $wpdb->get_var("SELECT COUNT(id) FROM $wpdb->erphpvipcard where usertype='".esc_sql($_GET['type'])."'");
}elseif(isset($_GET['card']) && $_GET['card']){
	$totals = $wpdb->get_var("SELECT COUNT(id) FROM $wpdb->erphpvipcard where card='".$_GET['card']."'");
}else{
	$totals = $wpdb->get_var("SELECT COUNT(id) FROM $wpdb->erphpvipcard");
}
$perpage = 50;
$pages = ceil($totals / $perpage);
$page=isset($_GET['paged']) ?intval($_GET['paged']) :1;
$offset = $perpage*($page-1);
if(isset($_GET['type']) && $_GET['type']){
	$results = $wpdb->get_results("select * from $wpdb->erphpvipcard where usertype='".esc_sql($_GET['type'])."' order by id desc limit $offset,$perpage");
}elseif(isset($_GET['card']) && $_GET['card']){
	$results = $wpdb->get_results("select * from $wpdb->erphpvipcard where card='".$_GET['card']."' order by id desc limit $offset,$perpage");
}else{
	$results = $wpdb->get_results("select * from $wpdb->erphpvipcard order by id desc limit $offset,$perpage");
}



?>
<div class="wrap">
	<h2>VIP充值卡列表</h2>
	<p>剩余体验VIP：<?php echo getVipCardTypeLeft(6);?>&nbsp;&nbsp;&nbsp;&nbsp;剩余包月VIP：<?php echo getVipCardTypeLeft(7);?>&nbsp;&nbsp;&nbsp;&nbsp;剩余包季VIP：<?php echo getVipCardTypeLeft(8);?>&nbsp;&nbsp;&nbsp;&nbsp;剩余包年VIP：<?php echo getVipCardTypeLeft(9);?>&nbsp;&nbsp;&nbsp;&nbsp;剩余终身VIP：<?php echo getVipCardTypeLeft(10);?></p>
	<p>VIP筛选：
		<a href="<?php echo admin_url();?>admin.php?page=erphpdown/addon/vipcard/list.php&type=6">体验VIP</a>&nbsp;&nbsp;
		<a href="<?php echo admin_url();?>admin.php?page=erphpdown/addon/vipcard/list.php&type=7">包月VIP</a>&nbsp;&nbsp;
		<a href="<?php echo admin_url();?>admin.php?page=erphpdown/addon/vipcard/list.php&type=8">包季VIP</a>&nbsp;&nbsp;
		<a href="<?php echo admin_url();?>admin.php?page=erphpdown/addon/vipcard/list.php&type=9">包年VIP</a>&nbsp;&nbsp;
		<a href="<?php echo admin_url();?>admin.php?page=erphpdown/addon/vipcard/list.php&type=10">终身VIP</a>
	</p>
	<form method="get" action="<?php echo admin_url();?>admin.php"><input type="hidden" name="page" value="erphpdown/addon/vipcard/list.php"><input type="text" name="card" placeholder="激活码卡号" value="<?php if(isset($_GET['card'])) echo $_GET['card'];?>"><button type="submit" class="button button">查询</button></form><br>
	<table class="wp-list-table widefat fixed posts">
            <thead>
            <tr>
				<th width="3%"><input type="checkbox" id="checkbox" onclick="selectAll()" style='margin-left:0'></th>
				<th width="22%">激活码卡号</th>
                <th width="10%">VIP类型</th>
                <th width="10%">发布时间</th>
                <th width="10%">过期时间</th>
				<th width="32%">使用</th>
				<th width="8%">删除</th>
            </tr>
            </thead>
            <tbody>
			<?php 
				if($results){
					foreach($results as $result){
						$typeid = $result->usertype;
						if($typeid == '6'){
							$usertype = '体验VIP';
						}elseif($typeid == '7'){
							$usertype = '包月VIP';
						}elseif($typeid == '8'){
							$usertype = '包季VIP';
						}elseif($typeid == '9'){
							$usertype = '包年VIP';
						}elseif($typeid == '10'){
							$usertype = '终身VIP';
						}
						echo '<tr>';
						echo '<td>&nbsp;&nbsp;<input type="checkbox" class="checkbox" value="'.$result->id.'"></td>';
						echo '<td>'.$result->card.'</td>';
						echo '<td>'.$usertype.'</td>';
						echo '<td>'.$result->createtime.'</td>';
						echo '<td>'.(($result->endtime == '0000-00-00 00:00:00')?'无限期':$result->endtime).'</td>';
						echo '<td>'.isErphpVipCardUsed($result->id).'</td>';
						echo "<td>";
						echo "<form method=post ><input type=hidden id=delid name=delid value=".$result->id."><input type=submit value=删除 class=button onclick=\"return confirm('确认删除?');\"></form>";
						echo "</td>";
						echo '</tr>';
					}
				}
			?>
			</tbody>
	</table>
	<?php erphp_admin_pagenavi($totals,$perpage);?>
	<p>
		<button type="button" class="delselects button-primary">批量删除所选</button>
	</p>
</div>
<script>
	function selectAll(){
		if (jQuery('#checkbox').is(':checked')) {
			jQuery(".checkbox").attr("checked", true);
		} else {
			jQuery(".checkbox").attr("checked", false);
		}

	}

	jQuery(".delselects").click(function(){
		var that = jQuery(this);
		var ids = '';
		jQuery(".checkbox").each(function() {
			if (jQuery(this).is(':checked')) {
		      ids += ',' + jQuery(this).val(); //逐个获取id
		  }
		});
		ids = ids.substring(1); // 对id进行处理，去除第一个逗号
		if (ids.length == 0) {
			alert('请至少选择一项！');
		} else {
			if (confirm("确定操作？")) {
				that.attr("disabled","disabled").text("处理中...");
				jQuery.ajax({
					type: "post",
					url: "<?php echo ERPHPDOWN_URL;?>/addon/vipcard/do.php",
					data: "do=dels&ids=" + ids,
					dataType: "html",
					success: function (data) {
						if(data == 'success'){
							that.removeAttr("disabled","").text("操作成功");
							location.reload();
						}
					},
					error: function (request) {
						that.removeAttr("disabled","").text("删除所选项");
						alert("操作失败，请稍后重试！");
					}
				});
			}
		}
		return false;
	});
</script>