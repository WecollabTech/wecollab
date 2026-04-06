<?php
// database/migrations/2024_01_01_000001_create_tutoriales_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tutoriales', function (Blueprint $table) {
            $table->id();
            $table->string('titulo', 100)->unique();
            $table->text('descripcion')->nullable();
            $table->enum('tipo_material', ['video', 'manual', 'guia', 'post', 'triptico', 'avisos importantes'])->nullable();
            $table->enum('formato', ['pdf', 'word', 'mp4'])->nullable();
            $table->enum('alcance', [
                'Superadmin we collab',
                'Admin we collab',
                'Soporte we collab',
                'Usuario we collab',
                'Suscriptor SLC',
                'Usuario Admin SLC',
                'Usuario Premium SLC',
                'Usuario Público',
                'Prospecto'
            ])->nullable();
            $table->string('estado')->default('activo');
            $table->text('url')->nullable();
            $table->text('observacion')->nullable();

            // ✅ Foreign keys
            $table->foreignId('categorias_id')->nullable()->constrained('categorias')->onDelete('set null');
            $table->foreignId('subcategoria_id')->nullable()->constrained('subcategorias')->onDelete('set null');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');

            $table->integer('vistas')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tutoriales');
    }
};