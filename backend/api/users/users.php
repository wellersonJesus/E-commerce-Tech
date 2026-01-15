<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';
session_start();

$method = $_SERVER['REQUEST_METHOD'];
$db = get_db();

function require_admin() {
    if (empty($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
        http_response_code(403);
        echo json_encode(['error' => 'Forbidden']);
        exit;
    }
}

if ($method === 'GET') {
    // List users (admin only)
    require_admin();
    $stmt = $db->query('SELECT id, name, email, role, created_at FROM users');
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['users' => $rows]);
    exit;
}

if ($method === 'POST') {
    // Create user (admin)
    require_admin();
    $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    $name = $input['name'] ?? null;
    $email = $input['email'] ?? null;
    $password = $input['password'] ?? null;
    $role = $input['role'] ?? 'user';
    if (!$name || !$email || !$password) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing fields']);
        exit;
    }
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $db->prepare('INSERT INTO users (name, email, password, role) VALUES (:name, :email, :pw, :role)');
    $stmt->execute([':name'=>$name, ':email'=>$email, ':pw'=>$hash, ':role'=>$role]);
    echo json_encode(['success' => true, 'id' => $db->lastInsertId()]);
    exit;
}

http_response_code(405);
echo json_encode(['error' => 'Method not allowed']);
