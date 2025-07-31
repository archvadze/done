# Acumen Craft – ვიზუალური სტილის გზამკვლევი

ეს დოკუმენტი განსაზღვრავს ACUMEN CRAFT-ის ბრენდინგის, ფერების, ტიპოგრაფიისა და UI ელემენტების სტანდარტებს.

---

## 🎨 ფერების პალიტრა

| სახელი            | Hex კოდი | გამოყენება                                        |
| ----------------- | -------- | ------------------------------------------------- |
| შავი ფონი         | #090909  | ძირითადი ფონი                                     |
| მუქი ოქრო         | #c28840  | მთავარი სათაურები (H1-H2), აქცენტირებული ღილაკები |
| სტაფილოსფერი ოქრო | #d4a743  | საშუალო ზომის სათაურები (H3-H4), ჰოვერ-ეფექტები   |
| ღია ოქრო          | #f0c75e  | მცირე ზომის სათაურები (H5-H6), მცირე აქცენტები    |
| თეთრი             | #f9f9f9  | ძირითადი ტექსტი მუქ ფონზე                         |

 ---

## 🖼️ ლოგო და ფავიკონი

- **ლოგო**  
  გამოყენებული უნდა იყოს მხოლოდ SVG ან PNG ფორმატში, ვიზუალურად აღსაქმელი ზომით.  
  რეკომენდებულია გამოიყენოთ SVG ფორმატი ან მაღალი ხარისხის PNG.
  (../public/logo.png)
  (../public/logo.svg)

- **Favicons**  
  ფორმატი: .ico ან .png (16x16px ან 32x32px).  
  (./public/android-chrome-192x192.png)
  (../public/android-chrome-512x512.png)
  (../public/apple-touch-icon.png)
  (../public/favicon.ico)
  (../public/favicon-16x16.png)
  (../public/favicon-32x32.png)

---

## ✍️ ტიპოგრაფია

| ელემენტი  | შრიფტის ზომა | ფერი              | შრიფტი (რეკომენდაცია)                           |
| --------- | ------------ | ----------------- | ----------------------------------------------- |
| H1        | 36px         | მუქი ოქრო         | extrasquareCaps,NovaSquare, serif               |
| H2        | 28px         | მუქი ოქრო         | extrasquareCaps,NovaSquare, serif               |
| H3        | 24px         | სტაფილოსფერი ოქრო | extrasquareCaps,NovaSquare, serif               |
| H4        | 20px         | სტაფილოსფერი ოქრო | extrasquareCaps,NovaSquare, serif  (Sans-serif) |
| H5        | 17px         | ღია ოქრო          | extrasquareCaps,NovaSquare, serif  (Sans-serif) |
| H6        | 14px         | ღია ოქრო          | extrasquareCaps,NovaSquare, serif  (Sans-serif) |
| პარაგრაფი | 13px         | თეთრი             | extrasquare,NovaSquare, serif  (Sans-serif)     |

- **Fonts [ფონტები]**  
  (../public/fonts/bpg_extrasquare_2009.ttf)
  (../public/fonts/bpg_extrasquare_mtavruli_2009.ttf)
  (../public/fonts/NovaSquare-Regular.ttf)

---

## 🟨 ღილაკები და UI ელემენტები

