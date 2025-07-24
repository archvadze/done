# Acumen Craft – Changelog

All notable changes to this project will be documented in this file.  
Follows [Keep a Changelog](https://keepachangelog.com/en/1.0.0/) conventions, date format: YYYY-MM-DD.

---

# Acumen Craft – Changelog

All notable changes to this project will be documented in this file.  
Follows [Keep a Changelog](https://keepachangelog.com/en/1.0.0/) conventions, date format: YYYY-MM-DD.

---

## [Unreleased]

### Added
- **Automatic Language Detection Service**: New `LanguageDetectionService` for auto-detecting content language (Georgian, German, English) and auto-translation to all active languages
- **Simplified Multilingual UI**: Replaced complex Facebook-style language tabs with single form + auto-detection banner for better UX
- **Moderator Role Restrictions**: Added policy restrictions preventing moderators from creating/editing artworks and evaluations (ACQ)

### Changed
- **Multilingual Forms Redesign**: Simplified artwork creation/editing forms from multiple language tabs to single title/description fields with automatic translation
- **ArtworkController**: Updated to use auto-language detection instead of manual language selection
- **Validation Rules**: Removed `content_language` required validation in favor of auto-detection
- **User Experience**: Eliminated overwhelming multiple input fields per language as requested by users

### Fixed
- **File Upload Issue**: Fixed artwork file upload failure caused by missing `content_language` validation after UI simplification
- **Syntax Errors**: Resolved PHP syntax errors in ArtworkController validation arrays

### Security
- **Role-based Access Control**: Enhanced policies to ensure moderators cannot create content or provide evaluations

---

## [0.1.0] – 2025-07-22

### Added
- First public repository release
- Core Laravel backend with modular API
- Authentication (JWT, OAuth, 2FA)
- Artworks CRUD (multi-language, media upload, AI-generated badge)
- Evaluation/ACQ logic (manual & AI)
- Payments (Stripe, PayPal, Crypto)
- NFT minting (Polygon, Ethereum)
- Chat (user, group, AI assistant)
- Community, notification, and helpdesk modules
- Core mobile app features (Flutter/React Native beta)
- UI/UX mockups and branding assets

---

Older versions and internal alpha logs are available upon request.
