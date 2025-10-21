<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import WhmcsAdminLayout from '@/Layouts/WhmcsAdminLayout.vue';

const props = defineProps({
    categories: Object,
});

const deleteCategory = (id) => {
    if (confirm('Are you sure you want to delete this category? All articles in this category will also be deleted.')) {
        router.delete(route('managit.knowledgebase.destroy', id));
    }
};
</script>

<template>
    <Head title="Knowledgebase" />

    <WhmcsAdminLayout>
        <template #header>
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right">
                            <Link :href="route('managit.knowledgebase.create')" class="btn btn-primary">
                                <i class="ti ti-plus me-1"></i> Create Category
                            </Link>
                        </div>
                        <h4 class="page-title">Knowledgebase</h4>
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
                                        <th>Category Name</th>
                                        <th>Slug</th>
                                        <th>Articles</th>
                                        <th>Display Order</th>
                                        <th>Status</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="category in categories.data" :key="category.id">
                                        <td>
                                            <strong>{{ category.name }}</strong>
                                            <br>
                                            <small class="text-muted">{{ category.description }}</small>
                                        </td>
                                        <td><code>{{ category.slug }}</code></td>
                                        <td>
                                            <span class="badge bg-info">{{ category.articles_count || 0 }} articles</span>
                                        </td>
                                        <td>{{ category.display_order }}</td>
                                        <td>
                                            <span :class="['badge', category.is_published ? 'bg-success' : 'bg-secondary']">
                                                {{ category.is_published ? 'Published' : 'Draft' }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <div class="btn-group">
                                                <Link 
                                                    :href="route('managit.knowledgebase.edit', category.id)" 
                                                    class="btn btn-sm btn-warning"
                                                    title="Edit"
                                                >
                                                    <i class="ti ti-edit"></i>
                                                </Link>
                                                <button 
                                                    @click="deleteCategory(category.id)" 
                                                    class="btn btn-sm btn-danger"
                                                    title="Delete"
                                                >
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-if="categories.data.length === 0">
                                        <td colspan="6" class="text-center text-muted py-4">
                                            No categories found. Create your first category to get started.
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
