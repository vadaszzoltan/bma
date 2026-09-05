<?php
defined( 'ABSPATH' ) || exit;

$all_services = get_posts(
	array(
		'post_type'      => BMA_AJ_Service_CPT::POST_TYPE,
		'posts_per_page' => -1,
		'post_status'    => 'publish',
		'orderby'        => 'title',
		'order'          => 'ASC',
	)
);

$gallery_ids   = isset( $gallery ) && is_array( $gallery ) ? $gallery : array();
$gallery_csv   = implode( ',', array_map( 'absint', $gallery_ids ) );
$project_types = array( 'Ipari beruházás', 'Új építés', 'Felújítás', 'Bővítés', 'Egyéb' );
$categories    = array( 'Lakossági', 'Üzleti' );
?>
<table class="form-table">
	<tr>
		<th><label for="bma_category"><?php esc_html_e( 'Kategória', 'bma-ajanlatkeres' ); ?></label></th>
		<td>
			<select id="bma_category" name="bma_category">
				<option value=""><?php esc_html_e( '— Válasszon —', 'bma-ajanlatkeres' ); ?></option>
				<?php foreach ( $categories as $cat ) : ?>
					<option value="<?php echo esc_attr( $cat ); ?>" <?php selected( ( $category ?? '' ), $cat ); ?>><?php echo esc_html( $cat ); ?></option>
				<?php endforeach; ?>
			</select>
		</td>
	</tr>
	<tr>
		<th><label for="bma_project_type"><?php esc_html_e( 'Projekt típusa', 'bma-ajanlatkeres' ); ?></label></th>
		<td>
			<input type="text" id="bma_project_type" name="bma_project_type" list="bma_project_type_list" value="<?php echo esc_attr( $project_type ?? '' ); ?>" class="regular-text">
			<datalist id="bma_project_type_list">
				<?php foreach ( $project_types as $pt ) : ?>
					<option value="<?php echo esc_attr( $pt ); ?>">
				<?php endforeach; ?>
			</datalist>
		</td>
	</tr>
	<tr>
		<th><label for="bma_location"><?php esc_html_e( 'Helyszín', 'bma-ajanlatkeres' ); ?></label></th>
		<td><input type="text" id="bma_location" name="bma_location" value="<?php echo esc_attr( $location ?? '' ); ?>" class="regular-text"></td>
	</tr>
	<tr>
		<th><label for="bma_year"><?php esc_html_e( 'Év', 'bma-ajanlatkeres' ); ?></label></th>
		<td><input type="text" id="bma_year" name="bma_year" value="<?php echo esc_attr( $year ?? '' ); ?>" class="small-text" placeholder="2024"></td>
	</tr>
	<tr>
		<th><label for="bma_project_description"><?php esc_html_e( 'A projekt', 'bma-ajanlatkeres' ); ?></label></th>
		<td><textarea id="bma_project_description" name="bma_project_description" rows="5" class="large-text"><?php echo esc_textarea( $project_description ?? '' ); ?></textarea></td>
	</tr>
	<tr>
		<th><label for="bma_task"><?php esc_html_e( 'Mi volt a feladat?', 'bma-ajanlatkeres' ); ?></label></th>
		<td><textarea id="bma_task" name="bma_task" rows="4" class="large-text"><?php echo esc_textarea( $task ?? '' ); ?></textarea></td>
	</tr>
	<tr>
		<th><label for="bma_work_done"><?php esc_html_e( 'Mit végeztünk el?', 'bma-ajanlatkeres' ); ?></label></th>
		<td>
			<textarea id="bma_work_done" name="bma_work_done" rows="6" class="large-text" placeholder="<?php esc_attr_e( 'Egy sor = egy pont', 'bma-ajanlatkeres' ); ?>"><?php echo esc_textarea( $work_done ?? '' ); ?></textarea>
		</td>
	</tr>
	<tr>
		<th><label for="bma_solution"><?php esc_html_e( 'A megoldás', 'bma-ajanlatkeres' ); ?></label></th>
		<td><textarea id="bma_solution" name="bma_solution" rows="4" class="large-text"><?php echo esc_textarea( $solution ?? '' ); ?></textarea></td>
	</tr>
	<tr>
		<th><label for="bma_result"><?php esc_html_e( 'Eredmény', 'bma-ajanlatkeres' ); ?></label></th>
		<td><textarea id="bma_result" name="bma_result" rows="3" class="large-text"><?php echo esc_textarea( $result ?? '' ); ?></textarea></td>
	</tr>
	<tr>
		<th><?php esc_html_e( 'Projektgaléria', 'bma-ajanlatkeres' ); ?></th>
		<td>
			<input type="hidden" id="bma_gallery" name="bma_gallery" value="<?php echo esc_attr( $gallery_csv ); ?>">
			<div id="bma-gallery-preview" class="bma-gallery-preview">
				<?php foreach ( $gallery_ids as $gid ) : ?>
					<?php echo wp_get_attachment_image( $gid, 'thumbnail' ); ?>
				<?php endforeach; ?>
			</div>
			<p>
				<button type="button" class="button" id="bma-gallery-add"><?php esc_html_e( 'Képek kiválasztása', 'bma-ajanlatkeres' ); ?></button>
				<button type="button" class="button" id="bma-gallery-clear"><?php esc_html_e( 'Galéria törlése', 'bma-ajanlatkeres' ); ?></button>
			</p>
		</td>
	</tr>
	<tr>
		<th><?php esc_html_e( 'Kapcsolódó szolgáltatások', 'bma-ajanlatkeres' ); ?></th>
		<td>
			<?php foreach ( $all_services as $svc ) : ?>
				<label style="display:block;margin-bottom:0.35rem">
					<input type="checkbox" name="bma_related_services[]" value="<?php echo esc_attr( (string) $svc->ID ); ?>" <?php checked( in_array( (int) $svc->ID, array_map( 'intval', (array) ( $related ?? array() ) ), true ) ); ?>>
					<?php echo esc_html( $svc->post_title ); ?>
				</label>
			<?php endforeach; ?>
		</td>
	</tr>
	<tr>
		<th><label for="bma_cta_title"><?php esc_html_e( 'CTA cím', 'bma-ajanlatkeres' ); ?></label></th>
		<td><input type="text" id="bma_cta_title" name="bma_cta_title" value="<?php echo esc_attr( $cta_title ?? '' ); ?>" class="large-text"></td>
	</tr>
	<tr>
		<th><label for="bma_cta_text"><?php esc_html_e( 'CTA szöveg', 'bma-ajanlatkeres' ); ?></label></th>
		<td><textarea id="bma_cta_text" name="bma_cta_text" rows="3" class="large-text"><?php echo esc_textarea( $cta_text ?? '' ); ?></textarea></td>
	</tr>
</table>
<style>
.bma-gallery-preview { display:flex; flex-wrap:wrap; gap:8px; margin-bottom:8px; }
.bma-gallery-preview img { width:72px; height:72px; object-fit:cover; border:1px solid #c3c4c7; }
</style>
