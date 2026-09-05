</main>

<footer class="bma-site-footer" role="contentinfo">
	<?php
	$company = class_exists( 'BMA_AJ_Company' ) ? BMA_AJ_Company::get( 'company_name' ) : 'BMA Engineering';
	$addr    = class_exists( 'BMA_AJ_Company' ) ? BMA_AJ_Company::get( 'contact_address' ) : '';
	$phone   = class_exists( 'BMA_AJ_Company' ) ? BMA_AJ_Company::get( 'contact_phone' ) : '';
	$email   = class_exists( 'BMA_AJ_Company' ) ? BMA_AJ_Company::get( 'contact_email' ) : '';
	$cui     = class_exists( 'BMA_AJ_Company' ) ? BMA_AJ_Company::get( 'cui' ) : '';
	?>
	<div class="bma-footer-inner">
		<div class="bma-footer-col">
			<div class="bma-footer-logo-slot" aria-hidden="true">[LOGÓ]</div>
			<?php if ( $company ) : ?>
				<p><strong><?php echo esc_html( $company ); ?></strong></p>
			<?php endif; ?>
			<?php if ( $addr ) : ?>
				<p><?php echo nl2br( esc_html( $addr ) ); ?></p>
			<?php endif; ?>
			<?php if ( $phone || $email ) : ?>
				<p>
					<?php if ( $phone ) : ?>
						<a href="<?php echo esc_url( class_exists( 'BMA_AJ_Company' ) ? BMA_AJ_Company::phone_href( $phone ) : 'tel:' . preg_replace( '/\s+/', '', $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a>
					<?php endif; ?>
					<?php if ( $phone && $email ) : ?> · <?php endif; ?>
					<?php if ( $email ) : ?>
						<a href="<?php echo esc_url( 'mailto:' . $email ); ?>"><?php echo esc_html( $email ); ?></a>
					<?php endif; ?>
				</p>
			<?php endif; ?>
			<?php if ( $cui ) : ?>
				<p><?php printf( esc_html__( 'CUI: %s', 'bma-engineering' ), esc_html( $cui ) ); ?></p>
			<?php endif; ?>
		</div>
		<div class="bma-footer-col">
			<h2 class="bma-footer-heading"><?php esc_html_e( 'Navigáció', 'bma-engineering' ); ?></h2>
			<nav aria-label="<?php esc_attr_e( 'Lábléc navigáció', 'bma-engineering' ); ?>">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'footer',
						'container'      => false,
						'menu_class'     => 'bma-menu',
						'fallback_cb'    => 'bma_footer_fallback_menu',
					)
				);
				?>
			</nav>
		</div>
		<div class="bma-footer-col">
			<h2 class="bma-footer-heading"><?php esc_html_e( 'Közösségi média', 'bma-engineering' ); ?></h2>
			<?php if ( shortcode_exists( 'bma_social_links' ) ) : ?>
				<?php echo do_shortcode( '[bma_social_links]' ); ?>
			<?php endif; ?>
		</div>
	</div>
	<div class="bma-footer-bottom">
		<p>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php echo esc_html( $company ?: 'BMA Engineering' ); ?></p>
		<p>
			<a href="<?php echo esc_url( home_url( '/adatvedelem/' ) ); ?>"><?php esc_html_e( 'Adatvédelem', 'bma-engineering' ); ?></a>
			·
			<a href="<?php echo esc_url( home_url( '/cookie-tajekoztato/' ) ); ?>"><?php esc_html_e( 'Cookie tájékoztató', 'bma-engineering' ); ?></a>
			·
			<a href="<?php echo esc_url( home_url( '/aszf/' ) ); ?>"><?php esc_html_e( 'ÁSZF', 'bma-engineering' ); ?></a>
		</p>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
