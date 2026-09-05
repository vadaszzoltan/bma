<?php
/**
 * Homepage services grid (redesign v2).
 *
 * @package BMA_Ajanlatkeres
 */

defined( 'ABSPATH' ) || exit;

$services = BMA_AJ_Service_CPT::get_active_services();
$svc_url  = ( class_exists( 'BMA_AJ_I18n' ) ? BMA_AJ_I18n::page_url( 'szolgaltatasok' ) : home_url( '/szolgaltatasok/' ) );
?>
<section class="bma-section bma-home-services" aria-labelledby="bma-services-title">
	<div class="bma-container">
		<div class="sec-head">
			<div>
				<p class="eyebrow"><?php esc_html_e( 'Szolgáltatások', 'bma-ajanlatkeres' ); ?></p>
				<h2 class="h2" id="bma-services-title"><?php esc_html_e( 'Miben tudunk segíteni?', 'bma-ajanlatkeres' ); ?></h2>
				<p class="lead" style="margin-top:14px">
					<?php esc_html_e( 'Négy szolgáltatási terület, azon belül konkrét részfeladatokkal. Az ajánlatkérésbe bármennyi tétel bekerülhet.', 'bma-ajanlatkeres' ); ?>
				</p>
			</div>
			<a href="<?php echo esc_url( $svc_url ); ?>" class="btn btn--outline btn--sm"><?php esc_html_e( 'Összes szolgáltatás', 'bma-ajanlatkeres' ); ?></a>
		</div>
		<div class="svc-grid">
			<?php foreach ( array_slice( $services, 0, 4 ) as $service ) : ?>
				<?php
				$items = class_exists( 'BMA_AJ_Service_Items' )
					? BMA_AJ_Service_Items::get_for_service( (int) $service->ID )
					: array();
				$desc  = get_post_meta( $service->ID, '_bma_short_description', true ) ?: $service->post_excerpt;
				?>
				<article class="svc-card">
					<h3><?php echo esc_html( $service->post_title ); ?></h3>
					<p><?php echo esc_html( $desc ); ?></p>
					<?php if ( $items ) : ?>
						<ul class="svc-tasks">
							<?php foreach ( array_slice( $items, 0, 4 ) as $item ) : ?>
								<li><?php echo esc_html( $item->name ); ?></li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
					<div class="svc-foot">
						<a href="<?php echo esc_url( get_permalink( $service ) ); ?>">
							<?php esc_html_e( 'Részletek és kiválasztás →', 'bma-ajanlatkeres' ); ?>
						</a>
					</div>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>
