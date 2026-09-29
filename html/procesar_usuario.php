<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'admin') {
    header("Location: Inicio_sesion.php");
    exit();
}

require_once __DIR__ . '/../includes/config.php';

// 1. ELIMINAR USUARIO
if (isset($_GET['accion']) && $_GET['accion'] == 'eliminar' && isset($_GET['id_usuario'])) {
    $id = $_GET['id_usuario'];
    
    if ($id != $_SESSION['usuario_id']) {
        $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
        $stmt->execute([$id]);
    }
    header("Location: usuarios.php");
    exit();
}

// 2. CREAR Y EDITAR USUARIO
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $accion = $_POST['accion'];
    $id_usuario = $_POST['id_usuario'];
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $rol = $_POST['rol'];
    $estado = ($_POST['estado'] == 'Activo') ? 1 : 0;

    if ($accion == 'guardar' && empty($id_usuario)) {
        // ES UN USUARIO NUEVO
        $password = md5($_POST['password']); 
        $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, password, rol, activo) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$nombre, $email, $password, $rol, $estado]);
        
    } else if ($accion == 'editar' && !empty($id_usuario)) {
        // ES UNA EDICIÓN
        if (!empty($_POST['password'])) {
            $password = md5($_POST['password']);
            $stmt = $pdo->prepare("UPDATE usuarios SET nombre=?, email=?, password=?, rol=?, activo=? WHERE id=?");
            $stmt->execute([$nombre, $email, $password, $rol, $estado, $id_usuario]);
        } else {
            $stmt = $pdo->prepare("UPDATE usuarios SET nombre=?, email=?, rol=?, activo=? WHERE id=?");
            $stmt->execute([$nombre, $email, $rol, $estado, $id_usuario]);
        }
    }

    header("Location: usuarios.php");
    exit();
}
?>