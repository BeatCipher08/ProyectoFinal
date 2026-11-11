<?php
ini_set('display_errors', 1); 
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'admin') {
    header("Location: Inicio_sesion.php");
    exit();
}

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/funciones.php';

$stmt = $pdo->query("SELECT COUNT(*) FROM usuarios");
$total_usuarios = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM reservas");
$total_reservas = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM reservas WHERE estado = 'pendiente'");
$reservas_pendientes = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM recursos");
$total_recursos = $stmt->fetchColumn();

$reservasRecientesStmt = $pdo->query(
    "SELECT
        r.fecha,
        r.hora_inicio,
        r.hora_fin,
        r.estado,
        u.nombre AS usuario_nombre,
        u.email AS usuario_email,
        rec.nombre AS recurso_nombre,
        rec.tipo AS recurso_tipo
    FROM reservas r
    LEFT JOIN usuarios u ON u.id = r.id_usuario
    LEFT JOIN recursos rec ON rec.id = r.id_recurso
    ORDER BY r.created_at DESC, r.id DESC
    LIMIT 8"
);

$reservas_recientes = $reservasRecientesStmt->fetchAll(PDO::FETCH_ASSOC);

function claseEstadoAdmin(string $estado): string {
    $estadoNormalizado = strtolower(trim($estado));

    if ($estadoNormalizado === 'aprobada') {
        return 'confirmada';
    }

    if ($estadoNormalizado === 'rechazada' || $estadoNormalizado === 'cancelada') {
        return 'cancelada';
    }

    return 'pendiente';
}

