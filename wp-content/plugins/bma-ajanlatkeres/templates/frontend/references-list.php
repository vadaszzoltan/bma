<?php
/**
 * References list with filters.
 *
 * @package BMA_Ajanlatkeres
 */

defined( 'ABSPATH' ) || exit;

$filter_type    = isset( $_GET['ref_type'] ) ? sanitize_text_field( wp_unslash( $_GET['ref_type'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$filter_service = isset( $_GET['ref_service'] ) ? absint( $_GET['ref_service'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$filter_year    = isset( $_GET['ref_year'] ) ? sanitize_text_field( wp_unslash( $_GET['ref_year'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

$args = array(
	'post_type'      => BMA_AJ_Reference_CPT::POST_TYPE,
	'posts_per_page' => -1,
	'post_status'    => 'publish',
	'orderby'        => 'date',
	'order'          => 'DESC',
);

$meta_query = array();
if ( $filter_type ) {
	$meta_query[] = array(
		'key'   => '_bma_project_type',
		'value' => $filter_type,
	);
}
if ( $filter_year ) {
	$meta_query[] = array(
		'key'   => '_bma_year',
		'value' => $filter_year,
	);
}
if ( $meta_query ) {
	$args['meta_query'] = $meta_query;
}

$refs = get_posts( $args );

if ( $filter_service ) {
	$refs = array_values(
		array_filter(
			$refs,
			static function ( $ref ) use ( $filter_service ) {
				$related = get_post_meta( $ref->ID, '_bma_related_services', true );
				return is_array( $related ) && in_array( $filter_service, array_map( 'intval', $related ), true );
			}
		)
	);
}

$all_refs = get_posts(
	array(
		'post_type'      => BMA_AJ_Reference_CPT::POST_TYPE,
		'posts_per_page' => -1,
		'post_status'    => 'publish',
		'fields'         => 'ids',
	)
);

$types = array();
$years = array();
foreach ( $all_refs as $rid ) {
	$t = (string) get_post_meta( $rid, '_bma_project_type', true );
	$y = (string) get_post_meta( $rid, '_bma_year', true );
	if ( $t && ! preg_match( '/^\[/', $t ) ) {
		$types[ $t ] = true;
	}
	if ( $y && ! preg_match( '/^\[/', $y ) ) {
		$years[ $y ] = true;
	}
}
$types = array_keys( $types );
sort( $types );
$years = array_keys( $years );
rsort( $years );

$services = BMA_AJ_Service_CPT::get_active_services();
$show_filters = count( $all_refs ) >= 3;
$list_url     = ( class_exists( 'BMA_AJ_I18n' ) ? BMA_AJ_I18n::page_url( 'referenciak' ) : home_url( '/referenciak/' ) );
$svc_url      = ( class_exists( 'BMA_AJ_I18n' ) ? BMA_AJ_I18n::page_url( 'szolgaltatasok' ) : home_url( '/szolgaltatasok/' ) );
?>
<section class="bma-section bma-page-references" aria-labelledby="bma-refs-list-title">
	<div class="bma-container">
		<p class="bma-section-eyebrow"><?php esc_html_e( 'Projektek', 'bma-ajanlatkeres' ); ?></p>
		<h1 id="bma-refs-list-title"><?php esc_html_e( 'Referenciák', 'bma-ajanlatkeres' ); ?></h1>
		<p class="bma-section-intro">
			<?php esc_html_e( 'Ilyen projektet csináltunk – ilyen szolgáltatással, ilyen környezetben.', 'bma-ajanlatkeres' ); ?>
		</p>

		<?php if ( $show_filters ) : ?>
			<form class="bma-ref-filters" method="get" action="<?php echo esc_url( $list_url ); ?>">
				<label>
					<span><?php esc_html_e( 'Projekt típusa', 'bma-ajanlatkeres' ); ?></span>
					<select name="ref_type">
						<option value=""><?php esc_html_e( 'Mind', 'bma-ajanlatkeres' ); ?></option>
						<?php foreach ( $types as $t ) : ?>
							<option value="<?php echo esc_attr( $t ); ?>" <?php selected( $filter_type, $t ); ?>>
								<?php echo esc_html( class_exists( 'BMA_AJ_I18n' ) ? BMA_AJ_I18n::translate_label( $t ) : $t ); ?>
							</option>
						<?php endforeach; ?>
					</select>
				</label>
				<label>
					<span><?php esc_html_e( 'Szolgáltatás', 'bma-ajanlatkeres' ); ?></span>
					<select name="ref_service">
						<option value=""><?php esc_html_e( 'Mind', 'bma-ajanlatkeres' ); ?></option>
						<?php foreach ( $services as $svc ) : ?>
							<?php
							$svc_filter_id = class_exists( 'BMA_AJ_I18n' )
								? ( BMA_AJ_I18n::source_post_id( (int) $svc->ID ) ?: (int) $svc->ID )
								: (int) $svc->ID;
							?>
							<option value="<?php echo esc_attr( (string) $svc_filter_id ); ?>" <?php selected( $filter_service, $svc_filter_id ); ?>><?php echo esc_html( $svc->post_title ); ?></option>
						<?php endforeach; ?>
					</select>
				</label>
				<label>
					<span><?php esc_html_e( 'Év', 'bma-ajanlatkeres' ); ?></span>
					<select name="ref_year">
						<option value=""><?php esc_html_e( 'Mind', 'bma-ajanlatkeres' ); ?></option>
						<?php foreach ( $years as $y ) : ?>
							<option value="<?php echo esc_attr( $y ); ?>" <?php selected( $filter_year, $y ); ?>><?php echo esc_html( $y ); ?></option>
						<?php endforeach; ?>
					</select>
				</label>
				<button type="submit" class="bma-btn bma-btn-outline"><?php esc_html_e( 'Szűrés', 'bma-ajanlatkeres' ); ?></button>
				<?php if ( $filter_type || $filter_service || $filter_year ) : ?>
					<a class="bma-btn" href="<?php echo esc_url( $list_url ); ?>"><?php esc_html_e( 'Törlés', 'bma-ajanlatkeres' ); ?></a>
				<?php endif; ?>
			</form>
		<?php endif; ?>

		<?php if ( empty( $refs ) ) : ?>
			<p class="bma-section-intro"><?php esc_html_e( 'Nincs a szűrésnek megfelelő referencia.', 'bma-ajanlatkeres' ); ?></p>
		<?php else : ?>
			<div class="bma-ref-grid">
				<?php foreach ( $refs as $ref ) : ?>
					<?php
					$meta     = BMA_AJ_Reference_CPT::get_all_meta( (int) $ref->ID );
					$related  = BMA_AJ_Reference_CPT::get_related_services( (int) $ref->ID );
					$location = $meta['location'];
					if ( preg_match( '/^\[/', $location ) ) {
						$location = '';
					}
					$year = $meta['year'];
					if ( preg_match( '/^\[/', $year ) ) {
						$year = '';
					}
					?>
					<article class="bma-ref-tile">
						<a class="bma-ref-tile-link" href="<?php echo esc_url( get_permalink( $ref ) ); ?>">
							<div class="bma-ref-tile-media">
								<?php BMA_AJ_Frontend::render_featured_or_placeholder( (int) $ref->ID, '[REFERENCIA GALÉRIA PLACEHOLDER]' ); ?>
							</div>
							<div class="bma-ref-tile-body">
								<p class="bma-tile-eyebrow">
									<?php
									$bits = array_filter( array( $meta['project_type'], $meta['category'] ) );
									echo esc_html( $bits ? implode( ' · ', $bits ) : __( 'Referencia', 'bma-ajanlatkeres' ) );
									?>
								</p>
								<h2><?php echo esc_html( $ref->post_title ); ?></h2>
								<?php if ( $location || $year ) : ?>
									<p class="bma-ref-tile-meta">
										<?php echo esc_html( implode( ' · ', array_filter( array( $location, $year ) ) ) ); ?>
									</p>
								<?php endif; ?>
								<?php if ( $related ) : ?>
									<p class="bma-ref-tile-services">
										<?php echo esc_html( implode( ', ', wp_list_pluck( $related, 'post_title' ) ) ); ?>
									</p>
								<?php endif; ?>
								<?php if ( $ref->post_excerpt ) : ?>
									<p><?php echo esc_html( $ref->post_excerpt ); ?></p>
								<?php endif; ?>
								<span class="bma-ref-tile-cta"><?php esc_html_e( 'Projekt megtekintése', 'bma-ajanlatkeres' ); ?></span>
							</div>
						</a>
					</article>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<p class="bma-section-cta">
			<a href="<?php echo esc_url( $svc_url ); ?>" class="bma-btn bma-btn-accent bma-btn-lg">
				<?php esc_html_e( 'Ajánlatkérés indítása', 'bma-ajanlatkeres' ); ?>
			</a>
		</p>
	</div>
</section>
