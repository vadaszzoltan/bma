<?php
defined( 'ABSPATH' ) || exit;

$request_id = absint( $_GET['request'] ?? 0 );
$quote_id   = absint( $_GET['quote'] ?? 0 );
$method     = sanitize_text_field( wp_unslash( $_GET['method'] ?? '' ) );
$paid       = isset( $_GET['paid'] );

if ( ! $request_id || ! $quote_id || ! BMA_AJ_Roles::can_access_request( $request_id ) ) {
	?>
	<section class="bma-section">
		<div class="bma-container bma-container-narrow">
			<p class="bma-section-eyebrow"><?php esc_html_e( 'Fizetés', 'bma-ajanlatkeres' ); ?></p>
			<h1><?php esc_html_e( 'Bejelentkezés szükséges', 'bma-ajanlatkeres' ); ?></h1>
			<p class="bma-section-intro"><?php esc_html_e( 'A fizetéshez be kell jelentkeznie, és érvényes ajánlathoz kell rendelkeznie.', 'bma-ajanlatkeres' ); ?></p>
			<a href="<?php echo esc_url( wp_login_url( home_url( '/fizetes/' ) ) ); ?>" class="bma-btn bma-btn-accent bma-btn-lg"><?php esc_html_e( 'Bejelentkezés', 'bma-ajanlatkeres' ); ?></a>
		</div>
	</section>
	<?php
	return;
}

$quote   = BMA_AJ_Payments::get_quote( $quote_id, $request_id );
$request = BMA_AJ_Quote_Requests::get( $request_id );

if ( ! $quote || ! $request ) {
	echo '<section class="bma-section"><div class="bma-container"><p>' . esc_html__( 'Ajánlat nem található.', 'bma-ajanlatkeres' ) . '</p></div></section>';
	return;
}

if ( 'bank' === $method && 'paid' !== ( $quote->payment_status ?? '' ) ) {
	BMA_AJ_Quotes::mark_bank_pending( $quote_id );
	$quote = BMA_AJ_Payments::get_quote( $quote_id, $request_id );
}

