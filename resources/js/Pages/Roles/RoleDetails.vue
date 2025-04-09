<template>
    <div class="bg-white rounded-lg border border-gray-200 shadow-xl p-6">
        <h3 class="text-xl font-semibold mb-4">Detalles del Rol</h3>

        <!-- Detalles del Rol -->
        <div class="mb-6">
            <h4 class="text-lg font-medium text-gray-700">
                Información del Rol
            </h4>
            <div class="mt-2 space-y-2">
                <p><span class="font-medium">ID:</span> {{ role.id }}</p>
                <p>
                    <span class="font-medium">Nombre:</span> {{ role.nombre }}
                </p>
                <p>
                    <span class="font-medium">Estado:</span>
                    <span
                        :class="
                            role.estado === 'activo'
                                ? 'bg-green-100 text-green-800'
                                : 'bg-red-100 text-red-800'
                        "
                        class="px-2 py-1 text-xs font-semibold rounded-full"
                    >
                        {{ role.estado === "activo" ? "Activo" : "Inactivo" }}
                    </span>
                </p>
                <p>
                    <span class="font-medium">Descripción:</span>
                    {{ role.descripcion }}
                </p>
            </div>
        </div>

        <!-- Permisos asociados -->
        <div>
            <h4 class="text-lg font-medium text-gray-700">
                Permisos Asociados
            </h4>
            <div class="mt-2 space-y-2">
                <div
                    v-for="permiso in role.permisos"
                    :key="permiso.id"
                    class="flex items-center"
                >
                    <span class="text-sm text-gray-700">
                        {{ permiso.nombre }}
                    </span>
                </div>
                <p
                    v-if="role.permisos.length === 0"
                    class="text-sm text-gray-500"
                >
                    No hay permisos asociados a este rol.
                </p>
            </div>
        </div>

        <!-- Botón para cerrar -->
        <div class="flex justify-end mt-6">
            <button
                @click="closeModal"
                class="bg-green-500 text-white px-6 py-2 rounded-md hover:bg-green-600 transition duration-200 shadow-md"
            >
                Cerrar
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";

// Props: Recibe el ID del rol como parámetro
const props = defineProps({
    roleId: {
        type: Number,
        required: true,
    },
});

// Estado para almacenar los detalles del rol
const role = ref({ permisos: [] });

// Función para cargar los detalles del rol
const loadRoleDetails = async () => {
    try {
        const response = await axios.get(`/roles/${props.roleId}`);
        role.value = response.data;
    } catch (error) {
        console.error("Error al cargar los detalles del rol:", error);
    }
};

// Cargar los detalles del rol cuando el componente se monta
onMounted(() => {
    loadRoleDetails();
});

// Emitir evento para cerrar el modal
const emit = defineEmits(["close"]);
const closeModal = () => {
    emit("close");
};
</script>
