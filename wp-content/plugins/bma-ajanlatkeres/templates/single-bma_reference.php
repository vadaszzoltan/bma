<?php
/**
 * Single reference – markup/06-referencia-projekt.html
 *
 * @package BMA_Ajanlatkeres
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	$post_id   = get_the_ID();
	$meta      = BMA_AJ_Reference_CPT::get_all_meta( $post_id );
	$related   = BMA_AJ_Reference_CPT::get_related_services( $post_id );
	$work_rows = BMA_AJ_Reference_CPT::labeled_lines( (string) $meta['work'] );
	$gallery   = $meta['gallery'];
	$more      = BMA_AJ_Reference_CPT::get_related_projects( $post_id, 3 );

	$location = preg_match( '/^\[/', $meta['location'] ) ? '' : $meta['location'];
	$year     = preg_match( '/^\[/', $meta['year'] ) ? '' : $meta['year'];
	$duration = trim( (string) $meta['duration'] );
	$task     = trim( (string) $meta['task'] );
	$result   = trim( (string) $meta['result'] );

	$home_url    = function_exists( 'bma_page_url' ) ? bma_page_url( 'fooldal' ) : home_url( '/' );
	$list_url    = function_exists( 'bma_page_url' ) ? bma_page_url( 'referenciak' ) : home_url( '/referenciak/' );
	$svc_url     = function_exists( 'bma_page_url' ) ? bma_page_url( 'szolgaltatasok' ) : home_url( '/szolgaltatasok/' );
	$contact_url = function_exists( 'bma_page_url' ) ? bma_page_url( 'kapcsolat' ) : home_url( '/kapcsolat/' );

	$eyebrow_bits = array_filter( array( $meta['project_type'], $meta['category'] ) );
	$lead          = has_excerpt() ? get_the_excerpt() : (string) $meta['project_description'];
	$svc_names     = $related ? implode( ' · ', wp_list_pluck( $related, 'post_title' ) ) : '';
	$cta_title     = $meta['cta_title'] ?: __( 'Hasonló projektet tervez?', 'bma-ajanlatkeres' );
	$cta_text      = $meta['cta_text'] ?: __( 'Állítsa össze, mire van szüksége, és néhány adat megadásával kérjen személyre szabott ajánlatot.', 'bma-ajanlatkeres' );

	$thumb_url = get_the_post_thumbnail_url( $post_id, 'full' );
	?>
	<article <?php post_class( 'bma-single-reference' ); ?>>
		<section class="pagehead">
			<div class="pagehead-inner pagehead-inner--split">
				<div>
					<p class="crumb">
						<a href="<?php echo esc_url( $home_url ); ?>"><?php esc_html_e( 'Főoldal', 'bma-ajanlatkeres' ); ?></a>
						/
						<a href="<?php echo esc_url( $list_url ); ?>"><?php esc_html_e( 'Referenciák', 'bma-ajanlatkeres' ); ?></a>
						/ <?php the_title(); ?>
					</p>
					<?php if ( $eyebrow_bits ) : ?>
						<p class="eyebrow eyebrow--light"><?php echo esc_html( implode( ' · ', $eyebrow_bits ) ); ?></p>
					<?php endif; ?>
					<h1><?php the_title(); ?></h1>
					<?php if ( $lead ) : ?>
						<p class="pagehead-lead"><?php echo esc_html( $lead ); ?></p>
					<?php endif; ?>
				</div>
				<div class="sidecard">
					<h3><?php esc_html_e( 'Projekt adatai', 'bma-ajanlatkeres' ); ?></h3>
					<ul>
						<?php if ( $location ) : ?>
							<li>
								<svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 21s7-6.2 7-11a7 7 0 10-14 0c0 4.8 7 11 7 11z"></path></svg>
								<span><?php printf( esc_html__( 'Helyszín: %s', 'bma-ajanlatkeres' ), esc_html( $location ) ); ?></span>
							</li>
						<?php endif; ?>
						<?php if ( $meta['category'] ) : ?>
							<li>
								<svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 20V7l8-4 8 4v13z"></path></svg>
								<span><?php printf( esc_html__( 'Típus: %s', 'bma-ajanlatkeres' ), esc_html( $meta['category'] ) ); ?></span>
							</li>
						<?php endif; ?>
						<?php if ( $year ) : ?>
							<li>
								<svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="8.5"></circle><path d="M12 7v5l3 2"></path></svg>
								<span><?php printf( esc_html__( 'Év: %s', 'bma-ajanlatkeres' ), esc_html( $year ) ); ?></span>
							</li>
						<?php endif; ?>
						<?php if ( $related ) : ?>
							<li>
								<svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h10"></path></svg>
								<span><?php printf( esc_html__( 'Szolgáltatások: %s', 'bma-ajanlatkeres' ), esc_html( implode( ', ', wp_list_pluck( $related, 'post_title' ) ) ) ); ?></span>
							</li>
						<?php endif; ?>
					</ul>
					<span class="tiny" style="color:#8fa2c4;display:block">
						<?php esc_html_e( 'A projekt részletei az ügyféllel egyeztetett mértékben nyilvánosak.', 'bma-ajanlatkeres' ); ?>
					</span>
				</div>
			</div>
		</section>

		<div class="rd-hero">
			<?php if ( $thumb_url ) : ?>
				<img src="<?php echo esc_url( $thumb_url ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>">
			<?php endif; ?>
		</div>

		<div class="appbody">
			<div class="bma-container">
				<div class="rd-grid">
					<div>
						<?php if ( $task ) : ?>
							<div class="rd-block">
								<h2><?php esc_html_e( 'A feladat', 'bma-ajanlatkeres' ); ?></h2>
								<?php echo wp_kses_post( wpautop( esc_html( $task ) ) ); ?>
							</div>
						<?php endif; ?>

						<?php if ( $work_rows || $duration ) : ?>
							<div class="rd-block">
								<h2><?php esc_html_e( 'Amit csináltunk', 'bma-ajanlatkeres' ); ?></h2>
								<div class="rd-facts">
									<?php foreach ( $work_rows as $row ) : ?>
										<div class="datarow">
											<?php if ( $row['label'] ) : ?>
												<span><?php echo esc_html( $row['label'] ); ?></span>
											<?php else : ?>
												<span><?php esc_html_e( 'Tétel', 'bma-ajanlatkeres' ); ?></span>
											<?php endif; ?>
											<strong style="font-weight:600"><?php echo esc_html( $row['value'] ); ?></strong>
										</div>
									<?php endforeach; ?>
									<?php if ( $duration ) : ?>
										<div class="datarow">
											<span><?php esc_html_e( 'Átfutás', 'bma-ajanlatkeres' ); ?></span>
											<strong style="font-weight:600"><?php echo esc_html( $duration ); ?></strong>
										</div>
									<?php endif; ?>
								</div>
							</div>
						<?php endif; ?>

						<?php if ( $result ) : ?>
							<div class="rd-block">
								<h2><?php esc_html_e( 'Eredmény', 'bma-ajanlatkeres' ); ?></h2>
								<?php echo wp_kses_post( wpautop( esc_html( $result ) ) ); ?>
							</div>
						<?php endif; ?>

						<?php if ( $gallery ) : ?>
							<div class="rd-block">
								<h2><?php esc_html_e( 'Képek', 'bma-ajanlatkeres' ); ?></h2>
								<div class="gallery">
									<?php foreach ( $gallery as $gid ) : ?>
										<button
											type="button"
											class="bma-ref-gallery-item"
											data-bma-lightbox="<?php echo esc_url( wp_get_attachment_image_url( $gid, 'large' ) ); ?>"
											aria-label="<?php esc_attr_e( 'Kép nagyítása', 'bma-ajanlatkeres' ); ?>"
										>
											<?php echo wp_get_attachment_image( $gid, 'medium_large' ); ?>
										</button>
									<?php endforeach; ?>
								</div>
							</div>
						<?php endif; ?>
					</div>

					<div>
						<?php if ( $related ) : ?>
							<div class="panel" style="margin-bottom:24px">
								<div class="panel-head"><h2><?php esc_html_e( 'Ehhez kapcsolódó szolgáltatások', 'bma-ajanlatkeres' ); ?></h2></div>
								<?php foreach ( $related as $svc ) : ?>
									<?php
									$hint = (string) get_post_meta( $svc->ID, '_bma_short_description', true );
									if ( '' === $hint ) {
										$hint = (string) $svc->post_excerpt;
									}
									?>
									<div class="svclink">
										<span>
											<strong><?php echo esc_html( $svc->post_title ); ?></strong>
											<?php if ( $hint ) : ?>
												<span><?php echo esc_html( wp_trim_words( $hint, 10 ) ); ?></span>
											<?php endif; ?>
										</span>
										<a href="<?php echo esc_url( get_permalink( $svc ) ); ?>" class="btn btn--outline btn--sm">
											<?php esc_html_e( 'Részletek', 'bma-ajanlatkeres' ); ?>
										</a>
									</div>
								<?php endforeach; ?>
								<div class="sum-foot">
									<a href="<?php echo esc_url( $svc_url ); ?>" class="btn btn--primary btn--sm" style="width:100%;justify-content:center">
										<?php esc_html_e( 'Ajánlatkérés indítása', 'bma-ajanlatkeres' ); ?>
									</a>
									<p class="sum-note"><?php esc_html_e( 'Az ajánlatkérés díjmentes és nem jár kötelezettséggel.', 'bma-ajanlatkeres' ); ?></p>
								</div>
							</div>
						<?php endif; ?>

						<?php if ( $more ) : ?>
							<div class="panel">
								<div class="panel-head"><h2><?php esc_html_e( 'További projektek', 'bma-ajanlatkeres' ); ?></h2></div>
								<?php foreach ( $more as $other ) : ?>
									<?php
									$other_meta = BMA_AJ_Reference_CPT::get_all_meta( (int) $other->ID );
									$sub        = $other_meta['category'] ?: ( $other_meta['project_type'] ?: '' );
									?>
									<a class="svclink" href="<?php echo esc_url( get_permalink( $other ) ); ?>" style="text-decoration:none;color:inherit">
										<span>
											<strong><?php echo esc_html( $other->post_title ); ?></strong>
											<?php if ( $sub ) : ?>
												<span><?php echo esc_html( $sub ); ?></span>
											<?php endif; ?>
										</span>
									</a>
								<?php endforeach; ?>
								<div class="sum-foot">
									<a href="<?php echo esc_url( $list_url ); ?>" class="btn btn--outline btn--sm" style="width:100%;justify-content:center">
										<?php esc_html_e( 'Összes referencia', 'bma-ajanlatkeres' ); ?>
									</a>
								</div>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>

		<section class="closing">
			<div class="closing-inner">
				<div>
					<p class="eyebrow eyebrow--light"><?php esc_html_e( 'Következő lépés', 'bma-ajanlatkeres' ); ?></p>
					<h2 class="h2"><?php echo esc_html( $cta_title ); ?></h2>
					<p><?php echo esc_html( $cta_text ); ?></p>
				</div>
				<div class="closing-actions">
					<a href="<?php echo esc_url( $svc_url ); ?>" class="btn btn--primary"><?php esc_html_e( 'Ajánlatkérés indítása', 'bma-ajanlatkeres' ); ?></a>
					<a href="<?php echo esc_url( $contact_url ); ?>" class="btn btn--ghost-light"><?php esc_html_e( 'Kapcsolat', 'bma-ajanlatkeres' ); ?></a>
				</div>
			</div>
		</section>
	</article>

	<dialog class="bma-lightbox" data-bma-lightbox-dialog>
		<button type="button" class="bma-lightbox-close" data-bma-lightbox-close aria-label="<?php esc_attr_e( 'Bezárás', 'bma-ajanlatkeres' ); ?>">×</button>
		<img src="" alt="" data-bma-lightbox-img>
	</dialog>
	<?php
endwhile;

get_footer();
