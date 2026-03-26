<!-- Dateizweck: Tab-Template "timetable" fuer die Dashboard-Ansicht. -->
<!-- Hinweis: Enthält primär HTML-Struktur und UI-Bausteine fuer diesen Bereich. -->
                <!-- Stundenplan Detail View -->
                <div id="timetable" class="view-content" style="display: none;">
                    <div class="content-header">
                        <h1>📅 <?php echo htmlspecialchars(t('timetable.title')); ?></h1>
                        <p><?php echo htmlspecialchars(t('timetable.subtitle')); ?></p>
                    </div>

                    <!-- Stundenplan Widget -->
                    <div class="widget">
                        <div class="widget-header">
                            <div class="widget-title">📋 <?php echo htmlspecialchars(t('timetable.week_plan')); ?></div>
                            <div style="display:flex; gap:0.5rem; flex-wrap:wrap;">
                                <button class="btn-secondary" onclick="exportTimetableCSV()">⬇️ CSV</button>
                                <button class="btn-secondary" onclick="exportTimetablePDF()">🧾 PDF</button>
                                <button id="timetableEditBtn" class="btn-primary" onclick="toggleTimetableEdit()">✏️ <?php echo htmlspecialchars(t('timetable.edit')); ?></button>
                            </div>
                        </div>

                        <!-- Ansichtsmodus -->
                        <div id="timetableViewMode">
                            <div id="timetableGrid"></div>
                        </div>

                        <!-- Bearbeitungsmodus -->
                        <div id="timetableEditMode" style="display:none;">
                            <div class="tt-edit-section">
                                <div class="tt-edit-section-title">⏰ <?php echo htmlspecialchars(t('timetable.period_config')); ?></div>
                                <div id="periodTimesEditor"></div>
                            </div>
                            <div class="tt-edit-section" style="margin-top:2rem;">
                                <div class="tt-edit-section-title">📝 <?php echo htmlspecialchars(t('timetable.subject_room')); ?></div>
                                <div id="timetableEditor" style="overflow-x:auto;"></div>
                            </div>
                            <div style="margin-top:1.5rem; display:flex; gap:0.75rem; flex-wrap:wrap;">
                                <button class="btn-primary" onclick="saveTimetable()">💾 <?php echo htmlspecialchars(t('common.save')); ?></button>
                                <button class="btn-secondary" onclick="cancelTimetableEdit()"><?php echo htmlspecialchars(t('common.cancel')); ?></button>
                            </div>
                        </div>
                    </div>

                    <!-- Hausaufgaben Zusammenfassung -->
                    <div class="widget" style="margin-top:1.5rem;">
                        <div class="widget-header">
                            <div class="widget-title">📚 <?php echo htmlspecialchars(t('homework.title')); ?></div>
                        </div>
                        <div id="homeworkGridTimetable"></div>
                    </div>
                </div>
