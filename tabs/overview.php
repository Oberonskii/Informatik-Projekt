                <!-- Overview Content -->
                <div id="overview" class="view-content">
                    <div class="content-header">
                        <h1>Willkommen zurück! 👋</h1>
                        <p>Hier ist deine Lernübersicht für heute</p>
                    </div>

                    <div class="dashboard-grid">
                        <!-- Stundenplan Widget -->
                        <div class="widget">
                            <div class="widget-header">
                                <div class="widget-title">
                                    <span class="widget-icon">📅</span>
                                    Heute im Stundenplan
                                </div>
                                <button class="widget-action" data-view="timetable">→</button>
                            </div>
                            <div class="timetable">
                                <div class="timetable-day">
                                    <span class="day-name">08:00</span>
                                    <div class="day-classes">
                                        <span class="class-badge">Mathematik</span>
                                    </div>
                                </div>
                                <div class="timetable-day">
                                    <span class="day-name">09:45</span>
                                    <div class="day-classes">
                                        <span class="class-badge">Informatik</span>
                                    </div>
                                </div>
                                <div class="timetable-day">
                                    <span class="day-name">11:30</span>
                                    <div class="day-classes">
                                        <span class="class-badge">Physik</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Noten Widget -->
                        <div class="widget">
                            <div class="widget-header">
                                <div class="widget-title">
                                    <span class="widget-icon">📝</span>
                                    Aktuelle Noten
                                </div>
                                <button class="widget-action" data-view ="grades">→</button>
                            </div>
                            <div class="grades-list">
                                <div class="grade-item">
                                    <span class="grade-subject">Informatik</span>
                                    <span class="grade-value">13 P</span>
                                </div>
                                <div class="grade-item">
                                    <span class="grade-subject">Mathematik</span>
                                    <span class="grade-value">12 P</span>
                                </div>
                                <div class="grade-item">
                                    <span class="grade-subject">Physik</span>
                                    <span class="grade-value warning">10 P</span>
                                </div>
                            </div>
                        </div>

                        <!-- To-Do Widget -->
                        <div class="widget">
                            <div class="widget-header">
                                <div class="widget-title">
                                    <span class="widget-icon">✅</span>
                                    Offene Aufgaben
                                </div>
                                <button class="widget-action" data-view="todos">→</button>
                            </div>
                            <div class="todo-list" id="todoList">
                                <div class="todo-item">
                                    <div class="todo-checkbox" onclick="toggleTodo(this)"></div>
                                    <div class="todo-text">Mathematik Hausaufgaben fertigstellen</div>
                                    <div class="todo-priority priority-high"></div>
                                </div>
                                <div class="todo-item">
                                    <div class="todo-checkbox" onclick="toggleTodo(this)"></div>
                                    <div class="todo-text">Physik Referat vorbereiten</div>
                                    <div class="todo-priority priority-medium"></div>
                                </div>
                                <div class="todo-item">
                                    <div class="todo-checkbox" onclick="toggleTodo(this)"></div>
                                    <div class="todo-text">Karteikarten für Informatik erstellen</div>
                                    <div class="todo-priority priority-low"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Klassenarbeiten Widget -->
                        <div class="widget">
                            <div class="widget-header">
                                <div class="widget-title">
                                    <span class="widget-icon">📝</span>
                                    Klassenarbeiten
                                </div>
                                <button class="widget-action" data-view="exams">→</button>
                            </div>
                            <div class="grades-list">
                                <div class="grade-item">
                                    <div>
                                        <div class="grade-subject">Mathe</div>
                                        <div style="font-size: 0.8rem; color: var(--color-text-secondary);">
                                            Nächste Arbeit: 15.03.2026
                                        </div>
                                    </div>
                                    <div class="grade-value warning">LK</div>
                                </div>
                                <div class="grade-item">
                                    <div>
                                        <div class="grade-subject">Deutsch</div>
                                        <div style="font-size: 0.8rem; color: var(--color-text-secondary);">
                                            Nächste Arbeit: 22.03.2026
                                        </div>
                                    </div>
                                    <div class="grade-value">GK</div>
                                </div>
                            </div>
                        </div>


                        <!-- Karteikarten Widget -->
                        <div class="widget">
                            <div class="widget-header">
                                <div class="widget-title">
                                    <span class="widget-icon">🎴</span>
                                    Karteikarten lernen
                                </div>
                                <button class="widget-action" data-view="flashcards">→</button>
                            </div>
                            <div class="flashcard" id="flashcard" onclick="flipCard('flashcard')">
                                <div class="flashcard-inner" id="flashcardInner">
                                    <div class="flashcard-front">
                                        <p><strong>Frage:</strong> Was ist ein Automat?</p>
                                    </div>
                                    <div class="flashcard-back">
                                        <p>Ein abstraktes Modell eines Rechners mit endlich vielen Zuständen</p>
                                    </div>
                                </div>
                            </div>
                            <div class="flashcard-nav">
                                <button class="flashcard-btn" onclick="previousCard(); event.stopPropagation();">← Zurück</button>
                                <button class="flashcard-btn" onclick="nextCard(); event.stopPropagation();">Weiter →</button>
                            </div>
                        </div>

                        <!-- Dateien Widget -->
                        <div class="widget">
                            <div class="widget-header">
                                <div class="widget-title">
                                    <span class="widget-icon">📁</span>
                                    Letzte Dateien
                                </div>
                                <button class="widget-action" view-data="files">→</button>
                            </div>
                            <div class="files-list">
                                <div class="file-item">
                                    <span class="file-icon">📄</span>
                                    <div class="file-info">
                                        <div class="file-name">Informatik_Klausur_Vorbereitung.pdf</div>
                                        <div class="file-meta">Hochgeladen vor 2 Tagen • 2.3 MB</div>
                                    </div>
                                </div>
                                <div class="file-item">
                                    <span class="file-icon">📊</span>
                                    <div class="file-info">
                                        <div class="file-name">Mathe_Formelsammlung.xlsx</div>
                                        <div class="file-meta">Hochgeladen vor 5 Tagen • 1.1 MB</div>
                                    </div>
                                </div>
                                <div class="file-item">
                                    <span class="file-icon">🎥</span>
                                    <div class="file-info">
                                        <div class="file-name">Physik_Experiment_Video.mp4</div>
                                        <div class="file-meta">Hochgeladen vor 1 Woche • 45 MB</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Admin Nachrichten Widget -->
                        <div class="widget">
                            <div class="widget-header">
                                <div class="widget-title">
                                    <span class="widget-icon">💬</span>
                                    <span>Admin Nachrichten</span>
                                </div>
                                <button class="widget-action" data-view="admin-messages">→</button>
                            </div>
                            <div class="widget-body">
                                <div class="messages-list" id="adminMessagesPreview">
                                    <!-- Hier z.B. die letzten 2–3 Nachrichten anzeigen -->
                                    <div class="message-item">
                                        <div class="message-title">Wichtige Info zur Klassenarbeit</div>
                                        <div class="message-meta">von Admin · vor 1 Tag</div>
                                    </div>
                                    <div class="message-item">
                                        <div class="message-title">Neues Material im Kurs Informatik</div>
                                        <div class="message-meta">von Admin · vor 3 Tagen</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Admin Panel Widget -->
                        <div class="widget">
                            <div class="widget-header">
                                <div class="widget-title">
                                    <span class="widget-icon">⚙️</span>
                                    Admin Statistiken
                                </div>
                                <button class="widget-action" data-view="admin">→</button>
                            </div>
                            <div class="admin-stats">
                                <div class="stat-card">
                                    <div class="stat-value">156</div>
                                    <div class="stat-label">Aktive User</div>
                                </div>
                                <div class="stat-card">
                                    <div class="stat-value">24</div>
                                    <div class="stat-label">Kurse</div>
                                </div>
                                <div class="stat-card">
                                    <div class="stat-value">89%</div>
                                    <div class="stat-label">Abschlussrate</div>
                                </div>
                                <div class="stat-card">
                                    <div class="stat-value">4.8</div>
                                    <div class="stat-label">Ø Bewertung</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
