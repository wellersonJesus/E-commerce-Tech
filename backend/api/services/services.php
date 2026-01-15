<?php
// Ativar exibição de erros para debug
ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';
session_start();

$db = get_db();
$method = $_SERVER['REQUEST_METHOD'];

// Public listing: GET /backend/api/services/services.php?public=1
if ($method === 'GET') {
    $public = isset($_GET['public']);
    if ($public) {
        $stmt = $db->prepare('SELECT id, title, description FROM services WHERE active = 1');
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['services' => $rows]);
        exit;
    }
    // Admin listing (all)
    if (empty($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
        http_response_code(403);
        echo json_encode(['error' => 'Forbidden']);
        exit;
    }
    $stmt = $db->query('SELECT * FROM services');
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['services' => $rows]);
    exit;
}

// Admin-only create/update/delete
if (empty($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden']);
    exit;
}

if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    $title = $input['title'] ?? null;
    $description = $input['description'] ?? '';
    $active = isset($input['active']) ? (int)$input['active'] : 1;
    if (!$title) { http_response_code(400); echo json_encode(['error'=>'Missing title']); exit; }
    $stmt = $db->prepare('INSERT INTO services (title, description, active) VALUES (:t, :d, :a)');
    $stmt->execute([':t'=>$title, ':d'=>$description, ':a'=>$active]);
    echo json_encode(['success'=>true, 'id'=>$db->lastInsertId()]);
    exit;
}

if ($method === 'PUT') {
    parse_str(file_get_contents('php://input'), $input);
    $id = $input['id'] ?? null;
    $title = $input['title'] ?? null;
    $description = $input['description'] ?? null;
    $active = isset($input['active']) ? (int)$input['active'] : null;
    if (!$id) { http_response_code(400); echo json_encode(['error'=>'Missing id']); exit; }
    $fields = [];
    $params = [':id'=>$id];
    if ($title !== null) { $fields[] = 'title = :title'; $params[':title']=$title; }
    if ($description !== null) { $fields[] = 'description = :description'; $params[':description']=$description; }
    if ($active !== null) { $fields[] = 'active = :active'; $params[':active']=$active; }
    if (empty($fields)) { http_response_code(400); echo json_encode(['error'=>'No fields']); exit; }
    $sql = 'UPDATE services SET ' . implode(', ', $fields) . ' WHERE id = :id';
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    echo json_encode(['success'=>true]);
    exit;
}

if ($method === 'DELETE') {
    parse_str(file_get_contents('php://input'), $input);
    $id = $input['id'] ?? null;
    if (!$id) { http_response_code(400); echo json_encode(['error'=>'Missing id']); exit; }
    $stmt = $db->prepare('DELETE FROM services WHERE id = :id');
    $stmt->execute([':id'=>$id]);
    echo json_encode(['success'=>true]);
    exit;
}

http_response_code(405);
echo json_encode(['error' => 'Method not allowed']);
