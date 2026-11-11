<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laboratorios de Física</title>
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
                <h1>Laboratorios de Física</h1>
                <p>Espacios especializados para experimentos de mecánica, óptica, electricidad y termodinámica con mesas estables e instrumental de precisión.</p>
            </div>
            <div class="foto-salon">
                <img src="../imagenes/imageninstituto.jpg" alt="Laboratorio de física del instituto">
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
                    <div class="icono-equipo-horizontal">🧲</div>
                    <div class="texto-equipo-horizontal">
                        <h3>Mesadas estables</h3>
                        <p>Superficies firmes y resistentes para trabajos de precisión y montaje experimental.</p>
                    </div>
                </div>
                <div class="tarjeta-equipo-horizontal">
                    <div class="icono-equipo-horizontal">⚡</div>
                    <div class="texto-equipo-horizontal">
                        <h3>Tomas reforzadas</h3>
                        <p>Tomacorrientes de alta capacidad para equipamiento eléctrico y instrumental técnico.</p>
                    </div>
                </div>
                <div class="tarjeta-equipo-horizontal">
                    <div class="icono-equipo-horizontal">🔬</div>
                    <div class="texto-equipo-horizontal">
                        <h3>Kits de física</h3>
                        <p>Instrumental didáctico para demostraciones y prácticas de mecánica y electricidad.</p>
                    </div>
                </div>
                <div class="tarjeta-equipo-horizontal">
                    <div class="icono-equipo-horizontal">📊</div>
                    <div class="texto-equipo-horizontal">
                        <h3>Osciloscopios</h3>
                        <p>Equipos de medición para análisis de señales eléctricas y fenómenos físicos.</p>
                    </div>
                </div>
                <div class="tarjeta-equipo-horizontal">
                    <div class="icono-equipo-horizontal">🧮</div>
                    <div class="texto-equipo-horizontal">
                        <h3>Multímetros</h3>
                        <p>Herramientas de medición para circuitos, corriente y resistencias.</p>
                    </div>
                </div>
                <div class="tarjeta-equipo-horizontal">
                    <div class="icono-equipo-horizontal">📽️</div>
                    <div class="texto-equipo-horizontal">
                        <h3>Proyección multimedia</h3>
                        <p>Equipamiento para la visualización de fenómenos físicos y contenidos docentes.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="seccion-distribucion">
            <div class="caja-distribucion">
                <h2><i class="fa-solid fa-location-dot"></i> Ubicación y uso</h2>
                <p>Los laboratorios de física están ubicados en el Ala Este, Planta Baja, diseñados para la experimentación práctica y la enseñanza de fundamentos científicos.</p>
                <ul class="lista-sectores">
                    <li><strong>Zona experimental:</strong> puestos para montaje de circuitos y equipos de medición.</li>
                    <li><strong>Zona docente:</strong> espacio para explicación, demostración y observación general.</li>
                    <li><strong>Uso principal:</strong> mecánica, óptica, electricidad y termodinámica.</li>
                </ul>
            </div>
        </section>

        <section class="seccion-normas">
            <article class="articulo-normas">
                <header class="encabezado-normas">
                    <h2>Normas de uso</h2>
                    <p>Condiciones para prácticas seguras</p>
                </header>
                <div class="contenido-normas">
                    <p>• Todo montaje debe revisarse antes de energizarse para prevenir fallas eléctricas.</p>
                    <p>• Los instrumentos deben manipularse con cuidado y devolverse a su lugar tras cada práctica.</p>
                    <p>• Se recomienda mantener el orden del espacio para garantizar la seguridad y la precisión del trabajo.</p>
                </div>
            </article>
        </section>

        <?php
        $rolUsuario = strtolower((string)($_SESSION['rol'] ?? ''));
        $puedeVerMaterial = isset($_SESSION['usuario_id']) && in_array($rolUsuario, ['alumno', 'profesor', 'admin'], true);
        ?>

        <?php if ($puedeVerMaterial): ?>
            <section class="seccion-documentos">
                <div class="caja-documentos">
                    <span class="etiqueta-documentos">Apoyo</span>
                    <h2>Documentación del laboratorio</h2>
                    <p>Material útil para preparar prácticas, ejercicios y trabajo experimental en este espacio.</p>

                    <div class="lista-documentos">
                        <a class="tarjeta-documento" href="../documentos_anexo/F%C3%ADsica%20Raymond%20A.%20Serway%20-%20Volumen%20I%20-%206ta%20Edici%C3%B3n.pdf" target="_blank" rel="noopener noreferrer" download>
                            <span class="icono-documento"><i class="fa-solid fa-file-pdf"></i></span>
                            <div class="texto-documento">
                                <strong>Física - Volumen I</strong>
                                <small>Descargar PDF</small>
                            </div>
                        </a>

                        <a class="tarjeta-documento" href="../documentos_anexo/F%C3%ADsica%20Raymond%20A.%20Serway%20-%20Volumen%20II%20-%206ta%20Edici%C3%B3n.pdf" target="_blank" rel="noopener noreferrer" download>
                            <span class="icono-documento"><i class="fa-solid fa-file-pdf"></i></span>
                            <div class="texto-documento">
                                <strong>Física - Volumen II</strong>
                                <small>Descargar PDF</small>
                            </div>
                        </a>

                        <a class="tarjeta-documento" href="https://www.geogebra.org/classic?lang=es" target="_blank" rel="noopener noreferrer">
                            <span class="icono-documento"><i class="fa-solid fa-function"></i></span>
                            <div class="texto-documento">
                                <strong>GeoGebra</strong>
                                <small>Abrir en otra pestaña</small>
                            </div>
                        </a>

                        <a class="tarjeta-documento" href="https://www.desmos.com/scientific?lang=es" target="_blank" rel="noopener noreferrer">
                            <span class="icono-documento"><i class="fa-solid fa-calculator"></i></span>
                            <div class="texto-documento">
                                <strong>Calculadora gráfica</strong>
                                <small>Abrir en otra pestaña</small>
                            </div>
                        </a>
                    </div>
                </div>
            </section>
        <?php endif; ?>
    </main>

    <?php require_once __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
