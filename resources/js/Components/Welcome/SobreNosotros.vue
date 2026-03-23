<script setup>
import { ref } from "vue";

const props = defineProps({
    title: { type: String, default: "Sobre Nosotros" },
    subtitle: { type: String, default: "Conoce nuestra esencia" },
    mission: {
        type: String,
        default:
            "Transformamos la operación de las empresas diseñando sistemas inteligentes basados en Bitrix24, guiados por inteligencia artificial y construidos con metodología profesional. Nuestra misión es generar orden, claridad y eficiencia real para que los equipos tomen mejores decisiones, más rápido y con datos confiables.",
    },
    vision: {
        type: String,
        default: "Ser líderes globales en formación tecnológica.",
    },
    values: { type: String, default: "Innovación, excelencia y compromiso." },
    imageSrc: { type: String, default: "/img/wecollab.png" },
    buttonText: { type: String, default: "Conoce Más" },
    buttonLink: { type: String, default: "https://we-collab.tech/" },
    openInNewTab: { type: Boolean, default: true },
    stats: {
        type: Array,
        default: () => [
            { number: "5+", label: "Años de Experiencia" },
            { number: "500+", label: "Empresas Confían" },
            { number: "98%", label: "Satisfacción" },
        ],
    },
});

const navigateTo = () => {
    if (props.openInNewTab) {
        window.open(props.buttonLink, "_blank", "noopener,noreferrer");
    } else {
        window.location.href = props.buttonLink;
    }
};

const isImageLoaded = ref(false);
</script>

