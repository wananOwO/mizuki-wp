<?php
/**
 * Customizer 设置面板:Banner、主色 hue、个人资料、社交链接、Live2D。
 *
 * @package Mizuki
 */
defined( 'ABSPATH' ) || exit;

function mizuki_customize_register( $wp_customize ) {
	// === Banner ===
	$wp_customize->add_section( 'mizuki_banner', array(
		'title'    => __( 'Banner 设置', 'mizuki' ),
		'priority' => 30,
	) );
	$wp_customize->add_setting( 'mizuki_banner_image', array(
		'default'           => '',
		'sanitize_callback' => 'esc_url_raw',
	) );
	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'mizuki_banner_image', array(
		'label'   => __( 'Banner 图片', 'mizuki' ),
		'section' => 'mizuki_banner',
	) ) );
	$wp_customize->add_setting( 'mizuki_banner_height', array(
		'default'           => '60vh',
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'mizuki_banner_height', array(
		'label'   => __( 'Banner 高度', 'mizuki' ),
		'section' => 'mizuki_banner',
		'type'    => 'text',
	) );

	// === 主题色 ===
	$wp_customize->add_section( 'mizuki_color', array(
		'title'    => __( '主题色', 'mizuki' ),
		'priority' => 35,
	) );
	$wp_customize->add_setting( 'mizuki_hue', array(
		'default'           => 240,
		'sanitize_callback' => 'absint',
		'transport'         => 'postMessage',
	) );
	$wp_customize->add_control( 'mizuki_hue', array(
		'label'       => __( '主题色相 (Hue)', 'mizuki' ),
		'description' => __( '0-360,默认 240(蓝色)。', 'mizuki' ),
		'section'     => 'mizuki_color',
		'type'        => 'range',
		'input_attrs' => array( 'min' => 0, 'max' => 360, 'step' => 1 ),
	) );
	$wp_customize->add_setting( 'mizuki_hue_fixed', array(
		'default'           => false,
		'sanitize_callback' => 'wp_validate_boolean',
	) );
	$wp_customize->add_control( 'mizuki_hue_fixed', array(
		'label'   => __( '锁定主题色(隐藏访客调色器)', 'mizuki' ),
		'section' => 'mizuki_color',
		'type'    => 'checkbox',
	) );

	// === 个人资料 ===
	$wp_customize->add_section( 'mizuki_profile', array(
		'title'    => __( '个人资料', 'mizuki' ),
		'priority' => 40,
	) );
	$fields = array(
		'mizuki_avatar'   => array( __( '头像 URL', 'mizuki' ), 'esc_url_raw', 'url' ),
		'mizuki_nickname' => array( __( '昵称', 'mizuki' ), 'sanitize_text_field', 'text' ),
		'mizuki_bio'      => array( __( '简介', 'mizuki' ), 'sanitize_text_field', 'textarea' ),
	);
	foreach ( $fields as $id => $cfg ) {
		$wp_customize->add_setting( $id, array( 'default' => '', 'sanitize_callback' => $cfg[1] ) );
		$wp_customize->add_control( $id, array( 'label' => $cfg[0], 'section' => 'mizuki_profile', 'type' => $cfg[2] ) );
	}

	// === 社交链接 ===
	$wp_customize->add_section( 'mizuki_social', array(
		'title'    => __( '社交链接', 'mizuki' ),
		'priority' => 45,
	) );
	foreach ( array( 'github', 'twitter', 'email', 'rss' ) as $p ) {
		$wp_customize->add_setting( "mizuki_social_{$p}", array( 'default' => '', 'sanitize_callback' => 'esc_url_raw' ) );
		$wp_customize->add_control( "mizuki_social_{$p}", array( 'label' => ucfirst( $p ) . ' URL', 'section' => 'mizuki_social', 'type' => 'url' ) );
	}

	// === Live2D ===
	$wp_customize->add_section( 'mizuki_live2d', array(
		'title'    => __( 'Live2D 看板娘', 'mizuki' ),
		'priority' => 50,
	) );
	$wp_customize->add_setting( 'mizuki_live2d_enabled', array( 'default' => false, 'sanitize_callback' => 'wp_validate_boolean' ) );
	$wp_customize->add_control( 'mizuki_live2d_enabled', array( 'label' => __( '启用 Live2D 看板娘', 'mizuki' ), 'section' => 'mizuki_live2d', 'type' => 'checkbox' ) );
}
add_action( 'customize_register', 'mizuki_customize_register' );
