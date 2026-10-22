<?php
defined( 'ABSPATH' ) || exit;
$id       = absint( $_GET['id'] ?? 0 );
$request  = BMA_AJ_Quote_Requests::get( $id );
if ( ! $request ) {
	echo '<div class="wrap"><p>' . esc_html__( 'Ajánlatkérés nem található.', 'bma-ajanlatkeres' ) . '</p></div>';
	return;
}
$services      = BMA_AJ_Quote_Requests::get_services( $id );
$statuses      = BMA_AJ_Statuses::get_all();
$history       = BMA_AJ_Statuses::get_history( $id );
$client_docs   = BMA_AJ_Documents::get_for_request( $id, false, 'client' );
$deliverables  = BMA_AJ_Documents::get_for_request( $id, false, 'deliverable' );
$ordered_items = BMA_AJ_Quote_Requests::get_ordered_items( $id );
$plan_groups   = BMA_AJ_Documents::group_by_ordered_item( $ordered_items, $deliverables );
$quote         = BMA_AJ_Quotes::get_latest_for_request( $id );
$current       = BMA_AJ_Statuses::get( (int) $request->status_id );
?>
<div class="wrap bma-admin-detail">
	<h1><?php echo esc_html( $request->request_number ); ?> – <?php echo esc_html( $request->client_name ); ?></h1>
	<?php settings_errors( 'bma_quote' ); ?>

	<div class="bma-admin-grid">
		<div class="bma-admin-panel">
			<h2><?php esc_html_e( 'Ügyféladatok', 'bma-ajanlatkeres' ); ?></h2>
			<table class="form-table">
				<?php foreach ( BMA_AJ_Quote_Requests::get_client_data_rows( $request, 2 !== (int) ( $request->flow_version ?? 1 ) ) as $row ) : ?>
					<tr>
						<th><?php echo esc_html( $row['label'] ); ?></th>
						<td><?php echo esc_html( $row['value'] ); ?></td>
					</tr>
				<?php endforeach; ?>
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

	<?php if ( 2 === (int) ( $request->flow_version ?? 1 ) ) : ?>
		<div class="bma-admin-panel">
			<h2><?php esc_html_e( 'Az ajánlatkérés összefoglalója', 'bma-ajanlatkeres' ); ?></h2>
			<table class="form-table">
				<?php
				// Same language as the office e-mail, whatever the admin user's locale.
				$summary_rows = BMA_AJ_I18n::in_language( 'hu', static fn() => BMA_AJ_Quote_Flow::summary_rows( $request, true ) );
				foreach ( $summary_rows as $row ) :
					?>
					<tr>
						<th><?php echo esc_html( $row['label'] ); ?></th>
						<td><?php echo esc_html( $row['value'] ); ?></td>
					</tr>
				<?php endforeach; ?>
			</table>
		</div>
	<?php else : ?>
	<div class="bma-admin-panel">
		<h2><?php esc_html_e( 'Kiválasztott szolgáltatások', 'bma-ajanlatkeres' ); ?></h2>
		<?php foreach ( $services as $svc ) : ?>
			<div class="bma-service-block">
				<h3><?php echo esc_html( $svc->service_name ); ?></h3>
				<?php if ( ! empty( $svc->selected_items ) ) : ?>
					<p><strong><?php esc_html_e( 'Elemek:', 'bma-ajanlatkeres' ); ?></strong>
					<?php
					global $wpdb;
					$names = array();
					foreach ( $svc->selected_items as $item_id ) {
						$name = $wpdb->get_var( $wpdb->prepare( "SELECT name FROM {$wpdb->prefix}bma_service_items WHERE id = %d", $item_id ) );
						if ( $name ) {
							$names[] = $name;
						}
					}
					echo esc_html( implode( ', ', $names ) );
					?>
					</p>
				<?php endif; ?>
			</div>
		<?php endforeach; ?>
	</div>
	<?php endif; ?>

	<div class="bma-admin-grid">
		<div class="bma-admin-panel">
			<h2><?php esc_html_e( 'Ajánlat', 'bma-ajanlatkeres' ); ?></h2>
			<form method="post" enctype="multipart/form-data">
				<?php wp_nonce_field( 'bma_admin_request', 'bma_admin_nonce' ); ?>
				<?php if ( $quote ) : ?>
					<input type="hidden" name="quote_id" value="<?php echo esc_attr( (string) $quote->id ); ?>">
				<?php endif; ?>
				<table class="form-table">
					<tr>
						<th><?php esc_html_e( 'Ajánlat száma', 'bma-ajanlatkeres' ); ?></th>
						<td>
							<?php if ( $quote && ! empty( $quote->quote_number ) ) : ?>
								<code><?php echo esc_html( $quote->quote_number ); ?></code>
							<?php else : ?>
								<span class="description"><?php esc_html_e( 'Mentéskor automatikusan generálódik (Oferta-BMA-NR-YYYY-MM-DD).', 'bma-ajanlatkeres' ); ?></span>
							<?php endif; ?>
						</td>
					</tr>
					<tr><th><?php esc_html_e( 'Nettó', 'bma-ajanlatkeres' ); ?></th><td><input type="text" inputmode="decimal" name="net_amount" value="<?php echo esc_attr( isset( $quote->net_amount ) ? (string) $quote->net_amount : '' ); ?>" placeholder="0.00"></td></tr>
					<tr><th><?php esc_html_e( 'Adó', 'bma-ajanlatkeres' ); ?></th><td><input type="text" inputmode="decimal" name="tax_amount" value="<?php echo esc_attr( isset( $quote->tax_amount ) ? (string) $quote->tax_amount : '' ); ?>" placeholder="0.00"></td></tr>
					<tr><th><?php esc_html_e( 'Bruttó', 'bma-ajanlatkeres' ); ?></th><td><input type="text" inputmode="decimal" name="gross_amount" value="<?php echo esc_attr( isset( $quote->gross_amount ) ? (string) $quote->gross_amount : '' ); ?>" placeholder="0.00"><p class="description"><?php esc_html_e( 'Ha üresen hagyja, a nettó + adó összegéből számolódik.', 'bma-ajanlatkeres' ); ?></p></td></tr>
					<tr><th><?php esc_html_e( 'Pénznem', 'bma-ajanlatkeres' ); ?></th><td>
						<select name="currency">
							<option value="EUR" <?php selected( $quote->currency ?? 'EUR', 'EUR' ); ?>>EUR</option>
							<option value="RON" <?php selected( $quote->currency ?? '', 'RON' ); ?>>RON</option>
						</select>
					</td></tr>
					<tr><th><?php esc_html_e( 'Érvényesség', 'bma-ajanlatkeres' ); ?></th><td><input type="date" name="valid_until" value="<?php echo esc_attr( $quote->valid_until ?? '' ); ?>"></td></tr>
					<tr><th><?php esc_html_e( 'Ajánlati PDF', 'bma-ajanlatkeres' ); ?></th><td><input type="file" name="quote_pdf" accept=".pdf"></td></tr>
					<tr><th><?php esc_html_e( 'Megjegyzés', 'bma-ajanlatkeres' ); ?></th><td><textarea name="quote_note" rows="3" class="large-text"><?php echo esc_textarea( $quote->note ?? '' ); ?></textarea></td></tr>
					<?php if ( $quote ) : ?>
						<tr>
							<th><?php esc_html_e( 'Fizetési állapot', 'bma-ajanlatkeres' ); ?></th>
							<td>
								<strong><?php echo esc_html( BMA_AJ_Quotes::display_label( $quote ) ); ?></strong>
								<?php if ( ! empty( $quote->payment_method ) ) : ?>
									<br><span class="description"><?php echo esc_html( sprintf( /* translators: %s method */ __( 'Módszer: %s', 'bma-ajanlatkeres' ), $quote->payment_method ) ); ?></span>
								<?php endif; ?>
								<?php if ( ! empty( $quote->paid_at ) ) : ?>
									<br><span class="description"><?php echo esc_html( sprintf( /* translators: %s date */ __( 'Fizetve: %s', 'bma-ajanlatkeres' ), mysql2date( 'Y.m.d H:i', $quote->paid_at ) ) ); ?></span>
								<?php endif; ?>
							</td>
						</tr>
					<?php endif; ?>
				</table>
				<p><button type="submit" name="save_quote" class="button button-primary"><?php esc_html_e( 'Ajánlat mentése és küldése', 'bma-ajanlatkeres' ); ?></button></p>
				<?php if ( $quote && 'bank_pending' === ( $quote->payment_status ?? '' ) ) : ?>
					<p>
						<button type="submit" name="mark_bank_paid" class="button button-secondary" onclick="return confirm('<?php echo esc_js( __( 'Rögzíti a banki befizetést?', 'bma-ajanlatkeres' ) ); ?>');">
							<?php esc_html_e( 'Banki befizetés rögzítése', 'bma-ajanlatkeres' ); ?>
						</button>
					</p>
				<?php endif; ?>
			</form>
		</div>

		<div class="bma-admin-panel">
			<h2><?php esc_html_e( 'Ügyfél dokumentumai', 'bma-ajanlatkeres' ); ?></h2>
			<p class="description"><?php esc_html_e( 'Az ajánlatkéréshez és az ügyfélportálon feltöltött fájlok.', 'bma-ajanlatkeres' ); ?></p>
			<?php if ( empty( $client_docs ) ) : ?>
				<p><?php esc_html_e( 'Még nincs ügyfél által feltöltött dokumentum.', 'bma-ajanlatkeres' ); ?></p>
			<?php else : ?>
				<ul>
					<?php foreach ( $client_docs as $doc ) : ?>
						<li>
							<a href="<?php echo esc_url( BMA_AJ_Documents::get_download_url( (int) $doc->id ) ); ?>"><?php echo esc_html( $doc->title ); ?></a>
							(<?php echo esc_html( BMA_AJ_Documents::type_label( (string) $doc->doc_type ) ); ?>)
							<form method="post" style="display:inline;margin-left:0.5rem;" onsubmit="return confirm('<?php echo esc_js( __( 'Biztosan törli ezt a dokumentumot?', 'bma-ajanlatkeres' ) ); ?>');">
								<?php wp_nonce_field( 'bma_admin_request', 'bma_admin_nonce' ); ?>
								<input type="hidden" name="document_id" value="<?php echo esc_attr( (string) $doc->id ); ?>">
								<button type="submit" name="delete_document" class="button-link-delete"><?php esc_html_e( 'Törlés', 'bma-ajanlatkeres' ); ?></button>
							</form>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>

		<div class="bma-admin-panel">
			<h2><?php esc_html_e( 'Elkészült tervek és dokumentumok', 'bma-ajanlatkeres' ); ?></h2>
			<p class="description"><?php esc_html_e( 'A kész terveket a megrendelt szolgáltatáselemekhez külön töltse fel. Az ügyfélportálon is elemenként jelennek meg.', 'bma-ajanlatkeres' ); ?></p>

			<?php if ( empty( $ordered_items ) ) : ?>
				<p><?php esc_html_e( 'Ehhez a kéréshez nincs kiválasztott szolgáltatáselem.', 'bma-ajanlatkeres' ); ?></p>
			<?php else : ?>
				<?php foreach ( $plan_groups as $group ) : ?>
					<?php
					$item = $group['item'];
					$docs = $group['docs'];
					if ( empty( $item->id ) ) {
						continue;
					}
					?>
					<div class="bma-admin-deliverable-block" style="margin:1.25rem 0;padding:1rem;border:1px solid #c3c4c7;background:#fff;">
						<h3 style="margin-top:0;">
							<?php echo esc_html( $item->name ); ?>
							<?php if ( ! empty( $item->service_name ) ) : ?>
								<span class="description"> — <?php echo esc_html( $item->service_name ); ?></span>
							<?php endif; ?>
						</h3>
						<?php if ( empty( $docs ) ) : ?>
							<p class="description"><?php esc_html_e( 'Még nincs feltöltött fájl ehhez a tételhez.', 'bma-ajanlatkeres' ); ?></p>
						<?php else : ?>
							<ul>
								<?php foreach ( $docs as $doc ) : ?>
									<li>
										<a href="<?php echo esc_url( BMA_AJ_Documents::get_download_url( (int) $doc->id ) ); ?>"><?php echo esc_html( $doc->title ); ?></a>
										(<?php echo esc_html( BMA_AJ_Documents::type_label( (string) $doc->doc_type ) ); ?>)
										<form method="post" style="display:inline;margin-left:0.5rem;" onsubmit="return confirm('<?php echo esc_js( __( 'Biztosan törli ezt a dokumentumot?', 'bma-ajanlatkeres' ) ); ?>');">
											<?php wp_nonce_field( 'bma_admin_request', 'bma_admin_nonce' ); ?>
											<input type="hidden" name="document_id" value="<?php echo esc_attr( (string) $doc->id ); ?>">
											<button type="submit" name="delete_document" class="button-link-delete"><?php esc_html_e( 'Törlés', 'bma-ajanlatkeres' ); ?></button>
										</form>
									</li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>
						<form method="post" enctype="multipart/form-data" style="margin-top:0.75rem;">
							<?php wp_nonce_field( 'bma_admin_request', 'bma_admin_nonce' ); ?>
							<input type="hidden" name="deliverable_item_id" value="<?php echo esc_attr( (string) $item->id ); ?>">
							<select name="deliverable_type">
								<option value="plan"><?php esc_html_e( 'Terv', 'bma-ajanlatkeres' ); ?></option>
								<option value="technical"><?php esc_html_e( 'Műszaki dokumentum', 'bma-ajanlatkeres' ); ?></option>
								<option value="handover"><?php esc_html_e( 'Átadási dokumentum', 'bma-ajanlatkeres' ); ?></option>
								<option value="other"><?php esc_html_e( 'Egyéb', 'bma-ajanlatkeres' ); ?></option>
							</select>
							<input type="file" name="admin_deliverable" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.dwg,.zip" required>
							<button type="submit" class="button button-primary"><?php esc_html_e( 'Feltöltés ehhez a tételhez', 'bma-ajanlatkeres' ); ?></button>
						</form>
					</div>
				<?php endforeach; ?>
			<?php endif; ?>

			<?php
			// Legacy / unassigned deliverables.
			foreach ( $plan_groups as $group ) {
				if ( ! empty( $group['item']->id ) || empty( $group['docs'] ) ) {
					continue;
				}
				?>
				<div class="bma-admin-deliverable-block" style="margin:1.25rem 0;padding:1rem;border:1px solid #c3c4c7;">
					<h3 style="margin-top:0;"><?php echo esc_html( $group['item']->name ); ?></h3>
					<ul>
						<?php foreach ( $group['docs'] as $doc ) : ?>
							<li>
								<a href="<?php echo esc_url( BMA_AJ_Documents::get_download_url( (int) $doc->id ) ); ?>"><?php echo esc_html( $doc->title ); ?></a>
								(<?php echo esc_html( BMA_AJ_Documents::type_label( (string) $doc->doc_type ) ); ?>)
								<form method="post" style="display:inline;margin-left:0.5rem;" onsubmit="return confirm('<?php echo esc_js( __( 'Biztosan törli ezt a dokumentumot?', 'bma-ajanlatkeres' ) ); ?>');">
									<?php wp_nonce_field( 'bma_admin_request', 'bma_admin_nonce' ); ?>
									<input type="hidden" name="document_id" value="<?php echo esc_attr( (string) $doc->id ); ?>">
									<button type="submit" name="delete_document" class="button-link-delete"><?php esc_html_e( 'Törlés', 'bma-ajanlatkeres' ); ?></button>
								</form>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
				<?php
			}
			?>
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
