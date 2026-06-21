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
	return get_theme_mod( 'mizuki_anime_mode', 'local' );
}

/**
 * 获取 Bangumi 用户 ID
 */
function mizuki_get_bangumi_user_id() {
	return get_theme_mod( 'mizuki_bangumi_user_id', '' );
}

/**
 * 获取 Bilibili VMID
 */
function mizuki_get_bilibili_vmid() {
	return get_theme_mod( 'mizuki_bilibili_vmid', '' );
}

/**
 * 获取缓存时间（小时）
 */
function mizuki_get_anime_cache_hours() {
	return absint( get_theme_mod( 'mizuki_anime_cache_hours', 24 ) );
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
			'post_type'      => 'mizuki_anime',
			'posts_per_page' => -1,
			'orderby'        => 'date',
			'order'          => 'DESC',
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
				'title'           => get_the_title(),
				'status'          => $status ?: 'planned',
				'rating'          => $score ? floatval( $score ) : 0,
				'cover'           => get_the_post_thumbnail_url( $post_id, 'medium_large' ) ?: '',
				'description'     => get_the_excerpt(),
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

	// 获取不同状态的番剧
	$collections = array(
		array(
			'type'   => 3,
			'status' => 'watching',
		),
		array(
			'type'   => 1,
			'status' => 'planned',
		),
		array(
			'type'   => 2,
			'status' => 'completed',
		),
		array(
			'type'   => 4,
			'status' => 'onhold',
		),
		array(
			'type'   => 5,
			'status' => 'dropped',
		),
	);

	$anime_list = array();

	foreach ( $collections as $collection ) {
		$items = mizuki_fetch_bangumi_collection( $user_id, $collection['type'], $collection['status'] );
		$anime_list = array_merge( $anime_list, $items );
	}

	// 缓存数据
	$cache_hours = mizuki_get_anime_cache_hours();
	set_transient( $cache_key, $anime_list, $cache_hours * HOUR_IN_SECONDS );

	return $anime_list;
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

	while ( true ) {
		$url      = sprintf( '%s/v0/users/%s/collections?subject_type=2&type=%d&limit=%d&offset=%d', $api_base, $user_id, $type, $limit, $offset );
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

		if ( ! isset( $data['data'] ) || empty( $data['data'] ) ) {
			break;
		}

		$all_data = array_merge( $all_data, $data['data'] );

		if ( count( $data['data'] ) < $limit ) {
			break;
		}

		$offset += $limit;
		sleep( 1 ); // 防止请求过快
	}

	return mizuki_process_bangumi_data( $all_data, $status );
}

/**
 * 处理 Bangumi 数据
 *
 * @param array  $items  原始数据
 * @param string $status 状态
 * @return array
 */
function mizuki_process_bangumi_data( $items, $status ) {
	$results = array();

	foreach ( $items as $item ) {
		$subject        = isset( $item['subject'] ) ? $item['subject'] : array();
		$year           = isset( $subject['date'] ) ? substr( $subject['date'], 0, 4 ) : '';
		$rating         = isset( $item['rate'] ) ? floatval( $item['rate'] ) : ( isset( $subject['score'] ) ? floatval( $subject['score'] ) : 0 );
		$progress       = isset( $item['ep_status'] ) ? intval( $item['ep_status'] ) : 0;
		$total_episodes = isset( $subject['eps'] ) ? intval( $subject['eps'] ) : $progress;
		$cover          = isset( $subject['images']['medium'] ) ? $subject['images']['medium'] : '';
		$description    = isset( $subject['short_summary'] ) ? $subject['short_summary'] : ( isset( $subject['name_cn'] ) ? $subject['name_cn'] : '' );

		// 获取详细信息（包括制作公司）
		$subject_id     = isset( $subject['id'] ) ? $subject['id'] : 0;
		$subject_detail = array();
		if ( $subject_id ) {
			$subject_detail = mizuki_fetch_bangumi_subject( $subject_id );
		}

		$studio = 'Unknown';
		if ( ! empty( $subject_detail['infobox'] ) ) {
			$studio = mizuki_get_studio_from_infobox( $subject_detail['infobox'] );
		}

		if ( ! empty( $subject_detail['summary'] ) ) {
			$description = $subject_detail['summary'];
		}

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
			'title'           => isset( $subject['name_cn'] ) ? $subject['name_cn'] : ( isset( $subject['name'] ) ? $subject['name'] : 'Unknown' ),
			'status'          => $status,
			'rating'          => $rating,
			'cover'           => $cover,
			'description'     => trim( $description ),
			'year'            => $year,
			'genre'           => $genre,
			'studio'          => $studio,
			'link'            => $subject_id ? 'https://bgm.tv/subject/' . $subject_id : '#',
			'progress'        => $progress,
			'totalEpisodes'   => $total_episodes,
			'progressPercent' => $total_episodes > 0 ? round( ( $progress / $total_episodes ) * 100 ) : 0,
		);

		sleep( 1 ); // 获取详细信息后延迟
	}

	return $results;
}

