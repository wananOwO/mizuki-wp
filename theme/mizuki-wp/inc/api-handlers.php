<?php
/**
 * API Handlers for Bangumi and Bilibili
 *
 * 处理 Bangumi 和 Bilibili API 调用，包括数据获取和缓存
 *
 * @package Mizuki
 */

defined( 'ABSPATH' ) || exit;

/**
 * 获取追番数据源模式
 */
function mizuki_get_anime_mode() {
	return mizuki_get_theme_mod( 'mizuki_anime_mode', 'local' );
}

/**
 * 获取 Bangumi 用户 ID
 */
function mizuki_get_bangumi_user_id() {
	return mizuki_get_theme_mod( 'mizuki_bangumi_user_id', '' );
}

/**
 * 获取 Bilibili VMID
 */
function mizuki_get_bilibili_vmid() {
	return mizuki_get_theme_mod( 'mizuki_bilibili_vmid', '' );
}

/**
 * 获取缓存时间（小时）
 */
function mizuki_get_anime_cache_hours() {
	return absint( mizuki_get_theme_mod( 'mizuki_anime_cache_hours', 24 ) );
}

/**
 * 获取追番列表数据
 *
 * @return array 追番列表数组
 */
function mizuki_get_anime_list() {
	$mode = mizuki_get_anime_mode();

	switch ( $mode ) {
		case 'bangumi':
			return mizuki_get_bangumi_data();
		case 'bilibili':
			return mizuki_get_bilibili_data();
		case 'local':
		default:
			return mizuki_get_local_anime_data();
	}
}

/**
 * 获取本地追番数据（从自定义文章类型）
 *
 * @return array
 */
function mizuki_get_local_anime_data() {
	$cache_key = 'mizuki_local_anime_data';
	$cached    = get_transient( $cache_key );

	if ( false !== $cached ) {
		return $cached;
	}

	$anime_query = new WP_Query(
		array(
			'post_type'              => 'mizuki_anime',
			'posts_per_page'        => 200,
			'orderby'                => 'date',
			'order'                  => 'DESC',
			'no_found_rows'         => true,
			'update_post_term_cache' => false,
			'update_post_meta_cache' => true,
		)
	);

	$anime_list = array();

	if ( $anime_query->have_posts() ) {
		while ( $anime_query->have_posts() ) {
			$anime_query->the_post();
			$post_id = get_the_ID();

			$status   = get_post_meta( $post_id, '_mizuki_anime_status', true );
			$score    = get_post_meta( $post_id, '_mizuki_anime_score', true );
			$url      = get_post_meta( $post_id, '_mizuki_anime_url', true );
			$progress = get_post_meta( $post_id, '_mizuki_anime_progress', true );

			// 解析进度
			$progress_num     = 0;
			$total_episodes   = 0;
			$progress_percent = 0;

			if ( $progress && strpos( $progress, '/' ) !== false ) {
				list( $cur, $total ) = array_map( 'trim', explode( '/', $progress, 2 ) );
				$progress_num       = (int) $cur;
				$total_episodes     = (int) $total;
				if ( $total_episodes > 0 ) {
					$progress_percent = min( 100, ( $progress_num / $total_episodes ) * 100 );
				}
			}

			$anime_list[] = array(
				'title' => sanitize_text_field( get_the_title() ),
				'status'          => $status ?: 'planned',
				'rating'          => $score ? floatval( $score ) : 0,
				'cover'           => get_the_post_thumbnail_url( $post_id, 'medium_large' ) ?: '',
				'description' => wp_kses_post( get_the_excerpt() ),
				'year'            => get_the_date( 'Y' ),
				'studio'          => '',
				'genre'           => array(),
				'link'            => $url ?: get_permalink(),
				'progress'        => $progress_num,
				'totalEpisodes'   => $total_episodes,
				'progressPercent' => $progress_percent,
			);
		}
		wp_reset_postdata();
	}

	// 缓存 1 小时
	set_transient( $cache_key, $anime_list, HOUR_IN_SECONDS );

	return $anime_list;
}

/**
 * 获取 Bangumi 数据
 *
 * 缓存未命中时同步拉取收藏列表（已移除逐条详情请求，整套仅需数秒）。
 * 不再依赖 WP-Cron 触发：部分主机未启用 WP-Cron 会导致追番永远为空，
 * 同步拉取能保证首次访问即可看到数据，拉取后写入 transient 缓存。
 *
 * @return array
 */
