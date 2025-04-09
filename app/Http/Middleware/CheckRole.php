<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CheckRole
{
    // public function handle(Request $request, Closure $next, ...$roles)
    // {
    //     $user = Auth::user(); // Obtener el usuario autenticado
        
    //     // Si no hay usuario autenticado, redirige a la página de acceso denegado
    //     if (!$user) {
    //         return Inertia::render('PaginaError/AccessDenied');
    //     }
        
    //     // Convierte los roles pasados a un array de enteros para verificar
    //     $roles = array_map('intval', $roles);
        
    //     // Verifica si el rol del usuario está en la lista de roles permitidos
    //     if (!in_array($user->role_id, $roles)) {
    //         return Inertia::render('PaginaError/AccessDenied'); // Redirige a la página de acceso denegado
    //     }

    //     // Si pasa la validación, continúa con la solicitud
    //     return $next($request);
    // }



    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = Auth::user(); // Obtener el usuario autenticado
    
        if (!$user) {
            return Inertia::render('PaginaError/AccessDenied');
        }
    
        // Convertir los roles pasados a un array de strings
        $roles = array_map('strval', $roles);  // Asegurarnos de que sean strings
    
        // Verifica si el rol del usuario está en la lista de roles permitidos
        if (!in_array($user->role->nombre, $roles)) {
            return Inertia::render('PaginaError/AccessDenied');
        }
    
        // Obtener los permisos del usuario asociados a su rol
        $permisos = $user->role->permisos->pluck('nombre')->toArray();
    
        // Compartir permisos con el frontend
        Inertia::share([
            'permisos' => $permisos, // Los permisos estarán disponibles globalmente
        ]);
    
        return $next($request);
    }
    

}