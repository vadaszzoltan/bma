<?php
/**
 * REST API endpoints.
 *
 * @package BMA_Ajanlatkeres
 */

defined( 'ABSPATH' ) || exit;

final class BMA_AJ_REST {

	/**
	 * Init hooks.
	 */
	public static function init(): void {
		add_action( 'rest_api_init', array( self::class, 'register_routes' ) );
	}

	/**
	 * Register REST routes.
	 */
	public static function register_routes(): void {
		register_rest_route(
			'bma/v1',
			'/services/(?P<id>\d+)/config',
			array(
				'methods'             => 'GET',
				'callback'            => array( self::class, 'get_service_config' ),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			'bma/v1',
			'/services/(?P<id>\d+)/fields',
			array(
				'methods'             => 'GET',
				'callback'            => array( self::class, 'get_service_fields_html' ),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			'bma/v1',
			'/cart/count',
			array(
				'methods'             => 'GET',
				'callback'            => static fn() => rest_ensure_response( array( 'count' => BMA_AJ_Cart::count() ) ),
				'permission_callback' => '__return_true',
			)
		);
	}

	/**
	 * Get service configuration for quote form.
	 *
	 * @param WP_REST_Request $request Request.
	 */
	public static function get_service_config( WP_REST_Request $request ): WP_REST_Response {
		$service_id = absint( $request['id'] );
		$items_raw  = sanitize_text_field( $request->get_param( 'items' ) ?? '' );
		$selected   = array_filter( array_map( 'absint', explode( ',', $items_raw ) ) );
		$items      = BMA_AJ_Dynamic_Fields::get_service_items( $service_id );
		$fields     = BMA_AJ_Dynamic_Fields::get_fields_for_context( $service_id, $selected );

		return rest_ensure_response(
			array(
				'items'  => $items,
				'fields' => $fields,
			)
		);
	}

	/**
	 * Get rendered fields HTML for service and selected items.
	 *
	 * @param WP_REST_Request $request Request.
	 */
	public static function get_service_fields_html( WP_REST_Request $request ): WP_REST_Response {
		$service_id = absint( $request['id'] );
		$items_raw  = sanitize_text_field( $request->get_param( 'items' ) ?? '' );
		$selected   = array_filter( array_map( 'absint', explode( ',', $items_raw ) ) );

		return rest_ensure_response(
			array(
				'html' => BMA_AJ_Dynamic_Fields::render_fields_html( $service_id, $selected ),
			)
		);
	}
}
