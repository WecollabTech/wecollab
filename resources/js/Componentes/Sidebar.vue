<script setup>
import { ref, onMounted, onUnmounted, computed } from "vue";
import { Link, router, usePage } from "@inertiajs/vue3";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import NavLink from "@/Components/NavLink.vue";

const page = usePage();
const props = defineProps({ showingNavigationDropdown: Boolean });
const emit = defineEmits(["update:showingNavigationDropdown"]);

// ─────────────────────────────────────────────────────
// 🔐 IDs y Grupos de Roles (Optimizado - sin objeto ROLES redundante)
// ─────────────────────────────────────────────────────
const ROLE_IDS = {
    SUPERADMIN_WE_COLLAB: 1,
    ADMIN_WE_COLLAB: 2,
    SUSCRIPTOR_SLC: 3,
    CLIENTE_ADMIN: 4,
    CLIENTE_PREMIUM: 5,
    USUARIO_PUBLICO: 6,
    PROSPECTO: 7,
    USUARIO_ESTANDAR: 8,
};

const ROLE_GROUPS = {
    ADMIN_WE_COLLAB_TOTAL: [ROLE_IDS.SUPERADMIN_WE_COLLAB],
    ADMIN_WE_COLLAB_OPERATIVO: [
        ROLE_IDS.SUPERADMIN_WE_COLLAB,
        ROLE_IDS.ADMIN_WE_COLLAB,
    ],
    GESTORES: [
        ROLE_IDS.SUPERADMIN_WE_COLLAB,
        ROLE_IDS.ADMIN_WE_COLLAB,
        ROLE_IDS.CLIENTE_ADMIN,
    ],
    CLIENTES_CON_ACCESO: [
        ROLE_IDS.SUSCRIPTOR_SLC,
        ROLE_IDS.CLIENTE_ADMIN,
        ROLE_IDS.CLIENTE_PREMIUM,
    ],
    ACCESO_CATALOGO: [
        ROLE_IDS.USUARIO_PUBLICO,
        ROLE_IDS.PROSPECTO,
        ROLE_IDS.USUARIO_ESTANDAR,
    ],
    TODOS_ACTIVOS: Object.values(ROLE_IDS),
};

// ─────────────────────────────────────────────────────
// 🔐 USUARIO Y ROL (desde Inertia page.props.auth)
// ─────────────────────────────────────────────────────
const currentUser = computed(() => page.props.auth?.user);
const userRole = computed(() => currentUser.value?.role);
const userRoleId = computed(() => userRole.value?.id);
const userRoleName = computed(() => userRole.value?.nombre);
const isRoleActive = computed(() => userRole.value?.estado === "activo");

// ─────────────────────────────────────────────────────
// 🔒 VALIDACIÓN DE ACCESO (FAIL-CLOSED - Seguro por defecto)
// ─────────────────────────────────────────────────────
const canAccess = (allowedRoles) => {
    if (!userRoleId.value || !isRoleActive.value) return false;
    if (!allowedRoles || allowedRoles.length === 0) return false;
    return allowedRoles.includes(userRoleId.value);
};

// Helper: Obtener clase CSS para badge de rol según tipo
const getRoleBadgeClass = (roleId) => {
    if (!isRoleActive.value)
        return "bg-gray-500/20 text-gray-400 border-gray-500/30";

    const classes = {
        [ROLE_IDS.SUPERADMIN_WE_COLLAB]:
            "bg-emerald-500/20 text-emerald-300 border-emerald-500/30",
        [ROLE_IDS.ADMIN_WE_COLLAB]:
            "bg-blue-500/20 text-blue-300 border-blue-500/30",
        [ROLE_IDS.CLIENTE_ADMIN]:
            "bg-cyan-500/20 text-cyan-300 border-cyan-500/30",
        [ROLE_IDS.SUSCRIPTOR_SLC]:
            "bg-purple-500/20 text-purple-300 border-purple-500/30",
        [ROLE_IDS.CLIENTE_PREMIUM]:
            "bg-violet-500/20 text-violet-300 border-violet-500/30",
        [ROLE_IDS.USUARIO_PUBLICO]:
            "bg-indigo-500/20 text-indigo-300 border-indigo-500/30",
        [ROLE_IDS.PROSPECTO]: "bg-gray-500/20 text-gray-300 border-gray-500/30",
        [ROLE_IDS.USUARIO_ESTANDAR]:
            "bg-slate-500/20 text-slate-300 border-slate-500/30",
    };
    return classes[roleId] || "bg-gray-500/20 text-gray-300 border-gray-500/30";
};

