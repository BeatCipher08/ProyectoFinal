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

// ... acá siguen las consultas SQL de las estadísticas ...
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
            
            <div class="perfil">
                <div class="avatar" style="background: #2c3e50; color: white; display: flex; align-items: center; justify-content: center; border-radius: 50%; width: 45px; height: 45px; font-weight: bold;">
                    <?php echo $iniciales; ?>
                </div>
                <div>
                    <strong><?php echo htmlspecialchars($nombre_profesor); ?></strong>
                    <span>Profesor</span>
                </div>
            </div>
        </section>

        <!-- Se mantienen tus estadísticas originales -->
        <section class="estadisticas">
            <div class="tarjeta">
                <div class="icono">📅</div>
                <div><span class="numero">8</span><p>Mis reservas</p></div>
            </div>
            <div class="tarjeta">
                <div class="icono">⏳</div>
                <div><span class="numero">5</span><p>Reservas pendientes</p></div>
            </div>
            <div class="tarjeta">
                <div class="icono">🏫</div>
                <div><span class="numero">12</span><p>Aulas disponibles</p></div>
            </div>
            <div class="tarjeta">
                <div class="icono">💻</div>
                <div><span class="numero">27</span><p>Equipos disponibles</p></div>
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
                    <a href="reservas_profesor.php" class="btn-accion" style="text-decoration:none; text-align:center;">Ver todas</a>
                </div>

                <div class="tabla-contenedor">
                    <table>
                        <thead>
                            <tr>
                                <th>Recurso</th>
                                <th>Fecha</th>
                                <th>Horario</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Aula 204</td>
                                <td>11/08/2026</td>
                                <td>08:00 - 10:00</td>
                                <td><span class="estado confirmada">Confirmada</span></td>
                            </tr>
                            <tr>
                                <td>Proyector 03</td>
                                <td>12/08/2026</td>
                                <td>10:00 - 12:00</td>
                                <td><span class="estado confirmada">Confirmada</span></td>
                            </tr>
                            <tr>
                                <td>Laboratorio 1</td>
                                <td>14/08/2026</td>
                                <td>13:00 - 15:00</td>
                                <td><span class="estado pendiente">Pendiente</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div>
                <div class="seccion" style="margin-bottom: 25px;">
                    <div class="titulo-seccion"><div><h2>Acciones rápidas</h2></div></div>
                    <button class="accion" onclick="window.location.href='reservas_profesor.php'">
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
                    <div class="actividad">
                        <div class="fecha-cuadro"><strong>11</strong><span>AGO</span></div>
                        <div class="actividad-info"><strong>Clase en Aula 204</strong><span>08:00 - 10:00</span></div>
                    </div>
                    <div class="actividad">
                        <div class="fecha-cuadro"><strong>12</strong><span>AGO</span></div>
                        <div class="actividad-info"><strong>Uso de Proyector 03</strong><span>10:00 - 12:00</span></div>
                    </div>
                </div>
            </div>
        </section>
    </main>
</body>
</html>