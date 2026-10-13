<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../includes/config.php';

// Validar seguridad (Admin)
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'admin') {
    header("Location: Inicio_sesion.php");
    exit();
}

// 1. Obtener Estadísticas Reales
$total_reservas = $pdo->query("SELECT COUNT(*) FROM reservas")->fetchColumn();
$reservas_aprobadas = $pdo->query("SELECT COUNT(*) FROM reservas WHERE estado = 'aprobada'")->fetchColumn();
$reservas_pendientes = $pdo->query("SELECT COUNT(*) FROM reservas WHERE estado = 'pendiente'")->fetchColumn();
$reservas_rechazadas = $pdo->query("SELECT COUNT(*) FROM reservas WHERE estado = 'rechazada'")->fetchColumn();
$reservas_canceladas = $pdo->query("SELECT COUNT(*) FROM reservas WHERE estado = 'cancelada'")->fetchColumn();

// 2. Obtener todas las reservas con los datos del usuario y el recurso.
// Se ordenan por la reserva más nueva para que las solicitudes recientes aparezcan primero.
$query = "
    SELECT r.id, r.id_usuario, r.fecha, r.hora_inicio, r.hora_fin, r.estado,
           r.motivo_profesor, r.motivo_rechazo, r.created_at,
           u.nombre AS usuario_nombre, u.email AS usuario_email,
           rec.nombre AS recurso_nombre, rec.tipo AS recurso_tipo
    FROM reservas r
    INNER JOIN usuarios u ON r.id_usuario = u.id
    INNER JOIN recursos rec ON r.id_recurso = rec.id
    ORDER BY r.created_at DESC, r.fecha DESC, r.hora_inicio DESC, r.id DESC
";
$reservasRaw = $pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);

// 3. Agrupar reservas vinculadas: cuando una misma solicitud incluye sala + equipo
// se muestra como una sola fila en administración, pero se conserva la lista de ids
// para aprobar/rechazar todo el conjunto.
$reservasAgrupadas = [];
foreach ($reservasRaw as $res) {
    $grupoKey = implode('|', [
        (string)($res['id_usuario'] ?? ''),
        (string)($res['fecha'] ?? ''),
        (string)($res['hora_inicio'] ?? ''),
        (string)($res['hora_fin'] ?? ''),
        strtolower((string)($res['estado'] ?? 'pendiente')),
        (string)($res['motivo_profesor'] ?? '')
    ]);

    if (!isset($reservasAgrupadas[$grupoKey])) {
        $reservasAgrupadas[$grupoKey] = [
            'ids' => [],
            'usuario_nombre' => $res['usuario_nombre'] ?? 'Usuario',
            'usuario_email' => $res['usuario_email'] ?? '',
            'recursos' => [],
            'fecha' => $res['fecha'] ?? '',
            'hora_inicio' => $res['hora_inicio'] ?? '00:00:00',
            'hora_fin' => $res['hora_fin'] ?? '00:00:00',
            'estado' => $res['estado'] ?? 'pendiente',
            'motivo_profesor' => $res['motivo_profesor'] ?? null,
            'motivo_rechazo' => $res['motivo_rechazo'] ?? null,
        ];
    }

    $idReserva = (int)($res['id'] ?? 0);
    if ($idReserva > 0) {
        $reservasAgrupadas[$grupoKey]['ids'][] = $idReserva;
    }

    $recursoNombre = trim((string)($res['recurso_nombre'] ?? ''));
    if ($recursoNombre !== '' && !in_array($recursoNombre, $reservasAgrupadas[$grupoKey]['recursos'], true)) {
        $reservasAgrupadas[$grupoKey]['recursos'][] = $recursoNombre;
    }
}

$reservasAgrupadas = array_values($reservasAgrupadas);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de reservas</title>
    <link rel="stylesheet" href="../css/style_reservas_admin.css">
