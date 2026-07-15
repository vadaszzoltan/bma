<?php defined( 'ABSPATH' ) || exit;
$refs = get_posts( array( 'post_type' => BMA_AJ_Reference_CPT::POST_TYPE, 'posts_per_page' => -1, 'post_status' => 'publish' ) );
?>
<section class="bma-section" aria-labelledby="bma-refs-list-title">
	<div class="bma-container">
		<h1 id="bma-refs-list-title"><?php esc_html_e( 'Referenciák', 'bma-ajanlatkeres' ); ?></h1>
		<div class="bma-grid bma-grid-2">
			<?php foreach ( $refs as $ref ) : ?>
				<article class="bma-card">
					<div class="bma-placeholder-img">[REFERENCIA GALÉRIA PLACEHOLDER]</div>
					<h2><a href="<?php echo esc_url( get_permalink( $ref ) ); ?>"><?php echo esc_html( $ref->post_title ); ?></a></h2>
					<p><?php echo esc_html( $ref->post_excerpt ); ?></p>
					<?php $type = get_post_meta( $ref->ID, '_bma_project_type', true ); if ( $type ) : ?>
						<p class="bma-meta"><?php echo esc_html( $type ); ?></p>
					<?php endif; ?>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>
