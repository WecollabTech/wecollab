<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BitrixService
{
    protected $baseUrl;

    // 🗂️ Mapeo de roles de Laravel → IDs de Bitrix
    protected const ROLE_MAPPING = [
        // Rol por defecto / estándar
        'usuario' => 5367,           // "Suscriptor SLC"

        // Roles con company_id
        'cliente_admin' => 5369,     // "Cliente Admin"
        'cliente_premium' => 5371,   // "Cliente Premium"

        // Otros roles (si los usas en el futuro)
        'superadmin' => 5363,        // "Superadmin We collab"
        'admin' => 5365,             // "Admin We collab"
        'publico' => 5373,           // "Usuario Público"
        'prospecto' => 5375,         // "Prospecto"
    ];

    // Valor por defecto si el rol no está mapeado
    protected const DEFAULT_ROLE_ID = 5367;  // "Suscriptor SLC"

    public function __construct()
    {
        $this->baseUrl = env('BITRIX_BASE');
    }

    public function crearContacto($user)
    {
        // 📱 Formatear teléfono con código de país
        $telefonoBitrix = $this->formatearTelefonoParaBitrix($user);

        // 🎭 Obtener ID del rol para Bitrix
        $roleIdBitrix = $this->obtenerRoleIdParaBitrix($user);

        $fields = [
            'NAME' => $user->name,
            'LAST_NAME' => $user->apellido,
            'EMAIL' => [
                ['VALUE' => $user->email, 'VALUE_TYPE' => 'WORK']
            ],
            'PHONE' => [
                ['VALUE' => $telefonoBitrix, 'VALUE_TYPE' => 'WORK']
            ],
            'ADDRESS' => $user->direccion,
        ];

        // 🆕 Agregar campo personalizado de rol SOLO si tiene valor
        if ($roleIdBitrix) {
            $fields['UF_CRM_1774285999281'] = $roleIdBitrix;
        }

        $response = Http::post($this->baseUrl . 'crm.contact.add.json', [
            'fields' => $fields
        ]);

        return $response->json()['result'] ?? null;
    }

    public function crearDeal($user, $contactId)
    {
        // 📱 Formatear teléfono para comentarios
        $telefonoBitrix = $this->formatearTelefonoParaBitrix($user);

        // 🎭 Obtener nombre legible del rol para comentarios
        $roleName = $this->obtenerNombreRolParaBitrix($user);

        Http::post($this->baseUrl . 'crm.deal.add.json', [
            'fields' => [
                'TITLE' => 'Nuevo usuario: ' . $user->name . ' ' . $user->apellido,
                'CONTACT_ID' => $contactId,

                // 🔥 TU EMBUDO SLC
                'CATEGORY_ID' => 69,
                'STAGE_ID' => 'C69:NEW',

                'COMMENTS' =>
                    "Email: {$user->email}\n" .
                    "Teléfono: {$telefonoBitrix}\n" .
                    "Rol: {$roleName}\n" .
                    "País: {$user->country_name}\n" .
                    "Estado: {$user->state}\n" .
                    "Ciudad: {$user->city}" .
                    ($user->company_id ? "\nCompany ID: {$user->company_id}" : ""),
            ]
        ]);
    }

    public function enviarUsuario($user)
    {
        try {
            $contactId = $this->crearContacto($user);

            if ($contactId) {
                $this->crearDeal($user, $contactId);
            }

            Log::info('✅ Usuario enviado a Bitrix', [
                'user_id' => $user->id,
                'email' => $user->email,
                'role_laravel' => $user->role?->nombre ?? 'sin_rol',
                'role_bitrix_id' => $this->obtenerRoleIdParaBitrix($user),
                'company_id' => $user->company_id,
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Bitrix error', [
                'user_id' => $user->id ?? null,
                'email' => $user->email ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * 🎭 Mapea el rol de Laravel al ID correspondiente en Bitrix
     */
    protected function obtenerRoleIdParaBitrix($user): ?int
    {
        // Obtener nombre del rol desde la relación
        $roleName = $user->role?->nombre ?? null;

        if (!$roleName) {
            return self::DEFAULT_ROLE_ID;
        }

        // Buscar en el mapeo, si no existe usar default
        return self::ROLE_MAPPING[$roleName] ?? self::DEFAULT_ROLE_ID;
    }

    /**
     * 🏷️ Obtiene el nombre legible del rol para mostrar en comentarios
     */
    protected function obtenerNombreRolParaBitrix($user): string
    {
        $roleName = $user->role?->nombre ?? 'usuario';

        $roleLabels = [
            'usuario' => 'Suscriptor SLC',
            'cliente_admin' => 'Cliente Admin',
            'cliente_premium' => 'Cliente Premium',
            'superadmin' => 'Superadmin We collab',
            'admin' => 'Admin We collab',
            'publico' => 'Usuario Público',
            'prospecto' => 'Prospecto',
        ];

        return $roleLabels[$roleName] ?? 'Suscriptor SLC';
    }

    /**
     * 📱 Formatea el teléfono para Bitrix agregando código de país si falta
     */
    protected function formatearTelefonoParaBitrix($user): string
    {
        $telefono = $user->telefono ?? '';

        // Si ya tiene código de país (+), retornar tal cual
        if (empty($telefono) || str_starts_with($telefono, '+')) {
            return $telefono;
        }

        // Mapping de códigos telefónicos
        $phoneCodes = [
            'AR' => '+54',
            'BO' => '+591',
            'BR' => '+55',
            'CL' => '+56',
            'CO' => '+57',
            'CR' => '+506',
            'CU' => '+53',
            'EC' => '+593',
            'SV' => '+503',
            'GT' => '+502',
            'HN' => '+504',
            'MX' => '+52',
            'NI' => '+505',
            'PA' => '+507',
            'PY' => '+595',
            'PE' => '+51',
            'DO' => '+1',
            'UY' => '+598',
            'VE' => '+58',
            'ES' => '+34',
            'US' => '+1',
            'CA' => '+1',
            'GB' => '+44',
            'DE' => '+49',
            'FR' => '+33',
            'IT' => '+39',
            'PT' => '+351',
            'AU' => '+61',
            'JP' => '+81',
            'CN' => '+86',
            'IN' => '+91',
            'RU' => '+7',
            'ZA' => '+27',
            'NG' => '+234',
            'KE' => '+254',
            'EG' => '+20',
            'SA' => '+966',
            'AE' => '+971',
            'IL' => '+972',
            'TR' => '+90',
        ];

        // Obtener código del país del usuario
        $codigoPais = $user->pais?->codigo
            ? ($phoneCodes[$user->pais->codigo] ?? '')
            : '';

        // Si no hay código de país, retornar teléfono original
        if (!$codigoPais) {
            return $telefono;
        }

        // Limpiar número y concatenar: +52 + 9191185654 = +529191185654
        $numeroLimpio = preg_replace('/\D/', '', $telefono);

        return $codigoPais . $numeroLimpio;
    }
}