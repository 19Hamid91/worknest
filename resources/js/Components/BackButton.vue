<script setup>
import { Link } from "@inertiajs/inertia-vue3";
defineProps({ href: String });
</script>

<template>
    <Link
        :href="href"
        class="w-10 h-10 rounded-full bg-slate-600 hover:bg-slate-500 flex justify-center items-center"
    >
        <i class="pi pi-arrow-left text-white"></i>
    </Link>
</template>
