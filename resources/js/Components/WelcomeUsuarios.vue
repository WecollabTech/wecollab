<script setup>
import { ref, computed, onMounted, watch } from "vue";
import { usePage, router } from "@inertiajs/vue3";

// ✅ Axios global configurado en bootstrap.js
const axios = window.axios;

// 🔷 ESTADO GLOBAL
const page = usePage();
const user = computed(() => page.props.auth?.user);
const tutoriales = ref([]);
const loading = ref(true);
const error = ref(null);

// 🔷 FILTROS Y UI
const search = ref("");
const searchDebounced = ref("");
const tipoSeleccionado = ref("todo");
const viewMode = ref(localStorage.getItem("tutorialViewMode") || "grid");
const showToast = ref(false);
const toastMessage = ref("");
const toastType = ref("success");

// 🔷 TIPOS DE MATERIAL (con iconos y rutas)
const tipos = [
    { label: "Todo", value: "todo", icon: "📚" },
    { label: "Video", value: "video", icon: "🎬" },
    { label: "Manual", value: "manual", icon: "📖" },
    { label: "Guía", value: "guia", icon: "🧭" },
    { label: "Tríptico", value: "infografia", icon: "🎨" },
];

// 🎯 ✅ ALCANCES VÁLIDOS EXACTOS DE TU TABLA 'roles' EN BD
const ALCANCES_VALIDOS = [
    "Superadmin we collab",
    "Admin we collab",
    "Soporte we collab",
    "Usuario we collab",
    "Suscriptor SLC",
    "Usuario Admin SLC",
    "Usuario Premium SLC",
    "Usuario Público",
    "Prospecto",
];

// 🔷 HELPER: Obtener rol limpio del usuario
const getUserRole = () => {
    const role = user.value?.role;
    if (!role) return "";
    if (typeof role === "object") {
        return (
            role.nombre?.toString() ||
            role.name?.toString() ||
            role.slug?.toString() ||
            role.role?.toString() ||
            ""
        ).trim();
    }
    return role.toString().trim();
};

// 🔷 HELPER: Normalizar para comparación case-insensitive
const normalize = (str) => str?.toString().toLowerCase().trim() || "";

// 🔐 ¿El usuario tiene acceso al tutorial?
const tieneAcceso = (tutorial) => {
    const rolUsuario = normalize(getUserRole());
    const alcanceTutorial = tutorial.alcance;

    // Validar que el alcance sea un valor válido de la BD
    if (alcanceTutorial && !ALCANCES_VALIDOS.includes(alcanceTutorial)) {
        console.warn(
            `⚠️ Alcance inválido en tutorial ${tutorial.id}: "${alcanceTutorial}"`,
        );
        return false;
    }

    // Contenido público (alcance vacío o null) → todos lo ven
    if (!alcanceTutorial || alcanceTutorial.trim() === "") return true;

    // Usuario sin rol → solo ve públicos
    if (!rolUsuario) return false;

    // Superadmin we collab → ve TODO el contenido
    if (rolUsuario === "superadmin we collab") return true;

    // Match exacto case-insensitive entre rol del usuario y alcance del recurso
    return normalize(rolUsuario) === normalize(alcanceTutorial);
};

// 🔐 ¿Está bloqueado para este usuario?
const estaBloqueado = (tutorial) => !tieneAcceso(tutorial);

// 🔷 Toast notifications
const mostrarNotificacion = (mensaje, tipo = "success") => {
    toastMessage.value = mensaje;
    toastType.value = tipo;
    showToast.value = true;
    setTimeout(() => {
        showToast.value = false;
    }, 3000);
};

// 🔷 Debounce para búsqueda (mejora rendimiento)
let searchTimeout;
watch(search, (newValue) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        searchDebounced.value = newValue;
    }, 300);
});

