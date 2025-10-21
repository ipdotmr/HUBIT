<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import WhmcsAdminLayout from '@/Layouts/WhmcsAdminLayout.vue';

const props = defineProps({
    invoices: Object,
    filters: Object
});

const search = ref(props.filters.search || '');
const status = ref(props.filters.status || '');

const filterInvoices = () => {
    router.get(route('managit.invoices.index'), {
        search: search.value,
        status: status.value
    }, {
        preserveState: true,
        preserveScroll: true
    });
};
</script>

<template>
    <Head title="Invoices" />
    
    <WhmcsAdminLayout>
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="mb-0">Invoices</h3>
                <Link :href="route('managit.invoices.create')" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Create Invoice
                </Link>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <input 
                                v-model="search" 
                                type="text" 
                                class="form-control" 
                                placeholder="Search invoices..."
                                @input="filterInvoices"
                            >
                        </div>
                        <div class="col-md-4">
                            <select v-model="status" class="form-select" @change="filterInvoices">
                                <option value="">All Status</option>
                                <option value="unpaid">Unpaid</option>
                                <option value="paid">Paid</option>
                                <option value="cancelled">Cancelled</option>
                                <option value="refunded">Refunded</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button @click="search = ''; status = ''; filterInvoices();" class="btn btn-secondary w-100">
                                Clear
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Invoice #</th>
                                    <th>Client</th>
                                    <th>Date</th>
                                    <th>Due Date</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="invoice in invoices.data" :key="invoice.id">
                                    <td><strong>#{{ invoice.id }}</strong></td>
                                    <td>{{ invoice.client_name }}</td>
                                    <td>{{ invoice.date }}</td>
                                    <td>{{ invoice.due_date }}</td>
                                    <td><strong>${{ invoice.total }}</strong></td>
                                    <td>
                                        <span class="badge" :class="{
                                            'bg-success': invoice.status === 'paid',
                                            'bg-warning': invoice.status === 'unpaid',
                                            'bg-danger': invoice.status === 'cancelled',
                                            'bg-secondary': invoice.status === 'refunded'
                                        }">
                                            {{ invoice.status }}
                                        </span>
                                    </td>
                                    <td>
                                        <Link :href="route('managit.invoices.show', invoice.id)" class="btn btn-sm btn-primary me-1">
                                            <i class="fas fa-eye"></i>
                                        </Link>
                                        <Link :href="route('managit.invoices.edit', invoice.id)" class="btn btn-sm btn-secondary">
                                            <i class="fas fa-edit"></i>
                                        </Link>
                                    </td>
                                </tr>
                                <tr v-if="invoices.data.length === 0">
                                    <td colspan="7" class="text-center text-muted py-4">
                                        No invoices found
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div v-if="invoices.links && invoices.links.length > 3" class="card-footer bg-white">
                    <nav>
                        <ul class="pagination mb-0 justify-content-center">
                            <li v-for="link in invoices.links" :key="link.label" 
                                class="page-item" 
                                :class="{ active: link.active, disabled: !link.url }">
                                <Link v-if="link.url" :href="link.url" class="page-link" v-html="link.label"></Link>
                                <span v-else class="page-link" v-html="link.label"></span>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </WhmcsAdminLayout>
</template>
