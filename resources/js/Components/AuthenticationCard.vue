<template>
    <div
        class="h-[90vh] flex flex-col justify-center items-center py-12 bg-gradient-to-r blue-50 to-blue-200"
    >
        <div class="mb-6">
            <slot name="logo" />
        </div>

        <div
            class="w-full sm:max-w-2xl lg:max-w-3xl mt-6 px-8 py-6 rounded-3xl border border-gray-300 shadow-lg bg-white"
        >
            <slot />
        </div>
    </div>
</template>
