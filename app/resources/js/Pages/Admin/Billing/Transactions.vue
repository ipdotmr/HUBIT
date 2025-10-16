<template>
    <Head title="Payment Transactions - Admin" />
    <AuthenticatedLayout>
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-semibold text-gray-900">Payment Transactions</h2>
                    <p class="mt-1 text-sm text-gray-600">Review and approve offline payment submissions.</p>
                </div>
            </div>

            <div class="bg-white shadow rounded-lg p-4 mb-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status</label>
                        <select v-model="filters.status" class="mt-1 block w-full rounded-md border-gray-300">
                            <option value="">All</option>
                            <option value="pending">Pending</option>
                            <option value="under_review">Under Review</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Method</label>
                        <select v-model="filters.method" class="mt-1 block w-full rounded-md border-gray-300">
                            <option value="">All</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="cash">Cash</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reference</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Client</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Invoice</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Method</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="txn in transactions.data" :key="txn.id">
                            <td class="px-6 py-4 text-sm font-mono text-gray-900">{{ txn.meta?.reference }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ txn.client?.company_name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ txn.invoice?.number }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ txn.amount }} {{ txn.currency }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ txn.method }}</td>
                            <td class="px-6 py-4">
                                <span :class="{
                                    'bg-yellow-100 text-yellow-800': txn.status === 'pending',
                                    'bg-blue-100 text-blue-800': txn.status === 'under_review',
                                    'bg-green-100 text-green-800': txn.status === 'approved',
                                    'bg-red-100 text-red-800': txn.status === 'rejected'
                                }" class="px-2 py-1 text-xs rounded-full">
                                    {{ txn.status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <button v-if="txn.status === 'pending' || txn.status === 'under_review'" 
                                        @click="reviewTransaction(txn, true)" 
                                        class="text-green-600 hover:text-green-800 mr-3">
                                    Approve
                                </button>
                                <button v-if="txn.status === 'pending' || txn.status === 'under_review'" 
                                        @click="reviewTransaction(txn, false)" 
                                        class="text-red-600 hover:text-red-800">
                                    Reject
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    transactions: Object,
    filters: Object
});

const filters = ref(props.filters || {});

const reviewTransaction = (txn, approve) => {
    if (!confirm(`${approve ? 'Approve' : 'Reject'} this transaction?`)) return;
    
    router.post(route('managit.transactions.review', txn.id), {
        approve: approve,
        note: ''
    });
};
</script>
