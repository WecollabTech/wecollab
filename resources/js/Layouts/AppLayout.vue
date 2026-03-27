<script setup>
import { ref, onMounted, onUnmounted, computed } from "vue";
import { Head, router } from "@inertiajs/vue3";
import Banner from "@/Components/Banner.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import ResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";
import Sidebar from "@/Componentes/Sidebar.vue";
import HeroSection from "@/Components/Dashboard/HeroSection.vue";

defineProps({
    title: String,
});

const showingNavigationDropdown = ref(false);
const searchQuery = ref("");
const isScrolled = ref(false);

// ✅ Helper seguro para rutas Ziggy
const safeRoute = (name, params = {}) => {
    return route().has(name) ? route(name, params) : "#";
};

const switchToTeam = (team) => {
    router.put(
        route("current-team.update"),
        { team_id: team.id },
        { preserveState: false },
    );
};

const logout = () => {
    router.post(route("logout"));
};

const handleScroll = () => {
    isScrolled.value = window.scrollY > 20;
};

const handleResize = () => {
    if (window.innerWidth >= 1024) {
        showingNavigationDropdown.value = false;
    }
};

const handleKeydown = (e) => {
    if (e.key === "Escape" && showingNavigationDropdown.value) {
        showingNavigationDropdown.value = false;
    }
};

onMounted(() => {
    window.addEventListener("scroll", handleScroll);
    window.addEventListener("resize", handleResize);
    window.addEventListener("keydown", handleKeydown);
    handleScroll();
});

onUnmounted(() => {
    window.removeEventListener("scroll", handleScroll);
    window.removeEventListener("resize", handleResize);
    window.removeEventListener("keydown", handleKeydown);
    document.body.style.overflow = "";
});
</script>

