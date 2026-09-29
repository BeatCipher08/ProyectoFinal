<?php
// Incluir conexión y funciones (config.php ya tiene session_start())
require_once '../includes/config.php';
require_once '../includes/funciones.php';

$error = '';

// Verificar si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Capturar los inputs por su atributo "name"
    $email = trim($_POST['usuario']); 
    $password = trim($_POST['contraseña']);

    if (empty($email) || empty($password)) {
        $error = "Por favor, completa todos los campos.";
    } else {
        // Primero buscamos al usuario solo por email, sin importar si está activo o no
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario && $usuario['password'] === md5($password)) {
            // Si la contraseña es correcta, verificamos si está aprobado
            if ($usuario['activo'] == 1) {
                // Cuenta aprobada: iniciamos sesión
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['nombre'] = $usuario['nombre'];
                $_SESSION['rol'] = $usuario['rol'];
                redirigir('pagina_principal.php'); 
            } else {
                // Cuenta pendiente de aprobación
                $error = "Tu cuenta está pendiente de aprobación por un administrador.";
            }
        } else {
            $error = "Correo o contraseña incorrectos.";
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
    <title>Iniciar Sesion</title>
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
        <h2 class="titulo">Inicio de sesión</h2>
        <p>Coloque su usuario y contraseña.</p>
        <!-- Imprime el error solo si la variable no está vacía -->
<?php if (!empty($error)): ?>
    <p style="color: #ff4d4d; font-weight: bold; margin-bottom: 10px;">
        <?php echo $error; ?>
    </p>
<?php endif; ?>

        <section class="form_login">

            <form class="inicio_sesion_imputs" action="Inicio_sesion.php" method="post">
            
                <div class="correo">
                    <input type="email" id="correo" name="usuario" required placeholder="Correo electrónico">
                </div>

                <div class="contraseña">
                    <input type="password" id="contraseña" name="contraseña" required placeholder="Contraseña">
                </div>

                <button class="enviar" type="submit">Iniciar Sesion</button>
            
            </form>

            <div class="links-de-pie">
                <a href="#">Crear una cuenta nueva </a>
            </div>
    </section>
    </body>
</html>