<?php
/**
 * Client portal – list + detail (?request=).
 *
 * @package BMA_Ajanlatkeres
 */

defined( 'ABSPATH' ) || exit;

$user      = wp_get_current_user();
$requests  = BMA_AJ_Quote_Requests::get_for_user( (int) $user->ID );
$detail_id = absint( $_GET['request'] ?? 0 ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

$portal_url = function_exists( 'bma_page_url' ) ? bma_page_url( 'ugyfelportal' ) : home_url( '/ugyfelportal/' );
$home_url   = function_exists( 'bma_page_url' ) ? bma_page_url( 'fooldal' ) : home_url( '/' );
$svc_url    = function_exists( 'bma_page_url' ) ? bma_page_url( 'szolgaltatasok' ) : home_url( '/szolgaltatasok/' );
$contact_phone = class_exists( 'BMA_AJ_Company' ) ? ( BMA_AJ_Company::get( 'phone' ) ?: '+40 728 205 982' ) : '+40 728 205 982';
$contact_email = class_exists( 'BMA_AJ_Company' ) ? ( BMA_AJ_Company::get( 'email' ) ?: 'office@bmaeng.ro' ) : 'office@bmaeng.ro';

// ---------------------------------------------------------------------------
// Detail view
// ---------------------------------------------------------------------------
if ( $detail_id ) {
	if ( ! BMA_AJ_Roles::can_access_request( $detail_id ) ) {
		?>
		<section class="pagehead">
			<div class="pagehead-inner pagehead-inner--row">
				<div>
					<p class="crumb">
						<a href="<?php echo esc_url( $home_url ); ?>"><?php esc_html_e( 'Főoldal', 'bma-ajanlatkeres' ); ?></a>
						/ <?php esc_html_e( 'Ügyfélportál', 'bma-ajanlatkeres' ); ?>
					</p>
					<h1><?php esc_html_e( 'Ügyfélportál', 'bma-ajanlatkeres' ); ?></h1>
					<p class="pagehead-lead"><?php esc_html_e( 'Ehhez a kéréshez nincs hozzáférése.', 'bma-ajanlatkeres' ); ?></p>
				</div>
			</div>
		</section>
		<div class="appbody">
			<div class="bma-container">
				<a href="<?php echo esc_url( $portal_url ); ?>" class="btn btn--outline"><?php esc_html_e( '← Vissza a portálra', 'bma-ajanlatkeres' ); ?></a>
			</div>
		</div>
		<?php
		return;
	}

	$request       = BMA_AJ_Quote_Requests::get( $detail_id );
	$services      = BMA_AJ_Quote_Requests::get_services( $detail_id );
	$history       = BMA_AJ_Statuses::get_history( $detail_id );
	$all_docs      = BMA_AJ_Documents::get_for_request( $detail_id, true, 'all' );
	$deliverables  = BMA_AJ_Documents::get_for_request( $detail_id, true, 'deliverable' );
	$ordered_items = BMA_AJ_Quote_Requests::get_ordered_items( $detail_id );
	$plan_groups   = BMA_AJ_Documents::group_by_ordered_item( $ordered_items, $deliverables );
	$quote         = BMA_AJ_Quotes::get_latest_for_request( $detail_id );
	$status        = BMA_AJ_Statuses::get( (int) $request->status_id );
	$shared        = BMA_AJ_Quote_Requests::get_shared_fields( $request );
	$item_count    = count( $ordered_items );
	$max_mb        = max( 1, (int) floor( wp_max_upload_size() / MB_IN_BYTES ) );

	$req_proxy = (object) array(
		'quote_status'   => $quote->status ?? '',
		'payment_status' => $quote->payment_status ?? '',
		'status_slug'    => $status->slug ?? '',
	);
	$badge_cls = BMA_AJ_Quote_Requests::portal_badge_class( $req_proxy );
	$bucket    = BMA_AJ_Quote_Requests::portal_bucket( $req_proxy );

	$can_accept = $quote && in_array( $quote->status, array( 'sent', 'viewed' ), true ) && 'paid' !== ( $quote->payment_status ?? '' );
	$needs_pay  = $quote && 'fizetes' === $bucket;
	$pdf_url    = $quote ? BMA_AJ_Payments::get_quote_pdf_url( $quote ) : '';
	?>
	<section class="pagehead">
		<div class="pagehead-inner pagehead-inner--row">
			<div>
				<p class="crumb">
					<a href="<?php echo esc_url( $home_url ); ?>"><?php esc_html_e( 'Főoldal', 'bma-ajanlatkeres' ); ?></a>
					/
					<a href="<?php echo esc_url( $portal_url ); ?>"><?php esc_html_e( 'Ügyfélportál', 'bma-ajanlatkeres' ); ?></a>
					/ <?php echo esc_html( $request->request_number ); ?>
				</p>
				<p class="eyebrow eyebrow--light"><?php esc_html_e( 'Ügyfélterület', 'bma-ajanlatkeres' ); ?></p>
				<h1><?php esc_html_e( 'Ügyfélportál', 'bma-ajanlatkeres' ); ?></h1>
			</div>
		</div>
	</section>

	<div class="appbody">
		<div class="bma-container">
			<?php if ( is_user_logged_in() ) : ?>
			<div style="margin-bottom:18px">
				<a href="<?php echo esc_url( $portal_url ); ?>" class="small" style="font-weight:700;color:var(--bma-primary)">
					<?php esc_html_e( '← Vissza a portálra', 'bma-ajanlatkeres' ); ?>
				</a>
			</div>
			<?php endif; ?>

			<div style="display:flex;align-items:flex-end;justify-content:space-between;gap:24px;margin-bottom:24px;flex-wrap:wrap">
				<div>
					<p class="eyebrow"><?php esc_html_e( 'Ajánlatkérés', 'bma-ajanlatkeres' ); ?></p>
					<h2 class="h2"><?php echo esc_html( $request->request_number ); ?></h2>
					<p class="small muted" style="margin:8px 0 0">
						<?php
						printf(
							/* translators: %s: date */
							esc_html__( 'Beküldve: %s · Kérés részletei, státusz, dokumentumok és ajánlat.', 'bma-ajanlatkeres' ),
							esc_html( mysql2date( 'Y.m.d', $request->submitted_at ) )
						);
						?>
					</p>
				</div>
				<span class="badge <?php echo esc_attr( $badge_cls ); ?>" style="font-size:13px;padding:8px 14px">
					<?php echo esc_html( $status->name ?? '' ); ?>
				</span>
			</div>

			<div class="det-grid">
				<div>
					<div class="panel" style="margin-bottom:24px">
						<div class="panel-head">
							<h2><?php esc_html_e( 'Kiválasztott tételek', 'bma-ajanlatkeres' ); ?></h2>
							<span class="small muted">
								<?php
								echo esc_html(
									sprintf(
										/* translators: %d: item count */
										_n( '%d tétel', '%d tétel', $item_count, 'bma-ajanlatkeres' ),
										$item_count
									)
								);
								?>
							</span>
						</div>
						<div class="panel-body" style="padding-top:14px">
							<?php if ( empty( $ordered_items ) ) : ?>
								<p class="small muted" style="margin:0"><?php esc_html_e( 'Nincs kiválasztott tétel.', 'bma-ajanlatkeres' ); ?></p>
							<?php else : ?>
								<?php foreach ( $ordered_items as $it ) : ?>
									<?php
									$item_state = $status->name ?? '';
									foreach ( $plan_groups as $g ) {
										if ( (int) $g['item']->id === (int) $it->id && ! empty( $g['docs'] ) ) {
											$item_state = __( 'Terv elérhető', 'bma-ajanlatkeres' );
											break;
										}
									}
									?>
									<div class="rev-row">
										<span class="badge badge--soft"><?php echo esc_html( $it->service_name ); ?></span>
										<strong style="font-size:16px"><?php echo esc_html( $it->name ); ?></strong>
										<span class="small muted" style="margin-left:auto"><?php echo esc_html( $item_state ); ?></span>
									</div>
								<?php endforeach; ?>
							<?php endif; ?>
						</div>
					</div>

					<div class="panel" style="margin-bottom:24px">
						<div class="panel-head">
							<h2><?php esc_html_e( 'Projekt adatai', 'bma-ajanlatkeres' ); ?></h2>
						</div>
						<div class="panel-body" style="padding-top:10px">
							<?php
							$rows = array();
							if ( ! empty( $request->client_note ) ) {
								$rows[] = array(
									'label' => __( 'Megjegyzés', 'bma-ajanlatkeres' ),
									'value' => (string) $request->client_note,
								);
							}
							if ( 2 === (int) ( $request->flow_version ?? 1 ) ) {
								// Items are listed above; show the rest of the v2 summary.
								$skip  = __( 'Kiválasztott elemek', 'bma-ajanlatkeres' );
								$rows  = array_merge( $rows, array_values( array_filter( BMA_AJ_Quote_Flow::summary_rows( $request ), static fn( $r ) => $r['label'] !== $skip ) ) );
								$shared = array();
							}
							foreach ( $shared as $key => $val ) {
								$display = BMA_AJ_Quote_Requests::format_field_value( $val );
								if ( '' === trim( $display ) ) {
									continue;
								}
								$rows[] = array(
									'label' => BMA_AJ_Dynamic_Fields::get_label_for_key( (string) $key ),
									'value' => $display,
								);
							}
							?>
							<?php if ( empty( $rows ) ) : ?>
								<p class="small muted" style="margin:0"><?php esc_html_e( 'Nincs megadott projektadat.', 'bma-ajanlatkeres' ); ?></p>
							<?php else : ?>
								<?php foreach ( $rows as $row ) : ?>
									<div class="datarow">
										<span><?php echo esc_html( $row['label'] ); ?></span>
										<strong style="font-weight:600"><?php echo esc_html( $row['value'] ); ?></strong>
									</div>
								<?php endforeach; ?>
							<?php endif; ?>
						</div>
					</div>

					<div class="panel">
						<div class="panel-head">
							<h2><?php esc_html_e( 'Dokumentumok', 'bma-ajanlatkeres' ); ?></h2>
						</div>
						<div class="panel-body" style="padding-top:6px">
							<?php if ( empty( $all_docs ) ) : ?>
								<p class="small muted"><?php esc_html_e( 'Még nincs dokumentum ehhez a kéréshez.', 'bma-ajanlatkeres' ); ?></p>
							<?php else : ?>
								<?php foreach ( $all_docs as $doc ) : ?>
									<?php
									$who = ( 'client' === ( $doc->uploader_type ?? '' ) )
										? __( 'Ügyfél feltöltés', 'bma-ajanlatkeres' )
										: 'BMA';
									?>
									<div class="docrow">
										<svg class="icon icon--sm icon--navy" viewBox="0 0 24 24" aria-hidden="true"><path d="M6 3h9l4 4v14H6z"></path><path d="M15 3v4h4"></path></svg>
										<span style="flex:1">
											<strong style="font-size:15.5px;display:block"><?php echo esc_html( $doc->title ); ?></strong>
											<span class="tiny muted"><?php echo esc_html( $who . ' · ' . mysql2date( 'Y.m.d', $doc->uploaded_at ) ); ?></span>
										</span>
										<a href="<?php echo esc_url( BMA_AJ_Documents::get_download_url( (int) $doc->id ) ); ?>" class="tiny"><?php esc_html_e( 'Letöltés', 'bma-ajanlatkeres' ); ?></a>
									</div>
								<?php endforeach; ?>
							<?php endif; ?>

							<form id="bma-client-upload-form" class="bma-form" enctype="multipart/form-data" method="post" style="margin-top:18px">
								<input type="hidden" name="request_id" value="<?php echo esc_attr( (string) $detail_id ); ?>">
								<label class="field-label" for="bma-client-doc"><?php esc_html_e( 'További dokumentum feltöltése', 'bma-ajanlatkeres' ); ?></label>
								<div class="drop" data-bma-drop>
									<svg class="icon" viewBox="0 0 24 24" style="margin:0 auto 10px;display:block" aria-hidden="true"><path d="M12 16V4M8 8l4-4 4 4"></path><path d="M4 16v3h16v-3"></path></svg>
									<strong><?php esc_html_e( 'Húzza ide a fájlokat, vagy tallózzon', 'bma-ajanlatkeres' ); ?></strong>
									<span class="small muted">
										<?php
										echo esc_html(
											sprintf(
												/* translators: %d: max MB */
												__( 'PDF, DWG, JPG, DOCX · legfeljebb %d MB', 'bma-ajanlatkeres' ),
												$max_mb
											)
										);
										?>
									</span>
									<input type="file" id="bma-client-doc" name="document" class="bma-file-input" required accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.dwg,.zip">
								</div>
								<div style="margin-top:14px">
									<label class="field-label" for="bma-client-doc-type"><?php esc_html_e( 'Dokumentumtípus', 'bma-ajanlatkeres' ); ?></label>
									<select id="bma-client-doc-type" name="doc_type" class="input">
										<option value="other"><?php esc_html_e( 'Egyéb', 'bma-ajanlatkeres' ); ?></option>
										<option value="technical"><?php esc_html_e( 'Műszaki dokumentum', 'bma-ajanlatkeres' ); ?></option>
										<option value="plan"><?php esc_html_e( 'Terv', 'bma-ajanlatkeres' ); ?></option>
									</select>
								</div>
								<button type="submit" class="btn btn--primary btn--sm" style="margin-top:14px"><?php esc_html_e( 'Feltöltés', 'bma-ajanlatkeres' ); ?></button>
								<p id="bma-upload-status" class="bma-upload-status small muted" role="status" aria-live="polite"></p>
							</form>
						</div>
					</div>
				</div>

				<div>
					<?php if ( $quote ) : ?>
						<div class="offer" style="margin-bottom:24px">
							<h3><?php esc_html_e( 'Ajánlat', 'bma-ajanlatkeres' ); ?></h3>
							<div class="price"><?php echo esc_html( BMA_AJ_Payments::format_money( (float) $quote->gross_amount, $quote->currency ?: 'EUR' ) ); ?></div>
							<div class="meta">
								<?php printf( esc_html__( 'Ajánlat száma: %s', 'bma-ajanlatkeres' ), esc_html( $quote->quote_number ) ); ?><br>
								<?php if ( ! empty( $quote->valid_until ) ) : ?>
									<?php printf( esc_html__( 'Érvényes: %s', 'bma-ajanlatkeres' ), esc_html( $quote->valid_until ) ); ?><br>
								<?php endif; ?>
								<?php printf( esc_html__( 'Státusz: %s', 'bma-ajanlatkeres' ), esc_html( BMA_AJ_Quotes::display_label( $quote ) ) ); ?>
							</div>
							<?php if ( $pdf_url ) : ?>
								<a href="<?php echo esc_url( $pdf_url ); ?>" class="btn btn--primary" download><?php esc_html_e( 'Ajánlati PDF letöltése', 'bma-ajanlatkeres' ); ?></a>
							<?php endif; ?>
							<?php if ( $can_accept ) : ?>
								<button
									type="button"
									class="btn btn--primary bma-accept-quote"
									style="margin-top:12px;width:100%"
									data-request="<?php echo esc_attr( (string) $detail_id ); ?>"
									data-quote="<?php echo esc_attr( (string) $quote->id ); ?>"
								><?php esc_html_e( 'Ajánlat elfogadása', 'bma-ajanlatkeres' ); ?></button>
							<?php elseif ( $needs_pay && 'paid' !== ( $quote->payment_status ?? '' ) ) : ?>
								<?php if ( class_exists( 'BMA_AJ_Stripe' ) && BMA_AJ_Stripe::is_configured() ) : ?>
									<button
										type="button"
										class="btn btn--primary bma-stripe-pay"
										style="margin-top:12px;width:100%"
										data-request="<?php echo esc_attr( (string) $detail_id ); ?>"
										data-quote="<?php echo esc_attr( (string) $quote->id ); ?>"
									><?php esc_html_e( 'Kártyás fizetés (Stripe)', 'bma-ajanlatkeres' ); ?></button>
								<?php endif; ?>
								<a
									href="<?php echo esc_url( add_query_arg( array( 'request' => $detail_id, 'quote' => $quote->id ), home_url( '/fizetes/' ) ) ); ?>"
									class="btn btn--outline"
									style="margin-top:12px;width:100%;justify-content:center"
								><?php esc_html_e( 'Fizetés indítása', 'bma-ajanlatkeres' ); ?></a>
							<?php elseif ( 'paid' === ( $quote->payment_status ?? '' ) ) : ?>
								<p class="small" style="margin-top:16px;color:#c3cfe6"><?php esc_html_e( 'Fizetve', 'bma-ajanlatkeres' ); ?></p>
							<?php endif; ?>
						</div>
					<?php endif; ?>

					<div class="panel" style="margin-bottom:24px">
						<div class="panel-head"><h2><?php esc_html_e( 'Elkészült tervek', 'bma-ajanlatkeres' ); ?></h2></div>
						<div class="panel-body" style="padding-top:12px">
							<?php if ( empty( $deliverables ) ) : ?>
								<p class="small muted" style="margin:0"><?php esc_html_e( 'Az elkészült tervek itt jelennek meg, tételenként. Jelenleg még nincs feltöltött terv.', 'bma-ajanlatkeres' ); ?></p>
							<?php else : ?>
								<?php foreach ( $plan_groups as $group ) : ?>
									<?php
									$item = $group['item'];
									$docs = $group['docs'];
									if ( empty( $item->id ) && empty( $docs ) ) {
										continue;
									}
									?>
									<div style="margin-bottom:16px">
										<strong style="display:block;font-size:15.5px"><?php echo esc_html( $item->name ); ?></strong>
										<?php if ( ! empty( $item->service_name ) ) : ?>
											<span class="tiny muted"><?php echo esc_html( $item->service_name ); ?></span>
										<?php endif; ?>
										<?php if ( empty( $docs ) ) : ?>
											<p class="small muted"><?php esc_html_e( 'Még nincs feltöltött terv ehhez a tételhez.', 'bma-ajanlatkeres' ); ?></p>
										<?php else : ?>
											<?php foreach ( $docs as $doc ) : ?>
												<div class="docrow">
													<svg class="icon icon--sm" viewBox="0 0 24 24" aria-hidden="true"><path d="M6 3h9l4 4v14H6z"></path><path d="M15 3v4h4"></path></svg>
													<span style="flex:1"><strong style="font-size:15px;display:block"><?php echo esc_html( $doc->title ); ?></strong></span>
													<a href="<?php echo esc_url( BMA_AJ_Documents::get_download_url( (int) $doc->id ) ); ?>" class="tiny"><?php esc_html_e( 'Letöltés', 'bma-ajanlatkeres' ); ?></a>
												</div>
											<?php endforeach; ?>
										<?php endif; ?>
									</div>
								<?php endforeach; ?>
							<?php endif; ?>
						</div>
					</div>

					<div class="panel">
						<div class="panel-head"><h2><?php esc_html_e( 'Státuszelőzmények', 'bma-ajanlatkeres' ); ?></h2></div>
						<div class="panel-body">
							<div class="tl">
								<?php
								$i     = 0;
								$total = count( $history );
								foreach ( $history as $h ) :
									$cls = ( 0 === $i ) ? '' : 'is-muted';
									++$i;
									?>
									<div class="tl-item <?php echo esc_attr( $cls ); ?>">
										<strong><?php echo esc_html( $h->status_name ); ?></strong>
										<span><?php echo esc_html( mysql2date( 'Y.m.d H:i', $h->changed_at ) ); ?></span>
									</div>
								<?php endforeach; ?>
								<?php if ( ! $total ) : ?>
									<p class="small muted" style="margin:0"><?php esc_html_e( 'Még nincs státuszváltozás.', 'bma-ajanlatkeres' ); ?></p>
								<?php endif; ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php
	return;
}

// ---------------------------------------------------------------------------
// List view
// ---------------------------------------------------------------------------
$filter = isset( $_GET['szures'] ) ? sanitize_key( wp_unslash( $_GET['szures'] ) ) : 'osszes'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
if ( ! in_array( $filter, array( 'osszes', 'ajanlat', 'folyamatban', 'fizetes' ), true ) ) {
	$filter = 'osszes';
}

$stats = BMA_AJ_Quote_Requests::get_portal_stats( (int) $user->ID );

$filtered = array();
foreach ( $requests as $req ) {
	$bucket = BMA_AJ_Quote_Requests::portal_bucket( $req );
	if ( 'osszes' === $filter || $bucket === $filter ) {
		$filtered[] = $req;
	}
}

$urgent = BMA_AJ_Quote_Requests::portal_urgent_request( $requests );

$all_docs = array();
foreach ( $requests as $req ) {
	foreach ( BMA_AJ_Documents::get_for_request( (int) $req->id, true, 'all' ) as $doc ) {
		$all_docs[] = $doc;
	}
}
usort(
	$all_docs,
	static function ( $a, $b ) {
		return strcmp( (string) ( $b->uploaded_at ?? '' ), (string) ( $a->uploaded_at ?? '' ) );
	}
);

$filters = array(
	'osszes'     => array(
		'n'     => (int) $stats['total'],
		'label' => __( 'Összes kérés', 'bma-ajanlatkeres' ),
		'tab'   => __( 'Összes', 'bma-ajanlatkeres' ),
	),
	'ajanlat'    => array(
		'n'     => (int) $stats['ajanlat'],
		'label' => __( 'Ajánlat elérhető', 'bma-ajanlatkeres' ),
		'tab'   => __( 'Ajánlat elérhető', 'bma-ajanlatkeres' ),
	),
	'folyamatban' => array(
		'n'     => (int) $stats['folyamatban'],
		'label' => __( 'Folyamatban', 'bma-ajanlatkeres' ),
		'tab'   => __( 'Folyamatban', 'bma-ajanlatkeres' ),
	),
	'fizetes'    => array(
		'n'     => (int) $stats['fizetes'],
		'label' => __( 'Fizetésre vár', 'bma-ajanlatkeres' ),
		'tab'   => __( 'Fizetésre vár', 'bma-ajanlatkeres' ),
	),
);

$filter_url = static function ( string $key ) use ( $portal_url ): string {
	if ( 'osszes' === $key ) {
		return $portal_url;
	}
	return add_query_arg( 'szures', $key, $portal_url );
};
?>
<section class="pagehead">
	<div class="pagehead-inner pagehead-inner--row">
		<div>
			<p class="crumb">
				<a href="<?php echo esc_url( $home_url ); ?>"><?php esc_html_e( 'Főoldal', 'bma-ajanlatkeres' ); ?></a>
				/ <?php esc_html_e( 'Ügyfélportál', 'bma-ajanlatkeres' ); ?>
			</p>
			<p class="eyebrow eyebrow--light"><?php esc_html_e( 'Ügyfélterület', 'bma-ajanlatkeres' ); ?></p>
			<h1><?php esc_html_e( 'Ügyfélportál', 'bma-ajanlatkeres' ); ?></h1>
			<p class="pagehead-lead">
				<?php
				printf(
					/* translators: 1: display name, 2: email */
					esc_html__( 'Üdvözöljük, %1$s · %2$s', 'bma-ajanlatkeres' ),
					esc_html( $user->display_name ),
					esc_html( $user->user_email )
				);
				?>
			</p>
		</div>
		<div class="pagehead-actions">
			<a href="<?php echo esc_url( $svc_url ); ?>" class="btn btn--primary"><?php esc_html_e( 'Új ajánlatkérés', 'bma-ajanlatkeres' ); ?></a>
			<a href="<?php echo esc_url( wp_logout_url( $home_url ) ); ?>" class="btn btn--ghost-light"><?php esc_html_e( 'Kijelentkezés', 'bma-ajanlatkeres' ); ?></a>
		</div>
	</div>
</section>

<div class="appbody">
	<div class="bma-container">

		<?php if ( $urgent ) : ?>
			<?php
			$urgent_bucket = BMA_AJ_Quote_Requests::portal_bucket( $urgent );
			$urgent_url    = add_query_arg( 'request', (int) $urgent->id, $portal_url );
			if ( 'fizetes' === $urgent_bucket ) {
				$todo_title = ( 1 === (int) $stats['fizetes'] )
					? __( '1 díjbekérő vár rendezésre', 'bma-ajanlatkeres' )
					: sprintf(
						/* translators: %d: count */
						__( '%d díjbekérő vár rendezésre', 'bma-ajanlatkeres' ),
						(int) $stats['fizetes']
					);
				$todo_cta = __( 'Fizetés megtekintése', 'bma-ajanlatkeres' );
			} else {
				$todo_title = ( 1 === (int) $stats['ajanlat'] )
					? __( '1 ajánlat vár elfogadásra', 'bma-ajanlatkeres' )
					: sprintf(
						/* translators: %d: count */
						__( '%d ajánlat vár elfogadásra', 'bma-ajanlatkeres' ),
						(int) $stats['ajanlat']
					);
				$todo_cta = __( 'Ajánlat megtekintése', 'bma-ajanlatkeres' );
			}
			$todo_sub = $urgent->request_number . ' · ' . ( $urgent->services_label ?: '—' );
			if ( ! empty( $urgent->quote_valid_until ) ) {
				$days = (int) ceil( ( strtotime( $urgent->quote_valid_until . ' 23:59:59' ) - time() ) / DAY_IN_SECONDS );
				if ( $days > 0 ) {
					$todo_sub .= ' · ' . sprintf(
						/* translators: %d: days */
						__( 'az ajánlat %d napig érvényes', 'bma-ajanlatkeres' ),
						$days
					);
				}
			}
			?>
			<div class="todo">
				<span class="todo-ic" aria-hidden="true">
					<svg class="icon" viewBox="0 0 24 24"><path d="M6 3h9l4 4v14H6z"></path><path d="M15 3v4h4"></path><path d="M9 13h6M9 17h4"></path></svg>
				</span>
				<span>
					<strong><?php echo esc_html( $todo_title ); ?></strong>
					<span><?php echo esc_html( $todo_sub ); ?></span>
				</span>
				<a href="<?php echo esc_url( $urgent_url ); ?>" class="btn btn--primary btn--sm" style="margin-left:auto"><?php echo esc_html( $todo_cta ); ?></a>
			</div>
		<?php endif; ?>

		<div class="stats">
			<?php foreach ( $filters as $key => $meta ) : ?>
				<a
					href="<?php echo esc_url( $filter_url( $key ) ); ?>"
					class="stat<?php echo $filter === $key ? ' is-on' : ''; ?><?php echo 'ajanlat' === $key && (int) $meta['n'] > 0 ? ' is-accent' : ''; ?>"
				>
					<b><?php echo esc_html( (string) $meta['n'] ); ?></b>
					<span><?php echo esc_html( $meta['label'] ); ?></span>
				</a>
			<?php endforeach; ?>
		</div>

		<div class="panel">
			<div class="panel-head" style="padding-bottom:0;border-bottom:0;display:block">
				<h2 style="margin-bottom:14px"><?php esc_html_e( 'Ajánlatkéréseim', 'bma-ajanlatkeres' ); ?></h2>
				<div class="tabs" role="tablist">
					<?php foreach ( $filters as $key => $meta ) : ?>
						<a
							href="<?php echo esc_url( $filter_url( $key ) ); ?>"
							class="tab<?php echo $filter === $key ? ' is-on' : ''; ?>"
							role="tab"
							aria-selected="<?php echo $filter === $key ? 'true' : 'false'; ?>"
						><?php echo esc_html( $meta['tab'] ); ?></a>
					<?php endforeach; ?>
				</div>
			</div>

			<?php if ( empty( $filtered ) ) : ?>
				<div class="sum-empty" style="padding:34px 24px">
					<?php if ( empty( $requests ) ) : ?>
						<?php esc_html_e( 'Még nincs ajánlatkérése.', 'bma-ajanlatkeres' ); ?>
						<div style="margin-top:16px">
							<a href="<?php echo esc_url( $svc_url ); ?>" class="btn btn--primary btn--sm"><?php esc_html_e( 'Ajánlatkérés indítása', 'bma-ajanlatkeres' ); ?></a>
						</div>
					<?php else : ?>
						<?php esc_html_e( 'Ebben a nézetben most nincs ajánlatkérés.', 'bma-ajanlatkeres' ); ?>
					<?php endif; ?>
				</div>
			<?php else : ?>
				<div class="reqhead">
					<span><?php esc_html_e( 'Azonosító', 'bma-ajanlatkeres' ); ?></span>
					<span><?php esc_html_e( 'Dátum', 'bma-ajanlatkeres' ); ?></span>
					<span><?php esc_html_e( 'Szolgáltatások', 'bma-ajanlatkeres' ); ?></span>
					<span><?php esc_html_e( 'Státusz', 'bma-ajanlatkeres' ); ?></span>
					<span></span>
				</div>
				<?php foreach ( $filtered as $req ) : ?>
					<?php
					$items_label = sprintf(
						/* translators: %d: item count */
						_n( '%d tétel', '%d tétel', (int) $req->item_count, 'bma-ajanlatkeres' ),
						(int) $req->item_count
					);
					$next = BMA_AJ_Quote_Requests::portal_next_step( $req );
					$detail_link = add_query_arg( 'request', (int) $req->id, $portal_url );
					?>
					<div class="reqrow">
						<span>
							<span class="reqrow-id"><?php echo esc_html( $req->request_number ); ?></span>
							<span class="reqrow-sub"><?php echo esc_html( $items_label ); ?></span>
						</span>
						<span class="small"><?php echo esc_html( mysql2date( 'Y.m.d', $req->submitted_at ) ); ?></span>
						<span class="small"><?php echo esc_html( $req->services_label ); ?></span>
						<span>
							<span class="badge <?php echo esc_attr( BMA_AJ_Quote_Requests::portal_badge_class( $req ) ); ?>"><?php echo esc_html( $req->status_name ); ?></span>
							<?php if ( $next ) : ?>
								<span class="reqrow-sub"><?php echo esc_html( $next ); ?></span>
							<?php endif; ?>
						</span>
						<a href="<?php echo esc_url( $detail_link ); ?>" class="btn btn--outline btn--sm"><?php esc_html_e( 'Részletek', 'bma-ajanlatkeres' ); ?></a>
					</div>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>

		<div class="grid portal-cols" style="margin-top:26px">
			<div class="panel">
				<div class="panel-head">
					<h2><?php esc_html_e( 'Dokumentumaim', 'bma-ajanlatkeres' ); ?></h2>
				</div>
				<div class="panel-body" style="padding-top:6px">
					<?php if ( empty( $all_docs ) ) : ?>
						<p class="small muted" style="margin:0"><?php esc_html_e( 'Még nincs feltöltött vagy kapott dokumentum.', 'bma-ajanlatkeres' ); ?></p>
					<?php else : ?>
						<?php foreach ( array_slice( $all_docs, 0, 12 ) as $doc ) : ?>
							<?php
							$who = ( 'client' === ( $doc->uploader_type ?? '' ) )
								? __( 'Ügyfél feltöltés', 'bma-ajanlatkeres' )
								: 'BMA';
							?>
							<div class="docrow">
								<svg class="icon icon--sm icon--navy" viewBox="0 0 24 24" aria-hidden="true"><path d="M6 3h9l4 4v14H6z"></path><path d="M15 3v4h4"></path></svg>
								<span style="flex:1">
									<strong style="font-size:15.5px;display:block"><?php echo esc_html( $doc->title ); ?></strong>
									<span class="tiny muted"><?php echo esc_html( $who . ' · ' . mysql2date( 'Y.m.d', $doc->uploaded_at ) ); ?></span>
								</span>
								<a href="<?php echo esc_url( BMA_AJ_Documents::get_download_url( (int) $doc->id ) ); ?>" class="tiny"><?php esc_html_e( 'Letöltés', 'bma-ajanlatkeres' ); ?></a>
							</div>
						<?php endforeach; ?>
					<?php endif; ?>
				</div>
			</div>
			<div class="panel">
				<div class="panel-head"><h2><?php esc_html_e( 'Segítségre van szüksége?', 'bma-ajanlatkeres' ); ?></h2></div>
				<div class="panel-body">
					<p style="margin:0 0 18px;font-size:16px;line-height:1.55;color:#4a5a7d">
						<?php esc_html_e( 'Ha egy kéréssel kapcsolatban kérdése van, vagy módosítaná a kiválasztott tételeket, keressen minket.', 'bma-ajanlatkeres' ); ?>
					</p>
					<div class="btnrow" style="display:flex;gap:12px;flex-wrap:wrap">
						<a href="<?php echo esc_url( 'tel:' . preg_replace( '/\s+/', '', $contact_phone ) ); ?>" class="btn btn--outline btn--sm"><?php echo esc_html( $contact_phone ); ?></a>
						<a href="<?php echo esc_url( 'mailto:' . $contact_email ); ?>" class="btn btn--outline btn--sm"><?php echo esc_html( $contact_email ); ?></a>
					</div>
				</div>
			</div>
		</div>

	</div>
</div>
