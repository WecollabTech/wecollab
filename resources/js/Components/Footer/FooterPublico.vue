<script setup>
import { Link } from "@inertiajs/vue3";
import { ref } from "vue";
import WhatsAppButton from "./WhatsAppButton.vue";

const email = ref("");
const isSubmitting = ref(false);

const submitNewsletter = () => {
    if (!email.value) return;

    isSubmitting.value = true;

    setTimeout(() => {
        isSubmitting.value = false;
        email.value = "";
        alert("¡Gracias por suscribirte!");
    }, 1500);
};

const socialLinks = [
    { icon: "fa-brands fa-facebook-f", url: "#", label: "Facebook" },
    { icon: "fa-brands fa-instagram", url: "#", label: "Instagram" },
    { icon: "fa-brands fa-linkedin-in", url: "#", label: "LinkedIn" },
    { icon: "fa-brands fa-tiktok", url: "#", label: "TikTok" },
];

const services = [
    { name: "Desarrollo Web", href: "#" },
    { name: "Desarrollo de Software", href: "#" },
    { name: "Consultoría Tecnológica", href: "#" },
    { name: "Automatización Empresarial", href: "#" },
];

const company = [
    { name: "Sobre Nosotros", href: "#" },
    { name: "Nuestro Equipo", href: "#" },
    { name: "Casos de Éxito", href: "#" },
    { name: "Blog Tecnológico", href: "#" },
];

const resources = [
    { name: "Centro de Ayuda", href: "#" },
    { name: "Documentación", href: "#" },
    { name: "Preguntas Frecuentes", href: "#" },
    { name: "Soporte", href: "#" },
];

const legal = [
    { name: "Política de Privacidad", href: "#" },
    { name: "Términos y Condiciones", href: "#" },
    { name: "Cookies", href: "#" },
];
</script>

