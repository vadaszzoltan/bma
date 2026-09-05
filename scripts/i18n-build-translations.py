#!/usr/bin/env python3
"""Build Polylang-ready translation catalog (HU → RO / EN) + readable markdown."""

from __future__ import annotations

import json
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
OUT = ROOT / "docs" / "i18n"
HU = json.loads((OUT / "content-hu.json").read_text(encoding="utf-8"))
DB = json.loads((OUT / "db-extras.json").read_text(encoding="utf-8"))


def T(ro: str, en: str) -> dict:
    return {"ro": ro, "en": en}


# --- Pages (by source ID) ---
PAGES = {
    10: {
        "ro": {
            "title": "Pagina principală",
            "slug": "acasa",
            "excerpt": "BMA Engineering oferă proiectare tehnică, autorizare și documentație pentru proiecte rezidențiale și de business.",
            "seo_title": "BMA Engineering | Proiectare tehnică și autorizare",
            "seo_description": "Proiectare tehnică, autorizare, documentație și coordonare utilități. Solicitați o ofertă online – BMA Engineering, Odorheiu Secuiesc.",
            "focus_keyword": "proiectare tehnică, autorizare",
        },
        "en": {
            "title": "Home",
            "slug": "home",
            "excerpt": "BMA Engineering provides engineering design, permitting and technical documentation for residential and business projects.",
            "seo_title": "BMA Engineering | Engineering design and permitting",
            "seo_description": "Engineering design, permitting, technical documentation and utility coordination. Request an online quote – BMA Engineering, Odorheiu Secuiesc.",
            "focus_keyword": "engineering design, permitting",
        },
    },
    11: {
        "ro": {
            "title": "Servicii",
            "slug": "servicii",
            "excerpt": "Proiectare, autorizare, documentație tehnică și coordonare utilități cu o singură echipă de ingineri.",
            "seo_title": "Servicii | Proiectare, autorizare, documentație",
            "seo_description": "Descoperiți serviciile BMA Engineering: proiectare, autorizare, documentație tehnică și coordonare branșamente.",
            "focus_keyword": "servicii de inginerie",
        },
        "en": {
            "title": "Services",
            "slug": "services",
            "excerpt": "Design, permitting, technical documentation and utility coordination with one engineering team.",
            "seo_title": "Services | Design, permitting, documentation",
            "seo_description": "Explore BMA Engineering services: design, permitting, technical documentation and utility connection coordination.",
            "focus_keyword": "engineering services",
        },
    },
    12: {
        "ro": {
            "title": "Referințe",
            "slug": "referinte",
            "excerpt": "Proiecte de referință, de la imobile rezidențiale la investiții industriale.",
            "seo_title": "Referințe | Proiecte de inginerie finalizate",
            "seo_description": "Selecție din proiectele de referință BMA Engineering: proiectare și autorizare pentru clădiri rezidențiale, birouri și industrii.",
            "focus_keyword": "referințe inginerie",
        },
        "en": {
            "title": "References",
            "slug": "references",
            "excerpt": "Reference projects from residential properties to industrial investments.",
            "seo_title": "References | Completed engineering projects",
            "seo_description": "A selection of BMA Engineering reference projects: design and permitting for residential, office and industrial work.",
            "focus_keyword": "engineering references",
        },
    },
    13: {
        "ro": {
            "title": "Contact",
            "slug": "contact",
            "excerpt": "Contact: Odorheiu Secuiesc, Str. Raza Soarelui 11 – office@bmaeng.ro",
            "seo_title": "Contact | BMA Engineering Odorheiu Secuiesc",
            "seo_description": "Contactați echipa BMA Engineering. Adresă: Str. Raza Soarelui 11, Odorheiu Secuiesc. Tel: +40 728 205 982.",
            "focus_keyword": "BMA Engineering contact",
        },
        "en": {
            "title": "Contact",
            "slug": "contact",
            "excerpt": "Get in touch: Odorheiu Secuiesc, Str. Raza Soarelui 11 – office@bmaeng.ro",
            "seo_title": "Contact | BMA Engineering Odorheiu Secuiesc",
            "seo_description": "Contact the BMA Engineering team. Address: Str. Raza Soarelui 11, Odorheiu Secuiesc. Phone: +40 728 205 982.",
            "focus_keyword": "BMA Engineering contact",
        },
    },
    14: {
        "ro": {
            "title": "Cerere de ofertă",
            "slug": "cerere-oferta",
            "excerpt": "Cerere de ofertă online pentru proiectare, autorizare și documentație.",
            "seo_title": "Cerere de ofertă | Ofertă de inginerie online",
            "seo_description": "Solicitați o ofertă personalizată pentru proiectare, autorizare sau documentație tehnică. Proces rapid și transparent.",
            "focus_keyword": "cerere ofertă inginerie",
        },
        "en": {
            "title": "Request a quote",
            "slug": "request-quote",
            "excerpt": "Online quote request for design, permitting and documentation.",
            "seo_title": "Request a quote | Online engineering quote",
            "seo_description": "Request a tailored engineering quote for design, permitting or technical documentation. Fast, transparent process.",
            "focus_keyword": "engineering quote request",
        },
    },
    15: {
        "ro": {
            "title": "Coș cerere de ofertă",
            "slug": "cos-oferta",
            "excerpt": "",
            "seo_title": "Coș cerere de ofertă",
            "seo_description": "Serviciile selectate pentru cererea de ofertă.",
            "focus_keyword": "",
        },
        "en": {
            "title": "Quote cart",
            "slug": "quote-cart",
            "excerpt": "",
            "seo_title": "Quote cart",
            "seo_description": "Your selected services for the quote request.",
            "focus_keyword": "",
        },
    },
    16: {
        "ro": {
            "title": "Portal clienți",
            "slug": "portal-clienti",
            "excerpt": "",
            "seo_title": "Portal clienți",
            "seo_description": "Portal pentru clienți autentificați.",
            "focus_keyword": "",
        },
        "en": {
            "title": "Client portal",
            "slug": "client-portal",
            "excerpt": "",
            "seo_title": "Client portal",
            "seo_description": "Portal for logged-in clients.",
            "focus_keyword": "",
        },
    },
    17: {
        "ro": {
            "title": "Politica de confidențialitate",
            "slug": "confidentialitate",
            "excerpt": "Politica de confidențialitate pentru clienții și vizitatorii BMA Engineering.",
            "seo_title": "Politica de confidențialitate | BMA Engineering",
            "seo_description": "Informare privind prelucrarea datelor cu caracter personal la BMA Engineering.",
            "focus_keyword": "confidențialitate",
        },
        "en": {
            "title": "Privacy policy",
            "slug": "privacy-policy",
            "excerpt": "Privacy policy for BMA Engineering clients and website visitors.",
            "seo_title": "Privacy policy | BMA Engineering",
            "seo_description": "BMA Engineering privacy and data protection notice for clients and website visitors.",
            "focus_keyword": "privacy policy",
        },
    },
    18: {
        "ro": {
            "title": "Politica cookie",
            "slug": "politica-cookie",
            "excerpt": "Informații despre cookie-urile folosite pe site-ul BMA Engineering.",
            "seo_title": "Politica cookie | BMA Engineering",
            "seo_description": "Informații despre cookie-urile folosite pe site-ul BMA Engineering și opțiunile de setare.",
            "focus_keyword": "politica cookie",
        },
        "en": {
            "title": "Cookie policy",
            "slug": "cookie-policy",
            "excerpt": "Information about cookies used on the BMA Engineering website.",
            "seo_title": "Cookie policy | BMA Engineering",
            "seo_description": "Information about cookies used on the BMA Engineering website and how to manage them.",
            "focus_keyword": "cookie policy",
        },
    },
    19: {
        "ro": {
            "title": "Autentificare",
            "slug": "autentificare",
            "excerpt": "",
            "seo_title": "Autentificare",
            "seo_description": "Autentificare în portalul clienți.",
            "focus_keyword": "",
        },
        "en": {
            "title": "Login",
            "slug": "login",
            "excerpt": "",
            "seo_title": "Login",
            "seo_description": "Client portal login.",
            "focus_keyword": "",
        },
    },
    47: {
        "ro": {
            "title": "Plată",
            "slug": "plata",
            "excerpt": "",
            "seo_title": "Plată",
            "seo_description": "Pagină de plată a ofertei.",
            "focus_keyword": "",
        },
        "en": {
            "title": "Payment",
            "slug": "payment",
            "excerpt": "",
            "seo_title": "Payment",
            "seo_description": "Quote payment page.",
            "focus_keyword": "",
        },
    },
    71: {
        "ro": {
            "title": "Termeni și condiții",
            "slug": "termeni",
            "excerpt": "Termeni și condiții generale pentru serviciile BMA Engineering.",
            "seo_title": "Termeni și condiții | BMA Engineering",
            "seo_description": "Termenii și condițiile generale ale site-ului și serviciilor BMA Engineering.",
            "focus_keyword": "termeni și condiții",
        },
        "en": {
            "title": "Terms of use",
            "slug": "terms",
            "excerpt": "General terms of use for BMA Engineering services.",
            "seo_title": "Terms of use | BMA Engineering",
            "seo_description": "General terms of use for the BMA Engineering website and services.",
            "focus_keyword": "terms of use",
        },
    },
}

