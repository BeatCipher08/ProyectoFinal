document.addEventListener("DOMContentLoaded", function () {

    const lista = document.getElementById("listaReservas");
    const sinReservas = document.getElementById("sinReservas");
    const cantidadActivas = document.getElementById("cantidadActivas");
    const cantidadCanceladas = document.getElementById("cantidadCanceladas");
    const proximaReserva = document.getElementById("proximaReserva");
    const filtro = document.getElementById("filtro");

    if (!lista || !sinReservas || !cantidadActivas || !cantidadCanceladas || !proximaReserva || !filtro) {
        alert("ERROR: El HTML de Mis Reservas no tiene alguno de los elementos necesarios.");
        return;
    }

    function obtenerReservas() {
        try {
            return JSON.parse(localStorage.getItem("reservas")) || [];
        } catch (error) {
            alert("ERROR: No se pudieron leer las reservas.");
            return [];
        }
    }

    function obtenerCanceladas() {
        try {
            return JSON.parse(localStorage.getItem("reservasCanceladas")) || [];
        } catch (error) {
            return [];
        }
    }

    function mostrarReservas() {

        const reservas = obtenerReservas();
        const canceladas = obtenerCanceladas();

        lista.innerHTML = "";

        let reservasMostrar = [...reservas];

        if (filtro.value === "proximas") {
            reservasMostrar = reservasMostrar.filter(
                reserva => reserva.fecha >= obtenerFechaHoy()
            );
        }

        if (filtro.value === "hoy") {
            reservasMostrar = reservasMostrar.filter(
                reserva => reserva.fecha === obtenerFechaHoy()
            );
        }

        cantidadActivas.textContent = reservas.length;
        cantidadCanceladas.textContent = canceladas.length;

        if (reservasMostrar.length === 0) {

            sinReservas.style.display = "block";

        } else {

            sinReservas.style.display = "none";

            reservasMostrar.forEach(function (reserva) {
                crearReserva(reserva);
            });

        }

        actualizarProxima(reservas);
    }

    function crearReserva(reserva) {

        const fecha = new Date(reserva.fecha + "T00:00:00");

        const dia = String(fecha.getDate()).padStart(2, "0");

        const meses = [
            "ENE",
            "FEB",
            "MAR",
            "ABR",
            "MAY",
            "JUN",
            "JUL",
            "AGO",
            "SEP",
            "OCT",
            "NOV",
            "DIC"
        ];

        const mes = meses[fecha.getMonth()];

        const articulo = document.createElement("article");

        articulo.className = "reserva";

        articulo.innerHTML = `
            <div class="fecha">
                <strong>${dia}</strong>
                <span>${mes}</span>
            </div>

            <div class="informacion">

                <h3>${reserva.motivo}</h3>

                <p>🏫 ${reserva.recurso}</p>

                <p>🕐 ${reserva.horaInicio} - ${reserva.horaFin}</p>

                <p>👥 ${reserva.cantidad} personas</p>

                <p>📽️ ${reserva.equipamiento || "Ninguno"}</p>

            </div>

            <div class="estado">

                <span>Pendiente</span>

                <button>
                    Cancelar
                </button>

            </div>
        `;

        articulo
            .querySelector("button")
            .addEventListener("click", function () {
                cancelarReserva(reserva.id);
            });

        lista.appendChild(articulo);
    }

    function cancelarReserva(id) {

        const confirmar = confirm(
            "¿Querés cancelar esta reserva?"
        );

        if (!confirmar) {
            return;
        }

        let reservas = obtenerReservas();

        let canceladas = obtenerCanceladas();

        const reserva = reservas.find(
            item => item.id === id
        );

        if (!reserva) {
            return;
        }

        reservas = reservas.filter(
            item => item.id !== id
        );

        canceladas.push(reserva);

        localStorage.setItem(
            "reservas",
            JSON.stringify(reservas)
        );

        localStorage.setItem(
            "reservasCanceladas",
            JSON.stringify(canceladas)
        );

        mostrarReservas();
    }

    function obtenerFechaHoy() {

        const hoy = new Date();

        const año = hoy.getFullYear();

        const mes = String(
            hoy.getMonth() + 1
        ).padStart(2, "0");

        const dia = String(
            hoy.getDate()
        ).padStart(2, "0");

        return `${año}-${mes}-${dia}`;
    }

    function actualizarProxima(reservas) {

        if (reservas.length === 0) {
            proximaReserva.textContent = "-";
            return;
        }

        const futuras = reservas
            .filter(
                reserva =>
                    reserva.fecha >= obtenerFechaHoy()
            )
            .sort(
                (a, b) =>
                    new Date(a.fecha) -
                    new Date(b.fecha)
            );

        if (futuras.length === 0) {
            proximaReserva.textContent = "-";
            return;
        }

        const fecha = new Date(
            futuras[0].fecha + "T00:00:00"
        );

        const dia = String(
            fecha.getDate()
        ).padStart(2, "0");

        const mes = String(
            fecha.getMonth() + 1
        ).padStart(2, "0");

        proximaReserva.textContent =
            `${dia}/${mes}`;
    }

    filtro.addEventListener(
        "change",
        mostrarReservas
    );

    mostrarReservas();

});