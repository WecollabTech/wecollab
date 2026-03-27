<script setup>
import { ref, onMounted, watch, computed } from "vue";
import axios from "axios";
import AppLayout from "@/Layouts/AppLayout.vue";

// ─────────────────────────────────────────────────────────────
// 📋 PROPS
// ─────────────────────────────────────────────────────────────
const props = defineProps({
    tipo: { type: String, required: false, default: "" },
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
const activeTypeFilters = ref(new Set()); // AHORA SOPORTA MÚLTIPLES
const error = ref(null);
const stats = ref({
    total: 0,
    activos: 0,
});

// Tipos de recursos disponibles
const tiposDisponibles = [
    { value: "video", label: "Video", icon: "🎬" },
    { value: "manual", label: "Manual", icon: "📚" },
    { value: "guia", label: "Guía", icon: "🧭" },
    { value: "post", label: "Post", icon: "📝" },
    { value: "triptico", label: "Tríptico", icon: "🎨" },
    { value: "avisos importantes", label: "Avisos", icon: "⚠️" },
];

// ─────────────────────────────────────────────────────────────
// 🎨 CONFIGURACIÓN DE TIPOS Y ESTILOS
// ─────────────────────────────────────────────────────────────
const tipoConfig = {
    video: {
        icon: "🎬",
        gradient: "from-rose-500 via-pink-500 to-rose-600",
        gradientSoft: "from-rose-50 to-pink-50",
        badge: "bg-rose-100 text-rose-700",
        border: "border-rose-200",
        glow: "shadow-rose-500/25",
        ring: "ring-rose-400/50",
        text: "text-rose-600",
    },
    manual: {
        icon: "📚",
        gradient: "from-blue-500 via-cyan-500 to-blue-600",
        gradientSoft: "from-blue-50 to-cyan-50",
        badge: "bg-blue-100 text-blue-700",
        border: "border-blue-200",
        glow: "shadow-blue-500/25",
        ring: "ring-blue-400/50",
        text: "text-blue-600",
    },
    guia: {
        icon: "🧭",
        gradient: "from-emerald-500 via-teal-500 to-emerald-600",
        gradientSoft: "from-emerald-50 to-teal-50",
        badge: "bg-emerald-100 text-emerald-700",
        border: "border-emerald-200",
        glow: "shadow-emerald-500/25",
        ring: "ring-emerald-400/50",
        text: "text-emerald-600",
    },
    post: {
        icon: "📝",
        gradient: "from-amber-500 via-orange-500 to-amber-600",
        gradientSoft: "from-amber-50 to-orange-50",
        badge: "bg-amber-100 text-amber-700",
        border: "border-amber-200",
        glow: "shadow-amber-500/25",
        ring: "ring-amber-400/50",
        text: "text-amber-600",
    },
    triptico: {
        icon: "🎨",
        gradient: "from-violet-500 via-purple-500 to-violet-600",
        gradientSoft: "from-violet-50 to-purple-50",
        badge: "bg-violet-100 text-violet-700",
        border: "border-violet-200",
        glow: "shadow-violet-500/25",
        ring: "ring-violet-400/50",
        text: "text-violet-600",
    },
    "avisos importantes": {
        icon: "⚠️",
        gradient: "from-red-500 via-orange-500 to-red-600",
        gradientSoft: "from-red-50 to-orange-50",
        badge: "bg-red-100 text-red-700",
        border: "border-red-200",
        glow: "shadow-red-500/25",
        ring: "ring-red-400/50",
        text: "text-red-600",
    },
    default: {
        icon: "📦",
        gradient: "from-gray-500 via-slate-500 to-gray-600",
        gradientSoft: "from-gray-50 to-slate-50",
        badge: "bg-gray-100 text-gray-700",
        border: "border-gray-200",
        glow: "shadow-gray-500/25",
        ring: "ring-gray-400/50",
        text: "text-gray-600",
    },
};

const getTipoConfig = (tipo) => tipoConfig[tipo] || tipoConfig.default;

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
// 🔄 CARGA DE DATOS - SOPORTE PARA MÚLTIPLES FILTROS
// ─────────────────────────────────────────────────────────────
const loadData = async () => {
    loading.value = true;
    error.value = null;

    try {
        const params = {
            search: searchDebounced.value,
            sort: sortBy.value,
        };

        // SOPORTE PARA MÚLTIPLES FILTROS
        if (activeTypeFilters.value.size > 0) {
            // Convertir Set a string separado por comas
            // Ejemplo: ['video', 'manual'] -> "video,manual"
            params.tipos = Array.from(activeTypeFilters.value).join(",");
        }

        console.log("Parámetros enviados:", params);

        const { data } = await axios.get("/recursos", { params });
        recursos.value = data.data || [];
        pagination.value = data;

        // Actualizar estadísticas
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
        case "recent":
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

const copyToClipboard = async (text) => {
    try {
        await navigator.clipboard.writeText(text);
        const toast = document.createElement("div");
        toast.textContent = "✅ Enlace copiado";
        toast.className =
            "fixed bottom-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50 animate-fade-in-out";
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 2000);
    } catch (err) {
        console.error("Error al copiar:", err);
    }
};

// Función para filtrar por tipo de recurso (SOPORTA MÚLTIPLES)
const toggleTypeFilter = (tipo) => {
    if (activeTypeFilters.value.has(tipo)) {
        activeTypeFilters.value.delete(tipo);
    } else {
        activeTypeFilters.value.add(tipo);
    }
    loadData();
};

// Función para limpiar todos los filtros
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
    loadData();
});

// Watch que incluye activeTypeFilters para múltiples filtros
watch([searchDebounced, sortBy, activeTypeFilters], () => {
    loadData();
});
</script>

<template>
    <AppLayout
        :title="`Recursos • ${props.tipo?.toUpperCase() || 'BIBLIOTECA'}`"
    >
        <!-- 🎨 FONDO ANIMADO -->
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
            <!-- ═══════════════════════════════════════════════ -->
            <!-- 📌 HEADER - DESIGN PREMIUM COMPLETO -->
            <!-- ═══════════════════════════════════════════════ -->
            <header class="mb-8">
                <div
                    class="relative overflow-hidden rounded-2xl bg-white/95 dark:bg-gray-800/95 backdrop-blur-xl border border-gray-200/50 dark:border-gray-700/50 shadow-xl shadow-gray-200/30 dark:shadow-gray-900/30 transition-all duration-300 hover:shadow-2xl"
                >
                    <!-- Fondo decorativo con gradientes dinámicos -->
                    <div
                        class="absolute inset-0 opacity-40 pointer-events-none"
                    >
                        <div
                            class="absolute -top-32 -right-32 w-96 h-96 bg-gradient-to-br from-indigo-400/20 to-purple-400/20 rounded-full blur-3xl animate-pulse"
                        ></div>
                        <div
                            class="absolute -bottom-32 -left-32 w-96 h-96 bg-gradient-to-br from-pink-400/20 to-rose-400/20 rounded-full blur-3xl animate-pulse"
                            style="animation-delay: 1s"
                        ></div>
                        <div
                            class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-64 h-64 bg-gradient-to-r from-indigo-500/5 to-purple-500/5 rounded-full blur-2xl"
                        ></div>
                    </div>

                    <div class="relative p-5 sm:p-6 lg:p-7">
                        <!-- Fila 1: Icono + Título y Controles -->
                        <div
                            class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between"
                        >
                            <!-- Sección Izquierda: Icono + Título -->
                            <div class="flex items-center gap-4">
                                <div class="group relative">
                                    <div
                                        class="absolute -inset-1 rounded-xl bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 opacity-0 blur-lg transition-all duration-500 group-hover:opacity-60 group-hover:blur-md"
                                    ></div>
                                    <div
                                        class="relative flex h-14 w-14 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 text-2xl text-white shadow-lg shadow-indigo-500/25 transition-all duration-300 group-hover:scale-105 group-hover:rotate-3 group-hover:shadow-xl"
                                    >
                                        📚
                                    </div>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h1
                                            class="text-2xl sm:text-3xl lg:text-4xl font-bold bg-gradient-to-r from-gray-900 to-gray-700 dark:from-gray-100 dark:to-gray-300 bg-clip-text text-transparent"
                                        >
                                            {{
                                                activeTypeFilters.size > 0
                                                    ? `Recursos Filtrados (${activeTypeFilters.size})`
                                                    : "Todos los Recursos"
                                            }}
                                        </h1>
                                        <span
                                            class="hidden sm:inline-flex h-2 w-2 rounded-full bg-emerald-500 animate-pulse shadow-lg shadow-emerald-500/50"
                                        ></span>
                                    </div>
                                    <p
                                        class="text-sm text-gray-500 dark:text-gray-400 mt-0.5"
                                    >
                                        {{
                                            activeTypeFilters.size > 0
                                                ? `Mostrando recursos de tipo: ${Array.from(
                                                      activeTypeFilters,
                                                  )
                                                      .map((t) =>
                                                          t ===
                                                          "avisos importantes"
                                                              ? "Avisos"
                                                              : t
                                                                    .charAt(0)
                                                                    .toUpperCase() +
                                                                t.slice(1),
                                                      )
                                                      .join(", ")}`
                                                : "Explora todos los recursos disponibles en la biblioteca"
                                        }}
                                    </p>
                                </div>
                            </div>

                            <!-- Sección Derecha: Controles Premium -->
                            <div class="flex flex-wrap items-center gap-2">
                                <!-- Vista Completa | Lista | Cuadrícula -->
                                <div
                                    class="flex bg-gray-100/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-xl p-1 shadow-inner"
                                >
                                    <button
                                        @click="viewMode = 'complete'"
                                        :class="[
                                            'px-3 py-1.5 rounded-lg text-sm font-medium transition-all duration-200 flex items-center gap-1.5',
                                            viewMode === 'complete'
                                                ? 'bg-white dark:bg-gray-700 text-indigo-600 dark:text-indigo-400 shadow-sm ring-1 ring-indigo-500/20'
                                                : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 hover:bg-white/50 dark:hover:bg-gray-700/50',
                                        ]"
                                        type="button"
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
                                                d="M5 3v4M3 5h4M6 17v4M4 19h4M13 3v4M11 5h4M14 17v4M12 19h4M19 3v4M17 5h4M20 17v4M18 19h4"
                                            />
                                        </svg>
                                        <span>Completa</span>
                                    </button>
                                    <button
                                        @click="viewMode = 'list'"
                                        :class="[
                                            'px-3 py-1.5 rounded-lg text-sm font-medium transition-all duration-200 flex items-center gap-1.5',
                                            viewMode === 'list'
                                                ? 'bg-white dark:bg-gray-700 text-indigo-600 dark:text-indigo-400 shadow-sm ring-1 ring-indigo-500/20'
                                                : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 hover:bg-white/50 dark:hover:bg-gray-700/50',
                                        ]"
                                        type="button"
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
                                                d="M4 6h16M4 12h16M4 18h16"
                                            />
                                        </svg>
                                        <span>Lista</span>
                                    </button>
                                    <button
                                        @click="viewMode = 'grid'"
                                        :class="[
                                            'px-3 py-1.5 rounded-lg text-sm font-medium transition-all duration-200 flex items-center gap-1.5',
                                            viewMode === 'grid'
                                                ? 'bg-white dark:bg-gray-700 text-indigo-600 dark:text-indigo-400 shadow-sm ring-1 ring-indigo-500/20'
                                                : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 hover:bg-white/50 dark:hover:bg-gray-700/50',
                                        ]"
                                        type="button"
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
                                                d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"
                                            />
                                        </svg>
                                        <span>Cuadrícula</span>
                                    </button>
                                </div>

                                <!-- Selector de Orden -->
                                <div class="relative group">
                                    <select
                                        v-model="sortBy"
                                        class="appearance-none bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl px-3 py-1.5 pr-8 text-sm text-gray-700 dark:text-gray-300 cursor-pointer focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200 hover:border-indigo-300 dark:hover:border-indigo-600"
                                    >
                                        <option value="recent">
                                            🕐 Recientes
                                        </option>
                                        <option value="title">🔤 A-Z</option>
                                        <option value="format">
                                            📁 Formato
                                        </option>
                                    </select>
                                    <svg
                                        class="absolute right-2 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400 pointer-events-none transition-transform duration-200 group-hover:rotate-180"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M19 9l-7 7-7-7"
                                        />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- Separador decorativo -->
                        <div
                            class="my-5 h-px bg-gradient-to-r from-transparent via-indigo-200 dark:via-indigo-800 to-transparent"
                        ></div>

                        <!-- Fila 2: Search Bar Premium -->
                        <div class="relative">
                            <div
                                :class="[
                                    'absolute -inset-0.5 rounded-xl bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 opacity-0 blur-xl transition-all duration-500',
                                    isSearchFocused
                                        ? 'opacity-40 blur-2xl scale-105'
                                        : '',
                                ]"
                            ></div>

                            <div
                                :class="[
                                    'relative flex items-center rounded-xl bg-white dark:bg-gray-800 transition-all duration-300 shadow-sm',
                                    isSearchFocused
                                        ? 'ring-2 ring-indigo-500/50 ring-offset-2 ring-offset-white dark:ring-offset-gray-900 shadow-xl shadow-indigo-500/10'
                                        : 'border border-gray-200 dark:border-gray-700 hover:border-indigo-300 dark:hover:border-indigo-600 hover:shadow-md',
                                ]"
                            >
                                <div class="pl-4 pr-2">
                                    <div
                                        :class="[
                                            'flex h-10 w-10 items-center justify-center rounded-lg transition-all duration-300',
                                            isSearchFocused
                                                ? 'bg-gradient-to-br from-indigo-500 to-purple-500 text-white shadow-md scale-110'
                                                : 'text-gray-400 dark:text-gray-500',
                                        ]"
                                    >
                                        <svg
                                            class="h-5 w-5 transition-transform duration-300"
                                            :class="
                                                isSearchFocused
                                                    ? 'scale-110'
                                                    : ''
                                            "
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
                                    placeholder="Buscar recursos por título, descripción, formato o etiquetas..."
                                    class="w-full bg-transparent py-3.5 pr-3 text-sm text-gray-900 dark:text-gray-100 placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:outline-none"
                                    aria-label="Buscar recursos"
                                />

                                <transition name="fade-scale">
                                    <button
                                        v-if="search"
                                        @click="clearSearch"
                                        class="mr-2 rounded-lg p-1.5 text-gray-400 transition-all duration-200 hover:bg-red-50 dark:hover:bg-red-900/30 hover:text-red-500 hover:scale-110"
                                        type="button"
                                        aria-label="Limpiar búsqueda"
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
                                </transition>

                                <div
                                    class="hidden h-6 w-px bg-gradient-to-b from-transparent via-gray-300 dark:via-gray-600 to-transparent sm:block"
                                ></div>
                                <div
                                    class="hidden items-center gap-1.5 px-3 sm:flex"
                                >
                                    <span
                                        class="text-xs text-gray-400 dark:text-gray-500"
                                        >⌘</span
                                    >
                                    <kbd
                                        class="inline-flex h-6 w-6 items-center justify-center rounded-md border border-gray-300 dark:border-gray-600 bg-gradient-to-b from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-800 text-xs font-semibold text-gray-600 dark:text-gray-400 shadow-sm"
                                    >
                                        K
                                    </kbd>
                                </div>
                            </div>

                            <div
                                v-if="loading && searchDebounced"
                                class="absolute -bottom-1 left-0 right-0 h-0.5 overflow-hidden rounded-full"
                            >
                                <div
                                    class="h-full animate-progress bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 rounded-full"
                                ></div>
                            </div>

                            <transition name="slide-down">
                                <div
                                    v-if="
                                        isSearchFocused &&
                                        searchSuggestions.length > 0
                                    "
                                    class="absolute z-50 mt-2 w-full rounded-xl border border-gray-200 dark:border-gray-700 bg-white/95 dark:bg-gray-800/95 backdrop-blur-sm shadow-2xl shadow-gray-200/50 dark:shadow-gray-900/50 overflow-hidden"
                                >
                                    <div
                                        class="flex items-center justify-between px-4 py-2.5 border-b border-gray-100 dark:border-gray-700 bg-gradient-to-r from-gray-50 to-white dark:from-gray-800 dark:to-gray-800"
                                    >
                                        <div class="flex items-center gap-2">
                                            <svg
                                                class="h-3.5 w-3.5 text-indigo-500"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M13 10V3L4 14h7v7l9-11h-7z"
                                                />
                                            </svg>
                                            <p
                                                class="text-xs font-medium text-gray-500 dark:text-gray-400"
                                            >
                                                Sugerencias para "{{ search }}"
                                            </p>
                                        </div>
                                        <span class="text-[10px] text-gray-400"
                                            >{{
                                                searchSuggestions.length
                                            }}
                                            resultados</span
                                        >
                                    </div>
                                    <ul class="max-h-64 overflow-y-auto py-1">
                                        <li
                                            v-for="s in searchSuggestions"
                                            :key="s.id"
                                            class="group px-4 py-2.5 hover:bg-gradient-to-r hover:from-indigo-50 hover:to-purple-50 dark:hover:from-indigo-950/30 dark:hover:to-purple-950/30 cursor-pointer transition-all duration-200 flex items-center gap-3"
                                            @click="selectSuggestion(s)"
                                        >
                                            <div
                                                :class="[
                                                    'flex h-8 w-8 items-center justify-center rounded-lg text-sm shadow-sm bg-gradient-to-br text-white transition-transform duration-200 group-hover:scale-105',
                                                    getTipoConfig(
                                                        s.tipo_material,
                                                    )?.gradient,
                                                ]"
                                            >
                                                {{
                                                    getTipoConfig(
                                                        s.tipo_material,
                                                    )?.icon
                                                }}
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div
                                                    class="flex items-center gap-2"
                                                >
                                                    <p
                                                        class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors"
                                                    >
                                                        {{ s.titulo }}
                                                    </p>
                                                    <span
                                                        :class="[
                                                            'px-1.5 py-0.5 rounded text-[10px] font-medium',
                                                            getTipoConfig(
                                                                s.tipo_material,
                                                            )?.badge,
                                                        ]"
                                                    >
                                                        {{ s.tipo_material }}
                                                    </span>
                                                </div>
                                                <div
                                                    class="flex items-center gap-2 mt-0.5"
                                                >
                                                    <span
                                                        class="text-xs text-gray-500 dark:text-gray-400"
                                                    >
                                                        {{
                                                            s.formato ||
                                                            "Sin formato"
                                                        }}
                                                    </span>
                                                    <span
                                                        class="text-gray-300 dark:text-gray-600"
                                                        >•</span
                                                    >
                                                    <span
                                                        class="text-xs text-gray-400"
                                                    >
                                                        {{
                                                            formatDate(
                                                                s.created_at,
                                                            )
                                                        }}
                                                    </span>
                                                </div>
                                            </div>
                                            <svg
                                                class="h-4 w-4 text-gray-400 opacity-0 group-hover:opacity-100 transition-all duration-200 group-hover:translate-x-0.5"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M9 5l7 7-7 7"
                                                />
                                            </svg>
                                        </li>
                                    </ul>
                                    <div
                                        class="px-4 py-2 border-t border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50"
                                    >
                                        <p
                                            class="text-[11px] text-gray-400 text-center"
                                        >
                                            Presiona
                                            <kbd
                                                class="px-1.5 py-0.5 rounded bg-gray-200 dark:bg-gray-700 text-xs font-mono"
                                                >Enter</kbd
                                            >
                                            para ver todos los resultados
                                        </p>
                                    </div>
                                </div>
                            </transition>
                        </div>

                        <!-- Fila 3: Filtros y Estadísticas Premium - MÚLTIPLES SELECCIONES -->
                        <div
                            class="mt-5 flex flex-wrap items-center justify-between gap-3"
                        >
                            <div class="flex flex-wrap items-center gap-2">
                                <span
                                    class="text-xs font-medium text-gray-500 dark:text-gray-400"
                                    >Filtrar por tipo:</span
                                >

                                <!-- Botones de filtro - AHORA CON MÚLTIPLES SELECCIONES -->
                                <button
                                    v-for="tipo in tiposDisponibles"
                                    :key="tipo.value"
                                    @click="toggleTypeFilter(tipo.value)"
                                    :class="[
                                        'px-2.5 py-1 rounded-full text-xs font-medium transition-all duration-200 flex items-center gap-1',
                                        activeTypeFilters.has(tipo.value)
                                            ? 'bg-gradient-to-r from-indigo-500 to-purple-500 text-white shadow-md shadow-indigo-500/25 scale-105'
                                            : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-600 hover:scale-105',
                                    ]"
                                    type="button"
                                >
                                    <span class="text-sm">{{ tipo.icon }}</span>
                                    <span>{{ tipo.label }}</span>
                                </button>

                                <!-- Botón limpiar filtros -->
                                <button
                                    v-if="activeTypeFilters.size > 0"
                                    @click="clearTypeFilters"
                                    class="px-2.5 py-1 rounded-full text-xs font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-all duration-200 hover:scale-105"
                                >
                                    Limpiar todos
                                </button>
                            </div>

                            <div class="flex items-center gap-4 text-xs">
                                <!-- Badge de filtros activos -->
                                <div
                                    v-if="activeTypeFilters.size > 0"
                                    class="flex items-center gap-1.5 px-2 py-1 rounded-full bg-indigo-100 dark:bg-indigo-900/30"
                                >
                                    <svg
                                        class="h-3 w-3 text-indigo-600 dark:text-indigo-400"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"
                                        />
                                    </svg>
                                    <span
                                        class="text-xs font-medium text-indigo-600 dark:text-indigo-400"
                                        >{{ activeTypeFilters.size }} filtro{{
                                            activeTypeFilters.size > 1
                                                ? "s"
                                                : ""
                                        }}
                                        activo{{
                                            activeTypeFilters.size > 1
                                                ? "s"
                                                : ""
                                        }}</span
                                    >
                                </div>

                                <!-- Badge de todos los recursos -->
                                <div
                                    v-else
                                    class="flex items-center gap-1.5 px-2 py-1 rounded-full bg-emerald-100 dark:bg-emerald-900/30"
                                >
                                    <svg
                                        class="h-3 w-3 text-emerald-600 dark:text-emerald-400"
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
                                    <span
                                        class="text-xs font-medium text-emerald-600 dark:text-emerald-400"
                                        >Todos los tipos</span
                                    >
                                </div>

                                <div
                                    class="flex items-center gap-1.5 group cursor-help"
                                    title="Recursos activos disponibles"
                                >
                                    <span class="relative flex h-2 w-2">
                                        <span
                                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"
                                        ></span>
                                        <span
                                            class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"
                                        ></span>
                                    </span>
                                    <span
                                        class="text-gray-600 dark:text-gray-400"
                                        >Activos:</span
                                    >
                                    <span
                                        class="font-semibold text-emerald-600 dark:text-emerald-400"
                                        >{{ stats.activos }}</span
                                    >
                                </div>

                                <div class="flex items-center gap-1.5">
                                    <span
                                        class="h-2 w-2 rounded-full bg-gray-300 dark:bg-gray-600"
                                    ></span>
                                    <span
                                        class="text-gray-600 dark:text-gray-400"
                                        >Totales:</span
                                    >
                                    <span
                                        class="font-semibold text-gray-900 dark:text-gray-100"
                                        >{{ stats.total }}</span
                                    >
                                </div>

                                <div
                                    class="flex items-center gap-1.5 text-gray-400 dark:text-gray-500"
                                >
                                    <svg
                                        class="h-3 w-3"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                        />
                                    </svg>
                                    <span>{{
                                        formatDate(recursos[0]?.created_at) ||
                                        "Ahora"
                                    }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- ═══════════════════════════════════════════════ -->
            <!-- ⚠️ ERROR STATE -->
            <!-- ═══════════════════════════════════════════════ -->
            <div
                v-if="error"
                class="mb-8 rounded-2xl border border-red-200 bg-red-50 p-4 text-red-700 flex items-start gap-3"
                role="alert"
            >
                <svg
                    class="h-5 w-5 flex-shrink-0 mt-0.5"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                    />
                </svg>
                <div>
                    <p class="font-medium">Oops, algo salió mal</p>
                    <p class="text-sm">{{ error }}</p>
                    <button
                        @click="loadData"
                        class="mt-2 text-sm font-semibold text-red-800 hover:underline"
                    >
                        Reintentar
                    </button>
                </div>
            </div>

            <!-- ═══════════════════════════════════════════════ -->
            <!-- ⏳ SKELETON LOADING -->
            <!-- ═══════════════════════════════════════════════ -->
            <div
                v-if="loading"
                class="space-y-3"
                aria-busy="true"
                aria-live="polite"
            >
                <div
                    v-for="i in 5"
                    :key="`sk-${i}`"
                    class="animate-pulse rounded-2xl border border-gray-100 bg-white p-4"
                >
                    <div class="flex items-center gap-4">
                        <div class="h-10 w-10 rounded-xl bg-gray-200"></div>
                        <div class="flex-1 space-y-3">
                            <div class="h-4 w-1/3 rounded bg-gray-200"></div>
                            <div class="h-3 w-full rounded bg-gray-100"></div>
                        </div>
                        <div class="flex gap-2">
                            <div
                                class="h-6 w-16 rounded-full bg-gray-100"
                            ></div>
                            <div
                                class="h-6 w-20 rounded-full bg-gray-100"
                            ></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ═══════════════════════════════════════════════ -->
            <!-- 📊 VISTA DE TABLA (para Lista y Completa) -->
            <!-- ═══════════════════════════════════════════════ -->
            <div
                v-if="
                    (viewMode === 'list' || viewMode === 'complete') &&
                    recursosFiltrados.length > 0
                "
                class="overflow-hidden rounded-2xl border border-gray-200/80 bg-white/90 shadow-sm backdrop-blur-sm"
            >
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead
                            class="bg-gradient-to-r from-gray-50 to-gray-100/80"
                        >
                            <tr>
                                <th
                                    scope="col"
                                    class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500"
                                >
                                    ID
                                </th>
                                <th
                                    scope="col"
                                    class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500"
                                >
                                    Recurso
                                </th>
                                <th
                                    scope="col"
                                    class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 hidden sm:table-cell"
                                >
                                    Tipo
                                </th>
                                <th
                                    scope="col"
                                    class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 hidden md:table-cell"
                                >
                                    Formato
                                </th>
                                <th
                                    scope="col"
                                    class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider text-gray-500"
                                >
                                    Estado
                                </th>
                                <th
                                    scope="col"
                                    class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-gray-500"
                                >
                                    Acciones
                                </th>
                            </tr>
                        </thead>

                        <TransitionGroup
                            name="table-row"
                            tag="tbody"
                            class="divide-y divide-gray-100"
                        >
                            <tr
                                v-for="r in recursosFiltrados"
                                :key="`tbl-${r.id}`"
                                @mouseenter="hoveredRow = `tbl-${r.id}`"
                                @mouseleave="hoveredRow = null"
                                :class="[
                                    'group transition-all duration-300',
                                    hoveredRow === `tbl-${r.id}`
                                        ? 'bg-gradient-to-r ' +
                                          getTipoConfig(r.tipo_material)
                                              ?.gradientSoft +
                                          ' shadow-inner'
                                        : 'hover:bg-gray-50/80',
                                ]"
                            >
                                <td
                                    class="px-6 py-4 text-sm text-gray-500 font-mono"
                                >
                                    {{ r.id }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-4">
                                        <div class="relative flex-shrink-0">
                                            <div
                                                :class="[
                                                    'absolute -inset-1 rounded-xl blur opacity-30 transition-opacity bg-gradient-to-br',
                                                    getTipoConfig(
                                                        r.tipo_material,
                                                    )?.gradient,
                                                    hoveredRow === `tbl-${r.id}`
                                                        ? 'opacity-50 scale-110'
                                                        : '',
                                                ]"
                                            ></div>
                                            <div
                                                :class="[
                                                    'relative flex h-10 w-10 items-center justify-center rounded-xl text-lg shadow-sm text-white bg-gradient-to-br',
                                                    getTipoConfig(
                                                        r.tipo_material,
                                                    )?.gradient,
                                                ]"
                                            >
                                                {{
                                                    getTipoConfig(
                                                        r.tipo_material,
                                                    )?.icon
                                                }}
                                            </div>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p
                                                :class="[
                                                    'font-semibold text-gray-900 truncate transition-colors',
                                                    hoveredRow === `tbl-${r.id}`
                                                        ? getTipoConfig(
                                                              r.tipo_material,
                                                          )?.text
                                                        : '',
                                                ]"
                                                :title="r.titulo"
                                            >
                                                {{ r.titulo }}
                                            </p>
                                            <p
                                                v-if="r.descripcion"
                                                class="mt-0.5 text-xs text-gray-500 line-clamp-1"
                                                :title="r.descripcion"
                                            >
                                                {{ r.descripcion }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 hidden sm:table-cell">
                                    <span
                                        :class="[
                                            'inline-flex items-center rounded-lg px-3 py-1 text-xs font-semibold transition-all',
                                            getTipoConfig(r.tipo_material)
                                                ?.badge,
                                            hoveredRow === `tbl-${r.id}`
                                                ? 'shadow-sm scale-105'
                                                : '',
                                        ]"
                                    >
                                        {{ r.tipo_material }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 hidden md:table-cell">
                                    <span
                                        class="inline-flex items-center gap-1.5 rounded-lg bg-gray-100 px-3 py-1 text-xs font-medium text-gray-700"
                                    >
                                        <svg
                                            class="h-3 w-3"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                            />
                                        </svg>
                                        {{ r.formato || "N/D" }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span
                                        :class="[
                                            'inline-flex items-center justify-center rounded-full px-3 py-1 text-xs font-semibold ring-1 ring-inset',
                                            r.estado === 'activo'
                                                ? 'bg-emerald-100 text-emerald-700 ring-emerald-600/20'
                                                : 'bg-gray-100 text-gray-600 ring-gray-600/20',
                                        ]"
                                    >
                                        <span
                                            :class="[
                                                'mr-1.5 h-1.5 w-1.5 rounded-full',
                                                r.estado === 'activo'
                                                    ? 'bg-emerald-500 animate-pulse'
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
                                        class="flex items-center justify-end gap-2"
                                    >
                                        <a
                                            v-if="r.url"
                                            :href="r.url"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            @click.stop
                                            :class="[
                                                'inline-flex items-center justify-center rounded-lg p-2 transition-all',
                                                hoveredRow === `tbl-${r.id}`
                                                    ? 'bg-gradient-to-r ' +
                                                      getTipoConfig(
                                                          r.tipo_material,
                                                      )?.gradient +
                                                      ' text-white shadow-md scale-110'
                                                    : 'text-gray-400 hover:text-indigo-600 hover:bg-indigo-50',
                                            ]"
                                            title="Abrir recurso"
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
                                                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"
                                                />
                                            </svg>
                                        </a>
                                        <button
                                            v-if="r.url"
                                            @click.stop="copyToClipboard(r.url)"
                                            type="button"
                                            :class="[
                                                'inline-flex items-center justify-center rounded-lg p-2 transition-all',
                                                hoveredRow === `tbl-${r.id}`
                                                    ? 'bg-gray-900 text-white shadow-md scale-110'
                                                    : 'text-gray-400 hover:text-gray-700 hover:bg-gray-100',
                                            ]"
                                            title="Copiar enlace"
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
                                                    d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"
                                                />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </TransitionGroup>
                    </table>
                </div>

                <div
                    class="px-6 py-3 border-t border-gray-200 bg-gray-50/50 flex items-center justify-between text-xs text-gray-500"
                >
                    <span>
                        Mostrando
                        <strong class="text-gray-700">{{
                            recursosFiltrados.length
                        }}</strong>
                        de
                        <strong class="text-gray-700">{{
                            recursos.length
                        }}</strong>
                        recursos
                    </span>
                    <div class="flex items-center gap-4">
                        <span class="flex items-center gap-1">
                            <span
                                class="w-2 h-2 rounded-full bg-emerald-500"
                            ></span>
                            Activo
                        </span>
                        <span class="flex items-center gap-1">
                            <span
                                class="w-2 h-2 rounded-full bg-gray-300"
                            ></span>
                            Inactivo
                        </span>
                    </div>
                </div>
            </div>

            <!-- ═══════════════════════════════════════════════ -->
            <!-- 🧩 VISTA DE CUADRÍCULA -->
            <!-- ═══════════════════════════════════════════════ -->
            <TransitionGroup
                v-else-if="viewMode === 'grid' && recursosFiltrados.length > 0"
                name="grid"
                tag="div"
                class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3"
            >
                <article
                    v-for="r in recursosFiltrados"
                    :key="`g-${r.id}`"
                    class="group relative cursor-pointer overflow-hidden rounded-3xl border border-gray-200/80 bg-white/90 backdrop-blur-sm transition-all duration-500 hover:border-transparent hover:shadow-2xl hover:-translate-y-2 hover:shadow-indigo-500/15"
                >
                    <div
                        :class="[
                            'relative h-3 bg-gradient-to-r transition-all duration-500 group-hover:opacity-100',
                            getTipoConfig(r.tipo_material)?.gradient,
                        ]"
                    ></div>
                    <div class="p-7">
                        <div class="relative mb-5 inline-block">
                            <div
                                :class="[
                                    'absolute -inset-1 rounded-xl blur-xl opacity-30 transition-all duration-500 group-hover:opacity-50 group-hover:scale-110 bg-gradient-to-br',
                                    getTipoConfig(r.tipo_material)?.gradient,
                                ]"
                            ></div>
                            <div
                                :class="[
                                    'relative flex h-14 w-14 items-center justify-center rounded-xl text-2xl text-white shadow-lg transition-transform duration-300 group-hover:scale-110 bg-gradient-to-br',
                                    getTipoConfig(r.tipo_material)?.gradient,
                                ]"
                            >
                                {{ getTipoConfig(r.tipo_material)?.icon }}
                            </div>
                        </div>
                        <h3
                            class="line-clamp-2 min-h-[3.5rem] text-lg font-bold text-gray-900 transition-all duration-300 group-hover:bg-gradient-to-r group-hover:from-indigo-500 group-hover:via-purple-500 group-hover:to-pink-500 group-hover:bg-clip-text group-hover:text-transparent"
                        >
                            {{ r.titulo }}
                        </h3>
                        <p
                            v-if="r.descripcion"
                            class="mt-4 line-clamp-3 text-sm text-gray-600"
                        >
                            {{ r.descripcion }}
                        </p>
                        <div class="mt-5 flex flex-wrap gap-2">
                            <span
                                :class="[
                                    'rounded-xl px-3 py-1.5 text-xs font-semibold transition-all group-hover:shadow-md group-hover:scale-105',
                                    getTipoConfig(r.tipo_material)?.badge,
                                ]"
                                >{{ r.tipo_material }}</span
                            >
                            <span
                                class="rounded-xl bg-gray-100 px-3 py-1.5 text-xs font-semibold text-gray-700 transition-all group-hover:shadow-md group-hover:scale-105 group-hover:bg-gray-200"
                                >{{ r.formato }}</span
                            >
                        </div>
                        <div
                            class="mt-6 flex items-center justify-between border-t border-gray-100 pt-5"
                        >
                            <span
                                :class="[
                                    'flex items-center gap-2 text-xs font-semibold',
                                    r.estado === 'activo'
                                        ? 'text-emerald-600'
                                        : 'text-gray-400',
                                ]"
                            >
                                <span
                                    :class="[
                                        'h-2.5 w-2.5 rounded-full',
                                        r.estado === 'activo'
                                            ? 'bg-emerald-500 animate-pulse'
                                            : 'bg-gray-300',
                                    ]"
                                ></span>
                                {{
                                    r.estado === "activo"
                                        ? "Activo"
                                        : "Inactivo"
                                }}
                            </span>
                            <div class="flex gap-2">
                                <a
                                    v-if="r.url"
                                    :href="r.url"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    @click.stop
                                    class="inline-flex items-center gap-1.5 text-sm font-semibold text-indigo-600 transition-all group-hover:text-indigo-700 group-hover:underline group-hover:underline-offset-4"
                                    >Ver
                                    <svg
                                        class="h-4 w-4 transition-transform group-hover:translate-x-1"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M9 5l7 7-7 7"
                                        />
                                    </svg>
                                </a>
                                <button
                                    v-if="r.url"
                                    @click.stop="copyToClipboard(r.url)"
                                    class="text-gray-400 hover:text-gray-600 transition-colors"
                                    title="Copiar enlace"
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
                                            d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"
                                        />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </article>
            </TransitionGroup>

            <!-- ═══════════════════════════════════════════════ -->
            <!-- 📭 EMPTY STATE -->
            <!-- ═══════════════════════════════════════════════ -->
            <div
                v-else-if="!loading && recursosFiltrados.length === 0"
                class="relative overflow-hidden rounded-3xl border-2 border-dashed border-gray-200 bg-gradient-to-br from-gray-50 to-white py-24 text-center"
            >
                <div class="absolute inset-0 overflow-hidden">
                    <div
                        class="absolute -top-20 -right-20 h-40 w-40 rounded-full bg-indigo-100/50 blur-2xl"
                    ></div>
                    <div
                        class="absolute -bottom-20 -left-20 h-40 w-40 rounded-full bg-purple-100/50 blur-2xl"
                    ></div>
                </div>
                <div class="relative">
                    <div class="relative mx-auto inline-block">
                        <div
                            class="absolute -inset-4 rounded-full bg-gradient-to-r from-indigo-400/20 to-purple-400/20 blur-2xl"
                        ></div>
                        <div
                            class="relative mx-auto flex h-28 w-28 items-center justify-center rounded-full bg-gradient-to-br from-white to-gray-100 shadow-xl"
                        >
                            <svg
                                class="h-14 w-14 text-gray-400"
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
                    </div>
                    <h3 class="mt-8 text-2xl font-bold text-gray-900">
                        No hay recursos
                    </h3>
                    <p class="mx-auto mt-3 max-w-md text-gray-600">
                        {{
                            search || activeTypeFilters.size > 0
                                ? `No encontramos resultados para tu búsqueda${search ? ` "${search}"` : ""}${activeTypeFilters.size > 0 ? " con los filtros aplicados" : ""}`
                                : "No hay recursos disponibles en la biblioteca"
                        }}
                    </p>
                    <button
                        v-if="search || activeTypeFilters.size > 0"
                        @click="clearSearch"
                        type="button"
                        class="mt-8 inline-flex items-center gap-2 rounded-2xl bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-3 font-semibold text-white shadow-lg transition-all hover:shadow-xl hover:scale-105 active:scale-95"
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
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
                            />
                        </svg>
                        Limpiar búsqueda y filtros
                    </button>
                </div>
            </div>

            <!-- ═══════════════════════════════════════════════ -->
            <!-- 📄 PAGINACIÓN -->
            <!-- ═══════════════════════════════════════════════ -->
            <nav
                v-if="pagination?.links?.length > 1"
                class="mt-16 flex justify-center"
                aria-label="Paginación"
            >
                <div
                    class="inline-flex items-center gap-2 rounded-2xl border border-gray-200 bg-white p-2 shadow-lg backdrop-blur-sm"
                >
                    <button
                        v-for="(link, i) in pagination.links"
                        :key="`p-${i}`"
                        @click="link.url && loadDataFromUrl(link.url)"
                        :disabled="!link.url || link.active"
                        :class="[
                            'min-w-[44px] rounded-xl px-4 py-2.5 text-sm font-semibold transition-all',
                            link.active
                                ? 'bg-gradient-to-r from-indigo-500 to-purple-500 text-white shadow-md scale-105'
                                : 'text-gray-700 hover:bg-gray-100',
                            !link.url || link.active
                                ? 'cursor-not-allowed opacity-40'
                                : 'cursor-pointer hover:scale-105',
                        ]"
                        type="button"
                        v-html="link.label"
                    ></button>
                </div>
            </nav>
        </div>
    </AppLayout>
