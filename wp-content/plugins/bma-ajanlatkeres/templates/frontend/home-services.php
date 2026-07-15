<?php defined( 'ABSPATH' ) || exit; $services = BMA_AJ_Service_CPT::get_active_services(); ?>
<section class="bma-section bma-home-services" aria-labelledby="bma-services-title">
	<div class="bma-container">
		<h2 id="bma-services-title"><?php esc_html_e( 'Fő szolgáltatások', 'bma-ajanlatkeres' ); ?></h2>
		<p class="bma-section-intro"><?php esc_html_e( 'Válassza ki a projektjéhez szükséges tervezési és dokumentációs szolgáltatásokat.', 'bma-ajanlatkeres' ); ?></p>
		<div class="bma-grid bma-grid-3">
			<?php foreach ( array_slice( $services, 0, 3 ) as $service ) : ?>
				<article class="bma-card">
					<div class="bma-placeholder-img">[SZOLGÁLTATÁS KÉP PLACEHOLDER]</div>
					<h3><?php echo esc_html( $service->post_title ); ?></h3>
					<p><?php echo esc_html( get_post_meta( $service->ID, '_bma_short_description', true ) ?: $service->post_excerpt ); ?></p>
					<a href="<?php echo esc_url( get_permalink( $service ) ); ?>" class="bma-link"><?php esc_html_e( 'Részletek', 'bma-ajanlatkeres' ); ?></a>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>
