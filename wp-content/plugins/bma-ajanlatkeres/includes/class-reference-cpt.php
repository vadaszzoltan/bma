<?php
/**
 * Reference custom post type.
 *
 * @package BMA_Ajanlatkeres
 */

defined( 'ABSPATH' ) || exit;

final class BMA_AJ_Reference_CPT {

	public const POST_TYPE = 'bma_reference';
	public const TAXONOMY  = 'bma_reference_category';

	/**
	 * Init hooks.
	 */
	public static function init(): void {
		add_action( 'init', array( self::class, 'register' ) );
		add_action( 'add_meta_boxes', array( self::class, 'add_meta_boxes' ) );
		add_action( 'save_post_' . self::POST_TYPE, array( self::class, 'save_meta' ), 10, 2 );
	}

	/**
	 * Register CPT.
	 */
	public static function register(): void {
		register_post_type(
			self::POST_TYPE,
			array(
				'labels'       => array(
					'name'          => __( 'Referenciák', 'bma-ajanlatkeres' ),
					'singular_name' => __( 'Referencia', 'bma-ajanlatkeres' ),
				),
				'public'       => true,
				'has_archive'  => true,
				'rewrite'      => array( 'slug' => 'referencia' ),
				'supports'     => array( 'title', 'editor', 'excerpt', 'thumbnail' ),
				'menu_icon'    => 'dashicons-portfolio',
				'show_in_rest' => true,
			)
		);

		register_taxonomy(
			self::TAXONOMY,
			self::POST_TYPE,
			array(
				'labels'       => array(
					'name' => __( 'Referencia kategóriák', 'bma-ajanlatkeres' ),
				),
				'public'       => true,
				'hierarchical' => true,
				'rewrite'      => array( 'slug' => 'referencia-kategoria' ),
				'show_in_rest' => true,
			)
		);
	}

	/**
	 * Add meta boxes.
	 */
	public static function add_meta_boxes(): void {
		add_meta_box(
			'bma_reference_details',
			__( 'Referencia adatok', 'bma-ajanlatkeres' ),
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
		wp_nonce_field( 'bma_reference_meta', 'bma_reference_nonce' );

		$project_type = get_post_meta( $post->ID, '_bma_project_type', true );
		$location     = get_post_meta( $post->ID, '_bma_location', true );
		$year         = get_post_meta( $post->ID, '_bma_year', true );
		$related      = get_post_meta( $post->ID, '_bma_related_services', true ) ?: array();

		include BMA_AJ_PLUGIN_DIR . 'templates/admin/reference-meta-box.php';
	}

	/**
	 * Save meta.
	 *
	 * @param int $post_id Post ID.
	 */
	public static function save_meta( int $post_id ): void {
		if ( ! isset( $_POST['bma_reference_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['bma_reference_nonce'] ) ), 'bma_reference_meta' ) ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		update_post_meta( $post_id, '_bma_project_type', sanitize_text_field( wp_unslash( $_POST['bma_project_type'] ?? '' ) ) );
		update_post_meta( $post_id, '_bma_location', sanitize_text_field( wp_unslash( $_POST['bma_location'] ?? '' ) ) );
		update_post_meta( $post_id, '_bma_year', sanitize_text_field( wp_unslash( $_POST['bma_year'] ?? '' ) ) );

		$services = isset( $_POST['bma_related_services'] ) ? array_map( 'absint', (array) wp_unslash( $_POST['bma_related_services'] ) ) : array();
		update_post_meta( $post_id, '_bma_related_services', $services );
	}
}
