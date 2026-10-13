<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

require_once __DIR__ . '/../includes/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function json_response($data, $status = 200): void
{
    http_response_code($status);
    echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

function read_json_body(): array
{
    $raw = file_get_contents('php://input');
    if (trim($raw) === '') {
        return [];
    }

    $decoded = json_decode($raw, true);
    return is_array($decoded) ? $decoded : [];
}

function normalize_ids($value): array
{
    if (is_array($value)) {
        $ids = $value;
    } else {
        $ids = array_map('trim', explode(',', (string) $value));
    }

    $filtered = [];
    foreach ($ids as $id) {
        $intId = (int) $id;
        if ($intId > 0) {
            $filtered[] = $intId;
        }
    }

    return array_values(array_unique($filtered));
}

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$base = '/Proyecto_Final';

if (strpos($path, $base) === 0) {
    $path = substr($path, strlen($base));
}

$parts = array_values(array_filter(explode('/', trim($path, '/')), static fn($part) => $part !== ''));
if (($parts[0] ?? '') === 'api') {
    array_shift($parts);
}

$resource = $parts[0] ?? null;
$method = $_SERVER['REQUEST_METHOD'];

if ($resource === null) {
    json_response([
        'success' => true,
        'endpoints' => [
            'POST /api/login',
            'GET /api/recursos',
            'GET /api/reservas',
            'POST /api/reservas',
            'PATCH /api/reservas',
        ]
    ]);
}

if ($resource === 'login') {
    if ($method !== 'POST') {
        json_response(['success' => false, 'error' => 'Método no permitido'], 405);
    }

    $payload = read_json_body();
    $email = trim((string) ($payload['email'] ?? $payload['usuario'] ?? ''));
    $password = (string) ($payload['password'] ?? $payload['contraseña'] ?? '');

    if ($email === '' || $password === '') {
        json_response(['success' => false, 'error' => 'Email y contraseña son obligatorios'], 400);
    }

    $stmt = $pdo->prepare('SELECT * FROM usuarios WHERE email = ?');
    $stmt->execute([$email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario || $usuario['password'] !== md5($password)) {
        json_response(['success' => false, 'error' => 'Credenciales inválidas'], 401);
    }

    if ((int) $usuario['activo'] !== 1) {
        json_response(['success' => false, 'error' => 'La cuenta está pendiente de aprobación'], 403);
    }

    $_SESSION['usuario_id'] = (int) $usuario['id'];
    $_SESSION['nombre'] = $usuario['nombre'];
    $_SESSION['rol'] = $usuario['rol'];

    json_response([
        'success' => true,
        'user' => [
            'id' => (int) $usuario['id'],
            'nombre' => $usuario['nombre'],
            'email' => $usuario['email'],
            'rol' => $usuario['rol'],
        ]
    ]);
}

if ($resource === 'recursos') {
    if ($method !== 'GET') {
        json_response(['success' => false, 'error' => 'Método no permitido'], 405);
    }

    $tipo = isset($_GET['tipo']) ? trim((string) $_GET['tipo']) : null;
    $activo = isset($_GET['activo']) ? (int) $_GET['activo'] : 1;

    $sql = 'SELECT id, nombre, tipo, descripcion, cantidad_total, activo FROM recursos WHERE activo = :activo';
    $params = [':activo' => $activo];

    if ($tipo !== null && $tipo !== '') {
        $sql .= ' AND tipo = :tipo';
        $params[':tipo'] = $tipo;
    }

    $sql .= ' ORDER BY tipo ASC, nombre ASC';
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    json_response([
        'success' => true,
        'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)
    ]);
}

if ($resource === 'reservas') {
    $usuarioId = $_SESSION['usuario_id'] ?? null;
    if (!$usuarioId) {
        json_response(['success' => false, 'error' => 'No autenticado'], 401);
    }

    if ($method === 'GET') {
        if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], ['admin', 'profesor'], true)) {
            json_response(['success' => false, 'error' => 'Rol no permitido'], 403);
        }

        if ($_SESSION['rol'] === 'admin') {
            $sql = "
                SELECT r.id, r.id_usuario, r.fecha, r.hora_inicio, r.hora_fin, r.estado,
                       r.motivo_profesor, r.motivo_rechazo, r.created_at,
                       u.nombre AS usuario_nombre, u.email AS usuario_email,
                       rec.nombre AS recurso_nombre, rec.tipo AS recurso_tipo
                FROM reservas r
                INNER JOIN usuarios u ON r.id_usuario = u.id
                INNER JOIN recursos rec ON r.id_recurso = rec.id
                ORDER BY r.created_at DESC, r.fecha DESC, r.hora_inicio DESC, r.id DESC
            ";
        } else {
            $sql = "
                SELECT r.id, r.id_usuario, r.fecha, r.hora_inicio, r.hora_fin, r.estado,
                       r.motivo_profesor, r.motivo_rechazo, r.created_at,
                       rec.nombre AS recurso_nombre, rec.tipo AS recurso_tipo
                FROM reservas r
                LEFT JOIN recursos rec ON r.id_recurso = rec.id
                WHERE r.id_usuario = :usuario_id
                ORDER BY r.created_at DESC, r.fecha DESC, r.hora_inicio DESC, r.id DESC
            ";
        }

        $stmt = $pdo->prepare($sql);
        if ($_SESSION['rol'] === 'profesor') {
            $stmt->execute([':usuario_id' => $usuarioId]);
        } else {
            $stmt->execute();
        }

        json_response([
            'success' => true,
            'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)
        ]);
    }

    if ($method === 'POST') {
        if ($_SESSION['rol'] !== 'profesor') {
            json_response(['success' => false, 'error' => 'Solo el profesor puede crear reservas'], 403);
        }

        $payload = read_json_body();
        $fecha = trim((string) ($payload['fecha'] ?? ''));
        $horaInicio = trim((string) ($payload['hora_inicio'] ?? ''));
        $cantidadClases = (int) ($payload['cantidad_clases'] ?? 1);
        $salaId = !empty($payload['sala_id']) ? (int) $payload['sala_id'] : null;
        $equipoId = !empty($payload['equipo_id']) ? (int) $payload['equipo_id'] : null;
        $motivoProfesor = isset($payload['motivo_profesor']) ? trim((string) $payload['motivo_profesor']) : null;

        if ($fecha === '' || $horaInicio === '' || $cantidadClases < 1) {
            json_response(['success' => false, 'error' => 'Fecha, hora y cantidad de clases son obligatorios'], 400);
        }

        if (!$salaId && !$equipoId) {
            json_response(['success' => false, 'error' => 'Debes seleccionar al menos una sala o un equipo'], 400);
        }

        $duracionBloque = 45;
        $bloques = [];
        for ($i = 0; $i < $cantidadClases; $i++) {
            $inicio = date('H:i:s', strtotime($fecha . ' ' . $horaInicio) + ($i * $duracionBloque * 60));
            $fin = date('H:i:s', strtotime($fecha . ' ' . $horaInicio) + (($i + 1) * $duracionBloque * 60));
            $bloques[] = ['inicio' => $inicio, 'fin' => $fin];
        }

        $recursos = [];
        if ($salaId) {
            $recursos[] = ['id' => $salaId, 'tipo' => 'sala'];
        }
        if ($equipoId) {
            $recursos[] = ['id' => $equipoId, 'tipo' => 'equipo'];
        }

        foreach ($recursos as $recurso) {
            foreach ($bloques as $bloque) {
                $check = $pdo->prepare(
                    "SELECT id FROM reservas WHERE id_recurso = ? AND fecha = ? AND estado = 'aprobada' AND hora_inicio < ? AND hora_fin > ?"
                );
                $check->execute([$recurso['id'], $fecha, $bloque['fin'], $bloque['inicio']]);

                if ($check->fetchColumn()) {
                    json_response([
                        'success' => false,
                        'error' => 'El recurso ya está reservado en ese horario',
                        'recurso_id' => $recurso['id'],
                        'horario' => $bloque['inicio'] . ' - ' . $bloque['fin']
                    ], 409);
                }
            }
        }

        $created = [];
        foreach ($recursos as $recurso) {
            foreach ($bloques as $bloque) {
                $stmt = $pdo->prepare(
                    'INSERT INTO reservas (id_usuario, id_recurso, fecha, hora_inicio, hora_fin, estado, motivo_profesor) VALUES (?, ?, ?, ?, ?, ?, ?)'
                );
                $stmt->execute([
                    $usuarioId,
                    $recurso['id'],
                    $fecha,
                    $bloque['inicio'],
                    $bloque['fin'],
                    'pendiente',
                    $motivoProfesor
                ]);

                $created[] = [
                    'id' => (int) $pdo->lastInsertId(),
                    'id_recurso' => $recurso['id'],
                    'tipo' => $recurso['tipo'],
                    'fecha' => $fecha,
                    'hora_inicio' => $bloque['inicio'],
                    'hora_fin' => $bloque['fin'],
                    'estado' => 'pendiente',
                ];
            }
        }

        json_response([
            'success' => true,
            'message' => 'Reserva creada correctamente',
            'data' => $created
        ], 201);
    }

    if (in_array($method, ['PUT', 'PATCH'], true)) {
        $payload = read_json_body();
        $estado = strtolower((string) ($payload['estado'] ?? ''));
        $ids = normalize_ids($payload['ids'] ?? $payload['id_reserva'] ?? []);

        if (!in_array($estado, ['aprobada', 'rechazada', 'cancelada', 'pendiente'], true) || empty($ids)) {
            json_response(['success' => false, 'error' => 'Estado o ids inválidos'], 400);
        }

        if ($_SESSION['rol'] !== 'admin' && $estado !== 'cancelada') {
            json_response(['success' => false, 'error' => 'Solo el admin puede cambiar ese estado'], 403);
        }

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        if ($_SESSION['rol'] !== 'admin') {
            $check = $pdo->prepare("SELECT id FROM reservas WHERE id IN ($placeholders) AND id_usuario = ?");
            $check->execute(array_merge($ids, [$usuarioId]));
            $owned = $check->fetchAll(PDO::FETCH_COLUMN);
            if (count($owned) !== count($ids)) {
                json_response(['success' => false, 'error' => 'No podés modificar reservas ajenas'], 403);
            }
        }

        $values = [$estado];
        $fields = ['estado = ?'];

        if ($estado === 'rechazada' && $_SESSION['rol'] === 'admin') {
            $motivo = trim((string) ($payload['motivo_rechazo'] ?? ''));
            if ($motivo === '') {
                json_response(['success' => false, 'error' => 'El motivo de rechazo es obligatorio'], 400);
            }
            $fields[] = 'motivo_rechazo = ?';
            $values[] = $motivo;
        }

        if ($estado === 'cancelada' && $_SESSION['rol'] !== 'admin') {
            $motivo = trim((string) ($payload['motivo_cancelacion'] ?? 'Cancelada por el profesor'));
            $fields[] = 'motivo_profesor = ?';
            $values[] = $motivo;
        }

        $sql = 'UPDATE reservas SET ' . implode(', ', $fields) . ' WHERE id IN (' . $placeholders . ')';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array_merge($values, $ids));

        json_response([
            'success' => true,
            'updated' => $ids,
            'estado' => $estado,
        ]);
    }

    json_response(['success' => false, 'error' => 'Ruta no encontrada'], 404);
}

json_response(['success' => false, 'error' => 'Ruta no encontrada'], 404);
