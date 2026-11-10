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
    
    // Ocultamos el selector de estado al crear y lo forzamos a Activo
    document.getElementById("grupo-estado").style.display = "none";
    document.getElementById("estado").value = "Activo";
}

// Función para cerrar el modal
function cerrarFormulario() {
    document.getElementById("modalUsuario").style.display = "none";
}

// Función exclusiva para el botón "Aprobar"
function aprobarUsuario(boton, id) {
    // Primero cargamos los datos como si fuéramos a editar
    editarUsuario(boton, id);
    
    // Luego modificamos la interfaz para que parezca una aprobación
    document.getElementById("tituloFormulario").textContent = "Aprobar usuario";
    document.getElementById("grupo-estado").style.display = "none"; // Ocultamos el estado
    document.getElementById("estado").value = "Activo"; // Lo forzamos a Activo
}

// Modificado para recibir el ID de la base de datos
function editarUsuario(boton, id) {
    const fila = boton.closest("tr");

    // Extraer datos de la fila de la tabla
    const nombre = fila.cells[0].querySelector("strong").textContent;
    const correo = fila.cells[1].textContent;
    // Pasamos el rol a minúscula para que coincida con el 'value' del select
    const rol = fila.cells[2].textContent.trim().toLowerCase(); 
    const estado = fila.cells[3].textContent.trim();

    document.getElementById("tituloFormulario").textContent = "Editar usuario";
    
    // Mostramos el selector de estado al editar
    document.getElementById("grupo-estado").style.display = "flex";
    document.getElementById("grupo-estado").style.flexDirection = "column";
    
    // Llenamos los campos y los inputs ocultos
    document.getElementById("id_usuario").value = id;
    document.getElementById("accion").value = "editar";
    document.getElementById("nombre").value = nombre;
    document.getElementById("correo").value = correo;
    
    // Al editar, la contraseña NO es obligatoria
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
    const rol = fila.cells[2].textContent.trim().toLowerCase();

    if (rol === "admin" || rol === "administrador") {
        alert("No se puede eliminar a un usuario administrador.");
        return;
    }

    const confirmar = confirm("¿Seguro que querés eliminar al usuario " + nombre + "?");

    if (confirmar) {
        window.location.href = "procesar_usuario.php?accion=eliminar&id_usuario=" + id;
    }
}

// --- NUEVA LÓGICA DE PESTAÑAS Y FILTROS ---
let pestanaActual = 1; // 1 = Activos, 0 = Pendientes

function cambiarPestana(estado, botonElement) {
    pestanaActual = estado;
    
    // Cambiar el estilo visual de los botones
    document.querySelectorAll('.btn-pestana').forEach(btn => btn.classList.remove('activa'));
    botonElement.classList.add('activa');
    
    // Volver a filtrar la tabla
    aplicarFiltros();
}

function aplicarFiltros() {
    const textoBusqueda = document.getElementById("buscar").value.toLowerCase();
    const rolFiltro = document.getElementById("filtroRol").value.toLowerCase();
    const filas = document.querySelectorAll("#tablaUsuarios tr");

    let alumnos = 0, profesores = 0, administradores = 0, totalVisibles = 0;

    filas.forEach(function(fila) {
        const contenidoFila = fila.textContent.toLowerCase();
        const rolFila = fila.cells[2].textContent.trim().toLowerCase();
        const estadoFila = parseInt(fila.getAttribute("data-activo")); // Lee el 1 o 0

        // Condiciones para mostrar la fila
        const coincidePestana = (estadoFila === pestanaActual);
        const coincideTexto = contenidoFila.includes(textoBusqueda);
        const coincideRol = (rolFiltro === "todos" || rolFila === rolFiltro);

        if (coincidePestana && coincideTexto && coincideRol) {
            fila.style.display = ""; // Mostrar
            
            // Sumar a las estadísticas solo a los que estamos viendo
            totalVisibles++;
            if (rolFila === "alumno") alumnos++;
            if (rolFila === "profesor") profesores++;
            if (rolFila === "administrador" || rolFila === "admin") administradores++;
        } else {
            fila.style.display = "none"; // Ocultar
        }
    });

    // Actualizar las tarjetas de números con lo que se está viendo en pantalla
    document.getElementById("totalUsuarios").textContent = totalVisibles;
    document.getElementById("alumnos").textContent = alumnos;
    document.getElementById("profesores").textContent = profesores;
    document.getElementById("administradores").textContent = administradores;
}

// Asegurarse de que se aplique la pestaña "Activos" apenas carga la página
document.addEventListener("DOMContentLoaded", function() {
    aplicarFiltros();
});

// Cerrar modal al hacer clic afuera
window.onclick = function(evento) {
    const modal = document.getElementById("modalUsuario");
    if (evento.target === modal) {
        cerrarFormulario();
    }
};