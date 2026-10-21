<?php
/**
 * Quote request v2: 7-step flow (project, design phase, items, add-ons,
 * documents, contact, summary). Business rules live here; the template and
 * quote-wizard.js only mirror them for instant feedback.
 *
 * @package BMA_Ajanlatkeres
 */

defined( 'ABSPATH' ) || exit;

final class BMA_AJ_Quote_Flow {

	public const FLOW_VERSION = 2;

	/** Add-on slug suffix marking "we obtain this document for you". */
	private const PROCURE_SUFFIX = '-beszerzese';

	/**
	 * Request-level cache of the catalog.
	 *
	 * @var array<string, mixed>|null
	 */
	private static ?array $catalog = null;

	/**
	 * Hooks.
	 */
	public static function init(): void {
		add_action( 'wp_ajax_bma_submit_quote_v2', array( self::class, 'ajax_submit' ) );
		add_action( 'wp_ajax_nopriv_bma_submit_quote_v2', array( self::class, 'ajax_submit' ) );
		add_action( 'admin_post_bma_submit_quote_v2', array( self::class, 'handle_form_submit' ) );
		add_action( 'admin_post_nopriv_bma_submit_quote_v2', array( self::class, 'handle_form_submit' ) );
		add_action( 'template_redirect', array( self::class, 'redirect_legacy' ), 1 );
	}

	/* ------------------------------------------------------------------
	 * Vocabulary
	 * ---------------------------------------------------------------- */

	/**
	 * Client types.
	 *
	 * @return array<string, string>
	 */
	public static function client_types(): array {
		return array(
			'maganszemely' => __( 'Magánszemély', 'bma-ajanlatkeres' ),
			'ceg'          => __( 'Cég vagy vállalkozás', 'bma-ajanlatkeres' ),
			'onkormanyzat' => __( 'Önkormányzat vagy közintézmény', 'bma-ajanlatkeres' ),
		);
	}

	/**
	 * Design phases. only: client types allowed to pick it (empty = all).
	 *
	 * @return array<string, array{label:string,desc:string,only:string[]}>
	 */
	public static function phases(): array {
		return array(
			'dtac'    => array(
				'label' => __( 'Engedélyes terv (DTAC)', 'bma-ajanlatkeres' ),
				'desc'  => __( 'Az építési engedély megszerzéséhez szükséges dokumentáció. Ezt kell beadni a hatósághoz, mielőtt az építkezés elindul.', 'bma-ajanlatkeres' ),
				'only'  => array(),
			),
			'pt_dde'  => array(
				'label' => __( 'Kivitelezési terv (PT + DDE)', 'bma-ajanlatkeres' ),
				'desc'  => __( 'Ha az engedélyes terv már megvan, és csak a kivitelezéshez szükséges részletes terv kell: méretek, anyagok, részletrajzok.', 'bma-ajanlatkeres' ),
				'only'  => array(),
			),
			'dtac_pt' => array(
				'label' => __( 'Engedélyes + kivitelezési terv', 'bma-ajanlatkeres' ),
				'desc'  => __( 'Teljes tervcsomag az engedélytől a kivitelezésig. Ez a leggyakoribb választás.', 'bma-ajanlatkeres' ),
				'only'  => array(),
			),
			'sf_dali' => array(
				'label' => __( 'Megvalósíthatósági fázis (SF / DALI)', 'bma-ajanlatkeres' ),
				'desc'  => __( 'Önkormányzati, közbeszerzéses beruházások előkészítő tanulmánya.', 'bma-ajanlatkeres' ),
				'only'  => array( 'onkormanyzat' ),
			),
		);
	}

	/**
	 * Phase label.
	 *
	 * @param string $key Phase key.
	 */
	public static function phase_label( string $key ): string {
		return self::phases()[ $key ]['label'] ?? $key;
	}

	/**
	 * Phase allowed for a client type.
	 *
	 * @param string $phase       Phase key.
	 * @param string $client_type Client type key.
	 */
	public static function phase_allowed( string $phase, string $client_type ): bool {
		$all = self::phases();
		if ( ! isset( $all[ $phase ] ) ) {
			return false;
		}
		return empty( $all[ $phase ]['only'] ) || in_array( $client_type, $all[ $phase ]['only'], true );
	}

	/* ------------------------------------------------------------------
	 * Catalog (what the form offers)
	 * ---------------------------------------------------------------- */

