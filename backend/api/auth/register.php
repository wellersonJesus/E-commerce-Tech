<?php
// backend/api/auth/register.php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (empty($data['name']) || empty($data['email']) || empty($data['password'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Preencha todos os campos']);
    exit;
}

// Verifica se email já existe
$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$data['email']]);
if ($stmt->fetch()) {
    http_response_code(409);
    echo json_encode(['error' => 'E-mail já cadastrado']);
    exit;
}

try {
    // Hash da senha para armazenamento seguro
    $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

    // Insere novo usuário (role = user)
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'user')");
    $stmt->execute([$data['name'], $data['email'], $hashedPassword]);
    
    echo json_encode(['message' => 'Cadastro realizado com sucesso!']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao salvar usuário']);
}