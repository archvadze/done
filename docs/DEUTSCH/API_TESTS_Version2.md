# Acumen Craft – API Testleitfaden

_Letztes Update: 22.07.2025_

---

## Übersicht

Dieses Dokument beschreibt die grundlegenden Prinzipien, Methoden und Tools für das Testen der RESTful API von Acumen Craft.  
Es richtet sich an Backend-Entwickler:innen, QA-Ingenieur:innen und externe Integratoren.

---

## 1. Testarten

- **Unit-Tests:** Prüfen einzelne Endpunkte oder Controller-Methoden (z.B. Laravel Feature/Unit Tests)
- **Integrationstests:** Prüfen das Zusammenspiel mehrerer Komponenten (z.B. User-Flow von Registrierung bis Upload)
- **End-to-End-Tests (E2E):** Komplettes System z.B. mit Cypress, Postman-Flows oder Dredd
- **Sicherheitstests:** Authentifizierung, Rechte, Input-Validierung, Rate-Limiting

---

## 2. Test-Tools

- **PHPUnit** (Laravel-intern)
- **Postman** (Sammlungen & automatisierte Tests)
- **Insomnia** (API-Entwicklung & Tests)
- **Cypress** (E2E-Tests)
- **Swagger/OpenAPI** (API-Definition, Mocking & Testautomatisierung)
- **Dredd** (Validierung gegen swagger.yaml)

---

## 3. Testumgebung

- Lokale Umgebung oder dedizierte Test-Instanz
- `.env.testing` für spezifische Test-Configs
- Testdatenbank (wird nach jedem Test zurückgesetzt)
- Separate API-Keys & Dummy-Benutzer

---

## 4. Beispiel: API-Test mit PHPUnit

```php
public function test_api_login_route_works()
{
    $user = User::factory()->create([
        'password' => bcrypt('secretpass'),
    ]);
    $response = $this->postJson('/api/login', [
        'email' => $user->email,
        'password' => 'secretpass'
    ]);
    $response->assertStatus(200)
             ->assertJsonStructure(['token']);
}
```

---

## 5. Beispiel: Test in Postman

1. Lade die [Postman Collection](./postman_collection.json) herunter.
2. Setze Umgebungsvariablen für Host & Token.
3. Führe Test-Suite aus und prüfe die automatischen Checks (Assertions).

---

## 6. Automatisierte Tests

- Alle wichtigen Endpunkte sind durch automatisierte Tests abgedeckt.
- GitHub Actions: Bei jedem Push/PR werden Tests automatisch ausgeführt.
- Reports unter `/storage/test-reports` oder im CI/CD-Log.

---

## 7. Häufig getestete Szenarien

- **Auth:** Registrierung, Login, Refresh, Logout, Passwort zurücksetzen
- **Werk-Upload:** Verschiedene Medien, Metadaten, Rechte
- **Bewertungen:** Abgabe, Update, Löschung, Doppelteinreichung verhindern
- **NFT:** Minting, Übertragung, Blockchain-Validierung
- **Zahlungen:** Spenden, Verkäufe, Fehlerfälle
- **Rate-Limiting:** API-Schutz gegen Missbrauch

---

## 8. Sicherheitstests

- Überprüfung von Rollen & Berechtigungen (RBAC)
- Fuzzing / unsichere Eingaben
- Cross-Site-Scripting (XSS), SQL-Injection, CSRF
- Token-Handling & Ablauf

---

## 9. Guidelines

- Schreibe für jeden neuen Endpunkt einen Unit- und Integrationstest.
- Tests müssen reproduzierbar und unabhängig sein.
- Nutze Factories/Seeder für Testdaten.
- Dokumentiere spezielle Testfälle im Pull Request.

---

## 10. Weitere Ressourcen

- [swagger.yaml](./swagger.yaml) – API-Dokumentation & Beispielanfragen
- [DATABASE.md](./DATABASE.md) – Datenbankschema
- [DEPLOY.md](./DEPLOY.md) – Deployment/CI/CD-Anleitung

---

_Fragen/Bugs? Bitte öffne ein Issue oder kontaktiere das Backend-Team._