</head>
<body>

    <?php require_once __DIR__ . '/../includes/barra_admin.php'; ?>

    <main class="contenedor">
        <section class="encabezado">
            <div>
                <h1>Gestión de reservas</h1>
                <p>Consultá y administrá todas las reservas del sistema.</p>
            </div>
        </section>

        <!-- Estadísticas Conectadas -->
        <section class="estadisticas">
            <div class="tarjeta">
                <span class="numero" id="totalReservas"><?php echo $total_reservas; ?></span>
                <span>Total de reservas</span>
            </div>
            <div class="tarjeta confirmada">
                <span class="numero" id="reservasConfirmadas"><?php echo $reservas_aprobadas; ?></span>
                <span>Aprobadas</span>
            </div>
            <div class="tarjeta pendiente">
                <span class="numero" id="reservasPendientes"><?php echo $reservas_pendientes; ?></span>
                <span>Pendientes</span>
            </div>
            <div class="tarjeta cancelada">
                <span class="numero" id="reservasCanceladas"><?php echo $reservas_rechazadas; ?></span>
                <span>Rechazadas</span>
            </div>
            <div class="tarjeta cancelada">
                <span class="numero" id="reservasCanceladasTotales"><?php echo $reservas_canceladas; ?></span>
                <span>Canceladas</span>
            </div>
        </section>

        <section class="panel">
            <div class="herramientas">
                <input type="text" id="buscarReserva" placeholder="Buscar reserva, usuario o recurso..." onkeyup="buscarReservas()">

                <select id="filtroEstado" onchange="filtrarReservas()">
                    <option value="todos">Todos los estados</option>
                    <option value="Aprobada">Aprobadas</option>
                    <option value="Pendiente">Pendientes</option>
                    <option value="Rechazada">Rechazadas</option>
                    <option value="Cancelada">Canceladas</option>
                </select>

                <select id="filtroRecurso" onchange="filtrarReservas()">
                    <option value="todos">Todos los recursos</option>
                    <option value="Sala">Salas</option>
                    <option value="Laboratorio">Laboratorios</option>
                    <option value="Equipo">Equipamiento</option>
                </select>
            </div>

            <div class="tabla-contenedor">
                <table>
                    <thead>
                        <tr>
                            <th>Usuario</th>
                            <th>Recurso</th>
                            <th>Fecha</th>
                            <th>Horario</th>
                            <th>Razón</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tablaReservas">
                        <?php if (empty($reservasAgrupadas)): ?>
                            <tr><td colspan="6" style="text-align: center; padding: 20px;">No hay reservas registradas en el sistema.</td></tr>
                        <?php else: ?>
                            <?php foreach ($reservasAgrupadas as $res): ?>
                                <?php 
                                    $fechaFormateada = date("d/m/Y", strtotime($res['fecha']));
                                    $horario = substr($res['hora_inicio'], 0, 5) . ' - ' . substr($res['hora_fin'], 0, 5);
                                    $estadoClase = strtolower($res['estado']);
                                    if ($estadoClase === 'aprobada') $estadoClase = 'confirmada';
                                    $idsString = implode(',', $res['ids']);
                                    $recursoTexto = implode(' + ', $res['recursos']);
                                ?>
                                <tr data-recurso="<?php echo ucfirst($res['estado']); ?>">
                                    <td>
                                        <strong><?php echo htmlspecialchars($res['usuario_nombre']); ?></strong>
                                        <small><?php echo htmlspecialchars($res['usuario_email']); ?></small>
                                    </td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($recursoTexto); ?></strong>
                                        <small><?php echo count($res['recursos']) > 1 ? 'Reserva vinculada' : 'Reserva simple'; ?></small>
                                    </td>
                                    <td><?php echo $fechaFormateada; ?></td>
                                    <td><?php echo $horario; ?></td>
                                    <td>
                                        <?php if (!empty($res['motivo_profesor'])): ?>
                                            <small style="color: #666; display: block; font-style: italic;"><?php echo htmlspecialchars(substr($res['motivo_profesor'], 0, 60)) . (strlen($res['motivo_profesor']) > 60 ? '...' : ''); ?></small>
                                        <?php else: ?>
                                            <small style="color: #999;">Sin razón</small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="estado <?php echo $estadoClase; ?>"><?php echo ucfirst($res['estado']); ?></span>
                                        <?php if (!empty($res['motivo_rechazo']) && $res['estado'] === 'rechazada'): ?>
                                            <small style="color: #d32f2f; display: block; font-style: italic; margin-top: 4px;">Motivo: <?php echo htmlspecialchars(substr($res['motivo_rechazo'], 0, 40)) . (strlen($res['motivo_rechazo']) > 40 ? '...' : ''); ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td class="acciones">
                                        <?php if ($res['estado'] === 'pendiente'): ?>
                                            <form action="procesar_estado_reserva.php" method="POST" style="display:inline;">
                                                <input type="hidden" name="id_reserva" value="<?php echo htmlspecialchars($idsString); ?>">
                                                <input type="hidden" name="estado" value="aprobada">
                                                <button type="submit" class="btn-editar" style="background-color: #4CAF50; color: white; border: none; padding: 5px 10px; cursor: pointer; border-radius: 4px;">Aprobar</button>
                                            </form>
                                            <button type="button" class="btn-cancelar" style="background-color: #f44336; color: white; border: none; padding: 5px 10px; cursor: pointer; border-radius: 4px;" data-id="<?php echo htmlspecialchars($idsString); ?>" onclick="abrirModalRechazo(this.dataset.id)">Rechazar</button>
                                        <?php elseif ($res['estado'] === 'aprobada'): ?>
                                            <button type="button" class="btn-cancelar" style="background-color: #f44336; color: white; border: none; padding: 5px 10px; cursor: pointer; border-radius: 4px;" data-id="<?php echo htmlspecialchars($idsString); ?>" onclick="abrirModalRechazo(this.dataset.id)">Rechazar</button>
                                        <?php else: ?>
                                            <span style="color: #999; font-size: 13px;">Sin acciones</span>
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

    <!-- Modal de Rechazo -->
    <div id="modalRechazo" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
        <div style="background: white; padding: 30px; border-radius: 8px; max-width: 400px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            <h2>Rechazar Reserva</h2>
            <p id="modalRechazoInfo" style="margin: 15px 0; color: #666; font-size: 14px;"></p>
            <form id="formRechazo" method="POST" action="procesar_estado_reserva.php">
                <input type="hidden" id="modalRechazoId" name="id_reserva">
                <input type="hidden" name="estado" value="rechazada">
                <label for="motivoRechazo" style="display: block; margin-bottom: 8px; font-weight: 500;">Razón del rechazo:</label>
                <textarea id="motivoRechazo" name="motivo_rechazo" required placeholder="Explica por qué se rechaza esta reserva..." style="width: 100%; height: 100px; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-family: Arial, sans-serif; resize: vertical;"></textarea>
                <div style="margin-top: 20px; display: flex; gap: 10px; justify-content: flex-end;">
                    <button type="button" onclick="cerrarModalRechazo()" style="padding: 10px 20px; background: #e0e0e0; border: none; border-radius: 4px; cursor: pointer;">Cancelar</button>
                    <button type="submit" style="padding: 10px 20px; background: #f44336; color: white; border: none; border-radius: 4px; cursor: pointer;">Rechazar</button>
                </div>
            </form>
        </div>
    </div>

    <script src="../scripts/reservas_admin.js"></script>
    <script>
        function abrirModalRechazo(idReserva) {
            document.getElementById('modalRechazoId').value = idReserva;
            document.getElementById('modalRechazo').style.display = 'flex';

            const fila = document.querySelector(`button[data-id="${CSS.escape(idReserva)}"]`)?.closest('tr');
            if (fila) {
                const usuario = fila.cells[0].textContent.trim();
                const recurso = fila.cells[1].textContent.trim();
                const fecha = fila.cells[2].textContent.trim();
                const horario = fila.cells[3].textContent.trim();
                document.getElementById('modalRechazoInfo').textContent = `${usuario} - ${recurso} - ${fecha} ${horario}`;
            }
        }
        
        function cerrarModalRechazo() {
            document.getElementById('modalRechazo').style.display = 'none';
            document.getElementById('motivoRechazo').value = '';
        }
        
        // Cerrar modal al hacer clic fuera de él
        document.getElementById('modalRechazo').addEventListener('click', function(e) {
            if (e.target === this) {
                cerrarModalRechazo();
            }
        });
    </script>
</body>
</html>