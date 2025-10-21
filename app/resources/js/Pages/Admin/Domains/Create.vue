<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import WhmcsAdminLayout from '@/Layouts/WhmcsAdminLayout.vue';

const props = defineProps({
    clients: Array
});

const form = useForm({
    client_id: '',
    domain: '',
    registration_date: '',
    expiry_date: '',
    registrar: '',
    nameservers: ['', ''],
});

const addNameserver = () => {
    form.nameservers.push('');
};

const removeNameserver = (index) => {
    form.nameservers.splice(index, 1);
};

const submit = () => {
    form.post(route('managit.domains.store'));
};
</script>

<template>
    <Head title="Register Domain" />
    
    <WhmcsAdminLayout>
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="mb-0">Register Domain</h3>
                <Link :href="route('managit.domains.index')" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Domains
                </Link>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form @submit.prevent="submit">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="client_id" class="form-label">Client *</label>
                                <select 
                                    v-model="form.client_id" 
                                    class="form-select"
                                    :class="{ 'is-invalid': form.errors.client_id }"
                                    id="client_id"
                                    required
                                >
                                    <option value="">Select Client</option>
                                    <option v-for="client in clients" :key="client.id" :value="client.id">
                                        {{ client.name }} ({{ client.email }})
                                    </option>
                                </select>
                                <div v-if="form.errors.client_id" class="invalid-feedback">{{ form.errors.client_id }}</div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="domain" class="form-label">Domain Name *</label>
                                <input 
                                    v-model="form.domain" 
                                    type="text" 
                                    class="form-control" 
                                    :class="{ 'is-invalid': form.errors.domain }"
                                    id="domain"
                                    placeholder="example.com"
                                    required
                                >
                                <div v-if="form.errors.domain" class="invalid-feedback">{{ form.errors.domain }}</div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="registration_date" class="form-label">Registration Date *</label>
                                <input 
                                    v-model="form.registration_date" 
                                    type="date" 
                                    class="form-control" 
                                    :class="{ 'is-invalid': form.errors.registration_date }"
                                    id="registration_date"
                                    required
                                >
                                <div v-if="form.errors.registration_date" class="invalid-feedback">{{ form.errors.registration_date }}</div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="expiry_date" class="form-label">Expiry Date *</label>
                                <input 
                                    v-model="form.expiry_date" 
                                    type="date" 
                                    class="form-control" 
                                    :class="{ 'is-invalid': form.errors.expiry_date }"
                                    id="expiry_date"
                                    required
                                >
                                <div v-if="form.errors.expiry_date" class="invalid-feedback">{{ form.errors.expiry_date }}</div>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label for="registrar" class="form-label">Registrar *</label>
                                <select 
                                    v-model="form.registrar" 
                                    class="form-select" 
                                    :class="{ 'is-invalid': form.errors.registrar }"
                                    id="registrar"
                                    required
                                >
                                    <option value="">Select Registrar</option>
                                    <option value="hubit">Hubit Registrar (Offline)</option>
                                    <option value="coccaep">CoccaEP</option>
                                    <option value="namecom">Name.com</option>
                                    <option value="namecheap">Namecheap</option>
                                    <option value="resellerclub">ResellerClub</option>
                                </select>
                                <div v-if="form.errors.registrar" class="invalid-feedback">{{ form.errors.registrar }}</div>
                                <div class="form-text">Select "Hubit Registrar" for offline/manual domain registration</div>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label">Nameservers</label>
                                <div v-for="(ns, index) in form.nameservers" :key="index" class="mb-2">
                                    <div class="input-group">
                                        <input 
                                            v-model="form.nameservers[index]" 
                                            type="text" 
                                            class="form-control" 
                                            :placeholder="`Nameserver ${index + 1}`"
                                        >
                                        <button 
                                            v-if="form.nameservers.length > 1"
                                            type="button" 
                                            class="btn btn-outline-danger" 
                                            @click="removeNameserver(index)"
                                        >
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-primary" @click="addNameserver">
                                    <i class="fas fa-plus me-1"></i>Add Nameserver
                                </button>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <Link :href="route('managit.domains.index')" class="btn btn-secondary">
                                Cancel
                            </Link>
                            <button type="submit" class="btn btn-primary" :disabled="form.processing">
                                <span v-if="form.processing" class="spinner-border spinner-border-sm me-2"></span>
                                Register Domain
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </WhmcsAdminLayout>
</template>
