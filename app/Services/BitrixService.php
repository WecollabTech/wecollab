<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BitrixService
{
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = env('BITRIX_BASE');
    }

    public function crearContacto($user)
    {
        // 📱 Asegurar que el teléfono tenga código de país
        $telefonoBitrix = $this->formatearTelefonoParaBitrix($user);

        $response = Http::post($this->baseUrl . 'crm.contact.add.json', [
            'fields' => [
                'NAME' => $user->name,
                'LAST_NAME' => $user->apellido,
                'EMAIL' => [
                    ['VALUE' => $user->email, 'VALUE_TYPE' => 'WORK']
                ],
                'PHONE' => [
                    ['VALUE' => $telefonoBitrix, 'VALUE_TYPE' => 'WORK']
                ],
                'ADDRESS' => $user->direccion,
            ]
        ]);

        return $response->json()['result'] ?? null;
    }

    public function crearDeal($user, $contactId)
    {
        // 📱 Formatear teléfono para comentarios
        $telefonoBitrix = $this->formatearTelefonoParaBitrix($user);

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
                    "País: {$user->country_name}\n" .
                    "Estado: {$user->state}\n" .
                    "Ciudad: {$user->city}",
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

        } catch (\Exception $e) {
            Log::error('Bitrix error: ' . $e->getMessage(), [
                'user_id' => $user->id ?? null,
                'telefono_original' => $user->telefono ?? null,
            ]);
        }
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

        // Mapping de códigos telefónicos (mismo que en frontend)
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