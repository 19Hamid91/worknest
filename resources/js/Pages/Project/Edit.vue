<script setup>
import Form from "./Form.vue";
defineProps({
    user: Object,
});
</script>

<template>
    <Form :isEdit="true" :user="user" :defaultImage="defaultImage" />
</template>
