<template>
    <AppLayout title="Crear Nueva Categoría">
        <template #header>
            <div
                class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4"
            >
                <div>
                    <h2
                        class="text-2xl font-bold text-gray-800 dark:text-white"
                    >
                        Crear Nueva Categoría
                    </h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        Completa los campos para registrar una nueva categoría
                    </p>
                </div>

                <Link
                    :href="route('categorias.index')"
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
        </template>

        <div class="py-6 max-w-4xl mx-auto">
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden"
            >
                <form @submit.prevent="guardarCategoria">
                    <div class="p-6 space-y-6">
                        <!-- Nombre de la categoría -->
                        <div>
                            <label
                                for="nombre"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                            >
                                Nombre de la categoría *
                            </label>
                            <input
                                id="nombre"
                                v-model="form.nombre"
                                type="text"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white dark:bg-gray-900 text-gray-900 dark:text-white transition-colors"
                                :class="{
                                    'border-red-500 dark:border-red-500':
                                        errors.nombre,
                                }"
                                placeholder="Ej: Electrónica, Ropa, Hogar..."
                                autofocus
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
                                Mínimo 3 caracteres, máximo 50. Debe ser único.
                            </p>
                        </div>

                        <!-- Estado -->
                        <div>
                            <label
                                for="estado"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                            >
                                Estado *
                            </label>
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
                                for="descripcion"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                            >
                                Descripción
                            </label>
                            <textarea
                                id="descripcion"
                                v-model="form.descripcion"
                                rows="4"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white dark:bg-gray-900 text-gray-900 dark:text-white resize-none transition-colors"
                                :class="{
                                    'border-red-500 dark:border-red-500':
                                        errors.descripcion,
                                }"
                                placeholder="Describe brevemente esta categoría (opcional)"
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

                        <!-- Vista previa en tiempo real -->
                        <div
                            v-if="form.nombre"
                            class="border-t border-gray-200 dark:border-gray-700 pt-4"
                        >
                            <h3
                                class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                            >
                                Vista previa:
                            </h3>
                            <div
                                class="bg-gray-50 dark:bg-gray-900/50 rounded-lg p-4"
                            >
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg flex items-center justify-center"
                                    >
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
                                                d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l5 5a2 2 0 01.586 1.414V19a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2z"
                                            />
                                        </svg>
                                    </div>
                                    <div>
                                        <p
                                            class="font-medium text-gray-900 dark:text-white"
                                        >
                                            {{ form.nombre }}
                                        </p>
                                        <p
                                            class="text-sm text-gray-500 dark:text-gray-400"
                                        >
                                            {{
                                                form.descripcion ||
                                                "Sin descripción"
                                            }}
                                        </p>
                                    </div>
                                    <div class="ml-auto">
                                        <span
                                            class="px-2 py-1 rounded-full text-xs font-medium"
                                            :class="
                                                form.estado === 'activo'
                                                    ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400'
                                                    : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400'
                                            "
                                        >
                                            {{
                                                form.estado === "activo"
                                                    ? "Activo"
                                                    : "Inactivo"
                                            }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Acciones del formulario -->
                    <div
                        class="px-6 py-4 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row gap-3 justify-end"
                    >
                        <Link
                            :href="route('categorias.index')"
                            class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200 text-center"
                        >
                            Cancelar
                        </Link>
                        <button
                            type="submit"
                            :disabled="enviando"
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
                            {{
                                enviando ? "Guardando..." : "Guardar categoría"
                            }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Toast de notificación -->
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
import { ref, reactive } from "vue";
import { Link, router } from "@inertiajs/vue3";
import axios from "axios";
import AppLayout from "@/Layouts/AppLayout.vue";

// Estado del formulario
const form = reactive({
    nombre: "",
    estado: "activo",
    descripcion: "",
});

const errors = reactive({});
const enviando = ref(false);
const toast = ref({
    show: false,
    message: "",
    type: "success",
});

// Mostrar notificación
const mostrarToast = (message, type = "success") => {
    toast.value = {
        show: true,
        message,
        type,
    };
    setTimeout(() => {
        toast.value.show = false;
    }, 3000);
};

// Guardar categoría
const guardarCategoria = async () => {
    enviando.value = true;

    // Limpiar errores anteriores
    Object.keys(errors).forEach((key) => delete errors[key]);

    try {
        const response = await axios.post("/categorias", form);

        if (response.data.success) {
            mostrarToast(response.data.message, "success");

            // Redirigir al listado después de 1 segundo
            setTimeout(() => {
                router.visit(route("categorias.index"));
            }, 1000);
        }
    } catch (error) {
        if (error.response?.status === 422) {
            // Errores de validación
            const validationErrors = error.response.data.errors;
            Object.keys(validationErrors).forEach((key) => {
                errors[key] = validationErrors[key][0];
            });
            mostrarToast(
                "Por favor, corrige los errores del formulario",
                "error",
            );
        } else {
            mostrarToast(
                "Error al guardar la categoría. Intenta nuevamente.",
                "error",
            );
        }
    } finally {
        enviando.value = false;
    }
};
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
