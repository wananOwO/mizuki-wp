<?php 
if ( !defined('ABSPATH') ) {exit;}
if(!is_user_logged_in())
{
	wp_die('请登录系统');
}
global $wpdb;

if(isset($_POST['delid']) && $_POST['delid']){
	$sql = "delete from $wpdb->erphpcard where id=".esc_sql($_POST['delid']);
	$result=$wpdb->query($sql);
	if(!$result)
	{
		echo "<div id=message><div class='error settings-error'><p>添加失败！</p></div></div>";
	}else{echo "<div id=message><div class='updated settings-error'><p>添加成功！</p></div></div>";}
}


if(isset($_GET['card'])){
	$totals = $wpdb->get_var("SELECT COUNT(id) FROM $wpdb->erphpcard where card='".esc_sql(trim($_GET['card']))."'");
}else{
	$totals = $wpdb->get_var("SELECT COUNT(id) FROM $wpdb->erphpcard");
}
$perpage = 50;
$pages = ceil($totals / $perpage);
$page=isset($_GET['paged']) ?intval($_GET['paged']) :1;
$offset = $perpage*($page-1);
if(isset($_GET['card'])){
	$results = $wpdb->get_results("select * from $wpdb->erphpcard where card='".esc_sql(trim($_GET['card']))."' order by id desc limit $offset,$perpage");
}else{
	$results = $wpdb->get_results("select * from $wpdb->erphpcard order by id desc limit $offset,$perpage");
}
?>
<div class="wrap">
	<h2>充值卡列表</h2>
	<form method="get"><input type="hidden" name="page" value="erphpdown/addon/card/list.php">搜索充值卡号：<input type="text" name="card" value="<?php if(isset($_GET['card'])) echo trim($_GET['card']);?>"><input type="submit" value="查询" class="button"></form><br>
	<table class="wp-list-table widefat fixed striped posts">
        <thead>
        <tr>
        	<th width="3%"><input type="checkbox" id="checkbox" onclick="selectAll()" style='margin-left:0'></th>
			<th>序号</th>
			<th>卡号</th>
			<th>密码</th>
            <th>面值(元)</th>
			<th>使用</th>
			<th>删除</th>
        </tr>
        </thead>
        <tbody>
		<?php 
			if($results){
				foreach($results as $result){
					echo '<tr>';
					echo "<td><input type='checkbox' class='checkbox' value='".$result->id."'></td>";
					echo '<td>'.$result->id.'</td>';
					echo '<td>'.$result->card.'</td>';
					echo '<td>'.$result->password.'</td>';
					echo '<td>'.$result->price.'</td>';
					echo '<td>'.isErphpCardUsed($result->id).'</td>';
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
	<input type="button" value="批量删除所选" class="button-primary delete-more">
</div>
<script type="text/javascript">
	jQuery(".delete-more").click(function(){
		var that = jQuery(this);
		var ids = '';
		jQuery(".checkbox").each(function() {
			if (jQuery(this).is(':checked')) {
		      	ids += ',' + jQuery(this).val();
		  	}
		});
		ids = ids.substring(1);
		if (ids.length == 0) {
			alert('请至少选择一项！');
		} else {
			if (confirm("确定操作？")) {
				that.attr("disabled","disabled").val("处理中...");
				jQuery.ajax({
					type: "post",
					url: "<?php echo ERPHPDOWN_URL;?>/addon/card/do.php",
					data: "do=delcard&ids=" + ids,
					dataType: "html",
					success: function (data) {
						if(data == 'success'){
							alert("操作成功");
							location.reload();
						}
					},
					error: function (request) {
						that.attr("disabled","").val("确认");
						alert("操作失败，请稍后重试！");
					}
				});
			}
		}
		return false;
	});

	function selectAll(){
		if (jQuery('#checkbox').is(':checked')) {
			jQuery(".checkbox").attr("checked", true);
		} else {
			jQuery(".checkbox").attr("checked", false);
		}
	}
</script>