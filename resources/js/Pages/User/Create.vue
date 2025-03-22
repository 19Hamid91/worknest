<script setup>
import Form from "./Form.vue";

const props = defineProps({
    defaultImage: {
        type: String,
        default: null,
    },
});
</script>

<template>
    <Form :defaultImage="defaultImage" />
</template>
