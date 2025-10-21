<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import WhmcsAdminLayout from '@/Layouts/WhmcsAdminLayout.vue';
import { ref } from 'vue';

const props = defineProps({
    accounts: Array,
    currencies: Array,
    settings: Object,
    auditLogs: Array
});

const showModal = ref(false);
const editingAccount = ref(null);

const form = useForm({
    gateway: '',
    name: '',
    account_number: '',
    bank_name: '',
    currency_id: null,
    status: 'active',
    notes: ''
});

const openAddModal = () => {
    editingAccount.value = null;
    form.reset();
    form.status = 'active';
    showModal.value = true;
};

const openEditModal = (account) => {
    editingAccount.value = account;
    form.gateway = account.gateway;
    form.name = account.name;
    form.account_number = account.account_number || '';
    form.bank_name = account.bank_name || '';
    form.currency_id = account.currency_id;
    form.status = account.status;
    form.notes = account.notes || '';
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    editingAccount.value = null;
    form.reset();
};

const submitForm = () => {
    if (editingAccount.value) {
        form.put(route('managit.settings.payment-accounts.update', editingAccount.value.id), {
            onSuccess: () => closeModal()
        });
    } else {
        form.post(route('managit.settings.payment-accounts.store'), {
            onSuccess: () => closeModal()
        });
    }
};

const deleteAccount = (accountId) => {
    if (confirm('Are you sure you want to delete this payment account?')) {
        form.delete(route('managit.settings.payment-accounts.delete', accountId));
    }
};
</script>

<template>
    <Head title="Payment Accounts - Settings" />
    
    <WhmcsAdminLayout>
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="mb-0">Payment Accounts</h3>
                    <p class="text-muted">Manage payment gateway accounts and configurations</p>
                </div>
                <button @click="openAddModal" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Add Account
                </button>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Configured Accounts</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Gateway</th>
                                    <th>Account Name</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="account in accounts" :key="account.id">
                                    <td><strong>{{ account.gateway }}</strong></td>
                                    <td>{{ account.name }}</td>
                                    <td>
                                        <span class="badge" :class="{
                                            'bg-success': account.status === 'active',
                                            'bg-danger': account.status === 'inactive'
                                        }">
                                            {{ account.status }}
                                        </span>
                                    </td>
                                    <td>
                                        <button @click="openEditModal(account)" class="btn btn-sm btn-primary me-2">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button @click="deleteAccount(account.id)" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr v-if="!accounts || accounts.length === 0">
                                    <td colspan="4" class="text-center text-muted py-4">No payment accounts configured</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add/Edit Modal -->
        <div v-if="showModal" class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ editingAccount ? 'Edit Payment Account' : 'Add Payment Account' }}</h5>
                        <button type="button" class="btn-close" @click="closeModal"></button>
                    </div>
                    <form @submit.prevent="submitForm">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Gateway Type *</label>
                                    <select v-model="form.gateway" class="form-select" required>
                                        <option value="">Select Gateway</option>
                                        <option value="Bank Transfer">Bank Transfer</option>
                                        <option value="Cash">Cash</option>
                                        <option value="Stripe">Stripe</option>
                                        <option value="PayPal">PayPal</option>
                                        <option value="Other">Other</option>
                                    </select>
                                    <div v-if="form.errors.gateway" class="text-danger small mt-1">{{ form.errors.gateway }}</div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Account Name *</label>
                                    <input v-model="form.name" type="text" class="form-control" required placeholder="e.g., Main Bank Account">
                                    <div v-if="form.errors.name" class="text-danger small mt-1">{{ form.errors.name }}</div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Account Number</label>
                                    <input v-model="form.account_number" type="text" class="form-control" placeholder="Optional">
                                    <div v-if="form.errors.account_number" class="text-danger small mt-1">{{ form.errors.account_number }}</div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Bank Name</label>
                                    <input v-model="form.bank_name" type="text" class="form-control" placeholder="Optional">
                                    <div v-if="form.errors.bank_name" class="text-danger small mt-1">{{ form.errors.bank_name }}</div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Currency</label>
                                    <select v-model="form.currency_id" class="form-select">
                                        <option :value="null">Select Currency (Optional)</option>
                                        <option v-for="currency in currencies" :key="currency.id" :value="currency.id">
                                            {{ currency.code }} - {{ currency.name }}
                                        </option>
                                    </select>
                                    <div v-if="form.errors.currency_id" class="text-danger small mt-1">{{ form.errors.currency_id }}</div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Status *</label>
                                    <select v-model="form.status" class="form-select" required>
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                    <div v-if="form.errors.status" class="text-danger small mt-1">{{ form.errors.status }}</div>
                                </div>

                                <div class="col-12 mb-3">
                                    <label class="form-label">Notes</label>
                                    <textarea v-model="form.notes" class="form-control" rows="3" placeholder="Optional notes about this account"></textarea>
                                    <div v-if="form.errors.notes" class="text-danger small mt-1">{{ form.errors.notes }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" @click="closeModal">Cancel</button>
                            <button type="submit" class="btn btn-primary" :disabled="form.processing">
                                <span v-if="form.processing">Saving...</span>
                                <span v-else>{{ editingAccount ? 'Update' : 'Create' }} Account</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </WhmcsAdminLayout>
</template>
