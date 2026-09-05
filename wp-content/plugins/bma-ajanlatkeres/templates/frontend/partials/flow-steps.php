<?php
/**
 * Quote flow step strip (.steps / .stepchip).
 *
 * Expects: $current_step (int 1..4)
 *
 * @package BMA_Ajanlatkeres
 */

defined( 'ABSPATH' ) || exit;

$current_step = isset( $current_step ) ? max( 1, min( 4, (int) $current_step ) ) : 1;

$steps = array(
	1 => array(
		'num'   => '01',
		'label' => __( 'Szolgáltatások', 'bma-ajanlatkeres' ),
		'url'   => function_exists( 'bma_page_url' ) ? bma_page_url( 'szolgaltatasok' ) : home_url( '/' ),
	),
	2 => array(
		'num'   => '02',
		'label' => __( 'Kosár', 'bma-ajanlatkeres' ),
		'url'   => function_exists( 'bma_page_url' ) ? bma_page_url( 'ajanlatkosar' ) : home_url( '/' ),
	),
	3 => array(
		'num'   => '03',
		'label' => __( 'Adatok és beküldés', 'bma-ajanlatkeres' ),
		'url'   => function_exists( 'bma_page_url' ) ? bma_page_url( 'ajanlatkeres' ) : home_url( '/' ),
	),
	4 => array(
		'num'   => '04',
		'label' => __( 'Visszaigazolás', 'bma-ajanlatkeres' ),
		'url'   => '',
	),
);
?>
<nav class="steps" aria-label="<?php esc_attr_e( 'Ajánlatkérési lépések', 'bma-ajanlatkeres' ); ?>">
	<?php foreach ( $steps as $n => $step ) : ?>
		<?php
		$is_now  = ( $n === $current_step );
		$is_done = ( $n < $current_step );
		$class   = 'stepchip';
		if ( $is_now ) {
			$class .= ' is-now';
		} elseif ( $is_done ) {
			$class .= ' is-done';
		}
		?>
		<?php if ( $is_done && $step['url'] ) : ?>
			<a
				href="<?php echo esc_url( $step['url'] ); ?>"
				class="<?php echo esc_attr( $class ); ?>"
			><b><?php echo esc_html( $step['num'] ); ?></b><span><?php echo esc_html( $step['label'] ); ?></span></a>
		<?php elseif ( $is_now ) : ?>
			<div class="<?php echo esc_attr( $class ); ?>" aria-current="step">
				<b><?php echo esc_html( $step['num'] ); ?></b><span><?php echo esc_html( $step['label'] ); ?></span>
			</div>
		<?php else : ?>
			<div class="<?php echo esc_attr( $class ); ?>">
				<b><?php echo esc_html( $step['num'] ); ?></b><span><?php echo esc_html( $step['label'] ); ?></span>
			</div>
		<?php endif; ?>
	<?php endforeach; ?>
</nav>
