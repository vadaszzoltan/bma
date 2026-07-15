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
			'_bma_is_active'          => isset( $_POST['bma_is_active'] ) ? '1' : '0',
			'_bma_sort_order'         => absint( $_POST['bma_sort_order'] ?? 0 ),
			'_bma_client_info'          => sanitize_textarea_field( wp_unslash( $_POST['bma_client_info'] ?? '' ) ),
			'_bma_admin_note'           => sanitize_textarea_field( wp_unslash( $_POST['bma_admin_note'] ?? '' ) ),
			'_bma_short_description'    => sanitize_textarea_field( wp_unslash( $_POST['bma_short_description'] ?? '' ) ),
			'_bma_use_placeholder'      => isset( $_POST['bma_use_placeholder'] ) ? '1' : '0',
		);

		foreach ( $fields as $key => $value ) {
			update_post_meta( $post_id, $key, $value );
		}
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
}
