<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";
import { ref, onMounted, watch, computed, nextTick } from "vue";
import AuthenticationCardLogo from "@/Components/AuthenticationCardLogo.vue";
import Checkbox from "@/Components/Checkbox.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import AppLayoutPublico from "@/Layouts/AppLayoutPublico.vue";

// ============================================
// 🔒 CONFIGURACIÓN Y FORMULARIO
// ============================================
const form = useForm({
    name: "",
    apellido: "",
    email: "",
    telefono: "",
    password: "",
    password_confirmation: "",
    terms: false,
    fotoperfil: null,
    country_code: "",
    country_name: "",
    state: "",
    city: "",
    company_id: "",
    direccion: "",
    status: "activo",
});

// ============================================
// 📍 UBICACIÓN - GEO NAMES API
// ============================================
const GEONAMES_USERNAME = "wecollab";
const countries = ref([]),
    states = ref([]),
    cities = ref([]);
const isLoading = ref({ countries: false, states: false, cities: false });

// 📱 Códigos telefónicos por país
const phoneCodes = {
    AR: "+54",
    BO: "+591",
    BR: "+55",
    CL: "+56",
    CO: "+57",
    CR: "+506",
    CU: "+53",
    EC: "+593",
    SV: "+503",
    GT: "+502",
    HN: "+504",
    MX: "+52",
    NI: "+505",
    PA: "+507",
    PY: "+595",
    PE: "+51",
    DO: "+1",
    UY: "+598",
    VE: "+58",
    ES: "+34",
    US: "+1",
    CA: "+1",
    GB: "+44",
    DE: "+49",
    FR: "+33",
    IT: "+39",
    PT: "+351",
    AU: "+61",
    JP: "+81",
    CN: "+86",
    IN: "+91",
    RU: "+7",
    ZA: "+27",
    NG: "+234",
    KE: "+254",
    EG: "+20",
    SA: "+966",
    AE: "+971",
    IL: "+972",
    TR: "+90",
    GR: "+30",
    PL: "+48",
    NL: "+31",
    BE: "+32",
    SE: "+46",
    NO: "+47",
    DK: "+45",
    FI: "+358",
    IE: "+353",
    AT: "+43",
    CH: "+41",
    CZ: "+420",
    SK: "+421",
    HU: "+36",
    RO: "+40",
    BG: "+359",
    HR: "+385",
    SI: "+386",
    RS: "+381",
    UA: "+380",
    BY: "+375",
    LT: "+370",
    LV: "+371",
    EE: "+372",
    IS: "+354",
    NZ: "+64",
    SG: "+65",
    MY: "+60",
    TH: "+66",
    PH: "+63",
    VN: "+84",
    ID: "+62",
    KR: "+82",
    TW: "+886",
    HK: "+852",
};

const currentPhoneCode = computed(() => {
    if (!form.country_code) return "";
    return phoneCodes[form.country_code] || "";
});

const fetchCountries = async () => {
    isLoading.value.countries = true;
    try {
        const response = await fetch(
            `https://secure.geonames.org/countryInfoJSON?username=${GEONAMES_USERNAME}`,
        );
        if (!response.ok) throw new Error(`Error HTTP: ${response.status}`);
        const data = await response.json();
        if (!data.geonames || !Array.isArray(data.geonames))
            throw new Error("No se encontraron países");
        countries.value = data.geonames
            .map((c) => ({
                code: c.countryCode,
                name: c.countryName,
                geonameId: c.geonameId,
            }))
            .sort((a, b) => a.name.localeCompare(b.name));
    } catch (error) {
        console.error("Error países:", error);
        countries.value = [];
    } finally {
        isLoading.value.countries = false;
    }
};

const fetchStates = async (countryCode) => {
    if (!countryCode) {
        states.value = [];
        form.state = "";
        cities.value = [];
        form.city = "";
        return;
    }
    isLoading.value.states = true;
    try {
        const selectedCountry = countries.value.find(
            (c) => c.code === countryCode,
        );
        if (!selectedCountry?.geonameId)
            throw new Error("GeonameId del país no encontrado");
        const response = await fetch(
            `https://secure.geonames.org/childrenJSON?geonameId=${selectedCountry.geonameId}&maxRows=100&username=${GEONAMES_USERNAME}`,
        );
        if (!response.ok) throw new Error(`Error HTTP: ${response.status}`);
        const data = await response.json();
        states.value = data.geonames
            ? data.geonames
                  .map((r) => ({ code: r.geonameId, name: r.name }))
                  .sort((a, b) => a.name.localeCompare(b.name))
            : [];
    } catch (error) {
        console.error("Error estados:", error);
        states.value = [];
    } finally {
        isLoading.value.states = false;
    }
};

const fetchCities = async (stateName) => {
    if (!stateName) {
        cities.value = [];
        form.city = "";
        return;
    }
    isLoading.value.cities = true;
    try {
        const selectedState = states.value.find((s) => s.name === stateName);
        if (!selectedState?.code)
            throw new Error("GeonameId del estado no encontrado");
        const response = await fetch(
            `https://secure.geonames.org/childrenJSON?geonameId=${selectedState.code}&maxRows=100&username=${GEONAMES_USERNAME}&featureClass=P`,
        );
        if (!response.ok) throw new Error(`Error HTTP: ${response.status}`);
        const data = await response.json();
        cities.value = data.geonames
            ? data.geonames
                  .map((c) => ({ code: c.geonameId, name: c.name }))
                  .sort((a, b) => a.name.localeCompare(b.name))
            : [];
    } catch (error) {
        console.error("Error ciudades:", error);
        cities.value = [];
    } finally {
        isLoading.value.cities = false;
    }
};

watch(
    () => form.country_code,
    (newCode) => {
        form.state = "";
        form.city = "";
        const selected = countries.value.find((c) => c.code === newCode);
        form.country_name = selected ? selected.name : "";
        fetchStates(newCode);
    },
);

watch(
    () => form.state,
    (newState) => {
        form.city = "";
        if (newState) fetchCities(newState);
    },
);

// ============================================
// 📍 REFERENCIAS PARA SCROLL
// ============================================
const formContainerRef = ref(null);
const headerRef = ref(null);

