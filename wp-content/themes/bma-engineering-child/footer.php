</main>

<footer class="bma-site-footer" role="contentinfo">
	<div class="bma-footer-inner">
		<div class="bma-footer-col">
			<p class="bma-footer-brand"><strong>BMA Engineering</strong></p>
			<p><?php echo esc_html( get_option( 'bma_contact_address', '[KAPCSOLATI ADAT – Cím]' ) ); ?></p>
			<p><?php echo esc_html( get_option( 'bma_contact_phone', '[KAPCSOLATI ADAT – Telefon]' ) ); ?></p>
		</div>
		<div class="bma-footer-col">
			<nav aria-label="<?php esc_attr_e( 'Lábléc navigáció', 'bma-engineering' ); ?>">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'footer',
						'container'      => false,
						'fallback_cb'    => 'bma_footer_fallback_menu',
					)
				);
				?>
			</nav>
		</div>
		<div class="bma-footer-col">
			<?php if ( shortcode_exists( 'bma_social_links' ) ) : ?>
				<?php echo do_shortcode( '[bma_social_links]' ); ?>
			<?php endif; ?>
		</div>
	</div>
	<div class="bma-footer-bottom">
		<p>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> BMA Engineering. <?php esc_html_e( 'Alapítva:', 'bma-engineering' ); ?> [CÉG ALAPÍTÁSÁNAK ÉVE]</p>
		<p><a href="<?php echo esc_url( home_url( '/adatvedelem/' ) ); ?>"><?php esc_html_e( 'Adatvédelem', 'bma-engineering' ); ?></a> · <a href="<?php echo esc_url( home_url( '/cookie-tajekoztato/' ) ); ?>"><?php esc_html_e( 'Cookie tájékoztató', 'bma-engineering' ); ?></a></p>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
