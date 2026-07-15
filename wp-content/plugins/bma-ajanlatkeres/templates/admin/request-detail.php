<?php
defined( 'ABSPATH' ) || exit;
$id       = absint( $_GET['id'] ?? 0 );
$request  = BMA_AJ_Quote_Requests::get( $id );
if ( ! $request ) {
	echo '<div class="wrap"><p>' . esc_html__( 'Ajánlatkérés nem található.', 'bma-ajanlatkeres' ) . '</p></div>';
	return;
}
$services  = BMA_AJ_Quote_Requests::get_services( $id );
$statuses  = BMA_AJ_Statuses::get_all();
$history   = BMA_AJ_Statuses::get_history( $id );
$documents = BMA_AJ_Documents::get_for_request( $id );
$quote     = BMA_AJ_Quotes::get_latest_for_request( $id );
$current   = BMA_AJ_Statuses::get( (int) $request->status_id );
?>
<div class="wrap bma-admin-detail">
	<h1><?php echo esc_html( $request->request_number ); ?> – <?php echo esc_html( $request->client_name ); ?></h1>

	<div class="bma-admin-grid">
		<div class="bma-admin-panel">
			<h2><?php esc_html_e( 'Ügyféladatok', 'bma-ajanlatkeres' ); ?></h2>
			<table class="form-table">
				<tr><th><?php esc_html_e( 'Név', 'bma-ajanlatkeres' ); ?></th><td><?php echo esc_html( $request->client_name ); ?></td></tr>
				<tr><th><?php esc_html_e( 'E-mail', 'bma-ajanlatkeres' ); ?></th><td><?php echo esc_html( $request->client_email ); ?></td></tr>
				<tr><th><?php esc_html_e( 'Telefon', 'bma-ajanlatkeres' ); ?></th><td><?php echo esc_html( $request->client_phone ); ?></td></tr>
				<?php if ( $request->company_name ) : ?><tr><th><?php esc_html_e( 'Cég', 'bma-ajanlatkeres' ); ?></th><td><?php echo esc_html( $request->company_name ); ?></td></tr><?php endif; ?>
				<?php if ( $request->client_note ) : ?><tr><th><?php esc_html_e( 'Megjegyzés', 'bma-ajanlatkeres' ); ?></th><td><?php echo esc_html( $request->client_note ); ?></td></tr><?php endif; ?>
			</table>
		</div>

		<div class="bma-admin-panel">
			<h2><?php esc_html_e( 'Státusz', 'bma-ajanlatkeres' ); ?></h2>
			<p><span class="bma-status-badge" style="background:<?php echo esc_attr( $current->color ?? '#666' ); ?>"><?php echo esc_html( $current->name ?? '' ); ?></span></p>
			<form method="post">
				<?php wp_nonce_field( 'bma_admin_request', 'bma_admin_nonce' ); ?>
				<select name="status_id">
					<?php foreach ( $statuses as $st ) : ?>
						<option value="<?php echo esc_attr( $st->id ); ?>" <?php selected( $request->status_id, $st->id ); ?>><?php echo esc_html( $st->name ); ?></option>
					<?php endforeach; ?>
				</select>
				<textarea name="status_note" placeholder="<?php esc_attr_e( 'Megjegyzés…', 'bma-ajanlatkeres' ); ?>" rows="2" class="large-text"></textarea>
				<p><button type="submit" name="change_status" class="button button-primary"><?php esc_html_e( 'Státusz módosítása', 'bma-ajanlatkeres' ); ?></button></p>
			</form>

			<h3><?php esc_html_e( 'Státuszelőzmények', 'bma-ajanlatkeres' ); ?></h3>
			<ul class="bma-history-list">
				<?php foreach ( $history as $h ) : ?>
					<li><?php echo esc_html( mysql2date( 'Y.m.d H:i', $h->changed_at ) ); ?> – <?php echo esc_html( $h->status_name ); ?><?php echo $h->note ? ': ' . esc_html( $h->note ) : ''; ?></li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>

	<div class="bma-admin-panel">
		<h2><?php esc_html_e( 'Kiválasztott szolgáltatások', 'bma-ajanlatkeres' ); ?></h2>
		<?php foreach ( $services as $svc ) : ?>
			<div class="bma-service-block">
				<h3><?php echo esc_html( $svc->service_name ); ?></h3>
				<?php if ( ! empty( $svc->selected_items ) ) : ?>
					<p><strong><?php esc_html_e( 'Elemek:', 'bma-ajanlatkeres' ); ?></strong>
					<?php
					global $wpdb;
					foreach ( $svc->selected_items as $item_id ) {
						$name = $wpdb->get_var( $wpdb->prepare( "SELECT name FROM {$wpdb->prefix}bma_service_items WHERE id = %d", $item_id ) );
						echo esc_html( $name ) . ', ';
					}
					?>
					</p>
				<?php endif; ?>
				<?php if ( ! empty( $svc->field_values ) ) : ?>
					<table class="widefat">
						<?php foreach ( $svc->field_values as $key => $val ) : ?>
							<tr><th><?php echo esc_html( $key ); ?></th><td><?php echo esc_html( is_array( $val ) ? implode( ', ', $val ) : $val ); ?></td></tr>
						<?php endforeach; ?>
					</table>
				<?php endif; ?>
			</div>
		<?php endforeach; ?>
	</div>

	<div class="bma-admin-grid">
		<div class="bma-admin-panel">
			<h2><?php esc_html_e( 'Ajánlat', 'bma-ajanlatkeres' ); ?></h2>
			<form method="post" enctype="multipart/form-data">
				<?php wp_nonce_field( 'bma_admin_request', 'bma_admin_nonce' ); ?>
				<table class="form-table">
					<tr><th><?php esc_html_e( 'Ajánlat száma', 'bma-ajanlatkeres' ); ?></th><td><input type="text" name="quote_number" value="<?php echo esc_attr( $quote->quote_number ?? '' ); ?>" class="regular-text"></td></tr>
					<tr><th><?php esc_html_e( 'Nettó', 'bma-ajanlatkeres' ); ?></th><td><input type="number" step="0.01" name="net_amount" value="<?php echo esc_attr( $quote->net_amount ?? '' ); ?>"></td></tr>
					<tr><th><?php esc_html_e( 'Adó', 'bma-ajanlatkeres' ); ?></th><td><input type="number" step="0.01" name="tax_amount" value="<?php echo esc_attr( $quote->tax_amount ?? '' ); ?>"></td></tr>
					<tr><th><?php esc_html_e( 'Bruttó', 'bma-ajanlatkeres' ); ?></th><td><input type="number" step="0.01" name="gross_amount" value="<?php echo esc_attr( $quote->gross_amount ?? '' ); ?>"></td></tr>
					<tr><th><?php esc_html_e( 'Pénznem', 'bma-ajanlatkeres' ); ?></th><td><input type="text" name="currency" value="<?php echo esc_attr( $quote->currency ?? 'HUF' ); ?>"></td></tr>
					<tr><th><?php esc_html_e( 'Érvényesség', 'bma-ajanlatkeres' ); ?></th><td><input type="date" name="valid_until" value="<?php echo esc_attr( $quote->valid_until ?? '' ); ?>"></td></tr>
					<tr><th><?php esc_html_e( 'Ajánlati PDF', 'bma-ajanlatkeres' ); ?></th><td><input type="file" name="quote_pdf" accept=".pdf"></td></tr>
					<tr><th><?php esc_html_e( 'Megjegyzés', 'bma-ajanlatkeres' ); ?></th><td><textarea name="quote_note" rows="3" class="large-text"><?php echo esc_textarea( $quote->note ?? '' ); ?></textarea></td></tr>
				</table>
				<p><button type="submit" name="save_quote" class="button button-primary"><?php esc_html_e( 'Ajánlat mentése és küldése', 'bma-ajanlatkeres' ); ?></button></p>
			</form>
		</div>

		<div class="bma-admin-panel">
			<h2><?php esc_html_e( 'Dokumentumok', 'bma-ajanlatkeres' ); ?></h2>
			<ul>
				<?php foreach ( $documents as $doc ) : ?>
					<li><a href="<?php echo esc_url( BMA_AJ_Documents::get_download_url( (int) $doc->id ) ); ?>"><?php echo esc_html( $doc->title ); ?></a> (<?php echo esc_html( $doc->doc_type ); ?>)</li>
				<?php endforeach; ?>
			</ul>
			<form method="post" enctype="multipart/form-data">
				<?php wp_nonce_field( 'bma_admin_request', 'bma_admin_nonce' ); ?>
				<select name="doc_type">
					<option value="technical"><?php esc_html_e( 'Műszaki dokumentum', 'bma-ajanlatkeres' ); ?></option>
					<option value="plan"><?php esc_html_e( 'Terv', 'bma-ajanlatkeres' ); ?></option>
					<option value="handover"><?php esc_html_e( 'Átadási dokumentum', 'bma-ajanlatkeres' ); ?></option>
					<option value="other"><?php esc_html_e( 'Egyéb', 'bma-ajanlatkeres' ); ?></option>
				</select>
				<input type="file" name="admin_document">
				<button type="submit" class="button"><?php esc_html_e( 'Dokumentum feltöltése', 'bma-ajanlatkeres' ); ?></button>
			</form>
		</div>
	</div>

	<div class="bma-admin-panel">
		<h2><?php esc_html_e( 'Adminisztrátori megjegyzés', 'bma-ajanlatkeres' ); ?></h2>
		<form method="post">
			<?php wp_nonce_field( 'bma_admin_request', 'bma_admin_nonce' ); ?>
			<textarea name="admin_note" rows="4" class="large-text"><?php echo esc_textarea( $request->admin_note ); ?></textarea>
			<p><button type="submit" name="save_admin_note" class="button"><?php esc_html_e( 'Mentés', 'bma-ajanlatkeres' ); ?></button></p>
		</form>
	</div>
</div>
