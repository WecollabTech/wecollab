<template>
    <AppLayout title="Crear Usuario">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Crear Nuevo Usuario
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <form @submit.prevent="submit">
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

                                <!-- Contraseña -->
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700"
                                        >Contraseña *</label
                                    >
                                    <input
                                        v-model="form.password"
                                        type="password"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                        :class="{
                                            'border-red-500': errors.password,
                                        }"
                                    />
                                    <p
                                        v-if="errors.password"
                                        class="text-red-500 text-xs mt-1"
                                    >
                                        {{ errors.password }}
                                    </p>
                                </div>

                                <!-- Confirmar Contraseña -->
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700"
                                        >Confirmar Contraseña *</label
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
                                            ? "Guardando..."
                                            : "Crear Usuario"
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

const roles = ref([]);
const paises = ref([]);
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
    try {
        const [rolesRes, paisesRes] = await Promise.all([
            usuarioService.getRoles(),
            usuarioService.getPaises(),
        ]);
        roles.value = rolesRes.data || [];
        paises.value = paisesRes.data || [];
    } catch (error) {
        console.error("Error al cargar datos:", error);
        Swal.fire(
            "Error",
            "No se pudieron cargar los datos necesarios",
            "error",
        );
    }
};

const submit = async () => {
    submitting.value = true;
    errors.value = {};

    try {
        await usuarioService.createUsuario(form.value);
        Swal.fire({
            icon: "success",
            title: "¡Éxito!",
            text: "Usuario creado correctamente",
            timer: 2000,
            showConfirmButton: false,
        });
        router.visit(route("usuarios.index"));
    } catch (error) {
        if (error.response?.status === 422) {
            errors.value = error.response.data.errors;
            Swal.fire(
                "Error de validación",
                "Por favor verifica los campos marcados",
                "error",
            );
        } else {
            Swal.fire(
                "Error",
                error.response?.data?.message || "No se pudo crear el usuario",
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
