<script setup>
import { ref, onMounted } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";

const page = usePage();
const form = ref({
    titulo: "",
    descripcion: "",
    tipo_material: "video",
    formato: "mp4",
    alcance: "soporte",
    estado: "activo",
    url: "",
    observacion: "",
    subcategoria_id: "",
    user_id: page.props.auth.user?.id || "", // Manejo seguro del user id
});

const subcategorias = ref([]);
const loading = ref(false);
const formErrors = ref({});
const apiError = ref(null);

const fetchSubcategorias = async () => {
    try {
        loading.value = true;
        const response = await axios.get("/subcategorias");

        // Validación y filtrado de subcategorías
        subcategorias.value = (response.data?.data || [])
            .filter((sub) => sub?.id && sub?.nombre)
            .map((sub) => ({
                id: sub.id,
                nombre: sub.nombre,
            }));
    } catch (err) {
        apiError.value = "Error al cargar las subcategorías";
        console.error("Error al cargar subcategorías:", err);
    } finally {
        loading.value = false;
    }
};

const submit = async () => {
    formErrors.value = {};
    apiError.value = null;
    loading.value = true;

    try {
        const response = await axios.post("/tutoriales", form.value);
        console.log("Respuesta del servidor:", response.data); // Ahora sí se usa `response`
        router.visit(route("tutoriales"), {
            preserveState: false,
            preserveScroll: true,
        });
    } catch (error) {
        if (error.response?.status === 422) {
            formErrors.value = error.response.data.errors || {};
        } else {
            apiError.value = "Error al guardar el tutorial";
            console.error("Error:", error);
        }
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    fetchSubcategorias();
});
</script>

<template>
    <AppLayout title="Registro de Tutoriales">
        <template #header>
            <h2 class="text-xl font-semibold text-gray-800">
                Registro de Tutorial
            </h2>
        </template>

        <div
            class="bg-ghostwhite overflow-hidden shadow-lg sm:rounded-2xl border-2 border-gray-300 mt-10"
        >
            <div class="p-6 bg-white border-b border-gray-300 rounded-2xl">
                <h3 class="text-lg font-semibold text-gray-700 mb-6">
                    Crear Nuevo Tutorial
                </h3>

                <!-- Mensajes de error -->
                <div
                    v-if="apiError"
                    class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 text-red-700"
                >
                    <p>{{ apiError }}</p>
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Columna 1 -->
                        <div class="space-y-4">
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700"
                                    >Título *</label
                                >
                                <input
                                    v-model="form.titulo"
                                    type="text"
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                    :class="{
                                        'border-red-500': formErrors.titulo,
                                    }"
                                    required
                                />
                                <span
                                    v-if="formErrors.titulo"
                                    class="text-sm text-red-600"
                                >
                                    {{ formErrors.titulo[0] }}
                                </span>
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700"
                                    >Descripción *</label
                                >
                                <textarea
                                    v-model="form.descripcion"
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                    :class="{
                                        'border-red-500':
                                            formErrors.descripcion,
                                    }"
                                    rows="3"
                                    required
                                ></textarea>
                                <span
                                    v-if="formErrors.descripcion"
                                    class="text-sm text-red-600"
                                >
                                    {{ formErrors.descripcion[0] }}
                                </span>
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700"
                                    >Tipo de Material *</label
                                >
                                <select
                                    v-model="form.tipo_material"
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                    :class="{
                                        'border-red-500':
                                            formErrors.tipo_material,
                                    }"
                                    required
                                >
                                    <option value="video">Video</option>
                                    <option value="manual">Manual</option>
                                    <option value="guia">Guía</option>
                                    <option value="post">Post</option>
                                    <option value="triptico">Tríptico</option>
                                </select>
                                <span
                                    v-if="formErrors.tipo_material"
                                    class="text-sm text-red-600"
                                >
                                    {{ formErrors.tipo_material[0] }}
                                </span>
                            </div>
                        </div>

                        <!-- Columna 2 -->
                        <div class="space-y-4">
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700"
                                    >Formato *</label
                                >
                                <select
                                    v-model="form.formato"
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                    :class="{
                                        'border-red-500': formErrors.formato,
                                    }"
                                    required
                                >
                                    <option value="mp4">MP4</option>
                                    <option value="pdf">PDF</option>
                                    <option value="word">Word</option>
                                </select>
                                <span
                                    v-if="formErrors.formato"
                                    class="text-sm text-red-600"
                                >
                                    {{ formErrors.formato[0] }}
                                </span>
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700"
                                    >Alcance *</label
                                >
                                <select
                                    v-model="form.alcance"
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                    :class="{
                                        'border-red-500': formErrors.alcance,
                                    }"
                                    required
                                >
                                    <option value="soporte">Soporte</option>
                                    <option value="admin">Administrador</option>
                                    <option value="clientefinal">
                                        Cliente Final
                                    </option>
                                </select>
                                <span
                                    v-if="formErrors.alcance"
                                    class="text-sm text-red-600"
                                >
                                    {{ formErrors.alcance[0] }}
                                </span>
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700"
                                    >Subcategoría *</label
                                >
                                <select
                                    v-model="form.subcategoria_id"
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                    :class="{
                                        'border-red-500':
                                            formErrors.subcategoria_id,
                                    }"
                                    required
                                    :disabled="loading"
                                >
                                    <option value="">
                                        Seleccione una subcategoría
                                    </option>
                                    <option
                                        v-for="subcategoria in subcategorias"
                                        :key="subcategoria.id"
                                        :value="subcategoria.id"
                                    >
                                        {{ subcategoria.nombre }}
                                    </option>
                                </select>
                                <span
                                    v-if="loading"
                                    class="text-sm text-gray-500"
                                >
                                    Cargando subcategorías...
                                </span>
                                <span
                                    v-if="formErrors.subcategoria_id"
                                    class="text-sm text-red-600"
                                >
                                    {{ formErrors.subcategoria_id[0] }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700"
                                >URL</label
                            >
                            <input
                                v-model="form.url"
                                type="url"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                :class="{
                                    'border-red-500': formErrors.url,
                                }"
                                placeholder="https://ejemplo.com"
                            />
                            <span
                                v-if="formErrors.url"
                                class="text-sm text-red-600"
                            >
                                {{ formErrors.url[0] }}
                            </span>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700"
                                >Estado *</label
                            >
                            <select
                                v-model="form.estado"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                :class="{
                                    'border-red-500': formErrors.estado,
                                }"
                                required
                            >
                                <option value="activo">Activo</option>
                                <option value="inactivo">Inactivo</option>
                            </select>
                            <span
                                v-if="formErrors.estado"
                                class="text-sm text-red-600"
                            >
                                {{ formErrors.estado[0] }}
                            </span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700"
                            >Observación</label
                        >
                        <textarea
                            v-model="form.observacion"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                            :class="{
                                'border-red-500': formErrors.observacion,
                            }"
                            rows="2"
                        ></textarea>
                        <span
                            v-if="formErrors.observacion"
                            class="text-sm text-red-600"
                        >
                            {{ formErrors.observacion[0] }}
                        </span>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button
                            type="button"
                            @click="router.visit(route('tutoriales.index'))"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                            :disabled="loading"
                        >
                            Cancelar
                        </button>
                        <button
                            type="submit"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                            :disabled="loading"
                        >
                            <span v-if="loading">
                                <svg
                                    class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
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
                                Guardando...
                            </span>
                            <span v-else>Guardar Tutorial</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
