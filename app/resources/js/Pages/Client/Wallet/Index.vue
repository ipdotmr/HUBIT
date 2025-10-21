<script setup>
import { ref } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import OsenLayout from '@/Layouts/OsenLayout.vue';

const props = defineProps({
    balance: Number,
    transactions: Object,
    filters: Object,
});

const filters = ref({
    type: props.filters?.type || '',
});

const addFundsForm = useForm({
    amount: '',
});

const search = () => {
    router.get(route('client.wallet.index'), filters.value, {
        preserveState: true,
        preserveScroll: true,
    });
};

const clearFilters = () => {
    filters.value = { type: '' };
    search();
};

const transactionBadge = (type) => {
    const badges = {
        credit: 'text-success',
        debit: 'text-danger',
        refund: 'text-info',
    };
    return badges[type] || badges.credit;
};
</script>

<template>
    <Head title="My Wallet" />

    <OsenLayout>
        <template #header>
            <h4 class="page-title">My Wallet</h4>
            <p class="text-muted">Manage your account balance and transactions</p>
        </template>

        <div class="row">
            <div class="col-xxl-4">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="avatar-lg mx-auto mb-3">
                            <span class="avatar-title bg-success-subtle rounded-circle fs-22">
                                <i class="ti ti-wallet text-success"></i>
                            </span>
                        </div>
                        <h5 class="text-muted fs-13 text-uppercase mb-2">Current Balance</h5>
                        <h2 class="mb-3 fw-bold">${{ balance?.toFixed(2) || '0.00' }}</h2>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addFundsModal">
                            <i class="ti ti-plus me-1"></i> Add Funds
                        </button>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h4 class="header-title">Quick Actions</h4>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button class="btn btn-light text-start" data-bs-toggle="modal" data-bs-target="#addFundsModal">
                                <i class="ti ti-plus me-2"></i> Add Funds
                            </button>
                            <Link :href="route('client.invoices.index')" class="btn btn-light text-start">
                                <i class="ti ti-file-invoice me-2"></i> View Invoices
                            </Link>
                            <Link :href="route('client.wallet.transactions')" class="btn btn-light text-start">
                                <i class="ti ti-history me-2"></i> Transaction History
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xxl-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="header-title">Filter Transactions</h4>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Transaction Type</label>
                                <select
                                    v-model="filters.type"
                                    class="form-select"
                                    @change="search"
                                >
                                    <option value="">All Types</option>
                                    <option value="credit">Credit</option>
                                    <option value="debit">Debit</option>
                                    <option value="refund">Refund</option>
                                </select>
                            </div>

                            <div class="col-md-6 d-flex align-items-end gap-2">
                                <button @click="search" class="btn btn-primary">
                                    <i class="ti ti-search me-1"></i> Search
                                </button>
                                <button @click="clearFilters" class="btn btn-light">
                                    <i class="ti ti-x me-1"></i> Clear
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="header-title">Recent Transactions</h4>
                        <span class="badge bg-primary">{{ transactions.total }} Total</span>
                    </div>
                    <div class="card-body p-0">
                        <div v-if="transactions.data.length === 0" class="text-center py-5 text-muted">
                            <i class="ti ti-history fs-48 mb-3 d-block"></i>
                            <p>No transactions yet.</p>
                        </div>

                        <div v-else class="table-responsive">
                            <table class="table table-custom table-centered table-nowrap table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Description</th>
                                        <th>Type</th>
                                        <th class="text-end">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="transaction in transactions.data" :key="transaction.id">
                                        <td>
                                            <span class="text-muted">{{ transaction.created_at ? new Date(transaction.created_at).toLocaleDateString() : 'N/A' }}</span>
                                        </td>
                                        <td>
                                            <div>
                                                <h5 class="fs-14 mb-0">{{ transaction.description }}</h5>
                                                <span class="text-muted fs-12">{{ transaction.reference || 'N/A' }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <i class="ti ti-circle-filled fs-12" :class="transactionBadge(transaction.type)"></i>
                                            <span class="text-capitalize">{{ transaction.type }}</span>
                                        </td>
                                        <td class="text-end">
                                            <span class="fw-semibold" :class="{
                                                'text-success': transaction.type === 'credit',
                                                'text-danger': transaction.type === 'debit'
                                            }">
                                                {{ transaction.type === 'credit' ? '+' : '-' }}${{ transaction.amount }}
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div v-if="transactions.links.length > 3" class="card-footer">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-muted">
                                    Showing {{ transactions.from }} to {{ transactions.to }} of {{ transactions.total }} results
                                </div>
                                <nav>
                                    <ul class="pagination pagination-sm mb-0">
                                        <li
                                            v-for="(link, index) in transactions.links"
                                            :key="index"
                                            class="page-item"
                                            :class="{ 'active': link.active, 'disabled': !link.url }"
                                        >
                                            <Link
                                                v-if="link.url"
                                                :href="link.url"
                                                class="page-link"
                                                v-html="link.label"
                                            ></Link>
                                            <span v-else class="page-link" v-html="link.label"></span>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </OsenLayout>
</template>
