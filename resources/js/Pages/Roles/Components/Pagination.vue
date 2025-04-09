<template>
    <div class="flex justify-center items-center mt-4 space-x-4">
        <button
            @click="$emit('prev')"
            :disabled="!prevPageUrl"
            class="px-4 py-2 bg-[#277cbb] text-white rounded-md hover:bg-[#63a2c4] transition duration-200"
        >
            Anterior
        </button>
        <span class="text-gray-700">
            Página {{ currentPage }} de {{ lastPage }}
        </span>
        <button
            @click="$emit('next')"
            :disabled="!nextPageUrl"
            class="px-4 py-2 bg-[#277cbb] text-white rounded-md hover:bg-[#63a2c4] transition duration-200"
        >
            Siguiente
        </button>
    </div>
</template>

<script setup>
defineProps({
    currentPage: {
        type: Number,
        required: true,
    },
    lastPage: {
        type: Number,
        required: true,
    },
    prevPageUrl: {
        type: String,
        default: null,
    },
    nextPageUrl: {
        type: String,
        default: null,
    },
});

defineEmits(["prev", "next"]);
</script>
