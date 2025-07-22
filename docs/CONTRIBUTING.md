# Contributing to Acumen Craft

Thank you for your interest in contributing to Acumen Craft!  
We welcome code, documentation, design, and feedback contributions from everyone.

---

## Table of Contents

1. [Code of Conduct](#code-of-conduct)
2. [How to Contribute](#how-to-contribute)
3. [Development Setup](#development-setup)
4. [Pull Request Process](#pull-request-process)
5. [Issue Reporting](#issue-reporting)
6. [Coding Standards](#coding-standards)
7. [Translations & Accessibility](#translations--accessibility)
8. [Security Policy](#security-policy)
9. [License](#license)
10. [Community](#community)

---

## Code of Conduct

By participating in this project, you agree to abide by our [Code of Conduct](./docs/CODE_OF_CONDUCT.md).  
All interactions must be respectful and inclusive.

---

## How to Contribute

- **Report Bugs:** Use [GitHub Issues](./docs/issues) and provide clear reproduction steps.
- **Suggest Features:** Open an issue with the `enhancement` label and detailed description.
- **Submit Pull Requests:** See [Pull Request Process](#pull-request-process).
- **UI/UX/Docs:** Propose changes via PR or suggest improvements in issues.
- **Translations:** Help us localize the app (see [Translations](#translations--accessibility) section).

---

## Development Setup

1. **Clone the repo:**  
   `git clone https://github.com/acumencraft/done.git`
2. **Install dependencies:**  
   - **Backend:** PHP 8.2+, Composer, MariaDB/MySQL  
   - **Frontend:** Node.js (18+), npm/yarn  
   - **Mobile:** Flutter 3+ or React Native 0.74+
3. **Environment:**  
   - Copy `.env.example` to `.env` and update DB/API keys as needed.
   - Set up database: `php artisan migrate --seed` (see [db.sql](./docs/db.sql))
   - For mobile, see [MOBILE_GUIDE.md](./docs/MOBILE_GUIDE.md)
4. **Run:**  
   - Backend: `php artisan serve`
   - Frontend: `npm run dev`
   - Mobile: `flutter run` or `npx react-native run-android`

---

## Pull Request Process

- Fork the repository and create your branch from `main`.
- Write clear, atomic commits with descriptive messages.
- Add/modify tests as appropriate.
- Ensure all lint checks and tests pass (`php artisan test`, `npm test`).
- Reference related issues (e.g., `Fixes #42`).
- Submit a pull request with a clear summary of your changes.
- One of the maintainers will review and provide feedback.
- After approval, your branch will be merged and deployed as appropriate.

---

## Issue Reporting

- **Bug:** Include steps to reproduce, expected/actual behavior, logs, screenshots if possible.
- **Feature:** Describe use case, expected impact, relevant mockups or references.
- **Security:** See [SECURITY.md](./docs/SECURITY.md) and email security@acumencraft.com for confidential reports.

---

## Coding Standards

- **Backend:** PSR-12, Laravel best practices, docblocks
- **Frontend:** ESLint (Airbnb), Prettier, React/TS or Flutter conventions
- **Tests:** PHPUnit, Pest (backend); Jest/RTL (frontend); Flutter/React Native test frameworks
- **Commits:** Conventional Commits encouraged (fix:, feat:, docs:, chore:, refactor:, test:, etc.)

---

## Translations & Accessibility

- All UI text should use translation files (`en.json`, `ka.json`, etc.).
- Accessibility (a11y): Use semantic HTML, ARIA, voiceover tools.
- Contribute new translations via PR in `/resources/lang` or `/src/locales`.

---

## Security Policy

See [SECURITY.md](./docs/SECURITY.md) for responsible vulnerability disclosure and security practices.

---

## License

By contributing, you agree your work will be released under the [project license](./docs/LICENSE.md).

---

## Community

- [Discussions](./docs/discussions)
- [UI/Branding](./docs/branding.pdf)
- [Roadmap](./docs/Roadmap.md)

For questions, join our community chat or email contact@acumencraft.com.

---

Thank you for making Acumen Craft better!
