# Acumen Craft – Unified Design Style Guide

This document defines the **design standards** for the entire Acumen Craft application (Laravel Blade + React).

All changes must apply to:

- Laravel Blade templates (`resources/views/`)
- React components (if any)
- CSS/Tailwind config and all shared styles
- Admin panels, user dashboards, public pages, error pages

---

## 🎨 Color Palette

| Name            | Hex       | Usage                                    |
| --------------- | --------- | ---------------------------------------- |
| Dark Background | `#090909` | Global background                        |
| Deep Gold       | `#c28840` | Main headings (H1–H2), primary buttons   |
| Carrot Gold     | `#d4a743` | Subheadings (H3–H4), hover effects       |
| Light Gold      | `#f0c75e` | Smaller headings (H5–H6), accent details |
| White           | `#f9f9f9` | Default text on dark backgrounds         |

---

## ✍️ Typography

| Element   | Size | Color       | Font Stack                             |
| --------- | ---- | ----------- | -------------------------------------- |
| H1        | 36px | Deep Gold   | `extrasquareCaps`, `NovaSquare`, serif |
| H2        | 28px | Deep Gold   | `extrasquareCaps`, `NovaSquare`, serif |
| H3        | 24px | Carrot Gold | `extrasquareCaps`, `NovaSquare`, serif |
| H4        | 20px | Carrot Gold | `extrasquareCaps`, serif               |
| H5        | 17px | Light Gold  | `NovaSquare`, serif                    |
| H6        | 14px | Light Gold  | `NovaSquare`, serif                    |
| Paragraph | 13px | White       | `extrasquare`, serif                   |

Fonts to be loaded from:

/public/fonts/bpg_extrasquare_2009.ttf  
/public/fonts/bpg_extrasquare_mtavruli_2009.ttf  
/public/fonts/NovaSquare-Regular.ttf

---

## 🟨 Buttons & UI Elements

| Element          | Background  | Text Color | Notes                      |
| ---------------- | ----------- | ---------- | -------------------------- |
| Primary Button   | `#c28840`   | Black      | Hover: `#d4a743`           |
| Secondary Button | Transparent | Light Gold | With light gold box-shadow |
| Text Button      | None        | Light Gold | Underlined on hover        |
| Likes Button     | Transparent | Inherit    | No background color        |

---

## 🧱 Components Style Rules

- **Global `border-radius`:** All buttons, inputs, cards, images, and form elements must have `border-radius: 4px`.
- **Form Fields (Dark Mode):**
  - `background-color: #292929`
  - `color: #f9f9f9`
  - Use padding and focus styling for accessibility
- **Cards/Modals:**
  - `background-color: #090909`
  - Gold colored box-shadow if needed

---

## 🗑️ Remove the following classes (globally):

From HTML/CSS/JS/Blade/React:

- `.bg`, `.bg-*`
- `.border`, `.border-*`
- `.rounded`, `.rounded-*`
- `bg-white`, `border-b`, `bg-gradient-to-br`

---

## ✅ Apply the following

- Global background: `#090909`
- Font families: `extrasquareCaps`, `NovaSquare`, `serif`
- Gold color palette
- Unified border-radius: `4px`
- Use compact button sizing (`btn-sm` style if Bootstrap used)

---

## ⚠️ Important Instructions

- Do **not** alter any styles **unless explicitly described above**.
- Do **not** remove unrelated components, structure, or functionality.
- Do **not** overwrite existing layout paddings or spacing unless specified.
- All changes must be **applied globally and consistently**, including:
  - Admin views
  - Auth pages (login/register/reset)
  - Error pages
  - Public pages
- **Do not rely only on `routes/web.php` to discover pages** — scan all templates/components.
- Consider updating Tailwind config (`tailwind.config.js`) or using a shared `base.css` to maintain consistency.

---

## 🧹 After changes:

```bash
php artisan view:clear
php artisan cache:clear
npm run dev
```

## 📌 Summary

This is the **single source of truth** for UI and style consistency across Acumen Craft. Apply all changes accordingly, and consult `/docs` or design team for component-specific specs.
