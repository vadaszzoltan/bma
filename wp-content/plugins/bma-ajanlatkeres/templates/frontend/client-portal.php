<?php
defined( 'ABSPATH' ) || exit;
$user     = wp_get_current_user();
$requests = BMA_AJ_Quote_Requests::get_for_user( $user->ID );
$detail_id = absint( $_GET['request'] ?? 0 );

if ( $detail_id && BMA_AJ_Roles::can_access_request( $detail_id ) ) {
	$request   = BMA_AJ_Quote_Requests::get( $detail_id );
	$services  = BMA_AJ_Quote_Requests::get_services( $detail_id );
	$history   = BMA_AJ_Statuses::get_history( $detail_id );
	$documents = BMA_AJ_Documents::get_for_request( $detail_id, true );
	$quote     = BMA_AJ_Quotes::get_latest_for_request( $detail_id );
	$status    = BMA_AJ_Statuses::get( (int) $request->status_id );
	?>
	<section class="bma-section bma-portal-detail">
		<div class="bma-container">
			<p><a href="<?php echo esc_url( home_url( '/ugyfelportal/' ) ); ?>">&larr; <?php esc_html_e( 'Vissza', 'bma-ajanlatkeres' ); ?></a></p>
			<h1><?php echo esc_html( $request->request_number ); ?></h1>
			<p><span class="bma-status-badge" style="background:<?php echo esc_attr( $status->color ?? '#666' ); ?>"><?php echo esc_html( $status->name ?? '' ); ?></span></p>

			<h2><?php esc_html_e( 'Szolgáltatások', 'bma-ajanlatkeres' ); ?></h2>
			<?php foreach ( $services as $svc ) : ?>
				<div class="bma-portal-block">
					<h3><?php echo esc_html( $svc->service_name ); ?></h3>
					<?php if ( ! empty( $svc->field_values ) ) : ?>
						<dl>
							<?php foreach ( $svc->field_values as $k => $v ) : ?>
								<dt><?php echo esc_html( $k ); ?></dt>
								<dd><?php echo esc_html( is_array( $v ) ? implode( ', ', $v ) : $v ); ?></dd>
							<?php endforeach; ?>
						</dl>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>

			<?php if ( $quote ) : ?>
				<h2><?php esc_html_e( 'Ajánlat', 'bma-ajanlatkeres' ); ?></h2>
				<p><?php printf( esc_html__( 'Bruttó összeg: %s %s', 'bma-ajanlatkeres' ), esc_html( number_format( (float) $quote->gross_amount, 0, ',', ' ' ) ), esc_html( $quote->currency ) ); ?></p>
				<?php if ( $quote->valid_until ) : ?><p><?php printf( esc_html__( 'Érvényes: %s', 'bma-ajanlatkeres' ), esc_html( $quote->valid_until ) ); ?></p><?php endif; ?>
			<?php endif; ?>

			<h2><?php esc_html_e( 'Dokumentumok', 'bma-ajanlatkeres' ); ?></h2>
			<ul class="bma-doc-list">
				<?php foreach ( $documents as $doc ) : ?>
					<li><a href="<?php echo esc_url( BMA_AJ_Documents::get_download_url( (int) $doc->id ) ); ?>"><?php echo esc_html( $doc->title ); ?></a></li>
				<?php endforeach; ?>
			</ul>

			<h2><?php esc_html_e( 'Státuszelőzmények', 'bma-ajanlatkeres' ); ?></h2>
			<ul class="bma-history-list">
				<?php foreach ( $history as $h ) : ?>
					<li><?php echo esc_html( mysql2date( 'Y.m.d H:i', $h->changed_at ) ); ?> – <?php echo esc_html( $h->status_name ); ?></li>
				<?php endforeach; ?>
			</ul>
		</div>
	</section>
	<?php
	return;
}
?>
<section class="bma-section bma-portal-dashboard">
	<div class="bma-container">
		<h1><?php esc_html_e( 'Ügyfélportál', 'bma-ajanlatkeres' ); ?></h1>
		<p><?php printf( esc_html__( 'Üdvözöljük, %s!', 'bma-ajanlatkeres' ), esc_html( $user->display_name ) ); ?> <a href="<?php echo esc_url( wp_logout_url( home_url() ) ); ?>"><?php esc_html_e( 'Kijelentkezés', 'bma-ajanlatkeres' ); ?></a></p>

		<h2><?php esc_html_e( 'Ajánlatkéréseim', 'bma-ajanlatkeres' ); ?></h2>
		<?php if ( empty( $requests ) ) : ?>
			<p><?php esc_html_e( 'Még nincs ajánlatkérése.', 'bma-ajanlatkeres' ); ?></p>
			<a href="<?php echo esc_url( home_url( '/szolgaltatasok/' ) ); ?>" class="bma-btn bma-btn-primary"><?php esc_html_e( 'Ajánlatkérés indítása', 'bma-ajanlatkeres' ); ?></a>
		<?php else : ?>
			<table class="bma-portal-table">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Azonosító', 'bma-ajanlatkeres' ); ?></th>
						<th><?php esc_html_e( 'Dátum', 'bma-ajanlatkeres' ); ?></th>
						<th><?php esc_html_e( 'Státusz', 'bma-ajanlatkeres' ); ?></th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $requests as $req ) : ?>
						<tr>
							<td><?php echo esc_html( $req->request_number ); ?></td>
							<td><?php echo esc_html( mysql2date( 'Y.m.d', $req->submitted_at ) ); ?></td>
							<td><span class="bma-status-badge" style="background:<?php echo esc_attr( $req->status_color ); ?>"><?php echo esc_html( $req->status_name ); ?></span></td>
							<td><a href="<?php echo esc_url( home_url( '/ugyfelportal/?request=' . $req->id ) ); ?>"><?php esc_html_e( 'Részletek', 'bma-ajanlatkeres' ); ?></a></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif; ?>
	</div>
</section>
