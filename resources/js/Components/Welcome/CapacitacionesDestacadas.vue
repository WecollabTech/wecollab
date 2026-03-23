<!-- resources/js/Components/Welcome/CapacitacionesDestacadas.vue -->
<script setup>
import { ref, computed, onMounted, onBeforeUnmount, watch } from "vue";
import { router } from "@inertiajs/vue3";
import { Swiper, SwiperSlide } from "swiper/vue";
import { Navigation, Pagination, Autoplay } from "swiper/modules";
import "swiper/css";
import "swiper/css/navigation";
import "swiper/css/pagination";
import Swal from "sweetalert2";

// ============================================
// 📦 DATOS DE TUTORIALES CON YOUTUBE VIDEO ID
// ============================================
const tutoriales = ref([
    {
        id: 1,
        titulo: "Configuración Inicial de Bitrix24 CRM",
        descripcion: "Aprende a configurar tu CRM en Bitrix24 desde cero.",
        videoId: "3LrKsyPJVME",
        imagen: "https://img.youtube.com/vi/3LrKsyPJVME/maxresdefault.jpg",
        link: "/bitrix24-crm-setup",
        categoria: "CRM",
        duracion: "1.5 horas",
        esNuevo: true,
        esPopular: true,
        progreso: 25,
    },
    {
        id: 2,
        titulo: "Automatización de Procesos en Bitrix24",
        descripcion: "Optimiza flujos de trabajo con reglas y triggers.",
        videoId: "vQ3r7jGirEo",
        imagen: "https://img.youtube.com/vi/vQ3r7jGirEo/maxresdefault.jpg",
        link: "/bitrix24-automation",
        categoria: "Automatización",
        duracion: "2 horas",
        esNuevo: false,
        esPopular: true,
        progreso: 70,
    },
    {
        id: 3,
        titulo: "Diseño de Formularios en Bitrix24",
        descripcion: "Crea formularios personalizados para captar leads.",
        videoId: "RlfnjCQ3gak",
        imagen: "https://img.youtube.com/vi/RlfnjCQ3gak/maxresdefault.jpg",
        link: "/bitrix24-forms",
        categoria: "CRM",
        duracion: "1 hora",
        esNuevo: true,
        esPopular: false,
        progreso: 10,
    },
    {
        id: 4,
        titulo: "Gestión de Leads con Bitrix24",
        descripcion:
            "Convierte más prospectos en clientes con estrategias efectivas.",
        videoId: "MEDybci-8oA",
        imagen: "https://img.youtube.com/vi/MEDybci-8oA/maxresdefault.jpg",
        link: "/bitrix24-leads",
        categoria: "CRM",
        duracion: "1.5 horas",
        esNuevo: false,
        esPopular: true,
        progreso: 45,
    },
    {
        id: 5,
        titulo: "Reportes y Analytics en Bitrix24",
        descripcion:
            "Toma decisiones basadas en datos con reportes personalizados.",
        videoId: "3LrKsyPJVME",
        imagen: "https://img.youtube.com/vi/3LrKsyPJVME/maxresdefault.jpg",
        link: "/bitrix24-analytics",
        categoria: "Automatización",
        duracion: "2.5 horas",
        esNuevo: true,
        esPopular: false,
        progreso: 0,
    },
    {
        id: 6,
        titulo: "Integraciones con Bitrix24",
        descripcion: "Conecta Bitrix24 con tus herramientas favoritas.",
        videoId: "vQ3r7jGirEo",
        imagen: "https://img.youtube.com/vi/vQ3r7jGirEo/maxresdefault.jpg",
        link: "/bitrix24-integrations",
        categoria: "Automatización",
        duracion: "2 horas",
        esNuevo: false,
        esPopular: true,
        progreso: 90,
    },
]);

// ============================================
// CONFIGURACIÓN DEL COMPONENTE
// ============================================
const props = defineProps({
    categorias: {
        type: Array,
        default: () => ["Todos", "CRM", "Automatización"],
    },
    isLoggedIn: {
        type: Boolean,
        default: false,
    },
    previewDuration: {
        type: Number,
        default: 25,
    },
});

const emit = defineEmits([
    "share",
    "toggle-favorite",
    "card-click",
    "preview-ended",
]);

// Estado local
const searchQuery = ref("");
const selectedCategory = ref("Todos");
const favorites = ref(JSON.parse(localStorage.getItem("favorites")) || []);

