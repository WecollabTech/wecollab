<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        $user = $request->user()?->loadMissing('role.permisos');

        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $user ? [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role?->only(['id', 'nombre', 'descripcion']),
                    'permissions' => $user->getPermissions(),
                    'can' => $this->getUserAbilities($user),
                ] : null,
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
                'warning' => fn () => $request->session()->get('warning'),
                'info' => fn () => $request->session()->get('info'),
            ],
        ]);
    }

    protected function getUserAbilities($user): array
    {
        if (!$user) {
            return [];
        }

        return [
            'viewConfigMenu' => $user->hasRole('Superadministrador'),
            'manageUsers' => $user->hasPermission('gestionar_usuarios'),
            // Agrega más habilidades según sea necesario
        ];
    }
}