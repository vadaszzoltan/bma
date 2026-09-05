<?php
/**
 * Homepage benefits (redesign v2).
 *
 * @package BMA_Ajanlatkeres
 */

defined( 'ABSPATH' ) || exit;

$benefits = array(
	array(
		'title' => __( 'Átlátható folyamat', 'bma-ajanlatkeres' ),
		'text'  => __( 'Az ajánlatkéréstől a dokumentumok átadásáig követhető, hol tart a folyamat, és milyen következő lépésekre van szükség.', 'bma-ajanlatkeres' ),
		'icon'  => '<svg class="icon icon--sm" viewBox="0 0 24 24" aria-hidden="true"><path d="M3 12h4l3 7 4-14 3 7h4"></path></svg>',
	),
	array(
		'title' => __( 'Kapcsolódó szolgáltatások egy helyen', 'bma-ajanlatkeres' ),
		'text'  => __( 'Egy ajánlatkérésben több tervezési, engedélyeztetési, közmű-koordinációs és dokumentációs feladat is összeállítható.', 'bma-ajanlatkeres' ),
		'icon'  => '<svg class="icon icon--sm" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 5h7l2 2h7v12H4z"></path></svg>',
	),
	array(
		'title' => __( 'Dokumentumok rendezett kezelése', 'bma-ajanlatkeres' ),
		'text'  => __( 'A szükséges tervrajzok és műszaki dokumentumok az adott ajánlatkéréshez kapcsolódva tölthetők fel és kezelhetők.', 'bma-ajanlatkeres' ),
		'icon'  => '<svg class="icon icon--sm" viewBox="0 0 24 24" aria-hidden="true"><path d="M6 3h9l4 4v14H6z"></path><path d="M15 3v4h4"></path><path d="M9 13h6M9 17h4"></path></svg>',
	),
	array(
		'title' => __( 'Kevesebb felesleges egyeztetés', 'bma-ajanlatkeres' ),
		'text'  => __( 'Már az ajánlatkérés során megadhatók a projekt legfontosabb adatai, így az ajánlatkészítés célzottabban indulhat el.', 'bma-ajanlatkeres' ),
		'icon'  => '<svg class="icon icon--sm" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="8.5"></circle><path d="M9 12.5l2 2 4-4.5"></path></svg>',
	),
);
?>
<section class="bma-section bma-section--bg bma-home-benefits" aria-labelledby="bma-benefits-title">
	<div class="bma-container">
		<p class="eyebrow"><?php esc_html_e( 'Előnyök', 'bma-ajanlatkeres' ); ?></p>
		<h2 class="h2" id="bma-benefits-title" style="margin-bottom:34px"><?php esc_html_e( 'Átláthatóbb műszaki ügyintézés', 'bma-ajanlatkeres' ); ?></h2>
		<div class="benefits">
			<?php foreach ( $benefits as $benefit ) : ?>
				<div class="benefit">
					<span class="benefit-ic">
						<?php echo $benefit['icon']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed inline SVG. ?>
					</span>
					<div>
						<h3><?php echo esc_html( $benefit['title'] ); ?></h3>
						<p><?php echo esc_html( $benefit['text'] ); ?></p>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