SERVICES = {
    48: {
        "ro": {
            "title": "Proiectare",
            "slug": "proiectare",
            "excerpt": "Planuri de utilități și instalații pentru proiecte rezidențiale și de business, ținând cont de condițiile din teren.",
            "content": "<p>Serviciul nostru de proiectare sprijină pregătirea tehnică completă a proiectului: de la branșamentele de utilități până la proiectarea rețelei electrice. Lucrăm pe baza reglementărilor în vigoare și a nevoilor clientului.</p><p>Pe parcurs clarificăm documentele disponibile și semnalăm dacă sunt necesare date tehnice suplimentare. Pe baza datelor din cererea de ofertă evaluăm complexitatea proiectului.</p>",
            "seo_title": "Proiectare | Planuri utilități și instalații",
            "seo_description": "Proiectare de utilități și instalații pentru proiecte rezidențiale și de business. Electric, gaz și apă – BMA Engineering.",
            "focus_keyword": "proiectare utilități, instalații",
        },
        "en": {
            "title": "Design",
            "slug": "design",
            "excerpt": "Utility and building services plans for residential and business projects, tailored to site conditions.",
            "content": "<p>Our design service supports the full technical preparation of your project: from utility connections to electrical network design. We always follow applicable regulations and client requirements.</p><p>We review available documents and flag when additional technical data is needed. Based on the quote request we assess project complexity upfront.</p>",
            "seo_title": "Design | Utility and building services plans",
            "seo_description": "Utility and building services design for residential and business projects. Electrical, gas and water design from BMA Engineering.",
            "focus_keyword": "utility design, building services",
        },
    },
    49: {
        "ro": {
            "title": "Autorizare",
            "slug": "autorizare",
            "excerpt": "Pregătirea autorizațiilor de construire și de recepție, plus sprijin pentru clarificări cu autoritățile.",
            "content": "<p>Scopul serviciului de autorizare este ca documentația proiectului să îndeplinească cerințele autorităților. Ajutăm la alcătuirea dosarului, pregătirea depunerii și gestionarea completărilor.</p><p>Procesul este transparent: vă informăm la fiecare pas, iar statusul poate fi urmărit în portalul clienți.</p>",
            "seo_title": "Autorizare | Autorizație de construire și recepție",
            "seo_description": "Pregătirea autorizațiilor de construire și de recepție, sprijin pentru clarificări și completări.",
            "focus_keyword": "autorizare construire",
        },
        "en": {
            "title": "Permitting",
            "slug": "permitting",
            "excerpt": "Preparation of building and occupancy permits, with support for authority coordination.",
            "content": "<p>Our permitting service ensures project documentation meets authority requirements. We help assemble the permit package, prepare submission and manage follow-up requests.</p><p>The process is transparent: you are notified at each step, and status is visible in the client portal.</p>",
            "seo_title": "Permitting | Building and occupancy permits",
            "seo_description": "Preparation of building and occupancy permits, authority coordination and follow-up support.",
            "focus_keyword": "building permitting",
        },
    },
    50: {
        "ro": {
            "title": "Documentație tehnică",
            "slug": "documentatie-tehnica",
            "excerpt": "Memorii tehnice, procese-verbale, documentație de predare și materiale de închidere a proiectului.",
            "content": "<p>Pregătim documentația tehnică completă a proiectului, conform cerințelor autorităților și contractuale. Documentele sunt structurate unitar și ușor de identificat.</p><p>La cerere organizăm și completăm materialele existente cu părțile lipsă.</p>",
            "seo_title": "Documentație tehnică | Memorii și predare",
            "seo_description": "Memorii tehnice, procese-verbale și documentație de predare conform cerințelor autorităților și contractuale.",
            "focus_keyword": "documentație tehnică",
        },
        "en": {
            "title": "Technical documentation",
            "slug": "technical-documentation",
            "excerpt": "Technical descriptions, records, handover documentation and project closing materials.",
            "content": "<p>We prepare the full technical documentation for your project to meet authority and contractual requirements. Documents follow a consistent, easy-to-identify structure.</p><p>On request we also organize existing materials and fill missing parts.</p>",
            "seo_title": "Technical documentation | Specs and handover",
            "seo_description": "Technical descriptions, records and handover documentation prepared to authority and contractual requirements.",
            "focus_keyword": "technical documentation",
        },
    },
    51: {
        "ro": {
            "title": "Coordonare branșamente utilități",
            "slug": "coordonare-utilitati",
            "excerpt": "Coordonarea discuțiilor cu operatorii, a cererilor de branșare și a termenelor – într-un singur loc.",
            "content": "<p>Serviciul de coordonare a branșamentelor reunește procesele pentru apă, gaz și energie electrică. Pregătim cererile necesare, urmărim răspunsurile operatorilor și vă informăm despre pașii următori.</p>",
            "seo_title": "Coordonare utilități | Apă, gaz, electricitate",
            "seo_description": "Coordonarea discuțiilor cu operatorii, a cererilor de branșare și a termenelor – apă, gaz și energie electrică.",
            "focus_keyword": "coordonare branșamente",
        },
        "en": {
            "title": "Utility connection coordination",
            "slug": "utility-coordination",
            "excerpt": "Coordination of utility operator discussions, connection requests and deadlines in one place.",
            "content": "<p>Utility connection coordination brings together water, gas and electricity processes. We prepare the required applications, track operator responses and keep you informed about next steps.</p>",
            "seo_title": "Utility coordination | Water, gas, electricity",
            "seo_description": "Coordination of utility operator discussions, connection requests and deadlines – water, gas and electricity.",
            "focus_keyword": "utility connection coordination",
        },
    },
}

