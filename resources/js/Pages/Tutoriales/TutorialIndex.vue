<script setup>
import { ref, computed, onMounted, watch } from "vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import { router } from "@inertiajs/vue3";
import axios from "axios";
import Swal from "sweetalert2";
import TutorialViewer from "./Components/TutorialViewer.vue";
import TutorialForm from "./Components/TutorialForm.vue";

defineProps({
    crearvideo: Boolean,
    editarvideo: Boolean,
    tutorialId: Number,
    permisos: {
        type: Array,
        default: () => [],
    },
});

// Función debounce
const debounce = (func, wait) => {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
};

// Variables reactivas
const isLoading = ref(false);
const tutoriales = ref([]);
const searchQuery = ref("");
const sortKey = ref("titulo");
const sortOrder = ref("asc");
const currentPage = ref(1);
const perPage = ref(10);
const totalItems = ref(0);
const lastPage = ref(1);
const selectedMaterialType = ref("todos");

// Modales
const showViewer = ref(false);
const showEditModal = ref(false);
const selectedTutorial = ref(null);
const currentTutorial = ref(null);

const fetchTutoriales = async () => {
    try {
        isLoading.value = true;
        const params = {
            search: searchQuery.value,
            tipo_material:
                selectedMaterialType.value === "todos"
                    ? null
                    : selectedMaterialType.value,
            sort_by: sortKey.value,
            sort_order: sortOrder.value,
            page: currentPage.value,
            per_page: perPage.value,
        };
        const response = await axios.get("/tutoriales", { params });
        tutoriales.value = response.data.data;
        currentPage.value = response.data.current_page;
        lastPage.value = response.data.last_page;
        perPage.value = response.data.per_page;
        totalItems.value = response.data.total;
    } catch (error) {
        console.error("Error al obtener los tutoriales:", error);
        Swal.fire({
            icon: "error",
            title: "Error",
            text: "No se pudieron cargar los tutoriales",
        });
    } finally {
        isLoading.value = false;
    }
};

const debouncedFetch = debounce(fetchTutoriales, 300);

const goToPage = (page) => {
    if (page >= 1 && page <= lastPage.value) {
        currentPage.value = page;
        fetchTutoriales();
    }
};

const goToCreateTutorial = async () => {
    try {
        await router.visit(route("CreateTutorial"), {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => {
                fetchTutoriales();
            },
        });
    } catch (error) {
        console.error("Error al navegar a crear tutorial:", error);
    }
};

const deleteTutorial = async (id) => {
    const result = await Swal.fire({
        icon: "warning",
        title: "¿Estás seguro?",
        text: "¿Deseas eliminar este tutorial?",
        showCancelButton: true,
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar",
        confirmButtonColor: "#dc3545",
    });
    if (result.isConfirmed) {
        try {
            await axios.delete(`/tutoriales/${id}`);
            await fetchTutoriales();
            Swal.fire({
                icon: "success",
                title: "Eliminado",
                text: "Tutorial eliminado correctamente",
                timer: 1500,
                showConfirmButton: false,
            });
        } catch (error) {
            console.error("Error al eliminar el tutorial:", error);
            Swal.fire({
                icon: "error",
                title: "Error",
                text:
                    error.response?.data?.message ||
                    "Error al eliminar el tutorial",
            });
        }
    }
};

const setMaterialType = (type) => {
    selectedMaterialType.value = type;
    currentPage.value = 1;
    fetchTutoriales();
};

const sortBy = (key) => {
    if (sortKey.value === key) {
        sortOrder.value = sortOrder.value === "asc" ? "desc" : "asc";
    } else {
        sortKey.value = key;
        sortOrder.value = "asc";
    }
    currentPage.value = 1;
    fetchTutoriales();
};

const viewTutorial = (id) => {
    selectedTutorial.value = tutoriales.value.find((t) => t.id === id);
    if (selectedTutorial.value) {
        showViewer.value = true;
    }
};

const editTutorial = (id) => {
    currentTutorial.value = tutoriales.value.find((t) => t.id === id);
    if (currentTutorial.value) {
        showEditModal.value = true;
    }
};

const handleViewerClose = () => {
    showViewer.value = false;
    selectedTutorial.value = null;
};

