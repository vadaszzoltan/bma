<?php
/**
 * BMA Engineering Child Theme (parent: Ollie)
 *
 * @package BMA_Engineering
 */

defined( 'ABSPATH' ) || exit;

define( 'BMA_THEME_VERSION', '1.15.0-redesign-exp' );
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

	// Design CSS from package (overwrite freely). WP chrome lives in bma-v2-integration.css.
	$v2 = BMA_THEME_DIR . '/assets/css/bma-v2.css';
	if ( file_exists( $v2 ) ) {
		wp_enqueue_style(
			'bma-v2',
			BMA_THEME_URI . '/assets/css/bma-v2.css',
			array( 'bma-theme-layout' ),
			(string) filemtime( $v2 )
		);
	}

	$v2_integration = BMA_THEME_DIR . '/assets/css/bma-v2-integration.css';
	if ( file_exists( $v2 ) && file_exists( $v2_integration ) ) {
		wp_enqueue_style(
			'bma-v2-integration',
			BMA_THEME_URI . '/assets/css/bma-v2-integration.css',
			array( 'bma-v2' ),
			(string) filemtime( $v2_integration )
		);
	}

	// Redesign fonts (Manrope / Source Sans 3) + Latin Extended for RO diacritics.
	wp_enqueue_style(
		'bma-v2-fonts',
		'https://fonts.googleapis.com/css2?family=Manrope:wght@600;700;800&family=Source+Sans+3:ital,wght@0,400;0,600;0,700;1,400&display=swap',
		array(),
		null
	);

	$mobile_nav = BMA_THEME_DIR . '/assets/js/mobile-nav.js';
	if ( file_exists( $mobile_nav ) ) {
		wp_enqueue_script(
			'bma-mobile-nav',
			BMA_THEME_URI . '/assets/js/mobile-nav.js',
			array(),
			(string) filemtime( $mobile_nav ),
			true
		);
		wp_localize_script(
			'bma-mobile-nav',
			'bmaMobileNav',
			array(
				'openLabel'  => __( 'Menü megnyitása', 'bma-engineering' ),
				'closeLabel' => __( 'Menü bezárása', 'bma-engineering' ),
			)
		);
	}
}
add_action( 'wp_enqueue_scripts', 'bma_enqueue_styles', 20 );

/**
 * Add hreflang x-default pointing at the default-language translation of the current URL.
 */
function bma_output_hreflang_x_default(): void {
	if ( is_admin() || ! function_exists( 'pll_default_language' ) || ! function_exists( 'pll_get_post' ) ) {
		return;
	}

	$default = pll_default_language( 'slug' );
	if ( ! $default ) {
		return;
	}

	$url = '';

	if ( is_singular() ) {
		$post_id = get_queried_object_id();
		if ( ! $post_id ) {
			return;
		}
		$tr_id = pll_get_post( $post_id, $default );
		if ( ! $tr_id ) {
			return;
		}
		$url = get_permalink( $tr_id );
	} elseif ( is_home() || is_front_page() ) {
		if ( function_exists( 'pll_home_url' ) ) {
			$url = pll_home_url( $default );
		}
	} elseif ( is_category() || is_tag() || is_tax() ) {
		$term = get_queried_object();
		if ( $term && ! empty( $term->term_id ) && function_exists( 'pll_get_term' ) ) {
			$tr = pll_get_term( (int) $term->term_id, $default );
			if ( $tr ) {
				$url = get_term_link( (int) $tr );
			}
		}
	}

	if ( ! $url || is_wp_error( $url ) ) {
		return;
	}

	printf(
		'<link rel="alternate" href="%s" hreflang="x-default" />' . "\n",
		esc_url( $url )
	);
}
add_action( 'wp_head', 'bma_output_hreflang_x_default', 2 );

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
 * Load theme textdomain for the current Polylang locale.
 * after_setup_theme is too early (locale still default); unload+JIT can miss the .mo.
 */
function bma_load_textdomain_for_locale(): void {
	$locale = determine_locale();
	$mofile = BMA_THEME_DIR . '/languages/bma-engineering-' . $locale . '.mo';
	if ( ! is_readable( $mofile ) ) {
		$mofile = WP_LANG_DIR . '/themes/bma-engineering-' . $locale . '.mo';
	}
	if ( ! is_readable( $mofile ) ) {
		return;
	}
	unload_textdomain( 'bma-engineering' );
	load_textdomain( 'bma-engineering', $mofile );
}
add_action( 'init', 'bma_load_textdomain_for_locale', 20 );
add_action( 'change_locale', 'bma_load_textdomain_for_locale' );

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
 * URL of a brand asset in assets/img/brand/.
 */
