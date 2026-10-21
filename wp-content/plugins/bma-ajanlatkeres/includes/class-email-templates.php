<?php
/**
 * Email templates and sending.
 *
 * @package BMA_Ajanlatkeres
 */

defined( 'ABSPATH' ) || exit;

final class BMA_AJ_Email_Templates {

	/**
	 * Init hooks.
	 */
	public static function init(): void {
		// Admin UI in BMA_AJ_Admin.
	}

	/**
	 * Seed default email templates.
	 */
	public static function seed_defaults(): void {
		global $wpdb;
		$table = $wpdb->prefix . 'bma_email_templates';

		$count = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table}" );
		if ( $count > 0 ) {
			return;
		}

		$templates = array(
			array(
				'slug'    => 'request_confirmation',
				'name'    => 'Ajánlatkérés visszaigazolás (ügyfél)',
				'subject' => 'Ajánlatkérés visszaigazolás – {request_number}',
				'body'    => "Kedves {client_name}!\n\nKöszönjük ajánlatkérését. Azonosító: {request_number}\n\nSzolgáltatások: {services}\n\nAjánlatkérését feldolgozzuk. Az aktuális állapotot az ügyfélportálon követheti:\n{portal_link}\n\nÜdvözlettel,\nBMA Engineering\n{company_phone}\n{company_email}",
			),
			array(
				'slug'    => 'request_admin_notify',
				'name'    => 'Új ajánlatkérés (admin)',
				'subject' => 'Új ajánlatkérés – {request_number}',
				'body'    => "Új ajánlatkérés érkezett.\n\nAzonosító: {request_number}\n\nÜgyféladatok:\n{client_data}\n\nSzolgáltatások: {services}\n\nAdmin felület: {admin_link}",
			),
			array(
				'slug'    => 'status_change',
				'name'    => 'Státuszváltozás',
				'subject' => 'Ajánlatkérés státusz frissítés – {request_number}',
				'body'    => "Kedves {client_name}!\n\nAjánlatkérése ({request_number}) státusza megváltozott: {status}\n\nRészletek: {portal_link}\n\nÜdvözlettel,\nBMA Engineering",
			),
			array(
				'slug'    => 'info_request',
				'name'    => 'További információ kérése',
				'subject' => 'További információ szükséges – {request_number}',
				'body'    => "Kedves {client_name}!\n\nAjánlatkéréséhez ({request_number}) további információra van szükségünk.\n\nKérjük, lépjen kapcsolatba velünk, vagy töltse fel a hiányzó dokumentumokat az ügyfélportálon:\n{portal_link}\n\nÜdvözlettel,\nBMA Engineering",
			),
			array(
				'slug'    => 'quote_uploaded',
				'name'    => 'Ajánlat feltöltve',
				'subject' => 'Ajánlat elérhető – {request_number}',
				'body'    => "Kedves {client_name}!\n\nAjánlatkéréséhez ({request_number}) elkészült az ajánlat.\n\nBruttó összeg: {quote_amount}\nÉrvényesség: {quote_valid_until}\n\nAjánlat megtekintése: {portal_link}\n\nÜdvözlettel,\nBMA Engineering",
			),
			array(
				'slug'    => 'document_uploaded',
				'name'    => 'Új dokumentum feltöltve',
				'subject' => 'Új dokumentum – {request_number}',
				'body'    => "Kedves {client_name}!\n\nÚj dokumentum került feltöltésre az ajánlatkéréséhez ({request_number}).\n\nMegtekintés: {portal_link}\n\nÜdvözlettel,\nBMA Engineering",
			),
			array(
				'slug'    => 'documents_ready',
				'name'    => 'Elkészült tervek elérhetők',
				'subject' => 'Elkészült dokumentumok – {request_number}',
				'body'    => "Kedves {client_name}!\n\nAz ajánlatkéréshez ({request_number}) tartozó elkészült tervek és dokumentumok elérhetők az ügyfélportálon:\n{portal_link}\n\nÜdvözlettel,\nBMA Engineering",
			),
		);

