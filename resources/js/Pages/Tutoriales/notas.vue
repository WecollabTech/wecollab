<script setup>
import { ref, computed, onMounted, watch } from "vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link } from "@inertiajs/vue3";
import axios from "axios";
import { router } from "@inertiajs/vue3";

// Definir las propiedades que pasas desde Laravel
defineProps({
    crearvideo: Boolean,
    editarvideo: Boolean,
    tutorialId: Number,
});

// Estado para el tipo de material seleccionado
const selectedMaterialType = ref("videos");

// Función para cambiar el tipo de material
const setMaterialType = (type) => {
    selectedMaterialType.value = type;
};

const goToCreateTutorial = () => {
    router.visit(route("CreateTutorial"), {
        preserveState: true,
        preserveScroll: true,
    });
};

// Datos y estados
const tutoriales = ref([]);
const searchQuery = ref("");
const sortKey = ref("titulo");
const sortOrder = ref("asc");
const showModal = ref(false);
const selectedContentInfo = ref(null);
const isContentLoading = ref(false);
const showOverlay = ref(true);
const documentContent = ref(null);

// Tipos de documentos soportados para vista previa
const supportedPreviewTypes = ["pdf", "jpg", "jpeg", "png", "gif", "txt", "md"];

// Función para detectar si es un documento de Google
const isGoogleDoc = (url) => {
    return url.includes("docs.google.com") || url.includes("drive.google.com");
};

// Función para obtener la URL de visualización de Google Docs
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

// Función para generar token seguro
const generateSecureToken = () => {
    const array = new Uint32Array(5);
    window.crypto.getRandomValues(array);
    return array.join("").slice(0, 32);
};

// Función para crear URL dinámica protegida (solo para videos)
const createProtectedUrl = (originalUrl) => {
    try {
        const url = new URL(originalUrl);
        const token = generateSecureToken();
        const timestamp = Date.now();

        url.searchParams.append("token", token);
        url.searchParams.append("t", timestamp);
        url.searchParams.append("ref", window.location.hostname);

        if (
            url.hostname.includes("youtube.com") ||
            url.hostname.includes("youtu.be")
        ) {
            url.searchParams.append("modestbranding", "1");
            url.searchParams.append("controls", "0");
            url.searchParams.append("rel", "0");
            url.searchParams.append("iv_load_policy", "3");
            url.searchParams.append("disablekb", "1");
            url.searchParams.append("fs", "0");
            url.searchParams.append("showinfo", "0");
            url.hostname = "www.youtube-nocookie.com";
        } else if (url.hostname.includes("loom.com")) {
            url.searchParams.append("hide_share", "true");
            url.searchParams.append("hideEmbedTopBar", "true");
            url.searchParams.append("hide_owner", "true");
            url.searchParams.append("hide_title", "true");
        } else if (url.hostname.includes("vimeo.com")) {
            url.searchParams.append("title", "0");
            url.searchParams.append("byline", "0");
            url.searchParams.append("portrait", "0");
        } else if (url.hostname.includes("dailymotion.com")) {
            url.searchParams.append("ui-logo", "0");
            url.searchParams.append("endscreen-enable", "0");
            url.searchParams.append("ui-highlight", "0");
        }

        return url.toString();
    } catch {
        return originalUrl;
    }
};

// Función para cargar el contenido del documento
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
                    url: URL.createObjectURL(blob),
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
            console.error("Error al cargar el documento:", response.statusText);
            documentContent.value = null;
        }
    } catch (error) {
        console.error("Error al cargar el documento:", error);
        documentContent.value = null;
    } finally {
        isContentLoading.value = false;
    }
};

