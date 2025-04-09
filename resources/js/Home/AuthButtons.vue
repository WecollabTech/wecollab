<script setup>
import { Link } from "@inertiajs/vue3";
import { ref, onMounted } from "vue";

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
});

const isLoginPage = ref(false);
const isRegisterPage = ref(false);

onMounted(() => {
    // Verificar la ruta actual y establecer el estado
    const currentPath = window.location.pathname;

    isLoginPage.value = currentPath === "/login"; // Si estamos en la página de login
    isRegisterPage.value = currentPath === "/register"; // Si estamos en la página de registro
});
</script>

<template>
    <div class="flex justify-between items-center">
        <!-- Autenticación -->
        <nav
            v-if="canLogin"
            class="flex flex-col sm:flex-row space-y-3 sm:space-x-3 sm:space-y-0"
        >
            <Link
                v-if="$page.props.auth.user"
                :href="route('dashboard')"
                class="text-white hover:text-gray-200 transition duration-300 ease-in-out text-sm sm:text-base"
            >
                Dashboard
            </Link>

            <template v-else>
                <div
                    class="flex flex-col sm:flex-row items-center gap-4 ml-[0px]"
                >
                    <!-- Mostrar solo el botón de 'Iniciar sesión' si estamos en la página de inicio o de registro -->
                    <Link
                        v-if="!isLoginPage"
                        :href="route('login')"
                        class="flex items-center space-x-4 text-white sm:border sm:border-white px-4 py-2 rounded-full transition duration-300 ease-in-out hover:bg-green-500 hover:text-white"
                    >
                        <i class="fas fa-sign-in-alt text-white text-xl"></i>
                        <span class="text-lg">Iniciar Sesión</span>
                    </Link>

                    <!-- Mostrar solo el botón de 'Registrarse' si estamos en la página de inicio o de login -->
                    <Link
                        v-if="!isRegisterPage && canRegister"
                        :href="route('register')"
                        class="flex items-center space-x-4 text-white sm:border sm:border-white px-4 py-2 rounded-full transition duration-300 ease-in-out hover:bg-green-500 hover:text-white"
                    >
                        <i class="fas fa-user-plus text-white text-xl"></i>
                        <span class="text-lg">Registrarse</span>
                    </Link>
                </div>
            </template>
        </nav>
    </div>
</template>
