<template>
    <AppLayout title="Crear Tutorial">
        <template #header>
            <div
                class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4"
            >
                <div>
                    <h2
                        class="text-2xl font-bold text-gray-800 dark:text-white"
                    >
                        Crear Nuevo Tutorial
                    </h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        Completa el formulario para agregar un nuevo tutorial
                    </p>
                </div>

                <div class="flex gap-3">
                    <Link
                        :href="route('recursos.index')"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg"
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
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden"
            >
                <form @submit.prevent="crearTutorial">
                    <div class="p-6 space-y-6">
                        <!-- ============================================================ -->
                        <!-- TÍTULO -->
                        <!-- ============================================================ -->
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                            >
                                Título *
                            </label>
                            <input
                                v-model="form.titulo"
                                type="text"
                                maxlength="100"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                :class="{ 'border-red-500': errors.titulo }"
                                placeholder="Ej: Introducción a Laravel"
                                @blur="validarCampo('titulo')"
                            />
                            <p
                                v-if="errors.titulo"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ errors.titulo[0] }}
                            </p>
                            <p class="mt-1 text-xs text-gray-500">
                                Mínimo 3 caracteres, máximo 100
                            </p>
                        </div>

                        <!-- ============================================================ -->
                        <!-- DESCRIPCIÓN (opcional) -->
                        <!-- ============================================================ -->
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                            >
                                Descripción
                            </label>
                            <textarea
                                v-model="form.descripcion"
                                rows="4"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 resize-none"
                                :class="{
                                    'border-red-500': errors.descripcion,
                                }"
                                placeholder="Describe el contenido del tutorial..."
                            ></textarea>
                            <p
                                v-if="errors.descripcion"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ errors.descripcion[0] }}
                            </p>
                        </div>

                        <!-- ============================================================ -->
                        <!-- TIPO DE MATERIAL Y FORMATO -->
                        <!-- ============================================================ -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Tipo de Material -->
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                                >
                                    Tipo de Material *
                                </label>
                                <select
                                    v-model="form.tipo_material"
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                    :class="{
                                        'border-red-500': errors.tipo_material,
                                    }"
                                    @change="validarCampo('tipo_material')"
                                >
                                    <option value="">Seleccione un tipo</option>
                                    <option value="video">Video</option>
                                    <option value="manual">Manual</option>
                                    <option value="guia">Guía</option>
                                    <option value="post">Post</option>
                                    <option value="triptico">Infografia</option>
                                    <option value="avisos importantes">
                                        Avisos Importantes
                                    </option>
                                </select>
                                <p
                                    v-if="errors.tipo_material"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ errors.tipo_material[0] }}
                                </p>
                            </div>

                            <!-- Formato -->
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                                >
                                    Formato *
                                </label>
                                <select
                                    v-model="form.formato"
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                    :class="{
                                        'border-red-500': errors.formato,
                                    }"
                                    @change="validarCampo('formato')"
                                >
                                    <option value="">
                                        Seleccione un formato
                                    </option>
                                    <option value="mp4">MP4</option>
                                    <option value="pdf">PDF</option>
                                    <option value="word">Word</option>
                                </select>
                                <p
                                    v-if="errors.formato"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ errors.formato[0] }}
                                </p>
                            </div>
                        </div>

                        <!-- ============================================================ -->
                        <!-- CATEGORÍA Y SUBCATEGORÍA -->
                        <!-- ============================================================ -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Categoría (opcional) -->
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                                >
                                    Categoría
                                </label>
                                <select
                                    v-model="form.categorias_id"
                                    @change="onCategoriaChange"
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                    :class="{
                                        'border-red-500': errors.categorias_id,
                                    }"
                                >
                                    <option :value="null">
                                        Seleccione una categoría
                                    </option>
                                    <option
                                        v-for="cat in categorias"
                                        :key="cat.id"
                                        :value="cat.id"
                                    >
                                        {{ cat.nombre }}
                                    </option>
                                </select>
                                <p
                                    v-if="errors.categorias_id"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ errors.categorias_id[0] }}
                                </p>
                            </div>

                            <!-- Subcategoría (opcional) -->
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                                >
                                    Subcategoría
                                </label>
                                <select
                                    v-model="form.subcategoria_id"
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                    :class="{
                                        'border-red-500':
                                            errors.subcategoria_id,
                                    }"
                                    :disabled="!form.categorias_id"
                                >
                                    <option :value="null">
                                        {{
                                            !form.categorias_id
                                                ? "Primero seleccione una categoría"
                                                : "Seleccione una subcategoría"
                                        }}
                                    </option>
                                    <option
                                        v-for="sub in subcategoriasFiltradas"
                                        :key="sub.id"
                                        :value="sub.id"
                                    >
                                        {{ sub.nombre }}
                                    </option>
                                </select>
                                <p
                                    v-if="errors.subcategoria_id"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ errors.subcategoria_id[0] }}
                                </p>
                                <p
                                    v-if="
                                        form.categorias_id &&
                                        subcategoriasFiltradas.length === 0
                                    "
                                    class="mt-1 text-xs text-amber-600"
                                >
                                    Esta categoría no tiene subcategorías
                                </p>
                            </div>
                        </div>

                        <!-- ============================================================ -->
                        <!-- ALCANCE Y ESTADO -->
                        <!-- ============================================================ -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Alcance -->
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                                >
                                    Alcance *
                                </label>

                                <select
                                    v-model="form.alcance"
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                    :class="{
                                        'border-red-500': errors.alcance,
                                    }"
                                    @change="validarCampo('alcance')"
                                >
                                    <option value="">
                                        Seleccione un alcance
                                    </option>
                                    <option value="Superadmin we collab">
                                        Superadmin we collab
                                    </option>
                                    <option value="Admin we collab">
                                        Admin we collab
                                    </option>
                                    <option value="Soporte we collab">
                                        Soporte we collab
                                    </option>
                                    <option value="Usuario we collab">
                                        Usuario we collab
                                    </option>
                                    <option value="Suscriptor SLC">
                                        Suscriptor SLC
                                    </option>
                                    <option value="Usuario Admin SLC">
                                        Usuario Admin SLC
                                    </option>
                                    <option value="Usuario Premium SLC">
                                        Usuario Premium SLC
                                    </option>
                                    <option value="Usuario Público">
                                        Usuario Público
                                    </option>
                                    <option value="Prospecto">Prospecto</option>
                                </select>

                                <p
                                    v-if="errors.alcance"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ errors.alcance[0] }}
                                </p>
                            </div>

                            <!-- Estado -->
                            <div>
                                <label
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
                                            class="form-radio text-indigo-600"
                                        />
                                        <span class="ml-2">Activo</span>
                                    </label>

                                    <label class="inline-flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.estado"
                                            value="inactivo"
                                            class="form-radio text-indigo-600"
                                        />
                                        <span class="ml-2">Inactivo</span>
                                    </label>
                                </div>

                                <p
                                    v-if="errors.estado"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ errors.estado[0] }}
                                </p>
                            </div>
                        </div>

                        <!-- ============================================================ -->
                        <!-- URL (opcional) -->
                        <!-- ============================================================ -->
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                            >
                                URL del contenido
                            </label>
                            <input
                                v-model="form.url"
                                type="url"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                :class="{ 'border-red-500': errors.url }"
                                placeholder="https://ejemplo.com/tutorial"
                                @blur="validarCampo('url')"
                            />
                            <p
                                v-if="errors.url"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ errors.url[0] }}
                            </p>
                            <p class="mt-1 text-xs text-gray-500">
                                Debe ser una URL válida (http:// o https://)
                            </p>

                            <!-- Preview de YouTube -->
                            <div v-if="previewUrl" class="mt-3">
                                <p class="text-sm text-gray-500 mb-2">
                                    Vista previa:
                                </p>
                                <img
                                    :src="previewUrl"
                                    class="w-48 h-28 object-cover rounded-lg shadow"
                                />
                            </div>
                        </div>

                        <!-- ============================================================ -->
                        <!-- OBSERVACIÓN (opcional) -->
                        <!-- ============================================================ -->
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                            >
                                Observación
                                <span class="text-xs text-gray-500 ml-1"
                                    >(solo visible para admins)</span
                                >
                            </label>
                            <textarea
                                v-model="form.observacion"
                                rows="2"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 resize-none"
                                placeholder="Notas internas para administradores..."
                            ></textarea>
                        </div>
                    </div>

                    <!-- ============================================================ -->
                    <!-- BOTONES -->
                    <!-- ============================================================ -->
                    <div
                        class="px-6 py-4 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700 flex justify-end gap-3"
                    >
                        <Link
                            :href="route('recursos.index')"
                            class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50"
                        >
                            Cancelar
                        </Link>
                        <button
                            type="submit"
                            :disabled="enviando"
                            class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg shadow-sm disabled:opacity-50 flex items-center gap-2"
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
                            {{ enviando ? "Guardando..." : "Crear Tutorial" }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Toast -->
        <div v-if="toast.show" class="fixed bottom-4 right-4 z-50">
            <div
                :class="
                    toast.type === 'success' ? 'bg-green-500' : 'bg-red-500'
                "
                class="text-white px-6 py-3 rounded-lg shadow-lg"
            >
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

// ============================================================
// FORMULARIO
// ============================================================
const form = reactive({
    titulo: "",
    descripcion: "",
    tipo_material: "",
    formato: "",
    categorias_id: null,
    subcategoria_id: null,
    alcance: "",
    estado: "activo",
    url: "",
    observacion: "",
});

const categorias = ref([]);
const subcategorias = ref([]);
const subcategoriasFiltradas = ref([]);
const enviando = ref(false);
const errors = reactive({});
const toast = ref({ show: false, message: "", type: "success" });

// ============================================================
// VALIDACIÓN DE CADA CAMPO
// ============================================================
const validarCampo = (campo) => {
    switch (campo) {
        case "titulo":
            const tituloLimpio = form.titulo ? form.titulo.trim() : "";
            if (!tituloLimpio) {
                errors.titulo = ["El título es obligatorio."];
            } else if (tituloLimpio.length < 3) {
                errors.titulo = ["El título debe tener al menos 3 caracteres."];
            } else if (tituloLimpio.length > 100) {
                errors.titulo = [
                    "El título no puede tener más de 100 caracteres.",
                ];
            } else {
                delete errors.titulo;
            }
            break;

        case "tipo_material":
            if (!form.tipo_material) {
                errors.tipo_material = [
                    "Debe seleccionar un tipo de material.",
                ];
            } else {
                delete errors.tipo_material;
            }
            break;

        case "formato":
            if (!form.formato) {
                errors.formato = ["Debe seleccionar un formato."];
            } else {
                delete errors.formato;
            }
            break;

        case "alcance":
            if (!form.alcance) {
                errors.alcance = ["Debe seleccionar un alcance."];
            } else {
                delete errors.alcance;
            }
            break;

        case "url":
            if (form.url && form.url.trim() !== "") {
                const urlPattern =
                    /^https?:\/\/(www\.)?[-a-zA-Z0-9@:%._\+~#=]{1,256}\.[a-zA-Z0-9()]{1,6}\b([-a-zA-Z0-9()@:%_\+.~#?&//=]*)$/;
                if (!urlPattern.test(form.url)) {
                    errors.url = ["La URL ingresada no es válida."];
                } else {
                    delete errors.url;
                }
            } else {
                delete errors.url;
            }
            break;
    }
};

// ============================================================
// VALIDACIÓN COMPLETA DEL FORMULARIO
// ============================================================
const validarFormularioCompleto = () => {
    // Validar todos los campos obligatorios
    validarCampo("titulo");
    validarCampo("tipo_material");
    validarCampo("formato");
    validarCampo("alcance");
    validarCampo("url");

    // Retornar true si no hay errores
    return Object.keys(errors).length === 0;
};

// ============================================================
// PREVIEW DE YOUTUBE
// ============================================================
const extractYouTubeId = (url) => {
    if (!url) return null;
    const patterns = [
        /youtu\.be\/([a-zA-Z0-9_-]{11})/,
        /[?&]v=([a-zA-Z0-9_-]{11})/,
        /embed\/([a-zA-Z0-9_-]{11})/,
        /shorts\/([a-zA-Z0-9_-]{11})/,
    ];
    for (const pattern of patterns) {
        const match = url.match(pattern);
        if (match && match[1]) return match[1];
    }
    return null;
};

const previewUrl = computed(() => {
    if (form.tipo_material !== "video") return null;
    const videoId = extractYouTubeId(form.url);
    if (videoId) return `https://img.youtube.com/vi/${videoId}/hqdefault.jpg`;
    return null;
});

// ============================================================
// NOTIFICACIONES
// ============================================================
const mostrarToast = (message, type = "success") => {
    toast.value = { show: true, message, type };
    setTimeout(() => {
        toast.value.show = false;
    }, 3000);
};

// ============================================================
// CARGAR CATEGORÍAS Y SUBCATEGORÍAS
// ============================================================
const cargarCategorias = async () => {
    try {
        const response = await axios.get("/categorias/lista");
        categorias.value =
            response.data?.data ||
            response.data?.categorias ||
            response.data ||
            [];
    } catch (error) {
        console.error("Error:", error);
        mostrarToast("Error al cargar las categorías", "error");
    }
};

const cargarSubcategorias = async () => {
    try {
        const response = await axios.get("/subcategorias/all");
        subcategorias.value =
            response.data?.data ||
            response.data?.subcategorias ||
            response.data ||
            [];
    } catch (error) {
        console.error("Error:", error);
        mostrarToast("Error al cargar las subcategorías", "error");
    }
};

// ============================================================
// FILTRAR SUBCATEGORÍAS POR CATEGORÍA
// ============================================================
const onCategoriaChange = () => {
    if (form.categorias_id) {
        subcategoriasFiltradas.value = subcategorias.value.filter(
            (sub) => sub.categoria_id === form.categorias_id,
        );
    } else {
        subcategoriasFiltradas.value = [];
    }
    form.subcategoria_id = null;
};

// ============================================================
// ENVIAR FORMULARIO
// ============================================================
const crearTutorial = async () => {
    enviando.value = true;

    // ✅ Validar todos los campos antes de enviar
    const esValido = validarFormularioCompleto();

    if (!esValido) {
        enviando.value = false;
        mostrarToast(
            "Por favor, complete todos los campos obligatorios",
            "error",
        );
        return;
    }

    try {
        const payload = {
            titulo: form.titulo.trim(),
            descripcion: form.descripcion || null,
            tipo_material: form.tipo_material,
            formato: form.formato,
            categorias_id: form.categorias_id || null,
            subcategoria_id: form.subcategoria_id || null,
            alcance: form.alcance,
            estado: form.estado,
            url: form.url || null,
            observacion: form.observacion || null,
        };

        console.log("📤 Payload enviado:", payload);

        const response = await axios.post("/tutoriales", payload);

        if (response.data.success) {
            mostrarToast(response.data.message, "success");
            setTimeout(() => router.visit(route("recursos.index")), 1000);
        }
    } catch (error) {
        console.error("❌ Error:", error.response?.data);
        if (error.response?.status === 422) {
            // ✅ Mostrar errores del backend
            Object.assign(errors, error.response.data.errors);
            mostrarToast(
                "Por favor, corrija los errores del formulario",
                "error",
            );
        } else {
            mostrarToast(
                error.response?.data?.message || "Error al crear el tutorial",
                "error",
            );
        }
    } finally {
        enviando.value = false;
    }
};

// ============================================================
// LIFECYCLE
// ============================================================
onMounted(() => {
    cargarCategorias();
    cargarSubcategorias();
});
</script>
