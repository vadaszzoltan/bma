<?php
/**
 * Single service template – selection UX.
 *
 * @package BMA_Ajanlatkeres
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	$service_id    = get_the_ID();
	$items         = BMA_AJ_Dynamic_Fields::get_service_items( $service_id );
	$related       = BMA_AJ_Service_CPT::get_related_services( $service_id );
	$short_desc    = get_post_meta( $service_id, '_bma_short_description', true ) ?: get_the_excerpt();
	$choose_summary = get_post_meta( $service_id, '_bma_choose_summary', true );
	$when_needed   = get_post_meta( $service_id, '_bma_when_needed', true );
	$cart_sel      = BMA_AJ_Cart::get_selection_map();
	$sel           = $cart_sel[ (string) $service_id ] ?? array();
	$partial       = BMA_AJ_PLUGIN_DIR . 'templates/frontend/partials/select-card.php';
	?>
	<article <?php post_class( 'bma-single-service bma-section bma-selection-page' ); ?> data-bma-selection-root data-selection-mode="detail" data-service-id="<?php echo esc_attr( (string) $service_id ); ?>">
		<div class="bma-container">
			<header class="bma-service-header">
				<div class="bma-service-header-media">
					<?php BMA_AJ_Frontend::render_featured_or_placeholder( $service_id, '[SZOLGÁLTATÁS KÉP PLACEHOLDER]', 'large', 'bma-placeholder-lg' ); ?>
				</div>
				<div class="bma-service-header-copy">
					<p class="bma-section-eyebrow"><?php esc_html_e( 'Szolgáltatás', 'bma-ajanlatkeres' ); ?></p>
					<h1><?php the_title(); ?></h1>
					<?php if ( $choose_summary ) : ?>
						<p class="bma-choose-summary"><?php echo esc_html( $choose_summary ); ?></p>
					<?php elseif ( $short_desc ) : ?>
						<p class="bma-lead"><?php echo esc_html( $short_desc ); ?></p>
					<?php endif; ?>
				</div>
			</header>

			<div class="bma-service-content">
				<?php the_content(); ?>
			</div>

			<?php if ( $when_needed ) : ?>
				<section class="bma-when-needed" aria-labelledby="bma-when-needed-title">
					<h2 id="bma-when-needed-title"><?php esc_html_e( 'Mikor lehet erre szüksége?', 'bma-ajanlatkeres' ); ?></h2>
					<ul>
						<?php foreach ( preg_split( '/\r\n|\r|\n/', $when_needed ) as $line ) : ?>
							<?php
							$line = trim( $line );
							if ( '' === $line ) {
								continue;
							}
							?>
							<li><?php echo esc_html( $line ); ?></li>
						<?php endforeach; ?>
					</ul>
				</section>
			<?php endif; ?>

			<div class="bma-info-box bma-checkout-notice" role="note">
				<strong><?php esc_html_e( 'Hogyan működik?', 'bma-ajanlatkeres' ); ?></strong>
				<p><?php esc_html_e( 'Jelölje ki a szükséges részfeladatokat, majd adja hozzá őket az ajánlatkéréshez. A projekt részletes adatait egyszer, a következő lépésben kérjük be.', 'bma-ajanlatkeres' ); ?></p>
			</div>

			<?php if ( $items ) : ?>
				<section aria-labelledby="bma-items-title" class="bma-items-section">
					<h2 id="bma-items-title"><?php esc_html_e( 'Mit szeretne kérni?', 'bma-ajanlatkeres' ); ?></h2>
					<p class="bma-section-intro"><?php esc_html_e( 'Több elemet is kiválaszthat. Csak azokat jelölje be, amelyekre a projektjéhez szüksége van.', 'bma-ajanlatkeres' ); ?></p>
					<div class="bma-items-grid bma-items-grid--large">
						<?php
						foreach ( $items as $item ) {
							$service_name = get_the_title();
							$checked      = in_array( (int) $item->id, array_map( 'intval', $sel ), true );
							include $partial;
						}
						?>
					</div>
				</section>
			<?php endif; ?>

			<?php if ( $related ) : ?>
				<section class="bma-related-services" aria-labelledby="bma-related-title">
					<h2 id="bma-related-title"><?php esc_html_e( 'Egyéb szolgáltatások', 'bma-ajanlatkeres' ); ?></h2>
					<p class="bma-section-intro"><?php esc_html_e( 'Más szolgáltatásra is szüksége lehet? Tekintse meg a kapcsolódó területeket – a kiválasztás az adott szolgáltatás oldalán történik.', 'bma-ajanlatkeres' ); ?></p>
					<div class="bma-related-grid">
						<?php foreach ( $related as $rel ) : ?>
							<a class="bma-related-nav-card" href="<?php echo esc_url( get_permalink( $rel ) ); ?>" data-bma-nav-guard>
								<strong class="bma-related-title"><?php echo esc_html( $rel->post_title ); ?></strong>
								<span class="bma-related-desc"><?php echo esc_html( get_post_meta( $rel->ID, '_bma_short_description', true ) ?: $rel->post_excerpt ); ?></span>
								<span class="bma-related-cta"><?php esc_html_e( 'Szolgáltatás megtekintése', 'bma-ajanlatkeres' ); ?></span>
							</a>
						<?php endforeach; ?>
					</div>
				</section>
			<?php endif; ?>
		</div>

		<?php
		$cta_label = __( 'Kiválasztott szolgáltatások hozzáadása az ajánlatkéréshez', 'bma-ajanlatkeres' );
		$mode      = 'detail';
		include BMA_AJ_PLUGIN_DIR . 'templates/frontend/partials/selection-bar.php';
		?>
	</article>
	<?php
endwhile;

get_footer();
