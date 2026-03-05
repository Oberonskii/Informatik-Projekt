                <!-- Klassenarbeiten Detail View -->
                <div id="exams" class="view-content" style="display: none;">
                    <div class="content-header">
                        <h1>📝 Klassenarbeiten</h1>
                        <p>Behalte deine kommenden Arbeiten im Blick</p>
                    </div>
                    <div class="widget">
                        <div class="widget-header">
                            <div class="widget-title">Neue Klassenarbeit hinzufügen</div>
                        </div>
                        <div class="input-group">
                            <input type="text" id="examSubject" placeholder="Fach...">
                            <input type="date" id="examDate">
                            <input type="text" id="examTopic" placeholder="Thema...">
                            <button class="btn-primary" onclick="addExam()">Hinzufügen</button>
                        </div>
                        <div class="grades-list" id="examsList" style="margin-top: 1.5rem;"></div>
                    </div>
                </div>
