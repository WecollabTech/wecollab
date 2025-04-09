<script setup>
import { ref, onMounted, onBeforeUnmount } from "vue";
import { Link } from "@inertiajs/vue3";
import AuthButtons from "@/Home/AuthButtons.vue";

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    isLoginPage: Boolean, // Añadir esta prop para controlar la página de login
    showHeaderBottom: Boolean, // Propiedad para controlar si mostrar la parte inferior del header
});

const isMobile = ref(false);
const isMenuOpen = ref(false);

const checkScreenSize = () => {
    isMobile.value = window.innerWidth <= 768;
    if (!isMobile.value && isMenuOpen.value) {
        isMenuOpen.value = false;
    }
};

onMounted(() => {
    checkScreenSize();
    window.addEventListener("resize", checkScreenSize);
});

onBeforeUnmount(() => {
    window.removeEventListener("resize", checkScreenSize);
});

const toggleMenu = () => {
    isMenuOpen.value = !isMenuOpen.value;
};
</script>

<template>
    <header class="header">
        <div class="header-top">
            <a href="/">
                <div class="logo-container">
                    <img src="/img/logo.png" alt="Atrápalo" class="logo" />
                    <div class="logo-text">SISTEMAGT</div>
                </div>
            </a>

            <!-- Mostrar solo si estamos en la página de login -->
            <nav v-if="isLoginPage" class="top-nav">
                <a
                    href="#"
                    class="flex items-center border text-white border-white px-4 py-2 rounded-full transition duration-300 ease-in-out hover:bg-blue-800 hover:text-white hover:shadow-lg transform hover:scale-105 text-sm sm:text-base"
                >
                    <i
                        class="fas fa-question-circle transform transition duration-300 ease-in-out hover:rotate-12"
                    ></i>
                    <span class="ml-3 sm:inline-block">AYUDA</span>
                </a>

                <AuthButtons :canLogin="canLogin" :canRegister="canRegister" />
            </nav>

            <div class="hamburger-menu" @click="toggleMenu">
                <i class="fas fa-bars"></i>
            </div>
        </div>

        <div
            v-if="showHeaderBottom"
            class="header-bottom"
            v-show="!isMobile || isMenuOpen"
        >
            <!-- Menú en pantallas grandes -->
            <nav
                :class="{ 'main-nav': true, active: isMenuOpen }"
                class="flex space-x-1"
            >
                <a
                    href="#"
                    v-show="!isMobile"
                    class="flex items-center text-white px-4 py-2 rounded-full transition duration-300 ease-in-out hover:bg-blue-800 hover:text-white hover:shadow-lg transform hover:scale-105 text-sm sm:text-base"
                >
                    <i
                        class="fas fa-chalkboard-teacher text-white text-lg sm:text-base"
                    ></i>
                    <span class="ml-2 text-base sm:text-sm text-white"
                        >Curso de CRM</span
                    >
                </a>
                <a
                    href="#"
                    v-show="!isMobile"
                    class="flex items-center text-white px-4 py-2 rounded-full transition duration-300 ease-in-out hover:bg-blue-800 hover:text-white hover:shadow-lg transform hover:scale-105 text-sm sm:text-base"
                >
                    <i
                        class="fas fa-bullhorn text-white text-lg sm:text-base"
                    ></i>
                    <span class="ml-2 text-base sm:text-sm text-white"
                        >Marketing Digital</span
                    >
                </a>
                <a
                    href="#"
                    v-show="!isMobile"
                    class="flex items-center text-white px-4 py-2 rounded-full transition duration-300 ease-in-out hover:bg-blue-800 hover:text-white hover:shadow-lg transform hover:scale-105 text-sm sm:text-base"
                >
                    <i
                        class="fas fa-chart-line text-white text-lg sm:text-base"
                    ></i>
                    <span class="ml-2 text-base sm:text-sm text-white"
                        >Estrategias de Marketing</span
                    >
                </a>
                <a
                    href="#"
                    v-show="!isMobile"
                    class="flex items-center text-white px-4 py-2 rounded-full transition duration-300 ease-in-out hover:bg-blue-800 hover:text-white hover:shadow-lg transform hover:scale-105 text-sm sm:text-base"
                >
                    <i class="fas fa-users text-white text-lg sm:text-base"></i>
                    <span class="ml-2 text-base sm:text-sm text-white"
                        >Gestión de Clientes</span
                    >
                </a>
                <a
                    href="#"
                    v-show="!isMobile"
                    class="flex items-center text-white px-4 py-2 rounded-full transition duration-300 ease-in-out hover:bg-blue-800 hover:text-white hover:shadow-lg transform hover:scale-105 text-sm sm:text-base"
                >
                    <i
                        class="fas fa-briefcase text-white text-lg sm:text-base"
                    ></i>
                    <span class="ml-2 text-base sm:text-sm text-white"
                        >Ventas y CRM</span
                    >
                </a>
            </nav>

            <div
                v-show="isMobile && isMenuOpen"
                class="flex flex-col ml-4 space-y-6"
            >
                <a href="#" class="flex items-center space-x-4">
                    <i class="fas fa-chalkboard-teacher text-white text-xl"></i>
                    <span class="text-lg text-white">Curso de CRM</span>
                </a>
                <a href="#" class="flex items-center space-x-4">
                    <i class="fas fa-bullhorn text-white text-xl"></i>
                    <span class="text-lg text-white">Marketing Digital</span>
                </a>
                <a href="#" class="flex items-center space-x-4">
                    <i class="fas fa-chart-line text-white text-xl"></i>
                    <span class="text-lg text-white"
                        >Estrategias de Marketing</span
                    >
                </a>
                <a href="#" class="flex items-center space-x-4">
                    <i class="fas fa-users text-white text-xl"></i>
                    <span class="text-lg text-white">Gestión de Clientes</span>
                </a>
                <a href="#" class="flex items-center space-x-4">
                    <i class="fas fa-briefcase text-white text-xl"></i>
                    <span class="text-lg text-white">Ventas y CRM</span>
                </a>
            </div>

            <!-- Mostrar los botones de autenticación solo si estamos en la página de login y en un dispositivo móvil -->
            <template v-if="isLoginPage && isMobile && isMenuOpen">
                <div class="flex items-center space-x-4 mt-4">
                    <AuthButtons
                        :canLogin="canLogin"
                        :canRegister="canRegister"
                    />
                </div>
            </template>
        </div>
    </header>
