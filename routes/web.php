<?php

use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\RecursosSLCController;
use App\Http\Controllers\SubcategoriaController;
use App\Http\Controllers\TutorialController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Http\Request;


use App\Models\Tutorial;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// =========================================================================
// RUTAS PÚBLICAS
// =========================================================================

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
    ]);
});

// =========================================================================
// RUTAS AUTENTICADAS (DASHBOARD)
// =========================================================================

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->get('/dashboard', function () {
    $user = Auth::user();
    $rol = optional($user->role)->nombre;
    if ($rol === 'Superadmin we collab' || $rol === 'Admin we collab') {
        return Inertia::render('Dashboard');
    }
    return Inertia::render('Usuarios');
})->name('dashboard');

// =========================================================================
// RUTAS AUTENTICADAS - GRUPO PRINCIPAL
// =========================================================================

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    // Roles y Permisos
    Route::get('/roles', fn() => Inertia::render('Roles/Index'))->name('roles');
    Route::get('/permisos', fn() => Inertia::render('Permisos/Index'))->name('permisos');
    Route::get('/usuario', fn() => Inertia::render('Usuarios/Index'))->name('usuario');

    // Tutoriales (vista alternativa)
    Route::get('/Registrar_video', fn() => Inertia::render('Tutoriales/CreateTutorial'))->name('CreateTutorial');
    Route::put('/tutoriales/{tutorial}', [TutorialController::class, 'updateWeb'])->name('tutoriales.update');
    Route::get('/tutoriales/{tutorial}/edit', function (Tutorial $tutorial) {
        return Inertia::render('Tutoriales/Edit', [
            'tutorial' => $tutorial,
            'subcategorias' => \App\Models\Subcategoria::all(),
            'users' => \App\Models\User::all(),
        ]);
    })->name('Editar');

    // Listado de tutoriales (con permiso)
    Route::get('/tutoriales', function () {
        $user = Auth::user();
        $rol = optional($user->role)->nombre;
        if ($rol === 'Superadmin we collab' || $rol === 'Admin We collab') {
            return Inertia::render('Tutoriales/Index');
        }
        abort(403, 'No tienes permisos para acceder a esta página.');
    })->name('tutoriales');

    // Ver tutorial individual
    Route::get('/tutorial/{id}', function ($id, Request $request) {
        $user = $request->user();
        $tutorial = Tutorial::find($id);

        if (!$tutorial || $tutorial->estado !== 'activo') {
            abort(404, 'Tutorial no disponible');
        }

        $tieneAcceso = false;

        if (empty($tutorial->alcance) || trim($tutorial->alcance) === '') {
            $tieneAcceso = true;
        } elseif ($user && $user->role) {
            $rolUsuario = is_string($user->role)
                ? $user->role
                : ($user->role->nombre ?? $user->role->name ?? '');

            if (strtolower(trim($rolUsuario)) === 'superadmin we collab') {
                $tieneAcceso = true;
            } else {
                $tieneAcceso = strtolower(trim($rolUsuario)) === strtolower(trim($tutorial->alcance));
            }
        }

        if (!$tieneAcceso) {
            return Inertia::render('Errors/403');
        }

        return Inertia::render('Tutoriales/Show', [
            'tutorial' => $tutorial,
            'user' => $user ? [
                'id' => $user->id,
                'name' => $user->name,
                'role' => is_string($user->role)
                    ? $user->role
                    : ($user->role->nombre ?? $user->role->name ?? null),
            ] : null,
        ]);
    })->name('tutorial.show');
});

// =========================================================================
// RECURSOS - RUTAS PRINCIPALES (LIMPIAS Y ORGANIZADAS)
// =========================================================================

