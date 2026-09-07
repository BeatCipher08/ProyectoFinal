let filaEditando = null;

function abrirFormulario() {
    document.getElementById("modalUsuario").style.display = "flex";
    document.getElementById("tituloFormulario").textContent = "Agregar usuario";

    document.getElementById("nombre").value = "";
    document.getElementById("usuario").value = "";
    document.getElementById("correo").value = "";
    document.getElementById("rol").value = "Alumno";
    document.getElementById("estado").value = "Activo";

    filaEditando = null;
}

function cerrarFormulario() {
    document.getElementById("modalUsuario").style.display = "none";
}

function guardarUsuario(evento) {
    evento.preventDefault();

    const nombre = document.getElementById("nombre").value;
    const usuario = document.getElementById("usuario").value;
    const correo = document.getElementById("correo").value;
    const rol = document.getElementById("rol").value;
    const estado = document.getElementById("estado").value;

    const claseRol = rol.toLowerCase();
    const claseEstado = estado.toLowerCase();

    if (filaEditando) {

        filaEditando.cells[0].innerHTML =
            "<strong>" + nombre + "</strong>" +
            "<small>" + usuario + "</small>";

        filaEditando.cells[1].textContent = correo;

        filaEditando.cells[2].innerHTML =
            '<span class="rol ' + claseRol + '">' +
            rol +
            "</span>";

        filaEditando.cells[3].innerHTML =
            '<span class="estado ' + claseEstado + '">' +
            estado +
            "</span>";

    } else {

        const tabla = document.getElementById("tablaUsuarios");

        const fila = document.createElement("tr");

        fila.innerHTML =
            "<td>" +
                "<strong>" + nombre + "</strong>" +
                "<small>" + usuario + "</small>" +
            "</td>" +

            "<td>" + correo + "</td>" +

            "<td>" +
                '<span class="rol ' + claseRol + '">' +
                rol +
                "</span>" +
            "</td>" +

            "<td>" +
                '<span class="estado ' + claseEstado + '">' +
                estado +
                "</span>" +
            "</td>" +

            "<td class='acciones'>" +

                '<button class="btn-editar" onclick="editarUsuario(this)">' +
                "Editar" +
                "</button>" +

                '<button class="btn-eliminar" onclick="eliminarUsuario(this)">' +
                "Eliminar" +
                "</button>" +

            "</td>";

        tabla.appendChild(fila);
    }

    actualizarEstadisticas();
    cerrarFormulario();
}

function editarUsuario(boton) {

    filaEditando = boton.closest("tr");

    const nombre = filaEditando.cells[0].querySelector("strong").textContent;
    const usuario = filaEditando.cells[0].querySelector("small").textContent;
    const correo = filaEditando.cells[1].textContent;
    const rol = filaEditando.cells[2].textContent.trim();
    const estado = filaEditando.cells[3].textContent.trim();

    document.getElementById("tituloFormulario").textContent =
        "Editar usuario";

    document.getElementById("nombre").value = nombre;
    document.getElementById("usuario").value = usuario;
    document.getElementById("correo").value = correo;
    document.getElementById("rol").value = rol;
    document.getElementById("estado").value = estado;

    document.getElementById("modalUsuario").style.display = "flex";
}

function eliminarUsuario(boton) {

    const fila = boton.closest("tr");

    const nombre =
        fila.cells[0].querySelector("strong").textContent;

    const confirmar = confirm(
        "¿Seguro que querés eliminar al usuario " +
        nombre +
        "?"
    );

    if (confirmar) {
        fila.remove();
        actualizarEstadisticas();
    }
}

function buscarUsuario() {

    const texto =
        document.getElementById("buscar").value.toLowerCase();

    const filas =
        document.querySelectorAll("#tablaUsuarios tr");

    filas.forEach(function(fila) {

        const contenido =
            fila.textContent.toLowerCase();

        if (contenido.includes(texto)) {
            fila.style.display = "";
        } else {
            fila.style.display = "none";
        }

    });
}

function filtrarUsuarios() {

    const rol =
        document.getElementById("filtroRol").value;

    const estado =
        document.getElementById("filtroEstado").value;

    const filas =
        document.querySelectorAll("#tablaUsuarios tr");

    filas.forEach(function(fila) {

        const rolFila =
            fila.cells[2].textContent.trim();

        const estadoFila =
            fila.cells[3].textContent.trim();

        const coincideRol =
            rol === "todos" || rolFila === rol;

        const coincideEstado =
            estado === "todos" || estadoFila === estado;

        if (coincideRol && coincideEstado) {
            fila.style.display = "";
        } else {
            fila.style.display = "none";
        }

    });
}

function actualizarEstadisticas() {

    const filas =
        document.querySelectorAll("#tablaUsuarios tr");

    let alumnos = 0;
    let profesores = 0;
    let administradores = 0;

    filas.forEach(function(fila) {

        const rol =
            fila.cells[2].textContent.trim();

        if (rol === "Alumno") {
            alumnos++;
        }

        if (rol === "Profesor") {
            profesores++;
        }

        if (rol === "Administrador") {
            administradores++;
        }

    });

    document.getElementById("totalUsuarios").textContent =
        filas.length;

    document.getElementById("alumnos").textContent =
        alumnos;

    document.getElementById("profesores").textContent =
        profesores;

    document.getElementById("administradores").textContent =
        administradores;
}

window.onclick = function(evento) {

    const modal =
        document.getElementById("modalUsuario");

    if (evento.target === modal) {
        cerrarFormulario();
    }

};