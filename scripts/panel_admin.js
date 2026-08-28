function mostrarFecha() {

    const fecha = new Date();

    const dia = String(fecha.getDate()).padStart(2, "0");

    const mes = String(fecha.getMonth() + 1).padStart(2, "0");

    const año = fecha.getFullYear();

    document.getElementById("fechaActual").textContent =
        dia + "/" + mes + "/" + año;
}


mostrarFecha();