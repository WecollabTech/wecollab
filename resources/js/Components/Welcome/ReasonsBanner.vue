<template>
    <div
        class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 rounded-3xl p-8 sm:p-10 mb-10 shadow-2xl text-white relative overflow-hidden w-full"
    >
        <!-- Decoración -->
        <div
            class="absolute -top-10 -right-10 w-40 h-40 bg-white/10 rounded-full blur-3xl"
        ></div>
        <div
            class="absolute -bottom-10 -left-10 w-40 h-40 bg-white/10 rounded-full blur-3xl"
        ></div>
        <div class="absolute inset-0 bg-black/10"></div>

        <!-- Layout ancho completo -->
        <div
            class="relative grid grid-cols-1 md:grid-cols-[auto,1fr] gap-6 items-start w-full"
        >
            <!-- Logo -->
            <div class="flex justify-center md:justify-start">
                <div
                    class="w-24 h-24 flex items-center justify-center bg-white/95 backdrop-blur-xl rounded-2xl shadow-xl border border-white/60 transition-all duration-300 hover:scale-105"
                >
                    <img
                        src="/img/logo.png"
                        alt="SLC Logo"
                        class="w-14 h-14 object-contain"
                    />
                </div>
            </div>

            <!-- Contenido ocupa TODO el ancho -->
            <div class="w-full">
                <!-- Badge -->
                <span
                    class="inline-block bg-white/20 text-white text-xs px-3 py-1 rounded-full mb-2"
                >
                    Plataforma corporativa
                </span>

                <!-- Título -->
                <h2
                    class="text-2xl sm:text-3xl font-extrabold mb-2 tracking-tight"
                >
                    ¿Qué es SLC?
                </h2>

                <!-- Subtítulo -->
                <p class="text-white/80 text-sm sm:text-base mb-3">
                    Aprende, resuelve dudas y mejora tu desempeño en un solo
                    lugar.
                </p>

                <!-- Descripción JUSTIFICADA FULL WIDTH -->
                <p
                    class="text-white/90 text-base sm:text-lg leading-relaxed text-justify mb-4 w-full"
                >
                    <strong>SynergyFlow Support & Learning Center (SLC)</strong>
                    es una plataforma integral diseñada para centralizar el
                    aprendizaje, el soporte y la mejora continua dentro de tu
                    organización. Permite acceder a capacitaciones, resolver
                    dudas de forma eficiente y fortalecer habilidades clave en
                    un solo entorno digital, facilitando el crecimiento
                    profesional y aumentando la productividad.
                </p>

                <!-- Línea decorativa -->
                <div class="w-20 h-1 bg-white/40 rounded mb-4"></div>

                <!-- Crédito -->
                <p class="text-white/70 text-sm mb-5">
                    Desarrollado por <strong>We Collab</strong>
                </p>

                <!-- CTA -->
                <button
                    class="inline-flex items-center gap-2 bg-white text-purple-700 px-6 py-3 rounded-full font-semibold text-sm shadow-md hover:shadow-lg hover:bg-gray-100 transition-all duration-300 hover:scale-105"
                >
                    <i class="fas fa-rocket"></i>
                    Comenzar ahora
                </button>
            </div>
        </div>
    </div>
</template>
