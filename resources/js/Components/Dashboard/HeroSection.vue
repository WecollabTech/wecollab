<template>
    <!-- ==================== HERO SECTION ==================== -->
    <div
        class="relative bg-gradient-to-br from-emerald-600 via-teal-700 to-cyan-800 overflow-hidden"
    >
        <!-- Animated Background -->
        <div class="absolute inset-0">
            <img
                src="https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=1920&q=80"
                alt="Mountain background"
                class="w-full h-full object-cover opacity-20 scale-105 animate-pulse-slow"
            />
            <div
                class="absolute inset-0 bg-gradient-to-b from-black/40 via-black/20 to-emerald-900/60"
            ></div>
            <div
                class="absolute top-10 left-10 w-72 h-72 bg-emerald-400/20 rounded-full blur-3xl animate-blob"
            ></div>
            <div
                class="absolute top-10 right-10 w-72 h-72 bg-cyan-400/20 rounded-full blur-3xl animate-blob animation-delay-2000"
            ></div>
            <div
                class="absolute -bottom-8 left-1/2 w-72 h-72 bg-teal-400/20 rounded-full blur-3xl animate-blob animation-delay-4000"
            ></div>
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <!-- Welcome -->
            <div
                class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6 mb-8"
            >
                <div class="space-y-2">
                    <div
                        class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 backdrop-blur-sm border border-white/20 text-emerald-100 text-sm"
                    >
                        <span
                            class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"
                        ></span>
                        <span>Good morning</span>
                    </div>

                    <h1
                        class="text-3xl sm:text-4xl lg:text-5xl font-bold text-white leading-tight"
                    >
                        Welcome back,
                        <span
                            class="bg-gradient-to-r from-emerald-300 to-cyan-300 bg-clip-text text-transparent"
                        >
                            {{ userName }}
                        </span>
                        👋
                    </h1>

                    <p class="text-emerald-100/80 text-lg max-w-2xl">
                        Here's what's happening with your projects today. You
                        have 3 pending tasks.
                    </p>
                </div>

                <!-- Quick Stats -->
                <div class="flex gap-4">
                    <div
                        class="px-6 py-4 bg-white/10 backdrop-blur-md rounded-2xl border border-white/20 text-center hover:bg-white/20 transition-all duration-300 hover:scale-105"
                    >
                        <div class="text-3xl font-bold text-white">12</div>
                        <div class="text-sm text-emerald-100/70">Projects</div>
                    </div>

                    <div
                        class="px-6 py-4 bg-white/10 backdrop-blur-md rounded-2xl border border-white/20 text-center hover:bg-white/20 transition-all duration-300 hover:scale-105"
                    >
                        <div class="text-3xl font-bold text-white">85%</div>
                        <div class="text-sm text-emerald-100/70">
                            Completion
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search Bar -->
            <div class="relative group">
                <div
                    class="absolute -inset-1 bg-gradient-to-r from-emerald-400 via-teal-400 to-cyan-400 rounded-2xl blur opacity-25 group-hover:opacity-50 transition duration-1000 group-hover:duration-200"
                ></div>

                <div
                    class="relative bg-white/95 dark:bg-gray-800/95 backdrop-blur-xl rounded-2xl shadow-2xl p-2 flex items-center gap-2 border border-white/20"
                >
                    <div class="flex-1 flex items-center px-4">
                        <i class="fas fa-search w-6 h-6 text-gray-400 mr-3"></i>

                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Search for projects, tasks, or team members..."
                            class="w-full py-4 text-gray-700 dark:text-gray-200 bg-transparent border-none outline-none placeholder-gray-400 text-lg"
                        />
                    </div>

                    <div class="flex items-center gap-1 pr-2">
                        <button
                            class="p-3 text-gray-600 dark:text-gray-300 hover:bg-gradient-to-br hover:from-emerald-500 hover:to-cyan-500 hover:text-white rounded-xl transition-all duration-300 hover:scale-110 hover:shadow-lg"
                        >
                            <i class="fas fa-plus w-5 h-5"></i>
                        </button>
                        <button
                            class="p-3 text-gray-600 dark:text-gray-300 hover:bg-gradient-to-br hover:from-emerald-500 hover:to-cyan-500 hover:text-white rounded-xl transition-all duration-300 hover:scale-110 hover:shadow-lg"
                        >
                            <i class="fas fa-globe w-5 h-5"></i>
                        </button>
                        <button
                            class="p-3 text-gray-600 dark:text-gray-300 hover:bg-gradient-to-br hover:from-emerald-500 hover:to-cyan-500 hover:text-white rounded-xl transition-all duration-300 hover:scale-110 hover:shadow-lg"
                        >
                            <i class="fas fa-clock w-5 h-5"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="mt-6 flex flex-wrap gap-3">
                <button
                    class="px-4 py-2 bg-white/10 backdrop-blur-sm border border-white/20 rounded-xl text-white text-sm font-medium hover:bg-white/20 transition-all duration-300 hover:scale-105 flex items-center gap-2"
                >
                    <i class="fas fa-plus w-4 h-4"></i>
                    New Project
                </button>

                <button
                    class="px-4 py-2 bg-white/10 backdrop-blur-sm border border-white/20 rounded-xl text-white text-sm font-medium hover:bg-white/20 transition-all duration-300 hover:scale-105 flex items-center gap-2"
                >
                    <i class="fas fa-user-plus w-4 h-4"></i>
                    Invite Team
                </button>

                <button
                    class="px-4 py-2 bg-white/10 backdrop-blur-sm border border-white/20 rounded-xl text-white text-sm font-medium hover:bg-white/20 transition-all duration-300 hover:scale-105 flex items-center gap-2"
                >
                    <i class="fas fa-chart-bar w-4 h-4"></i>
                    View Reports
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from "vue";

defineProps({
    userName: String,
});

const searchQuery = ref("");
</script>
