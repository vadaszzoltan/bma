<?php
/**
 * Legal page content (privacy, cookies, terms).
 *
 * @package BMA_Ajanlatkeres
 */

defined( 'ABSPATH' ) || exit;

final class BMA_AJ_Legal_Content {

	/**
	 * Render privacy policy HTML.
	 */
	public static function privacy_html(): string {
		$company = BMA_AJ_Company::get( 'company_name' );
		$addr    = BMA_AJ_Company::get( 'contact_address' );
		$email   = BMA_AJ_Company::get( 'contact_email' );
		$phone   = BMA_AJ_Company::get( 'contact_phone' );
		$cui     = BMA_AJ_Company::get( 'cui' );
		$cookie  = home_url( '/cookie-tajekoztato/' );

		ob_start();
		?>
		<article class="bma-legal-doc">
			<p class="bma-section-eyebrow"><?php esc_html_e( 'Jogi információ', 'bma-ajanlatkeres' ); ?></p>
			<h1><?php esc_html_e( 'Adatvédelmi tájékoztató', 'bma-ajanlatkeres' ); ?></h1>
			<p class="bma-legal-updated"><?php esc_html_e( 'Utolsó frissítés: 2026. október', 'bma-ajanlatkeres' ); ?></p>
			<p><?php echo esc_html( sprintf( /* translators: %s company */ __( 'A %s fontosnak tartja a személyes adatok védelmét és az adatok átlátható kezelését.', 'bma-ajanlatkeres' ), $company ) ); ?></p>
			<p><?php esc_html_e( 'Ez a tájékoztató bemutatja, hogy milyen személyes adatokat kezelünk a weboldal használata, kapcsolatfelvétel, ajánlatkérés és ügyfélfiók használata során, milyen célból kezeljük ezeket az adatokat, valamint milyen jogok illetik meg az érintetteket.', 'bma-ajanlatkeres' ); ?></p>

			<h2><?php esc_html_e( '1. Az adatkezelő', 'bma-ajanlatkeres' ); ?></h2>
			<p><strong><?php echo esc_html( $company ); ?></strong><br>
			<?php echo nl2br( esc_html( $addr ) ); ?><br>
			<?php if ( $cui ) : ?>CUI: <?php echo esc_html( $cui ); ?><br><?php endif; ?>
			E-mail: <a href="<?php echo esc_url( 'mailto:' . $email ); ?>"><?php echo esc_html( $email ); ?></a><br>
			Telefon: <a href="<?php echo esc_url( BMA_AJ_Company::phone_href( $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a></p>

			<h2><?php esc_html_e( '2. Milyen adatokat kezelünk?', 'bma-ajanlatkeres' ); ?></h2>
			<h3><?php esc_html_e( 'Kapcsolatfelvétel esetén', 'bma-ajanlatkeres' ); ?></h3>
			<ul><li><?php esc_html_e( 'név, e-mail, telefonszám (ha megadja), téma, üzenet, beküldés időpontja', 'bma-ajanlatkeres' ); ?></li></ul>
			<h3><?php esc_html_e( 'Ajánlatkérés esetén', 'bma-ajanlatkeres' ); ?></h3>
			<ul><li><?php esc_html_e( 'név, e-mail, telefon, cégnév és adószám (ha megadja), számlázási/projektadatok, kiválasztott szolgáltatások, feltöltött dokumentumok, kapcsolódó kommunikáció', 'bma-ajanlatkeres' ); ?></li></ul>
			<h3><?php esc_html_e( 'Ügyfélfiók használata esetén', 'bma-ajanlatkeres' ); ?></h3>
			<ul><li><?php esc_html_e( 'fiókazonosító adatok, e-mail, ajánlatkérések, ajánlatok, státuszok, feltöltött és letöltött dokumentumok, technikai használati adatok', 'bma-ajanlatkeres' ); ?></li></ul>
			<h3><?php esc_html_e( 'AI chat asszisztens használata esetén', 'bma-ajanlatkeres' ); ?></h3>
			<ul><li><?php esc_html_e( 'a chatbe írt kérdések és a kapott válaszok, a beszélgetés azonosítója és időpontja, az oldal, amelyen a chatet használta, valamint az IP-cím (visszaélések és túlterhelés megelőzésére)', 'bma-ajanlatkeres' ); ?></li></ul>
			<h3><?php esc_html_e( 'Technikai használat során', 'bma-ajanlatkeres' ); ?></h3>
			<ul><li><?php esc_html_e( 'IP-cím, böngésző és eszköz típusa, meglátogatott oldalak, technikai naplóadatok, cookie-hozzájárulási beállítások', 'bma-ajanlatkeres' ); ?></li></ul>

			<h2><?php esc_html_e( '3. Adatkezelés célja és jogalapja', 'bma-ajanlatkeres' ); ?></h2>
			<ul>
				<li><strong><?php esc_html_e( 'Kapcsolatfelvétel:', 'bma-ajanlatkeres' ); ?></strong> <?php esc_html_e( 'megkeresések megválaszolása.', 'bma-ajanlatkeres' ); ?></li>
				<li><strong><?php esc_html_e( 'Ajánlatkérés:', 'bma-ajanlatkeres' ); ?></strong> <?php esc_html_e( 'igényfelmérés, ajánlatkészítés, szerződés előkészítése.', 'bma-ajanlatkeres' ); ?></li>
				<li><strong><?php esc_html_e( 'Szerződés teljesítése:', 'bma-ajanlatkeres' ); ?></strong> <?php esc_html_e( 'szolgáltatás, dokumentumkezelés, kapcsolattartás.', 'bma-ajanlatkeres' ); ?></li>
				<li><strong><?php esc_html_e( 'Jogi kötelezettségek:', 'bma-ajanlatkeres' ); ?></strong> <?php esc_html_e( 'számviteli és jogszabályi kötelezettségek.', 'bma-ajanlatkeres' ); ?></li>
				<li><strong><?php esc_html_e( 'Weboldal biztonsága:', 'bma-ajanlatkeres' ); ?></strong> <?php esc_html_e( 'visszaélések megelőzése.', 'bma-ajanlatkeres' ); ?></li>
				<li><strong><?php esc_html_e( 'AI chat asszisztens:', 'bma-ajanlatkeres' ); ?></strong> <?php esc_html_e( 'kérdések megválaszolása a weboldal tartalma alapján, a szolgáltatás minőségének ellenőrzése (jogos érdek; a chat használata önkéntes).', 'bma-ajanlatkeres' ); ?></li>
				<li><strong><?php esc_html_e( 'Analitika:', 'bma-ajanlatkeres' ); ?></strong> <?php esc_html_e( 'csak megfelelő hozzájárulás esetén (GA4 / GTM).', 'bma-ajanlatkeres' ); ?></li>
			</ul>

			<h2><?php esc_html_e( '4. Feltöltött dokumentumok', 'bma-ajanlatkeres' ); ?></h2>
			<p><?php esc_html_e( 'A feltöltött dokumentumokat kizárólag az adott ajánlatkérés vagy szolgáltatás kezelése céljából használjuk. Kérjük, csak a projekthez szükséges adatokat töltse fel.', 'bma-ajanlatkeres' ); ?></p>

			<h2><?php esc_html_e( '4/A. AI chat asszisztens', 'bma-ajanlatkeres' ); ?></h2>
			<p><?php esc_html_e( 'A weboldalon elérhető chat asszisztens válaszait mesterséges intelligencia állítja elő a weboldal tartalma alapján. A válaszok tájékoztató jellegűek: ajánlat, ár vagy szerződés a chaten keresztül nem jön létre, ehhez az ajánlatkérő űrlapot vagy a megadott elérhetőségeket kell használni.', 'bma-ajanlatkeres' ); ?></p>
			<p><?php esc_html_e( 'Kérjük, a chatbe ne írjon érzékeny személyes adatot (például egészségügyi adatot, személyi számot vagy bankkártyaadatot). A beszélgetéseket a szolgáltatás minőségének ellenőrzéséhez és fejlesztéséhez őrizzük meg, legfeljebb az ehhez szükséges ideig; törlésüket a fenti e-mail-címen kérheti.', 'bma-ajanlatkeres' ); ?></p>

			<h2><?php esc_html_e( '5. Adattovábbítás', 'bma-ajanlatkeres' ); ?></h2>
			<p><?php esc_html_e( 'Szükség esetén hozzáférhetnek: tárhelyszolgáltató, e-mail-szolgáltató, weboldal-karbantartó, analitikai szolgáltatók (hozzájárulás esetén), könyvelési vagy jogi szolgáltatók, valamint a választott fizetési szolgáltató (pl. Stripe), ha online fizetést használ.', 'bma-ajanlatkeres' ); ?></p>

			<p><?php esc_html_e( 'A chat asszisztens üzeneteit a válasz előállításához a Google (Gemini API) mesterségesintelligencia-szolgáltatás dolgozza fel adatfeldolgozóként.', 'bma-ajanlatkeres' ); ?></p>

			<h2><?php esc_html_e( '6. EGT-n kívüli adattovábbítás', 'bma-ajanlatkeres' ); ?></h2>
			<p><?php esc_html_e( 'Ha külső szolgáltató miatt ilyen történik, csak az alkalmazandó adatvédelmi szabályoknak megfelelő módon.', 'bma-ajanlatkeres' ); ?></p>

			<p><?php esc_html_e( 'A Google a chat üzeneteit az EGT-n kívül (például az Egyesült Államokban) is feldolgozhatja; ez az EU–USA adatvédelmi keretrendszer, illetve az Európai Bizottság által elfogadott általános szerződési feltételek alapján történik.', 'bma-ajanlatkeres' ); ?></p>

			<h2><?php esc_html_e( '7. Megőrzési idő', 'bma-ajanlatkeres' ); ?></h2>
			<p><?php esc_html_e( 'Az adatokat csak az adatkezelési célhoz szükséges ideig őrizzük meg.', 'bma-ajanlatkeres' ); ?></p>

			<h2><?php esc_html_e( '8. Érintetti jogok', 'bma-ajanlatkeres' ); ?></h2>
			<p><?php esc_html_e( 'Jogosult lehet: tájékoztatás, hozzáférés, helyesbítés, törlés, korlátozás, tiltakozás, adathordozhatóság, hozzájárulás visszavonása.', 'bma-ajanlatkeres' ); ?></p>
			<p><?php esc_html_e( 'Kapcsolat:', 'bma-ajanlatkeres' ); ?> <a href="<?php echo esc_url( 'mailto:' . $email ); ?>"><?php echo esc_html( $email ); ?></a></p>

			<h2><?php esc_html_e( '9. Panasz', 'bma-ajanlatkeres' ); ?></h2>
			<p><?php esc_html_e( 'Az érintett jogosult panaszt benyújtani az illetékes adatvédelmi felügyeleti hatósághoz.', 'bma-ajanlatkeres' ); ?></p>

			<h2><?php esc_html_e( '10. Adatbiztonság', 'bma-ajanlatkeres' ); ?></h2>
			<ul>
				<li>HTTPS</li>
				<li><?php esc_html_e( 'Jogosultság alapú hozzáférés', 'bma-ajanlatkeres' ); ?></li>
				<li><?php esc_html_e( 'Biztonságos dokumentumkezelés', 'bma-ajanlatkeres' ); ?></li>
				<li><?php esc_html_e( 'Hozzáférés-védelem és mentések', 'bma-ajanlatkeres' ); ?></li>
			</ul>

			<h2><?php esc_html_e( '11. Cookie-k', 'bma-ajanlatkeres' ); ?></h2>
			<p><?php esc_html_e( 'Analitikai cookie-k csak hozzájárulás után. Részletek:', 'bma-ajanlatkeres' ); ?> <a href="<?php echo esc_url( $cookie ); ?>"><?php esc_html_e( 'Cookie tájékoztató', 'bma-ajanlatkeres' ); ?></a></p>

			<h2><?php esc_html_e( '12. Módosítás', 'bma-ajanlatkeres' ); ?></h2>
			<p><?php esc_html_e( 'Az aktuális változat mindig ezen az oldalon érhető el. A szöveget publikálás előtt célszerű jogi/adatvédelmi szakértővel ellenőriztetni.', 'bma-ajanlatkeres' ); ?></p>
		</article>
		<?php
		return (string) ob_get_clean();
	}

