<?php
defined( 'ABSPATH' ) || exit;
$is_active       = $is_active ?? get_post_meta( $post->ID, '_bma_is_active', true );
$sort_order      = $sort_order ?? get_post_meta( $post->ID, '_bma_sort_order', true );
$client_info     = $client_info ?? get_post_meta( $post->ID, '_bma_client_info', true );
$admin_note      = $admin_note ?? get_post_meta( $post->ID, '_bma_admin_note', true );
$short_desc      = $short_desc ?? get_post_meta( $post->ID, '_bma_short_description', true );
$use_placeholder = $use_placeholder ?? get_post_meta( $post->ID, '_bma_use_placeholder', true );
$choose_summary  = $choose_summary ?? get_post_meta( $post->ID, '_bma_choose_summary', true );
$when_needed     = $when_needed ?? get_post_meta( $post->ID, '_bma_when_needed', true );
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
		<th><label for="bma_short_title"><?php esc_html_e( 'Rövid név (ajánlatkérésben)', 'bma-ajanlatkeres' ); ?></label></th>
		<td>
			<input type="text" id="bma_short_title" name="bma_short_title" class="regular-text" value="<?php echo esc_attr( (string) get_post_meta( $post->ID, '_bma_short_title', true ) ); ?>">
			<p class="description"><?php esc_html_e( 'Csoportnév az ajánlatkérés lépéseiben és az összefoglalóban, pl. „Épületgépészet”.', 'bma-ajanlatkeres' ); ?></p>
		</td>
	</tr>
	<tr>
		<th><label for="bma_no_design_phase"><?php esc_html_e( 'Tervfázis nélkül', 'bma-ajanlatkeres' ); ?></label></th>
		<td><label><input type="checkbox" id="bma_no_design_phase" name="bma_no_design_phase" value="1" <?php checked( get_post_meta( $post->ID, '_bma_no_design_phase', true ), '1' ); ?>> <?php esc_html_e( 'Az elemei egyetlen fázisban készülnek (pl. bekötések), az ajánlatkérésben nincs fázisválasztás.', 'bma-ajanlatkeres' ); ?></label></td>
	</tr>
	<tr>
		<th><label for="bma_short_description"><?php esc_html_e( 'Rövid leírás', 'bma-ajanlatkeres' ); ?></label></th>
		<td><textarea id="bma_short_description" name="bma_short_description" rows="3" class="large-text"><?php echo esc_textarea( $short_desc ); ?></textarea></td>
	</tr>
	<tr>
		<th><label for="bma_choose_summary"><?php esc_html_e( 'Ezt válassza, ha… (összefoglaló)', 'bma-ajanlatkeres' ); ?></label></th>
		<td><textarea id="bma_choose_summary" name="bma_choose_summary" rows="3" class="large-text"><?php echo esc_textarea( $choose_summary ); ?></textarea></td>
	</tr>
	<tr>
		<th><label for="bma_when_needed"><?php esc_html_e( 'Mikor lehet erre szüksége?', 'bma-ajanlatkeres' ); ?></label></th>
		<td><textarea id="bma_when_needed" name="bma_when_needed" rows="5" class="large-text" placeholder="<?php esc_attr_e( 'Egy sor = egy pont', 'bma-ajanlatkeres' ); ?>"><?php echo esc_textarea( $when_needed ); ?></textarea></td>
	</tr>
	<tr>
		<th><label for="bma_needs_required"><?php esc_html_e( 'Mire lesz szükség? – Kötelező', 'bma-ajanlatkeres' ); ?></label></th>
		<td>
			<textarea id="bma_needs_required" name="bma_needs_required" rows="4" class="large-text" placeholder="<?php esc_attr_e( 'Egy sor = egy tétel', 'bma-ajanlatkeres' ); ?>"><?php echo esc_textarea( (string) ( $needs_required ?? '' ) ); ?></textarea>
			<p class="description"><?php esc_html_e( 'A szekció akkor jelenik meg, ha legalább egy oszlop ki van töltve – csak a kitöltött oszlopok renderelődnek.', 'bma-ajanlatkeres' ); ?></p>
		</td>
	</tr>
	<tr>
		<th><label for="bma_needs_optional"><?php esc_html_e( 'Mire lesz szükség? – Ha rendelkezésre áll', 'bma-ajanlatkeres' ); ?></label></th>
		<td><textarea id="bma_needs_optional" name="bma_needs_optional" rows="4" class="large-text" placeholder="<?php esc_attr_e( 'Egy sor = egy tétel', 'bma-ajanlatkeres' ); ?>"><?php echo esc_textarea( (string) ( $needs_optional ?? '' ) ); ?></textarea></td>
	</tr>
	<tr>
		<th><label for="bma_needs_later"><?php esc_html_e( 'Mire lesz szükség? – Később is pótolható', 'bma-ajanlatkeres' ); ?></label></th>
		<td><textarea id="bma_needs_later" name="bma_needs_later" rows="4" class="large-text" placeholder="<?php esc_attr_e( 'Egy sor = egy tétel', 'bma-ajanlatkeres' ); ?>"><?php echo esc_textarea( (string) ( $needs_later ?? '' ) ); ?></textarea></td>
	</tr>
	<tr>
		<th><label for="bma_faq"><?php esc_html_e( 'GYIK', 'bma-ajanlatkeres' ); ?></label></th>
		<td>
			<textarea id="bma_faq" name="bma_faq" rows="10" class="large-text" placeholder="<?php esc_attr_e( "K: Kérdés?\nV: Válasz.\n\nK: Másik kérdés?\nV: Másik válasz.", 'bma-ajanlatkeres' ); ?>"><?php echo esc_textarea( (string) ( $faq ?? '' ) ); ?></textarea>
			<p class="description"><?php esc_html_e( 'Blokkok üres sorral elválasztva. Üres mezőnél a GYIK szekció nem jelenik meg.', 'bma-ajanlatkeres' ); ?></p>
		</td>
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
	<tr>
		<th><?php esc_html_e( 'Szolgáltatáselemek', 'bma-ajanlatkeres' ); ?></th>
		<td>
			<?php
			$linked_items = BMA_AJ_Service_Items::get_for_service( (int) $post->ID );
			if ( empty( $linked_items ) ) :
				?>
				<p><?php esc_html_e( 'Még nincs elem ehhez a szolgáltatáshoz.', 'bma-ajanlatkeres' ); ?></p>
			<?php else : ?>
				<ul style="margin:0;">
					<?php foreach ( $linked_items as $linked ) : ?>
						<li>
							<a href="<?php echo esc_url( admin_url( 'admin.php?page=bma-service-items&edit=' . (int) $linked->id ) ); ?>">
								<?php echo esc_html( $linked->name ); ?>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
			<p>
				<a class="button" href="<?php echo esc_url( admin_url( 'admin.php?page=bma-service-items' ) ); ?>">
					<?php esc_html_e( 'Szolgáltatáselemek kezelése', 'bma-ajanlatkeres' ); ?>
				</a>
			</p>
		</td>
	</tr>
</table>
