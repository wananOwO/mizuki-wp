<?php
/**
 * 左 / 右 侧边栏渲染 — 与 Mizuki dist 的 #sidebar / #right-sidebar 1:1。
 *
 * 组件:资料卡、公告、分类、标签(左);站点统计、分类(右)。
 * 卡片结构沿用 Mizuki:card-base + before: 强调条标题 + collapse-wrapper 内容。
 *
 * @package Mizuki
 */
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'mizuki_widget_icons' ) ) {
	/**
	 * 侧栏图标路径(由 dist 导出)。
	 *
	 * @return array<string,array{0:string,1:string}> key => [viewBox, path]
	 */
	function mizuki_widget_icons() {
		return array(
			'article'  => array( '0 0 24 24', 'M7 17h7v-2H7zm0-4h10v-2H7zm0-4h10V7H7zM5 21q-.825 0-1.412-.587T3 19V5q0-.825.588-1.412T5 3h14q.825 0 1.413.588T21 5v14q0 .825-.587 1.413T19 21zm0-2h14V5H5zM5 5v14z' ),
			'folder'   => array( '0 0 24 24', 'M4 20q-.825 0-1.412-.587T2 18V6q0-.825.588-1.412T4 4h6l2 2h8q.825 0 1.413.588T22 8v10q0 .825-.587 1.413T20 20zm0-2h16V8h-8.825l-2-2H4zm0 0V6z' ),
			'label'    => array( '0 0 24 24', 'M4 20q-.825 0-1.412-.587T2 18V6q0-.825.588-1.412T4 4h11q.475 0 .9.213t.7.587L22 12l-5.4 7.2q-.275.375-.7.588T15 20zm0-2h11l4.5-6L15 6H4z' ),
			'words'    => array( '0 0 24 24', 'M4 20q-.825 0-1.412-.587T2 18V6q0-.825.588-1.412T4 4h16q.825 0 1.413.588T22 6v12q0 .825-.587 1.413T20 20zm2-3h12q.425 0 .713-.288T19 16t-.288-.712T18 15H6q-.425 0-.712.288T5 16t.288.713T6 17m0-4h12q.425 0 .713-.288T19 12t-.288-.712T18 11H6q-.425 0-.712.288T5 12t.288.713T6 13m0-4h8q.425 0 .713-.288T15 8t-.288-.712T14 7H6q-.425 0-.712.288T5 8t.288.713T6 9' ),
			'calendar' => array( '0 0 24 24', 'M5 8h14V6H5zm0 14q-.825 0-1.412-.587T3 20V6q0-.825.588-1.412T5 4h1V2h2v2h8V2h2v2h1q.825 0 1.413.588T21 6v5.675q-.475-.225-.975-.375T19 11.075V10H5v10h6.3q.175.55.413 1.05t.562.95zm9.463-.462Q13 20.075 13 18t1.463-3.537T18 13t3.538 1.463T23 18t-1.463 3.538T18 23t-3.537-1.463m5.212-1.162l.7-.7L18.5 17.8V15h-1v3.2z' ),
			'heart'    => array( '0 0 24 24', 'M12 21q-.45 0-.862-.162t-.738-.488l-6.7-6.725q-.875-.875-1.287-2T2 9.275Q2 6.7 3.675 4.85T7.85 3q1.2 0 2.263.475T12 4.8q.8-.85 1.863-1.325T16.125 3q2.5 0 4.188 1.85T22 9.25q0 1.225-.425 2.35t-1.275 2l-6.725 6.75q-.325.325-.725.488T12 21' ),
		);
	}
}

if ( ! function_exists( 'mizuki_widget_open' ) ) {
	/**
	 * 输出 Mizuki 卡片组件开头(card-base + 强调条标题 + collapse-wrapper)。
	 *
	 * @param string $title 标题。
	 * @param int    $delay 动画延迟 ms。
	 */
	function mizuki_widget_open( $title, $delay = 0 ) {
		printf(
			'<div class="pb-4 card-base onload-animation" style="animation-delay:%dms">',
			(int) $delay
		);
		echo '<div class="font-bold transition text-lg text-neutral-900 dark:text-neutral-100 relative ml-8 mt-4 mb-2 flex items-center before:w-1 before:h-4 before:rounded-md before:bg-[var(--primary)] before:absolute before:left-[-16px] before:top-[5.5px]">' . esc_html( $title ) . '</div>';
		echo '<div class="collapse-wrapper px-4 overflow-hidden">';
	}
}

