<?php

namespace App\Providers;
use Illuminate\Auth\Events\Registered;
use App\Listeners\EnviarUsuarioABitrix;

use Illuminate\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        Registered::class => [
            EnviarUsuarioABitrix::class,
        ],
    ];
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}