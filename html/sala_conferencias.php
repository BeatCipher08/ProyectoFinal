<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sala de Conferencias Principal</title>
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="../css/style_salon_uno.css">
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
                <h1>Sala de Conferencias</h1>
                <p>Espacio institucional pensado para conferencias, presentaciones técnicas, eventos académicos y actividades de alto nivel con apoyo audiovisual profesional.</p>
            </div>
            <div class="foto-salon conferencia-carousel" data-carousel>
                <img src="../imagenes/imagen_conferencia.jpg" alt="Sala de conferencias - vista 1" class="carousel-slide activo" data-slide>
                <img src="../imagenes/imagen_conferencia_2.jpg" alt="Sala de conferencias - vista 2" class="carousel-slide" data-slide>
                <div class="carousel-indicadores" aria-hidden="true">
                    <span class="indicador activo" data-indicador="0"></span>
                    <span class="indicador" data-indicador="1"></span>
                </div>
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
            <h2>Equipamiento técnico de la sala</h2>
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
                        <p>Consola, micrófonos inalámbricos y audio de alta calidad para eventos masivos.</p>
                    </div>
                </div>
                <div class="tarjeta-equipo-horizontal">
                    <div class="icono-equipo-horizontal">💺</div>
                    <div class="texto-equipo-horizontal">
                        <h3>Butacas de auditorio</h3>
                        <p>300 butacas con diseño cómodo para exposiciones, reuniones y jornadas prolongadas.</p>
                    </div>
                </div>
                <div class="tarjeta-equipo-horizontal">
                    <div class="icono-equipo-horizontal">🌡️</div>
                    <div class="texto-equipo-horizontal">
                        <h3>Climatización central</h3>
                        <p>Control ambiental para mantener un entorno cómodo durante actividades prolongadas.</p>
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
                        <p>Espacio para disertantes, presentaciones, ceremonias y actividades de gran escala.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="seccion-distribucion">
            <div class="caja-distribucion">
                <h2><i class="fa-solid fa-location-dot"></i> Ubicación y uso</h2>
                <p>La Sala de Conferencias se encuentra en el Ala Norte y está diseñada para actos institucionales, exposiciones, ponencias y reuniones de alto nivel.</p>
                <ul class="lista-sectores">
                    <li><strong>Auditorio principal:</strong> espacio central para asistentes, exposiciones y ponencias.</li>
                    <li><strong>Cabina técnica:</strong> área de control y apoyo audiovisual.</li>
                    <li><strong>Uso principal:</strong> conferencias, actos institucionales, jornadas y presentaciones técnicas.</li>
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
                    <p>• Está prohibido consumir alimentos o bebidas dentro del salón durante actividades formales o presentaciones técnicas.</p>
                </div>
            </article>
        </section>
    </main>

    <?php require_once __DIR__ . '/../includes/footer.php'; ?>

    <script>
        (function () {
            var carousel = document.querySelector('[data-carousel]');
            if (!carousel) {
                return;
            }

            var slides = carousel.querySelectorAll('[data-slide]');
            var indicadores = carousel.querySelectorAll('[data-indicador]');
            var indiceActual = 0;

            function mostrarSlide(indice) {
                slides.forEach(function (slide, i) {
                    slide.classList.toggle('activo', i === indice);
                });

                indicadores.forEach(function (indicador, i) {
                    indicador.classList.toggle('activo', i === indice);
                });
            }

            function siguienteSlide() {
                indiceActual = (indiceActual + 1) % slides.length;
                mostrarSlide(indiceActual);
            }

            indicadores.forEach(function (indicador, i) {
                indicador.addEventListener('click', function () {
                    indiceActual = i;
                    mostrarSlide(indiceActual);
                });
            });

            setInterval(siguienteSlide, 3800);
        })();
    </script>
</body>
</html>
