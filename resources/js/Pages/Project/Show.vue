<script setup>
import Form from "./Form.vue";
defineProps({
    project: Object,
    logs: Object,
});
</script>

<template>
    <Form
        :isDisabled="true"
        :project="project"
        :logs="logs"
        :defaultImage="defaultImage"
    />
</template>