		foreach ( $templates as $tpl ) {
			$wpdb->insert( $table, $tpl );
		}
	}

	/**
	 * Get template by slug.
	 *
	 * @param string $slug Template slug.
	 */
	public static function get_by_slug( string $slug ): ?object {
		global $wpdb;
		$table = $wpdb->prefix . 'bma_email_templates';

		$row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE slug = %s AND is_active = 1", $slug ) );
		return $row ?: null;
	}

	/**
	 * Send email for a request.
	 *
	 * @param string   $slug       Template slug.
	 * @param int      $request_id Request ID.
	 * @param string[] $extra      Extra replacements.
	 */
	public static function send( string $slug, int $request_id, array $extra = array() ): bool {
		$template = self::get_by_slug( $slug );
		if ( ! $template ) {
			return false;
		}

		$data = self::build_replacements( $request_id, $extra, $slug );

		$subject = self::replace( $template->subject, $data );
		$body    = self::replace( $template->body, $data );

		$request = BMA_AJ_Quote_Requests::get( $request_id );
		if ( ! $request ) {
			return false;
		}

		$to = 'request_admin_notify' === $slug
			? get_option( 'bma_admin_email', get_option( 'admin_email' ) )
			: $request->client_email;

		$headers = array( 'Content-Type: text/plain; charset=UTF-8' );

		return (bool) wp_mail( $to, $subject, $body, $headers );
	}

	/**
	 * Build replacement tokens.
	 *
	 * @param int      $request_id Request ID.
	 * @param string[] $extra      Extra tokens.
	 * @param string   $slug       Template slug.
	 * @return array<string, string>
	 */
	private static function build_replacements( int $request_id, array $extra = array(), string $slug = '' ): array {
		$request  = BMA_AJ_Quote_Requests::get( $request_id );
		$services = BMA_AJ_Quote_Requests::get_services( $request_id );
		$status   = $request ? BMA_AJ_Statuses::get( (int) $request->status_id ) : null;
		$quote    = BMA_AJ_Quotes::get_latest_for_request( $request_id );

		$service_names = array_map(
			static fn( $s ) => $s->service_name,
			$services
		);

		$field_lines = array();
		if ( $request ) {
			foreach ( BMA_AJ_Quote_Requests::get_client_data_rows( $request ) as $row ) {
				$field_lines[] = $row['label'] . ': ' . $row['value'];
			}
		}

		// v2 requests: {services} expands to the full summary (phase, items, add-ons, project data).
		$services_text = implode( ', ', $service_names );
		$summary_text  = '';
		if ( $request && (int) ( $request->flow_version ?? 1 ) >= BMA_AJ_Quote_Flow::FLOW_VERSION ) {
			$summary_text  = BMA_AJ_Quote_Flow::summary_text( $request, 'request_admin_notify' === $slug );
			$services_text = "\n" . $summary_text;
			$field_lines   = array();
			foreach ( BMA_AJ_Quote_Requests::get_client_data_rows( $request, false ) as $row ) {
				$field_lines[] = $row['label'] . ': ' . $row['value'];
			}
		}

		$data = array(
			'{request_number}'    => $request->request_number ?? '',
			'{client_name}'       => $request->client_name ?? '',
			'{client_email}'      => $request->client_email ?? '',
			'{client_phone}'      => $request->client_phone ?? '',
			'{client_data}'       => implode( "\n", $field_lines ),
			'{services}'          => $services_text,
			'{summary}'           => $summary_text,
			'{status}'            => $status->name ?? '',
			'{portal_link}'       => home_url( '/ugyfelportal/?request=' . $request_id ),
			'{admin_link}'        => admin_url( 'admin.php?page=bma-requests&action=view&id=' . $request_id ),
			'{quote_amount}'      => $quote ? number_format( (float) $quote->gross_amount, 0, ',', ' ' ) . ' ' . $quote->currency : '',
			'{quote_valid_until}' => $quote && $quote->valid_until ? $quote->valid_until : '',
			'{company_name}'      => BMA_AJ_Company::get( 'company_name' ),
			'{company_phone}'     => BMA_AJ_Company::get( 'contact_phone' ),
			'{company_email}'     => BMA_AJ_Company::get( 'contact_email' ),
			'{company_address}'   => BMA_AJ_Company::get( 'contact_address' ),
		);

		return array_merge( $data, $extra );
	}

	/**
	 * Replace tokens in text.
	 *
	 * @param string               $text Text.
	 * @param array<string,string> $data Replacements.
	 */
	private static function replace( string $text, array $data ): string {
		return str_replace( array_keys( $data ), array_values( $data ), $text );
	}
}
