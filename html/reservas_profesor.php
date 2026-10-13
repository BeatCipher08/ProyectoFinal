<?php
// Forzar a PHP a mostrar el error real en pantalla (puedes quitarlo cuando termines el proyecto)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../includes/config.php';

// Validar seguridad antes de cargar nada
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'profesor') {
    header("Location: Inicio_sesion.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];
$reservaError = $_SESSION['reserva_error'] ?? null;
unset($_SESSION['reserva_error']);

// 1. Obtener recursos activos desde la base real y normalizarlos para evitar filas vacías o sin nombre.
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

// 2. Generar todos los horarios válidos en intervalos de 45 minutos desde 07:00 hasta 23:55
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

// 3. Obtener todas las reservas de este profesor desde la base de datos
$queryReservas = $pdo->prepare("
    SELECT
        r.id,
        r.fecha,
        r.hora_inicio,
        r.hora_fin,
        r.estado,
        COALESCE(rec.nombre, 'Recurso eliminado') AS recurso_nombre,
        COALESCE(rec.tipo, 'sala') AS recurso_tipo
    FROM reservas r
    LEFT JOIN recursos rec ON r.id_recurso = rec.id
    WHERE r.id_usuario = ?
      AND r.estado IN ('pendiente', 'aprobada')
    ORDER BY r.fecha ASC, r.hora_inicio ASC
");
$queryReservas->execute([$usuario_id]);
$reservasRaw = $queryReservas->fetchAll(PDO::FETCH_ASSOC);

// 3. Agrupar reservas dobles (sala + equipo) por fecha y horario.
// Esto evita filas duplicadas para la misma solicitud cuando el usuario
// reserva tanto un espacio como un recurso asociado en el mismo bloque.
$reservasAgrupadas = [];
$total_solicitadas = 0; $aprobadas = 0; $pendientes = 0; $canceladas = 0;

foreach ($reservasRaw as $res) {
    $fecha = $res['fecha'] ?? '';
    $horaInicio = substr((string)($res['hora_inicio'] ?? '00:00'), 0, 5);
    $horaFin = substr((string)($res['hora_fin'] ?? '00:00'), 0, 5);
    $estado = $res['estado'] ?? 'pendiente';
    $grupoKey = $fecha . '|' . $horaInicio . '|' . $horaFin . '|' . $estado;

    if (!isset($reservasAgrupadas[$grupoKey])) {
        $reservasAgrupadas[$grupoKey] = [
            'ids' => [],
            'fecha' => $fecha,
            'horario' => $horaInicio . ' - ' . $horaFin,
            'estado' => $estado,
            'sala' => null,
            'equipo' => null,
        ];

        $total_solicitadas++;
        if ($estado == 'aprobada') {
            $aprobadas++;
        } elseif ($estado == 'pendiente') {
            $pendientes++;
        } else {
            $canceladas++;
        }
    }

    $idReserva = (int)($res['id'] ?? 0);
    if ($idReserva > 0) {
        $reservasAgrupadas[$grupoKey]['ids'][] = $idReserva;
    }

    $tipo = strtolower((string)($res['recurso_tipo'] ?? 'sala'));
    $nombreRecurso = trim((string)($res['recurso_nombre'] ?? ''));
    $nombreVisible = $nombreRecurso !== '' ? $nombreRecurso : 'Recurso #' . $idReserva;

    if ($tipo === 'equipo') {
        $reservasAgrupadas[$grupoKey]['equipo'] = $nombreVisible;
    } else {
        $reservasAgrupadas[$grupoKey]['sala'] = $nombreVisible;
    }
}

$reservasAgrupadas = array_values($reservasAgrupadas);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Reservas - Profesor</title>
    <!-- Referencias a tus CSS separados -->
    <link rel="stylesheet" href="../css/style_reservas_admin.css">
    <link rel="stylesheet" href="../css/style_reservas_profesor.css">
</head>
<body>

    <!-- Llamada a la barra dinámica en el body -->
    <?php require_once __DIR__ . '/../includes/barra_profesor.php'; ?>

    <main class="contenedor">
        <section class="encabezado">
            <div>
                <h1>Mis Reservas</h1>
                <p>Gestioná tus solicitudes de salas, laboratorios y equipamiento.</p>
            </div>
            <button class="btn-agregar" onclick="abrirFormularioReserva()">+ Nueva reserva</button>
        </section>

        <!-- Estadísticas generadas con PHP -->
        <section class="estadisticas">
            <div class="tarjeta"><span class="numero"><?php echo $total_solicitadas; ?></span><span>Total solicitadas</span></div>
            <div class="tarjeta confirmada"><span class="numero"><?php echo $aprobadas; ?></span><span>Aprobadas</span></div>
            <div class="tarjeta pendiente"><span class="numero"><?php echo $pendientes; ?></span><span>Pendientes</span></div>
            <div class="tarjeta cancelada"><span class="numero"><?php echo $canceladas; ?></span><span>Rechazadas/Canceladas</span></div>
        </section>

        <section class="panel">
            <div class="herramientas">
                <input type="text" placeholder="Buscar por fecha o sala..." class="input-busqueda">
                <select class="select-filtro">
                    <option value="todos">Todos los estados</option>
                    <option value="Confirmada">Aprobadas</option>
                    <option value="Pendiente">Pendientes</option>
                </select>
            </div>

            <div class="tabla-contenedor">
                <table>
                    <thead>
                        <tr>
                            <th>Espacio (Sala/Lab)</th>
                            <th>Equipamiento Vinculado</th>
                            <th>Fecha</th>
                            <th>Horario</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($reservasAgrupadas)): ?>
                            <tr><td colspan="6" style="text-align: center; padding: 20px;">No tienes reservas registradas.</td></tr>
                        <?php else: ?>
                            <?php foreach ($reservasAgrupadas as $reserva): ?>
                                <?php 
                                    $fechaFormateada = date("d/m/Y", strtotime($reserva['fecha']));
                                    $estadoClase = strtolower($reserva['estado']);
                                ?>
                                <tr>
                                    <td>
                                        <?php if ($reserva['sala']): ?>
                                            <strong><?php echo htmlspecialchars($reserva['sala']); ?></strong>
                                        <?php else: ?>
                                            <span class="texto-vacio">Uso externo / Ninguno</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($reserva['equipo']): ?>
                                            <strong><?php echo htmlspecialchars($reserva['equipo']); ?></strong>
                                        <?php else: ?>
                                            <span class="texto-vacio">Ninguno</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo $fechaFormateada; ?></td>
                                    <td><?php echo htmlspecialchars($reserva['horario']); ?></td>
                                    <td><span class="estado <?php echo $estadoClase; ?>"><?php echo ucfirst($reserva['estado']); ?></span></td>
                                    <td class="acciones">
                                        <?php if ($estadoClase === 'pendiente' || $estadoClase === 'confirmada' || $estadoClase === 'aprobada'): ?>
                                            <form action="procesar_estado_reserva.php" method="POST" onsubmit="return confirm('¿Seguro que querés cancelar esta reserva?');">
                                                <input type="hidden" name="id_reserva" value="<?php echo htmlspecialchars(implode(',', $reserva['ids'])); ?>">
                                                <input type="hidden" name="estado" value="cancelada">
                                                <button type="submit" class="btn-cancelar">Cancelar</button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <!-- Modal Dinámico (Conectado a la base de datos) -->
    <div id="modalNuevaReserva" class="modal">
        <div class="modal-contenido">
            <span class="cerrar" onclick="cerrarFormularioReserva()">×</span>
            <h2>Solicitar Nueva Reserva</h2>
            <p class="modal-subtitulo">Seleccioná qué necesitás reservar. Podés elegir uno o ambos.</p>
            
            <form id="formReserva" action="procesar_reserva.php" method="POST">
                
                <div class="grupo-opciones">
                    <label class="label-opcion">
                        <input type="checkbox" id="checkSala" onchange="toggleCamposDinámicos()" class="input-checkbox">
                        🏫 Quiero reservar una Sala o Laboratorio
                    </label>
                    <label class="label-opcion">
                        <input type="checkbox" id="checkEquipo" onchange="toggleCamposDinámicos()" class="input-checkbox">
                        💻 Quiero reservar Equipamiento Móvil (Ej: Proyector)
                    </label>
                </div>

                <!-- Selectores llenados por PHP -->
                <div id="seccionSala" class="seccion-oculta">
                    <label class="label-formulario">Seleccionar Espacio</label>
                    <select name="sala_id" class="input-formulario">
                        <option value="">-- Elegí un Aula o Laboratorio --</option>
                        <?php foreach ($salas as $sala): ?>
                            <?php $nombreSala = $sala['nombre'] ?? ('Recurso #' . ($sala['id'] ?? 0)); ?>
                            <option value="<?php echo (int)($sala['id'] ?? 0); ?>"><?php echo htmlspecialchars($nombreSala); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div id="seccionEquipo" class="seccion-oculta">
                    <label class="label-formulario">Seleccionar Equipamiento</label>
                    <select name="equipo_id" class="input-formulario">
                        <option value="">-- Elegí un equipo móvil --</option>
                        <?php foreach ($equipos as $equipo): ?>
                            <?php $nombreEquipo = $equipo['nombre'] ?? ('Recurso #' . ($equipo['id'] ?? 0)); ?>
                            <option value="<?php echo (int)($equipo['id'] ?? 0); ?>"><?php echo htmlspecialchars($nombreEquipo); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="contenedor-fecha-hora">
                    <div class="columna-mitad">
                        <label class="label-formulario">Fecha</label>
                        <input type="date" name="fecha" required class="input-formulario" min="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <div class="columna-mitad">
                        <label class="label-formulario">Hora de inicio</label>
                        <select name="hora_inicio" required class="input-formulario">
                            <option value="">-- Seleccionar --</option>
                            <?php foreach ($horariosValidos as $horario): ?>
                                <option value="<?php echo htmlspecialchars($horario['value']); ?>"><?php echo htmlspecialchars($horario['label']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="contenedor-fecha-hora">
                    <div class="columna-mitad">
                        <label class="label-formulario">Cantidad de clases</label>
                        <select name="cantidad_clases" required class="input-formulario">
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
                    <div class="columna-mitad">
                        <label class="label-formulario">Horario operativo</label>
                        <div class="input-formulario" style="display: flex; align-items: center; justify-content: center; min-height: 44px; background: #f3f5f7; color: #2c3e50; font-weight: 600;">
                            07:00 - 23:55
                        </div>
                    </div>
                </div>

                <div class="contenedor-fecha-hora">
                    <label class="label-formulario">Razón o descripción de la reserva (opcional)</label>
                    <textarea name="motivo_profesor" class="input-formulario" style="resize: vertical; min-height: 80px;" placeholder="Explicá por qué necesitás reservar este espacio/equipamiento..."></textarea>
                </div>

                <button type="submit" class="btn-guardar btn-submit-reserva btn-deshabilitado" id="btnEnviarReserva" disabled>
                    Confirmar Solicitud
                </button>
            </form>
        </div>
    </div>

    <script src="../scripts/reservas_profesor.js"></script>
</body>
</html>