function mizuki_get_bangumi_data() {
	$user_id = mizuki_get_bangumi_user_id();
	if ( empty( $user_id ) ) {
		return array();
	}

	$cache_key   = 'mizuki_bangumi_data_' . $user_id;
	$cached_data = get_transient( $cache_key );

	if ( false !== $cached_data ) {
		return $cached_data;
	}

	// 缓存未命中：同步刷新（数秒内完成），随后回读缓存。
	mizuki_refresh_remote_anime_data();

	$cached_data = get_transient( $cache_key );
	return false !== $cached_data ? $cached_data : array();
}

/**
 * 获取 Bangumi 收藏数据
 *
 * @param string $user_id Bangumi 用户 ID
 * @param int    $type    收藏类型
 * @param string $status  状态标识
 * @return array
 */
function mizuki_fetch_bangumi_collection( $user_id, $type, $status ) {
	$api_base = 'https://api.bgm.tv';
	$all_data = array();
	$offset   = 0;
	$limit    = 50;

	// Bangumi 官方 API 规范要求附带可识别来源的 User-Agent，便于统计与限流。
	$args = array(
		'timeout' => 15,
		'headers' => array(
			'User-Agent' => 'Mizuki-WP/1.0 (https://github.com/wananOwO/Mizuki)',
		),
	);

	while ( true ) {
		$url      = sprintf( '%s/v0/users/%s/collections?subject_type=2&type=%d&limit=%d&offset=%d', $api_base, $user_id, $type, $limit, $offset );
		$response = wp_remote_get( $url, $args );

		if ( is_wp_error( $response ) ) {
			break;
		}

		$code = wp_remote_retrieve_response_code( $response );
		if ( 200 !== $code ) {
			break;
		}

		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );

		if ( ! isset( $data['data'] ) || empty( $data['data'] ) ) {
			break;
		}

		$all_data = array_merge( $all_data, $data['data'] );

		if ( count( $data['data'] ) < $limit ) {
			break;
		}

		$offset += $limit;
		usleep( 500000 ); // 0.5 秒延迟（非阻塞微延迟，替代 sleep(1)）
	}

	return mizuki_process_bangumi_data( $all_data, $status );
}

/**
 * 处理 Bangumi 数据
 *
 * 仅使用收藏列表接口返回的字段，不再逐条请求条目详情。
 * 原先的 N+1 详情请求会导致刷新过程在 WP-Cron（30s 超时）中无法完成，
 * 进而缓存永远写不进去、前台永远为空。收藏接口本身已包含标题、封面、
 * 评分、简介、年份、标签、集数等渲染所需字段，逐条详情并非必需。
 *
 * @param array  $items  原始数据
 * @param string $status 状态
 * @return array
 */
function mizuki_process_bangumi_data( $items, $status ) {
	$results = array();

	foreach ( $items as $item ) {
		$subject        = isset( $item['subject'] ) ? $item['subject'] : array();
		$subject_id     = isset( $subject['id'] ) ? intval( $subject['id'] ) : 0;
		$year           = isset( $subject['date'] ) ? substr( $subject['date'], 0, 4 ) : '';
		$rating         = isset( $item['rate'] ) ? floatval( $item['rate'] ) : ( isset( $subject['score'] ) ? floatval( $subject['score'] ) : 0 );
		$progress       = isset( $item['ep_status'] ) ? intval( $item['ep_status'] ) : 0;
		$total_episodes = isset( $subject['eps'] ) ? intval( $subject['eps'] ) : $progress;
		$cover          = isset( $subject['images']['medium'] ) ? $subject['images']['medium'] : '';
		$description    = isset( $subject['short_summary'] ) ? $subject['short_summary'] : '';

		// 类型/标签（收藏接口已提供，无需逐条请求详情）
		$genre = array();
		if ( isset( $subject['tags'] ) && is_array( $subject['tags'] ) ) {
			foreach ( array_slice( $subject['tags'], 0, 3 ) as $tag ) {
				if ( isset( $tag['name'] ) ) {
					$genre[] = $tag['name'];
				}
			}
		}
		if ( empty( $genre ) ) {
			$genre = array( 'Unknown' );
		}

		$results[] = array(
			'title'           => sanitize_text_field( isset( $subject['name_cn'] ) ? $subject['name_cn'] : ( isset( $subject['name'] ) ? $subject['name'] : 'Unknown' ) ),
			'status'          => $status,
			'rating'          => $rating,
			'cover'           => $cover,
			'description'     => wp_kses_post( trim( $description ) ),
			'year'            => $year,
			'genre'           => $genre,
			'studio'          => 'Unknown',
			'link'            => $subject_id ? 'https://bgm.tv/subject/' . $subject_id : '#',
			'progress'        => $progress,
			'totalEpisodes'   => $total_episodes,
			'progressPercent' => $total_episodes > 0 ? round( ( $progress / $total_episodes ) * 100 ) : 0,
		);
	}

	return $results;
}