	/**
	 * Cookie policy HTML (aligned with current implementation).
	 */
	public static function cookie_html(): string {
		$privacy = home_url( '/adatvedelem/' );
		ob_start();
		?>
		<article class="bma-legal-doc">
			<p class="bma-section-eyebrow"><?php esc_html_e( 'Jogi információ', 'bma-ajanlatkeres' ); ?></p>
			<h1><?php esc_html_e( 'Cookie tájékoztató', 'bma-ajanlatkeres' ); ?></h1>
			<p class="bma-legal-updated"><?php esc_html_e( 'Utolsó frissítés: 2026. augusztus', 'bma-ajanlatkeres' ); ?></p>
			<p><?php esc_html_e( 'Ez a tájékoztató bemutatja, milyen sütiket (cookie-kat) és hasonló technológiákat használ a BMA Engineering weboldala.', 'bma-ajanlatkeres' ); ?></p>

			<h2><?php esc_html_e( '1. Mi az a cookie?', 'bma-ajanlatkeres' ); ?></h2>
			<p><?php esc_html_e( 'A cookie kis adatfájl, amelyet a böngésző tárol. Segíti a weboldal működését, a beállítások megjegyzését és – hozzájárulás esetén – a látogatottsági statisztikát.', 'bma-ajanlatkeres' ); ?></p>

			<h2><?php esc_html_e( '2. Cookie kategóriák ezen az oldalon', 'bma-ajanlatkeres' ); ?></h2>
			<h3><?php esc_html_e( 'Szükséges', 'bma-ajanlatkeres' ); ?></h3>
			<ul>
				<li><?php esc_html_e( 'Munkamenet és biztonság (WordPress, bejelentkezés)', 'bma-ajanlatkeres' ); ?></li>
				<li><?php esc_html_e( 'Ajánlatkérési kosár azonosító (bma_cart_id) – a kiválasztott szolgáltatások megőrzéséhez', 'bma-ajanlatkeres' ); ?></li>
				<li><?php esc_html_e( 'Cookie-hozzájárulás tárolása (böngésző localStorage: bma_cookie_consent)', 'bma-ajanlatkeres' ); ?></li>
			</ul>
			<p><?php esc_html_e( 'Ezek a működéshez szükségesek, és a „Csak szükséges” választással is aktívak maradnak.', 'bma-ajanlatkeres' ); ?></p>

			<h3><?php esc_html_e( 'Analitika', 'bma-ajanlatkeres' ); ?></h3>
			<ul>
				<li><?php esc_html_e( 'Google Analytics 4 és/vagy Google Tag Manager – csak ha az admin beállította, és Ön elfogadja az analitikai cookie-kat', 'bma-ajanlatkeres' ); ?></li>
			</ul>
			<p><?php esc_html_e( 'Hozzájárulás nélkül ezek a scriptek nem indulnak el.', 'bma-ajanlatkeres' ); ?></p>

			<h3><?php esc_html_e( 'Marketing', 'bma-ajanlatkeres' ); ?></h3>
			<p><?php esc_html_e( 'Jelenleg nincs külön marketing cookie integráció. Az „Összes elfogadása” a jövőbeli marketing kategóriát is engedélyezi, ha később bekapcsolásra kerül.', 'bma-ajanlatkeres' ); ?></p>

			<h2><?php esc_html_e( '3. Térkép', 'bma-ajanlatkeres' ); ?></h2>
			<p><?php esc_html_e( 'A kapcsolat oldalon OpenStreetMap csempéket jelenítünk meg (Leaflet). A térkép csak hozzájárulás után vagy a „Térkép betöltése” gombra kattintva töltődik be. Az „Útvonaltervezés” az OpenStreetMap irányító oldalára vezet (külső oldal).', 'bma-ajanlatkeres' ); ?></p>

			<h2><?php esc_html_e( '4. Hozzájárulás kezelése', 'bma-ajanlatkeres' ); ?></h2>
			<p><?php esc_html_e( 'A lábléc feletti cookie sávon választhat: „Csak szükséges” vagy „Összes elfogadása”. A beállítás a böngészőben marad; törléséhez törölje az oldal adatait / localStorage-t, majd töltse újra az oldalt.', 'bma-ajanlatkeres' ); ?></p>

			<h2><?php esc_html_e( '5. Kapcsolódó dokumentumok', 'bma-ajanlatkeres' ); ?></h2>
			<p><a href="<?php echo esc_url( $privacy ); ?>"><?php esc_html_e( 'Adatvédelmi tájékoztató', 'bma-ajanlatkeres' ); ?></a></p>
		</article>
		<?php
		return (string) ob_get_clean();
	}

