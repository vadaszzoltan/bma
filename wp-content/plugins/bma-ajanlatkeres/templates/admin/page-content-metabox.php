<?php
/**
 * Page marketing content meta box.
 *
 * Expects: $post (WP_Post), $page_key (string).
 *
 * @package BMA_Ajanlatkeres
 */

defined( 'ABSPATH' ) || exit;

$sections     = BMA_AJ_Content::sections();
$fields       = BMA_AJ_Content::fields();
$section_page = BMA_AJ_Content::section_pages();
$lang         = BMA_AJ_Content::lang_for_post( (int) $post->ID );

$visible_sections = array();
foreach ( $sections as $sid => $slabel ) {
	if ( ( $section_page[ $sid ] ?? null ) === $page_key ) {
		$visible_sections[ $sid ] = $slabel;
	}
}
?>
<p class="description">
	<?php
	printf(
		/* translators: %s: language code */
		esc_html__( 'Ezek a szövegek ezen az oldalon jelennek meg. A mezők a jelenlegi tartalommal vannak kitöltve (%s). A fejléc elérhetőségei a Beállítások → Kapcsolat részen szerkeszthetők.', 'bma-ajanlatkeres' ),
		esc_html( strtoupper( $lang ) )
	);
	?>
</p>

<?php foreach ( $visible_sections as $sid => $slabel ) : ?>
	<h3 style="margin:1.5em 0 .6em;padding-top:.8em;border-top:1px solid #dcdcde"><?php echo esc_html( $slabel ); ?></h3>
	<table class="form-table" role="presentation">
		<?php foreach ( $fields as $key => $meta ) : ?>
			<?php if ( ( $meta['section'] ?? '' ) !== $sid ) : ?>
				<?php continue; ?>
			<?php endif; ?>
			<?php
			$value = BMA_AJ_Content::edit_value( $key, (int) $post->ID );
			$type  = (string) ( $meta['type'] ?? 'text' );
			$fid   = 'bma_pc_' . md5( $key );
			?>
			<tr>
				<th scope="row">
					<label for="<?php echo esc_attr( $fid ); ?>"><?php echo esc_html( (string) $meta['label'] ); ?></label>
				</th>
				<td>
					<?php if ( 'textarea' === $type || 'html' === $type ) : ?>
						<textarea
							class="large-text"
							rows="<?php echo 'html' === $type ? 3 : 4; ?>"
							id="<?php echo esc_attr( $fid ); ?>"
							name="bma_page_content[<?php echo esc_attr( $key ); ?>]"
						><?php echo esc_textarea( $value ); ?></textarea>
					<?php else : ?>
						<input
							type="text"
							class="large-text"
							id="<?php echo esc_attr( $fid ); ?>"
							name="bma_page_content[<?php echo esc_attr( $key ); ?>]"
							value="<?php echo esc_attr( $value ); ?>"
						>
					<?php endif; ?>
				</td>
			</tr>
		<?php endforeach; ?>
	</table>
<?php endforeach; ?>
