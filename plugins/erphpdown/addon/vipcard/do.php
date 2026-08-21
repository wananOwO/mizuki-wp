<?php
require( dirname(__FILE__) . '/../../../../../wp-load.php' );
if(current_user_can('administrator')){
	if($_POST['do']=='dels'){
		$ids=esc_sql($_POST['ids']);
		$idarr = explode(',', $ids);
		if(count($idarr)){
			foreach ($idarr as $pid) {
				$sql = "delete from $wpdb->erphpvipcard where id=".$pid;
				$result=$wpdb->query($sql);
			}
		}
		echo 'success';
	}
}