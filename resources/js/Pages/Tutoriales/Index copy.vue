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

// Funciones de protección
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

// Protección específica para YouTube
const blockYouTubeInspection = (e) => {
    e.preventDefault();
    e.stopPropagation();
    return false;
};

// Protección específica para Loom (bloqueo de clic derecho e inspección)
const blockLoomInspection = (e) => {
    if (e.type === "contextmenu") {
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

const showSuggestionsModal = ref(false); // Modal de sugerencias

const selectedMaterialType = ref("todos");
const setMaterialType = (type) => {
    selectedMaterialType.value = type;
    currentPage.value = 1;
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

const currentPage = ref(1);
const perPage = ref(10);
const totalItems = ref(0);
const lastPage = ref(1);

const supportedPreviewTypes = ["pdf", "jpg", "jpeg", "png", "gif", "txt", "md"];

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
                    let embedUrl = `https://www.youtube-nocookie.com/embed/${videoId}?autoplay=1&rel=0&modestbranding=1&controls=1&fs=0&disablekb=0&iv_load_policy=3&cc_load_policy=0`;
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
                    embedUrl: `https://www.dailymotion.com/embed/video/${videoId}?autoplay=1&controls=1&ui-highlight=0&sharing-enable=0&queue-enable=0`,
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

const handleVideoLoad = (iframe) => {
    isContentLoading.value = false;
    showVideoOverlay.value = false;
    if (iframe) {
        setupVideoEndDetection(iframe);
        adjustIframeHeight(iframe);
    }
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

// Detectar fin del video para todas las plataformas
const setupVideoEndDetection = (iframe) => {
    try {
        const videoElement =
            iframe.contentWindow.document.querySelector("video");
        if (videoElement) {
            videoElement.addEventListener("ended", () => {
                showSuggestionsModal.value = true;
            });
        } else {
            // Intentar detectar el fin del video para YouTube específicamente
            if (selectedContentInfo.value?.platform === "YouTube") {
                const player = iframe.contentWindow.YT?.Player;
                if (player && iframe.id) {
                    const ytPlayer = new player(iframe.id, {
                        events: {
                            onStateChange: (event) => {
                                if (event.data === 0) {
                                    // 0 = ended
                                    showSuggestionsModal.value = true;
                                }
                            },
                        },
                    });
                }
            } else {
                setTimeout(() => setupVideoEndDetection(iframe), 500);
            }
        }
    } catch (error) {
        console.error(
            "Error al configurar la detección de fin de video:",
            error
        );
        setTimeout(() => setupVideoEndDetection(iframe), 500);
    }
};

const setupModalProtection = () => {
    const videoContainer = document.querySelector(".video-container");
    const documentContainer = document.querySelector(".document-container");

    const blockEvents = [
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

    if (videoContainer && selectedContentInfo.value?.type === "video") {
        videoContainer.style.userSelect = "none";
        videoContainer.style.webkitUserSelect = "none";
        videoContainer.style.mozUserSelect = "none";
        videoContainer.style.msUserSelect = "none";
        videoContainer.style.webkitTouchCallout = "none";

        blockEvents.forEach((event) => {
            videoContainer.addEventListener(
                event,
                blockAllUnwantedActions,
                true
            );
        });

        let iframe;
        switch (selectedContentInfo.value?.platform) {
            case "YouTube":
                const youtubeContainer =
                    videoContainer.querySelector(".youtube-container");
                iframe = youtubeContainer.querySelector("iframe");

                if (youtubeContainer && iframe) {
                    const youtubeBlockEvents = [
                        "contextmenu",
                        "mousedown",
                        "mouseup",
                        "click",
                        "dblclick",
                        "dragstart",
                        "dragend",
                        "selectstart",
                        "keydown",
                        "keypress",
                        "keyup",
                        "wheel",
                        "mousewheel",
                        "touchstart",
                        "touchmove",
                        "touchend",
                    ];

                    youtubeBlockEvents.forEach((event) => {
                        youtubeContainer.addEventListener(
                            event,
                            blockYouTubeInspection,
                            { capture: true, passive: false }
                        );
                        iframe.addEventListener(event, blockYouTubeInspection, {
                            capture: true,
                            passive: false,
                        });
                    });

                    iframe.setAttribute(
                        "sandbox",
                        "allow-same-origin allow-scripts"
                    );
                    iframe.setAttribute("disablepictureinpicture", "true");
                    iframe.style.pointerEvents = "none";

                    const tryBlockIframeEvents = () => {
                        try {
                            const iframeDoc =
                                iframe.contentDocument ||
                                iframe.contentWindow.document;
                            youtubeBlockEvents.forEach((event) => {
                                iframeDoc.addEventListener(
                                    event,
                                    blockYouTubeInspection,
                                    { capture: true, passive: false }
                                );
                            });
                            iframeDoc.body.style.userSelect = "none";
                            iframeDoc.body.style.pointerEvents = "none";
                        } catch {
                            setTimeout(tryBlockIframeEvents, 500);
                        }
                    };

                    iframe.addEventListener("load", () => {
                        tryBlockIframeEvents();
                        handleVideoLoad(iframe);
                    });

                    const protectionInterval = setInterval(
                        tryBlockIframeEvents,
                        1000
                    );
                    watch(showModal, (val) => {
                        if (!val) clearInterval(protectionInterval);
                    });
                }
                break;

            case "Loom":
                const loomContainer =
                    videoContainer.querySelector(".loom-container");
                iframe = loomContainer.querySelector("iframe");

                if (loomContainer && iframe) {
                    // Bloquear clic derecho y acciones de inspección en el contenedor
                    const loomBlockEvents = [
                        "contextmenu",
                        "selectstart",
                        "dragstart",
                    ];

                    loomBlockEvents.forEach((event) => {
                        loomContainer.addEventListener(
                            event,
                            blockLoomInspection,
                            { capture: true, passive: false }
                        );
                    });

                    // Bloquear clic derecho dentro del iframe
                    const tryBlockIframeRightClick = () => {
                        try {
                            const iframeDoc =
                                iframe.contentDocument ||
                                iframe.contentWindow.document;
                            iframeDoc.addEventListener(
                                "contextmenu",
                                blockRightClick,
                                { capture: true, passive: false }
                            );
                        } catch {
                            setTimeout(tryBlockIframeRightClick, 500);
                        }
                    };

                    iframe.addEventListener("load", () => {
                        tryBlockIframeRightClick();
                        handleVideoLoad(iframe);
                    });

                    const protectionInterval = setInterval(
                        tryBlockIframeRightClick,
                        1000
                    );
                    watch(showModal, (val) => {
                        if (!val) clearInterval(protectionInterval);
                    });
                }
                break;

            case "Vimeo":
                iframe = videoContainer.querySelector(
                    ".vimeo-container iframe"
                );
                if (iframe) {
                    iframe.addEventListener("contextmenu", blockRightClick);
                    iframe.addEventListener("load", () =>
                        handleVideoLoad(iframe)
                    );
                }
                break;

            case "Dailymotion":
                iframe = videoContainer.querySelector(
                    ".dailymotion-container iframe"
                );
                if (iframe) {
                    iframe.addEventListener("contextmenu", blockRightClick);
                    iframe.addEventListener("load", () =>
                        handleVideoLoad(iframe)
                    );
                }
                break;
        }
    }

    if (documentContainer && selectedContentInfo.value?.type !== "video") {
        documentContainer.addEventListener("contextmenu", blockRightClick);
    }

    window.addEventListener("blur", handleWindowBlur);
    disableTextSelection();
    document.body.classList.add("modal-open");
    document.documentElement.style.overflow = "hidden";
};

let observer = null;
const cleanupModalProtection = () => {
    const videoContainer = document.querySelector(".video-container");
    const documentContainer = document.querySelector(".document-container");

    const blockEvents = [
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

    const youtubeBlockEvents = [
        "contextmenu",
        "mousedown",
        "mouseup",
        "click",
        "dblclick",
        "dragstart",
        "dragend",
        "selectstart",
        "keydown",
        "keypress",
        "keyup",
        "wheel",
        "mousewheel",
        "touchstart",
        "touchmove",
        "touchend",
    ];

    const loomBlockEvents = ["contextmenu", "selectstart", "dragstart"];

    if (videoContainer) {
        blockEvents.forEach((event) => {
            videoContainer.removeEventListener(
                event,
                blockAllUnwantedActions,
                true
            );
        });

        const youtubeContainer =
            videoContainer.querySelector(".youtube-container");
        if (youtubeContainer) {
            youtubeBlockEvents.forEach((event) => {
                youtubeContainer.removeEventListener(
                    event,
                    blockYouTubeInspection,
                    true
                );
            });
        }

        const loomContainer = videoContainer.querySelector(".loom-container");
        if (loomContainer) {
            loomBlockEvents.forEach((event) => {
                loomContainer.removeEventListener(
                    event,
                    blockLoomInspection,
                    true
                );
            });
        }
    }

    if (documentContainer) {
        documentContainer.removeEventListener("contextmenu", blockRightClick);
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
            const iframe =
                document.querySelector(".video-container iframe") ||
                document.querySelector(".document-container iframe");
            secureIframe(iframe);
        });
    } else {
        cleanupModalProtection();
    }
});

watch(searchQuery, () => {
    currentPage.value = 1;
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

const closeSuggestionsModal = () => {
    showSuggestionsModal.value = false;
};

const sortBy = (key) => {
    if (sortKey.value === key) {
        sortOrder.value = sortOrder.value === "asc" ? "desc" : "asc";
    } else {
        sortKey.value = key;
        sortOrder.value = "asc";
    }
    currentPage.value = 1;
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
            >
                <div
                    v-if="selectedContentInfo?.type === 'video'"
                    class="video-container flex-1 overflow-auto p-4"
                >
                    <div class="relative h-full w-full">
                        <div
                            v-if="showVideoOverlay"
                            class="absolute inset-0 bg-black bg-opacity-50 z-10 flex items-center justify-center"
                        >
                            <span class="text-white text-lg"
                                >Cargando video...</span
                            >
                        </div>

                        <div
                            v-if="selectedContentInfo?.platform === 'YouTube'"
                            class="youtube-container h-full"
                        >
                            <iframe
                                :id="'youtube-iframe-' + Date.now()"
                                :src="selectedContentInfo.embedUrl"
                                class="w-full h-full"
                                frameborder="0"
                                allow="autoplay; encrypted-media"
                                allowfullscreen
                                @load="handleVideoLoad($event.target)"
                                @error="handleVideoError"
                                referrerpolicy="strict-origin-when-cross-origin"
                            ></iframe>
                        </div>

                        <div
                            v-if="selectedContentInfo?.platform === 'Vimeo'"
                            class="vimeo-container h-full"
                        >
                            <iframe
                                :src="selectedContentInfo.embedUrl"
                                class="w-full h-full"
                                frameborder="0"
                                allow="autoplay; encrypted-media"
                                allowfullscreen
                                @load="handleVideoLoad($event.target)"
                                @error="handleVideoError"
                                referrerpolicy="strict-origin-when-cross-origin"
                                sandbox="allow-same-origin allow-scripts allow-presentation"
                            ></iframe>
                        </div>

                        <div
                            v-if="
                                selectedContentInfo?.platform === 'Dailymotion'
                            "
                            class="dailymotion-container h-full"
                        >
                            <iframe
                                :src="selectedContentInfo.embedUrl"
                                class="w-full h-full"
                                frameborder="0"
                                allow="autoplay; encrypted-media"
                                allowfullscreen
                                @load="handleVideoLoad($event.target)"
                                @error="handleVideoError"
                                referrerpolicy="strict-origin-when-cross-origin"
                                sandbox="allowGEM-same-origin allow-scripts allow-presentation"
                            ></iframe>
                        </div>

                        <div
                            v-if="selectedContentInfo?.platform === 'Loom'"
                            class="loom-container h-full"
                        >
                            <iframe
                                :src="selectedContentInfo.embedUrl"
                                class="w-full h-full"
                                frameborder="0"
                                allow="autoplay; encrypted-media"
                                allowfullscreen
                                @load="handleVideoLoad($event.target)"
                                @error="handleVideoError"
                                referrerpolicy="strict-origin-when-cross-origin"
                            ></iframe>
                        </div>
                    </div>
                </div>

                <div v-else class="document-container flex-1 overflow-auto p-4">
                    <div class="h-full w-full">
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

        <!-- Modal de Sugerencias -->
        <div
            v-if="showSuggestionsModal"
            class="fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center z-[1100]"
            @click.self="closeSuggestionsModal"
        >
            <div
                class="bg-white rounded-lg p-6 w-full max-w-md max-h-[80vh] overflow-y-auto"
            >
                <h3 class="text-xl font-semibold mb-4">Sugerencias</h3>
                <p class="text-gray-600 mb-6">
                    El video ha terminado. Aquí tienes algunas sugerencias de
                    contenido relacionado:
                </p>
                <div class="space-y-4">
                    <button
                        @click="viewTutorial(tutoriales[0]?.id)"
                        class="w-full text-left px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg transition"
                    >
                        {{ tutoriales[0]?.titulo || "Tutorial 1" }}
                    </button>
                    <button
                        @click="viewTutorial(tutoriales[1]?.id)"
                        class="w-full text-left px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg transition"
                    >
                        {{ tutoriales[1]?.titulo || "Tutorial 2" }}
                    </button>
                    <button
                        @click="viewTutorial(tutoriales[2]?.id)"
                        class="w-full text-left px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg transition"
                    >
                        {{ tutoriales[2]?.titulo || "Tutorial 3" }}
                    </button>
                </div>
                <div class="flex justify-end mt-6">
                    <button
                        @click="closeSuggestionsModal"
                        class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition"
                    >
                        Cerrar
                    </button>
                </div>
            </div>
        </div>

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

.video-container {
    height: 100%;
    width: 100%;
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
}

.video-container * {
    pointer-events: none !important;
}

.video-container iframe {
    pointer-events: auto !important;
}

.youtube-container {
    background-color: #ffebee;
    position: relative;
    -webkit-user-select: none !important;
    -moz-user-select: none !important;
    -ms-user-select: none !important;
    user-select: none !important;
    pointer-events: none !important;
}

.youtube-container iframe {
    pointer-events: none !important;
    position: relative;
    z-index: 1;
}

.vimeo-container {
    background-color: #e3f2fd;
}

.dailymotion-container {
    background-color: #e0f7fa;
}

.loom-container {
    background-color: #f3e5f5;
    position: relative;
    -webkit-user-select: none !important;
    -moz-user-select: none !important;
    -ms-user-select: none !important;
    user-select: none !important;
    -webkit-touch-callout: none !important;
}

.youtube-container iframe,
.vimeo-container iframe,
.dailymotion-container iframe,
.loom-container iframe {
    aspect-ratio: 16/9;
    max-height: 100%;
    max-width: 100%;
}

@media (max-width: 768px) {
    .youtube-container iframe,
    .vimeo-container iframe,
    .dailymotion-container iframe,
    .loom-container iframe {
        height: auto;
        width: 100%;
    }
}

.document-container {
    height: 100%;
    width: 100%;
    user-select: text !important;
    -webkit-user-select: text !important;
    -moz-user-select: text !important;
    -ms-user-select: text !important;
    cursor: default !important;
    pointer-events: auto !important;
}

.document-container iframe,
.document-container img,
.document-container .document-content {
    pointer-events: auto !important;
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
