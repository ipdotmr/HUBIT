<template>
    <Head title="Payment Transactions - Admin" />
    <WhmcsAdminLayout>
        <div class="container-fluid">
            <!-- Page Title -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="mb-0">Payment Transactions</h3>
                    <p class="text-muted">Review and approve offline payment submissions</p>
                </div>
            </div>

            <!-- Filters -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Status</label>
                            <select v-model="filters.status" class="form-select">
                                <option value="">All Statuses</option>
                                <option value="pending">Pending</option>
                                <option value="under_review">Under Review</option>
                                <option value="approved">Approved</option>
                                <option value="rejected">Rejected</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Method</label>
                            <select v-model="filters.method" class="form-select">
                                <option value="">All Methods</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="cash">Cash</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transactions Table -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Transactions</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Reference</th>
                                    <th>Client</th>
                                    <th>Invoice</th>
                                    <th>Amount</th>
                                    <th>Method</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="txn in transactions.data" :key="txn.id">
                                    <td><code>{{ txn.meta?.reference }}</code></td>
                                    <td>{{ txn.client?.company_name }}</td>
                                    <td>{{ txn.invoice?.number }}</td>
                                    <td><strong>{{ txn.amount }} {{ txn.currency }}</strong></td>
                                    <td>{{ txn.method }}</td>
                                    <td>
                                        <span class="badge" :class="{
                                            'bg-warning': txn.status === 'pending',
                                            'bg-info': txn.status === 'under_review',
                                            'bg-success': txn.status === 'approved',
                                            'bg-danger': txn.status === 'rejected'
                                        }">
                                            {{ txn.status }}
                                        </span>
                                    </td>
                                    <td>
                                        <button 
                                            v-if="txn.status === 'pending' || txn.status === 'under_review'" 
                                            @click="reviewTransaction(txn, true)" 
                                            class="btn btn-sm btn-success me-2">
                                            <i class="fas fa-check me-1"></i>Approve
                                        </button>
                                        <button 
                                            v-if="txn.status === 'pending' || txn.status === 'under_review'" 
                                            @click="reviewTransaction(txn, false)" 
                                            class="btn btn-sm btn-danger">
                                            <i class="fas fa-times me-1"></i>Reject
                                        </button>
                                    </td>
                                </tr>
                                <tr v-if="!transactions.data || transactions.data.length === 0">
                                    <td colspan="7" class="text-center text-muted py-4">
                                        No transactions found
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </WhmcsAdminLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import WhmcsAdminLayout from '@/Layouts/WhmcsAdminLayout.vue';

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
