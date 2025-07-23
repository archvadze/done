# Acumen Craft – სრული ტექნიკური დავალება (AI Agent-ready README)

---

## პროექტის მიზანი

**Acumen Craft** არის ინტელექტუალური ხელოვნების პლატფორმა, რომელიც აერთიანებს თანამედროვე ტექნოლოგიებს (AI, Blockchain, Social, Mobile), უზრუნველყოფს შემოქმედთა უფლებების დაცვას, ინოვაციურ შეფასებებს, გლობალურ ურთიერთობას და ფინანსურ მხარდაჭერას.  
დოკუმენტი განკუთვნილია როგორც დეველოპერული გუნდისთვის, ასევე AI კოდგენერატორი/აგენტისთვის (Copilot, Cody, Tabnine, Cursor), რათა არც ერთი კრიტიკული დეტალი არ დარჩეს შეუმჩნეველი და განუვითარებელი.

---

## არქიტექტურა და ტექნოლოგიური სტეკი

- **Backend:** Laravel 11+ (PHP 8.2+), RESTful API, Eloquent ORM, Redis (cache/queue)
- **AI Service:** Python 3.11+, Flask/FastAPI, OpenAI/PyTorch, dockerized
- **Frontend:** Blade Templates, TailwindCSS, Alpine.js, Vite (SPA/PWA support)
- **DB:** MariaDB 10.11+ (MySQL-compatible, JSON support)
- **File Storage:** AWS S3, CloudFront CDN
- **DevOps:** Docker Compose, DDEV, CI/CD (GitHub Actions), IaC (Terraform)
- **Monitoring:** Sentry, New Relic, CloudWatch
- **Mobile:** Mobile-first responsive, PWA, Flutter/React Native (optional)
- **Internationalization:** Laravel Localization, JSON fields, i18n frontend
- **Payments:** Stripe, PayPal, Coinbase Commerce (crypto), NFT minting (optional)
- **Identity:** Socialite (OAuth2), 2FA, passwordless/email, JWT

---

## ძირითადი მოდულები და ურთიერთდამოკიდებულება

### 1. მომხმარებლის აუთენტიფიკაცია და პროფილი

- **Email/Password, Social OAuth2 (Google, Apple, Facebook, Github, Microsoft)**
- **2FA (TOTP/SMS/Email), Passwordless (optional)**
- **Profile customization:** creative field, avatar, bio, language, privacy settings
- **Linked Accounts:** Multiple OAuth providers per user

**დამოკიდებული მოდულები:** Payments, Notifications, Chat, Copyright, Communities

---

### 2. ნამუშევრების ატვირთვა, აღწერა და მართვა

- **Upload:** Image, audio, video, PDF (S3 storage, presigned URLs)
- **Multilingual title/description (JSON), media_type, tags, categories**
- **Licensing:** All Rights Reserved, Creative Commons, NFT, AI Generated
- **Copyright Notice, Watermark, File Hash, Blockchain Timestamp**
- **Visibility:** public, private, unlisted

**დამოკიდებული მოდულები:** ACQ, Payments, Communities, Copyright

---

### 3. შეფასების სისტემა და ACQ

- **Criteria-based manual evaluation:** technique, composition, originality, impact
- **AI-based analysis:** scoring, feedback (REST API, queue)
- **Aggregate ACQ (Acumen Craft Quotient) per artwork/artist**
- **Leaderboard, badge system, analytics dashboard**

**დამოკიდებული მოდულები:** Artworks, Users, AI Service

---

### 4. ფინანსური მოდული (Payments & Donations)

- **Stripe/PayPal:** One-time and recurring (subscriptions)
- **Crypto Payments:** Coinbase Commerce, MetaMask/WalletConnect
- **NFT minting/sale:** ERC-721/1155, OpenSea/Rarible integration
- **Withdrawal & balance management**
- **Full audit trail, webhook handling, AML/KYC (optional)**

**დამოკიდებული მოდულები:** Users, Artworks, Copyright, Notifications

---

### 5. საავტორო უფლებები და სამართლებრივი დაცვა

- **USER_COPYRIGHT.md**: სრულად აღწერილი წესები და ტექნიკური enforcement
- **Artwork-level licensing, digital signature, watermark, file hash**
- **Blockchain timestamp (optional)**
- **Infringement reporting (API, UI, admin review)**
- **Visibility, DMCA workflow, evidence upload**

**დამოკიდებული მოდულები:** Artworks, Users, Admin Panel

---

### 6. ჩატი და კომუნიკაცია

