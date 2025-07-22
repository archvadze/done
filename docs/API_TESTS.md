# Acumen Craft – API Testing Guide

_Last updated: 2025-07-22_

---

## Overview

This document provides instructions and examples for testing Acumen Craft’s REST API & Webhooks.  
Covers manual API testing, automated tests, authentication, test data, and best practices.

---

## 1. Tools & Environment

- **Recommended tools:**  
  - [Postman](https://postman.com) or [Insomnia](https://insomnia.rest) for manual testing
  - [cURL](https://curl.se/) for CLI/automation
  - PHPUnit (backend, Laravel) for automated tests
  - [Jest](https://jestjs.io/) (frontend), [Dart test](https://dart.dev/guides/testing) (Flutter)
- **Base URL:**  
  - Local: `http://localhost:8000/api/v1/`
  - Staging/Prod: See environment variables or API docs
- **Swagger/OpenAPI:**  
  - [swagger.yaml](./docs/swagger.yaml) – for schemas, endpoints, and examples

---

## 2. Authentication

- **Bearer JWT:** Required for most endpoints  
  - Obtain via `/auth/login` with email/password  
  - Example:
    ```http
    POST /api/v1/auth/login
    {
      "email": "test@example.com",
      "password": "secret123"
    }
    ```
  - Response: `{ "token": "<JWT>" }`
  - Use `Authorization: Bearer <JWT>` header for requests
- **OAuth / Social:**  
  - Use `/auth/oauth/{provider}` endpoints (see docs)
- **API Keys:**  
  - For developer/integration access (`api_keys` table)

---

## 3. Example Requests

### Create Artwork

```http
POST /api/v1/artworks
Authorization: Bearer <JWT>
Content-Type: application/json

{
  "title_en": "Sunset",
  "description_en": "A beautiful digital sunset.",
  "media_url": "https://cdn.acumencraft.com/artworks/sunset.png",
  "tags": "sunset,landscape,digital",
  "license": "CC BY-NC-SA",
  "is_ai_generated": false
}
```

### Evaluate Artwork

```http
POST /api/v1/evaluations
Authorization: Bearer <JWT>
Content-Type: application/json

{
  "artwork_id": 1005,
  "technique": 8,
  "composition": 9,
  "originality": 7,
  "impact": 8,
  "feedback": "Great use of color!"
}
```

### Get Notifications

```http
GET /api/v1/notifications
Authorization: Bearer <JWT>
```

---

## 4. Automated Testing

- **Backend (PHPUnit/Laravel):**
  - Tests in `tests/Feature/` and `tests/Unit/`
  - Run: `php artisan test` or `vendor/bin/phpunit`
  - Example:
    ```php
    $response = $this->actingAs($user)->postJson('/api/v1/artworks', [...]);
    $response->assertStatus(201);
    $response->assertJsonStructure(['id', 'title_en', 'media_url']);
    ```
- **Frontend (Jest/RTL):**
  - API calls are mocked/stubbed for UI logic tests
- **Webhooks:**
  - Simulate POST requests from trusted IPs/services
  - Validate signature (see [WEBHOOKS.md](./docs/WEBHOOKS.md))

---

## 5. Test Data & Fixtures

- Use seeders (`php artisan db:seed`) for standard test users, artworks, payments, etc.
- For destructive tests, use local/dev environments only.
- Anonymize or purge sensitive data after test runs.

---

## 6. Error Handling

- All API errors return JSON with HTTP status codes and error messages:
  ```json
  {
    "error": "Unauthorized",
    "message": "Invalid credentials."
  }
  ```
- Common codes: 400 (Bad Request), 401 (Unauthorized), 403 (Forbidden), 404 (Not Found), 422 (Validation), 500 (Server)

---

## 7. Best Practices

- Always test with valid and invalid/missing data.
- Validate authentication, rate limiting, and authorization rules.
- Check edge cases (large uploads, invalid file types, max length).
- Review and update tests after every API change.
- Use Postman Collections or OpenAPI for regression/CI checks.

---

## 8. References

- [swagger.yaml](./docs/swagger.yaml) – full API schema & examples
- [WEBHOOKS.md](./docs/WEBHOOKS.md)
- [DATABASE.md](./docs/DATABASE.md)
- [SECURITY.md](./docs/SECURITY.md)

---

_Questions? Contact api@acumencraft.com_
