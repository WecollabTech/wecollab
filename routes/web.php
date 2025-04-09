<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Models\Tutorial;
use App\Http\Controllers\TutorialController;
use App\Http\Middleware\CheckRole; // Si tienes el middleware para comprobar roles
use App\Http\Middleware\CheckPermission;
use App\Models\Subcategoria;
use App\Models\User;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',            // Verifica que el usuario esté autenticado con Sanctum.
    config('jetstream.auth_session'),  // Verifica que la sesión de Jetstream esté activa.
    'verified',                // Verifica que el correo electrónico del usuario haya sido verificado.
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');  // Renderiza el componente Inertia llamado 'Dashboard'
    })->name('dashboard');
});






// Ruta para renderizar la vista de roles
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





Route::get('/tutoriales', function () {
    return Inertia::render('Tutoriales/Index');
})->name('tutoriales');


// Ruta para editar un tutorial
Route::get('/tutoriales/{tutorial}/edit', function (Tutorial $tutorial) {
    return Inertia::render('Tutoriales/Edit', [
        'tutorial' => $tutorial,
        'subcategorias' => \App\Models\Subcategoria::all(),
        'users' => \App\Models\User::all(),
    ]);
})->name('Editar');

// Ruta para actualizar un tutorial con Inertia
Route::put('/tutoriales/{tutorial}', [TutorialController::class, 'updateWeb'])->name('tutoriales.update');



// Route::middleware(['auth', 'permission:crear_video'])->group(function () {
//     Route::get('/Registrar_video', function () {
//         return Inertia::render('Tutoriales/CreateTutorial');
//     })->name('CreateTutorial');
// });


// Route::middleware([
//     'auth:sanctum',
//     'verified',
//     CheckRole::class.':usuario' // Pasando roles de forma sencilla
// ])->group(function () {
//     Route::get('/Registrar_video', function () {
//         return Inertia::render('Tutoriales/CreateTutorial');
//     })->name('CreateTutorial'); // Definiendo un nombre para la ruta
// });


// Route::middleware([
//     'auth:sanctum',
//     config('jetstream.auth_session'),
//     'verified',
//     'role:admin,usuario'  // Usando el middleware con los nombres de los roles
// ])->group(function () {
//     Route::get('/tutoriales', function () {
//         return Inertia::render('Tutoriales/Index');
//     })->name('tutoriales');
// });


// Route::middleware([
//     'auth:sanctum', // Verifica que el usuario esté autenticado
//     'verified', // Verifica que el usuario esté verificado
//     CheckRole::class.':usuario,admin,cliente', // Verifica que el usuario tenga el rol 'usuario'
//     CheckPermission::class.':crear_video' // Verifica que el usuario tenga el permiso 'crear_video'
// ])->group(function () {
//     Route::get('/Registrar_video', function () {
//         return Inertia::render('Tutoriales/CreateTutorial');
//     })->name('CreateTutorial'); // Definiendo un nombre para la ruta
// });



// Route::middleware([
//     'auth:sanctum',
//     config('jetstream.auth_session'),
//     'verified',
//     CheckRole::class.':usuario,admin', // Verifica que el usuario tenga el rol 'admin'
//     CheckPermission::class.':ver_video,' // Verifica que el usuario tenga el permiso 'crear_video'
// ])->group(function () {
//     Route::get('/tutoriales', function () {
//         return Inertia::render('Tutoriales/Index');
//     })->name('tutoriales');
// });








Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/usuario', function () {
        return Inertia::render('Usuarios/Index');
    })->name('usuario');
});