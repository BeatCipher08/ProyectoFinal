<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laboratorio / Taller de Mecánica</title>
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="../css/style_salon_grande_uno.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>
    <?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    require_once __DIR__ . '/../includes/barra_superior.php';
    ?>

    <main>
        <section class="hero-salon hero-grande">
            <div class="contenido-salon">
                <h1>Laboratorio / Taller de Mecánica</h1>
                <p>Amplio espacio industrial para prácticas de mecánica automotriz, matricería, neumática y estructuras metálicas, ubicado detrás de la sala de conferencias principal.</p>
            </div>
            <div class="foto-salon">
                <img src="../imagenes/imageninstituto.png" alt="Taller de mecánica del instituto">
            </div>
        </section>

        <section class="datos-rapidos">
            <div class="dato-item">
                <h2>1</h2>
                <p>Unidad</p>
            </div>
            <div class="dato-item">
                <h2>Ala Norte</h2>
                <p>Ubicación</p>
            </div>
            <div class="dato-item">
                <h2>45</h2>
                <p>Capacidad</p>
            </div>
            <div class="dato-item">
                <h2>07:00 - 23:45</h2>
                <p>Horario</p>
            </div>
        </section>

        <section class="seccion-equipamiento">
            <h2>Equipamiento del taller</h2>
            <div class="contenedor-tarjetas-horizontales">
                <div class="tarjeta-equipo-horizontal">
                    <div class="icono-equipo-horizontal">🛠️</div>
                    <div class="texto-equipo-horizontal">
                        <h3>Bancos de trabajo</h3>
                        <p>Mesas pesadas con mordazas y herramientas para trabajos metálicos y mecánicos.</p>
                    </div>
                </div>
                <div class="tarjeta-equipo-horizontal">
                    <div class="icono-equipo-horizontal">⚙️</div>
                    <div class="texto-equipo-horizontal">
                        <h3>Herramientas especializadas</h3>
                        <p>Equipamiento para trabajos de mecánica, desarmado y armado de piezas.</p>
                    </div>
                </div>
                <div class="tarjeta-equipo-horizontal">
                    <div class="icono-equipo-horizontal">🔥</div>
                    <div class="texto-equipo-horizontal">
                        <h3>Soldadura</h3>
                        <p>Equipos de soldadura para reparación, unión y montaje de estructuras metálicas.</p>
                    </div>
                </div>
                <div class="tarjeta-equipo-horizontal">
                    <div class="icono-equipo-horizontal">🚗</div>
                    <div class="texto-equipo-horizontal">
                        <h3>Gatas hidráulicas</h3>
                        <p>Herramientas pesadas para trabajos de mantenimiento y estudio automotriz.</p>
                    </div>
                </div>
                <div class="tarjeta-equipo-horizontal">
                    <div class="icono-equipo-horizontal">🌬️</div>
                    <div class="texto-equipo-horizontal">
                        <h3>Ventilación industrial</h3>
                        <p>Sistema de extracción adecuado para tareas de mecanizado y soldadura.</p>
                    </div>
                </div>
                <div class="tarjeta-equipo-horizontal">
                    <div class="icono-equipo-horizontal">🧱</div>
                    <div class="texto-equipo-horizontal">
                        <h3>Estructuras metálicas</h3>
                        <p>Espacio adecuado para proyectos de montaje, ajuste y manipulación técnica.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="seccion-distribucion">
            <div class="caja-distribucion">
                <h2><i class="fa-solid fa-location-dot"></i> Ubicación y uso</h2>
                <p>El taller de mecánica está ubicado en el Ala Norte, dentro del galpón de mecánica, inmediatamente detrás de la sala de conferencias principal.</p>
                <ul class="lista-sectores">
                    <li><strong>Zona de trabajo:</strong> bancos de trabajo, herramientas y áreas de montaje.</li>
                    <li><strong>Zona de seguridad:</strong> ventilación industrial y control de actividades pesadas.</li>
                    <li><strong>Uso principal:</strong> mecánica automotriz, matricería, neumática y estructuras metálicas.</li>
                </ul>
            </div>
        </section>

        <section class="seccion-normas">
            <article class="articulo-normas">
                <header class="encabezado-normas">
                    <h2>Normas de uso</h2>
                    <p>Seguridad y cuidado industrial</p>
                </header>
                <div class="contenido-normas">
                    <p>• Se debe usar indumentaria y elementos de protección adecuados durante cada práctica.</p>
                    <p>• Los equipos pesados deben manipularse con precaución y bajo supervisión.</p>
                    <p>• La zona debe mantenerse ordenada para evitar riesgos asociados a herramientas y piezas.</p>
                </div>
            </article>
        </section>
    </main>
</body>
</html>
