<template>
    <AppLayout title="Gestión de Subcategorías">
        <template #header>
            <div
                class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4"
            >
                <div>
                    <h2
                        class="text-2xl font-bold text-gray-800 dark:text-white"
                    >
                        Gestión de Subcategorías
                    </h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        Administra las subcategorías del sistema
                    </p>
                </div>

                <div class="flex gap-3">
                    <!-- Botón de exportar -->
                    <div class="relative" ref="exportMenuRef">
                        <button
                            @click="toggleExportMenu"
                            :disabled="exportando"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                            title="Exportar datos"
                        >
                            <svg
                                v-if="exportando"
                                class="w-5 h-5 animate-spin"
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
                                    d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"
                                />
                            </svg>
                            {{ exportando ? "Exportando..." : "Exportar" }}
                            <svg
                                class="w-4 h-4"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M19 9l-7 7-7-7"
                                />
                            </svg>
                        </button>

                        <div
                            v-if="exportMenuOpen"
                            class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-10 py-1"
                        >
                            <button
                                @click="exportarDatos(true)"
                                class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2"
                            >
                                <span class="text-lg">📄</span> CSV (Filtros
                                actuales)
                            </button>
                            <button
                                @click="exportarDatos(false)"
                                class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2"
                            >
                                <span class="text-lg">📋</span> CSV (Todos los
                                datos)
                            </button>
                            <hr
                                class="my-1 border-gray-200 dark:border-gray-700"
                            />
                            <button
                                @click="copiarAlPortapapeles"
                                class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2"
                            >
                                <span class="text-lg">📋</span> Copiar al
                                portapapeles
                            </button>
                        </div>
                    </div>

                    <Link
                        :href="route('subcategorias.create')"
                        class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white px-4 py-2 rounded-lg shadow-sm transition-all duration-200"
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
                                d="M12 4v16m8-8H4"
                            />
                        </svg>
                        Nueva subcategoría
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-6">
            <!-- Stats Cards -->
            <div
                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6"
            >
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow duration-200"
                >
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Total subcategorías
                            </p>
                            <p
                                class="text-2xl font-bold text-gray-800 dark:text-white"
                            >
                                {{ subcategorias.total || 0 }}
                            </p>
                        </div>
                        <div
                            class="bg-indigo-100 dark:bg-indigo-900/30 rounded-lg p-3"
                        >
                            <svg
                                class="w-6 h-6 text-indigo-600 dark:text-indigo-400"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l5 5a2 2 0 01.586 1.414V19a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2z"
                                />
                            </svg>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow duration-200"
                >
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Activas
                            </p>
                            <p class="text-2xl font-bold text-green-600">
                                {{ subcategoriasActivas }}
                            </p>
                        </div>
                        <div
                            class="bg-green-100 dark:bg-green-900/30 rounded-lg p-3"
                        >
                            <svg
                                class="w-6 h-6 text-green-600 dark:text-green-400"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                />
                            </svg>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow duration-200"
                >
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Inactivas
                            </p>
                            <p class="text-2xl font-bold text-red-600">
                                {{ subcategoriasInactivas }}
                            </p>
                        </div>
                        <div
                            class="bg-red-100 dark:bg-red-900/30 rounded-lg p-3"
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
                                    d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"
                                />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filtros -->
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm mb-6 p-4 border border-gray-200 dark:border-gray-700"
            >
                <div class="flex flex-col lg:flex-row gap-4">
                    <div class="flex-1">
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                            >Buscar</label
                        >
                        <div class="relative">
                            <svg
                                class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                                />
                            </svg>
                            <input
                                v-model="filtros.busqueda"
                                type="text"
                                placeholder="Buscar por nombre..."
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white dark:bg-gray-900 text-gray-900 dark:text-white"
                                @input="debouncedBuscar"
                            />
                        </div>
                    </div>

                    <div class="w-full lg:w-48">
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                            >Categoría</label
                        >
                        <select
                            v-model="filtros.categoria_id"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white dark:bg-gray-900 text-gray-900 dark:text-white"
                            @change="aplicarFiltros"
                        >
                            <option value="">Todas</option>
                            <option
                                v-for="cat in categoriasDisponibles"
                                :key="cat.id"
                                :value="cat.id"
                            >
                                {{ cat.nombre }}
                            </option>
                        </select>
                    </div>

                    <div class="w-full lg:w-48">
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                            >Estado</label
                        >
                        <select
                            v-model="filtros.estado"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white dark:bg-gray-900 text-gray-900 dark:text-white"
                            @change="aplicarFiltros"
                        >
                            <option value="">Todos</option>
                            <option value="activo">✅ Activo</option>
                            <option value="inactivo">❌ Inactivo</option>
                        </select>
                    </div>

                    <div class="flex items-end gap-2">
                        <button
                            v-if="filtrosActivos"
                            @click="limpiarFiltros"
                            class="px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 transition-colors"
                        >
                            Limpiar filtros
                        </button>
                        <button
                            @click="recargarDatos"
                            class="px-4 py-2 text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 transition-colors"
                            title="Recargar datos"
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
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
                                />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Tabla -->
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden border border-gray-200 dark:border-gray-700"
            >
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead
                            class="bg-gray-50 dark:bg-gray-900/50 border-b border-gray-200 dark:border-gray-700 sticky top-0"
                        >
                            <tr>
                                <th
                                    class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors duration-150 group"
                                    @click="ordenarPor('id')"
                                >
                                    <div class="flex items-center gap-2">
                                        <span>ID</span>
                                        <div class="flex flex-col">
                                            <svg
                                                class="w-3 h-3 transition-colors duration-150"
                                                :class="
                                                    filtros.orden === 'id_asc'
                                                        ? 'text-indigo-600 dark:text-indigo-400'
                                                        : 'text-gray-400 group-hover:text-gray-600'
                                                "
                                                fill="currentColor"
                                                viewBox="0 0 20 20"
                                            >
                                                <path
                                                    fill-rule="evenodd"
                                                    d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z"
                                                    clip-rule="evenodd"
                                                />
                                            </svg>
                                            <svg
                                                class="w-3 h-3 -mt-1 transition-colors duration-150"
                                                :class="
                                                    filtros.orden === 'id_desc'
                                                        ? 'text-indigo-600 dark:text-indigo-400'
                                                        : 'text-gray-400 group-hover:text-gray-600'
                                                "
                                                fill="currentColor"
                                                viewBox="0 0 20 20"
                                            >
                                                <path
                                                    fill-rule="evenodd"
                                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 011.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                    clip-rule="evenodd"
                                                />
                                            </svg>
                                        </div>
                                    </div>
                                </th>
                                <th
                                    class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors duration-150 group"
                                    @click="ordenarPor('nombre')"
                                >
                                    <div class="flex items-center gap-2">
                                        <span>Nombre</span>
                                        <div class="flex flex-col">
                                            <svg
                                                class="w-3 h-3 transition-colors duration-150"
                                                :class="
                                                    filtros.orden ===
                                                    'nombre_asc'
                                                        ? 'text-indigo-600 dark:text-indigo-400'
                                                        : 'text-gray-400 group-hover:text-gray-600'
                                                "
                                                fill="currentColor"
                                                viewBox="0 0 20 20"
                                            >
                                                <path
                                                    fill-rule="evenodd"
                                                    d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z"
                                                    clip-rule="evenodd"
                                                />
                                            </svg>
                                            <svg
                                                class="w-3 h-3 -mt-1 transition-colors duration-150"
                                                :class="
                                                    filtros.orden ===
                                                    'nombre_desc'
                                                        ? 'text-indigo-600 dark:text-indigo-400'
                                                        : 'text-gray-400 group-hover:text-gray-600'
                                                "
                                                fill="currentColor"
                                                viewBox="0 0 20 20"
                                            >
                                                <path
                                                    fill-rule="evenodd"
                                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 011.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                    clip-rule="evenodd"
                                                />
                                            </svg>
                                        </div>
                                    </div>
                                </th>
                                <th
                                    class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                                >
                                    Categoría
                                </th>
                                <th
                                    class="text-left px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                                >
                                    Estado
                                </th>
                                <th
                                    class="text-center px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                                >
                                    Acciones
                                </th>
                            </tr>
                        </thead>

                        <tbody
                            class="divide-y divide-gray-200 dark:divide-gray-700"
                        >
                            <tr
                                v-for="subcategoria in subcategorias.data"
                                :key="subcategoria.id"
                                class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150"
                            >
                                <td
                                    class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400"
                                >
                                    #{{ subcategoria.id }}
                                </td>
                                <td class="px-4 py-3">
                                    <div
                                        class="font-medium text-gray-900 dark:text-white"
                                    >
                                        {{ subcategoria.nombre }}
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span
                                        class="px-2.5 py-1 rounded-lg bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400 text-xs font-medium inline-flex items-center gap-1.5"
                                    >
                                        <svg
                                            class="w-3 h-3"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l5 5a2 2 0 01.586 1.414V19a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2z"
                                            />
                                        </svg>
                                        {{
                                            subcategoria.categoria?.nombre ||
                                            "—"
                                        }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span
                                        :class="{
                                            'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400':
                                                subcategoria.estado ===
                                                'activo',
                                            'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-400':
                                                subcategoria.estado ===
                                                'inactivo',
                                        }"
                                        class="px-2 py-1 rounded-full text-xs font-medium inline-flex items-center gap-1.5"
                                    >
                                        <span
                                            :class="{
                                                'bg-green-500':
                                                    subcategoria.estado ===
                                                    'activo',
                                                'bg-red-500':
                                                    subcategoria.estado ===
                                                    'inactivo',
                                            }"
                                            class="w-1.5 h-1.5 rounded-full"
                                        ></span>
                                        {{
                                            subcategoria.estado === "activo"
                                                ? "Activo"
                                                : "Inactivo"
                                        }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div
                                        class="flex items-center justify-center gap-1.5"
                                    >
                                        <Link
                                            :href="
                                                route(
                                                    'subcategorias.show',
                                                    subcategoria.id,
                                                )
                                            "
                                            class="inline-flex items-center justify-center gap-1 px-3 py-1.5 bg-gray-50 dark:bg-gray-700/50 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-all duration-200 text-sm font-medium"
                                            title="Ver detalles"
                                        >
                                            <svg
                                                class="w-4 h-4"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
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
                                            <span class="hidden sm:inline"
                                                >Ver</span
                                            >
                                        </Link>

                                        <Link
                                            :href="
                                                route(
                                                    'subcategorias.edit',
                                                    subcategoria.id,
                                                )
                                            "
                                            class="inline-flex items-center justify-center gap-1 px-3 py-1.5 bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 hover:bg-blue-100 dark:hover:bg-blue-900/50 rounded-lg transition-all duration-200 text-sm font-medium"
                                            title="Editar subcategoría"
                                        >
                                            <svg
                                                class="w-4 h-4"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                                                />
                                            </svg>
                                            <span class="hidden sm:inline"
                                                >Editar</span
                                            >
                                        </Link>

                                        <button
                                            @click="
                                                confirmarEliminar(subcategoria)
                                            "
                                            class="inline-flex items-center justify-center gap-1 px-3 py-1.5 bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/50 rounded-lg transition-all duration-200 text-sm font-medium"
                                            title="Eliminar subcategoría"
                                        >
                                            <svg
                                                class="w-4 h-4"
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
                                            <span class="hidden sm:inline"
                                                >Eliminar</span
                                            >
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <tr v-if="cargando">
                                <td colspan="5" class="px-4 py-12">
                                    <div
                                        class="flex flex-col items-center justify-center gap-3"
                                    >
                                        <div
                                            class="animate-spin rounded-full h-10 w-10 border-b-2 border-indigo-600"
                                        ></div>
                                        <p
                                            class="text-sm text-gray-500 dark:text-gray-400"
                                        >
                                            Cargando subcategorías...
                                        </p>
                                    </div>
                                </td>
                            </tr>

                            <tr v-else-if="subcategorias.data?.length === 0">
                                <td colspan="5" class="px-4 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <svg
                                            class="w-16 h-16 text-gray-400 dark:text-gray-600 mb-3"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="1.5"
                                                d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"
                                            />
                                        </svg>
                                        <p
                                            class="text-base font-medium text-gray-900 dark:text-white mb-1"
                                        >
                                            No hay subcategorías registradas
                                        </p>
                                        <p
                                            class="text-sm text-gray-500 dark:text-gray-400 mb-4"
                                        >
                                            Comienza creando tu primera
                                            subcategoría
                                        </p>
                                        <Link
                                            :href="
                                                route('subcategorias.create')
                                            "
                                            class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors"
                                        >
                                            <svg
                                                class="w-4 h-4"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M12 4v16m8-8H4"
                                                />
                                            </svg>
                                            Crear primera subcategoría
                                        </Link>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Paginación -->
            <div
                class="flex flex-col sm:flex-row justify-between items-center gap-4 mt-6"
            >
                <div class="flex items-center gap-3">
                    <span class="text-sm text-gray-600 dark:text-gray-400">
                        Mostrando {{ subcategorias.from || 0 }} -
                        {{ subcategorias.to || 0 }} de
                        {{ subcategorias.total || 0 }}
                    </span>
                    <select
                        v-model="itemsPorPagina"
                        class="px-2 py-1 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-900 text-gray-900 dark:text-white"
                        @change="cambiarItemsPorPagina"
                    >
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>

                <div class="flex gap-1 flex-wrap justify-center">
                    <button
                        @click="obtenerSubcategorias(1)"
                        :disabled="subcategorias.current_page === 1"
                        class="px-3 py-1.5 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200"
                    >
                        «
                    </button>
                    <button
                        @click="
                            obtenerSubcategorias(subcategorias.current_page - 1)
                        "
                        :disabled="subcategorias.current_page === 1"
                        class="px-3 py-1.5 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200"
                    >
                        Anterior
                    </button>

                    <button
                        v-for="page in paginasMostradas"
                        :key="page"
                        @click="obtenerSubcategorias(page)"
                        :class="{
                            'bg-indigo-600 text-white border-indigo-600':
                                page === subcategorias.current_page,
                            'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700':
                                page !== subcategorias.current_page,
                        }"
                        class="px-3 py-1.5 rounded-lg border transition-all duration-200 min-w-[36px] font-medium"
                    >
                        {{ page }}
                    </button>

                    <button
                        @click="
                            obtenerSubcategorias(subcategorias.current_page + 1)
                        "
                        :disabled="
                            subcategorias.current_page ===
                            subcategorias.last_page
                        "
                        class="px-3 py-1.5 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200"
                    >
                        Siguiente
                    </button>
                    <button
                        @click="obtenerSubcategorias(subcategorias.last_page)"
                        :disabled="
                            subcategorias.current_page ===
                            subcategorias.last_page
                        "
                        class="px-3 py-1.5 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200"
                    >
                        »
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal de confirmación -->
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
                                Confirmar eliminación
                            </h3>
                            <p
                                class="text-sm text-gray-500 dark:text-gray-400 mt-1"
                            >
                                Esta acción no se puede deshacer
                            </p>
                        </div>
                    </div>

                    <div
                        class="mb-6 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg"
                    >
                        <p class="text-sm text-gray-600 dark:text-gray-300">
                            ¿Estás seguro de eliminar la subcategoría?
                        </p>
                        <p
                            class="font-medium text-gray-900 dark:text-white mt-1"
                        >
                            "{{ subcategoriaAEliminar?.nombre }}"
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
                            @click="eliminar"
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

        <!-- Toast -->
        <div
            v-if="toast.show"
            class="fixed bottom-4 right-4 z-50 animate-slide-up"
        >
            <div
                :class="
                    toast.type === 'success'
                        ? 'bg-green-500'
                        : toast.type === 'warning'
                          ? 'bg-yellow-500'
                          : 'bg-red-500'
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
                    v-else-if="toast.type === 'warning'"
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
import { ref, onMounted, computed, onUnmounted } from "vue";
import axios from "axios";
import { Link } from "@inertiajs/vue3";
import { debounce } from "lodash";
import AppLayout from "@/Layouts/AppLayout.vue";

