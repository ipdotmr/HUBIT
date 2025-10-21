<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import WhmcsAdminLayout from '@/Layouts/WhmcsAdminLayout.vue';

const props = defineProps({
    servers: Array,
});

const deleteServer = (id) => {
    if (confirm('Are you sure you want to delete this server?')) {
        router.delete(route('managit.servers.destroy', id));
    }
};

const getStatusBadgeClass = (status) => {
    const classes = {
        'online': 'bg-success',
        'offline': 'bg-danger',
        'maintenance': 'bg-warning'
    };
    return classes[status] || 'bg-secondary';
};

const getTypeBadgeClass = (type) => {
    const classes = {
        'cpanel': 'bg-primary',
        'plesk': 'bg-info',
        'directadmin': 'bg-success',
        'custom': 'bg-secondary'
    };
    return classes[type] || 'bg-secondary';
};
</script>

<template>
    <Head title="Server Management" />

    <WhmcsAdminLayout>
        <template #header>
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right">
                            <Link :href="route('managit.servers.create')" class="btn btn-primary">
                                <i class="ti ti-plus me-1"></i> Add Server
                            </Link>
                        </div>
                        <h4 class="page-title">Server Management</h4>
                    </div>
                </div>
            </div>
        </template>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-centered mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Name</th>
                                        <th>Type</th>
                                        <th>Hostname</th>
                                        <th>IP Address</th>
                                        <th>Accounts</th>
                                        <th>Status</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="server in servers" :key="server.id">
                                        <td>
                                            <strong>{{ server.name }}</strong>
                                            <span v-if="!server.is_active" class="badge bg-secondary ms-2">Inactive</span>
                                        </td>
                                        <td>
                                            <span :class="['badge', getTypeBadgeClass(server.type)]">
                                                {{ server.type.toUpperCase() }}
                                            </span>
                                        </td>
                                        <td>{{ server.hostname }}</td>
                                        <td>{{ server.ip_address }}</td>
                                        <td>
                                            <span class="badge bg-info">
                                                {{ server.active_accounts }} / {{ server.max_accounts }}
                                            </span>
                                        </td>
                                        <td>
                                            <span :class="['badge', getStatusBadgeClass(server.status)]">
                                                {{ server.status }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <div class="btn-group">
                                                <Link 
                                                    :href="route('managit.servers.edit', server.id)" 
                                                    class="btn btn-sm btn-warning"
                                                    title="Edit"
                                                >
                                                    <i class="ti ti-edit"></i>
                                                </Link>
                                                <button 
                                                    @click="deleteServer(server.id)" 
                                                    class="btn btn-sm btn-danger"
                                                    title="Delete"
                                                >
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-if="servers.length === 0">
                                        <td colspan="7" class="text-center text-muted py-4">
                                            No servers found. Add your first server to get started.
                                        </td>
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
