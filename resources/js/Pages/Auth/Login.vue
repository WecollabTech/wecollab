<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";
// Se elimina AuthenticationCard y AuthenticationCardLogo
import AppLayoutPublico from "@/Layouts/AppLayoutPublico.vue"; // Asegúrate de que la ruta sea correcta
import Checkbox from "@/Components/Checkbox.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
// Se eliminan Header y Footer manuales, ya que el Layout los incluye

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    email: "",
    password: "",
    remember: false,
});

const submit = () => {
    form.transform((data) => ({
        ...data,
        remember: form.remember ? "on" : "",
    })).post(route("login"), {
        onFinish: () => form.reset("password"),
    });
};
</script>

<template>
    <!-- El Layout envuelve toda la página -->
    <AppLayoutPublico>
        <Head title="Iniciar Sesion" />

        <!-- Contenedor principal para centrar el formulario (estilo tarjeta) -->
        <div
            class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100"
        >
            <!-- Opcional: Si necesitas mostrar el logo aquí específicamente -->
            <!-- <div class="mb-4"><AuthenticationCardLogo /></div> -->

            <div
                class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg"
            >
                <div
                    v-if="status"
                    class="mb-4 font-medium text-sm text-green-600"
                >
                    {{ status }}
                </div>

                <form @submit.prevent="submit">
                    <div>
                        <InputLabel for="email" value="Correo Electronico" />
                        <TextInput
                            id="email"
                            v-model="form.email"
                            type="email"
                            class="mt-1 block w-full"
                            required
                            autofocus
                            autocomplete="username"
                        />
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <div class="mt-4">
                        <InputLabel for="password" value="Contraseña" />
                        <TextInput
                            id="password"
                            v-model="form.password"
                            type="password"
                            class="mt-1 block w-full"
                            required
                            autocomplete="current-password"
                        />
                        <InputError
                            class="mt-2"
                            :message="form.errors.password"
                        />
                    </div>

                    <div class="block mt-4">
                        <label class="flex items-center">
                            <Checkbox
                                v-model:checked="form.remember"
                                name="remember"
                            />
                            <span class="ms-2 text-sm text-gray-600"
                                >Acuérdate de mí</span
                            >
                        </label>
                    </div>

                    <div class="flex items-center justify-center mt-4">
                        <PrimaryButton
                            class="ms-4 bg-green-500 hover:bg-green-600 active:bg-green-700 text-white"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                        >
                            Iniciar Sesión
                        </PrimaryButton>
                    </div>

                    <div v-if="canResetPassword" class="mt-6 text-left">
                        <Link
                            :href="route('password.request')"
                            class="text-sm text-red-500 hover:text-green-600 active:text-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                        >
                            ¿Olvidaste tu contraseña?
                        </Link>
                    </div>
                </form>
            </div>
        </div>
    </AppLayoutPublico>
</template>