function bma_brand_asset_url( string $file ): string {
	return BMA_THEME_URI . '/assets/img/brand/' . $file;
}

/**
 * Favicon / app icons from the brand package, unless a Site Icon is set in the Customizer.
 */
function bma_output_brand_icons(): void {
	if ( has_site_icon() ) {
		return;
	}
	printf( '<link rel="icon" href="%s" sizes="any">' . "\n", esc_url( bma_brand_asset_url( 'favicon.ico' ) ) );
	foreach ( array( 16, 32, 48 ) as $size ) {
		printf( '<link rel="icon" type="image/png" sizes="%1$dx%1$d" href="%2$s">' . "\n", (int) $size, esc_url( bma_brand_asset_url( "favicon-{$size}.png" ) ) );
	}
	printf( '<link rel="apple-touch-icon" href="%s">' . "\n", esc_url( bma_brand_asset_url( 'apple-touch-icon-180.png' ) ) );
}
add_action( 'wp_head', 'bma_output_brand_icons', 5 );
add_action( 'admin_head', 'bma_output_brand_icons', 5 );
add_action( 'login_head', 'bma_output_brand_icons', 5 );

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
 * Polylang-aware page permalink by HU slug.
 *
 * Finds the page via slug, then resolves the current-language translation
 * with pll_get_post(). Falls back to the original page if no translation.
 *
 * @param string $slug Page slug (e.g. szolgaltatasok, referenciak, kapcsolat).
 */
function bma_page_url( string $slug ): string {
	$slug = trim( $slug, '/' );
	if ( '' === $slug ) {
		return home_url( '/' );
	}

	// Prefer plugin page map when available (same logical keys as HU slugs).
	if ( class_exists( 'BMA_AJ_I18n' ) ) {
		$url = BMA_AJ_I18n::page_url( $slug );
		if ( is_string( $url ) && '' !== $url && $url !== home_url( '/' ) ) {
			return $url;
		}
	}

	$page = get_page_by_path( $slug, OBJECT, 'page' );
	if ( ! $page instanceof WP_Post ) {
		return home_url( '/' );
	}

	return bma_post_url( (int) $page->ID );
}

/**
 * Polylang-aware permalink for any post/CPT (bma_service, bma_reference, …).
 *
 * @param int $post_id Post ID in any language.
 */
function bma_post_url( int $post_id ): string {
	if ( $post_id <= 0 ) {
		return home_url( '/' );
	}

	$id = $post_id;
	if ( function_exists( 'pll_get_post' ) ) {
		$translated = pll_get_post( $id );
		if ( $translated ) {
			$id = (int) $translated;
		}
	}

	$url = get_permalink( $id );
	return $url ? $url : home_url( '/' );
}

/**
 * Fallback primary menu.
 */
function bma_fallback_menu(): void {
	echo '<ul class="bma-menu">';
	$items = array(
		'fooldal'        => __( 'Főoldal', 'bma-engineering' ),
		'szolgaltatasok' => __( 'Szolgáltatások', 'bma-engineering' ),
		'referenciak'    => __( 'Referenciák', 'bma-engineering' ),
		'kapcsolat'      => __( 'Kapcsolat', 'bma-engineering' ),
	);
	foreach ( $items as $key => $label ) {
		printf( '<li><a href="%s">%s</a></li>', esc_url( bma_page_url( $key ) ), esc_html( $label ) );
	}
	echo '</ul>';
}

/**
 * Fallback footer menu.
 */
function bma_footer_fallback_menu(): void {
	bma_fallback_menu();
}

/**
 * Sticky mobile CTA bar (homepage + services list + service detail + quote flow).
 */
