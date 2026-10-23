<?php
/**
 * Quote request page: confirmation after submit, otherwise the v2 wizard.
 *
 * @package BMA_Ajanlatkeres
 */

defined( 'ABSPATH' ) || exit;

$home_url      = function_exists( 'bma_page_url' ) ? bma_page_url( 'fooldal' ) : home_url( '/' );
$svc_url       = function_exists( 'bma_page_url' ) ? bma_page_url( 'szolgaltatasok' ) : home_url( '/szolgaltatasok/' );
$portal_url    = function_exists( 'bma_page_url' ) ? bma_page_url( 'ugyfelportal' ) : home_url( '/ugyfelportal/' );

$confirm_number = '';
if ( isset( $_GET['submitted'] ) ) {
	$confirm_number = BMA_AJ_Quote_Requests::confirmation_number_from_request();
	if ( '' === $confirm_number && ! empty( $_GET['number'] ) ) {
		// Legacy redirects without token still show the number once (cart already cleared).
		$confirm_number = sanitize_text_field( wp_unslash( $_GET['number'] ) );
	}
}

if ( $confirm_number ) :
	?>
	<section class="pagehead">
		<div class="pagehead-inner pagehead-inner--wizard">
			<p class="crumb">
				<a href="<?php echo esc_url( $home_url ); ?>"><?php esc_html_e( 'Főoldal', 'bma-ajanlatkeres' ); ?></a>
				/
				<a href="<?php echo esc_url( $svc_url ); ?>"><?php esc_html_e( 'Szolgáltatások', 'bma-ajanlatkeres' ); ?></a>
				/ <?php esc_html_e( 'Ajánlatkérés', 'bma-ajanlatkeres' ); ?>
			</p>
			<p class="eyebrow eyebrow--light"><?php esc_html_e( 'Ajánlatkérés', 'bma-ajanlatkeres' ); ?></p>
			<h1><?php esc_html_e( 'Visszaigazolás', 'bma-ajanlatkeres' ); ?></h1>
		</div>
	</section>

	<div class="appbody">
		<div class="bma-container">
			<div class="done-wrap" role="status" aria-live="polite">
				<div class="done-ic" aria-hidden="true">
					<svg viewBox="0 0 24 24" width="38" height="38" fill="none" stroke="#ee7643" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><path d="M8 12.5l2.6 2.6L16 9.5"></path></svg>
				</div>
				<p class="eyebrow"><?php esc_html_e( 'Beküldve', 'bma-ajanlatkeres' ); ?></p>
				<h2 class="h2"><?php esc_html_e( 'Köszönjük, megkaptuk az ajánlatkérését', 'bma-ajanlatkeres' ); ?></h2>
				<div class="done-id"><?php echo esc_html( $confirm_number ); ?></div>
				<p class="small muted" style="margin:0">
					<?php esc_html_e( 'Az azonosítót e-mailben is elküldtük. Erre hivatkozva bármikor tud érdeklődni.', 'bma-ajanlatkeres' ); ?>
				</p>
				<div class="nextsteps">
					<div class="nextstep">
						<b><?php esc_html_e( 'Most', 'bma-ajanlatkeres' ); ?></b>
						<strong style="display:block;font-size:16px;margin-bottom:6px"><?php esc_html_e( 'Visszaigazolás', 'bma-ajanlatkeres' ); ?></strong>
						<span class="small muted"><?php esc_html_e( 'E-mailben elküldtük a kérés összefoglalóját és a feltöltött dokumentumok listáját.', 'bma-ajanlatkeres' ); ?></span>
					</div>
					<div class="nextstep">
						<b><?php echo esc_html( BMA_AJ_Company::fill_response_days( __( '[X] munkanapon belül', 'bma-ajanlatkeres' ) ) ); ?></b>
						<strong style="display:block;font-size:16px;margin-bottom:6px"><?php esc_html_e( 'Felmérés', 'bma-ajanlatkeres' ); ?></strong>
						<span class="small muted"><?php esc_html_e( 'Átnézzük a megadott adatokat, és ha szükséges, egyeztetünk a hiányzó részletekről.', 'bma-ajanlatkeres' ); ?></span>
						<span class="small muted" style="display:block;margin-top:8px"><?php esc_html_e( 'Ha beszerzést is kért (pl. urbanisztikai bizonylat), annak költségét és idejét az ajánlatban külön tételként látja.', 'bma-ajanlatkeres' ); ?></span>
					</div>
					<div class="nextstep">
						<b><?php esc_html_e( 'Ezután', 'bma-ajanlatkeres' ); ?></b>
						<strong style="display:block;font-size:16px;margin-bottom:6px"><?php esc_html_e( 'Ajánlat', 'bma-ajanlatkeres' ); ?></strong>
						<span class="small muted"><?php esc_html_e( 'Az elkészült ajánlat az ügyfélportálon és e-mailben is elérhető lesz.', 'bma-ajanlatkeres' ); ?></span>
					</div>
				</div>
				<div class="btnrow" style="display:flex;gap:12px;justify-content:center;margin-top:34px;flex-wrap:wrap">
					<a href="<?php echo esc_url( $portal_url ); ?>" class="btn btn--primary"><?php esc_html_e( 'Ügyfélportál megnyitása', 'bma-ajanlatkeres' ); ?></a>
					<a href="<?php echo esc_url( BMA_AJ_I18n::page_url( 'ajanlatkeres' ) ); ?>" class="btn btn--outline"><?php esc_html_e( 'Új ajánlatkérés', 'bma-ajanlatkeres' ); ?></a>
				</div>
			</div>
		</div>
	</div>
	<?php
	return;
endif;

include BMA_AJ_PLUGIN_DIR . 'templates/frontend/quote-wizard.php';