if ( ! function_exists( 'mizuki_widget_close' ) ) {
	/**
	 * 关闭 Mizuki 卡片组件。
	 */
	function mizuki_widget_close() {
		echo '</div></div>';
	}
}

if ( ! function_exists( 'mizuki_stat_row' ) ) {
	/**
	 * 输出站点统计行。
	 *
	 * @param string $icon  图标键。
	 * @param string $label 标签。
	 * @param string $value 值。
	 * @param string $id    可选 span id。
	 */
	function mizuki_stat_row( $icon, $label, $value, $id = '' ) {
		$icons = mizuki_widget_icons();
		$svg   = '';
		if ( isset( $icons[ $icon ] ) ) {
			$svg = '<svg viewBox="' . esc_attr( $icons[ $icon ][0] ) . '" width="1em" height="1em"><path fill="currentColor" d="' . $icons[ $icon ][1] . '"/></svg>';
		}
		$id_attr = $id ? ' id="' . esc_attr( $id ) . '"' : '';
		echo '<div class="flex items-center justify-between px-2 py-2"><div class="flex items-center gap-2.5 flex-1 min-w-0"><div class="text-[var(--primary)] text-xl shrink-0">' . $svg . '</div><span class="text-neutral-700 dark:text-neutral-300 font-medium text-sm break-words leading-tight">' . esc_html( $label ) . '</span></div><div class="flex items-center ml-3 shrink-0"><span' . $id_attr . ' class="text-base font-bold text-neutral-900 dark:text-neutral-100">' . esc_html( $value ) . '</span></div></div>';
	}
}

