<?php
defined( 'ABSPATH' ) || exit;
global $wpdb;
$table = $wpdb->prefix . 'bma_email_templates';

if ( isset( $_POST['bma_email_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['bma_email_nonce'] ) ), 'bma_email_save' ) ) {
	foreach ( (array) ( $_POST['template'] ?? array() ) as $id => $data ) {
		$wpdb->update(
			$table,
			array(
				'subject' => sanitize_text_field( wp_unslash( $data['subject'] ?? '' ) ),
				'body'    => sanitize_textarea_field( wp_unslash( $data['body'] ?? '' ) ),
			),
			array( 'id' => absint( $id ) )
		);
	}
	echo '<div class="notice notice-success"><p>' . esc_html__( 'Mentve.', 'bma-ajanlatkeres' ) . '</p></div>';
}

$templates = $wpdb->get_results( "SELECT * FROM {$table} ORDER BY id ASC" );
?>
<div class="wrap">
	<h1><?php esc_html_e( 'E-mail sablonok', 'bma-ajanlatkeres' ); ?></h1>
	<p><?php esc_html_e( 'Elérhető változók: {request_number}, {client_name}, {client_email}, {client_phone}, {client_data}, {services}, {status}, {portal_link}, {quote_amount}, {quote_valid_until}', 'bma-ajanlatkeres' ); ?></p>
	<form method="post">
		<?php wp_nonce_field( 'bma_email_save', 'bma_email_nonce' ); ?>
		<?php foreach ( $templates as $tpl ) : ?>
			<div class="bma-admin-panel" style="margin-bottom:20px;padding:15px;background:#fff;border:1px solid #ccd0d4;">
				<h2><?php echo esc_html( $tpl->name ); ?> <code><?php echo esc_html( $tpl->slug ); ?></code></h2>
				<p><label><?php esc_html_e( 'Tárgy', 'bma-ajanlatkeres' ); ?><br>
				<input type="text" name="template[<?php echo esc_attr( $tpl->id ); ?>][subject]" value="<?php echo esc_attr( $tpl->subject ); ?>" class="large-text"></label></p>
				<p><label><?php esc_html_e( 'Törzs', 'bma-ajanlatkeres' ); ?><br>
				<textarea name="template[<?php echo esc_attr( $tpl->id ); ?>][body]" rows="8" class="large-text"><?php echo esc_textarea( $tpl->body ); ?></textarea></label></p>
			</div>
		<?php endforeach; ?>
		<p><button type="submit" class="button button-primary"><?php esc_html_e( 'Mentés', 'bma-ajanlatkeres' ); ?></button></p>
	</form>
</div>
