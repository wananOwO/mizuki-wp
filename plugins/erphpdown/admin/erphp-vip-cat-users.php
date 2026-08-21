<?php
// +----------------------------------------------------------------------
// | ERPHP [ PHP DEVELOP ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.mobantu.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: mobantu <82708210@qq.com>
// +----------------------------------------------------------------------
if ( !defined('ABSPATH') ) {exit;}
$user_info=wp_get_current_user();
$issearch = 0;
if(current_user_can('administrator')){
	if(isset($_POST['action'])){
		$action = $_POST['action'];
		if($action == 1){
			$result = $wpdb->query("update $wpdb->icecat set endTime = adddate(endTime,interval ".esc_sql($_POST['adddate'])." day) where userType > 0");
			if($result){
				echo '<div class="updated settings-error"><p>批量延长（减少）VIP天数成功！</p></div>';
			}else{
				echo '<div class="error settings-error"><p>操作失败！</p></div>';
			}
		}elseif($action == 2){
			$user = get_user_by('login',$_POST['username']);
			if($user){
				$suid = $user->ID;
				$issearch = 1;
			}else{
				echo '<div class="error settings-error"><p>用户不存在！</p></div>';
			}
		}
	}

	if(isset($_GET['type'])){
		$total_trade  = $wpdb->get_var("select count(ice_id) from  ".$wpdb->icecat." where userType ='".esc_sql($_GET['type'])."'");
	}else{
		if($issearch){
			$total_trade  = $wpdb->get_var("select count(ice_id) from  ".$wpdb->icecat." where userType > 0 and ice_user_id=".$suid);
		}else{
			$total_trade  = $wpdb->get_var("select count(ice_id) from  ".$wpdb->icecat." where userType > 0");
		}
	}

	$ice_perpage = 20;
	$pages = ceil($total_trade / $ice_perpage);
	$page=isset($_GET['paged']) ?intval($_GET['paged']) :1;
	$offset = $ice_perpage*($page-1);
	if(isset($_GET['type'])){
		$list = $wpdb->get_results("select * from  ".$wpdb->icecat." where userType = '".esc_sql($_GET['type'])."' order by ice_id DESC limit $offset,$ice_perpage");
	}else{
		if($issearch){
			$list = $wpdb->get_results("select * from  ".$wpdb->icecat." where userType > 0 and ice_user_id=".$suid." order by ice_id DESC limit $offset,$ice_perpage");
		}else{
			$list = $wpdb->get_results("select * from  ".$wpdb->icecat." where userType > 0 order by ice_id DESC limit $offset,$ice_perpage");
		}
	}
	?>
	<div class="wrap">
		<h2>分类VIP用户<?php if($issearch) echo '（当前查询用户：'.$_POST['username'].'）';?></h2>
		<p><?php echo '共有<strong>'.$total_trade.'</strong>个VIP用户'; ?></p>
		<div>
			<h3>批量操作</h3>
			<form method="post" onsubmit="return confirm('确定处理所有VIP用户的到期时间？请确保输入的信息无误，否则操作后耶稣都挽救不了！');">给所有VIP都延长（减少）<input type="number" name="adddate" placeholder="整数天数"> 天的VIP权限 <input type="submit" value="确定操作" class="button"><input type="hidden" name="action" value="1"> （输入负数表示减少天数）</form><br>
			<h3>单用户查询</h3>
			<form method="post">搜索用户：<input type="text" name="username" placeholder="例如：admin"><input type="submit" value="查询" class="button"><input type="hidden" name="action" value="2"></form>

			<p>筛选：
				<a href="<?php echo admin_url();?>admin.php?page=erphpdown/admin/erphp-vip-cat-users.php">全部</a>&nbsp;&nbsp;
				<a href="<?php echo admin_url();?>admin.php?page=erphpdown/admin/erphp-vip-cat-users.php&type=6">体验VIP</a>&nbsp;&nbsp;
				<a href="<?php echo admin_url();?>admin.php?page=erphpdown/admin/erphp-vip-cat-users.php&type=7">包月VIP</a>&nbsp;&nbsp;
				<a href="<?php echo admin_url();?>admin.php?page=erphpdown/admin/erphp-vip-cat-users.php&type=8">包季VIP</a>&nbsp;&nbsp;
				<a href="<?php echo admin_url();?>admin.php?page=erphpdown/admin/erphp-vip-cat-users.php&type=9">包年VIP</a>&nbsp;&nbsp;
				<a href="<?php echo admin_url();?>admin.php?page=erphpdown/admin/erphp-vip-cat-users.php&type=10">终身VIP</a>
			</p>
		</div>
		<table class="widefat fixed striped posts">
			<thead>
				<tr>
					<th>用户ID</th>
					<th>VIP类型</th>
					<th>分类</th>
					<th>到期时间</th>	
					<th>操作</th>				
				</tr>
			</thead>
			<tbody>
				<?php
				if($list) {
					foreach($list as $value){
						if($value->userType == 6) $typeName = '体验';
						else {$typeName=$value->userType==7 ?'包月' :($value->userType==8 ?'包季' : ($value->userType==10 ?'终身' : '包年'));}
						echo "<tr class=\"vip-$value->ice_id\">\n";
						echo "<td>".get_the_author_meta( 'user_login', $value->ice_user_id )."</td>\n";
						echo "<td>$typeName</td>\n";
						echo "<td>".get_category($value->ice_cat_id)->name."</td>\n";
						if($value->userType==10 && !get_option('erphp_life_days')){
							echo '<td>终身</td>';
						}else{
							echo "<td><input type=text name=p_price_$value->ice_id id=p_price_$value->ice_id value=$value->endTime style=\"width:120px;\" /><input type=button id=editpricebtn_$value->ice_id onclick=editPrice($value->ice_id) value=修改 class=button></td>";
						}
						echo '<td><a href="javascript:;" class="delvip" data-id="'.$value->ice_user_id.'" onclick="delvip('.$value->ice_id.','.$value->ice_user_id.','.$value->ice_cat_id.')">删除VIP权限</a></td>';
						echo "</tr>";
					}
				}else{
					echo '<tr><td colspan="5" align="center"><strong>没有记录</strong></td></tr>';
				}
				?>
			</tbody>
		</table>
		<?php echo erphp_admin_pagenavi($total_trade,$ice_perpage);?>
	</div>
	<script type="text/javascript">
		function delvip(id,uid,cid){
			if(confirm('确认删除VIP权限?')){
				jQuery.ajax({
					type: "post",
					url: "<?php echo constant("erphpdown");?>admin/action/vip.php",
					data: "do=delcat&uid=" + uid+'&cid='+cid+'&id='+id,
					date:"",
					dataType: "html",
					success: function (data) {
						if(data == 'success'){
							jQuery('.vip-'+id).remove();
						}
					},
					error: function (request) {
								
						//alert("修改失败");
					}
				});
			}
		}

		function editPrice(id){
			jQuery("#editpricebtn_"+id).val("修改中..");
			jQuery.ajax({
				type: "post",
				url: "<?php echo constant("erphpdown");?>admin/action/vip.php",
				data: "do=editcat&id=" + id + "&new_date=" + jQuery("#p_price_"+id).val(),
				date:"",
				dataType: "html",
				success: function (data) {
					if(data == 'success'){
						jQuery("#editpricebtn_"+id).val("修改成功");
					}
				},
				error: function (request) {
					jQuery("#editpricebtn_"+id).val("修改");
					alert("修改失败");
				}
			});
		}

	</script>
<?php }?>
