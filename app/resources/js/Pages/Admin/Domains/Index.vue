<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import WhmcsAdminLayout from '@/Layouts/WhmcsAdminLayout.vue';

const props = defineProps({
    domains: Object,
    filters: Object
});

const search = ref(props.filters.search || '');
const status = ref(props.filters.status || '');

const filterDomains = () => {
    router.get(route('managit.domains.index'), {
        search: search.value,
        status: status.value
    }, {
        preserveState: true,
        preserveScroll: true
    });
};
</script>

<template>
    <Head title="Domains" />
    
    <WhmcsAdminLayout>
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="mb-0">Domain Management</h3>
                <Link :href="route('managit.domains.create')" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Add Domain
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
                                placeholder="Search domains..."
                                @input="filterDomains"
                            >
                        </div>
                        <div class="col-md-4">
                            <select v-model="status" class="form-select" @change="filterDomains">
                                <option value="">All Status</option>
                                <option value="active">Active</option>
                                <option value="expired">Expired</option>
                                <option value="cancelled">Cancelled</option>
                                <option value="pending">Pending</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button @click="search = ''; status = ''; filterDomains();" class="btn btn-secondary w-100">
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
                                    <th>Domain</th>
                                    <th>Client</th>
                                    <th>Registration Date</th>
                                    <th>Expiry Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="domain in domains.data" :key="domain.id">
                                    <td><strong>{{ domain.domain }}</strong></td>
                                    <td>{{ domain.client_name }}</td>
                                    <td>{{ domain.registration_date }}</td>
                                    <td>{{ domain.expiry_date }}</td>
                                    <td>
                                        <span class="badge" :class="{
                                            'bg-success': domain.status === 'active',
                                            'bg-danger': domain.status === 'expired',
                                            'bg-secondary': domain.status === 'cancelled',
                                            'bg-warning': domain.status === 'pending'
                                        }">
                                            {{ domain.status }}
                                        </span>
                                    </td>
                                    <td>
                                        <Link :href="route('managit.domains.show', domain.id)" class="btn btn-sm btn-primary me-1">
                                            <i class="fas fa-eye"></i>
                                        </Link>
                                        <Link :href="route('managit.domains.edit', domain.id)" class="btn btn-sm btn-secondary">
                                            <i class="fas fa-edit"></i>
                                        </Link>
                                    </td>
                                </tr>
                                <tr v-if="domains.data.length === 0">
                                    <td colspan="6" class="text-center text-muted py-4">
                                        No domains found
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div v-if="domains.links && domains.links.length > 3" class="card-footer bg-white">
                    <nav>
                        <ul class="pagination mb-0 justify-content-center">
                            <li v-for="link in domains.links" :key="link.label" 
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
