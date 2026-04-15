<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // 1. Validar que el usuario sí haya escrito algo
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // 2. Intentar iniciar sesión con la base de datos
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // 3. ¡Magia de Roles! Hacia dónde los mandamos
            if (Auth::user()->role === 'admin') {
                return redirect('/rutas'); // El admin va a rutas
            }

            return redirect('/app'); // El cliente normal va a la app principal
        }

        // 4. Si se equivocan de contraseña
        return back()->withErrors([
            'email' => 'El correo o la contraseña son incorrectos.',
        ]);
    }
}
