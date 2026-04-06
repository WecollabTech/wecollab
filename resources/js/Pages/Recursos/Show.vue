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
const contentLoaded = ref(false);
const isFullscreen = ref(false);
const playerContainer = ref(null);

const page = usePage();
const pageProps = page.props;

// ─────────────────────────────────────────────────────────────
// 🎯 DETECCIÓN DE TIPO DE CONTENIDO
// ─────────────────────────────────────────────────────────────
const getContentType = (url) => {
    if (!url) return null;
    const lower = url.toLowerCase();

    if (lower.includes("youtube.com") || lower.includes("youtu.be"))
        return "youtube";
    if (lower.includes("vimeo.com")) return "vimeo";
    if (lower.includes("docs.google.com/document")) return "gdocs";
    if (lower.includes("docs.google.com/spreadsheets")) return "gsheets";
    if (lower.includes("docs.google.com/presentation")) return "gslides";
    if (lower.includes("drive.google.com/file")) return "gdrive";

    return "unknown";
};

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
// 📄 FUNCIONES PARA GOOGLE DOCS/SHEETS/SLIDES/DRIVE
// ─────────────────────────────────────────────────────────────
const extractGoogleFileId = (url) => {
    if (!url) return null;

    // Docs/Sheets/Slides: /d/FILE_ID/
    const match = url.match(/\/d\/([a-zA-Z0-9_-]+)/);
    if (match && match[1]) return match[1];

    // Drive: /file/d/FILE_ID/
    const driveMatch = url.match(/\/file\/d\/([a-zA-Z0-9_-]+)/);
    if (driveMatch && driveMatch[1]) return driveMatch[1];

    return null;
};

const getGoogleEmbedUrl = (url, type = "gdocs") => {
    const fileId = extractGoogleFileId(url);
    if (!fileId) return null;

    const baseUrl = {
        gdocs: `https://docs.google.com/document/d/${fileId}/preview`,
        gsheets: `https://docs.google.com/spreadsheets/d/${fileId}/preview?widget=true&headers=false`,
        gslides: `https://docs.google.com/presentation/d/${fileId}/embed?start=false&loop=false&delayms=3000`,
        gdrive: `https://drive.google.com/file/d/${fileId}/preview`,
    };

    return baseUrl[type] || baseUrl.gdrive;
};

const getDocumentThumbnail = (url, type) => {
    // Icons por tipo de documento
    const icons = {
        gdocs: "📄",
        gsheets: "📊",
        gslides: "🎞️",
        gdrive: "📁",
    };
    return `data:image/svg+xml,${encodeURIComponent(`<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><rect width="100" height="100" fill="#1a1a2e"/><text x="50" y="60" font-size="40" text-anchor="middle">${icons[type] || "📄"}</text></svg>`)}`;
};

// ─────────────────────────────────────────────────────────────
// 📊 COMPUTED PROPERTIES
// ─────────────────────────────────────────────────────────────
const contentType = computed(() =>
    resource.value?.url ? getContentType(resource.value.url) : null,
);

const isVideo = computed(() =>
    ["youtube", "vimeo"].includes(contentType.value),
);
const isDocument = computed(() =>
    ["gdocs", "gsheets", "gslides", "gdrive"].includes(contentType.value),
);

const hasValidVideo = computed(() => {
    if (!resource.value?.url || !isVideo.value) return false;
    if (contentType.value === "youtube")
        return extractYouTubeId(resource.value.url) !== null;
    return true; // Vimeo con URL válida
});

const hasValidDocument = computed(() => {
    if (!resource.value?.url || !isDocument.value) return false;
    return extractGoogleFileId(resource.value.url) !== null;
});

const embedUrl = computed(() => {
    if (!resource.value?.url) return null;

    if (contentType.value === "youtube")
        return getYouTubeEmbedUrl(resource.value.url, true);
    if (contentType.value === "vimeo")
        return getVimeoEmbedUrl(resource.value.url, true);
    if (isDocument.value)
        return getGoogleEmbedUrl(resource.value.url, contentType.value);

    return null;
});

const contentThumbnail = computed(() => {
    if (!resource.value?.url) return null;

    if (isVideo.value) return getVideoThumbnail(resource.value.url);
    if (isDocument.value)
        return getDocumentThumbnail(resource.value.url, contentType.value);

    return null;
});

