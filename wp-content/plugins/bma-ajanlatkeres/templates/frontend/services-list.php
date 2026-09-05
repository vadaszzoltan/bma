<?php
/**
 * Services list with multi-service item selection.
 *
 * @package BMA_Ajanlatkeres
 */

defined( 'ABSPATH' ) || exit;

$services   = BMA_AJ_Service_CPT::get_active_services();
$cart_sel   = BMA_AJ_Cart::get_selection_map();
$partial    = BMA_AJ_PLUGIN_DIR . 'templates/frontend/partials/select-card.php';
?>
<section class="bma-section bma-page-services bma-selection-page" aria-labelledby="bma-all-services-title" data-bma-selection-root data-selection-mode="list">
	<div class="bma-container">
		<p class="bma-section-eyebrow"><?php esc_html_e( 'Ajánlatkérés', 'bma-ajanlatkeres' ); ?></p>
		<h1 id="bma-all-services-title"><?php esc_html_e( 'Szolgáltatások', 'bma-ajanlatkeres' ); ?></h1>
		<p class="bma-section-intro">
			<?php esc_html_e( 'Válassza ki az Ön projektjéhez kapcsolódó szolgáltatási területet. Egy ajánlatkérésben több szolgáltatást és azokon belül több részfeladatot is megjelölhet.', 'bma-ajanlatkeres' ); ?>
		</p>

		<?php foreach ( $services as $service ) : ?>
			<?php
			$sid   = (int) $service->ID;
			$items = BMA_AJ_Dynamic_Fields::get_service_items( $sid );
			$sel   = $cart_sel[ (string) $sid ] ?? array();
			?>
			<article class="bma-service-select-block" data-service-id="<?php echo esc_attr( (string) $sid ); ?>">
				<header class="bma-service-select-header">
					<div>
						<p class="bma-tile-eyebrow"><?php esc_html_e( 'Szolgáltatás', 'bma-ajanlatkeres' ); ?></p>
						<h2><?php echo esc_html( $service->post_title ); ?></h2>
						<p><?php echo esc_html( get_post_meta( $sid, '_bma_short_description', true ) ?: $service->post_excerpt ); ?></p>
					</div>
					<a class="bma-btn bma-btn-outline" href="<?php echo esc_url( get_permalink( $service ) ); ?>">
						<?php esc_html_e( 'Részletek', 'bma-ajanlatkeres' ); ?>
					</a>
				</header>

				<?php if ( $items ) : ?>
					<div class="bma-items-grid bma-items-grid--large" role="group" aria-label="<?php echo esc_attr( sprintf( /* translators: %s service */ __( '%s részfeladatai', 'bma-ajanlatkeres' ), $service->post_title ) ); ?>">
						<?php
						foreach ( $items as $item ) {
							$service_id   = $sid;
							$service_name = $service->post_title;
							$checked      = in_array( (int) $item->id, array_map( 'intval', $sel ), true );
							include $partial;
						}
						?>
					</div>
				<?php endif; ?>
			</article>
		<?php endforeach; ?>

		<section class="bma-help-block" aria-labelledby="bma-multi-help-title">
			<h2 id="bma-multi-help-title"><?php esc_html_e( 'Több szolgáltatásra van szüksége?', 'bma-ajanlatkeres' ); ?></h2>
			<p><?php esc_html_e( 'Egy projekt során gyakran több tervezési, engedélyeztetési vagy dokumentációs feladat kapcsolódik egymáshoz.', 'bma-ajanlatkeres' ); ?></p>
			<p><?php esc_html_e( 'Az ajánlatkérési folyamatban több szolgáltatást is kiválaszthat. A projekt alapadatait és dokumentumait egyszer kell megadnia, a rendszer pedig az összes kiválasztott szolgáltatáshoz kapcsolódó információkat egy ajánlatkérésben kezeli.', 'bma-ajanlatkeres' ); ?></p>
		</section>

		<section class="bma-help-block bma-help-block--cta" aria-labelledby="bma-unsure-title">
			<h2 id="bma-unsure-title"><?php esc_html_e( 'Nem biztos benne, melyik szolgáltatásra van szüksége?', 'bma-ajanlatkeres' ); ?></h2>
			<p><?php esc_html_e( 'Írja le röviden a projektet és a jelenlegi helyzetet. Segítünk meghatározni, milyen tervezési, engedélyeztetési vagy dokumentációs feladatokra lehet szükség.', 'bma-ajanlatkeres' ); ?></p>
			<p>
				<a class="bma-btn bma-btn-outline" href="<?php echo esc_url( ( class_exists( 'BMA_AJ_I18n' ) ? BMA_AJ_I18n::page_url( 'kapcsolat' ) : home_url( '/kapcsolat/' ) ) ); ?>">
					<?php esc_html_e( 'Kapcsolatfelvétel', 'bma-ajanlatkeres' ); ?>
				</a>
			</p>
		</section>
	</div>

	<?php
	$cta_label = __( 'Tovább az ajánlatkéréshez', 'bma-ajanlatkeres' );
	$mode      = 'list';
	include BMA_AJ_PLUGIN_DIR . 'templates/frontend/partials/selection-bar.php';
	?>
</section>
