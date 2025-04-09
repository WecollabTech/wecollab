<template>
    <!-- Marco del dispositivo reducido -->
    <div
        class="relative w-[220px] h-[440px] rounded-[36px] overflow-hidden shadow-3xl device-frame transition-all duration-300"
    >
        <!-- Parte trasera con cámaras ajustadas -->
        <div
            class="absolute inset-0 bg-gradient-to-br from-gray-850 to-gray-900 rounded-[36px] device-back"
        >
            <div
                class="absolute top-2 left-2 w-[40px] h-[48px] bg-gray-900 rounded-[12px] shadow-camera-module flex flex-col items-center justify-center p-1"
            >
                <div
                    class="w-[12px] h-[12px] bg-black rounded-full border-[1.5px] border-gray-800 mb-1 shadow-lens glow-lens"
                ></div>
                <div
                    class="w-[10px] h-[10px] bg-black rounded-full border-[1.5px] border-gray-800 shadow-lens glow-lens"
                ></div>
                <div
                    class="absolute bottom-1 right-1 w-[4px] h-[4px] bg-amber-400 rounded-full glow-flash"
                ></div>
            </div>
        </div>

        <!-- Pantalla del dispositivo -->
        <div
            class="absolute inset-[5px] bg-gray-950 rounded-[32px] overflow-hidden flex flex-col screen-inner z-10"
        >
            <!-- Notch reducido -->
            <div
                class="absolute top-0 left-1/2 -translate-x-1/2 w-[80px] h-[18px] bg-black rounded-b-[14px] flex justify-center items-center notch"
            >
                <div
                    class="w-[50px] h-[3px] bg-gray-900 rounded-full flex items-center justify-between px-1 shadow-inner-notch"
                >
                    <div
                        class="w-[5px] h-[5px] bg-black rounded-full border-[1px] border-gray-800 shadow-lens glow-lens-sm"
                    ></div>
                    <div
                        class="w-[2px] h-[2px] rounded-full bg-gray-850 border border-gray-700"
                    ></div>
                    <div
                        class="w-[1.5px] h-[1.5px] rounded-full bg-gray-800"
                    ></div>
                </div>
            </div>

            <!-- Botones laterales ajustados -->
            <div
                class="absolute right-[-2px] top-[70px] w-[2px] h-[40px] bg-gradient-to-b from-gray-800 to-gray-900 rounded-l-[2px] button-side shadow-button"
            ></div>
            <div
                class="absolute left-[-2px] top-[110px] w-[2px] h-[30px] bg-gradient-to-b from-gray-800 to-gray-900 rounded-r-[2px] button-side shadow-button"
            ></div>

            <!-- Encabezado ajustado -->
            <header
                class="w-full pt-6 pb-1 px-2 text-white text-center relative z-10 header-glass"
                :style="{ background: headerBackground }"
            >
                <div class="flex items-center justify-center">
                    <span class="material-icons text-sm mr-1 text-white/90">{{
                        headerIcon
                    }}</span>
                    <h1 class="text-xs font-semibold tracking-tight">
                        {{ headerTitle }}
                    </h1>
                </div>
            </header>

            <!-- Contenido principal -->
            <main class="flex-1 w-full overflow-hidden relative">
                <!-- 1. Pantalla de Bienvenida -->
                <transition name="fade-scale">
                    <div
                        v-if="currentScreen === 'welcome'"
                        class="absolute inset-0 flex flex-col items-center justify-center p-2 bg-gradient-to-br from-blue-600 to-indigo-700"
                    >
                        <div class="logo-container mb-4 animate-float">
                            <div
                                class="w-12 h-12 rounded-full bg-white/25 backdrop-blur-md border-2 border-white/40 flex items-center justify-center shadow-lg glow-ring"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-6 w-6 text-white"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    stroke-width="1.5"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M13 10V3L4 14h7v7l9-11h-7z"
                                    />
                                </svg>
                            </div>
                        </div>
                        <h1
                            class="text-base font-bold mb-1 text-center font-display tracking-tight text-white"
                        >
                            <span
                                v-for="(letter, index) in 'WeCollab Pro'.split(
                                    ''
                                )"
                                :key="index"
                                class="inline-block animate-letter"
                                :style="{
                                    animationDelay: `${index * 0.1}s`,
                                }"
                                >{{ letter }}</span
                            >
                        </h1>
                        <p
                            class="text-white/80 text-center mb-2 max-w-[150px] text-[9px]"
                        >
                            Tu solución de colaboración definitiva
                        </p>
                        <div class="w-full max-w-[150px] mb-1">
                            <div
                                class="w-full bg-white/20 rounded-full h-1 overflow-hidden shadow-inner"
                            >
                                <div
                                    class="bg-white h-1 rounded-full transition-all duration-500 ease-out shadow-progress glow-progress"
                                    :style="{ width: progress + '%' }"
                                ></div>
                            </div>
                            <p
                                class="text-right text-[8px] text-white/60 mt-0.5"
                            >
                                {{ progress }}%
                            </p>
                        </div>
                    </div>
                </transition>

                <!-- 2. Pantalla de Tutoriales -->
                <transition name="slide-fade">
                    <div
                        v-if="currentScreen === 'tutorial'"
                        class="absolute inset-0 bg-gray-50 flex flex-col overflow-hidden"
                    >
                        <div
                            class="pt-2 pb-1 px-2 bg-white relative z-10 shadow-md"
                        >
                            <h2
                                class="text-sm font-bold text-gray-900 mb-1 font-display"
                            >
                                Aprende SistemaGT
                            </h2>
                            <p class="text-[9px] text-gray-700">We collab</p>
                            <div class="mt-1 relative">
                                <input
                                    type="text"
                                    placeholder="Buscar..."
                                    class="w-full bg-white border border-gray-200 rounded-lg py-0.5 px-2 pl-6 text-[9px] focus:outline-none focus:ring-1 focus:ring-blue-500 shadow-sm"
                                />
                                <span
                                    class="material-icons absolute left-1 top-0.5 text-gray-400 text-xs"
                                    >search</span
                                >
                            </div>
                        </div>
                        <div
                            class="flex-1 overflow-y-auto pb-2 px-1"
                            ref="scrollableMain"
                        >
                            <div class="grid gap-1 py-1">
                                <div
                                    v-for="(tutorial, index) in tutorials"
                                    :key="index"
                                    class="bg-white rounded-md shadow-md border border-gray-100 overflow-hidden transition-all duration-300 hover:shadow-lg hover:-translate-y-0.5 tutorial-card"
                                >
                                    <div class="relative">
                                        <div
                                            class="h-12 bg-gradient-to-r from-blue-500 to-indigo-600 flex items-center justify-center"
                                        >
                                            <span
                                                class="material-icons text-white text-xl opacity-90"
                                                >play_circle_filled</span
                                            >
                                            <div
                                                class="absolute bottom-0.5 right-0.5 bg-black/50 text-white text-[8px] px-1 py-0.5 rounded-full flex items-center shadow-sm"
                                            >
                                                <span
                                                    class="material-icons text-[8px] mr-0.5"
                                                    >schedule</span
                                                >
                                                {{ tutorial.duration }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="p-1">
                                        <h4
                                            class="font-bold text-gray-800 text-[10px] mb-0.5"
                                        >
                                            {{ tutorial.title }}
                                        </h4>
                                        <p
                                            class="text-[9px] text-gray-600 mb-0.5"
                                        >
                                            {{ tutorial.description }}
                                        </p>
                                        <div
                                            class="flex items-center justify-between"
                                        >
                                            <div class="flex items-center">
                                                <div
                                                    class="w-3 h-3 rounded-full bg-gray-300 mr-0.5 overflow-hidden border border-white shadow-sm"
                                                >
                                                    <img
                                                        :src="
                                                            tutorial.instructor
                                                                .avatar
                                                        "
                                                        class="w-full h-full object-cover"
                                                    />
                                                </div>
                                                <span
                                                    class="text-[8px] text-gray-500"
                                                    >{{
                                                        tutorial.instructor.name
                                                    }}</span
                                                >
                                            </div>
                                            <span
                                                class="text-[8px] px-0.5 py-0.5 bg-blue-100 text-blue-600 rounded-full font-medium shadow-sm"
                                                >Nuevo</span
                                            >
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </transition>

                <!-- 3. Pantalla de Anuncios -->
                <transition name="zoom-fade">
                    <div
                        v-if="currentScreen === 'announcement'"
                        class="absolute inset-0 bg-gradient-to-br from-green-500 to-teal-600 flex flex-col"
                    >
                        <div
                            class="flex-1 flex items-center justify-center p-2"
                        >
                            <div
                                class="bg-white/20 backdrop-blur-lg rounded-lg overflow-hidden shadow-xl w-full max-w-[170px] border border-white/30"
                            >
                                <div
                                    class="h-20 bg-gradient-to-r from-green-600 to-emerald-700 flex items-center justify-center relative overflow-hidden"
                                >
                                    <span
                                        class="material-icons text-white/90 text-2xl"
                                        >campaign</span
                                    >
                                    <div
                                        class="absolute bottom-0.5 left-0.5 bg-white/95 text-green-800 text-[8px] font-bold px-1 py-0.5 rounded-full shadow-md"
                                    >
                                        {{ currentAnnouncement.date }}
                                    </div>
                                </div>
                                <div class="p-1">
                                    <h3
                                        class="text-xs font-bold text-white mb-0.5"
                                    >
                                        {{ currentAnnouncement.title }}
                                    </h3>
                                    <p class="text-white/90 text-[9px] mb-1">
                                        {{ currentAnnouncement.content }}
                                    </p>
                                    <div
                                        v-if="currentAnnouncement.details"
                                        class="bg-white/15 p-1 rounded-md mb-1 backdrop-blur-sm border border-white/20"
                                    >
                                        <h4
                                            class="font-semibold text-white text-[9px] mb-0.5 flex items-center"
                                        >
                                            <span
                                                class="material-icons text-[10px] mr-0.5 text-emerald-300"
                                                >info</span
                                            >
                                            Detalles
                                        </h4>
                                        <ul
                                            class="text-[8px] text-white/80 space-y-0.5"
                                        >
                                            <li
                                                v-for="(
                                                    detail, index
                                                ) in currentAnnouncement.details"
                                                :key="index"
                                                class="flex items-start"
                                            >
                                                <span
                                                    class="material-icons text-[8px] text-emerald-300 mr-0.5 mt-0.5"
                                                    >check_circle</span
                                                >
                                                {{ detail }}
                                            </li>
                                        </ul>
                                    </div>
                                    <button
                                        class="w-full bg-white text-green-700 py-0.5 px-1 rounded-md font-medium text-[9px] flex items-center justify-center hover:bg-gray-100 transition-all duration-300 shadow-md hover:shadow-lg"
                                    >
                                        <span
                                            class="material-icons text-[10px] mr-0.5 text-green-600"
                                            >arrow_forward</span
                                        >
                                        {{ currentAnnouncement.cta }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </transition>

                <!-- 4. Pantalla Corporativa -->
                <transition name="slide-up">
                    <div
                        v-if="currentScreen === 'corporate'"
                        class="absolute inset-0 bg-gradient-to-br from-indigo-600 to-purple-700 flex flex-col"
                    >
                        <div class="flex-1 overflow-y-auto p-2">
                            <div class="max-w-[170px] mx-auto">
                                <div
                                    class="bg-white/20 backdrop-blur-lg rounded-lg overflow-hidden shadow-xl mb-2 border border-white/30"
                                >
                                    <div class="p-2 text-center">
                                        <div
                                            class="w-10 h-10 rounded-full bg-white/25 backdrop-blur-md mx-auto mb-1 border-2 border-white/40 flex items-center justify-center shadow-lg glow-ring"
                                        >
                                            <img
                                                src="https://via.placeholder.com/80/ffffff/4f46e5?text=WC"
                                                class="w-5 h-5"
                                            />
                                        </div>
                                        <h2
                                            class="text-sm font-bold text-white mb-0.5 font-display"
                                        >
                                            WeCollab Inc.
                                        </h2>
                                        <p
                                            class="text-indigo-100 mb-1 text-[9px]"
                                        >
                                            {{ corporateInfo.mission }}
                                        </p>
                                        <div class="grid grid-cols-2 gap-1">
                                            <div
                                                class="bg-white/15 p-1 rounded-md backdrop-blur-sm border border-white/20"
                                            >
                                                <span
                                                    class="material-icons block mb-0.5 text-white/90 text-sm"
                                                    >groups</span
                                                >
                                                <span
                                                    class="text-[8px] font-medium text-white/90"
                                                    >{{
                                                        corporateInfo.employees
                                                    }}+</span
                                                >
                                            </div>
                                            <div
                                                class="bg-white/15 p-1 rounded-md backdrop-blur-sm border border-white/20"
                                            >
                                                <span
                                                    class="material-icons block mb-0.5 text-white/90 text-sm"
                                                    >public</span
                                                >
                                                <span
                                                    class="text-[8px] font-medium text-white/90"
                                                    >{{
                                                        corporateInfo.countries
                                                    }}</span
                                                >
                                            </div>
                                            <div
                                                class="bg-white/15 p-1 rounded-md backdrop-blur-sm border border-white/20"
                                            >
                                                <span
                                                    class="material-icons block mb-0.5 text-white/90 text-sm"
                                                    >rocket_launch</span
                                                >
                                                <span
                                                    class="text-[8px] font-medium text-white/90"
                                                    >{{
                                                        corporateInfo.projects
                                                    }}</span
                                                >
                                            </div>
                                            <div
                                                class="bg-white/15 p-1 rounded-md backdrop-blur-sm border border-white/20"
                                            >
                                                <span
                                                    class="material-icons block mb-0.5 text-white/90 text-sm"
                                                    >celebration</span
                                                >
                                                <span
                                                    class="text-[8px] font-medium text-white/90"
                                                    >{{
                                                        corporateInfo.founded
                                                    }}</span
                                                >
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div
                                    class="bg-white/20 backdrop-blur-lg rounded-lg overflow-hidden shadow-xl p-1 border border-white/30"
                                >
                                    <h3
                                        class="text-xs font-bold text-white mb-0.5 flex items-center"
                                    >
                                        <span
                                            class="material-icons text-sm mr-0.5 text-indigo-300"
                                            >star</span
                                        >
                                        Valores
                                    </h3>
                                    <div class="space-y-0.5">
                                        <div
                                            v-for="(
                                                value, index
                                            ) in corporateInfo.values"
                                            :key="index"
                                            class="bg-white/10 p-0.5 rounded-md backdrop-blur-sm border border-white/20"
                                        >
                                            <h4
                                                class="font-semibold text-white text-[9px]"
                                            >
                                                {{ value.title }}
                                            </h4>
                                            <p
                                                class="text-[8px] text-indigo-100 mt-0.5"
                                            >
                                                {{ value.description }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </transition>

                <!-- 5. Pantalla de Emergencia -->
                <transition name="fade-slow">
                    <div
                        v-if="currentScreen === 'emergency'"
                        class="absolute inset-0 bg-gradient-to-br from-red-600 to-rose-700 flex flex-col items-center justify-center p-2 text-center"
                    >
                        <div class="emergency-alert animate-pulse-slow">
                            <div
                                class="bg-white/25 backdrop-blur-md rounded-full p-2 inline-flex border-2 border-white/40 shadow-lg glow-ring-red"
                            >
                                <span class="material-icons text-2xl text-white"
                                    >warning</span
                                >
                            </div>
                        </div>
                        <h1
                            class="text-sm font-bold mt-2 mb-0.5 font-display text-white"
                        >
                            Comunicado
                        </h1>
                        <p
                            class="mb-2 px-1 text-white/90 max-w-[170px] text-[9px]"
                        >
                            {{ emergencyMessage }}
                        </p>
                        <div
                            class="bg-white/20 backdrop-blur-sm p-1 rounded-md max-w-[150px] flex items-center text-[8px] text-white border border-white/30"
                        >
                            <span class="material-icons text-[10px] mr-0.5"
                                >schedule</span
                            >
                            <span>12 Nov 2023 - 10:00 PM</span>
                        </div>
                        <button
                            class="mt-2 bg-white text-red-600 py-0.5 px-2 rounded-full font-medium text-[9px] flex items-center shadow-md hover:shadow-lg transition-all duration-300"
                        >
                            <span class="material-icons text-[10px] mr-0.5"
                                >visibility</span
                            >
                            Confirmar
                        </button>
                    </div>
                </transition>

                <!-- 6. Pantalla de Destacado -->
                <transition name="zoom-fade">
                    <div
                        v-if="currentScreen === 'highlight'"
                        class="absolute inset-0 bg-gradient-to-br from-amber-500 to-orange-600 flex flex-col items-center justify-center p-2 text-center"
                    >
                        <div class="highlight-badge mb-2">
                            <div class="relative">
                                <img
                                    :src="highlightEmployee.avatar"
                                    class="w-12 h-12 rounded-full mx-auto border-2 border-white/90 shadow-lg object-cover glow-ring-yellow"
                                />
                                <div
                                    class="absolute -bottom-0.5 -right-0.5 bg-amber-200 text-amber-800 rounded-full p-0.5 shadow-md"
                                >
                                    <span class="material-icons text-[10px]"
                                        >star</span
                                    >
                                </div>
                            </div>
                        </div>
                        <h1
                            class="text-sm font-bold text-white mb-0.5 font-display"
                        >
                            {{ highlightEmployee.name }}
                        </h1>
                        <p class="text-amber-100 mb-1 text-[9px]">
                            {{ highlightEmployee.position }}
                        </p>
                        <div
                            class="bg-white/20 backdrop-blur-sm rounded-md p-1 max-w-[150px] mb-2 border border-white/30"
                        >
                            <p class="text-white/90 italic text-[9px]">
                                "{{ highlightEmployee.quote }}"
                            </p>
                        </div>
                        <div class="flex space-x-1">
                            <button
                                class="bg-white/15 backdrop-blur-sm text-white py-0.5 px-1 rounded-full text-[9px] flex items-center border border-white/30 hover:bg-white/25 transition-colors shadow-sm"
                            >
                                <span class="material-icons text-[10px] mr-0.5"
                                    >thumb_up</span
                                >
                                Felicitar
                            </button>
                            <button
                                class="bg-white text-amber-600 py-0.5 px-1 rounded-full text-[9px] flex items-center shadow-md hover:shadow-lg transition-shadow"
                            >
                                <span class="material-icons text-[10px] mr-0.5"
                                    >share</span
                                >
                                Compartir
                            </button>
                        </div>
                    </div>
                </transition>

                <!-- 7. Pantalla de Redes Sociales -->
                <transition name="slide-up">
                    <div
                        v-if="currentScreen === 'social'"
                        class="absolute inset-0 bg-gradient-to-br from-rose-500 to-pink-600 flex flex-col"
                    >
                        <div
                            class="flex-1 flex items-center justify-center p-2"
                        >
                            <div class="w-full max-w-[170px]">
                                <div class="text-center mb-3">
                                    <div
                                        class="bg-white/25 backdrop-blur-md rounded-full p-1.5 inline-flex mb-1 border border-white/40 shadow-lg glow-ring"
                                    >
                                        <span
                                            class="material-icons text-xl text-white"
                                            >share</span
                                        >
                                    </div>
                                    <h1
                                        class="text-sm font-bold text-white mb-0.5 font-display"
                                    >
                                        Conéctate
                                    </h1>
                                    <p
                                        class="text-white/80 px-1 text-[9px] max-w-[150px]"
                                    >
                                        Noticias y actualizaciones
                                    </p>
                                </div>
                                <div class="grid grid-cols-2 gap-1">
                                    <a
                                        v-for="(
                                            network, index
                                        ) in socialNetworks"
                                        :key="index"
                                        :href="network.url"
                                        class="bg-white/15 backdrop-blur-sm rounded-md p-1 flex flex-col items-center hover:bg-white/25 transition-colors border border-white/30 shadow-sm hover:shadow-md"
                                        target="_blank"
                                    >
                                        <div
                                            class="w-6 h-6 rounded-full bg-white/25 flex items-center justify-center mb-0.5 border border-white/40"
                                        >
                                            <span
                                                class="material-icons text-white text-sm"
                                                >{{ network.icon }}</span
                                            >
                                        </div>
                                        <span
                                            class="text-[9px] text-white font-medium"
                                            >{{ network.name }}</span
                                        >
                                        <span
                                            class="text-[8px] text-white/60 mt-0.5"
                                            >@{{ network.handle }}</span
                                        >
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </transition>

                <!-- 8. Pantalla de Proyectos -->
                <transition name="slide-fade">
                    <div
                        v-if="currentScreen === 'projects'"
                        class="absolute inset-0 bg-gradient-to-br from-gray-700 to-gray-800 flex flex-col"
                    >
                        <div
                            class="pt-8 pb-1 px-2 bg-gray-900/95 backdrop-blur-md relative z-10 shadow-md"
                        >
                            <h2
                                class="text-sm font-bold text-white mb-1 font-display"
                            >
                                Proyectos
                            </h2>
                            <p class="text-[9px] text-gray-300">
                                Estado de tus proyectos
                            </p>
                        </div>
                        <div class="flex-1 overflow-y-auto p-2">
                            <div class="space-y-1">
                                <div
                                    v-for="(project, index) in projects"
                                    :key="index"
                                    class="bg-white/10 backdrop-blur-sm rounded-md p-1 border border-white/20 shadow-md hover:shadow-lg transition-all duration-300"
                                >
                                    <h4
                                        class="text-[10px] font-semibold text-white mb-0.5"
                                    >
                                        {{ project.name }}
                                    </h4>
                                    <div
                                        class="w-full bg-gray-700 rounded-full h-1 mb-0.5 overflow-hidden"
                                    >
                                        <div
                                            class="bg-blue-500 h-1 rounded-full transition-all duration-300"
                                            :style="{
                                                width: project.progress + '%',
                                            }"
                                        ></div>
                                    </div>
                                    <div
                                        class="flex justify-between text-[8px] text-gray-300"
                                    >
                                        <span>{{ project.progress }}%</span>
                                        <span>{{ project.deadline }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </transition>

                <!-- 9. Pantalla de Chat -->
                <transition name="zoom-fade">
                    <div
                        v-if="currentScreen === 'chat'"
                        class="absolute inset-0 bg-gradient-to-br from-blue-500 to-cyan-600 flex flex-col"
                    >
                        <div
                            class="pt-8 pb-1 px-2 bg-blue-700/95 backdrop-blur-md relative z-10 shadow-md"
                        >
                            <h2
                                class="text-sm font-bold text-white mb-1 font-display"
                            >
                                Chat
                            </h2>
                            <p class="text-[9px] text-blue-100">
                                Equipo de soporte
                            </p>
                        </div>
                        <div class="flex-1 overflow-y-auto p-2 space-y-1">
                            <div
                                v-for="(message, index) in chatMessages"
                                :key="index"
                                :class="[
                                    'rounded-lg p-1 max-w-[70%] text-[9px] shadow-md',
                                    message.sentByUser
                                        ? 'bg-blue-600 text-white ml-auto'
                                        : 'bg-white/20 text-white',
                                ]"
                            >
                                {{ message.text }}
                                <span
                                    class="block text-[7px] text-gray-200 mt-0.5"
                                    >{{ message.time }}</span
                                >
                            </div>
                        </div>
                        <div
                            class="p-1 bg-blue-700/95 backdrop-blur-md border-t border-white/20"
                        >
                            <div class="flex items-center">
                                <input
                                    type="text"
                                    placeholder="Escribe un mensaje..."
                                    class="flex-1 bg-white/10 border border-white/20 rounded-l-md py-0.5 px-1 text-[9px] text-white focus:outline-none focus:ring-1 focus:ring-blue-400"
                                />
                                <button
                                    class="bg-blue-500 text-white py-0.5 px-1 rounded-r-md hover:bg-blue-600 transition-all duration-300"
                                >
                                    <span class="material-icons text-xs"
                                        >send</span
                                    >
                                </button>
                            </div>
                        </div>
                    </div>
                </transition>

                <!-- 10. Pantalla de Calendario -->
                <transition name="slide-up">
                    <div
                        v-if="currentScreen === 'calendar'"
                        class="absolute inset-0 bg-gradient-to-br from-purple-500 to-indigo-600 flex flex-col"
                    >
                        <div
                            class="pt-8 pb-1 px-2 bg-purple-700/95 backdrop-blur-md relative z-10 shadow-md"
                        >
                            <h2
                                class="text-sm font-bold text-white mb-1 font-display"
                            >
                                Calendario
                            </h2>
                            <p class="text-[9px] text-purple-100">
                                Eventos y plazos
                            </p>
                        </div>
                        <div class="flex-1 overflow-y-auto p-2">
                            <div class="space-y-1">
                                <div
                                    v-for="(event, index) in calendarEvents"
                                    :key="index"
                                    class="bg-white/15 backdrop-blur-sm rounded-md p-1 border border-white/30 shadow-md hover:shadow-lg transition-all duration-300"
                                >
                                    <div class="flex items-center">
                                        <span
                                            class="material-icons text-white text-xs mr-0.5"
                                            >event</span
                                        >
                                        <h4
                                            class="text-[10px] font-semibold text-white"
                                        >
                                            {{ event.title }}
                                        </h4>
                                    </div>
                                    <p class="text-[8px] text-white/80 mt-0.5">
                                        {{ event.date }} - {{ event.time }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </transition>

                <!-- 11. Pantalla de Estadísticas -->
                <transition name="fade-scale">
                    <div
                        v-if="currentScreen === 'stats'"
                        class="absolute inset-0 bg-gradient-to-br from-teal-500 to-cyan-600 flex flex-col"
                    >
                        <div
                            class="pt-8 pb-1 px-2 bg-teal-700/95 backdrop-blur-md relative z-10 shadow-md"
                        >
                            <h2
                                class="text-sm font-bold text-white mb-1 font-display"
                            >
                                Estadísticas
                            </h2>
                            <p class="text-[9px] text-teal-100">
                                Rendimiento del equipo
                            </p>
                        </div>
                        <div class="flex-1 p-2">
                            <div class="space-y-2">
                                <div
                                    class="bg-white/20 backdrop-blur-sm rounded-md p-1 border border-white/30 shadow-md"
                                >
                                    <h4
                                        class="text-[10px] font-semibold text-white mb-0.5"
                                    >
                                        Tareas Completadas
                                    </h4>
                                    <div
                                        class="w-full bg-teal-700 rounded-full h-1 overflow-hidden"
                                    >
                                        <div
                                            class="bg-teal-400 h-1 rounded-full"
                                            style="width: 75%"
                                        ></div>
                                    </div>
                                    <p
                                        class="text-[8px] text-white/80 mt-0.5 text-right"
                                    >
                                        75%
                                    </p>
                                </div>
                                <div
                                    class="bg-white/20 backdrop-blur-sm rounded-md p-1 border border-white/30 shadow-md"
                                >
                                    <h4
                                        class="text-[10px] font-semibold text-white mb-0.5"
                                    >
                                        Horas Trabajadas
                                    </h4>
                                    <p class="text-[9px] text-white font-bold">
                                        120h
                                    </p>
                                    <p class="text-[8px] text-white/80 mt-0.5">
                                        Esta semana
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </transition>
            </main>

            <!-- Barra de navegación ajustada -->
            <transition name="fade-up">
                <nav
                    v-if="showNavigation"
                    class="w-full bg-white/95 backdrop-blur-lg p-0.5 flex justify-around border-t border-white/20 rounded-b-[32px] z-10 nav-premium shadow-nav"
                >
                    <button
                        v-for="(item, index) in navItems"
                        :key="index"
                        @click="navigateTo(item.screen)"
                        class="flex flex-col items-center px-1 py-0 rounded-md transition-all duration-300"
                        :class="{
                            'text-blue-600 bg-white shadow-md active-nav':
                                currentScreen === item.screen,
                            'text-gray-600 hover:text-gray-800 hover:bg-gray-100':
                                currentScreen !== item.screen,
                        }"
                    >
                        <div class="relative">
                            <span class="material-icons text-xs">{{
                                item.icon
                            }}</span>
                            <span
                                v-if="
                                    item.notification &&
                                    currentScreen !== item.screen
                                "
                                class="absolute -top-0.5 -right-0.5 w-1 h-1 bg-red-500 rounded-full border border-white shadow-sm glow"
                            ></span>
                        </div>
                        <span class="text-[7px] mt-0">{{ item.label }}</span>
                    </button>
                </nav>
            </transition>
        </div>
    </div>