// Estado del modal de video
const isModalOpen = ref(false);
const selectedTutorial = ref(null);
const modalVideoPlayer = ref(null);
const modalPreviewState = ref({
    isPlaying: false,
    currentTime: 0,
    isBlocked: false,
    checkInterval: null,
});

// ============================================
// LÓGICA DE FILTRADO
// ============================================
const filteredTutoriales = computed(() => {
    return tutoriales.value.filter((tutorial) => {
        const matchesSearch = tutorial.titulo
            .toLowerCase()
            .includes(searchQuery.value.toLowerCase());
        const matchesCategory =
            props.categorias.includes("Todos") &&
            selectedCategory.value === "Todos"
                ? true
                : tutorial.categoria === selectedCategory.value;
        return matchesSearch && matchesCategory;
    });
});

// ============================================
// 🎬 SISTEMA DE MODAL CON PREVIEW DE VIDEO
// ============================================

// Abrir modal con tutorial seleccionado
const openVideoModal = (tutorial) => {
    selectedTutorial.value = tutorial;
    isModalOpen.value = true;
    document.body.style.overflow = "hidden";

    if (!window.YT) {
        const tag = document.createElement("script");
        tag.src = "https://www.youtube.com/iframe_api";
        const firstScriptTag = document.getElementsByTagName("script")[0];
        firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
        window.onYouTubeIframeAPIReady = () => {
            initModalPlayer();
        };
    } else {
        setTimeout(initModalPlayer, 100);
    }
};

// Inicializar player de YouTube en el modal
const initModalPlayer = () => {
    if (
        !selectedTutorial.value ||
        !document.getElementById("modal-youtube-player")
    )
        return;

    modalPreviewState.value = {
        isPlaying: false,
        currentTime: 0,
        isBlocked: false,
        checkInterval: null,
    };

    modalVideoPlayer.value = new window.YT.Player("modal-youtube-player", {
        videoId: selectedTutorial.value.videoId,
        playerVars: {
            autoplay: 1,
            controls: 1,
            modestbranding: 1,
            rel: 0,
            iv_load_policy: 3,
            fs: 1,
            disablekb: 0,
        },
        events: {
            onReady: onModalPlayerReady,
            onStateChange: onModalPlayerStateChange,
        },
    });
};

const onModalPlayerReady = (event) => {
    event.target.playVideo();
};

const onModalPlayerStateChange = (event) => {
    if (event.data === window.YT.PlayerState.PLAYING) {
        modalPreviewState.value.isPlaying = true;
        startModalPreviewTimer();
    } else if (
        event.data === window.YT.PlayerState.PAUSED ||
        event.data === window.YT.PlayerState.ENDED
    ) {
        modalPreviewState.value.isPlaying = false;
    }
};

// Iniciar timer de preview en el modal
const startModalPreviewTimer = () => {
    if (modalPreviewState.value.checkInterval) {
        clearInterval(modalPreviewState.value.checkInterval);
    }

    modalPreviewState.value.checkInterval = setInterval(() => {
        if (modalVideoPlayer.value?.getCurrentTime) {
            modalPreviewState.value.currentTime =
                modalVideoPlayer.value.getCurrentTime();

            if (
                modalPreviewState.value.currentTime >= props.previewDuration &&
                !modalPreviewState.value.isBlocked
            ) {
                blockModalVideo();
            }
        }
    }, 500);
};

// Bloquear video en el modal después del preview
const blockModalVideo = () => {
    if (modalPreviewState.value.checkInterval) {
        clearInterval(modalPreviewState.value.checkInterval);
        modalPreviewState.value.checkInterval = null;
    }

    if (modalVideoPlayer.value?.pauseVideo) {
        modalVideoPlayer.value.pauseVideo();
    }

    modalPreviewState.value.isBlocked = true;
    emit("preview-ended", selectedTutorial.value);

    if (!props.isLoggedIn) {
        showModalRegistrationPrompt();
    }
};

