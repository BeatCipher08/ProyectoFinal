/* ==========================================================
   LÓGICA DEL CARRUSEL 3D INFINITO (MÓVIL)
   ========================================================== */
document.addEventListener("DOMContentLoaded", function() {
    
    const tarjetas = document.querySelectorAll('.tarjeta');
    const btnPrev = document.querySelector('.btn-prev');
    const btnNext = document.querySelector('.btn-next');

    // Valida que existan los elementos del carrusel en la página actual
    if (tarjetas.length > 0 && btnPrev && btnNext) {
        
        let indiceActual = 0; // Índice de la tarjeta central por defecto
        const total = tarjetas.length;

        function actualizarCarrusel() {
            // 1. Limpia las clases dinámicas de todas las tarjetas
            tarjetas.forEach(t => t.classList.remove('activa', 'anterior', 'siguiente', 'oculta'));

            // 2. Aplica el comportamiento solo en vistas móviles (menor o igual a 768px)
            if (window.innerWidth <= 768) {
                
                // Cálculo matemático modular para un bucle infinito real
                let anterior = (indiceActual - 1 + total) % total;
                let siguiente = (indiceActual + 1) % total;

                // Asignación de roles visuales al carrusel
                tarjetas[indiceActual].classList.add('activa');
                tarjetas[anterior].classList.add('anterior');
                tarjetas[siguiente].classList.add('siguiente');

                // Oculta las tarjetas que no están adyacentes al foco actual
                tarjetas.forEach((t, i) => {
                    if (i !== indiceActual && i !== anterior && i !== siguiente) {
                        t.classList.add('oculta');
                    }
                });
            }
        }

        // Evento click en la flecha derecha
        btnNext.addEventListener('click', () => {
            indiceActual = (indiceActual + 1) % total;
            actualizarCarrusel();
        });

        // Evento click en la flecha izquierda
        btnPrev.addEventListener('click', () => {
            indiceActual = (indiceActual - 1 + total) % total;
            actualizarCarrusel();
        });

        // Recalcula si el usuario cambia el tamaño de la ventana o rota el dispositivo
        window.addEventListener('resize', actualizarCarrusel);

        // Dispara la función al cargar la página para establecer el estado inicial
        actualizarCarrusel();
    }
});