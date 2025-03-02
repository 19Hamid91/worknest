<script setup>
import Button from "primevue/button";

import Checkbox from "primevue/checkbox";
import BreezeGuestLayout from "@/Layouts/Guest.vue";
import InputText from "primevue/inputtext";
import FloatLabel from "primevue/floatlabel";
import BreezeValidationErrors from "@/Components/ValidationErrors.vue";
import { Head, Link, useForm } from "@inertiajs/inertia-vue3";

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    email: "",
    password: "",
    remember: false,
});

const submit = () => {
    form.post(route("login"), {
        onFinish: () => form.reset("password"),
    });
};
</script>

<template>
    <BreezeGuestLayout>
        <Head title="Log in" />

        <BreezeValidationErrors class="mb-4" />

        <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="mt-4">
            <div>
                <FloatLabel variant="on">
                    <InputText
                        id="email"
                        type="email"
                        class="mt-1 block w-full"
                        v-model="form.email"
                        required
                        autofocus
                        autocomplete="username"
                    />
                    <label for="email">Email</label>
                </FloatLabel>
            </div>

            <div class="mt-4">
                <FloatLabel variant="on">
                    <InputText
                        id="password"
                        type="password"
                        class="mt-1 block w-full"
                        v-model="form.password"
                        required
                        autocomplete="current-password"
                    />
                    <label for="password">Password</label>
                </FloatLabel>
            </div>

            <div class="block mt-4">
                <label class="flex items-center">
                    <Checkbox
                        name="remember"
                        v-model:checked="form.remember"
                        binary
                    />
                    <span class="ml-2 text-sm text-gray-600">Remember me</span>
                </label>
            </div>

            <div class="flex items-center justify-end mt-4">
                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="underline text-sm text-gray-600 hover:text-gray-900"
                >
                    Forgot your password?
                </Link>

                <Button
                    class="ml-4"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Log in
                </Button>
            </div>
        </form>
    </BreezeGuestLayout>
</template>
