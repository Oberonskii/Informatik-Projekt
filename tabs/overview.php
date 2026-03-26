<!-- Dateizweck: Tab-Template "overview" fuer die Dashboard-Ansicht. -->
<!-- Hinweis: Enthält primär HTML-Struktur und UI-Bausteine fuer diesen Bereich. -->
                <!-- Overview Content -->
                <div id="overview" class="view-content">
                    <div class="content-header">
                        <div class="content-header-toolbar">
                            <div>
                                <h1><?php echo htmlspecialchars(t('overview.welcome')); ?></h1>
                                <p><?php echo htmlspecialchars(t('overview.subtitle')); ?></p>
                            </div>
                            <button class="btn-secondary overview-customize-btn" id="overviewCustomizeToggle" type="button"><?php echo htmlspecialchars(t('overview.customize')); ?></button>
                        </div>
                    </div>

                    <div class="dashboard-grid" id="overviewWidgetGrid">
                        <!-- Stundenplan Widget -->
                        <div class="widget" data-widget-id="timetable">
                            <div class="widget-header">
                                <div class="widget-title">
                                    <span class="widget-icon">📅</span>
                                    <?php echo htmlspecialchars(t('overview.timetable_today')); ?>
                                </div>
                                <button class="widget-action" data-view="timetable">→</button>
                            </div>
                            <div class="timetable" id="overviewTimetable">
                                <p style="color:var(--color-text-muted);text-align:center;padding:0.5rem;"><?php echo htmlspecialchars(t('common.loading')); ?></p>
                            </div>
                        </div>

                        <!-- Noten Widget -->
                        <div class="widget" data-widget-id="grades">
                            <div class="widget-header">
                                <div class="widget-title">
                                    <span class="widget-icon">📝</span>
                                    <?php echo htmlspecialchars(t('overview.current_grades')); ?>
                                </div>
                                <button class="widget-action" data-view="grades">→</button>
                            </div>
                            <div class="grades-list" id="overviewGrades">
                                <p style="color:var(--color-text-muted);text-align:center;padding:0.5rem;"><?php echo htmlspecialchars(t('common.loading')); ?></p>
                            </div>
                        </div>

                        <!-- To-Do Widget -->
                        <div class="widget" data-widget-id="todos">
                            <div class="widget-header">
                                <div class="widget-title">
                                    <span class="widget-icon">✅</span>
                                    <?php echo htmlspecialchars(t('overview.open_tasks')); ?>
                                </div>
                                <button class="widget-action" data-view="todos">→</button>
                            </div>
                            <div class="todo-list" id="overviewTodos">
                                <p style="color:var(--color-text-muted);text-align:center;padding:0.5rem;"><?php echo htmlspecialchars(t('common.loading')); ?></p>
                            </div>
                        </div>

                        <!-- Hausaufgaben Widget -->
                        <div class="widget" data-widget-id="homeworks">
                            <div class="widget-header">
                                <div class="widget-title">
                                    <span class="widget-icon">📚</span>
                                    <?php echo htmlspecialchars(t('overview.homework')); ?>
                                </div>
                                <button class="widget-action" data-view="homework">→</button>
                            </div>
                            <div class="todo-list" id="overviewHomeworks">
                                <p style="color:var(--color-text-muted);text-align:center;padding:0.5rem;"><?php echo htmlspecialchars(t('common.loading')); ?></p>
                            </div>
                        </div>

                        <!-- Klassenarbeiten Widget -->
                        <div class="widget" data-widget-id="exams">
                            <div class="widget-header">
                                <div class="widget-title">
                                    <span class="widget-icon">📝</span>
                                    <?php echo htmlspecialchars(t('overview.exams')); ?>
                                </div>
                                <button class="widget-action" data-view="exams">→</button>
                            </div>
                            <div class="grades-list" id="overviewExams">
                                <p style="color:var(--color-text-muted);text-align:center;padding:0.5rem;"><?php echo htmlspecialchars(t('common.loading')); ?></p>
                            </div>
                        </div>

                        <!-- Kalender Widget -->
                        <div class="widget" data-widget-id="calendar">
                            <div class="widget-header">
                                <div class="widget-title">
                                    <span class="widget-icon">📆</span>
                                    <?php echo htmlspecialchars(t('overview.events')); ?>
                                </div>
                                <button class="widget-action" data-view="calendar">→</button>
                            </div>
                            <div class="grades-list" id="overviewCalendar">
                                <p style="color:var(--color-text-muted);text-align:center;padding:0.5rem;"><?php echo htmlspecialchars(t('common.loading')); ?></p>
                            </div>
                        </div>


                        <!-- Karteikarten Widget -->
                        <div class="widget" data-widget-id="flashcards">
                            <div class="widget-header">
                                <div class="widget-title">
                                    <span class="widget-icon">🎴</span>
                                    <?php echo htmlspecialchars(t('overview.flashcards')); ?>
                                </div>
                                <button class="widget-action" data-view="flashcards">→</button>
                            </div>
                            <div style="display:flex; align-items:center; justify-content:space-between; gap:0.75rem; margin-bottom:0.75rem; font-size:0.82rem; color:var(--color-text-secondary);">
                                <span id="overviewFlashcardDeckName"><?php echo htmlspecialchars(t('common.loading')); ?></span>
                                <span id="cardCounter"></span>
                            </div>
                            <div class="flashcard" id="flashcard" onclick="flipCard('flashcard')">
                                <div class="flashcard-inner" id="flashcardInner">
                                    <div class="flashcard-front">
                                        <p><strong><?php echo htmlspecialchars(t('overview.flashcard_question_label')); ?></strong> <?php echo htmlspecialchars(t('overview.flashcard_question_1')); ?></p>
                                    </div>
                                    <div class="flashcard-back">
                                        <p><?php echo htmlspecialchars(t('overview.flashcard_answer_1')); ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="flashcard-nav" id="overviewFlashcardNav">
                                <button class="flashcard-btn" onclick="previousCard(); event.stopPropagation();"><?php echo htmlspecialchars(t('overview.previous')); ?></button>
                                <button class="flashcard-btn" onclick="nextCard(); event.stopPropagation();"><?php echo htmlspecialchars(t('overview.next')); ?></button>
                            </div>
                        </div>

                        <!-- Dateien Widget -->
                        <div class="widget" data-widget-id="files">
                            <div class="widget-header">
                                <div class="widget-title">
                                    <span class="widget-icon">📁</span>
                                    <?php echo htmlspecialchars(t('overview.files')); ?>
                                </div>
                                <button class="widget-action" data-view="files">→</button>
                            </div>
                            <div class="files-list" id="overviewFiles">
                                <p style="color:var(--color-text-muted);text-align:center;padding:0.5rem;"><?php echo htmlspecialchars(t('common.loading')); ?></p>
                            </div>
                        </div>

                        <!-- Admin Nachrichten Widget -->
                        <div class="widget" data-widget-id="messages">
                            <div class="widget-header">
                                <div class="widget-title">
                                    <span class="widget-icon">💬</span>
                                    <span><?php echo htmlspecialchars(t('overview.admin_messages')); ?></span>
                                </div>
                                <button class="widget-action" data-view="admin-messages">→</button>
                            </div>
                            <div class="widget-body">
                                <div class="messages-list" id="overviewMessages">
                                    <p style="color:var(--color-text-muted);text-align:center;padding:0.5rem;"><?php echo htmlspecialchars(t('common.loading')); ?></p>
                                </div>
                            </div>
                        </div>

                        <?php if (!empty($is_admin)): ?>
                        <!-- Admin Panel Widget -->
                        <div class="widget" data-widget-id="admin-panel">
                            <div class="widget-header">
                                <div class="widget-title">
                                    <span class="widget-icon">⚙️</span>
                                    <?php echo htmlspecialchars(t('overview.admin_stats')); ?>
                                </div>
                                <button class="widget-action" data-view="admin">→</button>
                            </div>
                            <div class="admin-stats">
                                <div class="stat-card">
                                    <div class="stat-value" id="overviewAdminMau">-</div>
                                    <div class="stat-label">MAU</div>
                                </div>
                                <div class="stat-card">
                                    <div class="stat-value" id="overviewAdminUsers">-</div>
                                    <div class="stat-label"><?php echo htmlspecialchars(t('overview.stat_users')); ?></div>
                                </div>
                                <div class="stat-card">
                                    <div class="stat-value" id="overviewAdminTodos">-</div>
                                    <div class="stat-label"><?php echo htmlspecialchars(t('overview.stat_todo_rate')); ?></div>
                                </div>
                                <div class="stat-card">
                                    <div class="stat-value" id="overviewAdminFailures">-</div>
                                    <div class="stat-label"><?php echo htmlspecialchars(t('overview.stat_failed_logins')); ?></div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