</template>

<script>
export default {
    name: "WeCollabPremiumSystem",
    data() {
        return {
            currentScreen: "welcome",
            progress: 0,
            showNavigation: false,
            currentAnnouncementIndex: 0,
            flowSequence: [
                "welcome",
                "tutorial",
                "announcement",
                "corporate",
                "emergency",
                "highlight",
                "social",
                "projects",
                "chat",
                "calendar",
                "stats",
            ],
            currentFlowIndex: 0,
            tutorials: [
                {
                    title: "Onboarding Completo",
                    description: "Guía para nuevos usuarios",
                    duration: "15 min",
                    instructor: {
                        name: "Ana Martínez",
                        avatar: "https://randomuser.me/api/portraits/women/44.jpg",
                    },
                },
                {
                    title: "Gestión de Proyectos",
                    description: "Herramientas de gestión",
                    duration: "22 min",
                    instructor: {
                        name: "Carlos Ruiz",
                        avatar: "https://randomuser.me/api/portraits/men/32.jpg",
                    },
                },
                {
                    title: "Automatización",
                    description: "Flujos automatizados",
                    duration: "18 min",
                    instructor: {
                        name: "Laura Gómez",
                        avatar: "https://randomuser.me/api/portraits/women/68.jpg",
                    },
                },
            ],
            announcements: [
                {
                    title: "WeCollab 4.0",
                    content: "Nueva versión con mejoras.",
                    date: "15 Jun 2023",
                    details: [
                        "Comentarios",
                        "Plantillas",
                        "Google",
                        "Rendimiento",
                    ],
                    cta: "Ver",
                },
                {
                    title: "Webinar Remoto",
                    content: "Aprende trabajo remoto.",
                    date: "28 May 2023",
                    details: ["7 Jun", "45 min", "Regalo"],
                    cta: "Registrarme",
                },
            ],
            corporateInfo: {
                mission: "Colaboración con tecnología innovadora",
                employees: 320,
                countries: 18,
                projects: 1200,
                founded: 2016,
                values: [
                    { title: "Colaboración", description: "Trabajo en equipo" },
                    { title: "Innovación", description: "Soluciones nuevas" },
                    { title: "Transparencia", description: "Honestidad" },
                ],
            },
            emergencyMessage:
                "Mantenimiento este sábado 10PM-2AM. Sistema no disponible.",
            highlightEmployee: {
                name: "María González",
                position: "Líder de Desarrollo",
                avatar: "https://randomuser.me/api/portraits/women/65.jpg",
                quote: "Colaboración multiplica resultados.",
            },
            socialNetworks: [
                {
                    name: "Facebook",
                    icon: "facebook",
                    handle: "WeCollab",
                    url: "#",
                },
                {
                    name: "Twitter",
                    icon: "twitter",
                    handle: "WeCollabHQ",
                    url: "#",
                },
                {
                    name: "LinkedIn",
                    icon: "linkedin",
                    handle: "WeCollab",
                    url: "#",
                },
                {
                    name: "Instagram",
                    icon: "instagram",
                    handle: "WeCollab",
                    url: "#",
                },
            ],
            projects: [
                { name: "Proyecto Alpha", progress: 60, deadline: "15 Abr" },
                { name: "Proyecto Beta", progress: 85, deadline: "20 Abr" },
                { name: "Proyecto Gamma", progress: 30, deadline: "30 Abr" },
            ],
            chatMessages: [
                {
                    text: "Hola, ¿cómo puedo ayudarte?",
                    sentByUser: false,
                    time: "10:00",
                },
                {
                    text: "Necesito acceso al proyecto Beta.",
                    sentByUser: true,
                    time: "10:01",
                },
                {
                    text: "Te lo asigno ahora mismo.",
                    sentByUser: false,
                    time: "10:02",
                },
            ],
            calendarEvents: [
                { title: "Reunión Equipo", date: "10 Abr", time: "14:00" },
                { title: "Entrega Alpha", date: "15 Abr", time: "09:00" },
                { title: "Webinar", date: "20 Abr", time: "16:00" },
            ],
            navItems: [
                {
                    screen: "tutorial",
                    icon: "school",
                    label: "Aprender",
                    notification: true,
                },
                {
                    screen: "projects",
                    icon: "folder",
                    label: "Proyectos",
                    notification: true,
                },
                {
                    screen: "chat",
                    icon: "chat",
                    label: "Chat",
                    notification: true,
                },
                {
                    screen: "calendar",
                    icon: "calendar_today",
                    label: "Calendario",
                    notification: false,
                },
            ],
            screenTimeout: null,
            scrollInterval: null,
            direction: 1,
        };
    },
    computed: {
        headerTitle() {
            const titles = {
                welcome: "Bienvenido",
                tutorial: "Aprender",
                announcement: "Anuncios",
                corporate: "WeCollab",
                emergency: "Importante",
                highlight: "Destacado",
                social: "Redes",
                projects: "Proyectos",
                chat: "Chat",
                calendar: "Calendario",
                stats: "Estadísticas",
            };
            return titles[this.currentScreen];
        },
        headerIcon() {
            const icons = {
                welcome: "home",
                tutorial: "school",
                announcement: "campaign",
                corporate: "business",
                emergency: "warning",
                highlight: "star",
                social: "share",
                projects: "folder",
                chat: "chat",
                calendar: "calendar_today",
                stats: "bar_chart",
            };
            return icons[this.currentScreen];
        },
        headerBackground() {
            const colors = {
                welcome:
                    "linear-gradient(135deg, rgba(59, 130, 246, 0.95), rgba(79, 70, 229, 0.95))",
                tutorial:
                    "linear-gradient(135deg, rgba(139, 92, 246, 0.95), rgba(124, 58, 237, 0.95))",
                announcement:
                    "linear-gradient(135deg, rgba(16, 185, 129, 0.95), rgba(5, 150, 105, 0.95))",
                corporate:
                    "linear-gradient(135deg, rgba(99, 102, 241, 0.95), rgba(124, 58, 237, 0.95))",
                emergency:
                    "linear-gradient(135deg, rgba(239, 68, 68, 0.95), rgba(220, 38, 38, 0.95))",
                highlight:
                    "linear-gradient(135deg, rgba(245, 158, 11, 0.95), rgba(217, 119, 6, 0.95))",
                social: "linear-gradient(135deg, rgba(244, 63, 94, 0.95), rgba(225, 29, 72, 0.95))",
                projects:
                    "linear-gradient(135deg, rgba(55, 65, 81, 0.95), rgba(31, 41, 55, 0.95))",
                chat: "linear-gradient(135deg, rgba(59, 130, 246, 0.95), rgba(6, 182, 212, 0.95))",
                calendar:
                    "linear-gradient(135deg, rgba(139, 92, 246, 0.95), rgba(79, 70, 229, 0.95))",
                stats: "linear-gradient(135deg, rgba(13, 148, 136, 0.95), rgba(6, 182, 212, 0.95))",
            };
            return colors[this.currentScreen];
        },
        currentAnnouncement() {
            return this.announcements[this.currentAnnouncementIndex];
        },
    },
    mounted() {
        this.startFlow();
    },
    beforeUnmount() {
        this.clearTimers();
    },
    methods: {
        startFlow() {
            this.currentScreen = this.flowSequence[this.currentFlowIndex];
            if (this.currentScreen === "welcome") {
                this.startWelcomeScreen();
            } else {
                this.initializeScreen();
            }
        },
        startWelcomeScreen() {
            this.progress = 0;
            this.showNavigation = false;
            const progressInterval = setInterval(() => {
                this.progress += 1;
                if (this.progress >= 100) clearInterval(progressInterval);
            }, 100);
            this.screenTimeout = setTimeout(() => {
                this.nextScreen();
            }, 10000);
        },
        initializeScreen() {
            this.clearTimers();
            this.showNavigation = true;
            if (
                this.currentScreen === "tutorial" ||
                this.currentScreen === "projects" ||
                this.currentScreen === "calendar"
            ) {
                this.$nextTick(() => {
                    if (this.$refs.scrollableMain) {
                        this.$refs.scrollableMain.scrollTop = 0;
                    }
                    this.startAutoScroll();
                });
            } else if (this.currentScreen === "announcement") {
                this.currentAnnouncementIndex =
                    (this.currentAnnouncementIndex + 1) %
                    this.announcements.length;
            }
            this.screenTimeout = setTimeout(() => {
                this.nextScreen();
            }, 10000);
        },
        nextScreen() {
            this.currentFlowIndex =
                (this.currentFlowIndex + 1) % this.flowSequence.length;
            this.startFlow();
        },
        startAutoScroll() {
            const scrollableElement = this.$refs.scrollableMain;
            if (!scrollableElement) return;
            this.scrollInterval = setInterval(() => {
                scrollableElement.scrollBy({
                    top: this.direction * 5,
                    behavior: "smooth",
                });
                if (
                    scrollableElement.scrollTop +
                        scrollableElement.clientHeight >=
                    scrollableElement.scrollHeight - 8
                ) {
                    this.direction = -1;
                } else if (scrollableElement.scrollTop <= 0) {
                    this.direction = 1;
                }
            }, 50);
        },
        clearTimers() {
            if (this.scrollInterval) {
                clearInterval(this.scrollInterval);
                this.scrollInterval = null;
            }
            if (this.screenTimeout) {
                clearTimeout(this.screenTimeout);
                this.screenTimeout = null;
            }
        },
        navigateTo(screen) {
            const index = this.flowSequence.findIndex(
                (item) => item === screen
            );
            if (index !== -1) {
                this.currentFlowIndex = index;
                this.startFlow();
            }
        },
    },
};
</script>

