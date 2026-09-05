# Fordítások – román és angol

Két fájl, mindkettőben ugyanaz a **175 msgid**, ami a redesign során került be vagy változott:

```
bma-forditasok-ro.po   Language: ro_RO
bma-forditasok-en.po   Language: en_US
```

Ellenőrizve: nincs üres fordítás, és minden `%s` / `%d` / `%1$s` helyettesítő megegyezik
a magyar eredetivel.

## Ez nem kész .po – fordítási memória

A stringek **két text domain között oszlanak meg** (`bma-ajanlatkeres` és
`bma-engineering`), a lista viszont fájlnév nélkül készült. Ezért ne ezeket a fájlokat
tedd a `languages/` mappába, hanem töltsd fel belőlük a két domain saját `.po`-ját:

1. Generáld le a két `.pot`-ot (ez a 10. lépésben megvolt).
2. Minden domainre és nyelvre hozz létre `.po`-t a `.pot`-ból
   (`bma-ajanlatkeres-ro_RO.po`, `bma-ajanlatkeres-en_US.po`,
   `bma-engineering-ro_RO.po`, `bma-engineering-en_US.po`).
3. Töltsd fel a `msgstr` mezőket az itteni fájlokból **msgid-egyezés alapján**.
   Erre jó a `msgmerge --no-fuzzy-matching`, vagy egy pár soros script.
   Ami nincs meg itt (régi, változatlan string), az maradjon úgy, ahogy volt.
4. Fordítsd `.mo`-ra (`msgfmt` vagy `wp i18n make-mo`).
5. Ellenőrizd, hogy a Polylang a helyes nyelvi fájlt tölti be.

## Amire figyelj

**A GYIK mezők `K:` / `V:` előtagja NE forduljon le.** A `_bma_faq` metát a PHP a `K:` és
`V:` prefixek alapján bontja fel. A minta-szöveget ezért szándékosan `K:` / `V:` alakban
hagytam románul és angolul is. Ha az admin felületen mégis lefordított előtagot szeretnétek
(`Î:` / `R:`, `Q:` / `A:`), akkor a feldolgozó függvénynek is el kell fogadnia azokat –
enélkül a GYIK szekció némán üres marad.

**A `[X]` és `[X–Y]` helyőrzők a fordításokban is bent maradtak** – ezek továbbra is valós
adatra várnak, nyelvenként külön nem kell őket kitalálni.

**A `_bma_work` mező `Címke: szöveg` formátuma** kettőspontra bont, ezért a minta románul
és angolul is kettősponttal készült.

## Terminológia, amit végig tartottam

| Magyar | Román | Angol |
|---|---|---|
| ajánlatkérés | cerere de ofertă | quote request |
| ajánlat | ofertă | quote |
| tétel (kosárban) | poziție | item |
| részfeladat | subactivitate | sub-task |
| tervezés | proiectare | design |
| engedélyeztetés | autorizare | permitting |
| műszaki dokumentáció | documentație tehnică | technical documentation |
| közmű-koordináció | coordonare utilități | utility coordination |
| ügyfélportál | portal client | client portal |
| kosár | coș | cart |
| díjbekérő | proformă | proforma invoice |
| munkanap | zi lucrătoare | working day |
| átfutás | durată | turnaround |

A román szövegek végig magázó (dumneavoastră) formában vannak, ahogy a magyar eredeti is.
