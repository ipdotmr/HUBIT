<script setup>
import { Head, Link } from '@inertiajs/vue3';
import WhmcsAdminLayout from '@/Layouts/WhmcsAdminLayout.vue';

const props = defineProps({
    client: Object,
    services: Array,
    invoices: Array
});
</script>

<template>
    <Head :title="`Client: ${client.name}`" />
    
    <WhmcsAdminLayout>
        <div class="container-fluid">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="mb-0">{{ client.name }}</h3>
                    <p class="text-muted">Client ID: #{{ client.id }}</p>
                </div>
                <div>
                    <Link :href="route('managit.clients.edit', client.id)" class="btn btn-primary me-2">
                        <i class="fas fa-edit me-2"></i>Edit Client
                    </Link>
                    <Link :href="route('managit.clients.index')" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Clients
                    </Link>
                </div>
            </div>

            <div class="row g-4">
                <!-- Client Info -->
                <div class="col-md-8">
                    <!-- Contact Information -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-0"><i class="fas fa-user me-2"></i>Contact Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted small">Name</label>
                                    <div><strong>{{ client.name }}</strong></div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted small">Email</label>
                                    <div><strong>{{ client.email }}</strong></div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted small">Company</label>
                                    <div>{{ client.company || 'N/A' }}</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted small">Phone</label>
                                    <div>{{ client.phone || 'N/A' }}</div>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="text-muted small">Address</label>
                                    <div>{{ client.address || 'N/A' }}</div>
                                    <div v-if="client.city || client.state || client.postcode">
                                        {{ client.city }}, {{ client.state }} {{ client.postcode }}
                                    </div>
                                    <div v-if="client.country">{{ client.country }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Services -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-0"><i class="fas fa-server me-2"></i>Services ({{ services.length }})</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ID</th>
                                            <th>Product</th>
                                            <th>Billing Cycle</th>
                                            <th>Next Due</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="service in services" :key="service.id">
                                            <td><strong>#{{ service.id }}</strong></td>
                                            <td>{{ service.product_name }}</td>
                                            <td>{{ service.billing_cycle }}</td>
                                            <td>{{ service.next_due_date || 'N/A' }}</td>
                                            <td>
                                                <span class="badge" :class="{
                                                    'bg-success': service.status === 'active',
                                                    'bg-warning': service.status === 'pending',
                                                    'bg-danger': service.status === 'suspended'
                                                }">
                                                    {{ service.status }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr v-if="services.length === 0">
                                            <td colspan="5" class="text-center text-muted py-4">No services</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Invoices -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0"><i class="fas fa-file-invoice me-2"></i>Recent Invoices ({{ invoices.length }})</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ID</th>
                                            <th>Total</th>
                                            <th>Due Date</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="invoice in invoices" :key="invoice.id">
                                            <td><strong>#{{ invoice.id }}</strong></td>
                                            <td><strong>${{ invoice.total }}</strong></td>
                                            <td>{{ invoice.due_date || 'N/A' }}</td>
                                            <td>
                                                <span class="badge" :class="{
                                                    'bg-success': invoice.status === 'paid',
                                                    'bg-warning': invoice.status === 'unpaid',
                                                    'bg-danger': invoice.status === 'overdue'
                                                }">
                                                    {{ invoice.status }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr v-if="invoices.length === 0">
                                            <td colspan="4" class="text-center text-muted py-4">No invoices</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-md-4">
                    <!-- Stats -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Overview</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="text-muted small">Status</label>
                                <div>
                                    <span class="badge" :class="{
                                        'bg-success': client.status === 'active',
                                        'bg-danger': client.status === 'inactive',
                                        'bg-warning': client.status === 'suspended'
                                    }">
                                        {{ client.status }}
                                    </span>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="text-muted small">Balance</label>
                                <div><strong class="text-primary">${{ client.balance }}</strong></div>
                            </div>
                            <div class="mb-3">
                                <label class="text-muted small">Member Since</label>
                                <div>{{ client.created_at }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Stats -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Quick Stats</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Services</span>
                                <strong>{{ client.services_count }}</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Domains</span>
                                <strong>{{ client.domains_count }}</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Invoices</span>
                                <strong>{{ client.invoices_count }}</strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Tickets</span>
                                <strong>{{ client.tickets_count }}</strong>
                            </div>
                        </div>
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
