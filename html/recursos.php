<?php
ini_set('display_errors', 1); 
error_reporting(E_ALL);
if (session_status() === PHP_SESSION_NONE) { session_start(); }

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'admin') {
    header("Location: Inicio_sesion.php");
    exit();
}

require_once __DIR__ . '/../includes/config.php';

$estadoOperacion = $_GET['estado'] ?? '';
$reservasBloqueo = (int)($_GET['reservas'] ?? 0);
$mensajeOperacion = '';
$tipoMensaje = '';

if ($estadoOperacion === 'guardado_ok') {
    $mensajeOperacion = 'Recurso guardado correctamente.';
    $tipoMensaje = 'ok';
} elseif ($estadoOperacion === 'eliminado_ok') {
    $mensajeOperacion = 'Recurso eliminado correctamente.';
    $tipoMensaje = 'ok';
} elseif ($estadoOperacion === 'desactivado_ok') {
    $mensajeOperacion = 'Recurso desactivado correctamente.';
    $tipoMensaje = 'ok';
} elseif ($estadoOperacion === 'reactivado_ok') {
    $mensajeOperacion = 'Recurso reactivado correctamente.';
    $tipoMensaje = 'ok';
} elseif ($estadoOperacion === 'stock_actualizado_ok') {
    $mensajeOperacion = 'Stock actualizado correctamente.';
    $tipoMensaje = 'ok';
} elseif ($estadoOperacion === 'desactivado_por_historial') {
    $mensajeOperacion = 'El recurso tiene reservas asociadas y no se puede eliminar. Se marcó como en mantenimiento.';
    $tipoMensaje = 'aviso';
} elseif ($estadoOperacion === 'eliminacion_bloqueada_historial') {
    $mensajeOperacion = 'No se puede eliminar el recurso porque tiene reservas asociadas' . ($reservasBloqueo > 0 ? ' (' . $reservasBloqueo . ')' : '') . '. Usá la opción de desactivar.';
    $tipoMensaje = 'aviso';
} elseif ($estadoOperacion === 'stock_no_aplica') {
    $mensajeOperacion = 'No se puede editar stock para salas o laboratorios. Esos recursos se gestionan como espacios únicos.';
    $tipoMensaje = 'aviso';
} elseif ($estadoOperacion === 'error_bd') {
    $mensajeOperacion = 'No se pudo completar la operación por un error de base de datos.';
    $tipoMensaje = 'error';
} elseif ($estadoOperacion === 'error_subida') {
    $mensajeOperacion = 'No se pudo subir la imagen del recurso.';
    $tipoMensaje = 'error';
} elseif ($estadoOperacion === 'error_ruta_uploads') {
    $mensajeOperacion = 'La carpeta de destino para imágenes no existe.';
    $tipoMensaje = 'error';
} elseif ($estadoOperacion === 'error_permisos_uploads') {
    $mensajeOperacion = 'No hay permisos para guardar la imagen en la carpeta uploads/recursos.';
    $tipoMensaje = 'error';
} elseif ($estadoOperacion === 'error_id_invalido') {
    $mensajeOperacion = 'El recurso seleccionado no es válido.';
    $tipoMensaje = 'error';
}

$total_recursos = $pdo->query("SELECT COUNT(*) FROM recursos")->fetchColumn();
$recursos_disponibles = $pdo->query("SELECT COUNT(*) FROM recursos WHERE activo = 1")->fetchColumn();
$recursos_mantenimiento = $total_recursos - $recursos_disponibles;

$resultado_recursos = $pdo->query(
    "SELECT r.*, COALESCE(rr.total_reservas, 0) AS total_reservas
     FROM recursos r
     LEFT JOIN (
         SELECT id_recurso, COUNT(*) AS total_reservas
         FROM reservas
         GROUP BY id_recurso
     ) rr ON rr.id_recurso = r.id
     ORDER BY r.tipo ASC, r.nombre ASC"
);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salas y Recursos</title>
    <link rel="stylesheet" href="../css/style_aulas.css">
