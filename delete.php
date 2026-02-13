<?php
session_start();
header('Content-Type: application/json');


function is_ajax_request(): bool {
    return isset($_SERVER['HTTP_X_REQUESTED_WITH'])
        && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(["error" => "Nicht eingeloggt"]);
    exit();
}

$file_id = $_POST['file_id'] ?? $_GET['file_id'] ?? '';

if ($file_id === '') {
if (!isset($_GET['file_id']) || $_GET['file_id'] === '') {
    http_response_code(400);
    echo json_encode(["error" => "Keine Datei-ID übergeben"]);
    exit();
}

$user_id = $_SESSION['user_id'];

$backend_url = "http://127.0.0.1:8000/files/$user_id/$file_id";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $backend_url);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);

if ($response === false) {
    http_response_code(502);
    echo json_encode(["error" => "cURL Fehler: " . curl_error($ch)]);
    curl_close($ch);
    exit();
}

$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$decoded_response = json_decode($response, true);

if ($status_code >= 400) {
    http_response_code($status_code);
    echo json_encode([
        "error" => $decoded_response['detail'] ?? "Löschen fehlgeschlagen"
    ]);
    exit();
}

if (!is_ajax_request()) {
    header('Location: current_dashboard.php#files');
    exit();
}

echo json_encode([
    "message" => $decoded_response['message'] ?? "Datei gelöscht"
]);
exit();