- **User-to-User Direct Messaging (WebSockets, E2E encryption optional)**
- **Group Chat/Communities**
- **AI-powered Guest/FAQ ChatBot**
- **Abuse reporting, moderation, audit logs**

**დამოკიდებული მოდულები:** Users, Communities, HelpDesk, Notifications

---

### 7. თემატური გაერთიანებები (Communities)

- **Interest groups, post/comment threads, moderation**
- **Role-based access (admin/moderator/member)**
- **Multilingual content, polls, events**

**დამოკიდებული მოდულები:** Users, Chat, Artworks

---

### 8. დახმარების ცენტრი და AI HelpDesk

- **FAQ, searchable help articles, ticket system**
- **AI-powered contextual help (multilingual)**
- **Onboarding tour, feedback widget, live support integration**

**დამოკიდებული მოდულები:** Users, Notifications, AI Service

---

### 9. შეტყობინებები (Notifications)

- **In-app, email, mobile push**
- **User preferences per event/channel**
- **Actionable messages (new comment, sale, report, etc.)**

**დამოკიდებული მოდულები:** Users, Payments, Chat, HelpDesk

---

### 10. ანალიტიკა და ადმინისტრაცია

- **User/artwork/activity analytics (charts, exports)**
- **Admin dashboard, moderation queue, audit trail**
- **API usage stats, system health, error monitoring**

---

### 11. ინფორმაციული უსაფრთხოება და კონფიდენციალურობა

- **GDPR/CCPA compliance, data download/erase**
- **Access control (RBAC), security logs, regular audits**
- **2FA, session/device management**
- **Incident alerting, backup/rollback, SSL everywhere**

---

## მონაცემთა მოდელი (სქემა/მიგრაციები)

**იხილეთ:**  
- [db.sql](./docs/db.sql) – სრული SQL/ERD  
- [DB.html](./docs/DB.html) – ვიზუალური დიაგრამა  
- [DATABASE.md](./docs/DATABASE.md) – მონაცემთა ბაზის სტრუქტურა და გზამკვლევი
- [TECHNICAL_SPEC_ACUMEN_CRAFT.md](./docs/TECHNICAL_SPEC_ACUMEN_CRAFT.md) – ძირითადი ტექნიკური სპეციფიკაცია
- [TECH_SPEC_MODULAR_FEATURES.md](./docs/TECH_SPEC_MODULAR_FEATURES.md) – მოდულური ფუნქციონალი (ACQ, Subscriptions, Communities)
- [TECHNICAL_SPEC_COPYRIGHT_MODULE.md](./docs/TECHNICAL_SPEC_COPYRIGHT_MODULE.md) – საავტორო უფლებები

---

## API და ინტეგრაციები

- **RESTful API:** ყველა მოდულისათვის, versioned (/api/v1)
- **Public/Partner API:** OAuth2, API Keys, Webhooks
- **OpenAPI Spec:** [swagger.yaml](./docs/swagger.yaml)
- **Webhook Docs:** [WEBHOOKS.md](./docs/WEBHOOKS.md)
- **Internationalization & AI Integration:** [INTERNATIONALIZATION_AI_WEB_ARCH.md](./docs/INTERNATIONALIZATION_AI_WEB_ARCH.md)
- **Postman Collection:** (მალე)

---

## UI/UX სტანდარტები და Mobile

- **Responsive/Adaptive Design:** TailwindCSS, Mobile - [STYLE_GUIDE.md](./docs/STYLE_GUIDE.md)
- **PWA:** Offline mode, installable, push notifications
- **Mobile App:** (Flutter/React Native) – [MOBILE_GUIDE.md](./docs/MOBILE_GUIDE.md)
- **UI Mockups:** [branding.pdf](./docs/branding.pdf), [UI_MOCKUPS.md](./docs/UI_MOCKUPS.md)
- **Onboarding Tour:** Shepherd.js/Intro.js

---

## DevOps, ტესტირება & მონიტორინგი

- **Dockerized env (DDEV, Docker Compose), IaC (Terraform)**
- **CI/CD:** GitHub Actions, test/build/deploy pipeline
- **Automated tests:** PHPUnit, PyTest, Playwright/Cypress
- **Monitoring:** Sentry/New Relic/CloudWatch
- **Backup/restore, rollback procedures**

---

## მონაცემთა დაცვა და შესაბამისობა

- **Privacy Policy:** [PRIVACY.md](./docs/PRIVACY.md)
- **Security Guide:** [SECURITY.md](./docs/SECURITY.md)
- **Cookie consent, Terms of Use, Data portability**

---

## მოქმედების გეგმა და მართვა

