# Acumen Craft – მოწინავე მოდულების ERD & UI Mockup-ების გზამკვლევი

---

## 1. სოციალური და მობილური ავთენტიფიკაცია

### **ERD**

```mermaid
erDiagram
    USERS {
      BIGINT id PK
      VARCHAR name
      VARCHAR email
      VARCHAR password
      VARCHAR provider
      VARCHAR provider_id
      VARCHAR oauth_avatar
      BOOL oauth_email_verified
      VARCHAR 2fa_secret
      ...
    }
    LINKED_ACCOUNTS {
      BIGINT id PK
      BIGINT user_id FK
      VARCHAR provider
      VARCHAR provider_id
      VARCHAR email
      VARCHAR avatar_url
      TIMESTAMP created_at
    }
    USERS ||--o{ LINKED_ACCOUNTS : has
```

### **UI Mockup (Textual Sketch)**

- **Login/Register Page**:  
  [ Google ] [ Apple ] [ Facebook ]  
  —or—  
  Email/Password  
  [ 2FA Code (optional) ]  
  [ Link other accounts ]  
  [ Reset password ]  
  [ Privacy Policy ]

- **Profile > Linked Accounts**:  
  - Google: ✅ Linked | [ Unlink ]  
  - Apple: [ Link ]  
  - Facebook: [ Link ]  
  - 2FA: [ Setup ] [ Regenerate Backup Codes ]

---

## 2. გადახდები: Fiat & კრიპტო

### **ERD**

```mermaid
erDiagram
    PAYMENTS {
      BIGINT id PK
      BIGINT user_id FK
      DECIMAL amount
      VARCHAR currency
      ENUM provider
      ENUM status
      VARCHAR payment_id
      TIMESTAMP created_at
      TIMESTAMP refunded_at
    }
    WITHDRAWALS {
      BIGINT id PK
      BIGINT user_id FK
      DECIMAL amount
      VARCHAR currency
      ENUM provider
      ENUM status
      TIMESTAMP requested_at
      TIMESTAMP processed_at
    }
    CRYPTO_PAYMENTS {
      BIGINT id PK
      BIGINT user_id FK
      DECIMAL amount
      VARCHAR currency
      VARCHAR tx_hash
      ENUM status
      VARCHAR network
      TIMESTAMP created_at
    }
    NFT_OWNERSHIP {
      BIGINT id PK
      BIGINT artwork_id FK
      VARCHAR owner_wallet
      VARCHAR network
      VARCHAR token_id
      VARCHAR tx_hash
      TIMESTAMP minted_at
    }
    USERS ||--o{ PAYMENTS : has
    USERS ||--o{ WITHDRAWALS : has
    USERS ||--o{ CRYPTO_PAYMENTS : has
    ARTWORKS ||--o{ NFT_OWNERSHIP : can_be_minted
```

### **UI Mockup (Textual Sketch)**

- **Payments:**  
  - [Pay by Card] [PayPal] [Crypto]  
  - Amount: $___  
  - [ Checkout ]

- **Withdrawals:**  
  - Balance: $___  
  - [Withdraw] (choose method: Stripe, PayPal, Crypto)

- **NFT Mint:**  
  - [ Connect Wallet ]  
  - [ Mint as NFT ]  
  - [ View on Explorer ]

---

## 3. ლაივ ჩატი (User2User, Guest, AI)

### **ERD**

```mermaid
erDiagram
    CONVERSATIONS {
      BIGINT id PK
      ENUM type
      TIMESTAMP created_at
    }
    CONVERSATION_PARTICIPANTS {
      BIGINT id PK
      BIGINT conversation_id FK
      BIGINT user_id FK
      TIMESTAMP joined_at
    }
    MESSAGES {
      BIGINT id PK
      BIGINT conversation_id FK
      BIGINT sender_id FK
      TEXT message
      ENUM message_type
      ENUM status
      TIMESTAMP sent_at
      TIMESTAMP read_at
      VARCHAR attachment_url
    }
    AI_CHAT_SESSIONS {
      BIGINT id PK
      BIGINT user_id FK
      VARCHAR guest_id
      TIMESTAMP started_at
      TIMESTAMP ended_at
    }
    AI_CHAT_MESSAGES {
      BIGINT id PK
      BIGINT session_id FK
      ENUM role
      TEXT content
      TIMESTAMP sent_at
    }
    CONVERSATIONS ||--o{ CONVERSATION_PARTICIPANTS : has
    CONVERSATIONS ||--o{ MESSAGES : has
    AI_CHAT_SESSIONS ||--o{ AI_CHAT_MESSAGES : has
```

### **UI Mockup (Textual Sketch)**

- **Inbox:**  
  - [User1] [AI Assistant] [New Chat]  
  - Conversation List

- **Chat Window:**  
  - Messages  
  - [Type message...] [Send] [Attach]

- **AI Chat Widget:**  
  - "Ask AI"  
  - [Suggested Prompts]  
  - [👍] [👎]

---

## 4. HelpDesk & Guided Tour

### **ERD**

```mermaid
erDiagram
    HELP_ARTICLES {
      BIGINT id PK
      VARCHAR category
      JSON title_translations
      JSON content_translations
      TIMESTAMP created_at
      TIMESTAMP updated_at
    }
    SUPPORT_TICKETS {
      BIGINT id PK
      BIGINT user_id FK
      VARCHAR subject
      TEXT description
      ENUM status
      BIGINT assigned_to FK
      TIMESTAMP created_at
      TIMESTAMP resolved_at
      ENUM priority
    }
    TICKET_MESSAGES {
      BIGINT id PK
      BIGINT ticket_id FK
      BIGINT sender_id FK
      TEXT content
      TIMESTAMP sent_at
    }
    USERS ||--o{ SUPPORT_TICKETS : has
    SUPPORT_TICKETS ||--o{ TICKET_MESSAGES : has
```