/**
 * 获取 Bangumi 条目详细信息
 *
 * @param int $subject_id 条目 ID
 * @return array
 */
function mizuki_fetch_bangumi_subject( $subject_id ) {
	$url      = 'https://api.bgm.tv/v0/subjects/' . $subject_id;
	$response = wp_remote_get(
		$url,
		array(
			'timeout' => 15,
		)
	);

	if ( is_wp_error( $response ) ) {
		return array();
	}

	$code = wp_remote_retrieve_response_code( $response );
	if ( 200 !== $code ) {
		return array();
	}

	$body = wp_remote_retrieve_body( $response );
	return json_decode( $body, true ) ?: array();
}

/**
 * 从 infobox 中提取制作公司
 *
 * @param array $infobox
 * @return string
 */
function mizuki_get_studio_from_infobox( $infobox ) {
	if ( ! is_array( $infobox ) ) {
		return 'Unknown';
	}

	$target_keys = array( '动画制作', '制作', '製作', '开发' );

	foreach ( $target_keys as $key ) {
		foreach ( $infobox as $item ) {
			if ( isset( $item['key'] ) && $item['key'] === $key ) {
				if ( isset( $item['value'] ) ) {
					if ( is_string( $item['value'] ) ) {
						return $item['value'];
					}
					if ( is_array( $item['value'] ) ) {
						foreach ( $item['value'] as $v ) {
							if ( isset( $v['v'] ) ) {
								return $v['v'];
							}
						}
					}
				}
			}
		}
	}

	return 'Unknown';
}

/**
 * 获取 Bilibili 数据
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

	// 获取三种状态的数据：1=想看, 2=在看, 3=已看
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

	// 缓存数据
	$cache_hours = mizuki_get_anime_cache_hours();
	set_transient( $cache_key, $anime_list, $cache_hours * HOUR_IN_SECONDS );

	return $anime_list;
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
		sleep( 1 ); // 防止请求过快
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
			if ( get_theme_mod( 'mizuki_bilibili_use_webp', true ) ) {
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
			'title'           => isset( $bangumi['title'] ) ? $bangumi['title'] : 'Unknown',
			'status'          => $status,
			'rating'          => $rating,
			'cover'           => $cover,
			'description'     => $description,
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
 * AJAX 处理：更新追番数据
 */
function mizuki_ajax_refresh_anime_data() {
	check_ajax_referer( 'mizuki_anime_refresh', 'nonce' );

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( array( 'message' => __( '没有权限', 'mizuki' ) ) );
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

	$data = mizuki_get_anime_list();
	wp_send_json_success( $data );
}
add_action( 'wp_ajax_mizuki_get_anime_data', 'mizuki_ajax_get_anime_data' );
add_action( 'wp_ajax_nopriv_mizuki_get_anime_data', 'mizuki_ajax_get_anime_data' );
