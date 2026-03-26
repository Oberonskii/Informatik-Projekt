<!-- Dateizweck: Tab-Template "calendar" fuer die Dashboard-Ansicht. -->
<!-- Hinweis: Enthält primär HTML-Struktur und UI-Bausteine fuer diesen Bereich. -->
                <!-- Calendar Detail View -->
                <div id="calendar" class="view-content" style="display: none;">
                    <div class="content-header">
                        <h1>📆 <?php echo htmlspecialchars(t('calendar.title')); ?></h1>
                        <p><?php echo htmlspecialchars(t('calendar.subtitle')); ?></p>
                    </div>

                    <!-- Monatsübersicht -->
                    <div class="widget">
                        <div class="widget-header">
                            <div class="widget-title"><?php echo htmlspecialchars(t('calendar.month_overview')); ?></div>
                            <button class="btn-primary" onclick="openCalendarQuickAddModal()"><?php echo htmlspecialchars(t('calendar.add_event')); ?></button>
                        </div>
                        <div id="calendarLayout" style="padding:1rem;">
                            <div id="calendarContainer">
                                <div id="calendarControls" style="margin-bottom:0.5rem; display:flex; justify-content:space-between; align-items:center;">
                                    <button class="btn-secondary" onclick="prevMonth()">◀</button>
                                    <span id="calendarMonthLabel" style="font-weight:600"></span>
                                    <button class="btn-secondary" onclick="nextMonth()">▶</button>
                                </div>
                                <div id="calendarSelectedHint"><?php echo htmlspecialchars(t('calendar.selected_day')); ?></div>
                                <table id="calendarGrid" style="width:100%; border-collapse:collapse;"></table>
                            </div>
                            <div id="calendarDayEvents" style="padding:1rem;">
                                <h3 id="calendarDayLabel"></h3>
                                <div id="calendarEventList"></div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-overlay" id="calendarQuickAddModal">
                        <div class="modal-box" style="max-width: 500px;">
                            <h2><?php echo htmlspecialchars(t('calendar.modal_add_title')); ?></h2>
                            <div class="modal-section">
                                <h3><?php echo htmlspecialchars(t('calendar.selected_day_label')); ?></h3>
                                <p id="calendarQuickAddDateLabel" style="color:var(--color-text-secondary); margin-bottom:0.4rem;">-</p>
                                <div class="calendar-title-input-row">
                                    <span id="calendarTitleColorPreview" class="calendar-title-preview-dot" onclick="document.getElementById('quickEventColor').click()" title="<?php echo htmlspecialchars(t('calendar.color_pick')); ?>"></span>
                                    <input type="text" id="quickEventTitle" placeholder="<?php echo htmlspecialchars(t('calendar.title_placeholder')); ?>" autocomplete="off" oninput="updateCalendarTitleSuggestions(this.value)" onfocus="updateCalendarTitleSuggestions(this.value)">
                                    <input type="color" id="quickEventColor" value="#0d6efd" onchange="updateCalendarTitlePreview(document.getElementById('quickEventTitle')?.value || '')">
                                </div>
                                <div id="calendarTitleSuggestions" class="calendar-title-suggestions"></div>
                                <input type="text" id="quickEventDesc" placeholder="<?php echo htmlspecialchars(t('calendar.desc_placeholder')); ?>">
                                <label class="calendar-repeat-label"><?php echo htmlspecialchars(t('calendar.time_optional')); ?></label>
                                <div class="calendar-time-row">
                                    <div class="calendar-time-field">
                                        <label class="calendar-repeat-label" for="quickEventStartTime"><?php echo htmlspecialchars(t('calendar.from')); ?></label>
                                        <input type="time" id="quickEventStartTime">
                                    </div>
                                    <div class="calendar-time-field">
                                        <label class="calendar-repeat-label" for="quickEventEndTime"><?php echo htmlspecialchars(t('calendar.to')); ?></label>
                                        <input type="time" id="quickEventEndTime">
                                    </div>
                                </div>
                                <label class="calendar-repeat-label" for="quickEventRecurrence"><?php echo htmlspecialchars(t('calendar.repeat')); ?></label>
                                <select id="quickEventRecurrence" class="calendar-repeat-select">
                                    <option value="none"><?php echo htmlspecialchars(t('calendar.repeat_none')); ?></option>
                                    <option value="weekly"><?php echo htmlspecialchars(t('calendar.repeat_weekly')); ?></option>
                                    <option value="monthly"><?php echo htmlspecialchars(t('calendar.repeat_monthly')); ?></option>
                                    <option value="yearly"><?php echo htmlspecialchars(t('calendar.repeat_yearly')); ?></option>
                                </select>
                            </div>
                            <div class="modal-footer">
                                <button class="modal-btn modal-btn-primary" onclick="submitCalendarQuickAdd()"><?php echo htmlspecialchars(t('common.save')); ?></button>
                                <button class="modal-btn modal-btn-close" onclick="closeCalendarQuickAddModal()"><?php echo htmlspecialchars(t('common.cancel')); ?></button>
                            </div>
                        </div>
                    </div>

                    <div class="modal-overlay" id="calendarDeleteModal">
                        <div class="modal-box" style="max-width: 460px;">
                            <h2><?php echo htmlspecialchars(t('calendar.delete_recurring_title')); ?></h2>
                            <div class="modal-section">
                                <p id="calendarDeleteModalText" style="color:var(--color-text-secondary); margin:0;"><?php echo htmlspecialchars(t('calendar.delete_recurring_text')); ?></p>
                            </div>
                            <div class="modal-footer">
                                <button class="modal-btn modal-btn-primary" onclick="confirmCalendarDelete('occurrence')"><?php echo htmlspecialchars(t('calendar.delete_occurrence')); ?></button>
                                <button class="modal-btn modal-btn-danger" onclick="confirmCalendarDelete('series')"><?php echo htmlspecialchars(t('calendar.delete_series')); ?></button>
                                <button class="modal-btn modal-btn-close" onclick="closeCalendarDeleteModal()"><?php echo htmlspecialchars(t('common.cancel')); ?></button>
                            </div>
                        </div>
                    </div>
                </div>