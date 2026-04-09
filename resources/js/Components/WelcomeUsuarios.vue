<template>
    <div
        class="w-full min-h-screen bg-gradient-to-br from-slate-50 via-white to-slate-100"
    >
        <!-- HEADER -->
        <header
            class="bg-white/80 backdrop-blur-lg border-b border-gray-200 px-4 sm:px-6 py-4 sticky top-0 z-20 shadow-sm"
        >
            <div class="max-w-7xl mx-auto">
                <div
                    class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4"
                >
                    <div>
                        <h1
                            class="text-2xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent"
                        >
                            📚 Biblioteca de Contenido
                        </h1>
                        <p class="text-sm text-gray-500 mt-1">
                            Bienvenido,
                            <strong>{{ user?.name || "Invitado" }}</strong>
                            <span
                                v-if="getUserRole() !== 'Invitado'"
                                class="text-indigo-600 ml-1"
                            >
                                <!-- • {{ getUserRole() }} (Nivel
                                {{ getRolLevel(getUserRole()) }}) -->
                            </span>
                            <span
                                v-if="esRolWeCollab()"
                                class="text-purple-600 ml-2 text-xs"
                                >🏢 We Collab</span
                            >
                            <span
                                v-else-if="esRolCliente()"
                                class="text-teal-600 ml-2 text-xs"
                            ></span>
                        </p>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="flex bg-gray-100 rounded-xl p-1">
                            <button
                                @click="cambiarVista('grid')"
                                :class="[
                                    'px-3 py-1.5 rounded-lg text-sm font-medium',
                                    viewMode === 'grid'
                                        ? 'bg-white text-indigo-600 shadow-sm'
                                        : 'text-gray-600 hover:bg-white/50',
                                ]"
                            >
                                🖼️ Grid
                            </button>
                            <button
                                @click="cambiarVista('list')"
                                :class="[
                                    'px-3 py-1.5 rounded-lg text-sm font-medium',
                                    viewMode === 'list'
                                        ? 'bg-white text-indigo-600 shadow-sm'
                                        : 'text-gray-600 hover:bg-white/50',
                                ]"
                            >
                                📋 Lista
                            </button>
                        </div>

                        <div class="relative w-full lg:w-80">
                            <input
                                v-model="search"
                                type="text"
                                placeholder="Buscar contenido..."
                                class="w-full pl-10 pr-4 py-2 rounded-xl border border-gray-300 bg-white focus:ring-2 focus:ring-indigo-500 outline-none text-sm"
                            />
                            <span class="absolute left-3 top-2.5 text-gray-400"
                                >🔍</span
                            >
                        </div>
                    </div>
                </div>

                <!-- Filtros de tipo con iconos mejorados -->
                <div class="flex flex-wrap gap-2 mt-4">
                    <button
                        v-for="tipo in tipos"
                        :key="tipo.value"
                        @click="tipoSeleccionado = tipo.value"
                        :class="filterClass(tipo.value)"
                    >
                        <span class="mr-1">{{ tipo.icon }}</span>
                        {{ tipo.label }}
                    </button>
                    <button
                        v-if="search || tipoSeleccionado !== 'todo'"
                        @click="resetFiltros"
                        class="px-3 py-1.5 rounded-full text-sm text-gray-500 hover:bg-gray-100"
                    >
                        ✕ Limpiar
                    </button>
                </div>

                <!-- Filtros de Categoría y Subcategoría -->
                <div class="flex flex-wrap gap-4 mt-4">
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-xs text-gray-500 mb-1"
                            >Categoría</label
                        >
                        <select
                            v-model="categoriaSeleccionada"
                            @change="cargarSubcategorias"
                            class="w-full px-3 py-2 rounded-lg border border-gray-300 bg-white focus:ring-2 focus:ring-indigo-500 outline-none text-sm"
                        >
                            <option value="">Todas las categorías</option>
                            <option
                                v-for="cat in categorias"
                                :key="cat.id"
                                :value="cat.id"
                            >
                                {{ cat.nombre }}
                            </option>
                        </select>
                    </div>

                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-xs text-gray-500 mb-1"
                            >Subcategoría</label
                        >
                        <select
                            v-model="subcategoriaSeleccionada"
                            class="w-full px-3 py-2 rounded-lg border border-gray-300 bg-white focus:ring-2 focus:ring-indigo-500 outline-none text-sm"
                            :disabled="!categoriaSeleccionada"
                        >
                            <option value="">Todas las subcategorías</option>
                            <option
                                v-for="sub in subcategoriasFiltradas"
                                :key="sub.id"
                                :value="sub.id"
                            >
                                {{ sub.nombre }}
                            </option>
                        </select>
                    </div>

                    <div class="flex items-end">
                        <button
                            @click="limpiarFiltrosCategoria"
                            class="px-4 py-2 rounded-lg bg-gray-200 text-gray-700 hover:bg-gray-300 transition text-sm"
                        >
                            Limpiar filtros
                        </button>
                    </div>
                </div>

                <!-- Stats -->
                <div class="flex gap-4 mt-3 text-xs text-gray-500">
                    <span>📊 Total: {{ stats.total }}</span>
                    <span>👁️ Visibles: {{ stats.visibles }}</span>
                    <span v-if="stats.accesibles > 0" class="text-green-600"
                        >✅ Accesibles: {{ stats.accesibles }}</span
                    >
                    <span v-if="stats.restringidos > 0" class="text-amber-600"
                        >🔒 Restringidos: {{ stats.restringidos }}</span
                    >
                </div>
            </div>
        </header>

        <!-- CONTENIDO PRINCIPAL -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 py-6">
            <div
                v-if="error"
                class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6 flex items-center justify-between"
            >
                <span>{{ error }}</span>
                <button
                    @click="cargarDatosIniciales"
                    class="text-sm font-semibold underline"
                >
                    Reintentar
                </button>
            </div>

            <!-- Loading -->
            <div
                v-if="loading"
                :class="
                    viewMode === 'grid'
                        ? 'grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 xl:grid-cols-5 gap-5'
                        : 'space-y-3'
                "
            >
                <div v-for="i in 10" :key="i" class="animate-pulse">
                    <div
                        v-if="viewMode === 'grid'"
                        class="bg-gray-200 rounded-xl h-48"
                    ></div>
                    <div class="mt-3 space-y-2">
                        <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                        <div class="h-3 bg-gray-100 rounded w-full"></div>
                    </div>
                </div>
            </div>

            <!-- Mensaje si es cliente y no hay contenido visible -->
            <div
                v-else-if="
                    esRolCliente() &&
                    filtrados.length === 0 &&
                    !loading &&
                    !error &&
                    tutoriales.length > 0
                "
                class="text-center py-20"
            >
                <div class="text-6xl mb-4">🔒</div>
                <h3 class="text-xl font-semibold text-gray-800">
                    Contenido exclusivo para We Collab
                </h3>
                <p class="text-gray-500 mt-2">
                    Los materiales que buscas son parte del ecosistema interno.
                </p>
                <p class="text-sm text-gray-400 mt-4">
                    Tu rol: {{ getUserRole() }}
                </p>
            </div>

            <!-- VISTA GRID -->
            <div
                v-else-if="
                    viewMode === 'grid' &&
                    (materialesAccesibles.length > 0 ||
                        Object.keys(materialesPorCategoria).length > 0)
                "
                class="space-y-8"
            >
                <!-- 🌟 SECCIÓN DESTACADOS -->
                <div v-if="materialesAccesibles.length > 0" class="space-y-4">
                    <div
                        class="flex items-center justify-between border-b-2 border-indigo-200 pb-2"
                    >
                        <div class="flex items-center gap-2">
                            <span class="text-2xl">🌟</span>
                            <h2
                                class="text-xl font-bold text-indigo-700 uppercase tracking-wide"
                            >
                                Destacados para ti
                            </h2>
                            <span
                                class="text-sm text-indigo-500 bg-indigo-100 px-2 py-0.5 rounded-full"
                            >
                                {{ materialesAccesibles.length }}
                            </span>
                        </div>
                    </div>

                    <div
                        class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 xl:grid-cols-5 gap-5"
                    >
                        <article
                            v-for="recurso in materialesAccesibles.slice(0, 10)"
                            :key="recurso.id"
                            class="group bg-white rounded-xl overflow-hidden shadow-md hover:shadow-xl transition-all duration-300 border border-indigo-100 hover:-translate-y-1 ring-1 ring-indigo-200"
                        >
                            <div
                                class="relative h-48 overflow-hidden bg-gray-100"
                            >
                                <!-- Thumbnail mejorado para documentos -->
                                <img
                                    v-if="esVideo(recurso.url)"
                                    :src="getThumbnailVideo(recurso.url)"
                                    :alt="recurso.titulo"
                                    class="w-full h-full object-cover group-hover:scale-110 transition duration-500"
                                    @error="
                                        $event.target.src =
                                            getPlaceholderPorTipo(
                                                recurso.tipo_material,
                                            )
                                    "
                                />
                                <div
                                    v-else
                                    :class="
                                        getPlaceholderBgClass(
                                            recurso.tipo_material,
                                        )
                                    "
                                    class="w-full h-full flex flex-col items-center justify-center group-hover:scale-110 transition duration-500"
                                >
                                    <span
                                        class="text-6xl mb-3 drop-shadow-lg"
                                        >{{
                                            getIconoPorTipo(
                                                recurso.tipo_material,
                                            )
                                        }}</span
                                    >
                                    <span
                                        class="text-white text-base font-bold px-4 text-center uppercase tracking-wide"
                                        >{{
                                            getNombreTipo(recurso.tipo_material)
                                        }}</span
                                    >
                                    <div class="mt-2 flex gap-1">
                                        <span class="text-white/60 text-[10px]"
                                            >●</span
                                        >
                                        <span class="text-white/60 text-[10px]"
                                            >●</span
                                        >
                                        <span class="text-white/60 text-[10px]"
                                            >●</span
                                        >
                                    </div>
                                    <span
                                        class="text-white/80 text-[11px] mt-2 line-clamp-1 max-w-[90%] font-medium"
                                        >{{ recurso.titulo }}</span
                                    >
                                </div>

                                <span
                                    class="absolute top-2 left-2 bg-gradient-to-r from-amber-500 to-yellow-500 text-white text-[10px] px-2 py-1 rounded-full z-20"
                                >
                                    ⭐ Destacado
                                </span>
                                <span
                                    :class="[
                                        'absolute top-2 right-2 text-white text-[10px] px-2 py-1 rounded-full z-20 font-semibold shadow-sm',
                                        getTipoBadgeClass(
                                            recurso.tipo_material,
                                        ),
                                    ]"
                                >
                                    {{ getIconoPorTipo(recurso.tipo_material) }}
                                    {{ recurso.tipo_material }}
                                </span>
                            </div>
                            <div class="p-4">
                                <h3
                                    class="text-sm font-bold line-clamp-1 text-gray-800"
                                >
                                    {{ recurso.titulo }}
                                </h3>
                                <p
                                    class="text-xs text-gray-500 line-clamp-2 mt-2 leading-relaxed"
                                >
                                    {{
                                        recurso.descripcion || "Sin descripción"
                                    }}
                                </p>
                                <div
                                    class="flex items-center justify-between mt-4 pt-2 border-t border-gray-100"
                                >
                                    <span
                                        class="text-[10px] text-gray-400 flex items-center gap-1"
                                    >
                                        <span>📅</span>
                                        {{
                                            new Date(
                                                recurso.created_at,
                                            ).toLocaleDateString("es-ES")
                                        }}
                                    </span>
                                    <!-- <span
                                        v-if="recurso.alcance"
                                        :class="
                                            getAlcanceBadgeClass(
                                                recurso.alcance,
                                            )
                                        "
                                        class="text-[10px] px-2 py-1 rounded-full font-medium"
                                    >
                                        {{ recurso.alcance }}
                                    </span> -->
                                </div>
                                <div class="mt-4">
                                    <button
                                        @click.stop="verVideo(recurso)"
                                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-white text-xs font-semibold transition-all transform hover:scale-105"
                                        :class="
                                            getBotonGradiente(
                                                recurso.tipo_material,
                                            )
                                        "
                                    >
                                        <span>{{
                                            esVideo(recurso.url)
                                                ? "🎬 Ver video"
                                                : "📄 Ver documento"
                                        }}</span>
                                        <span>→</span>
                                    </button>
                                </div>
                            </div>
                        </article>
                    </div>
                </div>

                <div
                    v-if="materialesAccesibles.length > 0"
                    class="border-t-2 border-gray-200 my-4"
                ></div>

                <!-- ORGANIZACIÓN POR CATEGORÍA Y SUBCATEGORÍA - GRID -->
                <div
                    v-for="(categoria, catIndex) in categoriasConContenido"
                    :key="catIndex"
                    class="space-y-4"
                >
                    <div
                        class="flex items-center justify-between border-b-2 border-indigo-200 pb-2 bg-gradient-to-r from-indigo-50 to-transparent px-3 py-2 rounded-lg"
                    >
                        <div class="flex items-center gap-2">
                            <span class="text-2xl">{{ categoria.icono }}</span>
                            <h2
                                class="text-xl font-bold text-indigo-700 uppercase tracking-wide"
                            >
                                {{ categoria.nombre }}
                            </h2>
                            <span
                                class="text-sm text-indigo-500 bg-indigo-100 px-2 py-0.5 rounded-full"
                            >
                                {{ categoria.totalMateriales }}
                            </span>
                        </div>
                    </div>

                    <div
                        v-for="subcategoria in categoria.subcategorias"
                        :key="subcategoria.id"
                        class="space-y-3 ml-4"
                    >
                        <div
                            class="flex items-center gap-2 border-l-4 border-indigo-300 pl-3 py-1"
                        >
                            <span class="text-lg">📁</span>
                            <h3
                                class="text-md font-semibold text-gray-700 uppercase tracking-wide"
                            >
                                {{ subcategoria.nombre }}
                            </h3>
                            <span
                                class="text-xs text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full"
                            >
                                {{ subcategoria.items.length }}
                            </span>
                        </div>

                        <div
                            class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 xl:grid-cols-5 gap-5 pl-4"
                        >
                            <article
                                v-for="recurso in subcategoria.items"
                                :key="recurso.id"
                                class="group bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 hover:-translate-y-1"
                            >
                                <div
                                    class="relative h-48 overflow-hidden bg-gray-100"
                                >
                                    <img
                                        v-if="esVideo(recurso.url)"
                                        :src="getThumbnailVideo(recurso.url)"
                                        :alt="recurso.titulo"
                                        class="w-full h-full object-cover group-hover:scale-110 transition duration-500"
                                        @error="
                                            $event.target.src =
                                                getPlaceholderPorTipo(
                                                    recurso.tipo_material,
                                                )
                                        "
                                    />
                                    <div
                                        v-else
                                        :class="
                                            getPlaceholderBgClass(
                                                recurso.tipo_material,
                                            )
                                        "
                                        class="w-full h-full flex flex-col items-center justify-center group-hover:scale-110 transition duration-500"
                                    >
                                        <span
                                            class="text-6xl mb-3 drop-shadow-lg"
                                            >{{
                                                getIconoPorTipo(
                                                    recurso.tipo_material,
                                                )
                                            }}</span
                                        >
                                        <span
                                            class="text-white text-base font-bold px-4 text-center uppercase tracking-wide"
                                            >{{
                                                getNombreTipo(
                                                    recurso.tipo_material,
                                                )
                                            }}</span
                                        >
                                        <div class="mt-2 flex gap-1">
                                            <span
                                                class="text-white/60 text-[10px]"
                                                >●</span
                                            >
                                            <span
                                                class="text-white/60 text-[10px]"
                                                >●</span
                                            >
                                            <span
                                                class="text-white/60 text-[10px]"
                                                >●</span
                                            >
                                        </div>
                                        <span
                                            class="text-white/80 text-[11px] mt-2 line-clamp-1 max-w-[90%] font-medium"
                                            >{{ recurso.titulo }}</span
                                        >
                                    </div>

                                    <div
                                        v-if="estaBloqueado(recurso)"
                                        class="absolute inset-0 bg-black/70 backdrop-blur-sm flex flex-col items-center justify-center z-10"
                                    >
                                        <span class="text-4xl mb-2">🔒</span>
                                        <span
                                            class="text-white text-xs font-medium text-center px-3"
                                            >Contenido restringido</span
                                        >
                                        <span
                                            class="text-white/70 text-[10px] mt-1"
                                            >{{ recurso.alcance }}</span
                                        >
                                    </div>

                                    <span
                                        :class="[
                                            'absolute top-2 left-2 text-white text-[10px] px-2 py-1 rounded-full z-20 font-semibold shadow-sm',
                                            getTipoBadgeClass(
                                                recurso.tipo_material,
                                            ),
                                        ]"
                                    >
                                        {{
                                            getIconoPorTipo(
                                                recurso.tipo_material,
                                            )
                                        }}
                                        {{ recurso.tipo_material }}
                                    </span>
                                </div>

                                <div class="p-4">
                                    <h3
                                        class="text-sm font-bold line-clamp-1"
                                        :class="
                                            estaBloqueado(recurso)
                                                ? 'text-gray-400'
                                                : 'text-gray-800'
                                        "
                                    >
                                        {{ recurso.titulo }}
                                    </h3>
                                    <p
                                        class="text-xs text-gray-500 line-clamp-2 mt-2 leading-relaxed"
                                    >
                                        {{
                                            recurso.descripcion ||
                                            "Sin descripción"
                                        }}
                                    </p>
                                    <div
                                        class="flex items-center justify-between mt-4 pt-2 border-t border-gray-100"
                                    >
                                        <span
                                            class="text-[10px] text-gray-400 flex items-center gap-1"
                                        >
                                            <span>📅</span>
                                            {{
                                                new Date(
                                                    recurso.created_at,
                                                ).toLocaleDateString("es-ES")
                                            }}
                                        </span>
                                        <span
                                            class="text-[10px] text-gray-400 flex items-center gap-1"
                                        >
                                            <span>👁️</span>
                                            {{ recurso.visitas || 0 }} vistas
                                        </span>
                                    </div>

                                    <div class="mt-4">
                                        <button
                                            @click.stop="verVideo(recurso)"
                                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-white text-xs font-semibold transition-all transform hover:scale-105"
                                            :class="
                                                estaBloqueado(recurso)
                                                    ? 'bg-gray-400 cursor-not-allowed'
                                                    : getBotonGradiente(
                                                          recurso.tipo_material,
                                                      )
                                            "
                                            :disabled="estaBloqueado(recurso)"
                                        >
                                            <span>{{
                                                estaBloqueado(recurso)
                                                    ? "🔒 Sin acceso"
                                                    : esVideo(recurso.url)
                                                      ? "🎬 Ver video"
                                                      : "📄 Ver documento"
                                            }}</span>
                                            <span v-if="!estaBloqueado(recurso)"
                                                >→</span
                                            >
                                        </button>
                                    </div>
                                </div>
                            </article>
                        </div>
                    </div>
                </div>
            </div>

            <!-- VISTA LISTA -->
            <div
                v-else-if="
                    viewMode === 'list' &&
                    (materialesAccesibles.length > 0 ||
                        Object.keys(materialesPorCategoria).length > 0)
                "
                class="space-y-8"
            >
                <!-- 🌟 SECCIÓN DESTACADOS - LISTA -->
                <div v-if="materialesAccesibles.length > 0" class="space-y-4">
                    <div
                        class="flex items-center justify-between border-b-2 border-indigo-200 pb-2"
                    >
                        <div class="flex items-center gap-2">
                            <span class="text-2xl">🌟</span>
                            <h2
                                class="text-xl font-bold text-indigo-700 uppercase tracking-wide"
                            >
                                Destacados para ti
                            </h2>
                            <span
                                class="text-sm text-indigo-500 bg-indigo-100 px-2 py-0.5 rounded-full"
                            >
                                {{ materialesAccesibles.length }}
                            </span>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <div
                            v-for="recurso in materialesAccesibles.slice(0, 10)"
                            :key="recurso.id"
                            class="group bg-white rounded-xl overflow-hidden shadow-md hover:shadow-lg transition-all border border-indigo-100 hover:border-indigo-200"
                        >
                            <div class="flex items-center gap-5 p-4">
                                <div
                                    class="relative w-16 h-16 rounded-lg overflow-hidden flex-shrink-0"
                                >
                                    <img
                                        v-if="esVideo(recurso.url)"
                                        :src="getThumbnailVideo(recurso.url)"
                                        :alt="recurso.titulo"
                                        class="w-full h-full object-cover"
                                        @error="
                                            $event.target.src =
                                                getPlaceholderPorTipo(
                                                    recurso.tipo_material,
                                                )
                                        "
                                    />
                                    <div
                                        v-else
                                        :class="
                                            getPlaceholderBgClass(
                                                recurso.tipo_material,
                                            )
                                        "
                                        class="w-full h-full flex items-center justify-center"
                                    >
                                        <span class="text-2xl">{{
                                            getIconoPorTipo(
                                                recurso.tipo_material,
                                            )
                                        }}</span>
                                    </div>
                                    <span
                                        class="absolute top-1 left-1 bg-gradient-to-r from-amber-500 to-yellow-500 text-white text-[8px] px-1 py-0.5 rounded-full"
                                    >
                                        ⭐
                                    </span>
                                </div>

                                <div class="flex-1 min-w-0">
                                    <div
                                        class="flex items-center gap-2 flex-wrap"
                                    >
                                        <h3
                                            class="text-sm font-bold text-gray-800"
                                        >
                                            {{ recurso.titulo }}
                                        </h3>
                                        <span
                                            :class="[
                                                'text-[10px] px-2 py-0.5 rounded-full font-medium',
                                                getTipoBadgeClass(
                                                    recurso.tipo_material,
                                                ),
                                            ]"
                                        >
                                            {{
                                                getIconoPorTipo(
                                                    recurso.tipo_material,
                                                )
                                            }}
                                            {{ recurso.tipo_material }}
                                        </span>
                                    </div>
                                    <p
                                        class="text-xs text-gray-500 line-clamp-1 mt-1"
                                    >
                                        {{
                                            recurso.descripcion ||
                                            "Sin descripción"
                                        }}
                                    </p>
                                    <div
                                        class="flex items-center gap-4 mt-2 text-[10px] text-gray-400"
                                    >
                                        <span class="flex items-center gap-1"
                                            >📅
                                            {{
                                                new Date(
                                                    recurso.created_at,
                                                ).toLocaleDateString("es-ES")
                                            }}</span
                                        >
                                        <span class="flex items-center gap-1"
                                            >👁️
                                            {{ recurso.visitas || 0 }}
                                            vistas</span
                                        >
                                        <span class="text-green-600"
                                            >✅ Accesible</span
                                        >
                                    </div>
                                </div>

                                <div class="flex flex-col items-end gap-2">
                                    <span
                                        v-if="recurso.alcance"
                                        :class="
                                            getAlcanceBadgeClass(
                                                recurso.alcance,
                                            )
                                        "
                                        class="text-[10px] px-2 py-1 rounded-full font-medium"
                                    >
                                        {{ recurso.alcance }}
                                    </span>
                                    <button
                                        @click.stop="verVideo(recurso)"
                                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-white text-xs font-semibold transition-all"
                                        :class="
                                            getBotonGradiente(
                                                recurso.tipo_material,
                                            )
                                        "
                                    >
                                        {{
                                            esVideo(recurso.url)
                                                ? "🎬 Ver video"
                                                : "📄 Abrir documento"
                                        }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    v-if="materialesAccesibles.length > 0"
                    class="border-t-2 border-gray-200 my-4"
                ></div>

                <!-- ORGANIZACIÓN POR CATEGORÍA Y SUBCATEGORÍA - LISTA -->
                <div
                    v-for="(categoria, catIndex) in categoriasConContenido"
                    :key="catIndex"
                    class="space-y-4"
                >
                    <div
                        class="flex items-center justify-between border-b-2 border-indigo-200 pb-2 bg-gradient-to-r from-indigo-50 to-transparent px-3 py-2 rounded-lg"
                    >
                        <div class="flex items-center gap-2">
                            <span class="text-2xl">{{ categoria.icono }}</span>
                            <h2
                                class="text-xl font-bold text-indigo-700 uppercase tracking-wide"
                            >
                                {{ categoria.nombre }}
                            </h2>
                            <span
                                class="text-sm text-indigo-500 bg-indigo-100 px-2 py-0.5 rounded-full"
                            >
                                {{ categoria.totalMateriales }}
                            </span>
                        </div>
                    </div>

                    <div
                        v-for="subcategoria in categoria.subcategorias"
                        :key="subcategoria.id"
                        class="space-y-3 ml-4"
                    >
                        <div
                            class="flex items-center gap-2 border-l-4 border-indigo-300 pl-3 py-1"
                        >
                            <span class="text-lg">📁</span>
                            <h3
                                class="text-md font-semibold text-gray-700 uppercase tracking-wide"
                            >
                                {{ subcategoria.nombre }}
                            </h3>
                            <span
                                class="text-xs text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full"
                            >
                                {{ subcategoria.items.length }}
                            </span>
                        </div>

                        <div class="space-y-2 pl-4">
                            <div
                                v-for="recurso in subcategoria.items"
                                :key="recurso.id"
                                class="group bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-all border border-gray-100 hover:border-indigo-200"
                            >
                                <div class="flex items-center gap-5 p-4">
                                    <div
                                        class="relative w-16 h-16 rounded-lg overflow-hidden flex-shrink-0"
                                    >
                                        <img
                                            v-if="esVideo(recurso.url)"
                                            :src="
                                                getThumbnailVideo(recurso.url)
                                            "
                                            :alt="recurso.titulo"
                                            class="w-full h-full object-cover"
                                            @error="
                                                $event.target.src =
                                                    getPlaceholderPorTipo(
                                                        recurso.tipo_material,
                                                    )
                                            "
                                        />
                                        <div
                                            v-else
                                            :class="
                                                getPlaceholderBgClass(
                                                    recurso.tipo_material,
                                                )
                                            "
                                            class="w-full h-full flex items-center justify-center"
                                        >
                                            <span class="text-2xl">{{
                                                getIconoPorTipo(
                                                    recurso.tipo_material,
                                                )
                                            }}</span>
                                        </div>
                                        <div
                                            v-if="estaBloqueado(recurso)"
                                            class="absolute inset-0 bg-black/60 flex items-center justify-center rounded-lg"
                                        >
                                            <span class="text-white text-sm"
                                                >🔒</span
                                            >
                                        </div>
                                    </div>

                                    <div class="flex-1 min-w-0">
                                        <div
                                            class="flex items-center gap-2 flex-wrap"
                                        >
                                            <h3
                                                class="text-sm font-bold"
                                                :class="
                                                    estaBloqueado(recurso)
                                                        ? 'text-gray-400'
                                                        : 'text-gray-800'
                                                "
                                            >
                                                {{ recurso.titulo }}
                                            </h3>
                                            <span
                                                :class="[
                                                    'text-[10px] px-2 py-0.5 rounded-full font-medium',
                                                    getTipoBadgeClass(
                                                        recurso.tipo_material,
                                                    ),
                                                ]"
                                            >
                                                {{
                                                    getIconoPorTipo(
                                                        recurso.tipo_material,
                                                    )
                                                }}
                                                {{ recurso.tipo_material }}
                                            </span>
                                        </div>
                                        <p
                                            class="text-xs text-gray-500 line-clamp-1 mt-1"
                                        >
                                            {{
                                                recurso.descripcion ||
                                                "Sin descripción"
                                            }}
                                        </p>
                                        <div
                                            class="flex items-center gap-4 mt-2 text-[10px] text-gray-400"
                                        >
                                            <span
                                                class="flex items-center gap-1"
                                                >📅
                                                {{
                                                    new Date(
                                                        recurso.created_at,
                                                    ).toLocaleDateString(
                                                        "es-ES",
                                                    )
                                                }}</span
                                            >
                                            <span
                                                class="flex items-center gap-1"
                                                >👁️
                                                {{ recurso.visitas || 0 }}
                                                vistas</span
                                            >
                                            <span
                                                v-if="estaBloqueado(recurso)"
                                                class="text-amber-600 flex items-center gap-1"
                                            >
                                                🔒 Requiere:
                                                {{ recurso.alcance }}
                                            </span>
                                            <span
                                                v-else
                                                class="text-green-600 flex items-center gap-1"
                                                >✅ Accesible</span
                                            >
                                        </div>
                                    </div>

                                    <div class="flex flex-col items-end gap-2">
                                        <span
                                            v-if="recurso.alcance"
                                            :class="
                                                getAlcanceBadgeClass(
                                                    recurso.alcance,
                                                )
                                            "
                                            class="text-[10px] px-2 py-1 rounded-full font-medium"
                                        >
                                            {{ recurso.alcance }}
                                        </span>
                                        <button
                                            @click.stop="verVideo(recurso)"
                                            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-white text-xs font-semibold transition-all"
                                            :class="
                                                estaBloqueado(recurso)
                                                    ? 'bg-gray-400 cursor-not-allowed'
                                                    : getBotonGradiente(
                                                          recurso.tipo_material,
                                                      )
                                            "
                                            :disabled="estaBloqueado(recurso)"
                                        >
                                            {{
                                                estaBloqueado(recurso)
                                                    ? "🔒 Sin acceso"
                                                    : esVideo(recurso.url)
                                                      ? "🎬 Ver video"
                                                      : "📄 Abrir documento"
                                            }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Estados vacíos -->
            <div
                v-if="
                    !loading &&
                    !error &&
                    Object.keys(materialesPorCategoria).length === 0 &&
                    materialesAccesibles.length === 0 &&
                    searchDebounced
                "
                class="text-center py-20"
            >
                <div class="text-6xl mb-4">🔍</div>
                <p class="text-gray-600">No se encontró "{{ search }}"</p>
                <button
                    @click="resetFiltros"
                    class="mt-3 text-indigo-600 text-sm hover:underline"
                >
                    Limpiar filtros
                </button>
            </div>

            <div
                v-if="!loading && !error && tutoriales.length === 0"
                class="text-center py-20"
            >
                <div class="text-6xl mb-4">📚</div>
                <p class="text-gray-600">Aún no hay contenido disponible</p>
            </div>
        </main>

        <!-- Toast -->
        <Teleport to="body">
            <Transition name="toast">
                <div
                    v-if="showToast"
                    :class="[
                        'fixed bottom-4 right-4 z-50 flex items-center gap-2 px-4 py-3 rounded-lg shadow-lg',
                        toastType === 'success'
                            ? 'bg-green-500 text-white'
                            : 'bg-red-500 text-white',
                    ]"
                >
                    <span>{{ toastType === "success" ? "✅" : "⚠️" }}</span>
                    <span class="text-sm">{{ toastMessage }}</span>
                </div>
            </Transition>
        </Teleport>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from "vue";
