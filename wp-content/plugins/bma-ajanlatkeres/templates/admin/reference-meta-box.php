<?php
defined( 'ABSPATH' ) || exit;
$all_services = get_posts( array( 'post_type' => BMA_AJ_Service_CPT::POST_TYPE, 'posts_per_page' => -1, 'post_status' => 'publish' ) );
?>
<table class="form-table">
	<tr>
		<th><label for="bma_project_type"><?php esc_html_e( 'Projekt típusa', 'bma-ajanlatkeres' ); ?></label></th>
		<td><input type="text" id="bma_project_type" name="bma_project_type" value="<?php echo esc_attr( $project_type ?? '' ); ?>" class="regular-text"></td>
	</tr>
	<tr>
		<th><label for="bma_location"><?php esc_html_e( 'Helyszín', 'bma-ajanlatkeres' ); ?></label></th>
		<td><input type="text" id="bma_location" name="bma_location" value="<?php echo esc_attr( $location ?? '' ); ?>" class="regular-text"></td>
	</tr>
	<tr>
		<th><label for="bma_year"><?php esc_html_e( 'Év', 'bma-ajanlatkeres' ); ?></label></th>
		<td><input type="text" id="bma_year" name="bma_year" value="<?php echo esc_attr( $year ?? '' ); ?>" class="regular-text"></td>
	</tr>
	<tr>
		<th><?php esc_html_e( 'Kapcsolódó szolgáltatások', 'bma-ajanlatkeres' ); ?></th>
		<td>
			<?php foreach ( $all_services as $svc ) : ?>
				<label><input type="checkbox" name="bma_related_services[]" value="<?php echo esc_attr( $svc->ID ); ?>" <?php checked( in_array( $svc->ID, (array) $related, true ) ); ?>> <?php echo esc_html( $svc->post_title ); ?></label><br>
			<?php endforeach; ?>
		</td>
	</tr>
</table>
