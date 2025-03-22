<script setup>
import { ref } from "vue";
import { Head, Link, useForm } from "@inertiajs/inertia-vue3";
import Authenticated from "@/Layouts/Authenticated.vue";
import BackButton from "@/Components/BackButton.vue";
import {
    Message,
    Toast,
    InputText,
    Button,
    FileUpload,
    InputNumber,
} from "primevue";
import { useToast } from "primevue/usetoast";

const toast = useToast();

const props = defineProps({
    user: {
        type: Object,
        default: () => ({
            id: null,
            name: "",
            username: "",
            email: "",
            phone: "",
            photo: "",
        }),
    },
    isEdit: {
        type: Boolean,
        default: false,
    },
    defaultImage: {
        type: String,
        default: null,
    },
});

const src = ref(props.user.photo_url || props.defaultImage);

const form = useForm({
    id: props.user.id,
    name: props.user.name,
    username: props.user.username,
    email: props.user.email,
    phone: props.user.phone,
    photo: null,
    password: "",
    password_confirmation: "",
});

const submit = () => {
    if (props.isEdit) {
        form.post(route("user.update", form.id), {
            headers: { "X-HTTP-Method-Override": "PATCH" },
            forceFormData: true,
            onSuccess: () => {
                toast.add({
                    severity: "success",
                    summary: "Success",
                    detail: "User updated!",
                    life: 3000,
                });
            },
            onError: (errors) => {
                toast.add({
                    severity: "error",
                    summary: "Error",
                    detail: "Failed to update user",
                    life: 3000,
                });
            },
        });
    } else {
        form.post(route("user.store"), {
            forceFormData: true,
            onSuccess: () => {
                toast.add({
                    severity: "success",
                    summary: "Success",
                    detail: "User created!",
                    life: 3000,
                });
                form.reset();
            },
            onError: (errors) => {
                toast.add({
                    severity: "error",
                    summary: "Error",
                    detail: "Failed to create user",
                    life: 3000,
                });
                form.reset(["password", "password_confirmation"]);
            },
        });
    }
};

const onFileSelect = (event) => {
    const file = event.files[0];
    const reader = new FileReader();

    reader.onload = async (e) => {
        src.value = e.target.result;
    };

    form.photo = file;
    reader.readAsDataURL(file);
};
</script>

<template>
    <Authenticated>
        <Head :title="isEdit ? 'Edit User' : 'Create User'" />
        <Toast />
        <div class="max-w-7xl mx-auto sm:px-4 lg:px-6 flex flex-col gap-4">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg px-2">
                <div class="flex gap-6 w-full p-4 items-center">
                    <BackButton :href="route('user.index')" />

                    <h2 class="text-2xl">
                        {{ isEdit ? "Edit User" : "Create User" }}
                    </h2>
                </div>
            </div>
            <form @submit.prevent="submit" :user="user" :isEdit="isEdit" multi>
                <div class="bg-white shadow-sm sm:rounded-lg p-4">
                    <div
                        class="flex flex-col justify-center items-center gap-2"
                    >
                        <img
                            v-if="src"
                            :src="src"
                            alt="Image"
                            class="shadow-md w-32 h-32 rounded-full object-cover"
                        />
                        <FileUpload
                            mode="basic"
                            @select="onFileSelect"
                            customUpload
                            auto
                            severity="secondary"
                            class="p-button-outlined"
                            accept="image/*"
                            :maxFileSize="2000000"
                        />
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-6">
                        <div class="flex flex-col gap-2">
                            <label for="name">Name</label>
                            <InputText id="name" v-model="form.name" />
                            <Message
                                v-if="form.errors.name"
                                size="small"
                                severity="error"
                                variant="simple"
                            >
                                {{ form.errors.name }}
                            </Message>
                        </div>
                        <div class="flex flex-col gap-2">
                            <label for="username">Username</label>
                            <InputText id="username" v-model="form.username" />
                            <Message
                                v-if="form.errors.username"
                                size="small"
                                severity="error"
                                variant="simple"
                            >
                                {{ form.errors.username }}
                            </Message>
                        </div>
                        <div class="flex flex-col gap-2">
                            <label for="email">Email</label>
                            <InputText id="email" v-model="form.email" />
                            <Message
                                v-if="form.errors.email"
                                size="small"
                                severity="error"
                                variant="simple"
                            >
                                {{ form.errors.email }}
                            </Message>
                        </div>
                        <div class="flex flex-col gap-2">
                            <label for="phone">Phone</label>
                            <InputNumber
                                id="phone"
                                v-model="form.phone"
                                :useGrouping="false"
                            />
                            <Message
                                v-if="form.errors.phone"
                                size="small"
                                severity="error"
                                variant="simple"
                            >
                                {{ form.errors.phone }}
                            </Message>
                        </div>
                        <div v-show="!isEdit" class="flex flex-col gap-2">
                            <label for="password">Password</label>
                            <InputText
                                id="password"
                                type="password"
                                v-model="form.password"
                            />
                            <Message
                                v-if="form.errors.password"
                                size="small"
                                severity="error"
                                variant="simple"
                                >{{ form.errors.password }}</Message
                            >
                        </div>
                        <div v-show="!isEdit" class="flex flex-col gap-2">
                            <label for="password_confirmation"
                                >Confirm Password</label
                            >
                            <InputText
                                id="password_confirmation"
                                type="password"
                                v-model="form.password_confirmation"
                            />
                        </div>
                    </div>
                    <div class="mt-4 flex w-full justify-end pe-6">
                        <Button
                            type="submit"
                            :loading="form.processing"
                            icon="pi pi-save"
                            iconPos="left"
                            loadingIcon="pi pi-spin pi-spinner"
                            @click="submit"
                            :label="isEdit ? 'Update' : 'Save'"
                        />
                    </div>
                </div>
            </form>
        </div>
    </Authenticated>
</template>
