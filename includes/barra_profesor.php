<?php
$pagina_actual = basename($_SERVER['PHP_SELF']);
$usuario_nombre = $_SESSION['nombre'] ?? 'Usuario';
$usuario_rol = strtolower((string)($_SESSION['rol'] ?? 'usuario'));
?>
<header class="barra barra-profesor">
    <div class="brand-group logo-profesor-brand">
        <a class="brand-home" href="pagina_principal.php" aria-label="Ir a la página principal">
            <img class="brand-logo" src="../imagenes/logo_proyecto.png" alt="Logo del instituto">
            <span class="logo logo-profesor">S.G.E.R</span>
        </a>
        <div class="usuario-header">| <?php echo htmlspecialchars($usuario_nombre); ?> - <?php echo htmlspecialchars($usuario_rol); ?></div>
    </div>
    <nav class="nav-profesor">
        <a href="panel_profesor.php" class="<?php echo ($pagina_actual == 'panel_profesor.php') ? 'activo' : ''; ?>">Inicio</a>
        <a href="calendario_profesor.php" class="<?php echo ($pagina_actual == 'calendario_profesor.php') ? 'activo' : ''; ?>">Reservas y calendario</a>

        <a href="Inicio_sesion.php" class="peligro">Cerrar sesión</a>
    </nav>
</header>