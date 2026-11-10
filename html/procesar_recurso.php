<?php
session_start();
require_once __DIR__ . '/../includes/config.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'admin') {
    die("Acceso denegado");
}

function redirigirRecursos(string $estado, array $extra = []): void {
    $query = array_merge(['estado' => $estado], $extra);
    header("Location: recursos.php?" . http_build_query($query));
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';

    try {
        if ($accion === 'guardar') {
            $nombre = trim($_POST['nombre']);
            $tipo = $_POST['tipo'];
            $descripcion = trim($_POST['descripcion']);
            $cantidad = $tipo === 'equipo' ? max(1, (int)($_POST['cantidad_total'] ?? 1)) : 1;
            $activo = (int)$_POST['activo'];

            $ruta_imagen = null;

            // Si se subió una imagen, la guardamos para cualquier tipo de recurso
            if (isset($_FILES['imagen_equipo']) && $_FILES['imagen_equipo']['error'] !== UPLOAD_ERR_NO_FILE) {
                if ($_FILES['imagen_equipo']['error'] !== UPLOAD_ERR_OK) {
                    redirigirRecursos('error_subida');
                }

                $dir = '../uploads/recursos/';
                if (!file_exists($dir) && !mkdir($dir, 0775, true)) {
                    redirigirRecursos('error_ruta_uploads');
                }

                $nombre_archivo = time() . '_' . basename($_FILES['imagen_equipo']['name']);
                $ruta_destino = $dir . $nombre_archivo;

                if (move_uploaded_file($_FILES['imagen_equipo']['tmp_name'], $ruta_destino)) {
                    $ruta_imagen = 'uploads/recursos/' . $nombre_archivo;
                } else {
                    redirigirRecursos('error_permisos_uploads');
                }
            }

            $stmt = $pdo->prepare("INSERT INTO recursos (nombre, tipo, descripcion, cantidad_total, activo, imagen) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$nombre, $tipo, $descripcion, $cantidad, $activo, $ruta_imagen]);
            redirigirRecursos('guardado_ok');
        }

        if ($accion === 'eliminar') {
            $id = (int)($_POST['id'] ?? 0);
            if ($id <= 0) {
                redirigirRecursos('error_id_invalido');
            }

            $usoStmt = $pdo->prepare("SELECT COUNT(*) FROM reservas WHERE id_recurso = ?");
            $usoStmt->execute([$id]);
            $reservasAsociadas = (int)$usoStmt->fetchColumn();

            if ($reservasAsociadas > 0) {
                redirigirRecursos('eliminacion_bloqueada_historial', ['reservas' => $reservasAsociadas]);
            }

            $stmt = $pdo->prepare("DELETE FROM recursos WHERE id = ?");
            $stmt->execute([$id]);
            redirigirRecursos('eliminado_ok');
        }

        if ($accion === 'actualizar_stock') {
            $id = (int)($_POST['id'] ?? 0);
            $cantidad = max(1, (int)($_POST['cantidad_total'] ?? 1));

            if ($id <= 0) {
                redirigirRecursos('error_id_invalido');
            }

            $tipoStmt = $pdo->prepare("SELECT tipo FROM recursos WHERE id = ? LIMIT 1");
            $tipoStmt->execute([$id]);
            $tipo = $tipoStmt->fetchColumn();

            if (!$tipo) {
                redirigirRecursos('error_id_invalido');
            }

            if ($tipo !== 'equipo') {
                redirigirRecursos('stock_no_aplica');
            }

            $stmt = $pdo->prepare("UPDATE recursos SET cantidad_total = ? WHERE id = ?");
            $stmt->execute([$cantidad, $id]);
            redirigirRecursos('stock_actualizado_ok');
        }

        if ($accion === 'desactivar' || $accion === 'reactivar') {
            $id = (int)($_POST['id'] ?? 0);
            if ($id <= 0) {
                redirigirRecursos('error_id_invalido');
            }

            $nuevoEstado = $accion === 'desactivar' ? 0 : 1;
            $stmt = $pdo->prepare("UPDATE recursos SET activo = ? WHERE id = ?");
            $stmt->execute([$nuevoEstado, $id]);
            redirigirRecursos($accion === 'desactivar' ? 'desactivado_ok' : 'reactivado_ok');
        }

        redirigirRecursos('accion_no_valida');
    } catch (PDOException $e) {
        redirigirRecursos('error_bd');
    }
}