<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController; // Importa el controlador correcto
use App\Http\Controllers\PermisoController; // Importa el controlador correcto
use App\Http\Controllers\CategoriaController; // Importa el controlador correcto
use App\Http\Controllers\SubcategoriaController; // Importa el controlador correcto
use App\Http\Controllers\TutorialController; // Importa el controlador correcto
use App\Models\User;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// Rutas de API para roles
Route::apiResource('roles', RoleController::class);
Route::get('/permisos/all', [PermisoController::class, 'all']);
Route::apiResource('permisos', PermisoController::class);
Route::apiResource('categorias', CategoriaController::class);
Route::apiResource('subcategorias', SubcategoriaController::class);

// 🔐 Grupo con middleware COMPLETO de Jetstream para session auth


// 📋 Listar TODOS los tutoriales (frontend maneja UI del candado)
Route::get('/tutoriales', [TutorialController::class, 'index'])
    ->name('tutoriales.index');

// 👁️ Ver tutorial específico (valida acceso real por rol/alcance)
Route::get('/tutoriales/{tutorial}', [TutorialController::class, 'show'])
    ->name('tutoriales.show');

// ✏️ CRUD protegido para administradores
Route::post('/tutoriales', [TutorialController::class, 'store'])
    ->name('tutoriales.store');

Route::put('/tutoriales/{id}', [TutorialController::class, 'update'])
    ->name('tutoriales.update');

Route::patch('/tutoriales/{id}', [TutorialController::class, 'update'])
    ->name('tutoriales.update.patch');

Route::delete('/tutoriales/{id}', [TutorialController::class, 'destroy'])
    ->name('tutoriales.destroy');


// 🌍 Rutas públicas (sin autenticación)
Route::get('/tutoriales/public', [TutorialController::class, 'index'])
    ->name('tutoriales.public');