REFERENCES = {
    52: {
        "ro": {
            "title": "Extindere casă – planuri utilități",
            "slug": "extindere-casa-planuri-utilitati",
            "excerpt": "Proiectare apă, gaz și canalizare pentru extinderea unei locuințe, cu sprijin la autorizare.",
            "seo_title": "Extindere casă – planuri utilități | Referință",
            "seo_description": "Planuri de apă, gaz și canalizare pentru extinderea unei locuințe, cu sprijin la autorizare – referință BMA Engineering.",
            "focus_keyword": "planuri utilități casă",
        },
        "en": {
            "title": "House extension – utility plans",
            "slug": "house-extension-utility-plans",
            "excerpt": "Water, gas and sewage design for a residential extension, with permitting support.",
            "seo_title": "House extension – utility plans | Reference",
            "seo_description": "Water, gas and sewage plans for a residential extension with permitting support – BMA Engineering reference.",
            "focus_keyword": "house utility plans",
        },
    },
    53: {
        "ro": {
            "title": "Renovare birou – documentație tehnică",
            "slug": "renovare-birou-documentatie",
            "excerpt": "Memorii tehnice și documentație de predare pentru renovarea unui imobil de birouri.",
            "seo_title": "Renovare birou – documentație tehnică | Referință",
            "seo_description": "Memorii tehnice și documentație de predare pentru renovarea unui imobil comercial – referință BMA Engineering.",
            "focus_keyword": "documentație tehnică birou",
        },
        "en": {
            "title": "Office renovation – technical documentation",
            "slug": "office-renovation-documentation",
            "excerpt": "Technical descriptions and handover documentation for a commercial property renovation.",
            "seo_title": "Office renovation – technical documentation | Reference",
            "seo_description": "Technical descriptions and handover documentation for a commercial renovation – BMA Engineering reference.",
            "focus_keyword": "office technical documentation",
        },
    },
    54: {
        "ro": {
            "title": "Construcție nouă – pachet complet de autorizare",
            "slug": "constructie-noua-autorizare",
            "excerpt": "Pregătirea autorizației de construire pentru o clădire nouă și sprijin pe parcursul procesului.",
            "seo_title": "Construcție nouă – pachet autorizare | Referință",
            "seo_description": "Pregătirea autorizației de construire pentru o locuință nouă și sprijin pe parcursul procesului – referință BMA Engineering.",
            "focus_keyword": "pachet autorizare construire",
        },
        "en": {
            "title": "New build – full permitting package",
            "slug": "new-build-permitting-package",
            "excerpt": "Preparation of the building permit for a new residential building and support through the process.",
            "seo_title": "New build – permitting package | Reference",
            "seo_description": "Preparation of a building permit for a new home and support through the authority process – BMA Engineering reference.",
            "focus_keyword": "building permit package",
        },
    },
    55: {
        "ro": {
            "title": "Hală industrială – coordonare utilități",
            "slug": "hala-industriala-utilitati",
            "excerpt": "Coordonarea și documentarea branșamentelor pentru o investiție industrială.",
            "seo_title": "Hală industrială – coordonare utilități | Referință",
            "seo_description": "Coordonarea și documentarea branșamentelor unei investiții industriale – referință BMA Engineering.",
            "focus_keyword": "coordonare utilități industriale",
        },
        "en": {
            "title": "Industrial hall – utility coordination",
            "slug": "industrial-hall-utility-coordination",
            "excerpt": "Coordination and documentation of utility connections for an industrial investment.",
            "seo_title": "Industrial hall – utility coordination | Reference",
            "seo_description": "Coordination and documentation of utility connections for an industrial investment – BMA Engineering reference.",
            "focus_keyword": "industrial utility coordination",
        },
    },
}

