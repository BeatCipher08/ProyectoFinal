function buscarReservas() {

    const texto =
        document.getElementById("buscarReserva")
        .value
        .toLowerCase();

    const filas =
        document.querySelectorAll("#tablaReservas tr");

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


function filtrarReservas() {

    const estado =
        document.getElementById("filtroEstado").value;

    const recurso =
        document.getElementById("filtroRecurso").value;

    const filas =
        document.querySelectorAll("#tablaReservas tr");

    filas.forEach(function(fila) {

        const estadoFila =
            fila.cells[4].textContent.trim();

        const recursoFila =
            fila.dataset.recurso;

        const coincideEstado =
            estado === "todos" ||
            estadoFila === estado;

        const coincideRecurso =
            recurso === "todos" ||
            recursoFila === recurso;

        if (coincideEstado && coincideRecurso) {
            fila.style.display = "";
        } else {
            fila.style.display = "none";
        }

    });
}


function verDetalles(boton) {

    const fila =
        boton.closest("tr");

    const usuario =
        fila.cells[0].querySelector("strong").textContent;

    const nombreUsuario =
        fila.cells[0].querySelector("small").textContent;

    const recurso =
        fila.cells[1].querySelector("strong").textContent;

    const tipoRecurso =
        fila.cells[1].querySelector("small").textContent;

    const fecha =
        fila.cells[2].textContent.trim();

    const horario =
        fila.cells[3].textContent.trim();

    const estado =
        fila.cells[4].textContent.trim();


    document.getElementById("informacionReserva").innerHTML =

        '<div class="detalle">' +
            '<strong>Usuario</strong>' +
            '<span>' + usuario + '</span>' +
        '</div>' +

        '<div class="detalle">' +
            '<strong>Nombre de usuario</strong>' +
            '<span>' + nombreUsuario + '</span>' +
        '</div>' +

        '<div class="detalle">' +
            '<strong>Recurso</strong>' +
            '<span>' + recurso + '</span>' +
        '</div>' +

        '<div class="detalle">' +
            '<strong>Tipo</strong>' +
            '<span>' + tipoRecurso + '</span>' +
        '</div>' +

        '<div class="detalle">' +
            '<strong>Fecha</strong>' +
            '<span>' + fecha + '</span>' +
        '</div>' +

        '<div class="detalle">' +
            '<strong>Horario</strong>' +
            '<span>' + horario + '</span>' +
        '</div>' +

        '<div class="detalle">' +
            '<strong>Estado</strong>' +
            '<span>' + estado + '</span>' +
        '</div>';


    document.getElementById("modalDetalles").style.display =
        "flex";
}


function cerrarDetalles() {

    document.getElementById("modalDetalles").style.display =
        "none";
}


function cancelarReserva(boton) {

    const fila =
        boton.closest("tr");

    const usuario =
        fila.cells[0].querySelector("strong").textContent;

    const recurso =
        fila.cells[1].querySelector("strong").textContent;


    const confirmar = confirm(
        "¿Seguro que querés cancelar la reserva de " +
        usuario +
        " para " +
        recurso +
        "?"
    );


    if (confirmar) {

        fila.cells[4].innerHTML =
            '<span class="estado cancelada">' +
            'Cancelada' +
            '</span>';

        alert(
            "La reserva fue cancelada correctamente."
        );

        actualizarEstadisticas();
    }

}


function actualizarEstadisticas() {

    const filas =
        document.querySelectorAll("#tablaReservas tr");

    let confirmadas = 0;
    let pendientes = 0;
    let canceladas = 0;


    filas.forEach(function(fila) {

        const estado =
            fila.cells[4].textContent.trim();

        if (estado === "Confirmada") {
            confirmadas++;
        }

        if (estado === "Pendiente") {
            pendientes++;
        }

        if (estado === "Cancelada") {
            canceladas++;
        }

    });


    document.getElementById("totalReservas").textContent =
        filas.length;

    document.getElementById("reservasConfirmadas").textContent =
        confirmadas;

    document.getElementById("reservasPendientes").textContent =
        pendientes;

    document.getElementById("reservasCanceladas").textContent =
        canceladas;

}


window.onclick = function(evento) {

    const modal =
        document.getElementById("modalDetalles");

    if (evento.target === modal) {
        cerrarDetalles();
    }

};


actualizarEstadisticas();