if ( ! function_exists( 'mizuki_profile_card' ) ) {
	/**
	 * 输出资料卡(card-base p-3:头像 + 昵称 + 强调条 + 简介 + 社交 + 统计)。
	 */
	function mizuki_profile_card() {
		$avatar = get_theme_mod( 'mizuki_avatar', '' );
		if ( ! $avatar ) {
			$cid = get_theme_mod( 'custom_logo' );
			if ( $cid ) {
				$avatar = wp_get_attachment_image_url( $cid, 'full' );
			}
		}
		if ( ! $avatar ) {
			$avatar = get_template_directory_uri() . '/assets/home/default-logo.webp';
		}
		$nickname = get_theme_mod( 'mizuki_nickname', '' );
		if ( ! $nickname ) {
			$nickname = get_bloginfo( 'name' );
		}
		$bio        = get_theme_mod( 'mizuki_bio', get_bloginfo( 'description' ) );
		$about      = get_page_by_path( 'about' );
		$avatar_url = $about ? get_permalink( $about ) : home_url( '/' );
		?>
		<div class="card-base p-3">
			<a aria-label="<?php esc_attr_e( '关于', 'mizuki' ); ?>" href="<?php echo esc_url( $avatar_url ); ?>" class="group block relative mx-auto mt-1 lg:mx-0 lg:mt-0 mb-3 max-w-[12rem] lg:max-w-none overflow-hidden rounded-xl active:scale-95">
				<div class="absolute transition pointer-events-none group-hover:bg-black/30 group-active:bg-black/50 w-full h-full z-50 flex items-center justify-center">
					<svg viewBox="0 0 640 640" width="1em" height="1em" class="transition opacity-0 scale-90 group-hover:scale-100 group-hover:opacity-100 text-white text-5xl"><path fill="currentColor" d="M544 144c8.8 0 16 7.2 16 16v320c0 8.8-7.2 16-16 16H96c-8.8 0-16-7.2-16-16V160c0-8.8 7.2-16 16-16zM96 96c-35.3 0-64 28.7-64 64v320c0 35.3 28.7 64 64 64h448c35.3 0 64-28.7 64-64V160c0-35.3-28.7-64-64-64zm144 216c30.9 0 56-25.1 56-56s-25.1-56-56-56s-56 25.1-56 56s25.1 56 56 56m-32 40c-44.2 0-80 35.8-80 80c0 8.8 7.2 16 16 16h192c8.8 0 16-7.2 16-16c0-44.2-35.8-80-80-80z"/></svg>
				</div>
				<div class="mx-auto lg:w-full h-full lg:mt-0 overflow-hidden relative">
					<div class="transition absolute inset-0 dark:bg-black/10 bg-opacity-50 pointer-events-none"></div>
					<img src="<?php echo esc_url( $avatar ); ?>" alt="<?php echo esc_attr( $nickname ); ?>" loading="eager" decoding="async" class="w-full h-full object-cover" style="object-position:center">
				</div>
			</a>
			<div class="px-2">
				<div class="font-bold text-xl text-center mb-1 dark:text-neutral-50 transition"><?php echo esc_html( $nickname ); ?></div>
				<div class="h-1 w-5 bg-[var(--primary)] mx-auto rounded-full mb-2 transition"></div>
				<?php if ( $bio ) : ?>
				<div class="text-center text-neutral-400 mb-2.5 transition"><?php echo esc_html( $bio ); ?></div>
				<?php endif; ?>
				<?php
				// 社交链接(btn-regular 方块按钮)。
				$socials = array();
				foreach ( array( 'github', 'twitter', 'email', 'rss' ) as $p ) {
					$url = get_theme_mod( "mizuki_social_{$p}", '' );
					if ( ! $url ) {
						continue;
					}
					if ( 'email' === $p && false !== strpos( $url, '@' ) && 0 !== strpos( $url, 'mailto:' ) ) {
						$url = 'mailto:' . $url;
					}
					$socials[ $p ] = $url;
				}
				if ( $socials ) :
					$sicons = array(
						'github'  => '<svg viewBox="0 0 24 24" width="1em" height="1em" class="text-[1.5rem]"><path fill="currentColor" d="M12 2A10 10 0 0 0 2 12c0 4.42 2.87 8.17 6.84 9.5c.5.08.66-.23.66-.5v-1.69c-2.77.6-3.36-1.34-3.36-1.34c-.46-1.16-1.11-1.47-1.11-1.47c-.91-.62.07-.6.07-.6c1 .07 1.53 1.03 1.53 1.03c.87 1.52 2.34 1.07 2.91.83c.09-.65.35-1.09.63-1.34c-2.22-.25-4.55-1.11-4.55-4.92c0-1.11.38-2 1.03-2.71c-.1-.25-.45-1.29.1-2.64c0 0 .84-.27 2.75 1.02c.79-.22 1.65-.33 2.5-.33s1.71.11 2.5.33c1.91-1.29 2.75-1.02 2.75-1.02c.55 1.35.2 2.39.1 2.64c.65.71 1.03 1.6 1.03 2.71c0 3.82-2.34 4.66-4.57 4.91c.36.31.69.92.69 1.85V21c0 .27.16.59.67.5C19.14 20.16 22 16.42 22 12A10 10 0 0 0 12 2"/></svg>',
						'twitter' => '<svg viewBox="0 0 24 24" width="1em" height="1em" class="text-[1.5rem]"><path fill="currentColor" d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>',
						'email'   => '<svg viewBox="0 0 24 24" width="1em" height="1em" class="text-[1.5rem]"><path fill="currentColor" d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2m0 4l-8 5-8-5V6l8 5 8-5z"/></svg>',
						'rss'     => '<svg viewBox="0 0 24 24" width="1em" height="1em" class="text-[1.5rem]"><path fill="currentColor" d="M6.18 17.82a2.18 2.18 0 1 0 0-4.36 2.18 2.18 0 0 0 0 4.36M4 4.44v2.83c7.03 0 12.73 5.7 12.73 12.73h2.83c0-8.59-6.97-15.56-15.56-15.56m0 5.66v2.83c3.9 0 7.07 3.17 7.07 7.07h2.83c0-5.47-4.43-9.9-9.9-9.9"/></svg>',
					);
					?>
				<div class="flex flex-wrap gap-2 justify-center mb-1">
					<?php foreach ( $socials as $name => $url ) : ?>
					<a rel="me" aria-label="<?php echo esc_attr( ucfirst( $name ) ); ?>" href="<?php echo esc_url( $url ); ?>" target="_blank" class="btn-regular rounded-lg h-10 w-10 active:scale-90"><?php echo isset( $sicons[ $name ] ) ? $sicons[ $name ] : ''; // phpcs:ignore ?></a>
					<?php endforeach; ?>
				</div>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}
}