/**
 * 获取 Bilibili 数据
 *
 * 缓存未命中时同步拉取，随后回读缓存（Bilibili 无逐条详情请求，速度很快）。
 *
 * @return array
 */
function mizuki_get_bilibili_data() {
	$vmid = mizuki_get_bilibili_vmid();
	if ( empty( $vmid ) ) {
		return array();
	}

	$cache_key   = 'mizuki_bilibili_data_' . $vmid;
	$cached_data = get_transient( $cache_key );

	if ( false !== $cached_data ) {
		return $cached_data;
	}

	// 缓存未命中：同步刷新，随后回读缓存。
	mizuki_refresh_remote_anime_data();

	$cached_data = get_transient( $cache_key );
	return false !== $cached_data ? $cached_data : array();
}

/**
 * 获取 Bilibili 收藏数据
 *
 * @param string $vmid       用户 ID
 * @param int    $status_num 状态数字
 * @param string $status     状态标识
 * @return array
 */
function mizuki_fetch_bilibili_collection( $vmid, $status_num, $status ) {
	$api_base   = 'https://api.bilibili.com/x/space/bangumi/follow/list';
	$page_size  = 30;
	$all_data   = array();
	$page       = 1;
	$total_page = 1;

	// 首先获取总页数
	$first_url      = sprintf( '%s?type=1&follow_status=%d&vmid=%s&ps=1&pn=1', $api_base, $status_num, $vmid );
	$first_response = wp_remote_get(
		$first_url,
		array(
			'timeout' => 15,
		)
	);

	if ( ! is_wp_error( $first_response ) ) {
		$first_body = wp_remote_retrieve_body( $first_response );
		$first_data = json_decode( $first_body, true );
		if ( isset( $first_data['code'] ) && 0 === $first_data['code'] && isset( $first_data['data']['total'] ) ) {
			$total_page = ceil( $first_data['data']['total'] / $page_size ) + 1;
		}
	}

	// 获取所有页面数据
	while ( $page < $total_page ) {
		$url      = sprintf( '%s?type=1&follow_status=%d&vmid=%s&ps=%d&pn=%d', $api_base, $status_num, $vmid, $page_size, $page );
		$response = wp_remote_get(
			$url,
			array(
				'timeout' => 15,
			)
		);

		if ( is_wp_error( $response ) ) {
			break;
		}

		$code = wp_remote_retrieve_response_code( $response );
		if ( 200 !== $code ) {
			break;
		}

		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );

		if ( ! isset( $data['code'] ) || 0 !== $data['code'] ) {
			break;
		}

		if ( ! isset( $data['data']['list'] ) || empty( $data['data']['list'] ) ) {
			break;
		}

		$all_data = array_merge( $all_data, $data['data']['list'] );
		++$page;
		usleep( 500000 ); // 0.5 秒延迟（非阻塞微延迟，替代 sleep(1)）
	}

	return mizuki_process_bilibili_data( $all_data, $status );
}

/**
 * 处理 Bilibili 数据
 *
 * @param array  $items  原始数据
 * @param string $status 状态
 * @return array
 */
