<?php
/**
 * Sideload Unsplash demo images with SEO alt text.
 *
 * @package BMA_Ajanlatkeres
 */

defined( 'ABSPATH' ) || exit;

final class BMA_AJ_Media_Seeder {

	public const HERO_OPTION = 'bma_hero_image_id';

	/**
	 * Image definitions: key => meta.
	 * Keys: hero, service slugs, reference post_name prefixes.
	 *
	 * @return array<string, array{url: string, title: string, alt: string, caption: string}>
	 */
	public static function catalog(): array {
		return array(
			'hero' => array(
				'url'     => 'https://images.unsplash.com/photo-1503387762-592deb58ef4e?auto=format&fit=crop&w=1600&q=80',
				'title'   => 'Mérnöki tervrajzok – BMA Engineering',
				'alt'     => 'Építészeti tervrajzok és műszaki dokumentáció asztalon, mérnöki tervezéshez',
				'caption' => 'Műszaki tervezés és dokumentáció – Unsplash',
			),
			'tervezes' => array(
				'url'     => 'https://images.unsplash.com/photo-1581094794329-c8112a89af12?auto=format&fit=crop&w=1200&q=80',
				'title'   => 'Közmű- és épületgépészeti tervezés',
				'alt'     => 'Mérnök műszaki terveket vizsgál ipari környezetben – közmű- és épületgépészeti tervezés',
				'caption' => 'Tervezési szolgáltatás – BMA Engineering',
			),
			'engedelyeztetes' => array(
				'url'     => 'https://images.unsplash.com/photo-1450101499163-c8848c66ca85?auto=format&fit=crop&w=1200&q=80',
				'title'   => 'Építési engedélyeztetés dokumentáció',
				'alt'     => 'Szerződés és engedélyezési dokumentumok aláírása – építési engedélyeztetési folyamat',
				'caption' => 'Engedélyeztetés – BMA Engineering',
			),
			'muszaki-dokumentacio' => array(
				'url'     => 'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?auto=format&fit=crop&w=1200&q=80',
				'title'   => 'Műszaki dokumentáció készítése',
				'alt'     => 'Laptopon nyitott műszaki leírás és projekt dokumentáció – műszaki dokumentációs szolgáltatás',
				'caption' => 'Műszaki dokumentáció – BMA Engineering',
			),
			'kozmu-koordinacio' => array(
				'url'     => 'https://images.unsplash.com/photo-1621905251189-08b45d6a269e?auto=format&fit=crop&w=1200&q=80',
				'title'   => 'Közmű csatlakozás koordináció',
				'alt'     => 'Elektromos kapcsolószekrény és közmű infrastruktúra – víz, gáz és áram csatlakozás koordinálása',
				'caption' => 'Közmű koordináció – BMA Engineering',
			),
			'csaladi-haz-bovites-kozmutervek' => array(
				'url'     => 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1200&q=80',
				'title'   => 'Családi ház bővítés referencia',
				'alt'     => 'Modern családi ház külső homlokzata – lakossági bővítés közműtervezési referencia',
				'caption' => 'Családi ház bővítés – közműtervek',
			),
			'irodahaz-felujitas-muszaki-dokumentacio' => array(
				'url'     => 'https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&w=1200&q=80',
				'title'   => 'Irodaház felújítás referencia',
				'alt'     => 'Világos modern irodai belső tér – üzleti ingatlan felújítás műszaki dokumentációja',
				'caption' => 'Irodaház felújítás – műszaki dokumentáció',
			),
			'uj-epites-teljes-engedelyeztetesi-csomag' => array(
				'url'     => 'https://images.unsplash.com/photo-1541888946425-d81bb19240f5?auto=format&fit=crop&w=1200&q=80',
				'title'   => 'Új építés engedélyeztetés referencia',
				'alt'     => 'Építkezési helyszín daruval és állványzattal – új lakóépület engedélyeztetési projekt',
				'caption' => 'Új építés – engedélyeztetési csomag',
			),
			'ipari-csarnok-kozmu-koordinacio' => array(
				'url'     => 'https://images.unsplash.com/photo-1565793298595-6a879b1d9492?auto=format&fit=crop&w=1200&q=80',
				'title'   => 'Ipari csarnok közmű koordináció',
				'alt'     => 'Ipari raktárcsarnok belseje – ipari beruházás közmű csatlakozás koordinációja',
				'caption' => 'Ipari csarnok – közmű koordináció',
			),
		);
	}

	/**
	 * Sideload all catalog images and assign to posts / hero.
	 *
	 * @return array<string, int> key => attachment ID
	 */
	public static function run(): array {
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		$uploaded = array();

		foreach ( self::catalog() as $key => $meta ) {
			$attachment_id = self::sideload( $meta );
			if ( ! $attachment_id ) {
				continue;
			}
			$uploaded[ $key ] = $attachment_id;

			if ( 'hero' === $key ) {
				update_option( self::HERO_OPTION, $attachment_id );
				continue;
			}

			$post = get_page_by_path( $key, OBJECT, array( BMA_AJ_Service_CPT::POST_TYPE, BMA_AJ_Reference_CPT::POST_TYPE ) );
			if ( ! $post ) {
				$posts = get_posts(
					array(
						'name'           => $key,
						'post_type'      => array( BMA_AJ_Service_CPT::POST_TYPE, BMA_AJ_Reference_CPT::POST_TYPE ),
						'posts_per_page' => 1,
						'post_status'    => 'any',
					)
				);
				$post = $posts[0] ?? null;
			}

			if ( $post ) {
				set_post_thumbnail( (int) $post->ID, $attachment_id );
				update_post_meta( (int) $post->ID, '_bma_use_placeholder', '0' );
			}
		}

		update_option( 'bma_aj_media_seeded', 1 );
		return $uploaded;
	}

	/**
	 * Download and create media attachment.
	 *
	 * @param array{url: string, title: string, alt: string, caption: string} $meta Image meta.
	 */
	private static function sideload( array $meta ): int {
		$tmp = download_url( $meta['url'], 60 );
		if ( is_wp_error( $tmp ) ) {
			return 0;
		}

		$filename = sanitize_file_name( sanitize_title( $meta['title'] ) ) . '.jpg';
		$file     = array(
			'name'     => $filename,
			'tmp_name' => $tmp,
		);

		$attachment_id = media_handle_sideload( $file, 0, $meta['caption'] );
		if ( is_wp_error( $attachment_id ) ) {
			@unlink( $tmp ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
			return 0;
		}

		wp_update_post(
			array(
				'ID'         => $attachment_id,
				'post_title' => $meta['title'],
				'post_excerpt' => $meta['caption'],
			)
		);
		update_post_meta( $attachment_id, '_wp_attachment_image_alt', $meta['alt'] );

		return (int) $attachment_id;
	}

	/**
	 * Hero image HTML or null.
	 *
	 * @param string $size Image size.
	 */
	public static function get_hero_html( string $size = 'large' ): string {
		$id = absint( get_option( self::HERO_OPTION, 0 ) );
		if ( ! $id ) {
			return '';
		}

		$alt = get_post_meta( $id, '_wp_attachment_image_alt', true );
		if ( ! $alt ) {
			$alt = __( 'BMA Engineering mérnöki tervezés', 'bma-ajanlatkeres' );
		}

		return wp_get_attachment_image(
			$id,
			$size,
			false,
			array(
				'class'    => 'bma-hero-image',
				'alt'      => $alt,
				'loading'  => 'eager',
				'decoding' => 'async',
			)
		);
	}
}
