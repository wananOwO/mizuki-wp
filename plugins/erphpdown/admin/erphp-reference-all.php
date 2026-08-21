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
		echo '<div class="error settings-error"><p>用户不存在！</p></div>';
	}
}


if($issearch){
	$total_user  = $wpdb->get_var("SELECT COUNT(ID) FROM $wpdb->users WHERE father_id =".$suid);
}else{
	$total_user  = $wpdb->get_var("SELECT COUNT(ID) FROM $wpdb->users WHERE father_id > 0");
}
$ice_perpage = 20;
$pages = ceil($total_user / $ice_perpage);
$page=isset($_GET['paged']) ?intval($_GET['paged']) :1;
$offset = $ice_perpage*($page-1);
if($issearch){
	$list = $wpdb->get_results("SELECT ID,user_login,user_registered,father_id FROM $wpdb->users where father_id = ".$suid." order by ID desc limit $offset,$ice_perpage");
}else{
	$list = $wpdb->get_results("SELECT ID,user_login,user_registered,father_id FROM $wpdb->users where father_id > 0 order by ID desc limit $offset,$ice_perpage");
}

?>
<div class="wrap">
	<h2>所有推广用户记录</h2>
	<form method="get" action="<?php echo admin_url();?>admin.php">搜索推广人：<input type="text" name="username" placeholder="例如：admin" value="<?php if(isset($_GET['username'])) echo $_GET['username'];?>"><input type="submit" value="查询" class="button"><input type="hidden" name="page" value="erphpdown/admin/erphp-reference-all.php"></form><br>
	<table class="widefat fixed striped posts">
		<thead>
			<tr>
				<th width="20%">用户ID</th>
				<th width="20%">推广人ID</th>
				<th width="20%">注册时间</th>
				<th width="20%">VIP类型</th>	    
				<th width="20%">消费额(<?php echo get_option('ice_name_alipay');?>)</th>	    
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
					$userType= EPD::getUserVipType($value->ID);
					$typeName = '无';
					if($userType==7) $typeName = '包月'.$erphp_month_name;elseif($userType==8) $typeName = '包季'.$erphp_quarter_name;elseif($userType==9) $typeName = '包年'.$erphp_year_name;elseif($userType==10) $typeName = '终身'.$erphp_life_name;elseif($userType==6) $typeName = '体验'.$erphp_day_name;
					echo "<tr>\n";
					echo "<td>".$value->user_login."</td>";
					echo "<td>".get_user_by('id',$value->father_id)->user_login."</td>";
					echo "<td>".get_date_from_gmt($value->user_registered)."</td>";
					echo "<td>$typeName</td>";
					echo "<td>".erphpGetUserAllXiaofei($value->ID)."</td>";
					echo "</tr>";
				}
			}
			else
			{
				echo '<tr><td colspan="5" align="center"><strong>没有推广记录</strong></td></tr>';
			}
			?>
		</tbody>
	</table>
	<?php echo erphp_admin_pagenavi($total_user,$ice_perpage);?> 
	　　
</div>

