<?php
/**
 * Analytics integration (GA4/GTM).
 *
 * @package BMA_Ajanlatkeres
 */

defined( 'ABSPATH' ) || exit;

final class BMA_AJ_Analytics {

	/**
	 * Init hooks.
	 */
	public static function init(): void {
		add_action( 'wp_head', array( self::class, 'output_head_scripts' ), 20 );
		add_action( 'wp_footer', array( self::class, 'output_event_bridge' ), 20 );
	}

	/**
	 * Output GA4/GTM scripts (respects cookie consent).
	 */
	public static function output_head_scripts(): void {
		$ga4 = get_option( 'bma_ga4_id', '' );
		$gtm = get_option( 'bma_gtm_id', '' );

		if ( $gtm ) {
			echo "<!-- BMA GTM -->\n";
			echo '<script>window.dataLayer=window.dataLayer||[];</script>' . "\n";
			echo '<script type="text/plain" data-cookie-category="analytics" id="bma-gtm">' . "\n";
			echo "(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','" . esc_js( $gtm ) . "');\n";
			echo "</script>\n";
		}

		if ( $ga4 && ! $gtm ) {
			echo '<script type="text/plain" data-cookie-category="analytics" async src="https://www.googletagmanager.com/gtag/js?id=' . esc_attr( $ga4 ) . '"></script>' . "\n";
			echo '<script type="text/plain" data-cookie-category="analytics">window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag("js",new Date());gtag("config","' . esc_js( $ga4 ) . '");</script>' . "\n";
		}
	}

	/**
	 * Event bridge for frontend JS.
	 */
	public static function output_event_bridge(): void {
		echo '<script>window.bmaTrackEvent=function(n,p){if(typeof gtag==="function")gtag("event",n,p||{});if(window.dataLayer)window.dataLayer.push(Object.assign({event:n},p||{}));};</script>';
	}

	/**
	 * Track server-side event (logged for now, can push to queue).
	 *
	 * @param string               $event Event name.
	 * @param array<string, mixed> $data  Event data.
	 */
	public static function track_event( string $event, array $data = array() ): void {
		do_action( 'bma_analytics_event', $event, $data );
	}
}