	/**
	 * Active services with items and permit add-on, plus general add-ons.
	 *
	 * @return array{services: array<int, array<string, mixed>>, items: array<int, array<string, mixed>>, addons: array<int, object>, general: int[]}
	 */
	public static function catalog(): array {
		if ( null !== self::$catalog ) {
			return self::$catalog;
		}

		$services = array();
		$items    = array();
		$addons   = array();

		foreach ( BMA_AJ_Service_CPT::get_active_services() as $post ) {
			$source_id = BMA_AJ_I18n::source_post_id( (int) $post->ID );
			$svc_items = BMA_AJ_Service_Items::get_for_service( (int) $post->ID );
			if ( ! $svc_items ) {
				continue;
			}
			$short = (string) get_post_meta( $post->ID, '_bma_short_title', true );
			$svc   = array(
				'id'        => $source_id,
				'post_id'   => (int) $post->ID,
				'slug'      => (string) get_post_field( 'post_name', $source_id ),
				'title'     => get_the_title( $post ),
				'short'     => '' !== $short ? $short : get_the_title( $post ),
				'has_phase' => '1' !== get_post_meta( $source_id, '_bma_no_design_phase', true ),
				'items'     => $svc_items,
				'permit'    => null,
			);
			foreach ( BMA_AJ_Service_Items::get_for_service( (int) $post->ID, 'addon' ) as $permit ) {
				$svc['permit']                = $permit;
				$addons[ (int) $permit->id ] = $permit;
				break;
			}
			foreach ( $svc_items as $item ) {
				$items[ (int) $item->id ] = array(
					'item'      => $item,
					'service'   => $source_id,
					'slug'      => $svc['slug'],
					'has_phase' => $svc['has_phase'],
				);
			}
			$services[ $source_id ] = $svc;
		}

		$general = array();
		foreach ( BMA_AJ_Service_Items::get_general_addons() as $addon ) {
			$addons[ (int) $addon->id ] = $addon;
			$general[]                  = (int) $addon->id;
		}

		self::$catalog = array(
			'services' => $services,
			'items'    => $items,
			'addons'   => $addons,
			'general'  => $general,
		);
		return self::$catalog;
	}

	/**
	 * v2 fields grouped by placement.
	 *
	 * @return array<string, array<int, object>>
	 */
	public static function fields(): array {
		$out = array();
		foreach ( array( 'project', 'addons', 'documents', 'item' ) as $placement ) {
			$out[ $placement ] = BMA_AJ_Dynamic_Fields::get_by_placement( $placement );
		}
		return $out;
	}

	/**
	 * Add-ons worth showing on a service page: its permit, then the general
	 * add-ons whose auto rule depends on a field this service asks for.
	 *
	 * @param int $service_id Service post ID (any language).
	 * @return array<int, object>
	 */
	public static function topical_addons( int $service_id ): array {
		$cat = self::catalog();
		$sid = BMA_AJ_I18n::source_post_id( $service_id );
		if ( ! isset( $cat['services'][ $sid ] ) ) {
			return array();
		}
		$svc      = $cat['services'][ $sid ];
		$item_ids = array_map( static fn( $i ) => (int) $i->id, $svc['items'] );

		$keys = array();
		foreach ( self::fields() as $placement => $fields ) {
			foreach ( $fields as $field ) {
				if ( 'item' !== $placement || array_intersect( $item_ids, array_map( 'intval', (array) $field->item_ids ) ) ) {
					$keys[] = $field->field_key;
				}
			}
		}
		if ( $svc['has_phase'] ) {
			$keys[] = '_phase';
		}

		$out = $svc['permit'] ? array( $svc['permit'] ) : array();
		foreach ( $cat['general'] as $id ) {
			$addon = $cat['addons'][ $id ];
			foreach ( (array) ( BMA_AJ_Service_Items::auto_rule( $addon )['any'] ?? array() ) as $cond ) {
				if ( in_array( $cond['field'] ?? '', $keys, true ) ) {
					$out[] = $addon;
					break;
				}
			}
		}
		return $out;
	}

	/* ------------------------------------------------------------------
	 * State and rules
	 * ---------------------------------------------------------------- */

