<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import ClientLayout from '@/Layouts/ClientLayout.vue';

const props = defineProps({
    invoices: Object,
    filters: Object,
});

const filters = ref({
    status: props.filters?.status || '',
});

const search = () => {
    router.get(route('client.invoices.index'), filters.value, {
        preserveState: true,
        preserveScroll: true,
    });
};

const clearFilters = () => {
    filters.value = { status: '' };
    search();
};

const statusBadge = (status) => {
    const badges = {
        paid: 'text-success',
        open: 'text-warning',
        overdue: 'text-danger',
        cancelled: 'text-secondary',
    };
    return badges[status] || badges.open;
};
</script>

<template>
    <Head title="My Invoices" />

    <ClientLayout>
        <div class="mb-4">
            <h2>My Invoices</h2>
            <p class="text-muted">View and manage your invoices</p>
        </div>

        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <h4 class="header-title">Filter Invoices</h4>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Status</label>
                                <select
                                    v-model="filters.status"
                                    class="form-select"
                                    @change="search"
                                >
                                    <option value="">All Statuses</option>
                                    <option value="open">Open</option>
                                    <option value="paid">Paid</option>
                                    <option value="overdue">Overdue</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>

                            <div class="col-md-8 d-flex align-items-end gap-2">
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
                        <h4 class="header-title">Invoices</h4>
                        <span class="badge bg-primary">{{ invoices.total }} Total</span>
                    </div>
                    <div class="card-body p-0">
                        <div v-if="invoices.data.length === 0" class="text-center py-5 text-muted">
                            <i class="ti ti-file-invoice fs-48 mb-3 d-block"></i>
                            <p>No invoices found.</p>
                        </div>

                        <div v-else class="table-responsive">
                            <table class="table table-custom table-centered table-nowrap table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Invoice Number</th>
                                        <th>Status</th>
                                        <th>Issue Date</th>
                                        <th>Due Date</th>
                                        <th>Total</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="invoice in invoices.data" :key="invoice.id">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm flex-shrink-0 me-2">
                                                    <span class="avatar-title bg-primary-subtle rounded-circle">
                                                        <i class="ti ti-file-invoice"></i>
                                                    </span>
                                                </div>
                                                <div>
                                                    <h5 class="fs-14 mb-0">#{{ invoice.number }}</h5>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <i class="ti ti-circle-filled fs-12" :class="statusBadge(invoice.status)"></i>
                                            <span class="text-capitalize">{{ invoice.status }}</span>
                                        </td>
                                        <td>
                                            <span class="text-muted">{{ invoice.issue_date ? new Date(invoice.issue_date).toLocaleDateString() : 'N/A' }}</span>
                                        </td>
                                        <td>
                                            <span class="text-muted">{{ invoice.due_date ? new Date(invoice.due_date).toLocaleDateString() : 'N/A' }}</span>
                                        </td>
                                        <td>
                                            <span class="fw-semibold">${{ invoice.total }}</span>
                                        </td>
                                        <td class="text-end">
                                            <Link
                                                :href="route('invoices.show', invoice.id)"
                                                class="btn btn-sm btn-light"
                                            >
                                                <i class="ti ti-eye me-1"></i> View
                                            </Link>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div v-if="invoices.links.length > 3" class="card-footer">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-muted">
                                    Showing {{ invoices.from }} to {{ invoices.to }} of {{ invoices.total }} results
                                </div>
                                <nav>
                                    <ul class="pagination pagination-sm mb-0">
                                        <li
                                            v-for="(link, index) in invoices.links"
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
    </ClientLayout>
</template>
