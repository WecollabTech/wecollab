<?php

namespace Database\Seeders;

use App\Models\Permiso;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Limpiar tablas primero
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('role_permiso')->truncate();
        DB::table('permisos')->truncate();
        DB::table('roles')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 1. Crear Roles
        $roles = [
            [
                'nombre' => 'Superadministrador',
                'descripcion' => 'Acceso completo a todas las funcionalidades del sistema',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nombre' => 'Administrador',
                'descripcion' => 'Administrador con permisos limitados',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nombre' => 'Cliente',
                'descripcion' => 'Cliente con acceso básico al sistema',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nombre' => 'Usuario público',
                'descripcion' => 'Usuario con acceso mínimo',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }

        // 2. Crear Permisos
        $permisos = [
            // Permisos de Tutoriales
            ['nombre' => 'ver_categorias', 'descripcion' => 'Ver categorías de tutoriales'],
            ['nombre' => 'ver_subcategorias', 'descripcion' => 'Ver subcategorías de tutoriales'],
            ['nombre' => 'ver_videos', 'descripcion' => 'Ver videos de capacitación'],
            ['nombre' => 'crear_video', 'descripcion' => 'Crear nuevos videos'],
            ['nombre' => 'ver_comentarios', 'descripcion' => 'Ver comentarios'],
            
            // Permisos de Usuarios
            ['nombre' => 'crear_usuario', 'descripcion' => 'Crear nuevos usuarios'],
            ['nombre' => 'editar_usuario', 'descripcion' => 'Editar usuarios existentes'],
            ['nombre' => 'eliminar_usuario', 'descripcion' => 'Eliminar usuarios'],
            
            // Permisos de Configuración
            ['nombre' => 'ver_roles', 'descripcion' => 'Ver roles del sistema'],
            ['nombre' => 'ver_permisos', 'descripcion' => 'Ver permisos del sistema'],
            ['nombre' => 'ver_reportes', 'descripcion' => 'Ver reportes del sistema']
        ];

        foreach ($permisos as $permiso) {
            Permiso::create($permiso);
        }

        // 3. Asignar permisos a roles
        $superadmin = Role::where('nombre', 'Superadministrador')->first();
        $superadmin->permisos()->attach(Permiso::all());

        $admin = Role::where('nombre', 'Administrador')->first();
        $adminPermisos = Permiso::whereIn('nombre', [
            'ver_categorias',
            'ver_subcategorias',
            'ver_videos',
            'crear_video',
            'ver_comentarios',
            'crear_usuario',
            'editar_usuario'
        ])->pluck('id');
        $admin->permisos()->attach($adminPermisos);

        $cliente = Role::where('nombre', 'Cliente')->first();
        $clientePermisos = Permiso::whereIn('nombre', [
            'ver_categorias',
            'ver_subcategorias',
            'ver_videos'
        ])->pluck('id');
        $cliente->permisos()->attach($clientePermisos);
    }
}