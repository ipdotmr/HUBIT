<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import WhmcsAdminLayout from '@/Layouts/WhmcsAdminLayout.vue';

const props = defineProps({
    statuses: Object,
});

const deleteStatus = (id) => {
    if (confirm('Are you sure you want to delete this network status?')) {
        router.delete(route('managit.network-status.destroy', id));
    }
};

const getStatusBadge = (status) => {
    const badges = {
        operational: 'bg-success',
        degraded: 'bg-warning',
        partial_outage: 'bg-warning',
        major_outage: 'bg-danger',
        maintenance: 'bg-info',
    };
    return badges[status] || 'bg-secondary';
};

const getPriorityBadge = (priority) => {
    const badges = {
        low: 'bg-info',
        medium: 'bg-warning',
        high: 'bg-danger',
        critical: 'bg-danger',
    };
    return badges[priority] || 'bg-secondary';
};
</script>

<template>
    <Head title="Network Status" />

    <WhmcsAdminLayout>
        <template #header>
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right">
                            <Link :href="route('managit.network-status.create')" class="btn btn-primary">
                                <i class="ti ti-plus me-1"></i> Create Status
                            </Link>
                        </div>
                        <h4 class="page-title">Network Status</h4>
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
                                        <th>Title</th>
                                        <th>Status</th>
                                        <th>Priority</th>
                                        <th>Started At</th>
                                        <th>Resolved</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="status in statuses.data" :key="status.id">
                                        <td>
                                            <strong>{{ status.title }}</strong>
                                            <br>
                                            <small class="text-muted">{{ status.description.substring(0, 80) }}...</small>
                                        </td>
                                        <td>
                                            <span :class="['badge', getStatusBadge(status.status)]">
                                                {{ status.status.replace('_', ' ').toUpperCase() }}
                                            </span>
                                        </td>
                                        <td>
                                            <span :class="['badge', getPriorityBadge(status.priority)]">
                                                {{ status.priority.toUpperCase() }}
                                            </span>
                                        </td>
                                        <td>{{ status.started_at }}</td>
                                        <td>
                                            <span :class="['badge', status.is_resolved ? 'bg-success' : 'bg-warning']">
                                                {{ status.is_resolved ? 'Resolved' : 'Ongoing' }}
                                            </span>
                                            <br>
                                            <small v-if="status.resolved_at" class="text-muted">{{ status.resolved_at }}</small>
                                        </td>
                                        <td class="text-end">
                                            <div class="btn-group">
                                                <Link 
                                                    :href="route('managit.network-status.edit', status.id)" 
                                                    class="btn btn-sm btn-warning"
                                                    title="Edit"
                                                >
                                                    <i class="ti ti-edit"></i>
                                                </Link>
                                                <button 
                                                    @click="deleteStatus(status.id)" 
                                                    class="btn btn-sm btn-danger"
                                                    title="Delete"
                                                >
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-if="statuses.data.length === 0">
                                        <td colspan="6" class="text-center text-muted py-4">
                                            No network status entries found. Create your first entry to get started.
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
