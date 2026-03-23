<script setup>
import NavLink from "@/Components/NavLink.vue";
</script>

<template>
    <div
        class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-teal-50/40"
    >
        <!-- ✅ CONTENEDOR OPTIMIZADO -->
        <div
            class="w-full max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-10"
        >
            <!-- HEADER -->
            <div
                class="relative overflow-hidden bg-white border border-slate-200 rounded-2xl shadow-lg shadow-slate-200/50 p-6 sm:p-8"
            >
                <!-- DECORACIÓN -->
                <div
                    class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-gradient-to-br from-teal-100 to-emerald-100 rounded-full opacity-50 blur-2xl"
                ></div>
                <div
                    class="absolute bottom-0 left-0 -mb-4 -ml-4 w-32 h-32 bg-gradient-to-tr from-indigo-100 to-purple-100 rounded-full opacity-40 blur-2xl"
                ></div>

                <div class="relative z-10">
                    <h1
                        class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight"
                    >
                        ¡Bienvenido a su aplicación
                        <span
                            class="text-transparent bg-clip-text bg-gradient-to-r from-teal-600 to-emerald-600"
                        >
                            SistemaGT </span
                        >!
                    </h1>

                    <p
                        class="mt-4 text-slate-600 text-sm sm:text-base max-w-3xl leading-relaxed"
                    >
                        SistemaGT es una plataforma web interactiva, escalable y
                        segura, diseñada para optimizar el aprendizaje y la
                        gestión de contenido.
                    </p>
                </div>
            </div>

            <!-- GRID -->
            <div
                class="mt-10 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6"
            >
                <!-- CARD -->
                <div
                    class="group bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-1"
                >
                    <div
                        class="h-1 bg-gradient-to-r from-teal-500 to-emerald-500 rounded-t-2xl"
                    ></div>

                    <div class="p-6 flex flex-col gap-4">
                        <div class="flex items-center gap-2">
                            <div
                                class="w-9 h-9 bg-teal-100 text-teal-600 flex items-center justify-center rounded-lg"
                            >
                                <i class="fas fa-play"></i>
                            </div>
                            <h3 class="font-bold text-slate-900">
                                Video de Capacitación
                            </h3>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div class="bg-slate-50 p-3 rounded-lg text-center">
                                <p class="text-xs text-slate-500">Total</p>
                                <p class="font-bold text-lg">10</p>
                            </div>
                            <div class="bg-slate-50 p-3 rounded-lg text-center">
                                <p class="text-xs text-slate-500">Noticias</p>
                                <p class="font-bold text-lg">42</p>
                            </div>
                        </div>

                        <button
                            class="mt-2 bg-gradient-to-r from-teal-600 to-emerald-600 text-white py-2 rounded-xl font-semibold hover:scale-105 transition"
                        >
                            Ver más
                        </button>
                    </div>
                </div>

                <!-- USUARIOS -->
                <div
                    class="group bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-1"
                >
                    <div
                        class="h-1 bg-gradient-to-r from-indigo-500 to-purple-500 rounded-t-2xl"
                    ></div>

                    <div class="p-6 flex flex-col gap-4">
                        <div class="flex items-center gap-2">
                            <div
                                class="w-9 h-9 bg-indigo-100 text-indigo-600 flex items-center justify-center rounded-lg"
                            >
                                <i class="fas fa-users"></i>
                            </div>
                            <h3 class="font-bold text-slate-900">Usuarios</h3>
                        </div>

                        <button
                            class="mt-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-2 rounded-xl font-semibold hover:scale-105 transition"
                        >
                            Ver más
                        </button>
                    </div>
                </div>

                <!-- ONLINE -->
                <div
                    class="group bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-1"
                >
                    <div
                        class="h-1 bg-gradient-to-r from-green-500 to-emerald-500 rounded-t-2xl"
                    ></div>

                    <div class="p-6 flex flex-col gap-4">
                        <div class="flex items-center gap-2">
                            <div
                                class="w-9 h-9 bg-green-100 text-green-600 flex items-center justify-center rounded-lg"
                            >
                                <i class="fas fa-wifi"></i>
                            </div>
                            <h3 class="font-bold text-slate-900">
                                Usuarios en línea
                            </h3>
                        </div>

                        <p class="text-3xl font-extrabold text-slate-900">47</p>

                        <button
                            class="bg-gradient-to-r from-green-600 to-emerald-600 text-white py-2 rounded-xl font-semibold hover:scale-105 transition"
                        >
                            Ver más
                        </button>
                    </div>
                </div>
            </div>

            <!-- FOOTER -->
            <div class="mt-12 pt-6 border-t border-slate-200 text-center">
                <p class="text-xs text-slate-400">
                    SistemaGT • Plataforma de Capacitación Digital
                </p>
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
