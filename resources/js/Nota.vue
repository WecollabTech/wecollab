<template>
    <header class="header">
        <div class="header-top">
            <div class="logo-container">
                <img src="/img/logo.png" alt="Atrápalo" class="logo" />
                <div class="logo-text">SISTEMAGT</div>
            </div>

            <!-- Barra de navegación superior -->
            <nav class="top-nav">
                <a href="#"><i class="fas fa-search"></i></a>
                <a href="#"><i class="fas fa-briefcase"></i> EMPRESAS</a>
                <a href="#"><i class="fas fa-lightbulb"></i> INSPÍRATE</a>
                <a href="#"><i class="fas fa-question-circle"></i> AYUDA</a>
                <a href="#"><i class="fas fa-user"></i></a>

                <!-- Opciones de login, registro y dashboard -->
                <div v-if="canLogin" class="auth-links">
                    <!-- Si el usuario está logueado, mostrar enlace al Dashboard -->
                    <Link
                        v-if="$page.props.auth.user"
                        :href="route('dashboard')"
                        class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                    >
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </Link>

                    <!-- Si el usuario no está logueado, mostrar "Log in" y "Register" -->
                    <template v-else>
                        <Link
                            :href="route('login')"
                            class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                        >
                            <i class="fas fa-sign-in-alt"></i> Log in
                        </Link>

                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                            class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                        >
                            <i class="fas fa-user-plus"></i> Register
                        </Link>
                    </template>
                </div>
            </nav>

            <!-- Menú hamburguesa -->
            <div class="hamburger-menu" @click="toggleMenu">
                <i class="fas fa-bars"></i>
            </div>
        </div>

        <div class="header-bottom">
            <nav :class="{ 'main-nav': true, active: isMenuOpen }">
                <a href="#">Vuelos</a>
                <a href="#">Hoteles</a>
                <a href="#">Vuelo+Hotel</a>
                <a href="#">Paquetes</a>
                <a href="#">Cruceros</a>
                <a href="#">Autos</a>
                <a href="#">Entradas</a>
                <a href="#">Actividades</a>
                <a href="#">Giftcard</a>
            </nav>
        </div>
    </header>
</template>

<script>
export default {
    data() {
        return {
            isMenuOpen: false,
            canLogin: true, // Puedes cambiar este valor dependiendo de si el usuario está autenticado
            canRegister: true, // Puedes agregar lógica para habilitar o deshabilitar el registro
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
.header {
    background-color: #e60012;
    color: white;
    font-family: Arial, sans-serif;
}

.header-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 20px;
    flex-wrap: nowrap; /* Asegura que todo esté en una sola fila */
}

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
}

.auth-links {
    display: flex;
    align-items: center;
    gap: 15px;
}

.auth-links a {
    display: flex;
    align-items: center;
    gap: 5px;
}

.hamburger-menu {
    display: none;
    cursor: pointer;
    font-size: 30px;
    color: white;
}

.header-bottom {
    background-color: #d60010;
    padding: 10px 0;
}

.main-nav {
    display: flex;
    justify-content: flex-start;
    gap: 20px;
    margin-left: 20px;
}

.main-nav a {
    color: white;
    text-decoration: none;
    font-size: 16px;
    font-weight: bold;
}

/* Responsividad */
@media screen and (max-width: 768px) {
    .header-top {
        flex-direction: row; /* Asegura que todo se mantenga en una sola fila */
        align-items: center;
        text-align: center;
    }

    .logo-text {
        font-size: 20px;
        margin-left: 5px;
    }

    .top-nav {
        display: none; /* Ocultar los enlaces en pantallas pequeñas */
    }

    .hamburger-menu {
        display: block; /* Mostrar el icono del menú hamburguesa */
    }

    .main-nav {
        display: none; /* Escondemos el menú en pantallas pequeñas */
        flex-direction: column;
        gap: 10px;
        text-align: center;
        margin-left: 0;
    }

    .main-nav.active {
        display: flex; /* Mostrar el menú cuando el menú está abierto */
    }

    .main-nav a {
        font-size: 14px;
    }
}
</style>

<template>
    <header class="header">
        <div class="header-top">
            <div class="logo-container">
                <img src="/img/logo.png" alt="Atrápalo" class="logo" />
                <div class="logo-text">SISTEMAGT</div>
            </div>
            <nav class="top-nav">
                <a href="#"><i class="fas fa-search"></i></a>
                <a href="#"><i class="fas fa-briefcase"></i> EMPRESAS</a>
                <a href="#"><i class="fas fa-lightbulb"></i> INSPÍRATE</a>
                <a href="#"><i class="fas fa-question-circle"></i> AYUDA</a>

                <!-- Nuevas opciones basadas en la condición de login -->

                <nav v-if="canLogin" class="-mx-3 flex flex-1 justify-end">
                    <Link
                        v-if="$page.props.auth.user"
                        :href="route('dashboard')"
                        class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                    >
                        <a href="#"
                            ><i class="fas fa-tachometer-alt"></i> DASHBOARD</a
                        >
                        <!-- Nueva opción -->
                    </Link>

                    <template v-else>
                        <Link
                            :href="route('login')"
                            class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                        >
                            <a href="#"
                                ><i class="fas fa-sign-in-alt"></i> INICIO DE
                                SESIÓN</a
                            >
                            <!-- Nueva opción -->
                        </Link>

                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                            class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                        >
                            <a href="#"
                                ><i class="fas fa-user-plus"></i> REGISTRO</a
                            >
                            <!-- Nueva opción -->
                        </Link>
                    </template>
                </nav>
            </nav>
            <div class="hamburger-menu" @click="toggleMenu">
                <i class="fas fa-bars"></i>
            </div>
        </div>

        <div class="header-bottom">
            <nav :class="{ 'main-nav': true, active: isMenuOpen }">
                <a href="#">Vuelos</a>
                <a href="#">Hoteles</a>
                <a href="#">Vuelo+Hotel</a>
                <a href="#">Paquetes</a>
                <a href="#">Cruceros</a>
                <a href="#">Autos</a>
                <a href="#">Entradas</a>
                <a href="#">Actividades</a>
                <a href="#">Giftcard</a>
            </nav>
        </div>
    </header>
</template>

<!-- <style scoped>
.header {
    background-color: #e60012;
    color: white;
    font-family: Arial, sans-serif;
}

.header-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 20px;
    flex-wrap: nowrap;
}

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
}

.hamburger-menu {
    display: none;
    cursor: pointer;
    font-size: 30px;
    color: white;
}

.header-bottom {
    background-color: #d60010;
    padding: 10px 0;
}

.main-nav {
    display: flex;
    justify-content: flex-start;
    gap: 20px;
    margin-left: 20px;
}

.main-nav a {
    color: white;
    text-decoration: none;
    font-size: 16px;
    font-weight: bold;
}

/* Responsividad */
@media screen and (max-width: 768px) {
    .nav-text {
        display: none; /* Oculta el texto en pantallas pequeñas */
    }

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
        display: none;
        flex-direction: column;
        gap: 10px;
        text-align: center;
        margin-left: 0;
    }

    .main-nav.active {
        display: flex;
    }

    .main-nav a {
        font-size: 14px;
    }

    /* Botones de Login y Registro solo cuando el menú hamburguesa está abierto */
    .header-bottom .hamburger-nav {
        display: block;
        flex-direction: column;
        gap: 10px;
    }

    .header-bottom .hamburger-nav .nav-link {
        text-decoration: none;
        font-size: 14px;
        color: white;
    }
}

/* Estilos para pantallas grandes (mayores a 768px) */
@media screen and (min-width: 500px) {
    .hamburger-menu {
        display: none; /* Oculta el menú hamburguesa en pantallas grandes */
    }

    .main-nav {
        display: flex; /* Muestra las opciones de navegación */
    }

    .header-bottom .hamburger-nav {
        display: none; /* Asegura que los botones de login y registro no se muestren */
    }
}
</style> -->

<!-- <div class="header-bottom" v-show="!isMobile || isMenuOpen">
            <nav
                :class="{ 'main-nav': true, active: isMenuOpen }"
                class="flex space-x-8"
            >
                <a href="#" v-show="!isMobile" class="flex items-center">
                    <i class="fas fa-chalkboard-teacher text-white"></i>
                    <span class="ml-2">Curso de CRM</span>
                </a>
                <a href="#" v-show="!isMobile" class="flex items-center">
                    <i class="fas fa-bullhorn text-white"></i>
                    <span class="ml-2">Marketing Digital</span>
                </a>
                <a href="#" v-show="!isMobile" class="flex items-center">
                    <i class="fas fa-chart-line text-white"></i>
                    <span class="ml-2">Estrategias de Marketing</span>
                </a>
                <a href="#" v-show="!isMobile" class="flex items-center">
                    <i class="fas fa-users text-white"></i>
                    <span class="ml-2">Gestión de Clientes</span>
                </a>
                <a href="#" v-show="!isMobile" class="flex items-center">
                    <i class="fas fa-briefcase text-white"></i>
                    <span class="ml-2">Ventas y CRM</span>
                </a>

   
                <div
                    v-show="isMobile && isMenuOpen"
                    class="flex flex-col ml-4 space-y-6"
                >
                    <a href="#" class="flex items-center">
                        <i class="fas fa-chalkboard-teacher text-white"></i>
                        <span class="ml-2">Curso de CRM</span>
                    </a>
                    <a href="#" class="flex items-center">
                        <i class="fas fa-bullhorn text-white"></i>
                        <span class="ml-2">Marketing Digital</span>
                    </a>
                    <a href="#" class="flex items-center">
                        <i class="fas fa-chart-line text-white"></i>
                        <span class="ml-2">Estrategias de Marketing</span>
                    </a>
                    <a href="#" class="flex items-center">
                        <i class="fas fa-users text-white"></i>
                        <span class="ml-2">Gestión de Clientes</span>
                    </a>
                    <a href="#" class="flex items-center">
                        <i class="fas fa-briefcase text-white"></i>
                        <span class="ml-2">Ventas y CRM</span>
                    </a>

                    
                    <div class="flex items-center space-x-2 mt-4 ml-2">
                        <template v-if="isMobile && isMenuOpen">
                            <AuthButtons
                                :canLogin="canLogin"
                                :canRegister="canRegister"
                            />
                        </template>
                    </div>
                </div>
            </nav>
        </div> -->

<script setup>
import { ref } from "vue";
import { Swiper, SwiperSlide } from "swiper/vue";
import "swiper/css";
import "swiper/css/navigation";
import "swiper/css/pagination";
import { Navigation, Pagination, Autoplay } from "swiper/modules";

// Importamos los componentes Header y Footer
import Header from "@/Vistas/Header.vue";
import Footer from "@/Vistas/Footer.vue";

// Datos para el formulario de búsqueda de hoteles
const destino = ref("");
const fechaEntrada = ref("");
const fechaSalida = ref("");
const huespedes = ref("1 habitación, 2 huéspedes");

const buscarHoteles = () => {
    console.log(
        "Buscando hoteles en:",
        destino.value,
        fechaEntrada.value,
        fechaSalida.value,
        huespedes.value
    );
};

// Lista de imágenes para el carrusel
const imagenes = [
    "https://assets.turismocity.com/hotelImages/1678977396639_000.jpg",
    "https://assets.turismocity.com/hotelImages/1678977396639_000.jpg",
    "https://assets.turismocity.com/hotelImages/1678977396639_000.jpg",
    "https://assets.turismocity.com/hotelImages/1678977396639_000.jpg",
];

// Datos para el carrusel de tutoriales destacados
const tutoriales = [
    {
        titulo: "Tutorial de Vue.js",
        descripcion: "Aprende los fundamentos de Vue.js",
        imagen: "https://via.placeholder.com/600x300",
    },
    {
        titulo: "Tutorial de React",
        descripcion: "Construye aplicaciones con React",
        imagen: "https://via.placeholder.com/600x300",
    },
    {
        titulo: "Tutorial de Node.js",
        descripcion: "Desarrolla aplicaciones con Node.js",
        imagen: "https://via.placeholder.com/600x300",
    },
];
</script>

