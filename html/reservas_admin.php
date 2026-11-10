<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../includes/config.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'admin') {
    header('Location: Inicio_sesion.php');
    exit();
}

$estadisticas = [
    'pendiente' => 0,
    'aprobada' => 0,
    'rechazada' => 0,
    'cancelada' => 0,
];

$reservasStmt = $pdo->query(
    "SELECT
        r.id,
        r.id_usuario,
        r.fecha,
        r.hora_inicio,
        r.hora_fin,
        r.estado,
        r.motivo_profesor,
        r.motivo_rechazo,
        u.nombre AS profesor_nombre,
        u.email AS usuario_email,
        rec.nombre AS recurso_nombre,
        rec.tipo AS recurso_tipo
    FROM reservas r
    LEFT JOIN usuarios u ON u.id = r.id_usuario
    LEFT JOIN recursos rec ON rec.id = r.id_recurso
    ORDER BY r.fecha ASC, r.hora_inicio ASC, r.id ASC"
);

$reservas = $reservasStmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($reservas as $reserva) {
    $estado = strtolower((string)($reserva['estado'] ?? 'pendiente'));
    if (isset($estadisticas[$estado])) {
        $estadisticas[$estado]++;
    }
}

$actividadPorFecha = [];
$reservasListado = [];
$gruposReservas = [];

foreach ($reservas as $reserva) {
    $fecha = trim((string)($reserva['fecha'] ?? ''));
    if ($fecha === '') {
        continue;
    }

    $tipoRecurso = strtolower((string)($reserva['recurso_tipo'] ?? 'sala'));
    $titulo = trim((string)($reserva['recurso_nombre'] ?? 'Recurso'));
    $descripcion = trim((string)($reserva['motivo_profesor'] ?? ''));
    $profesor = trim((string)($reserva['profesor_nombre'] ?? 'Profesor'));
    $estado = strtolower((string)($reserva['estado'] ?? 'pendiente'));
    $idReserva = (int)($reserva['id'] ?? 0);
    $horaInicio = trim((string)($reserva['hora_inicio'] ?? ''));
    $horaFin = trim((string)($reserva['hora_fin'] ?? ''));
    $horaSesion = $horaInicio !== '' || $horaFin !== '' ? $horaInicio . ' - ' . $horaFin : 'Horario no disponible';
    $claveGrupo = implode('|', [$fecha, $profesor, $titulo, $descripcion, $estado, $tipoRecurso]);

    if (!isset($gruposReservas[$claveGrupo])) {
        $gruposReservas[$claveGrupo] = [
            'id' => $idReserva,
            'ids' => [],
            'horarios' => [],
            'fecha' => $fecha,
            'fecha_formateada' => date('d/m/Y', strtotime($fecha)),
            'hora_inicio' => $horaInicio,
            'hora_fin' => $horaFin,
            'horario' => $horaSesion,
            'titulo' => $titulo !== '' ? $titulo : 'Reserva de recurso',
            'recurso' => $titulo !== '' ? $titulo : 'Recurso',
            'profesor' => $profesor !== '' ? $profesor : 'Profesor',
            'descripcion' => $descripcion !== '' ? $descripcion : 'Sin descripción cargada.',
            'estado' => $estado,
            'tipo_recurso' => $tipoRecurso,
            'usuario_email' => trim((string)($reserva['usuario_email'] ?? '')),
            'motivo_rechazo' => trim((string)($reserva['motivo_rechazo'] ?? '')),
        ];
    }

    if ($idReserva > 0) {
        $gruposReservas[$claveGrupo]['ids'][] = $idReserva;
    }

    $gruposReservas[$claveGrupo]['horarios'][] = [
        'id' => $idReserva,
        'hora_inicio' => $horaInicio,
        'hora_fin' => $horaFin,
        'label' => $horaSesion,
    ];

    if (!isset($actividadPorFecha[$fecha])) {
        $actividadPorFecha[$fecha] = [];
    }
    $actividadPorFecha[$fecha][] = $gruposReservas[$claveGrupo];
}

$reservasListado = array_values($gruposReservas);
foreach ($reservasListado as $index => $item) {
    $reservasListado[$index]['ids'] = array_values(array_unique(array_map('intval', $item['ids'] ?? [])));
    $reservasListado[$index]['horarios'] = array_values(array_filter($item['horarios'] ?? [], fn($bloque) => is_array($bloque)));
}

