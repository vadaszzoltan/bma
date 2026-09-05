<?php
/**
 * Company contact defaults and helpers (no dummy public fallbacks).
 *
 * @package BMA_Ajanlatkeres
 */

defined( 'ABSPATH' ) || exit;

final class BMA_AJ_Company {

	/**
	 * Canonical company defaults (spec. §4).
	 *
	 * @return array<string, string>
	 */
	public static function defaults(): array {
		return array(
			'company_name'     => 'BMA ENGINEERING S.R.L.',
			'contact_address'  => "Str. Raza Soarelui 11\n535600 Odorheiu Secuiesc\nHarghita, Romania",
			'contact_phone'    => '+40 728 205 982',
			'contact_email'    => 'office@bmaeng.ro',
			'opening_hours'    => 'Hétfő–Péntek, 08:00–17:00',
			'cui'              => '40593519',
			'admin_email'      => 'office@bmaeng.ro',
			'smtp_from'        => 'office@bmaeng.ro',
			'smtp_from_name'   => 'BMA Engineering',
			'bank_beneficiary' => 'BMA ENGINEERING S.R.L.',
			// Approximate (city centre) until a precise street pin is available.
			'map_lat'          => '46.3047462',
			'map_lng'          => '25.2950261',
			'map_zoom'         => '15',
		);
	}

	/**
	 * Apply defaults into WP options (overwrite).
	 */
	public static function seed_options(): void {
		$d = self::defaults();
		update_option( 'bma_company_name', $d['company_name'] );
		update_option( 'bma_contact_address', $d['contact_address'] );
		update_option( 'bma_contact_phone', $d['contact_phone'] );
		update_option( 'bma_contact_email', $d['contact_email'] );
		update_option( 'bma_opening_hours', $d['opening_hours'] );
		update_option( 'bma_company_cui', $d['cui'] );
		update_option( 'bma_admin_email', $d['admin_email'] );
		update_option( 'bma_smtp_from', $d['smtp_from'] );
		update_option( 'bma_smtp_from_name', $d['smtp_from_name'] );
		update_option( 'bma_map_lat', $d['map_lat'] );
		update_option( 'bma_map_lng', $d['map_lng'] );
		update_option( 'bma_map_zoom', $d['map_zoom'] );

		if ( ! get_option( 'bma_bank_beneficiary' ) ) {
			update_option( 'bma_bank_beneficiary', $d['bank_beneficiary'] );
		}

		// Do not seed fake social URLs – leave empty until real profiles exist.
		foreach ( array( 'bma_social_facebook', 'bma_social_linkedin', 'bma_social_instagram', 'bma_social_x', 'bma_social_youtube' ) as $key ) {
			$val = (string) get_option( $key, '' );
			if ( in_array( rtrim( $val, '/' ), array( 'https://facebook.com', 'https://linkedin.com', 'https://www.facebook.com', 'https://www.linkedin.com' ), true ) ) {
				delete_option( $key );
			}
		}
	}

	/**
	 * Get option with company default (never bracket-dummy).
	 *
	 * @param string $key Option key without bma_ prefix where mapped, or full key.
	 */
	public static function get( string $key ): string {
		$map = array(
			'company_name'    => 'bma_company_name',
			'contact_address' => 'bma_contact_address',
			'contact_phone'   => 'bma_contact_phone',
			'contact_email'   => 'bma_contact_email',
			'opening_hours'   => 'bma_opening_hours',
			'cui'             => 'bma_company_cui',
			'map_lat'         => 'bma_map_lat',
			'map_lng'         => 'bma_map_lng',
			'map_zoom'        => 'bma_map_zoom',
		);
		$option = $map[ $key ] ?? $key;
		$defaults = self::defaults();
		$fallback = $defaults[ $key ] ?? '';
		$value    = (string) get_option( $option, $fallback );
		$value    = trim( $value );

		// Strip legacy dummy markers if still stored.
		if ( preg_match( '/^\[KAPCSOLATI ADAT/u', $value ) || preg_match( '/^\[(IBAN|BIC|KEDVEZMÉNYEZETT)/u', $value ) ) {
			return $fallback;
		}

		$value = '' !== $value ? $value : $fallback;

		// Language-aware opening hours (Polylang overlay / UI map).
		if ( 'opening_hours' === $key && class_exists( 'BMA_AJ_I18n' ) ) {
			$tr = BMA_AJ_I18n::overlay( 'options', 'bma_opening_hours' );
			if ( '' !== $tr ) {
				return $tr;
			}
			$lang = BMA_AJ_I18n::current_lang();
			if ( 'hu' !== $lang ) {
				$map = get_option( 'bma_i18n_ui_strings', array() );
				if ( is_array( $map ) && ! empty( $map[ $value ][ $lang ] ) && is_string( $map[ $value ][ $lang ] ) ) {
					return $map[ $value ][ $lang ];
				}
			}
		}

		return $value;
	}

	/**
	 * Tel: href from phone string.
	 */
	public static function phone_href( string $phone = '' ): string {
		$phone = $phone ?: self::get( 'contact_phone' );
		return 'tel:' . preg_replace( '/[^\d+]/', '', $phone );
	}
}
