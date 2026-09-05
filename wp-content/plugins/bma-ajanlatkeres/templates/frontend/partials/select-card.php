<?php
/**
 * Selectable service item (.task).
 *
 * Expects: $item (object), $service_id (int), $service_name (string), $checked (bool)
 *
 * @package BMA_Ajanlatkeres
 */

defined( 'ABSPATH' ) || exit;

$choose_if    = isset( $item->choose_if ) ? (string) $item->choose_if : '';
$checked      = ! empty( $checked );
$service_id   = (int) $service_id;
$service_name = (string) $service_name;
$input_name   = 'selections[' . $service_id . '][]';
?>
<label
	class="task<?php echo $checked ? ' is-sel' : ''; ?>"
	data-item-id="<?php echo esc_attr( (string) $item->id ); ?>"
	data-item-name="<?php echo esc_attr( $item->name ); ?>"
	data-service-id="<?php echo esc_attr( (string) $service_id ); ?>"
	data-service-name="<?php echo esc_attr( $service_name ); ?>"
>
	<input
		type="checkbox"
		class="visually-hidden bma-item-checkbox"
		name="<?php echo esc_attr( $input_name ); ?>"
		value="<?php echo esc_attr( (string) $item->id ); ?>"
		<?php checked( $checked ); ?>
		data-service-id="<?php echo esc_attr( (string) $service_id ); ?>"
	>
	<span class="box" aria-hidden="true">
		<svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="#fff" stroke-width="3.4" stroke-linecap="round" stroke-linejoin="round">
			<path d="M4 12.5l5 5L20 6.5"></path>
		</svg>
	</span>
	<span>
		<strong><?php echo esc_html( $item->name ); ?></strong>
		<?php if ( ! empty( $item->short_description ) ) : ?>
			<span class="hint"><?php echo esc_html( $item->short_description ); ?></span>
		<?php endif; ?>
		<?php if ( $choose_if ) : ?>
			<span class="task-when">
				<b><?php esc_html_e( 'Ezt válassza, ha:', 'bma-ajanlatkeres' ); ?></b>
				<span><?php echo esc_html( $choose_if ); ?></span>
			</span>
		<?php endif; ?>
	</span>
</label>