// 🔷 CONFIGURACIÓN DE TIPOS (para rutas dinámicas y badges)
const tipoConfig = {
    video: {
        icon: "🎬",
        gradient: "from-rose-500 via-pink-500 to-rose-600",
        badge: "bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300",
        route: "videos",
    },
    manual: {
        icon: "📚",
        gradient: "from-blue-500 via-cyan-500 to-blue-600",
        badge: "bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300",
        route: "manuales",
    },
    guia: {
        icon: "🧭",
        gradient: "from-emerald-500 via-teal-500 to-emerald-600",
        badge: "bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300",
        route: "guias",
    },
    post: {
        icon: "📝",
        gradient: "from-amber-500 via-orange-500 to-amber-600",
        badge: "bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300",
        route: "posts",
    },
    triptico: {
        icon: "🎨",
        gradient: "from-violet-500 via-purple-500 to-violet-600",
        badge: "bg-violet-100 text-violet-700 dark:bg-violet-900/40 dark:text-violet-300",
        route: "tripticos",
    },
    "avisos importantes": {
        icon: "⚠️",
        gradient: "from-red-500 via-orange-500 to-red-600",
        badge: "bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300",
        route: "avisos",
    },
    default: {
        icon: "📦",
        gradient: "from-gray-500 via-slate-500 to-gray-600",
        badge: "bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300",
        route: "todos",
    },
};

const getTipoConfig = (tipo) => tipoConfig[tipo] || tipoConfig.default;

// 🔷 CARGAR DATOS DESDE API
onMounted(async () => {
    try {
        error.value = null;
        loading.value = true;

        const res = await axios.get("/tutoriales", { timeout: 10000 });

        if (res.data?.data && Array.isArray(res.data.data)) {
            tutoriales.value = res.data.data;
        } else if (Array.isArray(res.data)) {
            tutoriales.value = res.data;
        } else {
            throw new Error("Respuesta de API con formato inesperado");
        }
    } catch (err) {
        console.error("❌ Error cargando tutoriales:", err);

        if (err.response?.status === 401) {
            error.value = "Tu sesión ha expirado. Redirigiendo...";
            mostrarNotificacion("Sesión expirada", "error");
            setTimeout(() => router.visit("/login"), 2000);
        } else if (err.response?.status === 403) {
            error.value = "No tienes permisos para acceder";
            mostrarNotificacion("Acceso denegado", "error");
        } else if (err.response?.status === 404) {
            error.value = "La ruta no está disponible";
            mostrarNotificacion("Recurso no encontrado", "error");
        } else {
            error.value = "Error de conexión. Intenta de nuevo.";
            mostrarNotificacion("Error al cargar contenidos", "error");
        }
    } finally {
        loading.value = false;
    }
});

// 🔷 FILTRADO COMPUTADO (muestra todos, frontend maneja UI del candado)
const filtrados = computed(() => {
    return tutoriales.value.filter((t) => {
        if (t.estado !== "activo") return false;

        const tipoOK =
            tipoSeleccionado.value === "todo" ||
            normalize(t.tipo_material) === normalize(tipoSeleccionado.value);
        if (!tipoOK) return false;

        const searchTerm = searchDebounced.value.toLowerCase().trim();
        const searchOK =
            !searchTerm ||
            t.titulo?.toLowerCase().includes(searchTerm) ||
            t.descripcion?.toLowerCase().includes(searchTerm);
        if (!searchOK) return false;

        // ✅ NO filtrar por alcance aquí: el frontend muestra 🔒
        return true;
    });
});

// 📊 Estadísticas computadas
const stats = computed(() => ({
    total: tutoriales.value.length,
    visibles: filtrados.value.length,
    bloqueados: filtrados.value.filter((t) => estaBloqueado(t)).length,
    tipos: new Set(tutoriales.value.map((t) => t.tipo_material)).size,
}));

