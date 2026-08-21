<?php
require( dirname(__FILE__).'/../../../../../wp-load.php' );
if(is_user_logged_in()){
	global $wpdb;
	if($_POST['do'] == 'delcard'){
		if(current_user_can('administrator')){
			$ids=esc_sql($_POST['ids']);
			$idarr = explode(',', $ids);
			if(count($idarr)){
				foreach ($idarr as $pid) {
					$wpdb->query("delete from $wpdb->erphpcard where id=".$pid);
				}
			}
			echo "success";
		}
	}
}