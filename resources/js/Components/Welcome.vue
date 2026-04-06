<script setup>
import { ref, computed, onMounted } from "vue";
import { usePage, router } from "@inertiajs/vue3";

const axios = window.axios;

// 🔷 USUARIO Y ROLES
const page = usePage();
const user = computed(() => page.props.auth?.user);

// 🔷 Estado
const loading = ref(true);
const error = ref(null);
const lastUpdate = ref(null);

// 🔷 Datos principales
const stats = ref({
    usuarios: { total: 0, activos: 0, prospectos: 0, verificados: 0 },
    tutoriales: {
        total: 0,
        activos: 0,
        videos: 0,
        manuales: 0,
        guias: 0,
        tripticos: 0,
    },
    recursos: { total: 0, activos: 0 },
    categorias: { total: 0, activas: 0 },
    subcategorias: { total: 0, activas: 0 },
    roles: { total: 0 },
});

// 🔷 Listas recientes
const ultimosTutoriales = ref([]);
const ultimosRecursos = ref([]);
const ultimosUsuarios = ref([]);
const categoriasRecientes = ref([]);
const subcategoriasRecientes = ref([]);

// 🔷 Distribuciones
const tutorialesPorTipo = ref({});
const usuariosPorRol = ref({});
const tutorialesPorCategoria = ref({});

// 🔷 Obtener rol del usuario
const getUserRole = () => {
    const role = user.value?.role;
    if (!role) return "Invitado";
    if (typeof role === "object") {
        return (
            role.nombre?.toString() ||
            role.name?.toString() ||
            role.slug?.toString() ||
            ""
        );
    }
    return role.toString().trim();
};

// 🔷 Roles importantes
const ROLES_IMPORTANTES = [
    "Superadmin we collab",
    "Admin we collab",
    "Admin We collab",
];
const esAdmin = computed(() => ROLES_IMPORTANTES.includes(getUserRole()));

// 🔷 Cargar todos los datos
const cargarDashboard = async () => {
    loading.value = true;
    error.value = null;

    try {
        // 1. TUTORIALES
        const tutorialesRes = await axios.get("/tutoriales");
        const tutoriales = tutorialesRes.data?.data || tutorialesRes.data || [];

        stats.value.tutoriales.total = tutoriales.length;
        stats.value.tutoriales.activos = tutoriales.filter(
            (t) => t.estado === "activo",
        ).length;
        stats.value.tutoriales.videos = tutoriales.filter(
            (t) => t.tipo_material === "video",
        ).length;
        stats.value.tutoriales.manuales = tutoriales.filter(
            (t) => t.tipo_material === "manual",
        ).length;
        stats.value.tutoriales.guias = tutoriales.filter(
            (t) => t.tipo_material === "guia",
        ).length;
        stats.value.tutoriales.tripticos = tutoriales.filter(
            (t) => t.tipo_material === "triptico",
        ).length;

        // Tutoriales por tipo
        tutorialesPorTipo.value = {
            videos: stats.value.tutoriales.videos,
            manuales: stats.value.tutoriales.manuales,
            guias: stats.value.tutoriales.guias,
            tripticos: stats.value.tutoriales.tripticos,
        };

        // Últimos tutoriales
        ultimosTutoriales.value = [...tutoriales]
            .sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
            .slice(0, 5);

        // 2. RECURSOS
        try {
            const recursosRes = await axios.get("/recursos");
            const recursos = recursosRes.data?.data || recursosRes.data || [];
            stats.value.recursos.total = recursos.length;
            stats.value.recursos.activos = recursos.filter(
                (r) => r.estado === "activo",
            ).length;

            ultimosRecursos.value = [...recursos]
                .sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
                .slice(0, 5);
        } catch (e) {
            console.warn("Error recursos:", e);
        }

        // 3. CATEGORÍAS
        try {
            const categoriasRes = await axios.get("/categorias/all");
            const categorias = categoriasRes.data || [];
            stats.value.categorias.total = categorias.length;
            stats.value.categorias.activas = categorias.filter(
                (c) => c.estado === "activo",
            ).length;
            categoriasRecientes.value = categorias.slice(0, 5);
        } catch (e) {
            console.warn("Error categorías:", e);
        }

        // 4. SUBCATEGORÍAS
        try {
            const subcategoriasRes = await axios.get("/subcategorias/all");
            const subcategorias = subcategoriasRes.data || [];
            stats.value.subcategorias.total = subcategorias.length;
            stats.value.subcategorias.activas = subcategorias.filter(
                (s) => s.estado === "activo",
            ).length;
            subcategoriasRecientes.value = subcategorias.slice(0, 5);
        } catch (e) {
            console.warn("Error subcategorías:", e);
        }

        // 5. USUARIOS (solo admins)
        if (esAdmin.value) {
            try {
                const usuariosRes = await axios.get("/usuarios/all");
                const usuarios = usuariosRes.data || [];
                stats.value.usuarios.total = usuarios.length;
                stats.value.usuarios.activos = usuarios.filter(
                    (u) => u.estado === "activo",
                ).length;
                stats.value.usuarios.prospectos = usuarios.filter(
                    (u) =>
                        u.role?.nombre === "Prospecto" ||
                        u.role === "Prospecto",
                ).length;
                stats.value.usuarios.verificados =
                    stats.value.usuarios.total -
                    stats.value.usuarios.prospectos;

                // Usuarios por rol
                usuariosPorRol.value = usuarios.reduce((acc, u) => {
                    const rol = u.role?.nombre || u.role || "Sin rol";
                    acc[rol] = (acc[rol] || 0) + 1;
                    return acc;
                }, {});

                ultimosUsuarios.value = [...usuarios]
                    .sort(
                        (a, b) =>
                            new Date(b.created_at) - new Date(a.created_at),
                    )
                    .slice(0, 5);
            } catch (e) {
                console.warn("Error usuarios:", e);
            }
        }

        // 6. ROLES
        try {
            const rolesRes = await axios.get("/roles");
            const roles = rolesRes.data?.data || rolesRes.data || [];
            stats.value.roles.total = roles.length;
        } catch (e) {
            console.warn("Error roles:", e);
        }

        lastUpdate.value = new Date().toLocaleString();
    } catch (err) {
        console.error("Error:", err);
        error.value = "Error al cargar datos";
    } finally {
        loading.value = false;
    }
};

