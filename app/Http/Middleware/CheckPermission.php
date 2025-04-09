<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CheckPermission
{
    public function handle(Request $request, Closure $next, $permission)
    {
        $user = Auth::user(); // Obtener el usuario autenticado
    
        if (!$user) {
            return Inertia::render('PaginaError/AccessDenied');
        }
    
        // Obtener los permisos del usuario
        $userPermissions = $user->role->permisos->pluck('nombre')->toArray();
    
        // Verificar si el usuario tiene el permiso necesario
        if (!in_array($permission, $userPermissions)) {
            return Inertia::render('PaginaError/AccessDenied'); // Acceso denegado por permisos
        }
    
        return $next($request);
    }
    
}