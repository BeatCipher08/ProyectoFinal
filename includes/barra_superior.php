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

    <div class="dropdown-salones" style="position: relative; display: inline-block;">
        <a href="pagina_principal.php#salones" class="dropdown-trigger" style="display: inline-block; padding: 12px 18px; color: #ffffff; text-decoration: none; font-weight: 600;">Salones</a>
        <div class="dropdown-menu" style="display: none; position: absolute; top: 100%; left: 0; min-width: 240px; background: #ffffff; border: 1px solid #dfe9e1; border-radius: 12px; box-shadow: 0 12px 28px rgba(0,0,0,0.12); z-index: 50; padding: 10px 0;">
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
    <a href="catalogo_recursos.php">Recursos</a>
    <a href="#">Novedades</a>
    <a href="#">Sobre nosotros</a>

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
</header>

<style>
    .dropdown-salones:hover .dropdown-menu,
    .dropdown-salones:focus-within .dropdown-menu {
        display: block !important;
    }
    .dropdown-salones .dropdown-menu a:hover {
        background: #edf7ee;
    }
</style>