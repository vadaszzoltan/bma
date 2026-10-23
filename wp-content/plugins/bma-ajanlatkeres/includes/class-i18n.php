<?php
/**
 * Multilingual helpers (Polylang + translation overlays).
 *
 * @package BMA_Ajanlatkeres
 */

defined( 'ABSPATH' ) || exit;

final class BMA_AJ_I18n {

	/**
	 * Init hooks.
	 */
	public static function init(): void {
		add_action( 'init', array( self::class, 'register_polylang_strings' ), 20 );
		add_action( 'pll_init', array( self::class, 'on_pll_init' ) );
		// Newer Polylang versions drop unregistered types from its option on
		// pll_init (CPTs register later, on init); these filters always apply.
		add_filter(
			'pll_get_post_types',
			static fn( $types ) => array_merge( (array) $types, array( 'bma_service' => 'bma_service', 'bma_reference' => 'bma_reference' ) )
		);
		add_filter(
			'pll_get_taxonomies',
			static fn( $taxes ) => array_merge( (array) $taxes, array( 'bma_service_category' => 'bma_service_category', 'bma_reference_category' => 'bma_reference_category' ) )
		);
		add_filter( 'gettext', array( self::class, 'filter_gettext' ), 20, 3 );
		add_filter( 'gettext_with_context', array( self::class, 'filter_gettext_ctx' ), 20, 4 );
		add_filter( 'bma_service_item_name', array( self::class, 'translate_service_item_name' ), 10, 2 );
		add_filter( 'bma_service_item_short_description', array( self::class, 'translate_service_item_field' ), 10, 3 );
		add_filter( 'bma_service_item_choose_if', array( self::class, 'translate_service_item_field' ), 10, 3 );
		add_filter( 'bma_status_name', array( self::class, 'translate_status_name' ), 10, 2 );
		add_filter( 'bma_field_client_label', array( self::class, 'translate_field_label' ), 10, 2 );
		add_filter( 'pll_rel_hreflang_attributes', array( self::class, 'filter_hreflang' ) );
	}

	/**
	 * The service area is Romania: mark HU/RO alternates with the RO region.
	 *
	 * @param array<string, string> $hreflangs Language code => URL.
	 * @return array<string, string>
	 */
	public static function filter_hreflang( $hreflangs ): array {
		$map = array(
			'hu' => 'hu-RO',
			'ro' => 'ro-RO',
		);
		$out = array();
		foreach ( (array) $hreflangs as $code => $url ) {
			$out[ $map[ $code ] ?? $code ] = $url;
		}
		return $out;
	}

	/**
	 * After Polylang boots: mark CPT/tax as translatable.
	 */
	public static function on_pll_init(): void {
		if ( ! function_exists( 'PLL' ) || ! PLL() ) {
			return;
		}
		$options = get_option( 'polylang', array() );
		if ( ! is_array( $options ) ) {
			$options = array();
		}
		$options['post_types'] = array_values(
			array_unique(
				array_merge(
					isset( $options['post_types'] ) && is_array( $options['post_types'] ) ? $options['post_types'] : array(),
					array( 'post', 'page', 'wp_block', 'bma_service', 'bma_reference' )
				)
			)
		);
		$options['taxonomies'] = array_values(
			array_unique(
				array_merge(
					isset( $options['taxonomies'] ) && is_array( $options['taxonomies'] ) ? $options['taxonomies'] : array(),
					array( 'category', 'post_tag', 'bma_service_category', 'bma_reference_category' )
				)
			)
		);
		$options['force_lang']    = 1;
		$options['hide_default']  = 1;
		$options['rewrite']       = 1;
		$options['default_lang']  = $options['default_lang'] ?? 'hu';
		update_option( 'polylang', $options );
	}

	/**
	 * Register option strings with Polylang String Translations.
	 */
	public static function register_polylang_strings(): void {
		if ( ! function_exists( 'pll_register_string' ) ) {
			return;
		}
		$overlay = get_option( 'bma_i18n_overlay', array() );
		$opts    = is_array( $overlay['options'] ?? null ) ? $overlay['options'] : array();
		foreach ( $opts as $key => $langs ) {
			$hu = (string) get_option( $key, '' );
			if ( '' === $hu ) {
				continue;
			}
			pll_register_string( $key, $hu, 'BMA Options', false );
		}
		pll_register_string( 'blogdescription', (string) get_option( 'blogdescription', '' ), 'BMA Options', false );
	}

	/** Language forced by in_language(), null when not forced. */
	private static ?string $forced_lang = null;

