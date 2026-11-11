<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['rol'] ?? '', ['alumno', 'admin', 'profesor'], true)) {
    header("Location: Inicio_sesion.php");
    exit();
}

$pagina_actual = basename($_SERVER['PHP_SELF']);
$usuario_nombre = $_SESSION['nombre'] ?? 'Usuario';
$usuario_rol = strtolower((string)($_SESSION['rol'] ?? 'usuario'));
?>
<header class="barra">
    <button class="menu-toggle" type="button" aria-label="Abrir menú" aria-expanded="false">☰</button>
    <div class="brand-group">
        <a class="brand-home" href="pagina_principal.php" aria-label="Ir a la página principal">
            <img class="brand-logo" src="../imagenes/logo_proyecto.png" alt="Logo del instituto">
            <span class="logo">S.G.E.R</span>
        </a>
        <div class="usuario-header">| <?php echo htmlspecialchars($usuario_nombre); ?> - <?php echo htmlspecialchars($usuario_rol); ?></div>
    </div>
    <nav class="nav-links">
        <a href="panel_alumno.php" class="<?php echo ($pagina_actual == 'panel_alumno.php') ? 'activo' : ''; ?>">Inicio</a>
        <a href="calendario_alumno.php" class="<?php echo ($pagina_actual == 'calendario_alumno.php') ? 'activo' : ''; ?>">Calendario</a>
        <a href="catalogo_recursos.php" class="<?php echo ($pagina_actual == 'catalogo_recursos.php') ? 'activo' : ''; ?>">Equipos</a>
        <a href="cerrar_sesion.php" class="cerrar-sesion">Cerrar sesión</a>
    </nav>
</header>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const header = document.currentScript.previousElementSibling;
        if (!header) return;
        const toggle = header.querySelector('.menu-toggle');
        const nav = header.querySelector('.nav-links');
        if (!toggle || !nav) return;

        toggle.addEventListener('click', function () {
            const isOpen = nav.classList.toggle('open');
            toggle.setAttribute('aria-expanded', String(isOpen));
        });
    });
</script>
