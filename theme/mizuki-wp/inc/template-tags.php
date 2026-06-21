<?php
/**
 * 模板辅助函数。
 *
 * @package Mizuki
 */
defined( 'ABSPATH' ) || exit;

/**
 * 估算当前文章阅读时间(分钟,按 ~300 中文字/分钟)。
 *
 * @param int|null $post_id 文章 ID,默认当前文章。
 * @return int 阅读分钟数(至少 1)。
 */
function mizuki_reading_time( $post_id = null ) {
	$content = get_post_field( 'post_content', $post_id ?: get_the_ID() );
	$text    = wp_strip_all_tags( $content );
	$count   = mb_strlen( preg_replace( '/\s+/u', '', $text ), 'UTF-8' );
	return max( 1, (int) ceil( $count / 300 ) );
}

/**
 * 统计当前文章字数(中文)。
 *
 * @param int|null $post_id 文章 ID,默认当前文章。
 * @return int 字数。
 */
function mizuki_word_count( $post_id = null ) {
	$content = get_post_field( 'post_content', $post_id ?: get_the_ID() );
	$text    = wp_strip_all_tags( $content );
	return mb_strlen( preg_replace( '/\s+/u', '', $text ), 'UTF-8' );
}

/**
 * 分类筛选标签(project_category / skill_category)的默认显示名映射。
 *
 * 这些是各模板原先硬编码的默认值,迁出来集中维护,作为 theme_mod 覆盖的回退基线。
 *
 * @param string $taxonomy 'project_category' 或 'skill_category'。
 * @return array slug => 显示名。
 */
function mizuki_default_category_labels( $taxonomy ) {
	$defaults = array(
		'project_category' => array(
			'web'     => 'Web',
			'mobile'  => '移动端',
			'desktop' => '桌面端',
			'other'   => '其他',
		),
		'skill_category'   => array(
			'frontend' => '前端',
			'backend'  => '后端',
			'database' => '数据库',
			'tools'    => '工具',
			'other'    => '其他',
		),
	);
	return isset( $defaults[ $taxonomy ] ) ? $defaults[ $taxonomy ] : array();
}

/**
 * 分类筛选标签的默认图标映射(内联 SVG 字符串,直接 echo 到筛选 Tab)。
 *
 * @param string $taxonomy 'project_category' 或 'skill_category'。
 * @return array slug => 内联 SVG 字符串。键 'other' 必定存在,作为未知 slug 的回退。
 */
function mizuki_default_category_icons( $taxonomy ) {
	$svg_other = '<svg viewBox="0 0 24 24" width="1em" height="1em" class="text-base w-4 h-4"><path fill="currentColor" d="M12 2l-5.5 9h11L12 2zm0 3.84L13.93 9h-3.87L12 5.84zM17.5 13c-2.49 0-4.5 2.01-4.5 4.5s2.01 4.5 4.5 4.5 4.5-2.01 4.5-4.5-2.01-4.5-4.5-4.5zm0 7c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5zM3 21.5h8v-8H3v8zm2-6h4v4H5v-4z"/></svg>';
	$defaults = array(
		'project_category' => array(
			'web'     => '<svg viewBox="0 0 24 24" width="1em" height="1em" class="text-base w-4 h-4"><path fill="currentColor" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/></svg>',
			'mobile'  => '<svg viewBox="0 0 24 24" width="1em" height="1em" class="text-base w-4 h-4"><path fill="currentColor" d="M17 1.01L7 1c-1.1 0-2 .9-2 2v18c0 1.1.9 2 2 2h10c1.1 0 2-.9 2-2V3c0-1.1-.9-1.99-2-1.99zM17 19H7V5h10v14z"/></svg>',
			'desktop' => '<svg viewBox="0 0 24 24" width="1em" height="1em" class="text-base w-4 h-4"><path fill="currentColor" d="M21 2H3c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h7v2H8v2h8v-2h-2v-2h7c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm0 14H3V4h18v12z"/></svg>',
			'other'   => $svg_other,
		),
		'skill_category'   => array(
			'frontend' => '<svg viewBox="0 0 24 24" width="1em" height="1em" class="text-base w-4 h-4"><path fill="currentColor" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/></svg>',
			'backend'  => '<svg viewBox="0 0 24 24" width="1em" height="1em" class="text-base w-4 h-4"><path fill="currentColor" d="M20 13H4c-.55 0-1 .45-1 1v6c0 .55.45 1 1 1h16c.55 0 1-.45 1-1v-6c0-.55-.45-1-1-1zM7 19c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zM20 3H4c-.55 0-1 .45-1 1v6c0 .55.45 1 1 1h16c.55 0 1-.45 1-1V4c0-.55-.45-1-1-1zM7 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2z"/></svg>',
			'database' => '<svg viewBox="0 0 24 24" width="1em" height="1em" class="text-base w-4 h-4"><path fill="currentColor" d="M2 20h20v-4H2v4zm2-3h2v2H4v-2zM2 4v4h20V4H2zm4 3H4V5h2v2zm-4 7h20v-4H2v4zm2-3h2v2H4v-2z"/></svg>',
			'tools'    => '<svg viewBox="0 0 24 24" width="1em" height="1em" class="text-base w-4 h-4"><path fill="currentColor" d="M22.7 19l-9.1-9.1c.9-2.3.4-5-1.5-6.9-2-2-5-2.4-7.4-1.3L9 6 6 9 1.6 4.7C.4 7.1.9 10.1 2.9 12.1c1.9 1.9 4.6 2.4 6.9 1.5l9.1 9.1c.4.4 1 .4 1.4 0l2.3-2.3c.5-.4.5-1.1.1-1.4z"/></svg>',
			'other'    => $svg_other,
		),
	);
	return isset( $defaults[ $taxonomy ] ) ? $defaults[ $taxonomy ] : array( 'other' => $svg_other );
}

