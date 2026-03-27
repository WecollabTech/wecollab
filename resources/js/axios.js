// resources/js/bootstrap.js
import axios from "axios";

// ✅ Configurar axios globalmente
axios.defaults.withCredentials = true;
axios.defaults.withXSRFToken = true;
axios.defaults.baseURL = "http://127.0.0.1:8000";
axios.defaults.headers.common["Accept"] = "application/json";
axios.defaults.headers.common["Content-Type"] = "application/json";
axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

// Exportar para usar en toda la app
window.axios = axios;

export default axios;
