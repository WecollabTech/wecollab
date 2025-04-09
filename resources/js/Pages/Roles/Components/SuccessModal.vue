<template>
    <div
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center"
    >
        <div
            class="bg-white rounded-lg border border-gray-200 shadow-2xl p-8 w-1/3 text-center"
        >
            <div class="flex justify-center mb-6">
                <svg
                    class="w-16 h-16 text-green-500"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M5 13l4 4L19 7"
                    ></path>
                </svg>
            </div>
            <h3 class="text-2xl font-semibold mb-4">¡Éxito!</h3>
            <p class="mb-6 text-gray-600">{{ message }}</p>
            <div class="flex justify-center">
                <button
                    @click="$emit('close')"
                    class="bg-green-500 text-white px-6 py-2 rounded-md hover:bg-green-600 transition duration-200 shadow-md"
                >
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    message: {
        type: String,
        required: true,
    },
});

defineEmits(["close"]);
</script>
