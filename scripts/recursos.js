// Manejo del Modal
function abrirFormulario() {
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
    
    if (tipo === 'equipo') {
        campoImagen.style.display = 'block';
    } else {
        campoImagen.style.display = 'none';
    }
}

// Sistema de Pestañas
function cambiarPestana(categoria, botonClickeado) {
    // Cambiar estilos de los botones
    let botones = document.querySelectorAll('.btn-pestana');
    botones.forEach(btn => {
        btn.style.background = 'transparent';
        btn.style.color = '#777';
        btn.classList.remove('activa');
    });
    
    botonClickeado.style.background = '#e5f2e5';
    botonClickeado.style.color = '#4f8c4f';
    botonClickeado.classList.add('activa');

    // Filtrar la tabla
    let filas = document.querySelectorAll('#tablaRecursos tr');
    filas.forEach(fila => {
        if (fila.getAttribute('data-categoria') === categoria) {
            fila.style.display = '';
        } else {
            fila.style.display = 'none';
        }
    });
}

// Cargar la pestaña de espacios por defecto al iniciar
document.addEventListener('DOMContentLoaded', () => {
    let filas = document.querySelectorAll('#tablaRecursos tr');
    filas.forEach(fila => {
        if (fila.getAttribute('data-categoria') !== 'espacios') {
            fila.style.display = 'none';
        }
    });
});