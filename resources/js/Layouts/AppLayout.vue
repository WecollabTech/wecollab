<script setup>
import { ref, onMounted, onUnmounted } from "vue";
import { Head, Link, router } from "@inertiajs/vue3";
import ApplicationMark from "@/Components/ApplicationMark.vue";
import Banner from "@/Components/Banner.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import NavLink from "@/Components/NavLink.vue";
import ResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";
import Sidebar from "@/Componentes/Sidebar.vue";
import Buscador from "@/Componentes/Buscador.vue";

defineProps({
    title: String,
});

const showingNavigationDropdown = ref(false);
const isSidebarOpen = ref(false);
const mobileSidebarOpen = ref(false);

const handleResize = () => {
    if (window.innerWidth >= 768) {
        showingNavigationDropdown.value = false;
        mobileSidebarOpen.value = false;
    }
};

onMounted(() => {
    window.addEventListener("resize", handleResize);
    handleResize();
});

onUnmounted(() => {
    window.removeEventListener("resize", handleResize);
});

const switchToTeam = (team) => {
    router.put(
        route("current-team.update"),
        {
            team_id: team.id,
        },
        {
            preserveState: false,
        }
    );
};

const logout = () => {
    router.post(route("logout"));
};

const toggleMobileSidebar = () => {
    mobileSidebarOpen.value = !mobileSidebarOpen.value;
};
</script>