function bma_render_mobilebar(): void {
	$quote_url = bma_page_url( 'ajanlatkeres' );
	$label     = __( 'Ajánlatkérés', 'bma-engineering' );
	$title     = __( 'Kérjen ajánlatot', 'bma-engineering' );
	$sub       = __( 'Díjmentes, kötelezettség nélkül', 'bma-engineering' );
	$context   = bma_mobilebar_context();

	if ( '' === $context ) {
		return;
	}
	if ( 'service' === $context ) {
		$service_id = (int) get_queried_object_id();
		$title      = get_the_title( $service_id );
		$quote_url  = add_query_arg( 'service', $service_id, $quote_url );
	} elseif ( 'reference' === $context ) {
		$ref_id  = (int) get_queried_object_id();
		$related = class_exists( 'BMA_AJ_Reference_CPT' ) ? BMA_AJ_Reference_CPT::get_related_services( $ref_id ) : array();
		$title   = get_the_title( $ref_id );
		$sub     = $related ? implode( ' · ', wp_list_pluck( $related, 'post_title' ) ) : __( 'Referencia projekt', 'bma-engineering' );
	}
	?>
	<div class="mobilebar" data-bma-mobilebar="<?php echo esc_attr( $context ); ?>">
		<span class="mobilebar-info">
			<strong><?php echo esc_html( $title ); ?></strong>
			<span><?php echo esc_html( $sub ); ?></span>
		</span>
		<a href="<?php echo esc_url( $quote_url ); ?>" class="btn btn--primary btn--sm"><?php echo esc_html( $label ); ?></a>
	</div>
	<?php
}

/**
 * Which sticky mobile CTA bar the current page gets ('' = none).
 * The quote page has its own step navigation, so it gets none.
 */
function bma_mobilebar_context(): string {
	$is_page = static function ( string $key, array $slugs ): bool {
		return class_exists( 'BMA_AJ_I18n' ) ? BMA_AJ_I18n::is_page( $key ) : is_page( $slugs );
	};
	if ( is_front_page() ) {
		return 'home';
	}
	if ( is_singular( 'bma_service' ) ) {
		return 'service';
	}
	if ( is_singular( 'bma_reference' ) ) {
		return 'reference';
	}
	if ( $is_page( 'referenciak', array( 'referenciak', 'referinte', 'references' ) ) ) {
		return 'references';
	}
	if ( $is_page( 'szolgaltatasok', array( 'szolgaltatasok', 'servicii', 'services' ) ) ) {
		return 'services';
	}
	return '';
}

/**
 * Body class when the sticky mobile bar is present.
 *
 * @param string[] $classes Body classes.
 * @return string[]
 */
function bma_mobilebar_body_class( array $classes ): array {
	if ( '' !== bma_mobilebar_context() ) {
		$classes[] = 'has-mobilebar';
	}
	return $classes;
}
add_filter( 'body_class', 'bma_mobilebar_body_class' );

/**
 * Render Polylang language switcher (.langswitch markup).
 *
 * @param string $extra_class Optional extra class (e.g. langswitch--light).
 */
function bma_render_langswitch( string $extra_class = '' ): void {
	if ( ! function_exists( 'pll_the_languages' ) ) {
		return;
	}

	$langs = pll_the_languages(
		array(
			'raw'                    => 1,
			'force_home'             => 0,
			'hide_if_empty'          => 0,
			'hide_if_no_translation' => 0,
			'hide_current'           => 0,
		)
	);

	if ( empty( $langs ) || ! is_array( $langs ) ) {
		return;
	}

	$classes = trim( 'bma-lang-switcher langswitch ' . $extra_class );
	?>
	<div class="<?php echo esc_attr( $classes ); ?>" role="group" aria-label="<?php esc_attr_e( 'Nyelv választása', 'bma-engineering' ); ?>">
		<?php foreach ( $langs as $lang ) : ?>
			<?php
			$slug  = isset( $lang['slug'] ) ? (string) $lang['slug'] : '';
			if ( '' === $slug ) {
				continue;
			}
			$label = strtoupper( $slug );
			$url   = isset( $lang['url'] ) ? (string) $lang['url'] : '';
			$cur   = ! empty( $lang['current_lang'] );
			$off   = ! empty( $lang['no_translation'] );
			?>
			<?php if ( $off && ! $cur ) : ?>
				<span class="lang lang--off" lang="<?php echo esc_attr( $slug ); ?>"><?php echo esc_html( $label ); ?></span>
			<?php elseif ( $cur ) : ?>
				<a
					href="<?php echo esc_url( $url ); ?>"
					class="lang is-on"
					lang="<?php echo esc_attr( $slug ); ?>"
					hreflang="<?php echo esc_attr( $slug ); ?>"
					aria-current="true"
				><?php echo esc_html( $label ); ?></a>
			<?php else : ?>
				<a
					href="<?php echo esc_url( $url ); ?>"
					class="lang"
					lang="<?php echo esc_attr( $slug ); ?>"
					hreflang="<?php echo esc_attr( $slug ); ?>"
				><?php echo esc_html( $label ); ?></a>
			<?php endif; ?>
		<?php endforeach; ?>
	</div>
	<?php
}
