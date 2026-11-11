<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$novedades = [
    [
        'id' => 'evento-quimica',
        'categoria' => 'Evento académico',
        'titulo' => 'Semana de química aplicada abre inscripciones para talleres y demostraciones',
        'resumen' => 'La Escuela de Química invita a estudiantes, docentes y público general a participar de una semana de actividades prácticas con laboratorio abierto y charlas de innovación.',
        'fecha' => '18 de noviembre de 2026',
        'fecha_iso' => '2026-11-18',
        'imagen' => '../imagenes/quimica_laboratorio.jpg',
        'contenido' => [
            'El Instituto Tecnológico abrirá la próxima semana la “Semana de Química Aplicada”, una serie de actividades orientadas a fortalecer la formación práctica en laboratorios, análisis y nuevas tecnologías ambientales.',
            'Durante la jornada inaugural, estudiantes de la carrera de Química Industrial presentarán experiencias de laboratorio en temas como análisis de materiales, tratamiento de aguas y sostenibilidad industrial.',
            'Además, se ofrecerán talleres dirigidos por docentes y profesionales del sector para explorar soluciones innovadoras en procesos productivos y gestión de residuos, con una mirada hacia la eficiencia y la sostenibilidad.',
            'La actividad se realizará en los laboratorios del bloque de ciencias y contará con una muestra abierta para familias y público interesado. Se recomienda reservar cupo previo por el sistema institucional o directamente con la coordinación académica.',
            'La apertura será el 18 de noviembre, a las 09:30, y se extenderá durante toda la semana con visitas guiadas, demostraciones prácticas y conferencias de cierre.'
        ]
    ],
    [
        'id' => 'pc-escritorio',
        'categoria' => 'Infraestructura',
        'titulo' => 'Próxima llegada de nuevas PCs de escritorio para el salón de informática',
        'resumen' => 'La institución incorporará equipos de escritorio de última generación para reforzar las prácticas de programación, diseño y análisis de datos.',
        'fecha' => '22 de noviembre de 2026',
        'fecha_iso' => '2026-11-22',
        'imagen' => '../imagenes/imageninstituto.jpg',
        'contenido' => [
            'El área de informática anunció la próxima incorporación de un nuevo lote de computadoras de escritorio para el salón de práctica del instituto, con equipos diseñados para satisfacer las demandas actuales de programación, diseño gráfico, edición audiovisual y análisis de datos.',
            'La adquisición incluye monitores de mayor resolución, unidades de procesamiento ampliadas y periféricos actualizados, con el objetivo de mejorar la experiencia de aprendizaje y reducir tiempos de espera en las prácticas.',
            'Los docentes ya prepararon la planificación de uso del nuevo equipamiento para el próximo ciclo lectivo, con actividades guiadas y mayor capacidad de trabajo en equipo para proyectos de investigación y desarrollo.',
            'La entrega está prevista para el 22 de noviembre, y la puesta en funcionamiento será gradual para evitar interrupciones en las clases habituales. El personal administrativo y docente seguirá capacitando al alumnado en el uso de los nuevos sistemas.',
            'La iniciativa refleja el compromiso institucional con la actualización tecnológica y la mejora de los espacios de aprendizaje de la comunidad educativa.'
        ]
    ],
    [
        'id' => 'graduacion-quimica',
        'categoria' => 'Graduación',
        'titulo' => 'Se celebrará la graduación del grupo de Química Industrial',
        'resumen' => 'Un grupo de estudiantes egresados de la carrera de Química Industrial culminará su etapa académica con una ceremonia institucional.',
        'fecha' => '27 de noviembre de 2026',
        'fecha_iso' => '2026-11-27',
        'imagen' => '../imagenes/imagen_conferencia.jpg',
        'contenido' => [
            'La carrera de Química Industrial celebrará la graduación de un nuevo grupo de egresados, una instancia de gran valor para la comunidad educativa y para quienes acompañaron a los estudiantes durante su formación.',
            'La ceremonia incluirá palabras de bienvenida de autoridades académicas, reconocimiento a los estudiantes destacados y una proyección de la trayectoria profesional de la promoción.',
            'Además, el evento contará con una muestra de trabajos finales y proyectos realizados a lo largo de la carrera, con especial énfasis en innovación, sostenibilidad y procesos industriales aplicados a la realidad del sector.',
            'La graduación está programada para el 27 de noviembre, a las 18:00, en el auditorio principal del instituto. La comunidad estará invitada a acompañar a la promoción en este cierre de ciclo académico.',
            'La actividad se enmarca dentro del proceso de fortalecimiento de la vinculación con la industria y la formación de profesionales especializados en procesos químicos, seguridad, calidad y producción sostenible.'
        ]
    ]
];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novedades | Sistema de Gestión</title>
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="../css/style_pagina_principal.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <?php require_once __DIR__ . '/../includes/barra_superior.php'; ?>

    <main class="main-hub">
        <section class="hero-hub">
            <span class="eyebrow">Comunidad</span>
            <h1>Novedades institucionales</h1>
            <p>Enterate de los eventos, inauguraciones, avances y actividades relevantes para la comunidad educativa del instituto.</p>
        </section>

        <section class="hub-listado">
            <?php foreach ($novedades as $novedad): ?>
                <article class="card-hub">
                    <img src="<?php echo htmlspecialchars($novedad['imagen']); ?>" alt="<?php echo htmlspecialchars($novedad['titulo']); ?>" loading="lazy">
                    <div class="card-body">
                        <span class="badge"><?php echo htmlspecialchars($novedad['categoria']); ?></span>
                        <h2><?php echo htmlspecialchars($novedad['titulo']); ?></h2>
                        <p class="meta-noticia">
                            <time datetime="<?php echo htmlspecialchars($novedad['fecha_iso']); ?>"><?php echo htmlspecialchars($novedad['fecha']); ?></time>
                        </p>
                        <p><?php echo htmlspecialchars($novedad['resumen']); ?></p>
                        <a href="novedad.php?id=<?php echo urlencode($novedad['id']); ?>">Leer más</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </section>
    </main>

    <footer>
        <div class="footer-grid">
            <div>
                <h3>Instituto Tecnológico</h3>
                <p>Sistema de Gestión de Espacios y Recursos.</p>
            </div>
            <div>
                <h3>Enlaces</h3>
                <ul>
                    <li>Inicio</li>
                    <li>Reservas</li>
                    <li>Novedades</li>
                    <li>Contacto</li>
                </ul>
            </div>
            <div>
                <h3>Contacto</h3>
                <p>📍 Esquina Alfredo Zitarrosa y Avenida Republica Argentina, Tacuarembo, Uruguay</p>
                <p>📞 4632-0000</p>
                <p>📧 contacto@instituto.edu.uy</p>
            </div>
        </div>
        <hr>
        <p class="copy">© 2026 Instituto Tecnológico de Tacuarembó - Todos los derechos reservados.</p>
    </footer>
</body>
</html>
