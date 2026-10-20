<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laboratorios de Biología</title>
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
                <h1>Laboratorios de Biología</h1>
                <p>Espacios orientados a la observación microscópica, estudio celular y ciencias biológicas con iluminación y equipamiento técnico adecuados.</p>
            </div>
            <div class="foto-salon">
                <img src="../imagenes/imageninstituto.png" alt="Laboratorio de biología del instituto">
            </div>
        </section>

        <section class="datos-rapidos">
            <div class="dato-item">
                <h2>2</h2>
                <p>Unidades</p>
            </div>
            <div class="dato-item">
                <h2>Ala Este</h2>
                <p>Ubicación</p>
            </div>
            <div class="dato-item">
                <h2>25</h2>
                <p>Capacidad</p>
            </div>
            <div class="dato-item">
                <h2>07:00 - 23:45</h2>
                <p>Horario</p>
            </div>
        </section>

        <section class="seccion-equipamiento">
            <h2>Equipamiento del laboratorio</h2>
            <div class="contenedor-tarjetas-horizontales">
                <div class="tarjeta-equipo-horizontal">
                    <div class="icono-equipo-horizontal">🔬</div>
                    <div class="texto-equipo-horizontal">
                        <h3>Microscopios ópticos</h3>
                        <p>Instrumental clave para observación de preparaciones biológicas y análisis celular.</p>
                    </div>
                </div>
                <div class="tarjeta-equipo-horizontal">
                    <div class="icono-equipo-horizontal">📐</div>
                    <div class="texto-equipo-horizontal">
                        <h3>Estereomicroscopios</h3>
                        <p>Herramientas para estudios detallados y observación tridimensional de muestras.</p>
                    </div>
                </div>
                <div class="tarjeta-equipo-horizontal">
                    <div class="icono-equipo-horizontal">🧪</div>
                    <div class="texto-equipo-horizontal">
                        <h3>Mesadas con piletas</h3>
                        <p>Espacios de limpieza y preparación de materiales con agua y sistemas de lavado.</p>
                    </div>
                </div>
                <div class="tarjeta-equipo-horizontal">
                    <div class="icono-equipo-horizontal">❄️</div>
                    <div class="texto-equipo-horizontal">
                        <h3>Heladeras para muestras</h3>
                        <p>Conservación segura de ensayos biológicos y material experimental.</p>
                    </div>
                </div>
                <div class="tarjeta-equipo-horizontal">
                    <div class="icono-equipo-horizontal">🧫</div>
                    <div class="texto-equipo-horizontal">
                        <h3>Material de vidrio</h3>
                        <p>Portaobjetos, cubreobjetos, tubos y elementos esenciales para la práctica.</p>
                    </div>
                </div>
                <div class="tarjeta-equipo-horizontal">
                    <div class="icono-equipo-horizontal">💡</div>
                    <div class="texto-equipo-horizontal">
                        <h3>Iluminación óptima</h3>
                        <p>Ambiente luminoso y controlado para observaciones de alta precisión.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="seccion-distribucion">
            <div class="caja-distribucion">
                <h2><i class="fa-solid fa-location-dot"></i> Ubicación y uso</h2>
                <p>Los laboratorios de biología están ubicados en el Ala Este, Planta Baja, preparados para la práctica observacional, enseñanza y estudio de ciencias biológicas.</p>
                <ul class="lista-sectores">
                    <li><strong>Zona de observación:</strong> microscopios y estereomicroscopios para análisis detallado.</li>
                    <li><strong>Zona de preparación:</strong> mesadas con piletas y almacenamiento de materiales.</li>
                    <li><strong>Uso principal:</strong> observación, análisis y práctica experimental biológica.</li>
                </ul>
            </div>
        </section>

        <section class="seccion-normas">
            <article class="articulo-normas">
                <header class="encabezado-normas">
                    <h2>Normas de uso</h2>
                    <p>Buenas prácticas para laboratorio</p>
                </header>
                <div class="contenido-normas">
                    <p>• Se debe respetar la limpieza y la correcta disposición de muestras e instrumentos.</p>
                    <p>• El manejo de materiales debe realizarse con guantes y según el protocolo indicado.</p>
                    <p>• Todo material de vidrio y reactivo debe devolverse a su lugar tras la práctica.</p>
                </div>
            </article>
        </section>
    </main>
</body>
</html>