	/**
	 * Normalize submitted data into a flow state (unknown ids/keys dropped).
	 *
	 * @param array<string, mixed> $in    Unslashed POST.
	 * @param array<string, int>   $files Field key => uploaded file count.
	 * @return array<string, mixed>
	 */
	public static function state_from_input( array $in, array $files = array() ): array {
		$cat = self::catalog();

		$client_type = sanitize_key( (string) ( $in['client_type'] ?? '' ) );
		if ( ! isset( self::client_types()[ $client_type ] ) ) {
			$client_type = '';
		}

		$phase = sanitize_key( (string) ( $in['design_phase'] ?? '' ) );
		if ( ! self::phase_allowed( $phase, $client_type ) ) {
			$phase = '';
		}

		$items = array();
		foreach ( (array) ( $in['items'] ?? array() ) as $id ) {
			$id = absint( $id );
			if ( isset( $cat['items'][ $id ] ) ) {
				$items[ $id ] = $id;
			}
		}
		$items = array_values( $items );

		$item_phases = array();
		foreach ( (array) ( $in['item_phases'] ?? array() ) as $id => $p ) {
			$id = absint( $id );
			$p  = sanitize_key( (string) $p );
			if ( in_array( $id, $items, true ) && $cat['items'][ $id ]['has_phase'] && self::phase_allowed( $p, $client_type ) && $p !== $phase ) {
				$item_phases[ $id ] = $p;
			}
		}

		$values = array();
		foreach ( (array) ( $in['field_values'] ?? array() ) as $key => $val ) {
			$values[ sanitize_key( (string) $key ) ] = $val;
		}

		$state = array(
			'client_type' => $client_type,
			'phase'       => $phase,
			'items'       => $items,
			'item_phases' => $item_phases,
			'addons'      => array(),
			'values'      => $values,
			'files'       => $files,
		);

		$allowed = self::allowed_addons( $state );
		foreach ( (array) ( $in['addons'] ?? array() ) as $id ) {
			$id = absint( $id );
			if ( in_array( $id, $allowed, true ) ) {
				$state['addons'][ $id ] = $id;
			}
		}
		$state['addons'] = array_values( $state['addons'] );

		$state['values'] = self::visible_values( $state );

		// Auto add-ons: without JS (no addons_js flag) apply all, otherwise only locked ones.
		foreach ( self::auto_addons( $state ) as $id => $locked ) {
			if ( $locked || empty( $in['addons_js'] ) ) {
				$state['addons'][] = $id;
			}
		}
		$state['addons'] = array_values( array_unique( $state['addons'] ) );

		return $state;
	}

	/**
	 * Effective phase per selected item (items without design phase are left out).
	 *
	 * @param array<string, mixed> $state Flow state.
	 * @return array<int, string>
	 */
	public static function effective_phases( array $state ): array {
		$cat = self::catalog();
		$out = array();
		foreach ( $state['items'] as $id ) {
			if ( empty( $cat['items'][ $id ]['has_phase'] ) ) {
				continue;
			}
			$out[ $id ] = $state['item_phases'][ $id ] ?? $state['phase'];
		}
		return $out;
	}

	/**
	 * Slugs of services that have a selected item.
	 *
	 * @param array<string, mixed> $state Flow state.
	 * @return string[]
	 */
	public static function selected_service_slugs( array $state ): array {
		$cat   = self::catalog();
		$slugs = array();
		foreach ( $state['items'] as $id ) {
			$slugs[ $cat['items'][ $id ]['slug'] ] = true;
		}
		return array_keys( $slugs );
	}

	/**
	 * Add-on IDs the visitor may pick: general ones + permits of selected groups.
	 *
	 * @param array<string, mixed> $state Flow state.
	 * @return int[]
	 */
	public static function allowed_addons( array $state ): array {
		$cat     = self::catalog();
		$allowed = $cat['general'];
		$groups  = array();
		foreach ( $state['items'] as $id ) {
			$groups[ $cat['items'][ $id ]['service'] ] = true;
		}
		foreach ( array_keys( $groups ) as $sid ) {
			if ( ! empty( $cat['services'][ $sid ]['permit'] ) ) {
				$allowed[] = (int) $cat['services'][ $sid ]['permit']->id;
			}
		}
		return $allowed;
	}

	/**
	 * Values a rule can look at: field values + _phase and _addon pseudo fields.
	 *
	 * @param array<string, mixed> $state Flow state.
	 * @return array<string, mixed>
	 */
	private static function context( array $state ): array {
		$cat           = self::catalog();
		$ctx           = $state['values'];
		$ctx['_phase'] = array_values( array_unique( self::effective_phases( $state ) ) );
		$ctx['_addon'] = array();
		foreach ( $state['addons'] as $id ) {
			if ( isset( $cat['addons'][ $id ] ) ) {
				$ctx['_addon'][] = (string) $cat['addons'][ $id ]->slug;
			}
		}
		return $ctx;
	}

	/**
	 * Does one {field, values} condition hold?
	 *
	 * @param array<string, mixed> $cond Condition.
	 * @param array<string, mixed> $ctx  Context.
	 */
	public static function condition_matches( array $cond, array $ctx ): bool {
		$have = $ctx[ (string) ( $cond['field'] ?? '' ) ] ?? null;
		if ( null === $have || '' === $have ) {
			return false;
		}
		$have = array_map( 'strval', (array) $have );
		return (bool) array_intersect( $have, array_map( 'strval', (array) ( $cond['values'] ?? array() ) ) );
	}

