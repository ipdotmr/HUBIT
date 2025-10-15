<template>
    <Head title="Payment Accounts - Settings" />
    <AdminLayout>
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-semibold text-gray-900">Payment Accounts</h2>
                    <p class="mt-1 text-sm text-gray-600">Manage bank transfer and cash payment accounts for offline payments.</p>
                </div>
                <button @click="showCreateModal = true" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Add Account
                </button>
            </div>

            <div class="bg-white shadow rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Currency</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Details</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Enabled</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="account in accounts" :key="account.id">
                            <td class="px-6 py-4 text-sm text-gray-900">{{ account.type }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ account.currency }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                <div v-if="account.type === 'bank_transfer'">
                                    <div>{{ account.details.account_name }}</div>
                                    <div class="text-xs text-gray-500">{{ account.details.bank_name }} - {{ account.details.account_number }}</div>
                                </div>
                                <div v-else>{{ account.details.location }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span :class="account.enabled ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" 
                                      class="px-2 py-1 text-xs rounded-full">
                                    {{ account.enabled ? 'Enabled' : 'Disabled' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <button @click="editAccount(account)" class="text-blue-600 hover:text-blue-800 mr-3">Edit</button>
                                <button @click="deleteAccount(account)" class="text-red-600 hover:text-red-800">Delete</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    accounts: Array,
    currencies: Array,
    settings: Object
});

const showCreateModal = ref(false);
const editAccount = (account) => alert('Edit: ' + account.id);
const deleteAccount = (account) => confirm('Delete ' + account.type + '?');
</script>
