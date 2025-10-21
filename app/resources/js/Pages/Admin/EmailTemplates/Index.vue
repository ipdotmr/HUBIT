<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import WhmcsAdminLayout from '@/Layouts/WhmcsAdminLayout.vue';
import { ref } from 'vue';

const props = defineProps({
    templates: Object,
    filters: Object,
});

const search = ref(props.filters.search || '');
const type = ref(props.filters.type || '');
const category = ref(props.filters.category || '');

const searchTemplates = () => {
    router.get(route('managit.email-templates.index'), {
        search: search.value,
        type: type.value,
        category: category.value,
    }, {
        preserveState: true,
        replace: true,
    });
};

const getTypeBadgeClass = (type) => {
    return type === 'outgoing' ? 'bg-primary' : 'bg-info';
};

const deleteTemplate = (templateId) => {
    if (confirm('Are you sure you want to delete this email template?')) {
        router.delete(route('managit.email-templates.destroy', templateId));
    }
};
</script>

<template>
    <Head title="Email Templates" />

    <WhmcsAdminLayout>
        <template #header>
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right">
                            <Link :href="route('managit.email-templates.create')" class="btn btn-primary">
                                <i class="ti ti-plus me-1"></i> Add New Template
                            </Link>
                        </div>
                        <h4 class="page-title">Email Templates</h4>
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
                            <div class="col-md-4">
                                <input 
                                    v-model="search" 
                                    @keyup.enter="searchTemplates"
                                    type="text" 
                                    class="form-control" 
                                    placeholder="Search by name or subject..."
                                >
                            </div>
                            <div class="col-md-3">
                                <select v-model="type" @change="searchTemplates" class="form-select">
                                    <option value="">All Types</option>
                                    <option value="incoming">Incoming</option>
                                    <option value="outgoing">Outgoing</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <input 
                                    v-model="category" 
                                    @keyup.enter="searchTemplates"
                                    type="text" 
                                    class="form-control" 
                                    placeholder="Category..."
                                >
                            </div>
                            <div class="col-md-2">
                                <button @click="searchTemplates" class="btn btn-primary w-100">
                                    <i class="ti ti-search me-1"></i> Search
                                </button>
                            </div>
                        </div>

                        <!-- Templates Table -->
                        <div class="table-responsive">
                            <table class="table table-hover table-centered mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Type</th>
                                        <th>Category</th>
                                        <th>Subject (EN)</th>
                                        <th>Status</th>
                                        <th>Last Updated</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="template in templates.data" :key="template.id">
                                        <td>#{{ template.id }}</td>
                                        <td>
                                            <Link :href="route('managit.email-templates.edit', template.id)" class="text-body fw-semibold">
                                                {{ template.name }}
                                            </Link>
                                        </td>
                                        <td>
                                            <span :class="['badge', getTypeBadgeClass(template.type)]">
                                                {{ template.type }}
                                            </span>
                                        </td>
                                        <td>{{ template.category || '-' }}</td>
                                        <td>{{ template.subject_en }}</td>
                                        <td>
                                            <span :class="['badge', template.is_active ? 'bg-success' : 'bg-secondary']">
                                                {{ template.is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td>{{ template.updated_at }}</td>
                                        <td class="text-end">
                                            <div class="btn-group">
                                                <Link 
                                                    :href="route('managit.email-templates.edit', template.id)" 
                                                    class="btn btn-sm btn-warning"
                                                    title="Edit"
                                                >
                                                    <i class="ti ti-edit"></i>
                                                </Link>
                                                <button 
                                                    @click="deleteTemplate(template.id)" 
                                                    class="btn btn-sm btn-danger"
                                                    title="Delete"
                                                >
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-if="templates.data.length === 0">
                                        <td colspan="8" class="text-center text-muted py-4">
                                            No email templates found
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div v-if="templates.links.length > 3" class="mt-3">
                            <nav>
                                <ul class="pagination justify-content-end mb-0">
                                    <li 
                                        v-for="(link, index) in templates.links" 
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
