<!-- resources/js/Pages/TiposMateriales/Show.vue -->

<template>
    <AppLayout>
        <div class="max-w-[900px] mx-auto p-8">
            <!-- Encabezado con acciones -->
            <div class="flex justify-between items-center mb-8 flex-wrap gap-4">
                <Link
                    :href="route('tipos-materiales.index')"
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
                        :href="route('tipos-materiales.edit', id)"
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
                            <path d="M17 3l4 4-7 7H10v-4l7-7z" />
                            <path d="M4 20h16" />
                        </svg>
                        Editar
                    </Link>
                </div>
            </div>

            <!-- Card de detalles -->
            <div
                v-if="tipoMaterial"
                class="bg-white rounded-xl p-8 shadow-md transition-shadow duration-200 hover:shadow-lg"
            >
                <!-- Título -->
                <div class="border-b-2 border-gray-200 pb-4 mb-6">
                    <h1
                        class="font-['Jura',system-ui,sans-serif] font-semibold text-3xl m-0 mb-2 text-gray-800 tracking-tight"
                    >
                        {{ tipoMaterial.nombre }}
                    </h1>
                    <span
                        class="inline-block bg-gray-100 py-1 px-2 rounded text-xs text-gray-600 font-['Helvetica_Neue',Helvetica,Arial,sans-serif]"
                    >
                        ID: {{ tipoMaterial.id }}
                    </span>
                </div>

                <!-- Descripción -->
                <div class="mb-8">
                    <h3
                        class="font-['Jura',system-ui,sans-serif] font-semibold text-xl mb-2 text-gray-800"
                    >
                        Descripción
                    </h3>
                    <p
                        class="leading-relaxed text-gray-600 font-['Helvetica_Neue',Helvetica,Arial,sans-serif]"
                    >
                        {{
                            tipoMaterial.descripcion ||
                            "No hay descripción disponible"
                        }}
                    </p>
                </div>

                <!-- Metadatos -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div
                        class="mb-2 text-sm font-['Helvetica_Neue',Helvetica,Arial,sans-serif]"
                    >
                        <strong class="text-gray-800 mr-2"
                            >Fecha de creación:</strong
                        >
                        <span class="text-gray-500">{{
                            formatDate(tipoMaterial.created_at)
                        }}</span>
                    </div>
                    <div
                        class="text-sm font-['Helvetica_Neue',Helvetica,Arial,sans-serif]"
                    >
                        <strong class="text-gray-800 mr-2"
                            >Última actualización:</strong
                        >
                        <span class="text-gray-500">{{
                            formatDate(tipoMaterial.updated_at)
                        }}</span>
                    </div>
                </div>
            </div>

            <!-- Estado de carga -->
            <div
                v-else
                class="text-center py-12 font-['Helvetica_Neue',Helvetica,Arial,sans-serif] text-gray-500"
            >
                <svg
                    class="animate-spin mx-auto mb-4"
                    width="32"
                    height="32"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <circle cx="12" cy="12" r="10" />
                    <path d="M12 2a10 10 0 1 0 10 10" />
                </svg>
                Cargando información del tipo de material...
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

const tipoMaterial = ref(null);

const fetchData = async () => {
    try {
        const response = await axios.get(`/tipos-materiales/${props.id}`);
        tipoMaterial.value = response.data.data;
    } catch (error) {
        console.error("Error al cargar datos:", error);

        await Swal.fire({
            icon: "error",
            title: "Error",
            text: "Error al cargar el tipo de material",
            confirmButtonColor: "#003366",
        });

        router.visit(route("tipos-materiales.index"));
    }
};

const formatDate = (date) => {
    if (!date) return "-";
    return new Date(date).toLocaleString("es-ES");
};

onMounted(() => {
    fetchData();
});
</script>
