# Acumen Craft – Webhooks Dokumentation

_Letztes Update: 22.07.2025_

---

## Übersicht

Mit Webhooks können externe Dienste oder eigene Anwendungen automatisierte Benachrichtigungen über Ereignisse auf Acumen Craft erhalten (z.B. neuer Upload, NFT-Minting, Zahlungseingang).

---

## 1. Was sind Webhooks?

Webhooks sind HTTP-Callbacks, die bei bestimmten Events von Acumen Craft an eine hinterlegte URL gesendet werden.  
Sie ermöglichen Echtzeit-Integration mit externen Tools, Automatisierungen oder Benachrichtigungssystemen.

---

## 2. Einrichtung eines Webhooks

1. **Im Dashboard anmelden**
2. Navigiere zu: Einstellungen → Integrationen → Webhooks
3. Klicke auf "Webhook hinzufügen"
4. Gib die Ziel-URL und optionale Secret/Token für die Signaturprüfung ein
5. Wähle die Events aus, die du abonnieren möchtest
6. Speichern

---

## 3. Unterstützte Events

| Event                | Beschreibung                              |
|----------------------|-------------------------------------------|
| artwork.created      | Neues Werk hochgeladen                    |
| artwork.updated      | Werk aktualisiert                         |
| artwork.deleted      | Werk gelöscht                             |
| payment.completed    | Zahlung eingegangen                       |
| nft.minted           | NFT erfolgreich gemintet                  |
| nft.transferred      | NFT wurde transferiert                    |
| user.registered      | Neuer Nutzer registriert                  |
| comment.added        | Kommentar hinzugefügt                     |
| rating.submitted     | Neue Bewertung abgegeben                  |

---

## 4. Beispiel: Webhook Payload

```json
{
  "event": "artwork.created",
  "timestamp": "2025-07-22T20:00:00Z",
  "data": {
    "artwork_id": 12345,
    "title": "Galaxy Dreams",
    "user_id": 6789,
    "username": "artlover",
    "type": "image",
    "url": "https://acumencraft.com/artworks/12345"
  }
}
```

---

## 5. Sicherheit

- **Secret/Token:** Für jede Webhook-URL kann ein Secret gesetzt werden.  
  Acumen Craft signiert alle Requests via `X-AcumenCraft-Signature` (HMAC-SHA256).
- **HTTPS:** Verwende ausschließlich HTTPS-URLs für Webhooks.
- **Retries:** Bei Fehlerstatus (HTTP 4xx/5xx) erfolgt bis zu 5x ein erneuter Zustellversuch mit exponentiellem Backoff.
- **IP-Whitelist** (optional): Webhook-Requests kommen von festen IP-Ranges (siehe Einstellungen).

---

## 6. Empfang und Verarbeitung

- Prüfe die Kopfzeile `X-AcumenCraft-Signature` auf Gültigkeit (HMAC-SHA256 mit deinem Secret)
- Antworte innerhalb von 5 Sekunden mit HTTP 2xx (sonst Retry)
- Verarbeite nur bekannte Events und prüfe das Payload-Schema

---

## 7. Testen & Debugging

- Über das Dashboard können Test-Events an deine Webhook-URL gesendet werden
- Logs zu letzten Zustellungen und Fehlern findest du unter Einstellungen → Integrationen → Webhooks → Logs

---

## 8. Fehlerbehandlung

- Nach 5 fehlgeschlagenen Zustellversuchen wird der Webhook deaktiviert und du erhältst eine Benachrichtigung
- Du kannst den Webhook jederzeit reaktivieren oder Logs einsehen

---

## 9. Beispiel: HMAC Signature-Check (PHP)

```php
$payload = file_get_contents('php://input');
$signature = $_SERVER['HTTP_X_ACUMENCRAFT_SIGNATURE'];
$secret = 'DEIN_WEBHOOK_SECRET';

$hash = hash_hmac('sha256', $payload, $secret);
if (hash_equals($hash, $signature)) {
    // Gültig, Event verarbeiten
}
```

---

## 10. Weitere Hinweise

- Dokumentation der Event-Schemata: [swagger.yaml](./swagger.yaml)
- Webhook-Limits: max. 10 URLs pro Account, 100 Events/min pro URL
- Support: support@acumencraft.com

---

_Fragen oder Featurewünsche? Melde dich bei uns!_