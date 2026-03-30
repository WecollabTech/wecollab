<script setup>
import { ref, onMounted, watch, computed } from "vue";
import axios from "axios";
import { router } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";

// ─────────────────────────────────────────────────────────────
// 📋 PROPS
// ─────────────────────────────────────────────────────────────
const props = defineProps({
    tipo: { type: String, required: false, default: "todos" },
});

// ─────────────────────────────────────────────────────────────
// 📊 ESTADO REACTIVO
// ─────────────────────────────────────────────────────────────
const recursos = ref([]);
const search = ref("");
const searchDebounced = ref("");
const loading = ref(false);
const viewMode = ref("complete");
const sortBy = ref("recent");
const pagination = ref({});
const isSearchFocused = ref(false);
const searchSuggestions = ref([]);
const hoveredRow = ref(null);
const activeTypeFilters = ref(new Set());
const error = ref(null);
const stats = ref({ total: 0, activos: 0 });
const showDeleteModal = ref(false);
const resourceToDelete = ref(null);
const showShareModal = ref(false);
const resourceToShare = ref(null);
const shareLink = ref("");
const showToast = ref(false);
const toastMessage = ref("");
const toastType = ref("success");

// Tipos de recursos disponibles
const tiposDisponibles = [
    {
        value: "video",
        label: "Video",
        icon: "🎬",
        route: "videos",
        color: "rose",
    },
    {
        value: "manual",
        label: "Manual",
        icon: "📚",
        route: "manuales",
        color: "blue",
    },
    {
        value: "guia",
        label: "Guía",
        icon: "🧭",
        route: "guias",
        color: "emerald",
    },
    {
        value: "post",
        label: "Post",
        icon: "📝",
        route: "posts",
        color: "amber",
    },
    {
        value: "triptico",
        label: "Tríptico",
        icon: "🎨",
        route: "tripticos",
        color: "violet",
    },
    {
        value: "avisos importantes",
        label: "Avisos",
        icon: "⚠️",
        route: "avisos",
        color: "red",
    },
];

// ─────────────────────────────────────────────────────────────
// 🎨 CONFIGURACIÓN DE TIPOS Y ESTILOS
// ─────────────────────────────────────────────────────────────
const tipoConfig = {
    video: {
        icon: "🎬",
        gradient: "from-rose-500 via-pink-500 to-rose-600",
        gradientSoft: "from-rose-50 to-pink-50",
        badge: "bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-300",
        text: "text-rose-600 dark:text-rose-400",
        route: "videos",
    },
    manual: {
        icon: "📚",
        gradient: "from-blue-500 via-cyan-500 to-blue-600",
        gradientSoft: "from-blue-50 to-cyan-50",
        badge: "bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300",
        text: "text-blue-600 dark:text-blue-400",
        route: "manuales",
    },
    guia: {
        icon: "🧭",
        gradient: "from-emerald-500 via-teal-500 to-emerald-600",
        gradientSoft: "from-emerald-50 to-teal-50",
        badge: "bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300",
        text: "text-emerald-600 dark:text-emerald-400",
        route: "guias",
    },
    post: {
        icon: "📝",
        gradient: "from-amber-500 via-orange-500 to-amber-600",
        gradientSoft: "from-amber-50 to-orange-50",
        badge: "bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300",
        text: "text-amber-600 dark:text-amber-400",
        route: "posts",
    },
    triptico: {
        icon: "🎨",
        gradient: "from-violet-500 via-purple-500 to-violet-600",
        gradientSoft: "from-violet-50 to-purple-50",
        badge: "bg-violet-100 text-violet-700 dark:bg-violet-900/30 dark:text-violet-300",
        text: "text-violet-600 dark:text-violet-400",
        route: "tripticos",
    },
    "avisos importantes": {
        icon: "⚠️",
        gradient: "from-red-500 via-orange-500 to-red-600",
        gradientSoft: "from-red-50 to-orange-50",
        badge: "bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300",
        text: "text-red-600 dark:text-red-400",
        route: "avisos",
    },
    default: {
        icon: "📦",
        gradient: "from-gray-500 via-slate-500 to-gray-600",
        gradientSoft: "from-gray-50 to-slate-50",
        badge: "bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300",
        text: "text-gray-600 dark:text-gray-400",
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

const getYouTubeEmbedUrl = (url) => {
    const videoId = extractYouTubeId(url);
    if (!videoId) return url;
    return `https://www.youtube.com/embed/${videoId}?modestbranding=1&rel=0&controls=1&playsinline=1`;
};

const getVimeoEmbedUrl = (url) => {
    const match = url.match(/vimeo\.com\/(\d+)/);
    if (match && match[1]) {
        return `https://player.vimeo.com/video/${match[1]}?title=0&byline=0&portrait=0`;
    }
    return url;
};

const getVideoThumbnail = (url) => {
    const videoId = extractYouTubeId(url);
    if (videoId) return `https://img.youtube.com/vi/${videoId}/hqdefault.jpg`;
    return null;
};

// ─────────────────────────────────────────────────────────────
// 🔍 DEBOUNCE + SUGERENCIAS
// ─────────────────────────────────────────────────────────────
let debounceTimer = null;
const handleSearchInput = (value) => {
    if (debounceTimer) clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        searchDebounced.value = value;
        if (value.length >= 2) {
            searchSuggestions.value = recursos.value
                .filter(
                    (r) =>
                        r.titulo?.toLowerCase().includes(value.toLowerCase()) ||
                        r.descripcion
                            ?.toLowerCase()
                            .includes(value.toLowerCase()),
                )
                .slice(0, 5);
        } else {
            searchSuggestions.value = [];
        }
    }, 300);
};
watch(search, (val) => handleSearchInput(val));

