<?php
/**
 * Homepage references (redesign v2).
 *
 * @package BMA_Ajanlatkeres
 */

defined( 'ABSPATH' ) || exit;

$refs = get_posts(
	array(
		'post_type'      => BMA_AJ_Reference_CPT::POST_TYPE,
		'posts_per_page' => 3,
		'post_status'    => 'publish',
	)
);
$ref_url = function_exists( 'bma_page_url' ) ? bma_page_url( 'referenciak' ) : home_url( '/' );
?>
<section class="bma-section bma-home-references" aria-labelledby="bma-refs-title">
	<div class="bma-container">
		<div class="sec-head">
			<div>
				<p class="eyebrow"><?php esc_html_e( 'Projektek', 'bma-ajanlatkeres' ); ?></p>
				<h2 class="h2" id="bma-refs-title"><?php esc_html_e( 'Kiemelt referenciák', 'bma-ajanlatkeres' ); ?></h2>
				<p class="lead" style="margin-top:14px">
					<?php esc_html_e( 'Válogatás megvalósult tervezési, engedélyeztetési és közmű-koordinációs munkáinkból.', 'bma-ajanlatkeres' ); ?>
				</p>
			</div>
			<a href="<?php echo esc_url( $ref_url ); ?>" class="btn btn--outline btn--sm"><?php esc_html_e( 'Összes referencia', 'bma-ajanlatkeres' ); ?></a>
		</div>
		<div class="refs">
			<?php foreach ( $refs as $ref ) : ?>
				<?php
				$meta     = BMA_AJ_Reference_CPT::get_all_meta( (int) $ref->ID );
				$related  = BMA_AJ_Reference_CPT::get_related_services( (int) $ref->ID );
				$bits     = array_filter( array( $meta['project_type'] ?? '', $meta['category'] ?? '' ) );
				$permalink = function_exists( 'bma_post_url' ) ? bma_post_url( (int) $ref->ID ) : get_permalink( $ref );
				?>
				<article class="ref">
					<a href="<?php echo esc_url( $permalink ); ?>" class="ref-img" aria-label="<?php echo esc_attr( $ref->post_title ); ?>">
						<?php
						if ( has_post_thumbnail( $ref ) ) {
							echo get_the_post_thumbnail( $ref, 'medium_large', array( 'loading' => 'lazy' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						} else {
							echo '<span>' . esc_html__( '[Projekt fotó]', 'bma-ajanlatkeres' ) . '</span>';
						}
						?>
					</a>
					<div class="ref-body">
						<p class="eyebrow" style="margin:0">
							<?php echo esc_html( $bits ? implode( ' · ', $bits ) : __( 'Referencia', 'bma-ajanlatkeres' ) ); ?>
						</p>
						<h3><?php echo esc_html( $ref->post_title ); ?></h3>
						<?php if ( $related ) : ?>
							<div class="ref-tags">
								<?php foreach ( $related as $svc ) : ?>
									<span class="ref-tag"><?php echo esc_html( $svc->post_title ); ?></span>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>
						<?php if ( $ref->post_excerpt ) : ?>
							<p class="small muted" style="margin:0"><?php echo esc_html( $ref->post_excerpt ); ?></p>
						<?php endif; ?>
						<div class="ref-link">
							<a href="<?php echo esc_url( $permalink ); ?>"><?php esc_html_e( 'Projekt megtekintése →', 'bma-ajanlatkeres' ); ?></a>
						</div>
					</div>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>
