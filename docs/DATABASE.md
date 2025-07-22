# Acumen Craft – Database Schema & Guide

_Last updated: 2025-07-22_

---

## Overview

This document summarizes the main database structure and best practices for Acumen Craft.  
The official schema is maintained in SQL migration files (see `db.sql`), but this guide provides a high-level, human-readable overview.

---

## 1. Core Tables

### users

| Field            | Type           | Description                              |
|------------------|----------------|------------------------------------------|
| id               | BIGINT, PK     | Unique user ID                           |
| name             | VARCHAR(128)   | Display/full name                        |
| email            | VARCHAR(255)   | Email (unique, indexed)                  |
| password_hash    | VARCHAR(255)   | Bcrypt/argon2 hash                       |
| avatar_url       | VARCHAR(255)   | Profile image                            |
| bio              | TEXT           | Short biography                          |
| country          | VARCHAR(2)     | ISO country code                         |
| lang             | VARCHAR(5)     | Preferred language code                  |
| oauth_google     | VARCHAR(128)   | Google OAuth sub                         |
| oauth_facebook   | VARCHAR(128)   | Facebook OAuth sub                       |
| oauth_apple      | VARCHAR(128)   | Apple OAuth sub                          |
| twofa_secret     | VARCHAR(128)   | TOTP secret (encrypted)                  |
| is_admin         | BOOL           | Admin privileges                         |
| status           | ENUM           | active, banned, pending, deleted         |
| created_at       | TIMESTAMP      |                                          |
| updated_at       | TIMESTAMP      |                                          |

### artworks

| Field           | Type           | Description                              |
|-----------------|----------------|------------------------------------------|
| id              | BIGINT, PK     | Unique artwork ID                        |
| user_id         | BIGINT, FK     | Owner (users.id)                         |
| title_en        | VARCHAR(255)   | Title (English)                          |
| title_de        | VARCHAR(255)   | Title (German)                           |
| description_en  | TEXT           | Description (English)                    |
| description_de  | TEXT           | Description (German)                     |
| media_url       | VARCHAR(255)   | Media file URL                           |
| media_type      | ENUM           | image, audio, video, other               |
| tags            | VARCHAR(255)   | Comma-separated, indexed                 |
| license         | VARCHAR(64)    | License type (e.g., CC BY-SA)            |
| copyright_notice| TEXT           | Copyright/attribution text               |
| is_ai_generated | BOOL           | AI-generated flag                        |
| nft_token_id    | VARCHAR(128)   | Blockchain token ID (nullable)           |
| blockchain      | VARCHAR(32)    | ETH, Polygon, etc. (nullable)            |
| visibility      | ENUM           | public, private, draft                   |
| acq_score       | DECIMAL(5,2)   | ACQ score (manual/AI evaluation)         |
| created_at      | TIMESTAMP      |                                          |
| updated_at      | TIMESTAMP      |                                          |

### evaluations

| Field         | Type           | Description                              |
|---------------|----------------|------------------------------------------|
| id            | BIGINT, PK     | Unique evaluation ID                     |
| artwork_id    | BIGINT, FK     | Evaluated artwork                        |
| user_id       | BIGINT, FK     | Evaluator (nullable for AI)              |
| technique     | INT            | 1-10                                     |
| composition   | INT            | 1-10                                     |
| originality   | INT            | 1-10                                     |
| impact        | INT            | 1-10                                     |
| feedback      | TEXT           | Free-form feedback                       |
| type          | ENUM           | manual, ai                               |
| created_at    | TIMESTAMP      |                                          |

### comments

| Field         | Type           | Description                              |
|---------------|----------------|------------------------------------------|
| id            | BIGINT, PK     | Unique comment ID                        |
| artwork_id    | BIGINT, FK     | Related artwork                          |
| user_id       | BIGINT, FK     | Comment author                           |
| content       | TEXT           | Comment text                             |
| status        | ENUM           | visible, hidden, deleted                 |
| created_at    | TIMESTAMP      |                                          |

---

## 2. Payments & Crypto

### payments

