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
    <title>Sistema de Gestión de Espacios y Recursos</title>

    <!-- Archivos CSS -->
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="../css/style_pagina_principal.css">

    <!-- Fuentes e Íconos -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>

<body>
    <!-- ==========================================================
       BARRA DE NAVEGACIÓN SUPERIOR (HEADER GLOBAL)
       ========================================================== -->
    <?php require_once __DIR__ . '/../includes/barra_superior.php'; ?>

    <!-- CONTENEDOR PRINCIPAL -->
    <main>
        <!-- ======================================================
           SECCIÓN PORTADA (HERO)
           ====================================================== -->
        <section class="hero">
            <div class="contenido">
                <h1>Sistema de Gestión de Espacios y Recursos</h1>
                <p>Reserva laboratorios, salones, biblioteca y equipamiento de manera fácil y rápida.</p>
            </div>
            <div class="foto">
               <!-- Se mantiene sin lazy-loading por estar arriba del todo (Above the fold) -->
               <img src="../imagenes/imageninstituto.jpg" alt="Vista exterior del instituto tecnológico">
            </div>
        </section>

        <!-- ======================================================
           SECCIÓN ESTADÍSTICAS
           ====================================================== -->
        <section class="estadisticas">
            <div class="dato">
                <h2>+1200</h2>
                <p>Estudiantes</p>
            </div>
            <div class="dato">
                <h2>15</h2>
                <p>Laboratorios</p>
            </div>
            <div class="dato">
                <h2>1</h2>
                <p>Biblioteca</p>
            </div>
            <div class="dato">
                <h2>+50</h2>
                <p>Equipos disponibles</p>
            </div>
        </section>

        <!-- ======================================================
           SECCIÓN ANUNCIO IMPORTANTE
           ====================================================== -->
        <section class="seccion-anuncio">
            <article class="articulo-noticia">
                <header class="encabezado-noticia">
                    <h2 class="titulo-noticia">¡Bienvenidos al nuevo portal institucional!</h2>
                    <p class="subtitulo-noticia">Lanzamiento oficial de la plataforma web</p>
                    <div class="metadatos-noticia">
                        <span class="autor-noticia">Por: Administración Educativa</span> | 
                        <time class="fecha-noticia" datetime="2026-08-05">5 de Agosto, 2026</time>
                    </div>
                </header>
                <div class="contenido-noticia">
                    <p>¡Hola a toda la comunidad educativa! Les damos la más cordial bienvenida a nuestro nuevo sitio web.</p>
                    <p>La principal funcionalidad de esta página es permitir a estudiantes y docentes organizar, gestionar y reservar espacios físicos como laboratorios y salones, así como equipamiento tecnológico, de una forma mucho más ágil, rápida y centralizada.</p>
                    <p>Queremos avisarles que, en un futuro cercano, <strong>este recuadro verde será utilizado exclusivamente para publicar noticias urgentes</strong>, anuncios institucionales de importancia y cambios de último minuto que deban conocer al instante.</p>
                    <p>¡Esperamos que le saquen el máximo provecho a esta nueva herramienta! Saludos cordiales.</p>
                </div>
            </article>
        </section>

        <!-- ======================================================
           SECCIÓN TARJETAS DE RESERVA Y CARRUSEL
           ====================================================== -->
        <section class="reservas" id="reservas">
            <h2>¿Qué puedes reservar?</h2>
            <div class="carrusel-wrapper">
                <!-- Botón Izquierdo con accesibilidad (Móvil) -->
                <button class="btn-carrusel btn-prev" aria-label="Tarjeta anterior">
                    <i class="fa-solid fa-chevron-left" aria-hidden="true"></i>
                </button>

                <div class="contenedor-tarjetas">
                    <div class="tarjeta">
                        <div class="icono" aria-hidden="true">🧪</div>
                        <h3>Laboratorios</h3>
                        <p>Reserva laboratorios totalmente equipados para prácticas y proyectos.</p>
                        <button type="button" onclick="window.location.href='laboratorio_informatica.php'">Ver laboratorio</button>
                    </div>
                    <div class="tarjeta">
                        <div class="icono" aria-hidden="true">🏫</div>
                        <h3>Salones</h3>
                        <p>Consulta la disponibilidad de aulas y realiza tu reserva fácilmente.</p>
                        <button type="button" onclick="window.location.href='sala_conferencias.php'">Ver salón</button>
                    </div>
                    <div class="tarjeta">
                        <div class="icono" aria-hidden="true">📚</div>
                        <h3>Biblioteca</h3>
                        <p>Reserva espacios de estudio individuales o grupales.</p>
                        <button type="button" onclick="window.location.href='pagina_principal.php'">Ver detalle</button>
                    </div>
                    <div class="tarjeta">
                        <div class="icono" aria-hidden="true">💻</div>
                        <h3>Equipamiento</h3>
                        <p>Solicita proyectores, notebooks y otros recursos tecnológicos.</p>
                        <button type="button" onclick="window.location.href='laboratorio_robotica.php'">Ver equipo</button>
                    </div>
                </div>

                <!-- Botón Derecho con accesibilidad (Móvil) -->
                <button class="btn-carrusel btn-next" aria-label="Tarjeta siguiente">
                    <i class="fa-solid fa-chevron-right" aria-hidden="true"></i>
                </button>
            </div>
        </section>

        <!-- ======================================================
           SECCIÓN NOVEDADES MODERNAS
           ====================================================== -->
        <section class="novedades-modernas">
            <div class="titulo-novedades">
                <h2>Últimas novedades</h2>
                <p>Mantente informado sobre cambios, eventos y anuncios del instituto.</p>
            </div>
            <div class="contenedor-novedades">
                <div class="card-novedad">
                    <img src="../imagenes/quimica_laboratorio.jpg" alt="Evento académico de química" loading="lazy">
                    <div class="contenido-novedad">
                       <h3>Semana de química aplicada</h3>
                       <p>El instituto abre una semana de actividades prácticas y charlas sobre innovación química.</p>
                       <a href="novedad.php?id=evento-quimica">Leer más →</a>
                    </div>
                </div>
                <div class="card-novedad">
                    <img src="../imagenes/imageninstituto.jpg" alt="Nuevas PC de escritorio para laboratorio de informática" loading="lazy">
                    <div class="contenido-novedad">
                       <h3>Llegan nuevas PCs al salón de informática</h3>
                       <p>La próxima semana se incorporarán equipos de última generación para prácticas y talleres.</p>
                       <a href="novedad.php?id=pc-escritorio">Leer más →</a>
                    </div>
                </div>
                <div class="card-novedad">
                    <img src="../imagenes/imagen_conferencia.jpg" alt="Graduación del grupo de química industrial" loading="lazy">
                    <div class="contenido-novedad">
                       <h3>Graduación del grupo de química industrial</h3>
                       <p>Se celebrará una ceremonia de cierre para estudiantes egresados del último ciclo de la carrera.</p>
                       <a href="novedad.php?id=graduacion-quimica">Leer más →</a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- ==========================================================
       PIE DE PÁGINA (FOOTER GLOBAL)
       ========================================================== -->
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
        <p class="copy">
            © 2026 Instituto Tecnológico de Tacuarembó - Todos los derechos reservados.
        </p>
    </footer>

    <!-- Scripts -->
    <script src="../scripts/scripts_pagina_principal.js"></script>
</html>