# Acumen Craft – არქიტექტურის მიმოხილვა

ეს დოკუმენტი აღწერს Acumen Craft პლატფორმის სისტემურ არქიტექტურას, ტექნოლოგიურ სტეკს, მოდულებსა და ურთიერთკავშირს.

---

## 1. არქიტექტურის საერთო ხედვა

Acumen Craft წარმოადგენს მოდულურ, ინკრემენტალურად განვითარებად პლატფორმას, რომელიც აერთიანებს თანამედროვე ვებ-ტექნოლოგიებს, ხელოვნური ინტელექტის ინტეგრაციას, უსაფრთხოების მაღალ სტანდარტებსა და სკალირებად ინფრასტრუქტურას.

**მთავარი პრინციპები:**
- Microservices/Modular მონოლითი (მოდულური გააზრება)
- RESTful API & WebSocket კომუნიკაცია
- Cloud-native, Docker-იზირებული, IaC მხარდაჭერით
- DevOps, CI/CD და ავტომატური ტესტირება
- უსაფრთხოება და GDPR/CCPA შესაბამისობა

---

## 2. ტექნოლოგიური სტეკი

| დონე              | ტექნოლოგია/პლატფორმა                          |
|-------------------|-----------------------------------------------|
| Frontend          | React.js, Next.js, TypeScript, Tailwind CSS   |
| Mobile            | React Native / Expo                           |
| Backend           | Node.js (Express.js)/NestJS, Python (AI მოდულები) |
| Database          | PostgreSQL (primary), Redis (cache/sessions)  |
| File Storage      | AWS S3 ან მსგავსი cloud storage                |
| AI/ML             | Python (TensorFlow, PyTorch), FastAPI         |
| Blockchain        | Ethereum/Solidity (NFT, provenance)           |
| CI/CD             | GitHub Actions, Docker, Terraform             |
| Monitoring        | Sentry, Prometheus, Grafana, CloudWatch       |

---

## 3. სისტემური მოდულები

- **Auth & User Management:** რეგისტრაცია, ავტორიზაცია, OAuth2, 2FA, User profile, როლები
- **Artwork Management:** ატვირთვა, აღწერა, ფაილების შენახვა, გამოქვეყნება, ლიცენზირება
- **ACQ (Acumen Craft Quotient):** AI-ზე დაფუძნებული შეფასების სისტემა, სკორინგი, ანალიტიკა
- **Feed & Discovery:** ნამუშევრების დინამიური ლენტა, კატეგორიებით/ტეგებით ძიება
- **Engagement & Social:** კომენტარები, მოწონებები, გაზიარება, თემები, შეტყობინებები
- **Moderation:** კონტენტის მოდერაცია (AI+Manual), მონიტორინგი, Flag/Report
- **Payments & Donations:** Stripe/PayPal ინტეგრაცია, კრიპტო-დონაციები, NFT
- **Notifications:** Email, In-app, Push
- **Admin Panel:** სტატისტიკა, მომხმარებლის მართვა, კონტენტის კონტროლი
- **API Layer:** REST/GraphQL, Rate limiting, API keys

---

## 4. არქიტექტურული სქემა (High-level Diagram)

```
[ User ] <==> [ Frontend (React/Next.js) ] <==REST/WS==> [ API Gateway ]
                                                   |
                                                   v
                          +----------------+-----------------------+
                          |                |                       |
                [ User Service ]   [ Artwork Service ]      [ ACQ/AI Service ]
                          |                |                       |
                    [ PostgreSQL ]   [ S3 Storage ]         [ Python/ML ]
                          |
                [ Notification/Email ]
                          |
                   [ Admin / Analytics ]
```

---

## 5. მონაცემთა ნაკადები და ინტეგრაცია

- **User Interaction:** მომხმარებელი ურთიერთობს Frontend-თან, რომელიც REST/GraphQL API-ს იყენებს მონაცემებისთვის.
- **Artwork Upload:** ატვირთული ფაილები ინახება S3-ში, მეტამონაცემები – PostgreSQL-ში.
- **AI/ACQ:** ნამუშევრის შეფასებისთვის ხდება ინტეგრაცია AI მიკროსერვისებთან (Python FastAPI).
- **Payments:** უსაფრთხო გადახდები ხდება Stripe/PayPal SDK-ებით ან Blockchain-ით.
- **Notifications:** ყველა მნიშვნელოვანი მოვლენა ინიცირებს შეტყობინებას (email, push).

---

## 6. უსაფრთხოება და შესაბამისობა

- JWT/OAuth2/2FA ავთენტიკაცია
- მონაცემთა დაშიფვრა (TLS, at-rest encryption)
- Rate limiting & Audit logging
- GDPR/CCPA შესაბამისობა, მონაცემთა წაშლის და ექსპორტის ფუნქციონალი
- ფილტრაცია და მოდერაცია (AI+Manual)

---

## 7. DevOps და გარემოები

- **Infrastructure as Code:** Terraform, Docker Compose
- **CI/CD:** ყველა ბრენჩზე ავტომატური ტესტირება და დეპლოი
- **Environment Separation:** Dev, Staging, Production
- **Monitoring & Alerting:** Sentry, Grafana, Cloudwatch

---

## 8. გაფართოებადობა და სკალირება

- სერვისების ჰორიზონტალური მასშტაბირება (Kubernetes-ready)
- Microservices ან მოდულური მონოლითი (future-proof)
- API gateway და reverse proxy (NGINX/Traefik)

---

## 9. დოკუმენტაცია და სტანდარტები

- ყველა მოდულის API აღწერა – OpenAPI/Swagger
- არქიტექტურის დიაგრამები – /docs/ არქივში (draw.io, mermaid)
- კოდის სტილი: ESLint, Prettier, EditorConfig, Conventional Commits

---

**დამატებითი კითხვებისთვის იხილეთ Wiki ან დაუკავშირდით ტექნიკურ გუნდს.**

Acumen Craft © 2025