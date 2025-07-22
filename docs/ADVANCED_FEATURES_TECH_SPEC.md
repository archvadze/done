# დამატებითი მოწინავე ფუნქციონალი – ვრცელი ტექნიკური დავალება  
_Acumen Craft – ვერსია: 5.2_

---

## 1. სოციალური და მობილური ავთენტიფიკაცია

### 1.1. OAuth2/Social Login Integration

#### მიზანი  
- უზრუნველყოს სწრაფი და უსაფრთხო რეგისტრაცია/შესვლა Google, Apple, Facebook, Microsoft, Github, Twitter, LinkedIn-ით.

#### არქიტექტურა  
- გამოიყენე Laravel Socialite.
- თითოეული provider-ისთვის ცალკე კონფიგურაცია (.env, secrets).
- ავთენტიფიკაციის callback-uri-ს უსაფრთხო whitelist.
- მომხმარებლის პროფილში დაკავშირებული ანგარიშების სია (link/unlink).

#### მონაცემთა მოდელი  
- **users** ცხრილში:
  - provider (ENUM/string)
  - provider_id (VARCHAR)
  - oauth_avatar (URL)
  - oauth_email_verified (BOOLEAN)
  - 2fa_secret (VARCHAR, optional)
- **linked_accounts** (user_id, provider, provider_id, email, avatar_url, created_at)

#### API  
- `GET /api/v1/auth/social/{provider}/redirect`  
- `GET /api/v1/auth/social/{provider}/callback`
- `POST /api/v1/auth/link-provider`
- `POST /api/v1/auth/unlink-provider`
- `POST /api/v1/auth/2fa/setup`, `POST /api/v1/auth/2fa/verify`

#### UX/UI  
- Social login buttons (Google, Apple, Facebook, ...).
- Two-factor auth setup (QR code, backup codes).
- Linked Accounts management.

#### უსაფრთხოება  
- OAuth state protection.
- Email verification.
- 2FA ნებაყოფლობით ან სავალდებულო (role-based).
- Rate-limiting login attempts.

---

## 2. გადახდები: Fiat (Stripe/PayPal) და კრიპტო

### 2.1. Fiat (Stripe & PayPal)

#### Stripe/PayPal Integration  
- One-time და recurring (subscriptions) გადახდები.
- Webhook endpoints: /webhook/stripe, /webhook/paypal
- User balance, payout, withdrawal support.

#### მონაცემთა მოდელი  
- **payments** (id, user_id, amount, currency, provider, status, payment_id, created_at, refunded_at)
- **withdrawals** (id, user_id, amount, currency, provider, status, requested_at, processed_at)

#### API  
- `POST /api/v1/payments/stripe/checkout`
- `POST /api/v1/payments/paypal/checkout`
- `GET /api/v1/payments/history`
- `POST /api/v1/withdrawals/request`

#### უსაფრთხოება  
- Webhook signature validation.
- Fraud detection (Stripe Radar).
- GDPR-compliant მონაცემთა დამუშავება.

---

### 2.2. კრიპტო: Crypto Payments & NFTs

#### მიზანი  
- მომხმარებელს შეეძლოს შემოწირულობა/გადახდა კრიპტოვალუტით, artworks-ის NFT-ად გამოცემა, Wallet-ის დაკავშირება.

#### არქიტექტურა  
- Coinbase Commerce ან Stripe Crypto API.
- NFT minting: OpenSea, Rarible, ან Polygon/Ethereum custom integration.
- WalletConnect/MetaMask ინტეგრაცია.

#### მონაცემთა მოდელი  
- **crypto_payments** (id, user_id, amount, currency, tx_hash, status, network, created_at)
- **nft_ownership** (artwork_id, owner_wallet, network, token_id, tx_hash, minted_at)

#### API  
- `POST /api/v1/crypto/checkout`
- `POST /api/v1/crypto/verify-payment`
- `POST /api/v1/nft/mint`
- `GET /api/v1/nft/ownership/{artwork_id}`

#### უსაფრთხოება  
- Web3 signature validation.
- AML/KYC checks (withdrawal > threshold).

#### UI/UX  
- Crypto Wallet connect (MetaMask, WalletConnect).
- NFT mint/share button artworks-ზე.
- Blockchain explorer ბმული.

