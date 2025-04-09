<template>
    <AppLayout title="Roles">
        <template #header>
            <h2>Roles</h2>
        </template>

        <div
            class="p-6 bg-white rounded-lg border border-gray-200 shadow-xl"
            style="margin-top: 60px"
        >
            <!-- Encabezado con título y botón para crear nuevo rol -->
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-semibold">Listado de Roles</h3>
                <button
                    @click="openModal"
                    class="bg-[#277cbb] text-white px-4 py-2 rounded-md hover:bg-[#63a2c4] transition duration-200 shadow-md"
                >
                    Crear Nuevo Rol
                </button>
            </div>

            <!-- Filtros de búsqueda -->
            <SearchFilters
                :initialSearchName="searchName"
                :initialSearchStatus="searchStatus"
                @update:searchName="handleSearchNameUpdate"
                @update:searchStatus="handleSearchStatusUpdate"
            />

            <!-- Tabla de roles -->
            <div class="overflow-x-auto">
                <table
                    class="min-w-full bg-white border border-gray-200 shadow-sm rounded-lg overflow-hidden"
                >
                    <thead class="bg-gray-50">
                        <tr>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                            >
                                ID
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                            >
                                Nombre
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                            >
                                Estado
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                            >
                                Descripción
                            </th>
                            <th
                                class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider text-center"
                            >
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr
                            v-for="role in roles"
                            :key="role.id"
                            class="hover:bg-gray-50 transition duration-200"
                        >
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ role.id }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ role.nombre }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <span
                                    :class="
                                        role.estado === 'activo'
                                            ? 'bg-green-100 text-green-800'
                                            : 'bg-red-100 text-red-800'
                                    "
                                    class="px-2 py-1 text-xs font-semibold rounded-full"
                                >
                                    {{
                                        role.estado === "activo"
                                            ? "Activo"
                                            : "Inactivo"
                                    }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ role.descripcion }}
                            </td>
                            <td
                                class="px-6 py-4 text-sm text-gray-900 text-center"
                            >
                                <div
                                    class="flex items-center justify-center space-x-2"
                                >
                                    <ActionButton
                                        @click="editRole(role)"
                                        color="blue"
                                        title="Editar"
                                        iconPath="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"
                                    />
                                    <ActionButton
                                        @click="confirmDelete(role)"
                                        color="red"
                                        title="Eliminar"
                                        iconPath="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                    />
                                    <ActionButton
                                        @click="assignPermissions(role)"
                                        color="green"
                                        title="Asignar Permiso"
                                        iconPath="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"
                                    />
                                    <ActionButton
                                        @click="viewRoleDetails(role.id)"
                                        color="yellow"
                                        title="Ver Detalles"
                                        iconPath="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7z"
                                    />
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Modal de detalles del rol -->
            <div
                v-if="isDetailsModalOpen"
                class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50"
            >
                <div
                    class="bg-white rounded-lg shadow-lg w-96 p-6 relative animate-fade-in"
                >
                    <button
                        @click="closeDetailsModal"
                        class="absolute top-3 right-3 text-gray-500 hover:text-gray-700"
                    >
                        ✖
                    </button>
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">
                        Detalles del Rol
                    </h2>
                    <RoleDetails
                        :roleId="selectedRoleId"
                        @close="closeDetailsModal"
                    />
                </div>
            </div>

            <!-- Paginación -->
            <Pagination
                :currentPage="pagination.current_page"
                :lastPage="pagination.last_page"
                :prevPageUrl="pagination.prev_page_url"
                :nextPageUrl="pagination.next_page_url"
                @prev="changePage(pagination.prev_page_url)"
                @next="changePage(pagination.next_page_url)"
            />

            <!-- Modal para crear/editar roles -->
            <div
                v-if="isModalOpen"
                class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center"
            >
                <div
                    class="bg-white rounded-lg border border-gray-200 shadow-2xl p-6 w-1/2 max-h-[90vh] overflow-y-auto"
                >
                    <div
                        v-if="errorMessage"
                        class="bg-red-100 text-red-700 p-4 mb-4 rounded-md"
                    >
                        <p>{{ errorMessage }}</p>
                    </div>

                    <h3 class="text-xl font-semibold mb-4">
                        {{ isEditing ? "Editar Rol" : "Crear Nuevo Rol" }}
                    </h3>

                    <!-- Pestañas -->
                    <div class="mb-4">
                        <div class="flex space-x-4 border-b border-gray-200">
                            <button
                                @click="activeTab = 0"
                                :class="{
                                    'border-blue-500 text-blue-500':
                                        activeTab === 0,
                                    'border-transparent text-gray-500 hover:text-gray-700':
                                        activeTab !== 0,
                                }"
                                class="py-2 px-4 border-b-2 font-medium text-sm focus:outline-none"
                            >
                                Datos del Rol
                            </button>
                            <button
                                @click="activeTab = 1"
                                :class="{
                                    'border-blue-500 text-blue-500':
                                        activeTab === 1,
                                    'border-transparent text-gray-500 hover:text-gray-700':
                                        activeTab !== 1,
                                }"
                                class="py-2 px-4 border-b-2 font-medium text-sm focus:outline-none"
                            >
                                Permisos
                            </button>
                        </div>
                    </div>

                    <!-- Contenido de las pestañas -->
                    <form
                        @submit.prevent="
                            isEditing ? updateRole() : createRole()
                        "
                    >
                        <!-- Pestaña 1: Datos del Rol -->
                        <div
                            v-if="activeTab === 0"
                            class="min-h-[300px] flex flex-col justify-between"
                        >
                            <div>
                                <div class="mb-4">
                                    <label
                                        class="block text-sm font-medium text-gray-700"
                                        >Nombre del Rol</label
                                    >
                                    <input
                                        v-model="form.nombre"
                                        type="text"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        required
                                    />
                                </div>
                                <div class="mb-4">
                                    <label
                                        class="block text-sm font-medium text-gray-700"
                                        >Estado</label
                                    >
                                    <select
                                        v-model="form.estado"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    >
                                        <option value="activo">Activo</option>
                                        <option value="inactivo">
                                            Inactivo
                                        </option>
                                    </select>
                                </div>
                                <div class="mb-4">
                                    <label
                                        class="block text-sm font-medium text-gray-700"
                                        >Descripción</label
                                    >
                                    <textarea
                                        v-model="form.descripcion"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    ></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Pestaña 2: Permisos -->
                        <div
                            v-if="activeTab === 1"
                            class="min-h-[300px] flex flex-col justify-between"
                        >
                            <div>
                                <div class="mb-4">
                                    <label
                                        class="block text-sm font-medium text-gray-700"
                                        >Permisos</label
                                    >
                                    <div
                                        v-if="!allPermisosCargados"
                                        class="mt-2"
                                    >
                                        <p class="text-gray-500">
                                            Cargando permisos...
                                        </p>
                                    </div>
                                    <div
                                        v-else
                                        :class="[
                                            'mt-2 space-y-2',
                                            allPermisos.length > 10
                                                ? 'grid grid-cols-5 gap-4'
                                                : '',
                                        ]"
                                    >
                                        <div
                                            v-for="permiso in allPermisos"
                                            :key="permiso.id"
                                            class="flex items-center"
                                        >
                                            <input
                                                type="checkbox"
                                                :value="permiso.id"
                                                v-model="form.permisos"
                                                class="h-4 w-4 text-blue-500 border-gray-300 rounded focus:ring-blue-500"
                                            />
                                            <label
                                                class="ml-2 text-sm text-gray-700"
                                                >{{ permiso.nombre }}</label
                                            >
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-center mt-6">
                            <button
                                type="button"
                                @click="closeModal"
                                class="mr-2 bg-[#cc1928] text-white px-4 py-2 rounded-md hover:bg-[#dd0431] transition duration-200 shadow-md"
                            >
                                Cancelar
                            </button>
                            <button
                                type="submit"
                                class="bg-[#34b445] text-white px-4 py-2 rounded-md hover:bg-[#4fab5c] transition duration-200 shadow-md"
                            >
                                {{ isEditing ? "Actualizar" : "Crear" }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Modal de éxito -->
            <SuccessModal
                v-if="isSuccessModalOpen"
                :message="successMessage"
                @close="closeSuccessModal"
            />

            <!-- Modal de confirmación para eliminar -->
            <ConfirmationModal
                v-if="isConfirmModalOpen"
                title="¿Eliminar Rol?"
                message="¿Estás seguro de que deseas eliminar este rol? Esta acción no se puede deshacer."
                @confirm="deleteRole"
                @cancel="closeConfirmModal"
            />
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, onMounted, watch } from "vue";
import axios from "axios";
import AppLayout from "@/Layouts/AppLayout.vue";
import RoleDetails from "./RoleDetails.vue";
import SearchFilters from "@/Components/SearchFilters.vue";
import ActionButton from "./Components/ActionButton.vue";
import ConfirmationModal from "./Components/ConfirmationModal.vue";
import SuccessModal from "./Components/SuccessModal.vue";
import Pagination from "./Components/Pagination.vue";

