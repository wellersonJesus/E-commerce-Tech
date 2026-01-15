<?php
header('Content-Type: application/json');
session_start();

if (isset($_SESSION['user'])) {
    echo json_encode($_SESSION['user']);
} else {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
}