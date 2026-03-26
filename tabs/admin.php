<!-- Dateizweck: Tab-Template "admin" fuer die Dashboard-Ansicht. -->
<!-- Hinweis: Enthält primär HTML-Struktur und UI-Bausteine fuer diesen Bereich. -->
                <!-- Admin Detail View -->
                <div id="admin" class="view-content" style="display: none;">
                    <div class="content-header">
                        <h1>⚙️ <?php echo htmlspecialchars(t('admin.title')); ?></h1>
                        <p><?php echo htmlspecialchars(t('admin.subtitle')); ?></p>
                    </div>
                    <div class="dashboard-grid">
                        <div class="widget">
                            <div class="widget-header">
                                <div class="widget-title"><?php echo htmlspecialchars(t('admin.kpis')); ?></div>
                                <button class="btn-primary" onclick="loadAdminPanel()"><?php echo htmlspecialchars(t('common.update')); ?></button>
                            </div>
                            <div class="admin-stats">
                                <div class="stat-card">
                                    <div class="stat-value" id="adminTotalUsers">-</div>
                                    <div class="stat-label"><?php echo htmlspecialchars(t('admin.total_users')); ?></div>
                                </div>
                                <div class="stat-card">
                                    <div class="stat-value" id="adminNewUsers30d">-</div>
                                    <div class="stat-label"><?php echo htmlspecialchars(t('admin.new_users')); ?></div>
                                </div>
                                <div class="stat-card">
                                    <div class="stat-value" id="adminDauWauMau">-</div>
                                    <div class="stat-label"><?php echo htmlspecialchars(t('admin.dau_wau_mau')); ?></div>
                                </div>
                                <div class="stat-card">
                                    <div class="stat-value" id="adminTodoRate">-</div>
                                    <div class="stat-label"><?php echo htmlspecialchars(t('admin.todo_rate')); ?></div>
                                </div>
                                <div class="stat-card">
                                    <div class="stat-value" id="adminAverageGrade">-</div>
                                    <div class="stat-label"><?php echo htmlspecialchars(t('admin.avg_grade')); ?></div>
                                </div>
                                <div class="stat-card">
                                    <div class="stat-value" id="adminFailedLogins7d">-</div>
                                    <div class="stat-label"><?php echo htmlspecialchars(t('admin.failed_logins')); ?></div>
                                </div>
                            </div>
                            <p id="adminGeneratedAt" style="margin-top:1rem;color:var(--color-text-muted);font-size:0.85rem;"><?php echo htmlspecialchars(t('admin.last_updated')); ?></p>
                        </div>
                        <div class="widget">
                            <div class="widget-header">
                                <div class="widget-title"><?php echo htmlspecialchars(t('admin.learning_trends')); ?></div>
                            </div>
                            <div class="input-group" style="flex-direction: column; gap: 1rem;">
                                <div>
                                    <strong><?php echo htmlspecialchars(t('admin.content')); ?></strong>
                                    <p id="adminContentSummary" style="margin-top:0.25rem;color:var(--color-text-secondary);">-</p>
                                </div>
                                <div>
                                    <strong><?php echo htmlspecialchars(t('admin.top_users')); ?></strong>
                                    <div id="adminTopUsers" style="margin-top:0.25rem;color:var(--color-text-secondary);">-</div>
                                </div>
                                <div>
                                    <strong><?php echo htmlspecialchars(t('admin.open_todos')); ?></strong>
                                    <div id="adminOpenTodos" style="margin-top:0.25rem;color:var(--color-text-secondary);">-</div>
                                </div>
                                <div>
                                    <strong><?php echo htmlspecialchars(t('admin.registrations_logins')); ?></strong>
                                    <div id="adminTrends" style="margin-top:0.25rem;color:var(--color-text-secondary);">-</div>
                                </div>
                            </div>
                        </div>
                        <div class="widget">
                            <div class="widget-header">
                                <div class="widget-title"><?php echo htmlspecialchars(t('admin.write_message')); ?></div>
                                <button class="btn-primary" onclick="sendAdminMessage()"><?php echo htmlspecialchars(t('common.send')); ?></button>
                            </div>
                            <div class="input-group" style="flex-direction: column; gap: 0.75rem;">
                                <input type="text" id="adminMessageTitle" placeholder="<?php echo htmlspecialchars(t('admin.message_title_placeholder')); ?>">
                                <select id="adminMessageRecipient">
                                    <option value=""><?php echo htmlspecialchars(t('admin.all_users')); ?></option>
                                </select>
                                <textarea id="adminMessageBody" rows="6" placeholder="<?php echo htmlspecialchars(t('admin.message_body_placeholder')); ?>"></textarea>
                                <p id="adminMessageStatus" class="form-status"></p>
                            </div>
                        </div>
                        <div class="widget admin-users-widget">
                            <div class="widget-header">
                                <div class="widget-title"><?php echo htmlspecialchars(t('admin.user_management')); ?></div>
                                <button class="btn-primary" onclick="loadAdminUsers()"><?php echo htmlspecialchars(t('common.update')); ?></button>
                            </div>
                            <p style="margin-bottom:0.75rem;color:var(--color-text-secondary);font-size:0.9rem;">
                                <?php echo htmlspecialchars(t('admin.user_management_hint')); ?>
                            </p>
                            <div class="input-group" style="margin-top:0;margin-bottom:0.75rem;">
                                <input
                                    type="text"
                                    id="adminUsersSearch"
                                    placeholder="<?php echo htmlspecialchars(t('admin.user_search_placeholder')); ?>"
                                    oninput="filterAdminUsers()"
                                >
                            </div>
                            <div id="adminUsersList" class="messages-list">
                                <p class="message-empty"><?php echo htmlspecialchars(t('admin.users_loading')); ?></p>
                            </div>
                            <p id="adminUsersStatus" class="form-status"></p>
                        </div>
                        <div class="widget admin-sent-messages-widget">
                            <div class="widget-header">
                                <div class="widget-title"><?php echo htmlspecialchars(t('admin.sent_messages')); ?></div>
                                <button class="btn-primary" onclick="loadAdminMessageManagement()"><?php echo htmlspecialchars(t('common.update')); ?></button>
                            </div>
                            <div class="messages-list" id="adminSentMessages">
                                <p class="message-empty"><?php echo htmlspecialchars(t('admin.no_sent_messages')); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