<template>
    <div>
        <!-- Header -->
        <Header />

        <!-- Carrusel de imágenes -->
        <main class="relative w-full h-[60vh]">
            <Swiper
                :modules="[Navigation, Pagination, Autoplay]"
                :spaceBetween="0"
                :slidesPerView="1"
                :navigation="true"
                :pagination="{ clickable: true }"
                :autoplay="{ delay: 4000, disableOnInteraction: false }"
                class="w-full h-full"
            >
                <SwiperSlide v-for="(imagen, index) in imagenes" :key="index">
                    <div
                        class="relative w-full h-[60vh] bg-cover bg-center flex items-center justify-center"
                        :style="{ backgroundImage: `url(${imagen})` }"
                    >
                        <div class="absolute inset-0 bg-black/40"></div>

                        <div class="relative z-10 text-center text-white">
                            <h1 class="text-3xl font-bold">
                                BIENVENIDOS A WE COLLAB
                            </h1>
                            <p class="mt-2 text-xl font-semibold">
                                Disfruta de nuestros tutoriales en línea
                            </p>
                        </div>
                    </div>
                </SwiperSlide>
            </Swiper>
        </main>

        <!-- Carrusel de tutoriales destacados -->
        <section class="mt-10">
            <h2 class="text-center text-2xl font-bold mb-6">
                Tutoriales Destacados
            </h2>
            <Swiper
                :modules="[Navigation, Pagination, Autoplay]"
                :spaceBetween="20"
                :slidesPerView="3"
                :navigation="true"
                :pagination="{ clickable: true }"
                :autoplay="{ delay: 4000, disableOnInteraction: false }"
                class="w-full h-full"
            >
                <SwiperSlide
                    v-for="(tutorial, index) in tutoriales"
                    :key="index"
                >
                    <div
                        class="relative w-full bg-white rounded-lg shadow-lg overflow-hidden"
                    >
                        <img
                            :src="tutorial.imagen"
                            alt="Tutorial"
                            class="w-full h-40 object-cover"
                        />
                        <div class="p-4">
                            <h3 class="text-xl font-semibold">
                                {{ tutorial.titulo }}
                            </h3>
                            <p class="mt-2 text-lg">
                                {{ tutorial.descripcion }}
                            </p>
                        </div>
                    </div>
                </SwiperSlide>
            </Swiper>
        </section>

        <!-- Formulario de búsqueda de hoteles -->
        <section class="mt-10 bg-gray-100 p-6 rounded-lg">
            <h3 class="text-center text-xl font-semibold mb-4">
                Buscar Hoteles
            </h3>
        </section>

        <!-- Footer -->
        <Footer />
    </div>
</template>

<!-- <template v-else>
                <div
                    class="flex flex-col sm:flex-row items-center gap-4 ml-[0px]"
                >
                 
                    <Link
                        :href="route('login')"
                        class="flex items-center justify-center text-white sm:border sm:border-white px-4 py-2 rounded-full transition duration-300 ease-in-out hover:bg-green-500 hover:text-white text-lg sm:text-xl space-x-2"
                    >
                        <i class="fas fa-sign-in-alt text-xl"></i>
                        <span class="ml-2 sm:inline-block text-sm sm:text-base"
                            >Iniciar Sesión</span
                        >
                    </Link>

                    <Link
                        v-if="canRegister"
                        :href="route('register')"
                        class="flex items-center justify-center text-white sm:border sm:border-white px-4 py-2 rounded-full transition duration-300 ease-in-out hover:bg-green-500 hover:text-white text-xl sm:text-xl space-x-2"
                    >
                        <i class="fas fa-user-plus text-xl"></i>
                        <span class="ml-2 sm:inline-block text-sm sm:text-base"
                            >Registrarse</span
                        >
                    </Link>
                </div>
            </template> -->

<!-- <template v-else>
                <div
                    class="flex flex-col sm:flex-row items-center gap-2 ml-[0px]"
                >
                    <Link
                        :href="route('login')"
                        class="flex items-center justify-center text-white sm:border sm:border-white px-3 py-1 rounded-full transition duration-300 ease-in-out hover:bg-green-500 hover:text-white text-sm sm:text-base"
                    >
                        <i class="fas fa-sign-in-alt"></i>
                        <span class="ml-1 sm:inline-block">Iniciar Sesión</span>
                    </Link>

                    <Link
                        v-if="canRegister"
                        :href="route('register')"
                        class="flex items-center justify-center text-white sm:border sm:border-white px-3 py-1 rounded-full transition duration-300 ease-in-out hover:bg-green-500 hover:text-white text-sm sm:text-base"
                    >
                        <i class="fas fa-user-plus"></i>
                        <span class="ml-1 sm:inline-block">Registrarse</span>
                    </Link>
                </div>
            </template> -->

<!-- <template v-else>
                <Link
                    :href="route('login')"
                    class="flex items-center text-white sm:border sm:border-white px-1 py-1 rounded-full transition duration-300 ease-in-out hover:bg-green-500 hover:text-white text-sm sm:text-base"
                >
                    <i class="fas fa-sign-in-alt"></i>
                    <span class="ml-1 sm:inline-block">Iniciar Sesión</span>
                </Link>

                <Link
                    v-if="canRegister"
                    :href="route('register')"
                    class="sm:border sm:border-white p-2 rounded-full text-white flex items-center space-x-0 transition duration-300 ease-in-out hover:bg-green-500 hover:text-white text-sm sm:text-base"
                >
                    <i class="fas fa-user-plus"></i>
                    <span class="ml-2 sm:inline-block">Registrarse</span>
                </Link>
            </template> -->
<!-- <template v-else>
              
                <Link
                    :href="route('login')"
                    class="flex items-center text-white sm:border sm:border-white px-3 py-1 rounded-full transition duration-300 ease-in-out hover:bg-green-500 hover:text-white text-sm sm:text-base sm:ml-1"
                >
                    <i class="fas fa-sign-in-alt"></i>
                    <span class="ml-2 sm:inline-block">INICIO DE SESIÓN</span>
                </Link>

               
                <Link
                    v-if="canRegister"
                    :href="route('register')"
                    class="sm:border sm:border-white p-2 rounded-full text-white flex items-center space-x-2 transition duration-300 ease-in-out hover:bg-green-500 hover:text-white text-sm sm:text-base sm:ml-1"
                >
                    <i class="fas fa-user-plus"></i>
                    <span class="ml-2 sm:inline-block">REGISTRARSE</span>
                </Link>
            </template> -->

<!-- layouts  -->
<script setup>
import { ref } from "vue";
import { Head, Link, router } from "@inertiajs/vue3";
import ApplicationMark from "@/Components/ApplicationMark.vue";
import Banner from "@/Components/Banner.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import NavLink from "@/Components/NavLink.vue";
import ResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";
import Sidebar from "@/Componentes/Sidebar.vue"; // Importa el componente Sidebar

defineProps({
    title: String,
});

const showingNavigationDropdown = ref(false);

const switchToTeam = (team) => {
    router.put(
        route("current-team.update"),
        {
            team_id: team.id,
        },
        {
            preserveState: false,
        }
    );
};