// URLs de la API
const API_URL = "/roles";
const ALL_PERMISOS_URL = "/permisos/all";

// Estado del modal y formulario
const isModalOpen = ref(false);
const isEditing = ref(false);
const isSuccessModalOpen = ref(false);
const isConfirmModalOpen = ref(false);
const successMessage = ref("");
const errorMessage = ref("");
const roleIdToDelete = ref(null);
const activeTab = ref(0);
const allPermisosCargados = ref(false);

// Estado para controlar el modal de detalles
const isDetailsModalOpen = ref(false);
const selectedRoleId = ref(null);

// Función para abrir el modal de detalles
const viewRoleDetails = (roleId) => {
    selectedRoleId.value = roleId;
    isDetailsModalOpen.value = true;
};

// Función para cerrar el modal de detalles
const closeDetailsModal = () => {
    isDetailsModalOpen.value = false;
    selectedRoleId.value = null;
};

// Datos del formulario
const form = ref({
    nombre: "",
    estado: "activo",
    descripcion: "",
    permisos: [],
});

// Roles y permisos obtenidos desde el backend
const roles = ref([]);
const allPermisos = ref([]);
const pagination = ref({
    current_page: 1,
    last_page: 1,
    prev_page_url: null,
    next_page_url: null,
});

// Filtros de búsqueda
const searchName = ref("");
const searchStatus = ref("");

