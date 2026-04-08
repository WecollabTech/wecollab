<template>
    <AppLayout title="Editar Tutorial">
        <template #header>
            <div
                class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4"
            >
                <div>
                    <h2
                        class="text-2xl font-bold text-gray-800 dark:text-white"
                    >
                        Editar Tutorial
                    </h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        Modifica los datos del tutorial existente
                    </p>
                </div>

                <div class="flex gap-3">
                    <button
                        @click="cancelar"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg transition-all duration-200"
                    >
                        <svg
                            class="w-5 h-5"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18"
                            />
                        </svg>
                        Volver al listado
                    </button>

                    <button
                        v-if="tutorialOriginal"
                        @click="confirmarEliminacion"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-all duration-200"
                    >
                        <svg
                            class="w-5 h-5"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                            />
                        </svg>
                        Eliminar
                    </button>
                </div>
            </div>
        </template>

        <div class="py-6 max-w-4xl mx-auto">
            <!-- Estado de carga -->
            <div
                v-if="cargando"
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-12"
            >
                <div class="flex flex-col items-center justify-center">
                    <div
                        class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mb-4"
                    ></div>
                    <p class="text-gray-600 dark:text-gray-400">
                        Cargando datos del tutorial...
                    </p>
                </div>
            </div>

            <!-- Formulario -->
            <div
                v-else-if="tutorialOriginal"
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden"
            >
                <form @submit.prevent="actualizarTutorial">
                    <div class="p-6 space-y-6">
                        <!-- Indicador de cambios -->
                        <div
                            v-if="hayCambios"
                            class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-3"
                        >
                            <div
                                class="flex items-center gap-2 text-yellow-800 dark:text-yellow-300"
                            >
                                <svg
                                    class="w-5 h-5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                                    />
                                </svg>
                                <span class="text-sm font-medium"
                                    >Tienes cambios sin guardar</span
                                >
                            </div>
                        </div>

                        <!-- ID del tutorial -->
                        <div
                            class="bg-gray-50 dark:bg-gray-900/50 rounded-lg p-3"
                        >
                            <div class="flex items-center gap-2">
                                <span
                                    class="text-sm font-medium text-gray-500 dark:text-gray-400"
                                    >ID:</span
                                >
                                <span
                                    class="text-sm font-mono text-gray-700 dark:text-gray-300"
                                    >#{{ tutorialOriginal.id }}</span
                                >
                            </div>
                        </div>

                        <!-- Titulo -->
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                            >
                                Titulo *
                            </label>
                            <input
                                v-model="form.titulo"
                                type="text"
                                maxlength="100"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white dark:bg-gray-900 text-gray-900 dark:text-white transition-colors"
                                :class="{
                                    'border-red-500 dark:border-red-500':
                                        errors.titulo,
                                }"
                                placeholder="Ej: Introduccion a Laravel"
                            />
                            <p
                                v-if="errors.titulo"
                                class="mt-1 text-sm text-red-600 dark:text-red-400"
                            >
                                {{ errors.titulo }}
                            </p>
                        </div>

                        <!-- Descripcion -->
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                            >
                                Descripcion
                            </label>
                            <textarea
                                v-model="form.descripcion"
                                rows="4"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white dark:bg-gray-900 text-gray-900 dark:text-white resize-none transition-colors"
                                :class="{
                                    'border-red-500 dark:border-red-500':
                                        errors.descripcion,
                                }"
                                placeholder="Describe el contenido del tutorial..."
                            ></textarea>
                            <p
                                v-if="errors.descripcion"
                                class="mt-1 text-sm text-red-600 dark:text-red-400"
                            >
                                {{ errors.descripcion }}
                            </p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Tipo de Material -->
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                                >
                                    Tipo de Material *
                                </label>
                                <select
                                    v-model="form.tipo_material"
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white dark:bg-gray-900 text-gray-900 dark:text-white transition-colors"
                                    :class="{
                                        'border-red-500 dark:border-red-500':
                                            errors.tipo_material,
                                    }"
                                >
                                    <option value="video">Video</option>
                                    <option value="manual">Manual</option>
                                    <option value="guia">Guia</option>
                                    <option value="post">Post</option>
                                    <option value="triptico">Infografia</option>
                                    <option value="avisos importantes">
                                        Avisos Importantes
                                    </option>
                                </select>
                                <p
                                    v-if="errors.tipo_material"
                                    class="mt-1 text-sm text-red-600 dark:text-red-400"
                                >
                                    {{ errors.tipo_material }}
                                </p>
                            </div>

                            <!-- Formato -->
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                                >
                                    Formato *
                                </label>
                                <select
                                    v-model="form.formato"
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white dark:bg-gray-900 text-gray-900 dark:text-white transition-colors"
                                    :class="{
                                        'border-red-500 dark:border-red-500':
                                            errors.formato,
                                    }"
                                >
                                    <option value="pdf">PDF</option>
                                    <option value="word">Word</option>
                                    <option value="mp4">MP4</option>
                                </select>
                                <p
                                    v-if="errors.formato"
                                    class="mt-1 text-sm text-red-600 dark:text-red-400"
                                >
                                    {{ errors.formato }}
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Categoria -->
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                                >
                                    Categoria *
                                </label>
                                <select
                                    v-model="form.categorias_id"
                                    @change="onCategoriaChange"
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white dark:bg-gray-900 text-gray-900 dark:text-white transition-colors"
                                    :class="{
                                        'border-red-500 dark:border-red-500':
                                            errors.categorias_id,
                                    }"
                                >
                                    <option :value="null">
                                        Seleccione una categoria
                                    </option>
                                    <option
                                        v-for="cat in categorias"
                                        :key="cat.id"
                                        :value="cat.id"
                                    >
                                        {{ cat.nombre }}
                                    </option>
                                </select>
                                <p
                                    v-if="errors.categorias_id"
                                    class="mt-1 text-sm text-red-600 dark:text-red-400"
                                >
                                    {{ errors.categorias_id }}
                                </p>
                            </div>

                            <!-- Subcategoria -->
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                                >
                                    Subcategoria *
                                </label>
                                <select
                                    v-model="form.subcategoria_id"
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white dark:bg-gray-900 text-gray-900 dark:text-white transition-colors"
                                    :class="{
                                        'border-red-500 dark:border-red-500':
                                            errors.subcategoria_id,
                                    }"
                                    :disabled="!form.categorias_id"
                                >
                                    <option :value="null">
                                        {{
                                            !form.categorias_id
                                                ? "Primero seleccione una categoria"
                                                : "Seleccione una subcategoria"
                                        }}
                                    </option>
                                    <option
                                        v-for="sub in subcategoriasFiltradas"
                                        :key="sub.id"
                                        :value="sub.id"
                                    >
                                        {{ sub.nombre }}
                                    </option>
                                </select>
                                <p
                                    v-if="errors.subcategoria_id"
                                    class="mt-1 text-sm text-red-600 dark:text-red-400"
                                >
                                    {{ errors.subcategoria_id }}
                                </p>
                                <p
                                    v-if="
                                        form.categorias_id &&
                                        subcategoriasFiltradas.length === 0
                                    "
                                    class="mt-1 text-xs text-amber-600 dark:text-amber-400"
                                >
                                    Esta categoria no tiene subcategorias
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Alcance -->
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                                >
                                    Alcance *
                                </label>

                                <select
                                    v-model="form.alcance"
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white dark:bg-gray-900 text-gray-900 dark:text-white transition-colors"
                                    :class="{
                                        'border-red-500 dark:border-red-500':
                                            errors.alcance,
                                    }"
                                    @change="validarCampo('alcance')"
                                >
                                    <option value="">
                                        Seleccione un alcance
                                    </option>
                                    <option value="Superadmin we collab">
                                        Superadmin we collab
                                    </option>
                                    <option value="Admin we collab">
                                        Admin we collab
                                    </option>
                                    <option value="Soporte we collab">
                                        Soporte we collab
                                    </option>
                                    <option value="Usuario we collab">
                                        Usuario we collab
                                    </option>
                                    <option value="Suscriptor SLC">
                                        Suscriptor SLC
                                    </option>
                                    <option value="Usuario Admin SLC">
                                        Usuario Admin SLC
                                    </option>
                                    <option value="Usuario Premium SLC">
                                        Usuario Premium SLC
                                    </option>
                                    <option value="Usuario Público">
                                        Usuario Público
                                    </option>
                                    <option value="Prospecto">Prospecto</option>
                                </select>

                                <p
                                    v-if="errors.alcance"
                                    class="mt-1 text-sm text-red-600 dark:text-red-400"
                                >
                                    {{ errors.alcance[0] || errors.alcance }}
                                </p>
                            </div>

                            <!-- Estado -->
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                                >
                                    Estado *
                                </label>
                                <div class="flex gap-4">
                                    <label class="inline-flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.estado"
                                            value="activo"
                                            class="form-radio text-indigo-600 focus:ring-indigo-500"
                                        />
                                        <span
                                            class="ml-2 text-gray-700 dark:text-gray-300"
                                            >Activo</span
                                        >
                                    </label>
                                    <label class="inline-flex items-center">
                                        <input
                                            type="radio"
                                            v-model="form.estado"
                                            value="inactivo"
                                            class="form-radio text-indigo-600 focus:ring-indigo-500"
                                        />
                                        <span
                                            class="ml-2 text-gray-700 dark:text-gray-300"
                                            >Inactivo</span
                                        >
                                    </label>
                                </div>
                                <p
                                    v-if="errors.estado"
                                    class="mt-1 text-sm text-red-600 dark:text-red-400"
                                >
                                    {{ errors.estado }}
                                </p>
                            </div>
                        </div>

                        <!-- URL -->
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                            >
                                URL del contenido
                            </label>
                            <input
                                v-model="form.url"
                                type="url"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white dark:bg-gray-900 text-gray-900 dark:text-white transition-colors"
                                :class="{
                                    'border-red-500 dark:border-red-500':
                                        errors.url,
                                }"
                                placeholder="https://ejemplo.com/tutorial"
                            />
                            <p
                                v-if="errors.url"
                                class="mt-1 text-sm text-red-600 dark:text-red-400"
                            >
                                {{ errors.url }}
                            </p>

                            <!-- Preview de YouTube -->
                            <div v-if="previewUrl" class="mt-3">
                                <p
                                    class="text-sm text-gray-500 dark:text-gray-400 mb-2"
                                >
                                    Vista previa:
                                </p>
                                <img
                                    :src="previewUrl"
                                    class="w-48 h-28 object-cover rounded-lg shadow"
                                />
                            </div>
                        </div>

                        <!-- Observacion -->
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                            >
                                Observacion
                                <span class="text-xs text-gray-500 ml-1"
                                    >(solo visible para admins)</span
                                >
                            </label>
                            <textarea
                                v-model="form.observacion"
                                rows="2"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white dark:bg-gray-900 text-gray-900 dark:text-white resize-none transition-colors"
                                :class="{
                                    'border-red-500 dark:border-red-500':
                                        errors.observacion,
                                }"
                                placeholder="Notas internas para administradores..."
                            ></textarea>
                            <p
                                v-if="errors.observacion"
                                class="mt-1 text-sm text-red-600 dark:text-red-400"
                            >
                                {{ errors.observacion }}
                            </p>
                        </div>

                        <!-- Información de fechas -->
                        <div
                            class="border-t border-gray-200 dark:border-gray-700 pt-4 grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm"
                        >
                            <div>
                                <span class="text-gray-500 dark:text-gray-400"
                                    >Creado:</span
                                >
                                <span
                                    class="ml-2 text-gray-700 dark:text-gray-300"
                                    >{{
                                        formatDate(tutorialOriginal.created_at)
                                    }}</span
                                >
                            </div>
                            <div>
                                <span class="text-gray-500 dark:text-gray-400"
                                    >Ultima actualizacion:</span
                                >
                                <span
                                    class="ml-2 text-gray-700 dark:text-gray-300"
                                    >{{
                                        formatDate(tutorialOriginal.updated_at)
                                    }}</span
                                >
                            </div>
                        </div>
                    </div>

                    <!-- Botones -->
                    <div
                        class="px-6 py-4 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row gap-3 justify-end"
                    >
                        <button
                            type="button"
                            @click="cancelar"
                            class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200 text-center"
                        >
                            Cancelar
                        </button>
                        <button
                            type="button"
                            v-if="hayCambios"
                            @click="restaurarOriginal"
                            class="px-4 py-2 rounded-lg border border-yellow-300 dark:border-yellow-700 text-yellow-700 dark:text-yellow-400 hover:bg-yellow-50 dark:hover:bg-yellow-900/20 transition-colors duration-200"
                        >
                            Restaurar
                        </button>
                        <button
                            type="submit"
                            :disabled="enviando || !hayCambios"
                            class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white rounded-lg shadow-sm transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                        >
                            <svg
                                v-if="enviando"
                                class="w-4 h-4 animate-spin"
                                fill="none"
                                stroke="currentColor"
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
                            {{ enviando ? "Guardando..." : "Guardar cambios" }}
                        </button>
                    </div>
                </form>
            </div>

            <!-- Error al cargar -->
            <div
                v-else
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-12 text-center"
            >
                <svg
                    class="mx-auto h-16 w-16 text-gray-400 mb-4"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                    />
                </svg>
                <h3
                    class="text-lg font-medium text-gray-900 dark:text-white mb-2"
                >
                    Tutorial no encontrado
                </h3>
                <p class="text-gray-600 dark:text-gray-400 mb-4">
                    El tutorial que buscas no existe o ha sido eliminado.
                </p>
                <button
                    @click="cancelar"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg"
                >
                    Volver al listado
                </button>
            </div>
        </div>

        <!-- Modal de confirmacion para eliminar -->
        <div
            v-if="modalVisible"
            class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4"
            @click.self="modalVisible = false"
        >
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-md w-full transform transition-all animate-slide-up"
            >
                <div class="p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div
                            class="bg-red-100 dark:bg-red-900/30 rounded-full p-2.5"
                        >
                            <svg
                                class="w-6 h-6 text-red-600 dark:text-red-400"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                                />
                            </svg>
                        </div>
                        <div>
                            <h3
                                class="text-xl font-semibold text-gray-900 dark:text-white"
                            >
                                Eliminar tutorial
                            </h3>
                            <p
                                class="text-sm text-gray-500 dark:text-gray-400 mt-1"
                            >
                                Esta accion no se puede deshacer
                            </p>
                        </div>
                    </div>

                    <div
                        class="mb-6 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg"
                    >
                        <p class="text-sm text-gray-600 dark:text-gray-300">
                            ¿Estas seguro de eliminar el tutorial?
                        </p>
                        <p
                            class="font-medium text-gray-900 dark:text-white mt-1"
                        >
                            "{{ tutorialOriginal?.titulo }}"
                        </p>
                    </div>

                    <div class="flex gap-3 justify-end">
                        <button
                            @click="modalVisible = false"
                            class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-200"
                        >
                            Cancelar
                        </button>
                        <button
                            @click="eliminarTutorial"
                            :disabled="eliminando"
                            class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 flex items-center gap-2"
                        >
                            <svg
                                v-if="eliminando"
                                class="w-4 h-4 animate-spin"
                                fill="none"
                                stroke="currentColor"
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
                            {{ eliminando ? "Eliminando..." : "Eliminar" }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Toast de notificacion -->
        <div
            v-if="toast.show"
            class="fixed bottom-4 right-4 z-50 animate-slide-up"
        >
            <div
                :class="
                    toast.type === 'success' ? 'bg-green-500' : 'bg-red-500'
                "
                class="text-white px-6 py-3 rounded-lg shadow-lg flex items-center gap-2"
            >
                <svg
                    v-if="toast.type === 'success'"
                    class="w-5 h-5"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M5 13l4 4L19 7"
                    />
                </svg>
                <svg
                    v-else
                    class="w-5 h-5"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M6 18L18 6M6 6l12 12"
                    />
                </svg>
                {{ toast.message }}
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import axios from "axios";
import AppLayout from "@/Layouts/AppLayout.vue";

// ✅ CORREGIDO: Props que puede recibir el componente
const props = defineProps({
    id: {
        type: [Number, String],
        required: false,
        default: null,
    },
    tutorial: {
        type: Object,
        required: false,
        default: null,
    },
    categorias: {
        type: Array,
        required: false,
        default: () => [],
    },
    subcategorias: {
        type: Array,
        required: false,
        default: () => [],
    },
});

// ✅ Obtener el ID de diferentes fuentes
const page = usePage();
const tutorialId = computed(() => {
    return (
        props.id ||
        props.tutorial?.id ||
        page.props.id ||
        page.props.tutorial?.id
    );
});

console.log("ID obtenido en Edit.vue:", tutorialId.value);

// Estado
const tutorialOriginal = ref(null);
const categorias = ref(props.categorias || []);
const subcategorias = ref(props.subcategorias || []);
const subcategoriasFiltradas = ref([]);
const cargando = ref(!props.tutorial); // Si ya tenemos el tutorial, no cargamos
const enviando = ref(false);
const eliminando = ref(false);
const modalVisible = ref(false);
const errors = reactive({});
const toast = ref({
    show: false,
    message: "",
    type: "success",
});

// Formulario
const form = reactive({
    titulo: "",
    descripcion: "",
    tipo_material: "",
    formato: "",
    categorias_id: null,
    subcategoria_id: null,
    alcance: "",
    estado: "activo",
    url: "",
    observacion: "",
});

// Computed para detectar cambios
const hayCambios = computed(() => {
    if (!tutorialOriginal.value) return false;
    return (
        form.titulo !== tutorialOriginal.value.titulo ||
        form.descripcion !== (tutorialOriginal.value.descripcion || "") ||
        form.tipo_material !== tutorialOriginal.value.tipo_material ||
        form.formato !== tutorialOriginal.value.formato ||
        form.categorias_id !== tutorialOriginal.value.categorias_id ||
        form.subcategoria_id !== tutorialOriginal.value.subcategoria_id ||
        form.alcance !== tutorialOriginal.value.alcance ||
        form.estado !== tutorialOriginal.value.estado ||
        form.url !== (tutorialOriginal.value.url || "") ||
        form.observacion !== (tutorialOriginal.value.observacion || "")
    );
});

// Preview de YouTube
const extractYouTubeId = (url) => {
    if (!url) return null;
    const patterns = [
        /youtu\.be\/([a-zA-Z0-9_-]{11})/,
        /[?&]v=([a-zA-Z0-9_-]{11})/,
        /embed\/([a-zA-Z0-9_-]{11})/,
        /shorts\/([a-zA-Z0-9_-]{11})/,
    ];
    for (const pattern of patterns) {
        const match = url.match(pattern);
        if (match && match[1]) return match[1];
    }
    return null;
};

const previewUrl = computed(() => {
    if (form.tipo_material !== "video") return null;
    const videoId = extractYouTubeId(form.url);
    if (videoId) return `https://img.youtube.com/vi/${videoId}/hqdefault.jpg`;
    return null;
});

// Formatear fecha
const formatDate = (date) => {
    if (!date) return "—";
    const d = new Date(date);
    return d.toLocaleDateString("es-ES", {
        day: "2-digit",
        month: "long",
        year: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
};

// Mostrar notificacion
const mostrarToast = (message, type = "success") => {
    toast.value = {
        show: true,
        message,
        type,
    };
    setTimeout(() => {
        toast.value.show = false;
    }, 3000);
};

// Cargar categorias si no vinieron por props
const cargarCategorias = async () => {
    if (categorias.value.length > 0) return;

    try {
        const response = await axios.get("/categorias/lista");
        categorias.value =
            response.data?.data ||
            response.data?.categorias ||
            response.data ||
            [];
    } catch (error) {
        console.error("Error al cargar categorias:", error);
        mostrarToast("Error al cargar las categorias", "error");
    }
};

// Cargar subcategorias si no vinieron por props
const cargarSubcategorias = async () => {
    if (subcategorias.value.length > 0) return;

    try {
        const response = await axios.get("/subcategorias/all");
        subcategorias.value =
            response.data?.data ||
            response.data?.subcategorias ||
            response.data ||
            [];
    } catch (error) {
        console.error("Error al cargar subcategorias:", error);
        mostrarToast("Error al cargar las subcategorias", "error");
    }
};

// Filtrar subcategorias por categoria seleccionada
const onCategoriaChange = () => {
    if (form.categorias_id) {
        subcategoriasFiltradas.value = subcategorias.value.filter(
            (sub) => sub.categoria_id === form.categorias_id,
        );
    } else {
        subcategoriasFiltradas.value = [];
    }
    form.subcategoria_id = null;
};

// Cargar datos del tutorial si no vino por props
const cargarTutorial = async () => {
    const id = tutorialId.value;
    if (!id) {
        console.error("No se pudo obtener el ID del tutorial");
        mostrarToast("Error: No se especificó el tutorial a editar", "error");
        cargando.value = false;
        return;
    }

    cargando.value = true;
    try {
        const response = await axios.get(`/tutoriales/${id}`);
        if (response.data.success) {
            tutorialOriginal.value = response.data.data;

            // Rellenar formulario
            form.titulo = tutorialOriginal.value.titulo;
            form.descripcion = tutorialOriginal.value.descripcion || "";
            form.tipo_material = tutorialOriginal.value.tipo_material;
            form.formato = tutorialOriginal.value.formato;
            form.categorias_id = tutorialOriginal.value.categorias_id;
            form.subcategoria_id = tutorialOriginal.value.subcategoria_id;
            form.alcance = tutorialOriginal.value.alcance;
            form.estado = tutorialOriginal.value.estado;
            form.url = tutorialOriginal.value.url || "";
            form.observacion = tutorialOriginal.value.observacion || "";

            // Filtrar subcategorias
            if (form.categorias_id) {
                subcategoriasFiltradas.value = subcategorias.value.filter(
                    (sub) => sub.categoria_id === form.categorias_id,
                );
            }
        }
    } catch (error) {
        console.error("Error al cargar tutorial:", error);
        mostrarToast("Error al cargar los datos del tutorial", "error");
    } finally {
        cargando.value = false;
    }
};

// Actualizar tutorial
const actualizarTutorial = async () => {
    if (!hayCambios.value) return;

    enviando.value = true;
    Object.keys(errors).forEach((key) => delete errors[key]);

    try {
        const payload = {
            titulo: form.titulo,
            descripcion: form.descripcion,
            tipo_material: form.tipo_material,
            formato: form.formato,
            categorias_id: form.categorias_id || null,
            subcategoria_id: form.subcategoria_id || null,
            alcance: form.alcance,
            estado: form.estado,
            url: form.url || null,
            observacion: form.observacion || null,
        };

        const id = tutorialId.value;
        const response = await axios.put(`/tutoriales/${id}`, payload);

        if (response.data.success) {
            mostrarToast(response.data.message, "success");

            setTimeout(() => {
                router.visit(route("recursos.index"));
            }, 1000);
        }
    } catch (error) {
        if (error.response?.status === 422) {
            Object.assign(errors, error.response.data.errors);
            mostrarToast("Corrige los errores del formulario", "error");
        } else {
            mostrarToast(
                error.response?.data?.message || "Error al actualizar",
                "error",
            );
        }
    } finally {
        enviando.value = false;
    }
};

// Restaurar valores originales
const restaurarOriginal = () => {
    if (tutorialOriginal.value) {
        form.titulo = tutorialOriginal.value.titulo;
        form.descripcion = tutorialOriginal.value.descripcion || "";
        form.tipo_material = tutorialOriginal.value.tipo_material;
        form.formato = tutorialOriginal.value.formato;
        form.categorias_id = tutorialOriginal.value.categorias_id;
        form.subcategoria_id = tutorialOriginal.value.subcategoria_id;
        form.alcance = tutorialOriginal.value.alcance;
        form.estado = tutorialOriginal.value.estado;
        form.url = tutorialOriginal.value.url || "";
        form.observacion = tutorialOriginal.value.observacion || "";

        onCategoriaChange();
        mostrarToast("Cambios revertidos", "success");
    }
};

// Confirmar eliminacion
const confirmarEliminacion = () => {
    modalVisible.value = true;
};

// Eliminar tutorial
const eliminarTutorial = async () => {
    eliminando.value = true;

    try {
        const id = tutorialId.value;
        const response = await axios.delete(`/tutoriales/${id}`);

        if (response.data.success) {
            mostrarToast(response.data.message, "success");

            setTimeout(() => {
                router.visit(route("recursos.index"));
            }, 1000);
        }
    } catch (error) {
        if (error.response?.status === 422) {
            mostrarToast(error.response.data.message, "error");
        } else {
            mostrarToast("Error al eliminar el tutorial", "error");
        }
        modalVisible.value = false;
    } finally {
        eliminando.value = false;
    }
};

// Cancelar - redirigir a recursos.index
const cancelar = () => {
    router.visit(route("recursos.index"));
};

onMounted(async () => {
    // Si ya tenemos el tutorial por props, usarlo
    if (props.tutorial) {
        tutorialOriginal.value = props.tutorial;
        form.titulo = tutorialOriginal.value.titulo;
        form.descripcion = tutorialOriginal.value.descripcion || "";
        form.tipo_material = tutorialOriginal.value.tipo_material;
        form.formato = tutorialOriginal.value.formato;
        form.categorias_id = tutorialOriginal.value.categorias_id;
        form.subcategoria_id = tutorialOriginal.value.subcategoria_id;
        form.alcance = tutorialOriginal.value.alcance;
        form.estado = tutorialOriginal.value.estado;
        form.url = tutorialOriginal.value.url || "";
        form.observacion = tutorialOriginal.value.observacion || "";
        cargando.value = false;
    }

    await cargarCategorias();
    await cargarSubcategorias();

    if (!props.tutorial && tutorialId.value) {
        await cargarTutorial();
    }

    // Filtrar subcategorias iniciales
    if (form.categorias_id) {
        subcategoriasFiltradas.value = subcategorias.value.filter(
            (sub) => sub.categoria_id === form.categorias_id,
        );
    }
});
</script>

<style scoped>
@keyframes slide-up {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-slide-up {
    animation: slide-up 0.3s ease-out;
}
</style>
