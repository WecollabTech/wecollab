<script setup>
import { ref, onMounted, onUnmounted, computed } from "vue";
import { Link, router, usePage } from "@inertiajs/vue3";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import NavLink from "@/Components/NavLink.vue";

const props = defineProps({
    showingNavigationDropdown: Boolean,
});

const emit = defineEmits(["update:showingNavigationDropdown"]);

// Obtener página de Inertia
const page = usePage();

// ============================================
// 🔐 CONFIGURACIÓN DE PERMISOS POR ROL
// ============================================
const ROLE_PERMISSIONS = {
    Superadministrador: {
        tutorial: true,
        usuarios: true,
        configuracion: true,
        items: {
            tutorial: [
                "categorias",
                "subcategorias",
                "tutoriales",
                "comentarios",
            ],
            usuarios: ["crear", "editar", "eliminar"],
            configuracion: ["permisos", "roles", "reportes"],
        },
    },
    Administrador: {
        tutorial: true,
        usuarios: true,
        configuracion: true,
        items: {
            tutorial: [
                "categorias",
                "subcategorias",
                "tutoriales",
                "comentarios",
            ],
            usuarios: ["crear", "editar"],
            configuracion: ["permisos", "roles", "reportes"],
        },
    },
    ClienteAdmin: {
        tutorial: true,
        usuarios: false,
        configuracion: false,
        items: {
            tutorial: ["categorias", "subcategorias", "tutoriales"],
        },
    },
    ClientSuscriptor: {
        tutorial: true,
        usuarios: false,
        configuracion: false,
        items: {
            tutorial: ["tutoriales", "comentarios"],
        },
    },
    UsuarioPúblico: {
        tutorial: false,
        usuarios: false,
        configuracion: false,
        items: {},
    },
    Prospecto: {
        tutorial: false,
        usuarios: false,
        configuracion: false,
        items: {},
    },
    usuario: {
        tutorial: true,
        usuarios: false,
        configuracion: false,
        items: {
            tutorial: ["tutoriales"],
        },
    },
};

// ============================================
// 🎯 ESTADO DEL MENÚ
// ============================================
const isSubmenuOpen = ref({
    tutorial: false,
    usuarios: false,
    configuracion: false,
});

const toggleSubmenu = (menu) => {
    Object.keys(isSubmenuOpen.value).forEach((key) => {
        if (key !== menu) {
            isSubmenuOpen.value[key] = false;
        }
    });
    isSubmenuOpen.value[menu] = !isSubmenuOpen.value[menu];
};

const logout = () => {
    router.post(route("logout"));
};

const handleResize = () => {
    if (window.innerWidth >= 1024) {
        emit("update:showingNavigationDropdown", false);
    }
};

const handleKeydown = (e) => {
    if (e.key === "Escape" && props.showingNavigationDropdown) {
        emit("update:showingNavigationDropdown", false);
    }
};

// ============================================
// 👤 ROL DEL USUARIO ACTUAL
// ============================================
const userRole = computed(() => {
    const auth = page.props.auth;
    return (
        auth?.user?.role?.nombre ||
        auth?.user?.rol?.nombre ||
        auth?.user?.role_name ||
        "usuario"
    );
});

// ============================================
// 🔍 VERIFICACIÓN DE PERMISOS
// ============================================
const canAccessSection = (section) => {
    const permissions = ROLE_PERMISSIONS[userRole.value];
    return permissions?.[section] === true;
};

const canAccessItem = (section, itemKey) => {
    const permissions = ROLE_PERMISSIONS[userRole.value];
    return permissions?.items?.[section]?.includes(itemKey) || false;
};