// Estado
const subcategorias = ref({
    data: [],
    current_page: 1,
    last_page: 1,
    total: 0,
    from: 0,
    to: 0,
});

const categoriasDisponibles = ref([]);
const cargando = ref(false);
const eliminando = ref(false);
const modalVisible = ref(false);
const subcategoriaAEliminar = ref(null);
const itemsPorPagina = ref(10);
const exportando = ref(false);
const exportMenuOpen = ref(false);
const exportMenuRef = ref(null);

const toast = ref({
    show: false,
    message: "",
    type: "success",
});

const filtros = ref({
    busqueda: "",
    estado: "",
    categoria_id: "",
    orden: "id_desc",
});

// Computed
const subcategoriasActivas = computed(() => {
    return (
        subcategorias.value.data?.filter((s) => s.estado === "activo").length ||
        0
    );
});

const subcategoriasInactivas = computed(() => {
    return (
        subcategorias.value.data?.filter((s) => s.estado === "inactivo")
            .length || 0
    );
});

const filtrosActivos = computed(() => {
    return (
        filtros.value.busqueda !== "" ||
        filtros.value.estado !== "" ||
        filtros.value.categoria_id !== ""
    );
});

const paginasMostradas = computed(() => {
    const current = subcategorias.value.current_page;
    const last = subcategorias.value.last_page;
    const delta = 2;
    const range = [];

    for (
        let i = Math.max(2, current - delta);
        i <= Math.min(last - 1, current + delta);
        i++
    ) {
        range.push(i);
    }

    if (current - delta > 2) range.unshift("...");
    if (current + delta < last - 1) range.push("...");
    range.unshift(1);
    if (last !== 1) range.push(last);

    return range;
});

