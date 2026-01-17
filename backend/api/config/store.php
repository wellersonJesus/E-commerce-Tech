<?php
// backend/api/contacts/store.php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/env.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (empty($data['name']) || empty($data['email']) || empty($data['message'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Preencha todos os campos: nome, email e mensagem.']);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO contacts (name, email, message) VALUES (?, ?, ?)");
    $stmt->execute([$data['name'], $data['email'], $data['message']]);
    
    // Envio de notificação por e-mail
    $destinatario = getenv('CONTACT_EMAIL');
    
    if ($destinatario) {
        $assunto = "Nova mensagem de contato: {$data['name']}";
        $mensagem = "Nome: {$data['name']}\n";
        $mensagem .= "E-mail: {$data['email']}\n\n";
        $mensagem .= "Mensagem:\n{$data['message']}";
        
        $headers = "From: noreply@landpage.com\r\nReply-To: {$data['email']}";
        
        // Tenta enviar o e-mail. Se falhar (comum em localhost), salva em log.
        if (!@mail($destinatario, $assunto, $mensagem, $headers)) {
            $logDir = __DIR__ . '/../../logs';
            if (!is_dir($logDir)) {
                mkdir($logDir, 0777, true);
            }
            $logFile = $logDir . '/email_log.txt';
            $logEntry = "--- " . date('Y-m-d H:i:s') . " ---\n";
            $logEntry .= "Para: $destinatario\n";
            $logEntry .= "Assunto: $assunto\n";
            $logEntry .= "Mensagem:\n$mensagem\n\n";
            file_put_contents($logFile, $logEntry, FILE_APPEND);
        }
    }
    
    echo json_encode(['message' => 'Mensagem recebida com sucesso! Entraremos em contato em breve.']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao salvar mensagem.']);
}