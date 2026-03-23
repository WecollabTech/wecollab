<script setup>
import { ref, onMounted, computed } from "vue";

const isOpen = ref(false);
const showTooltip = ref(true);
const department = ref("bitrix24");
const studentName = ref("");

const agent = {
    name: "Equipo SLC",
    role: "Soporte & Implementación",
    avatar: "/img/Logo.png",
    status: "En línea",
    responseTime: "2 min",
};

// ✅ Teléfono de WhatsApp SLC
const phone = "523351213546";

// ✅ Departamentos actualizados para SLC Platform
const departments = [
    {
        name: "Implementación Bitrix24",
        value: "bitrix24",
        icon: "🚀",
        description: "Configuración y puesta en marcha de Bitrix24",
    },
    {
        name: "Soporte técnico",
        value: "soporte",
        icon: "🔧",
        description: "Asistencia técnica y resolución de incidencias",
    },
    {
        name: "Licenciamientos",
        value: "licenciamientos",
        icon: "📋",
        description: "Información sobre licencias y planes disponibles",
    },
    {
        name: "Mejoras en la Plataforma SLC",
        value: "mejoras",
        icon: "✨",
        description: "Sugerencias y solicitudes de nuevas funcionalidades",
    },
];

// ✅ Detectar página actual para contexto del mensaje
const currentPage = computed(() => {
    return window.location.pathname;
});

// ✅ Detectar categoría de curso si está en una página de curso
const currentCourse = computed(() => {
    const path = currentPage.value;
    if (path.includes("/marketing")) return "Marketing Digital";
    if (path.includes("/ventas")) return "Ventas Profesionales";
    if (path.includes("/liderazgo")) return "Liderazgo";
    if (path.includes("/tecnologia")) return "Tecnología";
    if (path.includes("/emprendimiento")) return "Emprendimiento";
    return null;
});

// ✅ Generar mensaje personalizado para WhatsApp (Formato solicitado)
const openWhatsApp = () => {
    // 1. Saludo con nombre (opcional)
    const saludo = studentName.value
        ? `Hola, soy ${studentName.value}.`
        : "Hola,";

    // 2. Contexto fijo: origen del contacto
    const origen = "Los contacto desde el sistema SLC.";

    // 3. Necesidad: Formato simple "Necesito + Nombre del Departamento"
    const selectedDept = departments.find((d) => d.value === department.value);
    const necesidad = `Necesito ${selectedDept?.name || "información"}.`;

    // 4. Contexto adicional de página (opcional)
    let contextoPagina = "";
    if (currentPage.value.includes("/courses")) {
        contextoPagina = "Estoy explorando el catálogo de cursos.";
    } else if (currentPage.value.includes("/pricing")) {
        contextoPagina = "Estoy viendo los planes de suscripción.";
    } else if (currentPage.value.includes("/certifications")) {
        contextoPagina = "Me interesa saber más sobre las certificaciones.";
    }

    // ✅ Armar mensaje final con saltos de línea para mejor legibilidad en WhatsApp
    const mensaje = contextoPagina
        ? `${saludo}\n${origen}\n${necesidad}\n${contextoPagina}`
        : `${saludo}\n${origen}\n${necesidad}`;

    // 🔗 Construir URL de WhatsApp
    const url = `https://wa.me/${phone}?text=${encodeURIComponent(mensaje)}`;

    // 📊 Tracking simple (opcional)
    console.log("WhatsApp chat opened:", {
        department: department.value,
        studentName: studentName.value,
        course: currentCourse.value,
    });

    // 🚀 Abrir WhatsApp en nueva pestaña
    window.open(url, "_blank", "noopener,noreferrer");

    // ❌ Cerrar el chat después de abrir WhatsApp
    setTimeout(() => {
        isOpen.value = false;
    }, 500);
};

const toggleChat = () => {
    isOpen.value = !isOpen.value;
    if (isOpen.value) {
        showTooltip.value = false;
    }
};

onMounted(() => {
    // Ocultar tooltip después de 6 segundos
    setTimeout(() => {
        showTooltip.value = false;
    }, 6000);
});
</script>