| ელემენტი        | ფონი                | ტექსტის ფერი        | შენიშვნა                                          |
| --------------- | ------------------- | ------------------- | ------------------------------------------------- |
| მთავარი ღილაკი  | მუქი ოქრო (#c28840) | შავი                | ჰოვერზე ფერი იცვლება სტაფილოსფერ ოქროზე (#d4a743) |
| მეორადი ღილაკი  | გამჭვირვალე         | მუქი ოქრო (#f0c75e) | აქვს ღია ოქროს ფერი box-shadow                    |
| ტექსტური ღილაკი | არ აქვს             | ღია ოქრო (#f0c75e)  | ჰოვერზე ხაზი ესმება ქვემოდან                      |

---

## 🧱 კომპონენტების სტილი

- **ფორმები (Inputs):**  
  ფონი: #090909. 

- **ბარათები (Cards):**  
  ფონი: #090909.

- **მოდალური ფანჯრები:**  
  ფონი: #090909,  box-shadow მუქი ოქროს ფერი ჩარჩოთი.

---

## ⚙️ მაგალითი (CSS Snippet)

```css
@font-face {
  font-family: extrasquare;
  src: url(./public/fonts/bpg_extrasquare_2009.ttf);
}
@font-face {
  font-family: extrasquareCaps;
  src: url(./public/fonts/bpg_extrasquare_mtavruli_2009.ttf);
}
@font-face {
  font-family: NovaSquare;
  src: url(./public/fonts/NovaSquare-Regular.ttf);
}
body {
  background-color: #090909;
  color: #f9f9f9;
  font-family: 'extrasquare','NovaSquare', serif;
}

h1, h2 {
  color: #c28840;
  font-family: 'extrasquareCaps','NovaSquare', serif;
}

h3 {
  color: #d4a743;
  font-family: 'extrasquareCaps','NovaSquare', serif;
}
```

---

**შენიშვნა:**  
დამატებითი დეტალები და დიზაინის ფაილები იხილეთ /docs დირექტორიაში ან დიზაინის გუნდში.

> არ გამოიყენო არსად border სტილი არც HTML, არც CSS, არც  JavaScript (არ ვგულისხმობ border-radius,  border-*, ან სხვა)

 ---

 ამ დოკუმენტში დეტალურადაა გაწერილი HTML და CSS კლასები, რომლებიც უნდა წაიშალოს ან შეიცვალოს, რათა დიზაინი იყოს იდეალურ ჰარმონიაში მომხმარებელთან.

---

## 🗑️ CSS-ში წასაშლელი კლასები

- .bg-gradient-to-br
- .border-*
- .border
- .rounded-*

> წაშალე ამ კლასების გამოყენება მთელ პროექტში.

---

## ✏️ CSS-ში შესაცვლელი მნიშვნელობები

- გახადე Likes ღილაკების ფონი უფერული (`transparent`)

---

## 🗑️ HTML-ში წასაშლელი კლასები

- bg-gradient-to-br
- border-*
- bg-white
- border-b
- rounded-*

---

Please create the UI styling to ensure a consistent look and feel across both our Laravel Blade and React applications. The goal is to unify the design based on the following rules.
Here are the specific changes required:

1. **Global Border Radius:** Change the `border-radius` for all interactive and container elements (like buttons, inputs, cards, forms, etc.) to a consistent `5px`. This should be a global rule.
2. **Dark Mode Form Inputs:** The current input fields are not visible on dark/black backgrounds.  A suggested background color is `#292929`, but ensure the text inside is clearly legible (e.g., use a light-colored text).
3. **Universal Consistency:** These styles must apply everywhere, for both public-facing pages (visitors) and internal admin panels. The experience should be seamless.
   To achieve this, please consider creating a core CSS file or updating the base Tailwind CSS configuration (`tailwind.config.js`) that can be shared or replicated across both the Laravel and React projects. This will be the single source of truth for our design system.

Do the **entire Laravel application** design based on STYLE_GUIDE.md:

- Remove: `.bg`, `.bg-*`, `.border`, `.border-*`, `.rounded` , `.rounded-*` (everywhere: Blade, React, CSS)
- Use `background-color: #090909` as global background
- Use gold color palette (#c28840, #d4a743, #f0c75e) for headings, buttons, accents
- Fonts: `extrasquareCaps`, `NovaSquare`, serif
- Make all buttons rounded consistently
- Make "Likes" buttons background transparent
- Apply to login page, register page, admin pages, user dashboards, error pages, etc.

Apply these design changes to every page and URL in the Laravel application,
Do NOT rely only on routes/web.php.
Instead, search and update *all* Blade files, React components and CSS files in the whole project,
so that every page and every URL looks consistent.

Check and update:

- all Blade templates in resources/views (and subfolders)
- all React components if any
- all CSS files
- even if the URL or view is not explicitly listed in routes/web.php

Remove:

- .border and .border-* classes
- .rounded and .rounded-* classes
- .bg-*, .bg

Apply:

- Mostly background color #090909
- gold color palette and fonts as in STYLE_GUIDE.md

> ⚠️ Make sure to apply these changes everywhere — even in dynamically generated pages, admin modules, or package views.
> ⚠️ Do NOT change or remove other unrelated styles.

> ⚠️ Change every part of the app (Blade, React, CSS). Do **not** remove or change other unrelated styles or functionalities.
> 🧹 Clear cache and rebuild after changes:
> php artisan view:clear && php artisan cache:clear && npm run dev

Make sure to clear caches and rebuild assets if necessary:
php artisan view:clear
php artisan cache:clear
npm run dev

## ⚠️ დამატებითი მითითებები (ტექნიკური სიტყვების გარეშე)

- დატოვე ყველა სხვა სტილი და ფერები უცვლელად, თუ ისინი ამ დოკუმენტში არ არის ნახსენები.
- არ შეცვალო კომპონენტების ფუნქციონალი.
