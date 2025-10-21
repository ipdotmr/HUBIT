<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import WhmcsAdminLayout from '@/Layouts/WhmcsAdminLayout.vue';

const props = defineProps({
    clients: Array,
    products: Array
});

const form = useForm({
    client_id: '',
    items: [{ product_id: '', quantity: 1 }],
    notes: ''
});

const addItem = () => {
    form.items.push({ product_id: '', quantity: 1 });
};

const removeItem = (index) => {
    if (form.items.length > 1) {
        form.items.splice(index, 1);
    }
};

const getProductPrice = (productId) => {
    const product = props.products.find(p => p.id === parseInt(productId));
    return product ? product.price : 0;
};

const subtotal = computed(() => {
    return form.items.reduce((sum, item) => {
        const price = getProductPrice(item.product_id);
        return sum + (price * item.quantity);
    }, 0);
});

const tax = computed(() => subtotal.value * 0.15);
const total = computed(() => subtotal.value + tax.value);

const submit = () => {
    form.post(route('managit.orders.store'));
};
</script>

<template>
    <Head title="Create New Order" />

    <WhmcsAdminLayout>
        <div class="container-fluid">
            <div class="row mb-4">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="h3 mb-0">Create New Order</h1>
                            <p class="text-muted">Create a new order for a client</p>
                        </div>
                        <Link :href="route('managit.orders.index')" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Orders
                        </Link>
                    </div>
                </div>
            </div>

            <form @submit.prevent="submit">
                <div class="row">
                    <div class="col-lg-8">
                        <!-- Client Selection -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">Client Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Select Client *</label>
                                    <select v-model="form.client_id" class="form-select" :class="{ 'is-invalid': form.errors.client_id }" required>
                                        <option value="">Choose a client...</option>
                                        <option v-for="client in clients" :key="client.id" :value="client.id">
                                            {{ client.name }} ({{ client.email }})
                                        </option>
                                    </select>
                                    <div v-if="form.errors.client_id" class="invalid-feedback">{{ form.errors.client_id }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Order Items -->
                        <div class="card mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Order Items</h5>
                                <button type="button" @click="addItem" class="btn btn-sm btn-primary">
                                    <i class="fas fa-plus me-2"></i>Add Item
                                </button>
                            </div>
                            <div class="card-body">
                                <div v-for="(item, index) in form.items" :key="index" class="row mb-3 align-items-end">
                                    <div class="col-md-6">
                                        <label class="form-label">Product *</label>
                                        <select v-model="item.product_id" class="form-select" required>
                                            <option value="">Select product...</option>
                                            <option v-for="product in products" :key="product.id" :value="product.id">
                                                {{ product.name }} - ${{ product.price }}/{{ product.billing_cycle }}
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Quantity *</label>
                                        <input v-model.number="item.quantity" type="number" class="form-control" min="1" required />
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Total</label>
                                        <input :value="'$' + (getProductPrice(item.product_id) * item.quantity).toFixed(2)" type="text" class="form-control" readonly />
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" @click="removeItem(index)" class="btn btn-danger" :disabled="form.items.length === 1">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                <div v-if="form.errors.items" class="text-danger">{{ form.errors.items }}</div>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">Additional Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Order Notes</label>
                                    <textarea v-model="form.notes" class="form-control" rows="3" placeholder="Add any notes about this order..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Summary -->
                    <div class="col-lg-4">
                        <div class="card sticky-top" style="top: 20px;">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">Order Summary</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Subtotal:</span>
                                    <strong>${{ subtotal.toFixed(2) }}</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Tax (15%):</span>
                                    <strong>${{ tax.toFixed(2) }}</strong>
                                </div>
                                <hr />
                                <div class="d-flex justify-content-between mb-3">
                                    <span class="h5">Total:</span>
                                    <strong class="h5 text-primary">${{ total.toFixed(2) }}</strong>
                                </div>
                                <button type="submit" class="btn btn-primary w-100" :disabled="form.processing">
                                    <i class="fas fa-check me-2"></i>
                                    {{ form.processing ? 'Creating...' : 'Create Order' }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </WhmcsAdminLayout>
</template>
