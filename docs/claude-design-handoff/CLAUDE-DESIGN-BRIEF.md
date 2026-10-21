# Claude Design brief — hogyan készíts Cursornak design csomagot

Te (Claude Design) **nem kódot írsz a WordPressbe**. Te egy **implementációs csomagot** adsz át a Cursor agentnek: referencia-markup, kész CSS, szövegváltozások listája, és pontos bekötési utasítások.

A Cursor fogja: feltérképezni a repo-t, összehasonlítani a meglévő PHP-t a markupkal, CSS-t bekötni, shortcode-template-eket módosítani, AJAX-ot megtartani.

---

## 1. Alapelvek

1. **Egy markup, három nézet.** Nincs külön mobil HTML. Desktop markup + CSS media query (jellemzően 1100 / 900 / 640).
2. **Ne találgass architektúrát.** Ha nem láttad a PHP-t / shortcode-okat, írd be a README-be: „Cursor derítse fel.” Ne állítsd, hogy a markup a *témába* megy, ha lehet, hogy *plugin shortcode* rendereli.
3. **Ne találgass üzleti adatot.** Ismeretlen érték → `[SZÖGLETES ZÁRÓJEL]` helyőrző + listázás egy helyen. Ne írj kitalált GYIK-választ, települést, határidőt.
4. **Szöveg ≠ markup.** Minden szövegeltérést a meglévő oldalhoz képest írj a `SZOVEGVALTOZASOK.md`-be (VÁLTOZATLAN / MÓDOSÍTOTT / ÚJ / HELYŐRZŐ / TÖRÖLVE / JAVASLAT). Amit nem sorolsz fel, azt a Cursor **ne írja át**.
5. **Viselkedés vs kinézet.** Ha a terv UX-et is változtat (azonnali kosár, lépéssáv), írd le a *kívánt viselkedést* és a *megtartandó meglévő API-t* (action nevek, ha ismertek). Ne írj új kosárlogikát „zöldmezősnek”.
6. **Felderítés utáni korrekció.** Ha a Cursor visszajelez (pl. rossz enqueue handle, már átírt hero, külön kosár-oldal), készíts `FELDERITES-UTAN.md`-t, ami **felülírja** a README feltételezéseit. Ütközésnél ez a mérvadó.

---

## 2. Mit adj át egy csomagban (kötelező)

Lásd részletesen: `PACKAGE-TEMPLATE.md`.

Minimum:
- `README.md` — mi ez, tokenek, komponenstár, lépések sorrendje, szabályok
- `PROMPT.md` — bemásolható Cursor-prompt (lépésenként, „először csak feltérképezés”)
- `VALTOZASOK.md` — mi változott és miért (UX / layout indokok)
- `SZOVEGVALTOZASOK.md` — tételes szöveglista
- `css/*.css` — egy fő, reszponzív CSS (media query-k a fájl **végén**)
- `markup/NN-*.html` — oldalanként egy fájl, számozva, stabil fájlnevek

Ha a Cursor már felderített: `FELDERITES-UTAN.md` (opcionális első körben, kötelező második körben).

---

## 3. Markup szabályok (Cursor-barát)

### Osztályok
- Használj **stabil, semleges** osztályokat (`.hero`, `.panel`, `.task`, `.summary`, `.btn`), ne WordPress-specifikus PHP osztályneveket.
- Ha a meglévő oldalon már van burkoló (pl. `.bma-section`), a README-ben írd: „külső burkoló maradhat / cserélendő” — ne legyen kétféle utasítás.
- Közös komponenseket **egyszer** definiáld a CSS „shared” részében; minden oldalon ugyanazt a markupot add.

### Dinamikus tartalom
- Adatvezérelt mezők: `{{service_title}}`, `{{ref_permalink}}` stb. — rövid, egyértelmű token.
- Ismeretlen ügyféladat: `[X] munkanapon belül`, `[TELEPÜLÉS]`.
- Ne építs be 4 hardcode-olt szolgáltatásnevet „véglegesnek”, ha azok DB-ből jönnek — a markupban legyen minta, a README-ben: „adatvezérelt”.

