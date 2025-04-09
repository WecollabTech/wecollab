<script setup>
// Aquí puedes agregar lógica si es necesario
</script>

<template>
    <div class="container mx-auto px-6 lg:px-12 py-8">
        <!-- Header -->
        <div class="p-8 bg-white border border-gray-300 rounded-lg shadow-lg">
            <h1 class="text-3xl font-bold text-gray-900">
                ¡Bienvenido al Nuevo Dashboard!
            </h1>

            <p class="mt-4 text-gray-600 leading-relaxed">
                Este es el nuevo dashboard de tu aplicación. Aquí puedes agregar
                contenido y componentes interactivos.
            </p>
        </div>

        <!-- Sección con tarjetas estadísticas -->
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
        </div>
    </div>
</template>

<style scoped>
/* Puedes agregar estilos específicos aquí si es necesario */
</style>
