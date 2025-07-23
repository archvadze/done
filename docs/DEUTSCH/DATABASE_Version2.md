# Acumen Craft – Datenbankschema

_Letztes Update: 22.07.2025_

---

## Übersicht

Die folgende Dokumentation beschreibt die Haupttabellen und Beziehungen der Datenbankstruktur von Acumen Craft.  
Das tatsächliche Schema kann je nach Version oder Deployment leicht variieren.

---

## 1. Nutzer:innen (`users`)

| Feld               | Typ            | Beschreibung                  |
|--------------------|----------------|-------------------------------|
| id                 | BIGINT         | Primärschlüssel               |
| name               | VARCHAR(100)   | Anzeigename                   |
| username           | VARCHAR(40)    | Eindeutiger Nutzername        |
| email              | VARCHAR(120)   | Eindeutige E-Mail             |
| password           | VARCHAR(255)   | Passwort-Hash                 |
| avatar_url         | VARCHAR(255)   | Profilbild                    |
| bio                | TEXT           | Nutzer-Biografie              |
| is_verified        | BOOL           | Verifizierter Account         |
| role               | ENUM           | user, moderator, admin        |
| two_factor_secret  | VARCHAR(255)   | 2FA-Secret (optional)         |
| created_at         | TIMESTAMP      | Erstellungsdatum              |
| updated_at         | TIMESTAMP      | Letzte Änderung               |

---

## 2. Werke (`artworks`)

| Feld            | Typ            | Beschreibung                      |
|-----------------|----------------|-----------------------------------|
| id              | BIGINT         | Primärschlüssel                   |
| user_id         | BIGINT         | Verweis auf Nutzer                |
| title           | VARCHAR(120)   | Titel des Werks                   |
| description     | TEXT           | Beschreibung                      |
| type            | ENUM           | image, music, video, ai, other    |
| file_url        | VARCHAR(255)   | Speicherort/URL                   |
| thumbnail_url   | VARCHAR(255)   | Vorschau                          |
| is_nft          | BOOL           | NFT-Status                        |
| nft_token_id    | VARCHAR(80)    | Blockchain Token ID (optional)     |
| blockchain      | ENUM           | ethereum, polygon, none           |
| status          | ENUM           | active, archived, deleted         |
| created_at      | TIMESTAMP      | Upload-Datum                      |
| updated_at      | TIMESTAMP      | Letzte Änderung                   |

---

## 3. Bewertungen (`ratings`)

| Feld         | Typ         | Beschreibung                         |
|--------------|-------------|--------------------------------------|
| id           | BIGINT      | Primärschlüssel                      |
| artwork_id   | BIGINT      | Verweis auf Werk                     |
| user_id      | BIGINT      | Verweis auf Nutzer                   |
| aiq_score    | FLOAT       | ACQ-Score (AI/Community)             |
| technique    | TINYINT     | 1-10 (Technik)                       |
| originality  | TINYINT     | 1-10 (Originalität)                  |
| composition  | TINYINT     | 1-10 (Komposition)                   |
| impact       | TINYINT     | 1-10 (Wirkung)                       |
| comment      | TEXT        | Optionaler Kommentar                 |
| created_at   | TIMESTAMP   | Bewertungsdatum                      |

---

## 4. Zahlungen & Monetarisierung (`payments`)

| Feld         | Typ            | Beschreibung                        |
|--------------|----------------|-------------------------------------|
| id           | BIGINT         | Primärschlüssel                     |
| user_id      | BIGINT         | Empfänger                           |
| artwork_id   | BIGINT         | Optional: Bezug zu Werk             |
| amount       | DECIMAL(12,2)  | Betrag                              |
| currency     | VARCHAR(10)    | Währung (z.B. EUR, USD, ETH)        |
| method       | ENUM           | stripe, paypal, crypto              |
| status       | ENUM           | pending, completed, failed          |
| tx_id        | VARCHAR(255)   | Transaktions-ID                     |
| created_at   | TIMESTAMP      | Zahlzeitpunkt                       |

---

## 5. NFT & Blockchain (`nfts`)

| Feld         | Typ          | Beschreibung                         |
|--------------|--------------|--------------------------------------|
| id           | BIGINT       | Primärschlüssel                      |
| artwork_id   | BIGINT       | Bezug zu Werk                        |
| owner_id     | BIGINT       | Aktueller Besitzer                   |
| token_id     | VARCHAR(80)  | Blockchain Token ID                  |
| contract     | VARCHAR(255) | Smart Contract Adresse               |
| chain        | ENUM         | ethereum, polygon                    |
| minted_at    | TIMESTAMP    | Minting-Datum                        |
| metadata_url | VARCHAR(255) | Link zu NFT-Metadaten                |

---

## 6. Kommentare (`comments`)

| Feld         | Typ         | Beschreibung                         |
|--------------|-------------|--------------------------------------|
| id           | BIGINT      | Primärschlüssel                      |
| artwork_id   | BIGINT      | Bezug zu Werk                        |
| user_id      | BIGINT      | Bezug zu Nutzer                      |
| comment      | TEXT        | Kommentartext                        |
| created_at   | TIMESTAMP   | Kommentarzeitpunkt                   |

---

## 7. Protokoll & Moderation (`logs`, `reports`, `moderations`)

**Beispiele:**

### `logs`
| id | user_id | action | target_type | target_id | data | created_at |
|----|---------|--------|-------------|-----------|------|------------|

### `reports`
| id | reporter_id | target_type | target_id | reason | status | created_at |

### `moderations`
| id | moderator_id | report_id | action | note | created_at |

---

## 8. Sonstige Tabellen

- `sessions` – Login-Sessions, Tokens
- `webhooks` – API- und Integrations-Logs
- `settings` – Plattformkonfiguration
- `files` – Medienverwaltung (bei Multi-Upload)
- `notifications` – Benachrichtigungen

---

## Beziehungen (Vereinfachtes ER-Diagramm)

- **users** 1---n **artworks**
- **artworks** 1---n **ratings**
- **artworks** 1---n **comments**
- **artworks** 1---1 **nfts**
- **users** 1---n **payments**
- **users** 1---n **notifications**

---

_Änderungen und Erweiterungen sind je nach Release möglich. Für detaillierte Migrations siehe `/database/migrations/` im Repository._
