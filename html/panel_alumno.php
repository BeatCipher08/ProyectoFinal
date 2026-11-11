<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'alumno') {
    header("Location: Inicio_sesion.php");
    exit();
}

require_once __DIR__ . '/../includes/config.php';

$usuario_id = (int) $_SESSION['usuario_id'];
$nombre_alumno = $_SESSION['nombre'] ?? 'Alumno';
$iniciales = strtoupper(substr($nombre_alumno, 0, 2));

$estadisticas = [
    'pendiente' => 0,
    'aprobada' => 0,
    'cancelada' => 0,
    'rechazada' => 0,
];

$estadisticasStmt = $pdo->prepare("SELECT estado, COUNT(*) AS total FROM reservas WHERE id_usuario = ? GROUP BY estado");
$estadisticasStmt->execute([$usuario_id]);
foreach ($estadisticasStmt->fetchAll(PDO::FETCH_ASSOC) as $fila) {
    $estado = strtolower((string)($fila['estado'] ?? ''));
    if (isset($estadisticas[$estado])) {
        $estadisticas[$estado] = (int)($fila['total'] ?? 0);
    }
}

$reservasAprobadasStmt = $pdo->prepare(
    "SELECT r.id, r.fecha, r.hora_inicio, r.hora_fin, r.estado,
            rec.nombre AS recurso_nombre,
            COALESCE(u.nombre, 'Sin usuario') AS solicitante
     FROM reservas r
     LEFT JOIN recursos rec ON rec.id = r.id_recurso
     LEFT JOIN usuarios u ON u.id = r.id_usuario
     WHERE r.estado = 'aprobada'
     ORDER BY r.fecha ASC, r.hora_inicio ASC
     LIMIT 8"
);
$reservasAprobadasStmt->execute();
$reservasAprobadas = $reservasAprobadasStmt->fetchAll(PDO::FETCH_ASSOC);

$recursosActivos = (int) $pdo->query("SELECT COUNT(*) FROM recursos WHERE activo = 1")->fetchColumn();
$equiposActivos = (int) $pdo->query("SELECT COUNT(*) FROM recursos WHERE tipo = 'equipo' AND activo = 1")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel del Alumno</title>
    <link rel="stylesheet" href="../css/style_panel_alumno.css">
</head>
<body>
    <?php require_once __DIR__ . '/../includes/barra_alumno.php'; ?>

    <main class="contenedor">
        <section class="bienvenida">
            <div>
                <h1>Panel del alumno</h1>
                <p>Consultá tus actividades, reservas y recursos disponibles.</p>
            </div>
        </section>

        <section class="estadisticas">
            <div class="tarjeta">
                <div class="icono">📅</div>
                <div>
                    <span class="numero"><?php echo (int) array_sum($estadisticas); ?></span>
                    <p>Mis reservas</p>
                </div>
            </div>

            <div class="tarjeta">
                <div class="icono">🏫</div>
                <div>
                    <span class="numero"><?php echo $recursosActivos; ?></span>
                    <p>Recursos activos</p>
                </div>
            </div>

            <div class="tarjeta">
                <div class="icono">💻</div>
                <div>
                    <span class="numero"><?php echo $equiposActivos; ?></span>
                    <p>Equipos disponibles</p>
                </div>
            </div>

            <div class="tarjeta">
                <div class="icono">⏳</div>
                <div>
                    <span class="numero"><?php echo (int) $estadisticas['pendiente']; ?></span>
                    <p>Pendientes</p>
                </div>
            </div>
        </section>

        <section class="contenido-grid">
            <div class="seccion">
                <div class="titulo-seccion">
                    <div>
                        <h2>Reservas aprobadas generales</h2>
                        <p>Visualización institucional de reservas confirmadas.</p>
                    </div>
                    <a href="calendario_alumno.php" class="btn-accion">Ver calendario</a>
                </div>

                <div class="tabla-contenedor">
                    <table>
                        <thead>
                            <tr>
                                <th>Solicitante</th>
                                <th>Recurso</th>
                                <th>Fecha</th>
                                <th>Horario</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($reservasAprobadas)): ?>
                                <tr>
                                    <td colspan="5" style="text-align: center; color: #666; padding: 20px;">No hay reservas aprobadas para mostrar en este momento.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($reservasAprobadas as $reserva): ?>
                                    <?php
                                    $estado = strtolower((string)($reserva['estado'] ?? 'pendiente'));
                                    $claseEstado = $estado === 'aprobada' ? 'aprobada' : ($estado === 'pendiente' ? 'pendiente' : ($estado === 'rechazada' ? 'rechazada' : 'cancelada'));
                                    $fecha = date('d/m/Y', strtotime((string)($reserva['fecha'] ?? date('Y-m-d'))));
                                    $horario = trim((string)($reserva['hora_inicio'] ?? '')) . ' - ' . trim((string)($reserva['hora_fin'] ?? ''));
                                    ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars((string)($reserva['solicitante'] ?? 'Sin usuario')); ?></td>
                                        <td><?php echo htmlspecialchars((string)($reserva['recurso_nombre'] ?? 'Recurso')); ?></td>
                                        <td><?php echo htmlspecialchars($fecha); ?></td>
                                        <td><?php echo htmlspecialchars($horario); ?></td>
                                        <td><span class="estado <?php echo htmlspecialchars($claseEstado); ?>"><?php echo htmlspecialchars(ucfirst($estado)); ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="panel">
                <div class="panel-titulo">
                    <div>
                        <h2>Acciones rápidas</h2>
                        <p>Consulta la información que necesitás.</p>
                    </div>
                </div>

                <div class="accesos">
                    <a href="calendario_alumno.php" class="acceso">
                        <div class="acceso-icono">📅</div>
                        <div>
                            <h3>Ver calendario</h3>
                            <p>Consultá actividades y horarios.</p>
                        </div>
                        <span>→</span>
                    </a>

                    <a href="catalogo_recursos.php" class="acceso">
                        <div class="acceso-icono">💻</div>
                        <div>
                            <h3>Ver equipos</h3>
                            <p>Consultar el inventario disponible para reservar.</p>
                        </div>
                        <span>→</span>
                    </a>

                    <a href="cerrar_sesion.php" class="acceso">
                        <div class="acceso-icono">👤</div>
                        <div>
                            <h3>Cerrar sesión</h3>
                            <p>Salir del panel del alumno.</p>
                        </div>
                        <span>→</span>
                    </a>
                </div>
            </div>
        </section>
    </main>
</body>
</html>
