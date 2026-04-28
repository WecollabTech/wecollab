<!-- resources/js/Pages/TiposMateriales/Index.vue -->

<template>
    <AppLayout>
        <div class="max-w-[1400px] mx-auto p-8 bg-white">
            <!-- Encabezado corporativo -->
            <div class="mb-8">
                <div class="relative inline-block mb-3">
                    <h1
                        class="font-['Jura',system-ui,sans-serif] font-semibold text-3xl text-gray-800 m-0 tracking-tight"
                    >
                        Tipos de Materiales
                    </h1>
                    <div
                        class="absolute -bottom-1 left-0 w-[60px] h-0.5 bg-[#cc6600] rounded-full"
                    ></div>
                </div>
                <p
                    class="font-['Helvetica_Neue',Helvetica,Arial,sans-serif] text-sm text-gray-500 m-0 leading-relaxed"
                >
                    Gestione y administre los tipos de materiales utilizados en
                    la plataforma
                </p>
            </div>

            <!-- Barra de acciones -->
            <div class="flex justify-between items-center gap-4 flex-wrap mb-6">
                <div
                    class="flex items-center gap-2 flex-1 max-w-[400px] bg-white border border-gray-300 rounded-md transition-all duration-200 focus-within:border-[#003366] focus-within:shadow-[0_0_0_3px_rgba(0,51,102,0.1)]"
                >
                    <div class="flex items-center pl-3 text-gray-400">
                        <svg
                            width="16"
                            height="16"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <circle cx="11" cy="11" r="8" />
                            <line x1="21" y1="21" x2="16.65" y2="16.65" />
                        </svg>
                    </div>
                    <input
                        v-model="search"
                        @keyup.enter="fetchData"
                        type="text"
                        placeholder="Buscar por nombre..."
                        class="flex-1 py-2 px-0 border-none font-['Helvetica_Neue',Helvetica,Arial,sans-serif] text-sm text-gray-800 bg-transparent focus:outline-none placeholder:text-gray-400"
                    />
                    <button
                        @click="fetchData"
                        class="py-2 px-4 bg-gray-500 text-white border-none rounded-r-md cursor-pointer font-['Helvetica_Neue',Helvetica,Arial,sans-serif] font-medium text-sm transition-all duration-200 hover:bg-gray-600"
                    >
                        Buscar
                    </button>
                </div>
                <Link
                    :href="route('tipos-materiales.create')"
                    class="inline-flex items-center gap-2 py-2 px-4 bg-[#003366] text-white no-underline rounded-md font-['Helvetica_Neue',Helvetica,Arial,sans-serif] font-medium text-sm transition-all duration-200 hover:bg-[#002244] hover:scale-[1.02] hover:shadow-md"
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
                    Nuevo Tipo de Material
                </Link>
            </div>

            <!-- Tabla de datos -->
            <div
                class="overflow-x-auto bg-white border border-gray-200 rounded-lg shadow-sm"
            >
                <table
                    class="w-full border-collapse font-['Helvetica_Neue',Helvetica,Arial,sans-serif]"
                >
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th
                                class="w-[60px] py-3.5 px-4 text-left font-semibold text-[0.65rem] uppercase tracking-wider text-gray-500"
                            >
                                ID
                            </th>
                            <th
                                class="w-[20%] py-3.5 px-4 text-left font-semibold text-[0.65rem] uppercase tracking-wider text-gray-500"
                            >
                                Nombre
                            </th>
                            <th
                                class="w-[40%] py-3.5 px-4 text-left font-semibold text-[0.65rem] uppercase tracking-wider text-gray-500"
                            >
                                Descripción
                            </th>
                            <th
                                class="w-[120px] py-3.5 px-4 text-left font-semibold text-[0.65rem] uppercase tracking-wider text-gray-500"
                            >
                                Fecha Creación
                            </th>
                            <th
                                class="w-[100px] py-3.5 px-4 text-left font-semibold text-[0.65rem] uppercase tracking-wider text-gray-500"
                            >
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="item in tiposMateriales.data"
                            :key="item.id"
                            class="hover:bg-gray-50"
                        >
                            <td
                                class="py-4 px-4 border-b border-gray-100 text-sm text-gray-800 font-medium text-gray-500"
                            >
                                {{ item.id }}
                            </td>
                            <td
                                class="py-4 px-4 border-b border-gray-100 text-sm text-gray-800"
                            >
                                <span class="font-semibold text-[#003366]">{{
                                    item.nombre
                                }}</span>
                            </td>
                            <td
                                class="py-4 px-4 border-b border-gray-100 text-sm text-gray-800"
                            >
                                <span class="text-gray-600 leading-relaxed">{{
                                    truncateText(item.descripcion, 80)
                                }}</span>
                            </td>
                            <td
                                class="py-4 px-4 border-b border-gray-100 text-sm text-gray-800"
                            >
                                <span
                                    class="inline-block py-1 px-2 bg-gray-100 rounded text-xs text-gray-500"
                                    >{{ formatDate(item.created_at) }}</span
                                >
                            </td>
                            <td
                                class="py-4 px-4 border-b border-gray-100 text-sm text-gray-800"
                            >
                                <div class="flex gap-2">
                                    <Link
                                        :href="
                                            route(
                                                'tipos-materiales.show',
                                                item.id,
                                            )
                                        "
                                        class="inline-flex items-center justify-center p-1.5 border-none bg-transparent cursor-pointer rounded transition-all duration-200 text-gray-500 hover:bg-[#e0f2fe] hover:text-[#003366] hover:scale-105"
                                        title="Ver detalles"
                                    >
                                        <svg
                                            width="16"
                                            height="16"
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
                                                'tipos-materiales.edit',
                                                item.id,
                                            )
                                        "
                                        class="inline-flex items-center justify-center p-1.5 border-none bg-transparent cursor-pointer rounded transition-all duration-200 text-gray-500 hover:bg-[#fef3c7] hover:text-[#cc6600] hover:scale-105"
                                        title="Editar"
                                    >
                                        <svg
                                            width="16"
                                            height="16"
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
                                        class="inline-flex items-center justify-center p-1.5 border-none bg-transparent cursor-pointer rounded transition-all duration-200 text-gray-500 hover:bg-[#fee2e2] hover:text-red-600 hover:scale-105"
                                        title="Eliminar"
                                    >
                                        <svg
                                            width="16"
                                            height="16"
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
                        <tr v-if="tiposMateriales.data.length === 0">
                            <td colspan="5" class="text-center py-12">
                                <div class="flex flex-col items-center gap-4">
                                    <svg
                                        width="48"
                                        height="48"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="#9CA3AF"
                                        stroke-width="1.5"
                                    >
                                        <rect
                                            x="3"
                                            y="3"
                                            width="18"
                                            height="18"
                                            rx="2"
                                            ry="2"
                                        />
                                        <line x1="9" y1="9" x2="15" y2="15" />
                                        <line x1="15" y1="9" x2="9" y2="15" />
                                    </svg>
                                    <p class="text-gray-500 text-sm m-0">
                                        No hay tipos de materiales registrados
                                    </p>
                                    <Link
                                        :href="route('tipos-materiales.create')"
                                        class="inline-flex py-2 px-4 bg-[#003366] text-white no-underline rounded-md text-sm transition-all duration-200 hover:bg-[#002244] hover:scale-[1.02]"
                                    >
                                        Crear primer tipo de material
                                    </Link>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div
                class="mt-6 flex justify-between items-center flex-wrap gap-4"
                v-if="tiposMateriales.links"
            >
                <div
                    class="font-['Helvetica_Neue',Helvetica,Arial,sans-serif] text-xs text-gray-500"
                >
                    Mostrando {{ tiposMateriales.from || 0 }} a
                    {{ tiposMateriales.to || 0 }} de
                    {{ tiposMateriales.total || 0 }} resultados
                </div>
                <div class="flex gap-1 flex-wrap">
                    <Link
                        v-for="link in tiposMateriales.links"
                        :key="link.label"
                        :href="link.url"
                        class="inline-flex items-center justify-center min-w-[2rem] py-1.5 px-2 border border-gray-200 text-gray-600 no-underline rounded-md font-['Helvetica_Neue',Helvetica,Arial,sans-serif] text-sm transition-all duration-200 hover:bg-gray-100 hover:border-gray-300"
                        :class="{
                            'bg-[#003366] text-white border-[#003366] hover:bg-[#003366]':
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

