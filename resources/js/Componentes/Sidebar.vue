<script setup>
import { ref, onMounted, onUnmounted, computed, watch } from "vue";
import { Link, router, usePage } from "@inertiajs/vue3";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";

const page = usePage();
const props = defineProps({
    showingNavigationDropdown: Boolean,
});

const emit = defineEmits(["update:showingNavigationDropdown"]);

// ================================
// 🔐 VERIFICACIÓN DE ROLES PERMITIDOS
// ================================
const ALLOWED_ROLE_IDS = [1, 2];
const ALLOWED_ROLE_NAMES = ["Superadmin we collab", "Admin we collab"];

const getUserRole = () => {
    const user = page.props.auth?.user;

    if (!user) {
        return null;
    }

    if (user.rol) {
        return user.rol;
    }

    if (user.role) {
        return user.role;
    }

    if (user.roles && Array.isArray(user.roles) && user.roles.length > 0) {
        return user.roles[0];
    }

    if (user.data && user.data.rol) {
        return user.data.rol;
    }

    return null;
};

const hasFullMenuAccess = computed(() => {
    const userRole = getUserRole();

    if (!userRole) {
        return false;
    }

    if (userRole.id && ALLOWED_ROLE_IDS.includes(userRole.id)) {
        return true;
    }

    if (userRole.nombre && ALLOWED_ROLE_NAMES.includes(userRole.nombre)) {
        return true;
    }

    if (typeof userRole === "string" && ALLOWED_ROLE_NAMES.includes(userRole)) {
        return true;
    }

    if (typeof userRole === "number" && ALLOWED_ROLE_IDS.includes(userRole)) {
        return true;
    }

    return false;
});

// ================================
// 📊 CONFIGURACIÓN DEL MENÚ
// ================================
const menuSections = {
    dashboard: {
        title: "Inicio",
        icon: "fa-home",
        isSingle: true,
        href: "/dashboard",
        description: "Panel principal",
    },

    biblioteca: {
        title: "Biblioteca",
        icon: "fa-book-open",
        items: [
            {
                key: "categorias",
                label: "Categorías",
                icon: "fa-tags",
                href: "/categorias",
                description: "Administrar categorías de contenido",
            },
            {
                key: "subcategorias",
                label: "Subcategorías",
                icon: "fa-sitemap",
                href: "/subcategorias",
                description: "Administrar subcategorías",
            },
            {
                key: "lista",
                label: "Lista de Recursos",
                icon: "fa-video",
                href: "/recursos",
                description: "Biblioteca de videos",
            },
            {
                key: "videos",
                label: "Videos",
                icon: "fa-book",
                href: "/recursos/videos",
                description: "Cursos de Capacitaciones",
            },
            {
                key: "manuales",
                label: "Manuales",
                icon: "fa-book",
                href: "/recursos/manuales",
                description: "Manuales y documentación",
            },
            {
                key: "guias",
                label: "Guías",
                icon: "fa-compass",
                href: "/recursos/guias",
                description: "Guías paso a paso",
            },
            {
                key: "posts",
                label: "Posts",
                icon: "fa-newspaper",
                href: "/recursos/posts",
                description: "Materiales de Post",
            },
            {
                key: "triptico",
                label: "Infografia",
                icon: "fa-newspaper",
                href: "/recursos/tripticos",
                description: "Infografia",
            },
            {
                key: "avisos",
                label: "Avisos Importante",
                icon: "fa-bell",
                href: "/recursos/avisos-importantes",
                description: "Avisos del Sistema",
            },
        ],
    },

    materiales: {
        title: "Materiales",
        icon: "fa-boxes",
        items: [
            {
                key: "tipos_materiales",
                label: "Tipos de Materiales",
                icon: "fa-tag",
                href: "/tipos-materiales",
                description: "Administrar tipos de materiales",
            },
            {
                key: "formatos_materiales",
                label: "Formatos de Materiales",
                icon: "fa-shapes",
                href: "/formatos-materiales",
                description: "Administrar formatos de materiales",
            },
        ],
    },

    comunidad: {
        title: "Comunidad",
        icon: "fa-users",
        items: [
            {
                key: "crear_usuario",
                label: "Nuevo Usuario",
                icon: "fa-user-plus",
                href: "/usuarios/create",
                badge: "Nuevo",
            },
            {
                key: "listar_usuarios",
                label: "Todos los Usuarios",
                icon: "fa-users",
                href: "/usuarios",
                description: "Lista completa de usuarios",
            },
            {
                key: "comentarios",
                label: "Comentarios",
                icon: "fa-comments",
                href: "/comentarios",
                description: "Moderar comentarios",
                badge: "Pendientes",
            },
        ],
    },

    sistema: {
        title: "Sistema",
        icon: "fa-cogs",
        items: [
            {
                key: "permisos",
                label: "Permisos",
                icon: "fa-key",
                href: "/permisos",
                badge: "Admin",
                description: "Permisos globales",
            },
            {
                key: "roles",
                label: "Roles",
                icon: "fa-user-shield",
                href: "/roles",
                badge: "Admin",
                description: "Administrar roles",
            },
        ],
    },
};

