<?php 
// +----------------------------------------------------------------------
// | ERPHP [ PHP DEVELOP ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.mobantu.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: mobantu <82708210@qq.com>
// +----------------------------------------------------------------------
if ( !defined('ABSPATH') ) exit; ?>
<div class="wrap">
<?php
if(isset($_POST['Submit']) && current_user_can('administrator'))
{
	//check
	$user_info=get_user_by('login', $_POST['username']);
	if($user_info){
		$uid=$user_info->ID;
		if($_POST['pid'] ){
			$pp = get_post($_POST['pid']);
			if($pp){
			    $subject   = $pp->post_title;
				$postUserId=$pp->post_author;
				$result=erphpAddDownloadByUid($subject, $_POST['pid'], $uid, $_POST['pprice'],1, '', $postUserId);
				if($result){
					$down_activation = get_post_meta($_POST['pid'], 'down_activation', true);
					if($down_activation && function_exists('doErphpAct')){
						$activation_num = doErphpAct($user_info->ID,$_POST['pid']);
						$wpdb->query("update $wpdb->icealipay set ice_data = '".$activation_num."' where ice_url='".$result."'");
						if($user_info->user_email){
							wp_mail($user_info->user_email, '【'.$subject.'】激活码', '您购买的资源【'.$subject.'】激活码：'.$activation_num);
						}
					}
					echo '<div class="updated settings-error"><p>赠送成功！</p></div>';
				}else{
					echo '<div class="error settings-error"><p>赠送失败！</p></div>';
				}
			}else{
				echo '<div class="error settings-error"><p>文章不存在！</p></div>';
			}
		}
	}else{
		echo '<div class="error settings-error"><p>用户不存在！</p></div>';
	}
	
}


?>
<form method="post" action="<?php echo admin_url('admin.php?page='.plugin_basename(__FILE__)); ?>">

	<h2>赠送购买</h2>
	<table class="form-table">
		
		<tr>
			<td valign="top" width="30%"><strong>文章ID</strong><br />
			</td>
			<td><input type="number" step="1" min="1" name="pid" required="" class="erphpdown-select-postid" placeholder="文章ID">
			</td>
		</tr>
		<tr>
			<td valign="top" width="30%"><strong>文章价格</strong><br />
			</td>
			<td><input type="number" name="pprice" value="0" required="">
			</td>
		</tr>
        <tr>
			<td valign="top" width="30%"><strong>被赠送用户名</strong><br />
			</td>
			<td><input type="text" name="username" required="" placeholder="用户名">
			</td>
		</tr>
		<tr>
			<td colspan="2"><input type="submit" name="Submit" value="确认赠送"
				onclick="return confirm('确认赠送?')" class="button-primary" />
			</td>
		</tr>
	</table>
</form>
</div>