# საავტორო უფლებების ტექნიკური მოდული – Acumen Craft

---

## მიზანი

სისტემაში სრულად იყოს ასახული საავტორო უფლებების და კონტენტის მფლობელობის მექანიზმი – როგორც მონაცემთა მოდელში, ასევე API-ში, UI-ში, ბიზნეს ლოგიკასა და ადმინისტრაციაში.  
პლატფორმა უზრუნველყოფს:  
- ავტორის უფლებების შენარჩუნებას  
- ლიცენზირების მოქნილ არჩევანს  
- საავტორო განაცხადის დეკლარაციას  
- ციფრული "თითის ანაბეჭდის" გენერაციას  
- დარღვევების მოხსენების მექანიზმს  
- დამატებით: Blockchain timestamp-ს და NFT ownership-ს (სურვილის შემთხვევაში)

---

## 1. მონაცემთა მოდელი (ERD & მიგრაციები)

### 1.1. artworks ცხრილში ახალი ველები

| ველი                  | ტიპი               | აღწერა |
|-----------------------|--------------------|--------|
| copyright_notice      | TEXT               | ავტორის საავტორო განაცხადი/დეკლარაცია |
| license_type          | ENUM               | ლიცენზიის ტიპი (all_rights_reserved, cc_by, cc_by_sa, cc_by_nc, cc_by_nd, nft, ai_generated) |
| license_detail        | TEXT/JSON          | ლიცენზიის დეტალები (CC ვერსიები, NFT info, AI involvement) |
| file_hash             | VARCHAR(128)       | SHA-256 ან სხვა ჰეში ატვირთვისას |
| upload_timestamp      | TIMESTAMP          | ატვირთვის დრო (UTC) |
| watermark_meta        | JSON               | watermark-ის/შტამპის მეტამონაცემები |
| visibility            | ENUM               | public, private, unlisted |
| blockchain_txid       | VARCHAR(128)       | ბლოკჩეინზე დამოწმების TxID (არჩევითი) |
| ai_generated          | BOOLEAN            | AI involvement flag |
| ai_edit_detail        | TEXT               | ადამიანის ჩარევის აღწერა |

**SQL მიგრაცია:**  
```sql
ALTER TABLE artworks
  ADD COLUMN copyright_notice TEXT,
  ADD COLUMN license_type ENUM('all_rights_reserved','cc_by','cc_by_sa','cc_by_nc','cc_by_nd','nft','ai_generated') DEFAULT 'all_rights_reserved',
  ADD COLUMN license_detail TEXT,
  ADD COLUMN file_hash VARCHAR(128),
  ADD COLUMN watermark_meta JSON,
  ADD COLUMN visibility ENUM('public','private','unlisted') DEFAULT 'public',
  ADD COLUMN blockchain_txid VARCHAR(128),
  ADD COLUMN ai_generated BOOLEAN DEFAULT 0,
  ADD COLUMN ai_edit_detail TEXT;
```

### 1.2. infringement_reports (საავტორო დარღვევების განცხადებები)

```sql
CREATE TABLE infringement_reports (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  artwork_id BIGINT UNSIGNED,
  reporter_id BIGINT UNSIGNED NULL,
  reporter_contact VARCHAR(255),
  description TEXT,
  assertion TEXT,
  evidence TEXT,
  status ENUM('pending','reviewing','resolved','rejected') DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  reviewed_at TIMESTAMP NULL,
  FOREIGN KEY (artwork_id) REFERENCES artworks(id),
  FOREIGN KEY (reporter_id) REFERENCES users(id)
);
```

---

## 2. Backend ლოგიკა & API

### 2.1. Artwork Upload/Update

- ატვირთვისას:
  - ითვლება file_hash (SHA-256, HEX).
  - ინახება upload_timestamp.
  - იწერება copyright_notice.
  - ირჩევა license_type/CC ვერსია.
  - ინახება watermark_meta (JSON).
  - ინახება visibility.
  - სურვილის შემთხვევაში ხდება blockchain_txid-ის გენერაცია (გარე სერვისით).
  - AI involvement და შესაბამისი აღწერა.

**API Endpoint:**  
- `POST /api/v1/artworks`  
- `PUT /api/v1/artworks/{id}`

**Request Body (JSON):**
```json
{
  "title_translations": {"en":"...", "de":"..."},
  "description_translations": {"en":"...", "de":"..."},
  "copyright_notice": "...",
  "license_type": "cc_by_sa",
  "license_detail": "...",
  "visibility": "public",
  "ai_generated": true,
  "ai_edit_detail": "...",
  "blockchain_proof": true
}
```

**Response:**  
- აბრუნებს ყველა ზემოთ ჩამოთვლილ ველს, პლუს file_hash, upload_timestamp, watermark_meta.

### 2.2. Blockchain Timestamp (სურვილის შემთხვევაში)