const logout = () => {
    router.post(route("logout"));
};
</script>
<template>
    <div>
        <Head :title="title" />

        <Banner />

        <div class="min-h-screen bg-gray-100 flex">
            <!-- Sidebar -->
            <Sidebar
                :class="{
                    'hidden md:block': !isSidebarOpen,
                    block: isSidebarOpen,
                }"
            />

            <!-- Contenido principal -->
            <div class="flex-1 md:ml-64">
                <nav
                    class="bg-white border-b"
                    style="background-color: #6c63ff"
                >
                    <!-- Primary Navigation Menu -->
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div class="flex justify-between h-16 p-8">
                            <div class="flex">
                                <!-- Logo -->
                                <!-- <div class="shrink-0 flex items-center">
                                    <Link :href="route('dashboard')">
                                        <ApplicationMark class="block h-9 w-auto" />
                                    </Link>
                                </div> -->
                                <div
                                    class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex"
                                >
                                    <!-- <NavLink
                                        :href="route('dashboard')"
                                        :active="route().current('dashboard')"
                                    >
                                        Dashboard
                                    </NavLink> -->
                                </div>
                            </div>

                            <div class="hidden sm:flex sm:items-center sm:ms-6">
                                <div class="ms-3 relative">
                                    <!-- Teams Dropdown -->
                                    <Dropdown
                                        v-if="
                                            $page.props.jetstream
                                                .hasTeamFeatures
                                        "
                                        align="right"
                                        width="60"
                                    >
                                        <template #trigger>
                                            <span
                                                class="inline-flex rounded-md"
                                            >
                                                <button
                                                    type="button"
                                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150"
                                                >
                                                    {{
                                                        $page.props.auth.user
                                                            .current_team.name
                                                    }}

                                                    <svg
                                                        class="ms-2 -me-0.5 size-4"
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        fill="none"
                                                        viewBox="0 0 24 24"
                                                        stroke-width="1.5"
                                                        stroke="currentColor"
                                                    >
                                                        <path
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9"
                                                        />
                                                    </svg>
                                                </button>
                                            </span>
                                        </template>

                                        <template #content>
                                            <div class="w-60">
                                                <!-- Team Management -->
                                                <div
                                                    class="block px-4 py-2 text-xs text-gray-400"
                                                >
                                                    Gestionar equipo
                                                </div>

                                                <!-- Team Settings -->
                                                <DropdownLink
                                                    :href="
                                                        route(
                                                            'teams.show',
                                                            $page.props.auth
                                                                .user
                                                                .current_team
                                                        )
                                                    "
                                                >
                                                    Team Settings
                                                </DropdownLink>

                                                <DropdownLink
                                                    v-if="
                                                        $page.props.jetstream
                                                            .canCreateTeams
                                                    "
                                                    :href="
                                                        route('teams.create')
                                                    "
                                                >
                                                    Create New Team
                                                </DropdownLink>

                                                <!-- Team Switcher -->
                                                <template
                                                    v-if="
                                                        $page.props.auth.user
                                                            .all_teams.length >
                                                        1
                                                    "
                                                >
                                                    <div
                                                        class="border-t border-gray-200"
                                                    />

                                                    <div
                                                        class="block px-4 py-2 text-xs text-gray-400"
                                                    >
                                                        Switch Teams
                                                    </div>

                                                    <template
                                                        v-for="team in $page
                                                            .props.auth.user
                                                            .all_teams"
                                                        :key="team.id"
                                                    >
                                                        <form
                                                            @submit.prevent="
                                                                switchToTeam(
                                                                    team
                                                                )
                                                            "
                                                        >
                                                            <DropdownLink
                                                                as="button"
                                                            >
                                                                <div
                                                                    class="flex items-center"
                                                                >
                                                                    <svg
                                                                        v-if="
                                                                            team.id ==
                                                                            $page
                                                                                .props
                                                                                .auth
                                                                                .user
                                                                                .current_team_id
                                                                        "
                                                                        class="me-2 size-5 text-green-400"
                                                                        xmlns="http://www.w3.org/2000/svg"
                                                                        fill="none"
                                                                        viewBox="0 0 24 24"
                                                                        stroke-width="1.5"
                                                                        stroke="currentColor"
                                                                    >
                                                                        <path
                                                                            stroke-linecap="round"
                                                                            stroke-linejoin="round"
                                                                            d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                                                        />
                                                                    </svg>

                                                                    <div>
                                                                        {{
                                                                            team.name
                                                                        }}
                                                                    </div>
                                                                </div>
                                                            </DropdownLink>
                                                        </form>
                                                    </template>
                                                </template>
                                            </div>
                                        </template>
                                    </Dropdown>
                                </div>

                                <!-- Texto "HOLA BIENVENIDO" al lado del botón del nombre del usuario -->
                                <div
                                    class="flex items-center justify-start ml-4"
                                >
                                    <h1
                                        class="text-white font-bold"
                                        style="
                                            font-family: 'Helvetica Neue',
                                                Helvetica, Arial, sans-serif;
                                            font-size: 15px;
                                        "
                                    >
                                        Hola Bienvenido
                                    </h1>
                                </div>

                                <!-- Settings Dropdown -->
                                <!-- <div class="ms-3 relative">
                                    <Dropdown align="right" width="48">
                                        <template #trigger>
                                            <button
                                                v-if="
                                                    $page.props.jetstream
                                                        .managesProfilePhotos
                                                "
                                                class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition"
                                            >
                                                <img
                                                    class="size-8 rounded-full object-cover"
                                                    :src="
                                                        $page.props.auth.user
                                                            .profile_photo_url
                                                    "
                                                    :alt="
                                                        $page.props.auth.user
                                                            .name
                                                    "
                                                />
                                            </button>

                                            <span
                                                v-else
                                                class="inline-flex rounded-md"
                                            >
                                                <button
                                                    type="button"
                                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150"
                                                >
                                                    {{
                                                        $page.props.auth.user
                                                            .name
                                                    }}

                                                    <svg
                                                        class="ms-2 -me-0.5 size-4"
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        fill="none"
                                                        viewBox="0 0 24 24"
                                                        stroke-width="1.5"
                                                        stroke="currentColor"
                                                    >
                                                        <path
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            d="M19.5 8.25l-7.5 7.5-7.5-7.5"
                                                        />
                                                    </svg>
                                                </button>
                                            </span>
                                        </template>

                                        <template #content>
                                           
                                            <div
                                                class="block px-4 py-2 text-xs text-gray-400"
                                            >
                                                Manage Account
                                            </div>

                                            <DropdownLink
                                                :href="route('profile.show')"
                                            >
                                                Profile
                                            </DropdownLink>

                                            <DropdownLink
                                                v-if="
                                                    $page.props.jetstream
                                                        .hasApiFeatures
                                                "
                                                :href="
                                                    route('api-tokens.index')
                                                "
                                            >
                                                API Tokens
                                            </DropdownLink>

                                            <div
                                                class="border-t border-gray-200"
                                            />

                                         
                                            <form @submit.prevent="logout">
                                                <DropdownLink as="button">
                                                    Log Out
                                                </DropdownLink>
                                            </form>
                                        </template>
                                    </Dropdown>
                                </div> -->

                                <div class="ms-3 relative">
                                    <Dropdown align="right" width="48">
                                        <template #trigger>
                                            <!-- Profile Picture or Name Button -->
                                            <button
                                                v-if="
                                                    $page.props.jetstream
                                                        .managesProfilePhotos
                                                "
                                                class="flex items-center justify-center p-1 bg-gray-200 rounded-full shadow-md focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all"
                                            >
                                                <img
                                                    class="h-10 w-10 rounded-full object-cover"
                                                    :src="
                                                        $page.props.auth.user
                                                            .profile_photo_url
                                                    "
                                                    :alt="
                                                        $page.props.auth.user
                                                            .name
                                                    "
                                                />
                                            </button>

                                            <span
                                                v-else
                                                class="inline-flex rounded-md"
                                            >
                                                <button
                                                    type="button"
                                                    class="inline-flex items-center px-3 py-2 text-sm leading-4 font-medium rounded-md text-gray-700 bg-white border border-gray-300 hover:text-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition-all"
                                                >
                                                    {{
                                                        $page.props.auth.user
                                                            .name
                                                    }}

                                                    <svg
                                                        class="ms-2 -me-0.5 h-4 w-4"
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        fill="none"
                                                        viewBox="0 0 24 24"
                                                        stroke-width="1.5"
                                                        stroke="currentColor"
                                                    >
                                                        <path
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            d="M19.5 8.25l-7.5 7.5-7.5-7.5"
                                                        />
                                                    </svg>
                                                </button>
                                            </span>
                                        </template>

                                        <template #content>
                                            <!-- Account Management -->
                                            <div
                                                class="block px-4 py-2 text-xs text-gray-400"
                                            >
                                                Account Management
                                            </div>

                                            <DropdownLink
                                                :href="route('profile.show')"
                                                class="text-sm text-gray-600 hover:text-gray-800"
                                            >
                                                Profile
                                            </DropdownLink>

                                            <DropdownLink
                                                v-if="
                                                    $page.props.jetstream
                                                        .hasApiFeatures
                                                "
                                                :href="
                                                    route('api-tokens.index')
                                                "
                                                class="text-sm text-gray-600 hover:text-gray-800"
                                            >
                                                API Tokens
                                            </DropdownLink>

                                            <div
                                                class="border-t border-gray-200"
                                            ></div>

                                            <!-- Authentication -->
                                            <form @submit.prevent="logout">
                                                <DropdownLink
                                                    as="button"
                                                    class="text-sm text-red-600 hover:text-red-800"
                                                >
                                                    Log Out
                                                </DropdownLink>
                                            </form>
                                        </template>
                                    </Dropdown>
                                </div>
                            </div>

                            <!-- Hamburger -->
                            <div class="-me-2 flex items-center sm:hidden">
                                <button
                                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out"
                                    @click="
                                        showingNavigationDropdown =
                                            !showingNavigationDropdown
                                    "
                                >
                                    <svg
                                        class="size-6"
                                        stroke="currentColor"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            :class="{
                                                hidden: showingNavigationDropdown,
                                                'inline-flex':
                                                    !showingNavigationDropdown,
                                            }"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M4 6h16M4 12h16M4 18h16"
                                        />
                                        <path
                                            :class="{
                                                hidden: !showingNavigationDropdown,
                                                'inline-flex':
                                                    showingNavigationDropdown,
                                            }"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"
                                        />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Responsive Navigation Menu -->
                    <div
                        :class="{
                            block: showingNavigationDropdown,
                            hidden: !showingNavigationDropdown,
                        }"
                        class="sm:hidden"
                    >
                        <div class="pt-2 pb-3 space-y-1">
                            <ResponsiveNavLink
                                :href="route('dashboard')"
                                :active="route().current('dashboard')"
                            >
                                Dashboard
                            </ResponsiveNavLink>
                        </div>

                        <!-- Responsive Settings Options -->
                        <div class="pt-4 pb-1 border-t border-gray-200">
                            <div class="flex items-center px-4">
                                <div
                                    v-if="
                                        $page.props.jetstream
                                            .managesProfilePhotos
                                    "
                                    class="shrink-0 me-3"
                                >
                                    <img
                                        class="size-10 rounded-full object-cover"
                                        :src="
                                            $page.props.auth.user
                                                .profile_photo_url
                                        "
                                        :alt="$page.props.auth.user.name"
                                    />
                                </div>

                                <div>
                                    <div
                                        class="font-medium text-base text-gray-800"
                                    >
                                        {{ $page.props.auth.user.name }}
                                    </div>
                                    <div
                                        class="font-medium text-sm text-gray-500"
                                    >
                                        {{ $page.props.auth.user.email }}
                                    </div>
                                </div>
                            </div>

                            <div class="mt-3 space-y-1">
                                <ResponsiveNavLink
                                    :href="route('profile.show')"
                                    :active="route().current('profile.show')"
                                >
                                    Profile
                                </ResponsiveNavLink>

                                <ResponsiveNavLink
                                    v-if="$page.props.jetstream.hasApiFeatures"
                                    :href="route('api-tokens.index')"
                                    :active="
                                        route().current('api-tokens.index')
                                    "
                                >
                                    API Tokens
                                </ResponsiveNavLink>

                                <!-- Authentication -->
                                <form method="POST" @submit.prevent="logout">
                                    <ResponsiveNavLink as="button">
                                        Log Out
                                    </ResponsiveNavLink>
                                </form>

                                <!-- Team Management -->
                                <template
                                    v-if="$page.props.jetstream.hasTeamFeatures"
                                >
                                    <div class="border-t border-gray-200" />

                                    <div
                                        class="block px-4 py-2 text-xs text-gray-400"
                                    >
                                        Manage Team
                                    </div>

                                    <!-- Team Settings -->
                                    <ResponsiveNavLink
                                        :href="
                                            route(
                                                'teams.show',
                                                $page.props.auth.user
                                                    .current_team
                                            )
                                        "
                                        :active="route().current('teams.show')"
                                    >
                                        Team Settings
                                    </ResponsiveNavLink>

                                    <ResponsiveNavLink
                                        v-if="
                                            $page.props.jetstream.canCreateTeams
                                        "
                                        :href="route('teams.create')"
                                        :active="
                                            route().current('teams.create')
                                        "
                                    >
                                        Create New Team
                                    </ResponsiveNavLink>

                                    <!-- Team Switcher -->
                                    <template
                                        v-if="
                                            $page.props.auth.user.all_teams
                                                .length > 1
                                        "
                                    >
                                        <div class="border-t border-gray-200" />

                                        <div
                                            class="block px-4 py-2 text-xs text-gray-400"
                                        >
                                            Switch Teams
                                        </div>

                                        <template
                                            v-for="team in $page.props.auth.user
                                                .all_teams"
                                            :key="team.id"
                                        >
                                            <form
                                                @submit.prevent="
                                                    switchToTeam(team)
                                                "
                                            >
                                                <ResponsiveNavLink as="button">
                                                    <div
                                                        class="flex items-center"
                                                    >
                                                        <svg
                                                            v-if="
                                                                team.id ==
                                                                $page.props.auth
                                                                    .user
                                                                    .current_team_id
                                                            "
                                                            class="me-2 size-5 text-green-400"
                                                            xmlns="http://www.w3.org/2000/svg"
                                                            fill="none"
                                                            viewBox="0 0 24 24"
                                                            stroke-width="1.5"
                                                            stroke="currentColor"
                                                        >
                                                            <path
                                                                stroke-linecap="round"
                                                                stroke-linejoin="round"
                                                                d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                                            />
                                                        </svg>
                                                        <div>
                                                            {{ team.name }}
                                                        </div>
                                                    </div>
                                                </ResponsiveNavLink>
                                            </form>
                                        </template>
                                    </template>
                                </template>
                            </div>
                        </div>
                    </div>
                </nav>

                <!-- Page Heading -->
                <header
                    v-if="$slots.header"
                    class="shadow bg-blue-500 border-none -mt-1"
                >
                    <div class="max-w-7xl mx-auto py-3 px-4 sm:px-6 lg:px-8">
                        <slot name="header" />
                    </div>
                </header>

                <!-- Page Content -->
                <main
                    class="fixed top-16 left-0 md:left-64 right-0 bottom-0 overflow-y-auto p-4 z-40"
                >
                    <slot />
                </main>
            </div>
        </div>
    </div>
</template>

<template>
    <aside
        :class="{
            'bg-white text-gray-800': isMobile,
            'bg-[#140b5c] text-white': !isMobile,
        }"
        class="w-64 min-h-screen flex-shrink-0 shadow-lg fixed inset-y-0 left-0 overflow-y-auto transition-transform duration-300 transform"
    >
        <div class="p-6">
            <a
                :href="route('dashboard')"
                :active="route().current('dashboard')"
            >
                <div
                    class="logo-container flex items-center"
                    style="margin-top: -1px"
                >
                    <img
                        src="/img/logo.png"
                        alt="Atrápalo"
                        class="logo h-8 w-auto mr-2"
                    />
                    <div class="text-2xl font-semibold">SISTEMAGT</div>
                </div>
            </a>

            <hr class="border-gray-500 opacity-50 my-2" />

            <ul class="mt-6 space-y-2">
                <!-- Dashboard -->
                <li>
                    <NavLink
                        :href="route('dashboard')"
                        :active="route().current('dashboard')"
                        class="menu-item"
                    >
                        <i class="fas fa-tachometer-alt text-lg"></i>
                        <span>Dashboard</span>
                    </NavLink>
                </li>

                <!-- Profile -->
                <li>
                    <NavLink
                        :href="route('profile.show')"
                        :active="route().current('profile.show')"
                        class="menu-item"
                    >
                        <i class="fas fa-user text-lg"></i>
                        <span>Profile</span>
                    </NavLink>
                </li>

                <hr class="border-gray-500 opacity-50 my-4" />

                <!-- Tutoriales -->
                <li>
                    <div class="menu-header" @click="toggleSubmenu('tutorial')">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-columns"></i>
                            <span>Tutorial</span>
                        </div>
                        <i
                            class="fas fa-chevron-down transition-transform duration-300"
                            :class="{ 'rotate-180': isSubmenuOpen.tutorial }"
                        ></i>
                    </div>
                    <ul v-if="isSubmenuOpen.tutorial" class="submenu">
                        <li>
                            <NavLink href="#" class="submenu-item">
                                <i class="fas fa-layer-group"></i>
                                Default Layout
                            </NavLink>
                        </li>
                        <li>
                            <NavLink href="#" class="submenu-item">
                                <i class="fas fa-eye"></i>
                                Transparent Sidebar
                            </NavLink>
                        </li>
                        <li>
                            <NavLink href="#" class="submenu-item">
                                <i class="fas fa-bars"></i>
                                Top Navigation
                            </NavLink>
                        </li>
                    </ul>
                </li>

                <hr class="border-gray-500 opacity-50 my-4" />

                <!-- Usuarios -->
                <li>
                    <div class="menu-header" @click="toggleSubmenu('usuarios')">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-users"></i>
                            <span>Usuarios</span>
                        </div>
                        <i
                            class="fas fa-chevron-down transition-transform duration-300"
                            :class="{ 'rotate-180': isSubmenuOpen.usuarios }"
                        ></i>
                    </div>
                    <ul v-if="isSubmenuOpen.usuarios" class="submenu">
                        <li>
                            <NavLink href="#" class="submenu-item">
                                <i class="fas fa-user-plus"></i>
                                Crear Usuario
                            </NavLink>
                        </li>
                        <li>
                            <NavLink href="#" class="submenu-item">
                                <i class="fas fa-user-edit"></i>
                                Editar Usuario
                            </NavLink>
                        </li>
                        <li>
                            <NavLink href="#" class="submenu-item">
                                <i class="fas fa-user-times"></i>
                                Eliminar Usuario
                            </NavLink>
                        </li>
                    </ul>
                </li>

                <hr class="border-gray-500 opacity-50 my-4" />

                <!-- Configuracion -->
                <li>
                    <div
                        class="menu-header"
                        @click="toggleSubmenu('configuracion')"
                    >
                        <div class="flex items-center gap-3">
                            <i class="fab fa-bootstrap"></i>
                            <span>Configuracion</span>
                        </div>
                        <i
                            class="fas fa-chevron-down transition-transform duration-300"
                            :class="{
                                'rotate-180': isSubmenuOpen.configuracion,
                            }"
                        ></i>
                    </div>
                    <ul v-if="isSubmenuOpen.configuracion" class="submenu">
                        <li>
                            <NavLink href="#" class="submenu-item">
                                <i class="fas fa-exclamation-circle"></i>
                                Alert
                            </NavLink>
                        </li>
                        <li>
                            <NavLink href="#" class="submenu-item">
                                <i class="fas fa-certificate"></i>
                                Badge
                            </NavLink>
                        </li>
                        <li>
                            <NavLink href="#" class="submenu-item">
                                <i class="fas fa-map-signs"></i>
                                Breadcrumb
                            </NavLink>
                        </li>
                    </ul>
                </li>

                <hr class="border-gray-500 opacity-50 my-4" />

                <!-- User Menu (Integrado en el Sidebar) -->
                <li>
                    <div class="menu-header" @click="toggleSubmenu('userMenu')">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-user-circle text-lg"></i>
                            <span>Mi Cuenta</span>
                        </div>
                        <i
                            class="fas fa-chevron-down transition-transform duration-300"
                            :class="{ 'rotate-180': isSubmenuOpen.userMenu }"
                        ></i>
                    </div>
                    <ul v-if="isSubmenuOpen.userMenu" class="submenu">
                        <li>
                            <NavLink
                                :href="route('profile.show')"
                                class="submenu-item"
                            >
                                <i class="fas fa-user text-lg"></i>
                                <span>Perfil</span>
                            </NavLink>
                        </li>
                        <li v-if="$page.props.jetstream.hasApiFeatures">
                            <NavLink
                                :href="route('api-tokens.index')"
                                class="submenu-item"
                            >
                                <i class="fas fa-key text-lg"></i>
                                <span>API Tokens</span>
                            </NavLink>
                        </li>
                        <li>
                            <form @submit.prevent="logout">
                                <NavLink as="button" class="submenu-item">
                                    <i
                                        class="fas fa-right-from-bracket text-lg"
                                    ></i>
                                    <span>Cerrar Sesión</span>
                                </NavLink>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </aside>
