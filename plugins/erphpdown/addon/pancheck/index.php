<?php
add_filter('views_edit-post', 'epd_custom_post_views');
function epd_custom_post_views($views) {
    if( is_admin() ) {
        $query_pan = array(
            'post_type'   => 'post',
            'meta_key' => 'pan_status',
            'meta_value' => '0'
        );
        $result_pan = new WP_Query($query_pan);
        $class_pan = (isset($_GET['pan_status']) && $_GET['pan_status'] == '0') ? ' class="current"' : '';
        $views['pan_status'] = sprintf(__('<a href="%s"%s>'. '网盘失效' .' <span class="count">（%d）</span></a>', 'mobantu' ), admin_url('edit.php?post_status=all&post_type=post&pan_status=0'), $class_pan, $result_pan->found_posts); 
    }
    return $views;
}

add_filter( 'parse_query', 'epd_posts_filter' );
function epd_posts_filter( $query ){
    global $pagenow;
    $type = 'post';
    if (isset($_GET['post_type'])) {
        $type = $_GET['post_type'];
    }
    if ( 'post' == $type && is_admin() && $pagenow=='edit.php' && isset($_GET['pan_status']) && $_GET['pan_status'] != '') {
        $query->query_vars['meta_key'] = 'pan_status';
        $query->query_vars['meta_value'] = $_GET['pan_status'];
    }
}

add_filter( 'bulk_actions-edit-post', 'epd_post_bulk_actions' );
function epd_post_bulk_actions($bulk_actions) {
    $bulk_actions['delete_pan_status'] = '清除网盘状态';
    return $bulk_actions;
}

add_filter( 'handle_bulk_actions-edit-post', 'epd_post_bulk_action_handler', 10, 3 );
function epd_post_bulk_action_handler( $redirect_to, $doaction, $post_ids ) {
    if ( $doaction !== 'delete_pan_status' ) {
        return $redirect_to;
    }
    foreach ( $post_ids as $post_id ) {
        delete_post_meta($post_id, 'pan_status');
        delete_post_meta($post_id, 'pan_times');
        delete_post_meta($post_id, 'pan_result');
    }
    $redirect_to = add_query_arg( 'bulk_delete_pan_status_posts', count( $post_ids ), $redirect_to );
    return $redirect_to;
}

add_action( 'admin_notices', 'epd_bulk_action_admin_notice' );
function epd_bulk_action_admin_notice() {
    if ( ! empty( $_REQUEST['bulk_delete_pan_status_posts'] ) ) {
        $posts_count = intval( $_REQUEST['bulk_delete_pan_status_posts'] );
        printf( '<div id="message" class="updated settings-error fade"><p>成功清除 '.$posts_count.' 篇文章的网盘状态。</p></div>', $posts_count );
    }
}

