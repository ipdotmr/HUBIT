<script setup>
import { ref } from 'vue';
import { router, useForm } from '@inertiajs/vue3';

const props = defineProps({
    service: Object,
});

const showPasswordReset = ref(false);
const passwordForm = useForm({
    password: '',
});

const resetPassword = () => {
    passwordForm.post(route('client.services.reset-password', props.service.id), {
        preserveScroll: true,
        onSuccess: () => {
            passwordForm.reset();
            showPasswordReset.value = false;
        },
    });
};

const syncService = () => {
    router.post(route('client.services.sync', props.service.id), {}, {
        preserveScroll: true,
    });
};

const suspendService = () => {
    if (confirm('Are you sure you want to suspend this service?')) {
        router.post(route('client.services.suspend', props.service.id), {}, {
            preserveScroll: true,
        });
    }
};

const unsuspendService = () => {
    router.post(route('client.services.unsuspend', props.service.id), {}, {
        preserveScroll: true,
    });
};
</script>

<template>
    <div class="space-y-6">
        <div class="rounded-lg border border-gray-200 p-6 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Reset Password</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Change your service password. Password must be at least 12 characters.
            </p>

            <div v-if="!showPasswordReset" class="mt-4">
                <button
                    @click="showPasswordReset = true"
                    :disabled="service.status !== 'active'"
                    class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50"
                >
                    Reset Password
                </button>
            </div>

            <form v-else @submit.prevent="resetPassword" class="mt-4 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">New Password</label>
                    <input
                        v-model="passwordForm.password"
                        type="password"
                        required
                        minlength="12"
                        maxlength="64"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 sm:text-sm"
                        placeholder="Enter new password (min 12 characters)"
                    />
                    <p v-if="passwordForm.errors.password" class="mt-1 text-sm text-red-600">{{ passwordForm.errors.password }}</p>
                </div>

                <div class="flex gap-3">
                    <button
                        type="submit"
                        :disabled="passwordForm.processing"
                        class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50"
                    >
                        {{ passwordForm.processing ? 'Resetting...' : 'Confirm Reset' }}
                    </button>
                    <button
                        @click="showPasswordReset = false; passwordForm.reset()"
                        type="button"
                        class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700"
                    >
                        Cancel
                    </button>
                </div>
            </form>
        </div>

        <div class="rounded-lg border border-gray-200 p-6 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Sync Service</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Synchronize service data with the provisioner to get the latest usage and status information.
            </p>
            <div class="mt-4">
                <button
                    @click="syncService"
                    :disabled="!['active', 'suspended'].includes(service.status)"
                    class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50"
                >
                    Sync Now
                </button>
            </div>
        </div>

        <div v-if="service.status === 'active'" class="rounded-lg border border-orange-200 bg-orange-50 p-6 dark:border-orange-800 dark:bg-orange-900/20">
            <h3 class="text-lg font-medium text-orange-900 dark:text-orange-300">Suspend Service</h3>
            <p class="mt-1 text-sm text-orange-700 dark:text-orange-400">
                Temporarily suspend this service. You can unsuspend it later.
            </p>
            <div class="mt-4">
                <button
                    @click="suspendService"
                    class="rounded-md bg-orange-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2"
                >
                    Suspend Service
                </button>
            </div>
        </div>

        <div v-if="service.status === 'suspended'" class="rounded-lg border border-green-200 bg-green-50 p-6 dark:border-green-800 dark:bg-green-900/20">
            <h3 class="text-lg font-medium text-green-900 dark:text-green-300">Unsuspend Service</h3>
            <p class="mt-1 text-sm text-green-700 dark:text-green-400">
                Reactivate this service to restore full functionality.
            </p>
            <div class="mt-4">
                <button
                    @click="unsuspendService"
                    class="rounded-md bg-green-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
                >
                    Unsuspend Service
                </button>
            </div>
        </div>
    </div>
</template>
