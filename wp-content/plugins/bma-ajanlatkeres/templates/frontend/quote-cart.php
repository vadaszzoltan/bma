<?php defined( 'ABSPATH' ) || exit; $items = BMA_AJ_Cart::get_items(); ?>
<section class="bma-section bma-quote-cart" aria-labelledby="bma-cart-title">
	<div class="bma-container">
		<h1 id="bma-cart-title"><?php esc_html_e( 'Ajánlatkérési kosár', 'bma-ajanlatkeres' ); ?></h1>

		<?php if ( empty( $items ) ) : ?>
			<p role="status"><?php esc_html_e( 'A kosár üres. Válasszon szolgáltatást a folytatáshoz.', 'bma-ajanlatkeres' ); ?></p>
			<a href="<?php echo esc_url( home_url( '/szolgaltatasok/' ) ); ?>" class="bma-btn bma-btn-primary"><?php esc_html_e( 'Szolgáltatások', 'bma-ajanlatkeres' ); ?></a>
		<?php else : ?>
			<div class="bma-cart-items">
				<?php foreach ( $items as $key => $item ) : ?>
					<article class="bma-cart-item" data-cart-key="<?php echo esc_attr( $key ); ?>">
						<h2><?php echo esc_html( $item['service_name'] ); ?></h2>
						<?php if ( ! empty( $item['selected_items'] ) ) : ?>
							<ul>
								<?php
								global $wpdb;
								foreach ( $item['selected_items'] as $item_id ) {
									$name = $wpdb->get_var( $wpdb->prepare( "SELECT name FROM {$wpdb->prefix}bma_service_items WHERE id = %d", $item_id ) );
									echo '<li>' . esc_html( $name ) . '</li>';
								}
								?>
							</ul>
						<?php endif; ?>
						<?php if ( ! empty( $item['field_values'] ) ) : ?>
							<dl class="bma-field-summary">
								<?php foreach ( $item['field_values'] as $fk => $fv ) : ?>
									<dt><?php echo esc_html( $fk ); ?></dt>
									<dd><?php echo esc_html( is_array( $fv ) ? implode( ', ', $fv ) : $fv ); ?></dd>
								<?php endforeach; ?>
							</dl>
						<?php endif; ?>
						<div class="bma-cart-actions">
							<a href="<?php echo esc_url( get_permalink( $item['service_id'] ) ); ?>" class="bma-btn"><?php esc_html_e( 'Szerkesztés', 'bma-ajanlatkeres' ); ?></a>
							<button type="button" class="bma-btn bma-btn-danger bma-remove-cart" data-cart-key="<?php echo esc_attr( $key ); ?>"><?php esc_html_e( 'Eltávolítás', 'bma-ajanlatkeres' ); ?></button>
						</div>
					</article>
				<?php endforeach; ?>
			</div>
			<div class="bma-cart-footer">
				<a href="<?php echo esc_url( home_url( '/szolgaltatasok/' ) ); ?>" class="bma-btn"><?php esc_html_e( 'Új szolgáltatás hozzáadása', 'bma-ajanlatkeres' ); ?></a>
				<a href="<?php echo esc_url( home_url( '/ajanlatkeres/' ) ); ?>" class="bma-btn bma-btn-primary"><?php esc_html_e( 'Tovább az ajánlatkéréshez', 'bma-ajanlatkeres' ); ?></a>
			</div>
		<?php endif; ?>
	</div>
</section>
