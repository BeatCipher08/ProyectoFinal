let recursoEditando = null;


function abrirFormulario() {

    document.getElementById("modalRecurso").style.display = "flex";

    document.getElementById("tituloFormulario").textContent =
        "Agregar recurso";

    document.getElementById("nombre").value = "";
    document.getElementById("codigo").value = "";
    document.getElementById("tipo").value = "Espacio";
    document.getElementById("capacidad").value = "";
    document.getElementById("estado").value = "Disponible";

    recursoEditando = null;
}


function cerrarFormulario() {

    document.getElementById("modalRecurso").style.display =
        "none";

}


function guardarRecurso(evento) {

    evento.preventDefault();


    const nombre =
        document.getElementById("nombre").value;

    const codigo =
        document.getElementById("codigo").value;

    const tipo =
        document.getElementById("tipo").value;

    const capacidad =
        document.getElementById("capacidad").value;

    const estado =
        document.getElementById("estado").value;


    const claseTipo =
        tipo.toLowerCase();

    const claseEstado =
        estado.toLowerCase();


    if (recursoEditando) {

        recursoEditando.cells[0].innerHTML =
            "<strong>" +
            nombre +
            "</strong>" +
            "<small>" +
            codigo +
            "</small>";


        recursoEditando.cells[1].innerHTML =
            '<span class="tipo ' +
            claseTipo +
            '">' +
            tipo +
            "</span>";


        recursoEditando.cells[2].textContent =
            capacidad;


        recursoEditando.cells[3].innerHTML =
            '<span class="estado ' +
            claseEstado +
            '">' +
            estado +
            "</span>";


        recursoEditando.dataset.tipo =
            tipo;

        recursoEditando.dataset.estado =
            estado;


    } else {


        const tabla =
            document.getElementById("tablaRecursos");


        const fila =
            document.createElement("tr");


        fila.dataset.tipo = tipo;
        fila.dataset.estado = estado;


        fila.innerHTML =

            "<td>" +

                "<strong>" +
                nombre +
                "</strong>" +

                "<small>" +
                codigo +
                "</small>" +

            "</td>" +


            "<td>" +

                '<span class="tipo ' +
                claseTipo +
                '">' +

                tipo +

                "</span>" +

            "</td>" +


            "<td>" +

                capacidad +

            "</td>" +


            "<td>" +

                '<span class="estado ' +
                claseEstado +
                '">' +

                estado +

                "</span>" +

            "</td>" +


            "<td class='acciones'>" +

                '<button class="btn-editar" onclick="editarRecurso(this)">' +

                    "Editar" +

                "</button>" +


                '<button class="btn-eliminar" onclick="eliminarRecurso(this)">' +

                    "Eliminar" +

                "</button>" +

            "</td>";


        tabla.appendChild(fila);

    }


    actualizarEstadisticas();

    cerrarFormulario();

}


function editarRecurso(boton) {

    recursoEditando =
        boton.closest("tr");


    const nombre =
        recursoEditando.cells[0]
        .querySelector("strong")
        .textContent;


    const codigo =
        recursoEditando.cells[0]
        .querySelector("small")
        .textContent;


    const tipo =
        recursoEditando.cells[1]
        .textContent
        .trim();


    const capacidad =
        recursoEditando.cells[2]
        .textContent
        .trim();


    const estado =
        recursoEditando.cells[3]
        .textContent
        .trim();


    document.getElementById("tituloFormulario")
        .textContent =
        "Editar recurso";


    document.getElementById("nombre").value =
        nombre;


    document.getElementById("codigo").value =
        codigo;


    document.getElementById("tipo").value =
        tipo;


    document.getElementById("capacidad").value =
        capacidad;


    document.getElementById("estado").value =
        estado;


    document.getElementById("modalRecurso")
        .style.display =
        "flex";

}


function eliminarRecurso(boton) {

    const fila =
        boton.closest("tr");


    const nombre =
        fila.cells[0]
        .querySelector("strong")
        .textContent;


    const confirmar =
        confirm(
            "¿Seguro que querés eliminar " +
            nombre +
            "?"
        );


    if (confirmar) {

        fila.remove();

        actualizarEstadisticas();

    }

}


