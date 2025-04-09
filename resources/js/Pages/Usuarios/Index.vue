<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";
import { ref, onMounted, watch, onBeforeUnmount } from "vue";
import AuthenticationCard from "@/Components/AuthenticationCard.vue";
import AuthenticationCardLogo from "@/Components/AuthenticationCardLogo.vue";
import Checkbox from "@/Components/Checkbox.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import Header from "@/Vistas/Header.vue";
import Footer from "@/Vistas/Footer.vue";

// Configuración
const GEONAMES_USERNAME = "wecollab";
const MAX_FILE_SIZE = 2 * 1024 * 1024; // 2MB
const ALLOWED_FILE_TYPES = ["image/jpeg", "image/png", "image/gif"];

// Formulario
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
    direccion: "",
    status: "activo",
});

// Estados y datos
const countries = ref([]);
const states = ref([]);
const cities = ref([]);
const isLoading = ref({
    countries: false,
    states: false,
    cities: false,
});
const fileError = ref(null);
const filePreviewUrl = ref(null);

// Función para crear URL de vista previa segura
const createPreviewUrl = (file) => {
    // Limpiar URL previa si existe
    if (filePreviewUrl.value) {
        URL.revokeObjectURL(filePreviewUrl.value);
        filePreviewUrl.value = null;
    }

    // Verificar si el navegador soporta createObjectURL
    if (file && window.URL && window.URL.createObjectURL) {
        filePreviewUrl.value = URL.createObjectURL(file);
    }
};

// Obtener lista de países
const fetchCountries = async () => {
    isLoading.value.countries = true;
    try {
        const response = await fetch(
            `https://secure.geonames.org/countryInfoJSON?username=${GEONAMES_USERNAME}`
        );

        if (!response.ok) {
            throw new Error(`Error HTTP: ${response.status}`);
        }

        const data = await response.json();

        if (!data.geonames || !Array.isArray(data.geonames)) {
            throw new Error("No se encontraron países en la respuesta");
        }

        countries.value = data.geonames
            .map((country) => ({
                code: country.countryCode,
                name: country.countryName,
                geonameId: country.geonameId,
            }))
            .sort((a, b) => a.name.localeCompare(b.name));
    } catch (error) {
        console.error("Error al obtener países:", error);
        countries.value = [];
    } finally {
        isLoading.value.countries = false;
    }
};

// Obtener estados/provincias
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
            (c) => c.code === countryCode
        );
        if (!selectedCountry?.geonameId) {
            throw new Error("No se encontró el geonameId del país");
        }

        const response = await fetch(
            `https://secure.geonames.org/childrenJSON?geonameId=${selectedCountry.geonameId}&maxRows=100&username=${GEONAMES_USERNAME}`
        );

        if (!response.ok) {
            throw new Error(`Error HTTP: ${response.status}`);
        }

        const data = await response.json();
        states.value = data.geonames
            ? data.geonames
                  .map((region) => ({
                      code: region.geonameId,
                      name: region.name,
                  }))
                  .sort((a, b) => a.name.localeCompare(b.name))
            : [];
    } catch (error) {
        console.error("Error al obtener estados:", error);
        states.value = [];
    } finally {
        isLoading.value.states = false;
    }
};

// Obtener ciudades
const fetchCities = async (stateName) => {
    if (!stateName) {
        cities.value = [];
        form.city = "";
        return;
    }

    isLoading.value.cities = true;
    try {
        const selectedState = states.value.find((s) => s.name === stateName);
        if (!selectedState?.code) {
            throw new Error("No se encontró el geonameId del estado");
        }

        const response = await fetch(
            `https://secure.geonames.org/childrenJSON?geonameId=${selectedState.code}&maxRows=100&username=${GEONAMES_USERNAME}&featureClass=P`
        );

        if (!response.ok) {
            throw new Error(`Error HTTP: ${response.status}`);
        }

        const data = await response.json();
        cities.value = data.geonames
            ? data.geonames
                  .map((city) => ({
                      code: city.geonameId,
                      name: city.name,
                  }))
                  .sort((a, b) => a.name.localeCompare(b.name))
            : [];
    } catch (error) {
        console.error("Error al obtener ciudades:", error);
        cities.value = [];
    } finally {
        isLoading.value.cities = false;
    }
};

// Observadores
watch(
    () => form.country_code,
    (newCountryCode) => {
        form.state = "";
        form.city = "";
        const selectedCountry = countries.value.find(
            (c) => c.code === newCountryCode
        );
        form.country_name = selectedCountry ? selectedCountry.name : "";
        fetchStates(newCountryCode);
    }
);

watch(
    () => form.state,
    (newState) => {
        form.city = "";
        if (newState) fetchCities(newState);
    }
);

// Cargar países al iniciar
onMounted(fetchCountries);

