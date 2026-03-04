# =========================================
# LearnHub Backend
# Autor: Backend-Team
# Technologie: FastAPI + SQLite
# =========================================
from fastapi import UploadFile, File, Form
from fastapi.responses import FileResponse
from fastapi import FastAPI, HTTPException, Depends
from pydantic import BaseModel
from typing import List, Optional
from datetime import datetime
import sqlite3
import uuid
import hashlib
import os
from fastapi import UploadFile, File
from fastapi.responses import FileResponse

UPLOAD_DIR = "uploads"
os.makedirs(UPLOAD_DIR, exist_ok=True)


# =========================================
# APP INITIALISIERUNG
# =========================================

app = FastAPI(
    title="LearnHub API",
    description="Backend für die LearnHub Lernplattform",
    version="2.0.0"
)

DB_NAME = "learnhub.db"


# =========================================
# DATABASE SETUP
# =========================================

def get_db():
    conn = sqlite3.connect(DB_NAME)
    conn.row_factory = sqlite3.Row
    return conn


def safe_add_column(cursor, table, column, definition):
    """Fügt eine Spalte hinzu, ignoriert Fehler wenn sie bereits existiert."""
    try:
        cursor.execute(f"ALTER TABLE {table} ADD COLUMN {column} {definition}")
    except sqlite3.OperationalError:
        pass


def init_db():
    db = get_db()
    cursor = db.cursor()

    # FILES
    cursor.execute("""
    CREATE TABLE IF NOT EXISTS files (
        id TEXT PRIMARY KEY,
        user_id TEXT,
        filename TEXT,
        original_name TEXT,
        subject TEXT,
        uploaded_at TEXT
    )
    """)

    # USERS
    cursor.execute("""
    CREATE TABLE IF NOT EXISTS users (
        id TEXT PRIMARY KEY,
        username TEXT UNIQUE,
        email TEXT,
        password TEXT,
        role TEXT DEFAULT 'user',
        created_at TEXT
    )
    """)

    # Neue Spalten zu users hinzufügen (falls noch nicht vorhanden)
    safe_add_column(cursor, "users", "grade_type", "TEXT DEFAULT 'points'")

    # TODOS
    cursor.execute("""
    CREATE TABLE IF NOT EXISTS todos (
        id TEXT PRIMARY KEY,
        user_id TEXT,
        title TEXT,
        subject TEXT,
        due_date TEXT,
        priority TEXT,
        done INTEGER DEFAULT 0
    )
    """)

    # GRADES
    cursor.execute("""
    CREATE TABLE IF NOT EXISTS grades (
        id TEXT PRIMARY KEY,
        user_id TEXT,
        subject TEXT,
        value REAL,
        description TEXT,
        date TEXT
    )
    """)

    # Neue Spalte zu grades hinzufügen
    safe_add_column(cursor, "grades", "weight_type", "TEXT DEFAULT 'written'")

    # TIMETABLE
    cursor.execute("""
    CREATE TABLE IF NOT EXISTS timetable (
        id TEXT PRIMARY KEY,
        user_id TEXT,
        day TEXT,
        time TEXT,
        subject TEXT
    )
    """)

    # Neue Spalten zu timetable hinzufügen
    safe_add_column(cursor, "timetable", "slot_number", "INTEGER DEFAULT 0")

    # FLASHCARDS
    cursor.execute("""
    CREATE TABLE IF NOT EXISTS flashcards (
        id TEXT PRIMARY KEY,
        user_id TEXT,
        subject TEXT,
        front TEXT,
        back TEXT,
        public INTEGER DEFAULT 0
    )
    """)

    # Neue Spalte zu flashcards hinzufügen
    safe_add_column(cursor, "flashcards", "stack_name", "TEXT DEFAULT 'Standard'")

    # LESSON TIMES
    cursor.execute("""
    CREATE TABLE IF NOT EXISTS lesson_times (
        id TEXT PRIMARY KEY,
        user_id TEXT,
        slot_number INTEGER,
        start_time TEXT,
        end_time TEXT,
        UNIQUE(user_id, slot_number)
    )
    """)

    # SUBJECT SETTINGS (Gewichtung mündlich/schriftlich)
    cursor.execute("""
    CREATE TABLE IF NOT EXISTS subject_settings (
        id TEXT PRIMARY KEY,
        user_id TEXT,
        subject TEXT,
        oral_weight REAL DEFAULT 50,
        written_weight REAL DEFAULT 50,
        UNIQUE(user_id, subject)
    )
    """)

    db.commit()
    db.close()


