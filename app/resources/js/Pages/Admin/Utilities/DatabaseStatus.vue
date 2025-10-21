<script setup>
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import WhmcsAdminLayout from '@/Layouts/WhmcsAdminLayout.vue';

const props = defineProps({
    tables: Array,
    total_size: String,
    database_name: String
});

const optimizeDatabase = () => {
    if (confirm('Are you sure you want to optimize all database tables? This may take a few minutes.')) {
        router.post('/managit/utilities/database/optimize');
    }
};
</script>

<template>
    <Head title="Database Status - Utilities" />

    <WhmcsAdminLayout>
        <div class="container-fluid">
            <div class="row mb-4">
                <div class="col-12">
                    <h1 class="h3 mb-0">Database Status</h1>
                    <p class="text-muted">View database tables and optimize performance</p>
                </div>
            </div>

            <!-- Database Info Card -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5><i class="fas fa-database me-2 text-primary"></i>Database Information</h5>
                            <p class="mb-1"><strong>Database Name:</strong> {{ database_name }}</p>
                            <p class="mb-1"><strong>Total Tables:</strong> {{ tables.length }}</p>
                            <p class="mb-0"><strong>Total Size:</strong> {{ total_size }}</p>
                        </div>
                        <div class="col-md-6 text-end">
                            <button @click="optimizeDatabase" class="btn btn-primary">
                                <i class="fas fa-cog me-2"></i>Optimize All Tables
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tables List -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Database Tables</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Table Name</th>
                                    <th>Rows</th>
                                    <th>Size</th>
                                    <th>Engine</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="table in tables" :key="table.name">
                                    <td>
                                        <i class="fas fa-table me-2 text-muted"></i>
                                        {{ table.name }}
                                    </td>
                                    <td>{{ table.rows.toLocaleString() }}</td>
                                    <td>{{ table.size }}</td>
                                    <td>
                                        <span class="badge bg-secondary">{{ table.engine }}</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </WhmcsAdminLayout>
</template>
