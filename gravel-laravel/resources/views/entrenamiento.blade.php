<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrenamiento - GravelExpedition</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    
    <link rel="stylesheet" href="{{ asset('style.css') }}">

    <style>
        .tabla-plan th {
            background-color: #f1f2f6;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #636e72;
        }
        .tabla-plan td, .tabla-plan th { padding: 12px 15px; vertical-align: middle; }
        .dia-hoy { background-color: #fff8f5; border-left: 3px solid #fc4c02; }
        .zona-badge { font-size: 0.75rem; padding: 3px 10px; border-radius: 20px; }
        #chartContainer { position: relative; height: 200px; }
        .empty-state { text-align: center; padding: 60px 20px; color: #636e72; }
        .empty-state i { font-size: 3.5rem; color: #dfe6e9; margin-bottom: 16px; display: block; }
        .empty-state h4 { color: #2d3436; margin-bottom: 8px; }
        .semana-badge {
            font-size: 0.7rem;
            background: #f1f2f6;
            color: #636e72;
            padding: 2px 8px;
            border-radius: 10px;
            margin-left: 6px;
        }
    </style>
</head>
<body class="app-body">

    <header class="main-header">
        <div class="brand">🚴 GravelExpedition</div>
        <div class="user-profile">
            <span>Hola, <strong>{{ Auth::user()->name }}</strong></span>
            <img src="{{ asset('img/usuario.png') }}" alt="Avatar" class="avatar">
        </div>
    </header>

    <div class="layout-container">
        <nav class="main-nav">
            <ul>
                <li><a href="/app"><i class="bi bi-speedometer2 me-2"></i>Tablero</a></li>
                <li><a href="/actividades"><i class="bi bi-list-ul me-2"></i>Mis Actividades</a></li>
                <li><a href="/rutas"><i class="bi bi-map me-2"></i>Mapas / Rutas</a></li>
                <li><a href="/entrenamiento" class="active"><i class="bi bi-heart-pulse me-2"></i>Entrenamiento</a></li>
                <li><a href="/" class="logout" id="btnLogout"><i class="bi bi-box-arrow-left me-2"></i>Cerrar Sesión</a></li>
            </ul>
        </nav>

        <section class="feed-content">
            <h2>💪 Plan Semanal</h2>

            <article class="activity-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="mb-0">Estado de Forma</h3>
                    <span id="badgeCarga" class="badge bg-secondary">Sin datos</span>
                </div>
                <div class="row text-center mb-3">
                    <div class="col"><div class="stat"><span class="label">Fatiga</span><span class="value" id="valFatiga">—</span></div></div>
                    <div class="col"><div class="stat"><span class="label">Forma</span><span class="value" id="valForma">—</span></div></div>
                    <div class="col"><div class="stat"><span class="label">FC Reposo</span><span class="value" id="valFC">—</span></div></div>
                    <div class="col"><div class="stat"><span class="label">FTP</span><span class="value" id="valFTP">—</span></div></div>
                </div>
                <div class="mb-2">
                    <div class="d-flex justify-content-between small text-muted mb-1"><span>Fatiga Acumulada</span><span id="pctFatiga">0%</span></div>
                    <div class="progress-bar"><div class="fill" id="barFatiga" style="width:0%; background:#d63031;"></div></div>
                </div>
                <div class="mb-2">
                    <div class="d-flex justify-content-between small text-muted mb-1"><span>Forma Física</span><span id="pctForma">0%</span></div>
                    <div class="progress-bar"><div class="fill" id="barForma" style="width:0%; background:#00b894;"></div></div>
                </div>
                <div>
                    <div class="d-flex justify-content-between small text-muted mb-1"><span>Recuperación</span><span id="pctRec">0%</span></div>
                    <div class="progress-bar"><div class="fill" id="barRec" style="width:0%; background:#fdcb6e;"></div></div>
                </div>
            </article>

            <article class="activity-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="mb-0">
                        <i class="bi bi-calendar-week me-2"></i>Calendario
                        <span id="labelSemana" class="semana-badge"></span>
                    </h3>
                    <button class="btn btn-sm btn-warning text-white fw-bold" data-bs-toggle="modal" data-bs-target="#modalSesion">
                        <i class="bi bi-plus-circle me-1"></i> Agregar Sesión
                    </button>
                </div>
                <div id="contenidoCalendario"></div>
            </article>

            <article class="activity-card">
                <h3><i class="bi bi-bar-chart-line me-2"></i>Sesiones por Semana — Últimas 4 Semanas</h3>
                <p class="small text-muted mb-2">Cada barra muestra cuántas sesiones registraste esa semana.</p>
                <div id="chartContainer"><canvas id="graficaCarga"></canvas></div>
            </article>
        </section>

        <aside class="sidebar-right">
            <div class="widget">
                <h3><i class="bi bi-bar-chart me-1"></i>Esta Semana</h3>
                <ul class="club-list" id="statsEntrenamiento">
                    <li>📅 Semana: <strong id="numSemana">—</strong></li>
                    <li>🗓️ Sesiones: <strong id="numSesiones">0</strong></li>
                    <li>🔥 Carga: <strong id="nivelCarga">—</strong></li>
                </ul>
            </div>
            <div class="widget">
                <h3><i class="bi bi-heart-pulse me-1"></i>Zonas FC</h3>
                <ul class="club-list" style="font-size:0.85rem">
                    <li>Z1 Recuperación: <strong>&lt;130 bpm</strong></li>
                    <li>Z2 Base aeróbica: <strong>130–148</strong></li>
                    <li>Z3 Tempo: <strong>148–162</strong></li>
                    <li>Z4 Umbral: <strong>162–174</strong></li>
                    <li>Z5 VO2 Max: <strong>&gt;174 bpm</strong></li>
                </ul>
            </div>
            <div class="widget">
                <h3><i class="bi bi-lightbulb me-1"></i>Consejo del día</h3>
                <p class="small text-muted mb-0" id="consejo"></p>
            </div>

            <div class="widget">
                <h3><i class="bi bi-cloud-sun me-1"></i>Clima en Oaxaca</h3>
                <div id="climaWidget">
                    <p class="small text-muted">Cargando clima...</p>
                </div>
            </div>
        </aside>
    </div>

    <footer class="main-footer"><p>&copy; 2026 GravelExpedition</p></footer>

    <div class="modal fade" id="modalSesion" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">➕ Nueva Sesión de Entrenamiento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Día de la semana</label>
                        <select id="diaSesion" class="form-select">
                            <option>Lunes</option><option>Martes</option><option>Miércoles</option>
                            <option>Jueves</option><option>Viernes</option><option>Sábado</option><option>Domingo</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Actividad</label>
                        <input type="text" id="actividadSesion" class="form-control" placeholder="Ej. Intervalos de potencia">
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label class="form-label fw-bold">Zona</label>
                            <select id="zonaSesion" class="form-select">
                                <option value="Z1|bg-success">Zona 1 — Recuperación</option>
                                <option value="Z2|bg-primary">Zona 2 — Base aeróbica</option>
                                <option value="Z3|bg-info text-dark">Zona 3 — Tempo</option>
                                <option value="Z4|bg-warning text-dark">Zona 4 — Umbral</option>
                                <option value="Z5|bg-danger">Zona 5 — VO2 Max</option>
                            </select>
                        </div>
                        <div class="col mb-3">
                            <label class="form-label fw-bold">Duración</label>
                            <input type="text" id="duracionSesion" class="form-control" placeholder="Ej. 1h 30 min">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Fecha <span class="text-muted fw-normal">(para la gráfica semanal)</span></label>
                        <input type="date" id="fechaSesion" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button class="btn-main" style="width:auto;padding:8px 20px;" onclick="agregarSesion()">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // Cerrar sesión redirige a la raíz
        document.getElementById('btnLogout').addEventListener('click', function(e) {
            e.preventDefault();
            window.location.href = '/';
        });

        const hoy = new Date();
        document.getElementById('fechaSesion').value = hoy.toISOString().split('T')[0];

        function obtenerSemana(fecha) {
            const d = new Date(fecha);
            d.setHours(0, 0, 0, 0);
            d.setDate(d.getDate() + 4 - (d.getDay() || 7));
            const yearStart = new Date(d.getFullYear(), 0, 1);
            return Math.ceil((((d - yearStart) / 86400000) + 1) / 7);
        }

        function labelSemana(fecha) {
            const d = new Date(fecha);
            const lunes = new Date(d);
            lunes.setDate(d.getDate() - (d.getDay() === 0 ? 6 : d.getDay() - 1));
            const domingo = new Date(lunes);
            domingo.setDate(lunes.getDate() + 6);
            const fmt = f => f.toLocaleDateString('es-MX', { day: 'numeric', month: 'short' });
            return `${fmt(lunes)} – ${fmt(domingo)}`;
        }

        // ¡Magia! Usamos el ID real de Laravel
        function cargarSesiones() {
            const key = 'ge_sesiones_{{ Auth::user()->id }}';
            return JSON.parse(localStorage.getItem(key) || '[]');
        }

        function guardarSesionesStorage(sesiones) {
            const key = 'ge_sesiones_{{ Auth::user()->id }}';
            localStorage.setItem(key, JSON.stringify(sesiones));
        }

        const semanaActual = obtenerSemana(hoy);
        const anioActual   = hoy.getFullYear();

        function renderCalendario() {
            const sesiones = cargarSesiones();

            const sesionesSemana = sesiones.filter(s => {
                if (!s.fecha || s.fecha === '' || s.fecha === '—') return true; 
                const sem = obtenerSemana(s.fecha);
                const anio = new Date(s.fecha).getFullYear();
                return sem === semanaActual && anio === anioActual;
            });

            document.getElementById('labelSemana').textContent = labelSemana(hoy);
            document.getElementById('numSemana').textContent = 'Semana ' + semanaActual;
            document.getElementById('numSesiones').textContent = sesionesSemana.length;

            const contenido = document.getElementById('contenidoCalendario');

            if (sesionesSemana.length === 0) {
                contenido.innerHTML = `
                    <div class="empty-state">
                        <i class="bi bi-calendar-x"></i>
                        <h4>No hay sesiones esta semana</h4>
                        <p class="text-muted">Agrega tu primera sesión con el botón de arriba.</p>
                    </div>`;
                document.getElementById('badgeCarga').textContent = 'Sin datos';
                document.getElementById('badgeCarga').className = 'badge bg-secondary';
                document.getElementById('nivelCarga').textContent = '—';
            } else {
                const tssZona = { 'Z1': 30, 'Z2': 50, 'Z3': 70, 'Z4': 90, 'Z5': 120 };
                const tssTotal = sesionesSemana.reduce((sum, s) => sum + (tssZona[s.zona] || 50), 0);
                const fatiga = Math.min(Math.round((tssTotal / 500) * 100), 100);
                const forma  = Math.min(Math.round((sesionesSemana.length / 7) * 100), 100);
                const rec    = Math.max(100 - fatiga, 10);

                document.getElementById('valFatiga').textContent = fatiga + '%';
                document.getElementById('valForma').textContent = forma + '%';
                document.getElementById('valFC').textContent = '52 bpm';
                document.getElementById('valFTP').textContent = '245 W';
                document.getElementById('pctFatiga').textContent = fatiga + '%';
                document.getElementById('barFatiga').style.width = fatiga + '%';
                document.getElementById('pctForma').textContent = forma + '%';
                document.getElementById('barForma').style.width = forma + '%';
                document.getElementById('pctRec').textContent = rec + '%';
                document.getElementById('barRec').style.width = rec + '%';

                const nivel = tssTotal < 150 ? 'Baja' : tssTotal < 300 ? 'Moderada' : 'Alta';
                const color = tssTotal < 150 ? 'bg-success' : tssTotal < 300 ? 'bg-warning text-dark' : 'bg-danger';
                document.getElementById('badgeCarga').textContent = 'Carga ' + nivel;
                document.getElementById('badgeCarga').className = 'badge ' + color;
                document.getElementById('nivelCarga').textContent = nivel;

                const diasOrden = ['Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo'];
                const sorted = [...sesionesSemana].sort((a, b) =>
                    diasOrden.indexOf(a.dia) - diasOrden.indexOf(b.dia));

                contenido.innerHTML = `
                    <div class="table-responsive">
                        <table class="table tabla-plan table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Día</th><th>Actividad</th><th>Zona</th>
                                    <th>Duración</th><th>Fecha</th><th></th>
                                </tr>
                            </thead>
                            <tbody>
                                ${sorted.map((s, i) => {
                                    const esHoy = s.fecha === hoy.toISOString().split('T')[0];
                                    const fechaFmt = s.fecha
                                        ? new Date(s.fecha + 'T12:00:00').toLocaleDateString('es-MX', { day: 'numeric', month: 'short' })
                                        : '—';
                                    return `
                                    <tr class="${esHoy ? 'dia-hoy' : ''}">
                                        <td><strong>${s.dia}</strong>${esHoy ? ' <span class="badge bg-warning text-dark" style="font-size:0.65rem">Hoy</span>' : ''}</td>
                                        <td>${s.actividad}</td>
                                        <td><span class="badge ${s.badge} zona-badge">${s.zona}</span></td>
                                        <td>${s.duracion}</td>
                                        <td><small class="text-muted">${fechaFmt}</small></td>
                                        <td><button class="btn btn-sm btn-outline-danger" onclick="eliminarSesion('${s.id}')"><i class="bi bi-trash"></i></button></td>
                                    </tr>`;
                                }).join('')}
                            </tbody>
                        </table>
                    </div>`;
            }

            actualizarGrafica(sesiones);
        }

        function agregarSesion() {
            const dia       = document.getElementById('diaSesion').value;
            const actividad = document.getElementById('actividadSesion').value.trim();
            const zonaRaw   = document.getElementById('zonaSesion').value.split('|');
            const duracion  = document.getElementById('duracionSesion').value.trim();
            const fecha     = document.getElementById('fechaSesion').value;

            if (!actividad || !duracion) { alert('Por favor completa la actividad y la duración.'); return; }
            if (!fecha) { alert('Por favor selecciona una fecha.'); return; }

            const sesiones = cargarSesiones();
            sesiones.push({
                id: Date.now().toString(),
                dia,
                actividad,
                zona: zonaRaw[0],
                badge: zonaRaw[1],
                duracion,
                fecha  
            });
            guardarSesionesStorage(sesiones);
            renderCalendario();

            bootstrap.Modal.getInstance(document.getElementById('modalSesion')).hide();
            document.getElementById('actividadSesion').value = '';
            document.getElementById('duracionSesion').value = '';
            document.getElementById('fechaSesion').value = hoy.toISOString().split('T')[0];
        }

        function eliminarSesion(id) {
            if (!confirm('¿Eliminar esta sesión?')) return;
            let sesiones = cargarSesiones();
            sesiones = sesiones.filter(s => s.id !== id);
            guardarSesionesStorage(sesiones);
            renderCalendario();
        }

        let graficaChart = null;

        function actualizarGrafica(sesiones) {
            const semanas = [];
            for (let i = 3; i >= 0; i--) {
                const d = new Date(hoy);
                d.setDate(hoy.getDate() - i * 7);
                semanas.push({
                    num: obtenerSemana(d),
                    anio: d.getFullYear(),
                    label: 'Sem ' + obtenerSemana(d) + '\n' + labelSemana(d)
                });
            }

            const conteos = semanas.map(s =>
                sesiones.filter(ses => {
                    if (!ses.fecha) return false;
                    return obtenerSemana(ses.fecha) === s.num &&
                           new Date(ses.fecha).getFullYear() === s.anio;
                }).length
            );

            const labels = semanas.map(s => labelSemana(
                (() => { const d = new Date(hoy); d.setDate(hoy.getDate() - (semanas.indexOf(s)) * 7); return d; })()
            ));

            const ctx = document.getElementById('graficaCarga').getContext('2d');

            if (graficaChart) graficaChart.destroy();

            graficaChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: semanas.map((s, i) => 'Semana ' + s.num),
                    datasets: [{
                        label: 'Sesiones',
                        data: conteos,
                        backgroundColor: conteos.map((v, i) =>
                            i === 3 ? '#fc4c02' : '#fc4c0260'),
                        borderRadius: 6,
                        borderSkipped: false
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                title: (items) => {
                                    const idx = items[0].dataIndex;
                                    return labels[idx];
                                },
                                label: ctx => ` ${ctx.parsed.y} sesión(es)`
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1, color: '#636e72' },
                            grid: { color: '#f0f0f0' }
                        },
                        x: { grid: { display: false }, ticks: { color: '#636e72' } }
                    }
                }
            });
        }

        const consejos = [
            'La Zona 2 mejora tu base aeróbica y es clave para el gravel de larga distancia.',
            'Descansa bien: el cuerpo mejora durante la recuperación, no durante el esfuerzo.',
            'Hidrátate antes, durante y después de cada rodada.',
            'El VO2 Max se entrena con series cortas a máxima intensidad.',
            'Incorpora trabajo de fuerza en subidas para mejorar tu potencia.'
        ];
        document.getElementById('consejo').textContent = consejos[Math.floor(Math.random() * consejos.length)];

        renderCalendario();
    </script>

    <script src="{{ asset('clima.js') }}"></script>
</body>
</html>