<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../includes/config.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'profesor') {
    header('Location: Inicio_sesion.php');
    exit();
}

$usuarioIdSesion = (int)$_SESSION['usuario_id'];
$reservaError = $_SESSION['reserva_error'] ?? null;
unset($_SESSION['reserva_error']);

$recursosActivos = $pdo->query("SELECT id, nombre, tipo FROM recursos WHERE activo = 1 ORDER BY tipo ASC, nombre ASC")->fetchAll(PDO::FETCH_ASSOC);
$salas = [];
$equipos = [];

foreach ($recursosActivos as $recurso) {
    $tipo = strtolower((string)($recurso['tipo'] ?? ''));
    $nombre = trim((string)($recurso['nombre'] ?? ''));
    $id = (int)($recurso['id'] ?? 0);

    if ($id <= 0) {
        continue;
    }

    $item = [
        'id' => $id,
        'nombre' => $nombre !== '' ? $nombre : 'Recurso #' . $id
    ];

    if ($tipo === 'equipo') {
        $equipos[] = $item;
    } elseif (in_array($tipo, ['sala', 'laboratorio'], true)) {
        $salas[] = $item;
    }
}

$horariosValidos = [];
$inicioDia = strtotime('2026-01-01 07:00:00');
$finDia = strtotime('2026-01-01 23:55:00');
$duracionBloque = 45 * 60;

for ($ts = $inicioDia; $ts + $duracionBloque <= $finDia; $ts += $duracionBloque) {
    $inicio = date('H:i', $ts);
    $fin = date('H:i', $ts + $duracionBloque);
    $horariosValidos[] = [
        'value' => $inicio,
        'label' => $inicio . ' - ' . $fin
    ];
}

$reservasQuery = $pdo->query(
    "SELECT
        r.id,
        r.id_usuario,
        r.id_recurso,
        r.cantidad_solicitada,
        r.fecha,
        r.hora_inicio,
        r.hora_fin,
        r.motivo_profesor,
        r.estado,
        r.created_at,
        COALESCE(rec.nombre, CONCAT('Recurso #', r.id_recurso)) AS recurso_nombre,
        COALESCE(rec.tipo, 'equipo') AS recurso_tipo,
        COALESCE(u.nombre, 'Profesor') AS profesor_nombre
    FROM reservas r
    LEFT JOIN recursos rec ON rec.id = r.id_recurso
    LEFT JOIN usuarios u ON u.id = r.id_usuario
    WHERE r.estado IN ('pendiente', 'aprobada')
    ORDER BY r.fecha ASC, r.created_at ASC, r.id_usuario ASC, r.hora_inicio ASC, r.id ASC"
);

$reservas = $reservasQuery->fetchAll(PDO::FETCH_ASSOC);

$estadisticasQuery = $pdo->prepare("SELECT estado, COUNT(*) AS total FROM reservas WHERE id_usuario = ? GROUP BY estado");
$estadisticasQuery->execute([$usuarioIdSesion]);
$estadisticasReservas = [
    'pendiente' => 0,
    'aprobada' => 0,
    'cancelada' => 0,
    'rechazada' => 0,
];

foreach ($estadisticasQuery->fetchAll(PDO::FETCH_ASSOC) as $fila) {
    $estado = strtolower((string)($fila['estado'] ?? ''));
    if (isset($estadisticasReservas[$estado])) {
        $estadisticasReservas[$estado] = (int)($fila['total'] ?? 0);
    }
}

$totalSolicitadas = array_sum($estadisticasReservas);
$grupos = [];

foreach ($reservas as $fila) {
    $fecha = (string)($fila['fecha'] ?? '');
    $usuario = (int)($fila['id_usuario'] ?? 0);
    $estado = (string)($fila['estado'] ?? 'pendiente');
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
            'profesor' => (string)($fila['profesor_nombre'] ?? 'Profesor'),
            'descripcion' => trim((string)($fila['motivo_profesor'] ?? '')),
            'estado' => $estado,
            'id_usuario' => $usuario,
            'ids' => [],
            'bloques' => [],
            'horarios' => [],
            'recursos' => [],
            'sala_id' => null,
            'equipo_id' => null
        ];
    }

    if ((string)$fila['hora_inicio'] < $grupos[$claveGrupo]['hora_inicio_min']) {
        $grupos[$claveGrupo]['hora_inicio_min'] = (string)$fila['hora_inicio'];
    }

    if ((string)$fila['hora_fin'] > $grupos[$claveGrupo]['hora_fin_max']) {
        $grupos[$claveGrupo]['hora_fin_max'] = (string)$fila['hora_fin'];
    }

    $idRecurso = (int)($fila['id_recurso'] ?? 0);
    $idReserva = (int)($fila['id'] ?? 0);
    $tipoRecursoFila = strtolower((string)($fila['recurso_tipo'] ?? 'equipo'));

    if ($idReserva > 0) {
        $grupos[$claveGrupo]['ids'][] = $idReserva;
    }

    if (in_array($tipoRecursoFila, ['sala', 'laboratorio'], true) && empty($grupos[$claveGrupo]['sala_id'])) {
        $grupos[$claveGrupo]['sala_id'] = $idRecurso;
    } elseif ($tipoRecursoFila === 'equipo' && empty($grupos[$claveGrupo]['equipo_id'])) {
        $grupos[$claveGrupo]['equipo_id'] = $idRecurso;
    }

    $bloqueKey = (string)$fila['hora_inicio'] . '|' . (string)$fila['hora_fin'];
    $grupos[$claveGrupo]['bloques'][$bloqueKey] = true;
    $grupos[$claveGrupo]['horarios'][] = [
        'id' => $idReserva,
        'hora_inicio' => (string)$fila['hora_inicio'],
        'hora_fin' => (string)$fila['hora_fin'],
        'label' => substr((string)$fila['hora_inicio'], 0, 5) . ' - ' . substr((string)$fila['hora_fin'], 0, 5)
    ];

    if ($idRecurso <= 0) {
        continue;
    }

    if (!isset($grupos[$claveGrupo]['recursos'][$idRecurso])) {
        $grupos[$claveGrupo]['recursos'][$idRecurso] = [
            'nombre' => trim((string)($fila['recurso_nombre'] ?? 'Recurso')), 
            'tipo' => strtolower((string)($fila['recurso_tipo'] ?? 'equipo')),
            'cantidad' => (int)($fila['cantidad_solicitada'] ?? 1)
        ];
    } else {
        $cantidadActual = (int)$grupos[$claveGrupo]['recursos'][$idRecurso]['cantidad'];
        $cantidadFila = (int)($fila['cantidad_solicitada'] ?? 1);
        $grupos[$claveGrupo]['recursos'][$idRecurso]['cantidad'] = max($cantidadActual, $cantidadFila);
    }
}

