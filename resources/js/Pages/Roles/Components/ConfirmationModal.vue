<template>
    <div
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center"
    >
        <div
            class="bg-white rounded-lg border border-gray-200 shadow-2xl p-8 w-1/3 text-center"
        >
            <h3 class="text-2xl font-semibold mb-4">{{ title }}</h3>
            <p class="mb-6 text-gray-600">{{ message }}</p>
            <div class="flex justify-center gap-4">
                <button
                    @click="$emit('cancel')"
                    class="bg-gray-500 text-white px-6 py-2 rounded-md hover:bg-gray-600 transition duration-200 shadow-md"
                >
                    Cancelar
                </button>
                <button
                    @click="$emit('confirm')"
                    class="bg-red-500 text-white px-6 py-2 rounded-md hover:bg-red-600 transition duration-200 shadow-md"
                >
                    Confirmar
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    title: {
        type: String,
        required: true,
    },
    message: {
        type: String,
        required: true,
    },
});

defineEmits(["confirm", "cancel"]);
</script>
