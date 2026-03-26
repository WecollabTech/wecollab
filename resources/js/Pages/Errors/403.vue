<script setup>
import { router } from "@inertiajs/vue3";
</script>

<template>
    <div
        class="min-h-screen flex flex-col items-center justify-center bg-gray-100 text-center p-6"
    >
        <!-- Imagen -->
        <img
            src="/img/AccesosDenegado.png"
            alt="Sin acceso"
            class="w-64 mb-6"
        />

        <!-- Texto -->
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Acceso denegado</h1>

        <p class="text-gray-600 mb-6">
            No tienes permiso para ver este contenido
        </p>

        <!-- Botones -->
        <div class="flex gap-3">
            <button
                @click="router.visit('/dashboard')"
                class="px-5 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-700"
            >
                Ir al inicio
            </button>
        </div>
    </div>
</template>