// Función para manejar la actualización del nombre de búsqueda
const handleSearchNameUpdate = (newValue) => {
    searchName.value = newValue;
};

// Función para manejar la actualización del estado de búsqueda
const handleSearchStatusUpdate = (newValue) => {
    searchStatus.value = newValue;
};

// Cargar roles con sus permisos relacionados
const loadRoles = async (searchParams = {}) => {
    try {
        const response = await axios.get(API_URL, { params: searchParams });
        console.log("Roles recibidos:", response.data.data);
        roles.value = response.data.data;
        updatePagination(response.data);
    } catch (error) {
        console.error("Error al cargar los roles:", error);
    }
};

// Watcher para realizar la búsqueda cuando cambian los filtros
watch([searchName, searchStatus], ([newSearchName, newSearchStatus]) => {
    const searchParams = {};
    if (newSearchName) searchParams.nombre = newSearchName;
    if (newSearchStatus) searchParams.estado = newSearchStatus;
    loadRoles(searchParams);
});

// Cargar todos los permisos para el modal
const loadAllPermisos = async () => {
    try {
        const response = await axios.get(ALL_PERMISOS_URL);
        console.log("Todos los permisos recibidos:", response.data);
        allPermisos.value = response.data;
        allPermisosCargados.value = true;
    } catch (error) {
        console.error("Error al cargar todos los permisos:", error);
        errorMessage.value = "No se pudieron cargar los permisos.";
    }
};

// Función para actualizar la paginación
const updatePagination = (data) => {
    pagination.value = {
        current_page: data.current_page,
        last_page: data.last_page,
        prev_page_url: data.prev_page_url,
        next_page_url: data.next_page_url,
    };
};

// Cambiar de página
const changePage = async (url) => {
    if (!url) return;
    try {
        const response = await axios.get(url);
        roles.value = response.data.data;
        updatePagination(response.data);
    } catch (error) {
        console.error("Error al cambiar de página:", error);
    }
};

