<script setup>
import { ref, onMounted } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import axios from "axios"; // ✅ IMPORTANTE: agregar axios
import AppLayout from "@/Layouts/AppLayout.vue";

const page = usePage();

// 🎯 VALORES EXACTOS DE TUS ENUMS EN LA BD
const ENUMS = {
    TIPO_MATERIAL: ["video", "manual", "guia", "post", "triptico"],
    FORMATO: ["pdf", "word", "mp4"],
    ALCANCE: [
        "Superadmin We collab",
        "Admin We collab",
        "Suscriptor SLC",
        "Cliente Admin",
        "Cliente Premium",
        "Usuario Publico",
        "Prospecto",
    ],
    ESTADO: ["activo", "inactivo"],
};

// ✅ Formulario con valores por defecto seguros
const form = ref({
    titulo: "",
    descripcion: "",
    tipo_material: ENUMS.TIPO_MATERIAL[0], // ✅ "video" (primer valor válido)
    formato: ENUMS.FORMATO[2], // ✅ "mp4" (valor válido)
    alcance: "", // ✅ Forzar selección (no hay valor por defecto seguro)
    estado: ENUMS.ESTADO[0], // ✅ "activo"
    url: "",
    observacion: "",
    subcategoria_id: null, // ✅ null para foreign key
    // user_id: page.props.auth.user?.id ?? null, // ⏳ Pendiente si es requerido
});

const subcategorias = ref([]);
const loading = ref(false);
const formErrors = ref({});
const apiError = ref(null);

// 🔍 Validación frontend
const validateForm = () => {
    const errors = {};

    if (!form.value.titulo?.trim()) {
        errors.titulo = ["El título es requerido"];
    }

    if (!form.value.descripcion?.trim()) {
        errors.descripcion = ["La descripción es requerida"];
    }

    // ✅ Validar que los enums coincidan con los valores de la BD
    if (!ENUMS.TIPO_MATERIAL.includes(form.value.tipo_material)) {
        errors.tipo_material = ["Seleccione un tipo de material válido"];
    }

    if (!ENUMS.FORMATO.includes(form.value.formato)) {
        errors.formato = ["Seleccione un formato válido"];
    }

    if (!ENUMS.ALCANCE.includes(form.value.alcance)) {
        errors.alcance = ["Seleccione un alcance válido"];
    }

    if (!form.value.subcategoria_id) {
        errors.subcategoria_id = ["Debe seleccionar una subcategoría"];
    }

    if (form.value.url && !/^https?:\/\/.+/.test(form.value.url)) {
        errors.url = ["Ingrese una URL válida (http/https)"];
    }

    return errors;
};

const fetchSubcategorias = async () => {
    try {
        loading.value = true;
        const response = await axios.get("/subcategorias/all");

        subcategorias.value = (response.data?.data || response.data || [])
            .filter((sub) => sub?.id && sub?.nombre)
            .map((sub) => ({
                id: sub.id,
                nombre: sub.nombre,
            }));
    } catch (err) {
        apiError.value = "Error al cargar las subcategorías";
        console.error("❌ Error al cargar subcategorías:", err);
    } finally {
        loading.value = false;
    }
};

