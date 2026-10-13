<?php
require_once __DIR__ . '/includes/config.php';

header('Content-Type: text/plain; charset=utf-8');

try {
    $stmt = $pdo->query("SELECT DATABASE() AS db_name");
    $db = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "BASE ACTUAL: " . ($db['db_name'] ?? 'N/A') . PHP_EOL . PHP_EOL;

    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "TABLAS: " . implode(', ', $tables) . PHP_EOL . PHP_EOL;

    $stmt = $pdo->query("SELECT id, nombre, tipo, activo FROM recursos ORDER BY id");
    $recursos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($recursos)) {
        echo "NO HAY RECURSOS EN LA BASE ACTUAL." . PHP_EOL;
    } else {
        echo "RECURSOS:" . PHP_EOL;
        foreach ($recursos as $r) {
            echo "- id=" . ($r['id'] ?? 'N/A') . " | nombre=" . ($r['nombre'] ?? 'N/A') . " | tipo=" . ($r['tipo'] ?? 'N/A') . " | activo=" . ($r['activo'] ?? 'N/A') . PHP_EOL;
        }
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . PHP_EOL;
}
