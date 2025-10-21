<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import WhmcsAdminLayout from '@/Layouts/WhmcsAdminLayout.vue';

const props = defineProps({
    logs: Object,
    filters: Object
});

const search = ref(props.filters.search || '');
const dateFrom = ref(props.filters.date_from || '');
const dateTo = ref(props.filters.date_to || '');

const applyFilters = () => {
    router.get('/managit/utilities/logs', {
        search: search.value,
        date_from: dateFrom.value,
        date_to: dateTo.value
    }, {
        preserveState: true,
        preserveScroll: true
    });
};

const clearFilters = () => {
    search.value = '';
    dateFrom.value = '';
    dateTo.value = '';
    router.get('/managit/utilities/logs');
};
</script>

<template>
    <Head title="Activity Logs - Utilities" />

    <WhmcsAdminLayout>
        <div class="container-fluid">
            <div class="row mb-4">
                <div class="col-12">
                    <h1 class="h3 mb-0">Activity Logs</h1>
                    <p class="text-muted">View and search system activity logs</p>
                </div>
            </div>

            <!-- Filters Card -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Search</label>
                            <input 
                                v-model="search" 
                                type="text" 
                                class="form-control" 
                                placeholder="Search logs..."
                                @keyup.enter="applyFilters"
                            />
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Date From</label>
                            <input 
                                v-model="dateFrom" 
                                type="date" 
                                class="form-control"
                            />
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Date To</label>
                            <input 
                                v-model="dateTo" 
                                type="date" 
                                class="form-control"
                            />
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button @click="applyFilters" class="btn btn-primary me-2">
                                <i class="fas fa-search"></i> Filter
                            </button>
                            <button @click="clearFilters" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Clear
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Logs Table -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date/Time</th>
                                    <th>Description</th>
                                    <th>Subject</th>
                                    <th>Causer</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="log in logs.data" :key="log.id">
                                    <td>{{ new Date(log.created_at).toLocaleString() }}</td>
                                    <td>{{ log.description }}</td>
                                    <td>
                                        <span class="badge bg-info">{{ log.subject_type }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ log.causer_type || 'System' }}</span>
                                    </td>
                                </tr>
                                <tr v-if="logs.data.length === 0">
                                    <td colspan="4" class="text-center text-muted py-4">
                                        <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                        No activity logs found
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div v-if="logs.data.length > 0" class="d-flex justify-content-between align-items-center mt-3">
                        <div class="text-muted">
                            Showing {{ logs.from }} to {{ logs.to }} of {{ logs.total }} entries
                        </div>
                        <nav>
                            <ul class="pagination mb-0">
                                <li class="page-item" :class="{ disabled: !logs.prev_page_url }">
                                    <Link 
                                        :href="logs.prev_page_url || '#'" 
                                        class="page-link"
                                        preserve-state
                                        preserve-scroll
                                    >
                                        Previous
                                    </Link>
                                </li>
                                <li class="page-item" :class="{ disabled: !logs.next_page_url }">
                                    <Link 
                                        :href="logs.next_page_url || '#'" 
                                        class="page-link"
                                        preserve-state
                                        preserve-scroll
                                    >
                                        Next
                                    </Link>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </WhmcsAdminLayout>
</template>