	/**
	 * Does an add-on auto rule ({any: [conditions], locked}) hold?
	 *
	 * @param array<string, mixed> $rule Rule.
	 * @param array<string, mixed> $ctx  Context.
	 */
	public static function rule_matches( array $rule, array $ctx ): bool {
		foreach ( (array) ( $rule['any'] ?? array() ) as $cond ) {
			if ( is_array( $cond ) && self::condition_matches( $cond, $ctx ) ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Is a field shown for this state?
	 *
	 * @param object               $field Field row (from get_by_placement).
	 * @param array<string, mixed> $state Flow state.
	 * @param array<string, mixed> $ctx   Context.
	 */
	public static function field_visible( object $field, array $state, array $ctx ): bool {
		if ( 'item' === $field->placement && ! array_intersect( $field->item_ids, $state['items'] ) ) {
			return false;
		}
		if ( ! empty( $field->cond['field'] ) && ! self::condition_matches( $field->cond, $ctx ) ) {
			return false;
		}
		return true;
	}

	/**
	 * Is a (visible) field required for this state?
	 *
	 * @param object               $field Field row.
	 * @param array<string, mixed> $state Flow state.
	 */
	public static function field_required( object $field, array $state ): bool {
		$required = (bool) $field->is_required;
		if ( ! empty( $field->cond['required_services'] ) ) {
			$required = $required || (bool) array_intersect( (array) $field->cond['required_services'], self::selected_service_slugs( $state ) );
		}
		if ( $required && ! empty( $field->cond['one_of'] ) ) {
			foreach ( (array) $field->cond['one_of'] as $other ) {
				if ( self::has_value( (string) $other, $state ) ) {
					return false;
				}
			}
		}
		return $required;
	}

	/**
	 * Whether a field has a value (or an uploaded file).
	 *
	 * @param string               $key   Field key.
	 * @param array<string, mixed> $state Flow state.
	 */
	private static function has_value( string $key, array $state ): bool {
		if ( ! empty( $state['files'][ $key ] ) ) {
			return true;
		}
		$val = $state['values'][ $key ] ?? '';
		if ( is_array( $val ) ) {
			return (bool) array_filter( array_map( 'trim', array_map( 'strval', $val ) ) );
		}
		return '' !== trim( (string) $val );
	}

	/**
	 * Drop values of fields that are hidden in this state.
	 *
	 * @param array<string, mixed> $state Flow state.
	 * @return array<string, mixed>
	 */
	private static function visible_values( array $state ): array {
		$fields = array_merge( ...array_values( self::fields() ) );
		// Two passes: a field can depend on another conditional field.
		for ( $pass = 0; $pass < 2; $pass++ ) {
			$ctx  = self::context( $state );
			$keep = array();
			foreach ( $fields as $field ) {
				if ( isset( $state['values'][ $field->field_key ] ) && self::field_visible( $field, $state, $ctx ) ) {
					$keep[ $field->field_key ] = $state['values'][ $field->field_key ];
				}
			}
			$state['values'] = $keep;
		}
		return $state['values'];
	}

	/**
	 * Add-ons switched on by the current answers. id => locked.
	 *
	 * @param array<string, mixed> $state Flow state.
	 * @return array<int, bool>
	 */
	public static function auto_addons( array $state ): array {
		$cat = self::catalog();
		$ctx = self::context( $state );
		$out = array();
		foreach ( $cat['general'] as $id ) {
			$rule = BMA_AJ_Service_Items::auto_rule( $cat['addons'][ $id ] );
			if ( $rule && self::rule_matches( $rule, $ctx ) ) {
				$out[ $id ] = ! empty( $rule['locked'] );
			}
		}
		return $out;
	}

	/**
	 * Validate a state. Error data: step (1–6) and fields (key => message).
	 *
	 * @param array<string, mixed> $state   Flow state.
	 * @param array<string, mixed> $contact Contact data.
	 * @return true|WP_Error
	 */
	public static function validate( array $state, array $contact ) {
		$required_msg = __( 'Ez a mező kötelező.', 'bma-ajanlatkeres' );
		$errors       = array();
		$first_step   = 7;
		$add          = static function ( int $step, string $key, string $msg ) use ( &$errors, &$first_step ): void {
			$errors[ $key ] = $msg;
			$first_step     = min( $first_step, $step );
		};

		if ( '' === $state['client_type'] ) {
			$add( 1, 'client_type', $required_msg );
		}

		$fields = self::fields();
		$ctx    = self::context( $state );
		$steps  = array(
			'project'   => 1,
			'addons'    => 4,
			'documents' => 5,
			'item'      => 5,
		);
		foreach ( $fields as $placement => $list ) {
			foreach ( $list as $field ) {
				if ( self::field_visible( $field, $state, $ctx ) && self::field_required( $field, $state ) && ! self::has_value( $field->field_key, $state ) ) {
					$add( $steps[ $placement ], $field->field_key, $required_msg );
				}
			}
		}

		if ( ! $state['items'] ) {
			$add( 3, 'items', __( 'Kérjük, válasszon legalább egy szolgáltatást.', 'bma-ajanlatkeres' ) );
		} elseif ( '' === $state['phase'] && in_array( '', self::effective_phases( $state ), true ) ) {
			$add( 2, 'design_phase', __( 'Kérjük, válassza ki, milyen tervre van szüksége.', 'bma-ajanlatkeres' ) );
		}

		foreach ( array( 'name', 'phone' ) as $key ) {
			if ( '' === trim( (string) ( $contact[ $key ] ?? '' ) ) ) {
				$add( 6, $key, $required_msg );
			}
		}
		if ( ! is_email( (string) ( $contact['email'] ?? '' ) ) ) {
			$add( 6, 'email', __( 'Érvénytelen e-mail cím.', 'bma-ajanlatkeres' ) );
		}
		if ( in_array( $state['client_type'], array( 'ceg', 'onkormanyzat' ), true ) && '' === trim( (string) ( $contact['company'] ?? '' ) ) ) {
			$add( 6, 'company', $required_msg );
		}
		if ( empty( $contact['privacy'] ) ) {
			$add( 6, 'privacy', __( 'Az adatvédelmi hozzájárulás kötelező.', 'bma-ajanlatkeres' ) );
		}

		if ( ! $errors ) {
			return true;
		}

		$message = 1 === count( $errors ) && ( isset( $errors['items'] ) || isset( $errors['design_phase'] ) )
			? reset( $errors )
			: __( 'Néhány kötelező adat hiányzik. A pirossal jelölt mezőket kérjük, töltse ki.', 'bma-ajanlatkeres' );

		return new WP_Error(
			'invalid',
			$message,
			array(
				'step'   => $first_step,
				'fields' => $errors,
			)
		);
	}

	/* ------------------------------------------------------------------
	 * Submission
	 * ---------------------------------------------------------------- */

	/**
	 * AJAX submit.
	 */
	public static function ajax_submit(): void {
		check_ajax_referer( 'bma_aj_public', 'nonce' );

		$result = self::process();
		if ( is_wp_error( $result ) ) {
			wp_send_json_error(
				array_merge(
					array( 'message' => $result->get_error_message() ),
					(array) $result->get_error_data()
				)
			);
		}
		wp_send_json_success( $result );
	}

	/**
	 * No-JS submit (PRG).
	 */
	public static function handle_form_submit(): void {
		check_admin_referer( 'bma_aj_public', 'nonce' );

		$result = self::process();
		if ( is_wp_error( $result ) ) {
			wp_safe_redirect( add_query_arg( 'bma_error', rawurlencode( $result->get_error_message() ), BMA_AJ_I18n::page_url( 'ajanlatkeres' ) ) );
			exit;
		}
		wp_safe_redirect( $result['redirect'] );
		exit;
	}

	/**
	 * Uploaded files of a field (single or multiple input) in $_FILES shape.
	 *
	 * @param string $key Field key.
	 * @return array<int, array<string, mixed>>
	 */
	private static function uploaded_files( string $key ): array {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce checked by caller.
		$f = $_FILES['field_values'] ?? null;
		if ( ! $f || ! isset( $f['name'][ $key ] ) ) {
			return array();
		}
		$out = array();
		foreach ( (array) $f['name'][ $key ] as $i => $name ) {
			$pick = static fn( string $prop ) => is_array( $f[ $prop ][ $key ] ) ? $f[ $prop ][ $key ][ $i ] : $f[ $prop ][ $key ];
			if ( '' === (string) $name || UPLOAD_ERR_NO_FILE === (int) $pick( 'error' ) ) {
				continue;
			}
			$out[] = array(
				'name'     => $name,
				'type'     => $pick( 'type' ),
				'tmp_name' => $pick( 'tmp_name' ),
				'error'    => $pick( 'error' ),
				'size'     => $pick( 'size' ),
			);
		}
		return $out;
	}

	/**
	 * Validate, store, notify.
	 *
	 * @return array{request_id:int,request_number:string,redirect:string}|WP_Error
	 */
	private static function process() {
		// phpcs:disable WordPress.Security.NonceVerification.Missing -- nonce checked by callers.
		$in = wp_unslash( $_POST );
		// phpcs:enable

		$token_key = BMA_AJ_Quote_Requests::check_submit_token( (string) ( $in['bma_submit_token'] ?? '' ) );
		if ( is_wp_error( $token_key ) ) {
			return $token_key;
		}

		$file_fields = array();
		foreach ( self::fields() as $list ) {
			foreach ( $list as $field ) {
				if ( 'file' === $field->field_type ) {
					$file_fields[ $field->field_key ] = $field;
				}
			}
		}
		$uploads = array();
		$counts  = array();
		foreach ( array_keys( $file_fields ) as $key ) {
			$uploads[ $key ] = self::uploaded_files( $key );
			$counts[ $key ]  = count( $uploads[ $key ] );
		}

		$state   = self::state_from_input( $in, $counts );
		$contact = array(
			'name'            => sanitize_text_field( (string) ( $in['name'] ?? '' ) ),
			'email'           => sanitize_email( (string) ( $in['email'] ?? '' ) ),
			'phone'           => sanitize_text_field( (string) ( $in['phone'] ?? '' ) ),
			'company'         => sanitize_text_field( (string) ( $in['company'] ?? '' ) ),
			'tax_number'      => sanitize_text_field( (string) ( $in['tax_number'] ?? '' ) ),
			'billing_address' => sanitize_textarea_field( (string) ( $in['billing_address'] ?? '' ) ),
			'note'            => sanitize_textarea_field( (string) ( $in['note'] ?? '' ) ),
			'privacy'         => ! empty( $in['privacy'] ),
		);

		$valid = self::validate( $state, $contact );
		if ( is_wp_error( $valid ) ) {
			return $valid;
		}

		// Files of visible fields only.
		$ctx       = self::context( $state );
		$documents = array();
		foreach ( $file_fields as $key => $field ) {
			if ( ! $uploads[ $key ] || ! self::field_visible( $field, $state, $ctx ) ) {
				continue;
			}
			$names = array();
			foreach ( $uploads[ $key ] as $file ) {
				$doc = BMA_AJ_Documents::handle_temp_upload( $file, $key );
				if ( is_wp_error( $doc ) ) {
					return $doc;
				}
				$documents[] = $doc;
				$names[]     = $doc['original_name'];
			}
			$state['values'][ $key ] = $names;
		}

		$user_id = BMA_AJ_Quote_Requests::ensure_account( $contact, ! empty( $in['create_account'] ) );
		if ( is_wp_error( $user_id ) ) {
			return $user_id;
		}

		$request_id = self::store( $state, $contact, $user_id );
		if ( is_wp_error( $request_id ) ) {
			return $request_id;
		}
		foreach ( $documents as $doc ) {
			BMA_AJ_Documents::promote_temp_to_request( $doc, $request_id, 'client_upload' );
		}

		delete_transient( $token_key );
		$number = (string) BMA_AJ_Quote_Requests::get( $request_id )->request_number;
		BMA_AJ_Quote_Requests::after_submit( $request_id, $user_id, $number );

		return array(
			'request_id'     => $request_id,
			'request_number' => $number,
			'redirect'       => BMA_AJ_Quote_Requests::confirmation_url( $number ),
		);
	}

	/**
	 * Insert request + service rows.
	 *
	 * @param array<string, mixed> $state   Flow state.
	 * @param array<string, mixed> $contact Contact data.
	 * @param int                  $user_id User ID (0 = guest).
	 * @return int|WP_Error
	 */
	private static function store( array $state, array $contact, int $user_id ) {
		global $wpdb;
		$cat = self::catalog();

		$values = array();
		foreach ( self::fields() as $list ) {
			foreach ( $list as $field ) {
				if ( ! array_key_exists( $field->field_key, $state['values'] ) ) {
					continue;
				}
				$values[ $field->field_key ] = self::sanitize_value( $field, $state['values'][ $field->field_key ] );
			}
		}

		$auto   = self::auto_addons( $state );
		$addons = array();
		foreach ( $state['addons'] as $id ) {
			$addons[] = array(
				'id'   => $id,
				'slug' => (string) $cat['addons'][ $id ]->slug,
				'name' => self::source_item_name( $id ),
				'auto' => isset( $auto[ $id ] ),
			);
		}

		$inserted = $wpdb->insert(
			$wpdb->prefix . 'bma_quote_requests',
			array(
				'request_number'      => BMA_AJ_Quote_Requests::generate_number(),
				'user_id'             => $user_id ?: null,
				'client_name'         => $contact['name'],
				'client_email'        => $contact['email'],
				'client_phone'        => $contact['phone'],
				'company_name'        => $contact['company'],
				'tax_number'          => $contact['tax_number'],
				'billing_address'     => $contact['billing_address'],
				'client_note'         => $contact['note'],
				'shared_field_values' => wp_json_encode( $values ),
				'flow_version'        => self::FLOW_VERSION,
				'client_type'         => $state['client_type'],
				'design_phase'        => $state['phase'],
				'addons'              => wp_json_encode( $addons ),
				'status_id'           => BMA_AJ_Statuses::get_default_id(),
				'privacy_accepted'    => 1,
			)
		);
		if ( ! $inserted ) {
			return new WP_Error( 'db_error', __( 'Hiba történt a mentés során.', 'bma-ajanlatkeres' ) );
		}
		$request_id = (int) $wpdb->insert_id;

		$phases = self::effective_phases( $state );
		$groups = array();
		foreach ( $state['items'] as $id ) {
			$groups[ $cat['items'][ $id ]['service'] ][] = $id;
		}
		$sort = 0;
		foreach ( $cat['services'] as $sid => $svc ) {
			if ( empty( $groups[ $sid ] ) ) {
				continue;
			}
			$item_phases = array();
			foreach ( $groups[ $sid ] as $id ) {
				if ( isset( $phases[ $id ] ) ) {
					$item_phases[ $id ] = $phases[ $id ];
				}
			}
			$wpdb->insert(
				$wpdb->prefix . 'bma_quote_request_services',
				array(
					'request_id'     => $request_id,
					'service_id'     => $sid,
					'service_name'   => get_the_title( $sid ),
					'selected_items' => wp_json_encode( $groups[ $sid ] ),
					'item_phases'    => wp_json_encode( (object) $item_phases ),
					'field_values'   => wp_json_encode( array() ),
					'sort_order'     => $sort++,
				)
			);
		}

		return $request_id;
	}

	/**
	 * Sanitize one stored value by field type.
	 *
	 * @param object $field Field row.
	 * @param mixed  $val   Raw value.
	 * @return mixed
	 */
	private static function sanitize_value( object $field, $val ) {
		switch ( $field->field_type ) {
			case 'number':
				return '' === trim( (string) $val ) ? '' : (string) floatval( str_replace( ',', '.', (string) $val ) );
			case 'textarea':
			case 'address':
				return sanitize_textarea_field( (string) $val );
			case 'file':
			case 'multiselect':
				return array_map( 'sanitize_text_field', (array) $val );
			default:
				return sanitize_text_field( (string) $val );
		}
	}

	/**
	 * Item name in the default language (stored for the admin).
	 *
	 * @param int $id Item ID.
	 */
	private static function source_item_name( int $id ): string {
		global $wpdb;
		return (string) $wpdb->get_var( $wpdb->prepare( "SELECT name FROM {$wpdb->prefix}bma_service_items WHERE id = %d", $id ) );
	}

	/* ------------------------------------------------------------------
	 * Summary (emails, portal, admin)
	 * ---------------------------------------------------------------- */

	/**
	 * Structured summary of a v2 request, in the current language.
	 *
	 * @param object $request Request row.
	 * @return array<string, mixed>
	 */
	public static function summary( object $request ): array {
		$groups    = array();
		$different = array();
		$global    = (string) $request->design_phase;

		foreach ( BMA_AJ_Quote_Requests::get_services( (int) $request->id ) as $svc ) {
			$phases   = json_decode( (string) ( $svc->item_phases ?? '' ), true ) ?: array();
			$no_phase = '1' === get_post_meta( (int) $svc->service_id, '_bma_no_design_phase', true );
			$names    = array();
			foreach ( (array) $svc->selected_items as $id ) {
				$item = BMA_AJ_Service_Items::get( (int) $id );
				$name = $item ? (string) $item->name : '#' . (int) $id;
				$names[] = $name;
				$phase   = (string) ( $phases[ $id ] ?? '' );
				if ( '' !== $phase && $phase !== $global ) {
					$different[] = $name . ' – ' . self::phase_label( $phase );
				}
			}
			$groups[] = array(
				'service'  => self::service_short( (int) $svc->service_id, (string) $svc->service_name ),
				'items'    => $names,
				'no_phase' => $no_phase,
			);
		}

		$addons  = array();
		$procure = array();
		foreach ( json_decode( (string) ( $request->addons ?? '' ), true ) ?: array() as $addon ) {
			$item     = BMA_AJ_Service_Items::get( (int) ( $addon['id'] ?? 0 ) );
			$addons[] = array(
				'name' => $item ? (string) $item->name : (string) ( $addon['name'] ?? '' ),
				'auto' => ! empty( $addon['auto'] ),
			);
			$slug = (string) ( $addon['slug'] ?? '' );
			if ( substr( $slug, -strlen( self::PROCURE_SUFFIX ) ) === self::PROCURE_SUFFIX ) {
				$procure[] = self::procure_label( (string) ( $addon['name'] ?? '' ) );
			}
		}

		$fields = array();
		$values = BMA_AJ_Quote_Requests::get_shared_fields( $request );
		foreach ( self::fields() as $list ) {
			foreach ( $list as $field ) {
				if ( ! isset( $values[ $field->field_key ] ) ) {
					continue;
				}
				$val = 'file' === $field->field_type
					? implode( ', ', (array) $values[ $field->field_key ] )
					: BMA_AJ_Dynamic_Fields::option_label( $field, $values[ $field->field_key ] );
				if ( '' === trim( $val ) ) {
					continue;
				}
				$fields[] = array(
					'label' => BMA_AJ_Dynamic_Fields::client_label( $field ),
					'value' => $val,
				);
			}
		}

		return array(
			'client_type' => self::client_types()[ (string) $request->client_type ] ?? '',
			'phase'       => '' !== $global ? self::phase_label( $global ) : '',
			'groups'      => $groups,
			'different'   => $different,
			'addons'      => $addons,
			'procure'     => $procure,
			'fields'      => $fields,
		);
	}

	/**
	 * Short group name ("Épületgépészet") in the current language.
	 *
	 * @param int    $service_id Source service ID.
	 * @param string $fallback   Stored name.
	 */
	private static function service_short( int $service_id, string $fallback ): string {
		$post_id = $service_id;
		if ( function_exists( 'pll_get_post' ) ) {
			$post_id = (int) ( pll_get_post( $service_id ) ?: $service_id );
		}
		$short = (string) get_post_meta( $post_id, '_bma_short_title', true );
		return '' !== $short ? $short : $fallback;
	}

	/**
	 * "Urbanisztikai bizonylat beszerzése" → "urbanisztikai bizonylat".
	 *
	 * @param string $name Add-on name (default language).
	 */
	private static function procure_label( string $name ): string {
		$name = trim( preg_replace( '/\s+beszerzése$/u', '', $name ) );
		return mb_strtolower( mb_substr( $name, 0, 1 ) ) . mb_substr( $name, 1 );
	}

	/**
	 * Summary as label/value lines (emails).
	 *
	 * @param object $request  Request row.
	 * @param bool   $for_admin Prepend the "to obtain" line.
	 * @return array<int, array{label:string,value:string}>
	 */
	public static function summary_rows( object $request, bool $for_admin = false ): array {
		$s    = self::summary( $request );
		$rows = array();

		if ( $for_admin && $s['procure'] ) {
			$rows[] = array(
				'label' => __( 'Beszerzendő', 'bma-ajanlatkeres' ),
				'value' => implode( ', ', $s['procure'] ),
			);
		}
		if ( $s['client_type'] ) {
			$rows[] = array(
				'label' => __( 'Megrendelő', 'bma-ajanlatkeres' ),
				'value' => $s['client_type'],
			);
		}
		if ( $s['phase'] ) {
			$rows[] = array(
				'label' => __( 'Tervfázis', 'bma-ajanlatkeres' ),
				'value' => $s['phase'],
			);
		}
		$groups = array();
		foreach ( $s['groups'] as $g ) {
			$groups[] = $g['service'] . ': ' . implode( ', ', $g['items'] ) . ( $g['no_phase'] ? ' (' . __( 'tervfázis nélkül', 'bma-ajanlatkeres' ) . ')' : '' );
		}
		$rows[] = array(
			'label' => __( 'Kiválasztott elemek', 'bma-ajanlatkeres' ),
			'value' => implode( ' · ', $groups ),
		);
		if ( $s['different'] ) {
			$rows[] = array(
				'label' => __( 'Eltérő tervfázis', 'bma-ajanlatkeres' ),
				'value' => implode( ', ', $s['different'] ),
			);
		}
		if ( $s['addons'] ) {
			$rows[] = array(
				'label' => __( 'Kiegészítők', 'bma-ajanlatkeres' ),
				'value' => implode(
					', ',
					array_map(
						static fn( $a ) => $a['name'] . ( $a['auto'] ? ' (' . __( 'automatikusan hozzáadva', 'bma-ajanlatkeres' ) . ')' : '' ),
						$s['addons']
					)
				),
			);
		}
		return array_merge( $rows, $s['fields'] );
	}

	/**
	 * Plain-text summary block for emails.
	 *
	 * @param object $request   Request row.
	 * @param bool   $for_admin Admin variant.
	 */
	public static function summary_text( object $request, bool $for_admin = false ): string {
		$lines = array();
		foreach ( self::summary_rows( $request, $for_admin ) as $row ) {
			$lines[] = $row['label'] . ': ' . $row['value'];
		}
		if ( self::summary( $request )['procure'] ) {
			$lines[] = '';
			$lines[] = __( 'Ha beszerzést is kért, annak költségét és idejét az ajánlatban külön tételként látja.', 'bma-ajanlatkeres' );
		}
		return implode( "\n", $lines );
	}

	/* ------------------------------------------------------------------
	 * Legacy URLs
	 * ---------------------------------------------------------------- */

	/**
	 * 301 retired service pages and the old cart page.
	 */
	public static function redirect_legacy(): void {
		if ( is_singular( BMA_AJ_Service_CPT::POST_TYPE ) ) {
			$post_id = (int) get_queried_object_id();
			if ( '1' === get_post_meta( $post_id, '_bma_is_active', true ) || '' === get_post_meta( $post_id, '_bma_redirect_to', true ) ) {
				return;
			}
			$target = (int) get_post_meta( $post_id, '_bma_redirect_to', true );
			if ( $target && function_exists( 'pll_get_post' ) && function_exists( 'pll_get_post_language' ) ) {
				$target = (int) ( pll_get_post( $target, (string) pll_get_post_language( $post_id ) ) ?: $target );
			}
			$url = $target ? get_permalink( $target ) : BMA_AJ_I18n::page_url( 'szolgaltatasok' );
			wp_safe_redirect( $url, 301 );
			exit;
		}

		if ( BMA_AJ_I18n::is_page( 'ajanlatkosar' ) ) {
			wp_safe_redirect( BMA_AJ_I18n::page_url( 'ajanlatkeres' ), 301 );
			exit;
		}
	}
}