### **UI Mockup (Textual Sketch)**

- **Help Center:**  
  - [Search FAQ...]  
  - Categories: Uploading, Payments, Copyright...
  - [Contact Support] (opens ticket form)

- **Ticket View:**  
  - Subject  
  - Messages thread  
  - [Reply]

- **Onboarding Tour:**  
  - "Welcome to Acumen Craft!"  
  - [Next Step] [Skip]

---

## 5. Notification System

### **ERD**

```mermaid
erDiagram
    NOTIFICATIONS {
      BIGINT id PK
      BIGINT user_id FK
      ENUM type
      JSON content
      JSON data
      TIMESTAMP read_at
      TIMESTAMP created_at
    }
    NOTIFICATION_SETTINGS {
      BIGINT id PK
      BIGINT user_id FK
      ENUM type
      BOOL enabled
      ENUM channel
    }
    USERS ||--o{ NOTIFICATIONS : receives
    USERS ||--o{ NOTIFICATION_SETTINGS : configures
```

### **UI Mockup (Textual Sketch)**

- **Header:**  
  - [🔔] (shows dropdown with latest notifications)

- **Notification Center:**  
  - All, Unread, Settings  
  - [Mark all as read]

- **Settings:**  
  - Email, Push, In-app toggles for each event type

---

## 6. Privacy & Security

### **ERD**

```mermaid
erDiagram
    USERS {
      ... (as above)
      JSON notification_prefs
      JSON privacy_prefs
    }
    SECURITY_LOGS {
      BIGINT id PK
      BIGINT user_id FK
      VARCHAR action
      VARCHAR ip
      TEXT meta
      TIMESTAMP created_at
    }
    USERS ||--o{ SECURITY_LOGS : records
```

### **UI Mockup (Textual Sketch)**

- **Privacy Dashboard:**  
  - [Download my data] [Erase account]  
  - [Manage consents]  
  - [Public/Private profile toggle]

- **Security:**  
  - [Enable 2FA]  
  - [Active devices]  
  - [Session history]  
  - [Logout all devices]

---

## 7. API Exposure & Integration

### **ERD**

```mermaid
erDiagram
    API_CLIENTS {
      BIGINT id PK
      BIGINT user_id FK
      VARCHAR name
      VARCHAR api_key
      ENUM scope
      BOOL enabled
      TIMESTAMP created_at
    }
    API_USAGE {
      BIGINT id PK
      BIGINT client_id FK
      VARCHAR endpoint
      INT count
      DATE day
    }
    API_CLIENTS ||--o{ API_USAGE : tracks
```

### **UI Mockup (Textual Sketch)**

- **API Key Management:**  
  - [Generate API Key]  
  - [View Usage]  
  - [Revoke]

---

## 8. Analytics & Insights

### **ERD**

```mermaid
erDiagram
    ANALYTICS_EVENTS {
      BIGINT id PK
      BIGINT user_id FK
      VARCHAR event_type
      JSON event_data
      TIMESTAMP created_at
    }
    ADMIN_REPORTS {
      BIGINT id PK
      VARCHAR report_type
      JSON data
      TIMESTAMP generated_at
    }
```

### **UI Mockup (Textual Sketch)**

- **Admin Dashboard:**  
  - [User Growth Chart] [Active Users]  
  - [Artwork Stats]  
  - [Export CSV/Excel]

- **User Dashboard:**  
  - My Uploads  
  - My ACQ  
  - My Financials

---

## 9. Mobile-first & PWA

**ERD-ში ცვლილება არ მოითხოვს, უბრალოდ დიზაინის და API-ს მხარეა**.

### **UI Mockup (Textual Sketch)**

- **Mobile Navbar:**  
  - [Home] [Explore] [Upload] [Notifications] [Profile]

- **Mobile Upload:**  
  - Select/Take Photo  
  - Fill in details  
  - [Submit]

- **Push notifications:**  
  - OS-native permission prompts  
  - In-app notification banner

---

## 10. საავტორო უფლებები და infringement

**იხილე დეტალურად: [TECHNICAL_SPEC_COPYRIGHT_MODULE.md](./TECHNICAL_SPEC_COPYRIGHT_MODULE.md)**

---

## შენიშვნები

- ყველა Mermaid.js დიაგრამა შეგიძლია გრაფიკულად გარდაქმნა [Mermaid Live Editor](https://mermaid.live/)–ში ან VS Code Mermaid Extensions-ით.
- ყველა UI mockup მოცემულია ტექსტური ფორმით, მაგრამ შეგიძლია გადაიტანო Figma/Balsamiq–ში ან გამოიყენო Copilot-თან ერთად დიზაინისთვის.
- მონაცემთა მოდელში ყველა ცხრილი/ველები შეგიძლია პირდაპირ გამოიყენო Laravel/Eloquent მიგრაციებში ან სხვა ORM-ში.

---

**თუ გჭირდება რომელიმე ბლოკის სრული კოდი (მიგრაცია, controller, Vue/React კომპონენტი, Mermaid-სქემა SVG-დ), მომწერე კონკრეტულად!**
