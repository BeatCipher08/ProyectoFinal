<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laboratorios de Química</title>
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
                <h1>Laboratorios de Química</h1>
                <p>Espacios especializados para prácticas experimentales, análisis químicos y actividades de laboratorio con alta seguridad y equipamiento técnico.</p>
            </div>
            <div class="foto-salon">
                <img src="../imagenes/quimica_laboratorio.jpg" alt="Laboratorio de química del instituto">
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
                    <div class="icono-equipo-horizontal">🧪</div>
                    <div class="texto-equipo-horizontal">
                        <h3>Mesadas de granito</h3>
                        <p>Mesadas de alta resistencia con piletas para trabajos experimentales y limpieza segura.</p>
                    </div>
                </div>
                <div class="tarjeta-equipo-horizontal">
                    <div class="icono-equipo-horizontal">💨</div>
                    <div class="texto-equipo-horizontal">
                        <h3>Campanas extractoras</h3>
                        <p>Sistema de extracción de gases para garantizar condiciones de seguridad en prácticas químicas.</p>
                    </div>
                </div>
                <div class="tarjeta-equipo-horizontal">
                    <div class="icono-equipo-horizontal">🚿</div>
                    <div class="texto-equipo-horizontal">
                        <h3>Duchas y lavaojos</h3>
                        <p>Equipamiento de seguridad disponible para emergencias y procedimientos de laboratorio.</p>
                    </div>
                </div>
                <div class="tarjeta-equipo-horizontal">
                    <div class="icono-equipo-horizontal">🔧</div>
                    <div class="texto-equipo-horizontal">
                        <h3>Conexiones de gas y agua</h3>
                        <p>Instalación técnica para experimentación con alta demanda operativa y precisión.</p>
                    </div>
                </div>
                <div class="tarjeta-equipo-horizontal">
                    <div class="icono-equipo-horizontal">🧰</div>
                    <div class="texto-equipo-horizontal">
                        <h3>Armarios de seguridad</h3>
                        <p>Almacenamiento controlado para reactivos y materiales sensibles.</p>
                    </div>
                </div>
                <div class="tarjeta-equipo-horizontal">
                    <div class="icono-equipo-horizontal">🛡️</div>
                    <div class="texto-equipo-horizontal">
                        <h3>Seguridad integral</h3>
                        <p>Diseño pensado para prácticas con sustancias químicas y protocolo institucional.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="seccion-distribucion">
            <div class="caja-distribucion">
                <h2><i class="fa-solid fa-location-dot"></i> Ubicación y uso</h2>
                <p>Los laboratorios de química se encuentran en el Ala Este, Planta Baja, en espacios pensados para prácticas experimentales y análisis de laboratorio.</p>
                <ul class="lista-sectores">
                    <li><strong>Zona de trabajo:</strong> mesadas y puestos de análisis para grupos pequeños.</li>
                    <li><strong>Seguridad:</strong> campanas, duchas de emergencia y control de sustancias peligrosas.</li>
                    <li><strong>Uso principal:</strong> prácticas químicas, análisis y experimentación aplicada.</li>
                </ul>
            </div>
        </section>

        <section class="seccion-normas">
            <article class="articulo-normas">
                <header class="encabezado-normas">
                    <h2>Normas de uso</h2>
                    <p>Recomendaciones para prácticas seguras y ordenadas</p>
                </header>
                <div class="contenido-normas">
                    <p>• El ingreso debe realizarse con indumentaria de seguridad y respetando las instrucciones del responsable.</p>
                    <p>• Los reactivos deben usarse bajo supervisión, cuidando la ventilación del espacio.</p>
                    <p>• Se exige un orden extremo de los materiales tras cada práctica para preservar la seguridad.</p>
                </div>
            </article>
        </section>

        <section class="seccion-documentos">
            <div class="caja-documentos">
                <span class="etiqueta-documentos">Apoyo</span>
                <h2>Bibliografía recomendada</h2>
                <p>Material de referencia para química general y análisis organometálico.</p>
                <div class="lista-documentos">
                    <div class="tarjeta-documento tarjeta-libro">
                        <div class="portada-libro color-quimica">
                            <span>Química</span>
                            <strong>General</strong>
                        </div>
                        <div class="texto-documento">
                            <strong>Química general</strong>
                            <small>Bibliografía base</small>
                        </div>
                    </div>
                    <div class="tarjeta-documento tarjeta-libro">
                        <div class="portada-libro color-quimica">
                            <span>Química</span>
                            <strong>Orgánica</strong>
                        </div>
                        <div class="texto-documento">
                            <strong>Química orgánica</strong>
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