// Métodos auxiliares
const mostrarToast = (message, type = "success") => {
    toast.value = { show: true, message, type };
    setTimeout(() => {
        toast.value.show = false;
    }, 3000);
};

// Método para ordenar
const ordenarPor = (campo) => {
    const ordenActual = filtros.value.orden;
    let nuevoOrden = "";

    if (campo === "id") {
        nuevoOrden = ordenActual === "id_desc" ? "id_asc" : "id_desc";
    } else if (campo === "nombre") {
        nuevoOrden =
            ordenActual === "nombre_desc" ? "nombre_asc" : "nombre_desc";
    }

    filtros.value.orden = nuevoOrden;
    obtenerSubcategorias(1);
};

const aplicarFiltros = () => {
    obtenerSubcategorias(1);
};

// Métodos principales
const obtenerCategoriasDisponibles = async () => {
    try {
        const response = await axios.get("/categorias/lista");
        if (response.data.success) {
            categoriasDisponibles.value = response.data.data;
        }
    } catch (error) {
        console.error("Error al obtener categorías:", error);
        categoriasDisponibles.value = [];
    }
};

const obtenerSubcategorias = async (page = 1) => {
    cargando.value = true;

    try {
        const params = new URLSearchParams({
            page: page,
            per_page: itemsPorPagina.value,
            ...(filtros.value.busqueda && { search: filtros.value.busqueda }),
            ...(filtros.value.estado && { estado: filtros.value.estado }),
            ...(filtros.value.categoria_id && {
                categoria_id: filtros.value.categoria_id,
            }),
            ...(filtros.value.orden && { orden: filtros.value.orden }),
        });

        const response = await axios.get(`/subcategorias?${params}`);
        subcategorias.value = response.data.data;
    } catch (error) {
        console.error("Error al obtener subcategorías:", error);
        mostrarToast("Error al cargar las subcategorías", "error");
    } finally {
        cargando.value = false;
    }
};