// ─────────────────────────────────────────────────────────────
// 🔄 CARGA DE DATOS
// ─────────────────────────────────────────────────────────────
const loadData = async () => {
    loading.value = true;
    error.value = null;

    try {
        const params = {
            search: searchDebounced.value,
            sort: sortBy.value,
            tipo: props.tipo !== "todos" ? props.tipo : undefined,
        };

        if (activeTypeFilters.value.size > 0) {
            params.tipos = Array.from(activeTypeFilters.value).join(",");
        }

        const { data } = await axios.get("/recursos", { params });
        recursos.value = data.data || [];
        pagination.value = data;

        stats.value.total = data.total || recursos.value.length;
        stats.value.activos = recursos.value.filter(
            (r) => r.estado === "activo",
        ).length;
    } catch (err) {
        console.error("Error:", err);
        error.value = "No se pudieron cargar los recursos";
        recursos.value = [];
    } finally {
        loading.value = false;
    }
};

const loadDataFromUrl = async (url) => {
    if (!url) return;
    loading.value = true;
    try {
        const { data } = await axios.get(url);
        recursos.value = data.data || [];
        pagination.value = data;
    } catch (err) {
        console.error("Error paginación:", err);
        error.value = "Error al navegar páginas";
    } finally {
        loading.value = false;
    }
};

// ─────────────────────────────────────────────────────────────
// 📋 FILTRADO Y ORDENAMIENTO
// ─────────────────────────────────────────────────────────────
const recursosFiltrados = computed(() => {
    let lista = [...recursos.value];

    switch (sortBy.value) {
        case "title":
            lista.sort((a, b) =>
                (a.titulo?.toLowerCase() || "").localeCompare(
                    b.titulo?.toLowerCase() || "",
                ),
            );
            break;
        case "format":
            lista.sort((a, b) =>
                (a.formato?.toLowerCase() || "").localeCompare(
                    b.formato?.toLowerCase() || "",
                ),
            );
            break;
        default:
            lista.sort(
                (a, b) =>
                    new Date(b.created_at || 0) - new Date(a.created_at || 0),
            );
            break;
    }

    return lista;
});

