<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sobre Nosotros | Instituto Tecnológico</title>
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="../css/style_sobre_nosotros.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <?php require_once __DIR__ . '/../includes/barra_superior.php'; ?>

    <main class="main-sobre">
        <section class="hero-sobre">
            <div class="sobre-texto">
                <span class="sobre-etiqueta">Institucional</span>
                <h1>Sobre nosotros</h1>
                <p>Somos una institución orientada a la formación técnica y al desarrollo de competencias para los desafíos actuales de la industria, la tecnología y la innovación.</p>
                <p>Trabajamos para ofrecer espacios de aprendizaje modernos, recursos de calidad y un entorno educativo que conecte la teoría con la práctica profesional.</p>
            </div>
            <div class="sobre-foto">
                <img src="../imagenes/imageninstituto.jpg" alt="Vista del instituto tecnológico">
            </div>
        </section>

        <section class="bloque-sobre" aria-label="Pilares institucionales">
            <article class="tarjeta-sobre">
                <h2>Formación de calidad</h2>
                <p>Diseñamos propuestas académicas con enfoque práctico, para que estudiantes y docentes trabajen con metodologías alineadas al mundo real.</p>
            </article>
            <article class="tarjeta-sobre">
                <h2>Infraestructura y recursos</h2>
                <p>Contamos con laboratorios, salones y equipamiento tecnológico que fortalecen el aprendizaje y la experiencia institucional diaria.</p>
            </article>
            <article class="tarjeta-sobre">
                <h2>Compromiso con la comunidad</h2>
                <p>Promovemos un vínculo activo con nuestro entorno, impulsando proyectos, actividades y eventos para la comunidad educativa.</p>
            </article>
        </section>

        <section class="mision-vision" aria-label="Misión y visión institucional">
            <article class="mision-box">
                <h3>Misión</h3>
                <p>Formar profesionales competentes, éticos y comprometidos, capaces de transformar su entorno mediante el conocimiento técnico, la creatividad y la innovación aplicada.</p>
            </article>
            <article class="mision-box">
                <h3>Visión</h3>
                <p>Ser una institución referente en educación tecnológica, reconocida por su excelencia académica, su modernización constante y su aporte al desarrollo regional.</p>
            </article>
        </section>

        <section class="cierre-sobre" aria-label="Mensaje institucional final">
            <h3>Una comunidad que evoluciona</h3>
            <p>Creemos en una educación conectada con el futuro. Por eso, impulsamos mejoras continuas en nuestros procesos, espacios y herramientas para acompañar a cada estudiante en su crecimiento académico y profesional.</p>
        </section>
    </main>

    <?php require_once __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