// ─────────────────────────────────────────────────────
// 📋 CONFIGURACIÓN DEL MENÚ (Con roles reales de tu BD)
// ─────────────────────────────────────────────────────
const MENU_CONFIG = {
    tutorial: {
        visible: true,
        items: [
            {
                key: "categorias",
                label: "Categorías",
                icon: "fa-layer-group",
                href: "/categorias",
                roles: ROLE_GROUPS.GESTORES,
                description: "Gestión de categorías de contenido",
            },
            {
                key: "subcategorias",
                label: "Subcategorías",
                icon: "fa-sitemap",
                href: "/subcategorias",
                roles: ROLE_GROUPS.GESTORES,
            },
            {
                key: "tutoriales",
                label: "Videos Capacitación",
                icon: "fa-video",
                href: "/tutoriales",
                roles: [
                    ROLE_IDS.SUPERADMIN_WE_COLLAB,
                    ROLE_IDS.ADMIN_WE_COLLAB,
                ],
            },
            {
                key: "comentarios",
                label: "Comentarios",
                icon: "fa-comments",
                href: "/comentarios",
                roles: ROLE_GROUPS.GESTORES,
            },
        ],
    },
    usuarios: {
        visible: true,
        items: [
            {
                key: "crear_usuario",
                label: "Crear Usuario",
                icon: "fa-user-plus",
                href: "/usuarios/create",
                roles: ROLE_GROUPS.GESTORES,
                tooltip: "Solo usuarios de tu empresa",
                restrictToCompany: true,
            },
            {
                key: "editar_usuario",
                label: "Editar Usuario",
                icon: "fa-user-edit",
                href: "/usuarios",
                roles: ROLE_GROUPS.GESTORES,
            },
            {
                key: "eliminar_usuario",
                label: "Eliminar Usuario",
                icon: "fa-user-times",
                href: "/usuarios",
                roles: ROLE_GROUPS.GESTORES,
                requiresConfirm: true,
            },
        ],
    },
    configuracion: {
        visible: true,
        items: [
            {
                key: "permisos",
                label: "Permisos",
                icon: "fa-key",
                href: "/permisos",
                roles: ROLE_GROUPS.ADMIN_WE_COLLAB_TOTAL,
                badge: "SuperAdmin",
            },
            {
                key: "roles",
                label: "Roles",
                icon: "fa-user-shield",
                href: "/roles",
                roles: ROLE_GROUPS.ADMIN_WE_COLLAB_TOTAL,
                badge: "SuperAdmin",
            },
            {
                key: "reportes",
                label: "Reportes",
                icon: "fa-chart-bar",
                href: "/reportes",
                roles: ROLE_GROUPS.GESTORES,
            },
        ],
    },
    // ✅ NUEVA SECCIÓN: Centro de Recursos
    recursos: {
        visible: true,
        items: [
            {
                key: "videos",
                label: "Videos",
                icon: "fa-video",
                href: "/recursos/videos",
                roles: ROLE_GROUPS.CLIENTES_CON_ACCESO,
                description: "Biblioteca de videos de capacitación",
            },
            {
                key: "manuales",
                label: "Manuales",
                icon: "fa-book",
                href: "/recursos/manuales",
                roles: ROLE_GROUPS.CLIENTES_CON_ACCESO,
                description: "Documentación y manuales de uso",
            },
            {
                key: "guias",
                label: "Guías",
                icon: "fa-compass",
                href: "/recursos/guias",
                roles: ROLE_GROUPS.CLIENTES_CON_ACCESO,
                description: "Guías paso a paso",
            },
            {
                key: "posts",
                label: "Posts",
                icon: "fa-newspaper",
                href: "/recursos/posts",
                roles: ROLE_GROUPS.CLIENTES_CON_ACCESO,
                description: "Artículos y novedades",
            },
            {
                key: "tripticos",
                label: "Trípticos",
                icon: "fa-file-pdf",
                href: "/recursos/tripticos",
                roles: ROLE_GROUPS.CLIENTES_CON_ACCESO,
                description: "Material descargable e informativo",
            },
        ],
    },
};

