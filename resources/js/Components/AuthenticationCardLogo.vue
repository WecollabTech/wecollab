<script setup>
import { Link } from "@inertiajs/vue3";
</script>

<template>
    <Link :href="'/'" class="logo-link">
        <div class="logo-container">
            <img src="/img/logo.png" alt="Atrápalo" class="logo custom-logo" />
        </div>
    </Link>
</template>

<style scoped>
.logo-container {
    display: flex;
    align-items: center;
    justify-content: center;
    margin-top: 20px; /* Ajusta este valor según sea necesario */
}

.custom-logo {
    width: 120px; /* Ajusta el tamaño de la imagen */
    height: auto; /* Mantiene la proporción de la imagen */
    margin-right: 10px; /* Separación entre logo y texto */
}

.logo-text {
    font-size: 24px; /* Tamaño del texto */
    font-weight: bold; /* Fuente en negrita */
    color: #333; /* Color del texto */
}

.logo-link {
    text-decoration: none; /* Elimina el subrayado del enlace */
}
</style>
