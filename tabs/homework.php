<!-- Dateizweck: Tab-Template "homework" fuer die Dashboard-Ansicht. -->
<!-- Hinweis: Enthält primär HTML-Struktur und UI-Bausteine fuer diesen Bereich. -->
                <!-- Hausaufgaben Detail View -->
                <div id="homework" class="view-content" style="display: none;">
                    <div class="content-header">
                        <h1>📚 <?php echo htmlspecialchars(t('homework.title')); ?></h1>
                        <p><?php echo htmlspecialchars(t('homework.subtitle')); ?></p>
                    </div>

                    <div class="widget">
                        <div class="widget-header">
                            <div class="widget-title"><?php echo htmlspecialchars(t('homework.title')); ?></div>
                        </div>
                        <div id="homeworkGrid"></div>
                    </div>
                </div>