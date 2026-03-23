<script setup>
import { Link, usePage } from "@inertiajs/vue3";
import { ref, computed, onMounted, onUnmounted } from "vue";
import FooterPublico from "@/Components/Footer/FooterPublico.vue";

const props = defineProps({
    canLogin: { type: Boolean, default: true },
    canRegister: { type: Boolean, default: true },
    showFooter: { type: Boolean, default: true },
});

const page = usePage();

// Páginas donde se muestran los headers
const fullHeaderPages = [""]; // Puedes añadir más
const promoPages = ["/", "/welcome"];

// Computed para headers
const showFullHeader = computed(() => fullHeaderPages.includes(page.url));
const showPromoHeader = computed(() => promoPages.includes(page.url));

const isLoginPage = computed(() => page.url.includes("login"));
const isRegisterPage = computed(() => page.url.includes("register"));

const isLoaded = ref(false);
const mobileMenuOpen = ref(false);

// Padding dinámico según headers
const headerPadding = {
    both: "pt-[150px] sm:pt-[160px] lg:pt-[190px]",
    full: "pt-[110px] sm:pt-[120px] lg:pt-[150px]",
    promo: "pt-[90px] sm:pt-[100px] lg:pt-[120px]",
    none: "pt-[70px] sm:pt-[80px] lg:pt-[100px]",
};

const mainPadding = computed(() => {
    if (showFullHeader.value && showPromoHeader.value)
        return headerPadding.both;
    if (showFullHeader.value) return headerPadding.full;
    if (showPromoHeader.value) return headerPadding.promo;
    return headerPadding.none;
});

// Items de navegación
const navItems = [
    { name: "Inicio", href: "/", icon: "fa-house" },
    { name: "Cursos", href: "/courses", icon: "fa-book-open" },
    { name: "Marketing", href: "/marketing", icon: "fa-chart-bar" },
    { name: "Ventas", href: "/sales", icon: "fa-briefcase" },
    { name: "Implementación", href: "/implementation", icon: "fa-rocket" },
    { name: "Soporte", href: "/support", icon: "fa-headset" },
];

const toggleMenu = () => {
    mobileMenuOpen.value = !mobileMenuOpen.value;
    document.body.style.overflow = mobileMenuOpen.value ? "hidden" : "";
};

onMounted(() => {
    isLoaded.value = true;
    updateCountdown();
    setInterval(updateCountdown, 1000);
});

onUnmounted(() => {
    document.body.style.overflow = "";
});

// Countdown dinámico
const countdown = ref("00D 00H 00M 00S");
const targetDate = new Date();
targetDate.setDate(targetDate.getDate() + 2); // ejemplo: 2 días desde ahora

const updateCountdown = () => {
    const now = new Date();
    const diff = targetDate - now;

    if (diff <= 0) {
        countdown.value = "00D 00H 00M 00S";
        return;
    }

    const days = String(Math.floor(diff / (1000 * 60 * 60 * 24))).padStart(
        2,
        "0",
    );
    const hours = String(Math.floor((diff / (1000 * 60 * 60)) % 24)).padStart(
        2,
        "0",
    );
    const minutes = String(Math.floor((diff / (1000 * 60)) % 60)).padStart(
        2,
        "0",
    );
    const seconds = String(Math.floor((diff / 1000) % 60)).padStart(2, "0");

    countdown.value = `${days}D ${hours}H ${minutes}M ${seconds}S`;
};
</script>

