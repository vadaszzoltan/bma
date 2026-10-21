<?php
/**
 * References list – markup/05-referenciak.html
 *
 * @package BMA_Ajanlatkeres
 */

defined( 'ABSPATH' ) || exit;

$filter_type = isset( $_GET['tipus'] ) ? sanitize_title( wp_unslash( $_GET['tipus'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$filter_svc  = isset( $_GET['szolgaltatas'] ) ? absint( $_GET['szolgaltatas'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
if ( $filter_svc && class_exists( 'BMA_AJ_I18n' ) ) {
	$filter_svc = BMA_AJ_I18n::source_post_id( $filter_svc ) ?: $filter_svc;
}

$list_url    = function_exists( 'bma_page_url' ) ? bma_page_url( 'referenciak' ) : home_url( '/referenciak/' );
$home_url    = function_exists( 'bma_page_url' ) ? bma_page_url( 'fooldal' ) : home_url( '/' );
$svc_url     = function_exists( 'bma_page_url' ) ? bma_page_url( 'szolgaltatasok' ) : home_url( '/szolgaltatasok/' );
$contact_url = function_exists( 'bma_page_url' ) ? bma_page_url( 'kapcsolat' ) : home_url( '/kapcsolat/' );

$filter_href = static function ( string $tipus, int $svc ) use ( $list_url ): string {
	$args = array();
	if ( '' !== $tipus ) {
		$args['tipus'] = $tipus;
	}
	if ( $svc > 0 ) {
		$args['szolgaltatas'] = $svc;
	}
	return $args ? add_query_arg( $args, $list_url ) : $list_url;
};

$type_terms = get_terms(
	array(
		'taxonomy'   => BMA_AJ_Reference_CPT::TAXONOMY,
		'hide_empty' => true,
	)
);
if ( is_wp_error( $type_terms ) ) {
	$type_terms = array();
}

$query_args = array(
	'post_type'      => BMA_AJ_Reference_CPT::POST_TYPE,
	'posts_per_page' => -1,
	'post_status'    => 'publish',
	'orderby'        => 'date',
	'order'          => 'DESC',
);

if ( $filter_type ) {
	$query_args['tax_query'] = array(
		array(
			'taxonomy' => BMA_AJ_Reference_CPT::TAXONOMY,
			'field'    => 'slug',
			'terms'    => $filter_type,
		),
	);
}

$type_filtered = get_posts( $query_args );

// Service chips: related services appearing in the type-filtered set.
$service_ids_in_set = array();
foreach ( $type_filtered as $ref ) {
	$related_ids = get_post_meta( (int) $ref->ID, '_bma_related_services', true );
	if ( ! is_array( $related_ids ) ) {
		continue;
	}
	foreach ( $related_ids as $sid ) {
		$sid = absint( $sid );
		if ( ! $sid ) {
			continue;
		}
		if ( class_exists( 'BMA_AJ_I18n' ) ) {
			$sid = BMA_AJ_I18n::source_post_id( $sid ) ?: $sid;
		}
		$service_ids_in_set[ $sid ] = true;
	}
}

$service_chips = array();
if ( $service_ids_in_set ) {
	$chip_ids = array_keys( $service_ids_in_set );
	$display_ids = $chip_ids;
	if ( class_exists( 'BMA_AJ_I18n' ) && function_exists( 'pll_get_post' ) ) {
		$lang = BMA_AJ_I18n::current_lang();
		$mapped = array();
		foreach ( $chip_ids as $sid ) {
			$tr = pll_get_post( $sid, $lang );
			$mapped[] = $tr ? (int) $tr : $sid;
		}
		$display_ids = array_values( array_unique( $mapped ) );
	}
	$service_chips = get_posts(
		array(
			'post_type'      => BMA_AJ_Service_CPT::POST_TYPE,
			'post__in'       => $display_ids,
			'posts_per_page' => -1,
			'post_status'    => 'publish',
			'orderby'        => 'title',
			'order'          => 'ASC',
			'lang'           => '',
		)
	);
}

$refs = $type_filtered;
if ( $filter_svc ) {
	$refs = array_values(
		array_filter(
			$refs,
			static function ( $ref ) use ( $filter_svc ) {
				$related = get_post_meta( (int) $ref->ID, '_bma_related_services', true );
				if ( ! is_array( $related ) ) {
					return false;
				}
				$normalized = array();
				foreach ( $related as $sid ) {
					$sid = absint( $sid );
					if ( ! $sid ) {
						continue;
					}
					if ( class_exists( 'BMA_AJ_I18n' ) ) {
						$sid = BMA_AJ_I18n::source_post_id( $sid ) ?: $sid;
					}
					$normalized[] = $sid;
				}
				return in_array( $filter_svc, $normalized, true );
			}
		)
	);
}

$count       = count( $refs );
$count_label = ( 1 === $count )
	? __( '1 projekt', 'bma-ajanlatkeres' )
	: sprintf(
		/* translators: %d: project count */
		__( '%d projekt', 'bma-ajanlatkeres' ),
		$count
	);
?>
<section class="pagehead">
	<div class="pagehead-inner">
		<p class="crumb">
			<a href="<?php echo esc_url( $home_url ); ?>"><?php esc_html_e( 'Főoldal', 'bma-ajanlatkeres' ); ?></a>
			/ <?php esc_html_e( 'Referenciák', 'bma-ajanlatkeres' ); ?>
		</p>
		<p class="eyebrow eyebrow--light"><?php BMA_AJ_Content::e( 'refs.eyebrow' ); ?></p>
		<h1><?php BMA_AJ_Content::e( 'refs.title' ); ?></h1>
		<p class="pagehead-lead">
			<?php BMA_AJ_Content::e( 'refs.lead' ); ?>
		</p>
	</div>
</section>

<div class="appbody">
	<div class="bma-container">
		<div class="filterbar">
			<div class="filtergroup">
				<span><?php esc_html_e( 'Típus', 'bma-ajanlatkeres' ); ?></span>
				<a
					href="<?php echo esc_url( $filter_href( '', $filter_svc ) ); ?>"
					class="fchip<?php echo '' === $filter_type ? ' is-on' : ''; ?>"
					<?php echo '' === $filter_type ? ' aria-current="true"' : ''; ?>
				><?php esc_html_e( 'Összes', 'bma-ajanlatkeres' ); ?></a>
				<?php foreach ( $type_terms as $term ) : ?>
					<a
						href="<?php echo esc_url( $filter_href( $term->slug, $filter_svc ) ); ?>"
						class="fchip<?php echo $filter_type === $term->slug ? ' is-on' : ''; ?>"
						<?php echo $filter_type === $term->slug ? ' aria-current="true"' : ''; ?>
					><?php echo esc_html( $term->name ); ?></a>
				<?php endforeach; ?>
			</div>
			<div class="filtergroup">
				<span><?php esc_html_e( 'Szolgáltatás', 'bma-ajanlatkeres' ); ?></span>
				<a
					href="<?php echo esc_url( $filter_href( $filter_type, 0 ) ); ?>"
					class="fchip<?php echo ! $filter_svc ? ' is-on' : ''; ?>"
					<?php echo ! $filter_svc ? ' aria-current="true"' : ''; ?>
				><?php esc_html_e( 'Összes', 'bma-ajanlatkeres' ); ?></a>
				<?php foreach ( $service_chips as $svc ) : ?>
					<?php
					$chip_id = (int) $svc->ID;
					if ( class_exists( 'BMA_AJ_I18n' ) ) {
						$chip_id = BMA_AJ_I18n::source_post_id( $chip_id ) ?: $chip_id;
					}
					?>
					<a
						href="<?php echo esc_url( $filter_href( $filter_type, $chip_id ) ); ?>"
						class="fchip<?php echo $filter_svc === $chip_id ? ' is-on' : ''; ?>"
						<?php echo $filter_svc === $chip_id ? ' aria-current="true"' : ''; ?>
					><?php echo esc_html( $svc->post_title ); ?></a>
				<?php endforeach; ?>
			</div>
			<span class="filtercount"><?php echo esc_html( $count_label ); ?></span>
		</div>

		<div class="refs refs--page">
			<?php if ( empty( $refs ) ) : ?>
				<div class="ref-empty">
					<?php esc_html_e( 'Ezzel a szűréssel nincs találat. Próbáljon másik típust vagy szolgáltatást, vagy állítsa vissza a szűrőt „Összes”-re.', 'bma-ajanlatkeres' ); ?>
				</div>
			<?php else : ?>
				<?php foreach ( $refs as $ref ) : ?>
					<?php
					$meta    = BMA_AJ_Reference_CPT::get_all_meta( (int) $ref->ID );
					$related = BMA_AJ_Reference_CPT::get_related_services( (int) $ref->ID );
					$kicker_bits = array_filter( array( $meta['project_type'], $meta['category'] ) );
					$kicker      = $kicker_bits ? implode( ' · ', $kicker_bits ) : __( 'Referencia', 'bma-ajanlatkeres' );
					$thumb       = get_the_post_thumbnail_url( $ref, 'large' );
					$lead        = $ref->post_excerpt ?: wp_trim_words( (string) $meta['project_description'], 28 );
					?>
					<article class="ref">
						<div class="ref-img"<?php echo $thumb ? ' style="background-image:url(' . esc_url( $thumb ) . ');background-size:cover;background-position:center"' : ''; ?>>
							<?php if ( ! $thumb ) : ?>
								<span><?php esc_html_e( 'Projekt fotó', 'bma-ajanlatkeres' ); ?></span>
							<?php endif; ?>
						</div>
						<div class="ref-body">
							<p class="eyebrow" style="margin:0"><?php echo esc_html( $kicker ); ?></p>
							<h3><?php echo esc_html( $ref->post_title ); ?></h3>
							<?php if ( $related ) : ?>
								<div class="ref-tags">
									<?php foreach ( $related as $svc ) : ?>
										<span class="ref-tag"><?php echo esc_html( $svc->post_title ); ?></span>
									<?php endforeach; ?>
								</div>
							<?php endif; ?>
							<?php if ( $lead ) : ?>
								<p class="small muted" style="margin:0"><?php echo esc_html( $lead ); ?></p>
							<?php endif; ?>
							<div class="ref-link">
								<a href="<?php echo esc_url( get_permalink( $ref ) ); ?>"><?php esc_html_e( 'Projekt megtekintése →', 'bma-ajanlatkeres' ); ?></a>
							</div>
						</div>
					</article>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>

		<p class="datanote">
			<?php esc_html_e( 'A lista a referencia-adatbázisból töltődik; itt a jelenlegi oldalon szereplő projektek láthatók. Új projekt felvételekor automatikusan megjelenik a szűrőkben is.', 'bma-ajanlatkeres' ); ?>
		</p>
	</div>
</div>

<section class="closing">
	<div class="closing-inner">
		<div>
			<p class="eyebrow eyebrow--light"><?php BMA_AJ_Content::e( 'refs.closing_eyebrow' ); ?></p>
			<h2 class="h2"><?php BMA_AJ_Content::e( 'refs.closing_title' ); ?></h2>
			<p><?php esc_html_e( 'Válassza ki a szükséges szolgáltatásokat, adja meg a projekt alapadatait, és néhány napon belül személyre szabott ajánlatot küldünk.', 'bma-ajanlatkeres' ); ?></p>
		</div>
		<div class="closing-actions">
			<a href="<?php echo esc_url( $svc_url ); ?>" class="btn btn--primary"><?php esc_html_e( 'Ajánlatkérés indítása', 'bma-ajanlatkeres' ); ?></a>
			<a href="<?php echo esc_url( $contact_url ); ?>" class="btn btn--ghost-light"><?php esc_html_e( 'Kapcsolat', 'bma-ajanlatkeres' ); ?></a>
		</div>
	</div>
</section>
