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
        <?php require_once __DIR__ . '/../includes/barra_superior.php'; ?>

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

                <div class="contraseña password-field">
                    <input type="password" id="contrasena" name="contraseña" required placeholder="Contraseña">
                    <button type="button" class="toggle-password" data-target="contrasena" aria-label="Mostrar contraseña" aria-pressed="false" title="Mostrar contraseña">👁</button>
                </div>

                <button class="enviar" type="submit">Iniciar Sesion</button>
            
            </form>

            <div class="links-de-pie">
                <a href="registro.php">Crear una cuenta nueva</a>
            </div>
    </section>
    <script>
        document.querySelectorAll('.toggle-password').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var input = document.getElementById(btn.dataset.target);
                if (!input) return;

                var showing = input.type === 'text';
                input.type = showing ? 'password' : 'text';
                btn.setAttribute('aria-pressed', String(!showing));
                btn.setAttribute('aria-label', showing ? 'Mostrar contraseña' : 'Ocultar contraseña');
                btn.setAttribute('title', showing ? 'Mostrar contraseña' : 'Ocultar contraseña');
                btn.classList.toggle('is-active', !showing);
            });
        });
    </script>
    </body>
</html>