init_db()

# =========================================
# HILFSFUNKTIONEN
# =========================================

def hash_password(password: str) -> str:
    return hashlib.sha256(password.encode()).hexdigest()


def generate_id() -> str:
    return str(uuid.uuid4())


# =========================================
# Pydantic MODELS (Request / Response)
# =========================================

class ChangeUsername(BaseModel):
    new_username: str


class ChangePassword(BaseModel):
    old_password: str
    new_password: str


class UserRegister(BaseModel):
    username: str
    email: str
    password: str


class UserLogin(BaseModel):
    username: str
    password: str


class TodoCreate(BaseModel):
    title: str
    subject: str
    due_date: str
    priority: str


class TodoUpdate(BaseModel):
    done: bool


class GradeCreate(BaseModel):
    subject: str
    value: float
    description: Optional[str] = ""
    weight_type: Optional[str] = "written"


class DeleteAccountRequest(BaseModel):
    password: str


class FlashcardCreate(BaseModel):
    subject: str
    front: str
    back: str
    public: bool = False
    stack_name: Optional[str] = "Standard"


class FlashcardUpdate(BaseModel):
    front: str
    back: str
    stack_name: Optional[str] = "Standard"


class TimetableCreate(BaseModel):
    day: str
    time: str
    subject: str
    slot_number: Optional[int] = 0


class LessonTimeEntry(BaseModel):
    slot_number: int
    start_time: str
    end_time: str


class LessonTimeBulk(BaseModel):
    times: List[LessonTimeEntry]


class UserSettings(BaseModel):
    grade_type: str  # 'points' oder 'grades'


class SubjectSetting(BaseModel):
    subject: str
    oral_weight: float
    written_weight: float


# =========================================
# AUTH ROUTES
# =========================================

@app.post("/auth/register")
def register(user: UserRegister):
    db = get_db()
    cursor = db.cursor()

    try:
        cursor.execute("""
        INSERT INTO users (id, username, email, password, role, created_at, grade_type)
        VALUES (?, ?, ?, ?, ?, ?, ?)
        """, (
            generate_id(),
            user.username,
            user.email,
            hash_password(user.password),
            "user",
            datetime.utcnow().isoformat(),
            "points"
        ))
        db.commit()
    except sqlite3.IntegrityError:
        raise HTTPException(status_code=400, detail="Username existiert bereits")

    return {"message": "Registrierung erfolgreich"}


@app.post("/auth/login")
def login(user: UserLogin):
    db = get_db()
    cursor = db.cursor()

    cursor.execute("""
    SELECT * FROM users WHERE username = ?
    """, (user.username,))
    user_db = cursor.fetchone()

    if user_db is None or user_db['password'] != hash_password(user.password):
        raise HTTPException(status_code=401, detail="Ungültiger Benutzername oder Passwort")

    return {
        "message": "Login erfolgreich",
        "user_id": user_db["id"],
        "username": user_db["username"],
        "role": user_db["role"]
    }


@app.put("/auth/change-username/{user_id}")
def change_username(user_id: str, data: ChangeUsername):
    db = get_db()
    cursor = db.cursor()

    cursor.execute("SELECT id FROM users WHERE username=?", (data.new_username,))
    if cursor.fetchone():
        raise HTTPException(status_code=400, detail="Username bereits vergeben")

    cursor.execute("""
        UPDATE users SET username=? WHERE id=?
    """, (data.new_username, user_id))

    if cursor.rowcount == 0:
        raise HTTPException(status_code=404, detail="User nicht gefunden")

    db.commit()
    return {"message": "Username erfolgreich geändert"}


