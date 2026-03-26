<!-- Dateizweck: Tab-Template "admin-messages" fuer die Dashboard-Ansicht. -->
<!-- Hinweis: Enthält primär HTML-Struktur und UI-Bausteine fuer diesen Bereich. -->
                <!-- Admin Nachrichten Detail View -->
                <div id="admin-messages" class="view-content" style="display: none;">
                    <div class="content-header">
                        <h1><?php echo htmlspecialchars(t('admin.messages.title')); ?></h1>
                        <p><?php echo htmlspecialchars(t('admin.messages.subtitle')); ?></p>
                    </div>

                    <div class="widget">
                        <div class="widget-header">
                            <div class="widget-title">
                                <span class="widget-icon">💬</span>
                                <span><?php echo htmlspecialchars(t('admin.messages.overview')); ?></span>
                            </div>
                            <button class="btn-primary" onclick="loadAdminMessages()"><?php echo htmlspecialchars(t('common.update')); ?></button>
                        </div>
                        <div class="widget-body">
                            <div class="messages-list" id="adminMessagesList">
                                <p class="message-empty"><?php echo htmlspecialchars(t('common.loading')); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
