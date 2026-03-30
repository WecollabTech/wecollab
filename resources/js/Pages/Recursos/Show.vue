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
const showToast = ref(false);
const toastMessage = ref("");
const toastType = ref("success");
const videoLoaded = ref(false);
const isFullscreen = ref(false);
const playerContainer = ref(null);
const baseUrl = ref("");

const page = usePage();
const pageProps = page.props;

// ─────────────────────────────────────────────────────────────
// 🎨 CONFIGURACIÓN DE TIPOS Y ESTILOS
// ─────────────────────────────────────────────────────────────
const tipoConfig = {
    video: {
        icon: "🎬",
        gradient: "from-rose-500 to-rose-600",
        badge: "bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-300",
        route: "videos",
    },
    manual: {
        icon: "📚",
        gradient: "from-blue-500 to-blue-600",
        badge: "bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300",
        route: "manuales",
    },
    guia: {
        icon: "🧭",
        gradient: "from-emerald-500 to-emerald-600",
        badge: "bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300",
        route: "guias",
    },
    post: {
        icon: "📝",
        gradient: "from-amber-500 to-amber-600",
        badge: "bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300",
        route: "posts",
    },
    triptico: {
        icon: "🎨",
        gradient: "from-violet-500 to-violet-600",
        badge: "bg-violet-100 text-violet-700 dark:bg-violet-900/30 dark:text-violet-300",
        route: "tripticos",
    },
    "avisos importantes": {
        icon: "⚠️",
        gradient: "from-red-500 to-red-600",
        badge: "bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300",
        route: "avisos",
    },
    default: {
        icon: "📦",
        gradient: "from-gray-500 to-gray-600",
        badge: "bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300",
        route: "todos",
    },
};

const getTipoConfig = (tipo) => tipoConfig[tipo] || tipoConfig.default;

// ─────────────────────────────────────────────────────────────
// 🎬 FUNCIONES PARA CONTENIDO EMBEBIDO
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
// 🚫 BLOQUEO DE INTERACCIONES
// ─────────────────────────────────────────────────────────────
const preventInteraction = (e) => {
    e.preventDefault();
    e.stopPropagation();
    return false;
};

// ─────────────────────────────────────────────────────────────
// 🛠️ UTILIDADES
// ─────────────────────────────────────────────────────────────
const formatDate = (str) => {
    if (!str) return "";
    const date = new Date(str);
    if (isNaN(date.getTime())) return "Fecha inválida";
    return date.toLocaleDateString("es-ES", {
        year: "numeric",
        month: "long",
        day: "numeric",
    });
};

const showNotification = (message, type = "success") => {
    toastMessage.value = message;
    toastType.value = type;
    showToast.value = true;
    setTimeout(() => {
        showToast.value = false;
    }, 3000);
};

const copyToClipboard = async (text) => {
    try {
        await navigator.clipboard.writeText(text);
        showNotification("✅ Enlace copiado al portapapeles");
    } catch (err) {
        showNotification("❌ Error al copiar el enlace", "error");
    }
};

