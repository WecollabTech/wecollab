<template>
    <AppLayout title="Ver Usuario">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Detalles del Usuario
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <div v-if="loading" class="text-center py-8">
                            <div
                                class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"
                            ></div>
                            <p class="mt-2 text-gray-600">
                                Cargando datos del usuario...
                            </p>
                        </div>

                        <div v-else>
                            <!-- Información del usuario -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Foto de perfil -->
                                <div
                                    class="md:col-span-2 flex justify-center mb-6"
                                >
                                    <div class="relative">
                                        <img
                                            :src="
                                                user.fotoperfil
                                                    ? `/storage/${user.fotoperfil}`
                                                    : user.profile_photo_url
                                            "
                                            :alt="user.name"
                                            class="h-32 w-32 rounded-full object-cover border-4 border-gray-200"
                                        />
                                    </div>
                                </div>

                                <!-- Nombre completo -->
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <label
                                        class="block text-xs font-medium text-gray-500 uppercase"
                                        >Nombre Completo</label
                                    >
                                    <p
                                        class="mt-1 text-lg font-semibold text-gray-900"
                                    >
                                        {{ user.name }} {{ user.apellido }}
                                    </p>
                                </div>

                                <!-- Email -->
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <label
                                        class="block text-xs font-medium text-gray-500 uppercase"
                                        >Correo Electrónico</label
                                    >
                                    <p
                                        class="mt-1 text-lg font-semibold text-gray-900"
                                    >
                                        {{ user.email }}
                                    </p>
                                </div>

                                <!-- Teléfono -->
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <label
                                        class="block text-xs font-medium text-gray-500 uppercase"
                                        >Teléfono</label
                                    >
                                    <p
                                        class="mt-1 text-lg font-semibold text-gray-900"
                                    >
                                        {{ user.telefono || "No registrado" }}
                                    </p>
                                </div>

                                <!-- Rol -->
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <label
                                        class="block text-xs font-medium text-gray-500 uppercase"
                                        >Rol</label
                                    >
                                    <p class="mt-1">
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800"
                                        >
                                            {{
                                                user.role?.nombre ||
                                                "No asignado"
                                            }}
                                        </span>
                                    </p>
                                </div>

                                <!-- País -->
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <label
                                        class="block text-xs font-medium text-gray-500 uppercase"
                                        >País</label
                                    >
                                    <p
                                        class="mt-1 text-lg font-semibold text-gray-900"
                                    >
                                        {{
                                            user.pais?.nombre ||
                                            "No especificado"
                                        }}
                                    </p>
                                </div>

                                <!-- ID de Compañía -->
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <label
                                        class="block text-xs font-medium text-gray-500 uppercase"
                                        >ID de Compañía</label
                                    >
                                    <p
                                        class="mt-1 text-lg font-semibold text-gray-900"
                                    >
                                        {{
                                            user.company_id || "No especificado"
                                        }}
                                    </p>
                                </div>

                                <!-- Estado -->
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <label
                                        class="block text-xs font-medium text-gray-500 uppercase"
                                        >Estado</label
                                    >
                                    <p class="mt-1">
                                        <span
                                            :class="{
                                                'px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800':
                                                    user.status === 'activo',
                                                'px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800':
                                                    user.status === 'inactivo',
                                            }"
                                        >
                                            {{
                                                user.status === "activo"
                                                    ? "Activo"
                                                    : "Inactivo"
                                            }}
                                        </span>
                                    </p>
                                </div>

                                <!-- Fecha de creación -->
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <label
                                        class="block text-xs font-medium text-gray-500 uppercase"
                                        >Fecha de Registro</label
                                    >
                                    <p
                                        class="mt-1 text-lg font-semibold text-gray-900"
                                    >
                                        {{ formatDate(user.created_at) }}
                                    </p>
                                </div>

                                <!-- Última actualización -->
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <label
                                        class="block text-xs font-medium text-gray-500 uppercase"
                                        >Última Actualización</label
                                    >
                                    <p
                                        class="mt-1 text-lg font-semibold text-gray-900"
                                    >
                                        {{ formatDate(user.updated_at) }}
                                    </p>
                                </div>

                                <!-- Dirección (ocupa todo el ancho) -->
                                <div
                                    class="md:col-span-2 bg-gray-50 rounded-lg p-4"
                                >
                                    <label
                                        class="block text-xs font-medium text-gray-500 uppercase"
                                        >Dirección</label
                                    >
                                    <p class="mt-1 text-gray-900">
                                        {{ user.direccion || "No registrada" }}
                                    </p>
                                </div>
                            </div>

                            <!-- Botones de acción con permisos -->
                            <div
                                class="flex justify-end space-x-3 mt-8 pt-6 border-t border-gray-200"
                            >
                                <Link
                                    :href="route('usuarios.index')"
                                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded transition duration-150 ease-in-out"
                                >
                                    Volver
                                </Link>

                                <!-- Botón Editar - visible para Superadmin y Admin -->
                                <Link
                                    v-if="puedeEditar"
                                    :href="route('usuarios.edit', user.id)"
                                    class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded transition duration-150 ease-in-out"
                                >
                                    Editar Usuario
                                </Link>

                                <!-- Botón Activar/Desactivar - visible para Superadmin y Admin -->
                                <button
                                    v-if="puedeCambiarEstado"
                                    @click="toggleStatus"
                                    :class="
                                        user.status === 'activo'
                                            ? 'bg-orange-500 hover:bg-orange-700'
                                            : 'bg-green-500 hover:bg-green-700'
                                    "
                                    class="text-white font-bold py-2 px-4 rounded transition duration-150 ease-in-out"
                                >
                                    {{
                                        user.status === "activo"
                                            ? "Desactivar"
                                            : "Activar"
                                    }}
                                </button>

                                <!-- 🔐 Botón Eliminar - SOLO visible para Superadmin -->
                                <button
                                    v-if="puedeEliminar"
                                    @click="deleteUser"
                                    class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded transition duration-150 ease-in-out"
                                >
                                    Eliminar Usuario
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import { Link, router, usePage } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import { usuarioService } from "@/services/usuarioService";
import Swal from "sweetalert2";

