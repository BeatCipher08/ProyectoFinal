<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laboratorio de Robótica</title>
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
                <h1>Laboratorio de Robótica</h1>
                <p>Gran espacio tecnológico para armado, programación y prueba de prototipos robóticos, impresoras 3D y sistemas automatizados.</p>
            </div>
            <div class="foto-salon">
                <img src="../imagenes/imageninstituto.jpg" alt="Laboratorio de robótica del instituto">
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
                <h2>40</h2>
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
                    <div class="icono-equipo-horizontal">🤖</div>
                    <div class="texto-equipo-horizontal">
                        <h3>Kits de robótica</h3>
                        <p>Estaciones de trabajo con componentes de robotica avanzada para prototipado.</p>
                    </div>
                </div>
                <div class="tarjeta-equipo-horizontal">
                    <div class="icono-equipo-horizontal">🛠️</div>
                    <div class="texto-equipo-horizontal">
                        <h3>Estaciones de soldadura</h3>
                        <p>Espacios dedicados para armado de estructuras, componentes y conexiones.</p>
                    </div>
                </div>
                <div class="tarjeta-equipo-horizontal">
                    <div class="icono-equipo-horizontal">🖨️</div>
                    <div class="texto-equipo-horizontal">
                        <h3>Impresoras 3D</h3>
                        <p>Equipo para prototipado rápido y creación de piezas complejas.</p>
                    </div>
                </div>
                <div class="tarjeta-equipo-horizontal">
                    <div class="icono-equipo-horizontal">🧰</div>
                    <div class="texto-equipo-horizontal">
                        <h3>Herramientas de precisión</h3>
                        <p>Instrumental para montaje, reparación y calibración.</p>
                    </div>
                </div>
                <div class="tarjeta-equipo-horizontal">
                    <div class="icono-equipo-horizontal">📍</div>
                    <div class="texto-equipo-horizontal">
                        <h3>Pista de pruebas</h3>
                        <p>Espacio para ensayos operativos de prototipos y sistemas automatizados.</p>
                    </div>
                </div>
                <div class="tarjeta-equipo-horizontal">
                    <div class="icono-equipo-horizontal">💻</div>
                    <div class="texto-equipo-horizontal">
                        <h3>Estaciones electrónicas</h3>
                        <p>Equipos para programación, prueba y análisis de sistemas inteligentes.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="seccion-distribucion">
            <div class="caja-distribucion">
                <h2><i class="fa-solid fa-location-dot"></i> Ubicación y uso</h2>
                <p>El laboratorio de robótica está ubicado en la Ala Este, Planta Alta, como espacio tecnológico avanzado para automatización y prototipado.</p>
                <ul class="lista-sectores">
                    <li><strong>Zona de armado:</strong> estaciones para montaje y configuración de prototipos.</li>
                    <li><strong>Zona de prueba:</strong> pista para ensayo de movimiento y respuesta del sistema.</li>
                    <li><strong>Uso principal:</strong> robótica, automatización y prototipado avanzado.</li>
                </ul>
            </div>
        </section>

        <section class="seccion-normas">
            <article class="articulo-normas">
                <header class="encabezado-normas">
                    <h2>Normas de uso</h2>
                    <p>Seguridad y cuidado en laboratorio tecnológico</p>
                </header>
                <div class="contenido-normas">
                    <p>• El uso del laboratorio requiere la supervisión de responsables del área.</p>
                    <p>• Las herramientas y equipos deben utilizarse con protocolos específicos de seguridad.</p>
                    <p>• Se debe mantener el espacio ordenado para garantizar un entorno de trabajo seguro.</p>
                </div>
            </article>
        </section>

        <section class="seccion-documentos">
            <div class="caja-documentos">
                <span class="etiqueta-documentos">Apoyo</span>
                <h2>Bibliografía recomendada</h2>
                <p>Libros de referencia para robótica, automatización y sistemas inteligentes.</p>
                <div class="lista-documentos">
                    <div class="tarjeta-documento tarjeta-libro">
                        <div class="portada-libro color-robotica">
                            <span>Robótica</span>
                            <strong>Móvil</strong>
                        </div>
                        <div class="texto-documento">
                            <strong>Robótica móvil</strong>
                            <small>Bibliografía base</small>
                        </div>
                    </div>
                    <div class="tarjeta-documento tarjeta-libro">
                        <div class="portada-libro color-robotica">
                            <span>Sistemas</span>
                            <strong>Embebidos</strong>
                        </div>
                        <div class="texto-documento">
                            <strong>Sistemas embebidos</strong>
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
