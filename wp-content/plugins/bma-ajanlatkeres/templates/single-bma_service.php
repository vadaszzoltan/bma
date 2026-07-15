<?php
/**
 * Single service template.
 *
 * @package BMA_Ajanlatkeres
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	$service_id  = get_the_ID();
	$items       = BMA_AJ_Dynamic_Fields::get_service_items( $service_id );
	$fields      = BMA_AJ_Dynamic_Fields::get_fields_for_context( $service_id );
	$client_info = get_post_meta( $service_id, '_bma_client_info', true );
	?>
	<article <?php post_class( 'bma-single-service' ); ?>>
		<div class="bma-container">
			<header class="bma-service-header">
				<div class="bma-placeholder-img bma-placeholder-lg">[SZOLGÁLTATÁS KÉP PLACEHOLDER]</div>
				<div>
					<h1><?php the_title(); ?></h1>
					<p class="bma-lead"><?php echo esc_html( get_post_meta( $service_id, '_bma_short_description', true ) ?: get_the_excerpt() ); ?></p>
				</div>
			</header>

			<div class="bma-service-content">
				<?php the_content(); ?>
			</div>

			<?php if ( $items ) : ?>
				<section aria-labelledby="bma-items-title">
					<h2 id="bma-items-title"><?php esc_html_e( 'Választható szolgáltatáselemek', 'bma-ajanlatkeres' ); ?></h2>
					<div class="bma-items-grid">
						<?php foreach ( $items as $item ) : ?>
							<label class="bma-item-card">
								<input type="checkbox" name="selected_items[]" value="<?php echo esc_attr( $item->id ); ?>" class="bma-item-checkbox">
								<strong><?php echo esc_html( $item->name ); ?></strong>
								<?php if ( $item->short_description ) : ?><span><?php echo esc_html( $item->short_description ); ?></span><?php endif; ?>
								<?php if ( $item->is_required ) : ?><em class="bma-required-tag"><?php esc_html_e( 'Kötelező', 'bma-ajanlatkeres' ); ?></em><?php endif; ?>
							</label>
						<?php endforeach; ?>
					</div>
				</section>
			<?php endif; ?>

			<form id="bma-service-quote-form" class="bma-form" data-service-id="<?php echo esc_attr( $service_id ); ?>">
				<section aria-labelledby="bma-fields-title">
					<h2 id="bma-fields-title"><?php esc_html_e( 'Szükséges adatok és dokumentumok', 'bma-ajanlatkeres' ); ?></h2>
					<div id="bma-dynamic-fields">
						<?php foreach ( $fields as $field ) : ?>
							<?php echo BMA_AJ_Dynamic_Fields::render_field( $field ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<?php endforeach; ?>
					</div>
				</section>

				<?php if ( $client_info ) : ?>
					<div class="bma-info-box" role="note"><?php echo esc_html( $client_info ); ?></div>
				<?php endif; ?>

				<button type="submit" class="bma-btn bma-btn-primary"><?php esc_html_e( 'Hozzáadás az ajánlatkérési kosárhoz', 'bma-ajanlatkeres' ); ?></button>
			</form>
		</div>
	</article>
	<?php
endwhile;

get_footer();
