<?php
session_start();

// 1. Confirmación de Admin: Si no hay sesión o el rol NO es admin, lo redirige.
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'admin') {
    header("Location: Inicio_sesion.php");
    exit();
}

// 2. Conectar a la base de datos
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/funciones.php';

// Opcional: Obtener todos los usuarios para la tabla
$query = "SELECT * FROM usuarios ORDER BY nombre ASC";
$resultado_usuarios = mysqli_query($conexion, $query);
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

    <header class="barra">

        <div class="logo">
            ReservaTech
        </div>

        <nav>
            <a href="panel_admin.html">Panel</a>
            <a href="reservas_admin.html">Reservas</a>
            <a href="usuarios.html" class="activo">Usuarios</a>
            <a href="Inicio_sesion.html">Cerrar sesión</a>
        </nav>

    </header>


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

            <div class="herramientas">

                <input
                    type="text"
                    id="buscar"
                    placeholder="Buscar usuario..."
                    onkeyup="buscarUsuario()"
                >

                <select id="filtroRol" onchange="filtrarUsuarios()">
                    <option value="todos">Todos los roles</option>
                    <option value="Alumno">Alumno</option>
                    <option value="Profesor">Profesor</option>
                    <option value="Administrador">Administrador</option>
                </select>

                <select id="filtroEstado" onchange="filtrarUsuarios()">
                    <option value="todos">Todos los estados</option>
                    <option value="Activo">Activo</option>
                    <option value="Inactivo">Inactivo</option>
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
                        <?php while ($usuario = mysqli_fetch_assoc($resultado_usuarios)): ?>
                        <?php 
            // Determinar clases CSS según el rol y estado[cite: 3]
            $claseRol = strtolower($usuario['rol']); // ej: "admin" -> "administrador" en tu CSS, puedes ajustarlo
            if($claseRol == 'admin') $claseRol = 'administrador';

            $estadoTexto = $usuario['activo'] == 1 ? 'Activo' : 'Inactivo';
            $claseEstado = $usuario['activo'] == 1 ? 'activo' : 'inactivo';
        ?>
        <tr>
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
                <button class="btn-editar" onclick="editarUsuario(this, <?php echo $usuario['id']; ?>)">Editar</button>
                <button class="btn-eliminar" onclick="eliminarUsuario(this, <?php echo $usuario['id']; ?>)">Eliminar</button>
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

            <span class="cerrar" onclick="cerrarFormulario()">
                ×
            </span>

            <h2 id="tituloFormulario">
                Agregar usuario
            </h2>

            <form action="procesar_usuario.php" method="POST">
                <!-- Añadimos un campo oculto para saber si creamos o editamos -->
                <input type="hidden" name="id_usuario" id="id_usuario" value="">
                <input type="hidden" name="accion" id="accion" value="guardar">

                <label>Nombre completo</label>
                <input
                    type="text"
                    id="nombre"
                    required
                >

                <label>Usuario</label>
                <input
                    type="text"
                    id="usuario"
                    required
                >

                <label>Correo electrónico</label>
                <input
                    type="email"
                    id="correo"
                    required
                >

                <label>Contraseña (solo si es nuevo o desea cambiarla)</label>
                <input type="password" id="password" name="password">

                <label>Rol</label>
                <select id="rol" required>
                    <option value="Alumno">Alumno</option>
                    <option value="Profesor">Profesor</option>
                    <option value="Administrador">Administrador</option>
                </select>

                <label>Estado</label>
                <select id="estado" required>
                    <option value="Activo">Activo</option>
                    <option value="Inactivo">Inactivo</option>
                </select>

                <button type="submit" class="btn-guardar">
                    Guardar usuario
                </button>

            </form>

        </div>

    </div>


    <script src="../scripts/usuarios.js"></script>

</body>

</html>