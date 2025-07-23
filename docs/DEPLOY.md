# Acumen Craft – Deployment Guide

_Last updated: 2025-07-22_

---

## Overview

This guide describes the recommended process for deploying Acumen Craft across development, staging, and production environments.

---

## 1. Prerequisites

- **Server:** Ubuntu 22.04+ (or compatible), 2+ vCPU, 4GB+ RAM
- **Stack:** PHP 8.2+, Composer, Node.js 18+, npm/yarn, MariaDB/MySQL 10.6+, Redis, Nginx/Apache
- **Domain/DNS:** Configured for the target environment
- **SSL:** Recommended (Let's Encrypt or custom)
- **Cloud:** Supports AWS, GCP, Azure, DigitalOcean, or on-premise

---

## 2. Environment Preparation

1. **System Update & Packages:**
   ```bash
   sudo apt update && sudo apt upgrade
   sudo apt install nginx php8.2 php8.2-fpm php8.2-mysql php8.2-xml php8.2-curl \
     php8.2-mbstring php8.2-zip php8.2-gd php8.2-bcmath git unzip redis-server
   curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
   curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
   sudo apt install nodejs
   ```

2. **Database:**
   - Install and secure MariaDB/MySQL
   - Create database and user:
     ```sql
     CREATE DATABASE acumen_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
     CREATE USER 'acumen_user'@'localhost' IDENTIFIED BY 'strongpassword';
     GRANT ALL PRIVILEGES ON acumen_db.* TO 'acumen_user'@'localhost';
     FLUSH PRIVILEGES;
     ```

3. **Clone Repo & App Setup:**
   ```bash
   git clone https://github.com/acumencraft/done.git
   cd done
   cp .env.example .env
   # Edit .env for DB, APP_URL, MAIL, etc.
   composer install --no-dev --optimize-autoloader
   npm install
   npm run build
   ```

---

## 3. Application Setup

1. **Key Generation:**
   ```bash
   php artisan key:generate
   ```

2. **Database Migration & Seed:**
   ```bash
   php artisan migrate --force --seed
   ```

3. **Storage Permissions:**
   ```bash
   sudo chown -R www-data:www-data storage bootstrap/cache
   sudo chmod -R 775 storage bootstrap/cache
   ```

4. **Caching:**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

5. **Queue & Schedule:**
   - Start queue worker (supervised mode recommended):
     ```bash
     php artisan queue:work --daemon
     ```
   - Set up cron for scheduler:
     ```
     * * * * * cd /path/to/done && php artisan schedule:run >> /dev/null 2>&1
     ```

---

## 4. Web Server Configuration

- **Nginx Example:**
  ```nginx
  server {
      listen 80;
      server_name yourdomain.com;
      root /path/to/done/public;
      index index.php index.html;

      location / {
          try_files $uri $uri/ /index.php?$query_string;
      }

      location ~ \.php$ {
          include snippets/fastcgi-php.conf;
          fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
      }

      location ~ /\.ht {
          deny all;
      }

      client_max_body_size 64M;
  }
  ```

- **SSL (Let's Encrypt):**
  ```bash
  sudo apt install certbot python3-certbot-nginx
  sudo certbot --nginx -d yourdomain.com
  ```

---

## 5. Production Considerations

- **Debug:** Ensure `APP_DEBUG=false` in `.env`
- **Logging:** Set up log rotation and external monitoring (e.g., Sentry, Loggly)
- **Backups:** Enable automated DB and storage backups (see [DISASTER_RECOVERY.md](./docs/DISASTER_RECOVERY.md))
- **Scaling:** Use load balancer, horizontal scaling, and Redis for sessions/queues in high-traffic use

---

## 6. Deployment Automation (Optional)

- **CI/CD:** Use GitHub Actions, GitLab CI, or similar for automated tests and deployments
- **Zero-downtime:** Use `php artisan down`/`up` or deploy with blue/green strategy
- **Rolling Updates:** Staging environment for QA before production

---

## 7. Mobile App Deployment

- See [MOBILE_GUIDE.md](./docs/MOBILE_GUIDE.md) for Flutter/React Native build, test, and store publishing steps.

---

## 8. Post-Deployment Checklist

- [ ] Site loads over HTTPS and redirects from HTTP
- [ ] Database seeded and up-to-date
- [ ] Background workers active
- [ ] Email and notifications tested
- [ ] API endpoints reachable (test with [API_TESTS.md](./docs/API_TESTS.md))
- [ ] Monitoring and alerts enabled
- [ ] Backups scheduled

---

## 9. Troubleshooting

- Check logs in `storage/logs/`
- Test permissions and DB connectivity
- Use `php artisan config:clear` and `php artisan cache:clear` after changes

---

## 10. References

- [DATABASE.md](./docs/DATABASE.md)
- [API_TESTS.md](./docs/API_TESTS.md)
- [DISASTER_RECOVERY.md](./docs/DISASTER_RECOVERY.md)
- [CONTRIBUTING.md](./docs/CONTRIBUTING.md)

---

_Questions? Contact devops@acumencraft.com_
