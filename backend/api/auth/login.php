<?php
// backend/api/auth/login.php
header('Content-Type: application/json');

session_start();
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (empty($data['email']) || empty($data['password'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'E-mail e senha são obrigatórios.']);
    exit;
}

// Busca o usuário pelo e-mail
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$data['email']]);
$user = $stmt->fetch();

// Verificação segura de senha com password_verify
if ($user && password_verify($data['password'], $user['password'])) {
    // Armazena dados do usuário em um único array na sessão, como esperado por outros scripts
    $_SESSION['user'] = [
        'id' => $user['id'],
        'name' => $user['name'],
        'role' => $user['role']
    ];

    // Retorna sucesso e a role do usuário para o frontend decidir o redirecionamento
    echo json_encode(['success' => true, 'role' => $user['role']]);
} else {
    // Credenciais inválidas
    http_response_code(401); // Unauthorized
    echo json_encode(['error' => 'E-mail ou senha inválidos.']);
}