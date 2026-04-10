<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BitrixService
{
    protected $baseUrl;

    // 🗂️ MAPEO DE ROLES
    protected const ROLE_MAPPING = [
        'superadmin' => 5363,
        'admin' => 5365,
        'soporte' => 5585,
        'usuario_we_collab' => 5583,
        'usuario' => 5367,
        'Usuario Admin SLC' => 5369,
        'Usuario Premium SLC' => 5371,
        'Usuario Publico' => 5373,
        'prospecto' => 5375,
        'Usuario Admin' => 5369,
        'Admin SLC' => 5369,
        'Usuario Premium' => 5371,
        'Premium SLC' => 5371,
        'Publico' => 5373,
    ];

    protected const DEFAULT_ROLE_ID = 5367;
    protected const BITRIX_ROLE_FIELD = 'UF_CRM_1774285999281';

    public function __construct()
    {
        $this->baseUrl = env('BITRIX_BASE');
    }

    public function enviarUsuario($user)
    {
        try {
            $roleName = !empty($user->role->nombre) ? $user->role->nombre : 'usuario';
            $roleBitrixId = $this->obtenerRoleIdPorNombre($roleName);

            Log::info('🎭 Rol asignado al usuario', [
                'user_id' => $user->id,
                'email' => $user->email,
                'role_laravel' => $roleName,
                'role_bitrix_id' => $roleBitrixId
            ]);

            $existingContact = $this->findContactByEmail($user->email);
            $contactId = null;

            if ($existingContact) {
                $contactId = $existingContact['id'];
                $this->actualizarRolContacto($contactId, $roleBitrixId);
                $this->agregarTelefonoSiNuevo($contactId, $user);

                Log::info('🔄 Rol actualizado en contacto existente', [
                    'contact_id' => $contactId,
                    'new_role_id' => $roleBitrixId,
                    'role_name' => $roleName
                ]);
            } else {
                $contactId = $this->crearContacto($user, $roleBitrixId);
                Log::info('➕ Contacto creado con rol', [
                    'contact_id' => $contactId,
                    'role_id' => $roleBitrixId,
                    'role_name' => $roleName
                ]);
            }

            if ($contactId) {
                $existingDeal = $this->findDealByContactId($contactId);

                if (!$existingDeal) {
                    $this->crearDeal($user, $contactId, $roleName, $roleBitrixId);
                    Log::info('➕ Negociación creada', [
                        'user_id' => $user->id,
                        'contact_id' => $contactId
                    ]);
                } else {
                    Log::info('🔄 Negociación ya existe', [
                        'user_id' => $user->id,
                        'contact_id' => $contactId,
                        'deal_id' => $existingDeal['id']
                    ]);
                }
            }

            Log::info('✅ Usuario sincronizado con Bitrix', [
                'user_id' => $user->id,
                'email' => $user->email,
                'role_laravel' => $roleName,
                'role_bitrix_id' => $roleBitrixId,
                'contact_id' => $contactId
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Bitrix error', [
                'user_id' => !empty($user->id) ? $user->id : null,
                'email' => !empty($user->email) ? $user->email : null,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function obtenerRoleIdPorNombre(string $roleName): int
    {
        if (isset(self::ROLE_MAPPING[$roleName])) {
            return self::ROLE_MAPPING[$roleName];
        }
        return self::DEFAULT_ROLE_ID;
    }

    private function actualizarRolContacto(int $contactId, int $roleBitrixId): bool
    {
        try {
            $response = Http::post($this->baseUrl . 'crm.contact.update.json', [
                'id' => $contactId,
                'fields' => [
                    self::BITRIX_ROLE_FIELD => $roleBitrixId
                ]
            ]);

            if ($response->successful()) {
                Log::info('Rol actualizado en Bitrix', [
                    'contact_id' => $contactId,
                    'role_bitrix_id' => $roleBitrixId
                ]);
                return true;
            }

            return false;

        } catch (\Exception $e) {
            Log::error('Error al actualizar rol', [
                'contact_id' => $contactId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * 📱 Agrega teléfono SOLO si es diferente a los existentes
     */
    private function agregarTelefonoSiNuevo(int $contactId, $user): void
    {
        $newPhone = $this->formatearTelefonoParaBitrix($user);

        if (empty($newPhone)) {
            Log::info('📱 No hay teléfono para agregar', [
                'contact_id' => $contactId
            ]);
            return;
        }

        try {
            // Obtener teléfonos actuales
            $response = Http::timeout(10)
                ->get($this->baseUrl . 'crm.contact.get.json', [
                    'id' => $contactId
                ]);

            if (!$response->successful()) {
                Log::warning('No se pudo obtener teléfonos actuales', [
                    'contact_id' => $contactId
                ]);
                return;
            }

            $data = $response->json();
            $currentPhones = isset($data['result']['PHONE']) ? $data['result']['PHONE'] : [];

            // Limpiar nuevo teléfono
            $newPhoneClean = preg_replace('/\D/', '', $newPhone);

            // Verificar si ya existe
            $phoneExists = false;
            foreach ($currentPhones as $phone) {
                $existingPhoneClean = preg_replace('/\D/', '', $phone['VALUE']);
                if ($existingPhoneClean === $newPhoneClean) {
                    $phoneExists = true;
                    break;
                }
            }

            // Si ya existe, NO hacer nada
            if ($phoneExists) {
                Log::info('📱 Teléfono ya existe - No se agrega duplicado', [
                    'contact_id' => $contactId,
                    'phone' => $newPhone,
                    'existing_phones' => count($currentPhones)
                ]);
                return;
            }

            // Agregar nuevo teléfono
            $currentPhones[] = [
                'VALUE' => $newPhone,
                'VALUE_TYPE' => 'WORK'
            ];

            $updateResponse = Http::post($this->baseUrl . 'crm.contact.update.json', [
                'id' => $contactId,
                'fields' => ['PHONE' => $currentPhones]
            ]);

            if ($updateResponse->successful()) {
                Log::info('📱 Nuevo teléfono agregado (era diferente)', [
                    'contact_id' => $contactId,
                    'new_phone' => $newPhone,
                    'total_phones' => count($currentPhones)
                ]);
            } else {
                Log::error('Error al agregar nuevo teléfono', [
                    'contact_id' => $contactId,
                    'phone' => $newPhone
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Error al procesar teléfono', [
                'contact_id' => $contactId,
                'error' => $e->getMessage()
            ]);
        }
    }

    private function findContactByEmail(string $email): ?array
    {
        try {
            $response = Http::timeout(10)
                ->get($this->baseUrl . 'crm.contact.list.json', [
                    'filter' => ['EMAIL' => $email],
                    'select' => ['ID', 'COMPANY_ID', 'NAME', 'LAST_NAME']
                ]);

            if (!$response->successful()) {
                return null;
            }

            $data = $response->json();

            if (isset($data['result']) && !empty($data['result'])) {
                $contact = $data['result'][0];
                return [
                    'id' => (int) $contact['ID'],
                    'company_id' => !empty($contact['COMPANY_ID']) ? (int) $contact['COMPANY_ID'] : null
                ];
            }

            return null;

        } catch (\Exception $e) {
            Log::error('Error al buscar contacto', [
                'email' => $email,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    private function findDealByContactId(int $contactId): ?array
    {
        try {
            $response = Http::timeout(10)
                ->get($this->baseUrl . 'crm.deal.list.json', [
                    'filter' => ['CONTACT_ID' => $contactId],
                    'select' => ['ID', 'TITLE']
                ]);

            if (!$response->successful()) {
                return null;
            }

            $data = $response->json();

            if (isset($data['result']) && !empty($data['result'])) {
                $deal = $data['result'][0];
                return [
                    'id' => (int) $deal['ID'],
                    'title' => isset($deal['TITLE']) ? $deal['TITLE'] : null
                ];
            }

            return null;

        } catch (\Exception $e) {
            return null;
        }
    }

    public function crearContacto($user, int $roleBitrixId)
    {
        $telefonoBitrix = $this->formatearTelefonoParaBitrix($user);

        $fields = [
            'NAME' => $user->name,
            'LAST_NAME' => $user->apellido,
            'EMAIL' => [
                ['VALUE' => $user->email, 'VALUE_TYPE' => 'WORK']
            ],
            'ADDRESS' => $user->direccion,
            self::BITRIX_ROLE_FIELD => $roleBitrixId
        ];

        if (!empty($telefonoBitrix)) {
            $fields['PHONE'] = [
                ['VALUE' => $telefonoBitrix, 'VALUE_TYPE' => 'WORK']
            ];
        }

        if ($user->company_id) {
            $fields['COMPANY_ID'] = $user->company_id;
        }

        $response = Http::post($this->baseUrl . 'crm.contact.add.json', [
            'fields' => $fields
        ]);

        $result = $response->json();
        $contactId = isset($result['result']) ? $result['result'] : null;

        if ($contactId) {
            Log::info('Contacto creado con rol', [
                'contact_id' => $contactId,
                'email' => $user->email,
                'role_bitrix_id' => $roleBitrixId
            ]);
        }

        return $contactId;
    }

    public function crearDeal($user, $contactId, string $roleName, int $roleBitrixId)
    {
        $telefonoBitrix = $this->formatearTelefonoParaBitrix($user);

        $response = Http::post($this->baseUrl . 'crm.deal.add.json', [
            'fields' => [
                'TITLE' => 'Nuevo usuario: ' . $user->name . ' ' . $user->apellido,
                'CONTACT_ID' => $contactId,
                'CATEGORY_ID' => 69,
                'STAGE_ID' => 'C69:NEW',
                'COMMENTS' =>
                    "📋 DATOS DEL USUARIO\n" .
                    "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n" .
                    "📧 Email: {$user->email}\n" .
                    "📱 Teléfono: {$telefonoBitrix}\n" .
                    "🎭 Rol asignado: {$roleName} (ID Bitrix: {$roleBitrixId})\n" .
                    "🏢 Company ID: " . ($user->company_id ? $user->company_id : 'Ninguno') . "\n" .
                    "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n" .
                    "📅 Registro: " . now()->format('d/m/Y H:i:s'),
            ]
        ]);

        $result = $response->json();
        Log::info('Negociación creada', [
            'contact_id' => $contactId,
            'role_bitrix_id' => $roleBitrixId,
            'deal_id' => isset($result['result']) ? $result['result'] : 'error'
        ]);
    }

    protected function formatearTelefonoParaBitrix($user): string
    {
        $telefono = isset($user->telefono) ? $user->telefono : '';

        if (empty($telefono) || str_starts_with($telefono, '+')) {
            return $telefono;
        }

        $phoneCodes = [
            'MX' => '+52',
            'US' => '+1',
            'CA' => '+1',
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
            'NI' => '+505',
            'PA' => '+507',
            'PY' => '+595',
            'PE' => '+51',
            'DO' => '+1',
            'UY' => '+598',
            'VE' => '+58',
            'ES' => '+34',
            'GB' => '+44',
            'DE' => '+49',
            'FR' => '+33',
            'IT' => '+39',
            'PT' => '+351',
            'AU' => '+61',
        ];

        $codigoPais = '';
        if (!empty($user->pais->codigo) && isset($phoneCodes[$user->pais->codigo])) {
            $codigoPais = $phoneCodes[$user->pais->codigo];
        }

        if (!$codigoPais) {
            return $telefono;
        }

        $numeroLimpio = preg_replace('/\D/', '', $telefono);
        return $codigoPais . $numeroLimpio;
    }
}