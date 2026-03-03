# LearnHub – Änderungsprotokoll

## Übersicht der Änderungen (Stand: 03.03.2026)

---

## 1. `login.php` – Bugfix + Rolle in Session speichern

### Gelöscht
```php
// Doppelter curl_exec auf bereits geschlossenem Handle
$response = curl_exec($ch);   // Zeile 28 – erster Aufruf (korrekt)
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);              // Zeile 30 – Handle vorzeitig geschlossen

$response = curl_exec($ch);   // Zeile 33 – FEHLER: Handle bereits geschlossen → gibt false zurück

if ($response === false) {    // War immer true → Login schlug immer fehl
    $error_message = "Server nicht erreichbar.";
} else {
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);  // ungültiger Handle
    $responseData = json_decode($response, true);
    if ($http_code == 200) {
        $_SESSION['user_id'] = $responseData['user_id'];
        $_SESSION['username'] = $responseData['username'];
        // Rolle wurde NICHT gespeichert
        ...
    }
}
curl_close($ch);  // zweites close auf ungültigem Handle
```

### Hinzugefügt
```php
// Sauberer, einmaliger curl-Aufruf
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($response === false) {
    $error_message = "Server nicht erreichbar.";
} else {
    $responseData = json_decode($response, true);
    if ($http_code == 200) {
        $_SESSION['user_id']  = $responseData['user_id'];
        $_SESSION['username'] = $responseData['username'];
        $_SESSION['role']     = $responseData['role'] ?? 'user';  // NEU: Rolle speichern
        header('Location: current_dashboard.php');
        exit();
    } else {
        $error_message = $responseData['detail'] ?? "Anmeldung fehlgeschlagen.";
    }
}
```

**Warum:** Der ursprüngliche Code führte `curl_exec()` auf einem bereits geschlossenen Handle aus, was immer `false` zurückgab. Außerdem wurde die Benutzerrolle nicht in der Session gespeichert, was für das Admin-Panel benötigt wird.

---

## 2. `current_dashboard.php` – Komplette Neuentwicklung (1350 → 1527 Zeilen)

Das gesamte Dashboard wurde neu geschrieben. Alle statischen/hardcodierten Daten wurden durch echte API-Aufrufe ersetzt.

---

### 2.1 PHP-Header

#### Gelöscht
```php
// Nur user_id in Session, keine Rolle
$user_id = $_SESSION['user_id'];
// Keine Username/Rolle-Variable
// Nur Datei-Fetch via curl
```

#### Hinzugefügt
```php
$user_id  = $_SESSION['user_id'];
$username = $_SESSION['username'] ?? 'User';
$role     = $_SESSION['role'] ?? 'user';

// Fallback: Rolle via API holen wenn nicht in Session (alte Sessions)
if (!isset($_SESSION['role'])) {
    $ch = curl_init("http://127.0.0.1:8000/admin/users");
    // ... filtert nach user_id um Rolle zu ermitteln
}
```

---

### 2.2 CSS – Neue Klassen

Folgende CSS-Klassen wurden neu hinzugefügt (bestehende Variablen und Styles wurden beibehalten):

#### Gelöscht
- `.widget-action` – ersetzt durch einheitliche `.btn`-Klassen
- `.timetable` / `.timetable-day` / `.day-name` / `.day-classes` / `.class-badge` – statische Stundenplan-Styles
- `.grade-item` / `.grade-subject` / `.grade-value` – rudimentäre Noten-Styles
- `.flashcard` / `.flashcard-inner` / `.flashcard-front` / `.flashcard-back` / `.flashcard-nav` / `.flashcard-btn` – altes Karteikarten-Widget
- `.admin-stats` / `.stat-card` – rudimentäre Admin-Statistik-Styles
- `.input-group` – ersetzt durch `.form-row` / `.form-group`
- `.btn-primary` (alte Version ohne Flexbox)

