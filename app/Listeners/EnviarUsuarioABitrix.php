<?php

namespace App\Listeners;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use App\Services\BitrixService;

class EnviarUsuarioABitrix
{
    protected $bitrix;

    public function __construct(BitrixService $bitrix)
    {
        $this->bitrix = $bitrix;
    }

    public function handle(Registered $event)
    {
        /** @var User $user */
        $user = $event->user;

        $this->bitrix->enviarUsuario($user);
    }
}