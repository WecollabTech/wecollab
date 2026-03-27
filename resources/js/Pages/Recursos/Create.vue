<script setup>
import { ref } from "vue";
import { useForm } from "@inertiajs/inertia-vue3";

// Props recibidas desde la ruta
const props = defineProps({
    tipo: String, // Tipo preseleccionado opcional
    tiposValidos: Array, // Lista de tipos si se va a seleccionar
    subcategorias: Array, // Lista de subcategorías desde backend
});

const tipoSeleccionado = ref(props.tipo || "");

// Formulario
const form = useForm({
    titulo: "",
    descripcion: "",
    tipo_material: tipoSeleccionado.value,
    formato: "",
    alcance: "",
    estado: "",
    url: "",
    observacion: "",
    subcategoria_id: null,
    archivo: null, // para adjuntar archivos si aplica
});

// Manejar cambio de archivo
function handleFileChange(event) {
    form.archivo = event.target.files[0];
}

// Cambiar tipo material
function cambiarTipo() {
    form.tipo_material = tipoSeleccionado.value;
}

// Enviar formulario
function submit() {
    form.post(route("recursos.store"), { onSuccess: () => form.reset() });
}
</script>

<template>
    <div class="max-w-4xl mx-auto p-6 space-y-6">
        <h1 class="text-2xl font-bold mb-4">Registrar Recurso</h1>

        <!-- Selector de tipo si no viene preseleccionado -->
        <div v-if="!tipo">
            <label class="block font-semibold mb-1">Tipo de Material</label>
            <select
                v-model="tipoSeleccionado"
                @change="cambiarTipo"
                class="w-full rounded-xl border p-3 mb-4"
            >
                <option value="">Seleccione un tipo</option>
                <option v-for="t in tiposValidos" :key="t" :value="t">
                    {{ t }}
                </option>
            </select>
        </div>

        <form @submit.prevent="submit" class="space-y-4">
            <input
                v-model="form.titulo"
                type="text"
                placeholder="Título"
                class="w-full rounded-xl border p-3"
                required
            />

            <textarea
                v-model="form.descripcion"
                placeholder="Descripción"
                class="w-full rounded-xl border p-3"
            ></textarea>

            <select
                v-model="form.tipo_material"
                class="w-full rounded-xl border p-3"
                required
            >
                <option value="">Selecciona Tipo de Material</option>
                <option v-for="t in tiposValidos" :key="t" :value="t">
                    {{ t }}
                </option>
            </select>

            <input
                v-model="form.formato"
                type="text"
                placeholder="Formato"
                class="w-full rounded-xl border p-3"
            />

            <input
                v-model="form.alcance"
                type="text"
                placeholder="Alcance"
                class="w-full rounded-xl border p-3"
            />

            <select v-model="form.estado" class="w-full rounded-xl border p-3">
                <option value="">Selecciona Estado</option>
                <option value="activo">Activo</option>
                <option value="inactivo">Inactivo</option>
            </select>

            <input
                v-model="form.url"
                type="url"
                placeholder="URL"
                class="w-full rounded-xl border p-3"
            />

            <textarea
                v-model="form.observacion"
                placeholder="Observación"
                class="w-full rounded-xl border p-3"
            ></textarea>

            <select
                v-model="form.subcategoria_id"
                class="w-full rounded-xl border p-3"
            >
                <option value="">Selecciona Subcategoría</option>
                <option v-for="s in subcategorias" :key="s.id" :value="s.id">
                    {{ s.nombre }}
                </option>
            </select>

            <input
                type="file"
                @change="handleFileChange"
                class="w-full rounded-xl border p-3"
            />

            <button
                type="submit"
                class="bg-indigo-600 text-white px-6 py-2 rounded-xl hover:bg-indigo-700 transition"
            >
                Guardar Recurso
            </button>
        </form>
    </div>
</template>
