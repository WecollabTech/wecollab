<script setup>
import { ref, computed, onMounted, watch } from "vue";
import { usePage, router } from "@inertiajs/vue3";

const axios = window.axios;

// 🔷 ESTADO GLOBAL
const page = usePage();
const user = computed(() => page.props.auth?.user);
const tutoriales = ref([]);
const categorias = ref([]);
const subcategorias = ref([]);
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
const categoriaSeleccionada = ref("");
const subcategoriaSeleccionada = ref("");

// 🔷 TIPOS DE MATERIAL
const tipos = [
    { label: "Todo", value: "todo", icon: "📚" },
    { label: "Video", value: "video", icon: "🎬" },
    { label: "Manual", value: "manual", icon: "📘" },
    { label: "Guía", value: "guia", icon: "🗺️" },
    { label: "Infografía", value: "triptico", icon: "📊" },
];

// ============================================
// 🎯 ECOSISTEMAS Y JERARQUÍA DE ROLES
// ============================================

const ROLES_WE_COLLAB = [
    "Superadmin we collab",
    "Admin we collab",
    "Soporte we collab",
    "Usuario we collab",
];

const ROLES_CLIENTE = [
    "Suscriptor SLC",
    "Usuario Admin SLC",
    "Usuario Premium SLC",
    "Usuario Público",
    "Prospecto",
    "Invitado",
];

const JERARQUIA_ROLES = {
    "Superadmin we collab": 100,
    "Admin we collab": 90,
    "Soporte we collab": 80,
    "Usuario we collab": 70,
    "Suscriptor SLC": 60,
    "Usuario Admin SLC": 50,
    "Usuario Premium SLC": 40,
    "Usuario Público": 30,
    Prospecto: 20,
    Invitado: 10,
};

const MAPEO_ROLES = {
    "Superadmin we collab": "Superadmin we collab",
    "Admin we collab": "Admin we collab",
    "Admin We collab": "Admin we collab",
    "Soporte we collab": "Soporte we collab",
    "Usuario we collab": "Usuario we collab",
    "Suscriptor SLC": "Suscriptor SLC",
    "Usuario Admin SLC": "Usuario Admin SLC",
    "Usuario Premium SLC": "Usuario Premium SLC",
    "Usuario Público": "Usuario Público",
    "Usuario Publico": "Usuario Público",
    Prospecto: "Prospecto",
    Invitado: "Invitado",
    "Cliente Admin": "Usuario Admin SLC",
    "Cliente Premium": "Usuario Premium SLC",
};

const ALCANCES_WE_COLLAB = [...ROLES_WE_COLLAB];
const ALCANCES_CLIENTE = [...ROLES_CLIENTE.filter((r) => r !== "Invitado")];
const ALCANCES_VALIDOS = [...ALCANCES_WE_COLLAB, ...ALCANCES_CLIENTE];

const normalize = (str) => str?.toString().toLowerCase().trim() || "";

const getUserRole = () => {
    const role = user.value?.role;
    if (!role) return "Invitado";
    if (typeof role === "object") {
        const roleName =
            role.nombre || role.name || role.slug || role.role || "";
        return MAPEO_ROLES[roleName.trim()] || roleName.trim() || "Invitado";
    }
    return (
        MAPEO_ROLES[role.toString().trim()] ||
        role.toString().trim() ||
        "Invitado"
    );
};

const getRolLevel = (rolNombre) => JERARQUIA_ROLES[rolNombre] || 0;
const esRolWeCollab = () => ROLES_WE_COLLAB.includes(getUserRole());
const esRolCliente = () => ROLES_CLIENTE.includes(getUserRole());
const getEcosistema = () =>
    esRolWeCollab() ? "we_collab" : esRolCliente() ? "cliente" : "desconocido";

const getAlcanceEcosistema = (alcance) => {
    if (ALCANCES_WE_COLLAB.includes(alcance)) return "we_collab";
    if (ALCANCES_CLIENTE.includes(alcance)) return "cliente";
    return "publico";
};

const debeOcultarCompletamente = (tutorial) => {
    const alcanceTutorial = tutorial.alcance;
    if (esRolCliente()) {
        if (!alcanceTutorial || alcanceTutorial.trim() === "") return false;
        const alcanceNorm = MAPEO_ROLES[alcanceTutorial] || alcanceTutorial;
        return getAlcanceEcosistema(alcanceNorm) === "we_collab";
    }
    return false;
};