// ─────────────────────────────────────────────────────
// 🔍 FILTRADO DINÁMICO DE ITEMS (con validación de estado)
// ─────────────────────────────────────────────────────
const getVisibleItems = (sectionKey) => {
    const section = MENU_CONFIG[sectionKey];
    if (!section?.items || !isRoleActive.value) return [];
    return section.items.filter((item) => canAccess(item.roles));
};

const visibleTutorialItems = computed(() => getVisibleItems("tutorial"));
const visibleUsuariosItems = computed(() => getVisibleItems("usuarios"));
const visibleConfigItems = computed(() => getVisibleItems("configuracion"));
const visibleRecursosItems = computed(() => getVisibleItems("recursos"));

const showTutorialSection = computed(
    () => visibleTutorialItems.value.length > 0 && isRoleActive.value,
);
const showUsuariosSection = computed(
    () => visibleUsuariosItems.value.length > 0 && isRoleActive.value,
);
const showConfigSection = computed(
    () => visibleConfigItems.value.length > 0 && isRoleActive.value,
);
const showRecursosSection = computed(
    () => visibleRecursosItems.value.length > 0 && isRoleActive.value,
);

const isSuperAdminWeCollab = computed(
    () => userRoleId.value === ROLE_IDS.SUPERADMIN_WE_COLLAB,
);
const isClienteAdmin = computed(
    () => userRoleId.value === ROLE_IDS.CLIENTE_ADMIN,
);

// ─────────────────────────────────────────────────────
// 🔄 LÓGICA DE SUBMENÚS Y EVENTOS
// ─────────────────────────────────────────────────────
const isSubmenuOpen = ref({
    tutorial: false,
    usuarios: false,
    configuracion: false,
    recursos: false,
});

const toggleSubmenu = (menu) => {
    Object.keys(isSubmenuOpen.value).forEach((key) => {
        if (key !== menu) isSubmenuOpen.value[key] = false;
    });
    isSubmenuOpen.value[menu] = !isSubmenuOpen.value[menu];
};

