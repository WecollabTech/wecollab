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
                'name' => 'Jose Gonzalez',
                'apellido' => 'Leon',
                'email' => 'superadminjose@we-collab.tech',
                'password' => $password,
                'status' => 'activo',
                'company_id' => 'WECOLLAB001',
                'pais_id' => 1,
                'telefono' => '+525500000001',
                'role_id' => 1, // Superadmin
            ],
            [
                'name' => 'Victor Antonio',
                'apellido' => 'Gomez',
                'email' => 'superadminvictor@we-collab.tech',
                'password' => $password,
                'status' => 'activo',
                'company_id' => 'WECOLLAB001',
                'pais_id' => 1,
                'telefono' => '+525500000002',
                'role_id' => 1, // Superadmin
            ],
            [
                'name' => 'Andres',
                'apellido' => 'Marin',
                'email' => 'adminandres@we-collab.tech',
                'password' => $password,
                'status' => 'activo',
                'company_id' => 'WECOLLAB001',
                'pais_id' => 2,
                'telefono' => '+525500000003',
                'role_id' => 2, // Admin we collab
            ],
            [
                'name' => 'Victor',
                'apellido' => 'Gomez',
                'email' => 'victorgomez@we-collab.tech',
                'password' => $password,
                'status' => 'activo',
                'company_id' => null,
                'pais_id' => 1,
                'telefono' => '+525500000004',
                'role_id' => 8, // Usuario Público
            ],
            [
                'name' => 'Jose ',
                'apellido' => 'Gonzalez',
                'email' => 'josegonzalez@we-collab.tech',
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