const getBadgeConfig = (type, value) => {
    const configs = {
        formato: {
            bg: "bg-indigo-50 dark:bg-indigo-950/50",
            text: "text-indigo-700 dark:text-indigo-300",
            icon: "📄",
        },
        alcance: {
            bg: "bg-teal-50 dark:bg-teal-950/50",
            text: "text-teal-700 dark:text-teal-300",
            icon: "👥",
        },
        estado: {
            activo: {
                bg: "bg-emerald-50 dark:bg-emerald-950/50",
                text: "text-emerald-700 dark:text-emerald-300",
                icon: "✓",
            },
            inactivo: {
                bg: "bg-slate-50 dark:bg-slate-800",
                text: "text-slate-600 dark:text-slate-400",
                icon: "○",
            },
            borrador: {
                bg: "bg-amber-50 dark:bg-amber-950/50",
                text: "text-amber-700 dark:text-amber-300",
                icon: "✎",
            },
        },
    };
    if (type === "estado") {
        return configs.estado[value] || configs.estado.inactivo;
    }
    return (
        configs[type] || {
            bg: "bg-slate-50 dark:bg-slate-800",
            text: "text-slate-700 dark:text-slate-300",
            icon: "•",
        }
    );
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
// 🔧 ACCIONES
// ─────────────────────────────────────────────────────────────
const handleEdit = () => {
    const resourceId = props.id || pageProps.id;
    if (resourceId) router.visit(`/recursos/${resourceId}/edit`);
};

const handleShare = () => {
    if (resource.value) {
        const shareUrl = `${baseUrl.value}/recursos/compartir/${resource.value.id}`;
        copyToClipboard(shareUrl);
        showNotification(
            "✅ Enlace personalizado copiado al portapapeles. ¡No expone la URL original!",
        );
    }
};

const handleBack = () => {
    const referrer = document.referrer;
    if (
        referrer &&
        (referrer.includes("/recursos") || referrer.includes("/dashboard"))
    ) {
        window.history.back();
    } else if (resource.value && resource.value.tipo_material) {
        const tipoRoute = getTipoConfig(resource.value.tipo_material)?.route;
        if (tipoRoute && tipoRoute !== "todos") {
            router.visit(`/recursos/${tipoRoute}`);
            return;
        }
    }
    router.visit("/recursos");
};

const getBaseUrl = () => {
    if (typeof window !== "undefined") {
        baseUrl.value = window.location.origin;
    }
};

// ─────────────────────────────────────────────────────────────
// 🎨 ESTILOS DE PANTALLA COMPLETA
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
    getBaseUrl();
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
    <AppLayout :title="resource?.titulo || 'Cargando...'">
        <main
            class="min-h-screen bg-gradient-to-b from-slate-50 via-white to-slate-100 dark:from-gray-900 dark:via-gray-900 dark:to-gray-800"
        >
            <div
                class="max-w-screen-xl mx-auto px-4 pt-2 pb-10 sm:px-6 lg:px-8"
            >
                <!-- Header con botón volver -->
                <header
                    class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6"
                >
                    <button
                        @click="handleBack"
                        class="group inline-flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-slate-600 dark:text-slate-400 font-medium hover:text-slate-900 dark:hover:text-slate-200 hover:bg-white dark:hover:bg-gray-800 hover:shadow-sm transition-all duration-150"
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
                        <span>Volver</span>
                    </button>
                    <span
                        v-if="resource?.estado"
                        :class="[
                            getBadgeConfig('estado', resource.estado).bg,
                            getBadgeConfig('estado', resource.estado).text,
                        ]"
                        class="inline-flex self-start sm:self-auto items-center gap-1.5 px-3.5 py-2 rounded-full text-xs font-bold tracking-wide uppercase"
                    >
                        <span>{{
                            getBadgeConfig("estado", resource.estado).icon
                        }}</span>
                        {{ resource.estado }}
                    </span>
                </header>

                <!-- Skeleton Loading -->
                <div v-if="loading" class="space-y-6">
                    <div
                        class="relative aspect-video w-full bg-slate-200 dark:bg-gray-700 rounded-2xl overflow-hidden"
                    >
                        <div
                            class="absolute inset-0 -translate-x-full bg-gradient-to-r from-transparent via-white/40 to-transparent animate-shimmer"
                        ></div>
                    </div>
                    <div
                        class="bg-white dark:bg-gray-800 rounded-2xl p-6 md:p-8 shadow-sm ring-1 ring-slate-900/5 dark:ring-gray-700 space-y-4"
                    >
                        <div
                            class="h-8 bg-slate-200 dark:bg-gray-700 rounded w-3/4 animate-pulse"
                        ></div>
                        <div class="space-y-3">
                            <div
                                class="h-4 bg-slate-200 dark:bg-gray-700 rounded w-full animate-pulse"
                            ></div>
                            <div
                                class="h-4 bg-slate-200 dark:bg-gray-700 rounded w-5/6 animate-pulse"
                            ></div>
                        </div>
                    </div>
                </div>

                <!-- Error State -->
                <div
                    v-else-if="error"
                    class="bg-gradient-to-br from-red-50 to-rose-50 dark:from-red-950/30 dark:to-rose-950/30 border border-red-200 dark:border-red-800 rounded-2xl p-6 md:p-8 flex flex-col md:flex-row items-center gap-5 text-red-800 dark:text-red-300 shadow-sm"
                >
                    <div class="relative flex-shrink-0">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="relative h-10 w-10 text-red-500"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="1.5"
                                d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"
                            />
                        </svg>
                    </div>
                    <div class="flex-1 text-center md:text-left">
                        <p class="font-semibold text-lg">
                            No se pudo cargar el recurso
                        </p>
                        <p class="text-sm opacity-80 mt-1">{{ error }}</p>
                    </div>
                    <button
                        @click="loadResource"
                        class="px-5 py-2.5 bg-red-600 text-white rounded-xl font-medium hover:bg-red-700 transition-all"
                    >
                        Reintentar
                    </button>
                </div>

                <!-- Contenido Principal -->
                <article v-else-if="resource" class="space-y-6">
                    <!-- Header del recurso -->
                    <div
                        class="relative overflow-hidden rounded-2xl bg-white/95 dark:bg-gray-800/95 backdrop-blur-xl border shadow-xl"
                    >
                        <div
                            :class="[
                                'h-2 bg-gradient-to-r',
                                getTipoConfig(resource.tipo_material)?.gradient,
                            ]"
                        ></div>
                        <div class="p-6 sm:p-8">
                            <div
                                class="flex flex-col lg:flex-row lg:items-start gap-6"
                            >
                                <div class="flex items-start gap-4 flex-1">
                                    <div class="relative">
                                        <div
                                            :class="[
                                                'absolute -inset-1 rounded-xl blur-xl opacity-30 bg-gradient-to-br',
                                                getTipoConfig(
                                                    resource.tipo_material,
                                                )?.gradient,
                                            ]"
                                        ></div>
                                        <div
                                            :class="[
                                                'relative flex h-16 w-16 items-center justify-center rounded-xl text-3xl text-white shadow-lg bg-gradient-to-br',
                                                getTipoConfig(
                                                    resource.tipo_material,
                                                )?.gradient,
                                            ]"
                                        >
                                            {{
                                                getTipoConfig(
                                                    resource.tipo_material,
                                                )?.icon
                                            }}
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <h1
                                            class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900 dark:text-gray-100"
                                        >
                                            {{ resource.titulo }}
                                        </h1>
                                        <div class="flex flex-wrap gap-2 mt-2">
                                            <span
                                                :class="[
                                                    'px-3 py-1 rounded-full text-sm font-medium',
                                                    getTipoConfig(
                                                        resource.tipo_material,
                                                    )?.badge,
                                                ]"
                                                >{{
                                                    resource.tipo_material
                                                }}</span
                                            >
                                            <span
                                                v-if="resource.formato"
                                                :class="[
                                                    getBadgeConfig(
                                                        'formato',
                                                        resource.formato,
                                                    ).bg,
                                                    getBadgeConfig(
                                                        'formato',
                                                        resource.formato,
                                                    ).text,
                                                ]"
                                                class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-sm font-medium"
                                            >
                                                <span>{{
                                                    getBadgeConfig(
                                                        "formato",
                                                        resource.formato,
                                                    ).icon
                                                }}</span>
                                                {{ resource.formato }}
                                            </span>
                                            <span
                                                class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm"
                                            >
                                                {{
                                                    formatDate(
                                                        resource.created_at,
                                                    )
                                                }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex-shrink-0">
                                    <span
                                        :class="[
                                            'inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-semibold',
                                            resource.estado === 'activo'
                                                ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300'
                                                : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400',
                                        ]"
                                    >
                                        <span
                                            :class="[
                                                'h-2 w-2 rounded-full',
                                                resource.estado === 'activo'
                                                    ? 'bg-emerald-500 animate-pulse'
                                                    : 'bg-gray-400',
                                            ]"
                                        ></span>
                                        {{
                                            resource.estado === "activo"
                                                ? "Activo"
                                                : "Inactivo"
                                        }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 🎬 VIDEO PLAYER CON BLOQUEO DE BARRAS -->
                    <section
                        v-if="resource.url"
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

                            <!-- Iframe de YouTube con capas protectoras -->
                            <div
                                v-if="hasValidVideo && videoEmbedUrl"
                                class="relative w-full h-full overflow-hidden"
                            >
                                <!-- VIDEO -->
                                <iframe
                                    :src="videoEmbedUrl"
                                    :title="resource.titulo"
                                    class="absolute inset-0 w-full h-full transition-opacity duration-500"
                                    :class="
                                        videoLoaded
                                            ? 'opacity-100'
                                            : 'opacity-0'
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

                                <!-- 🔒 CAPA SUPERIOR -->
                                <div
                                    class="absolute top-0 left-0 right-0 z-30 h-[clamp(60px,12%,120px)] bg-gradient-to-b from-black/80 via-black/40 to-transparent pointer-events-auto"
                                    @click.prevent="preventInteraction"
                                    @touchstart.prevent="preventInteraction"
                                    @contextmenu.prevent="preventInteraction"
                                ></div>

                                <!-- 🔒 CAPA INFERIOR -->
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
                                <h3
                                    class="text-xl font-semibold text-white mb-2"
                                >
                                    Video no disponible
                                </h3>
                                <p class="text-slate-400 text-sm max-w-xs">
                                    La URL del video no es compatible o el
                                    contenido ha sido restringido.
                                </p>
                            </div>
                        </div>

                        <!-- Botón Fullscreen -->
                        <button
                            @click="toggleFullscreen"
                            class="absolute z-40 right-3 bottom-[95px] md:bottom-[85px] flex items-center justify-center w-11 h-11 md:w-10 md:h-10 rounded-lg bg-black/50 hover:bg-black/70 text-white/90 hover:text-white backdrop-blur-md transition-all duration-200"
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

                    <!-- Descripción -->
                    <!-- <div
                        class="rounded-2xl bg-white/95 dark:bg-gray-800/95 backdrop-blur-xl border shadow-xl overflow-hidden"
                    >
                        <div
                            :class="[
                                'h-1.5 bg-gradient-to-r',
                                getTipoConfig(resource.tipo_material)?.gradient,
                            ]"
                        ></div>
                        <div class="p-6">
                            <h3
                                class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4"
                            >
                                📝 Descripción
                            </h3>
                            <p
                                class="text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-wrap"
                            >
                                {{
                                    resource.descripcion ||
                                    "Sin descripción disponible."
                                }}
                            </p>
                        </div>
                    </div> -->
                    <!-- Información adicional -->
                    <div
                        class="rounded-2xl bg-white/95 dark:bg-gray-800/95 backdrop-blur-xl border shadow-xl overflow-hidden"
                    >
                        <!-- Barra superior -->
                        <div
                            :class="[
                                'h-1.5 bg-gradient-to-r',
                                getTipoConfig(resource.tipo_material)?.gradient,
                            ]"
                        ></div>

                        <div class="p-5 space-y-4">
                            <h3
                                class="text-base font-semibold text-gray-900 dark:text-gray-100"
                            >
                                ℹ️ Información adicional
                            </h3>

                            <div class="grid md:grid-cols-2 gap-4 text-sm">
                                <!-- Item -->
                                <div>
                                    <p class="text-gray-500">ID del recurso</p>
                                    <p
                                        class="font-mono text-gray-900 dark:text-gray-100"
                                    >
                                        {{ resource.id }}
                                    </p>
                                </div>

                                <div>
                                    <p class="text-gray-500">
                                        Última actualización
                                    </p>
                                    <p class="text-gray-900 dark:text-gray-100">
                                        {{ formatDate(resource.updated_at) }}
                                    </p>
                                </div>

                                <div v-if="resource.alcance">
                                    <p class="text-gray-500">Alcance</p>
                                    <p class="text-gray-900 dark:text-gray-100">
                                        {{ resource.alcance }}
                                    </p>
                                </div>

                                <!-- Compartir -->
                                <div class="md:col-span-2">
                                    <p class="text-gray-500">
                                        Enlace para compartir
                                    </p>

                                    <div class="flex gap-2 mt-1">
                                        <input
                                            :value="`${baseUrl}/recursos/compartir/${resource.id}`"
                                            readonly
                                            class="flex-1 px-3 py-1.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg truncate focus:outline-none"
                                        />

                                        <button
                                            @click="handleShare"
                                            class="px-3 py-1.5 rounded-lg bg-gradient-to-r from-indigo-500 to-purple-600 text-white hover:shadow-md"
                                        >
                                            Copiar
                                        </button>
                                    </div>

                                    <p class="text-xs text-gray-500 mt-1">
                                        🔒 Enlace seguro
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Acciones -->
                    <div class="flex gap-4 justify-end">
                        <button
                            @click="handleBack"
                            class="px-6 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                        >
                            Volver
                        </button>
                        <button
                            v-if="resource?.url"
                            @click="handleShare"
                            class="px-6 py-2.5 rounded-lg bg-gradient-to-r from-blue-500 to-blue-600 text-white hover:shadow-lg transition-all flex items-center gap-2"
                        >
                            <svg
                                class="h-4 w-4"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"
                                />
                            </svg>
                            Compartir (URL segura)
                        </button>
                        <button
                            @click="handleEdit"
                            class="px-6 py-2.5 rounded-lg bg-gradient-to-r from-amber-500 to-amber-600 text-white hover:shadow-lg transition-all flex items-center gap-2"
                        >
                            <svg
                                class="h-4 w-4"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                                />
                            </svg>
                            Editar
                        </button>
                    </div>
                </article>
            </div>
        </main>

        <!-- Toast notification -->
        <Teleport to="body">
            <Transition name="toast">
                <div
                    v-if="showToast"
                    :class="[
                        'fixed bottom-4 right-4 z-50 flex items-center gap-2 px-4 py-3 rounded-lg shadow-lg transition-all',
                        toastType === 'success'
                            ? 'bg-green-500 text-white'
                            : toastType === 'info'
                              ? 'bg-blue-500 text-white'
                              : 'bg-red-500 text-white',
                    ]"
                >
                    <svg
                        v-if="toastType === 'success'"
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M5 13l4 4L19 7"
                        />
                    </svg>
                    <svg
                        v-else-if="toastType === 'info'"
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                        />
                    </svg>
                    <svg
                        v-else
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"
                        />
                    </svg>
                    <span class="text-sm">{{ toastMessage }}</span>
                </div>
            </Transition>
        </Teleport>
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
.select-none {
    user-select: none;
    -webkit-user-select: none;
}
.touch-none {
    touch-action: none;
}
.toast-enter-active,
.toast-leave-active {
    transition: all 0.3s ease;
}
.toast-enter-from,
.toast-leave-to {
    opacity: 0;
    transform: translateX(100%);
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
