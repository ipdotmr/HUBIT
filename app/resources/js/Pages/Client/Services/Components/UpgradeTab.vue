<script setup>
import { ref } from 'vue';
import { router, useForm } from '@inertiajs/vue3';

const props = defineProps({
    service: Object,
    upgradePlans: Array,
});

const selectedPlan = ref(null);

const form = useForm({
    product_id: null,
});

const selectPlan = (plan) => {
    selectedPlan.value = plan;
    form.product_id = plan.id;
};

const submitUpgrade = () => {
    form.post(route('client.services.upgrade', props.service.id), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            selectedPlan.value = null;
        },
    });
};
</script>

<template>
    <div>
        <div v-if="!upgradePlans || upgradePlans.length === 0" class="text-center py-8">
            <p class="text-gray-500 dark:text-gray-400">No upgrade plans available at this time.</p>
        </div>

        <div v-else>
            <div class="mb-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Available Upgrade Plans</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Select a plan to upgrade your service. You'll be charged the prorated difference.
                </p>
            </div>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                <div
                    v-for="plan in upgradePlans"
                    :key="plan.id"
                    @click="selectPlan(plan)"
                    :class="[
                        'cursor-pointer rounded-lg border-2 p-6 transition-all',
                        selectedPlan?.id === plan.id
                            ? 'border-indigo-600 bg-indigo-50 dark:border-indigo-400 dark:bg-indigo-900/20'
                            : 'border-gray-200 hover:border-indigo-300 dark:border-gray-700 dark:hover:border-indigo-700',
                    ]"
                >
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ plan.name }}</h4>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">{{ plan.description }}</p>
                    <div class="mt-4">
                        <div class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">
                            ${{ plan.pricing?.total || '0.00' }}
                        </div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            Prorated charge today
                        </div>
                    </div>
                    <div v-if="selectedPlan?.id === plan.id" class="mt-4">
                        <svg class="h-6 w-6 text-indigo-600 dark:text-indigo-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>
            </div>

            <div v-if="selectedPlan" class="mt-8">
                <div class="rounded-lg bg-yellow-50 p-4 dark:bg-yellow-900/20">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-300">Upgrade Confirmation</h3>
                            <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-400">
                                <p>You are about to upgrade to <strong>{{ selectedPlan.name }}</strong>.</p>
                                <p class="mt-1">An invoice will be generated for ${{ selectedPlan.pricing?.total || '0.00' }} (prorated amount).</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button
                        @click="selectedPlan = null"
                        type="button"
                        class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700"
                    >
                        Cancel
                    </button>
                    <button
                        @click="submitUpgrade"
                        :disabled="form.processing"
                        class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50"
                    >
                        <span v-if="form.processing">Processing...</span>
                        <span v-else>Proceed to Upgrade</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
