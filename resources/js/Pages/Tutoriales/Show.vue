<script setup lang="ts">
import { ref, onMounted, computed, watch, onUnmounted } from "vue";
import axios from "axios";
import { router, usePage } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";

// ─────────────────────────────────────────────────────────────
// 📋 Types (TypeScript)
// ─────────────────────────────────────────────────────────────
interface Tutorial {
    id: number;
    titulo: string;
    descripcion: string | null;
    url: string | null;
    formato: string | null;
    alcance: string | null;
    estado: "activo" | "inactivo" | "borrador";
    duracion?: string;
    visualizaciones?: number;
    created_at: string | null;
    updated_at: string | null;
}

// ─────────────────────────────────────────────────────────────
// 📥 Props y Emits
// ─────────────────────────────────────────────────────────────
const props = defineProps<{
    id?: number | string; // ✅ Hacer opcional
    tutorial?: Tutorial; // ✅ Permitir pasar tutorial desde Inertia
}>();

const emit = defineEmits<{
    (e: "report-issue"): void;
    (e: "tutorial-loaded", tutorial: Tutorial): void;
}>();

// ✅ Obtener page para fallback si no viene el tutorial por props
const page = usePage();

// ─────────────────────────────────────────────────────────────
// 📡 Estado Reactivo
// ─────────────────────────────────────────────────────────────
const tutorial = ref<Tutorial | null>(null);
const loading = ref(true);
const error = ref<string | null>(null);
const retryCount = ref(0);
const videoLoaded = ref(false);
const isFullscreen = ref(false);
const playerContainer = ref<HTMLElement | null>(null);

// ─────────────────────────────────────────────────────────────
// 🔗 Obtener ID válido con fallback múltiple
// ─────────────────────────────────────────────────────────────
const getTutorialId = (): number | null => {
    // 1️⃣ Prioridad: prop id
    if (props.id && props.id !== "undefined" && props.id !== "") {
        return typeof props.id === "string" ? parseInt(props.id) : props.id;
    }

    // 2️⃣ Fallback: tutorial desde Inertia props
    // if (page.props?.tutorial?.id) {
    //     return page.props.tutorial.id;
    // }

    // 3️⃣ Fallback: URL params
    const urlParams = new URLSearchParams(window.location.search);
    const urlId = urlParams.get("id");
    if (urlId && urlId !== "undefined") {
        return parseInt(urlId);
    }

    return null;
};

// ─────────────────────────────────────────────────────────────
// 🔒 YouTube Embed - Extracción de Video ID
// ─────────────────────────────────────────────────────────────
const extractVideoId = (url: string | null): string | null => {
    if (!url) return null;
    const cleanUrl = url.trim();
    const patterns = [
        /youtu\.be\/([a-zA-Z0-9_-]{11})/,
        /[?&]v=([a-zA-Z0-9_-]{11})/,
        /embed\/([a-zA-Z0-9_-]{11})/,
        /shorts\/([a-zA-Z0-9_-]{11})/,
    ];
    for (const pattern of patterns) {
        const match = cleanUrl.match(pattern);
        if (match?.[1]) return match[1];
    }
    return null;
};

const getEmbedUrl = (url: string | null): string | null => {
    const videoId = extractVideoId(url);
    if (!videoId) return null;
    const params = new URLSearchParams({
        modestbranding: "1",
        rel: "0",
        controls: "1",
        fs: "0",
        disablekb: "0",
        iv_load_policy: "3",
        cc_load_policy: "0",
        autoplay: "0",
        playsinline: "1",
    }).toString();
    return `https://www.youtube.com/embed/${videoId}?${params}`;
};

const getYouTubeThumbnail = (url: string | null): string | null => {
    const videoId = extractVideoId(url);
    return videoId
        ? `https://img.youtube.com/vi/${videoId}/hqdefault.jpg`
        : null;
};

// ─────────────────────────────────────────────────────────────
// 🧮 Computed Properties
// ─────────────────────────────────────────────────────────────
const hasValidVideo = computed(() => {
    return tutorial.value?.url && extractVideoId(tutorial.value.url) !== null;
});