// 🔷 THUMBNAIL DE YOUTUBE
const getThumbnail = (url) => {
    if (!url) return "/img/default.jpg";

    if (url.includes("youtube.com") || url.includes("youtu.be")) {
        let videoId = null;
        if (url.includes("youtu.be/")) {
            videoId = url.split("youtu.be/")[1]?.split(/[?&#]/)[0];
        } else if (url.includes("v=")) {
            videoId = url.split("v=")[1]?.split(/[?&#]/)[0];
        } else if (url.includes("/embed/")) {
            videoId = url.split("/embed/")[1]?.split(/[?&#]/)[0];
        }
        if (videoId && videoId.length === 11) {
            return `https://img.youtube.com/vi/${videoId}/hqdefault.jpg`;
        }
    }
    if (url.includes("vimeo.com")) {
        const match = url.match(/vimeo\.com\/(\d+)/);
        if (match) return `https://vumbnail.com/${match[1]}.jpg`;
    }
    return "/img/default.jpg";
};

// 🎨 ✅ BADGE COLORS POR ALCANCE (COINCIDEN EXACTAMENTE CON TU BD)
const getAlcanceBadgeClass = (alcance) => {
    const colors = {
        "Superadmin we collab":
            "bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-300",
        "Admin we collab":
            "bg-indigo-100 text-indigo-800 dark:bg-indigo-900/40 dark:text-indigo-300",
        "Soporte we collab":
            "bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300",
        "Usuario we collab":
            "bg-cyan-100 text-cyan-800 dark:bg-cyan-900/40 dark:text-cyan-300",
        "Suscriptor SLC":
            "bg-teal-100 text-teal-800 dark:bg-teal-900/40 dark:text-teal-300",
        "Usuario Admin SLC":
            "bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300",
        "Usuario Premium SLC":
            "bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300",
        "Usuario Público":
            "bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300",
        Prospecto:
            "bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-300",
    };
    return (
        colors[alcance] ||
        "bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300"
    );
};

// 🎨 Badge tipo material
const getTipoBadgeClass = (tipo) => {
    const colors = {
        video: "bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300",
        manual: "bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300",
        guia: "bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300",
        triptico:
            "bg-violet-100 text-violet-700 dark:bg-violet-900/40 dark:text-violet-300",
    };
    return (
        colors[tipo] ||
        "bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300"
    );
};

// 🔷 🎯 FUNCIÓN VER HÍBRIDA (Acceso por alcance + Ruta por tipo)
const verVideo = (recurso) => {
    // ✅ 1. Validar acceso por campo 'alcance'
    if (estaBloqueado(recurso)) {
        mostrarNotificacion(
            `🔒 Contenido restringido para rol: "${recurso.alcance}"`,
            "error",
        );
        return;
    }

    // ✅ 2. Obtener ruta según tipo_material
    const tipoRoute = getTipoConfig(recurso.tipo_material)?.route || "todos";

    // ✅ 3. Navegar a la ruta estructurada
    router.visit(`/recursos/${tipoRoute}/${recurso.id}`);
};

// 🔷 Cambiar modo de vista con persistencia
const cambiarVista = (modo) => {
    viewMode.value = modo;
    localStorage.setItem("tutorialViewMode", modo);
};

// 🔷 Clases para filtros
const filterClass = (tipo) =>
    tipoSeleccionado.value === tipo
        ? "filter filter-active"
        : "filter filter-inactive";

// 🔷 Reset filtros con feedback
const resetFiltros = () => {
    search.value = "";
    searchDebounced.value = "";
    tipoSeleccionado.value = "todo";
    mostrarNotificacion("Filtros limpiados", "success");
};
</script>

<template>
    <div
        class="w-full min-h-screen bg-gradient-to-br from-slate-50 via-white to-slate-100 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 transition-colors"
    >
        <!-- HEADER -->
        <header
            class="bg-white/80 dark:bg-gray-900/80 backdrop-blur-lg border-b border-gray-200 dark:border-gray-800 px-4 sm:px-6 py-4 sticky top-0 z-20 shadow-sm"
        >
            <div class="max-w-7xl mx-auto">
                <div
                    class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4"
                >
                    <div>
                        <h1
                            class="text-xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 dark:from-indigo-400 dark:to-purple-400 bg-clip-text text-transparent"
                        >
                            📚 Biblioteca de Contenido
                        </h1>
                        <p
                            class="text-sm text-gray-500 dark:text-gray-400 mt-1"
                        >
                            Bienvenido,
                            <strong class="text-gray-700 dark:text-gray-300">{{
                                user?.name || "Invitado"
                            }}</strong>
                            <span
                                v-if="getUserRole()"
                                class="text-indigo-600 dark:text-indigo-400 ml-1"
                                >• {{ getUserRole() }}</span
                            >
                        </p>
                    </div>

                    <div class="flex items-center gap-3 w-full lg:w-auto">
                        <div
                            class="flex bg-gray-100 dark:bg-gray-800 rounded-xl p-1"
                        >
                            <button
                                @click="cambiarVista('grid')"
                                :class="[
                                    'px-3 py-1.5 rounded-lg text-sm font-medium transition-all',
                                    viewMode === 'grid'
                                        ? 'bg-white dark:bg-gray-700 text-indigo-600 dark:text-indigo-400 shadow-sm'
                                        : 'text-gray-600 dark:text-gray-400 hover:bg-white/50',
                                ]"
                                title="Vista cuadrícula"
                            >
                                🖼️
                            </button>
                            <button
                                @click="cambiarVista('list')"
                                :class="[
                                    'px-3 py-1.5 rounded-lg text-sm font-medium transition-all',
                                    viewMode === 'list'
                                        ? 'bg-white dark:bg-gray-700 text-indigo-600 dark:text-indigo-400 shadow-sm'
                                        : 'text-gray-600 dark:text-gray-400 hover:bg-white/50',
                                ]"
                                title="Vista lista"
                            >
                                📋
                            </button>
                        </div>

                        <div class="relative w-full lg:w-72">
                            <input
                                v-model="search"
                                type="text"
                                placeholder="Buscar contenido..."
                                class="w-full pl-10 pr-4 py-2 rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 focus:ring-2 focus:ring-indigo-500 outline-none text-sm transition-all dark:text-gray-100 placeholder-gray-400"
                            />
                            <span class="absolute left-3 top-2.5 text-gray-400"
                                >🔍</span
                            >
                            <button
                                v-if="search"
                                @click="
                                    search = '';
                                    searchDebounced = '';
                                "
                                class="absolute right-2 top-2 p-1 text-gray-400 hover:text-red-500 transition"
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
                    </div>
                </div>

                <!-- Filtros de tipo -->
                <div class="flex flex-wrap gap-2 mt-4">
                    <button
                        v-for="tipo in tipos"
                        :key="tipo.value"
                        @click="tipoSeleccionado = tipo.value"
                        :class="filterClass(tipo.value)"
                    >
                        <span class="mr-1">{{ tipo.icon }}</span>
                        {{ tipo.label }}
                    </button>
                    <button
                        v-if="search || tipoSeleccionado !== 'todo'"
                        @click="resetFiltros"
                        class="px-3 py-1.5 rounded-full text-sm text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 transition"
                    >
                        ✕ Limpiar
                    </button>
                </div>

                <!-- Stats -->
                <div
                    class="flex gap-4 mt-3 text-xs text-gray-500 dark:text-gray-400"
                >
                    <span>📊 Total: {{ stats.total }}</span>
                    <span>👁️ Visibles: {{ stats.visibles }}</span>
                    <span v-if="stats.bloqueados > 0"
                        >🔒 Restringidos: {{ stats.bloqueados }}</span
                    >
                    <span>🏷️ Tipos: {{ stats.tipos }}</span>
                </div>
            </div>
        </header>

        <!-- CONTENIDO PRINCIPAL -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 py-6">
            <!-- Error State -->
            <div
                v-if="error"
                class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 px-4 py-3 rounded-xl mb-6 flex items-center justify-between"
            >
                <span>{{ error }}</span>
                <button
                    @click="router.visit('/tutoriales')"
                    class="text-sm font-semibold underline hover:text-red-800"
                >
                    Reintentar
                </button>
            </div>

            <!-- Loading Skeleton -->
            <div
                v-if="loading"
                :class="
                    viewMode === 'grid'
                        ? 'grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 xl:grid-cols-5 gap-5'
                        : 'space-y-3'
                "
            >
                <div
                    v-for="i in 10"
                    :key="i"
                    :class="
                        viewMode === 'grid'
                            ? 'animate-pulse'
                            : 'animate-pulse bg-white dark:bg-gray-800 rounded-xl p-4'
                    "
                >
                    <div
                        v-if="viewMode === 'grid'"
                        class="bg-gray-200 dark:bg-gray-700 rounded-xl h-40"
                    ></div>
                    <div class="mt-3 space-y-2">
                        <div
                            class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-3/4"
                        ></div>
                        <div
                            class="h-3 bg-gray-100 dark:bg-gray-800 rounded w-full"
                        ></div>
                    </div>
                </div>
            </div>

            <!-- VISTA GRID -->
            <div
                v-else-if="viewMode === 'grid' && filtrados.length > 0"
                class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 xl:grid-cols-5 gap-5"
            >
                <article
                    v-for="recurso in filtrados"
                    :key="recurso.id"
                    class="group bg-white dark:bg-gray-800 rounded-xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 dark:border-gray-700"
                >
                    <!-- Thumbnail -->
                    <div
                        class="relative h-40 overflow-hidden bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800"
                    >
                        <img
                            :src="getThumbnail(recurso.url)"
                            :alt="recurso.titulo"
                            class="w-full h-full object-cover transition duration-500"
                            @error="$event.target.src = '/img/default.jpg'"
                        />

                        <!-- 🔒 Overlay de candado para contenido bloqueado -->
                        <div
                            v-if="estaBloqueado(recurso)"
                            class="absolute inset-0 bg-black/70 backdrop-blur-sm flex flex-col items-center justify-center z-10"
                        >
                            <span class="text-4xl mb-2">🔒</span>
                            <span
                                class="text-white text-xs font-medium text-center px-3"
                            >
                                Contenido restringido
                            </span>
                            <span class="text-white/70 text-[10px] mt-1">
                                {{ recurso.alcance }}
                            </span>
                        </div>

                        <!-- Badge Tipo -->
                        <span
                            :class="[
                                'absolute top-2 left-2 text-white text-[10px] px-2 py-1 rounded z-20',
                                getTipoBadgeClass(recurso.tipo_material),
                            ]"
                        >
                            {{ recurso.tipo_material }}
                        </span>

                        <!-- Badge Alcance (visible para admins) -->
                        <span
                            v-if="
                                [
                                    'Superadmin we collab',
                                    'Admin we collab',
                                ].includes(getUserRole()) && recurso.alcance
                            "
                            :class="getAlcanceBadgeClass(recurso.alcance)"
                            class="absolute top-2 right-2 text-[10px] px-2 py-1 rounded font-medium z-20 shadow-sm"
                        >
                            {{ recurso.alcance }}
                        </span>
                    </div>

                    <!-- Contenido de la tarjeta -->
                    <div class="p-3">
                        <h3
                            class="text-sm font-semibold line-clamp-1"
                            :class="
                                estaBloqueado(recurso)
                                    ? 'text-gray-400 dark:text-gray-500'
                                    : 'text-gray-800 dark:text-gray-200'
                            "
                        >
                            {{ recurso.titulo }}
                        </h3>
                        <p
                            class="text-xs text-gray-500 dark:text-gray-400 line-clamp-2 mt-1"
                            :class="estaBloqueado(recurso) ? 'opacity-60' : ''"
                        >
                            {{ recurso.descripcion || "Sin descripción" }}
                        </p>
                        <div
                            class="flex items-center justify-between mt-3 pt-2 border-t border-gray-100 dark:border-gray-700"
                        >
                            <span class="text-[10px] text-gray-400">
                                {{
                                    new Date(
                                        recurso.created_at,
                                    ).toLocaleDateString("es-ES")
                                }}
                            </span>
                            <span
                                v-if="recurso.alcance"
                                :class="getAlcanceBadgeClass(recurso.alcance)"
                                class="text-[10px] px-2 py-0.5 rounded font-medium"
                            >
                                {{ recurso.alcance }}
                            </span>
                        </div>

                        <!-- ✅ BOTÓN VER EXPLÍCITO -->
                        <div class="mt-3 flex justify-end">
                            <button
                                v-if="!estaBloqueado(recurso)"
                                @click.stop="verVideo(recurso)"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-medium transition-all duration-200 hover:shadow-md"
                            >
                                <svg
                                    class="w-3.5 h-3.5"
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
                                Ver
                            </button>
                            <span
                                v-else
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-gray-200 dark:bg-gray-700 text-gray-500 dark:text-gray-400 text-xs font-medium cursor-not-allowed"
                            >
                                <svg
                                    class="w-3.5 h-3.5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"
                                    />
                                </svg>
                                Restringido
                            </span>
                        </div>
                    </div>
                </article>
            </div>

            <!-- VISTA LISTA -->
            <div
                v-else-if="viewMode === 'list' && filtrados.length > 0"
                class="space-y-3"
            >
                <div
                    v-for="recurso in filtrados"
                    :key="recurso.id"
                    class="group bg-white dark:bg-gray-800 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-all border border-gray-100 dark:border-gray-700"
                >
                    <div class="flex items-center gap-4 p-4">
                        <!-- Thumbnail -->
                        <div
                            class="relative w-20 h-20 rounded-lg overflow-hidden flex-shrink-0 bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800"
                        >
                            <img
                                :src="getThumbnail(recurso.url)"
                                :alt="recurso.titulo"
                                class="w-full h-full object-cover"
                                @error="$event.target.src = '/img/default.jpg'"
                            />
                            <div
                                v-if="estaBloqueado(recurso)"
                                class="absolute inset-0 bg-black/60 flex items-center justify-center"
                            >
                                <span class="text-white text-lg">🔒</span>
                            </div>
                        </div>

                        <!-- Info -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <h3
                                    class="text-sm font-semibold"
                                    :class="
                                        estaBloqueado(recurso)
                                            ? 'text-gray-400'
                                            : 'text-gray-800 dark:text-gray-200'
                                    "
                                >
                                    {{ recurso.titulo }}
                                </h3>
                                <span
                                    :class="[
                                        'text-[10px] px-2 py-0.5 rounded',
                                        getTipoBadgeClass(
                                            recurso.tipo_material,
                                        ),
                                    ]"
                                >
                                    {{ recurso.tipo_material }}
                                </span>
                            </div>
                            <p
                                class="text-xs text-gray-500 dark:text-gray-400 line-clamp-1 mt-1"
                            >
                                {{ recurso.descripcion || "Sin descripción" }}
                            </p>
                            <div
                                class="flex items-center gap-3 mt-2 text-[10px] text-gray-400"
                            >
                                <span>{{
                                    new Date(
                                        recurso.created_at,
                                    ).toLocaleDateString("es-ES")
                                }}</span>
                                <span
                                    v-if="estaBloqueado(recurso)"
                                    class="text-amber-600 dark:text-amber-400"
                                    >🔒 Restringido</span
                                >
                            </div>
                        </div>

                        <!-- Badge alcance + estado + Botón Ver -->
                        <div class="flex flex-col items-end gap-2">
                            <span
                                v-if="recurso.alcance"
                                :class="getAlcanceBadgeClass(recurso.alcance)"
                                class="text-[10px] px-2 py-0.5 rounded font-medium"
                            >
                                {{ recurso.alcance }}
                            </span>
                            <span
                                :class="
                                    recurso.estado === 'activo'
                                        ? 'text-emerald-600'
                                        : 'text-gray-400'
                                "
                                class="text-[10px]"
                            >
                                {{
                                    recurso.estado === "activo"
                                        ? "● Activo"
                                        : "○ Inactivo"
                                }}
                            </span>
                            <button
                                v-if="!estaBloqueado(recurso)"
                                @click.stop="verVideo(recurso)"
                                class="mt-1 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-medium transition-all duration-200 hover:shadow-md"
                            >
                                <svg
                                    class="w-3.5 h-3.5"
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
                                Ver
                            </button>
                            <span
                                v-else
                                class="mt-1 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-gray-200 dark:bg-gray-700 text-gray-500 dark:text-gray-400 text-xs font-medium cursor-not-allowed"
                            >
                                🔒
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Estados vacíos -->
            <div
                v-if="
                    !loading &&
                    !error &&
                    filtrados.length === 0 &&
                    searchDebounced
                "
                class="text-center py-20"
            >
                <div class="text-6xl mb-4">🔍</div>
                <p class="text-gray-600 dark:text-gray-400">
                    No se encontró "{{ search }}"
                </p>
                <button
                    @click="resetFiltros"
                    class="mt-3 text-indigo-600 dark:text-indigo-400 text-sm hover:underline"
                >
                    Limpiar filtros
                </button>
            </div>

            <div
                v-if="!loading && !error && tutoriales.length === 0"
                class="text-center py-20"
            >
                <div class="text-6xl mb-4">📚</div>
                <p class="text-gray-600 dark:text-gray-400">
                    Aún no hay contenido disponible
                </p>
            </div>

            <div
                v-if="
                    !loading &&
                    !error &&
                    tutoriales.length > 0 &&
                    filtrados.length === 0 &&
                    !searchDebounced
                "
                class="text-center py-20"
            >
                <div class="text-6xl mb-4">🔒</div>
                <p class="text-gray-600 dark:text-gray-400">
                    No tienes acceso a ningún contenido con los filtros actuales
                </p>
                <p class="text-sm text-gray-500 dark:text-gray-500 mt-2">
                    Tu rol: {{ getUserRole() || "Invitado" }}
                </p>
            </div>
        </main>

        <!-- Toast Notifications -->
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
                    <span>{{ toastType === "success" ? "✅" : "⚠️" }}</span>
                    <span class="text-sm">{{ toastMessage }}</span>
                </div>
            </Transition>
        </Teleport>
    </div>
</template>

<style scoped>
.filter {
    @apply px-4 py-1.5 rounded-full text-sm font-medium transition whitespace-nowrap;
}
.filter-active {
    @apply bg-indigo-600 text-white shadow-md;
}
.filter-inactive {
    @apply bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700;
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
.toast-enter-active,
.toast-leave-active {
    transition: all 0.3s ease;
}
.toast-enter-from,
.toast-leave-to {
    opacity: 0;
    transform: translateX(100%);
}
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
.group {
    animation: fadeInUp 0.3s ease-out;
}
.dark .group:hover {
    box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.3);
}
</style>