const recargarDatos = () => {
    obtenerSubcategorias(subcategorias.value.current_page);
    mostrarToast("Datos recargados correctamente", "success");
};

const cambiarItemsPorPagina = () => {
    obtenerSubcategorias(1);
};

const limpiarFiltros = () => {
    filtros.value = {
        busqueda: "",
        estado: "",
        categoria_id: "",
        orden: "id_desc",
    };
    obtenerSubcategorias(1);
};

const confirmarEliminar = (subcategoria) => {
    subcategoriaAEliminar.value = subcategoria;
    modalVisible.value = true;
};

const eliminar = async () => {
    if (!subcategoriaAEliminar.value) return;

    eliminando.value = true;

    try {
        await axios.delete(`/subcategorias/${subcategoriaAEliminar.value.id}`);
        modalVisible.value = false;
        mostrarToast("Subcategoría eliminada correctamente", "success");

        const paginaActual =
            subcategorias.value.data.length === 1 &&
            subcategorias.value.current_page > 1
                ? subcategorias.value.current_page - 1
                : subcategorias.value.current_page;

        await obtenerSubcategorias(paginaActual);
    } catch (error) {
        console.error("Error al eliminar:", error);
        mostrarToast(
            error.response?.data?.message ||
                "Error al eliminar la subcategoría",
            "error",
        );
    } finally {
        eliminando.value = false;
        subcategoriaAEliminar.value = null;
    }
};

