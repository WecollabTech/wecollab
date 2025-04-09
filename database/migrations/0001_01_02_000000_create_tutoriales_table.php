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
        Schema::create('tutoriales', function (Blueprint $table) {
            $table->id();
            $table->string('titulo', 100)->unique();
            $table->text('descripcion')->nullable();
            $table->enum('tipo_material', ['video', 'manaul','guia','post','triptico'])->nullable();
            $table->enum('formato', ['pdf', 'word','mp4'])->nullable();
            $table->enum('alcance', ['soporte', 'admin','clientefinal'])->nullable();
            $table->string('estado')->default('activo');
            $table->text('url')->nullable();
            $table->text('observacion')->nullable();
            $table->foreignId('subcategoria_id')->nullable()->constrained('subcategorias')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tutoriales');
    }
};