// Función para detectar tipo de contenido y generar información para visualización
const getContentInfo = (url) => {
    if (!url) return null;

    // Verificar primero si es un documento de Google
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

        // Detectar tipo de documento
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
        };

        if (documentTypes[fileExtension]) {
            type = "document";
            platform = documentTypes[fileExtension].name;
            icon = documentTypes[fileExtension].icon;

            // Verificar si podemos mostrar una vista previa
            if (supportedPreviewTypes.includes(fileExtension)) {
                canEmbed = true;
            }
        }
        // Si no es documento, verifica si es video
        else {
            const protectedUrl = createProtectedUrl(url);
            const protectedUrlObj = new URL(protectedUrl);

            if (
                protectedUrlObj.hostname.includes("youtube") ||
                protectedUrlObj.hostname.includes("youtu.be")
            ) {
                platform = "YouTube";
                const videoId =
                    protectedUrlObj.searchParams.get("v") ||
                    protectedUrlObj.pathname.split("/").pop();
                embedUrl = `https://www.youtube-nocookie.com/embed/${videoId}?${protectedUrlObj.searchParams.toString()}&autoplay=1&rel=0&showinfo=0`;
                canEmbed = true;
                type = "video";
                icon = "video";
            } else if (protectedUrlObj.hostname.includes("loom.com")) {
                platform = "Loom";
                const videoId = protectedUrlObj.pathname.split("/").pop();
                embedUrl = `https://www.loom.com/embed/${videoId}?${protectedUrlObj.searchParams.toString()}&autoplay=1`;
                canEmbed = true;
                type = "video";
                icon = "video";
            } else if (protectedUrlObj.hostname.includes("vimeo.com")) {
                platform = "Vimeo";
                const videoId = protectedUrlObj.pathname.split("/").pop();
                embedUrl = `https://player.vimeo.com/video/${videoId}?${protectedUrlObj.searchParams.toString()}&autoplay=1&loop=0`;
                canEmbed = true;
                type = "video";
                icon = "video";
            } else if (protectedUrlObj.hostname.includes("dailymotion.com")) {
                platform = "Dailymotion";
                const videoId = protectedUrlObj.pathname.includes("/video/")
                    ? protectedUrlObj.pathname.split("/video/")[1]
                    : protectedUrlObj.pathname.split("/")[1];
                embedUrl = `https://www.dailymotion.com/embed/video/${videoId}?${protectedUrlObj.searchParams.toString()}&autoplay=1&controls=0`;
                canEmbed = true;
                type = "video";
                icon = "video";
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

// Bloquear acciones no deseadas de manera exhaustiva
const blockUnwantedActions = (e) => {
    // Bloquear menú contextual (click derecho)
    if (e.type === "contextmenu" || e.button === 2) {
        e.preventDefault();
        e.stopPropagation();
        return false;
    }

    // Bloquear teclas específicas
    const forbiddenKeys = [
        123, 116, 73, 74, 85, 83, 80, 27, 19, 44, 91, 93, 112, 113, 114, 115,
        117, 118, 119, 120, 121, 122,
    ];

    // Bloquear combinaciones de teclas
    const forbiddenCombinations = [
        e.ctrlKey && e.shiftKey && e.keyCode === 73, // Ctrl+Shift+I
        e.ctrlKey && e.shiftKey && e.keyCode === 74, // Ctrl+Shift+J
        e.ctrlKey && e.shiftKey && e.keyCode === 67, // Ctrl+Shift+C
        e.ctrlKey && e.keyCode === 85, // Ctrl+U
        e.ctrlKey && e.keyCode === 83, // Ctrl+S
        e.ctrlKey && e.keyCode === 80, // Ctrl+P
        e.ctrlKey && e.keyCode === 65, // Ctrl+A
        e.altKey && e.keyCode === 115, // Alt+F4
        e.altKey && e.keyCode === 37, // Alt+Left
        e.altKey && e.keyCode === 39, // Alt+Right
    ];

    if (
        forbiddenKeys.includes(e.keyCode) ||
        forbiddenCombinations.some((comb) => comb)
    ) {
        e.preventDefault();
        e.stopPropagation();
        return false;
    }

    // Bloquear arrastrar y soltar
    if (e.type === "dragstart" || e.type === "drop") {
        e.preventDefault();
        e.stopPropagation();
        return false;
    }
};

// Configurar protección para el modal de manera robusta
const setupModalProtection = () => {
    // Añadir event listeners para bloquear interacciones
    const handlers = [
        { event: "keydown", handler: blockUnwantedActions, options: true },
        { event: "contextmenu", handler: blockUnwantedActions, options: true },
        { event: "dragstart", handler: blockUnwantedActions, options: true },
        { event: "drop", handler: blockUnwantedActions, options: true },
        { event: "copy", handler: blockUnwantedActions, options: true },
        { event: "cut", handler: blockUnwantedActions, options: true },
        { event: "paste", handler: blockUnwantedActions, options: true },
        { event: "selectstart", handler: blockUnwantedActions, options: true },
        { event: "mousedown", handler: blockUnwantedActions, options: true },
        { event: "mouseup", handler: blockUnwantedActions, options: true },
    ];

    handlers.forEach(({ event, handler, options }) => {
        document.addEventListener(event, handler, options);
    });

    // Ocultar cursor y deshabilitar selección de texto
    const modal = document.querySelector(".content-modal-container");
    if (modal) {
        modal.style.cursor = "none";
        modal.style.userSelect = "none";
        modal.style.webkitUserSelect = "none";
        modal.style.mozUserSelect = "none";
        modal.style.msUserSelect = "none";

        modal.addEventListener(
            "mousemove",
            () => {
                modal.style.cursor = "none";
            },
            true
        );
    }

    // Deshabilitar selección de texto en todo el documento
    document.querySelectorAll("*").forEach((el) => {
        el.style.userSelect = "none";
        el.style.webkitUserSelect = "none";
        el.style.mozUserSelect = "none";
        el.style.msUserSelect = "none";
    });

    // Bloquear la navegación con el teclado
    window.addEventListener(
        "keydown",
        (e) => {
            if ([37, 38, 39, 40].includes(e.keyCode)) {
                // Flechas
                e.preventDefault();
            }
        },
        true
    );

    showOverlay.value = true;
    setTimeout(() => {
        showOverlay.value = false;
    }, 5000);

    // Bloquear la salida del modal
    document.body.style.overflow = "hidden";
};

// Limpiar protección al cerrar modal
const cleanupModalProtection = () => {
    // Remover todos los event listeners
    const events = [
        "keydown",
        "contextmenu",
        "dragstart",
        "drop",
        "copy",
        "cut",
        "paste",
        "selectstart",
        "mousedown",
        "mouseup",
    ];

    events.forEach((event) => {
        document.removeEventListener(event, blockUnwantedActions, true);
    });

    // Restaurar el comportamiento normal del cursor y selección
    document.querySelectorAll("*").forEach((el) => {
        el.style.userSelect = "";
        el.style.webkitUserSelect = "";
        el.style.mozUserSelect = "";
        el.style.msUserSelect = "";
    });

    showOverlay.value = false;
    document.body.style.overflow = "";

    // Liberar objeto URL cuando se cierra el modal
    if (documentContent.value?.url?.startsWith("blob:")) {
        URL.revokeObjectURL(documentContent.value.url);
    }
    documentContent.value = null;
};

// Obtener tutoriales desde la API
const fetchTutoriales = async () => {
    try {
        const response = await axios.get("/tutoriales");
        tutoriales.value = response.data.data;
    } catch (error) {
        console.error("Error al obtener los tutoriales:", error);
    }
};

// Cargar tutoriales al montar el componente
onMounted(() => {
    fetchTutoriales();
});

// Manejar apertura/cierre del modal
watch(showModal, (val) => {
    if (val) {
        setupModalProtection();
        document.body.classList.add("modal-open");
    } else {
        cleanupModalProtection();
        document.body.classList.remove("modal-open");
    }
});

// Filtrar y ordenar tutoriales
const filteredAndSortedTutoriales = computed(() => {
    let filtered = tutoriales.value;

    if (searchQuery.value) {
        filtered = filtered.filter((tutorial) =>
            Object.values(tutorial)
                .filter((value) => typeof value === "string")
                .some((value) =>
                    value
                        .toLowerCase()
                        .includes(searchQuery.value.toLowerCase())
                )
        );
    }

    return [...filtered].sort((a, b) => {
        const key = sortKey.value;
        const order = sortOrder.value === "asc" ? 1 : -1;
        return a[key] > b[key] ? order : -order;
    });
});

// Filtrar por tipo de material
const filteredByMaterialType = computed(() => {
    return filteredAndSortedTutoriales.value.filter((tutorial) => {
        if (selectedMaterialType.value === "videos") {
            return tutorial.tipo_material === "video";
        } else if (selectedMaterialType.value === "documentos") {
            const fileExtension = getContentInfo(tutorial.url)?.fileExtension;
            return (
                [
                    "pdf",
                    "doc",
                    "docx",
                    "xls",
                    "xlsx",
                    "ppt",
                    "pptx",
                    "txt",
                    "md",
                    "jpg",
                    "jpeg",
                    "png",
                    "gif",
                ].includes(fileExtension) || isGoogleDoc(tutorial.url)
            );
        } else if (selectedMaterialType.value === "guia") {
            return tutorial.tipo_material === "guia";
        } else if (selectedMaterialType.value === "triptico") {
            return tutorial.tipo_material === "triptico";
        }
        return true;
    });
});

// Abrir modal con el contenido
const viewTutorial = async (id) => {
    const tutorial = tutoriales.value.find((t) => t.id === id);
    if (tutorial && tutorial.url) {
        selectedContentInfo.value = getContentInfo(tutorial.url);

        // Si es un documento con vista previa soportada
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

        // Si es un documento de Google
        if (selectedContentInfo.value.type === "google-doc") {
            showModal.value = true;
            isContentLoading.value = true;
            return;
        }

        // Si no se puede incrustar, abrir en nueva pestaña
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

// Cerrar el modal
const closeModal = () => {
    showModal.value = false;
    selectedContentInfo.value = null;
    isContentLoading.value = false;

    // Liberar objeto URL cuando se cierra el modal
    if (
        documentContent.value &&
        documentContent.value.url &&
        documentContent.value.url.startsWith("blob:")
    ) {
        URL.revokeObjectURL(documentContent.value.url);
    }
    documentContent.value = null;
};

// Ordenar tabla
const sortBy = (key) => {
    if (sortKey.value === key) {
        sortOrder.value = sortOrder.value === "asc" ? "desc" : "asc";
    } else {
        sortKey.value = key;
        sortOrder.value = "asc";
    }
};

// Funciones CRUD
const editTutorial = (id) => {
    console.log(`Editar tutorial con ID: ${id}`);
};

const deleteTutorial = async (id) => {
    if (confirm("¿Estás seguro de que deseas eliminar este tutorial?")) {
        try {
            await axios.delete(`/tutoriales/${id}`);
            tutoriales.value = tutoriales.value.filter(
                (tutorial) => tutorial.id !== id
            );
        } catch (error) {
            console.error("Error al eliminar el tutorial:", error);
        }
    }
};
</script>

<template>
    <AppLayout title="Gestión de Tutoriales">
        <template #header>Gestión de Tutoriales</template>

        <div
            class="max-w-7xl mx-auto px-4 py-8 rounded-lg bg-ghostwhite mt-[70px]"
        >
            <!-- Encabezado -->
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
                            placeholder="Buscar tutoriales..."
                            class="w-full p-2 pl-10 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
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
                        class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition flex items-center gap-2"
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

            <!-- Botones de selección de tipo de material -->
            <div class="flex flex-wrap gap-2 mb-6">
                <button
                    @click="setMaterialType('videos')"
                    :class="{
                        'bg-blue-500 text-white':
                            selectedMaterialType === 'videos',
                        'bg-gray-200 text-gray-700':
                            selectedMaterialType !== 'videos',
                    }"
                    class="px-4 py-2 rounded-lg transition"
                >
                    Videos
                </button>
                <button
                    @click="setMaterialType('documentos')"
                    :class="{
                        'bg-blue-500 text-white':
                            selectedMaterialType === 'documentos',
                        'bg-gray-200 text-gray-700':
                            selectedMaterialType !== 'documentos',
                    }"
                    class="px-4 py-2 rounded-lg transition"
                >
                    Documentos
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

            <!-- Tabla -->
            <div class="overflow-x-auto shadow-md rounded-lg">
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
                                <span class="ml-1" v-if="sortKey === 'titulo'">
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
                            v-for="tutorial in filteredByMaterialType"
                            :key="tutorial.id"
                            class="border-b border-gray-200 hover:bg-gray-100 transition"
                        >
                            <td class="py-3 px-6 text-left">
                                <button
                                    @click="viewTutorial(tutorial.id)"
                                    class="text-blue-600 hover:underline truncate max-w-[200px] inline-block flex items-center gap-1"
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
                                                d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"
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
                                    getContentInfo(tutorial.url)?.platform ||
                                    "Documento"
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
                        <tr v-if="filteredByMaterialType.length === 0">
                            <td
                                colspan="8"
                                class="py-4 text-center text-gray-500"
                            >
                                No se encontraron tutoriales que coincidan con
                                la búsqueda
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal para reproducir el contenido con bloqueo completo -->
        <div
            v-if="showModal"
            class="fixed inset-0 bg-gray-900 bg-opacity-90 flex items-center justify-center z-[1000] select-none"
            @click.self="closeModal"
            @contextmenu.prevent="blockUnwantedActions"
        >
            <!-- Capa superpuesta de protección (semi-transparente) -->
            <div
                class="absolute inset-0 z-50 pointer-events-none"
                :class="{ 'bg-black bg-opacity-20': showOverlay }"
            ></div>

            <div
                class="relative rounded-lg overflow-hidden content-modal-container bg-white"
                :style="{
                    width: '80%',
                    height: '90%',
                    maxWidth: '90vw',
                    maxHeight: '90vh',
                }"
                @contextmenu.prevent="blockUnwantedActions"
            >
                <!-- Contenedor interno con protección -->
                <div class="absolute inset-0" style="pointer-events: none">
                    <!-- Contenedor del video protegido -->
                    <iframe
                        v-if="selectedContentInfo?.type === 'video'"
                        :src="selectedContentInfo.embedUrl"
                        class="w-full h-full"
                        frameborder="0"
                        allow="autoplay; encrypted-media"
                        allowfullscreen
                        @load="isContentLoading = false"
                        @error="closeModal"
                        sandbox="allow-same-origin allow-scripts"
                        referrerpolicy="no-referrer"
                        style="pointer-events: none"
                    ></iframe>

                    <!-- Contenedor para Google Docs -->
                    <iframe
                        v-if="selectedContentInfo?.type === 'google-doc'"
                        :src="selectedContentInfo.embedUrl"
                        class="w-full h-full border-none"
                        frameborder="0"
                        @load="isContentLoading = false"
                        style="pointer-events: none"
                    ></iframe>

                    <!-- Contenedor para PDF -->
                    <iframe
                        v-if="documentContent?.type === 'pdf'"
                        :src="documentContent.url"
                        class="w-full h-full bg-white"
                        frameborder="0"
                        @load="isContentLoading = false"
                        style="pointer-events: none"
                    ></iframe>

                    <!-- Contenedor para imágenes -->
                    <div
                        v-if="documentContent?.type === 'image'"
                        class="w-full h-full bg-white flex items-center justify-center p-4"
                    >
                        <img
                            :src="documentContent.url"
                            class="max-w-full max-h-full object-contain"
                            @load="isContentLoading = false"
                            style="pointer-events: none"
                        />
                    </div>

                    <!-- Contenedor para texto plano/markdown -->
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
                            <span class="font-medium">{{
                                selectedContentInfo?.originalUrl
                                    .split("/")
                                    .pop()
                            }}</span>
                        </div>
                        <div
                            class="flex-1 overflow-auto p-4 whitespace-pre-wrap font-mono bg-gray-50"
                            style="pointer-events: none"
                        >
                            {{ documentContent.content }}
                        </div>
                    </div>

                    <!-- Mensaje para documentos no soportados para vista previa -->
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
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
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
                            style="pointer-events: auto"
                        >
                            Descargar documento
                        </a>
                    </div>

                    <!-- Cargando -->
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

                <!-- Botón de cerrar con protección adicional -->
                <button
                    @click="closeModal"
                    class="absolute top-4 right-4 z-60 bg-white bg-opacity-70 rounded-full p-2 shadow-md text-gray-700 hover:text-gray-900"
                    @mousedown.stop="
                        (e) => {
                            e.stopPropagation();
                        }
                    "
                    @click.stop="closeModal"
                    style="pointer-events: auto"
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
    </AppLayout>
</template>

<style scoped>
/* Estilos para el bloqueo completo */
.content-modal-container {
    transition: all 0.3s ease;
    -webkit-touch-callout: none;
    -webkit-user-drag: none;
    -khtml-user-drag: none;
    -moz-user-drag: none;
    -o-user-drag: none;
    user-drag: none;
    cursor: none !important;
}

/* Bloquear selección de texto en todo el modal */
.content-modal-container * {
    -webkit-user-select: none !important;
    -moz-user-select: none !important;
    -ms-user-select: none !important;
    user-select: none !important;
}

/* Deshabilitar eventos de puntero en iframes */
.content-modal-container iframe {
    pointer-events: none;
}

/* Estilo para la capa de protección */
.protection-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 100;
    background-color: rgba(0, 0, 0, 0.1);
}

/* Estilos base */
.bg-ghostwhite {
    background-color: ghostwhite;
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

/* Estilos para el contenido de texto */
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

/* Estilos para iframes */
iframe {
    background: white;
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

.modal-open {
    overflow: hidden;
    position: fixed;
    width: 100%;
    height: 100%;
}
</style>