<template>
    <div class="fixed bottom-6 right-6 z-50 flex flex-col items-end gap-4">
        <!-- 🪟 Chat Window -->
        <transition
            enter-active-class="transition-all duration-300 ease-out"
            enter-from-class="opacity-0 translate-y-4 scale-95"
            enter-to-class="opacity-100 translate-y-0 scale-100"
            leave-active-class="transition-all duration-200 ease-in"
            leave-from-class="opacity-100 translate-y-0 scale-100"
            leave-to-class="opacity-0 translate-y-4 scale-95"
        >
            <div
                v-if="isOpen"
                class="w-80 sm:w-96 max-w-[92vw] bg-white dark:bg-gray-900 rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-700 overflow-hidden"
            >
                <!-- Header con colores SLC -->
                <div
                    class="bg-gradient-to-r from-[#200c3e] to-[#5B2DA3] text-white p-4 flex items-center gap-3"
                >
                    <div class="relative">
                        <img
                            :src="agent.avatar"
                            :alt="agent.name"
                            class="w-10 h-10 rounded-full border-2 border-white/80 object-contain bg-white p-0.5"
                            loading="lazy"
                        />
                        <span
                            class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-green-400 border-2 border-[#200c3e] rounded-full"
                        ></span>
                    </div>

                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-sm truncate">
                            {{ agent.name }}
                        </p>
                        <p class="text-xs flex items-center gap-1.5 opacity-90">
                            <span
                                class="w-1.5 h-1.5 bg-green-400 rounded-full animate-pulse"
                            ></span>
                            {{ agent.status }} • Respuesta en
                            {{ agent.responseTime }}
                        </p>
                    </div>

                    <button
                        @click="toggleChat"
                        class="p-1.5 hover:bg-white/10 rounded-lg transition"
                        aria-label="Cerrar chat"
                    >
                        <svg
                            class="w-4 h-4"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                    </button>
                </div>

                <!-- Contenido del Chat -->
                <div
                    class="p-4 sm:p-5 text-sm text-gray-600 dark:text-gray-300 space-y-4 max-h-80 overflow-y-auto"
                >
                    <!-- Mensaje de bienvenida -->
                    <div
                        class="bg-gray-50 dark:bg-gray-800 rounded-xl p-3 border border-gray-100 dark:border-gray-700"
                    >
                        <p
                            class="font-medium text-gray-800 dark:text-white mb-1"
                        >
                            👋 ¡Hola! Bienvenido a SLC Platform
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            ¿En qué podemos ayudarte hoy?
                        </p>
                    </div>

                    <!-- Input para nombre (opcional) -->
                    <div>
                        <label
                            class="text-xs text-gray-500 dark:text-gray-400 mb-1.5 block"
                        >
                            Tu nombre (opcional)
                        </label>
                        <input
                            v-model="studentName"
                            type="text"
                            placeholder="Ej. José García"
                            class="w-full px-3 py-2 text-sm border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                        />
                    </div>

                    <!-- Selector de Departamento -->
                    <div>
                        <p
                            class="text-xs text-gray-500 dark:text-gray-400 mb-2"
                        >
                            Selecciona el área de atención
                        </p>

                        <div class="space-y-2">
                            <button
                                v-for="dep in departments"
                                :key="dep.value"
                                @click="department = dep.value"
                                class="w-full text-left p-3 rounded-xl border-2 transition-all duration-200 group"
                                :class="
                                    department === dep.value
                                        ? 'border-indigo-600 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-900 dark:text-indigo-100'
                                        : 'border-gray-200 dark:border-gray-700 hover:border-indigo-300 dark:hover:border-indigo-600 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300'
                                "
                            >
                                <div class="flex items-center gap-3">
                                    <span class="text-lg">{{ dep.icon }}</span>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-medium text-sm">
                                            {{ dep.name }}
                                        </p>
                                        <p
                                            class="text-xs text-gray-500 dark:text-gray-400 truncate"
                                        >
                                            {{ dep.description }}
                                        </p>
                                    </div>
                                    <span
                                        class="w-5 h-5 rounded-full border-2 flex items-center justify-center transition-colors"
                                        :class="
                                            department === dep.value
                                                ? 'border-indigo-600 bg-indigo-600'
                                                : 'border-gray-300 dark:border-gray-600'
                                        "
                                    >
                                        <span
                                            v-if="department === dep.value"
                                            class="w-2 h-2 bg-white rounded-full"
                                        ></span>
                                    </span>
                                </div>
                            </button>
                        </div>
                    </div>

                    <!-- ✅ Servicios según departamento -->
                    <div
                        class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-3 border border-gray-100 dark:border-gray-700"
                    >
                        <p
                            class="text-xs font-medium text-gray-700 dark:text-gray-300 mb-2"
                        >
                            {{
                                department === "bitrix24"
                                    ? "🚀 Servicios de implementación:"
                                    : department === "soporte"
                                      ? "🔧 Soporte técnico:"
                                      : department === "licenciamientos"
                                        ? "📋 Opciones de licenciamiento:"
                                        : "✨ Mejoras y sugerencias:"
                            }}
                        </p>
                        <ul
                            class="text-xs text-gray-500 dark:text-gray-400 space-y-1.5"
                        >
                            <template v-if="department === 'bitrix24'">
                                <li class="flex items-start gap-2">
                                    <span class="text-indigo-500 mt-0.5"
                                        >•</span
                                    >
                                    <span
                                        >Diagnóstico inicial y
                                        planificación</span
                                    >
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-indigo-500 mt-0.5"
                                        >•</span
                                    >
                                    <span
                                        >Configuración de flujos de
                                        trabajo</span
                                    >
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-indigo-500 mt-0.5"
                                        >•</span
                                    >
                                    <span>Capacitación y acompañamiento</span>
                                </li>
                            </template>
                            <template v-else-if="department === 'soporte'">
                                <li class="flex items-start gap-2">
                                    <span class="text-indigo-500 mt-0.5"
                                        >•</span
                                    >
                                    <span
                                        >Problemas de acceso o contraseña</span
                                    >
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-indigo-500 mt-0.5"
                                        >•</span
                                    >
                                    <span
                                        >Errores en la plataforma o
                                        integración</span
                                    >
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-indigo-500 mt-0.5"
                                        >•</span
                                    >
                                    <span
                                        >Recuperación de datos o
                                        configuraciones</span
                                    >
                                </li>
                            </template>
                            <template
                                v-else-if="department === 'licenciamientos'"
                            >
                                <li class="flex items-start gap-2">
                                    <span class="text-indigo-500 mt-0.5"
                                        >•</span
                                    >
                                    <span>Planes por usuario o empresa</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-indigo-500 mt-0.5"
                                        >•</span
                                    >
                                    <span
                                        >Renovación y actualización de
                                        licencias</span
                                    >
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-indigo-500 mt-0.5"
                                        >•</span
                                    >
                                    <span>Facturación y métodos de pago</span>
                                </li>
                            </template>
                            <template v-else>
                                <li class="flex items-start gap-2">
                                    <span class="text-indigo-500 mt-0.5"
                                        >•</span
                                    >
                                    <span>Reporte de bugs o errores</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-indigo-500 mt-0.5"
                                        >•</span
                                    >
                                    <span
                                        >Solicitud de nuevas
                                        funcionalidades</span
                                    >
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-indigo-500 mt-0.5"
                                        >•</span
                                    >
                                    <span
                                        >Mejoras en experiencia de usuario</span
                                    >
                                </li>
                            </template>
                        </ul>
                    </div>

                    <!-- Tiempo de respuesta -->
                    <p
                        class="text-xs text-gray-400 dark:text-gray-500 flex items-center gap-1.5"
                    >
                        <svg
                            class="w-3.5 h-3.5"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                        </svg>
                        Tiempo promedio de respuesta:
                        <span
                            class="font-medium text-gray-600 dark:text-gray-300"
                            >{{ agent.responseTime }}</span
                        >
                    </p>
                </div>

                <!-- Botón de Acción -->
                <div
                    class="p-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50"
                >
                    <button
                        @click="openWhatsApp"
                        class="w-full bg-[#25D366] hover:bg-[#20bd5a] text-white py-3 rounded-xl font-semibold transition-all duration-200 flex items-center justify-center gap-2 shadow-lg shadow-green-500/25 hover:shadow-xl hover:-translate-y-0.5 active:translate-y-0"
                    >
                        <svg
                            class="w-5 h-5"
                            fill="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.095 3.2 5.076 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"
                            />
                        </svg>
                        Chatear por WhatsApp
                    </button>

                    <p
                        class="text-[10px] text-center text-gray-400 dark:text-gray-500 mt-2"
                    >
                        🔒 Tus datos están protegidos. Respuesta garantizada.
                    </p>
                </div>
            </div>
        </transition>

        <!-- 💬 Tooltip flotante -->
        <transition
            enter-active-class="transition-all duration-300"
            enter-from-class="opacity-0 translate-y-2"
            enter-to-class="opacity-100 translate-y-0"
            leave-active-class="transition-all duration-200"
            leave-from-class="opacity-100 translate-y-0"
            leave-to-class="opacity-0 translate-y-2"
        >
            <div
                v-if="showTooltip && !isOpen"
                class="bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-200 text-sm px-4 py-2.5 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700 flex items-center gap-2 animate-bounce-once"
            >
                <span class="text-lg">💬</span>
                <span>¿Necesitas ayuda con tu proyecto?</span>
            </div>
        </transition>

        <!-- 🟢 Botón Flotante Principal -->
        <button
            @click="toggleChat"
            class="relative group w-16 h-16 bg-[#25D366] hover:bg-[#20bd5a] rounded-full shadow-xl flex items-center justify-center text-white text-2xl transition-all duration-300 hover:scale-110 active:scale-95 focus:outline-none focus:ring-4 focus:ring-green-500/30"
            aria-label="Abrir chat de WhatsApp"
            aria-expanded="isOpen"
        >
            <!-- Ícono WhatsApp -->
            <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24">
                <path
                    d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.095 3.2 5.076 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"
                />
            </svg>

            <!-- Efecto de pulso -->
            <span
                class="absolute inset-0 rounded-full bg-green-400 opacity-70 animate-ping"
            ></span>
        </button>
    </div>
</template>

<style scoped>
/* Animación personalizada para el tooltip */
@keyframes bounce-once {
    0%,
    100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-8px);
    }
}
.animate-bounce-once {
    animation: bounce-once 2s ease-in-out 1;
}

/* Scroll suave para el contenido del chat */
.overflow-y-auto::-webkit-scrollbar {
    width: 4px;
}
.overflow-y-auto::-webkit-scrollbar-track {
    background: transparent;
}
.overflow-y-auto::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}
.dark .overflow-y-auto::-webkit-scrollbar-thumb {
    background: #475569;
}

/* Accesibilidad: focus visible */
button:focus-visible {
    outline: 2px solid #6366f1;
    outline-offset: 2px;
}

/* Reducir movimiento para usuarios con preferencia */
@media (prefers-reduced-motion: reduce) {
    * {
        animation-duration: 0.01ms !important;
        transition-duration: 0.01ms !important;
    }
}
</style>
