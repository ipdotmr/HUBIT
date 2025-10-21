<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import WhmcsAdminLayout from '@/Layouts/WhmcsAdminLayout.vue';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    company: '',
    address: '',
    city: '',
    state: '',
    postcode: '',
    country: '',
    phone: '',
});

const submit = () => {
    form.post(route('managit.clients.store'));
};
</script>

<template>
    <Head title="Create Client" />

    <WhmcsAdminLayout>
        <div class="container-fluid">
            <!-- Page Title -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="mb-0">Create New Client</h3>
                <Link :href="route('managit.clients.index')" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Clients
                </Link>
            </div>

            <!-- Create Form -->
            <div class="row">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Client Information</h5>
                        </div>
                        <div class="card-body">
                            <form @submit.prevent="submit">
                                <!-- Personal Information -->
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="name" class="form-label">Full Name *</label>
                                        <input
                                            id="name"
                                            v-model="form.name"
                                            type="text"
                                            class="form-control"
                                            :class="{ 'is-invalid': form.errors.name }"
                                            required
                                        />
                                        <div v-if="form.errors.name" class="invalid-feedback">
                                            {{ form.errors.name }}
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="email" class="form-label">Email Address *</label>
                                        <input
                                            id="email"
                                            v-model="form.email"
                                            type="email"
                                            class="form-control"
                                            :class="{ 'is-invalid': form.errors.email }"
                                            required
                                        />
                                        <div v-if="form.errors.email" class="invalid-feedback">
                                            {{ form.errors.email }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Password -->
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="password" class="form-label">Password *</label>
                                        <input
                                            id="password"
                                            v-model="form.password"
                                            type="password"
                                            class="form-control"
                                            :class="{ 'is-invalid': form.errors.password }"
                                            required
                                        />
                                        <div v-if="form.errors.password" class="invalid-feedback">
                                            {{ form.errors.password }}
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="password_confirmation" class="form-label">Confirm Password *</label>
                                        <input
                                            id="password_confirmation"
                                            v-model="form.password_confirmation"
                                            type="password"
                                            class="form-control"
                                            required
                                        />
                                    </div>
                                </div>

                                <!-- Company Information -->
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="company" class="form-label">Company Name</label>
                                        <input
                                            id="company"
                                            v-model="form.company"
                                            type="text"
                                            class="form-control"
                                        />
                                    </div>

                                    <div class="col-md-6">
                                        <label for="phone" class="form-label">Phone Number</label>
                                        <input
                                            id="phone"
                                            v-model="form.phone"
                                            type="tel"
                                            class="form-control"
                                        />
                                    </div>
                                </div>

                                <!-- Address -->
                                <div class="mb-3">
                                    <label for="address" class="form-label">Address</label>
                                    <textarea
                                        id="address"
                                        v-model="form.address"
                                        class="form-control"
                                        rows="2"
                                    ></textarea>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label for="city" class="form-label">City</label>
                                        <input
                                            id="city"
                                            v-model="form.city"
                                            type="text"
                                            class="form-control"
                                        />
                                    </div>

                                    <div class="col-md-4">
                                        <label for="state" class="form-label">State/Province</label>
                                        <input
                                            id="state"
                                            v-model="form.state"
                                            type="text"
                                            class="form-control"
                                        />
                                    </div>

                                    <div class="col-md-4">
                                        <label for="postcode" class="form-label">Postal Code</label>
                                        <input
                                            id="postcode"
                                            v-model="form.postcode"
                                            type="text"
                                            class="form-control"
                                        />
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label for="country" class="form-label">Country</label>
                                    <select
                                        id="country"
                                        v-model="form.country"
                                        class="form-select"
                                    >
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

                                <!-- Submit Buttons -->
                                <div class="d-flex gap-2">
                                    <button
                                        type="submit"
                                        class="btn btn-primary"
                                        :disabled="form.processing"
                                    >
                                        <i class="fas fa-save me-2"></i>
                                        {{ form.processing ? 'Creating...' : 'Create Client' }}
                                    </button>
                                    <Link :href="route('managit.clients.index')" class="btn btn-secondary">
                                        Cancel
                                    </Link>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Help Panel -->
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Information</h5>
                        </div>
                        <div class="card-body">
                            <p class="mb-2"><strong>Required Fields:</strong></p>
                            <ul class="mb-3">
                                <li>Full Name</li>
                                <li>Email Address</li>
                                <li>Password</li>
                            </ul>

                            <p class="mb-2"><strong>Password Requirements:</strong></p>
                            <ul class="mb-0">
                                <li>Minimum 8 characters</li>
                                <li>Must match confirmation</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </WhmcsAdminLayout>
</template>

<style scoped>
.card {
    transition: transform 0.2s, box-shadow 0.2s;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
}
</style>
