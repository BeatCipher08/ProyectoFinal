const monthLabel = document.getElementById('monthLabel');
const calendarGrid = document.getElementById('calendarGrid');
const detailHeader = document.getElementById('detailHeader');
const detailList = document.getElementById('detailList');
const prevMonth = document.getElementById('prevMonth');
const nextMonth = document.getElementById('nextMonth');
const toggleAllReservations = document.getElementById('toggleAllReservations');
const reservationSearch = document.getElementById('reservationSearch');
const filterPopover = document.getElementById('filterPopover');
const openFilters = document.getElementById('openFilters');
const closeFilters = document.getElementById('closeFilters');
const applyFilters = document.getElementById('applyFilters');
const clearFilters = document.getElementById('clearFilters');
const filterEstado = document.getElementById('filterEstado');
const filterTipoRecurso = document.getElementById('filterTipoRecurso');
const filterFecha = document.getElementById('filterFecha');

const now = new Date();
const initialYear = Number.isInteger(window.calendarInitialYear) ? window.calendarInitialYear : now.getFullYear();
const initialMonth = Number.isInteger(window.calendarInitialMonth) ? window.calendarInitialMonth : now.getMonth();
const calendarDate = new Date(initialYear, initialMonth, 1);
let selectedDate = typeof window.calendarInitialDate === 'string' && window.calendarInitialDate
    ? window.calendarInitialDate
    : formatDateKey(now);
let showAllReservations = false;
const today = new Date();
today.setHours(0, 0, 0, 0);

const weekdayNames = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];
const monthNames = [
    'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
    'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
];

const reservaMap = (window.activityMap && typeof window.activityMap === 'object' && !Array.isArray(window.activityMap))
    ? window.activityMap
    : {};

