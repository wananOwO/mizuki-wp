<?php
// +----------------------------------------------------------------------
// | ERPHP [ PHP DEVELOP ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.mobantu.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: mobantu <82708210@qq.com>
// +----------------------------------------------------------------------
if ( !defined('ABSPATH') ) {exit;}
?>
<?php 
global $wpdb, $wppay_table_name;
if(isset($_POST['action'])){
	if($_POST['action'] == 1){
		$wpdb->query("delete from $wpdb->icemoney WHERE ice_success = 0 and ice_time < DATE_SUB(CURDATE(), INTERVAL 1 WEEK)");
		$wpdb->query("delete from $wppay_table_name WHERE order_status = 0 and order_time < DATE_SUB(CURDATE(), INTERVAL 1 WEEK)");
		echo'<div class="updated settings-error"><p>清理成功！</p></div>';
	}elseif($_POST['action'] == 2){
		$wpdb->query("delete from $wpdb->down WHERE ice_time < DATE_SUB(CURDATE(), INTERVAL 1 WEEK)");
		echo'<div class="updated settings-error"><p>清理成功！</p></div>';
	}elseif($_POST['action'] == 3){
		$wpdb->query("truncate table $wpdb->icemoney");
		echo'<div class="updated settings-error"><p>清空成功！</p></div>';
	}elseif($_POST['action'] == 4){
		$wpdb->query("truncate table $wpdb->icealipay");
		$wpdb->query("truncate table $wppay_table_name");
		echo'<div class="updated settings-error"><p>清空成功！</p></div>';
	}elseif($_POST['action'] == 5){
		$wpdb->query("truncate table $wpdb->vip");
		echo'<div class="updated settings-error"><p>清空成功！</p></div>';
	}elseif($_POST['action'] == 6){
		$wpdb->query("truncate table $wpdb->icelog");
		echo'<div class="updated settings-error"><p>清空成功！</p></div>';
	}
}
?>
<div class="wrap">
	<h1>清理数据表</h1>
	<p>清理数据表冗余数据可有效减轻数据库负担，对网站已知统计数据无影响。为确保万无一失，请在清理前备份下数据库。</p>
	<form action="" method="post">
		<input type="hidden" name="action" value="1"  />
		<input type="submit" value="清理 1 周之前所有未支付的订单" class="button-primary">（随着时间推移网站未完成的充值订单记录会越来越多，可清理）
	</form>
	<br><br>
	<form action="" method="post">
		<input type="hidden" name="action" value="2"  />
		<input type="submit" value="清理 1 周之前所有下载记录数据" class="button-primary">（统计会员每天下载资源个数的数据，不是用户购买订单，可清理）
	</form>
	<br><br>
	<h1><font color="red">清空</font>数据表</h1>
	<p>不可恢复，谨慎操作。</p>
	<form action="" method="post" onsubmit="return confirm('确认清空充值记录？清空后耶稣都恢复不了哦~');">
		<input type="hidden" name="action" value="3"  />
		<input type="submit" value="清空充值订单" class="button-primary">（用户充值统计）
	</form>
	<br><br>
	<form action="" method="post" onsubmit="return confirm('确认清空购买记录？清空后耶稣都恢复不了哦~');">
		<input type="hidden" name="action" value="4"  />
		<input type="submit" value="清空购买订单" class="button-primary">（用户购买文章统计）
	</form>
	<br><br>
	<form action="" method="post" onsubmit="return confirm('确认清空VIP记录？清空后耶稣都恢复不了哦~');">
		<input type="hidden" name="action" value="5"  />
		<input type="submit" value="清空VIP订单" class="button-primary">（用户升级VIP统计，不影响VIP用户到期，只是清空升级记录）
	</form>
	<br><br>
	<form action="" method="post" onsubmit="return confirm('确认清空余额明细？清空后耶稣都恢复不了哦~');">
		<input type="hidden" name="action" value="6"  />
		<input type="submit" value="清空余额明细" class="button-primary">
	</form>
</div>