<?php
ini_set('display_errors', 1); 
error_reporting(E_ALL);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'admin') {
    header("Location: Inicio_sesion.php");
    exit();
}

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/funciones.php';

$query = "SELECT * FROM usuarios ORDER BY nombre ASC";
$resultado_usuarios = $pdo->query($query);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de usuarios</title>
    <link rel="stylesheet" href="../css/style_usuarios.css">
</head>
<body>

    <?php require_once __DIR__ . '/../includes/barra_admin.php'; ?>

    <main class="contenedor">
        <section class="encabezado">
            <div>
                <h1>Gestión de usuarios</h1>
                <p>Administrá los usuarios registrados en el sistema.</p>
            </div>
            <button class="btn-agregar" onclick="abrirFormulario()">
                + Agregar usuario
            </button>
        </section>

        <section class="estadisticas">
            <div class="tarjeta">
                <span class="numero" id="totalUsuarios">6</span>
                <span>Total de usuarios</span>
            </div>
            <div class="tarjeta">
                <span class="numero" id="alumnos">3</span>
                <span>Alumnos</span>
            </div>
            <div class="tarjeta">
                <span class="numero" id="profesores">2</span>
                <span>Profesores</span>
            </div>
            <div class="tarjeta">
                <span class="numero" id="administradores">1</span>
                <span>Administradores</span>
            </div>
        </section>

        <section class="panel">
            <div class="pestanas">
                <button class="btn-pestana activa" onclick="cambiarPestana(1, this)">Usuarios Activos</button>
                <button class="btn-pestana" onclick="cambiarPestana(0, this)">Solicitudes Pendientes</button>
            </div>

            <div class="herramientas">
                <input type="text" id="buscar" placeholder="Buscar usuario..." onkeyup="aplicarFiltros()">
                
                <select id="filtroRol" onchange="aplicarFiltros()">
                    <option value="todos">Todos los roles</option>
                    <option value="Alumno">Alumno</option>
                    <option value="Profesor">Profesor</option>
                    <option value="Admin">Administrador</option>
                </select>
            </div>

            <div class="tabla-contenedor">
                <table>
                    <thead>
                        <tr>
                            <th>Usuario</th>
                            <th>Correo</th>
                            <th>Rol</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tablaUsuarios">
                        <?php while ($usuario = $resultado_usuarios->fetch(PDO::FETCH_ASSOC)): ?>
                            <?php 
                                $claseRol = strtolower($usuario['rol']);
                                if($claseRol == 'admin') $claseRol = 'administrador';

                                $estadoTexto = $usuario['activo'] == 1 ? 'Activo' : 'Inactivo';
                                $claseEstado = $usuario['activo'] == 1 ? 'activo' : 'inactivo';
                            ?>
                            <tr data-activo="<?php echo $usuario['activo']; ?>">
                                <td>
                                    <strong><?php echo htmlspecialchars($usuario['nombre']); ?></strong>
                                    <small><?php echo htmlspecialchars($usuario['email']); ?></small>
                                </td>
                                <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                                <td>
                                    <span class="rol <?php echo $claseRol; ?>">
                                        <?php echo ucfirst($usuario['rol']); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="estado <?php echo $claseEstado; ?>">
                                        <?php echo $estadoTexto; ?>
                                    </span>
                                </td>
                                <td class="acciones">
                                    <?php if ($usuario['activo'] == 0): ?>
                                        <button class="btn-editar" style="background: #4f8c4f; color: white;" onclick="aprobarUsuario(this, <?php echo $usuario['id']; ?>)">Aprobar</button>
                                    <?php else: ?>
                                        <button class="btn-editar" onclick="editarUsuario(this, <?php echo $usuario['id']; ?>)">Editar</button>
                                    <?php endif; ?>

                                    <?php if (strtolower((string)$usuario['rol']) === 'admin'): ?>
                                        <button class="btn-eliminar" disabled style="opacity: 0.5; cursor: not-allowed;">Eliminar</button>
                                    <?php else: ?>
                                        <button class="btn-eliminar" onclick="eliminarUsuario(this, <?php echo $usuario['id']; ?>)">Eliminar</button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <div id="modalUsuario" class="modal">
        <div class="modal-contenido">
            <span class="cerrar" onclick="cerrarFormulario()">×</span>
            <h2 id="tituloFormulario">Agregar usuario</h2>

            <form action="procesar_usuario.php" method="POST">
                <input type="hidden" name="id_usuario" id="id_usuario" value="">
                <input type="hidden" name="accion" id="accion" value="guardar">
                
                <div id="grupo-estado">
                    <label>Estado</label>
                    <select id="estado" name="estado" required>
                        <option value="Activo">Activo</option>
                        <option value="Inactivo">Inactivo</option>
                    </select>
                </div>

                <label>Nombre completo</label>
                <input type="text" id="nombre" name="nombre" required>

                <label>Correo electrónico</label>
                <input type="email" id="correo" name="email" required>

                <label>Contraseña (solo si es nuevo o desea cambiarla)</label>
                <input type="password" id="password" name="password">

                <label>Rol</label>
                <select id="rol" name="rol" required>
                    <option value="alumno">Alumno</option>
                    <option value="profesor">Profesor</option>
                    <option value="admin">Administrador</option>
                </select>

                <button type="submit" class="btn-guardar">Guardar usuario</button>
            </form>
        </div>
    </div>

    <script src="../scripts/usuarios.js"></script>
</body>
</html>