import { usePage, router } from "@inertiajs/vue3";

const axios = window.axios;

// 🔷 ESTADO GLOBAL
const page = usePage();
const user = computed(() => page.props.auth?.user);
const tutoriales = ref([]);
const categorias = ref([]);
const subcategorias = ref([]);
const loading = ref(true);
const error = ref(null);

// 🔷 FILTROS Y UI
const search = ref("");
const searchDebounced = ref("");
const tipoSeleccionado = ref("todo");
const viewMode = ref(localStorage.getItem("tutorialViewMode") || "grid");
const showToast = ref(false);
const toastMessage = ref("");
const toastType = ref("success");
const categoriaSeleccionada = ref("");
const subcategoriaSeleccionada = ref("");

// 🔷 TIPOS DE MATERIAL CON ICONOS MEJORADOS
const tipos = [
    { label: "Todo", value: "todo", icon: "📚" },
    { label: "Video", value: "video", icon: "🎬" },
    { label: "Manual", value: "manual", icon: "📘" },
    { label: "Guía", value: "guia", icon: "🗺️" },
    { label: "Infografía", value: "triptico", icon: "📊" },
];

// ============================================
// 🎯 ECOSISTEMAS Y JERARQUÍA DE ROLES
// ============================================

const ROLES_WE_COLLAB = [
    "Superadmin we collab",
    "Admin we collab",
    "Soporte we collab",
    "Usuario we collab",
];