</template>

<script setup>
import { ref } from "vue";
import NavLink from "@/Components/NavLink.vue";
import { router } from "@inertiajs/vue3";

const isSubmenuOpen = ref({
    tutorial: false,
    usuarios: false,
    configuracion: false,
    userMenu: false, // Nuevo estado para el menú de usuario
});

const toggleSubmenu = (menu) => {
    isSubmenuOpen.value[menu] = !isSubmenuOpen.value[menu];
};

const logout = () => {
    router.post(route("logout"));
};

defineProps({
    isOpen: {
        type: Boolean,
        required: true,
    },
    isMobile: {
        type: Boolean,
        default: false,
    },
});
</script>

<style scoped>
.menu-item,
.submenu-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    transition: background-color 0.2s, color 0.2s;
}

.menu-item:hover,
.submenu-item:hover {
    background-color: #4caf50;
    color: white;
}

.menu-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.75rem;
    border-radius: 0.5rem;
    cursor: pointer;
    transition: background-color 0.2s;
}

.menu-header:hover {
    background-color: #4caf50;
}

.submenu {
    padding-left: 1.5rem;
    margin-top: 0.5rem;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.rotate-180 {
    transform: rotate(180deg);
}
</style>

<script setup>
import { ref, onMounted, onUnmounted } from "vue";
import { Head, Link, router } from "@inertiajs/vue3";
import ApplicationMark from "@/Components/ApplicationMark.vue";
import Banner from "@/Components/Banner.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import NavLink from "@/Components/NavLink.vue";
import ResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";
import Sidebar from "@/Componentes/Sidebar.vue";

defineProps({
    title: String,
});

const showingNavigationDropdown = ref(false);

const handleResize = () => {
    if (window.innerWidth >= 500) {
        showingNavigationDropdown.value = false; // Oculta el menú en pantallas grandes
    }
};

onMounted(() => {
    window.addEventListener("resize", handleResize);
    handleResize(); // Ejecutar al cargar para verificar el tamaño inicial
});

onUnmounted(() => {
    window.removeEventListener("resize", handleResize);
});

const switchToTeam = (team) => {
    router.put(
        route("current-team.update"),
        {
            team_id: team.id,
        },
        {
            preserveState: false,
        }
    );
};

const logout = () => {
    router.post(route("logout"));
};
</script>

<template>
    <div>
        <Head :title="title" />
        <Banner />

        <div class="min-h-screen bg-gray-100 flex">
            <!-- Sidebar -->
            <Sidebar
                :class="{
                    'hidden md:block': !isSidebarOpen,
                    block: isSidebarOpen,
                }"
            />

            <!-- Contenido principal -->
            <div class="flex-1 md:ml-64">
                <nav
                    class="bg-white border-b"
                    style="background-color: #6c63ff"
                >
                    <!-- Primary Navigation Menu -->
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div class="flex justify-between h-16 p-8">
                            <div class="flex">
                                <div class="shrink-0 flex items-center">
                                    <Link :href="route('dashboard')">
                                        <img
                                            src="/img/logo.png"
                                            alt="Atrápalo"
                                            class="block md:hidden h-8 w-auto mr-2"
                                        />
                                    </Link>
                                </div>

                                <!-- <div
                                    class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex"
                                >
                                    <NavLink
                                        :href="route('dashboard')"
                                        :active="route().current('dashboard')"
                                    >
                                        Inicio
                                    </NavLink>
                                </div> -->
                            </div>

                            <div class="hidden sm:flex sm:items-center sm:ms-6">
                                <!-- Teams Dropdown -->
                                <Dropdown
                                    v-if="$page.props.jetstream.hasTeamFeatures"
                                    align="right"
                                    width="60"
                                    class="z-50"
                                >
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button
                                                type="button"
                                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150"
                                            >
                                                {{
                                                    $page.props.auth.user
                                                        .current_team.name
                                                }}
                                                <svg
                                                    class="ms-2 -me-0.5 size-4"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    fill="none"
                                                    viewBox="0 0 24 24"
                                                    stroke-width="1.5"
                                                    stroke="currentColor"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9"
                                                    />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <div class="w-60">
                                            <!-- Team Management -->
                                            <div
                                                class="block px-4 py-2 text-xs text-gray-400"
                                            >
                                                Gestionar equipo
                                            </div>

                                            <!-- Team Settings -->
                                            <DropdownLink
                                                :href="
                                                    route(
                                                        'teams.show',
                                                        $page.props.auth.user
                                                            .current_team
                                                    )
                                                "
                                            >
                                                Team Settings
                                            </DropdownLink>

                                            <DropdownLink
                                                v-if="
                                                    $page.props.jetstream
                                                        .canCreateTeams
                                                "
                                                :href="route('teams.create')"
                                            >
                                                Create New Team
                                            </DropdownLink>

                                            <!-- Team Switcher -->
                                            <template
                                                v-if="
                                                    $page.props.auth.user
                                                        .all_teams.length > 1
                                                "
                                            >
                                                <div
                                                    class="border-t border-gray-200"
                                                />
                                                <div
                                                    class="block px-4 py-2 text-xs text-gray-400"
                                                >
                                                    Switch Teams
                                                </div>
                                                <template
                                                    v-for="team in $page.props
                                                        .auth.user.all_teams"
                                                    :key="team.id"
                                                >
                                                    <form
                                                        @submit.prevent="
                                                            switchToTeam(team)
                                                        "
                                                    >
                                                        <DropdownLink
                                                            as="button"
                                                        >
                                                            <div
                                                                class="flex items-center"
                                                            >
                                                                <svg
                                                                    v-if="
                                                                        team.id ==
                                                                        $page
                                                                            .props
                                                                            .auth
                                                                            .user
                                                                            .current_team_id
                                                                    "
                                                                    class="me-2 size-5 text-green-400"
                                                                    xmlns="http://www.w3.org/2000/svg"
                                                                    fill="none"
                                                                    viewBox="0 0 24 24"
                                                                    stroke-width="1.5"
                                                                    stroke="currentColor"
                                                                >
                                                                    <path
                                                                        stroke-linecap="round"
                                                                        stroke-linejoin="round"
                                                                        d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                                                    />
                                                                </svg>
                                                                <div>
                                                                    {{
                                                                        team.name
                                                                    }}
                                                                </div>
                                                            </div>
                                                        </DropdownLink>
                                                    </form>
                                                </template>
                                            </template>
                                        </div>
                                    </template>
                                </Dropdown>

                                <!-- Texto "HOLA BIENVENIDO" -->
                                <div
                                    class="flex items-center justify-start ml-4"
                                >
                                    <h1
                                        class="text-white font-bold"
                                        style="
                                            font-family: 'Helvetica Neue',
                                                Helvetica, Arial, sans-serif;
                                            font-size: 15px;
                                        "
                                    >
                                        Hola Bienvenido
                                    </h1>
                                </div>

                                <!-- Settings Dropdown -->
                                <div class="ms-3 relative">
                                    <Dropdown
                                        align="right"
                                        width="48"
                                        class="z-[9999] relative"
                                    >
                                        <template #trigger>
                                            <button
                                                v-if="
                                                    $page.props.jetstream
                                                        .managesProfilePhotos
                                                "
                                                class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition"
                                            >
                                                <img
                                                    class="size-8 rounded-full object-cover"
                                                    :src="
                                                        $page.props.auth.user
                                                            .profile_photo_url
                                                    "
                                                    :alt="
                                                        $page.props.auth.user
                                                            .name
                                                    "
                                                />
                                            </button>
                                            <span
                                                v-else
                                                class="inline-flex rounded-md"
                                            >
                                                <button
                                                    type="button"
                                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150"
                                                >
                                                    {{
                                                        $page.props.auth.user
                                                            .name
                                                    }}
                                                    <svg
                                                        class="ms-2 -me-0.5 size-4"
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        fill="none"
                                                        viewBox="0 0 24 24"
                                                        stroke-width="1.5"
                                                        stroke="currentColor"
                                                    >
                                                        <path
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            d="M19.5 8.25l-7.5 7.5-7.5-7.5"
                                                        />
                                                    </svg>
                                                </button>
                                            </span>
                                        </template>

                                        <template #content>
                                            <!-- Account Management -->
                                            <div
                                                class="block px-4 py-2 text-xs text-gray-400"
                                            >
                                                Administrar cuenta
                                            </div>
                                            <DropdownLink
                                                :href="route('profile.show')"
                                            >
                                                <div
                                                    class="flex items-center gap-x-2"
                                                >
                                                    <i
                                                        class="fas fa-user text-lg"
                                                    ></i>
                                                    <span
                                                        class="font-sans font-bold text-[15px]"
                                                        >Perfil</span
                                                    >
                                                </div>
                                            </DropdownLink>
                                            <DropdownLink
                                                v-if="
                                                    $page.props.jetstream
                                                        .hasApiFeatures
                                                "
                                                :href="
                                                    route('api-tokens.index')
                                                "
                                            >
                                                API Tokens
                                            </DropdownLink>
                                            <div
                                                class="border-t border-gray-200"
                                            />
                                            <form @submit.prevent="logout">
                                                <DropdownLink as="button">
                                                    <div
                                                        class="flex items-center gap-x-2"
                                                    >
                                                        <i
                                                            class="fas fa-right-from-bracket text-lg"
                                                        ></i>
                                                        <span
                                                            class="font-sans font-bold text-[15px]"
                                                            >Cerrar Sesión</span
                                                        >
                                                    </div>
                                                </DropdownLink>
                                            </form>
                                        </template>
                                    </Dropdown>
                                </div>
                            </div>

                            <!-- Hamburger -->
                            <div class="-me-2 flex items-center sm:hidden">
                                <button
                                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out"
                                    @click="
                                        showingNavigationDropdown =
                                            !showingNavigationDropdown
                                    "
                                >
                                    <svg
                                        class="size-6"
                                        stroke="currentColor"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            :class="{
                                                hidden: showingNavigationDropdown,
                                                'inline-flex':
                                                    !showingNavigationDropdown,
                                            }"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M4 6h16M4 12h16M4 18h16"
                                        />
                                        <path
                                            :class="{
                                                hidden: !showingNavigationDropdown,
                                                'inline-flex':
                                                    showingNavigationDropdown,
                                            }"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"
                                        />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Responsive Navigation Menu -->
                    <div
                        :class="{
                            block: showingNavigationDropdown,
                            hidden: !showingNavigationDropdown,
                        }"
                        class="lg:hidden fixed inset-y-0 left-0 w-64 bg-white shadow-lg z-[100]"
                        @click="showingNavigationDropdown = false"
                    >
                        <div
                            class="fixed inset-y-0 left-0 w-64 bg-white shadow-lg z-70"
                            @click.stop
                        >
                            <div class="pt-2 pb-3 space-y-1">
                                <ResponsiveNavLink
                                    :href="route('dashboard')"
                                    :active="route().current('dashboard')"
                                >
                                    Dashboard
                                </ResponsiveNavLink>
                            </div>

                            <!-- Responsive Settings Options -->
                            <div class="pt-4 pb-1 border-t border-gray-200">
                                <div class="flex items-center px-4">
                                    <div
                                        v-if="
                                            $page.props.jetstream
                                                .managesProfilePhotos
                                        "
                                        class="shrink-0 me-3"
                                    >
                                        <img
                                            class="size-10 rounded-full object-cover"
                                            :src="
                                                $page.props.auth.user
                                                    .profile_photo_url
                                            "
                                            :alt="$page.props.auth.user.name"
                                        />
                                    </div>

                                    <div>
                                        <div
                                            class="font-medium text-base text-gray-800"
                                        >
                                            {{ $page.props.auth.user.name }}
                                        </div>
                                        <div
                                            class="font-medium text-sm text-gray-500"
                                        >
                                            {{ $page.props.auth.user.email }}
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-3 space-y-1">
                                    <ResponsiveNavLink
                                        :href="route('profile.show')"
                                        :active="
                                            route().current('profile.show')
                                        "
                                    >
                                        <div class="flex items-center gap-x-2">
                                            <i class="fas fa-user text-lg"></i>
                                            <span
                                                class="font-sans font-bold text-[15px]"
                                                >Perfil</span
                                            >
                                        </div>
                                    </ResponsiveNavLink>

                                    <ResponsiveNavLink
                                        v-if="
                                            $page.props.jetstream.hasApiFeatures
                                        "
                                        :href="route('api-tokens.index')"
                                        :active="
                                            route().current('api-tokens.index')
                                        "
                                    >
                                        API Tokens
                                    </ResponsiveNavLink>

                                    <!-- Authentication -->
                                    <form
                                        method="POST"
                                        @submit.prevent="logout"
                                    >
                                        <ResponsiveNavLink as="button">
                                            Log Out
                                        </ResponsiveNavLink>
                                    </form>
                                    <Sidebar
                                        :isOpen="true"
                                        :isMobile="false"
                                        class="hidden lg:block"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </nav>

                <!-- Page Heading -->
                <header
                    v-if="$slots.header"
                    class="shadow bg-blue-500 border-none -mt-1 z-50 relative"
                >
                    <div class="max-w-7xl mx-auto py-3 px-4 sm:px-6 lg:px-8">
                        <h2
                            class="font-semibold text-xl text-white leading-tight"
                        >
                            <slot name="header" />
                        </h2>
                    </div>
                </header>

                <!-- Page Content -->
                <main
                    class="fixed top-16 left-0 md:left-64 right-0 bottom-0 overflow-y-auto p-4 z-40 bg-white"
                >
                    <slot />
                </main>
            </div>
        </div>
    </div>
