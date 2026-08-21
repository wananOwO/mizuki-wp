<?php
require( dirname(__FILE__).'/../../../../../wp-load.php' );
if(is_uploaded_file($_FILES['erphpFile']['tmp_name']) && is_user_logged_in() && current_user_can('publish_posts')){
	$vname = $_FILES['erphpFile']['name'];
	$size = $_FILES['erphpFile']['size'];
	if ($vname != "") {
		$link = '';
		$year = date("Y");$month = date("m");
		$filename = md5(date("YmdHis").mt_rand(100,999)).strrchr($vname,'.');
		//上传路径
		$upfile = '../../../../../wp-content/uploads/'.$year.'/'.$month.'/erphpdown/';
		if(!file_exists($upfile)){  mkdir($upfile,0777,true);} 
		$file_path = '../../../../../wp-content/uploads/'.$year.'/'.$month.'/erphpdown/'. $filename;
		if(move_uploaded_file($_FILES['erphpFile']['tmp_name'], $file_path)){
			$link = home_url().'/wp-content/uploads/'.$year.'/'.$month.'/erphpdown/'. $filename;
		}

		$arr=array(
			"size"=>sprintf("%1\$.2f", $size/(1024*1024)), //保留两位小数，单位MB
			"link"=>$link
		); 
		$jarr=json_encode($arr); 
		echo $jarr;

	}
}