const ROLES_CLIENTE = [
    "Suscriptor SLC",
    "Usuario Admin SLC",
    "Usuario Premium SLC",
    "Usuario Público",
    "Prospecto",
    "Invitado",
];

const JERARQUIA_ROLES = {
    "Superadmin we collab": 100,
    "Admin we collab": 90,
    "Soporte we collab": 80,
    "Usuario we collab": 70,
    "Suscriptor SLC": 60,
    "Usuario Admin SLC": 50,
    "Usuario Premium SLC": 40,
    "Usuario Público": 30,
    Prospecto: 20,
    Invitado: 10,
};

const MAPEO_ROLES = {
    "Superadmin we collab": "Superadmin we collab",
    "Admin we collab": "Admin we collab",
    "Admin We collab": "Admin we collab",
    "Soporte we collab": "Soporte we collab",
    "Usuario we collab": "Usuario we collab",
    "Suscriptor SLC": "Suscriptor SLC",
    "Usuario Admin SLC": "Usuario Admin SLC",
    "Usuario Premium SLC": "Usuario Premium SLC",
    "Usuario Público": "Usuario Público",
    "Usuario Publico": "Usuario Público",
    Prospecto: "Prospecto",
    Invitado: "Invitado",
    "Cliente Admin": "Usuario Admin SLC",
    "Cliente Premium": "Usuario Premium SLC",
};