---

## 3. ლაივ ჩატი (User2User, Guest, AI)

### 3.1. User-to-User Chat

#### არქიტექტურა  
- WebSockets: Laravel Echo + Pusher/Soketi.
- Direct Message (1:1), Group Chat (N:N), Conversation history.
- End-to-end encryption (optional).
- Attachment (image, audio, doc) support.

#### მონაცემთა მოდელი  
- **conversations** (id, type, created_at)
- **conversation_participants** (conversation_id, user_id, joined_at)
- **messages** (id, conversation_id, sender_id, message, message_type, status, sent_at, read_at, attachment_url)

#### API  
- `POST /api/v1/chat/start`
- `GET /api/v1/chat/conversations`
- `POST /api/v1/chat/send-message`
- `GET /api/v1/chat/messages/{conversation_id}`
- `POST /api/v1/chat/mark-read`

#### UI/UX  
- Chat window (Inbox, New chat button).
- Read/unread status.
- Attachment upload UI.
- Abuse reporting ("Report message").

---

### 3.2. Guest/AI Chat

#### მიზანი  
- სტუმრებს/ყველა მომხმარებელს ჰქონდეს კონტექსტური AI-ჩატის ასისტენტი (FAQ, onboarding, art advisor).

#### არქიტექტურა  
- OpenAI GPT-4/Claude API ან custom LLM.
- Context-aware prompts: FAQ, Upload Guide, Copyright, Recommendations.
- Rate-limited, abuse-prevented.

#### მონაცემთა მოდელი  
- **ai_chat_sessions** (session_id, user_id/guest_id, started_at, ended_at)
- **ai_chat_messages** (id, session_id, role, content, sent_at)

#### API  
- `POST /api/v1/ai-chat/message`
- `GET /api/v1/ai-chat/history/{session_id}`

#### UI/UX  
- "Ask AI" button/platform-wide widget.
- Suggested prompts/buttons.
- Feedback thumbs (Helpful/Not helpful).

#### უსაფრთხოება  
- Prompt filtering (profanity, abuse).
- Rate limiting per IP/user.

---

## 4. HelpDesk & Guided Tour (Support, FAQ, Live Help)

### 4.1. HelpDesk Center

#### არქიტექტურა  
- FAQ database, searchable.
- Ticket system: User → Support/Admin.
- AI-powered FAQ bot (answers common platform questions).

#### მონაცემთა მოდელი  
- **help_articles** (id, category, title_translations, content_translations, created_at, updated_at)
- **support_tickets** (id, user_id, subject, description, status, assigned_to, created_at, resolved_at, priority)
- **ticket_messages** (id, ticket_id, sender_id, content, sent_at)

#### API  
- `GET /api/v1/helpdesk/articles`
- `GET /api/v1/helpdesk/articles/{id}`
- `POST /api/v1/helpdesk/ticket`
- `GET /api/v1/helpdesk/tickets`
- `POST /api/v1/helpdesk/ticket-message`

#### UI/UX  
- Searchable FAQ (Algolia/Meilisearch).
- Guided onboarding (Shepherd.js, Tour.js).
- "Help" widget ყველა მთავარ გვერდზე.
- Ticket submission, ticket tracking.
- Live Chat (Intercom/Zendesk).

---

### 4.2. AI HelpBot

#### ფუნქციონალი  
- Natural language query support (multilingual).
- კონტექსტური დახმარება: „როგორ ავტვირთო ნამუშევარი“, „როგორ დავიცვა საავტორო უფლება“, „როგორ გავაკეთო გადახდა“.
- Suggestions on error screens.

#### უსაფრთხოება  
- Abuse prevention, feedback collection.

---

## 5. Notification System

### 5.1. Notification Types

- In-app (Bell icon)
- Email
- Push (WebPush, Mobile Push)

### 5.2. მონაცემთა მოდელი  
- **notifications** (id, user_id, type, content, data, read_at, created_at)
- **notification_settings** (user_id, type, enabled, channel)

### 5.3. API  
- `GET /api/v1/notifications`
- `POST /api/v1/notifications/mark-read`
- `GET /api/v1/notifications/settings`
- `POST /api/v1/notifications/settings`