function mizuki_process_bilibili_data( $items, $status ) {
	$results = array();

	foreach ( $items as $bangumi ) {
		// 处理封面
		$cover = isset( $bangumi['cover'] ) ? $bangumi['cover'] : '';
		if ( ! empty( $cover ) ) {
			// 确保使用 https
			$cover = str_replace( 'http://', 'https://', $cover );

			// 使用 WebP 优化
			if ( mizuki_get_theme_mod( 'mizuki_bilibili_use_webp', true ) ) {
				if ( strpos( $cover, '@' ) === false ) {
					$cover .= '@220w_280h.webp';
				}
			}
		}

		// 处理进度
		$progress       = 0;
		$total_episodes = isset( $bangumi['total_count'] ) ? intval( $bangumi['total_count'] ) : 0;

		if ( isset( $bangumi['progress'] ) && ! empty( $bangumi['progress'] ) ) {
			if ( is_string( $bangumi['progress'] ) ) {
				preg_match( '/(\d+)/', $bangumi['progress'], $matches );
				if ( ! empty( $matches[1] ) ) {
					$progress = intval( $matches[1] );
				}
			} elseif ( is_numeric( $bangumi['progress'] ) ) {
				$progress = intval( $bangumi['progress'] );
			}
		}

		$progress_percent = ( $total_episodes > 0 && $progress > 0 ) ? round( ( $progress / $total_episodes ) * 100 ) : 0;

		// 描述
		$description = isset( $bangumi['evaluate'] ) ? $bangumi['evaluate'] : ( isset( $bangumi['summary'] ) ? $bangumi['summary'] : '' );
		$description = trim( $description );

		// 年份
		$year = '';
		if ( isset( $bangumi['publish']['release_date'] ) ) {
			preg_match( '/^(\d{4})/', $bangumi['publish']['release_date'], $matches );
			if ( ! empty( $matches[1] ) ) {
				$year = $matches[1];
			}
		} elseif ( isset( $bangumi['publish']['pub_time'] ) ) {
			preg_match( '/^(\d{4})/', $bangumi['publish']['pub_time'], $matches );
			if ( ! empty( $matches[1] ) ) {
				$year = $matches[1];
			}
		}

		// 制作公司
		$studio = '';
		if ( isset( $bangumi['areas'] ) && is_array( $bangumi['areas'] ) && ! empty( $bangumi['areas'] ) ) {
			$studio = $bangumi['areas'][0]['name'];
		}

		// 类型/标签
		$genre = array();
		if ( isset( $bangumi['styles'] ) && is_array( $bangumi['styles'] ) ) {
			$genre = $bangumi['styles'];
		}
		if ( empty( $genre ) && isset( $bangumi['season_type_name'] ) ) {
			$genre[] = $bangumi['season_type_name'];
		}
		if ( empty( $genre ) ) {
			$genre = array( 'Unknown' );
		}

		// 链接
		$link = '#';
		if ( isset( $bangumi['url'] ) ) {
			$link = $bangumi['url'];
		} elseif ( isset( $bangumi['season_id'] ) ) {
			$link = 'https://www.bilibili.com/bangumi/play/ss' . $bangumi['season_id'];
		} elseif ( isset( $bangumi['media_id'] ) ) {
			$link = 'https://www.bilibili.com/bangumi/media/md' . $bangumi['media_id'] . '/';
		}

		// 评分
		$rating = 0;
		if ( isset( $bangumi['rating']['score'] ) ) {
			$rating = floatval( $bangumi['rating']['score'] );
		}

		$results[] = array(
			'title'           => sanitize_text_field( isset( $bangumi['title'] ) ? $bangumi['title'] : 'Unknown' ),
			'status'          => $status,
			'rating'          => $rating,
			'cover'           => $cover,
			'description'     => wp_kses_post( $description ),
			'year'            => $year,
			'studio'          => $studio,
			'genre'           => $genre,
			'link'            => $link,
			'progress'        => $progress,
			'totalEpisodes'   => $total_episodes,
			'progressPercent' => $progress_percent,
		);
	}

	return $results;
}

/**
 * 简单的基于 transient 的速率限制。
 *
 * @param string $action 动作名。
 * @param int    $limit  窗口内允许的次数。
 * @param int    $window 窗口秒数。
 * @return bool 通过返回 true，超限返回 false。
 */
function mizuki_check_rate_limit( $action, $limit = 10, $window = 60 ) {
	$ip    = isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : 'unknown';
	$key   = 'mizuki_rl_' . $action . '_' . md5( $ip );
	$count = (int) get_transient( $key );
	if ( $count >= $limit ) {
		return false;
	}
	set_transient( $key, $count + 1, $window );
	return true;
}

/**
 * AJAX 处理：更新追番数据
 */
function mizuki_ajax_refresh_anime_data() {
	check_ajax_referer( 'mizuki_anime_refresh', 'nonce' );

	if ( ! mizuki_check_rate_limit( 'anime_data', 20, 60 ) ) {
		wp_send_json_error( array( 'message' => esc_html__( '请求过于频繁，请稍后再试。', 'mizuki' ) ), 429 );
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( array( 'message' => __( '没有权限', 'mizuki' ) ), 403 );
	}

	$mode = mizuki_get_anime_mode();

	// 清除缓存
	if ( 'bangumi' === $mode ) {
		$user_id   = mizuki_get_bangumi_user_id();
		$cache_key = 'mizuki_bangumi_data_' . $user_id;
		delete_transient( $cache_key );
	} elseif ( 'bilibili' === $mode ) {
		$vmid      = mizuki_get_bilibili_vmid();
		$cache_key = 'mizuki_bilibili_data_' . $vmid;
		delete_transient( $cache_key );
	} else {
		delete_transient( 'mizuki_local_anime_data' );
	}

	// 重新获取数据
	$data = mizuki_get_anime_list();

	wp_send_json_success(
		array(
			'message' => __( '数据已更新', 'mizuki' ),
			'count'   => count( $data ),
		)
	);
}
add_action( 'wp_ajax_mizuki_refresh_anime_data', 'mizuki_ajax_refresh_anime_data' );

