<?php
/**
 * Service custom post type.
 *
 * @package BMA_Ajanlatkeres
 */

defined( 'ABSPATH' ) || exit;

final class BMA_AJ_Service_CPT {

	public const POST_TYPE = 'bma_service';
	public const TAXONOMY  = 'bma_service_category';

	/**
	 * Init hooks.
	 */
	public static function init(): void {
		add_action( 'init', array( self::class, 'register' ) );
		add_action( 'add_meta_boxes', array( self::class, 'add_meta_boxes' ) );
		add_action( 'save_post_' . self::POST_TYPE, array( self::class, 'save_meta' ), 10, 2 );
	}

	/**
	 * Register CPT and taxonomy.
	 */
	public static function register(): void {
		register_post_type(
			self::POST_TYPE,
			array(
				'labels'       => array(
					'name'          => __( 'Szolgáltatások', 'bma-ajanlatkeres' ),
					'singular_name' => __( 'Szolgáltatás', 'bma-ajanlatkeres' ),
					'add_new_item'  => __( 'Új szolgáltatás', 'bma-ajanlatkeres' ),
					'edit_item'     => __( 'Szolgáltatás szerkesztése', 'bma-ajanlatkeres' ),
				),
				'public'       => true,
				'has_archive'  => true,
				'rewrite'      => array( 'slug' => 'szolgaltatas' ),
				'supports'     => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes' ),
				'menu_icon'    => 'dashicons-hammer',
				'show_in_rest' => true,
			)
		);

		register_taxonomy(
			self::TAXONOMY,
			self::POST_TYPE,
			array(
				'labels'       => array(
					'name' => __( 'Szolgáltatás kategóriák', 'bma-ajanlatkeres' ),
				),
				'public'       => true,
				'hierarchical' => true,
				'rewrite'      => array( 'slug' => 'szolgaltatas-kategoria' ),
				'show_in_rest' => true,
			)
		);
	}

	/**
	 * Add meta boxes.
	 */
	public static function add_meta_boxes(): void {
		add_meta_box(
			'bma_service_settings',
			__( 'Szolgáltatás beállítások', 'bma-ajanlatkeres' ),
			array( self::class, 'render_meta_box' ),
			self::POST_TYPE,
			'normal',
			'high'
		);
	}

	/**
	 * Render meta box.
	 *
	 * @param WP_Post $post Post object.
	 */
	public static function render_meta_box( WP_Post $post ): void {
		wp_nonce_field( 'bma_service_meta', 'bma_service_nonce' );

		$is_active       = get_post_meta( $post->ID, '_bma_is_active', true );
		$sort_order      = get_post_meta( $post->ID, '_bma_sort_order', true );
		$client_info     = get_post_meta( $post->ID, '_bma_client_info', true );
		$admin_note      = get_post_meta( $post->ID, '_bma_admin_note', true );
		$short_desc      = get_post_meta( $post->ID, '_bma_short_description', true );
		$use_placeholder = get_post_meta( $post->ID, '_bma_use_placeholder', true );
		$choose_summary  = get_post_meta( $post->ID, '_bma_choose_summary', true );
		$when_needed     = get_post_meta( $post->ID, '_bma_when_needed', true );
		$needs_required  = get_post_meta( $post->ID, '_bma_needs_required', true );
		$needs_optional  = get_post_meta( $post->ID, '_bma_needs_optional', true );
		$needs_later     = get_post_meta( $post->ID, '_bma_needs_later', true );
		$faq             = get_post_meta( $post->ID, '_bma_faq', true );

		include BMA_AJ_PLUGIN_DIR . 'templates/admin/service-meta-box.php';
	}