Route::middleware(['auth', 'verified'])->prefix('recursos')->group(function () {

    // -------------------------------------------------------------------------
    // VISTAS PRINCIPALES (INDEX)
    // -------------------------------------------------------------------------
    Route::get('/', [RecursosSLCController::class, 'index'])->name('recursos.index');
    Route::get('/videos', fn() => Inertia::render('Recursos/Index', ['tipo' => 'video']))->name('recursos.videos');
    Route::get('/manuales', fn() => Inertia::render('Recursos/Index', ['tipo' => 'manual']))->name('recursos.manuales');
    Route::get('/guias', fn() => Inertia::render('Recursos/Index', ['tipo' => 'guia']))->name('recursos.guias');
    Route::get('/posts', fn() => Inertia::render('Recursos/Index', ['tipo' => 'post']))->name('recursos.posts');
    Route::get('/tripticos', fn() => Inertia::render('Recursos/Index', ['tipo' => 'triptico']))->name('recursos.tripticos');

    // ✅ Avisos Importantes (única ruta, sin duplicados)
    Route::get('/avisos-importantes', fn() => Inertia::render('Recursos/Index', ['tipo' => 'avisos importantes']))->name('recursos.avisos');

    // -------------------------------------------------------------------------
    // VER RECURSOS INDIVIDUALES (SHOW)
    // -------------------------------------------------------------------------
    Route::get('/videos/{id}', [RecursosSLCController::class, 'show'])->name('recursos.videos.show');
    Route::get('/manuales/{id}', [RecursosSLCController::class, 'show'])->name('recursos.manuales.show');
    Route::get('/guias/{id}', [RecursosSLCController::class, 'show'])->name('recursos.guias.show');
    Route::get('/posts/{id}', [RecursosSLCController::class, 'show'])->name('recursos.posts.show');
    Route::get('/tripticos/{id}', [RecursosSLCController::class, 'show'])->name('recursos.tripticos.show');

    // ✅ Ver aviso importante individual
    Route::get('/avisos-importantes/{id}', [RecursosSLCController::class, 'show'])->name('recursos.avisos.show');

    // -------------------------------------------------------------------------
    // COMPARTIR RECURSO
    // -------------------------------------------------------------------------
    Route::get('/compartir/{id}', function ($id) {
        return Inertia::render('Recursos/Redirect', ['id' => $id]);
    })->name('recursos.share');

    // -------------------------------------------------------------------------
    // CRUD - CREAR, EDITAR, ACTUALIZAR, ELIMINAR
    // -------------------------------------------------------------------------
    Route::get('/crear/{tipo}', [RecursosSLCController::class, 'create'])->name('recursos.create');
    Route::post('/', [RecursosSLCController::class, 'store'])->name('recursos.store');
    Route::get('/{id}/edit', [RecursosSLCController::class, 'edit'])->name('recursos.edit');
    Route::put('/{id}', [RecursosSLCController::class, 'update'])->name('recursos.update');
    Route::delete('/{id}', [RecursosSLCController::class, 'destroy'])->name('recursos.destroy');
});

// =========================================================================
// CATEGORIAS
// =========================================================================

Route::middleware(['auth', 'verified'])
    ->prefix('categorias')
    ->group(function () {
        Route::get('/', fn() => Inertia::render('Categorias/Index'))->name('categorias.index');
        Route::get('/create', fn() => Inertia::render('Categorias/Create'))->name('categorias.create');
        Route::get('/{id}/edit', fn($id) => Inertia::render('Categorias/Edit', ['id' => $id]))->name('categorias.edit');
        Route::get('/export', [CategoriaController::class, 'export'])->name('categorias.export');
    });

// =========================================================================
// SUBCATEGORIAS
// =========================================================================

Route::middleware(['auth', 'verified'])
    ->prefix('subcategorias')
    ->group(function () {
        Route::get('/', fn() => Inertia::render('Subcategorias/Index'))->name('subcategorias.index');
        Route::get('/create', fn() => Inertia::render('Subcategorias/Create', [
            'categoriaId' => request()->query('categoria_id')
        ]))->name('subcategorias.create');
        Route::get('/{id}', fn($id) => Inertia::render('Subcategorias/Show', ['id' => $id]))->name('subcategorias.show');
        Route::get('/{id}/edit', fn($id) => Inertia::render('Subcategorias/Edit', ['id' => $id]))->name('subcategorias.edit');
        Route::get('/export', [SubcategoriaController::class, 'export'])->name('subcategorias.export');
    });



// Rutas protegidas por autenticación
Route::middleware(['auth', 'verified'])
    ->prefix('usuarios')
    ->group(function () {
        // Vistas principales (solo renderizado)
        Route::get('/', fn() => Inertia::render('Usuarios/Index'))->name('usuarios.index');
        Route::get('/create', fn() => Inertia::render('Usuarios/Create'))->name('usuarios.create');
        Route::get('/{id}', fn($id) => Inertia::render('Usuarios/Show', ['id' => $id]))->name('usuarios.show');
        Route::get('/{id}/edit', fn($id) => Inertia::render('Usuarios/Edit', ['id' => $id]))->name('usuarios.edit');

        // Si necesitas exportar vista
        Route::get('/export/view', fn() => Inertia::render('Usuarios/Export'))->name('usuarios.export.view');

        // Nota: La lógica de exportar está en API, solo la vista aquí
    });