// Mostrar SweetAlert de registro en el modal
const showModalRegistrationPrompt = () => {
    if (!selectedTutorial.value) return;

    Swal.fire({
        title: "🔒 Preview finalizado",
        html: `
            <div class="text-left">
                <p class="mb-3">Has visto el preview de <strong>"${selectedTutorial.value.titulo}"</strong>.</p>
                <p class="text-sm text-gray-600">
                    <i class="fas fa-lock text-cyan-600 mr-1"></i>
                    Regístrate para continuar viendo el contenido completo y acceder a:
                </p>
                <ul class="text-xs text-gray-500 mt-2 space-y-1">
                    <li><i class="fas fa-check text-green-500 mr-1"></i>Video completo sin interrupciones</li>
                    <li><i class="fas fa-check text-green-500 mr-1"></i>Soporte prioritario</li>
                </ul>
            </div>
        `,
        icon: "info",
        showCancelButton: true,
        confirmButtonText: "Regístrate ahora",
        cancelButtonText: "Seguir explorando",
        confirmButtonColor: "#06b6d4",
        cancelButtonColor: "#6b7280",
        reverseButtons: true,
        allowOutsideClick: false,
    }).then((result) => {
        if (result.isConfirmed) {
            router.visit("/register");
        }
    });
};

// Cerrar modal
const closeModal = () => {
    if (modalPreviewState.value.checkInterval) {
        clearInterval(modalPreviewState.value.checkInterval);
        modalPreviewState.value.checkInterval = null;
    }

    if (modalVideoPlayer.value?.destroy) {
        modalVideoPlayer.value.destroy();
        modalVideoPlayer.value = null;
    }

    isModalOpen.value = false;
    selectedTutorial.value = null;
    modalPreviewState.value = {
        isPlaying: false,
        currentTime: 0,
        isBlocked: false,
        checkInterval: null,
    };

    document.body.style.overflow = "";
};

// Toggle play/pause en el modal
const toggleModalVideoPlay = () => {
    if (!modalVideoPlayer.value) return;

    if (modalPreviewState.value.isBlocked && !props.isLoggedIn) {
        showModalRegistrationPrompt();
        return;
    }

    if (modalPreviewState.value.isPlaying) {
        modalVideoPlayer.value.pauseVideo();
    } else {
        modalVideoPlayer.value.playVideo();
    }
};

// ============================================
// FUNCIONES DE INTERACCIÓN GENERALES
// ============================================

// Manejar clic en tarjeta (ABRE MODAL DE VIDEO)
const handleCardClick = (tutorial) => {
    openVideoModal(tutorial);
    emit("card-click", tutorial);
};

// Compartir en redes sociales
const shareTutorial = (tutorial, event) => {
    event?.stopPropagation();
    const shareUrl = encodeURIComponent(window.location.origin + tutorial.link);
    const shareText = encodeURIComponent(
        `Mira este tutorial de Bitrix24: ${tutorial.titulo}`,
    );
    const twitterUrl = `https://twitter.com/intent/tweet?text=${shareText}&url=${shareUrl}`;
    window.open(twitterUrl, "_blank", "noopener,noreferrer");
    emit("share", tutorial);
};

// Toggle favorito con persistencia
const toggleFavorite = (tutorial, event) => {
    event?.stopPropagation();
    const index = favorites.value.findIndex((fav) => fav.id === tutorial.id);
    if (index === -1) {
        favorites.value.push(tutorial);
    } else {
        favorites.value.splice(index, 1);
    }
    localStorage.setItem("favorites", JSON.stringify(favorites.value));
    emit("toggle-favorite", tutorial);
};

// Verificar si es favorito
const isFavorite = (tutorialId) => {
    return favorites.value.some((fav) => fav.id === tutorialId);
};

// ============================================
// CLEANUP Y EVENTOS GLOBALES
// ============================================

const handleKeydown = (e) => {
    if (e.key === "Escape" && isModalOpen.value) {
        closeModal();
    }
};

onMounted(() => {
    document.addEventListener("keydown", handleKeydown);

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add(
                        "animate__animated",
                        "animate__fadeInUp",
                    );
                }
            });
        },
        { threshold: 0.1 },
    );
    document.querySelectorAll(".observe").forEach((el) => observer.observe(el));
});

onBeforeUnmount(() => {
    document.removeEventListener("keydown", handleKeydown);
    if (isModalOpen.value) {
        closeModal();
    }
});

watch(
    () => props.isLoggedIn,
    (newVal) => {
        if (newVal && modalPreviewState.value.isBlocked) {
            modalPreviewState.value.isBlocked = false;
            modalVideoPlayer.value?.playVideo();
        }
    },
);
</script>

