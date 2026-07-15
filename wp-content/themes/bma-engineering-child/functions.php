<?php
/**
 * BMA Engineering Child Theme
 *
 * A megvásárolt WordPress template child theme-je.
 * Alapértelmezett parent: Twenty Twenty-Four (telepítés után cserélhető).
 *
 * @package BMA_Engineering
 */

defined( 'ABSPATH' ) || exit;

define( 'BMA_THEME_VERSION', '1.0.0' );
define( 'BMA_THEME_DIR', get_stylesheet_directory() );
define( 'BMA_THEME_URI', get_stylesheet_directory_uri() );

/**
 * Enqueue child theme styles.
 */
function bma_enqueue_styles(): void {
	wp_enqueue_style(
		'bma-child-style',
		get_stylesheet_uri(),
		array(),
		BMA_THEME_VERSION
	);

	wp_enqueue_style(
		'bma-theme-layout',
		BMA_THEME_URI . '/assets/css/theme.css',
		array( 'bma-child-style' ),
		BMA_THEME_VERSION
	);
}
add_action( 'wp_enqueue_scripts', 'bma_enqueue_styles' );

/**
 * Register navigation menus.
 */
function bma_register_menus(): void {
	register_nav_menus(
		array(
			'primary' => __( 'Főmenü', 'bma-engineering' ),
			'footer'  => __( 'Lábléc menü', 'bma-engineering' ),
		)
	);
}
add_action( 'after_setup_theme', 'bma_register_menus' );

/**
 * Theme supports.
 */
function bma_theme_setup(): void {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'custom-logo', array(
		'height'      => 80,
		'width'       => 240,
		'flex-height' => true,
		'flex-width'  => true,
	) );
	load_theme_textdomain( 'bma-engineering', BMA_THEME_DIR . '/languages' );
}
add_action( 'after_setup_theme', 'bma_theme_setup' );

/**
 * Output robots meta for protected areas.
 */
function bma_robots_meta(): void {
	if ( is_page( 'ugyfelportal' ) || is_page( 'ajanlatkosar' ) ) {
		echo '<meta name="robots" content="noindex, nofollow">' . "\n";
	}
}
add_action( 'wp_head', 'bma_robots_meta', 1 );

/**
 * Register widget areas.
 */
function bma_widgets_init(): void {
	register_sidebar(
		array(
			'name'          => __( 'Lábléc', 'bma-engineering' ),
			'id'            => 'footer-1',
			'before_widget' => '<div class="bma-footer-widget">',
			'after_widget'  => '</div>',
		)
	);
}
add_action( 'widgets_init', 'bma_widgets_init' );

/**
 * Fallback primary menu.
 */
function bma_fallback_menu(): void {
	echo '<ul class="bma-menu">';
	$items = array(
		'/'                => __( 'Főoldal', 'bma-engineering' ),
		'/szolgaltatasok/' => __( 'Szolgáltatások', 'bma-engineering' ),
		'/referenciak/'    => __( 'Referenciák', 'bma-engineering' ),
		'/kapcsolat/'      => __( 'Kapcsolat', 'bma-engineering' ),
	);
	foreach ( $items as $path => $label ) {
		printf( '<li><a href="%s">%s</a></li>', esc_url( home_url( $path ) ), esc_html( $label ) );
	}
	echo '</ul>';
}

/**
 * Fallback footer menu.
 */
function bma_footer_fallback_menu(): void {
	bma_fallback_menu();
}
