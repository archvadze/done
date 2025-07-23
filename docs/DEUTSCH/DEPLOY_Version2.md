# Acumen Craft – Deployment- und Installationsanleitung

_Letztes Update: 22.07.2025_

---

## 1. Voraussetzungen

- **Server:** Ubuntu 22.04+ oder vergleichbar (empfohlen: 4+ CPU, 8+ GB RAM)
- **Software:**  
  - PHP 8.2+ (mit Extensions: pdo, mbstring, openssl, curl, gd, xml, zip)
  - Composer 2.x
  - Node.js 20+, npm/yarn
  - MariaDB 10.6+ oder MySQL 8+
  - Redis
  - Git
  - (Optional: Docker, Docker Compose)
- **Zusätzliche Dienste:**  
  - E-Mail SMTP Zugangsdaten
  - S3-kompatibler Storage (z.B. AWS S3, Minio)
  - Blockchain/RPC Endpoint (Ethereum, Polygon – für NFT)

---

## 2. Vorbereitung

1. **Repository klonen**
   ```bash
   git clone https://github.com/acumencraft/done.git
   cd done
   ```

2. **Umgebungsvariablen kopieren und anpassen**
   ```bash
   cp .env.example .env
   ```
   Passe die Werte in `.env` an (Datenbank, Mail, Storage, Blockchain, etc).

3. **Abhängigkeiten installieren**
   ```bash
   composer install --no-dev --optimize-autoloader
   npm install
   npm run build
   ```

---

## 3. Datenbank & Storage

1. **Migrationen & Seeders ausführen**
   ```bash
   php artisan migrate --seed
   ```

2. **Storage verlinken**
   ```bash
   php artisan storage:link
   ```

---

## 4. Schlüssel & Sicherheit

- **App Key generieren**
  ```bash
  php artisan key:generate
  ```
- **JWT-Secret generieren (falls benötigt)**
  ```bash
  php artisan jwt:secret
  ```

- **Datei- und Ordnerrechte setzen**
  ```bash
  chmod -R 775 storage bootstrap/cache
  chown -R www-data:www-data storage bootstrap/cache
  ```

---

## 5. Starten / Deployment

- **Lokale Entwicklung**
  ```bash
  php artisan serve
  ```

- **Produktiv mit Supervisor/pm2/Process Manager**
  - Webserver (z.B. nginx oder Apache) auf `/public`-Verzeichnis zeigen lassen
  - Beispiel nginx-Konfiguration siehe `/deploy/nginx.example.conf`
  - Hintergrundjobs/Queues via Supervisor oder Laravel Horizon starten:
    ```bash
    php artisan queue:work
    ```

- **Mit Docker (optional)**
  ```bash
  docker compose up --build -d
  ```

---

## 6. Monitoring & Wartung

- **Logs prüfen:** `storage/logs/laravel.log`
- **Gesundheitschecks:** `/api/health` Endpoint verwenden
- **Backups:** Siehe [DISASTER_RECOVERY.md](./DISASTER_RECOVERY.md)
- **Automatisierte Tests:**  
  ```bash
  php artisan test
  npm run test
  ```

---

## 7. CI/CD (GitHub Actions)

- Jede Änderung in `main`/`develop` triggert automatisierte Tests und Deployment (je nach Workflow).  
- Siehe `.github/workflows/` für Konfigurationsdetails.

---

## 8. Updates & Migrationen

- Bei neuen Versionen:
  ```bash
  git pull origin main
  composer install --no-dev
  npm install && npm run build
  php artisan migrate
  ```

---

## 9. Fehlerbehebung

- **Leerer Bildschirm:** `APP_DEBUG=true` in `.env` setzen, Logs prüfen
- **Datenbankfehler:** Credentials & Migrationen prüfen
- **Storage/Uploads:** Rechte & S3-Konfiguration prüfen
- **Mailversand:** SMTP-Logs und .env prüfen

---

## 10. Support

Probleme oder Fragen?  
- Siehe [FAQ.md](./FAQ.md)
- Erstelle ein [GitHub Issue](https://github.com/acumencraft/done/issues)
- Kontaktiere das Team: support@acumencraft.com

---