</head>
<body>

    <?php require_once __DIR__ . '/../includes/barra_admin.php'; ?>

    <main class="contenedor">
        <?php if ($mensajeOperacion !== ''): ?>
            <div style="padding: 12px 14px; margin-bottom: 18px; border-radius: 8px; font-weight: 600; <?php echo $tipoMensaje === 'ok' ? 'background:#e8f7e8;color:#2d6a2d;border:1px solid #b7deb7;' : ($tipoMensaje === 'aviso' ? 'background:#fff7e6;color:#946200;border:1px solid #f1d79a;' : 'background:#fdeaea;color:#9b2c2c;border:1px solid #f1b5b5;'); ?>">
                <?php echo htmlspecialchars($mensajeOperacion); ?>
            </div>
        <?php endif; ?>

        <section class="encabezado">
            <div>
                <h1>Salas y Recursos</h1>
                <p>Administrá las salas, laboratorios y equipamiento disponibles.</p>
            </div>
            <button class="btn-agregar" onclick="abrirFormulario()">+ Agregar recurso</button>
        </section>

        <section class="estadisticas">
            <div class="tarjeta">
                <span class="numero"><?php echo $total_recursos; ?></span>
                <span>Total en inventario</span>
            </div>
            <div class="tarjeta">
                <span class="numero"><?php echo $recursos_disponibles; ?></span>
                <span>Disponibles (Activos)</span>
            </div>
            <div class="tarjeta">
                <span class="numero mantenimiento"><?php echo $recursos_mantenimiento; ?></span>
                <span>En mantenimiento</span>
            </div>
        </section>

        <section class="panel">
            <div class="pestanas-wrap">
                <div class="pestanas">
                    <button type="button" class="btn-pestana activa" data-categoria="espacios" onclick="cambiarPestana('espacios', this)">Salas y Laboratorios</button>
                    <button type="button" class="btn-pestana" data-categoria="equipos" onclick="cambiarPestana('equipos', this)">Equipamiento</button>
                </div>

                <div class="filtro-desactivados" aria-label="Filtro de recursos desactivados">
                    <span class="filtro-label">Solo activos</span>
                    <button
                        type="button"
                        id="toggleDesactivados"
                        class="toggle-desactivados"
                        aria-pressed="false"
                        onclick="alternarDesactivados(this)">
                        <span class="toggle-knob"></span>
                    </button>
                    <span class="filtro-label">Incluir desactivados</span>
                </div>
            </div>

            <div class="tabla-contenedor">
                <table>
                    <thead>
                        <tr>
                            <th>Imagen/Info</th>
                            <th>Recurso</th>
                            <th>Tipo</th>
                            <th>Stock</th>
                            <th>Descripción</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tablaRecursos">
                        <?php while ($recurso = $resultado_recursos->fetch(PDO::FETCH_ASSOC)): ?>
                            <?php
                                $tipoDB = $recurso['tipo'];
                                $categoria = ($tipoDB == 'equipo') ? 'equipos' : 'espacios';
                                $estadoTexto = $recurso['activo'] == 1 ? 'Disponible' : 'Mantenimiento';
                                $claseEstado = $recurso['activo'] == 1 ? 'disponible' : 'mantenimiento';
                                $totalReservas = (int)($recurso['total_reservas'] ?? 0);
                                $tieneHistorial = $totalReservas > 0;
                            ?>
                            <tr data-categoria="<?php echo $categoria; ?>" data-activo="<?php echo (int)$recurso['activo']; ?>">
                                <td style="width: 150px; padding: 15px 10px; vertical-align: middle;">
                                    <?php if (!empty($recurso['imagen'])): ?>
                                        <img src="../<?php echo htmlspecialchars($recurso['imagen']); ?>" alt="Foto" style="width: 130px; height: 90px; object-fit: cover; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.15);">
                                    <?php else: ?>
                                        <div style="width: 130px; height: 90px; background: #fafafa; border-radius: 8px; display: flex; flex-direction: column; align-items: center; justify-content: center; font-size: 28px; border: 2px dashed #ddd; color: #aaa;">
                                            <?php echo in_array($tipoDB, ['sala', 'laboratorio'], true) ? '🏫' : '💻'; ?>
                                            <span style="font-size: 11px; margin-top: 5px; font-weight: bold;">Sin foto</span>
                                        </div>
                                    <?php endif; ?>
                                </td>

                                <td style="vertical-align: middle;">
                                    <strong><?php echo htmlspecialchars($recurso['nombre']); ?></strong>
                                    <small>ID: <?php echo (int)$recurso['id']; ?></small>
                                </td>
                                <td><span class="tipo"><?php echo ucfirst($tipoDB); ?></span></td>
                                <td>
                                    <?php if ($tipoDB === 'equipo'): ?>
                                        <form action="procesar_recurso.php" method="POST" class="stock-form">
                                            <input type="hidden" name="id" value="<?php echo (int)$recurso['id']; ?>">
                                            <input type="hidden" name="accion" value="actualizar_stock">
                                            <input type="number" name="cantidad_total" min="1" value="<?php echo (int)$recurso['cantidad_total']; ?>" aria-label="Stock de <?php echo htmlspecialchars($recurso['nombre']); ?>">
                                            <button type="submit" class="btn-stock">Guardar</button>
                                        </form>
                                    <?php else: ?>
                                        <span class="stock-no-aplica">No aplica</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($recurso['descripcion']); ?></td>
                                <td><span class="estado <?php echo $claseEstado; ?>"><?php echo $estadoTexto; ?></span></td>
                                <td class="acciones">
                                    <?php if ($tieneHistorial): ?>
                                        <form action="procesar_recurso.php" method="POST" style="display:inline;">
                                            <input type="hidden" name="id" value="<?php echo $recurso['id']; ?>">
                                            <input type="hidden" name="accion" value="<?php echo $recurso['activo'] == 1 ? 'desactivar' : 'reactivar'; ?>">
                                            <?php if ($recurso['activo'] == 1): ?>
                                                <button type="submit" class="btn-desactivar" onclick="return abrirConfirmacionCambioEstado(this.form, 'desactivar', '<?php echo htmlspecialchars($recurso['nombre'], ENT_QUOTES); ?>')">Desactivar</button>
                                            <?php else: ?>
                                                <button type="submit" class="btn-reactivar" onclick="return abrirConfirmacionCambioEstado(this.form, 'reactivar', '<?php echo htmlspecialchars($recurso['nombre'], ENT_QUOTES); ?>')">Reactivar</button>
                                            <?php endif; ?>
                                        </form>
                                        <button type="button" class="btn-bloqueado" onclick="mostrarMotivoNoEliminable(<?php echo $totalReservas; ?>)">No eliminable</button>
                                        <small class="nota-historial">Con historial (<?php echo $totalReservas; ?>)</small>
                                    <?php else: ?>
                                        <form action="procesar_recurso.php" method="POST" style="display:inline;">
                                            <input type="hidden" name="id" value="<?php echo $recurso['id']; ?>">
                                            <input type="hidden" name="accion" value="eliminar">
                                            <button type="submit" class="btn-eliminar" onclick="return confirmarEliminacion('<?php echo htmlspecialchars($recurso['nombre'], ENT_QUOTES); ?>')">Eliminar definitivo</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <div id="modalRecurso" class="modal">
        <div class="modal-contenido">
            <span class="cerrar" onclick="cerrarFormulario()">×</span>
            <h2 id="tituloFormulario">Agregar recurso</h2>
            
            <form id="formRecurso" action="procesar_recurso.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="accion" value="guardar">
                
                <label>Nombre</label>
                <input type="text" name="nombre" required>

                <label>Tipo</label>
                <select id="tipoRecurso" name="tipo" onchange="verificarTipo()" required>
                    <option value="sala">Sala</option>
                    <option value="laboratorio">Laboratorio</option>
                    <option value="equipo">Equipamiento</option>
                </select>

                <label>Descripción</label>
                <input type="text" name="descripcion" required>

                <div id="campoCantidad" style="margin-top: 0;">
                    <label>Cantidad en inventario</label>
                    <input id="cantidadTotalInput" type="number" name="cantidad_total" value="1" min="1" required>
                </div>

                <label>Estado</label>
                <select id="estadoRecurso" name="activo" required>
                    <option value="1">Activo / Disponible</option>
                    <option value="0">En mantenimiento</option>
                </select>

                <div id="campoImagen" style="display: block; margin-top: 15px;">
                    <label>Subir foto del recurso (Opcional)</label>
                    <input type="file" name="imagen_equipo" accept="image/*">
                    <small style="display:block; margin-top:6px; color:#6b7280;">Podés cargar imagen para salones, laboratorios y equipamiento.</small>
                </div>

                <button type="submit" class="btn-guardar" style="margin-top: 20px;">Guardar recurso</button>
            </form>
        </div>
    </div>

    <div id="modalConfirmacionAccion" class="modal modal-aviso" style="display:none;">
        <div class="modal-aviso-contenido">
            <h3 id="tituloConfirmacionAccion">Confirmar acción</h3>
            <p id="textoConfirmacionAccion"></p>
            <div class="modal-aviso-acciones">
                <button type="button" class="btn-aviso-cancelar" onclick="cerrarConfirmacionAccion()">Cancelar</button>
                <button type="button" class="btn-aviso-confirmar" onclick="confirmarAccionPendiente()">Confirmar</button>
            </div>
        </div>
    </div>

    <div id="modalInfoAccion" class="modal modal-aviso" style="display:none;">
        <div class="modal-aviso-contenido">
            <h3>No se puede eliminar</h3>
            <p id="textoInfoAccion"></p>
            <div class="modal-aviso-acciones">
                <button type="button" class="btn-aviso-confirmar" onclick="cerrarInfoAccion()">Entendido</button>
            </div>
        </div>
    </div>

    <script src="../scripts/recursos.js"></script>
    <script>
        let formularioPendiente = null;

        function abrirModalConfirmacion(titulo, texto, form) {
            formularioPendiente = form;
            document.getElementById('tituloConfirmacionAccion').textContent = titulo;
            document.getElementById('textoConfirmacionAccion').textContent = texto;
            document.getElementById('modalConfirmacionAccion').style.display = 'flex';
            return false;
        }

        function cerrarConfirmacionAccion() {
            formularioPendiente = null;
            document.getElementById('modalConfirmacionAccion').style.display = 'none';
        }

        function confirmarAccionPendiente() {
            if (formularioPendiente) {
                const form = formularioPendiente;
                cerrarConfirmacionAccion();
                form.submit();
            }
        }

        function confirmarEliminacion(nombreRecurso) {
            const texto = 'Vas a eliminar definitivamente el recurso "' + nombreRecurso + '". Esta acción no se puede deshacer.';
            return abrirModalConfirmacion('Confirmar eliminación', texto, event.target.form);
        }

        function abrirConfirmacionCambioEstado(form, accion, nombreRecurso) {
            const esDesactivar = accion === 'desactivar';
            const titulo = esDesactivar ? 'Confirmar desactivación' : 'Confirmar reactivación';
            const texto = esDesactivar
                ? 'El recurso "' + nombreRecurso + '" tiene historial y no puede eliminarse. ¿Deseas desactivarlo?'
                : '¿Deseas reactivar el recurso "' + nombreRecurso + '" para que vuelva a estar disponible?';
            return abrirModalConfirmacion(titulo, texto, form);
        }

        function mostrarMotivoNoEliminable(totalReservas) {
            document.getElementById('textoInfoAccion').textContent =
                'Este recurso tiene ' + totalReservas + ' reserva(s) asociada(s). Para mantener la integridad del historial, solo puede desactivarse.';
            document.getElementById('modalInfoAccion').style.display = 'flex';
        }

        function cerrarInfoAccion() {
            document.getElementById('modalInfoAccion').style.display = 'none';
        }
    </script>
</body>
</html>