	/**
	 * Run a callback in another site language (e.g. to e-mail a client in the
	 * language they used, from an admin request). Reloads the text domains too.
	 *
	 * @param string   $lang hu|ro|en.
	 * @param callable $fn   Callback.
	 * @return mixed Callback result.
	 */
	public static function in_language( string $lang, callable $fn ) {
		$previous = self::current_lang();
		if ( $lang === $previous || ! in_array( $lang, array( 'hu', 'ro', 'en' ), true ) ) {
			return $fn();
		}
		$forced            = self::$forced_lang;
		self::$forced_lang = $lang;
		self::load_domains( $lang );
		try {
			return $fn();
		} finally {
			self::$forced_lang = $forced;
			self::load_domains( $previous );
		}
	}

	/**
	 * Load the site's text domains for a language. switch_to_locale() is not
	 * used: it refuses locales without installed WordPress core files.
	 *
	 * @param string $lang hu|ro|en.
	 */
	private static function load_domains( string $lang ): void {
		$locale  = array( 'ro' => 'ro_RO', 'en' => 'en_US' )[ $lang ] ?? '';
		$domains = array(
			'bma-ajanlatkeres' => array( WP_LANG_DIR . '/plugins', BMA_AJ_PLUGIN_DIR . 'languages' ),
			'bma-engineering'  => array( WP_LANG_DIR . '/themes', get_stylesheet_directory() . '/languages' ),
		);
		foreach ( $domains as $domain => $dirs ) {
			// Not reloadable: just-in-time loading would bring back the request's locale.
			unload_textdomain( $domain );
			if ( '' === $locale ) {
				continue; // Hungarian is the source language.
			}
			foreach ( $dirs as $dir ) {
				$file = $dir . '/' . $domain . '-' . $locale . '.mo';
				if ( is_readable( $file ) ) {
					load_textdomain( $domain, $file, $locale );
					break;
				}
			}
		}
	}

	/**
	 * Current language slug (hu|ro|en).
	 */
	public static function current_lang(): string {
		if ( null !== self::$forced_lang ) {
			return self::$forced_lang;
		}
		if ( function_exists( 'pll_current_language' ) ) {
			$lang = pll_current_language( 'slug' );
			if ( is_string( $lang ) && $lang ) {
				return $lang;
			}
		}
		$locale = determine_locale();
		if ( 0 === strpos( $locale, 'ro' ) ) {
			return 'ro';
		}
		if ( 0 === strpos( $locale, 'en' ) ) {
			return 'en';
		}
		return 'hu';
	}

	/**
	 * Page URL by logical key (fooldal, szolgaltatasok, …), language-aware.
	 *
	 * @param string $key Page key from bma_aj_page_ids.
	 */
	public static function page_url( string $key ): string {
		$map_i18n = get_option( 'bma_aj_page_ids_i18n', array() );
		$lang     = self::current_lang();
		if ( is_array( $map_i18n ) && ! empty( $map_i18n[ $lang ][ $key ] ) ) {
			$url = get_permalink( (int) $map_i18n[ $lang ][ $key ] );
			if ( $url ) {
				return $url;
			}
		}
		$map = get_option( 'bma_aj_page_ids', array() );
		if ( ! empty( $map[ $key ] ) ) {
			$pid = (int) $map[ $key ];
			if ( function_exists( 'pll_get_post' ) ) {
				$tr = pll_get_post( $pid, $lang );
				if ( $tr ) {
					$pid = (int) $tr;
				}
			}
			$url = get_permalink( $pid );
			if ( $url ) {
				return $url;
			}
		}
		return home_url( '/' );
	}

	/**
	 * Resolve a post to the default-language (source) translation ID.
	 * Service items / cart keys stay anchored to the HU post IDs.
	 *
	 * @param int $post_id Any language variant.
	 */
	public static function source_post_id( int $post_id ): int {
		if ( $post_id <= 0 ) {
			return 0;
		}
		if ( ! function_exists( 'pll_get_post' ) || ! function_exists( 'pll_default_language' ) ) {
			return $post_id;
		}
		$default = pll_default_language( 'slug' );
		if ( ! is_string( $default ) || '' === $default ) {
			$default = 'hu';
		}
		$source = pll_get_post( $post_id, $default );
		return $source ? (int) $source : $post_id;
	}

