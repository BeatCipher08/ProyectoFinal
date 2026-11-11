document.addEventListener('DOMContentLoaded', () => {
    const tabs = document.querySelectorAll('.catalogo-tab');
    const cards = document.querySelectorAll('.resource-card');
    const emptyStates = document.querySelectorAll('.empty-state');

    function aplicarCategoria(categoria) {
        let visibles = 0;

        cards.forEach((card) => {
            const mostrar = card.dataset.category === categoria;
            card.style.display = mostrar ? 'flex' : 'none';
            if (mostrar) visibles++;
        });

        emptyStates.forEach((empty) => {
            const esCategoria = empty.dataset.category === categoria;
            empty.classList.toggle('visible', esCategoria && visibles === 0);
        });

        tabs.forEach((tab) => {
            tab.classList.toggle('active', tab.dataset.target === categoria);
        });
    }

    tabs.forEach((tab) => {
        tab.addEventListener('click', () => aplicarCategoria(tab.dataset.target));
    });

    aplicarCategoria('equipos');
});
