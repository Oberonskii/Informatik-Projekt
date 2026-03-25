<?php
/**
 * Dateizweck: Endpoint oder Seite "user_role_update" im Modul "admin".
 * Hinweis: Diese Datei ist Teil der LearnHub-Backend/Frontend-Anbindung.
 */
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(["error" => "Nicht eingeloggt"]);
    exit();
}

$user_id = $_SESSION['user_id'];
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    http_response_code(400);
    echo json_encode(["error" => "Ungültige Eingabe"]);
    exit();
}

$target_user_id = trim((string)($input['user_id'] ?? ''));
$role = strtolower(trim((string)($input['role'] ?? '')));

if ($target_user_id === '') {
    http_response_code(400);
    echo json_encode(["error" => "Nutzer-ID fehlt"]);
    exit();
}

if ($role !== 'admin' && $role !== 'user') {
    http_response_code(400);
    echo json_encode(["error" => "Ungültige Rolle"]);
    exit();
}

$backend_url = "http://127.0.0.1:8000/admin/users/$user_id/$target_user_id/role";
$payload = json_encode(['role' => $role]);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $backend_url);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($response === false || $response === null) {
    http_response_code(502);
    echo json_encode(["error" => "Backend nicht erreichbar"]);
    exit();
}

http_response_code($httpCode ?: 500);
echo $response;
exit();