- `POST /api/v1/artworks/{id}/blockchain-proof`
- სისტემამ იღებს file_hash-ს, აბრუნებს TxID-ს, ინახავს blockchain_txid-ში.
- შესაძლებელია 3rd-party integration (Ethereum, Solana, Arweave).

### 2.3. Report Infringement

- `POST /api/v1/report-infringement`
  - params: artwork_id, reporter_contact, description, assertion, evidence
- ინახავს infringement_reports ცხრილში.
- ადმინისტრატორი იღებს შეტყობინებას.

- `GET /api/v1/infringement-reports` (admin)
  - სტატუსის ფილტრი, განხილვის/გადაწყვეტილების ფუნქცია.

---

## 3. Frontend / UI-UX

### 3.1. Artwork Upload/Manage

- **საავტორო განაცხადი:** Textarea ("გთხოვთ მიუთითოთ თქვენი საავტორო განაცხადი ან დეკლარაცია")
- **ლიცენზიის არჩევა:**
  - Dropdown (All Rights Reserved, CC BY, CC BY-SA, CC BY-NC, CC BY-ND, NFT, AI Generated)
  - Tooltip-ები და ლოგოები (Creative Commons)
  - License detail-ის დამატება (optional field)
- **AI involvement:** Checkbox ("შეიქმნა ხელოვნური ინტელექტით"), დამატებითი აღწერის ველი.
- **Visibility:** Radio (Public/Private/Unlisted)
- **Blockchain proof:** Toggle (ჩართვა/გამორთვა)
- **Watermark (შტამპი):** სისტემური ავტომატური გენერაცია, ან მომხმარებლის არჩევანი.

### 3.2. Artwork View

- საავტორო განაცხადი და ლიცენზიის ტიპი (CC ლოგო, NFT ბმული, AI involvement)
- Watermark overlay ან მეტამონაცემებში ინფორმაციის ჩვენება.
- "Report Infringement" ღილაკი.

### 3.3. Report Infringement ფორმა

- გამოჩნდება ყველა ნამუშევრის გვერდზე.
- ველები: artwork_id (hidden), contact, description, assertion, evidence (file/url/optional).
- წარმატების შეტყობინება.

---

## 4. ბიზნეს ლოგიკა

- ატვირთვისას მხოლოდ იმ მომხმარებელს შეუძლია ატვირთვა, რომელსაც აქვს შესაბამისი უფლება (role-based access).
- თუ ნამუშევარი არის AI Generated ან NFT, შესაბამისი ველი აუცილებლად უნდა იყოს შევსებული.
- Blockchain timestamp-ის ჩაწერა აუცილებელია მხოლოდ თუ ჩართულია პარამეტრში.
- Visibility-ს მიხედვით, ნამუშევრის ნახვის უფლება.
- infringement_reports-ის სტატუსი მხოლოდ admin/moderator-ს შეუძლია შეცვალოს.

---

## 5. უსაფრთხოება

- ყველა ჩანაწერის ავტორიზაცია (მხოლოდ მფლობელი ან ადმინი).
- License type-სთვის whitelist/validation.
- Evidence attachments whitelist (filetype, size), სკანირება malicious code-ზე.
- API rate limit Report Infringement-ზე.
- ყველა ცვლადი და პარამეტრი escaping/XSS safe.

---

## 6. ადმინისტრაცია

- infringement_reports-ის სია, სტატუსის შეცვლა, კომენტარი.
- dispute escalation – საჭიროების შემთხვევაში პლატფორმის იურისტის ჩართვა.
- ყველა ცვლილების audit log.

---

## 7. დოკუმენტაცია & Usage

- [USER_COPYRIGHT.md](./USER_COPYRIGHT.md) – სრული, მომხმარებლებისთვის გასაგები ვერსია (ადვილად ხელმისაწვდომი upload/manage გვერდიდან).
- [swagger.yaml] – ყველა API Endpoint აღწერით, პარამეტრებით, სტატუსებით.
- UI/UX mockup-ები (branding.pdf/UI Mockups).
- ადმინისტრატორის სახელმძღვანელო (admin guide).

---

## 8. Roadmap (ქვეტასქები)

- მონაცემთა მოდელის გაფართოება (artworks + infringement_reports)
- API endpoints-ის დამატება/ტესტირება
- Artwork Upload/Manage UI-ს გაფართოება (copyright/license)
- Blockchain integration (optional)
- Report Infringement ფორმის და backend-ის იმპლემენტაცია
- ადმინისტრატორის პანელი (reports management)
- დოკუმენტაციის განახლება (user, API, admin)
- ტესტები: upload, viewing, reporting, admin actions

---

## 9. დანართი – მთავარ README-ში ბმული

README.md-ში ჩასასმელად:  
````markdown
- [მომხმარებლის საავტორო უფლებები და კონტენტის მართვა](./USER_COPYRIGHT.md)