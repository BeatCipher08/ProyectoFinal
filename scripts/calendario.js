const dias = document.getElementById("dias");
const mesActual = document.getElementById("mesActual");
const mesAnterior = document.getElementById("mesAnterior");
const mesSiguiente = document.getElementById("mesSiguiente");

let fechaActual = new Date(2026, 7, 1);

const reservas = {
    "2026-08-27": {
        usuario: "Juan Pérez",
        aula: "Aula 101",
        horario: "08:00 - 10:00",
        motivo: "Clase"
    },

    "2026-08-29": {
        usuario: "María González",
        aula: "Laboratorio 1",
        horario: "14:00 - 16:00",
        motivo: "Trabajo práctico"
    },

    "2026-09-02": {
        usuario: "Ana López",
        aula: "Aula 204",
        horario: "10:00 - 12:00",
        motivo: "Clase de informática"
    }
};

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

function generarCalendario() {

    dias.innerHTML = "";

    const año = fechaActual.getFullYear();
    const mes = fechaActual.getMonth();

    mesActual.textContent = meses[mes] + " " + año;

    const primerDia = new Date(año, mes, 1).getDay();

    const cantidadDias = new Date(
        año,
        mes + 1,
        0
    ).getDate();

    let inicio = primerDia - 1;

    if (inicio < 0) {
        inicio = 6;
    }

    for (let i = 0; i < inicio; i++) {

        const espacio = document.createElement("div");

        espacio.className = "dia vacio";

        dias.appendChild(espacio);
    }

    for (let numero = 1; numero <= cantidadDias; numero++) {

        const dia = document.createElement("div");

        dia.className = "dia";

        const numeroDia = document.createElement("span");

        numeroDia.className = "numero";

        numeroDia.textContent = numero;

        dia.appendChild(numeroDia);

        const mesTexto = String(mes + 1).padStart(2, "0");

        const diaTexto = String(numero).padStart(2, "0");

        const fecha =
            año + "-" +
            mesTexto + "-" +
            diaTexto;

        if (reservas[fecha]) {

            dia.classList.add("reservado");

            const evento = document.createElement("div");

            evento.className = "evento";

            evento.textContent =
                reservas[fecha].aula;

            dia.appendChild(evento);

            dia.addEventListener("click", function () {

                mostrarReserva(
                    fecha,
                    reservas[fecha]
                );

            });

        } else {

            dia.classList.add("disponible");

        }

        dias.appendChild(dia);
    }
}

function mostrarReserva(fecha, reserva) {

    alert(
        "Reserva\n\n" +
        "Fecha: " + fecha + "\n" +
        "Usuario: " + reserva.usuario + "\n" +
        "Aula: " + reserva.aula + "\n" +
        "Horario: " + reserva.horario + "\n" +
        "Motivo: " + reserva.motivo
    );
}

mesAnterior.addEventListener("click", function () {

    fechaActual.setMonth(
        fechaActual.getMonth() - 1
    );

    generarCalendario();

});

mesSiguiente.addEventListener("click", function () {

    fechaActual.setMonth(
        fechaActual.getMonth() + 1
    );

    generarCalendario();

});

generarCalendario();