const ALCANCES_WE_COLLAB = [...ROLES_WE_COLLAB];
const ALCANCES_CLIENTE = [...ROLES_CLIENTE.filter((r) => r !== "Invitado")];
const ALCANCES_VALIDOS = [...ALCANCES_WE_COLLAB, ...ALCANCES_CLIENTE];

const normalize = (str) => str?.toString().toLowerCase().trim() || "";

const getUserRole = () => {
    const role = user.value?.role;
    if (!role) return "Invitado";
    if (typeof role === "object") {
        const roleName =
            role.nombre?.toString() ||
            role.name?.toString() ||
            role.slug?.toString() ||
            role.role?.toString() ||
            "";
        return MAPEO_ROLES[roleName.trim()] || roleName.trim() || "Invitado";
    }
    return (
        MAPEO_ROLES[role.toString().trim()] ||
        role.toString().trim() ||
        "Invitado"
    );
};

const getRolLevel = (rolNombre) => JERARQUIA_ROLES[rolNombre] || 0;

const esRolWeCollab = () => ROLES_WE_COLLAB.includes(getUserRole());
const esRolCliente = () => ROLES_CLIENTE.includes(getUserRole());

const getEcosistema = () => {
    if (esRolWeCollab()) return "we_collab";
    if (esRolCliente()) return "cliente";
    return "desconocido";
};

