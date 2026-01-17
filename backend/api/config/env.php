<?php
// backend/api/config/env.php

// Caminho para o arquivo .env na raiz do projeto
$envFile = __DIR__ . '/../../../.env';

if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Ignora comentários
        if (strpos(trim($line), '#') === 0) continue;
        
        // Separa chave e valor
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            
            // Define a variável de ambiente
            putenv("$key=$value");
            $_ENV[$key] = $value;
        }
    }
}