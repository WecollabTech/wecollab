<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('permisos', function (Blueprint $table) {
            $table->id(); // Columna 'id' (PK, autoincremental)
            $table->string('nombre', 50); // Columna 'nombre' (VARCHAR(50))
            $table->enum('estado', ['activo', 'inactivo'])->default('activo'); // Columna 'estado' (ENUM)
            $table->string('descripcion', 255)->nullable(); // Columna 'descripción' (VARCHAR(255), nullable
            $table->timestamps(); // Columnas 'created_at' y 'updated_at'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permisos');
    }
};
