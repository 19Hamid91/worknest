<script setup>
import { ref } from "vue";
import { Head, useForm } from "@inertiajs/inertia-vue3";
import Authenticated from "@/Layouts/Authenticated.vue";
import BackButton from "@/Components/BackButton.vue";
import {
    Message,
    Toast,
    InputText,
    Button,
    FileUpload,
    InputNumber,
    DataTable,
    Column,
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
    logs: {
        type: Object,
        default: null,
    },
    isEdit: {
        type: Boolean,
        default: false,
    },
    isDisabled: {
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

const formatDate = (date) => {
    if (!date) return "-";
    return new Date(date).toLocaleString();
};

function parseJson(str) {
    try {
        return JSON.parse(str);
    } catch (e) {
        return {};
    }
}
</script>

<template>
    <Authenticated>
        <Head
            :title="
                isDisabled
                    ? 'Detail User'
                    : isEdit
                    ? 'Edit User'
                    : 'Create User'
            "
        />
        <Toast />
        <div class="max-w-7xl mx-auto sm:px-4 lg:px-6 flex flex-col gap-4">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg px-2">
                <div class="flex gap-6 w-full p-4 items-center">
                    <BackButton :href="route('user.index')" />

                    <h2 class="text-2xl">
                        {{
                            isDisabled
                                ? "Detail User"
                                : isEdit
                                ? "Edit User"
                                : "Create User"
                        }}
                    </h2>
                </div>
            </div>
            <form @submit.prevent="submit" multi>
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
                            v-show="!isDisabled"
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
                            <InputText
                                id="name"
                                v-model="form.name"
                                :disabled="isDisabled"
                            />
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
                            <InputText
                                id="username"
                                v-model="form.username"
                                :disabled="isDisabled"
                            />
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
                            <InputText
                                id="email"
                                v-model="form.email"
                                :disabled="isDisabled"
                            />
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
                            <InputText
                                id="phone"
                                v-model="form.phone"
                                :useGrouping="false"
                                :disabled="isDisabled"
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
                                :disabled="isDisabled"
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
                                :disabled="isDisabled"
                            />
                        </div>
                    </div>
                    <div class="mt-4 flex w-full justify-end pe-6">
                        <Button
                            v-show="!isDisabled"
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
            <div
                v-show="isDisabled"
                class="bg-white overflow-hidden shadow-sm sm:rounded-lg px-2"
            >
                <div class="p-4">
                    <h4 class="text-xl">Logs</h4>
                </div>
                <div class="w-full px-4">
                    <DataTable
                        :value="logs"
                        :rowsPerPageOptions="[5, 10, 20, 50]"
                        paginator
                        removableSort
                        :rows="5"
                        :totalRecords="logs?.length || 0"
                        showGridlines
                        stripedRows
                        scrollable
                        :size="'small'"
                    >
                        <template #empty>
                            <div class="flex justify-center">No log found.</div>
                        </template>
                        <template #loading>
                            <div
                                class="flex items-center gap-2 p-4 text-slate-300"
                            >
                                <i class="pi pi-spin pi-spinner text-lg"></i>
                                Loading data. Please wait...
                            </div>
                        </template>
                        <Column field="id" header="#" sortable></Column>
                        <Column field="name" header="User" sortable>
                            <template #body="{ data }">
                                {{ data.user.name }}
                            </template>
                        </Column>
                        <Column field="event" header="Event" sortable></Column>
                        <Column
                            field="old_values"
                            header="Old"
                            class="bg-red-200"
                        >
                            <template #body="{ data }">
                                <div v-if="data.old_values">
                                    <div
                                        v-for="(value, key) in parseJson(
                                            data.old_values
                                        )"
                                        :key="key"
                                    >
                                        <strong>{{ key }}:</strong> {{ value }}
                                    </div>
                                </div>
                                <div v-else>
                                    <span>no data</span>
                                </div>
                            </template>
                        </Column>
                        <Column
                            field="new_value"
                            header="New"
                            class="bg-green-200"
                        >
                            <template #body="{ data }">
                                <div v-if="data.new_values">
                                    <div
                                        v-for="(value, key) in parseJson(
                                            data.new_values
                                        )"
                                        :key="key"
                                    >
                                        <strong>{{ key }}:</strong> {{ value }}
                                    </div>
                                </div>
                                <div v-else>
                                    <span>no data</span>
                                </div>
                            </template>
                        </Column>
                        <Column field="created_at" header="Changed At" sortable>
                            <template #body="{ data }">
                                {{ formatDate(data.created_at) }}
                            </template>
                        </Column>
                    </DataTable>
                </div>
            </div>
        </div>
    </Authenticated>
</template>
