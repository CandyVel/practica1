<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rutas - GravelExpedition</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <link rel="stylesheet" href="{{ asset('style.css') }}">
    <style>
        #mapa { height: 420px; border-radius: 8px; width: 100%; z-index: 0; cursor: crosshair; }
        .instruccion {
            background: #fff3ee; border-left: 3px solid #fc4c02;
            padding: 10px 14px; border-radius: 0 8px 8px 0;
            font-size: 0.88rem; color: #2d3436; margin-bottom: 12px;
        }
        .instruccion strong { color: #fc4c02; }
        .paso-badge {
            display: inline-flex; align-items: center; justify-content: center;
            width: 22px; height: 22px; border-radius: 50%;
            font-size: 0.75rem; font-weight: 700; margin-right: 6px; flex-shrink: 0;
        }
        .paso-a   { background: #00b894; color: white; }
        .paso-b   { background: #d63031; color: white; }
        .paso-off { background: #dfe6e9; color: #636e72; }
        .ruta-item {
            padding: 12px 0; border-bottom: 1px solid #eee;
            display: flex; justify-content: space-between; align-items: center;
            cursor: pointer; transition: all 0.2s;
        }
        .ruta-item:last-child { border-bottom: none; }
        .ruta-item:hover { background: #fff8f5; padding-left: 8px; }
        .ruta-item.activa { background: #fff3ee; border-left: 3px solid #fc4c02; padding-left: 10px; }
        .badge-dificultad { font-size: 0.7rem; padding: 3px 8px; border-radius: 20px; }
        .empty-state { text-align: center; padding: 60px 20px; color: #636e72; }
        .empty-state i { font-size: 3.5rem; color: #dfe6e9; margin-bottom: 16px; display: block; }
        .empty-state h4 { color: #2d3436; margin-bottom: 8px; }
        .lugar-badge { background: #f1f2f6; color: #636e72; font-size: 0.75rem; padding: 2px 8px; border-radius: 10px; max-width: 180px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .buscador-wrap { position: relative; }
        .buscador-resultados {
            position: absolute; top: 100%; left: 0; right: 0;
            background: white; border: 1px solid #dee2e6;
            border-top: none; border-radius: 0 0 8px 8px;
            max-height: 220px; overflow-y: auto;
            z-index: 9999; box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            display: none;
        }
        .buscador-resultados.show { display: block; }
        .resultado-item {
            padding: 9px 14px; cursor: pointer; font-size: 0.88rem;
            border-bottom: 1px solid #f1f2f6; transition: background 0.15s;
        }
        .resultado-item:last-child { border-bottom: none; }
        .resultado-item:hover { background: #fff3ee; color: #fc4c02; }
        .resultado-item .nombre { font-weight: 600; }
        .resultado-item .subtitulo { font-size: 0.78rem; color: #636e72; }
        .resultado-item.cargando { color: #636e72; font-style: italic; }
        .punto-info {
            display: flex; align-items: flex-start;
            padding: 8px 12px; border-radius: 8px; font-size: 0.85rem;
        }
        .punto-info.activo-a { background: #e8faf5; border: 1px solid #00b894; }
        .punto-info.activo-b { background: #fef0f0; border: 1px solid #d63031; }
        .punto-info.pendiente { background: #f8f9fa; border: 1px dashed #dee2e6; color: #636e72; }
        .nombre-lugar { font-weight: 600; color: #2d3436; font-size: 0.82rem; line-height: 1.3; }
    </style>
</head>
<body class="app-body">

    <header class="main-header">
        <div class="brand">GravelExpedition</div>
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
                <li><a href="/" class="logout" id="btnLogout"><i class="bi bi-box-arrow-left me-2"></i>Cerrar Sesion</a></li>
                <li><a href="/usuarios"><i class="bi bi-people me-2"></i>Gestionar Usuarios</a></li>
            </ul>
        </nav>

        <section class="feed-content">
            <h2>Explorador de Rutas</h2>

            <!-- Buscador de lugares -->
            <article class="activity-card mb-3">
                <h3 class="mb-3">Buscar lugar en el mapa</h3>
                <div class="buscador-wrap">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" id="buscadorLugar" class="form-control"
                            placeholder="Busca un lugar en Oaxaca o haz clic en el mapa..."
                            autocomplete="off"
                            oninput="buscarLugar(this.value)">
                        <button class="btn btn-outline-secondary" onclick="limpiarBuscador()">
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                    <div class="buscador-resultados" id="resultadosBusqueda"></div>
                </div>
                <small class="text-muted mt-2 d-block">
                    <i class="bi bi-info-circle me-1"></i>
                    Busca un lugar para ir al mapa, o haz clic directamente sobre el mapa para marcar puntos.
                </small>
            </article>

            <!-- Instruccion -->
            <div class="instruccion" id="instruccion">
                <span class="paso-badge paso-a">A</span>
                <strong>Haz clic en el mapa</strong> para marcar el punto de inicio, o busca un lugar arriba.
            </div>

            <!-- Puntos seleccionados -->
            <div class="row g-2 mb-3" id="infoPuntos">
                <div class="col-md-6">
                    <div class="punto-info pendiente" id="wrapPuntoA">
                        <span class="paso-badge paso-off me-2" id="badgeA">A</span>
                        <div>
                            <div class="small text-muted">Punto de inicio</div>
                            <div class="nombre-lugar" id="nombreA">Sin seleccionar</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="punto-info pendiente" id="wrapPuntoB">
                        <span class="paso-badge paso-off me-2" id="badgeB">B</span>
                        <div>
                            <div class="small text-muted">Punto de destino</div>
                            <div class="nombre-lugar" id="nombreB">Sin seleccionar</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mapa -->
            <article class="activity-card">
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                    <h3 class="mb-0">Mapa de Oaxaca</h3>
                    <div class="d-flex gap-2 flex-wrap">
                        <button class="btn btn-outline-secondary btn-sm" onclick="centrarMapa()">
                            <i class="bi bi-geo-alt me-1"></i> Centrar
                        </button>
                        <button class="btn btn-sm btn-outline-danger" onclick="resetearPuntos()" id="btnReset" style="display:none">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Reiniciar
                        </button>
                        <button class="btn btn-sm btn-warning text-white fw-bold" onclick="abrirModalGuardar()" id="btnGuardarRuta" style="display:none">
                            <i class="bi bi-floppy me-1"></i> Guardar Ruta
                        </button>
                    </div>
                </div>
                <div id="mapa"></div>

                <!-- Distancia calculada -->
                <div id="distanciaInfo" class="mt-3" style="display:none">
                    <div class="d-flex align-items-center gap-3 p-3 rounded" style="background:#fff3ee;border:1px solid #fc4c02">
                        <i class="bi bi-arrows-expand text-warning fs-4"></i>
                        <div>
                            <div class="fw-bold">Distancia calculada</div>
                            <div class="small text-muted">Distancia en linea recta entre los dos puntos</div>
                        </div>
                        <div class="ms-auto fw-bold fs-5" style="color:#fc4c02" id="kmCalculado">0 km</div>
                    </div>
                </div>
            </article>

            <!-- Rutas guardadas -->
            <article class="activity-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="mb-0"><i class="bi bi-bookmark-star me-2"></i>Rutas Guardadas</h3>
                </div>
                <div id="buscadorRutasWrap" class="d-none">
                    <input type="text" id="buscadorRutas" class="form-control mb-3" placeholder="Buscar ruta guardada...">
                </div>
                <ul class="club-list" id="listaRutas" style="padding:0;"></ul>
            </article>
        </section>

        <aside class="sidebar-right">
            <div class="widget">
                <h3><i class="bi bi-bar-chart me-1"></i>Estadisticas</h3>
                <ul class="club-list">
                    <li>Rutas guardadas: <strong id="totalRutas">0</strong></li>
                    <li>km total: <strong id="totalKmRutas">0 km</strong></li>
                    <li>Ruta mas larga: <strong id="rutaMasLarga">—</strong></li>
                    <li>Dificultad frec.: <strong id="difFrec">—</strong></li>
                </ul>
            </div>
            <div class="widget">
                <h3><i class="bi bi-info-circle me-1"></i>Ruta Activa</h3>
                <div id="infoRutaActiva">
                    <p class="small text-muted mb-0">Selecciona una ruta guardada para verla en el mapa.</p>
                </div>
            </div>

            <!-- Widget clima Open-Meteo -->
            <div class="widget">
                <h3><i class="bi bi-cloud-sun me-1"></i>Clima en Oaxaca</h3>
                <div id="climaWidget">
                    <p class="small text-muted">Cargando clima...</p>
                </div>
            </div>

            <div class="widget">
                <h3><i class="bi bi-lightbulb me-1"></i>Como usar</h3>
                <ul class="club-list" style="font-size:0.82rem">
                    <li>Busca un lugar en el buscador</li>
                    <li>O haz clic directo en el mapa</li>
                    <li>Primer clic = punto A (inicio)</li>
                    <li>Segundo clic = punto B (destino)</li>
                    <li>La distancia se calcula sola</li>
                    <li>Guarda con el boton naranja</li>
                </ul>
            </div>
        </aside>
    </div>

    <footer class="main-footer"><p>&copy; 2026 GravelExpedition</p></footer>

    <!-- MODAL GUARDAR RUTA -->
    <div class="modal fade" id="modalGuardar" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Guardar Ruta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3 p-3 rounded" style="background:#f8f9fa;border:1px solid #dee2e6">
                        <div class="small text-muted mb-2">Ruta trazada</div>
                        <div class="d-flex align-items-start gap-2 mb-2">
                            <span class="paso-badge paso-a flex-shrink-0">A</span>
                            <span class="small" id="modalInicio">—</span>
                        </div>
                        <div class="d-flex align-items-start gap-2 mb-2">
                            <span class="paso-badge paso-b flex-shrink-0">B</span>
                            <span class="small" id="modalFin">—</span>
                        </div>
                        <div class="fw-bold mt-2" style="color:#fc4c02" id="modalKm">0 km</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nombre de la Ruta</label>
                        <input type="text" id="nombreRuta" class="form-control" placeholder="Ej. Ruta del Cafe">
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label class="form-label fw-bold">Desnivel (m) <small class="text-muted fw-normal">opcional</small></label>
                            <input type="number" id="desnivel" class="form-control" placeholder="Ej. 800">
                        </div>
                        <div class="col mb-3">
                            <label class="form-label fw-bold">Dificultad</label>
                            <select id="dificultad" class="form-select">
                                <option value="Facil|bg-success">Facil</option>
                                <option value="Moderada|bg-info text-dark">Moderada</option>
                                <option value="Dificil|bg-warning text-dark">Dificil</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button class="btn-main" style="width:auto;padding:8px 20px;" onclick="confirmarGuardar()">
                        <i class="bi bi-floppy me-1"></i>Guardar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // ── Estado ──────────────────────────────────────────────
        let puntoA = null, puntoB = null;
        let marcadorA = null, marcadorB = null;
        let lineaRuta = null;
        let buscarTimeout = null;

        // ── Haversine ───────────────────────────────────────────
        function calcularDistanciaKm(lat1, lng1, lat2, lng2) {
            const R    = 6371;
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLng = (lng2 - lng1) * Math.PI / 180;
            const a    =
                Math.sin(dLat/2)*Math.sin(dLat/2) +
                Math.cos(lat1*Math.PI/180)*Math.cos(lat2*Math.PI/180)*
                Math.sin(dLng/2)*Math.sin(dLng/2);
            return (R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a))).toFixed(2);
        }

        // ── Nominatim: geocodificacion inversa ──────────────────
        async function obtenerNombreLugar(lat, lng) {
            try {
                const res = await fetch(
                    `https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json&accept-language=es`,
                    { headers: { 'Accept-Language': 'es' } }
                );
                const data = await res.json();
                if (data && data.display_name) {
                    const partes = data.display_name.split(', ');
                    return partes.slice(0, 2).join(', ');
                }
            } catch(e) {}
            return `${lat.toFixed(4)}, ${lng.toFixed(4)}`;
        }

        // ── Nominatim: buscar lugar por nombre ──────────────────
        async function buscarLugar(termino) {
            clearTimeout(buscarTimeout);
            const resultados = document.getElementById('resultadosBusqueda');
            if (termino.trim().length < 3) { resultados.classList.remove('show'); return; }
            resultados.innerHTML = '<div class="resultado-item cargando">Buscando...</div>';
            resultados.classList.add('show');
            buscarTimeout = setTimeout(async () => {
                try {
                    const res = await fetch(
                        `https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(termino + ' Oaxaca Mexico')}&format=json&limit=6&accept-language=es`,
                        { headers: { 'Accept-Language': 'es' } }
                    );
                    const data = await res.json();
                    if (!data.length) { resultados.innerHTML = '<div class="resultado-item cargando">Sin resultados.</div>'; return; }
                    resultados.innerHTML = data.map(lugar => {
                        const partes    = lugar.display_name.split(', ');
                        const nombre    = partes.slice(0, 2).join(', ');
                        const subtitulo = partes.slice(2, 4).join(', ');
                        return `<div class="resultado-item"
                            onclick="irALugar(${lugar.lat}, ${lugar.lon}, '${nombre.replace(/'/g,"\\'")}')">
                            <div class="nombre"><i class="bi bi-geo-alt me-1"></i>${nombre}</div>
                            <div class="subtitulo">${subtitulo}</div>
                        </div>`;
                    }).join('');
                } catch(e) {
                    resultados.innerHTML = '<div class="resultado-item cargando">Error al buscar.</div>';
                }
            }, 500);
        }

        function irALugar(lat, lng, nombre) {
            mapa.setView([lat, lng], 14);
            document.getElementById('resultadosBusqueda').classList.remove('show');
            document.getElementById('buscadorLugar').value = nombre;
        }

        function limpiarBuscador() {
            document.getElementById('buscadorLugar').value = '';
            document.getElementById('resultadosBusqueda').classList.remove('show');
        }

        document.addEventListener('click', function(e) {
            if (!e.target.closest('.buscador-wrap'))
                document.getElementById('resultadosBusqueda').classList.remove('show');
        });

        // ── Mapa ────────────────────────────────────────────────
        const coordOaxaca = [17.0732, -96.7266];
        const mapa = L.map('mapa').setView(coordOaxaca, 11);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(mapa);

        const iconA = L.divIcon({
            html: `<div style="background:#00b894;color:white;border-radius:50%;width:32px;height:32px;display:flex;align-items:center;justify-content:center;font-size:15px;font-weight:700;border:3px solid white;box-shadow:0 3px 8px rgba(0,0,0,0.35)">A</div>`,
            iconSize: [32,32], iconAnchor: [16,16]
        });
        const iconB = L.divIcon({
            html: `<div style="background:#d63031;color:white;border-radius:50%;width:32px;height:32px;display:flex;align-items:center;justify-content:center;font-size:15px;font-weight:700;border:3px solid white;box-shadow:0 3px 8px rgba(0,0,0,0.35)">B</div>`,
            iconSize: [32,32], iconAnchor: [16,16]
        });

        // ── Clic en el mapa ─────────────────────────────────────
        mapa.on('click', async function(e) {
            const { lat, lng } = e.latlng;
            if (!puntoA) {
                document.getElementById('nombreA').textContent = 'Obteniendo nombre...';
                document.getElementById('wrapPuntoA').className = 'punto-info activo-a';
                document.getElementById('badgeA').className = 'paso-badge paso-a me-2';
                puntoA = { lat, lng, nombre: '' };
                if (marcadorA) mapa.removeLayer(marcadorA);
                marcadorA = L.marker([lat, lng], { icon: iconA }).addTo(mapa);
                const nombre = await obtenerNombreLugar(lat, lng);
                puntoA.nombre = nombre;
                marcadorA.bindPopup(`Inicio: ${nombre}`).openPopup();
                document.getElementById('nombreA').textContent = nombre;
                document.getElementById('btnReset').style.display = 'inline-flex';
                document.getElementById('instruccion').innerHTML = `
                    <span class="paso-badge paso-b">B</span>
                    Ahora haz clic para marcar el <strong>punto de destino</strong>.`;
            } else if (!puntoB) {
                document.getElementById('nombreB').textContent = 'Obteniendo nombre...';
                document.getElementById('wrapPuntoB').className = 'punto-info activo-b';
                document.getElementById('badgeB').className = 'paso-badge paso-b me-2';
                puntoB = { lat, lng, nombre: '' };
                if (marcadorB) mapa.removeLayer(marcadorB);
                marcadorB = L.marker([lat, lng], { icon: iconB }).addTo(mapa);
                const nombre = await obtenerNombreLugar(lat, lng);
                puntoB.nombre = nombre;
                marcadorB.bindPopup(`Destino: ${nombre}`).openPopup();
                document.getElementById('nombreB').textContent = nombre;
                if (lineaRuta) mapa.removeLayer(lineaRuta);
                lineaRuta = L.polyline(
                    [[puntoA.lat, puntoA.lng], [puntoB.lat, puntoB.lng]],
                    { color: '#fc4c02', weight: 4, opacity: 0.9, dashArray: '8 4' }
                ).addTo(mapa);
                mapa.fitBounds(lineaRuta.getBounds(), { padding: [50, 50] });
                const km = calcularDistanciaKm(puntoA.lat, puntoA.lng, puntoB.lat, puntoB.lng);
                document.getElementById('kmCalculado').textContent = km + ' km';
                document.getElementById('distanciaInfo').style.display = 'block';
                document.getElementById('btnGuardarRuta').style.display = 'inline-flex';
                document.getElementById('instruccion').innerHTML = `
                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                    <strong>Ruta lista.</strong> Distancia: <strong style="color:#fc4c02">${km} km</strong>
                    — Guarda la ruta o usa Reiniciar para trazar una nueva.`;
            }
        });

        function centrarMapa() { mapa.setView(coordOaxaca, 11); }

        function resetearPuntos() {
            if (marcadorA) { mapa.removeLayer(marcadorA); marcadorA = null; }
            if (marcadorB) { mapa.removeLayer(marcadorB); marcadorB = null; }
            if (lineaRuta) { mapa.removeLayer(lineaRuta); lineaRuta = null; }
            puntoA = null; puntoB = null;
            document.getElementById('instruccion').innerHTML = `
                <span class="paso-badge paso-a">A</span>
                <strong>Haz clic en el mapa</strong> para marcar el punto de inicio de tu ruta.`;
            document.getElementById('nombreA').textContent = 'Sin seleccionar';
            document.getElementById('nombreB').textContent = 'Sin seleccionar';
            document.getElementById('wrapPuntoA').className = 'punto-info pendiente';
            document.getElementById('wrapPuntoB').className = 'punto-info pendiente';
            document.getElementById('badgeA').className = 'paso-badge paso-off me-2';
            document.getElementById('badgeB').className = 'paso-badge paso-off me-2';
            document.getElementById('distanciaInfo').style.display = 'none';
            document.getElementById('btnGuardarRuta').style.display = 'none';
            document.getElementById('btnReset').style.display = 'none';
            document.getElementById('infoRutaActiva').innerHTML =
                '<p class="small text-muted mb-0">Selecciona una ruta guardada para verla en el mapa.</p>';
        }

        // ── Modal guardar ───────────────────────────────────────
        function abrirModalGuardar() {
            if (!puntoA || !puntoB) return;
            const km = calcularDistanciaKm(puntoA.lat, puntoA.lng, puntoB.lat, puntoB.lng);
            document.getElementById('modalInicio').textContent  = puntoA.nombre;
            document.getElementById('modalFin').textContent     = puntoB.nombre;
            document.getElementById('modalKm').textContent      = km + ' km';
            document.getElementById('nombreRuta').value         = '';
            document.getElementById('desnivel').value           = '0';
            document.getElementById('dificultad').selectedIndex = 0;
            new bootstrap.Modal(document.getElementById('modalGuardar')).show();
        }

        function confirmarGuardar() {
            const nombre   = document.getElementById('nombreRuta').value.trim();
            const desnivel = document.getElementById('desnivel').value;
            const difRaw   = document.getElementById('dificultad').value.split('|');
            const km       = calcularDistanciaKm(puntoA.lat, puntoA.lng, puntoB.lat, puntoB.lng);
            if (!nombre) { alert('Ingresa el nombre de la ruta.'); return; }
            const rutas = cargarRutas();
            rutas.unshift({
                nombre,
                inicio: puntoA.nombre, fin: puntoB.nombre,
                latA: puntoA.lat, lngA: puntoA.lng,
                latB: puntoB.lat, lngB: puntoB.lng,
                km, desnivel, dificultad: difRaw[0], badge: difRaw[1]
            });
            guardarRutasStorage(rutas);
            renderRutas();
            document.getElementById('infoRutaActiva').innerHTML = `
                <p class="mb-1 fw-bold" style="color:#fc4c02">${nombre}</p>
                <p class="small mb-1"><strong>Inicio:</strong> ${puntoA.nombre}</p>
                <p class="small mb-1"><strong>Destino:</strong> ${puntoB.nombre}</p>
                <p class="small mb-0">${km} km · ${desnivel || '0'} m desnivel</p>`;
            bootstrap.Modal.getInstance(document.getElementById('modalGuardar')).hide();
            resetearPuntos();
        }

        // ── CRUD ────────────────────────────────────────────────
        function cargarRutas() {
            return JSON.parse(localStorage.getItem('ge_rutas_{{ Auth::user()->id }}') || '[]');
        }
        function guardarRutasStorage(rutas) {
            localStorage.setItem('ge_rutas_{{ Auth::user()->id }}', JSON.stringify(rutas));
        }

        let capaRutaSel   = null;
        let marcadoresSel = [];

        function renderRutas() {
            const rutas    = cargarRutas();
            const lista    = document.getElementById('listaRutas');
            const buscador = document.getElementById('buscadorRutasWrap');
            if (rutas.length === 0) {
                buscador.classList.add('d-none');
                lista.innerHTML = `
                    <div class="empty-state">
                        <i class="bi bi-map"></i>
                        <h4>No tienes rutas guardadas</h4>
                        <p class="text-muted">Traza y guarda tu primera ruta en el mapa.</p>
                    </div>`;
                document.getElementById('totalRutas').textContent   = '0';
                document.getElementById('totalKmRutas').textContent = '0 km';
                document.getElementById('rutaMasLarga').textContent = '—';
                document.getElementById('difFrec').textContent      = '—';
                return;
            }
            const totalKm  = rutas.reduce((s, r) => s + parseFloat(r.km || 0), 0);
            const masLarga = rutas.reduce((max, r) => parseFloat(r.km) > parseFloat(max.km || 0) ? r : max, rutas[0]);
            const frecDif  = ['Facil','Moderada','Dificil']
                .map(d => ({ d, count: rutas.filter(r => r.dificultad === d).length }))
                .sort((a, b) => b.count - a.count)[0];
            document.getElementById('totalRutas').textContent   = rutas.length;
            document.getElementById('totalKmRutas').textContent = totalKm.toFixed(1) + ' km';
            document.getElementById('rutaMasLarga').textContent = masLarga ? masLarga.km + ' km' : '—';
            document.getElementById('difFrec').textContent      = frecDif && frecDif.count > 0 ? frecDif.d : '—';
            buscador.classList.remove('d-none');
            lista.innerHTML = rutas.map((r, i) => `
                <li class="ruta-item" data-nombre="${r.nombre.toLowerCase()}" onclick="verRutaGuardada(${i})">
                    <div style="flex:1">
                        <strong>${r.nombre}</strong>
                        <div class="d-flex gap-1 mt-1 flex-wrap">
                            <span class="lugar-badge" title="${r.inicio}">${r.inicio}</span>
                            <span style="color:#aaa;font-size:0.8rem;flex-shrink:0">→</span>
                            <span class="lugar-badge" title="${r.fin}">${r.fin}</span>
                        </div>
                        <div class="text-muted small mt-1">${r.km} km · Desnivel: ${r.desnivel || '0'} m</div>
                    </div>
                    <div class="d-flex align-items-center gap-2 ms-2 flex-shrink-0">
                        <span class="badge ${r.badge} badge-dificultad">${r.dificultad}</span>
                        <button class="btn btn-sm btn-outline-danger"
                            onclick="event.stopPropagation(); eliminarRuta(${i})">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </li>`).join('');
        }

        function verRutaGuardada(index) {
            const rutas = cargarRutas();
            const r     = rutas[index];
            document.querySelectorAll('.ruta-item').forEach((el, i) =>
                el.classList.toggle('activa', i === index));
            if (!r.latA || !r.latB) return;
            if (capaRutaSel) mapa.removeLayer(capaRutaSel);
            marcadoresSel.forEach(m => mapa.removeLayer(m));
            marcadoresSel = [];
            capaRutaSel = L.polyline(
                [[r.latA, r.lngA], [r.latB, r.lngB]],
                { color: '#fc4c02', weight: 4, opacity: 0.85, dashArray: '8 4' }
            ).addTo(mapa);
            marcadoresSel.push(
                L.marker([r.latA, r.lngA], { icon: iconA }).addTo(mapa).bindPopup(`Inicio: ${r.inicio}`),
                L.marker([r.latB, r.lngB], { icon: iconB }).addTo(mapa).bindPopup(`Destino: ${r.fin}`)
            );
            mapa.fitBounds(capaRutaSel.getBounds(), { padding: [50, 50] });
            document.getElementById('infoRutaActiva').innerHTML = `
                <p class="mb-1 fw-bold" style="color:#fc4c02">${r.nombre}</p>
                <p class="small mb-1"><strong>Inicio:</strong> ${r.inicio}</p>
                <p class="small mb-1"><strong>Destino:</strong> ${r.fin}</p>
                <p class="small mb-0">${r.km} km · ${r.desnivel || '0'} m desnivel</p>`;
        }

        function eliminarRuta(index) {
            if (!confirm('Eliminar esta ruta?')) return;
            const rutas = cargarRutas();
            rutas.splice(index, 1);
            guardarRutasStorage(rutas);
            if (capaRutaSel) { mapa.removeLayer(capaRutaSel); capaRutaSel = null; }
            marcadoresSel.forEach(m => mapa.removeLayer(m));
            marcadoresSel = [];
            document.getElementById('infoRutaActiva').innerHTML =
                '<p class="small text-muted mb-0">Selecciona una ruta guardada para verla en el mapa.</p>';
            renderRutas();
        }

        document.getElementById('buscadorRutas').addEventListener('input', function() {
            const filtro = this.value.toLowerCase();
            document.querySelectorAll('#listaRutas .ruta-item').forEach(item => {
                item.style.display = item.dataset.nombre.includes(filtro) ? 'flex' : 'none';
            });
        });

        document.getElementById('btnLogout').addEventListener('click', function(e) {
            e.preventDefault();
            window.location.href = '/';
        });

    </script>

    <script src="{{ asset('clima.js') }}"></script>
</body>
</html>