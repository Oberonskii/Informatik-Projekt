# LearnHub

Eine webbasierte Lernplattform für Schüler und Studierende mit Karteikarten, Stundenplan, Notenverwaltung und Dateiorganisation. Ermöglicht effizientes Lernen durch personalisierte Dashboards und kollaborative Funktionen.

***

## Inhaltsübersicht
- Ziel des Projekts
- Anforderungen
- Projektstruktur
- Module & Zuständigkeiten
- Installation & Start
- Arbeitsweise & Regeln
- Projektstatus

***

## 1. Ziel des Projekts

**LearnHub** löst das Chaos bei Lernorganisation: Schüler und Studierende haben ihre Karteikarten, Noten, Hausaufgaben, Stundenpläne und Lernmaterialien stets übersichtlich an einem Ort. Die Plattform zeigt auf dem Dashboard sofort den Lernfortschritt, offene To-Dos und kommende Klausuren – perfekt für den Schul-/Uni-Alltag.

Das Endergebnis ist eine **responsive Web-App** mit Login, Admin-Bereich und Premium-Features, die in Gruppen im Informatik-Leistungskurs JS1 umgesetzt wird.

***

## 2. Anforderungen

### MUSS
- Login/Registrierung mit Benutzername + Passwort
- Dashboard mit Stundenplan-Widget, Lernfortschritt und nächsten To-Dos
- Karteikarten: Erstellen/Lernen (CRUD + Lernmodi)
- Noteneingabe (Punkte 0-15 / Noten 1-6) mit Durchschnittsberechnung
- Datei-Upload mit Fachzuordnung
- Admin-Panel: Nutzerübersicht + Rollenverwaltung

### SOLL
- Responsives Design (Mobile/Tablet/Desktop)
- Dunkel-/Hellmodus
- To-Do-Liste mit Fälligkeitsdaten
- Lernfortschritt-Balken + Badges (Streaks)
- Geteilte Karteikarten-Sets (öffentlich/privat)

### KANN
- Premium-Features (erweiterte Statistiken, mehr Speicher)
- Onboarding-Wizard beim ersten Login
- Export-Funktionen (PDF/CSV)
- Prüfungsmodus mit Timer für Karteikarten

***

## 3. Projektstruktur
```
learnhub/
├── index.html          # Einstiegspunkt
├── css/
│   ├── main.css       # Global Styles
│   ├── dashboard.css  # Widget-Layout
│   └── components.css # Buttons, Cards, etc.
├── js/
│   ├── main.js        # App-Initialisierung + Routing
│   ├── auth.js        # Login/Registrierung
│   ├── dashboard.js   # Widgets + Layout
│   ├── flashcards.js  # Karteikarten-Logik
│   ├── grades.js      # Notenverwaltung
│   ├── timetable.js   # Stundenplan
│   ├── files.js       # Datei-Upload/Verwaltung
│   ├── todos.js       # To-Do-Liste
│   ├── admin.js       # Admin-Funktionen
│   └── storage.js     # localStorage Helper
├── data/              # Demo-Daten (JSON)
│   ├── users.json
│   └── subjects.json
├── assets/            # Icons, Bilder
└── README.md          # Diese Datei
```

***

## 4. Module & Zuständigkeiten

### Modul: Authentifizierung
**Zweck:** Benutzerverwaltung, Login/Logout, Session-Handling  
**Verantwortlich:** [Name1]  
**Dateien:** `js/auth.js`  
**Schnittstellen (öffentliche Funktionen):**  
- `login(username, password) → boolean` (Login prüfen/speichern)  
- `register(userData) → boolean` (Neuen User anlegen)  
- `getCurrentUser() → object|null` (Aktueller Benutzer)  
- `logout() → void` (Session löschen)  
- `isAdmin() → boolean` (Admin-Rechte prüfen)

### Modul: Dashboard
**Zweck:** Hauptübersicht mit Widgets (Stundenplan, To-Dos, Fortschritt)  
**Verantwortlich:** [Name2]  
**Dateien:** `js/dashboard.js`, `css/dashboard.css`  
**Schnittstellen:**  
- `loadDashboard() → void` (Alle Widgets laden)  
- `updateProgress(subjectId) → void` (Fortschrittsbalken aktualisieren)  
- `getNextTodos(count) → array` (Nächste Aufgaben)  