<template>
    <div class="min-h-screen flex flex-col bg-gray-50 font-sans">
        <!-- HEADER -->
        <header
            class="fixed top-0 left-0 right-0 z-50 transition-all duration-500 shadow-xl"
            :class="
                isLoaded
                    ? 'translate-y-0 opacity-100'
                    : '-translate-y-6 opacity-0'
            "
        >
            <!-- NIVEL 1: Logo y botones -->
            <div
                class="bg-[#0B1B51]/100 backdrop-blur-xl border-b border-white/10"
            >
                <div
                    class="max-w-9xl mx-auto px-4 sm:px-6 h-20 flex items-center justify-between"
                >
                    <!-- LOGO -->
                    <Link href="/" class="flex items-center gap-3 group">
                        <img
                            src="/img/LogoBlanco.png"
                            class="h-14 sm:h-16 w-auto object-contain transition-transform group-hover:scale-105"
                            style="margin-top: 2px"
                        />
                        <div
                            class="flex flex-col justify-center gap-1 leading-snug"
                        >
                            <span
                                class="text-lg sm:text-xl lg:text-2xl font-black text-white"
                            >
                                SynergyFlow Support & Learning Center
                            </span>
                            <p
                                class="text-[9px] sm:text-[10px] uppercase tracking-widest text-purple-300/80"
                            >
                                We-Collab
                            </p>
                        </div>
                    </Link>

                    <!-- BOTONES DESKTOP -->
                    <div class="hidden lg:flex items-center gap-3">
                        <Link
                            v-if="canLogin && !isLoginPage"
                            :href="route('login')"
                            class="px-6 py-3 text-[15px] font-semibold text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 transition flex items-center gap-2 shadow"
                        >
                            <i class="fas fa-right-to-bracket"></i>
                            Iniciar Sesión
                        </Link>

                        <Link
                            v-if="canRegister && !isRegisterPage"
                            :href="route('register')"
                            class="px-6 py-3 text-[15px] font-semibold text-white bg-gradient-to-r from-purple-600 to-indigo-600 rounded-xl hover:opacity-90 transition flex items-center gap-2 shadow"
                        >
                            <i class="fas fa-user-plus"></i>
                            Registrarse
                        </Link>
                    </div>

                    <!-- BOTON MOBILE -->
                    <button
                        @click="toggleMenu"
                        class="lg:hidden p-3 text-white hover:bg-white/10 rounded-xl transition"
                    >
                        <i
                            class="fas text-lg"
                            :class="mobileMenuOpen ? 'fa-xmark' : 'fa-bars'"
                        ></i>
                    </button>
                </div>
            </div>

            <!-- NIVEL 2: MENÚ DESKTOP -->
            <div
                v-if="showFullHeader"
                class="hidden lg:block bg-[#0B1B51]/90 backdrop-blur border-b border-white/10"
            >
                <div
                    class="max-w-9xl mx-auto px-6 h-14 flex items-center gap-2"
                >
                    <Link
                        v-for="item in navItems"
                        :key="item.href"
                        :href="item.href"
                        class="flex items-center gap-2 px-4 py-2.5 text-[15px] font-semibold text-gray-300 hover:text-white rounded-lg transition hover:bg-white/10"
                    >
                        <i :class="`fas ${item.icon}`"></i>
                        {{ item.name }}
                    </Link>
                </div>
            </div>

            <!-- PROMO HEADER llamativo y persuasivo -->
            <div
                v-if="showPromoHeader"
                class="hidden lg:flex bg-yellow-400 text-black font-bold border-b border-black/20 shadow-md"
            >
                <div
                    class="max-w-9xl mx-auto px-6 h-14 flex items-center justify-center gap-6 flex-wrap"
                >
                    <span class="text-lg text-center">
                        ¡Aún no eres cliente? Regístrate y obtén acceso
                        inmediato a nuestra plataforma.
                    </span>
                    <span class="font-mono text-lg">{{ countdown }}</span>
                    <Link
                        href="/register"
                        class="bg-black text-yellow-400 px-4 py-2 rounded-lg hover:bg-gray-900 transition font-semibold"
                    >
                        Registrarse
                    </Link>
                </div>
            </div>
        </header>

        <!-- OVERLAY MOBILE -->
        <Transition
            enter-active-class="transition-opacity duration-300"
            leave-active-class="transition-opacity duration-200"
        >
            <div
                v-if="mobileMenuOpen"
                class="fixed inset-0 bg-black/70 backdrop-blur-sm z-40 lg:hidden"
                @click="toggleMenu"
            />
        </Transition>

        <!-- MENU MOBILE -->
        <Transition
            enter-active-class="transition-transform duration-300"
            leave-active-class="transition-transform duration-200"
        >
            <div
                v-if="mobileMenuOpen"
                class="fixed inset-y-0 left-0 w-80 bg-slate-900 z-50 shadow-2xl lg:hidden"
            >
                <div class="flex flex-col h-full">
                    <div
                        class="flex items-center justify-between p-6 border-b border-white/10"
                    >
                        <p class="text-xl font-bold text-white">Menú</p>
                        <button
                            @click="toggleMenu"
                            class="text-gray-400 hover:text-white"
                        >
                            <i class="fas fa-xmark text-lg"></i>
                        </button>
                    </div>

                    <nav class="flex-1 overflow-y-auto p-5 space-y-2">
                        <Link
                            v-for="item in [
                                ...(showFullHeader ? navItems : []),
                            ]"
                            :key="item.href"
                            :href="item.href"
                            @click="toggleMenu"
                            class="flex items-center gap-3 px-5 py-4 rounded-xl text-[16px] text-white font-semibold hover:bg-white/10 transition"
                        >
                            <i :class="`fas ${item.icon}`"></i>
                            {{ item.name }}
                        </Link>
                    </nav>

                    <div class="p-5 border-t border-white/10 space-y-3">
                        <Link
                            v-if="canLogin && !isLoginPage"
                            :href="route('login')"
                            @click="toggleMenu"
                            class="flex justify-center w-full px-5 py-3 text-[15px] bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl"
                        >
                            Iniciar Sesión
                        </Link>

                        <Link
                            v-if="canRegister && !isRegisterPage"
                            :href="route('register')"
                            @click="toggleMenu"
                            class="flex justify-center w-full px-5 py-3 text-[15px] bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-semibold rounded-xl"
                        >
                            Registrarse
                        </Link>
                    </div>
                </div>
            </div>
        </Transition>

        <!-- CONTENIDO -->
        <main class="flex-1 transition-all duration-300" :class="mainPadding">
            <div class="max-w-9xl mx-auto px-4 sm:px-6 py-6">
                <slot />
            </div>
        </main>

        <!-- FOOTER -->
        <FooterPublico v-if="showFooter" />
    </div>
</template>

<style scoped>
html {
    scroll-behavior: smooth;
}
</style>