TAXONOMIES = {
    5: {"ro": {"name": "Rezidențial", "slug": "rezidential"}, "en": {"name": "Residential", "slug": "residential"}},
    4: {"ro": {"name": "Business", "slug": "business"}, "en": {"name": "Business", "slug": "business"}},
}

OPTIONS = {
    "blogdescription": T(
        "Proiectare tehnică, autorizare și documentație",
        "Engineering design, permitting and technical documentation",
    ),
    "bma_opening_hours": T("Luni–Vineri, 08:00–17:00", "Monday–Friday, 08:00–17:00"),
}

SEO = {
    "homepage_title": T(
        "BMA Engineering | Proiectare tehnică și autorizare",
        "BMA Engineering | Engineering design and permitting",
    ),
    "homepage_description": T(
        "Proiectare tehnică, autorizare, documentație și coordonare utilități. Solicitați o ofertă online – BMA Engineering, Odorheiu Secuiesc.",
        "Engineering design, permitting, technical documentation and utility coordination. Request an online quote – BMA Engineering, Odorheiu Secuiesc.",
    ),
    "organization_description": T(
        "BMA Engineering oferă proiectare, autorizare, documentație tehnică și coordonare branșamente pentru proiecte rezidențiale și de business în zona Odorheiu Secuiesc și în România.",
        "BMA Engineering provides design, permitting, technical documentation and utility connection coordination for residential and business projects in the Odorheiu Secuiesc area and across Romania.",
    ),
    "breadcrumbs_home_label": T("Acasă", "Home"),
}

STATUSES = {
    1: T("Primită", "Received"),
    2: T("În procesare", "In progress"),
    3: T("Informații suplimentare necesare", "Additional information required"),
    4: T("Ofertă în pregătire", "Quote being prepared"),
    5: T("Ofertă finalizată", "Quote ready"),
    6: T("În execuție", "In delivery"),
    7: T("Documente încărcate", "Documents uploaded"),
    8: T("Închisă", "Closed"),
}

