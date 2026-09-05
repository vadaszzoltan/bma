<?php
/**
 * Shortcodes for page content.
 *
 * @package BMA_Ajanlatkeres
 */

defined( 'ABSPATH' ) || exit;

final class BMA_AJ_Shortcodes {

	/**
	 * Init hooks.
	 */
	public static function init(): void {
		add_shortcode( 'bma_services_list', array( self::class, 'services_list' ) );
		add_shortcode( 'bma_references_list', array( self::class, 'references_list' ) );
		add_shortcode( 'bma_quote_cart', array( self::class, 'quote_cart' ) );
		add_shortcode( 'bma_quote_form', array( self::class, 'quote_form' ) );
		add_shortcode( 'bma_client_portal', array( self::class, 'client_portal' ) );
		add_shortcode( 'bma_contact_form', array( self::class, 'contact_form' ) );
		add_shortcode( 'bma_home_hero', array( self::class, 'home_hero' ) );
		add_shortcode( 'bma_home_services', array( self::class, 'home_services' ) );
		add_shortcode( 'bma_home_multi', array( self::class, 'home_multi' ) );
		add_shortcode( 'bma_home_benefits', array( self::class, 'home_benefits' ) );
		add_shortcode( 'bma_home_process', array( self::class, 'home_process' ) );
		add_shortcode( 'bma_home_references', array( self::class, 'home_references' ) );
		add_shortcode( 'bma_home_about', array( self::class, 'home_about' ) );
		add_shortcode( 'bma_home_closing', array( self::class, 'home_closing' ) );
		add_shortcode( 'bma_social_links', array( self::class, 'social_links' ) );
		add_shortcode( 'bma_contact_info', array( self::class, 'contact_info' ) );
		add_shortcode( 'bma_payment', array( self::class, 'payment_page' ) );
		add_shortcode( 'bma_privacy_policy', array( self::class, 'privacy_policy' ) );
		add_shortcode( 'bma_cookie_policy', array( self::class, 'cookie_policy' ) );
		add_shortcode( 'bma_terms', array( self::class, 'terms' ) );
		add_shortcode( 'bma_login', array( self::class, 'login_form' ) );
	}

	/**
	 * Services list shortcode.
	 */
	public static function services_list(): string {
		ob_start();
		include BMA_AJ_PLUGIN_DIR . 'templates/frontend/services-list.php';
		return ob_get_clean();
	}

	/**
	 * References list shortcode.
	 */
	public static function references_list(): string {
		ob_start();
		include BMA_AJ_PLUGIN_DIR . 'templates/frontend/references-list.php';
		return ob_get_clean();
	}

	/**
	 * Quote cart shortcode.
	 */
	public static function quote_cart(): string {
		ob_start();
		include BMA_AJ_PLUGIN_DIR . 'templates/frontend/quote-cart.php';
		return ob_get_clean();
	}

	/**
	 * Quote submission form shortcode.
	 */
	public static function quote_form(): string {
		ob_start();
		include BMA_AJ_PLUGIN_DIR . 'templates/frontend/quote-form.php';
		return ob_get_clean();
	}

	/**
	 * Client portal shortcode.
	 */
	public static function client_portal(): string {
		if ( ! is_user_logged_in() ) {
			ob_start();
			include BMA_AJ_PLUGIN_DIR . 'templates/frontend/login-prompt.php';
			return ob_get_clean();
		}

		ob_start();
		include BMA_AJ_PLUGIN_DIR . 'templates/frontend/client-portal.php';
		return ob_get_clean();
	}

	/**
	 * Contact form shortcode.
	 */
	public static function contact_form(): string {
		ob_start();
		include BMA_AJ_PLUGIN_DIR . 'templates/frontend/contact-form.php';
		return ob_get_clean();
	}

