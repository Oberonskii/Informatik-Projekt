<?php
/**
 * Dateizweck: Endpoint oder Seite "homework_add" im Modul "homework".
 * Hinweis: Diese Datei ist Teil der LearnHub-Backend/Frontend-Anbindung.
 */
session_start();

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(["error" => "Nicht eingeloggt"]);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input || empty($input['day']) || empty($input['title']) || !isset($input['period'])) {
    http_response_code(400);
    echo json_encode(["error" => "Ungültige Eingabedaten"]);
    exit();
}

$user_id = $_SESSION['user_id'];
$backend_url = "http://127.0.0.1:8000/homework/$user_id";

$payload = json_encode([
    'day' => $input['day'],
    'period' => (int)$input['period'],
    'title' => $input['title']
]);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $backend_url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

http_response_code($httpCode);
header('Content-Type: application/json');
echo $response;
exit();
