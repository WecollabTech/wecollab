<template>
    <div class="pt-2 pb-3 space-y-1">
        <ResponsiveNavLink
            :href="route('dashboard')"
            :active="route().current('dashboard')"
        >
            Dashboard
        </ResponsiveNavLink>
    </div>

    <!-- Responsive Settings Options -->
    <div class="pt-4 pb-1 border-t border-gray-200">
        <div class="flex items-center px-4">
            <div
                v-if="$page.props.jetstream.managesProfilePhotos"
                class="shrink-0 me-3"
            >
                <img
                    class="size-10 rounded-full object-cover"
                    :src="$page.props.auth.user.profile_photo_url"
                    :alt="$page.props.auth.user.name"
                />
            </div>

            <div>
                <div class="font-medium text-base text-gray-800">
                    {{ $page.props.auth.user.name }}
                </div>
                <div class="font-medium text-sm text-green-500">
                    {{ $page.props.auth.user.email }}
                </div>
            </div>
        </div>

        <div class="mt-3 space-y-1">
            <ResponsiveNavLink
                :href="route('profile.show')"
                :active="route().current('profile.show')"
            >
                <div class="flex items-center gap-x-2">
                    <i class="fas fa-user text-lg"></i>
                    <span class="font-sans font-bold text-[15px]">Perfil</span>
                </div>
            </ResponsiveNavLink>

            <ResponsiveNavLink
                v-if="$page.props.jetstream.hasApiFeatures"
                :href="route('api-tokens.index')"
                :active="route().current('api-tokens.index')"
            >
                API Tokens
            </ResponsiveNavLink>

            <!-- Authentication -->
            <form method="POST" @submit.prevent="logout">
                <ResponsiveNavLink as="button"
                    >Cerrar Sesión
                </ResponsiveNavLink>
            </form>
            <Sidebar :isOpen="true" :isMobile="false" class="hidden lg:block" />
        </div>
    </div>
</template>

<script setup>
import DropdownLink from "@/Components/DropdownLink.vue";
import { router } from "@inertiajs/vue3";

const logout = () => {
    router.post(route("logout"));
};
</script>
