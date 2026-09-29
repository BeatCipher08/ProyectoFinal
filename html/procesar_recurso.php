<?php
session_start();
require_once __DIR__ . '/../includes/config.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'admin') {
    die("Acceso denegado");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';

    if ($accion === 'guardar') {
        $nombre = trim($_POST['nombre']);
        $tipo = $_POST['tipo'];
        $descripcion = trim($_POST['descripcion']);
        $cantidad = (int)$_POST['cantidad_total'];
        $activo = (int)$_POST['activo'];
        
        $ruta_imagen = null;

        // Si es equipo y se subió una imagen, la guardamos
        if ($tipo === 'equipo' && isset($_FILES['imagen_equipo'])) {
            
            // 1. Verificamos si PHP bloqueó la subida desde el inicio
            if ($_FILES['imagen_equipo']['error'] !== 0) {
                die("🚨 ERROR DE PHP: El archivo no subió. Código de error: " . $_FILES['imagen_equipo']['error']);
            }

            $dir = '../uploads/equipos/';
            
            // 2. Verificamos si la carpeta existe
            if (!file_exists($dir)) {
                die("🚨 ERROR DE RUTA: La carpeta " . $dir . " no existe y el sistema no deja crearla automáticamente.");
            }
            
            $nombre_archivo = time() . '_' . basename($_FILES['imagen_equipo']['name']);
            $ruta_destino = $dir . $nombre_archivo;
            
            // 3. Intentamos mover el archivo. Si falla, es 100% permisos.
            if (move_uploaded_file($_FILES['imagen_equipo']['tmp_name'], $ruta_destino)) {
                $ruta_imagen = 'uploads/equipos/' . $nombre_archivo;
            } else {
                die("🚨 ERROR DE PERMISOS: El archivo llegó bien, pero XAMPP no tiene permiso para escribir (guardar) en la carpeta " . $dir . ". Debes darle permisos 777 a la carpeta uploads.");
            }
        }

        $stmt = $pdo->prepare("INSERT INTO recursos (nombre, tipo, descripcion, cantidad_total, activo, imagen) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$nombre, $tipo, $descripcion, $cantidad, $activo, $ruta_imagen]);

    } elseif ($accion === 'eliminar') {
        $id = (int)$_POST['id'];
        // Opcional: Podrías buscar la imagen con un SELECT y hacer unlink() para borrar el archivo físico
        $stmt = $pdo->prepare("DELETE FROM recursos WHERE id = ?");
        $stmt->execute([$id]);
    }

    header("Location: recursos.php");
    exit();
}