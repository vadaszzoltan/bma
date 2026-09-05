<?php
/**
 * Appearance / design tokens (colors, fonts) controllable from admin.
 *
 * @package BMA_Ajanlatkeres
 */

defined( 'ABSPATH' ) || exit;

final class BMA_AJ_Appearance {

	public const OPTION_COLORS = 'bma_appearance_colors';
	public const OPTION_FONTS  = 'bma_appearance_fonts';
	public const OPTION_RADIUS = 'bma_appearance_radius';

	/**
	 * Init hooks.
	 */
	public static function init(): void {
		add_action( 'wp_enqueue_scripts', array( self::class, 'enqueue_fonts' ), 5 );
		add_action( 'wp_enqueue_scripts', array( self::class, 'output_tokens' ), 30 );
		add_action( 'admin_enqueue_scripts', array( self::class, 'admin_assets' ) );
	}

	/**
	 * Default color palette.
	 *
	 * @return array<string, string>
	 */
	public static function default_colors(): array {
		return array(
			'primary'       => '#0E2356',
			'primary_dark'  => '#0A1A40',
			'accent'        => '#EE7643',
			'text'          => '#0E2356',
			'muted'         => '#6B7280',
			'border'        => '#E6E9EF',
			'bg'            => '#F7F8FA',
			'surface'       => '#FFFFFF',
			'footer_bg'     => '#0E2356',
			'footer_text'   => '#D7DEEA',
			'danger'        => '#C0392B',
			'success'       => '#1E8A4C',
			'focus'         => '#EE7643',
		);
	}

	/**
	 * Allowed Google Fonts (slug => CSS family stack).
	 *
	 * @return array<string, array{label: string, family: string, google: string}>
	 */
	public static function font_catalog(): array {
		return array(
			'source-sans-3' => array(
				'label'  => 'Source Sans 3',
				'family' => '"Source Sans 3", system-ui, sans-serif',
				'google' => 'Source+Sans+3:ital,wght@0,400;0,600;0,700;1,400',
			),
			'dm-sans'       => array(
				'label'  => 'DM Sans',
				'family' => '"DM Sans", system-ui, sans-serif',
				'google' => 'DM+Sans:ital,wght@0,400;0,600;0,700;1,400',
			),
			'manrope'       => array(
				'label'  => 'Manrope',
				'family' => '"Manrope", system-ui, sans-serif',
				'google' => 'Manrope:wght@400;600;700',
			),
			'inter'         => array(
				'label'  => 'Inter',
				'family' => '"Inter", system-ui, sans-serif',
				'google' => 'Inter:ital,wght@0,400;0,600;0,700;1,400',
			),
			'ibm-plex-sans' => array(
				'label'  => 'IBM Plex Sans',
				'family' => '"IBM Plex Sans", system-ui, sans-serif',
				'google' => 'IBM+Plex+Sans:ital,wght@0,400;0,600;0,700;1,400',
			),
			'libre-franklin'=> array(
				'label'  => 'Libre Franklin',
				'family' => '"Libre Franklin", system-ui, sans-serif',
				'google' => 'Libre+Franklin:ital,wght@0,400;0,600;0,700;1,400',
			),
			'source-serif-4'=> array(
				'label'  => 'Source Serif 4',
				'family' => '"Source Serif 4", Georgia, serif',
				'google' => 'Source+Serif+4:ital,wght@0,400;0,600;0,700;1,400',
			),
			'fraunces'      => array(
				'label'  => 'Fraunces',
				'family' => '"Fraunces", Georgia, serif',
				'google' => 'Fraunces:ital,wght@0,400;0,600;0,700;1,400',
			),
			'system'        => array(
				'label'  => 'Rendszer betűtípus',
				'family' => 'system-ui, -apple-system, "Segoe UI", Roboto, sans-serif',
				'google' => '',
			),
		);
	}

	/**
	 * Default fonts.
	 *
	 * @return array{heading: string, body: string}
	 */
	public static function default_fonts(): array {
		return array(
			'heading' => 'manrope',
			'body'    => 'source-sans-3',
		);
	}

