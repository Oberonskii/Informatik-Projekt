                <!-- Karteikarten Detail View -->
                <div id="flashcards" class="view-content" style="display: none;">
                    <div class="content-header">
                        <h1>🎴 Karteikarten</h1>
                        <p>Lerne mit deinen Karteikarten</p>
                    </div>
                    <div class="widget">
                        <div class="flashcard" id="flashcardDetail" onclick="flipCard('flashcardDetail')">
                            <div class="flashcard-inner" id="flashcardInnerDetail">
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
                            <span style="color: var(--color-text-secondary);" id="cardCounter">Karte 1 von 5</span>
                            <button class="flashcard-btn" onclick="nextCard(); event.stopPropagation();">Weiter →</button>
                        </div>
                    </div>
                </div>
