Acumen Craft – ვიზუალური სტილის გზამკვლევი
ეს დოკუმენტი განსაზღვრავს ACUMEN CRAFT-ის ბრენდინგის, ფერების, ტიპოგრაფიისა და UI ელემენტების სტანდარტებს.

🎨 ფერების პალიტრა
სახელი

Hex კოდი

გამოყენება

შავი ფონი

#000000

ძირითადი ფონი

მუქი ოქრო

#c28840

მთავარი სათაურები (H1-H2), აქცენტირებული ღილაკები

სტაფილოსფერი ოქრო

#d4a743

საშუალო ზომის სათაურები (H3-H4), ჰოვერ-ეფექტები

ღია ოქრო

#f0c75e

მცირე ზომის სათაურები (H5-H6), მცირე აქცენტები

სუფთა ოქრო (სიმბოლო)

#ffd700

ლოგოს სიმბოლო, განსაკუთრებული აქცენტები

თეთრი

#ffffff

ძირითადი ტექსტი მუქ ფონზე

მუქი ნაცრისფერი

#111111

ტექსტი ღია ფონზე (იშვიათად)

✍️ ტიპოგრაფია
ელემენტი

შრიფტის ზომა

ფერი

შრიფტი (რეკომენდაცია)

H1

48px

მუქი ოქრო

Cinzel, Playfair Display (Serif)

H2

36px

მუქი ოქრო

Cinzel, Playfair Display (Serif)

H3

28px

სტაფილოსფერი ოქრო

Cinzel, Playfair Display (Serif)

H4

24px

სტაფილოსფერი ოქრო

Inter, Poppins (Sans-serif)

H5

20px

ღია ოქრო

Inter, Poppins (Sans-serif)

H6

18px

ღია ოქრო

Inter, Poppins (Sans-serif)

პარაგრაფი

16px

თეთრი

Inter, Open Sans (Sans-serif)

🟨 ღილაკები და UI ელემენტები
ელემენტი

ფონი

ტექსტის ფერი

შენიშვნა

მთავარი ღილაკი

მუქი ოქრო (#c28840)

შავი

ჰოვერზე ფერი იცვლება სტაფილოსფერ ოქროზე (#d4a743)

მეორადი ღილაკი

გამჭვირვალე

ღია ოქრო (#f0c75e)

აქვს ოქროსფერი ჩარჩო (border)

ტექსტური ღილაკი

არ აქვს

ღია ოქრო (#f0c75e)

ჰოვერზე ხაზი ესმება ქვემოდან

🧱 კომპონენტების სტილი
ფორმები (Inputs): ფონი: #111111, ჩარჩო: #333333. ფოკუსირებისას ჩარჩო ხდება ღია ოქროსფერი.

ბარათები (Cards): ფონი: #111111 ან #0A0A0A. შეიძლება ჰქონდეს ძალიან მკრთალი ოქროსფერი ჩრდილი ჰოვერზე.

მოდალური ფანჯრები: ფონი: #0A0A0A, 1px სისქის ოქროსფერი ჩარჩოთი.

⚙️ მაგალითი (CSS Snippet)
body {
  background-color: #000;
  color: #fff;
  font-family: 'Inter', sans-serif;
}

h1, h2 {
  color: #c28840;
  font-family: 'Cinzel', serif;
}

h3 {
  color: #d4a743;
  font-family: 'Cinzel', serif;
}

.button-primary {
  background-color: #c28840;
  color: #000;
  padding: 12px 24px;
  border: none;
  border-radius: 8px;
  font-weight: bold;
  transition: background-color 0.3s ease;
}

.button-primary:hover {
  background-color: #d4a743;
}

.logo-symbol {
  fill: #ffd700;
  filter: drop-shadow(0 0 15px rgba(255, 215, 0, 0.3));
}

