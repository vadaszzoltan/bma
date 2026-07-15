# BMA Engineering – Technikai dokumentáció

## 1. Telepítés és környezet

### Követelmények

- Docker és Docker Compose
- PHP 8.1+ (konténerben)
- MySQL 8.0
- WordPress 6.4+

### Környezeti változók (`.env`)

| Változó | Leírás |
|---------|--------|
| `DB_NAME` | Adatbázis neve |
| `DB_USER` / `DB_PASSWORD` | MySQL hozzáférés |
| `WP_DEBUG` | Debug mód (0/1) |
| `SMTP_*` | E-mail küldés (MailHog dev, SMTP éles) |
| `GA4_MEASUREMENT_ID` | Google Analytics 4 |
| `GTM_CONTAINER_ID` | Google Tag Manager |

### Élesítés

1. WordPress telepítés SSL-lel
2. `DISALLOW_FILE_EDIT` engedélyezve (`config/wp-config-extra.php`)
3. `WP_DEBUG` kikapcsolva
4. SMTP beállítása (BMA → Beállítások)
5. Rendszeres mentés (UpdraftPlus vagy hosting backup)
6. Permalinkek: `/beallitasok/%postname%/`

---

## 2. Pluginok és struktúra

### Saját plugin: `bma-ajanlatkeres`

```
bma-ajanlatkeres/
├── bma-ajanlatkeres.php
├── includes/
│   ├── class-plugin.php          # Bootstrap
│   ├── class-activator.php       # Aktiválás, oldalak, seed
│   ├── class-service-cpt.php       # Szolgáltatás CPT
│   ├── class-reference-cpt.php   # Referencia CPT
│   ├── class-dynamic-fields.php  # Dinamikus mezők
│   ├── class-cart.php            # Ajánlatkérési kosár
│   ├── class-quote-requests.php  # Ajánlatkérések
│   ├── class-quotes.php          # Ajánlatkezelés
│   ├── class-documents.php       # Dokumentumok
│   ├── class-statuses.php        # Státuszok
│   ├── class-email-templates.php # E-mailek
│   ├── class-roles.php           # Ügyfél szerepkör
│   ├── class-security.php        # Biztonság
│   ├── admin/class-admin.php     # Admin UI
│   └── database/class-schema.php # DB táblák
├── templates/                    # Admin és frontend sablonok
└── assets/                       # CSS, JS
```

### Child theme: `bma-engineering-child`

- Parent theme: konfigurálható (`style.css` → `Template:`)
- Oldalsablonok: főoldal, szolgáltatások, referenciák, kapcsolat, ajánlatkérés, kosár, ügyfélportál
- Header/footer, navigáció, placeholder logó

### Ajánlott külső pluginok (telepítés után)

- **Yoast SEO** vagy **Rank Math** – meta title/description, sitemap
- **Wordfence** – brute-force védelem kiegészítés
- **UpdraftPlus** – mentés

---

## 3. Adatmodell

### WordPress CPT

| CPT | Slug | Leírás |
|-----|------|--------|
| `bma_service` | `/szolgaltatas/{slug}` | Szolgáltatások |
| `bma_reference` | `/referencia/{slug}` | Referenciák |

### Custom táblák (prefix: `wp_bma_`)

| Tábla | Tartalom |
|-------|----------|
| `bma_service_items` | Szolgáltatáselemek |
| `bma_dynamic_fields` | Mező definíciók |
| `bma_field_assignments` | Mező ↔ szolgáltatás/elem |
| `bma_statuses` | Státuszok |
| `bma_quote_requests` | Ajánlatkérések |
| `bma_quote_request_services` | Kérés ↔ szolgáltatások |
| `bma_status_history` | Státuszelőzmények |
| `bma_quotes` | Ajánlatok |
| `bma_documents` | Fájlok meta |
| `bma_email_templates` | E-mail sablonok |
| `bma_security_log` | Biztonsági napló |

### Felhasználói szerepkör

- **`bma_client`** – ügyfél: saját kérések és dokumentumok

---

## 4. Folyamatok

### Ajánlatkérés

1. Szolgáltatás kiválasztása → elemek + mezők kitöltése
2. Kosárba helyezés (session)
3. Kapcsolattartói adatok + opcionális fiók
4. Mentés DB-be, e-mailek, kosár ürítése

### Dokumentumok

- Fájlok: `wp-content/uploads/bma-documents/` (`.htaccess` deny)
- Letöltés: `/bma-download/{id}/` – jogosultság ellenőrzés

### E-mail események

- `request_confirmation` – ügyfél
- `request_admin_notify` – admin
- `status_change`, `info_request`, `quote_uploaded`, `document_uploaded`, `documents_ready`

---

## 5. REST API

- `GET /wp-json/bma/v1/services/{id}/config` – szolgáltatás mezők/elemek
- `GET /wp-json/bma/v1/cart/count` – kosár elemszám

---

## 6. Biztonság

- Nonce minden AJAX/űrlap műveletnél
- Capability checks (`bma_*` jogosultságok)
- Input sanitization, output escaping
- Brute-force limit: 5 kísérlet / 15 perc
- Védett fájlletöltés
- `noindex` ügyfélportál és kosár

---

## 7. Analitika események

- `service_added_to_cart`
- `quote_request_submitted`
- `account_created`
- `document_download`

GA4/GTM csak cookie hozzájárulás után töltődik.

---

## 8. Shortcode-ok

| Shortcode | Használat |
|-----------|-----------|
| `[bma_home_hero]` | Főoldal hero |
| `[bma_home_services]` | Fő szolgáltatások |
| `[bma_services_list]` | Szolgáltatás lista |
| `[bma_references_list]` | Referencia lista |
| `[bma_quote_cart]` | Kosár |
| `[bma_quote_form]` | Beküldő űrlap |
| `[bma_client_portal]` | Ügyfélportál |
| `[bma_contact_form]` | Kapcsolat űrlap |
| `[bma_social_links]` | Közösségi média |
