# BMA Engineering – Adminisztrátori útmutató

## Bejelentkezés

1. Nyissa meg: `https://[domain]/wp-admin/`
2. Jelentkezzen be adminisztrátori fiókkal

---

## Kezdeti beállítások

### 1. Téma és plugin

- Aktiválja a **BMA Engineering Child** témát
- Aktiválja a **BMA Ajánlatkérés** plugint
- **Beállítások → Permalinkek** → Mentés (fontos az URL-ekhez)

### 2. Kapcsolati adatok

**BMA Ajánlatkérés → Beállítások**

- Admin értesítési e-mail (új ajánlatkérésről)
- Telefon, e-mail, cím
- Közösségi média linkek (Facebook, LinkedIn, Instagram, X, YouTube)
- GA4 / GTM azonosítók (opcionális)
- SMTP beállítások éles e-mailhez

---

## Szolgáltatások kezelése

**Szolgáltatások** menüpont (WordPress admin bal oldali menü)

Minden szolgáltatásnál:

- **Cím, leírás** – tartalmi mezők
- **Aktív** – csak aktív szolgáltatás jelenik meg
- **Sorrend** – megjelenítési sorrend
- **Placeholder kép** – vizuális helykitöltő
- **Ügyféloldali információ** – segítő szöveg az ajánlatkérésnél
- **Admin megjegyzés** – belső használatra

### Szolgáltatáselemek

**BMA Ajánlatkérés → Szolgáltatáselemek**

- Pl. Tervezés alatt: vízbekötési terv, gázbekötési terv
- Kötelező/opcionális jelölés
- Sorrend beállítása

---

## Dinamikus mezők

**BMA Ajánlatkérés → Dinamikus mezők**

Új mező létrehozása:

1. Mező kulcs (pl. `alapterulet`) – csak kisbetű, szám, aláhúzás
2. Típus: szöveg, szám, e-mail, dátum, legördülő, fájl stb.
3. Admin és ügyfél label
4. Kapcsolódó szolgáltatás
5. Kötelező jelölés

A mezők csak a hozzárendelt szolgáltatásnál jelennek meg.

---

## Referenciák

**Referenciák** menüpont

- Projekt címe, leírás
- Projekt típusa, helyszín, év
- Kapcsolódó szolgáltatások
- Galéria helyett placeholder jelenik meg

---

## Ajánlatkérések feldolgozása

**BMA Ajánlatkérés → Ajánlatkérések**

### Lista

- Keresés azonosító, név, e-mail alapján
- Szűrés státusz szerint

### Részletes nézet

1. **Ügyféladatok** – beküldött kapcsolattartói információk
2. **Státusz módosítása** – válasszon új státuszt, opcionális megjegyzés
3. **Ajánlat rögzítése** – nettó/adó/bruttó, érvényesség, PDF feltöltés
4. **Dokumentum feltöltése** – terv, műszaki dok., átadási dok.
5. **Admin megjegyzés** – belső jegyzet

Státuszváltozáskor automatikus e-mail megy az ügyfélnek (ha be van kapcsolva).

---

## Státuszok

**BMA Ajánlatkérés → Státuszok**

Alap státuszok:

- Beérkezett → Feldolgozás alatt → Ajánlat készül → Ajánlat elkészült → Teljesítés alatt → Dokumentumok feltöltve → Lezárva
- További információ szükséges (külön ág)

Beállítható: szín, sorrend, ügyfél láthatóság, automatikus e-mail.

---

## E-mail sablonok

**BMA Ajánlatkérés → E-mail sablonok**

Szerkeszthető változók:

- `{request_number}` – ajánlatkérés azonosító
- `{client_name}`, `{client_email}`
- `{services}` – kiválasztott szolgáltatások
- `{status}` – aktuális státusz
- `{portal_link}` – ügyfélportál link
- `{quote_amount}`, `{quote_valid_until}`

---

## Ügyfélportál

Az ügyfelek a `/ugyfelportal/` oldalon:

- Látják saját ajánlatkéréseiket
- Letölthetik az ajánlatot és dokumentumokat
- Követhetik a státuszt

Ügyfélfiók létrehozható ajánlatkérés közben, vagy külön regisztrációval.

---

## Gyakori feladatok

| Feladat | Hol |
|---------|-----|
| Új szolgáltatás | Szolgáltatások → Új hozzáadása |
| Mező hozzáadása | BMA → Dinamikus mezők |
| Ajánlat feltöltése | BMA → Ajánlatkérések → Megtekintés |
| Kapcsolat módosítása | BMA → Beállítások |
| E-mail szöveg | BMA → E-mail sablonok |

---

## Placeholder tartalmak cseréje

A következő placeholdereket cserélje valós adatokra:

- `[LOGÓ PLACEHOLDER]` – Megjelenés → Testreszabás → Logó
- `[KAPCSOLATI ADAT]` – BMA Beállítások
- `[CÉG ALAPÍTÁSÁNAK ÉVE]` – Lábléc (footer.php vagy widget)
- `[ADATVÉDELMI TÁJÉKOZTATÓ SZÖVEGE]` – Adatvédelmi oldal
- `[COOKIE TÁJÉKOZTATÓ SZÖVEGE]` – Cookie oldal
- `[SZAKMAI TANÚSÍTVÁNYOK]` – Tartalmi oldal
