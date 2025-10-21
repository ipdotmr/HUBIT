<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import WhmcsAdminLayout from '@/Layouts/WhmcsAdminLayout.vue';

const props = defineProps({
    clients: Array
});

const form = useForm({
    client_id: '',
    subject: '',
    priority: 'medium',
    department: 'General',
    message: ''
});

const submit = () => {
    form.post(route('managit.support.store'));
};
</script>

<template>
    <Head title="Create New Ticket" />
    
    <WhmcsAdminLayout>
        <div class="container-fluid">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="mb-0">Create New Ticket</h3>
                    <p class="text-muted mb-0">Open a new support ticket</p>
                </div>
                <Link :href="route('managit.support.index')" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Tickets
                </Link>
            </div>

            <!-- Create Form -->
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form @submit.prevent="submit">
                        <div class="row g-4">
                            <!-- Client Selection -->
                            <div class="col-md-6">
                                <label class="form-label">Client <span class="text-danger">*</span></label>
                                <select v-model="form.client_id" class="form-select" :class="{ 'is-invalid': form.errors.client_id }" required>
                                    <option value="">Select a client...</option>
                                    <option v-for="client in clients" :key="client.id" :value="client.id">
                                        {{ client.name }} ({{ client.email }})
                                    </option>
                                </select>
                                <div v-if="form.errors.client_id" class="invalid-feedback">{{ form.errors.client_id }}</div>
                            </div>

                            <!-- Priority -->
                            <div class="col-md-6">
                                <label class="form-label">Priority <span class="text-danger">*</span></label>
                                <select v-model="form.priority" class="form-select" :class="{ 'is-invalid': form.errors.priority }" required>
                                    <option value="low">Low</option>
                                    <option value="medium">Medium</option>
                                    <option value="high">High</option>
                                    <option value="urgent">Urgent</option>
                                </select>
                                <div v-if="form.errors.priority" class="invalid-feedback">{{ form.errors.priority }}</div>
                            </div>

                            <!-- Department -->
                            <div class="col-md-6">
                                <label class="form-label">Department</label>
                                <select v-model="form.department" class="form-select">
                                    <option value="General">General</option>
                                    <option value="Technical">Technical</option>
                                    <option value="Billing">Billing</option>
                                    <option value="Sales">Sales</option>
                                </select>
                            </div>

                            <!-- Subject -->
                            <div class="col-md-12">
                                <label class="form-label">Subject <span class="text-danger">*</span></label>
                                <input 
                                    v-model="form.subject" 
                                    type="text" 
                                    class="form-control" 
                                    :class="{ 'is-invalid': form.errors.subject }"
                                    placeholder="Brief description of the issue"
                                    required
                                >
                                <div v-if="form.errors.subject" class="invalid-feedback">{{ form.errors.subject }}</div>
                            </div>

                            <!-- Message -->
                            <div class="col-md-12">
                                <label class="form-label">Message <span class="text-danger">*</span></label>
                                <textarea 
                                    v-model="form.message" 
                                    class="form-control" 
                                    :class="{ 'is-invalid': form.errors.message }"
                                    rows="8"
                                    placeholder="Detailed description of the issue..."
                                    required
                                ></textarea>
                                <div v-if="form.errors.message" class="invalid-feedback">{{ form.errors.message }}</div>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="col-md-12">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary" :disabled="form.processing">
                                        <i class="fas fa-save me-2"></i>
                                        {{ form.processing ? 'Creating...' : 'Create Ticket' }}
                                    </button>
                                    <Link :href="route('managit.support.index')" class="btn btn-secondary">
                                        Cancel
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </WhmcsAdminLayout>
</template>
