<script setup>
import { Link } from "@inertiajs/vue3";
import { ref } from "vue";

const props = defineProps({
    canRegister: { type: Boolean, default: true },
});

const emit = defineEmits(["close"]);
const popupClosed = ref(false);

const close = () => {
    emit("close");
    popupClosed.value = true;
    localStorage.setItem("slc_popup_closed", "true");
};

const shouldShow = () => !localStorage.getItem("slc_popup_closed");
</script>

<template>
    <Transition
        enter-active-class="transition-all duration-500 ease-out"
        enter-from-class="opacity-0 scale-90"
        enter-to-class="opacity-100 scale-100"
        leave-active-class="transition-all duration-300 ease-in"
        leave-from-class="opacity-100 scale-100"
        leave-to-class="opacity-0 scale-90"
    >
        <div
            v-if="shouldShow() && !popupClosed"
            class="fixed inset-0 z-[100] flex items-start justify-center pt-14 pb-12 px-4"
            @click.self="close"
        >
            <div
                class="absolute inset-0 bg-slate-950/90 backdrop-blur-xl"
            ></div>
            <div
                class="absolute top-1/4 left-1/4 w-96 h-96 bg-purple-500/30 rounded-full blur-3xl animate-pulse"
            ></div>
            <div
                class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-indigo-500/30 rounded-full blur-3xl animate-pulse delay-1000"
            ></div>

            <div
                class="relative bg-white rounded-3xl shadow-2xl max-w-xl w-full overflow-hidden transform transition-all hover:scale-[1.01]"
            >
                <button
                    @click="close"
                    class="absolute top-4 right-4 w-9 h-9 bg-white/90 hover:bg-white shadow-lg rounded-full flex items-center justify-center transition-all duration-300 hover:scale-110 hover:rotate-90 z-10"
                    aria-label="Cerrar popup"
                >
                    <i class="fas fa-xmark w-4 h-4 text-gray-700"></i>
                </button>

                <div
                    class="bg-gradient-to-r from-indigo-600 via-purple-600 to-fuchsia-600 p-8 text-center relative overflow-hidden"
                >
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
                    <span
                        class="inline-block text-xs font-bold bg-white/20 text-white px-3 py-1 rounded-full mb-4 tracking-wider uppercase backdrop-blur-sm border border-white/30"
                        >🎉 Nueva Experiencia</span
                    >
                    <div class="text-5xl mb-4 animate-bounce">🚀</div>
                    <h3
                        class="text-2xl font-black text-white mb-2 drop-shadow-lg"
                    >
                        Bienvenido a SLC
                    </h3>
                    <p class="text-white/90 text-sm font-medium">
                        Impulsa tu crecimiento profesional con aprendizaje
                        inteligente
                    </p>
                </div>

                <div class="p-6">
                    <div class="grid gap-3 mb-6">
                        <div
                            class="flex gap-3 p-4 bg-slate-50 rounded-2xl border border-slate-100"
                        >
                            <i
                                class="fas fa-book-open text-2xl text-indigo-600"
                            ></i>
                            <div>
                                <p class="font-bold text-gray-900">
                                    +500 Cursos Actualizados
                                </p>
                                <p class="text-xs text-gray-500">
                                    Contenido en constante evolución
                                </p>
                            </div>
                        </div>
                        <div
                            class="flex gap-3 p-4 bg-slate-50 rounded-2xl border border-slate-100"
                        >
                            <i
                                class="fas fa-certificate text-2xl text-indigo-600"
                            ></i>
                            <div>
                                <p class="font-bold text-gray-900">
                                    Capacitaciones Oficiales
                                </p>
                                <p class="text-xs text-gray-500">
                                    Reconocimiento profesional internacional
                                </p>
                            </div>
                        </div>
                        <div
                            class="flex gap-3 p-4 bg-slate-50 rounded-2xl border border-slate-100"
                        >
                            <i
                                class="fas fa-chalkboard-user text-2xl text-indigo-600"
                            ></i>
                            <div>
                                <p class="font-bold text-gray-900">
                                    Mentoría Especializada
                                </p>
                                <p class="text-xs text-gray-500">
                                    Acompañamiento personalizado 24/7
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                            @click="close"
                            class="w-full flex items-center justify-center px-6 py-3 bg-gradient-to-r from-indigo-600 via-purple-600 to-fuchsia-600 text-white font-bold rounded-2xl transition-all duration-300 shadow-lg hover:shadow-xl hover:-translate-y-1"
                        >
                            Comenzar Gratis
                        </Link>
                        <Link
                            :href="route('login')"
                            @click="close"
                            class="w-full flex items-center justify-center px-6 py-3 bg-slate-100 hover:bg-slate-200 text-gray-900 font-bold rounded-2xl transition-all duration-300"
                        >
                            Ya tengo cuenta
                        </Link>
                    </div>

                    <label class="flex items-center gap-3 mt-6 cursor-pointer">
                        <input
                            type="checkbox"
                            @change="
                                (e) => {
                                    if (e.target.checked)
                                        localStorage.setItem(
                                            'slc_popup_closed',
                                            'true',
                                        );
                                }
                            "
                            class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                        />
                        <span class="text-xs text-gray-500"
                            >No mostrar este mensaje de nuevo</span
                        >
                    </label>
                </div>
            </div>
        </div>
    </Transition>
</template>