if ( ! function_exists( 'mizuki_render_left_sidebar' ) ) {
	/**
	 * 输出左侧边栏(第 1 栏)。
	 */
	function mizuki_render_left_sidebar() {
		?>
<div class="contents">
	<div id="sidebar" class="onload-animation block md:block md:mb-4 md:max-w-[17.5rem] w-full">
		<div id="sidebar-sticky" class="transition-all duration-700 flex flex-col w-full gap-4 sticky top-4">
			<?php
			mizuki_profile_card();

			// 公告。
			$announcement = get_theme_mod( 'mizuki_announcement', __( '欢迎来到我的小站 ✨', 'mizuki' ) );
			if ( $announcement ) {
				mizuki_widget_open( __( '公告', 'mizuki' ), 100 );
				echo '<div class="text-sm text-neutral-700 dark:text-neutral-300 leading-relaxed">' . wp_kses_post( $announcement ) . '</div>';
				mizuki_widget_close();
			}

			// 分类。
			mizuki_left_categories_widget();

			// 标签。
			mizuki_left_tags_widget();

			// WordPress 小工具区(如有)。
			if ( is_active_sidebar( 'sidebar-1' ) ) {
				dynamic_sidebar( 'sidebar-1' );
			}
			?>
		</div>
	</div>
</div>
		<?php
	}
}

if ( ! function_exists( 'mizuki_left_categories_widget' ) ) {
	/**
	 * 分类卡片(链接 + 计数)。
	 */
	function mizuki_left_categories_widget() {
		$cats = get_categories( array( 'hide_empty' => true ) );
		if ( ! $cats ) {
			return;
		}
		mizuki_widget_open( __( '分类', 'mizuki' ), 150 );
		echo '<div class="flex flex-col gap-1">';
		foreach ( $cats as $cat ) {
			printf(
				'<a href="%1$s" class="flex justify-between items-center py-2 px-2 rounded-lg gap-4 transition hover:bg-[var(--btn-plain-bg-hover)] hover:text-[var(--primary)] text-neutral-700 dark:text-neutral-300"><span class="truncate">%2$s</span><span class="text-50 text-sm">%3$d</span></a>',
				esc_url( get_category_link( $cat ) ),
				esc_html( $cat->name ),
				(int) $cat->count
			);
		}
		echo '</div>';
		mizuki_widget_close();
	}
}

if ( ! function_exists( 'mizuki_left_tags_widget' ) ) {
	/**
	 * 标签卡片(pill 链接)。
	 */
	function mizuki_left_tags_widget() {
		$tags = get_tags( array( 'hide_empty' => true, 'number' => 50 ) );
		if ( ! $tags ) {
			return;
		}
		mizuki_widget_open( __( '标签', 'mizuki' ), 200 );
		echo '<div class="flex gap-2 flex-wrap">';
		foreach ( $tags as $tag ) {
			printf(
				'<a href="%1$s" class="btn-regular h-8 text-sm px-3 rounded-lg transition flex items-center">%2$s</a>',
				esc_url( get_tag_link( $tag ) ),
				esc_html( $tag->name )
			);
		}
		echo '</div>';
		mizuki_widget_close();
	}
}

if ( ! function_exists( 'mizuki_render_right_sidebar' ) ) {
	/**
	 * 输出右侧边栏(第 3 栏,lg+ 可见):站点统计 + 分类。
	 */
	function mizuki_render_right_sidebar() {
		?>
<div id="right-sidebar" class="w-full sidebar-column-root hidden lg:block">
	<div id="right-sidebar-sticky" class="transition-all duration-700 flex flex-col w-full gap-4 sticky top-4">
		<?php
		mizuki_site_stats_widget();
		mizuki_calendar_widget();
		?>
	</div>
</div>
		<?php
	}
}

