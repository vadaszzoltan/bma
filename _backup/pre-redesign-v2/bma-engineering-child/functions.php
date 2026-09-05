<?php
/**
 * BMA Engineering Child Theme (parent: Ollie)
 *
 * @package BMA_Engineering
 */

defined( 'ABSPATH' ) || exit;

define( 'BMA_THEME_VERSION', '1.12.0' );
define( 'BMA_THEME_DIR', get_stylesheet_directory() );
define( 'BMA_THEME_URI', get_stylesheet_directory_uri() );

/**
 * Enqueue child theme styles on top of Ollie global styles.
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

	// Latin Extended–friendly UI font for Romanian diacritics.
	wp_enqueue_style(
		'bma-ollie-fonts',
		'https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap&subset=latin,latin-ext',
		array(),
		null
	);
}
add_action( 'wp_enqueue_scripts', 'bma_enqueue_styles', 20 );

/**
 * Register navigation menus (used by BMA PHP header/footer).
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
	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' )
	);
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 80,
			'width'       => 240,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);
	load_theme_textdomain( 'bma-engineering', BMA_THEME_DIR . '/languages' );
}
add_action( 'after_setup_theme', 'bma_theme_setup' );

/**
 * Classic editor for post content – keep Site Editor / patterns available for Ollie.
 * Block widgets stay off to avoid admin confusion.
 */
function bma_editor_preferences(): void {
	add_filter( 'use_widgets_block_editor', '__return_false' );
}
add_action( 'after_setup_theme', 'bma_editor_preferences' );

/**
 * Prefer classic editor for CPT content screens, but allow blocks on Pages
 * so Ollie patterns remain usable when editing marketing pages.
 */
function bma_use_classic_for_content( bool $use_block, $post ): bool {
	if ( ! $post instanceof WP_Post ) {
		return $use_block;
	}
	if ( in_array( $post->post_type, array( 'post', 'bma_service', 'bma_reference' ), true ) ) {
		return false;
	}
	return $use_block;
}
add_filter( 'use_block_editor_for_post', 'bma_use_classic_for_content', 100, 2 );

/**
 * Output robots meta for protected areas.
 */
function bma_robots_meta(): void {
	if ( is_page( 'ugyfelportal' ) || is_page( 'ajanlatkosar' ) || is_page( 'fizetes' ) || is_page( 'belepes' ) ) {
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
		'fooldal'         => __( 'Főoldal', 'bma-engineering' ),
		'szolgaltatasok'  => __( 'Szolgáltatások', 'bma-engineering' ),
		'referenciak'     => __( 'Referenciák', 'bma-engineering' ),
		'kapcsolat'       => __( 'Kapcsolat', 'bma-engineering' ),
	);
	foreach ( $items as $key => $label ) {
		$url = class_exists( 'BMA_AJ_I18n' ) ? BMA_AJ_I18n::page_url( $key ) : home_url( '/' );
		printf( '<li><a href="%s">%s</a></li>', esc_url( $url ), esc_html( $label ) );
	}
	echo '</ul>';
}

/**
 * Fallback footer menu.
 */
function bma_footer_fallback_menu(): void {
	bma_fallback_menu();
}
