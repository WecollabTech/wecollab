import axios from "axios";
window.axios = axios;

window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

// Configura Axios con el CSRF token
axios.defaults.baseURL = "/api"; // Base URL para las rutas API
axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

const token = document.head.querySelector('meta[name="csrf-token"]')?.content;
if (token) {
    axios.defaults.headers.common["X-CSRF-TOKEN"] = token;
}
