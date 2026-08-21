<?php
// +----------------------------------------------------------------------
// | ERPHP [ PHP DEVELOP ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.mobantu.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: mobantu <82708210@qq.com>
// +----------------------------------------------------------------------

date_default_timezone_set('Asia/Shanghai');
if ( !defined('ABSPATH') ) {exit;}

$action=isset($_POST['action']) ?$_POST['action'] :false;
$id=(isset($_POST['id']) && is_numeric($_POST['id'])) ? intval($_POST['id']) :0;
if(!$id){
	$id=(isset($_GET['id']) && is_numeric($_GET['id'])) ? intval($_GET['id']) :0;
}

if($action=="save" && current_user_can('administrator')){
	$result = isset($_POST['result']) && is_numeric($_POST['result']) ?intval($_POST['result']) :0;
	$note   = isset($_POST['note']) ?$_POST['note'] :'';
	$ok=$wpdb->query("update ".$wpdb->iceget." set ice_success=".$result.",ice_note='".$note."',ice_success_time='".date("Y-m-d H:i:s")."' where ice_id=".$id);
	if(!$ok){
		echo "<font color='red'>系统错误，请稍后重试！</font>";
	}
	else {

		echo "<font color='green'>更新成功！</font>";
	}
	unset($id);
}

if(isset($id) && $id && current_user_can('administrator')){
	$info=$wpdb->get_row("select * from ".$wpdb->iceget." where ice_id=".$id);
	if(!$info->ice_id)
	{
		echo "<font color='red'>错误的ID</font>";
		exit;
	}
	$userMoney=$wpdb->get_row("select * from ".$wpdb->iceinfo." where ice_user_id=".$info->ice_user_id);

	$lv=get_option("ice_ali_money_site");
	$ice_ali_money_site = get_user_meta($info->ice_user_id,'ice_ali_money_site',true);
	if($ice_ali_money_site != '' && ($ice_ali_money_site || $ice_ali_money_site == 0)){
		$lv = $ice_ali_money_site;
	}

	?>
	<div class="wrap">
		<form method="post" style="width:70%;float:left;">

			<h2>处理提现申请</h2>
			<table class="form-table">
				<tr>
					<td valign="top" width="30%"><strong>支付宝帐号</strong><br />
					</td>
					<td><?php echo $info->ice_alipay?></td>
				</tr>
				<tr>
					<td valign="top" width="30%"><strong>支付宝姓名</strong><br />
					</td>
					<td><?php echo $info->ice_name?></td>
				</tr>
				<tr>
					<td valign="top" width="30%"><strong>提现<?php echo get_option('ice_name_alipay');?></strong><br />
					</td>
					<td><?php echo $info->ice_money?>
				</td>
			</tr>
			<tr>
				<td valign="top" width="30%"><strong>手续费</strong><br />
				</td>
				<td><?php echo $lv;?> %
				</td>
			</tr>
			<tr>
				<td valign="top" width="30%"><strong>实际转账</strong><br />
				</td>
				<td style="color: red;"><?php echo  ($info->ice_money*(100-$lv)/100) / get_option('ice_proportion_alipay') ?> 元
				</td>
			</tr>
			<tr>
				<td valign="top" width="30%"><strong>处理结果</strong><br />
				</td>
				<td><input type="radio" name="result" id="res1" value="1" <?php if($info->ice_success==1) echo "checked";?>/>已支付 
					<input type="radio" name="result" id="res1" value="0" <?php if($info->ice_success==0) echo "checked";?>/>未处理
				</td>
			</tr>
			<tr>
				<td valign="top" width="30%"><strong>处理时间</strong><br />
				</td>
				<td><?php echo $info->ice_success_time?>
			</td>
		</tr>
		<tr>
			<td valign="top" width="30%"><strong>备注</strong><br />
			</td>
			<td>
				<input type="text" name="note" id="note" value="<?php echo $info->ice_note ?>" />
			</td>
		</tr>
	</table>

	<table> 
		<td><p class="submit">
			<input type="submit" name="Submit" value="处理提现" class="button-primary"/>
			<input type="hidden" name="id" value="<?php echo $id;?>">
			<input type="hidden" name="action" value="save">
		</p>
	</td>

</tr> </table>

</form>
</div>
<?php
exit;
}
//统计数据

$total_trade = $wpdb->get_var("SELECT count(ice_id) FROM $wpdb->iceget");

$ice_perpage = 20;
$pages = ceil($total_trade / $ice_perpage);
$page=isset($_GET['paged']) ?intval($_GET['paged']) :1;
$offset = $ice_perpage*($page-1);
$list        = $wpdb->get_results("SELECT * FROM $wpdb->iceget order by ice_time DESC limit $offset,$ice_perpage");
$lv=get_option("ice_ali_money_site");
?>
<div class="wrap">
	<h2>所有提现列表</h2>
	<table class="widefat striped">
		<thead>
			<tr>
				<th>用户ID</th>
				<th>申请时间</th>
				<th>申请<?php echo get_option('ice_name_alipay');?></th>
				<th>到帐金额</th>
				<th>支付状态</th>
				<th>备注</th>
				<th>管理</th>
			</tr>
		</thead>
		<tbody>
			<?php
			if($list) {
				foreach($list as $value)
				{
					$ice_ali_money_site = get_user_meta($value->ice_user_id,'ice_ali_money_site',true);
					if($ice_ali_money_site != '' && ($ice_ali_money_site || $ice_ali_money_site == 0)){
						$lv = $ice_ali_money_site;
					}

					$result=$value->ice_success==1?'已支付':'--';
					echo "<tr>\n";
					echo "<td>".get_user_by('id',$value->ice_user_id)->user_login."</td>\n";
					echo "<td>$value->ice_time</td>\n";
					echo "<td>$value->ice_money</td>\n";
					echo "<td>".( (100-$lv) * $value->ice_money / 100) / get_option('ice_proportion_alipay')."元</td>\n";
					echo "<td>$result</td>\n";
					echo "<td>$value->ice_note</td>\n";
					echo "<td><a href='".admin_url('admin.php?page=erphpdown/admin/erphp-tixian-list.php&id='.$value->ice_id)."'>操作</a> <a href='javascript:;' class='delorder' data-id='".$value->ice_id."'>删除</a></td>";
					echo "</tr>";
				}
			}
			else
			{
				echo '<tr><td colspan="7" align="center"><strong>没有提现记录</strong></td></tr>';
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
				data: "do=deltx&id=" + jQuery(this).data("id"),
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
