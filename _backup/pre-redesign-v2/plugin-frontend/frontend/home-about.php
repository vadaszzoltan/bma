<?php
/**
 * Homepage – about BMA (§7.7).
 *
 * @package BMA_Ajanlatkeres
 */

defined( 'ABSPATH' ) || exit;

$company = BMA_AJ_Company::get( 'company_name' );
?>
<section class="bma-section bma-home-about" aria-labelledby="bma-about-title">
	<div class="bma-container bma-home-about-inner">
		<p class="bma-section-eyebrow"><?php esc_html_e( 'Rólunk', 'bma-ajanlatkeres' ); ?></p>
		<h2 id="bma-about-title"><?php echo esc_html( $company ?: 'BMA Engineering' ); ?></h2>
		<p>
			<?php esc_html_e( 'A BMA Engineering mérnöki tervezési, engedélyeztetési, közmű-koordinációs és műszaki dokumentációs feladatokban támogatja ügyfeleit.', 'bma-ajanlatkeres' ); ?>
		</p>
		<p>
			<?php esc_html_e( 'Munkánk során arra törekszünk, hogy a projekt műszaki követelményei, a szükséges dokumentációk és a kapcsolódó egyeztetések egy átlátható és követhető folyamat részeként legyenek kezelhetők.', 'bma-ajanlatkeres' ); ?>
		</p>
		<p>
			<?php esc_html_e( 'Lakossági és üzleti projektekhez egyaránt biztosítunk kapcsolódó mérnöki szolgáltatásokat, az egyedi projektigényekhez igazodva.', 'bma-ajanlatkeres' ); ?>
		</p>
	</div>
</section>
