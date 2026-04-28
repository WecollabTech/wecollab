<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class TipoMaterial extends Model
{
    use HasFactory;
    protected $table = 'tipos_materiales';
    protected $fillable = [
        'nombre',
        'descripcion'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];




    /**
     * Relación con los tutoriales que usan este tipo de material
     */
    public function tutoriales()
    {
        return $this->hasMany(Tutorial::class, 'tipo_material_id');
    }

    /**
     * Contar cuántos tutoriales usan este tipo
     */
    public function getTutorialesCountAttribute(): int
    {
        return $this->tutoriales()->count();
    }

    /**
     * Verificar si el tipo está siendo usado en algún tutorial
     */
    public function isUsed(): bool
    {
        return $this->tutoriales()->exists();
    }

    /**
     * Scope para buscar por nombre
     */
    public function scopeSearch($query, $search)
    {
        if ($search) {
            return $query->where('nombre', 'LIKE', "%{$search}%");
        }
        return $query;
    }

}