// ================================
// 🔍 FUNCIÓN PARA DETECTAR SECCIÓN ACTIVA
// ================================
const getCurrentSection = () => {
    const currentUrl = page.url;

    if (currentUrl === "/dashboard" || currentUrl === "/") {
        return "dashboard";
    }

    for (const [sectionKey, section] of Object.entries(menuSections)) {
        if (section.isSingle) continue;

        for (const item of section.items) {
            if (
                currentUrl === item.href ||
                currentUrl.startsWith(item.href + "/")
            ) {
                return sectionKey;
            }
        }
    }

    return null;
};

// ================================
// 🔽 ESTADO DE SUBMENÚS
// ================================
const isSubmenuOpen = ref({
    biblioteca: false,
    materiales: false,
    comunidad: false,
    sistema: false,
});

const initializeOpenSubmenus = () => {
    if (!hasFullMenuAccess.value) return;
    const currentSection = getCurrentSection();
    if (currentSection && currentSection !== "dashboard") {
        isSubmenuOpen.value[currentSection] = true;
    }
};

watch(
    () => page.url,
    () => {
        if (!hasFullMenuAccess.value) return;

        const currentSection = getCurrentSection();

        Object.keys(isSubmenuOpen.value).forEach((key) => {
            isSubmenuOpen.value[key] = false;
        });

        if (currentSection && currentSection !== "dashboard") {
            isSubmenuOpen.value[currentSection] = true;
        }
    },
);

// ================================
// 🎯 FUNCIONES DE VERIFICACIÓN
// ================================
const isItemActive = (href) => {
    const currentUrl = page.url;
    return currentUrl === href || currentUrl.startsWith(href + "/");
};

const isSectionActive = (sectionKey) => {
    if (sectionKey === "dashboard") {
        return page.url === "/dashboard" || page.url === "/";
    }

    const section = menuSections[sectionKey];
    if (!section || !section.items) return false;

    return section.items.some((item) => isItemActive(item.href));
};

const toggleSubmenu = (menu) => {
    if (!hasFullMenuAccess.value) return;

    if (isSubmenuOpen.value[menu]) {
        isSubmenuOpen.value[menu] = false;
    } else {
        Object.keys(isSubmenuOpen.value).forEach((key) => {
            isSubmenuOpen.value[key] = false;
        });
        isSubmenuOpen.value[menu] = true;
    }
};

const logout = () => router.post(route("logout"));

const closeSidebar = () => {
    emit("update:showingNavigationDropdown", false);
};

const handleResize = () => {
    if (window.innerWidth >= 1024) closeSidebar();
};

const handleKeydown = (e) => {
    if (e.key === "Escape" && props.showingNavigationDropdown) {
        closeSidebar();
    }
};