| Field         | Type           | Description                              |
|---------------|----------------|------------------------------------------|
| id            | BIGINT, PK     | Unique payment ID                        |
| user_id       | BIGINT, FK     | User receiving payment                   |
| amount        | DECIMAL(10,2)  | Amount (in USD or specified currency)    |
| currency      | VARCHAR(8)     | USD, EUR, ETH, USDT, BTC, etc.           |
| method        | ENUM           | stripe, paypal, crypto                   |
| tx_ref        | VARCHAR(255)   | Transaction reference/ID                 |
| status        | ENUM           | pending, completed, failed, refunded     |
| created_at    | TIMESTAMP      |                                          |

### wallets

| Field         | Type           | Description                              |
|---------------|----------------|------------------------------------------|
| id            | BIGINT, PK     | Wallet ID                                |
| user_id       | BIGINT, FK     | Owner                                    |
| address       | VARCHAR(128)   | Public wallet address                    |
| blockchain    | VARCHAR(32)    | ETH, Polygon, BTC, etc.                  |
| verified      | BOOL           | Ownership proof status                   |
| created_at    | TIMESTAMP      |                                          |

---

## 3. Notifications & Support

### notifications

| Field         | Type           | Description                              |
|---------------|----------------|------------------------------------------|
| id            | BIGINT, PK     | Notification ID                          |
| user_id       | BIGINT, FK     | Recipient                                |
| type          | VARCHAR(32)    | comment, payment, report, etc.           |
| content       | TEXT           | JSON or text payload                     |
| is_read       | BOOL           | Read status                              |
| created_at    | TIMESTAMP      |                                          |

### support_tickets

| Field         | Type           | Description                              |
|---------------|----------------|------------------------------------------|
| id            | BIGINT, PK     | Ticket ID                                |
| user_id       | BIGINT, FK     | Author                                   |
| subject       | VARCHAR(255)   | Ticket subject                           |
| status        | ENUM           | open, in_progress, closed                |
| created_at    | TIMESTAMP      |                                          |
| updated_at    | TIMESTAMP      |                                          |

### support_messages

| Field         | Type           | Description                              |
|---------------|----------------|------------------------------------------|
| id            | BIGINT, PK     | Message ID                               |
| ticket_id     | BIGINT, FK     | Related ticket                           |
| user_id       | BIGINT, FK     | Sender                                   |
| content       | TEXT           | Message text                             |
| created_at    | TIMESTAMP      |                                          |

---

## 4. Additional Tables

- **sessions**: Session tokens, device info, expiry
- **reports**: Content/user reports (copyright, abuse)
- **groups, group_members, group_messages**: Community/chat modules
- **analytics_events**: Usage logging (anonymized)
- **api_keys**: Developer/API client management

---

## 5. Indexing & Performance

- All foreign keys indexed.
- `artworks.tags`, `artworks.media_type`, `users.status`, `payments.status`, etc. are indexed for fast filtering.
- Full-text search enabled on `artworks.title_*`, `artworks.description_*`, and `comments.content`.

---

## 6. Data Protection

- Passwords: bcrypt/argon2
- 2FA secrets: encrypted
- Payments: No raw card data stored (PCI DSS)
- Personal data: Erasure and export supported (see [PRIVACY.md](./docs/PRIVACY.md))

---

## 7. Schema Evolution

- All migrations are timestamped and reversible.
- Use `php artisan migrate` (Laravel) or compatible tool.
- Production DB is MariaDB 10.6+/MySQL 8+ (Azure/AWS RDS compatible).
- For changes, update `db.sql`, migration scripts, and this document.

---

## 8. Entity Relationship Diagram (ERD)

> Textual overview (see attached PDF/online ERD for visualization):

- **users** 1---* **artworks**
- **users** 1---* **evaluations** (*as evaluator*)
- **artworks** 1---* **evaluations**
- **artworks** 1---* **comments**
- **users** 1---* **payments**
- **users** 1---* **wallets**
- **users** 1---* **notifications**
- **users** 1---* **support_tickets**
- **support_tickets** 1---* **support_messages**

---

## 9. References

- [db.sql](./docs/db.sql) – canonical schema, migrations
- [PRIVACY.md](./docs/PRIVACY.md), [SECURITY.md](./docs/SECURITY.md)
- [MOBILE_GUIDE.md](./docs/MOBILE_GUIDE.md) for mobile DB sync/local storage

---

_Questions? Contact devops@acumencraft.com_
