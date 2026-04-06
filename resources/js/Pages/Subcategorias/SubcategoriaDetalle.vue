<template>
    <div
        v-if="mostrar"
        class="fixed inset-0 overflow-hidden z-50"
        aria-labelledby="slide-over-title"
        role="dialog"
        aria-modal="true"
    >
        <div class="absolute inset-0 overflow-hidden">
            <!-- Fondo oscuro -->
            <div
                class="absolute inset-0 bg-gray-500 bg-opacity-75 transition-opacity dark:bg-gray-900 dark:bg-opacity-80"
                @click="cerrar"
            ></div>

            <div
                class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10"
            >
                <div
                    class="pointer-events-auto w-screen max-w-md transform transition-transform duration-300 ease-in-out"
                >
                    <div
                        class="flex h-full flex-col overflow-y-scroll bg-white dark:bg-gray-800 shadow-xl"
                    >
                        <!-- Cabecera -->
                        <div
                            class="px-4 py-6 sm:px-6 border-b border-gray-200 dark:border-gray-700"
                        >
                            <div class="flex items-start justify-between">
                                <h2
                                    id="slide-over-title"
                                    class="text-lg font-medium text-gray-900 dark:text-white"
                                >
                                    Detalles de subcategoría
                                </h2>
                                <button
                                    @click="cerrar"
                                    class="rounded-md text-gray-400 hover:text-gray-500 focus:outline-none"
                                >
                                    <svg
                                        class="h-6 w-6"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"
                                        />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Contenido -->
                        <div class="flex-1 px-4 py-6 sm:px-6 space-y-6">
                            <div v-if="cargando" class="text-center py-12">
                                <div
                                    class="animate-spin rounded-full h-10 w-10 border-b-2 border-indigo-600 mx-auto"
                                ></div>
                                <p class="mt-2 text-sm text-gray-500">
                                    Cargando detalles...
                                </p>
                            </div>

                            <div v-else-if="subcategoria" class="space-y-6">
                                <!-- Badge estado -->
                                <div class="flex justify-between items-center">
                                    <span
                                        class="text-xs font-medium text-gray-500 uppercase"
                                        >Estado</span
                                    >
                                    <span
                                        :class="
                                            subcategoria.estado === 'activo'
                                                ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400'
                                                : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400'
                                        "
                                        class="px-2 py-1 rounded-full text-xs font-medium"
                                    >
                                        {{
                                            subcategoria.estado === "activo"
                                                ? "Activo"
                                                : "Inactivo"
                                        }}
                                    </span>
                                </div>

                                <!-- ID -->
                                <div>
                                    <dt
                                        class="text-xs font-medium text-gray-500 uppercase"
                                    >
                                        ID
                                    </dt>
                                    <dd
                                        class="mt-1 text-sm text-gray-900 dark:text-white"
                                    >
                                        #{{ subcategoria.id }}
                                    </dd>
                                </div>

                                <!-- Nombre -->
                                <div>
                                    <dt
                                        class="text-xs font-medium text-gray-500 uppercase"
                                    >
                                        Nombre
                                    </dt>
                                    <dd
                                        class="mt-1 text-sm font-medium text-gray-900 dark:text-white"
                                    >
                                        {{ subcategoria.nombre }}
                                    </dd>
                                </div>

                                <!-- Categoría padre -->
                                <div>
                                    <dt
                                        class="text-xs font-medium text-gray-500 uppercase"
                                    >
                                        Categoría padre
                                    </dt>
                                    <dd class="mt-1">
                                        <span
                                            class="inline-flex items-center gap-1 px-2 py-1 bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400 rounded-md text-sm"
                                        >
                                            <svg
                                                class="w-3 h-3"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l5 5a2 2 0 01.586 1.414V19a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2z"
                                                />
                                            </svg>
                                            {{
                                                subcategoria.categoria
                                                    ?.nombre || "Sin categoría"
                                            }}
                                        </span>
                                    </dd>
                                </div>

                                <!-- Descripción -->
                                <div v-if="subcategoria.descripcion">
                                    <dt
                                        class="text-xs font-medium text-gray-500 uppercase"
                                    >
                                        Descripción
                                    </dt>
                                    <dd
                                        class="mt-1 text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap"
                                    >
                                        {{ subcategoria.descripcion }}
                                    </dd>
                                </div>

                                <!-- Fechas -->
                                <div
                                    class="pt-4 border-t border-gray-200 dark:border-gray-700 grid grid-cols-2 gap-4 text-sm"
                                >
                                    <div>
                                        <dt class="text-xs text-gray-500">
                                            Creada
                                        </dt>
                                        <dd
                                            class="mt-1 text-gray-700 dark:text-gray-300"
                                        >
                                            {{
                                                formatearFecha(
                                                    subcategoria.created_at,
                                                )
                                            }}
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-xs text-gray-500">
                                            Actualizada
                                        </dt>
                                        <dd
                                            class="mt-1 text-gray-700 dark:text-gray-300"
                                        >
                                            {{
                                                formatearFecha(
                                                    subcategoria.updated_at,
                                                )
                                            }}
                                        </dd>
                                    </div>
                                </div>
                            </div>

                            <div v-else class="text-center py-12 text-gray-500">
                                No hay datos disponibles
                            </div>
                        </div>

                        <!-- Footer con acciones -->
                        <div
                            class="border-t border-gray-200 dark:border-gray-700 px-4 py-4 sm:px-6 flex justify-end gap-3"
                        >
                            <button
                                @click="cerrar"
                                class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition"
                            >
                                Cerrar
                            </button>
                            <Link
                                v-if="subcategoria"
                                :href="`/subcategorias/${subcategoria.id}/edit`"
                                class="px-4 py-2 text-sm font-medium bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition"
                                @click="cerrar"
                            >
                                Editar subcategoría
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, watch } from "vue";
import { Link } from "@inertiajs/vue3";
import axios from "axios";

const props = defineProps({
    mostrar: {
        type: Boolean,
        default: false,
    },
    subcategoriaId: {
        type: [Number, String],
        default: null,
    },
});

const emit = defineEmits(["cerrar"]);

const subcategoria = ref(null);
const cargando = ref(false);

const cerrar = () => {
    emit("cerrar");
    subcategoria.value = null;
};

const cargarDetalle = async (id) => {
    if (!id) return;
    cargando.value = true;
    try {
        const response = await axios.get(`/subcategorias/${id}`);
        if (response.data.success) {
            subcategoria.value = response.data.data;
        }
    } catch (error) {
        console.error("Error cargando detalle:", error);
    } finally {
        cargando.value = false;
    }
};

const formatearFecha = (fecha) => {
    if (!fecha) return "—";
    return new Date(fecha).toLocaleString("es-ES", {
        dateStyle: "medium",
        timeStyle: "short",
    });
};

watch(
    () => props.mostrar,
    (nuevo) => {
        if (nuevo && props.subcategoriaId) {
            cargarDetalle(props.subcategoriaId);
        }
    },
);
</script>
