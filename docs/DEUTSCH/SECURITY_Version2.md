# Sicherheitsrichtlinie von Acumen Craft

_Letztes Update: 22.07.2025_

---

## 1. Unser Sicherheitsansatz

Der Schutz der Daten und der Plattformintegrität von Acumen Craft hat höchste Priorität. Wir setzen moderne Technologien und Best Practices ein, um Nutzer:innen, Inhalte und Systeme vor Missbrauch und Angriffen zu schützen.

---

## 2. Sichere Entwicklung

- **Sichere Programmierung:** Verwendung von Framework-Best-Practices, Input-Validierung und Output-Encoding
- **Regelmäßige Code-Reviews** und statische Code-Analyse
- **Automatisierte Tests** für Sicherheitskritische Funktionen (z.B. Authentifizierung, Zahlungsprozesse)
- **Abhängigkeiten:** Laufendes Monitoring mittels Dependabot und Snyk

---

## 3. Authentifizierung & Autorisierung

- **Passwort-Hashing** (bcrypt/argon2)
- **Zwei-Faktor-Authentifizierung** (2FA) optional für alle Accounts
- **OAuth 2.0 / SSO** Unterstützung für Social-Logins
- **JWT**- und rollenbasierte Zugriffskontrolle (RBAC)

---

## 4. Datenschutz & Verschlüsselung

- **HTTPS-Verschlüsselung** (TLS 1.3) für alle Verbindungen
- **Verschlüsselte Speicherung** sensibler Daten und Geheimnisse
- **Keine Speicherung von Klartext-Passwörtern**
- **DSGVO/CCPA-Konformität** und Privacy-by-Design

---

## 5. Infrastruktur- & Betriebs-Sicherheit

- **Firewall** und restriktive Netzwerkzugriffe
- **Regelmäßige Backups** der Datenbank und Dateien
- **Überwachung** (Monitoring) & Alarme auf verdächtige Aktivitäten
- **Isolierte Umgebungen** für Entwicklung, Test und Produktion (Staging/Prod)
- **Automatische Sicherheitsupdates** für Betriebssystem und Abhängigkeiten

---

## 6. Schwachstellenmanagement

- **Bug Bounty:** Sicherheitslücken können anonym an security@acumencraft.com gemeldet werden
- **Schnelle Reaktionszeiten** und zeitnahe Behebung kritischer Schwachstellen
- **Transparente Kommunikation** bei sicherheitsrelevanten Vorfällen

---

## 7. Nutzerhinweise

- Wähle starke, einzigartige Passwörter und aktiviere 2FA
- Teile keine sensiblen Daten öffentlich oder mit Unbefugten
- Melde verdächtige Aktivitäten sofort an das Sicherheitsteam

---

## 8. Kontakt

Sicherheitsprobleme oder Schwachstellen?  
Bitte kontaktiere uns: **security@acumencraft.com**  
Alle Meldungen werden vertraulich und schnellstmöglich behandelt.

---

_Gemeinsam sorgen wir für eine sichere Acumen Craft Plattform!_