function textoRecursos(array $recursos, array $tiposPermitidos): string
{
    $partes = [];

    foreach ($recursos as $recurso) {
        $tipo = strtolower((string)($recurso['tipo'] ?? 'equipo'));
        if (!in_array($tipo, $tiposPermitidos, true)) {
            continue;
        }

        $nombre = trim((string)($recurso['nombre'] ?? 'Recurso'));
        $cantidad = max(1, (int)($recurso['cantidad'] ?? 1));

        if ($tipo === 'equipo' && $cantidad > 1) {
            $partes[] = $nombre . ' x' . $cantidad;
        } else {
            $partes[] = $nombre;
        }
    }

    return implode(', ', $partes);
}

$actividadesPorFecha = [];
$reservasListado = [];

foreach ($grupos as $grupo) {
    $recursos = array_values($grupo['recursos']);
    $textoSalaLab = textoRecursos($recursos, ['sala', 'laboratorio']);
    $textoEquipos = textoRecursos($recursos, ['equipo']);

    if ($textoSalaLab !== '' && $textoEquipos !== '') {
        $titulo = $textoSalaLab . ' + ' . $textoEquipos;
    } elseif ($textoSalaLab !== '') {
        $titulo = $textoSalaLab;
    } elseif ($textoEquipos !== '') {
        $titulo = $textoEquipos;
    } else {
        $titulo = 'Reserva de recurso';
    }

    $descripcion = $grupo['descripcion'] !== ''
        ? $grupo['descripcion']
        : 'Sin descripción cargada por el profesor.';

    $horario = substr($grupo['hora_inicio_min'], 0, 5) . ' - ' . substr($grupo['hora_fin_max'], 0, 5);
    $cantidadClases = max(1, count($grupo['bloques']));
    $ids = array_values(array_unique(array_map('intval', $grupo['ids'])));
    $esPropia = ((int)($grupo['id_usuario'] ?? 0) === $usuarioIdSesion);
    $fecha = $grupo['fecha'];
    $tipoRecurso = 'recurso';

    if ($textoSalaLab !== '' && $textoEquipos === '') {
        $tipoRecurso = 'sala';
    } elseif ($textoEquipos !== '' && $textoSalaLab === '') {
        $tipoRecurso = 'equipo';
    } elseif ($textoSalaLab !== '' && $textoEquipos !== '') {
        $tipoRecurso = 'mixto';
    }

    $reservaBase = [
        'id' => (int)($ids[0] ?? 0),
        'fecha' => $fecha,
        'titulo' => $titulo,
        'horario' => $horario,
        'hora_inicio' => substr($grupo['hora_inicio_min'], 0, 5),
        'profesor' => $grupo['profesor'],
        'descripcion' => $descripcion,
        'recurso' => $titulo,
        'estado' => strtolower($grupo['estado']),
        'tipo_recurso' => $tipoRecurso,
        'cantidad_clases' => $cantidadClases,
        'ids' => $ids,
        'horarios' => $grupo['horarios'] ?? [],
        'es_propia' => $esPropia,
        'fecha_formateada' => date('d/m/Y', strtotime($fecha)),
        'sala_id' => $grupo['sala_id'] ?? null,
        'equipo_id' => $grupo['equipo_id'] ?? null,
        'motivo' => $descripcion
    ];

    $reservasListado[] = $reservaBase;

    if (!isset($actividadesPorFecha[$fecha])) {
        $actividadesPorFecha[$fecha] = [];
    }

    $actividadesPorFecha[$fecha][] = [
        'titulo' => $titulo,
        'horario' => $horario,
        'hora_inicio' => substr($grupo['hora_inicio_min'], 0, 5),
        'profesor' => $grupo['profesor'],
        'descripcion' => $descripcion,
        'recursos' => $titulo,
        'estado' => ucfirst($grupo['estado']),
        'cantidad_clases' => $cantidadClases,
        'ids' => $ids,
        'es_propia' => $esPropia,
        'tipo_recurso' => $tipoRecurso,
        'fecha' => $fecha,
        'sala_id' => $grupo['sala_id'] ?? null,
        'equipo_id' => $grupo['equipo_id'] ?? null,
        'motivo' => $descripcion
    ];
}

ksort($actividadesPorFecha);
usort($reservasListado, static function ($a, $b) {
    $compareFecha = strcmp($a['fecha'] ?? '', $b['fecha'] ?? '');
    if ($compareFecha !== 0) {
        return $compareFecha;
    }

    return strcmp($a['titulo'] ?? '', $b['titulo'] ?? '');
});

$fechaHoy = date('Y-m-d');
$primeraFechaConActividad = array_key_first($actividadesPorFecha);
$fechaInicial = isset($actividadesPorFecha[$fechaHoy])
    ? $fechaHoy
    : ($primeraFechaConActividad ?: $fechaHoy);

$partesFechaInicial = explode('-', $fechaInicial);
$anioInicial = (int)($partesFechaInicial[0] ?? date('Y'));
$mesInicial = max(1, (int)($partesFechaInicial[1] ?? date('n')));

