<script setup>
import { ref, onMounted, watch, onUnmounted, computed } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import axios from "axios";
import AppLayout from "@/Layouts/AppLayout.vue";

// ─────────────────────────────────────────────────────────────
// 📋 PROPS
// ─────────────────────────────────────────────────────────────
const props = defineProps({
    id: { type: [String, Number], required: false },
    recurso: { type: Object, required: false },
});

// ─────────────────────────────────────────────────────────────
// 📊 ESTADO REACTIVO
// ─────────────────────────────────────────────────────────────
const resource = ref(null);
const loading = ref(true);
const error = ref(null);
const videoLoaded = ref(false);
const isFullscreen = ref(false);
const playerContainer = ref(null);

const page = usePage();
const pageProps = page.props;

// ─────────────────────────────────────────────────────────────
// 🎬 FUNCIONES PARA VIDEO EMBEBIDO
// ─────────────────────────────────────────────────────────────
const extractYouTubeId = (url) => {
    if (!url) return null;
    const patterns = [
        /youtu\.be\/([a-zA-Z0-9_-]{11})/,
        /[?&]v=([a-zA-Z0-9_-]{11})/,
        /embed\/([a-zA-Z0-9_-]{11})/,
        /shorts\/([a-zA-Z0-9_-]{11})/,
    ];
    for (const pattern of patterns) {
        const match = url.match(pattern);
        if (match && match[1]) return match[1];
    }
    return null;
};

const getYouTubeEmbedUrl = (url, autoplay = true) => {
    const videoId = extractYouTubeId(url);
    if (!videoId) return null;
    const params = new URLSearchParams({
        modestbranding: "1",
        rel: "0",
        controls: "1",
        playsinline: "1",
        showinfo: "0",
        disablekb: "0",
        iv_load_policy: "3",
        cc_load_policy: "0",
    });
    if (autoplay) params.append("autoplay", "1");
    return `https://www.youtube.com/embed/${videoId}?${params.toString()}`;
};

const getVimeoEmbedUrl = (url, autoplay = true) => {
    const match = url.match(/vimeo\.com\/(\d+)/);
    if (match && match[1]) {
        let embedUrl = `https://player.vimeo.com/video/${match[1]}?title=0&byline=0&portrait=0`;
        if (autoplay) embedUrl += "&autoplay=1";
        embedUrl += "&dnt=1";
        return embedUrl;
    }
    return url;
};

const getVideoThumbnail = (url) => {
    const videoId = extractYouTubeId(url);
    if (videoId) return `https://img.youtube.com/vi/${videoId}/hqdefault.jpg`;
    return null;
};

// ─────────────────────────────────────────────────────────────
// 📊 COMPUTED PROPERTIES
// ─────────────────────────────────────────────────────────────
const hasValidVideo = computed(() => {
    return resource.value?.url && extractYouTubeId(resource.value.url) !== null;
});

const videoEmbedUrl = computed(() => {
    return resource.value?.url
        ? getYouTubeEmbedUrl(resource.value.url, true)
        : null;
});

const videoThumbnail = computed(() => {
    return resource.value?.url ? getVideoThumbnail(resource.value.url) : null;
});

// ─────────────────────────────────────────────────────────────
// 🖥️ FULLSCREEN API
// ─────────────────────────────────────────────────────────────
const toggleFullscreen = async () => {
    if (!playerContainer.value) return;
    try {
        if (!isFullscreen.value) {
            if (playerContainer.value.requestFullscreen) {
                await playerContainer.value.requestFullscreen();
            } else if (playerContainer.value.webkitRequestFullscreen) {
                await playerContainer.value.webkitRequestFullscreen();
            }
        } else {
            if (document.exitFullscreen) {
                await document.exitFullscreen();
            } else if (document.webkitExitFullscreen) {
                await document.webkitExitFullscreen();
            }
        }
    } catch (err) {
        console.error("Error toggling fullscreen:", err);
    }
};

