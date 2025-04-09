<template>
    <AppLayout title="Gestión de Subcategorías">
        <template #header>
            <h2>Gestión de Subcategorías</h2>
        </template>

        <div
            class="p-6 bg-white rounded-lg border border-gray-200 shadow-xl"
            style="margin-top: 60px"
        >
            <div>
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-semibold">
                        Listado de Subcategorías
                    </h3>
                    <button
                        @click="openModal"
                        class="bg-[#277cbb] text-white px-4 py-2 rounded-md hover:bg-[#63a2c4] transition duration-200 shadow-md"
                    >
                        Crear Nueva Subcategoría
                    </button>
                </div>
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
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    Categoría
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
                                v-for="subcategoria in subcategorias"
                                :key="subcategoria.id"
                                class="hover:bg-gray-50 transition duration-200"
                            >
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ subcategoria.id }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ subcategoria.nombre }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    <span
                                        :class="
                                            subcategoria.estado === 'activo'
                                                ? 'bg-green-100 text-green-800'
                                                : 'bg-red-100 text-red-800'
                                        "
                                        class="px-2 py-1 text-xs font-semibold rounded-full"
                                    >
                                        {{
                                            subcategoria.estado === "activo"
                                                ? "Activo"
                                                : "Inactivo"
                                        }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{
                                        subcategoria.descripcion ||
                                        "Sin descripción"
                                    }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{
                                        subcategoria.categoria
                                            ? subcategoria.categoria.nombre
                                            : "Sin categoría"
                                    }}
                                </td>
                                <td
                                    class="px-6 py-4 text-sm text-gray-900 text-center"
                                >
                                    <div
                                        class="flex items-center justify-center space-x-2"
                                    >
                                        <button
                                            @click="editItem(subcategoria)"
                                            class="bg-blue-500 text-white px-3 py-1 rounded-md hover:bg-blue-600 transition duration-200"
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
                                        </button>
                                        <button
                                            @click="confirmDelete(subcategoria)"
                                            class="bg-red-500 text-white px-3 py-1 rounded-md hover:bg-red-600 transition duration-200"
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
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="flex justify-center items-center mt-4 space-x-4">
                    <button
                        @click="changePage(pagination.prev_page_url)"
                        :disabled="!pagination.prev_page_url"
                        class="px-4 py-2 bg-[#277cbb] text-white rounded-md hover:bg-[#63a2c4] transition duration-200"
                    >
                        Anterior
                    </button>
                    <span class="text-gray-700"
                        >Página {{ pagination.current_page }} de
                        {{ pagination.last_page }}</span
                    >
                    <button
                        @click="changePage(pagination.next_page_url)"
                        :disabled="!pagination.next_page_url"
                        class="px-4 py-2 bg-[#277cbb] text-white rounded-md hover:bg-[#63a2c4] transition duration-200"
                    >
                        Siguiente
                    </button>
                </div>
            </div>

            <!-- Modal para crear/editar -->
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
                            isEditing
                                ? "Editar Subcategoría"
                                : "Crear Nueva Subcategoría"
                        }}
                    </h3>
                    <form
                        @submit.prevent="
                            isEditing ? updateItem() : createItem()
                        "
                    >
                        <div class="mb-4">
                            <label
                                class="block text-sm font-medium text-gray-700"
                                >Nombre</label
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
                        <div class="mb-4">
                            <label
                                class="block text-sm font-medium text-gray-700"
                                >Categoría</label
                            >
                            <select
                                v-model="form.categoria_id"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                required
                            >
                                <option value="">
                                    Selecciona una categoría
                                </option>
                                <option
                                    v-for="categoria in categorias"
                                    :key="categoria.id"
                                    :value="categoria.id"
                                >
                                    {{ categoria.nombre }}
                                </option>
                            </select>
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
                    <button
                        @click="closeSuccessModal"
                        class="bg-green-500 text-white px-6 py-2 rounded-md hover:bg-green-600 transition duration-200 shadow-md"
                    >
                        Cerrar
                    </button>
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
                        ¿Eliminar Subcategoría?
                    </h3>
                    <p class="mb-6 text-gray-600">
                        ¿Estás seguro de que deseas eliminar este elemento? Esta
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
                            @click="deleteItem"
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

