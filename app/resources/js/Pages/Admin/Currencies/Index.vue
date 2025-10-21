<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import WhmcsAdminLayout from '@/Layouts/WhmcsAdminLayout.vue';

const props = defineProps({
    currencies: Array,
});

const deleteCurrency = (code) => {
    if (confirm('Are you sure you want to delete this currency?')) {
        router.delete(route('managit.currencies.destroy', code));
    }
};
</script>

<template>
    <Head title="Currency Management" />

    <WhmcsAdminLayout>
        <template #header>
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right">
                            <Link :href="route('managit.currencies.create')" class="btn btn-primary">
                                <i class="ti ti-plus me-1"></i> Add Currency
                            </Link>
                        </div>
                        <h4 class="page-title">Currency Management</h4>
                    </div>
                </div>
            </div>
        </template>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-centered mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Code</th>
                                        <th>Name</th>
                                        <th>Symbol</th>
                                        <th>Position</th>
                                        <th>Format</th>
                                        <th>Decimals</th>
                                        <th>Exchange Rate (USD)</th>
                                        <th>Status</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="currency in currencies" :key="currency.code">
                                        <td>
                                            <strong>{{ currency.code }}</strong>
                                            <span v-if="currency.is_default" class="badge bg-primary ms-2">Default</span>
                                        </td>
                                        <td>{{ currency.name }}</td>
                                        <td>{{ currency.symbol }}</td>
                                        <td>
                                            <span class="badge bg-info">
                                                {{ currency.symbol_position === 'before' ? 'Before' : 'After' }}
                                            </span>
                                        </td>
                                        <td>{{ currency.format }}</td>
                                        <td>{{ currency.decimals }}</td>
                                        <td>{{ currency.exchange_rate_to_usd }}</td>
                                        <td>
                                            <span :class="['badge', currency.enabled ? 'bg-success' : 'bg-secondary']">
                                                {{ currency.enabled ? 'Enabled' : 'Disabled' }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <div class="btn-group">
                                                <Link 
                                                    :href="route('managit.currencies.edit', currency.code)" 
                                                    class="btn btn-sm btn-warning"
                                                    title="Edit"
                                                >
                                                    <i class="ti ti-edit"></i>
                                                </Link>
                                                <button 
                                                    v-if="!currency.is_default"
                                                    @click="deleteCurrency(currency.code)" 
                                                    class="btn btn-sm btn-danger"
                                                    title="Delete"
                                                >
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-if="currencies.length === 0">
                                        <td colspan="9" class="text-center text-muted py-4">
                                            No currencies found. Add your first currency to get started.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </WhmcsAdminLayout>
</template>