// 🔷 Acciones rápidas
const irA = (ruta) => router.visit(ruta);

// 🔷 Inicializar
onMounted(() => {
    cargarDashboard();
});
</script>

<template>
    <div
        class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-slate-100"
    >
        <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
            <!-- HEADER -->
            <div class="mb-8">
                <div
                    class="flex flex-col md:flex-row md:items-center md:justify-between gap-4"
                >
                    <div>
                        <h1
                            class="text-3xl font-bold bg-gradient-to-r from-slate-800 to-slate-600 bg-clip-text text-transparent"
                        >
                            Dashboard
                        </h1>
                        <p class="text-slate-500 mt-1">
                            Panel de control de la plataforma SLC
                        </p>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="text-right">
                            <p class="text-sm text-slate-500">
                                {{ user?.name }}
                            </p>
                            <p class="text-xs text-slate-400">
                                {{ getUserRole() }}
                            </p>
                        </div>
                        <div
                            class="w-10 h-10 rounded-full bg-gradient-to-r from-teal-500 to-emerald-500 flex items-center justify-center text-white font-bold"
                        >
                            {{ user?.name?.charAt(0) || "U" }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- LOADING -->
            <div v-if="loading" class="space-y-6">
                <div
                    class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5"
                >
                    <div
                        v-for="i in 8"
                        :key="i"
                        class="bg-white rounded-2xl p-5 animate-pulse"
                    >
                        <div class="h-4 bg-slate-200 rounded w-24"></div>
                        <div class="h-8 bg-slate-200 rounded w-16 mt-2"></div>
                    </div>
                </div>
            </div>

            <div v-else>
                <!-- CARDS PRINCIPALES - FILA 1 -->
                <div
                    class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5"
                >
                    <!-- Usuarios -->
                    <div
                        class="bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-md transition p-5"
                    >
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-slate-500">
                                    Total Usuarios
                                </p>
                                <p class="text-3xl font-bold text-slate-800">
                                    {{ stats.usuarios.total || 0 }}
                                </p>
                                <div class="flex gap-3 mt-2 text-xs">
                                    <span class="text-green-600"
                                        >✓
                                        {{ stats.usuarios.activos }}
                                        activos</span
                                    >
                                    <span class="text-amber-600"
                                        >📝
                                        {{ stats.usuarios.prospectos }}
                                        prospectos</span
                                    >
                                </div>
                            </div>
                            <div
                                class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center"
                            >
                                <svg
                                    class="w-6 h-6 text-indigo-600"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
                                    />
                                </svg>
                            </div>
                        </div>
                        <button
                            @click="irA('/usuarios')"
                            class="mt-4 text-sm text-indigo-600 hover:text-indigo-800 flex items-center gap-1"
                        >
                            Ver detalles →
                        </button>
                    </div>

                    <!-- Tutoriales -->
                    <div
                        class="bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-md transition p-5"
                    >
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-slate-500">Tutoriales</p>
                                <p class="text-3xl font-bold text-slate-800">
                                    {{ stats.tutoriales.total || 0 }}
                                </p>
                                <div class="flex gap-2 mt-2 text-xs flex-wrap">
                                    <span class="text-rose-600"
                                        >🎬 {{ stats.tutoriales.videos }}</span
                                    >
                                    <span class="text-blue-600"
                                        >📚
                                        {{ stats.tutoriales.manuales }}</span
                                    >
                                    <span class="text-emerald-600"
                                        >🧭 {{ stats.tutoriales.guias }}</span
                                    >
                                </div>
                            </div>
                            <div
                                class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center"
                            >
                                <svg
                                    class="w-6 h-6 text-emerald-600"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"
                                    />
                                </svg>
                            </div>
                        </div>
                        <button
                            @click="irA('/recursos')"
                            class="mt-4 text-sm text-emerald-600 hover:text-emerald-800 flex items-center gap-1"
                        >
                            Ver tutoriales →
                        </button>
                    </div>

                    <!-- Recursos -->
                    <div
                        class="bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-md transition p-5"
                    >
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-slate-500">Recursos</p>
                                <p class="text-3xl font-bold text-slate-800">
                                    {{ stats.recursos.total || 0 }}
                                </p>
                                <p class="text-xs text-green-600 mt-2">
                                    ✓ {{ stats.recursos.activos }} activos
                                </p>
                            </div>
                            <div
                                class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center"
                            >
                                <svg
                                    class="w-6 h-6 text-amber-600"
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
                            </div>
                        </div>
                        <button
                            @click="irA('/recursos')"
                            class="mt-4 text-sm text-amber-600 hover:text-amber-800 flex items-center gap-1"
                        >
                            Ver recursos →
                        </button>
                    </div>

                    <!-- Roles -->
                    <div
                        class="bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-md transition p-5"
                    >
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-slate-500">
                                    Roles del sistema
                                </p>
                                <p class="text-3xl font-bold text-slate-800">
                                    {{ stats.roles.total || 0 }}
                                </p>
                                <p class="text-xs text-slate-400 mt-2">
                                    🔐 Control de acceso
                                </p>
                            </div>
                            <div
                                class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center"
                            >
                                <svg
                                    class="w-6 h-6 text-purple-600"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"
                                    />
                                </svg>
                            </div>
                        </div>
                        <button
                            @click="irA('/roles')"
                            class="mt-4 text-sm text-purple-600 hover:text-purple-800 flex items-center gap-1"
                        >
                            Gestionar roles →
                        </button>
                    </div>
                </div>

                <!-- CARDS SECUNDARIAS - FILA 2 -->
                <div
                    class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mt-5"
                >
                    <!-- Categorías -->
                    <div
                        class="bg-gradient-to-br from-slate-50 to-white rounded-2xl border border-slate-100 p-5"
                    >
                        <div class="flex items-center justify-between mb-3">
                            <div
                                class="w-10 h-10 bg-teal-100 rounded-xl flex items-center justify-center"
                            >
                                <svg
                                    class="w-5 h-5 text-teal-600"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l5 5a2 2 0 01.586 1.414V19a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2z"
                                    />
                                </svg>
                            </div>
                            <span class="text-xs text-teal-600 font-medium"
                                >Clasificación</span
                            >
                        </div>
                        <p class="text-2xl font-bold text-slate-800">
                            {{ stats.categorias.total }}
                        </p>
                        <p class="text-xs text-slate-500">Categorías totales</p>
                        <p class="text-xs text-green-600 mt-1">
                            {{ stats.categorias.activas }} activas
                        </p>
                    </div>

                    <!-- Subcategorías -->
                    <div
                        class="bg-gradient-to-br from-slate-50 to-white rounded-2xl border border-slate-100 p-5"
                    >
                        <div class="flex items-center justify-between mb-3">
                            <div
                                class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center"
                            >
                                <svg
                                    class="w-5 h-5 text-blue-600"
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
                            </div>
                            <span class="text-xs text-blue-600 font-medium"
                                >Detalle</span
                            >
                        </div>
                        <p class="text-2xl font-bold text-slate-800">
                            {{ stats.subcategorias.total }}
                        </p>
                        <p class="text-xs text-slate-500">Subcategorías</p>
                        <p class="text-xs text-green-600 mt-1">
                            {{ stats.subcategorias.activas }} activas
                        </p>
                    </div>

                    <!-- Contenido activo -->
                    <div
                        class="bg-gradient-to-br from-slate-50 to-white rounded-2xl border border-slate-100 p-5"
                    >
                        <div class="flex items-center justify-between mb-3">
                            <div
                                class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center"
                            >
                                <svg
                                    class="w-5 h-5 text-green-600"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                    />
                                </svg>
                            </div>
                            <span class="text-xs text-green-600 font-medium"
                                >Estado</span
                            >
                        </div>
                        <p class="text-2xl font-bold text-slate-800">
                            {{
                                stats.tutoriales.activos +
                                stats.recursos.activos
                            }}
                        </p>
                        <p class="text-xs text-slate-500">
                            Contenido activo total
                        </p>
                        <p class="text-xs text-slate-400 mt-1">
                            + {{ stats.tutoriales.activos }} tutoriales •
                            {{ stats.recursos.activos }} recursos
                        </p>
                    </div>

                    <!-- Última actualización -->
                    <div
                        class="bg-gradient-to-br from-slate-50 to-white rounded-2xl border border-slate-100 p-5"
                    >
                        <div class="flex items-center justify-between mb-3">
                            <div
                                class="w-10 h-10 bg-slate-100 rounded-xl flex items-center justify-center"
                            >
                                <svg
                                    class="w-5 h-5 text-slate-600"
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
                            </div>
                            <button
                                @click="cargarDashboard"
                                class="text-xs text-indigo-600 hover:text-indigo-800"
                            >
                                ↻ Actualizar
                            </button>
                        </div>
                        <p class="text-xs text-slate-500">
                            Última sincronización
                        </p>
                        <p class="text-sm font-medium text-slate-700">
                            {{ lastUpdate || "No disponible" }}
                        </p>
                    </div>
                </div>

                <!-- LISTAS RECIENTES - Solo para admins -->
                <div
                    v-if="esAdmin"
                    class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-6"
                >
                    <!-- Últimos tutoriales -->
                    <div
                        class="bg-white rounded-2xl border shadow-sm overflow-hidden"
                    >
                        <div
                            class="px-5 py-4 border-b bg-slate-50 flex justify-between items-center"
                        >
                            <h3 class="font-semibold text-slate-800">
                                🎬 Últimos tutoriales
                            </h3>
                            <button
                                @click="irA('/recursos')"
                                class="text-xs text-indigo-600 hover:underline"
                            >
                                Ver todos
                            </button>
                        </div>
                        <div class="divide-y">
                            <div
                                v-for="t in ultimosTutoriales"
                                :key="t.id"
                                @click="irA(`/tutorial/${t.id}`)"
                                class="p-4 hover:bg-slate-50 transition cursor-pointer"
                            >
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="font-medium text-slate-800">
                                            {{ t.titulo }}
                                        </p>
                                        <p class="text-xs text-slate-500">
                                            {{ t.tipo_material }} •
                                            {{ t.alcance || "Público" }}
                                        </p>
                                    </div>
                                    <span
                                        :class="
                                            t.estado === 'activo'
                                                ? 'text-green-600'
                                                : 'text-red-500'
                                        "
                                        class="text-xs"
                                    >
                                        {{ t.estado }}
                                    </span>
                                </div>
                            </div>
                            <div
                                v-if="ultimosTutoriales.length === 0"
                                class="p-8 text-center text-slate-400"
                            >
                                No hay tutoriales
                            </div>
                        </div>
                    </div>

                    <!-- Últimos usuarios -->
                    <div
                        class="bg-white rounded-2xl border shadow-sm overflow-hidden"
                    >
                        <div
                            class="px-5 py-4 border-b bg-slate-50 flex justify-between items-center"
                        >
                            <h3 class="font-semibold text-slate-800">
                                👥 Últimos usuarios
                            </h3>
                            <button
                                @click="irA('/usuarios')"
                                class="text-xs text-indigo-600 hover:underline"
                            >
                                Ver todos
                            </button>
                        </div>
                        <div class="divide-y">
                            <div
                                v-for="u in ultimosUsuarios"
                                :key="u.id"
                                class="p-4"
                            >
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="font-medium text-slate-800">
                                            {{ u.name }}
                                        </p>
                                        <p class="text-xs text-slate-500">
                                            {{ u.email }}
                                        </p>
                                    </div>
                                    <span
                                        class="text-xs px-2 py-1 rounded-full"
                                        :class="
                                            u.estado === 'activo'
                                                ? 'bg-green-100 text-green-700'
                                                : 'bg-red-100 text-red-700'
                                        "
                                    >
                                        {{
                                            u.estado === "activo"
                                                ? "Activo"
                                                : "Inactivo"
                                        }}
                                    </span>
                                </div>
                            </div>
                            <div
                                v-if="ultimosUsuarios.length === 0"
                                class="p-8 text-center text-slate-400"
                            >
                                No hay usuarios
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CATEGORÍAS Y SUBCATEGORÍAS RECIENTES -->
                <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Categorías -->
                    <div class="bg-white rounded-2xl border shadow-sm p-5">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="font-semibold text-slate-800">
                                📁 Categorías destacadas
                            </h3>
                            <button
                                @click="irA('/categorias')"
                                class="text-xs text-teal-600 hover:underline"
                            >
                                Gestionar
                            </button>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <span
                                v-for="cat in categoriasRecientes"
                                :key="cat.id"
                                class="px-3 py-1.5 bg-slate-100 rounded-lg text-sm text-slate-700"
                            >
                                {{ cat.nombre }}
                            </span>
                            <span
                                v-if="categoriasRecientes.length === 0"
                                class="text-slate-400 text-sm"
                                >Sin categorías</span
                            >
                        </div>
                    </div>

                    <!-- Subcategorías -->
                    <div class="bg-white rounded-2xl border shadow-sm p-5">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="font-semibold text-slate-800">
                                🔖 Subcategorías
                            </h3>
                            <button
                                @click="irA('/subcategorias')"
                                class="text-xs text-blue-600 hover:underline"
                            >
                                Gestionar
                            </button>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <span
                                v-for="sub in subcategoriasRecientes"
                                :key="sub.id"
                                class="px-3 py-1.5 bg-slate-100 rounded-lg text-sm text-slate-700"
                            >
                                {{ sub.nombre }}
                            </span>
                            <span
                                v-if="subcategoriasRecientes.length === 0"
                                class="text-slate-400 text-sm"
                                >Sin subcategorías</span
                            >
                        </div>
                    </div>
                </div>

                <!-- ACCIONES RÁPIDAS -->
                <div class="mt-8 bg-white border rounded-2xl p-6 shadow-sm">
                    <h2 class="font-bold text-slate-800 mb-4">
                        ⚡ Acciones rápidas
                    </h2>
                    <div class="flex flex-wrap gap-3">
                        <button
                            @click="irA('/recursos')"
                            class="bg-teal-600 text-white px-5 py-2.5 rounded-xl text-sm font-medium hover:bg-teal-700 transition"
                        >
                            + Subir material
                        </button>
                        <button
                            @click="irA('/usuarios')"
                            class="bg-indigo-600 text-white px-5 py-2.5 rounded-xl text-sm font-medium hover:bg-indigo-700 transition"
                        >
                            Ver usuarios
                        </button>
                        <button
                            @click="irA('/categorias')"
                            class="bg-emerald-600 text-white px-5 py-2.5 rounded-xl text-sm font-medium hover:bg-emerald-700 transition"
                        >
                            Gestionar categorías
                        </button>
                        <button
                            @click="irA('/recursos')"
                            class="bg-amber-600 text-white px-5 py-2.5 rounded-xl text-sm font-medium hover:bg-amber-700 transition"
                        >
                            Ir a catálogo
                        </button>
                        <button
                            @click="irA('/roles')"
                            class="bg-purple-600 text-white px-5 py-2.5 rounded-xl text-sm font-medium hover:bg-purple-700 transition"
                        >
                            Configurar roles
                        </button>
                    </div>
                </div>
            </div>

            <!-- ERROR -->
            <div
                v-if="error"
                class="mt-6 bg-red-50 border border-red-200 rounded-2xl p-4 text-red-700 text-center"
            >
                {{ error }}
                <button @click="cargarDashboard" class="ml-3 underline">
                    Reintentar
                </button>
            </div>

            <!-- FOOTER -->
            <div class="mt-10 pt-6 border-t text-center text-xs text-slate-400">
                Plataforma SLC • Dashboard actualizado en tiempo real
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Transiciones y animaciones */
.group {
    transition: all 0.3s ease;
}

.group:hover {
    transform: translateY(-2px);
}
</style>
