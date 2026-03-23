<!-- resources/js/Components/Welcome/HeroCarousel.vue -->
<script setup>
import { ref, onMounted, onUnmounted } from "vue";
import { Swiper, SwiperSlide } from "swiper/vue";
import { Autoplay, EffectFade, Pagination, Navigation } from "swiper/modules";
import "swiper/css";
import "swiper/css/effect-fade";
import "swiper/css/pagination";
import "swiper/css/navigation";

const props = defineProps({
    slides: {
        type: Array,
        required: true,
        default: () => [],
    },
});

const swiperRef = ref(null);
const currentSlide = ref(0);
const totalSlides = props.slides.length;
let isSwiperUpdating = false;

// Exponer métodos para controlar autoplay desde el padre
defineExpose({
    startAutoplay: () => {
        if (swiperRef.value?.swiper) {
            swiperRef.value.swiper.autoplay.start();
        }
    },
    resetAutoplay: () => {
        if (swiperRef.value?.swiper) {
            swiperRef.value.swiper.autoplay.stop();
            swiperRef.value.swiper.autoplay.start();
        }
    },
});

// Sincronizar el estado del slide actual
const updateCurrentSlide = () => {
    if (swiperRef.value?.swiper && !isSwiperUpdating) {
        isSwiperUpdating = true;
        currentSlide.value = swiperRef.value.swiper.realIndex;
        setTimeout(() => {
            isSwiperUpdating = false;
        }, 100);
    }
};

onMounted(() => {
    if (swiperRef.value?.swiper) {
        swiperRef.value.swiper.autoplay.start();
        currentSlide.value = swiperRef.value.swiper.realIndex;
        swiperRef.value.swiper.on("slideChange", updateCurrentSlide);
    }
});

onUnmounted(() => {
    if (swiperRef.value?.swiper) {
        swiperRef.value.swiper.autoplay.stop();
        swiperRef.value.swiper.off("slideChange", updateCurrentSlide);
    }
});
</script>

<template>
    <div class="relative mb-6 sm:mb-8 lg:mb-12 group">
        <!-- Contenedor principal -->
        <div
            class="rounded-3xl sm:rounded-[2.5rem] h-[28rem] sm:h-[32rem] md:h-[36rem] lg:h-[40rem] xl:h-[35rem] w-full shadow-2xl overflow-hidden relative ring-1 ring-black/10"
        >
            <!-- Glow effect decorativo -->
            <div
                class="absolute -top-20 -right-20 w-40 h-40 bg-indigo-500/20 rounded-full blur-3xl pointer-events-none"
            ></div>
            <div
                class="absolute -bottom-20 -left-20 w-40 h-40 bg-purple-500/20 rounded-full blur-3xl pointer-events-none"
            ></div>

            <!-- Swiper con clases ÚNICAS para evitar conflictos -->
            <Swiper
                ref="swiperRef"
                :modules="[Autoplay, EffectFade, Pagination, Navigation]"
                :autoplay="{
                    delay: 5000,
                    disableOnInteraction: false,
                    pauseOnMouseEnter: true,
                    waitForTransition: true,
                }"
                :effect="'fade'"
                :fade-effect="{ crossFade: true }"
                :speed="700"
                :loop="true"
                :grab-cursor="true"
                :prevent-interaction-on-transition="false"
                :pagination="{
                    clickable: true,
                    el: '.hero-swiper-pagination',
                    bulletClass: 'hero-swiper-bullet',
                    bulletActiveClass: 'hero-swiper-bullet-active',
                }"
                :navigation="{
                    nextEl: '.hero-swiper-next',
                    prevEl: '.hero-swiper-prev',
                }"
                class="w-full h-full"
            >
                <SwiperSlide
                    v-for="(slide, index) in props.slides"
                    :key="index"
                >
                    <div class="absolute inset-0">
                        <!-- Imagen con overlay -->
                        <div class="absolute inset-0">
                            <img
                                :src="slide.image"
                                class="w-full h-full object-cover object-center transition-transform duration-1000 group-hover:scale-105"
                                loading="eager"
                                :alt="slide.title"
                                @error="
                                    $event.target.src =
                                        '/img/placeholder-carousel.jpg'
                                "
                            />
                            <!-- Overlay gradient -->
                            <div
                                class="absolute inset-0 bg-gradient-to-r from-black/70 via-black/40 to-transparent sm:from-black/65 sm:via-black/35"
                            ></div>
                            <div
                                class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent sm:from-black/45"
                            ></div>
                        </div>

                        <!-- Contenido -->
                        <div
                            class="relative h-full flex items-center px-6 sm:px-8 md:px-12 lg:px-20"
                        >
                            <div
                                class="max-w-full sm:max-w-2xl md:max-w-3xl text-white"
                            >
                                <!-- Badge -->
                                <div
                                    class="inline-flex items-center gap-2 px-4 py-2 bg-white/20 backdrop-blur-md rounded-full text-xs sm:text-sm font-semibold mb-4 sm:mb-6 border border-white/30 shadow-lg"
                                >
                                    <span
                                        class="w-2 h-2 bg-green-400 rounded-full animate-pulse"
                                    ></span>
                                    <span>✨ Nuevo Curso Disponible</span>
                                </div>

                                <!-- Título -->
                                <h2
                                    class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl xl:text-7xl font-black mb-3 sm:mb-4 lg:mb-6 leading-tight drop-shadow-2xl"
                                >
                                    {{ slide.title }}
                                </h2>

                                <!-- Subtítulo -->
                                <p
                                    class="text-base sm:text-lg md:text-xl lg:text-2xl font-bold text-white/95 mb-4 sm:mb-6 lg:mb-8 drop-shadow-lg"
                                >
                                    {{ slide.subtitle }}
                                </p>

                                <!-- Descripción -->
                                <p
                                    class="hidden sm:block text-sm sm:text-base md:text-lg text-white/90 mb-6 sm:mb-8 lg:mb-10 leading-relaxed max-w-2xl"
                                >
                                    Domina las habilidades más demandadas del
                                    mercado con nuestros cursos especializados.
                                    Aprende de expertos y lleva tu carrera al
                                    siguiente nivel.
                                </p>

                                <!-- Botón -->
                                <div>
                                    <button
                                        class="group px-6 py-3 sm:px-8 sm:py-4 bg-gradient-to-r from-orange-500 to-red-500 text-white text-sm sm:text-base font-bold rounded-2xl hover:from-orange-600 hover:to-red-600 transition-all duration-300 shadow-xl shadow-black/30 hover:shadow-2xl hover:shadow-black/40 hover:-translate-y-1 flex items-center gap-2"
                                    >
                                        <span>Ver más</span>
                                        <i
                                            class="fas fa-arrow-right w-4 h-4 group-hover:translate-x-1 transition-transform duration-300"
                                        ></i>
                                    </button>
                                </div>

                                <!-- Texto legal -->
                                <p
                                    class="hidden sm:block text-xs text-white/60 mt-4"
                                >
                                    Sujeto a disponibilidad, aplican TyC.
                                </p>
                            </div>
                        </div>
                    </div>
                </SwiperSlide>
            </Swiper>

            <!-- Controls con clases ÚNICAS y z-index alto -->
            <div class="absolute bottom-6 left-1/2 -translate-x-1/2 z-40">
                <div
                    class="bg-white/95 backdrop-blur-xl rounded-full px-4 py-3 shadow-2xl border border-white/60 inline-flex items-center gap-3 ring-1 ring-black/10"
                >
                    <button
                        class="hero-swiper-prev p-2 hover:bg-gray-100 rounded-full transition-all duration-300 w-10 h-10 flex items-center justify-center hover:scale-110 active:scale-95"
                        aria-label="Slide anterior"
                    >
                        <i
                            class="fas fa-chevron-left w-4 h-4 text-gray-700"
                        ></i>
                    </button>

                    <!-- Pagination con clase única -->
                    <div
                        class="hero-swiper-pagination flex items-center gap-2"
                    ></div>

                    <button
                        class="hero-swiper-next p-2 hover:bg-gray-100 rounded-full transition-all duration-300 w-10 h-10 flex items-center justify-center hover:scale-110 active:scale-95"
                        aria-label="Siguiente slide"
                    >
                        <i
                            class="fas fa-chevron-right w-4 h-4 text-gray-700"
                        ></i>
                    </button>
                </div>
            </div>

            <!-- Progress bar del autoplay -->
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-black/20">
                <div
                    class="h-full bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 transition-all duration-100 ease-linear"
                    :style="{
                        width: `${((currentSlide + 1) / totalSlides) * 100}%`,
                    }"
                ></div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* ========================================
   HERO SWIPER - ESTILOS ÚNICOS (NO AFECTAN OTROS COMPONENTES)
   ======================================== */