const getAlcanceEcosistema = (alcance) => {
    if (ALCANCES_WE_COLLAB.includes(alcance)) return "we_collab";
    if (ALCANCES_CLIENTE.includes(alcance)) return "cliente";
    return "publico";
};

const debeOcultarCompletamente = (tutorial) => {
    const alcanceTutorial = tutorial.alcance;
    if (esRolCliente()) {
        if (!alcanceTutorial || alcanceTutorial.trim() === "") return false;
        const alcanceNorm = MAPEO_ROLES[alcanceTutorial] || alcanceTutorial;
        const ecosistemaAlcance = getAlcanceEcosistema(alcanceNorm);
        return ecosistemaAlcance === "we_collab";
    }
    return false;
};

const tieneAcceso = (tutorial) => {
    const rolUsuario = getUserRole();
    let alcanceTutorial = tutorial.alcance;

    if (!alcanceTutorial || alcanceTutorial.trim() === "") return true;
    alcanceTutorial = MAPEO_ROLES[alcanceTutorial] || alcanceTutorial;
    if (!ALCANCES_VALIDOS.includes(alcanceTutorial)) return false;

    const ecosistemaUsuario = getEcosistema();
    const ecosistemaAlcance = getAlcanceEcosistema(alcanceTutorial);

    if (ecosistemaUsuario === "cliente" && ecosistemaAlcance === "we_collab") {
        return false;
    }

    if (rolUsuario === "Superadmin we collab") return true;
    if (rolUsuario === "Admin we collab") return true;

    return getRolLevel(rolUsuario) >= getRolLevel(alcanceTutorial);
};

