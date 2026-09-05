<?php
/**
 * Quote request cart (cookie + transient storage for WordPress AJAX compatibility).
 *
 * @package BMA_Ajanlatkeres
 */

defined( 'ABSPATH' ) || exit;

final class BMA_AJ_Cart {

	public const COOKIE_NAME      = 'bma_cart_id';
	public const TRANSIENT_PREFIX = 'bma_cart_';
	public const TTL              = WEEK_IN_SECONDS;

	/**
	 * Init hooks.
	 */
	public static function init(): void {
		add_action( 'wp_ajax_bma_add_to_cart', array( self::class, 'ajax_add' ) );
		add_action( 'wp_ajax_nopriv_bma_add_to_cart', array( self::class, 'ajax_add' ) );
		add_action( 'wp_ajax_bma_remove_from_cart', array( self::class, 'ajax_remove' ) );
		add_action( 'wp_ajax_nopriv_bma_remove_from_cart', array( self::class, 'ajax_remove' ) );
	}

	/**
	 * Cart storage ID (from cookie or newly generated).
	 */
	public static function get_storage_id(): string {
		if ( ! empty( $_COOKIE[ self::COOKIE_NAME ] ) ) {
			$id = sanitize_key( wp_unslash( $_COOKIE[ self::COOKIE_NAME ] ) );
			if ( strlen( $id ) >= 16 ) {
				return $id;
			}
		}

		return '';
	}

	/**
	 * Resolve cart ID for current request, creating cookie when needed.
	 */
	private static function resolve_cart_id( bool $create_cookie = false ): string {
		if ( ! empty( $_COOKIE[ self::COOKIE_NAME ] ) ) {
			$id = sanitize_key( wp_unslash( $_COOKIE[ self::COOKIE_NAME ] ) );
			if ( strlen( $id ) >= 16 ) {
				return $id;
			}
		}

		$id = wp_generate_password( 32, false );
		if ( $create_cookie ) {
			self::set_cart_cookie( $id );
		}

		return $id;
	}

	/**
	 * Persist cart cookie for subsequent requests (including admin-ajax).
	 *
	 * @param string $cart_id Cart ID.
	 */
	private static function set_cart_cookie( string $cart_id ): void {
		if ( headers_sent() ) {
			return;
		}

		setcookie(
			self::COOKIE_NAME,
			$cart_id,
			array(
				'expires'  => time() + self::TTL,
				'path'     => COOKIEPATH ? COOKIEPATH : '/',
				'domain'   => COOKIE_DOMAIN,
				'secure'   => is_ssl(),
				'httponly'  => true,
				'samesite' => 'Lax',
			)
		);

		$_COOKIE[ self::COOKIE_NAME ] = $cart_id;
	}

	/**
	 * Save cart items to transient storage.
	 *
	 * @param array<string, array<string, mixed>> $items Cart items keyed by cart_key.
	 */
	private static function save_items( array $items ): void {
		$cart_id = self::resolve_cart_id( true );
		set_transient( self::TRANSIENT_PREFIX . $cart_id, $items, self::TTL );
	}

	/**
	 * Get merged dynamic fields for entire cart (deduplicated by field_key).
	 *
	 * @return array<int, object>
	 */
	public static function get_merged_fields(): array {
		$items  = self::get_items();
		$merged = array();

		foreach ( $items as $item ) {
			$fields = BMA_AJ_Dynamic_Fields::get_fields_for_context(
				(int) $item['service_id'],
				$item['selected_items'] ?? array()
			);
			$merged = array_merge( $merged, $fields );
		}

		return BMA_AJ_Dynamic_Fields::dedupe_fields( $merged );
	}

	/**
	 * Upsert cart item for service (one entry per service).
	 *
	 * @param array<string, mixed> $item Cart item data.
	 */
	public static function upsert_item( array $item ): void {
		$items      = self::get_items();
		$service_id = (int) $item['service_id'];

		foreach ( $items as $key => $existing ) {
			if ( (int) $existing['service_id'] === $service_id ) {
				$items[ $key ] = array_merge( $existing, $item, array( 'cart_key' => $key ) );
				self::save_items( $items );
				return;
			}
		}

		$key              = 'svc_' . $service_id . '_' . time();
		$item['cart_key'] = $key;
		$items[ $key ]    = $item;
		self::save_items( $items );
	}