// Abrir modal para crear un nuevo rol
const openModal = () => {
    if (!allPermisosCargados.value) {
        errorMessage.value = "Esperando a que los permisos se carguen...";
        return;
    }
    form.value = {
        nombre: "",
        estado: "activo",
        descripcion: "",
        permisos: [],
    };
    isEditing.value = false;
    errorMessage.value = "";
    isModalOpen.value = true;
    activeTab.value = 0;
    console.log("Todos los permisos disponibles:", allPermisos.value);
};

// Cerrar el modal
const closeModal = () => {
    isModalOpen.value = false;
};

// Abrir modal para editar un rol
const editRole = (role) => {
    if (!allPermisosCargados.value) {
        errorMessage.value = "Esperando a que los permisos se carguen...";
        return;
    }
    form.value = {
        ...role,
        permisos: role.permisos ? role.permisos.map((p) => p.id) : [],
    };
    isEditing.value = true;
    errorMessage.value = "";
    isModalOpen.value = true;
    activeTab.value = 0;
    console.log("Permisos del rol:", form.value.permisos);
};

// Crear un nuevo rol
const createRole = async () => {
    try {
        const response = await axios.post(API_URL, form.value);
        roles.value.push(response.data);
        successMessage.value = "Rol creado exitosamente.";
        closeModal();
        isSuccessModalOpen.value = true;
    } catch (error) {
        handleError(error, "Error al crear el rol.");
    }
};

// Actualizar un rol
const updateRole = async () => {
    try {
        const response = await axios.put(
            `${API_URL}/${form.value.id}`,
            form.value
        );
        const index = roles.value.findIndex(
            (role) => role.id === form.value.id
        );
        if (index !== -1) roles.value[index] = response.data;
        successMessage.value = "Rol actualizado exitosamente.";
        closeModal();
        isSuccessModalOpen.value = true;
    } catch (error) {
        handleError(error, "Error al actualizar el rol.");
    }
};

// Confirmar la eliminación de un rol
const confirmDelete = (role) => {
    roleIdToDelete.value = role.id;
    isConfirmModalOpen.value = true;
};

// Eliminar un rol
const deleteRole = async () => {
    try {
        await axios.delete(`${API_URL}/${roleIdToDelete.value}`);
        roles.value = roles.value.filter(
            (role) => role.id !== roleIdToDelete.value
        );
        isConfirmModalOpen.value = false;
        successMessage.value = "Rol eliminado exitosamente.";
        isSuccessModalOpen.value = true;
    } catch (error) {
        handleError(error, "Error al eliminar el rol.");
    }
};

// Cerrar el modal de confirmación
const closeConfirmModal = () => {
    isConfirmModalOpen.value = false;
};

// Cerrar el modal de éxito
const closeSuccessModal = () => {
    isSuccessModalOpen.value = false;
};

// Función para manejar errores
const handleError = (error, defaultMessage) => {
    if (error.response && error.response.data && error.response.data.errors) {
        const errorMessages = Object.values(error.response.data.errors);
        errorMessage.value =
            errorMessages.length > 0 ? errorMessages[0][0] : defaultMessage;
    } else {
        errorMessage.value = defaultMessage;
    }
};

// Función del botón de Asignar permiso
const assignPermissions = (role) => {
    if (!allPermisosCargados.value) {
        errorMessage.value = "Esperando a que los permisos se carguen...";
        return;
    }
    form.value = {
        ...role,
        permisos: role.permisos ? role.permisos.map((p) => p.id) : [],
    };
    isEditing.value = true;
    errorMessage.value = "";
    isModalOpen.value = true;
    activeTab.value = 1;
};

// Cargar los roles y todos los permisos cuando se monta el componente
onMounted(async () => {
    await loadAllPermisos();
    await loadRoles();
});

// Watcher para ocultar el mensaje de error al cambiar el formulario
watch(
    () => form.value,
    () => {
        errorMessage.value = "";
    },
    { deep: true }
);
</script>

<style>
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
.animate-fade-in {
    animation: fadeIn 0.3s ease-out;
}
</style>
