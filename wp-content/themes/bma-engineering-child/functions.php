<?php
/**
 * BMA Engineering Child Theme (parent: Ollie)
 *
 * @package BMA_Engineering
 */

defined( 'ABSPATH' ) || exit;

define( 'BMA_THEME_VERSION', '1.13.0-redesign-exp' );
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
	$is_services = class_exists( 'BMA_AJ_I18n' )
		? BMA_AJ_I18n::is_page( 'szolgaltatasok' )
		: is_page( array( 'szolgaltatasok', 'servicii', 'services' ) );
	$is_cart = class_exists( 'BMA_AJ_I18n' )
		? BMA_AJ_I18n::is_page( 'ajanlatkosar' )
		: is_page( array( 'ajanlatkosar', 'cos-oferta', 'quote-cart' ) );
	$is_form = class_exists( 'BMA_AJ_I18n' )
		? BMA_AJ_I18n::is_page( 'ajanlatkeres' )
		: is_page( array( 'ajanlatkeres', 'cerere-oferta', 'quote-request' ) );

	if ( is_front_page() ) {
		$svc_url = bma_page_url( 'szolgaltatasok' );
		?>
		<div class="mobilebar" data-bma-mobilebar="home">
			<span class="mobilebar-info">
				<strong><?php esc_html_e( 'Kérjen ajánlatot', 'bma-engineering' ); ?></strong>
				<span><?php esc_html_e( 'Díjmentes, kötelezettség nélkül', 'bma-engineering' ); ?></span>
			</span>
			<a href="<?php echo esc_url( $svc_url ); ?>" class="btn btn--primary btn--sm"><?php esc_html_e( 'Ajánlatkérés', 'bma-engineering' ); ?></a>
		</div>
		<?php
		return;
	}

	if ( is_singular( 'bma_service' ) ) {
		$service_id = (int) get_queried_object_id();
		$title      = get_the_title( $service_id );
		$items      = class_exists( 'BMA_AJ_Dynamic_Fields' )
			? BMA_AJ_Dynamic_Fields::get_service_items( $service_id )
			: array();
		$count_label = sprintf(
			/* translators: %d: number of selectable items */
			__( '%d választható részfeladat', 'bma-engineering' ),
			count( $items )
		);
		$list_url = bma_page_url( 'szolgaltatasok' );
		?>
		<div class="mobilebar" data-bma-mobilebar="service">
			<span class="mobilebar-info">
				<strong><?php echo esc_html( $title ); ?></strong>
				<span data-bma-mobilebar-sub><?php echo esc_html( $count_label ); ?></span>
			</span>
			<a href="#bma-service-items" class="btn btn--primary btn--sm" data-bma-mobilebar-cta><?php esc_html_e( 'Hozzáadás', 'bma-engineering' ); ?></a>
			<a href="<?php echo esc_url( $list_url ); ?>" class="visually-hidden" data-bma-nojs-service-list><?php esc_html_e( 'Szolgáltatások', 'bma-engineering' ); ?></a>
		</div>
		<?php
		return;
	}

	$count = class_exists( 'BMA_AJ_Cart' ) ? (int) BMA_AJ_Cart::item_count() : 0;
	$count_label = ( 1 === $count )
		? __( '1 tétel kiválasztva', 'bma-engineering' )
		: sprintf(
			/* translators: %d: selected item count */
			__( '%d tétel kiválasztva', 'bma-engineering' ),
			$count
		);

	if ( $is_cart ) {
		$form_url = bma_page_url( 'ajanlatkeres' );
		$svc_url  = bma_page_url( 'szolgaltatasok' );
		$cta_url  = $count ? $form_url : $svc_url;
		$cta_label = $count
			? __( 'Tovább az adatokhoz', 'bma-engineering' )
			: __( 'Szolgáltatások', 'bma-engineering' );
		?>
		<div class="mobilebar" data-bma-mobilebar="cart">
			<span class="mobilebar-info">
				<strong><?php echo esc_html( $count_label ); ?></strong>
				<span><?php esc_html_e( '02 · Kosár', 'bma-engineering' ); ?></span>
			</span>
			<?php if ( $count ) : ?>
				<button type="submit" form="bma-quote-cart-form" class="btn btn--primary btn--sm"><?php echo esc_html( $cta_label ); ?></button>
			<?php else : ?>
				<a href="<?php echo esc_url( $cta_url ); ?>" class="btn btn--primary btn--sm"><?php echo esc_html( $cta_label ); ?></a>
			<?php endif; ?>
		</div>
		<?php
		return;
	}

	if ( $is_form ) {
		$submitted = isset( $_GET['submitted'] );
		if ( $submitted ) {
			return;
		}
		?>
		<div class="mobilebar" data-bma-mobilebar="form">
			<span class="mobilebar-info">
				<strong><?php echo esc_html( $count_label ); ?></strong>
				<span><?php esc_html_e( '03 · Adatok és beküldés', 'bma-engineering' ); ?></span>
			</span>
			<?php if ( $count ) : ?>
				<button type="submit" form="bma-quote-submit-form" class="btn btn--primary btn--sm"><?php esc_html_e( 'Beküldés', 'bma-engineering' ); ?></button>
			<?php else : ?>
				<a href="<?php echo esc_url( bma_page_url( 'szolgaltatasok' ) ); ?>" class="btn btn--primary btn--sm"><?php esc_html_e( 'Szolgáltatások', 'bma-engineering' ); ?></a>
			<?php endif; ?>
		</div>
		<?php
		return;
	}

	if ( ! $is_services ) {
		return;
	}

	$cart_url = bma_page_url( 'ajanlatkosar' );
	?>
	<div class="mobilebar" data-bma-mobilebar="services">
		<span class="mobilebar-info">
			<strong data-bma-mobilebar-count><?php echo esc_html( $count_label ); ?></strong>
			<span><?php esc_html_e( 'Az ajánlatkérés díjmentes', 'bma-engineering' ); ?></span>
		</span>
		<a
			href="<?php echo esc_url( $cart_url ); ?>"
			class="btn btn--primary btn--sm<?php echo $count ? '' : ' is-disabled'; ?>"
			data-bma-mobilebar-cta
			<?php echo $count ? '' : ' aria-disabled="true" tabindex="-1"'; ?>
		><?php esc_html_e( 'Tovább', 'bma-engineering' ); ?></a>
	</div>
	<?php
}

/**
 * Body class when the sticky mobile bar is present.
 *
 * @param string[] $classes Body classes.
 * @return string[]
 */
function bma_mobilebar_body_class( array $classes ): array {
	$is_services = class_exists( 'BMA_AJ_I18n' )
		? BMA_AJ_I18n::is_page( 'szolgaltatasok' )
		: is_page( array( 'szolgaltatasok', 'servicii', 'services' ) );
	$is_cart = class_exists( 'BMA_AJ_I18n' )
		? BMA_AJ_I18n::is_page( 'ajanlatkosar' )
		: is_page( array( 'ajanlatkosar', 'cos-oferta', 'quote-cart' ) );
	$is_form = class_exists( 'BMA_AJ_I18n' )
		? BMA_AJ_I18n::is_page( 'ajanlatkeres' )
		: is_page( array( 'ajanlatkeres', 'cerere-oferta', 'quote-request' ) );

	if ( is_front_page() || $is_services || is_singular( 'bma_service' ) || $is_cart || ( $is_form && empty( $_GET['submitted'] ) ) ) {
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
