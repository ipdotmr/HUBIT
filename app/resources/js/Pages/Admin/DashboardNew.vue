<script setup>
import { Head } from '@inertiajs/vue3';
import WhmcsAdminLayout from '@/Layouts/WhmcsAdminLayout.vue';

const props = defineProps({
    stats: Object,
    recentOrders: Array,
    recentInvoices: Array,
    recentTickets: Array,
    incomeData: Array,
    clientActivity: Array,
});

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
    }).format(amount);
};
</script>

<template>
    <Head title="Admin Dashboard" />

    <WhmcsAdminLayout>
        <div class="container-fluid">
            <!-- Page Title -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="mb-0">Dashboard</h3>
                <div class="text-muted">
                    <i class="fas fa-calendar me-2"></i>
                    {{ new Date().toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }) }}
                </div>
            </div>

            <!-- Top Row - 4 Main Stats -->
            <div class="row mb-4">
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-2">Pending Orders</h6>
                                    <h2 class="mb-0">{{ stats.pending_orders }}</h2>
                                </div>
                                <div class="text-primary">
                                    <i class="fas fa-shopping-cart fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-2">Tickets Waiting</h6>
                                    <h2 class="mb-0">{{ stats.open_tickets }}</h2>
                                </div>
                                <div class="text-warning">
                                    <i class="fas fa-ticket-alt fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-2">Pending Cancellations</h6>
                                    <h2 class="mb-0">{{ stats.pending_cancellations }}</h2>
                                </div>
                                <div class="text-danger">
                                    <i class="fas fa-times-circle fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-2">Pending Module Actions</h6>
                                    <h2 class="mb-0">{{ stats.pending_module_actions }}</h2>
                                </div>
                                <div class="text-info">
                                    <i class="fas fa-cog fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Billing Section -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0"><i class="fas fa-dollar-sign me-2"></i>Billing</h5>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
                                    <h6 class="text-muted">Today</h6>
                                    <h4 class="text-success mb-0">$0.00</h4>
                                </div>
                                <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
                                    <h6 class="text-muted">This Month</h6>
                                    <h4 class="text-success mb-0">$0.00</h4>
                                </div>
                                <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
                                    <h6 class="text-muted">This Year</h6>
                                    <h4 class="text-success mb-0">$123.00</h4>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <h6 class="text-muted">All Time</h6>
                                    <h4 class="text-success mb-0">{{ formatCurrency(stats.total_revenue) }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Middle Row - To-Do List, Network Status, System Overview -->
            <div class="row mb-4">
                <div class="col-lg-4 mb-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white">
                            <h5 class="mb-0"><i class="fas fa-tasks me-2"></i>To-Do List</h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted text-center py-4">No pending tasks</p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 mb-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white">
                            <h5 class="mb-0"><i class="fas fa-network-wired me-2"></i>Network Status</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <span class="badge bg-success me-2">●</span>
                                <span>All Systems Operational</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 mb-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white">
                            <h5 class="mb-0"><i class="fas fa-server me-2"></i>System Overview</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-2">
                                <small class="text-muted">Active Services:</small>
                                <strong class="float-end">{{ stats.active_services }}</strong>
                            </div>
                            <div class="mb-2">
                                <small class="text-muted">Total Clients:</small>
                                <strong class="float-end">{{ stats.total_clients }}</strong>
                            </div>
                            <div>
                                <small class="text-muted">Unpaid Invoices:</small>
                                <strong class="float-end text-danger">{{ stats.unpaid_invoices }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Support, Staff Online, Client Activity -->
            <div class="row mb-4">
                <div class="col-lg-4 mb-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white">
                            <h5 class="mb-0"><i class="fas fa-life-ring me-2"></i>Support</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-2">
                                <span class="badge bg-danger me-2">{{ stats.open_tickets }}</span>
                                <span>Open Tickets</span>
                            </div>
                            <div class="mb-2">
                                <span class="badge bg-success me-2">0</span>
                                <span>Answered Tickets</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 mb-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white">
                            <h5 class="mb-0"><i class="fas fa-users me-2"></i>Staff Online</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <img src="https://via.placeholder.com/32" class="rounded-circle me-2" width="32" height="32" />
                                <div>
                                    <div class="fw-bold">Administrator</div>
                                    <small class="text-muted">Online now</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 mb-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white">
                            <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Client Activity</h5>
                        </div>
                        <div class="card-body">
                            <div v-if="clientActivity && clientActivity.length > 0">
                                <div v-for="client in clientActivity.slice(0, 3)" :key="client.id" class="mb-2">
                                    <small>{{ client.name }} - {{ client.created_at }}</small>
                                </div>
                            </div>
                            <p v-else class="text-muted mb-0">No recent activity</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Orders and Invoices -->
            <div class="row mb-4">
                <div class="col-lg-6 mb-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0"><i class="fas fa-shopping-cart me-2"></i>Recent Orders</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Client</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="order in recentOrders" :key="order.id">
                                            <td>#{{ order.id }}</td>
                                            <td>{{ order.client_name }}</td>
                                            <td>{{ formatCurrency(order.amount) }}</td>
                                            <td>
                                                <span class="badge" :class="{
                                                    'bg-warning': order.status === 'pending',
                                                    'bg-success': order.status === 'active',
                                                    'bg-danger': order.status === 'cancelled'
                                                }">
                                                    {{ order.status }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr v-if="!recentOrders || recentOrders.length === 0">
                                            <td colspan="4" class="text-center text-muted">No recent orders</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 mb-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0"><i class="fas fa-file-invoice me-2"></i>Recent Invoices</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Client</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="invoice in recentInvoices" :key="invoice.id">
                                            <td>#{{ invoice.id }}</td>
                                            <td>{{ invoice.client_name }}</td>
                                            <td>{{ formatCurrency(invoice.amount) }}</td>
                                            <td>
                                                <span class="badge" :class="{
                                                    'bg-success': invoice.status === 'paid',
                                                    'bg-danger': invoice.status === 'unpaid',
                                                    'bg-warning': invoice.status === 'pending'
                                                }">
                                                    {{ invoice.status }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr v-if="!recentInvoices || recentInvoices.length === 0">
                                            <td colspan="4" class="text-center text-muted">No recent invoices</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Activity Log -->
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0"><i class="fas fa-history me-2"></i>Activity Log</h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted text-center py-3">No recent activity</p>
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

.badge {
    font-size: 0.75rem;
    padding: 0.35rem 0.65rem;
}
</style>
