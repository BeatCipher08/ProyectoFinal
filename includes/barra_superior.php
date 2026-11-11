<?php
// Verificamos si la sesión ya está iniciada para evitar errores
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<header id="menusuperior">
    <a id="menuhamburguesa" href="#" aria-label="Menú de navegación">≡</a>
    <a class="logo-home" href="../html/pagina_principal.php" aria-label="Ir a la página principal">
    <img src="../imagenes/logo_proyecto.png" alt="Logo del instituto" >
    </a>
    <a id="nombreinsti" href="pagina_principal.php">
        <span class="nombre_insti">Instituto tecnológico de Tacuarembó</span>
        <span class="siglas">ITT</span>
    </a>

    <div class="nav-mobile-wrapper">
        <div class="nav-principal">
            <div class="dropdown-salones" style="position: relative; display: inline-block;">
                <a href="pagina_principal.php#salones" class="dropdown-trigger" style="display: inline-block; padding: 12px 18px; color: #ffffff; text-decoration: none; font-weight: 600; cursor: pointer;">Salones</a>
                <div class="dropdown-menu">
                    <a href="sala_conferencias.php" style="display: block; padding: 10px 16px; color: #173720; text-decoration: none;">Sala de conferencias</a>
                    <a href="salon_grande_uno.php" style="display: block; padding: 10px 16px; color: #173720; text-decoration: none;">Salón grande</a>
                    <a href="salon_uno.php" style="display: block; padding: 10px 16px; color: #173720; text-decoration: none;">Salón normal</a>
                    <a href="laboratorio_informatica.php" style="display: block; padding: 10px 16px; color: #173720; text-decoration: none;">Laboratorio de informática</a>
                    <a href="laboratorio_quimica.php" style="display: block; padding: 10px 16px; color: #173720; text-decoration: none;">Laboratorio de química</a>
                    <a href="laboratorio_fisica.php" style="display: block; padding: 10px 16px; color: #173720; text-decoration: none;">Laboratorio de física</a>
                    <a href="laboratorio_biologia.php" style="display: block; padding: 10px 16px; color: #173720; text-decoration: none;">Laboratorio de biología</a>
                    <a href="laboratorio_ciencias.php" style="display: block; padding: 10px 16px; color: #173720; text-decoration: none;">Laboratorio de ciencias</a>
                    <a href="laboratorio_robotica.php" style="display: block; padding: 10px 16px; color: #173720; text-decoration: none;">Laboratorio de robótica</a>
                    <a href="taller_mecanica.php" style="display: block; padding: 10px 16px; color: #173720; text-decoration: none;">Taller de mecánica</a>
                </div>
            </div>
            <a href="catalogo_recursos.php">Equipos</a>
            <a href="novedades.php">Novedades</a>
            <a href="sobre_nosotros.php">Sobre nosotros</a>
        </div>

        <?php if (isset($_SESSION['usuario_id'])): ?>
            <!-- VISTA: USUARIO CON SESIÓN INICIADA -->
            <div class="acciones-usuario">
                <span class="nombre-usuario">
                    <?php echo htmlspecialchars($_SESSION['nombre']); ?>
                </span>

                <?php if ($_SESSION['rol'] === 'admin'): ?>
                    <!-- VISTA: SOLO PARA ADMINISTRADORES -->
                    <button class="registro panel-admin" onclick="window.location.href='panel_admin.php'">
                        Panel Admin
                    </button>
                <?php elseif ($_SESSION['rol'] === 'profesor'): ?>
                    <!-- VISTA: SOLO PARA PROFESORES -->
                    <button class="registro panel-profesor" onclick="window.location.href='panel_profesor.php'">
                        Panel Profesor
                    </button>
                <?php elseif ($_SESSION['rol'] === 'alumno'): ?>
                    <!-- VISTA: SOLO PARA ALUMNOS -->
                    <button class="registro panel-alumno" onclick="window.location.href='panel_alumno.php'">
                        Panel Alumno
                    </button>
                <?php endif; ?>

                <!-- BOTÓN CERRAR SESIÓN -->
                <button class="sesion panel-cerrar" onclick="window.location.href='cerrar_sesion.php'">
                    Cerrar sesión
                </button>
            </div>

        <?php else: ?>
            <!-- VISTA: VISITANTE SIN SESIÓN -->
            <div class="acciones-usuario">
                <button class="registro" onclick="window.location.href='registro.php'">Registrarse</button>
                <button class="sesion" onclick="window.location.href='Inicio_sesion.php'">Iniciar sesion</button>
            </div>
        <?php endif; ?>
    </div>
