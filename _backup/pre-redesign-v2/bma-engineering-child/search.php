<?php
/**
 * Search results (including empty state).
 *
 * @package BMA_Engineering
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<section class="bma-section bma-error-page bma-search-page" aria-labelledby="bma-search-title">
	<div class="bma-container bma-container-narrow">
		<p class="bma-section-eyebrow"><?php esc_html_e( 'Keresés', 'bma-engineering' ); ?></p>
		<h1 id="bma-search-title">
			<?php
			printf(
				/* translators: %s: search query */
				esc_html__( 'Keresés: %s', 'bma-engineering' ),
				esc_html( get_search_query() )
			);
			?>
		</h1>

		<?php if ( have_posts() ) : ?>
			<ul class="bma-search-results">
				<?php
				while ( have_posts() ) :
					the_post();
					?>
					<li>
						<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
						<?php if ( has_excerpt() ) : ?>
							<p><?php echo esc_html( get_the_excerpt() ); ?></p>
						<?php endif; ?>
					</li>
				<?php endwhile; ?>
			</ul>
		<?php else : ?>
			<p class="bma-section-intro">
				<?php esc_html_e( 'Nincs találat a keresésre. Próbáljon másik kifejezést, vagy böngésszen a szolgáltatások között.', 'bma-engineering' ); ?>
			</p>
			<div class="bma-form-actions">
				<a class="bma-btn bma-btn-accent bma-btn-lg" href="<?php echo esc_url( home_url( '/szolgaltatasok/' ) ); ?>">
					<?php esc_html_e( 'Szolgáltatások', 'bma-engineering' ); ?>
				</a>
				<a class="bma-btn" href="<?php echo esc_url( home_url( '/' ) ); ?>">
					<?php esc_html_e( 'Főoldal', 'bma-engineering' ); ?>
				</a>
			</div>
		<?php endif; ?>
	</div>
</section>
<?php
get_footer();
