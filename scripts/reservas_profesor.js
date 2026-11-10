function abrirFormularioReserva() {
    document.getElementById('modalNuevaReserva').style.display = 'flex';
    document.getElementById('formReserva').reset();
    limpiarErrorReserva();
    toggleCamposDinámicos(); 
}

function cerrarFormularioReserva() {
    document.getElementById('modalNuevaReserva').style.display = 'none';
}

function actualizarEstadoBotonReserva() {
    const btnEnviar = document.getElementById('btnEnviarReserva');
    if (!btnEnviar) return;

    const checkSala = document.getElementById('checkSala')?.checked;
    const checkEquipo = document.getElementById('checkEquipo')?.checked;
    const salaSelect = document.querySelector('select[name="sala_id"]');
    const equipoSelect = document.querySelector('select[name="equipo_id"]');
    const fechaInput = document.querySelector('input[name="fecha"]');
    const horaSelect = document.querySelector('select[name="hora_inicio"]');
    const cantidadSelect = document.querySelector('select[name="cantidad_clases"]');

    const tieneReservaSeleccionada = !!(checkSala || checkEquipo);
    const salaCompleta = !checkSala || !!(salaSelect && !salaSelect.disabled && salaSelect.value);
    const equipoCompleta = !checkEquipo || !!(equipoSelect && !equipoSelect.disabled && equipoSelect.value);
    const fechaCompleta = !!(fechaInput && fechaInput.value);
    const horaCompleta = !!(horaSelect && horaSelect.value);
    const cantidadCompleta = !!(cantidadSelect && cantidadSelect.value);

    const formValido = tieneReservaSeleccionada && salaCompleta && equipoCompleta && fechaCompleta && horaCompleta && cantidadCompleta;

    btnEnviar.disabled = !formValido;
    btnEnviar.classList.toggle('btn-deshabilitado', !formValido);
    btnEnviar.style.background = formValido ? '' : '#e7ebee';
    btnEnviar.style.color = formValido ? '' : '#6c757d';
    btnEnviar.style.cursor = formValido ? 'pointer' : 'not-allowed';
    btnEnviar.style.opacity = formValido ? '1' : '0.65';
}

function toggleCamposDinámicos() {
    const checkSala = document.getElementById('checkSala').checked;
    const checkEquipo = document.getElementById('checkEquipo').checked;
    
    const seccionSala = document.getElementById('seccionSala');
    const seccionEquipo = document.getElementById('seccionEquipo');
    const salaSelect = seccionSala?.querySelector('select');
    const equipoSelect = seccionEquipo?.querySelector('select');

    if (checkSala) {
        seccionSala.style.display = 'block';
        if (salaSelect) {
            salaSelect.disabled = false;
            salaSelect.setAttribute('required', 'true');
        }
    } else {
        seccionSala.style.display = 'none';
        if (salaSelect) {
            salaSelect.disabled = true;
            salaSelect.removeAttribute('required');
            salaSelect.value = '';
        }
    }

    if (checkEquipo) {
        seccionEquipo.style.display = 'block';
        if (equipoSelect) {
            equipoSelect.disabled = false;
            equipoSelect.setAttribute('required', 'true');
        }
    } else {
        seccionEquipo.style.display = 'none';
        if (equipoSelect) {
            equipoSelect.disabled = true;
            equipoSelect.removeAttribute('required');
            equipoSelect.value = '';
        }
    }

    actualizarEstadoBotonReserva();
}

function limpiarErrorReserva() {
    const errorBox = document.getElementById('errorReservaInline');
    if (errorBox) {
        errorBox.style.display = 'none';
        errorBox.classList.remove('error-visible');
    }
}

function mostrarErrorReserva(mensaje) {
    let errorBox = document.getElementById('errorReservaInline');
    if (!errorBox) {
        const botonSubmit = document.getElementById('btnEnviarReserva');
        errorBox = document.createElement('div');
        errorBox.id = 'errorReservaInline';
        errorBox.className = 'error-reserva';
        errorBox.setAttribute('role', 'alert');
        errorBox.innerHTML = '<span class="error-linea"></span><span></span>';
        botonSubmit.parentElement.insertBefore(errorBox, botonSubmit);
    }
    errorBox.querySelector('span:last-child').textContent = mensaje;
    errorBox.classList.remove('error-visible');
    // Forzar reflow para que se registre el estado inicial
    void errorBox.offsetWidth;
    // Luego agregar la clase para disparar la animación
    errorBox.classList.add('error-visible');
}

function enviarReservaAjax(e) {
    e.preventDefault();
    console.log('Iniciando envío AJAX'); // Debug
    
    const form = document.getElementById('formReserva');
    limpiarErrorReserva();

    const formData = new FormData(form);
    formData.append('ajax', '1');

    console.log('FormData preparado'); // Debug

    fetch('procesar_reserva.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        console.log('Response status:', response.status); // Debug
        return response.text().then(text => {
            console.log('Response text:', text); // Debug
            try {
                return JSON.parse(text);
            } catch (e) {
                throw new Error('Invalid JSON: ' + text);
            }
        });
    })
    .then(data => {
        console.log('Data recibido:', data); // Debug
        if (data.success) {
            console.log('Éxito! Redirigiendo...'); // Debug
            form.reset();
            toggleCamposDinámicos();
            setTimeout(() => {
                window.location.href = 'panel_profesor.php';
            }, 800);
        } else {
            console.log('Error detectado:', data.error); // Debug
            mostrarErrorReserva(data.error);
        }
    })
    .catch(error => {
        console.error('Error en fetch:', error); // Debug
        mostrarErrorReserva('Error al procesar la solicitud. Intenta de nuevo: ' + error.message);
    });
    
    return false;
}

// Agregar el event listener lo antes posible
const formReserva = document.getElementById('formReserva');
if (formReserva) {
    formReserva.addEventListener('submit', function(e) {
        console.log('Submit form disparado');
        e.preventDefault();
        enviarReservaAjax(e);
        return false;
    });
    console.log('Event listener agregado al form');
} else {
    console.warn('Form #formReserva no encontrado');
}

// Backup: también con DOMContentLoaded por si acaso
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('formReserva');
    if (form && !form.getAttribute('data-listener-attached')) {
        form.setAttribute('data-listener-attached', 'true');
        form.addEventListener('submit', function(e) {
            console.log('Submit form disparado (DOMContentLoaded)');
            e.preventDefault();
            enviarReservaAjax(e);
            return false;
        });
        form.addEventListener('input', actualizarEstadoBotonReserva);
        form.addEventListener('change', actualizarEstadoBotonReserva);
        console.log('Event listener agregado al form (DOMContentLoaded)');
    }

    if (window.location.hash === '#nueva-reserva') {
        abrirFormularioReserva();
    }

    actualizarEstadoBotonReserva();
});