const API_URL = "/subcategorias";
const CATEGORIA_API_URL = "/categorias";

// Estado del modal y formulario
const isModalOpen = ref(false);
const isEditing = ref(false);
const isSuccessModalOpen = ref(false);
const isConfirmModalOpen = ref(false);
const successMessage = ref("");
const errorMessage = ref("");
const itemIdToDelete = ref(null);

// Datos del formulario
const form = ref({
    nombre: "",
    estado: "activo",
    descripcion: "",
    categoria_id: "",
});

// Lista y paginación
const subcategorias = ref([]);
const categorias = ref([]);
const pagination = ref({
    current_page: 1,
    last_page: 1,
    prev_page_url: null,
    next_page_url: null,
});

const updatePagination = (data) => {
    pagination.value = {
        current_page: data.current_page,
        last_page: data.last_page,
        prev_page_url: data.prev_page_url,
        next_page_url: data.next_page_url,
    };
};

const openModal = () => {
    form.value = {
        nombre: "",
        estado: "activo",
        descripcion: "",
        categoria_id: "",
    };
    isEditing.value = false;
    errorMessage.value = "";
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
};

const editItem = (item) => {
    form.value = { ...item, categoria_id: item.categoria_id || "" };
    isEditing.value = true;
    errorMessage.value = "";
    isModalOpen.value = true;
};

const createItem = async () => {
    try {
        const response = await axios.post(API_URL, form.value);
        subcategorias.value.push(response.data);
        successMessage.value = "Subcategoría creada exitosamente.";
        closeModal();
        isSuccessModalOpen.value = true;
    } catch (error) {
        handleError(error, "Error al crear la subcategoría.");
    }
};

const updateItem = async () => {
    try {
        const response = await axios.put(
            `${API_URL}/${form.value.id}`,
            form.value
        );
        const index = subcategorias.value.findIndex(
            (item) => item.id === form.value.id
        );
        if (index !== -1) {
            subcategorias.value[index] = response.data;
        }
        successMessage.value = "Subcategoría actualizada exitosamente.";
        closeModal();
        isSuccessModalOpen.value = true;
    } catch (error) {
        handleError(error, "Error al actualizar la subcategoría.");
    }
};

const confirmDelete = (item) => {
    itemIdToDelete.value = item.id;
    isConfirmModalOpen.value = true;
};

const deleteItem = async () => {
    try {
        await axios.delete(`${API_URL}/${itemIdToDelete.value}`);
        subcategorias.value = subcategorias.value.filter(
            (item) => item.id !== itemIdToDelete.value
        );
        isConfirmModalOpen.value = false;
        successMessage.value = "Subcategoría eliminada exitosamente.";
        isSuccessModalOpen.value = true;
    } catch (error) {
        handleError(error, "Error al eliminar la subcategoría.");
    }
};

const closeConfirmModal = () => {
    isConfirmModalOpen.value = false;
};

const closeSuccessModal = () => {
    isSuccessModalOpen.value = false;
};

const changePage = async (url) => {
    if (!url) return;
    try {
        const response = await axios.get(url);
        if (response.data) {
            subcategorias.value = response.data.data;
            updatePagination(response.data);
        }
    } catch (error) {
        handleError(error, "Error al cambiar de página.");
    }
};

onMounted(async () => {
    try {
        const [subcatResponse, catResponse] = await Promise.all([
            axios.get(API_URL),
            axios.get(CATEGORIA_API_URL),
        ]);
        if (subcatResponse.data) {
            subcategorias.value = subcatResponse.data.data;
            updatePagination(subcatResponse.data);
        }
        if (catResponse.data) {
            categorias.value = catResponse.data.data;
        }
    } catch (error) {
        handleError(error, "Error al cargar los datos.");
    }
});

const handleError = (error, defaultMessage) => {
    if (error.response && error.response.data && error.response.data.errors) {
        const errorMessages = Object.values(error.response.data.errors);
        errorMessage.value =
            errorMessages.length > 0 ? errorMessages[0][0] : defaultMessage;
    } else {
        errorMessage.value = defaultMessage;
    }
};

watch(
    () => form.value,
    () => {
        errorMessage.value = "";
    },
    { deep: true }
);
</script>