if ( ! function_exists( 'mizuki_site_stats_widget' ) ) {
	/**
	 * 站点统计卡片(文章/分类/标签/字数/运行天数/最近更新)。
	 */
	function mizuki_site_stats_widget() {
		$posts_count = (int) wp_count_posts()->publish;
		$cats_count  = (int) wp_count_terms( array( 'taxonomy' => 'category', 'hide_empty' => true ) );
		$tags_count  = (int) wp_count_terms( array( 'taxonomy' => 'post_tag', 'hide_empty' => true ) );

		// 运行天数(自最早文章)。
		$oldest = get_posts( array( 'numberposts' => 1, 'order' => 'ASC', 'orderby' => 'date', 'post_status' => 'publish', 'fields' => 'ids' ) );
		$running_days = 0;
		if ( $oldest ) {
			$first = get_the_date( 'U', $oldest[0] );
			$running_days = max( 1, (int) floor( ( time() - $first ) / DAY_IN_SECONDS ) );
		}

		// 最近更新。
		$last = get_posts( array( 'numberposts' => 1, 'orderby' => 'modified', 'order' => 'DESC', 'post_status' => 'publish', 'fields' => 'ids' ) );
		$last_update = $last ? get_the_modified_date( get_option( 'date_format' ), $last[0] ) : '—';

		// 总字数(缓存 12h)。
		$total_words = get_transient( 'mizuki_total_words' );
		if ( false === $total_words ) {
			$total_words = 0;
			$ids = get_posts( array( 'numberposts' => 500, 'post_status' => 'publish', 'fields' => 'ids' ) );
			foreach ( $ids as $pid ) {
				$total_words += str_word_count( wp_strip_all_tags( get_post_field( 'post_content', $pid ) ) );
			}
			set_transient( 'mizuki_total_words', $total_words, 12 * HOUR_IN_SECONDS );
		}
		$words_disp = $total_words >= 1000 ? round( $total_words / 1000, 1 ) . 'k' : (string) $total_words;

		mizuki_widget_open( __( '站点统计', 'mizuki' ), 200 );
		echo '<div class="flex flex-col gap-1">';
		mizuki_stat_row( 'article', __( '文章', 'mizuki' ), (string) $posts_count );
		mizuki_stat_row( 'folder', __( '分类', 'mizuki' ), (string) $cats_count );
		mizuki_stat_row( 'label', __( '标签', 'mizuki' ), (string) $tags_count );
		mizuki_stat_row( 'words', __( '总字数', 'mizuki' ), $words_disp );
		mizuki_stat_row( 'calendar', __( '运行天数', 'mizuki' ), (string) $running_days, 'running-days' );
		mizuki_stat_row( 'heart', __( '最近更新', 'mizuki' ), $last_update, 'last-update' );
		echo '</div>';
		mizuki_widget_close();
	}
}

if ( ! function_exists( 'mizuki_calendar_widget' ) ) {
	/**
	 * 日历卡片(当前月网格,高亮今天)。
	 */
	function mizuki_calendar_widget() {
		$now           = current_time( 'timestamp' ); // phpcs:ignore WordPress.DateTime.CurrentTimeTimestamp
		$year          = (int) wp_date( 'Y', $now );
		$month         = (int) wp_date( 'n', $now );
		$today         = (int) wp_date( 'j', $now );
		$days_in_month = (int) wp_date( 't', $now );
		$first_dow     = (int) wp_date( 'w', mktime( 0, 0, 0, $month, 1, $year ) );

		mizuki_widget_open( wp_date( 'Y M', $now ), 250 );
		echo '<div class="w-full">';
		echo '<div class="grid grid-cols-7 gap-1 text-center text-xs text-neutral-400 mb-1">';
		foreach ( array( '日', '一', '二', '三', '四', '五', '六' ) as $d ) {
			echo '<div class="py-1">' . esc_html( $d ) . '</div>';
		}
		echo '</div>';
		echo '<div class="grid grid-cols-7 gap-1 text-center text-sm">';
		for ( $i = 0; $i < $first_dow; $i++ ) {
			echo '<div></div>';
		}
		for ( $day = 1; $day <= $days_in_month; $day++ ) {
			$cls = 'py-1 rounded-md transition ' . ( $day === $today ? 'bg-[var(--primary)] text-white font-bold' : 'text-neutral-700 dark:text-neutral-300 hover:bg-[var(--btn-plain-bg-hover)]' );
			echo '<div class="' . esc_attr( $cls ) . '">' . (int) $day . '</div>';
		}
		echo '</div></div>';
		mizuki_widget_close();
	}
}
