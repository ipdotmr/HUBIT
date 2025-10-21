<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import WhmcsAdminLayout from '@/Layouts/WhmcsAdminLayout.vue';

const props = defineProps({
    groups: Array,
});

const deleteGroup = (id) => {
    if (confirm('Are you sure you want to delete this product group?')) {
        router.delete(route('managit.product-groups.destroy', id));
    }
};
</script>

<template>
    <Head title="Product Groups" />

    <WhmcsAdminLayout>
        <template #header>
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right">
                            <Link :href="route('managit.product-groups.create')" class="btn btn-primary">
                                <i class="ti ti-plus me-1"></i> Add Product Group
                            </Link>
                        </div>
                        <h4 class="page-title">Product Groups</h4>
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
                                        <th>Name</th>
                                        <th>Description</th>
                                        <th>Products Count</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="group in groups" :key="group.id">
                                        <td>{{ group.sort_order }}</td>
                                        <td><strong>{{ group.name }}</strong></td>
                                        <td>{{ group.description || '-' }}</td>
                                        <td>
                                            <span class="badge bg-info">
                                                {{ group.products_count }} {{ group.products_count === 1 ? 'Product' : 'Products' }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <div class="btn-group">
                                                <Link 
                                                    :href="route('managit.product-groups.edit', group.id)" 
                                                    class="btn btn-sm btn-warning"
                                                    title="Edit"
                                                >
                                                    <i class="ti ti-edit"></i>
                                                </Link>
                                                <button 
                                                    @click="deleteGroup(group.id)" 
                                                    class="btn btn-sm btn-danger"
                                                    title="Delete"
                                                    :disabled="group.products_count > 0"
                                                >
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-if="groups.length === 0">
                                        <td colspan="5" class="text-center text-muted py-4">
                                            No product groups found. Add your first group to organize your products.
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
