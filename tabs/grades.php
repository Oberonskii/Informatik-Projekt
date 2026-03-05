                <!-- Noten Detail View -->
                <div id="grades" class="view-content" style="display: none;">
                    <div class="content-header">
                        <h1>📝 Notenübersicht</h1>
                        <p>Alle deine Fächer und Noten</p>
                    </div>
                    <div class="widget">
                        <div class="widget-header">
                            <div class="widget-title">Note hinzufügen</div>
                        </div>
                        <div class="input-group">
                            <input type="text" id="gradeSubject" placeholder="Fach eingeben...">
                            <input type="number" id="gradeValue" placeholder="Note (0-15)" min="0" max="15">
                            <button class="btn-primary" onclick="addGrade()">Hinzufügen</button>
                        </div>
                        <div class="grades-list" id="gradesList" style="margin-top: 1.5rem;">
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
                            <div class="grade-item">
                                <span class="grade-subject">Deutsch</span>
                                <span class="grade-value">11 P</span>
                            </div>
                            <div class="grade-item">
                                <span class="grade-subject">Englisch</span>
                                <span class="grade-value">14 P</span>
                            </div>
                            <div class="grade-item">
                                <span class="grade-subject">Ethik</span>
                                <span class="grade-value">12 P</span>
                            </div>
                        </div>
                    </div>
                </div>