function formatDateKey(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

function formatLongDate(dateKey) {
    const [year, month, day] = dateKey.split('-').map(Number);
    const date = new Date(year, month - 1, day);
    return date.toLocaleDateString('es-ES', {
        weekday: 'long',
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    });
}

function formatDateShort(dateKey) {
    const [year, month, day] = dateKey.split('-').map(Number);
    const date = new Date(year, month - 1, day);
    return date.toLocaleDateString('es-ES');
}

function getFilterState() {
    return {
        query: (reservationSearch?.value || '').trim().toLowerCase(),
        estado: filterEstado?.value || 'todos',
        tipo: filterTipoRecurso?.value || 'todos',
        fecha: filterFecha?.value || ''
    };
}

function matchesReservationFilters(item, filters) {
    if (!item) return false;

    if (filters.query) {
        const searchable = [
            item.titulo,
            item.recurso,
            item.profesor,
            item.descripcion,
            item.horario,
            item.fecha,
            formatDateShort(item.fecha || ''),
            formatLongDate(item.fecha || '')
        ].join(' ').toLowerCase();

        if (!searchable.includes(filters.query)) {
            return false;
        }
    }

    if (filters.estado !== 'todos' && String(item.estado || '').toLowerCase() !== filters.estado) {
        return false;
    }

    if (filters.tipo !== 'todos' && String(item.tipo_recurso || '').toLowerCase() !== filters.tipo) {
        return false;
    }

    if (filters.fecha && String(item.fecha || '') !== filters.fecha) {
        return false;
    }

    return true;
}

function getFilteredReservations() {
    const source = showAllReservations
        ? Object.entries(reservaMap).flatMap(([date, dayItems]) =>
            dayItems.map((item) => ({ ...item, fecha: date }))
        )
        : (reservaMap[selectedDate] || []).map((item) => ({ ...item, fecha: selectedDate }));

    const filters = getFilterState();
    return source.filter((item) => matchesReservationFilters(item, filters));
}

function setPopoverOpen(open) {
    if (!filterPopover) return;
    filterPopover.classList.toggle('open', open);
    filterPopover.setAttribute('aria-hidden', String(!open));
}

function renderCalendar() {
    calendarGrid.innerHTML = '';

    weekdayNames.forEach((day) => {
        const weekday = document.createElement('div');
        weekday.className = 'weekday';
        weekday.textContent = day;
        calendarGrid.appendChild(weekday);
    });

    const year = calendarDate.getFullYear();
    const month = calendarDate.getMonth();
    const firstDay = new Date(year, month, 1);
    const lastDay = new Date(year, month + 1, 0);
    const startOffset = (firstDay.getDay() + 6) % 7;
    const totalDays = lastDay.getDate();

    monthLabel.textContent = `${monthNames[month]} ${year}`;

    for (let i = 0; i < startOffset; i++) {
        const filler = document.createElement('div');
        filler.className = 'day-cell empty';
        calendarGrid.appendChild(filler);
    }

    for (let day = 1; day <= totalDays; day++) {
        const date = new Date(year, month, day);
        const dateKey = formatDateKey(date);
        const cell = document.createElement('button');
        cell.type = 'button';
        cell.className = 'day-cell';

        if (dateKey === selectedDate) {
            cell.classList.add('selected');
        }

        const compareDate = new Date(year, month, day);
        compareDate.setHours(0, 0, 0, 0);
        const isPastDay = compareDate.getTime() < today.getTime();
        if (isPastDay) {
            cell.classList.add('past-day');
        }

        const items = Array.isArray(reservaMap[dateKey]) ? reservaMap[dateKey] : [];
        const estados = items
            .map((item) => String(item.estado || '').toLowerCase())
            .filter(Boolean);
        const hasPending = estados.includes('pendiente');
        const hasApproved = estados.includes('aprobada');
        const hasActivity = hasPending || hasApproved;
        if (hasActivity && !isPastDay) {
            cell.classList.add('has-activity');
        }

        const badgeHtml = isPastDay
            ? ''
            : (hasActivity
                ? `${hasPending ? '<span class="day-badge day-badge-warning">Solicitud</span>' : ''}${hasApproved ? '<span class="day-badge day-badge-success">Reservado</span>' : ''}`
                : '<span class="day-empty">Libre</span>');

        cell.innerHTML = `
            <span class="day-number">${day}</span>
            <span class="day-badge-stack">${badgeHtml}</span>
        `;

        cell.addEventListener('click', () => {
            selectedDate = dateKey;
            showAllReservations = false;
            updateToggle();
            renderCalendar();
            renderDetail();
        });

        calendarGrid.appendChild(cell);
    }

    const totalCells = calendarGrid.children.length;
    const remainingTiles = (7 - (totalCells % 7)) % 7;

    for (let i = 0; i < remainingTiles; i++) {
        const filler = document.createElement('div');
        filler.className = 'day-cell empty';
        calendarGrid.appendChild(filler);
    }
}

function updateToggle() {
    if (!toggleAllReservations) return;
    toggleAllReservations.classList.toggle('active', showAllReservations);
    toggleAllReservations.setAttribute('aria-pressed', String(showAllReservations));
}

function renderDetail() {
    const items = getFilteredReservations();

    if (!showAllReservations) {
        detailHeader.textContent = formatLongDate(selectedDate).replace(/^./, (char) => char.toUpperCase());
    } else {
        detailHeader.textContent = 'Todas mis reservas';
    }

    if (!items.length) {
        detailList.innerHTML = `
            <div class="detail-empty">
                <p>${showAllReservations ? 'No hay reservas que coincidan con los filtros aplicados.' : 'No hay reservas para este día con los filtros actuales.'}</p>
            </div>
        `;
        return;
    }

    detailList.innerHTML = items.map((item) => {
        const estado = String(item.estado || 'pendiente').toLowerCase();
        const estadoLabel = estado === 'aprobada' ? 'Aprobada' : estado === 'pendiente' ? 'Pendiente' : 'Rechazada';
        return `
            <article class="activity-card">
                <div class="activity-title">${item.titulo}</div>
                <div class="activity-line"><span>Recurso:</span> ${item.recurso}</div>
                <div class="activity-line"><span>Fecha:</span> ${showAllReservations ? formatDateShort(item.fecha) : formatLongDate(item.fecha)}</div>
                <div class="activity-line"><span>Horario:</span> ${item.horario}</div>
                <div class="activity-line"><span>Cantidad de clases:</span> ${item.cantidad_clases || 1}</div>
                <div class="activity-line"><span>Estado:</span> ${estadoLabel}</div>
                <div class="activity-line"><span>Profesor:</span> ${item.profesor || 'Profesor'}</div>
                <div class="activity-line"><span>Descripción:</span> ${item.descripcion}</div>
            </article>
        `;
    }).join('');
}

prevMonth.addEventListener('click', () => {
    calendarDate.setMonth(calendarDate.getMonth() - 1);
    renderCalendar();
    renderDetail();
});

nextMonth.addEventListener('click', () => {
    calendarDate.setMonth(calendarDate.getMonth() + 1);
    renderCalendar();
    renderDetail();
});

toggleAllReservations.addEventListener('click', () => {
    showAllReservations = !showAllReservations;
    updateToggle();
    renderDetail();
    if (!showAllReservations) {
        renderCalendar();
    }
});

if (reservationSearch) {
    reservationSearch.addEventListener('input', () => {
        renderDetail();
    });
}

if (openFilters) {
    openFilters.addEventListener('click', () => {
        setPopoverOpen(true);
    });
}

if (closeFilters) {
    closeFilters.addEventListener('click', () => {
        setPopoverOpen(false);
    });
}

if (applyFilters) {
    applyFilters.addEventListener('click', () => {
        setPopoverOpen(false);
        renderDetail();
    });
}

if (clearFilters) {
    clearFilters.addEventListener('click', () => {
        if (filterEstado) filterEstado.value = 'todos';
        if (filterTipoRecurso) filterTipoRecurso.value = 'todos';
        if (filterFecha) filterFecha.value = '';
        if (reservationSearch) reservationSearch.value = '';
        renderDetail();
    });
}

renderCalendar();
renderDetail();