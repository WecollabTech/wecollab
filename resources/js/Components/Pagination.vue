<template>
    <div
        v-if="links && links.length > 0"
        class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:px-6"
    >
        <div class="flex flex-1 justify-between sm:hidden">
            <Link
                v-for="(link, index) in links"
                :key="index"
                :href="link.url || '#'"
                :class="{
                    'relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50': true,
                    'cursor-not-allowed opacity-50': !link.url,
                    'bg-gray-100': link.active,
                }"
                v-html="link.label"
            />
        </div>
        <div
            class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between"
        >
            <div>
                <p class="text-sm text-gray-700">
                    Mostrando
                    <span class="font-medium">{{ from }}</span>
                    al
                    <span class="font-medium">{{ to }}</span>
                    de
                    <span class="font-medium">{{ total }}</span>
                    resultados
                </p>
            </div>
            <div>
                <nav
                    class="isolate inline-flex -space-x-px rounded-md shadow-sm"
                    aria-label="Pagination"
                >
                    <template v-for="(link, index) in links" :key="index">
                        <!-- Previous button -->
                        <div
                            v-if="
                                index === 0 && link.label.includes('Anterior')
                            "
                            class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 focus:z-20 focus:outline-offset-0"
                            :class="{
                                'hover:bg-gray-50 cursor-pointer': link.url,
                                'cursor-not-allowed': !link.url,
                            }"
                        >
                            <Link
                                :href="link.url || '#'"
                                class="flex items-center"
                            >
                                <span class="sr-only">Anterior</span>
                                <svg
                                    class="h-5 w-5"
                                    viewBox="0 0 20 20"
                                    fill="currentColor"
                                    aria-hidden="true"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                                <span class="ml-1">Anterior</span>
                            </Link>
                        </div>

                        <!-- Page numbers -->
                        <div
                            v-else-if="
                                index !== 0 && index !== links.length - 1
                            "
                        >
                            <Link
                                :href="link.url || '#'"
                                :class="{
                                    'relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 focus:z-20 focus:outline-offset-0': true,
                                    'cursor-not-allowed opacity-50': !link.url,
                                    'bg-blue-600 text-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600':
                                        link.active,
                                    'hover:bg-gray-50':
                                        !link.active && link.url,
                                }"
                                v-html="link.label"
                            />
                        </div>

                        <!-- Next button -->
                        <div
                            v-else-if="
                                index === links.length - 1 &&
                                link.label.includes('Siguiente')
                            "
                            class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 focus:z-20 focus:outline-offset-0"
                            :class="{
                                'hover:bg-gray-50 cursor-pointer': link.url,
                                'cursor-not-allowed': !link.url,
                            }"
                        >
                            <Link
                                :href="link.url || '#'"
                                class="flex items-center"
                            >
                                <span class="mr-1">Siguiente</span>
                                <svg
                                    class="h-5 w-5"
                                    viewBox="0 0 20 20"
                                    fill="currentColor"
                                    aria-hidden="true"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                                <span class="sr-only">Siguiente</span>
                            </Link>
                        </div>
                    </template>
                </nav>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link } from "@inertiajs/vue3";
import { computed } from "vue";

const props = defineProps({
    links: {
        type: Array,
        required: true,
    },
});

// Si tienes los datos de paginación completos
const from = computed(() => {
    if (props.links && props.links.length > 0) {
        // Esto asume que tu respuesta de API tiene la estructura de Laravel
        const currentPage = props.links.find((link) => link.active)?.label;
        if (currentPage) {
            const perPage = 10; // Ajusta según tu configuración
            const page = parseInt(currentPage);
            return (page - 1) * perPage + 1;
        }
    }
    return 0;
});

const to = computed(() => {
    if (props.links && props.links.length > 0) {
        const currentPage = props.links.find((link) => link.active)?.label;
        if (currentPage) {
            const perPage = 10;
            const page = parseInt(currentPage);
            return page * perPage;
        }
    }
    return 0;
});

const total = computed(() => {
    // Esto deberías pasarlo como prop desde el padre
    return props.total || 0;
});
</script>
