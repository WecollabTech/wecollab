<script setup>
import { ref, watch } from "vue";
import axios from "axios";
import Swal from "sweetalert2";

const props = defineProps({
    show: Boolean,
    tutorial: Object,
});

const emit = defineEmits(["close", "saved"]);

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

const isLoading = ref(false);

watch(
    () => props.tutorial,
    (tutorial) => {
        if (tutorial) {
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
        }
    },
    { immediate: true },
);

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

        isLoading.value = true;
        await axios.put(`/tutoriales/${props.tutorial.id}`, editForm.value);

        Swal.fire({
            icon: "success",
            title: "Éxito",
            text: "Tutorial actualizado correctamente",
            timer: 1500,
            showConfirmButton: false,
        });

        emit("saved");
    } catch (error) {
        console.error("Error al actualizar:", error);
        Swal.fire({
            icon: "error",
            title: "Error",
            text: error.response?.data?.message || "Error al actualizar",
        });
    } finally {
        isLoading.value = false;
    }
};

const closeModal = () => {
    emit("close");
};
</script>

<template>
    <Teleport to="body">
        <div
            v-if="show"
            class="fixed inset-0 bg-black/90 backdrop-blur-xl flex items-center justify-center z-[1000]"
            @click.self="closeModal"
        >
            <div
                class="bg-gradient-to-br from-gray-900 to-slate-900 rounded-2xl p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto border border-white/20 shadow-2xl"
            >
                <h3
                    class="text-2xl font-bold mb-6 text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-purple-400"
                >
                    Editar Tutorial
                </h3>

                <form @submit.prevent="saveTutorial">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-gray-400 mb-2 text-sm"
                                >Título</label
                            >
                            <input
                                v-model="editForm.titulo"
                                type="text"
                                class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-cyan-400 text-white"
                                required
                                maxlength="100"
                            />
                        </div>

                        <div>
                            <label class="block text-gray-400 mb-2 text-sm"
                                >Tipo de Material</label
                            >
                            <select
                                v-model="editForm.tipo_material"
                                class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-cyan-400 text-white"
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
                            <label class="block text-gray-400 mb-2 text-sm"
                                >Formato</label
                            >
                            <select
                                v-model="editForm.formato"
                                class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-cyan-400 text-white"
                                required
                            >
                                <option value="pdf">PDF</option>
                                <option value="word">Word</option>
                                <option value="mp4">MP4</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-gray-400 mb-2 text-sm"
                                >Alcance</label
                            >
                            <select
                                v-model="editForm.alcance"
                                class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-cyan-400 text-white"
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
                            <label class="block text-gray-400 mb-2 text-sm"
                                >Estado</label
                            >
                            <select
                                v-model="editForm.estado"
                                class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-cyan-400 text-white"
                                required
                            >
                                <option value="activo">Activo</option>
                                <option value="inactivo">Inactivo</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-gray-400 mb-2 text-sm"
                                >URL</label
                            >
                            <input
                                v-model="editForm.url"
                                type="url"
                                class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-cyan-400 text-white"
                            />
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-400 mb-2 text-sm"
                            >Descripción</label
                        >
                        <textarea
                            v-model="editForm.descripcion"
                            class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-cyan-400 text-white"
                            rows="3"
                            required
                        ></textarea>
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-400 mb-2 text-sm"
                            >Observación</label
                        >
                        <textarea
                            v-model="editForm.observacion"
                            class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-cyan-400 text-white"
                            rows="2"
                        ></textarea>
                    </div>

                    <div class="flex justify-end gap-3">
                        <button
                            type="button"
                            @click="closeModal"
                            class="px-6 py-2 bg-white/10 text-white rounded-full hover:bg-white/20 transition border border-white/20"
                            :disabled="isLoading"
                        >
                            Cancelar
                        </button>
                        <button
                            type="submit"
                            class="px-6 py-2 bg-gradient-to-r from-cyan-500 to-blue-500 text-white rounded-full hover:from-cyan-600 hover:to-blue-600 transition shadow-lg"
                            :disabled="isLoading"
                        >
                            Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </Teleport>
</template>

<style scoped>
.backdrop-blur-xl {
    backdrop-filter: blur(16px);
}
</style>
