<?php
// Integración del control de sesión y navegación
require_once __DIR__ . '/../includes/barra_profesor.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendario - Profesor</title>
    <link rel="stylesheet" href="../css/style_reservas_admin.css">
    <link rel="stylesheet" href="../css/style_reservas_profesor.css">
    <link rel="stylesheet" href="../css/style_calendario.css">
</head>
<body>

    <?php require_once __DIR__ . '/../includes/barra_profesor.php'; ?>

    <main class="contenedor">
        <section class="encabezado">
            <div>
                <h1>Calendario de Disponibilidad</h1>
                <p>Consultá las reservas confirmadas de manera visual.</p>
            </div>
            <div>
                <button class="btn-agregar" onclick="window.location.href='reservas_profesor.php'">+ Nueva reserva</button>
            </div>
        </section>

        <section class="panel">
            <div class="herramientas">
                <h2 style="margin: 0;">Agosto 2026</h2>
                <select class="select-filtro">
                    <option value="todos">Todos los recursos</option>
                    <option value="salas">Solo Salas</option>
                    <option value="equipos">Solo Equipamiento</option>
                </select>
            </div>

            <div class="calendario-grid">
                <div class="dia-header">Lunes</div>
                <div class="dia-header">Martes</div>
                <div class="dia-header">Miércoles</div>
                <div class="dia-header">Jueves</div>
                <div class="dia-header">Viernes</div>
                <div class="dia-header">Sábado</div>
                <div class="dia-header">Domingo</div>

                <!-- Fila 1 -->
                <div class="dia-celda dia-vacio"></div>
                <div class="dia-celda dia-vacio"></div>
                <div class="dia-celda dia-vacio"></div>
                <div class="dia-celda dia-vacio"></div>
                <div class="dia-celda dia-vacio"></div>
                <div class="dia-celda"><span class="fecha-numero">1</span></div>
                <div class="dia-celda"><span class="fecha-numero">2</span></div>

                <!-- Fila 2 -->
                <div class="dia-celda"><span class="fecha-numero">3</span></div>
                <div class="dia-celda"><span class="fecha-numero">4</span></div>
                <div class="dia-celda"><span class="fecha-numero">5</span></div>
                <div class="dia-celda"><span class="fecha-numero">6</span></div>
                <div class="dia-celda"><span class="fecha-numero">7</span></div>
                <div class="dia-celda"><span class="fecha-numero">8</span></div>
                <div class="dia-celda"><span class="fecha-numero">9</span></div>

                <!-- Fila 3 con eventos -->
                <div class="dia-celda"><span class="fecha-numero">10</span></div>
                <div class="dia-celda">
                    <span class="fecha-numero">11</span>
                    <span class="reserva-badge">08:00 - Aula 204</span>
                </div>
                <div class="dia-celda">
                    <span class="fecha-numero">12</span>
                    <span class="reserva-badge">10:00 - Proyector 03</span>
                </div>
                <div class="dia-celda"><span class="fecha-numero">13</span></div>
                <div class="dia-celda">
                    <span class="fecha-numero">14</span>
                    <span class="reserva-badge pendiente">13:00 - Lab 1 (Pendiente)</span>
                </div>
                <div class="dia-celda"><span class="fecha-numero">15</span></div>
                <div class="dia-celda"><span class="fecha-numero">16</span></div>
            </div>
        </section>
    </main>
</body>
</html>