#### Hinzugefügt
```css
/* Einheitliches Button-System */
.btn { ... }
.btn-primary / .btn-success / .btn-danger / .btn-secondary { ... }
.btn-sm / .btn-icon { ... }

/* Formular-System */
.form-row / .form-group / .form-input { ... }

/* Toast-Benachrichtigungen */
#toast { position:fixed; bottom:1.5rem; right:1.5rem; ... }
#toast.success / .error / .info { ... }

/* Modales System */
.modal-overlay / .modal / .modal-header / .modal-body / .modal-close { ... }
.modal-section / .danger-zone { ... }

/* Stundenplan */
.day-tabs / .day-tab / .day-tab.active / .day-tab.today { ... }
.period-row / .period-num / .period-time / .period-subject / .period-body { ... }
.hw-list / .hw-item / .hw-item.done / .hw-checkbox { ... }
.hw-add-form { ... }
.timetable-edit-grid / .grid-input { ... }
.periods-editor / .period-edit-row / .period-label-sm { ... }

/* Noten */
.subject-card / .subject-card-header / .subject-card-body { ... }
.grade-entry / .grade-type-badge.written / .oral { ... }
.grade-val / .grade-desc / .grade-date { ... }
.subject-avg.good / .warn / .bad { ... }
.weight-editor / .weight-row / .weight-val { ... }
.system-toggle / .system-btn.active { ... }

/* To-Dos */
.filter-tabs / .filter-tab.active { ... }
.todo-item / .todo-item.done / .todo-checkbox / .todo-title { ... }
.todo-subject-tag / .todo-due { ... }
.priority-badge.high / .medium / .low { ... }

/* Karteikarten */
.decks-grid / .deck-card / .deck-card-top / .deck-card-body { ... }
.deck-card-name / .deck-card-count / .deck-card-actions { ... }
.card-list-item / .card-list-front { ... }
.add-form-box.open { ... }

/* Lernmodus */
.learning-overlay.open { position:fixed; inset:0; z-index:500; ... }
.learning-card / .learning-card.flipped / .learning-card-inner { ... }
.learning-face.front / .back { backface-visibility:hidden; ... }
.learning-progress-bar / .learning-progress-fill { ... }
.learning-actions { ... }

/* Admin */
.stats-grid / .stat-card / .stat-value / .stat-label { ... }
.admin-table-wrap / .admin-table / .role-badge.admin / .user { ... }

/* Übersicht-Widgets */
.overview-timetable-item / .overview-time / .overview-subject { ... }
.ov-grade-item / .ov-grade-val { ... }
.mini-card / .mini-face.front / .back { ... }

/* Utility */
.empty-state / .back-btn / .add-form-toggle / .divider { ... }
```

---

### 2.3 HTML – Sidebar

#### Gelöscht
- Statische `<span>` für Benutzernamen
- Kein Abmelden-Button
- `<button class="account-btn">Account</button>` ohne Funktion

#### Hinzugefügt
```html
<!-- PHP-Variable für Username -->
<button class="account-btn" onclick="openAccount()">👤 <?php echo htmlspecialchars($username); ?></button>
<button class="account-btn" onclick="logout()" style="color:var(--color-danger)...">🚪 Abmelden</button>

<!-- Admin-Link nur bei role==='admin' -->
<?php if ($role === 'admin'): ?>
<a class="nav-item" data-view="admin">⚙️ Admin Panel</a>
<?php endif; ?>
```

---

### 2.4 HTML – Views

#### Gelöscht (alles statisch)
- Stundenplan-View: Statische `<div class="timetable-day">` mit hardcodierten Fächern (Mathematik, Informatik, Physik…)
- Noten-View: Statische `<div class="grade-item">` mit 13P, 12P, 10P…
- To-Do-View: Statische Beispiel-Todos im JavaScript-Array
- Karteikarten-View: Statisches Automaten-Beispiel (5 hardcodierte Karten)
- Admin-View: Statische Zahlen (156 User, 24 Kurse, 89%, 4.8…)

#### Hinzugefügt

**Stundenplan:**
```html
<!-- View-Modus: Tages-Tabs + dynamische Period-Rows mit Hausaufgaben -->
<div class="day-tabs" id="dayTabs"></div>
<div class="widget" id="tt-day-content"></div>

<!-- Edit-Modus: Stundenzeiten-Editor + Wochenraster-Tabelle -->
<div id="tt-edit-mode">
  <div class="periods-editor"><div id="periods-list"></div></div>
  <div class="timetable-edit-grid"><div id="edit-grid-wrap"></div></div>
</div>
```

**Noten:**
```html
<!-- System-Toggle Punkte/Noten + dynamische Fachkarten -->
<div class="system-toggle" id="gradeSystemToggle">
  <button class="system-btn" id="sysPoints">Punkte (0–15)</button>
  <button class="system-btn" id="sysGrades">Noten (1–6)</button>
</div>
<div id="grades-list"></div>
```

**To-Dos:**
```html
<!-- Filter-Tabs + Add-Form + dynamische Liste -->
<div class="filter-tabs">
  <button class="filter-tab" data-filter="all">Alle</button>
  <button class="filter-tab" data-filter="open">Offen</button>
  <button class="filter-tab" data-filter="done">Erledigt</button>
</div>
<div id="todos-list"></div>
```

