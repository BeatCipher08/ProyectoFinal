function abrirFormularioReserva() {
    document.getElementById('modalNuevaReserva').style.display = 'flex';
    document.getElementById('formReserva').reset();
    limpiarErrorReserva();
    toggleCamposDinámicos(); 
}

function cerrarFormularioReserva() {
    document.getElementById('modalNuevaReserva').style.display = 'none';
}

function toggleCamposDinámicos() {
    const checkSala = document.getElementById('checkSala').checked;
    const checkEquipo = document.getElementById('checkEquipo').checked;
    
    const seccionSala = document.getElementById('seccionSala');
    const seccionEquipo = document.getElementById('seccionEquipo');
    const btnEnviar = document.getElementById('btnEnviarReserva');

    if (checkSala) {
        seccionSala.style.display = 'block';
        seccionSala.querySelector('select').setAttribute('required', 'true');
    } else {
        seccionSala.style.display = 'none';
        seccionSala.querySelector('select').removeAttribute('required');
    }

    if (checkEquipo) {
        seccionEquipo.style.display = 'block';
        seccionEquipo.querySelector('select').setAttribute('required', 'true');
    } else {
        seccionEquipo.style.display = 'none';
        seccionEquipo.querySelector('select').removeAttribute('required');
    }

    if (checkSala || checkEquipo) {
        btnEnviar.disabled = false;
        btnEnviar.classList.remove('btn-deshabilitado');
    } else {
        btnEnviar.disabled = true;
        btnEnviar.classList.add('btn-deshabilitado');
    }
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
                window.location.href = 'reservas_profesor.php';
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
        console.log('Event listener agregado al form (DOMContentLoaded)');
    }
});
