<?php
// Mostrar errores en pantalla (solo para desarrollo)
ini_set('display_errors', 1);
error_reporting(E_ALL);
// Requerimos los archivos de configuración
require_once '../includes/config.php';
require_once '../includes/funciones.php';

$error = '';
$exito = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Capturar datos del formulario según los atributos "name"
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['contraseña'] ?? '');

    // Validaciones básicas
    if (empty($nombre) || empty($email) || empty($password)) {
        $error = "Todos los campos son obligatorios.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Ingresa un correo electrónico válido.";
    } else {
        // Verificar si el correo ya existe en la base de datos
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            $error = "El correo electrónico ya está registrado.";
        } else {
            // Cifrar la contraseña
            $password_md5 = md5($password);

            // Insertar el nuevo usuario usando los valores por defecto para 'rol' y 'activo'
            $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, password) VALUES (?, ?, ?)");
            
            if ($stmt->execute([$nombre, $email, $password_md5])) {
                $exito = "¡Cuenta creada exitosamente! </br> Redirigiendo...";
                
                // Le indica al navegador que espere 3 segundos y luego cambie de página
                header("refresh:3;url=pagina_principal.html"); 
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
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="../css/style_inicio_de_sesion.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=M+PLUS+U:wght@100..900&display=swap" rel="stylesheet">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrarse</title>
</head>
<body>
    <header id="menusuperior">
        <a id="menuhamburguesa" href="#" aria-label="Menú de navegación">≡</a>
        <img src="../imagenes/logo_proyecto.png" alt="Logo del instituto">
        <a id="nombreinsti" href="#">Instituto tecnológico de Tacuarembó</a>
        <a href="#">Salones</a>
        <a href="#">Novedades</a>
        <a href="#">Sobre nosotros</a>
        <button class="registro" onclick="window.location.href='registro.php'">Registrarse</button>
        <button class="sesion" onclick="window.location.href='Inicio_sesion.php'">Iniciar sesion</button>
        </button>
    </header>

    <section class="iniciodesesion">
        <h2 class="titulo">Crear una cuenta</h2>
        <p>Complete sus datos para registrarse en el sistema.</p>
        <!-- Mensajes de Error y Éxito -->
        <?php if (!empty($error)): ?>
            <p style="color: #ff4d4d; font-weight: bold; margin-bottom: 10px; text-align: center;">
                <?php echo $error; ?>
            </p>
        <?php endif; ?>

        <?php if (!empty($exito)): ?>
            <p style="color: #ffffff; font-weight: bold; margin-bottom: 10px; text-align: center;">
                <?php echo $exito; ?>
            </p>
        <?php endif; ?>
        <section class="form_login">

            <form class="inicio_sesion_imputs" action="registro.php" method="post">
            
                <div class="nombre"> 
                    <input type="text" id="nombre" name="nombre" required placeholder="Nombre completo">
                </div>

                <div class="usuario">
                    <input type="email" id="email" name="email" required placeholder="Correo">
                </div>

                <div class="contraseña">
                    <input type="password" id="contraseña" name="contraseña" required placeholder="Contraseña">
                </div>

                <button type="submit">Registrarse</button>
            
            </form>

            <div class="links-de-pie">
                <a href="login.php">¿Ya tienes una cuenta? Inicia sesión</a>
            </div>
        </section>
    </section>
</body>
</html>