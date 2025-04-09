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

const form = ref({
    id: props.tutorial.id,
    titulo: props.tutorial.titulo || "",
    descripcion: props.tutorial.descripcion || "",
    url: props.tutorial.url || "",
    tipo_material: props.tutorial.tipo_material || "video",
    formato: props.tutorial.formato || "youtube",
    alcance: props.tutorial.alcance || "publico",
    estado: props.tutorial.estado || "activo",
    observacion: props.tutorial.observacion || "",
    subcategoria_id: props.tutorial.subcategoria_id || "",
    user_id: props.tutorial.user_id || null,
});

const isLoading = ref(false);

const submit = () => {
    isLoading.value = true;

    router.put(route("tutoriales.update", form.value.id), form.value, {
        preserveScroll: true,
        onSuccess: () => {
            router.visit(route("tutoriales"));
        },
        onError: () => {},
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
                        v-if="$page.props.flash.success"
                        class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative"
                    >
                        {{ $page.props.flash.success }}
                    </div>

                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <div>
                                    <label
                                        class="block font-medium text-sm text-gray-700"
                                    >
                                        Título *
                                    </label>
                                    <input
                                        v-model="form.titulo"
                                        type="text"
                                        required
                                        class="mt-1 block w-full border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 rounded-md shadow-sm"
                                        :class="{
                                            'border-red-500': errors.titulo,
                                        }"
                                    />
                                    <span
                                        v-if="errors.titulo"
                                        class="text-sm text-red-600"
                                    >
                                        {{ errors.titulo }}
                                    </span>
                                </div>

                                <div>
                                    <label
                                        class="block font-medium text-sm text-gray-700"
                                    >
                                        Subcategoría *
                                    </label>
                                    <select
                                        v-model="form.subcategoria_id"
                                        required
                                        class="mt-1 block w-full border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 rounded-md shadow-sm"
                                        :class="{
                                            'border-red-500':
                                                errors.subcategoria_id,
                                        }"
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
                                        v-if="errors.subcategoria_id"
                                        class="text-sm text-red-600"
                                    >
                                        {{ errors.subcategoria_id }}
                                    </span>
                                </div>

                                <div>
                                    <label
                                        class="block font-medium text-sm text-gray-700"
                                    >
                                        Responsable
                                    </label>
                                    <select
                                        v-model="form.user_id"
                                        class="mt-1 block w-full border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 rounded-md shadow-sm"
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

                            <div class="space-y-4">
                                <div>
                                    <label
                                        class="block font-medium text-sm text-gray-700"
                                    >
                                        Tipo de Material *
                                    </label>
                                    <select
                                        v-model="form.tipo_material"
                                        required
                                        class="mt-1 block w-full border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 rounded-md shadow-sm"
                                        :class="{
                                            'border-red-500':
                                                errors.tipo_material,
                                        }"
                                    >
                                        <option value="video">Video</option>
                                        <option value="documento">
                                            Documento
                                        </option>
                                        <option value="guia">Guía</option>
                                        <option value="triptico">
                                            Tríptico
                                        </option>
                                    </select>
                                    <span
                                        v-if="errors.tipo_material"
                                        class="text-sm text-red-600"
                                    >
                                        {{ errors.tipo_material }}
                                    </span>
                                </div>

                                <div>
                                    <label
                                        class="block font-medium text-sm text-gray-700"
                                    >
                                        Formato *
                                    </label>
                                    <select
                                        v-model="form.formato"
                                        required
                                        class="mt-1 block w-full border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 rounded-md shadow-sm"
                                        :class="{
                                            'border-red-500': errors.formato,
                                        }"
                                    >
                                        <option value="youtube">YouTube</option>
                                        <option value="vimeo">Vimeo</option>
                                        <option value="pdf">PDF</option>
                                        <option value="word">Word</option>
                                        <option value="excel">Excel</option>
                                        <option value="powerpoint">
                                            PowerPoint
                                        </option>
                                        <option value="imagen">Imagen</option>
                                    </select>
                                    <span
                                        v-if="errors.formato"
                                        class="text-sm text-red-600"
                                    >
                                        {{ errors.formato }}
                                    </span>
                                </div>

                                <div>
                                    <label
                                        class="block font-medium text-sm text-gray-700"
                                    >
                                        URL del contenido
                                    </label>
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
                                        {{ errors.url }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label
                                    class="block font-medium text-sm text-gray-700"
                                >
                                    Alcance *
                                </label>
                                <select
                                    v-model="form.alcance"
                                    required
                                    class="mt-1 block w-full border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 rounded-md shadow-sm"
                                    :class="{
                                        'border-red-500': errors.alcance,
                                    }"
                                >
                                    <option value="admin">admin</option>
                                    <option value="soporte">soporte</option>
                                    <option value="clientefinal">
                                        cliente final
                                    </option>
                                </select>
                                <span
                                    v-if="errors.alcance"
                                    class="text-sm text-red-600"
                                >
                                    {{ errors.alcance }}
                                </span>
                            </div>

                            <div>
                                <label
                                    class="block font-medium text-sm text-gray-700"
                                >
                                    Estado *
                                </label>
                                <select
                                    v-model="form.estado"
                                    required
                                    class="mt-1 block w-full border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 rounded-md shadow-sm"
                                    :class="{ 'border-red-500': errors.estado }"
                                >
                                    <option value="activo">Activo</option>
                                    <option value="inactivo">Inactivo</option>
                                </select>
                                <span
                                    v-if="errors.estado"
                                    class="text-sm text-red-600"
                                >
                                    {{ errors.estado }}
                                </span>
                            </div>
                        </div>

                        <div>
                            <label
                                class="block font-medium text-sm text-gray-700"
                            >
                                Observaciones
                            </label>
                            <textarea
                                v-model="form.observacion"
                                rows="2"
                                class="mt-1 block w-full border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 rounded-md shadow-sm"
                                :class="{
                                    'border-red-500': errors.observacion,
                                }"
                            ></textarea>
                            <span
                                v-if="errors.observacion"
                                class="text-sm text-red-600"
                            >
                                {{ errors.observacion }}
                            </span>
                        </div>

                        <div>
                            <label
                                class="block font-medium text-sm text-gray-700"
                            >
                                Descripción *
                            </label>
                            <textarea
                                v-model="form.descripcion"
                                rows="4"
                                required
                                class="mt-1 block w-full border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 rounded-md shadow-sm"
                                :class="{
                                    'border-red-500': errors.descripcion,
                                }"
                            ></textarea>
                            <span
                                v-if="errors.descripcion"
                                class="text-sm text-red-600"
                            >
                                {{ errors.descripcion }}
                            </span>
                        </div>

                        <div
                            class="flex items-center justify-end space-x-4 pt-6 border-t"
                        >
                            <button
                                type="button"
                                @click="router.visit(route('tutoriales'))"
                                class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                :disabled="isLoading"
                            >
                                Cancelar
                            </button>
                            <button
                                type="submit"
                                :disabled="isLoading"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-75"
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
