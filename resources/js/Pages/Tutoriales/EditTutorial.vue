<script setup>
import { ref } from "vue";
import { router } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";

const props = defineProps({
    tutorial: {
        type: Object,
        required: true,
    },
    subcategorias: {
        type: Array,
        required: true,
    },
    users: {
        type: Array,
        required: true,
    },
    errors: Object,
});

// ✅ Mismos cambios que en CreateTutorial.vue
const form = ref({
    id: props.tutorial.id,
    titulo: props.tutorial.titulo || "",
    descripcion: props.tutorial.descripcion || "",
    url: props.tutorial.url || null,

    // ✅ Valores actualizados según tu BD (igual que en Create)
    tipo_material: props.tutorial.tipo_material || "video",
    formato: props.tutorial.formato || "mp4",
    alcance: props.tutorial.alcance || "Superadministrador",
    estado: props.tutorial.estado || "activo",

    observacion: props.tutorial.observacion || null,

    // ✅ null para foreign keys (no string vacío) - igual que en Create
    subcategoria_id: props.tutorial.subcategoria_id ?? null,

    // ⏳ user_id: pendiente (igual que en CreateTutorial)
    user_id: props.tutorial.user_id ?? null,
});

const isLoading = ref(false);

const submit = () => {
    isLoading.value = true;

    // ✅ Preparar datos: asegurar null en foreign keys (igual que en Create)
    const payload = {
        ...form.value,
        subcategoria_id: form.value.subcategoria_id || null,
        user_id: form.value.user_id || null,
        url: form.value.url || null,
        observacion: form.value.observacion || null,
    };

    router.put(route("tutoriales.update", form.value.id), payload, {
        preserveScroll: true,
        onSuccess: () => {
            router.visit(route("tutoriales.index"));
        },
        onError: (errors) => {
            console.error("Errores de validación:", errors);
        },
        onFinish: () => {
            isLoading.value = false;
        },
    });
};
</script>