// Exportar datos
const exportarDatos = async (soloFiltrados = true) => {
    exportando.value = true;
    exportMenuOpen.value = false;

    try {
        const params = new URLSearchParams();
        if (soloFiltrados) {
            if (filtros.value.busqueda)
                params.append("search", filtros.value.busqueda);
            if (filtros.value.estado)
                params.append("estado", filtros.value.estado);
            if (filtros.value.categoria_id)
                params.append("categoria_id", filtros.value.categoria_id);
        }

        const url = `/subcategorias/export${params.toString() ? "?" + params.toString() : ""}`;
        const response = await fetch(url, {
            method: "GET",
            headers: {
                Accept: "text/csv",
                "X-Requested-With": "XMLHttpRequest",
            },
        });

        if (!response.ok) throw new Error("Error en la exportación");

        const blob = await response.blob();
        const blobUrl = window.URL.createObjectURL(blob);
        const link = document.createElement("a");
        link.href = blobUrl;

        const contentDisposition = response.headers.get("Content-Disposition");
        let filename = `subcategorias_${new Date().toISOString().slice(0, 19)}.csv`;
        if (contentDisposition) {
            const filenameMatch = contentDisposition.match(
                /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/,
            );
            if (filenameMatch && filenameMatch[1])
                filename = filenameMatch[1].replace(/['"]/g, "");
        }

        link.download = filename;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        window.URL.revokeObjectURL(blobUrl);

        mostrarToast(`Exportación completada: ${filename}`, "success");
    } catch (error) {
        console.error("Error al exportar:", error);
        mostrarToast("Error al exportar los datos", "error");
    } finally {
        exportando.value = false;
    }
};

// Copiar al portapapeles
const copiarAlPortapapeles = async () => {
    exportando.value = true;
    exportMenuOpen.value = false;

    try {
        const params = new URLSearchParams();
        if (filtros.value.busqueda)
            params.append("search", filtros.value.busqueda);
        if (filtros.value.estado) params.append("estado", filtros.value.estado);
        if (filtros.value.categoria_id)
            params.append("categoria_id", filtros.value.categoria_id);

        const response = await fetch(
            `/subcategorias/export?${params.toString()}`,
        );
        const text = await response.text();
        await navigator.clipboard.writeText(text);
        mostrarToast("Datos copiados al portapapeles", "success");
    } catch (error) {
        console.error("Error al copiar:", error);
        mostrarToast("Error al copiar los datos", "error");
    } finally {
        exportando.value = false;
    }
};

const toggleExportMenu = () => {
    exportMenuOpen.value = !exportMenuOpen.value;
};
const handleClickOutside = (event) => {
    if (exportMenuRef.value && !exportMenuRef.value.contains(event.target))
        exportMenuOpen.value = false;
};
const debouncedBuscar = debounce(() => {
    obtenerSubcategorias(1);
}, 500);

onMounted(() => {
    obtenerSubcategorias();
    obtenerCategoriasDisponibles();
    document.addEventListener("click", handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener("click", handleClickOutside);
    debouncedBuscar.cancel();
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
.overflow-x-auto {
    scrollbar-width: thin;
    scrollbar-color: #cbd5e1 #f1f5f9;
}
.overflow-x-auto::-webkit-scrollbar {
    height: 6px;
}
.overflow-x-auto::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 3px;
}
.overflow-x-auto::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}
.overflow-x-auto::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>
