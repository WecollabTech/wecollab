<script setup>
import InfoCard from "./InfoCard.vue";

const infoCards = [
    {
        icon: "fa-pen-to-square",
        title: "Requisitos Previos",
        description: "Conocimientos básicos y acceso a internet.",
        color: "from-blue-500 to-blue-600",
    },
    {
        icon: "fa-clock",
        title: "Duración",
        description: "Cursos flexibles de 4 a 12 semanas.",
        color: "from-purple-500 to-purple-600",
    },
    {
        icon: "fa-graduation-cap",
        title: "Certificación",
        description: "Diploma reconocido al completar el curso.",
        color: "from-pink-500 to-pink-600",
    },
];
</script>

<template>
    <div class="text-center mb-12">
        <div
            class="inline-flex items-center justify-center w-14 h-14 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl mb-4 shadow-xl shadow-indigo-500/30"
        >
            <i class="fas fa-list-check text-2xl text-white"></i>
        </div>
        <h3 class="text-2xl font-bold text-gray-900 mb-3">
            Información para curso
        </h3>
        <p class="text-base text-gray-600 max-w-3xl mx-auto leading-relaxed">
            Revisa todo lo que necesitas antes de tu registro para evitar
            complicaciones
        </p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <InfoCard v-for="(card, idx) in infoCards" :key="idx" v-bind="card" />
    </div>
</template>
