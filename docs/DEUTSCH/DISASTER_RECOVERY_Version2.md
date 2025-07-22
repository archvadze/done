# Acumen Craft – Notfall- und Wiederherstellungsplan (Disaster Recovery)

_Letztes Update: 22.07.2025_

---

## 1. Ziel

Dieser Plan stellt sicher, dass Acumen Craft bei schwerwiegenden Ausfällen, Angriffen oder Katastrophen rasch wiederhergestellt werden kann und Ausfallzeiten sowie Datenverluste minimiert werden.

---

## 2. Bedrohungsszenarien

- Hardware-Ausfall (Server, Storage)
- Datenbank-Korruption oder -Verlust
- Ransomware- oder Malware-Angriff
- Menschlicher Fehler (z.B. versehentliches Löschen)
- Cloud-/Infrastruktur-Ausfall (z.B. AWS, Hetzner)
- Naturkatastrophen (Brand, Überschwemmung)
- Sicherheitsvorfälle (z.B. Datenleck, Angriff auf API)

---

## 3. Backup-Strategie

- **Automatische tägliche Backups** aller Datenbanken (mind. 7 Tage Aufbewahrung)
- **Stündliche Snapshots** kritischer Dateien und Medien (24h Rollback möglich)
- **Backups an mehreren Standorten** (min. 1 extern, verschlüsselt)
- **Regelmäßige Testwiederherstellungen** (mind. 1x/Quartal)
- **Versionskontrolle** für Quellcode und Konfigurationsdateien (GitHub)

---

## 4. Wiederherstellungsschritte

1. **Vorfall identifizieren**  
   - Alarm durch Monitoring, Nutzer:innen oder automatisierte Checks
   - Sofortige Information des Incident Response Teams

2. **Schadensanalyse & Kommunikation**  
   - Ausmaß und Ursache klären  
   - Interne & ggf. externe Kommunikation (Statuspage, E-Mail)

3. **Systeme isolieren**  
   - Zugriff sperren, Netzwerk isolieren, ggf. Server vom Netz trennen

4. **Datenwiederherstellung**  
   - Jüngstes sauberes Backup einspielen  
   - Integrität und Konsistenz der Daten prüfen

5. **Systeme & Dienste reaktivieren**  
   - Schrittweise Wiederinbetriebnahme  
   - Überwachung auf erneute Auffälligkeiten

6. **Nachbereitung**  
   - Ursache dokumentieren, Lessons Learned  
   - Prozesse und Sicherheitsmaßnahmen verbessern

---

## 5. Verantwortlichkeiten

- **Incident Response Team:** Leitung, Kommunikation, Koordination
- **DevOps/IT:** Backup, Wiederherstellung, Infrastruktur
- **Datenschutzbeauftragte:** Kommunikation bei Datenleck, Einhaltung DSGVO

---

## 6. Kommunikation im Notfall

- Interner Notfall-Chat (z.B. Slack, Signal)
- Statusupdates auf status.acumencraft.com
- E-Mail-Benachrichtigung an Nutzer:innen bei längeren Ausfällen
- Presse/Öffentlichkeit nur nach Abstimmung mit Geschäftsführung

---

## 7. Wichtige Kontakte

- Notfall-E-Mail: **dr@acumencraft.com**
- Hosting/Cloud-Support: siehe interne Kontaktliste
- Datenschutz: **privacy@acumencraft.com**
- Sicherheit: **security@acumencraft.com**

---

## 8. Test & Aktualisierung

- Der Plan wird jährlich geprüft und nach jedem größeren Vorfall aktualisiert.
- Testläufe mindestens einmal pro Jahr dokumentieren.

---

_Bei Fragen oder Verbesserungsvorschlägen zu diesem Plan bitte an dr@acumencraft.com wenden._