<template>
    <!-- 🔥 CONTENEDOR GLOBAL -->
    <div class="w-full mb-16">
        <div
            class="bg-gradient-to-r from-gray-50 via-white to-gray-100 rounded-3xl p-6 sm:p-8 lg:p-10 shadow-xl border border-gray-200 relative overflow-hidden"
        >
            <!-- Sección con márgenes mínimos -->
            <div class="relative py-2 sm:py-4 px-2 sm:px-4 overflow-hidden">
                <!-- Elementos decorativos de fondo -->
                <div class="absolute inset-0 pointer-events-none">
                    <div
                        class="absolute top-10 left-10 w-64 h-64 bg-cyan-200/15 rounded-full blur-3xl"
                    ></div>
                    <div
                        class="absolute bottom-10 right-10 w-80 h-80 bg-purple-200/15 rounded-full blur-3xl"
                    ></div>
                </div>

                <div class="relative max-w-7xl mx-auto">
                    <!-- HEADER PRINCIPAL -->
                    <div class="text-center mb-6 sm:mb-8">
                        <span
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-gradient-to-r from-cyan-100 to-cyan-200 text-cyan-800 text-sm font-semibold mb-3 shadow-sm"
                        >
                            <i class="fas fa-sparkles text-cyan-600"></i>
                            {{ subtitle }}
                        </span>

                        <h2
                            class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-gray-900 mb-3"
                        >
                            {{ title }}
                        </h2>

                        <div
                            class="w-20 h-1.5 bg-gradient-to-r from-cyan-500 via-purple-500 to-cyan-500 mx-auto rounded-full"
                        ></div>
                    </div>

                    <!-- CONTENIDO PRINCIPAL (2 Columnas) -->
                    <div
                        class="flex flex-col lg:flex-row items-start gap-6 lg:gap-10"
                    >
                        <!-- COLUMNA IZQUIERDA: IMAGEN + STATS -->
                        <div class="w-full lg:w-1/2 space-y-4">
                            <div class="relative">
                                <div
                                    class="absolute -inset-1 bg-gradient-to-r from-cyan-400 via-purple-400 to-cyan-400 rounded-3xl opacity-100 blur-sm"
                                ></div>

                                <div
                                    class="relative bg-white p-1.5 rounded-3xl shadow-2xl"
                                >
                                    <div
                                        class="relative overflow-hidden rounded-2xl bg-gray-100"
                                    >
                                        <div
                                            v-if="!isImageLoaded"
                                            class="absolute inset-0 bg-gray-200 animate-pulse"
                                        ></div>
                                        <img
                                            :src="imageSrc"
                                            alt="Equipo de We Collab"
                                            class="w-full h-48 sm:h-56 lg:h-64 object-contain p-4 sm:p-6 transition-transform duration-500 hover:scale-105"
                                            loading="lazy"
                                            @load="isImageLoaded = true"
                                        />
                                    </div>
                                </div>

                                <div
                                    class="absolute -top-4 -left-4 w-8 h-8 bg-cyan-400 rounded-full opacity-40"
                                ></div>
                                <div
                                    class="absolute -bottom-2 -right-2 w-6 h-6 bg-purple-400 rounded-full opacity-40"
                                ></div>
                            </div>

                            <!-- Estadísticas -->
                            <div class="grid grid-cols-3 gap-3">
                                <div
                                    v-for="(stat, index) in stats"
                                    :key="index"
                                    class="text-center p-4 bg-white/80 backdrop-blur-sm rounded-xl shadow-md border border-gray-100 hover:shadow-lg transition-shadow duration-300"
                                >
                                    <p
                                        class="text-2xl sm:text-3xl font-extrabold bg-gradient-to-r from-cyan-600 to-purple-600 bg-clip-text text-transparent"
                                    >
                                        {{ stat.number }}
                                    </p>
                                    <p class="text-sm text-gray-600 mt-1">
                                        {{ stat.label }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- COLUMNA DERECHA: CONTENIDO -->
                        <div class="w-full lg:w-1/2 space-y-2">
                            <!-- MISIÓN -->
                            <div
                                class="relative bg-gradient-to-br from-cyan-50 to-purple-50 rounded-2xl p-5 border border-cyan-200 shadow-lg"
                            >
                                <div
                                    class="absolute -top-2.5 -left-2.5 w-9 h-9 bg-gradient-to-br from-cyan-500 to-purple-600 rounded-full flex items-center justify-center shadow-lg"
                                >
                                    <i
                                        class="fas fa-bullseye text-white text-sm"
                                    ></i>
                                </div>

                                <h3
                                    class="text-lg font-extrabold text-cyan-800 mb-3 flex items-center gap-2"
                                >
                                    <span
                                        class="w-8 h-0.5 bg-cyan-500 rounded-full"
                                    ></span>
                                    Nuestra Misión
                                </h3>

                                <p
                                    class="text-base text-gray-700 leading-relaxed"
                                >
                                    {{ mission }}
                                </p>

                                <div class="flex flex-wrap gap-2 mt-3">
                                    <span
                                        v-for="keyword in [
                                            'Bitrix24',
                                            'IA',
                                            'Metodología',
                                            'Eficiencia',
                                            'Datos',
                                        ]"
                                        :key="keyword"
                                        class="px-3 py-1 bg-white/70 backdrop-blur-sm rounded-full text-xs font-medium text-cyan-700 border border-cyan-200 shadow-sm hover:bg-cyan-100 transition-colors duration-200 cursor-default"
                                    >
                                        {{ keyword }}
                                    </span>
                                </div>
                            </div>

                            <!-- VISIÓN Y VALORES -->
                            <div class="grid gap-3 sm:grid-cols-2">
                                <!-- Visión -->
                                <div
                                    class="group bg-white/90 backdrop-blur-sm rounded-2xl p-5 shadow-lg border border-gray-100 hover:border-cyan-300 hover:shadow-xl transition-all duration-300"
                                >
                                    <div class="flex items-start gap-4">
                                        <div
                                            class="w-12 h-12 bg-gradient-to-br from-cyan-500 to-cyan-600 rounded-xl flex items-center justify-center flex-shrink-0 shadow-md group-hover:scale-110 transition-transform duration-300"
                                        >
                                            <i
                                                class="fas fa-eye text-white text-lg"
                                            ></i>
                                        </div>
                                        <div>
                                            <h3
                                                class="font-bold text-gray-900 mb-1.5 text-base"
                                            >
                                                Visión
                                            </h3>
                                            <p
                                                class="text-sm text-gray-600 leading-relaxed"
                                            >
                                                {{ vision }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Valores -->
                                <div
                                    class="group bg-white/90 backdrop-blur-sm rounded-2xl p-5 shadow-lg border border-gray-100 hover:border-purple-300 hover:shadow-xl transition-all duration-300"
                                >
                                    <div class="flex items-start gap-4">
                                        <div
                                            class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center flex-shrink-0 shadow-md group-hover:scale-110 transition-transform duration-300"
                                        >
                                            <i
                                                class="fas fa-heart text-white text-lg"
                                            ></i>
                                        </div>
                                        <div>
                                            <h3
                                                class="font-bold text-gray-900 mb-1.5 text-base"
                                            >
                                                Valores
                                            </h3>
                                            <p
                                                class="text-sm text-gray-600 leading-relaxed"
                                            >
                                                {{ values }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- BENEFICIOS + CTA -->
                            <div
                                class="bg-white/60 backdrop-blur-sm rounded-2xl p-5 border border-gray-100 shadow-sm"
                            >
                                <div
                                    class="flex flex-col sm:flex-row sm:items-center gap-4"
                                >
                                    <div class="flex-1">
                                        <h4
                                            class="font-bold text-gray-800 mb-3 text-base"
                                        >
                                            <i
                                                class="fas fa-star text-yellow-500 mr-2"
                                            ></i>
                                            ¿Por qué elegirnos?
                                        </h4>
                                        <div class="space-y-2">
                                            <div
                                                v-for="(item, index) in [
                                                    'Capacitación 100% práctica',
                                                    'Soporte personalizado',
                                                    'Certificación incluida',
                                                ]"
                                                :key="index"
                                                class="flex items-center gap-2.5 text-gray-700"
                                            >
                                                <div
                                                    class="w-5 h-5 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0"
                                                >
                                                    <i
                                                        class="fas fa-check text-green-600 text-sm"
                                                    ></i>
                                                </div>
                                                <span
                                                    class="text-sm font-medium"
                                                    >{{ item }}</span
                                                >
                                            </div>
                                        </div>
                                    </div>

                                    <div
                                        class="hidden sm:block w-px h-16 bg-gray-200"
                                    ></div>

                                    <div
                                        class="flex-shrink-0 text-center sm:text-right"
                                    >
                                        <button
                                            @click="navigateTo"
                                            class="group relative inline-flex items-center justify-center gap-2 bg-gradient-to-r from-cyan-600 via-cyan-500 to-purple-600 text-white px-6 py-3 rounded-full font-semibold text-base shadow-lg hover:shadow-cyan-500/40 transform hover:-translate-y-0.5 transition-all duration-300 overflow-hidden focus:outline-none focus:ring-4 focus:ring-cyan-300/50 w-full sm:w-auto"
                                            :aria-label="buttonText"
                                        >
                                            <span
                                                class="relative flex items-center gap-2"
                                            >
                                                {{ buttonText }}
                                                <i
                                                    class="fas fa-arrow-right group-hover:translate-x-1 transition-transform duration-300"
                                                ></i>
                                            </span>
                                        </button>
                                        <p
                                            class="mt-2 text-sm text-gray-500 flex items-center gap-1.5 justify-center sm:justify-end"
                                        >
                                            <i
                                                class="fas fa-shield-check text-green-500"
                                            ></i>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- FIN CONTENIDO PRINCIPAL -->
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
@media (prefers-reduced-motion: no-preference) {
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    .animate-fade-in-up {
        animation: fadeInUp 0.6s ease-out forwards;
    }
}

.hover-lift {
    transition:
        transform 0.3s ease,
        box-shadow 0.3s ease;
}
.hover-lift:hover {
    transform: translateY(-4px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
}
</style>
