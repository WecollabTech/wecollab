<template>
    <AppLayout>
        <div class="max-w-[800px] mx-auto p-8">
            <!-- Header con jerarquía clara -->
            <div class="mb-8">
                <Link
                    :href="route('formatos-materiales.index')"
                    class="inline-flex items-center gap-2 mb-6 text-gray-500 no-underline font-['Helvetica_Neue',Helvetica,Arial,sans-serif] text-sm font-medium transition-all duration-200 hover:text-[#003366] hover:-translate-x-0.5"
                >
                    <svg
                        class="transition-transform duration-200"
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
                    <span>Volver</span>
                </Link>

                <div class="border-l-3 border-l-[#cc6600] pl-4">
                    <h1
                        class="font-['Jura',system-ui,sans-serif] font-semibold text-[1.875rem] m-0 mb-2 text-gray-800 tracking-tight"
                    >
                        Crear Formato de Material
                    </h1>
                    <p class="text-gray-500 m-0 text-sm leading-relaxed">
                        Registra un nuevo formato de material con excelencia y
                        vocación de servicio
                    </p>
                </div>
            </div>

            <!-- Card principal -->
            <div
                class="bg-white border border-gray-200 rounded-lg p-8 shadow-sm transition-shadow duration-200 hover:shadow-md"
            >
                <form @submit.prevent="submitForm">
                    <!-- Campo Nombre -->
                    <div class="mb-7">
                        <label
                            for="nombre"
                            class="flex items-center gap-2 mb-2.5 font-semibold text-gray-800 font-['Helvetica_Neue',Helvetica,Arial,sans-serif] text-sm"
                        >
                            Nombre
                            <span
                                class="bg-gray-100 text-gray-500 text-[0.7rem] font-medium py-0.5 px-2 rounded tracking-wide"
                            >
                                Requerido
                            </span>
                        </label>
                        <input
                            id="nombre"
                            v-model="form.nombre"
                            type="text"
                            placeholder="Ej: Lámina, Rollo, Barra, Plancha..."
                            :class="{
                                'border-red-600 bg-red-50': errors.nombre,
                                'border-gray-300 focus:border-[#003366] focus:shadow-[0_0_0_3px_rgba(0,51,102,0.1)]':
                                    !errors.nombre,
                            }"
                            class="w-full py-2.5 px-3.5 border rounded-md font-['Helvetica_Neue',Helvetica,Arial,sans-serif] text-sm text-gray-800 transition-all duration-200 bg-white hover:border-gray-500 focus:outline-none"
                            autofocus
                        />
                        <div class="mt-2">
                            <span
                                v-if="errors.nombre"
                                class="flex items-center gap-1.5 text-red-600 text-xs font-['Helvetica_Neue',Helvetica,Arial,sans-serif]"
                            >
                                <svg
                                    class="flex-shrink-0"
                                    width="14"
                                    height="14"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <circle cx="12" cy="12" r="10" />
                                    <line x1="12" y1="8" x2="12" y2="12" />
                                    <line x1="12" y1="16" x2="12.01" y2="16" />
                                </svg>
                                {{ errors.nombre }}
                            </span>
                            <small
                                v-else
                                class="flex items-center gap-1.5 text-gray-500 text-xs font-['Helvetica_Neue',Helvetica,Arial,sans-serif]"
                            >
                                <svg
                                    class="flex-shrink-0"
                                    width="12"
                                    height="12"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <circle cx="12" cy="12" r="10" />
                                    <line x1="12" y1="16" x2="12" y2="12" />
                                    <line x1="12" y1="8" x2="12.01" y2="8" />
                                </svg>
                                Nombre único para identificar el formato
                            </small>
                        </div>
                    </div>

                    <!-- Campo Descripción -->
                    <div class="mb-8">
                        <label
                            for="descripcion"
                            class="flex items-center gap-2 mb-2.5 font-semibold text-gray-800 font-['Helvetica_Neue',Helvetica,Arial,sans-serif] text-sm"
                        >
                            Descripción
                        </label>
                        <textarea
                            id="descripcion"
                            v-model="form.descripcion"
                            rows="5"
                            placeholder="Describe las características, usos o especificaciones de este formato de material..."
                            :class="{
                                'border-red-600 bg-red-50': errors.descripcion,
                                'border-gray-300 focus:border-[#003366] focus:shadow-[0_0_0_3px_rgba(0,51,102,0.1)]':
                                    !errors.descripcion,
                            }"
                            class="w-full py-2.5 px-3.5 border rounded-md font-['Helvetica_Neue',Helvetica,Arial,sans-serif] text-sm text-gray-800 transition-all duration-200 bg-white resize-y min-h-[100px] hover:border-gray-500 focus:outline-none"
                        ></textarea>
                        <div class="mt-2">
                            <span
                                v-if="errors.descripcion"
                                class="flex items-center gap-1.5 text-red-600 text-xs font-['Helvetica_Neue',Helvetica,Arial,sans-serif]"
                            >
                                <svg
                                    class="flex-shrink-0"
                                    width="14"
                                    height="14"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <circle cx="12" cy="12" r="10" />
                                    <line x1="12" y1="8" x2="12" y2="12" />
                                    <line x1="12" y1="16" x2="12.01" y2="16" />
                                </svg>
                                {{ errors.descripcion }}
                            </span>
                            <small
                                v-else
                                class="flex items-center gap-1.5 text-gray-500 text-xs font-['Helvetica_Neue',Helvetica,Arial,sans-serif]"
                            >
                                <svg
                                    class="flex-shrink-0"
                                    width="12"
                                    height="12"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <circle cx="12" cy="12" r="10" />
                                    <line x1="12" y1="16" x2="12" y2="12" />
                                    <line x1="12" y1="8" x2="12.01" y2="8" />
                                </svg>
                                Máximo 500 caracteres. Sé claro y preciso.
                            </small>
                        </div>
                    </div>

                    <!-- Acciones del formulario -->
                    <div
                        class="flex gap-4 justify-end mt-8 pt-6 border-t border-gray-200"
                    >
                        <Link
                            :href="route('formatos-materiales.index')"
                            class="inline-flex items-center justify-center py-2.5 px-6 bg-gray-100 text-gray-800 no-underline rounded-md font-['Helvetica_Neue',Helvetica,Arial,sans-serif] font-medium text-sm transition-all duration-200 border border-gray-200 hover:bg-gray-200 hover:scale-[1.02] active:scale-[0.98]"
                        >
                            Cancelar
                        </Link>
                        <button
                            type="submit"
                            class="inline-flex items-center justify-center gap-2 py-2.5 px-6 bg-[#003366] text-white border-none rounded-md cursor-pointer font-['Helvetica_Neue',Helvetica,Arial,sans-serif] font-medium text-sm transition-all duration-200 disabled:opacity-60 disabled:cursor-not-allowed hover:disabled:opacity-60 hover:disabled:scale-100 hover:bg-[#002244] hover:scale-[1.02] active:scale-[0.98]"
                            :disabled="loading"
                        >
                            <span
                                v-if="loading"
                                class="inline-flex items-center gap-2"
                            >
                                <svg
                                    class="animate-spin"
                                    width="16"
                                    height="16"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <circle cx="12" cy="12" r="10" />
                                    <path d="M12 2a10 10 0 1 0 10 10" />
                                </svg>
                                Guardando...
                            </span>
                            <span v-else class="inline-flex items-center gap-2">
                                <svg
                                    width="16"
                                    height="16"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <path
                                        d="M20 14.66V20a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h5.34"
                                    />
                                    <polygon
                                        points="18 2 22 6 12 16 8 16 8 12 18 2"
                                    />
                                </svg>
                                Crear Formato de Material
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref } from "vue";
import { Link, router } from "@inertiajs/vue3";
import axios from "axios";
import AppLayout from "@/Layouts/AppLayout.vue";
import Swal from "sweetalert2";

const form = ref({
    nombre: "",
    descripcion: "",
});

const errors = ref({});
const loading = ref(false);

const submitForm = async () => {
    loading.value = true;
    errors.value = {};

    try {
        const response = await axios.post("/formatos-materiales", form.value);

        if (response.data.success) {
            await Swal.fire({
                icon: "success",
                title: "¡Creado!",
                text: "Formato de material creado exitosamente",
                confirmButtonColor: "#003366",
                timer: 2000,
                showConfirmButton: true,
            });

            router.visit(route("formatos-materiales.index"));
        }
    } catch (error) {
        if (error.response?.status === 422) {
            errors.value = error.response.data.errors;
            await Swal.fire({
                icon: "error",
                title: "Error de validación",
                text: "Por favor, revise los campos marcados",
                confirmButtonColor: "#003366",
            });
        } else {
            console.error("Error:", error);
            await Swal.fire({
                icon: "error",
                title: "Error",
                text:
                    error.response?.data?.message ||
                    "Error al crear el formato de material",
                confirmButtonColor: "#003366",
            });
        }
    } finally {
        loading.value = false;
    }
};
</script>
