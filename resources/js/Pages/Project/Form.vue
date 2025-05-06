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
    Textarea,
    DatePicker,
} from "primevue";
import { useToast } from "primevue/usetoast";

const toast = useToast();

const props = defineProps({
    project: {
        type: Object,
        default: () => ({
            id: null,
            name: "",
            start_date: "",
            end_date: "",
            description: "",
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
});

const form = useForm({
    id: props.project.id,
    name: props.project.name,
    start_date: props.project.start_date,
    end_date: props.project.end_date,
    description: props.project.description,
});

const submit = () => {
    if (props.isEdit) {
        form.post(route("project.update", form.id), {
            headers: { "X-HTTP-Method-Override": "PATCH" },
            forceFormData: true,
            onSuccess: () => {
                toast.add({
                    severity: "success",
                    summary: "Success",
                    detail: "Project updated!",
                    life: 3000,
                });
            },
            onError: (errors) => {
                toast.add({
                    severity: "error",
                    summary: "Error",
                    detail: "Failed to update project",
                    life: 3000,
                });
            },
        });
    } else {
        form.post(route("project.store"), {
            forceFormData: true,
            onSuccess: () => {
                toast.add({
                    severity: "success",
                    summary: "Success",
                    detail: "Project created!",
                    life: 3000,
                });
                form.reset();
            },
            onError: (errors) => {
                toast.add({
                    severity: "error",
                    summary: "Error",
                    detail: "Failed to create project",
                    life: 3000,
                });
            },
        });
    }
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
                    ? 'Detail Project'
                    : isEdit
                    ? 'Edit Project'
                    : 'Create Project'
            "
        />
        <Toast />
        <div class="max-w-7xl mx-auto sm:px-4 lg:px-6 flex flex-col gap-4">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg px-2">
                <div class="flex gap-6 w-full p-4 items-center">
                    <BackButton :href="route('project.index')" />

                    <h2 class="text-2xl">
                        {{
                            isDisabled
                                ? "Detail Project"
                                : isEdit
                                ? "Edit Project"
                                : "Create Project"
                        }}
                    </h2>
                </div>
            </div>
            <form @submit.prevent="submit" multi>
                <div class="bg-white shadow-sm sm:rounded-lg p-4">
                    <div class="flex flex-col w-full gap-6 p-2 md:p-6">
                        <div class="flex flex-col gap-2">
                            <label for="name">Project Title</label>
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
                        <div
                            class="flex flex-col w-full md:flex-row gap-6 md:gap-4"
                        >
                            <div class="flex flex-col gap-2 w-full">
                                <label for="start_date">Start Date</label>
                                <DatePicker
                                    dateFormat="dd/mm/yy"
                                    id="start_date"
                                    v-model="form.start_date"
                                    :disabled="isDisabled"
                                />
                                <Message
                                    v-if="form.errors.start_date"
                                    size="small"
                                    severity="error"
                                    variant="simple"
                                >
                                    {{ form.errors.start_date }}
                                </Message>
                            </div>
                            <div class="flex flex-col gap-2 w-full">
                                <label for="end_date">End Date</label>
                                <DatePicker
                                    dateFormat="dd/mm/yy"
                                    id="end_date"
                                    v-model="form.end_date"
                                    :disabled="isDisabled"
                                />
                                <Message
                                    v-if="form.errors.end_date"
                                    size="small"
                                    severity="error"
                                    variant="simple"
                                >
                                    {{ form.errors.end_date }}
                                </Message>
                            </div>
                        </div>
                        <div class="flex flex-col gap-2">
                            <label for="description">Description</label>
                            <Textarea
                                id="description"
                                v-model="form.description"
                                :useGrouping="false"
                                :disabled="isDisabled"
                            />
                            <Message
                                v-if="form.errors.description"
                                size="small"
                                severity="error"
                                variant="simple"
                            >
                                {{ form.errors.description }}
                            </Message>
                        </div>
                    </div>
                    <div class="mt-4 flex w-full justify-end md:pe-6">
                        <Button
                            class="w-full m-2 md:m-0 md:w-auto"
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
                        <Column field="name" header="Project" sortable>
                            <template #body="{ data }">
                                {{ data.Project.name }}
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