const props = defineProps({
    id: {
        type: [String, Number],
        required: true,
    },
});

// 🔐 Obtener usuario actual y su rol
const page = usePage();
const currentUser = computed(() => page.props.auth?.user);

// 🔐 Obtener rol del usuario actual
const getUserRole = () => {
    const user = currentUser.value;
    if (!user) return "Invitado";

    const role = user.role;
    if (!role) return "Sin rol";

    if (typeof role === "object") {
        return role.nombre?.toString() || role.name?.toString() || "";
    }
    return role.toString();
};

// 🔐 Definir roles
const ROL_SUPERADMIN = "Superadmin we collab";
const ROL_ADMIN = "Admin we collab";

// 🔐 Permisos basados en rol
const esSuperadmin = computed(() => getUserRole() === ROL_SUPERADMIN);
const esAdmin = computed(() => getUserRole() === ROL_ADMIN);

// 🔐 Permisos específicos
const puedeEliminar = computed(() => esSuperadmin.value); // Solo Superadmin puede eliminar
const puedeEditar = computed(() => esSuperadmin.value || esAdmin.value); // Admin y Superadmin pueden editar
const puedeCambiarEstado = computed(() => esSuperadmin.value || esAdmin.value); // Admin y Superadmin pueden cambiar estado

const user = ref(null);
const loading = ref(true);

const formatDate = (date) => {
    if (!date) return "No disponible";
    return new Date(date).toLocaleDateString("es-ES", {
        year: "numeric",
        month: "long",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
};

const loadUser = async () => {
    loading.value = true;
    try {
        const response = await usuarioService.getUsuario(props.id);
        user.value = response.data;
    } catch (error) {
        console.error("Error al cargar usuario:", error);
        Swal.fire(
            "Error",
            "No se pudieron cargar los datos del usuario",
            "error",
        );
        router.visit(route("usuarios.index"));
    } finally {
        loading.value = false;
    }
};

const toggleStatus = async () => {
    // Verificar permisos
    if (!puedeCambiarEstado.value) {
        Swal.fire(
            "Error",
            "No tienes permisos para cambiar el estado",
            "error",
        );
        return;
    }

    const result = await Swal.fire({
        title: "¿Estás seguro?",
        text: `¿Deseas ${user.value.status === "activo" ? "desactivar" : "activar"} a ${user.value.name} ${user.value.apellido}?`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor:
            user.value.status === "activo" ? "#f59e0b" : "#10b981",
        confirmButtonText: `Sí, ${user.value.status === "activo" ? "desactivar" : "activar"}`,
        cancelButtonText: "Cancelar",
    });

    if (result.isConfirmed) {
        try {
            await usuarioService.toggleStatus(user.value.id);
            await loadUser();
            Swal.fire(
                "¡Éxito!",
                `Usuario ${user.value.status === "activo" ? "activado" : "desactivado"} correctamente`,
                "success",
            );
        } catch (error) {
            Swal.fire(
                "Error",
                "No se pudo cambiar el estado del usuario",
                "error",
            );
        }
    }
};

const deleteUser = async () => {
    // Verificar permisos nuevamente por seguridad
    if (!puedeEliminar.value) {
        Swal.fire(
            "Error",
            "No tienes permisos para eliminar usuarios",
            "error",
        );
        return;
    }

    const result = await Swal.fire({
        title: "¿Estás seguro?",
        text: `¿Eliminar permanentemente a ${user.value.name} ${user.value.apellido}? Esta acción no se puede deshacer.`,
        icon: "error",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar",
    });

    if (result.isConfirmed) {
        try {
            await usuarioService.deleteUsuario(user.value.id);
            Swal.fire(
                "¡Eliminado!",
                "Usuario eliminado correctamente",
                "success",
            );
            router.visit(route("usuarios.index"));
        } catch (error) {
            Swal.fire(
                "Error",
                error.response?.data?.message ||
                    "No se pudo eliminar el usuario",
                "error",
            );
        }
    }
};

onMounted(() => {
    loadUser();
});
</script>
