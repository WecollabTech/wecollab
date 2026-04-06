<template>
    <AppLayout title="Editar Subcategoría">
        <template #header>
            <div
                class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4"
            >
                <div>
                    <h2
                        class="text-2xl font-bold text-gray-800 dark:text-white"
                    >
                        Editar Subcategoría
                    </h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        Modifica los datos de la subcategoría existente
                    </p>
                </div>

                <div class="flex gap-3">
                    <!-- ✅ CORREGIDO: Volver sin parámetro categoria_id -->
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
                </div>
            </div>
        </template>

        <div class="py-6 max-w-4xl mx-auto">
            <div
                v-if="cargando"
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-12"
            >
                <div class="flex flex-col items-center justify-center">
                    <div
                        class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mb-4"
                    ></div>
                    <p class="text-gray-600 dark:text-gray-400">
                        Cargando datos de la subcategoría...
                    </p>
                </div>
            </div>

            <div
                v-else-if="subcategoriaOriginal"
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden"
            >
                <form @submit.prevent="actualizarSubcategoria">
                    <div class="p-6 space-y-6">
                        <div
                            v-if="hayCambios"
                            class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-3"
                        >
                            <div
                                class="flex items-center gap-2 text-yellow-800 dark:text-yellow-300"
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
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                                    />
                                </svg>
                                <span class="text-sm font-medium"
                                    >Tien cambios sin guardar</span
                                >
                            </div>
                        </div>

                        <!-- ID de la subcategoría -->
                        <div
                            class="bg-gray-50 dark:bg-gray-900/50 rounded-lg p-3"
                        >
                            <div class="flex items-center gap-2">
                                <span
                                    class="text-sm font-medium text-gray-500 dark:text-gray-400"
                                    >ID:</span
                                >
                                <span
                                    class="text-sm font-mono text-gray-700 dark:text-gray-300"
                                    >#{{ subcategoriaOriginal.id }}</span
                                >
                            </div>
                        </div>

                        <!-- Categoría Padre -->
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                            >
                                Categoría Padre *
                            </label>
                            <select
                                v-model="form.categoria_id"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white dark:bg-gray-900 text-gray-900 dark:text-white"
                                :class="{
                                    'border-red-500 dark:border-red-500':
                                        errors.categoria_id,
                                }"
                            >
                                <option value="">
                                    Seleccione una categoría
                                </option>
                                <option
                                    v-for="categoria in categorias"
                                    :key="categoria.id"
                                    :value="categoria.id"
                                >
                                    {{ categoria.nombre }}
                                </option>
                            </select>
                            <p
                                v-if="errors.categoria_id"
                                class="mt-1 text-sm text-red-600 dark:text-red-400"
                            >
                                {{ errors.categoria_id }}
                            </p>
                        </div>

                        <!-- Nombre -->
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                            >
                                Nombre de la subcategoría *
                            </label>
                            <input
                                v-model="form.nombre"
                                type="text"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white dark:bg-gray-900 text-gray-900 dark:text-white"
                                :class="{
                                    'border-red-500 dark:border-red-500':
                                        errors.nombre,
                                }"
                                placeholder="Ej: Electrónicos, Ropa de Hombre, Utensilios..."
                            />
                            <p
                                v-if="errors.nombre"
                                class="mt-1 text-sm text-red-600 dark:text-red-400"
                            >
                                {{ errors.nombre }}
                            </p>
                            <p
                                class="mt-1 text-xs text-gray-500 dark:text-gray-400"
                            >
                                Mínimo 3 caracteres, máximo 50. Debe ser único
                                dentro de la categoría.
                            </p>
                        </div>

                        <!-- Estado -->
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                                >Estado *</label
                            >
                            <div class="flex gap-4">
                                <label class="inline-flex items-center">
                                    <input
                                        type="radio"
                                        v-model="form.estado"
                                        value="activo"
                                        class="form-radio text-indigo-600 focus:ring-indigo-500"
                                    />
                                    <span
                                        class="ml-2 text-gray-700 dark:text-gray-300"
                                        >Activo</span
                                    >
                                </label>
                                <label class="inline-flex items-center">
                                    <input
                                        type="radio"
                                        v-model="form.estado"
                                        value="inactivo"
                                        class="form-radio text-indigo-600 focus:ring-indigo-500"
                                    />
                                    <span
                                        class="ml-2 text-gray-700 dark:text-gray-300"
                                        >Inactivo</span
                                    >
                                </label>
                            </div>
                            <p
                                v-if="errors.estado"
                                class="mt-1 text-sm text-red-600 dark:text-red-400"
                            >
                                {{ errors.estado }}
                            </p>
                        </div>

                        <!-- Descripción -->
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                                >Descripción</label
                            >
                            <textarea
                                v-model="form.descripcion"
                                rows="4"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white dark:bg-gray-900 text-gray-900 dark:text-white resize-none"
                                :class="{
                                    'border-red-500 dark:border-red-500':
                                        errors.descripcion,
                                }"
                                placeholder="Describe brevemente esta subcategoría (opcional)"
                            ></textarea>
                            <p
                                v-if="errors.descripcion"
                                class="mt-1 text-sm text-red-600 dark:text-red-400"
                            >
                                {{ errors.descripcion }}
                            </p>
                            <p
                                class="mt-1 text-xs text-gray-500 dark:text-gray-400"
                            >
                                Máximo 255 caracteres.
                            </p>
                        </div>

                        <!-- Información de fechas -->
                        <div
                            class="border-t border-gray-200 dark:border-gray-700 pt-4 grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm"
                        >
                            <div>
                                <span class="text-gray-500 dark:text-gray-400"
                                    >Creada:</span
                                >
                                <span
                                    class="ml-2 text-gray-700 dark:text-gray-300"
                                    >{{
                                        formatDate(
                                            subcategoriaOriginal.created_at,
                                        )
                                    }}</span
                                >
                            </div>
                            <div>
                                <span class="text-gray-500 dark:text-gray-400"
                                    >Última actualización:</span
                                >
                                <span
                                    class="ml-2 text-gray-700 dark:text-gray-300"
                                    >{{
                                        formatDate(
                                            subcategoriaOriginal.updated_at,
                                        )
                                    }}</span
                                >
                            </div>
                        </div>
                    </div>

                    <div
                        class="px-6 py-4 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row gap-3 justify-end"
                    >
                        <!-- ✅ CORREGIDO: Cancelar sin parámetro categoria_id -->
                        <Link
                            :href="route('subcategorias.index')"
                            class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200 text-center"
                        >
                            Cancelar
                        </Link>
                        <button
                            type="button"
                            v-if="hayCambios"
                            @click="restaurarOriginal"
                            class="px-4 py-2 rounded-lg border border-yellow-300 dark:border-yellow-700 text-yellow-700 dark:text-yellow-400 hover:bg-yellow-50 dark:hover:bg-yellow-900/20 transition-colors duration-200"
                        >
                            Restaurar
                        </button>
                        <button
                            type="submit"
                            :disabled="enviando || !hayCambios"
                            class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white rounded-lg shadow-sm transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                        >
                            <svg
                                v-if="enviando"
                                class="w-4 h-4 animate-spin"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <circle
                                    class="opacity-25"
                                    cx="12"
                                    cy="12"
                                    r="10"
                                    stroke="currentColor"
                                    stroke-width="4"
                                ></circle>
                                <path
                                    class="opacity-75"
                                    fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                ></path>
                            </svg>
                            {{ enviando ? "Guardando..." : "Guardar cambios" }}
                        </button>
                    </div>
                </form>
            </div>

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
                    >Volver al listado</Link
                >
            </div>
        </div>

        <div
            v-if="toast.show"
            class="fixed bottom-4 right-4 z-50 animate-slide-up"
        >
            <div
                :class="
                    toast.type === 'success' ? 'bg-green-500' : 'bg-red-500'
                "
                class="text-white px-6 py-3 rounded-lg shadow-lg flex items-center gap-2"
            >
                <svg
                    v-if="toast.type === 'success'"
                    class="w-5 h-5"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M5 13l4 4L19 7"
                    />
                </svg>
                <svg
                    v-else
                    class="w-5 h-5"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M6 18L18 6M6 6l12 12"
                    />
                </svg>
                {{ toast.message }}
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from "vue";
import { Link, router } from "@inertiajs/vue3";
import axios from "axios";
import AppLayout from "@/Layouts/AppLayout.vue";

