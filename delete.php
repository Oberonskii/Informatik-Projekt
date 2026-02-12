<?php
session_start();
$user_id = $_SESSION['user_id'];
$file_id = $_GET['file_id'];

$backend_url = "http://127.0.0.1:8000/files/delete/$user_id/$file_id";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $backend_url);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
curl_close($ch);

header("Location: current_dashboard.php");
exit();
?>