const logout = () => router.post(route("logout"));

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
    <!-- ✅ CORRECCIÓN PRINCIPAL: lg:static → lg:fixed para que no se mueva al hacer scroll -->
    <aside
        :class="{
            'translate-x-0': showingNavigationDropdown,
            '-translate-x-full': !showingNavigationDropdown,
        }"
        class="fixed inset-y-0 left-0 z-50 w-72 bg-gradient-to-b from-[#1a3080] to-[#223e9c] shadow-2xl transform transition-all duration-300 ease-out lg:translate-x-0 lg:fixed lg:inset-y-0"
    >
        <!-- Header con Logo -->
        <div
            class="flex items-center justify-between h-20 px-6 border-b border-white/10 bg-white/5 backdrop-blur-sm"
        >
            <Link
                :href="route('dashboard')"
                class="flex items-center gap-3 group"
            >
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

        <!-- ✅ Navigation con scroll interno (solo el menú se desplaza) -->
        <nav
            class="flex-1 px-4 py-6 space-y-1 overflow-y-auto max-h-[calc(100vh-200px)]"
        >
            <!-- Dashboard (Visible solo si rol está activo) -->
            <div class="mb-6" v-if="isRoleActive">
                <NavLink
                    :href="route('dashboard')"
                    :active="route().current('dashboard')"
                    class="group relative flex items-center gap-3 px-4 py-3.5 rounded-xl text-sm font-semibold transition-all duration-300 text-white hover:bg-white/10"
                    :class="{
                        'bg-gradient-to-r from-cyan-500/20 to-blue-500/20 border border-cyan-400/30 shadow-lg shadow-cyan-500/10':
                            route().current('dashboard'),
                    }"
                >
                    <div
                        v-if="route().current('dashboard')"
                        class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-8 bg-gradient-to-b from-cyan-400 to-blue-400 rounded-r-full"
                    ></div>
                    <div class="relative">
                        <i
                            class="fas fa-home-alt w-5 h-5 transition-all duration-300"
                            :class="
                                route().current('dashboard')
                                    ? 'text-cyan-400 scale-110'
                                    : 'text-white/70 group-hover:text-white group-hover:scale-110'
                            "
                        ></i>
                    </div>
                    <span class="flex-1">Inicio</span>
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

            <!-- Separador "Components" (Solo si rol activo) -->
            <div class="px-4 mb-2" v-if="isRoleActive">
                <p
                    class="text-xs font-semibold text-white/40 uppercase tracking-wider"
                >
                    Components
                </p>
            </div>

            <!-- ⚠️ MENSAJE: Rol inactivo -->
            <div v-if="!isRoleActive" class="px-4 py-6">
                <div
                    class="p-4 bg-amber-500/10 border border-amber-500/30 rounded-xl text-center"
                >
                    <i
                        class="fas fa-exclamation-triangle text-amber-400 text-xl mb-2 block"
                    ></i>
                    <p class="text-sm font-medium text-white/90">
                        Rol inactivo
                    </p>
                    <p class="text-xs text-white/60 mt-1">
                        Tu acceso está suspendido. Contacta al administrador.
                    </p>
                </div>
            </div>

            <!-- 📚 Sección: Tutorial -->
            <div v-if="showTutorialSection" class="mb-2">
                <button
                    @click="toggleSubmenu('tutorial')"
                    class="w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm font-medium text-white/90 hover:bg-white/10 hover:text-white transition-all duration-200 group"
                    type="button"
                    :aria-expanded="isSubmenuOpen.tutorial"
                >
                    <div class="flex items-center gap-3">
                        <i
                            class="fas fa-columns w-5 h-5 text-white/60 group-hover:text-cyan-400 transition-colors duration-300"
                        ></i>
                        <span>Tutorial</span>
                    </div>
                    <i
                        class="fas fa-chevron-down w-4 h-4 text-white/40 transition-all duration-300 group-hover:text-white/70"
                        :class="{ 'rotate-180': isSubmenuOpen.tutorial }"
                    ></i>
                </button>
                <div
                    v-show="isSubmenuOpen.tutorial"
                    class="mt-1 ml-4 pl-4 border-l-2 border-white/10 space-y-1 overflow-hidden transition-all duration-300"
                >
                    <NavLink
                        v-for="item in visibleTutorialItems"
                        :key="item.key"
                        :href="item.href"
                        class="group flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm text-white/70 hover:bg-white/10 hover:text-white hover:translate-x-1 transition-all duration-200"
                        :title="item.description"
                    >
                        <i
                            :class="`fas ${item.icon} w-4 h-4 text-white/50 group-hover:text-cyan-400 transition-colors`"
                        ></i>
                        <span>{{ item.label }}</span>
                    </NavLink>
                </div>
            </div>

            <!-- 👥 Sección: Usuarios -->
            <div v-if="showUsuariosSection" class="mb-2">
                <button
                    @click="toggleSubmenu('usuarios')"
                    class="w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm font-medium text-white/90 hover:bg-white/10 hover:text-white transition-all duration-200 group"
                    type="button"
                    :aria-expanded="isSubmenuOpen.usuarios"
                >
                    <div class="flex items-center gap-3">
                        <i
                            class="fas fa-users w-5 h-5 text-white/60 group-hover:text-cyan-400 transition-colors duration-300"
                        ></i>
                        <span>Usuarios</span>
                        <span
                            v-if="isClienteAdmin"
                            class="ml-2 px-2 py-0.5 text-[10px] font-medium bg-cyan-500/20 text-cyan-300 rounded-full border border-cyan-500/30"
                        >
                            Tu empresa
                        </span>
                    </div>
                    <i
                        class="fas fa-chevron-down w-4 h-4 text-white/40 transition-all duration-300 group-hover:text-white/70"
                        :class="{ 'rotate-180': isSubmenuOpen.usuarios }"
                    ></i>
                </button>
                <div
                    v-show="isSubmenuOpen.usuarios"
                    class="mt-1 ml-4 pl-4 border-l-2 border-white/10 space-y-1 overflow-hidden transition-all duration-300"
                >
                    <NavLink
                        v-for="item in visibleUsuariosItems"
                        :key="item.key"
                        :href="item.href"
                        class="group flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm text-white/70 hover:bg-white/10 hover:text-white hover:translate-x-1 transition-all duration-200"
                    >
                        <i
                            :class="`fas ${item.icon} w-4 h-4 text-white/50 group-hover:text-cyan-400 transition-colors`"
                        ></i>
                        <span class="flex-1">{{ item.label }}</span>
                        <i
                            v-if="item.requiresConfirm"
                            class="fas fa-exclamation-triangle w-3 h-3 text-amber-400"
                            :title="item.tooltip || 'Requiere confirmación'"
                        ></i>
                    </NavLink>
                </div>
            </div>

            <!-- ⚙️ Sección: Configuración -->
            <div v-if="showConfigSection" class="mb-2">
                <button
                    @click="toggleSubmenu('configuracion')"
                    class="w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm font-medium text-white/90 hover:bg-white/10 hover:text-white transition-all duration-200 group"
                    type="button"
                    :aria-expanded="isSubmenuOpen.configuracion"
                >
                    <div class="flex items-center gap-3">
                        <i
                            class="fas fa-cog w-5 h-5 text-white/60 group-hover:text-cyan-400 transition-colors duration-300"
                        ></i>
                        <span>Configuración</span>
                    </div>
                    <i
                        class="fas fa-chevron-down w-4 h-4 text-white/40 transition-all duration-300 group-hover:text-white/70"
                        :class="{ 'rotate-180': isSubmenuOpen.configuracion }"
                    ></i>
                </button>
                <div
                    v-show="isSubmenuOpen.configuracion"
                    class="mt-1 ml-4 pl-4 border-l-2 border-white/10 space-y-1 overflow-hidden transition-all duration-300"
                >
                    <NavLink
                        v-for="item in visibleConfigItems"
                        :key="item.key"
                        :href="item.href"
                        class="group flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm text-white/70 hover:bg-white/10 hover:text-white hover:translate-x-1 transition-all duration-200"
                    >
                        <i
                            :class="`fas ${item.icon} w-4 h-4 text-white/50 group-hover:text-cyan-400 transition-colors`"
                        ></i>
                        <span class="flex-1">{{ item.label }}</span>
                        <span
                            v-if="item.badge"
                            class="ml-auto px-1.5 py-0.5 text-[9px] font-medium bg-red-500/20 text-red-300 rounded border border-red-500/30"
                        >
                            {{ item.badge }}
                        </span>
                    </NavLink>
                </div>
            </div>

            <!-- 🎓 Sección: Centro de Recursos (NUEVA) -->
            <div v-if="showRecursosSection" class="mb-2">
                <button
                    @click="toggleSubmenu('recursos')"
                    class="w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm font-medium text-white/90 hover:bg-white/10 hover:text-white transition-all duration-200 group"
                    type="button"
                    :aria-expanded="isSubmenuOpen.recursos"
                >
                    <div class="flex items-center gap-3">
                        <i
                            class="fas fa-graduation-cap w-5 h-5 text-white/60 group-hover:text-cyan-400 transition-colors duration-300"
                        ></i>
                        <span>Centro de Recursos</span>
                    </div>
                    <i
                        class="fas fa-chevron-down w-4 h-4 text-white/40 transition-all duration-300 group-hover:text-white/70"
                        :class="{ 'rotate-180': isSubmenuOpen.recursos }"
                    ></i>
                </button>
                <div
                    v-show="isSubmenuOpen.recursos"
                    class="mt-1 ml-4 pl-4 border-l-2 border-white/10 space-y-1 overflow-hidden transition-all duration-300"
                >
                    <NavLink
                        v-for="item in visibleRecursosItems"
                        :key="item.key"
                        :href="item.href"
                        class="group flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm text-white/70 hover:bg-white/10 hover:text-white hover:translate-x-1 transition-all duration-200"
                        :title="item.description"
                    >
                        <i
                            :class="`fas ${item.icon} w-4 h-4 text-white/50 group-hover:text-cyan-400 transition-colors`"
                        ></i>
                        <span>{{ item.label }}</span>
                    </NavLink>
                </div>
            </div>

            <!-- ⚠️ MENSAJE: Rol activo pero sin menús disponibles -->
            <div
                v-else-if="
                    isRoleActive &&
                    ![
                        ...visibleTutorialItems,
                        ...visibleUsuariosItems,
                        ...visibleConfigItems,
                        ...visibleRecursosItems,
                    ].length
                "
                class="px-4 py-6"
            >
                <div
                    class="p-4 bg-blue-500/10 border border-blue-500/30 rounded-xl text-center"
                >
                    <i
                        class="fas fa-info-circle text-blue-400 text-xl mb-2 block"
                    ></i>
                    <p class="text-sm font-medium text-white/90">
                        Menú personalizado
                    </p>
                    <p class="text-xs text-white/60 mt-1">
                        Tu rol "{{ userRoleName }}" tiene acceso limitado.
                    </p>
                </div>
            </div>
        </nav>

        <!-- Footer: Perfil de usuario con Dropdown -->
        <div
            class="p-4 border-t border-white/10 bg-gradient-to-b from-transparent to-black/20"
        >
            <Dropdown align="right" width="48">
                <template #trigger>
                    <button
                        class="group w-full flex items-center gap-3 px-4 py-3 rounded-xl bg-white/10 hover:bg-white/15 backdrop-blur-sm border border-white/10 hover:border-white/20 transition-all duration-300 hover:shadow-lg hover:shadow-cyan-500/10"
                        type="button"
                    >
                        <!-- Avatar / Iniciales -->
                        <div class="relative">
                            <div
                                class="absolute inset-0 bg-cyan-400/30 rounded-xl blur-md opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                            ></div>
                            <div
                                v-if="
                                    !$page.props.jetstream
                                        ?.managesProfilePhotos ||
                                    !$page.props.auth.user?.profile_photo_url
                                "
                                class="relative w-11 h-11 rounded-xl bg-gradient-to-br from-cyan-400 to-blue-500 flex items-center justify-center text-white font-bold text-lg shadow-lg"
                            >
                                {{
                                    $page.props.auth.user?.name
                                        ?.charAt(0)
                                        ?.toUpperCase() || "U"
                                }}
                            </div>
                            <img
                                v-else
                                :src="$page.props.auth.user.profile_photo_url"
                                :alt="$page.props.auth.user.name"
                                class="relative w-11 h-11 rounded-xl object-cover border-2 border-white/20 group-hover:border-cyan-400/50 transition-colors duration-300"
                            />
                            <!-- Indicador de estado del rol -->
                            <span
                                class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 rounded-full border-2 border-[#1a3080] shadow-lg"
                                :class="
                                    isRoleActive
                                        ? 'bg-emerald-400 shadow-emerald-500/30 animate-pulse-slow'
                                        : 'bg-amber-400 shadow-amber-500/30'
                                "
                                :title="isRoleActive ? 'Activo' : 'Inactivo'"
                            ></span>
                        </div>

                        <!-- Información del usuario -->
                        <div class="flex-1 min-w-0 text-left">
                            <p
                                class="text-sm font-semibold text-white truncate group-hover:text-cyan-300 transition-colors duration-300"
                            >
                                {{ $page.props.auth.user?.name || "Usuario" }}
                            </p>

                            <!-- Badge del rol -->
                            <div
                                v-if="userRole && isRoleActive"
                                class="flex items-center gap-2 mt-0.5"
                            >
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium border truncate max-w-[140px] cursor-help"
                                    :class="getRoleBadgeClass(userRoleId)"
                                    :title="userRole.descripcion"
                                >
                                    <i
                                        class="fas fa-shield-alt w-3 h-3 mr-1"
                                    ></i>
                                    {{ userRoleName }}
                                </span>
                            </div>

                            <!-- Mensaje si rol está inactivo -->
                            <p
                                v-else-if="userRole"
                                class="text-xs text-amber-300/80 mt-0.5"
                            >
                                <i class="fas fa-pause-circle mr-1"></i>
                                {{ userRoleName }} • Inactivo
                            </p>

                            <!-- Email del usuario -->
                            <p class="text-xs text-white/60 truncate">
                                {{ $page.props.auth.user?.email || "" }}
                            </p>
                        </div>

                        <!-- Flecha del dropdown -->
                        <svg
                            class="w-4 h-4 text-white/40 group-hover:text-white/70 transition-colors flex-shrink-0"
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

                <!-- Contenido del Dropdown -->
                <template #content>
                    <div class="py-1">
                        <!-- Cabecera del dropdown -->
                        <div
                            class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-blue-50 to-cyan-50 dark:from-gray-800 dark:to-blue-900/20"
                        >
                            <p
                                class="text-sm font-semibold text-gray-900 dark:text-white"
                            >
                                {{ $page.props.auth.user?.name }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $page.props.auth.user?.email }}
                            </p>
                            <!-- Estado y rol en dropdown -->
                            <p
                                v-if="userRole"
                                class="text-[10px] mt-1 px-2 py-0.5 rounded inline-block"
                                :class="{
                                    'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300':
                                        isRoleActive,
                                    'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300':
                                        !isRoleActive,
                                }"
                            >
                                <i
                                    class="fas fa-circle w-2 h-2 mr-1"
                                    :class="
                                        isRoleActive
                                            ? 'text-emerald-500'
                                            : 'text-amber-500'
                                    "
                                ></i>
                                {{ isRoleActive ? "Activo" : "Inactivo" }} •
                                {{ userRoleName }}
                            </p>
                        </div>

                        <!-- Links del dropdown -->
                        <DropdownLink
                            :href="route('profile.show')"
                            class="flex items-center gap-2 text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-gray-800"
                        >
                            <i class="fas fa-user w-4 h-4 text-blue-500"></i>
                            Perfil
                        </DropdownLink>

                        <DropdownLink
                            v-if="$page.props.ziggy?.routes?.['profile.edit']"
                            :href="route('profile.edit')"
                            class="flex items-center gap-2 text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-gray-800"
                        >
                            <i class="fas fa-cog w-4 h-4 text-gray-500"></i>
                            Configuración
                        </DropdownLink>

                        <div
                            class="border-t border-gray-200 dark:border-gray-700 my-1"
                        ></div>
                        <form @submit.prevent="logout">
                            <DropdownLink
                                as="button"
                                class="flex items-center gap-2 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 w-full text-left"
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

        <!-- Upgrade Button (SOLO para Superadmin We collab ID 1 y solo si está activo) -->
        <div v-if="isSuperAdminWeCollab && isRoleActive" class="p-4">
            <button
                class="group w-full relative overflow-hidden bg-gradient-to-r from-cyan-500 via-blue-500 to-cyan-500 hover:from-cyan-400 hover:via-blue-400 hover:to-cyan-400 text-white font-semibold py-3 px-4 rounded-xl transition-all duration-300 text-sm shadow-lg shadow-cyan-500/30 hover:shadow-cyan-500/50 hover:scale-[1.02] active:scale-[0.98]"
                type="button"
            >
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

    <!-- Overlay para móvil -->
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
/* ✅ Scrollbar solo para el nav interno (no para todo el aside) */
nav::-webkit-scrollbar {
    width: 4px;
}
nav::-webkit-scrollbar-track {
    background: transparent;
}
nav::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.2);
    border-radius: 4px;
}
nav::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.4);
}

/* Scrollbar para aside (fallback) */
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

/* Animación de rotación para iconos de submenu */
.rotate-180 {
    transform: rotate(180deg);
}

/* Transiciones para animaciones Vue */
.v-enter-active,
.v-leave-active {
    transition: all 0.3s ease;
}
.v-enter-from,
.v-leave-to {
    opacity: 0;
    transform: translateY(-10px);
}

/* Animación suave para el indicador de estado activo */
@keyframes pulse-slow {
    0%,
    100% {
        opacity: 1;
    }
    50% {
        opacity: 0.7;
    }
}
.animate-pulse-slow {
    animation: pulse-slow 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>
