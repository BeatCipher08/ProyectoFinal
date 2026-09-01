function cancelarReserva(boton) {
    const reserva = boton.closest(".reserva");

    const confirmar = confirm(
        "¿Seguro que querés cancelar esta reserva?"
    );

    if (confirmar) {
        const titulo = reserva.querySelector("h3").textContent;

        reserva.remove();

        alert("La reserva de " + titulo + " fue cancelada correctamente.");
    }
}

function mostrarDetalles(boton) {
    const reserva = boton.closest(".reserva");

    const titulo = reserva.querySelector("h3").textContent;
    const datos = reserva.querySelector(".datos-reserva").textContent;

    alert(
        "Reserva: " + titulo + "\n\n" +
        datos
    );
}