@app.put("/auth/change-password/{user_id}")
def change_password(user_id: str, data: ChangePassword):
    db = get_db()
    cursor = db.cursor()

    cursor.execute("SELECT password FROM users WHERE id=?", (user_id,))
    user = cursor.fetchone()

    if not user:
        raise HTTPException(status_code=404, detail="User nicht gefunden")

    if user["password"] != hash_password(data.old_password):
        raise HTTPException(status_code=401, detail="Altes Passwort ist falsch")

    cursor.execute("""
        UPDATE users SET password=? WHERE id=?
    """, (hash_password(data.new_password), user_id))

    db.commit()
    return {"message": "Passwort erfolgreich geändert"}


@app.delete("/auth/delete-account/{user_id}")
def delete_account(user_id: str, data: DeleteAccountRequest):
    db = get_db()
    cursor = db.cursor()

    cursor.execute("SELECT password FROM users WHERE id=?", (user_id,))
    user = cursor.fetchone()

    if not user:
        raise HTTPException(status_code=404, detail="User nicht gefunden")

    if user["password"] != hash_password(data.password):
        raise HTTPException(status_code=401, detail="Passwort ist falsch")

    cursor.execute("DELETE FROM todos WHERE user_id=?", (user_id,))
    cursor.execute("DELETE FROM grades WHERE user_id=?", (user_id,))
    cursor.execute("DELETE FROM timetable WHERE user_id=?", (user_id,))
    cursor.execute("DELETE FROM flashcards WHERE user_id=?", (user_id,))
    cursor.execute("DELETE FROM files WHERE user_id=?", (user_id,))
    cursor.execute("DELETE FROM lesson_times WHERE user_id=?", (user_id,))
    cursor.execute("DELETE FROM subject_settings WHERE user_id=?", (user_id,))
    cursor.execute("DELETE FROM users WHERE id=?", (user_id,))
    db.commit()

    user_dir = os.path.join(UPLOAD_DIR, user_id)
    if os.path.exists(user_dir):
        for filename in os.listdir(user_dir):
            file_path = os.path.join(user_dir, filename)
            if os.path.isfile(file_path):
                os.remove(file_path)
        os.rmdir(user_dir)

    return {"message": "Account erfolgreich gelöscht"}


# =========================================
# USER SETTINGS ROUTES
# =========================================

@app.get("/users/settings/{user_id}")
def get_user_settings(user_id: str):
    db = get_db()
    cursor = db.cursor()

    cursor.execute("SELECT grade_type, role FROM users WHERE id=?", (user_id,))
    user = cursor.fetchone()

    if not user:
        raise HTTPException(status_code=404, detail="User nicht gefunden")

    return {
        "grade_type": user["grade_type"] or "points",
        "role": user["role"] or "user"
    }


@app.put("/users/settings/{user_id}")
def update_user_settings(user_id: str, settings: UserSettings):
    db = get_db()
    cursor = db.cursor()

    if settings.grade_type not in ("points", "grades"):
        raise HTTPException(status_code=400, detail="Ungültiger grade_type")

    cursor.execute("""
        UPDATE users SET grade_type=? WHERE id=?
    """, (settings.grade_type, user_id))

    if cursor.rowcount == 0:
        raise HTTPException(status_code=404, detail="User nicht gefunden")

    db.commit()
    return {"message": "Einstellungen gespeichert"}


# =========================================
# SUBJECT SETTINGS ROUTES
# =========================================

@app.get("/subject-settings/{user_id}")
def get_subject_settings(user_id: str):
    db = get_db()
    cursor = db.cursor()

    cursor.execute("""
        SELECT subject, oral_weight, written_weight
        FROM subject_settings WHERE user_id=?
    """, (user_id,))

    return [dict(row) for row in cursor.fetchall()]


