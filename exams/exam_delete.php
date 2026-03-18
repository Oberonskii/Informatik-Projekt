<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(["error" => "Nicht eingeloggt"]);
    exit();
}

if (!isset($_GET['exam_id'])) {
    http_response_code(400);
    echo json_encode(["error" => "exam_id fehlt"]);
    exit();
}

$user_id = $_SESSION['user_id'];
$exam_id = $_GET['exam_id'];
$backend_url = "http://127.0.0.1:8000/exams/$user_id/$exam_id";

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