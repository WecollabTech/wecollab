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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Laravel\Jetstream\Jetstream;
use Illuminate\Validation\ValidationException;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    public function create(array $input): User
    {
        // ====================================================================
        // ✅ VALIDACIÓN INICIAL
        // ====================================================================
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'apellido' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => $this->passwordRules(),
            'country_code' => ['required', 'string', 'max:10'],
            'country_name' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'company_id' => ['nullable', 'numeric', 'min:1'],
            'status' => ['nullable', 'in:activo,inactivo'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'fotoperfil' => ['nullable', 'file', 'image', 'mimes:jpeg,png,gif', 'max:2048'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature()
                ? ['accepted', 'required']
                : 'nullable',
        ], [
            'name.required' => 'El nombre es requerido',
            'apellido.required' => 'El apellido es requerido',
            'email.required' => 'El correo es requerido',
            'email.unique' => 'Este correo ya está registrado',
            'password.required' => 'La contraseña es requerida',
            'country_code.required' => 'El país es requerido',
            'company_id.numeric' => 'El ID de compañía debe ser un número válido',
            'company_id.min' => 'El ID de compañía debe ser mayor a 0',
            'terms.accepted' => 'Debes aceptar los términos y condiciones',
        ])->validateWithBag('default');

        // ====================================================================
        // 🚫 VERIFICAR SI EL EMAIL YA EXISTE EN LARAVEL
        // ====================================================================
        if (User::where('email', $input['email'])->exists()) {
            throw ValidationException::withMessages([
                'email' => 'Este correo electrónico ya está registrado en el sistema.'
            ]);
        }

        // ====================================================================
        // 🔗 CONFIGURACIÓN DE BITRIX24
        // ====================================================================
        $webhookUrl = config('services.bitrix24.webhook_url');

        if (empty($webhookUrl)) {
            Log::error('BITRIX_BASE no configurada');
            throw ValidationException::withMessages([
                'email' => 'Error de configuración del sistema.'
            ]);
        }

        $email = $input['email'];
        $submittedCompanyId = !empty($input['company_id']) ? (int) $input['company_id'] : null;

        // ====================================================================
        // 📞 PASO 1: BUSCAR CONTACTO EXISTENTE POR EMAIL
        // ====================================================================

        $existingContact = $this->findContactByEmail($email, $webhookUrl);
        $contactId = null;
        $finalCompanyId = $submittedCompanyId;
        $companyType = null;
        $contactAction = null;

        if ($existingContact) {
            // ✅ CONTACTO YA EXISTE - REUTILIZAR
            $contactId = $existingContact['id'];
            $contactAction = 'existing_used';

            Log::info('📞 Contacto EXISTENTE encontrado - Reutilizando', [
                'email' => $email,
                'contact_id' => $contactId,
                'existing_company_id' => $existingContact['company_id']
            ]);

            // Usar la compañía que YA tiene el contacto (si existe)
            if (!empty($existingContact['company_id'])) {
                $finalCompanyId = (int) $existingContact['company_id'];
                Log::info('🏢 Usando compañía del contacto existente', [
                    'company_id' => $finalCompanyId
                ]);
            }

            // Validar que la compañía existe en Bitrix24
            if ($finalCompanyId) {
                $companyInfo = $this->getCompanyInfo($finalCompanyId, $webhookUrl);
                if ($companyInfo) {
                    $companyType = $companyInfo['company_type'];
                    Log::info('✅ Compañía validada', [
                        'company_id' => $finalCompanyId,
                        'company_type' => $companyType
                    ]);
                } else {
                    Log::warning('⚠️ Compañía del contacto ya no existe', [
                        'company_id' => $finalCompanyId
                    ]);
                    $finalCompanyId = null;
                }
            }

            // Actualizar datos del contacto existente
            $this->updateContactData($contactId, $input, $webhookUrl);
            $contactAction = 'existing_updated';

        } else {
            // ❌ CONTACTO NO EXISTE - Crear nuevo
            Log::info('📞 Contacto NO existe - Se creará nuevo', [
                'email' => $email,
                'submitted_company_id' => $submittedCompanyId
            ]);

            // Validar compañía si se envió
            if ($submittedCompanyId) {
                $companyInfo = $this->getCompanyInfo($submittedCompanyId, $webhookUrl);
                if ($companyInfo) {
                    $finalCompanyId = $submittedCompanyId;
                    $companyType = $companyInfo['company_type'];
                    Log::info('✅ Compañía validada para nuevo contacto', [
                        'company_id' => $finalCompanyId,
                        'company_type' => $companyType
                    ]);
                } else {
                    throw ValidationException::withMessages([
                        'company_id' => 'El ID de compañía proporcionado no existe en Bitrix24.'
                    ]);
                }
            }

            // Crear nuevo contacto
            $contactId = $this->createNewContact($input, $finalCompanyId, $webhookUrl);

            if (!$contactId) {
                throw ValidationException::withMessages([
                    'email' => 'Error al crear el contacto en Bitrix24.'
                ]);
            }
            $contactAction = 'created';
        }

        // ====================================================================
        // 🌍 GESTIÓN DE UBICACIÓN
        // ====================================================================
        $pais = Pais::firstOrCreate(
            ['codigo' => $input['country_code']],
            ['nombre' => $input['country_name'] ?? $input['country_code']]
        );

        $estado_id = null;
        if (!empty($input['state'])) {
            $estado = Estado::firstOrCreate(
                ['nombre' => $input['state'], 'pais_id' => $pais->id],
                ['pais_id' => $pais->id]
            );
            $estado_id = $estado->id;
        }

        $ciudad_id = null;
        if (!empty($input['city']) && $estado_id) {
            $ciudad = Ciudad::firstOrCreate(
                ['nombre' => $input['city'], 'estado_id' => $estado_id],
                ['estado_id' => $estado_id]
            );
            $ciudad_id = $ciudad->id;
        }

        // ====================================================================
        // 📸 SUBIDA DE FOTO
        // ====================================================================
        $fotoPath = null;
        if (isset($input['fotoperfil']) && $input['fotoperfil'] instanceof \Illuminate\Http\UploadedFile) {
            $fotoPath = $input['fotoperfil']->store('profile-photos', 'public');
        }

        // ====================================================================
        // 🎭 ASIGNAR ROL
        // ====================================================================
        $role = $this->getRoleByCompanyType($finalCompanyId, $companyType);

        // ====================================================================
        // 👤 CREAR USUARIO EN LARAVEL
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
            'estado_id' => $estado_id,
            'ciudad_id' => $ciudad_id,
            'company_id' => $finalCompanyId,
            'role_id' => $role->id,
        ]);

        // ====================================================================
        // 📊 LOGGING FINAL
        // ====================================================================
        Log::info('✅ Usuario registrado exitosamente', [
            'user_id' => $user->id,
            'email' => $user->email,
            'name' => $user->name . ' ' . $user->apellido,
            'company_id' => $finalCompanyId,
            'company_type' => $companyType,
            'bitrix_contact_id' => $contactId,
            'bitrix_contact_action' => $contactAction,
            'role_assigned' => $role->nombre,
        ]);

        return $user;
    }

    // ====================================================================
    // 🔍 MÉTODOS PRIVADOS
    // ====================================================================

    /**
     * Busca un contacto por email en Bitrix24
     */
    private function findContactByEmail(string $email, string $webhookUrl): ?array
    {
        try {
            $response = Http::timeout(10)
                ->get($webhookUrl . 'crm.contact.list.json', [
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
                    'company_id' => !empty($contact['COMPANY_ID']) ? (int) $contact['COMPANY_ID'] : null,
                    'name' => $contact['NAME'] ?? null,
                    'last_name' => $contact['LAST_NAME'] ?? null
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

    /**
     * Obtiene información de una compañía en Bitrix24
     */
    private function getCompanyInfo(int $companyId, string $webhookUrl): ?array
    {
        try {
            $response = Http::timeout(10)
                ->get($webhookUrl . 'crm.company.get.json', ['id' => $companyId]);

            if (!$response->successful()) {
                return null;
            }

            $data = $response->json();

            if (isset($data['error']) || !isset($data['result'])) {
                return null;
            }

            return [
                'id' => (int) $data['result']['ID'],
                'company_type' => $data['result']['COMPANY_TYPE'] ?? null,
                'title' => $data['result']['TITLE'] ?? null
            ];

        } catch (\Exception $e) {
            Log::error('Error al obtener compañía', [
                'company_id' => $companyId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Actualiza los datos de un contacto existente
     */
    private function updateContactData(int $contactId, array $userData, string $webhookUrl): bool
    {
        try {
            $fields = [];

            if (!empty($userData['name'])) {
                $fields['NAME'] = $userData['name'];
            }
            if (!empty($userData['apellido'])) {
                $fields['LAST_NAME'] = $userData['apellido'];
            }
            if (!empty($userData['telefono'])) {
                $fields['PHONE'] = [['VALUE' => $userData['telefono'], 'VALUE_TYPE' => 'WORK']];
            }

            if (empty($fields)) {
                return true;
            }

            $response = Http::timeout(10)
                ->post($webhookUrl . 'crm.contact.update.json', [
                    'id' => $contactId,
                    'fields' => $fields
                ]);

            if ($response->successful()) {
                Log::info('Contacto actualizado', [
                    'contact_id' => $contactId,
                    'fields' => array_keys($fields)
                ]);
                return true;
            }

            return false;

        } catch (\Exception $e) {
            Log::error('Error al actualizar contacto', [
                'contact_id' => $contactId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Crea un nuevo contacto en Bitrix24
     */
    private function createNewContact(array $userData, ?int $companyId, string $webhookUrl): ?int
    {
        try {
            $contactData = [
                'NAME' => $userData['name'],
                'LAST_NAME' => $userData['apellido'],
                'EMAIL' => [['VALUE' => $userData['email'], 'VALUE_TYPE' => 'WORK']],
            ];

            if (!empty($userData['telefono'])) {
                $contactData['PHONE'] = [['VALUE' => $userData['telefono'], 'VALUE_TYPE' => 'WORK']];
            }

            if (!empty($companyId)) {
                $contactData['COMPANY_ID'] = $companyId;
            }

            $response = Http::timeout(10)
                ->post($webhookUrl . 'crm.contact.add.json', ['fields' => $contactData]);

            if (!$response->successful()) {
                Log::error('Error al crear contacto', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return null;
            }

            $data = $response->json();

            if (isset($data['result'])) {
                Log::info('Contacto creado', [
                    'contact_id' => $data['result'],
                    'email' => $userData['email'],
                    'company_id' => $companyId
                ]);
                return (int) $data['result'];
            }

            return null;

        } catch (\Exception $e) {
            Log::error('Excepción al crear contacto', [
                'email' => $userData['email'],
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Obtiene el rol según el tipo de compañía
     */
    private function getRoleByCompanyType(?int $companyId, ?string $companyType): Role
    {
        if (empty($companyId)) {
            return $this->getPublicUserRole();
        }

        if ($companyType === 'CUSTOMER') {
            return $this->getAdminSlcRole();
        }

        return $this->getPublicUserRole();
    }

    private function getPublicUserRole(): Role
    {
        return Role::firstOrCreate(
            ['nombre' => 'Usuario Publico'],
            ['descripcion' => 'Usuario público sin privilegios administrativos']
        );
    }

    private function getAdminSlcRole(): Role
    {
        return Role::firstOrCreate(
            ['nombre' => 'Usuario Admin SLC'],
            ['descripcion' => 'Administrador de SLC con acceso premium']
        );
    }
}