const estaBloqueado = (tutorial) => {
    const alcanceTutorial = tutorial.alcance;
    if (!alcanceTutorial || alcanceTutorial.trim() === "") return false;
    const alcanceNorm = MAPEO_ROLES[alcanceTutorial] || alcanceTutorial;
    const ecosistemaUsuario = getEcosistema();
    const ecosistemaAlcance = getAlcanceEcosistema(alcanceNorm);
    if (ecosistemaUsuario === "cliente" && ecosistemaAlcance === "we_collab") {
        return true;
    }
    return !tieneAcceso(tutorial);
};

const filtrados = computed(() => {
    return tutoriales.value.filter((t) => {
        if (debeOcultarCompletamente(t)) return false;
        if (t.estado !== "activo") return false;
        const tipoOK =
            tipoSeleccionado.value === "todo" ||
            normalize(t.tipo_material) === normalize(tipoSeleccionado.value);
        if (!tipoOK) return false;
        const searchTerm = searchDebounced.value.toLowerCase().trim();
        const searchOK =
            !searchTerm ||
            t.titulo?.toLowerCase().includes(searchTerm) ||
            t.descripcion?.toLowerCase().includes(searchTerm);
        return searchOK;
    });
});

const materialesAccesibles = computed(() => {
    return filtrados.value.filter((t) => tieneAcceso(t));
});

