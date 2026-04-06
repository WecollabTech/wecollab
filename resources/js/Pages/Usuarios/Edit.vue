<template>
    <AppLayout title="Editar Usuario">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Editar Usuario:
                {{ user ? `${user.name} ${user.apellido}` : "Cargando..." }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <!-- Mostrar loading mientras carga -->
                        <div v-if="loading" class="text-center py-8">
                            <div
                                class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"
                            ></div>
                            <p class="mt-2 text-gray-600">
                                Cargando datos del usuario...
                            </p>
                        </div>

                        <!-- Mostrar error si hay problema -->
                        <div v-else-if="error" class="text-center py-8">
                            <div class="text-red-600 mb-4">
                                <svg
                                    class="mx-auto h-12 w-12"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                    ></path>
                                </svg>
                            </div>
                            <p class="text-gray-600 mb-4">{{ error }}</p>
                            <Link
                                :href="route('usuarios.index')"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                            >
                                Volver al listado
                            </Link>
                        </div>

                        <!-- Formulario cuando hay datos -->
                        <form v-else @submit.prevent="submit">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Nombre -->
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700"
                                        >Nombre *</label
                                    >
                                    <input
                                        v-model="form.name"
                                        type="text"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                        :class="{
                                            'border-red-500': errors.name,
                                        }"
                                    />
                                    <p
                                        v-if="errors.name"
                                        class="text-red-500 text-xs mt-1"
                                    >
                                        {{ errors.name }}
                                    </p>
                                </div>

                                <!-- Apellido -->
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700"
                                        >Apellido *</label
                                    >
                                    <input
                                        v-model="form.apellido"
                                        type="text"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                        :class="{
                                            'border-red-500': errors.apellido,
                                        }"
                                    />
                                    <p
                                        v-if="errors.apellido"
                                        class="text-red-500 text-xs mt-1"
                                    >
                                        {{ errors.apellido }}
                                    </p>
                                </div>

                                <!-- Email -->
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700"
                                        >Email *</label
                                    >
                                    <input
                                        v-model="form.email"
                                        type="email"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                        :class="{
                                            'border-red-500': errors.email,
                                        }"
                                    />
                                    <p
                                        v-if="errors.email"
                                        class="text-red-500 text-xs mt-1"
                                    >
                                        {{ errors.email }}
                                    </p>
                                </div>

                                <!-- Teléfono -->
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700"
                                        >Teléfono</label
                                    >
                                    <input
                                        v-model="form.telefono"
                                        type="text"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                    />
                                </div>

                                <!-- Contraseña (opcional en edición) -->
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700"
                                        >Nueva Contraseña</label
                                    >
                                    <input
                                        v-model="form.password"
                                        type="password"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                        placeholder="Dejar en blanco para mantener actual"
                                    />
                                    <p class="text-xs text-gray-500 mt-1">
                                        Solo completar si deseas cambiar la
                                        contraseña
                                    </p>
                                </div>

                                <!-- Confirmar Contraseña -->
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700"
                                        >Confirmar Nueva Contraseña</label
                                    >
                                    <input
                                        v-model="form.password_confirmation"
                                        type="password"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                    />
                                </div>

                                <!-- Rol -->
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700"
                                        >Rol *</label
                                    >
                                    <select
                                        v-model="form.role_id"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                        :class="{
                                            'border-red-500': errors.role_id,
                                        }"
                                    >
                                        <option value="">
                                            Seleccione un rol
                                        </option>
                                        <option
                                            v-for="role in roles"
                                            :key="role.id"
                                            :value="role.id"
                                        >
                                            {{ role.nombre }}
                                        </option>
                                    </select>
                                    <p
                                        v-if="errors.role_id"
                                        class="text-red-500 text-xs mt-1"
                                    >
                                        {{ errors.role_id }}
                                    </p>
                                </div>

                                <!-- País -->
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700"
                                        >País *</label
                                    >
                                    <select
                                        v-model="form.pais_id"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                        :class="{
                                            'border-red-500': errors.pais_id,
                                        }"
                                    >
                                        <option value="">
                                            Seleccione un país
                                        </option>
                                        <option
                                            v-for="pais in paises"
                                            :key="pais.id"
                                            :value="pais.id"
                                        >
                                            {{ pais.nombre }}
                                        </option>
                                    </select>
                                    <p
                                        v-if="errors.pais_id"
                                        class="text-red-500 text-xs mt-1"
                                    >
                                        {{ errors.pais_id }}
                                    </p>
                                </div>

                                <!-- Company ID -->
                                <!-- Company ID - Cambiar de number a text -->
                                <!-- Company ID - Cambiar de number a text -->
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700"
                                    >
                                        ID de Compañía
                                    </label>
                                    <input
                                        v-model="form.company_id"
                                        type="text"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                        placeholder="Ej: WECOLLAB001"
                                    />
                                    <p class="text-xs text-gray-500 mt-1">
                                        ID de la compañía (puede ser texto o
                                        número - opcional)
                                    </p>
                                </div>

                                <!-- Dirección -->
                                <div class="md:col-span-2">
                                    <label
                                        class="block text-sm font-medium text-gray-700"
                                        >Dirección</label
                                    >
                                    <textarea
                                        v-model="form.direccion"
                                        rows="3"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                    ></textarea>
                                </div>

                                <!-- Estado Activo -->
                                <div>
                                    <label class="flex items-center">
                                        <input
                                            v-model="form.status"
                                            type="checkbox"
                                            value="activo"
                                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                        />
                                        <span class="ml-2 text-sm text-gray-600"
                                            >Usuario Activo</span
                                        >
                                    </label>
                                </div>
                            </div>

                            <!-- Botones -->
                            <div class="flex justify-end space-x-3 mt-6">
                                <Link
                                    :href="route('usuarios.index')"
                                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded transition duration-150 ease-in-out"
                                >
                                    Cancelar
                                </Link>
                                <button
                                    type="submit"
                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition duration-150 ease-in-out disabled:opacity-50 disabled:cursor-not-allowed"
                                    :disabled="submitting"
                                >
                                    <span
                                        v-if="submitting"
                                        class="inline-block animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"
                                    ></span>
                                    {{
                                        submitting
                                            ? "Actualizando..."
                                            : "Actualizar Usuario"
                                    }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { Link, router } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import { usuarioService } from "@/services/usuarioService";
import Swal from "sweetalert2";

const props = defineProps({
    id: {
        type: [String, Number],
        required: true,
    },
});

const user = ref(null);
const roles = ref([]);
const paises = ref([]);
const loading = ref(true);
const error = ref(null);
const submitting = ref(false);
const errors = ref({});

const form = ref({
    name: "",
    apellido: "",
    email: "",
    password: "",
    password_confirmation: "",
    telefono: "",
    direccion: "",
    role_id: "",
    pais_id: "",
    company_id: "",
    status: "activo",
});

const loadData = async () => {
    loading.value = true;
    error.value = null;

    try {
        // Cargar usuario, roles y países en paralelo
        const [userRes, rolesRes, paisesRes] = await Promise.all([
            usuarioService.getUsuario(props.id),
            usuarioService.getRoles(),
            usuarioService.getPaises(),
        ]);

        user.value = userRes.data;
        roles.value = rolesRes.data || [];
        paises.value = paisesRes.data || [];

        // Llenar el formulario con los datos del usuario
        form.value = {
            name: user.value.name || "",
            apellido: user.value.apellido || "",
            email: user.value.email || "",
            password: "",
            password_confirmation: "",
            telefono: user.value.telefono || "",
            direccion: user.value.direccion || "",
            role_id: user.value.role_id || "",
            pais_id: user.value.pais_id || "",
            company_id: user.value.company_id || "",
            status: user.value.status || "activo",
        };
    } catch (err) {
        console.error("Error al cargar datos:", err);
        error.value =
            "No se pudieron cargar los datos del usuario. Por favor, intenta de nuevo.";
        Swal.fire("Error", error.value, "error");
    } finally {
        loading.value = false;
    }
};

const submit = async () => {
    submitting.value = true;
    errors.value = {};

    try {
        await usuarioService.updateUsuario(props.id, form.value);
        Swal.fire({
            icon: "success",
            title: "¡Éxito!",
            text: "Usuario actualizado correctamente",
            timer: 2000,
            showConfirmButton: false,
        });
        router.visit(route("usuarios.index"));
    } catch (err) {
        if (err.response?.status === 422) {
            errors.value = err.response.data.errors;
            Swal.fire(
                "Error de validación",
                "Por favor verifica los campos marcados",
                "error",
            );
        } else {
            Swal.fire(
                "Error",
                err.response?.data?.message ||
                    "No se pudo actualizar el usuario",
                "error",
            );
        }
    } finally {
        submitting.value = false;
    }
};

onMounted(() => {
    loadData();
});
</script>
