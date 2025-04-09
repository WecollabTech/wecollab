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
        Schema::create('roles', function (Blueprint $table) {
            $table->id(); // ID automático como clave primaria
            $table->string('nombre', 50); // Nombre del rol (ej. 'superadmin', 'admin', 'cliente')
            $table->enum('estado', ['activo', 'inactivo'])->default('activo'); // Estado del rol ('activo' o 'inactivo')
            $table->string('descripcion', 255)->nullable(); // Descripción del rol
            $table->timestamps(); // Tiempos de creación y actualización
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
