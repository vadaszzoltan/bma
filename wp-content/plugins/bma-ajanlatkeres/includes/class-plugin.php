<?php
/**
 * Main plugin bootstrap.
 *
 * @package BMA_Ajanlatkeres
 */

defined( 'ABSPATH' ) || exit;

final class BMA_AJ_Plugin {

	/**
	 * Singleton instance.
	 *
	 * @var self|null
	 */
	private static ?self $instance = null;

	/**
	 * Get instance.
	 */
	public static function instance(): self {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Initialize plugin modules.
	 */
	public function init(): void {
		load_plugin_textdomain( 'bma-ajanlatkeres', false, dirname( BMA_AJ_PLUGIN_BASENAME ) . '/languages' );

		require_once BMA_AJ_PLUGIN_DIR . 'includes/database/class-schema.php';
		BMA_AJ_Schema::maybe_upgrade();

		BMA_AJ_Roles::init();
		BMA_AJ_Service_CPT::init();
		BMA_AJ_Reference_CPT::init();
		BMA_AJ_Statuses::init();
		BMA_AJ_Email_Templates::init();
		BMA_AJ_Documents::init();
		BMA_AJ_Cart::init();
		BMA_AJ_Quote_Requests::init();
		BMA_AJ_Quotes::init();
		BMA_AJ_Payments::init();
		BMA_AJ_Security::init();
		BMA_AJ_SMTP::init();
		BMA_AJ_Shortcodes::init();
		BMA_AJ_Frontend::init();
		BMA_AJ_Admin::init();
		BMA_AJ_Appearance::init();
		BMA_AJ_Content::init();
		BMA_AJ_REST::init();
		BMA_AJ_Analytics::init();
		BMA_AJ_Cookie_Consent::init();
		BMA_AJ_I18n::init();

		add_action( 'admin_post_bma_contact_submit', array( $this, 'handle_contact_form' ) );
		add_action( 'admin_post_nopriv_bma_contact_submit', array( $this, 'handle_contact_form' ) );

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_public_assets' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );

		add_action( 'init', array( $this, 'maybe_sync_content' ), 20 );
	}

	/**
	 * One-shot content sync when plugin version advances.
	 */
	public function maybe_sync_content(): void {
		$stored = (string) get_option( 'bma_aj_content_version', '' );
		if ( $stored === BMA_AJ_VERSION ) {
			return;
		}
		BMA_AJ_Seeder::sync_legal_page_content();
		BMA_AJ_Seeder::sync_service_selection_content();
		BMA_AJ_Seeder::sync_references_content();
		update_option( 'bma_aj_content_version', BMA_AJ_VERSION );
	}

	/**
	 * Public assets.
	 */
	public function enqueue_public_assets(): void {
		wp_enqueue_style(
			'bma-aj-public',
			BMA_AJ_PLUGIN_URL . 'assets/css/public.css',
			array(),
			BMA_AJ_VERSION
		);

		$script_deps = array( 'jquery' );

		if ( class_exists( 'BMA_AJ_I18n' ) && BMA_AJ_I18n::is_page( 'kapcsolat' ) ) {
			wp_enqueue_style(
				'leaflet',
				'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css',
				array(),
				'1.9.4'
			);
			wp_enqueue_script(
				'leaflet',
				'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js',
				array(),
				'1.9.4',
				true
			);
			$script_deps[] = 'leaflet';
		}

		wp_enqueue_script(
			'bma-aj-public',
			BMA_AJ_PLUGIN_URL . 'assets/js/public.js',
			$script_deps,
			BMA_AJ_VERSION,
			true
		);

		$needs_selection = is_singular( BMA_AJ_Service_CPT::POST_TYPE )
			|| ( class_exists( 'BMA_AJ_I18n' ) && BMA_AJ_I18n::is_page( 'szolgaltatasok' ) )
			|| is_page( array( 'szolgaltatasok', 'servicii', 'services' ) );
		if ( $needs_selection ) {
			wp_enqueue_script(
				'bma-aj-selection',
				BMA_AJ_PLUGIN_URL . 'assets/js/selection.js',
				array( 'jquery', 'bma-aj-public' ),
				BMA_AJ_VERSION,
				true
			);
		}

		$cart_url     = class_exists( 'BMA_AJ_I18n' ) ? BMA_AJ_I18n::page_url( 'ajanlatkosar' ) : home_url( '/ajanlatkosar/' );
		$services_url = class_exists( 'BMA_AJ_I18n' ) ? BMA_AJ_I18n::page_url( 'szolgaltatasok' ) : home_url( '/szolgaltatasok/' );

		$service_sources = array();
		foreach ( BMA_AJ_Service_CPT::get_active_services() as $svc ) {
			$sid = (int) $svc->ID;
			$src = class_exists( 'BMA_AJ_I18n' ) ? BMA_AJ_I18n::source_post_id( $sid ) : $sid;
			$service_sources[ (string) $sid ] = (string) ( $src ?: $sid );
			if ( function_exists( 'pll_get_post_translations' ) ) {
				foreach ( pll_get_post_translations( $src ?: $sid ) as $tr_id ) {
					$service_sources[ (string) (int) $tr_id ] = (string) ( $src ?: $sid );
				}
			}
		}

		$cart_selection = BMA_AJ_Cart::get_selection_map();
		// wp_localize_script JSON-encodes empty PHP arrays as [] – force an object for JS maps.
		if ( empty( $cart_selection ) ) {
			$cart_selection = new stdClass();
		}

		wp_localize_script(
			'bma-aj-public',
			'bmaAj',
			array(
				'ajaxUrl'         => admin_url( 'admin-ajax.php' ),
				'restUrl'         => rest_url( 'bma/v1/' ),
				'nonce'           => wp_create_nonce( 'bma_aj_public' ),
				'restNonce'       => wp_create_nonce( 'wp_rest' ),
				'cartUrl'         => $cart_url,
				'servicesUrl'     => $services_url,
				'cartSelection'   => $cart_selection,
				'summary'         => BMA_AJ_Cart::get_summary_items(),
				'itemCount'       => (int) BMA_AJ_Cart::item_count(),
				'serviceSources'  => $service_sources ?: new stdClass(),
				'strings'         => array(
					'confirmRemove'         => __( 'Biztosan eltávolítja ezt a szolgáltatást?', 'bma-ajanlatkeres' ),
					'uploadError'           => __( 'A fájl feltöltése sikertelen.', 'bma-ajanlatkeres' ),
					'uploadSuccess'         => __( 'Feltöltés sikeres.', 'bma-ajanlatkeres' ),
					'uploading'             => __( 'Feltöltés folyamatban…', 'bma-ajanlatkeres' ),
					'processing'            => __( 'Átirányítás…', 'bma-ajanlatkeres' ),
					'required'              => __( 'Ez a mező kötelező.', 'bma-ajanlatkeres' ),
					'oneItemSelected'       => __( '1 szolgáltatáselem kiválasztva', 'bma-ajanlatkeres' ),
					'nItemsSelected'        => __( '%d szolgáltatáselem kiválasztva', 'bma-ajanlatkeres' ),
					'oneItemSelectedShort'  => __( '1 tétel kiválasztva', 'bma-ajanlatkeres' ),
					'nItemsSelectedShort'   => __( '%d tétel kiválasztva', 'bma-ajanlatkeres' ),
					'continueQuote'         => __( 'Tovább az ajánlatkéréshez', 'bma-ajanlatkeres' ),
					'addToQuote'            => __( 'Kiválasztott szolgáltatások hozzáadása az ajánlatkéréshez', 'bma-ajanlatkeres' ),
					'alreadyAdded'          => __( 'Már hozzáadva az ajánlatkéréshez', 'bma-ajanlatkeres' ),
					'unsavedConfirm'        => __( 'A kiválasztott elemeket még nem adta hozzá az ajánlatkéréshez. Tovább lép?', 'bma-ajanlatkeres' ),
					'removeItem'            => __( 'Tétel eltávolítása', 'bma-ajanlatkeres' ),
					'cartAria'              => __( 'Ajánlat, %d tétel', 'bma-ajanlatkeres' ),
					'addLabel'              => __( 'Hozzáadás', 'bma-ajanlatkeres' ),
					'addedLabel'            => __( 'Hozzáadva', 'bma-ajanlatkeres' ),
					'summaryEmpty'          => __( 'Még nem választott tételt. Jelölje be bal oldalon, mire van szüksége – bármennyi tétel egy ajánlatkérésbe kerülhet.', 'bma-ajanlatkeres' ),
					'summaryEmptyNote'      => __( 'Legalább egy tételt válasszon ki a továbblépéshez.', 'bma-ajanlatkeres' ),
					'summaryFilledNote'     => __( 'A kiválasztás bármikor módosítható a beküldésig. Az ajánlatkérés nem jár kötelezettséggel.', 'bma-ajanlatkeres' ),
				),
			)
		);
	}

	/**
	 * Admin assets.
	 *
	 * @param string $hook Current admin page hook.
	 */
	public function enqueue_admin_assets( string $hook ): void {
		if ( strpos( $hook, 'bma-' ) === false && strpos( $hook, 'bma_' ) === false ) {
			return;
		}

		wp_enqueue_style(
			'bma-aj-admin',
			BMA_AJ_PLUGIN_URL . 'assets/css/admin.css',
			array(),
			BMA_AJ_VERSION
		);

		wp_enqueue_script(
			'bma-aj-admin',
			BMA_AJ_PLUGIN_URL . 'assets/js/admin.js',
			array( 'jquery' ),
			BMA_AJ_VERSION,
			true
		);
	}

	/**
	 * Handle contact form submission.
	 */
	public function handle_contact_form(): void {
		if ( ! isset( $_POST['bma_contact_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['bma_contact_nonce'] ) ), 'bma_contact_form' ) ) {
			wp_die( esc_html__( 'Érvénytelen kérés.', 'bma-ajanlatkeres' ) );
		}

		$redirect = wp_get_referer() ?: home_url( '/kapcsolat/' );

		// Honeypot: bots fill this; humans never see it.
		$honeypot = sanitize_text_field( wp_unslash( $_POST['bma_website'] ?? '' ) );
		if ( '' !== $honeypot ) {
			wp_safe_redirect( add_query_arg( 'contact', 'sent', $redirect ) );
			exit;
		}

		// Time-based: reject near-instant submissions (< 3s).
		$ts = absint( $_POST['bma_form_ts'] ?? 0 );
		if ( $ts && ( time() - $ts ) < 3 ) {
			wp_safe_redirect( add_query_arg( 'contact', 'error', $redirect ) );
			exit;
		}

		$name    = sanitize_text_field( wp_unslash( $_POST['name'] ?? '' ) );
		$email   = sanitize_email( wp_unslash( $_POST['email'] ?? '' ) );
		$phone   = sanitize_text_field( wp_unslash( $_POST['phone'] ?? '' ) );
		$topic   = sanitize_key( wp_unslash( $_POST['topic'] ?? '' ) );
		$message = sanitize_textarea_field( wp_unslash( $_POST['message'] ?? '' ) );
		$privacy = ! empty( $_POST['privacy'] );

		$topics = array(
			'general'  => __( 'Általános érdeklődés', 'bma-ajanlatkeres' ),
			'design'   => __( 'Tervezés', 'bma-ajanlatkeres' ),
			'permit'   => __( 'Engedélyeztetés', 'bma-ajanlatkeres' ),
			'docs'     => __( 'Műszaki dokumentáció', 'bma-ajanlatkeres' ),
			'utility'  => __( 'Közmű csatlakozás koordináció', 'bma-ajanlatkeres' ),
			'existing' => __( 'Meglévő ajánlatkérés vagy projekt', 'bma-ajanlatkeres' ),
			'other'    => __( 'Egyéb', 'bma-ajanlatkeres' ),
		);

		if ( ! $name || ! is_email( $email ) || ! $message || ! $privacy || ! isset( $topics[ $topic ] ) ) {
			wp_safe_redirect( add_query_arg( 'contact', 'error', $redirect ) );
			exit;
		}

		if ( $phone && ! preg_match( '/^[\d\s\+\-\(\)]{6,20}$/', $phone ) ) {
			wp_safe_redirect( add_query_arg( 'contact', 'error', $redirect ) );
			exit;
		}

		$topic_label = $topics[ $topic ];
		$when        = wp_date( 'Y-m-d H:i' );
		$admin_to    = BMA_AJ_Company::get( 'contact_email' ) ?: get_option( 'bma_admin_email', get_option( 'admin_email' ) );

		$admin_body = sprintf(
			"Név: %s\nE-mail: %s\nTelefon: %s\nTéma: %s\nIdőpont: %s\n\n%s",
			$name,
			$email,
			$phone ?: '—',
			$topic_label,
			$when,
			$message
		);

		wp_mail(
			$admin_to,
			sprintf( '[BMA Kapcsolat] %s – %s', $topic_label, $name ),
			$admin_body,
			array( 'Reply-To: ' . $email )
		);

		$company = BMA_AJ_Company::get( 'company_name' );
		$reply   = sprintf(
			"Kedves %s!\n\nKöszönjük az üzenetét. Megkaptuk a megkeresését, és hamarosan felvesszük Önnel a kapcsolatot.\n\nTéma: %s\n\nÜdvözlettel,\n%s",
			$name,
			$topic_label,
			$company
		);

		wp_mail(
			$email,
			sprintf( __( 'Megkaptuk az üzenetét – %s', 'bma-ajanlatkeres' ), $company ),
			$reply
		);

		wp_safe_redirect( add_query_arg( 'contact', 'sent', $redirect ) );
		exit;
	}
}