const submit = async () => {
    formErrors.value = {};
    apiError.value = null;

    // ✅ Validar antes de enviar
    const errors = validateForm();
    if (Object.keys(errors).length > 0) {
        formErrors.value = errors;
        return;
    }

    loading.value = true;

    try {
        // ✅ Payload limpio con valores exactos de la BD
        const payload = {
            titulo: form.value.titulo.trim(),
            descripcion: form.value.descripcion.trim(),
            tipo_material: form.value.tipo_material,
            formato: form.value.formato,
            alcance: form.value.alcance,
            estado: form.value.estado,
            url: form.value.url?.trim() || null,
            observacion: form.value.observacion?.trim() || null,
            subcategoria_id: form.value.subcategoria_id || null,
            // user_id: page.props.auth.user?.id ?? null, // ⏳ Agregar si es requerido
        };

        console.log("📤 Enviando tutorial:", payload);

        const response = await axios.post("/tutoriales", payload);

        console.log("✅ Tutorial creado:", response.data);

        // ✅ Redirección con Inertia (verifica el nombre de tu ruta)
        router.visit(route("tutoriales"), {
            preserveState: false,
            preserveScroll: true,
        });
    } catch (error) {
        if (error.response?.status === 422) {
            // ✅ Errores de validación del backend Laravel
            formErrors.value = error.response.data.errors || {};
        } else if (error.response?.status === 500) {
            apiError.value =
                error.response.data.message || "Error interno del servidor";
            console.error("❌ Error 500:", error.response.data);
        } else {
            apiError.value = "Error al guardar el tutorial";
            console.error("❌ Error:", error);
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

                <!-- ✅ Mensaje de error global -->
                <div
                    v-if="apiError"
                    class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded"
                >
                    <p>{{ apiError }}</p>
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- ===== COLUMNA 1 ===== -->
                        <div class="space-y-4">
                            <!-- Título -->
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700"
                                    >Título *</label
                                >
                                <input
                                    v-model="form.titulo"
                                    type="text"
                                    maxlength="100"
                                    required
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                    :class="{
                                        'border-red-500': formErrors.titulo,
                                    }"
                                    placeholder="Ej: Introducción a Vue 3"
                                />
                                <span
                                    v-if="formErrors.titulo"
                                    class="text-sm text-red-600"
                                >
                                    {{ formErrors.titulo[0] }}
                                </span>
                            </div>

                            <!-- Descripción -->
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700"
                                    >Descripción *</label
                                >
                                <textarea
                                    v-model="form.descripcion"
                                    rows="3"
                                    required
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                    :class="{
                                        'border-red-500':
                                            formErrors.descripcion,
                                    }"
                                    placeholder="Breve descripción del contenido..."
                                ></textarea>
                                <span
                                    v-if="formErrors.descripcion"
                                    class="text-sm text-red-600"
                                >
                                    {{ formErrors.descripcion[0] }}
                                </span>
                            </div>

                            <!-- Tipo de Material (ENUM: video, manual, guia, post, triptico) -->
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700"
                                    >Tipo de Material *</label
                                >
                                <select
                                    v-model="form.tipo_material"
                                    required
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                    :class="{
                                        'border-red-500':
                                            formErrors.tipo_material,
                                    }"
                                >
                                    <option disabled value="">
                                        Seleccione un tipo
                                    </option>
                                    <option
                                        v-for="val in ENUMS.TIPO_MATERIAL"
                                        :key="val"
                                        :value="val"
                                    >
                                        {{
                                            val.charAt(0).toUpperCase() +
                                            val.slice(1)
                                        }}
                                    </option>
                                </select>
                                <span
                                    v-if="formErrors.tipo_material"
                                    class="text-sm text-red-600"
                                >
                                    {{ formErrors.tipo_material[0] }}
                                </span>
                            </div>
                        </div>

                        <!-- ===== COLUMNA 2 ===== -->
                        <div class="space-y-4">
                            <!-- Formato (ENUM: pdf, word, mp4) -->
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700"
                                    >Formato *</label
                                >
                                <select
                                    v-model="form.formato"
                                    required
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                    :class="{
                                        'border-red-500': formErrors.formato,
                                    }"
                                >
                                    <option disabled value="">
                                        Seleccione un formato
                                    </option>
                                    <option
                                        v-for="val in ENUMS.FORMATO"
                                        :key="val"
                                        :value="val"
                                    >
                                        {{ val.toUpperCase() }}
                                    </option>
                                </select>
                                <span
                                    v-if="formErrors.formato"
                                    class="text-sm text-red-600"
                                >
                                    {{ formErrors.formato[0] }}
                                </span>
                            </div>

                            <!-- Alcance (ENUM: valores exactos de tu BD) -->
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700"
                                    >Alcance *</label
                                >
                                <select
                                    v-model="form.alcance"
                                    required
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                    :class="{
                                        'border-red-500': formErrors.alcance,
                                    }"
                                >
                                    <option disabled value="">
                                        Seleccione un alcance
                                    </option>
                                    <option
                                        v-for="val in ENUMS.ALCANCE"
                                        :key="val"
                                        :value="val"
                                    >
                                        {{ val }}
                                    </option>
                                </select>
                                <span
                                    v-if="formErrors.alcance"
                                    class="text-sm text-red-600"
                                >
                                    {{ formErrors.alcance[0] }}
                                </span>
                            </div>

                            <!-- Subcategoría (dinámica desde API) -->
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700"
                                    >Subcategoría *</label
                                >
                                <select
                                    v-model="form.subcategoria_id"
                                    required
                                    :disabled="loading"
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                    :class="{
                                        'border-red-500':
                                            formErrors.subcategoria_id,
                                    }"
                                >
                                    <option :value="null" disabled>
                                        {{
                                            loading
                                                ? "Cargando..."
                                                : "Seleccione una subcategoría"
                                        }}
                                    </option>
                                    <option
                                        v-for="sub in subcategorias"
                                        :key="sub.id"
                                        :value="sub.id"
                                    >
                                        {{ sub.nombre }}
                                    </option>
                                </select>
                                <span
                                    v-if="formErrors.subcategoria_id"
                                    class="text-sm text-red-600"
                                >
                                    {{ formErrors.subcategoria_id[0] }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- ===== FILA: URL + ESTADO ===== -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- URL -->
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700"
                                >URL</label
                            >
                            <input
                                v-model="form.url"
                                type="url"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                :class="{ 'border-red-500': formErrors.url }"
                                placeholder="https://ejemplo.com/recurso"
                            />
                            <span
                                v-if="formErrors.url"
                                class="text-sm text-red-600"
                            >
                                {{ formErrors.url[0] }}
                            </span>
                        </div>

                        <!-- Estado (ENUM: activo, inactivo) -->
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700"
                                >Estado *</label
                            >
                            <select
                                v-model="form.estado"
                                required
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                :class="{ 'border-red-500': formErrors.estado }"
                            >
                                <option
                                    v-for="val in ENUMS.ESTADO"
                                    :key="val"
                                    :value="val"
                                >
                                    {{
                                        val.charAt(0).toUpperCase() +
                                        val.slice(1)
                                    }}
                                </option>
                            </select>
                            <span
                                v-if="formErrors.estado"
                                class="text-sm text-red-600"
                            >
                                {{ formErrors.estado[0] }}
                            </span>
                        </div>
                    </div>

                    <!-- Observación -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700"
                            >Observación</label
                        >
                        <textarea
                            v-model="form.observacion"
                            rows="2"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                            :class="{
                                'border-red-500': formErrors.observacion,
                            }"
                            placeholder="Notas adicionales (opcional)..."
                        ></textarea>
                        <span
                            v-if="formErrors.observacion"
                            class="text-sm text-red-600"
                        >
                            {{ formErrors.observacion[0] }}
                        </span>
                    </div>

                    <!-- ===== BOTONES ===== -->
                    <div
                        class="flex justify-end space-x-3 pt-4 border-t border-gray-200"
                    >
                        <button
                            type="button"
                            @click="router.visit(route('tutoriales.index'))"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50"
                            :disabled="loading"
                        >
                            Cancelar
                        </button>
                        <button
                            type="submit"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                            :disabled="loading"
                        >
                            <span v-if="loading" class="flex items-center">
                                <svg
                                    class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
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
                            <span v-else>💾 Guardar Tutorial</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
