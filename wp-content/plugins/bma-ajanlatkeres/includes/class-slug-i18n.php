<?php
/**
 * Translated URL bases for the service and reference post types.
 *
 * The post types are registered with Hungarian rewrite slugs
 * (szolgaltatas, referencia); Polylang only prefixes them with the
 * language code. This class serves and links RO/EN posts under
 * translated bases (/ro/serviciu/…, /en/service/…) and redirects the
 * old /ro/szolgaltatas/… style URLs.
 *
 * @package BMA_Ajanlatkeres
 */

defined( 'ABSPATH' ) || exit;

final class BMA_AJ_Slug_I18n {

	/**
	 * Post type => [ default (HU) slug, [ lang => translated slug ] ].
	 */
	private const MAP = array(
		'bma_service'   => array(
			'szolgaltatas',
			array(
				'ro' => 'serviciu',
				'en' => 'service',
			),
		),
		'bma_reference' => array(
			'referencia',
			array(
				'ro' => 'referinta',
				'en' => 'reference',
			),
		),
	);

	/**
	 * Init hooks.
	 */
	public static function init(): void {
		add_filter( 'rewrite_rules_array', array( self::class, 'add_rules' ) );
		add_filter( 'post_type_link', array( self::class, 'filter_link' ), 20, 2 );
		add_filter( 'post_type_archive_link', array( self::class, 'filter_archive_link' ), 20, 2 );
		add_filter( 'pll_translation_url', array( self::class, 'translate_url' ), 20, 2 );
		add_action( 'template_redirect', array( self::class, 'redirect_old_urls' ), 1 );
		add_action( 'init', array( self::class, 'maybe_flush' ), 99 );
	}

	/**
	 * Prepend rules for the translated bases.
	 *
	 * @param array<string, string> $rules Rewrite rules.
	 * @return array<string, string>
	 */
	public static function add_rules( $rules ): array {
		$new = array();
		foreach ( self::MAP as $post_type => $config ) {
			foreach ( $config[1] as $lang => $slug ) {
				$base                                     = $lang . '/' . $slug;
				$new[ $base . '/?$' ]                     = 'index.php?lang=' . $lang . '&post_type=' . $post_type;
				$new[ $base . '/page/([0-9]{1,})/?$' ]    = 'index.php?lang=' . $lang . '&post_type=' . $post_type . '&paged=$matches[1]';
				$new[ $base . '/([^/]+)/?$' ]             = 'index.php?lang=' . $lang . '&' . $post_type . '=$matches[1]';
			}
		}
		return array_merge( $new, (array) $rules );
	}

	/**
	 * Swap the base in a URL for the given language.
	 *
	 * @param string $url       URL.
	 * @param string $post_type Post type.
	 * @param string $lang      Language slug.
	 */
	private static function swap_base( string $url, string $post_type, string $lang ): string {
		if ( ! isset( self::MAP[ $post_type ][1][ $lang ] ) ) {
			return $url;
		}
		$from = '/' . $lang . '/' . self::MAP[ $post_type ][0] . '/';
		$to   = '/' . $lang . '/' . self::MAP[ $post_type ][1][ $lang ] . '/';
		return str_replace( $from, $to, $url );
	}

	/**
	 * Single post permalinks.
	 *
	 * @param string  $url  Permalink.
	 * @param WP_Post $post Post.
	 */
	public static function filter_link( $url, $post ): string {
		if ( ! $post instanceof WP_Post || ! isset( self::MAP[ $post->post_type ] ) || ! function_exists( 'pll_get_post_language' ) ) {
			return (string) $url;
		}
		$lang = (string) pll_get_post_language( $post->ID );
		return self::swap_base( (string) $url, $post->post_type, $lang );
	}

	/**
	 * Archive links (Polylang has already added the language prefix).
	 *
	 * @param string $url       Archive URL.
	 * @param string $post_type Post type.
	 */
	public static function filter_archive_link( $url, $post_type ): string {
		$url = (string) $url;
		if ( ! isset( self::MAP[ $post_type ] ) ) {
			return $url;
		}
		foreach ( array_keys( self::MAP[ $post_type ][1] ) as $lang ) {
			$url = self::swap_base( $url, (string) $post_type, $lang );
		}
		return $url;
	}

	/**
	 * Language switcher URLs for archives.
	 *
	 * @param string $url  Translation URL.
	 * @param string $lang Language slug.
	 */
	public static function translate_url( $url, $lang ): string {
		$url = (string) $url;
		foreach ( array_keys( self::MAP ) as $post_type ) {
			$url = self::swap_base( $url, $post_type, (string) $lang );
		}
		return $url;
	}

	/**
	 * 301 from /ro/szolgaltatas/… (old base) to the translated base.
	 */
	public static function redirect_old_urls(): void {
		$path = (string) wp_parse_url( isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '', PHP_URL_PATH ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		foreach ( self::MAP as $post_type => $config ) {
			foreach ( array_keys( $config[1] ) as $lang ) {
				if ( strpos( $path, '/' . $lang . '/' . $config[0] . '/' ) === false ) {
					continue;
				}
				$target = '';
				if ( is_singular( $post_type ) ) {
					$target = (string) get_permalink( get_queried_object_id() );
				} elseif ( is_post_type_archive( $post_type ) ) {
					$target = (string) get_post_type_archive_link( $post_type );
				}
				if ( '' !== $target ) {
					wp_safe_redirect( $target, 301 );
					exit;
				}
			}
		}
	}

	/**
	 * Flush rewrite rules once whenever the slug map changes.
	 */
	public static function maybe_flush(): void {
		$hash = md5( (string) wp_json_encode( self::MAP ) );
		if ( get_option( 'bma_aj_slug_i18n' ) !== $hash ) {
			flush_rewrite_rules( false );
			update_option( 'bma_aj_slug_i18n', $hash );
		}
	}
}
