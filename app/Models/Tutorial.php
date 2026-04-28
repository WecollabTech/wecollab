<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tutorial extends Model
{
    use HasFactory;

    protected $table = 'tutoriales';

    protected $fillable = [
        'titulo',
        'descripcion',
        'tipo_material',
        'formato',
        'alcance',
        'estado',
        'url',
        'observacion',
        'categorias_id',      // ✅ CORRECTO: con S
        'subcategoria_id',
        'tipo_material_id',      // 👈 NUEVO
        'formato_material_id',   // 👈 NUEVO
        'user_id',
        'vistas'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'vistas' => 'integer'
    ];

    // ✅ Relacion con Categoria (usando categorias_id)
    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'categorias_id');
    }

    // Relacion con Subcategoria
    public function subcategoria()
    {
        return $this->belongsTo(Subcategoria::class, 'subcategoria_id');
    }

    // Relacion con usuario
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    public function tipoMaterial()
    {
        return $this->belongsTo(TipoMaterial::class, 'tipo_material_id');
    }


    public function formatoMaterial()
    {
        return $this->belongsTo(FormatoMaterial::class, 'formato_material_id');
    }




    public function hasTipoMaterial(): bool
    {
        return !is_null($this->tipo_material_id);
    }
    /**
     * Verificar si el tutorial tiene formato de material asignado
     */
    public function hasFormatoMaterial(): bool
    {
        return !is_null($this->formato_material_id);
    }


    /**
     * Obtener el nombre del tipo de material
     */
    public function getTipoMaterialNombreAttribute(): string
    {
        return $this->tipoMaterial?->nombre ?? 'Sin asignar';
    }

    /**
     * Obtener el nombre del formato de material
     */
    public function getFormatoMaterialNombreAttribute(): string
    {
        return $this->formatoMaterial?->nombre ?? 'Sin asignar';
    }










    // Scopes
    public function scopeActivo($query)
    {
        return $query->where('estado', 'activo');
    }

    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo_material', $tipo);
    }

    public function scopePorCategoria($query, $categoriaId)
    {
        return $query->where('categorias_id', $categoriaId);
    }

    public function scopePorSubcategoria($query, $subcategoriaId)
    {
        return $query->where('subcategoria_id', $subcategoriaId);
    }
}