### Modul: Karteikarten
**Zweck:** Erstellen, Lernen, Statistiken von Karteikarten-Sets  
**Verantwortlich:** [Name3]  
**Dateien:** `js/flashcards.js`  
**Schnittstellen:**  
- `createCard(front, back, subjectId) → string` (Karten-ID)  
- `startLearning(setId, mode) → void` (Lernsession starten)  
- `getStats(setId) → object` (Erfolgsquote, Zeit)  
- `markPublic(setId, isPublic) → void` (Set teilen)

### Modul: Noten
**Zweck:** Noteneingabe, Durchschnittsberechnung, Trends  
**Verantwortlich:** [Name1]  
**Dateien:** `js/grades.js`  
**Schnittstellen:**  
- `addGrade(subjectId, value, type) → void` (Note hinzufügen)  
- `getAverage(subjectId) → number` (Durchschnitt)  
- `getAllGrades(subjectId) → array` (Alle Noten)

### Modul: Stundenplan
**Zweck:** Anzeige und Verwaltung des Wochen-/Monatsplans  
**Verantwortlich:** [Name2]  
**Dateien:** `js/timetable.js`  
**Schnittstellen:**  
- `setSchedule(day, slot, subject) → void` (Eintrag setzen)  
- `getTodaySchedule() → array` (Heutige Kurse)  
- `getWeekSchedule() → array` (aktuelle Woche)

### Modul: Dateien
**Zweck:** Upload, Organisation und Suche von Lernmaterial  
**Verantwortlich:** [Name3]  
**Dateien:** `js/files.js`  
**Schnittstellen:**  
- `uploadFile(file, subjectId, tags) → string` (Datei-ID)  
- `getFiles(subjectId) → array` (Fach-Dateien)  
- `searchFiles(query) → array` (Suche)

### Modul: Admin
**Zweck:** Nutzer- und Abo-Verwaltung für Administratoren  
**Verantwortlich:** [Name1]  
**Dateien:** `js/admin.js`  
**Schnittstellen:**  
- `getAllUsers() → array` (Alle Nutzer)  
- `setRole(userId, role) → void` (Rolle ändern)  
- `getUserStats() → object` (Dashboard-Zahlen)

***

## 5. Installation & Start

1. Repository klonen: `git clone [URL]`
2. Browser öffnen: `index.html` direkt öffnen (kein Server nötig)
3. Demo-Login: `admin/admin` oder `user/user`
4. Daten werden in `localStorage` gespeichert

**Entwicklung:** Live-Server empfohlen (`npx live-server`)

***

## 6. Arbeitsweise & Regeln

**Git-Branching:**
```
main     → produktive Version
develop  → Integration
feature/ → neue Features ([Name]-flashcards)
```

**Commits:** `git commit -m "feat: karteikarten lernmodus hinzugefügt"`
**Stand-ups:** Mo/Mi/Fr 15 Min (Discord/Slack)
**Code Review:** Jeder PR muss von 1 anderem genehmigt werden

**Qualitätsregeln:**
- Semikolons überall
- 2 Spaces Einrückung
- ESLint aktivieren
- Konsistente Namenskonventionen (camelCase)

***

## 7. Projektstatus

| Sprint | Features | Status | Verantwortlich |
|--------|----------|--------|---------------|
| Sprint 1 | Login + Dashboard | ⏳ geplant | Name1+Name2 |
| Sprint 2 | Karteikarten + Noten | ⏳ geplant | Name3+Name1 |
| Sprint 3 | Stundenplan + Dateien | ⏳ geplant | Name2+Name3 |
| Sprint 4 | Admin + Polish | ⏳ geplant | Alle |

**Nächster Meilenstein:** Sprint 1 fertig (Ende Woche 2)

***

**🚀 Bereit zum Start!** Ersetzt die [NameX]-Platzhalter mit euren Namen und legt los. Wer übernimmt Sprint 1?