@app.post("/subject-settings/{user_id}")
def set_subject_setting(user_id: str, setting: SubjectSetting):
    db = get_db()
    cursor = db.cursor()

    cursor.execute("""
        INSERT INTO subject_settings (id, user_id, subject, oral_weight, written_weight)
        VALUES (?, ?, ?, ?, ?)
        ON CONFLICT(user_id, subject) DO UPDATE SET
            oral_weight=excluded.oral_weight,
            written_weight=excluded.written_weight
    """, (
        generate_id(),
        user_id,
        setting.subject,
        setting.oral_weight,
        setting.written_weight
    ))

    db.commit()
    return {"message": "Facheinstellung gespeichert"}


# =========================================
# LESSON TIMES ROUTES
# =========================================

@app.get("/lesson-times/{user_id}")
def get_lesson_times(user_id: str):
    db = get_db()
    cursor = db.cursor()

    cursor.execute("""
        SELECT slot_number, start_time, end_time
        FROM lesson_times WHERE user_id=?
        ORDER BY slot_number
    """, (user_id,))

    return [dict(row) for row in cursor.fetchall()]


@app.put("/lesson-times/{user_id}")
def update_lesson_times(user_id: str, bulk: LessonTimeBulk):
    db = get_db()
    cursor = db.cursor()

    for entry in bulk.times:
        cursor.execute("""
            INSERT INTO lesson_times (id, user_id, slot_number, start_time, end_time)
            VALUES (?, ?, ?, ?, ?)
            ON CONFLICT(user_id, slot_number) DO UPDATE SET
                start_time=excluded.start_time,
                end_time=excluded.end_time
        """, (
            generate_id(),
            user_id,
            entry.slot_number,
            entry.start_time,
            entry.end_time
        ))

    db.commit()
    return {"message": "Stundenzeiten gespeichert"}


# =========================================
# TIMETABLE ROUTES
# =========================================

@app.get("/timetable/{user_id}")
def get_timetable(user_id: str):
    db = get_db()
    cursor = db.cursor()

    cursor.execute("""
    SELECT id, day, time, subject, slot_number
    FROM timetable
    WHERE user_id=?
    ORDER BY day, slot_number, time
    """, (user_id,))

    return [dict(row) for row in cursor.fetchall()]


@app.post("/timetable/{user_id}")
def add_timetable_entry(user_id: str, entry: TimetableCreate):
    db = get_db()
    cursor = db.cursor()

    cursor.execute("""
    INSERT INTO timetable (id, user_id, day, time, subject, slot_number)
    VALUES (?, ?, ?, ?, ?, ?)
    """, (
        generate_id(),
        user_id,
        entry.day,
        entry.time,
        entry.subject,
        entry.slot_number
    ))

    db.commit()
    return {"message": "Stunde hinzugefügt"}


@app.put("/timetable/{user_id}/{entry_id}")
def update_timetable_entry(user_id: str, entry_id: str, entry: TimetableCreate):
    db = get_db()
    cursor = db.cursor()

    cursor.execute("""
    UPDATE timetable
    SET day=?, time=?, subject=?, slot_number=?
    WHERE id=? AND user_id=?
    """, (
        entry.day,
        entry.time,
        entry.subject,
        entry.slot_number,
        entry_id,
        user_id
    ))

    if cursor.rowcount == 0:
        raise HTTPException(status_code=404, detail="Eintrag nicht gefunden")

    db.commit()
    return {"message": "Stunde aktualisiert"}


@app.delete("/timetable/{user_id}/{entry_id}")
def delete_timetable_entry(user_id: str, entry_id: str):
    db = get_db()
    cursor = db.cursor()

    cursor.execute("""
    DELETE FROM timetable WHERE id=? AND user_id=?
    """, (entry_id, user_id))

    if cursor.rowcount == 0:
        raise HTTPException(status_code=404, detail="Eintrag nicht gefunden")

    db.commit()
    return {"message": "Stunde gelöscht"}


# =========================================
# FILE UPLOAD ROUTES
# =========================================

