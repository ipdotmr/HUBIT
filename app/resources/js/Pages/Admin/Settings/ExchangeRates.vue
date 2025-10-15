<template>
    <Head title="Exchange Rates - Settings" />
    <AuthenticatedLayout>
        <div class="space-y-6">
            <div>
                <h2 class="text-2xl font-semibold text-gray-900">Exchange Rates</h2>
                <p class="mt-1 text-sm text-gray-600">View and manage currency exchange rates for multi-currency billing.</p>
            </div>

            <div class="bg-white shadow rounded-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium">Current Rates</h3>
                    <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Refresh Rates
                    </button>
                </div>

                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pair</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rate</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Updated</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="rate in rates" :key="rate.id">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                {{ rate.base }} → {{ rate.quote }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ rate.rate }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ formatDate(rate.fetched_at) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    rates: Array,
    settings: Object,
    auditLogs: Array
});

const formatDate = (date) => new Date(date).toLocaleString();
</script>