</template>

<script>
export default {
    data() {
        return {
            isMenuOpen: false,
            canLogin: true, // Cambiar según tu lógica
            canRegister: true, // Cambiar según tu lógica
        };
    },
    methods: {
        toggleMenu() {
            this.isMenuOpen = !this.isMenuOpen;
        },
    },
};
</script>

<style scoped>
/* Fondo principal del header */
.header {
    background: #140b5c; /* Azul oscuro */
    color: white;
    font-family: Arial, sans-serif;
}

/* Fondo del menú de navegación */
.header-bottom {
    background: #6c63ff; /* Morado vibrante */
    padding: 0px 0;
}

.header-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 20px;
    flex-wrap: nowrap;
}

/* Logo */
.logo-container {
    display: flex;
    align-items: center;
    margin-right: 20px;
}

.logo {
    height: 40px;
}

.logo-text {
    color: white;
    font-weight: bold;
    font-size: 25px;
    margin-left: 10px;
}

/* Navegación superior */
.top-nav {
    display: flex;
    gap: 20px;
    margin-right: 20px;
}

.top-nav a {
    color: white;
    text-decoration: none;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 5px;
    border: 2px solid white;
    padding: 8px 15px;
    border-radius: 20px;
    transition: 0.3s;
}

.top-nav a:hover {
    background-color: #2e9f50; /* Verde */
    color: white;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
}

/* Menú Hamburguesa */
.hamburger-menu {
    display: none;
    cursor: pointer;
    font-size: 30px;
    color: white;
}

.main-nav {
    display: flex;
    margin-left: 20px;
    justify-content: flex-start; /* Alinea las opciones a la izquierda */
    gap: 20px;
    margin-right: 20px; /* Si quieres que también tenga margen a la derecha */
}

.main-nav a {
    color: white;
    text-decoration: none;
    font-size: 16px;
    font-weight: bold;
    transition: 0.3s;
}

.main-nav a:hover {
    color: #e08a00; /* Naranja */
}

/* Responsividad */
@media screen and (max-width: 768px) {
    .header-top {
        flex-direction: row;
        align-items: center;
        text-align: center;
    }

    .logo-text {
        font-size: 20px;
        margin-left: 5px;
    }

    .top-nav {
        display: none;
    }

    .hamburger-menu {
        display: block;
    }

    .main-nav {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        margin-left: 1rem; /* Ajusta el margen según lo necesites */
    }

    .main-nav.active {
        display: flex;
    }

    .main-nav a {
        font-size: 14px;
        margin-bottom: 10px; /* Para agregar espacio entre las opciones */
    }
}

/* Estilos para pantallas grandes */
@media screen and (min-width: 500px) {
    .hamburger-menu {
        display: none;
    }

    .main-nav {
        margin-left: 15px; /* Añadir margen en dispositivos móviles */
        margin-right: 15px; /* Añadir margen derecho en dispositivos móviles */
    }
}
</style>
