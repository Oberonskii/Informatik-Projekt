<?php
/**
 * Dateizweck: Endpoint oder Seite "calendar_delete" im Modul "calendar".
 * Hinweis: Diese Datei ist Teil der LearnHub-Backend/Frontend-Anbindung.
 */
session_start();

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(["error" => "Nicht eingeloggt"]);
    exit();
}

if (!isset($_GET['event_id'])) {
    http_response_code(400);
    echo json_encode(["error" => "event_id fehlt"]);
    exit();
}

$user_id = $_SESSION['user_id'];
$event_id = $_GET['event_id'];
$delete_scope = $_GET['delete_scope'] ?? 'series';
$occurrence_date = $_GET['occurrence_date'] ?? null;

$query = http_build_query(array_filter([
    'delete_scope' => $delete_scope,
    'occurrence_date' => $occurrence_date,
], static fn ($value) => $value !== null && $value !== ''));

$backend_url = "http://127.0.0.1:8000/calendar-extras/$user_id/$event_id" . ($query ? "?$query" : '');

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $backend_url);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

http_response_code($httpCode);
header('Content-Type: application/json');
echo $response;
exit();
