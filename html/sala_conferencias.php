<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sala de Conferencias Principal</title>
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
                <h1>Sala de Conferencias Principal</h1>
                <p>Auditorio institucional de gran escala diseñado para conferencias, eventos masivos, ponencias y actividades académicas de alta concurrencia.</p>
            </div>
            <div class="foto-salon">
                <img src="../imagenes/imageninstituto.png" alt="Sala de conferencias del instituto">
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
                <h2>300</h2>
                <p>Capacidad</p>
            </div>
            <div class="dato-item">
                <h2>07:00 - 23:45</h2>
                <p>Horario</p>
            </div>
        </section>

        <section class="seccion-equipamiento">
            <h2>Equipamiento del espacio</h2>
            <div class="contenedor-tarjetas-horizontales">
                <div class="tarjeta-equipo-horizontal">
                    <div class="icono-equipo-horizontal">📺</div>
                    <div class="texto-equipo-horizontal">
                        <h3>Pantalla gigante</h3>
                        <p>Pantalla de alta definición para exposiciones, conferencias y presentaciones institucionales.</p>
                    </div>
                </div>
                <div class="tarjeta-equipo-horizontal">
                    <div class="icono-equipo-horizontal">🎙️</div>
                    <div class="texto-equipo-horizontal">
                        <h3>Sistema de audio profesional</h3>
                        <p>Consola, micrófonos inalámbricos y audio de alta calidad para audiencias masivas.</p>
                    </div>
                </div>
                <div class="tarjeta-equipo-horizontal">
                    <div class="icono-equipo-horizontal">💺</div>
                    <div class="texto-equipo-horizontal">
                        <h3>Butacas ergonómicas</h3>
                        <p>300 butacas con diseño cómodo para eventos prolongados y jornadas de trabajo.</p>
                    </div>
                </div>
                <div class="tarjeta-equipo-horizontal">
                    <div class="icono-equipo-horizontal">🌡️</div>
                    <div class="texto-equipo-horizontal">
                        <h3>Climatización central</h3>
                        <p>Control ambiental para mantener un entorno cómodo durante los eventos.</p>
                    </div>
                </div>
                <div class="tarjeta-equipo-horizontal">
                    <div class="icono-equipo-horizontal">📡</div>
                    <div class="texto-equipo-horizontal">
                        <h3>Conectividad audiovisual</h3>
                        <p>Instalación preparada para presentaciones multimedia y transmisión institucional.</p>
                    </div>
                </div>
                <div class="tarjeta-equipo-horizontal">
                    <div class="icono-equipo-horizontal">🎭</div>
                    <div class="texto-equipo-horizontal">
                        <h3>Escenario y apoyo técnico</h3>
                        <p>Espacio para disertantes, presentaciones y actividades ceremoniales.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="seccion-distribucion">
            <div class="caja-distribucion">
                <h2><i class="fa-solid fa-location-dot"></i> Ubicación y uso</h2>
                <p>La Sala de Conferencias Principal se encuentra en el Ala Norte, delante del taller de mecánica, y está diseñada para eventos institucionales masivos.</p>
                <ul class="lista-sectores">
                    <li><strong>Auditorio principal:</strong> espacio central para asistentes y ponencias.</li>
                    <li><strong>Cabina técnica:</strong> área de control y apoyo audiovisual.</li>
                    <li><strong>Uso principal:</strong> conferencias, eventos, graduaciones y exposiciones institucionales.</li>
                </ul>
            </div>
        </section>

        <section class="seccion-normas">
            <article class="articulo-normas">
                <header class="encabezado-normas">
                    <h2>Normas de uso</h2>
                    <p>Organización para eventos institucionales</p>
                </header>
                <div class="contenido-normas">
                    <p>• La coordinación técnica debe realizarse con anticipación para garantizar el correcto funcionamiento del evento.</p>
                    <p>• Se deben respetar las normas de aforo y flujo de ingreso para garantizar seguridad.</p>
                    <p>• Está prohibido consumir alimentos o bebidas dentro del salón durante actividades formales.</p>
                </div>
            </article>
        </section>
    </main>
</body>
</html>
