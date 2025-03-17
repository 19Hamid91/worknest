<script setup>
import { ref, onMounted, watch } from "vue";
import Authenticated from "@/Layouts/Authenticated.vue";
import Button from "primevue/button";
import DataTable from "primevue/datatable";
import Column from "primevue/column";
import IconField from "primevue/iconfield";
import InputIcon from "primevue/inputicon";
import InputText from "primevue/inputtext";
import { useToast } from "primevue/usetoast";
import Toast from "primevue/toast";

const toast = useToast();
const users = ref([]);
const totalRecords = ref(0);
const loading = ref(false);
const lazyParams = ref({});
const rows = ref(10);
const searchValue = ref(null);
let searchTimeout = null;

const fetchUsers = async () => {
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
            `/api/user/all?${new URLSearchParams(params)}`
        );
        const result = await response.json();
        console.log(result.data);
        users.value = result.data;
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
    fetchUsers();
};

const onSort = (event) => {
    lazyParams.value = event;
    fetchUsers();
};

const onSearch = () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        fetchUsers();
    }, 500);
};

const formatDate = (date) => {
    if (!date) return "-";
    return new Date(date).toLocaleString();
};

const editUser = (user) => {
    console.log("Edit user", user);
};

const confirmDelete = (id) => {
    if (confirm("Are you sure you want to delete this user?")) {
        console.log("Delete user ID:", id);
    }
};

onMounted(() => {
    fetchUsers();
});
</script>

<template>
    <Authenticated>
        <Toast />
        <div class="max-w-7xl mx-auto sm:px-4 lg:px-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="flex flex-col gap-4 w-full p-6">
                    <div class="flex w-full justify-between items-center">
                        <h1 class="text-2xl">User</h1>
                        <Button>Add User</Button>
                    </div>
                    <div class="w-full">
                        <DataTable
                            :value="users"
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
                            <Column field="id" header="#" sortable></Column>
                            <Column
                                field="name"
                                header="Name"
                                sortable
                            ></Column>
                            <Column
                                field="username"
                                header="Username"
                                sortable
                            ></Column>
                            <Column
                                field="email"
                                header="Email"
                                sortable
                            ></Column>
                            <Column
                                field="phone"
                                header="Phone"
                                sortable
                            ></Column>
                            <Column
                                field="last_login_at"
                                header="Last Login"
                                sortable
                            >
                                <template #body="{ data }">
                                    {{ formatDate(data.last_login_at) }}
                                </template>
                            </Column>
                            <Column header="Actions">
                                <template #body="{ data }">
                                    <Button
                                        icon="pi pi-pencil"
                                        class="p-button-text p-button-sm"
                                        @click="editUser(data)"
                                    />
                                    <Button
                                        icon="pi pi-trash"
                                        class="p-button-text p-button-sm p-button-danger"
                                        @click="confirmDelete(data.id)"
                                    />
                                </template>
                            </Column>
                        </DataTable>
                    </div>
                </div>
            </div>
        </div>
    </Authenticated>
</template>
