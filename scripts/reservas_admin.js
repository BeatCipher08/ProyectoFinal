function mostrarReservas(tipo) {

    const detalle = document.getElementById("detalleReservas");

    const contenido = document.getElementById("contenidoDetalle");

    const titulo = document.getElementById("tituloDetalle");

    const descripcion = document.getElementById("descripcionDetalle");


    let datos = [];


    if (tipo === "todas") {

        titulo.textContent = "Todas las reservas";

        descripcion.textContent =
            "Listado completo de las reservas del sistema.";

        datos = [

            ["Juan Pérez", "Aula 204", "10/08/2026", "10:00 - 12:00", "Confirmada"],

            ["María Rodríguez", "Proyector 03", "10/08/2026", "14:00 - 16:00", "Pendiente"],

            ["Lucas Silva", "Laboratorio 1", "11/08/2026", "08:00 - 10:00", "Confirmada"],

            ["Agustina López", "Aula 105", "11/08/2026", "13:00 - 15:00", "Cancelada"],

            ["Pedro Martínez", "Aula 305", "12/08/2026", "09:00 - 11:00", "Pendiente"]

        ];

    }


    if (tipo === "confirmadas") {

        titulo.textContent = "Reservas confirmadas";

        descripcion.textContent =
            "Personas que tienen reservas confirmadas.";

        datos = [

            ["Juan Pérez", "Aula 204", "10/08/2026", "10:00 - 12:00", "Confirmada"],

            ["Lucas Silva", "Laboratorio 1", "11/08/2026", "08:00 - 10:00", "Confirmada"],

            ["Sofía Fernández", "Aula 203", "12/08/2026", "11:00 - 13:00", "Confirmada"],

            ["Carlos Gómez", "Aula 301", "13/08/2026", "14:00 - 16:00", "Confirmada"]

        ];

    }


    if (tipo === "pendientes") {

        titulo.textContent = "Reservas pendientes";

        descripcion.textContent =
            "Personas que tienen reservas esperando confirmación.";

        datos = [

            ["María Rodríguez", "Proyector 03", "10/08/2026", "14:00 - 16:00", "Pendiente"],

            ["Pedro Martínez", "Aula 305", "12/08/2026", "09:00 - 11:00", "Pendiente"],

            ["Carlos Gómez", "Laboratorio 2", "13/08/2026", "08:00 - 10:00", "Pendiente"]

        ];

    }


    if (tipo === "canceladas") {

        titulo.textContent = "Reservas canceladas";

        descripcion.textContent =
            "Personas que cancelaron una reserva y el momento en que lo hicieron.";

        datos = [

            ["Agustina López", "Aula 105", "11/08/2026", "13:00 - 15:00", "Cancelada", "10/08/2026 - 09:17"],

            ["Martín Silva", "Laboratorio 1", "12/08/2026", "08:00 - 10:00", "Cancelada", "10/08/2026 - 11:36"],

            ["Laura Pérez", "Aula 202", "13/08/2026", "14:00 - 16:00", "Cancelada", "10/08/2026 - 15:42"],

            ["Federico Díaz", "Aula 302", "14/08/2026", "10:00 - 12:00", "Cancelada", "10/08/2026 - 17:08"]

        ];

    }


    contenido.innerHTML = "";


    datos.forEach(function(reserva) {


        let informacionCancelacion = "";


        if (tipo === "canceladas") {

            informacionCancelacion = `

                <div>

                    <small>
                        Cancelada el
                    </small>

                    <strong>
                        ${reserva[5]}
                    </strong>

                </div>

            `;

        }


        contenido.innerHTML += `

            <div class="reserva-detalle">


                <div class="usuario">

                    <div class="avatar-detalle">

                        ${reserva[0].substring(0, 2).toUpperCase()}

                    </div>


                    <div>

                        <strong>
                            ${reserva[0]}
                        </strong>

                        <small>
                            Profesor
                        </small>

                    </div>

                </div>



                <div>

                    <small>
                        Recurso
                    </small>

                    <strong>
                        ${reserva[1]}
                    </strong>

                </div>



                <div>

                    <small>
                        Fecha
                    </small>

                    <strong>
                        ${reserva[2]}
                    </strong>

                </div>



                <div>

                    <small>
                        Horario
                    </small>

                    <strong>
                        ${reserva[3]}
                    </strong>

                </div>



                <div>

                    <small>
                        Estado
                    </small>

                    <span class="estado ${reserva[4].toLowerCase()}">

                        ${reserva[4]}

                    </span>

                </div>


                ${informacionCancelacion}


            </div>

        `;

    });


    detalle.classList.add("mostrar");


    detalle.scrollIntoView({

        behavior: "smooth",

        block: "start"

    });

}



function cerrarDetalle() {

    const detalle =
        document.getElementById("detalleReservas");

    detalle.classList.remove("mostrar");

}