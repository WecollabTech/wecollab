<template>
    <button
        @click="$emit('click')"
        :class="`bg-${color}-500 text-white px-3 py-1 rounded-md hover:bg-${color}-600 transition duration-200 relative`"
        :title="title"
    >
        <slot>
            <svg
                class="w-5 h-5"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    :d="iconPath"
                ></path>
            </svg>
        </slot>
        <span
            class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-gray-700 text-white text-xs px-2 py-1 rounded opacity-0 hover:opacity-100 transition-opacity duration-200"
        >
            {{ title }}
        </span>
    </button>
</template>

<script setup>
defineProps({
    color: {
        type: String,
        default: "blue",
    },
    title: {
        type: String,
        required: true,
    },
    iconPath: {
        type: String,
        default: "",
    },
});
</script>
