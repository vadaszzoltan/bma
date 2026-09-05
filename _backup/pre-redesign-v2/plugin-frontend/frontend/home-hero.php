<section class="bma-hero" aria-labelledby="bma-hero-title">
	<div class="bma-hero-media" aria-hidden="true">
		<?php
		$hero = BMA_AJ_Media_Seeder::get_hero_html( 'full' );
		if ( $hero ) {
			echo $hero; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image
		} else {
			?>
			<div class="bma-hero-placeholder" role="img" aria-label="<?php esc_attr_e( 'Mérnöki tervrajzok és műszaki dokumentáció', 'bma-ajanlatkeres' ); ?>"></div>
			<?php
		}
		?>
	</div>
	<div class="bma-hero-overlay">
		<div class="bma-container bma-hero-content">
			<p class="bma-hero-brand">BMA Engineering</p>
			<h1 id="bma-hero-title"><?php esc_html_e( 'Mérnöki tervezés átlátható folyamattal', 'bma-ajanlatkeres' ); ?></h1>
			<p class="bma-hero-lead">
				<?php esc_html_e( 'A BMA Engineering műszaki tervezési, engedélyeztetési, dokumentációs és közmű-koordinációs szolgáltatásokat biztosít lakossági és üzleti projektekhez. Válassza ki a projektjéhez szükséges szolgáltatásokat, adja meg a rendelkezésre álló adatokat, és kérjen személyre szabott ajánlatot.', 'bma-ajanlatkeres' ); ?>
			</p>
			<div class="bma-hero-actions">
				<a href="<?php echo esc_url( class_exists( 'BMA_AJ_I18n' ) ? BMA_AJ_I18n::page_url( 'szolgaltatasok' ) : home_url( '/szolgaltatasok/' ) ); ?>" class="bma-btn bma-btn-accent bma-btn-lg">
					<?php esc_html_e( 'Ajánlatkérés indítása', 'bma-ajanlatkeres' ); ?>
				</a>
				<a href="<?php echo esc_url( class_exists( 'BMA_AJ_I18n' ) ? BMA_AJ_I18n::page_url( 'referenciak' ) : home_url( '/referenciak/' ) ); ?>" class="bma-btn bma-btn-ghost bma-btn-lg">
					<?php esc_html_e( 'Referenciák', 'bma-ajanlatkeres' ); ?>
				</a>
			</div>
		</div>
	</div>
</section>
