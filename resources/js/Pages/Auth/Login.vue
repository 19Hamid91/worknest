<script setup>
import { watch } from "vue";
import { Head, Link, useForm } from "@inertiajs/inertia-vue3";
import BreezeGuestLayout from "@/Layouts/Guest.vue";
import Button from "primevue/button";
import Checkbox from "primevue/checkbox";
import Message from "primevue/message";
import InputText from "primevue/inputtext";
import FloatLabel from "primevue/floatlabel";

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

Object.keys(form.data()).forEach((field) => {
    watch(
        () => form[field],
        () => {
            if (form.errors[field]) {
                form.errors[field] = null;
            }
        }
    );
});
</script>

<template>
    <BreezeGuestLayout>
        <Head title="Log in" />

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
                        :invalid="!!form.errors.email"
                        fluid
                    />
                    <label for="email">Email</label>
                    <Message
                        v-if="form.errors.email"
                        severity="error"
                        size="small"
                        variant="simple"
                        >{{ form.errors.email }}</Message
                    >
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
                        :invalid="!!form.errors.password"
                        fluid
                    />
                    <label for="password">Password</label>
                    <Message
                        v-if="form.errors.password"
                        severity="error"
                        size="small"
                        variant="simple"
                        >{{ form.errors.password }}</Message
                    >
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
                    type="submit"
                    @click="submit"
                >
                    Log in
                </Button>
            </div>
        </form>
    </BreezeGuestLayout>
</template>
