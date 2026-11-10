// Manejo del Modal
function abrirFormulario() {
    const form = document.getElementById('formRecurso');
    if (form) {
        form.reset();
    }

    const tipoSelect = document.getElementById('tipoRecurso');
    const estadoSelect = document.getElementById('estadoRecurso');
    const cantidadInput = document.getElementById('cantidadTotalInput');

    if (tipoSelect) tipoSelect.value = 'sala';
    if (estadoSelect) estadoSelect.value = '1';
    if (cantidadInput) cantidadInput.value = 1;

    document.getElementById('modalRecurso').style.display = 'flex';
    verificarTipo(); // Chequear si hay que mostrar la imagen al abrir
}

function cerrarFormulario() {
    document.getElementById('modalRecurso').style.display = 'none';
}

// Mostrar input de imagen solo si es equipamiento
function verificarTipo() {
    let tipo = document.getElementById('tipoRecurso').value;
    let campoImagen = document.getElementById('campoImagen');
    let campoCantidad = document.getElementById('campoCantidad');
    let cantidadInput = document.getElementById('cantidadTotalInput');

    if (campoImagen) {
        campoImagen.style.display = 'block';
    }
    
    if (tipo === 'equipo') {
        if (campoCantidad) campoCantidad.style.display = 'block';
        if (cantidadInput) {
            cantidadInput.value = Math.max(1, Number(cantidadInput.value || 1));
            cantidadInput.readOnly = false;
        }
    } else {
        if (campoCantidad) campoCantidad.style.display = 'none';
        if (cantidadInput) {
            cantidadInput.value = 1;
            cantidadInput.readOnly = true;
        }
    }
}

// Filtros combinados: categoría + mostrar/ocultar desactivados
let categoriaActiva = 'espacios';
let mostrarDesactivados = false;

function aplicarFiltrosRecursos() {
    const filas = document.querySelectorAll('#tablaRecursos tr');

    filas.forEach(fila => {
        const categoria = fila.getAttribute('data-categoria');
        const activo = fila.getAttribute('data-activo') === '1';

        const coincideCategoria = categoria === categoriaActiva;
        const visiblePorEstado = mostrarDesactivados ? true : activo;

        fila.style.display = (coincideCategoria && visiblePorEstado) ? '' : 'none';
    });
}

function cambiarPestana(categoria, botonClickeado) {
    categoriaActiva = categoria;

    const botones = document.querySelectorAll('.btn-pestana');
    botones.forEach(btn => btn.classList.remove('activa'));
    botonClickeado.classList.add('activa');

    aplicarFiltrosRecursos();
}

function alternarDesactivados(boton) {
    mostrarDesactivados = !mostrarDesactivados;
    boton.setAttribute('aria-pressed', mostrarDesactivados ? 'true' : 'false');
    boton.classList.toggle('activo', mostrarDesactivados);
    aplicarFiltrosRecursos();
}

document.addEventListener('DOMContentLoaded', () => {
    const botonToggle = document.getElementById('toggleDesactivados');
    if (botonToggle) {
        botonToggle.setAttribute('aria-pressed', 'false');
        botonToggle.classList.remove('activo');
    }

    aplicarFiltrosRecursos();
});