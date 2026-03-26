<script setup>
import { ref, computed, onMounted } from "vue";
import { usePage, router } from "@inertiajs/vue3";

// ✅ Usar window.axios (configurado en bootstrap.js con withCredentials, baseURL, CSRF)
const axios = window.axios;

// 🔷 ESTADO GLOBAL
const page = usePage();
const user = computed(() => page.props.auth?.user);
const tutoriales = ref([]);
const loading = ref(true);
const error = ref(null);

// 🔷 FILTROS
const search = ref("");
const tipoSeleccionado = ref("todo");

// 🔷 TIPOS DE MATERIAL
const tipos = [
    { label: "Todo", value: "todo" },
    { label: "Video", value: "video" },
    { label: "Manual", value: "manual" },
    { label: "Guía", value: "guia" },
    { label: "Tríptico", value: "triptico" },
];

// 🎯 VALORES EXACTOS DE TU ENUM 'alcance' EN LA BD
const ALCANCES_VALIDOS = [
    "Superadmin We collab",
    "Admin We collab",
    "Suscriptor SLC",
    "Cliente Admin",
    "Cliente Premium",
    "Usuario Público",
    "Prospecto",
];

// 🔷 HELPER: Obtener rol del usuario como string limpio
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
        );
    }
    return role.toString().trim();
};

// 🔷 HELPER: Normalizar string para comparación (case-insensitive)
const normalize = (str) => {
    if (!str) return "";
    return str.toString().toLowerCase().trim();
};

// 🔐 FUNCIÓN: ¿El usuario tiene acceso al tutorial? (para UI del candado)
const tieneAcceso = (tutorial) => {
    const rolUsuario = normalize(getUserRole());
    const alcanceTutorial = tutorial.alcance;

    // Validar que el alcance sea un valor válido
    if (alcanceTutorial && !ALCANCES_VALIDOS.includes(alcanceTutorial)) {
        console.warn(
            `⚠️ Alcance inválido en tutorial ${tutorial.id}: "${alcanceTutorial}"`,
        );
        return false;
    }

    // Contenido público: todos lo ven
    if (!alcanceTutorial || alcanceTutorial.trim() === "") return true;

    // Sin rol: solo ve contenidos públicos
    if (!rolUsuario) return false;

    // Superadmin: ve todo el contenido
    if (rolUsuario === "superadmin we collab") return true;

    // Match exacto (case-insensitive)
    return normalize(rolUsuario) === normalize(alcanceTutorial);
};

// 🔐 FUNCIÓN: ¿El tutorial está bloqueado para este usuario?
const estaBloqueado = (tutorial) => !tieneAcceso(tutorial);

// 🔷 CARGAR DATOS DESDE API
onMounted(async () => {
    try {
        error.value = null;
        loading.value = true;

        // ✅ CON baseURL='/api' en bootstrap.js: usar solo '/tutoriales'
        // ✅ Headers y cookies ya están configurados globalmente
        const res = await axios.get("/tutoriales", {
            timeout: 10000,
        });

        // Validar estructura de respuesta
        if (res.data?.data && Array.isArray(res.data.data)) {
            tutoriales.value = res.data.data;
        } else if (Array.isArray(res.data)) {
            tutoriales.value = res.data;
        } else {
            throw new Error("Respuesta de API con formato inesperado");
        }
    } catch (err) {
        console.error("❌ Error cargando tutoriales:", err);

        // Mensajes informativos según el tipo de error
        if (err.response?.status === 401) {
            error.value = "Tu sesión ha expirado. Redirigiendo al login...";
            // Redirigir al login después de 2 segundos
            setTimeout(() => router.visit("/login"), 2000);
        } else if (err.response?.status === 403) {
            error.value = "No tienes permisos para acceder a esta sección";
        } else if (err.response?.status === 404) {
            error.value = "La ruta de tutoriales no está disponible";
        } else {
            error.value =
                "No se pudieron cargar los contenidos. Verifica tu conexión.";
        }
    } finally {
        loading.value = false;
    }
});

// 🔷 FILTRADO COMPUTADO - Muestra TODOS los videos (frontend maneja UI del candado)
const filtrados = computed(() => {
    return tutoriales.value.filter((t) => {
        // Solo filtrar por estado, tipo y búsqueda (NO por alcance)
        if (t.estado !== "activo") return false;

        const tipoOK =
            tipoSeleccionado.value === "todo" ||
            normalize(t.tipo_material) === normalize(tipoSeleccionado.value);
        if (!tipoOK) return false;

        const searchOK =
            !search.value ||
            t.titulo?.toLowerCase().includes(search.value.toLowerCase());
        if (!searchOK) return false;

        // ✅ NO filtrar por alcance: const alcanceOK = tieneAcceso(t);
        // El frontend muestra candado 🔒 en videos restringidos

        return true;
    });
});