<style>
@import url("https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap");
@import url("https://fonts.googleapis.com/icon?family=Material+Icons");

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

@keyframes pulse {
    0%,
    100% {
        opacity: 1;
    }
    50% {
        opacity: 0.85;
    }
}

@keyframes pulse-slow {
    0%,
    100% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.03);
    }
}

@keyframes float {
    0%,
    100% {
        transform: translateY(0) scale(1);
    }
    50% {
        transform: translateY(-5px) scale(1.02);
    }
}

@keyframes letter-pop {
    0% {
        opacity: 0;
        transform: translateY(6px) scale(0.8);
    }
    60% {
        opacity: 1;
        transform: translateY(-1px) scale(1.05);
    }
    100% {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

.fade-scale-enter-active {
    transition: all 0.4s ease;
}
.fade-scale-leave-active {
    transition: all 0.3s ease;
}
.fade-scale-enter-from {
    opacity: 0;
    transform: scale(0.98);
}
.fade-scale-leave-to {
    opacity: 0;
    transform: scale(1.03);
}

.slide-fade-enter-active {
    transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
}
.slide-fade-leave-active {
    transition: all 0.3s ease;
}
.slide-fade-enter-from {
    opacity: 0;
    transform: translateX(12px);
}
.slide-fade-leave-to {
    opacity: 0;
    transform: translateX(-12px);
}

.zoom-fade-enter-active {
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.1);
    animation: pulse 0.7s 0.3s;
}
.zoom-fade-leave-active {
    transition: all 0.3s ease-in;
}
.zoom-fade-enter-from {
    opacity: 0;
    transform: scale(0.94);
}
.zoom-fade-leave-to {
    opacity: 0;
    transform: scale(1.02);
}

.slide-up-enter-active {
    transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
}
.slide-up-leave-active {
    transition: all 0.3s ease;
}
.slide-up-enter-from {
    opacity: 0;
    transform: translateY(20px);
}
.slide-up-leave-to {
    opacity: 0;
    transform: translateY(-20px);
}

.fade-slow-enter-active {
    transition: all 0.5s ease;
    animation: pulse 1s;
}
.fade-slow-leave-active {
    transition: all 0.4s ease;
}
.fade-slow-enter-from,
.fade-slow-leave-to {
    opacity: 0;
}

.fade-up-enter-active {
    transition: all 0.3s ease-out;
}
.fade-up-leave-active {
    transition: all 0.2s ease-in;
}
.fade-up-enter-from {
    opacity: 0;
    transform: translateY(5px);
}
.fade-up-leave-to {
    opacity: 0;
    transform: translateY(2px);
}

.animate-float {
    animation: float 3s ease-in-out infinite;
}
.animate-pulse-slow {
    animation: pulse-slow 2s ease-in-out infinite;
}
.animate-letter {
    animation: letter-pop 0.5s ease-out forwards;
}

body {
    overflow-x: hidden;
    font-family: "Inter", system-ui, sans-serif;
}
.font-display {
    font-weight: 700;
    letter-spacing: -0.02em;
}

/* Dispositivo reducido */
.device-frame {
    border: 5px solid rgba(255, 255, 255, 0.05);
    box-shadow: 0 0 0 2px rgba(0, 0, 0, 0.4),
        0 30px 60px -15px rgba(0, 0, 0, 0.8),
        0 15px 45px -10px rgba(0, 0, 0, 0.6),
        inset 0 0 25px rgba(255, 255, 255, 0.02),
        inset 3px 3px 12px rgba(255, 255, 255, 0.1),
        inset -3px -3px 12px rgba(0, 0, 0, 0.3);
    background: linear-gradient(145deg, #2d2d2d, #1f1f1f, #121212);
    transform: perspective(1200px) rotateX(4deg) rotateY(-4deg);
    position: relative;
}

.device-back {
    box-shadow: inset 0 0 20px rgba(0, 0, 0, 0.6),
        inset 0 2px 10px rgba(255, 255, 255, 0.05);
    background: linear-gradient(145deg, #2d2d2d, #1f1f1f, #121212);
}

.shadow-camera-module {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.5),
        inset 0 1px 4px rgba(255, 255, 255, 0.1),
        inset 0 -1px 4px rgba(0, 0, 0, 0.4);
    transform: translateZ(5px);
}

.shadow-lens {
    box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.6),
        inset 0 -1px 2px rgba(255, 255, 255, 0.05),
        0 1px 4px rgba(0, 0, 0, 0.25);
}

.glow-lens {
    box-shadow: 0 0 5px rgba(255, 255, 255, 0.25),
        inset 0 0 1px rgba(255, 255, 255, 0.1);
}
.glow-lens-sm {
    box-shadow: 0 0 3px rgba(255, 255, 255, 0.15),
        inset 0 0 1px rgba(255, 255, 255, 0.05);
}
.glow-flash {
    box-shadow: 0 0 6px rgba(251, 191, 36, 0.6);
}

.screen-inner {
    box-shadow: inset 0 0 10px rgba(0, 0, 0, 0.6),
        inset 0 2px 6px rgba(255, 255, 255, 0.05),
        0 3px 12px rgba(0, 0, 0, 0.35);
    border: 1px solid rgba(255, 255, 255, 0.01);
    background: #050505;
}

.notch {
    background: linear-gradient(
        135deg,
        rgba(0, 0, 0, 0.98),
        rgba(30, 30, 30, 0.9)
    );
    box-shadow: inset 0 2px 6px rgba(0, 0, 0, 0.5), 0 3px 8px rgba(0, 0, 0, 0.3),
        inset 0 -1px 2px rgba(255, 255, 255, 0.02);
    backdrop-filter: blur(5px);
    -webkit-backdrop-filter: blur(5px);
    z-index: 10;
}

.shadow-inner-notch {
    box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.5),
        inset 0 -1px 1px rgba(255, 255, 255, 0.05);
}

