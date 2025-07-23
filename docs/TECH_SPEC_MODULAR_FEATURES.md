# დამატებითი მოდულური ფუნქციონალი – ტექნიკური დავალება

_ვერსია 1.0 – გამოყოფილი ცხრილები, მოდულური API, სრული უკუ თავსებადობა_

---

## 1. ACQ – Acumen Craftsmanship Quotient

**მიზანი:**  
გენერირდეს ინტელექტუალური ქულა (ACQ) თითოეული ნამუშევრისთვის/ხელოვანისთვის, რომელიც დინამიურად განახლდება შეფასებებისა და AI ანალიზის საფუძველზე.

### 1.1. მონაცემთა ბაზა

**ახალი ცხრილი:**  
```sql
CREATE TABLE acq_scores (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  artwork_id BIGINT UNSIGNED,
  user_id BIGINT UNSIGNED, -- optional: aggregate per artist
  acq_score DECIMAL(4,2) NOT NULL,
  calculated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  source ENUM('human','ai','aggregate') DEFAULT 'aggregate',
  -- შენიშვნა: შესაძლებელია score-ს ისტორიის შენახვა (e.g. versioning)
  FOREIGN KEY (artwork_id) REFERENCES artworks(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

### 1.2. Backend API

**Endpoints:**

- **GET /api/v1/artworks/{id}/acq**  
  აბრუნებს კონკრეტული ნამუშევრის ACQ ქულას.
- **GET /api/v1/users/{id}/acq**  
  აბრუნებს ხელოვანის საშუალო ACQ-ს (ყველა ნამუშევრის აგრეგაცია).
- **POST /api/v1/artworks/{id}/acq/recalculate**  
  ხელახლა ითვლის ACQ-ს (trigger, მხოლოდ მოდერატორის/Admin-ისთვის).

### 1.3. ლოგიკა

- ACQ გამომითვლება შეფასებების (evaluations) საშუალო არითმეტიკული, პლიუს AI ანალიზი (საშუალო ან წონიანი).
- ყოველი ახალი შეფასების ან AI ანალიზის შემდეგ ACQ ავტომატურად განახლდება.
- ისტორიისთვის შესაძლებელია ცხრილში versioning (multiple rows per artwork).

### 1.4. Frontend

- ნამუშევრის გვერდზე: ACQ ქულა (გრაფიკული/რიცხვითი), tooltip-ზე განმარტება.
- ხელოვანის პროფილზე: საშუალო ACQ, ტოპ ნამუშევრები ACQ-ით.

---

## 2. Subscriptions – პერიოდული მხარდაჭერა

**მიზანი:**  
პატრეონის მსგავსი სუბსკრიპციული მხარდაჭერის მოდული, სადაც მომხმარებლებს შეუძლიათ არჩეული არტისტების რეგულარული დაფინანსება.

### 2.1. მონაცემთა ბაზა

**ახალი ცხრილები:**
```sql
CREATE TABLE subscriptions (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  patron_id BIGINT UNSIGNED NOT NULL,
  artist_id BIGINT UNSIGNED NOT NULL,
  period ENUM('monthly','quarterly','yearly') NOT NULL,
  amount DECIMAL(8,2) NOT NULL,
  currency VARCHAR(3) NOT NULL,
  status ENUM('active','paused','cancelled','expired') DEFAULT 'active',
  started_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  last_payment_at TIMESTAMP,
  stripe_subscription_id VARCHAR(255), -- Stripe integration
  paypal_subscription_id VARCHAR(255), -- PayPal integration
  FOREIGN KEY (patron_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (artist_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE subscription_payments (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  subscription_id BIGINT UNSIGNED NOT NULL,
  payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  amount DECIMAL(8,2) NOT NULL,
  currency VARCHAR(3) NOT NULL,
  payment_status ENUM('pending','completed','failed') DEFAULT 'pending',
  external_payment_id VARCHAR(255), -- Stripe/PayPal payment reference
  FOREIGN KEY (subscription_id) REFERENCES subscriptions(id) ON DELETE CASCADE
);
```

### 2.2. Backend API

- **POST /api/v1/artists/{id}/subscribe**  
  Request: {amount, period, payment_method}  
  Stripe/PayPal Checkout Session redirect
- **GET /api/v1/users/{id}/subscriptions**  
  აბრუნებს მომხმარებლის აქტიურ/წარსულ სუბსკრიპციებს.
- **POST /api/v1/subscriptions/{id}/cancel**  
  წყვეტს სუბსკრიპციას.

### 2.3. Stripe/PayPal ინტეგრაცია

- გამოიყენეთ Stripe Subscription API (recurring billing plans).
- შეინახეთ Stripe/PayPal-ის IDs ცხრილში.
- Webhooks-ის მხარდაჭერა (გადახდის სტატუსის განახლება).

### 2.4. Frontend

- არტისტის გვერდზე: „გახდი მხარდამჭერი“ ღილაკი, სუბსკრიპციის მოდალი (period picker).
- მომხმარებლის პროფილში: აქტიური სუბსკრიპციების სია, სახელშეკრულებო სტატუსი.

---

## 3. Communities – ინტერესთა გაერთიანებები

**მიზანი:**  
საზოგადოების (interest-based communities) ფუნქციონალი Reddit-ის ან Facebook Groups-ის სტილში, სადაც მომხმარებლები ქმნიან თემატურ ჯგუფებს, პოსტავენ, წყვეტენ დისკუსიებს.

### 3.1. მონაცემთა ბაზა

```sql
CREATE TABLE communities (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(100) NOT NULL,
  description TEXT,
  created_by BIGINT UNSIGNED NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (created_by) REFERENCES users(id)
);

CREATE TABLE community_members (
  community_id BIGINT UNSIGNED NOT NULL,
  user_id BIGINT UNSIGNED NOT NULL,
  role ENUM('admin','moderator','member') DEFAULT 'member',
  joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (community_id, user_id),
  FOREIGN KEY (community_id) REFERENCES communities(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE community_posts (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  community_id BIGINT UNSIGNED NOT NULL,
  user_id BIGINT UNSIGNED NOT NULL,
  content TEXT,
  media_path VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (community_id) REFERENCES communities(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE community_post_comments (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  post_id BIGINT UNSIGNED NOT NULL,
  user_id BIGINT UNSIGNED NOT NULL,
  content TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (post_id) REFERENCES community_posts(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

### 3.2. Backend API

- **GET /api/v1/communities**  
  თემატური ჯგუფების სია (ფილტრაცია/საძიებო).
- **POST /api/v1/communities**  
  ახალი ჯგუფის შექმნა.
- **POST /api/v1/communities/{id}/join**  
  გაწევრიანება.
- **GET /api/v1/communities/{id}/posts**  
  ჯგუფის პოსტების სია.
- **POST /api/v1/communities/{id}/posts**  
  ახალი პოსტის დამატება.
- **POST /api/v1/community-posts/{id}/comment**  
  კომენტარის დამატება პოსტზე.

### 3.3. Frontend

- მენიუში: „გაერთიანებები“ – თემატური ჯგუფების ფიდი, შექმნა, ძიება.
- ჯგუფის გვერდი: პოსტების ფიდი, წევრთა სია, გაწევრიანების/გამოსვლის ღილაკი.
- პოსტის გვერდი: კომენტარები, მოწონებები.

---

## 4. უსაფრთხოება, ACL და უკუ თავსებადობა

- ყველა ცხრილი იყენებს FOREIGN KEY constraints-ს, მონაცემთა მთლიანობისთვის.
- API-ში აუცილებელია ავტორიზაცია და როლებზე დაფუძნებული წვდომა (middleware).
- არსებული მონაცემები და ფუნქციონალი უცვლელი რჩება — ახალი მოდულები იზოლირებულია (არ არღვევენ ძველ ცხრილებს).
- ყველა ახალი სექციისთვის უნდა დაიწეროს unit და integration ტესტები (PHPUnit/pytest).

---

## 5. დამატებითი რეკომენდაციები

- **მიგრაციები:** ყველა SQL ცვლილება განახორციელეთ Laravel-ის მიგრაციებით.
- **დოკუმენტაცია:** განაახლეთ API Reference და README.
- **CI/CD:** დაამატეთ ტესტები და Webhook-ების simulation სტეიჯები.
- **UI/UX:** ახალი ფუნქციონალი ინტეგრირდეს არსებულ დიზაინში, შეინარჩუნეთ ბრენდინგი და სტილი.

---

## 6. დანართი: API Endpoint-ების მაგალითები

**ACQ:**  
- `GET /api/v1/artworks/42/acq` → `{ "artwork_id": 42, "acq": 7.4, "updated_at": "..." }`
- `GET /api/v1/users/1/acq` → `{ "user_id": 1, "avg_acq": 8.1 }`

**Subscription:**  
- `POST /api/v1/artists/2/subscribe` → Stripe redirect/session
- `GET /api/v1/users/1/subscriptions` → სუბსკრიპციების სია

**Communities:**  
- `GET /api/v1/communities` → თემების სია
- `POST /api/v1/communities/3/posts` → ახალი პოსტი

---

**ყველა მოდული შესაძლებელია ჩაირთოს ან გამოირთოს დამოუკიდებლად, მონაცემთა მთლიანობის ან არსებული ფუნქციონალის დარღვევის გარეშე.**

---
