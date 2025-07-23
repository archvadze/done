# ინტერნაციონალიზაცია, AI და WEB სერვისების არქიტექტურა – დეტალური ტექნიკური დავალება

---

## 1. ინტერნაციონალიზაცია (i18n) და მრავალენოვნება

### 1.1. მიზანი
- პლატფორმის სრული მხარდაჭერა ინგლისურ, გერმანულ და სხვა ენებზე.
- UI, system messages, validation, email და ნამუშევრის აღწერების მრავალენოვანი რენდერი.

### 1.2. ტექნიკური რეალიზაცია

#### a) Laravel (Backend & Frontend)
- გამოიყენე [Laravel Localization](https://laravel.com/docs/12.x/localization).
- **resources/lang/{locale}/** დირექტორიაში თითოეული ენისათვის ცალკე ფოლდერი: `en`, `de`, ...
- ყველა სტრინგი (UI, ვალიდაცია, სისტემური შეტყობინებები) გადატანილია `lang` ფაილებში.
- Blade-ში გამოიყენე: 
  ```blade
  {{ __('messages.welcome') }}
  ```
- მომხმარებლისთვის ენის არჩევის ფუნქცია (UI-ში language switcher).
- Authentication და validation მესიჯები ავტომატურად ითარგმნება.

#### b) Frontend JS (Alpine.js, Tailwind, Vite)
- გამოიყენე JS-ზე ლოკალიზაციის helper-ები (მაგალითად, Alpine Store ან Vue-i18n, თუ გაქვს SPA).
- პლაგინები/კონფიგი: ტექსტების გადაცემა JS მოდულებიდან ან JSON ფაილებიდან.

#### c) Content Multilingual Support
- **ნამუშევრის ტიტული/აღწერა**:  
  - მონაცემთა ბაზაში დამატებითი ველი (JSON):  
    ```sql
    title_translations JSON, description_translations JSON
    ```
  - მაგალითი:
    ```json
    {
      "en": "Sunset",
      "de": "Sonnenuntergang"
    }
    ```
  - UI-ში ენის არჩევისას ავტომატურად რენდერდება შესაბამისი ტექსტი, fallback: EN.

#### d) Admin Panel & Seeders
- ადმინისტრატორის პანელი უზრუნველყოფს ყველა ენის ტექსტების მართვას.
- Seeder-ები და ფიქსირებული მონაცემები (ტეგები, კატეგორიები) ინახება მრავალენოვან ფორმატში.

#### e) E-mails, Notifications
- ყველა სისტემური შეტყობინება იგზავნება მომხმარებლის არჩეულ ენაზე.
- გამოიყენე Laravel Notification Channels-ის ლოკალიზაცია.

### 1.3. რეკომენდაციები
- გამოიყენე [Laravel Localization Middleware](https://laravel.com/docs/12.x/localization#defining-the-current-locale) URL-based ან Cookie-based ენების არჩევისთვის.
- ყველა ახალი ფუნქციონალის დიზაინი დაიგეგმოს მრავალენოვნების და Unicode-ის სრული მხარდაჭერით.
- რეგულარული QA მრავალ ენაზე.

---

## 2. AI და WEB სერვისების კომუნიკაცია – დეტალური არქიტექტურა

### 2.1. არქიტექტურული აღწერა

#### სტრუქტურა:
```
+-----------------+      +-------------------------+      +-------------------+
|   Web Browser   | <--> |   Laravel Backend/API   | <--> |   AI Service      |
+-----------------+      |  (PHP, Nginx, Redis)   |      |   (Python, Flask) |
                         +-------------------------+      +-------------------+
                                 |
                         +-------v---------+
                         |   MariaDB DB    |
                         +-----------------+
```

#### დეტალები:
- **Web Browser** (UI): ფრონტენდი (Blade/Tailwind/Alpine.js, ან SPA).
- **Laravel Backend**: API, ავტორიზაცია, ბიზნეს ლოგიკა, მონაცემთა მართვა, AI სერვისთან REST-კომუნიკაცია.
- **AI Service** (Flask/FastAPI): იზოლირებული სერვისი, იღებს მოთხოვნებს API-დან, აბრუნებს ანალიზს/ქულებს.
- **Redis**: caching, queue, rate-limiting.
- **MariaDB**: მონაცემთა ბაზა.

### 2.2. კომუნიკაციის პროტოკოლი

#### a) RESTful API (HTTP JSON)
- **Laravel** იყენებს Guzzle/Http Client-ს:
  ```php
  $response = Http::post('http://ai:5000/api/v1/evaluate', [
      'artwork_id' => $artwork->id,
      'description' => $artwork->description,
      'language' => $locale, // ენაზე გათვალისწინებული ანალიზი (თუ საჭიროა)
  ]);
  ```
- **AI სერვისი** აბრუნებს JSON პასუხს:
  ```json
  {
    "acumen_score": 8.4,
    "analysis": {
      "en": "Deep conceptual value.",
      "de": "Tiefer konzeptueller Wert."
    }
  }
  ```
- **AI სერვისის Endpoint-ები**:
  - /api/v1/evaluate (POST): ნამუშევრის ანალიზი
  - /api/v1/batch-evaluate (POST): რამდენიმე ნამუშევრის ანალიტიკა

#### b) ავტორიზაცია
- მხოლოდ შიდა REST API (Docker ქსელი), არ საჭიროებს JWT-ს (ლოკალურად).
- გლობალურ გარემოში გამოიყენე API Key ან OAuth2 protected endpoints.

#### c) Rate Limiting და Queue
- მაღალი დატვირთვისთვის: შეფასების მოთხოვნები იყრება queue-ში (Laravel Queue, Redis).
- AI სერვისი ამუშავებს მოთხოვნებს რიგის მიხედვით.

#### d) Error Handling & Monitoring
- ყველა კომუნიკაცია ლოგირებულია (request/response).
- შეცდომები (timeout, invalid input, AI crash) ინახება Sentry-ში.

---

## 3. სამუშაო გარემოს რეკომენდაციები: ლოკალური და გლობალური სერვერები

### 3.1. ლოკალური გარემო (DDEV/Docker)

- **DDEV**: მართავს ყველა სერვისს ერთ კონტეინერიზებულ გარემოში.
    - `laravel` (nginx, php), `mariadb`, `redis`, `ai` (python)
- **სერვისები** პირდაპირ შიდა ქსელით უკავშირდებიან (http://ai:5000).
- **კონფიგურაცია**: .ddev/config.yaml-ში ყველა სერვისის აღწერა.
- **ცხრილების/მოდელების მიგრაციები**: artisan და flask-ის Alembic ან სხვა მიგრაციების გამოყენება.
- **ტესტირება**: ხელმისაწვდომია ყველა სერვისისთვის (ddev exec).

### 3.2. გლობალური (Production) გარემო

- **Docker Compose ან Kubernetes** გამოიყენე სერვისების ორკესტრაციისთვის.
    - ინფრასტრუქტურა: AWS ECS/EKS, Azure AKS, Google GKE ან DigitalOcean App Platform
- **Web/API Layer**: Nginx Load Balancer, multiple Laravel app replicas (auto scaling).
- **AI Service**: იზოლირებული Python-based სერვერი (Dockerized), განცალკევებული რესურსებით (CPU/GPU).
- **Database**: MariaDB (managed service), Read Replica-ებით, სავალდებულო backup-ით.
- **Caching/Queue**: AWS ElastiCache Redis ან სხვა მენეჯდ პლატფორმა.
- **File Storage**: AWS S3, presigned URLs, CloudFront CDN.
- **Secrets Management**: AWS Secrets Manager, .env ფაილები არ ინახება vcs-ში.
- **Monitoring**: Sentry, New Relic, AWS CloudWatch ლოგები.
- **CI/CD**: GitHub Actions → Docker Registry → Deploy Script.

#### სქემა (Production):

```
+----------------------+
|     Load Balancer    |
+----------+-----------+
           |
+----------v-----------+
|   Laravel App (N)    | <----> | Redis Cache/Queue |
+----------+-----------+         +------------------+
           |
+----------v-----------+
|     AI Service (N)   |
+----------+-----------+
           |
+----------v-----------+
|   MariaDB Cluster    |
+----------+-----------+
           |
+----------v-----------+
|       AWS S3         |
+----------------------+
```

#### რეკომენდაციები:

- თითოეული სერვისი იზოლირებულია (Zero Trust Principle).
- სერვისებს შორის კომუნიკაცია მხოლოდ შიდა ქსელით.
- ყველა კონტეინერს აქვს საკუთარი Resource Limits (CPU/RAM).
- Auto Scaling: Laravel/AI სერვისისთვის ჰორიზონტალური მასშტაბირება.
- Cloud CDN ფაილების სწრაფი მიწოდებისთვის.
- ინფრასტრუქტურის IaC (Terraform, AWS CloudFormation) გამოყენება.
- ყოველ გარემოში იდენტური კოდის deploy. პარამეტრები .env ან Secrets Manager-ით.

---

## 4. უსაფრთხოების best practices

- **TLS/SSL** ყველგან — ანაზღაურებადი სერტიფიკატები პლიუს ჰოსტინგის სერტიფიკატები.
- **API Keys** მხოლოდ შიდა use-case-ებისთვის, გარე API-სთან OAuth2.
- **Secrets** მხოლოდ Secrets Manager-ით.
- **Backup/Restore** როლბექის დაგეგმილი სცენარები.
- **Sentry/New Relic** აქტიურად ჩართული ყველა სერვისზე.

---

## 5. ტესტირება და Deploy

- **ლოკალურად:**  
  - ყველა სერვისზე დამოუკიდებელი ტესტები (PHPUnit, PyTest)
  - Integration ტესტები (Laravel ↔ Flask)
- **CI/CD:**  
  - GitHub Actions: build, test, docker image push
  - Deploy script/runner: ECS/EKS/GKE-ზე განახლება
- **Rollback:**  
  - ყოველი რელიზის წინ backup
  - Health check და ავტომატური rollback-ი

---

## 6. დოკუმენტაციის რეკომენდაციები

- ყველა ახალი ფუნქციონალის/endpoint-ის დოკუმენტაცია ინახება [swagger.yaml](./swagger.yaml) ან Postman Collection-ში.
- ყველა ენის ტექსტები version control-ში (resources/lang/).
- გაწერე usage guide თითოეული ენისთვის (README-ის დანართი).

---

**შესაძლებელია ამ სტრუქტურის პირდაპირ ინტეგრირება შენს პროექტში, მინიმალური ცვლილებებით, მაქსიმალური უსაფრთხოებითა და ზრდადობით.**

---

_შემიძლია შემდგომ ეტაპზე მოგიმზადო:_
- მიგრაციების და მოდელების მაგალითები მრავალენოვანი მონაცემებისთვის
- API კონტროლერების მაგალითები i18n/AI-სთვის
- Docker Compose ან Kubernetes yaml-ების ნიმუშები
- AWS/Cloud ინფრასტრუქტურის IaC მაგალითები

**დამიკონკრეტე, რომელი ბლოკი გინდა კიდევ უფრო გაღრმავებულად!**