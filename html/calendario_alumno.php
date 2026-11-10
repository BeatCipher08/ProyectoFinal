<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../includes/config.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'alumno') {
    header("Location: Inicio_sesion.php");
    exit();
}

$usuario_id = (int) $_SESSION['usuario_id'];

$stmtUsuario = $pdo->prepare("SELECT id, nombre, rol FROM usuarios WHERE id = ? LIMIT 1");
$stmtUsuario->execute([$usuario_id]);
$usuarioActual = $stmtUsuario->fetch(PDO::FETCH_ASSOC);

if (!$usuarioActual || strtolower((string)($usuarioActual['rol'] ?? '')) !== 'alumno') {
    session_unset();
    session_destroy();
    header("Location: Inicio_sesion.php");
    exit();
}

$_SESSION['nombre'] = $_SESSION['nombre'] ?? (string)($usuarioActual['nombre'] ?? 'Alumno');

$reservasStmt = $pdo->prepare(
    "SELECT
        r.id,
        r.id_usuario,
        r.fecha,
        r.hora_inicio,
        r.hora_fin,
        r.motivo_profesor,
        r.estado,
        r.created_at,
        COALESCE(u.nombre, 'Profesor') AS profesor_nombre,
        COALESCE(rec.nombre, CONCAT('Recurso #', r.id_recurso)) AS recurso_nombre,
        COALESCE(rec.tipo, 'recurso') AS recurso_tipo
    FROM reservas r
    LEFT JOIN usuarios u ON u.id = r.id_usuario
    LEFT JOIN recursos rec ON rec.id = r.id_recurso
    WHERE LOWER(COALESCE(u.rol, '')) = 'profesor'
      AND LOWER(COALESCE(r.estado, '')) IN ('pendiente', 'aprobada')
    ORDER BY r.fecha ASC, r.hora_inicio ASC"
);
$reservasStmt->execute();
$reservasProfesor = $reservasStmt->fetchAll(PDO::FETCH_ASSOC);

$grupos = [];
foreach ($reservasProfesor as $fila) {
    $fecha = (string)($fila['fecha'] ?? '');
    $usuario = (int)($fila['id_usuario'] ?? 0);
    $estado = strtolower((string)($fila['estado'] ?? 'pendiente'));
    $createdAt = (string)($fila['created_at'] ?? '');

    if ($fecha === '' || $usuario <= 0 || $createdAt === '') {
        continue;
    }

    $claveGrupo = $fecha . '|' . $usuario . '|' . $estado . '|' . $createdAt;

    if (!isset($grupos[$claveGrupo])) {
        $grupos[$claveGrupo] = [
            'fecha' => $fecha,
            'hora_inicio_min' => (string)$fila['hora_inicio'],
            'hora_fin_max' => (string)$fila['hora_fin'],
            'profesor' => trim((string)($fila['profesor_nombre'] ?? 'Profesor')),
            'descripcion' => trim((string)($fila['motivo_profesor'] ?? '')),
            'estado' => $estado,
            'bloques' => [],
            'recursos' => []
        ];
    }

    if ((string)$fila['hora_inicio'] < $grupos[$claveGrupo]['hora_inicio_min']) {
        $grupos[$claveGrupo]['hora_inicio_min'] = (string)$fila['hora_inicio'];
    }

    if ((string)$fila['hora_fin'] > $grupos[$claveGrupo]['hora_fin_max']) {
        $grupos[$claveGrupo]['hora_fin_max'] = (string)$fila['hora_fin'];
    }

    $bloqueKey = (string)$fila['hora_inicio'] . '|' . (string)$fila['hora_fin'];
    $grupos[$claveGrupo]['bloques'][$bloqueKey] = true;

    $nombreRecurso = trim((string)($fila['recurso_nombre'] ?? 'Recurso'));
    $tipoRecurso = strtolower((string)($fila['recurso_tipo'] ?? 'recurso'));
    if ($nombreRecurso !== '') {
        $grupos[$claveGrupo]['recursos'][] = [
            'nombre' => $nombreRecurso,
            'tipo' => $tipoRecurso
        ];
    }
}

function textoRecursosAlumno(array $recursos, array $tiposPermitidos): string
{
    $partes = [];

    foreach ($recursos as $recurso) {
        $tipo = strtolower((string)($recurso['tipo'] ?? 'recurso'));
        if (!in_array($tipo, $tiposPermitidos, true)) {
            continue;
        }

        $nombre = trim((string)($recurso['nombre'] ?? 'Recurso'));
        if ($nombre !== '' && !in_array($nombre, $partes, true)) {
            $partes[] = $nombre;
        }
    }

    return implode(', ', $partes);
}

