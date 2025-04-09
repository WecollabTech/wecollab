<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;


class Role extends Model
{
    protected $fillable = [
        'nombre',
        'estado', // Nuevo campo
        'descripcion',
    ];

   /**
     * Scope para roles activos
     */
    public function scopeActive(Builder $query)
    {
        return $query->where('estado', 'activo');
    }

    /**
     * Relación muchos a muchos con Permiso
     */
    public function permisos()
    {
        return $this->belongsToMany(Permiso::class, 'role_permiso', 'role_id', 'permiso_id')
            ->withTimestamps();
    }

    /**
     * Relación con los usuarios que tienen este rol
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Asigna permisos al rol
     */
    public function assignPermissions(array $permissionIds)
    {
        $this->permisos()->sync($permissionIds);
    }


}