	/**
	 * Get cart items.
	 *
	 * @return array<string, array<string, mixed>>
	 */
	public static function get_items(): array {
		$cart_id = self::resolve_cart_id( false );
		if ( empty( $_COOKIE[ self::COOKIE_NAME ] ) ) {
			return array();
		}

		$items = get_transient( self::TRANSIENT_PREFIX . $cart_id );
		return is_array( $items ) ? $items : array();
	}

	/**
	 * Selection map: service_id => list of item IDs (for UI hydration).
	 *
	 * @return array<string, array<int>>
	 */
	public static function get_selection_map(): array {
		$map = array();
		foreach ( self::get_items() as $row ) {
			$sid = (int) ( $row['service_id'] ?? 0 );
			if ( ! $sid ) {
				continue;
			}
			$ids = array_values( array_map( 'intval', $row['selected_items'] ?? array() ) );
			$map[ (string) $sid ] = $ids;
			// Also expose under translated service IDs so RO/EN list UIs hydrate.
			if ( function_exists( 'pll_get_post_translations' ) ) {
				foreach ( pll_get_post_translations( $sid ) as $tr_id ) {
					$map[ (string) (int) $tr_id ] = $ids;
				}
			}
		}
		return $map;
	}

	/**
	 * Apply multi-service selection map to cart.
	 * Empty item lists remove the service from the cart.
	 *
	 * @param array<int|string, array<int>> $selections service_id => item IDs.
	 */
	public static function apply_selections( array $selections ): void {
		foreach ( $selections as $service_id => $item_ids ) {
			$sid = absint( $service_id );
			if ( ! $sid ) {
				continue;
			}
			// Keep cart keys on the default-language service post.
			if ( class_exists( 'BMA_AJ_I18n' ) ) {
				$sid = BMA_AJ_I18n::source_post_id( $sid ) ?: $sid;
			}

			$item_ids = array_values( array_unique( array_filter( array_map( 'absint', (array) $item_ids ) ) ) );

			if ( empty( $item_ids ) ) {
				foreach ( self::get_items() as $key => $existing ) {
					$existing_sid = (int) $existing['service_id'];
					if ( class_exists( 'BMA_AJ_I18n' ) ) {
						$existing_sid = BMA_AJ_I18n::source_post_id( $existing_sid ) ?: $existing_sid;
					}
					if ( $existing_sid === $sid ) {
						self::remove_item( $key );
					}
				}
				continue;
			}

			$service = get_post( $sid );
			if ( ! $service || BMA_AJ_Service_CPT::POST_TYPE !== $service->post_type ) {
				continue;
			}

			self::upsert_item(
				array(
					'service_id'     => $sid,
					'service_name'   => $service->post_title,
					'selected_items' => $item_ids,
				)
			);
		}
	}

	/**
	 * Add item to cart.
	 *
	 * @param array<string, mixed> $item Cart item data.
	 */
	public static function add_item( array $item ): void {
		$items            = self::get_items();
		$key              = 'svc_' . (int) $item['service_id'] . '_' . time();
		$item['cart_key'] = $key;
		$items[ $key ]    = $item;
		self::save_items( $items );
	}

	/**
	 * Remove item.
	 *
	 * @param string $cart_key Cart key.
	 */
	public static function remove_item( string $cart_key ): void {
		$items = self::get_items();
		unset( $items[ $cart_key ] );
		self::save_items( $items );
	}

	/**
	 * Update item.
	 *
	 * @param string               $cart_key Cart key.
	 * @param array<string, mixed> $data     Updated data.
	 */
	public static function update_item( string $cart_key, array $data ): void {
		$items = self::get_items();
		if ( isset( $items[ $cart_key ] ) ) {
			$items[ $cart_key ] = array_merge( $items[ $cart_key ], $data );
			self::save_items( $items );
		}
	}

	/**
	 * Clear cart.
	 */
	public static function clear(): void {
		$cart_id = self::resolve_cart_id( false );
		if ( ! empty( $_COOKIE[ self::COOKIE_NAME ] ) ) {
			delete_transient( self::TRANSIENT_PREFIX . $cart_id );
		}
	}