// ============================================
// ➕ WIZARD LOGIC - 4 PASOS
// ============================================
const currentStep = ref(1);
const totalSteps = 4;
const hasNavigatedToError = ref(false);
const stepErrors = ref({});

const stepFields = {
    1: ["name", "apellido", "email"],
    2: ["country_code", "state", "city", "telefono"],
    3: ["password", "password_confirmation"],
    4: ["terms", "direccion", "status", "company_id"],
};

const getStepForField = (fieldName) => {
    for (const [step, fields] of Object.entries(stepFields)) {
        if (fields.includes(fieldName)) return Number(step);
    }
    return 1;
};

// ============================================
// 🎯 FUNCIÓN DE SCROLL MEJORADA
// ============================================
const scrollToForm = ({ behavior = "smooth", offset = 0 } = {}) => {
    nextTick(() => {
        const target = formContainerRef.value || document.querySelector("form");
        if (target) {
            const rect = target.getBoundingClientRect();
            const scrollTop = window.pageYOffset + rect.top + offset - 100;
            window.scrollTo({ top: scrollTop, behavior: behavior });
        }
    });
};

// ============================================
// 📍 AUTO-FOCUS AL CARGAR
// ============================================
const focusFirstInput = () => {
    nextTick(() => {
        setTimeout(() => {
            const firstInput = document.querySelector(
                "input:not([disabled]):not([type='hidden']):not([type='file'])",
            );
            if (firstInput) {
                firstInput.focus();
                firstInput.scrollIntoView({
                    behavior: "smooth",
                    block: "center",
                });
            }
        }, 300);
    });
};

// ============================================
// 🔔 WATCH PARA ERRORES CON SCROLL AUTOMÁTICO
// ============================================
watch(
    () => form.errors,
    (errors) => {
        if (Object.keys(errors).length > 0 && !hasNavigatedToError.value) {
            const firstErrorField = Object.keys(errors)[0];
            const targetStep = getStepForField(firstErrorField);
            if (targetStep && targetStep !== currentStep.value) {
                hasNavigatedToError.value = true;
                currentStep.value = targetStep;
                setTimeout(() => {
                    scrollToForm({ offset: -50 });
                    setTimeout(() => {
                        const errorField = document.querySelector(
                            ".border-red-300, [aria-invalid='true'], input:invalid",
                        );
                        if (errorField) {
                            errorField.focus();
                            errorField.scrollIntoView({
                                behavior: "smooth",
                                block: "center",
                            });
                        }
                    }, 200);
                }, 150);
                showStepErrorNotification(targetStep);
            } else {
                setTimeout(() => {
                    scrollToForm({ offset: -50 });
                    const errorField = document.querySelector(
                        ".border-red-300, [aria-invalid='true']",
                    );
                    if (errorField) {
                        errorField.focus();
                        errorField.scrollIntoView({
                            behavior: "smooth",
                            block: "center",
                        });
                    }
                }, 100);
            }
        }
    },
    { deep: true },
);

watch(
    () => [currentStep.value, form.errors],
    () => {
        if (Object.keys(form.errors).length === 0) {
            hasNavigatedToError.value = false;
        }
    },
);

// ============================================
// 🔔 NOTIFICACIÓN DE ERRORES
// ============================================
const stepErrorNotification = ref({ show: false, step: null, message: "" });

const showStepErrorNotification = (step) => {
    const stepNames = {
        1: "Información Personal",
        2: "Ubicación",
        3: "Seguridad",
        4: "Finalizar",
    };
    stepErrorNotification.value = {
        show: true,
        step,
        message: `⚠️ Hay errores en: ${stepNames[step]}`,
    };
    setTimeout(() => {
        stepErrorNotification.value.show = false;
    }, 5000);
};

// ============================================
// ✅ VALIDACIÓN POR PASO
// ============================================
const validateCurrentStep = () => {
    stepErrors.value = {};
    const currentFields = stepFields[currentStep.value];
    let isValid = true;
    currentFields.forEach((field) => {
        const value = form[field];
        if (field === "email" && value && !/\S+@\S+\.\S+/.test(value)) {
            stepErrors.value[field] = "Ingresa un correo válido";
            isValid = false;
        }
        if (field === "password" && value && value.length < 8) {
            stepErrors.value[field] = "Mínimo 8 caracteres";
            isValid = false;
        }
        if (field === "password_confirmation" && value !== form.password) {
            stepErrors.value[field] = "Las contraseñas no coinciden";
            isValid = false;
        }
        if (field === "terms" && !value && currentStep.value === 4) {
            stepErrors.value[field] = "Debes aceptar los términos";
            isValid = false;
        }
        if (field === "country_code" && !value && currentStep.value === 2) {
            stepErrors.value[field] = "Selecciona un país";
            isValid = false;
        }
        if (field === "company_id" && value && !/^\d+$/.test(value)) {
            stepErrors.value[field] = "El ID de compañía debe ser numérico";
            isValid = false;
        }
    });
    return isValid;
};

// ============================================
// 📊 CONFIGURACIÓN DE PASOS
// ============================================
const steps = computed(() => [
    {
        id: 1,
        title: "Personal",
        icon: "👤",
        fields: ["name", "apellido", "email"],
        description: "Comienza con tus datos básicos",
    },
    {
        id: 2,
        title: "Ubicación",
        icon: "🌍",
        fields: ["country_code", "state", "city", "telefono"],
        description: "Selecciona tu ubicación y teléfono",
    },
    {
        id: 3,
        title: "Seguridad",
        icon: "🔐",
        fields: ["password", "password_confirmation"],
        description: "Protege tu cuenta",
    },
    {
        id: 4,
        title: "Finalizar",
        icon: "✅",
        fields: ["terms", "direccion", "status", "company_id"],
        description: "Revisa y confirma",
    },
]);

// ============================================
// 🧭 NAVEGACIÓN ENTRE PASOS
// ============================================
const nextStep = () => {
    if (validateCurrentStep() && currentStep.value < totalSteps) {
        currentStep.value++;
        stepErrors.value = {};
        setTimeout(() => {
            scrollToForm({ offset: -80 });
            focusFirstInput();
        }, 100);
    }
};

