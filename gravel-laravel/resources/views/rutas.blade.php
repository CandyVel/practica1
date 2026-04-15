<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rutas - GravelExpedition</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    
    <link rel="stylesheet" href="{{ asset('style.css') }}">

    <style>
        #mapa { height: 350px; border-radius: 8px; width: 100%; }
        .ruta-item {
            padding: 12px 0;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
            transition: background 0.2s;
        }
        .ruta-item:last-child { border-bottom: none; }
        .ruta-item:hover { background: #fff8f5; padding-left: 8px; }
        .ruta-item .ver-btn { color: #fc4c02; font-weight: bold; text-decoration: none; font-size: 0.95rem; }
        .badge-dificultad { font-size: 0.7rem; padding: 3px 8px; border-radius: 20px; }
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
                <li><a href="/actividades"><i class="bi bi-list-ul me-2"></i>Mis Actividades</a></li>
                <li><a href="/rutas" class="active"><i class="bi bi-map me-2"></i>Mapas / Rutas</a></li>
                <li><a href="/entrenamiento"><i class="bi bi-heart-pulse me-2"></i>Entrenamiento</a></li>
                <li><a href="/" class="logout" id="btnLogout"><i class="bi bi-box-arrow-left me-2"></i>Cerrar Sesión</a></li>
                <li><a href="/usuarios"><i class="bi bi-people me-2"></i>Gestionar Usuarios</a></li>
            </ul>
        </nav>

        <section class="feed-content">
            <h2>🗺️ Explorador de Rutas</h2>

            <article class="activity-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="mb-0">Mapa de Oaxaca</h3>
                    <button class="btn btn-sm btn-warning text-white fw-bold" data-bs-toggle="modal" data-bs-target="#modalRuta">
                        <i class="bi bi-plus-circle me-1"></i> Nueva Ruta
                    </button>
                </div>
                <div id="mapa"></div>
                <div class="d-flex gap-2 mt-3 flex-wrap">
                    <button class="btn btn-outline-secondary btn-sm" onclick="centrarMapa()">
                        <i class="bi bi-geo-alt me-1"></i> Centrar Mapa
                    </button>
                </div>
            </article>

            <article class="activity-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="mb-0"><i class="bi bi-bookmark-star me-2"></i>Rutas Guardadas</h3>
                </div>

                <div id="buscadorWrap" class="d-none">
                    <input type="text" id="buscadorRutas" class="form-control mb-3" placeholder="🔍 Buscar ruta...">
                </div>

                <ul class="club-list" id="listaRutas" style="padding:0;"></ul>
            </article>
        </section>

        <aside class="sidebar-right">
            <div class="widget">
                <h3><i class="bi bi-bar-chart me-1"></i>Estadísticas de Rutas</h3>
                <ul class="club-list" id="statsRutas">
                    <li>🗺️ Rutas guardadas: <strong id="totalRutas">0</strong></li>
                    <li>📏 km total: <strong id="totalKmRutas">0 km</strong></li>
                    <li>🏔️ Ruta más larga: <strong id="rutaMasLarga">—</strong></li>
                    <li>⚡ Dificultad freq.: <strong id="difFrec">—</strong></li>
                </ul>
            </div>
            <div class="widget">
                <h3><i class="bi bi-info-circle me-1"></i>Ruta Activa</h3>
                <p id="infoRutaActiva" class="small text-muted">Selecciona una ruta para verla en el mapa.</p>
            </div>
            <div class="widget">
                <h3><i class="bi bi-cloud-sun me-1"></i>Clima Hoy</h3>
                <p class="small mb-1">☀️ Oaxaca: <strong>24°C</strong></p>
                <p class="small text-muted mb-0">Condiciones ideales para rodar 🚴</p>
            </div>
        </aside>
    </div>

    <footer class="main-footer"><p>© 2026 GravelExpedition</p></footer>

    <div class="modal fade" id="modalRuta" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">➕ Nueva Ruta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nombre de la Ruta</label>
                        <input type="text" id="nombreRuta" class="form-control" placeholder="Ej. Ruta del Café">
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label class="form-label fw-bold">Distancia (km)</label>
                            <input type="number" id="kmRuta" class="form-control" placeholder="Ej. 45">
                        </div>
                        <div class="col mb-3">
                            <label class="form-label fw-bold">Desnivel (m)</label>
                            <input type="number" id="desnivel" class="form-control" placeholder="Ej. 800">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Dificultad</label>
                        <select id="dificultad" class="form-select">
                            <option value="Fácil|bg-success">Fácil</option>
                            <option value="Moderada|bg-info text-dark">Moderada</option>
                            <option value="Difícil|bg-warning text-dark">Difícil</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button class="btn-main" style="width:auto;padding:8px 20px;" onclick="guardarRuta()">Guardar Ruta</button>
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

        // ¡Magia! Usamos el ID real de Laravel
        function cargarRutas() {
            const key = 'ge_rutas_{{ Auth::user()->id }}';
            return JSON.parse(localStorage.getItem(key) || '[]');
        }

        function guardarRutasStorage(rutas) {
            const key = 'ge_rutas_{{ Auth::user()->id }}';
            localStorage.setItem(key, JSON.stringify(rutas));
        }

        function renderRutas() {
            const rutas = cargarRutas();
            const lista = document.getElementById('listaRutas');
            const buscador = document.getElementById('buscadorWrap');

            if (rutas.length === 0) {
                buscador.classList.add('d-none');
                lista.innerHTML = `
                    <div class="empty-state">
                        <i class="bi bi-map"></i>
                        <h4>No tienes rutas guardadas</h4>
                        <p class="text-muted">Agrega tu primera ruta con el botón "Nueva Ruta".</p>
                    </div>`;
                return;
            }

            const totalKmRutas = rutas.reduce((s, r) => s + parseFloat(r.km || 0), 0);
            const rutaMasLarga = rutas.reduce((max, r) => parseFloat(r.km) > parseFloat(max.km || 0) ? r : max, rutas[0]);
            const frecDif = ['Fácil','Moderada','Difícil'].map(d => ({
                d, count: rutas.filter(r => r.dificultad === d).length
            })).sort((a,b) => b.count - a.count)[0];

            document.getElementById('totalRutas').textContent = rutas.length;
            document.getElementById('totalKmRutas').textContent = totalKmRutas.toFixed(1) + ' km';
            document.getElementById('rutaMasLarga').textContent = rutaMasLarga ? rutaMasLarga.km + ' km' : '—';
            document.getElementById('difFrec').textContent = frecDif && frecDif.count > 0 ? frecDif.d : '—';

            buscador.classList.remove('d-none');
            lista.innerHTML = rutas.map((r, i) => `
                <li class="ruta-item" data-nombre="${r.nombre.toLowerCase()}" onclick="cargarRutaMapa('${r.nombre}')">
                    <div>
                        <strong>${r.nombre}</strong>
                        <div class="text-muted small">${r.km} km • Desnivel: ${r.desnivel || '?'} m</div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge ${r.badge} badge-dificultad">${r.dificultad}</span>
                        <span class="ver-btn">Ver ›</span>
                    </div>
                </li>`).join('');
        }

        function guardarRuta() {
            const nombre    = document.getElementById('nombreRuta').value.trim();
            const km        = document.getElementById('kmRuta').value;
            const desnivel  = document.getElementById('desnivel').value;
            const difRaw    = document.getElementById('dificultad').value.split('|');
            const dificultad = difRaw[0];
            const badge      = difRaw[1];

            if (!nombre || !km) { alert('Por favor ingresa el nombre y la distancia.'); return; }

            const rutas = cargarRutas();
            rutas.unshift({ nombre, km, desnivel, dificultad, badge });
            guardarRutasStorage(rutas);
            renderRutas();

            bootstrap.Modal.getInstance(document.getElementById('modalRuta')).hide();
            document.getElementById('nombreRuta').value = '';
            document.getElementById('kmRuta').value = '';
            document.getElementById('desnivel').value = '';
        }

        function cargarRutaMapa(nombre) {
            document.getElementById('infoRutaActiva').innerHTML = `<strong>${nombre}</strong> cargada en el mapa.`;
        }

        // Buscador
        document.getElementById('buscadorRutas').addEventListener('input', function() {
            const filtro = this.value.toLowerCase();
            document.querySelectorAll('#listaRutas .ruta-item').forEach(item => {
                item.style.display = item.dataset.nombre.includes(filtro) ? 'flex' : 'none';
            });
        });

        // Mapa
        let mapa, marcador;
        function initMap() {
            const oaxaca = { lat: 17.0732, lng: -96.7266 };
            mapa = new google.maps.Map(document.getElementById('mapa'), {
                zoom: 11, center: oaxaca, mapTypeId: 'terrain'
            });
            marcador = new google.maps.Marker({
                position: oaxaca, map: mapa, title: 'Oaxaca',
                icon: { url: 'http://maps.google.com/mapfiles/ms/icons/orange-dot.png' }
            });
        }

        function centrarMapa() {
            mapa.setCenter({ lat: 17.0732, lng: -96.7266 });
            mapa.setZoom(11);
        }

        renderRutas();
    </script>

    <script src="https://maps.googleapis.com/maps/api/js?key=TU_API_KEY&callback=initMap" async defer></script>
</body>
</html>