	/**
	 * Get item count.
	 */
	public static function count(): int {
		return count( self::get_items() );
	}

	/**
	 * Service title in the current frontend language.
	 *
	 * @param int $service_id Canonical or translated service ID.
	 */
	public static function service_title_for_display( int $service_id ): string {
		$service_id = absint( $service_id );
		if ( ! $service_id ) {
			return '';
		}
		$display_id = $service_id;
		if ( class_exists( 'BMA_AJ_I18n' ) && function_exists( 'pll_get_post' ) ) {
			$lang = BMA_AJ_I18n::current_lang();
			$tr   = pll_get_post( $service_id, $lang );
			if ( $tr ) {
				$display_id = (int) $tr;
			}
		}
		$post = get_post( $display_id );
		if ( $post && BMA_AJ_Service_CPT::POST_TYPE === $post->post_type ) {
			return (string) $post->post_title;
		}
		$fallback = get_post( $service_id );
		return $fallback ? (string) $fallback->post_title : '';
	}

	/**
	 * Service permalink in the current frontend language.
	 *
	 * @param int $service_id Canonical or translated service ID.
	 */
	public static function service_url_for_display( int $service_id ): string {
		$service_id = absint( $service_id );
		if ( ! $service_id ) {
			return '';
		}
		$display_id = $service_id;
		if ( class_exists( 'BMA_AJ_I18n' ) && function_exists( 'pll_get_post' ) ) {
			$lang = BMA_AJ_I18n::current_lang();
			$tr   = pll_get_post( $service_id, $lang );
			if ( $tr ) {
				$display_id = (int) $tr;
			}
		}
		$url = get_permalink( $display_id );
		return $url ? (string) $url : '';
	}

	/**
	 * Service item name in the current frontend language.
	 *
	 * @param int $item_id Service item ID.
	 */
	public static function item_name_for_display( int $item_id ): string {
		$item = BMA_AJ_Service_Items::get( absint( $item_id ) );
		return $item ? (string) $item->name : '';
	}

	/**
	 * AJAX: add/update cart from selection map (list or detail page).
	 */
	public static function ajax_add(): void {
		check_ajax_referer( 'bma_aj_public', 'nonce' );

		$selections = array();

		if ( ! empty( $_POST['selections'] ) ) {
			$raw = wp_unslash( $_POST['selections'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			if ( is_string( $raw ) ) {
				$decoded = json_decode( $raw, true );
				if ( is_array( $decoded ) ) {
					$selections = $decoded;
				}
			} elseif ( is_array( $raw ) ) {
				$selections = $raw;
			}
		}

		// Legacy single-service payload.
		if ( empty( $selections ) ) {
			$service_id     = absint( $_POST['service_id'] ?? 0 );
			$selected_items = isset( $_POST['selected_items'] ) ? array_map( 'absint', (array) wp_unslash( $_POST['selected_items'] ) ) : array();
			if ( $service_id ) {
				$selections[ $service_id ] = $selected_items;
			}
		}

		if ( empty( $selections ) ) {
			wp_send_json_error( array( 'message' => __( 'Nincs kiválasztott szolgáltatáselem.', 'bma-ajanlatkeres' ) ), 400 );
		}

		self::apply_selections( $selections );

		$first_id = (int) array_key_first( $selections );
		BMA_AJ_Analytics::track_event( 'service_added_to_cart', array( 'service_id' => $first_id ) );

		$redirect = ! empty( $_POST['redirect'] ) ? esc_url_raw( wp_unslash( $_POST['redirect'] ) ) : '';

		wp_send_json_success(
			array(
				'count'      => self::count(),
				'selection'  => self::get_selection_map(),
				'redirect'   => $redirect,
				'cartUrl'    => class_exists( 'BMA_AJ_I18n' ) ? BMA_AJ_I18n::page_url( 'ajanlatkosar' ) : home_url( '/ajanlatkosar/' ),
				'message'    => __( 'Hozzáadva az ajánlatkéréshez', 'bma-ajanlatkeres' ),
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

		wp_send_json_success(
			array(
				'count'     => self::count(),
				'selection' => self::get_selection_map(),
			)
		);
	}
}
