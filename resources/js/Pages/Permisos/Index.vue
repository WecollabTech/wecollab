<template>
    <AppLayout title="Permisos">
        <template #header>
            <h2>Permisos</h2>
        </template>

        <div
            class="p-6 bg-white rounded-lg border border-gray-200 shadow-xl"
            style="margin-top: 60px"
        >
            <!-- Encabezado con título y botón para crear nuevo permiso -->
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-semibold">Listado de Permisos</h3>
                <button
                    @click="openModal"
                    class="bg-[#277cbb] text-white px-4 py-2 rounded-md hover:bg-[#63a2c4] transition duration-200 shadow-md"
                >
                    Crear Nuevo Permiso
                </button>
            </div>

            <!-- Tabla de permisos -->
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
                            v-for="permission in permissions"
                            :key="permission.id"
                            class="hover:bg-gray-50 transition duration-200"
                        >
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ permission.id }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ permission.nombre }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <span
                                    :class="
                                        permission.estado === 'activo'
                                            ? 'bg-green-100 text-green-800'
                                            : 'bg-red-100 text-red-800'
                                    "
                                    class="px-2 py-1 text-xs font-semibold rounded-full"
                                >
                                    {{
                                        permission.estado === "activo"
                                            ? "Activo"
                                            : "Inactivo"
                                    }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ permission.descripcion }}
                            </td>
                            <td
                                class="px-6 py-4 text-sm text-gray-900 text-center"
                            >
                                <div
                                    class="flex items-center justify-center space-x-2"
                                >
                                    <button
                                        @click="editPermission(permission)"
                                        class="bg-blue-500 text-white px-3 py-1 rounded-md hover:bg-blue-600 transition duration-200 relative"
                                        title="Editar"
                                    >
                                        <svg
                                            class="w-5 h-5"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                            xmlns="http://www.w3.org/2000/svg"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"
                                            ></path>
                                        </svg>
                                        <span
                                            class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-gray-700 text-white text-xs px-2 py-1 rounded opacity-0 hover:opacity-100 transition-opacity duration-200"
                                        >
                                            Editar
                                        </span>
                                    </button>
                                    <button
                                        @click="confirmDelete(permission)"
                                        class="bg-red-500 text-white px-3 py-1 rounded-md hover:bg-red-600 transition duration-200 relative"
                                        title="Eliminar"
                                    >
                                        <svg
                                            class="w-5 h-5"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                            xmlns="http://www.w3.org/2000/svg"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                            ></path>
                                        </svg>
                                        <span
                                            class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-gray-700 text-white text-xs px-2 py-1 rounded opacity-0 hover:opacity-100 transition-opacity duration-200"
                                        >
                                            Eliminar
                                        </span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <!-- Paginación -->
            <div class="flex justify-center items-center mt-4 space-x-4">
                <button
                    @click="changePage(pagination.prev_page_url)"
                    :disabled="!pagination.prev_page_url"
                    class="px-4 py-2 bg-[#277cbb] text-white rounded-md hover:bg-[#63a2c4] transition duration-200"
                >
                    Anterior
                </button>

                <span class="text-gray-700">
                    Página {{ pagination.current_page }} de
                    {{ pagination.last_page }}
                </span>

                <button
                    @click="changePage(pagination.next_page_url)"
                    :disabled="!pagination.next_page_url"
                    class="px-4 py-2 bg-[#277cbb] text-white rounded-md hover:bg-[#63a2c4] transition duration-200"
                >
                    Siguiente
                </button>
            </div>

            <!-- Modal para crear/editar permisos -->
            <div
                v-if="isModalOpen"
                class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center"
            >
                <div
                    class="bg-white rounded-lg border border-gray-200 shadow-2xl p-6 w-1/3"
                >
                    <div
                        v-if="errorMessage"
                        class="bg-red-100 text-red-700 p-4 mb-4 rounded-md"
                    >
                        <p>{{ errorMessage }}</p>
                    </div>

                    <h3 class="text-xl font-semibold mb-4">
                        {{
                            isEditing ? "Editar Permiso" : "Crear Nuevo Permiso"
                        }}
                    </h3>
                    <form
                        @submit.prevent="
                            isEditing ? updatePermission() : createPermission()
                        "
                    >
                        <div class="mb-4">
                            <label
                                class="block text-sm font-medium text-gray-700"
                                >Nombre del Permiso</label
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
                                <option value="inactivo">Inactivo</option>
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
                        <div class="flex justify-center">
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
            <div
                v-if="isSuccessModalOpen"
                class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center"
            >
                <div
                    class="bg-white rounded-lg border border-gray-200 shadow-2xl p-8 w-1/3 text-center"
                >
                    <div class="flex justify-center mb-6">
                        <svg
                            class="w-16 h-16 text-green-500"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M5 13l4 4L19 7"
                            ></path>
                        </svg>
                    </div>

                    <h3 class="text-2xl font-semibold mb-4">¡Éxito!</h3>
                    <p class="mb-6 text-gray-600">{{ successMessage }}</p>

                    <div class="flex justify-center">
                        <button
                            @click="closeSuccessModal"
                            class="bg-green-500 text-white px-6 py-2 rounded-md hover:bg-green-600 transition duration-200 shadow-md"
                        >
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>

            <!-- Modal de confirmación para eliminar -->
            <div
                v-if="isConfirmModalOpen"
                class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center"
            >
                <div
                    class="bg-white rounded-lg border border-gray-200 shadow-2xl p-8 w-1/3 text-center"
                >
                    <h3 class="text-2xl font-semibold mb-4">
                        ¿Eliminar Permiso?
                    </h3>
                    <p class="mb-6 text-gray-600">
                        ¿Estás seguro de que deseas eliminar este permiso? Esta
                        acción no se puede deshacer.
                    </p>

                    <div class="flex justify-center gap-4">
                        <button
                            @click="closeConfirmModal"
                            class="bg-gray-500 text-white px-6 py-2 rounded-md hover:bg-gray-600 transition duration-200 shadow-md"
                        >
                            Cancelar
                        </button>
                        <button
                            @click="deletePermission"
                            class="bg-red-500 text-white px-6 py-2 rounded-md hover:bg-red-600 transition duration-200 shadow-md"
                        >
                            Eliminar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, onMounted, watch } from "vue";