const tieneAcceso = (tutorial) => {
    const rolUsuario = getUserRole();
    let alcanceTutorial = tutorial.alcance;
    if (!alcanceTutorial || alcanceTutorial.trim() === "") return true;
    alcanceTutorial = MAPEO_ROLES[alcanceTutorial] || alcanceTutorial;
    if (!ALCANCES_VALIDOS.includes(alcanceTutorial)) return false;
    if (
        getEcosistema() === "cliente" &&
        getAlcanceEcosistema(alcanceTutorial) === "we_collab"
    )
        return false;
    if (
        rolUsuario === "Superadmin we collab" ||
        rolUsuario === "Admin we collab"
    )
        return true;
    return getRolLevel(rolUsuario) >= getRolLevel(alcanceTutorial);
};

const estaBloqueado = (tutorial) => {
    const alcanceTutorial = tutorial.alcance;
    if (!alcanceTutorial || alcanceTutorial.trim() === "") return false;
    const alcanceNorm = MAPEO_ROLES[alcanceTutorial] || alcanceTutorial;
    if (
        getEcosistema() === "cliente" &&
        getAlcanceEcosistema(alcanceNorm) === "we_collab"
    )
        return true;
    return !tieneAcceso(tutorial);
};

const filtrados = computed(() => {
    return tutoriales.value.filter((t) => {
        if (debeOcultarCompletamente(t)) return false;
        if (t.estado !== "activo") return false;
        const tipoOK =
            tipoSeleccionado.value === "todo" ||
            normalize(t.tipo_material) === normalize(tipoSeleccionado.value);
        if (!tipoOK) return false;
        const searchTerm = searchDebounced.value.toLowerCase().trim();
        return (
            !searchTerm ||
            t.titulo?.toLowerCase().includes(searchTerm) ||
            t.descripcion?.toLowerCase().includes(searchTerm)
        );
    });
});

const materialesAccesibles = computed(() =>
    filtrados.value.filter((t) => tieneAcceso(t)),
);

const materialesPorCategoria = computed(() => {
    const resultado = {};
    let materialesFiltrados = filtrados.value;
    if (categoriaSeleccionada.value)
        materialesFiltrados = materialesFiltrados.filter(
            (t) => t.categoria_id == categoriaSeleccionada.value,
        );
    if (subcategoriaSeleccionada.value)
        materialesFiltrados = materialesFiltrados.filter(
            (t) => t.subcategoria_id == subcategoriaSeleccionada.value,
        );

    materialesFiltrados.forEach((tutorial) => {
        const catId = tutorial.categoria_id || "sin_categoria";
        const catNom = tutorial.categoria?.nombre || "Sin categoría";
        const subId = tutorial.subcategoria_id || "sin_subcategoria";
        const subNom = tutorial.subcategoria?.nombre || "Sin subcategoría";

        if (!resultado[catId]) {
            resultado[catId] = {
                id: catId,
                nombre: catNom,
                icono: "📚",
                subcategorias: {},
            };
        }
        if (!resultado[catId].subcategorias[subId]) {
            resultado[catId].subcategorias[subId] = {
                id: subId,
                nombre: subNom,
                items: [],
            };
        }
        resultado[catId].subcategorias[subId].items.push(tutorial);
    });

    return Object.values(resultado).map((cat) => ({
        ...cat,
        subcategorias: Object.values(cat.subcategorias),
        totalMateriales: Object.values(cat.subcategorias).reduce(
            (total, sub) => total + sub.items.length,
            0,
        ),
    }));
});

const categoriasConContenido = computed(() =>
    materialesPorCategoria.value.filter((cat) => cat.totalMateriales > 0),
);
const subcategoriasFiltradas = computed(() =>
    categoriaSeleccionada.value
        ? subcategorias.value.filter(
              (sub) => sub.categorias_id == categoriaSeleccionada.value,
          )
        : [],
);
const stats = computed(() => ({
    total: tutoriales.value.length,
    visibles: filtrados.value.length,
    accesibles: filtrados.value.filter(tieneAcceso).length,
    restringidos: filtrados.value.filter(estaBloqueado).length,
}));

// ============================================
// 🖼️ UTILIDADES VISUALES
// ============================================

const getIconoPorTipo = (tipo) =>
    ({ video: "🎬", manual: "📘", guia: "🗺️", triptico: "📊" })[tipo] || "📄";

const getNombreTipo = (tipo) =>
    ({
        video: "VIDEO TUTORIAL",
        manual: "MANUAL TÉCNICO",
        guia: "GUÍA PRÁCTICA",
        triptico: "INFOGRAFÍA",
    })[tipo] || "DOCUMENTO";

