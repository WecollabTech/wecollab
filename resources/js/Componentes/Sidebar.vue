<script setup>
import { ref, computed } from "vue";
import { usePage } from "@inertiajs/vue3"; // Importa usePage
import NavLink from "@/Components/NavLink.vue";
import { router } from "@inertiajs/vue3";

// Obtiene las propiedades de la página
const page = usePage();

const isSubmenuOpen = ref({
    tutorial: false,
    usuarios: false,
    configuracion: false,
    userMenu: false,
});

const toggleSubmenu = (menu) => {
    isSubmenuOpen.value[menu] = !isSubmenuOpen.value[menu];
};

const logout = () => {
    router.post(route("logout"));
};

// Función para verificar permisos
const hasPermission = (permission) => {
    if (!page.props.auth.user) return false;

    // Verifica si el usuario tiene el permiso directamente
    if (page.props.auth.user.permissions?.includes(permission)) {
        return true;
    }

    // O verifica si tiene un rol que tenga este permiso
    return page.props.auth.user.can?.[permission] || false;
};

// Función para determinar si mostrar el separador
const showSeparator = (section) => {
    if (section === "tutorial") {
        return (
            page.props.auth.user &&
            (hasPermission("ver_tutoriales") ||
                hasPermission("gestionar_usuarios") ||
                hasPermission("acceder_configuracion"))
        );
    }
    return false;
};

defineProps({
    isOpen: {
        type: Boolean,
        required: true,
    },
    isMobile: {
        type: Boolean,
        default: false,
    },
});
</script>

<style scoped>
/* Tus estilos existentes se mantienen igual */
.menu-item,
.submenu-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    transition: background-color 0.2s, color 0.2s;
}

.menu-item:hover,
.submenu-item:hover {
    background-color: #4caf50;
    color: white;
}

.menu-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.75rem;
    border-radius: 0.5rem;
    cursor: pointer;
    transition: background-color 0.2s;
}

.menu-header:hover {
    background-color: #4caf50;
}