	/**
	 * Get merged colors.
	 *
	 * @return array<string, string>
	 */
	public static function get_colors(): array {
		$saved = get_option( self::OPTION_COLORS, array() );
		if ( ! is_array( $saved ) ) {
			$saved = array();
		}
		$colors = array_merge( self::default_colors(), $saved );
		foreach ( $colors as $key => $value ) {
			$sanitized = sanitize_hex_color( $value );
			$colors[ $key ] = $sanitized ? $sanitized : self::default_colors()[ $key ];
		}
		return $colors;
	}

	/**
	 * Get fonts.
	 *
	 * @return array{heading: string, body: string}
	 */
	public static function get_fonts(): array {
		$catalog = self::font_catalog();
		$saved   = get_option( self::OPTION_FONTS, array() );
		if ( ! is_array( $saved ) ) {
			$saved = array();
		}
		$fonts = array_merge( self::default_fonts(), $saved );
		if ( ! isset( $catalog[ $fonts['heading'] ] ) ) {
			$fonts['heading'] = self::default_fonts()['heading'];
		}
		if ( ! isset( $catalog[ $fonts['body'] ] ) ) {
			$fonts['body'] = self::default_fonts()['body'];
		}
		return $fonts;
	}

	/**
	 * Border radius in px.
	 */
	public static function get_radius(): int {
		$radius = absint( get_option( self::OPTION_RADIUS, 10 ) );
		return max( 0, min( 24, $radius ) );
	}

	/**
	 * Lighten / mix helpers for derived tokens.
	 *
	 * @param string $hex Hex color.
	 * @param float  $amount 0–1 toward white.
	 */
	public static function mix_white( string $hex, float $amount ): string {
		$hex = ltrim( $hex, '#' );
		if ( 3 === strlen( $hex ) ) {
			$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
		}
		$r = hexdec( substr( $hex, 0, 2 ) );
		$g = hexdec( substr( $hex, 2, 2 ) );
		$b = hexdec( substr( $hex, 4, 2 ) );
		$r = (int) round( $r + ( 255 - $r ) * $amount );
		$g = (int) round( $g + ( 255 - $g ) * $amount );
		$b = (int) round( $b + ( 255 - $b ) * $amount );
		return sprintf( '#%02x%02x%02x', $r, $g, $b );
	}

	/**
	 * Hex to rgba string.
	 *
	 * @param string $hex   Hex.
	 * @param float  $alpha Alpha.
	 */
	public static function hex_to_rgba( string $hex, float $alpha ): string {
		$hex = ltrim( $hex, '#' );
		if ( 3 === strlen( $hex ) ) {
			$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
		}
		$r = hexdec( substr( $hex, 0, 2 ) );
		$g = hexdec( substr( $hex, 2, 2 ) );
		$b = hexdec( substr( $hex, 4, 2 ) );
		return sprintf( 'rgba(%d,%d,%d,%.2f)', $r, $g, $b, $alpha );
	}