<template>
    <AppLayout title="Editar Tutorial">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Editar Tutorial: {{ form.titulo }}
                </h2>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div
                    class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6"
                >
                    <div
                        v-if="$page.props.flash?.success"
                        class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded"
                    >
                        {{ $page.props.flash.success }}
                    </div>

                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- ============ COLUMNA 1 ============ -->
                            <div class="space-y-4">
                                <!-- Título -->
                                <div>
                                    <label
                                        class="block font-medium text-sm text-gray-700"
                                        >Título *</label
                                    >
                                    <input
                                        v-model="form.titulo"
                                        type="text"
                                        required
                                        maxlength="100"
                                        class="mt-1 block w-full border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 rounded-md shadow-sm"
                                        :class="{
                                            'border-red-500': errors.titulo,
                                        }"
                                    />
                                    <!-- ✅ Acceso a [0] como en CreateTutorial -->
                                    <span
                                        v-if="errors.titulo"
                                        class="text-sm text-red-600"
                                    >
                                        {{ errors.titulo[0] }}
                                    </span>
                                </div>

                                <!-- Subcategoría -->
                                <div>
                                    <label
                                        class="block font-medium text-sm text-gray-700"
                                        >Subcategoría *</label
                                    >
                                    <select
                                        v-model="form.subcategoria_id"
                                        required
                                        class="mt-1 block w-full border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 rounded-md shadow-sm bg-white"
                                        :class="{
                                            'border-red-500':
                                                errors.subcategoria_id,
                                        }"
                                    >
                                        <!-- ✅ :value="null" igual que en CreateTutorial -->
                                        <option :value="null" disabled>
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
                                        v-if="errors.subcategoria_id"
                                        class="text-sm text-red-600"
                                    >
                                        {{ errors.subcategoria_id[0] }}
                                    </span>
                                </div>

                                <!-- ⏳ Responsable (user_id) - Pendiente (igual que en Create) -->
                                <div>
                                    <label
                                        class="block font-medium text-sm text-gray-700"
                                    >
                                        Responsable
                                        <span class="text-gray-400"
                                            >(opcional)</span
                                        >
                                    </label>
                                    <select
                                        v-model="form.user_id"
                                        class="mt-1 block w-full border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 rounded-md shadow-sm bg-white"
                                    >
                                        <option :value="null">
                                            Sin asignar
                                        </option>
                                        <option
                                            v-for="user in users"
                                            :key="user.id"
                                            :value="user.id"
                                        >
                                            {{ user.name }}
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <!-- ============ COLUMNA 2 ============ -->
                            <div class="space-y-4">
                                <!-- Tipo de Material ✅ Mismos valores que en CreateTutorial -->
                                <div>
                                    <label
                                        class="block font-medium text-sm text-gray-700"
                                        >Tipo de Material *</label
                                    >
                                    <select
                                        v-model="form.tipo_material"
                                        required
                                        class="mt-1 block w-full border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 rounded-md shadow-sm bg-white"
                                        :class="{
                                            'border-red-500':
                                                errors.tipo_material,
                                        }"
                                    >
                                        <option value="video">Video</option>
                                        <option value="manual">Manual</option>
                                        <option value="guia">Guía</option>
                                        <option value="post">Post</option>
                                        <option value="triptico">
                                            Tríptico
                                        </option>
                                    </select>
                                    <span
                                        v-if="errors.tipo_material"
                                        class="text-sm text-red-600"
                                    >
                                        {{ errors.tipo_material[0] }}
                                    </span>
                                </div>

                                <!-- Formato ✅ Mismos valores que en CreateTutorial -->
                                <div>
                                    <label
                                        class="block font-medium text-sm text-gray-700"
                                        >Formato *</label
                                    >
                                    <select
                                        v-model="form.formato"
                                        required
                                        class="mt-1 block w-full border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 rounded-md shadow-sm bg-white"
                                        :class="{
                                            'border-red-500': errors.formato,
                                        }"
                                    >
                                        <option value="mp4">MP4</option>
                                        <option value="pdf">PDF</option>
                                        <option value="word">Word</option>
                                    </select>
                                    <span
                                        v-if="errors.formato"
                                        class="text-sm text-red-600"
                                    >
                                        {{ errors.formato[0] }}
                                    </span>
                                </div>

                                <!-- URL -->
                                <div>
                                    <label
                                        class="block font-medium text-sm text-gray-700"
                                        >URL del contenido</label
                                    >
                                    <input
                                        v-model="form.url"
                                        type="url"
                                        class="mt-1 block w-full border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 rounded-md shadow-sm"
                                        :class="{
                                            'border-red-500': errors.url,
                                        }"
                                        placeholder="https://ejemplo.com"
                                    />
                                    <span
                                        v-if="errors.url"
                                        class="text-sm text-red-600"
                                    >
                                        {{ errors.url[0] }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- ============ FILA: ALCANCE y ESTADO ============ -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- ✅ Alcance: Mismos valores que en CreateTutorial -->
                            <div>
                                <label
                                    class="block font-medium text-sm text-gray-700"
                                    >Alcance *</label
                                >
                                <select
                                    v-model="form.alcance"
                                    required
                                    class="mt-1 block w-full border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 rounded-md shadow-sm bg-white"
                                    :class="{
                                        'border-red-500': errors.alcance,
                                    }"
                                >
                                    <option value="Superadministrador">
                                        Superadministrador
                                    </option>
                                    <option value="Administrador">
                                        Administrador
                                    </option>
                                    <option value="ClienteAdmin">
                                        Cliente Admin
                                    </option>
                                    <option value="ClienteSuscriptor">
                                        Cliente Suscriptor
                                    </option>
                                    <option value="UsuarioPúblico">
                                        Usuario Público
                                    </option>
                                    <option value="Prospecto">Prospecto</option>
                                </select>
                                <span
                                    v-if="errors.alcance"
                                    class="text-sm text-red-600"
                                >
                                    {{ errors.alcance[0] }}
                                </span>
                            </div>

                            <!-- Estado -->
                            <div>
                                <label
                                    class="block font-medium text-sm text-gray-700"
                                    >Estado *</label
                                >
                                <select
                                    v-model="form.estado"
                                    required
                                    class="mt-1 block w-full border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 rounded-md shadow-sm bg-white"
                                    :class="{ 'border-red-500': errors.estado }"
                                >
                                    <option value="activo">Activo</option>
                                    <option value="inactivo">Inactivo</option>
                                </select>
                                <span
                                    v-if="errors.estado"
                                    class="text-sm text-red-600"
                                >
                                    {{ errors.estado[0] }}
                                </span>
                            </div>
                        </div>

                        <!-- Observaciones -->
                        <div>
                            <label
                                class="block font-medium text-sm text-gray-700"
                                >Observaciones</label
                            >
                            <textarea
                                v-model="form.observacion"
                                rows="2"
                                class="mt-1 block w-full border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 rounded-md shadow-sm resize-y"
                                :class="{
                                    'border-red-500': errors.observacion,
                                }"
                            ></textarea>
                            <span
                                v-if="errors.observacion"
                                class="text-sm text-red-600"
                            >
                                {{ errors.observacion[0] }}
                            </span>
                        </div>

                        <!-- Descripción -->
                        <div>
                            <label
                                class="block font-medium text-sm text-gray-700"
                                >Descripción *</label
                            >
                            <textarea
                                v-model="form.descripcion"
                                rows="4"
                                required
                                class="mt-1 block w-full border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 rounded-md shadow-sm resize-y"
                                :class="{
                                    'border-red-500': errors.descripcion,
                                }"
                            ></textarea>
                            <span
                                v-if="errors.descripcion"
                                class="text-sm text-red-600"
                            >
                                {{ errors.descripcion[0] }}
                            </span>
                        </div>

                        <!-- ============ BOTONES ============ -->
                        <div
                            class="flex items-center justify-end space-x-4 pt-6 border-t"
                        >
                            <button
                                type="button"
                                @click="router.visit(route('tutoriales.index'))"
                                class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition disabled:opacity-50"
                                :disabled="isLoading"
                            >
                                Cancelar
                            </button>
                            <button
                                type="submit"
                                :disabled="isLoading"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition disabled:opacity-75"
                            >
                                <svg
                                    v-if="isLoading"
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
                                {{
                                    isLoading
                                        ? "Actualizando..."
                                        : "Guardar Cambios"
                                }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