// ============================================
// 📋 ITEMS DEL MENÚ
// ============================================
const tutorialItems = computed(() => [
    {
        href: "/categorias",
        icon: "fa-layer-group",
        label: "Categorías",
        key: "categorias",
    },
    {
        href: "/subcategorias",
        icon: "fa-sitemap",
        label: "Subcategorías",
        key: "subcategorias",
    },
    {
        href: "/tutoriales",
        icon: "fa-video",
        label: "Videos Capacitación",
        key: "tutoriales",
    },
    {
        href: "/comentarios",
        icon: "fa-comments",
        label: "Comentarios",
        key: "comentarios",
    },
]);

const usuariosItems = computed(() => [
    {
        href: "/usuarios/create",
        icon: "fa-user-plus",
        label: "Crear Usuario",
        key: "crear",
    },
    {
        href: "/usuarios",
        icon: "fa-user-edit",
        label: "Editar Usuario",
        key: "editar",
    },
    {
        href: "/usuarios/eliminar",
        icon: "fa-user-times",
        label: "Eliminar Usuario",
        key: "eliminar",
    },
]);

const configuracionItems = computed(() => [
    { href: "/permisos", icon: "fa-key", label: "Permisos", key: "permisos" },
    { href: "/roles", icon: "fa-user-shield", label: "Roles", key: "roles" },
    {
        href: "/reportes",
        icon: "fa-chart-bar",
        label: "Reportes",
        key: "reportes",
    },
]);

// ============================================
// 🎭 ITEMS FILTRADOS POR PERMISOS
// ============================================
const filteredTutorialItems = computed(() => {
    return tutorialItems.value.filter((item) =>
        canAccessItem("tutorial", item.key),
    );
});

const filteredUsuariosItems = computed(() => {
    if (!canAccessSection("usuarios")) return [];
    return usuariosItems.value.filter((item) =>
        canAccessItem("usuarios", item.key),
    );
});

const filteredConfigItems = computed(() => {
    if (!canAccessSection("configuracion")) return [];
    return configuracionItems.value.filter((item) =>
        canAccessItem("configuracion", item.key),
    );
});

// ============================================
// 🔄 EVENTOS DEL CICLO DE VIDA
// ============================================
onMounted(() => {
    window.addEventListener("resize", handleResize);
    window.addEventListener("keydown", handleKeydown);
});

onUnmounted(() => {
    window.removeEventListener("resize", handleResize);
    window.removeEventListener("keydown", handleKeydown);
});
</script>