@app.delete("/files/{user_id}/{file_id}")
def delete_file(user_id: str, file_id: str):
    db = get_db()
    cursor = db.cursor()

    cursor.execute("""
    SELECT filename FROM files WHERE id=? AND user_id=?
    """, (file_id, user_id))

    file = cursor.fetchone()
    if not file:
        raise HTTPException(status_code=404, detail="Datei nicht gefunden")

    file_path = os.path.join(UPLOAD_DIR, user_id, file["filename"])

    if os.path.exists(file_path):
        os.remove(file_path)

    cursor.execute("DELETE FROM files WHERE id=?", (file_id,))
    db.commit()

    return {"message": "Datei gelöscht"}


@app.get("/files/download/{user_id}/{file_id}")
def download_file(user_id: str, file_id: str):
    db = get_db()
    cursor = db.cursor()

    cursor.execute("""
    SELECT filename, original_name
    FROM files WHERE id=? AND user_id=?
    """, (file_id, user_id))

    file = cursor.fetchone()
    if not file:
        raise HTTPException(status_code=404, detail="Datei nicht gefunden")

    file_path = os.path.join(UPLOAD_DIR, user_id, file["filename"])

    if not os.path.exists(file_path):
        raise HTTPException(status_code=404, detail="Datei physisch nicht vorhanden")

    return FileResponse(
        path=file_path,
        filename=file["original_name"],
        media_type="application/octet-stream",
        headers={"Content-Disposition": f'attachment; filename="{file["original_name"]}"'}
    )


@app.get("/files/{user_id}")
def get_user_files(user_id: str):
    db = get_db()
    cursor = db.cursor()

    cursor.execute("""
    SELECT id, original_name, subject, uploaded_at
    FROM files WHERE user_id=?
    """, (user_id,))

    return cursor.fetchall()


ALLOWED_EXTENSIONS = {"pdf", "png", "jpg", "jpeg", "docx", "txt"}
MAX_FILE_SIZE = 5 * 1024 * 1024  # 5MB


@app.post("/files/upload/{user_id}")
async def upload_file(
    user_id: str,
    subject: str = Form(...),
    file: UploadFile = File(...)
):
    db = get_db()
    cursor = db.cursor()

    file_ext = file.filename.split(".")[-1].lower()
    if file_ext not in ALLOWED_EXTENSIONS:
        raise HTTPException(status_code=400, detail="Dateityp nicht erlaubt")

    content = await file.read()
    if len(content) > MAX_FILE_SIZE:
        raise HTTPException(status_code=400, detail="Datei zu groß (max 5MB)")

    file_id = generate_id()
    user_dir = os.path.join(UPLOAD_DIR, user_id)
    os.makedirs(user_dir, exist_ok=True)

    stored_filename = f"{file_id}.{file_ext}"
    file_path = os.path.join(user_dir, stored_filename)

    with open(file_path, "wb") as f:
        f.write(content)

    cursor.execute("""
    INSERT INTO files VALUES (?, ?, ?, ?, ?, ?)
    """, (
        file_id,
        user_id,
        stored_filename,
        file.filename,
        subject,
        datetime.utcnow().isoformat()
    ))

    db.commit()

    return {"message": "Datei erfolgreich hochgeladen"}


# =========================================
# TODO ROUTES
# =========================================

@app.post("/todos/{user_id}")
def create_todo(user_id: str, todo: TodoCreate):
    db = get_db()
    cursor = db.cursor()

    cursor.execute("""
    INSERT INTO todos VALUES (?, ?, ?, ?, ?, ?, ?)
    """, (
        generate_id(),
        user_id,
        todo.title,
        todo.subject,
        todo.due_date,
        todo.priority,
        0
    ))

    db.commit()
    return {"message": "To-Do erstellt"}


@app.get("/todos/{user_id}")
def get_todos(user_id: str):
    db = get_db()
    cursor = db.cursor()

    cursor.execute("SELECT * FROM todos WHERE user_id=? ORDER BY done ASC, priority DESC", (user_id,))
    return [dict(row) for row in cursor.fetchall()]


