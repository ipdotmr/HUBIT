<script setup>
import { Head } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { computed } from 'vue';

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

const getStatusBadgeClass = (status) => {
    const classes = {
        'pending': 'bg-warning',
        'active': 'bg-success',
        'paid': 'bg-success',
        'unpaid': 'bg-danger',
        'open': 'bg-info',
        'closed': 'bg-secondary',
        'completed': 'bg-success',
        'cancelled': 'bg-danger',
    };
    return classes[status] || 'bg-secondary';
};

const getPriorityBadgeClass = (priority) => {
    const classes = {
        'low': 'bg-info',
        'medium': 'bg-warning',
        'high': 'bg-danger',
    };
    return classes[priority] || 'bg-secondary';
};
</script>

<template>
    <Head title="Admin Dashboard" />

    <AdminLayout>
        <template #header>
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <h4 class="page-title">Admin Dashboard</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item active">Dashboard</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <!-- Statistics Cards -->
        <div class="row">
            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 me-3">
                                <div class="avatar-sm">
                                    <span class="avatar-title bg-primary-lighten text-primary rounded">
                                        <i class="ti ti-shopping-bag fs-24"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="mb-1">{{ stats.pending_orders }}</h5>
                                <p class="text-muted mb-0">Pending Orders</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 me-3">
                                <div class="avatar-sm">
                                    <span class="avatar-title bg-success-lighten text-success rounded">
                                        <i class="ti ti-server fs-24"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="mb-1">{{ stats.active_services }}</h5>
                                <p class="text-muted mb-0">Active Services</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 me-3">
                                <div class="avatar-sm">
                                    <span class="avatar-title bg-danger-lighten text-danger rounded">
                                        <i class="ti ti-file-invoice fs-24"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="mb-1">{{ stats.unpaid_invoices }}</h5>
                                <p class="text-muted mb-0">Unpaid Invoices</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 me-3">
                                <div class="avatar-sm">
                                    <span class="avatar-title bg-info-lighten text-info rounded">
                                        <i class="ti ti-ticket fs-24"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="mb-1">{{ stats.open_tickets }}</h5>
                                <p class="text-muted mb-0">Open Tickets</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Stats Row -->
        <div class="row">
            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 me-3">
                                <div class="avatar-sm">
                                    <span class="avatar-title bg-warning-lighten text-warning rounded">
                                        <i class="ti ti-alert-circle fs-24"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="mb-1">{{ stats.pending_cancellations }}</h5>
                                <p class="text-muted mb-0">Pending Cancellations</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 me-3">
                                <div class="avatar-sm">
                                    <span class="avatar-title bg-secondary-lighten text-secondary rounded">
                                        <i class="ti ti-clock fs-24"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="mb-1">{{ stats.pending_module_actions }}</h5>
                                <p class="text-muted mb-0">Module Queue</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 me-3">
                                <div class="avatar-sm">
                                    <span class="avatar-title bg-primary-lighten text-primary rounded">
                                        <i class="ti ti-users fs-24"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="mb-1">{{ stats.total_clients }}</h5>
                                <p class="text-muted mb-0">Total Clients</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 me-3">
                                <div class="avatar-sm">
                                    <span class="avatar-title bg-success-lighten text-success rounded">
                                        <i class="ti ti-currency-dollar fs-24"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="mb-1">{{ formatCurrency(stats.total_revenue) }}</h5>
                                <p class="text-muted mb-0">Total Revenue</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="row">
            <!-- Recent Orders -->
            <div class="col-xl-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Recent Orders</h4>
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
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="order in recentOrders" :key="order.id">
                                        <td>#{{ order.id }}</td>
                                        <td>{{ order.client_name }}</td>
                                        <td>{{ formatCurrency(order.amount) }}</td>
                                        <td>
                                            <span :class="['badge', getStatusBadgeClass(order.status)]">
                                                {{ order.status }}
                                            </span>
                                        </td>
                                        <td>{{ order.created_at }}</td>
                                    </tr>
                                    <tr v-if="recentOrders.length === 0">
                                        <td colspan="5" class="text-center text-muted">No recent orders</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Invoices -->
            <div class="col-xl-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Recent Invoices</h4>
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
                                        <th>Due Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="invoice in recentInvoices" :key="invoice.id">
                                        <td>#{{ invoice.id }}</td>
                                        <td>{{ invoice.client_name }}</td>
                                        <td>{{ formatCurrency(invoice.amount) }}</td>
                                        <td>
                                            <span :class="['badge', getStatusBadgeClass(invoice.status)]">
                                                {{ invoice.status }}
                                            </span>
                                        </td>
                                        <td>{{ invoice.due_date || 'N/A' }}</td>
                                    </tr>
                                    <tr v-if="recentInvoices.length === 0">
                                        <td colspan="5" class="text-center text-muted">No recent invoices</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Tickets and Client Activity -->
        <div class="row">
            <!-- Recent Tickets -->
            <div class="col-xl-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Recent Support Tickets</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Client</th>
                                        <th>Subject</th>
                                        <th>Priority</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="ticket in recentTickets" :key="ticket.id">
                                        <td>#{{ ticket.id }}</td>
                                        <td>{{ ticket.client_name }}</td>
                                        <td>{{ ticket.subject }}</td>
                                        <td>
                                            <span :class="['badge', getPriorityBadgeClass(ticket.priority)]">
                                                {{ ticket.priority }}
                                            </span>
                                        </td>
                                        <td>
                                            <span :class="['badge', getStatusBadgeClass(ticket.status)]">
                                                {{ ticket.status }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr v-if="recentTickets.length === 0">
                                        <td colspan="5" class="text-center text-muted">No recent tickets</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Client Activity -->
            <div class="col-xl-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Recent Client Activity</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Registered</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="client in clientActivity" :key="client.id">
                                        <td>#{{ client.id }}</td>
                                        <td>{{ client.name }}</td>
                                        <td>{{ client.email }}</td>
                                        <td>{{ client.created_at }}</td>
                                    </tr>
                                    <tr v-if="clientActivity.length === 0">
                                        <td colspan="4" class="text-center text-muted">No recent activity</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