	/**
	 * Save meta.
	 *
	 * @param int     $post_id Post ID.
	 * @param WP_Post $post    Post object.
	 */
	public static function save_meta( int $post_id, WP_Post $post ): void {
		if ( ! isset( $_POST['bma_service_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['bma_service_nonce'] ) ), 'bma_service_meta' ) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$fields = array(
			'_bma_is_active'           => isset( $_POST['bma_is_active'] ) ? '1' : '0',
			'_bma_sort_order'          => absint( $_POST['bma_sort_order'] ?? 0 ),
			'_bma_client_info'         => sanitize_textarea_field( wp_unslash( $_POST['bma_client_info'] ?? '' ) ),
			'_bma_admin_note'          => sanitize_textarea_field( wp_unslash( $_POST['bma_admin_note'] ?? '' ) ),
			'_bma_short_description'   => sanitize_textarea_field( wp_unslash( $_POST['bma_short_description'] ?? '' ) ),
			'_bma_choose_summary'      => sanitize_textarea_field( wp_unslash( $_POST['bma_choose_summary'] ?? '' ) ),
			'_bma_when_needed'         => sanitize_textarea_field( wp_unslash( $_POST['bma_when_needed'] ?? '' ) ),
			'_bma_needs_required'      => sanitize_textarea_field( wp_unslash( $_POST['bma_needs_required'] ?? '' ) ),
			'_bma_needs_optional'      => sanitize_textarea_field( wp_unslash( $_POST['bma_needs_optional'] ?? '' ) ),
			'_bma_needs_later'         => sanitize_textarea_field( wp_unslash( $_POST['bma_needs_later'] ?? '' ) ),
			'_bma_faq'                 => sanitize_textarea_field( wp_unslash( $_POST['bma_faq'] ?? '' ) ),
			'_bma_use_placeholder'     => isset( $_POST['bma_use_placeholder'] ) ? '1' : '0',
		);

		foreach ( $fields as $key => $value ) {
			update_post_meta( $post_id, $key, $value );
		}
	}

	/**
	 * Split a newline-separated meta value into non-empty lines.
	 *
	 * @param string $raw Meta value.
	 * @return string[]
	 */
	public static function lines_from_meta( string $raw ): array {
		$lines = preg_split( '/\r\n|\r|\n/', $raw ) ?: array();
		$out   = array();
		foreach ( $lines as $line ) {
			$line = trim( (string) $line );
			if ( '' !== $line ) {
				$out[] = $line;
			}
		}
		return $out;
	}

	/**
	 * Parse FAQ meta (K: / V: blocks) into question/answer pairs.
	 *
	 * @param string $raw Meta value.
	 * @return array<int, array{q:string,a:string}>
	 */
	public static function faq_from_meta( string $raw ): array {
		$raw = trim( $raw );
		if ( '' === $raw ) {
			return array();
		}

		$pairs = array();
		$blocks = preg_split( '/\n\s*\n/', $raw ) ?: array();
		foreach ( $blocks as $block ) {
			$block = trim( $block );
			if ( '' === $block ) {
				continue;
			}
			$q = '';
			$a = '';
			if ( preg_match( '/^K:\s*(.+?)(?:\nV:\s*([\s\S]+))?$/u', $block, $m ) ) {
				$q = trim( $m[1] );
				$a = isset( $m[2] ) ? trim( $m[2] ) : '';
			} elseif ( preg_match( '/^(.+?)\n([\s\S]+)$/u', $block, $m ) ) {
				$q = trim( $m[1] );
				$a = trim( $m[2] );
			}
			if ( '' !== $q && '' !== $a ) {
				$pairs[] = array(
					'q' => $q,
					'a' => $a,
				);
			}
		}
		return $pairs;
	}

	/**
	 * Get active services.
	 *
	 * @return WP_Post[]
	 */
	public static function get_active_services(): array {
		return get_posts(
			array(
				'post_type'      => self::POST_TYPE,
				'posts_per_page' => -1,
				'post_status'    => 'publish',
				'meta_query'     => array(
					array(
						'key'   => '_bma_is_active',
						'value' => '1',
					),
				),
				'orderby'        => 'meta_value_num',
				'meta_key'       => '_bma_sort_order',
				'order'          => 'ASC',
			)
		);
	}

	/**
	 * Get related services for detail page.
	 *
	 * @param int $post_id Current service ID.
	 * @return WP_Post[]
	 */
	public static function get_related_services( int $post_id ): array {
		$related = get_post_meta( $post_id, '_bma_related_services', true );
		if ( is_array( $related ) && ! empty( $related ) ) {
			$posts = get_posts(
				array(
					'post_type'      => self::POST_TYPE,
					'post__in'       => array_map( 'absint', $related ),
					'posts_per_page' => 6,
					'post_status'    => 'publish',
					'orderby'        => 'post__in',
				)
			);
			if ( $posts ) {
				return $posts;
			}
		}

		return get_posts(
			array(
				'post_type'      => self::POST_TYPE,
				'posts_per_page' => 4,
				'post_status'    => 'publish',
				'post__not_in'   => array( $post_id ),
				'meta_query'     => array(
					array(
						'key'   => '_bma_is_active',
						'value' => '1',
					),
				),
				'orderby'        => 'meta_value_num',
				'meta_key'       => '_bma_sort_order',
				'order'          => 'ASC',
			)
		);
	}
}
