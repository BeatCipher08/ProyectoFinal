let fechaActual = new Date();

const reservas = [
    {
        fecha: "2026-08-10",
        titulo: "Clase de Matemática",
        hora: "08:00 - 10:00",
        aula: "Aula 101",
        persona: "Juan Pérez",
        motivo: "Clase"
    },
    {
        fecha: "2026-08-12",
        titulo: "Laboratorio",
        hora: "10:00 - 12:00",
        aula: "Laboratorio 2",
        persona: "María González",
        motivo: "Práctica"
    },
    {
        fecha: "2026-08-15",
        titulo: "Reunión docente",
        hora: "14:00 - 16:00",
        aula: "Sala 3",
        persona: "Carlos Rodríguez",
        motivo: "Reunión"
    },
    {
        fecha: "2026-08-18",
        titulo: "Informática",
        hora: "09:00 - 11:00",
        aula: "Aula 204",
        persona: "Ana Martínez",
        motivo: "Clase"
    },
    {
        fecha: "2026-08-20",
        titulo: "Proyecto",
        hora: "13:00 - 15:00",
        aula: "Laboratorio 1",
        persona: "Pedro Silva",
        motivo: "Trabajo práctico"
    }
];

function mostrarCalendario() {

    const contenedor = document.getElementById("dias");
    const titulo = document.getElementById("mesAnio");

    contenedor.innerHTML = "";

    const año = fechaActual.getFullYear();
    const mes = fechaActual.getMonth();

    const meses = [
        "Enero",
        "Febrero",
        "Marzo",
        "Abril",
        "Mayo",
        "Junio",
        "Julio",
        "Agosto",
        "Septiembre",
        "Octubre",
        "Noviembre",
        "Diciembre"
    ];

    titulo.textContent = meses[mes] + " " + año;

    let primerDia = new Date(año, mes, 1).getDay();

    if (primerDia === 0) {
        primerDia = 7;
    }

    const cantidadDias = new Date(año, mes + 1, 0).getDate();

    const diasMesAnterior = new Date(año, mes, 0).getDate();

    for (let i = primerDia - 1; i > 0; i--) {

        const dia = document.createElement("div");

        dia.className = "dia otro-mes";

        dia.innerHTML = `
            <div class="numero">
                ${diasMesAnterior - i + 1}
            </div>
        `;

        contenedor.appendChild(dia);
    }

    for (let numero = 1; numero <= cantidadDias; numero++) {

        const dia = document.createElement("div");

        dia.className = "dia";

        const fecha = año + "-" +
            String(mes + 1).padStart(2, "0") + "-" +
            String(numero).padStart(2, "0");

        dia.innerHTML = `
            <div class="numero">${numero}</div>
        `;

        const reservasDelDia = reservas.filter(
            reserva => reserva.fecha === fecha
        );

        reservasDelDia.forEach(reserva => {

            const evento = document.createElement("div");

            evento.className = "reserva";

            evento.innerHTML = `
                <strong>${reserva.titulo}</strong>
                ${reserva.hora}
            `;

            evento.addEventListener("click", function () {
                mostrarReserva(reserva);
            });

            dia.appendChild(evento);
        });

        contenedor.appendChild(dia);
    }

    const totalDias = primerDia - 1 + cantidadDias;

    const celdasRestantes = 42 - totalDias;

    for (let numero = 1; numero <= celdasRestantes; numero++) {

        const dia = document.createElement("div");

        dia.className = "dia otro-mes";

        dia.innerHTML = `
            <div class="numero">${numero}</div>
        `;

        contenedor.appendChild(dia);
    }
}

function mesAnterior() {

    fechaActual.setMonth(
        fechaActual.getMonth() - 1
    );

    mostrarCalendario();
}

function mesSiguiente() {

    fechaActual.setMonth(
        fechaActual.getMonth() + 1
    );

    mostrarCalendario();
}

function irHoy() {

    fechaActual = new Date();

    mostrarCalendario();
}

function mostrarReserva(reserva) {

    document.getElementById("tituloReserva").textContent =
        reserva.titulo;

    document.getElementById("fechaReserva").textContent =
        reserva.fecha;

    document.getElementById("horaReserva").textContent =
        reserva.hora;

    document.getElementById("aulaReserva").textContent =
        reserva.aula;

    document.getElementById("personaReserva").textContent =
        reserva.persona;

    document.getElementById("motivoReserva").textContent =
        reserva.motivo;

    document.getElementById("ventanaReserva").style.display =
        "flex";
}

function cerrarReserva() {

    document.getElementById("ventanaReserva").style.display =
        "none";
}

function volver() {

    window.history.back();
}

window.addEventListener("load", function () {

    mostrarCalendario();

});