<template>
    <aside
        :class="{
            'translate-x-0': showingNavigationDropdown,
            '-translate-x-full': !showingNavigationDropdown,
        }"
        class="fixed inset-y-0 left-0 z-50 w-72 bg-gradient-to-b from-[#1a3080] to-[#223e9c] shadow-2xl transform transition-all duration-300 ease-out lg:translate-x-0 lg:static lg:inset-0 flex flex-col"
    >
        <!-- ✨ HEADER CON LOGO -->
        <div
            class="flex items-center justify-between h-20 px-6 border-b border-white/10 bg-white/5 backdrop-blur-sm shrink-0"
        >
            <Link
                :href="route('dashboard')"
                class="flex items-center gap-3 group"
            >
                <!-- Logo con efecto glow -->
                <div class="relative">
                    <div
                        class="absolute inset-0 bg-cyan-400/30 rounded-xl blur-xl opacity-0 group-hover:opacity-100 transition-opacity duration-500"
                    ></div>
                    <div
                        class="relative w-11 h-11 bg-white rounded-xl flex items-center justify-center shadow-lg shadow-black/20 group-hover:scale-110 group-hover:rotate-3 transition-all duration-300"
                    >
                        <img
                            src="/img/Logo.png"
                            alt="SLC"
                            class="h-8 w-auto object-contain"
                        />
                    </div>
                </div>
                <!-- Texto del logo -->
                <div
                    class="group-hover:translate-x-1 transition-transform duration-300"
                >
                    <span class="text-2xl font-bold text-white tracking-tight"
                        >SLC</span
                    >
                    <p
                        class="text-xs text-cyan-300 -mt-0.5 font-medium tracking-wide"
                    >
                        we collab
                    </p>
                </div>
            </Link>

            <!-- Botón cerrar móvil -->
            <button
                class="lg:hidden p-2 text-white/70 hover:text-white hover:bg-white/10 rounded-xl transition-all duration-200 hover:rotate-90"
                @click="emit('update:showingNavigationDropdown', false)"
                type="button"
                aria-label="Cerrar menú"
            >
                <svg
                    class="w-5 h-5"
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

        <!-- 🧭 NAVIGATION SCROLLABLE -->
        <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
            <!-- 🔹 DASHBOARD (Siempre visible) -->
            <div class="mb-4">
                <NavLink
                    :href="route('dashboard')"
                    :active="route().current('dashboard')"
                    class="group relative flex items-center gap-3 px-4 py-3.5 rounded-xl text-sm font-semibold transition-all duration-300 text-white hover:bg-white/10"
                    :class="{
                        'bg-gradient-to-r from-cyan-500/20 to-blue-500/20 border border-cyan-400/30 shadow-lg shadow-cyan-500/10':
                            route().current('dashboard'),
                    }"
                >
                    <!-- Indicador activo lateral -->
                    <div
                        v-if="route().current('dashboard')"
                        class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-8 bg-gradient-to-b from-cyan-400 to-blue-400 rounded-r-full"
                    ></div>

                    <div class="relative">
                        <i
                            class="fas fa-home w-5 h-5 transition-all duration-300"
                            :class="
                                route().current('dashboard')
                                    ? 'text-cyan-400 scale-110'
                                    : 'text-white/70 group-hover:text-white group-hover:scale-110'
                            "
                        ></i>
                    </div>
                    <span class="flex-1">Inicio</span>

                    <!-- Punto animado para activo -->
                    <span v-if="route().current('dashboard')" class="relative">
                        <span
                            class="absolute inset-0 bg-cyan-400 rounded-full animate-ping opacity-75"
                        ></span>
                        <span
                            class="relative w-2 h-2 bg-cyan-400 rounded-full"
                        ></span>
                    </span>
                </NavLink>
            </div>

            <!-- ✦ SEPARATOR Y TÍTULO DE SECCIÓN -->
            <div class="px-4 py-2">
                <div
                    class="h-px bg-gradient-to-r from-transparent via-white/20 to-transparent"
                ></div>
                <p
                    class="text-[10px] font-semibold text-white/40 uppercase tracking-widest mt-3 mb-1"
                >
                    Menú Principal
                </p>
            </div>

            <!-- 🎓 SECCIÓN TUTORIAL (Con permisos) -->
            <div v-if="canAccessSection('tutorial')" class="mb-1">
                <button
                    v-if="filteredTutorialItems.length > 0"
                    @click="toggleSubmenu('tutorial')"
                    class="w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm font-medium text-white/90 hover:bg-white/10 hover:text-white transition-all duration-200 group"
                    type="button"
                    :aria-expanded="isSubmenuOpen.tutorial"
                >
                    <div class="flex items-center gap-3">
                        <div
                            class="w-8 h-8 rounded-lg bg-violet-500/20 flex items-center justify-center border border-violet-400/20"
                        >
                            <i
                                class="fas fa-columns w-4 h-4 text-violet-400"
                            ></i>
                        </div>
                        <span>Tutorial</span>
                    </div>
                    <i
                        class="fas fa-chevron-down w-4 h-4 text-white/40 transition-transform duration-300 group-hover:text-white/70"
                        :class="{ 'rotate-180': isSubmenuOpen.tutorial }"
                    ></i>
                </button>

                <!-- Submenu con animación -->
                <div
                    v-show="
                        isSubmenuOpen.tutorial &&
                        filteredTutorialItems.length > 0
                    "
                    class="mt-1 ml-4 pl-4 border-l-2 border-white/10 space-y-1 overflow-hidden transition-all duration-300"
                >
                    <NavLink
                        v-for="item in filteredTutorialItems"
                        :key="item.href"
                        :href="item.href"
                        :active="route().current(item.href.replace('/', ''))"
                        class="group flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm text-white/70 hover:bg-white/10 hover:text-white hover:translate-x-1 transition-all duration-200"
                    >
                        <i
                            :class="`fas ${item.icon} w-4 h-4 text-white/50 group-hover:text-cyan-400 transition-colors`"
                        ></i>
                        <span>{{ item.label }}</span>
                    </NavLink>
                </div>
            </div>

            <!-- 👥 SECCIÓN USUARIOS (Con permisos) -->
            <div v-if="canAccessSection('usuarios')" class="mb-1">
                <button
                    v-if="filteredUsuariosItems.length > 0"
                    @click="toggleSubmenu('usuarios')"
                    class="w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm font-medium text-white/90 hover:bg-white/10 hover:text-white transition-all duration-200 group"
                    type="button"
                    :aria-expanded="isSubmenuOpen.usuarios"
                >
                    <div class="flex items-center gap-3">
                        <div
                            class="w-8 h-8 rounded-lg bg-emerald-500/20 flex items-center justify-center border border-emerald-400/20"
                        >
                            <i
                                class="fas fa-users w-4 h-4 text-emerald-400"
                            ></i>
                        </div>
                        <span>Usuarios</span>
                    </div>
                    <i
                        class="fas fa-chevron-down w-4 h-4 text-white/40 transition-transform duration-300 group-hover:text-white/70"
                        :class="{ 'rotate-180': isSubmenuOpen.usuarios }"
                    ></i>
                </button>

                <div
                    v-show="
                        isSubmenuOpen.usuarios &&
                        filteredUsuariosItems.length > 0
                    "
                    class="mt-1 ml-4 pl-4 border-l-2 border-white/10 space-y-1 overflow-hidden transition-all duration-300"
                >
                    <NavLink
                        v-for="item in filteredUsuariosItems"
                        :key="item.href"
                        :href="item.href"
                        :active="route().current('usuarios')"
                        class="group flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm text-white/70 hover:bg-white/10 hover:text-white hover:translate-x-1 transition-all duration-200"
                        :class="{
                            'text-red-400 hover:text-red-300':
                                item.key === 'eliminar',
                        }"
                    >
                        <i
                            :class="[
                                `fas ${item.icon} w-4 h-4 transition-colors`,
                                item.key === 'eliminar'
                                    ? 'text-red-400/80 group-hover:text-red-300'
                                    : 'text-white/50 group-hover:text-cyan-400',
                            ]"
                        >
                        </i>
                        <span>{{ item.label }}</span>
                    </NavLink>
                </div>
            </div>

            <!-- ⚙️ SECCIÓN CONFIGURACIÓN (Con permisos) -->
            <div v-if="canAccessSection('configuracion')" class="mb-1">
                <button
                    v-if="filteredConfigItems.length > 0"
                    @click="toggleSubmenu('configuracion')"
                    class="w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm font-medium text-white/90 hover:bg-white/10 hover:text-white transition-all duration-200 group"
                    type="button"
                    :aria-expanded="isSubmenuOpen.configuracion"
                >
                    <div class="flex items-center gap-3">
                        <div
                            class="w-8 h-8 rounded-lg bg-amber-500/20 flex items-center justify-center border border-amber-400/20"
                        >
                            <i class="fas fa-cog w-4 h-4 text-amber-400"></i>
                        </div>
                        <span>Configuración</span>
                    </div>
                    <i
                        class="fas fa-chevron-down w-4 h-4 text-white/40 transition-transform duration-300 group-hover:text-white/70"
                        :class="{ 'rotate-180': isSubmenuOpen.configuracion }"
                    ></i>
                </button>

                <div
                    v-show="
                        isSubmenuOpen.configuracion &&
                        filteredConfigItems.length > 0
                    "
                    class="mt-1 ml-4 pl-4 border-l-2 border-white/10 space-y-1 overflow-hidden transition-all duration-300"
                >
                    <NavLink
                        v-for="item in filteredConfigItems"
                        :key="item.href"
                        :href="item.href"
                        :active="route().current(item.href.replace('/', ''))"
                        class="group flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm text-white/70 hover:bg-white/10 hover:text-white hover:translate-x-1 transition-all duration-200"
                    >
                        <i
                            :class="`fas ${item.icon} w-4 h-4 text-white/50 group-hover:text-cyan-400 transition-colors`"
                        ></i>
                        <span>{{ item.label }}</span>
                    </NavLink>
                </div>
            </div>

            <!-- ✦ SEPARATOR FINAL -->
            <div class="px-4 py-3 mt-2">
                <div
                    class="h-px bg-gradient-to-r from-transparent via-white/10 to-transparent"
                ></div>
            </div>
        </nav>

        <!-- 👤 FOOTER: PERFIL DE USUARIO -->
        <div
            class="p-4 border-t border-white/10 bg-gradient-to-b from-transparent to-black/20 shrink-0"
        >
            <Dropdown align="right" width="48">
                <template #trigger>
                    <button
                        class="group w-full flex items-center gap-3 px-4 py-3 rounded-xl bg-white/10 hover:bg-white/15 backdrop-blur-sm border border-white/10 hover:border-white/20 transition-all duration-300 hover:shadow-lg hover:shadow-cyan-500/10"
                        type="button"
                    >
                        <!-- Avatar -->
                        <div class="relative">
                            <div
                                class="absolute inset-0 bg-cyan-400/30 rounded-xl blur-md opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                            ></div>

                            <img
                                v-if="
                                    page.props.jetstream
                                        ?.managesProfilePhotos &&
                                    page.props.auth.user?.profile_photo_url
                                "
                                :src="page.props.auth.user.profile_photo_url"
                                :alt="page.props.auth.user.name"
                                class="relative w-11 h-11 rounded-xl object-cover border-2 border-white/20 group-hover:border-cyan-400/50 transition-colors duration-300"
                            />

                            <div
                                v-else
                                class="relative w-11 h-11 rounded-xl bg-gradient-to-br from-cyan-400 to-blue-500 flex items-center justify-center text-white font-bold text-lg shadow-lg"
                            >
                                {{
                                    page.props.auth.user?.name
                                        ?.charAt(0)
                                        ?.toUpperCase() || "U"
                                }}
                            </div>

                            <!-- Online indicator -->
                            <span
                                class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 bg-emerald-400 border-2 border-[#1a3080] rounded-full shadow-lg shadow-emerald-500/30"
                            ></span>
                        </div>

                        <!-- Info usuario -->
                        <div class="flex-1 min-w-0 text-left">
                            <p
                                class="text-sm font-semibold text-white truncate group-hover:text-cyan-300 transition-colors duration-300"
                            >
                                {{ page.props.auth.user?.name || "Usuario" }}
                            </p>
                            <p class="text-xs text-white/60 truncate">
                                {{ userRole }}
                            </p>
                        </div>

                        <svg
                            class="w-4 h-4 text-white/40 group-hover:text-white/70 transition-colors"
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
                    </button>
                </template>

                <template #content>
                    <div class="py-1">
                        <!-- Header del dropdown -->
                        <div
                            class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-blue-50 to-cyan-50 dark:from-gray-800 dark:to-blue-900/20"
                        >
                            <p
                                class="text-sm font-semibold text-gray-900 dark:text-white"
                            >
                                {{ page.props.auth.user?.name }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{ page.props.auth.user?.email }}
                            </p>
                            <span
                                class="inline-flex items-center px-2 py-0.5 mt-1.5 rounded text-xs font-medium bg-cyan-100 text-cyan-800 dark:bg-cyan-900/50 dark:text-cyan-300"
                            >
                                {{ userRole }}
                            </span>
                        </div>

                        <DropdownLink
                            :href="route('profile.show')"
                            class="flex items-center gap-2 text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-gray-800"
                        >
                            <i class="fas fa-user w-4 h-4 text-blue-500"></i>
                            Perfil
                        </DropdownLink>

                        <DropdownLink
                            v-if="page.props.jetstream?.hasApiFeatures"
                            :href="route('api-tokens.index')"
                            class="flex items-center gap-2 text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-gray-800"
                        >
                            <i class="fas fa-key w-4 h-4 text-blue-500"></i> API
                            Tokens
                        </DropdownLink>

                        <div
                            class="border-t border-gray-200 dark:border-gray-700 my-1"
                        ></div>

                        <form @submit.prevent="logout">
                            <DropdownLink
                                as="button"
                                class="flex items-center gap-2 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20"
                            >
                                <i
                                    class="fas fa-right-from-bracket w-4 h-4"
                                ></i>
                                Cerrar Sesión
                            </DropdownLink>
                        </form>
                    </div>
                </template>
            </Dropdown>
        </div>

        <!-- 🚀 BOTÓN UPGRADE (Solo para roles específicos) -->
        <div
            v-if="
                ['Prospecto', 'ClientSuscriptor', 'usuario'].includes(userRole)
            "
            class="p-4 shrink-0"
        >
            <button
                class="group w-full relative overflow-hidden bg-gradient-to-r from-cyan-500 via-blue-500 to-cyan-500 hover:from-cyan-400 hover:via-blue-400 hover:to-cyan-400 text-white font-semibold py-3 px-4 rounded-xl transition-all duration-300 text-sm shadow-lg shadow-cyan-500/30 hover:shadow-cyan-500/50 hover:scale-[1.02] active:scale-[0.98]"
                type="button"
            >
                <!-- Shine effect -->
                <div
                    class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-700"
                ></div>
                <span class="relative flex items-center justify-center gap-2">
                    <span class="text-lg">✨</span>
                    <span>Upgrade to pro</span>
                </span>
            </button>
        </div>
    </aside>

    <!-- 🌑 OVERLAY PARA MÓVIL -->
    <transition
        enter-active-class="transition-opacity duration-300"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition-opacity duration-200"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div
            v-show="showingNavigationDropdown"
            class="fixed inset-0 bg-black/60 backdrop-blur-sm z-40 lg:hidden"
            @click="emit('update:showingNavigationDropdown', false)"
        ></div>
    </transition>
</template>

<style scoped>
/* 🎨 Custom Scrollbar para el aside */
aside::-webkit-scrollbar {
    width: 6px;
}
aside::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 10px;
}
aside::-webkit-scrollbar-thumb {
    background: linear-gradient(
        180deg,
        rgba(34, 211, 238, 0.4),
        rgba(59, 130, 246, 0.4)
    );
    border-radius: 10px;
}
aside::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(
        180deg,
        rgba(34, 211, 238, 0.6),
        rgba(59, 130, 246, 0.6)
    );
}

/* 🔄 Rotate icon para chevron */
.rotate-180 {
    transform: rotate(180deg);
}

/* ✨ Animación suave para submenús */
.v-enter-active,
.v-leave-active {
    transition: all 0.3s ease;
}
.v-enter-from,
.v-leave-to {
    opacity: 0;
    transform: translateY(-10px);
}

/* 💫 Animación pulse para indicador activo */
@keyframes pulse-ring {
    0% {
        transform: scale(0.8);
        opacity: 1;
    }
    100% {
        transform: scale(2);
        opacity: 0;
    }
}
.animate-ping {
    animation: pulse-ring 2s cubic-bezier(0, 0, 0.2, 1) infinite;
}

/* 🎯 Efectos hover en iconos de sección */
.group:hover .fa-columns,
.group:hover .fa-users,
.group:hover .fa-cog {
    transform: scale(1.1);
    transition: transform 0.2s ease;
}
</style>
