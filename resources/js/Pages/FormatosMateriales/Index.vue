<!-- resources/js/Pages/FormatosMateriales/Index.vue -->

<template>
    <AppLayout>
        <div class="max-w-[1200px] mx-auto p-8 bg-white">
            <!-- Encabezado con frase inspiracional -->
            <div
                class="bg-gradient-to-br from-[#667eea] to-[#764ba2] p-8 rounded-xl mb-8 text-white"
            >
                <h1
                    class="font-['Jura',system-ui,sans-serif] font-semibold text-3xl m-0 mb-2 tracking-tight"
                >
                    Formatos de Materiales
                </h1>
            </div>

            <!-- Barra de acciones -->
            <div class="flex justify-between items-center gap-4 flex-wrap mb-8">
                <div class="flex gap-2 flex-1 max-w-[400px]">
                    <input
                        v-model="search"
                        @keyup.enter="fetchData"
                        type="text"
                        placeholder="Buscar por nombre..."
                        class="flex-1 py-2 px-3 border border-gray-300 rounded-md font-['Helvetica_Neue',Helvetica,Arial,sans-serif] text-sm text-gray-800 transition-all duration-200 focus:outline-none focus:border-[#667eea] focus:shadow-[0_0_0_3px_rgba(102,126,234,0.1)]"
                    />
                    <button
                        @click="fetchData"
                        class="py-2 px-4 bg-gray-600 text-white border-none rounded-md cursor-pointer font-['Helvetica_Neue',Helvetica,Arial,sans-serif] font-medium text-sm transition-all duration-200 hover:bg-gray-700"
                    >
                        Buscar
                    </button>
                </div>
                <Link
                    :href="route('formatos-materiales.create')"
                    class="inline-flex items-center gap-2 py-2 px-4 bg-[#667eea] text-white no-underline rounded-md font-['Helvetica_Neue',Helvetica,Arial,sans-serif] font-medium text-sm transition-all duration-200 hover:bg-[#5a67d8] hover:-translate-y-0.5 hover:shadow-lg"
                >
                    <svg
                        width="16"
                        height="16"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <line x1="12" y1="5" x2="12" y2="19" />
                        <line x1="5" y1="12" x2="19" y2="12" />
                    </svg>
                    Nuevo Formato de Material
                </Link>
            </div>

            <!-- Tabla de datos -->
            <div class="overflow-x-auto bg-white rounded-xl shadow-md">
                <table class="w-full border-collapse">
                    <thead class="bg-gray-50 border-b-2 border-gray-200">
                        <tr>
                            <th
                                class="py-4 px-4 text-left font-semibold text-gray-800"
                            >
                                ID
                            </th>
                            <th
                                class="py-4 px-4 text-left font-semibold text-gray-800"
                            >
                                Nombre
                            </th>
                            <th
                                class="py-4 px-4 text-left font-semibold text-gray-800"
                            >
                                Descripción
                            </th>
                            <th
                                class="py-4 px-4 text-left font-semibold text-gray-800"
                            >
                                Fecha Creación
                            </th>
                            <th
                                class="py-4 px-4 text-center font-semibold text-gray-800"
                            >
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="item in formatosMateriales.data"
                            :key="item.id"
                            class="border-b border-gray-100 hover:bg-gray-50 transition-colors"
                        >
                            <td
                                class="py-4 px-4 text-center text-sm text-gray-600"
                            >
                                {{ item.id }}
                            </td>
                            <td class="py-4 px-4">
                                <strong class="text-gray-800">{{
                                    item.nombre
                                }}</strong>
                            </td>
                            <td
                                class="py-4 px-4 text-gray-600 leading-relaxed max-w-[500px] text-sm"
                            >
                                {{ truncateText(item.descripcion, 80) }}
                            </td>
                            <td
                                class="py-4 px-4 text-center text-sm text-gray-500"
                            >
                                {{ formatDate(item.created_at) }}
                            </td>
                            <td class="py-4 px-4">
                                <div class="flex gap-2 justify-center">
                                    <Link
                                        :href="
                                            route(
                                                'formatos-materiales.show',
                                                item.id,
                                            )
                                        "
                                        class="inline-flex items-center justify-center p-2 bg-transparent border-none cursor-pointer rounded transition-all duration-200 hover:bg-blue-100 hover:scale-105"
                                        title="Ver"
                                    >
                                        <svg
                                            width="18"
                                            height="18"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                        >
                                            <path
                                                d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"
                                            />
                                            <circle cx="12" cy="12" r="3" />
                                        </svg>
                                    </Link>
                                    <Link
                                        :href="
                                            route(
                                                'formatos-materiales.edit',
                                                item.id,
                                            )
                                        "
                                        class="inline-flex items-center justify-center p-2 bg-transparent border-none cursor-pointer rounded transition-all duration-200 hover:bg-yellow-100 hover:scale-105"
                                        title="Editar"
                                    >
                                        <svg
                                            width="18"
                                            height="18"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                        >
                                            <path
                                                d="M17 3l4 4-7 7H10v-4l7-7z"
                                            />
                                            <path d="M4 20h16" />
                                        </svg>
                                    </Link>
                                    <button
                                        @click="confirmDelete(item)"
                                        class="inline-flex items-center justify-center p-2 bg-transparent border-none cursor-pointer rounded transition-all duration-200 hover:bg-red-100 hover:scale-105"
                                        title="Eliminar"
                                    >
                                        <svg
                                            width="18"
                                            height="18"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                        >
                                            <polyline points="3 6 5 6 21 6" />
                                            <path
                                                d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"
                                            />
                                            <line
                                                x1="10"
                                                y1="11"
                                                x2="10"
                                                y2="17"
                                            />
                                            <line
                                                x1="14"
                                                y1="11"
                                                x2="14"
                                                y2="17"
                                            />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="formatosMateriales.data?.length === 0">
                            <td
                                colspan="5"
                                class="text-center py-12 text-gray-500"
                            >
                                No hay formatos de materiales registrados
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div
                class="mt-6 flex justify-between items-center flex-wrap gap-4"
                v-if="formatosMateriales.links"
            >
                <div
                    class="text-sm text-gray-600 font-['Helvetica_Neue',Helvetica,Arial,sans-serif]"
                >
                    Mostrando {{ formatosMateriales.from || 0 }} a
                    {{ formatosMateriales.to || 0 }} de
                    {{ formatosMateriales.total || 0 }} resultados
                </div>
                <div class="flex gap-1 flex-wrap">
                    <Link
                        v-for="link in formatosMateriales.links"
                        :key="link.label"
                        :href="link.url"
                        class="inline-flex items-center justify-center min-w-[2rem] py-1.5 px-2 border border-gray-300 text-gray-600 no-underline rounded-md font-['Helvetica_Neue',Helvetica,Arial,sans-serif] text-sm transition-all duration-200 hover:bg-gray-100"
                        :class="{
                            'bg-[#667eea] text-white border-[#667eea] hover:bg-[#667eea]':
                                link.active,
                        }"
                        v-html="link.label"
                    />
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { Link, router } from "@inertiajs/vue3";
import axios from "axios";
import AppLayout from "@/Layouts/AppLayout.vue";
import Swal from "sweetalert2";

