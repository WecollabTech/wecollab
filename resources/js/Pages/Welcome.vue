<script setup>
import { ref, onMounted } from "vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { Swiper, SwiperSlide } from "swiper/vue";
import "swiper/css";
import "swiper/css/navigation";
import "swiper/css/pagination";
import { Navigation, Pagination, Autoplay } from "swiper/modules";
import Swal from "sweetalert2"; // Para alertas

import Header from "@/Vistas/Header.vue";
import Footer from "@/Vistas/Footer.vue";
import Prueba from "./Tutoriales/Prueba.vue";

// Simula si el usuario está autenticado (cámbialo dinámicamente según tu lógica real)
const isLoggedIn = ref(false);

// Función para manejar el clic en las tarjetas
const handleCardClick = (tutorial) => {
    if (isLoggedIn.value) {
        // Si el usuario está autenticado, redirige a la página del tutorial
        window.location.href = tutorial.link; // Cambia `link` por la URL correcta
    } else {
        // Si no está autenticado, muestra un mensaje de alerta
        Swal.fire({
            title: "Iniciar sesión requerido",
            text: "Debes iniciar sesión para ver este contenido.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Iniciar sesión",
            cancelButtonText: "Cancelar",
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "/login"; // Redirige a la página de inicio de sesión
            }
        });
    }
};

// Lista de tutoriales (agregué un campo `link`)
const tutoriales = [
    {
        titulo: "Tutorial de Vue.js",
        descripcion: "Aprende los fundamentos de Vue.js",
        imagen: "https://escuelafullstack.com/web/image/slide.channel/22/image_512",
        link: "https://vuejs.org", // Reemplázalo con la URL correcta
    },
    {
        titulo: "Tutorial de React",
        descripcion: "Construye aplicaciones con React",
        imagen: "https://assets-global.website-files.com/5b6901669b93d7837e36dc4c/615e177cd027d456769dd210_React-Native-1.png",
        link: "https://react.dev", // Reemplázalo con la URL correcta
    },
    {
        titulo: "Tutorial de CRM",
        descripcion: "Ventas de CRM",
        imagen: "https://th.bing.com/th/id/OIP.XCyLZC0FEtD2UE1Qr_UHhgHaE8?rs=1&pid=ImgDetMain",
        link: "https://react.dev", // Reemplázalo con la URL correcta
    },
];

// Datos para el swiper de imágenes
const imagenes = [
    "https://assets.turismocity.com/hotelImages/1678977396639_000.jpg",
    "https://assets.turismocity.com/hotelImages/1678977396639_000.jpg",
    "https://assets.turismocity.com/hotelImages/1678977396639_000.jpg",
];

// Tiempos de autoplay
const autoplayDelay = ref(30000); // 30 segundos para la primera diapositiva

// Control de reproducción del video
const videoRef = ref(null);

// Función para habilitar o desactivar el sonido
const toggleSound = () => {
    if (videoRef.value) {
        videoRef.value.muted = !videoRef.value.muted; // Alterna entre habilitar y desactivar el sonido
    }
};

// Función para desactivar el sonido
const muteVideo = () => {
    if (videoRef.value) {
        videoRef.value.muted = true; // Desactiva el sonido
    }
};

// Habilitar o desactivar el sonido al hacer clic en cualquier parte de la página
onMounted(() => {
    window.addEventListener("click", toggleSound);
});

// Función para manejar el cambio de diapositiva
const onSlideChange = (swiper) => {
    muteVideo(); // Desactiva el sonido al cambiar de diapositiva
};
</script>