<template>
    <!-- ============================================ -->
    <!-- 💎 CAPACITACIONES DESTACADAS - CON MODAL DE VIDEO -->
    <!-- ============================================ -->
    <div
        class="bg-gradient-to-r from-gray-50 via-white to-gray-100 rounded-3xl p-6 sm:p-8 lg:p-10 shadow-xl border border-gray-200 relative overflow-hidden"
    >
        <div class="max-w-7xl mx-auto">
            <!-- Header de la sección -->
            <div class="text-center mb-8 sm:mb-10">
                <h2
                    class="text-3xl sm:text-4xl font-extrabold text-gray-900 mb-3"
                >
                    Capacitaciones Destacadas
                </h2>
                <div
                    class="w-20 h-1.5 bg-gradient-to-r from-cyan-500 via-purple-500 to-cyan-500 mx-auto rounded-full"
                ></div>
            </div>

            <!-- Controles: Búsqueda y Filtros -->
            <div
                class="max-w-5xl mx-auto mb-8 flex flex-col sm:flex-row justify-center gap-4 items-center"
            >
                <!-- Búsqueda con icono -->
                <div class="relative w-full sm:w-80">
                    <i
                        class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"
                    ></i>
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Buscar capacitaciones..."
                        class="w-full pl-10 pr-4 py-2.5 rounded-full border border-gray-200 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 text-sm shadow-sm transition-all bg-gray-50/50 hover:bg-white"
                        aria-label="Buscar capacitaciones"
                    />
                </div>

                <!-- Filtros de Categoría -->
                <div class="flex flex-wrap gap-2 justify-center">
                    <button
                        v-for="category in ['Todos', 'CRM', 'Automatización']"
                        :key="category"
                        @click="selectedCategory = category"
                        :class="[
                            'px-4 py-2 rounded-full font-medium text-sm transition-all duration-200 border',
                            selectedCategory === category
                                ? 'bg-gradient-to-r from-cyan-600 to-cyan-700 text-white border-cyan-700 shadow-md'
                                : 'bg-white text-gray-600 border-gray-200 hover:border-cyan-300 hover:bg-cyan-50 hover:text-cyan-700',
                        ]"
                        :aria-pressed="selectedCategory === category"
                    >
                        {{ category }}
                    </button>
                </div>
            </div>

            <!-- Carrusel Swiper con CLASES ÚNICAS -->
            <Swiper
                :modules="[Navigation, Pagination, Autoplay]"
                :spaceBetween="20"
                :slidesPerView="1"
                :breakpoints="{
                    640: { slidesPerView: 2, spaceBetween: 20 },
                    1024: { slidesPerView: 3, spaceBetween: 24 },
                }"
                :navigation="{
                    nextEl: '.cursos-swiper-next',
                    prevEl: '.cursos-swiper-prev',
                }"
                :pagination="{
                    clickable: true,
                    el: '.cursos-swiper-pagination',
                    bulletClass: 'cursos-swiper-bullet',
                    bulletActiveClass: 'cursos-swiper-bullet-active',
                }"
                :autoplay="{
                    delay: 5000,
                    disableOnInteraction: false,
                    pauseOnMouseEnter: true,
                }"
                :loop="filteredTutoriales.length > 3"
                class="w-full pb-4"
            >
                <SwiperSlide
                    v-for="(tutorial, index) in filteredTutoriales"
                    :key="tutorial.id || index"
                >
                    <!-- Tarjeta Premium - Click abre modal de video -->
                    <article
                        @click="handleCardClick(tutorial)"
                        class="group relative bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden hover:shadow-2xl hover:border-cyan-300 transition-all duration-300 h-full flex flex-col cursor-pointer"
                        :class="{
                            'ring-2 ring-cyan-400 ring-offset-2':
                                tutorial.esPopular,
                        }"
                    >
                        <!-- Imagen con overlay de play -->
                        <div class="relative h-40 sm:h-44 overflow-hidden">
                            <img
                                :src="tutorial.imagen"
                                :alt="`Imagen de ${tutorial.titulo}`"
                                class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                                loading="lazy"
                            />

                            <!-- Overlay oscuro al hover -->
                            <div
                                class="absolute inset-0 bg-gradient-to-t from-black/50 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                            ></div>

                            <!-- Botón de play centrado -->
                            <div
                                class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                            >
                                <div
                                    class="w-14 h-14 sm:w-16 sm:h-16 bg-white/90 rounded-full flex items-center justify-center shadow-2xl hover:scale-110 transition-transform"
                                >
                                    <i
                                        class="fas fa-play text-cyan-600 ml-1 text-lg sm:text-xl"
                                    ></i>
                                </div>
                            </div>

                            <!-- Badge de preview -->
                            <div
                                class="absolute top-3 left-3 bg-black/70 backdrop-blur-sm text-white text-xs px-2.5 py-1 rounded-full flex items-center gap-1.5 z-10"
                            >
                                <i class="fas fa-eye text-cyan-400"></i>
                                <span>Preview: {{ previewDuration }}s</span>
                            </div>

                            <!-- Badges de tutorial -->
                            <div
                                v-if="tutorial.esNuevo"
                                class="absolute top-3 left-3 sm:left-auto sm:right-14 bg-gradient-to-r from-cyan-500 to-cyan-600 text-white text-xs font-semibold px-3 py-1 rounded-full shadow-lg flex items-center gap-1 z-10"
                            >
                                <i class="fas fa-sparkles text-[10px]"></i>
                                <span>Nuevo</span>
                            </div>
                            <div
                                v-if="tutorial.esPopular"
                                class="absolute top-3 right-3 bg-white/95 backdrop-blur-sm text-yellow-500 text-xs px-2.5 py-1 rounded-full shadow-md flex items-center gap-1 z-10"
                            >
                                <i class="fas fa-star"></i>
                                <span
                                    class="text-gray-700 font-medium hidden sm:inline"
                                    >Popular</span
                                >
                            </div>
                        </div>

                        <!-- Contenido de la tarjeta -->
                        <div class="flex-grow p-4 sm:p-5">
                            <!-- Categoría con badge -->
                            <span
                                class="inline-flex items-center px-2.5 py-1 bg-gradient-to-r from-cyan-50 to-blue-50 text-cyan-700 text-xs font-semibold rounded-full mb-3 border border-cyan-100"
                            >
                                <i class="fas fa-tag mr-1.5 text-[10px]"></i>
                                {{ tutorial.categoria }}
                            </span>

                            <!-- Título -->
                            <h3
                                class="text-base sm:text-lg font-bold text-gray-900 mb-2 line-clamp-2 group-hover:text-cyan-700 transition-colors leading-tight"
                            >
                                {{ tutorial.titulo }}
                            </h3>

                            <!-- Descripción -->
                            <p
                                class="text-sm text-gray-600 leading-relaxed line-clamp-2 mb-4"
                            >
                                {{ tutorial.descripcion }}
                            </p>

                            <!-- Meta info -->
                            <div
                                class="flex items-center gap-4 text-xs text-gray-500 mb-4"
                            >
                                <span class="flex items-center gap-1.5">
                                    <i class="fas fa-clock text-cyan-500"></i>
                                    {{ tutorial.duracion }}
                                </span>
                            </div>

                            <!-- Barra de progreso del curso -->
                            <div
                                v-if="tutorial.progreso !== undefined"
                                class="mb-4"
                            >
                                <div
                                    class="flex justify-between text-xs text-gray-500 mb-1.5"
                                >
                                    <span class="font-medium">Progreso</span>
                                    <span class="font-bold text-cyan-600"
                                        >{{ tutorial.progreso }}%</span
                                    >
                                </div>
                                <div
                                    class="h-2 bg-gray-100 rounded-full overflow-hidden"
                                >
                                    <div
                                        class="h-full bg-gradient-to-r from-cyan-500 via-cyan-400 to-cyan-600 rounded-full transition-all duration-700 ease-out"
                                        :style="{
                                            width: `${tutorial.progreso}%`,
                                        }"
                                    ></div>
                                </div>
                            </div>
                        </div>

                        <!-- Footer con acciones -->
                        <div
                            class="px-4 sm:px-5 pb-4 pt-0 border-t border-gray-100 bg-gradient-to-r from-gray-50 to-white"
                            @click.stop
                        >
                            <div class="flex gap-2.5">
                                <button
                                    @click.stop="
                                        shareTutorial(tutorial, $event)
                                    "
                                    class="flex-1 inline-flex items-center justify-center gap-2 bg-gradient-to-r from-cyan-600 to-cyan-700 text-white px-4 py-2.5 rounded-full font-semibold text-sm shadow-md hover:shadow-lg hover:from-cyan-700 hover:to-cyan-800 transform hover:-translate-y-0.5 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:ring-offset-2"
                                    aria-label="Compartir capacitación"
                                >
                                    <i class="fas fa-share-alt text-xs"></i>
                                    <span class="hidden sm:inline"
                                        >Compartir</span
                                    >
                                </button>
                                <button
                                    @click.stop="
                                        toggleFavorite(tutorial, $event)
                                    "
                                    class="w-10 h-10 flex items-center justify-center rounded-full border border-gray-200 bg-white text-gray-400 hover:text-red-500 hover:border-red-300 hover:bg-red-50 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 group/btn"
                                    :aria-label="
                                        isFavorite(tutorial.id)
                                            ? 'Quitar de favoritos'
                                            : 'Agregar a favoritos'
                                    "
                                    :aria-pressed="isFavorite(tutorial.id)"
                                >
                                    <i
                                        :class="
                                            isFavorite(tutorial.id)
                                                ? 'fas fa-heart text-red-500 animate-pulse'
                                                : 'far fa-heart group-hover/btn:animate-pulse'
                                        "
                                        class="transition-all"
                                    ></i>
                                </button>
                            </div>
                        </div>
                    </article>
                </SwiperSlide>

                <!-- Navegación personalizada con CLASES ÚNICAS -->
                <template #container-end>
                    <div class="flex items-center justify-center gap-4 mt-6">
                        <button
                            class="cursos-swiper-prev w-10 h-10 rounded-full bg-white border border-gray-200 text-gray-600 hover:bg-cyan-50 hover:text-cyan-600 hover:border-cyan-300 shadow-md hover:shadow-lg transition-all duration-200 flex items-center justify-center focus:outline-none focus:ring-2 focus:ring-cyan-500"
                            aria-label="Capacitación anterior"
                        >
                            <i class="fas fa-chevron-left text-sm"></i>
                        </button>

                        <div class="cursos-swiper-pagination"></div>

                        <button
                            class="cursos-swiper-next w-10 h-10 rounded-full bg-white border border-gray-200 text-gray-600 hover:bg-cyan-50 hover:text-cyan-600 hover:border-cyan-300 shadow-md hover:shadow-lg transition-all duration-200 flex items-center justify-center focus:outline-none focus:ring-2 focus:ring-cyan-500"
                            aria-label="Siguiente capacitación"
                        >
                            <i class="fas fa-chevron-right text-sm"></i>
                        </button>
                    </div>
                </template>
            </Swiper>

            <!-- Estado vacío -->
            <div
                v-if="filteredTutoriales.length === 0"
                class="text-center py-12"
            >
                <div
                    class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gradient-to-br from-cyan-100 to-blue-100 mb-4"
                >
                    <i class="fas fa-search text-cyan-600 text-2xl"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">
                    Sin resultados
                </h3>
                <p class="text-sm text-gray-600 mb-4 max-w-sm mx-auto">
                    No encontramos capacitaciones con esos criterios. Intenta
                    con otra búsqueda.
                </p>
                <button
                    @click="
                        searchQuery = '';
                        selectedCategory = 'Todos';
                    "
                    class="inline-flex items-center gap-2 text-sm font-semibold text-cyan-600 hover:text-cyan-700 bg-cyan-50 hover:bg-cyan-100 px-4 py-2 rounded-full transition-colors"
                >
                    <i class="fas fa-rotate-left"></i>
                    Limpiar filtros
                </button>
            </div>
        </div>
    </div>

    <!-- ============================================ -->
    <!-- 🎬 MODAL DE PREVIEW DE VIDEO YOUTUBE -->
    <!-- ============================================ -->
    <Teleport to="body">
        <Transition name="modal">
            <div
                v-if="isModalOpen && selectedTutorial"
                class="fixed inset-0 z-50 flex items-center justify-center p-4"
                @click.self="closeModal"
                role="dialog"
                aria-modal="true"
                :aria-labelledby="'modal-title-' + selectedTutorial.id"
            >
                <!-- Overlay oscuro -->
                <div
                    class="absolute inset-0 bg-black/80 backdrop-blur-sm"
                    @click="closeModal"
                ></div>

                <!-- Contenido del modal -->
                <div
                    class="relative bg-white rounded-2xl shadow-2xl max-w-4xl w-full overflow-hidden animate-modal-enter"
                >
                    <!-- Header del modal -->
                    <div
                        class="flex items-center justify-between px-4 sm:px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-cyan-50 to-blue-50"
                    >
                        <div class="flex items-center gap-3 min-w-0">
                            <span
                                class="inline-flex items-center px-2.5 py-1 bg-cyan-100 text-cyan-700 text-xs font-semibold rounded-full flex-shrink-0"
                            >
                                {{ selectedTutorial.categoria }}
                            </span>
                            <h3
                                :id="'modal-title-' + selectedTutorial.id"
                                class="text-base sm:text-lg font-bold text-gray-900 truncate"
                            >
                                {{ selectedTutorial.titulo }}
                            </h3>
                        </div>
                        <button
                            @click="closeModal"
                            class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 hover:bg-gray-200 text-gray-500 hover:text-gray-700 transition-colors focus:outline-none focus:ring-2 focus:ring-cyan-500"
                            aria-label="Cerrar modal"
                        >
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <!-- Contenedor del video -->
                    <div class="relative bg-black aspect-video">
                        <!-- Iframe de YouTube -->
                        <div
                            id="modal-youtube-player"
                            class="w-full h-full"
                        ></div>

                        <!-- Overlay de bloqueo (después del preview) -->
                        <div
                            v-if="modalPreviewState.isBlocked && !isLoggedIn"
                            class="absolute inset-0 bg-black/90 backdrop-blur-sm flex flex-col items-center justify-center text-white z-20 animate-fade-in"
                        >
                            <div class="text-center p-6 max-w-sm">
                                <div
                                    class="w-16 h-16 bg-gradient-to-br from-cyan-500 to-blue-600 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg"
                                >
                                    <i class="fas fa-lock text-2xl"></i>
                                </div>
                                <h4 class="text-lg font-bold mb-2">
                                    Preview finalizado
                                </h4>
                                <p class="text-sm text-gray-300 mb-5">
                                    Has visto los primeros
                                    {{ previewDuration }} segundos. Regístrate
                                    para ver el contenido completo.
                                </p>
                                <div
                                    class="flex flex-col sm:flex-row gap-3 justify-center"
                                >
                                    <button
                                        @click="router.visit('/register')"
                                        class="bg-gradient-to-r from-cyan-500 to-blue-600 text-white px-6 py-2.5 rounded-full font-semibold text-sm hover:from-cyan-600 hover:to-blue-700 transition-all shadow-lg"
                                    >
                                        <i class="fas fa-user-plus mr-2"></i>
                                        Regístrate gratis
                                    </button>
                                    <button
                                        @click="closeModal"
                                        class="bg-gray-700 text-white px-6 py-2.5 rounded-full font-semibold text-sm hover:bg-gray-600 transition-all"
                                    >
                                        Cerrar
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Controles del video (solo si no está bloqueado o está logueado) -->
                        <div
                            v-if="!modalPreviewState.isBlocked || isLoggedIn"
                            class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/80 to-transparent p-4"
                        >
                            <!-- Barra de progreso del preview -->
                            <div class="flex items-center gap-3 mb-2">
                                <div
                                    class="flex-1 h-1 bg-gray-600 rounded-full overflow-hidden"
                                >
                                    <div
                                        class="h-full bg-gradient-to-r from-cyan-500 to-blue-500 transition-all duration-300"
                                        :style="{
                                            width: `${Math.min(
                                                (modalPreviewState.currentTime /
                                                    previewDuration) *
                                                    100,
                                                100,
                                            )}%`,
                                        }"
                                    ></div>
                                    <!-- Marcador de límite de preview -->
                                    <div
                                        class="absolute top-0 w-px h-full bg-red-400"
                                        style="left: 100%"
                                        title="Límite de preview"
                                    ></div>
                                </div>
                                <span
                                    class="text-xs text-gray-300 font-mono whitespace-nowrap"
                                >
                                    {{
                                        Math.floor(
                                            modalPreviewState.currentTime,
                                        )
                                    }}s / {{ previewDuration }}s
                                </span>
                            </div>

                            <!-- Badge de preview -->
                            <div class="flex items-center justify-center gap-2">
                                <span
                                    class="text-xs text-cyan-400 font-medium flex items-center gap-1"
                                >
                                    <i class="fas fa-eye"></i>
                                    Preview gratuito
                                </span>
                                <span
                                    v-if="!isLoggedIn"
                                    class="text-xs text-gray-400"
                                >
                                    • Regístrate para ver completo
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Footer del modal -->
                    <div
                        class="px-4 sm:px-6 py-4 bg-gray-50 border-t border-gray-200"
                    >
                        <div
                            class="flex flex-col sm:flex-row sm:items-center justify-between gap-3"
                        >
                            <p class="text-sm text-gray-600">
                                <i class="fas fa-clock text-cyan-500 mr-1"></i>
                                Duración total:
                                <strong>{{ selectedTutorial.duracion }}</strong>
                            </p>
                            <div class="flex items-center gap-2">
                                <button
                                    @click="unlockTutorial(selectedTutorial)"
                                    class="inline-flex items-center gap-1.5 text-sm text-gray-600 hover:text-cyan-600 transition-colors"
                                >
                                    <i class="fas fa-lock"></i>
                                    Desbloquear
                                </button>
                                <button
                                    v-if="isLoggedIn"
                                    @click="router.visit(selectedTutorial.link)"
                                    class="inline-flex items-center gap-1.5 bg-gradient-to-r from-cyan-600 to-cyan-700 text-white px-4 py-2 rounded-full font-medium text-sm hover:from-cyan-700 hover:to-cyan-800 transition-all shadow"
                                >
                                    <i class="fas fa-external-link-alt"></i>
                                    Ver contenido completo
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<style scoped>
/* ========================================
   CURSOS SWIPER - ESTILOS ÚNICOS (NO AFECTAN OTROS COMPONENTES)
   ======================================== */

