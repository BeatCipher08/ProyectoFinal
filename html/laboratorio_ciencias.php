<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laboratorio de Ciencias</title>
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
                <h1>Laboratorio de Ciencias</h1>
                <p>Espacio multidisciplinario ubicado en planta alta, pensado para proyectos de investigación y actividades que combinan distintas ramas científicas.</p>
            </div>
            <div class="foto-salon">
                <img src="../imagenes/imageninstituto.png" alt="Laboratorio de ciencias del instituto">
            </div>
        </section>

        <section class="datos-rapidos">
            <div class="dato-item">
                <h2>1</h2>
                <p>Unidad</p>
            </div>
            <div class="dato-item">
                <h2>Ala Este</h2>
                <p>Ubicación</p>
            </div>
            <div class="dato-item">
                <h2>35</h2>
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
                    <div class="icono-equipo-horizontal">📊</div>
                    <div class="texto-equipo-horizontal">
                        <h3>Mesadas perimetrales</h3>
                        <p>Espacio amplio para experimentación y preparación de materiales de investigación.</p>
                    </div>
                </div>
                <div class="tarjeta-equipo-horizontal">
                    <div class="icono-equipo-horizontal">🧭</div>
                    <div class="texto-equipo-horizontal">
                        <h3>Medición científica</h3>
                        <p>Equipamiento de medición para distintas ramas del conocimiento científico.</p>
                    </div>
                </div>
                <div class="tarjeta-equipo-horizontal">
                    <div class="icono-equipo-horizontal">📘</div>
                    <div class="texto-equipo-horizontal">
                        <h3>Pizarra digital</h3>
                        <p>Herramienta visual para procedimientos, análisis y demostraciones.</p>
                    </div>
                </div>
                <div class="tarjeta-equipo-horizontal">
                    <div class="icono-equipo-horizontal">🔊</div>
                    <div class="texto-equipo-horizontal">
                        <h3>Sistema de audio</h3>
                        <p>Sonido de calidad para exposiciones, presentaciones y explicaciones técnicas.</p>
                    </div>
                </div>
                <div class="tarjeta-equipo-horizontal">
                    <div class="icono-equipo-horizontal">🌡️</div>
                    <div class="texto-equipo-horizontal">
                        <h3>Climatización central</h3>
                        <p>Control ambiental para un entorno estable durante actividades prolongadas.</p>
                    </div>
                </div>
                <div class="tarjeta-equipo-horizontal">
                    <div class="icono-equipo-horizontal">🧪</div>
                    <div class="texto-equipo-horizontal">
                        <h3>Material experimental</h3>
                        <p>Diseñado para actividades con fundamentos multidisciplinarios y de investigación.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="seccion-distribucion">
            <div class="caja-distribucion">
                <h2><i class="fa-solid fa-location-dot"></i> Ubicación y uso</h2>
                <p>El laboratorio de ciencias se ubica en el Ala Este, Planta Alta, y es un espacio polivalente para proyectos e investigaciones científicas.</p>
                <ul class="lista-sectores">
                    <li><strong>Zona de trabajo:</strong> mesadas perimetrales y centrales para actividades grupales.</li>
                    <li><strong>Zona técnica:</strong> equipamiento de medición y pizarra digital para análisis.</li>
                    <li><strong>Uso principal:</strong> experimentación, investigación y proyectos interdisciplinarios.</li>
                </ul>
            </div>
        </section>

        <section class="seccion-normas">
            <article class="articulo-normas">
                <header class="encabezado-normas">
                    <h2>Normas de uso</h2>
                    <p>Requisitos para investigación y práctica</p>
                </header>
                <div class="contenido-normas">
                    <p>• Debe respetarse el protocolo de cada experiencia antes de iniciar cualquier procedimiento.</p>
                    <p>• El equipo y los materiales deben quedar en condiciones adecuadas tras cada sesión.</p>
                    <p>• El laboratorio es un espacio de trabajo serio, por lo que se exige orden y cuidado constante.</p>
                </div>
            </article>
        </section>
    </main>
</body>
</html>
