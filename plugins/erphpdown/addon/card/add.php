<?php 
if ( !defined('ABSPATH') ) {exit;}
if(!is_user_logged_in())
{
	wp_die('请登录系统');
}
global $wpdb;
if(isset($_POST['createerphpcard']) && $_POST['createerphpcard']){
	$price = esc_sql($_POST['erphpcard_price']);
	$num = $_POST['erphpcard_num'];
	$i=0;$out = '<p>生成的【卡号 卡密】如下：</p><p>';
	for($i=0;$i < $num;$i++){
		$card = erphpdown_card_create_guid();
		$password = wp_create_nonce(rand(10,1000));
		$result = $wpdb->query("insert into $wpdb->erphpcard (card,password,price) values('".$card."','".$password."','".$price."')");
		$out .= $card.' '.$password.'<br />';
	}
	$out .='</p>';
	echo '<div id="message" class="notice notice-success">'.$out.'</div>';
}

?>
<div class="wrap">
	<?php
		if(!empty($text)){
			echo '<div id="message">'.$text.'</div>';
		} 
	?>
	<h2>添加充值卡</h2>
	<p>添加充值卡，便于用户充值使用，生成后会输出刚生成的充值卡。</p>
	<form action="" method="post" name="createerphpcard" id="createerphpcard" class="validate">
		<input name="action" type="hidden" value="createerphpcard">
		<table class="form-table">
			<tbody>
		    <tr class="form-field form-required">
				<th scope="row"><label for="erphpcard_name">个数 </label></th>
				<td><input name="erphpcard_num" type="text" id="erphpcard_num" value="1" required></td>
			</tr>
			<tr class="form-field form-required">
				<th scope="row"><label for="erphpcard_price">面值 <span class="description">(单位：元  必填)</span></label></th>
				<td><input name="erphpcard_price" type="number" step="0.01" min="0.01" id="erphpcard_price" placeholder="0.00" required></td>
			</tr>
			</tbody>
		</table>
		<p class="submit"><input type="submit" name="createerphpcard" id="createerphpcardsub" class="button button-primary" value="添加充值卡"></p>
	</form>
</div>
