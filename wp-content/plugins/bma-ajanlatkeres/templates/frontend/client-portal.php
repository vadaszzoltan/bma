<?php
defined( 'ABSPATH' ) || exit;
$user      = wp_get_current_user();
$requests  = BMA_AJ_Quote_Requests::get_for_user( $user->ID );
$detail_id = absint( $_GET['request'] ?? 0 ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

if ( $detail_id && BMA_AJ_Roles::can_access_request( $detail_id ) ) {
	$request   = BMA_AJ_Quote_Requests::get( $detail_id );
	$services  = BMA_AJ_Quote_Requests::get_services( $detail_id );
	$history       = BMA_AJ_Statuses::get_history( $detail_id );
	$client_docs   = BMA_AJ_Documents::get_for_request( $detail_id, true, 'client' );
	$deliverables  = BMA_AJ_Documents::get_for_request( $detail_id, true, 'deliverable' );
	$ordered_items = BMA_AJ_Quote_Requests::get_ordered_items( $detail_id );
	$plan_groups   = BMA_AJ_Documents::group_by_ordered_item( $ordered_items, $deliverables );
	$quote         = BMA_AJ_Quotes::get_latest_for_request( $detail_id );
	$status        = BMA_AJ_Statuses::get( (int) $request->status_id );
	?>
	<section class="bma-section bma-portal-detail">
		<div class="bma-container">
			<p class="bma-back-link"><a href="<?php echo esc_url( ( class_exists( 'BMA_AJ_I18n' ) ? BMA_AJ_I18n::page_url( 'ugyfelportal' ) : home_url( '/ugyfelportal/' ) ) ); ?>">&larr; <?php esc_html_e( 'Vissza a portálra', 'bma-ajanlatkeres' ); ?></a></p>
			<p class="bma-section-eyebrow"><?php esc_html_e( 'Ajánlatkérés', 'bma-ajanlatkeres' ); ?></p>
			<header class="bma-portal-detail-head">
				<div>
					<h1><?php echo esc_html( $request->request_number ); ?></h1>
					<p class="bma-section-intro"><?php esc_html_e( 'Kérés részletei, státusz, dokumentumok és ajánlat.', 'bma-ajanlatkeres' ); ?></p>
				</div>
				<span class="bma-status-badge" style="background:<?php echo esc_attr( $status->color ?? '#666' ); ?>"><?php echo esc_html( $status->name ?? '' ); ?></span>
			</header>

			<div class="bma-portal-grid">
				<div class="bma-portal-main">
					<section class="bma-portal-panel">
						<h2><?php esc_html_e( 'Szolgáltatások', 'bma-ajanlatkeres' ); ?></h2>
						<?php foreach ( $services as $svc ) : ?>
							<div class="bma-portal-block">
								<h3><?php echo esc_html( $svc->service_name ); ?></h3>
								<?php if ( ! empty( $svc->selected_items ) ) : ?>
									<ul class="bma-cart-tags">
										<?php
										global $wpdb;
										foreach ( $svc->selected_items as $item_id ) {
											$name = $wpdb->get_var( $wpdb->prepare( "SELECT name FROM {$wpdb->prefix}bma_service_items WHERE id = %d", $item_id ) );
											if ( $name ) {
												echo '<li>' . esc_html( $name ) . '</li>';
											}
										}
										?>
									</ul>
								<?php endif; ?>
							</div>
						<?php endforeach; ?>
					</section>

					<?php
					$shared = BMA_AJ_Quote_Requests::get_shared_fields( $request );
					if ( ! empty( $shared ) ) :
						?>
						<section class="bma-portal-panel">
							<h2><?php esc_html_e( 'Projekt adatok', 'bma-ajanlatkeres' ); ?></h2>
							<dl class="bma-field-summary">
								<?php foreach ( $shared as $key => $val ) : ?>
									<?php
									$display = BMA_AJ_Quote_Requests::format_field_value( $val );
									if ( '' === trim( $display ) ) {
										continue;
									}
									?>
									<dt><?php echo esc_html( BMA_AJ_Dynamic_Fields::get_label_for_key( $key ) ); ?></dt>
									<dd><?php echo esc_html( $display ); ?></dd>
								<?php endforeach; ?>
							</dl>
						</section>
					<?php endif; ?>

					<section class="bma-portal-panel">
						<h2><?php esc_html_e( 'Beküldött dokumentumaim', 'bma-ajanlatkeres' ); ?></h2>
						<?php if ( empty( $client_docs ) ) : ?>
							<p class="bma-cart-muted"><?php esc_html_e( 'Még nem töltött fel dokumentumot.', 'bma-ajanlatkeres' ); ?></p>
						<?php else : ?>
							<ul class="bma-doc-list">
								<?php foreach ( $client_docs as $doc ) : ?>
									<li>
										<a href="<?php echo esc_url( BMA_AJ_Documents::get_download_url( (int) $doc->id ) ); ?>"><?php echo esc_html( $doc->title ); ?></a>
										<span class="bma-doc-type"><?php echo esc_html( BMA_AJ_Documents::type_label( (string) $doc->doc_type ) ); ?></span>
									</li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>

						<h3><?php esc_html_e( 'További dokumentum feltöltése', 'bma-ajanlatkeres' ); ?></h3>
						<form id="bma-client-upload-form" class="bma-form" enctype="multipart/form-data" method="post">
							<input type="hidden" name="request_id" value="<?php echo esc_attr( (string) $detail_id ); ?>">
							<div class="bma-form-field">
								<label for="bma-client-doc"><?php esc_html_e( 'Fájl', 'bma-ajanlatkeres' ); ?></label>
								<input type="file" id="bma-client-doc" name="document" required accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.dwg,.zip">
							</div>
							<div class="bma-form-field">
								<label for="bma-client-doc-type"><?php esc_html_e( 'Dokumentumtípus', 'bma-ajanlatkeres' ); ?></label>
								<select id="bma-client-doc-type" name="doc_type">
									<option value="other"><?php esc_html_e( 'Egyéb', 'bma-ajanlatkeres' ); ?></option>
									<option value="technical"><?php esc_html_e( 'Műszaki dokumentum', 'bma-ajanlatkeres' ); ?></option>
									<option value="plan"><?php esc_html_e( 'Terv', 'bma-ajanlatkeres' ); ?></option>
								</select>
							</div>
							<button type="submit" class="bma-btn bma-btn-accent"><?php esc_html_e( 'Feltöltés', 'bma-ajanlatkeres' ); ?></button>
							<p id="bma-upload-status" class="bma-upload-status" role="status" aria-live="polite"></p>
						</form>
					</section>
				</div>

				<aside class="bma-portal-aside">
					<?php if ( $quote ) : ?>
						<section class="bma-portal-panel bma-quote-box">
							<p class="bma-tile-eyebrow"><?php esc_html_e( 'Ajánlat', 'bma-ajanlatkeres' ); ?></p>
							<p class="bma-price-total"><?php echo esc_html( BMA_AJ_Payments::format_money( (float) $quote->gross_amount, $quote->currency ?: 'EUR' ) ); ?></p>
							<p><?php printf( esc_html__( 'Ajánlat száma: %s', 'bma-ajanlatkeres' ), esc_html( $quote->quote_number ) ); ?></p>
							<p><strong><?php echo esc_html( BMA_AJ_Quotes::display_label( $quote ) ); ?></strong></p>
							<?php if ( $quote->valid_until ) : ?><p><?php printf( esc_html__( 'Érvényes: %s', 'bma-ajanlatkeres' ), esc_html( $quote->valid_until ) ); ?></p><?php endif; ?>
							<?php
							$pdf_url    = BMA_AJ_Payments::get_quote_pdf_url( $quote );
							$can_accept = in_array( $quote->status, array( 'sent', 'viewed' ), true ) && 'paid' !== ( $quote->payment_status ?? '' );
							?>
							<div class="bma-quote-actions">
								<?php if ( $pdf_url ) : ?>
									<p>
										<a href="<?php echo esc_url( $pdf_url ); ?>" class="bma-btn bma-btn-warning bma-btn-block" download>
											<?php esc_html_e( 'Ajánlati PDF letöltése', 'bma-ajanlatkeres' ); ?>
										</a>
									</p>
								<?php endif; ?>
								<?php if ( $can_accept ) : ?>
									<button type="button" class="bma-btn bma-btn-success bma-btn-block bma-btn-lg bma-accept-quote" data-request="<?php echo esc_attr( (string) $detail_id ); ?>" data-quote="<?php echo esc_attr( (string) $quote->id ); ?>">
										<?php esc_html_e( 'Ajánlat elfogadása', 'bma-ajanlatkeres' ); ?>
									</button>
								<?php elseif ( 'paid' === ( $quote->payment_status ?? '' ) ) : ?>
									<p class="bma-paid-badge"><?php esc_html_e( 'Fizetve', 'bma-ajanlatkeres' ); ?></p>
								<?php elseif ( 'bank_pending' === ( $quote->payment_status ?? '' ) ) : ?>
									<p><a href="<?php echo esc_url( home_url( '/fizetes/?request=' . $detail_id . '&quote=' . $quote->id . '&method=bank' ) ); ?>" class="bma-btn bma-btn-accent bma-btn-block"><?php esc_html_e( 'Banki utalás adatai', 'bma-ajanlatkeres' ); ?></a></p>
								<?php endif; ?>
							</div>
						</section>
					<?php endif; ?>

					<?php if ( ! empty( $ordered_items ) || ! empty( $deliverables ) ) : ?>
						<section class="bma-portal-panel bma-plans-box">
							<p class="bma-tile-eyebrow"><?php esc_html_e( 'Tervek', 'bma-ajanlatkeres' ); ?></p>
							<h2 class="bma-plans-box-title"><?php esc_html_e( 'Elkészült tervek', 'bma-ajanlatkeres' ); ?></h2>
							<?php foreach ( $plan_groups as $group ) : ?>
								<?php
								$item = $group['item'];
								$docs = $group['docs'];
								if ( empty( $item->id ) && empty( $docs ) ) {
									continue;
								}
								?>
								<div class="bma-plan-item">
									<h3><?php echo esc_html( $item->name ); ?></h3>
									<?php if ( ! empty( $item->service_name ) ) : ?>
										<p class="bma-plan-item-service"><?php echo esc_html( $item->service_name ); ?></p>
									<?php endif; ?>
									<?php if ( empty( $docs ) ) : ?>
										<p class="bma-plan-item-empty"><?php esc_html_e( 'Még nincs feltöltött terv ehhez a tételhez.', 'bma-ajanlatkeres' ); ?></p>
									<?php else : ?>
										<ul class="bma-doc-list bma-doc-list-deliverables">
											<?php foreach ( $docs as $doc ) : ?>
												<li>
													<a class="bma-btn bma-btn-warning bma-btn-block" href="<?php echo esc_url( BMA_AJ_Documents::get_download_url( (int) $doc->id ) ); ?>">
														<?php echo esc_html( $doc->title ); ?>
													</a>
													<span class="bma-doc-type"><?php echo esc_html( BMA_AJ_Documents::type_label( (string) $doc->doc_type ) ); ?></span>
												</li>
											<?php endforeach; ?>
										</ul>
									<?php endif; ?>
								</div>
							<?php endforeach; ?>
						</section>
					<?php endif; ?>

					<section class="bma-portal-panel">
						<h2><?php esc_html_e( 'Státuszelőzmények', 'bma-ajanlatkeres' ); ?></h2>
						<ul class="bma-history-list">
							<?php foreach ( $history as $h ) : ?>
								<li>
									<strong><?php echo esc_html( $h->status_name ); ?></strong>
									<span><?php echo esc_html( mysql2date( 'Y.m.d H:i', $h->changed_at ) ); ?></span>
								</li>
							<?php endforeach; ?>
						</ul>
					</section>
				</aside>
			</div>
		</div>
	</section>
	<?php
	return;
}

$stats = BMA_AJ_Quote_Requests::get_portal_stats( (int) $user->ID );
?>
<section class="bma-section bma-portal-dashboard">
	<div class="bma-container">
		<p class="bma-section-eyebrow"><?php esc_html_e( 'Ügyfélterület', 'bma-ajanlatkeres' ); ?></p>
		<header class="bma-portal-dash-head">
			<div>
				<h1><?php esc_html_e( 'Ügyfélportál', 'bma-ajanlatkeres' ); ?></h1>
				<p class="bma-section-intro"><?php printf( esc_html__( 'Üdvözöljük, %s!', 'bma-ajanlatkeres' ), esc_html( $user->display_name ) ); ?></p>
			</div>
			<a class="bma-link" href="<?php echo esc_url( wp_logout_url( home_url() ) ); ?>"><?php esc_html_e( 'Kijelentkezés', 'bma-ajanlatkeres' ); ?></a>
		</header>

		<?php if ( $stats['total'] > 0 ) : ?>
			<div class="bma-portal-stats" role="list">
				<div class="bma-portal-stat" role="listitem">
					<span class="bma-portal-stat-value"><?php echo esc_html( (string) $stats['active'] ); ?></span>
					<span class="bma-portal-stat-label"><?php esc_html_e( 'Aktív kérések', 'bma-ajanlatkeres' ); ?></span>
				</div>
				<div class="bma-portal-stat" role="listitem">
					<span class="bma-portal-stat-value"><?php echo esc_html( (string) $stats['new_quotes'] ); ?></span>
					<span class="bma-portal-stat-label"><?php esc_html_e( 'Új ajánlat', 'bma-ajanlatkeres' ); ?></span>
				</div>
				<div class="bma-portal-stat" role="listitem">
					<span class="bma-portal-stat-value"><?php echo esc_html( (string) $stats['documents'] ); ?></span>
					<span class="bma-portal-stat-label"><?php esc_html_e( 'Elkészült tervek', 'bma-ajanlatkeres' ); ?></span>
				</div>
				<div class="bma-portal-stat" role="listitem">
					<span class="bma-portal-stat-value"><?php echo esc_html( (string) $stats['payment_due'] ); ?></span>
					<span class="bma-portal-stat-label"><?php esc_html_e( 'Fizetésre vár', 'bma-ajanlatkeres' ); ?></span>
				</div>
			</div>
		<?php endif; ?>

		<div class="bma-portal-panel">
			<h2><?php esc_html_e( 'Ajánlatkéréseim', 'bma-ajanlatkeres' ); ?></h2>
			<?php if ( empty( $requests ) ) : ?>
				<div class="bma-flow-empty">
					<p><?php esc_html_e( 'Még nincs ajánlatkérése.', 'bma-ajanlatkeres' ); ?></p>
					<a href="<?php echo esc_url( ( class_exists( 'BMA_AJ_I18n' ) ? BMA_AJ_I18n::page_url( 'szolgaltatasok' ) : home_url( '/szolgaltatasok/' ) ) ); ?>" class="bma-btn bma-btn-accent bma-btn-lg"><?php esc_html_e( 'Ajánlatkérés indítása', 'bma-ajanlatkeres' ); ?></a>
				</div>
			<?php else : ?>
				<div class="bma-portal-table-wrap">
					<table class="bma-portal-table">
						<thead>
							<tr>
								<th><?php esc_html_e( 'Azonosító', 'bma-ajanlatkeres' ); ?></th>
								<th><?php esc_html_e( 'Dátum', 'bma-ajanlatkeres' ); ?></th>
								<th><?php esc_html_e( 'Szolgáltatások', 'bma-ajanlatkeres' ); ?></th>
								<th><?php esc_html_e( 'Státusz', 'bma-ajanlatkeres' ); ?></th>
								<th><?php esc_html_e( 'Ajánlat', 'bma-ajanlatkeres' ); ?></th>
								<th><span class="screen-reader-text"><?php esc_html_e( 'Művelet', 'bma-ajanlatkeres' ); ?></span></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ( $requests as $req ) : ?>
								<tr>
									<td data-label="<?php esc_attr_e( 'Azonosító', 'bma-ajanlatkeres' ); ?>"><?php echo esc_html( $req->request_number ); ?></td>
									<td data-label="<?php esc_attr_e( 'Dátum', 'bma-ajanlatkeres' ); ?>"><?php echo esc_html( mysql2date( 'Y.m.d', $req->submitted_at ) ); ?></td>
									<td data-label="<?php esc_attr_e( 'Szolgáltatások', 'bma-ajanlatkeres' ); ?>"><?php echo esc_html( $req->services_label ); ?></td>
									<td data-label="<?php esc_attr_e( 'Státusz', 'bma-ajanlatkeres' ); ?>"><span class="bma-status-badge" style="background:<?php echo esc_attr( $req->status_color ); ?>"><?php echo esc_html( $req->status_name ); ?></span></td>
									<td data-label="<?php esc_attr_e( 'Ajánlat', 'bma-ajanlatkeres' ); ?>"><?php echo esc_html( $req->quote_label ); ?></td>
									<td data-label="">
										<a class="bma-btn bma-btn-accent" href="<?php echo esc_url( add_query_arg( 'request', $req->id, class_exists( 'BMA_AJ_I18n' ) ? BMA_AJ_I18n::page_url( 'ugyfelportal' ) : home_url( '/ugyfelportal/' ) ) ); ?>"><?php esc_html_e( 'Részletek', 'bma-ajanlatkeres' ); ?></a>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
				<p class="bma-section-cta bma-section-cta--left">
					<a href="<?php echo esc_url( ( class_exists( 'BMA_AJ_I18n' ) ? BMA_AJ_I18n::page_url( 'szolgaltatasok' ) : home_url( '/szolgaltatasok/' ) ) ); ?>" class="bma-btn"><?php esc_html_e( 'Új ajánlatkérés', 'bma-ajanlatkeres' ); ?></a>
				</p>
			<?php endif; ?>
		</div>
	</div>
</section>
