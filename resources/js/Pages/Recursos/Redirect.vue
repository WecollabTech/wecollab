<script setup>
import { ref, onMounted } from "vue";
import { router } from "@inertiajs/vue3";
import axios from "axios";
import AppLayout from "@/Layouts/AppLayout.vue";

const props = defineProps({
    id: { type: [String, Number], required: true },
});

const loading = ref(true);
const error = ref(null);
const resource = ref(null);
let redirectTimer = null;

const getTipoRoute = (tipo) => {
    const tipoRoutes = {
        video: "videos",
        manual: "manuales",
        guia: "guias",
        post: "posts",
        triptico: "tripticos",
        "avisos importantes": "avisos",
    };
    return tipoRoutes[tipo] || "videos";
};

const loadAndRedirect = async () => {
    loading.value = true;
    error.value = null;

    try {
        const response = await axios.get(`/recursos/${props.id}`);

        if (
            typeof response.data === "string" &&
            response.data.includes("<!DOCTYPE html>")
        ) {
            error.value =
                "Error: La API está devolviendo HTML en lugar de JSON.";
            loading.value = false;
            return;
        }

        resource.value = response.data;

        redirectTimer = setTimeout(() => {
            const tipoRoute = getTipoRoute(resource.value.tipo_material);
            router.visit(`/recursos/${tipoRoute}/${resource.value.id}`);
        }, 1500);
    } catch (err) {
        console.error("Error al cargar recurso:", err);

        if (err.response) {
            if (err.response.status === 404) {
                error.value =
                    "El recurso que buscas no existe o ha sido eliminado";
            } else if (err.response.status === 500) {
                error.value =
                    "Error en el servidor. Por favor, intenta más tarde";
            } else {
                error.value = `Error ${err.response.status}: No se pudo cargar el recurso`;
            }
        } else if (err.request) {
            error.value =
                "No se pudo conectar con el servidor. Verifica tu conexión a internet";
        } else {
            error.value =
                "Error al cargar el recurso. Por favor, intenta de nuevo";
        }
    } finally {
        loading.value = false;
    }
};

const goToResource = () => {
    if (resource.value) {
        if (redirectTimer) clearTimeout(redirectTimer);
        const tipoRoute = getTipoRoute(resource.value.tipo_material);
        router.visit(`/recursos/${tipoRoute}/${resource.value.id}`);
    }
};

const goBack = () => {
    if (redirectTimer) clearTimeout(redirectTimer);
    router.visit("/recursos");
};

onMounted(() => {
    loadAndRedirect();
});
</script>

<template>
    <AppLayout :title="'Redirigiendo...'">
        <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none">
            <div
                class="absolute -top-40 -right-40 h-96 w-96 bg-gradient-to-br from-indigo-400/30 to-purple-400/30 rounded-full blur-3xl animate-pulse"
            ></div>
            <div
                class="absolute top-1/3 -left-32 h-72 w-72 bg-gradient-to-br from-blue-400/25 to-cyan-400/25 rounded-full blur-3xl animate-pulse"
                style="animation-delay: 1s"
            ></div>
            <div
                class="absolute -bottom-32 right-1/4 h-80 w-80 bg-gradient-to-br from-pink-400/25 to-rose-400/25 rounded-full blur-3xl animate-pulse"
                style="animation-delay: 2s"
            ></div>
        </div>

        <div class="mx-auto max-w-7xl px-4 pt-2 pb-10 sm:px-6 lg:px-8">
            <div class="flex flex-col items-center justify-center min-h-[70vh]">
                <div v-if="error" class="text-center max-w-2xl">
                    <div class="relative mx-auto inline-block mb-6">
                        <div
                            class="absolute -inset-4 rounded-full bg-gradient-to-r from-red-400/20 to-red-600/20 blur-2xl"
                        ></div>
                        <div
                            class="relative mx-auto flex h-28 w-28 items-center justify-center rounded-full bg-gradient-to-br from-red-100 to-red-200 shadow-xl"
                        >
                            <svg
                                class="h-14 w-14 text-red-500"
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
                        </div>
                    </div>
                    <h3
                        class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-2"
                    >
                        Error al cargar
                    </h3>
                    <p
                        class="text-gray-600 dark:text-gray-400 mb-6 whitespace-pre-line"
                    >
                        {{ error }}
                    </p>
                    <div class="flex gap-3 justify-center">
                        <button
                            @click="loadAndRedirect"
                            class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-semibold hover:shadow-lg transition-all"
                        >
                            Reintentar
                        </button>
                        <button
                            @click="goBack"
                            class="px-6 py-2.5 rounded-xl border border-gray-300 text-gray-700 hover:bg-gray-100 transition-all"
                        >
                            Volver
                        </button>
                    </div>
                </div>

                <div v-else-if="loading" class="text-center">
                    <div class="relative mb-8">
                        <div
                            class="absolute -inset-4 rounded-full bg-gradient-to-r from-indigo-400/20 to-purple-400/20 blur-2xl animate-pulse"
                        ></div>
                        <div
                            class="relative mx-auto flex h-28 w-28 items-center justify-center rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 shadow-xl"
                        >
                            <svg
                                class="h-14 w-14 text-white animate-spin"
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
                        </div>
                    </div>
                    <h3
                        class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-2"
                    >
                        Cargando recurso
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">
                        Preparando la redirección...
                    </p>
                    <div
                        class="w-64 mx-auto h-2 bg-gray-200 rounded-full overflow-hidden"
                    >
                        <div
                            class="h-full bg-gradient-to-r from-indigo-500 to-purple-600 rounded-full animate-progress"
                        ></div>
                    </div>
                </div>

                <div v-else class="text-center">
                    <div class="relative mx-auto inline-block mb-6">
                        <div
                            class="absolute -inset-4 rounded-full bg-gradient-to-r from-emerald-400/20 to-green-400/20 blur-2xl"
                        ></div>
                        <div
                            class="relative mx-auto flex h-28 w-28 items-center justify-center rounded-full bg-gradient-to-br from-emerald-500 to-green-600 shadow-xl"
                        >
                            <svg
                                class="h-14 w-14 text-white"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.102m3.172-3.172a4 4 0 00-5.656-5.656l-1.102 1.102"
                                />
                            </svg>
                        </div>
                    </div>
                    <h3
                        class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-2"
                    >
                        Redirigiendo
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">
                        Serás redirigido a:
                        <strong class="text-indigo-600">{{
                            resource?.titulo
                        }}</strong>
                    </p>
                    <div class="flex flex-wrap gap-2 justify-center mb-6">
                        <span
                            class="px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-700"
                            >{{ resource?.tipo_material }}</span
                        >
                        <span
                            class="px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-700"
                            >{{ resource?.formato }}</span
                        >
                    </div>
                    <div class="flex gap-3 justify-center">
                        <button
                            @click="goToResource"
                            class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-semibold hover:shadow-lg transition-all"
                        >
                            Ir ahora
                        </button>
                        <button
                            @click="goBack"
                            class="px-6 py-2.5 rounded-xl border border-gray-300 text-gray-700 hover:bg-gray-100 transition-all"
                        >
                            Volver
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
@keyframes progress {
    0% {
        transform: translateX(-100%);
    }
    100% {
        transform: translateX(100%);
    }
}
.animate-progress {
    animation: progress 1.5s ease-in-out infinite;
}
</style>
