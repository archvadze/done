# Acumen Craft – Third-Party Notices

_Last updated: 2025-07-22_

This project includes dependencies and assets developed by third parties.  
Their respective licenses and notices are summarized below. For details, see the LICENSE or NOTICE file in each dependency’s source.

---

## 1. Backend Dependencies

- **Laravel** (https://laravel.com)  
  License: MIT  
  Copyright (c) Taylor Otwell

- **Composer Packages**  
  All Composer dependencies are open source, primarily MIT/Apache/BSD/ISC licenses.  
  See `composer.lock` for a full list.

---

## 2. Frontend Dependencies

- **React** (https://react.dev)  
  License: MIT  
  Copyright (c) Facebook

- **Tailwind CSS** (https://tailwindcss.com)  
  License: MIT

- **Other npm Packages:**  
  Most npm dependencies are MIT, Apache-2.0, BSD, or ISC licensed.  
  See `package-lock.json` or `yarn.lock` for the full list.

---

## 3. Mobile Dependencies

- **Flutter** (https://flutter.dev)  
  License: BSD-3-Clause

- **React Native** (https://reactnative.dev)  
  License: MIT

---

## 4. Icons, Fonts, and Assets

- **Font Awesome Free** (https://fontawesome.com)  
  License: CC BY 4.0 (font), SIL OFL 1.1 (fonts), MIT (code)

- **Google Fonts** (Inter, Roboto, etc.)  
  License: SIL OFL 1.1

- **Heroicons** (https://heroicons.com)  
  License: MIT

- **SVG & PNG assets**  
  Some UI mockup icons may originate from open source icon sets; see `/assets/` subfolders for LICENSE/NOTICE files.

---

## 5. AI and Cloud Services

- **OpenAI API** (https://openai.com)  
  License: Commercial (see OpenAI TOS)

- **AWS, GCP, Firebase**  
  License: See respective provider terms

---

## 6. Other Notices

- All trademarks and copyrights are property of their respective owners.
- If you believe a third-party dependency is omitted from this list, please open an issue or contact legal@acumencraft.com.

---

## 7. How to Update

To update this file:
- Run `composer licenses` for PHP dependencies.
- Run `npm list --depth=0 --json` or `yarn licenses list` for JS dependencies.
- Check `/assets/`, `/fonts/`, and `/icons/` for manual asset attributions.
- Review cloud/API providers’ terms for any updates.

---

Thank you to all open source contributors and third-party developers who make Acumen Craft possible!
