# Acumen Craft

Acumen Craft ist eine moderne Webplattform für Kreativität, Bewertung und Monetarisierung, auf der Künstler:innen, Designer:innen, Fotograf:innen und KI-Kreative ihre Werke teilen, bewerten lassen und ihre Rechte schützen können.

---

## 🌟 Hauptfunktionen

- **Hochladen und Teilen von Werken** – Unterstützung für Kunst, Musik, Fotografie, Video und KI-generierte Inhalte
- **Community- und KI-Bewertungen** – Bewertung nach Technik, Originalität, Komposition und Wirkung (ACQ-Score)
- **NFT-Minting & Blockchain-Integration** – Schutz und Monetarisierung von Werken auf Ethereum/Polygon
- **Monetarisierung & Zahlungen** – Spenden, Verkauf, Stripe, PayPal und Kryptowährungen
- **Datenschutz & Sicherheit** – Schutz der Daten (DSGVO/CCPA-Konformität, 2FA), Lizenzverwaltung und Urheberrechtsschutz
- **Mobile App** – iOS-/Android-Apps (Flutter/React Native)
- **Webhooks & API** – Vollständig dokumentierte RESTful API und Integrationen

---

## 🚀 Installation (lokal)

### Anforderungen

- PHP 8.2+, Composer
- Node.js 18+, npm oder yarn
- MariaDB/MySQL 10.6+, Redis
- Nginx oder Apache

### Schnellstart

```bash
git clone https://github.com/acumencraft/done.git
cd acumencraft
cp .env.example .env   # .env Datei konfigurieren
composer install
npm install && npm run build
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

---

## 🧩 Architektur

- **Backend:** Laravel (PHP), RESTful API, JWT-Authentifizierung
- **Frontend:** React + Tailwind CSS
- **Mobile:** Flutter oder React Native
- **Storage:** S3/Blob, CDN, MariaDB/MySQL, Redis
- **CI/CD:** GitHub Actions, Docker (optional)
- **Blockchain:** Web3.js, ethers.js, NFT-Smart Contracts (Ethereum/Polygon)

---

## 📑 Zentrale Dokumentation

- [DATABASE.md](./DATABASE.md) — Datenbankschema
- [swagger.yaml](./swagger.yaml) — API-Dokumentation
- [WEBHOOKS.md](./WEBHOOKS.md) — Beschreibung der Webhooks
- [MOBILE_GUIDE.md](./MOBILE_GUIDE.md) — Mobile App Leitfaden
- [UI_MOCKUPS.md](./UI_MOCKUPS.md) — UI-Struktur und Mockups
- [PRIVACY.md](./PRIVACY.md) — Datenschutzerklärung
- [SECURITY.md](./SECURITY.md) — Sicherheitsleitfaden
- [DISASTER_RECOVERY.md](./DISASTER_RECOVERY.md) — Notfallwiederherstellungsplan
- [DEPLOY.md](./DEPLOY.md) — Deployment-Anleitung
- [FAQ.md](./FAQ.md) — Häufig gestellte Fragen

---

## 💡 Mitwirken

Open-Source-Contributions sind willkommen!  
Siehe [CONTRIBUTING.md](./CONTRIBUTING.md) und [CODE_OF_CONDUCT.md](./CODE_OF_CONDUCT.md).  
Pull Requests, Bug-Reports und Ideen sind gerne gesehen.

---

## 🛡️ Lizenz

MIT License — siehe [LICENSE.md](./LICENSE.md)  
Drittanbieter-Lizenzen: [THIRD_PARTY_NOTICES.md](./THIRD_PARTY_NOTICES.md)

---

## 📞 Support und Kontakt

- support@acumencraft.com — Technischer Support und Anfragen
- [GitHub Issues](https://github.com/acumencraft/done/issues) — Bug-Reports und Feature Requests

---

Acumen Craft — Deine Möglichkeit, kreativen Mehrwert zu schaffen!