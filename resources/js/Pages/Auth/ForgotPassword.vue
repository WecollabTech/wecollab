<script setup>
import { Head, useForm } from "@inertiajs/vue3";
import AuthenticationCard from "@/Components/AuthenticationCard.vue";
import AuthenticationCardLogo from "@/Components/AuthenticationCardLogo.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import Header from "@/Vistas/Header.vue";
import Footer from "@/Vistas/Footer.vue";

defineProps({
    status: String,
});

const form = useForm({
    email: "",
});

const submit = () => {
    form.post(route("password.email"));
};
</script>

<template>
    <Header :isLoginPage="true" :canLogin="true" :canRegister="true" />
    <Head title="Forgot Password" />

    <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo />
        </template>

        <div class="mb-6 text-sm text-gray-600 mx-auto max-w-3xl px-4">
            <p class="leading-relaxed">
                ¿Olvidaste tu contraseña? No hay problema. Solo indícanos tu
                dirección de correo electrónico y te enviaremos un enlace para
                restablecer tu contraseña que te permitirá elegir una nueva.
            </p>
        </div>

        <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
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

            <div class="flex items-center justify-center mt-6">
                <PrimaryButton
                    :class="{
                        'bg-green-500 hover:bg-green-600 active:bg-green-700 text-white':
                            !form.processing,
                        'opacity-25': form.processing,
                    }"
                    :disabled="form.processing"
                    class="px-6 py-3 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                >
                    Restablecer Contraseña
                </PrimaryButton>
            </div>
        </form>
    </AuthenticationCard>
    <Footer style="margin-top: 20px" />
</template>
