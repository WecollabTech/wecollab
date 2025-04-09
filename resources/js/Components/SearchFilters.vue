<template>
    <div class="flex space-x-4 mb-6">
        <!-- Filtro por nombre -->
        <div class="flex-1">
            <label class="block text-sm font-medium text-gray-700"
                >Buscar por Nombre</label
            >
            <input
                v-model="searchName"
                type="text"
                placeholder="Nombre del rol"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
            />
        </div>

        <!-- Filtro por estado -->
        <div class="flex-1">
            <label class="block text-sm font-medium text-gray-700"
                >Filtrar por Estado</label
            >
            <select
                v-model="searchStatus"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
            >
                <option value="">Todos</option>
                <option value="activo">Activo</option>
                <option value="inactivo">Inactivo</option>
            </select>
        </div>
    </div>
</template>

<script setup>
import { ref, watch } from "vue";

// Props para recibir los valores iniciales de los filtros
const props = defineProps({
    initialSearchName: {
        type: String,
        default: "",
    },
    initialSearchStatus: {
        type: String,
        default: "",
    },
});

// Emits para notificar cambios en los filtros
const emit = defineEmits(["update:searchName", "update:searchStatus"]);

// Variables reactivas para los filtros
const searchName = ref(props.initialSearchName);
const searchStatus = ref(props.initialSearchStatus);

// Watchers para emitir los cambios en los filtros
watch(searchName, (newValue) => {
    emit("update:searchName", newValue);
});

watch(searchStatus, (newValue) => {
    emit("update:searchStatus", newValue);
});
</script>
