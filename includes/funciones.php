<?php
function estaLogueado() {
    return isset($_SESSION['usuario_id']);
}

function esAdmin() {
    return isset($_SESSION['rol']) && $_SESSION['rol'] == 'admin';
}

function esProfesor() {
    return isset($_SESSION['rol']) && $_SESSION['rol'] == 'profesor';
}

function redirigir($url) {
    header("Location: $url");
    exit;
}

function obtenerRecursos($pdo) {
    $stmt = $pdo->query("SELECT * FROM recursos WHERE activo = 1 ORDER BY nombre");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
/* ==========================================================================
 * NUEVA FUNCIÓN: Calcular disponibilidad real considerando la cantidad pedida
 * ========================================================================== */
function obtenerStockDisponible($pdo, $id_recurso, $fecha, $hora_inicio, $hora_fin) {
    // 1. Consulta la cantidad total original del recurso
    $stmt = $pdo->prepare("SELECT cantidad_total FROM recursos WHERE id = ? AND activo = 1");
    $stmt->execute([$id_recurso]);
    $recurso = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$recurso) {
        return 0; // Si no existe o no está activo, el stock es 0
    }

    $stockTotal = $recurso['cantidad_total'];

    // 2. Suma las unidades ocupadas en ese mismo rango horario
    $sql = "SELECT SUM(cantidad_solicitada) AS ocupados
    FROM reservas
    WHERE id_recurso = ?
    AND fecha = ?
    AND estado IN ('aprobada', 'pendiente')
    AND (hora_inicio < ? AND hora_fin > ?)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_recurso, $fecha, $hora_fin, $hora_inicio]);
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

    $unidadesOcupadas = $resultado['ocupados'] ? $resultado['ocupados'] : 0;

    // 3. Devuelve las unidades realmente libres
    return $stockTotal - $unidadesOcupadas;
}
?>
