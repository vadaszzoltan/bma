<?php
defined( 'ABSPATH' ) || exit;
$refs = get_posts(
	array(
		'post_type'      => BMA_AJ_Reference_CPT::POST_TYPE,
		'posts_per_page' => 4,
		'post_status'    => 'publish',
	)
);
?>
<section class="bma-section bma-home-references" aria-labelledby="bma-refs-title">
	<div class="bma-container">
		<p class="bma-section-eyebrow"><?php esc_html_e( 'Projektek', 'bma-ajanlatkeres' ); ?></p>
		<h2 id="bma-refs-title"><?php esc_html_e( 'Kiemelt referenciák', 'bma-ajanlatkeres' ); ?></h2>
		<p class="bma-section-intro">
			<?php esc_html_e( 'Válogatás megvalósult tervezési, engedélyeztetési és közmű-koordinációs munkáinkból.', 'bma-ajanlatkeres' ); ?>
		</p>
		<div class="bma-ref-grid">
			<?php foreach ( $refs as $ref ) : ?>
				<?php
				$meta    = BMA_AJ_Reference_CPT::get_all_meta( (int) $ref->ID );
				$related = BMA_AJ_Reference_CPT::get_related_services( (int) $ref->ID );
				?>
				<article class="bma-ref-tile">
					<a class="bma-ref-tile-link" href="<?php echo esc_url( get_permalink( $ref ) ); ?>">
						<div class="bma-ref-tile-media">
							<?php BMA_AJ_Frontend::render_featured_or_placeholder( (int) $ref->ID, '[REFERENCIA GALÉRIA PLACEHOLDER]' ); ?>
						</div>
						<div class="bma-ref-tile-body">
							<p class="bma-tile-eyebrow">
								<?php
								$bits = array_filter( array( $meta['project_type'], $meta['category'] ) );
								echo esc_html( $bits ? implode( ' · ', $bits ) : __( 'Referencia', 'bma-ajanlatkeres' ) );
								?>
							</p>
							<h3><?php echo esc_html( $ref->post_title ); ?></h3>
							<?php if ( $related ) : ?>
								<p class="bma-ref-tile-services"><?php echo esc_html( implode( ', ', wp_list_pluck( $related, 'post_title' ) ) ); ?></p>
							<?php endif; ?>
							<?php if ( $ref->post_excerpt ) : ?>
								<p><?php echo esc_html( $ref->post_excerpt ); ?></p>
							<?php endif; ?>
							<span class="bma-ref-tile-cta"><?php esc_html_e( 'Projekt megtekintése', 'bma-ajanlatkeres' ); ?></span>
						</div>
					</a>
				</article>
			<?php endforeach; ?>
		</div>
		<p class="bma-section-cta">
			<a href="<?php echo esc_url( home_url( '/referenciak/' ) ); ?>" class="bma-btn bma-btn-outline">
				<?php esc_html_e( 'Összes referencia', 'bma-ajanlatkeres' ); ?>
			</a>
		</p>
	</div>
</section>
