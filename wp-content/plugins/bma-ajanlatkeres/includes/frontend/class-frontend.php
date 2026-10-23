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
		if ( is_singular( array( BMA_AJ_Service_CPT::POST_TYPE, BMA_AJ_Reference_CPT::POST_TYPE ) ) ) {
			// The parent block theme's resolved single template already queued its
			// own <title>; our PHP templates get it from title-tag support instead.
			remove_action( 'wp_head', '_block_template_render_title_tag', 1 );
		}

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
	 * Output featured image or placeholder.
	 *
	 * @param int    $post_id     Post ID.
	 * @param string $placeholder Placeholder text.
	 * @param string $size        Image size.
	 * @param string $class_extra Extra CSS class for placeholder.
	 */
	public static function render_featured_or_placeholder( int $post_id, string $placeholder, string $size = 'large', string $class_extra = '' ): void {
		if ( has_post_thumbnail( $post_id ) ) {
			$thumb_id = get_post_thumbnail_id( $post_id );
			$alt      = get_post_meta( $thumb_id, '_wp_attachment_image_alt', true );
			if ( ! $alt ) {
				$alt = get_the_title( $post_id );
			}
			echo wp_get_attachment_image(
				(int) $thumb_id,
				$size,
				false,
				array(
					'class'    => trim( 'bma-featured-img ' . $class_extra ),
					'alt'      => $alt,
					'loading'  => 'lazy',
					'decoding' => 'async',
				)
			);
			return;
		}

		printf(
			'<div class="bma-placeholder-img %s" role="img" aria-label="%s">%s</div>',
			esc_attr( trim( $class_extra ) ),
			esc_attr( $placeholder ),
			esc_html( $placeholder )
		);
	}

	/**
	 * Output basic SEO meta tags.
	 * Skipped when Rank Math (or another dedicated SEO plugin) is active.
	 */
	public static function output_seo_meta(): void {
		if ( defined( 'RANK_MATH_VERSION' ) || class_exists( 'RankMath' ) || defined( 'WPSEO_VERSION' ) ) {
			return;
		}

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
				if ( has_post_thumbnail( $post ) ) {
					$img = wp_get_attachment_image_url( get_post_thumbnail_id( $post ), 'large' );
					if ( $img ) {
						echo '<meta property="og:image" content="' . esc_url( $img ) . '">' . "\n";
					}
				}
			}
		} elseif ( is_front_page() ) {
			$hero_id = absint( get_option( BMA_AJ_Media_Seeder::HERO_OPTION, 0 ) );
			if ( $hero_id ) {
				$img = wp_get_attachment_image_url( $hero_id, 'large' );
				$alt = get_post_meta( $hero_id, '_wp_attachment_image_alt', true );
				echo '<meta name="description" content="' . esc_attr__( 'BMA Engineering – műszaki tervezés, engedélyeztetés és dokumentáció lakossági és üzleti projektekhez.', 'bma-ajanlatkeres' ) . '">' . "\n";
				if ( $img ) {
					echo '<meta property="og:image" content="' . esc_url( $img ) . '">' . "\n";
				}
				if ( $alt ) {
					echo '<meta property="og:image:alt" content="' . esc_attr( $alt ) . '">' . "\n";
				}
			}
		}
	}
}
