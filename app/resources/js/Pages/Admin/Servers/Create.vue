<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import WhmcsAdminLayout from '@/Layouts/WhmcsAdminLayout.vue';

const form = useForm({
    name: '',
    hostname: '',
    ip_address: '',
    type: 'cpanel',
    port: 2087,
    username: '',
    password: '',
    access_hash: '',
    use_ssl: true,
    max_accounts: 0,
    nameserver1: '',
    nameserver2: '',
    nameserver3: '',
    nameserver4: '',
    is_active: true,
    notes: '',
});

const submit = () => {
    form.post(route('managit.servers.store'));
};
</script>

<template>
    <Head title="Add Server" />

    <WhmcsAdminLayout>
        <template #header>
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right">
                            <Link :href="route('managit.servers.index')" class="btn btn-secondary">
                                <i class="ti ti-arrow-left me-1"></i> Back to Servers
                            </Link>
                        </div>
                        <h4 class="page-title">Add Server</h4>
                    </div>
                </div>
            </div>
        </template>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form @submit.prevent="submit">
                            <h5 class="mb-3">Basic Information</h5>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="name" class="form-label">Server Name *</label>
                                    <input 
                                        v-model="form.name" 
                                        type="text" 
                                        class="form-control" 
                                        :class="{ 'is-invalid': form.errors.name }"
                                        id="name"
                                        placeholder="Web Server 1"
                                        required
                                    >
                                    <div v-if="form.errors.name" class="invalid-feedback">{{ form.errors.name }}</div>
                                </div>
                                <div class="col-md-6">
                                    <label for="type" class="form-label">Server Type *</label>
                                    <select 
                                        v-model="form.type" 
                                        class="form-select" 
                                        :class="{ 'is-invalid': form.errors.type }"
                                        id="type"
                                        @change="form.port = form.type === 'plesk' ? 8443 : 2087"
                                        required
                                    >
                                        <option value="cpanel">cPanel/WHM</option>
                                        <option value="plesk">Plesk</option>
                                        <option value="directadmin">DirectAdmin</option>
                                        <option value="custom">Custom</option>
                                    </select>
                                    <div v-if="form.errors.type" class="invalid-feedback">{{ form.errors.type }}</div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="hostname" class="form-label">Hostname *</label>
                                    <input 
                                        v-model="form.hostname" 
                                        type="text" 
                                        class="form-control" 
                                        :class="{ 'is-invalid': form.errors.hostname }"
                                        id="hostname"
                                        placeholder="server1.example.com"
                                        required
                                    >
                                    <div v-if="form.errors.hostname" class="invalid-feedback">{{ form.errors.hostname }}</div>
                                </div>
                                <div class="col-md-6">
                                    <label for="ip_address" class="form-label">IP Address *</label>
                                    <input 
                                        v-model="form.ip_address" 
                                        type="text" 
                                        class="form-control" 
                                        :class="{ 'is-invalid': form.errors.ip_address }"
                                        id="ip_address"
                                        placeholder="192.168.1.1"
                                        required
                                    >
                                    <div v-if="form.errors.ip_address" class="invalid-feedback">{{ form.errors.ip_address }}</div>
                                </div>
                            </div>

                            <h5 class="mb-3 mt-4">Connection Details</h5>
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="port" class="form-label">Port *</label>
                                    <input 
                                        v-model.number="form.port" 
                                        type="number" 
                                        class="form-control" 
                                        :class="{ 'is-invalid': form.errors.port }"
                                        id="port"
                                        min="1"
                                        max="65535"
                                        required
                                    >
                                    <div v-if="form.errors.port" class="invalid-feedback">{{ form.errors.port }}</div>
                                </div>
                                <div class="col-md-4">
                                    <label for="username" class="form-label">Username</label>
                                    <input 
                                        v-model="form.username" 
                                        type="text" 
                                        class="form-control" 
                                        :class="{ 'is-invalid': form.errors.username }"
                                        id="username"
                                        placeholder="root"
                                    >
                                    <div v-if="form.errors.username" class="invalid-feedback">{{ form.errors.username }}</div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check form-switch mt-4">
                                        <input 
                                            v-model="form.use_ssl" 
                                            type="checkbox" 
                                            class="form-check-input" 
                                            id="use_ssl"
                                        >
                                        <label class="form-check-label" for="use_ssl">
                                            Use SSL
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="password" class="form-label">Password</label>
                                    <input 
                                        v-model="form.password" 
                                        type="password" 
                                        class="form-control" 
                                        :class="{ 'is-invalid': form.errors.password }"
                                        id="password"
                                        autocomplete="new-password"
                                    >
                                    <div v-if="form.errors.password" class="invalid-feedback">{{ form.errors.password }}</div>
                                    <small class="text-muted">Leave empty if using access hash</small>
                                </div>
                                <div class="col-md-6">
                                    <label for="access_hash" class="form-label">Access Hash / API Token</label>
                                    <textarea 
                                        v-model="form.access_hash" 
                                        class="form-control" 
                                        :class="{ 'is-invalid': form.errors.access_hash }"
                                        id="access_hash"
                                        rows="3"
                                    ></textarea>
                                    <div v-if="form.errors.access_hash" class="invalid-feedback">{{ form.errors.access_hash }}</div>
                                    <small class="text-muted">For cPanel/WHM API access</small>
                                </div>
                            </div>

                            <h5 class="mb-3 mt-4">Nameservers</h5>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="nameserver1" class="form-label">Nameserver 1</label>
                                    <input 
                                        v-model="form.nameserver1" 
                                        type="text" 
                                        class="form-control" 
                                        id="nameserver1"
                                        placeholder="ns1.example.com"
                                    >
                                </div>
                                <div class="col-md-6">
                                    <label for="nameserver2" class="form-label">Nameserver 2</label>
                                    <input 
                                        v-model="form.nameserver2" 
                                        type="text" 
                                        class="form-control" 
                                        id="nameserver2"
                                        placeholder="ns2.example.com"
                                    >
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="nameserver3" class="form-label">Nameserver 3</label>
                                    <input 
                                        v-model="form.nameserver3" 
                                        type="text" 
                                        class="form-control" 
                                        id="nameserver3"
                                        placeholder="ns3.example.com"
                                    >
                                </div>
                                <div class="col-md-6">
                                    <label for="nameserver4" class="form-label">Nameserver 4</label>
                                    <input 
                                        v-model="form.nameserver4" 
                                        type="text" 
                                        class="form-control" 
                                        id="nameserver4"
                                        placeholder="ns4.example.com"
                                    >
                                </div>
                            </div>

                            <h5 class="mb-3 mt-4">Limits & Status</h5>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="max_accounts" class="form-label">Maximum Accounts *</label>
                                    <input 
                                        v-model.number="form.max_accounts" 
                                        type="number" 
                                        class="form-control" 
                                        :class="{ 'is-invalid': form.errors.max_accounts }"
                                        id="max_accounts"
                                        min="0"
                                        required
                                    >
                                    <div v-if="form.errors.max_accounts" class="invalid-feedback">{{ form.errors.max_accounts }}</div>
                                    <small class="text-muted">0 = unlimited</small>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check form-switch mt-4">
                                        <input 
                                            v-model="form.is_active" 
                                            type="checkbox" 
                                            class="form-check-input" 
                                            id="is_active"
                                        >
                                        <label class="form-check-label" for="is_active">
                                            {{ form.is_active ? 'Active' : 'Inactive' }}
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12">
                                    <label for="notes" class="form-label">Notes</label>
                                    <textarea 
                                        v-model="form.notes" 
                                        class="form-control" 
                                        id="notes"
                                        rows="3"
                                        placeholder="Additional notes about this server..."
                                    ></textarea>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-12">
                                    <button 
                                        type="submit" 
                                        class="btn btn-primary"
                                        :disabled="form.processing"
                                    >
                                        <i class="ti ti-device-floppy me-1"></i>
                                        {{ form.processing ? 'Creating...' : 'Create Server' }}
                                    </button>
                                    <Link :href="route('managit.servers.index')" class="btn btn-secondary ms-2">
                                        Cancel
                                    </Link>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </WhmcsAdminLayout>
</template>
