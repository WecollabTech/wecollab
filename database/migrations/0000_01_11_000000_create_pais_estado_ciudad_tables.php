<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up() {
        Schema::create('paises', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->string('codigo', 10)->unique(); // Código ISO del país
            $table->timestamps();
        });

        Schema::create('estados', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->foreignId('pais_id')->constrained('paises')->onDelete('cascade');
            $table->unique(['nombre', 'pais_id']); // Evitar duplicados
            $table->timestamps();
        });

        Schema::create('ciudades', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->foreignId('estado_id')->constrained('estados')->onDelete('cascade');
            $table->unique(['nombre', 'estado_id']); // Evitar duplicados
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('ciudades');
        Schema::dropIfExists('estados');
        Schema::dropIfExists('paises');
    }
};