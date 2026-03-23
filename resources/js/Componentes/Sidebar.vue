<script setup>
import { ref, onMounted, onUnmounted, computed } from "vue";
import { Link, router } from "@inertiajs/vue3";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import NavLink from "@/Components/NavLink.vue";

const props = defineProps({
    showingNavigationDropdown: Boolean,
});

const emit = defineEmits(["update:showingNavigationDropdown"]);

const isSubmenuOpen = ref({
    tutorial: false,
    usuarios: false,
    configuracion: false,
});

const toggleSubmenu = (menu) => {
    // Cerrar otros submenús
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

const tutorialItems = computed(() => [
    { href: "/categorias", icon: "fa-layer-group", label: "Categorías" },
    { href: "/subcategorias", icon: "fa-sitemap", label: "Subcategorías" },
    { href: "/tutoriales", icon: "fa-video", label: "Videos Capacitación" },
    { href: "/comentarios", icon: "fa-comments", label: "Comentarios" },
]);

const usuariosItems = computed(() => [
    { href: "/usuarios/create", icon: "fa-user-plus", label: "Crear Usuario" },
    { href: "/usuarios", icon: "fa-user-edit", label: "Editar Usuario" },
    { href: "/usuarios", icon: "fa-user-times", label: "Eliminar Usuario" },
]);

const configuracionItems = computed(() => [
    { href: "/permisos", icon: "fa-key", label: "Permisos" },
    { href: "/roles", icon: "fa-user-shield", label: "Roles" },
    { href: "/reportes", icon: "fa-chart-bar", label: "Reportes" },
]);

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
        class="fixed inset-y-0 left-0 z-50 w-72 bg-gradient-to-b from-[#1a3080] to-[#223e9c] shadow-2xl transform transition-all duration-300 ease-out lg:translate-x-0 lg:static lg:inset-0"
    >
        <!-- Sidebar Header con Logo -->
        <div
            class="flex items-center justify-between h-20 px-6 border-b border-white/10 bg-white/5 backdrop-blur-sm"
        >
            <Link
                :href="route('dashboard')"
                class="flex items-center gap-3 group"
            >
                <div class="relative">
                    <!-- Glow effect -->
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

            <!-- Close Button Mobile -->
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

        <!-- Sidebar Navigation -->
        <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
            <!-- Dashboard - Principal -->
            <div class="mb-6">
                <NavLink
                    :href="route('dashboard')"
                    :active="route().current('dashboard')"
                    class="group relative flex items-center gap-3 px-4 py-3.5 rounded-xl text-sm font-semibold transition-all duration-300 text-white hover:bg-white/10"
                    :class="{
                        'bg-gradient-to-r from-cyan-500/20 to-blue-500/20 border border-cyan-400/30 shadow-lg shadow-cyan-500/10':
                            route().current('dashboard'),
                    }"
                >
                    <!-- Indicador activo -->
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

            <!-- Section Title -->
            <div class="px-4 mb-2">
                <p
                    class="text-xs font-semibold text-white/40 uppercase tracking-wider"
                >
                    Components
                </p>
            </div>

            <!-- Tutorial Section -->
            <div class="mb-2">
                <button
                    @click="toggleSubmenu('tutorial')"
                    class="w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm font-medium text-white/90 hover:bg-white/10 hover:text-white transition-all duration-200 group"
                    type="button"
                    :aria-expanded="isSubmenuOpen.tutorial"
                >
                    <div class="flex items-center gap-3">
                        <div class="relative">
                            <i
                                class="fas fa-columns w-5 h-5 text-white/60 group-hover:text-cyan-400 transition-colors duration-300"
                            ></i>
                        </div>
                        <span>Tutorial</span>
                    </div>
                    <i
                        class="fas fa-chevron-down w-4 h-4 text-white/40 transition-all duration-300 group-hover:text-white/70"
                        :class="{ 'rotate-180': isSubmenuOpen.tutorial }"
                    ></i>
                </button>

                <!-- Submenu con animación -->
                <div
                    v-show="isSubmenuOpen.tutorial"
                    class="mt-1 ml-4 pl-4 border-l-2 border-white/10 space-y-1 overflow-hidden transition-all duration-300"
                >
                    <NavLink
                        v-for="item in tutorialItems"
                        :key="item.href"
                        :href="item.href"
                        class="group flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm text-white/70 hover:bg-white/10 hover:text-white hover:translate-x-1 transition-all duration-200"
                    >
                        <i
                            :class="`fas ${item.icon} w-4 h-4 text-white/50 group-hover:text-cyan-400 transition-colors`"
                        ></i>
                        <span>{{ item.label }}</span>
                    </NavLink>
                </div>
            </div>

            <!-- Usuarios Section -->
            <div class="mb-2">
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
                        v-for="item in usuariosItems"
                        :key="item.href"
                        :href="item.href"
                        class="group flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm text-white/70 hover:bg-white/10 hover:text-white hover:translate-x-1 transition-all duration-200"
                    >
                        <i
                            :class="`fas ${item.icon} w-4 h-4 text-white/50 group-hover:text-cyan-400 transition-colors`"
                        ></i>
                        <span>{{ item.label }}</span>
                    </NavLink>
                </div>
            </div>

            <!-- Configuración Section -->
            <div class="mb-2">
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
                        v-for="item in configuracionItems"
                        :key="item.href"
                        :href="item.href"
                        class="group flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm text-white/70 hover:bg-white/10 hover:text-white hover:translate-x-1 transition-all duration-200"
                    >
                        <i
                            :class="`fas ${item.icon} w-4 h-4 text-white/50 group-hover:text-cyan-400 transition-colors`"
                        ></i>
                        <span>{{ item.label }}</span>
                    </NavLink>
                </div>
            </div>
        </nav>

        <!-- Sidebar Footer - User Profile -->
        <div
            class="p-4 border-t border-white/10 bg-gradient-to-b from-transparent to-black/20"
        >
            <Dropdown align="right" width="48">
                <template #trigger>
                    <button
                        class="group w-full flex items-center gap-3 px-4 py-3 rounded-xl bg-white/10 hover:bg-white/15 backdrop-blur-sm border border-white/10 hover:border-white/20 transition-all duration-300 hover:shadow-lg hover:shadow-cyan-500/10"
                        type="button"
                    >
                        <div class="relative">
                            <!-- Anillo animado -->
                            <div
                                class="absolute inset-0 bg-cyan-400/30 rounded-xl blur-md opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                            ></div>
                            <img
                                v-if="
                                    $page.props.jetstream
                                        ?.managesProfilePhotos &&
                                    $page.props.auth.user?.profile_photo_url
                                "
                                :src="$page.props.auth.user.profile_photo_url"
                                :alt="$page.props.auth.user.name"
                                class="relative w-11 h-11 rounded-xl object-cover border-2 border-white/20 group-hover:border-cyan-400/50 transition-colors duration-300"
                            />
                            <div
                                v-else
                                class="relative w-11 h-11 rounded-xl bg-gradient-to-br from-cyan-400 to-blue-500 flex items-center justify-center text-white font-bold text-lg shadow-lg"
                            >
                                {{
                                    $page.props.auth.user?.name
                                        ?.charAt(0)
                                        ?.toUpperCase() || "U"
                                }}
                            </div>
                            <!-- Indicador online -->
                            <span
                                class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 bg-emerald-400 border-2 border-[#1a3080] rounded-full shadow-lg shadow-emerald-500/30"
                            ></span>
                        </div>
                        <div class="flex-1 min-w-0 text-left">
                            <p
                                class="text-sm font-semibold text-white truncate group-hover:text-cyan-300 transition-colors duration-300"
                            >
                                {{ $page.props.auth.user?.name || "Usuario" }}
                            </p>
                            <p class="text-xs text-white/60 truncate">
                                {{ $page.props.auth.user?.email || "" }}
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
                        </div>

                        <DropdownLink
                            :href="route('profile.show')"
                            class="flex items-center gap-2 text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-gray-800"
                        >
                            <i class="fas fa-user w-4 h-4 text-blue-500"></i>
                            Perfil
                        </DropdownLink>

                        <DropdownLink
                            v-if="$page.props.jetstream?.hasApiFeatures"
                            :href="route('api-tokens.index')"
                            class="flex items-center gap-2 text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-gray-800"
                        >
                            <i class="fas fa-key w-4 h-4 text-blue-500"></i>
                            API Tokens
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

        <!-- Botón Upgrade -->
        <div class="p-4">
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
/* Custom scrollbar elegante */
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

/* Rotate icon */
.rotate-180 {
    transform: rotate(180deg);
}

/* Animación suave para submenús */
.v-enter-active,
.v-leave-active {
    transition: all 0.3s ease;
}

.v-enter-from,
.v-leave-to {
    opacity: 0;
    transform: translateY(-10px);
}
</style>
