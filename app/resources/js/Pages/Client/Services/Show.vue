<script setup>
import { ref, defineAsyncComponent } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    service: Object,
    upgradePlans: Array,
    panelUrl: String,
});

const activeTab = ref('overview');

const tabs = [
    { id: 'overview', label: 'Overview', component: defineAsyncComponent(() => import('./Components/OverviewTab.vue')) },
    { id: 'upgrade', label: 'Upgrade', component: defineAsyncComponent(() => import('./Components/UpgradeTab.vue')) },
    { id: 'actions', label: 'Actions', component: defineAsyncComponent(() => import('./Components/ActionsTab.vue')) },
    { id: 'billing', label: 'Billing', component: defineAsyncComponent(() => import('./Components/BillingTab.vue')) },
];

const statusBadge = (status) => {
    const badges = {
        active: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
        suspended: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
        pending: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
        pending_suspension: 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200',
        pending_unsuspension: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
        terminated: 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200',
    };
    return badges[status] || badges.pending;
};
</script>

<template>
    <Head :title="service.product?.name || 'Service Details'" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    {{ service.product?.name || 'Service Details' }}
                </h2>
                <span :class="statusBadge(service.status)" class="inline-flex rounded-full px-3 py-1 text-xs font-semibold">
                    {{ service.status }}
                </span>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="mb-6 overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
                    <div class="border-b border-gray-200 dark:border-gray-700">
                        <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                            <button
                                v-for="tab in tabs"
                                :key="tab.id"
                                @click="activeTab = tab.id"
                                :class="[
                                    activeTab === tab.id
                                        ? 'border-indigo-500 text-indigo-600 dark:border-indigo-400 dark:text-indigo-400'
                                        : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300',
                                    'whitespace-nowrap border-b-2 px-1 py-4 text-sm font-medium transition-colors',
                                ]"
                            >
                                {{ tab.label }}
                            </button>
                        </nav>
                    </div>

                    <div class="p-6">
                        <component
                            :is="tabs.find(t => t.id === activeTab)?.component"
                            :service="service"
                            :upgradePlans="upgradePlans"
                            :panelUrl="panelUrl"
                        />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