const handleFullscreenChange = () => {
    isFullscreen.value = !!(
        document.fullscreenElement || document.webkitFullscreenElement
    );
};

// ─────────────────────────────────────────────────────────────
// 🚫 BLOQUEO DE INTERACCIONES EN CAPAS PROTECTORAS
// ─────────────────────────────────────────────────────────────
const preventInteraction = (e) => {
    e.preventDefault();
    e.stopPropagation();
    return false;
};

// ─────────────────────────────────────────────────────────────
// 🔄 CARGA DE DATOS
// ─────────────────────────────────────────────────────────────
const loadResource = async () => {
    if (props.recurso) {
        resource.value = props.recurso;
        loading.value = false;
        return;
    }

    const resourceId = props.id || pageProps.id;

    if (!resourceId) {
        error.value = "No se especificó el recurso a cargar";
        loading.value = false;
        return;
    }

    loading.value = true;
    error.value = null;
    videoLoaded.value = false;

    try {
        const { data } = await axios.get(`/api/recursos/${resourceId}`);
        resource.value = data;
    } catch (err) {
        console.error("Error al cargar recurso:", err);
        if (err.response?.status === 404) {
            error.value = "El recurso no existe o ha sido eliminado";
        } else if (err.response?.status === 403) {
            error.value = "No tienes permisos para ver este contenido";
        } else {
            error.value = "No se pudo cargar el recurso solicitado";
        }
    } finally {
        loading.value = false;
    }
};

// ─────────────────────────────────────────────────────────────
// 🔙 VOLVER AL DASHBOARD (Ruta nombrada 'dashboard')
// ─────────────────────────────────────────────────────────────
const handleBack = () => {
    // ✅ Redirección directa usando ruta nombrada de Laravel
    // El middleware resolverá automáticamente Dashboard o Usuarios según el rol
    router.visit(route("dashboard"));
};

// ─────────────────────────────────────────────────────────────
// 🎨 ESTILOS DE PANTALLA COMPLETA PARA OCULTAR CONTROLES YOUTUBE
// ─────────────────────────────────────────────────────────────
const injectFullscreenStyles = () => {
    if (
        typeof document !== "undefined" &&
        !document.getElementById("fullscreen-block-styles")
    ) {
        const style = document.createElement("style");
        style.id = "fullscreen-block-styles";
        style.textContent = `
            :-webkit-full-screen .ytp-chrome-top,
            :-webkit-full-screen .ytp-chrome-bottom,
            :fullscreen .ytp-chrome-top,
            :fullscreen .ytp-chrome-bottom {
                display: none !important;
                opacity: 0 !important;
                visibility: hidden !important;
                pointer-events: none !important;
            }
        `;
        document.head.appendChild(style);
    }
};

// ─────────────────────────────────────────────────────────────
// LIFECYCLE
// ─────────────────────────────────────────────────────────────
onMounted(() => {
    loadResource();
    injectFullscreenStyles();
    document.addEventListener("fullscreenchange", handleFullscreenChange);
    document.addEventListener("webkitfullscreenchange", handleFullscreenChange);
});

onUnmounted(() => {
    document.removeEventListener("fullscreenchange", handleFullscreenChange);
    document.removeEventListener(
        "webkitfullscreenchange",
        handleFullscreenChange,
    );
});

watch(resource, (newVal) => {
    if (newVal && newVal.url) {
        setTimeout(() => {
            if (videoEmbedUrl.value) {
                videoLoaded.value = true;
            }
        }, 500);
    }
});
</script>