**Karteikarten:**
```html
<!-- 3 Sub-Views: Stapel-Übersicht, Karten in Stapel, Lernmodus -->
<div id="fc-decks-view"><div class="decks-grid" id="decks-grid"></div></div>
<div id="fc-cards-view"><div id="cards-list"></div></div>
<div class="learning-overlay" id="learning-overlay">
  <!-- Vollbild-Lernmodus mit Fortschrittsbalken -->
</div>
```

**Admin:**
```html
<!-- Echte Statistiken + Benutzertabelle -->
<div class="stats-grid" id="admin-stats"></div>
<table class="admin-table">
  <tbody id="admin-users-tbody"></tbody>
</table>
```

**Account-Modal (NEU):**
```html
<div class="modal-overlay" id="accountModal">
  <!-- Username ändern -->
  <!-- Passwort ändern -->
  <!-- Notensystem Toggle -->
  <!-- Account löschen (Danger Zone) -->
</div>
```

**Note-hinzufügen-Modal (NEU):**
```html
<div class="modal-overlay" id="addGradeModal">
  <!-- Fach, Wert, Typ (Schriftlich/Mündlich), Beschreibung -->
</div>
```

**Stapel-erstellen-Modal (NEU):**
```html
<div class="modal-overlay" id="addDeckModal">
  <!-- Name, Farbauswahl (8 Farben) -->
</div>
```

---

### 2.5 JavaScript – Komplett neu

#### Gelöscht
```javascript
// Statische Todos-Array
const todos = [
  { text: 'Mathematik Hausaufgaben...', priority: 'high', completed: false },
  ...
];

// Statische Karteikarten
const flashcards = [
  { question: 'Was ist ein Automat?', answer: 'Ein abstraktes...' },
  ...
];

// addGrade() – nur DOM-Manipulation, kein API-Aufruf
function addGrade() { ... gradesList.appendChild(gradeItem); }

// toggleTodo() – nur CSS-Toggle, kein API-Aufruf
function toggleTodo(element) { element.classList.toggle('checked'); }
```

#### Hinzugefügt

**Basis-Infrastruktur:**
```javascript
const API = 'http://127.0.0.1:8000';
const USER_ID = '<?php echo $user_id; ?>';
const USER_ROLE = '<?php echo $role; ?>';

// Universeller API-Helper mit async/await
async function api(method, path, body = null) { ... }

// Toast-Benachrichtigung (success/error/info)
function toast(msg, type = 'success') { ... }
```

**Stundenplan-Modul:**
```javascript
// Zustandsvariablen
let ttEntries = [], ttPeriods = [], ttHomework = [], ttCurrentDay, ttEditMode = false;

loadTimetable()     // Lädt ttEntries + ttPeriods + ttHomework parallel
renderDayTabs()     // Rendert Mo-Fr Tabs, today hervorgehoben
renderDayView(day)  // Rendert Stunden mit Zeiten und Hausaufgaben
addHw()             // POST /homework/{user_id}
toggleHw()          // PUT /homework/{user_id}/{id}/toggle
deleteHw()          // DELETE /homework/{user_id}/{id}
toggleEditMode()    // Wechsel zwischen Ansicht/Bearbeitung
renderPeriodsEditor() // Stundenzeiten-Liste mit Inputs
addPeriodRow()      // PUT /periods/{user_id} mit nächster Nummer
savePeriod(num)     // PUT /periods/{user_id} mit neuen Zeiten
deletePeriod(num)   // DELETE /periods/{user_id}/{num}
renderEditGrid()    // Wochenraster-Tabelle mit Input-Feldern
saveGridCell(inp)   // POST/PUT/DELETE je nach vorherigem Wert
```

**Noten-Modul:**
```javascript
let allGrades = [], subjectSettings = [];
let gradeSystem = 'points';  // global

loadGradeSettings()    // GET /settings/{user_id} + /settings/{user_id}/subjects
loadGrades()           // GET /grades/{user_id}
setGradeSystem(sys)    // PUT /settings/{user_id} → aktualisiert UI + rendert neu
gradeColorClass(val)   // good/warn/bad je nach System
gradeDisplay(val)      // "13 P" oder "2" je nach System
calcWeightedAvg()      // gewichteter Durchschnitt (schriftlich/mündlich)
renderGrades()         // Fachkarten mit Noten + Gewichtungs-Slider
updateWeightSlider()   // Live-Slider mit 600ms Debounce
saveSubjectWeight()    // PUT /settings/{user_id}/subjects
openAddGradeModal()    // Modal mit passendem min/max je System
submitAddGrade()       // POST /grades/{user_id}
deleteGrade(id)        // DELETE /grades/{user_id}/{id}
```

