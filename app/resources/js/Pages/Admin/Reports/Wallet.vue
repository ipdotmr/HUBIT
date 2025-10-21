<script setup>
import { Head } from '@inertiajs/vue3';
import WhmcsAdminLayout from '@/Layouts/WhmcsAdminLayout.vue';

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

    <WhmcsAdminLayout>
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="mb-0">Wallet Report</h3>
                    <p class="text-muted">Overview of wallet transactions and balances</p>
                </div>
                <button @click="exportCSV" class="btn btn-primary">
                    <i class="fas fa-download me-2"></i>Export CSV
                </button>
            </div>

            <!-- Stats Cards -->
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="text-muted small">Total Transactions</div>
                            <h2 class="mb-0 mt-2">{{ data.total_transactions }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="text-muted small">Total Credits</div>
                            <h2 class="mb-0 mt-2 text-success">${{ data.total_credits?.toFixed(2) || '0.00' }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="text-muted small">Total Debits</div>
                            <h2 class="mb-0 mt-2 text-danger">${{ data.total_debits?.toFixed(2) || '0.00' }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="text-muted small">Net Flow</div>
                            <h2 class="mb-0 mt-2 text-primary">${{ ((data.total_credits || 0) - (data.total_debits || 0)).toFixed(2) }}</h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Transactions by Type</h5>
                        </div>
                        <div class="card-body">
                            <div v-for="(count, type) in data.by_type" :key="type" class="d-flex justify-content-between mb-2">
                                <span class="text-capitalize">{{ type }}</span>
                                <strong>{{ count }}</strong>
                            </div>
                            <div v-if="Object.keys(data.by_type || {}).length === 0" class="text-muted text-center py-3">
                                No data available
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Daily Volume</h5>
                        </div>
                        <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                            <div v-for="(volume, date) in data.daily_volume" :key="date" class="d-flex justify-content-between mb-2">
                                <span>{{ date }}</span>
                                <strong>${{ volume.toFixed(2) }}</strong>
                            </div>
                            <div v-if="Object.keys(data.daily_volume || {}).length === 0" class="text-muted text-center py-3">
                                No data available
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Transactions Table -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Recent Transactions</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Client</th>
                                    <th>Type</th>
                                    <th class="text-end">Amount</th>
                                    <th>Description</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="txn in data.recent_transactions" :key="txn.id">
                                    <td><strong>#{{ txn.id }}</strong></td>
                                    <td>{{ txn.wallet?.client?.company_name || txn.wallet?.client?.contact_name || 'N/A' }}</td>
                                    <td>
                                        <span class="badge" :class="{
                                            'bg-success': txn.type === 'credit',
                                            'bg-danger': txn.type === 'debit'
                                        }">
                                            {{ txn.type }}
                                        </span>
                                    </td>
                                    <td class="text-end" :class="{
                                        'text-success': txn.type === 'credit',
                                        'text-danger': txn.type === 'debit'
                                    }">
                                        <strong>{{ txn.type === 'credit' ? '+' : '-' }}${{ txn.amount }}</strong>
                                    </td>
                                    <td>{{ txn.description || 'N/A' }}</td>
                                    <td>{{ new Date(txn.created_at).toLocaleDateString() }}</td>
                                </tr>
                                <tr v-if="!data.recent_transactions || data.recent_transactions.length === 0">
                                    <td colspan="6" class="text-center text-muted py-4">No recent transactions</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </WhmcsAdminLayout>
</template>

<style scoped>
.card {
    transition: transform 0.2s, box-shadow 0.2s;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
}
</style>
