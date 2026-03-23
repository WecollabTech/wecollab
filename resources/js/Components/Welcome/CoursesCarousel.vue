<script setup>
import { ref, computed } from "vue";
import CourseCard from "./CourseCard.vue";

const props = defineProps({
    courses: {
        type: Array,
        required: true,
        default: () => [],
    },
});

const coursesContainer = ref(null);
const isDragging = ref(false);
const startX = ref(0);
const scrollLeft = ref(0);

// 👉 Detectar si usar carrusel
const isScrollable = computed(() => props.courses.length > 3);

// 👉 Scroll dinámico
const getScrollAmount = () => {
    const width = window.innerWidth;
    if (width >= 1280) return 400 * 3;
    if (width >= 1024) return 400 * 2;
    if (width >= 768) return 400;
    return 300;
};

const scrollLeftCarousel = () => {
    coursesContainer.value?.scrollBy({
        left: -getScrollAmount(),
        behavior: "smooth",
    });
};

const scrollRightCarousel = () => {
    coursesContainer.value?.scrollBy({
        left: getScrollAmount(),
        behavior: "smooth",
    });
};

// 👉 Drag
const handleMouseDown = (e) => {
    if (!coursesContainer.value) return;
    isDragging.value = true;
    startX.value = e.pageX - coursesContainer.value.offsetLeft;
    scrollLeft.value = coursesContainer.value.scrollLeft;
    coursesContainer.value.style.cursor = "grabbing";
};

const handleMouseLeave = () => {
    isDragging.value = false;
    if (coursesContainer.value) coursesContainer.value.style.cursor = "grab";
};

const handleMouseUp = () => {
    isDragging.value = false;
    if (coursesContainer.value) coursesContainer.value.style.cursor = "grab";
};

const handleMouseMove = (e) => {
    if (!isDragging.value || !coursesContainer.value) return;
    e.preventDefault();
    const x = e.pageX - coursesContainer.value.offsetLeft;
    const walk = (x - startX.value) * 1.8;
    coursesContainer.value.scrollLeft = scrollLeft.value - walk;
};
</script>

<template>
    <div class="w-full mb-16">
        <!-- CONTENEDOR ESTILO PREMIUM -->
        <div
            class="bg-gradient-to-r from-gray-50 via-white to-gray-100 rounded-3xl p-6 sm:p-8 lg:p-10 shadow-xl border border-gray-200"
        >
            <!-- HEADER -->
            <div class="mb-10 text-left max-w-2xl">
                <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-2">
                    Cursos <span class="text-indigo-600">populares</span>
                </h2>
                <p class="text-gray-600 text-base lg:text-lg">
                    Contenido exclusivo para miembros SLC y capacitaciones
                    disponibles en la plataforma.
                </p>
            </div>

            <!-- CONTENIDO -->
            <div class="relative">
                <!-- 🟢 GRID (POCOS CURSOS) -->
                <div
                    v-if="!isScrollable"
                    class="flex flex-wrap justify-center gap-6"
                >
                    <CourseCard
                        v-for="(course, idx) in courses"
                        :key="idx"
                        :course="course"
                        class="w-full sm:w-[420px] md:w-[480px] bg-white rounded-2xl shadow-md hover:shadow-xl transition duration-300 hover:scale-[1.04]"
                    />
                </div>

                <!-- 🔵 CARRUSEL (MUCHOS CURSOS) -->
                <div v-else>
                    <!-- Botón izquierda -->
                    <button
                        @click="scrollLeftCarousel"
                        class="absolute left-2 top-1/2 -translate-y-1/2 z-10 w-11 h-11 bg-white rounded-full shadow-lg flex items-center justify-center hover:bg-gray-50 transition transform hover:scale-110"
                    >
                        <i class="fas fa-chevron-left text-gray-700"></i>
                    </button>

                    <!-- SCROLL -->
                    <div
                        ref="coursesContainer"
                        class="flex gap-6 overflow-x-auto scroll-smooth snap-x snap-mandatory px-4 sm:px-6 pb-4"
                        style="scrollbar-width: none; cursor: grab"
                        @mousedown="handleMouseDown"
                        @mouseleave="handleMouseLeave"
                        @mouseup="handleMouseUp"
                        @mousemove="handleMouseMove"
                    >
                        <CourseCard
                            v-for="(course, idx) in courses"
                            :key="idx"
                            :course="course"
                            class="flex-shrink-0 snap-center basis-[90%] sm:basis-[calc(50%-12px)] lg:basis-[calc(33.333%-16px)] xl:basis-[calc(25%-18px)] bg-white rounded-2xl shadow-md hover:shadow-xl transition duration-300 hover:scale-[1.03]"
                        />
                    </div>

                    <!-- Botón derecha -->
                    <button
                        @click="scrollRightCarousel"
                        class="absolute right-2 top-1/2 -translate-y-1/2 z-10 w-11 h-11 bg-white rounded-full shadow-lg flex items-center justify-center hover:bg-gray-50 transition transform hover:scale-110"
                    >
                        <i class="fas fa-chevron-right text-gray-700"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<style>
::-webkit-scrollbar {
    display: none;
}
</style>
