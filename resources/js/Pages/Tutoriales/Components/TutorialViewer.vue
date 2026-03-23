<script setup>
import { ref, watch, nextTick, onUnmounted } from "vue";
import Swal from "sweetalert2";

const props = defineProps({
    show: Boolean,
    tutorial: Object,
});

const emit = defineEmits(["close", "video-ended"]);

// Variables
const isContentLoading = ref(false);
const showVideoOverlay = ref(true);
const iframeHeight = ref("100%");
const documentContent = ref(null);
const selectedContentInfo = ref(null);
const showSuggestionsModal = ref(false);
const observer = ref(null);

// Funciones de protección (COPIAR TODAS DEL CÓDIGO ORIGINAL)
const blockRightClick = (e) => {
    e.preventDefault();
    e.stopPropagation();
    return false;
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
            (mod) => e[`${mod.toLowerCase()}Key`],
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

const blockYouTubeInspection = (e) => {
    e.preventDefault();
    e.stopPropagation();
    return false;
};

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
    if (props.show) {
        window.focus();
    }
};

// Funciones de utilidad (COPIAR DEL ORIGINAL)
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
            if (
                ["pdf", "jpg", "jpeg", "png", "gif", "txt", "md"].includes(
                    fileExtension,
                )
            ) {
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
    emit("close");
};

