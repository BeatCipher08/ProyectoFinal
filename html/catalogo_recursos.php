<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario_id'])) {
    header("Location: Inicio_sesion.php");
    exit();
}

require_once __DIR__ . '/../includes/config.php';

$fechaActual = date('Y-m-d');
$horaActual = date('H:i:s');

$recursos = $pdo->query(
    "SELECT * FROM recursos WHERE activo = 1 AND tipo = 'equipo' ORDER BY nombre ASC"
)->fetchAll(PDO::FETCH_ASSOC);

$ocupacionesStmt = $pdo->prepare(
    "SELECT r.id_recurso,
            SUM(r.cantidad_solicitada) AS usados,
            GROUP_CONCAT(CONCAT(u.nombre, '||', r.hora_inicio, '||', r.hora_fin, '||', COALESCE(r.motivo_profesor, '')) SEPARATOR ';;') AS detalle
     FROM reservas r
     LEFT JOIN usuarios u ON u.id = r.id_usuario
     WHERE r.estado = 'aprobada'
       AND r.fecha = :fecha
       AND :hora >= r.hora_inicio
       AND :hora < r.hora_fin
     GROUP BY r.id_recurso"
);
$ocupacionesStmt->execute([
    ':fecha' => $fechaActual,
    ':hora' => $horaActual,
]);

$ocupaciones = [];
while ($fila = $ocupacionesStmt->fetch(PDO::FETCH_ASSOC)) {
    $ocupaciones[(int)$fila['id_recurso']] = $fila;
}

function obtenerTextoEstado(string $estado): string
{
    $texto = [
        'libre' => 'Disponible',
        'parcial' => 'Parcialmente ocupado',
        'ocupado' => 'Ocupado',
    ];

    return $texto[$estado] ?? 'Disponible';
}

function construirTooltip(array $detalle): string
{
    if (empty($detalle)) {
        return '<strong>Disponible</strong><br>Sin reservas activas en este momento.';
    }

    $bloques = [];
    foreach ($detalle as $item) {
        $usuario = htmlspecialchars($item['usuario'] ?? 'Usuario');
        $inicio = htmlspecialchars($item['inicio'] ?? '');
        $fin = htmlspecialchars($item['fin'] ?? '');
        $motivo = htmlspecialchars(trim((string)($item['motivo'] ?? '')));
        $bloque = '<div><strong>' . $usuario . '</strong><br>Horario: ' . $inicio . ' a ' . $fin;
        if ($motivo !== '') {
            $bloque .= '<br>Motivo: ' . $motivo;
        }
        $bloque .= '</div>';
        $bloques[] = $bloque;
    }

    $resumen = implode('<br>', $bloques);
    return $resumen !== '' ? $resumen : '<strong>Uso actual</strong><br>Sin información disponible.';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo de equipos</title>
    <link rel="stylesheet" href="../css/style_panel_alumno.css">
    <link rel="stylesheet" href="../css/style_catalogo_recursos.css">
</head>
<body>
    <?php require_once __DIR__ . '/../includes/barra_alumno.php'; ?>

    <main class="catalogo-page">
        <header class="catalogo-header">
            <div>
                <h1>Catálogo de equipos</h1>
                <p>Consulta el inventario disponible para reservar en el instituto.</p>
            </div>
        </header>

        <section class="catalogo-shell">
            <div class="catalogo-tabs" aria-label="Pestañas del catálogo">
                <button class="catalogo-tab active" type="button" data-target="equipos">Equipos</button>
            </div>

            <div class="resource-grid">
                <?php foreach ($recursos as $recurso): ?>
                    <?php
                    $idRecurso = (int) $recurso['id'];
                    $cantidadTotal = max(1, (int) $recurso['cantidad_total']);
                    $usadoActual = 0;
                    $detalleUso = [];

                    if (isset($ocupaciones[$idRecurso])) {
                        $usadoActual = (int) ($ocupaciones[$idRecurso]['usados'] ?? 0);
                        $segmentos = explode(';;', (string) ($ocupaciones[$idRecurso]['detalle'] ?? ''));

                        foreach ($segmentos as $segmento) {
                            if ($segmento === '') {
                                continue;
                            }

                            $partes = explode('||', $segmento);
                            if (count($partes) < 4) {
                                continue;
                            }

                            $detalleUso[] = [
                                'usuario' => $partes[0],
                                'inicio' => $partes[1],
                                'fin' => $partes[2],
                                'motivo' => $partes[3],
                            ];
                        }
                    }

                    if ($usadoActual >= $cantidadTotal) {
                        $estado = 'ocupado';
                    } elseif ($usadoActual > 0) {
                        $estado = 'parcial';
                    } else {
                        $estado = 'libre';
                    }

                    $porcentajeUso = min(($usadoActual / $cantidadTotal) * 100, 100);
                    $textoEstado = obtenerTextoEstado($estado);
                    $tooltipHtml = construirTooltip($detalleUso);
                    ?>
                    <article class="resource-card" data-category="equipos" data-state="<?php echo htmlspecialchars($estado); ?>">
                        <div class="resource-head">
                            <div class="resource-icon">💻</div>
                            <div>
                                <h3><?php echo htmlspecialchars($recurso['nombre']); ?></h3>
                                <span class="resource-kind">Equipo</span>
                            </div>
                            <span class="status-indicator <?php echo htmlspecialchars($estado); ?>" tabindex="0" aria-label="Estado de uso">
                                <span class="status-dot"></span>
                                <?php echo htmlspecialchars($textoEstado); ?>
                                <span class="status-tooltip"><?php echo $tooltipHtml; ?></span>
                            </span>
                        </div>

                        <p class="resource-description"><?php echo htmlspecialchars($recurso['descripcion'] ?? 'Sin descripción disponible.'); ?></p>

                        <div class="stock-row">
                            <span>Stock usado</span>
                            <span><?php echo $usadoActual; ?> / <?php echo $cantidadTotal; ?></span>
                        </div>

                        <div class="stock-bar" aria-label="Porcentaje de uso actual">
                            <span style="width: <?php echo number_format($porcentajeUso, 2, '.', ''); ?>%;"></span>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>

            <div class="empty-state visible" data-category="equipos">
                No hay equipos disponibles para reservar en este momento.
            </div>
        </section>
    </main>

    <script src="../scripts/catalogo_recursos.js"></script>
</body>
</html>