SERVICE_ITEMS = {
    33: {
        "ro": {
            "name": "Plan branșament apă",
            "short_description": "Proiectarea racordului la rețeaua de apă potabilă și a instalației interioare.",
            "choose_if": "aveți nevoie de un branșament nou de apă sau modificați rețeaua existentă.",
        },
        "en": {
            "name": "Water connection plan",
            "short_description": "Design of the potable water connection and internal water network.",
            "choose_if": "you need a new water connection or changes to the existing water network.",
        },
    },
    34: {
        "ro": {
            "name": "Plan branșament gaz",
            "short_description": "Racordarea sigură a alimentării cu gaz și a rețelei interioare.",
            "choose_if": "doriți un branșament nou de gaz sau este necesar un plan nou din cauza modificărilor.",
        },
        "en": {
            "name": "Gas connection plan",
            "short_description": "Safe gas supply connection and internal gas network design.",
            "choose_if": "you want a new gas connection or need a new plan due to system changes.",
        },
    },
    35: {
        "ro": {
            "name": "Plan branșament canalizare",
            "short_description": "Proiectarea evacuării apelor uzate și pluviale.",
            "choose_if": "realizați un branșament nou de canalizare sau modificați sistemul existent.",
        },
        "en": {
            "name": "Sewage connection plan",
            "short_description": "Design of wastewater and stormwater drainage.",
            "choose_if": "you are creating a new sewage connection or changing the existing system.",
        },
    },
    36: {
        "ro": {
            "name": "Proiectare rețea electrică",
            "short_description": "Proiectare rețea de curenți tari și evaluarea consumului.",
            "choose_if": "se realizează un sistem electric nou, se modifică unul existent sau proiectul necesită proiectare electrică.",
        },
        "en": {
            "name": "Electrical network design",
            "short_description": "Power network design and demand assessment.",
            "choose_if": "a new electrical system is built, an existing one is modified, or the project needs electrical design.",
        },
    },
    37: {
        "ro": {
            "name": "Pregătire autorizație de construire",
            "short_description": "Alcătirea documentației necesare pentru autorizația de construire.",
            "choose_if": "aveți nevoie de documentație de autorizare pentru construcție nouă, extindere sau altă lucrare autorizabilă.",
        },
        "en": {
            "name": "Building permit preparation",
            "short_description": "Compilation of documentation required for a building permit.",
            "choose_if": "you need permitting documentation for new construction, extension or another permit-required works.",
        },
    },
    38: {
        "ro": {
            "name": "Procedură de recepție",
            "short_description": "Autorizație de recepție și procese-verbale aferente.",
            "choose_if": "lucrările s-au finalizat și doriți să demarați sau să închideți recepția imobilului.",
        },
        "en": {
            "name": "Occupancy procedure",
            "short_description": "Occupancy permit and related records.",
            "choose_if": "construction works are finished and you need to start or complete occupancy handover.",
        },
    },
    39: {
        "ro": {
            "name": "Avizare / consultare primărie",
            "short_description": "Clarificări privind reglementările locale și urbanistice.",
            "choose_if": "proiectul necesită documentație, consultare sau aviz din partea autorității locale.",
        },
        "en": {
            "name": "Municipal consultation",
            "short_description": "Alignment with local planning and municipal requirements.",
            "choose_if": "the project needs municipal documentation, consultation or approval.",
        },
    },
    40: {
        "ro": {
            "name": "Memoriu tehnic",
            "short_description": "Memoriu tehnic de construcții și specificații.",
            "choose_if": "aveți nevoie de un memoriu tehnic oficial sau detaliat pentru proiect.",
        },
        "en": {
            "name": "Technical description",
            "short_description": "Construction technical description and specifications.",
            "choose_if": "you need an official or detailed technical description for the project.",
        },
    },
    41: {
        "ro": {
            "name": "Jurnal de șantier",
            "short_description": "Completarea și închiderea jurnalului de șantier.",
            "choose_if": "aveți nevoie de gestionarea jurnalului de șantier ca parte a documentației de execuție.",
        },
        "en": {
            "name": "Site diary",
            "short_description": "Keeping and closing the construction site diary.",
            "choose_if": "you need site diary management as part of construction documentation.",
        },
    },
    42: {
        "ro": {
            "name": "Proces-verbal de predare",
            "short_description": "Documentație de predare-primire.",
            "choose_if": "o lucrare, un sistem sau un proiect finalizat trebuie predat în formă documentată.",
        },
        "en": {
            "name": "Handover record",
            "short_description": "Compilation of handover documentation.",
            "choose_if": "a completed works, system or project must be handed over in documented form.",
        },
    },
    43: {
        "ro": {
            "name": "Consultare operator apă",
            "short_description": "Depunerea cererii de branșament apă / canalizare.",
            "choose_if": "aveți nevoie de branșament nou de apă sau canalizare ori de modificare a branșamentului existent.",
        },
        "en": {
            "name": "Water utility coordination",
            "short_description": "Submission of water and sewage connection requests.",
            "choose_if": "you need a new water/sewage connection or a change to an existing connection.",
        },
    },
    44: {
        "ro": {
            "name": "Consultare operator gaz",
            "short_description": "Inițierea procesului de branșament la operatorul de gaz.",
            "choose_if": "doriți un branșament nou de gaz sau este necesară interacțiune cu operatorul pentru branșamentul existent.",
        },
        "en": {
            "name": "Gas utility coordination",
            "short_description": "Starting the gas operator connection process.",
            "choose_if": "you want a new gas connection or need operator handling for an existing connection.",
        },
    },
    45: {
        "ro": {
            "name": "Consultare operator energie",
            "short_description": "Notificare putere și coordonarea branșamentului electric.",
            "choose_if": "aveți nevoie de branșament electric nou, majorare de putere sau altă modificare de rețea.",
        },
        "en": {
            "name": "Electricity utility coordination",
            "short_description": "Power demand notification and connection coordination.",
            "choose_if": "you need a new electricity connection, capacity upgrade or other network change.",
        },
    },
}

