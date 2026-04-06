<template>
    <AppLayout
        :title="`Subcategoría: ${subcategoria?.nombre || 'Cargando...'}`"
    >
        <template #header>
            <div
                class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4"
            >
                <div>
                    <h2
                        class="text-2xl font-bold text-gray-800 dark:text-white"
                    >
                        Detalles de Subcategoría
                    </h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        Información completa de la subcategoría
                    </p>
                </div>

                <div class="flex gap-3">
                    <Link
                        :href="route('subcategorias.index')"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg transition-all duration-200"
                    >
                        <svg
                            class="w-5 h-5"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18"
                            />
                        </svg>
                        Volver al listado
                    </Link>

                    <Link
                        v-if="subcategoria"
                        :href="route('subcategorias.edit', subcategoria.id)"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg shadow-sm transition-all duration-200"
                    >
                        <svg
                            class="w-5 h-5"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                            />
                        </svg>
                        Editar subcategoría
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-6 max-w-4xl mx-auto">
            <!-- Estado de carga -->
            <div
                v-if="cargando"
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-12"
            >
                <div class="flex flex-col items-center justify-center">
                    <div
                        class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mb-4"
                    ></div>
                    <p class="text-gray-600 dark:text-gray-400">
                        Cargando datos...
                    </p>
                </div>
            </div>

            <!-- Contenido de detalles -->
            <div
                v-else-if="subcategoria"
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden"
            >
                <!-- Cabecera con estado -->
                <div
                    class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-purple-50 to-indigo-50 dark:from-purple-900/20 dark:to-indigo-900/20"
                >
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-full flex items-center justify-center"
                            >
                                <svg
                                    class="w-6 h-6 text-purple-600 dark:text-purple-400"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M3 7h18M3 12h18M3 17h18"
                                    />
                                </svg>
                            </div>
                            <div>
                                <p
                                    class="text-sm text-gray-500 dark:text-gray-400"
                                >
                                    ID #{{ subcategoria.id }}
                                </p>
                                <h3
                                    class="text-xl font-bold text-gray-900 dark:text-white"
                                >
                                    {{ subcategoria.nombre }}
                                </h3>
                            </div>
                        </div>
                        <span
                            :class="{
                                'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400':
                                    subcategoria.estado === 'activo',
                                'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-400':
                                    subcategoria.estado === 'inactivo',
                            }"
                            class="px-3 py-1 rounded-full text-sm font-medium inline-flex items-center gap-1.5"
                        >
                            <span
                                :class="{
                                    'bg-green-500':
                                        subcategoria.estado === 'activo',
                                    'bg-red-500':
                                        subcategoria.estado === 'inactivo',
                                }"
                                class="w-1.5 h-1.5 rounded-full"
                            ></span>
                            {{
                                subcategoria.estado === "activo"
                                    ? "Activo"
                                    : "Inactivo"
                            }}
                        </span>
                    </div>
                </div>

                <!-- Cuerpo con detalles -->
                <div class="p-6 space-y-6">
                    <!-- Información general -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label
                                class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1"
                            >
                                Categoría padre
                            </label>
                            <div class="flex items-center gap-2">
                                <div
                                    class="w-8 h-8 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg flex items-center justify-center"
                                >
                                    <svg
                                        class="w-4 h-4 text-indigo-600 dark:text-indigo-400"
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
                                </div>
                                <span
                                    class="font-medium text-gray-900 dark:text-white"
                                >
                                    {{
                                        subcategoria.categoria?.nombre ||
                                        "No asignada"
                                    }}
                                </span>
                            </div>
                        </div>

                        <div>
                            <label
                                class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1"
                            >
                                Fecha de creación
                            </label>
                            <p
                                class="text-gray-900 dark:text-white flex items-center gap-2"
                            >
                                <svg
                                    class="w-4 h-4 text-gray-400"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                                    />
                                </svg>
                                {{ formatDate(subcategoria.created_at) }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1"
                            >
                                Última actualización
                            </label>
                            <p
                                class="text-gray-900 dark:text-white flex items-center gap-2"
                            >
                                <svg
                                    class="w-4 h-4 text-gray-400"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
                                    />
                                </svg>
                                {{ formatDate(subcategoria.updated_at) }}
                            </p>
                        </div>
                    </div>

                    <!-- Descripción - Mejorada y destacada -->
                    <div
                        class="border-t border-gray-200 dark:border-gray-700 pt-4"
                    >
                        <div class="flex items-center gap-2 mb-3">
                            <svg
                                class="w-5 h-5 text-indigo-600 dark:text-indigo-400"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h7"
                                />
                            </svg>
                            <label
                                class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider"
                            >
                                Descripción
                            </label>
                        </div>

                        <div v-if="subcategoria.descripcion" class="relative">
                            <div
                                class="bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900/50 dark:to-gray-800/50 rounded-xl p-5 border border-gray-200 dark:border-gray-700"
                            >
                                <div class="flex gap-3">
                                    <div class="flex-shrink-0">
                                        <svg
                                            class="w-5 h-5 text-indigo-500 dark:text-indigo-400 mt-0.5"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                            />
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <p
                                            class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap leading-relaxed"
                                        >
                                            {{ subcategoria.descripcion }}
                                        </p>
                                        <div
                                            class="mt-3 pt-2 text-xs text-gray-400 dark:text-gray-500 flex items-center gap-1"
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
                                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6"
                                                />
                                            </svg>
                                            <span
                                                >{{
                                                    contarPalabras(
                                                        subcategoria.descripcion,
                                                    )
                                                }}
                                                palabras •
                                                {{
                                                    subcategoria.descripcion
                                                        .length
                                                }}
                                                caracteres</span
                                            >
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Estado cuando no hay descripción -->
                        <div
                            v-else
                            class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-4 border border-yellow-200 dark:border-yellow-800"
                        >
                            <div class="flex items-center gap-3">
                                <svg
                                    class="w-5 h-5 text-yellow-600 dark:text-yellow-500"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                                    />
                                </svg>
                                <p
                                    class="text-yellow-700 dark:text-yellow-400 text-sm"
                                >
                                    Esta subcategoría no tiene una descripción
                                    asociada.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Botones de acción -->
                    <div
                        class="pt-4 border-t border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row gap-3 justify-end"
                    >
                        <Link
                            :href="route('subcategorias.index')"
                            class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200 text-center"
                        >
                            Volver al listado
                        </Link>
                        <Link
                            :href="route('subcategorias.edit', subcategoria.id)"
                            class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg shadow-sm transition-all duration-200 text-center flex items-center justify-center gap-2"
                        >
                            <svg
                                class="w-4 h-4"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"
                                />
                            </svg>
                            Editar subcategoría
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Estado de error / no encontrado -->
            <div
                v-else
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-12 text-center"
            >
                <svg
                    class="mx-auto h-16 w-16 text-gray-400 mb-4"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                    />
                </svg>
                <h3
                    class="text-lg font-medium text-gray-900 dark:text-white mb-2"
                >
                    Subcategoría no encontrada
                </h3>
                <p class="text-gray-600 dark:text-gray-400 mb-4">
                    La subcategoría que buscas no existe o ha sido eliminada.
                </p>
                <Link
                    :href="route('subcategorias.index')"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg"
                >
                    Volver al listado
                </Link>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { Link } from "@inertiajs/vue3";
import axios from "axios";
import AppLayout from "@/Layouts/AppLayout.vue";

const props = defineProps({
    id: {
        type: [String, Number],
        required: true,
    },
});

const subcategoria = ref(null);
const cargando = ref(true);

const formatDate = (date) => {
    if (!date) return "—";
    return new Date(date).toLocaleDateString("es-ES", {
        day: "2-digit",
        month: "long",
        year: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
};

const contarPalabras = (texto) => {
    if (!texto) return 0;
    return texto.trim().split(/\s+/).length;
};

const cargarSubcategoria = async () => {
    cargando.value = true;
    try {
        const response = await axios.get(`/subcategorias/${props.id}`);
        if (response.data.success) {
            subcategoria.value = response.data.data;
        }
    } catch (error) {
        console.error("Error al cargar subcategoría:", error);
    } finally {
        cargando.value = false;
    }
};

onMounted(() => {
    cargarSubcategoria();
});
</script>