</template>

<!-- Registro -->

<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";
import AuthenticationCard from "@/Components/AuthenticationCard.vue";
import AuthenticationCardLogo from "@/Components/AuthenticationCardLogo.vue";
import Checkbox from "@/Components/Checkbox.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import Header from "@/Vistas/Header.vue";
import Footer from "@/Vistas/Footer.vue";

const form = useForm({
    name: "",
    apellido: "",
    email: "",
    phone: "",
    password: "",
    password_confirmation: "",
    terms: false,
    status: "",
    direccion: "",
    fotoperfil: "", // Ahora es una cadena en lugar de un archivo
    telefono: "",
});

const handleFileUpload = (event) => {
    const file = event.target.files[0];
    if (file) {
        form.fotoperfil = URL.createObjectURL(file); // Solo para vista previa
        // Si necesitas guardar la URL en la base de datos, aquí debes subir el archivo a Firebase o tu backend
    }
};

const submit = () => {
    form.post(route("register"), {
        onFinish: () => {
            console.log(form.errors); // Muestra errores en consola
            form.reset("password", "password_confirmation");
        },
    });
};
</script>

<template>
    <div class="container">
        <!-- Header -->
        <Header :isLoginPage="true" :canLogin="true" :canRegister="true" />
        <Head title="Register" />

        <!-- Contenido del formulario -->
        <div class="content">
            <AuthenticationCard>
                <template #logo>
                    <AuthenticationCardLogo />
                </template>

                <form @submit.prevent="submit">
                    <div
                        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4"
                    >
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
                            <InputError
                                class="mt-2"
                                :message="form.errors.name"
                            />
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
                            <InputError
                                class="mt-2"
                                :message="form.errors.apellido"
                            />
                        </div>

                        <!-- Estado -->
                        <div>
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
                            <InputError
                                class="mt-2"
                                :message="form.errors.status"
                            />
                        </div>

                        <!-- Correo Electrónico -->
                        <div>
                            <InputLabel
                                for="email"
                                value="Correo Electrónico"
                            />
                            <TextInput
                                id="email"
                                v-model="form.email"
                                type="email"
                                class="mt-1 block w-full"
                                required
                                autocomplete="username"
                            />
                            <InputError
                                class="mt-2"
                                :message="form.errors.email"
                            />
                        </div>

                        <!-- Dirección -->
                        <div>
                            <InputLabel for="direccion" value="Dirección" />
                            <TextInput
                                id="direccion"
                                v-model="form.direccion"
                                type="text"
                                class="mt-1 block w-full"
                                required
                            />
                            <InputError
                                class="mt-2"
                                :message="form.errors.direccion"
                            />
                        </div>

                        <!-- Teléfono -->
                        <div>
                            <InputLabel for="telefono" value="Teléfono" />
                            <TextInput
                                id="telefono"
                                v-model="form.telefono"
                                type="text"
                                class="mt-1 block w-full"
                                required
                            />
                            <InputError
                                class="mt-2"
                                :message="form.errors.telefono"
                            />
                        </div>

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
                            <InputError
                                class="mt-2"
                                :message="form.errors.password"
                            />
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

                        <!-- Foto de Perfil -->
                        <div>
                            <InputLabel
                                for="fotoperfil"
                                value="Subir Foto de Perfil"
                            />
                            <input
                                id="fotoperfil"
                                type="file"
                                class="mt-1 block w-full"
                                @change="handleFileUpload"
                            />
                            <InputError
                                class="mt-2"
                                :message="form.errors.fotoperfil"
                            />
                        </div>

                        <!-- Nuevo Dato -->
                        <div>
                            <InputLabel for="nuevo1" value="Nuevo 1" />
                            <TextInput
                                id="nuevo1"
                                v-model="form.nuevo1"
                                type="text"
                                class="mt-1 block w-full"
                                required
                            />
                            <InputError
                                class="mt-2"
                                :message="form.errors.nuevo1"
                            />
                        </div>

                        <!-- Nuevo Dato -->
                        <div>
                            <InputLabel for="nuevo2" value="Nuevo 2" />
                            <TextInput
                                id="nuevo2"
                                v-model="form.nuevo2"
                                type="text"
                                class="mt-1 block w-full"
                                required
                            />
                            <InputError
                                class="mt-2"
                                :message="form.errors.nuevo2"
                            />
                        </div>

                        <!-- Términos y Política de Privacidad -->
                        <div
                            v-if="
                                $page.props.jetstream
                                    .hasTermsAndPrivacyPolicyFeature
                            "
                            class="col-span-1 sm:col-span-2 lg:col-span-3"
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
                                <InputError
                                    class="mt-2"
                                    :message="form.errors.terms"
                                />
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
        </div>

        <!-- Footer -->
        <Footer class="footer" />
    </div>
</template>

<style scoped>
/* Estilos para el contenedor principal */
.container {
    display: flex;
    flex-direction: column;
    min-height: 100vh; /* Asegura que el contenedor ocupe al menos el 100% de la altura de la ventana */
}

/* Estilos para el contenido del formulario */
.content {
    flex: 1; /* Hace que el contenido ocupe el espacio restante */
    margin-top: 60px; /* Margen superior para separar del header */
    margin-bottom: 40px; /* Margen inferior para separar del footer */
}

/* Estilos para el footer */
.footer {
    margin-top: auto; /* Asegura que el footer esté en la parte inferior */
}
</style>

<!-- Index mejorado con estilos y layout personalizado -->

<script setup>
import { ref, computed, onMounted, watch, nextTick } from "vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link } from "@inertiajs/vue3";
import axios from "axios";
import { router } from "@inertiajs/vue3";
import Swal from "sweetalert2";

defineProps({
    crearvideo: Boolean,
    editarvideo: Boolean,
    tutorialId: Number,
    permisos: {
        type: Array,
        default: () => [],
    },
});

// Función debounce
const debounce = (func, wait) => {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
};

// Funciones de protección (sin cambios)
const blockRightClick = (e) => {
    e.preventDefault();
    e.stopPropagation();
    return false;
};

const blockEvent = (e) => {
    if (e.type === "contextmenu" || e.type === "copy" || e.type === "cut") {
        e.preventDefault();
        e.stopPropagation();
        return false;
    }
};

const blockAllUnwantedActions = (e) => {
    const blockedKeys = ["F12", "Control", "Alt", "Shift", "Meta"];
    const blockedCombinations = {
        i: ["Control", "Shift"],
        c: ["Control", "Shift"],
        j: ["Control", "Shift"],
        u: ["Control"],
        s: ["Control"],
        f5: [],
    };

    const key = e.key?.toLowerCase();

    if (key && blockedCombinations[key]) {
        const allModifiersPressed = blockedCombinations[key].every(
            (mod) => e[`${mod.toLowerCase()}Key`]
        );
        if (allModifiersPressed) {
            e.preventDefault();
            e.stopPropagation();
            return false;
        }
    }

    if (e.key && blockedKeys.includes(e.key)) {
        e.preventDefault();
        e.stopPropagation();
        return false;
    }

    if (e.type === "contextmenu" || e.type === "selectstart") {
        e.preventDefault();
        e.stopPropagation();
        return false;
    }
};

const disableTextSelection = () => {
    document.body.style.userSelect = "none";
    document.body.style.webkitUserSelect = "none";
    document.body.style.mozUserSelect = "none";
    document.body.style.msUserSelect = "none";
};

const enableTextSelection = () => {
    document.body.style.userSelect = "";
    document.body.style.webkitUserSelect = "";
    document.body.style.mozUserSelect = "";
    document.body.style.msUserSelect = "";
};

const handleWindowBlur = () => {
    if (showModal.value) {
        window.focus();
    }
};

const secureIframe = (iframe) => {
    if (!iframe) return;

    const sandboxAttrs = [
        "allow-same-origin",
        "allow-scripts",
        "allow-presentation",
    ].join(" ");

    iframe.setAttribute("sandbox", sandboxAttrs);
    iframe.setAttribute("referrerpolicy", "no-referrer");
    iframe.setAttribute("allow", "autoplay; fullscreen");

    const disableIframeContextMenu = () => {
        try {
            const iframeDoc =
                iframe.contentDocument || iframe.contentWindow.document;
            iframeDoc.addEventListener("contextmenu", blockRightClick);
            iframeDoc.body.style.userSelect = "none";
        } catch (error) {
            console.error("No se pudo proteger el iframe:", error);
        }
    };

    iframe.addEventListener("load", () => {
        disableIframeContextMenu();
        const interval = setInterval(disableIframeContextMenu, 1000);
        watch(showModal, (val) => {
            if (!val) clearInterval(interval);
        });
    });
};

