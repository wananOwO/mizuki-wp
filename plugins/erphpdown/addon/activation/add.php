<?php 
if ( !defined('ABSPATH') ) {exit;}
global $wpdb;
if(isset($_POST['erphpact_pid'])){
	$pid = $_POST['erphpact_pid'];
	$nums = $_POST['erphpact_nums'];
	$arr = explode("\n", $nums);//若不行的话换成'\n'或'\r\n'或"\r\n"
	$count = count($arr);
	$i=0;
	for($i=0;$i < $count;$i++){
		$num = $arr[$i];
		if(trim($num) != '')
			$wpdb->query("insert into $wpdb->erphpact (num,pid,ctime) values('".trim($num)."',".$pid.",'".date("Y-m-d H:i:s")."')");
	}
	echo '<div class="updated settings-error"><p>添加成功！</p></div>';
}

?>
<div class="wrap">
	<h2 id="add-new-user"> 添加激活码</h2>

	<div id="ajax-response"></div>

	<p>添加激活码/卡密，一行一个。</p>
	<form method="post" class="validate">
		<table class="form-table">
			<tbody>
		    <tr>
				<th scope="row"><label for="erphpact_pid">文章ID </label></th>
				<td><input name="erphpact_pid" type="number" min="1" step="1" id="erphpact_pid" class="erphpdown-select-postid" placeholder="绑定的文章ID" required=""></td>
			</tr>
			<tr class="form-field form-required">
				<th scope="row"><label for="erphpact_nums">激活码</label></th>
				<td><textarea id="erphpact_nums" name="erphpact_nums" required="" rows="20" placeholder="一行一个，卡号与卡密用空格隔开"></textarea></td>
			</tr>
			</tbody>
		</table>
		<p class="submit"><input type="submit" class="button button-primary" value="添加激活码"></p>
	</form>
</div>
