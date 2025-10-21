<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import WhmcsAdminLayout from '@/Layouts/WhmcsAdminLayout.vue';
import { ref } from 'vue';

const props = defineProps({
    users: Array,
    currencies: Array,
});

const form = useForm({
    user_id: '',
    subject: '',
    notes: '',
    subtotal: 0,
    tax: 0,
    total: 0,
    currency: 'USD',
    valid_until: '',
});

const calculateTotal = () => {
    form.total = parseFloat(form.subtotal) + parseFloat(form.tax);
};

const submit = () => {
    form.post(route('managit.quotes.store'));
};
</script>

<template>
    <Head title="Create Quote" />

    <WhmcsAdminLayout>
        <template #header>
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right">
                            <Link :href="route('managit.quotes.index')" class="btn btn-secondary">
                                <i class="ti ti-arrow-left me-1"></i> Back to Quotes
                            </Link>
                        </div>
                        <h4 class="page-title">Create New Quote</h4>
                    </div>
                </div>
            </div>
        </template>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form @submit.prevent="submit">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="user_id" class="form-label">Client <span class="text-danger">*</span></label>
                                    <select
                                        id="user_id"
                                        v-model="form.user_id"
                                        class="form-select"
                                        :class="{ 'is-invalid': form.errors.user_id }"
                                        required
                                    >
                                        <option value="">Select Client</option>
                                        <option v-for="user in users" :key="user.id" :value="user.id">
                                            {{ user.name }} ({{ user.email }})
                                        </option>
                                    </select>
                                    <div v-if="form.errors.user_id" class="invalid-feedback">{{ form.errors.user_id }}</div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="currency" class="form-label">Currency <span class="text-danger">*</span></label>
                                    <select
                                        id="currency"
                                        v-model="form.currency"
                                        class="form-select"
                                        :class="{ 'is-invalid': form.errors.currency }"
                                        required
                                    >
                                        <option v-for="currency in currencies" :key="currency.code" :value="currency.code">
                                            {{ currency.code }} - {{ currency.name }}
                                        </option>
                                    </select>
                                    <div v-if="form.errors.currency" class="invalid-feedback">{{ form.errors.currency }}</div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="subject" class="form-label">Subject <span class="text-danger">*</span></label>
                                <input
                                    id="subject"
                                    type="text"
                                    v-model="form.subject"
                                    class="form-control"
                                    :class="{ 'is-invalid': form.errors.subject }"
                                    required
                                    placeholder="Quote subject"
                                />
                                <div v-if="form.errors.subject" class="invalid-feedback">{{ form.errors.subject }}</div>
                            </div>

                            <div class="mb-3">
                                <label for="notes" class="form-label">Notes</label>
                                <textarea
                                    id="notes"
                                    v-model="form.notes"
                                    class="form-control"
                                    :class="{ 'is-invalid': form.errors.notes }"
                                    rows="4"
                                    placeholder="Additional notes for this quote"
                                ></textarea>
                                <div v-if="form.errors.notes" class="invalid-feedback">{{ form.errors.notes }}</div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="subtotal" class="form-label">Subtotal <span class="text-danger">*</span></label>
                                    <input
                                        id="subtotal"
                                        type="number"
                                        step="0.01"
                                        v-model="form.subtotal"
                                        @input="calculateTotal"
                                        class="form-control"
                                        :class="{ 'is-invalid': form.errors.subtotal }"
                                        required
                                        placeholder="0.00"
                                    />
                                    <div v-if="form.errors.subtotal" class="invalid-feedback">{{ form.errors.subtotal }}</div>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="tax" class="form-label">Tax <span class="text-danger">*</span></label>
                                    <input
                                        id="tax"
                                        type="number"
                                        step="0.01"
                                        v-model="form.tax"
                                        @input="calculateTotal"
                                        class="form-control"
                                        :class="{ 'is-invalid': form.errors.tax }"
                                        required
                                        placeholder="0.00"
                                    />
                                    <div v-if="form.errors.tax" class="invalid-feedback">{{ form.errors.tax }}</div>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="total" class="form-label">Total</label>
                                    <input
                                        id="total"
                                        type="number"
                                        step="0.01"
                                        v-model="form.total"
                                        class="form-control"
                                        readonly
                                        placeholder="0.00"
                                    />
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="valid_until" class="form-label">Valid Until</label>
                                <input
                                    id="valid_until"
                                    type="date"
                                    v-model="form.valid_until"
                                    class="form-control"
                                    :class="{ 'is-invalid': form.errors.valid_until }"
                                />
                                <div v-if="form.errors.valid_until" class="invalid-feedback">{{ form.errors.valid_until }}</div>
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <Link :href="route('managit.quotes.index')" class="btn btn-secondary">
                                    Cancel
                                </Link>
                                <button
                                    type="submit"
                                    class="btn btn-primary"
                                    :disabled="form.processing"
                                >
                                    <span v-if="!form.processing">Create Quote</span>
                                    <span v-else>Creating...</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </WhmcsAdminLayout>
</template>
