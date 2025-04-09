<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pais extends Model
{
    protected $table = 'paises';
    protected $fillable = ['nombre', 'codigo'];

    public function estados()
    {
        return $this->hasMany(Estado::class, 'pais_id');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'pais_id');
    }
}