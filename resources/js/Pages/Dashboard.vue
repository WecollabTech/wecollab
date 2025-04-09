<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import Welcome from "@/Components/Welcome.vue";
</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            Dashboard
            <!-- El título heredará los estilos del <h2> en AppLayout -->
        </template>

        <div class="py-6">
            <div class="max-w-8xl mx-auto sm:px-8 lg:px-8">
                <div
                    class="-mt-1 bg-white border border-gray-200 shadow-xl rounded-lg p-1 z-10"
                >
                    <Welcome />
                    <!-- Agregar el nuevo componente DashboardInfo -->
                </div>
            </div>
        </div>
    </AppLayout>
</template>