const materialesPorCategoria = computed(() => {
    const resultado = {};
    let materialesFiltrados = filtrados.value;

    if (categoriaSeleccionada.value) {
        materialesFiltrados = materialesFiltrados.filter(
            (t) => t.categoria_id == categoriaSeleccionada.value,
        );
    }
    if (subcategoriaSeleccionada.value) {
        materialesFiltrados = materialesFiltrados.filter(
            (t) => t.subcategoria_id == subcategoriaSeleccionada.value,
        );
    }

    materialesFiltrados.forEach((tutorial) => {
        const categoriaId = tutorial.categoria_id || "sin_categoria";
        const categoriaNombre = tutorial.categoria?.nombre || "Sin categoría";
        const subcategoriaId = tutorial.subcategoria_id || "sin_subcategoria";
        const subcategoriaNombre =
            tutorial.subcategoria?.nombre || "Sin subcategoría";

        if (!resultado[categoriaId]) {
            resultado[categoriaId] = {
                id: categoriaId,
                nombre: categoriaNombre,
                icono: "📚",
                subcategorias: {},
            };
        }
        if (!resultado[categoriaId].subcategorias[subcategoriaId]) {
            resultado[categoriaId].subcategorias[subcategoriaId] = {
                id: subcategoriaId,
                nombre: subcategoriaNombre,
                items: [],
            };
        }
        resultado[categoriaId].subcategorias[subcategoriaId].items.push(
            tutorial,
        );
    });

    return Object.values(resultado).map((cat) => ({
        ...cat,
        subcategorias: Object.values(cat.subcategorias),
        totalMateriales: Object.values(cat.subcategorias).reduce(
            (total, sub) => total + sub.items.length,
            0,
        ),
    }));
});

const categoriasConContenido = computed(() => {
    return materialesPorCategoria.value.filter(
        (cat) => cat.totalMateriales > 0,
    );
});

const subcategoriasFiltradas = computed(() => {
    if (!categoriaSeleccionada.value) return [];
    return subcategorias.value.filter(
        (sub) => sub.categorias_id == categoriaSeleccionada.value,
    );
});

const stats = computed(() => ({
    total: tutoriales.value.length,
    visibles: filtrados.value.length,
    accesibles: filtrados.value.filter((t) => tieneAcceso(t)).length,
    restringidos: filtrados.value.filter((t) => estaBloqueado(t)).length,
}));

// ============================================
// 🖼️ FUNCIONES MEJORADAS PARA THUMBNAILS Y PLACEHOLDERS
// ============================================

const getIconoPorTipo = (tipo) => {
    const iconos = {
        video: "🎬",
        manual: "📘",
        guia: "🗺️",
        triptico: "📊",
        default: "📄",
    };
    return iconos[tipo] || iconos.default;
};

const getNombreTipo = (tipo) => {
    const nombres = {
        video: "VIDEO TUTORIAL",
        manual: "MANUAL TÉCNICO",
        guia: "GUÍA PRÁCTICA",
        triptico: "INFOGRAFÍA",
        default: "DOCUMENTO",
    };
    return nombres[tipo] || nombres.default;
};

const getBotonGradiente = (tipo) => {
    const gradientes = {
        video: "bg-gradient-to-r from-rose-500 to-rose-600 hover:from-rose-600 hover:to-rose-700",
        manual: "bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700",
        guia: "bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700",
        triptico:
            "bg-gradient-to-r from-violet-500 to-violet-600 hover:from-violet-600 hover:to-violet-700",
        default:
            "bg-gradient-to-r from-indigo-500 to-indigo-600 hover:from-indigo-600 hover:to-indigo-700",
    };
    return gradientes[tipo] || gradientes.default;
};

const esVideo = (url) => {
    if (!url) return false;
    return url.includes("youtube.com") || url.includes("youtu.be");
};

