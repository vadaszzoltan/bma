# Csomag sablon (kötelező szerkezet)

```
projektnev-redesign-cursor/
├── README.md                 # áttekintés, tokenek, komponensek, lépések
├── PROMPT.md                 # bemásolható Cursor indító prompt
├── VALTOZASOK.md             # UX/layout: mi változott és miért
├── SZOVEGVALTOZASOK.md       # tételes szövegeltérések
├── FELDERITES-UTAN.md        # (2. kör) Cursor-felderítés utáni korrekciók
├── css/
│   └── projekt-v2.css        # egy fő, reszponzív CSS
└── markup/
    ├── 01-fooldal.html
    ├── 02-….html
    └── …
```

Zip neve: `projektnev-redesign-cursor.zip` (a gyökérben egy mappa legyen, ne lapos fájlhalmaz).

---

## README.md kötelező szakaszok

1. **Mit tartalmaz** — fájllista (egyezzék a zippel)
2. **Amire a kód épül** — ismert pathok; ami nem ismert: „Cursor derítse fel”
3. **Design tokenek** — mi marad, mi új
4. **Komponens-tábla** — osztály → hol használatos
5. **Reszponzív töréspontok** — px + mi változik
6. **Bekötési lépések** — sorrend (CSS → közös részek → oldalak)
7. **Szövegkezelés** — hivatkozás `SZOVEGVALTOZASOK.md`-re
8. **Szabályok** — ne nyúlj X-hez, escape, a11y minimum
9. **Helyőrzők listája** — egy táblázatban
10. **Amit a csomag NEM tartalmaz** — őszintén (nem láttam PHP-t, nem nyílt meg /referenciak/, stb.)

---

## PROMPT.md kötelező szerkezet

```markdown
1. Csomag kicsomagolás + fájlellenőrzés (hiány → állj meg)
2. Olvasási sorrend: README → SZOVEGVALTOZASOK → VALTOZASOK → PROMPT
   (+ FELDERITES-UTAN, ha van — az felülír)
3. Lépés 1: CSAK feltérképezés, ne módosíts
4. Lépés 2+: CSS, majd oldalak — külön-külön
5. Szabályok (token, text domain felderítés, ne találgass, tartsd a kosár/AJAX-ot…)
```

A feltérképezés kérje ki legalább:
- hol renderelődik az egyes oldal / szekció
- enqueue handle-ek
- kosár / AJAX action-ök
- CPT / meta / taxonómia
- i18n plugin
- text domain-ek
- mi NEM található

---

## Markup fájlnevek

- `NN-rovid-slug.html`, stabil sorrend (01 = főoldal).
- A README, PROMPT és a tényleges fájlnevek **legyenek azonosak**.
- Egy oldal = egy fájl. Ha lista + detail egy URL-családban van, vagy szétválasztod (`05-lista`, `06-detail`), vagy egy fájlban egyértelmű blokkokkal jelölöd.

### Markup fejléce (ajánlott komment)

```html
<!--
  Oldal: /szolgaltatasok/
  Cél (ha ismert): plugins/.../services-list.php
  Függő komponensek: .panel .task .summary .pagehead
  Dinamikus: {{...}}  Helyőrző: [ ...]
-->
```

---

## SZOVEGVALTOZASOK.md formátum

Oldalanként / szekciónként táblázat:

| Elem | Állapot | Eredeti | Új | Megjegyzés |
|------|---------|---------|-----|------------|

Állapotok: `VÁLTOZATLAN` | `MÓDOSÍTOTT` | `ÚJ` | `TÖRÖLVE` | `HELYŐRZŐ` | `JAVASLAT`

Globális javítások (helyesírás, átnevezés) külön „G1, G2…” listában — jelezd, ha **adatbázis-tartalom** is (ügyfél-jóváhagyás kell).

---

## FELDERITES-UTAN.md (2. kör)

Csak azokat írd, amik a README-t felülírják:
- tényleges PHP path → markup mapping
- enqueue handle
- URL / shortcode / külön oldalak (pl. kosár ≠ űrlap)
- meta vs taxonómia a szűrőkhöz
- „már átírt” fájlok (ne írd felül vakon)
- téves feltételezések javítása (pl. „van fizetés”)

Első mondat: **„Ütközés esetén ez a fájl a mérvadó.”**