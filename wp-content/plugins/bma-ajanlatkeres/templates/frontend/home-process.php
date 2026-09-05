<?php
/**
 * Homepage process steps (redesign v2).
 *
 * @package BMA_Ajanlatkeres
 */

defined( 'ABSPATH' ) || exit;

$svc_url = ( class_exists( 'BMA_AJ_I18n' ) ? BMA_AJ_I18n::page_url( 'szolgaltatasok' ) : home_url( '/szolgaltatasok/' ) );
$steps   = array(
	array(
		'num'   => '01',
		'title' => __( 'Szolgáltatások kiválasztása', 'bma-ajanlatkeres' ),
		'text'  => __( 'Válassza ki a projektjéhez szükséges szolgáltatásokat és azokon belül a konkrét részfeladatokat.', 'bma-ajanlatkeres' ),
		'active'=> true,
	),
	array(
		'num'   => '02',
		'title' => __( 'Projekt adatai', 'bma-ajanlatkeres' ),
		'text'  => __( 'Töltse ki a kiválasztott tételekhez tartozó adatokat, és töltse fel a meglévő tervrajzokat.', 'bma-ajanlatkeres' ),
		'active'=> true,
	),
	array(
		'num'   => '03',
		'title' => __( 'Beküldés', 'bma-ajanlatkeres' ),
		'text'  => __( 'Ellenőrizze az összeállítást, adja meg a kapcsolattartási adatait, és küldje el a kérést.', 'bma-ajanlatkeres' ),
		'active'=> true,
	),
	array(
		'num'   => '04',
		'title' => __( 'Nyomon követés', 'bma-ajanlatkeres' ),
		'text'  => __( 'Az ügyfélportálon látja a státuszt, az elkészült ajánlatot és a feltöltött dokumentumokat.', 'bma-ajanlatkeres' ),
		'active'=> false,
	),
);
?>
<section class="bma-section bma-home-process" aria-labelledby="bma-process-title">
	<div class="bma-container">
		<p class="eyebrow"><?php esc_html_e( 'Folyamat', 'bma-ajanlatkeres' ); ?></p>
		<h2 class="h2" id="bma-process-title" style="margin-bottom:34px"><?php esc_html_e( 'Az ajánlatkérés menete', 'bma-ajanlatkeres' ); ?></h2>
		<div class="process">
			<?php foreach ( $steps as $step ) : ?>
				<div class="step<?php echo ! empty( $step['active'] ) ? ' step--active' : ''; ?>">
					<div class="step-num"><?php echo esc_html( $step['num'] ); ?></div>
					<h3><?php echo esc_html( $step['title'] ); ?></h3>
					<p><?php echo esc_html( $step['text'] ); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
		<div style="margin-top:34px">
			<a href="<?php echo esc_url( $svc_url ); ?>" class="btn btn--primary"><?php esc_html_e( 'Ajánlatkérés indítása', 'bma-ajanlatkeres' ); ?></a>
		</div>
	</div>
</section>
