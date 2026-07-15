<?php
/**
 * Single reference template.
 *
 * @package BMA_Ajanlatkeres
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	$type     = get_post_meta( get_the_ID(), '_bma_project_type', true );
	$location = get_post_meta( get_the_ID(), '_bma_location', true );
	$year     = get_post_meta( get_the_ID(), '_bma_year', true );
	?>
	<article <?php post_class( 'bma-single-reference' ); ?>>
		<div class="bma-container">
			<div class="bma-placeholder-img bma-placeholder-lg">[REFERENCIA GALÉRIA PLACEHOLDER]</div>
			<h1><?php the_title(); ?></h1>
			<?php if ( $type || $location || $year ) : ?>
				<ul class="bma-meta-list">
					<?php if ( $type ) : ?><li><?php esc_html_e( 'Projekt típusa:', 'bma-ajanlatkeres' ); ?> <?php echo esc_html( $type ); ?></li><?php endif; ?>
					<?php if ( $location ) : ?><li><?php esc_html_e( 'Helyszín:', 'bma-ajanlatkeres' ); ?> <?php echo esc_html( $location ); ?></li><?php endif; ?>
					<?php if ( $year ) : ?><li><?php esc_html_e( 'Év:', 'bma-ajanlatkeres' ); ?> <?php echo esc_html( $year ); ?></li><?php endif; ?>
				</ul>
			<?php endif; ?>
			<div class="bma-reference-content"><?php the_content(); ?></div>
		</div>
	</article>
	<?php
endwhile;

get_footer();
