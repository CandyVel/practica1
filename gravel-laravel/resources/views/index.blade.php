<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - GravelExpedition</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="{{ asset('style.css') }}">
</head>
<body class="login-page">

    <div class="hero-panel">

        <div class="hero-logo">
            <div class="logo-icon">🚴</div>
            <div>
                <div class="logo-text">GRAVEL<span>EXP</span></div>
                <div class="logo-sub">Expedition</div>
            </div>
        </div>

        <div class="hero-content">
            <div class="hero-tag">🏔️ Ciclismo de Aventura</div>
            <h1 class="hero-title">
                REGISTRA.<br>
                EXPLORA.<br>
                <span>CONQUISTA.</span>
            </h1>
            <p class="hero-desc">
                Gestiona tus rutas, entrena con datos reales y lleva tu ciclismo al siguiente nivel con Gravel Expedition.
            </p>
            <div class="hero-stats">
                <div class="hero-stat-item">
                    <div class="num">1,250+</div>
                    <div class="lbl">km registrados</div>
                </div>
                <div class="divider-v"></div>
                <div class="hero-stat-item">
                    <div class="num">24</div>
                    <div class="lbl">actividades</div>
                </div>
                <div class="divider-v"></div>
                <div class="hero-stat-item">
                    <div class="num">12.4k</div>
                    <div class="lbl">m desnivel</div>
                </div>
            </div>
        </div>
    </div>

    <div class="login-form-panel">

        <div class="form-title">BIENVENIDO</div>
        <div class="form-subtitle">Accede a tu cuenta o crea una nueva para comenzar.</div>

        <div class="login-tabs">
            <button class="login-tab-btn active" onclick="mostrarTab('login', this)">Iniciar Sesión</button>
            <button class="login-tab-btn" onclick="mostrarTab('registro', this)">Registrarse</button>
        </div>

        <div id="panelLogin" class="login-section active">
            
            @if($errors->any())
                <div class="login-alert login-alert-error" style="display: block;">
                    <i class="bi bi-exclamation-circle me-2"></i>{{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST">
                @csrf 
                <div class="login-field">
                    <label>Correo Electrónico</label>
                    <div class="input-wrap">
                        <i class="bi bi-envelope"></i>
                        <input type="email" name="email" placeholder="ejemplo@gravel.com" required>
                    </div>
                </div>
                <div class="login-field">
                    <label>Contraseña</label>
                    <div class="input-wrap">
                        <i class="bi bi-lock"></i>
                        <input type="password" name="password" placeholder="••••••••" required>
                    </div>
                </div>
                <button type="submit" class="btn-login">
                    <i class="bi bi-arrow-right-circle me-2"></i>Entrar
                </button>
            </form>
        </div>

        <div id="panelRegistro" class="login-section">
            <form action="{{ url('/registro') }}" method="POST">
                @csrf
                <input type="hidden" name="role" value="cliente">

                <div class="login-field">
                    <label>Nombre completo</label>
                    <div class="input-wrap">
                        <i class="bi bi-person-badge"></i>
                        <input type="text" name="name" placeholder="Ej. Juan Pérez" required>
                    </div>
                </div>
                <div class="login-field">
                    <label>Correo electrónico</label>
                    <div class="input-wrap">
                        <i class="bi bi-envelope"></i>
                        <input type="email" name="email" placeholder="correo@ejemplo.com" required>
                    </div>
                </div>
                <div class="login-field">
                    <label>Contraseña</label>
                    <div class="input-wrap">
                        <i class="bi bi-shield-lock"></i>
                        <input type="password" name="password" placeholder="Mínimo 4 caracteres" required>
                    </div>
                </div>
                <button type="submit" class="btn-login">
                    <i class="bi bi-person-plus me-2"></i>Crear Cuenta
                </button>
            </form>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Función para cambiar el diseño entre las pestañas de Login y Registro
        function mostrarTab(tab, btn) {
            document.querySelectorAll('.login-tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.login-section').forEach(p => p.classList.remove('active'));
            btn.classList.add('active');
            document.getElementById('panel' + tab.charAt(0).toUpperCase() + tab.slice(1)).classList.add('active');
        }
    </script>

</body>
</html>