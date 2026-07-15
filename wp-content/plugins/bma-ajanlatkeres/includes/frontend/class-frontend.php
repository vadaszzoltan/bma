<?php
/**
 * Frontend rendering and templates.
 *
 * @package BMA_Ajanlatkeres
 */

defined( 'ABSPATH' ) || exit;

final class BMA_AJ_Frontend {

	/**
	 * Init hooks.
	 */
	public static function init(): void {
		add_filter( 'template_include', array( self::class, 'template_include' ) );
		add_action( 'wp_head', array( self::class, 'output_seo_meta' ), 1 );
	}

	/**
	 * Load plugin templates for CPTs.
	 *
	 * @param string $template Template path.
	 */
	public static function template_include( string $template ): string {
		if ( is_singular( BMA_AJ_Service_CPT::POST_TYPE ) ) {
			$custom = BMA_AJ_PLUGIN_DIR . 'templates/single-bma_service.php';
			if ( file_exists( $custom ) ) {
				return $custom;
			}
		}

		if ( is_singular( BMA_AJ_Reference_CPT::POST_TYPE ) ) {
			$custom = BMA_AJ_PLUGIN_DIR . 'templates/single-bma_reference.php';
			if ( file_exists( $custom ) ) {
				return $custom;
			}
		}

		return $template;
	}

	/**
	 * Output basic SEO meta tags.
	 */
	public static function output_seo_meta(): void {
		if ( is_singular() ) {
			$post = get_queried_object();
			if ( $post instanceof WP_Post ) {
				$desc = $post->post_excerpt ?: wp_trim_words( wp_strip_all_tags( $post->post_content ), 30 );
				echo '<meta name="description" content="' . esc_attr( $desc ) . '">' . "\n";
				echo '<link rel="canonical" href="' . esc_url( get_permalink( $post ) ) . '">' . "\n";
				echo '<meta property="og:title" content="' . esc_attr( $post->post_title ) . '">' . "\n";
				echo '<meta property="og:description" content="' . esc_attr( $desc ) . '">' . "\n";
				echo '<meta property="og:url" content="' . esc_url( get_permalink( $post ) ) . '">' . "\n";
				echo '<meta property="og:type" content="website">' . "\n";
			}
		}
	}
}
