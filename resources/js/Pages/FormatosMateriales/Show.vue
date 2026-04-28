<!-- resources/js/Pages/FormatosMateriales/Show.vue -->

<template>
    <AppLayout>
        <div class="max-w-[900px] mx-auto p-8">
            <!-- Encabezado con acciones -->
            <div class="flex justify-between items-center mb-8 flex-wrap gap-4">
                <Link
                    :href="route('formatos-materiales.index')"
                    class="inline-flex items-center gap-2 text-[#667eea] no-underline font-['Helvetica_Neue',Helvetica,Arial,sans-serif] font-medium text-sm transition-all duration-200 hover:text-[#5a67d8] hover:gap-3"
                >
                    <svg
                        width="16"
                        height="16"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
                        <path d="M19 12H5M12 19l-7-7 7-7" />
                    </svg>
                    Volver al listado
                </Link>

                <div class="flex gap-4">
                    <Link
                        :href="route('formatos-materiales.edit', id)"
                        class="inline-flex items-center gap-2 py-2 px-4 bg-[#667eea] text-white no-underline rounded-md font-['Helvetica_Neue',Helvetica,Arial,sans-serif] font-medium text-sm transition-all duration-200 hover:bg-[#5a67d8] hover:scale-[1.02] hover:shadow-md"
                    >
                        <svg
                            width="16"
                            height="16"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path d="M17 3l4 4-7 7H10v-4l7-7z" />
                            <path d="M4 20h16" />
                        </svg>
                        Editar
                    </Link>
                </div>
            </div>

            <!-- Card de detalles -->
            <div
                v-if="formatoMaterial"
                class="bg-white rounded-xl p-8 shadow-md transition-all duration-300 hover:shadow-xl"
            >
                <!-- Título con badge de ID -->
                <div class="border-b-2 border-gray-200 pb-5 mb-6">
                    <div
                        class="flex items-start justify-between flex-wrap gap-4"
                    >
                        <h1
                            class="font-['Jura',system-ui,sans-serif] font-semibold text-3xl m-0 text-gray-800 tracking-tight"
                        >
                            {{ formatoMaterial.nombre }}
                        </h1>
                        <span
                            class="inline-flex items-center gap-1.5 bg-gradient-to-r from-gray-100 to-gray-200 py-1.5 px-3 rounded-full text-xs font-semibold text-gray-700 font-['Helvetica_Neue',Helvetica,Arial,sans-serif] shadow-sm"
                        >
                            <svg
                                width="12"
                                height="12"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <circle cx="12" cy="12" r="10" />
                                <line x1="12" y1="8" x2="12" y2="12" />
                                <line x1="12" y1="16" x2="12.01" y2="16" />
                            </svg>
                            ID: {{ formatoMaterial.id }}
                        </span>
                    </div>
                </div>

                <!-- Descripción -->
                <div class="mb-8">
                    <h3
                        class="font-['Jura',system-ui,sans-serif] font-semibold text-xl mb-3 text-gray-800 flex items-center gap-2"
                    >
                        <svg
                            width="20"
                            height="20"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path d="M4 4h16v16H4z" />
                            <line x1="8" y1="8" x2="16" y2="8" />
                            <line x1="8" y1="12" x2="16" y2="12" />
                            <line x1="8" y1="16" x2="12" y2="16" />
                        </svg>
                        Descripción
                    </h3>
                    <div
                        class="bg-gray-50 rounded-lg p-5 border-l-4 border-[#667eea]"
                    >
                        <p
                            class="leading-relaxed text-gray-700 font-['Helvetica_Neue',Helvetica,Arial,sans-serif] m-0"
                        >
                            {{
                                formatoMaterial.descripcion ||
                                "No hay descripción disponible"
                            }}
                        </p>
                    </div>
                </div>

                <!-- Metadatos -->
                <div
                    class="bg-gradient-to-r from-gray-50 to-gray-100 p-5 rounded-lg"
                >
                    <h4
                        class="font-['Jura',system-ui,sans-serif] font-semibold text-base mb-3 text-gray-700 flex items-center gap-2"
                    >
                        <svg
                            width="16"
                            height="16"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <circle cx="12" cy="12" r="10" />
                            <polyline points="12 6 12 12 16 14" />
                        </svg>
                        Información adicional
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div
                            class="flex items-center gap-2 text-sm font-['Helvetica_Neue',Helvetica,Arial,sans-serif]"
                        >
                            <svg
                                width="14"
                                height="14"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="#667eea"
                                stroke-width="2"
                            >
                                <rect
                                    x="3"
                                    y="4"
                                    width="18"
                                    height="18"
                                    rx="2"
                                    ry="2"
                                />
                                <line x1="16" y1="2" x2="16" y2="6" />
                                <line x1="8" y1="2" x2="8" y2="6" />
                                <line x1="3" y1="10" x2="21" y2="10" />
                            </svg>
                            <strong class="text-gray-700"
                                >Fecha de creación:</strong
                            >
                            <span class="text-gray-600">{{
                                formatDate(formatoMaterial.created_at)
                            }}</span>
                        </div>
                        <div
                            class="flex items-center gap-2 text-sm font-['Helvetica_Neue',Helvetica,Arial,sans-serif]"
                        >
                            <svg
                                width="14"
                                height="14"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="#667eea"
                                stroke-width="2"
                            >
                                <path
                                    d="M20 14.66V20a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h5.34"
                                />
                                <polygon
                                    points="18 2 22 6 12 16 8 16 8 12 18 2"
                                />
                            </svg>
                            <strong class="text-gray-700"
                                >Última actualización:</strong
                            >
                            <span class="text-gray-600">{{
                                formatDate(formatoMaterial.updated_at)
                            }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Estado de carga mejorado -->
            <div v-else class="text-center py-16 bg-gray-50 rounded-xl">
                <svg
                    class="animate-spin mx-auto mb-4"
                    width="40"
                    height="40"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="#667eea"
                    stroke-width="2"
                >
                    <circle cx="12" cy="12" r="10" />
                    <path d="M12 2a10 10 0 1 0 10 10" />
                </svg>
                <p
                    class="font-['Helvetica_Neue',Helvetica,Arial,sans-serif] text-gray-500"
                >
                    Cargando información del formato de material...
                </p>
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

const props = defineProps({
    id: {
        type: [String, Number],
        required: true,
    },
});

const formatoMaterial = ref(null);

const fetchData = async () => {
    try {
        const response = await axios.get(`/formatos-materiales/${props.id}`);
        formatoMaterial.value = response.data.data;
    } catch (error) {
        console.error("Error al cargar datos:", error);

        await Swal.fire({
            icon: "error",
            title: "Error",
            text: "Error al cargar el formato de material",
            confirmButtonColor: "#667eea",
        });

        router.visit(route("formatos-materiales.index"));
    }
};

const formatDate = (date) => {
    if (!date) return "-";
    return new Date(date).toLocaleString("es-ES", {
        year: "numeric",
        month: "long",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
};

onMounted(() => {
    fetchData();
});
</script>