const getThumbnailVideo = (url) => {
    if (!url) return null;
    let videoId = null;
    if (url.includes("youtu.be/")) {
        videoId = url.split("youtu.be/")[1]?.split(/[?&#]/)[0];
    } else if (url.includes("v=")) {
        videoId = url.split("v=")[1]?.split(/[?&#]/)[0];
    } else if (url.includes("/embed/")) {
        videoId = url.split("/embed/")[1]?.split(/[?&#]/)[0];
    }
    if (videoId && videoId.length === 11) {
        return `https://img.youtube.com/vi/${videoId}/hqdefault.jpg`;
    }
    return null;
};

const getPlaceholderBgClass = (tipo) => {
    const classes = {
        video: "bg-gradient-to-br from-rose-500 via-rose-600 to-rose-700",
        manual: "bg-gradient-to-br from-blue-500 via-blue-600 to-blue-700",
        guia: "bg-gradient-to-br from-emerald-500 via-emerald-600 to-emerald-700",
        triptico:
            "bg-gradient-to-br from-violet-500 via-violet-600 to-violet-700",
    };
    return (
        classes[tipo] ||
        "bg-gradient-to-br from-gray-500 via-gray-600 to-gray-700"
    );
};

const getPlaceholderPorTipo = (tipo) => {
    const placeholders = {
        video: "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 400 300'%3E%3Crect width='400' height='300' fill='%23ef4444'/%3E%3Ctext x='200' y='140' text-anchor='middle' fill='white' font-size='64'%3E🎬%3C/text%3E%3Ctext x='200' y='200' text-anchor='middle' fill='white' font-size='20' font-weight='bold'%3EVIDEO%3C/text%3E%3C/svg%3E",
        manual: "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 400 300'%3E%3Crect width='400' height='300' fill='%233b82f6'/%3E%3Ctext x='200' y='140' text-anchor='middle' fill='white' font-size='64'%3E📘%3C/text%3E%3Ctext x='200' y='200' text-anchor='middle' fill='white' font-size='20' font-weight='bold'%3EMANUAL%3C/text%3E%3C/svg%3E",
        guia: "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 400 300'%3E%3Crect width='400' height='300' fill='%2310b981'/%3E%3Ctext x='200' y='140' text-anchor='middle' fill='white' font-size='64'%3E🗺️%3C/text%3E%3Ctext x='200' y='200' text-anchor='middle' fill='white' font-size='20' font-weight='bold'%3EGUÍA%3C/text%3E%3C/svg%3E",
        triptico:
            "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 400 300'%3E%3Crect width='400' height='300' fill='%238b5cf6'/%3E%3Ctext x='200' y='140' text-anchor='middle' fill='white' font-size='64'%3E📊%3C/text%3E%3Ctext x='200' y='200' text-anchor='middle' fill='white' font-size='20' font-weight='bold'%3EINFOGRAFÍA%3C/text%3E%3C/svg%3E",
    };
    return placeholders[tipo] || placeholders.manual;
};

// ============================================
// CARGA DE DATOS
// ============================================

const cargarCategorias = async () => {
    try {
        const res = await axios.get("/categorias/lista");
        if (res.data?.success) categorias.value = res.data.data;
    } catch (err) {
        console.error("Error cargando categorías:", err);
    }
};

const cargarSubcategorias = async () => {
    try {
        const params = {};
        if (categoriaSeleccionada.value)
            params.categorias_id = categoriaSeleccionada.value;
        const res = await axios.get("/subcategorias/all", { params });
        if (res.data?.success) subcategorias.value = res.data.data;
    } catch (err) {
        console.error("Error cargando subcategorías:", err);
    }
};

const cargarTutoriales = async () => {
    try {
        error.value = null;
        loading.value = true;
        const res = await axios.get("/tutoriales", { timeout: 10000 });
        if (res.data?.data && Array.isArray(res.data.data)) {
            tutoriales.value = res.data.data;
        } else if (Array.isArray(res.data)) {
            tutoriales.value = res.data;
        } else {
            throw new Error("Respuesta inesperada");
        }
    } catch (err) {
        console.error("Error:", err);
        error.value = "Error de conexión. Intenta de nuevo.";
        mostrarNotificacion("Error al cargar contenidos", "error");
    } finally {
        loading.value = false;
    }
};

const cargarDatosIniciales = async () => {
    await Promise.all([
        cargarTutoriales(),
        cargarCategorias(),
        cargarSubcategorias(),
    ]);
};

const limpiarFiltrosCategoria = () => {
    categoriaSeleccionada.value = "";
    subcategoriaSeleccionada.value = "";
    cargarSubcategorias();
};

const mostrarNotificacion = (mensaje, tipo = "success") => {
    toastMessage.value = mensaje;
    toastType.value = tipo;
    showToast.value = true;
    setTimeout(() => {
        showToast.value = false;
    }, 3000);
};

let searchTimeout;
watch(search, (newValue) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        searchDebounced.value = newValue;
    }, 300);
});

watch(categoriaSeleccionada, () => {
    subcategoriaSeleccionada.value = "";
});

const tipoConfig = {
    video: {
        icon: "🎬",
        gradient: "from-rose-500 to-rose-600",
        badge: "bg-rose-100 text-rose-700",
        route: "videos",
    },
    manual: {
        icon: "📘",
        gradient: "from-blue-500 to-blue-600",
        badge: "bg-blue-100 text-blue-700",
        route: "manuales",
    },
    guia: {
        icon: "🗺️",
        gradient: "from-emerald-500 to-emerald-600",
        badge: "bg-emerald-100 text-emerald-700",
        route: "guias",
    },
    triptico: {
        icon: "📊",
        gradient: "from-violet-500 to-violet-600",
        badge: "bg-violet-100 text-violet-700",
        route: "tripticos",
    },
    default: {
        icon: "📄",
        gradient: "from-gray-500 to-gray-600",
        badge: "bg-gray-100 text-gray-700",
        route: "todos",
    },
};

const getTipoConfig = (tipo) => tipoConfig[tipo] || tipoConfig.default;

const getAlcanceBadgeClass = (alcance) => {
    let alcanceNorm = MAPEO_ROLES[alcance] || alcance;
    const colors = {
        "Superadmin we collab": "bg-purple-100 text-purple-800",
        "Admin we collab": "bg-indigo-100 text-indigo-800",
        "Soporte we collab": "bg-blue-100 text-blue-800",
        "Usuario we collab": "bg-cyan-100 text-cyan-800",
        "Suscriptor SLC": "bg-teal-100 text-teal-800",
        "Usuario Admin SLC": "bg-emerald-100 text-emerald-800",
        "Usuario Premium SLC": "bg-amber-100 text-amber-800",
        "Usuario Público": "bg-gray-100 text-gray-800",
        Prospecto: "bg-slate-100 text-slate-800",
    };
    return colors[alcanceNorm] || "bg-gray-100 text-gray-700";
};

const getTipoBadgeClass = (tipo) => {
    const colors = {
        video: "bg-rose-100 text-rose-700",
        manual: "bg-blue-100 text-blue-700",
        guia: "bg-emerald-100 text-emerald-700",
        triptico: "bg-violet-100 text-violet-700",
    };
    return colors[tipo] || "bg-gray-100 text-gray-700";
};

const verVideo = (recurso) => {
    if (estaBloqueado(recurso)) {
        mostrarNotificacion(
            `🔒 Contenido restringido. Requiere rol: "${recurso.alcance || "Público"}"`,
            "error",
        );
        return;
    }
    const tipoRoute = getTipoConfig(recurso.tipo_material)?.route || "todos";
    router.visit(`/recursos/${tipoRoute}/${recurso.id}`);
};

const cambiarVista = (modo) => {
    viewMode.value = modo;
    localStorage.setItem("tutorialViewMode", modo);
};

const filterClass = (tipo) =>
    tipoSeleccionado.value === tipo
        ? "filter filter-active"
        : "filter filter-inactive";

const resetFiltros = () => {
    search.value = "";
    searchDebounced.value = "";
    tipoSeleccionado.value = "todo";
    categoriaSeleccionada.value = "";
    subcategoriaSeleccionada.value = "";
    mostrarNotificacion("Filtros limpiados", "success");
};

onMounted(() => {
    cargarDatosIniciales();
});
</script>

<style scoped>
.filter {
    @apply px-4 py-1.5 rounded-full text-sm font-medium transition whitespace-nowrap;
}
.filter-active {
    @apply bg-indigo-600 text-white shadow-md;
}
.filter-inactive {
    @apply bg-gray-100 text-gray-600 hover:bg-gray-200;
}
.line-clamp-1 {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.toast-enter-active,
.toast-leave-active {
    transition: all 0.3s ease;
}
.toast-enter-from,
.toast-leave-to {
    opacity: 0;
    transform: translateX(100%);
}
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
.group {
    animation: fadeInUp 0.3s ease-out;
}
</style>
