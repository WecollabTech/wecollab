<script setup>
const props = defineProps({
    icon: { type: String, required: true },
    title: { type: String, required: true },
    description: { type: String, required: true },
    color: { type: String, required: true },
});
</script>

<template>
    <div
        class="bg-white rounded-xl sm:rounded-2xl p-4 sm:p-5 lg:p-6 shadow-lg hover:shadow-2xl transition-all duration-300 border border-gray-100 hover:-translate-y-1 group"
    >
        <div
            class="w-10 h-10 sm:w-12 sm:h-12 lg:w-14 lg:h-14 bg-gradient-to-br rounded-xl sm:rounded-2xl flex items-center justify-center mb-3 sm:mb-4 group-hover:scale-110 transition-transform duration-300 shadow-lg"
            :class="[color, `shadow-${color.split('-')[1]}-500/30`]"
        >
            <i
                :class="`fas ${icon} text-lg sm:text-xl lg:text-2xl text-white`"
            ></i>
        </div>
        <h4
            class="text-sm sm:text-base lg:text-lg font-bold text-gray-900 mb-1.5 sm:mb-2"
        >
            {{ title }}
        </h4>
        <p class="text-xs sm:text-sm text-gray-600 leading-relaxed">
            {{ description }}
        </p>
    </div>
</template>
