<?php
/**
 * Dateizweck: Speichert die gewaehlte Sprache in der Session und leitet zurueck.
 */
session_start();

require_once __DIR__ . '/../includes/i18n.php';

$locale = $_POST['locale'] ?? $_GET['locale'] ?? 'de';
learnhub_set_locale($locale);

$redirect = $_POST['redirect'] ?? $_GET['redirect'] ?? '../current_dashboard.php';
$redirect = is_string($redirect) && $redirect !== '' ? $redirect : '../current_dashboard.php';

header('Location: ' . $redirect);
exit();