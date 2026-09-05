# Bemásolható prompt Cursorhoz

> Az alábbi szöveget másold be a Cursor chatbe, miután a `bma-redesign-cursor` mappát
> bemásoltad a projekt gyökerébe.

---

A projekt egy WordPress oldal: `bma-engineering-child` gyerektéma (szülő: `ollie`) és
egy saját `bma-ajanlatkeres` plugin.

A repo gyökerében van egy `bma-redesign-cursor/` mappa: ez egy jóváhagyott UI-terv
implementációs csomagja. Olvasd el elsőként a `bma-redesign-cursor/README.md`-t, majd
a `VALTOZASOK.md`-t.

Feladat, ebben a sorrendben, külön commitokban:

1. **Feltérképezés.** Keresd meg és sorold fel: hol renderelődik a főoldal minden szekciója
   (`.bma-hero`, `.bma-home-services`, `.bma-home-multi`, `.bma-home-benefits`,
   `.bma-home-process`, `.bma-home-references`, `.bma-home-about`, `.bma-home-closing`),
   hol a `/szolgaltatasok/`, `/ajanlatkeres/` és `/ugyfelportal/` template-je, és hol van
   a kosár (ajánlat) állapotkezelése. Írd ki, mit találtál, és **még ne módosíts semmit**.

2. **CSS bekötés.** Másold be a `css/bma-v2.css`-t a gyerektéma `assets/css/` mappájába,
   és kösd be a meglévő `theme.css` enqueue után, függőségként megadva annak a handle-jét.

3. **Főoldal.** Cseréld le a szekciók belső markupját a `markup/01-fooldal.html` szerint.
   A dinamikus tartalom (szolgáltatások, referenciák) maradjon adatvezérelt – a fájlban
   lévő szöveg csak minta. A külső `.bma-section` / `.bma-container` burkolókat tartsd meg.

4. **Szolgáltatás lista.** A `/szolgaltatasok/` oldal a `markup/02-szolgaltatasok.html` szerint:
   területenként panel, ugróhivatkozások, ragadós kosár-összegző. A tételválasztót **külön
   sablonrészletként** (template part) csináld meg, mert az ajánlatkérő 1. lépése ugyanaz.

5. **Szolgáltatás aloldal.** A meglévő `/szolgaltatas/{slug}/` sablon átdolgozása a
   `markup/05-szolgaltatas-aloldal.html` alapján, mind a négy területre. Javítsd a fejléc fölött
   formázatlanul megjelenő hu/ro/en nyelvváltót is.

6. **Ajánlatkérő folyamat.** Alakítsd 4 lépéses folyamattá a `markup/03-ajanlatkeres.html`
   szerint, a 4. pontban készített tételválasztó sablonrészletet újrahasználva. A kiválasztás
   azonnal frissítse a kosarat és a fejléc számlálóját (AJAX + nonce), ne külön „hozzáadás"
   gombbal. A meglévő kosár- és mentési logikát tartsd meg.

7. **Ügyfélportál.** A `markup/04-ugyfelportal.html` szerint: teendő-sáv, szűrő csempék és fülek,
   bővebb táblázatsorok, dokumentum-panel, és a részletes nézet átrendezése.

Szabályok:
- Ne írd át a `:root` design tokeneket, és ne nyúlj a szülő témához.
- Minden felhasználói szöveg `__()` / `esc_html()` / `esc_attr()`.
- A markupban `{{...}}` dinamikus érték, `[SZÖGLETES ZÁRÓJEL]` pedig hiányzó valós adat –
  utóbbit hagyd benne helyőrzőként, és a végén listázd, mit kell kitölteni.
- A közös komponenseket (`.pagehead`, `.panel`, `.task`, `.summary`, `.btn`, `.process`) egyszer
  valósítsd meg sablonrészletként, és mindenhol azt használd – ne szülessen két változat
  ugyanarra.
- Minden lépés után írd le egy mondatban, mit változtattál és melyik fájlban.
