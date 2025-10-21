<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import WhmcsAdminLayout from '@/Layouts/WhmcsAdminLayout.vue';

const props = defineProps({
    departments: Array,
});

const deleteDepartment = (id) => {
    if (confirm('Are you sure you want to delete this department?')) {
        router.delete(route('managit.support-departments.destroy', id));
    }
};
</script>

<template>
    <Head title="Support Departments" />

    <WhmcsAdminLayout>
        <template #header>
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right">
                            <Link :href="route('managit.support-departments.create')" class="btn btn-primary">
                                <i class="ti ti-plus me-1"></i> Add Department
                            </Link>
                        </div>
                        <h4 class="page-title">Support Departments</h4>
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
                                        <th>Email</th>
                                        <th>Status</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="dept in departments" :key="dept.id">
                                        <td>{{ dept.sort_order }}</td>
                                        <td><strong>{{ dept.name_en }}</strong></td>
                                        <td dir="rtl">{{ dept.name_ar }}</td>
                                        <td>{{ dept.name_fr }}</td>
                                        <td>{{ dept.email || '-' }}</td>
                                        <td>
                                            <span :class="['badge', dept.is_active ? 'bg-success' : 'bg-secondary']">
                                                {{ dept.is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <div class="btn-group">
                                                <Link 
                                                    :href="route('managit.support-departments.edit', dept.id)" 
                                                    class="btn btn-sm btn-warning"
                                                    title="Edit"
                                                >
                                                    <i class="ti ti-edit"></i>
                                                </Link>
                                                <button 
                                                    @click="deleteDepartment(dept.id)" 
                                                    class="btn btn-sm btn-danger"
                                                    title="Delete"
                                                >
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-if="departments.length === 0">
                                        <td colspan="7" class="text-center text-muted py-4">
                                            No support departments found. Add your first department to get started.
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
