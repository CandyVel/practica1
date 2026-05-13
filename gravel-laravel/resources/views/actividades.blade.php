<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actividades - GravelExpedition</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    
    <link rel="stylesheet" href="{{ asset('style.css') }}">

    <style>
        .empty-state { text-align: center; padding: 60px 20px; color: #636e72; }
        .empty-state i { font-size: 3.5rem; color: #dfe6e9; margin-bottom: 16px; display: block; }
        .empty-state h4 { color: #2d3436; margin-bottom: 8px; }
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
                <li><a href="/actividades" class="active"><i class="bi bi-list-ul me-2"></i>Mis Actividades</a></li>
                <li><a href="/rutas"><i class="bi bi-map me-2"></i>Mapas / Rutas</a></li>
                <li><a href="/entrenamiento"><i class="bi bi-heart-pulse me-2"></i>Entrenamiento</a></li>
                <li><a href="/" class="logout" id="btnLogout"><i class="bi bi-box-arrow-left me-2"></i>Cerrar Sesión</a></li>
            </ul>
        </nav>

        <section class="feed-content">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="mb-0">📜 Historial de Actividades</h2>
                <button class="btn btn-sm btn-warning text-white fw-bold" data-bs-toggle="modal" data-bs-target="#modalActividad">
                    <i class="bi bi-plus-circle me-1"></i> Nueva Actividad
                </button>
            </div>

            <div id="filtros" class="mb-3 d-flex gap-2 flex-wrap d-none">
                <button class="btn btn-sm btn-outline-secondary active" onclick="filtrar('todas', this)">Todas</button>
                <button class="btn btn-sm btn-outline-secondary" onclick="filtrar('corta', this)">Cortas (&lt;40km)</button>
                <button class="btn btn-sm btn-outline-secondary" onclick="filtrar('larga', this)">Largas (&gt;40km)</button>
            </div>

            <div id="listaActividades"></div>

            <div id="sinActividades" class="alert alert-info d-none text-center">
                No hay actividades para este filtro.
            </div>
        </section>

        <aside class="sidebar-right">
            <div class="widget">
                <h3><i class="bi bi-trophy me-1"></i>Total Anual</h3>
                <p class="fs-5 fw-bold mb-1" id="totalKm">0 km</p>
                <p class="text-muted small">recorridos este año</p>
                <div class="progress-bar">
                    <div class="fill" id="barAnual" style="width: 0%"></div>
                </div>
                <small class="text-muted" id="pctMeta">0% de tu meta (2,000 km)</small>
            </div>
            <div class="widget">
                <h3><i class="bi bi-bar-chart me-1"></i>Resumen</h3>
                <ul class="club-list">
                    <li>🗓️ Actividades: <strong id="resActividades">0</strong></li>
                    <li>⏱️ Tiempo total: <strong id="resTiempo">0h 0min</strong></li>
                    <li>📅 Última salida: <strong id="resUltima">—</strong></li>
                </ul>
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

    <div class="modal fade" id="modalActividad" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">➕ Nueva Actividad</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nombre de la Actividad</label>
                        <input type="text" id="nombreAct" class="form-control" placeholder="Ej. Rodada matutina">
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label class="form-label fw-bold">Distancia (km)</label>
                            <input type="number" id="kmAct" class="form-control" placeholder="Ej. 45" min="0">
                        </div>
                        <div class="col mb-3">
                            <label class="form-label fw-bold">Fecha</label>
                            <input type="date" id="fechaAct" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label class="form-label fw-bold">Horas</label>
                            <input type="number" id="horasAct" class="form-control" placeholder="Ej. 2" min="0" max="24">
                        </div>
                        <div class="col mb-3">
                            <label class="form-label fw-bold">Minutos</label>
                            <input type="number" id="minutosAct" class="form-control" placeholder="Ej. 30" min="0" max="59">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Descripción</label>
                        <textarea id="descAct" class="form-control" rows="2" placeholder="Notas de la salida..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn-main" style="width:auto; padding: 8px 20px;" id="btnGuardar">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Cerrar sesión redirige a la raíz
        document.getElementById('btnLogout').addEventListener('click', function(e) {
            e.preventDefault();
            window.location.href = '/';
        });

        const hoy = new Date().toISOString().split('T')[0];
        document.getElementById('fechaAct').value = hoy;

        // ¡Magia! Usamos el ID real de Laravel para guardar los datos localmente
        const KEY = 'ge_actividades_{{ Auth::user()->id }}';

        function cargarActividades() {
            return JSON.parse(localStorage.getItem(KEY) || '[]');
        }

        function guardarActividades(acts) {
            localStorage.setItem(KEY, JSON.stringify(acts));
        }

        function formatTiempo(minutos) {
            if (!minutos || minutos === 0) return '—';
            const h = Math.floor(minutos / 60);
            const m = minutos % 60;
            if (h === 0) return m + ' min';
            if (m === 0) return h + 'h';
            return h + 'h ' + m + 'min';
        }

        function renderActividades() {
            const acts = cargarActividades();
            const lista = document.getElementById('listaActividades');
            const filtros = document.getElementById('filtros');

            const totalKm = acts.reduce((s, a) => s + parseFloat(a.km || 0), 0);
            const totalMin = acts.reduce((s, a) => s + parseInt(a.minutos || 0), 0);
            const pct = Math.min(Math.round((totalKm / 2000) * 100), 100);

            document.getElementById('totalKm').textContent = totalKm.toFixed(1) + ' km';
            document.getElementById('barAnual').style.width = pct + '%';
            document.getElementById('pctMeta').textContent = pct + '% de tu meta (2,000 km)';
            document.getElementById('resActividades').textContent = acts.length;
            document.getElementById('resTiempo').textContent = formatTiempo(totalMin);
            document.getElementById('resUltima').textContent = acts.length > 0 ? acts[0].fecha : '—';

            if (acts.length === 0) {
                filtros.classList.add('d-none');
                lista.innerHTML = `
                    <article class="activity-card">
                        <div class="empty-state">
                            <i class="bi bi-bicycle"></i>
                            <h4>Aún no tienes actividades</h4>
                            <p class="text-muted">Registra tu primera salida y empieza a construir tu historial.</p>
                            <button class="btn-main" style="width:auto;padding:10px 24px;"
                                data-bs-toggle="modal" data-bs-target="#modalActividad">
                                <i class="bi bi-plus-circle me-2"></i>Agregar primera actividad
                            </button>
                        </div>
                    </article>`;
                return;
            }

            filtros.classList.remove('d-none');

            lista.innerHTML = acts.map(a => {
                const km = parseFloat(a.km || 0);
                const badge = km >= 40
                    ? `<span class="badge bg-danger">${a.km} km</span>`
                    : `<span class="badge bg-warning text-dark">${a.km} km</span>`;

                return `
                    <article class="activity-card" data-km="${a.km}">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h4 class="mb-1">${a.nombre}</h4>
                                <p class="date mb-1">
                                    <i class="bi bi-calendar3 me-1"></i>${a.fecha}
                                    &bull; <strong>${a.km} km</strong>
                                    ${a.minutos ? `&bull; <i class="bi bi-clock me-1"></i><strong>${formatTiempo(a.minutos)}</strong>` : ''}
                                </p>
                                <p class="mb-0 text-muted">${a.desc || 'Sin descripción.'}</p>
                            </div>
                            ${badge}
                        </div>
                    </article>`;
            }).join('');
        }

        document.getElementById('btnGuardar').addEventListener('click', function() {
            const nombre  = document.getElementById('nombreAct').value.trim();
            const km      = document.getElementById('kmAct').value.trim();
            const fecha   = document.getElementById('fechaAct').value;
            const horas   = parseInt(document.getElementById('horasAct').value || 0);
            const minutos = parseInt(document.getElementById('minutosAct').value || 0);
            const desc    = document.getElementById('descAct').value.trim();

            if (!nombre || !km) {
                alert('Por favor completa el nombre y la distancia.');
                return;
            }

            const totalMinutos = (horas * 60) + minutos;

            const fechaFmt = fecha
                ? new Date(fecha + 'T12:00:00').toLocaleDateString('es-MX', { day: 'numeric', month: 'short', year: 'numeric' })
                : 'Sin fecha';

            const acts = cargarActividades();
            acts.unshift({
                id: Date.now().toString(),
                nombre,
                km,
                desc,
                fecha: fechaFmt,
                fechaISO: fecha,
                minutos: totalMinutos
            });
            guardarActividades(acts);
            renderActividades();

            bootstrap.Modal.getInstance(document.getElementById('modalActividad')).hide();
            document.getElementById('nombreAct').value = '';
            document.getElementById('kmAct').value = '';
            document.getElementById('horasAct').value = '';
            document.getElementById('minutosAct').value = '';
            document.getElementById('descAct').value = '';
            document.getElementById('fechaAct').value = hoy;
        });

        function filtrar(tipo, btn) {
            document.querySelectorAll('#filtros button').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            const cards = document.querySelectorAll('#listaActividades .activity-card');
            let visibles = 0;
            cards.forEach(card => {
                const km = parseFloat(card.dataset.km);
                let mostrar = true;
                if (tipo === 'corta' && km >= 40) mostrar = false;
                if (tipo === 'larga' && km < 40) mostrar = false;
                card.style.display = mostrar ? 'block' : 'none';
                if (mostrar) visibles++;
            });
            document.getElementById('sinActividades').classList.toggle('d-none', visibles > 0);
        }

        renderActividades();
    </script>

    <script src="{{ asset('clima.js') }}"></script>
</body>
</html>