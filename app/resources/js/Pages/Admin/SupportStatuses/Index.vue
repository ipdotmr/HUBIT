<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import WhmcsAdminLayout from '@/Layouts/WhmcsAdminLayout.vue';

const props = defineProps({
    statuses: Array,
});

const deleteStatus = (id) => {
    if (confirm('Are you sure you want to delete this status?')) {
        router.delete(route('managit.support-statuses.destroy', id));
    }
};
</script>

<template>
    <Head title="Support Statuses" />

    <WhmcsAdminLayout>
        <template #header>
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right">
                            <Link :href="route('managit.support-statuses.create')" class="btn btn-primary">
                                <i class="ti ti-plus me-1"></i> Add Status
                            </Link>
                        </div>
                        <h4 class="page-title">Support Statuses</h4>
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
                                        <th>Order</th>
                                        <th>Name (EN)</th>
                                        <th>Name (AR)</th>
                                        <th>Name (FR)</th>
                                        <th>Color</th>
                                        <th>Closed Status</th>
                                        <th>Status</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="status in statuses" :key="status.id">
                                        <td>{{ status.sort_order }}</td>
                                        <td><strong>{{ status.name_en }}</strong></td>
                                        <td dir="rtl">{{ status.name_ar }}</td>
                                        <td>{{ status.name_fr }}</td>
                                        <td>
                                            <span 
                                                class="badge" 
                                                :style="{ backgroundColor: status.color, color: '#fff' }"
                                            >
                                                {{ status.color }}
                                            </span>
                                        </td>
                                        <td>
                                            <span :class="['badge', status.is_closed ? 'bg-danger' : 'bg-info']">
                                                {{ status.is_closed ? 'Closed' : 'Open' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span :class="['badge', status.is_active ? 'bg-success' : 'bg-secondary']">
                                                {{ status.is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <div class="btn-group">
                                                <Link 
                                                    :href="route('managit.support-statuses.edit', status.id)" 
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
                                    <tr v-if="statuses.length === 0">
                                        <td colspan="8" class="text-center text-muted py-4">
                                            No support statuses found. Add your first status to get started.
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
