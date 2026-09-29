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
                        <tr>
                            <td>
                                <strong>Juan Pérez</strong>
                                <small>juanperez</small>
                            </td>
                            <td>
                                <strong>Aula 101</strong>
                                <small>Aula</small>
                            </td>
                            <td>27/08/2026</td>
                            <td>08:00 - 10:00</td>
                            <td>
                                <span class="estado confirmada">Confirmada</span>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong>María Rodríguez</strong>
                                <small>mrodriguez</small>
                            </td>
                            <td>
                                <strong>Laboratorio 2</strong>
                                <small>Laboratorio</small>
                            </td>
                            <td>27/08/2026</td>
                            <td>10:00 - 12:00</td>
                            <td>
                                <span class="estado pendiente">Pendiente</span>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Carlos Silva</strong>
                                <small>csilva</small>
                            </td>
                            <td>
                                <strong>Proyector 1</strong>
                                <small>Equipamiento</small>
                            </td>
                            <td>28/08/2026</td>
                            <td>09:00 - 11:00</td>
                            <td>
                                <span class="estado confirmada">Confirmada</span>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Lucía Gómez</strong>
                                <small>lgomez</small>
                            </td>
                            <td>
                                <strong>Aula 204</strong>
                                <small>Aula</small>
                            </td>
                            <td>28/08/2026</td>
                            <td>13:00 - 15:00</td>
                            <td>
                                <span class="estado pendiente">Pendiente</span>
                            </td>
                        </tr>
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