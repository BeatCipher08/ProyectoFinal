<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laboratorios de Informática</title>
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
                <h1>Laboratorios de Informática</h1>
                <p>Salas equipadas con estaciones de trabajo de alta velocidad, conectividad de red y recursos para programación, diseño e investigación digital.</p>
            </div>
            <div class="foto-salon">
                <img src="../imagenes/imageninstituto.jpg" alt="Laboratorio de informática del instituto">
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
                <h2>30</h2>
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
                    <div class="icono-equipo-horizontal">💻</div>
                    <div class="texto-equipo-horizontal">
                        <h3>30 PCs de escritorio</h3>
                        <p>Estaciones de trabajo modernas para programación, diseño, edición y análisis de datos.</p>
                    </div>
                </div>
                <div class="tarjeta-equipo-horizontal">
                    <div class="icono-equipo-horizontal">🖥️</div>
                    <div class="texto-equipo-horizontal">
                        <h3>Pantallas LED</h3>
                        <p>Monitores de alta resolución para tareas técnicas y visualización de contenido.</p>
                    </div>
                </div>
                <div class="tarjeta-equipo-horizontal">
                    <div class="icono-equipo-horizontal">🪑</div>
                    <div class="texto-equipo-horizontal">
                        <h3>Sillas ergonómicas</h3>
                        <p>Diseño pensado para largas sesiones de trabajo y aprendizaje.</p>
                    </div>
                </div>
                <div class="tarjeta-equipo-horizontal">
                    <div class="icono-equipo-horizontal">🌬️</div>
                    <div class="texto-equipo-horizontal">
                        <h3>Aire acondicionado</h3>
                        <p>Climatización para mantener un ambiente confortable durante clases y prácticas.</p>
                    </div>
                </div>
                <div class="tarjeta-equipo-horizontal">
                    <div class="icono-equipo-horizontal">📡</div>
                    <div class="texto-equipo-horizontal">
                        <h3>Red Ethernet gigabit</h3>
                        <p>Conectividad ultra rápida para competencias, trabajosy transferencias de datos.</p>
                    </div>
                </div>
                <div class="tarjeta-equipo-horizontal">
                    <div class="icono-equipo-horizontal">📽️</div>
                    <div class="texto-equipo-horizontal">
                        <h3>Proyector interactivo</h3>
                        <p>Herramienta para clases demostrativas, talleres y presentaciones grupales.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="seccion-distribucion">
            <div class="caja-distribucion">
                <h2><i class="fa-solid fa-location-dot"></i> Ubicación y uso</h2>
                <p>Estos laboratorios se encuentran en el Ala Este, Planta Baja, destinados a programación, diseño, prácticas y trabajos de informática.</p>
                <ul class="lista-sectores">
                    <li><strong>Estaciones de trabajo:</strong> 30 equipos conectados en red para actividades simultáneas.</li>
                    <li><strong>Docencia:</strong> proyección multimedia y soporte técnico para clases interactivas.</li>
                    <li><strong>Uso principal:</strong> programación, diseño y prácticas de sistemas.</li>
                </ul>
            </div>
        </section>

        <section class="seccion-normas">
            <article class="articulo-normas">
                <header class="encabezado-normas">
                    <h2>Normas de uso</h2>
                    <p>Uso responsable de espacios tecnológicos</p>
                </header>
                <div class="contenido-normas">
                    <p>• El uso de los equipos debe ser responsable y respetando las normas de seguridad digital.</p>
                    <p>• Se debe mantener el orden de cada puesto de trabajo durante y después del uso.</p>
                    <p>• Los materiales y equipos deben quedar conectados y operativos para el siguiente turno.</p>
                </div>
            </article>
        </section>

        <section class="seccion-documentos">
            <div class="caja-documentos">
                <span class="etiqueta-documentos">Apoyo</span>
                <h2>Bibliografía recomendada</h2>
                <p>Libros clave para programación, algoritmos y diseño de software.</p>
                <div class="lista-documentos">
                    <div class="tarjeta-documento tarjeta-libro">
                        <div class="portada-libro color-info">
                            <span>Informática</span>
                            <strong>Algoritmos</strong>
                        </div>
                        <div class="texto-documento">
                            <strong>Estructuras de datos</strong>
                            <small>Bibliografía base</small>
                        </div>
                    </div>
                    <div class="tarjeta-documento tarjeta-libro">
                        <div class="portada-libro color-info">
                            <span>Software</span>
                            <strong>Diseño</strong>
                        </div>
                        <div class="texto-documento">
                            <strong>Diseño de software</strong>
                            <small>Material de apoyo</small>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php require_once __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