// ─────────────────────────────────────────────────────────────
// 🛠️ UTILIDADES
// ─────────────────────────────────────────────────────────────
const formatDate = (str) => {
    if (!str) return "";
    const date = new Date(str);
    if (isNaN(date.getTime())) return "Fecha inválida";
    return date.toLocaleDateString("es-ES", {
        year: "numeric",
        month: "short",
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
        console.error("Error al copiar:", err);
        showNotification("❌ Error al copiar el enlace", "error");
    }
};

// ─────────────────────────────────────────────────────────────
// 🔧 ACCIONES CRUD
// ─────────────────────────────────────────────────────────────
const handleCreate = () => {
    const tipoParam = props.tipo !== "todos" ? props.tipo : "video";
    router.visit(`/recursos/crear/${tipoParam}`);
};

const handleView = (resource) => {
    const tipoRoute = getTipoConfig(resource.tipo_material)?.route || "todos";
    router.visit(`/recursos/${tipoRoute}/${resource.id}`);
};

const handleEdit = (resource) => {
    router.visit(`/recursos/${resource.id}/edit`);
};

const handleDelete = (resource) => {
    resourceToDelete.value = resource;
    showDeleteModal.value = true;
};

const confirmDelete = async () => {
    if (!resourceToDelete.value) return;

    try {
        await axios.delete(`/recursos/${resourceToDelete.value.id}`);
        showNotification("✅ Recurso eliminado correctamente");
        loadData();
    } catch (err) {
        console.error("Error al eliminar:", err);
        showNotification("❌ Error al eliminar el recurso", "error");
    } finally {
        showDeleteModal.value = false;
        resourceToDelete.value = null;
    }
};

const handleShare = (resource) => {
    resourceToShare.value = resource;
    shareLink.value = `${window.location.origin}/recursos/compartir/${resource.id}`;
    showShareModal.value = true;
};

const copyShareLink = () => {
    copyToClipboard(shareLink.value);
};

const toggleTypeFilter = (tipo) => {
    if (activeTypeFilters.value.has(tipo)) {
        activeTypeFilters.value.delete(tipo);
    } else {
        activeTypeFilters.value.add(tipo);
    }
    loadData();
};

const clearTypeFilters = () => {
    activeTypeFilters.value.clear();
    loadData();
};

const clearSearch = () => {
    search.value = "";
    searchSuggestions.value = [];
    searchDebounced.value = "";
    loadData();
};

const selectSuggestion = (suggestion) => {
    search.value = suggestion.titulo;
    isSearchFocused.value = false;
    searchDebounced.value = suggestion.titulo;
    loadData();
};

// ─────────────────────────────────────────────────────────────
// LIFECYCLE
// ─────────────────────────────────────────────────────────────
onMounted(() => {
    if (props.tipo && props.tipo !== "todos") {
        activeTypeFilters.value.clear();
        activeTypeFilters.value.add(props.tipo);
    }
    loadData();
});

watch([searchDebounced, sortBy, activeTypeFilters], () => {
    loadData();
});
</script>

<template>
    <AppLayout
        :title="`Recursos • ${props.tipo?.toUpperCase() || 'BIBLIOTECA'}`"
    >
        <!-- FONDO ANIMADO -->
        <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none">
            <div
                class="absolute -top-40 -right-40 h-96 w-96 bg-gradient-to-br from-indigo-400/30 to-purple-400/30 rounded-full blur-3xl animate-pulse"
            ></div>
            <div
                class="absolute top-1/3 -left-32 h-72 w-72 bg-gradient-to-br from-blue-400/25 to-cyan-400/25 rounded-full blur-3xl animate-pulse"
                style="animation-delay: 1s"
            ></div>
            <div
                class="absolute -bottom-32 right-1/4 h-80 w-80 bg-gradient-to-br from-pink-400/25 to-rose-400/25 rounded-full blur-3xl animate-pulse"
                style="animation-delay: 2s"
            ></div>
        </div>

        <div class="mx-auto max-w-7xl px-4 pt-2 pb-10 sm:px-6 lg:px-8">
            <!-- HEADER -->
            <header class="mb-8">
                <div
                    class="relative overflow-hidden rounded-2xl bg-white/95 dark:bg-gray-800/95 backdrop-blur-xl border border-gray-200/50 dark:border-gray-700/50 shadow-xl transition-all duration-300 hover:shadow-2xl"
                >
                    <div class="relative p-5 sm:p-6 lg:p-7">
                        <div
                            class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between"
                        >
                            <div class="flex items-center gap-4">
                                <div class="group relative">
                                    <div
                                        class="absolute -inset-1 rounded-xl bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 opacity-0 blur-lg transition-all duration-500 group-hover:opacity-60"
                                    ></div>
                                    <div
                                        class="relative flex h-14 w-14 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 text-2xl text-white shadow-lg transition-all duration-300 group-hover:scale-105"
                                    >
                                        📚
                                    </div>
                                </div>
                                <div>
                                    <h1
                                        class="text-2xl sm:text-3xl lg:text-4xl font-bold bg-gradient-to-r from-gray-900 to-gray-700 dark:from-gray-100 dark:to-gray-300 bg-clip-text text-transparent"
                                    >
                                        {{
                                            activeTypeFilters.size > 0
                                                ? `Recursos Filtrados (${activeTypeFilters.size})`
                                                : props.tipo !== "todos"
                                                  ? `Recursos de ${props.tipo}`
                                                  : "Todos los Recursos"
                                        }}
                                    </h1>
                                    <p
                                        class="text-sm text-gray-500 dark:text-gray-400 mt-0.5"
                                    >
                                        {{
                                            activeTypeFilters.size > 0
                                                ? `Mostrando recursos de tipo: ${Array.from(activeTypeFilters).join(", ")}`
                                                : props.tipo !== "todos"
                                                  ? `Explora todos los recursos de tipo ${props.tipo}`
                                                  : "Explora y gestiona todos los recursos disponibles"
                                        }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex flex-wrap items-center gap-3">
                                <button
                                    @click="handleCreate"
                                    class="group relative overflow-hidden rounded-xl bg-gradient-to-r from-indigo-500 to-purple-600 px-5 py-2.5 text-sm font-semibold text-white shadow-lg transition-all duration-300 hover:shadow-xl hover:scale-105"
                                >
                                    <span
                                        class="relative z-10 flex items-center gap-2"
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
                                                d="M12 4v16m8-8H4"
                                            />
                                        </svg>
                                        Agregar Recurso
                                    </span>
                                    <span
                                        class="absolute inset-0 -translate-x-full bg-gradient-to-r from-indigo-600 to-purple-700 transition-transform duration-300 group-hover:translate-x-0"
                                    ></span>
                                </button>

                                <div
                                    class="flex bg-gray-100/80 dark:bg-gray-800/80 rounded-xl p-1"
                                >
                                    <button
                                        @click="viewMode = 'complete'"
                                        :class="[
                                            'px-3 py-1.5 rounded-lg text-sm font-medium transition-all',
                                            viewMode === 'complete'
                                                ? 'bg-white dark:bg-gray-700 text-indigo-600 shadow-sm'
                                                : 'text-gray-600 hover:bg-white/50',
                                        ]"
                                    >
                                        <svg
                                            class="h-4 w-4 inline mr-1"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M5 3v4M3 5h4M6 17v4M4 19h4M13 3v4M11 5h4M14 17v4M12 19h4M19 3v4M17 5h4M20 17v4M18 19h4"
                                            />
                                        </svg>
                                        Completa
                                    </button>
                                    <button
                                        @click="viewMode = 'list'"
                                        :class="[
                                            'px-3 py-1.5 rounded-lg text-sm font-medium transition-all',
                                            viewMode === 'list'
                                                ? 'bg-white dark:bg-gray-700 text-indigo-600 shadow-sm'
                                                : 'text-gray-600 hover:bg-white/50',
                                        ]"
                                    >
                                        <svg
                                            class="h-4 w-4 inline mr-1"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M4 6h16M4 12h16M4 18h16"
                                            />
                                        </svg>
                                        Lista
                                    </button>
                                    <button
                                        @click="viewMode = 'grid'"
                                        :class="[
                                            'px-3 py-1.5 rounded-lg text-sm font-medium transition-all',
                                            viewMode === 'grid'
                                                ? 'bg-white dark:bg-gray-700 text-indigo-600 shadow-sm'
                                                : 'text-gray-600 hover:bg-white/50',
                                        ]"
                                    >
                                        <svg
                                            class="h-4 w-4 inline mr-1"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"
                                            />
                                        </svg>
                                        Cuadrícula
                                    </button>
                                </div>

                                <select
                                    v-model="sortBy"
                                    class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl px-3 py-1.5 text-sm"
                                >
                                    <option value="recent">🕐 Recientes</option>
                                    <option value="title">🔤 A-Z</option>
                                    <option value="format">📁 Formato</option>
                                </select>
                            </div>
                        </div>

                        <!-- Search Bar -->
                        <div class="mt-5 relative">
                            <div
                                class="relative flex items-center rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700"
                            >
                                <div class="pl-4 pr-2">
                                    <div
                                        class="flex h-10 w-10 items-center justify-center text-gray-400"
                                    >
                                        <svg
                                            class="h-5 w-5"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                                            />
                                        </svg>
                                    </div>
                                </div>
                                <input
                                    v-model="search"
                                    @focus="isSearchFocused = true"
                                    @blur="isSearchFocused = false"
                                    type="text"
                                    placeholder="Buscar recursos por título, descripción..."
                                    class="w-full bg-transparent py-3.5 pr-3 text-sm focus:outline-none"
                                />
                                <button
                                    v-if="search"
                                    @click="clearSearch"
                                    class="mr-2 p-1.5 text-gray-400 hover:text-red-500"
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
                                            d="M6 18L18 6M6 6l12 12"
                                        />
                                    </svg>
                                </button>
                            </div>

                            <!-- Sugerencias -->
                            <div
                                v-if="
                                    isSearchFocused &&
                                    searchSuggestions.length > 0
                                "
                                class="absolute z-50 mt-2 w-full rounded-xl border bg-white shadow-2xl overflow-hidden"
                            >
                                <ul class="max-h-64 overflow-y-auto">
                                    <li
                                        v-for="s in searchSuggestions"
                                        :key="s.id"
                                        class="px-4 py-2.5 hover:bg-indigo-50 cursor-pointer flex items-center gap-3"
                                        @click="selectSuggestion(s)"
                                    >
                                        <div
                                            :class="[
                                                'flex h-8 w-8 items-center justify-center rounded-lg text-white',
                                                getTipoConfig(s.tipo_material)
                                                    ?.gradient,
                                            ]"
                                        >
                                            {{
                                                getTipoConfig(s.tipo_material)
                                                    ?.icon
                                            }}
                                        </div>
                                        <div class="flex-1">
                                            <p
                                                class="text-sm font-medium text-gray-900"
                                            >
                                                {{ s.titulo }}
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                {{ s.tipo_material }} •
                                                {{ s.formato || "Sin formato" }}
                                            </p>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- Filtros -->
                        <div class="mt-5 flex flex-wrap items-center gap-2">
                            <span class="text-xs text-gray-500">Filtrar:</span>
                            <button
                                v-for="tipo in tiposDisponibles"
                                :key="tipo.value"
                                @click="toggleTypeFilter(tipo.value)"
                                :class="[
                                    'px-2.5 py-1 rounded-full text-xs font-medium transition-all',
                                    activeTypeFilters.has(tipo.value)
                                        ? 'bg-gradient-to-r from-indigo-500 to-purple-500 text-white'
                                        : 'bg-gray-100 dark:bg-gray-700 text-gray-600 hover:bg-gray-200',
                                ]"
                            >
                                <span class="text-sm mr-1">{{
                                    tipo.icon
                                }}</span>
                                {{ tipo.label }}
                            </button>
                            <button
                                v-if="activeTypeFilters.size > 0"
                                @click="clearTypeFilters"
                                class="px-2.5 py-1 text-xs text-gray-500 hover:text-gray-700"
                            >
                                Limpiar
                            </button>
                        </div>

                        <!-- Stats -->
                        <div class="mt-4 flex items-center gap-4 text-xs">
                            <div class="flex items-center gap-1.5">
                                <span class="relative flex h-2 w-2"
                                    ><span
                                        class="animate-ping absolute h-full w-full rounded-full bg-emerald-400"
                                    ></span
                                    ><span
                                        class="relative rounded-full h-2 w-2 bg-emerald-500"
                                    ></span
                                ></span>
                                <span>Activos: {{ stats.activos }}</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <span
                                    class="h-2 w-2 rounded-full bg-gray-300"
                                ></span>
                                <span>Totales: {{ stats.total }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- ERROR STATE -->
            <div
                v-if="error"
                class="mb-8 rounded-2xl border border-red-200 bg-red-50 p-4 text-red-700"
            >
                <p class="font-medium">Error al cargar</p>
                <p class="text-sm">{{ error }}</p>
                <button
                    @click="loadData"
                    class="mt-2 text-sm font-semibold text-red-800 hover:underline"
                >
                    Reintentar
                </button>
            </div>

            <!-- SKELETON LOADING -->
            <div v-if="loading" class="space-y-3">
                <div
                    v-for="i in 5"
                    :key="i"
                    class="animate-pulse rounded-2xl border bg-white p-4"
                >
                    <div class="flex items-center gap-4">
                        <div class="h-10 w-10 rounded-xl bg-gray-200"></div>
                        <div class="flex-1 space-y-2">
                            <div class="h-4 w-1/3 rounded bg-gray-200"></div>
                            <div class="h-3 w-full rounded bg-gray-100"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- VISTA DE TABLA -->
            <div
                v-if="
                    (viewMode === 'list' || viewMode === 'complete') &&
                    recursosFiltrados.length > 0
                "
                class="overflow-hidden rounded-2xl border bg-white/90 shadow-sm"
            >
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold uppercase"
                                >
                                    ID
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold uppercase"
                                >
                                    Recurso
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold uppercase hidden sm:table-cell"
                                >
                                    Tipo
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold uppercase hidden md:table-cell"
                                >
                                    Formato
                                </th>
                                <th
                                    class="px-6 py-4 text-center text-xs font-semibold uppercase"
                                >
                                    Estado
                                </th>
                                <th
                                    class="px-6 py-4 text-right text-xs font-semibold uppercase"
                                >
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr
                                v-for="r in recursosFiltrados"
                                :key="r.id"
                                class="group hover:bg-gray-50 transition-colors"
                            >
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ r.id }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            :class="[
                                                'flex h-10 w-10 items-center justify-center rounded-xl text-white',
                                                getTipoConfig(r.tipo_material)
                                                    ?.gradient,
                                            ]"
                                        >
                                            {{
                                                getTipoConfig(r.tipo_material)
                                                    ?.icon
                                            }}
                                        </div>
                                        <div>
                                            <p
                                                class="font-semibold text-gray-900"
                                            >
                                                {{ r.titulo }}
                                            </p>
                                            <p
                                                v-if="r.descripcion"
                                                class="text-xs text-gray-500 line-clamp-1"
                                            >
                                                {{ r.descripcion }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 hidden sm:table-cell">
                                    <span
                                        :class="[
                                            'px-3 py-1 rounded-lg text-xs font-semibold',
                                            getTipoConfig(r.tipo_material)
                                                ?.badge,
                                        ]"
                                        >{{ r.tipo_material }}</span
                                    >
                                </td>
                                <td class="px-6 py-4 hidden md:table-cell">
                                    <span
                                        class="px-3 py-1 rounded-lg bg-gray-100 text-xs"
                                        >{{ r.formato || "N/D" }}</span
                                    >
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span
                                        :class="[
                                            'inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold',
                                            r.estado === 'activo'
                                                ? 'bg-emerald-100 text-emerald-700'
                                                : 'bg-gray-100 text-gray-600',
                                        ]"
                                    >
                                        <span
                                            :class="[
                                                'h-1.5 w-1.5 rounded-full',
                                                r.estado === 'activo'
                                                    ? 'bg-emerald-500'
                                                    : 'bg-gray-400',
                                            ]"
                                        ></span>
                                        {{
                                            r.estado === "activo"
                                                ? "Activo"
                                                : "Inactivo"
                                        }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div
                                        class="flex items-center justify-end gap-1"
                                    >
                                        <button
                                            @click.stop="handleView(r)"
                                            class="p-2 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg"
                                            title="Ver"
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
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                                                />
                                            </svg>
                                        </button>
                                        <button
                                            @click.stop="handleEdit(r)"
                                            class="p-2 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg"
                                            title="Editar"
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
                                        </button>
                                        <button
                                            @click.stop="handleShare(r)"
                                            class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg"
                                            title="Compartir"
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
                                        </button>
                                        <button
                                            @click.stop="handleDelete(r)"
                                            class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg"
                                            title="Eliminar"
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
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                                />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- VISTA DE CUADRÍCULA -->
            <div
                v-else-if="viewMode === 'grid' && recursosFiltrados.length > 0"
                class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3"
            >
                <article
                    v-for="r in recursosFiltrados"
                    :key="r.id"
                    class="group overflow-hidden rounded-2xl border bg-white/90 transition-all duration-300 hover:shadow-xl hover:-translate-y-1"
                >
                    <div
                        :class="[
                            'h-2',
                            getTipoConfig(r.tipo_material)?.gradient,
                        ]"
                    ></div>
                    <div class="p-6">
                        <div
                            :class="[
                                'inline-flex h-12 w-12 items-center justify-center rounded-xl text-white shadow-lg',
                                getTipoConfig(r.tipo_material)?.gradient,
                            ]"
                        >
                            {{ getTipoConfig(r.tipo_material)?.icon }}
                        </div>
                        <h3 class="mt-4 font-bold text-gray-900 line-clamp-2">
                            {{ r.titulo }}
                        </h3>
                        <p
                            v-if="r.descripcion"
                            class="mt-2 text-sm text-gray-600 line-clamp-2"
                        >
                            {{ r.descripcion }}
                        </p>
                        <div class="mt-4 flex flex-wrap gap-2">
                            <span
                                :class="[
                                    'px-2 py-1 rounded-full text-xs font-medium',
                                    getTipoConfig(r.tipo_material)?.badge,
                                ]"
                                >{{ r.tipo_material }}</span
                            >
                            <span
                                class="px-2 py-1 rounded-full bg-gray-100 text-xs"
                                >{{ r.formato }}</span
                            >
                        </div>
                        <div
                            class="mt-4 flex items-center justify-between pt-4 border-t"
                        >
                            <span
                                :class="[
                                    'flex items-center gap-1 text-xs',
                                    r.estado === 'activo'
                                        ? 'text-emerald-600'
                                        : 'text-gray-400',
                                ]"
                            >
                                <span
                                    :class="[
                                        'h-2 w-2 rounded-full',
                                        r.estado === 'activo'
                                            ? 'bg-emerald-500'
                                            : 'bg-gray-300',
                                    ]"
                                ></span>
                                {{
                                    r.estado === "activo"
                                        ? "Activo"
                                        : "Inactivo"
                                }}
                            </span>
                            <div class="flex gap-1">
                                <button
                                    @click.stop="handleView(r)"
                                    class="p-1.5 text-gray-400 hover:text-indigo-600 rounded-lg"
                                    title="Ver"
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
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                                        />
                                    </svg>
                                </button>
                                <button
                                    @click.stop="handleShare(r)"
                                    class="p-1.5 text-gray-400 hover:text-blue-600 rounded-lg"
                                    title="Compartir"
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
                                </button>
                            </div>
                        </div>
                    </div>
                </article>
            </div>

            <!-- EMPTY STATE -->
            <div
                v-else-if="!loading && recursosFiltrados.length === 0"
                class="text-center py-24"
            >
                <div
                    class="mx-auto flex h-24 w-24 items-center justify-center rounded-full bg-gray-100"
                >
                    <svg
                        class="h-12 w-12 text-gray-400"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.5"
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"
                        />
                    </svg>
                </div>
                <h3 class="mt-4 text-xl font-semibold text-gray-900">
                    No hay recursos
                </h3>
                <p class="mt-2 text-gray-600">
                    {{
                        search
                            ? `No se encontraron resultados para "${search}"`
                            : "Comienza agregando tu primer recurso"
                    }}
                </p>
                <button
                    @click="handleCreate"
                    class="mt-6 inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-2.5 text-white font-semibold hover:shadow-lg transition-all"
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
                            d="M12 4v16m8-8H4"
                        />
                    </svg>
                    Agregar recurso
                </button>
            </div>

            <!-- PAGINACIÓN -->
            <nav
                v-if="pagination?.links?.length > 1"
                class="mt-8 flex justify-center"
            >
                <div class="inline-flex gap-2 rounded-xl border bg-white p-1">
                    <button
                        v-for="(link, i) in pagination.links"
                        :key="i"
                        @click="link.url && loadDataFromUrl(link.url)"
                        :disabled="!link.url || link.active"
                        :class="[
                            'px-3 py-1.5 rounded-lg text-sm font-medium transition-all',
                            link.active
                                ? 'bg-indigo-600 text-white'
                                : 'text-gray-700 hover:bg-gray-100',
                            !link.url ? 'opacity-50 cursor-not-allowed' : '',
                        ]"
                        v-html="link.label"
                    ></button>
                </div>
            </nav>
        </div>

        <!-- MODAL DE ELIMINACIÓN -->
        <Teleport to="body">
            <div
                v-if="showDeleteModal"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
                @click.self="showDeleteModal = false"
            >
                <div
                    class="relative max-w-md w-full bg-white rounded-2xl shadow-2xl overflow-hidden"
                >
                    <div
                        class="h-2 bg-gradient-to-r from-red-500 to-red-600"
                    ></div>
                    <div class="p-6">
                        <div class="flex items-center gap-4 mb-4">
                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-full bg-red-100"
                            >
                                <svg
                                    class="h-6 w-6 text-red-600"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                    />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-900">
                                    Eliminar recurso
                                </h3>
                                <p class="text-sm text-gray-500">
                                    ¿Estás seguro de eliminar este recurso?
                                </p>
                            </div>
                        </div>
                        <p class="text-gray-600 mb-6">
                            <strong>{{ resourceToDelete?.titulo }}</strong
                            ><br />Esta acción no se puede deshacer.
                        </p>
                        <div class="flex gap-3 justify-end">
                            <button
                                @click="showDeleteModal = false"
                                class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50"
                            >
                                Cancelar
                            </button>
                            <button
                                @click="confirmDelete"
                                class="px-4 py-2 rounded-lg bg-gradient-to-r from-red-500 to-red-600 text-white hover:shadow-lg"
                            >
                                Eliminar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </Teleport>

        <!-- MODAL DE COMPARTIR -->
        <Teleport to="body">
            <div
                v-if="showShareModal"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
                @click.self="showShareModal = false"
            >
                <div
                    class="relative max-w-md w-full bg-white rounded-2xl shadow-2xl overflow-hidden"
                >
                    <div
                        class="h-2 bg-gradient-to-r from-blue-500 to-blue-600"
                    ></div>
                    <div class="p-6">
                        <div class="flex items-center gap-4 mb-4">
                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-full bg-blue-100"
                            >
                                <svg
                                    class="h-6 w-6 text-blue-600"
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
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-900">
                                    Compartir recurso
                                </h3>
                                <p class="text-sm text-gray-500">
                                    {{ resourceToShare?.titulo }}
                                </p>
                            </div>
                        </div>
                        <div class="mb-4 p-3 bg-blue-50 rounded-lg">
                            <p class="text-xs text-blue-700 mb-2">
                                🔒 Enlace seguro y personalizado:
                            </p>
                            <p class="text-xs text-gray-500">
                                Este enlace no expone la URL original del
                                material.
                            </p>
                        </div>
                        <div class="flex items-center gap-2 mb-6">
                            <input
                                v-model="shareLink"
                                type="text"
                                readonly
                                class="flex-1 px-3 py-2 border rounded-lg bg-gray-50 text-sm focus:outline-none"
                            />
                            <button
                                @click="copyShareLink"
                                class="px-4 py-2 rounded-lg bg-gradient-to-r from-indigo-500 to-purple-600 text-white hover:shadow-lg"
                            >
                                Copiar
                            </button>
                        </div>
                        <div class="flex justify-end">
                            <button
                                @click="showShareModal = false"
                                class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50"
                            >
                                Cerrar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </Teleport>

        <!-- TOAST NOTIFICACIÓN -->
        <Teleport to="body">
            <Transition name="toast">
                <div
                    v-if="showToast"
                    :class="[
                        'fixed bottom-4 right-4 z-50 flex items-center gap-2 px-4 py-3 rounded-lg shadow-lg transition-all',
                        toastType === 'success'
                            ? 'bg-green-500 text-white'
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
.toast-enter-active,
.toast-leave-active {
    transition: all 0.3s ease;
}
.toast-enter-from,
.toast-leave-to {
    opacity: 0;
    transform: translateX(100%);
}
.line-clamp-1 {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