const setupVideoEndDetection = (iframe) => {
    try {
        const videoElement =
            iframe.contentWindow.document.querySelector("video");
        if (videoElement) {
            videoElement.addEventListener("ended", () => {
                emit("video-ended");
            });
        } else {
            if (selectedContentInfo.value?.platform === "YouTube") {
                const player = iframe.contentWindow.YT?.Player;
                if (player && iframe.id) {
                    const ytPlayer = new player(iframe.id, {
                        events: {
                            onStateChange: (event) => {
                                if (event.data === 0) {
                                    emit("video-ended");
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
            error,
        );
        setTimeout(() => setupVideoEndDetection(iframe), 500);
    }
};

const adjustIframeHeight = (iframe) => {
    try {
        const checkHeight = () => {
            const contentHeight = Math.max(
                iframe.contentWindow.document.body.scrollHeight,
                iframe.contentWindow.document.documentElement.scrollHeight,
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

const setupMutationObserver = (iframe) => {
    observer.value = new MutationObserver(() => {
        adjustIframeHeight(iframe);
    });
    try {
        observer.value.observe(iframe.contentWindow.document.body, {
            childList: true,
            subtree: true,
            attributes: true,
            characterData: true,
        });
    } catch (error) {
        console.error("Error al configurar el observador de mutación:", error);
    }
};

const setupProtection = () => {
    disableTextSelection();
    document.body.classList.add("modal-open");
    window.addEventListener("blur", handleWindowBlur);
};

const cleanupProtection = () => {
    enableTextSelection();
    document.body.classList.remove("modal-open");
    window.removeEventListener("blur", handleWindowBlur);
    if (observer.value) observer.value.disconnect();
    if (documentContent.value?.url?.startsWith("blob:")) {
        URL.revokeObjectURL(documentContent.value.url);
    }
};

const closeModal = () => {
    emit("close");
};

const closeSuggestionsModal = () => {
    showSuggestionsModal.value = false;
};

// Watch
watch(
    () => props.show,
    (newVal) => {
        if (newVal && props.tutorial) {
            selectedContentInfo.value = getContentInfo(props.tutorial.url);
            setupProtection();

            if (selectedContentInfo.value?.type === "document") {
                loadDocumentContent(props.tutorial.url);
            } else if (selectedContentInfo.value?.type === "video") {
                isContentLoading.value = true;
            }
        } else {
            cleanupProtection();
            documentContent.value = null;
            selectedContentInfo.value = null;
            showVideoOverlay.value = true;
        }
    },
    { immediate: true },
);

onUnmounted(() => {
    cleanupProtection();
});
</script>

<template>
    <Teleport to="body">
        <div>
            <!-- Modal Principal de Visualización -->
            <div
                v-if="show"
                class="fixed inset-0 bg-black/95 backdrop-blur-xl flex items-center justify-center z-[1000]"
                @click.self="closeModal"
                @contextmenu.prevent="blockRightClick"
            >
                <div
                    class="modal-content-container relative rounded-2xl bg-gradient-to-br from-gray-900 to-slate-900 border border-white/20 shadow-2xl"
                    :style="{
                        width: '90%',
                        height: '90%',
                        maxWidth: '95vw',
                        maxHeight: '95vh',
                        display: 'flex',
                        flexDirection: 'column',
                    }"
                >
                    <!-- Contenido de Video -->
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
                                v-if="
                                    selectedContentInfo?.platform === 'YouTube'
                                "
                                class="youtube-container h-full"
                            >
                                <iframe
                                    :id="'youtube-iframe-' + Date.now()"
                                    :src="selectedContentInfo.embedUrl"
                                    class="w-full h-full rounded-xl"
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
                                    class="w-full h-full rounded-xl"
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
                                    selectedContentInfo?.platform ===
                                    'Dailymotion'
                                "
                                class="dailymotion-container h-full"
                            >
                                <iframe
                                    :src="selectedContentInfo.embedUrl"
                                    class="w-full h-full rounded-xl"
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
                                    class="w-full h-full rounded-xl"
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

                    <!-- Contenido de Documento -->
                    <div
                        v-else
                        class="document-container flex-1 overflow-auto p-4"
                    >
                        <div class="h-full w-full">
                            <iframe
                                v-if="
                                    selectedContentInfo?.type === 'google-doc'
                                "
                                :src="selectedContentInfo.embedUrl"
                                class="w-full border-none rounded-xl"
                                :style="{
                                    height: iframeHeight,
                                    minHeight: '100%',
                                }"
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
                                class="w-full bg-white rounded-xl"
                                :style="{
                                    height: iframeHeight,
                                    minHeight: '100%',
                                }"
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
                                class="w-full h-full bg-gray-900 flex items-center justify-center p-4"
                            >
                                <img
                                    :src="documentContent.url"
                                    class="max-w-full max-h-full object-contain shadow-2xl rounded-xl"
                                    @load="isContentLoading = false"
                                />
                            </div>
                            <div
                                v-if="documentContent?.type === 'text'"
                                class="h-full w-full flex flex-col"
                            >
                                <div
                                    class="bg-gray-800 py-3 px-4 border-b border-gray-700 flex items-center gap-2 rounded-t-xl"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-5 w-5 text-gray-400"
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
                                    <span class="font-medium text-gray-200">
                                        {{
                                            selectedContentInfo?.originalUrl
                                                .split("/")
                                                .pop()
                                        }}
                                    </span>
                                </div>
                                <div
                                    class="flex-1 overflow-auto p-6 whitespace-pre-wrap font-mono bg-gray-900 text-gray-300 document-content rounded-b-xl"
                                >
                                    {{ documentContent.content }}
                                </div>
                            </div>
                            <div
                                v-if="
                                    selectedContentInfo?.type === 'document' &&
                                    ![
                                        'pdf',
                                        'jpg',
                                        'jpeg',
                                        'png',
                                        'gif',
                                        'txt',
                                        'md',
                                    ].includes(
                                        selectedContentInfo?.fileExtension,
                                    ) &&
                                    !isGoogleDoc(
                                        selectedContentInfo?.originalUrl,
                                    )
                                "
                                class="absolute inset-0 flex flex-col items-center justify-center bg-gray-900 p-4 text-center rounded-2xl"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-16 w-16 text-gray-600 mb-4"
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
                                <h3
                                    class="text-xl font-medium text-gray-300 mb-2"
                                >
                                    Vista previa no disponible
                                </h3>
                                <p class="text-gray-500 mb-4">
                                    Este tipo de documento no puede mostrarse en
                                    el visor.
                                </p>
                                <a
                                    :href="selectedContentInfo?.directUrl"
                                    target="_blank"
                                    class="bg-gradient-to-r from-cyan-500 to-blue-500 text-white px-6 py-2 rounded-full hover:from-cyan-600 hover:to-blue-600 transition shadow-lg"
                                >
                                    Descargar documento
                                </a>
                            </div>
                            <div
                                v-if="isContentLoading"
                                class="absolute inset-0 flex items-center justify-center bg-gray-900 rounded-2xl"
                            >
                                <svg
                                    class="animate-spin h-12 w-12 text-cyan-400"
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

                    <!-- Botón Cerrar -->
                    <button
                        @click="closeModal"
                        class="absolute top-4 right-4 z-60 bg-white/10 backdrop-blur-md hover:bg-white/20 rounded-full p-2 shadow-md text-white hover:text-cyan-400 transition allow-interaction border border-white/20"
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
                class="fixed inset-0 bg-black/90 backdrop-blur-xl flex items-center justify-center z-[1100]"
                @click.self="closeSuggestionsModal"
            >
                <div
                    class="bg-gradient-to-br from-gray-900 to-slate-900 rounded-2xl p-6 w-full max-w-md max-h-[80vh] overflow-y-auto border border-white/20 shadow-2xl"
                >
                    <h3
                        class="text-2xl font-bold mb-4 text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-purple-400"
                    >
                        Sugerencias
                    </h3>
                    <p class="text-gray-400 mb-6">
                        El video ha terminado. Aquí tienes algunas sugerencias
                        de contenido relacionado:
                    </p>
                    <div class="space-y-3">
                        <button
                            @click="$emit('view-tutorial', 0)"
                            class="w-full text-left px-4 py-3 bg-white/10 hover:bg-white/20 rounded-xl transition text-gray-200 border border-white/10"
                        >
                            Tutorial 1
                        </button>
                        <button
                            @click="$emit('view-tutorial', 1)"
                            class="w-full text-left px-4 py-3 bg-white/10 hover:bg-white/20 rounded-xl transition text-gray-200 border border-white/10"
                        >
                            Tutorial 2
                        </button>
                        <button
                            @click="$emit('view-tutorial', 2)"
                            class="w-full text-left px-4 py-3 bg-white/10 hover:bg-white/20 rounded-xl transition text-gray-200 border border-white/10"
                        >
                            Tutorial 3
                        </button>
                    </div>
                    <div class="flex justify-end mt-6">
                        <button
                            @click="closeSuggestionsModal"
                            class="px-6 py-2 bg-gradient-to-r from-cyan-500 to-blue-500 text-white rounded-full hover:from-cyan-600 hover:to-blue-600 transition shadow-lg"
                        >
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </Teleport>
</template>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.backdrop-blur-xl {
    backdrop-filter: blur(16px);
}

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

.modal-open {
    overflow: hidden !important;
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
    background-color: #000;
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
    background-color: #000;
}

.dailymotion-container {
    background-color: #000;
}

.loom-container {
    background-color: #000;
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
</style>
