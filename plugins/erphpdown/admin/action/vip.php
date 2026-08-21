<?php
require( dirname(__FILE__) . '/../../../../../wp-load.php' );
if(isset($_POST['do']) && current_user_can('administrator')){
	global $wpdb;
	if($_POST['do']=='del'){
		$id=esc_sql(intval($_POST['id']));
		$sql="update ".$wpdb->iceinfo." set userType=0,endTime='1000-01-01' where ice_id=".$id;
		$a=$wpdb->query($sql);
		if($a){
			echo "success";
		}
	}elseif($_POST['do']=='delcat'){
		$id=esc_sql(intval($_POST['id']));
		$sql="update ".$wpdb->icecat." set userType=0,endTime='1000-01-01' where ice_id=".$id;
		$a=$wpdb->query($sql);
		if($a){
			echo "success";
		}
	}elseif($_POST['do']=='edit'){
		$id=esc_sql($_POST['id']);
		$new_date=esc_sql($_POST['new_date']);
		$sql="update ".$wpdb->iceinfo." set endTime='".$new_date."' where ice_id=".$id;
		$a=$wpdb->query($sql);
		if($a){
			echo "success";
		}
	}elseif($_POST['do']=='editcat'){
		$id=esc_sql($_POST['id']);
		$new_date=esc_sql($_POST['new_date']);
		$sql="update ".$wpdb->icecat." set endTime='".$new_date."' where ice_id=".$id;
		$a=$wpdb->query($sql);
		if($a){
			echo "success";
		}
	}elseif($_POST['do']=='type'){
		$ids=esc_sql($_POST['ids']);
		$type=esc_sql($_POST['type']);
		$down=esc_sql($_POST['down']);
		$price=esc_sql($_POST['price']);
		$days=esc_sql($_POST['days']);
		$repeat=esc_sql($_POST['repeat']);
		$idarr = explode(',', $ids);
		if(count($idarr)){
			foreach ($idarr as $pid) {
				if($type){
					update_post_meta($pid,"member_down",$type);
				}
				if($down){
					$data1 = '';$data2='';$data3='';$data5='';
					if($down == '1') $data1 = 'yes';
					if($down == '2') $data2 = 'yes';
					if($down == '3') $data3 = 'yes';
					if($down == '5') $data5 = 'yes';
					update_post_meta( $pid, 'start_down', $data1 );
					update_post_meta( $pid, 'start_see', $data2 );
					update_post_meta( $pid, 'start_see2', $data3 );
					update_post_meta( $pid, 'start_down2', $data5 );
					update_post_meta( $pid, 'erphp_down', $down );
				}
				if($price != ''){
					update_post_meta($pid,"down_price",$price);
				}
				if($days != ''){
					if($days == '0'){
						delete_post_meta($pid,"down_days");
					}else{
						update_post_meta($pid,"down_days",$days);
					}
				}
				if($repeat == 1){
					delete_post_meta($pid,"down_repeat");
				}elseif($repeat == 2){
					update_post_meta($pid,"down_repeat",1);
				}
			}
		}
		echo "success";
	}
}
