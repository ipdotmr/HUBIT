<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    domains: Object,
    filters: Object,
});

const form = ref({
    registrar: props.filters?.registrar || '',
    status: props.filters?.status || '',
    expiring_in_days: props.filters?.expiring_in_days || '',
});

const applyFilters = () => {
    router.get(route('client.domains.index'), form.value, {
        preserveState: true,
        replace: true,
    });
};

const clearFilters = () => {
    form.value = { registrar: '', status: '', expiring_in_days: '' };
    applyFilters();
};

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
    <Head :title="$t('domains.my_domains')" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ $t('domains.my_domains') }}
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="mb-6 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    {{ $t('domains.filter_registrar') }}
                                </label>
                                <select
                                    v-model="form.registrar"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                >
                                    <option value="">
                                        {{ $t('common.all') }}
                                    </option>
                                    <option value="namecheap">Namecheap</option>
                                    <option value="namecom">Name.com</option>
                                    <option value="coccaep">COCCA EP</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    {{ $t('domains.filter_status') }}
                                </label>
                                <select
                                    v-model="form.status"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                >
                                    <option value="">
                                        {{ $t('common.all') }}
                                    </option>
                                    <option value="active">{{ $t('domains.status_active') }}</option>
                                    <option value="pending">{{ $t('domains.status_pending') }}</option>
                                    <option value="expired">{{ $t('domains.status_expired') }}</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    {{ $t('domains.filter_expiring') }}
                                </label>
                                <select
                                    v-model="form.expiring_in_days"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                >
                                    <option value="">
                                        {{ $t('common.all') }}
                                    </option>
                                    <option value="30">30 {{ $t('common.days') }}</option>
                                    <option value="60">60 {{ $t('common.days') }}</option>
                                    <option value="90">90 {{ $t('common.days') }}</option>
                                </select>
                            </div>
                            <div class="flex items-end gap-2">
                                <button
                                    type="button"
                                    class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                                    @click="applyFilters"
                                >
                                    {{ $t('common.apply') }}
                                </button>
                                <button
                                    type="button"
                                    class="rounded-md bg-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2"
                                    @click="clearFilters"
                                >
                                    {{ $t('common.clear') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    v-if="!domains.data || domains.data.length === 0"
                    class="overflow-hidden bg-white p-12 text-center shadow-sm sm:rounded-lg"
                >
                    <svg
                        class="mx-auto h-12 w-12 text-gray-400"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"
                        />
                    </svg>
                    <p class="mt-4 text-gray-500">
                        {{ $t('domains.no_domains') }}
                    </p>
                    <Link
                        :href="route('domains.search')"
                        class="mt-4 inline-block text-blue-600 hover:text-blue-700"
                    >
                        {{ $t('domains.search_domains') }}
                    </Link>
                </div>

                <div v-else class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                >
                                    {{ $t('domains.domain') }}
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                >
                                    {{ $t('domains.registrar') }}
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                >
                                    {{ $t('domains.status') }}
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                >
                                    {{ $t('domains.expires_at') }}
                                </th>
                                <th
                                    class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500"
                                >
                                    {{ $t('common.actions') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr v-for="domain in domains.data" :key="domain.id" class="hover:bg-gray-50">
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">
                                    {{ domain.domain }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                    {{ domain.registrar }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm">
                                    <span
                                        :class="getStatusColor(domain.status)"
                                        class="inline-flex rounded-full px-2 py-1 text-xs font-semibold leading-5"
                                    >
                                        {{ $t(`domains.status_${domain.status}`) }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                    {{ domain.expires_at ? new Date(domain.expires_at).toLocaleDateString() : '-' }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                                    <Link
                                        :href="route('client.domains.show', domain.id)"
                                        class="text-blue-600 hover:text-blue-900 focus:outline-none focus:underline"
                                    >
                                        {{ $t('common.manage') }}
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div
                        v-if="domains.links && domains.links.length > 3"
                        class="border-t border-gray-200 bg-white px-4 py-3 sm:px-6"
                    >
                        <div class="flex items-center justify-between">
                            <div class="flex flex-1 justify-between sm:hidden">
                                <Link
                                    v-if="domains.prev_page_url"
                                    :href="domains.prev_page_url"
                                    class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                                >
                                    {{ $t('common.previous') }}
                                </Link>
                                <Link
                                    v-if="domains.next_page_url"
                                    :href="domains.next_page_url"
                                    class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                                >
                                    {{ $t('common.next') }}
                                </Link>
                            </div>
                            <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-sm text-gray-700">
                                        {{ $t('common.showing') }}
                                        <span class="font-medium">{{ domains.from }}</span>
                                        {{ $t('common.to') }}
                                        <span class="font-medium">{{ domains.to }}</span>
                                        {{ $t('common.of') }}
                                        <span class="font-medium">{{ domains.total }}</span>
                                        {{ $t('common.results') }}
                                    </p>
                                </div>
                                <div>
                                    <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                                        <component
                                            :is="link.url ? Link : 'span'"
                                            v-for="(link, index) in domains.links"
                                            :key="index"
                                            :href="link.url"
                                            :class="[
                                                link.active
                                                    ? 'z-10 bg-blue-50 border-blue-500 text-blue-600'
                                                    : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
                                                'relative inline-flex items-center px-4 py-2 text-sm font-medium border',
                                                index === 0 ? 'rounded-l-md' : '',
                                                index === domains.links.length - 1 ? 'rounded-r-md' : '',
                                            ]"
                                            v-html="link.label"
                                        />
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