FIELDS = {
    38: {
        "ro": {"client_label": "Locația proiectului", "description": "Cod poștal, localitate, stradă, număr", "placeholder": "", "options": []},
        "en": {"client_label": "Project location", "description": "Postal code, city, street, number", "placeholder": "", "options": []},
    },
    39: {
        "ro": {"client_label": "Număr cadastral", "description": "Dacă este disponibil", "placeholder": "ex. 12345/6", "options": []},
        "en": {"client_label": "Cadastral number", "description": "If available", "placeholder": "e.g. 12345/6", "options": []},
    },
    40: {
        "ro": {"client_label": "Suprafață utilă (m²)", "description": "Suprafața netă a clădirii", "placeholder": "ex. 120", "options": []},
        "en": {"client_label": "Floor area (m²)", "description": "Net building floor area", "placeholder": "e.g. 120", "options": []},
    },
    41: {
        "ro": {"client_label": "Număr de niveluri", "description": "Subsol, parter, etaj împreună", "placeholder": "ex. 2", "options": []},
        "en": {"client_label": "Number of floors", "description": "Basement, ground floor and upper floors together", "placeholder": "e.g. 2", "options": []},
    },
    42: {
        "ro": {"client_label": "Faza construcției", "description": "", "placeholder": "", "options": ["Construcție nouă", "Renovare", "Extindere", "Transformare"]},
        "en": {"client_label": "Construction phase", "description": "", "placeholder": "", "options": ["New build", "Renovation", "Extension", "Alteration"]},
    },
    43: {
        "ro": {"client_label": "Observații suplimentare", "description": "Alte informații importante despre proiect", "placeholder": "", "options": []},
        "en": {"client_label": "Additional notes", "description": "Other important project information", "placeholder": "", "options": []},
    },
    44: {
        "ro": {"client_label": "Încărcare plan / releveu", "description": "PDF, DWG, JPG – max. 20 MB", "placeholder": "", "options": []},
        "en": {"client_label": "Upload plan / floor plan", "description": "PDF, DWG, JPG – max. 20 MB", "placeholder": "", "options": []},
    },
    45: {
        "ro": {"client_label": "Necesar estimat de apă (m³/zi)", "description": "Pentru planul de branșament apă", "placeholder": "ex. 0.5", "options": []},
        "en": {"client_label": "Estimated water demand (m³/day)", "description": "For water connection plan", "placeholder": "e.g. 0.5", "options": []},
    },
    46: {
        "ro": {"client_label": "Tip alimentare gaz", "description": "Pentru planul de branșament gaz", "placeholder": "", "options": ["Gaze naturale (rețea)", "GPL", "În proiectare"]},
        "en": {"client_label": "Gas supply type", "description": "For gas connection plan", "placeholder": "", "options": ["Natural gas (grid)", "LPG", "Under design"]},
    },
    47: {
        "ro": {"client_label": "Necesar de putere (kW)", "description": "Pentru proiectarea rețelei electrice", "placeholder": "ex. 14", "options": []},
        "en": {"client_label": "Power demand (kW)", "description": "For electrical network design", "placeholder": "e.g. 14", "options": []},
    },
    48: {
        "ro": {"client_label": "Funcțiunea clădirii", "description": "", "placeholder": "", "options": ["Locuință", "Birou", "Comercial", "Industrial", "Altele"]},
        "en": {"client_label": "Building function", "description": "", "placeholder": "", "options": ["Residential", "Office", "Commercial", "Industrial", "Other"]},
    },
    49: {
        "ro": {"client_label": "Termen dorit", "description": "Dacă există o dată preferată de predare", "placeholder": "", "options": []},
        "en": {"client_label": "Desired deadline", "description": "If there is a preferred delivery date", "placeholder": "", "options": []},
    },
    50: {
        "ro": {"client_label": "Scopul documentației", "description": "", "placeholder": "", "options": ["Depunere la autorități", "Predare către investitor", "Uz intern"]},
        "en": {"client_label": "Documentation purpose", "description": "", "placeholder": "", "options": ["Authority submission", "Investor handover", "Internal use"]},
    },
    51: {
        "ro": {"client_label": "Încărcare document existent", "description": "PDF, DOC, ZIP – max. 20 MB", "placeholder": "", "options": []},
        "en": {"client_label": "Upload existing document", "description": "PDF, DOC, ZIP – max. 20 MB", "placeholder": "", "options": []},
    },
    52: {
        "ro": {"client_label": "Prioritatea proiectului", "description": "", "placeholder": "", "options": ["Normală", "Urgentă", "Termen flexibil"]},
        "en": {"client_label": "Project priority", "description": "", "placeholder": "", "options": ["Normal", "Urgent", "Flexible deadline"]},
    },
    53: {
        "ro": {"client_label": "E-mail contact pe șantier", "description": "Dacă diferă de datele solicitantului", "placeholder": "exemplu@email.ro", "options": []},
        "en": {"client_label": "On-site contact email", "description": "If different from the requester details", "placeholder": "example@email.com", "options": []},
    },
    54: {
        "ro": {"client_label": "Panouri fotovoltaice", "description": "", "placeholder": "", "options": ["Da", "Nu"]},
        "en": {"client_label": "Solar panels", "description": "", "placeholder": "", "options": ["Yes", "No"]},
    },
}