<template>
    <AppLayout :title="resource?.titulo || 'Reproduciendo...'">
        <main
            class="min-h-screen bg-gradient-to-b from-slate-50 via-white to-slate-100 dark:from-gray-900 dark:via-gray-900 dark:to-gray-800"
        >
            <div class="max-w-screen-xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
                <!-- 🔙 Botón VOLVER AL DASHBOARD (único elemento de navegación) -->
                <div class="mb-6">
                    <button
                        @click="handleBack"
                        class="group inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 font-medium hover:bg-gray-50 dark:hover:bg-gray-700 hover:shadow-md transition-all duration-200"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-4 w-4 transition-transform group-hover:-translate-x-0.5"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z"
                                clip-rule="evenodd"
                            />
                        </svg>
                        <span>Volver al Dashboard</span>
                    </button>
                </div>

                <!-- Skeleton Loading -->
                <div v-if="loading" class="space-y-6">
                    <div
                        class="relative aspect-video w-full bg-slate-200 dark:bg-gray-700 rounded-2xl overflow-hidden"
                    >
                        <div
                            class="absolute inset-0 -translate-x-full bg-gradient-to-r from-transparent via-white/40 to-transparent animate-shimmer"
                        ></div>
                    </div>
                </div>

                <!-- Error State -->
                <div
                    v-else-if="error"
                    class="bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-800 rounded-2xl p-6 text-center"
                >
                    <p class="text-red-700 dark:text-red-300 font-medium">
                        {{ error }}
                    </p>
                    <button
                        @click="loadResource"
                        class="mt-3 text-sm text-red-800 dark:text-red-200 hover:underline"
                    >
                        Reintentar
                    </button>
                </div>

                <!-- 🎬 VIDEO PLAYER CON BLOQUEOS SUPERIOR/INFERIOR -->
                <section
                    v-else-if="resource?.url"
                    ref="playerContainer"
                    class="relative group/video player-container"
                >
                    <div
                        class="relative aspect-video w-full overflow-hidden rounded-2xl bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 shadow-2xl"
                    >
                        <!-- Thumbnail mientras carga -->
                        <div
                            v-if="!videoLoaded"
                            class="absolute inset-0 flex flex-col items-center justify-center bg-slate-900/80 backdrop-blur-sm z-10"
                        >
                            <div
                                v-if="videoThumbnail"
                                class="absolute inset-0 bg-cover bg-center opacity-30"
                                :style="{
                                    backgroundImage: `url(${videoThumbnail})`,
                                }"
                            >
                                <div
                                    class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/50 to-transparent"
                                ></div>
                            </div>
                            <div
                                class="relative flex items-center justify-center w-20 h-20 rounded-full bg-white/10 backdrop-blur-md border border-white/20"
                            >
                                <svg
                                    class="w-8 h-8 text-white/80 ml-1"
                                    fill="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path d="M8 5v14l11-7z" />
                                </svg>
                            </div>
                            <p
                                class="mt-5 text-sm text-slate-400 font-medium animate-pulse"
                            >
                                Cargando video...
                            </p>
                        </div>

                        <!-- Iframe de YouTube con capas protectoras ORIGINAL -->
                        <div
                            v-if="hasValidVideo && videoEmbedUrl"
                            class="relative w-full h-full overflow-hidden"
                        >
                            <!-- VIDEO IFRAME -->
                            <iframe
                                :src="videoEmbedUrl"
                                :title="resource.titulo"
                                class="absolute inset-0 w-full h-full transition-opacity duration-500"
                                :class="
                                    videoLoaded ? 'opacity-100' : 'opacity-0'
                                "
                                frameborder="0"
                                allow="
                                    accelerometer;
                                    autoplay;
                                    clipboard-write;
                                    encrypted-media;
                                    gyroscope;
                                    picture-in-picture;
                                    web-share;
                                "
                                referrerpolicy="strict-origin-when-cross-origin"
                                loading="lazy"
                                @load="videoLoaded = true"
                            ></iframe>

                            <!-- 🔒 CAPA SUPERIOR DE BLOQUEO (DISEÑO ORIGINAL) -->
                            <div
                                class="absolute top-0 left-0 right-0 z-30 h-[clamp(60px,12%,120px)] bg-gradient-to-b from-black/80 via-black/40 to-transparent pointer-events-auto"
                                @click.prevent="preventInteraction"
                                @touchstart.prevent="preventInteraction"
                                @contextmenu.prevent="preventInteraction"
                            ></div>

                            <!-- 🔒 CAPA INFERIOR DE BLOQUEO (DISEÑO ORIGINAL) -->
                            <div
                                class="absolute bottom-0 left-0 right-0 z-30 h-[clamp(80px,14%,140px)] bg-gradient-to-t from-black/90 via-black/50 to-transparent pointer-events-auto"
                                @click.prevent="preventInteraction"
                                @touchstart.prevent="preventInteraction"
                                @contextmenu.prevent="preventInteraction"
                            ></div>
                        </div>

                        <!-- Video no disponible -->
                        <div
                            v-else-if="!hasValidVideo"
                            class="absolute inset-0 flex flex-col items-center justify-center bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 text-center p-6"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-20 w-20 text-amber-400/80 mb-4"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="1"
                                    d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"
                                />
                            </svg>
                            <h3 class="text-xl font-semibold text-white mb-2">
                                Video no disponible
                            </h3>
                            <p class="text-slate-400 text-sm max-w-xs">
                                La URL del video no es compatible o el contenido
                                ha sido restringido.
                            </p>
                        </div>
                    </div>

                    <!-- Botón Fullscreen (posicionado fuera de las capas de bloqueo) -->
                    <button
                        @click="toggleFullscreen"
                        class="absolute z-40 right-3 bottom-[95px] md:bottom-[85px] flex items-center justify-center w-11 h-11 md:w-10 md:h-10 rounded-lg bg-black/50 hover:bg-black/70 text-white/90 hover:text-white backdrop-blur-md transition-all duration-200"
                        :title="
                            isFullscreen
                                ? 'Salir de pantalla completa'
                                : 'Pantalla completa'
                        "
                    >
                        <svg
                            v-if="!isFullscreen"
                            class="w-5 h-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"
                            />
                        </svg>
                        <svg
                            v-else
                            class="w-5 h-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 9V4.5M9 9H4.5M9 9L3.5 3.5M15 9V4.5M15 9h4.5M15 9l5.5-5.5M9 15v4.5M9 15H4.5M9 15l-5.5 5.5M15 15v4.5M15 15h4.5M15 15l5.5 5.5"
                            />
                        </svg>
                    </button>
                </section>

                <!-- Fallback si no hay URL de video -->
                <div
                    v-else-if="!loading && !error && !resource?.url"
                    class="text-center py-12 text-gray-500 dark:text-gray-400"
                >
                    Este recurso no contiene un video para reproducir
                </div>
            </div>
        </main>
    </AppLayout>
</template>

<style scoped>
/* Animación shimmer para loading */
@keyframes shimmer {
    100% {
        transform: translateX(100%);
    }
}
.animate-shimmer {
    animation: shimmer 1.5s infinite;
}

/* Aspect ratio 16:9 para video */
.aspect-video {
    aspect-ratio: 16 / 9;
}

/* Prevención de selección de texto en el player */
.player-container {
    user-select: none;
    -webkit-user-select: none;
    touch-action: none;
}

/* Estilos para pantalla completa del contenedor */
.player-container:fullscreen,
.player-container:-webkit-full-screen {
    width: 100vw;
    height: 100vh;
    max-width: none;
    max-height: none;
    margin: 0;
    padding: 0;
    background: #000;
    z-index: 9999;
    border-radius: 0 !important;
}

/* Scrollbar sutil */
::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}
::-webkit-scrollbar-track {
    background: transparent;
}
::-webkit-scrollbar-thumb {
    background: rgb(148 163 184 / 0.5);
    border-radius: 4px;
}
::-webkit-scrollbar-thumb:hover {
    background: rgb(148 163 184 / 0.8);
}
</style>
