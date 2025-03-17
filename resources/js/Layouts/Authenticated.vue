<script setup>
import { ref } from "vue";
import BreezeApplicationLogo from "@/Components/ApplicationLogo.vue";
import BreezeNavLink from "@/Components/NavLink.vue";
import BreezeDropdownLink from "@/Components/DropdownLink.vue";
import { Link } from "@inertiajs/inertia-vue3";

const isSidebarOpen = ref(false);
</script>

<template>
    <div class="min-h-screen flex bg-gray-100">
        <!-- Sidebar -->
        <aside
            :class="{
                '-translate-x-full': !isSidebarOpen,
                'translate-x-0': isSidebarOpen,
            }"
            class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-gray-200 flex flex-col transform transition-transform duration-300 sm:relative sm:translate-x-0"
        >
            <!-- Logo -->
            <div class="p-2 border-b">
                <Link :href="route('dashboard')">
                    <BreezeApplicationLogo class="block h-10 w-auto mx-auto" />
                </Link>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 px-4 py-2 space-y-2">
                <BreezeNavLink
                    :href="route('dashboard')"
                    :active="route().current('dashboard')"
                >
                    Dashboard
                </BreezeNavLink>
            </nav>

            <!-- User Info & Logout -->
            <div class="p-4 border-t">
                <div class="text-gray-800 font-medium">
                    {{ $page.props.auth.user.name }}
                </div>
                <BreezeDropdownLink
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="mt-2 block text-red-500"
                >
                    Logout
                </BreezeDropdownLink>
            </div>
        </aside>

        <!-- Overlay (Agar Sidebar Bisa Ditutup dengan Klik di Luar) -->
        <div
            v-if="isSidebarOpen"
            class="fixed inset-0 bg-black bg-opacity-50 sm:hidden"
            @click="isSidebarOpen = false"
        ></div>

        <!-- Content -->
        <div class="flex-1 flex flex-col">
            <!-- Sidebar Toggle Button (Mobile) -->
            <button
                @click="isSidebarOpen = !isSidebarOpen"
                class="p-2 sm:hidden"
            >
                <svg
                    class="w-6 h-6"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M4 6h16M4 12h16M4 18h16"
                    />
                </svg>
            </button>

            <!-- Page Heading -->
            <header class="bg-white shadow px-6 py-4" v-if="$slots.header">
                <slot name="header" />
            </header>

            <!-- Page Content -->
            <main class="p-6">
                <slot />
            </main>
        </div>
    </div>
</template>
