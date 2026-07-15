<?php
defined( 'ABSPATH' ) || exit;
$is_active       = $is_active ?? get_post_meta( $post->ID, '_bma_is_active', true );
$sort_order      = $sort_order ?? get_post_meta( $post->ID, '_bma_sort_order', true );
$client_info     = $client_info ?? get_post_meta( $post->ID, '_bma_client_info', true );
$admin_note      = $admin_note ?? get_post_meta( $post->ID, '_bma_admin_note', true );
$short_desc      = $short_desc ?? get_post_meta( $post->ID, '_bma_short_description', true );
$use_placeholder = $use_placeholder ?? get_post_meta( $post->ID, '_bma_use_placeholder', true );
?>
<table class="form-table">
	<tr>
		<th><label for="bma_is_active"><?php esc_html_e( 'Aktív', 'bma-ajanlatkeres' ); ?></label></th>
		<td><input type="checkbox" id="bma_is_active" name="bma_is_active" value="1" <?php checked( $is_active, '1' ); ?>></td>
	</tr>
	<tr>
		<th><label for="bma_sort_order"><?php esc_html_e( 'Megjelenési sorrend', 'bma-ajanlatkeres' ); ?></label></th>
		<td><input type="number" id="bma_sort_order" name="bma_sort_order" value="<?php echo esc_attr( $sort_order ?: 0 ); ?>" min="0"></td>
	</tr>
	<tr>
		<th><label for="bma_short_description"><?php esc_html_e( 'Rövid leírás', 'bma-ajanlatkeres' ); ?></label></th>
		<td><textarea id="bma_short_description" name="bma_short_description" rows="3" class="large-text"><?php echo esc_textarea( $short_desc ); ?></textarea></td>
	</tr>
	<tr>
		<th><label for="bma_use_placeholder"><?php esc_html_e( 'Placeholder kép', 'bma-ajanlatkeres' ); ?></label></th>
		<td><input type="checkbox" id="bma_use_placeholder" name="bma_use_placeholder" value="1" <?php checked( $use_placeholder, '1' ); ?>> <?php esc_html_e( '[SZOLGÁLTATÁS KÉP PLACEHOLDER] megjelenítése', 'bma-ajanlatkeres' ); ?></td>
	</tr>
	<tr>
		<th><label for="bma_client_info"><?php esc_html_e( 'Ügyféloldali információ', 'bma-ajanlatkeres' ); ?></label></th>
		<td><textarea id="bma_client_info" name="bma_client_info" rows="3" class="large-text"><?php echo esc_textarea( $client_info ); ?></textarea></td>
	</tr>
	<tr>
		<th><label for="bma_admin_note"><?php esc_html_e( 'Adminisztrátori megjegyzés', 'bma-ajanlatkeres' ); ?></label></th>
		<td><textarea id="bma_admin_note" name="bma_admin_note" rows="3" class="large-text"><?php echo esc_textarea( $admin_note ); ?></textarea></td>
	</tr>
</table>
