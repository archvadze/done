# Acumen Craft – Mobile App Development Guide

---

## 1. მიზანი

Acumen Craft-ის მობილური აპლიკაცია უნდა უზრუნველყოფდეს სრულფასოვან და კომფორტულ წვდომას პლატფორმის ძირითად და მოწინავე ფუნქციონალზე, როგორც მომხმარებლისთვის, ისე შემოქმედებისთვის.  
აპის მთავარი ფოკუსია ხელოვნების ბრაუზინგი, ატვირთვა, შეფასება, კომუნიკაცია, ფინანსური ოპერაციები და უსაფრთხოება.

---

## 2. ტექნოლოგიური სტეკი

- **Flutter** (Dart) – Cross-platform (Android/iOS, Web optional)
- **React Native** (TypeScript/JS) – Cross-platform (Android/iOS)
- **API:** RESTful (OpenAPI/swagger.yaml)
- **Auth:** JWT, OAuth2 (Social), 2FA (TOTP/SMS)
- **Push Notifications:** Firebase Cloud Messaging (FCM)
- **Media uploads:** Multipart/form-data (S3 presigned URLs)
- **Payments:** Stripe/PayPal SDKs, external browser fallback
- **Deep Linking, Universal Links** მხარდაჭერა

---

## 3. ფუნქციონალური მოდულები

### 3.1. Authentication & Profile

- Email/password login, Social OAuth (Google, Apple, Facebook)
- Two-Factor authentication (OTP)
- Account recovery, password reset
- Profile management (avatar, bio, creative field, preferences)
- Linked accounts

### 3.2. Artworks

- Infinite scroll feed (public artworks; filtering by tags/category)
- Artwork detail view (media, author, ACQ score, license, AI-gen badge)
- Upload (multi-language title/desc, media picker, license, AI-generated?)
- Edit/delete own artwork
- Like, comment, share (native share dialog)

### 3.3. Evaluation & ACQ

- Artwork evaluation forms (sliders, feedback)
- Display own and global ACQ scores/leaderboards

### 3.4. Payments & NFT

- Donate to artist, buy artwork, subscribe
- Stripe/PayPal in-app or browser
- Crypto payments (external wallet, QR code, deeplink)
- NFT minting (connect wallet, MetaMask/WalletConnect, view on explorer)

### 3.5. Chat/AI/Notifications

- Real-time chat (WebSocket or REST polling fallback)
- Group/community chat
- AI assistant (chatbot, helpdesk)
- In-app notifications, push notifications
- Settings for notification preferences

### 3.6. Communities & Social

- Browse/join communities, group discussions
- User search, follow/unfollow
- Community posts/comments, moderation

### 3.7. HelpDesk & Support

- FAQ, search, open support ticket
- Ticket status tracking, messaging with support

### 3.8. Privacy, Security, Settings

- Privacy settings (profile visibility, data download/erase)
- 2FA enable/disable
- Language/theme change (dark/light mode)
- Session/device management

---

## 4. დიზაინი & UX

- Mobile-first, adaptive layouts
- Material (Flutter) ან iOS/Android native look (React Native)
- VoiceOver/Accessibility მხარდაჭერა
- Onboarding walkthrough (Intro.js/flutter_walkthrough)
- Multilingual (i18n ready)
- Error/loading states, empty screens

---

## 5. ინტეგრაციები

- API base URL: `/api/v1/`
- OpenAPI Reference: [swagger.yaml](./docs/swagger.yaml)
- Webhooks: [WEBHOOKS.md](./docs/WEBHOOKS.md)
- Asset uploads: S3 presigned URLs (get via API)
- Push notifications setup: FCM/APNs, [Notifications Guide](./docs/README.md#Notifications)

---

## 6. უსაფრთხოება

- JWT securely stored (Keychain/Keystore or SecureStorage)
- Refresh token rotation
- SSL pinning (optional)
- Clipboard protection for sensitive data
- 2FA enforcement

---

## 7. Deployment & Distribution

- Android: Google Play (Internal Test, Beta, Release)
- iOS: TestFlight, App Store
- CI/CD: GitHub Actions, Codemagic, Bitrise (optional)
- Beta groups for user feedback

---

## 8. სპეციალური შენიშვნები

- ყველა მოდული უნდა იყოს loosely coupled და მარტივად გაფართოებადი.
- აუტენტიფიკაცია და ფაილების ატვირთვა მაქსიმალურად მარტივი და დაცული.
- მონაცემთა სინქრონიზაცია – pull-to-refresh, background sync (optional).
- App Store/Google Play რეგულაციებთან შესაბამისობა.

---

## 9. დამატებითი რესურსები

- [swagger.yaml](./docs/swagger.yaml) – API Reference
- [WEBHOOKS.md](./docs/WEBHOOKS.md) – Webhooks
- [UI_MOCKUPS.md](./docs/UI_MOCKUPS.md) – UI/UX ნიმუშები
- [README.md](./docs/README.md) – სრული ტექნიკური დავალება

---

**შენიშვნა:**  
თუ გჭირდება კონკრეტული Flutter ან React Native გვერდის/კომპონენტის კოდის მაგალითი ან დამატებით UX/დიზაინის ფორმატში, მომწერე კონკრეტული მოთხოვნა!
