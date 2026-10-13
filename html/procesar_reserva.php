<?php
session_start();
require_once __DIR__ . '/../includes/config.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'profesor') {
    die("Acceso denegado. <a href='Inicio_sesion.php'>Iniciar sesión</a>");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $isAjax = isset($_POST['ajax']) && $_POST['ajax'] === '1';
    
    if ($isAjax) {
        header('Content-Type: application/json; charset=utf-8');
    }
    
    $id_usuario = $_SESSION['usuario_id'];
    $fecha = $_POST['fecha'] ?? null;
    $hora_inicio = $_POST['hora_inicio'] ?? null;

    if (!$hora_inicio && isset($_POST['horario'])) {
        $horarioLegacy = explode(' - ', $_POST['horario']);
        if (count($horarioLegacy) === 2) {
            $hora_inicio = trim($horarioLegacy[0]);
            $hora_fin_legacy = trim($horarioLegacy[1]);
            $diferenciaMinutos = ((int)substr($hora_fin_legacy, 0, 2) * 60 + (int)substr($hora_fin_legacy, 3, 2)) - ((int)substr($hora_inicio, 0, 2) * 60 + (int)substr($hora_inicio, 3, 2));
            $cantidad_clases = max(1, (int)round($diferenciaMinutos / 45));
        }
    }

    $cantidad_clases = isset($_POST['cantidad_clases']) ? (int)$_POST['cantidad_clases'] : (isset($cantidad_clases) ? $cantidad_clases : 1);

    $id_recurso_sala = !empty($_POST['sala_id']) ? (int)$_POST['sala_id'] : null;
    $id_recurso_equipo = !empty($_POST['equipo_id']) ? (int)$_POST['equipo_id'] : null;
    $motivo_profesor = isset($_POST['motivo_profesor']) ? trim($_POST['motivo_profesor']) : null;

    if (!$fecha || !$hora_inicio || $cantidad_clases < 1) {
        $msg = 'Debes seleccionar una fecha, hora de inicio y cantidad de clases válidas.';
        if ($isAjax) {
            echo json_encode(['success' => false, 'error' => $msg]);
            exit();
        }
        die("<script>alert('Error: $msg'); window.history.back();</script>");
    }

    if (!preg_match('/^\d{2}:\d{2}$/', $hora_inicio)) {
        $msg = 'La hora de inicio seleccionada no es válida.';
        if ($isAjax) {
            echo json_encode(['success' => false, 'error' => $msg]);
            exit();
        }
        die("<script>alert('Error: $msg'); window.history.back();</script>");
    }

    $inicioMinutos = ((int)substr($hora_inicio, 0, 2) * 60) + (int)substr($hora_inicio, 3, 2);
    $horaMinima = 7 * 60;
    $horaMaximaInicio = (22 * 60) + 45;
    $duracionBloque = 45;
    $duracionTotal = $cantidad_clases * $duracionBloque;
    $finMinutos = $inicioMinutos + $duracionTotal;
    $cierreMinutos = (23 * 60) + 55;

    if ($inicioMinutos < $horaMinima || $inicioMinutos > $horaMaximaInicio) {
        $msg = 'La hora de inicio debe estar dentro del horario institucional en bloques de 45 minutos.';
        if ($isAjax) {
            echo json_encode(['success' => false, 'error' => $msg]);
            exit();
        }
        die("<script>alert('Error: $msg'); window.history.back();</script>");
    }

    if ($finMinutos > $cierreMinutos) {
        $msg = 'La reserva excede el horario de atención de la institución (07:00 a 23:55).';
        if ($isAjax) {
            echo json_encode(['success' => false, 'error' => $msg]);
            exit();
        }
        die("<script>alert('Error: $msg'); window.history.back();</script>");
    }

    function sumarMinutos($hora, $sumarMinutos) {
        $segundos = strtotime('2000-01-01 ' . $hora . ':00');
        $segundos += $sumarMinutos * 60;
        return date('H:i:s', $segundos);
    }

    function obtenerNombreRecurso($pdo, $id_recurso) {
        $stmt = $pdo->prepare("SELECT nombre FROM recursos WHERE id = ?");
        $stmt->execute([$id_recurso]);
        return $stmt->fetchColumn() ?: 'Recurso';
    }

    function obtenerRangosConflictuados($pdo, $id_recurso, $fecha, $bloques) {
        $rangos = [];

        foreach ($bloques as $bloque) {
            $stmt = $pdo->prepare(
                "SELECT id
                 FROM reservas
                 WHERE id_recurso = :id_recurso
                   AND fecha = :fecha
                   AND estado = 'aprobada'
                   AND hora_inicio < :hora_fin
                   AND hora_fin > :hora_inicio"
            );

            $stmt->execute([
                ':id_recurso' => $id_recurso,
                ':fecha' => $fecha,
                ':hora_inicio' => $bloque['inicio'],
                ':hora_fin' => $bloque['fin'],
            ]);

            if ($stmt->fetchColumn()) {
                $rangos[] = $bloque['inicio'] . ' - ' . $bloque['fin'];
            }
        }

        return array_values(array_unique($rangos));
    }

    function construirMensajeConflicto($conflictosPorRecurso) {
        $frases = [];

        foreach ($conflictosPorRecurso as $conflicto) {
            $tipo = $conflicto['tipo'] === 'equipo' ? 'El equipo' : 'La sala';
            $frases[] = $tipo . ' "' . $conflicto['nombre'] . '" ya está reservado en el horario ' . implode(', ', $conflicto['rangos']);
        }

        if (count($frases) === 1) {
            return 'Error: ' . $frases[0];
        }

        if (count($frases) === 2) {
            return 'Error: ' . $frases[0] . ' y ' . $frases[1];
        }

        return 'Error: ' . $frases[0] . ' y ' . $frases[1];
    }

    $bloques = [];
    for ($i = 0; $i < $cantidad_clases; $i++) {
        $inicioBloque = sumarMinutos($hora_inicio, $i * $duracionBloque);
        $finBloque = sumarMinutos($hora_inicio, ($i + 1) * $duracionBloque);
        $bloques[] = ['inicio' => $inicioBloque, 'fin' => $finBloque];
    }

    $conflictosPorRecurso = [];

    if ($id_recurso_sala) {
        $rangosSala = obtenerRangosConflictuados($pdo, $id_recurso_sala, $fecha, $bloques);
        if (!empty($rangosSala)) {
            $conflictosPorRecurso[] = [
                'tipo' => 'sala',
                'nombre' => obtenerNombreRecurso($pdo, $id_recurso_sala),
                'rangos' => $rangosSala,
            ];
        }
    }

    if ($id_recurso_equipo) {
        $rangosEquipo = obtenerRangosConflictuados($pdo, $id_recurso_equipo, $fecha, $bloques);
        if (!empty($rangosEquipo)) {
            $conflictosPorRecurso[] = [
                'tipo' => 'equipo',
                'nombre' => obtenerNombreRecurso($pdo, $id_recurso_equipo),
                'rangos' => $rangosEquipo,
            ];
        }
    }

    if (!empty($conflictosPorRecurso)) {
        $errorMsg = construirMensajeConflicto($conflictosPorRecurso);
        if ($isAjax) {
            echo json_encode(['success' => false, 'error' => $errorMsg]);
            exit();
        }
        $_SESSION['reserva_error'] = $errorMsg;
        header('Location: reservas_profesor.php');
        exit();
    }

    if (!$id_recurso_sala && !$id_recurso_equipo) {
        $msg = 'Debes seleccionar al menos una sala o un equipo para realizar la reserva.';
        if ($isAjax) {
            echo json_encode(['success' => false, 'error' => $msg]);
            exit();
        }
        die("<script>alert('Error: $msg'); window.history.back();</script>");
    }

    if ($id_recurso_sala) {
        foreach ($bloques as $bloque) {
            $stmt = $pdo->prepare("INSERT INTO reservas (id_usuario, id_recurso, fecha, hora_inicio, hora_fin, estado, motivo_profesor) VALUES (?, ?, ?, ?, ?, 'pendiente', ?)");
            $stmt->execute([$id_usuario, $id_recurso_sala, $fecha, $bloque['inicio'], $bloque['fin'], $motivo_profesor]);
        }
    }

    if ($id_recurso_equipo) {
        foreach ($bloques as $bloque) {
            $stmt = $pdo->prepare("INSERT INTO reservas (id_usuario, id_recurso, fecha, hora_inicio, hora_fin, estado, motivo_profesor) VALUES (?, ?, ?, ?, ?, 'pendiente', ?)");
            $stmt->execute([$id_usuario, $id_recurso_equipo, $fecha, $bloque['inicio'], $bloque['fin'], $motivo_profesor]);
        }
    }

    if ($isAjax) {
        echo json_encode(['success' => true]);
        exit();
    }

    header("Location: reservas_profesor.php");
    exit();
}