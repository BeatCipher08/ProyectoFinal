<?php
// Obtenemos el nombre del archivo en el que estamos parados (ej: 'usuarios.php')
$pagina_actual = basename($_SERVER['PHP_SELF']);
?>
<header class="barra">
    <div class="logo">
        ReservaTech <span>| Admin</span>
    </div>
    <nav>
        <a href="panel_admin.php" class="<?php echo ($pagina_actual == 'panel_admin.php') ? 'activo' : ''; ?>">Panel</a>
        <a href="reservas_admin.php" class="<?php echo ($pagina_actual == 'reservas_admin.php') ? 'activo' : ''; ?>">Reservas</a>
        <a href="usuarios.php" class="<?php echo ($pagina_actual == 'usuarios.php') ? 'activo' : ''; ?>">Usuarios</a>
        <a href="recursos.php" class="<?php echo ($pagina_actual == 'recursos.php') ? 'activo' : ''; ?>">Recursos</a>
        <a href="Inicio_sesion.php">Cerrar sesión</a>
    </nav>
</header>