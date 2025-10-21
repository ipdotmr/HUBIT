<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import WhmcsAdminLayout from '@/Layouts/WhmcsAdminLayout.vue';

const props = defineProps({
    order: Object
});

const selectedStatus = ref(props.order.status);

const updateStatus = () => {
    if (confirm('Are you sure you want to update the order status?')) {
        router.post(route('managit.orders.update-status', props.order.id), {
            status: selectedStatus.value
        }, {
            preserveScroll: true
        });
    }
};
</script>

<template>
    <Head :title="`Order #${order.id} - Admin`" />
    
    <WhmcsAdminLayout>
        <div class="container-fluid">
            <!-- Page Title -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="mb-0">Order #{{ order.id }}</h3>
                    <p class="text-muted">View and manage order details</p>
                </div>
                <Link :href="route('managit.orders.index')" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Orders
                </Link>
            </div>

            <div class="row g-4">
                <!-- Order Details -->
                <div class="col-md-8">
                    <!-- Client Information -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-0"><i class="fas fa-user me-2"></i>Client Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-2"><strong>Name:</strong> {{ order.client_name }}</p>
                                    <p class="mb-0"><strong>Email:</strong> {{ order.client_email }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-2"><strong>Order Date:</strong> {{ order.created_at }}</p>
                                    <p class="mb-0">
                                        <strong>Status:</strong>
                                        <span class="badge ms-2" :class="{
                                            'bg-warning': order.status === 'pending',
                                            'bg-success': order.status === 'active' || order.status === 'completed',
                                            'bg-danger': order.status === 'cancelled' || order.status === 'fraud'
                                        }">
                                            {{ order.status }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Items -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0"><i class="fas fa-shopping-cart me-2"></i>Order Items</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Type</th>
                                            <th>Description</th>
                                            <th>Quantity</th>
                                            <th>Price</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="item in order.items" :key="item.id">
                                            <td><span class="badge bg-info">{{ item.type }}</span></td>
                                            <td>{{ item.description }}</td>
                                            <td>{{ item.quantity }}</td>
                                            <td>${{ item.price }}</td>
                                            <td><strong>${{ item.total }}</strong></td>
                                        </tr>
                                        <tr v-if="!order.items || order.items.length === 0">
                                            <td colspan="5" class="text-center text-muted py-4">
                                                No items in this order
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Summary Sidebar -->
                <div class="col-md-4">
                    <!-- Status Management -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-0"><i class="fas fa-cog me-2"></i>Manage Status</h5>
                        </div>
                        <div class="card-body">
                            <label class="form-label">Order Status</label>
                            <select v-model="selectedStatus" class="form-select mb-3">
                                <option value="pending">Pending</option>
                                <option value="active">Active</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                                <option value="fraud">Fraud</option>
                            </select>
                            <button @click="updateStatus" class="btn btn-primary w-100">
                                <i class="fas fa-save me-2"></i>Update Status
                            </button>
                        </div>
                    </div>

                    <!-- Order Totals -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0"><i class="fas fa-calculator me-2"></i>Order Totals</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal:</span>
                                <strong>${{ order.subtotal }}</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Tax:</span>
                                <strong>${{ order.tax }}</strong>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between">
                                <span class="h5 mb-0">Total:</span>
                                <strong class="h5 mb-0 text-primary">${{ order.total }}</strong>
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