EMAILS = {}
for row in HU.get("email_templates", []):
    sid = int(row["id"])
    EMAILS[sid] = {
        "ro": {
            "subject": row["subject"]
            .replace("Ajánlatkérés visszaigazolás", "Confirmare cerere de ofertă")
            .replace("Új ajánlatkérés", "Cerere de ofertă nouă")
            .replace("Ajánlatkérés státusz frissítés", "Actualizare status cerere de ofertă")
            .replace("További információ szükséges", "Informații suplimentare necesare")
            .replace("Ajánlat elérhető", "Ofertă disponibilă")
            .replace("Új dokumentum", "Document nou")
            .replace("Elkészült dokumentumok", "Documente finalizate"),
            "body": (
                row["body"]
                .replace("Kedves", "Stimate/Stimată")
                .replace("Köszönjük ajánlatkérését", "Vă mulțumim pentru cererea de ofertă")
                .replace("Azonosító", "Identificator")
                .replace("Szolgáltatások", "Servicii")
                .replace("Ajánlatkérését feldolgozzuk", "Cererea dvs. este în curs de procesare")
                .replace("Az aktuális állapotot az ügyfélportálon követheti", "Puteți urmări statusul în portalul clienți")
                .replace("Üdvözlettel", "Cu stimă")
                .replace("Új ajánlatkérés érkezett", "A sosit o cerere de ofertă nouă")
                .replace("Ügyféladatok", "Date client")
                .replace("Admin felület", "Interfață admin")
                .replace("Ajánlatkérése", "Cererea dvs. de ofertă")
                .replace("státusza megváltozott", "și-a schimbat statusul")
                .replace("Részletek", "Detalii")
                .replace("Ajánlatkéréséhez", "Pentru cererea dvs. de ofertă")
                .replace("további információra van szükségünk", "avem nevoie de informații suplimentare")
                .replace("Kérjük, lépjen kapcsolatba velünk, vagy töltse fel a hiányzó dokumentumokat az ügyfélportálon", "Vă rugăm să ne contactați sau să încărcați documentele lipsă în portalul clienți")
                .replace("elkészült az ajánlat", "oferta a fost finalizată")
                .replace("Bruttó összeg", "Sumă brută")
                .replace("Érvényesség", "Valabilitate")
                .replace("Ajánlat megtekintése", "Vizualizare ofertă")
                .replace("Új dokumentum került feltöltésre az ajánlatkéréséhez", "Un document nou a fost încărcat pentru cererea dvs. de ofertă")
                .replace("Megtekintés", "Vizualizare")
                .replace("Az ajánlatkéréshez", "Pentru cererea de ofertă")
                .replace("tartozó elkészült tervek és dokumentumok elérhetők az ügyfélportálon", "planurile și documentele finalizate sunt disponibile în portalul clienți")
                .replace("[KAPCSOLATI ADAT]", "[DATE DE CONTACT]")
            ),
        },
        "en": {
            "subject": row["subject"]
            .replace("Ajánlatkérés visszaigazolás", "Quote request confirmation")
            .replace("Új ajánlatkérés", "New quote request")
            .replace("Ajánlatkérés státusz frissítés", "Quote request status update")
            .replace("További információ szükséges", "Additional information required")
            .replace("Ajánlat elérhető", "Quote available")
            .replace("Új dokumentum", "New document")
            .replace("Elkészült dokumentumok", "Documents ready"),
            "body": (
                row["body"]
                .replace("Kedves", "Dear")
                .replace("Köszönjük ajánlatkérését", "Thank you for your quote request")
                .replace("Azonosító", "Reference")
                .replace("Szolgáltatások", "Services")
                .replace("Ajánlatkérését feldolgozzuk", "We are processing your request")
                .replace("Az aktuális állapotot az ügyfélportálon követheti", "You can track the current status in the client portal")
                .replace("Üdvözlettel", "Kind regards")
                .replace("Új ajánlatkérés érkezett", "A new quote request has arrived")
                .replace("Ügyféladatok", "Client details")
                .replace("Admin felület", "Admin area")
                .replace("Ajánlatkérése", "Your quote request")
                .replace("státusza megváltozott", "status has changed")
                .replace("Részletek", "Details")
                .replace("Ajánlatkéréséhez", "For your quote request")
                .replace("további információra van szükségünk", "we need additional information")
                .replace("Kérjük, lépjen kapcsolatba velünk, vagy töltse fel a hiányzó dokumentumokat az ügyfélportálon", "Please contact us or upload the missing documents in the client portal")
                .replace("elkészült az ajánlat", "the quote is ready")
                .replace("Bruttó összeg", "Gross amount")
                .replace("Érvényesség", "Valid until")
                .replace("Ajánlat megtekintése", "View quote")
                .replace("Új dokumentum került feltöltésre az ajánlatkéréséhez", "A new document has been uploaded for your quote request")
                .replace("Megtekintés", "View")
                .replace("Az ajánlatkéréshez", "For quote request")
                .replace("tartozó elkészült tervek és dokumentumok elérhetők az ügyfélportálon", "the finished plans and documents are available in the client portal")
                .replace("[KAPCSOLATI ADAT]", "[CONTACT DETAILS]")
            ),
        },
    }

