<script setup>
import Form from "./Form.vue";
defineProps({
    project: Object,
});
</script>

<template>
    <Form :isEdit="true" :project="project" :defaultImage="defaultImage" />
</template>
