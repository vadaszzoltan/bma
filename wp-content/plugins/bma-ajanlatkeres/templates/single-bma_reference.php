<?php
/**
 * Single reference template (§31 structure).
 *
 * @package BMA_Ajanlatkeres
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	$post_id  = get_the_ID();
	$meta     = BMA_AJ_Reference_CPT::get_all_meta( $post_id );
	$related  = BMA_AJ_Reference_CPT::get_related_services( $post_id );
	$work     = BMA_AJ_Reference_CPT::lines( $meta['work_done'] );
	$gallery  = $meta['gallery'];
	$location = preg_match( '/^\[/', $meta['location'] ) ? '' : $meta['location'];
	$year     = preg_match( '/^\[/', $meta['year'] ) ? '' : $meta['year'];
	$cta_title = $meta['cta_title'] ?: __( 'Hasonló projektet tervez?', 'bma-ajanlatkeres' );
	$cta_text  = $meta['cta_text'] ?: __( 'Válassza ki a szükséges szolgáltatásokat, és küldje el a projekt alapadatait.', 'bma-ajanlatkeres' );
	?>
	<article <?php post_class( 'bma-single-reference bma-section' ); ?>>
		<div class="bma-container">
			<header class="bma-reference-header">
				<?php if ( $meta['category'] ) : ?>
					<p class="bma-section-eyebrow"><?php echo esc_html( $meta['category'] ); ?></p>
				<?php endif; ?>
				<h1><?php the_title(); ?></h1>
				<?php if ( has_excerpt() ) : ?>
					<p class="bma-lead"><?php echo esc_html( get_the_excerpt() ); ?></p>
				<?php endif; ?>
				<div class="bma-reference-hero">
					<?php BMA_AJ_Frontend::render_featured_or_placeholder( $post_id, '[REFERENCIA GALÉRIA PLACEHOLDER]', 'large', 'bma-placeholder-lg' ); ?>
				</div>
			</header>

			<?php if ( $meta['project_type'] || $meta['category'] || $location || $year || $related ) : ?>
				<section class="bma-ref-facts" aria-labelledby="bma-ref-facts-title">
					<h2 id="bma-ref-facts-title"><?php esc_html_e( 'Projektadatok', 'bma-ajanlatkeres' ); ?></h2>
					<ul class="bma-meta-list bma-meta-list--stacked">
						<?php if ( $meta['project_type'] ) : ?>
							<li><strong><?php esc_html_e( 'Projekt típusa', 'bma-ajanlatkeres' ); ?></strong> <?php echo esc_html( $meta['project_type'] ); ?></li>
						<?php endif; ?>
						<?php if ( $meta['category'] ) : ?>
							<li><strong><?php esc_html_e( 'Kategória', 'bma-ajanlatkeres' ); ?></strong> <?php echo esc_html( $meta['category'] ); ?></li>
						<?php endif; ?>
						<?php if ( $location ) : ?>
							<li><strong><?php esc_html_e( 'Helyszín', 'bma-ajanlatkeres' ); ?></strong> <?php echo esc_html( $location ); ?></li>
						<?php endif; ?>
						<?php if ( $year ) : ?>
							<li><strong><?php esc_html_e( 'Év', 'bma-ajanlatkeres' ); ?></strong> <?php echo esc_html( $year ); ?></li>
						<?php endif; ?>
						<?php if ( $related ) : ?>
							<li>
								<strong><?php esc_html_e( 'Kapcsolódó szolgáltatások', 'bma-ajanlatkeres' ); ?></strong>
								<?php echo esc_html( implode( ', ', wp_list_pluck( $related, 'post_title' ) ) ); ?>
							</li>
						<?php endif; ?>
					</ul>
				</section>
			<?php endif; ?>

			<?php if ( $meta['project_description'] ) : ?>
				<section class="bma-ref-block" aria-labelledby="bma-ref-project-title">
					<h2 id="bma-ref-project-title"><?php esc_html_e( 'A projekt', 'bma-ajanlatkeres' ); ?></h2>
					<?php echo wp_kses_post( wpautop( esc_html( $meta['project_description'] ) ) ); ?>
				</section>
			<?php endif; ?>

			<?php if ( $meta['task'] ) : ?>
				<section class="bma-ref-block" aria-labelledby="bma-ref-task-title">
					<h2 id="bma-ref-task-title"><?php esc_html_e( 'Mi volt a feladat?', 'bma-ajanlatkeres' ); ?></h2>
					<?php echo wp_kses_post( wpautop( esc_html( $meta['task'] ) ) ); ?>
				</section>
			<?php endif; ?>

			<?php if ( $work ) : ?>
				<section class="bma-ref-block" aria-labelledby="bma-ref-work-title">
					<h2 id="bma-ref-work-title"><?php esc_html_e( 'Mit végeztünk el?', 'bma-ajanlatkeres' ); ?></h2>
					<ul class="bma-ref-work-list">
						<?php foreach ( $work as $line ) : ?>
							<li><?php echo esc_html( $line ); ?></li>
						<?php endforeach; ?>
					</ul>
				</section>
			<?php endif; ?>

			<?php if ( $meta['solution'] ) : ?>
				<section class="bma-ref-block" aria-labelledby="bma-ref-solution-title">
					<h2 id="bma-ref-solution-title"><?php esc_html_e( 'A megoldás', 'bma-ajanlatkeres' ); ?></h2>
					<?php echo wp_kses_post( wpautop( esc_html( $meta['solution'] ) ) ); ?>
				</section>
			<?php endif; ?>

			<?php if ( $meta['result'] ) : ?>
				<section class="bma-ref-block" aria-labelledby="bma-ref-result-title">
					<h2 id="bma-ref-result-title"><?php esc_html_e( 'Eredmény', 'bma-ajanlatkeres' ); ?></h2>
					<?php echo wp_kses_post( wpautop( esc_html( $meta['result'] ) ) ); ?>
				</section>
			<?php endif; ?>

			<?php if ( $gallery ) : ?>
				<section class="bma-ref-gallery" aria-labelledby="bma-ref-gallery-title">
					<h2 id="bma-ref-gallery-title"><?php esc_html_e( 'Projektgaléria', 'bma-ajanlatkeres' ); ?></h2>
					<div class="bma-ref-gallery-grid">
						<?php foreach ( $gallery as $gid ) : ?>
							<button type="button" class="bma-ref-gallery-item" data-bma-lightbox="<?php echo esc_url( wp_get_attachment_image_url( $gid, 'large' ) ); ?>" aria-label="<?php esc_attr_e( 'Kép nagyítása', 'bma-ajanlatkeres' ); ?>">
								<?php echo wp_get_attachment_image( $gid, 'medium_large' ); ?>
							</button>
						<?php endforeach; ?>
					</div>
				</section>
			<?php endif; ?>

			<?php if ( $related ) : ?>
				<section class="bma-ref-related" aria-labelledby="bma-ref-related-title">
					<h2 id="bma-ref-related-title"><?php esc_html_e( 'Kapcsolódó szolgáltatások', 'bma-ajanlatkeres' ); ?></h2>
					<div class="bma-related-grid">
						<?php foreach ( $related as $svc ) : ?>
							<a class="bma-related-nav-card" href="<?php echo esc_url( get_permalink( $svc ) ); ?>">
								<strong class="bma-related-title"><?php echo esc_html( $svc->post_title ); ?></strong>
								<span class="bma-related-desc"><?php echo esc_html( get_post_meta( $svc->ID, '_bma_short_description', true ) ?: $svc->post_excerpt ); ?></span>
								<span class="bma-related-cta"><?php esc_html_e( 'Szolgáltatás megtekintése', 'bma-ajanlatkeres' ); ?></span>
							</a>
						<?php endforeach; ?>
					</div>
				</section>
			<?php endif; ?>

			<section class="bma-ref-cta" aria-labelledby="bma-ref-cta-title">
				<h2 id="bma-ref-cta-title"><?php echo esc_html( $cta_title ); ?></h2>
				<p><?php echo esc_html( $cta_text ); ?></p>
				<p>
					<a class="bma-btn bma-btn-accent bma-btn-lg" href="<?php echo esc_url( ( class_exists( 'BMA_AJ_I18n' ) ? BMA_AJ_I18n::page_url( 'szolgaltatasok' ) : home_url( '/szolgaltatasok/' ) ) ); ?>">
						<?php esc_html_e( 'Ajánlatkérés indítása', 'bma-ajanlatkeres' ); ?>
					</a>
				</p>
			</section>
		</div>
	</article>
	<dialog class="bma-lightbox" data-bma-lightbox-dialog>
		<button type="button" class="bma-lightbox-close" data-bma-lightbox-close aria-label="<?php esc_attr_e( 'Bezárás', 'bma-ajanlatkeres' ); ?>">×</button>
		<img src="" alt="" data-bma-lightbox-img>
	</dialog>
	<?php
endwhile;

get_footer();
