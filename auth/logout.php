<?php
/**
 * Dateizweck: Endpoint oder Seite "logout" im Modul "auth".
 * Hinweis: Diese Datei ist Teil der LearnHub-Backend/Frontend-Anbindung.
 */
session_start();
session_destroy();
header("Location: login.php");
exit();
