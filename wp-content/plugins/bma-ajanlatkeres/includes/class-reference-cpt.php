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
		add_action( 'admin_enqueue_scripts', array( self::class, 'enqueue_admin' ) );
	}

	/**
	 * Register CPT + taxonomy.
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
				'supports'     => array( 'title', 'excerpt', 'thumbnail' ),
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
				'show_ui'      => true,
			)
		);
	}

	/**
	 * Admin media picker assets.
	 *
	 * @param string $hook Current admin page.
	 */
	public static function enqueue_admin( string $hook ): void {
		if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
			return;
		}
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
		if ( ! $screen || self::POST_TYPE !== $screen->post_type ) {
			return;
		}
		wp_enqueue_media();
		wp_enqueue_script(
			'bma-aj-admin',
			BMA_AJ_PLUGIN_URL . 'assets/js/admin.js',
			array( 'jquery' ),
			BMA_AJ_VERSION,
			true
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
		$meta    = self::get_all_meta( (int) $post->ID );
		$category = $meta['category'];
		$project_type = $meta['project_type'];
		$location = $meta['location'];
		$year = $meta['year'];
		$project_description = $meta['project_description'];
		$task = $meta['task'];
		$work = $meta['work'];
		$work_done = $meta['work_done'];
		$solution = $meta['solution'];
		$result = $meta['result'];
		$duration = $meta['duration'];
		$gallery = $meta['gallery'];
		$related = $meta['related'];
		$cta_title = $meta['cta_title'];
		$cta_text = $meta['cta_text'];

		include BMA_AJ_PLUGIN_DIR . 'templates/admin/reference-meta-box.php';
	}

	/**
	 * All structured meta for a reference.
	 *
	 * @param int $post_id Post ID.
	 * @return array<string, mixed>
	 */
	public static function get_all_meta( int $post_id ): array {
		$gallery = get_post_meta( $post_id, '_bma_gallery', true );
		if ( is_string( $gallery ) && '' !== $gallery ) {
			$gallery = array_filter( array_map( 'absint', explode( ',', $gallery ) ) );
		} elseif ( ! is_array( $gallery ) ) {
			$gallery = array();
		} else {
			$gallery = array_filter( array_map( 'absint', $gallery ) );
		}

		$related = get_post_meta( $post_id, '_bma_related_services', true );
		if ( ! is_array( $related ) ) {
			$related = array();
		}

		$category = get_post_meta( $post_id, '_bma_category', true );
		if ( ! $category ) {
			$terms = get_the_terms( $post_id, self::TAXONOMY );
			if ( $terms && ! is_wp_error( $terms ) ) {
				$category = $terms[0]->name;
			}
		}

		$project_type = (string) get_post_meta( $post_id, '_bma_project_type', true );
		$category     = (string) $category;

		if ( class_exists( 'BMA_AJ_I18n' ) ) {
			$category     = BMA_AJ_I18n::translate_label( $category );
			$project_type = BMA_AJ_I18n::translate_label( $project_type );
		}

		return array(
			'category'            => $category,
			'project_type'        => $project_type,
			'location'            => (string) get_post_meta( $post_id, '_bma_location', true ),
			'year'                => (string) get_post_meta( $post_id, '_bma_year', true ),
			'project_description' => (string) get_post_meta( $post_id, '_bma_project_description', true ),
			'task'                => (string) get_post_meta( $post_id, '_bma_task', true ),
			'work'                => (string) (
				get_post_meta( $post_id, '_bma_work', true )
					?: get_post_meta( $post_id, '_bma_work_done', true )
			),
			'work_done'           => (string) get_post_meta( $post_id, '_bma_work_done', true ),
			'solution'            => (string) get_post_meta( $post_id, '_bma_solution', true ),
			'result'              => (string) get_post_meta( $post_id, '_bma_result', true ),
			'duration'            => (string) get_post_meta( $post_id, '_bma_duration', true ),
			'gallery'             => array_values( $gallery ),
			'related'             => array_map( 'absint', $related ),
			'cta_title'           => (string) get_post_meta( $post_id, '_bma_cta_title', true ),
			'cta_text'            => (string) get_post_meta( $post_id, '_bma_cta_text', true ),
		);
	}

	/**
	 * Related service posts.
	 *
	 * @param int $post_id Reference ID.
	 * @return WP_Post[]
	 */
	public static function get_related_services( int $post_id ): array {
		$ids = get_post_meta( $post_id, '_bma_related_services', true );
		if ( ! is_array( $ids ) || empty( $ids ) ) {
			return array();
		}
		$ids = array_values( array_filter( array_map( 'absint', $ids ) ) );
		if ( ! $ids ) {
			return array();
		}

		// Related meta stores default-language IDs; resolve to current language when possible.
		if ( class_exists( 'BMA_AJ_I18n' ) && function_exists( 'pll_get_post' ) ) {
			$lang = BMA_AJ_I18n::current_lang();
			$mapped = array();
			foreach ( $ids as $id ) {
				$tr = pll_get_post( $id, $lang );
				$mapped[] = $tr ? (int) $tr : $id;
			}
			$ids = array_values( array_unique( $mapped ) );
		}

		$posts = get_posts(
			array(
				'post_type'      => BMA_AJ_Service_CPT::POST_TYPE,
				'post__in'       => $ids,
				'posts_per_page' => -1,
				'post_status'    => 'publish',
				'orderby'        => 'post__in',
				'lang'           => '', // include resolved IDs regardless of Polylang auto-filter.
			)
		);
		return $posts;
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

		$category = sanitize_text_field( wp_unslash( $_POST['bma_category'] ?? '' ) );
		update_post_meta( $post_id, '_bma_category', $category );
		update_post_meta( $post_id, '_bma_project_type', sanitize_text_field( wp_unslash( $_POST['bma_project_type'] ?? '' ) ) );
		update_post_meta( $post_id, '_bma_location', sanitize_text_field( wp_unslash( $_POST['bma_location'] ?? '' ) ) );
		update_post_meta( $post_id, '_bma_year', sanitize_text_field( wp_unslash( $_POST['bma_year'] ?? '' ) ) );
		update_post_meta( $post_id, '_bma_project_description', sanitize_textarea_field( wp_unslash( $_POST['bma_project_description'] ?? '' ) ) );
		update_post_meta( $post_id, '_bma_task', sanitize_textarea_field( wp_unslash( $_POST['bma_task'] ?? '' ) ) );
		$work = sanitize_textarea_field( wp_unslash( $_POST['bma_work'] ?? '' ) );
		update_post_meta( $post_id, '_bma_work', $work );
		// Keep legacy key in sync for older templates / exports.
		update_post_meta( $post_id, '_bma_work_done', $work );
		update_post_meta( $post_id, '_bma_solution', sanitize_textarea_field( wp_unslash( $_POST['bma_solution'] ?? '' ) ) );
		update_post_meta( $post_id, '_bma_result', sanitize_textarea_field( wp_unslash( $_POST['bma_result'] ?? '' ) ) );
		update_post_meta( $post_id, '_bma_duration', sanitize_text_field( wp_unslash( $_POST['bma_duration'] ?? '' ) ) );
		update_post_meta( $post_id, '_bma_cta_title', sanitize_text_field( wp_unslash( $_POST['bma_cta_title'] ?? '' ) ) );
		update_post_meta( $post_id, '_bma_cta_text', sanitize_textarea_field( wp_unslash( $_POST['bma_cta_text'] ?? '' ) ) );

		$gallery_raw = sanitize_text_field( wp_unslash( $_POST['bma_gallery'] ?? '' ) );
		$gallery_ids = array_values( array_filter( array_map( 'absint', explode( ',', $gallery_raw ) ) ) );
		update_post_meta( $post_id, '_bma_gallery', $gallery_ids );

		$services = isset( $_POST['bma_related_services'] ) ? array_map( 'absint', (array) wp_unslash( $_POST['bma_related_services'] ) ) : array();
		update_post_meta( $post_id, '_bma_related_services', $services );

		if ( $category ) {
			$term = term_exists( $category, self::TAXONOMY );
			if ( ! $term ) {
				$term = wp_insert_term( $category, self::TAXONOMY );
			}
			if ( ! is_wp_error( $term ) ) {
				$term_id = is_array( $term ) ? (int) $term['term_id'] : (int) $term;
				wp_set_object_terms( $post_id, array( $term_id ), self::TAXONOMY, false );
			}
		}
	}

	/**
	 * Split textarea into non-empty lines (for work lists).
	 *
	 * @param string $text Raw text.
	 * @return string[]
	 */
	public static function lines( string $text ): array {
		$out = array();
		foreach ( preg_split( '/\r\n|\r|\n/', $text ) as $line ) {
			$line = trim( $line );
			$line = ltrim( $line, "•\-*–— " );
			if ( '' !== $line ) {
				$out[] = $line;
			}
		}
		return $out;
	}

	/**
	 * Parse "Label: value" lines for .datarow rendering.
	 *
	 * @param string $text Raw work text.
	 * @return array<int, array{label:string,value:string}>
	 */
	public static function labeled_lines( string $text ): array {
		$rows = array();
		foreach ( self::lines( $text ) as $line ) {
			if ( preg_match( '/^([^:：]{1,80})\s*[:：]\s*(.+)$/u', $line, $m ) ) {
				$rows[] = array(
					'label' => trim( $m[1] ),
					'value' => trim( $m[2] ),
				);
			} else {
				$rows[] = array(
					'label' => '',
					'value' => $line,
				);
			}
		}
		return $rows;
	}

	/**
	 * Related references (same category preferred), excluding current.
	 *
	 * @param int $post_id Current reference ID.
	 * @param int $limit   Max posts.
	 * @return WP_Post[]
	 */
	public static function get_related_projects( int $post_id, int $limit = 3 ): array {
		$limit = max( 1, $limit );
		$term_ids = wp_get_post_terms( $post_id, self::TAXONOMY, array( 'fields' => 'ids' ) );
		if ( is_wp_error( $term_ids ) ) {
			$term_ids = array();
		}

		$found = array();
		if ( $term_ids ) {
			$found = get_posts(
				array(
					'post_type'      => self::POST_TYPE,
					'post_status'    => 'publish',
					'posts_per_page' => $limit,
					'post__not_in'   => array( $post_id ),
					'orderby'        => 'date',
					'order'          => 'DESC',
					'tax_query'      => array(
						array(
							'taxonomy' => self::TAXONOMY,
							'field'    => 'term_id',
							'terms'    => array_map( 'intval', $term_ids ),
						),
					),
				)
			);
		}

		if ( count( $found ) >= $limit ) {
			return $found;
		}

		$exclude = array_merge( array( $post_id ), wp_list_pluck( $found, 'ID' ) );
		$extra   = get_posts(
			array(
				'post_type'      => self::POST_TYPE,
				'post_status'    => 'publish',
				'posts_per_page' => $limit - count( $found ),
				'post__not_in'   => $exclude,
				'orderby'        => 'date',
				'order'          => 'DESC',
			)
		);

		return array_values( array_merge( $found, $extra ) );
	}
}