	/**
	 * Build :root CSS.
	 */
	public static function build_css(): string {
		$c     = self::get_colors();
		$fonts = self::get_fonts();
		$cat   = self::font_catalog();
		$r     = self::get_radius();

		$heading = $cat[ $fonts['heading'] ]['family'];
		$body    = $cat[ $fonts['body'] ]['family'];
		$tint    = self::mix_white( $c['accent'], 0.88 );
		$soft    = self::mix_white( $c['primary'], 0.92 );

		$vars = array(
			'--bma-primary'         => $c['primary'],
			'--bma-primary-dark'    => $c['primary_dark'],
			'--bma-accent'          => $c['accent'],
			'--bma-text'            => $c['text'],
			'--bma-muted'           => $c['muted'],
			'--bma-border'          => $c['border'],
			'--bma-bg'              => $c['bg'],
			'--bma-surface'         => $c['surface'],
			'--bma-footer-bg'       => $c['footer_bg'],
			'--bma-footer-text'     => $c['footer_text'],
			'--bma-danger'          => $c['danger'],
			'--bma-success'         => $c['success'],
			'--bma-focus'           => $c['focus'],
			'--bma-tint'            => $tint,
			'--bma-soft'            => $soft,
			'--bma-radius'          => $r . 'px',
			'--bma-shadow'          => '0 8px 24px ' . self::hex_to_rgba( $c['text'], 0.08 ),
			'--bma-shadow-sm'       => '0 2px 8px ' . self::hex_to_rgba( $c['text'], 0.06 ),
			'--bma-font-heading'    => $heading,
			'--bma-font-body'       => $body,
			'--bma-container'       => '1140px',
			'--bma-space'           => '1.25rem',
			'--bma-accent-focus'    => self::hex_to_rgba( $c['accent'], 0.25 ),
		);

		$css = ":root{\n";
		foreach ( $vars as $name => $value ) {
			$css .= "\t{$name}: {$value};\n";
		}
		$css .= "}\n";
		$css .= "body{font-family:var(--bma-font-body);color:var(--bma-text);background:var(--bma-bg);}\n";
		$css .= "h1,h2,h3,h4,h5,h6,.bma-logo-placeholder,.bma-hero-content h1{font-family:var(--bma-font-heading);}\n";

		return $css;
	}

	/**
	 * Enqueue Google Fonts when needed.
	 */
	public static function enqueue_fonts(): void {
		$fonts   = self::get_fonts();
		$catalog = self::font_catalog();
		$families = array();

		foreach ( array( $fonts['heading'], $fonts['body'] ) as $slug ) {
			if ( ! empty( $catalog[ $slug ]['google'] ) ) {
				$families[ $slug ] = $catalog[ $slug ]['google'];
			}
		}

		if ( empty( $families ) ) {
			return;
		}

		$query = 'family=' . implode( '&family=', array_values( $families ) ) . '&display=swap';
		wp_enqueue_style(
			'bma-google-fonts',
			'https://fonts.googleapis.com/css2?' . $query,
			array(),
			null
		);
	}

	/**
	 * Attach design tokens after public CSS.
	 */
	public static function output_tokens(): void {
		if ( ! wp_style_is( 'bma-aj-public', 'enqueued' ) && ! wp_style_is( 'bma-aj-public', 'registered' ) ) {
			return;
		}
		wp_add_inline_style( 'bma-aj-public', self::build_css() );
	}

	/**
	 * Admin color picker on settings page.
	 *
	 * @param string $hook Hook.
	 */
	public static function admin_assets( string $hook ): void {
		if ( false === strpos( $hook, 'bma-settings' ) ) {
			return;
		}
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );
		wp_add_inline_script(
			'wp-color-picker',
			"jQuery(function($){ $('.bma-color-field').wpColorPicker(); });"
		);
	}

	/**
	 * Sanitize colors option.
	 *
	 * @param mixed $input Raw input.
	 * @return array<string, string>
	 */
	public static function sanitize_colors( $input ): array {
		$out = self::default_colors();
		if ( ! is_array( $input ) ) {
			return $out;
		}
		foreach ( $out as $key => $default ) {
			if ( empty( $input[ $key ] ) ) {
				continue;
			}
			$sanitized = sanitize_hex_color( $input[ $key ] );
			if ( $sanitized ) {
				$out[ $key ] = $sanitized;
			}
		}
		return $out;
	}

	/**
	 * Sanitize fonts option.
	 *
	 * @param mixed $input Raw input.
	 * @return array{heading: string, body: string}
	 */
	public static function sanitize_fonts( $input ): array {
		$out     = self::default_fonts();
		$catalog = self::font_catalog();
		if ( ! is_array( $input ) ) {
			return $out;
		}
		if ( ! empty( $input['heading'] ) && isset( $catalog[ $input['heading'] ] ) ) {
			$out['heading'] = sanitize_key( $input['heading'] );
		}
		if ( ! empty( $input['body'] ) && isset( $catalog[ $input['body'] ] ) ) {
			$out['body'] = sanitize_key( $input['body'] );
		}
		return $out;
	}
}
