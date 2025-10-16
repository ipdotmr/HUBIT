<template>
    <Head title="Localization - Settings" />
    <AuthenticatedLayout>
        <div class="space-y-6">
            <div>
                <h2 class="text-2xl font-semibold text-gray-900">Localization & Currencies</h2>
                <p class="mt-1 text-sm text-gray-600">Configure supported currencies, default language, and regional settings.</p>
            </div>

            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium mb-4">Currencies</h3>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Decimals</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="currency in currencies" :key="currency.code">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ currency.code }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ currency.name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ currency.decimals }}</td>
                            <td class="px-6 py-4">
                                <span :class="currency.enabled ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'" 
                                      class="px-2 py-1 text-xs rounded-full">
                                    {{ currency.enabled ? 'Enabled' : 'Disabled' }}
                                </span>
                            </td>
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
    currencies: Array,
    settings: Object,
    auditLogs: Array
});
</script>
