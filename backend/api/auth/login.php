<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';
session_start();

$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
$email = $input['email'] ?? null;
$password = $input['password'] ?? null;

if (!$email || !$password) {
    http_response_code(400);
    echo json_encode(['error' => 'Email and password required']);
    exit;
}

$db = get_db();
$stmt = $db->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
$stmt->execute([':email' => $email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    http_response_code(401);
    echo json_encode(['error' => 'Invalid credentials']);
    exit;
}

$stored = $user['password'];
$verified = false;

// If stored password looks like a password_hash output, verify with password_verify
if (strpos($stored, '$2y$') === 0 || strpos($stored, '$argon2') === 0) {
    $verified = password_verify($password, $stored);
} else {
    // Fallback: plaintext comparison for initial seeds; if matches, re-hash
    $verified = ($password === $stored);
    if ($verified) {
        $newHash = password_hash($password, PASSWORD_DEFAULT);
        $u = $db->prepare('UPDATE users SET password = :pw WHERE id = :id');
        $u->execute([':pw' => $newHash, ':id' => $user['id']]);
        $user['password'] = $newHash;
    }
}

if (!$verified) {
    http_response_code(401);
    echo json_encode(['error' => 'Invalid credentials']);
    exit;
}

// Auth success: create session
$_SESSION['user'] = [
    'id' => $user['id'],
    'name' => $user['name'],
    'email' => $user['email'],
    'role' => $user['role']
];

echo json_encode(['success' => true, 'user' => $_SESSION['user']]);
