<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import WhmcsAdminLayout from '@/Layouts/WhmcsAdminLayout.vue';

const props = defineProps({
    groups: Array
});

const form = useForm({
    name: '',
    description: '',
    product_group_id: '',
    price: '',
    billing_cycle: 'monthly',
    setup_fee: '0',
    status: 'active'
});

const submit = () => {
    form.post(route('managit.products.store'));
};
</script>

<template>
    <Head title="Create Product" />
    
    <WhmcsAdminLayout>
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="mb-0">Create Product</h3>
                <Link :href="route('managit.products.index')" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Products
                </Link>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form @submit.prevent="submit">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Product Name *</label>
                                <input 
                                    v-model="form.name" 
                                    type="text" 
                                    class="form-control" 
                                    :class="{ 'is-invalid': form.errors.name }"
                                    id="name"
                                    required
                                >
                                <div v-if="form.errors.name" class="invalid-feedback">{{ form.errors.name }}</div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="product_group_id" class="form-label">Product Group *</label>
                                <select 
                                    v-model="form.product_group_id" 
                                    class="form-select"
                                    :class="{ 'is-invalid': form.errors.product_group_id }"
                                    id="product_group_id"
                                    required
                                >
                                    <option value="">Select Group</option>
                                    <option v-for="group in groups" :key="group.id" :value="group.id">
                                        {{ group.name }}
                                    </option>
                                </select>
                                <div v-if="form.errors.product_group_id" class="invalid-feedback">{{ form.errors.product_group_id }}</div>
                            </div>

                            <div class="col-12 mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea 
                                    v-model="form.description" 
                                    class="form-control" 
                                    :class="{ 'is-invalid': form.errors.description }"
                                    id="description"
                                    rows="4"
                                ></textarea>
                                <div v-if="form.errors.description" class="invalid-feedback">{{ form.errors.description }}</div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="price" class="form-label">Price *</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input 
                                        v-model="form.price" 
                                        type="number" 
                                        step="0.01"
                                        class="form-control" 
                                        :class="{ 'is-invalid': form.errors.price }"
                                        id="price"
                                        required
                                    >
                                </div>
                                <div v-if="form.errors.price" class="invalid-feedback">{{ form.errors.price }}</div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="billing_cycle" class="form-label">Billing Cycle *</label>
                                <select 
                                    v-model="form.billing_cycle" 
                                    class="form-select"
                                    :class="{ 'is-invalid': form.errors.billing_cycle }"
                                    id="billing_cycle"
                                    required
                                >
                                    <option value="monthly">Monthly</option>
                                    <option value="quarterly">Quarterly</option>
                                    <option value="semi-annually">Semi-Annually</option>
                                    <option value="annually">Annually</option>
                                    <option value="biennially">Biennially</option>
                                    <option value="triennially">Triennially</option>
                                </select>
                                <div v-if="form.errors.billing_cycle" class="invalid-feedback">{{ form.errors.billing_cycle }}</div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="setup_fee" class="form-label">Setup Fee</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input 
                                        v-model="form.setup_fee" 
                                        type="number" 
                                        step="0.01"
                                        class="form-control" 
                                        :class="{ 'is-invalid': form.errors.setup_fee }"
                                        id="setup_fee"
                                    >
                                </div>
                                <div v-if="form.errors.setup_fee" class="invalid-feedback">{{ form.errors.setup_fee }}</div>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label for="status" class="form-label">Status *</label>
                                <select 
                                    v-model="form.status" 
                                    class="form-select"
                                    :class="{ 'is-invalid': form.errors.status }"
                                    id="status"
                                    required
                                >
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                                <div v-if="form.errors.status" class="invalid-feedback">{{ form.errors.status }}</div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <Link :href="route('managit.products.index')" class="btn btn-secondary">
                                Cancel
                            </Link>
                            <button type="submit" class="btn btn-primary" :disabled="form.processing">
                                <span v-if="form.processing" class="spinner-border spinner-border-sm me-2"></span>
                                Create Product
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </WhmcsAdminLayout>
</template>
