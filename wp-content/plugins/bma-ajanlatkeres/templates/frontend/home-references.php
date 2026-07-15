<?php defined( 'ABSPATH' ) || exit;
$refs = get_posts( array( 'post_type' => BMA_AJ_Reference_CPT::POST_TYPE, 'posts_per_page' => 2, 'post_status' => 'publish' ) );
?>
<section class="bma-section bma-home-references" aria-labelledby="bma-refs-title">
	<div class="bma-container">
		<h2 id="bma-refs-title"><?php esc_html_e( 'Kiemelt referenciák', 'bma-ajanlatkeres' ); ?></h2>
		<div class="bma-grid bma-grid-2">
			<?php foreach ( $refs as $ref ) : ?>
				<article class="bma-card">
					<div class="bma-placeholder-img">[REFERENCIA GALÉRIA PLACEHOLDER]</div>
					<h3><a href="<?php echo esc_url( get_permalink( $ref ) ); ?>"><?php echo esc_html( $ref->post_title ); ?></a></h3>
					<p><?php echo esc_html( $ref->post_excerpt ); ?></p>
				</article>
			<?php endforeach; ?>
		</div>
		<p class="bma-text-center"><a href="<?php echo esc_url( home_url( '/referenciak/' ) ); ?>" class="bma-btn"><?php esc_html_e( 'Összes referencia', 'bma-ajanlatkeres' ); ?></a></p>
	</div>
</section>
