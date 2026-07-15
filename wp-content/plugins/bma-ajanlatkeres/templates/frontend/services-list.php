<?php defined( 'ABSPATH' ) || exit; $services = BMA_AJ_Service_CPT::get_active_services(); ?>
<section class="bma-section" aria-labelledby="bma-all-services-title">
	<div class="bma-container">
		<h1 id="bma-all-services-title"><?php esc_html_e( 'Szolgáltatások', 'bma-ajanlatkeres' ); ?></h1>
		<p><?php esc_html_e( 'Válasszon a mérnöki tervezési és dokumentációs szolgáltatásaink közül.', 'bma-ajanlatkeres' ); ?></p>
		<div class="bma-grid bma-grid-3">
			<?php foreach ( $services as $service ) : ?>
				<article class="bma-card bma-service-card">
					<div class="bma-placeholder-img">[SZOLGÁLTATÁS KÉP PLACEHOLDER]</div>
					<h2><?php echo esc_html( $service->post_title ); ?></h2>
					<p><?php echo esc_html( get_post_meta( $service->ID, '_bma_short_description', true ) ?: $service->post_excerpt ); ?></p>
					<div class="bma-card-actions">
						<a href="<?php echo esc_url( get_permalink( $service ) ); ?>" class="bma-btn"><?php esc_html_e( 'Részletek', 'bma-ajanlatkeres' ); ?></a>
						<button type="button" class="bma-btn bma-btn-primary bma-start-quote" data-service-id="<?php echo esc_attr( $service->ID ); ?>"><?php esc_html_e( 'Ajánlatkérés', 'bma-ajanlatkeres' ); ?></button>
					</div>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>