import axios from "axios";
import AppLayout from "@/Layouts/AppLayout.vue";

// URLs de la API
const API_URL = "/permisos";

// Estado del modal y formulario
const isModalOpen = ref(false);
const isEditing = ref(false);
const isSuccessModalOpen = ref(false);
const isConfirmModalOpen = ref(false);
const successMessage = ref("");
const errorMessage = ref("");
const permissionIdToDelete = ref(null);

// Datos del formulario
const form = ref({
    nombre: "",
    estado: "activo",
    descripcion: "",
});

// Permisos obtenidos desde el backend
const permissions = ref([]);
const pagination = ref({
    current_page: 1,
    last_page: 1,
    prev_page_url: null,
    next_page_url: null,
});

// Función para actualizar la paginación
const updatePagination = (data) => {
    pagination.value = {
        current_page: data.current_page,
        last_page: data.last_page,
        prev_page_url: data.prev_page_url,
        next_page_url: data.next_page_url,
    };
};

// Abrir modal para crear un nuevo permiso
const openModal = () => {
    form.value = { nombre: "", estado: "activo", descripcion: "" };
    isEditing.value = false;
    errorMessage.value = "";
    isModalOpen.value = true;
};

// Cerrar el modal
const closeModal = () => {
    isModalOpen.value = false;
};

// Abrir modal para editar un permiso
const editPermission = (permission) => {
    form.value = { ...permission };
    isEditing.value = true;
    errorMessage.value = "";
    isModalOpen.value = true;
};

const createPermission = async () => {
    try {
        const response = await axios.post(API_URL, form.value);
        permissions.value.push(response.data);
        successMessage.value = "Permiso creado exitosamente.";
        closeModal();
        isSuccessModalOpen.value = true;
    } catch (error) {
        handleError(error, "Error al crear el permiso.");
    }
};

const updatePermission = async () => {
    try {
        const response = await axios.put(
            `${API_URL}/${form.value.id}`,
            form.value
        );
        const index = permissions.value.findIndex(
            (permission) => permission.id === form.value.id
        );
        if (index !== -1) {
            permissions.value[index] = response.data;
        }
        successMessage.value = "Permiso actualizado exitosamente.";
        closeModal();
        isSuccessModalOpen.value = true;
    } catch (error) {
        handleError(error, "Error al actualizar el permiso.");
    }
};

// Confirmar la eliminación de un permiso
const confirmDelete = (permission) => {
    permissionIdToDelete.value = permission.id;
    isConfirmModalOpen.value = true;
};

// Eliminar un permiso
const deletePermission = async () => {
    try {
        await axios.delete(`${API_URL}/${permissionIdToDelete.value}`);
        permissions.value = permissions.value.filter(
            (permission) => permission.id !== permissionIdToDelete.value
        );
        isConfirmModalOpen.value = false;
        successMessage.value = "Permiso eliminado exitosamente.";
        isSuccessModalOpen.value = true;
    } catch (error) {
        handleError(error, "Error al eliminar el permiso.");
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

// Cambiar de página
const changePage = async (url) => {
    if (!url) return;

    try {
        const response = await axios.get(url);
        if (response.data) {
            permissions.value = response.data.data;
            updatePagination(response.data);
        } else {
            console.error(
                "La respuesta del servidor no tiene la estructura esperada:",
                response.data
            );
        }
    } catch (error) {
        handleError(error, "Error al cambiar de página.");
    }
};

// Cargar los permisos cuando se monta el componente
onMounted(async () => {
    try {
        const response = await axios.get(API_URL);
        if (response.data) {
            permissions.value = response.data.data;
            updatePagination(response.data);
        } else {
            console.error(
                "La respuesta del servidor no tiene la estructura esperada:",
                response.data
            );
        }
    } catch (error) {
        handleError(error, "Error al cargar los permisos.");
    }
});

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

// Watcher para ocultar el mensaje de error al cambiar el formulario
watch(
    () => form.value,
    () => {
        errorMessage.value = "";
    },
    { deep: true }
);
</script>
