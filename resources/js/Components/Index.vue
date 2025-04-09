<script setup>
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
</script>

<template>
    <div class="container mx-auto px-6 lg:px-12 py-8" style="margin-top: 10px">
        <!-- Header -->
        <div class="p-8 bg-white border border-gray-300 rounded-lg shadow-lg">
            <h1 class="text-3xl font-bold text-gray-900">
                ¡Bienvenido a su aplicación SistemaGT!
            </h1>

            <p class="mt-4 text-gray-600 leading-relaxed">
                SistemaGT es una plataforma web interactiva, escalable y segura,
                diseñada para optimizar el aprendizaje y la gestión de
                contenido. Nuestro objetivo es ofrecer una experiencia intuitiva
                donde los usuarios puedan acceder, explorar e interactuar con
                tutoriales en diversos formatos, como videos, textos e imágenes.
            </p>
        </div>

        <!-- Sections -->
        <div class="mt-10 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div
                class="bg-white p-6 rounded-lg shadow-lg border border-gray-200"
            >
                <h2 class="text-xl font-semibold text-gray-900">
                    Estadísticas
                </h2>
                <div class="mt-4 text-gray-700 space-y-2">
                    <p>Total Admin: <span class="font-semibold">10</span></p>
                    <p>Noticias: <span class="font-semibold">42</span></p>
                </div>
            </div>

            <div
                class="bg-white p-6 rounded-lg shadow-lg border border-gray-200"
            >
                <h2 class="text-xl font-semibold text-gray-900">Resultados</h2>
                <div class="mt-4 text-gray-700 space-y-2">
                    <p>Semana</p>
                    <p>Mes</p>
                </div>
            </div>

            <div
                class="bg-white p-6 rounded-lg shadow-lg border border-gray-200"
            >
                <h2 class="text-xl font-semibold text-gray-900">
                    Usuarios en línea
                </h2>
                <p class="mt-4 text-gray-700 text-lg font-semibold">47</p>
            </div>

            <div
                class="bg-white p-6 rounded-lg shadow-lg border border-gray-200 md:col-span-2 lg:col-span-3"
            >
                <h2 class="text-xl font-semibold text-gray-900">
                    Actividades Recientes
                </h2>
                <div class="mt-4 space-y-4">
                    <div
                        v-for="(activity, index) in [
                            'Farhan A Mujib',
                            'Ujang Maman',
                            'Rizal Fakhri',
                            'Alfa Zulkarnain',
                        ]"
                        :key="index"
                    >
                        <p class="text-sm font-medium text-gray-900">
                            {{ activity }}
                        </p>
                        <p class="text-sm text-gray-500">
                            Cras sit amet nibh libero, in gravida nulla. Nulla
                            vel metus scelerisque ante sollicitudin.
                        </p>
                    </div>
                </div>
            </div>

            <div
                class="bg-white p-6 rounded-lg shadow-lg border border-gray-200"
            >
                <h2 class="text-xl font-semibold text-gray-900">
                    Documentación
                </h2>
                <p class="mt-4 text-gray-500 text-sm leading-relaxed">
                    Laravel tiene una documentación increíble que cubre todos
                    los aspectos del framework.
                </p>
                <p class="mt-4 text-sm">
                    <a
                        href="https://laravel.com/docs"
                        class="inline-flex items-center font-semibold text-indigo-700"
                    >
                        Explorar la documentación
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 20 20"
                            class="ml-1 w-5 h-5 fill-indigo-500"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M5 10a.75.75 0 01.75-.75h6.638L10.23 7.29a.75.75 0 111.04-1.08l3.5 3.25a.75.75 0 010 1.08l-3.5 3.25a.75.75 0 11-1.04-1.08l2.158-1.96H5.75A.75.75 0 015 10z"
                                clip-rule="evenodd"
                            />
                        </svg>
                    </a>
                </p>
            </div>
        </div>
    </div>
</template>
