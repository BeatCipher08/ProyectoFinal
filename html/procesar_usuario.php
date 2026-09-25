<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'admin') {
    header("Location: Inicio_sesion.php");
    exit();
}

require_once __DIR__ . '/../includes/config.php';

// 1. ELIMINAR USUARIO (Viene por URL con el método GET)
if (isset($_GET['accion']) && $_GET['accion'] == 'eliminar' && isset($_GET['id_usuario'])) {
    $id = mysqli_real_escape_string($conexion, $_GET['id_usuario']);
    
    // Opcional: Evitar que el admin se elimine a sí mismo
    if ($id != $_SESSION['usuario_id']) {
        $sql = "DELETE FROM usuarios WHERE id = '$id'";
        mysqli_query($conexion, $sql);
    }
    header("Location: usuarios.php");
    exit();
}

// 2. CREAR Y EDITAR USUARIO (Vienen del formulario con POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $accion = $_POST['accion'];
    $id_usuario = mysqli_real_escape_string($conexion, $_POST['id_usuario']);
    $nombre = mysqli_real_escape_string($conexion, $_POST['nombre']);
    $email = mysqli_real_escape_string($conexion, $_POST['email']);
    $rol = mysqli_real_escape_string($conexion, $_POST['rol']);
    $estado = ($_POST['estado'] == 'Activo') ? 1 : 0;

    if ($accion == 'guardar' && empty($id_usuario)) {
        // ES UN USUARIO NUEVO
        $password = md5($_POST['password']); 
        $sql = "INSERT INTO usuarios (nombre, email, password, rol, activo) 
                VALUES ('$nombre', '$email', '$password', '$rol', '$estado')";
        mysqli_query($conexion, $sql);
        
    } else if ($accion == 'editar' && !empty($id_usuario)) {
        // ES UNA EDICIÓN
        if (!empty($_POST['password'])) {
            // Si escribió una nueva contraseña, la actualizamos
            $password = md5($_POST['password']);
            $sql = "UPDATE usuarios SET nombre='$nombre', email='$email', password='$password', rol='$rol', activo='$estado' WHERE id='$id_usuario'";
        } else {
            // Si dejó la contraseña en blanco, actualizamos todo MENOS la contraseña
            $sql = "UPDATE usuarios SET nombre='$nombre', email='$email', rol='$rol', activo='$estado' WHERE id='$id_usuario'";
        }
        mysqli_query($conexion, $sql);
    }

    header("Location: usuarios.php");
    exit();
}
?>