<template>
    <Head title="Home" />
    <div>
        <Header
            :isLoginPage="true"
            :canLogin="true"
            :canRegister="true"
            :showHeaderBottom="true"
        />

        <main class="relative w-full h-[50vh] sm:h-[60vh] md:h-[70vh]">
            <Swiper
                :modules="[Navigation, Pagination, Autoplay]"
                :spaceBetween="0"
                :slidesPerView="1"
                :navigation="true"
                :pagination="{ clickable: true }"
                :autoplay="{
                    delay: autoplayDelay,
                    disableOnInteraction: false,
                }"
                @slideChange="onSlideChange"
                class="w-full h-full"
            >
                <!-- Primera diapositiva: Video de bienvenida -->
                <SwiperSlide>
                    <div
                        class="relative w-full h-[50vh] sm:h-[60vh] md:h-[70vh]"
                    >
                        <div class="video-container">
                            <video
                                ref="videoRef"
                                autoplay
                                muted
                                loop
                                playsinline
                                class="video"
                            >
                                <!-- Ruta al video en la carpeta public/videos -->
                                <source
                                    src="/video/Bienvenidos.mp4"
                                    type="video/mp4"
                                />
                                Tu navegador no soporta la reproducción de
                                video.
                            </video>
                        </div>
                        <div class="absolute inset-0 bg-black/50"></div>
                        <div
                            class="relative z-10 text-center text-white max-w-2xl"
                        >
                            <h1 class="text-2xl sm:text-4xl font-extrabold">
                                BIENVENIDOS A WE COLLAB
                            </h1>
                            <p class="mt-4 text-sm sm:text-lg">
                                Descubre los mejores tutoriales en línea
                            </p>
                        </div>
                    </div>
                </SwiperSlide>

                <!-- Resto de las diapositivas: Imágenes -->
                <SwiperSlide v-for="(imagen, index) in imagenes" :key="index">
                    <div
                        class="relative w-full h-[50vh] sm:h-[60vh] md:h-[70vh] bg-cover bg-center flex items-center justify-center"
                        :style="{ backgroundImage: `url(${imagen})` }"
                    >
                        <div class="absolute inset-0 bg-black/50"></div>
                        <div
                            class="relative z-10 text-center text-white max-w-2xl"
                        >
                            <h1 class="text-2xl sm:text-4xl font-extrabold">
                                BIENVENIDOS A WE COLLAB
                            </h1>
                            <p class="mt-4 text-sm sm:text-lg">
                                Descubre los mejores tutoriales en línea
                            </p>
                        </div>
                    </div>
                </SwiperSlide>
            </Swiper>
        </main>

        <section class="mt-12 px-4 sm:px-6 bg-green-100 py-12">
            <h2
                class="text-center text-2xl sm:text-3xl font-bold mb-8 text-gray-900"
            >
                Videos Destacados
            </h2>
            <Swiper
                :modules="[Navigation, Pagination, Autoplay]"
                :spaceBetween="20"
                :slidesPerView="1"
                :breakpoints="{
                    640: { slidesPerView: 2 },
                    1024: { slidesPerView: 3 },
                }"
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
                        class="bg-white rounded-lg shadow-md transform hover:scale-105 transition-all cursor-pointer"
                        @click="handleCardClick(tutorial)"
                    >
                        <img
                            :src="tutorial.imagen"
                            alt="Tutorial"
                            class="w-full h-40 sm:h-48 object-cover"
                        />
                        <div class="p-5">
                            <h3
                                class="text-lg sm:text-xl font-semibold text-gray-800"
                            >
                                {{ tutorial.titulo }}
                            </h3>
                            <p class="mt-2 text-gray-600 text-sm sm:text-base">
                                {{ tutorial.descripcion }}
                            </p>
                        </div>
                    </div>
                </SwiperSlide>
            </Swiper>
        </section>
        <div
            class="movil"
            style="
                background-image: linear-gradient(bottom, #ffffcc, #ffff99);
                background-size: 100% 300px;
                padding: 20px;
                border-radius: 10px;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            "
        >
            <div style="float: right">
                <Prueba />
            </div>
        </div>

        <Footer style="margin-top: 20px" />
    </div>
</template>

<style scoped>
.movil {
    margin-top: 30px;
    align-items: center;
}

.contenedorMovil {
    margin-top: 20px;
    margin-left: 10px;
}

.video-container {
    position: relative;
    width: 100%;
    height: 100%;
    overflow: hidden;
}

.video {
    width: 100%;
    height: 100%;
    object-fit: cover; /* Ajusta el video al contenedor sin distorsionar */
    aspect-ratio: 16 / 9; /* Mantiene la relación de aspecto 16:9 */
}
</style>