	/**
	 * Terms of use HTML.
	 */
	public static function terms_html(): string {
		$company = BMA_AJ_Company::get( 'company_name' );
		$addr    = BMA_AJ_Company::get( 'contact_address' );
		$email   = BMA_AJ_Company::get( 'contact_email' );
		$phone   = BMA_AJ_Company::get( 'contact_phone' );
		$cui     = BMA_AJ_Company::get( 'cui' );
		$privacy = home_url( '/adatvedelem/' );

		ob_start();
		?>
		<article class="bma-legal-doc">
			<p class="bma-section-eyebrow"><?php esc_html_e( 'Jogi információ', 'bma-ajanlatkeres' ); ?></p>
			<h1><?php esc_html_e( 'Általános felhasználási feltételek', 'bma-ajanlatkeres' ); ?></h1>
			<p class="bma-legal-updated"><?php esc_html_e( 'Utolsó frissítés: 2026. augusztus', 'bma-ajanlatkeres' ); ?></p>
			<p><?php esc_html_e( 'A jelen feltételek a weboldal használatára, a kapcsolatfelvételre és az ajánlatkérésre vonatkoznak.', 'bma-ajanlatkeres' ); ?></p>

			<h2><?php esc_html_e( '1. Szolgáltató', 'bma-ajanlatkeres' ); ?></h2>
			<p><strong><?php echo esc_html( $company ); ?></strong><br>
			<?php echo nl2br( esc_html( $addr ) ); ?><br>
			<?php if ( $cui ) : ?>CUI: <?php echo esc_html( $cui ); ?><br><?php endif; ?>
			<a href="<?php echo esc_url( 'mailto:' . $email ); ?>"><?php echo esc_html( $email ); ?></a> ·
			<a href="<?php echo esc_url( BMA_AJ_Company::phone_href( $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a></p>

			<h2><?php esc_html_e( '2. A weboldal célja', 'bma-ajanlatkeres' ); ?></h2>
			<ul>
				<li><?php esc_html_e( 'cég és szolgáltatások bemutatása', 'bma-ajanlatkeres' ); ?></li>
				<li><?php esc_html_e( 'referenciák megjelenítése', 'bma-ajanlatkeres' ); ?></li>
				<li><?php esc_html_e( 'kapcsolatfelvétel és ajánlatkérés', 'bma-ajanlatkeres' ); ?></li>
				<li><?php esc_html_e( 'ajánlatok és projektinformációk elérése ügyfélfiókon keresztül', 'bma-ajanlatkeres' ); ?></li>
			</ul>
			<p><?php esc_html_e( 'A weboldali információk általános tájékoztatásnak minősülnek.', 'bma-ajanlatkeres' ); ?></p>

			<h2><?php esc_html_e( '3. Ajánlatkérés', 'bma-ajanlatkeres' ); ?></h2>
			<p><?php esc_html_e( 'Az ajánlatkérés beküldése nem jelent automatikus szerződéskötést vagy megrendelést. A BMA Engineering további információkat kérhet.', 'bma-ajanlatkeres' ); ?></p>

			<h2><?php esc_html_e( '4. Ajánlat', 'bma-ajanlatkeres' ); ?></h2>
			<p><?php esc_html_e( 'Az egyedi ajánlat vagy szerződés elsőbbséget élvez az általános weboldali feltételekkel szemben.', 'bma-ajanlatkeres' ); ?></p>

			<h2><?php esc_html_e( '5. Árak', 'bma-ajanlatkeres' ); ?></h2>
			<p><?php esc_html_e( 'Ha tájékoztató ár jelenik meg, az nem feltétlenül végleges ajánlat.', 'bma-ajanlatkeres' ); ?></p>

			<h2><?php esc_html_e( '6. Felhasználó által megadott információk', 'bma-ajanlatkeres' ); ?></h2>
			<p><?php esc_html_e( 'A felhasználó felelőssége, hogy a megadott adatok tudomása szerint pontosak és teljesek legyenek.', 'bma-ajanlatkeres' ); ?></p>

			<h2><?php esc_html_e( '7. Dokumentumfeltöltés', 'bma-ajanlatkeres' ); ?></h2>
			<p><?php esc_html_e( 'Csak olyan dokumentum tölthető fel, amelynek továbbítására a felhasználó jogosult. Nem tölthető fel jogellenes, rosszindulatú, harmadik fél jogait sértő vagy indokolatlanul érzékeny tartalom.', 'bma-ajanlatkeres' ); ?></p>

			<h2><?php esc_html_e( '8. Ügyfélfiók', 'bma-ajanlatkeres' ); ?></h2>
			<p><?php esc_html_e( 'A felhasználó felelős a hozzáférési adatok biztonságáért, és csak a saját adataihoz férhet hozzá.', 'bma-ajanlatkeres' ); ?></p>

			<h2><?php esc_html_e( '9. Szellemi tulajdon', 'bma-ajanlatkeres' ); ?></h2>
			<p><?php esc_html_e( 'A weboldali tartalom védelem alatt állhat. A projektben készült dokumentumokra az egyedi szerződés/ajánlat vonatkozik.', 'bma-ajanlatkeres' ); ?></p>

			<h2><?php esc_html_e( '10–12. Külső linkek, elérhetőség, felelősség', 'bma-ajanlatkeres' ); ?></h2>
			<p><?php esc_html_e( 'Nem felelünk külső oldalak tartalmáért. A folyamatos működés nem garantálható. A weboldali általános információ nem helyettesíti az egyedi műszaki felmérést.', 'bma-ajanlatkeres' ); ?></p>

			<h2><?php esc_html_e( '13. Adatvédelem', 'bma-ajanlatkeres' ); ?></h2>
			<p><a href="<?php echo esc_url( $privacy ); ?>"><?php esc_html_e( 'Adatkezelési tájékoztató', 'bma-ajanlatkeres' ); ?></a></p>

			<h2><?php esc_html_e( '14. Feltételek módosítása', 'bma-ajanlatkeres' ); ?></h2>
			<p><?php esc_html_e( 'A szolgáltató a szolgáltatások, a weboldal vagy a jogszabályok változása esetén módosíthatja a feltételeket.', 'bma-ajanlatkeres' ); ?></p>

			<h2><?php esc_html_e( '15. Kapcsolat', 'bma-ajanlatkeres' ); ?></h2>
			<p><?php echo esc_html( $company ); ?><br><?php echo nl2br( esc_html( $addr ) ); ?><br>
			<a href="<?php echo esc_url( 'mailto:' . $email ); ?>"><?php echo esc_html( $email ); ?></a><br>
			<a href="<?php echo esc_url( BMA_AJ_Company::phone_href( $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a></p>
			<p class="bma-legal-note"><?php esc_html_e( 'Megjegyzés: a jogi szöveget publikálás előtt célszerű jogi szakértővel ellenőriztetni.', 'bma-ajanlatkeres' ); ?></p>
		</article>
		<?php
		return (string) ob_get_clean();
	}
}
