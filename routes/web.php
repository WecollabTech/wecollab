<?php

use App\Http\Controllers\RecursosSLCController;
use Illuminate\Http\Request;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Models\Tutorial;
use App\Http\Controllers\TutorialController;
use App\Http\Middleware\CheckRole; // Si tienes el middleware para comprobar roles
use App\Http\Middleware\CheckPermission;
use App\Models\Subcategoria;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        // 'laravelVersion' => Application::VERSION,
        // 'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->get('/dashboard', function () {

    $user = Auth::user();

    $rol = optional($user->role)->nombre;

    if ($rol === 'Superadmin We collab' || $rol === 'Admin We collab') {
        return Inertia::render('Dashboard');
    }

    return Inertia::render('Usuarios');

})->name('dashboard');




Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    Route::get('/roles', function () {
        return Inertia::render('Roles/Index');
    })->name('roles');

    // Ruta para renderizar la vista de roles
    Route::get('/permisos', function () {
        return Inertia::render('Permisos/Index');
    })->name('permisos');

    // Ruta para renderizar la vista de categorias y subcategories
    Route::get('/categorias', function () {
        return inertia('Categorias/CategoriaManager');
    })->name('categorias');

    Route::get('/subcategorias', function () {
        return inertia('Subcategorias/SubcategoriaManager');
    })->name('subcategorias');



    Route::get('/Registrar_video', function () {
        return Inertia::render('Tutoriales/CreateTutorial');
    })->name('CreateTutorial');

    Route::get('/prueba', function () {
        return Inertia::render('Tutoriales/Prueba');
    })->name('prueba');

    // Route::get('/tutoriales', function () {
    //     return Inertia::render('Tutoriales/Index');
    // })->name('tutoriales');


    // Ruta para actualizar un tutorial con Inertia
    Route::put('/tutoriales/{tutorial}', [TutorialController::class, 'updateWeb'])->name('tutoriales.update');


    // Ruta para editar un tutorial
    Route::get('/tutoriales/{tutorial}/edit', function (Tutorial $tutorial) {
        return Inertia::render('Tutoriales/Edit', [
            'tutorial' => $tutorial,
            'subcategorias' => \App\Models\Subcategoria::all(),
            'users' => \App\Models\User::all(),
        ]);
    })->name('Editar');



    // 🔥 NUEVA RUTA PARA REPRODUCTOR
    // ✅ Ruta web para mostrar la vista del tutorial



});




Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->get('/tutoriales', function () {

    $user = Auth::user();
    $rol = optional($user->role)->nombre;

    // Solo permitir acceso a Superadmin We collab y Admin We collab
    if ($rol === 'Superadmin We collab' || $rol === 'Admin We collab') {
        return Inertia::render('Tutoriales/Index');
    }

    // Si no tiene permisos, redirigir al dashboard o mostrar error 403
    abort(403, 'No tienes permisos para acceder a esta página.');

    // O también podrías redirigir:
    // return redirect()->route('dashboard')->with('error', 'Acceso no autorizado');

})->name('tutoriales');

































Route::middleware(['auth'])->group(function () {

    // 👁️ Ver tutorial individual (valida acceso antes de mostrar)
    Route::get('/tutorial/{id}', function ($id, Request $request) {

        $user = $request->user();
        $tutorial = Tutorial::find($id);

        // ❌ No existe
        if (!$tutorial) {
            abort(404, 'Tutorial no encontrado');
        }

        // ❌ No activo
        if ($tutorial->estado !== 'activo') {
            abort(404, 'Tutorial no disponible');
        }

        // 🔐 Validar acceso por rol/alcance
        $tieneAcceso = false;

        // Contenido público
        if (empty($tutorial->alcance) || trim($tutorial->alcance) === '') {
            $tieneAcceso = true;
        }
        // Usuario con rol
        elseif ($user && $user->role) {
            $rolUsuario = is_string($user->role)
                ? $user->role
                : ($user->role->nombre ?? $user->role->name ?? '');

            // Superadmin ve todo
            if (strtolower(trim($rolUsuario)) === 'superadmin we collab') {
                $tieneAcceso = true;
            }
            // Match exacto
            else {
                $tieneAcceso = strtolower(trim($rolUsuario)) === strtolower(trim($tutorial->alcance));
            }
        }

        // ❌ Sin acceso
        if (!$tieneAcceso) {
            return Inertia::render('Errors/403');
        }

        // ✅ Renderizar vista
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






Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/usuario', function () {
        return Inertia::render('Usuarios/Index');
    })->name('usuario');
});








// Route::middleware(['auth', 'verified'])->group(function () {

//     Route::prefix('recursos')->group(function () {

