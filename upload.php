<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(["error" => "Nicht eingeloggt"]);
    exit();
}

$user_id = $_SESSION['user_id'];

if (!isset($_FILES['file'])) {
    http_response_code(400);
    echo json_encode(["error" => "Keine Datei empfangen"]);
    exit();
}

$subject = $_POST['subject'];
$file = $_FILES['file'];

$backend_url = "http://127.0.0.1:8000/files/upload/$user_id";

$ch = curl_init();

$data = [
    'subject' => $subject,
    'file' => new CURLFile(
        $file['tmp_name'],
        $file['type'],
        $file['name']
    )
];

curl_setopt($ch, CURLOPT_URL, $backend_url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

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
        "error" => $decoded_response['detail'] ?? "Upload fehlgeschlagen"
    ]);
    exit();
}

echo json_encode([
    "message" => $decoded_response['message'] ?? "Datei erfolgreich hochgeladen"
]);
exit();
