<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { router } from "@inertiajs/vue3";

const goBack = () => {
    window.history.back();
};
</script>

<template>
    <AppLayout title="Acceso Denegado">
        <template #header>Acceso Denegado</template>

        <div class="py-6">
            <div class="max-w-8xl mx-auto sm:px-8 lg:px-8">
                <!-- Div que queremos animar -->
                <div
                    class="-mt-1 bg-white border border-gray-200 shadow-xl rounded-lg p-8 z-10"
                    :class="{ 'animate-fadeIn': true }"
                >
                    <!-- Imagen de error -->
                    <img
                        src="/img/AccesosDenegado.png"
                        alt="Acceso Denegado"
                        class="mx-auto mb-6 w-1/3"
                    />

                    <h2
                        class="text-3xl font-bold text-center text-red-600 mb-4"
                    >
                        No tienes permisos para acceder a esta página.
                    </h2>
                    <p class="text-center text-lg text-gray-700 mb-6">
                        Si crees que esto es un error, por favor contacta con el
                        administrador.
                    </p>

                    <div class="text-center">
                        <button
                            @click="goBack"
                            class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-500 ease-in-out animate-bounce-in"
                        >
                            Volver
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
/* Animación de entrada para el div principal */
@keyframes fadeIn {
    0% {
        opacity: 0;
        transform: translateY(10px);
    }
    100% {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Animación de entrada para el botón */
@keyframes bounceIn {
    0% {
        transform: scale(0.8);
        opacity: 0;
    }
    50% {
        transform: scale(1.05);
        opacity: 1;
    }
    100% {
        transform: scale(1);
    }
}

/* Animación de hover para el botón */
@keyframes pulse {
    0% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.05);
    }
    100% {
        transform: scale(1);
    }
}

.animate-fadeIn {
    animation: fadeIn 1s ease-out;
}

.animate-bounce-in {
    animation: bounceIn 0.8s ease-out;
}

button {
    min-width: 120px;
}

button:hover {
    animation: pulse 1s infinite;
    box-shadow: 0 0 15px rgba(59, 130, 246, 0.6);
}
</style>
