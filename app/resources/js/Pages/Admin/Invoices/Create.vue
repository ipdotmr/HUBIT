<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import WhmcsAdminLayout from '@/Layouts/WhmcsAdminLayout.vue';

const props = defineProps({
    clients: Array
});

const form = useForm({
    client_id: '',
    due_date: '',
    items: [{ description: '', amount: 0 }]
});

const addItem = () => {
    form.items.push({ description: '', amount: 0 });
};

const removeItem = (index) => {
    form.items.splice(index, 1);
};

const calculateTotal = () => {
    return form.items.reduce((sum, item) => sum + parseFloat(item.amount || 0), 0).toFixed(2);
};

const submit = () => {
    form.post(route('managit.invoices.store'));
};
</script>

<template>
    <Head title="Create Invoice" />
    
    <WhmcsAdminLayout>
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="mb-0">Create New Invoice</h3>
                <Link :href="route('managit.invoices.index')" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Invoices
                </Link>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form @submit.prevent="submit">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Client *</label>
                                <select v-model="form.client_id" class="form-select" :class="{ 'is-invalid': form.errors.client_id }" required>
                                    <option value="">Select a client...</option>
                                    <option v-for="client in clients" :key="client.id" :value="client.id">
                                        {{ client.name }} ({{ client.email }})
                                    </option>
                                </select>
                                <div v-if="form.errors.client_id" class="invalid-feedback">{{ form.errors.client_id }}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Due Date *</label>
                                <input v-model="form.due_date" type="date" class="form-control" :class="{ 'is-invalid': form.errors.due_date }" required />
                                <div v-if="form.errors.due_date" class="invalid-feedback">{{ form.errors.due_date }}</div>
                            </div>
                        </div>

                        <h5 class="mb-3">Invoice Items</h5>
                        <div v-for="(item, index) in form.items" :key="index" class="row mb-3">
                            <div class="col-md-8">
                                <input v-model="item.description" type="text" class="form-control" placeholder="Description" required />
                            </div>
                            <div class="col-md-3">
                                <input v-model="item.amount" type="number" step="0.01" class="form-control" placeholder="Amount" required />
                            </div>
                            <div class="col-md-1">
                                <button v-if="form.items.length > 1" type="button" class="btn btn-danger" @click="removeItem(index)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>

                        <button type="button" class="btn btn-secondary mb-4" @click="addItem">
                            <i class="fas fa-plus me-2"></i>Add Item
                        </button>

                        <div class="row mb-4">
                            <div class="col-md-12 text-end">
                                <h4>Total: ${{ calculateTotal() }}</h4>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary" :disabled="form.processing">
                                <i class="fas fa-save me-2"></i>
                                {{ form.processing ? 'Creating...' : 'Create Invoice' }}
                            </button>
                            <Link :href="route('managit.invoices.index')" class="btn btn-secondary">Cancel</Link>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </WhmcsAdminLayout>
</template>