const formatosMateriales = ref({
    data: [],
    links: [],
    from: null,
    to: null,
    total: 0,
});
const search = ref("");
const formatoToDelete = ref(null);

const fetchData = async () => {
    try {
        const response = await axios.get("/formatos-materiales", {
            params: { search: search.value },
        });
        formatosMateriales.value = response.data.data;
    } catch (error) {
        console.error("Error al cargar datos:", error);
        await Swal.fire({
            icon: "error",
            title: "Error",
            text: "Error al cargar los formatos de materiales",
            confirmButtonColor: "#667eea",
        });
    }
};

const confirmDelete = (formato) => {
    formatoToDelete.value = formato;

    Swal.fire({
        title: "¿Está seguro?",
        html: `¿Está seguro de eliminar el formato de material <strong>${formato.nombre}</strong>?<br>Esta acción no se puede deshacer.`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#e53e3e",
        cancelButtonColor: "#6b7280",
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar",
        background: "#ffffff",
        customClass: {
            title: "font-['Jura',system-ui,sans-serif] text-gray-800",
            htmlContainer:
                "font-['Helvetica_Neue',Helvetica,Arial,sans-serif] text-gray-600",
            confirmButton: "font-['Helvetica_Neue',Helvetica,Arial,sans-serif]",
            cancelButton: "font-['Helvetica_Neue',Helvetica,Arial,sans-serif]",
        },
    }).then((result) => {
        if (result.isConfirmed) {
            deleteFormato();
        }
    });
};

const deleteFormato = async () => {
    try {
        await axios.delete(`/formatos-materiales/${formatoToDelete.value.id}`);
        formatoToDelete.value = null;
        await fetchData();

        await Swal.fire({
            icon: "success",
            title: "Eliminado",
            text: "Formato de material eliminado exitosamente",
            confirmButtonColor: "#667eea",
            timer: 2000,
            showConfirmButton: true,
        });
    } catch (error) {
        console.error("Error al eliminar:", error);
        await Swal.fire({
            icon: "error",
            title: "Error",
            text: "Error al eliminar el formato de material",
            confirmButtonColor: "#667eea",
        });
    }
};

const formatDate = (date) => {
    if (!date) return "-";
    return new Date(date).toLocaleDateString("es-ES");
};

const truncateText = (text, length) => {
    if (!text) return "-";
    return text.length > length ? text.substring(0, length) + "..." : text;
};

onMounted(() => {
    fetchData();
});
</script>
