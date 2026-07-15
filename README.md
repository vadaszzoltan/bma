# BMA Engineering – WordPress weboldal és ajánlatkérő rendszer

WordPress-alapú weboldal és ajánlatkérési rendszer a BMA Engineering számára.

## Gyors indítás

```bash
cp .env.example .env
chmod +x scripts/setup.sh
./scripts/setup.sh
```

Nyissa meg: **http://localhost:8080**

## Projektstruktúra

```
BMA/
├── docker-compose.yml          # WordPress + MySQL + MailHog
├── config/wp-config-extra.php  # Extra WP beállítások
├── wp-content/
│   ├── plugins/bma-ajanlatkeres/   # Saját ajánlatkérő plugin
│   ├── themes/bma-engineering-child/  # Child theme
│   └── robots.txt
├── docs/
│   ├── TECHNICAL.md            # Technikai dokumentáció
│   └── ADMIN.md                # Adminisztrátori útmutató
└── scripts/setup.sh
```

## Telepítés után

1. **WordPress telepítő** – admin felhasználó létrehozása
2. **Parent theme** – telepítse a megvásárolt template-et, vagy ideiglenesen a **Twenty Twenty-Four** témát
3. **Child theme** – aktiválja a **BMA Engineering Child** témát
4. **Plugin** – aktiválja a **BMA Ajánlatkérés** plugint (automatikusan létrehozza az oldalakat és demo tartalmat)
5. **Permalinkek** – Beállítások → Permalinkek → Mentés
6. **Beállítások** – BMA Ajánlatkérés → Beállítások (kapcsolat, SMTP, analitika)

## Parent theme csere

A child theme `style.css` fájlban módosítsa a `Template:` sort a megvásárolt theme mappanevére:

```css
Template: your-purchased-theme-folder
```

## Fő funkciók

- Szolgáltatások és szolgáltatáselemek adminisztrálása
- Dinamikus mezők szolgáltatásonként
- Ajánlatkérési kosár (ár nélkül)
- Ajánlatkérés beküldés opcionális ügyfélfiókkal
- Admin: ajánlatkérések, státuszok, ajánlatok, dokumentumok
- Ügyfélportál: saját kérések, ajánlatok, dokumentumok
- Védett dokumentumletöltés
- E-mail értesítések (szerkeszthető sablonok)
- Cookie consent, GA4/GTM integráció
- WCAG alapok (szemantikus HTML, fókusz, ARIA)

## Fejlesztői eszközök

- **MailHog** – e-mail tesztelés: http://localhost:8025
- **WP Debug** – `.env` fájlban: `WP_DEBUG=1`

## Dokumentáció

- [Technikai dokumentáció](docs/TECHNICAL.md)
- [Adminisztrátori útmutató](docs/ADMIN.md)

## Megjegyzés a template-ről

A parent theme fájljait **ne módosítsa**. Minden testreszabás a child theme-ben (`bma-engineering-child`) és a `bma-ajanlatkeres` pluginban történik.
