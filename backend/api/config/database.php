<?php
require_once __DIR__ . '/env.php';

function get_db()
{
    $connection = getenv('DB_CONNECTION') ?: 'sqlite';

    if ($connection === 'sqlite') {
        $envDb = getenv('DB_DATABASE');
        if ($envDb) {
            // Se o caminho não for absoluto (não começa com / ou C:), resolve a partir da raiz do projeto
            // __DIR__ é backend/api/config, então subir 3 níveis chega na raiz
            $dbPath = ($envDb[0] === '/' || (isset($envDb[1]) && $envDb[1] === ':')) ? $envDb : realpath(__DIR__ . '/../../../') . '/' . $envDb;
        } else {
            $dbPath = __DIR__ . '/../../database/landpage.sqlite';
        }

        $dir = dirname($dbPath);
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        $dsn = 'sqlite:' . $dbPath;
        $username = null;
        $password = null;
    } else {
        $host = getenv('DB_HOST') ?: '127.0.0.1';
        $db   = getenv('DB_DATABASE') ?: 'landpage';
        $username = getenv('DB_USERNAME') ?: 'root';
        $password = getenv('DB_PASSWORD') ?: '';
        $dsn = "$connection:host=$host;dbname=$db;charset=utf8mb4";
    }

    try {
        $pdo = new PDO($dsn, $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // Enable foreign keys
        if ($connection === 'sqlite') $pdo->exec('PRAGMA foreign_keys = ON;');
        return $pdo;
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database connection failed']);
        exit;
    }
}