$fechaInicial = $reservas[0]['fecha'] ?? date('Y-m-d');
$anioInicial = (int)date('Y', strtotime($fechaInicial));
$mesInicial = (int)date('n', strtotime($fechaInicial));
$actividadJson = json_encode($actividadPorFecha, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
$reservasJson = json_encode($reservasListado, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de reservas</title>
    <link rel="stylesheet" href="../css/style_reservas_admin.css">
    <link rel="stylesheet" href="../css/style_reservas_profesor.css">
    <link rel="stylesheet" href="../css/style_calendario.css">
</head>
<body>
    <?php require_once __DIR__ . '/../includes/barra_admin.php'; ?>

    <main class="calendar-page">
        <section class="estadisticas" aria-label="Resumen administrativo">
            <div class="tarjeta">
                <span class="numero"><?php echo (int)array_sum($estadisticas); ?></span>
                <span>Total</span>
            </div>
            <div class="tarjeta confirmada">
                <span class="numero"><?php echo (int)($estadisticas['aprobada'] ?? 0); ?></span>
                <span>Aprobadas</span>
            </div>
            <div class="tarjeta pendiente">
                <span class="numero"><?php echo (int)($estadisticas['pendiente'] ?? 0); ?></span>
                <span>Pendientes</span>
            </div>
            <div class="tarjeta cancelada">
                <span class="numero"><?php echo (int)($estadisticas['rechazada'] + $estadisticas['cancelada']); ?></span>
                <span>Rechazadas/Canceladas</span>
            </div>
        </section>

        <div class="calendar-layout">
            <section class="calendar-panel">
                <div class="calendar-toolbar">
                    <button type="button" class="month-nav" id="prevMonth" aria-label="Mes anterior">‹</button>
                    <span id="monthLabel">Mes</span>
                    <div class="calendar-toolbar-actions">
                        <button type="button" class="btn-resumen" id="summaryBtn">Resumen</button>
                        <button type="button" class="month-nav" id="nextMonth" aria-label="Mes siguiente">›</button>
                    </div>
                </div>
                <div class="calendar-grid" id="calendarGrid" aria-live="polite"></div>
            </section>

            <aside class="detail-panel">
                <div class="detail-toolbar">
                    <div class="detail-toggle-switch" aria-label="Cambiar vista de reservas">
                        <span class="toggle-label">Día</span>
                        <button type="button" id="toggleAllReservations" class="detail-toggle" aria-pressed="false">
                            <span class="toggle-knob"></span>
                        </button>
                        <span class="toggle-label">Todas</span>
                        <span class="toggle-help" tabindex="0" aria-label="Ayuda sobre el interruptor de vista">?</span>
                        <span class="toggle-help-tooltip">Usá este interruptor para ver el día seleccionado o todas las reservas del calendario.</span>
                    </div>
                    <button type="button" id="denyDayButton" class="filter-secondary" aria-label="Denegar todas las reservas del día seleccionado">
                        Denegar día completo
                    </button>

                    <div class="detail-search-wrap">
                        <div class="search-with-help">
                            <input id="reservationSearch" type="search" placeholder="Buscar por fecha, profesor o recurso..." aria-label="Buscar reservas">
                            <span class="search-help" tabindex="0" aria-label="Ayuda sobre búsqueda">?</span>
                            <span class="search-help-tooltip">Buscá por fecha con formato dia/mes/año o por profesor/recurso. La búsqueda se aplica automáticamente.</span>
                        </div>
                        <button type="button" id="openFilters" class="filter-button" aria-label="Abrir filtros">Filtros</button>
                    </div>
                </div>

                <div id="filterPopover" class="filter-popover" aria-hidden="true">
                    <div class="filter-popover-header">
                        <h3>Filtrar reservas</h3>
                        <button type="button" id="closeFilters" class="filter-close" aria-label="Cerrar filtros">×</button>
                    </div>
                    <div class="filter-grid">
                        <label>
                            <span>Estado</span>
                            <select id="filterEstado">
                                <option value="todos">Todos</option>
                                <option value="pendiente">Pendiente</option>
                                <option value="aprobada">Aprobada</option>
                                <option value="rechazada">Rechazada</option>
                                <option value="cancelada">Cancelada</option>
                            </select>
                        </label>
                        <label>
                            <span>Tipo de recurso</span>
                            <select id="filterTipoRecurso">
                                <option value="todos">Todos</option>
                                <option value="sala">Sala / Laboratorio</option>
                                <option value="equipo">Equipo</option>
                            </select>
                        </label>
                        <label>
                            <span>Fecha específica</span>
                            <input id="filterFecha" type="date">
                        </label>
                    </div>
                    <div class="filter-actions">
                        <button type="button" id="clearFilters" class="filter-secondary">Limpiar</button>
                        <button type="button" id="applyFilters" class="filter-primary">Aplicar</button>
                    </div>
                </div>

                <div class="detail-header" id="detailHeader">Fecha y día</div>
                <div class="detail-list" id="detailList"></div>
            </aside>
        </div>
    </main>

    <div id="rejectModal" class="reject-modal" aria-hidden="true">
        <div class="reject-modal-content">
            <div class="reject-modal-header">
                <h3>Rechazar reserva</h3>
                <button type="button" id="closeRejectModal" class="reject-close" aria-label="Cerrar rechazo">×</button>
            </div>
            <form id="rejectForm" method="POST" action="procesar_estado_reserva.php">
                <input type="hidden" id="rejectIdReserva" name="id_reserva" value="">
                <input type="hidden" name="estado" value="rechazada">
                <input type="hidden" name="redirect_to" value="reservas_admin.php">
                <label for="motivoRechazo">Motivo del rechazo</label>
                <textarea id="motivoRechazo" name="motivo_rechazo" required placeholder="Detallá la razón del rechazo..."></textarea>
                <div class="reject-actions">
                    <button type="button" class="filter-secondary" id="cancelRejectModal">Cancelar</button>
                    <button type="submit" class="reject-confirm">Confirmar rechazo</button>
                </div>
            </form>
        </div>
    </div>

    <form id="denyDayForm" method="POST" action="procesar_estado_reserva.php" style="display: none;">
        <input type="hidden" name="id_reserva" id="denyDayIds" value="">
        <input type="hidden" name="estado" value="rechazada">
        <input type="hidden" name="motivo_rechazo" id="denyDayReason" value="">
        <input type="hidden" name="redirect_to" value="reservas_admin.php">
    </form>

    <script>
        const activityMap = <?php echo $actividadJson ?: '{}'; ?>;
        const allReservations = <?php echo $reservasJson ?: '[]'; ?>;

        const calendarDate = new Date(<?php echo $anioInicial; ?>, <?php echo $mesInicial - 1; ?>, 1);
        const monthLabel = document.getElementById('monthLabel');
        const calendarGrid = document.getElementById('calendarGrid');
        const detailHeader = document.getElementById('detailHeader');
        const detailList = document.getElementById('detailList');
        const toggleAllReservations = document.getElementById('toggleAllReservations');
        const reservationSearch = document.getElementById('reservationSearch');
        const filterPopover = document.getElementById('filterPopover');
        const summaryBtn = document.getElementById('summaryBtn');
        const filterEstado = document.getElementById('filterEstado');
        const filterTipoRecurso = document.getElementById('filterTipoRecurso');
        const filterFecha = document.getElementById('filterFecha');
        const rejectModal = document.getElementById('rejectModal');
        const denyDayButton = document.getElementById('denyDayButton');
        const denyDayForm = document.getElementById('denyDayForm');
        const denyDayIds = document.getElementById('denyDayIds');
        const denyDayReason = document.getElementById('denyDayReason');

        let selectedDate = Object.keys(activityMap)[0] || formatDateKey(new Date());
        let showAllReservations = false;

        const weekdayNames = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];
        const monthNames = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

        function escapeHtml(value) {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        }

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

        function getDayBadgeState(dateKey) {
            const items = Array.isArray(activityMap[dateKey]) ? activityMap[dateKey] : [];
            const estados = items
                .map((item) => String(item.estado || '').toLowerCase())
                .filter(Boolean);

            if (!estados.length || estados.every((estado) => ['cancelada', 'rechazada'].includes(estado))) {
                return { badges: [] };
            }

            const badges = [];
            if (estados.includes('pendiente')) {
                badges.push({ label: 'Solicitud', className: 'pending' });
            }
            if (estados.includes('aprobada')) {
                badges.push({ label: 'Reservado', className: 'reserved' });
            }

            return { badges };
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
                const variants = [
                    item.fecha || '',
                    item.fecha_formateada || '',
                    item.recurso || '',
                    item.profesor || '',
                    item.descripcion || '',
                    item.horario || '',
                    item.titulo || '',
                    item.usuario_email || ''
                ].flatMap((value) => {
                    const text = String(value).toLowerCase();
                    return [text, text.replace(/\//g, '-'), text.replace(/-/g, '/')];
                });

                if (!variants.some((value) => value.includes(filters.query))) {
                    return false;
                }
            }

            if (filters.estado !== 'todos' && (item.estado || '').toLowerCase() !== filters.estado.toLowerCase()) {
                return false;
            }

            if (filters.tipo !== 'todos') {
                const tipo = String(item.tipo_recurso || '').toLowerCase();
                const esperado = filters.tipo.toLowerCase();
                const normalizado = tipo === 'laboratorio' || tipo === 'sala' ? 'sala' : tipo;
                if (normalizado !== esperado) {
                    return false;
                }
            }

            if (filters.fecha && item.fecha !== filters.fecha) {
                return false;
            }

            return true;
        }

        function getFilteredReservations() {
            const filters = getFilterState();
            const items = showAllReservations ? allReservations : (activityMap[selectedDate] || []);
            return items.filter((item) => matchesReservationFilters(item, filters));
        }

        function renderCalendar() {
            calendarGrid.innerHTML = '';

            weekdayNames.forEach((name) => {
                const cell = document.createElement('div');
                cell.className = 'weekday';
                cell.textContent = name;
                calendarGrid.appendChild(cell);
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
                const key = formatDateKey(date);
                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'day-cell';

                if (key === selectedDate) {
                    button.classList.add('selected');
                }

                const dayItems = Array.isArray(activityMap[key]) ? activityMap[key] : [];
                const activeItems = dayItems.filter((item) => {
                    const estado = String(item.estado || '').toLowerCase();
                    return estado === 'pendiente' || estado === 'aprobada';
                });
                const hasPending = activeItems.some((item) => String(item.estado || '').toLowerCase() === 'pendiente');
                const hasApproved = activeItems.some((item) => String(item.estado || '').toLowerCase() === 'aprobada');
                const hasActivity = activeItems.length > 0;
                if (hasActivity) {
                    button.classList.add('has-activity');
                }

                const badges = [];
                if (hasPending) {
                    badges.push('<span class="day-badge day-badge-warning">Solicitud</span>');
                }
                if (hasApproved) {
                    badges.push('<span class="day-badge day-badge-success">Reservado</span>');
                }

                button.innerHTML = `
                    <span class="day-number">${day}</span>
                    ${hasActivity ? `<span class="day-badge-stack">${badges.join('')}</span>` : '<span class="day-empty">Libre</span>'}
                `;

                button.addEventListener('click', () => {
                    selectedDate = key;
                    showAllReservations = false;
                    updateToggleState();
                    renderCalendar();
                    renderDetail();
                });

                calendarGrid.appendChild(button);
            }

            const totalCells = calendarGrid.children.length;
            const remainder = (7 - (totalCells % 7)) % 7;
            for (let i = 0; i < remainder; i++) {
                const filler = document.createElement('div');
                filler.className = 'day-cell empty';
                calendarGrid.appendChild(filler);
            }
        }

        function renderDetail() {
            const filteredItems = getFilteredReservations();
            detailHeader.textContent = showAllReservations ? 'Todas las reservas' : formatLongDate(selectedDate).replace(/^./, (char) => char.toUpperCase());

            if (!filteredItems.length) {
                detailList.innerHTML = `
                    <div class="detail-empty">
                        <p>${showAllReservations ? 'No hay reservas que coincidan con los filtros aplicados.' : 'No hay reservas registradas para este día.'}</p>
                    </div>
                `;
                return;
            }

            detailList.innerHTML = filteredItems.map((item) => {
                const ids = Array.isArray(item.ids) && item.ids.length ? item.ids : [item.id].filter(Boolean);
                const estadoLabel = item.estado.charAt(0).toUpperCase() + item.estado.slice(1);
                const estadoClass = item.estado === 'aprobada' ? 'confirmada' : item.estado;
                const tipo = item.tipo_recurso === 'equipo' ? 'Equipo' : 'Sala / Laboratorio';
                const motivo = item.motivo_rechazo ? `<div class="activity-line"><span>Motivo rechazo:</span> ${escapeHtml(item.motivo_rechazo)}</div>` : '';
                const horarios = Array.isArray(item.horarios) && item.horarios.length ? item.horarios : [{ label: item.horario || 'Horario no disponible' }];
                const horarioHTML = horarios.length > 1
                    ? `<details class="reservation-hours-menu" open>
                        <summary>Horarios (${horarios.length})</summary>
                        <ul>
                            ${horarios.map((bloque) => `<li>${escapeHtml(bloque.label || `${bloque.hora_inicio || ''} - ${bloque.hora_fin || ''}`)}</li>`).join('')}
                        </ul>
                    </details>`
                    : `<div class="activity-line"><span>Horario:</span> ${escapeHtml(horarios[0]?.label || item.horario || '')}</div>`;

                return `
                    <article class="activity-card">
                        <div class="activity-card-header">
                            <div>
                                <div class="activity-title">${escapeHtml(item.titulo || 'Reserva')}</div>
                                <div class="activity-meta">${escapeHtml(tipo)}</div>
                            </div>
                            <span class="status-pill ${estadoClass}">${escapeHtml(estadoLabel)}</span>
                        </div>
                        <div class="activity-lines">
                            <div class="activity-line"><span>Profesor:</span> ${escapeHtml(item.profesor || 'Profesor')}</div>
                            <div class="activity-line"><span>Recurso:</span> ${escapeHtml(item.recurso || item.titulo || 'Recurso')}</div>
                            <div class="activity-line"><span>Fecha:</span> ${escapeHtml(item.fecha_formateada || item.fecha || '')}</div>
                            ${horarioHTML}
                            <div class="activity-line"><span>Descripción:</span> ${escapeHtml(item.descripcion || 'Sin descripción cargada.')}</div>
                            ${motivo}
                        </div>
                        <div class="activity-actions">
                            <form method="POST" action="procesar_estado_reserva.php" class="inline-action-form">
                                <input type="hidden" name="id_reserva" value="${escapeHtml(ids.join(','))}">
                                <input type="hidden" name="estado" value="aprobada">
                                <input type="hidden" name="redirect_to" value="reservas_admin.php">
                                <button type="submit" class="mini-btn approve-btn">Aprobar reserva</button>
                            </form>
                            <button type="button" class="mini-btn reject-btn" data-ids="${escapeHtml(ids.join(','))}">Rechazar reserva</button>
                        </div>
                    </article>
                `;
            }).join('');

            document.querySelectorAll('.reject-btn').forEach((button) => {
                button.addEventListener('click', () => {
                    const ids = button.dataset.ids || '';
                    document.getElementById('rejectIdReserva').value = ids;
                    rejectModal.classList.add('open');
                    rejectModal.setAttribute('aria-hidden', 'false');
                    rejectModal.style.display = 'flex';
                });
            });
        }

        function updateToggleState() {
            if (!toggleAllReservations) return;
            toggleAllReservations.classList.toggle('active', showAllReservations);
            toggleAllReservations.setAttribute('aria-pressed', String(showAllReservations));
        }

        function toggleFilters() {
            if (!filterPopover) return;
            const isOpen = filterPopover.classList.contains('open');
            filterPopover.classList.toggle('open', !isOpen);
            filterPopover.setAttribute('aria-hidden', String(isOpen));
        }

        function clearFilterForm() {
            if (filterEstado) filterEstado.value = 'todos';
            if (filterTipoRecurso) filterTipoRecurso.value = 'todos';
            if (filterFecha) filterFecha.value = '';
            if (reservationSearch) reservationSearch.value = '';
        }

        function getReservationIdsForDay(dateKey) {
            const ids = new Set();
            allReservations
                .filter((item) => {
                    if (item.fecha !== dateKey) return false;
                    const estado = String(item.estado || '').toLowerCase();
                    return estado === 'pendiente' || estado === 'aprobada';
                })
                .forEach((item) => {
                    const idsItem = Array.isArray(item.ids) && item.ids.length
                        ? item.ids
                        : [item.id].filter(Boolean);

                    idsItem.forEach((id) => {
                        const n = Number(id);
                        if (Number.isInteger(n) && n > 0) {
                            ids.add(n);
                        }
                    });
                });

            return Array.from(ids);
        }

        function denySelectedDayReservations() {
            if (!selectedDate) {
                window.alert('Seleccioná un día del calendario primero.');
                return;
            }

            const ids = getReservationIdsForDay(selectedDate);
            if (!ids.length) {
                window.alert('No hay reservas activas para denegar en este día.');
                return;
            }

            const fechaTexto = formatLongDate(selectedDate).replace(/^./, (char) => char.toUpperCase());
            const confirmar = window.confirm(`¿Seguro que querés denegar TODAS las reservas del día ${fechaTexto}?`);
            if (!confirmar) {
                return;
            }

            const motivo = window.prompt('Ingresá la razón del rechazo para todas las reservas de este día:');
            if (motivo === null) {
                return;
            }

            const motivoLimpio = motivo.trim();
            if (!motivoLimpio) {
                window.alert('La razón del rechazo es obligatoria.');
                return;
            }

            if (!denyDayForm || !denyDayIds || !denyDayReason) {
                window.alert('No se pudo preparar la denegación masiva.');
                return;
            }

            denyDayIds.value = ids.join(',');
            denyDayReason.value = motivoLimpio;
            denyDayForm.submit();
        }

        document.getElementById('prevMonth').addEventListener('click', () => {
            calendarDate.setMonth(calendarDate.getMonth() - 1);
            const nextDate = new Date(calendarDate.getFullYear(), calendarDate.getMonth(), 1);
            selectedDate = formatDateKey(nextDate);
            renderCalendar();
            renderDetail();
        });

        document.getElementById('nextMonth').addEventListener('click', () => {
            calendarDate.setMonth(calendarDate.getMonth() + 1);
            const nextDate = new Date(calendarDate.getFullYear(), calendarDate.getMonth(), 1);
            selectedDate = formatDateKey(nextDate);
            renderCalendar();
            renderDetail();
        });

        if (toggleAllReservations) {
            toggleAllReservations.addEventListener('click', () => {
                showAllReservations = !showAllReservations;
                updateToggleState();
                renderDetail();
            });
        }

        if (summaryBtn) {
            summaryBtn.addEventListener('click', () => {
                showAllReservations = true;
                updateToggleState();
                renderDetail();
            });
        }

        if (reservationSearch) {
            reservationSearch.addEventListener('input', renderDetail);
        }

        document.getElementById('openFilters').addEventListener('click', toggleFilters);
        document.getElementById('closeFilters').addEventListener('click', toggleFilters);

        document.getElementById('applyFilters').addEventListener('click', () => {
            renderDetail();
            toggleFilters();
        });

        document.getElementById('clearFilters').addEventListener('click', () => {
            clearFilterForm();
            renderDetail();
            toggleFilters();
        });

        if (denyDayButton) {
            denyDayButton.addEventListener('click', denySelectedDayReservations);
        }

        document.getElementById('closeRejectModal').addEventListener('click', () => {
            rejectModal.style.display = 'none';
            rejectModal.setAttribute('aria-hidden', 'true');
            document.getElementById('rejectIdReserva').value = '';
            document.getElementById('motivoRechazo').value = '';
        });

        document.getElementById('cancelRejectModal').addEventListener('click', () => {
            rejectModal.style.display = 'none';
            rejectModal.setAttribute('aria-hidden', 'true');
            document.getElementById('rejectIdReserva').value = '';
            document.getElementById('motivoRechazo').value = '';
        });

        rejectModal.addEventListener('click', (event) => {
            if (event.target === rejectModal) {
                rejectModal.style.display = 'none';
                rejectModal.setAttribute('aria-hidden', 'true');
                document.getElementById('rejectIdReserva').value = '';
                document.getElementById('motivoRechazo').value = '';
            }
        });

        renderCalendar();
        renderDetail();
        updateToggleState();
    </script>
</body>
</html>
