<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import WhmcsAdminLayout from '@/Layouts/WhmcsAdminLayout.vue';

const form = useForm({
    title: '',
    content: '',
    is_published: false,
});

const submit = () => {
    form.post(route('managit.announcements.store'));
};
</script>

<template>
    <Head title="Create Announcement" />

    <WhmcsAdminLayout>
        <template #header>
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right">
                            <Link :href="route('managit.announcements.index')" class="btn btn-secondary">
                                <i class="ti ti-arrow-left me-1"></i> Back
                            </Link>
                        </div>
                        <h4 class="page-title">Create Announcement</h4>
                    </div>
                </div>
            </div>
        </template>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form @submit.prevent="submit">
                            <div class="mb-3">
                                <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                                <input
                                    id="title"
                                    type="text"
                                    v-model="form.title"
                                    class="form-control"
                                    :class="{ 'is-invalid': form.errors.title }"
                                    required
                                    placeholder="Announcement title"
                                />
                                <div v-if="form.errors.title" class="invalid-feedback">{{ form.errors.title }}</div>
                            </div>

                            <div class="mb-3">
                                <label for="content" class="form-label">Content <span class="text-danger">*</span></label>
                                <textarea
                                    id="content"
                                    v-model="form.content"
                                    class="form-control"
                                    :class="{ 'is-invalid': form.errors.content }"
                                    rows="10"
                                    required
                                    placeholder="Announcement content (supports HTML)"
                                ></textarea>
                                <small class="text-muted">You can use HTML formatting</small>
                                <div v-if="form.errors.content" class="invalid-feedback">{{ form.errors.content }}</div>
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input
                                        id="is_published"
                                        type="checkbox"
                                        v-model="form.is_published"
                                        class="form-check-input"
                                    />
                                    <label for="is_published" class="form-check-label">
                                        Publish immediately (visible to clients)
                                    </label>
                                </div>
                                <small class="text-muted">If unchecked, the announcement will be saved as a draft</small>
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <Link :href="route('managit.announcements.index')" class="btn btn-secondary">
                                    Cancel
                                </Link>
                                <button
                                    type="submit"
                                    class="btn btn-primary"
                                    :disabled="form.processing"
                                >
                                    <span v-if="!form.processing">Create Announcement</span>
                                    <span v-else>Creating...</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </WhmcsAdminLayout>
</template>
