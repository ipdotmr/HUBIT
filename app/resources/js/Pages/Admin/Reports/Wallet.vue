<script setup>
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    data: Object,
    filters: Object,
});

const exportCSV = () => {
    window.location.href = route('managit.reports.wallet.export', props.filters);
};
</script>

<template>
    <Head title="Wallet Report" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Wallet Report
                </h2>
                <button
                    @click="exportCSV"
                    class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                >
                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Export CSV
                </button>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-6">
                <div class="grid grid-cols-1 gap-6 md:grid-cols-4">
                    <div class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
                        <div class="p-6">
                            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Transactions</div>
                            <div class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">{{ data.total_transactions }}</div>
                        </div>
                    </div>

                    <div class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
                        <div class="p-6">
                            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Credits</div>
                            <div class="mt-2 text-3xl font-bold text-green-600 dark:text-green-400">${{ data.total_credits?.toFixed(2) || '0.00' }}</div>
                        </div>
                    </div>

                    <div class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
                        <div class="p-6">
                            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Debits</div>
                            <div class="mt-2 text-3xl font-bold text-red-600 dark:text-red-400">${{ data.total_debits?.toFixed(2) || '0.00' }}</div>
                        </div>
                    </div>

                    <div class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
                        <div class="p-6">
                            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Net Flow</div>
                            <div class="mt-2 text-3xl font-bold text-indigo-600 dark:text-indigo-400">
                                ${{ ((data.total_credits || 0) - (data.total_debits || 0)).toFixed(2) }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Transactions by Type</h3>
                            <div class="mt-4 space-y-3">
                                <div v-for="(count, type) in data.by_type" :key="type" class="flex justify-between">
                                    <span class="text-sm text-gray-600 dark:text-gray-400 capitalize">{{ type }}</span>
                                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ count }}</span>
                                </div>
                                <div v-if="Object.keys(data.by_type).length === 0" class="text-sm text-gray-500">
                                    No data available
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Daily Volume</h3>
                            <div class="mt-4 space-y-2 max-h-64 overflow-y-auto">
                                <div v-for="(volume, date) in data.daily_volume" :key="date" class="flex justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">{{ date }}</span>
                                    <span class="font-medium text-gray-900 dark:text-gray-100">${{ volume.toFixed(2) }}</span>
                                </div>
                                <div v-if="Object.keys(data.daily_volume || {}).length === 0" class="text-sm text-gray-500">
                                    No data available
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="mb-4 text-lg font-medium text-gray-900 dark:text-gray-100">Recent Transactions</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-900">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">ID</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Client</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Type</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Amount</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Description</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Date</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                                    <tr v-for="txn in data.recent_transactions" :key="txn.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900 dark:text-gray-100">#{{ txn.id }}</td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                                            {{ txn.wallet?.client?.company_name || txn.wallet?.client?.contact_name || 'N/A' }}
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm">
                                            <span :class="{
                                                'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200': txn.type === 'credit',
                                                'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200': txn.type === 'debit',
                                            }" class="inline-flex rounded-full px-2 text-xs font-semibold leading-5 capitalize">
                                                {{ txn.type }}
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium" :class="{
                                            'text-green-600 dark:text-green-400': txn.type === 'credit',
                                            'text-red-600 dark:text-red-400': txn.type === 'debit',
                                        }">
                                            {{ txn.type === 'credit' ? '+' : '-' }}${{ txn.amount }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                                            {{ txn.description || 'N/A' }}
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                            {{ new Date(txn.created_at).toLocaleDateString() }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