### 5.4. UI/UX  
- Notification center.
- Notification preference page.

---

## 6. User Privacy & Security

### 6.1. Privacy Features

- Cookie consent, Privacy Policy, Terms of Use, Data Download/Erase ("My Data").
- GDPR/CCPA/SOC2 compliance.
- Opt-out/Opt-in for analytics/marketing.
- Public/private profile toggle.

### 6.2. Security Features

- 2FA, SSO, OAuth scopes.
- Rate limiting, login throttling.
- Vulnerability scanning, regular audit.
- Security logs/audit trail.
- Password reset, device logout.

---

## 7. API Exposure & Integration

### 7.1. Public/Partner API

- OAuth2, API Keys, scope-based permissions.
- Rate limiting, abuse detection.
- Webhooks: payment, content, moderation events.

### 7.2. დოკუმენტაცია  
- OpenAPI/swagger.yaml
- Postman collection
- API usage quotas

### 7.3. Endpoints  
- /api/v1 (core features)
- /api/v1/partner (custom extensions)
- /api/v1/webhooks/...

---

## 8. Analytics & Insights

### 8.1. დავალებები

- User engagement (active users, retention, churn)
- Artwork stats (views, likes, ACQ, sales)
- Financial stats (payments, payouts, crypto flows)
- Community stats (posts, comments, moderation actions)

### 8.2. მონაცემთა მოდელი  
- **analytics_events** (id, user_id, event_type, event_data, created_at)
- **admin_reports** (report_type, data, generated_at)

### 8.3. UI/UX  
- Admin dashboard: charts, export.
- User dashboard: personal stats, export.

---

## 9. Mobile-first & PWA

### 9.1. მიზანი  
- სრულად ადაპტირებული ვებსაიტი ყველა მოწყობილობაზე.
- Progressive Web App (PWA): offline, push notifications.
- Mobile app (Flutter/React Native) – API compatibility, deep links.

### 9.2. UX დეტალები  
- Touch-friendly controls.
- Mobile upload flow.
- Mobile push notifications.

---

## 10. Best Practices, Monitoring & Compliance

### 10.1. Monitoring

- Sentry/New Relic/Datadog integration.
- Health checks.
- Incident alerting.

### 10.2. Compliance

- Access control (RBAC).
- Audit log (critical actions).
- SSL/TLS everywhere.
- Infrastructure as Code (Terraform/CloudFormation).
- Regular backups, rollback plans.

---

## 11. Roadmap & Documentation

- ყოველი ახალი მოდული აისახოს Roadmap.md-ში ქვეტასქებით.
- ყველა API/endpoint იყოს აღწერილი swagger.yaml-ში.
- ყველა UI/UX ცვლილება – branding.pdf/UI mockups.
- ყველა ცვლილება – CHANGELOG.md-ში.

---

## 12. დანართები და ბმულები (README-სთვის)

- [მომხმარებლის საავტორო უფლებები და კონტენტის მართვა](./USER_COPYRIGHT.md)
- [დახმარების ცენტრი და FAQ](./HELPDESK.md)
- [API დოკუმენტაცია (swagger.yaml)](./swagger.yaml)
- [მობილური აპის გზამკვლევი](./MOBILE_GUIDE.md)
- [ბრენდინგი და UI ნიმუშები](./branding.pdf)
- [Security Guide](./SECURITY.md)
- [Privacy Policy](./PRIVACY.md)
- [CONTRIBUTING.md](./CONTRIBUTING.md)
- [LICENSE.md](./LICENSE.md)

---

**შენიშვნა:**  
ყველა ზემოთ ჩამოთვლილი მოდული, მონაცემთა ველი, API და UI კომპონენტი უნდა იყოს მკაფიოდ აღწერილი, დოკუმენტირებული და ტესტირებული, რათა VS Code-ში ინტეგრირებულ აგენტს (Copilot) შეეძლო კონტექსტური დახმარება და სწრაფი გენერაცია.

---

**თუ რომელიმე სექცია გჭირდება კოდით, ERD-ით ან UI mockup-ით, მომწერე კონკრეტულად – მოგიმზადებ!**
