<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import WhmcsAdminLayout from '@/Layouts/WhmcsAdminLayout.vue';

const form = useForm({
    code: '',
    name: '',
    symbol: '',
    symbol_position: 'before',
    prefix: '',
    suffix: '',
    format: '1,234.56',
    decimals: 2,
    exchange_rate_to_usd: 1.000000,
    is_default: false,
    enabled: true,
});

const submit = () => {
    form.post(route('managit.currencies.store'));
};
</script>

<template>
    <Head title="Add Currency" />

    <WhmcsAdminLayout>
        <template #header>
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right">
                            <Link :href="route('managit.currencies.index')" class="btn btn-secondary">
                                <i class="ti ti-arrow-left me-1"></i> Back to Currencies
                            </Link>
                        </div>
                        <h4 class="page-title">Add Currency</h4>
                    </div>
                </div>
            </div>
        </template>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form @submit.prevent="submit">
                            <!-- Basic Information -->
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="code" class="form-label">Currency Code *</label>
                                    <input 
                                        v-model="form.code" 
                                        type="text" 
                                        class="form-control text-uppercase" 
                                        :class="{ 'is-invalid': form.errors.code }"
                                        id="code"
                                        maxlength="3"
                                        placeholder="USD"
                                        required
                                    >
                                    <div v-if="form.errors.code" class="invalid-feedback">{{ form.errors.code }}</div>
                                    <small class="text-muted">3-letter ISO code (e.g., USD, EUR, GBP)</small>
                                </div>
                                <div class="col-md-8">
                                    <label for="name" class="form-label">Currency Name *</label>
                                    <input 
                                        v-model="form.name" 
                                        type="text" 
                                        class="form-control" 
                                        :class="{ 'is-invalid': form.errors.name }"
                                        id="name"
                                        placeholder="US Dollar"
                                        required
                                    >
                                    <div v-if="form.errors.name" class="invalid-feedback">{{ form.errors.name }}</div>
                                </div>
                            </div>

                            <!-- Symbol and Position -->
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="symbol" class="form-label">Symbol *</label>
                                    <input 
                                        v-model="form.symbol" 
                                        type="text" 
                                        class="form-control" 
                                        :class="{ 'is-invalid': form.errors.symbol }"
                                        id="symbol"
                                        placeholder="$"
                                        required
                                    >
                                    <div v-if="form.errors.symbol" class="invalid-feedback">{{ form.errors.symbol }}</div>
                                </div>
                                <div class="col-md-4">
                                    <label for="symbol_position" class="form-label">Symbol Position *</label>
                                    <select 
                                        v-model="form.symbol_position" 
                                        class="form-select" 
                                        :class="{ 'is-invalid': form.errors.symbol_position }"
                                        id="symbol_position"
                                        required
                                    >
                                        <option value="before">Before Amount ($100)</option>
                                        <option value="after">After Amount (100$)</option>
                                    </select>
                                    <div v-if="form.errors.symbol_position" class="invalid-feedback">{{ form.errors.symbol_position }}</div>
                                </div>
                                <div class="col-md-4">
                                    <label for="format" class="form-label">Number Format *</label>
                                    <input 
                                        v-model="form.format" 
                                        type="text" 
                                        class="form-control" 
                                        :class="{ 'is-invalid': form.errors.format }"
                                        id="format"
                                        placeholder="1,234.56"
                                        required
                                    >
                                    <div v-if="form.errors.format" class="invalid-feedback">{{ form.errors.format }}</div>
                                    <small class="text-muted">e.g., 1,234.56 or 1.234,56</small>
                                </div>
                            </div>

                            <!-- Prefix and Suffix -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="prefix" class="form-label">Prefix (Optional)</label>
                                    <input 
                                        v-model="form.prefix" 
                                        type="text" 
                                        class="form-control" 
                                        :class="{ 'is-invalid': form.errors.prefix }"
                                        id="prefix"
                                        placeholder="e.g., USD"
                                    >
                                    <div v-if="form.errors.prefix" class="invalid-feedback">{{ form.errors.prefix }}</div>
                                    <small class="text-muted">Text to show before the amount</small>
                                </div>
                                <div class="col-md-6">
                                    <label for="suffix" class="form-label">Suffix (Optional)</label>
                                    <input 
                                        v-model="form.suffix" 
                                        type="text" 
                                        class="form-control" 
                                        :class="{ 'is-invalid': form.errors.suffix }"
                                        id="suffix"
                                        placeholder="e.g., USD"
                                    >
                                    <div v-if="form.errors.suffix" class="invalid-feedback">{{ form.errors.suffix }}</div>
                                    <small class="text-muted">Text to show after the amount</small>
                                </div>
                            </div>

                            <!-- Decimals and Exchange Rate -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="decimals" class="form-label">Decimal Places *</label>
                                    <input 
                                        v-model.number="form.decimals" 
                                        type="number" 
                                        class="form-control" 
                                        :class="{ 'is-invalid': form.errors.decimals }"
                                        id="decimals"
                                        min="0"
                                        max="4"
                                        required
                                    >
                                    <div v-if="form.errors.decimals" class="invalid-feedback">{{ form.errors.decimals }}</div>
                                </div>
                                <div class="col-md-6">
                                    <label for="exchange_rate_to_usd" class="form-label">Exchange Rate to USD *</label>
                                    <input 
                                        v-model.number="form.exchange_rate_to_usd" 
                                        type="number" 
                                        class="form-control" 
                                        :class="{ 'is-invalid': form.errors.exchange_rate_to_usd }"
                                        id="exchange_rate_to_usd"
                                        step="0.000001"
                                        min="0"
                                        required
                                    >
                                    <div v-if="form.errors.exchange_rate_to_usd" class="invalid-feedback">{{ form.errors.exchange_rate_to_usd }}</div>
                                    <small class="text-muted">1 USD = ? {{ form.code || 'Currency' }}</small>
                                </div>
                            </div>

                            <!-- Status Options -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-check form-switch">
                                        <input 
                                            v-model="form.is_default" 
                                            type="checkbox" 
                                            class="form-check-input" 
                                            id="is_default"
                                        >
                                        <label class="form-check-label" for="is_default">
                                            Set as Default Currency
                                        </label>
                                    </div>
                                    <small class="text-muted">This will be the primary currency for your system</small>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check form-switch">
                                        <input 
                                            v-model="form.enabled" 
                                            type="checkbox" 
                                            class="form-check-input" 
                                            id="enabled"
                                        >
                                        <label class="form-check-label" for="enabled">
                                            {{ form.enabled ? 'Enabled' : 'Disabled' }}
                                        </label>
                                    </div>
                                    <small class="text-muted">Enable this currency for use in the system</small>
                                </div>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="row mt-4">
                                <div class="col-12">
                                    <button 
                                        type="submit" 
                                        class="btn btn-primary"
                                        :disabled="form.processing"
                                    >
                                        <i class="ti ti-device-floppy me-1"></i>
                                        {{ form.processing ? 'Creating...' : 'Create Currency' }}
                                    </button>
                                    <Link :href="route('managit.currencies.index')" class="btn btn-secondary ms-2">
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
