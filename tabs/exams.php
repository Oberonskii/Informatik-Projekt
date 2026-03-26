<!-- Dateizweck: Tab-Template "exams" fuer die Dashboard-Ansicht. -->
<!-- Hinweis: Enthält primär HTML-Struktur und UI-Bausteine fuer diesen Bereich. -->
                <!-- Klassenarbeiten Detail View -->
                <div id="exams" class="view-content" style="display: none;">
                    <div class="content-header">
                        <h1>📝 <?php echo htmlspecialchars(t('exams.title')); ?></h1>
                        <p><?php echo htmlspecialchars(t('exams.subtitle')); ?></p>
                    </div>
                    <div class="widget">
                        <div class="widget-header">
                            <div class="widget-title"><?php echo htmlspecialchars(t('exams.add_title')); ?></div>
                        </div>
                        <div class="input-group">
                            <select id="examSubject" data-subject-dropdown>
                                <option value=""><?php echo htmlspecialchars(t('exams.subject_choose')); ?></option>
                            </select>
                            <input type="date" id="examDate">
                            <input type="text" id="examTopic" placeholder="<?php echo htmlspecialchars(t('exams.topic_placeholder')); ?>">
                            <select id="examPeriod" onchange="updateExamPeriodRangeOptions()">
                                <option value=""><?php echo htmlspecialchars(t('exams.period_optional')); ?></option>
                                <option value="1">1. Stunde</option>
                                <option value="2">2. Stunde</option>
                                <option value="3">3. Stunde</option>
                                <option value="4">4. Stunde</option>
                                <option value="5">5. Stunde</option>
                                <option value="6">6. Stunde</option>
                                <option value="7">7. Stunde</option>
                                <option value="8">8. Stunde</option>
                                <option value="9">9. Stunde</option>
                                <option value="10">10. Stunde</option>
                            </select>
                            <select id="examPeriodEnd" style="display:none;">
                                <option value=""><?php echo htmlspecialchars(t('exams.period_until')); ?></option>
                            </select>
                            <button class="btn-primary" onclick="addExam()"><?php echo htmlspecialchars(t('common.add')); ?></button>
                        </div>
                        <div class="grades-list" id="examsList" style="margin-top: 1.5rem;"></div>
                    </div>
                </div>