const videoEmbedUrl = computed(() => {
    return tutorial.value?.url ? getEmbedUrl(tutorial.value.url) : null;
});

const videoThumbnail = computed(() => {
    return tutorial.value?.url ? getYouTubeThumbnail(tutorial.value.url) : null;
});

// ─────────────────────────────────────────────────────────────
// 🖥️ Fullscreen API
// ─────────────────────────────────────────────────────────────
const toggleFullscreen = async () => {
    if (!playerContainer.value) return;
    try {
        if (!isFullscreen.value) {
            if (playerContainer.value.requestFullscreen) {
                await playerContainer.value.requestFullscreen();
            } else if ((playerContainer.value as any).webkitRequestFullscreen) {
                await (playerContainer.value as any).webkitRequestFullscreen();
            }
        } else {
            if (document.exitFullscreen) {
                await document.exitFullscreen();
            } else if ((document as any).webkitExitFullscreen) {
                await (document as any).webkitExitFullscreen();
            }
        }
    } catch (err) {
        console.error("Error toggling fullscreen:", err);
    }
};

const handleFullscreenChange = () => {
    isFullscreen.value = !!(
        document.fullscreenElement || (document as any).webkitFullscreenElement
    );
};

// ─────────────────────────────────────────────────────────────
// 🚫 Bloqueo de interacciones
// ─────────────────────────────────────────────────────────────
const preventInteraction = (e: Event) => {
    e.preventDefault();
    e.stopPropagation();
    return false;
};

