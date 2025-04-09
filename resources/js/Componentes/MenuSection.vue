<template>
    <li>
        <div
            class="flex items-center justify-between px-3 py-2 rounded-md cursor-pointer transition-colors text-sm"
            :class="[
                isMobile
                    ? 'hover:bg-gray-200 text-gray-800'
                    : 'hover:bg-green-600 text-white',
                open && 'bg-green-500 text-white',
            ]"
            @click="$emit('toggle')"
            aria-expanded="open"
        >
            <div class="flex items-center gap-3">
                <i :class="[icon, 'w-5 text-center']"></i>
                <span>{{ title }}</span>
            </div>
            <i
                class="fas fa-chevron-down text-xs transition-transform duration-200"
                :class="{ 'rotate-180': open }"
            ></i>
        </div>
        <ul
            v-show="open"
            class="pl-8 mt-1 space-y-1 overflow-hidden transition-all duration-300"
        >
            <slot></slot>
        </ul>
    </li>
</template>

<script setup>
defineProps({
    title: String,
    icon: String,
    open: Boolean,
    isMobile: Boolean,
});

defineEmits(["toggle"]);
</script>
