<?php
// backend/scripts/init_db.php
require_once __DIR__ . '/../api/config/env.php';
require_once __DIR__ . '/../api/config/database.php';

echo "Initializing SQLite database..." . PHP_EOL;

try {
    $db = get_db();
} catch (Exception $e) {
    echo "Failed to get DB: " . $e->getMessage() . PHP_EOL;
    exit(1);
}

$schemaFile = __DIR__ . '/../database/schema.sql';
$seedsFile = __DIR__ . '/../database/seeds.sql';

if (!file_exists($schemaFile)) {
    echo "Schema file not found: {$schemaFile}" . PHP_EOL;
    exit(1);
}

$schema = file_get_contents($schemaFile);
try {
    $db->exec($schema);
    echo "Schema applied." . PHP_EOL;
} catch (PDOException $e) {
    echo "Schema error: " . $e->getMessage() . PHP_EOL;
}

if (file_exists($seedsFile)) {
    $seeds = file_get_contents($seedsFile);
    try {
        $db->exec($seeds);
        echo "Seeds applied." . PHP_EOL;
    } catch (PDOException $e) {
        echo "Seeds error: " . $e->getMessage() . PHP_EOL;
    }
} else {
    echo "Seeds file not found: {$seedsFile}" . PHP_EOL;
}

// Create Admin User from .env
$adminEmail = getenv('ADMIN_EMAIL') ?: 'admin@admin.com';
$adminPass = getenv('ADMIN_PASSWORD');

if ($adminPass) {
    echo "Creating/Updating admin user from .env..." . PHP_EOL;
    $hash = password_hash($adminPass, PASSWORD_DEFAULT);
    
    // Check if exists to update or insert
    $stmt = $db->prepare("SELECT id FROM users WHERE email = :email");
    $stmt->execute([':email' => $adminEmail]);
    
    if ($stmt->fetch()) {
        $update = $db->prepare("UPDATE users SET password = :pw, role = 'admin' WHERE email = :email");
        $update->execute([':pw' => $hash, ':email' => $adminEmail]);
    } else {
        $insert = $db->prepare("INSERT INTO users (name, email, password, role) VALUES ('Admin', :email, :pw, 'admin')");
        $insert->execute([':email' => $adminEmail, ':pw' => $hash]);
    }
    echo "Admin user processed." . PHP_EOL;
} else {
    echo "WARNING: ADMIN_PASSWORD not found in environment. Admin user skipped." . PHP_EOL;
}

echo "Database init finished." . PHP_EOL;