onMounted(() => {
    initializeOpenSubmenus();
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
        class="fixed inset-y-0 left-0 z-50 w-72 bg-gradient-to-b from-[#023E8A] to-[#0e2d48] shadow-2xl transform transition-all duration-300 ease-out lg:translate-x-0 flex flex-col font-sans"
    >
        <!-- Header con Logo -->
        <div class="flex-shrink-0 relative">
            <div
                class="absolute inset-0 bg-gradient-to-r from-cyan-400/5 to-indigo-400/5"
            ></div>
            <div
                class="relative flex items-center justify-between h-20 px-6 border-b border-white/10 bg-white/[0.03] backdrop-blur-sm"
            >
                <Link
                    :href="route('dashboard')"
                    class="flex items-center gap-3 group"
                >
                    <div class="relative">
                        <div
                            class="absolute inset-0 bg-cyan-400/20 rounded-xl blur-lg opacity-0 group-hover:opacity-100 transition-opacity duration-500"
                        ></div>
                        <div
                            class="relative w-11 h-11 bg-gradient-to-br from-white to-slate-100 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-all duration-300"
                        >
                            <img
                                src="/img/Logo.png"
                                alt="SLC"
                                class="h-8 w-auto object-contain"
                            />
                        </div>
                    </div>
                    <div>
                        <span class="text-2xl font-bold text-white">SLC</span>
                        <p
                            class="text-xs text-cyan-300/90 -mt-0.5 font-medium tracking-wide"
                        >
                            we collab
                        </p>
                    </div>
                </Link>

                <button
                    class="lg:hidden p-2 text-white/80 hover:text-white hover:bg-white/10 rounded-xl transition-all duration-200 hover:rotate-90"
                    @click="closeSidebar"
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
        </div>

        <!-- Mostrar según el acceso -->
        <template v-if="hasFullMenuAccess">
            <nav class="flex-1 px-3 py-6 overflow-y-auto min-h-0">
                <!-- Dashboard - Item destacado -->
                <Link
                    :href="menuSections.dashboard.href"
                    class="flex items-center gap-3 px-4 py-3.5 rounded-xl text-sm font-semibold transition-all duration-300 mb-6 relative overflow-hidden group text-white"
                    :class="{
                        'bg-gradient-to-r from-cyan-500/30 to-indigo-500/30 border border-cyan-400/40 shadow-lg shadow-cyan-500/20':
                            isSectionActive('dashboard'),
                        'hover:bg-white/5': !isSectionActive('dashboard'),
                    }"
                >
                    <div
                        v-if="isSectionActive('dashboard')"
                        class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-8 bg-gradient-to-b from-cyan-400 to-indigo-400 rounded-r-full"
                    ></div>
                    <div class="relative">
                        <i
                            :class="[
                                `fas ${menuSections.dashboard.icon} w-5 h-5 transition-all duration-300`,
                                isSectionActive('dashboard')
                                    ? 'text-white scale-110'
                                    : 'text-white/80 group-hover:scale-110',
                            ]"
                        ></i>
                    </div>
                    <span class="flex-1 text-white">{{
                        menuSections.dashboard.title
                    }}</span>
                    <span v-if="isSectionActive('dashboard')" class="relative">
                        <span
                            class="absolute inset-0 bg-cyan-400/40 rounded-full animate-ping opacity-60"
                        ></span>
                        <span
                            class="relative w-2 h-2 bg-cyan-300 rounded-full"
                        ></span>
                    </span>
                </Link>

                <!-- Separador -->
                <div class="relative my-4">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-white/10"></div>
                    </div>
                    <div class="relative flex justify-center">
                        <span
                            class="px-3 text-[10px] font-medium uppercase tracking-wider text-white/60 bg-transparent"
                            >Menú Principal</span
                        >
                    </div>
                </div>

                <!-- Secciones del menú -->
                <div class="space-y-1">
                    <div v-for="(section, key) in menuSections" :key="key">
                        <template v-if="key !== 'dashboard'">
                            <button
                                @click="toggleSubmenu(key)"
                                class="w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm font-medium transition-all duration-300 group text-white"
                                :class="{
                                    'bg-gradient-to-r from-cyan-500/15 to-indigo-500/15 border-l-2 border-cyan-400/50':
                                        isSubmenuOpen[key] ||
                                        isSectionActive(key),
                                    'hover:bg-white/5': !(
                                        isSubmenuOpen[key] ||
                                        isSectionActive(key)
                                    ),
                                }"
                                type="button"
                                :aria-expanded="isSubmenuOpen[key]"
                            >
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-7 h-7 rounded-lg flex items-center justify-center transition-all duration-300"
                                        :class="{
                                            'bg-cyan-500/25':
                                                isSubmenuOpen[key] ||
                                                isSectionActive(key),
                                            'bg-white/5 group-hover:bg-white/10':
                                                !(
                                                    isSubmenuOpen[key] ||
                                                    isSectionActive(key)
                                                ),
                                        }"
                                    >
                                        <i
                                            :class="[
                                                `fas ${section.icon} w-4 h-4 transition-all duration-300`,
                                                isSectionActive(key)
                                                    ? 'text-white scale-110'
                                                    : 'text-white/80',
                                            ]"
                                        ></i>
                                    </div>
                                    <span
                                        class="font-medium text-white"
                                        :class="{
                                            'text-cyan-200':
                                                isSectionActive(key),
                                        }"
                                    >
                                        {{ section.title }}
                                    </span>
                                    <span
                                        v-if="section.badge"
                                        class="ml-2 px-1.5 py-0.5 text-[9px] font-bold bg-cyan-500/30 text-white rounded-full"
                                    >
                                        {{ section.badge }}
                                    </span>
                                </div>
                                <div
                                    class="w-6 h-6 rounded-lg flex items-center justify-center transition-all duration-300"
                                    :class="{
                                        'bg-cyan-500/25': isSubmenuOpen[key],
                                        'group-hover:bg-white/5':
                                            !isSubmenuOpen[key],
                                    }"
                                >
                                    <i
                                        class="fas fa-chevron-down w-3 h-3 transition-all duration-300 text-white"
                                        :class="{
                                            'rotate-180': isSubmenuOpen[key],
                                        }"
                                    ></i>
                                </div>
                            </button>

                            <div v-show="isSubmenuOpen[key]" class="mt-2 mb-1">
                                <div class="relative">
                                    <div
                                        class="absolute left-7 top-0 bottom-0 w-px bg-gradient-to-b from-cyan-400/30 via-white/10 to-transparent"
                                    ></div>

                                    <div class="space-y-1 ml-4">
                                        <Link
                                            v-for="item in section.items"
                                            :key="item.key"
                                            :href="item.href"
                                            class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm transition-all duration-200 group/item relative text-white"
                                            :class="{
                                                'bg-cyan-500/20 border-l-2 border-cyan-400 shadow-lg shadow-cyan-500/10':
                                                    isItemActive(item.href),
                                                'hover:bg-white/5 hover:translate-x-1':
                                                    !isItemActive(item.href),
                                            }"
                                            :title="item.description"
                                        >
                                            <div
                                                class="w-6 flex justify-center"
                                            >
                                                <i
                                                    :class="[
                                                        `fas ${item.icon} w-3.5 h-3.5 transition-all duration-200`,
                                                        isItemActive(item.href)
                                                            ? 'text-white scale-110'
                                                            : 'text-white/75',
                                                    ]"
                                                ></i>
                                            </div>
                                            <span
                                                class="flex-1 leading-tight text-white"
                                                :class="{
                                                    'font-semibold text-cyan-200':
                                                        isItemActive(item.href),
                                                }"
                                            >
                                                {{ item.label }}
                                            </span>
                                            <span
                                                v-if="item.badge"
                                                class="px-1.5 py-0.5 text-[9px] font-bold rounded-full"
                                                :class="{
                                                    'bg-cyan-500/40 text-white':
                                                        item.badge === 'Nuevo',
                                                    'bg-amber-500/40 text-white':
                                                        item.badge ===
                                                        'Pendientes',
                                                    'bg-red-500/40 text-white':
                                                        item.badge === 'Admin',
                                                }"
                                            >
                                                {{ item.badge }}
                                            </span>
                                            <div
                                                v-if="isItemActive(item.href)"
                                                class="w-1.5 h-1.5 rounded-full bg-cyan-300 animate-pulse"
                                            ></div>
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </nav>
        </template>

        <!-- Mensaje para roles sin acceso -->
        <div v-else class="flex-1 flex items-center justify-center p-8">
            <div class="text-center">
                <i class="fas fa-lock text-5xl text-white/30 mb-4"></i>
                <h3 class="text-white font-semibold text-lg mb-2">
                    Acceso Restringido
                </h3>
                <p class="text-white/60 text-sm">
                    No tienes permisos para acceder al menú de administración.
                </p>
                <p class="text-white/40 text-xs mt-3">
                    Rol actual: {{ getUserRole()?.nombre || "No detectado" }}
                </p>
            </div>
        </div>

        <!-- Footer: Perfil de usuario -->
        <div
            class="flex-shrink-0 p-3 border-t border-white/10 bg-gradient-to-t from-black/10 to-transparent"
        >
            <Dropdown align="right" width="48">
                <template #trigger>
                    <button
                        class="group w-full flex items-center gap-3 px-3 py-2.5 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 hover:border-white/20 transition-all duration-300 hover:shadow-lg hover:shadow-cyan-500/10"
                        type="button"
                    >
                        <div class="relative">
                            <div
                                class="absolute inset-0 bg-gradient-to-br from-cyan-400 to-indigo-500 rounded-xl blur-md opacity-0 group-hover:opacity-40 transition-opacity duration-300"
                            ></div>
                            <div
                                class="relative w-10 h-10 rounded-xl bg-gradient-to-br from-cyan-400 to-indigo-500 flex items-center justify-center text-white font-semibold text-base shadow-lg"
                            >
                                {{
                                    $page.props.auth.user?.name
                                        ?.charAt(0)
                                        ?.toUpperCase() || "U"
                                }}
                            </div>
                            <div
                                class="absolute -bottom-0.5 -right-0.5 w-3 h-3 rounded-full bg-emerald-400 border-2 border-[#023E8A] shadow-lg"
                            ></div>
                        </div>

                        <div class="flex-1 min-w-0 text-left">
                            <p
                                class="text-sm font-semibold text-white truncate group-hover:text-cyan-200 transition-colors"
                            >
                                {{ $page.props.auth.user?.name || "Usuario" }}
                            </p>
                            <p
                                class="text-xs text-white/60 truncate group-hover:text-white/80 transition-colors"
                            >
                                {{ $page.props.auth.user?.email || "" }}
                            </p>
                        </div>

                        <i
                            class="fas fa-chevron-down text-white/60 text-xs group-hover:text-white transition-all group-hover:rotate-180"
                        ></i>
                    </button>
                </template>

                <template #content>
                    <div class="py-2">
                        <div
                            class="px-4 py-3 border-b border-slate-200/20 dark:border-slate-700 bg-gradient-to-r from-slate-50 to-cyan-50 dark:from-slate-800 dark:to-cyan-900/20"
                        >
                            <p
                                class="text-sm font-bold text-slate-900 dark:text-slate-100"
                            >
                                {{ $page.props.auth.user?.name }}
                            </p>
                            <p
                                class="text-xs text-slate-500 dark:text-slate-400"
                            >
                                {{ $page.props.auth.user?.email }}
                            </p>
                        </div>

                        <DropdownLink
                            :href="route('profile.show')"
                            class="flex items-center gap-3 px-4 py-2.5 text-slate-700 dark:text-slate-300 hover:bg-cyan-50 dark:hover:bg-slate-800 transition-colors"
                        >
                            <i
                                class="fas fa-user-circle w-4 h-4 text-cyan-500"
                            ></i>
                            <span>Mi Perfil</span>
                        </DropdownLink>

                        <DropdownLink
                            v-if="$page.props.ziggy?.routes?.['profile.edit']"
                            :href="route('profile.edit')"
                            class="flex items-center gap-3 px-4 py-2.5 text-slate-700 dark:text-slate-300 hover:bg-cyan-50 dark:hover:bg-slate-800 transition-colors"
                        >
                            <i class="fas fa-cog w-4 h-4 text-slate-500"></i>
                            <span>Configuración</span>
                        </DropdownLink>

                        <div
                            class="border-t border-slate-200/20 dark:border-slate-700 my-2"
                        ></div>

                        <form @submit.prevent="logout">
                            <DropdownLink
                                as="button"
                                class="flex items-center gap-3 px-4 py-2.5 text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-900/20 w-full text-left transition-colors"
                            >
                                <i class="fas fa-sign-out-alt w-4 h-4"></i>
                                <span>Cerrar Sesión</span>
                            </DropdownLink>
                        </form>
                    </div>
                </template>
            </Dropdown>
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
            class="fixed inset-0 bg-black/40 backdrop-blur-sm z-40 lg:hidden"
            @click="closeSidebar"
        ></div>
    </transition>
</template>

<style scoped>
nav::-webkit-scrollbar {
    width: 5px;
}

nav::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.03);
    border-radius: 10px;
}

nav::-webkit-scrollbar-thumb {
    background: linear-gradient(
        180deg,
        rgba(34, 211, 238, 0.4),
        rgba(99, 102, 241, 0.4)
    );
    border-radius: 10px;
}

nav::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(
        180deg,
        rgba(34, 211, 238, 0.6),
        rgba(99, 102, 241, 0.6)
    );
}

@keyframes pulse {
    0%,
    100% {
        opacity: 1;
    }
    50% {
        opacity: 0.5;
    }
}

.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

.rotate-180 {
    transform: rotate(180deg);
}

aside {
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}
</style>
