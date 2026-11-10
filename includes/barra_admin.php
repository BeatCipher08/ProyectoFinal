<?php
// Obtenemos el nombre del archivo en el que estamos parados (ej: 'usuarios.php')
$pagina_actual = basename($_SERVER['PHP_SELF']);
$usuario_nombre = $_SESSION['nombre'] ?? 'Usuario';
$usuario_rol = strtolower((string)($_SESSION['rol'] ?? 'usuario'));
?>
<header class="barra">
    <div class="brand-group">
        <a class="brand-home" href="pagina_principal.php" aria-label="Ir a la página principal">
            <img class="brand-logo" src="../imagenes/logo_proyecto.png" alt="Logo del instituto">
            <span class="logo">S.G.E.R</span>
        </a>
        <div class="usuario-header">| <?php echo htmlspecialchars($usuario_nombre); ?> - <?php echo htmlspecialchars($usuario_rol); ?></div>
    </div>
    <nav>
        <a href="panel_admin.php" class="<?php echo ($pagina_actual == 'panel_admin.php') ? 'activo' : ''; ?>">Panel</a>
        <a href="reservas_admin.php" class="<?php echo ($pagina_actual == 'reservas_admin.php') ? 'activo' : ''; ?>">Reservas</a>
        <a href="usuarios.php" class="<?php echo ($pagina_actual == 'usuarios.php') ? 'activo' : ''; ?>">Usuarios</a>
        <a href="recursos.php" class="<?php echo ($pagina_actual == 'recursos.php') ? 'activo' : ''; ?>">Recursos</a>
        <a href="Inicio_sesion.php">Cerrar sesión</a>
    </nav>
</header>