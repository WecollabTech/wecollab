// resources/js/bootstrap.js
import axios from "axios";

// ✅ Hacer axios disponible globalmente
window.axios = axios;

// ✅ ENVIAR COOKIES DE SESIÓN (CRÍTICO para Jetstream + Inertia)
window.axios.defaults.withCredentials = true;

// ✅ Headers base para Laravel
window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";
window.axios.defaults.headers.common["Accept"] = "application/json";

// ✅ Base URL para endpoints API (tus rutas están en /api/*)
window.axios.defaults.baseURL = "/api";

// 🔐 CSRF Token desde meta tag (si existe en tu layout)
const csrfToken = document.head.querySelector(
    'meta[name="csrf-token"]',
)?.content;
if (csrfToken) {
    window.axios.defaults.headers.common["X-CSRF-TOKEN"] = csrfToken;
}

// 🔐 Interceptor: Agregar CSRF token desde cookie (Jetstream usa XSRF-TOKEN)
window.axios.interceptors.request.use((config) => {
    // Si no hay token en headers, intentar obtenerlo de la cookie
    if (!config.headers["X-CSRF-TOKEN"] && !config.headers["X-XSRF-TOKEN"]) {
        const xsrfToken = document.cookie
            .split("; ")
            .find((row) => row.startsWith("XSRF-TOKEN="))
            ?.split("=")[1];

        if (xsrfToken) {
            config.headers["X-XSRF-TOKEN"] = decodeURIComponent(xsrfToken);
        }
    }
    return config;
});

// 🔄 Interceptor: Manejar errores globalmente
window.axios.interceptors.response.use(
    (response) => response,
    (error) => {
        // 401: Session expirada → redirigir al login
        if (error.response?.status === 401 && window.Inertia) {
            window.Inertia.visit("/login");
        }
        // 403: Sin permisos → log opcional
        if (error.response?.status === 403) {
            console.warn("⚠️ Acceso denegado:", error.response.data?.message);
        }
        return Promise.reject(error);
    },
);

// ✅ Exportar para uso modular (opcional)
export default axios;
