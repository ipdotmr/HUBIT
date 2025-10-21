<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import WhmcsAdminLayout from '@/Layouts/WhmcsAdminLayout.vue';
import { ref, watch } from 'vue';

const props = defineProps({
    clients: Object,
    filters: Object,
});

const search = ref(props.filters.search || '');
const status = ref(props.filters.status || '');

const searchClients = () => {
    router.get(route('managit.clients.index'), {
        search: search.value,
        status: status.value,
    }, {
        preserveState: true,
        replace: true,
    });
};

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
    }).format(amount);
};

const getStatusBadgeClass = (status) => {
    const classes = {
        'active': 'bg-success',
        'inactive': 'bg-secondary',
        'suspended': 'bg-danger',
    };
    return classes[status] || 'bg-secondary';
};

const deleteClient = (clientId) => {
    if (confirm('Are you sure you want to delete this client? This action cannot be undone.')) {
        router.delete(route('managit.clients.destroy', clientId));
    }
};
</script>

<template>
    <Head title="Clients Management" />

    <WhmcsAdminLayout>
        <template #header>
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right">
                            <Link :href="route('managit.clients.create')" class="btn btn-primary">
                                <i class="ti ti-plus me-1"></i> Add New Client
                            </Link>
                        </div>
                        <h4 class="page-title">Clients Management</h4>
                    </div>
                </div>
            </div>
        </template>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <!-- Search and Filter -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <input 
                                    v-model="search" 
                                    @keyup.enter="searchClients"
                                    type="text" 
                                    class="form-control" 
                                    placeholder="Search by name, email, or company..."
                                >
                            </div>
                            <div class="col-md-3">
                                <select v-model="status" @change="searchClients" class="form-select">
                                    <option value="">All Statuses</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                    <option value="suspended">Suspended</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button @click="searchClients" class="btn btn-primary w-100">
                                    <i class="ti ti-search me-1"></i> Search
                                </button>
                            </div>
                        </div>

                        <!-- Clients Table -->
                        <div class="table-responsive">
                            <table class="table table-hover table-centered mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Company</th>
                                        <th>Status</th>
                                        <th>Balance</th>
                                        <th>Registered</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="client in clients.data" :key="client.id">
                                        <td>#{{ client.id }}</td>
                                        <td>
                                            <Link :href="route('managit.clients.show', client.id)" class="text-body fw-semibold">
                                                {{ client.name }}
                                            </Link>
                                        </td>
                                        <td>{{ client.email }}</td>
                                        <td>{{ client.company || '-' }}</td>
                                        <td>
                                            <span :class="['badge', getStatusBadgeClass(client.status)]">
                                                {{ client.status }}
                                            </span>
                                        </td>
                                        <td>{{ formatCurrency(client.balance) }}</td>
                                        <td>{{ client.created_at }}</td>
                                        <td class="text-end">
                                            <div class="btn-group">
                                                <Link 
                                                    :href="route('managit.clients.show', client.id)" 
                                                    class="btn btn-sm btn-info"
                                                    title="View"
                                                >
                                                    <i class="ti ti-eye"></i>
                                                </Link>
                                                <Link 
                                                    :href="route('managit.clients.edit', client.id)" 
                                                    class="btn btn-sm btn-warning"
                                                    title="Edit"
                                                >
                                                    <i class="ti ti-edit"></i>
                                                </Link>
                                                <button 
                                                    @click="deleteClient(client.id)" 
                                                    class="btn btn-sm btn-danger"
                                                    title="Delete"
                                                >
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-if="clients.data.length === 0">
                                        <td colspan="8" class="text-center text-muted py-4">
                                            No clients found
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div v-if="clients.links.length > 3" class="mt-3">
                            <nav>
                                <ul class="pagination justify-content-end mb-0">
                                    <li 
                                        v-for="(link, index) in clients.links" 
                                        :key="index"
                                        :class="['page-item', { 'active': link.active, 'disabled': !link.url }]"
                                    >
                                        <Link 
                                            v-if="link.url" 
                                            :href="link.url" 
                                            class="page-link"
                                            v-html="link.label"
                                        />
                                        <span v-else class="page-link" v-html="link.label" />
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </WhmcsAdminLayout>
</template>
