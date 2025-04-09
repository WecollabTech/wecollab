<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;



class Permiso extends Model
{
    use HasFactory;
    //
    protected $fillable = [
        'nombre',
        'estado',
        'descripcion',
    ];

    protected $casts = [
        'estado' => 'string', // Cast para el campo 'estado'
    ];


    /**
     * Scope para permisos activos
     */
    public function scopeActive(Builder $query)
    {
        return $query->where('estado', 'activo');
    }

    /**
     * Relación muchos a muchos con Role
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permiso', 'permiso_id', 'role_id')
            ->withTimestamps();
    }
}