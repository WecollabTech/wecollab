<template>
    <AppLayout title="Gestión de Usuarios">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Gestión de Usuarios
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <!-- Header -->
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-lg font-medium text-gray-900">
                                Lista de Usuarios
                            </h3>
                            <Link
                                v-if="puedeCrear"
                                :href="route('usuarios.create')"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                            >
                                + Nuevo Usuario
                            </Link>
                        </div>

                        <!-- Filtros -->
                        <div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                            <input
                                v-model="filters.search"
                                type="text"
                                placeholder="Buscar..."
                                class="rounded-md border-gray-300 shadow-sm"
                                @input="debouncedLoad"
                            />

                            <select
                                v-model="filters.status"
                                class="rounded-md border-gray-300 shadow-sm"
                                @change="loadUsuarios"
                            >
                                <option value="">Todos los estados</option>
                                <option value="activo">Activo</option>
                                <option value="inactivo">Inactivo</option>
                            </select>

                            <select
                                v-model="filters.role_id"
                                class="rounded-md border-gray-300 shadow-sm"
                                @change="loadUsuarios"
                            >
                                <option value="">Todos los roles</option>
                                <option
                                    v-for="role in roles"
                                    :key="role.id"
                                    :value="role.id"
                                >
                                    {{ role.nombre }}
                                </option>
                            </select>

                            <button
                                @click="clearFilters"
                                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded"
                            >
                                Limpiar
                            </button>
                        </div>

                        <!-- Loading -->
                        <div v-if="loading" class="text-center py-4">
                            <div
                                class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"
                            ></div>
                            <p class="mt-2 text-gray-600">
                                Cargando usuarios...
                            </p>
                        </div>

                        <!-- Tabla -->
                        <div v-else class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                                        >
                                            ID
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                                        >
                                            Nombre
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                                        >
                                            Email
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                                        >
                                            Rol
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                                        >
                                            Estado
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                                        >
                                            Acciones
                                        </th>
                                    </tr>
                                </thead>
                                <tbody
                                    class="bg-white divide-y divide-gray-200"
                                >
                                    <tr
                                        v-for="user in usuariosData"
                                        :key="user.id"
                                    >
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm"
                                        >
                                            {{ user.id }}
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm"
                                        >
                                            {{ user.name }} {{ user.apellido }}
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm"
                                        >
                                            {{ user.email }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="px-2 inline-flex text-xs font-semibold rounded-full bg-blue-100 text-blue-800"
                                            >
                                                {{ user.role?.nombre }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                :class="
                                                    user.status === 'activo'
                                                        ? 'text-green-600'
                                                        : 'text-red-600'
                                                "
                                            >
                                                {{ user.status }}
                                            </span>
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm space-x-2"
                                        >
                                            <Link
                                                :href="
                                                    route(
                                                        'usuarios.show',
                                                        user.id,
                                                    )
                                                "
                                                class="text-blue-600 hover:text-blue-900"
                                                >Ver</Link
                                            >
                                            <Link
                                                v-if="puedeEditar"
                                                :href="
                                                    route(
                                                        'usuarios.edit',
                                                        user.id,
                                                    )
                                                "
                                                class="text-yellow-600 hover:text-yellow-900"
                                                >Editar</Link
                                            >
                                            <button
                                                v-if="puedeCambiarEstado"
                                                @click="toggleStatus(user)"
                                                class="text-green-600 hover:text-green-900"
                                            >
                                                {{
                                                    user.status === "activo"
                                                        ? "Desactivar"
                                                        : "Activar"
                                                }}
                                            </button>
                                            <!-- 🔐 Botón Eliminar - SOLO Superadmin -->
                                            <button
                                                v-if="puedeEliminar"
                                                @click="deleteUser(user)"
                                                class="text-red-600 hover:text-red-900 font-medium"
                                            >
                                                Eliminar
                                            </button>
                                        </td>
                                    </tr>
                                    <tr v-if="usuariosData.length === 0">
                                        <td
                                            colspan="6"
                                            class="px-6 py-4 text-center text-gray-500"
                                        >
                                            No hay usuarios registrados
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginación -->
                        <div
                            v-if="usuariosLinks && usuariosLinks.length > 0"
                            class="mt-4"
                        >
                            <Pagination
                                :links="usuariosLinks"
                                @page-changed="handlePageChange"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { Link, usePage } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import Pagination from "@/Components/Pagination.vue";
import { usuarioService } from "@/services/usuarioService";
import Swal from "sweetalert2";
import { debounce } from "lodash";

// 🔐 Obtener usuario actual y su rol
const page = usePage();
const currentUser = computed(() => page.props.auth?.user);

// 🔐 Obtener rol del usuario actual
const getUserRole = () => {
    const user = currentUser.value;
    if (!user) return "Invitado";

    const role = user.role;
    if (!role) return "Sin rol";

    if (typeof role === "object") {
        return role.nombre?.toString() || role.name?.toString() || "";
    }
    return role.toString();
};

// 🔐 Definir roles
const ROL_SUPERADMIN = "Superadmin we collab";
const ROL_ADMIN = "Admin we collab";

// 🔐 Permisos basados en rol
const esSuperadmin = computed(() => getUserRole() === ROL_SUPERADMIN);
const esAdmin = computed(() => getUserRole() === ROL_ADMIN);

// 🔐 Permisos específicos
const puedeEliminar = computed(() => esSuperadmin.value); // Solo Superadmin puede eliminar
const puedeEditar = computed(() => esSuperadmin.value || esAdmin.value); // Admin y Superadmin pueden editar
const puedeCambiarEstado = computed(() => esSuperadmin.value || esAdmin.value); // Admin y Superadmin pueden cambiar estado
const puedeCrear = computed(() => esSuperadmin.value || esAdmin.value); // Admin y Superadmin pueden crear

const usuariosRaw = ref({
    data: [],
    current_page: 1,
    last_page: 1,
    links: [],
});
const roles = ref([]);
const loading = ref(false);
const filters = ref({
    search: "",
    status: "",
    role_id: "",
    page: 1,
});

// Computed properties para acceder fácilmente
const usuariosData = computed(() => usuariosRaw.value.data || []);
const usuariosLinks = computed(() => usuariosRaw.value.links || []);

const loadUsuarios = async () => {
    loading.value = true;
    try {
        const response = await usuarioService.getUsuarios(filters.value);
        usuariosRaw.value = response;
    } catch (error) {
        console.error("Error detallado:", error);
        Swal.fire(
            "Error",
            error.response?.data?.message ||
                "No se pudieron cargar los usuarios",
            "error",
        );
    } finally {
        loading.value = false;
    }
};

const handlePageChange = (page) => {
    filters.value.page = page;
    loadUsuarios();
};

const loadRoles = async () => {
    try {
        const response = await usuarioService.getRoles();
        roles.value = response.data || [];
    } catch (error) {
        console.error("Error al cargar roles:", error);
    }
};

const toggleStatus = async (user) => {
    if (!puedeCambiarEstado.value) {
        Swal.fire(
            "Error",
            "No tienes permisos para cambiar el estado",
            "error",
        );
        return;
    }

    try {
        await usuarioService.toggleStatus(user.id);
        await loadUsuarios();
        Swal.fire("Éxito", "Estado actualizado correctamente", "success");
    } catch (error) {
        Swal.fire(
            "Error",
            error.response?.data?.message || "No se pudo cambiar el estado",
            "error",
        );
    }
};

const deleteUser = async (user) => {
    // Verificar permisos nuevamente por seguridad
    if (!puedeEliminar.value) {
        Swal.fire(
            "Error",
            "No tienes permisos para eliminar usuarios",
            "error",
        );
        return;
    }

    const result = await Swal.fire({
        title: "¿Estás seguro?",
        text: `¿Eliminar a ${user.name} ${user.apellido}?`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar",
    });

    if (result.isConfirmed) {
        try {
            await usuarioService.deleteUsuario(user.id);
            await loadUsuarios();
            Swal.fire(
                "Eliminado",
                "Usuario eliminado correctamente",
                "success",
            );
        } catch (error) {
            Swal.fire(
                "Error",
                error.response?.data?.message ||
                    "No se pudo eliminar el usuario",
                "error",
            );
        }
    }
};

const clearFilters = () => {
    filters.value = {
        search: "",
        status: "",
        role_id: "",
        page: 1,
    };
    loadUsuarios();
};

const debouncedLoad = debounce(loadUsuarios, 500);

// Verificar conexión a la API al montar el componente
onMounted(async () => {
    try {
        await loadUsuarios();
        await loadRoles();
    } catch (error) {
        console.error("Error al inicializar:", error);
    }
});
</script>
