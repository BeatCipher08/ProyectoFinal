<?php
session_start();
session_destroy();
// Redirigimos a la página principal (que en la Fase 3 pasaremos a .php)
header("Location: pagina_principal.php");
exit();
?>