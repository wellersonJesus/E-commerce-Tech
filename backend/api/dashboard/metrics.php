<?php
// backend/api/dashboard/metrics.php
header('Content-Type: application/json');
session_start();
require_once __DIR__ . '/../config/database.php';

// Verifica autenticação
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    http_response_code(403); // Forbidden
    echo json_encode(['error' => 'Acesso negado. Somente administradores.']);
    exit;
}

try {
    // Total de Usuários
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $usersCount = $stmt->fetch()['count'];

    // Total de Serviços
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM services");
    $servicesCount = $stmt->fetch()['count'];

    echo json_encode([
        'users' => $usersCount,
        'services' => $servicesCount
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao carregar métricas']);
}