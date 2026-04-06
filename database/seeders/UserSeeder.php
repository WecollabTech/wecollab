<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('SynergyFlow2026*');

        $users = [
            [
                'name' => 'SuperAdminJose',
                'apellido' => 'WeCollab',
                'email' => 'superadminjose@we-collab.tech',
                'password' => $password,
                'status' => 'activo',
                'company_id' => 'WECOLLAB001',
                'pais_id' => 1,
                'telefono' => '+525500000001',
                'role_id' => 1, // Superadmin
            ],
            [
                'name' => 'SuperAdminVictor',
                'apellido' => 'WeCollab',
                'email' => 'superadminvictor@we-collab.tech',
                'password' => $password,
                'status' => 'activo',
                'company_id' => 'WECOLLAB001',
                'pais_id' => 1,
                'telefono' => '+525500000002',
                'role_id' => 1, // Superadmin
            ],
            [
                'name' => 'AdminWecollabAndres',
                'apellido' => 'Administrador',
                'email' => 'adminandres@we-collab.tech',
                'password' => $password,
                'status' => 'activo',
                'company_id' => 'WECOLLAB001',
                'pais_id' => 2,
                'telefono' => '+525500000003',
                'role_id' => 2, // Admin we collab
            ],
            [
                'name' => 'UsuarioPublicoVic',
                'apellido' => 'Publico',
                'email' => 'publicovic@we-collab.tech',
                'password' => $password,
                'status' => 'activo',
                'company_id' => null,
                'pais_id' => 1,
                'telefono' => '+525500000004',
                'role_id' => 8, // Usuario Público
            ],
            [
                'name' => 'UsuarioPublicoJose',
                'apellido' => 'Publico',
                'email' => 'publicojose@we-collab.tech',
                'password' => $password,
                'status' => 'activo',
                'company_id' => null,
                'pais_id' => 1,
                'telefono' => '+525500000005',
                'role_id' => 8, // Usuario Público
            ],
        ];

        foreach ($users as $user) {
            DB::table('users')->updateOrInsert(
                ['email' => $user['email']],
                array_merge($user, [
                    'updated_at' => now(),
                    'created_at' => DB::raw('COALESCE(created_at, NOW())'),
                ])
            );
        }
    }
}