.button-side {
    box-shadow: inset 0 1px 2px rgba(255, 255, 255, 0.15),
        inset 0 -1px 2px rgba(0, 0, 0, 0.4), 1px 0 6px rgba(0, 0, 0, 0.3);
    transform: translateZ(5px);
}

.shadow-button {
    box-shadow: 1px 1px 8px rgba(0, 0, 0, 0.35),
        -1px -1px 4px rgba(255, 255, 255, 0.05);
}

.header-glass {
    backdrop-filter: blur(14px);
    -webkit-backdrop-filter: blur(14px);
    background: linear-gradient(
        135deg,
        rgba(255, 255, 255, 0.15),
        rgba(255, 255, 255, 0.05)
    );
    border-bottom: 1px solid rgba(255, 255, 255, 0.25);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
}

.emergency-alert {
    filter: drop-shadow(0 0 6px rgba(255, 71, 71, 0.5));
}
.highlight-badge {
    filter: drop-shadow(0 3px 10px rgba(245, 158, 11, 0.35));
}

.tutorial-card {
    transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
}
.tutorial-card:hover {
    transform: translateY(-1px) scale(1.02);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
    background: linear-gradient(135deg, #fff, #f9fafb);
}

.nav-premium {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    box-shadow: 0 -2px 12px rgba(0, 0, 0, 0.15);
}
.shadow-nav {
    box-shadow: 0 -2px 12px rgba(0, 0, 0, 0.15),
        inset 0 1px 2px rgba(255, 255, 255, 0.1);
}
.active-nav {
    background: #fff;
    box-shadow: 0 1px 6px rgba(0, 0, 0, 0.15);
    transform: translateY(-1px);
}

h1 {
    font-size: 1rem;
    line-height: 1.2;
}
h2 {
    font-size: 0.875rem;
}
p {
    line-height: 1.4;
    margin-bottom: 0.375rem;
}

.backdrop-blur-lg {
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
}
.backdrop-blur-md {
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
}
.backdrop-blur-sm {
    backdrop-filter: blur(5px);
    -webkit-backdrop-filter: blur(5px);
}

::-webkit-scrollbar {
    width: 2px;
}
::-webkit-scrollbar-track {
    background: rgba(0, 0, 0, 0.05);
    border-radius: 4px;
}
::-webkit-scrollbar-thumb {
    background: rgba(0, 0, 0, 0.2);
    border-radius: 4px;
}
::-webkit-scrollbar-thumb:hover {
    background: rgba(0, 0, 0, 0.3);
}

/* Efectos adicionales */
.glow {
    box-shadow: 0 0 4px rgba(255, 0, 0, 0.5);
}
.glow-ring {
    box-shadow: 0 0 8px rgba(255, 255, 255, 0.25),
        inset 0 0 4px rgba(255, 255, 255, 0.1);
}
.glow-ring-red {
    box-shadow: 0 0 8px rgba(255, 71, 71, 0.5),
        inset 0 0 4px rgba(255, 255, 255, 0.1);
}
.glow-ring-yellow {
    box-shadow: 0 0 8px rgba(245, 158, 11, 0.5),
        inset 0 0 4px rgba(255, 255, 255, 0.1);
}
.shadow-progress {
    box-shadow: inset 0 1px 2px rgba(255, 255, 255, 0.3);
}
.glow-progress {
    box-shadow: 0 0 6px rgba(255, 255, 255, 0.6);
}

.content-section {
    animation: fadeInUp 0.4s cubic-bezier(0.25, 0.8, 0.25, 1) forwards;
}
</style>
