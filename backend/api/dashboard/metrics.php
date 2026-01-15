<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';
session_start();

if (empty($_SESSION['user'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden']);
    exit;
}

$db = get_db();
$u = $db->query('SELECT COUNT(*) as c FROM users')->fetch(PDO::FETCH_ASSOC);
$s = $db->query('SELECT COUNT(*) as c FROM services')->fetch(PDO::FETCH_ASSOC);

echo json_encode(['users' => (int)$u['c'], 'services' => (int)$s['c']]);