### Állapotok
- Interaktív UI-nál jelöld az állapotokat osztályokkal: `.is-on`, `.is-disabled`, `.is-done`, `.is-now`, `.step--active`.
- Ha egy HTML-ben több nézet van (lista / részlet, wizard lépések), használd explicit blokkokat, pl. komment + `<!-- step:02 -->` vagy `<sc-if>` jellegű jelölést, és a README-ben kösd meglévő URL-ekhez / template-ekhez.

### Ikonok
- Inline SVG, `aria-hidden="true"` a díszítőkre. Ne emoji. Ne külső ikonfont, ha elkerülhető.

### Linkek
- Prototípusban lehet `#`, de a README-ben sorold: melyik gomb hova menjen (slug / shortcode / CPT).

---

## 4. CSS szabályok

- **Ne írd felül** a meglévő `:root` design tokeneket, ha a projekt már definiálta őket (plugin `public.css` / téma). Új token: külön, dokumentálva (pl. `--bma-accent-dark`).
- Media query-k a fájl **végén**; új szabályt elé kell tenni — ezt írd bele a fájl fejlécébe is.
- Egy fájl = minden oldal (vagy világos split: `bma-v2.css` + opcionális kiegészítő). Ne legyen 12 töredezett CSS a Cursorra bízva, hogy melyiket enqueue-olja.
- Írd meg a **enqueue függőséget** csak akkor, ha biztos vagy a handle névben. Ha nem: „Cursor: keresd a meglévő `theme.css` `wp_enqueue_style` handle-jét.”
- Ha a repo-ban már lehet `bma-v2.css`: a PROMPT-ban legyen: **előbb diff**, felülírás csak ha a csomag újabb.

---

## 5. PROMPT.md — hogyan írd a Cursor promptot

A Cursor jobban dolgozik, ha a prompt:

1. **Először csak feltérképezést** kér (ne módosítson).
2. **Ellenőrizteti a zip tartalmát** (kötelező fájlok listája; hiány esetén álljon meg).
3. **Lépésenként** megy (külön commit / külön válasz), nem egyben az egész site redesign.
4. Explicit: **ne találgasson** helyőrzőket; ne nyúljon szülő témához; tartsa meg kosár/AJAX logikát.
5. Kéri a text domain **felderítését** a kódból, ne új domain bevezetését.
6. Ha már volt részleges bekötés: **ne írjon felül vakon** — PHP vs markup diff listázása előbb.

Rossz prompt: „Írd át az egész oldalt a terv szerint.”  
Jó prompt: „1) Feltérképezés. 2) CSS diff + bekötés. 3) Főoldal szekciónként, eltérések listázása után…”

---

## 6. Mit NE csinálj

- Ne feltételezd, hogy minden a **gyerektéma** `page-*.php` fájljaiban van — WordPressben gyakori a **plugin shortcode + CPT template**.
- Ne állítsd, hogy „nincs fizetés / nincs X modul”, ha csak a böngészőben nem láttad — írd: „nem ellenőriztem”.
- Ne adj **7 markupot ígérő README-t 5 fájllal** — a zip legyen teljes, mielőtt kiadod.
- Ne keverj elavult fájlneveket a PROMPT-ban (`02-ajanlatkeres.html` vs `03-ajanlatkeres.html`).
- Ne írj „másold a CSS-t és kész” — ha a meglévő CSS már részben be van kötve, diff kötelező.
- Ne tervezz **új taxonómiát**, amíg a Cursor meg nem nézte a meglévő metákat / CPT mezőket. Inkább: „szűrő forrása TBD – Cursor derítse fel.”

---

## 7. Ajánlott munkafolyamat veled (Design) és Cursorral

```
[Design] vászon + 3 breakpoint ellenőrzés
    → csomag v1 (README, markup, CSS, szövegek, PROMPT)
[Cursor] csak feltérképezés → jelentés
[Design] FELDERITES-UTAN.md (javított mapping, URL-ek, meta mezők)
[Cursor] CSS → nyelvválasztó → oldalanként
[Design] ha kell: frissített markup/CSS zip (verzió a fájlnévben / dátumban)
```

Minden új zip: **teljes mappa felülírásra** (ne „csak a hiányzó fájlok”), és a dátum/méret legyen látható a Letöltésekben.

---

## 8. Minőségkapu átadás előtt

Futtasd végig a `CHECKLIST.md`-t. Ha bármelyik kötelező pipa hiányzik, **ne add át** a Cursornak „majd kitalálja” alapon — javítsd a csomagot.