	/**
	 * Home hero section.
	 */
	public static function home_hero(): string {
		ob_start();
		include BMA_AJ_PLUGIN_DIR . 'templates/frontend/home-hero.php';
		return ob_get_clean();
	}

	/**
	 * Home services section.
	 */
	public static function home_services(): string {
		ob_start();
		include BMA_AJ_PLUGIN_DIR . 'templates/frontend/home-services.php';
		return ob_get_clean();
	}

	/**
	 * Home multi-service callout.
	 */
	public static function home_multi(): string {
		ob_start();
		include BMA_AJ_PLUGIN_DIR . 'templates/frontend/home-multi.php';
		return ob_get_clean();
	}

	/**
	 * Home benefits section.
	 */
	public static function home_benefits(): string {
		ob_start();
		include BMA_AJ_PLUGIN_DIR . 'templates/frontend/home-benefits.php';
		return ob_get_clean();
	}

	/**
	 * Home process section.
	 */
	public static function home_process(): string {
		ob_start();
		include BMA_AJ_PLUGIN_DIR . 'templates/frontend/home-process.php';
		return ob_get_clean();
	}

	/**
	 * Home references section.
	 */
	public static function home_references(): string {
		ob_start();
		include BMA_AJ_PLUGIN_DIR . 'templates/frontend/home-references.php';
		return ob_get_clean();
	}

	/**
	 * Home about section.
	 */
	public static function home_about(): string {
		ob_start();
		include BMA_AJ_PLUGIN_DIR . 'templates/frontend/home-about.php';
		return ob_get_clean();
	}

	/**
	 * Home closing CTA.
	 */
	public static function home_closing(): string {
		ob_start();
		include BMA_AJ_PLUGIN_DIR . 'templates/frontend/home-closing.php';
		return ob_get_clean();
	}

	/**
	 * Social links shortcode.
	 */
	public static function social_links(): string {
		$links = array(
			'facebook'  => get_option( 'bma_social_facebook', '' ),
			'linkedin'  => get_option( 'bma_social_linkedin', '' ),
			'instagram' => get_option( 'bma_social_instagram', '' ),
			'x'         => get_option( 'bma_social_x', '' ),
			'youtube'   => get_option( 'bma_social_youtube', '' ),
		);

		$links = array_filter( $links );
		if ( empty( $links ) ) {
			return '';
		}

		ob_start();
		echo '<ul class="bma-social-links" role="list">';
		foreach ( $links as $platform => $url ) {
			printf(
				'<li><a href="%s" target="_blank" rel="noopener noreferrer" aria-label="%s">%s</a></li>',
				esc_url( $url ),
				esc_attr( ucfirst( $platform ) ),
				esc_html( ucfirst( $platform ) )
			);
		}
		echo '</ul>';
		return ob_get_clean();
	}

	/**
	 * Contact info block.
	 */
	public static function contact_info(): string {
		ob_start();
		include BMA_AJ_PLUGIN_DIR . 'templates/frontend/contact-info.php';
		return ob_get_clean();
	}

	/**
	 * Payment page shortcode.
	 */
	public static function payment_page(): string {
		ob_start();
		include BMA_AJ_PLUGIN_DIR . 'templates/frontend/payment-page.php';
		return ob_get_clean();
	}

	/**
	 * Privacy policy shortcode.
	 */
	public static function privacy_policy(): string {
		return BMA_AJ_Legal_Content::privacy_html();
	}

	/**
	 * Cookie policy shortcode.
	 */
	public static function cookie_policy(): string {
		return BMA_AJ_Legal_Content::cookie_html();
	}

	/**
	 * Terms of use shortcode.
	 */
	public static function terms(): string {
		return BMA_AJ_Legal_Content::terms_html();
	}

	/**
	 * Frontend login form shortcode.
	 */
	public static function login_form(): string {
		ob_start();
		include BMA_AJ_PLUGIN_DIR . 'templates/frontend/login-form.php';
		return ob_get_clean();
	}
}