const props = defineProps({ id: { type: [String, Number], required: true } });

const subcategoriaOriginal = ref(null);
const categorias = ref([]);
const cargando = ref(true);
const enviando = ref(false);
const errors = reactive({});
const toast = ref({ show: false, message: "", type: "success" });

const form = reactive({
    categoria_id: "",
    nombre: "",
    estado: "activo",
    descripcion: "",
});

const hayCambios = computed(() => {
    if (!subcategoriaOriginal.value) return false;
    return (
        form.categoria_id != subcategoriaOriginal.value.categoria_id ||
        form.nombre !== subcategoriaOriginal.value.nombre ||
        form.estado !== subcategoriaOriginal.value.estado ||
        form.descripcion !== (subcategoriaOriginal.value.descripcion || "")
    );
});

const mostrarToast = (message, type = "success") => {
    toast.value = { show: true, message, type };
    setTimeout(() => {
        toast.value.show = false;
    }, 3000);
};

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

const cargarDatos = async () => {
    cargando.value = true;
    try {
        const [subcategoriaRes, categoriasRes] = await Promise.all([
            axios.get(`/subcategorias/${props.id}`),
            axios.get("/categorias/all"),
        ]);

        if (subcategoriaRes.data.success) {
            subcategoriaOriginal.value = subcategoriaRes.data.data;
            form.categoria_id = subcategoriaOriginal.value.categoria_id;
            form.nombre = subcategoriaOriginal.value.nombre;
            form.estado = subcategoriaOriginal.value.estado;
            form.descripcion = subcategoriaOriginal.value.descripcion || "";
        }

        if (categoriasRes.data.success)
            categorias.value = categoriasRes.data.data;
    } catch (error) {
        console.error("Error al cargar datos:", error);
        mostrarToast("Error al cargar los datos", "error");
    } finally {
        cargando.value = false;
    }
};