// ─────────────────────────────────────────────────────────────
// 📡 Cargar Tutorial desde API - ✅ CORREGIDO
// ─────────────────────────────────────────────────────────────
const fetchTutorial = async () => {
    try {
        loading.value = true;
        error.value = null;
        videoLoaded.value = false;

        // ✅ Validar que tenemos un ID válido
        const tutorialId = getTutorialId();

        if (!tutorialId || isNaN(tutorialId)) {
            // ✅ Fallback: usar tutorial desde Inertia props si está disponible
            if (page.props?.tutorial) {
                tutorial.value = page.props.tutorial as Tutorial;
                emit("tutorial-loaded", tutorial.value);
                loading.value = false;
                return;
            }
            throw new Error("ID de tutorial no válido o no proporcionado");
        }

        // ✅ Intentar con prefijo /api/ primero, luego fallback sin prefijo
        let apiUrl = `/api/tutoriales/${tutorialId}`;

        try {
            const response = await axios.get(apiUrl, {
                headers: {
                    Accept: "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                },
                timeout: 10000,
            });
            processTutorialResponse(response.data);
        } catch (primaryError: any) {
            // ✅ Fallback: intentar sin prefijo /api/ si falla con 404
            if (primaryError.response?.status === 404) {
                const fallbackUrl = `/tutoriales/${tutorialId}`;
                const fallbackResponse = await axios.get(fallbackUrl, {
                    headers: { Accept: "application/json" },
                    timeout: 10000,
                });
                processTutorialResponse(fallbackResponse.data);
            } else {
                throw primaryError;
            }
        }
    } catch (err: any) {
        console.error("❌ Error cargando tutorial:", err);

        if (err.response?.status === 404) {
            error.value =
                "Tutorial no encontrado. Verifica que el ID sea correcto.";
        } else if (err.response?.status === 403) {
            error.value = "No tienes permisos para ver este contenido";
        } else if (err.response?.status === 401) {
            error.value = "Debes iniciar sesión para continuar";
        } else {
            error.value =
                err.response?.data?.message ||
                err.message ||
                "No se pudo cargar el tutorial";
        }
    } finally {
        loading.value = false;
    }
};

// ✅ Helper para procesar la respuesta de la API
const processTutorialResponse = (data: any) => {
    const tutorialData: Tutorial = data?.data || data;

    if (!tutorialData?.id) {
        throw new Error(data?.message || "Datos inválidos");
    }

    if (tutorialData.estado !== "activo") {
        throw new Error("Este tutorial no está disponible actualmente");
    }

    tutorial.value = tutorialData;
    emit("tutorial-loaded", tutorialData);
};

const retryLoad = () => {
    if (retryCount.value < 3) {
        retryCount.value++;
        fetchTutorial();
    } else {
        router.visit("/dashboard");
    }
};

// ─────────────────────────────────────────────────────────────
// 🔄 Watchers y Lifecycle - ✅ CORREGIDO
// ─────────────────────────────────────────────────────────────
watch(
    () => props.id,
    (newId) => {
        if (newId && newId !== "undefined" && newId !== "") {
            videoLoaded.value = false;
            retryCount.value = 0;
            fetchTutorial();
        }
    },
    { immediate: false }, // ✅ No ejecutar inmediatamente
);

onMounted(() => {
    // ✅ Solo ejecutar fetch si tenemos ID válido o tutorial en props
    const tutorialId = getTutorialId();
    if (tutorialId || page.props?.tutorial) {
        fetchTutorial();
    } else {
        // ✅ Esperar un tick por si el prop llega después
        setTimeout(() => {
            if (getTutorialId() || page.props?.tutorial) {
                fetchTutorial();
            } else {
                loading.value = false;
                error.value = "No se pudo identificar el tutorial a cargar";
            }
        }, 100);
    }

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

// ─────────────────────────────────────────────────────────────
// 🎨 Helpers de UI
// ─────────────────────────────────────────────────────────────
const formatDate = (dateString: string | null): string => {
    if (!dateString) return "";
    return new Date(dateString).toLocaleDateString("es-ES", {
        year: "numeric",
        month: "long",
        day: "numeric",
    });
};

const goBack = () => {
    const referrer = document.referrer;
    if (
        referrer &&
        (referrer.includes("/dashboard") || referrer.includes("/tutoriales"))
    ) {
        window.history.back();
    } else {
        router.visit("/dashboard");
    }
};

const getBadgeConfig = (type: string, value: string) => {
    const configs: Record<string, any> = {
        formato: { bg: "bg-indigo-50", text: "text-indigo-700", icon: "📄" },
        alcance: { bg: "bg-teal-50", text: "text-teal-700", icon: "👥" },
        estado: {
            activo: {
                bg: "bg-emerald-50",
                text: "text-emerald-700",
                icon: "✓",
            },
            inactivo: { bg: "bg-slate-50", text: "text-slate-600", icon: "○" },
            borrador: { bg: "bg-amber-50", text: "text-amber-700", icon: "✎" },
        },
    };
    if (type === "estado") {
        return (
            configs.estado[value as keyof typeof configs.estado] ||
            configs.estado.inactivo
        );
    }
    return (
        configs[type] || {
            bg: "bg-slate-50",
            text: "text-slate-700",
            icon: "•",
        }
    );
};
</script>

<template>
    <AppLayout title="Ver Tutorial">
        <main
            class="min-h-screen bg-gradient-to-b from-slate-50 via-white to-slate-100"
        >
            <div class="max-w-screen-xl mx-auto px-4 pt-0 pb-6">
                <!-- 🔙 Header -->
                <header
                    class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1 mb-2 mt-0"
                >
                    <button
                        @click="goBack"
                        class="group inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-sm text-slate-600 font-medium hover:text-slate-900 hover:bg-white hover:shadow-sm transition-all duration-150"
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
                        v-if="tutorial?.estado"
                        :class="[
                            getBadgeConfig('estado', tutorial.estado).bg,
                            getBadgeConfig('estado', tutorial.estado).text,
                        ]"
                        class="inline-flex self-start sm:self-auto items-center gap-1.5 px-3.5 py-2 rounded-full text-xs font-bold tracking-wide uppercase"
                    >
                        <span>{{
                            getBadgeConfig("estado", tutorial.estado).icon
                        }}</span>
                        {{ tutorial.estado }}
                    </span>
                </header>

                <!-- ⏳ Skeleton Loader -->
                <div v-if="loading" class="space-y-6">
                    <div
                        class="relative aspect-video w-full bg-slate-200 rounded-2xl overflow-hidden"
                    >
                        <div
                            class="absolute inset-0 -translate-x-full bg-gradient-to-r from-transparent via-white/40 to-transparent animate-shimmer"
                        ></div>
                    </div>
                    <div
                        class="bg-white rounded-2xl p-6 md:p-8 shadow-sm ring-1 ring-slate-900/5 space-y-4"
                    >
                        <div
                            class="h-8 bg-slate-200 rounded w-3/4 animate-pulse"
                        ></div>
                        <div class="space-y-3">
                            <div
                                class="h-4 bg-slate-200 rounded w-full animate-pulse"
                            ></div>
                            <div
                                class="h-4 bg-slate-200 rounded w-5/6 animate-pulse"
                            ></div>
                        </div>
                    </div>
                </div>

                <!-- ❌ Error State -->
                <div
                    v-else-if="error"
                    class="bg-gradient-to-br from-red-50 to-rose-50 border border-red-200 rounded-2xl p-6 md:p-8 flex flex-col md:flex-row items-center gap-5 text-red-800 shadow-sm"
                    role="alert"
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
                            No se pudo cargar el tutorial
                        </p>
                        <p class="text-sm opacity-80 mt-1">{{ error }}</p>
                    </div>
                    <button
                        @click="retryLoad"
                        :disabled="retryCount >= 3"
                        class="px-5 py-2.5 bg-red-600 text-white rounded-xl font-medium hover:bg-red-700 disabled:opacity-50 transition-all"
                    >
                        {{ retryCount >= 3 ? "Ir al Dashboard" : "Reintentar" }}
                    </button>
                </div>

                <!-- ✅ Contenido Principal -->
                <article v-else-if="tutorial" class="space-y-6">
                    <!-- 🎬 VIDEO PLAYER -->
                    <section
                        ref="playerContainer"
                        class="relative group/video player-container"
                    >
                        <div
                            class="relative aspect-video w-full overflow-hidden rounded-2xl bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 shadow-2xl"
                        >
                            <!-- Thumbnail mientras carga -->
                            <div
                                v-if="!videoLoaded"
                                class="absolute inset-0 flex flex-col items-center justify-center bg-slate-900/80 backdrop-blur-sm"
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
                                <button
                                    class="relative group/btn cursor-wait focus:outline-none"
                                    disabled
                                >
                                    <span
                                        class="absolute inset-0 rounded-full bg-teal-500/30 animate-ping"
                                    ></span>
                                    <span
                                        class="relative flex items-center justify-center w-20 h-20 rounded-full bg-white/10 backdrop-blur-md border border-white/20"
                                    >
                                        <svg
                                            class="w-8 h-8 text-white/80 ml-1"
                                            fill="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path d="M8 5v14l11-7z" />
                                        </svg>
                                    </span>
                                </button>
                                <p
                                    class="mt-5 text-sm text-slate-400 font-medium animate-pulse"
                                >
                                    Cargando video...
                                </p>
                            </div>

                            <!-- Iframe de YouTube -->
                            <div
                                v-if="hasValidVideo && videoEmbedUrl"
                                class="relative w-full h-full"
                            >
                                <iframe
                                    :src="videoEmbedUrl"
                                    :title="tutorial.titulo"
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
                                    allowfullscreen="false"
                                    referrerpolicy="strict-origin-when-cross-origin"
                                    loading="lazy"
                                    @load="videoLoaded = true"
                                ></iframe>
                                <!-- Overlays de protección -->
                                <div
                                    class="absolute top-0 left-0 right-0 h-[12%] z-30 bg-gradient-to-b from-slate-900/70 via-slate-900/30 to-transparent cursor-not-allowed select-none"
                                    @click.prevent="preventInteraction"
                                    @touchstart.prevent="preventInteraction"
                                ></div>
                                <div
                                    class="absolute bottom-0 left-0 right-0 h-[15%] z-30 bg-gradient-to-t from-slate-900/85 via-slate-900/40 to-transparent cursor-not-allowed select-none"
                                    @click.prevent="preventInteraction"
                                    @touchstart.prevent="preventInteraction"
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
                                <button
                                    @click="emit('report-issue')"
                                    class="mt-6 inline-flex items-center gap-2 px-4 py-2 bg-white/10 hover:bg-white/20 text-white/90 rounded-lg text-sm font-medium transition-colors"
                                >
                                    Reportar problema
                                </button>
                            </div>
                        </div>

                        <!-- Botón Fullscreen -->
                        <button
                            @click="toggleFullscreen"
                            class="absolute top-4 right-4 z-40 inline-flex items-center justify-center w-10 h-10 rounded-xl bg-black/40 hover:bg-black/60 text-white/80 hover:text-white backdrop-blur-md border border-white/20 transition-all"
                            :title="
                                isFullscreen
                                    ? 'Salir de pantalla completa'
                                    : 'Ver en pantalla completa'
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

                    <!-- 📋 Información del Tutorial -->
                    <section
                        class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-900/5 p-6 md:p-8"
                    >
                        <h1
                            class="text-2xl md:text-3xl font-bold text-slate-900"
                        >
                            {{ tutorial.titulo }}
                        </h1>
                        <p class="mt-4 text-slate-600 leading-relaxed">
                            {{
                                tutorial.descripcion ||
                                "Sin descripción disponible."
                            }}
                        </p>

                        <!-- Badges -->
                        <div class="mt-6 flex flex-wrap gap-2.5">
                            <span
                                v-if="tutorial.formato"
                                :class="[
                                    getBadgeConfig('formato', tutorial.formato)
                                        .bg,
                                    getBadgeConfig('formato', tutorial.formato)
                                        .text,
                                ]"
                                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-full text-sm font-medium capitalize"
                            >
                                <span>{{
                                    getBadgeConfig("formato", tutorial.formato)
                                        .icon
                                }}</span>
                                {{ tutorial.formato }}
                            </span>
                            <span
                                v-if="tutorial.alcance"
                                :class="[
                                    getBadgeConfig('alcance', tutorial.alcance)
                                        .bg,
                                    getBadgeConfig('alcance', tutorial.alcance)
                                        .text,
                                ]"
                                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-full text-sm font-medium"
                            >
                                <span>{{
                                    getBadgeConfig("alcance", tutorial.alcance)
                                        .icon
                                }}</span>
                                {{ tutorial.alcance }}
                            </span>
                        </div>

                        <!-- Fechas -->
                        <div class="mt-8 pt-6 border-t border-slate-200">
                            <dl
                                class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm"
                            >
                                <div
                                    v-if="tutorial.created_at"
                                    class="flex items-center gap-2 text-slate-500"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-4 w-4"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                                        />
                                    </svg>
                                    <span
                                        ><strong class="text-slate-700"
                                            >Creado:</strong
                                        >
                                        {{
                                            formatDate(tutorial.created_at)
                                        }}</span
                                    >
                                </div>
                            </dl>
                        </div>
                    </section>

                    <!-- ⚠️ Aviso de confidencialidad -->
                    <aside
                        class="bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200/60 rounded-xl p-4 md:p-5 flex items-start gap-3"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5 text-amber-600 flex-shrink-0 mt-0.5"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                clip-rule="evenodd"
                            />
                        </svg>
                        <p class="text-sm text-amber-900/90 leading-relaxed">
                            <strong class="font-semibold">Confidencial:</strong>
                            Este contenido es exclusivo para personal
                            autorizado.
                        </p>
                    </aside>
                </article>

                <!-- ❓ Fallback: No encontrado -->
                <div v-else class="text-center py-16 md:py-24">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="mx-auto h-16 w-16 text-slate-300 mb-4"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.5"
                            d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                        />
                    </svg>
                    <h3 class="text-lg font-medium text-slate-900">
                        Tutorial no encontrado
                    </h3>
                    <p class="mt-2 text-slate-500">
                        El contenido que buscas no está disponible o ha sido
                        eliminado.
                    </p>
                    <button
                        @click="router.visit('/dashboard')"
                        class="mt-6 inline-flex items-center gap-2 px-5 py-2.5 bg-teal-600 text-white font-medium rounded-xl hover:bg-teal-700 transition-colors"
                    >
                        Volver al Dashboard
                    </button>
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
.select-none {
    user-select: none;
    -webkit-user-select: none;
}
.touch-none {
    touch-action: none;
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
.player-container__content:fullscreen {
    border-radius: 0 !important;
    box-shadow: none !important;
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