@app.put("/todos/{user_id}/{todo_id}")
def update_todo(user_id: str, todo_id: str, data: TodoUpdate):
    db = get_db()
    cursor = db.cursor()

    cursor.execute("""
    UPDATE todos SET done=? WHERE id=? AND user_id=?
    """, (int(data.done), todo_id, user_id))

    if cursor.rowcount == 0:
        raise HTTPException(status_code=404, detail="To-Do nicht gefunden")

    db.commit()
    return {"message": "To-Do aktualisiert"}


@app.delete("/todos/{user_id}/{todo_id}")
def delete_todo(user_id: str, todo_id: str):
    db = get_db()
    cursor = db.cursor()

    cursor.execute("""
    DELETE FROM todos WHERE id=? AND user_id=?
    """, (todo_id, user_id))

    if cursor.rowcount == 0:
        raise HTTPException(status_code=404, detail="To-Do nicht gefunden")

    db.commit()
    return {"message": "To-Do gelöscht"}


# =========================================
# GRADES ROUTES
# =========================================

@app.post("/grades/{user_id}")
def add_grade(user_id: str, grade: GradeCreate):
    db = get_db()
    cursor = db.cursor()

    cursor.execute("""
    INSERT INTO grades (id, user_id, subject, value, description, date, weight_type)
    VALUES (?, ?, ?, ?, ?, ?, ?)
    """, (
        generate_id(),
        user_id,
        grade.subject,
        grade.value,
        grade.description,
        datetime.utcnow().isoformat(),
        grade.weight_type
    ))

    db.commit()
    return {"message": "Note gespeichert"}


@app.get("/grades/{user_id}")
def get_grades(user_id: str):
    db = get_db()
    cursor = db.cursor()

    cursor.execute("SELECT * FROM grades WHERE user_id=? ORDER BY date DESC", (user_id,))
    return [dict(row) for row in cursor.fetchall()]


@app.delete("/grades/{user_id}/{grade_id}")
def delete_grade(user_id: str, grade_id: str):
    db = get_db()
    cursor = db.cursor()

    cursor.execute("DELETE FROM grades WHERE id=? AND user_id=?", (grade_id, user_id))

    if cursor.rowcount == 0:
        raise HTTPException(status_code=404, detail="Note nicht gefunden")

    db.commit()
    return {"message": "Note gelöscht"}


# =========================================
# FLASHCARDS ROUTES
# =========================================

@app.post("/flashcards/{user_id}")
def create_flashcard(user_id: str, card: FlashcardCreate):
    db = get_db()
    cursor = db.cursor()

    cursor.execute("""
    INSERT INTO flashcards (id, user_id, subject, front, back, public, stack_name)
    VALUES (?, ?, ?, ?, ?, ?, ?)
    """, (
        generate_id(),
        user_id,
        card.subject,
        card.front,
        card.back,
        int(card.public),
        card.stack_name or "Standard"
    ))

    db.commit()
    return {"message": "Karteikarte erstellt"}


@app.get("/flashcards/{user_id}")
def get_flashcards(user_id: str):
    db = get_db()
    cursor = db.cursor()

    cursor.execute("""
    SELECT * FROM flashcards WHERE user_id=? ORDER BY stack_name, id
    """, (user_id,))
    return [dict(row) for row in cursor.fetchall()]


@app.put("/flashcards/{user_id}/{card_id}")
def update_flashcard(user_id: str, card_id: str, data: FlashcardUpdate):
    db = get_db()
    cursor = db.cursor()

    cursor.execute("""
    UPDATE flashcards SET front=?, back=?, stack_name=?
    WHERE id=? AND user_id=?
    """, (data.front, data.back, data.stack_name or "Standard", card_id, user_id))

    if cursor.rowcount == 0:
        raise HTTPException(status_code=404, detail="Karteikarte nicht gefunden")

    db.commit()
    return {"message": "Karteikarte aktualisiert"}