/**
 * AJAX 处理：获取追番数据（前端使用）
 */
function mizuki_ajax_get_anime_data() {
	check_ajax_referer( 'mizuki_anime_nonce', 'nonce' );

	if ( ! mizuki_check_rate_limit( 'anime_data', 20, 60 ) ) {
		wp_send_json_error( array( 'message' => esc_html__( '请求过于频繁，请稍后再试。', 'mizuki' ) ), 429 );
	}

	$data = mizuki_get_anime_list();

	if ( ! headers_sent() ) {
		$etag = md5( wp_json_encode( $data ) );
		if ( isset( $_SERVER['HTTP_IF_NONE_MATCH'] ) && trim( $_SERVER['HTTP_IF_NONE_MATCH'] ) === $etag ) {
			status_header( 304 );
			exit;
		}
		header( 'ETag: ' . $etag );
		header( 'Cache-Control: public, max-age=300' );
	}

	wp_send_json_success( $data );
}
add_action( 'wp_ajax_mizuki_get_anime_data', 'mizuki_ajax_get_anime_data' );
add_action( 'wp_ajax_nopriv_mizuki_get_anime_data', 'mizuki_ajax_get_anime_data' );

/**
 * 刷新远程追番数据并写入缓存。
 *
 * 缓存未命中时由前台同步调用（mizuki_get_bangumi_data /
 * mizuki_get_bilibili_data），也可由 WP-Cron / AJAX 刷新触发。
 * Bangumi 已改为收藏列表单次拉取（无逐条详情），整套耗时数秒，
 * 远低于 PHP / WP-Cron 的 30s 超时，因此可安全同步执行。
 */
function mizuki_refresh_remote_anime_data() {
	$mode = mizuki_get_anime_mode();

	if ( 'bangumi' === $mode ) {
		$user_id = mizuki_get_bangumi_user_id();
		if ( empty( $user_id ) ) {
			return;
		}

		// 获取不同状态的番剧
		$collections = array(
			array( 'type' => 3, 'status' => 'watching' ),
			array( 'type' => 1, 'status' => 'planned' ),
			array( 'type' => 2, 'status' => 'completed' ),
			array( 'type' => 4, 'status' => 'onhold' ),
			array( 'type' => 5, 'status' => 'dropped' ),
		);

		$anime_list = array();
		foreach ( $collections as $collection ) {
			$items      = mizuki_fetch_bangumi_collection( $user_id, $collection['type'], $collection['status'] );
			$anime_list = array_merge( $anime_list, $items );
		}

		// 仅在拿到数据时写缓存，避免把一次性失败（空数组）缓存住。
		if ( ! empty( $anime_list ) ) {
			$cache_key   = 'mizuki_bangumi_data_' . $user_id;
			$cache_hours = mizuki_get_anime_cache_hours();
			set_transient( $cache_key, $anime_list, $cache_hours * HOUR_IN_SECONDS );
		}

	} elseif ( 'bilibili' === $mode ) {
		$vmid = mizuki_get_bilibili_vmid();
		if ( empty( $vmid ) ) {
			return;
		}

		$statuses   = array( 1, 2, 3 );
		$status_map = array(
			1 => 'planned',
			2 => 'watching',
			3 => 'completed',
		);

		$anime_list = array();
		foreach ( $statuses as $status_num ) {
			$items      = mizuki_fetch_bilibili_collection( $vmid, $status_num, $status_map[ $status_num ] );
			$anime_list = array_merge( $anime_list, $items );
		}

		if ( ! empty( $anime_list ) ) {
			$cache_key   = 'mizuki_bilibili_data_' . $vmid;
			$cache_hours = mizuki_get_anime_cache_hours();
			set_transient( $cache_key, $anime_list, $cache_hours * HOUR_IN_SECONDS );
		}
	}
}

/**
 * WP-Cron 回调：后台刷新远程追番数据。
 *
 * 供配置了服务器端真实 Cron 调用 wp-cron.php 的主机定期刷新使用；
 * 前台缓存未命中时也会同步走同一套逻辑，不再单方面依赖此 Cron。
 */
function mizuki_cron_refresh_anime() {
	mizuki_refresh_remote_anime_data();
}
add_action( 'mizuki_cron_refresh_anime', 'mizuki_cron_refresh_anime' );
