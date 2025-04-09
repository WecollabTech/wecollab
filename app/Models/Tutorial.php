<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tutorial extends Model
{
    use HasFactory;
    protected $table = 'tutoriales'; // Especifica explícitamente el nombre de la tabla

    protected $fillable = [
        'titulo',
        'descripcion',
        'tipo_material',
        'formato',
        'alcance',
        'estado',
        'url',
        'observacion',
        'subcategoria_id',
        'user_id'
    ];




    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    

    /**
     * Relación con la subcategoría.
     */
    public function subcategoria()
    {
        return $this->belongsTo(Subcategoria::class);
    }

    /**
     * Relación con el usuario que creó el tutorial.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}