	/**
	 * Whether the current request is a logical BMA page (any language).
	 *
	 * @param string $key Logical key from bma_aj_page_ids (e.g. szolgaltatasok).
	 */
	public static function is_page( string $key ): bool {
		if ( ! is_page() ) {
			return false;
		}
		$current = (int) get_queried_object_id();
		if ( $current <= 0 ) {
			return false;
		}

		$map_i18n = get_option( 'bma_aj_page_ids_i18n', array() );
		if ( is_array( $map_i18n ) ) {
			foreach ( $map_i18n as $pages ) {
				if ( is_array( $pages ) && ! empty( $pages[ $key ] ) && (int) $pages[ $key ] === $current ) {
					return true;
				}
			}
		}

		$map = get_option( 'bma_aj_page_ids', array() );
		if ( ! empty( $map[ $key ] ) ) {
			$hu = (int) $map[ $key ];
			if ( $hu === $current ) {
				return true;
			}
			if ( function_exists( 'pll_get_post_translations' ) ) {
				$trs = pll_get_post_translations( $hu );
				if ( is_array( $trs ) && in_array( $current, array_map( 'intval', $trs ), true ) ) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Translate a short UI / meta label via the runtime string map.
	 *
	 * @param string $text Hungarian source label.
	 */
	public static function translate_label( string $text ): string {
		$text = trim( $text );
		if ( '' === $text ) {
			return '';
		}
		$lang = self::current_lang();
		if ( 'hu' === $lang ) {
			return $text;
		}
		$map = get_option( 'bma_i18n_ui_strings', array() );
		if ( isset( $map[ $text ][ $lang ] ) && is_string( $map[ $text ][ $lang ] ) && $map[ $text ][ $lang ] !== '' ) {
			return $map[ $text ][ $lang ];
		}
		// Fall through gettext domains used on the frontend.
		$via = translate( $text, 'bma-ajanlatkeres' ); // phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText,WordPress.WP.I18n.LowLevelTranslationFunction
		if ( is_string( $via ) && $via !== $text ) {
			return $via;
		}
		return $text;
	}

	/**
	 * Overlay value helper.
	 *
	 * @param string $group Overlay group.
	 * @param string $id    Entity id.
	 * @param string $field Optional nested field.
	 */
	public static function overlay( string $group, string $id, string $field = '' ): string {
		$lang    = self::current_lang();
		if ( 'hu' === $lang ) {
			return '';
		}
		$overlay = get_option( 'bma_i18n_overlay', array() );
		$entry   = $overlay[ $group ][ $id ][ $lang ] ?? null;
		if ( null === $entry ) {
			return '';
		}
		if ( '' === $field ) {
			return is_string( $entry ) ? $entry : '';
		}
		if ( is_array( $entry ) && isset( $entry[ $field ] ) ) {
			$val = $entry[ $field ];
			return is_string( $val ) ? $val : '';
		}
		return '';
	}

	/**
	 * Translate service item name.
	 *
	 * @param string $name Original.
	 * @param int    $id   Item ID.
	 */
	public static function translate_service_item_name( string $name, int $id ): string {
		$tr = self::overlay( 'service_items', (string) $id, 'name' );
		return $tr !== '' ? $tr : $name;
	}

	/**
	 * Translate service item text field.
	 *
	 * @param string $value Original.
	 * @param int    $id    Item ID.
	 * @param string $field Field key.
	 */
	public static function translate_service_item_field( string $value, int $id, string $field = '' ): string {
		if ( '' === $field ) {
			// When used as short_description filter with 2 args only – detect from current filter.
			$field = current_filter() === 'bma_service_item_choose_if' ? 'choose_if' : 'short_description';
		}
		$tr = self::overlay( 'service_items', (string) $id, $field );
		return $tr !== '' ? $tr : $value;
	}

	/**
	 * Translate status name.
	 *
	 * @param string $name Original.
	 * @param int    $id   Status ID.
	 */
	public static function translate_status_name( string $name, int $id ): string {
		$tr = self::overlay( 'statuses', (string) $id );
		return $tr !== '' ? $tr : $name;
	}

	/**
	 * Translate dynamic field client label.
	 *
	 * @param string   $label Original.
	 * @param int|object $field Field id or row.
	 */
	public static function translate_field_label( string $label, $field ): string {
		$id = is_object( $field ) ? (int) ( $field->id ?? 0 ) : (int) $field;
		$tr = self::overlay( 'dynamic_fields', (string) $id, 'client_label' );
		return $tr !== '' ? $tr : $label;
	}

	/**
	 * Runtime UI string translations when .mo is missing.
	 *
	 * @param string $translation Translated text.
	 * @param string $text        Original.
	 * @param string $domain      Text domain.
	 */
	public static function filter_gettext( string $translation, string $text, string $domain ): string {
		if ( 'bma-ajanlatkeres' !== $domain && 'bma-engineering' !== $domain ) {
			return $translation;
		}
		$lang = self::current_lang();
		if ( 'hu' === $lang ) {
			return $translation;
		}
		$map = get_option( 'bma_i18n_ui_strings', array() );
		if ( isset( $map[ $text ][ $lang ] ) && is_string( $map[ $text ][ $lang ] ) && $map[ $text ][ $lang ] !== '' ) {
			return $map[ $text ][ $lang ];
		}
		return $translation;
	}

	/**
	 * Context-aware gettext passthrough to same map.
	 *
	 * @param string $translation Translation.
	 * @param string $text        Text.
	 * @param string $context     Context.
	 * @param string $domain      Domain.
	 */
	public static function filter_gettext_ctx( string $translation, string $text, string $context, string $domain ): string {
		return self::filter_gettext( $translation, $text, $domain );
	}
}
