<!-- Dateizweck: Tab-Template "grades" fuer die Dashboard-Ansicht. -->
<!-- Hinweis: Enthält primär HTML-Struktur und UI-Bausteine fuer diesen Bereich. -->
                <!-- Noten Detail View -->
                <div id="grades" class="view-content" style="display: none;">
                    <div class="content-header">
                        <h1>📝 Notenübersicht</h1>
                        <p>Alle deine Fächer und Noten </p>
                    </div>
                    <div class="widget">
                        <div class="widget-header">
                            <div class="widget-title">Note hinzufügen</div>
                            <div style="display:flex; gap:0.5rem; flex-wrap:wrap;">
                                <button class="btn-secondary" onclick="exportGradesCSV()">⬇️ CSV</button>
                                <button class="btn-secondary" onclick="exportGradesPDF()">🧾 PDF</button>
                            </div>
                        </div>
                        <div class="input-group">
                            <select id="gradeSubject" data-subject-dropdown placeholder="Fach wählen...">
                                <option value="">-- Fach wählen --</option>
                            </select>
                            <input type="number" id="gradeValue" placeholder="Punkte (0–15)" min="0" max="15" step="1">
                            <input type="number" id="gradeWeight" placeholder="Gewichtung (z.B. 1)" min="1" step="1" value="1">
                            <button class="btn-primary" onclick="addGrade()">Hinzufügen</button>
                        </div>
                        <div class="grades-list" id="gradesList" style="margin-top: 1.5rem;">
                            <p style="color:var(--color-text-muted);text-align:center;padding:1rem;">Wird geladen...</p>
                        </div>
                    </div>
                </div>