// Variables reactivas
const showEditModal = ref(false);
const currentTutorial = ref(null);
const editForm = ref({
    titulo: "",
    descripcion: "",
    tipo_material: "",
    formato: "",
    alcance: "",
    estado: "",
    url: "",
    observacion: "",
    subcategoria_id: "",
});

const selectedMaterialType = ref("todos"); // Por defecto "todos"
const setMaterialType = (type) => {
    selectedMaterialType.value = type;
    currentPage.value = 1; // Resetear a la primera página al cambiar pestaña
    fetchTutoriales();
};

const isLoading = ref(false);
const tutoriales = ref([]);
const searchQuery = ref("");
const sortKey = ref("titulo");
const sortOrder = ref("asc");
const showModal = ref(false);
const selectedContentInfo = ref(null);
const isContentLoading = ref(false);
const documentContent = ref(null);
const showVideoOverlay = ref(true);
const iframeHeight = ref("100%");

// Paginación
const currentPage = ref(1);
const perPage = ref(10);
const totalItems = ref(0);
const lastPage = ref(1);

const supportedPreviewTypes = ["pdf", "jpg", "jpeg", "png", "gif", "txt", "md"];

// Funciones CRUD
const fetchTutoriales = async () => {
    try {
        isLoading.value = true;
        const params = {
            search: searchQuery.value,
            tipo_material:
                selectedMaterialType.value === "todos"
                    ? null
                    : selectedMaterialType.value,
            sort_by: sortKey.value,
            sort_order: sortOrder.value,
            page: currentPage.value,
            per_page: perPage.value,
        };
        const response = await axios.get("/tutoriales", { params });
        tutoriales.value = response.data.data;
        currentPage.value = response.data.current_page;
        lastPage.value = response.data.last_page;
        perPage.value = response.data.per_page;
        totalItems.value = response.data.total;
    } catch (error) {
        console.error("Error al obtener los tutoriales:", error);
        Swal.fire({
            icon: "error",
            title: "Error",
            text: "No se pudieron cargar los tutoriales",
        });
    } finally {
        isLoading.value = false;
    }
};

const debouncedFetch = debounce(fetchTutoriales, 300);

const goToPage = (page) => {
    if (page >= 1 && page <= lastPage.value) {
        currentPage.value = page;
        fetchTutoriales();
    }
};

const goToCreateTutorial = async () => {
    try {
        await router.visit(route("CreateTutorial"), {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => {
                fetchTutoriales();
            },
        });
    } catch (error) {
        console.error("Error al navegar a crear tutorial:", error);
    }
};

const saveTutorial = async () => {
    try {
        if (!editForm.value.titulo.trim()) {
            Swal.fire({
                icon: "warning",
                title: "Validación",
                text: "El título es requerido",
            });
            return;
        }

        await axios.put(
            `/tutoriales/${currentTutorial.value.id}`,
            editForm.value
        );
        await fetchTutoriales();

        Swal.fire({
            icon: "success",
            title: "Éxito",
            text: "Tutorial actualizado correctamente",
            timer: 1500,
            showConfirmButton: false,
        });
        showEditModal.value = false;
    } catch (error) {
        console.error("Error al actualizar el tutorial:", error);
        Swal.fire({
            icon: "error",
            title: "Error",
            text:
                error.response?.data?.message ||
                "Error al actualizar el tutorial",
        });
    }
};

const deleteTutorial = async (id) => {
    const result = await Swal.fire({
        icon: "warning",
        title: "¿Estás seguro?",
        text: "¿Deseas eliminar este tutorial?",
        showCancelButton: true,
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar",
        confirmButtonColor: "#dc3545",
    });

    if (result.isConfirmed) {
        try {
            await axios.delete(`/tutoriales/${id}`);
            await fetchTutoriales();

            Swal.fire({
                icon: "success",
                title: "Eliminado",
                text: "Tutorial eliminado correctamente",
                timer: 1500,
                showConfirmButton: false,
            });
        } catch (error) {
            console.error("Error al eliminar el tutorial:", error);
            Swal.fire({
                icon: "error",
                title: "Error",
                text:
                    error.response?.data?.message ||
                    "Error al eliminar el tutorial",
            });
        }
    }
};