<template>
    <footer class="bg-[#1f2f6b] text-white">
        <!-- FRANJA SUPERIOR -->
        <div
            class="h-[10px] w-full bg-gradient-to-r from-[#1f2f6b] via-[#28023d] to-[#1f2f6b]"
        ></div>

        <div class="max-w-[1500px] mx-auto px-6 lg:px-12 py-16">
            <!-- GRID -->
            <div
                class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-x-12 gap-y-14"
            >
                <!-- LOGO + INFO -->
                <div class="space-y-6">
                    <!-- LOGO -->
                    <Link href="/" class="flex items-center gap-4 group w-fit">
                        <!-- Logo container -->
                        <div class="relative flex items-center justify-center">
                            <!-- Glow -->
                            <div
                                class="absolute -inset-3 rounded-2xl bg-purple-600/30 blur-xl opacity-0 group-hover:opacity-70 transition-all duration-500"
                            ></div>

                            <!-- Logo -->
                            <img
                                src="/img/Logo.png"
                                alt="SLC Platform"
                                class="relative h-14 w-auto object-contain transition duration-500 group-hover:scale-110"
                                loading="lazy"
                            />
                        </div>

                        <!-- Brand -->
                        <div class="flex flex-col leading-tight">
                            <span
                                class="text-3xl font-black tracking-tight text-white group-hover:text-transparent group-hover:bg-clip-text group-hover:bg-gradient-to-r group-hover:from-indigo-300 group-hover:via-purple-300 group-hover:to-indigo-300 transition-all duration-500"
                            >
                                SLC
                            </span>

                            <span
                                class="text-[11px] uppercase tracking-[0.25em] font-semibold text-purple-300/80 hidden sm:block"
                            >
                                Learning Platform
                            </span>
                        </div>
                    </Link>

                    <!-- Descripción -->
                    <p
                        class="text-xs text-gray-400 text-center mt-6 max-w-md mx-auto leading-relaxed"
                    >
                        Plataforma tecnológica de capacitación empresarial
                        desarrollada por
                        <span class="font-semibold text-white">We Collab</span>
                        para impulsar el crecimiento profesional.
                    </p>

                    <!-- Redes -->
                    <div class="flex items-center gap-3 pt-2">
                        <a
                            v-for="social in socialLinks"
                            :key="social.label"
                            :href="social.url"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="group relative w-10 h-10 flex items-center justify-center rounded-xl bg-white/5 border border-white/10 hover:bg-[#f28c28] hover:border-[#f28c28] transition-all duration-300 hover:scale-110"
                        >
                            <!-- Glow -->
                            <div
                                class="absolute inset-0 rounded-xl bg-[#f28c28]/40 blur-md opacity-0 group-hover:opacity-100 transition"
                            ></div>

                            <!-- Icon -->
                            <i
                                :class="social.icon"
                                class="relative text-gray-300 group-hover:text-white transition"
                            ></i>
                        </a>
                    </div>
                </div>

                <!-- SERVICIOS -->
                <div>
                    <h4
                        class="text-lg font-semibold tracking-wide mb-6 flex items-center gap-2"
                    >
                        <span class="w-1 h-6 bg-[#f28c28] rounded-full"></span>
                        Servicios
                    </h4>

                    <ul class="space-y-3">
                        <li v-for="item in services" :key="item.name">
                            <Link
                                :href="item.href"
                                class="text-sm text-gray-300 hover:text-[#f28c28] transition-colors duration-300 relative group inline-block"
                            >
                                {{ item.name }}

                                <span
                                    class="absolute -bottom-1 left-0 w-0 h-[2px] bg-[#f28c28] transition-all duration-300 group-hover:w-full"
                                ></span>
                            </Link>
                        </li>
                    </ul>
                </div>

                <!-- EMPRESA -->
                <div>
                    <h4
                        class="text-lg font-semibold tracking-wide mb-6 flex items-center gap-2"
                    >
                        <span class="w-1 h-6 bg-[#3b2f9c] rounded-full"></span>
                        Empresa
                    </h4>

                    <ul class="space-y-3">
                        <li v-for="item in company" :key="item.name">
                            <Link
                                :href="item.href"
                                class="text-sm text-gray-300 hover:text-[#f28c28] transition-colors duration-300 relative group inline-block"
                            >
                                {{ item.name }}

                                <span
                                    class="absolute -bottom-1 left-0 w-0 h-[2px] bg-[#f28c28] transition-all duration-300 group-hover:w-full"
                                ></span>
                            </Link>
                        </li>
                    </ul>
                </div>

                <!-- RECURSOS -->
                <div>
                    <h4
                        class="text-lg font-semibold tracking-wide mb-6 flex items-center gap-2"
                    >
                        <span class="w-1 h-6 bg-[#25D366] rounded-full"></span>
                        Recursos
                    </h4>

                    <ul class="space-y-3">
                        <li v-for="item in resources" :key="item.name">
                            <Link
                                :href="item.href"
                                class="text-sm text-gray-300 hover:text-[#f28c28] transition-colors duration-300 relative group inline-block"
                            >
                                {{ item.name }}

                                <span
                                    class="absolute -bottom-1 left-0 w-0 h-[2px] bg-[#f28c28] transition-all duration-300 group-hover:w-full"
                                ></span>
                            </Link>
                        </li>
                    </ul>
                </div>

                <!-- CONTACTO -->
                <div>
                    <h4 class="text-lg font-semibold mb-6">Contacto</h4>

                    <ul class="space-y-5">
                        <li class="flex items-start gap-3 group">
                            <div
                                class="w-9 h-9 rounded-lg bg-white/10 flex items-center justify-center text-[#f28c28] group-hover:bg-[#f28c28] group-hover:text-white transition"
                            >
                                <i class="fa-solid fa-location-dot"></i>
                            </div>

                            <div>
                                <p class="text-sm font-medium">
                                    Oficinas Centrales
                                </p>

                                <p
                                    class="text-xs text-gray-300 mt-1 leading-relaxed"
                                >
                                    Primo Feliciano Velazquez 3930<br />
                                    Guadalajara, Jalisco
                                </p>
                            </div>
                        </li>

                        <li
                            class="flex items-center gap-3 group cursor-pointer"
                        >
                            <div
                                class="w-9 h-9 rounded-lg bg-white/10 flex items-center justify-center text-[#25D366] group-hover:bg-[#25D366] group-hover:text-white transition"
                            >
                                <i class="fa-brands fa-whatsapp"></i>
                            </div>

                            <span
                                class="text-sm text-gray-300 group-hover:text-white transition"
                            >
                                +52 33 5121 3546
                            </span>
                        </li>

                        <li
                            class="flex items-center gap-3 group cursor-pointer"
                        >
                            <div
                                class="w-9 h-9 rounded-lg bg-white/10 flex items-center justify-center text-[#f28c28] group-hover:bg-[#f28c28] group-hover:text-white transition"
                            >
                                <i class="fa-solid fa-envelope"></i>
                            </div>

                            <span
                                class="text-sm text-gray-300 group-hover:text-white transition"
                            >
                                contacto@we-collab.tech
                            </span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- NEWSLETTER -->
            <!-- <div class="mt-16">
                <div class="bg-white/5 border border-white/10 rounded-xl p-8">
                    <div
                        class="flex flex-col md:flex-row justify-between items-center gap-6"
                    >
                        <div>
                            <h3 class="text-xl font-semibold mb-1">
                                Suscríbete a nuestro newsletter
                            </h3>

                            <p class="text-sm text-gray-300">
                                Recibe noticias sobre tecnología e innovación.
                            </p>
                        </div>

                        <form
                            @submit.prevent="submitNewsletter"
                            class="flex gap-2 w-full md:w-auto"
                        >
                            <input
                                v-model="email"
                                type="email"
                                placeholder="Tu correo electrónico"
                                class="px-4 py-3 rounded-lg bg-[#1f2f6b] border border-white/20 text-white placeholder-gray-400 focus:outline-none focus:border-[#f28c28] transition w-full md:w-[280px]"
                            />

                            <button
                                :disabled="isSubmitting"
                                class="bg-[#f28c28] px-6 py-3 rounded-lg font-semibold hover:bg-[#ff9d3f] transition"
                            >
                                <span v-if="isSubmitting">
                                    <i
                                        class="fa-solid fa-circle-notch fa-spin"
                                    ></i>
                                </span>

                                <span v-else> Suscribirse </span>
                            </button>
                        </form>
                    </div>
                </div>
            </div> -->

            <!-- FOOTER INFERIOR -->
            <div class="border-t border-white/10 mt-12 pt-8">
                <div
                    class="flex flex-col md:flex-row justify-between items-center gap-4 text-xs text-gray-400"
                >
                    <p>© {{ new Date().getFullYear() }} We Collab Tech</p>

                    <div class="flex gap-6 flex-wrap justify-center">
                        <Link
                            v-for="item in legal"
                            :key="item.name"
                            :href="item.href"
                            class="hover:text-[#f28c28] transition"
                        >
                            {{ item.name }}
                        </Link>
                    </div>
                </div>
            </div>
        </div>

        <WhatsAppButton
            phone="523351213546"
            message="Hola, quiero información sobre los servicios de We Collab."
        />
    </footer>
</template>
