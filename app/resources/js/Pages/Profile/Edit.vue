<script setup>
import OsenLayout from '@/Layouts/OsenLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
</script>

<template>
    <Head title="Profile" />

    <OsenLayout>
        <template #header>
            <h4 class="page-title">Profile Settings</h4>
            <p class="text-muted">Manage your account information and security</p>
        </template>

        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <h4 class="header-title">Profile Information</h4>
                    </div>
                    <div class="card-body">
                        <UpdateProfileInformationForm
                            :must-verify-email="mustVerifyEmail"
                            :status="status"
                        />
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h4 class="header-title">Update Password</h4>
                    </div>
                    <div class="card-body">
                        <UpdatePasswordForm />
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h4 class="header-title">Delete Account</h4>
                    </div>
                    <div class="card-body">
                        <DeleteUserForm />
                    </div>
                </div>
            </div>
        </div>
    </OsenLayout>
</template>
