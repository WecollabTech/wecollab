<script setup>
import { Head } from "@inertiajs/vue3";
import AppLayoutPublico from "@/Layouts/AppLayoutPublico.vue";
import { onMounted, onUnmounted, ref } from "vue";

// Components
import PopupModal from "@/Components/Welcome/PopupModal.vue";
import HeroCarousel from "@/Components/Welcome/HeroCarousel.vue";
import CoursesCarousel from "@/Components/Welcome/CoursesCarousel.vue";
import BenefitsGrid from "@/Components/Welcome/BenefitsGrid.vue";
import ReasonsBanner from "@/Components/Welcome/ReasonsBanner.vue";
import InfoCardsGrid from "@/Components/Welcome/InfoCardsGrid.vue";
import SobreNosotros from "@/Components/Welcome/SobreNosotros.vue";
import CapacitacionesDestacadas from "@/Components/Welcome/CapacitacionesDestacadas.vue";

defineProps({
    canLogin: { type: Boolean },
    canRegister: { type: Boolean },
});

// Data - Slides del Hero (imágenes locales)
const slides = [
    {
        title: "Marketing Digital",
        subtitle: "Estrategias que venden",
        image: "/img/image1.png",
    },
    {
        title: "Ventas Profesionales",
        subtitle: "Cierra más tratos",
        image: "/img/image2.png",
    },
    {
        title: "Liderazgo",
        subtitle: "Gestión de equipos",
        image: "/img/image3.png",
    },
    {
        title: "Tecnología",
        subtitle: "Innovación constante",
        image: "/img/image2.png",
    },
    {
        title: "Emprendimiento",
        subtitle: "Crea tu futuro",
        image: "/img/image1.png",
    },
];

// Cursos populares
const popularCourses = [
    {
        title: "Marketing Digital Avanzado",
        category: "Marketing",
        students: "2.4K estudiantes",
        rating: 4.9,
        image: "https://images.unsplash.com/photo-1432888498266-38ffec3eaf0a?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80",
    },
    {
        title: "Ventas B2B Estratégicas",
        category: "Ventas",
        students: "1.8K estudiantes",
        rating: 4.8,
        image: "https://images.unsplash.com/photo-1552664730-d307ca884978?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80",
    },
    {
        title: "Liderazgo Transformacional",
        category: "Liderazgo",
        students: "3.1K estudiantes",
        rating: 5.0,
        image: "https://images.unsplash.com/photo-1522071820081-009f0129c71c?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80",
    },
    {
        title: "Programación Web Full Stack",
        category: "Tecnología",
        students: "4.2K estudiantes",
        rating: 4.9,
        image: "https://images.unsplash.com/photo-1498050108023-c5249f4df085?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80",
    },
    {
        title: "Gestión de Proyectos Ágiles",
        category: "Gestión",
        students: "2.8K estudiantes",
        rating: 4.7,
        image: "https://images.unsplash.com/photo-1552664730-d307ca884978?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80",
    },
];

// Beneficios

const benefits = [
    {
        icon: "fa-book",
        title: "Acceso a Manuales",
        description:
            "Consulta guías y manuales completos de cada tema para aprender a tu ritmo",
        color: "from-teal-600 to-teal-800",
    },
    {
        icon: "fa-book",
        title: "Acceso a Manuales",
        description:
            "Consulta guías y manuales completos de cada tema para aprender a tu ritmo",
        color: "from-teal-600 to-teal-800",
    },
    {
        icon: "fa-book",
        title: "Acceso a Manuales",
        description:
            "Consulta guías y manuales completos de cada tema para aprender a tu ritmo",
        color: "from-teal-600 to-teal-800",
    },

    {
        icon: "fa-file-lines",
        title: "Guías Prácticas",
        description:
            "Accede a documentos y tutoriales paso a paso para implementar lo aprendido",
        color: "from-orange-500 to-red-500",
    },
    {
        icon: "fa-rotate",
        title: "Actualizaciones Constantes",
        description:
            "Mantente al día con nuevas capacitaciones y recursos actualizados",
        color: "from-green-500 to-emerald-600",
    },
];
const carouselRef = ref(null);

onMounted(() => {
    setTimeout(() => carouselRef.value?.startAutoplay?.(), 1000);
});

onUnmounted(() => {
    document.body.style.overflow = "";
});
</script>

<template>
    <AppLayoutPublico :can-login="canLogin" :can-register="canRegister">
        <Head title="Inicio" />

        <PopupModal :can-register="canRegister" @close="() => {}" />
        <HeroCarousel ref="carouselRef" :slides="slides" />
        <ReasonsBanner />
        <CoursesCarousel :courses="popularCourses" />
        <CapacitacionesDestacadas />
        <BenefitsGrid :benefits="benefits" />

        <!-- <InfoCardsGrid /> -->
        <SobreNosotros />
    </AppLayoutPublico>
</template>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;

    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
