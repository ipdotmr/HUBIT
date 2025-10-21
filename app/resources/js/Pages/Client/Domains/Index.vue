<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import ClientLayout from '@/Layouts/ClientLayout.vue';

const props = defineProps({
    domains: Object,
    filters: Object,
});

const filters = ref({
    domain: props.filters?.domain || '',
    status: props.filters?.status || '',
});

const search = () => {
    router.get(route('client.domains.index'), filters.value, {
        preserveState: true,
        preserveScroll: true,
    });
};

const clearFilters = () => {
    filters.value = { domain: '', status: '' };
    search();
};

const statusBadge = (status) => {
    const badges = {
        active: 'text-success',
        expired: 'text-danger',
        pending: 'text-warning',
        cancelled: 'text-secondary',
    };
    return badges[status] || badges.pending;
};
</script>

<template>
    <Head title="My Domains" />

    <ClientLayout>
        <div class="mb-4">
            <h4 class="page-title">My Domains</h4>
            <p class="text-muted">Manage your domain names</p>
        </div>

        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <h4 class="header-title">Filter Domains</h4>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Domain Name</label>
                                <input
                                    v-model="filters.domain"
                                    type="text"
                                    placeholder="Search by domain name..."
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
                                    <option value="expired">Expired</option>
                                    <option value="pending">Pending</option>
                                    <option value="cancelled">Cancelled</option>
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
                        <h4 class="header-title">Domains</h4>
                        <div class="d-flex gap-2">
                            <span class="badge bg-primary">{{ domains.total }} Total</span>
                            <Link :href="route('domains.search')" class="btn btn-sm btn-success">
                                <i class="ti ti-plus me-1"></i> Register New Domain
                            </Link>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div v-if="domains.data.length === 0" class="text-center py-5 text-muted">
                            <i class="ti ti-world fs-48 mb-3 d-block"></i>
                            <p>No domains found.</p>
                            <Link :href="route('domains.search')" class="btn btn-primary mt-2">
                                <i class="ti ti-plus me-1"></i> Register Your First Domain
                            </Link>
                        </div>

                        <div v-else class="table-responsive">
                            <table class="table table-custom table-centered table-nowrap table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Domain</th>
                                        <th>Status</th>
                                        <th>Registration Date</th>
                                        <th>Expiry Date</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="domain in domains.data" :key="domain.id">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm flex-shrink-0 me-2">
                                                    <span class="avatar-title bg-info-subtle rounded-circle">
                                                        <i class="ti ti-world"></i>
                                                    </span>
                                                </div>
                                                <div>
                                                    <h5 class="fs-14 mb-0">{{ domain.domain }}</h5>
                                                    <span class="text-muted fs-12">{{ domain.registrar || 'N/A' }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <i class="ti ti-circle-filled fs-12" :class="statusBadge(domain.status)"></i>
                                            <span class="text-capitalize">{{ domain.status }}</span>
                                        </td>
                                        <td>
                                            <span class="text-muted">{{ domain.registration_date ? new Date(domain.registration_date).toLocaleDateString() : 'N/A' }}</span>
                                        </td>
                                        <td>
                                            <span class="text-muted">{{ domain.expiry_date ? new Date(domain.expiry_date).toLocaleDateString() : 'N/A' }}</span>
                                        </td>
                                        <td class="text-end">
                                            <Link
                                                :href="route('client.domains.show', domain.id)"
                                                class="btn btn-sm btn-light"
                                            >
                                                <i class="ti ti-settings me-1"></i> Manage
                                            </Link>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div v-if="domains.links.length > 3" class="card-footer">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-muted">
                                    Showing {{ domains.from }} to {{ domains.to }} of {{ domains.total }} results
                                </div>
                                <nav>
                                    <ul class="pagination pagination-sm mb-0">
                                        <li
                                            v-for="(link, index) in domains.links"
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