**Todos-Modul:**
```javascript
let allTodos = [], todoFilter = 'all';

loadTodos()       // GET /todos/{user_id}
renderTodos()     // Gefiltert (all/open/done) + sortiert nach Priorität
addTodo()         // POST /todos/{user_id}
toggleTodo(id)    // PUT /todos/{user_id}/{id}/toggle
deleteTodo(id)    // DELETE /todos/{user_id}/{id}
```

**Karteikarten-Modul:**
```javascript
let allDecks = [], currentDeckCards = [], currentDeckId = null;
let learningCards = [], learningIdx = 0, learnedCount = 0;

loadDecks()           // GET /flashcard-decks/{user_id}
renderDecks()         // Kachel-Grid mit Farben
openDeck()            // GET /flashcards/{user_id}?deck_id=...
renderCardsList()     // Liste der Karten in einem Stapel
addCard()             // POST /flashcards/{user_id}
deleteCard(id)        // DELETE /flashcards/{user_id}/{id}
deleteDeck(id)        // DELETE /flashcard-decks/{user_id}/{id}
startLearning()       // Mischung der Karten, Vollbild-Overlay
showLearningCard()    // Karte anzeigen + Fortschritt updaten
flipLearningCard()    // 3D-Flip Animation
markKnown()           // PUT /flashcards/{user_id}/{id}/known {known:true} + weiter
markUnknown()         // PUT /flashcards/{user_id}/{id}/known {known:false} + weiter
endLearning()         // Overlay schließen
submitAddDeck()       // POST /flashcard-decks/{user_id}
selectDeckColor(c)    // Farbauswahl im Modal
```

**Admin-Modul:**
```javascript
loadAdminStats()      // GET /admin/stats → 6 Statistik-Kacheln
loadAdminUsers()      // GET /admin/users → Tabelle mit Aktionen
adminChangeRole()     // PUT /admin/users/{id}/role
adminDeleteUser()     // DELETE /admin/users/{id}
```

**Account-Modal:**
```javascript
openAccount()         // Modal öffnen + GET /settings laden
changeUsername()      // PUT /auth/change-username/{user_id}
changePassword()      // PUT /auth/change-password/{user_id}
setGradeSystemModal() // Ruft setGradeSystem() auf
deleteAccount()       // DELETE /auth/delete-account/{user_id} → redirect login
```

**Übersicht (loadOverview):**
```javascript
// Lädt parallel:
// - Heutiger Stundenplan aus API
// - Letzte 4 Noten
// - Erste 4 offene Todos
// - Zufällige Karteikarte aus erstem Stapel
// - Admin-Stats (nur wenn role==='admin')
```

**Hilfsfunktionen:**
```javascript
escHtml(s)   // XSS-Schutz für DOM-Ausgabe
escAttr(s)   // XSS-Schutz für HTML-Attribute
fmtDate(s)   // ISO-Datum → deutsches Format (TT.MM.JJ)
priLabel(p)  // 'high' → 'HOCH' usw.
```

---

## Zusammenfassung

| Datei | Änderungen |
|-------|-----------|
| `login.php` | Bugfix doppelter curl_exec, `$_SESSION['role']` hinzugefügt |
| `current_dashboard.php` | Komplette Neuentwicklung: 1350 → 1527 Zeilen, alle Features implementiert |
| `backend.py` | v2.0.0: Neue Tabellen und Routen (vorheriger Commit) |

### Implementierte Features
1. **Stundenplan** – Tages-Tabs, Perioden mit Zeiten, Hausaufgaben inline, Bearbeitungsmodus mit Wochenraster und Zeiten-Editor
2. **Noten** – Fachkarten, gewichtete Durchschnitte, Schriftlich/Mündlich, Punkte/Noten-Toggle
3. **To-Dos** – Vollständiges CRUD, Filter (Alle/Offen/Erledigt), Prioritäten
4. **Karteikarten** – Stapel-Verwaltung, Karteneditor, Lernmodus mit Fortschritt
5. **Admin-Panel** – Echte Statistiken, Benutzerverwaltung mit Rollen
6. **Account-Einstellungen** – Username, Passwort, Notensystem, Account-Löschung