@app.delete("/flashcards/{user_id}/{card_id}")
def delete_flashcard(user_id: str, card_id: str):
    db = get_db()
    cursor = db.cursor()

    cursor.execute("DELETE FROM flashcards WHERE id=? AND user_id=?", (card_id, user_id))

    if cursor.rowcount == 0:
        raise HTTPException(status_code=404, detail="Karteikarte nicht gefunden")

    db.commit()
    return {"message": "Karteikarte gelöscht"}


# =========================================
# ADMIN ROUTES
# =========================================

@app.get("/admin/stats")
def admin_stats():
    db = get_db()
    cursor = db.cursor()

    cursor.execute("SELECT COUNT(*) as count FROM users")
    users = cursor.fetchone()["count"]

    cursor.execute("SELECT COUNT(*) as count FROM flashcards")
    cards = cursor.fetchone()["count"]

    cursor.execute("SELECT COUNT(*) as count FROM todos")
    todos = cursor.fetchone()["count"]

    cursor.execute("SELECT COUNT(*) as count FROM todos WHERE done=1")
    todos_done = cursor.fetchone()["count"]

    cursor.execute("SELECT COUNT(*) as count FROM grades")
    grades = cursor.fetchone()["count"]

    cursor.execute("SELECT COUNT(*) as count FROM files")
    files = cursor.fetchone()["count"]

    return {
        "total_users": users,
        "total_flashcards": cards,
        "total_todos": todos,
        "total_todos_done": todos_done,
        "total_grades": grades,
        "total_files": files
    }


@app.get("/admin/users")
def admin_get_users(requester_id: str):
    db = get_db()
    cursor = db.cursor()

    cursor.execute("SELECT role FROM users WHERE id=?", (requester_id,))
    requester = cursor.fetchone()
    if not requester or requester["role"] != "admin":
        raise HTTPException(status_code=403, detail="Keine Berechtigung")

    cursor.execute("""
        SELECT id, username, email, role, created_at
        FROM users ORDER BY created_at DESC
    """)
    return [dict(row) for row in cursor.fetchall()]


@app.delete("/admin/users/{target_id}")
def admin_delete_user(target_id: str, requester_id: str):
    db = get_db()
    cursor = db.cursor()

    cursor.execute("SELECT role FROM users WHERE id=?", (requester_id,))
    requester = cursor.fetchone()
    if not requester or requester["role"] != "admin":
        raise HTTPException(status_code=403, detail="Keine Berechtigung")

    cursor.execute("DELETE FROM todos WHERE user_id=?", (target_id,))
    cursor.execute("DELETE FROM grades WHERE user_id=?", (target_id,))
    cursor.execute("DELETE FROM timetable WHERE user_id=?", (target_id,))
    cursor.execute("DELETE FROM flashcards WHERE user_id=?", (target_id,))
    cursor.execute("DELETE FROM files WHERE user_id=?", (target_id,))
    cursor.execute("DELETE FROM lesson_times WHERE user_id=?", (target_id,))
    cursor.execute("DELETE FROM subject_settings WHERE user_id=?", (target_id,))
    cursor.execute("DELETE FROM users WHERE id=?", (target_id,))
    db.commit()

    return {"message": "User gelöscht"}


@app.put("/admin/users/{target_id}/role")
def admin_set_role(target_id: str, requester_id: str, role: str):
    db = get_db()
    cursor = db.cursor()

    cursor.execute("SELECT role FROM users WHERE id=?", (requester_id,))
    requester = cursor.fetchone()
    if not requester or requester["role"] != "admin":
        raise HTTPException(status_code=403, detail="Keine Berechtigung")

    if role not in ("user", "admin"):
        raise HTTPException(status_code=400, detail="Ungültige Rolle")

    cursor.execute("UPDATE users SET role=? WHERE id=?", (role, target_id))
    db.commit()
    return {"message": "Rolle aktualisiert"}


if __name__ == "__main__":
    import uvicorn
    uvicorn.run(
        "backend:app",
        host="127.0.0.1",
        port=8000,
        reload=True
    )
