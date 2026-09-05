# Claude Design → Cursor handoff útmutató

Ez a mappa azt írja le, **hogyan készítsen a Claude Design olyan implementációs csomagot**, amit a Cursor agent a lehető legkevesebb félreértéssel tud bekötni.

## Mit add át a Claude Designnak?

1. **`CLAUDE-DESIGN-BRIEF.md`** — fő szabályok, munkafolyamat, mit szabad / mit ne
2. **`PACKAGE-TEMPLATE.md`** — kötelező mappaszerkezet és fájlok
3. **`CHECKLIST.md`** — átadás előtti ellenőrzőlista
4. **`BMA-KONTEXTUS.md`** — ehhez a projekthez tartozó tények (plugin, shortcode-ok, URL-ek)

A Designnak ezeket együtt add oda (zip vagy chat-csatolmány), plusz ha van: élő URL / screenshot / meglévő felderítési jegyzet.

## Cél

Egy zip, amit a Cursor **egy lépésben kicsomagol**, elolvas, feltérképez, majd lépésenként beköt — anélkül, hogy kitalálná a hiányzó adatokat, vagy rossz fájlba írjon.