/* Asegurar que los slides ocupen todo el contenedor */
:deep(.swiper-slide) {
    width: 100% !important;
    height: 100% !important;
}

/* Efecto fade suave */
:deep(.swiper-fade .swiper-slide) {
    pointer-events: none;
    transition-property: opacity;
}
:deep(.swiper-fade .swiper-slide-active) {
    pointer-events: auto;
}

/* Permitir interacción con controles */
:deep(.swiper) {
    overflow: visible !important;
}

/* ========================================
   PAGINATION - CLASES ÚNICAS PARA HERO
   ======================================== */

/* Bullet normal - SOLO para Hero */
:deep(.hero-swiper-bullet) {
    width: 10px !important;
    height: 10px !important;
    background: rgba(156, 163, 175, 0.6) !important;
    opacity: 1 !important;
    margin: 0 4px !important;
    transition: all 0.3s ease !important;
    border-radius: 50% !important;
    cursor: pointer;
}

/* Bullet activo - SOLO para Hero */
:deep(.hero-swiper-bullet-active) {
    width: 28px !important;
    background: linear-gradient(135deg, #4f46e5, #7c3aed) !important;
    border-radius: 5px !important;
    box-shadow: 0 2px 8px rgba(79, 70, 229, 0.4) !important;
}

/* Hover en bullets - SOLO para Hero */
:deep(.hero-swiper-bullet:hover) {
    background: rgba(107, 114, 128, 0.8) !important;
    transform: scale(1.2);
}

/* ========================================
   NAVEGACIÓN - CLASES ÚNICAS PARA HERO
   ======================================== */

:deep(.hero-swiper-prev),
:deep(.hero-swiper-next) {
    /* Los estilos ya están en las clases de Tailwind del template */
}

/* Ocultar flechas por defecto de Swiper */
:deep(.swiper-button-next),
:deep(.swiper-button-prev) {
    display: none !important;
}

/* Mejorar rendering de texto */
h2,
p,
button {
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}
</style>
