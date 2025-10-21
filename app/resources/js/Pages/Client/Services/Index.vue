<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import ClientLayout from '@/Layouts/ClientLayout.vue';

const props = defineProps({
    services: Object,
    filters: Object,
});

const filters = ref({
    product: props.filters?.product || '',
    status: props.filters?.status || '',
});

const search = () => {
    router.get(route('client.services.index'), filters.value, {
        preserveState: true,
        preserveScroll: true,
    });
};

const clearFilters = () => {
    filters.value = { product: '', status: '' };
    search();
};

const statusBadge = (status) => {
    const badges = {
        active: 'text-success',
        suspended: 'text-danger',
        pending: 'text-warning',
        pending_suspension: 'text-orange',
        pending_unsuspension: 'text-info',
        terminated: 'text-secondary',
    };
    return badges[status] || badges.pending;
};
</script>

<template>
    <Head title="My Services" />

    <ClientLayout>
        <div class="mb-4">
            <h2>My Services</h2>
            <p class="text-muted">Manage your hosting services and products</p>
        </div>

        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <h4 class="header-title">Filter Services</h4>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Product Name</label>
                                <input
                                    v-model="filters.product"
                                    type="text"
                                    placeholder="Search by product name..."
                                    class="form-control"
                                    @keyup.enter="search"
                                />
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Status</label>
                                <select
                                    v-model="filters.status"
                                    class="form-select"
                                    @change="search"
                                >
                                    <option value="">All Statuses</option>
                                    <option value="active">Active</option>
                                    <option value="suspended">Suspended</option>
                                    <option value="pending">Pending</option>
                                    <option value="terminated">Terminated</option>
                                </select>
                            </div>

                            <div class="col-md-4 d-flex align-items-end gap-2">
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
                        <h4 class="header-title">Services</h4>
                        <div class="d-flex gap-2">
                            <span class="badge bg-primary">{{ services.total }} Total</span>
                            <Link :href="route('cart.index')" class="btn btn-sm btn-success">
                                <i class="ti ti-plus me-1"></i> Order New Service
                            </Link>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div v-if="services.data.length === 0" class="text-center py-5 text-muted">
                            <i class="ti ti-server fs-48 mb-3 d-block"></i>
                            <p>No services found.</p>
                            <Link :href="route('cart.index')" class="btn btn-primary mt-2">
                                <i class="ti ti-plus me-1"></i> Order Your First Service
                            </Link>
                        </div>

                        <div v-else class="table-responsive">
                            <table class="table table-custom table-centered table-nowrap table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Product</th>
                                        <th>Status</th>
                                        <th>Next Due</th>
                                        <th>Recurring</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="service in services.data" :key="service.id">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm flex-shrink-0 me-2">
                                                    <span class="avatar-title bg-primary-subtle rounded-circle">
                                                        <i class="ti ti-server"></i>
                                                    </span>
                                                </div>
                                                <div>
                                                    <h5 class="fs-14 mb-0">{{ service.product?.name || 'N/A' }}</h5>
                                                    <span class="text-muted fs-12">{{ service.provisioner || 'N/A' }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <i class="ti ti-circle-filled fs-12" :class="statusBadge(service.status)"></i>
                                            <span class="text-capitalize">{{ service.status }}</span>
                                        </td>
                                        <td>
                                            <span class="text-muted">{{ service.next_due_at ? new Date(service.next_due_at).toLocaleDateString() : 'N/A' }}</span>
                                        </td>
                                        <td>
                                            <span class="fw-semibold">${{ service.recurring_amount || '0.00' }}</span>
                                            <span class="text-muted">/{{ service.billing_cycle || 'monthly' }}</span>
                                        </td>
                                        <td class="text-end">
                                            <Link
                                                :href="route('client.services.show', service.id)"
                                                class="btn btn-sm btn-light"
                                            >
                                                <i class="ti ti-settings me-1"></i> Manage
                                            </Link>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div v-if="services.links.length > 3" class="card-footer">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-muted">
                                    Showing {{ services.from }} to {{ services.to }} of {{ services.total }} results
                                </div>
                                <nav>
                                    <ul class="pagination pagination-sm mb-0">
                                        <li
                                            v-for="(link, index) in services.links"
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