const getThumbnailVideo = (url) => {
    if (!url) return null;
    let videoId = null;
    if (url.includes("youtu.be/"))
        videoId = url.split("youtu.be/")[1]?.split(/[?&#]/)[0];
    else if (url.includes("v="))
        videoId = url.split("v=")[1]?.split(/[?&#]/)[0];
    else if (url.includes("/embed/"))
        videoId = url.split("/embed/")[1]?.split(/[?&#]/)[0];
    return videoId && videoId.length === 11
        ? `https://img.youtube.com/vi/${videoId}/hqdefault.jpg`
        : null;
};

// ============================================
// CARGA Y LÓGICA
// ============================================

const cargarCategorias = async () => {
    try {
        const res = await axios.get("/categorias/lista");
        if (res.data?.success) categorias.value = res.data.data;
    } catch (e) {}
};
const cargarSubcategorias = async () => {
    try {
        const params = categoriaSeleccionada.value
            ? { categorias_id: categoriaSeleccionada.value }
            : {};
        const res = await axios.get("/subcategorias/all", { params });
        if (res.data?.success) subcategorias.value = res.data.data;
    } catch (e) {}
};
const cargarTutoriales = async () => {
    try {
        error.value = null;
        loading.value = true;
        const res = await axios.get("/tutoriales", { timeout: 10000 });
        tutoriales.value = res.data?.data || res.data || [];
    } catch (err) {
        error.value = "Error de conexión.";
    } finally {
        loading.value = false;
    }
};

const cargarDatosIniciales = () =>
    Promise.all([
        cargarTutoriales(),
        cargarCategorias(),
        cargarSubcategorias(),
    ]);

const mostrarNotificacion = (mensaje, tipo = "success") => {
    toastMessage.value = mensaje;
    toastType.value = tipo;
    showToast.value = true;
    setTimeout(() => {
        showToast.value = false;
    }, 3000);
};

let searchTimeout;
watch(search, (v) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        searchDebounced.value = v;
    }, 300);
});
watch(categoriaSeleccionada, () => {
    subcategoriaSeleccionada.value = "";
});

const verVideo = (recurso) => {
    if (estaBloqueado(recurso))
        return mostrarNotificacion(`🔒 Contenido restringido.`, "error");
    const route =
        {
            video: "videos",
            manual: "manuales",
            guia: "guias",
            triptico: "tripticos",
        }[recurso.tipo_material] || "todos";
    router.visit(`/recursos/${route}/${recurso.id}`);
};

const cambiarVista = (modo) => {
    viewMode.value = modo;
    localStorage.setItem("tutorialViewMode", modo);
};
const resetFiltros = () => {
    search.value = "";
    searchDebounced.value = "";
    tipoSeleccionado.value = "todo";
    categoriaSeleccionada.value = "";
    subcategoriaSeleccionada.value = "";
    mostrarNotificacion("Filtros limpiados", "success");
};

onMounted(cargarDatosIniciales);
</script>

<template>
    <div
        class="min-h-screen bg-white text-gray-800 font-['Helvetica_Neue',_Helvetica,_Arial,_sans-serif] relative overflow-x-hidden"
    >
        <!-- Textura de fondo WE COLLAB -->
        <div
            class="fixed inset-0 pointer-events-none opacity-[0.03]"
            style="
                background-image: radial-gradient(
                    #003366 0.8px,
                    transparent 0.8px
                );
                background-size: 28px 28px;
            "
        ></div>

        <!-- HEADER -->
        <header
            class="sticky top-0 z-50 bg-white/98 backdrop-blur-sm border-b border-gray-200 px-6 py-4 shadow-sm"
        >
            <div
                class="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between gap-4"
            >
                <div class="flex items-center gap-4 group">
                    <!-- <div
                        class="w-10 h-10 bg-[#003366] rounded-lg flex items-center justify-center transition-all group-hover:shadow-md"
                    >
                        <span class="text-white font-['Jura'] font-bold text-xl"
                            >WC</span
                        >
                    </div> -->
                    <div>
                        <h1
                            class="text-xl font-['Jura'] font-bold text-[#003366] tracking-tight"
                        >
                            WE COLLAB | Biblioteca de Contenido
                        </h1>
                        <p
                            class="text-[11px] uppercase tracking-wider text-[#003366]/60 font-semibold"
                        >
                            Más cercanos, más eficientes
                        </p>
                    </div>
                </div>

                <div
                    class="flex flex-wrap items-center justify-center gap-4 w-full md:w-auto"
                >
                    <div
                        class="hidden lg:block text-right pr-4 border-r border-gray-200"
                    >
                        <p class="text-xs text-gray-500">👋 Bienvenido</p>
                        <p class="text-sm font-bold text-gray-800">
                            {{ user?.name || "Invitado" }}
                            <span
                                v-if="esRolWeCollab()"
                                class="ml-2 text-[10px] font-medium text-[#003366] bg-[#003366]/10 px-2 py-0.5 rounded-full"
                                >We Collab</span
                            >
                        </p>
                    </div>

                    <div class="relative w-full sm:w-64">
                        <input
                            v-model="search"
                            type="text"
                            placeholder="Buscar contenido..."
                            class="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-300 rounded-md text-sm outline-none focus:border-[#003366] focus:ring-1 focus:ring-[#003366] transition-all"
                        />
                        <span class="absolute left-3 top-2.5 text-gray-400"
                            >🔍</span
                        >
                    </div>

                    <div
                        class="flex bg-gray-100 p-1 rounded-md border border-gray-200"
                    >
                        <button
                            @click="cambiarVista('grid')"
                            :class="[
                                'p-1.5 rounded transition-all',
                                viewMode === 'grid'
                                    ? 'bg-white shadow-sm text-[#003366]'
                                    : 'text-gray-400 hover:text-gray-600',
                            ]"
                            title="Vista cuadrícula"
                        >
                            <svg
                                class="w-5 h-5"
                                fill="currentColor"
                                viewBox="0 0 20 20"
                            >
                                <path
                                    d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"
                                />
                            </svg>
                        </button>
                        <button
                            @click="cambiarVista('list')"
                            :class="[
                                'p-1.5 rounded transition-all',
                                viewMode === 'list'
                                    ? 'bg-white shadow-sm text-[#003366]'
                                    : 'text-gray-400 hover:text-gray-600',
                            ]"
                            title="Vista lista"
                        >
                            <svg
                                class="w-5 h-5"
                                fill="currentColor"
                                viewBox="0 0 20 20"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </header>

        <!-- BARRA DE FILTROS -->
        <section
            class="bg-white border-b border-gray-200 py-3 px-6 sticky top-[73px] z-40"
        >
            <div
                class="max-w-7xl mx-auto flex flex-wrap items-center justify-between gap-4"
            >
                <div class="flex flex-wrap gap-2">
                    <button
                        v-for="t in tipos"
                        :key="t.value"
                        @click="tipoSeleccionado = t.value"
                        :class="[
                            'px-4 py-1.5 rounded-full text-xs font-semibold transition-all border',
                            tipoSeleccionado === t.value
                                ? 'bg-[#003366] border-[#003366] text-white shadow-sm'
                                : 'bg-white border-gray-300 text-gray-600 hover:bg-gray-50 hover:border-gray-400',
                        ]"
                    >
                        <span class="mr-1.5">{{ t.icon }}</span>
                        {{ t.label }}
                    </button>
                    <button
                        v-if="
                            search ||
                            tipoSeleccionado !== 'todo' ||
                            categoriaSeleccionada ||
                            subcategoriaSeleccionada
                        "
                        @click="resetFiltros"
                        class="px-4 py-1.5 rounded-full text-xs font-medium text-gray-500 hover:text-[#003366] hover:bg-gray-100 transition-all"
                    >
                        ✕ Limpiar filtros
                    </button>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <select
                        v-model="categoriaSeleccionada"
                        @change="cargarSubcategorias"
                        class="pl-3 pr-8 py-2 bg-gray-50 border border-gray-300 rounded-md text-xs font-medium focus:border-[#003366] focus:ring-1 focus:ring-[#003366] outline-none appearance-none cursor-pointer"
                    >
                        <option value="">Todas las categorías</option>
                        <option
                            v-for="cat in categorias"
                            :key="cat.id"
                            :value="cat.id"
                        >
                            {{ cat.nombre }}
                        </option>
                    </select>

                    <select
                        v-model="subcategoriaSeleccionada"
                        :disabled="!categoriaSeleccionada"
                        class="pl-3 pr-8 py-2 bg-gray-50 border border-gray-300 rounded-md text-xs font-medium focus:border-[#003366] focus:ring-1 focus:ring-[#003366] outline-none appearance-none cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed"
                    >
                        <option value="">Subcategorías</option>
                        <option
                            v-for="sub in subcategoriasFiltradas"
                            :key="sub.id"
                            :value="sub.id"
                        >
                            {{ sub.nombre }}
                        </option>
                    </select>

                    <!-- Stats compactos -->
                    <div
                        class="hidden lg:flex items-center gap-3 text-[10px] text-gray-400"
                    >
                        <span class="flex items-center gap-1">
                            <span
                                class="w-1.5 h-1.5 bg-gray-400 rounded-full"
                            ></span>
                            Total: {{ stats.total }}
                        </span>
                        <span class="flex items-center gap-1">
                            <span
                                class="w-1.5 h-1.5 bg-[#003366] rounded-full"
                            ></span>
                            Accesibles: {{ stats.accesibles }}
                        </span>
                    </div>
                </div>
            </div>
        </section>

        <!-- CONTENIDO PRINCIPAL -->
        <main class="max-w-7xl mx-auto px-6 py-10 relative">
            <!-- ERROR -->
            <div
                v-if="error"
                class="bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-lg mb-8 flex items-center justify-between"
            >
                <div class="flex items-center gap-3">
                    <span class="text-xl">⚠️</span>
                    <span class="text-sm">{{ error }}</span>
                </div>
                <button
                    @click="cargarDatosIniciales"
                    class="text-sm font-semibold text-red-700 hover:text-red-800 underline"
                >
                    Reintentar
                </button>
            </div>

            <!-- SECCIÓN DESTACADOS (solo cuando no hay filtros activos) -->
            <div
                v-if="
                    !loading &&
                    materialesAccesibles.length > 0 &&
                    !search &&
                    tipoSeleccionado === 'todo' &&
                    !categoriaSeleccionada &&
                    !subcategoriaSeleccionada
                "
                class="mb-16"
            >
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-1 h-8 bg-[#CC6600] rounded-full"></div>
                    <h2 class="text-2xl font-['Jura'] font-bold text-[#003366]">
                        ⭐ Destacados para ti
                    </h2>
                    <span
                        class="px-2 py-0.5 bg-gray-100 text-[#003366] text-[10px] font-bold rounded-full"
                        >{{
                            Math.min(materialesAccesibles.length, 4)
                        }}
                        recursos</span
                    >
                </div>

                <div
                    class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6"
                >
                    <div
                        v-for="recurso in materialesAccesibles.slice(0, 4)"
                        :key="'feat-' + recurso.id"
                        @click="verVideo(recurso)"
                        class="group bg-white border border-gray-200 rounded-lg overflow-hidden cursor-pointer hover:shadow-md hover:-translate-y-1 transition-all duration-300"
                    >
                        <div
                            class="aspect-video bg-[#003366] relative overflow-hidden"
                        >
                            <img
                                v-if="getThumbnailVideo(recurso.url)"
                                :src="getThumbnailVideo(recurso.url)"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                @error="
                                    $event.target.src =
                                        'data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22%3E%3Crect width=%22100%22 height=%22100%22 fill=%22%23003366%22/%3E%3Ctext x=%2250%22 y=%2255%22 text-anchor=%22middle%22 fill=%22white%22 font-size=%2240%22%3E' +
                                        getIconoPorTipo(recurso.tipo_material) +
                                        '%3C/text%3E%3C/svg%3E'
                                "
                            />
                            <div
                                v-else
                                class="w-full h-full flex items-center justify-center text-5xl bg-gradient-to-br from-[#003366] to-[#002244]"
                            >
                                {{ getIconoPorTipo(recurso.tipo_material) }}
                            </div>
                            <div
                                class="absolute top-2 right-2 px-2 py-0.5 bg-[#CC6600] text-white text-[9px] font-bold rounded shadow-sm"
                            >
                                ⭐ Destacado
                            </div>
                            <div
                                class="absolute bottom-0 left-0 right-0 h-1 bg-[#CC6600] transform scale-x-0 group-hover:scale-x-100 transition-transform origin-left"
                            ></div>
                        </div>
                        <div class="p-4">
                            <h3
                                class="font-bold text-gray-800 text-sm line-clamp-1 mb-2 group-hover:text-[#003366] transition-colors"
                            >
                                {{ recurso.titulo }}
                            </h3>
                            <p
                                class="text-xs text-gray-500 line-clamp-2 leading-relaxed mb-4"
                            >
                                {{ recurso.descripcion || "Sin descripción" }}
                            </p>
                            <button
                                class="w-full py-2 bg-[#003366] hover:bg-[#002244] text-white text-[11px] font-bold rounded-md transition-all active:scale-95 uppercase tracking-wide"
                            >
                                Ver contenido →
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CONTENIDO ORGANIZADO POR CATEGORÍAS -->
            <div
                v-if="!loading && categoriasConContenido.length > 0"
                class="space-y-12"
            >
                <div
                    v-for="cat in categoriasConContenido"
                    :key="cat.id"
                    class="relative"
                >
                    <!-- Cabecera de categoría -->
                    <div
                        class="flex items-center gap-3 mb-6 pb-3 border-b border-gray-100"
                    >
                        <div
                            class="w-10 h-10 rounded-lg bg-gray-50 flex items-center justify-center text-xl border border-gray-100"
                        >
                            {{ cat.icono }}
                        </div>
                        <h2
                            class="text-xl font-['Jura'] font-bold text-gray-800 tracking-tight"
                        >
                            {{ cat.nombre }}
                        </h2>
                        <span
                            class="ml-auto text-[10px] font-semibold text-gray-400 tracking-wide"
                            >{{ cat.totalMateriales }} recursos</span
                        >
                    </div>

                    <!-- Subcategorías -->
                    <div
                        v-for="sub in cat.subcategorias"
                        :key="sub.id"
                        class="mb-10 last:mb-0"
                    >
                        <div class="flex items-center gap-3 mb-5">
                            <div class="w-5 h-5 text-gray-400 text-sm">📁</div>
                            <h3
                                class="text-xs font-bold text-gray-500 uppercase tracking-wider"
                            >
                                {{ sub.nombre }}
                            </h3>
                            <div
                                class="flex-1 h-px bg-gradient-to-r from-gray-200 to-transparent"
                            ></div>
                            <span class="text-[9px] text-gray-400 font-medium"
                                >{{ sub.items.length }} items</span
                            >
                        </div>

                        <!-- VISTA GRID -->
                        <div
                            v-if="viewMode === 'grid'"
                            class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 gap-5"
                        >
                            <div
                                v-for="item in sub.items"
                                :key="item.id"
                                @click="verVideo(item)"
                                class="group bg-white border border-gray-200 rounded-lg overflow-hidden cursor-pointer hover:shadow-md hover:-translate-y-1 transition-all duration-300 flex flex-col"
                            >
                                <div
                                    class="aspect-video bg-[#003366] relative overflow-hidden flex-shrink-0"
                                >
                                    <img
                                        v-if="getThumbnailVideo(item.url)"
                                        :src="getThumbnailVideo(item.url)"
                                        class="w-full h-full object-cover opacity-90 group-hover:opacity-100 group-hover:scale-105 transition-all duration-500"
                                        @error="
                                            $event.target.src =
                                                'data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22%3E%3Crect width=%22100%22 height=%22100%22 fill=%22%23003366%22/%3E%3Ctext x=%2250%22 y=%2255%22 text-anchor=%22middle%22 fill=%22white%22 font-size=%2240%22%3E' +
                                                getIconoPorTipo(
                                                    item.tipo_material,
                                                ) +
                                                '%3C/text%3E%3C/svg%3E'
                                        "
                                    />
                                    <div
                                        v-else
                                        class="w-full h-full flex items-center justify-center text-4xl bg-gradient-to-br from-[#003366] to-[#002244]"
                                    >
                                        {{
                                            getIconoPorTipo(item.tipo_material)
                                        }}
                                    </div>
                                    <div
                                        v-if="estaBloqueado(item)"
                                        class="absolute inset-0 bg-black/60 backdrop-blur-[2px] flex items-center justify-center"
                                    >
                                        <span
                                            class="text-white text-sm font-bold px-2 py-1 bg-black/50 rounded"
                                            >🔒 Restringido</span
                                        >
                                    </div>
                                    <div
                                        class="absolute top-2 right-2 px-2 py-0.5 bg-black/50 backdrop-blur-sm text-white text-[9px] font-bold rounded"
                                    >
                                        {{ getNombreTipo(item.tipo_material) }}
                                    </div>
                                </div>
                                <div class="p-3 flex flex-col flex-1">
                                    <h4
                                        class="font-bold text-gray-800 text-xs line-clamp-2 group-hover:text-[#003366] mb-2 transition-colors"
                                        :class="
                                            estaBloqueado(item)
                                                ? 'text-gray-400 group-hover:text-gray-400'
                                                : ''
                                        "
                                    >
                                        {{ item.titulo }}
                                    </h4>
                                    <div
                                        class="mt-auto flex items-center justify-between pt-2 border-t border-gray-50"
                                    >
                                        <span
                                            class="text-[9px] font-medium text-gray-400 bg-gray-50 px-2 py-0.5 rounded"
                                        >
                                            {{ item.alcance || "Público" }}
                                        </span>
                                        <svg
                                            class="w-4 h-4 text-[#003366] opacity-0 group-hover:opacity-100 transition-all -translate-x-2 group-hover:translate-x-0"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2.5"
                                                d="M9 5l7 7-7 7"
                                            />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- VISTA LISTA - Botón siempre visible -->
                        <div v-else class="space-y-2">
                            <div
                                v-for="item in sub.items"
                                :key="item.id"
                                @click="verVideo(item)"
                                class="flex items-center gap-4 p-3 bg-white border border-gray-200 rounded-lg hover:shadow-sm hover:border-[#003366]/30 cursor-pointer group transition-all"
                            >
                                <div
                                    class="w-10 h-10 bg-[#003366] rounded-md flex-shrink-0 flex items-center justify-center text-lg transition-transform group-hover:scale-95"
                                >
                                    {{ getIconoPorTipo(item.tipo_material) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4
                                        class="font-bold text-sm text-gray-800 truncate group-hover:text-[#003366] transition-colors"
                                        :class="
                                            estaBloqueado(item)
                                                ? 'text-gray-400 group-hover:text-gray-400'
                                                : ''
                                        "
                                    >
                                        {{ item.titulo }}
                                    </h4>
                                    <p class="text-xs text-gray-500 truncate">
                                        {{
                                            item.descripcion ||
                                            "Sin descripción"
                                        }}
                                    </p>
                                </div>
                                <div class="hidden sm:flex items-center gap-3">
                                    <span
                                        class="text-[9px] font-semibold text-gray-400 uppercase tracking-wider"
                                        >{{
                                            getNombreTipo(item.tipo_material)
                                        }}</span
                                    >
                                    <span
                                        class="text-[9px] font-medium text-gray-400 bg-gray-50 px-2 py-0.5 rounded"
                                    >
                                        {{ item.alcance || "Público" }}
                                    </span>
                                    <!-- ✅ Botón SIEMPRE visible (sin opacidad 0) -->
                                    <button
                                        class="px-4 py-1.5 bg-[#003366] text-white text-[10px] font-bold rounded-md shadow-sm transition-all uppercase tracking-wider hover:bg-[#002244]"
                                        :class="
                                            estaBloqueado(item)
                                                ? 'bg-gray-400 cursor-not-allowed hover:bg-gray-400'
                                                : ''
                                        "
                                        :disabled="estaBloqueado(item)"
                                    >
                                        {{
                                            estaBloqueado(item)
                                                ? "🔒 Sin acceso"
                                                : "Abrir"
                                        }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Skeleton Loading -->
            <div
                v-if="loading"
                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 gap-6"
            >
                <div
                    v-for="n in 10"
                    :key="n"
                    class="bg-gray-50 rounded-lg p-4 space-y-3 border border-gray-100"
                >
                    <div
                        class="aspect-video bg-gray-200 rounded-lg animate-pulse"
                    ></div>
                    <div
                        class="h-3 bg-gray-200 rounded w-3/4 animate-pulse"
                    ></div>
                    <div
                        class="h-2 bg-gray-100 rounded w-full animate-pulse"
                    ></div>
                    <div
                        class="h-2 bg-gray-100 rounded w-1/2 animate-pulse"
                    ></div>
                </div>
            </div>

            <!-- Mensaje cliente sin contenido -->
            <div
                v-else-if="
                    esRolCliente() &&
                    filtrados.length === 0 &&
                    tutoriales.length > 0
                "
                class="flex flex-col items-center justify-center py-24 text-center"
            >
                <div
                    class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center text-4xl mb-6 border border-gray-100"
                >
                    🔒
                </div>
                <h3 class="text-xl font-['Jura'] font-bold text-gray-800 mb-2">
                    Contenido exclusivo para We Collab
                </h3>
                <p class="text-sm text-gray-500 max-w-md mx-auto">
                    Los materiales que buscas son parte del ecosistema interno
                    de WE COLLAB.
                    <br />Tu rol actual:
                    <span class="font-medium text-[#003366]">{{
                        getUserRole()
                    }}</span>
                </p>
            </div>

            <!-- Sin resultados -->
            <div
                v-else-if="
                    !loading &&
                    categoriasConContenido.length === 0 &&
                    materialesAccesibles.length === 0 &&
                    (search ||
                        tipoSeleccionado !== 'todo' ||
                        categoriaSeleccionada ||
                        subcategoriaSeleccionada)
                "
                class="flex flex-col items-center justify-center py-24 text-center"
            >
                <div
                    class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center text-4xl mb-6 border border-gray-100"
                >
                    🔍
                </div>
                <h3 class="text-xl font-['Jura'] font-bold text-gray-800 mb-2">
                    No se encontraron resultados
                </h3>
                <p class="text-sm text-gray-500 max-w-sm mx-auto mb-6">
                    No hay recursos que coincidan con "{{
                        search || "los filtros aplicados"
                    }}"
                </p>
                <button
                    @click="resetFiltros"
                    class="px-6 py-2.5 bg-[#003366] text-white text-xs font-bold rounded-md hover:bg-[#002244] transition-all uppercase tracking-wide"
                >
                    Limpiar filtros
                </button>
            </div>
        </main>

        <!-- FOOTER WE COLLAB -->
        <footer class="mt-20 bg-white border-t border-gray-200 py-10 px-6">
            <div class="max-w-7xl mx-auto flex flex-col items-center">
                <div
                    class="w-10 h-10 bg-[#003366] rounded-lg flex items-center justify-center mb-5"
                >
                    <span class="text-white font-['Jura'] font-bold text-lg"
                        >WC</span
                    >
                </div>
                <p class="text-sm text-gray-600 mb-2 text-center italic">
                    "La excelencia en el servicio es y seguirá siendo la
                    prioridad"
                </p>
                <p
                    class="text-[10px] font-bold text-[#003366] tracking-[0.2em] uppercase mb-6"
                >
                    WE COLLAB · Más cercanos, más eficientes
                </p>

                <div
                    class="w-full pt-6 border-t border-gray-100 flex flex-col md:flex-row justify-between items-center gap-3 text-[9px] text-gray-400 font-medium uppercase tracking-wider"
                >
                    <p>&copy; 2026 WE COLLAB · Todos los derechos reservados</p>
                    <div class="flex gap-5">
                        <a
                            href="#"
                            class="hover:text-[#003366] transition-colors"
                            >Términos</a
                        >
                        <a
                            href="#"
                            class="hover:text-[#003366] transition-colors"
                            >Privacidad</a
                        >
                        <a
                            href="#"
                            class="hover:text-[#003366] transition-colors"
                            >Soporte</a
                        >
                    </div>
                </div>
            </div>
        </footer>

        <!-- TOAST NOTIFICACIONES -->
        <Teleport to="body">
            <Transition name="toast">
                <div
                    v-if="showToast"
                    :class="[
                        'fixed bottom-6 right-6 z-[100] px-5 py-3 rounded-lg shadow-lg border-l-4 flex items-center gap-3 transition-all',
                        toastType === 'success'
                            ? 'bg-white border-[#003366] text-gray-800'
                            : 'bg-red-50 border-red-500 text-red-700',
                    ]"
                >
                    <div
                        :class="[
                            'w-7 h-7 rounded-full flex items-center justify-center text-sm font-bold',
                            toastType === 'success'
                                ? 'bg-[#003366]/10 text-[#003366]'
                                : 'bg-red-100 text-red-600',
                        ]"
                    >
                        {{ toastType === "success" ? "✓" : "!" }}
                    </div>
                    <div>
                        <p
                            class="text-[10px] font-black uppercase tracking-wider"
                        >
                            {{ toastType === "success" ? "Éxito" : "Atención" }}
                        </p>
                        <p class="text-sm font-medium">{{ toastMessage }}</p>
                    </div>
                </div>
            </Transition>
        </Teleport>
    </div>
</template>

<style scoped>
/* Fuente Jura para títulos */
@import url("https://fonts.googleapis.com/css2?family=Jura:wght@600;700&display=swap");

/* Animaciones */
.toast-enter-active,
.toast-leave-active {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.toast-enter-from {
    opacity: 0;
    transform: translateX(60px);
}
.toast-leave-to {
    opacity: 0;
    transform: scale(0.9);
}

/* Líneas truncadas */
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

/* Scrollbar corporativo */
::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}
::-webkit-scrollbar-track {
    background: #f3f4f6;
    border-radius: 10px;
}
::-webkit-scrollbar-thumb {
    background: #003366;
    border-radius: 10px;
}
::-webkit-scrollbar-thumb:hover {
    background: #002244;
}

/* Personalizar selects */
select {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
    background-position: right 0.6rem center;
    background-repeat: no-repeat;
    background-size: 1.2em 1.2em;
    padding-right: 2rem;
}

/* Eliminar outline por defecto */
*:focus {
    outline: none;
}

/* Mejorar contraste en hover */
button {
    cursor: pointer;
}
</style>
