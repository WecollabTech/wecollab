<script setup>
import { computed } from "vue";
import { Link } from "@inertiajs/vue3";

const props = defineProps({
    href: String,
    active: Boolean,
});

const classes = computed(() => {
    return props.active
        ? "inline-flex items-center px-3 py-2 rounded-lg bg-green-700 text-white text-sm font-medium transition duration-150 ease-in-out"
        : "inline-flex items-center px-3 py-2 rounded-lg text-inherit text-sm font-medium hover:bg-green-600 hover:text-white transition duration-150 ease-in-out";
});
</script>

<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>