$actividadesJson = json_encode($actividadesPorFecha, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
$fechaInicialJson = json_encode($fechaInicial, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
$reservasListadoJson = json_encode($reservasListado, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservas y calendario</title>
    <link rel="stylesheet" href="../css/style_reservas_admin.css">
    <link rel="stylesheet" href="../css/style_reservas_profesor.css">
    <link rel="stylesheet" href="../css/style_calendario.css">
</head>
<body>
    <?php require_once __DIR__ . '/../includes/barra_profesor.php'; ?>

    <main class="calendar-page">
        <section class="estadisticas" aria-label="Resumen de reservas">
            <div class="tarjeta">
                <span class="numero"><?php echo (int)$totalSolicitadas; ?></span>
                <span>Total solicitadas</span>
            </div>
            <div class="tarjeta confirmada">
                <span class="numero"><?php echo (int)$estadisticasReservas['aprobada']; ?></span>
                <span>Aprobadas</span>
            </div>
            <div class="tarjeta pendiente">
                <span class="numero"><?php echo (int)$estadisticasReservas['pendiente']; ?></span>
                <span>Pendientes</span>
            </div>
            <div class="tarjeta cancelada">
                <span class="numero"><?php echo (int)($estadisticasReservas['cancelada'] + $estadisticasReservas['rechazada']); ?></span>
                <span>Rechazadas/Canceladas</span>
            </div>
        </section>

        <div class="calendar-layout">
            <section class="calendar-panel">
                <div class="calendar-toolbar">
                    <button type="button" class="month-nav" id="prevMonth" aria-label="Mes anterior">‹</button>
                    <span id="monthLabel">Octubre 2026</span>
                    <div class="calendar-toolbar-actions">
                        <button type="button" class="btn-add-reserva" onclick="abrirFormularioReserva()" aria-label="Nueva reserva">
                            <span class="btn-add-icon">+</span>
                            <span class="btn-add-text">Nueva reserva</span>
                        </button>
                        <button type="button" class="month-nav" id="nextMonth" aria-label="Mes siguiente">›</button>
                    </div>
                </div>

                <div class="calendar-grid" id="calendarGrid" aria-live="polite"></div>
            </section>

            <aside class="detail-panel">
                <div class="detail-toolbar">
                    <div class="detail-toggle-switch" aria-label="Cambiar vista de reservas">
                        <span class="toggle-label">Día</span>
                        <button type="button" id="toggleAllReservations" class="detail-toggle" aria-pressed="false">
                            <span class="toggle-knob"></span>
                        </button>
                        <span class="toggle-label">Todas</span>
                        <span class="toggle-help" tabindex="0" aria-label="Ayuda sobre el interruptor de vista">?</span>
                        <span class="toggle-help-tooltip">Usá este interruptor para cambiar entre la vista del día seleccionado y la vista general con todas las reservas.</span>
                    </div>
                    <div class="detail-search-wrap">
                        <div class="search-with-help">
                            <input id="reservationSearch" type="search" placeholder="Buscar por fecha o recurso..." aria-label="Buscar reservas">
                            <span class="search-help" tabindex="0" aria-label="Ayuda sobre búsqueda de fecha">?</span>
                            <span class="search-help-tooltip">Si buscas una fecha debe estar en formato dia/mes/año, y no se buscará la fecha hasta que esté completamente escrita.</span>
                        </div>
                        <button type="button" id="openFilters" class="filter-button" aria-label="Abrir filtros">Filtros</button>
                    </div>
                </div>

                <div id="filterPopover" class="filter-popover" aria-hidden="true">
                    <div class="filter-popover-header">
                        <h3>Filtrar reservas</h3>
                        <button type="button" id="closeFilters" class="filter-close">×</button>
                    </div>
                    <div class="filter-grid">
                        <label>
                            <span>Estado</span>
                            <select id="filterEstado">
                                <option value="todos">Todos</option>
                                <option value="pendiente">Pendiente</option>
                                <option value="aprobada">Aprobada</option>
                                <option value="cancelada">Cancelada</option>
                                <option value="rechazada">Rechazada</option>
                            </select>
                        </label>
                        <label>
                            <span>Tipo de recurso</span>
                            <select id="filterTipoRecurso">
                                <option value="todos">Todos</option>
                                <option value="sala">Sala / Laboratorio</option>
                                <option value="equipo">Equipo</option>
                                <option value="mixto">Mixto</option>
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

    <div id="modalNuevaReserva" class="modal" style="display: none;">
        <div class="modal-contenido" style="padding: 28px 24px 22px; border-radius: 14px; position: relative; box-shadow: 0 18px 40px rgba(0,0,0,0.18); background: white;">
            <span class="cerrar" onclick="cerrarFormularioReserva()" style="position: absolute; right: 18px; top: 10px; font-size: 32px; line-height: 1; cursor: pointer; color: #6b7280;">×</span>
            <h2 style="margin: 0 0 8px; font-size: 1.7rem; color: #1f2937;">Solicitar Nueva Reserva</h2>
            <p class="modal-subtitulo" style="color: #666; margin-bottom: 20px; font-size: 14px;">Seleccioná qué necesitás reservar. Podés elegir uno o ambos.</p>

            <form id="formReserva" action="procesar_reserva.php" method="POST">
                <input type="hidden" name="redirect_to" value="panel_profesor.php">
                <input type="hidden" name="editar_reserva" value="0">
                <input type="hidden" name="id_reserva" value="">

                <div class="grupo-opciones" style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #e9ecef;">
                    <label class="label-opcion" style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px; cursor: pointer; font-weight: bold;">
                        <input type="checkbox" id="checkSala" onchange="toggleCamposDinamicos()" class="input-checkbox" style="width: 18px; height: 18px;">
                        Quiero reservar una Sala o Laboratorio
                    </label>
                    <label class="label-opcion" style="display: flex; align-items: center; gap: 10px; margin-bottom: 0; cursor: pointer; font-weight: bold;">
                        <input type="checkbox" id="checkEquipo" onchange="toggleCamposDinamicos()" class="input-checkbox" style="width: 18px; height: 18px;">
                        Quiero reservar Equipamiento Móvil
                    </label>
                </div>

                <div id="seccionSala" class="seccion-oculta" style="display: none; margin-bottom: 15px;">
                    <label class="label-formulario" style="display: block; margin-bottom: 5px; font-weight: bold;">Seleccionar Espacio</label>
                    <select name="sala_id" class="input-formulario" style="width: 100%; min-height: 46px; padding: 10px 42px 10px 12px; border-radius: 8px; border: 1px solid #cbd5e1; background: #fff; box-sizing: border-box;" disabled>
                        <option value="">-- Elegí un Aula o Laboratorio --</option>
                        <?php foreach ($salas as $sala): ?>
                            <?php $nombreSala = $sala['nombre'] ?? ('Recurso #' . ($sala['id'] ?? 0)); ?>
                            <option value="<?php echo (int)($sala['id'] ?? 0); ?>"><?php echo htmlspecialchars($nombreSala); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div id="seccionEquipo" class="seccion-oculta" style="display: none; margin-bottom: 15px;">
                    <label class="label-formulario" style="display: block; margin-bottom: 5px; font-weight: bold;">Seleccionar Equipamiento</label>
                    <select name="equipo_id" class="input-formulario" style="width: 100%; min-height: 46px; padding: 10px 42px 10px 12px; border-radius: 8px; border: 1px solid #cbd5e1; background: #fff; box-sizing: border-box;" disabled>
                        <option value="">-- Elegí un equipo móvil --</option>
                        <?php foreach ($equipos as $equipo): ?>
                            <?php $nombreEquipo = $equipo['nombre'] ?? ('Recurso #' . ($equipo['id'] ?? 0)); ?>
                            <option value="<?php echo (int)($equipo['id'] ?? 0); ?>"><?php echo htmlspecialchars($nombreEquipo); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="contenedor-fecha-hora" style="display: flex; gap: 15px; margin-top: 20px;">
                    <div class="columna-mitad" style="flex: 1;">
                        <label class="label-formulario" style="display: block; margin-bottom: 5px; font-weight: bold;">Fecha</label>
                        <input type="date" name="fecha" required class="input-formulario" min="<?php echo date('Y-m-d'); ?>" style="width: 100%; min-height: 46px; padding: 10px 42px 10px 12px; border-radius: 8px; border: 1px solid #cbd5e1; background: #fff; box-sizing: border-box;">
                    </div>
                    <div class="columna-mitad" style="flex: 1;">
                        <label class="label-formulario" style="display: block; margin-bottom: 5px; font-weight: bold;">Hora de inicio</label>
                        <select name="hora_inicio" required class="input-formulario" style="width: 100%; min-height: 46px; padding: 10px 42px 10px 12px; border-radius: 8px; border: 1px solid #cbd5e1; background: #fff; box-sizing: border-box;">
                            <option value="">-- Seleccionar --</option>
                            <?php foreach ($horariosValidos as $horario): ?>
                                <option value="<?php echo htmlspecialchars($horario['value']); ?>"><?php echo htmlspecialchars($horario['label']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="contenedor-fecha-hora" style="display: flex; gap: 15px; margin-top: 20px;">
                    <div class="columna-mitad" style="flex: 1;">
                        <label class="label-formulario" style="display: block; margin-bottom: 5px; font-weight: bold;">Cantidad de clases</label>
                        <select name="cantidad_clases" required class="input-formulario" style="width: 100%; min-height: 46px; padding: 10px 42px 10px 12px; border-radius: 8px; border: 1px solid #cbd5e1; background: #fff; box-sizing: border-box;">
                            <option value="1">1 clase (45 min)</option>
                            <option value="2">2 clases (1h 30 min)</option>
                            <option value="3">3 clases (2h 15 min)</option>
                            <option value="4">4 clases (3h)</option>
                            <option value="5">5 clases (3h 45 min)</option>
                            <option value="6">6 clases (4h 30 min)</option>
                            <option value="7">7 clases (5h 15 min)</option>
                            <option value="8">8 clases (6h)</option>
                            <option value="9">9 clases (6h 45 min)</option>
                            <option value="10">10 clases (7h 30 min)</option>
                        </select>
                    </div>
                    <div class="columna-mitad" style="flex: 1;">
                        <label class="label-formulario" style="display: block; margin-bottom: 5px; font-weight: bold;">Horario operativo</label>
                        <div class="input-formulario" style="display: flex; align-items: center; justify-content: center; min-height: 44px; background: #f3f5f7; color: #2c3e50; font-weight: 600; border-radius: 8px; border: 1px solid #cbd5e1; box-sizing: border-box;">
                            07:00 - 23:55
                        </div>
                    </div>
                </div>

                <div style="margin-top: 20px; margin-bottom: 20px;">
                    <label class="label-formulario" style="display: block; margin-bottom: 5px; font-weight: bold;">Razón o descripción de la reserva (opcional)</label>
                    <textarea name="motivo_profesor" class="input-formulario" style="width: 100%; resize: vertical; min-height: 80px; padding: 10px 12px; border-radius: 8px; border: 1px solid #cbd5e1; background: #fff; box-sizing: border-box;" placeholder="Explicá por qué necesitás reservar este espacio/equipamiento..."></textarea>
                </div>

                <button type="submit" class="btn-guardar btn-submit-reserva btn-deshabilitado" id="btnEnviarReserva" disabled>
                    Confirmar Solicitud
                </button>
            </form>
        </div>
    </div>

    <script>
        const activityMap = <?php echo $actividadesJson ?: '{}'; ?>;
        const allReservations = <?php echo $reservasListadoJson ?: '[]'; ?>;
        const reservaErrorInicial = <?php echo json_encode($reservaError, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;

        const monthLabel = document.getElementById('monthLabel');
        const calendarGrid = document.getElementById('calendarGrid');
        const detailHeader = document.getElementById('detailHeader');
        const detailList = document.getElementById('detailList');
        const toggleAllReservations = document.getElementById('toggleAllReservations');
        const reservationSearch = document.getElementById('reservationSearch');
        const filterPopover = document.getElementById('filterPopover');
        const openFilters = document.getElementById('openFilters');
        const closeFilters = document.getElementById('closeFilters');
        const applyFilters = document.getElementById('applyFilters');
        const clearFilters = document.getElementById('clearFilters');
        const filterEstado = document.getElementById('filterEstado');
        const filterTipoRecurso = document.getElementById('filterTipoRecurso');
        const filterFecha = document.getElementById('filterFecha');

        const calendarDate = new Date(<?php echo $anioInicial; ?>, <?php echo $mesInicial - 1; ?>, 1);
        let selectedDate = <?php echo $fechaInicialJson ?: 'null'; ?>;
        let showAllReservations = false;
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        const weekdayNames = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];
        const monthNames = [
            'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
            'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
        ];

        function escapeHtml(value) {
            return String(value ?? '')
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#39;');
        }

        function formatDateKey(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }

        function formatLongDate(dateKey) {
            const [year, month, day] = dateKey.split('-').map(Number);
            const date = new Date(year, month - 1, day);
            return date.toLocaleDateString('es-ES', {
                weekday: 'long',
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            });
        }

        function getDayBadgeState(dateKey) {
            const items = Array.isArray(activityMap[dateKey]) ? activityMap[dateKey] : [];
            const estados = items
                .map((item) => String(item.estado || '').toLowerCase())
                .filter(Boolean);

            if (!estados.length || estados.every((estado) => ['cancelada', 'rechazada'].includes(estado))) {
                return { badges: [] };
            }

            const badges = [];
            if (estados.includes('pendiente')) {
                badges.push({ label: 'Solicitud', className: 'pending' });
            }
            if (estados.includes('aprobada')) {
                badges.push({ label: 'Reservado', className: 'reserved' });
            }

            return { badges };
        }

        function getFilterState() {
            return {
                query: (reservationSearch?.value || '').trim().toLowerCase(),
                estado: filterEstado?.value || 'todos',
                tipo: filterTipoRecurso?.value || 'todos',
                fecha: filterFecha?.value || ''
            };
        }

        function matchesReservationFilters(item, filters) {
            if (!item) return false;

            const query = filters.query;
            const hayBusqueda = query !== '';

            if (hayBusqueda) {
                const dateVariants = [];
                const fechaRaw = String(item.fecha || '').trim();
                const fechaFormateada = String(item.fecha_formateada || '').trim();

                if (fechaRaw) {
                    dateVariants.push(fechaRaw);
                    dateVariants.push(fechaRaw.replace(/-/g, '/'));
                    const [year, month, day] = fechaRaw.split('-');
                    if (year && month && day) {
                        dateVariants.push(`${day}/${month}/${year}`);
                        dateVariants.push(`${day}-${month}-${year}`);
                        dateVariants.push(`${day}/${month}`);
                        dateVariants.push(`${month}/${year}`);
                        dateVariants.push(`${day}`);
                        dateVariants.push(`${month}`);
                        dateVariants.push(`${year}`);
                    }
                }

                if (fechaFormateada) {
                    dateVariants.push(fechaFormateada);
                    dateVariants.push(fechaFormateada.replace(/\//g, '-'));
                }

                const searchable = [
                    ...dateVariants,
                    item.recurso,
                    item.titulo,
                    item.profesor,
                    item.descripcion,
                    item.horario,
                    item.fecha_formateada || ''
                ].join(' ').toLowerCase();

                if (!searchable.includes(query)) {
                    return false;
                }
            }

            if (filters.estado !== 'todos' && String(item.estado || '').toLowerCase() !== String(filters.estado).toLowerCase()) {
                return false;
            }

            if (filters.tipo !== 'todos' && String(item.tipo_recurso || '').toLowerCase() !== String(filters.tipo).toLowerCase()) {
                return false;
            }

            if (filters.fecha && item.fecha !== filters.fecha) {
                return false;
            }

            return true;
        }

        function renderCalendar(targetGrid = calendarGrid) {
            targetGrid.innerHTML = '';

            weekdayNames.forEach((day) => {
                const header = document.createElement('div');
                header.className = 'weekday';
                header.textContent = day;
                targetGrid.appendChild(header);
            });

            const year = calendarDate.getFullYear();
            const month = calendarDate.getMonth();
            const firstDay = new Date(year, month, 1);
            const lastDay = new Date(year, month + 1, 0);
            const startOffset = (firstDay.getDay() + 6) % 7;
            const totalDays = lastDay.getDate();

            monthLabel.textContent = `${monthNames[month]} ${year}`;

            for (let i = 0; i < startOffset; i++) {
                const filler = document.createElement('div');
                filler.className = 'day-cell empty';
                targetGrid.appendChild(filler);
            }

            for (let day = 1; day <= totalDays; day++) {
                const date = new Date(year, month, day);
                const dateKey = formatDateKey(date);
                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'day-cell';

                const cellDate = new Date(year, month, day);
                cellDate.setHours(0, 0, 0, 0);
                const isPast = cellDate.getTime() < today.getTime();

                if (dateKey === selectedDate) {
                    button.classList.add('selected');
                }

                if (isPast) {
                    button.classList.add('past-day');
                }

                const dayItems = Array.isArray(activityMap[dateKey]) ? activityMap[dateKey] : [];
                const activeItems = dayItems.filter((item) => {
                    const estado = String(item.estado || '').toLowerCase();
                    return estado === 'pendiente' || estado === 'aprobada';
                });
                const hasPending = activeItems.some((item) => String(item.estado || '').toLowerCase() === 'pendiente');
                const hasApproved = activeItems.some((item) => String(item.estado || '').toLowerCase() === 'aprobada');
                const hasActivity = activeItems.length > 0;
                if (hasActivity) {
                    button.classList.add('has-activity');
                }
                const badges = [];
                if (hasPending) {
                    badges.push('<span class="day-badge day-badge-warning">Solicitud</span>');
                }
                if (hasApproved) {
                    badges.push('<span class="day-badge day-badge-success">Reservado</span>');
                }
                const dayLabel = hasActivity
                    ? `<span class="day-badge-stack">${badges.join('')}</span>`
                    : (isPast ? '' : '<span class="day-empty">Libre</span>');
                button.innerHTML = `
                    <span class="day-number">${day}</span>
                    ${dayLabel}
                `;

                button.addEventListener('click', () => {
                    selectedDate = dateKey;
                    showAllReservations = false;
                    updateToggleLabel();
                    renderCalendar();
                    renderDetail();
                });

                targetGrid.appendChild(button);
            }

            const totalCells = targetGrid.children.length;
            const remainingTiles = (7 - (totalCells % 7)) % 7;

            for (let i = 0; i < remainingTiles; i++) {
                const filler = document.createElement('div');
                filler.className = 'day-cell empty';
                targetGrid.appendChild(filler);
            }
        }

        function getFilteredReservations() {
            const filters = getFilterState();
            const items = showAllReservations ? allReservations : (activityMap[selectedDate] || []).map((item) => ({
                ...item,
                fecha: selectedDate,
                recurso: item.recursos || item.titulo,
                titulo: item.titulo,
                estado: String(item.estado || '').toLowerCase(),
                tipo_recurso: item.tipo_recurso || 'sala',
                fecha_formateada: formatLongDate(selectedDate)
            }));

            return items.filter((item) => matchesReservationFilters(item, filters));
        }

        function renderDetail() {
            const filters = getFilterState();
            const filteredItems = getFilteredReservations();

            if (showAllReservations) {
                detailHeader.textContent = 'Todas las reservas';
            } else {
                detailHeader.textContent = formatLongDate(selectedDate).replace(/^./, (char) => char.toUpperCase());
            }

            if (!filteredItems.length) {
                detailList.innerHTML = `
                    <div class="detail-empty">
                        <p>${showAllReservations ? 'No hay reservas que coincidan con los filtros aplicados.' : 'No hay actividades reservadas para este día.'}</p>
                    </div>
                `;
                return;
            }

            detailList.innerHTML = filteredItems.map((item) => {
                const ids = Array.isArray(item.ids) ? item.ids : [];
                const estadoTexto = String(item.estado || 'pendiente').charAt(0).toUpperCase() + String(item.estado || 'pendiente').slice(1);
                const fechaLabel = showAllReservations ? `<div class="activity-line"><span>Fecha:</span> ${escapeHtml(item.fecha_formateada || item.fecha || '')}</div>` : '';
                const recursoLabel = showAllReservations ? `<div class="activity-line"><span>Recurso:</span> ${escapeHtml(item.recurso || item.titulo || '')}</div>` : `<div class="activity-line"><span>Recurso:</span> ${escapeHtml(item.recursos || item.titulo || '')}</div>`;
                const horaInicio = item.hora_inicio || (typeof item.horario === 'string' ? item.horario.split(' - ')[0] : '');
                const salaId = item.sala_id ?? '';
                const equipoId = item.equipo_id ?? '';
                const horarios = Array.isArray(item.horarios) && item.horarios.length
                    ? item.horarios
                    : [{ label: item.horario || 'Horario no disponible', hora_inicio: horaInicio, hora_fin: '', id: ids[0] || 0 }];
                const horarioContenido = horarios.length > 1
                    ? `<details class="reservation-hours-menu" open>
                        <summary>Horarios (${horarios.length})</summary>
                        <ul>
                            ${horarios.map((bloque) => `<li><span>${escapeHtml(bloque.label || `${bloque.hora_inicio || ''} - ${bloque.hora_fin || ''}`)}</span></li>`).join('')}
                        </ul>
                    </details>`
                    : `<div class="activity-line"><span>Horario:</span> ${escapeHtml(horarios[0]?.label || item.horario || '')}</div>`;

                return `
                    <article class="activity-card">
                        <div class="activity-actions">
                            ${(item.es_propia || showAllReservations) ? `
                                <div class="actions-stack">
                                    <button type="button" class="btn-mini-editar" data-edit-ids="${escapeHtml(ids.join(','))}" data-edit-fecha="${escapeHtml(item.fecha || selectedDate)}" data-edit-hora="${escapeHtml(horaInicio)}" data-edit-cantidad="${escapeHtml(item.cantidad_clases || 1)}" data-edit-motivo="${escapeHtml(item.descripcion || item.motivo || '')}" data-edit-sala="${escapeHtml(String(salaId))}" data-edit-equipo="${escapeHtml(String(equipoId))}">Editar</button>
                                    <form class="cancel-form" action="procesar_estado_reserva.php" method="POST">
                                        <input type="hidden" name="id_reserva" value="${escapeHtml(ids.join(','))}">
                                        <input type="hidden" name="estado" value="cancelada">
                                        <input type="hidden" name="redirect_to" value="panel_profesor.php">
                                        <input type="hidden" name="motivo_cancelacion" value="Cancelada por el profesor desde calendario">
                                        <button type="submit" class="btn-mini-cancelar">Cancelar</button>
                                    </form>
                                </div>
                            ` : ''}
                        </div>
                        <div class="activity-title">${escapeHtml(item.titulo || item.recurso || 'Reserva')}</div>
                        ${recursoLabel}
                        ${fechaLabel}
                        ${horarioContenido}
                        <div class="activity-line"><span>Cantidad de clases:</span> ${escapeHtml(item.cantidad_clases || 1)}</div>
                        <div class="activity-line"><span>Profesor:</span> ${escapeHtml(item.profesor || 'Profesor')}</div>
                        <div class="activity-line"><span>Estado:</span> ${escapeHtml(estadoTexto)}</div>
                        <div class="activity-line"><span>Descripción:</span> ${escapeHtml(item.descripcion || 'Sin descripción cargada.')}</div>
                    </article>
                `;
            }).join('');

            document.querySelectorAll('.cancel-form').forEach((form) => {
                form.addEventListener('submit', (event) => {
                    const confirmar = window.confirm('¿Seguro que querés cancelar esta reserva?');
                    if (!confirmar) {
                        event.preventDefault();
                    }
                });
            });

            document.querySelectorAll('.btn-mini-editar').forEach((button) => {
                button.addEventListener('click', () => {
                    const ids = button.dataset.editIds || '';
                    const fecha = button.dataset.editFecha || selectedDate;
                    const horaInicio = button.dataset.editHora || '';
                    const cantidad = button.dataset.editCantidad || '1';
                    const motivo = button.dataset.editMotivo || '';
                    const salaId = button.dataset.editSala || '';
                    const equipoId = button.dataset.editEquipo || '';

                    prepararFormularioEdicion(ids, fecha, horaInicio, cantidad, motivo, salaId, equipoId);
                });
            });
        }

        function updateToggleLabel() {
            if (!toggleAllReservations) return;
            toggleAllReservations.classList.toggle('active', showAllReservations);
            toggleAllReservations.setAttribute('aria-pressed', String(showAllReservations));
        }

        function prepararFormularioEdicion(idsCsv, fecha, horaInicio, cantidadClases, motivo, salaId, equipoId) {
            const form = document.getElementById('formReserva');
            const modal = document.getElementById('modalNuevaReserva');
            const btnEnviar = document.getElementById('btnEnviarReserva');
            if (!form || !modal || !btnEnviar) return;

            const checkSala = document.getElementById('checkSala');
            const checkEquipo = document.getElementById('checkEquipo');

            form.querySelector('input[name="editar_reserva"]').value = '1';
            form.querySelector('input[name="id_reserva"]').value = idsCsv || '';
            form.querySelector('input[name="fecha"]').value = fecha || '';
            form.querySelector('select[name="hora_inicio"]').value = horaInicio || '';
            form.querySelector('select[name="cantidad_clases"]').value = cantidadClases || '1';
            form.querySelector('textarea[name="motivo_profesor"]').value = motivo || '';

            const salaSelect = form.querySelector('select[name="sala_id"]');
            const equipoSelect = form.querySelector('select[name="equipo_id"]');
            const tieneSala = !!(salaId && String(salaId) !== 'null' && String(salaId) !== 'undefined');
            const tieneEquipo = !!(equipoId && String(equipoId) !== 'null' && String(equipoId) !== 'undefined');

            if (checkSala) checkSala.checked = tieneSala;
            if (checkEquipo) checkEquipo.checked = tieneEquipo;

            if (salaSelect) {
                salaSelect.value = tieneSala ? String(salaId) : '';
                salaSelect.disabled = !tieneSala;
            }
            if (equipoSelect) {
                equipoSelect.value = tieneEquipo ? String(equipoId) : '';
                equipoSelect.disabled = !tieneEquipo;
            }

            toggleCamposDinamicos();
            btnEnviar.textContent = 'Guardar cambios';
            modal.style.display = 'flex';
            actualizarEstadoBotonReserva();
        }

        function toggleFilters() {
            if (!filterPopover) return;
            const isOpen = filterPopover.classList.contains('open');
            filterPopover.classList.toggle('open', !isOpen);
            filterPopover.setAttribute('aria-hidden', String(isOpen));
        }

        function clearFilterForm() {
            if (filterEstado) filterEstado.value = 'todos';
            if (filterTipoRecurso) filterTipoRecurso.value = 'todos';
            if (filterFecha) filterFecha.value = '';
            if (reservationSearch) reservationSearch.value = '';
        }

        function applyCurrentFilters() {
            renderDetail();
            toggleFilters();
        }

        document.getElementById('prevMonth').addEventListener('click', () => {
            calendarDate.setMonth(calendarDate.getMonth() - 1);
            const newDate = new Date(calendarDate.getFullYear(), calendarDate.getMonth(), 1);
            selectedDate = formatDateKey(newDate);
            renderCalendar();
            renderDetail();
        });

        document.getElementById('nextMonth').addEventListener('click', () => {
            calendarDate.setMonth(calendarDate.getMonth() + 1);
            const newDate = new Date(calendarDate.getFullYear(), calendarDate.getMonth(), 1);
            selectedDate = formatDateKey(newDate);
            renderCalendar();
            renderDetail();
        });

        if (toggleAllReservations) {
            toggleAllReservations.addEventListener('click', () => {
                showAllReservations = !showAllReservations;
                updateToggleLabel();
                renderDetail();
            });
        }

        if (reservationSearch) {
            reservationSearch.addEventListener('input', renderDetail);
        }

        if (openFilters) {
            openFilters.addEventListener('click', toggleFilters);
        }

        if (closeFilters) {
            closeFilters.addEventListener('click', toggleFilters);
        }

        if (applyFilters) {
            applyFilters.addEventListener('click', applyCurrentFilters);
        }

        if (clearFilters) {
            clearFilters.addEventListener('click', () => {
                clearFilterForm();
                renderDetail();
                toggleFilters();
            });
        }

        if (!selectedDate) {
            selectedDate = formatDateKey(new Date(calendarDate.getFullYear(), calendarDate.getMonth(), 1));
        }

        function abrirFormularioReserva() {
            const modal = document.getElementById('modalNuevaReserva');
            const form = document.getElementById('formReserva');
            const btnEnviar = document.getElementById('btnEnviarReserva');
            if (!modal || !form || !btnEnviar) return;

            form.querySelector('input[name="editar_reserva"]').value = '0';
            form.querySelector('input[name="id_reserva"]').value = '';
            modal.style.display = 'flex';
            form.reset();
            btnEnviar.textContent = 'Confirmar Solicitud';
            limpiarErrorReserva();
            toggleCamposDinamicos();
            actualizarEstadoBotonReserva();
        }

        function cerrarFormularioReserva() {
            const modal = document.getElementById('modalNuevaReserva');
            if (modal) {
                modal.style.display = 'none';
            }
        }

        function actualizarEstadoBotonReserva() {
            const btnEnviar = document.getElementById('btnEnviarReserva');
            if (!btnEnviar) return;

            const checkSala = document.getElementById('checkSala')?.checked;
            const checkEquipo = document.getElementById('checkEquipo')?.checked;
            const salaSelect = document.querySelector('select[name="sala_id"]');
            const equipoSelect = document.querySelector('select[name="equipo_id"]');
            const fechaInput = document.querySelector('input[name="fecha"]');
            const horaSelect = document.querySelector('select[name="hora_inicio"]');
            const cantidadSelect = document.querySelector('select[name="cantidad_clases"]');

            const tieneReservaSeleccionada = !!(checkSala || checkEquipo);
            const salaCompleta = !checkSala || !!(salaSelect && !salaSelect.disabled && salaSelect.value);
            const equipoCompleta = !checkEquipo || !!(equipoSelect && !equipoSelect.disabled && equipoSelect.value);
            const fechaCompleta = !!(fechaInput && fechaInput.value);
            const horaCompleta = !!(horaSelect && horaSelect.value);
            const cantidadCompleta = !!(cantidadSelect && cantidadSelect.value);

            const formValido = tieneReservaSeleccionada && salaCompleta && equipoCompleta && fechaCompleta && horaCompleta && cantidadCompleta;

            btnEnviar.disabled = !formValido;
            btnEnviar.classList.toggle('btn-deshabilitado', !formValido);
        }

        function toggleCamposDinamicos() {
            const checkSala = document.getElementById('checkSala')?.checked;
            const checkEquipo = document.getElementById('checkEquipo')?.checked;

            const seccionSala = document.getElementById('seccionSala');
            const seccionEquipo = document.getElementById('seccionEquipo');
            const salaSelect = seccionSala?.querySelector('select');
            const equipoSelect = seccionEquipo?.querySelector('select');

            if (seccionSala && salaSelect) {
                if (checkSala) {
                    seccionSala.style.display = 'block';
                    salaSelect.disabled = false;
                    salaSelect.setAttribute('required', 'true');
                } else {
                    seccionSala.style.display = 'none';
                    salaSelect.disabled = true;
                    salaSelect.removeAttribute('required');
                    salaSelect.value = '';
                }
            }

            if (seccionEquipo && equipoSelect) {
                if (checkEquipo) {
                    seccionEquipo.style.display = 'block';
                    equipoSelect.disabled = false;
                    equipoSelect.setAttribute('required', 'true');
                } else {
                    seccionEquipo.style.display = 'none';
                    equipoSelect.disabled = true;
                    equipoSelect.removeAttribute('required');
                    equipoSelect.value = '';
                }
            }

            actualizarEstadoBotonReserva();
        }

        function limpiarErrorReserva() {
            const errorBox = document.getElementById('errorReservaInline');
            if (errorBox) {
                errorBox.style.display = 'none';
                errorBox.classList.remove('error-visible');
            }
        }

        function mostrarErrorReserva(mensaje) {
            let errorBox = document.getElementById('errorReservaInline');
            if (!errorBox) {
                const botonSubmit = document.getElementById('btnEnviarReserva');
                if (!botonSubmit || !botonSubmit.parentElement) return;

                errorBox = document.createElement('div');
                errorBox.id = 'errorReservaInline';
                errorBox.className = 'error-reserva';
                errorBox.setAttribute('role', 'alert');
                errorBox.innerHTML = '<span class="error-linea"></span><span></span>';
                botonSubmit.parentElement.insertBefore(errorBox, botonSubmit);
            }

            errorBox.querySelector('span:last-child').textContent = mensaje;
            errorBox.style.display = 'flex';
            errorBox.classList.remove('error-visible');
            void errorBox.offsetWidth;
            errorBox.classList.add('error-visible');
        }

        async function enviarReservaAjax(event) {
            event.preventDefault();

            const form = document.getElementById('formReserva');
            if (!form) return;

            limpiarErrorReserva();

            const formData = new FormData(form);
            formData.append('ajax', '1');

            try {
                const response = await fetch('procesar_reserva.php', {
                    method: 'POST',
                    body: formData
                });

                const text = await response.text();
                let data;

                try {
                    data = JSON.parse(text);
                } catch (_error) {
                    throw new Error('Respuesta inválida del servidor');
                }

                if (data.success) {
                    window.location.href = 'panel_profesor.php';
                    return;
                }

                mostrarErrorReserva(data.error || 'No se pudo registrar la reserva.');
            } catch (_error) {
                mostrarErrorReserva('Error al procesar la solicitud. Intenta de nuevo.');
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('formReserva');
            if (form) {
                form.addEventListener('submit', enviarReservaAjax);
                form.addEventListener('input', actualizarEstadoBotonReserva);
                form.addEventListener('change', actualizarEstadoBotonReserva);
            }

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') {
                    cerrarFormularioReserva();
                    if (filterPopover && filterPopover.classList.contains('open')) {
                        toggleFilters();
                    }
                }
            });

            const modal = document.getElementById('modalNuevaReserva');
            if (modal) {
                modal.addEventListener('click', (event) => {
                    if (event.target === modal) {
                        cerrarFormularioReserva();
                    }
                });
            }

            if (reservaErrorInicial) {
                abrirFormularioReserva();
                mostrarErrorReserva(reservaErrorInicial);
            }

            updateToggleLabel();
            actualizarEstadoBotonReserva();
        });

        renderCalendar();
        renderDetail();
    </script>
</body>
</html>