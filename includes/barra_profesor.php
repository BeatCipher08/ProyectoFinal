<?php
$pagina_actual = basename($_SERVER['PHP_SELF']);
?>
<header class="barra barra-profesor">
    <div class="logo logo-profesor">
        ReservaTech <span>| Profesor</span>
    </div>
    <nav class="nav-profesor">
        <a href="panel_profesor.php" class="<?php echo ($pagina_actual == 'panel_profesor.php') ? 'activo' : ''; ?>">Inicio</a>
        <a href="reservas_profesor.php" class="<?php echo ($pagina_actual == 'reservas_profesor.php') ? 'activo' : ''; ?>">Mis reservas</a>
        <a href="calendario_profesor.php" class="<?php echo ($pagina_actual == 'calendario_profesor.php') ? 'activo' : ''; ?>">Calendario</a>
        
        <!-- 2. BOTÓN DE CERRAR SESIÓN (Sin el ../ y respetando mayúsculas) -->
        <a href="Inicio_sesion.php" class="peligro">Cerrar sesión</a>
    </nav>
</header>