function epd_check_pan_callback(){
	$post_id = esc_sql($_POST['post_id']);
	$index = esc_sql($_POST['post_index']);
	
	$pan_times=get_post_meta($post_id, 'pan_times', true);
	$pan_times = $pan_times?$pan_times:0;

	$result = array('status'=>'500','msg' =>'检测失败，请稍后重试');

	if($index){
		$urls=get_post_meta($post_id, 'down_urls', true);
		$cnt = count($urls['index']);
		if($cnt){
			for($i=0; $i<$cnt;$i++){
				if($urls['index'][$i] == $index){
					$url = $urls['url'][$i];
					break;
				}
			}
		}
	}else{
		$url=get_post_meta($post_id, 'down_url', true);
	}

	$erphp_downurl_old = get_option('erphp_downurl_old');
	$erphp_downurl_new = get_option('erphp_downurl_new');

	if($erphp_downurl_old && $erphp_downurl_new){
		$url = str_replace($erphp_downurl_old, $erphp_downurl_new, $url);
	}

	$url = trim($url);
	$downList=explode("\r\n",$url);
	foreach ($downList as $k=>$v){
		$filepath = $downList[$k];
		if($filepath){

			$erphp_colon_domains = get_option('erphp_colon_domains')?get_option('erphp_colon_domains'):'pan.baidu.com';
			if($erphp_colon_domains){
				$erphp_colon_domains_arr = explode(',', $erphp_colon_domains);
				foreach ($erphp_colon_domains_arr as $erphp_colon_domain) {
					if(strpos($filepath, $erphp_colon_domain) !== false){
						$filepath = str_replace('：', ': ', $filepath);
						break;
					}
				}
			}

			$erphp_blank_domains = get_option('erphp_blank_domains')?get_option('erphp_blank_domains'):'pan.baidu.com';
			$erphp_blank_domain_is = 0;
			if($erphp_blank_domains){
				$erphp_blank_domains_arr = explode(',', $erphp_blank_domains);
				foreach ($erphp_blank_domains_arr as $erphp_blank_domain) {
					if(strpos($filepath, $erphp_blank_domain) !== false){
						$erphp_blank_domain_is = 1;
						break;
					}
				}
			}

			if(strpos($filepath,',') !== false){
				$filearr = explode(',',$filepath);
				$arrlength = count($filearr);
				if($arrlength == 1){
					$url = $filepath;
				}elseif($arrlength >= 2){
					$url = $filearr[1];
				}
			}elseif(strpos($filepath,'  ') !== false && $erphp_blank_domain_is){
				$filearr = explode('  ',$filepath);
				$arrlength = count($filearr);
				if($arrlength == 1){
					$url = $filepath;
				}elseif($arrlength >= 2){
					$filearr2 = explode(':',$filearr[0]);
					$url = trim($filearr2[1].':'.$filearr2[2]);
				}
			}elseif(strpos($filepath,' ') !== false && $erphp_blank_domain_is){
				$filearr = explode(' ',$filepath);
				$arrlength = count($filearr);
				if($arrlength == 1){
					$url = $filepath;
				}elseif($arrlength >= 2){
					$url = $filearr[1];
				}
			}else{
				$url = $filepath;
			}

			if(strpos($url,'pan.baidu.com') !== false){
				$response = wp_remote_get( trim($url) );
				if ( is_array( $response ) && !is_wp_error($response) && $response['response']['code'] == '200' ) {
					$header = $response['headers'];
					$body = $response['body'];
					if(strpos($body,'页面不存在') !== false){ 
						$result = array('status'=>'500','msg' =>'该资源已失效，请勿购买');
						update_post_meta($post_id,'pan_times',$pan_times+1);
						update_post_meta($post_id,'pan_result','页面不存在');
						update_post_meta($post_id,'pan_status',0);
						break;
					}
					elseif(strpos($body,'链接不存在') !== false){ 
						$result = array('status'=>'500','msg' =>'该资源已失效，请勿购买');
						update_post_meta($post_id,'pan_times',$pan_times+1);
						update_post_meta($post_id,'pan_result','链接不存在');
						update_post_meta($post_id,'pan_status',0);
						break;
					}
					elseif(strpos($body,'分享的文件不存在') !== false){ 
						$result = array('status'=>'500','msg' =>'该资源已不存在，请勿购买');
						update_post_meta($post_id,'pan_status',0);
						break;
					}
					elseif(strpos($body,'此链接分享内容可能因为涉及侵权、色情、反动、低俗等信息，无法访问') !== false){ 
						$result = array('status'=>'500','msg' =>'该资源已失效，请勿购买');
						update_post_meta($post_id,'pan_status',0);
						break;
					}
					elseif(strpos($body,'啊哦，来晚了，该分享文件已过期') !== false){ 
						$result = array('status'=>'500','msg' =>'该资源分享已过期，请勿购买');
						update_post_meta($post_id,'pan_status',0);
						break;
					}
					elseif(strpos($body,'啊哦，你来晚了，分享的文件已经被删除了，下次要早点哟') !== false){ 
						$result = array('status'=>'500','msg' =>'该资源已被分享人删除了，请勿购买');
						update_post_meta($post_id,'pan_status',0);
						break;
					}
					elseif(strpos($body,'分享的文件已经被取消了') !== false){ 
						$result = array('status'=>'500','msg' =>'该资源已被分享人取消分享，请勿购买');
						update_post_meta($post_id,'pan_status',0);
						break;
					}
					elseif(strpos($body,'给您加密分享了文件') !== false){ 
						$result = array('status'=>'200','msg' =>'资源有效，可以购买');
						delete_post_meta($post_id, 'pan_status');
				        delete_post_meta($post_id, 'pan_times');
				        delete_post_meta($post_id, 'pan_result');
					}else{
						$result = array('status'=>'200','msg' =>'资源有效，可以购买');
						delete_post_meta($post_id, 'pan_status');
				        delete_post_meta($post_id, 'pan_times');
				        delete_post_meta($post_id, 'pan_result');
					}
				}elseif ( is_array( $response ) && !is_wp_error($response) && $response['response']['code'] == '404' ) {
					$result = array('status'=>'500','msg' =>'该资源不存在，请勿购买');
					update_post_meta($post_id,'pan_times',$pan_times+1);
					update_post_meta($post_id,'pan_result','页面不存在');
					update_post_meta($post_id,'pan_status',0);
					break;
				}else{
					$result = array('status'=>'500','msg' =>'检测失败，请稍后重试');
					break;
				}
			}elseif(strpos($url,'lanzou') !== false && strpos($url,'.com') !== false){
				$response = wp_remote_get( trim($url) );
				if ( is_array( $response ) && !is_wp_error($response) && $response['response']['code'] == '200' ) {
					$body = file_get_contents(trim($url));
					if(strpos($body,'来晚啦...文件取消分享了') !== false){ 
						$result = array('status'=>'500','msg' =>'该资源已失效，请勿购买');
						update_post_meta($post_id,'pan_times',$pan_times+1);
						update_post_meta($post_id,'pan_result','链接不存在');
						update_post_meta($post_id,'pan_status',0);
						break;
					}
					else{
						$result = array('status'=>'200','msg' =>'资源有效，可以购买');
						delete_post_meta($post_id, 'pan_status');
				        delete_post_meta($post_id, 'pan_times');
				        delete_post_meta($post_id, 'pan_result');
					}
				}elseif ( is_array( $response ) && !is_wp_error($response) && $response['response']['code'] == '404' ) {
					$result = array('status'=>'500','msg' =>'该资源不存在，请勿购买');
					update_post_meta($post_id,'pan_times',$pan_times+1);
					update_post_meta($post_id,'pan_result','页面不存在');
					update_post_meta($post_id,'pan_status',0);
					break;
				}else{
					$result = array('status'=>'500','msg' =>'检测失败，请稍后重试');
					break;
				}
			}elseif(strpos($url,'cloud.189.cn') !== false){
				$response = wp_remote_get( trim($url) );
				if ( is_array( $response ) && !is_wp_error($response) && $response['response']['code'] == '200' ) {
					$body = file_get_contents(trim($url));
					if(strpos($body,'抱歉，您访问的页面地址有误，或者该页面不存在') !== false){ 
						$result = array('status'=>'500','msg' =>'该资源已失效，请勿购买');
						update_post_meta($post_id,'pan_times',$pan_times+1);
						update_post_meta($post_id,'pan_result','链接不存在');
						update_post_meta($post_id,'pan_status',0);
						break;
					}
					elseif(strpos($body,'抱歉，此外链不存在') !== false){ 
						$result = array('status'=>'500','msg' =>'该资源已失效，请勿购买');
						update_post_meta($post_id,'pan_times',$pan_times+1);
						update_post_meta($post_id,'pan_result','链接不存在');
						update_post_meta($post_id,'pan_status',0);
						break;
					}
					else{
						$result = array('status'=>'200','msg' =>'资源有效，可以购买');
						delete_post_meta($post_id, 'pan_status');
				        delete_post_meta($post_id, 'pan_times');
				        delete_post_meta($post_id, 'pan_result');
					}
				}elseif ( is_array( $response ) && !is_wp_error($response) && $response['response']['code'] == '404' ) {
					$result = array('status'=>'500','msg' =>'该资源不存在，请勿购买');
					update_post_meta($post_id,'pan_times',$pan_times+1);
					update_post_meta($post_id,'pan_result','页面不存在');
					update_post_meta($post_id,'pan_status',0);
					break;
				}else{
					$result = array('status'=>'500','msg' =>'检测失败，请稍后重试');
					break;
				}
			}else{
				//$result = array('status'=>'500','msg' =>'检测失败，请稍后重试');
				//break;
			}

		}
	}

	if($result['status'] == '500'){
		//wp_mail(get_option('admin_email'), '资源ID '.$post_id.' 链接失效', '资源 '.get_post($post_id)->post_title.' 链接失效，资源地址：'.get_permalink($post_id));
	}

	header('Content-type: application/json');
	echo json_encode($result);
	exit;
}
add_action( 'wp_ajax_epd_check_pan', 'epd_check_pan_callback');
add_action( 'wp_ajax_nopriv_epd_check_pan', 'epd_check_pan_callback');

//add_filter('manage_post_posts_columns', 'epd_quickedit_post_columns');
function epd_quickedit_post_columns($columns) {
	$columns['pan_times'] = '失效次数';
	$columns['pan_result'] = '失效原因';
    $columns['down_url'] = '下载地址';
    return $columns;
}

//add_action( 'manage_post_posts_custom_column' , 'epd_quickedit_post_column', 10, 2 );
function epd_quickedit_post_column( $column, $post_id ) {
    switch ( $column ) {
    	case 'pan_times':
        	$pan_times = get_post_meta( $post_id , 'pan_times' , true );
        	echo $pan_times;
        break;
        case 'pan_result':
        	$pan_result = get_post_meta( $post_id , 'pan_result' , true );
        	echo $pan_result;
        break;
      	case 'down_url':
        	$down_url = get_post_meta( $post_id , 'down_url' , true );
        	echo $down_url;
        break;
    }
}