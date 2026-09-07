document.addEventListener('DOMContentLoaded', function() {
    // IMPORTANTE: El ID aquí debe ser exactamente el mismo del DIV en tu HTML
    var contenedorCalendario = document.getElementById('calendario-js');

    if (contenedorCalendario) {
        var calendar = new FullCalendar.Calendar(contenedorCalendario, {
            initialView: 'dayGridMonth',
            locale: 'es', // Idioma español
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: '' 
            },
            events: [
                {
                    title: 'Laboratorio 1 - Prof. Silva',
                    start: '2026-07-10'
                }
            ]
        });

        calendar.render();
    } else {
        console.error("No se encontró el contenedor #calendario-js");
    }
});