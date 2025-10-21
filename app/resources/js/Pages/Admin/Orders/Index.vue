<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import WhmcsAdminLayout from '@/Layouts/WhmcsAdminLayout.vue';

const props = defineProps({
    orders: Object,
    filters: Object
});

const search = ref(props.filters.search || '');
const status = ref(props.filters.status || '');

const filterOrders = () => {
    router.get(route('managit.orders.index'), {
        search: search.value,
        status: status.value
    }, {
        preserveState: true,
        preserveScroll: true
    });
};
</script>

<template>
    <Head title="Orders - Admin" />
    
    <WhmcsAdminLayout>
        <div class="container-fluid">
            <!-- Page Title -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="mb-0">Orders</h3>
                    <p class="text-muted">Manage all customer orders</p>
                </div>
                <Link :href="route('managit.orders.index')" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>New Order
                </Link>
            </div>

            <!-- Filters -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Search</label>
                            <input 
                                v-model="search" 
                                @keyup.enter="filterOrders"
                                type="text" 
                                class="form-control" 
                                placeholder="Search by client name or email..."
                            >
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <select v-model="status" @change="filterOrders" class="form-select">
                                <option value="">All Statuses</option>
                                <option value="pending">Pending</option>
                                <option value="active">Active</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                                <option value="fraud">Fraud</option>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button @click="filterOrders" class="btn btn-primary w-100">
                                <i class="fas fa-search me-2"></i>Filter
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Orders Table -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">All Orders</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Order ID</th>
                                    <th>Client</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="order in orders.data" :key="order.id">
                                    <td><strong>#{{ order.id }}</strong></td>
                                    <td>
                                        <div>
                                            <strong>{{ order.client_name }}</strong>
                                            <br><small class="text-muted">{{ order.client_email }}</small>
                                        </div>
                                    </td>
                                    <td><strong>${{ order.total }}</strong></td>
                                    <td>
                                        <span class="badge" :class="{
                                            'bg-warning': order.status === 'pending',
                                            'bg-success': order.status === 'active' || order.status === 'completed',
                                            'bg-danger': order.status === 'cancelled' || order.status === 'fraud'
                                        }">
                                            {{ order.status }}
                                        </span>
                                    </td>
                                    <td>{{ order.created_at }}</td>
                                    <td>
                                        <Link :href="route('managit.orders.show', order.id)" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye me-1"></i>View
                                        </Link>
                                    </td>
                                </tr>
                                <tr v-if="!orders.data || orders.data.length === 0">
                                    <td colspan="6" class="text-center text-muted py-4">
                                        No orders found
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Pagination -->
                <div v-if="orders.links && orders.links.length > 3" class="card-footer bg-white">
                    <nav>
                        <ul class="pagination mb-0 justify-content-center">
                            <li v-for="link in orders.links" :key="link.label" 
                                class="page-item" 
                                :class="{ 'active': link.active, 'disabled': !link.url }">
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

<style scoped>
.card {
    transition: transform 0.2s, box-shadow 0.2s;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
}
</style>