<template>
    <div
        class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-slate-100 dark:from-gray-900 dark:via-blue-950 dark:to-gray-900 flex transition-colors duration-300"
    >
        <Head :title="title" />
        <Banner />

        <!-- ==================== SIDEBAR (FIJO) ==================== -->
        <Sidebar
            :showingNavigationDropdown="showingNavigationDropdown"
            @update:showingNavigationDropdown="
                showingNavigationDropdown = $event
            "
        />

        <!-- ==================== MAIN CONTENT WRAPPER ==================== -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- ==================== TOP NAVBAR ==================== -->
            <!-- ✅ Agregado lg:ml-72 para no quedar detrás del sidebar fijo -->
            <nav
                class="bg-gradient-to-br from-[#1a3080] via-[#223e9c] to-[#1a3080] text-white backdrop-blur-xl border-b border-white/10 sticky top-0 z-40 transition-all duration-300 lg:ml-72"
                :class="{
                    'shadow-2xl shadow-blue-900/40 backdrop-blur-2xl':
                        isScrolled,
                    'opacity-95': isScrolled,
                }"
            >
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between items-center h-20">
                        <!-- LEFT SIDE -->
                        <div class="flex items-center gap-4">
                            <!-- Mobile Menu Button -->
                            <button
                                class="lg:hidden p-2 text-white/70 hover:text-white hover:bg-white/10 rounded-xl transition-all duration-200 hover:rotate-90"
                                @click="showingNavigationDropdown = true"
                                aria-label="Abrir menú"
                                title="Menú"
                            >
                                <svg
                                    class="w-6 h-6"
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
                            </button>

                            <!-- Breadcrumb -->
                            <div
                                class="hidden sm:flex items-center gap-2 text-sm text-white/90 font-medium"
                            >
                                <span
                                    class="hover:text-cyan-300 cursor-pointer transition-colors"
                                    >Home</span
                                >
                                <svg
                                    class="w-4 h-4 text-white/50"
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
                                <span class="text-white font-semibold"
                                    >Dashboard</span
                                >
                            </div>
                        </div>

                        <!-- RIGHT SIDE -->
                        <div class="flex items-center gap-3">
                            <!-- 🔔 Notifications -->
                            <button
                                class="relative p-2.5 text-white/70 hover:text-white hover:bg-white/10 rounded-xl transition-all duration-200 hover:scale-110 group"
                                title="Notificaciones"
                                aria-label="Notificaciones"
                            >
                                <i
                                    class="fas fa-bell w-5 h-5 transition-colors group-hover:text-cyan-400"
                                ></i>
                                <span
                                    class="absolute top-2 right-2 w-2.5 h-2.5 bg-gradient-to-r from-emerald-400 to-cyan-400 rounded-full ring-2 ring-[#1a3080] animate-pulse"
                                ></span>
                            </button>

                            <!-- ✉️ Messages -->
                            <!-- <button
                                class="relative p-2.5 text-white/70 hover:text-white hover:bg-white/10 rounded-xl transition-all duration-200 hover:scale-110 group"
                                title="Mensajes"
                                aria-label="Mensajes"
                            >
                                <i
                                    class="fas fa-envelope w-5 h-5 transition-colors group-hover:text-cyan-400"
                                ></i>
                                <span
                                    class="absolute -top-0.5 -right-0.5 w-5 h-5 bg-gradient-to-r from-rose-500 to-pink-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center ring-2 ring-[#1a3080]"
                                    >3</span
                                >
                            </button> -->

                            <!-- 👤 PERFIL DROPDOWN -->
                            <div class="relative" style="z-index: 99999">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <button
                                            class="group relative flex items-center gap-3 px-3 py-2 rounded-xl bg-white/10 hover:bg-white/15 backdrop-blur-sm border border-white/10 hover:border-white/20 transition-all duration-300 hover:shadow-lg hover:shadow-cyan-500/10"
                                            title="Menú de usuario"
                                            aria-label="Menú de usuario"
                                        >
                                            <!-- Avatar con efectos -->
                                            <div class="relative">
                                                <div
                                                    class="absolute inset-0 bg-cyan-400/30 rounded-xl blur-md opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                                                ></div>

                                                <img
                                                    v-if="
                                                        $page.props.auth.user
                                                            ?.fotoperfil
                                                    "
                                                    :src="`/storage/profile-photos/${$page.props.auth.user.fotoperfil}`"
                                                    class="relative w-11 h-11 rounded-xl object-cover border-2 border-white/20 group-hover:border-cyan-400/50 transition-colors duration-300"
                                                    alt="Foto de perfil"
                                                />
                                                <div
                                                    v-else
                                                    class="relative w-11 h-11 rounded-xl bg-gradient-to-br from-cyan-400 to-blue-500 flex items-center justify-center text-white font-bold text-lg shadow-lg"
                                                >
                                                    {{
                                                        $page.props.auth.user?.name
                                                            ?.charAt(0)
                                                            ?.toUpperCase() ||
                                                        "U"
                                                    }}
                                                </div>

                                                <!-- Online Indicator -->
                                                <span
                                                    class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 bg-emerald-400 border-2 border-[#1a3080] rounded-full shadow-lg shadow-emerald-500/30"
                                                    title="En línea"
                                                ></span>
                                            </div>

                                            <!-- Username (desktop) -->
                                            <div
                                                class="hidden md:block flex-1 min-w-0 text-left"
                                            >
                                                <p
                                                    class="text-sm font-semibold text-white truncate group-hover:text-cyan-300 transition-colors duration-300"
                                                >
                                                    {{
                                                        $page.props.auth.user?.name?.split(
                                                            " ",
                                                        )[0] || "Usuario"
                                                    }}
                                                </p>
                                                <p
                                                    class="text-xs text-white/60 truncate"
                                                >
                                                    {{
                                                        $page.props.auth.user?.email?.split(
                                                            "@",
                                                        )[0] || ""
                                                    }}
                                                </p>
                                            </div>

                                            <!-- Chevron -->
                                            <i
                                                class="fas fa-chevron-down w-4 h-4 text-white/40 group-hover:text-white/70 transition-colors"
                                            ></i>
                                        </button>
                                    </template>

                                    <!-- DROPDOWN CONTENT -->
                                    <template #content>
                                        <div class="py-1 min-w-64">
                                            <!-- Header con Info de Usuario -->
                                            <div
                                                class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-blue-50 to-cyan-50 dark:from-gray-800 dark:to-blue-900/20"
                                            >
                                                <div
                                                    class="flex items-center gap-3"
                                                >
                                                    <img
                                                        :src="
                                                            $page.props.auth
                                                                .user
                                                                ?.fotoperfil
                                                                ? `/storage/profile-photos/${$page.props.auth.user.fotoperfil}`
                                                                : `https://ui-avatars.com/api/?name=${encodeURIComponent($page.props.auth.user?.name || 'Usuario')}&background=0D8ABC&color=fff&size=128`
                                                        "
                                                        class="w-12 h-12 rounded-xl object-cover border-2 border-white/20"
                                                        alt="Perfil"
                                                    />
                                                    <div class="flex-1 min-w-0">
                                                        <p
                                                            class="text-sm font-semibold text-gray-900 dark:text-white truncate"
                                                        >
                                                            {{
                                                                $page.props.auth
                                                                    .user?.name
                                                            }}
                                                        </p>
                                                        <p
                                                            class="text-xs text-gray-500 dark:text-gray-400 truncate"
                                                        >
                                                            {{
                                                                $page.props.auth
                                                                    .user?.email
                                                            }}
                                                        </p>
                                                        <span
                                                            v-if="
                                                                $page.props.auth
                                                                    .user?.rol
                                                            "
                                                            class="inline-flex items-center mt-1 px-2 py-0.5 bg-blue-100 dark:bg-blue-900/30 rounded-full text-[10px] font-medium text-blue-700 dark:text-blue-300"
                                                        >
                                                            {{
                                                                $page.props.auth
                                                                    .user.rol
                                                            }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Links del Menú -->
                                            <div class="py-1">
                                                <DropdownLink
                                                    :href="
                                                        safeRoute(
                                                            'profile.show',
                                                        )
                                                    "
                                                    class="flex items-center gap-2.5 px-4 py-2.5 text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-gray-800 hover:text-blue-600 dark:hover:text-cyan-400 transition-all duration-200 group"
                                                >
                                                    <i
                                                        class="fas fa-user w-4 h-4 text-blue-500 group-hover:scale-110 transition-transform"
                                                    ></i>
                                                    <span
                                                        class="text-sm font-medium"
                                                        >Mi Perfil</span
                                                    >
                                                </DropdownLink>

                                                <DropdownLink
                                                    v-if="
                                                        $page.props.jetstream
                                                            ?.canUpdateProfileInformation ||
                                                        route().has(
                                                            'profile.edit',
                                                        )
                                                    "
                                                    :href="
                                                        safeRoute(
                                                            'profile.edit',
                                                        )
                                                    "
                                                    class="flex items-center gap-2.5 px-4 py-2.5 text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-gray-800 hover:text-blue-600 dark:hover:text-cyan-400 transition-all duration-200 group"
                                                >
                                                    <i
                                                        class="fas fa-cog w-4 h-4 text-blue-500 group-hover:scale-110 transition-transform"
                                                    ></i>
                                                    <span
                                                        class="text-sm font-medium"
                                                        >Configuración</span
                                                    >
                                                </DropdownLink>

                                                <DropdownLink
                                                    v-if="
                                                        $page.props.jetstream
                                                            ?.hasApiFeatures
                                                    "
                                                    :href="
                                                        safeRoute(
                                                            'api-tokens.index',
                                                        )
                                                    "
                                                    class="flex items-center gap-2.5 px-4 py-2.5 text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-gray-800 hover:text-blue-600 dark:hover:text-cyan-400 transition-all duration-200 group"
                                                >
                                                    <i
                                                        class="fas fa-key w-4 h-4 text-blue-500 group-hover:scale-110 transition-transform"
                                                    ></i>
                                                    <span
                                                        class="text-sm font-medium"
                                                        >API Tokens</span
                                                    >
                                                </DropdownLink>
                                            </div>

                                            <!-- Divider -->
                                            <div
                                                class="border-t border-gray-200 dark:border-gray-700 my-1"
                                            ></div>

                                            <!-- 🚪 Cerrar Sesión -->
                                            <div class="py-1">
                                                <form @submit.prevent="logout">
                                                    <DropdownLink
                                                        as="button"
                                                        class="flex items-center gap-2.5 px-4 py-2.5 w-full text-left text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-all duration-200 group"
                                                    >
                                                        <i
                                                            class="fas fa-right-from-bracket w-4 h-4 group-hover:translate-x-0.5 transition-transform"
                                                        ></i>
                                                        <span
                                                            class="text-sm font-medium"
                                                            >Cerrar Sesión</span
                                                        >
                                                    </DropdownLink>
                                                </form>
                                            </div>
                                        </div>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- ==================== HERO SECTION (Opcional) ==================== -->
            <!-- <HeroSection :userName="$page.props.auth.user.name" /> -->

            <!-- ==================== MOBILE MENU OVERLAY ==================== -->
            <div
                :class="{
                    block: showingNavigationDropdown,
                    hidden: !showingNavigationDropdown,
                }"
                class="sm:hidden bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700"
            >
                <div class="pt-2 pb-3 space-y-1">
                    <ResponsiveNavLink
                        :href="route('dashboard')"
                        :active="route().current('dashboard')"
                    >
                        Dashboard
                    </ResponsiveNavLink>
                </div>
                <div
                    class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-700"
                >
                    <div class="flex items-center px-4">
                        <div
                            v-if="$page.props.jetstream?.managesProfilePhotos"
                            class="shrink-0 me-3"
                        >
                            <img
                                class="size-10 rounded-full object-cover"
                                :src="$page.props.auth.user.profile_photo_url"
                                :alt="$page.props.auth.user.name"
                            />
                        </div>
                        <div>
                            <div
                                class="font-medium text-base text-gray-800 dark:text-white"
                            >
                                {{ $page.props.auth.user.name }}
                            </div>
                            <div
                                class="font-medium text-sm text-gray-500 dark:text-gray-400"
                            >
                                {{ $page.props.auth.user.email }}
                            </div>
                        </div>
                    </div>
                    <div class="mt-3 space-y-1">
                        <ResponsiveNavLink
                            :href="safeRoute('profile.show')"
                            :active="route().current('profile.show')"
                        >
                            Profile
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            v-if="$page.props.jetstream?.hasApiFeatures"
                            :href="safeRoute('api-tokens.index')"
                            :active="route().current('api-tokens.index')"
                        >
                            API Tokens
                        </ResponsiveNavLink>
                        <form method="POST" @submit.prevent="logout">
                            <ResponsiveNavLink as="button"
                                >Log Out</ResponsiveNavLink
                            >
                        </form>

                        <!-- Team Features -->
                        <template v-if="$page.props.jetstream?.hasTeamFeatures">
                            <div
                                class="border-t border-gray-200 dark:border-gray-700 my-2"
                            />
                            <div class="block px-4 py-2 text-xs text-gray-400">
                                Manage Team
                            </div>
                            <ResponsiveNavLink
                                :href="
                                    safeRoute(
                                        'teams.show',
                                        $page.props.auth.user.current_team,
                                    )
                                "
                                :active="route().current('teams.show')"
                            >
                                Team Settings
                            </ResponsiveNavLink>
                            <ResponsiveNavLink
                                v-if="$page.props.jetstream?.canCreateTeams"
                                :href="safeRoute('teams.create')"
                                :active="route().current('teams.create')"
                            >
                                Create New Team
                            </ResponsiveNavLink>

                            <!-- Switch Teams -->
                            <template
                                v-if="
                                    $page.props.auth.user?.all_teams?.length > 1
                                "
                            >
                                <div
                                    class="border-t border-gray-200 dark:border-gray-700 my-2"
                                />
                                <div
                                    class="block px-4 py-2 text-xs text-gray-400"
                                >
                                    Switch Teams
                                </div>
                                <template
                                    v-for="team in $page.props.auth.user
                                        .all_teams"
                                    :key="team.id"
                                >
                                    <form @submit.prevent="switchToTeam(team)">
                                        <ResponsiveNavLink as="button">
                                            <div class="flex items-center">
                                                <svg
                                                    v-if="
                                                        team.id ==
                                                        $page.props.auth.user
                                                            .current_team_id
                                                    "
                                                    class="me-2 size-5 text-green-400"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    fill="none"
                                                    viewBox="0 0 24 24"
                                                    stroke-width="1.5"
                                                    stroke="currentColor"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                                    />
                                                </svg>
                                                <div>{{ team.name }}</div>
                                            </div>
                                        </ResponsiveNavLink>
                                    </form>
                                </template>
                            </template>
                        </template>
                    </div>
                </div>
            </div>

            <!-- ==================== PAGE CONTENT ==================== -->

            <!-- ✅ Header con lg:ml-72 para compensar sidebar fijo -->
            <header
                v-if="$slots.header"
                class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700 transition-all duration-300 lg:ml-72"
            >
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- ✅ Main con lg:ml-72 para compensar sidebar fijo -->
            <main
                class="flex-1 px-4 sm:px-6 lg:px-8 py-6 lg:ml-72 transition-all duration-300 min-h-[calc(100vh-140px)]"
            >
                <div class="max-w-9xl mx-auto w-full">
                    <slot />
                </div>
            </main>
        </div>
    </div>
</template>

<style scoped>
/* Transiciones globales suaves */
* {
    transition-property:
        background-color, border-color, color, fill, stroke, transform, opacity;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 150ms;
}

/* Animaciones Blob */
@keyframes blob {
    0%,
    100% {
        transform: translate(0, 0) scale(1);
    }
    33% {
        transform: translate(30px, -50px) scale(1.1);
    }
    66% {
        transform: translate(-20px, 20px) scale(0.9);
    }
}
.animate-blob {
    animation: blob 7s infinite;
}
.animation-delay-2000 {
    animation-delay: 2s;
}
.animation-delay-4000 {
    animation-delay: 4s;
}

/* Pulse lento para background */
.animate-pulse-slow {
    animation: pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite;
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

/* Dropdown: Z-Index máximo */
:deep(.dropdown-content),
:deep([data-dropdown]) {
    z-index: 999999 !important;
}

/* Animación de entrada/salida del dropdown */
:deep(.dropdown-enter-active),
:deep(.dropdown-leave-active) {
    transition:
        opacity 0.2s ease,
        transform 0.2s ease;
}
:deep(.dropdown-enter-from),
:deep(.dropdown-leave-to) {
    opacity: 0;
    transform: translateY(-8px) scale(0.98);
}

/* Touch targets para móvil (accesibilidad) */
@media (max-width: 767px) {
    button,
    [role="button"],
    a {
        min-height: 44px;
        min-width: 44px;
    }
}

/* Prevenir zoom automático en inputs en iOS */
@media screen and (-webkit-min-device-pixel-ratio: 0) {
    input,
    select,
    textarea {
        font-size: 16px !important;
    }
}

/* Hover scale para iconos Font Awesome */
i.fas {
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
</style>
