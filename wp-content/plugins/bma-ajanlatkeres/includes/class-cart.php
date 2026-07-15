<?php
/**
 * Quote request cart (session-based).
 *
 * @package BMA_Ajanlatkeres
 */

defined( 'ABSPATH' ) || exit;

final class BMA_AJ_Cart {

	public const SESSION_KEY = 'bma_quote_cart';

	/**
	 * Init hooks.
	 */
	public static function init(): void {
		add_action( 'init', array( self::class, 'start_session' ), 1 );
		add_action( 'wp_ajax_bma_add_to_cart', array( self::class, 'ajax_add' ) );
		add_action( 'wp_ajax_nopriv_bma_add_to_cart', array( self::class, 'ajax_add' ) );
		add_action( 'wp_ajax_bma_remove_from_cart', array( self::class, 'ajax_remove' ) );
		add_action( 'wp_ajax_nopriv_bma_remove_from_cart', array( self::class, 'ajax_remove' ) );
		add_action( 'wp_ajax_bma_update_cart_item', array( self::class, 'ajax_update' ) );
		add_action( 'wp_ajax_nopriv_bma_update_cart_item', array( self::class, 'ajax_update' ) );
	}

	/**
	 * Start PHP session for cart.
	 */
	public static function start_session(): void {
		if ( ! session_id() && ! headers_sent() ) {
			session_start();
		}
	}

	/**
	 * Get cart items.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	public static function get_items(): array {
		self::start_session();
		return $_SESSION[ self::SESSION_KEY ] ?? array();
	}

	/**
	 * Add item to cart.
	 *
	 * @param array<string, mixed> $item Cart item data.
	 */
	public static function add_item( array $item ): void {
		self::start_session();
		if ( ! isset( $_SESSION[ self::SESSION_KEY ] ) ) {
			$_SESSION[ self::SESSION_KEY ] = array();
		}

		$key = 'svc_' . (int) $item['service_id'] . '_' . time();
		$item['cart_key'] = $key;
		$_SESSION[ self::SESSION_KEY ][ $key ] = $item;
	}

	/**
	 * Remove item.
	 *
	 * @param string $cart_key Cart key.
	 */
	public static function remove_item( string $cart_key ): void {
		self::start_session();
		unset( $_SESSION[ self::SESSION_KEY ][ $cart_key ] );
	}

	/**
	 * Update item.
	 *
	 * @param string               $cart_key Cart key.
	 * @param array<string, mixed> $data     Updated data.
	 */
	public static function update_item( string $cart_key, array $data ): void {
		self::start_session();
		if ( isset( $_SESSION[ self::SESSION_KEY ][ $cart_key ] ) ) {
			$_SESSION[ self::SESSION_KEY ][ $cart_key ] = array_merge(
				$_SESSION[ self::SESSION_KEY ][ $cart_key ],
				$data
			);
		}
	}

	/**
	 * Clear cart.
	 */
	public static function clear(): void {
		self::start_session();
		$_SESSION[ self::SESSION_KEY ] = array();
	}

	/**
	 * Get item count.
	 */
	public static function count(): int {
		return count( self::get_items() );
	}

	/**
	 * AJAX: add to cart.
	 */
	public static function ajax_add(): void {
		check_ajax_referer( 'bma_aj_public', 'nonce' );

		$service_id     = absint( $_POST['service_id'] ?? 0 );
		$selected_items = isset( $_POST['selected_items'] ) ? array_map( 'absint', (array) wp_unslash( $_POST['selected_items'] ) ) : array();
		$field_values   = isset( $_POST['field_values'] ) ? wp_unslash( $_POST['field_values'] ) : array();

		$service = get_post( $service_id );
		if ( ! $service || BMA_AJ_Service_CPT::POST_TYPE !== $service->post_type ) {
			wp_send_json_error( array( 'message' => __( 'Érvénytelen szolgáltatás.', 'bma-ajanlatkeres' ) ) );
		}

		$sanitized_fields = BMA_AJ_Dynamic_Fields::sanitize_submission( $field_values, $service_id, $selected_items );

		self::add_item(
			array(
				'service_id'     => $service_id,
				'service_name'   => $service->post_title,
				'selected_items' => $selected_items,
				'field_values'   => $sanitized_fields,
				'documents'      => array(),
			)
		);

		BMA_AJ_Analytics::track_event( 'service_added_to_cart', array( 'service_id' => $service_id ) );

		wp_send_json_success(
			array(
				'count'    => self::count(),
				'redirect' => home_url( '/ajanlatkosar/' ),
			)
		);
	}

	/**
	 * AJAX: remove from cart.
	 */
	public static function ajax_remove(): void {
		check_ajax_referer( 'bma_aj_public', 'nonce' );

		$cart_key = sanitize_text_field( wp_unslash( $_POST['cart_key'] ?? '' ) );
		self::remove_item( $cart_key );

		wp_send_json_success( array( 'count' => self::count() ) );
	}

	/**
	 * AJAX: update cart item.
	 */
	public static function ajax_update(): void {
		check_ajax_referer( 'bma_aj_public', 'nonce' );

		$cart_key       = sanitize_text_field( wp_unslash( $_POST['cart_key'] ?? '' ) );
		$selected_items = isset( $_POST['selected_items'] ) ? array_map( 'absint', (array) wp_unslash( $_POST['selected_items'] ) ) : array();
		$field_values   = isset( $_POST['field_values'] ) ? wp_unslash( $_POST['field_values'] ) : array();

		$items = self::get_items();
		if ( ! isset( $items[ $cart_key ] ) ) {
			wp_send_json_error();
		}

		$service_id       = (int) $items[ $cart_key ]['service_id'];
		$sanitized_fields = BMA_AJ_Dynamic_Fields::sanitize_submission( $field_values, $service_id, $selected_items );

		self::update_item(
			$cart_key,
			array(
				'selected_items' => $selected_items,
				'field_values'   => $sanitized_fields,
			)
		);

		wp_send_json_success();
	}
}
