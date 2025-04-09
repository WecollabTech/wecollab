<template>
    <li v-if="visible">
        <component
            :is="as"
            :href="href"
            @click="handleClick"
            class="flex items-center gap-3 px-3 py-2 rounded-md transition-colors text-sm"
            :class="[
                active
                    ? 'bg-green-500 text-white'
                    : isMobile
                    ? 'hover:bg-gray-200 text-gray-800'
                    : 'hover:bg-green-600 text-white',
                { 'pointer-events-none opacity-50': disabled },
            ]"
        >
            <i :class="[icon, 'w-5 text-center']"></i>
            <span class="flex-1 truncate">
                <slot></slot>
            </span>
        </component>
    </li>
</template>

<script setup>
const props = defineProps({
    href: String,
    icon: String,
    active: Boolean,
    as: {
        type: String,
        default: "NavLink",
    },
    disabled: Boolean,
    visible: {
        type: Boolean,
        default: true,
    },
    isMobile: Boolean,
});

const emit = defineEmits(["click"]);

const handleClick = (e) => {
    if (props.as === "button") {
        e.preventDefault();
        emit("click");
    }
};
</script>
