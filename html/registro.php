<?php
// Mostrar errores en pantalla (solo para desarrollo)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Requerimos los archivos de configuración
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/funciones.php';

$error = '';
$exito = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['contraseña'] ?? '');

    if (empty($nombre) || empty($email) || empty($password)) {
        $error = "Todos los campos son obligatorios.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Ingresa un correo electrónico válido.";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            $error = "El correo electrónico ya está registrado.";
        } else {
            $password_md5 = md5($password);

            // IMPORTANTE: Forzamos activo = 0 para que la cuenta nazca "Pendiente"
            // Dejamos el rol como 'alumno' temporalmente, el admin lo cambiará al aprobar
            $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, password, activo) VALUES (?, ?, ?, 0)");
            
            if ($stmt->execute([$nombre, $email, $password_md5])) {
                $exito = "¡Solicitud enviada exitosamente! <br> Un administrador verificará tus datos y aprobará tu cuenta pronto. Redirigiendo...";
                // Redirige al inicio de sesión en vez de la página principal (en .php)
                header("refresh:4;url=Inicio_sesion.php"); 
            } else {
                $error = "Ocurrió un error al guardar los datos. Intenta nuevamente.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrarse - ReservaTech</title>
    
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="../css/style_inicio_de_sesion.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=M+PLUS+U:wght@100..900&display=swap" rel="stylesheet">
</head>
<body>

    <!-- Insertamos la barra superior dinámica -->
    <?php require_once __DIR__ . '/../includes/barra_superior.php'; ?>

    <section class="iniciodesesion">
        <h2 class="titulo">Crear una cuenta</h2>
        <p>Complete sus datos para solicitar registro en el sistema.</p>
        
        <?php if (!empty($error)): ?>
            <p style="color: #ff4d4d; font-weight: bold; margin-bottom: 10px; text-align: center;">
                <?php echo $error; ?>
            </p>
        <?php endif; ?>

        <?php if (!empty($exito)): ?>
            <p style="color: #5f9f5f; font-weight: bold; margin-bottom: 10px; text-align: center;">
                <?php echo $exito; ?>
            </p>
        <?php endif; ?>

        <section class="form_login">
            <form class="inicio_sesion_imputs" action="registro.php" method="post">
                <div class="nombre"> 
                    <input type="text" id="nombre" name="nombre" required placeholder="Nombre completo">
                </div>
                <div class="usuario">
                    <input type="email" id="email" name="email" required placeholder="Correo electrónico">
                </div>
                <div class="contraseña">
                    <input type="password" id="contraseña" name="contraseña" required placeholder="Contraseña">
                </div>
                <button type="submit">Solicitar registro</button>
            </form>

            <div class="links-de-pie">
                <a href="Inicio_sesion.php">¿Ya tienes una cuenta? Inicia sesión</a>
            </div>
        </section>
    </section>
</body>
</html>