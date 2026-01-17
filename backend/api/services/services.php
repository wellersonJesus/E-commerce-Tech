<?php
// backend/api/services/services.php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    try {
        // Busca todos os serviços ativos
        $stmt = $pdo->query("SELECT * FROM services WHERE active = 1");
        $services = $stmt->fetchAll();
        echo json_encode(['services' => $services]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    http_response_code(405); // Method Not Allowed
}