// Funciones de utilidad (sin cambios)
const extractYouTubeId = (url) => {
    const patterns = [
        /youtu\.be\/([^#\&\?]{11})/,
        /\?v=([^#\&\?]{11})/,
        /&v=([^#\&\?]{11})/,
        /embed\/([^#\&\?]{11})/,
        /\/v\/([^#\&\?]{11})/,
    ];

    for (let i = 0; i < patterns.length; i++) {
        const match = url.match(patterns[i]);
        if (match && match[1]) return match[1];
    }

    const lastSegment = url.split("/").pop();
    if (
        lastSegment &&
        lastSegment.length === 11 &&
        !lastSegment.includes("?")
    ) {
        return lastSegment;
    }
    return null;
};

const isGoogleDoc = (url) => {
    return url.includes("docs.google.com") || url.includes("drive.google.com");
};

const getGoogleDocEmbedUrl = (url) => {
    try {
        const urlObj = new URL(url);
        if (urlObj.hostname.includes("docs.google.com")) {
            const match = url.match(/\/d\/([^\/]+)/);
            if (match && match[1]) {
                return `https://docs.google.com/document/d/${match[1]}/preview`;
            }
            const pubMatch = url.match(/\/d\/e\/([^\/]+)/);
            if (pubMatch && pubMatch[1]) {
                return `https://docs.google.com/document/d/e/${pubMatch[1]}/pub?embedded=true`;
            }
        }
        return url;
    } catch {
        return url;
    }
};

const getContentInfo = (url) => {
    if (!url) return null;

    if (isGoogleDoc(url)) {
        return {
            type: "google-doc",
            embedUrl: getGoogleDocEmbedUrl(url),
            directUrl: url,
            canEmbed: true,
            platform: "Google Docs",
            icon: "file-text",
        };
    }

    try {
        const urlObj = new URL(url);
        const fileExtension = urlObj.pathname.split(".").pop().toLowerCase();
        let type = "external";
        let embedUrl = null;
        let canEmbed = false;
        let platform = "Documento";
        let icon = "file";

        const documentTypes = {
            pdf: { name: "PDF", icon: "file-pdf" },
            doc: { name: "Word", icon: "file-word" },
            docx: { name: "Word", icon: "file-word" },
            xls: { name: "Excel", icon: "file-excel" },
            xlsx: { name: "Excel", icon: "file-excel" },
            ppt: { name: "PowerPoint", icon: "file-powerpoint" },
            pptx: { name: "PowerPoint", icon: "file-powerpoint" },
            txt: { name: "Texto", icon: "file-alt" },
            md: { name: "Markdown", icon: "file-alt" },
            jpg: { name: "Imagen", icon: "file-image" },
            jpeg: { name: "Imagen", icon: "file-image" },
            png: { name: "Imagen", icon: "file-image" },
            gif: { name: "Imagen", icon: "file-image" },
            mp4: { name: "Video", icon: "video" },
        };

        if (documentTypes[fileExtension]) {
            type = "document";
            platform = documentTypes[fileExtension].name;
            icon = documentTypes[fileExtension].icon;
            if (supportedPreviewTypes.includes(fileExtension)) {
                canEmbed = true;
            }
        } else {
            if (
                urlObj.hostname.includes("youtube.com") ||
                urlObj.hostname.includes("youtu.be")
            ) {
                const videoId = extractYouTubeId(url);
                if (videoId) {
                    let embedUrl = `https://www.youtube-nocookie.com/embed/${videoId}?autoplay=1&rel=0&modestbranding=1&controls=0&fs=0&disablekb=1&iv_load_policy=3&cc_load_policy=0`;
                    const timeParam = urlObj.searchParams.get("t");
                    if (timeParam) {
                        let seconds = timeParam.includes("m")
                            ? parseInt(timeParam.split("m")[0]) * 60 +
                              parseInt(timeParam.split("m")[1])
                            : parseInt(timeParam.replace("s", ""));
                        embedUrl += `&start=${seconds}`;
                    }
                    return {
                        type: "video",
                        platform: "YouTube",
                        embedUrl,
                        directUrl: url,
                        canEmbed: true,
                        originalUrl: url,
                        icon: "video",
                        fileExtension: null,
                    };
                }
            } else if (urlObj.hostname.includes("vimeo.com")) {
                const videoId = urlObj.pathname.split("/").pop();
                return {
                    type: "video",
                    platform: "Vimeo",
                    embedUrl: `https://player.vimeo.com/video/${videoId}?autoplay=1&title=0&byline=0&portrait=0&badge=0&share=0`,
                    directUrl: url,
                    canEmbed: true,
                    originalUrl: url,
                    icon: "video",
                };
            } else if (urlObj.hostname.includes("dailymotion.com")) {
                const videoId = urlObj.pathname.includes("/video/")
                    ? urlObj.pathname.split("/video/")[1]
                    : urlObj.pathname.split("/")[1];
                return {
                    type: "video",
                    platform: "Dailymotion",
                    embedUrl: `https://www.dailymotion.com/embed/video/${videoId}?autoplay=1&controls=0&ui-highlight=0&sharing-enable=0&queue-enable=0`,
                    directUrl: url,
                    canEmbed: true,
                    originalUrl: url,
                    icon: "video",
                };
            } else if (urlObj.hostname.includes("loom.com")) {
                const videoId = urlObj.pathname.split("/").pop();
                return {
                    type: "video",
                    platform: "Loom",
                    embedUrl: `https://www.loom.com/embed/${videoId}?autoplay=1&hide_owner=true&hide_share=true&hide_title=true&hideEmbedTopBar=true`,
                    directUrl: url,
                    canEmbed: true,
                    originalUrl: url,
                    icon: "video",
                };
            }
        }

        return {
            type,
            platform,
            embedUrl: canEmbed ? embedUrl : null,
            directUrl: url,
            canEmbed,
            originalUrl: url,
            icon,
            fileExtension,
        };
    } catch {
        return {
            type: "external",
            platform: "Enlace externo",
            embedUrl: null,
            directUrl: url,
            canEmbed: false,
            originalUrl: url,
            icon: "link",
        };
    }
};

const loadDocumentContent = async (url) => {
    try {
        isContentLoading.value = true;
        const response = await fetch(url);

        if (response.ok) {
            const fileExtension = url.split(".").pop().toLowerCase();

            if (fileExtension === "pdf") {
                const blob = await response.blob();
                documentContent.value = {
                    type: "pdf",
                    url: URL.createObjectURL(blob) + "#toolbar=0&navpanes=0",
                    content: null,
                };
            } else if (["jpg", "jpeg", "png", "gif"].includes(fileExtension)) {
                documentContent.value = {
                    type: "image",
                    url: url,
                    content: null,
                };
            } else if (["txt", "md"].includes(fileExtension)) {
                const text = await response.text();
                documentContent.value = {
                    type: "text",
                    url: url,
                    content: text,
                };
            }
        } else {
            throw new Error("Error al cargar el documento");
        }
    } catch (error) {
        console.error("Error al cargar el documento:", error);
        documentContent.value = null;
        Swal.fire({
            icon: "error",
            title: "Error",
            text: "No se pudo cargar el documento",
        });
    } finally {
        isContentLoading.value = false;
    }
};

const handleVideoLoad = () => {
    isContentLoading.value = false;
    showVideoOverlay.value = false;
    const iframe = document.querySelector(".modal-content-container iframe");
    if (iframe) adjustIframeHeight(iframe);
};

const handleVideoError = () => {
    isContentLoading.value = false;
    showVideoOverlay.value = false;
    Swal.fire({
        icon: "error",
        title: "Error",
        text: "No se pudo cargar el video. Por favor, intente nuevamente o use el enlace directo.",
    });
    closeModal();
};

const setupModalProtection = () => {
    const modal = document.querySelector(".modal-content-container");
    if (!modal) return;

    modal.style.userSelect = "none";
    modal.style.webkitUserSelect = "none";
    modal.style.mozUserSelect = "none";
    modal.style.msUserSelect = "none";
    modal.style.webkitTouchCallout = "none";

    const events = [
        "contextmenu",
        "copy",
        "cut",
        "paste",
        "dragstart",
        "drop",
        "keydown",
        "selectstart",
        "wheel",
        "mousewheel",
        "mousedown",
        "mouseup",
        "keyup",
        "keypress",
    ];

    events.forEach((event) => {
        modal.addEventListener(event, blockAllUnwantedActions, true);
    });

    if (selectedContentInfo.value?.canEmbed) {
        const iframe = modal.querySelector("iframe");
        if (iframe) {
            iframe.setAttribute(
                "sandbox",
                "allow-same-origin allow-scripts allow-presentation"
            );
            iframe.addEventListener("contextmenu", blockRightClick);

            const tryBlockIframeEvents = () => {
                try {
                    iframe.contentWindow.document.addEventListener(
                        "contextmenu",
                        blockRightClick
                    );
                    iframe.contentWindow.document.body.style.userSelect =
                        "none";
                } catch {
                    setTimeout(tryBlockIframeEvents, 500);
                }
            };

            iframe.addEventListener("load", () => {
                tryBlockIframeEvents();
                handleVideoLoad();
            });
        }
    }

    window.addEventListener("blur", handleWindowBlur);
    disableTextSelection();
    document.body.classList.add("modal-open");
    document.documentElement.style.overflow = "hidden";
};

let observer = null;
const cleanupModalProtection = () => {
    const modal = document.querySelector(".modal-content-container");
    if (modal) {
        const events = [
            "contextmenu",
            "copy",
            "cut",
            "paste",
            "dragstart",
            "drop",
            "keydown",
            "selectstart",
            "wheel",
            "mousewheel",
            "mousedown",
            "mouseup",
            "keyup",
            "keypress",
        ];
        events.forEach((event) => {
            modal.removeEventListener(event, blockAllUnwantedActions, true);
        });
    }

    window.removeEventListener("blur", handleWindowBlur);
    enableTextSelection();
    document.body.classList.remove("modal-open");
    document.documentElement.style.overflow = "";
    showVideoOverlay.value = true;
    iframeHeight.value = "100%";
    if (observer) observer.disconnect();
    if (documentContent.value?.url?.startsWith("blob:")) {
        URL.revokeObjectURL(documentContent.value.url);
    }
    documentContent.value = null;
};

const setupMutationObserver = (iframe) => {
    observer = new MutationObserver(() => {
        adjustIframeHeight(iframe);
    });

    try {
        observer.observe(iframe.contentWindow.document.body, {
            childList: true,
            subtree: true,
            attributes: true,
            characterData: true,
        });

        watch(showModal, (val) => {
            if (!val) observer.disconnect();
        });
    } catch (error) {
        console.error("Error al configurar el observador de mutación:", error);
    }
};

const adjustIframeHeight = (iframe) => {
    try {
        const checkHeight = () => {
            const contentHeight = Math.max(
                iframe.contentWindow.document.body.scrollHeight,
                iframe.contentWindow.document.documentElement.scrollHeight
            );
            if (contentHeight > 0) {
                iframeHeight.value = `${contentHeight}px`;
            }
        };
        checkHeight();
        setupMutationObserver(iframe);
    } catch (error) {
        console.error("Error en el ajuste inicial:", error);
        iframeHeight.value = "100%";
    }
};

onMounted(() => {
    fetchTutoriales();
});

watch(showModal, (val) => {
    if (val) {
        setupModalProtection();
        nextTick(() => {
            const iframe = document.querySelector(
                ".modal-content-container iframe"
            );
            secureIframe(iframe);
        });
    } else {
        cleanupModalProtection();
    }
});

watch(searchQuery, () => {
    currentPage.value = 1; // Resetear a la primera página al buscar
    debouncedFetch();
});

const displayedTutoriales = computed(() => {
    return tutoriales.value || [];
});

const viewTutorial = async (id) => {
    const tutorial = tutoriales.value.find((t) => t.id === id);
    if (tutorial && tutorial.url) {
        selectedContentInfo.value = getContentInfo(tutorial.url);

        if (
            selectedContentInfo.value.type === "document" &&
            supportedPreviewTypes.includes(
                selectedContentInfo.value.fileExtension
            )
        ) {
            await loadDocumentContent(tutorial.url);
            showModal.value = true;
            isContentLoading.value = false;
            return;
        }

        if (selectedContentInfo.value.type === "google-doc") {
            showModal.value = true;
            isContentLoading.value = true;
            return;
        }

        if (!selectedContentInfo.value.canEmbed) {
            window.open(
                selectedContentInfo.value.directUrl,
                "_blank",
                "noopener,noreferrer"
            );
            return;
        }

        showModal.value = true;
        isContentLoading.value = true;
    }
};

const closeModal = () => {
    showModal.value = false;
    selectedContentInfo.value = null;
    isContentLoading.value = false;
    if (documentContent.value?.url?.startsWith("blob:")) {
        URL.revokeObjectURL(documentContent.value.url);
    }
    documentContent.value = null;
    iframeHeight.value = "100%";
};

const sortBy = (key) => {
    if (sortKey.value === key) {
        sortOrder.value = sortOrder.value === "asc" ? "desc" : "asc";
    } else {
        sortKey.value = key;
        sortOrder.value = "asc";
    }
    currentPage.value = 1; // Resetear a la primera página al ordenar
    fetchTutoriales();
};

const editTutorial = (id) => {
    const tutorial = tutoriales.value.find((t) => t.id === id);
    if (tutorial) {
        currentTutorial.value = tutorial;
        editForm.value = {
            titulo: tutorial.titulo,
            descripcion: tutorial.descripcion,
            tipo_material: tutorial.tipo_material,
            formato: tutorial.formato,
            alcance: tutorial.alcance,
            estado: tutorial.estado,
            url: tutorial.url,
            observacion: tutorial.observacion || "",
            subcategoria_id: tutorial.subcategoria_id,
        };
        showEditModal.value = true;
    }
};
</script>

<template>
    <AppLayout title="Gestión de Tutoriales">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Gestión de Tutoriales
            </h2>
        </template>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <div
                    class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4"
                >
                    <h3 class="text-lg font-semibold text-gray-700">
                        Lista de Tutoriales
                    </h3>
                    <div class="flex items-center gap-3 w-full sm:w-auto">
                        <div class="relative w-full sm:w-64">
                            <input
                                type="text"
                                v-model="searchQuery"
                                placeholder="Buscar en todos los tutoriales..."
                                class="w-full pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            />
                            <svg
                                class="absolute left-3 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400"
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                                />
                            </svg>
                        </div>
                        <button
                            @click="goToCreateTutorial"
                            class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition flex items-center gap-2"
                            :disabled="isLoading"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 4v16m8-8H4"
                                />
                            </svg>
                            <span>Agregar</span>
                        </button>
                    </div>
                </div>

                <div v-if="isLoading" class="text-center py-4">
                    <svg
                        class="animate-spin h-8 w-8 text-blue-500 mx-auto"
                        xmlns="http://www.w3.org/2000/svg"
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
                        ></circle>
                        <path
                            class="opacity-75"
                            fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                        ></path>
                    </svg>
                    <span class="text-gray-600">Cargando tutoriales...</span>
                </div>

                <div class="flex flex-wrap gap-2 mb-6" v-show="!isLoading">
                    <button
                        @click="setMaterialType('todos')"
                        :class="{
                            'bg-blue-500 text-white':
                                selectedMaterialType === 'todos',
                            'bg-gray-200 text-gray-700':
                                selectedMaterialType !== 'todos',
                        }"
                        class="px-4 py-2 rounded-lg transition"
                    >
                        Todos
                    </button>
                    <button
                        @click="setMaterialType('video')"
                        :class="{
                            'bg-blue-500 text-white':
                                selectedMaterialType === 'video',
                            'bg-gray-200 text-gray-700':
                                selectedMaterialType !== 'video',
                        }"
                        class="px-4 py-2 rounded-lg transition"
                    >
                        Videos
                    </button>
                    <button
                        @click="setMaterialType('manual')"
                        :class="{
                            'bg-blue-500 text-white':
                                selectedMaterialType === 'manual',
                            'bg-gray-200 text-gray-700':
                                selectedMaterialType !== 'manual',
                        }"
                        class="px-4 py-2 rounded-lg transition"
                    >
                        Manuales
                    </button>
                    <button
                        @click="setMaterialType('guia')"
                        :class="{
                            'bg-blue-500 text-white':
                                selectedMaterialType === 'guia',
                            'bg-gray-200 text-gray-700':
                                selectedMaterialType !== 'guia',
                        }"
                        class="px-4 py-2 rounded-lg transition"
                    >
                        Guías
                    </button>
                    <button
                        @click="setMaterialType('post')"
                        :class="{
                            'bg-blue-500 text-white':
                                selectedMaterialType === 'post',
                            'bg-gray-200 text-gray-700':
                                selectedMaterialType !== 'post',
                        }"
                        class="px-4 py-2 rounded-lg transition"
                    >
                        Posts
                    </button>
                    <button
                        @click="setMaterialType('triptico')"
                        :class="{
                            'bg-blue-500 text-white':
                                selectedMaterialType === 'triptico',
                            'bg-gray-200 text-gray-700':
                                selectedMaterialType !== 'triptico',
                        }"
                        class="px-4 py-2 rounded-lg transition"
                    >
                        Trípticos
                    </button>
                </div>

                <div
                    class="overflow-x-auto shadow-md rounded-lg"
                    v-show="!isLoading"
                >
                    <table class="min-w-full bg-white">
                        <thead>
                            <tr
                                class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal"
                            >
                                <th
                                    class="py-3 px-6 text-left cursor-pointer"
                                    @click="sortBy('titulo')"
                                >
                                    Título
                                    <span
                                        class="ml-1"
                                        v-if="sortKey === 'titulo'"
                                    >
                                        {{ sortOrder === "asc" ? "↑" : "↓" }}
                                    </span>
                                </th>
                                <th class="py-3 px-6 text-left">Plataforma</th>
                                <th class="py-3 px-6 text-left">Descripción</th>
                                <th class="py-3 px-6 text-left">Tipo</th>
                                <th class="py-3 px-6 text-left">Formato</th>
                                <th class="py-3 px-6 text-left">Alcance</th>
                                <th class="py-3 px-6 text-left">Estado</th>
                                <th class="py-3 px-6 text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 text-sm">
                            <tr
                                v-for="tutorial in displayedTutoriales"
                                :key="tutorial.id"
                                class="border-b border-gray-200 hover:bg-gray-100 transition"
                            >
                                <td class="py-3 px-6 text-left">
                                    <button
                                        @click="viewTutorial(tutorial.id)"
                                        class="text-blue-600 hover:underline truncate max-w-[200px] flex items-center gap-1"
                                    >
                                        <span
                                            v-if="
                                                getContentInfo(tutorial.url)
                                                    ?.icon === 'video'
                                            "
                                        >
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="h-4 w-4"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 012-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"
                                                />
                                            </svg>
                                        </span>
                                        <span
                                            v-else-if="
                                                getContentInfo(tutorial.url)
                                                    ?.icon === 'file-pdf'
                                            "
                                        >
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="h-4 w-4 text-red-500"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"
                                                />
                                            </svg>
                                        </span>
                                        <span
                                            v-else-if="
                                                getContentInfo(tutorial.url)
                                                    ?.icon === 'file-word'
                                            "
                                        >
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="h-4 w-4 text-blue-500"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                                />
                                            </svg>
                                        </span>
                                        <span
                                            v-else-if="
                                                getContentInfo(tutorial.url)
                                                    ?.icon === 'file-excel'
                                            "
                                        >
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="h-4 w-4 text-green-500"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                                />
                                            </svg>
                                        </span>
                                        <span
                                            v-else-if="
                                                getContentInfo(tutorial.url)
                                                    ?.icon === 'file-image'
                                            "
                                        >
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="h-4 w-4"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
                                                />
                                            </svg>
                                        </span>
                                        <span
                                            v-else-if="
                                                getContentInfo(tutorial.url)
                                                    ?.icon === 'file-text'
                                            "
                                        >
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="h-4 w-4"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                                />
                                            </svg>
                                        </span>
                                        <span v-else>
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="h-4 w-4"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                                />
                                            </svg>
                                        </span>
                                        {{ tutorial.titulo }}
                                    </button>
                                </td>
                                <td class="py-3 px-6 text-left">
                                    {{
                                        getContentInfo(tutorial.url)
                                            ?.platform || "Documento"
                                    }}
                                </td>
                                <td
                                    class="py-3 px-6 text-left truncate max-w-[200px]"
                                >
                                    {{ tutorial.descripcion }}
                                </td>
                                <td class="py-3 px-6 text-left">
                                    {{ tutorial.tipo_material }}
                                </td>
                                <td class="py-3 px-6 text-left">
                                    {{ tutorial.formato }}
                                </td>
                                <td class="py-3 px-6 text-left">
                                    {{ tutorial.alcance }}
                                </td>
                                <td class="py-3 px-6 text-left">
                                    <span
                                        :class="{
                                            'text-green-600 font-semibold':
                                                tutorial.estado === 'activo',
                                            'text-red-600 font-semibold':
                                                tutorial.estado !== 'activo',
                                        }"
                                    >
                                        {{ tutorial.estado }}
                                    </span>
                                </td>
                                <td class="py-3 px-6 text-center">
                                    <div class="flex justify-center gap-2">
                                        <button
                                            @click="viewTutorial(tutorial.id)"
                                            class="p-2 text-blue-600 hover:bg-blue-100 rounded-full transition"
                                            title="Ver"
                                            :disabled="isLoading"
                                        >
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="h-5 w-5"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                                                />
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                                                />
                                            </svg>
                                        </button>
                                        <button
                                            @click="editTutorial(tutorial.id)"
                                            class="p-2 text-green-600 hover:bg-green-100 rounded-full transition"
                                            title="Editar"
                                            :disabled="isLoading"
                                        >
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="h-5 w-5"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                                                />
                                            </svg>
                                        </button>
                                        <button
                                            @click="deleteTutorial(tutorial.id)"
                                            class="p-2 text-red-600 hover:bg-red-100 rounded-full transition"
                                            title="Eliminar"
                                            :disabled="isLoading"
                                        >
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="h-5 w-5"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5-4h4m-4 0a2 2 0 00-2 2h8a2 2 0 00-2-2m-4 0V3m0 4h4"
                                                />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr
                                v-if="
                                    displayedTutoriales.length === 0 &&
                                    !isLoading
                                "
                            >
                                <td
                                    colspan="8"
                                    class="py-4 text-center text-gray-500"
                                >
                                    No se encontraron tutoriales que coincidan
                                    con la búsqueda
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Paginación -->
                    <div
                        class="flex justify-between items-center mt-4"
                        v-if="!isLoading && totalItems > 0"
                    >
                        <div class="text-gray-600">
                            Mostrando {{ (currentPage - 1) * perPage + 1 }} -
                            {{ Math.min(currentPage * perPage, totalItems) }} de
                            {{ totalItems }}
                        </div>
                        <div class="flex gap-2">
                            <button
                                @click="goToPage(currentPage - 1)"
                                :disabled="currentPage === 1"
                                class="px-3 py-1 border rounded-lg text-gray-600 hover:bg-gray-100 disabled:opacity-50"
                            >
                                Anterior
                            </button>
                            <button
                                v-for="page in Math.min(5, lastPage)"
                                :key="page"
                                @click="goToPage(page)"
                                :class="{
                                    'px-3 py-1 border rounded-lg': true,
                                    'bg-blue-500 text-white':
                                        currentPage === page,
                                    'text-gray-600 hover:bg-gray-100':
                                        currentPage !== page,
                                }"
                            >
                                {{ page }}
                            </button>
                            <button
                                @click="goToPage(currentPage + 1)"
                                :disabled="currentPage === lastPage"
                                class="px-3 py-1 border rounded-lg text-gray-600 hover:bg-gray-100 disabled:opacity-50"
                            >
                                Siguiente
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal para visualizar contenido -->
        <div
            v-if="showModal"
            class="fixed inset-0 bg-gray-900 bg-opacity-90 flex items-center justify-center z-[1000]"
            @click.self="closeModal"
            @contextmenu.prevent="blockRightClick"
        >
            <div
                class="modal-content-container relative rounded-lg bg-white"
                :style="{
                    width: '80%',
                    height: '90%',
                    maxWidth: '90vw',
                    maxHeight: '90vh',
                    display: 'flex',
                    flexDirection: 'column',
                }"
                @contextmenu.prevent="blockRightClick"
            >
                <div
                    class="flex-1 overflow-auto p-4"
                    @contextmenu.prevent="blockRightClick"
                >
                    <div class="h-full w-full">
                        <div
                            v-if="selectedContentInfo?.type === 'video'"
                            class="relative h-full w-full"
                            @contextmenu.prevent="blockRightClick"
                        >
                            <div
                                v-if="showVideoOverlay"
                                class="absolute inset-0 bg-black bg-opacity-50 z-10 flex items-center justify-center"
                            >
                                <span class="text-white text-lg"
                                    >Cargando video...</span
                                >
                            </div>
                            <iframe
                                :src="selectedContentInfo.embedUrl"
                                class="w-full h-full"
                                frameborder="0"
                                allow="autoplay; encrypted-media"
                                allowfullscreen
                                @load="handleVideoLoad"
                                @error="handleVideoError"
                                referrerpolicy="strict-origin-when-cross-origin"
                                sandbox="allow-same-origin allow-scripts allow-presentation"
                                @contextmenu.prevent="blockRightClick"
                            ></iframe>
                        </div>

                        <iframe
                            v-if="selectedContentInfo?.type === 'google-doc'"
                            :src="selectedContentInfo.embedUrl"
                            class="w-full border-none"
                            :style="{ height: iframeHeight, minHeight: '100%' }"
                            frameborder="0"
                            @load="
                                (e) => {
                                    isContentLoading = false;
                                    adjustIframeHeight(e.target);
                                }
                            "
                            sandbox="allow-same-origin allow-scripts"
                        ></iframe>

                        <iframe
                            v-if="documentContent?.type === 'pdf'"
                            :src="documentContent.url"
                            class="w-full bg-white"
                            :style="{ height: iframeHeight, minHeight: '100%' }"
                            frameborder="0"
                            @load="
                                (e) => {
                                    isContentLoading = false;
                                    adjustIframeHeight(e.target);
                                }
                            "
                            sandbox="allow-same-origin"
                        ></iframe>

                        <div
                            v-if="documentContent?.type === 'image'"
                            class="w-full h-full bg-white flex items-center justify-center p-4"
                        >
                            <img
                                :src="documentContent.url"
                                class="max-w-full max-h-full object-contain"
                                @load="isContentLoading = false"
                                @contextmenu.prevent="blockRightClick"
                            />
                        </div>

                        <div
                            v-if="documentContent?.type === 'text'"
                            class="h-full w-full flex flex-col"
                        >
                            <div
                                class="bg-white py-2 px-4 border-b flex items-center gap-2"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5 text-gray-500"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                    />
                                </svg>
                                <span class="font-medium">
                                    {{
                                        selectedContentInfo?.originalUrl
                                            .split("/")
                                            .pop()
                                    }}
                                </span>
                            </div>
                            <div
                                class="flex-1 overflow-auto p-4 whitespace-pre-wrap font-mono bg-gray-50 document-content"
                            >
                                {{ documentContent.content }}
                            </div>
                        </div>

                        <div
                            v-if="
                                selectedContentInfo?.type === 'document' &&
                                !supportedPreviewTypes.includes(
                                    selectedContentInfo?.fileExtension
                                ) &&
                                !isGoogleDoc(selectedContentInfo?.originalUrl)
                            "
                            class="absolute inset-0 flex flex-col items-center justify-center bg-white p-4 text-center"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-16 w-16 text-gray-400 mb-4"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"
                                />
                            </svg>
                            <h3 class="text-xl font-medium text-gray-700 mb-2">
                                Vista previa no disponible
                            </h3>
                            <p class="text-gray-500 mb-4">
                                Este tipo de documento no puede mostrarse en el
                                visor.
                            </p>
                            <a
                                :href="selectedContentInfo?.directUrl"
                                target="_blank"
                                class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition"
                            >
                                Descargar documento
                            </a>
                        </div>

                        <div
                            v-if="isContentLoading"
                            class="absolute inset-0 flex items-center justify-center bg-white"
                        >
                            <svg
                                class="animate-spin h-12 w-12 text-blue-500"
                                xmlns="http://www.w3.org/2000/svg"
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
                                ></circle>
                                <path
                                    class="opacity-75"
                                    fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                ></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <button
                    @click="closeModal"
                    class="absolute top-4 right-4 z-60 bg-white bg-opacity-70 rounded-full p-2 shadow-md text-gray-700 hover:text-gray-900 allow-interaction"
                    @contextmenu.stop
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-8 w-8"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
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
        </div>

        <!-- Modal para editar tutorial -->
        <div
            v-if="showEditModal"
            class="fixed inset-0 bg-gray-900 bg-opacity-90 flex items-center justify-center z-[1000]"
        >
            <div
                class="bg-white rounded-lg p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto"
            >
                <h3 class="text-xl font-semibold mb-4">Editar Tutorial</h3>

                <form @submit.prevent="saveTutorial">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-gray-700 mb-2"
                                >Título</label
                            >
                            <input
                                v-model="editForm.titulo"
                                type="text"
                                class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                required
                                maxlength="100"
                            />
                        </div>
                        <div>
                            <label class="block text-gray-700 mb-2"
                                >Tipo de Material</label
                            >
                            <select
                                v-model="editForm.tipo_material"
                                class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                required
                            >
                                <option value="video">Video</option>
                                <option value="manual">Manual</option>
                                <option value="guia">Guía</option>
                                <option value="post">Post</option>
                                <option value="triptico">Tríptico</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-700 mb-2"
                                >Formato</label
                            >
                            <select
                                v-model="editForm.formato"
                                class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                required
                            >
                                <option value="pdf">PDF</option>
                                <option value="word">Word</option>
                                <option value="mp4">MP4</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-700 mb-2"
                                >Alcance</label
                            >
                            <select
                                v-model="editForm.alcance"
                                class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                required
                            >
                                <option value="soporte">Soporte</option>
                                <option value="admin">Admin</option>
                                <option value="clientefinal">
                                    Cliente Final
                                </option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-700 mb-2"
                                >Estado</label
                            >
                            <select
                                v-model="editForm.estado"
                                class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                required
                            >
                                <option value="activo">Activo</option>
                                <option value="inactivo">Inactivo</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-700 mb-2">URL</label>
                            <input
                                v-model="editForm.url"
                                type="url"
                                class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            />
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2"
                            >Descripción</label
                        >
                        <textarea
                            v-model="editForm.descripcion"
                            class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            rows="3"
                            required
                        ></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2"
                            >Observación</label
                        >
                        <textarea
                            v-model="editForm.observacion"
                            class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            rows="2"
                        ></textarea>
                    </div>

                    <div class="flex justify-end gap-3">
                        <button
                            type="button"
                            @click="showEditModal = false"
                            class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition"
                            :disabled="isLoading"
                        >
                            Cancelar
                        </button>
                        <button
                            type="submit"
                            class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition"
                            :disabled="isLoading"
                        >
                            Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.modal-content-container {
    transition: all 0.3s ease;
    -webkit-touch-callout: none !important;
    -webkit-user-drag: none !important;
    -khtml-user-drag: none !important;
    -moz-user-drag: none !important;
    -o-user-drag: none !important;
    user-select: none !important;
    -webkit-user-select: none !important;
    -moz-user-select: none !important;
    -ms-user-select: none !important;
    cursor: default !important;
    pointer-events: auto !important;
    animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: scale(0.95);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

.modal-content-container * {
    pointer-events: none !important;
    user-select: none !important;
    -webkit-user-select: none !important;
    -moz-user-select: none !important;
    -ms-user-select: none !important;
}

.modal-content-container iframe,
.modal-content-container button,
.modal-content-container .document-content,
.modal-content-container .allow-interaction {
    pointer-events: auto !important;
}

.modal-content-container iframe {
    user-select: none !important;
    -webkit-user-select: none !important;
    -moz-user-select: none !important;
    -ms-user-select: none !important;
}

.document-content {
    user-select: text !important;
    -webkit-user-select: text !important;
    -moz-user-select: text !important;
    -ms-user-select: text !important;
    cursor: text !important;
}

.modal-open {
    overflow: hidden !important;
    position: fixed !important;
    width: 100% !important;
    height: 100% !important;
    touch-action: none !important;
}

button:focus {
    outline: 2px solid #2563eb;
    outline-offset: 2px;
}

button:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.table-striped tbody tr:nth-child(odd) {
    background-color: #f8fafc;
}

.table-striped tbody tr:nth-child(even) {
    background-color: #ffffff;
}

table {
    border-collapse: collapse;
}

th,
td {
    border: 1px solid #e5e7eb;
}

th {
    font-weight: 600;
}

.select-none {
    user-select: none;
}

.whitespace-pre-wrap {
    white-space: pre-wrap;
    word-wrap: break-word;
}

.font-mono {
    font-family: monospace;
}

.bg-gray-50 {
    background-color: #f9fafb;
}

iframe {
    background: white;
    overflow: auto !important;
}

@keyframes spin {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}

.animate-spin {
    animation: spin 1s linear infinite;
}

.modal-content-container {
    overflow: hidden;
}

.modal-content-container > div:first-child {
    overflow: auto;
    height: 100%;
}
</style>
