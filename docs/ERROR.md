# Acumen Craft – ვიზუალური სტილის გზამკვლევი

ეს დოკუმენტი განსაზღვრავს ACUMEN CRAFT-ის ბრენდინგის, ფერების, ტიპოგრაფიისა და UI ელემენტების სტანდარტებს.

---

## 🎨 ფერების პალიტრა

| სახელი               | Hex კოდი   | გამოყენება                                     |
|----------------------|------------|------------------------------------------------|
| შავი ფონი            | #090909    | ძირითადი ფონი                                   |
| მუქი ოქრო            | #c28840    | მთავარი სათაურები (H1-H2), აქცენტირებული ღილაკები|
| სტაფილოსფერი ოქრო    | #d4a743    | საშუალო ზომის სათაურები (H3-H4), ჰოვერ-ეფექტები |
| ღია ოქრო             | #f0c75e    | მცირე ზომის სათაურები (H5-H6), მცირე აქცენტები   |
| თეთრი                | #f9f9f9    | ძირითადი ტექსტი მუქ ფონზე                       |
 
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

| ელემენტი   | შრიფტის ზომა | ფერი             | შრიფტი (რეკომენდაცია)            |
|------------|-------------|------------------|----------------------------------|
| H1         | 36px        | მუქი ოქრო        | extrasquareCaps,NovaSquare, serif |
| H2         | 28px        | მუქი ოქრო        |extrasquareCaps,NovaSquare, serif  |
| H3         | 24px        | სტაფილოსფერი ოქრო| extrasquareCaps,NovaSquare, serif  |
| H4         | 20px        | სტაფილოსფერი ოქრო| extrasquareCaps,NovaSquare, serif  (Sans-serif) |
| H5         | 17px        | ღია ოქრო         | extrasquareCaps,NovaSquare, serif  (Sans-serif)    |
| H6         | 14px        | ღია ოქრო         | extrasquareCaps,NovaSquare, serif  (Sans-serif)    |
| პარაგრაფი  | 13px        | თეთრი            | extrasquare,NovaSquare, serif  (Sans-serif)     |

- **Fonts [ფონტები]**  
(../public/fonts/bpg_extrasquare_2009.ttf)
(../public/fonts/bpg_extrasquare_mtavruli_2009.ttf)
(../public/fonts/NovaSquare-Regular.ttf)
---

## 🟨 ღილაკები და UI ელემენტები

| ელემენტი          | ფონი                        | ტექსტის ფერი         | შენიშვნა                                         |
|-------------------|----------------------------|----------------------|--------------------------------------------------|
| მთავარი ღილაკი     | მუქი ოქრო (#c28840)        | შავი                 | ჰოვერზე ფერი იცვლება სტაფილოსფერ ოქროზე (#d4a743)|
| მეორადი ღილაკი     | გამჭვირვალე                 | მუქი ოქრო (#f0c75e)   | აქვს ღია ოქროს ფერი box-shadow                    |
| ტექსტური ღილაკი    | არ აქვს                    | ღია ოქრო (#f0c75e)   | ჰოვერზე ხაზი ესმება ქვემოდან                     |

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
>არ გამოიყენო არსად border სტილი არც HTML, არც CSS, არც  JavaScript (არ ვგულისხმობ border-radius,  border-*, ან სხვა)

 ---