// Manejo de archivos
const handleFileUpload = (event) => {
    fileError.value = null;
    const file = event.target.files[0];

    if (!file) {
        form.fotoperfil = null;
        createPreviewUrl(null);
        return;
    }

    // Validaciones
    if (!file.type.startsWith("image/")) {
        fileError.value = "El archivo debe ser una imagen (JPEG, PNG, GIF)";
        form.fotoperfil = null;
        createPreviewUrl(null);
        return;
    }

    if (!ALLOWED_FILE_TYPES.includes(file.type)) {
        fileError.value = "Formato de imagen no soportado. Use JPEG, PNG o GIF";
        form.fotoperfil = null;
        createPreviewUrl(null);
        return;
    }

    if (file.size > MAX_FILE_SIZE) {
        fileError.value = "La imagen es demasiado grande (máximo 2MB)";
        form.fotoperfil = null;
        createPreviewUrl(null);
        return;
    }

    form.fotoperfil = file;
    createPreviewUrl(file);
};

// Limpiar recursos al desmontar
onBeforeUnmount(() => {
    if (filePreviewUrl.value) {
        URL.revokeObjectURL(filePreviewUrl.value);
    }
});

// Envío del formulario
const submit = () => {
    form.post(route("register"), {
        forceFormData: true,
        onSuccess: () => {
            form.reset("password", "password_confirmation");
        },
        onError: (errors) => {
            console.error("Error en el registro:", errors);
        },
        onFinish: () => {
            if (filePreviewUrl.value) {
                URL.revokeObjectURL(filePreviewUrl.value);
                filePreviewUrl.value = null;
            }
        },
    });
};
</script>

