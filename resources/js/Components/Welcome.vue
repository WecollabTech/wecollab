<script setup>
import NavLink from "@/Components/NavLink.vue";
</script>

<template>
    <div
        class="container mx-auto px-4 sm:px-6 lg:px-12 py-8"
        style="margin-top: 10px"
    >
        <!-- Header -->
        <div
            class="p-6 sm:p-8 bg-white border border-gray-300 rounded-lg shadow-lg"
        >
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">
                ¡Bienvenido a su aplicación SistemaGT!
            </h1>

            <p class="mt-4 text-gray-600 leading-relaxed text-sm sm:text-base">
                SistemaGT es una plataforma web interactiva, escalable y segura,
                diseñada para optimizar el aprendizaje y la gestión de
                contenido. Nuestro objetivo es ofrecer una experiencia intuitiva
                donde los usuarios puedan acceder, explorar e interactuar con
                tutoriales en diversos formatos, como videos, textos e imágenes.
            </p>
        </div>

        <!-- Sections -->
        <div
            class="mt-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6"
        >
            <!-- Video de Capacitación Card -->
            <div
                class="bg-teal-100 p-3 sm:p-4 rounded-lg shadow-lg border border-gray-200 flex flex-col md:flex-row items-center transform transition duration-300 hover:scale-105 hover:shadow-xl"
            >
                <div
                    class="w-full md:w-1/2 flex flex-col items-center md:items-start text-center md:text-left"
                >
                    <h5
                        class="text-lg sm:text-xl font-bold text-gray-900 uppercase tracking-wide"
                    >
                        Video de Capacitación
                    </h5>
                    <p class="mt-2 text-gray-700 text-xs sm:text-sm">
                        Total Admin: 10<br />Noticias: 42
                    </p>
                    <button
                        onclick="window.location.href='/tutoriales'"
                        class="mt-4 bg-green-600 text-white font-semibold py-2 px-5 rounded-lg hover:bg-green-700 transition duration-300 text-sm sm:text-base flex items-center gap-2"
                    >
                        <i class="fas fa-play"></i> Ver más
                    </button>
                </div>

                <div
                    class="w-full md:w-1/2 flex justify-center mt-3 sm:mt-4 md:mt-0"
                >
                    <img
                        src="/img/logo.png"
                        alt="Atrápalo"
                        class="w-24 h-24 sm:w-32 sm:h-32 object-contain"
                    />
                </div>
            </div>

            <!-- Usuarios Card -->
            <div
                class="bg-teal-100 p-3 sm:p-4 rounded-lg shadow-lg border border-gray-200 flex flex-col md:flex-row items-center transform transition duration-300 hover:scale-105 hover:shadow-xl"
            >
                <div
                    class="w-full md:w-1/2 flex flex-col items-center md:items-start text-center md:text-left"
                >
                    <h2
                        class="text-lg sm:text-xl font-bold text-gray-900 uppercase"
                    >
                        Usuarios
                    </h2>
                    <p class="mt-2 text-gray-700 text-xs sm:text-sm">
                        Semana<br />Mes
                    </p>
                    <button
                        class="mt-3 sm:mt-4 bg-green-600 text-white font-semibold py-1 sm:py-2 px-3 sm:px-4 rounded-lg hover:bg-green-700 transition duration-300 text-xs sm:text-sm"
                    >
                        Ver más
                    </button>
                </div>
                <div
                    class="w-full md:w-1/2 flex justify-center mt-3 sm:mt-4 md:mt-0"
                >
                    <img
                        src="/img/logo.png"
                        alt="Atrápalo"
                        class="w-24 h-24 sm:w-32 sm:h-32 object-contain"
                    />
                </div>
            </div>

            <!-- Usuarios en línea Card -->
            <div
                class="bg-teal-100 p-3 sm:p-4 rounded-lg shadow-lg border border-gray-200 flex flex-col md:flex-row items-center transform transition duration-300 hover:scale-105 hover:shadow-xl"
            >
                <div
                    class="w-full md:w-1/2 flex flex-col items-center md:items-start text-center md:text-left"
                >
                    <h2
                        class="text-lg sm:text-xl font-bold text-gray-900 uppercase"
                    >
                        Usuarios en línea
                    </h2>
                    <p class="mt-2 text-gray-700 text-xs sm:text-sm">47</p>
                    <button
                        class="mt-3 sm:mt-4 bg-green-600 text-white font-semibold py-1 sm:py-2 px-3 sm:px-4 rounded-lg hover:bg-green-700 transition duration-300 text-xs sm:text-sm"
                    >
                        Ver más
                    </button>
                </div>
                <div
                    class="w-full md:w-1/2 flex justify-center mt-3 sm:mt-4 md:mt-0"
                >
                    <img
                        src="/img/logo.png"
                        alt="Atrápalo"
                        class="w-24 h-24 sm:w-32 sm:h-32 object-contain"
                    />
                </div>
            </div>

            <!-- Actividades Recientes Card -->
            <div
                class="bg-teal-100 p-3 sm:p-4 rounded-lg shadow-lg border border-gray-200 md:col-span-2 lg:col-span-3 flex flex-col md:flex-row items-center transform transition duration-300 hover:scale-105 hover:shadow-xl"
            >
                <div
                    class="w-full md:w-1/2 flex flex-col items-center md:items-start text-center md:text-left"
                >
                    <h2
                        class="text-lg sm:text-xl font-bold text-gray-900 uppercase"
                    >
                        Actividades Recientes
                    </h2>
                    <div class="mt-2 w-full">
                        <div
                            v-for="(activity, index) in [
                                'Farhan A Mujib',
                                'Ujang Maman',
                                'Rizal Fakhri',
                                'Alfa Zulkarnain',
                            ]"
                            :key="index"
                            class="text-gray-700 text-xs sm:text-sm"
                        >
                            <p class="font-medium">{{ activity }}</p>
                            <p class="text-xs">
                                Cras sit amet nibh libero, in gravida nulla.
                                Nulla vel metus scelerisque ante sollicitudin.
                            </p>
                        </div>
                    </div>
                    <button
                        class="mt-3 sm:mt-4 bg-green-600 text-white font-semibold py-1 sm:py-2 px-3 sm:px-4 rounded-lg hover:bg-green-700 transition duration-300 text-xs sm:text-sm"
                    >
                        Ver más
                    </button>
                </div>
                <div
                    class="w-full md:w-1/2 flex justify-center mt-3 sm:mt-4 md:mt-0"
                >
                    <img
                        src="https://via.placeholder.com/150x150.png?text=Actividades"
                        alt="Actividades Icon"
                        class="w-24 h-24 sm:w-32 sm:h-32 object-contain"
                    />
                </div>
            </div>

            <!-- Documentación Card -->
            <div
                class="bg-teal-100 p-3 sm:p-4 rounded-lg shadow-lg border border-gray-200 flex flex-col md:flex-row items-center transform transition duration-300 hover:scale-105 hover:shadow-xl"
            >
                <div
                    class="w-full md:w-1/2 flex flex-col items-center md:items-start text-center md:text-left"
                >
                    <h2
                        class="text-lg sm:text-xl font-bold text-gray-900 uppercase"
                    >
                        Documentación
                    </h2>
                    <p class="mt-2 text-gray-700 text-xs sm:text-sm">
                        Laravel tiene una documentación increíble que cubre
                        todos los aspectos del framework.
                    </p>
                    <p class="mt-2 text-xs sm:text-sm">
                        <a
                            href="https://laravel.com/docs"
                            class="inline-flex items-center font-semibold text-indigo-700"
                        >
                            Explorar la documentación
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 20 20"
                                class="ml-1 w-4 h-4 sm:w-5 sm:h-5 fill-indigo-500"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M5 10a.75.75 0 01.75-.75h6.638L10.23 7.29a.75.75 0 111.04-1.08l3.5 3.25a.75.75 0 010 1.08l-3.5 3.25a.75.75 0 11-1.04-1.08l2.158-1.96H5.75A.75.75 0 015 10z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                        </a>
                    </p>
                    <button
                        class="mt-3 sm:mt-4 bg-green-600 text-white font-semibold py-1 sm:py-2 px-3 sm:px-4 rounded-lg hover:bg-green-700 transition duration-300 text-xs sm:text-sm"
                    >
                        Ver más
                    </button>
                </div>
                <div
                    class="w-full md:w-1/2 flex justify-center mt-3 sm:mt-4 md:mt-0"
                >
                    <img
                        src="https://via.placeholder.com/150x150.png?text=Documentación"
                        alt="Documentación Icon"
                        class="w-24 h-24 sm:w-32 sm:h-32 object-contain"
                    />
                </div>
            </div>
        </div>
    </div>
</template>
