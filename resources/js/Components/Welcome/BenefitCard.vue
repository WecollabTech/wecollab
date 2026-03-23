<script setup>
const props = defineProps({
    benefit: {
        type: Object,
        required: true,
        default: () => ({}),
    },
});
</script>

<template>
    <div
        class="relative overflow-hidden rounded-3xl p-8 min-h-[280px] flex flex-col justify-between group cursor-pointer hover:shadow-2xl transition-all duration-300"
    >
        <div
            class="absolute inset-0 bg-gradient-to-br transition-all duration-500 group-hover:scale-105"
            :class="benefit.color"
        ></div>
        <div
            class="absolute inset-0 opacity-10"
            style="
                background-image: radial-gradient(
                    circle at 2px 2px,
                    white 1px,
                    transparent 0
                );
                background-size: 30px 30px;
            "
        ></div>

        <div class="relative z-10">
            <div class="text-5xl mb-4">
                <i :class="`fas ${benefit.icon}`"></i>
            </div>
            <h3 class="text-2xl font-bold text-white mb-3">
                {{ benefit.title }}
            </h3>
            <p class="text-white/90 text-base leading-relaxed">
                {{ benefit.description }}
            </p>
        </div>

        <div
            class="absolute -bottom-10 -right-10 w-32 h-32 bg-white/10 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-500"
        ></div>
    </div>
</template>
