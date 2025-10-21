<script setup>
import { Head, Link } from '@inertiajs/vue3';
import WhmcsAdminLayout from '@/Layouts/WhmcsAdminLayout.vue';

const props = defineProps({
    data: Object,
    filters: Object,
});

const exportCSV = () => {
    window.location.href = route('managit.reports.services.export', props.filters);
};
</script>

<template>
    <Head title="Services Report" />

    <WhmcsAdminLayout>
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="mb-0">Services Report</h3>
                    <p class="text-muted">Overview of all services and recurring revenue</p>
                </div>
                <button @click="exportCSV" class="btn btn-primary">
                    <i class="fas fa-download me-2"></i>Export CSV
                </button>
            </div>

            <!-- Stats Cards -->
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="text-muted small">Total Services</div>
                            <h2 class="mb-0 mt-2">{{ data.total_services }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="text-muted small">Active Services</div>
                            <h2 class="mb-0 mt-2 text-success">{{ data.active_services }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="text-muted small">Suspended</div>
                            <h2 class="mb-0 mt-2 text-danger">{{ data.suspended_services }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="text-muted small">Monthly Recurring</div>
                            <h2 class="mb-0 mt-2 text-primary">${{ data.mrr?.toFixed(2) || '0.00' }}</h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Services by Provisioner</h5>
                        </div>
                        <div class="card-body">
                            <div class="list-group list-group-flush">
                                <div v-for="(count, provisioner) in data.by_provisioner" :key="provisioner" 
                                     class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                                    <span>{{ provisioner || 'N/A' }}</span>
                                    <span class="badge bg-primary rounded-pill">{{ count }}</span>
                                </div>
                                <div v-if="Object.keys(data.by_provisioner).length === 0" class="text-muted text-center py-3">
                                    No data available
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Services by Product</h5>
                        </div>
                        <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                            <div class="list-group list-group-flush">
                                <div v-for="(count, product) in data.by_product" :key="product" 
                                     class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                                    <span>{{ product || 'N/A' }}</span>
                                    <span class="badge bg-primary rounded-pill">{{ count }}</span>
                                </div>
                                <div v-if="Object.keys(data.by_product).length === 0" class="text-muted text-center py-3">
                                    No data available
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Services Table -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Recent Services</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Client</th>
                                    <th>Product</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="service in data.recent_services" :key="service.id">
                                    <td><strong>#{{ service.id }}</strong></td>
                                    <td>{{ service.client?.company_name || service.client?.contact_name || 'N/A' }}</td>
                                    <td>{{ service.product?.name || 'N/A' }}</td>
                                    <td>
                                        <span class="badge" :class="{
                                            'bg-success': service.status === 'active',
                                            'bg-danger': service.status === 'suspended',
                                            'bg-warning': service.status === 'pending'
                                        }">
                                            {{ service.status }}
                                        </span>
                                    </td>
                                    <td>{{ new Date(service.created_at).toLocaleDateString() }}</td>
                                </tr>
                                <tr v-if="!data.recent_services || data.recent_services.length === 0">
                                    <td colspan="5" class="text-center text-muted py-4">No recent services</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </WhmcsAdminLayout>
</template>
