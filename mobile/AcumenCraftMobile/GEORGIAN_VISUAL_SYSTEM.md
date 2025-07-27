# 🎨 ქართული ვიზუალური სისტემა - Georgian Visual System

## 📱 ვიზუალის სრული განახლება

### 🇬🇪 ქართული ბრენდული იდენტობა

მთლიანად განახლებული ვიზუალური სისტემა Style Guide-ის მიხედვით:

#### ფერების პალიტრა
- **ქართული ოქროს ტრადიციული გამა**: 9 ვარიანტი (#8B5A2B - #FDF6C4)
- **თანამედროვე ნაცრისფერი**: 10 ვარიანტი (#0A0A0A - #FAFAFA)
- **სემანტიკური ფერები**: წარმატება, გაფრთხილება, შეცდომა, ინფო

#### ტიპოგრაფია
- **BPG ExtraSquare Caps**: სათაურებისთვის
- **BPG ExtraSquare**: ძირითადი ტექსტისთვის
- **Inter**: ლათინური ტექსტისთვის
- **ქართული ზომების სკალა**: 13px-64px

---

## 🧩 ქართული UI კომპონენტები

### KartGhilaki (ღილაკი)
```typescript
<KartGhilaki
  satauri="შემოსვლა"                    // სათაური
  varianti="dziritadi"                 // ვარიანტი: dziritadi | meoardi | teqsti
  zoma="didi"                          // ზომა: patara | sashualod | didi
  mtavaria={false}                     // Loading state
  gamotvlili={false}                   // Disabled state
  daaklaki={() => console.log('clicked')} // Click handler
/>
```

### KartTeqsti (ტექსტი)
```typescript
<KartTeqsti
  shvilebi="ქართული ტექსტი"            // შინაარსი
  varianti="satauri1"                  // ტიპი: satauri1-6, paragraphi, patara
  feri="#C28840"                       // ფერი
  wona="muki"                          // წონა: msubuqi-dzalianMuki
  teqstisGaswporeba="centri"           // გასწორება: marcxena | centri | marjvena
/>
```

### KartKarti (კარტი)
```typescript
<KartKarti
  varianti="naxati"                    // ტიპი: naxati | profili | standartuli
  shvilebi={<View>შინაარსი</View>}      // შვილები
  daaklaki={() => navigate()}          // Optional click handler
/>
```

### KartSheyvana (შეყვანის ველი)
```typescript
<KartSheyvana
  satauri="ემეილის მისამართი"            // ლეიბლი
  value={emaili}                       // მნიშვნელობა
  onChangeText={setEmaili}             // ცვლილების ფუნქცია
  placeholder="მაგ: user@example.com"   // Placeholder
  surathuli="mail-outline"             // Ionicons ხატულა
  parolisMnishvneloba={false}          // პაროლის ველი
  shecsavlia="შეცდომის შეტყობინება"      // Error message
/>
```

---

## 🎨 თემის სისტემა

### KartThemeProvider
```typescript
import { KartThemeProvider, useKartTheme } from './contexts/KartThemeContext';

// App.tsx-ში
<KartThemeProvider>
  <YourApp />
</KartThemeProvider>

// კომპონენტში
const theme = useKartTheme();
const primaryColor = theme.colors.gold[700];
```

### ფერების გამოყენება
```typescript
const theme = useKartTheme();

// ქართული ოქროს ფერები
theme.colors.gold[900]  // მუქი ქართული ოქრო
theme.colors.gold[700]  // მთავარი ბრენდ ოქრო
theme.colors.gold[500]  // ღია ოქრო

// ნაცრისფერი
theme.colors.gray[900]  // ღია შავი (ფონი)
theme.colors.gray[800]  // მუქი (კარტები)
theme.colors.gray[400]  // საშუალო (ტექსტი)

// სემანტიკური
theme.colors.success[500]  // წარმატება
theme.colors.error[500]    // შეცდომა
```

---

## 📱 Screen განახლებები

### LoginScreen - მთლიანად ქართული
- **ქართული ტექსტები**: "შემოსვლა", "ემეილის მისამართი", "პაროლი"
- **ქართული UI კომპონენტები**: KartGhilaki, KartSheyvana, KartKarti
- **ქართული ვალიდაცია**: "ემეილი სავალდებულოა", "პაროლი უნდა იყოს მინიმუმ 6 სიმბოლო"
- **ქართული Error Handling**: "შემოსვლა ვერ მოხერხდა"

### Navigation - ქართული ლეიბლები
- **მთავარი** (Home)
- **აღმოჩენა** (Explore)  
- **ატვირთვა** (Upload)
- **თემები** (Communities)
- **პროფილი** (Profile)

---

## 🎯 ფუნქციური ცვლილებები

### ქართული ცვლადების სახელები
```typescript
// ძველი (ინგლისური)
const [form, setForm] = useState<LoginForm>({
  email: '',
  password: '',
});

// ახალი (ქართული)
const [forma, setForma] = useState<ShmosalisForma>({
  emaili: '',
  paroli: '',
});
```

### ქართული ფუნქციების სახელები
```typescript
// ძველი
const validateForm = () => { ... }
const handleLogin = () => { ... }
const updateForm = () => { ... }

// ახალი
const formisShetvasSesitsema = () => { ... }    // ფორმის შეთვალსის სისწრაფე
const shemoslisGamortvai = () => { ... }        // შემოსვლის გამორთვა
const formisgganaxleba = () => { ... }          // ფორმის განახლება
```

---

## 🎨 სტილების სისტემა

### SPACING
```typescript
const SPACING = {
  xs: 6,     // ქართული ასოებისთვის
  sm: 10,    
  md: 18,    
  lg: 26,    
  xl: 36,    
  xxl: 52,   
};
```

### RADIUS
```typescript
const RADIUS = {
  sm: 6,     // პატარა ელემენტები
  md: 12,    // კარტები, ღილაკები
  lg: 16,    // დიდი კარტები
  xl: 20,    // მოდალები
  full: 999, // წრიული ელემენტები
};
```

### ICON_SIZES
```typescript
const ICON_SIZES = {
  xs: 14,    // პატარა inline ხატულები
  sm: 18,    // სტანდარტული ტექსტის ხატულები
  md: 22,    // ღილაკის ხატულები
  lg: 28,    // ნავიგაციის ხატულები
  xl: 36,    // დიდი feature ხატულები
  xxl: 52,   // hero ხატულები
};
```

---

## 🚀 შემდეგი ნაბიჯები

### 1. დარჩენილი Screens-ების განახლება
- [ ] HomeScreen ქართული კომპონენტებით
- [ ] ExploreScreen ძიებისა და ფილტრების ქართულად
- [ ] UploadScreen ქართული ატვირთვის ინტერფეისით
- [ ] ArtworkDetailScreen ქართული მეტადათებით

### 2. ქართული ანიმაციები
- [ ] Page transitions ქართული მოტივებით
- [ ] Loading states ქართული ტექსტებით
- [ ] Gesture animations ქართული UX პატერნებით

### 3. ქართული Assets
- [ ] ქართული fonts (BPG ExtraSquare) ლოადი
- [ ] ქართული icons და illustrations
- [ ] ქართული splash screen

---

*განახლების თარიღი: 26 ივლისი, 2025*
*სტატუსი: ძირითადი ვიზუალური სისტემა მზადაა*
*მომდევნო: Screen-ების მთლიანი ქართულ დიზაინზე გადაყვანა*