const contentTitle = computed(() => {
    if (!resource.value) return "Cargando...";
    const titles = {
        youtube: "Video de YouTube",
        vimeo: "Video de Vimeo",
        gdocs: "Documento de Google",
        gsheets: "Hoja de cálculo de Google",
        gslides: "Presentación de Google",
        gdrive: "Archivo de Google Drive",
    };
    return titles[contentType.value] || "Contenido";
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
// 🚫 BLOQUEO DE INTERACCIONES (solo para videos)
// ─────────────────────────────────────────────────────────────
const preventInteraction = (e) => {
    // Solo bloquear si es video (para ocultar controles de YouTube)
    if (isVideo.value) {
        e.preventDefault();
        e.stopPropagation();
        return false;
    }
    // Para documentos: permitir interacción normal
    return true;
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
    contentLoaded.value = false;

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
// 🔙 VOLVER AL DASHBOARD
// ─────────────────────────────────────────────────────────────
const handleBack = () => {
    router.visit(route("dashboard"));
};

// ─────────────────────────────────────────────────────────────
// 🎨 ESTILOS FULLSCREEN
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
                display: none !important; opacity: 0 !important;
                visibility: hidden !important; pointer-events: none !important;
            }
            /* Para documentos: permitir scroll en fullscreen */
            :-webkit-full-screen iframe,
            :fullscreen iframe {
                width: 100% !important;
                height: 100% !important;
                border: none;
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
    if (newVal?.url && embedUrl.value) {
        // Pequeño delay para asegurar que el iframe se renderice
        setTimeout(() => {
            contentLoaded.value = true;
        }, 500);
    }
});
</script>

<template>
    <AppLayout :title="resource?.titulo || contentTitle">
        <main
            class="min-h-screen bg-gradient-to-b from-slate-50 via-white to-slate-100 dark:from-gray-900 dark:via-gray-900 dark:to-gray-800"
        >
            <div class="max-w-screen-xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
                <!-- 🔙 Botón VOLVER -->
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

                <!-- 🎬📄 PLAYER UNIVERSAL -->
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
                            v-if="!contentLoaded"
                            class="absolute inset-0 flex flex-col items-center justify-center bg-slate-900/80 backdrop-blur-sm z-10"
                        >
                            <div
                                v-if="contentThumbnail"
                                class="absolute inset-0 bg-cover bg-center opacity-20"
                                :style="{
                                    backgroundImage: `url(${contentThumbnail})`,
                                }"
                            >
                                <div
                                    class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/50 to-transparent"
                                ></div>
                            </div>
                            <div
                                class="relative flex items-center justify-center w-20 h-20 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-3xl"
                            >
                                {{
                                    isVideo
                                        ? "▶"
                                        : isDocument
                                          ? contentType === "gsheets"
                                              ? "📊"
                                              : contentType === "gslides"
                                                ? "🎞️"
                                                : "📄"
                                          : "📁"
                                }}
                            </div>
                            <p
                                class="mt-5 text-sm text-slate-400 font-medium animate-pulse"
                            >
                                {{
                                    isVideo
                                        ? "Cargando video..."
                                        : "Cargando documento..."
                                }}
                            </p>
                        </div>

                        <!-- IFRAME PARA VIDEO (YouTube/Vimeo) -->
                        <div
                            v-if="isVideo && hasValidVideo && embedUrl"
                            class="relative w-full h-full overflow-hidden"
                        >
                            <iframe
                                :src="embedUrl"
                                :title="resource.titulo"
                                class="absolute inset-0 w-full h-full transition-opacity duration-500"
                                :class="
                                    contentLoaded ? 'opacity-100' : 'opacity-0'
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
                                @load="contentLoaded = true"
                            ></iframe>

                            <!-- 🔒 CAPAS DE BLOQUEO (solo para videos - ocultan controles) -->
                            <div
                                v-if="contentType === 'youtube'"
                                class="absolute top-0 left-0 right-0 z-30 h-[clamp(60px,12%,120px)] bg-gradient-to-b from-black/80 via-black/40 to-transparent pointer-events-auto"
                                @click.prevent="preventInteraction"
                                @touchstart.prevent="preventInteraction"
                                @contextmenu.prevent="preventInteraction"
                            ></div>
                            <div
                                v-if="contentType === 'youtube'"
                                class="absolute bottom-0 left-0 right-0 z-30 h-[clamp(80px,14%,140px)] bg-gradient-to-t from-black/90 via-black/50 to-transparent pointer-events-auto"
                                @click.prevent="preventInteraction"
                                @touchstart.prevent="preventInteraction"
                                @contextmenu.prevent="preventInteraction"
                            ></div>
                        </div>

                        <!-- IFRAME PARA DOCUMENTOS (Google Docs/Sheets/Slides/Drive) -->
                        <div
                            v-else-if="
                                isDocument && hasValidDocument && embedUrl
                            "
                            class="relative w-full h-full overflow-hidden"
                        >
                            <iframe
                                :src="embedUrl"
                                :title="resource.titulo"
                                class="absolute inset-0 w-full h-full transition-opacity duration-500 border-0"
                                :class="
                                    contentLoaded ? 'opacity-100' : 'opacity-0'
                                "
                                frameborder="0"
                                allowfullscreen
                                loading="lazy"
                                @load="contentLoaded = true"
                            ></iframe>
                            <!-- Sin capas de bloqueo para documentos → usuario puede interactuar -->
                        </div>

                        <!-- Contenido no compatible -->
                        <div
                            v-else-if="!hasValidVideo && !hasValidDocument"
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
                                    d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"
                                />
                            </svg>
                            <h3 class="text-xl font-semibold text-white mb-2">
                                Contenido no compatible
                            </h3>
                            <p class="text-slate-400 text-sm max-w-xs">
                                {{
                                    contentType === "unknown"
                                        ? "La URL no es de un servicio soportado."
                                        : "No se pudo procesar este tipo de contenido."
                                }}
                            </p>
                            <p class="text-slate-500 text-xs mt-2 break-all">
                                {{ resource.url }}
                            </p>
                        </div>
                    </div>

                    <!-- Badge de tipo de contenido -->
                    <div class="absolute top-4 left-4 z-40">
                        <span
                            :class="[
                                'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-medium backdrop-blur-md border',
                                isVideo
                                    ? 'bg-rose-500/20 text-rose-200 border-rose-400/30'
                                    : isDocument
                                      ? 'bg-blue-500/20 text-blue-200 border-blue-400/30'
                                      : 'bg-gray-500/20 text-gray-200 border-gray-400/30',
                            ]"
                        >
                            {{
                                isVideo
                                    ? "🎬 Video"
                                    : isDocument
                                      ? contentType === "gsheets"
                                          ? "📊 Sheets"
                                          : contentType === "gslides"
                                            ? "🎞️ Slides"
                                            : contentType === "gdocs"
                                              ? "📄 Docs"
                                              : "📁 Drive"
                                      : "📦 Contenido"
                            }}
                        </span>
                    </div>

                    <!-- Botón Fullscreen -->
                    <button
                        @click="toggleFullscreen"
                        class="absolute z-40 right-3 bottom-4 flex items-center justify-center w-10 h-10 rounded-lg bg-black/40 hover:bg-black/60 text-white/90 hover:text-white backdrop-blur-sm border border-white/10 transition-all duration-200"
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

                <!-- Fallback sin URL -->
                <div
                    v-else-if="!loading && !error && !resource?.url"
                    class="text-center py-12 text-gray-500 dark:text-gray-400"
                >
                    Este recurso no contiene contenido para visualizar
                </div>
            </div>
        </main>
    </AppLayout>
</template>

<style scoped>
@keyframes shimmer {
    100% {
        transform: translateX(100%);
    }
}
.animate-shimmer {
    animation: shimmer 1.5s infinite;
}
.aspect-video {
    aspect-ratio: 16 / 9;
}
.player-container {
    user-select: none;
    -webkit-user-select: none;
    touch-action: none;
}
/* Permitir selección en documentos */
.player-container:has(iframe[src*="docs.google.com"]),
.player-container:has(iframe[src*="drive.google.com"]) {
    user-select: text;
    -webkit-user-select: text;
    touch-action: auto;
}
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
