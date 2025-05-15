<script setup>
import { ref, onMounted, nextTick } from "vue";
import { Head, Link } from "@inertiajs/inertia-vue3";
import Authenticated from "@/Layouts/Authenticated.vue";
import {
    Button,
    DataTable,
    Column,
    IconField,
    InputIcon,
    InputText,
    Toast,
    Tag,
    Popover,
    RadioButton,
} from "primevue";
import { useToast } from "primevue/usetoast";
defineProps({
    statuses: Array,
});

const toast = useToast();
const data = ref([]);
const totalRecords = ref(0);
const loading = ref(false);
const lazyParams = ref({});
const rows = ref(10);
const searchValue = ref(null);
let searchTimeout = null;
const pop = ref();
const selectedProject = ref();

const fetchData = async () => {
    loading.value = true;
    try {
        const params = {
            page: (lazyParams.value.page || 0) + 1,
            rows: lazyParams.value.rows || rows.value,
            sortField: lazyParams.value.sortField || "id",
            sortOrder: lazyParams.value.sortOrder || 1,
            search: searchValue.value || "",
        };

        const response = await fetch(
            `/api/project/all?${new URLSearchParams(params)}`
        );
        const result = await response.json();

        data.value = result.data;
        totalRecords.value = result.total;
    } catch (error) {
        toast.add({
            severity: "error",
            summary: "Error",
            detail: "Failed to load data",
            life: 3000,
        });
    } finally {
        loading.value = false;
    }
};

const onPage = (event) => {
    lazyParams.value = event;
    fetchData();
};

const onSort = (event) => {
    lazyParams.value = event;
    fetchData();
};

const onSearch = () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        fetchData();
    }, 500);
};

const formatDate = (date) => {
    const options = {
        year: "numeric",
        month: "long",
        day: "numeric",
    };

    if (!date) return "-";
    return new Date(date).toLocaleDateString("en-US", options);
};

const getSeverity = (status) => {
    switch (status) {
        case "Pending":
            return "danger";

        case "Done":
            return "success";

        case "On Progress":
            return "info";

        case "renewal":
            return null;
    }
};

const displayStatus = (event, project) => {
    pop.value.hide();

    if (selectedProject.value?.id === project.id) {
        selectedProject.value = null;
        hidePopover();
    } else {
        selectedProject.value = project;

        nextTick(() => {
            pop.value.show(event);
        });
    }
};

const updateStatus = async (id, status) => {
    try {
        const response = await fetch(`/api/project/changeStatus`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({
                id: id,
                status: status,
            }),
        });

        const result = await response.json();

        toast.add({
            severity: "success",
            summary: "Updated",
            detail: result.message,
            life: 3000,
        });
    } catch (error) {
        console.log("error: " + error);
        toast.add({
            severity: "error",
            summary: "Error",
            detail: "Failed to change status",
            life: 3000,
        });
    } finally {
        hidePopover();
    }
};

const hidePopover = () => {
    pop.value.hide();
};

const truncateText = (text, maxLength = 150) => {
    return text.length > maxLength ? text.slice(0, maxLength) + "..." : text;
};

onMounted(() => {
    fetchData();
});
</script>