- **Agile/Scrum:** Sprint planning, review, retrospective
- **Roadmap:** [Roadmap.md](./docs/Roadmap.md)
- **Detailed Roadmap & Annexes:** [ROADMAP_AND_ANNEXES.md](./docs/ROADMAP_AND_ANNEXES.md)
- **Issue/PR Management:** GitHub Issues, Projects, Wiki
- **Documentation Driven Development:** CHANGELOG, Wiki, Guides

---

## დანართები და ბმულები

- [მონაცემთა მოდელი (db.sql)](./docs/db.sql)
- [ERD დიაგრამა (DB.html)](./docs/DB.html)
- [Mermaid დიაგრამების სრული რენდერისთვის გამოიყენეთ Mermaid Live Editor ან შესაბამისი VS Code Extension](./docs/ADVANCED_MODULES_ERD_UI.md) 
- [საავტორო უფლებები და კონტენტის მართვა](./docs/USER_COPYRIGHT.md)
- [დახმარების ცენტრი და FAQ](./docs/HELPDESK.md)
- [API დოკუმენტაცია (swagger.yaml)](./docs/swagger.yaml)
- [Webhook Integration](./docs/WEBHOOKS.md)
- [მობილური აპის გზამკვლევი](./docs/MOBILE_GUIDE.md)
- [ბრენდინგი და UI ნიმუშები](./docs/branding.pdf)
- [UI Mockups](./docs/UI_MOCKUPS.md)
- [Security Guide](./docs/SECURITY.md)
- [Privacy Policy](./docs/PRIVACY.md)
- [CONTRIBUTING.md](./docs/CONTRIBUTING.md)
- [LICENSE.md](./docs/LICENSE.md)
- [CHANGELOG.md](./docs/CHANGELOG.md)
- [Roadmap.md](./docs/Roadmap.md)

---

## ტექნიკური ურთიერთდამოკიდებულება და Best Practices

- **ყველა მოდული არის loosely coupled, strongly typed, scalable, i18n-ready.**
- **UserID** არის ყველა ურთიერთქმედების საწყისი წერტილი (Auth, Payments, Artworks, Communities).
- **Artworks**–ზე დამოკიდებულია ACQ, Payments, Copyright, Communities.
- **Payments**–ზე დამოკიდებულია Withdrawal, Subscriptions, NFT, Notifications.
- **Notifications**–ზე დამოკიდებულია ყველა ძირითადი ღონისძიება (comment, sale, abuse, support).
- **AI Service** მხოლოდ REST-ით უკავშირდება Backend-ს (secured, private network).
- **Admin Actions** ყოველთვის audit-logged, rollback-ready.
- **Data privacy** ყველა დონეზე დაცულია (encryption, access policy, audit).

---

## TODOs და Checklist (AI Agent-სთვის)

- [ ] სქემის ყველგან შესაბამისობა (ERD↔DB↔API↔UI)
- [ ] ყველა ახალი ველი/endpoint აღწერილი swagger.yaml-ში
- [ ] ყველა ფაილი/დანართი README-ს ბმულებში
- [ ] ყველა მოდულის ინიციალიზაცია/ტესტი ავტომატიზებული
- [ ] ყველა სამართლებრივი მოთხოვნა (copyright, privacy) ტექნიკურად enforce-ებულია
- [ ] API Versioning და deprecation მართვა
- [ ] Mobile/PWA compatibility
- [ ] Security and backup coverage
- [ ] ყველა UI ტექსტი resources/lang-ში (i18n)
- [ ] ყველა ცვლილება CHANGELOG.md-ში

> ყოველთვის გამოიყენეთ [ADVANCED_FEATURES_TECH_SPEC.md](./docs/ADVANCED_FEATURES_TECH_SPEC.md),  
> განსაკუთრებით სანამ ფორმალურად დააბრუნებთ თქვენს გადაწყვეტილებას ან პასუხს.

---

**შენიშვნა:**  
დოკუმენტი განკუთვნილია როგორც ადამიანებისთვის, ასევე ნებისმიერი თანამედროვე AI კოდგენერატორისთვის.  
ყველა სექცია და ბმული უნდა იყოს სინქრონიზებული დანართებთან და ტექნიკურ მოდულებთან.  
**არ გამოტოვო არც ერთი დეტალი:** ნებისმიერი ახალი ველი, სერვისი ან ფუნქცია – დაამატე შესაბამის ცხრილში, დოკუმენტაციაში და ტესტებში.

---

**Acumen Craft – შენი ინტელექტი, შენი ხელოვნება, შენი სივრცე.**
