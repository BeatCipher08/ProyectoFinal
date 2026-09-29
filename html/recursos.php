<?php
ini_set('display_errors', 1); 
error_reporting(E_ALL);
if (session_status() === PHP_SESSION_NONE) { session_start(); }

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'admin') {
    header("Location: Inicio_sesion.php");
    exit();
}

require_once __DIR__ . '/../includes/config.php';

$total_recursos = $pdo->query("SELECT COUNT(*) FROM recursos")->fetchColumn();
$recursos_disponibles = $pdo->query("SELECT COUNT(*) FROM recursos WHERE activo = 1")->fetchColumn();
$recursos_mantenimiento = $total_recursos - $recursos_disponibles;

$resultado_recursos = $pdo->query("SELECT * FROM recursos ORDER BY tipo ASC, nombre ASC");
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
            <div class="pestanas" style="margin-bottom: 20px;">
                <button class="btn-pestana activa" onclick="cambiarPestana('espacios', this)" style="padding: 10px 20px; font-weight: bold; cursor: pointer; border: none; background: #e5f2e5; color: #4f8c4f; border-radius: 5px;">Salas y Laboratorios</button>
                <button class="btn-pestana" onclick="cambiarPestana('equipos', this)" style="padding: 10px 20px; font-weight: bold; cursor: pointer; border: none; background: transparent; color: #777; border-radius: 5px;">Equipamiento</button>
            </div>

            <div class="tabla-contenedor">
                <table>
                    <thead>
                        <tr>
                            <th>Imagen/Info</th>
                            <th>Recurso</th>
                            <th>Tipo</th>
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
                            ?>
                            <tr data-categoria="<?php echo $categoria; ?>">
                                <td style="width: 150px; padding: 15px 10px; vertical-align: middle;">
                                    <?php if ($tipoDB == 'equipo' && !empty($recurso['imagen'])): ?>
                                        <img src="../<?php echo htmlspecialchars($recurso['imagen']); ?>" alt="Foto" style="width: 130px; height: 90px; object-fit: cover; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.15);">
                                    <?php elseif ($tipoDB == 'equipo'): ?>
                                        <div style="width: 130px; height: 90px; background: #fafafa; border-radius: 8px; display: flex; flex-direction: column; align-items: center; justify-content: center; font-size: 28px; border: 2px dashed #ddd; color: #aaa;">
                                            💻
                                            <span style="font-size: 11px; margin-top: 5px; font-weight: bold;">Sin foto</span>
                                        </div>
                                    <?php else: ?>
                                        <div style="width: 60px; height: 60px; background: #e5f2e5; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 24px; color: #4f8c4f;">
                                            🏫
                                        </div>
                                    <?php endif; ?>
                                </td>

                                <td style="vertical-align: middle;">
                                    <strong><?php echo htmlspecialchars($recurso['nombre']); ?></strong>
                                    <small>Stock total: <?php echo $recurso['cantidad_total']; ?></small>
                                </td>
                                <td><span class="tipo"><?php echo ucfirst($tipoDB); ?></span></td>
                                <td><?php echo htmlspecialchars($recurso['descripcion']); ?></td>
                                <td><span class="estado <?php echo $claseEstado; ?>"><?php echo $estadoTexto; ?></span></td>
                                <td class="acciones">
                                    <form action="procesar_recurso.php" method="POST" style="display:inline;">
                                        <input type="hidden" name="id" value="<?php echo $recurso['id']; ?>">
                                        <input type="hidden" name="accion" value="eliminar">
                                        <button type="submit" class="btn-eliminar" onclick="return confirm('¿Seguro que deseas eliminar este recurso?')">Eliminar</button>
                                    </form>
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
            
            <form action="procesar_recurso.php" method="POST" enctype="multipart/form-data">
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

                <label>Cantidad en inventario</label>
                <input type="number" name="cantidad_total" value="1" min="1" required>

                <label>Estado</label>
                <select name="activo" required>
                    <option value="1">Activo / Disponible</option>
                    <option value="0">En mantenimiento</option>
                </select>

                <div id="campoImagen" style="display: none; margin-top: 15px;">
                    <label>Subir foto del equipo (Opcional)</label>
                    <input type="file" name="imagen_equipo" accept="image/*">
                </div>

                <button type="submit" class="btn-guardar" style="margin-top: 20px;">Guardar recurso</button>
            </form>
        </div>
    </div>

    <script src="../scripts/recursos.js"></script>
</body>
</html>