$pdf_url  = BMA_AJ_Payments::get_quote_pdf_url( $quote );
$currency = strtoupper( $quote->currency ?: 'EUR' );
?>
<section class="bma-section bma-payment-page" aria-labelledby="bma-payment-title">
	<div class="bma-container bma-container-narrow">
		<p class="bma-section-eyebrow"><?php esc_html_e( 'Fizetés', 'bma-ajanlatkeres' ); ?></p>
		<h1 id="bma-payment-title"><?php esc_html_e( 'Ajánlat fizetése', 'bma-ajanlatkeres' ); ?></h1>
		<p class="bma-back-link"><a href="<?php echo esc_url( add_query_arg( 'request', $request_id, class_exists( 'BMA_AJ_I18n' ) ? BMA_AJ_I18n::page_url( 'ugyfelportal' ) : home_url( '/ugyfelportal/' ) ) ); ?>">&larr; <?php esc_html_e( 'Vissza az ajánlatkéréshez', 'bma-ajanlatkeres' ); ?></a></p>

		<?php if ( $paid || 'paid' === ( $quote->payment_status ?? '' ) ) : ?>
			<div class="bma-success-card" role="status">
				<p class="bma-section-eyebrow"><?php esc_html_e( 'Sikeres', 'bma-ajanlatkeres' ); ?></p>
				<h2><?php esc_html_e( 'Sikeres fizetés', 'bma-ajanlatkeres' ); ?></h2>
				<p><?php esc_html_e( 'Köszönjük! A fizetés feldolgozása megtörtént.', 'bma-ajanlatkeres' ); ?></p>
				<a href="<?php echo esc_url( add_query_arg( 'request', $request_id, class_exists( 'BMA_AJ_I18n' ) ? BMA_AJ_I18n::page_url( 'ugyfelportal' ) : home_url( '/ugyfelportal/' ) ) ); ?>" class="bma-btn bma-btn-accent bma-btn-lg"><?php esc_html_e( 'Vissza az ügyfélportálra', 'bma-ajanlatkeres' ); ?></a>
			</div>
		<?php elseif ( 'bank' === $method || 'bank_pending' === ( $quote->payment_status ?? '' ) ) : ?>
			<div class="bma-payment-card">
				<p class="bma-tile-eyebrow"><?php esc_html_e( 'Banki utalás', 'bma-ajanlatkeres' ); ?></p>
				<h2><?php esc_html_e( 'Utalási adatok', 'bma-ajanlatkeres' ); ?></h2>
				<p><?php esc_html_e( 'Kérjük, utalja az alábbi összeget a megadott bankszámlára. Az utalás közleményében tüntesse fel az ajánlat számát.', 'bma-ajanlatkeres' ); ?></p>
				<dl class="bma-bank-details">
					<dt><?php esc_html_e( 'Kedvezményezett', 'bma-ajanlatkeres' ); ?></dt>
					<dd><?php echo esc_html( get_option( 'bma_bank_beneficiary' ) ?: BMA_AJ_Company::get( 'company_name' ) ); ?></dd>
					<dt><?php esc_html_e( 'IBAN', 'bma-ajanlatkeres' ); ?></dt>
					<dd><?php echo esc_html( get_option( 'bma_bank_iban' ) ?: __( 'Az IBAN az admin beállításokban adható meg.', 'bma-ajanlatkeres' ) ); ?></dd>
					<dt><?php esc_html_e( 'BIC / SWIFT', 'bma-ajanlatkeres' ); ?></dt>
					<dd><?php echo esc_html( get_option( 'bma_bank_bic' ) ?: '—' ); ?></dd>
					<dt><?php esc_html_e( 'Bank neve', 'bma-ajanlatkeres' ); ?></dt>
					<dd><?php echo esc_html( get_option( 'bma_bank_name' ) ?: '—' ); ?></dd>
					<dt><?php esc_html_e( 'Összeg', 'bma-ajanlatkeres' ); ?></dt>
					<dd><strong><?php echo esc_html( BMA_AJ_Payments::format_money( (float) $quote->gross_amount, $currency ) ); ?></strong></dd>
					<dt><?php esc_html_e( 'Közlemény', 'bma-ajanlatkeres' ); ?></dt>
					<dd><?php echo esc_html( $quote->quote_number ); ?></dd>
				</dl>
			</div>
		<?php else : ?>
			<div class="bma-payment-card">
				<p class="bma-tile-eyebrow"><?php esc_html_e( 'Összegzés', 'bma-ajanlatkeres' ); ?></p>
				<h2><?php esc_html_e( 'Ajánlat részletei', 'bma-ajanlatkeres' ); ?></h2>
				<dl class="bma-quote-summary">
					<dt><?php esc_html_e( 'Ajánlat száma', 'bma-ajanlatkeres' ); ?></dt>
					<dd><?php echo esc_html( $quote->quote_number ); ?></dd>
					<dt><?php esc_html_e( 'Nettó összeg', 'bma-ajanlatkeres' ); ?></dt>
					<dd><?php echo esc_html( BMA_AJ_Payments::format_money( (float) $quote->net_amount, $currency ) ); ?></dd>
					<dt><?php esc_html_e( 'ÁFA', 'bma-ajanlatkeres' ); ?></dt>
					<dd><?php echo esc_html( BMA_AJ_Payments::format_money( (float) $quote->tax_amount, $currency ) ); ?></dd>
					<dt><?php esc_html_e( 'Bruttó összeg', 'bma-ajanlatkeres' ); ?></dt>
					<dd><strong class="bma-price-total"><?php echo esc_html( BMA_AJ_Payments::format_money( (float) $quote->gross_amount, $currency ) ); ?></strong></dd>
					<?php if ( $quote->valid_until ) : ?>
						<dt><?php esc_html_e( 'Érvényes', 'bma-ajanlatkeres' ); ?></dt>
						<dd><?php echo esc_html( $quote->valid_until ); ?></dd>
					<?php endif; ?>
					<?php if ( $quote->note ) : ?>
						<dt><?php esc_html_e( 'Megjegyzés', 'bma-ajanlatkeres' ); ?></dt>
						<dd><?php echo esc_html( $quote->note ); ?></dd>
					<?php endif; ?>
				</dl>

				<?php if ( $pdf_url ) : ?>
					<p><a href="<?php echo esc_url( $pdf_url ); ?>" class="bma-btn" download><?php esc_html_e( 'Ajánlati PDF letöltése', 'bma-ajanlatkeres' ); ?></a></p>
				<?php endif; ?>

				<div class="bma-payment-methods">
					<h3><?php esc_html_e( 'Válasszon fizetési módot', 'bma-ajanlatkeres' ); ?></h3>
					<div class="bma-payment-buttons">
						<?php if ( BMA_AJ_Stripe::is_configured() ) : ?>
						<button type="button" class="bma-btn bma-btn-accent bma-btn-lg bma-stripe-pay" data-request="<?php echo esc_attr( $request_id ); ?>" data-quote="<?php echo esc_attr( $quote_id ); ?>">
							<?php esc_html_e( 'Kártyás fizetés (Stripe)', 'bma-ajanlatkeres' ); ?>
						</button>
						<?php else : ?>
						<p class="bma-notice"><?php esc_html_e( 'A kártyás fizetés jelenleg nem elérhető. Kérjük, válassza a banki utalást.', 'bma-ajanlatkeres' ); ?></p>
						<?php endif; ?>
						<a
							href="<?php echo esc_url( add_query_arg( array( 'request' => $request_id, 'quote' => $quote_id, 'method' => 'bank' ), home_url( '/fizetes/' ) ) ); ?>"
							class="bma-btn bma-btn-lg"
							onclick="return confirm('<?php echo esc_js( __( 'Ha a banki utalást választja, a kártyás fizetés a továbbiakban nem lesz elérhető ennél az ajánlatnál. Folytatja?', 'bma-ajanlatkeres' ) ); ?>');"
						>
							<?php esc_html_e( 'Banki utalás', 'bma-ajanlatkeres' ); ?>
						</a>
					</div>
				</div>
			</div>
		<?php endif; ?>
	</div>
</section>
