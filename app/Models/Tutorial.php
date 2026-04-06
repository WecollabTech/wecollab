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