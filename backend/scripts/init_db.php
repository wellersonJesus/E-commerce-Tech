<?php
// backend/scripts/init_db.php

// Carrega variáveis de ambiente para pegar a senha do admin
require_once __DIR__ . '/../api/config/env.php';

// Caminho do banco de dados SQLite
$dbPath = __DIR__ . '/../database/database.sqlite';

echo "Inicializando banco de dados em: $dbPath\n";

try {
    // Conecta ao SQLite
    $pdo = new PDO('sqlite:' . $dbPath);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1. Executar Schema (Isso vai rodar os DROP TABLE e CREATE TABLE)
    echo "Executando schema.sql (Recriando tabelas)...\n";
    $schema = file_get_contents(__DIR__ . '/../database/schema.sql');
    $pdo->exec($schema);

    // 2. Executar Seeds (Insere os dados novos)
    echo "Executando seeds.sql (Inserindo dados de exemplo)...\n";
    $seeds = file_get_contents(__DIR__ . '/../database/seeds.sql');
    $pdo->exec($seeds);

    // 3. Garantir que o Admin existe (baseado no .env)
    $adminEmail = getenv('ADMIN_EMAIL') ?: 'admin@example.com';
    
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$adminEmail]);
    
    if (!$stmt->fetch()) {
        echo "Criando usuário Admin padrão...\n";
        $adminName = getenv('ADMIN_NAME') ?: 'Admin';
        $adminPass = getenv('ADMIN_PASSWORD') ?: 'admin123';
        $hashedAdminPass = password_hash($adminPass, PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'admin')");
        $stmt->execute([$adminName, $adminEmail, $hashedAdminPass]);
    }

    // 4. Garantir que o usuário de exemplo existe
    $userEmail = 'user@example.com';
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$userEmail]);

    if (!$stmt->fetch()) {
        echo "Criando usuário de exemplo (user@example.com)...\n";
        $userPass = '123456';
        $hashedUserPass = password_hash($userPass, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES ('User', ?, ?, 'user')");
        $stmt->execute([$userEmail, $hashedUserPass]);
    }


    echo "Sucesso! O banco de dados foi resetado.\n";

} catch (PDOException $e) {
    die("Erro ao manipular banco de dados: " . $e->getMessage() . "\n");
}