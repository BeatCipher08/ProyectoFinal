<?php
session_start();
require_once __DIR__ . '/../includes/config.php';

function obtenerRedirectDestinoProfesor(): string {
    $destino = basename((string)($_POST['redirect_to'] ?? ''));
    $permitidos = ['calendario_profesor.php'];
    return in_array($destino, $permitidos, true) ? $destino : 'calendario_profesor.php';
}

$redirectProfesor = obtenerRedirectDestinoProfesor();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: Inicio_sesion.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    if ($_SESSION['rol'] === 'admin') {
        header("Location: reservas_admin.php");
    } else {
        header("Location: " . $redirectProfesor);
    }
    exit();
}

$id_reserva_raw = $_POST['id_reserva'] ?? '';
$ids = array_filter(array_map('trim', explode(',', (string)$id_reserva_raw)), fn($id) => $id !== '');
$nuevo_estado = strtolower($_POST['estado'] ?? '');
$motivo_rechazo = isset($_POST['motivo_rechazo']) ? trim($_POST['motivo_rechazo']) : null;
$motivo_cancelacion = isset($_POST['motivo_cancelacion']) ? trim($_POST['motivo_cancelacion']) : null;

if (!in_array($nuevo_estado, ['aprobada', 'rechazada', 'cancelada', 'pendiente'], true) || empty($ids)) {
    if ($_SESSION['rol'] === 'admin') {
        header("Location: reservas_admin.php");
    } else {
        header("Location: " . $redirectProfesor);
    }
    exit();
}

$ids_int = array_map('intval', $ids);

if ($_SESSION['rol'] !== 'admin') {
    $placeholders = implode(',', array_fill(0, count($ids_int), '?'));
    $stmt = $pdo->prepare("SELECT id FROM reservas WHERE id IN ($placeholders) AND id_usuario = ?");
    $stmt->execute(array_merge($ids_int, [(int)$_SESSION['usuario_id']]));
    $ids_propias = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (count($ids_propias) !== count($ids_int)) {
        header("Location: " . $redirectProfesor);
        exit();
    }

    $ids_int = array_map('intval', $ids_propias);
}

$placeholders = implode(',', array_fill(0, count($ids_int), '?'));
$updateFields = ["estado = ?"];
$updateValues = [$nuevo_estado];

// Agregar motivo_rechazo si es rechazo por admin
if ($nuevo_estado === 'rechazada' && $_SESSION['rol'] === 'admin') {
    if (empty($motivo_rechazo)) {
        header("Location: reservas_admin.php");
        exit();
    }
    $updateFields[] = "motivo_rechazo = ?";
    $updateValues[] = $motivo_rechazo;
}

// Agregar motivo_profesor si es cancelación por profesor
if ($nuevo_estado === 'cancelada' && $_SESSION['rol'] !== 'admin') {
    $updateFields[] = "motivo_profesor = ?";
    $updateValues[] = $motivo_cancelacion ?? 'Cancelada por el profesor';
}

$updateSQL = implode(', ', $updateFields);
$stmt = $pdo->prepare("UPDATE reservas SET $updateSQL WHERE id IN ($placeholders)");
$stmt->execute(array_merge($updateValues, $ids_int));

if ($_SESSION['rol'] === 'admin') {
    header("Location: reservas_admin.php");
} else {
    header("Location: " . $redirectProfesor);
}
exit();
?>