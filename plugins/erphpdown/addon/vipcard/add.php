<?php 
date_default_timezone_set('Asia/Shanghai');
if(!is_user_logged_in())
{
	wp_die('请登录系统');
}
global $wpdb;
if(isset($_POST['createerphpcard']) && $_POST['createerphpcard']){
	$price = esc_sql($_POST['erphpcard_price']);
	$num = esc_sql($_POST['erphpcard_num']);
	$days = esc_sql($_POST['erphpcard_days']);

	$dd = '';
	if($days){
		$dd = date("Y-m-d",strtotime("+".$days." day"));
	}

	$name = '包月VIP';
	if($price == 6) $name = '体验VIP';
	if($price == 8) $name = '包季VIP';
	elseif($price == 9) $name = '包年VIP';
	elseif($price == 10) $name = '终身VIP';

	$i=0;$out = '<br>生成的 <strong>'.$name.'</strong> 激活码如下：<br /><div>';
	for($i=0;$i < $num;$i++){
		$card = create_vipguid();
		$password = wp_create_nonce(rand(10,1000));
		$result = $wpdb->query("insert into $wpdb->erphpvipcard (card,usertype,createtime,endtime) values('".$card."','".$price."','".date("Y-m-d H:i:s")."','".$dd."')");
		$out .= $card.'<br />';
	}
	$out .='</div>';
	echo $out;
}

?>
<script type="text/javascript">
	function checkFm()
	{
		if(document.getElementById("erphpcard_num").value=="")
		{
			alert('请输入个数');
			return false;
		}
		if(document.getElementById("erphpcard_price").value=="")
		{
			alert('请选择VIP类型');
			return false;
		}
		
	}
</script>
<div class="wrap">
<?php if(!empty($text))
{
	echo '<div id="message">'.$text.'</div>';
} ?>
<h2 id="add-new-user"> 添加激活码</h2>

<div id="ajax-response"></div>
<form action="" method="post" name="createerphpcard" id="createerphpcard" class="validate" onsubmit="return checkFm();">
<input name="action" type="hidden" value="createerphpcard">
<table class="form-table">
	<tbody>
    <tr class="form-field form-required">
		<th scope="row"><label for="erphpcard_name">个数 </label></th>
		<td><input name="erphpcard_num" type="number" id="erphpcard_num" min="1" step="1" value="1" aria-required="true" ></td>
	</tr>
	<tr class="form-field form-required">
		<th scope="row"><label for="erphpcard_price">VIP类型 </label></th>
		<td><input type="radio" id="erphpcard_price" name="erphpcard_price" value="10" checked />终身VIP会员 
				<input type="radio" id="erphpcard_price" name="erphpcard_price" value="9" />包年VIP会员 
				<input type="radio" id="erphpcard_price" name="erphpcard_price" value="8" />包季VIP会员 
				<input type="radio" id="erphpcard_price" name="erphpcard_price" value="7" />包月VIP会员
				<input type="radio" id="erphpcard_price" name="erphpcard_price" value="6" />体验VIP会员
		</td>
	</tr>
	<tr class="form-field form-required">
		<th scope="row"><label for="erphpcard_days">过期天数 </label></th>
		<td><input name="erphpcard_days" type="number" id="erphpcard_days" min="0" step="1" value="0" ><p>0表示不过期</p></td>
	</tr>
	</tbody>
</table>


<p class="submit"><input type="submit" name="createerphpcard" id="createerphpcardsub" class="button button-primary" value="添加"></p>
</form>
</div>
