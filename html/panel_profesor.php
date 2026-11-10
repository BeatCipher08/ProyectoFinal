<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../includes/config.php';

// Validación de seguridad directa (sin el ../)
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'profesor') {
    header("Location: Inicio_sesion.php");
    exit();
}

// Obtenemos el nombre de la sesión
$nombre_profesor = $_SESSION['nombre'] ?? 'Profesor';
$usuario_id = $_SESSION['usuario_id'];
$iniciales = strtoupper(substr($nombre_profesor, 0, 2));

$estadisticasReservas = [
    'pendiente' => 0,
    'aprobada' => 0,
    'cancelada' => 0,
    'rechazada' => 0,
];

$estadisticasStmt = $pdo->prepare("SELECT estado, COUNT(*) AS total FROM reservas WHERE id_usuario = ? GROUP BY estado");
$estadisticasStmt->execute([$usuario_id]);
foreach ($estadisticasStmt->fetchAll(PDO::FETCH_ASSOC) as $fila) {
    $estado = strtolower((string)($fila['estado'] ?? ''));
    if (isset($estadisticasReservas[$estado])) {
        $estadisticasReservas[$estado] = (int)($fila['total'] ?? 0);
    }
}

$ultimasReservasStmt = $pdo->prepare("
    SELECT
        r.id,
        r.id_usuario AS profesor_id,
        r.fecha,
        r.hora_inicio,
        r.hora_fin,
        r.estado,
        COALESCE(rec.nombre, 'Recurso eliminado') AS recurso_nombre,
        COALESCE(rec.tipo, 'sala') AS recurso_tipo,
        COALESCE(u.nombre, 'Profesor') AS profesor_nombre,
        CASE WHEN r.id_usuario = ? THEN 1 ELSE 0 END AS es_propia
    FROM reservas r
    LEFT JOIN recursos rec ON rec.id = r.id_recurso
    LEFT JOIN usuarios u ON u.id = r.id_usuario
    WHERE r.estado IN ('pendiente', 'aprobada', 'cancelada', 'rechazada')
    ORDER BY r.fecha DESC, r.hora_inicio DESC, r.id DESC
    LIMIT 12
");
$ultimasReservasStmt->execute([$usuario_id]);
$ultimasReservas = $ultimasReservasStmt->fetchAll(PDO::FETCH_ASSOC);

$fechaHoy = date('Y-m-d');
$agendaHoyStmt = $pdo->prepare(
    "SELECT
        r.fecha,
        r.hora_inicio,
        r.hora_fin,
        COALESCE(rec.nombre, 'Recurso eliminado') AS recurso_nombre
    FROM reservas r
    LEFT JOIN recursos rec ON rec.id = r.id_recurso
    WHERE r.id_usuario = ?
      AND r.estado = 'aprobada'
      AND r.fecha = ?
    ORDER BY r.hora_inicio ASC, r.id ASC"
);
$agendaHoyStmt->execute([(int)$usuario_id, $fechaHoy]);
$agendaHoy = $agendaHoyStmt->fetchAll(PDO::FETCH_ASSOC);

$mesesCortos = [
    '01' => 'ENE',
    '02' => 'FEB',
    '03' => 'MAR',
    '04' => 'ABR',
    '05' => 'MAY',
    '06' => 'JUN',
    '07' => 'JUL',
    '08' => 'AGO',
    '09' => 'SEP',
    '10' => 'OCT',
    '11' => 'NOV',
    '12' => 'DIC',
];

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
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel del Profesor</title>
    <link rel="stylesheet" href="../css/style_panel_profesor.css">
    <!-- Asumiendo que tu barra tiene estos estilos guardados aquí -->
    <link rel="stylesheet" href="../css/style_reservas_profesor.css">
</head>
<body>

    <!-- Incluimos la barra dinámica -->
    <?php require_once __DIR__ . '/../includes/barra_profesor.php'; ?>

    <main class="contenedor">
        <section class="bienvenida">
            <div>
                <h1>Panel del profesor</h1>
                <p>Visualizá el estado de tus reservas y gestioná tus actividades.</p>
            </div>
        </section>

        <section class="estadisticas">
            <div class="tarjeta">
                <div class="icono">📅</div>
                <div><span class="numero"><?php echo (int)array_sum($estadisticasReservas); ?></span><p>Mis reservas</p></div>
            </div>
            <div class="tarjeta">
                <div class="icono">⏳</div>
                <div><span class="numero"><?php echo (int)$estadisticasReservas['pendiente']; ?></span><p>Reservas pendientes</p></div>
            </div>
            <div class="tarjeta">
                <div class="icono">✅</div>
                <div><span class="numero"><?php echo (int)$estadisticasReservas['aprobada']; ?></span><p>Reservas aprobadas</p></div>
            </div>
            <div class="tarjeta">
                <div class="icono">❌</div>
                <div><span class="numero"><?php echo (int)($estadisticasReservas['cancelada'] + $estadisticasReservas['rechazada']); ?></span><p>Canceladas / rechazadas</p></div>
            </div>
        </section>

        <!-- Se mantiene tu Grid Inferior -->
        <section class="contenido-grid">
            <div class="seccion">
                <div class="titulo-seccion">
                    <div>
                        <h2>Reservas recientes</h2>
                        <p>Últimas solicitudes y su estado actual.</p>
                    </div>
                    <a href="calendario_profesor.php" class="btn-accion" style="text-decoration:none; text-align:center;">Ver calendario</a>
                </div>

                <div class="panel-toggle-wrap">
                    <div class="detail-toggle-switch panel-toggle-switch" aria-label="Cambiar vista de reservas del profesor">
                        <span class="toggle-label">Mías</span>
                        <button type="button" id="toggleVistaReservas" class="detail-toggle" aria-pressed="false">
                            <span class="toggle-knob"></span>
                        </button>
                        <span class="toggle-label">Todas</span>
                        <span class="toggle-help" tabindex="0" aria-label="Ayuda sobre el interruptor de vista">?</span>
                        <span class="toggle-help-tooltip">Usá este interruptor para ver solo tus reservas o todas las reservas de los profesores.</span>
                    </div>
                </div>

                <div class="tabla-contenedor">
                    <table>
                        <thead>
                            <tr>
                                <th>Recurso</th>
                                <th>Profesor</th>
                                <th>Fecha</th>
                                <th>Horario</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($ultimasReservas)): ?>
                                <tr>
                                    <td colspan="5" style="text-align: center; color: #666; padding: 20px;">Todavía no hay reservas registradas.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($ultimasReservas as $reserva): ?>
                                    <?php
                                        $estado = strtolower((string)($reserva['estado'] ?? 'pendiente'));
                                        $estadoTexto = ucfirst($estado);
                                        $claseEstado = $estado === 'aprobada' ? 'confirmada' : $estado;
                                        $tipoRecurso = strtolower((string)($reserva['recurso_tipo'] ?? 'sala'));
                                        $tipoLabel = $tipoRecurso === 'equipo' ? 'Equipo' : 'Sala / laboratorio';
                                        $fechaFormateada = date('d/m/Y', strtotime((string)($reserva['fecha'] ?? date('Y-m-d'))));
                                        $horario = trim((string)($reserva['hora_inicio'] ?? '')) . ' - ' . trim((string)($reserva['hora_fin'] ?? ''));
                                        $esPropia = (int)($reserva['es_propia'] ?? 0);
                                    ?>
                                    <tr class="reserva-panel-row" data-es-propia="<?php echo $esPropia; ?>" <?php echo $esPropia ? '' : 'style="display:none;"'; ?>>
                                        <td>
                                            <strong><?php echo htmlspecialchars((string)($reserva['recurso_nombre'] ?? 'Recurso')); ?></strong><br>
                                            <small style="color:#666; font-size:12px;"><?php echo htmlspecialchars($tipoLabel); ?></small>
                                        </td>
                                        <td><?php echo htmlspecialchars((string)($reserva['profesor_nombre'] ?? 'Profesor')); ?></td>
                                        <td><?php echo htmlspecialchars($fechaFormateada); ?></td>
                                        <td><?php echo htmlspecialchars($horario); ?></td>
                                        <td><span class="estado <?php echo htmlspecialchars($claseEstado); ?>"><?php echo htmlspecialchars($estadoTexto); ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div>
                <div class="seccion" style="margin-bottom: 25px;">
                    <div class="titulo-seccion"><div><h2>Acciones rápidas</h2></div></div>
                    <button class="accion" type="button" onclick="abrirFormularioReserva()">
                        <span>➕</span>
                        <div><strong>Nueva reserva</strong><small>Solicitar un aula o equipo</small></div>
                    </button>
                    <button class="accion" onclick="window.location.href='calendario_profesor.php'">
                        <span>🗓️</span>
                        <div><strong>Ver calendario</strong><small>Consultar disponibilidad global</small></div>
                    </button>
                </div>

                <div class="seccion">
                    <div class="titulo-seccion"><div><h2>Agenda del día</h2></div></div>
                    <?php if (empty($agendaHoy)): ?>
                        <div class="actividad">
                            <div class="fecha-cuadro"><strong><?php echo date('d'); ?></strong><span><?php echo $mesesCortos[date('m')] ?? strtoupper(date('M')); ?></span></div>
                            <div class="actividad-info"><strong>Sin reservas aprobadas hoy</strong><span>No tenés actividades aprobadas para el día actual.</span></div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($agendaHoy as $actividad): ?>
                            <?php
                                $dia = date('d', strtotime((string)$actividad['fecha']));
                                $mes = date('m', strtotime((string)$actividad['fecha']));
                                $mesTexto = $mesesCortos[$mes] ?? strtoupper(date('M', strtotime((string)$actividad['fecha'])));
                                $horaInicio = substr((string)($actividad['hora_inicio'] ?? ''), 0, 5);
                                $horaFin = substr((string)($actividad['hora_fin'] ?? ''), 0, 5);
                                $horario = trim($horaInicio . ' - ' . $horaFin);
                            ?>
                            <div class="actividad">
                                <div class="fecha-cuadro"><strong><?php echo htmlspecialchars($dia); ?></strong><span><?php echo htmlspecialchars($mesTexto); ?></span></div>
                                <div class="actividad-info"><strong><?php echo htmlspecialchars((string)($actividad['recurso_nombre'] ?? 'Reserva aprobada')); ?></strong><span><?php echo htmlspecialchars($horario); ?></span></div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </section>
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
                        <input type="checkbox" id="checkSala" onchange="toggleCamposDinámicos()" class="input-checkbox" style="width: 18px; height: 18px;">
                        🏫 Quiero reservar una Sala o Laboratorio
                    </label>
                    <label class="label-opcion" style="display: flex; align-items: center; gap: 10px; margin-bottom: 0; cursor: pointer; font-weight: bold;">
                        <input type="checkbox" id="checkEquipo" onchange="toggleCamposDinámicos()" class="input-checkbox" style="width: 18px; height: 18px;">
                        💻 Quiero reservar Equipamiento Móvil (Ej: Proyector)
                    </label>
                </div>

                <div id="seccionSala" class="seccion-oculta" style="display: none; margin-bottom: 15px;">
                    <label class="label-formulario" style="display: block; margin-bottom: 5px; font-weight: bold;">Seleccionar Espacio</label>
                    <select name="sala_id" class="input-formulario" style="width: 100%; min-height: 46px; padding: 10px 42px 10px 12px; border-radius: 8px; border: 1px solid #cbd5e1; background: #fff; box-sizing: border-box;">
                        <option value="">-- Elegí un Aula o Laboratorio --</option>
                        <?php foreach ($salas as $sala): ?>
                            <?php $nombreSala = $sala['nombre'] ?? ('Recurso #' . ($sala['id'] ?? 0)); ?>
                            <option value="<?php echo (int)($sala['id'] ?? 0); ?>"><?php echo htmlspecialchars($nombreSala); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div id="seccionEquipo" class="seccion-oculta" style="display: none; margin-bottom: 15px;">
                    <label class="label-formulario" style="display: block; margin-bottom: 5px; font-weight: bold;">Seleccionar Equipamiento</label>
                    <select name="equipo_id" class="input-formulario" style="width: 100%; min-height: 46px; padding: 10px 42px 10px 12px; border-radius: 8px; border: 1px solid #cbd5e1; background: #fff; box-sizing: border-box;">
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

                <button type="submit" class="btn-guardar btn-submit-reserva btn-deshabilitado" id="btnEnviarReserva" disabled style="margin-top: 25px; width: 100%; padding: 14px; border: none; border-radius: 10px; font-weight: 700;">
                    Confirmar Solicitud
                </button>
            </form>
        </div>
    </div>

    <script src="../scripts/reservas_profesor.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggle = document.getElementById('toggleVistaReservas');
            const rows = Array.from(document.querySelectorAll('.reserva-panel-row'));

            if (!toggle || rows.length === 0) {
                return;
            }

            const aplicarVista = () => {
                const mostrarTodas = toggle.classList.contains('active');

                rows.forEach((row) => {
                    const esPropia = row.dataset.esPropia === '1';
                    row.style.display = mostrarTodas || esPropia ? '' : 'none';
                });
            };

            toggle.addEventListener('click', function () {
                toggle.classList.toggle('active');
                toggle.setAttribute('aria-pressed', String(toggle.classList.contains('active')));
                aplicarVista();
            });

            aplicarVista();
        });
    </script>
</body>
</html>