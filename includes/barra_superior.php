<?php
// Verificamos si la sesión ya está iniciada para evitar errores
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<header id="menusuperior">
    <a id="menuhamburguesa" href="#" aria-label="Menú de navegación">≡</a>
    <img src="../imagenes/logo_proyecto.png" alt="Logo del instituto">
    <a id="nombreinsti" href="pagina_principal.php">
        <span class="nombre_insti">Instituto tecnológico de Tacuarembó</span>
        <span class="siglas">ITT</span>
    </a>
    
    <a href="#">Salones</a>
    <a href="#">Novedades</a>
    <a href="#">Sobre nosotros</a>

    <?php if (isset($_SESSION['usuario_id'])): ?>
        <!-- VISTA: USUARIO CON SESIÓN INICIADA -->
        <span class="nombre-usuario" style="margin-right: 15px; font-weight: bold; color: #5f9f5f;">
            Hola, <?php echo htmlspecialchars($_SESSION['nombre']); ?>
        </span>

        <?php if ($_SESSION['rol'] === 'admin'): ?>
            <!-- VISTA: SOLO PARA ADMINISTRADORES -->
            <button class="registro" onclick="window.location.href='panel_admin.php'" style="background: #397039;">
                Panel Admin
            </button>
        <?php endif; ?>

        <!-- BOTÓN CERRAR SESIÓN -->
        <button class="sesion" onclick="window.location.href='cerrar_sesion.php'" style="background: #d62828;">
            Cerrar sesión
        </button>

    <?php else: ?>
        <!-- VISTA: VISITANTE SIN SESIÓN -->
        <button class="registro" onclick="window.location.href='registro.php'">Registrarse</button>
        <button class="sesion" onclick="window.location.href='Inicio_sesion.php'">Iniciar sesion</button>
    <?php endif; ?>
</header>