$actividadesPorFecha = [];
foreach ($grupos as $grupo) {
    $recursos = $grupo['recursos'];
    $textoSalaLab = textoRecursosAlumno($recursos, ['sala', 'laboratorio']);
    $textoEquipos = textoRecursosAlumno($recursos, ['equipo']);

    if ($textoSalaLab !== '' && $textoEquipos !== '') {
        $titulo = $textoSalaLab . ' + ' . $textoEquipos;
    } elseif ($textoSalaLab !== '') {
        $titulo = $textoSalaLab;
    } elseif ($textoEquipos !== '') {
        $titulo = $textoEquipos;
    } else {
        $titulo = 'Reserva de recurso';
    }

    $tipoRecurso = 'recurso';
    if ($textoSalaLab !== '' && $textoEquipos === '') {
        $tipoRecurso = 'sala';
    } elseif ($textoEquipos !== '' && $textoSalaLab === '') {
        $tipoRecurso = 'equipo';
    } elseif ($textoSalaLab !== '' && $textoEquipos !== '') {
        $tipoRecurso = 'mixto';
    }

    $fecha = $grupo['fecha'];
    if (!isset($actividadesPorFecha[$fecha])) {
        $actividadesPorFecha[$fecha] = [];
    }

    $cantidadClases = max(1, count($grupo['bloques']));
    $descripcion = $grupo['descripcion'] !== ''
        ? $grupo['descripcion']
        : 'Reserva realizada por ' . ($grupo['profesor'] !== '' ? $grupo['profesor'] : 'Profesor') . '.';

    $actividadesPorFecha[$fecha][] = [
        'titulo' => $titulo,
        'recurso' => $titulo,
        'horario' => substr($grupo['hora_inicio_min'], 0, 5) . ' - ' . substr($grupo['hora_fin_max'], 0, 5),
        'descripcion' => $descripcion,
        'profesor' => $grupo['profesor'] !== '' ? $grupo['profesor'] : 'Profesor',
        'estado' => strtolower($grupo['estado']),
        'tipo_recurso' => $tipoRecurso,
        'cantidad_clases' => $cantidadClases
    ];
}

ksort($actividadesPorFecha);

$fechaHoy = date('Y-m-d');
$fechaInicial = $fechaHoy;
$anioInicial = (int)date('Y');
$mesInicial = (int)date('n');

$actividadesJson = json_encode($actividadesPorFecha, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
$fechaInicialJson = json_encode($fechaInicial, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendario del alumno</title>
    <link rel="stylesheet" href="../css/style_calendario.css">
</head>
<body>
    <?php require_once __DIR__ . '/../includes/barra_alumno.php'; ?>

    <main class="calendar-page">
        <div class="calendar-layout">
            <section class="calendar-panel">
                <div class="calendar-toolbar">
                    <button type="button" class="month-nav" id="prevMonth" aria-label="Mes anterior">‹</button>
                    <span id="monthLabel">Agosto 2026</span>
                    <div class="calendar-toolbar-actions">
                        <button type="button" class="month-nav" id="nextMonth" aria-label="Mes siguiente">›</button>
                    </div>
                </div>

                <div class="calendar-grid" id="calendarGrid" aria-live="polite"></div>
            </section>

            <aside class="detail-panel">
                <div class="detail-toolbar">
                    <div class="detail-toggle-switch" aria-label="Vista de calendario del alumno">
                        <span class="toggle-label">Día</span>
                        <button type="button" id="toggleAllReservations" class="detail-toggle" aria-pressed="false">
                            <span class="toggle-knob"></span>
                        </button>
                        <span class="toggle-label">Todo</span>
                    </div>

                    <div class="detail-search-wrap">
                        <div class="search-with-help">
                            <input id="reservationSearch" type="search" placeholder="Buscar por fecha o recurso..." aria-label="Buscar reservas">
                            <span class="search-help" tabindex="0" aria-label="Ayuda sobre búsqueda">?</span>
                            <span class="search-help-tooltip">Buscá por fecha (dd/mm/aaaa), recurso, profesor o descripción.</span>
                        </div>
                        <button type="button" id="openFilters" class="filter-button" aria-label="Abrir filtros">Filtros</button>
                    </div>
                </div>

                <div id="filterPopover" class="filter-popover" aria-hidden="true">
                    <div class="filter-popover-header">
                        <h3>Filtrar reservas</h3>
                        <button type="button" id="closeFilters" class="filter-close" aria-label="Cerrar filtros">×</button>
                    </div>
                    <div class="filter-grid">
                        <label>
                            <span>Estado</span>
                            <select id="filterEstado">
                                <option value="todos">Todos</option>
                                <option value="pendiente">Pendiente</option>
                                <option value="aprobada">Aprobada</option>
                            </select>
                        </label>
                        <label>
                            <span>Tipo de recurso</span>
                            <select id="filterTipoRecurso">
                                <option value="todos">Todos</option>
                                <option value="sala">Sala</option>
                                <option value="laboratorio">Laboratorio</option>
                                <option value="equipo">Equipo</option>
                            </select>
                        </label>
                        <label>
                            <span>Fecha específica</span>
                            <input id="filterFecha" type="date">
                        </label>
                    </div>
                    <div class="filter-actions">
                        <button type="button" id="clearFilters" class="filter-secondary">Limpiar</button>
                        <button type="button" id="applyFilters" class="filter-primary">Aplicar</button>
                    </div>
                </div>

                <div class="detail-header" id="detailHeader">Fecha y día</div>
                <div class="detail-list" id="detailList"></div>
            </aside>
        </div>
    </main>

    <script>
        window.activityMap = <?php echo $actividadesJson ?: '{}'; ?>;
        window.calendarInitialDate = <?php echo $fechaInicialJson ?: 'null'; ?>;
        window.calendarInitialYear = <?php echo (int)$anioInicial; ?>;
        window.calendarInitialMonth = <?php echo (int)($mesInicial - 1); ?>;
    </script>
    <script src="../scripts/calendario.js"></script>
</body>
</html>
