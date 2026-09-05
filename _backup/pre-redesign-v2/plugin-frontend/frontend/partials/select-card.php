<?php
/**
 * Selectable service item card.
 *
 * Expects: $item (object), $service_id (int), $service_name (string), $checked (bool)
 *
 * @package BMA_Ajanlatkeres
 */

defined( 'ABSPATH' ) || exit;

$choose_if = isset( $item->choose_if ) ? (string) $item->choose_if : '';
$checked   = ! empty( $checked );
?>
<label class="bma-select-card<?php echo $checked ? ' is-selected' : ''; ?>"
	data-item-id="<?php echo esc_attr( (string) $item->id ); ?>"
	data-item-name="<?php echo esc_attr( $item->name ); ?>"
	data-service-id="<?php echo esc_attr( (string) $service_id ); ?>"
	data-service-name="<?php echo esc_attr( $service_name ); ?>">
	<input type="checkbox"
		class="bma-item-checkbox"
		name="selected_items[]"
		value="<?php echo esc_attr( (string) $item->id ); ?>"
		<?php checked( $checked ); ?>
		data-service-id="<?php echo esc_attr( (string) $service_id ); ?>">
	<span class="bma-select-card-top">
		<span class="bma-select-card-check" aria-hidden="true"></span>
		<span class="bma-select-card-badge"><?php esc_html_e( 'Kiválasztva', 'bma-ajanlatkeres' ); ?></span>
	</span>
	<strong class="bma-select-card-title"><?php echo esc_html( $item->name ); ?></strong>
	<?php if ( ! empty( $item->short_description ) ) : ?>
		<span class="bma-select-card-desc"><?php echo esc_html( $item->short_description ); ?></span>
	<?php endif; ?>
	<?php if ( $choose_if ) : ?>
		<span class="bma-select-card-choose">
			<em><?php esc_html_e( 'Ezt válassza, ha:', 'bma-ajanlatkeres' ); ?></em>
			<?php echo esc_html( $choose_if ); ?>
		</span>
	<?php endif; ?>
	<?php if ( ! empty( $item->is_required ) ) : ?>
		<em class="bma-required-tag"><?php esc_html_e( 'Kötelező', 'bma-ajanlatkeres' ); ?></em>
	<?php endif; ?>
</label>
