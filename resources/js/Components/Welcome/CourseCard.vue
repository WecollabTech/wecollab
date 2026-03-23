<script setup>
const props = defineProps({
    course: {
        type: Object,
        required: true,
        default: () => ({}),
    },
});
</script>

<template>
    <div
        class="flex flex-col bg-white rounded-2xl overflow-hidden shadow-md hover:shadow-2xl transition-all duration-300 border border-gray-100 hover:-translate-y-2 group snap-start"
    >
        <!-- Imagen -->
        <div class="relative h-48 overflow-hidden">
            <img
                :src="course.image"
                :alt="course.title"
                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                loading="lazy"
            />

            <!-- Overlay -->
            <div
                class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent"
            ></div>

            <!-- Categoría -->
            <div class="absolute top-3 left-3">
                <span
                    class="inline-flex items-center px-3 py-1 bg-white/90 backdrop-blur-sm rounded-lg text-xs font-semibold text-gray-900 shadow"
                >
                    {{ course.category }}
                </span>
            </div>

            <!-- Icono lock -->
            <div class="absolute top-3 right-3">
                <div
                    class="w-10 h-10 bg-white/90 backdrop-blur-sm rounded-full flex items-center justify-center shadow-md"
                >
                    <i class="fas fa-lock text-gray-700"></i>
                </div>
            </div>

            <!-- Rating -->
            <div
                class="absolute bottom-3 left-3 right-3 flex items-center justify-between text-white text-xs font-medium"
            >
                <span class="flex items-center gap-1">
                    <i class="fas fa-star text-yellow-400"></i>
                    {{ course.rating }}
                </span>
                <span>{{ course.students }}</span>
            </div>
        </div>

        <!-- Contenido -->
        <div class="flex flex-col flex-grow p-5">
            <!-- Título -->
            <h3 class="text-base font-bold text-gray-900 mb-2 line-clamp-2">
                {{ course.title }}
            </h3>

            <!-- Badge -->
            <div class="flex items-center gap-2 mb-4">
                <span
                    class="inline-flex items-center px-2.5 py-1 bg-indigo-100 text-indigo-700 rounded-lg text-xs font-semibold"
                >
                    <i class="fas fa-lock text-xs mr-1"></i> Exclusivo
                </span>
            </div>

            <!-- Botón -->
            <button
                class="mt-auto w-full px-4 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white text-sm font-semibold rounded-lg hover:from-indigo-700 hover:to-purple-700 transition-all duration-300 shadow-md flex items-center justify-center gap-2 group-hover:shadow-xl"
            >
                <i class="fas fa-lock"></i>
                Desbloquear
            </button>
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
