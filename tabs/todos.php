                <!-- To-Dos Detail View -->
                <div id="todos" class="view-content" style="display: none;">
                    <div class="content-header">
                        <h1>✅ To-Do Liste</h1>
                        <p>Verwalte deine Aufgaben</p>
                    </div>
                    <div class="widget">
                        <div class="widget-header">
                            <div class="widget-title">Neue Aufgabe</div>
                        </div>
                        <div class="input-group">
                            <input type="text" id="todoInput" placeholder="Aufgabe eingeben...">
                            <select id="todoPriority">
                                <option value="low">Niedrig</option>
                                <option value="medium">Mittel</option>
                                <option value="high">Hoch</option>
                            </select>
                            <button class="btn-primary" onclick="addTodo()">Hinzufügen</button>
                        </div>
                        <div class="todo-list" id="todosDetailList" style="margin-top: 1.5rem;"></div>
                    </div>
                </div>