const actualizarSubcategoria = async () => {
    if (!hayCambios.value) return;
    enviando.value = true;
    Object.keys(errors).forEach((key) => delete errors[key]);

    try {
        const response = await axios.put(`/subcategorias/${props.id}`, form);
        if (response.data.success) {
            mostrarToast(response.data.message, "success");
            await cargarDatos();

            // ✅ CORREGIDO: Redirigir sin parámetro categoria_id
            setTimeout(() => {
                router.visit(route("subcategorias.index"));
            }, 1000);
        }
    } catch (error) {
        if (error.response?.status === 422) {
            const validationErrors = error.response.data.errors;
            Object.keys(validationErrors).forEach((key) => {
                errors[key] = validationErrors[key][0];
            });
            mostrarToast(
                "Por favor, corrige los errores del formulario",
                "error",
            );
        } else {
            mostrarToast("Error al actualizar la subcategoría", "error");
        }
    } finally {
        enviando.value = false;
    }
};

const restaurarOriginal = () => {
    if (subcategoriaOriginal.value) {
        form.categoria_id = subcategoriaOriginal.value.categoria_id;
        form.nombre = subcategoriaOriginal.value.nombre;
        form.estado = subcategoriaOriginal.value.estado;
        form.descripcion = subcategoriaOriginal.value.descripcion || "";
        mostrarToast("Cambios revertidos", "success");
    }
};

onMounted(() => {
    cargarDatos();
});
</script>

<style scoped>
@keyframes slide-up {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
.animate-slide-up {
    animation: slide-up 0.3s ease-out;
}
</style>
