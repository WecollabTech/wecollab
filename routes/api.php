<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController; // Importa el controlador correcto
use App\Http\Controllers\PermisoController; // Importa el controlador correcto
use App\Http\Controllers\CategoriaController; // Importa el controlador correcto
use App\Http\Controllers\RecursosSLCController;
use App\Http\Controllers\SubcategoriaController; // Importa el controlador correcto
use App\Http\Controllers\TutorialController; // Importa el controlador correcto
use App\Http\Controllers\UserManagementController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// Rutas de API para roles
Route::apiResource('roles', RoleController::class);
Route::get('/permisos/all', [PermisoController::class, 'all']);
Route::apiResource('permisos', PermisoController::class);


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



// Rutas API para recursos
Route::prefix('recursos')->group(function () {
    Route::get('/', [RecursosSLCController::class, 'indexApi']);
    Route::get('/{id}', [RecursosSLCController::class, 'showApi']);
    Route::post('/', [RecursosSLCController::class, 'storeApi']);
    Route::put('/{id}', [RecursosSLCController::class, 'updateApi']);
    Route::delete('/{id}', [RecursosSLCController::class, 'destroyApi']);
});

Route::prefix('categorias')->group(function () {
    Route::get('/', [CategoriaController::class, 'index']);
    Route::post('/', [CategoriaController::class, 'store']);
    Route::get('/all', [CategoriaController::class, 'all']);
    Route::get('/lista', [CategoriaController::class, 'lista']);
    Route::get('/{categoria}', [CategoriaController::class, 'show']);
    Route::put('/{categoria}', [CategoriaController::class, 'update']);
    Route::delete('/{categoria}', [CategoriaController::class, 'destroy']);

});




// Rutas para subcategorías
Route::prefix('subcategorias')->group(function () {
    Route::get('/', [SubcategoriaController::class, 'index']);
    Route::post('/', [SubcategoriaController::class, 'store']);
    Route::get('/all', [SubcategoriaController::class, 'all']);
    Route::get('/{subcategoria}', [SubcategoriaController::class, 'show']);
    Route::put('/{subcategoria}', [SubcategoriaController::class, 'update']);
    Route::delete('/{subcategoria}', [SubcategoriaController::class, 'destroy']);
    Route::get('/export', [SubcategoriaController::class, 'export']);
});


// Tutoriales API endpoints
Route::get('/tutoriales', [TutorialController::class, 'index']);
Route::get('/tutoriales/{tutorial}', [TutorialController::class, 'show']);
Route::post('/tutoriales', [TutorialController::class, 'store']);
Route::put('/tutoriales/{id}', [TutorialController::class, 'update']);
Route::delete('/tutoriales/{id}', [TutorialController::class, 'destroy']);
Route::post('/tutoriales/{id}/vista', [TutorialController::class, 'registrarVista']);

// Categorias y Subcategorias API endpoints
Route::get('/categorias/lista', [TutorialController::class, 'getCategorias']);
Route::get('/subcategorias/all', [TutorialController::class, 'getSubcategorias']);



// Rutas de API para usuarios
Route::prefix('usuarios')->group(function () {
    Route::get('/', [UserManagementController::class, 'index']);
    Route::get('/all', [UserManagementController::class, 'all']);
    Route::get('/roles/list', [UserManagementController::class, 'getRoles']);
    Route::get('/paises/list', [UserManagementController::class, 'getCountries']);
    Route::get('/empresas/list', [UserManagementController::class, 'getCompanies']);
    Route::post('/', [UserManagementController::class, 'store']);
    Route::get('/{usuario}', [UserManagementController::class, 'show']);
    Route::put('/{usuario}', [UserManagementController::class, 'update']);
    Route::delete('/{usuario}', [UserManagementController::class, 'destroy']);
    Route::patch('/{usuario}/toggle-status', [UserManagementController::class, 'toggleStatus']);
});