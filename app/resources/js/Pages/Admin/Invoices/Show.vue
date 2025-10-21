<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import WhmcsAdminLayout from '@/Layouts/WhmcsAdminLayout.vue';

const props = defineProps({
    invoice: Object
});

const deleteInvoice = () => {
    if (confirm('Are you sure you want to delete this invoice?')) {
        router.delete(route('managit.invoices.destroy', props.invoice.id));
    }
};
</script>

<template>
    <Head :title="`Invoice #${invoice.id}`" />
    
    <WhmcsAdminLayout>
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="mb-0">Invoice #{{ invoice.id }}</h3>
                    <p class="text-muted mb-0">{{ invoice.client_name }}</p>
                </div>
                <div>
                    <Link :href="route('managit.invoices.edit', invoice.id)" class="btn btn-primary me-2">
                        <i class="fas fa-edit me-2"></i>Edit
                    </Link>
                    <button @click="deleteInvoice" class="btn btn-danger me-2">
                        <i class="fas fa-trash me-2"></i>Delete
                    </button>
                    <Link :href="route('managit.invoices.index')" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back
                    </Link>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-md-8">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0"><i class="fas fa-file-invoice me-2"></i>Invoice Details</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="text-muted small">Client</label>
                                    <div><strong>{{ invoice.client_name }}</strong></div>
                                    <div class="text-muted">{{ invoice.client_email }}</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small">Status</label>
                                    <div>
                                        <span class="badge" :class="{
                                            'bg-success': invoice.status === 'paid',
                                            'bg-warning': invoice.status === 'unpaid',
                                            'bg-danger': invoice.status === 'cancelled',
                                            'bg-secondary': invoice.status === 'refunded'
                                        }">
                                            {{ invoice.status }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="text-muted small">Invoice Date</label>
                                    <div>{{ invoice.date }}</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small">Due Date</label>
                                    <div>{{ invoice.due_date }}</div>
                                </div>
                            </div>

                            <hr />

                            <h6 class="mb-3">Invoice Items</h6>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Description</th>
                                            <th class="text-end">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(item, index) in invoice.items" :key="index">
                                            <td>{{ item.description }}</td>
                                            <td class="text-end">${{ parseFloat(item.amount).toFixed(2) }}</td>
                                        </tr>
                                        <tr v-if="!invoice.items || invoice.items.length === 0">
                                            <td colspan="2" class="text-center text-muted">No items</td>
                                        </tr>
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr>
                                            <th>Total</th>
                                            <th class="text-end">${{ invoice.total }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Quick Actions</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <button class="btn btn-success" v-if="invoice.status === 'unpaid'">
                                    <i class="fas fa-check me-2"></i>Mark as Paid
                                </button>
                                <button class="btn btn-primary">
                                    <i class="fas fa-envelope me-2"></i>Send to Client
                                </button>
                                <button class="btn btn-secondary">
                                    <i class="fas fa-download me-2"></i>Download PDF
                                </button>
                                <button class="btn btn-info">
                                    <i class="fas fa-print me-2"></i>Print Invoice
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </WhmcsAdminLayout>
</template>
