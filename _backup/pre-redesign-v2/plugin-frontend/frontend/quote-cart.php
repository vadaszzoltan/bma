<?php
defined( 'ABSPATH' ) || exit;
$items = BMA_AJ_Cart::get_items();
?>
<section class="bma-section bma-quote-cart" aria-labelledby="bma-cart-title">
	<div class="bma-container">
		<p class="bma-section-eyebrow"><?php esc_html_e( 'Ajánlatkérés', 'bma-ajanlatkeres' ); ?></p>
		<h1 id="bma-cart-title"><?php esc_html_e( 'Ajánlatkérési kosár', 'bma-ajanlatkeres' ); ?></h1>

		<nav class="bma-flow-steps" aria-label="<?php esc_attr_e( 'Ajánlatkérési lépések', 'bma-ajanlatkeres' ); ?>">
			<span class="bma-flow-step is-done"><?php esc_html_e( '1. Szolgáltatások', 'bma-ajanlatkeres' ); ?></span>
			<span class="bma-flow-step is-current"><?php esc_html_e( '2. Kosár', 'bma-ajanlatkeres' ); ?></span>
			<span class="bma-flow-step"><?php esc_html_e( '3. Adatok', 'bma-ajanlatkeres' ); ?></span>
		</nav>

		<?php if ( empty( $items ) ) : ?>
			<div class="bma-flow-empty">
				<p role="status"><?php esc_html_e( 'A kosár üres. Válasszon szolgáltatást a folytatáshoz.', 'bma-ajanlatkeres' ); ?></p>
				<a href="<?php echo esc_url( home_url( '/szolgaltatasok/' ) ); ?>" class="bma-btn bma-btn-accent bma-btn-lg"><?php esc_html_e( 'Szolgáltatások', 'bma-ajanlatkeres' ); ?></a>
			</div>
		<?php else : ?>
			<p class="bma-section-intro"><?php esc_html_e( 'Ellenőrizze a kiválasztott szolgáltatásokat. A projekt adatait a következő lépésben, egyszer kell megadnia.', 'bma-ajanlatkeres' ); ?></p>

			<div class="bma-cart-layout">
				<div class="bma-cart-items">
					<?php foreach ( $items as $key => $item ) : ?>
						<article class="bma-cart-card" data-cart-key="<?php echo esc_attr( $key ); ?>">
							<div class="bma-cart-card-head">
								<p class="bma-tile-eyebrow"><?php esc_html_e( 'Szolgáltatás', 'bma-ajanlatkeres' ); ?></p>
								<button type="button" class="bma-cart-remove bma-remove-cart" data-cart-key="<?php echo esc_attr( $key ); ?>" aria-label="<?php esc_attr_e( 'Eltávolítás', 'bma-ajanlatkeres' ); ?>">&times;</button>
							</div>
							<h2><?php echo esc_html( $item['service_name'] ); ?></h2>
							<?php if ( ! empty( $item['selected_items'] ) ) : ?>
								<ul class="bma-cart-tags">
									<?php
									global $wpdb;
									foreach ( $item['selected_items'] as $item_id ) {
										$name = $wpdb->get_var( $wpdb->prepare( "SELECT name FROM {$wpdb->prefix}bma_service_items WHERE id = %d", $item_id ) );
										if ( $name ) {
											echo '<li>' . esc_html( $name ) . '</li>';
										}
									}
									?>
								</ul>
							<?php else : ?>
								<p class="bma-cart-muted"><?php esc_html_e( 'Nincs részletes elem kiválasztva – a szolgáltatás oldalán szerkesztheti.', 'bma-ajanlatkeres' ); ?></p>
							<?php endif; ?>
							<a href="<?php echo esc_url( get_permalink( $item['service_id'] ) ); ?>" class="bma-link"><?php esc_html_e( 'Elemek szerkesztése', 'bma-ajanlatkeres' ); ?></a>
						</article>
					<?php endforeach; ?>
				</div>

				<aside class="bma-cart-summary">
					<p class="bma-tile-eyebrow"><?php esc_html_e( 'Összesítés', 'bma-ajanlatkeres' ); ?></p>
					<h3><?php esc_html_e( 'Következő lépés', 'bma-ajanlatkeres' ); ?></h3>
					<p class="bma-cart-count"><?php printf( esc_html__( '%d szolgáltatás kiválasztva', 'bma-ajanlatkeres' ), count( $items ) ); ?></p>
					<p class="bma-cart-note"><?php esc_html_e( 'Az ajánlatkérés véglegesítése nem jár fizetési kötelezettséggel. Az árat az adminisztrátor készíti el később.', 'bma-ajanlatkeres' ); ?></p>
					<a href="<?php echo esc_url( home_url( '/ajanlatkeres/' ) ); ?>" class="bma-btn bma-btn-accent bma-btn-block bma-btn-lg"><?php esc_html_e( 'Tovább – adatok megadása', 'bma-ajanlatkeres' ); ?></a>
					<a href="<?php echo esc_url( home_url( '/szolgaltatasok/' ) ); ?>" class="bma-btn bma-btn-block"><?php esc_html_e( 'További szolgáltatás', 'bma-ajanlatkeres' ); ?></a>
				</aside>
			</div>
		<?php endif; ?>
	</div>
</section>