const prevStep = () => {
    if (currentStep.value > 1) {
        currentStep.value--;
        stepErrors.value = {};
        setTimeout(() => {
            scrollToForm({ offset: -80 });
            focusFirstInput();
        }, 100);
    }
};

const goToStep = (step) => {
    if (step <= currentStep.value + 1) {
        currentStep.value = step;
        stepErrors.value = {};
        setTimeout(() => {
            scrollToForm({ offset: -80 });
            focusFirstInput();
        }, 100);
    }
};

const isStepCompleted = (stepId) => stepId < currentStep.value;
const isStepActive = (stepId) => stepId === currentStep.value;
const hasStepErrors = (stepId) => {
    const fields = stepFields[stepId];
    return fields.some(
        (field) => form.errors[field] || stepErrors.value[field],
    );
};

// ============================================
// 🚀 SUBMIT CON TELÉFONO + CÓDIGO DE PAÍS
// ============================================
const submit = () => {
    if (!validateCurrentStep()) {
        scrollToForm({ offset: -50 });
        return;
    }
    if (currentPhoneCode.value && form.telefono) {
        const numeroLimpio = form.telefono.replace(/\D/g, "");
        form.telefono = currentPhoneCode.value + numeroLimpio;
    }
    if (!form.company_id || form.company_id.trim() === "") {
        form.company_id = null;
    }
    form.post(route("register"), {
        forceFormData: true,
        onSuccess: () => {
            form.reset("password", "password_confirmation");
            window.scrollTo({ top: 0, behavior: "smooth" });
        },
        onError: (errors) => console.error("Error registro:", errors),
    });
};

// ============================================
// 🔄 LIFECYCLE - AUTO-FOCUS AL CARGAR
// ============================================
onMounted(async () => {
    await fetchCountries();
    await nextTick();
    setTimeout(() => {
        scrollToForm({ behavior: "smooth", offset: -100 });
        focusFirstInput();
    }, 200);
});
</script>

