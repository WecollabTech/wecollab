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

// routes/api.php - SIN cambiar el controller
Route::get('/tutoriales', [TutorialController::class, 'index'])->name('tutoriales.index');
Route::post('/tutoriales', [TutorialController::class, 'store'])->name('tutoriales.store');

// ✅ show usa {tutorial} para model binding
Route::get('/tutoriales/{tutorial}', [TutorialController::class, 'show'])->name('tutoriales.show');

// ⚠️ update/destroy usan {id} porque tu controller espera $id
Route::put('/tutoriales/{id}', [TutorialController::class, 'update'])->name('tutoriales.update');
// Route::patch('/tutoriales/{id}', [TutorialController::class, 'update'])->name('tutoriales.update.patch');
Route::delete('/tutoriales/{id}', [TutorialController::class, 'destroy'])->name('tutoriales.destroy');

Route::get('/usuarios', function () {
    return User::all();
});