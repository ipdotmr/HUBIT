<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import WhmcsAdminLayout from '@/Layouts/WhmcsAdminLayout.vue';

const props = defineProps({
    domain: Object
});

const deleteDomain = () => {
    if (confirm('Are you sure you want to delete this domain?')) {
        router.delete(route('managit.domains.destroy', props.domain.id));
    }
};
</script>

<template>
    <Head title="Domain Details" />
    
    <WhmcsAdminLayout>
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="mb-0">Domain Details</h3>
                <div>
                    <Link :href="route('managit.domains.edit', domain.id)" class="btn btn-primary me-2">
                        <i class="fas fa-edit me-2"></i>Edit
                    </Link>
                    <button @click="deleteDomain" class="btn btn-danger me-2">
                        <i class="fas fa-trash me-2"></i>Delete
                    </button>
                    <Link :href="route('managit.domains.index')" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Domains
                    </Link>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Domain Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <strong>Domain Name:</strong>
                                </div>
                                <div class="col-md-8">
                                    <strong class="text-primary">{{ domain.domain }}</strong>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <strong>Client:</strong>
                                </div>
                                <div class="col-md-8">
                                    {{ domain.client_name }}
                                    <br>
                                    <small class="text-muted">{{ domain.client_email }}</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <strong>Registrar:</strong>
                                </div>
                                <div class="col-md-8">
                                    {{ domain.registrar }}
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <strong>Status:</strong>
                                </div>
                                <div class="col-md-8">
                                    <span class="badge" :class="{
                                        'bg-success': domain.status === 'active',
                                        'bg-warning': domain.status === 'pending',
                                        'bg-danger': domain.status === 'expired',
                                        'bg-secondary': domain.status === 'cancelled'
                                    }">
                                        {{ domain.status }}
                                    </span>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <strong>Nameservers:</strong>
                                </div>
                                <div class="col-md-8">
                                    <div v-if="domain.nameservers && domain.nameservers.length > 0">
                                        <div v-for="(ns, index) in domain.nameservers" :key="index" class="mb-1">
                                            <code>{{ ns }}</code>
                                        </div>
                                    </div>
                                    <span v-else class="text-muted">No nameservers configured</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Registration Details</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-12">
                                    <strong>Registration Date:</strong>
                                    <div class="text-muted">{{ domain.registration_date }}</div>
                                </div>
                            </div>

                            <div class="row mb-0">
                                <div class="col-12">
                                    <strong>Expiry Date:</strong>
                                    <div class="text-danger fw-bold">{{ domain.expiry_date }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </WhmcsAdminLayout>
</template>