<template>
    <div>
        <Head :title="title" />
        <Banner />

        <div class="min-h-screen bg-gray-100 flex">
            <!-- Sidebar para desktop -->
            <Sidebar
                class="hidden md:block fixed inset-y-0 z-30 w-64 shadow-lg"
            />

            <!-- Sidebar para móvil -->
            <div
                class="md:hidden fixed inset-0 z-40"
                v-show="mobileSidebarOpen"
                @click="mobileSidebarOpen = false"
            >
                <div
                    class="fixed inset-y-0 left-0 w-64 bg-white shadow-lg transform transition-transform duration-300 ease-in-out"
                    :class="{
                        'translate-x-0': mobileSidebarOpen,
                        '-translate-x-full': !mobileSidebarOpen,
                    }"
                    @click.stop
                >
                    <Sidebar :isMobile="true" />
                </div>
            </div>

            <!-- Contenido principal -->
            <div class="flex-1 flex flex-col md:ml-64">
                <!-- Barra de navegación fija (header principal) -->
                <nav
                    class="bg-indigo-600 fixed top-0 left-0 right-0 z-20 h-16 shadow-md flex items-center"
                    style="background-color: #6c63ff"
                >
                    <div class="relative w-full">
                        <!-- Contenedor principal dentro de max-w-7xl -->
                        <div
                            class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between h-full"
                        >
                            <!-- Logo y botón hamburguesa (mobile) -->
                            <div class="flex items-center md:hidden">
                                <button
                                    @click="toggleMobileSidebar"
                                    class="text-white mr-4 focus:outline-none"
                                >
                                    <svg
                                        class="h-6 w-6"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M4 6h16M4 12h16M4 18h16"
                                        />
                                    </svg>
                                </button>
                                <Link :href="route('dashboard')">
                                    <img
                                        src="/img/logo.png"
                                        alt="Logo"
                                        class="h-8 w-auto"
                                    />
                                </Link>
                            </div>

                            <!-- Logo (desktop) -->
                            <div class="hidden md:flex items-center">
                                <Link :href="route('dashboard')">
                                    <img
                                        src="/img/logo.png"
                                        alt="Logo"
                                        class="h-8 w-auto"
                                    />
                                </Link>
                            </div>

                            <!-- Teams Dropdown (permanece en su lugar) -->
                            <div v-if="$page.props.jetstream.hasTeamFeatures">
                                <Dropdown align="right" width="60">
                                    <template #trigger>
                                        <button
                                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:text-gray-900 focus:outline-none transition"
                                        >
                                            {{
                                                $page.props.auth.user
                                                    .current_team.name
                                            }}
                                            <svg
                                                class="ml-2 -mr-0.5 h-4 w-4"
                                                xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 20 20"
                                                fill="currentColor"
                                            >
                                                <path
                                                    fill-rule="evenodd"
                                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                    clip-rule="evenodd"
                                                />
                                            </svg>
                                        </button>
                                    </template>

                                    <template #content>
                                        <div class="w-60">
                                            <div
                                                class="block px-4 py-2 text-xs text-gray-400"
                                            >
                                                Gestionar equipo
                                            </div>
                                            <DropdownLink
                                                :href="
                                                    route(
                                                        'teams.show',
                                                        $page.props.auth.user
                                                            .current_team
                                                    )
                                                "
                                            >
                                                Configuración del equipo
                                            </DropdownLink>
                                            <DropdownLink
                                                v-if="
                                                    $page.props.jetstream
                                                        .canCreateTeams
                                                "
                                                :href="route('teams.create')"
                                            >
                                                Crear nuevo equipo
                                            </DropdownLink>

                                            <template
                                                v-if="
                                                    $page.props.auth.user
                                                        .all_teams.length > 1
                                                "
                                            >
                                                <div
                                                    class="border-t border-gray-200"
                                                />
                                                <div
                                                    class="block px-4 py-2 text-xs text-gray-400"
                                                >
                                                    Cambiar de equipo
                                                </div>
                                                <template
                                                    v-for="team in $page.props
                                                        .auth.user.all_teams"
                                                    :key="team.id"
                                                >
                                                    <form
                                                        @submit.prevent="
                                                            switchToTeam(team)
                                                        "
                                                    >
                                                        <DropdownLink
                                                            as="button"
                                                        >
                                                            <div
                                                                class="flex items-center"
                                                            >
                                                                <svg
                                                                    v-if="
                                                                        team.id ==
                                                                        $page
                                                                            .props
                                                                            .auth
                                                                            .user
                                                                            .current_team_id
                                                                    "
                                                                    class="mr-2 h-5 w-5 text-green-400"
                                                                    xmlns="http://www.w3.org/2000/svg"
                                                                    viewBox="0 0 20 20"
                                                                    fill="currentColor"
                                                                >
                                                                    <path
                                                                        fill-rule="evenodd"
                                                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                                        clip-rule="evenodd"
                                                                    />
                                                                </svg>
                                                                <div>
                                                                    {{
                                                                        team.name
                                                                    }}
                                                                </div>
                                                            </div>
                                                        </DropdownLink>
                                                    </form>
                                                </template>
                                            </template>
                                        </div>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>

                        <!-- Mensaje y menú en la esquina derecha absoluta -->
                        <div
                            class="absolute right-0 top-0 h-full flex items-center pr-2 space-x-4"
                        >
                            <!-- Mensaje de bienvenida -->
                            <span
                                class="hidden md:inline text-white text-sm font-medium"
                            >
                                Hola Bienvenido
                            </span>

                            <!-- Menú de usuario -->
                            <div class="relative">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <button
                                            v-if="
                                                $page.props.jetstream
                                                    .managesProfilePhotos
                                            "
                                            class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition"
                                        >
                                            <img
                                                class="h-8 w-8 rounded-full object-cover"
                                                :src="
                                                    $page.props.auth.user
                                                        .profile_photo_url
                                                "
                                                :alt="
                                                    $page.props.auth.user.name
                                                "
                                            />
                                        </button>
                                        <span
                                            v-else
                                            class="inline-flex rounded-md"
                                        >
                                            <button
                                                type="button"
                                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition"
                                            >
                                                {{ $page.props.auth.user.name }}
                                                <svg
                                                    class="ml-2 -mr-0.5 h-4 w-4"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <div
                                            class="block px-4 py-2 text-xs text-gray-400"
                                        >
                                            Administrar cuenta
                                        </div>
                                        <DropdownLink
                                            :href="route('profile.show')"
                                        >
                                            <div
                                                class="flex items-center gap-x-2"
                                            >
                                                <svg
                                                    class="h-5 w-5 text-gray-500"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                                <span class="font-medium"
                                                    >Perfil</span
                                                >
                                            </div>
                                        </DropdownLink>
                                        <DropdownLink
                                            v-if="
                                                $page.props.jetstream
                                                    .hasApiFeatures
                                            "
                                            :href="route('api-tokens.index')"
                                        >
                                            API Tokens
                                        </DropdownLink>
                                        <div class="border-t border-gray-200" />
                                        <form @submit.prevent="logout">
                                            <DropdownLink as="button">
                                                <div
                                                    class="flex items-center gap-x-2"
                                                >
                                                    <svg
                                                        class="h-5 w-5 text-gray-500"
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        viewBox="0 0 20 20"
                                                        fill="currentColor"
                                                    >
                                                        <path
                                                            fill-rule="evenodd"
                                                            d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z"
                                                            clip-rule="evenodd"
                                                        />
                                                    </svg>
                                                    <span class="font-medium"
                                                        >Cerrar Sesión</span
                                                    >
                                                </div>
                                            </DropdownLink>
                                        </form>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>
                    </div>
                </nav>

                <!-- Header secundario fijo -->
                <header
                    v-if="$slots.header"
                    class="bg-gradient-to-r from-blue-500 to-blue-600 shadow-sm fixed top-16 z-10"
                    :class="{
                        'left-0 right-0': !isSidebarOpen,
                        'left-64 right-0': isSidebarOpen,
                    }"
                >
                    <div
                        class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8 container-margen"
                    >
                        <h2
                            class="text-xl font-bold text-white ml-2 lg:ml-[45px]"
                        >
                            <slot name="header" />
                        </h2>
                    </div>
                </header>

                <!-- Contenido principal con margen dinámico -->
                <main
                    class="flex-1 pb-4 overflow-y-auto"
                    :class="{
                        'pt-32': !isSidebarOpen,
                        'pt-32 md:pl-64': isSidebarOpen,
                    }"
                >
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                        <slot />
                    </div>
                </main>
            </div>
        </div>
    </div>
</template>

<style>
.container-margen {
    max-width: 1140px;
}

.container-margen h2 {
    color: #fff;
}
</style>