function etiquetaEstadoAdmin(string $estado): string {
    $estadoNormalizado = strtolower(trim($estado));

    if ($estadoNormalizado === 'aprobada') {
        return 'Confirmada';
    }

    if ($estadoNormalizado === 'rechazada') {
        return 'Rechazada';
    }

    if ($estadoNormalizado === 'cancelada') {
        return 'Cancelada';
    }

    return 'Pendiente';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administrador</title>
    <link rel="stylesheet" href="../css/style_panel_admin.css">
</head>
<body>

    <?php require_once __DIR__ . '/../includes/barra_admin.php'; ?>

    <main class="contenedor">
        <section class="bienvenida">
            <div>
                <h1>Panel de administrador</h1>
                <p>Bienvenido al centro de control de ReservaTech.</p>
            </div>
            <div class="fecha">
                <span>Hoy</span>
                <strong id="fechaActual">
                    <?php echo date('d/m/Y'); ?>
                </strong>
            </div>
        </section>

        <section class="estadisticas">
            <div class="tarjeta">
                <div class="icono">👥</div>
                <div>
                    <span class="numero"><?php echo $total_usuarios; ?></span>
                    <p>Usuarios registrados</p>
                </div>
            </div>

            <div class="tarjeta">
                <div class="icono">📅</div>
                <div>
                    <span class="numero"><?php echo $total_reservas; ?></span>
                    <p>Reservas totales</p>
                </div>
            </div>

            <div class="tarjeta">
                <div class="icono">⏳</div>
                <div>
                    <span class="numero"><?php echo $reservas_pendientes; ?></span>
                    <p>Reservas pendientes</p>
                </div>
            </div>

            <div class="tarjeta">
                <div class="icono">🏫</div>
                <div>
                    <span class="numero"><?php echo $total_recursos; ?></span>
                    <p>Recursos disponibles</p>
                </div>
            </div>
        </section>

        <section class="seccion">
            <div class="titulo-seccion">
                <h2>Accesos rápidos</h2>
                <p>Administrá las diferentes partes del sistema.</p>
            </div>

            <div class="accesos">
                <a href="reservas_admin.php" class="acceso">
                    <div class="acceso-icono">📅</div>
                    <div>
                        <h3>Gestionar reservas</h3>
                        <p>Ver, aprobar, cancelar y controlar reservas.</p>
                    </div>
                    <span>→</span>
                </a>

                <a href="usuarios.php" class="acceso">
                    <div class="acceso-icono">👥</div>
                    <div>
                        <h3>Gestionar usuarios</h3>
                        <p>Administrar alumnos, profesores y administradores.</p>
                    </div>
                    <span>→</span>
                </a>

                <a href="recursos.php" class="acceso">
                    <div class="acceso-icono">🏫</div>
                    <div>
                        <h3>Recursos y aulas</h3>
                        <p>Consultar salas, laboratorios y equipamiento.</p>
                    </div>
                    <span>→</span>
                </a>

                <a href="calendario.html" class="acceso">
                    <div class="acceso-icono">🗓️️</div>
                    <div>
                        <h3>Calendario</h3>
                        <p>Consultar las reservas organizadas por fecha.</p>
                    </div>
                    <span>→</span>
                </a>
            </div>
        </section>

        <section class="seccion">
            <div class="titulo-seccion">
                <h2>Reservas recientes</h2>
                <a href="reservas_admin.php">Ver todas</a>
            </div>

            <div class="tabla-contenedor">
                <table>
                    <thead>
                        <tr>
                            <th>Usuario</th>
                            <th>Recurso</th>
                            <th>Fecha</th>
                            <th>Horario</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($reservas_recientes)): ?>
                            <tr>
                                <td colspan="5">Aún no hay reservas registradas.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($reservas_recientes as $reserva): ?>
                                <?php
                                $usuarioNombre = trim((string)($reserva['usuario_nombre'] ?? 'Usuario sin nombre'));
                                $usuarioEmail = trim((string)($reserva['usuario_email'] ?? 'sin email'));
                                $recursoNombre = trim((string)($reserva['recurso_nombre'] ?? 'Recurso'));
                                $recursoTipo = trim((string)($reserva['recurso_tipo'] ?? 'sala'));
                                $fecha = trim((string)($reserva['fecha'] ?? ''));
                                $horaInicio = trim((string)($reserva['hora_inicio'] ?? ''));
                                $horaFin = trim((string)($reserva['hora_fin'] ?? ''));
                                $estado = trim((string)($reserva['estado'] ?? 'pendiente'));

                                $fechaFormateada = $fecha !== '' ? date('d/m/Y', strtotime($fecha)) : '-';
                                $horaInicioFormateada = $horaInicio !== '' ? substr($horaInicio, 0, 5) : '--:--';
                                $horaFinFormateada = $horaFin !== '' ? substr($horaFin, 0, 5) : '--:--';
                                ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($usuarioNombre); ?></strong>
                                        <small><?php echo htmlspecialchars($usuarioEmail); ?></small>
                                    </td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($recursoNombre); ?></strong>
                                        <small><?php echo htmlspecialchars(ucfirst($recursoTipo)); ?></small>
                                    </td>
                                    <td><?php echo htmlspecialchars($fechaFormateada); ?></td>
                                    <td><?php echo htmlspecialchars($horaInicioFormateada . ' - ' . $horaFinFormateada); ?></td>
                                    <td>
                                        <span class="estado <?php echo htmlspecialchars(claseEstadoAdmin($estado)); ?>"><?php echo htmlspecialchars(etiquetaEstadoAdmin($estado)); ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="seccion">
            <div class="titulo-seccion">
                <h2>Estado del sistema</h2>
            </div>

            <div class="estado-sistema">
                <div class="sistema-item">
                    <span class="punto verde"></span>
                    <div>
                        <strong>Sistema de reservas</strong>
                        <p>Funcionando correctamente</p>
                    </div>
                </div>

                <div class="sistema-item">
                    <span class="punto verde"></span>
                    <div>
                        <strong>Recursos</strong>
                        <p>Disponibles para reservar</p>
                    </div>
                </div>

                <div class="sistema-item">
                    <span class="punto verde"></span>
                    <div>
                        <strong>Usuarios</strong>
                        <p>Sistema operativo</p>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <script src="../scripts/panel_admin.js"></script>
</body>
</html>