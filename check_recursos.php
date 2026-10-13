<?php
require_once __DIR__ . '/includes/config.php';

header('Content-Type: text/plain; charset=utf-8');

try {
    $stmt = $pdo->query("SELECT id, nombre, tipo, activo FROM recursos WHERE activo = 1 ORDER BY tipo, nombre");
    $recursos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "RECURSOS ACTIVOS:" . PHP_EOL;

    if (empty($recursos)) {
        echo "- Ninguno" . PHP_EOL;
    } else {
        foreach ($recursos as $r) {
            echo "- id=" . ($r['id'] ?? 'N/A') . " | nombre='" . ($r['nombre'] ?? 'N/A') . "' | tipo=" . ($r['tipo'] ?? 'N/A') . PHP_EOL;
        }
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . PHP_EOL;
}
