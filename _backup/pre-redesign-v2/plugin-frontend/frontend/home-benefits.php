<?php
/**
 * Homepage – benefits (§7.4).
 *
 * @package BMA_Ajanlatkeres
 */

defined( 'ABSPATH' ) || exit;

$benefits = array(
	array(
		'title' => __( 'Átlátható folyamat', 'bma-ajanlatkeres' ),
		'text'  => __( 'Az ajánlatkéréstől a dokumentumok átadásáig követhető, hogy hol tart a folyamat, és milyen következő lépésekre van szükség.', 'bma-ajanlatkeres' ),
	),
	array(
		'title' => __( 'Kapcsolódó szolgáltatások egy helyen', 'bma-ajanlatkeres' ),
		'text'  => __( 'Egy ajánlatkérésben több tervezési, engedélyeztetési, közművesítési és dokumentációs feladat is összeállítható.', 'bma-ajanlatkeres' ),
	),
	array(
		'title' => __( 'Dokumentumok rendezett kezelése', 'bma-ajanlatkeres' ),
		'text'  => __( 'A szükséges tervrajzok és műszaki dokumentumok az adott ajánlatkéréshez kapcsolódva tölthetők fel és kezelhetők.', 'bma-ajanlatkeres' ),
	),
	array(
		'title' => __( 'Kevesebb felesleges egyeztetés', 'bma-ajanlatkeres' ),
		'text'  => __( 'Már az ajánlatkérés során megadhatók a projekt legfontosabb adatai és feltölthetők a rendelkezésre álló dokumentumok, így az ajánlatkészítés célzottabban indulhat el.', 'bma-ajanlatkeres' ),
	),
);
?>
<section class="bma-section bma-home-benefits" aria-labelledby="bma-benefits-title">
	<div class="bma-container">
		<p class="bma-section-eyebrow"><?php esc_html_e( 'Előnyök', 'bma-ajanlatkeres' ); ?></p>
		<h2 id="bma-benefits-title"><?php esc_html_e( 'Átláthatóbb műszaki ügyintézés', 'bma-ajanlatkeres' ); ?></h2>
		<div class="bma-benefits-grid">
			<?php foreach ( $benefits as $benefit ) : ?>
				<article class="bma-benefit-item">
					<h3><?php echo esc_html( $benefit['title'] ); ?></h3>
					<p><?php echo esc_html( $benefit['text'] ); ?></p>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>
