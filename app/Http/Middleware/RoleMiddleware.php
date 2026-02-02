<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Verifica que el usuario autenticado tenga uno de los roles permitidos.
     * Si no tiene el rol requerido, redirige al dashboard con un mensaje de error.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles  Roles permitidos (ej: 'admin', 'user')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Verificar que el usuario esté autenticado
        if (!auth()->check()) {
            return redirect()->route('login')
                ->with('error', 'Debes iniciar sesión para acceder a esta página.');
        }

        // Obtener el usuario autenticado
        $user = auth()->user();

        // Verificar si el usuario tiene alguno de los roles permitidos
        if (!in_array($user->role, $roles)) {
            return redirect()->route('dashboard')
                ->with('error', 'No tienes permisos para realizar esta acción.');
        }

        // Si tiene el rol correcto, continuar con la petición
        return $next($request);
    }
}