/* Bullet normal - SOLO para Cursos */
:deep(.cursos-swiper-bullet) {
    width: 8px !important;
    height: 8px !important;
    background: #d1d5db !important;
    opacity: 1 !important;
    margin: 0 3px !important;
    transition: all 0.2s ease !important;
    border-radius: 4px !important;
    cursor: pointer;
}

/* Bullet activo - SOLO para Cursos */
:deep(.cursos-swiper-bullet-active) {
    width: 24px !important;
    background: linear-gradient(to right, #0891b2, #0e7490) !important;
    border-radius: 4px !important;
}

/* Hover en bullets - SOLO para Cursos */
:deep(.cursos-swiper-bullet:hover) {
    background: #9ca3af !important;
    transform: scale(1.1);
}

/* Botones de navegación - SOLO para Cursos */
:deep(.cursos-swiper-prev),
:deep(.cursos-swiper-next) {
    /* Estilos ya aplicados vía Tailwind en el template */
}

/* Ocultar flechas por defecto de Swiper */
:deep(.swiper-button-next),
:deep(.swiper-button-prev) {
    display: none !important;
}

/* ========================================
   MODAL ANIMATIONS
   ======================================== */

/* Animación de entrada del modal */
.animate-modal-enter {
    animation: modalEnter 0.3s ease-out forwards;
}

@keyframes modalEnter {
    from {
        opacity: 0;
        transform: scale(0.95) translateY(10px);
    }
    to {
        opacity: 1;
        transform: scale(1) translateY(0);
    }
}

/* Animación de fade-in para overlay de bloqueo */
@keyframes fade-in {
    from {
        opacity: 0;
        transform: scale(0.98);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}
.animate-fade-in {
    animation: fade-in 0.25s ease-out forwards;
}

/* Transiciones de Vue para el modal */
.modal-enter-active,
.modal-leave-active {
    transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
    opacity: 0;
}

.modal-enter-active > div:last-child,
.modal-leave-active > div:last-child {
    transition:
        transform 0.3s ease,
        opacity 0.3s ease;
}

.modal-enter-from > div:last-child,
.modal-leave-to > div:last-child {
    transform: scale(0.95) translateY(10px);
    opacity: 0;
}

/* ========================================
   ANIMACIONES GENERALES
   ======================================== */

@media (prefers-reduced-motion: no-preference) {
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-fade-in-up {
        animation: fadeInUp 0.6s ease-out forwards;
    }
}

/* Asegurar que el iframe de YouTube llene el contenedor */
:deep(#modal-youtube-player) iframe {
    width: 100%;
    height: 100%;
    border: none;
}

/* Cursor pointer en tarjetas */
article.cursor-pointer:hover {
    cursor: pointer;
}

/* Prevenir scroll cuando el modal está abierto */
:global(body.modal-open) {
    overflow: hidden;
}
</style>
