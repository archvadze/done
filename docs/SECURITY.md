# Acumen Craft – Security Guide

_Last updated: 2025-07-22_

---

## 1. Overview

Acumen Craft is committed to the highest standards of security for user data, content, and financial operations. This document outlines our security posture, user expectations, vulnerability reporting, and operational practices.

---

## 2. User Security Best Practices

- **Passwords:** Use strong, unique passwords for your account. Passwords are securely hashed (bcrypt/argon2).
- **2FA:** Enable two-factor authentication (TOTP or SMS) from your profile/security settings.
- **OAuth:** Social login is supported; we never store your social provider password.
- **Sessions:** Log out from unused devices. Session/device management is available in profile settings.
- **Beware of phishing:** We will never ask for your password via email or chat.

---

## 3. Platform Security Measures

- **Encryption:** All data is encrypted in transit (TLS 1.2+) and at rest.
- **Authentication:** JWT for API, OAuth2 for social, TOTP/SMS for 2FA.
- **Access Control:** Role-based access; admin/moderator functions are strictly protected.
- **Database:** Principle of least privilege, regular backups, audit logging.
- **Payments:** PCI DSS compliant payment providers (Stripe, PayPal); we never store raw card data.
- **Blockchain:** Only public wallet addresses and transaction hashes are stored.
- **Media:** File uploads are virus-scanned and validated before serving.
- **Secrets management:** All secrets/keys are stored securely (vault/env, never in code).

---

## 4. Vulnerability Reporting

We strongly encourage responsible disclosure. If you discover a vulnerability, please follow these steps:

1. **Do not publicly disclose or exploit the vulnerability.**
2. **Contact us securely:**  
   - Email: security@acumencraft.com  
   - PGP public key: [security.asc](./docs/security.asc)
3. Provide detailed information, including:
   - Steps to reproduce
   - Impact assessment
   - Suggested remediation (if possible)
4. We will acknowledge within 48 hours and keep you informed until resolution.
5. Critical issues may be eligible for a bug bounty.

---

## 5. Incident Response

- Security incidents are logged and monitored 24/7.
- In case of a data breach or critical incident, affected users will be notified within 72 hours as per GDPR/CCPA.
- All incidents are post-mortemed and root causes are addressed promptly.

---

## 6. Compliance

- **GDPR & CCPA:** Full compliance for data subject rights, consent, and erasure.
- **PCI DSS:** All payments are processed by certified providers.
- **Age restrictions:** No accounts for users under 16.

---

## 7. Third-Party Integrations

- Only reputable, audited services (cloud, AI, payments, analytics) are used.
- All integrations are regularly reviewed for security updates.
- Webhooks are signed (see [WEBHOOKS.md](./docs/WEBHOOKS.md)).

---

## 8. API & Developer Security

- API keys/secrets are unique per client and must be kept confidential.
- Rate limiting and abuse detection are enforced.
- CORS is restricted to trusted domains.
- API endpoints require JWT + scope/role checks.

---

## 9. Data Deletion & Portability

- You may delete your account and all associated data from your profile.
- Data exports (JSON/CSV) are available on request.
- Deleted data is removed from live systems within 24 hours and from backups within 30 days.

---

## 10. Updates

- Security practices are reviewed quarterly.
- You will be notified of any critical changes via email, in-app, or [SECURITY.md](./docs/SECURITY.md).

---

## 11. Contact

For all security matters, contact:  
**Email:** security@acumencraft.com  
**PGP:** [security.asc](./docs/security.asc)

---

**See also:** [PRIVACY.md](./docs/PRIVACY.md) | [LICENSE.md](./docs/LICENSE.md)
