<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Usuarios - Jefe Gravel</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    
    <link rel="stylesheet" href="{{ asset('style.css') }}">

    <style>
        /* Ajuste para que los modales no se corten si la tarjeta tiene overflow */
        .activity-card {
            overflow: visible !important;
        }
        /* Asegurar que el texto de los labels sea visible en el modal */
        .modal-body label {
            color: #2d3436;
            font-weight: 600;
        }
    </style>
</head>
<body class="app-body">

    <header class="main-header">
        <div class="brand">🚴 Panel Administrativo</div>
        <div class="user-profile">
            <span>Hola, <strong>{{ Auth::user()->name }}</strong> (Admin)</span>
            <img src="{{ asset('img/usuario.png') }}" alt="Avatar" class="avatar">
        </div>
    </header>

    <div class="layout-container">
        <nav class="main-nav">
            <ul>
                <li><a href="/rutas"><i class="bi bi-map me-2"></i>Mapas / Rutas</a></li>
                <li><a href="/usuarios" class="active"><i class="bi bi-people me-2"></i>Gestionar Usuarios</a></li>
                <li><a href="/"><i class="bi bi-box-arrow-left me-2"></i>Cerrar Sesión</a></li>
            </ul>
        </nav>

        <section class="feed-content">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>👥 Gestión de Ciclistas</h2>
                <button class="btn btn-warning text-white fw-bold" data-bs-toggle="modal" data-bs-target="#modalCrear">
                    <i class="bi bi-person-plus-fill me-1"></i> Nuevo Usuario
                </button>
            </div>

            <article class="activity-card p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th>Rol</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($usuarios as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    <span class="badge {{ $user->role == 'admin' ? 'bg-danger' : 'bg-primary' }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#modalEditar{{ $user->id }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>

                                    <form action="{{ route('usuarios.destroy', $user->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Seguro que quieres eliminar a este ciclista?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </article>
        </section>
    </div>

    <div class="modal fade" id="modalCrear" tabindex="-1" aria-labelledby="modalCrearLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('usuarios.store') }}" method="POST" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCrearLabel">Crear Nuevo Ciclista</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nombre Completo</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Rol</label>
                        <select name="role" class="form-select">
                            <option value="cliente">Cliente</option>
                            <option value="admin">Administrador</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Contraseña</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning text-white">Guardar Usuario</button>
                </div>
            </form>
        </div>
    </div>

    @foreach($usuarios as $user)
    <div class="modal fade" id="modalEditar{{ $user->id }}" tabindex="-1" aria-labelledby="label{{ $user->id }}" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('usuarios.update', $user->id) }}" method="POST" class="modal-content">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="label{{ $user->id }}">Editar Usuario: {{ $user->name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" name="name" value="{{ $user->name }}" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" value="{{ $user->email }}" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Rol</label>
                        <select name="role" class="form-select">
                            <option value="cliente" {{ $user->role == 'cliente' ? 'selected' : '' }}>Cliente</option>
                            <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Administrador</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nueva Contraseña</label>
                        <input type="password" name="password" class="form-control" placeholder="Dejar en blanco para no cambiar">
                        <small class="text-muted">Mínimo 4 caracteres si decides cambiarla.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning text-white">Actualizar Datos</button>
                </div>
            </form>
        </div>
    </div>
    @endforeach

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>