<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

use Illuminate\Support\Facades\Cache;


class User extends Authenticatable
{
    use HasApiTokens;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'apellido', // Nuevo campo
        'email',
        'password',
        'status', // Nuevo campo
        'direccion', // Nuevo campo
        'fotoperfil', // Nuevo campo
        'telefono', // Nuevo campo
        'role_id', // Nuevo campo
        'pais_id', // Nuevo campo
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    //relacion de la base de datos usuarios y roles para asingarle roles a los usuarios de la base de datos

    public function pais()
    {
        return $this->belongsTo(Pais::class, 'pais_id');
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

        /**
     * Verifica si el usuario tiene un rol específico
     */
    public function hasRole($roleName)
    {
        return $this->role->nombre === $roleName;
    }

        /**
     * Verifica si el usuario tiene un permiso específico
     */
    public function hasPermission($permissionName)
    {
        return Cache::remember("user_{$this->id}_permissions", now()->addDay(), function() {
            return $this->role->permisos->pluck('nombre')->toArray();
        });
        
        return in_array($permissionName, $this->getPermissions());
    }

    /**
     * Obtiene todos los permisos del usuario
     */
    public function getPermissions()
    {
        return $this->role->permisos->pluck('nombre')->toArray();
    }














  
    

    
}