</main>

<?php bma_render_mobilebar(); ?>

<footer class="footer bma-site-footer" role="contentinfo">
	<?php
	$company = class_exists( 'BMA_AJ_Company' ) ? BMA_AJ_Company::get( 'company_name' ) : 'BMA Engineering';
	$addr    = class_exists( 'BMA_AJ_Company' ) ? BMA_AJ_Company::get( 'contact_address' ) : '';
	$phone   = class_exists( 'BMA_AJ_Company' ) ? BMA_AJ_Company::get( 'contact_phone' ) : '';
	$email   = class_exists( 'BMA_AJ_Company' ) ? BMA_AJ_Company::get( 'contact_email' ) : '';
	$cui     = class_exists( 'BMA_AJ_Company' ) ? BMA_AJ_Company::get( 'cui' ) : '';
	$hours   = class_exists( 'BMA_AJ_Company' ) ? BMA_AJ_Company::get( 'opening_hours' ) : '';
	$svc_url = ( class_exists( 'BMA_AJ_I18n' ) ? BMA_AJ_I18n::page_url( 'szolgaltatasok' ) : home_url( '/szolgaltatasok/' ) );
	?>
	<div class="footer-inner bma-footer-inner">
		<div class="bma-footer-col">
			<?php $bma_logo_lang = ( class_exists( 'BMA_AJ_I18n' ) && 'ro' === BMA_AJ_I18n::current_lang() ) ? 'ro' : 'en'; ?>
			<img src="<?php echo esc_url( bma_brand_asset_url( "bma-logo-full-{$bma_logo_lang}-negative.svg" ) ); ?>" class="bma-footer-logo" width="252" height="75" alt="BMA Engineering" loading="lazy">
			<?php if ( $company ) : ?>
				<div style="font-weight:700;color:#fff;margin-bottom:10px"><?php echo esc_html( $company ); ?></div>
			<?php endif; ?>
			<?php if ( $addr ) : ?>
				<div class="small" style="line-height:1.6"><?php echo nl2br( esc_html( $addr ) ); ?></div>
			<?php endif; ?>
			<?php if ( $phone || $email ) : ?>
				<div class="small" style="margin-top:12px">
					<?php if ( $phone ) : ?>
						<a href="<?php echo esc_url( class_exists( 'BMA_AJ_Company' ) ? BMA_AJ_Company::phone_href( $phone ) : 'tel:' . preg_replace( '/\s+/', '', $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a>
					<?php endif; ?>
					<?php if ( $phone && $email ) : ?> · <?php endif; ?>
					<?php if ( $email ) : ?>
						<a href="<?php echo esc_url( 'mailto:' . $email ); ?>"><?php echo esc_html( $email ); ?></a>
					<?php endif; ?>
				</div>
			<?php endif; ?>
			<?php if ( $hours ) : ?>
				<div class="small" style="margin-top:6px">
					<?php
					echo esc_html(
						sprintf(
							/* translators: %s: opening hours */
							__( 'Nyitvatartás: %s', 'bma-engineering' ),
							$hours
						)
					);
					?>
				</div>
			<?php endif; ?>
			<?php if ( $cui ) : ?>
				<div class="small" style="margin-top:6px"><?php printf( esc_html__( 'CUI: %s', 'bma-engineering' ), esc_html( $cui ) ); ?></div>
			<?php endif; ?>
		</div>
		<div class="bma-footer-col">
			<h4 class="bma-footer-heading"><?php echo class_exists( 'BMA_AJ_Content' ) ? esc_html( BMA_AJ_Content::get( 'footer.nav_heading' ) ) : esc_html__( 'Navigáció', 'bma-engineering' ); ?></h4>
			<nav class="footer-links" aria-label="<?php esc_attr_e( 'Lábléc navigáció', 'bma-engineering' ); ?>">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'footer',
						'container'      => false,
						'menu_class'     => 'bma-menu footer-links',
						'fallback_cb'    => 'bma_footer_fallback_menu',
					)
				);
				?>
			</nav>
		</div>
		<div class="bma-footer-col">
			<h4 class="bma-footer-heading"><?php echo class_exists( 'BMA_AJ_Content' ) ? esc_html( BMA_AJ_Content::get( 'footer.cta_heading' ) ) : esc_html__( 'Ajánlatkérés', 'bma-engineering' ); ?></h4>
			<p class="small" style="margin:0 0 16px;line-height:1.6">
				<?php echo class_exists( 'BMA_AJ_Content' ) ? esc_html( BMA_AJ_Content::get( 'footer.cta_blurb' ) ) : esc_html__( 'Válassza ki a szükséges szolgáltatásokat, és néhány adat megadásával kérjen személyre szabott ajánlatot.', 'bma-engineering' ); ?>
			</p>
			<a href="<?php echo esc_url( $svc_url ); ?>" class="btn btn--primary btn--sm"><?php echo class_exists( 'BMA_AJ_Content' ) ? esc_html( BMA_AJ_Content::get( 'footer.cta_button' ) ) : esc_html__( 'Ajánlatkérés indítása', 'bma-engineering' ); ?></a>
		</div>
	</div>
	<div class="footer-bottom bma-footer-bottom">
		<span>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php echo esc_html( $company ?: 'BMA Engineering' ); ?></span>
		<?php bma_render_langswitch( 'langswitch--light' ); ?>
		<span>
			<a href="<?php echo esc_url( ( class_exists( 'BMA_AJ_I18n' ) ? BMA_AJ_I18n::page_url( 'adatvedelem' ) : home_url( '/adatvedelem/' ) ) ); ?>"><?php esc_html_e( 'Adatvédelem', 'bma-engineering' ); ?></a>
			·
			<a href="<?php echo esc_url( ( class_exists( 'BMA_AJ_I18n' ) ? BMA_AJ_I18n::page_url( 'cookie-tajekoztato' ) : home_url( '/cookie-tajekoztato/' ) ) ); ?>"><?php esc_html_e( 'Cookie tájékoztató', 'bma-engineering' ); ?></a>
			·
			<a href="<?php echo esc_url( ( class_exists( 'BMA_AJ_I18n' ) ? BMA_AJ_I18n::page_url( 'aszf' ) : home_url( '/aszf/' ) ) ); ?>"><?php esc_html_e( 'ÁSZF', 'bma-engineering' ); ?></a>
		</span>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
