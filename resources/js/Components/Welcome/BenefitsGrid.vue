<script setup>
import { ref, computed } from "vue";
import BenefitCard from "./BenefitCard.vue";

const props = defineProps({
    benefits: {
        type: Array,
        required: true,
        default: () => [],
    },
});

const benefitsContainer = ref(null);
const isDragging = ref(false);
const startX = ref(0);
const scrollLeft = ref(0);

// 👉 Carrusel solo si hay muchos
const isScrollable = computed(() => props.benefits.length > 3);

// Scroll dinámico
const scrollLeftCarousel = () => {
    benefitsContainer.value?.scrollBy({ left: -400, behavior: "smooth" });
};

const scrollRightCarousel = () => {
    benefitsContainer.value?.scrollBy({ left: 400, behavior: "smooth" });
};

// Drag
const handleMouseDown = (e) => {
    if (!benefitsContainer.value) return;
    isDragging.value = true;
    startX.value = e.pageX - benefitsContainer.value.offsetLeft;
    scrollLeft.value = benefitsContainer.value.scrollLeft;
    benefitsContainer.value.style.cursor = "grabbing";
};

const handleMouseLeave = () => {
    isDragging.value = false;
    benefitsContainer.value.style.cursor = "grab";
};

const handleMouseUp = () => {
    isDragging.value = false;
    benefitsContainer.value.style.cursor = "grab";
};

const handleMouseMove = (e) => {
    if (!isDragging.value || !benefitsContainer.value) return;
    e.preventDefault();
    const x = e.pageX - benefitsContainer.value.offsetLeft;
    const walk = (x - startX.value) * 2;
    benefitsContainer.value.scrollLeft = scrollLeft.value - walk;
};
</script>

<template>
    <div class="w-full mb-16">
        <!-- CONTENEDOR -->
        <div
            class="bg-gradient-to-r from-gray-50 via-white to-gray-100 rounded-3xl p-6 sm:p-8 lg:p-10 shadow-xl border border-gray-200"
        >
            <!-- HEADER CON TIPOGRAFÍA PERSONALIZADA -->
            <div class="mb-10 text-left max-w-2xl">
                <h2
                    class="text-3xl lg:text-4xl leading-tight mb-3 font-helvetica-bold text-gray-900"
                >
                    <strong class="text-indigo-700">Beneficios</strong> de ser
                    <span
                        class="text-transparent bg-clip-text bg-gradient-to-r from-yellow-500 to-orange-500"
                    >
                        cliente SLC
                    </span>
                </h2>

                <p
                    class="text-gray-700 text-base lg:text-lg font-helvetica-regular"
                >
                    Descubre todo lo que obtienes al
                    <strong class="text-indigo-600"
                        >unirte a la plataforma</strong
                    >
                    y comienza a aprovechar todas sus ventajas desde el primer
                    día.
                </p>
            </div>

            <!-- CONTENIDO -->
            <div class="relative">
                <!-- 🟢 GRID (POCOS → centrado perfecto) -->
                <div
                    v-if="!isScrollable"
                    class="flex flex-wrap justify-center gap-6"
                >
                    <BenefitCard
                        v-for="(benefit, idx) in benefits"
                        :key="idx"
                        :benefit="benefit"
                        class="w-full sm:w-[420px] bg-white rounded-2xl shadow-md hover:shadow-xl transition hover:scale-[1.03]"
                    />
                </div>

                <!-- 🔵 CARRUSEL (MUCHOS → SIN CORTES) -->
                <div v-else>
                    <!-- Botón izquierda -->
                    <button
                        @click="scrollLeftCarousel"
                        class="absolute left-2 top-1/2 -translate-y-1/2 z-10 w-11 h-11 bg-white rounded-full shadow-lg flex items-center justify-center hover:bg-gray-50 transition"
                    >
                        <i class="fas fa-chevron-left text-gray-700"></i>
                    </button>

                    <!-- SCROLL -->
                    <div
                        ref="benefitsContainer"
                        class="flex gap-6 overflow-x-auto scroll-smooth snap-x snap-mandatory px-4 sm:px-6 pb-4"
                        style="scrollbar-width: none; cursor: grab"
                        @mousedown="handleMouseDown"
                        @mouseleave="handleMouseLeave"
                        @mouseup="handleMouseUp"
                        @mousemove="handleMouseMove"
                    >
                        <BenefitCard
                            v-for="(benefit, idx) in benefits"
                            :key="idx"
                            :benefit="benefit"
                            class="flex-shrink-0 snap-center basis-full sm:basis-[calc(50%-12px)] lg:basis-[calc(33.333%-16px)] xl:basis-[calc(25%-18px)] bg-white rounded-2xl shadow-md hover:shadow-xl transition"
                        />
                    </div>

                    <!-- Botón derecha -->
                    <button
                        @click="scrollRightCarousel"
                        class="absolute right-2 top-1/2 -translate-y-1/2 z-10 w-11 h-11 bg-white rounded-full shadow-lg flex items-center justify-center hover:bg-gray-50 transition"
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

/* FUENTES PERSONALIZADAS */
.font-helvetica-bold {
    font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
    font-weight: 700;
}

.font-helvetica-regular {
    font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
    font-weight: 400;
}

.font-jura {
    font-family: "Jura", sans-serif;
    font-weight: 600;
}
</style>