const handleFormSave = async () => {
    await fetchTutoriales();
    showEditModal.value = false;
};

const displayedTutoriales = computed(() => {
    return tutoriales.value || [];
});

watch(searchQuery, () => {
    currentPage.value = 1;
    debouncedFetch();
});

onMounted(() => {
    fetchTutoriales();
});
</script>

<template>
    <AppLayout title="Gestión de Tutoriales">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Gestión de Tutoriales
            </h2>
        </template>

        <div
            class="min-h-screen bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900 py-8 px-4 sm:px-6 lg:px-8"
        >
            <div class="max-w-7xl mx-auto">
                <!-- Header -->
                <div
                    class="mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4"
                >
                    <div>
                        <h1
                            class="text-4xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-purple-400 mb-2"
                        >
                            Biblioteca de Contenido
                        </h1>
                        <p class="text-gray-400 text-sm">
                            Explora y gestiona todos los tutoriales disponibles
                        </p>
                    </div>

                    <div class="flex items-center gap-3 w-full sm:w-auto">
                        <div class="relative w-full sm:w-80 group">
                            <input
                                type="text"
                                v-model="searchQuery"
                                placeholder="Buscar tutoriales..."
                                class="w-full pl-12 pr-4 py-3 bg-white/10 backdrop-blur-lg border border-white/20 rounded-2xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-cyan-400 focus:border-transparent transition-all duration-300 shadow-lg"
                            />
                            <svg
                                class="absolute left-4 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400 group-focus-within:text-cyan-400 transition-colors"
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                                />
                            </svg>
                        </div>

                        <button
                            @click="goToCreateTutorial"
                            class="bg-gradient-to-r from-cyan-500 to-blue-500 hover:from-cyan-600 hover:to-blue-600 text-white px-6 py-3 rounded-2xl transition-all duration-300 flex items-center gap-2 font-semibold shadow-lg hover:shadow-cyan-500/50 hover:scale-105 active:scale-95"
                            :disabled="isLoading"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 4v16m8-8H4"
                                />
                            </svg>
                            <span>Nuevo</span>
                        </button>
                    </div>
                </div>

                <!-- Loader -->
                <div
                    v-if="isLoading"
                    class="flex flex-col items-center justify-center py-20"
                >
                    <div class="relative">
                        <div
                            class="absolute inset-0 bg-cyan-400 blur-xl opacity-50 animate-pulse"
                        ></div>
                        <svg
                            class="relative animate-spin h-16 w-16 text-cyan-400"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
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
                    </div>
                    <span
                        class="text-cyan-400 mt-6 text-lg font-medium animate-pulse"
                        >Cargando contenido...</span
                    >
                </div>

                <!-- Filtros -->
                <div class="flex flex-wrap gap-3 mb-10" v-show="!isLoading">
                    <button
                        v-for="type in [
                            'todos',
                            'video',
                            'manual',
                            'guia',
                            'post',
                            'triptico',
                        ]"
                        :key="type"
                        @click="setMaterialType(type)"
                        :class="{
                            'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg shadow-cyan-500/50 scale-105':
                                selectedMaterialType === type,
                            'bg-white/10 backdrop-blur-md text-gray-300 hover:bg-white/20 hover:text-white border border-white/20':
                                selectedMaterialType !== type,
                        }"
                        class="px-6 py-3 rounded-2xl text-sm font-semibold transition-all duration-300 hover:scale-105 active:scale-95 capitalize"
                    >
                        {{ type }}
                    </button>
                </div>

                <!-- Grid de Cards -->
                <div
                    class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-6"
                    v-show="!isLoading"
                >
                    <div
                        v-for="tutorial in displayedTutoriales"
                        :key="tutorial.id"
                        class="group relative bg-gradient-to-br from-white/10 to-white/5 backdrop-blur-lg rounded-3xl overflow-hidden border border-white/20 shadow-2xl hover:shadow-cyan-500/30 transition-all duration-500 cursor-pointer transform hover:scale-105 hover:-translate-y-2"
                    >
                        <!-- Thumbnail -->
                        <div class="relative aspect-video overflow-hidden">
                            <div
                                class="absolute inset-0 bg-gradient-to-br from-purple-600/20 via-cyan-600/20 to-blue-600/20 group-hover:opacity-0 transition-opacity duration-500"
                            ></div>

                            <div
                                class="absolute inset-0 flex items-center justify-center"
                            >
                                <div
                                    class="text-6xl transform group-hover:scale-110 transition-transform duration-500 filter drop-shadow-2xl"
                                >
                                    <span
                                        v-if="
                                            tutorial.tipo_material === 'video'
                                        "
                                        >🎬</span
                                    >
                                    <span v-else-if="tutorial.formato === 'pdf'"
                                        >📄</span
                                    >
                                    <span
                                        v-else-if="tutorial.formato === 'word'"
                                        >📝</span
                                    >
                                    <span
                                        v-else-if="tutorial.formato === 'excel'"
                                        >📊</span
                                    >
                                    <span v-else>📁</span>
                                </div>
                            </div>

                            <div class="absolute top-3 right-3">
                                <span
                                    :class="{
                                        'bg-gradient-to-r from-green-400 to-emerald-500 shadow-lg shadow-green-500/50':
                                            tutorial.estado === 'activo',
                                        'bg-gradient-to-r from-red-400 to-rose-500 shadow-lg shadow-red-500/50':
                                            tutorial.estado !== 'activo',
                                    }"
                                    class="px-3 py-1 rounded-full text-[10px] font-bold text-white uppercase tracking-wider shadow-lg"
                                >
                                    {{ tutorial.estado }}
                                </span>
                            </div>

                            <!-- Overlay con Acciones -->
                            <div
                                class="absolute inset-0 bg-black/60 backdrop-blur-sm opacity-0 group-hover:opacity-100 transition-all duration-300 flex items-center justify-center gap-3"
                            >
                                <button
                                    @click.stop="viewTutorial(tutorial.id)"
                                    class="p-3 bg-gradient-to-r from-cyan-500 to-blue-500 rounded-full hover:from-cyan-600 hover:to-blue-600 transition-all duration-300 transform hover:scale-110 shadow-lg hover:shadow-cyan-500/50"
                                    title="Ver"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-5 w-5 text-white"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                                        />
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                                        />
                                    </svg>
                                </button>
                                <button
                                    @click.stop="editTutorial(tutorial.id)"
                                    class="p-3 bg-gradient-to-r from-purple-500 to-pink-500 rounded-full hover:from-purple-600 hover:to-pink-600 transition-all duration-300 transform hover:scale-110 shadow-lg hover:shadow-purple-500/50"
                                    title="Editar"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-5 w-5 text-white"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                                        />
                                    </svg>
                                </button>
                                <button
                                    @click.stop="deleteTutorial(tutorial.id)"
                                    class="p-3 bg-gradient-to-r from-red-500 to-rose-500 rounded-full hover:from-red-600 hover:to-rose-600 transition-all duration-300 transform hover:scale-110 shadow-lg hover:shadow-red-500/50"
                                    title="Eliminar"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-5 w-5 text-white"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5-4h4m-4 0a2 2 0 00-2 2h8a2 2 0 00-2-2m-4 0V3m0 4h4"
                                        />
                                    </svg>
                                </button>
                            </div>

                            <div
                                class="absolute inset-0 bg-gradient-to-tr from-transparent via-white/10 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000"
                            ></div>
                        </div>

                        <!-- Información -->
                        <div class="p-4">
                            <h4
                                class="text-white font-bold text-sm mb-2 line-clamp-2 group-hover:text-cyan-400 transition-colors duration-300"
                                :title="tutorial.titulo"
                            >
                                {{ tutorial.titulo }}
                            </h4>
                            <p
                                class="text-gray-400 text-xs mb-3 line-clamp-2"
                                :title="tutorial.descripcion"
                            >
                                {{ tutorial.descripcion }}
                            </p>

                            <div class="flex flex-wrap gap-2">
                                <span
                                    :class="{
                                        'bg-gradient-to-r from-red-500 to-pink-500':
                                            tutorial.tipo_material === 'video',
                                        'bg-gradient-to-r from-blue-500 to-cyan-500':
                                            tutorial.tipo_material === 'manual',
                                        'bg-gradient-to-r from-green-500 to-emerald-500':
                                            tutorial.tipo_material === 'guia',
                                        'bg-gradient-to-r from-purple-500 to-pink-500':
                                            tutorial.tipo_material === 'post',
                                        'bg-gradient-to-r from-yellow-500 to-orange-500':
                                            tutorial.tipo_material ===
                                            'triptico',
                                    }"
                                    class="px-2 py-1 rounded-lg text-[10px] text-white font-bold uppercase tracking-wide shadow-lg"
                                >
                                    {{ tutorial.tipo_material }}
                                </span>
                                <span
                                    class="px-2 py-1 rounded-lg text-[10px] bg-white/10 backdrop-blur-md text-gray-300 border border-white/20"
                                >
                                    {{ tutorial.formato }}
                                </span>
                            </div>
                        </div>

                        <div
                            class="absolute inset-0 rounded-3xl border-2 border-transparent group-hover:border-cyan-400/50 transition-colors duration-300 pointer-events-none"
                        ></div>
                    </div>

                    <div
                        v-if="displayedTutoriales.length === 0 && !isLoading"
                        class="col-span-full text-center py-20"
                    >
                        <div
                            class="inline-block p-8 rounded-full bg-white/5 backdrop-blur-lg border border-white/10 mb-6"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-16 w-16 text-gray-400"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                />
                            </svg>
                        </div>
                        <p class="text-gray-400 text-xl font-medium mb-2">
                            No se encontraron tutoriales
                        </p>
                        <p class="text-gray-500 text-sm">
                            Intenta con otros filtros de búsqueda
                        </p>
                    </div>
                </div>

                <!-- Paginación -->
                <div
                    class="flex justify-between items-center mt-12 pt-8 border-t border-white/10"
                    v-if="!isLoading && totalItems > 0"
                >
                    <div class="text-gray-400 text-sm">
                        Mostrando
                        <span class="text-cyan-400 font-semibold">{{
                            (currentPage - 1) * perPage + 1
                        }}</span>
                        -
                        <span class="text-cyan-400 font-semibold">{{
                            Math.min(currentPage * perPage, totalItems)
                        }}</span>
                        de
                        <span class="text-cyan-400 font-semibold">{{
                            totalItems
                        }}</span>
                    </div>
                    <div class="flex gap-2">
                        <button
                            @click="goToPage(currentPage - 1)"
                            :disabled="currentPage === 1"
                            class="px-4 py-2 bg-white/10 backdrop-blur-md text-gray-300 rounded-xl hover:bg-white/20 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-300 border border-white/20 hover:border-cyan-400/50"
                        >
                            Anterior
                        </button>
                        <button
                            v-for="page in Math.min(5, lastPage)"
                            :key="page"
                            @click="goToPage(page)"
                            :class="{
                                'px-4 py-2 rounded-xl transition-all duration-300 border': true,
                                'bg-gradient-to-r from-cyan-500 to-blue-500 text-white border-transparent shadow-lg shadow-cyan-500/50 scale-105':
                                    currentPage === page,
                                'bg-white/10 backdrop-blur-md text-gray-300 border-white/20 hover:bg-white/20 hover:border-cyan-400/50':
                                    currentPage !== page,
                            }"
                        >
                            {{ page }}
                        </button>
                        <button
                            @click="goToPage(currentPage + 1)"
                            :disabled="currentPage === lastPage"
                            class="px-4 py-2 bg-white/10 backdrop-blur-md text-gray-300 rounded-xl hover:bg-white/20 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-300 border border-white/20 hover:border-cyan-400/50"
                        >
                            Siguiente
                        </button>
                    </div>
                </div>
            </div>

            <!-- Componentes Modales -->
            <TutorialViewer
                v-if="showViewer && selectedTutorial"
                :show="showViewer"
                :tutorial="selectedTutorial"
                @close="handleViewerClose"
            />

            <TutorialForm
                v-if="showEditModal && currentTutorial"
                :show="showEditModal"
                :tutorial="currentTutorial"
                @close="showEditModal = false"
                @saved="handleFormSave"
            />
        </div>
    </AppLayout>
</template>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.backdrop-blur-lg {
    backdrop-filter: blur(10px);
}

.backdrop-blur-md {
    backdrop-filter: blur(8px);
}

::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 10px;
}

::-webkit-scrollbar-thumb {
    background: linear-gradient(180deg, #06b6d4, #3b82f6);
    border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(180deg, #0891b2, #2563eb);
}
</style>