</template>

<style scoped>
/* ═══════════════════════════════════════════════ */
/* ✨ TRANSICIONES */
/* ═══════════════════════════════════════════════ */
.table-row-enter-active,
.table-row-leave-active {
    transition: all 0.3s ease;
}
.table-row-enter-from {
    opacity: 0;
    transform: translateX(-20px);
}
.table-row-leave-to {
    opacity: 0;
    transform: translateX(20px);
}

.grid-enter-active,
.grid-leave-active {
    transition: all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.grid-enter-from {
    opacity: 0;
    transform: scale(0.8) translateY(30px);
}
.grid-leave-to {
    opacity: 0;
    transform: scale(1.05) translateY(-10px);
}

.fade-scale-enter-active,
.fade-scale-leave-active {
    transition: all 0.2s ease;
}
.fade-scale-enter-from,
.fade-scale-leave-to {
    opacity: 0;
    transform: scale(0.8);
}

.slide-down-enter-active {
    transition: all 0.3s ease-out;
}
.slide-down-leave-active {
    transition: all 0.2s ease-in;
}
.slide-down-enter-from {
    opacity: 0;
    transform: translateY(-10px);
}
.slide-down-leave-to {
    opacity: 0;
    transform: translateY(-10px);
}

/* Line clamp */
.line-clamp-1,
.line-clamp-2,
.line-clamp-3 {
    display: -webkit-box;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.line-clamp-1 {
    -webkit-line-clamp: 1;
}
.line-clamp-2 {
    -webkit-line-clamp: 2;
}
.line-clamp-3 {
    -webkit-line-clamp: 3;
}

/* Animación de progreso */
@keyframes progress {
    0% {
        transform: translateX(-100%);
    }
    100% {
        transform: translateX(100%);
    }
}
.animate-progress {
    animation: progress 1s ease-in-out infinite;
}

/* Animación fade in/out para toast */
@keyframes fadeInOut {
    0% {
        opacity: 0;
        transform: translateY(20px);
    }
    10% {
        opacity: 1;
        transform: translateY(0);
    }
    90% {
        opacity: 1;
        transform: translateY(0);
    }
    100% {
        opacity: 0;
        transform: translateY(-20px);
    }
}
.animate-fade-in-out {
    animation: fadeInOut 2s ease-in-out forwards;
}

/* Scrollbar personalizado */
::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}
::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 9999px;
}
::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 9999px;
}
::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>