# Full public UI catalog (HU → RO/EN). Prefer docs/i18n/ui-strings-complete.json.
_UI_COMPLETE = OUT / "ui-strings-complete.json"
UI: dict[str, dict[str, str]] = {}
if _UI_COMPLETE.exists():
    _raw = json.loads(_UI_COMPLETE.read_text(encoding="utf-8"))
    for _src, _tr in _raw.items():
        if isinstance(_tr, dict) and _tr.get("ro") and _tr.get("en"):
            UI[str(_src)] = T(str(_tr["ro"]), str(_tr["en"]))
# Tiny fallback seed if complete file is missing.
if not UI:
    UI = {
        "Ajánlatkérés indítása": T("Începeți cererea de ofertă", "Start quote request"),
        "Referenciák": T("Referințe", "References"),
        "Szolgáltatások": T("Servicii", "Services"),
        "Kapcsolat": T("Contact", "Contact"),
        "Főoldal": T("Acasă", "Home"),
        "Belépés": T("Autentificare", "Log in"),
        "Bejelentkezés": T("Autentificare", "Log in"),
        "Kijelentkezés": T("Deconectare", "Log out"),
        "Ügyfélportál": T("Portal clienți", "Client portal"),
        "Ajánlatkérés": T("Cerere de ofertă", "Request a quote"),
        "Ajánlat": T("Ofertă", "Quote"),
    }


def build() -> dict:
    catalog = {
        "meta": {
            "source": "hu",
            "targets": ["ro", "en"],
            "note": "Polylang posts/taxonomies + bma_i18n_overlay for DB rows + gettext .po for UI",
        },
        "pages": {str(k): v for k, v in PAGES.items()},
        "services": {str(k): v for k, v in SERVICES.items()},
        "references": {str(k): v for k, v in REFERENCES.items()},
        "taxonomies": {str(k): v for k, v in TAXONOMIES.items()},
        "options": OPTIONS,
        "seo": SEO,
        "statuses": {str(k): v for k, v in STATUSES.items()},
        "service_items": {str(k): v for k, v in SERVICE_ITEMS.items()},
        "dynamic_fields": {str(k): v for k, v in FIELDS.items()},
        "email_templates": {str(k): v for k, v in EMAILS.items()},
        "ui_strings": UI,
        "source_snapshot": {
            "pages": len(HU.get("pages", [])),
            "services": len(HU.get("services", [])),
            "references": len(HU.get("references", [])),
            "service_items": len(DB.get("service_items", [])),
            "dynamic_fields": len(DB.get("dynamic_fields", [])),
        },
    }
    return catalog


def write_readable(catalog: dict) -> None:
    lines = ["# BMA Engineering – fordítások (RO / EN)", ""]
    for section in ("pages", "services", "references"):
        lines.append(f"## {section}")
        for sid, langs in catalog[section].items():
            hu_title = next((p["title"] for p in HU.get(section if section != "pages" else "pages", []) if str(p["id"]) == sid), sid)
            if section == "pages":
                hu_title = next((p["title"] for p in HU["pages"] if str(p["id"]) == sid), sid)
            elif section == "services":
                hu_title = next((p["title"] for p in HU["services"] if str(p["id"]) == sid), sid)
            else:
                hu_title = next((p["title"] for p in HU["references"] if str(p["id"]) == sid), sid)
            lines.append(f"### HU: {hu_title} (ID {sid})")
            for lang in ("ro", "en"):
                d = langs[lang]
                lines.append(f"- **{lang.upper()}** `{d.get('slug','')}`: {d.get('title','')}")
                if d.get("excerpt"):
                    lines.append(f"  - {d['excerpt']}")
                if d.get("seo_title"):
                    lines.append(f"  - SEO: {d['seo_title']} — {d.get('seo_description','')}")
            lines.append("")
    lines.append("## Státuszok / opciók / UI (részlet)")
    for sid, langs in catalog["statuses"].items():
        lines.append(f"- status {sid}: RO «{langs['ro']}» | EN «{langs['en']}»")
    (OUT / "translations.md").write_text("\n".join(lines) + "\n", encoding="utf-8")


def main() -> None:
    OUT.mkdir(parents=True, exist_ok=True)
    catalog = build()
    (OUT / "translations.json").write_text(
        json.dumps(catalog, ensure_ascii=False, indent=2) + "\n", encoding="utf-8"
    )
    write_readable(catalog)
    # also copy machine JSON into wp-content export folder for WP import
    wp_dir = ROOT / "wp-content" / "bma-i18n-export"
    wp_dir.mkdir(parents=True, exist_ok=True)
    (wp_dir / "translations.json").write_text(
        json.dumps(catalog, ensure_ascii=False, indent=2) + "\n", encoding="utf-8"
    )
    (wp_dir / "translations.md").write_text((OUT / "translations.md").read_text(encoding="utf-8"), encoding="utf-8")
    print(f"Wrote {OUT / 'translations.json'} and readable markdown")


if __name__ == "__main__":
    main()
