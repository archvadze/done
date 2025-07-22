# Acumen Craft – Webhook Integration Guide

## Overview

This document describes all supported webhooks for the Acumen Craft platform.  
Webhooks enable third-party integrations and real-time event-driven workflows for payments, NFT, moderation, chat, notifications, copyright/infringement, AI, and more.

- API version: v1
- Format: All payloads are sent as `application/json`
- Security: Use your unique **Webhook Secret** (HMAC SHA256, header: `X-Acumen-Signature`)
- Retries: 3 attempts on failure (exponential backoff)
- Endpoint setup: Manage from your profile > Developer/API settings.

---

## Webhook Events

### 1. Payments

#### `payment.completed`
Triggered when a fiat or crypto payment is completed.

**Payload:**
```json
{
  "event": "payment.completed",
  "data": {
    "payment_id": "pay_123456",
    "user_id": 17,
    "amount": 49.99,
    "currency": "USD",
    "provider": "stripe",
    "status": "completed",
    "created_at": "2025-07-22T09:55:44Z"
  }
}
```

#### `withdrawal.requested`
Triggered when a user requests a withdrawal.

```json
{
  "event": "withdrawal.requested",
  "data": {
    "withdrawal_id": "wdr_98765",
    "user_id": 17,
    "amount": 120,
    "currency": "USD",
    "provider": "paypal",
    "status": "pending",
    "requested_at": "2025-07-22T09:55:44Z"
  }
}
```

#### `crypto.payment.confirmed`
Triggered when an on-chain crypto payment is confirmed.

```json
{
  "event": "crypto.payment.confirmed",
  "data": {
    "crypto_payment_id": "cpt_42",
    "user_id": 34,
    "amount": 0.4,
    "currency": "ETH",
    "network": "ethereum",
    "tx_hash": "0xabc123...",
    "status": "confirmed",
    "created_at": "2025-07-22T09:55:44Z"
  }
}
```

---

### 2. NFT

#### `nft.minted`
Triggered when a new NFT is minted from an artwork.

```json
{
  "event": "nft.minted",
  "data": {
    "artwork_id": 101,
    "nft_ownership_id": 33,
    "owner_wallet": "0x1234...abcd",
    "network": "polygon",
    "token_id": "789",
    "tx_hash": "0xdef456...",
    "minted_at": "2025-07-22T09:55:44Z"
  }
}
```

---

### 3. Artworks & Evaluations

#### `artwork.uploaded`
Triggered when a user uploads a new artwork.

```json
{
  "event": "artwork.uploaded",
  "data": {
    "artwork_id": 55,
    "user_id": 18,
    "status": "pending",
    "media_type": "image",
    "title_translations": { "en": "Sunset", "de": "Sonnenuntergang" },
    "created_at": "2025-07-22T09:55:44Z"
  }
}
```

#### `artwork.approved`
Triggered when an artwork is approved by a moderator.

```json
{
  "event": "artwork.approved",
  "data": {
    "artwork_id": 55,
    "user_id": 18,
    "approved_by": 3,
    "status": "approved",
    "approved_at": "2025-07-22T09:55:44Z"
  }
}
```

#### `evaluation.submitted`
Triggered when an evaluation (manual or AI) is submitted for an artwork.

```json
{
  "event": "evaluation.submitted",
  "data": {
    "artwork_id": 55,
    "evaluator_id": 2,
    "scores": {
      "technique": 8,
      "composition": 7,
      "originality": 10,
      "impact": 9
    },
    "feedback_text": "Excellent originality.",
    "source": "human",
    "created_at": "2025-07-22T09:55:44Z"
  }
}
```

---

### 4. Copyright & Infringement

#### `infringement.reported`
Triggered when a user files an infringement report.

```json
{
  "event": "infringement.reported",
  "data": {
    "report_id": 77,
    "artwork_id": 55,
    "reporter_id": 45,
    "description": "Suspected copyright violation.",
    "status": "pending",
    "created_at": "2025-07-22T09:55:44Z"
  }
}
```

#### `infringement.resolved`
Triggered when a copyright/infringement report is resolved by an admin.

```json
{
  "event": "infringement.resolved",
  "data": {
    "report_id": 77,
    "artwork_id": 55,
    "resolved_by": 3,
    "status": "resolved",
    "resolved_at": "2025-07-22T09:55:44Z"
  }
}
```

---

### 5. Chat & AI

#### `chat.message`
Triggered when a new message is sent in a user or group chat.

```json
{
  "event": "chat.message",
  "data": {
    "conversation_id": 22,
    "message_id": 678,
    "sender_id": 17,
    "message": "Hello!",
    "sent_at": "2025-07-22T09:55:44Z"
  }
}
```

#### `ai.response`
Triggered when an AI assistant sends a response in a chat session.

```json
{
  "event": "ai.response",
  "data": {
    "session_id": 9,
    "message_id": 1098,
    "role": "assistant",
    "content": "How can I help you?",
    "sent_at": "2025-07-22T09:55:44Z"
  }
}
```

---

### 6. Notifications

#### `notification.created`
Triggered when a new notification is created for a user.

```json
{
  "event": "notification.created",
  "data": {
    "notification_id": 321,
    "user_id": 17,
    "type": "comment.new",
    "content": { "artwork_id": 55, "comment_id": 88 },
    "created_at": "2025-07-22T09:55:44Z"
  }
}
```

---

### 7. HelpDesk & Support

#### `support.ticket.created`
Triggered when a new support ticket is submitted.

```json
{
  "event": "support.ticket.created",
  "data": {
    "ticket_id": 404,
    "user_id": 17,
    "subject": "Payment issue",
    "status": "open",
    "created_at": "2025-07-22T09:55:44Z"
  }
}
```

#### `support.ticket.resolved`
Triggered when a support ticket is resolved by staff.

```json
{
  "event": "support.ticket.resolved",
  "data": {
    "ticket_id": 404,
    "resolved_by": 3,
    "status": "resolved",
    "resolved_at": "2025-07-22T09:55:44Z"
  }
}
```

---

## Webhook Security

- All requests are signed with an HMAC SHA256 signature.
- Signature header: `X-Acumen-Signature`.
- The signature is generated using your webhook secret and the raw request body.
- Always verify the signature before trusting the event.

## Example verification (PHP)

```php
$signature = $_SERVER['HTTP_X_ACUMEN_SIGNATURE'];
$raw_body = file_get_contents('php://input');
$expected = hash_hmac('sha256', $raw_body, $your_webhook_secret);
if (!hash_equals($expected, $signature)) {
    http_response_code(401);
    exit('Invalid signature');
}
```

---

## Event Delivery & Retries

- If your endpoint returns a non-2xx HTTP code, we will retry delivery up to 3 times.
- After 3 failures, the event is marked as failed in your developer dashboard.

---

## Best Practices

- Respond with 2xx quickly; process events asynchronously if needed.
- Idempotency: Always check the event id or payload to prevent duplicate processing.
- Log all incoming webhook deliveries for audit/troubleshooting.

---

## Further Reading

- [API Reference (swagger.yaml)](./docs/swagger.yaml)
- [Developer Portal](./docs/README.md#api-Exposure--integration)
- [Contact Support](mailto:support@acumencraft.com)

---
