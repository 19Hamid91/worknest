<script setup>
import Form from "./Form.vue";
defineProps({
    user: Object,
    logs: Object,
});
</script>

<template>
    <Form
        :isEdit="true"
        :isDisabled="true"
        :user="user"
        :logs="logs"
        :defaultImage="defaultImage"
    />
</template>
