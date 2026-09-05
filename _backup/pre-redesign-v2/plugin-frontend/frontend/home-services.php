<?php
defined( 'ABSPATH' ) || exit;
$services = BMA_AJ_Service_CPT::get_active_services();
?>
<section class="bma-section bma-home-services" aria-labelledby="bma-services-title">
	<div class="bma-container">
		<p class="bma-section-eyebrow"><?php esc_html_e( 'Szolgáltatások', 'bma-ajanlatkeres' ); ?></p>
		<h2 id="bma-services-title"><?php esc_html_e( 'Miben tudunk segíteni?', 'bma-ajanlatkeres' ); ?></h2>
		<p class="bma-section-intro">
			<?php esc_html_e( 'A projekt igényeihez igazodva egy vagy több szolgáltatást is kiválaszthat. Az ajánlatkérés során pontosan megadhatja, mely részfeladatokra van szüksége.', 'bma-ajanlatkeres' ); ?>
		</p>
		<div class="bma-service-grid">
			<?php foreach ( array_slice( $services, 0, 4 ) as $service ) : ?>
				<article class="bma-service-tile">
					<a class="bma-service-tile-link" href="<?php echo esc_url( get_permalink( $service ) ); ?>">
						<div class="bma-service-tile-media">
							<?php BMA_AJ_Frontend::render_featured_or_placeholder( (int) $service->ID, '[SZOLGÁLTATÁS KÉP PLACEHOLDER]' ); ?>
						</div>
						<div class="bma-service-tile-body">
							<p class="bma-tile-eyebrow"><?php esc_html_e( 'Szolgáltatás', 'bma-ajanlatkeres' ); ?></p>
							<h3><?php echo esc_html( $service->post_title ); ?></h3>
							<p><?php echo esc_html( get_post_meta( $service->ID, '_bma_short_description', true ) ?: $service->post_excerpt ); ?></p>
						</div>
					</a>
				</article>
			<?php endforeach; ?>
		</div>
		<p class="bma-section-cta">
			<a href="<?php echo esc_url( home_url( '/szolgaltatasok/' ) ); ?>" class="bma-btn bma-btn-accent"><?php esc_html_e( 'Összes szolgáltatás', 'bma-ajanlatkeres' ); ?></a>
		</p>
	</div>
</section>
