// Función para abrir el modal para agregar un NUEVO usuario
function abrirFormulario() {
    document.getElementById("modalUsuario").style.display = "flex";
    document.getElementById("tituloFormulario").textContent = "Agregar usuario";

    // Vaciamos los campos
    document.getElementById("id_usuario").value = "";
    document.getElementById("accion").value = "guardar"; // Acción para PHP
    document.getElementById("nombre").value = "";
    document.getElementById("correo").value = "";
    
    // La contraseña es obligatoria al crear un usuario nuevo
    document.getElementById("password").value = "";
    document.getElementById("password").required = true; 
    
    document.getElementById("rol").value = "alumno";
    document.getElementById("estado").value = "Activo";
}

function cerrarFormulario() {
    document.getElementById("modalUsuario").style.display = "none";
}

// LA FUNCIÓN guardarUsuario() FUE ELIMINADA PORQUE AHORA PHP HACE EL GUARDADO (POST)

// Modificado para recibir el ID de la base de datos
function editarUsuario(boton, id) {
    const fila = boton.closest("tr");

    // Extraer datos de la fila de la tabla
    const nombre = fila.cells[0].querySelector("strong").textContent;
    const correo = fila.cells[1].textContent;
    // Pasamos el rol a minúscula para que coincida con el 'value' del select (alumno, profesor, admin)
    const rol = fila.cells[2].textContent.trim().toLowerCase(); 
    const estado = fila.cells[3].textContent.trim();

    document.getElementById("tituloFormulario").textContent = "Editar usuario";
    
    // Llenamos los campos y los inputs ocultos
    document.getElementById("id_usuario").value = id;
    document.getElementById("accion").value = "editar";
    document.getElementById("nombre").value = nombre;
    document.getElementById("correo").value = correo;
    
    // Al editar, la contraseña NO es obligatoria (se deja en blanco si no se quiere cambiar)
    document.getElementById("password").value = "";
    document.getElementById("password").required = false; 

    document.getElementById("rol").value = rol;
    document.getElementById("estado").value = estado;

    document.getElementById("modalUsuario").style.display = "flex";
}

// Modificado para redirigir a PHP con el ID a eliminar
function eliminarUsuario(boton, id) {
    const fila = boton.closest("tr");
    const nombre = fila.cells[0].querySelector("strong").textContent;

    const confirmar = confirm("¿Seguro que querés eliminar al usuario " + nombre + "?");

    if (confirmar) {
        // Enviar la orden de eliminar a PHP mediante la URL (método GET)
        window.location.href = "procesar_usuario.php?accion=eliminar&id_usuario=" + id;
    }
}

// Búsqueda en tiempo real (Queda igual)
function buscarUsuario() {
    const texto = document.getElementById("buscar").value.toLowerCase();
    const filas = document.querySelectorAll("#tablaUsuarios tr");

    filas.forEach(function(fila) {
        const contenido = fila.textContent.toLowerCase();
        if (contenido.includes(texto)) {
            fila.style.display = "";
        } else {
            fila.style.display = "none";
        }
    });
}

// Filtros (Se ajustaron un poco para coincidir con mayúsculas/minúsculas)
function filtrarUsuarios() {
    const rolFiltro = document.getElementById("filtroRol").value.toLowerCase();
    const estadoFiltro = document.getElementById("filtroEstado").value;
    const filas = document.querySelectorAll("#tablaUsuarios tr");

    filas.forEach(function(fila) {
        const rolFila = fila.cells[2].textContent.trim().toLowerCase();
        const estadoFila = fila.cells[3].textContent.trim();

        const coincideRol = rolFiltro === "todos" || rolFila === rolFiltro;
        const coincideEstado = estadoFiltro === "todos" || estadoFila === estadoFiltro;

        if (coincideRol && coincideEstado) {
            fila.style.display = "";
        } else {
            fila.style.display = "none";
        }
    });
}

// Estadísticas dinámicas (Se ajustaron los textos a minúsculas para el conteo)
function actualizarEstadisticas() {
    const filas = document.querySelectorAll("#tablaUsuarios tr");

    let alumnos = 0;
    let profesores = 0;
    let administradores = 0;

    filas.forEach(function(fila) {
        const rol = fila.cells[2].textContent.trim().toLowerCase();

        if (rol === "alumno") alumnos++;
        if (rol === "profesor") profesores++;
        if (rol === "administrador" || rol === "admin") administradores++;
    });

    document.getElementById("totalUsuarios").textContent = filas.length;
    document.getElementById("alumnos").textContent = alumnos;
    document.getElementById("profesores").textContent = profesores;
    document.getElementById("administradores").textContent = administradores;
}

// Asegurarse de que las estadísticas se calculen apenas carga la página
document.addEventListener("DOMContentLoaded", function() {
    actualizarEstadisticas();
});

// Cerrar modal al hacer clic afuera
window.onclick = function(evento) {
    const modal = document.getElementById("modalUsuario");
    if (evento.target === modal) {
        cerrarFormulario();
    }
};