const tiposMateriales = ref({
    data: [],
    links: [],
    from: null,
    to: null,
    total: 0,
});
const search = ref("");
const showDeleteModal = ref(false);
const materialToDelete = ref(null);

const fetchData = async () => {
    try {
        const response = await axios.get("/tipos-materiales", {
            params: { search: search.value },
        });
        tiposMateriales.value = response.data.data;
    } catch (error) {
        console.error("Error al cargar datos:", error);
        await Swal.fire({
            icon: "error",
            title: "Error",
            text: "Error al cargar los tipos de materiales",
            confirmButtonColor: "#003366",
        });
    }
};

const confirmDelete = (material) => {
    materialToDelete.value = material;

    Swal.fire({
        title: "¿Está seguro?",
        html: `¿Está seguro de eliminar el tipo de material <strong>${material.nombre}</strong>?<br>Esta acción no se puede deshacer.`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#dc2626",
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
            deleteMaterial();
        }
    });
};

const deleteMaterial = async () => {
    try {
        await axios.delete(
            `/api/tipos-materiales/${materialToDelete.value.id}`,
        );
        materialToDelete.value = null;
        await fetchData();

        await Swal.fire({
            icon: "success",
            title: "Eliminado",
            text: "Tipo de material eliminado exitosamente",
            confirmButtonColor: "#003366",
            timer: 2000,
            showConfirmButton: true,
        });
    } catch (error) {
        console.error("Error al eliminar:", error);
        await Swal.fire({
            icon: "error",
            title: "Error",
            text: "Error al eliminar el tipo de material",
            confirmButtonColor: "#003366",
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
