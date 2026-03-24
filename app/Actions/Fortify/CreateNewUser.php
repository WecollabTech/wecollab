<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Models\Role;
use App\Models\Pais;
use App\Models\Estado;
use App\Models\Ciudad;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Illuminate\Support\Facades\Log; // Añadimos Log
use Illuminate\Support\Facades\Http; // Añadimos esto para hacer la solicitud HTTP
use Laravel\Jetstream\Jetstream;


class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;


    /**
     * Validar y crear un nuevo usuario.
     */
    // public function create(array $input): User
    // {
    //     // Validación consistente con el frontend
    //     Validator::make($input, [
    //         // Requeridos
    //         'name' => ['required', 'string', 'max:255'],
    //         'apellido' => ['required', 'string', 'max:255'],
    //         'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
    //         'password' => $this->passwordRules(),

    //         // Ubicación (requeridos)
    //         'country_code' => ['nullable', 'string', 'max:10'],
    //         'country_name' => ['nullable', 'string', 'max:255'],
    //         'state' => ['nullable', 'string', 'max:255'],
    //         'city' => ['nullable', 'string', 'max:255'],

    //         // Opcionales (nullable)
    //         'status' => ['nullable', 'in:activo,inactivo'], // Default: activo
    //         'direccion' => ['nullable', 'string', 'max:255'],
    //         'fotoperfil' => ['nullable', 'file', 'image', 'mimes:jpeg,png,gif', 'max:2048'], // 2MB
    //         'telefono' => ['nullable', 'string', 'max:15'],

    //         // Términos (condicional)
    //         'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : 'nullable',
    //     ], [
    //         // Mensajes personalizados
    //         'name.required' => 'El nombre es requerido',
    //         'apellido.required' => 'El apellido es requerido',
    //         'email.required' => 'El correo es requerido',
    //         'email.unique' => 'Este correo ya está registrado',
    //         'password.required' => 'La contraseña es requerida',
    //         'country_code.required' => 'El país es requerido',
    //         'state.required' => 'El estado es requerido',
    //         'city.required' => 'La ciudad es requerida',
    //         'terms.accepted' => 'Debes aceptar los términos y condiciones',
    //         'fotoperfil.image' => 'El archivo debe ser una imagen',
    //         'fotoperfil.mimes' => 'Formatos permitidos: JPEG, PNG, GIF',
    //         'fotoperfil.max' => 'La imagen no debe superar los 2MB',
    //     ])->validateWithBag('default');

    //     // ====================================================================
    //     // GESTIÓN DE UBICACIÓN (País → Estado → Ciudad)
    //     // ====================================================================

    //     $pais = Pais::firstOrCreate(
    //         ['codigo' => $input['country_code']],
    //         ['nombre' => $input['country_name']]
    //     );

    //     $estado = Estado::firstOrCreate(
    //         ['nombre' => $input['state'], 'pais_id' => $pais->id],
    //         ['pais_id' => $pais->id] // Evita duplicados
    //     );

    //     $ciudad = Ciudad::firstOrCreate(
    //         ['nombre' => $input['city'], 'estado_id' => $estado->id],
    //         ['estado_id' => $estado->id] // Evita duplicados
    //     );

    //     // ====================================================================
    //     // SUBIDA DE FOTO DE PERFIL
    //     // ====================================================================

    //     $fotoPath = null;
    //     if (isset($input['fotoperfil']) && $input['fotoperfil'] instanceof \Illuminate\Http\UploadedFile) {
    //         $fotoPath = $input['fotoperfil']->store('profile-photos', 'public');
    //     }

    //     // ====================================================================
    //     // ROL POR DEFECTO
    //     // ====================================================================

    //     $role = Role::firstOrCreate(
    //         ['nombre' => 'usuario'],
    //         ['descripcion' => 'Usuario estándar del sistema']
    //     );

    //     // ====================================================================
    //     // CREACIÓN DEL USUARIO
    //     // ====================================================================

    //     $user = User::create([
    //         'name' => $input['name'],
    //         'apellido' => $input['apellido'],
    //         'email' => $input['email'],
    //         'password' => Hash::make($input['password']),
    //         'status' => $input['status'] ?? 'activo', // Default: activo
    //         'direccion' => $input['direccion'] ?? null,
    //         'fotoperfil' => $fotoPath,
    //         'telefono' => $input['telefono'] ?? null,
    //         'pais_id' => $pais->id,
    //         'estado_id' => $estado->id, // ✅ Guardar estado
    //         'ciudad_id' => $ciudad->id, // ✅ Guardar ciudad
    //         'role_id' => $role->id,     // ✅ Guardar rol
    //     ]);

    //     return $user;
    // }


    public function create(array $input): User
    {
        // ====================================================================
        // ✅ VALIDACIÓN
        // ====================================================================
        Validator::make($input, [
            // Requeridos
            'name' => ['required', 'string', 'max:255'],
            'apellido' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => $this->passwordRules(),

            // Ubicación: país requerido, estado/ciudad opcionales
            'country_code' => ['required', 'string', 'max:10'],
            'country_name' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],

            // 🆕 Company ID (campo directo en users, opcional, numérico)
            'company_id' => ['nullable', 'numeric', 'min:1'],

            // Opcionales
            'status' => ['nullable', 'in:activo,inactivo'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'fotoperfil' => ['nullable', 'file', 'image', 'mimes:jpeg,png,gif', 'max:2048'],
            'telefono' => ['nullable', 'string', 'max:20'], // ↑ 20 para código internacional (+52...)

            // Términos
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature()
                ? ['accepted', 'required']
                : 'nullable',
        ], [
            // Mensajes personalizados
            'name.required' => 'El nombre es requerido',
            'apellido.required' => 'El apellido es requerido',
            'email.required' => 'El correo es requerido',
            'email.unique' => 'Este correo ya está registrado',
            'password.required' => 'La contraseña es requerida',
            'country_code.required' => 'El país es requerido',
            'company_id.numeric' => 'El ID de compañía debe ser un número válido',
            'company_id.min' => 'El ID de compañía debe ser mayor a 0',
            'terms.accepted' => 'Debes aceptar los términos y condiciones',
            'fotoperfil.image' => 'El archivo debe ser una imagen',
            'fotoperfil.mimes' => 'Formatos permitidos: JPEG, PNG, GIF',
            'fotoperfil.max' => 'La imagen no debe superar los 2MB',
        ])->validateWithBag('default');

        // ====================================================================
        // 🌍 GESTIÓN DE UBICACIÓN (País obligatorio, Estado/Ciudad opcionales)
        // ====================================================================

        // 1. País (SIEMPRE requerido)
        $pais = Pais::firstOrCreate(
            ['codigo' => $input['country_code']],
            ['nombre' => $input['country_name'] ?? $input['country_code']]
        );

        // 2. Estado (OPCIONAL - solo si se envió y no está vacío)
        $estado_id = null;
        if (!empty($input['state']) && !empty($input['country_name'])) {
            $estado = Estado::firstOrCreate(
                ['nombre' => $input['state'], 'pais_id' => $pais->id],
                ['pais_id' => $pais->id]
            );
            $estado_id = $estado->id;
        }

        // 3. Ciudad (OPCIONAL - solo si se envió estado y ciudad)
        $ciudad_id = null;
        if (!empty($input['city']) && $estado_id) {
            $ciudad = Ciudad::firstOrCreate(
                ['nombre' => $input['city'], 'estado_id' => $estado_id],
                ['estado_id' => $estado_id]
            );
            $ciudad_id = $ciudad->id;
        }

        // ====================================================================
        // 📸 SUBIDA DE FOTO DE PERFIL
        // ====================================================================
        $fotoPath = null;
        if (isset($input['fotoperfil']) && $input['fotoperfil'] instanceof \Illuminate\Http\UploadedFile) {
            $fotoPath = $input['fotoperfil']->store('profile-photos', 'public');
        }

        // ====================================================================
        // 🎭 ASIGNACIÓN DE ROL SEGÚN COMPANY_ID
        // ====================================================================

        // 🆕 Determinar rol: si hay company_id → "cliente_premium", sino → "usuario"
        $roleKey = !empty($input['company_id']) ? 'Cliente Premium' : 'usuario';
        $roleLabel = $roleKey === 'Cliente Premium' ? 'Cliente Premium' : 'Usuario';

        $role = Role::firstOrCreate(
            ['nombre' => $roleKey],  // nombre interno: 'cliente_premium' o 'usuario'
            [
                'descripcion' => $roleKey === 'cliente_premium'
                    ? 'Cliente Premium con acceso a compañía'
                    : 'Usuario estándar del sistema'
            ]
        );

        // ====================================================================
        // 👤 CREACIÓN DEL USUARIO
        // ====================================================================
        $user = User::create([
            'name' => $input['name'],
            'apellido' => $input['apellido'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
            'status' => $input['status'] ?? 'activo',
            'direccion' => $input['direccion'] ?? null,
            'fotoperfil' => $fotoPath,
            'telefono' => $input['telefono'] ?? null,
            'pais_id' => $pais->id,
            'estado_id' => $estado_id,   // ✅ Puede ser null
            'ciudad_id' => $ciudad_id,   // ✅ Puede ser null
            'company_id' => !empty($input['company_id']) ? (int) $input['company_id'] : null,  // 🆕 Campo directo en users
            'role_id' => $role->id,
        ]);

        // ====================================================================
        // 🔍 LOGGING PARA VERIFICAR GUARDADO CORRECTO
        // ====================================================================
        Log::info('✅ Usuario registrado - Verificación de datos', [
            'user_id' => $user->id,
            'email' => $user->email,
            'name' => $user->name . ' ' . $user->apellido,
            'company_id' => $user->company_id,
            'role_assigned' => [
                'key' => $roleKey,           // 'cliente_premium' o 'usuario'
                'label' => $roleLabel,       // 'Cliente Premium' o 'Usuario Estándar'
                'role_id' => $role->id,
            ],
            'location' => [
                'country' => $pais->nombre,
                'state_id' => $estado_id,
                'city_id' => $ciudad_id,
            ],
            'telefono' => $user->telefono,
            'created_at' => $user->created_at,
        ]);

        return $user;
    }



}