</header>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const menuBtn = document.getElementById('menuhamburguesa');
        const navWrapper = document.querySelector('.nav-mobile-wrapper');
        const navPrincipal = document.querySelector('.nav-principal');
        const accionesUsuario = document.querySelector('.acciones-usuario');
        const salonMenu = document.querySelector('.dropdown-salones');
        const salonTrigger = document.querySelector('.dropdown-trigger');

        if (menuBtn && navWrapper) {
            menuBtn.addEventListener('click', function (event) {
                event.preventDefault();
                const isOpen = navWrapper.classList.toggle('is-open');
                menuBtn.setAttribute('aria-expanded', String(isOpen));
            });
        }

        if (salonMenu && salonTrigger) {
            salonTrigger.addEventListener('click', function (event) {
                if (window.innerWidth <= 768) {
                    event.preventDefault();
                }
                const isOpen = salonMenu.classList.toggle('open');
                salonTrigger.setAttribute('aria-expanded', String(isOpen));
            });

            document.addEventListener('click', function (event) {
                if (!salonMenu.contains(event.target)) {
                    salonMenu.classList.remove('open');
                    salonTrigger.setAttribute('aria-expanded', 'false');
                }
            });
        }
    });
</script>

<style>
    .nav-mobile-wrapper {
        display: flex;
        align-items: center;
        gap: 12px;
        width: 100%;
    }

    .nav-principal {
        display: flex;
        align-items: center;
        gap: 4px;
        flex: 1;
    }

    .acciones-usuario {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-left: auto;
        margin-right: 12px;
    }

    .dropdown-salones {
        position: relative;
        display: inline-block;
    }

    .dropdown-salones .dropdown-menu {
        display: block;
        position: absolute;
        top: calc(100% + 8px);
        left: 0;
        min-width: 240px;
        background: #ffffff;
        border: 1px solid #dfe9e1;
        border-radius: 12px;
        box-shadow: 0 12px 28px rgba(0,0,0,0.12);
        z-index: 50;
        padding: 10px 0;
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
        transform: translateY(-8px);
        transition: opacity 0.2s ease, transform 0.2s ease, visibility 0.2s ease;
    }

    .dropdown-salones .dropdown-menu a {
        display: block;
        padding: 10px 16px;
        color: #173720;
        text-decoration: none;
    }

    .dropdown-salones:hover .dropdown-menu,
    .dropdown-salones:focus-within .dropdown-menu,
    .dropdown-salones.open .dropdown-menu {
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
        transform: translateY(0);
    }

    .dropdown-salones .dropdown-menu a:hover {
        background: #edf7ee;
    }

    @media (max-width: 768px) {
        .nav-mobile-wrapper {
            display: none;
            width: 100%;
            margin-top: 10px;
            flex-direction: column;
            align-items: stretch;
            gap: 10px;
            background: rgba(16, 38, 18, 0.18);
            border-radius: 14px;
            padding: 8px;
        }

        .nav-mobile-wrapper.is-open {
            display: flex;
        }

        .nav-principal {
            display: flex;
            width: 100%;
            flex-direction: column;
            align-items: stretch;
            gap: 6px;
        }

        .acciones-usuario {
            display: flex;
            width: 100%;
            margin: 0;
            flex-direction: column;
            align-items: stretch;
            gap: 8px;
            background: rgba(16, 38, 18, 0.12);
            border-radius: 14px;
            padding: 8px;
        }

        .nombre-usuario {
            display: none;
        }

        .nav-principal a,
        .dropdown-salones,
        .dropdown-salones > a,
        .acciones-usuario button {
            width: 100%;
            margin: 0;
            text-align: left;
            display: block;
        }

        .acciones-usuario button {
            display: block;
            text-align: center;
        }

        .dropdown-salones .dropdown-menu {
            position: static;
            min-width: 100%;
            margin-top: 8px;
            border-radius: 10px;
            box-shadow: none;
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
            transform: none;
            display: none;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.25s ease, opacity 0.2s ease;
        }

        .dropdown-salones.open .dropdown-menu {
            display: block;
            max-height: 500px;
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
        }
    }
</style>