# 🛠️ Full Design Refactor Request – Acumen Craft

Update the **entire Laravel application** design based on STYLE_GUIDE.md:

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
- background color #090909
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


