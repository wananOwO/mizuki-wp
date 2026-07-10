<?php
/**
 * Source-level contracts for layout geometry that must hold before JavaScript runs.
 */

$root       = dirname( __DIR__ );
$sidebar    = file_get_contents( $root . '/theme/mizuki-wp/inc/parts/sidebar.php' );
$layout_css = file_get_contents( $root . '/theme/mizuki-wp/assets/css/mizuki-layout-modes.css' );
$header     = file_get_contents( $root . '/theme/mizuki-wp/header.php' );
$banner     = file_get_contents( $root . '/theme/mizuki-wp/inc/parts/banner.php' );
$index      = file_get_contents( $root . '/theme/mizuki-wp/index.php' );

$failures = array();
$passes   = 0;

function layout_assert( $condition, $message ) {
	global $failures, $passes;

	if ( $condition ) {
		++$passes;
		echo "PASS: {$message}\n";
		return;
	}

	$failures[] = $message;
	echo "FAIL: {$message}\n";
}

function css_rule( $css, $selector ) {
	$pattern = '/' . preg_quote( $selector, '/' ) . '\s*\{([^}]*)\}/s';
	return preg_match( $pattern, $css, $matches ) ? $matches[1] : null;
}

function css_has_declaration( $rule, $property, $value ) {
	if ( null === $rule ) {
		return false;
	}

	$pattern = '/(?:^|;)\s*' . preg_quote( $property, '/' ) . '\s*:\s*' . preg_quote( $value, '/' ) . '\s*(?:;|$)/i';
	return 1 === preg_match( $pattern, $rule );
}

// The wrapper is the grid item. #right-sidebar remains its height-constrained child.
$wrapper_position = strpos( $sidebar, 'class="right-sidebar-container' );
$sidebar_position = strpos( $sidebar, 'id="right-sidebar"', false === $wrapper_position ? 0 : $wrapper_position );
layout_assert( false !== $wrapper_position, 'right sidebar has the upstream wrapper' );
layout_assert(
	false !== $wrapper_position && false !== $sidebar_position && $wrapper_position < $sidebar_position,
	'#right-sidebar is nested inside .right-sidebar-container'
);

$hidden_rule = css_rule( $layout_css, '.hidden-in-grid-mode' );
layout_assert(
	css_has_declaration( $hidden_rule, 'display', 'none !important' ),
	'.hidden-in-grid-mode is always hidden'
);

preg_match_all( '/([^{}]*hidden-in-grid-mode[^{}]*)\{([^{}]*)\}/s', $layout_css, $hidden_rules, PREG_SET_ORDER );
$forced_visible = false;
foreach ( $hidden_rules as $rule ) {
	if ( preg_match( '/display\s*:\s*(?:block|contents|flex|grid|inline)/i', $rule[2] ) ) {
		$forced_visible = true;
		break;
	}
}
layout_assert( ! $forced_visible, 'no hidden grid sidebar rule forces the element visible' );

$desktop_grid_rule = css_rule( $layout_css, '#main-grid[data-layout-mode="grid"]' );
$desktop_main_rule = css_rule( $layout_css, '#main-grid[data-layout-mode="grid"] #swup-container' );
layout_assert(
	css_has_declaration( $desktop_grid_rule, 'grid-template-columns', '17.5rem 1fr !important' ),
	'desktop grid mode uses the left-sidebar/content tracks'
);
layout_assert(
	css_has_declaration( $desktop_main_rule, 'grid-column', '2 / 3' ),
	'grid-mode main content is pinned to column 2'
);
layout_assert(
	1 === preg_match( '/@media\s*\(min-width:\s*1280px\)\s*\{[\s\S]*#main-grid\[data-layout-mode="grid"\]/', $layout_css ),
	'grid geometry is scoped to the 1280px desktop breakpoint'
);

$grid_cover_rule = css_rule( $layout_css, '#post-list-container.grid-mode .post-card-item > a[class*="md:absolute"]' );
layout_assert(
	css_has_declaration( $grid_cover_rule, 'order', '-1' ),
	'grid card cover is ordered above its text content'
);
layout_assert(
	false !== strpos( $index, 'post-card-title' )
	&& false !== strpos( $layout_css, '.post-card-title' )
	&& false === strpos( $layout_css, '.post-card-item a[href]' ),
	'grid title typography is scoped to the title link only'
);

// The fixed image layer and absolutely positioned content must overlap at top:0.
// These selectors deliberately match or outrank conflicting rules in mizuki-banner.css.
$fullscreen_banner_rule = css_rule( $layout_css, 'body.enable-banner.fullscreen-banner #banner-wrapper.w-full' );
$fullscreen_main_rule   = css_rule( $layout_css, 'body.enable-banner.fullscreen-banner .absolute.w-full.z-30:not(.no-banner-layout)' );
layout_assert(
	css_has_declaration( $fullscreen_banner_rule, 'position', 'fixed !important' )
	&& css_has_declaration( $fullscreen_banner_rule, 'top', '0 !important')
	&& css_has_declaration( $fullscreen_banner_rule, 'height', '100vh !important' ),
	'fullscreen image is a fixed viewport layer'
);
layout_assert(
	css_has_declaration( $fullscreen_main_rule, 'position', 'absolute !important' )
	&& css_has_declaration( $fullscreen_main_rule, 'top', '0 !important' )
	&& css_has_declaration( $fullscreen_main_rule, 'margin-top', '0 !important' ),
	'fullscreen content overlays the first viewport without a 100vh spacer'
);
layout_assert(
	false !== strpos( $header, "if ( 'fullscreen' === \$mz_banner_mode )" )
	&& false !== strpos( $header, "\$mz_main_top = '0';" ),
	'server-rendered fullscreen main position is top:0'
);
layout_assert(
	false === strpos( $banner, 'position:fixed;top:0;left:0;height:100vh;z-index:0;' )
	&& 1 === preg_match( '/\$wrapper_style\s*=\s*\'top: -30vh\';/', $banner ),
	'banner geometry has no mode-specific inline fixed state'
);

echo "\n{$passes} passed, " . count( $failures ) . " failed\n";
exit( $failures ? 1 : 0 );
