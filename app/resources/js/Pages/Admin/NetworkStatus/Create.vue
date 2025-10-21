<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import WhmcsAdminLayout from '@/Layouts/WhmcsAdminLayout.vue';

const form = useForm({
    title: '',
    description: '',
    status: 'operational',
    priority: 'medium',
    started_at: new Date().toISOString().slice(0, 16),
    resolved_at: '',
    is_resolved: false,
});

const submit = () => {
    form.post(route('managit.network-status.store'));
};
</script>

<template>
    <Head title="Create Network Status" />

    <WhmcsAdminLayout>
        <template #header>
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right">
                            <Link :href="route('managit.network-status.index')" class="btn btn-secondary">
                                <i class="ti ti-arrow-left me-1"></i> Back
                            </Link>
                        </div>
                        <h4 class="page-title">Create Network Status</h4>
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
                                    placeholder="e.g., Server Maintenance"
                                />
                                <div v-if="form.errors.title" class="invalid-feedback">{{ form.errors.title }}</div>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                                <textarea
                                    id="description"
                                    v-model="form.description"
                                    class="form-control"
                                    :class="{ 'is-invalid': form.errors.description }"
                                    rows="4"
                                    required
                                    placeholder="Detailed description of the status or incident"
                                ></textarea>
                                <div v-if="form.errors.description" class="invalid-feedback">{{ form.errors.description }}</div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                    <select
                                        id="status"
                                        v-model="form.status"
                                        class="form-select"
                                        :class="{ 'is-invalid': form.errors.status }"
                                        required
                                    >
                                        <option value="operational">Operational</option>
                                        <option value="degraded">Degraded Performance</option>
                                        <option value="partial_outage">Partial Outage</option>
                                        <option value="major_outage">Major Outage</option>
                                        <option value="maintenance">Maintenance</option>
                                    </select>
                                    <div v-if="form.errors.status" class="invalid-feedback">{{ form.errors.status }}</div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="priority" class="form-label">Priority <span class="text-danger">*</span></label>
                                    <select
                                        id="priority"
                                        v-model="form.priority"
                                        class="form-select"
                                        :class="{ 'is-invalid': form.errors.priority }"
                                        required
                                    >
                                        <option value="low">Low</option>
                                        <option value="medium">Medium</option>
                                        <option value="high">High</option>
                                        <option value="critical">Critical</option>
                                    </select>
                                    <div v-if="form.errors.priority" class="invalid-feedback">{{ form.errors.priority }}</div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="started_at" class="form-label">Started At <span class="text-danger">*</span></label>
                                    <input
                                        id="started_at"
                                        type="datetime-local"
                                        v-model="form.started_at"
                                        class="form-control"
                                        :class="{ 'is-invalid': form.errors.started_at }"
                                        required
                                    />
                                    <div v-if="form.errors.started_at" class="invalid-feedback">{{ form.errors.started_at }}</div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="resolved_at" class="form-label">Resolved At</label>
                                    <input
                                        id="resolved_at"
                                        type="datetime-local"
                                        v-model="form.resolved_at"
                                        class="form-control"
                                        :class="{ 'is-invalid': form.errors.resolved_at }"
                                    />
                                    <div v-if="form.errors.resolved_at" class="invalid-feedback">{{ form.errors.resolved_at }}</div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input
                                        id="is_resolved"
                                        type="checkbox"
                                        v-model="form.is_resolved"
                                        class="form-check-input"
                                    />
                                    <label for="is_resolved" class="form-check-label">
                                        Mark as resolved
                                    </label>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <Link :href="route('managit.network-status.index')" class="btn btn-secondary">
                                    Cancel
                                </Link>
                                <button
                                    type="submit"
                                    class="btn btn-primary"
                                    :disabled="form.processing"
                                >
                                    <span v-if="!form.processing">Create Status</span>
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
