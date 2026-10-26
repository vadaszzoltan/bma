<?php
/**
 * Cookie consent banner.
 *
 * @package BMA_Ajanlatkeres
 */

defined( 'ABSPATH' ) || exit;

final class BMA_AJ_Cookie_Consent {

	/**
	 * Init hooks.
	 */
	public static function init(): void {
		add_action( 'wp_footer', array( self::class, 'render_banner' ), 5 );
		add_action( 'wp_enqueue_scripts', array( self::class, 'enqueue' ) );
	}

	/**
	 * Enqueue consent script.
	 */
	public static function enqueue(): void {
		wp_add_inline_script(
			'bma-aj-public',
			"(function(){var k='bma_cookie_consent';function apply(c){document.querySelectorAll('script[type=\"text/plain\"][data-cookie-category]').forEach(function(el){if(!c[el.getAttribute('data-cookie-category')]||el._loaded)return;var s=document.createElement('script');if(el.src){s.src=el.src;s.async=true;}else{s.text=el.text;}el._loaded=1;el.parentNode.insertBefore(s,el.nextSibling);});if(c.map&&window.bmaLoadContactMap){window.bmaLoadContactMap();}}
var s=localStorage.getItem(k);if(s){try{apply(JSON.parse(s));}catch(e){}}
var b=document.getElementById('bma-cookie-banner');if(!b)return;if(s){b.hidden=true;return;}b.hidden=false;
b.querySelector('.bma-cookie-accept-all').addEventListener('click',function(){var c={necessary:1,analytics:1,marketing:1,map:1};localStorage.setItem(k,JSON.stringify(c));apply(c);b.hidden=true;});
b.querySelector('.bma-cookie-necessary').addEventListener('click',function(){var c={necessary:1,analytics:0,marketing:0,map:0};localStorage.setItem(k,JSON.stringify(c));apply(c);b.hidden=true;});
})();"
		);
	}

	/**
	 * Render cookie banner HTML.
	 */
	public static function render_banner(): void {
		?>
		<div id="bma-cookie-banner" class="bma-cookie-banner" hidden role="dialog" aria-label="<?php esc_attr_e( 'Cookie hozzájárulás', 'bma-ajanlatkeres' ); ?>">
			<div class="bma-cookie-inner">
				<p><?php esc_html_e( 'Weboldalunk sütiket használ a működéshez, statisztikához és marketing célokra. Részletek: Cookie tájékoztató.', 'bma-ajanlatkeres' ); ?></p>
				<div class="bma-cookie-actions">
					<button type="button" class="bma-btn bma-cookie-necessary"><?php esc_html_e( 'Csak szükséges', 'bma-ajanlatkeres' ); ?></button>
					<button type="button" class="bma-btn bma-btn-primary bma-cookie-accept-all"><?php esc_html_e( 'Összes elfogadása', 'bma-ajanlatkeres' ); ?></button>
				</div>
			</div>
		</div>
		<?php
	}
}
