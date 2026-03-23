<!-- Components/Header/HeaderLogo.vue -->
<script setup>
import { Link } from "@inertiajs/vue3";

defineProps({
    showText: { type: Boolean, default: true },
});
</script>

<template>
    <Link href="/" class="flex items-center gap-4 group">
        <div class="relative">
            <div
                class="absolute -inset-2 bg-purple-600/30 rounded-2xl blur-xl opacity-0 group-hover:opacity-60 transition-all"
            ></div>
            <img
                src="/img/Logo.png"
                alt="SLC"
                class="relative h-12 sm:h-14 w-auto object-contain group-hover:scale-105 transition-transform"
            />
        </div>

        <div v-if="showText" class="block leading-tight">
            <span
                class="text-xl sm:text-3xl lg:text-4xl font-black text-black group-hover:text-indigo-300 transition-colors"
            >
                SLC
            </span>
            <p
                class="text-[9px] sm:text-[11px] lg:text-[12px] uppercase tracking-widest text-purple-300/80"
            >
                Learning Platform
            </p>
        </div>
    </Link>
</template>