<template>
    <AppLayoutPublico :canLogin="true" :canRegister="false">
        <Head title="Crear Cuenta" />
        <div
            class="min-h-screen bg-gradient-to-br from-slate-50 via-indigo-50/30 to-purple-50/30 py-8 px-4 sm:px-6 lg:px-8"
        >
            <div class="max-w-5xl mx-auto">
                <!-- HEADER -->
                <div ref="headerRef" class="text-center mb-6">
                    <div
                        class="inline-flex items-center justify-center mb-3 p-2 bg-white rounded-xl shadow"
                    >
                        <AuthenticationCardLogo />
                    </div>

                    <h1
                        class="text-2xl sm:text-3xl font-bold text-gray-900 mb-1"
                    >
                        Crear cuenta
                    </h1>

                    <p class="text-gray-600 text-sm">
                        Completa
                        <span class="font-semibold text-indigo-600">
                            {{ totalSteps }} pasos
                        </span>
                    </p>
                </div>
                <!-- NOTIFICACIÓN DE ERROR -->
                <Transition name="slide-down">
                    <div
                        v-if="stepErrorNotification.show"
                        class="mb-6 p-4 bg-gradient-to-r from-red-50 to-orange-50 border-l-4 border-red-500 rounded-r-2xl flex items-start gap-4 shadow-lg"
                        role="alert"
                        aria-live="assertive"
                    >
                        <div
                            class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center shrink-0"
                        >
                            <svg
                                class="w-5 h-5 text-red-600"
                                fill="currentColor"
                                viewBox="0 0 20 20"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-bold text-red-800">
                                {{ stepErrorNotification.message }}
                            </p>
                            <p class="text-xs text-red-600 mt-1">
                                Corrige los errores marcados para continuar
                            </p>
                        </div>
                        <button
                            @click="stepErrorNotification.show = false"
                            class="text-red-400 hover:text-red-600 transition-colors"
                            aria-label="Cerrar notificación"
                        >
                            <svg
                                class="w-5 h-5"
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
                </Transition>

                <!-- STEPPER DE PROGRESO -->
                <div
                    v-if="currentStep <= 3"
                    class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/50 p-6 mb-6"
                >
                    <div class="flex items-center justify-between relative">
                        <!-- Línea de progreso -->
                        <div
                            class="absolute left-0 right-0 top-1/2 h-2 bg-gray-200 rounded-full -translate-y-1/2 hidden md:block"
                        ></div>
                        <div
                            class="absolute left-0 top-1/2 h-2 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 rounded-full -translate-y-1/2 transition-all duration-700 hidden md:block"
                            :style="{
                                width: `${((currentStep - 1) / (totalSteps - 1)) * 100}%`,
                            }"
                        ></div>
                        <!-- Pasos -->
                        <button
                            v-for="step in steps"
                            :key="step.id"
                            @click="goToStep(step.id)"
                            :disabled="step.id > currentStep + 1"
                            class="relative z-10 flex flex-col items-center gap-2 group focus:outline-none"
                            :class="{
                                'cursor-pointer hover:scale-110':
                                    step.id <= currentStep + 1,
                                'cursor-not-allowed opacity-40':
                                    step.id > currentStep + 1,
                            }"
                        >
                            <div
                                class="w-12 h-12 rounded-2xl flex items-center justify-center text-base font-bold transition-all border-2 shadow-lg"
                                :class="{
                                    'bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 text-white border-transparent':
                                        isStepCompleted(step.id) &&
                                        !hasStepErrors(step.id),
                                    'bg-white text-indigo-600 border-indigo-500 scale-110':
                                        isStepActive(step.id),
                                    'bg-gray-100 text-gray-400 border-gray-200':
                                        step.id > currentStep,
                                    'bg-red-50 text-red-600 border-red-300 ring-4 ring-red-100':
                                        hasStepErrors(step.id) &&
                                        !isStepActive(step.id),
                                }"
                            >
                                <span
                                    v-if="
                                        isStepCompleted(step.id) &&
                                        !hasStepErrors(step.id)
                                    "
                                    >✓</span
                                >
                                <span v-else-if="hasStepErrors(step.id)"
                                    >!</span
                                >
                                <span v-else>{{ step.icon }}</span>
                            </div>
                            <span
                                class="text-xs font-semibold text-center hidden md:block"
                                :class="{
                                    'text-indigo-700': isStepActive(step.id),
                                    'text-gray-500':
                                        !isStepActive(step.id) &&
                                        step.id <= currentStep,
                                    'text-gray-400': step.id > currentStep,
                                    'text-red-600': hasStepErrors(step.id),
                                }"
                            >
                                {{ step.title }}
                            </span>
                        </button>
                    </div>
                    <!-- Mobile stepper -->
                    <div class="mt-4 md:hidden text-center">
                        <div
                            class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-50 rounded-xl"
                        >
                            <span class="text-xl">{{
                                steps.find((s) => s.id === currentStep)?.icon
                            }}</span>
                            <span class="text-sm font-semibold text-gray-800">{{
                                steps.find((s) => s.id === currentStep)?.title
                            }}</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">
                            Paso {{ currentStep }} de {{ totalSteps }}
                        </p>
                    </div>
                </div>

                <!-- FORMULARIO -->
                <div
                    ref="formContainerRef"
                    class="bg-white rounded-3xl shadow-2xl overflow-hidden border border-gray-100 scroll-mt-24"
                    id="registro-form"
                >
                    <!-- ============================================
               🗑️ HEADER ELIMINADO - Ya no aparece
               ============================================ -->

                    <form
                        @submit.prevent="submit"
                        enctype="multipart/form-data"
                        class="p-8 sm:p-10"
                        aria-label="Formulario de registro"
                    >
                        <Transition name="fade-slide" mode="out-in">
                            <!-- PASO 1: Información Personal -->
                            <div
                                v-if="currentStep === 1"
                                key="step1"
                                class="space-y-6"
                            >
                                <div
                                    class="grid grid-cols-1 md:grid-cols-2 gap-6"
                                >
                                    <div
                                        v-for="(cfg, key) in {
                                            name: {
                                                label: 'Nombre *',
                                                placeholder: 'Ej. Juan',
                                                autocomplete: 'name',
                                                autofocus: true,
                                            },
                                            apellido: {
                                                label: 'Apellido *',
                                                placeholder: 'Ej. Pérez',
                                                autocomplete: 'family-name',
                                            },
                                            email: {
                                                label: 'Correo *',
                                                placeholder: 'juan@empresa.com',
                                                autocomplete: 'username',
                                                type: 'email',
                                            },
                                        }"
                                        :key="key"
                                    >
                                        <InputLabel
                                            :for="key"
                                            :value="cfg.label"
                                            class="text-gray-700 font-semibold text-sm mb-2"
                                        />
                                        <TextInput
                                            :id="key"
                                            v-model="form[key]"
                                            :type="cfg.type || 'text'"
                                            :required="!cfg.disabled"
                                            :autocomplete="cfg.autocomplete"
                                            :autofocus="cfg.autofocus"
                                            :aria-invalid="
                                                !!(
                                                    form.errors[key] ||
                                                    stepErrors[key]
                                                )
                                            "
                                            :aria-describedby="`${key}-error`"
                                            class="mt-0 block w-full rounded-2xl border-2 border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all py-4 px-5 text-base"
                                            :class="{
                                                'border-red-300 ring-4 ring-red-50':
                                                    form.errors[key] ||
                                                    stepErrors[key],
                                            }"
                                            :placeholder="cfg.placeholder"
                                        />
                                        <p
                                            v-if="stepErrors[key]"
                                            :id="`${key}-error`"
                                            class="text-sm text-red-600 mt-2"
                                            role="alert"
                                        >
                                            {{ stepErrors[key] }}
                                        </p>
                                        <InputError
                                            v-else
                                            :id="`${key}-error`"
                                            class="text-sm mt-2"
                                            :message="form.errors[key]"
                                            role="alert"
                                        />
                                    </div>
                                </div>
                            </div>

                            <!-- PASO 2: Ubicación + Teléfono -->
                            <div
                                v-else-if="currentStep === 2"
                                key="step2"
                                class="space-y-6"
                            >
                                <!-- País -->
                                <div class="mb-6">
                                    <InputLabel
                                        value="País *"
                                        class="text-gray-700 font-semibold text-sm mb-2"
                                    />
                                    <div class="relative mt-0">
                                        <select
                                            id="country_code"
                                            v-model="form.country_code"
                                            required
                                            :disabled="
                                                isLoading.countries ||
                                                !countries.length
                                            "
                                            :aria-invalid="
                                                !!(
                                                    form.errors.country_code ||
                                                    stepErrors.country_code
                                                )
                                            "
                                            class="block w-full rounded-2xl border-2 border-gray-200 shadow-sm py-4 px-5 pr-10 bg-white focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 transition-all disabled:bg-gray-50 disabled:text-gray-400 appearance-none text-base"
                                            :class="{
                                                'border-red-300 ring-4 ring-red-50':
                                                    form.errors.country_code ||
                                                    stepErrors.country_code,
                                            }"
                                        >
                                            <option value="" disabled>
                                                Seleccione un país
                                            </option>
                                            <option
                                                v-for="c in countries"
                                                :key="c.code"
                                                :value="c.code"
                                            >
                                                {{ c.name }}
                                            </option>
                                        </select>
                                        <div
                                            class="absolute inset-y-0 right-4 flex items-center pointer-events-none"
                                        >
                                            <svg
                                                v-if="!isLoading.countries"
                                                class="w-5 h-5 text-gray-400"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M19 9l-7 7-7-7"
                                                />
                                            </svg>
                                            <svg
                                                v-else
                                                class="animate-spin w-5 h-5 text-indigo-500"
                                                viewBox="0 0 24 24"
                                            >
                                                <circle
                                                    class="opacity-25"
                                                    cx="12"
                                                    cy="12"
                                                    r="10"
                                                    stroke="currentColor"
                                                    stroke-width="4"
                                                />
                                                <path
                                                    class="opacity-75"
                                                    fill="currentColor"
                                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"
                                                />
                                            </svg>
                                        </div>
                                    </div>
                                    <p
                                        v-if="isLoading.countries"
                                        class="text-xs text-indigo-600 mt-2 flex items-center gap-1 animate-pulse"
                                    >
                                        <svg
                                            class="w-3.5 h-3.5 animate-spin"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                        >
                                            <circle
                                                class="opacity-25"
                                                cx="12"
                                                cy="12"
                                                r="10"
                                                stroke="currentColor"
                                                stroke-width="4"
                                            />
                                            <path
                                                class="opacity-75"
                                                fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"
                                            />
                                        </svg>
                                        Cargando países...
                                    </p>
                                    <p
                                        v-if="
                                            stepErrors.country_code ||
                                            form.errors.country_code
                                        "
                                        class="text-sm text-red-600 mt-2"
                                        role="alert"
                                    >
                                        {{
                                            stepErrors.country_code ||
                                            form.errors.country_code
                                        }}
                                    </p>
                                </div>

                                <!-- Teléfono con Código de País -->
                                <div class="mb-6">
                                    <InputLabel
                                        value="Teléfono"
                                        class="text-gray-700 font-semibold text-sm mb-2"
                                    />
                                    <div class="flex gap-3">
                                        <div class="w-24 shrink-0">
                                            <div
                                                class="mt-0 block w-full rounded-2xl border-2 border-gray-200 bg-gray-100 text-gray-700 font-bold text-center py-4 px-3 text-base"
                                                :class="{
                                                    'bg-indigo-100 border-indigo-300 text-indigo-700':
                                                        currentPhoneCode,
                                                }"
                                            >
                                                {{ currentPhoneCode || "—" }}
                                            </div>
                                        </div>
                                        <div class="flex-1 relative">
                                            <TextInput
                                                id="telefono"
                                                v-model="form.telefono"
                                                type="tel"
                                                autocomplete="tel"
                                                :aria-invalid="
                                                    !!(
                                                        form.errors.telefono ||
                                                        stepErrors.telefono
                                                    )
                                                "
                                                class="mt-0 block w-full rounded-2xl border-2 border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all py-4 px-5 text-base"
                                                placeholder="Ingresa tu número"
                                            />
                                        </div>
                                    </div>
                                    <p
                                        v-if="currentPhoneCode"
                                        class="text-xs text-green-600 mt-2 flex items-center gap-1"
                                    >
                                        <svg
                                            class="w-3.5 h-3.5"
                                            fill="currentColor"
                                            viewBox="0 0 20 20"
                                        >
                                            <path
                                                fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                clip-rule="evenodd"
                                            />
                                        </svg>
                                        Código de
                                        {{ form.country_name || "país" }}
                                        detectado
                                    </p>
                                </div>

                                <!-- Estado/Provincia -->
                                <div>
                                    <InputLabel
                                        value="Estado/Provincia"
                                        class="text-gray-700 font-semibold text-sm mb-2"
                                    />
                                    <div class="relative mt-0">
                                        <select
                                            id="state"
                                            v-model="form.state"
                                            :disabled="
                                                !form.country_code ||
                                                isLoading.states ||
                                                !states.length
                                            "
                                            class="block w-full rounded-2xl border-2 border-gray-200 shadow-sm py-4 px-5 pr-10 bg-white focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 transition-all disabled:bg-gray-50 disabled:text-gray-400 appearance-none text-base"
                                        >
                                            <option value="" disabled>
                                                {{
                                                    !form.country_code
                                                        ? "Primero selecciona país"
                                                        : isLoading.states
                                                          ? "Cargando..."
                                                          : !states.length
                                                            ? "Sin resultados"
                                                            : "Seleccione un estado"
                                                }}
                                            </option>
                                            <option
                                                v-for="s in states"
                                                :key="s.code"
                                                :value="s.name"
                                            >
                                                {{ s.name }}
                                            </option>
                                        </select>
                                        <div
                                            class="absolute inset-y-0 right-4 flex items-center pointer-events-none"
                                        >
                                            <svg
                                                v-if="
                                                    !isLoading.states &&
                                                    states.length
                                                "
                                                class="w-5 h-5 text-gray-400"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M19 9l-7 7-7-7"
                                                />
                                            </svg>
                                            <svg
                                                v-if="isLoading.states"
                                                class="animate-spin w-5 h-5 text-indigo-500"
                                                viewBox="0 0 24 24"
                                            >
                                                <circle
                                                    class="opacity-25"
                                                    cx="12"
                                                    cy="12"
                                                    r="10"
                                                    stroke="currentColor"
                                                    stroke-width="4"
                                                />
                                                <path
                                                    class="opacity-75"
                                                    fill="currentColor"
                                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"
                                                />
                                            </svg>
                                        </div>
                                    </div>
                                    <p
                                        v-if="isLoading.states"
                                        class="text-xs text-indigo-600 mt-2 flex items-center gap-1 animate-pulse"
                                    >
                                        <svg
                                            class="w-3.5 h-3.5 animate-spin"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                        >
                                            <circle
                                                class="opacity-25"
                                                cx="12"
                                                cy="12"
                                                r="10"
                                                stroke="currentColor"
                                                stroke-width="4"
                                            />
                                            <path
                                                class="opacity-75"
                                                fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"
                                            />
                                        </svg>
                                        Cargando estados...
                                    </p>
                                </div>

                                <!-- Ciudad -->
                                <div>
                                    <InputLabel
                                        value="Ciudad"
                                        class="text-gray-700 font-semibold text-sm mb-2"
                                    />
                                    <div class="relative mt-0">
                                        <select
                                            id="city"
                                            v-model="form.city"
                                            :disabled="
                                                !form.state ||
                                                isLoading.cities ||
                                                !cities.length
                                            "
                                            class="block w-full rounded-2xl border-2 border-gray-200 shadow-sm py-4 px-5 pr-10 bg-white focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 transition-all disabled:bg-gray-50 disabled:text-gray-400 appearance-none text-base"
                                        >
                                            <option value="" disabled>
                                                {{
                                                    !form.state
                                                        ? "Primero selecciona estado"
                                                        : isLoading.cities
                                                          ? "Cargando..."
                                                          : !cities.length
                                                            ? "Sin resultados"
                                                            : "Seleccione una ciudad"
                                                }}
                                            </option>
                                            <option
                                                v-for="city in cities"
                                                :key="city.code"
                                                :value="city.name"
                                            >
                                                {{ city.name }}
                                            </option>
                                        </select>
                                        <div
                                            class="absolute inset-y-0 right-4 flex items-center pointer-events-none"
                                        >
                                            <svg
                                                v-if="
                                                    !isLoading.cities &&
                                                    cities.length
                                                "
                                                class="w-5 h-5 text-gray-400"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M19 9l-7 7-7-7"
                                                />
                                            </svg>
                                            <svg
                                                v-if="isLoading.cities"
                                                class="animate-spin w-5 h-5 text-indigo-500"
                                                viewBox="0 0 24 24"
                                            >
                                                <circle
                                                    class="opacity-25"
                                                    cx="12"
                                                    cy="12"
                                                    r="10"
                                                    stroke="currentColor"
                                                    stroke-width="4"
                                                />
                                                <path
                                                    class="opacity-75"
                                                    fill="currentColor"
                                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"
                                                />
                                            </svg>
                                        </div>
                                    </div>
                                    <p
                                        v-if="isLoading.cities"
                                        class="text-xs text-indigo-600 mt-2 flex items-center gap-1 animate-pulse"
                                    >
                                        <svg
                                            class="w-3.5 h-3.5 animate-spin"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                        >
                                            <circle
                                                class="opacity-25"
                                                cx="12"
                                                cy="12"
                                                r="10"
                                                stroke="currentColor"
                                                stroke-width="4"
                                            />
                                            <path
                                                class="opacity-75"
                                                fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"
                                            />
                                        </svg>
                                        Cargando ciudades...
                                    </p>
                                </div>
                            </div>

                            <!-- PASO 3: Seguridad -->
                            <div
                                v-else-if="currentStep === 3"
                                key="step3"
                                class="space-y-6"
                            >
                                <div
                                    class="grid grid-cols-1 md:grid-cols-2 gap-6"
                                >
                                    <div
                                        v-for="(label, key) in {
                                            password: 'Contraseña *',
                                            password_confirmation:
                                                'Confirmar *',
                                        }"
                                        :key="key"
                                    >
                                        <InputLabel
                                            :for="key"
                                            :value="label"
                                            class="text-gray-700 font-semibold text-sm mb-2"
                                        />
                                        <TextInput
                                            :id="key"
                                            v-model="form[key]"
                                            type="password"
                                            required
                                            autocomplete="new-password"
                                            :aria-invalid="
                                                !!(
                                                    form.errors[key] ||
                                                    stepErrors[key]
                                                )
                                            "
                                            :aria-describedby="`${key}-error`"
                                            class="mt-0 block w-full rounded-2xl border-2 border-gray-200 shadow-sm focus:border-purple-500 focus:ring-4 focus:ring-purple-100 transition-all py-4 px-5 text-base"
                                            :class="{
                                                'border-red-300 ring-4 ring-red-50':
                                                    form.errors[key] ||
                                                    stepErrors[key],
                                            }"
                                            placeholder="••••••••"
                                        />
                                        <p
                                            v-if="stepErrors[key]"
                                            :id="`${key}-error`"
                                            class="text-sm text-red-600 mt-2"
                                            role="alert"
                                        >
                                            {{ stepErrors[key] }}
                                        </p>
                                        <InputError
                                            v-else
                                            :id="`${key}-error`"
                                            class="text-sm mt-2"
                                            :message="form.errors[key]"
                                            role="alert"
                                        />
                                    </div>
                                </div>
                                <!-- Requisitos de contraseña -->
                                <div
                                    class="mt-4 p-5 bg-indigo-50 rounded-2xl border border-indigo-100"
                                >
                                    <p
                                        class="text-sm font-bold text-indigo-800 mb-3"
                                    >
                                        💡 Tu contraseña debe tener:
                                    </p>
                                    <ul class="space-y-2">
                                        <li
                                            class="flex items-center gap-2 text-sm"
                                            :class="{
                                                'text-green-600':
                                                    form.password?.length >= 8,
                                                'text-indigo-600':
                                                    form.password?.length < 8,
                                            }"
                                        >
                                            <span
                                                class="w-5 h-5 rounded-full flex items-center justify-center text-xs font-bold"
                                                :class="{
                                                    'bg-green-500 text-white':
                                                        form.password?.length >=
                                                        8,
                                                    'bg-indigo-200':
                                                        form.password?.length <
                                                        8,
                                                }"
                                            >
                                                {{
                                                    form.password?.length >= 8
                                                        ? "✓"
                                                        : "0"
                                                }}
                                            </span>
                                            <span
                                                :class="{
                                                    'line-through opacity-50':
                                                        form.password?.length >=
                                                        8,
                                                }"
                                            >
                                                Al menos 8 caracteres
                                            </span>
                                        </li>
                                        <li
                                            class="flex items-center gap-2 text-sm"
                                            :class="{
                                                'text-green-600': /[A-Z]/.test(
                                                    form.password,
                                                ),
                                                'text-indigo-600':
                                                    !/[A-Z]/.test(
                                                        form.password,
                                                    ),
                                            }"
                                        >
                                            <span
                                                class="w-5 h-5 rounded-full flex items-center justify-center text-xs"
                                                :class="{
                                                    'bg-green-500 text-white':
                                                        /[A-Z]/.test(
                                                            form.password,
                                                        ),
                                                    'bg-indigo-200':
                                                        !/[A-Z]/.test(
                                                            form.password,
                                                        ),
                                                }"
                                            >
                                                {{
                                                    /[A-Z]/.test(form.password)
                                                        ? "✓"
                                                        : "A"
                                                }}
                                            </span>
                                            <span
                                                :class="{
                                                    'line-through opacity-50':
                                                        /[A-Z]/.test(
                                                            form.password,
                                                        ),
                                                }"
                                            >
                                                Una mayúscula
                                            </span>
                                        </li>
                                        <li
                                            class="flex items-center gap-2 text-sm"
                                            :class="{
                                                'text-green-600': /[0-9]/.test(
                                                    form.password,
                                                ),
                                                'text-indigo-600':
                                                    !/[0-9]/.test(
                                                        form.password,
                                                    ),
                                            }"
                                        >
                                            <span
                                                class="w-5 h-5 rounded-full flex items-center justify-center text-xs"
                                                :class="{
                                                    'bg-green-500 text-white':
                                                        /[0-9]/.test(
                                                            form.password,
                                                        ),
                                                    'bg-indigo-200':
                                                        !/[0-9]/.test(
                                                            form.password,
                                                        ),
                                                }"
                                            >
                                                {{
                                                    /[0-9]/.test(form.password)
                                                        ? "✓"
                                                        : "1"
                                                }}
                                            </span>
                                            <span
                                                :class="{
                                                    'line-through opacity-50':
                                                        /[0-9]/.test(
                                                            form.password,
                                                        ),
                                                }"
                                            >
                                                Un número
                                            </span>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <!-- PASO 4: Finalizar + Company ID -->
                            <div
                                v-else-if="currentStep === 4"
                                key="step4"
                                class="space-y-6"
                            >
                                <!-- ID de Compañía -->
                                <div
                                    class="mb-6 p-5 bg-indigo-50/50 rounded-2xl border border-indigo-100"
                                >
                                    <InputLabel
                                        value="ID de Compañía"
                                        class="text-gray-700 font-semibold text-sm mb-2"
                                    />
                                    <TextInput
                                        id="company_id"
                                        v-model="form.company_id"
                                        type="text"
                                        autocomplete="off"
                                        :aria-invalid="
                                            !!(
                                                form.errors.company_id ||
                                                stepErrors.company_id
                                            )
                                        "
                                        :aria-describedby="`company_id-error`"
                                        class="mt-0 block w-full rounded-2xl border-2 border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all py-4 px-5 text-base"
                                        :class="{
                                            'border-red-300 ring-4 ring-red-50':
                                                form.errors.company_id ||
                                                stepErrors.company_id,
                                        }"
                                        placeholder="Ej: 12345 (Opcional)"
                                    />
                                    <p
                                        v-if="stepErrors.company_id"
                                        :id="`company_id-error`"
                                        class="text-sm text-red-600 mt-2"
                                        role="alert"
                                    >
                                        {{ stepErrors.company_id }}
                                    </p>
                                    <InputError
                                        v-else
                                        :id="`company_id-error`"
                                        class="text-sm mt-2"
                                        :message="form.errors.company_id"
                                        role="alert"
                                    />
                                    <p
                                        class="text-xs text-indigo-600 mt-2 flex items-center gap-1"
                                    >
                                        <svg
                                            class="w-3.5 h-3.5"
                                            fill="currentColor"
                                            viewBox="0 0 20 20"
                                        >
                                            <path
                                                fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                                clip-rule="evenodd"
                                            />
                                        </svg>
                                        proporcionas un ID de una compañia
                                    </p>
                                </div>

                                <!-- Resumen -->
                                <div
                                    class="bg-gradient-to-br from-gray-50 via-slate-50 to-indigo-50 rounded-3xl p-6 border-2 border-gray-200"
                                >
                                    <h3 class="font-bold text-gray-800 mb-4">
                                        📋 Resumen de tu cuenta
                                    </h3>
                                    <div
                                        class="grid grid-cols-2 lg:grid-cols-3 gap-4"
                                    >
                                        <div
                                            class="bg-white rounded-2xl p-4 shadow-sm border"
                                        >
                                            <span class="text-xs text-gray-500"
                                                >Nombre</span
                                            >
                                            <p class="font-bold text-sm">
                                                {{ form.name }}
                                                {{ form.apellido }}
                                            </p>
                                        </div>
                                        <div
                                            class="bg-white rounded-2xl p-4 shadow-sm border"
                                        >
                                            <span class="text-xs text-gray-500"
                                                >Email</span
                                            >
                                            <p
                                                class="font-bold text-sm truncate"
                                            >
                                                {{ form.email }}
                                            </p>
                                        </div>
                                        <div
                                            class="bg-white rounded-2xl p-4 shadow-sm border"
                                        >
                                            <span class="text-xs text-gray-500"
                                                >Teléfono</span
                                            >
                                            <p class="font-bold text-sm">
                                                {{ currentPhoneCode }}
                                                {{
                                                    form.telefono?.replace(
                                                        currentPhoneCode,
                                                        "",
                                                    ) || "No especificado"
                                                }}
                                            </p>
                                        </div>
                                        <div
                                            class="bg-white rounded-2xl p-4 shadow-sm border"
                                        >
                                            <span class="text-xs text-gray-500"
                                                >País</span
                                            >
                                            <p class="font-bold text-sm">
                                                {{
                                                    form.country_name ||
                                                    "No especificado"
                                                }}
                                            </p>
                                        </div>
                                        <div
                                            class="bg-white rounded-2xl p-4 shadow-sm border"
                                        >
                                            <span class="text-xs text-gray-500"
                                                >Estado</span
                                            >
                                            <p class="font-bold text-sm">
                                                {{
                                                    form.state ||
                                                    "No especificado"
                                                }}
                                            </p>
                                        </div>
                                        <div
                                            class="bg-white rounded-2xl p-4 shadow-sm border"
                                        >
                                            <span class="text-xs text-gray-500"
                                                >Ciudad</span
                                            >
                                            <p class="font-bold text-sm">
                                                {{
                                                    form.city ||
                                                    "No especificado"
                                                }}
                                            </p>
                                        </div>
                                        <div
                                            class="bg-white rounded-2xl p-4 shadow-sm border"
                                            :class="{
                                                'ring-2 ring-indigo-200 bg-indigo-50':
                                                    form.company_id,
                                            }"
                                        >
                                            <span class="text-xs text-gray-500"
                                                >Compañía</span
                                            >
                                            <p
                                                class="font-bold text-sm"
                                                :class="{
                                                    'text-indigo-700':
                                                        form.company_id,
                                                }"
                                            >
                                                {{
                                                    form.company_id
                                                        ? `ID: ${form.company_id} 🏢`
                                                        : "No especificado"
                                                }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Términos -->
                                <section
                                    v-if="
                                        $page.props.jetstream
                                            ?.hasTermsAndPrivacyPolicyFeature
                                    "
                                >
                                    <div
                                        class="p-6 bg-gradient-to-r from-amber-50 via-orange-50 to-amber-50 rounded-3xl border-2 border-amber-200"
                                        :class="{
                                            'border-red-300 bg-red-50/50':
                                                form.errors.terms ||
                                                stepErrors.terms,
                                        }"
                                    >
                                        <label
                                            class="flex items-start gap-4 cursor-pointer"
                                            for="terms"
                                        >
                                            <Checkbox
                                                id="terms"
                                                v-model:checked="form.terms"
                                                name="terms"
                                                required
                                                class="mt-1 text-amber-600 focus:ring-amber-500 rounded-xl w-5 h-5"
                                                :class="{
                                                    'ring-4 ring-red-200':
                                                        form.errors.terms ||
                                                        stepErrors.terms,
                                                }"
                                                :aria-invalid="
                                                    !!(
                                                        form.errors.terms ||
                                                        stepErrors.terms
                                                    )
                                                "
                                            />
                                            <span class="text-sm text-gray-700">
                                                Acepto los
                                                <a
                                                    :href="route('terms.show')"
                                                    target="_blank"
                                                    class="font-bold text-amber-700 underline"
                                                >
                                                    Términos
                                                </a>
                                                y la
                                                <a
                                                    :href="route('policy.show')"
                                                    target="_blank"
                                                    class="font-bold text-amber-700 underline"
                                                >
                                                    Política de Privacidad
                                                </a>
                                            </span>
                                        </label>
                                        <p
                                            v-if="stepErrors.terms"
                                            class="text-sm text-red-600 mt-3"
                                            role="alert"
                                        >
                                            {{ stepErrors.terms }}
                                        </p>
                                        <InputError
                                            v-else
                                            class="mt-3"
                                            :message="form.errors.terms"
                                            role="alert"
                                        />
                                    </div>
                                </section>

                                <!-- Dirección (Disabled) -->
                                <section class="space-y-4 opacity-60">
                                    <h3 class="text-sm font-bold text-gray-500">
                                        Dirección
                                        <span
                                            class="text-xs bg-gray-200 px-2 py-1 rounded-full"
                                        >
                                            Próximamente
                                        </span>
                                    </h3>
                                    <div
                                        class="grid grid-cols-1 md:grid-cols-2 gap-4"
                                    >
                                        <TextInput
                                            disabled
                                            placeholder="Calle, número..."
                                            class="bg-gray-50 text-gray-400 rounded-2xl"
                                        />
                                        <select
                                            disabled
                                            class="bg-gray-50 text-gray-400 rounded-2xl py-3 px-4"
                                        >
                                            <option>Activo</option>
                                        </select>
                                    </div>
                                </section>
                            </div>
                        </Transition>

                        <!-- Botones de Navegación -->
                        <div
                            class="flex flex-col sm:flex-row items-center justify-between gap-6 pt-8 border-t-2 border-gray-100 mt-8"
                        >
                            <Link
                                v-if="currentStep === 1"
                                :href="route('login')"
                                class="text-sm text-gray-600 hover:text-indigo-600 font-semibold flex items-center gap-2"
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
                                        d="M10 19l-7-7m0 0l7-7m-7 7h18"
                                    />
                                </svg>
                                ¿Ya tienes cuenta?
                                <span class="text-indigo-600 underline"
                                    >Inicia sesión</span
                                >
                            </Link>
                            <div v-else></div>
                            <div class="flex items-center gap-4">
                                <button
                                    v-if="currentStep > 1"
                                    @click="prevStep"
                                    type="button"
                                    class="px-6 py-3.5 text-gray-700 font-bold rounded-2xl border-2 border-gray-300 bg-white hover:bg-gray-50 transition-all flex items-center gap-2"
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
                                            d="M15 19l-7-7 7-7"
                                        />
                                    </svg>
                                    Anterior
                                </button>
                                <button
                                    v-if="currentStep < totalSteps"
                                    @click="nextStep"
                                    type="button"
                                    class="px-8 py-3.5 bg-gradient-to-r from-indigo-600 via-purple-600 to-indigo-700 text-white font-bold rounded-2xl shadow-xl hover:shadow-2xl transition-all flex items-center gap-2"
                                >
                                    Siguiente
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
                                            d="M9 5l7 7-7 7"
                                        />
                                    </svg>
                                </button>
                                <PrimaryButton
                                    v-if="currentStep === totalSteps"
                                    :disabled="form.processing"
                                    type="submit"
                                    class="px-10 py-3.5 bg-gradient-to-r from-emerald-600 via-teal-600 to-emerald-700 text-white font-bold rounded-2xl shadow-xl transition-all disabled:opacity-50 flex items-center gap-2"
                                >
                                    <span v-if="form.processing"
                                        >⏳ Procesando...</span
                                    >
                                    <span v-else>✓ ¡Crear Cuenta!</span>
                                </PrimaryButton>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="text-center mt-8 text-sm text-gray-400">
                    &copy; {{ new Date().getFullYear() }} Tu Plataforma. Todos
                    los derechos reservados.
                </div>
            </div>
        </div>
    </AppLayoutPublico>
</template>

<style scoped>
/* TRANSICIONES */
.fade-slide-enter-active,
.fade-slide-leave-active {
    transition:
        opacity 0.35s ease,
        transform 0.35s ease;
}
.fade-slide-enter-from {
    opacity: 0;
    transform: translateX(30px);
}
.fade-slide-leave-to {
    opacity: 0;
    transform: translateX(-30px);
}
.slide-down-enter-active,
.slide-down-leave-active {
    transition: all 0.4s ease;
}
.slide-down-enter-from {
    opacity: 0;
    transform: translateY(-30px);
}
.slide-down-leave-to {
    opacity: 0;
    transform: translateY(-30px);
}
/* ACCESIBILIDAD */
:focus-visible {
    outline: 3px solid #6366f1;
    outline-offset: 3px;
}
.scroll-mt-24 {
    scroll-margin-top: 6rem;
}
/* RESPONSIVE */
select {
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
}
input:focus,
select:focus {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(99, 102, 241, 0.2);
}
/* MOBILE - EVITAR TECLADO VIRTUAL */
@media (max-width: 768px) {
    input:focus {
        scroll-margin-top: 200px;
    }
}
</style>
