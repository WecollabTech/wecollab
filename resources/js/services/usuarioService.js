import axios from "axios";

const api = axios.create({
    baseURL: "/api",
    headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
    },
});

export const usuarioService = {
    // CRUD principal
    async getUsuarios(params = {}) {
        try {
            const response = await api.get("/usuarios", { params });
            if (response.data.success && response.data.data) {
                return {
                    data: response.data.data.data || [],
                    current_page: response.data.data.current_page || 1,
                    last_page: response.data.data.last_page || 1,
                    per_page: response.data.data.per_page || 10,
                    total: response.data.data.total || 0,
                    from: response.data.data.from || 0,
                    to: response.data.data.to || 0,
                    next_page_url: response.data.data.next_page_url,
                    prev_page_url: response.data.data.prev_page_url,
                    links: response.data.data.links || [],
                };
            }
            return {
                data: [],
                current_page: 1,
                last_page: 1,
                total: 0,
                from: 0,
                to: 0,
                links: [],
            };
        } catch (error) {
            console.error("Error en getUsuarios:", error);
            throw error;
        }
    },

    async getUsuario(id) {
        const response = await api.get(`/usuarios/${id}`);
        return response.data;
    },

    async createUsuario(data) {
        const response = await api.post("/usuarios", data);
        return response.data;
    },

    async updateUsuario(id, data) {
        const response = await api.put(`/usuarios/${id}`, data);
        return response.data;
    },

    async deleteUsuario(id) {
        const response = await api.delete(`/usuarios/${id}`);
        return response.data;
    },

    async toggleStatus(id) {
        const response = await api.patch(`/usuarios/${id}/toggle-status`);
        return response.data;
    },

    // Datos auxiliares
    async getRoles() {
        const response = await api.get("/usuarios/roles/list");
        return response.data;
    },

    async getPaises() {
        const response = await api.get("/usuarios/paises/list");
        return response.data;
    },
};
