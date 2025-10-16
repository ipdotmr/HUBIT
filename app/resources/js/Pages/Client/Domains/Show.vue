<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, defineAsyncComponent } from 'vue';

const props = defineProps({
    domain: Object,
    renewalPricing: Object,
});

const activeTab = ref('overview');

const tabs = [
    { id: 'overview', label: 'domains.tab_overview', component: defineAsyncComponent(() => import('./Components/OverviewTab.vue')) },
    { id: 'nameservers', label: 'domains.tab_nameservers', component: defineAsyncComponent(() => import('./Components/NameserversTab.vue')) },
    { id: 'dns', label: 'domains.tab_dns', component: defineAsyncComponent(() => import('./Components/DnsRecordsTab.vue')) },
    { id: 'privacy', label: 'domains.tab_privacy_lock', component: defineAsyncComponent(() => import('./Components/PrivacyLockTab.vue')) },
    { id: 'whois', label: 'domains.tab_whois', component: defineAsyncComponent(() => import('./Components/WhoisTab.vue')) },
    { id: 'billing', label: 'domains.tab_billing', component: defineAsyncComponent(() => import('./Components/BillingTab.vue')) },
];

const getStatusColor = (status) => {
    const colors = {
        active: 'bg-green-100 text-green-800',
        pending: 'bg-yellow-100 text-yellow-800',
        expired: 'bg-red-100 text-red-800',
        suspended: 'bg-gray-100 text-gray-800',
    };
    return colors[status] || 'bg-gray-100 text-gray-800';
};
</script>

<template>
    <Head :title="`${$t('domains.manage')} - ${domain.domain}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <Link
                        :href="route('client.domains.index')"
                        class="mb-2 inline-flex items-center text-sm text-gray-600 hover:text-gray-900"
                    >
                        <svg class="mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        {{ $t('domains.back_to_list') }}
                    </Link>
                    <h2 class="text-xl font-semibold leading-tight text-gray-800">
                        {{ domain.domain }}
                    </h2>
                </div>
                <span
                    :class="getStatusColor(domain.status)"
                    class="inline-flex rounded-full px-3 py-1 text-sm font-semibold"
                >
                    {{ $t(`domains.status_${domain.status}`) }}
                </span>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="mb-6 border-b border-gray-200 bg-white shadow-sm sm:rounded-t-lg">
                    <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                        <button
                            v-for="tab in tabs"
                            :key="tab.id"
                            type="button"
                            :class="[
                                activeTab === tab.id
                                    ? 'border-blue-500 text-blue-600'
                                    : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700',
                                'whitespace-nowrap border-b-2 px-1 py-4 text-sm font-medium focus:outline-none',
                            ]"
                            @click="activeTab = tab.id"
                        >
                            {{ $t(tab.label) }}
                        </button>
                    </nav>
                </div>

                <div class="overflow-hidden bg-white shadow-sm sm:rounded-b-lg">
                    <div class="p-6">
                        <Suspense>
                            <template #default>
                                <component
                                    :is="tabs.find(t => t.id === activeTab).component"
                                    :domain="domain"
                                    :renewal-pricing="renewalPricing"
                                />
                            </template>
                            <template #fallback>
                                <div class="flex items-center justify-center py-12">
                                    <div class="h-8 w-8 animate-spin rounded-full border-b-2 border-t-2 border-blue-500"></div>
                                    <span class="ml-3 text-gray-600">{{ $t('common.loading') }}</span>
                                </div>
                            </template>
                        </Suspense>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
