<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
ini_set('display_errors', 1); 
error_reporting(E_ALL);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salones Grandes - Detalles de Espacio</title>

    <!-- Archivos CSS -->
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="../css/style_salon_detalle.css">
    <link rel="stylesheet" href="../css/style_salon_uno.css">
    <link rel="stylesheet" href="../css/style_salon_grande_uno.css">

    <!-- Fuentes e Íconos -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>

    <?php require_once __DIR__ . '/../includes/barra_superior.php'; ?>

    <main>
        <!-- ==========================================================
           PORTADA (HERO)
           ========================================================== -->
        <section class="hero-salon hero-grande">
            <div class="contenido-salon">
                <h1>Salón de Universidad</h1>
                <p>Espacio de gran capacidad pensado para clases institucionales, actos académicos, reuniones universitarias y eventos con gran cantidad de asistentes.</p>
            </div>
            <div class="foto-salon">
                <img src="../imagenes/imageninstituto.jpg" alt="Vista interior del salón universitario">
            </div>
        </section>

        <!-- ==========================================================
           FICHA DE DATOS TÉCNICOS AMPLIADOS
           ========================================================== -->
        <section class="datos-rapidos">
            <div class="dato-item">
                <h2>5</h2>
                <p>Cantidad de Salones</p>
            </div>
            <div class="dato-item">
                <h2>Planta Alta-Baja</h2>
                <p>Ubicación (Ala Central)</p>
            </div>
            <div class="dato-item">
                <h2>150</h2>
                <p>Capacidad de Personas</p>
            </div>
            <div class="dato-item">
                <h2>07:00 - 23:00</h2>
                <p>Horario Extendido</p>
            </div>
        </section>

        <!-- ==========================================================
           SECCIÓN DE EQUIPAMIENTO CON TARJETAS HORIZONTALES
           ========================================================== -->
        <section class="seccion-equipamiento">
            <h2>Equipamiento del Salón Universitario</h2>
            
            <div class="contenedor-tarjetas-horizontales">
                
                <div class="tarjeta-equipo-horizontal">
                    <div class="icono-equipo-horizontal">📽️</div>
                    <div class="texto-equipo-horizontal">
                        <h3>Proyección central</h3>
                        <p>Sistema de proyección para clases magistrales, presentaciones académicas y actividades con público numeroso.</p>
                    </div>
                </div>

                <div class="tarjeta-equipo-horizontal">
                    <div class="icono-equipo-horizontal">🎙️</div>
                    <div class="texto-equipo-horizontal">
                        <h3>Audio de auditorio</h3>
                        <p>Micrófonos y equipamiento de sonido para conferencias, exposiciones y intervenciones del cuerpo docente.</p>
                    </div>
                </div>

                <div class="tarjeta-equipo-horizontal">
                    <div class="icono-equipo-horizontal">💺</div>
                    <div class="texto-equipo-horizontal">
                        <h3>Capacidad para audiencia</h3>
                        <p>Espacio pensado para recibir a un gran número de estudiantes, invitados y público institucional.</p>
                    </div>
                </div>

                <div class="tarjeta-equipo-horizontal">
                    <div class="icono-equipo-horizontal">🌡️</div>
                    <div class="texto-equipo-horizontal">
                        <h3>Climatización y confort</h3>
                        <p>Ambiente acondicionado para jornadas largas de actividades académicas o institucionales.</p>
                    </div>
                </div>

                <div class="tarjeta-equipo-horizontal">
                    <div class="icono-equipo-horizontal">🔌</div>
                    <div class="texto-equipo-horizontal">
                        <h3>Conectividad institucional</h3>
                        <p>Conexión para presentaciones, pantallas audiovisuales y apoyo a actividades académicas y administrativas.</p>
                    </div>
                </div>

                <div class="tarjeta-equipo-horizontal">
                    <div class="icono-equipo-horizontal">📚</div>
                    <div class="texto-equipo-horizontal">
                        <h3>Uso académico</h3>
                        <p>Ideal para clases abiertas, actos universitarios, jornadas y actividades con alto nivel de concurrencia.</p>
                    </div>
                </div>

            </div>
        </section>

        <!-- ==========================================================
           SECCIÓN DE DISTRIBUCIÓN Y ZONAS
           ========================================================== -->
        <section class="seccion-distribucion">
            <div class="caja-distribucion">
                <h2><i class="fa-solid fa-map-location-dot"></i> Distribución del Salón</h2>
                <p>El salón universitario está pensado para facilitar la visibilidad y el flujo de público de forma clara y ordenada:</p>
                <ul class="lista-sectores">
                    <li><strong>Platea principal:</strong> espacio central destinado a estudiantes, asistentes y público general.</li>
                    <li><strong>Sector de apoyo académico:</strong> zona para presentaciones, paneles y actividades institucionales.</li>
                    <li><strong>Cabina técnica:</strong> espacio de control para audio, video y coordinación del evento.</li>
                </ul>
            </div>
        </section>

        <!-- ==========================================================
           NORMAS DE USO INSTITUCIONALES
           ========================================================== -->
        <section class="seccion-normas">
            <article class="articulo-normas">
                <header class="encabezado-normas">
                    <h2>Protocolo y Normas de Uso del Salón</h2>
                    <p>Reglas generales para mantener el espacio en condiciones óptimas</p>
                </header>
                <div class="contenido-normas">
                    <p>• <strong>Reserva académica:</strong> el salón está orientado a actividades de alta concurrencia y requiere coordinación previa con la institución.</p>
                    <p>• <strong>Alimentación:</strong> está prohibido ingresar alimentos o bebidas en la zona de asientos durante eventos formales.</p>
                    <p>• <strong>Capacidad máxima:</strong> no se permite exceder el aforo establecido por seguridad y comodidad de los asistentes.</p>
                </div>
            </article>
        </section>
    </main>

    <?php require_once __DIR__ . '/../includes/footer.php'; ?>

</body>
</html>
