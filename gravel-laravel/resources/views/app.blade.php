<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - GravelExpedition</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    
    <link rel="stylesheet" href="{{ asset('style.css') }}">

    <style>
        .kpi-card {
            background: #fff; border: 1px solid #e6e6e6;
            border-radius: 8px; padding: 16px;
            text-align: center; border-top: 3px solid #fc4c02;
        }
        .kpi-card .kpi-value { font-size: 1.6rem; font-weight: 800; color: #2d3436; }
        .kpi-card .kpi-label { font-size: 0.78rem; color: #636e72; text-transform: uppercase; letter-spacing: 0.5px; }

        .welcome-banner {
            background: linear-gradient(135deg, #fc4c02, #ff7043);
            color: white; border-radius: 12px; padding: 28px 24px;
            margin-bottom: 24px; position: relative; overflow: hidden;
        }
        .welcome-banner .btn-white {
            background: white; color: #fc4c02; border: none; border-radius: 8px;
            padding: 10px 22px; font-weight: 700; cursor: pointer; font-size: 0.95rem; transition: 0.2s;
        }
        .welcome-banner .deco { position: absolute; right: -20px; top: -20px; font-size: 7rem; opacity: 0.12; }

        .empty-state { text-align: center; padding: 50px 20px; color: #636e72; }
        .empty-state i { font-size: 3.5rem; color: #dfe6e9; margin-bottom: 16px; display: block; }
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
                <li><a href="/app" class="active"><i class="bi bi-speedometer2 me-2"></i>Tablero</a></li>
                <li><a href="/actividades"><i class="bi bi-list-ul me-2"></i>Mis Actividades</a></li>
                <li><a href="/rutas"><i class="bi bi-map me-2"></i>Mapas / Rutas</a></li>
                <li><a href="/entrenamiento"><i class="bi bi-heart-pulse me-2"></i>Entrenamiento</a></li>
                <li><a href="/" class="logout" id="btnLogout"><i class="bi bi-box-arrow-left me-2"></i>Cerrar Sesión</a></li>
            </ul>
        </nav>

        <section class="feed-content">

            <div id="welcomeBanner" class="welcome-banner">
                <span class="deco">🚴</span>
                <h3>¡Bienvenido/a, {{ Auth::user()->name }}! 🎉</h3>
                <p>Tu cuenta está lista. Empieza registrando tu primera actividad para ver tus estadísticas.</p>
                <button class="btn-white" onclick="window.location.href='/actividades'">
                    <i class="bi bi-plus-circle me-2"></i>Agregar mi primera actividad
                </button>
            </div>

            <div class="row g-3 mb-4 mt-3">
                <div class="col-6 col-md-3">
                    <div class="kpi-card">
                        <div class="kpi-value" id="kpiKm">0</div>
                        <div class="kpi-label">km este año</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="kpi-card">
                        <div class="kpi-value" id="kpiActividades">0</div>
                        <div class="kpi-label">Actividades</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="kpi-card">
                        <div class="kpi-value" id="kpiHoras">0h</div>
                        <div class="kpi-label">Tiempo total</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="kpi-card">
                        <div class="kpi-value" id="kpiSesiones">0</div>
                        <div class="kpi-label">Sesiones entreno</div>
                    </div>
                </div>
            </div>

            <h2>Tu Actividad Reciente</h2>
            <div id="contenidoActividades">
                </div>

        </section>

        <aside class="sidebar-right">
            <div class="widget">
                <h3><i class="bi bi-bar-chart me-1"></i>Estadísticas Semanales</h3>
                <div class="mb-2">
                    <div class="d-flex justify-content-between small text-muted mb-1">
                        <span>Kilómetros</span><span id="statKm">0 / 100 km</span>
                    </div>
                    <div class="progress-bar"><div class="fill" id="barKm" style="width:0%;"></div></div>
                </div>
            </div>

            <div class="widget">
                <h3><i class="bi bi-trophy me-1"></i>Tus Retos</h3>
                <ul class="club-list">
                    <li>🏁 Gran Fondo Oaxaca (Feb)</li>
                    <li>⬆️ Reto de Escalada 5k</li>
                </ul>
            </div>
        </aside>
    </div>

    <footer class="main-footer">
        <p>&copy; 2026 GravelExpedition &mdash; Proyecto de Programación Web &mdash; TecNM Oaxaca</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // 1. LLAVES DE MEMORIA (Usando el ID real de Laravel)
        const KEY_ACT = 'ge_actividades_{{ Auth::user()->id }}';
        const KEY_ENT = 'ge_sesiones_{{ Auth::user()->id }}';

        // 2. CARGAR DATOS
        const actividades = JSON.parse(localStorage.getItem(KEY_ACT) || '[]');
        const sesionesEnt = JSON.parse(localStorage.getItem(KEY_ENT) || '[]');

        // 3. FUNCIONES DE APOYO
        function formatTiempo(min) {
            if (!min || min === 0) return '0h';
            const h = Math.floor(min / 60);
            const m = min % 60;
            if (h === 0) return m + 'min';
            if (m === 0) return h + 'h';
            return h + 'h ' + m + 'min';
        }

        // 4. CÁLCULOS
        const totalKm = actividades.reduce((sum, a) => sum + parseFloat(a.km || 0), 0);
        const totalMin = actividades.reduce((sum, a) => sum + parseInt(a.minutos || 0), 0);
        
        // 5. ACTUALIZAR INTERFAZ (KPIs)
        document.getElementById('kpiKm').textContent = totalKm.toFixed(1);
        document.getElementById('kpiActividades').textContent = actividades.length;
        document.getElementById('kpiHoras').textContent = formatTiempo(totalMin);
        document.getElementById('kpiSesiones').textContent = sesionesEnt.length;

        // 6. ACTUALIZAR BARRA LATERAL (Meta de 100km)
        const pctMeta = Math.min(Math.round((totalKm / 100) * 100), 100);
        document.getElementById('statKm').textContent = totalKm.toFixed(0) + ' / 100 km';
        document.getElementById('barKm').style.width = pctMeta + '%';

        // 7. OCULTAR BANNER SI YA HAY DATOS
        if (actividades.length > 0) {
            document.getElementById('welcomeBanner').style.display = 'none';
        }

        // 8. RENDERIZAR ACTIVIDADES RECIENTES EN EL FEED
        const contenedor = document.getElementById('contenidoActividades');
        if (actividades.length === 0) {
            contenedor.innerHTML = `
                <article class="activity-card p-4 text-center">
                    <div class="empty-state">
                        <i class="bi bi-bicycle"></i>
                        <h4>Aún no tienes actividades</h4>
                        <p class="text-muted">Registra tu primera salida para verla aquí.</p>
                    </div>
                </article>`;
        } else {
            // Mostramos las últimas 3 registradas
            const ultimas = actividades.slice(0, 3);
            contenedor.innerHTML = ultimas.map(a => `
                <article class="activity-card mb-3 p-3" style="background: white; border-radius: 8px; border: 1px solid #eee;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0" style="color: #fc4c02;">${a.nombre}</h4>
                            <small class="text-muted"><i class="bi bi-calendar3 me-1"></i>${a.fecha}</small>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-warning text-dark fs-6">${a.km} km</span>
                            <div class="small text-muted mt-1">${formatTiempo(a.minutos)}</div>
                        </div>
                    </div>
                </article>
            `).join('');
        }

        // 9. LOGOUT
        document.getElementById('btnLogout').addEventListener('click', function(e) {
            e.preventDefault();
            window.location.href = '/';
        });
    </script>
</body>
</html>