function buscarRecursos() {

    const texto =
        document.getElementById("buscar")
        .value
        .toLowerCase();


    const filas =
        document.querySelectorAll(
            "#tablaRecursos tr"
        );


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


function filtrarRecursos() {

    const tipo =
        document.getElementById("filtroTipo")
        .value;


    const estado =
        document.getElementById("filtroEstado")
        .value;


    const filas =
        document.querySelectorAll(
            "#tablaRecursos tr"
        );


    filas.forEach(function(fila) {

        const tipoFila =
            fila.dataset.tipo;


        const estadoFila =
            fila.dataset.estado;


        const coincideTipo =
            tipo === "todos" ||
            tipoFila === tipo;


        const coincideEstado =
            estado === "todos" ||
            estadoFila === estado;


        if (
            coincideTipo &&
            coincideEstado
        ) {

            fila.style.display = "";

        } else {

            fila.style.display = "none";

        }

    });

}


function actualizarEstadisticas() {

    const filas =
        document.querySelectorAll(
            "#tablaRecursos tr"
        );


    let disponibles = 0;
    let ocupados = 0;
    let mantenimiento = 0;


    filas.forEach(function(fila) {

        const estado =
            fila.dataset.estado;


        if (estado === "Disponible") {
            disponibles++;
        }


        if (estado === "Ocupado") {
            ocupados++;
        }


        if (estado === "Mantenimiento") {
            mantenimiento++;
        }

    });


    document.getElementById(
        "totalRecursos"
    ).textContent =
        filas.length;


    document.getElementById(
        "recursosDisponibles"
    ).textContent =
        disponibles;


    document.getElementById(
        "recursosOcupados"
    ).textContent =
        ocupados;


    document.getElementById(
        "recursosMantenimiento"
    ).textContent =
        mantenimiento;

}


window.onclick = function(evento) {

    const modal =
        document.getElementById(
            "modalRecurso"
        );


    if (evento.target === modal) {

        cerrarFormulario();

    }

};


actualizarEstadisticas();let recursoEditando = null;


function abrirFormulario() {

    document.getElementById("modalRecurso").style.display = "flex";

    document.getElementById("tituloFormulario").textContent =
        "Agregar recurso";

    document.getElementById("nombre").value = "";
    document.getElementById("codigo").value = "";
    document.getElementById("tipo").value = "Espacio";
    document.getElementById("capacidad").value = "";
    document.getElementById("estado").value = "Disponible";

    recursoEditando = null;
}


function cerrarFormulario() {

    document.getElementById("modalRecurso").style.display =
        "none";

}


function guardarRecurso(evento) {

    evento.preventDefault();


    const nombre =
        document.getElementById("nombre").value;

    const codigo =
        document.getElementById("codigo").value;

    const tipo =
        document.getElementById("tipo").value;

    const capacidad =
        document.getElementById("capacidad").value;

    const estado =
        document.getElementById("estado").value;


    const claseTipo =
        tipo.toLowerCase();

    const claseEstado =
        estado.toLowerCase();


    if (recursoEditando) {

        recursoEditando.cells[0].innerHTML =
            "<strong>" +
            nombre +
            "</strong>" +
            "<small>" +
            codigo +
            "</small>";


        recursoEditando.cells[1].innerHTML =
            '<span class="tipo ' +
            claseTipo +
            '">' +
            tipo +
            "</span>";


        recursoEditando.cells[2].textContent =
            capacidad;


        recursoEditando.cells[3].innerHTML =
            '<span class="estado ' +
            claseEstado +
            '">' +
            estado +
            "</span>";


        recursoEditando.dataset.tipo =
            tipo;

        recursoEditando.dataset.estado =
            estado;


    } else {


        const tabla =
            document.getElementById("tablaRecursos");


        const fila =
            document.createElement("tr");


        fila.dataset.tipo = tipo;
        fila.dataset.estado = estado;


        fila.innerHTML =

            "<td>" +

                "<strong>" +
                nombre +
                "</strong>" +

                "<small>" +
                codigo +
                "</small>" +

            "</td>" +


            "<td>" +

                '<span class="tipo ' +
                claseTipo +
                '">' +

                tipo +

                "</span>" +

            "</td>" +


            "<td>" +

                capacidad +

            "</td>" +


            "<td>" +

                '<span class="estado ' +
                claseEstado +
                '">' +

                estado +

                "</span>" +

            "</td>" +


            "<td class='acciones'>" +

                '<button class="btn-editar" onclick="editarRecurso(this)">' +

                    "Editar" +

                "</button>" +


                '<button class="btn-eliminar" onclick="eliminarRecurso(this)">' +

                    "Eliminar" +

                "</button>" +

            "</td>";


        tabla.appendChild(fila);

    }


    actualizarEstadisticas();

    cerrarFormulario();

}


function editarRecurso(boton) {

    recursoEditando =
        boton.closest("tr");


    const nombre =
        recursoEditando.cells[0]
        .querySelector("strong")
        .textContent;


    const codigo =
        recursoEditando.cells[0]
        .querySelector("small")
        .textContent;


    const tipo =
        recursoEditando.cells[1]
        .textContent
        .trim();


    const capacidad =
        recursoEditando.cells[2]
        .textContent
        .trim();


    const estado =
        recursoEditando.cells[3]
        .textContent
        .trim();


    document.getElementById("tituloFormulario")
        .textContent =
        "Editar recurso";


    document.getElementById("nombre").value =
        nombre;


    document.getElementById("codigo").value =
        codigo;


    document.getElementById("tipo").value =
        tipo;


    document.getElementById("capacidad").value =
        capacidad;


    document.getElementById("estado").value =
        estado;


    document.getElementById("modalRecurso")
        .style.display =
        "flex";

}


function eliminarRecurso(boton) {

    const fila =
        boton.closest("tr");


    const nombre =
        fila.cells[0]
        .querySelector("strong")
        .textContent;


    const confirmar =
        confirm(
            "¿Seguro que querés eliminar " +
            nombre +
            "?"
        );


    if (confirmar) {

        fila.remove();

        actualizarEstadisticas();

    }

}


function buscarRecursos() {

    const texto =
        document.getElementById("buscar")
        .value
        .toLowerCase();


    const filas =
        document.querySelectorAll(
            "#tablaRecursos tr"
        );


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


function filtrarRecursos() {

    const tipo =
        document.getElementById("filtroTipo")
        .value;


    const estado =
        document.getElementById("filtroEstado")
        .value;


    const filas =
        document.querySelectorAll(
            "#tablaRecursos tr"
        );


    filas.forEach(function(fila) {

        const tipoFila =
            fila.dataset.tipo;


        const estadoFila =
            fila.dataset.estado;


        const coincideTipo =
            tipo === "todos" ||
            tipoFila === tipo;


        const coincideEstado =
            estado === "todos" ||
            estadoFila === estado;


        if (
            coincideTipo &&
            coincideEstado
        ) {

            fila.style.display = "";

        } else {

            fila.style.display = "none";

        }

    });

}


function actualizarEstadisticas() {

    const filas =
        document.querySelectorAll(
            "#tablaRecursos tr"
        );


    let disponibles = 0;
    let ocupados = 0;
    let mantenimiento = 0;


    filas.forEach(function(fila) {

        const estado =
            fila.dataset.estado;


        if (estado === "Disponible") {
            disponibles++;
        }


        if (estado === "Ocupado") {
            ocupados++;
        }


        if (estado === "Mantenimiento") {
            mantenimiento++;
        }

    });


    document.getElementById(
        "totalRecursos"
    ).textContent =
        filas.length;


    document.getElementById(
        "recursosDisponibles"
    ).textContent =
        disponibles;


    document.getElementById(
        "recursosOcupados"
    ).textContent =
        ocupados;


    document.getElementById(
        "recursosMantenimiento"
    ).textContent =
        mantenimiento;

}


window.onclick = function(evento) {

    const modal =
        document.getElementById(
            "modalRecurso"
        );


    if (evento.target === modal) {

        cerrarFormulario();

    }

};


actualizarEstadisticas();