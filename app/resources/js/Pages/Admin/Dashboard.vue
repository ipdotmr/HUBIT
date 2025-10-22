<script setup>
import { Head } from '@inertiajs/vue3';
import WhmcsAdminLayout from '@/Layouts/WhmcsAdminLayout.vue';
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
    <Head title="HUBIT - Admin Dashboard" />

    <WhmcsAdminLayout>
        <!-- Page Title -->
        <div class="page-header mb-4">
            <h1 class="page-title">Dashboard</h1>
        </div>

        <!-- Statistics Cards -->
        <div class="row g-3 mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="stat-card stat-card-red">
                    <div class="stat-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value">{{ stats.pending_orders }}</div>
                        <div class="stat-label">Pending Orders</div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="stat-card stat-card-teal">
                    <div class="stat-icon">
                        <i class="fas fa-server"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value">{{ stats.active_services }}</div>
                        <div class="stat-label">Active Services</div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="stat-card stat-card-orange">
                    <div class="stat-icon">
                        <i class="fas fa-file-invoice"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value">{{ stats.unpaid_invoices }}</div>
                        <div class="stat-label">Unpaid Invoices</div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="stat-card stat-card-purple">
                    <div class="stat-icon">
                        <i class="fas fa-ticket-alt"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value">{{ stats.open_tickets }}</div>
                        <div class="stat-label">Open Tickets</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Stats Row -->
        <div class="row g-3 mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="stat-card stat-card-yellow">
                    <div class="stat-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value">{{ stats.pending_cancellations }}</div>
                        <div class="stat-label">Pending Cancellations</div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="stat-card stat-card-gray">
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value">{{ stats.pending_module_actions }}</div>
                        <div class="stat-label">Module Queue</div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="stat-card stat-card-blue">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value">{{ stats.total_clients }}</div>
                        <div class="stat-label">Total Clients</div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="stat-card stat-card-green">
                    <div class="stat-icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value">{{ formatCurrency(stats.total_revenue) }}</div>
                        <div class="stat-label">Total Revenue</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="row g-3 mb-4">
            <!-- Recent Orders -->
            <div class="col-xl-6">
                <div class="whmcs-card">
                    <div class="whmcs-card-header">
                        <h5 class="whmcs-card-title">Recent Orders</h5>
                    </div>
                    <div class="whmcs-card-body">
                        <div class="table-responsive">
                            <table class="whmcs-table">
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
                                            <span :class="['whmcs-badge', getStatusBadgeClass(order.status)]">
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
                <div class="whmcs-card">
                    <div class="whmcs-card-header">
                        <h5 class="whmcs-card-title">Recent Invoices</h5>
                    </div>
                    <div class="whmcs-card-body">
                        <div class="table-responsive">
                            <table class="whmcs-table">
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
                                            <span :class="['whmcs-badge', getStatusBadgeClass(invoice.status)]">
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
        <div class="row g-3">
            <!-- Recent Tickets -->
            <div class="col-xl-6">
                <div class="whmcs-card">
                    <div class="whmcs-card-header">
                        <h5 class="whmcs-card-title">Recent Support Tickets</h5>
                    </div>
                    <div class="whmcs-card-body">
                        <div class="table-responsive">
                            <table class="whmcs-table">
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
                                            <span :class="['whmcs-badge', getPriorityBadgeClass(ticket.priority)]">
                                                {{ ticket.priority }}
                                            </span>
                                        </td>
                                        <td>
                                            <span :class="['whmcs-badge', getStatusBadgeClass(ticket.status)]">
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
                <div class="whmcs-card">
                    <div class="whmcs-card-header">
                        <h5 class="whmcs-card-title">Recent Client Activity</h5>
                    </div>
                    <div class="whmcs-card-body">
                        <div class="table-responsive">
                            <table class="whmcs-table">
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
    </WhmcsAdminLayout>
</template>

<style scoped>
/* Page Header */
.page-header {
    margin-bottom: 1.5rem;
}

.page-title {
    font-size: 1.75rem;
    font-weight: 600;
    color: #333;
    margin: 0;
}

/* Stat Cards */
.stat-card {
    background: white;
    border-radius: 8px;
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    transition: transform 0.2s, box-shadow 0.2s;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.75rem;
    color: white;
}

.stat-card-red .stat-icon { background: #dc3545; }
.stat-card-teal .stat-icon { background: #17a2b8; }
.stat-card-orange .stat-icon { background: #fd7e14; }
.stat-card-purple .stat-icon { background: #6f42c1; }
.stat-card-yellow .stat-icon { background: #ffc107; }
.stat-card-gray .stat-icon { background: #6c757d; }
.stat-card-blue .stat-icon { background: #007bff; }
.stat-card-green .stat-icon { background: #28a745; }

.stat-content {
    flex: 1;
}

.stat-value {
    font-size: 1.75rem;
    font-weight: 700;
    color: #333;
    line-height: 1.2;
}

.stat-label {
    font-size: 0.875rem;
    color: #6c757d;
    margin-top: 0.25rem;
}

/* WHMCS Cards */
.whmcs-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    overflow: hidden;
}

.whmcs-card-header {
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #e9ecef;
    background: #f8f9fa;
}

.whmcs-card-title {
    font-size: 1rem;
    font-weight: 600;
    color: #333;
    margin: 0;
}

.whmcs-card-body {
    padding: 1.5rem;
}

/* WHMCS Table */
.whmcs-table {
    width: 100%;
    font-size: 0.875rem;
    border-collapse: collapse;
}

.whmcs-table thead th {
    padding: 0.75rem;
    text-align: left;
    font-weight: 600;
    color: #6c757d;
    border-bottom: 2px solid #e9ecef;
    font-size: 0.8rem;
    text-transform: uppercase;
}

.whmcs-table tbody td {
    padding: 0.75rem;
    border-bottom: 1px solid #f1f3f5;
    color: #495057;
}

.whmcs-table tbody tr:hover {
    background: #f8f9fa;
}

.whmcs-table tbody tr:last-child td {
    border-bottom: none;
}

/* WHMCS Badges */
.whmcs-badge {
    display: inline-block;
    padding: 0.25rem 0.6rem;
    font-size: 0.75rem;
    font-weight: 600;
    border-radius: 4px;
    text-transform: capitalize;
}

.whmcs-badge.bg-success {
    background: #28a745;
    color: white;
}

.whmcs-badge.bg-danger {
    background: #dc3545;
    color: white;
}

.whmcs-badge.bg-warning {
    background: #ffc107;
    color: #333;
}

.whmcs-badge.bg-info {
    background: #17a2b8;
    color: white;
}

.whmcs-badge.bg-secondary {
    background: #6c757d;
    color: white;
}
</style>
