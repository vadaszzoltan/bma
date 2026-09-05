# Átadás előtti checklist — Claude Design

Pipáld ki, mielőtt odaadod a zipet a Cursornak / felhasználónak.

## Csomag teljesség

- [ ] A zip gyökerében egy mappa van (`…-redesign-cursor/`)
- [ ] `README.md`, `PROMPT.md`, `VALTOZASOK.md`, `SZOVEGVALTOZASOK.md` megvan
- [ ] `css/` fő CSS megvan
- [ ] `markup/` fájlok száma = amit a README felsorol (nem kevesebb)
- [ ] Fájlnevek egyeznek a README / PROMPT hivatkozásaival
- [ ] Nincs elavult / félkész fájl a zipben (pl. régi 5 HTML, ha már 7 kell)

## Tartalomminőség

- [ ] Minden oldalhoz van desktop markup; mobil = ugyanaz + CSS
- [ ] Media query-k a CSS végén; fejlécben jelezve a töréspontok
- [ ] Shared komponensek egyszer vannak leírva (nem oldalankénti variáns)
- [ ] `{{dinamikus}}` és `[HELYŐRZŐ]` konzekvensen használva
- [ ] Helyőrzők egy táblázatban összegezve a README-ben vagy SZOVEGVALTOZASOK végén
- [ ] Szövegváltozások tételesen; ami nincs listázva = ne írd át
- [ ] Ami adatbázis-tartalom (szolgáltatásnév, referencia cím), az jelölve

## Architektúra-őszinteség

- [ ] Nem állítok olyan pathot / handle-t, amit nem ellenőriztem
- [ ] „Nem láttam / nem nyílt meg” szekció kitöltve
- [ ] Ha multi-step flow: leképezés meglévő URL-ekre *vagy* „Cursor derítse fel”
- [ ] Nincs „hozz létre új taxonómiát”, ha a mezők még ismeretlenek
- [ ] i18n: melyik plugin (ha ismert), nyelvválasztó markup megvan

## Cursor-prompt

- [ ] 1. lépés: csak feltérképezés, ne módosítson
- [ ] Hiányzó fájl esetén álljon meg
- [ ] Lépések sorrendje egyértelmű
- [ ] CSS: előbb létezés + diff ellenőrzés, ha már lehet a témában
- [ ] „Ne találgass helyőrzőt / ne nyúlj szülő témához” benne van

## Vizuális QA (Design oldalon)

- [ ] 390 / 768 / 1440 (vagy a dokumentált töréspontok) — nincs szándékolatlan vízszintes overflow
- [ ] Érintőcél ≥ 44 px mobilon a kattintható elemeken
- [ ] Kontraszt-problémák megemlítve, ha tudatos (márka vs WCAG)

## 2. kör (Cursor-felderítés után)

- [ ] `FELDERITES-UTAN.md` elkészült, README ütközéseit felülírja
- [ ] Friss zip felülírja a teljes mappát
- [ ] PROMPT / mapping tábla egyezik a felderített PHP pathokkal