<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import WhmcsAdminLayout from '@/Layouts/WhmcsAdminLayout.vue';

const props = defineProps({
    client: Object
});

const form = useForm({
    first_name: props.client.first_name || '',
    last_name: props.client.last_name || '',
    email: props.client.email || '',
    company_name: props.client.company || '',
    address: props.client.address || '',
    city: props.client.city || '',
    state: props.client.state || '',
    postal_code: props.client.postcode || '',
    country: props.client.country || '',
    phone: props.client.phone || '',
    status: props.client.status || 'active',
});

const submit = () => {
    form.put(route('managit.clients.update', props.client.id));
};
</script>

<template>
    <Head title="Edit Client" />
    <WhmcsAdminLayout>
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="mb-0">Edit Client</h3>
                <Link :href="route('managit.clients.show', props.client.id)" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Client
                </Link>
            </div>
            <div class="row">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Client Information</h5>
                        </div>
                        <div class="card-body">
                            <form @submit.prevent="submit">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="first_name" class="form-label">First Name *</label>
                                        <input id="first_name" v-model="form.first_name" type="text" class="form-control" :class="{ 'is-invalid': form.errors.first_name }" required />
                                        <div v-if="form.errors.first_name" class="invalid-feedback">{{ form.errors.first_name }}</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="last_name" class="form-label">Last Name *</label>
                                        <input id="last_name" v-model="form.last_name" type="text" class="form-control" :class="{ 'is-invalid': form.errors.last_name }" required />
                                        <div v-if="form.errors.last_name" class="invalid-feedback">{{ form.errors.last_name }}</div>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="email" class="form-label">Email Address *</label>
                                        <input id="email" v-model="form.email" type="email" class="form-control" :class="{ 'is-invalid': form.errors.email }" required />
                                        <div v-if="form.errors.email" class="invalid-feedback">{{ form.errors.email }}</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="company_name" class="form-label">Company Name</label>
                                        <input id="company_name" v-model="form.company_name" type="text" class="form-control" />
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="address" class="form-label">Address</label>
                                    <textarea id="address" v-model="form.address" class="form-control" rows="2"></textarea>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label for="city" class="form-label">City</label>
                                        <input id="city" v-model="form.city" type="text" class="form-control" />
                                    </div>
                                    <div class="col-md-4">
                                        <label for="state" class="form-label">State/Province</label>
                                        <input id="state" v-model="form.state" type="text" class="form-control" />
                                    </div>
                                    <div class="col-md-4">
                                        <label for="postal_code" class="form-label">Postal Code</label>
                                        <input id="postal_code" v-model="form.postal_code" type="text" class="form-control" />
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label for="country" class="form-label">Country</label>
                                    <select id="country" v-model="form.country" class="form-select">
                                        <option value="">Select Country</option>
                                        <option value="US">United States</option>
                                        <option value="GB">United Kingdom</option>
                                        <option value="CA">Canada</option>
                                        <option value="AU">Australia</option>
                                        <option value="FR">France</option>
                                        <option value="DE">Germany</option>
                                        <option value="MR">Mauritania</option>
                                    </select>
                                </div>
                                <div class="mb-4">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <input id="phone" v-model="form.phone" type="tel" class="form-control" />
                                </div>
                                <div class="mb-4">
                                    <label for="status" class="form-label">Status</label>
                                    <select id="status" v-model="form.status" class="form-select">
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                        <option value="suspended">Suspended</option>
                                    </select>
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary" :disabled="form.processing">
                                        <i class="fas fa-save me-2"></i>
                                        {{ form.processing ? 'Saving...' : 'Save Changes' }}
                                    </button>
                                    <Link :href="route('managit.clients.show', props.client.id)" class="btn btn-secondary">Cancel</Link>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Information</h5>
                        </div>
                        <div class="card-body">
                            <p class="mb-2"><strong>Required Fields:</strong></p>
                            <ul class="mb-3">
                                <li>First Name</li>
                                <li>Last Name</li>
                                <li>Email Address</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </WhmcsAdminLayout>
</template>