.submenu {
    padding-left: 1.5rem;
    margin-top: 0.5rem;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.rotate-180 {
    transform: rotate(180deg);
}
</style>

<template>
    <aside
        :class="{
            'bg-white text-black': isMobile,
            'bg-[#140b5c] text-white': !isMobile,
        }"
        class="w-64 min-h-screen flex-shrink-0 shadow-lg fixed inset-y-0 left-0 overflow-y-auto transition-transform duration-300 transform"
    >
        <div class="p-6">
            <a
                :href="route('dashboard')"
                :active="route().current('dashboard')"
            >
                <div
                    class="logo-container flex items-center"
                    style="margin-top: -1px"
                >
                    <img
                        src="/img/logo.png"
                        alt="Atrápalo"
                        class="logo h-8 w-auto mr-2"
                    />
                    <div class="text-2xl font-semibold">SISTEMAGT</div>
                </div>
            </a>

            <hr class="border-write-500 opacity-50 my-2" />

            <ul class="mt-6 space-y-2">
                <!-- Dashboard - Visible para todos los usuarios autenticados -->
                <li v-if="$page.props.auth.user">
                    <NavLink
                        :href="route('dashboard')"
                        :active="route().current('dashboard')"
                        class="menu-item"
                        :class="{
                            'text-black': isMobile,
                            'text-white': !isMobile,
                        }"
                    >
                        <i class="fas fa-tachometer-alt text-lg"></i>
                        <span>Dashboard</span>
                    </NavLink>
                </li>

                <hr
                    v-if="showSeparator('tutorial')"
                    class="border-write-500 opacity-50 my-4"
                />

                <!-- Tutoriales - Controlado por permisos -->
                <li v-if="hasPermission('ver_tutoriales')">
                    <div
                        class="menu-header"
                        @click="toggleSubmenu('tutorial')"
                        :class="{
                            'text-black': isMobile,
                            'text-white': !isMobile,
                        }"
                    >
                        <div class="flex items-center gap-3">
                            <i class="fas fa-columns"></i>
                            <span>Tutorial</span>
                        </div>
                        <i
                            class="fas fa-chevron-down transition-transform duration-300"
                            :class="{ 'rotate-180': isSubmenuOpen.tutorial }"
                        ></i>
                    </div>
                    <ul v-if="isSubmenuOpen.tutorial" class="submenu">
                        <li v-if="hasPermission('gestionar_categorias')">
                            <NavLink
                                href="/categorias"
                                class="submenu-item"
                                :class="{
                                    'text-black': isMobile,
                                    'text-white': !isMobile,
                                }"
                            >
                                <i class="fas fa-layer-group"></i>
                                Categorias
                            </NavLink>
                        </li>
                        <li v-if="hasPermission('gestionar_subcategorias')">
                            <NavLink
                                href="/subcategorias"
                                class="submenu-item"
                                :class="{
                                    'text-black': isMobile,
                                    'text-white': !isMobile,
                                }"
                            >
                                <i class="fas fa-layer-group"></i>
                                Subcategorias
                            </NavLink>
                        </li>
                        <li v-if="hasPermission('gestionar_videos')">
                            <NavLink
                                href="/tutoriales"
                                class="submenu-item"
                                :class="{
                                    'text-black': isMobile,
                                    'text-white': !isMobile,
                                }"
                            >
                                <i class="fas fa-eye"></i>
                                Videos capacitacion
                            </NavLink>
                        </li>
                        <li v-if="hasPermission('gestionar_comentarios')">
                            <NavLink
                                href="#"
                                class="submenu-item"
                                :class="{
                                    'text-black': isMobile,
                                    'text-white': !isMobile,
                                }"
                            >
                                <i class="fas fa-bars"></i>
                                Comentarios
                            </NavLink>
                        </li>
                    </ul>
                </li>

                <!-- Usuarios - Solo para administradores -->
                <li v-if="hasPermission('gestionar_usuarios')">
                    <div
                        class="menu-header"
                        @click="toggleSubmenu('usuarios')"
                        :class="{
                            'text-black': isMobile,
                            'text-white': !isMobile,
                        }"
                    >
                        <div class="flex items-center gap-3">
                            <i class="fas fa-users"></i>
                            <span>Usuarios</span>
                        </div>
                        <i
                            class="fas fa-chevron-down transition-transform duration-300"
                            :class="{ 'rotate-180': isSubmenuOpen.usuarios }"
                        ></i>
                    </div>
                    <ul v-if="isSubmenuOpen.usuarios" class="submenu">
                        <li v-if="hasPermission('crear_usuarios')">
                            <NavLink
                                href="/usuarios/create"
                                class="submenu-item"
                                :class="{
                                    'text-black': isMobile,
                                    'text-white': !isMobile,
                                }"
                            >
                                <i class="fas fa-user-plus"></i>
                                Crear Usuario
                            </NavLink>
                        </li>
                        <li v-if="hasPermission('editar_usuarios')">
                            <NavLink
                                href="/usuarios"
                                class="submenu-item"
                                :class="{
                                    'text-black': isMobile,
                                    'text-white': !isMobile,
                                }"
                            >
                                <i class="fas fa-user-edit"></i>
                                Editar Usuario
                            </NavLink>
                        </li>
                        <li v-if="hasPermission('eliminar_usuarios')">
                            <NavLink
                                href="/usuarios"
                                class="submenu-item"
                                :class="{
                                    'text-black': isMobile,
                                    'text-white': !isMobile,
                                }"
                            >
                                <i class="fas fa-user-times"></i>
                                Eliminar Usuario
                            </NavLink>
                        </li>
                    </ul>
                </li>

                <!-- Configuracion - Solo para superadmin -->
                <li v-if="hasPermission('acceder_configuracion')">
                    <div
                        class="menu-header"
                        @click="toggleSubmenu('configuracion')"
                        :class="{
                            'text-black': isMobile,
                            'text-white': !isMobile,
                        }"
                    >
                        <div class="flex items-center gap-3">
                            <i class="fab fa-bootstrap"></i>
                            <span>Configuracion</span>
                        </div>
                        <i
                            class="fas fa-chevron-down transition-transform duration-300"
                            :class="{
                                'rotate-180': isSubmenuOpen.configuracion,
                            }"
                        ></i>
                    </div>
                    <ul v-if="isSubmenuOpen.configuracion" class="submenu">
                        <li v-if="hasPermission('gestionar_permisos')">
                            <NavLink
                                href="/permisos"
                                class="submenu-item"
                                :class="{
                                    'text-black': isMobile,
                                    'text-white': !isMobile,
                                }"
                            >
                                <i class="fas fa-exclamation-circle"></i>
                                Permisos
                            </NavLink>
                        </li>
                        <li v-if="hasPermission('gestionar_roles')">
                            <NavLink
                                href="/roles"
                                class="submenu-item"
                                :class="{
                                    'text-black': isMobile,
                                    'text-white': !isMobile,
                                }"
                            >
                                <i class="fas fa-user-shield"></i>
                                Roles
                            </NavLink>
                        </li>
                        <li v-if="hasPermission('ver_reportes')">
                            <NavLink
                                href="/reportes"
                                class="submenu-item"
                                :class="{
                                    'text-black': isMobile,
                                    'text-white': !isMobile,
                                }"
                            >
                                <i class="fas fa-map-signs"></i>
                                Reportes
                            </NavLink>
                        </li>
                    </ul>
                </li>

                <!-- User Menu - Visible para todos los usuarios autenticados -->
                <li v-if="$page.props.auth.user">
                    <div
                        class="menu-header"
                        @click="toggleSubmenu('userMenu')"
                        :class="{
                            'text-black': isMobile,
                            'text-white': !isMobile,
                        }"
                    >
                        <div class="flex items-center gap-3">
                            <i class="fas fa-user-circle text-lg"></i>
                            <span>Mi Cuenta</span>
                        </div>
                        <i
                            class="fas fa-chevron-down transition-transform duration-300"
                            :class="{ 'rotate-180': isSubmenuOpen.userMenu }"
                        ></i>
                    </div>
                    <ul v-if="isSubmenuOpen.userMenu" class="submenu">
                        <li>
                            <NavLink
                                :href="route('profile.show')"
                                class="submenu-item"
                                :class="{
                                    'text-black': isMobile,
                                    'text-white': !isMobile,
                                }"
                            >
                                <i class="fas fa-user text-lg"></i>
                                <span>Perfil</span>
                            </NavLink>
                        </li>
                        <li
                            v-if="
                                $page.props.jetstream.hasApiFeatures &&
                                hasPermission('gestionar_api_tokens')
                            "
                        >
                            <NavLink
                                :href="route('api-tokens.index')"
                                class="submenu-item"
                                :class="{
                                    'text-black': isMobile,
                                    'text-white': !isMobile,
                                }"
                            >
                                <i class="fas fa-key text-lg"></i>
                                <span>API Tokens</span>
                            </NavLink>
                        </li>
                        <li>
                            <form @submit.prevent="logout">
                                <NavLink
                                    as="button"
                                    class="submenu-item"
                                    :class="{
                                        'text-black': isMobile,
                                        'text-white': !isMobile,
                                    }"
                                >
                                    <i
                                        class="fas fa-right-from-bracket text-lg"
                                    ></i>
                                    <span>Cerrar Sesión</span>
                                </NavLink>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </aside>
</template>
