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
		add_action( 'admin_post_bma_save_selection', array( self::class, 'handle_form_save' ) );
		add_action( 'admin_post_nopriv_bma_save_selection', array( self::class, 'handle_form_save' ) );
		add_action( 'admin_post_bma_save_cart_fields', array( self::class, 'handle_cart_fields_save' ) );
		add_action( 'admin_post_nopriv_bma_save_cart_fields', array( self::class, 'handle_cart_fields_save' ) );
		add_action( 'admin_post_bma_remove_cart_line', array( self::class, 'handle_remove_cart_line' ) );
		add_action( 'admin_post_nopriv_bma_remove_cart_line', array( self::class, 'handle_remove_cart_line' ) );
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
	 * Merged service-level (project) fields across the cart.
	 *
	 * @return array<int, object>
	 */
	public static function get_merged_service_fields(): array {
		$merged = array();
		foreach ( self::get_items() as $item ) {
			$merged = array_merge(
				$merged,
				BMA_AJ_Dynamic_Fields::get_fields_for_service( (int) ( $item['service_id'] ?? 0 ) )
			);
		}
		return BMA_AJ_Dynamic_Fields::dedupe_fields( $merged );
	}

	/**
	 * Draft field values stored between cart and form steps.
	 *
	 * @return array<string, mixed>
	 */
	public static function get_field_values(): array {
		$cart_id = self::resolve_cart_id( false );
		if ( '' === $cart_id || empty( $_COOKIE[ self::COOKIE_NAME ] ) ) {
			return array();
		}
		$vals = get_transient( self::TRANSIENT_PREFIX . 'fields_' . $cart_id );
		return is_array( $vals ) ? $vals : array();
	}

	/**
	 * Persist draft field values for the cart.
	 *
	 * @param array<string, mixed> $values Field values.
	 */
	public static function set_field_values( array $values ): void {
		$cart_id = self::resolve_cart_id( true );
		set_transient( self::TRANSIENT_PREFIX . 'fields_' . $cart_id, $values, self::TTL );
	}

	/**
	 * Merge into draft field values.
	 *
	 * @param array<string, mixed> $values Field values.
	 */
	public static function merge_field_values( array $values ): void {
		self::set_field_values( array_merge( self::get_field_values(), $values ) );
	}

	/**
	 * Clear draft field values.
	 */
	public static function clear_field_values(): void {
		$cart_id = self::resolve_cart_id( false );
		if ( '' !== $cart_id ) {
			delete_transient( self::TRANSIENT_PREFIX . 'fields_' . $cart_id );
		}
	}

	/**
	 * Remove a single selected service-item line from the cart.
	 *
	 * @param int $item_id Service item ID.
	 */
	public static function remove_selected_item( int $item_id ): void {
		$item_id = absint( $item_id );
		if ( ! $item_id ) {
			return;
		}

		foreach ( self::get_items() as $key => $row ) {
			$selected = array_values( array_map( 'intval', $row['selected_items'] ?? array() ) );
			if ( ! in_array( $item_id, $selected, true ) ) {
				continue;
			}
			$selected = array_values( array_filter( $selected, static fn( $id ) => (int) $id !== $item_id ) );
			if ( empty( $selected ) ) {
				self::remove_item( (string) $key );
			} else {
				self::update_item( (string) $key, array( 'selected_items' => $selected ) );
			}
		}
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
			delete_transient( self::TRANSIENT_PREFIX . 'fields_' . $cart_id );
		}
	}

	/**
	 * Get cart service-group count.
	 */
	public static function count(): int {
		return count( self::get_items() );
	}

	/**
	 * Flat selected service-item lines for summary UI.
	 *
	 * @return array<int, array{item_id:int,name:string,service_id:int,service_name:string,cart_key:string}>
	 */
	public static function get_summary_items(): array {
		$out = array();
		foreach ( self::get_items() as $key => $row ) {
			$sid  = (int) ( $row['service_id'] ?? 0 );
			$area = self::service_title_for_display( $sid );
			foreach ( (array) ( $row['selected_items'] ?? array() ) as $item_id ) {
				$item_id = (int) $item_id;
				if ( ! $item_id ) {
					continue;
				}
				$out[] = array(
					'item_id'      => $item_id,
					'name'         => self::item_name_for_display( $item_id ),
					'service_id'   => $sid,
					'service_name' => $area,
					'cart_key'     => (string) $key,
				);
			}
		}
		return $out;
	}

	/**
	 * Total selected service items (not service groups).
	 */
	public static function item_count(): int {
		return count( self::get_summary_items() );
	}

	/**
	 * JSON payload fragment shared by cart AJAX responses.
	 *
	 * @return array<string, mixed>
	 */
	public static function ajax_cart_payload(): array {
		return array(
			'count'     => self::count(),
			'itemCount' => self::item_count(),
			'selection' => self::get_selection_map(),
			'summary'   => self::get_summary_items(),
			'cartUrl'   => class_exists( 'BMA_AJ_I18n' ) ? BMA_AJ_I18n::page_url( 'ajanlatkosar' ) : home_url( '/ajanlatkosar/' ),
		);
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
		if ( $first_id ) {
			BMA_AJ_Analytics::track_event( 'service_added_to_cart', array( 'service_id' => $first_id ) );
		}

		$redirect = ! empty( $_POST['redirect'] ) ? esc_url_raw( wp_unslash( $_POST['redirect'] ) ) : '';
		$payload  = self::ajax_cart_payload();
		$payload['redirect'] = $redirect;
		$payload['message']  = __( 'Hozzáadva az ajánlatkéréshez', 'bma-ajanlatkeres' );

		wp_send_json_success( $payload );
	}

	/**
	 * AJAX: remove from cart (whole service group or a single item line).
	 */
	public static function ajax_remove(): void {
		check_ajax_referer( 'bma_aj_public', 'nonce' );

		$item_id = absint( $_POST['item_id'] ?? 0 );
		if ( $item_id ) {
			self::remove_selected_item( $item_id );
			wp_send_json_success( self::ajax_cart_payload() );
		}

		$cart_key = sanitize_text_field( wp_unslash( $_POST['cart_key'] ?? '' ) );
		self::remove_item( $cart_key );

		wp_send_json_success( self::ajax_cart_payload() );
	}

	/**
	 * No-JS: save item-specific field drafts and continue to quote form.
	 */
	public static function handle_cart_fields_save(): void {
		check_admin_referer( 'bma_aj_public', 'nonce' );

		if ( empty( self::get_items() ) ) {
			$svc_url = class_exists( 'BMA_AJ_I18n' ) ? BMA_AJ_I18n::page_url( 'szolgaltatasok' ) : home_url( '/szolgaltatasok/' );
			if ( function_exists( 'bma_page_url' ) ) {
				$svc_url = bma_page_url( 'szolgaltatasok' );
			}
			wp_safe_redirect( $svc_url );
			exit;
		}

		$raw = isset( $_POST['field_values'] ) ? wp_unslash( $_POST['field_values'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		if ( ! is_array( $raw ) ) {
			$raw = array();
		}

		$item_fields = array();
		foreach ( self::get_summary_items() as $line ) {
			$item_fields = array_merge(
				$item_fields,
				BMA_AJ_Dynamic_Fields::exclude_field_types(
					BMA_AJ_Dynamic_Fields::get_fields_for_item( (int) $line['item_id'] ),
					array( 'file' )
				)
			);
		}
		$item_fields = BMA_AJ_Dynamic_Fields::dedupe_fields( $item_fields );
		$clean       = BMA_AJ_Dynamic_Fields::sanitize_cart_submission( $raw, $item_fields );
		self::merge_field_values( $clean );

		$quote_url = class_exists( 'BMA_AJ_I18n' ) ? BMA_AJ_I18n::page_url( 'ajanlatkeres' ) : home_url( '/ajanlatkeres/' );
		if ( function_exists( 'bma_page_url' ) ) {
			$quote_url = bma_page_url( 'ajanlatkeres' );
		}
		wp_safe_redirect( $quote_url );
		exit;
	}

	/**
	 * No-JS: remove one cart line then return.
	 */
	public static function handle_remove_cart_line(): void {
		check_admin_referer( 'bma_aj_public', 'nonce' );

		$item_id = absint( $_POST['item_id'] ?? 0 );
		if ( $item_id ) {
			self::remove_selected_item( $item_id );
		} else {
			$cart_key = sanitize_text_field( wp_unslash( $_POST['cart_key'] ?? '' ) );
			if ( $cart_key ) {
				self::remove_item( $cart_key );
			}
		}

		$redirect = ! empty( $_POST['redirect_to'] ) ? esc_url_raw( wp_unslash( $_POST['redirect_to'] ) ) : '';
		if ( ! $redirect ) {
			$redirect = class_exists( 'BMA_AJ_I18n' ) ? BMA_AJ_I18n::page_url( 'ajanlatkosar' ) : home_url( '/ajanlatkosar/' );
			if ( function_exists( 'bma_page_url' ) ) {
				$redirect = bma_page_url( 'ajanlatkosar' );
			}
		}
		wp_safe_redirect( $redirect );
		exit;
	}

	/**
	 * No-JS form fallback: save selections and redirect to cart.
	 */
	public static function handle_form_save(): void {
		check_admin_referer( 'bma_aj_public', 'nonce' );

		$raw_services = isset( $_POST['service_ids'] ) ? (array) wp_unslash( $_POST['service_ids'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$service_ids  = array_values( array_unique( array_filter( array_map( 'absint', $raw_services ) ) ) );

		$raw_sel = isset( $_POST['selections'] ) ? (array) wp_unslash( $_POST['selections'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$selections = array();
		foreach ( $service_ids as $sid ) {
			$ids = isset( $raw_sel[ $sid ] ) ? (array) $raw_sel[ $sid ] : ( isset( $raw_sel[ (string) $sid ] ) ? (array) $raw_sel[ (string) $sid ] : array() );
			$selections[ $sid ] = array_values( array_unique( array_filter( array_map( 'absint', $ids ) ) ) );
		}

		if ( $selections ) {
			self::apply_selections( $selections );
		}

		$cart_url = class_exists( 'BMA_AJ_I18n' ) ? BMA_AJ_I18n::page_url( 'ajanlatkosar' ) : home_url( '/ajanlatkosar/' );
		if ( function_exists( 'bma_page_url' ) ) {
			$cart_url = bma_page_url( 'ajanlatkosar' );
		}
		wp_safe_redirect( $cart_url );
		exit;
	}
}