<template>
    <Header :isLoginPage="true" :canLogin="true" :canRegister="true" />
    <Head title="Register" />

    <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo />
        </template>

        <form @submit.prevent="submit" enctype="multipart/form-data">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Nombre -->
                <div>
                    <InputLabel for="name" value="Nombre" />
                    <TextInput
                        id="name"
                        v-model="form.name"
                        type="text"
                        class="mt-1 block w-full"
                        required
                        autofocus
                        autocomplete="name"
                    />
                    <InputError class="mt-2" :message="form.errors.name" />
                </div>

                <!-- Apellido -->
                <div>
                    <InputLabel for="apellido" value="Apellido" />
                    <TextInput
                        id="apellido"
                        v-model="form.apellido"
                        type="text"
                        class="mt-1 block w-full"
                        required
                    />
                    <InputError class="mt-2" :message="form.errors.apellido" />
                </div>

                <!-- Correo Electrónico -->
                <div>
                    <InputLabel for="email" value="Correo Electrónico" />
                    <TextInput
                        id="email"
                        v-model="form.email"
                        type="email"
                        class="mt-1 block w-full"
                        required
                        autocomplete="username"
                    />
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <!-- Teléfono -->
                <!-- <div>
                    <InputLabel for="telefono" value="Teléfono" />
                    <TextInput
                        id="telefono"
                        v-model="form.telefono"
                        type="tel"
                        class="mt-1 block w-full"
                        required
                        autocomplete="tel"
                    />
                    <InputError class="mt-2" :message="form.errors.telefono" />
                </div> -->

                <!-- Contraseña -->
                <div>
                    <InputLabel for="password" value="Contraseña" />
                    <TextInput
                        id="password"
                        v-model="form.password"
                        type="password"
                        class="mt-1 block w-full"
                        required
                        autocomplete="new-password"
                    />
                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <!-- Confirmar Contraseña -->
                <div>
                    <InputLabel
                        for="password_confirmation"
                        value="Confirmar Contraseña"
                    />
                    <TextInput
                        id="password_confirmation"
                        v-model="form.password_confirmation"
                        type="password"
                        class="mt-1 block w-full"
                        required
                        autocomplete="new-password"
                    />
                    <InputError
                        class="mt-2"
                        :message="form.errors.password_confirmation"
                    />
                </div>

                <!-- Dirección -->
                <!-- <div>
                    <InputLabel for="direccion" value="Dirección" />
                    <TextInput
                        id="direccion"
                        v-model="form.direccion"
                        type="text"
                        class="mt-1 block w-full"
                        required
                        placeholder="Calle, número, departamento"
                    />
                    <InputError class="mt-2" :message="form.errors.direccion" />
                </div> -->

                <!-- Estado (Status) -->
                <!-- <div>
                    <InputLabel for="status" value="Estado" />
                    <select
                        id="status"
                        v-model="form.status"
                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
                        required
                    >
                        <option value="activo">Activo</option>
                        <option value="inactivo">Inactivo</option>
                    </select>
                    <InputError class="mt-2" :message="form.errors.status" />
                </div> -->

                <!-- País -->
                <div>
                    <InputLabel for="country" value="País" />
                    <select
                        id="country"
                        v-model="form.country_code"
                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
                        required
                        :disabled="isLoading.countries || !countries.length"
                    >
                        <option value="">Seleccione un país</option>
                        <option
                            v-for="country in countries"
                            :key="country.code"
                            :value="country.code"
                        >
                            {{ country.name }}
                        </option>
                    </select>
                    <InputError
                        class="mt-2"
                        :message="form.errors.country_code"
                    />
                    <div
                        v-if="isLoading.countries"
                        class="text-sm text-gray-500 mt-1"
                    >
                        Cargando países...
                    </div>
                </div>

                <!-- Estado/Provincia -->
                <div>
                    <InputLabel for="state" value="Estado/Provincia" />
                    <select
                        id="state"
                        v-model="form.state"
                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
                        required
                        :disabled="
                            !form.country_code ||
                            isLoading.states ||
                            !states.length
                        "
                    >
                        <option value="">Seleccione un estado</option>
                        <option
                            v-for="state in states"
                            :key="state.code"
                            :value="state.name"
                        >
                            {{ state.name }}
                        </option>
                    </select>
                    <InputError class="mt-2" :message="form.errors.state" />
                    <div
                        v-if="isLoading.states"
                        class="text-sm text-gray-500 mt-1"
                    >
                        Cargando estados...
                    </div>
                    <div
                        v-if="
                            !isLoading.states &&
                            form.country_code &&
                            !states.length
                        "
                        class="text-sm text-gray-500 mt-1"
                    >
                        No hay estados disponibles
                    </div>
                </div>

                <!-- Ciudad -->
                <div>
                    <InputLabel for="city" value="Ciudad" />
                    <select
                        id="city"
                        v-model="form.city"
                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
                        required
                        :disabled="
                            !form.state || isLoading.cities || !cities.length
                        "
                    >
                        <option value="">Seleccione una ciudad</option>
                        <option
                            v-for="city in cities"
                            :key="city.code"
                            :value="city.name"
                        >
                            {{ city.name }}
                        </option>
                    </select>
                    <InputError class="mt-2" :message="form.errors.city" />
                    <div
                        v-if="isLoading.cities"
                        class="text-sm text-gray-500 mt-1"
                    >
                        Cargando ciudades...
                    </div>
                    <div
                        v-if="!isLoading.cities && form.state && !cities.length"
                        class="text-sm text-gray-500 mt-1"
                    >
                        No hay ciudades disponibles
                    </div>
                </div>

                <!-- Foto de perfil -->
                <div class="col-span-2 sm:col-span-1">
                    <InputLabel for="fotoperfil" value="Foto de Perfil" />
                    <input
                        id="fotoperfil"
                        type="file"
                        class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                        @change="handleFileUpload"
                        accept="image/*"
                    />

                    <div v-if="fileError" class="mt-2 text-sm text-red-600">
                        {{ fileError }}
                    </div>

                    <div v-if="form.fotoperfil" class="mt-2">
                        <div class="flex items-center gap-3">
                            <img
                                v-if="filePreviewUrl"
                                :src="filePreviewUrl"
                                alt="Vista previa"
                                class="h-20 w-20 rounded-full object-cover border border-gray-200"
                            />
                            <span class="text-sm text-gray-600">
                                {{ form.fotoperfil.name }}
                                ({{ (form.fotoperfil.size / 1024).toFixed(1) }}
                                KB)
                            </span>
                        </div>
                    </div>

                    <InputError
                        class="mt-2"
                        :message="form.errors.fotoperfil"
                    />
                </div>

                <!-- Términos y Política de Privacidad -->
                <div
                    v-if="$page.props.jetstream.hasTermsAndPrivacyPolicyFeature"
                    class="col-span-2"
                >
                    <InputLabel for="terms">
                        <div class="flex items-center">
                            <Checkbox
                                id="terms"
                                v-model:checked="form.terms"
                                name="terms"
                                required
                            />
                            <div class="ms-2">
                                Acepto los
                                <a
                                    target="_blank"
                                    :href="route('terms.show')"
                                    class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                >
                                    Términos de Servicio
                                </a>
                                y la
                                <a
                                    target="_blank"
                                    :href="route('policy.show')"
                                    class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                >
                                    Política de Privacidad
                                </a>
                            </div>
                        </div>
                        <InputError class="mt-2" :message="form.errors.terms" />
                    </InputLabel>
                </div>
            </div>

            <!-- Enlace a login y botón de registro -->
            <div class="flex items-center justify-end mt-4">
                <Link
                    :href="route('login')"
                    class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                    ¿Ya estás registrado?
                </Link>

                <PrimaryButton
                    class="ms-4"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Registrar
                </PrimaryButton>
            </div>
        </form>
    </AuthenticationCard>
    <Footer style="margin-top: 20px" />
</template>