//         Route::get('/videos', function () {
//             return Inertia::render('Recursos/Index', [
//                 'tipo' => 'video'
//             ]);
//         })->name('recursos.videos');

//         Route::get('/manuales', function () {
//             return Inertia::render('Recursos/Index', [
//                 'tipo' => 'manual'
//             ]);
//         })->name('recursos.manuales');

//         Route::get('/guias', function () {
//             return Inertia::render('Recursos/Index', [
//                 'tipo' => 'guia'
//             ]);
//         })->name('recursos.guias');

//         Route::get('/posts', function () {
//             return Inertia::render('Recursos/Index', [
//                 'tipo' => 'post'
//             ]);
//         })->name('recursos.posts');

//         Route::get('/tripticos', function () {
//             return Inertia::render('Recursos/Index', [
//                 'tipo' => 'triptico'
//             ]);
//         })->name('recursos.tripticos');

//     });

// });



// Route::middleware(['auth', 'verified'])->prefix('recursos')->group(function () {

//     // 🌐 Vista general (todos los recursos)
//     Route::get('/', function () {
//         return Inertia::render('Recursos/Index', [
//             'tipo' => 'todos'  // Esto indica que no filtramos por tipo
//         ]);
//     })->name('recursos.index');

//     // Listados por tipo
//     Route::get('/videos', fn() => Inertia::render('Recursos/Index', ['tipo' => 'video']))->name('recursos.videos');
//     Route::get('/manuales', fn() => Inertia::render('Recursos/Index', ['tipo' => 'manual']))->name('recursos.manuales');
//     Route::get('/guias', fn() => Inertia::render('Recursos/Index', ['tipo' => 'guia']))->name('recursos.guias');
//     Route::get('/posts', fn() => Inertia::render('Recursos/Index', ['tipo' => 'post']))->name('recursos.posts');
//     Route::get('/tripticos', fn() => Inertia::render('Recursos/Index', ['tipo' => 'triptico']))->name('recursos.tripticos');

//     // Crear recurso
//     Route::get('/crear/{tipo}', fn($tipo) => Inertia::render('Recursos/Create', ['tipo' => $tipo]))->name('recursos.create');
//     Route::post('/', [RecursosSLCController::class, 'store'])->name('recursos.store');
// });










Route::middleware(['auth', 'verified'])->prefix('recursos')->group(function () {

    // 🌐 Vista general (todos los recursos)
    Route::get('/', [RecursosSLCController::class, 'index'])->name('recursos.index');

    // Listados por tipo
    Route::get('/videos', fn() => Inertia::render('Recursos/Index', ['tipo' => 'video']))->name('recursos.videos');
    Route::get('/manuales', fn() => Inertia::render('Recursos/Index', ['tipo' => 'manual']))->name('recursos.manuales');
    Route::get('/guias', fn() => Inertia::render('Recursos/Index', ['tipo' => 'guia']))->name('recursos.guias');
    Route::get('/posts', fn() => Inertia::render('Recursos/Index', ['tipo' => 'post']))->name('recursos.posts');
    Route::get('/tripticos', fn() => Inertia::render('Recursos/Index', ['tipo' => 'triptico']))->name('recursos.tripticos');

    // ✅ RUTAS PARA VER RECURSOS INDIVIDUALES (URL AMIGABLES)
    Route::get('/videos/{id}', [RecursosSLCController::class, 'show'])->name('recursos.videos.show');
    Route::get('/manuales/{id}', [RecursosSLCController::class, 'show'])->name('recursos.manuales.show');
    Route::get('/guias/{id}', [RecursosSLCController::class, 'show'])->name('recursos.guias.show');
    Route::get('/posts/{id}', [RecursosSLCController::class, 'show'])->name('recursos.posts.show');
    Route::get('/tripticos/{id}', [RecursosSLCController::class, 'show'])->name('recursos.tripticos.show');

    // 🔗 RUTA DE REDIRECCIÓN PARA COMPARTIR (NO EXPONE LA URL REAL)
    Route::get('/compartir/{id}', function ($id) {
        return Inertia::render('Recursos/Redirect', ['id' => $id]);
    })->name('recursos.share');

    // Crear recurso
    Route::get('/crear/{tipo}', [RecursosSLCController::class, 'create'])->name('recursos.create');
    Route::post('/', [RecursosSLCController::class, 'store'])->name('recursos.store');

    // Editar recurso
    Route::get('/{id}/edit', [RecursosSLCController::class, 'edit'])->name('recursos.edit');
    Route::put('/{id}', [RecursosSLCController::class, 'update'])->name('recursos.update');

    // Eliminar recurso
    Route::delete('/{id}', [RecursosSLCController::class, 'destroy'])->name('recursos.destroy');
});