// 🔷 THUMBNAIL DE YOUTUBE - ✅ CORREGIDO (sin espacios en la URL)
const getThumbnail = (url) => {
    if (!url) return "/img/default.jpg";

    if (url.includes("youtube.com") || url.includes("youtu.be")) {
        let videoId = null;

        // Formato: youtu.be/VIDEO_ID
        if (url.includes("youtu.be/")) {
            videoId = url.split("youtu.be/")[1]?.split(/[?&#]/)[0];
        }
        // Formato: youtube.com/watch?v=VIDEO_ID
        else if (url.includes("v=")) {
            videoId = url.split("v=")[1]?.split(/[?&#]/)[0];
        }
        // Formato: youtube.com/embed/VIDEO_ID
        else if (url.includes("/embed/")) {
            videoId = url.split("/embed/")[1]?.split(/[?&#]/)[0];
        }

        // Validar que el ID tenga 11 caracteres (formato válido de YouTube)
        if (videoId && videoId.length === 11) {
            // ✅ SIN ESPACIOS en la URL (corregido)
            return `https://img.youtube.com/vi/${videoId}/hqdefault.jpg`;
        }
    }
    return "/img/default.jpg";
};

// 🎨 Helper para obtener color del badge según alcance
const getAlcanceBadgeClass = (alcance) => {
    const colors = {
        "Superadmin We collab": "bg-purple-100 text-purple-800",
        "Admin We collab": "bg-indigo-100 text-indigo-800",
        "Suscriptor SLC": "bg-teal-100 text-teal-800",
        "Cliente Admin": "bg-emerald-100 text-emerald-800",
        "Cliente Premium": "bg-amber-100 text-amber-800",
        "Usuario Público": "bg-gray-100 text-gray-800",
        Prospecto: "bg-blue-100 text-blue-800",
    };
    return colors[alcance] || "bg-slate-100 text-slate-800";
};

// 🔷 NAVEGACIÓN - Bloquea si no tiene acceso
const verVideo = (tutorial) => {
    if (estaBloqueado(tutorial)) {
        // Mostrar mensaje amigable en lugar de navegar
        alert(
            `🔒 Este contenido está disponible solo para el rol: "${tutorial.alcance}"\n\nTu rol actual: "${getUserRole() || "Invitado"}"`,
        );
        return;
    }
    router.visit(`/tutorial/${tutorial.id}`);
};

// 🔷 ESTILOS DE FILTROS
const filterClass = (tipo) =>
    tipoSeleccionado.value === tipo
        ? "filter filter-active"
        : "filter filter-inactive";

// 🔷 RESET FILTROS
const resetFiltros = () => {
    search.value = "";
    tipoSeleccionado.value = "todo";
};
</script>

<template>
    <div class="w-full min-h-screen bg-slate-100">
        <!-- HEADER -->
        <div class="bg-white border-b px-6 py-4 sticky top-0 z-10 shadow-sm">
            <div
                class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4"
            >
                <div>
                    <h1 class="text-xl font-bold text-slate-800">
                        📚 Biblioteca de Contenido
                    </h1>
                    <p class="text-sm text-slate-500">
                        Bienvenido,
                        <strong>{{ user?.name || "Invitado" }}</strong>
                        <span v-if="getUserRole()" class="text-blue-600 ml-1"
                            >• {{ getUserRole() }}</span
                        >
                    </p>
                </div>
                <div class="flex items-center gap-3 w-full lg:w-auto">
                    <div class="relative w-full lg:w-80">
                        <input
                            v-model="search"
                            type="text"
                            placeholder="Buscar contenido..."
                            class="w-full pl-10 pr-4 py-2 rounded-full border border-slate-300 focus:ring-2 focus:ring-blue-500 outline-none text-sm"
                        />
                        <span class="absolute left-3 top-2.5 text-slate-400"
                            >🔍</span
                        >
                    </div>
                </div>
            </div>
            <div class="flex gap-2 mt-4 overflow-x-auto pb-1">
                <button
                    v-for="tipo in tipos"
                    :key="tipo.value"
                    @click="tipoSeleccionado = tipo.value"
                    :class="filterClass(tipo.value)"
                >
                    {{ tipo.label }}
                </button>
                <button
                    v-if="search || tipoSeleccionado !== 'todo'"
                    @click="resetFiltros"
                    class="px-3 py-1.5 rounded-full text-sm text-slate-500 hover:bg-slate-100"
                >
                    ✕ Limpiar
                </button>
            </div>
        </div>

        <!-- CONTENIDO -->
        <div class="p-6">
            <!-- ERROR -->
            <div
                v-if="error"
                class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4"
            >
                {{ error }}
            </div>

            <!-- LOADING -->
            <div v-if="loading" class="flex justify-center py-20">
                <div
                    class="animate-spin h-10 w-10 border-4 border-blue-500 border-t-transparent rounded-full"
                ></div>
            </div>

            <!-- GRID - MUESTRA TODOS LOS VIDEOS (con candado si está restringido) -->
            <div
                v-else
                class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 xl:grid-cols-5 gap-5"
            >
                <div
                    v-for="tutorial in filtrados"
                    :key="tutorial.id"
                    @click="verVideo(tutorial)"
                    :class="[
                        'group bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-xl transition border border-slate-100',
                        estaBloqueado(tutorial)
                            ? 'cursor-not-allowed opacity-75'
                            : 'cursor-pointer',
                    ]"
                >
                    <div class="relative h-40 overflow-hidden bg-slate-200">
                        <img
                            :src="getThumbnail(tutorial.url)"
                            :alt="tutorial.titulo"
                            class="w-full h-full object-cover group-hover:scale-110 transition"
                            @error="$event.target.src = '/img/default.jpg'"
                        />

                        <!-- ✅ OVERLAY DE CANDADO para contenido bloqueado -->
                        <div
                            v-if="estaBloqueado(tutorial)"
                            class="absolute inset-0 bg-black/60 flex flex-col items-center justify-center z-10"
                        >
                            <span class="text-4xl mb-1">🔒</span>
                            <span
                                class="text-white text-xs font-medium text-center px-2"
                            >
                                Contenido restringido
                            </span>
                        </div>

                        <!-- Overlay Play (solo si tiene acceso) -->
                        <div
                            v-if="!estaBloqueado(tutorial)"
                            class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition"
                        >
                            <div
                                class="bg-white/30 backdrop-blur p-3 rounded-full text-white text-xl"
                            >
                                ▶
                            </div>
                        </div>

                        <!-- Badge Tipo -->
                        <span
                            class="absolute top-2 left-2 bg-blue-600 text-white text-[10px] px-2 py-1 rounded z-20"
                        >
                            {{ tutorial.tipo_material }}
                        </span>

                        <!-- Badge Alcance (visible para admins) -->
                        <span
                            v-if="
                                [
                                    'Superadmin We collab',
                                    'Admin We collab',
                                ].includes(getUserRole()) && tutorial.alcance
                            "
                            :class="getAlcanceBadgeClass(tutorial.alcance)"
                            class="absolute top-2 right-2 text-[10px] px-2 py-1 rounded font-medium z-20"
                        >
                            {{ tutorial.alcance }}
                        </span>
                    </div>

                    <div class="p-3">
                        <h3
                            class="text-sm font-semibold line-clamp-1"
                            :class="
                                estaBloqueado(tutorial)
                                    ? 'text-slate-400'
                                    : 'text-slate-800'
                            "
                        >
                            {{ tutorial.titulo }}
                        </h3>
                        <p
                            class="text-xs text-slate-500 line-clamp-2 mt-1"
                            :class="estaBloqueado(tutorial) ? 'opacity-60' : ''"
                        >
                            {{ tutorial.descripcion }}
                        </p>
                        <div
                            class="flex items-center justify-between mt-3 pt-2 border-t border-slate-100"
                        >
                            <span class="text-[10px] text-slate-400">
                                {{
                                    new Date(
                                        tutorial.created_at,
                                    ).toLocaleDateString("es-ES")
                                }}
                            </span>
                            <!-- Badge de alcance visible para todos -->
                            <span
                                v-if="tutorial.alcance"
                                :class="getAlcanceBadgeClass(tutorial.alcance)"
                                class="text-[10px] px-2 py-0.5 rounded font-medium"
                            >
                                {{ tutorial.alcance }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ESTADOS VACÍOS -->
            <div
                v-if="
                    !loading &&
                    !error &&
                    tutoriales.length > 0 &&
                    filtrados.length === 0 &&
                    search
                "
                class="text-center py-20"
            >
                <div class="text-4xl mb-3">🔍</div>
                <p class="text-slate-600">No se encontró "{{ search }}"</p>
                <button
                    @click="search = ''"
                    class="mt-2 text-blue-600 text-sm hover:underline"
                >
                    Limpiar búsqueda
                </button>
            </div>

            <div
                v-if="!loading && !error && tutoriales.length === 0"
                class="text-center py-20"
            >
                <div class="text-4xl mb-3">📚</div>
                <p class="text-slate-500">Aún no hay contenido disponible</p>
            </div>
        </div>
    </div>
</template>

<style scoped>
.filter {
    @apply px-4 py-1.5 rounded-full text-sm font-medium transition whitespace-nowrap;
}
.filter-active {
    @apply bg-blue-600 text-white shadow;
}
.filter-inactive {
    @apply bg-slate-100 text-slate-600 hover:bg-slate-200;
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
