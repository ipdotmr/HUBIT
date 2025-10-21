<script setup>
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import WhmcsAdminLayout from '@/Layouts/WhmcsAdminLayout.vue';

const props = defineProps({
    stats: Object
});

const cleanupForm = useForm({
    cleanup_type: ''
});

const performCleanup = (type) => {
    if (confirm(`Are you sure you want to perform ${type.replace('_', ' ')} cleanup?`)) {
        cleanupForm.cleanup_type = type;
        cleanupForm.post('/managit/utilities/system-cleanup');
    }
};
</script>

<template>
    <Head title="System Cleanup - Utilities" />

    <WhmcsAdminLayout>
        <div class="container-fluid">
            <div class="row mb-4">
                <div class="col-12">
                    <h1 class="h3 mb-0">System Cleanup</h1>
                    <p class="text-muted">Manage system cleanup tasks and optimize database performance</p>
                </div>
            </div>

            <div class="row g-4">
                <!-- Activity Logs Card -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-list-alt fa-2x text-primary"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5 class="card-title mb-0">Activity Logs</h5>
                                    <p class="text-muted mb-0">{{ stats.activity_logs }} entries</p>
                                </div>
                            </div>
                            <p class="card-text">Clean up old activity log entries older than 90 days.</p>
                            <button @click="performCleanup('activity_logs')" class="btn btn-primary btn-sm">
                                <i class="fas fa-trash me-2"></i>Clean Logs
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Old Sessions Card -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-user-clock fa-2x text-warning"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5 class="card-title mb-0">Old Sessions</h5>
                                    <p class="text-muted mb-0">{{ stats.old_sessions }} sessions</p>
                                </div>
                            </div>
                            <p class="card-text">Remove expired session records older than 30 days.</p>
                            <button @click="performCleanup('sessions')" class="btn btn-warning btn-sm">
                                <i class="fas fa-trash me-2"></i>Clean Sessions
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Cache Card -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-database fa-2x text-info"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5 class="card-title mb-0">Application Cache</h5>
                                    <p class="text-muted mb-0">{{ stats.cache_size }}</p>
                                </div>
                            </div>
                            <p class="card-text">Clear all application, config, and view caches.</p>
                            <button @click="performCleanup('cache')" class="btn btn-info btn-sm">
                                <i class="fas fa-sync me-2"></i>Clear Cache
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Temp Files Card -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-file fa-2x text-danger"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5 class="card-title mb-0">Temporary Files</h5>
                                    <p class="text-muted mb-0">{{ stats.temp_files }} files</p>
                                </div>
                            </div>
                            <p class="card-text">Delete temporary files older than 7 days.</p>
                            <button @click="performCleanup('temp_files')" class="btn btn-danger btn-sm">
                                <i class="fas fa-trash me-2"></i>Clean Files
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Database Optimization Section -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-tools me-2"></i>Database Optimization</h5>
                        </div>
                        <div class="card-body">
                            <p>Optimize all database tables to improve performance and reclaim unused space.</p>
                            <a href="/managit/utilities/database" class="btn btn-primary">
                                <i class="fas fa-database me-2"></i>View Database Status
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </WhmcsAdminLayout>
</template>
