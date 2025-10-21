<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import WhmcsAdminLayout from '@/Layouts/WhmcsAdminLayout.vue';

const props = defineProps({
    announcements: Object,
});

const deleteAnnouncement = (id) => {
    if (confirm('Are you sure you want to delete this announcement?')) {
        router.delete(route('managit.announcements.destroy', id));
    }
};
</script>

<template>
    <Head title="Announcements" />

    <WhmcsAdminLayout>
        <template #header>
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right">
                            <Link :href="route('managit.announcements.create')" class="btn btn-primary">
                                <i class="ti ti-plus me-1"></i> Create Announcement
                            </Link>
                        </div>
                        <h4 class="page-title">Announcements</h4>
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
                                        <th>Published Date</th>
                                        <th>Status</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="announcement in announcements.data" :key="announcement.id">
                                        <td>
                                            <strong>{{ announcement.title }}</strong>
                                            <br>
                                            <small class="text-muted">{{ announcement.content.substring(0, 100) }}...</small>
                                        </td>
                                        <td>{{ announcement.published_at || 'Not published' }}</td>
                                        <td>
                                            <span :class="['badge', announcement.is_published ? 'bg-success' : 'bg-secondary']">
                                                {{ announcement.is_published ? 'Published' : 'Draft' }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <div class="btn-group">
                                                <Link 
                                                    :href="route('managit.announcements.edit', announcement.id)" 
                                                    class="btn btn-sm btn-warning"
                                                    title="Edit"
                                                >
                                                    <i class="ti ti-edit"></i>
                                                </Link>
                                                <button 
                                                    @click="deleteAnnouncement(announcement.id)" 
                                                    class="btn btn-sm btn-danger"
                                                    title="Delete"
                                                >
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-if="announcements.data.length === 0">
                                        <td colspan="4" class="text-center text-muted py-4">
                                            No announcements found. Create your first announcement to get started.
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