/**
 * 取得分类显示名映射:theme_mod 覆盖叠加在硬编码默认值之上。
 *
 * theme_mod 键名:mizuki_{taxonomy}_labels(slug => 显示名 的关联数组)。
 *
 * @param string $taxonomy 'project_category' 或 'skill_category'。
 * @return array slug => 显示名。
 */
function mizuki_get_category_labels( $taxonomy ) {
	$defaults = mizuki_default_category_labels( $taxonomy );
	$override = get_theme_mod( 'mizuki_' . $taxonomy . '_labels', array() );
	if ( ! is_array( $override ) ) {
		$override = array();
	}
	return array_merge( $defaults, $override );
}

/**
 * 取得分类图标映射:theme_mod 覆盖叠加在硬编码默认值之上。
 *
 * theme_mod 键名:mizuki_{taxonomy}_icons(slug => 内联 SVG 的关联数组)。
 *
 * @param string $taxonomy 'project_category' 或 'skill_category'。
 * @return array slug => 内联 SVG 字符串(必含 'other' 回退键)。
 */
function mizuki_get_category_icons( $taxonomy ) {
	$icons    = mizuki_default_category_icons( $taxonomy );
	$override = get_theme_mod( 'mizuki_' . $taxonomy . '_icons', array() );
	if ( ! is_array( $override ) ) {
		$override = array();
	}
	// 控制台里用户填写的是图标 class(如 devicon-html5-plain,与 _mizuki_skill_icon 字段一致),
	// 这里转成可直接 echo 的 <i> HTML,保持模板"echo 映射值"的统一契约。
	foreach ( $override as $slug => $icon_class ) {
		$icon_class = trim( (string) $icon_class );
		if ( '' === $icon_class ) {
			continue;
		}
		$icons[ $slug ] = '<i class="' . esc_attr( $icon_class ) . ' text-base w-4 h-4"></i>';
	}
	return $icons;
}

/**
 * 输出分类筛选条(category-bar):Home + 归档 + 各分类胶囊。
 * 与原版 Mizuki 首页主内容区上方的 #category-bar 一致。
 */
function mizuki_category_bar() {
	$cats = get_categories( array( 'hide_empty' => true, 'number' => 30 ) );
	if ( ! $cats ) {
		return;
	}
	$archive_url   = get_permalink( get_option( 'page_for_posts' ) );
	if ( ! $archive_url ) {
		$archive_url = home_url( '/' );
	}
	$total         = (int) wp_count_posts()->publish;
	$current_cat   = is_category() ? (int) get_queried_object_id() : 0;
	$home_active   = ( is_home() || is_front_page() ) ? ' data-active' : '';
	$home_icon     = '<svg width="1em" height="1em" viewBox="0 0 24 24" class="text-lg"><path fill="currentColor" d="M4 21V9l8-6l8 6v12h-6v-7h-4v7z"/></svg>';
	?>
	<div class="card-base category-bar p-3 onload-animation" id="category-bar">
		<div class="category-bar-inner flex gap-2">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="category-pill text-sm px-2 py-1 rounded-lg flex-shrink-0 transition-colors duration-200 flex items-center justify-center" aria-label="<?php esc_attr_e( '首页', 'mizuki' ); ?>"<?php echo $home_active; // phpcs:ignore ?>><?php echo $home_icon; // phpcs:ignore ?></a>
			<a href="<?php echo esc_url( $archive_url ); ?>" class="category-pill text-sm px-3 py-1 rounded-lg whitespace-nowrap flex-shrink-0 transition-colors duration-200 flex items-center justify-center"><?php esc_html_e( '归档', 'mizuki' ); ?> <span class="text-xs opacity-60 ml-1"><?php echo (int) $total; ?></span></a>
			<div class="category-divider flex-shrink-0"></div>
			<div class="category-scroll flex gap-2 overflow-x-auto min-w-0 flex-1">
				<?php foreach ( $cats as $cat ) : ?>
				<a href="<?php echo esc_url( get_category_link( $cat ) ); ?>" class="category-pill text-sm px-3 py-1 rounded-lg whitespace-nowrap transition-colors duration-200 flex items-center justify-center"<?php echo ( $current_cat === (int) $cat->term_id ) ? ' data-active' : ''; ?>><?php echo esc_html( $cat->name ); ?> <span class="text-xs opacity-60 ml-1"><?php echo (int) $cat->count; ?></span></a>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
	<?php
}