<template>
    <Authenticated>
        <Head title="Project" />
        <Toast />
        <div class="max-w-7xl mx-auto sm:px-4 lg:px-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="flex flex-col gap-4 w-full p-6">
                    <div class="flex w-full justify-between items-center">
                        <h1 class="text-2xl">Project</h1>
                        <Link :href="route('project.create')">
                            <Button> Add Project </Button>
                        </Link>
                    </div>
                    <div class="w-full">
                        <DataTable
                            :value="data"
                            :rowsPerPageOptions="[5, 10, 20, 50]"
                            lazy
                            paginator
                            removableSort
                            :rows="rows"
                            :totalRecords="totalRecords"
                            showGridlines
                            stripedRows
                            scrollable
                            :loading="loading"
                            :size="'small'"
                            @page="onPage($event)"
                            @sort="onSort($event)"
                        >
                            <template #header>
                                <div class="flex justify-end">
                                    <IconField>
                                        <InputIcon>
                                            <i class="pi pi-search" />
                                        </InputIcon>
                                        <InputText
                                            v-model="searchValue"
                                            placeholder="Global Search"
                                            @input="onSearch"
                                        />
                                    </IconField>
                                </div>
                            </template>
                            <template #empty>
                                <div class="flex justify-center">
                                    No data found.
                                </div>
                            </template>
                            <template #loading>
                                <div
                                    class="flex items-center gap-2 p-4 text-slate-300"
                                >
                                    <i
                                        class="pi pi-spin pi-spinner text-lg"
                                    ></i>
                                    Loading data. Please wait...
                                </div>
                            </template>
                            <Column header="No">
                                <template #body="{ index }">
                                    {{
                                        index +
                                        1 +
                                        (lazyParams.page || 0) *
                                            (lazyParams.rows || rows)
                                    }}
                                </template>
                            </Column>
                            <Column
                                field="name"
                                header="Name"
                                sortable
                            ></Column>
                            <Column field="start_date" header="Start" sortable>
                                <template #body="{ data }">
                                    {{ formatDate(data.start_date) }}
                                </template>
                            </Column>
                            <Column field="end_date" header="End" sortable>
                                <template #body="{ data }">
                                    {{ formatDate(data.end_date) }}
                                </template>
                            </Column>
                            <Column field="description" header="Description">
                                <template #body="{ data }">
                                    {{ truncateText(data.description, 70) }}
                                </template>
                            </Column>
                            <Column field="status" header="Status" sortable>
                                <template #body="slotProps">
                                    <Tag
                                        :value="slotProps.data.status"
                                        :severity="
                                            getSeverity(slotProps.data.status)
                                        "
                                    />
                                </template>
                            </Column>
                            <Column header="Actions" style="min-width: 12rem">
                                <template #body="slotProps">
                                    <Link
                                        :href="
                                            route(
                                                'project.show',
                                                slotProps.data.id
                                            )
                                        "
                                    >
                                        <Button
                                            icon="pi pi-eye"
                                            class="mr-2"
                                            outlined
                                            rounded
                                            severity="info"
                                            v-tooltip.top="'Detail project'"
                                        />
                                    </Link>
                                    <Link
                                        :href="
                                            route(
                                                'project.edit',
                                                slotProps.data.id
                                            )
                                        "
                                    >
                                        <Button
                                            icon="pi pi-pencil"
                                            class="mr-2"
                                            outlined
                                            rounded
                                            severity="warn"
                                            v-tooltip.top="'Edit project'"
                                        />
                                    </Link>
                                    <Button
                                        type="button"
                                        @click="
                                            displayStatus(
                                                $event,
                                                slotProps.data
                                            )
                                        "
                                        icon="pi pi-sync"
                                        severity="help"
                                        outlined
                                        rounded
                                        v-tooltip.top="'Change project status'"
                                    ></Button>
                                </template>
                            </Column>
                        </DataTable>

                        <!-- popover -->
                        <Popover ref="pop">
                            <div class="rounded flex flex-col gap-2">
                                <div
                                    class="flex flex-col justify-between items-start gap-2 mb-4"
                                >
                                    <div
                                        v-for="(status, index) in statuses"
                                        :key="index"
                                        class="flex items-center gap-2"
                                    >
                                        <RadioButton
                                            name="statuss"
                                            :value="status"
                                            v-model="selectedProject.status"
                                            :inputId="status"
                                        />
                                        <Tag
                                            :severity="getSeverity(status)"
                                            :for="index"
                                            >{{ status }}</Tag
                                        >
                                    </div>
                                </div>
                                <div class="flex gap-2">
                                    <Button
                                        icon="pi pi-save"
                                        :label="'Save'"
                                        class="flex-auto whitespace-nowrap"
                                        @click="
                                            updateStatus(
                                                selectedProject?.id,
                                                selectedProject?.status
                                            )
                                        "
                                    ></Button>
                                </div>
                            </div>
                        </Popover>
                    </div>
                </div>
            </div>
        </div>
    </Authenticated>
</template>
