<script setup>
import NavLink from "@/Components/NavLink.vue";
</script>

<template>
    <div
        class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-teal-50/40"
    >
        <div
            class="w-full max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-10"
        >
            <!-- HEADER -->
            <div
                class="relative overflow-hidden bg-white border rounded-2xl shadow-lg p-6 sm:p-8"
            >
                <div
                    class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-teal-100 to-emerald-100 rounded-full blur-2xl opacity-50"
                ></div>
                <div
                    class="absolute bottom-0 left-0 w-32 h-32 bg-gradient-to-tr from-indigo-100 to-purple-100 rounded-full blur-2xl opacity-40"
                ></div>

                <div class="relative z-10">
                    <h1
                        class="text-2xl sm:text-3xl font-extrabold text-slate-900"
                    >
                        Bienvenidos -
                        <span
                            class="text-transparent bg-clip-text bg-gradient-to-r from-teal-600 to-emerald-600"
                        >
                            Plataforma SLC
                        </span>
                    </h1>

                    <p
                        class="mt-4 text-slate-600 max-w-3xl text-sm sm:text-base"
                    >
                        Gestión del MVP enfocada en registro de usuarios, acceso
                        a contenido y administración básica de materiales de
                        capacitación.
                    </p>
                </div>
            </div>

            <!-- RESUMEN GENERAL -->
            <div
                class="mt-8 grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6"
            >
                <!-- ESTADO MVP -->
                <div class="bg-white rounded-2xl border p-5 shadow-sm">
                    <p class="text-xs text-slate-500">Estado del MVP</p>
                    <p class="text-lg font-bold text-teal-600 mt-1">
                        En desarrollo
                    </p>
                    <div class="w-full bg-slate-100 rounded-full h-2 mt-3">
                        <div class="bg-teal-500 h-2 rounded-full w-2/3"></div>
                    </div>
                    <p class="text-xs text-slate-400 mt-2">
                        Progreso estimado 65%
                    </p>
                </div>

                <!-- USUARIOS -->
                <div class="bg-white rounded-2xl border p-5 shadow-sm">
                    <p class="text-xs text-slate-500">Usuarios registrados</p>
                    <p class="text-2xl font-bold text-slate-900 mt-1">--</p>
                    <p class="text-xs text-slate-400 mt-2">
                        Prospectos y verificados
                    </p>
                </div>

                <!-- MATERIALES -->
                <div class="bg-white rounded-2xl border p-5 shadow-sm">
                    <p class="text-xs text-slate-500">Materiales cargados</p>
                    <p class="text-2xl font-bold text-slate-900 mt-1">--</p>
                    <p class="text-xs text-slate-400 mt-2">
                        Videos, guías y documentos
                    </p>
                </div>

                <!-- CONTENIDO BLOQUEADO -->
                <div class="bg-white rounded-2xl border p-5 shadow-sm">
                    <p class="text-xs text-slate-500">Contenido restringido</p>
                    <p class="text-2xl font-bold text-slate-900 mt-1">Activo</p>
                    <p class="text-xs text-slate-400 mt-2">
                        Control de acceso habilitado
                    </p>
                </div>
            </div>

            <!-- FUNCIONALIDADES CLAVE -->
            <div
                class="mt-10 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6"
            >
                <!-- LANDING -->
                <div
                    class="group bg-white rounded-2xl border shadow-sm hover:shadow-xl transition p-6"
                >
                    <div class="flex items-center gap-2 mb-3">
                        <i class="fas fa-globe text-teal-600"></i>
                        <h3 class="font-bold text-slate-900">Landing Page</h3>
                    </div>
                    <p class="text-sm text-slate-600">
                        Página pública para atraer usuarios, explicar el sistema
                        y permitir el registro.
                    </p>
                </div>

                <!-- REGISTRO -->
                <div
                    class="group bg-white rounded-2xl border shadow-sm hover:shadow-xl transition p-6"
                >
                    <div class="flex items-center gap-2 mb-3">
                        <i class="fas fa-user-plus text-indigo-600"></i>
                        <h3 class="font-bold text-slate-900">
                            Registro simplificado
                        </h3>
                    </div>
                    <p class="text-sm text-slate-600">
                        Captura de nombre, apellido y correo para facilitar la
                        conversión de prospectos.
                    </p>
                </div>

                <!-- ROLES -->
                <div
                    class="group bg-white rounded-2xl border shadow-sm hover:shadow-xl transition p-6"
                >
                    <div class="flex items-center gap-2 mb-3">
                        <i class="fas fa-shield-alt text-purple-600"></i>
                        <h3 class="font-bold text-slate-900">
                            Roles y permisos
                        </h3>
                    </div>
                    <p class="text-sm text-slate-600">
                        Control de acceso según tipo de usuario: admin, cliente
                        o visitante.
                    </p>
                </div>

                <!-- MATERIALES -->
                <div
                    class="group bg-white rounded-2xl border shadow-sm hover:shadow-xl transition p-6"
                >
                    <div class="flex items-center gap-2 mb-3">
                        <i class="fas fa-book text-emerald-600"></i>
                        <h3 class="font-bold text-slate-900">
                            Catálogo de materiales
                        </h3>
                    </div>
                    <p class="text-sm text-slate-600">
                        Visualización de videos, guías y documentos disponibles.
                    </p>
                </div>

                <!-- BLOQUEO -->
                <div
                    class="group bg-white rounded-2xl border shadow-sm hover:shadow-xl transition p-6"
                >
                    <div class="flex items-center gap-2 mb-3">
                        <i class="fas fa-lock text-red-500"></i>
                        <h3 class="font-bold text-slate-900">
                            Contenido restringido
                        </h3>
                    </div>
                    <p class="text-sm text-slate-600">
                        Material exclusivo bloqueado para usuarios no
                        registrados.
                    </p>
                </div>

                <!-- ADMIN -->
                <div
                    class="group bg-white rounded-2xl border shadow-sm hover:shadow-xl transition p-6"
                >
                    <div class="flex items-center gap-2 mb-3">
                        <i class="fas fa-cogs text-slate-600"></i>
                        <h3 class="font-bold text-slate-900">
                            Panel administrativo
                        </h3>
                    </div>
                    <p class="text-sm text-slate-600">
                        Gestión de contenido: subir videos, guías y documentos.
                    </p>
                </div>
            </div>

            <!-- ACCIONES RÁPIDAS -->
            <div class="mt-10 bg-white border rounded-2xl p-6 shadow-sm">
                <h2 class="font-bold text-slate-800 mb-4">Acciones rápidas</h2>

                <div class="flex flex-wrap gap-3">
                    <button
                        class="bg-teal-600 text-white px-4 py-2 rounded-xl text-sm hover:scale-105 transition"
                    >
                        + Subir material
                    </button>

                    <button
                        class="bg-indigo-600 text-white px-4 py-2 rounded-xl text-sm hover:scale-105 transition"
                    >
                        Ver usuarios
                    </button>

                    <button
                        class="bg-emerald-600 text-white px-4 py-2 rounded-xl text-sm hover:scale-105 transition"
                    >
                        Ir a catálogo
                    </button>
                </div>
            </div>

            <!-- FOOTER -->
            <div class="mt-12 pt-6 border-t text-center text-xs text-slate-400">
                Plataforma SLC • MVP en desarrollo
            </div>
        </div>
    </div>
</template>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
